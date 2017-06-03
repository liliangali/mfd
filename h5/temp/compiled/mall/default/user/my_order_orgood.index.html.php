<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui" >
<title>待评价商品</title>
    <link href="public/static/wap/css/public.css" rel="stylesheet" />
    <link href="public/static/wap/css/slx_style.css" rel="stylesheet" />
</head>

<body class="body_bg">

<div class="container">


    <header class="topBar" id="header">
        <div class="wrap">
            <span class="back" onClick="history.go(-1)"></span>
            <h1>待评价</h1>
        </div>
    </header>

    <div id="box">

        <?php $_from = $this->_var['ordergoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'goods');$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['goods']):
        $this->_foreach['loop']['iteration']++;
?>
        <div class="pro_list">
            <a href="/my_comment-publish-<?php echo $this->_var['goods']['rec_id']; ?>-<?php echo $this->_var['goods']['type']; ?>.html">
                <div class="c_db pro_con">

                    <p><img src="<?php echo $this->_var['goods']['goods_image']; ?>"></p>

                    <div class="c_bf1">
                        <h2><?php echo $this->_var['goods']['goods_name']; ?></h2>

                        <h3>
                            <?php if ($this->_var['goods']['type'] == 'fdiy'): ?>
                            <?php echo $this->_var['goods']['format_params']; ?>
                            <?php endif; ?>
                        </h3>
                    </div>

                </div>

                <div class="c_db comm_btn">
                    <p class="c_bf1"><?php echo price_format($this->_var['goods']['price']); ?>×<?php echo $this->_var['goods']['quantity']; ?></p>

                    <p><span>去评价</span></p><i class="c_jtr"></i>
                </div>

            </a>
        </div>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

</div>


</div>

<script src="public/global/jquery-1.8.3.min.js"></script>
<script src="public/static/wap/js/public.js"></script>
<script>

var updata={
    page:1,//当前条数
    num:50,//每次加载条数
    init:function(){
        //$(window).on('scroll',updata.getData);
    },
    getData:function(){
       var winh=$(window).height(),
            $container=$('#box'),
            bodyh=$container.height();
        if(($(window).scrollTop()+winh)>=bodyh){
            $(window).off('scroll',updata.getData);
            $container.append('<p style="text-align:center" class="loading">加载中...</p>');

                updata.page+=updata.num;
                var aQuery = window.location.href.split("?");  //取得Get参数
                var data = "limit="+updata.page;
                if(aQuery && aQuery[1]){
                    data = "limit="+updata.page+"&"+aQuery[1];
                }
                $.ajax( {
                    url: "/my_order-ajax_order_list.html?status=<?php echo $this->_var['status']; ?>",
                    type: "GET",
                    data: data,
                    success: function(data) {
                        var data = $.parseJSON(data);
                        if(data.done == false){
                            $('.loading').html('');
							var obj=$('<p style="text-align:center" class="loading">加载完毕</p>');
                            $container.append(obj);
							setTimeout(function(){
								obj.remove();
							},1000)

                            return
                        }
                        var html = '';
                        //var len=data.retval.length;
                        //console.log(len)
                        if(data.done){
                          $.each(data.retval,function(i,d){
                            html+='<div class="imagetx"><h4><p class="ddh fl">订单号:'+d.order_sn+'</p><p class="dfk fr">'+d.status_name+'</p></h4><dl><a href="/my_order-detail-'+d.order_id+'.html">';
                                 for(var y=1;y<=d.goods_num;y++){
                                     html+='<dd><img src="'+d.item[y-1].goods_image+'" width="41" height="57"></dd>';
                                     if(d.goods_num  == 1){
                                       html+='<dt>';
                                       html+='<p>'+d.item[y-1].goods_name+'</p>';
                                       if(d.item[y-1].type == 'fdiy'){
                                         html+='<p style="color:#717171;">定制</p>'
                                       }else{
                                             /* $.each(d.goods[y-1].formatsize,function(y,size){
                                                html+='<p style="color:#717171;">'+size+'</p>';
                                             }); */
                                       }
                                       html+='<p>'+d.add_time+'</p>';
                                       html+='</dt>';
                                       html+='<span style="margin-top:10px;float:right;">¥'+d.item[y-1].price+' * '+d.item[y-1].quantity+'</span>'
                                     }
                                 }
                            html +='</a></dl>';
                            html +='<dl>实付款：<span style="color:#ff4400">¥'+d.final_amount+'</span></dl>';
                            html +='</div>';
                          })
                            $container.append(html);

                            $container.children('.loading').remove();
                            setTimeout(function(){
                              $(window).on('scroll',updata.getData);
                            },500)
                        }else{
                            return;
                        }
                    }
                });

        }
    }
}
updata.init()
</script>



</body>
</html>
