<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->dropDownList(\backend\models\Menu::getData());
echo $form->field($model,'url')->dropDownList(\backend\models\Menu::getPermissionItems());
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();