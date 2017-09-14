<?php
$form=\yii\bootstrap\ActiveForm::begin();//开启
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->textInput();
//验证码
echo $form->field($model,'code')->widget(yii\captcha\Captcha::className(),['captchaAction'=>'admin/captcha','template'=>'<div class="row"><div class="col-lg-2">{image}</div><div class="col-lg-2">{input}</div></div>']);
echo $form->field($model,'checkbook')->checkbox([1=>'记住我',0=>'放弃我']);
echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();//结束