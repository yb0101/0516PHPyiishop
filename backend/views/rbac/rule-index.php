<?php
?>
<a href="<?=\yii\helpers\Url::to(['rbac/add-rule'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>
<table class="table table-bordered table-responsive text-center">
    <tr>
        <td>角色名称</td>
        <td>角色描述</td>
        <td>操作</td>
    </tr>
    <?php foreach ($rules as $rule):?>
        <tr>
            <td><?=$rule->name?></td>
            <td><?=$rule->description?></td>
            <td><a href="<?=\yii\helpers\Url::to(['rbac/edit-rule','name'=>$rule->name])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
                <a href="<?=\yii\helpers\Url::to(['rbac/delete-rule','name'=>$rule->name])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td>
        </tr>
    <?php endforeach;?>
</table>
