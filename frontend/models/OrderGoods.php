<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class OrderGoods extends ActiveRecord{
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'amount'], 'integer'],
            [['price', 'total'], 'number'],
            [['goods_name', 'logo'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'goods_id' => '商品id',
            'goods_name' => '商品名称',
            'logo' => '图片',
            'price' => '价格',
            'amount' => '数量',
            'total' => '小计',
        ];
    }
}