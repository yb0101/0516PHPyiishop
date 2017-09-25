<?php
?>
<a href="<?=\yii\helpers\Url::to(['rbac/add'])?>" class="btn btn-default"><span class="glyphicon glyphicon-barcode"></span></a>


<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>权限名称</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($permissions as $permission):?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td><a href="<?=\yii\helpers\Url::to(['rbac/edit','name'=>$permission->name])?>" class="btn btn-default"><span class="glyphicon glyphicon-wrench"></span></a>
            <a href="<?=\yii\helpers\Url::to(['rbac/delete','name'=>$permission->name])?>" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a></td></td>
    </tr>
   <?php endforeach;?>
    </tbody>
</table>
<?php

/*<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="DataTables-1.10.15/media/css/jquery.dataTables.css">

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="DataTables-1.10.15/media/js/jquery.js"></script>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="DataTables-1.10.15/media/js/jquery.dataTables.js"></script>*/

/**
 * @var $this \yii\web\View
 */
//文件注册
$this->registerCssFile('@web/assets/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/assets/media/js/jquery.js');
$this->registerJsFile('@web/assets/media/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
//注册JS代码
$url_del=\yii\helpers\Url::to(['brand/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
$(document).ready( function () {
$('#table_id_example').DataTable();
} )
JS
));
?>

