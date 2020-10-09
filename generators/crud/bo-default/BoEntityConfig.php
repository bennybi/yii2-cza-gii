<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace backend\models\c2\form;

use Yii;
use cza\base\models\abstracts\ConfigForm as BaseForm;

/**
* Entity Config Form Model
* 
* @author Ben Bi <bennybi@qq.com>
    * @link http://www.cciza.com/
    * @copyright 2014-2016 CCIZA Software LLC
    * @license
    */

    class <?= ucfirst(StringHelper::basename($generator->modelClass)) ?>Config extends BaseForm {

    public $field1;
    public $field2;
    public $field3;
    public $field4;

    public function rules() {
        return [
            [['field1', 'field2',], 'integer'],
            [['field3', 'field4',], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'field1' => Yii::t('app.c2', 'field1'),
            'field2' => Yii::t('app.c2', 'field2'),
        ];
    }

}
