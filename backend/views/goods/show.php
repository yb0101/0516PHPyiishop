<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
//var_dump($articles);exit;
?>
<a href="<?=\yii\helpers\Url::to(['goods/index'])?>" class="btn btn-default"><span class="glyphicon glyphicon-menu-up"></span></a>
<table class="table table-bordered table-responsive text-center">
    <tr>
        <th>详情</th>
    </tr>
    <tr>
        <td><?=$data->content?></td>
    </tr>
</table>

