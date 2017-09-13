<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
<a href="<?=\yii\helpers\Url::to(['brand/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
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
        <tr data_id="<?=$brand->id?>">
            <td><?=$brand->id?></td>
            <td><?=$brand->name?></td>
            <td><?=$brand->intro?></td>
            <td><img src="<?=$brand->logo?>"width="150px" ></td>
            <td><?=$brand->sort?></td>
            <td><?=$brand->status?></td>
            <td><a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$brand->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                <a href="javaseript:;" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
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
//注册JS代码
$url_del=\yii\helpers\Url::to(['brand/delete']);
    $this->registerJs(new \yii\web\JsExpression(
         <<<JS
    $(".del_btn").click(function() {
        
      if(confirm("真的想好删除了吗？")){
          var tr =$(this).closest('tr');
          var id =tr.attr('data_id');
          $.post("{$url_del}",{id:id},function(data) {
            if(data == 'success'){
                alert('删除成功');
                tr.hide('slow');
            }else{
                alert('系统繁忙');
            }
          })
      }
    })
JS
    ));



