<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['menu/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>菜单ID</th>
            <th>菜单名称</th>
            <th>父ID</th>
            <th>路由</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        <?php foreach ($menus as $menu):?>
            <tr data-id="<?=$menu->id?>">
                <td><?=$menu->id?></td>
                <td><?=str_repeat('------------',($menu->parent_id?1:0)).$menu->name?></td>
                <td><?=$menu->parent_id?></td>
                <td><?=$menu->url?></td>
                <td><?=$menu->sort?></td>
                <td><a href="<?=\yii\helpers\Url::to(['menu/edit','id'=>$menu->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
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
$url_del=\yii\helpers\Url::to(['admin/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $(".del_btn").click(function() {
        
      if(confirm("真的想好删除了吗？")){
          var tr =$(this).closest('tr');//往上找父节点
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



