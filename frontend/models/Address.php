<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public function rules()
    {
        return [
            [['username','address','tel'],'required'],
            ['status','safe'],
            [['province','city','area'],'string'],
        ];
    }
}