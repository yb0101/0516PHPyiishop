<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    //列表功能
    public function actionIndex()
    {
        $where = \Yii::$app->request->get();
//var_dump($where);exit;
        $name = isset($where['name'])?$where['name']:'';
        $sn = isset($where['sn'])?$where['sn']:'';
        $cprice = isset($where['cprice'])?$where['cprice']:'';
        $dprice = isset($where['dprice'])?$where['dprice']:'';

        //1，获取商品总条数
        $total=Goods::find()->where(['>','status',0])->andFilterWhere(['like','name',$name])->andFilterWhere(['like','sn',$sn])->andFilterWhere(['between','shop_price',$cprice,$dprice])->count();
        //实列化一个分页工具条
        $pager = new Pagination([
            'totalCount'=>$total,
             'defaultPageSize'=>5
        ]
        );
        //查询数据
        $goods=Goods::find()->where(['>','status',0])->limit($pager->limit)->andFilterWhere(['like','name',$name])->andFilterWhere(['like','sn',$sn])->andFilterWhere(['between','shop_price',$cprice,$dprice])->offset($pager->offset)->all();
        //分配数据到视图
        return $this->render('index',['goods'=>$goods,'pager'=>$pager]);
    }
    //展示功能
    public function actionShow($id){
        $data=GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('show',['data'=>$data]);
    }
    //添加功能
    public function actionAdd(){
        $content =new GoodsIntro();//内容列表
        $model = new Goods();//商品列表
        $brand= Brand::find()->asArray()->all();//品牌信息
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate() && $content->validate()){
                //先保存基本表
                $model->create_time=time();

                $model->view_times=0;
                //根据新添加的ID在保存详情信息

                //计数列表
                $d = date('Y-m-d',time());//当前日期
                $date = GoodsDayCount::findOne(['day'=>$d]);
                if (!$date){
                    $date = new GoodsDayCount();
                    $date->day = $d;
                    $date->count=1;
                    $date->save();

                }else{
                    $date->count+=1;//计算
                    //var_dump($date);exit();
                    $date->save();//不走外面创建表了，直接给计数加1
                }
                //处理货号
                $s = $date->count;
                $model->sn=date('Ymd').str_repeat('0',4-strlen($s)).$s;//方法一
                // $model->sn=date('Ymd').str_pad($s,4,"0",STR_PAD_LEFT);、、方法二

                $model->save();
                $id=\Yii::$app->db->getLastInsertID();
                $content->goods_id=$id;
                $content->save(false);
                \Yii::$app->session->setFlash('success','添加成功');

                return $this->redirect(['index']);

            }

        }

        return $this->render('add',['model'=>$model,'content'=>$content,'brand'=>$brand]);
            }
    //编辑功能
    public function actionEdit($id){
        $content=new GoodsIntro();
        $content =GoodsIntro::findOne(['goods_id'=>$id]);//内容列表new GoodsIntro()
        $model = Goods::findOne(['id'=>$id]);//商品列表
        $brand= Brand::find()->asArray()->all();//品牌信息
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate()){
                //先保存基本表
                $model->create_time=time();

                $model->view_times=0;
                //根据新添加的ID在保存详情信息

                $model->save();
                $content->goods_id=$model->id;
                $content->save(false);
                \Yii::$app->session->setFlash('success','添加成功');

                return $this->redirect(['index']);

            }

        }

        return $this->render('add',['model'=>$model,'content'=>$content,'brand'=>$brand]);
    }
    //删除功能
    public function actionDelete($id){
        Goods::findOne(['id'=>$id])->delete();//删除基本表
        GoodsIntro::findOne(['goods_id'=>$id])->delete();//删除信息表
        $count=GoodsDayCount::findOne(['day'=>date('Ymd')]);
        $count->count-=1;
        $count->save();
        return $this->redirect(['index']);
    }
    //图片处理
    public function actionCheck(){
        $request=\Yii::$app->request;
        $id=$request->get('id');
        //查看相册
        $model=GoodsGallery::find()->where(['goods_id'=>$id])->asArray()->all();
        //接收goods_id保存

        $path=$request->get('fileUrl');
        if($path){
            $goods_id=$request->get('goods_id');
            $model=new GoodsGallery();
            $model->goods_id=$goods_id;
            $model->path=$path;
            $model->save(false);
            return json_encode(['success'=>true,'msg'=>'保存成功']);
        }
        //接收图片的URL
        return $this->render('check',['model'=>$model]);
    }

    public function actionAjax(){
        //接收数据,删除图片
        $request=\Yii::$app->request;
        $path=$request->get('fileUrl');
        $result=GoodsGallery::findOne(['path'=>$path])->delete();
        if($result){
            echo "{'success':true,'msg':'删除成功'}";
        }
    }

    //神奇插件

    public function actions() {
        return [
            'upload' => [
               'class' => 'kucha\ueditor\UEditorAction',

            ],
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
    public function actionTest(){
        //创建1级分类(顶级分类)
        //$model = new GoodsCategory(['name'=>'大保健']);
        //$model->parent_id = 0;
        //$model->makeRoot();
        //var_dump($model->getErrors());exit;
        //echo "成功";
        //创建子分类
        //$parent = GoodsCategory::findOne(['id'=>1]);
        $parent = GoodsCategory::findOne(['id'=>1]);
        $child = new GoodsCategory(['name' => '面部护理']);
        $child->parent_id = 1;
        $child->prepenTo($parent);//prependTo
        echo '操作成功';
    }
    //测试ZTREE
    public function actionZtree(){
        //不加载布局文件
        //$this->layout=false;
        $goodscategories=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //var_dump($goodscategories);exit;
        return $this->renderPartial('ztree',['goodscategories'=>$goodscategories]);//局部渲染，不加载布局文件

    }
    }
