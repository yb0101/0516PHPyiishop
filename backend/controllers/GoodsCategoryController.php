<?php

namespace backend\controllers;

use backend\models\Article_Category;
use backend\models\GoodsCategory;
use yii\data\Pagination;

class GoodsCategoryController extends \yii\web\Controller
{
    //添加商品分类
    public function actionAdd(){
        $model=new GoodsCategory();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
//            var_dump($request->post());exit;
            if($model->validate()){
                //判断是否是顶级分类
                if($model->parent_id==0){
                    //顶级分类
                    $model->makeRoot();

                }else{
                    //非顶级分类
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->prependTo($parent);
                }


            }
            \Yii::$app->session->setFlash('success','添加成功');

            return $this->redirect(['index']);

        }

        return $this->render('add',['model'=>$model]);
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
        $child->prependTo($parent);//prependTo
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
    public function actionIndex()
    {
        //1,获取数据库品牌信息
        $total=GoodsCategory::find()->where(['>','status',0])->count();
        //$bands=Brand::find()->all();
        //实列化一个分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,//总条数
            'defaultPageSize'=>2//每页多少条

        ]);
        //限制条件下的显示
        $goods=GoodsCategory::find()->where(['>','status',0])->limit($pager->limit)->offset($pager->offset)->all();
        //var_dump($bands);exit;
        //2,分配数据到视图
        return $this->render('index',['goods'=>$goods,'pager'=>$pager]);
    }
    //编辑功能
        public function actionEdit($id){
            $model = GoodsCategory::findOne(['id'=>$id]);
            $request = \Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());//回显加载数据
//            var_dump($request->post());exit;
                if($model->validate()){
                    //判断是否是顶级分类
                    if($model->parent_id==0){
                        //顶级分类
                        //处理是顶级分类，只修改名字不改变层级
                        if($model->getOldAttribute('parent_id')==0){
                            $model->save(false);
                        }else{
                            $model->makeRoot();
                        }

                    }else{
                        //非顶级分类
                        $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                        $model->prependTo($parent);
                    }


                }
                \Yii::$app->session->setFlash('success','修改成功');

                return $this->redirect(['index']);

            }

            return $this->render('add',['model'=>$model]);
        }
            //删除功能
    public function actionDelete($id){
        //$id = \Yii::$app->request->post('id');
        $model = GoodsCategory::findOne(['id'=>$id]);
        //判断是否是子节点，这是系统带的方法
        if($model->isLeaf()){//判断是否是叶子结点
            $model->deleteWithChildren();//删除当前节点以及子结点
            return $this->redirect(['index']);
        }else{
            \Yii::$app->session->setFlash('error','有孩子不能删除');
            return $this->redirect(['index']);
        }
    }
}
