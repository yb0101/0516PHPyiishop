<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>ID</th>
            <th>树</th>
            <th>左值</th>
            <th>右值</th>
            <th>层级</th>
            <th>名字</th>
            <th>父ID</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods as $good):?>
            <tr>
                <td><?=$good->id?></td>
                <td><?=$good->tree?></td>
                <td><?=$good->lft?></td>
                <td><?=$good->rgt?></td>
                <td><?=$good->depth?></td>
                <td><?=str_repeat('%--%',$good->depth).$good->name?></td>
                <td><?=$good->parent_id?></td>
                <td><?=$good->intro?></td>
                <td><a href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$good->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['goods-category/delete','id'=>$good->id])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
/**
 * @var $this \yii\web\View
 */
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination' =>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);





