<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    //添加功能
    public function actionAdd(){
        $model =new Brand();
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //处理上传大数据
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()){
                if($model->file){
                    //处理上传文件的信息
                    //拼接一个地址
                    $file = '/upload/'.uniqid().'.'.$model->file->getExtension();
                    //保存进去
                    $model->file->saveAs(\Yii::getAlias('@webroot').$file,false);
                    $model->logo=$file;//把上传文件的地址赋值给logo字段
                }
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！！');
                return $this->redirect(['brand/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //列表功能
    public function actionIndex()
    {
        //1,获取数据库品牌信息
        $total=Brand::find()->count();
        //$bands=Brand::find()->all();
        //实列化一个分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,//总条数
            'defaultPageSize'=>2//每页多少条

        ]);
        //限制条件下的显示
        ////当前页码$books=\frontend\models\Book::find()->limit($pager->limit)->offset($pager->offset)->all();
        $brands=Brand::find()->limit($pager->limit)->offset($pager->offset)->all();
        //var_dump($bands);exit;
        //2,分配数据到视图
        return $this->render('index',['brands'=>$brands,'pager'=>$pager]);
    }
//编辑功能
    public function actionEdit($id){
        $model =Brand::findOne(['id'=>$id]);
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //处理上传大数据
            $model->file=UploadedFile::getInstance($model,'file');
            if($model->validate()){
                if($model->file){
                    //处理上传文件的信息
                    //拼接一个地址
                    $file = '/upload/'.uniqid().'.'.$model->file->getExtension();
                    //保存进去
                    $model->file->saveAs(\Yii::getAlias('@webroot').$file,false);
                    $model->logo=$file;//把上传文件的地址赋值给logo字段
                }
                $model->save(false);
                \Yii::$app->session->setFlash('success','编辑成功！！');
                return $this->redirect(['brand/index']);
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
        $model=Brand::findOne(['id'=>$id]);
        //var_dump($model);exit;
        //改变状态
        $model->status=-1;
        //保存
        $model->save(false);
        //跳转
        return $this->redirect(['brand/index']);
    }
}
