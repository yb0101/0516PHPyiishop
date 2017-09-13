<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($data,'id','name'));
echo $form->field($model,'sort')->textInput();
echo $form->field($content,'content')->textarea();
echo $form->field($model,'status',['inline'=>true])->radioList([0=>'隐藏',1=>'正常']);
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();