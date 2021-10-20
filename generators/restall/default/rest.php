<?php
/**
 * This is the template for generating the model class of a specified table.
 */

use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

namespace <?= $generator->restNs ?>;

use Yii;
use yii\helpers\ArrayHelper;
use <?= '\\' . ltrim($generator->generateRestBaseClassName($modelClass), '\\') ?> as BaseModel;

/**
* @OA\Schema(
*      schema="<?= $className ?>",
<?php if (isset($oasSchemas['required'])): ?>
*      required={<?= rtrim(ltrim(json_encode($oasSchemas['required']),'['),']') ?>},
<?php endif; ?>
<?php foreach ($oasSchemas['properties'] as $property): ?>
<?php if (in_array($property['property'],['created_at','created_by','updated_at','updated_by','deleted_at'])):
continue;
endif; ?>
*     @OA\Property(
*        property="<?= $property['property'] ?>",
*        description="<?= ($property['description'] ? strtr($property['description'], ["\n" => ' ']) : '')?>",
*        type="<?= $property['type'] ?>",
<?php if (isset($property['format'])): ?>
*        format="<?= $property['format']?>",
<?php endif; ?>
<?php if (isset($property['maxLength'])): ?>
*        maxLength=<?= $property['maxLength']?>,
<?php endif; ?>
<?php if ($property['default'] !==null): ?>
*        default=<?= '"'. $property['default'].'"'?>,
<?php endif; ?>
<?php if (isset($property['enum'])): ?>
*        enum={<?= rtrim(ltrim(json_encode($property['enum']),'['),']') ?>}
<?php endif; ?>
*    ),
<?php endforeach; ?>
* )
*/

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends BaseModel
{
    public function rules() {
        return ArrayHelper::merge(parent::rules(), []);
    }

    public function fields()
    {
        $fields = parent::fields();
        $customFields = [];

        return ArrayHelper::merge($fields, $customFields);
    }

    public function extraFields()
    {
        return [];
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

}
