/*
 @功能：购物车页面js
 @作者：diamondwang
 @时间：2013年11月14日
 */

$(function(){
//删除商品
    $('.a').click(function(){
        //console.debug(11111);
        //发送ajax请求，删除对应的数据 cookie或者数据库
        var date = {};
        date.goods_id = $(this).closest('tr').attr('data-id');
        var that = $(this);
        $.post('del.html',date,function(rtn){
            if(rtn){
                //成功
                that.closest('tr').remove();
                // getTotal();
            }else{
                //失败
            }
        });
    });
    var changeCart = function(goods_id,amount){
        $.post("/member/ajax.html",{goods_id:goods_id,amount:amount},function(){});
    }
    //减少
    $(".reduce_num").click(function(){
        var amount = $(this).parent().find(".amount");
        if (parseInt($(amount).val()) <= 1){
            alert("商品数量最少为1");
        } else{
            $(amount).val(parseInt($(amount).val()) - 1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        var total = 0;
        $(".col5 span").each(function(){
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));
        changeCart($(this).closest('tr').attr('data-id'),amount.val());
    });

    //增加
    $(".add_num").click(function(){
        var amount = $(this).parent().find(".amount");
        $(amount).val(parseInt($(amount).val()) + 1);
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        var total = 0;
        $(".col5 span").each(function(){
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));

        changeCart($(this).closest('tr').attr('data-id'),amount.val());
        // console.log(amount.val());
    });

    //直接输入
    $(".amount").blur(function(){
        if (parseInt($(this).val()) < 1){
            alert("商品数量最少为1");
            $(this).val(1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        //总计金额
        var total = 0;
        $(".col5 span").each(function(){
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));
        changeCart($(this).closest('tr').attr('data-id'),$(this).val());
    });
});

