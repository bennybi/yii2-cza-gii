<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(StringHelper::dirname(ltrim($generator->controllerClass, '\\'))).'\widgets' ?>;

use Yii;
use cza\base\widgets\ui\common\part\EntityDetail as DetailWidget;

/**
 * Entity Detail Widget
 * 
 * @author Ben Bi <ben@cciza.com>
 * @link http://www.cciza.com/
 * @copyright 2014-2016 CCIZA Software LLC
 * @license
 */
 
class EntityDetail extends DetailWidget {
    <?php if($generator->withTranslationTabs): ?>
        public $withTranslationTabs = true;
    <?php else:?>
        public $withTranslationTabs = false;
    <?php endif?>
        
    <?php if($generator->withProfileTab): ?>
        public $withProfileTab = true;
    <?php else:?>
        public $withProfileTab = false;
    <?php endif?>
}