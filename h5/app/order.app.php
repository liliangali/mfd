<?php
/**
 * 订单操作类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: order.app.php 14055 2016-01-30 02:28:57Z tangsj $
 * @copyright Copyright 2014 redcollar
 */
use Cyteam\Shop\Cart\Carts;
class OrderApp extends ShoppingbaseApp
{
	var $_mod_order;
	var $_mod_order_figure;
	public $_mod_mtm_bt;
	public $_mod_mtm_pt;
	public $_mod_craft;
	public $_mod_craft_parent;
	public $_mod_fabric;
	public $_mod_emb;
	public $_mod_part;
	public $_mod_pay;
	public $_mod_member;
	public $_mod_order_model;
	public $_cloth;
	public $_embs;
	public $_mod_member_figure;
	public $_store;
	public $_mod_bills;
	function __construct()
    {
    	parent::__construct();
    	$this->_mod_order = &m("order");
    	$this->_mod_order_figure = &m('orderfigure');
    	$this->_mod_mtm_bt = &m("mtmbodytype");
		$this->_mod_mtm_pt = &m("mtmpositions");
    	$this->_mod_craft = &m('mtmcraft');
    	$this->_mod_craft_parent = &m('mtmcraftparent');
    	$this->_mod_fabric = &m('mtmfabric');
    	$this->_mod_emb = &m('mtmemb');
    	$this->_mod_part = &m('part');
    	$this->_mod_pay = &m("payment");
    	$this->_mod_member= &m('member');
    	$this->_mod_order_model=&m('order');
    	$this->_mod_member_figure = &m('member_figure');
    	$this->_mod_bills = &m("paymentbills");
    	$this->_cloth =array(
    	        3    => '上衣',
    	        2000 => '西裤',
    	        3000 => '衬衣',
    	        4000 => '马夹',
    	        6000 => '大衣',
    	);
    	$this->_embs = array(
    	        'emb_site' => '位置',
    	        'emb_font' => '字体',
    	        'emb_color' => '颜色',
    	);

    	$this->_store = $this->visitor->get();
    }

	/**
     * wap添加订单页面（可统一走addApp函数处理）
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function add()
	{
		
        $this->addApp();
    }
	
	/**
     * app调用添加订单页面
	 *
     * @param string store_id 裁缝ID; string $token 裁缝token
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function addApp()
	{
	//print_r(unserialize(stripslashes($_COOKIE['step_data'])));exit;
		header('Cache-control: private, must-revalidate');
		$opt    = $_REQUEST['opt'];

		//实现下订单步骤-3步
		if(isset($_COOKIE['step']) && $opt==3) {
			setcookie('step', 3);
			$three_data = unserialize(stripslashes($_COOKIE['step_data']));

			$step_data_arr   = $three_data ? $three_data : array();
			$step_data_three = array_merge($step_data_arr, $_POST);
			setcookie('step_data', serialize($step_data_three));

			//根据订单来源，获得用户信息
			$this->getByCatefigure($step_data_three['source'], $step_data_three['source_id']);
			//获得当前消费者的默认收获地址
			$this->assign('addrlist', $this->_get_addrlist());
			$this->display('order/step3.form.html');
        } else if(isset($_COOKIE['step']) && $opt==2) {
            //下订单的第二步
			setcookie('step', 2);
			/* 验证品类 */
			$_POST['clothingID'] = intval($_POST['clothingID']);

			if(!isset($this->_cloth[$_POST['clothingID']]) || $this->_cloth[$_POST['clothingID']] == null ){
				$this->assign('error_msg', '品类错误');
				$this->display('order/error.html');
				return false;
			}
			if(empty($_POST['source_id']) ){
				$this->assign('error_msg', '请选择客户来源');
				$this->display('order/error.html');
				return false;
			}
	
			//根据品类id获得品类id
			$cateName = '';
			switch($_POST['clothingID']) {
				case 3:
				    $cateName = '上衣';
					break;
				case 2000:
				    $cateName = '西裤';
					break;
				case 4000:
				    $cateName = '衬衣';
					break;
				case 5000:
				    $cateName = '马夹';
					break;
				case 6000:
				    $cateName = '大衣';
					break;
				default:
				    $cateName = '';
			}

			//获取数据并存入SESSION中,储存第一步数据信息
			if(!isset($_COOKIE['step_data'])) {
				setcookie('step_data', '');
			}
			$step_data_arr = unserialize(stripslashes($_COOKIE['step_data'])) ? unserialize(stripslashes($_COOKIE['step_data'])) : array();
			
			$step_data_two = array_merge($step_data_arr, $_POST);
			setcookie('step_data', serialize($step_data_two));

			//根据品类id获取工艺信息
			$this->getByCateCraft($_POST['clothingID']);
			//根据品类id获取刺绣信息
			$this->getByCateEmb($_POST['clothingID']);
			//根据品类id获得对应体形及风格
			$this->_get_body_type($_POST['clothingID']);
			//根据订单来源，获得量体数据
			$this->getByCatefigure($_POST['source'], $_POST['source_id']);
			//通过选择量体类型及品类，获得对应的量体字段
			$this->getByCatePositions($_POST['clothingID']);

			$this->assign('back_url', 'order-addApp-'.$this->_store['user_id'].'-'.$this->_store['user_token'].'-back.html');
			$this->assign('sess_data', unserialize(stripslashes($_COOKIE['step_data'])));
			$this->assign('cateName', $cateName);
			$this->display('order/step2.form.html');

        } else {
			//下订单的第一步
			setcookie('step', 1);

			$args = $this->get_params();

			//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
			if( !empty($args)) {
				//处理获得参数
				$store_id = empty($args[0]) ? 0 : intval($args[0]);//裁缝ID
				$token    = empty($args[1]) ? 0 : $args[1];//裁缝token
				$go_type  = empty($args[2]) ? '' : $args[2];//back
				//$go_type  = empty($_POST['type']) ? '' : $_POST['type'];//获得进入类型

				if($go_type != 'back') {//如果是重新进入页面，则进行初始化操作
					//执行初始化、登录
					$this->visitor->logout();
					$this->_store['user_id'] = '';

					if(empty($store_id) || empty($token)) {
						$this->assign('error_msg', '参数有误');
						$this->display('order/app_error.html');
						return false;
					}

					//判断当前用户是否存在
					$user_info = $this->_mod_member->get(array(
						'conditions' => "user_id = $store_id AND user_token = '$token'",
					));
					//执行自动登录
					$this->_do_login($user_info['user_id']);

					$this->_store['user_id']   = $user_info['user_id'];
				}

			}
			//print_r(unserialize(stripslashes($_COOKIE['step_data'])));exit;
			//判断当前用户是否已登录
			if(empty($this->_store['user_id'])) {
				$this->assign('error_msg', '您无权访问此页面！');
				$this->display('order/app_error.html');
				return false;
			} else {
				$this->assign('sess_data', unserialize(stripslashes($_COOKIE['step_data'])));
				$this->display('order/step1.form.html');
			}
        }
    }
	
	/**
     * app调用二次下单页面
	 *
     * @param string store_id 裁缝ID; string $token 裁缝token
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function secondOrderApp()
	{
		$args = $this->get_params();

		//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
		if( !empty($args)) {
			//处理获得参数
			$store_id = empty($args[0]) ? 0 : intval($args[0]);//裁缝ID
			$token    = empty($args[1]) ? 0 : $args[1];//裁缝token
			$order_id = empty($args[2]) ? '' : $args[2];//orderid
			//获取订单数据，重新组装，
			/* 获取订单信息 */
			$model_order =& m('order');
			$order_info = $model_order->get($order_id);
			if (!$order_info) {
				$this->assign('error_msg', '未找到此订单');
				$this->display('order/app_error.html');
				return false;
			}
		
			$figure = $this->_get_order_figure($order_id);
			$step_data = array(
				'source'     => $order_info['source'],
				'source_id'  => $order_info['source_id'],
				'clothingID' => $order_info['cloth'],
				'fabric'     => $order_info['fabric'],
				'crafts'     => unserialize($order_info['craft']),//工艺
				'embs'       => unserialize($order_info['embs']),//刺绣
				'figure'     => $figure,//量体
			);
			setcookie('step_data', serialize($step_data));
			header("Location: order-addApp-{$store_id}-$token.html ");
		}
	}
	
	function _get_order_figure($order_id)
    {
        $figure_mod = m('orderfigure');
        $figure_info = $figure_mod->get(array(
			'conditions' => 'order_id='.$order_id,
        ));
        return $figure_info;
    }

	/**
     * app执行支付
	 *
     * @param string store_id 裁缝ID; string $token 裁缝token；string $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function orderPay()
	{
		$args = $this->get_params();
		//处理获得参数
		$order_id = empty($args[0]) ? 0 : $args[0];//订单id

		if(empty($order_id)) {
			$this->assign('error_msg', '参数有误');
			$this->display('order/app_error.html');
			return false;	
		}
		//判断当前用户是否已登录
		if(empty($this->_user_id)) {
			$this->assign('error_msg', '您无权访问此页面！');
			$this->display('order/app_error.html');
			return false;
		} else {
			//获得订单信息
			$order = $this->_mod_order_model->get("order_id='$order_id'");

			if(empty($order)) {
				$this->assign('error_msg', '无对应支付订单！');
				$this->display('order/app_error.html');
				return false;
			}
			//执行app订单支付函数
			//include 'paycenter.app.php';
			//$payCen = NEW PaycenterApp();
			//$payCen->goToAppPay('order', $order['order_sn']);
			$this->assign('orderInfo', $order);
			header('Content-Type:text/html;charset=' . CHARSET);
			$this->display('order/apppay.form.html');
		}
	}

	/**
     * 保存添加订单页面
	 *
     * @param
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function create()
	{
		//判断时间是否重复提交
		if(empty($_COOKIE['step_data'])) {
			$this->assign('error_msg', '不得重复提交');
			$this->display('order/app_error.html');
			return false;
		}
		$create_data = unserialize(stripslashes($_COOKIE['step_data']));
	
		//重新组装数据
		$create_data['figures']['height'] = $_POST['figure']['height'];
		$create_data['figures']['weight'] = $_POST['figure']['weight'];
		$create_data['kh_order_num']      = $_POST['kh_order_num'];
		$create_data['kh_addr_id']        = $_POST['kh_addr_id'];
		$create_data = array_merge($create_data, $_POST);
		$data = $create_data;

        /* 验证品类 */
        $data['clothingID'] = intval($data['clothingID']);
        if(!isset($this->_cloth[$data['clothingID']]) || $this->_cloth[$data['clothingID']] == null ){
			$this->assign('error_msg', '品类错误');
			$this->display('order/app_error.html');
			return false;
        }

        $data['figure_type'] = 10052;

        /* 验证订单来源（需求、消费者） 并根据是否有会员返回userid */
        $_check_source = $this->_check_source($data);
        /* 验证工艺 */
        $data['crafts'] = $this->_check_craft($data);
        /* 验证刺绣 */
        $data['embs']   = $this->_check_emb($data);
        /* 验证面料 */
        $data['fabric'] = $this->_check_fabric($data);
        /* 验证量体数据 */
        //$_check_figure = $this->_check_figure($data);

        /* 物流地址 */
        $_check_address = $this->_check_address($data);
		if(!$_check_address || !$data['fabric'] ||  empty($_check_source)) {
			return false;
		}

		$data['user']    = $_check_source;//客户信息
		$data['address'] = $_check_address;//物流信息
	
        /* 会员信息 */
        /* 事务开始 */
        $transaction = $this->_mod_order->beginTransaction();

        $oInfo = $this->_submit($data);
        if (!$oInfo){
            $this->_mod_order->rollback();
			$this->assign('error_msg', $this->get_error());
			$this->display('order/app_error.html');
			return false;

        }
        $this->_mod_order->commit($transaction);

        //给会员发消息
        sendMessage(array('to_user_id'=>$data['user']['user_id'], 'location_url'=>'buyer_order.html', 'type'=>1));
        //给裁缝发短信提示订单生成 add by zhaoxinran
        $order=$this->_mod_order_model->get(array(
        		'conditions'=>"order_id={$oInfo['id']}"));
        $conditions = array('conditions'=>"user_id ='{$order['store_id']}'");
        $caifeng=$this->_mod_member->get($conditions);
        //smsAuthCode($caifeng['phone_mob'], 'order', 'order_create', 'get', 'pc','',array('order_sn'=>$order['order_sn'],'money'=>$order['goods_amount']));

		//成功后跳转成功页面
		$this->assign('orderInfo', $order);
		$this->display('order/step4.form.html');
    }

    /**
     * 提交订单
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _submit($post = array()){
        $oData = $this->_order_data($post);

        $fData = $this->_figure_data($post);
        $order_id = $this->_mod_order->add($oData);

        if($order_id){
            $fData['order_id'] = $order_id;
            $figure_id = $this->_mod_order_figure->add($fData);
            if($figure_id){
                return array('id'=>$order_id,'sn'=>$oData['order_sn']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 获取订单量体数据
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function _figure_data($post = array()){

        $data = array(
                'order_id' => '',
                'storeid'  => $this->_store['store_id'],
                'userid'   => $post['user']['user_id'],
                'figure'   => $post['figure_type'],
                'lw'       => $post['figures']['lw'],
                'xw'       => $post['figures']['xw'],
                'zyw'      => $post['figures']['zyw'],
                'tw'       => $post['figures']['tw'],
                'stw'      => $post['figures']['stw'],
                'zjk'      => $post['figures']['zjk'],
                'zxc'      => $post['figures']['zxc'],
                'yxc'      => $post['figures']['yxc'],
                'qjk'      => $post['figures']['qjk'],
                'hyjc'     => $post['figures']['hyjc'],
                'hyc'      => $post['figures']['hyc'],
                'qyj'      => $post['figures']['qyj'],
                'yw'       => $post['figures']['yw'],
                'tgw'      => $post['figures']['tgw'],
                'td'       => $post['figures']['td'],
                'hyg'      => $post['figures']['hyg'],
                'qyg'      => $post['figures']['qyg'],
                'zkc'      => $post['figures']['zkc'],
                'ykc'      => $post['figures']['ykc'],
                'xiw'      => $post['figures']['xiw'],
                'jk'       => $post['figures']['jk'],
                'part_label_10726' => $post['figures']['part_label_10726'],
                'part_label_10725' => $post['figures']['part_label_10725'],
                'hd'       => $post['figures']['hd'],
                'weight'   => $post['figures']['weight'],
                'height'   => $post['figures']['height'],
        );
        foreach ($post['body_type'] as $key=>$val){
            $data[$key] = $val;
        }
        return $data;
    }

    /**
     * 获取订单数据
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function _order_data($post = array()){
        $order_sn = $this->_gen_order_sn();
        //订单状态
//         if($post['sub_type'] == 1){
            $status = ORDER_PENDING;
//         }else{
//             $status = ORDER_SAVING;
//         }

        //获取移动支付方式为默认支付
        $payment = $this->_mod_pay->get(array(
                'conditions' => "enabled=1 AND ismobile = 1",
                'order'      => "sort_order DESC"
        ));
        $amount = $this->_order_amount($post['clothingID'] , $post['fabric'],$post['kh_num']);

        /* 消费者应支付金额 */
        $kh_amount = $this->_order_amount_kh($post['kh_amount'],$amount['order_amount']);

        /* 消费者支付时机 */
        $ship_pay = intval($post['kh_ship_time']) == 2 ? intval($post['kh_ship_time']) : 1;

        $store = $this->_store;

        //可修改价格
//        if ($post['user']['user_id'] == 2){
//            $amount['order_amount'] = 0.01;
//            $kh_amount = 0.02;
//        }
       
        $user_name = $post['user']['user_name'];

        
        if($post['user']['type'] >= 1)//=====  第三方登陆  用昵称  =====
        {
            $user_name = $post['user']['nickname'];
        }

        $data = array(
			'order_sn'     => $order_sn,
			'discount'     => '0',
			'goods_amount' => $amount['goods_amount'],
			'order_amount' => $amount['order_amount'],
			//'kh_order_num'  => $kh_num,
			'kh_order_amount' => $kh_amount,  //客户应支付的金额,这个钱是算还是裁缝自己写?
			'kh_ship_pay'  => $ship_pay,
			'figure_type'  => $post['figure_type'],
			'cloth'        => $post['clothingID'],
			'fabric'       => $post['fabric'],
			'craft'        => !empty($post['crafts']) ? serialize($post['crafts']) : '',
			'embs'         => !empty($post['embs']) ? serialize($post['embs']) : '',
			'source'       => $post['source'],
			'source_id'    => $post['source_id'],
			'store_id'     => $store['store_id'],
			'store_name'   => $store['s'][$store['user_id'].'_'.$store['store_id']]['store_name'],
			'user_id'      => $post['user']['user_id'],
			'user_name'    => $user_name,
			'status'       => $status,
			'add_time'     => gmtime(),
			'payment_id'   => $payment['payment_id'],
			'payment_name' => $payment['payment_name'],
			'payment_code' => $payment['payment_code'],
			'out_trade_sn' => '',
			'pay_time'     => '',
			'pay_message'  => '',

			'kh_payment_id'   => $payment['payment_id'],
			'kh_payment_name' => $payment['payment_name'],
			'kh_payment_code' => $payment['payment_code'],
			'kh_out_trade_sn' => '',
			'kh_pay_time'     => '',
			'kh_pay_message'  => '',

			'ship_name'    => $post['address']['ship_name'],
			'ship_area'    => $post['address']['ship_area'],
			'ship_addr'    => $post['address']['ship_addr'],
			'ship_zip'     => $post['address']['ship_zip'],
			'ship_tel'     => $post['address']['ship_tel'],
			'ship_mobile'  => $post['address']['ship_mobile'],
			'ship_email'   => $post['address']['ship_email'],

			'kh_addr'      => $post['kh']['address'],
			'kh_name'      => $post['kh']['name'],
			'kh_sex'       => $post['kh']['sex'],
			'kh_mobile'    => $post['kh']['mobile'],
			'ship_time'    => '',
			'last_modified'=> gmtime(),
			'memo'         => $post['memo'],
			'source_from'  => $this->_source_from,
            'fav_id'         =>$post['favorable_id'],
            'fav_name'      =>$favname,
            'fav_price'      =>$post['subtotal']-$mtmPrice,
        );

        return $data;
    }

    /**
     * 根据品类id获取工艺信息
	 * param int $clothId 品类id
     * @see getByCateCraft
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function getByCateCraft($clothId)
	{
        $arr = $this->_mod_craft_parent->find(array(
                'conditions' => " 1 = 1 AND clothingID = {$clothId} AND is_active = 1",
        ));
        foreach ($arr as $row){
            $crafts[$row['id']] = $row;
            $ids[$row['id']] = $row['id'];
        }
        $arr = $this->_mod_craft->find(array(
                'conditions' => db_create_in($ids,'parentId'),
        ));
        foreach ($arr as $row){
            $crafts[$row['parentId']]['list'][$row['id']] = $row;
        }

		$this->assign('crafts', $crafts);
    }
    /**
     * Ajax检验面料并获取库存
     * @see ajax_fabric
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function ajax_fabric(){
        $code    = isset($_POST['code']) ? trim($_POST['code']) : '' ;
        $clothId = isset($_POST['id']) ? intval($_POST['id']) : 0 ;

        if($code == '' || $clothId == 0){
            $this->json_error('参数错误');
            return ;
        }
        # 8050大衣 8030 衬衣 8001 西服
        if(in_array($clothId, array(1,3,2000,4000))){
            //$clothId = 1;
            $cateId = 8001;
        }elseif ($clothId == 3000){
            $cateId = 8030;
        }elseif ($clothId == 6000){
            $cateId = 8050;
        }
        $fabric = $this->_mod_part->get(" cate_id = '{$cateId}' AND code = '{$code}' ");

        if(empty($fabric)){
            $this->json_error('没有对应的面料信息！');
            return ;
        }
        //检查库存，需要接口
        //..
        $stock = 2000;
		//$stock = 0;
        if($stock <= 0){
            $this->json_error('面料库存不足');
            return ;
        }else{
			$amount = $this->_order_amount($clothId,$code);
			$content['goods'] = $amount['goods_amount'];
			$content['serve'] = $amount['serve_amount'];
			$content['stock'] = $stock;
			$this->json_result(array(
				'content' => $content
			));
			return ;
		}
    }

    /**
     * 根据品类id获取工艺信息
	 * param int $clothId 品类id
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function getByCateEmb($clothId)
	{
        $arr = $this->_mod_emb->find(array(
            'conditions' => " clothingID = '{$clothId}' AND e_tname IN ('emb_color','emb_site','emb_font')",
        ));
        if(empty($arr)){
            return ;
        }
        foreach ($arr as $row){
            $embs[$row['e_tname']]['tname']               = $this->_embs[$row['e_tname']];
            $embs[$row['e_tname']]['id']                  = $row['e_type'];
            $embs[$row['e_tname']]['clothingID']          = $clothId;
            $embs[$row['e_tname']]['list'][$row['e_id']]  = $row;
        }
        $embIds = $this->_get_embs_id($clothId);
        $this->assign('embs', $embs);
    }

    /**
     * Ajax筛选订单来源(需求/消费者)
     * @see ajax_source
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function ajax_source()
	{
        $id = isset($_POST['id']) ? intval($_POST['id']) ? intval($_POST['id']) : 1 : 1;
        if($id == 1){
            $demand = &m('demand');
            $dmdofer = &m('demandoffer');
            $ofs =  $dmdofer->find(array(
                'conditions' => " cf_id = '{$this->_store['user_id']}' AND status ='2' ",
            ));
            foreach ($ofs as $r){
                $in[$r['md_id']] = $r['md_id'];
            }
            $list = $demand->find(array(
				'conditions' => db_create_in($in,'md_id')." AND status = '3' ",
            ));
        }elseif($id == 2){//我的消费者
            $gk   = &m('customer_figure');
            $list = $gk->find(array(
				'conditions' => " storeid = '{$this->_store['user_id']}' ",
            ));
        }

        if(empty($list)){
            $this->json_error('no_data');
        }else{
            $this->assign('id',$id);
            $this->assign('list',$list);
			$this->assign('sess_data', unserialize(stripslashes($_COOKIE['step_data'])));
            $content = $this->_view->fetch('order/source.html');
            $this->json_result(array(
				'content' => $content,
            ));
            return ;
        }
    }

    /**
     * 通过选择的订单来源获取量体信息及客户信息
     * @see getByCatefigure
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function getByCatefigure($tp, $id)
	{
        if($tp == 1){
            $demand  = &m('demand');
            $dmdofer = &m('demandoffer');

            $ofs =  $dmdofer->find(array(
				'conditions' => " md_id = '{$id}' AND cf_id = '{$this->_store['user_id']}' AND status ='2' ",
            ));
            if(!empty($ofs)){
                $dmd = $demand->get(" md_id = {$id} AND status = '3' ");
            }

            if(empty($dmd)){
                $data = array();
            }else{
                $gk   = &m('customer_figure');
                $data['figure'] = $this->_mod_member_figure->get("userid = '{$dmd['user_id']}' AND storeid = '{$this->_store['user_id']}'");
                if(empty($data['figure'])){
                    $data['figure'] = $gk->get(" userid = '{$dmd['user_id']}' AND storeid = '{$this->_store['user_id']}' ");
                }
                $mem   = $this->_mod_member->get($dmd['user_id']);

                $data['kh_name'] = $data['figure']['customer_name'] ? $data['figure']['customer_name'] : $mem['real_name'];
                $data['kh_addr'] = $mem['region_name'];
                $data['kh_sex']  = $mem['gender'];
                $data['kh_mobile'] = $data['figure']['customer_mobile'] ? $data['figure']['customer_mobile'] : $mem['phone_mob'];
                if(!$data['figure']['height']) $data['figure']['height'] = $mem['height'];
                if(!$data['figure']['weight']) $data['figure']['weight'] = $mem['weight'];
            }
        }elseif ($tp == 2){
            $gk   = &m('customer_figure');
            $data['figure'] = $gk->get("figure_sn = '{$id}' AND storeid = '{$this->_store['user_id']}' ");
            $mem   = $this->_mod_member->get($data['figure']['userid']);

            $data['kh_name']   = $data['figure']['customer_name'] ? $data['figure']['customer_name'] : $mem['real_name'];
            $data['kh_mobile'] = $data['figure']['customer_mobile'] ? $data['figure']['customer_mobile'] : $mem['phone_mob'];

            $data['kh_addr'] = $mem['region_name'];
            $data['kh_sex'] = $mem['gender'];
            if(!$data['figure']['height']) $data['figure']['height'] = $mem['height'];
            if(!$data['figure']['weight']) $data['figure']['weight'] = $mem['weight'];

        }

        $this->assign('data',$data);
    }

	/**
     * 通过选择量体类型及品类，获得对应的量体字段
     * @see getByCatefigure
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    function getByCatePositions($clothingID)
	{
		$positions_tm = $this->_mod_mtm_pt->find(array(
			'conditions' => " clothId = $clothingID",
        ));

        foreach ($positions_tm as $row){
            if($row['categoryId'] == 10052){//净体量体
                $positions['totally'][$row['clothId']]['info']['clothName']  = $row['clothName'];
                $positions['totally'][$row['clothId']]['info']['clothId']    = $row['clothId'];
                $positions['totally'][$row['clothId']]['info']['categoryId'] = $row['categoryId'];
                $positions['totally'][$row['clothId']]['list'][$row['ids']]  = $row;
            }elseif($row['categoryId'] == 10054){//标准号加减
                $positions['caddyshack'][$row['clothId']]['info']['clothName']  = $row['clothName'];
                $positions['caddyshack'][$row['clothId']]['info']['clothId']    = $row['clothId'];
                $positions['caddyshack'][$row['clothId']]['info']['categoryId'] = $row['categoryId'];
                $positions['caddyshack'][$row['clothId']]['list'][$row['ids']]  = $row;
            }
        }

		$this->assign('positions', $positions);
	}

	/**
     * 根据品类id获得对应体形及风格
	 *
     * @param string store_id 裁缝ID; string $token 裁缝token
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function _get_body_type($clothId){
        $body_type_tm = $this->_mod_mtm_bt->find(array(
				'conditions' => " clothId = $clothId",
        ));
        foreach ($body_type_tm as $row){
            if($row['cateID'] != 32){
                $body_type['feature'][$row['cateID']]['info']['name'] = $row['cateName'];
                $body_type['feature'][$row['cateID']]['info']['id']   = $row['cateID'];
                $body_type['feature'][$row['cateID']]['info']['nm']   = 'body_type_'.$row['cateID'];
                $body_type['feature'][$row['cateID']]['list'][$row['id']] = $row;
            }else{
                $body_type['style'][$row['clothId']]['info']['name'] = $row['clothName'];
                $body_type['style'][$row['clothId']]['info']['id']   = $row['cateID'];
                $body_type['style'][$row['clothId']]['info']['nm']   = 'body_type_'.$row['clothId'];
                $body_type['style'][$row['clothId']]['list'][$row['id']] = $row;
            }
        }

		$this->assign('body_type',$body_type);
    }

    function _get_embs_id($clothID = 0){
        $embs = array(
                3 => array(
                        'emb_con'   => '421',//'内容'
                        'emb_site'  => '1218',//'位置'
                        'emb_font'  => '518',//'字体'
                        'emb_color' => '422',//'颜色'
                ),
                2000 => array(
                        'emb_con'   => '2207',//'内容'
                        'emb_site'  => '2507',//'位置'
                        'emb_font'  => '2523',//'字体'
                        'emb_color' => '2213',//'颜色'
                ),
                3000 => array(
                        'emb_con'   => '3676',//'内容'
                        'emb_site'  => '3201',//'位置'
                        'emb_font'  => '3248',//'字体'
                        'emb_color' => '3631',//'颜色'
                        //'emb_size' => '3259',//'绣字大小'
                ),
                4000 => array(
                        'emb_con'   => '4149',//'内容'
                        'emb_site'  => '4550',//'位置'
                        'emb_font'  => '4155',//'字体'
                        'emb_color' => '4150',//'颜色'
                ),
                6000 => array(
                        'emb_con'   => '6396',//'内容'
                        'emb_site'  => '6976',//'位置'
                        'emb_font'  => '6413',//'字体'
                        'emb_color' => '6404',//'颜色'
                ),
        );
        return $embs[$clothID];
    }

    /**
     * 获取收货地址列表
     *
     * @version 1.0.0 (Jan 18, 2015)
     * @author Ruesin
     */
    function _get_address_list(){
        $mAddr = &m('address');
        $addrlist = $mAddr->find(" user_id = '{$this->_store['user_id']}' ");
        return $addrlist;
    }

    /**
     * 获取收货地址列表
     *
     * @version 1.0.0 (Jan 18, 2015)
     * @author Ruesin
     */
    function _get_addrlist(){
        $mAddr = &m('address');
		$defaddrlist = $mAddr->get(" user_id = '{$this->_store['user_id']}'  AND addr_id=". $this->_store['def_addr']);
		if(empty($defaddrlist)) {
			$defaddrlist = $mAddr->get(" user_id = '{$this->_store['user_id']}' ");
		}
        return $defaddrlist;
    }
    ////============================================ 私有方法 ================================================////
    /**
     * 验证量体信息
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _check_figure($post = array()){
        $figure = $post['figures'];

        $ck = 1;
        if(!$figure['height']) {$ck = 0;}
        if(!$figure['weight']){$ck = 0;}

        if(!$figure['lw']){$ck = 0;}
        if(!$figure['xw']){$ck = 0;}
        if(!$figure['zyw']){$ck = 0;}
        if(!$figure['tw']){$ck = 0;}
        if(!$figure['stw']){$ck = 0;}
        if(!$figure['zjk']){$ck = 0;}
        if(!$figure['zxc']){$ck = 0;}
        if(!$figure['yxc']){$ck = 0;}
        if(!$figure['qjk']){$ck = 0;}
        if(!$figure['hyjc']){$ck = 0;}
        if(!$figure['hyc']){$ck = 0;}
        if(!$figure['qyj']){$ck = 0;}
        if(!$figure['yw']){$ck = 0;}
        if(!$figure['tgw']){$ck = 0;}
        if(!$figure['td']){$ck = 0;}
        if(!$figure['hyg']){$ck = 0;}
        if(!$figure['qyg']){$ck = 0;}
        if(!$figure['zkc']){$ck = 0;}
        if(!$figure['ykc']){$ck = 0;}
        if(!$figure['xiw']){$ck = 0;}
        if(!$figure['jk']){$ck = 0;}
        if(!$figure['part_label_10726']){$ck = 0;}
        if(!$figure['part_label_10725']){$ck = 0;}
        if(!$figure['hd']){$ck = 0;}
        if($ck){
            return $figure;
        }else{
			$this->assign('error_msg', '量体信息不正确');
			$this->display('order/error.html');
			return false;
        }
    }

    /**
     * 验证面料信息
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _check_fabric($post = array()){
        $code    = trim($post['fabric']);
        $clothId = $post['clothingID'];
        if($code == ''){
			$this->assign('error_msg', '面料错误');
			$this->display('order/error.html');
			return false;
        }

        # 8050大衣 8030 衬衣 8001 西服
        if(in_array($clothId, array(1,3,2000,4000))){
            $clothId = 1;
            $cateId = 8001;
        }elseif ($clothId == 3000){
            $cateId = 8030;
        }elseif ($clothId == 6000){
            $cateId = 8050;
        }
        $fabric = $this->_mod_part->get(" cate_id = '{$cateId}' AND code = '{$code}' ");

        if(empty($fabric)){
			$this->assign('error_msg', '无此面料');
			$this->display('order/error.html');
			return false;
        }
        //检查库存，需要接口
        //..
        $stock = 2000;
        if($stock < 0){
			$this->assign('error_msg', '面料库存不够');
			$this->display('order/error.html');
			return false;
        }
        return $code;
    }

    /**
     * 验证刺绣信息
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _check_emb($post){
        $type = $this->_get_embs_id($post['clothingID']);
        foreach ($post['embs'] as $k => $v){
            if($type[$k] == '' || !isset($type[$k])){
				$this->assign('error_msg', '刺绣信息错误');
				$this->display('order/error.html');
				return false;
            }
            if($v != ''){
                if($k != 'emb_con'){
                    $em[$v] = $v;
                }
            }else{
                unset($post['embs'][$k]);
            }
        }
        $arr = $this->_mod_emb->find(array(
                'conditions' => " clothingID = '{$post['clothingID']}' AND ".db_create_in($em,'e_id'),
        ));
        if(count($arr) != count($em)){
			$this->assign('error_msg', '刺绣信息错误');
			$this->display('order/error.html');
			return false;
        }
        return $post['embs'];
    }
    /**
     * 验证来源信息
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _check_source(&$post){
        if($post['source'] != 1 && $post['source'] != 2 ){
			$this->assign('error_msg', '客户来源错误');
			$this->display('order/error.html');
			return false;
        }

        if($post['source'] == 1){
            $demand = &m('demand');
            $dmdofer = &m('demandoffer');

            $ofs =  $dmdofer->find(array(
				'conditions' => " md_id = '{$post['source_id']}' AND cf_id = '{$this->_store['user_id']}' AND status ='2' ",
            ));
            if(empty($ofs)){
				$this->assign('error_msg', '未发现此需求');
				$this->display('order/error.html');
				return false;
            }
            $data = $demand->get("status = '3' AND md_id = '{$post['source_id']}'");
            $userid = $data['user_id'];
        }elseif($post['source'] == 2){
            $gk   = &m('customer_figure');
            $data = $gk->get(" storeid = '{$this->_store['user_id']}' AND figure_sn = '{$post['source_id']}'");
            $userid = $data['userid'];
        }

        if(!$userid){
			$this->assign('error_msg', '未发现此客户');
			$this->display('order/error.html');
			return false;
        }
        $mem = $this->_mod_member->get($userid);
        if(empty($mem)){
			$this->assign('error_msg', '未发现此客户');
			$this->display('order/error.html');
			return false;
        }
		//存储客户信息
		return $mem;
    }

    /**
     * 验证工艺信息
     * @see _check_craft
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _check_craft($post = array()){
        $clothId = $post['clothingID'];

        foreach ($post['crafts'] as $key=>$row){
            if($row != ''){
                $parent[$key] = $key;
            }else{
                unset($post['crafts'][$key]);
            }
        }

        $arr = $this->_mod_craft_parent->find(array(
            'conditions' => " 1 = 1 AND clothingID = {$clothId} AND is_active = 1 AND ".db_create_in($parent,'id'),
        ));
        if(count($arr) != count($parent)){
			$this->assign('error_msg', '工艺信息错误');
			$this->display('order/error.html');
			return false;
        }
        $arr = $this->_mod_craft->find(array(
			'conditions' => " 1 = 1 AND clothingID = {$clothId} AND ".db_create_in($parent,'parentId')." AND ".db_create_in($post['crafts'],'code'),
        ));
        if(count($arr) != count($post['crafts'])){
			$this->assign('error_msg', '工艺信息错误');
			$this->display('order/error.html');
			return false;
        }

        return $post['crafts'];
    }
    /**
     * 验证并获取收货信息
     *
     * @version 1.0.0 (Jan 18, 2015)
     * @author Ruesin
     */
    function _check_address(&$post){
        $id = $post['kh_addr_id'];
        if(!intval($id)){
			$this->assign('error_msg', '收获地址不正确');
			$this->display('order/error.html');
			return false;
        }
        $mAddr = &m('address');
        $addrs = $mAddr->get(" addr_id ='{$id}'  AND user_id = '{$this->_store['user_id']}' ");
        if(empty($addrs)){
			$this->assign('error_msg', '收获地址不正确');
			$this->display('order/error.html');
			return false;
        }
        $post['ship_name'] = $addrs['consignee'];
        $post['ship_area'] = $addrs['region_name'];
        $post['ship_addr'] = $addrs['address'];
        $post['ship_zip'] = $addrs['zipcode'];
        $post['ship_tel'] = $addrs['phone_tel'];
        $post['ship_mobile'] = $addrs['phone_mob'];
        $post['ship_email'] = $addrs['email'];

		return $post;
    }

    /**
     * 生成订单号
     *
     * @version 1.0.0 (2014-12-30)
     * @author Ruesin
     */
    private function _gen_order_sn(){
        mt_srand((double) microtime() * 1000000);
        $timestamp = gmtime();
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $orders = $this->_mod_order->find("order_sn='{$order_sn}'");
        if (empty($orders)){
            return $order_sn;
        }
        return $this->_gen_order_sn();
    }

    /**
     * 订单金额(只是计算出来默认价格，体形太大的没做处理)
     *
     * @author Ruesin
     */
    function _order_amount($clothId = 0,$fabric = '',$kh_num=1){
        //上衣 : 2.1    西裤 : 1.5    衬衫 : 2.8    马夹 : 0.8    大衣 : 1.68   里料 : 2
        //能到这里，说明之前已经验证过面料和品类，可以不做验证处理
        $dh = array(
                3    => 2.1,
                2000 => 1.5,
                3000 => 2.8,
                4000 => 0.8,
                6000 => 1.68,
                'll' => 2,
        );

        $part = $this->_mod_part->get(array(
                'conditions' => " goods_sn = '{$fabric}'",
                'fields'     => 'part_id,cost_price,price,maket_price',
        ));

        $data['goods_amount'] = $part['price'] * $dh[$clothId] * intval($kh_num);

        switch($clothId){
            case 1:

                break;
            case 3:

                break;
        }

        $data['serve_amount'] = 200;   //服务费
        $data['order_amount'] = $data['goods_amount']* + $data['serve_amount'];//加上服务费

        return $data;
    }
    /**
     * 格式裁缝写的消费者订单价格
     *
     * @version 1.0.0 (Jan 20, 2015)
     * @author Ruesin
     */
    function _order_amount_kh($kh_amount = 0,$cf_amount = 0){
        if(floatval($kh_amount) <= 0){
            $kh_amount = $cf_amount;
        }
        return $kh_amount;
    }

	/**
     * 收货地址列表
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function addrlist()
	{
		/* 取得列表数据 */
        import("address.lib");
        $address   = new Address($this->_store['user_id']);
        $addresses = $address->_list();

		$this->assign('def_addr', $this->_store['def_addr']);
        $this->assign('count_addresses', count($addresses));
        $this->assign('addresses', $addresses);
		$this->display('order/addrlist.html');
	}

	/**
     * 收获地址设为默认，并跳回订单页面
	 *
     * @param int addr_id 需要设为默认地址的id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function ajaxDefAddr()
	{
        $addr_id = $_POST['addr_id'];
        $def_addr_id = isset($addr_id) ? intval($addr_id) : 0;
        if($def_addr_id){
            import("address.lib");
            $this->visitor->setDef(array("def_addr"=>$def_addr_id)); //设置默认地址

			//更新缓存
			//$this->_store['def_addr']=$def_addr_id;
            $_SESSION['user_info']['def_addr'] = $def_addr_id;
			//设置成功跳转订单页面
			$this->json_result('', '默认地址设置成功');
			return;
        } else {
			//跳转到错误页面
			$this->json_error('没有对应数据');
            die;
        }

	}

	/**
     * 添加收获地址
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function addr_add()
	{
		if (!IS_POST) {
			/* 取得列表数据 */
            $this->assign('act', 'addr_add');
            header('Content-Type:text/html;charset=' . CHARSET);
			$this->_get_regions();
			$this->display('order/address.form.html');
		} else {

            if (!$_POST['phone_mob']) {
				$this->assign('error_msg', '请填写手机号！');
				$this->display('order/error.html');
				return false;
            }
            $region_name = $_POST['region_name'];
            $data = array(
                'user_id'       => $this->_store['user_id'],
                'consignee'     => $_POST['consignee'],
                'al_name'       => $_POST['consignee'],
               //'region_id'     => $_POST['region_id'],
				'region_id'     => $_POST['region_list'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'zipcode'       => $_POST['zipcode'],
                //'phone_tel'     => $_POST['phone_tel'],
                'phone_mob'     => $_POST['phone_mob'],
                'email'         => '89',
            );
            $model_address =& m('address');
            if (!($address_id = $model_address->add($data))) {
				$this->assign('error_msg', '地址添加失败，请稍后再试！');
				$this->display('order/error.html');
				return false;
            }
			//添加成功直接跳转到列表页面
            header("Location: order-addrlist.html ");
        }
	}

	/**
     * 编辑收获地址
	 *
     * @param int $addr_id  需要编辑的地址id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function addr_edit()
	{
		$addr_id = empty($_GET['addr_id']) ? 0 : intval($_GET['addr_id']);
        if (!$addr_id) {
			$this->assign('error_msg', '无当前收货地址');
			$this->display('order/error.html');
			return false;
        }
        if (!IS_POST) {
            $model_address =& m('address');
            $find_data     = $model_address->find("addr_id = {$addr_id} AND user_id=" . $this->_store['user_id']);
            if (empty($find_data)) {
				$this->assign('error_msg', '无当前收货地址');
				$this->display('order/error.html');
				return false;
            }
            $address = current($find_data);

            $this->assign('address', $address);
            $this->assign('act', 'addr_edit');
            $this->_get_regions();
            $this->display('order/address.form.html');
        } else {
            if (!$_POST['phone_mob']) {
				$this->assign('error_msg', '请填写手机号！');
				$this->display('order/error.html');
				return false;
            }
            $data = array(
                'consignee'     => $_POST['consignee'],
                //'region_id'     => $_POST['region_id'],
                'region_id'     => $_POST['region_list'],
                'al_name'       => $_POST['consignee'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                //'zipcode'       => $_POST['zipcode'],
                //'phone_tel'     => $_POST['phone_tel'],
                'phone_mob'     => $_POST['phone_mob'],
                //'email'         => $_POST['email'],
            );
            $model_address =& m('address');
            $model_address->edit("addr_id = {$addr_id} AND user_id=" . $this->_store['user_id'], $data);
            if ($model_address->has_error()) {
				$this->assign('error_msg', '地址编辑失败！');
				$this->display('order/error.html');
				return false;
            }
            //编辑成功直接跳转到列表页面
            header("Location: order-addrlist.html ");
        }
	}

	/**
     * ajax删除制定的收获地址
	 *
     * @param NLL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function ajaxDropAddr()
    {
        $addr_id = isset($_POST['addr_id']) ? trim($_POST['addr_id']) : 0;
        if (!$addr_id) {
			//设置成功跳转订单页面
			$this->json_result( $def_addr_id);
			return;
        }
        $ids = explode(',', $addr_id);//获取一个类似array(1, 2, 3)的数组
        $model_address = & m('address');
        $drop_count    = $model_address->drop("user_id = " . $this->_store['user_id'] . " AND addr_id " . db_create_in($ids));
        if (!$drop_count) {
			$this->json_error('地址删除失败');
            die();
        }

        if ($model_address->has_error()) {
			$this->json_error('地址删除失败');
            die();
        }

    }

	/**
     * 获得区域
	 *
     * @param NLL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function _get_regions()
    {
        $model_region =& m('region');
        $regions = $model_region->get_list(0);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }

        $this->assign('regions', $regions);
    }

	/**
     * 测试页面
	 *
     * @param string store_id 裁缝ID; string $token 裁缝token；string $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function error_test()
	{
		$this->assign('error_msg', '品类错误');
		$this->display('order/app_error.html');
	}

	/**
	 * 创建订单
	 *
	 * @author lian.chuang
	 */
	function newCreate()
    {
	    $user = $this->visitor->get();
        $member_mod = m('member');
        $user = $member_mod->get_info($user['user_id']);
        $user['user_name'] = addslashes($user['user_name']);
	    $this->_user_id = $user['user_id'];
	    if (!$this->_user_id)
	    {
	        $this->json_error('_user_id不存在');
	        return;
	    }
        $_Carts = new Carts();
        $aCart = $_Carts->main(['source_from'=>$this->_source_from]);
        if(!$aCart['object']){
            $this->json_error('购物车没有商品');
            return;
        }
	    $_order = $this->_order_info($aCart);

	    if (!$_order['shipping']['address'])
	    {
	        $this->json_error('请选择收货地址!');
	        return;
	    }
	    if (!$_order['ships']['defship'])
    	{
	        $this->json_error('没有选择物流信息');
	        return;
    	}

	    if (!$_order['ships']['defship']['is_fress'] && !$_order['ships']['defship']['post_fee']) //收费
	    {
	        $this->json_error('物流费用计算失败');
	        return;
	    }

        //校验库存
	    $this->_check_stock($aCart['object']);
	    $_order['user'] = $user;

	    if($_POST['payment']){
	        $payment = $this->_mod_pay->get("payment_code = '{$_POST['payment']}' ");
	    }
	    if(!$_POST['payment'] || !$payment){
	        $payment = $this->_mod_pay->get(array(
	            'conditions' => "payment_code = 'wxpay' ",
	            'order'      => "sort_order DESC"
	        ));
	    }
	    $_order['payment'] = $payment;
	    $_order['invoice'] = $_POST['invoice'];
	    $_order['remark'] = isset($_POST['remark']) ? trim($_POST['remark']) : '';
	    $order_type =& ot('news');
	    $transaction = $this->_mod_order->beginTransaction();
	    $oInfo = $order_type->submit(array(
	        '_order' => $_order,
	        '_cart'  => $aCart,
	    ));

	    if (!$oInfo)
	    {
	        $this->_mod_order->rollback();
	        $this->json_error($order_type->get_error());
	        return;
	    }
	    $this->_mod_order->commit($transaction);
	    //处理抵扣券
	    if($_order['debit']['data']){
	        foreach ($_order['debit']['data'] as $row){
	            $dIds[$row['id']] = $row['id'];
	        }
	        $mDebit = &m('voucher');
	        $mDebit->edit(db_create_in($dIds,'id'), array('use_status'=>'1','order_id'=>$oInfo['order_sn']));
	    }
	
	    //订单日志
	    imports("orderLogs.lib");
	    $oLogs = new OrderLogs();
	    $oLogs->_record(array(
	        'order_id' => $oInfo['order_id'],
	        'op_id'    => $_order['user']['user_id'],
	        'op_name'  => $_order['user']['user_name'],
	        'behavior' => 'create',
	        'from'     => '',
	        'to'       => ORDER_PENDING,
	    ));
	    if($oInfo['status'] != ORDER_PENDING){
	        $oLogs->_record(array(
	            'order_id' => $oInfo['order_id'],
	            'op_id'    => $_order['user']['user_id'],
	            'op_name'  => $_order['user']['user_name'],
	            'behavior' => 'payment',
	            'from'     => ORDER_PENDING,
	            'to'       => ORDER_ACCEPTED,
	            'text'     => '订单创建并支付成功!',
	        ));
	        if ($oInfo['status'] == ORDER_WAITFIGURE){
	            $oLogs->_record(array(
	                'order_id' => $oInfo['order_id'],
	                'op_id'    => $_order['user']['user_id'],
	                'op_name'  => $_order['user']['user_name'],
	                'behavior' => 'payment',
	                'from'     => ORDER_ACCEPTED,
	                'to'       => ORDER_WAITFIGURE,
	                'text'     => '订单创建支付成功，等待量体。'
	            ));
	        }elseif ($oInfo['status'] == ORDER_PRODUCTION){
	            $oLogs->_record(array(
	                'order_id' => $oInfo['order_id'],
	                'op_id'    => $_order['user']['user_id'],
	                'op_name'  => $_order['user']['user_name'],
	                'behavior' => 'payment',
	                'from'     => ORDER_ACCEPTED,
	                'to'       => ORDER_PRODUCTION,
	                'text'     => '订单创建支付成功，生产中。'
	            ));
	        }
	    }
	    // 订单生成成功清理购物车
	    $this->_clear();
	    $this->json_result($oInfo);
	}
    
    /**
     * 清理购物车数据
     *
     * @author Ruesin
     */
    function _clear()
    {
        $this->_mod_cart->drop(" user_id = '{$this->_user_id}' AND is_check=1");
        unset($_SESSION["_order"]);
        unset($_SESSION["_cart"]);
    }
    
    /**
     * 预计算新等级价格
     *
     * @author Ruesin
     */
    function _format_new_lv(&$aCart,&$_order,$disCount){
        foreach ($aCart['object'] as &$item){
            
            if(!$item['first']){
                //$prc = $this->_mod_cart->_calc_price($item['goods']['basePrice'] , $disCount);
                $prc['price'] = $this->_mod_cart->_format_price_int($item['goods']['markprice'] * $disCount);  //折扣价
                $item['goods']['price'] = $prc['price'];
                $item['subtotal'] = $item['goods']['price'] * $item['quantity'];
            }
            $amount += $item['subtotal'];
        }
        $aCart['goods_amount'] = $amount;
        $aCart['order_amount'] = $amount;
        $aCart['final_amount'] = $amount;
        
        if($_order['measure']['type'] == '1' || $_order['measure']['type'] == '2'){
            $aCart['order_amount'] = $aCart['final_amount'] = $aCart['order_amount'] + $aCart['measure_fee'];
            	
        }
        
        if($_order['debit']['data']){
            $aCart['final_amount'] -= $aCart['debit_fee'];
        }
         
        //余额
        if($_order['money'] > 0){
            
            if($_order['money'] >= $aCart['final_amount']){
                $money = $aCart['final_amount'];
            }else{
                $money = $_order['money'];
            }
            $_SESSION['_order']['money'] = $aCart['money_amount'] = $money;
            
            $aCart['final_amount'] -= $aCart['money_amount'];
        }
         
    }
    
    /**
     * 买一赠一
     *
     * @author Ruesin
     */
    function _oneToOne(&$aCart){
        $n = 1;
        
        $customs = $this->_mod_cart->_customs();
        foreach ($aCart['object'] as &$item){
            
            if ($item['type'] == 'suit'){
                $jgfCj = $customs['0016']['process_fee'] - $customs['0016']['one_fee'];
            }else{
                $jgfCj = $customs[$item['cloth']]['process_fee'] - $customs[$item['cloth']]['one_fee'];
            }
            
            if($item['type'] == 'diy'){
                $item['goods']['price'] = $item['goods']['basePrice'] - $jgfCj;
            }elseif ($item['type'] == 'suit'){
                if($item['goods']['is_promotion'] == '1' || $item['first']){
                    $item['goods']['price'] = $item['goods']['price'];
                }else{
                    ##$item['goods']['price'] = $this->_mod_cart->_format_price_int(($item['goods']['markprice']/$this->_mod_cart->coefficient) - $jgfCj);
                    $item['goods']['price'] = $this->_mod_cart->_format_price_int($item['goods']['basePrice']) - $jgfCj;
                }
            }
            
            $item['activity'] = 1;
            
            $item['subtotal'] = $item['goods']['price'] * $item['quantity'];
            
            $amount += $item['subtotal'];
            $p[$n] = $item['subtotal'];
            $n++;
            //$cloth[$item['cloth']] = $item['cloth'];
        }
        
        $mOneGift = &m('onegift');
        $a = $mOneGift->algoAmount($p,$_SESSION['_cart']['one_id']);
        
        
        $amount = $this->_mod_cart->_format_price_int($a['amount']*$aCart['actDiscount']);
        
        //$amount = $a['amount'];
        $discount = $a['discount'];
        $aCart['goods_amount'] = $amount;
        $aCart['order_amount'] = $amount;
        
        $aCart['final_amount'] = $aCart['order_amount'];
        $aCart['discount']     = $discount;
        $aCart['check_amount'] = $aCart['final_amount'];
        $aCart['point_g']      = $aCart['final_amount'];
        
    }
    
    
     /**
     * 订单支付页面
     * 
     * @author Ruesin
     */
    function paycenter(){
        $sn = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if(!$sn){
            $this->show_warning('参数错误1！');
            return;
        }




        $user = $this->visitor->get();
        $this->_user_id = $user['user_id'];
        if (!$this->_user_id)
        {
            $this->json_error('_user_id不存在');
            return;
        }
        
        $order = $this->_mod_order->get("order_sn = '{$sn}' AND user_id = '{$this->_user_id}'");
        if(!$order){
            $this->show_warning('参数错误2！');
            return;
        }
        $this->assign('order',$order);
        $order_id = $order['order_id'];
        
        if($order['status'] == ORDER_ACCEPTED || $order['status'] == ORDER_WAITFIGURE || $order['status'] == ORDER_PRODUCTION){
            //$this->display('orders/payment/haspend.html');
            $this->display('orders/payment/success.html');
            return;
        }

        if($order['status'] != ORDER_PENDING){
            $this->show_warning('订单不是待支付状态！');
            return;
        }
        
        //货到付款 
        if($order['payment_id'] == '-1'){
            $this->display('orders/payment/dispay.html');
        }
        
       
        
        if (is_weixin())
        {
            //=====  强制更新成微信支付  =====
            $payments = $this->_mod_pay->find("payment_code = 'wxpay' ");
            
            //===== 超过500订单  =====
            if($order['is_try'] == 1)
            {
                $day_time = strtotime(date("Y-m-d"));
                $order_num = $this->_mod_order->get(array(
                    'conditions' => " is_try=1 AND add_time >= $day_time AND status IN(20,30,40)",
                    'fields' => "count(*) as num",
                ));
                if($order_num['num'] >= 501)
                {
                    $this->show_warning('今日订单已经超过500单,请明天再来支付吧 ！');
                    return;
                }

            }
        }
        else
        {
            $payments =  $this->_mod_pay->find(array(
                'conditions' => "enabled=1 AND ismobile = 1",
                'order'      => "sort_order DESC"
            ));
        }
        //=====  微信支付Beg  =====
        if ($order['payment_code'] == 'wxpay') 
        {
            require_once "weipay/lib/WxPay.Api.php";
            require_once "weipay/WxPay.JsApiPay.php";
            require_once 'weipay/log.php';            //=====  创建支付订单  =====
            $tools = new JsApiPay();
            $openId = $tools->GetOpenid();
            
            
            
            $payment_sn = $this->get_payment_sn();
            $bill = array(
                'payment_sn' => $payment_sn,
                'amount'     => $order['order_amount'],
                'member_id'  => $order['user_id'],
                'account'    => $this->_config['alipay_account'], //?
                'ip'         => real_ip(),
                'start_time' => time(),
                'order_sn'   => $order['order_sn'],
                'pay_id'     => $order['payment_id'],
                'pay_code'   => $order['payment_code'],
                'pay_name'   => $order['payment_name'],
            );
             
            $this->out_trade = $payment_sn;   ///外部交易号
             
            $res = $this->_mod_bills->add($bill);
            if(!$res)
            {
                return false;
            }
             
            $res = $this->_mod_order->edit("order_id='{$order['order_id']}'" ,array("out_trade_sn" => $payment_sn));
            if(!$res)
            {
                return false;
            }
            
            
            $input = new WxPayUnifiedOrder();
            $input->SetBody("定制化主粮");
            $input->SetAttach("定制化主粮");
            $input->SetOut_trade_no($payment_sn);
            $input->SetTotal_fee($order["final_amount"]*100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("定制化主粮");
            $input->SetNotify_url('http://'.$_SERVER['SERVER_NAME']."/paynotifyw-index-{$order['order_sn']}.html");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $this->assign("payData", $jsApiParameters);
        }else{
        	$this->assign("payData", 'none');
        }
        //=====  微信支付End  =====
        
        $mOdebit = &m('orderdebit');
        $debits = $mOdebit->find("order_id = '{$order['order_id']}'");
        if($debits){
            $dbt_money = 0;
            foreach ($debits as $dbt){
                $dbt_money += $dbt['d_money'];
            }
            $this->assign('dmoney',$dbt_money);
        }
        $this->assign('apptk',$_REQUEST['token']);
        $this->assign('payments',$payments);
        $this->assign('obj', "order");
        $this->display('orders/payment/index.html');
    }
    
    /**
     * 得到唯一的payment_sn
     * @author yhao.bai
     * @params null
     * @return string payment_sn
     */
    public function get_payment_sn(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $payment_sn = time().str_pad($i,4,'0',STR_PAD_LEFT);
            $row = $this->_mod_bills->find("payment_sn='{$payment_sn}'");
        }while($row);
        return $payment_sn;
    }
    
    /**
     * Ajax 更改订单支付方式
     *
     * @author Ruesin
     */
    function change_payment(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $sn = isset($_POST['sn']) ?  trim($_POST['sn']) : '';
        if(!$id || !$sn){
            $this->json_error('params_error');
            return ;
        }
        $payment = $this->_mod_pay->get(" payment_id = '{$id}' AND enabled=1 AND ismobile = 1");
        if(empty($payment)){
            $this->json_error('payment_error');
            return ;
        }
    
        if (!$this->_mod_pay->in_white_list($payment['payment_code']))
        {
            $this->json_error('payment_disabled_by_system');
            return;
        }
        
        $eData = array(
                'payment_id'   => $payment['payment_id'],
                'payment_code' => $payment['payment_code'],
                'payment_name' => $payment['payment_name']
        );
        $edit = $this->_mod_order->edit("order_sn = '{$sn}' AND user_id = '{$this->_user_id}' AND status = '".ORDER_PENDING."'" ,$eData);
    
        if($edit >= 0){
            $this->json_result(array('nm'=>$payment['payment_name']));
            return ;
        }else{
            $this->json_error('change_payment_error');
            return ;
        }
    }

    function goToPay()
    {
        $args = $this->get_params();
        //处理获得参数
//        $order_id = empty($args[0]) ? 0 : $args[0];//订单id
        $obj      = isset($_POST['obj']) ? $_POST['obj'] : '';
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : '';
        if(empty($order_sn)) {
            $this->assign('error_msg', '参数有误');
            $this->display('order/app_error.html');
            return false;
        }
        //判断当前用户是否已登录
        if(empty($this->_user_id)) {
            $this->assign('error_msg', '您无权访问此页面！');
            $this->display('order/app_error.html');
            return false;
        } else {
            //获得订单信息
            $order = $this->_mod_order_model->get("order_sn='$order_sn'");

            if(empty($order)) {
                $this->assign('error_msg', '无对应支付订单！');
                $this->display('order/app_error.html');
                return false;
            }
            //执行app订单支付函数
            //include 'paycenter.app.php';
            //$payCen = NEW PaycenterApp();
            //$payCen->goToAppPay('order', $order['order_sn']);
            $this->assign('orderInfo', $order);
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->display('order/apppay.form.html');
        }

    }
    
    /**
     * 去支付(APP中的去支付请求的是orderPay，然后再POST到这里了)
     * 
     * @author Ruesin
     */
    function goToPay2(){
    
        $obj      = isset($_POST['obj']) ? $_POST['obj'] : '';
        $order_sn = isset($_POST['os']) ?  trim($_POST['os']) : '';
 
        if(!in_array($obj,array("order"))){
            $this->assign('error_msg', '支付对象错误');
            $this->display('order/app_error.html');
            return false;
        }
    
        if (!$order_sn){
            $this->assign('error_msg', '没有找到对应订单');
            $this->display('order/app_error.html');
            return false;
        }
        
        $order = $this->_mod_order->get("order_sn = '{$order_sn}' AND status = '11' AND user_id = '{$this->visitor->get("user_id")}'");
        
        if(!$order) {
            $this->assign('error_msg', '没有找到待支付订单');
            $this->display('order/app_error.html');
            return false;
        }
        
        $oPay = $order['payment_code'];
        
        //if($order['payment_code'] != 'malipay'){
            $order['payment_code'] = 'malipay';
        //}
        
        $payment_model =& m('payment');
         
         //验证支付方式是否可用，若不在白名单中，则不允许使用
    	if (!$payment_model->in_white_list($order['payment_code'])){
            $this->assign('error_msg', '支付方式不可用');
            $this->display('order/app_error.html');
            return false;
        }
         
        $payment_info  = $payment_model->get("payment_code = '{$order['payment_code']}' AND enabled=1 AND ismobile = 1");
    
        /* 没有启用，则不允许使用 */
        if (!$payment_info)
        {
            $this->assign('error_msg', '支付方式未开启');
            $this->display('order/app_error.html');
            return false;
        }
        if ($oPay != $order['payment_code']){
            $eData = array(
                    'payment_id'   => $payment_info['payment_id'],
                    'payment_code' => $payment_info['payment_code'],
                    'payment_name' => $payment_info['payment_name']
            );
        
            $this->_mod_order->edit("order_sn = '{$order_sn}' AND user_id = '{$this->visitor->get("user_id")}' AND status = '".ORDER_PENDING."'" ,$eData);
        }
        
    
        /* 生成支付URL或表单 */
        $payment    = $this->_get_payment($order['payment_code'], $payment_info);
    
        $res = $payment->createBill($order);
    
        if(!$res){
            $this->assign('error_msg', '意外错误无法支付，请联系客服！');
            $this->display('order/app_error.html');
            return false;
        }
           
        
         
        $payment_form = $payment->get_payform($order);
        $this->assign('payform', $payment_form);
        $this->assign('payment', $payment_info);
        header('Content-Type:text/html;charset=' . CHARSET);
        $this->display('/orders/payment/dopay.html');
    }

    
    
    
    ////==================================================================////
    ////********************** 新订单流程 End  ****************************////
    ////==================================================================////
    
}