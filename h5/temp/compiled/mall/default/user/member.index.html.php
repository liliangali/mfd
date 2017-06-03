<!DOCTYPE html>
<html lang="zh_cn">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<?php echo $this->_var['page_seo']; ?>

<link href="public/static/wap/css/public.css" rel="stylesheet" />
<link href="public/static/wap/css/slx_style.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="public/static/wap/css/footer.css" />
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery-1.8.3.min.js'; ?>"></script>
</head>
<body style="background:#f0f2f5;">
<div class="main">

 <div class="yfzx_top">

     <p class="p1">
     <?php if ($this->_var['avatar']): ?>
     <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'person')); ?>"><img src="<?php echo $this->_var['avatar']; ?>" width="66" height="66"></a>
     <?php else: ?>
     <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'person')); ?>">
         <img src="public/static/wap/images/yfzxtx.png"></a>
     <?php endif; ?>
     </p>
     <p class="p2"><?php echo $this->_var['user']['nickname']; ?>
         <!--<img src="public/static/wap/images/vip.png">-->
     </p>
 </div>



    <div class="yfzx_list">
        <ul style="border-color:#e9e9e9;">
            <li class="li_1">
                <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'all')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">全部订单</p>

                        <p class="p3"><i></i></p>
                    </div>
                </a>
            </li>
        </ul>
    </div>

    <div class="order_state c_db">
        <p class="p1">
          <?php if ($this->_var['order_count_num']['order_count_unpay']): ?>  <span><?php echo $this->_var['order_count_num']['order_count_unpay']; ?></span><?php endif; ?>
          
            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'unpay')); ?>"> <i></i>待付款</a>
        </p>

        <p class="p2">
            <?php if ($this->_var['order_count_num']['order_count_shipped']): ?> <span><?php echo $this->_var['order_count_num']['order_count_shipped']; ?></span><?php endif; ?>
            
            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'index','arg0'=>'1','arg1'=>'shipped')); ?>"><i></i>待收货</a>
        </p>

        <p class="p3">
            <?php if ($this->_var['order_count_num']['order_count_assess']): ?> <span><?php echo $this->_var['order_count_num']['order_count_assess']; ?></span><?php endif; ?>
            
            <a href="<?php echo $this->build_url(array('app'=>'my_order','act'=>'ordergoods')); ?>"><i></i>待评价</a>
        </p>

        <p class="p4">
            <a href="<?php echo $this->build_url(array('app'=>'comment','act'=>'index')); ?>"><i></i>已评价</a>
        </p>
    </div>

    <div class="yfzx_list">
        <ul>

            <li class="li_2">
                <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'quan','arg0'=>'1')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">我的麦券</p>
                    <?php if ($this->_var['debit_num'] > 0): ?>
                        <p class="p3"><span><?php echo $this->_var['debit_num']; ?></span><i></i></p>
                      <?php endif; ?>
                    </div>
                </a>
            </li>

            <li class="li_3">
                <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'jihuo','arg0'=>'1')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">激活麦券</p>

                        <p class="p3"><i></i></p>
                    </div>
                </a>
            </li>

            <li class="li_4">
                <a href="<?php echo $this->build_url(array('app'=>'my_favorite')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">我的收藏</p>
                        <?php if ($this->_var['collent_num'] > 0): ?>
                        <p class="p3"><span><?php echo $this->_var['collent_num']; ?></span><i></i></p>
                        <?php endif; ?>
                    </div>
                </a>
            </li>

            <li class="li_5">
                <a href="<?php echo $this->build_url(array('app'=>'member','act'=>'person')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">个人信息</p>

                        <p class="p3"><i></i></p>
                    </div>
                </a>
            </li>

        </ul>
    </div>


    <div class="yfzx_list" style="padding-bottom:15px;">
        <ul>
            <li class="li_6">
                <a href="<?php echo $this->build_url(array('app'=>'about')); ?>">
                    <div class="c_db c_bac">
                        <p class="p1"></p>

                        <p class="p2 c_bf1">关于麦富迪</p>

                        <p class="p3"><i></i></p>
                    </div>
                </a>
            </li>
        </ul>
    </div>


</div>

<?php echo $this->fetch('../footer_footer.html'); ?>
<?php echo $this->fetch('../fudong1.html'); ?>

</body>
</html>
