<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/3
 * Time: 0:20
 */
?>
    <a href="<?=\yii\helpers\Url::to(['article-category/add'])?>" class="btn btn-danger"><span class="glyphicon glyphicon-plus"></span></a>
    <table class="table table-bordered table-responsive text-center">
        <tr>
            <th>ID</th>
            <th>文章名称</th>
            <th>文章简介</th>
            <th>排序</th>
            <th>商品状态</th>
            <th>操作</th>
        </tr>
        <?php foreach ($article_categorys as $article_category):?>
            <tr>
                <td><?=$article_category->id?></td>
                <td><?=$article_category->name?></td>
                <td><?=$article_category->intro?></td>
                <td><?=$article_category->sort?></td>
                <td><?=$article_category->status?></td>
                <td><a href="<?=\yii\helpers\Url::to(['article-category/edit','id'=>$article_category->id])?>" class="btn btn-warning"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="<?=\yii\helpers\Url::to(['article-category/delete','id'=>$article_category->id])?>" class="btn btn-success"><span class="glyphicon glyphicon-remove"></span></a></td>
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