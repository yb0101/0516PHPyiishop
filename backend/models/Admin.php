<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    //定义应用场景
    const SCENARIO_ADD = 'add';
    //定义一个明文密码
    public $password;
    public $newpassword;
    public $rules;

    /**
     * @inheritdoc
     */
    //获取用户的菜单
    public function getMenus(){
        $menuItems = [];
        //获取所有一级菜单
        $menus = Menu::find()->where(['parent_id'=>0])->all();
        foreach($menus as $menu){
            //获取该一级菜单的所有子菜单
            $children = Menu::find()->where(['parent_id'=>$menu->id])->all();
            $items = [];
            foreach ($children as $child) {
                //判断 当前用户是否有该路由的权限
                if (Yii::$app->user->can($child->url)) {
                    $items[] = ['label' => $child->name, 'url' => [$child->url]];
                }
            }
            $menuItems[] = ['label' => $menu->name, 'items'=>$items];
        }
        return $menuItems;
    }
    public static function tableName()
    {
        return 'admin';
    }
    public static function getRuleItems(){
        $rules=\Yii::$app->authManager->getRoles();//获取全部的信息
        $itms=[];
        foreach ($rules as $rule){
            $itms[$rule->name] = $rule->description;
        }

        return  $itms;//用另一个方法做
    }

    /**
     * @inheritdoc
     */
    //保存之前要做的事
    public function beforeSave($insert)
    {
        //$insert 是否需要添加 放回一个布尔值
        if($insert){
            //添加
            $this->created_at=time();
            $this->last_login_ip= Yii::$app->request->userIP;
            $this->last_login_time=time();
            $this->auth_key=Yii::$app->security->generateRandomString();
            $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
        }else{
            //编辑
            $this->updated_at=time();
            if($this->password){//判断一下，如果有修改密码就吧新密码加密保存，没有就还是原来的密码
                $this->auth_key=Yii::$app->security->generateRandomString();
                $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
            }
        }
        return parent::beforeSave($insert); // 必须要返回父类方法,该方法必须要返回true,save()方法才会执行
    }

    public function rules()
    {
        return [
            [['username', 'status', 'email',], 'required'],
            ['password','required','on'=>[self::SCENARIO_ADD]],
            [['status', 'created_at', 'updated_at', 'last_login_time'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'last_login_ip'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password','newpassword'],'string'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['rules','safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登陆ip',
            'password' =>'密码'

        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $authKey==$this->auth_key;
    }
}
