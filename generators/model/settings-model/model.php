<?php
/**
 * This is the template for generating the model class of a specified table.
 */
/* @var $this yii\web\View */
/* @var $generator cza\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
* This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
*
<?php foreach ($tableSchema->columns as $column): ?>
    * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
    *
    <?php foreach ($relations as $name => $relation): ?>
        * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
    <?php endforeach; ?>
<?php endif; ?>
*/
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
/**
* @inheritdoc
*/
public static function tableName()
{
return '<?= $generator->generateTableName($tableName) ?>';
}
<?php if ($generator->db !== 'db'): ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
    return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

/**
* @inheritdoc
*/
public function rules()
{
return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
}

    public function getConfigModelClass() {
        return static::class . 'Config';
    }

    public function getConfigForm() {
        return new \backend\models\c2\form\<?= $className ?>Config(['model' => $this->config]);
    }

    public function getConfig() {
        $configModelClass = $this->getConfigModelClass();
        return $this->hasOne($configModelClass, ['owner_id' => 'id']);
    }
    
    public function beforeDelete() {
        if ($this->config) {
            $this->config->delete();
        }
        return parent::beforeDelete();
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            // 创建配置
            $configModelClass = $this->getConfigModelClass();
            $configModel = new $configModelClass();
            $configModel->loadDefaultValues();
            $configModel->setAttributes([
                'type' => 1,
                'code' => $this->code . '_CONFIG',
                'label' => $this->label . 'Config',
            ]);
            $configModel->link('owner', $this);
        }
    }
    
/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
<?php foreach ($labels as $name => $label): ?>
    <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
];
}
<?php foreach ($relations as $name => $relation): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>()
    {
    <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
    <?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
    * @inheritdoc
    * @return <?= $queryClassFullName ?> the active query used by this AR class.
    */
    public static function find()
    {
    return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

/**
* setup default values
**/
public function loadDefaultValues($skipIfSet = true) {
parent::loadDefaultValues($skipIfSet);
}

}
