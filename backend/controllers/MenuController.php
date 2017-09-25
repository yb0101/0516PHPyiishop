<?php
namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MenuController extends Controller{
    //列表功能
    public function actionIndex(){
        //获取Menu表单数据总条数
        $total=Menu::find()->all();
        //实列化一下分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,//总条数
            'defaultPageSize'=>20//每页多少条
        ]);
        //显示数据
        $menus=Menu::find()->limit($pager->limit)->offset($pager->offset)->all();
        //添加数据到视图
        return $this->render('index',['menus'=>$menus,'pager'=>$pager]);

    }
    //添加功能
    public function actionAdd(){
        $model=new Menu();
        $request=\Yii::$app->request;
        //$permissions=\Yii::$app->authManager->getPermissions();// 获取路径
        //$data=Menu::find()->asArray()->all();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //编辑功能
    public function actionEdit($id){
        $model=Menu::findOne(['id'=>$id]);
        //$permissions=\Yii::$app->authManager->getPermissions();// 获取路径
        //$permissions=ArrayHelper::merge(['name'=>'请选择'],$permissions);
        //var_dump($permissions);exit;
        //$data=Menu::find()->asArray()->all();
        //显示关联权限
        //$auth=\Yii::$app->authManager;
        //$permissions=array_keys($auth->getPermissionsByRole($id));
        //var_dump($permissions);exit;
      // $model->permissions=$permissions;
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                //在处理权限问题
                //先清除所有的权限
                //$auth->removeChildren($rule);
                //然后重新赋值权限
               // if($model->permissions){
                    //foreach ($model->permissions as $permissionName){
                       // $permission=$auth->getPermission($permissionName);
                        //$auth->addChild($rule,$permission);

                   // }
               // }
                \Yii::$app->session->setFlash('seccuss','修改成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model,'permissions'=>$permissions]);
}
//删除功能
 public function actionDelete(){
        $id=\Yii::$app->request->post('id');
        $model=Menu::findOne(['id'=>$id]);
     if($model){
         $model->delete();
         return "success";
     }
     return"faile";
 }
//    public function behaviors()
//    {
//        return [
//            'rbac'=>[
//                'class'=>RbacFilter::className(),
//                'except'=>['logout','login','captcha','error']
//            ]
//        ];
//    }
}