<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
    <div>
        <form action="<?=\yii\helpers\Url::to(['admin/index'])?>" method="get">
            <input type="text" name="username" placeholder="用户名"/>

            <input type="submit" value=" 搜索 " class="button" />
        </form>


    </div>

    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>创建时间</th>
            <th>邮箱</th>
            <th>状态</th>
            <th>最后登陆时间</th>
            <th>最后登陆IP</th>
            <th>操作</th>
        </tr>
        <?php foreach ($admins as $admin):?>
            <tr data_id="<?=$admin->id?>">
                <td><?=$admin->id?></td>
                <td><?=$admin->username?></td>
                <td><?=date('Y-m-d H:m:s',$admin->created_at)?></td>
                <td><?=$admin->email?></td>
                <td><?=$admin->status?></td>
                <td><?=date('Y-m-d H:m:s',$admin->last_login_time)?></td>
                <td><?=$admin->last_login_ip?></td>
                <td><a href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$admin->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['admin/eedit','id'=>$admin->id])?>" class="btn btn-default"><span class="glyphicon glyphicon-apple"></span></a>
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



