<?php

namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sn
 * @property string $logo
 * @property integer $goods_category_id	int
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',

            'logo' => 'LOGO图片',
            'goods_category_id	int' => '商品分类id',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '在售否',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }
    //获取商品分类的ztree数据
    public static function getZNodes(){
        $top = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        $goodsCategories =  GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        /*
     array_unshift($goodsCategories,$top);*/
        // return ArrayHelper::merge([$top],$goodsCategories);
        return ArrayHelper::merge([$top],$goodsCategories);

        //var_dump($goodsCategories);exit;
    }


//    public function behaviors() {
//        return [
//            'tree' => [
//                'class' => NestedSetsBehavior::className(),//NestedSetsBehavior
//                'treeAttribute' => 'tree',//这里必须打开，因为可能有多棵树
//                // 'leftAttribute' => 'lft',
//                // 'rightAttribute' => 'rgt',
//                // 'depthAttribute' => 'depth',
//            ],
//        ];
//    }
//
//    public function transactions()
//    {
//        return [
//            self::SCENARIO_DEFAULT => self::OP_ALL,
//        ];
//    }
//
//    /* public static function find()
//     {
//         return new CategoryQuery(get_called_class());
//     }*/
    public function getGoodsCategory(){
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }
}
