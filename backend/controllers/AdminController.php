<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\LoginForm;
use backend\models\PasswordForm;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\User;

class AdminController extends \yii\web\Controller
{
    //列表功能
    public function actionIndex()
    {
        $where = \Yii::$app->request->get();
//var_dump($where);exit;
        $name = isset($where['username'])?$where['username']:'';

        //1，获取商品总条数
        $total=Admin::find()->andwhere(['>','status',0])->andWhere(['like','username',$name])->count();
        //实列化一个分页工具条
        $pager = new Pagination([//Pagination
                'totalCount'=>$total,
                'defaultPageSize'=>5
            ]
        );
        //查询数据
        $admins=Admin::find()->where(['>','status',0])->limit($pager->limit)->andWhere(['like','username',$name])->offset($pager->offset)->all();
        //分配数据到视图
        return $this->render('index',['admins'=>$admins,'pager'=>$pager]);
    }
//添加功能
public function actionAdd()
{
    $model = new Admin(['scenario'=>Admin::SCENARIO_ADD]);
    //var_dump($model);exit;
    $request = \Yii::$app->request;
    if ($request->isPost) {
        $model->load($request->post());
        $auth=\Yii::$app->authManager;
        $rules=$auth->getRole($model->rules);//获取过来的角色
        if ($model->validate()) {
//            //$model->last_login_time = time();
//            //$model->last_login_ip = \Yii::$app->request->userIP;
//            //$password=$model->password_hash;
//            //var_dump($password);exit;
//            //哈希密码
//            //$model->password_hash=\Yii::$app->security->generatePasswordHash($password);

            $model->save(false);
            //处理角色保存的问题
            $id=$model->id;
            $auth->assign($rules,$id);
            \Yii::$app->session->setFlash('success', '添加成功');
            return $this->redirect(['index']);
        }else{
            \Yii::$app->session->setFlash('error','添加失败');
        }
    }
    return $this->render('add',['model'=>$model]);
}
//编辑功能
public function actionEdit($id){
        $model=Admin::findOne(['id'=>$id]);
        $request=\Yii::$app->request;
        //处理角色回显的问题
    $auth=\Yii::$app->authManager;
    $name=$model->username;
    //var_dump($auth->getRole($name));exit();
    //$rules=$auth->getPermissionsByUser($id);
    $model->rules=array_keys($auth->getRolesByUser($id));
        if($request->isPost){
            //解释哈希密码后的正经密码
           // $password=\Yii::$app->security->decryptByPassword($model->password_hash,'');
            //var_dump($password);exit;
            $model->load($request->post());
            if($model->validate()){
                //哈希密码
                //$password=$model->password_hash;
                //$model->password_hash=\Yii::$app->security->generatePasswordHash($password);
                $model->save();
                //处理角色保存的问题
               //1,先清除关联关联的所有角色
                $auth->revokeAll($id);
                //2，在重亲保存传过来的角色
                if($model->rules){
                    foreach ($model->rules as $ruleName){
                        $rule=$auth->getRole($ruleName);
                        $auth->assign($rule,$id);
                    }
                }
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['index']);
            }
        }else{
                \Yii::$app->session->setFlash('error','修改失败');
        }
    return $this->render('add',['model'=>$model]);
}
//自己修改密码功能
//自己做
//    public function actionEedit(){
//        $model=new LoginForm();
//        $identity=\Yii::$app->user->identity;//调用USER主键上的方法
//        $id=$identity->id;//里面的ID
//        $request=\Yii::$app->request;
//        //提交表单修改
//        if($request->isPost){
//            $model->load($request->post());//加载数据
//            //var_dump($model);exit;
//            //验证密码（处理老密码问题）
//           $result =\Yii::$app->security->validatePassword($model->password,$identity->password_hash);
//            if($result){
//                //处理新密码问题
//                if($model->newpassword != $model->nnewpassword){
//                    $model->addError('nnewpassword','两次输入不一致');
//                }else{
//                    //查询出一条记录,并更新密码
//                    $model=Admin::findOne(['id'=>$id]);
//                    $model->password_hash=\Yii::$app->security->generatePasswordHash($model->newpassword);
//                    //保存
//                   /* $this->updated_at=time();
//                    if($this->password){//判断一下，如果有修改密码就吧新密码加密保存，没有就还是原来的密码
//                        $this->auth_key=Yii::$app->security->generateRandomString();
//                        $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
//                    }*/
//                    $model->save(false);
//                    \Yii::$app->session->setFlash('success','密码修改成功');
//                    return $this->redirect('index');
//                }
//
//
//            }else{
//                $model->addError('password_hash','密码不正确');
//            }
//
//        }
//        return $this->render('eedit',['model'=>$model,'id'=>$identity]);
//    }
    //标准版
    public function actionEedit(){
    if(\Yii::$app->user->isGuest){
        return $this->render(['eedit']);
    }
    $model=new PasswordForm();
    $request=\Yii::$app->request;
    if($request->isPost){
        $model->load($request->post());
        if($model->validate()){
            $admin=\Yii::$app->user->identity;
            $admin->password=$model->newpassword;
            $admin->save();
        }
    }
        return $this->render('eddit',['model'=>$model]);
    }

//静态删除功能
public function actionDelete(){
    $id=\Yii::$app->request->post('id');
    $model=Admin::findOne(['id'=>$id]);
    if($model){
        $model->status=0;
        $model->save();
        return "success";
    }
        return"faile";
}
//登陆功能
public function actionLogin(){
    //显示登陆页面
    $model =new LoginForm();

    $request = \Yii::$app->request;
    if($request->isPost){
        $model->load($request->post());
       // var_dump($model->checkbook);exit;
        if($model->validate()){
            //认证
            if($model->login()){
                \Yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['admin/index']);
            }
        }
    }
    return $this->render('login',['model'=>$model]);
}

//验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //设置验证码
                'minLength' => 4,
                'maxLength' => 4
            ],
        ];
    }
//退出
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('info', '退出成功');
        return $this->redirect(['admin/index']);
    }

    //设置权限 未登录不能操作
//    public function behaviors()
//    {
//        return [
//            'access'=>[
//                'class'=>AccessControl::className(),
//                'only'=>[],
//                'rules'=>[
//                    [
//                        'allow'=>true,
//                        'actions'=>['login','index','captcha'],
//                        'roles'=>['?'],
//                    ],
//                    [
//                        'allow'=>true,
//                        'actions'=>['logout','add','edit','delete','index','captcha'],
//                        'roles'=>['@'],
//                    ],
//                ],
//            ]
//        ];
//    }
}
