<?php
namespace backend\models;
use yii\db\ActiveRecord;
class GoodsIntro extends ActiveRecord{
    public function rules()
    {
        return [
            [['content'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'content' => '商品描述',
        ];
    }
}