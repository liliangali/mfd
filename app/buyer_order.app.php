<?php
/**
 *    会员 - 我的订单
 */
class Buyer_orderApp extends MemberbaseApp{
    var $mod_order_invoice;
    var $mod_order;
    var $store_mod;
	 function __construct()
    {
        $this->Buyer_orderApp();
    }
    
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-11-17)
     * @author yhao.bai
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    }

    function Buyer_orderApp()
    {
        parent::__construct();
		$this->mod_msg =& m('msg');
		$this->mod_msglog =& m('msglog');
		
		$this->mod_order_invoice =& m('orderinvoice');
		$this->mod_order  =& m('order');
        $this->store_mod =& m('store');
        $this->order_goods_mod =& m('ordergoods');
        $this->order_figure_mod =& m('orderfigure');
    }
    /**
     *    订单列表首页
     *
     *    @author    Garbin
     *    @return    void
     *    ns add
     */
    function index()
    {
        $this->_get_orders($_GET['type']); //获取订单列表
        
        /* 当前用户中心菜单 */
        $this->_curitem('my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));
        
        /* 显示订单列表 */
        $this->display('order/buyer_order.index.html');
    }
    /**
     *    获取订单列表 ns up
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_orders($type = null)
    {
        $conditions = '';
        $page = $this->_get_page(4);
        //筛选条件
        $con = array();

        $_GET['type'] = is_null($type) ? 'all' :$type;

        if (is_numeric($type))
        {
            $con = array(
                array(      //按订单状态搜索
                    'field' => 'status',
                    'name'  => 'type',
                )
            );
        }
        $conditions = $this->_get_query_conditions($con);


        /* 查找订单 */
        $orders = $this->mod_order->find(array(
            'conditions'    => "user_id=" . $this->visitor->get('user_id'). "{$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
        ));
        $cids = array();
        $cls = array();
        foreach($orders as $val){
            $cls[$val["order_id"]] =$val;
            $cids[] = $val["order_id"];
        }

        //获取订单商品信息
        $customs  = $this->order_goods_mod->find(array(
            "conditions" => "order_id ".db_create_in($cids),
            //'fields'        => 'order_id,goods_id,goods_name,goods_image,subtotal,price',
        ));
        
        foreach ($customs as $item){
            if($item['suit_id']){
                $sIds[$item['suit_id']] = $item['suit_id'];
            }
        }
        $mSuit = &m('suitlist');
        $sData = $mSuit->find(db_create_in($sIds,"id"));
        foreach ($customs as $item){
            if($item['type'] == 'suit'){
                 
                if(!$carts[$item['dis_ident']]){
                    
                    $iTems = $item;
                    
                    $iTems['goods']       = $sData[$item['suit_id']];
                    $iTems['goods_id']    = $sData[$item['suit_id']]['id'];
                    $iTems['goods_name']  = $sData[$item['suit_id']]['suit_name'];
                    $iTems['price']       = $sData[$item['suit_id']]['price'];
                    $iTems['goods_image'] = $sData[$item['suit_id']]['image'];
                    
                    $iTems['type'] = $item['type'];
                    $iTems['ident'] = $item['dis_ident'];
                    
                    $iTems['quantity'] = $item['quantity'];
                    $iTems['subtotal'] = $item['subtotal'];
                     
                    $carts[$item['dis_ident']] = $iTems;
                    
                    unset($iTems);
                }else{
                     $carts[$item['dis_ident']]['quantity'] += $item['quantity'];
                     $carts[$item['dis_ident']]['subtotal'] += $item['subtotal'];
                }
                
            }else{
                $carts[$item['rec_id']] = $item;
            }
            
        }
        if($carts){
            foreach ($carts as $k=>$v){
                $cls[$v['order_id']]['goods'][$k] = $v;
                if(!intval($v['comment'])){ //还未评论过
                    $cls[$v['order_id']]['goods_comment'] = 'no';
                }
            }
        }

        
        
        $page['item_count'] = $this->mod_order->getCount();
        $this->_format_page($page);

        $this->assign('orders', $cls);


        if($type){
            $page['parameter'] = '?type='.$type;
        }

        $this->_format_page($page);
        $this->assign('page_info', $page);
    }
    
    /**
     *  更改需求状态
     * @access public
     * @version 1.0.0 (2014-12-23)
     * @author Xiao5
     */
    function upstatus(){
    	if (is_numeric($_POST['sn']))
    	{
    		$info = $this->mod_order->get( array('conditions'    => "order_sn='".$_POST['sn']."'"));
    		$conditions = '';
    		if ($info['status'] == STORE_SHIPPED)
    		{
    			//确认收货
    			$cs = &m('order');
    			
    			/* Ruesin接口 */
    			$rs = $cs ->_order_finish($info['order_sn']);
    			if ($rs)
    			{
    				$this->json_result(array('rs'=>$rs));
    				return;
    			}else {
    				$this->json_error('确认失败!');
    				return;
    			}
    		}else{
    			$this->json_error('更新失败!');
    			return;
    		}
    	}
    	$this->json_error('订单信息有误!');
    	return;
    }
    
    /**
     *    查看订单详情
     *
     *    @author    Garbin
     *    @return    void
     */
    function view()
    {
    	$args = $this->get_params();

    	$order_id = empty($args[0]) ? 0 : intval($args[0]);
        $order_info = $this->mod_order->get(array(
            'fields'        => "*, order.add_time as order_add_time",
            'conditions'    => "order_id={$order_id} AND user_id=" . $this->visitor->get('user_id'),
            'join'          => 'belongs_to_store',
            ));
        
        if (!$order_info)
        {
            $this->show_warning('no_such_order');

            return;
        }
        //获取量体信息
        $order_figure_info = $this->order_figure_mod->get(array(
            'conditions' => "order_id={$order_id} AND userid=" . $this->visitor->get('user_id'),
            ));
        //特殊量体信息
        $mbt_mod =& m('mtmbodytype'); 
        //肚
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_24']));
        //臀
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_26']));
        //手臂
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_25']));
        //左肩膀
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_19']));
        //右肩膀
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_20']));

        //风格上衣
        $order_figure_info['t'][] = $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_3']));
        //风格西裤
        $order_figure_info['t'][]= $mbt_mod->get(array('conditions' => 'id='.$order_figure_info['body_type_2000']));

        $this->assign('order_figure_info', $order_figure_info);
        
        
        
        //获取商品信息
        $goods  = $this->order_goods_mod->find(array(
                "conditions" => "order_id =".$order_id,
                //'fields'        => 'order_id,goods_id,goods_name,goods_image,subtotal,price',
        ));
        foreach ($goods as $item){
            if($item['suit_id']){
                $sIds[$item['suit_id']] = $item['suit_id'];
            }
        }
        $mSuit = &m('suitlist');
        $sData = $mSuit->find(db_create_in($sIds,"id"));
        foreach ($goods as $item){
            if($item['type'] == 'suit'){
                 
                if(!$carts[$item['dis_ident']]){
                    $iTems = $item;
        
                    $iTems['goods']       = $sData[$item['suit_id']];
                    $iTems['goods_id']    = $sData[$item['suit_id']]['id'];
                    $iTems['goods_name']  = $sData[$item['suit_id']]['suit_name'];
                    $iTems['price']       = $sData[$item['suit_id']]['price'];
                    $iTems['goods_image'] = $sData[$item['suit_id']]['image'];
        
                    $iTems['type'] = $item['type'];
                    $iTems['ident'] = $item['dis_ident'];
        
                    $iTems['quantity'] = $item['quantity'];
                    $iTems['subtotal'] = $item['subtotal'];
                     
                    $carts[$item['dis_ident']] = $iTems;
        
                    unset($iTems);
                }else{
                    $carts[$item['dis_ident']]['quantity'] += $item['quantity'];
                    $carts[$item['dis_ident']]['subtotal'] += $item['subtotal'];
                }
        
            }else{
                $carts[$item['rec_id']] = $item;
            }
        
        }
        
        $order_info['goods']  = $carts;
        
        //获取商品信息
        /* $order_info['goods']  = $this->order_goods_mod->find(array(
            "conditions" => "order_id =".$order_id,
            'fields'        => 'order_id,goods_id,goods_name,goods_image,subtotal,price',
        )); */
        
        $mMf = &m('member_figure');
        $this->assign('figures',$mMf->_positions());
        
        if($order_info['shipping_id'] == 2){
            $msv = &m('serve');
            $store = $msv->get($order_info['ship_store']);
            $this->assign('store',$store);
        }        
        
        /* 当前用户中心菜单 */
        $this->_curitem('my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));

        $this->assign('order', $order_info);
        $this->display('order/buyer_order.view.html');
    }

    
    function pay(){
    	$args = $this->get_params();
    	$order_id = isset($args["0"]) ? intval($args["0"]) : 0;

    	if (!$order_id)
    	{
    		$this->show_warning('no_such_order');
    	
    		return;
    	}
    	
 		$model_order =& m('order');
        //$order_info  = $model_order->get("order_id={$order_id} AND user_id=" . $this->visitor->get('user_id'));
        $order_info = $model_order->get(array(
            'fields'        => "*, order.add_time as order_add_time",
            'conditions'    => "order_id={$order_id} AND user_id=" . $this->visitor->get('user_id'),
            'join'          => 'belongs_to_store',
            ));
    	
    	if(!$order_info) return;
    	 
    	if($order_info['status'] != ORDER_PENDING || !$order_info['payment_id']){
    	
    		$this->show_warning('no_such_order');
    	
    		return;
    	}
    	$this->assign('order_info', $order_info);
    	
    	$this->import_resource(array(
    			'script' => 'easydialog/easydialog.min.js',
    			'style'  => "easydialog/easydialog.css"
    	));
    	
    	$this->display('buyer_order.pay.html');
    }
    /**
     * 申请发票
     * @author Ruesin
     */
    function invoice(){
        $this->assign('title', 'RCTAILOR-酷客中心-我的订单');
        $this->assign('title_info', '申请发票');
        $url=$this->_view->build_url(array('app'=>'buyer_order'));
        $args = $this->get_params();
        $order_id = empty($args[0]) ? 0 : intval($args[0]);
        if ($order_id == 0){
            $this->show_warning('no_such_order');
            return;
        }
        
        
        $info = $this->mod_order_invoice->get("order_id = '{$order_id}'");
        if(!empty($info)){
            
            $vc_pic = explode(',',$info['vc_pic']);
            
            $info['vc_pic'] = array_filter($vc_pic);
            
            $this->assign('info',$info);
            $this->display('buyer_order/invoice_info.html');
            return;
        }
        
        $order = $this->mod_order->get(array(
        	'conditions' => "order_id = '{$order_id}'",
        ));

        //if($order['status'] == ORDER_SUBMITTED || $order['status'] == ORDER_PENDING || $order['status'] == ORDER_ACCEPTED )
        if($order['status'] == ORDER_SHIPPED || $order['status'] == ORDER_FINISHED || $order['status'] == ORDER_CANCELED ){
            $this->show_message('无法提交发票信息!','返回', $url);
        }

        if(!IS_POST){
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.ad.js',
            ));
            $this->assign('order_id',$order_id);
            $this->assign('site_url',site_url());
            $this->display('buyer_order/invoice.html');
        }else{
            if(intval($_POST['id']) != $order_id){
                $this->show_warning('参数错误!');
                return;
            }
            
            
            $data = array(
            	//'vc_id'        => $vc_id,
                'vc_name'      => trim($_POST['name']),
                'order_id'     => $order_id,
                'vc_sn'        => trim($_POST['sn']),
                'vc_pic'       => $_POST['img'],
                'vc_addr'      => trim($_POST['addr']),
                'vc_phone'     => trim($_POST['telphone']),
                'vc_bank'      => trim($_POST['bank']),
                'vc_bank_num'  => trim($_POST['num']),
                'add_time'     => time(),
            );
            foreach ($data as $row){
            	if($row == '' || empty($row)){
            	    $this->show_warning('提交失败，所有选项为必填！');
            	    return;
            	}
            }
            
            if(count($data['vc_pic'])>5){
                $this->show_warning('提交失败，证件图片最多5张！');
                return;
            }
            foreach ($data['vc_pic'] as $value){
                $vc_pic .= $value.',';
            }
            $data['vc_pic'] = $vc_pic;
            
            $vc_id = $this->mod_order_invoice->add($data);
            if($vc_id){
                
                $url=$this->_view->build_url(array('app'=>'buyer_order'));
                $this->show_message('提交成功!','返回', $url);
                
            }else{
                $this->show_warning('提交失败!');
                return;
            }

        }
        
    }
    /**
     * Ajax上传证件
     * @author Ruesin
     */
    function ajax_upload(){
        
        $input_name = 'up_file';
        
        $dir = $_SERVER['DOCUMENT_ROOT'].'/upload/invoice/';

		//if(!is_dir($dir))mkDirs($dir);
        
        $fileName =  md5( uniqid() . mt_rand(0,255) ) . ".jpg";
        
        $fileDirName1 = $dir . $fileName;
        
        $rs = move_uploaded_file($_FILES[$input_name]["tmp_name"],$fileDirName1);
        
        $src=  "/upload/invoice/".$fileName;

        echo json_encode($src);
    }
    /**
     *    取消订单
     *
     *    @author    Garbin
     *    @return    void
     */
    function cancel_order()
    {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        $model_order    =&  m('order');
        /* 只有待付款的订单可以取消 */
        $order_info     = $model_order->get("order_id={$order_id} AND user_id=" . $this->visitor->get('user_id') . " AND status " . db_create_in(array(ORDER_PENDING, ORDER_SUBMITTED)));
        if (empty($order_info))
        {
            echo Lang::get('no_such_order');

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('buyer_order.cancel.html');
        }
        else
        {
            $model_order->edit($order_id, array('status' => ORDER_CANCELED));
            if ($model_order->has_error())
            {
                $this->pop_warning($model_order->get_error());

                return;
            }

            /* 加回商品库存 */
            $model_order->change_stock('+', $order_id);
            $cancel_reason = (!empty($_POST['remark'])) ? $_POST['remark'] : $_POST['cancel_reason'];
            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_CANCELED),
                'remark'    => $cancel_reason,
                'log_time'  => gmtime(),
            ));

            /* 发送给卖家订单取消通知 */
            $model_member =& m('member');
            $seller_info   = $model_member->get($order_info['seller_id']);
            $mail = get_mail('toseller_cancel_order_notify', array('order' => $order_info, 'reason' => $_POST['remark']));
            $this->_mailto($seller_info['email'], addslashes($mail['subject']), addslashes($mail['message']));

            $new_data = array(
                'status'    => Lang::get('order_canceled'),
                'actions'   => array(), //取消订单后就不能做任何操作了
            );

            $this->pop_warning('ok');
        }

    }
	
    /**
     *    取消订单
     *
     *    @author   liang.li
     *    @return    bool
     */
    function cancel_order_ajax()
    {
    	$order_id = isset($_POST['orderId']) ? intval($_POST['orderId']) : 0;
    	if (!$order_id)
    	{
    		return 1;
    		exit;
    	}
    	$model_order    =&  m('order');
    	/* 只有待付款的订单可以取消 */
    	$order_info     = $model_order->get("order_id={$order_id} AND user_id=" . $this->visitor->get('user_id') . " AND status " . db_create_in(array(ORDER_PENDING)));
    	if (empty($order_info))
    	{
    		return 2;
    		exit;
    	}
    	
    	$model_order->edit($order_id, array('status' => ORDER_CANCELED));
    	if ($model_order->has_error())
    	{
    		return 3;
    		exit;
    	}
    
    	/* 记录订单操作日志 */
    	$order_log =& m('orderlog');
    	$order_log->add(array(
    			'order_id'  => $order_id,
    			'operator'  => addslashes($this->visitor->get('user_name')),
    			'order_status' => order_status($order_info['status']),
    			'changed_status' => order_status(ORDER_CANCELED),
    			'remark'    => '',
    			'log_time'  => gmtime(),
    	));
    
    	return 4;
    }
    
    
    /**
     *    确认订单
     *
     *    @author    Garbin
     *    @return    void
     */
    function confirm_order()
    {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        $model_order    =&  m('order');
        /* 只有已发货的订单可以确认 */
        $order_info     = $model_order->get("order_id={$order_id} AND user_id=" . $this->visitor->get('user_id') . " AND status=" . ORDER_SHIPPED);
        if (empty($order_info))
        {
            echo Lang::get('no_such_order');

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('buyer_order.confirm.html');
        }
        else
        {
            $model_order->edit($order_id, array('status' => ORDER_FINISHED, 'finished_time' => gmtime()));
            if ($model_order->has_error())
            {
                $this->pop_warning($model_order->get_error());

                return;
            }

            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_FINISHED),
                'remark'    => Lang::get('buyer_confirm'),
                'log_time'  => gmtime(),
            ));

            /* 发送给卖家买家确认收货邮件，交易完成 */
            $model_member =& m('member');
            $seller_info   = $model_member->get($order_info['seller_id']);
            $mail = get_mail('toseller_finish_notify', array('order' => $order_info));
            $this->_mailto($seller_info['email'], addslashes($mail['subject']), addslashes($mail['message']));

            $new_data = array(
                'status'    => Lang::get('order_finished'),
                'actions'   => array('evaluate'),
            );

            /* 更新累计销售件数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $model_ordergoods =& m('ordergoods');
            $order_goods = $model_ordergoods->find("order_id={$order_id}");
            foreach ($order_goods as $goods)
            {
                $model_goodsstatistics->edit($goods['goods_id'], "sales=sales+{$goods['quantity']}");
            }

           //发送短信给买家 by v5 team 28565557
			$user_id = $order_info['seller_id'];
			$user_name = $order_info['seller_name'];
			$row_msg = $this->mod_msg->getrow("select * from ".DB_PREFIX."msg where user_id=".$user_id);
			$mobile = $row_msg['mobile']; //手机号
			$smsText = "您的订单：".$order_info['order_sn']."，买家".$order_info['buyer_name']."已经确定！";//内容
			$time = time();
			
			$checked_functions = $functions = array();
            $functions = $this->_get_functions();
            $tmp = explode(',', $row_msg['functions']);
            if ($functions)
            {
                foreach ($functions as $func)
                {
                    $checked_functions[$func] = in_array($func, $tmp);
                }
            }
			
			if($row_msg['state']==0)
			{
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			if($checked_functions['check'] != 1)
			{
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			if($row_msg['num']<=0)
			{
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			if($mobile == '')
			{
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			if($smsText == '')
			{
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			
			//九天接口
			$res = $this->SendSms($mobile,$smsText,1);
			$num = $row_msg['num']-1;
			$edit_msg = array(
					'num' => $num,
			);
			$this->mod_msg->edit('user_id='.$user_id,$edit_msg);
			$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);
			return;
			//中国网建接口
			/*$url='http://utf8.sms.webchinese.cn/?Uid='.SMS_UID.'&Key='.SMS_KEY.'&smsMob='.$mobile.'&smsText='.$smsText; 
			$res = $this->Sms_Get($url);
			if($res == '')
			{
            	$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}
			else if($res>0)
			{
				$num = $row_msg['num']-1;
				$edit_msg = array(
					'num' => $num,
				);
				$add_msglog = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'to_mobile' => $mobile,
					'content' => $smsText,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->mod_msg->edit('user_id='.$user_id,$edit_msg);
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);
				return;
			}
			else
			{
				$add_msglog = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'to_mobile' => $mobile,
					'content' => $content,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->pop_warning('ok','','index.php?app=buyer_order&act=evaluate&order_id='.$order_id);;
				return;
			}*/
        }

    }

    /**
     *    给卖家评价
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function evaluate()
    {
    	$args = $this->get_params();

    	$order_id = empty($args[0]) ? 0 : intval($args[0]);
    	$page_now  = empty($args[1]) ? 1 : intval($args[1]);
    	$this->assign('page_now',$page_now);
        //$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if (!$order_id)
        {
            $this->show_warning('no_such_order');

            return;
        }

        /* 验证订单有效性 */
        $model_order =& m('order');
        $order_info  = $model_order->get("order_id={$order_id} AND user_id=" . $this->visitor->get('user_id'));
        if (!$order_info)
        {
            $this->show_warning('no_such_order');

            return;
        }
        if ($order_info['status'] != ORDER_ACCEPTED )
        {
            /* 不是已完成的订单，无法评价 */
            $this->show_warning('商品尚未付款 不能评价');

            return;
        }
        if ($order_info['evaluation_status'] != 0)
        {
            /* 已评价的订单 */
            $this->show_warning('already_evaluate');

            return;
        }
        $model_ordergoods =& m('ordergoods');

        if (!IS_POST)
        {
            /* 显示评价表单 */
            /* 获取订单商品 */
            $goods_list = $model_ordergoods->find("order_id={$order_id}");
            foreach ($goods_list as $key => $goods)
            {
                empty($goods['goods_image']) && $goods_list[$key]['goods_image'] = Conf::get('default_goods_image');
            }
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=member',
                             LANG::get('my_order'), 'index.php?app=buyer_order',
                             LANG::get('evaluate'));
            $this->assign('goods_list', $goods_list);
            $this->assign('order', $order_info);

            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('credit_evaluate'));
            $this->display('buyer_order.evaluate.html');
        }
        else
        {
            $evaluations = array();
            /* 写入评价 */
            foreach ($_POST['evaluations'] as $rec_id => $evaluation)
            {
                if ($evaluation['evaluation'] <= 0 || $evaluation['evaluation'] > 3)
                {
                    $this->show_warning('evaluation_error');

                    return;
                }
                switch ($evaluation['evaluation'])
                {
                    case 3:
                        $credit_value = 1;
                    break;
                    case 1:
                        $credit_value = -1;
                    break;
                    default:
                        $credit_value = 0;
                    break;
                }
                $evaluations[intval($rec_id)] = array(
                    'evaluation'    => $evaluation['evaluation'],
                    'comment'       => $evaluation['comment'],
                    'credit_value'  => $credit_value
                );
            }
            $goods_list = $model_ordergoods->find("order_id={$order_id}");
            foreach ($evaluations as $rec_id => $evaluation)
            {
                $model_ordergoods->edit("rec_id={$rec_id} AND order_id={$order_id}", $evaluation);
                //var_dump($goods_list[$rec_id]['goods_id']);exit;
                
                /*
                $comments_model=m('comments');
                	小白评论表
                $commentres=$comments_model->get('rec_id='.$goods_list[$rec_id]['rec_id']);
                if(!$commentres['rec_id'])
                {
	               	$comment_arr=array('user_id'   => $this->visitor->get('user_id'),
	                	'content'   => $evaluation['comment'],
	                	'add_time'    =>gmtime(),
	                	'retop'=>0,
	                	'parent_id'=>0,
	                	'user_name'=>$this->visitor->get('user_name'),
	                	'goods_id'=>$goods_list[$rec_id]['goods_id'],
	                	'recount'=>0,
	                	'goods_name'=>$goods_list[$rec_id]['goods_name'],
	                	'status'=>0,
	                	'rec_id'=>$goods_list[$rec_id]['rec_id'],
	               		'anonymous'=>0,
	               		'rank'=>$evaluation['credit_value'],
	                );
	                $comments_model->add($comment_arr);
	                //var_dump($commentres);exit;
                }
                */
                setComment($this->visitor->get('user_id'),0 ,$goods_list[$rec_id]['rec_id'], 'goods_comment', $evaluation['comment'],$goods_list[$rec_id]['goods_id']);
                
                
                setPoint($this->visitor->get('user_id'), pointTurnNum('goods_comment'), 'add', 'goods_comment');
                
                //exit;
                
                
                
                
                $goods_url = SITE_URL . '/' . url('app=goods&id=' . $goods_list[$rec_id]['goods_id']);
                $goods_name = $goods_list[$rec_id]['goods_name'];
                $this->send_feed('goods_evaluated', array(
                    'user_id'   => $this->visitor->get('user_id'),
                    'user_name'   => $this->visitor->get('user_name'),
                    'goods_url'   => $goods_url,
                    'goods_name'   => $goods_name,
                    'evaluation'   => Lang::get('order_eval.' . $evaluation['evaluation']),
                    'comment'   => $evaluation['comment'],
                    'images'    => array(
                        array(
                            'url' => SITE_URL . '/' . $goods_list[$rec_id]['goods_image'],
                            'link' => $goods_url,
                        ),
                    ),
                ));
            }

            /* 更新订单评价状态 */
            $model_order->edit($order_id, array(
                'evaluation_status' => 1,
                'evaluation_time'   => gmtime()
            ));

            /* 更新卖家信用度及好评率 */
            $model_store =& m('store');
            $model_store->edit($order_info['seller_id'], array(
                'credit_value'  =>  $model_store->recount_credit_value($order_info['seller_id']),
                'praise_rate'   =>  $model_store->recount_praise_rate($order_info['seller_id'])
            ));

            /* 更新商品评价数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $goods_ids = array();
            foreach ($goods_list as $goods)
            {
                $goods_ids[] = $goods['goods_id'];
            }
            $model_goodsstatistics->edit($goods_ids, 'comments=comments+1');
			
            $this->_init_view();
            $url=$this->_view->build_url(array('app'=>'buyer_order'));
			
            $this->show_message('evaluate_successed',
                'back_list', $url);
        }
    }

    

    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'order_list',
                'url'   => 'index.php?app=buyer_order',
            ),
        );
        return $menus;
    }
    
    /**
     * 查看量体数据详情
     */
    function get_figure()
    {
    	$args = $this->get_params();
    	$figure = $args[0];
    	$figure_mod = m('orderfigure');
    	/* 是否存在 */
    	$figure = $figure_mod->get_info($figure);
    	if (!$figure)
    	{
    		$this->show_warning('user_empty');
    		return;
    	}
    	
    	/* if ($figure['figure_mod'] == 1)
    	{
    		$figure['figure_mod'] = '上门';
    	}
    	else 
    	{
    		$figure['figure_mod'] = '到店';
    	} */
    	
    	$this->assign('figure', $figure);
    	$this->display('figure.info.html');	
    }
    
    /**
     * 验证量体数据是否补录
     * @author liang.li
     */
    function _valid_figure_info($figure){
    
    		if(!$figure['figure_name']){
    			$this->_error("量体名称不能为空");
    			return false;
    		}
    			
    		if(!$figure['lw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['xw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['zyw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['tw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['stw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['zjk']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
   
    		if(!$figure['yxc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['zxc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['qjk']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['hyc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['yw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['td']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		/*
    		if(!$figure['hyg']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		*/
    		/*
    		if(!$figure['qyg']){
    			$this->_error("量体数量不合法");
    			return false;
    		}*/
    		/*
    		if(!$figure['kk']){
    			$this->_error("量体数量不合法");
    			return false;
    		}*/
    		if(!$figure['hyjc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['qyj']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['tgw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['zkc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['ykc']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		/*
    		if(!$figure['xiw']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		*/
    		if(!$figure['body_type_19']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['body_type_20']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['body_type_24']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['body_type_25']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		if(!$figure['body_type_26']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			
    		if(!$figure['body_type_3']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			
    		if(!$figure['body_type_2000']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			
    		if(!$figure['styleLength']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			
    		if(!$figure['part_label_10130']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			
    		if(!$figure['part_label_10131']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		/*
    		if(!$figure['part_label_10725']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    			*/
    		/*
    		if(!$figure['part_label_10726']){
    			$this->_error("量体数量不合法");
    			return false;
    		}
    		*/
    		return true;
    }
    
    function commit(){
        
        $oi = intval($_POST['oi']);
        
        $condition = "user_id='{$this->visitor->get('user_id')}' AND order_id = '{$oi}'";
        
        $oinfo = $this->mod_order->get(array('conditions' =>$condition));
        
        if(empty($oinfo) || $oinfo['status'] != ORDER_FINISHED){
            $this->json_error('非法操作');
            die();
        }
        
        $orderComment_mod = &m("ordercomment");
        
        $comment = $orderComment_mod->get(array(
            'conditions' => "member_id='{$this->visitor->get('user_id')}' AND order_id = '{$oi}'",
        ));
        
        if($comment){
            $this->json_error("已经评论过了！！");
            die();
        }
        
        $status = CONF::get('check_comment');
        
        $data = array(
            'approve'            => isset($_POST['star']) ? intval($_POST['star']) : 5,
            'member_id'			 => $this->visitor->get("user_id"),
            'order_id'           => $oinfo['order_id'],
            'tailor_id'          => $oinfo['store_id'],
    		'content'            => htmlspecialchars($_POST['content']),
    	    'addtime'            => gmtime(),
            'status'             => $status == 1 ? 0 : 1,
            'nickname'           => $this->visitor->get("nickname"),
            'imgs'               => implode(",", (array)$_POST['img']),
        );
        
        if(empty($data['content'])){
            $this->json_error("评论的内容不能为空");
            die();
        }
        
        if(!$this->visitor->has_login){
            $this->json_result("login");
            die();
        }
        
        $res = $orderComment_mod->add($data);
        if($res){
            $this->mod_order->edit($oi,array('comment'=>1));  //未做事务
            
            $sendData = sendMessage(array(
				'type' => 3,
				'location_url' => "tailor-info-{$oinfo['store_id']}.html",
				'to_user_id'    => $oinfo['store_id'],
            ));
			//订单评论发送信鸽
			sendXingeApp($sendData['addtime'], $sendData['type']);
        }
        $this->json_result();
        die();
    }
    
    
    function upload(){
        $orderID = intval($_POST['oi']);
        
        $condition = "user_id='{$this->visitor->get('user_id')}' AND order_id = '{$orderID}'";
        
        $oinfo = $this->mod_order->get(array('conditions' =>$condition));
        
        if(empty($oinfo) || $oinfo['status'] != ORDER_FINISHED){
            $this->json_error('非法操作');
            die();
        }
        
        $dir = "/upload_user_photo/order/{$this->visitor->get('user_id')}_{$orderID}";
    
        $count = $this->countFile($this->visitor->get('user_id')."_".$orderID);

        if($count >= 4){
            $this->json_error('最多只能上传4张图片');
            die();
        }
        import('uploader.lib'); // 导入上传类
        import('image.func');
        $uploader = new Uploader();

        $uploader->allowed_type(IMAGE_FILE_TYPE); // 限制文件类型
        $uploader->allowed_size(2048000); // 限制单个文件大小2M
        $uploader->addFile($_FILES['up_file']);
        if (!$uploader->file_info())
        {
            $this->json_error($uploader->get_error());
            exit();
        }

        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);
        $filename  = $uploader->random_filename();
        /* 上传 */
        $file_path = $uploader->save($dir, $filename);   // 保存到指定目录
        if (!$file_path)
        {
            $this->json_error('file_save_error');
            exit();
        }
    
        $src= $file_path;
        
        $arr = array('src'=>$src,'file'=>$filename.".".$uploader->_file['extension']);
        
        $this->json_result($arr);
        die();
    }
    
    function dropImg(){
        $oi = intval($_GET['oi']);
        $fn = trim($_GET['fn']);
        
        $file = ROOT_PATH."/upload_user_photo/order/{$this->visitor->get('user_id')}_{$oi}/$fn";
        
       if(is_file($file)){
           @unlink($file);
           $this->json_result(); 
           die();
       }else{
           $this->json_error("删除失败");
           die();
       }
    }
    
    function loadForm(){
        $oi = intval($_GET['oi']);
        
        $condition = "user_id='{$this->visitor->get('user_id')}' AND order_id = '{$oi}'";
        
        $oinfo = $this->mod_order->get(array('conditions' =>$condition));
        
        if(empty($oinfo) || $oinfo['status'] != ORDER_FINISHED){
            $this->json_error('非法操作');
            die();
        }
        
        $this->clearFile($this->visitor->get('user_id').'_'.$oi);
        
        $this->json_result();
        die();
    }
    
    function clearFile($dir){
        $path = ROOT_PATH."/upload_user_photo/order/{$dir}";
        if(is_dir($path)){
            $hander = opendir($path);
            if($hander){
                while($file = readdir($hander)){
                    if(is_file($path."/".$file)){
                        unlink($path."/".$file);
                    }
                }
            }
        }
    }
    
    function countFile($dir){
        $count = 0;
        $path = ROOT_PATH."/upload_user_photo/order/{$dir}";
        
        if(is_dir($path)){
            $hander = opendir($path);
            if($hander){
                while($file = readdir($hander)){
                    if(is_file($path."/".$file) && $file != "index.htm"){
                        $count += 1;
                    }
                }
            }
        }
        
        return $count;
    }


    /**
     *
     *  订单评论列表
     *  @author yusw
     */
    function order_comments_list(){
        $order_id =intval($_REQUEST['id']); //591

        if(!$order_id){
            $this->show_warning('参数错误');
            return;
        }

        /* 当前用户中心菜单 */
        $this->_curitem('my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));


        $list = array();
        $this->_order_mod = &m("order");
        $this->_order_goods_mod = m('ordergoods');

        $order_info = $this->_order_mod->get("order_id=$order_id");  //add_time
        $order_goods_info =$this->_order_goods_mod->find(array(
            'conditions' => "order_id=$order_id",
        ));//goods_name

        $list['addtime'] = $order_info['add_time'];
        $list['order_id'] = $order_id;
        $list['sn'] = $order_info['order_sn'];

        $suit = $s_ids = array();
        foreach($order_goods_info as $k=>$v){
            //没评论过的
            if($v['comment']==0){
                //套装
                if($v['type']=='suit'){
                    $suit[$k] = $v['suit_id'];
                    $s_ids[$v['suit_id']][]=$k;
                }

                //商品
                if($v['type']=='custom'){
                    $list['goods'][$k]['name'] = $v['goods_name'];
                    $list['goods'][$k]['type'] = $v['type'];
                    $list['goods'][$k]['id']   = $v['goods_id']; //被评论id
                    $list['goods'][$k]['price'] = $v['price'];

                }
            }
        }

        //去除套装重复项
        $suit = array_unique($suit);
        foreach($suit as $s_k=>$s_id){
            $this->_suit_mod      = & m('suitlist');
            $suit_info = $this->_suit_mod->get("id=$s_id");
            $list['goods'][$s_k]['name'] = $suit_info['suit_name'];
            $list['goods'][$s_k]['price'] = $suit_info['price'];
            $list['goods'][$s_k]['type'] = 'suit';
            $list['goods'][$s_k]['id'] = $s_id;//被评论id
            $list['goods'][$s_k]['suit_ids'] = empty($s_ids[$s_id])?'':serialize($s_ids[$s_id]);//被评论套装商品id 
        }

//        echo '<pre>';var_dump($list);die;

        $this->assign('list',$list);
        $this->display('order/detail_comment.index.html');
    }


    /**
     *  订单商品评论状态
     *  @return  1-全部评论过  0-还有需要评论的数据
     *  @author  yusw
     */


    function comment_sign($order_id){
        //商品
        $c_sign =$this->_order_goods_mod->find(array(
            'conditions' => "order_id=$order_id and comment=0",
        ));
        return empty($c_sign)?1:0;
    }






}

?>
