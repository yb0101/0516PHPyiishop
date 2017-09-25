<?php
namespace frontend\models;
use Codeception\Module\Yii1;
use common\models;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $checkbook;
    public function rules()
    {
        return[
            [['username','password'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
        ];
    }
    public function login(){
        $admin = Member::findOne(['username'=>$this->username]);
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