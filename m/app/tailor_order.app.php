<?php
/**
 *    买家的订单管理控制器
 */
class Tailor_orderApp extends MemberbaseApp{
    var $mod_order_invoice;
    var $mod_order;
	 function __construct()
    {
        $this->Tailor_orderApp();
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
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/user_center";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    function Tailor_orderApp()
    {
        parent::__construct();
		$this->mod_msg =& m('msg');
		$this->mod_msglog =& m('msglog');
		$this->_curitem('kuke');
		
		$this->mod_order_invoice =& m('orderinvoice');
		$this->mod_order  =& m('order');
    }

    //默认
    function index()
    {
        $user = $this->visitor->get();
        
       if (!$user['has_store'])
        {
           /*  show_message('您还不是裁缝，请返回！');  */
            header("Location:apply.html");


        }
        
        /* 获取订单列表 */
        $this->_get_orders();
        
        
        //ns add 获取左边菜单、头像信息
        /* 头像 add ns */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));
        
         
        $this->display('buyer_order_tailor.index.html');
    }

    function add()
    {
        echo 111;
    }
    /**
     *    获取订单列表
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_orders()
    {
        $cloth = array(
                3    => '上衣',
                2000 => '西裤',
                3000 => '衬衣',
                4000 => '马夹',
                6000 => '大衣',
        );
        $odTime = array(
                30  => '最近1个月',
                90  => '最近3个月',
                180 => '最近半年',
                999 => '半年以前',
        );

        $page = $this->_get_page(10);
        $model_order =& m('order');

        $con = array(
            array(
                'field' => 'status',
                'name'  => 'os',
                'equal' => '=',
            ),
            array(
                    'field' => 'cloth',
                    'name'  => 'ct',
                    'equal' => '=',
            ),
        );
        $conditions = $this->_get_query_conditions($con);
        if(trim($_GET['orderSearch']) != ''){
            $conditions .=  " AND ( order_sn = '{$_GET['orderSearch']}' OR kh_name = '{$_GET['orderSearch']}' OR fabric = '{$_GET['orderSearch']}' )";
        }
        
        /* 查找订单 */
        $orders = $model_order->findAll(array(
            'conditions'    => "store_id=" . $this->visitor->get('user_id') . "{$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
        ));

        $page['item_count'] = $model_order->getCount();
        $page['page_count'] = ceil($page['item_count']/$page['pageper']);
        $page['prev_page']  = ($page['curr_page'] >1) ? $page['curr_page'] - 1 : 1;
        $page['next_page']  = ($page['curr_page'] < $page['page_count']) ? $page['curr_page'] + 1 : $page['page_count'];
        $this->assign('page', $page);
        
        $this->assign('orders', $orders);
        $this->assign('odTime',$odTime);
        $this->assign('odSta',$odSta);
        $this->assign('cloth',$cloth);

    }
    /**
     *    查看订单详情
     *
     *    @author    Ruesin
     */
    function view(){

    	$args = $this->get_params();
    	$order_sn = !empty($args[1]) ? trim($args[1]) : 1;
    	
        $order_info = $this->mod_order->get(" order_sn = '{$order_sn}' AND store_id=" . $this->visitor->get('user_id'));
        
        if (!$order_info){
            $this->show_warning('no_such_order');
            return;
        }
        $oMem = &m('member');
        $mem = $oMem->get($order_info['user_id']);
        $order_info['nickname'] = $mem['nickname'];
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('order_detail'));

        $order['data']['figure'] = $this->_get_order_figure($order_info['order_id']);
        $order['data']['embs']   = $this->_get_order_embs($order_info['cloth'],$order_info['embs']);
        $order['data']['craft']  = $this->_get_order_craft($order_info['cloth'],$order_info['craft']);
        
        $this->assign('order', $order_info);
        $this->assign('data',$order['data']);


        //ns add 获取左边菜单、头像信息
        /* 头像 add ns */
        $user = $this->visitor->get();
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','user');
        $this->assign('user',$user);
        $this->assign('ac', 'tailor');
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_order');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));

        $this->display('buyer_order.view.html');
    }
    
    //获取订单量体数据
    function _get_order_figure($order_id){
        $figure_mod = m('orderfigure');
        $figure_info = $figure_mod->get(array(
                'conditions' => 'order_id='.$order_id,
        ));
        return $figure_info;
    }
    
    //获取订单刺绣信息
    function _get_order_embs($clothID = 0 ,$embs = ''){
        $arr = unserialize($embs);
        if(empty($arr))return ;
        foreach ($arr as $val){
            if(intval($val)){
                $dbin[] = $val;
            }else{
                $con['e_tname'] = 'emb_con';
                $con['e_name'] = $val;
            }
        }
        $mEmb = &m('mtmemb');
        $return = $mEmb->find(array(
                'conditions' => db_create_in($dbin,'e_id'),
        ));
        $return[-1] = $con;
        return $return;
    }
    
    //获取订单工艺信息
    function _get_order_craft($clothID = 0 ,$crafts = ''){
        $arr = unserialize($crafts);
        if(empty($arr))return ;
        foreach ($arr as $key=>$val){
            $pt[$key] = $key;
        }
        $mCraft = &m('mtmcraft');
        $mCraft_parent = &m('mtmcraftparent');
        $parent = $mCraft_parent->find(array(
                'conditions' => db_create_in($pt,'id'),
        ));
        $craft = $mCraft->find(array(
                'conditions' => db_create_in($arr,'code'),
        ));
        foreach ($parent as $row){
            $parent[$row['id']] = $row;
            unset($row);
        }
        foreach ($craft as &$row){
            $row['pname'] = $parent[$row['parentId']]['name'];
        }
        return $craft;
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
}

?>
