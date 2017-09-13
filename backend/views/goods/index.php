<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['goods/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
    <div>
        <form action="<?=\yii\helpers\Url::to(['goods/index'])?>" method="get">
            <input type="text" name="name" placeholder="商品名称"/>
            <input type="text" name="sn" placeholder="货号"/>
            <input type="text" name="cprice" placeholder="价格从"/>
            <input type="text" name="dprice" placeholder="到"/>

            <input type="submit" value=" 搜索 " class="button" />
        </form>


    </div>

    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>ID</th>
            <th>商品名称</th>
            <th>货号</th>
            <th>LOGO图片</th>
            <th>商品分类id</th>
            <th>品牌分类</th>
            <th>市场价格</th>
            <th>商品价格</th>
            <th>库存</th>
            <th>是否在售</th>
            <th>状态</th>
            <th>排序</th>
            <th>添加时间</th>
            <th>浏览次数</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods as $good):?>
            <tr data_id="<?=$good->id?>">
                <td><?=$good->id?></td>
                <td><?=$good->name?></td>
                <td><?=$good->sn?></td>
                <td><img src="<?=$good->logo?>"width="150px" ></td>
                <td><?=$good->goods_category_id?></td>
                <td><?=$good->brand_id?></td>
                <td><?=$good->market_price?></td>
                <td><?=$good->shop_price?></td>
                <td><?=$good->stock?></td>
                <td><?=$good->is_on_sale?></td>
                <td><?=$good->status?></td>
                <td><?=$good->sort?></td>
                <td><?=date('Y-m-d H:m:s',$good->create_time)?></td>
                <td><?=$good->view_times?></td>
                <td><a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$good->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['goods/show','id'=>$good->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-zoom-out"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['goods/check','id'=>$good->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-heart"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['goods/delete','id'=>$good->id])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
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



