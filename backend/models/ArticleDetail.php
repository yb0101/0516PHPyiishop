<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord{
    public function rules()
    {
        return [
            [['content'], 'string', 'max' => 255],
        ];
    }

    public static function tableName()
    {
        return 'article_detail';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'ID',
            'content' => '文章内容',
        ];
    }

}