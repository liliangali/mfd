<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no, minimal-ui" >
<title>订单列表</title>

<link href="public/static/wap/css/public.css" rel="stylesheet" />
<link type="text/css" href="public/static/wap/css/productlist.css" rel="stylesheet" />

</head>

<body class="body_bg">
<div class="container">

    <header class="topBar" id="header">
        <div class="wrap">
            <span class="back" onClick="history.go(-1)"></span>
            <h1>我的订单</h1>
        </div>
    </header>


    <div class="tab_box">
        <ul>
           <li <?php if (! $this->_var['status']): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index')); ?>">全部(<?php echo $this->_var['order_count_num']['order_count_all']; ?>)</a></li>
           <li <?php if ($this->_var['status'] == 'unpay'): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'unpay')); ?>">待付款(<?php echo $this->_var['order_count_num']['order_count_unpay']; ?>)</a></li>
           <li <?php if ($this->_var['status'] == 'payed'): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'payed')); ?>">待发货(<?php echo $this->_var['order_count_num']['order_count_payed']; ?>)</a></li>
           <li <?php if ($this->_var['status'] == 'shipped'): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'shipped')); ?>">待收货(<?php echo $this->_var['order_count_num']['order_count_shipped']; ?>)</a></li>
           <li <?php if ($this->_var['status'] == 'finished'): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'finished')); ?>">已完成(<?php echo $this->_var['order_count_num']['order_count_finished']; ?>)</a></li>
          <!--  <li <?php if ($this->_var['status'] == 'finished'): ?>class="now_hover" <?php else: ?> class="old_hover"<?php endif; ?> ><a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'finished')); ?>">已确认</a></li> -->
        </ul>
      </div>

      <?php echo $this->fetch('my_order_list_info.html'); ?>

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
