<?php

namespace backend\controllers;

use backend\models\Brand;

class BrandController extends \yii\web\Controller
{
    public function actionAdd(){
        $model =new Brand();
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！！');
                return $this->redirect(['brand\index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

}
