<?php
namespace frontend\models;
use yii\db\ActiveRecord;
use backend\models\Goods;
class Cart extends ActiveRecord{

    public static function delCart($goods_id)
    {
        if (\Yii::$app->user->isGuest) {
            //删除cookie中数据
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            //var_dump($cookie);exit;
            if ($cookie !== null) {
                //有cookie
                $date = unserialize($cookie->value);
                if (isset($date[$goods_id])) {
                    //有对应的商品
                    unset($date[$goods_id]);
                    $cookie->value = serialize($date);
                   \Yii::$app->response->cookies->add($cookie);
                    //var_dump($cookie);exit;
                    return true;
                }
            }
        } else {//删除数据库中的数据
            $model = self::findOne(['goods_id' => $goods_id, 'member_id' => \Yii::$app->user->id]);
            if ($model !== null) {
                return $model->delete();
            }
        }
        return false;
    }
    //建立订单表与商品表的相关
    public function getGoods(){
        return $this->hasOne(Goods::className(),['id'=>'goods_id']);
    }
}

