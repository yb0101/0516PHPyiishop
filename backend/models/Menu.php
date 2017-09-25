<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $ url
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }
    public static function getPermissionItems(){
        $permissions=\Yii::$app->authManager->getPermissions();//获取全部的信息
        $itms=[];
        foreach ($permissions as $permission){
            $itms[$permission->name] = $permission->name;
        }
        //var_dump( $itms);exit;
        $itmss=ArrayHelper::merge([0=>'请选择'],$itms);
        return  $itmss;
    }
public static function getData(){
    $parents=self::find()->where(['=','parent_id',0])->asArray()->all();//获取全部的数据
    $data=ArrayHelper::map($parents,'id','name');
//        var_dump($parents);exit();
    $datas=ArrayHelper::merge([0=>'顶级菜单'],$data);
//    var_dump($datas);exit();
        return $datas;
        }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'name' => '名称',
            'parent_id' => '父ID',
            ' url' => '路由',
            'sort' => '排序',
        ];
    }
}
