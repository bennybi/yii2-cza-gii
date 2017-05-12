<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\modules\Test\modules\UserCase\modules\Crud\modules\Cca2Popup\widgets\EntityDetail;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

if($model->isNewRecord){
$this->title = Yii::t('app.c2', '{actionTips} {modelClass}: ', ['actionTips' => Yii::t('app.c2', 'Create'), 'modelClass' => <?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>,]);
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
}
else{
$this->title = Yii::t('app.c2', '{actionTips} {modelClass}: ', ['actionTips' => Yii::t('app.c2', 'Update'), 'modelClass' => <?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>,]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $generator->generateUrlParams() ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Update') ?>;
}
?>

<?= "<?php " ?>Pjax::begin(['id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-pjax', 'enablePushState' => false]) ?>

<div class="modal-header">
    <button type="button" class="fa fa-close close" data-dismiss="modal" aria-hidden="true"></button>
    <button type="button" class="fa fa-window-maximize close"></button>
    <i class="fa fa-th"></i> <?= "<?= " ?> $this->title ?>
</div>

<div class="modal-body">
    <?= "<?php\n" ?>
    echo EntityDetail::widget([
    'model' => $model,
    ]);
    ?>
</div>

<div class="modal-footer">
</div>

<?= "<?php " ?>
$js = "";
$js.= "jQuery('{$model->getDetailPjaxName(true)}').off('pjax:send').on('pjax:send', function(){jQuery.fn.czaTools('showLoading', {selector:'.modal-content', 'msg':''});});\n";
$js.= "jQuery('{$model->getDetailPjaxName(true)}').off('pjax:complete').on('pjax:complete', function(){jQuery.fn.czaTools('hideLoading', {selector:'.modal-content'});});\n";
$this->registerJs($js);
?>
<?= "<?php " ?> Pjax::end() ?>

