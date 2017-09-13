<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\Article_Category;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ArticleController extends \yii\web\Controller
{
    //列表功能
    public function actionIndex()
    {
        //1,获取数据库品牌信息
        $total=Article::find()->count();
        //$bands=Brand::find()->all();
        //实列化一个分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,//总条数
            'defaultPageSize'=>2//每页多少条

        ]);
        //限制条件下的显示
        ////当前页码$books=\frontend\models\Book::find()->limit($pager->limit)->offset($pager->offset)->all();
        $articles=Article::find()->limit($pager->limit)->offset($pager->offset)->all();
        //var_dump($bands);exit;
        //2,分配数据到视图
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);
    }
//查询功能
    public function actionShow($id){
        //根据id获取数据
        //var_dump($id);exit;
        $data=ArticleDetail::findOne(['article_id'=>$id]);
        //$model = Book::findOne(['id' => $id]);
        //var_dump($data);exit;
        //分配数据到视图页面
        //return $this->render('article/show',['model'=>$model]);
       // return $this->redirect("show");
        return $this->render('show',['data'=>$data]);
    }
    //添加功能
    public function actionAdd(){

        $model =new Article();
        $data = Article_Category::find()->asArray()->all();
        //$data1 = ArrayHelper::map($data,'id','name');
        //var_dump($data1);exit;
        //$content= new Article_Detail();
        $content=new ArticleDetail();
        //var_dump($data );exit;
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate() && $content->validate()){
               $model->create_time=time();
                $model->save(false);
                //获取新保存进去的id
                $content->article_id=$model->id;
                //对应保存文章内容
                $content->save(false);
                \Yii::$app->session->setFlash('success','添加成功！！');
                return $this->redirect(['article/index']);
            }else{
                //验证失败

                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'data'=>$data,'content'=>$content]);
    }
    //编辑
    public function actionEdit($id)
    {
        //1,展示表单，将模型传递给表单
        $request = \Yii::$app->request;
        //$model=new Author();
        $model = Article::findOne(['id' => $id]);
        $content=ArticleDetail::findOne(['article_id'=>$id]);
        $data=Article_Category::find()->asArray()->all();
        if ($request->isPost) {
            $model->load($request->post());
            $content->load($request->post());
            if ($model->validate() && $content->validate()) {
                    $model->save(false);
                    //$content->article_id=$model->id;
                    $content->save(false);
                    return $this->redirect(['article/index']);
                } else {
                    //输出错误信息
                    var_dump($model->getErrors());
                    exit;
                }
            }
            //渲染到添加页面
            return $this->render('add', ['model' => $model, 'data' => $data,'content'=>$content]);
        }
        //删除功能
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = Article::findOne(['id'=>$id]);
        if($model){
            $model->status=-1;
            $model->save(false);
            return "success";
        }
        return "fail";
    }

}
