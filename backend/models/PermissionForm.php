<?php
namespace backend\models;
use yii\base\Model;
class PermissionForm extends Model{
    public $name;
    public $description;
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['name','vvalidateName'],//对名字进行自定义
        ];
    }
 public function vvalidateName(){//主管错误的
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','此权限名已经存在');
        }
 }
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'描述'
        ];
    }
}