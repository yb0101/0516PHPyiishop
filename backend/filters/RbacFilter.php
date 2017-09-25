<?php
namespace backend\filters;


use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        //当前访问的路由 $action->uniqueId   user/add
        //return \Yii::$app->user->can($action->uniqueId); //检查是否有当前路由的权限
        if (!\Yii::$app->user->can($action->uniqueId)) {
            //判断,如果用户没有登录,则引导用户跳转到登录页面
            if (\Yii::$app->user->isGuest) {
                //跳转必须要执行send方法,确保页面直接跳转.否则该次操作没有被拦截,相当于返回了true.
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            //如果没有权限,则显示提示信息页面
            throw new ForbiddenHttpException('对不起,您没有该操作权限');
        }
        return parent::beforeAction($action);
    }
}

