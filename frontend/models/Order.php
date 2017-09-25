<?php
namespace frontend\models;
use backend\models\Goods;
use yii\db\ActiveRecord;
use frontend\models\OrderGoods;

class Order extends ActiveRecord{
    public function rules()
    {
        return [

        ];
    }
    public static $deliveries = [
        1=>['顺丰快递',25,'服务好,价格高,速度最快'],
        2=>['圆通快递',15,'服务一般,价格便宜,速度一般'],
        3=>['EMS',20,'服务一般,价格高,速度一般,全国任何地方都可以到'],
    ];
    public static $play = [
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        3=>['上门自提','	自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['邮局汇款','	通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    //建立订单表与商品表的相关
    public function getGoods(){
        return $this->hasMany(OrderGoods::className(),['order_id'=>'id']);
    }
}