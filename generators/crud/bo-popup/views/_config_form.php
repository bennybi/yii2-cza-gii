<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$modelClass = $generator->modelClass;
$modelConfigClass = $generator->modelClass . 'Config';
$model = new $modelClass();
$profileModel = new $modelConfigClass();
$safeAttributes = [];
$entityModelSafeAttributes = $profileModel->safeAttributes();
$transSafeAttributes = $profileModel->safeAttributes();
$safeAttributes = array_intersect($transSafeAttributes, $entityModelSafeAttributes);

$tableSchema = $modelConfigClass::getTableSchema();
$columnNames = $tableSchema->getColumnNames();

if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use cza\base\widgets\ui\adminlte2\InfoBox;
use cza\base\models\statics\EntityModelStatus;
use yii\widgets\Pjax;

$regularLangName = \Yii::$app->czaHelper->getRegularLangName();
$messageName = $model->getMessageName();
?>

<?php echo "<?php"; ?> Pjax::begin(['id' => $model->getPjaxName(),'formSelector' => $model->getBaseFormName(true), 'enablePushState' => false])<?php echo "?>\n"; ?>

<?php echo "<?php\n"; ?>
$form = ActiveForm::begin([
            'action' => ['config-save', 'id' => $model->id],
            'options' => [
                'id' => $model->getBaseFormName(),
                'data-pjax' => true,
        ]]);
?>

<div class="<?php echo "<?="; ?>$model->getPrefixName('form') <?php echo "?>\n"; ?>">
<?php echo "<?php"; ?> if (Yii::$app->session->hasFlash($messageName)): <?php echo "?>\n"; ?>
    <?php echo "<?php"; ?>
    if (!$model->hasErrors()) {
        echo InfoBox::widget([
            'withWrapper' => false,
            'messages' => Yii::$app->session->getFlash($messageName),
        ]);
    } else {
        echo InfoBox::widget([
            'defaultMessageType' => InfoBox::TYPE_WARNING,
            'messages' => Yii::$app->session->getFlash($messageName),
        ]);
    }
    <?php echo "?>\n"; ?>
<?php echo "<?php"; ?> endif; <?php echo "?>\n"; ?>
    
<div class="well">
    <?php echo "<?php\n"; ?>
           echo Form::widget([
                'model' => $model,
                'form' => $form,
                'columns' => <?= $generator->formColumns; ?>,
                'attributes' => [
                'field1' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => $model->getAttributeLabel('field1')]],
                'field2' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => $model->getAttributeLabel('field2')]],
                'field3' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => $model->getAttributeLabel('field3')]],
                'field4' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => $model->getAttributeLabel('field4')]],
            ]
            ]);
            echo Html::hiddenInput('entity_id', $entityModel->id);
            
            echo Html::beginTag('div', ['class' => 'box-footer']);
            echo Html::submitButton('<i class="fa fa-save"></i> ' . Yii::t('app.c2', 'Save'), ['type' => 'button', 'class' => 'btn btn-primary pull-right']);
            echo Html::a('<i class="fa fa-close"></i> ' . Yii::t('app.c2', 'Close'), ['index'], ['data-dismiss' => 'modal', 'class' => 'btn btn-default pull-right', 'title' => Yii::t('app.c2', 'Close'),]);
            echo Html::endTag('div');
        <?php echo "?>\n"; ?>
</div>
</div>
<?php echo "<?php ActiveForm::end(); ?>\n"; ?>
<?php echo "<?php Pjax::end(); ?>\n"; ?>