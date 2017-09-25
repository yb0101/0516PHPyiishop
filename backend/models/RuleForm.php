<?php
namespace backend\models;
use yii\base\Model;

class RuleForm extends Model{
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public $name;
    public $description;
    public $permissions;
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['name','validateEditName','on'=>self::SCENARIO_EDIT],
        ];
    }
    public function validateName(){
        $author=\Yii::$app->authManager;
        if($author->getRole($this->name)){
            $this->addError('name','该角色已经存在');
        }
    }
    public function validateEditName(){
        //如果以前的名字和得到的不一样说明就修改名字了
        $request=\Yii::$app->request;
        $auth=\Yii::$app->authManager;
       if($request->get('name')!=$this->name){
           if($auth->getRole($this->name)){
               $this->addError('name','该角色已经存在');
           }
       }
    }
    public static function getPermissionItems(){
        $permissions=\Yii::$app->authManager->getPermissions();//获取全部的信息
        $itms=[];
        foreach ($permissions as $permission){
            $itms[$permission->name] = $permission->description;
        }

        return  $itms;//用另一个方法做
    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'描述',
            'permissions'=>'权限'
        ];
    }

}