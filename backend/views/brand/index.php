<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
<a href="<?=\yii\helpers\Url::to(['brand/add'])?>" class="btn btn-danger"><span class="glyphicon glyphicon-plus"></span></a>
<table class="table table-bordered table-responsive text-center">
    <tr>
        <th>ID</th>
        <th>品牌名称</th>
        <th>品牌简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>商品状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brands as $brand):?>
        <tr>
            <td><?=$brand->id?></td>
            <td><?=$brand->name?></td>
            <td><?=$brand->intro?></td>
            <td><img src="<?=$brand->logo?>"width="150px" ></td>
            <td><?=$brand->sort?></td>
            <td><?=$brand->status?></td>
            <td><a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$brand->id])?>" class="btn btn-warning"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="<?=\yii\helpers\Url::to(['brand/delete','id'=>$brand->id])?>" class="btn btn-success"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination' =>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);