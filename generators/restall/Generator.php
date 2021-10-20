<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace cza\gii\generators\restall;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Ben Bi <bennybi@qq.com>
 * @since 2.0
 */
//class Generator extends \yii\gii\Generator
class Generator extends \yii\gii\generators\model\Generator {

    const RELATIONS_NONE = 'none';
    const RELATIONS_ALL = 'all';
    const RELATIONS_ALL_INVERSE = 'all-inverse';

    public $db = 'db';
    public $ns = 'common\models\c2\entity';
    public $tableName;
    public $modelClass;
    public $baseClass = '\cza\base\models\ActiveRecord';
    public $generateRelations = self::RELATIONS_ALL;
    public $generateLabelsFromComments = false;
    public $useTablePrefix = true;
    public $useSchemaName = true;
    public $generateQuery = true;
    public $enableI18N = true;
    public $queryNs = 'common\models\c2\query';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $searchNs = 'common\models\c2\search';
    public $searchModelClass;
    public $searchBaseClass = '';
    public $restNs = 'common\models\c2\rest';
    public $restModelClass;
    public $restBaseClass = '';
    public $restModuleID;
    public $restModuleClass;
    public $messageCategory = 'app.c2';

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return "Rest All Generator \n (Models/Module/Controller)";
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription() {
        return 'This generator generates an BaseModel/SearchModel/RestModel/Module/Controller class for the specified entity.';
    }

    public function generate() {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $searchModelClassName = $this->generateSearchClassName($modelClassName);
            $restModelClassName = $this->generateRestClassName($modelClassName);
            $tableRelations = isset($relations[$tableName]) ? $relations[$tableName] : [];
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'searchModelClassName' => $searchModelClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'oasSchemas' => $this->generateOasSchemas($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => $tableRelations,
                'relationsClassHints' => $this->generateRelationsClassHints($tableRelations, $this->generateQuery),
            ];
            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                    $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                        Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                        $this->render('query.php', $params)
                );
            }

            // search :
            if ($searchModelClassName) {
                $params['className'] = $searchModelClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                        Yii::getAlias('@' . str_replace('\\', '/', $this->searchNs)) . '/' . $searchModelClassName . '.php',
                        $this->render('search.php', $params)
                );
            }

            // rest :
            if ($restModelClassName) {
                $params['className'] = $restModelClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                        Yii::getAlias('@' . str_replace('\\', '/', $this->restNs)) . '/' . $restModelClassName . '.php',
                        $this->render('rest.php', $params)
                );
            }

            $modulePath = $this->getRestModulePath();
            $files[] = new CodeFile(
                    $modulePath . '/' . 'Module.php',
                    $this->render("module.php")
            );

            $files[] = new CodeFile(
                    $modulePath . '/controllers/DefaultController.php',
                    $this->render("controller.php")
            );
        }

        return $files;
    }

    public function getTableSchema() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules($tableSchema) {
//        if (($table = $this->getTableSchema()) === false) {
//            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
//        }
        $table = $tableSchema;
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes($tableSchema) {
        return $this->getColumnNames($tableSchema);
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels($tableSchema) {
        $attributeLabels = $this->generateLabels($tableSchema);
        $labels = [];
        foreach ($this->getColumnNames($tableSchema) as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                        $label = substr($label, 0, -3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }
        return $labels;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions($tableSchema) {
        $columns = [];
        $table = $tableSchema;
        foreach ($table->columns as $column) {
            $columns[$column->name] = $column->type;
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                    . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                    . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * @return array model column names
     */
    public function getColumnNames($tableSchema) {
        return $tableSchema->getColumnNames();
    }

    public function generateRestClassName($modelClassName) {
        $restClassName = $this->restModelClass;
        if (empty($restClassName) || strpos($this->tableName, '*') !== false) {
            $restClassName = $modelClassName;
        }
        return $restClassName;
    }

    public function generateRestBaseClassName($modelClassName) {
        $name = !empty($this->restBaseClass) ? $this->restBaseClass : "{$this->searchNs}\\{$this->generateSearchClassName($modelClassName)}";
        return $name;
    }

    public function generateSearchClassName($modelClassName) {
        $searchClassName = $this->searchModelClass;
        if (empty($searchClassName) || strpos($this->tableName, '*') !== false) {
            $searchClassName = $modelClassName . 'Search';
        }
        return $searchClassName;
    }

    public function generateSearchBaseClassName($modelClassName) {
        $name = !empty($this->searchBaseClass) ? $this->searchBaseClass : "{$this->ns}\\{$modelClassName}";
        return $name;
    }

    public function rules() {
        return array_merge(parent::rules(), [
            [['searchNs', 'searchBaseClass'], 'string',],
            [['restNs', 'restBaseClass'], 'string',],
            [['restModuleID', 'restModuleClass'], 'filter', 'filter' => 'trim'],
            [['restModuleID', 'restModuleClass'], 'required'],
            [['restModuleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['restModuleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['restModuleClass'], 'validateModuleClass'],
        ]);
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'searchNs' => 'Search Namespace',
            'searchClass' => 'Search Class',
            'searchBaseClass' => 'Search Base Class',
            'restNs' => 'Rest Namespace',
            'restClass' => 'Rest Class',
            'restBaseClass' => 'Rest Base Class',
            'restModuleID' => 'Rest Module ID',
            'restModuleClass' => 'Rest Module Class',
        ]);
    }

    /**
     * Generates OAS properties for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated OAS properties
     */
    public function generateOasSchemas($table) {
        $schemas = [];
        $properties = [];
        foreach ($table->columns as $key => $column) {
            $properties[$key]['property'] = $column->name;
            $properties[$key]['description'] = $column->comment;
            $properties[$key]['default'] = $column->defaultValue;

            if (!$column->allowNull && $column->defaultValue === null) {
                if (!$column->isPrimaryKey) {
                    $schemas['required'][] = $column->name;
                }
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $properties[$key]['type'] = 'integer';
                    $properties[$key]['format'] = 'int64';
                    break;
                case Schema::TYPE_BOOLEAN:
                    $properties[$key]['type'] = 'boolean';
                    break;
                case Schema::TYPE_FLOAT:
                    $properties[$key]['type'] = 'number';
                    $properties[$key]['format'] = 'float';
                    break;
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $properties[$key]['type'] = 'number';
                    $properties[$key]['format'] = 'double';
                    break;
                case Schema::TYPE_DATE:
                    $properties[$key]['type'] = 'string';
                    $properties[$key]['format'] = 'date';
                    break;
                case Schema::TYPE_TIME:
                    $properties[$key]['type'] = 'string';
                    break;
                case Schema::TYPE_DATETIME:
                    $properties[$key]['type'] = 'string';
                    $properties[$key]['format'] = 'date-time';
                    break;
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                default: // strings
                    $properties[$key]['type'] = 'string';
                    if ($column->size > 0) {
                        $properties[$key]['maxLength'] = $column->size;
                    }
                    if ($column->enumValues !== null) {
                        $properties[$key]['enum'] = $column->enumValues;
                    }
            }
        }
        $schemas['properties'] = $properties;
        return $schemas;
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getRestModulePath() {
        return Yii::getAlias('@' . str_replace('\\', '/', substr($this->restModuleClass, 0, strrpos($this->restModuleClass, '\\'))));
    }

    public function hints() {
        return ArrayHelper::merge(parent::hints(), [
                    'restModuleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
                    'restModuleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ]);
    }

    public function validateModuleClass() {
        if (strpos($this->restModuleClass, '\\') === false || Yii::getAlias('@' . str_replace('\\', '/', $this->restModuleClass), false) === false) {
            $this->addError('restModuleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->restModuleClass) || substr_compare($this->restModuleClass, '\\', -1, 1) === 0) {
            $this->addError('restModuleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace() {
        return substr($this->restModuleClass, 0, strrpos($this->restModuleClass, '\\')) . '\controllers';
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage() {
        $seoStr = strtolower($this->restModuleClass);
        $seoStr = substr($seoStr, strpos($seoStr, 'modules\\'));
        $seoStr = strtr($seoStr, [
            'modules\\' => '',
            'module' => '',
            '\\' => '/',
        ]);
        $actionPattern = $seoStr . '<action:[\w\-]+>';
        $actionLink = $seoStr . 'default/<action>';

        $seoOutput = <<<EOD
<b>Add Url Rules(seo.php):</b>
EOD;
        $seoCode = <<<EOD
'{$actionPattern}' => '{$actionLink}'
EOD;
        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your module configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->restModuleID}' => [
            'class' => '{$this->restModuleClass}',
        ],
    ],
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>' . $seoOutput . '<pre>' . highlight_string($seoCode, true) . '</pre>';
    }

}
