<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'configFormModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'viewPath');
echo $form->field($generator, 'widgetsPath');
//echo $form->field($generator, 'baseControllerClass');
//echo $form->field($generator, 'indexWidgetType')->dropDownList([
//    'grid' => 'GridView',
//    'list' => 'ListView',
//]);
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'enablePlaceholder')->checkbox();
echo $form->field($generator, 'withTranslationTabs')->checkbox();
echo $form->field($generator, 'withProfileTab')->checkbox();
echo $form->field($generator, 'withConfigTab')->checkbox();
echo $form->field($generator, 'formColumns');
echo $form->field($generator, 'ignoreFormFields');
echo $form->field($generator, 'messageCategory');
