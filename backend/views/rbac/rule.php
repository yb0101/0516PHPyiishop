<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description')->textarea();
echo $form->field($model,'permissions')->checkboxList(\backend\models\RuleForm::getPermissionItems());
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();