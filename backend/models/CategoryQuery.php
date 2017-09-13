<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\ActiveRecord;

class CategoryQuery extends ActiveRecord
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}