<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>分类id</th>
            <th>排序</th>
            <th>状态</th>
            <th>发布时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($articles as $article):?>
            <tr data_id="<?=$article->id?>">
                <td><?=$article->id?></td>
                <td><?=$article->name?></td>
                <td><?=$article->intro?></td>
                <td><?=$article->article_category_id?></td>
                <td><?=$article->sort?></td>
                <td><?=$article->status?></td>
                <td><?=$article->create_time?></td>
                <td><a href="<?=\yii\helpers\Url::to(['article/edit','id'=>$article->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['article/show','id'=>$article->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-zoom-out"></span></a>
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



