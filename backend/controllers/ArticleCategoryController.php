<?php

namespace backend\controllers;

use backend\models\Article_Category;
use yii\data\Pagination;

class ArticleCategoryController extends \yii\web\Controller
{
    //列表功能
    public function actionIndex()
    {
        //1,获取数据库品牌信息
        $total=Article_Category::find()->where(['>','status',0])->count();
        //$bands=Brand::find()->all();
        //实列化一个分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,//总条数
            'defaultPageSize'=>2//每页多少条

        ]);
        //限制条件下的显示
        $article_categorys=Article_Category::find()->where(['>',"status",0])->limit($pager->limit)->offset($pager->offset)->all();
        //var_dump($bands);exit;
        //2,分配数据到视图
        return $this->render('index',['article_categorys'=>$article_categorys,'pager'=>$pager]);
    }
//添加功能
    public function actionAdd(){
        $model =new Article_Category();
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //处理上传大数据
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！！');
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //编辑功能
    public function actionEdit($id){
        $model =Article_Category::findOne(['id'=>$id]);
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if($model->validate()){
                $model->save(false);
                \Yii::$app->session->setFlash('success','编辑成功！！');
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //假删除功能
    public function actionDelete($id){
        //获取id
        $model=Article_Category::findOne(['id'=>$id]);
        //var_dump($model);exit;
        //改变状态
        $model->status=-1;
        //保存
        $model->save(false);
        //跳转
        return $this->redirect(['article-category/index']);
    }
}
