<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

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
    public function actionDelete(){
        //获取id
//        $model=Brand::findOne(['id'=>$id]);
//        //var_dump($model);exit;
//        //改变状态
//        $model->status=-1;
//        //保存
//        $model->save(false);
//        //跳转
//        return $this->redirect(['brand/index']);
        $id = \Yii::$app->request->post('id');
        $model = Brand::findOne(['id'=>$id]);
        if($model){
            $model->status=-1;
            $model->save(false);
            return "success";
        }
            return "fail";
    }
    //神奇插件

    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
               /* 'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },
               */
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //输出文件路径
                    //$action->output['fileUrl'] = $action->getWebUrl();
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    //$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云，并且返回七牛云的地址
                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    $file =$action->getSavePath();//解析路径
                    //上传文件到七牛云 同时指定一个KEY(名称，文件名)
                    $qiniu->uploadFile($file,$key);
                    //获取七牛云上的文件的URL地址
                    $url = $qiniu->getLink($key);
                    //输出文件路径
                    $action->output['fileUrl'] = $url;
                },
            ],
        ];
    }
    //测试七牛云
    public function actionQiniu(){
        /*$config = [
            'accessKey'=>'HhRHTi_menIUzS-9z35hgALpmhIxKYZhnPYKlxn3',
            'secretKey'=>'_TrEryrmSKhrzPNGnQUkeasRh_lSkq2I8qP6vBBw',
            'domain'=>'http://ovyba1lk7.bkt.clouddn.com/',
            'bucket'=>'0516php',
            'area'=>Qiniu::AREA_HUADONG
        ];*/



        $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
        $key = '1.jig';
        $file =\Yii::getAlias( '@webroot/upload/1.jpg');//解析路径
        //上传文件到七牛云 同时指定一个KEY(名称，文件名)
        $qiniu->uploadFile($file,$key);
        //获取七牛云上的文件的URL地址
        $url = $qiniu->getLink($key);
        var_dump($url);
    }
}
