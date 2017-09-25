<?php

namespace backend\controllers;

use backend\models\PasswordForm;
use backend\models\PermissionForm;
use backend\models\RuleForm;

class RbacController extends \yii\web\Controller
{
    //添加权限
    public function actionAdd(){
        $model=new PermissionForm();
        $request=\Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $auth=\Yii::$app->authManager;
                    //添加权限
                    //创建权限
                    $permission=$auth->createPermission($model->name);
                    $permission->description=$model->description;
                    //保存到数据库
                    $auth->add($permission);
                    return $this->redirect(['permission-index']);
                }
       }
        return $this->render('add',['model'=>$model]);
    }
    //编辑功能
    public function actionEdit($name){
        $auth=\Yii::$app->authManager;
        $permission=$auth->getPermission($name);
        $request=\Yii::$app->request;
        $model=new PermissionForm();
        //回显数据
        $model->name=$permission->name;
        $model->description=$permission->description;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $permission->name=$model->name;
                $permission->description=$model->description;
                //保存数据
                $auth->update($name,$permission);//完全不懂啊
                return $this->redirect(['permission-index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //权限列表功能
    public function actionPermissionIndex()
    {
        $auth=\Yii::$app->authManager;
        $permissions=$auth->getPermissions();

        return $this->render('permission-index',['permissions'=>$permissions]);
    }
    //删除功能
    public function actionDelete($name){
        $auth=\Yii::$app->authManager;
        $permission=$auth->getPermission($name);
        $auth->remove($permission);
        return $this->redirect(['permission-index']);
    }
//添加角色
public function actionAddRule(){
        $model=new RuleForm();
    $model->scenario=RuleForm::SCENARIO_ADD;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $auth=\Yii::$app->authManager;
                //创建一个角色
                $role=$auth->createRole($model->name);
                $role->description=$model->description;
                //保存角色到数据库
                $auth->add($role);
                //分配权限
                //var_dump($model->permissions);exit;
                if($model->permissions){//如果有传过来权限就添加，否则就不处理
                    foreach ($model->permissions as $permissionName){
                        $permission=$auth->getPermission($permissionName);//根据权限名字获取对应的权限
                        $auth->addChild($role,$permission);//角色，权限
                    }
                }
                return $this->redirect(['rule-index']);
            }
        }
        return $this->render('rule',['model'=>$model]);
}
    //权限列表功能
    public function actionRuleIndex()
    {
        $auth=\Yii::$app->authManager;
        $rules=$auth->getRoles();

        return $this->render('rule-index',['rules'=>$rules]);
    }
    //编辑功能
    public function actionEditRule($name){
    $auth=\Yii::$app->authManager;
    $rule=$auth->getRole($name);
    $model=new RuleForm();
    $model->scenario=RuleForm::SCENARIO_EDIT;
        //回显数据
        $model->name=$rule->name;
        $model->description=$rule->description;
       // 显示关联权限
        $permissions=array_keys($auth->getPermissionsByRole($name));
        //var_dump($permissions);exit;
        $model->permissions=$permissions;
    $request=\Yii::$app->request;
    if($request->isPost){
        $model->load($request->post());
        if($model->validate()){
            $rule->name=$model->name;
            $rule->description=$model->description;
            //保存到数据库
            $auth->update($name,$rule);
            //在处理权限问题
            //先清除所有的权限
            $auth->removeChildren($rule);
            //然后重新赋值权限
            if($model->permissions){
                foreach ($model->permissions as $permissionName){
                    $permission=$auth->getPermission($permissionName);
                    $auth->addChild($rule,$permission);

                }
            }
            return $this->redirect(['rule-index']);
        }
    }
        return $this->render('rule',['model'=>$model]);
    }
    //删除功能
    public function actionDeleteRule($name){
        $auth=\Yii::$app->authManager;
        $rule=$auth->getRole($name);
        $auth->remove($rule);
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['rule-index']);
    }

}
