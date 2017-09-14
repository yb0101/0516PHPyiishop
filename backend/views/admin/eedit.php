<?php
//var_dump($model);exit;
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'newpassword')->passwordInput();
echo $form->field($model,'nnewpassword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();