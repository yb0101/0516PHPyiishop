<?php
namespace backend\models;
use Codeception\Module\Yii1;
use common\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $code;
    public $checkbook;
    public $newpassword;
    public $nnewpassword;
    public $ppasswor;
    public $password_hash;
    public $ppasswor_hash;
    public function rules()
    {
        return[
            [['username','password'],'required'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
            ['checkbook','string'],
            [['ppasswor','newpassword','nnewpassword'],'string'],

        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'checkbook'=>'是否记住密码',
             'ppasswor'=>'老密码',
             'newpassword'=>'新密码',
             'nnewpassword'=>'再次确定新密码'
        ];
    }
    public function login(){
        $admin = Admin::findOne(['username'=>$this->username]);
        if($admin){
            //验证密码
            if(\Yii::$app->security->validatePassword($this->password,$admin->password_hash)){
                //账户密码正确,并且保存信息到session中
                $user=\Yii::$app->user;
                if($this->checkbook){
                   // var_dump($this->checkbook);exit;
//                    //自动登陆
                    return \Yii::$app->user->login($admin,3*3600);


                }else{
                    //普通登陆
                    return \Yii::$app->user->login($admin);
                }

            }else{
                //密码不正确
                //echo '密码错误';exit;
                $this->addError('password','密码不正确');
            }
        }else{
            //没有找到该账户
            //echo '账户不存在';exit;
            $this->addError('username','账户不存在');
        }
        return false;
    }
}