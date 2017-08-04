<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
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

$regularLangName = \Yii::$app->czaHelper->getRegularLangName();
$messageName = $model->getMessageName();
?>

<?php echo "<?php\n"; ?>
$form = ActiveForm::begin([
'action' => ['edit', 'id' => $model->id],
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
        <?php
        foreach ($generator->getColumnNames() as $attribute) {
            if (in_array($attribute, $safeAttributes) && !in_array($attribute, $generator->getIgnoreFormFields())) {
                echo " " . $generator->generateActiveField($attribute) . "\n";
            }
        }
        ?>
        ]
        ]);
        echo Html::beginTag('div', ['class' => 'box-footer']);
        echo Html::submitButton('<i class="fa fa-save"></i> ' . Yii::t('app.c2', 'Save'), ['type' => 'button', 'class' => 'btn btn-primary pull-right']);
        echo Html::a('<i class="fa fa-arrow-left"></i> ' . Yii::t('app.c2', 'Go Back'), ['index'], [ 'data-pjax' => '0', 'class' => 'btn btn-default pull-right', 'title' => Yii::t('app.c2', 'Go Back'),]);
        echo Html::endTag('div');
        <?php echo "?>\n"; ?>
    </div>
</div>
<?php echo "<?php ActiveForm::end(); ?>\n"; ?>
