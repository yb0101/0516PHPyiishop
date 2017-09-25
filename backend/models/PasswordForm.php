<?php
namespace backend\models;
use yii\base\Model;

class PasswordForm extends Model{
public $oldPassword;
public $newPassword;
public $nnewpassword;
public function rules()
{
    return [
        [['oldPassword','newPassword','nnewpassword'],'required'],
        ['nnewpassword','compare','compareAttribute'=>'newPassword'],//两次输入的密码必须一样
    ];
}

}