<?php
class OrderrefundApp extends BackendApp
{
	var $_orderrefund_mod;
    function __construct()
    {
        parent::__construct();
        $this->_orderrefund_mod =& m('orderrefund');
    }
	/**
     * 订单退款审核列表
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <280181131@qq.com>
     * @return void
     */ 
    function index() {
        $page = $this->_get_page();
        $list = $this->_orderrefund_mod->find(array(
            'join' => 'belongs_to_order',
            'fields' => 'this.*,order_alias.order_sn',
            'limit' => $page['limit'],
            'order' => "id DESC, status ASC",
            'count' => true,
        ));
		
        $this->assign('list', $list);
        $page['item_count'] = $this->_orderrefund_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('orderrefund.index.html');
    }
    
	/**
     * 订单退款详情
	 *
     * @param int  $id 退款审核id；
	 *
     * @access protected
     * @author xuganglong <280181131@qq.com>
     * @return void
     */ 
    function view() {
        $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
        $info = $this->_orderrefund_mod->find(array(
            'join' => 'belongs_to_order',
            'fields' => 'this.*,order_alias.order_sn,order_alias.order_amount,order_alias.discount,order_alias.status as o_status,order_alias.extension,order_alias.money_amount,order_alias.final_amount',
            'conditions' => "id='{$id}'",
        ));
        $info = current($info);
        if(empty($info)){
            $this->show_warning("非法操作~");
            return;
        }
		//获得当前订单的撤销日志信息
		$refund_log = m('orderrefundlog');
		$refund_log_list = $refund_log->findAll(array(
			'conditions' => "order_id='{$info['order_id']}'",
			'fields'     => 'name',
			'index_key'	 => '',
		));
		
		$info['return_money'] = $info['money_amount'] + $info['final_amount'];

        $this->assign("refund_log_list", $refund_log_list);
		$this->assign("info", $info);
        $this->display('orderrefund.info.html');
    }
	
	/**
     * 订单审核退款
	 *
     * @param int  $id 退款审核id；int $order_id 订单id;
	 *
     * @access protected
     * @author xuganglong <280181131@qq.com>
     * @return void
     */ 
    function process() {
        $id       = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
		$order_id = isset($_POST["order_id"]) ? intval($_POST["order_id"]) : 0;
		$user = $this->visitor->get();
		
		$order_mod  = m('order');
		$order_info = $order_mod->get("order_id = {$order_id} ");
        if(isset($_POST["finished"])) {
            $result_id = $this->_orderrefund_mod->edit($id, array("status" => 1, 'verify_id'=>$user['user_name'], 'verify_time'=>gmtime()));
			
			if(!$result_id) {
				$this->show_message("操作失败，请重新操作！");
				return ;
			}
			
			//执行撤销操作-----待完善  
			//==== 返回  抵扣券
			$mOd = &m('orderdebit');
			$dbs = $mOd->find("order_id = '{$order_id}'");
			$data_ref = array();
			if ($dbs){
				foreach ($dbs as $row){
					$dIds[$row['d_id']] = $row['d_id'];
				}
				$mDebit = &m('debit');
				$mDebit->edit(db_create_in($dIds,'id'), array('is_used'=>'0'));
				
				//查询获得修改抵扣券信息
				$debit_list = $mDebit->findAll(array(
					'conditions' => db_create_in($dIds, 'id'),
					'fields'     => 'debit_name, money, user_id',
				));
				
				foreach ($debit_list as $row){
					$data_ref[] = array(//抵扣券
						'name'       => '退回用户使用的'.$row['money'].$row['debit_name'],
						'order_id'   => $order_info['order_id'],
						'type'       => 3,
						'cash_money' => $row['money'],
						'user_id'    => $row['user_id'],
						'add_time'   => gmtime(),
					);
				}
			}

			//====返回用户支付的金额到余额（现金支付、余额支付），减去收益
			if($order_info['money_amount'] || $order_info['final_amount']) {
				$mMem = &m('member');
				$member = $mMem->get($order_info['user_id']);
				$refund_money = $order_info['money_amount'] + $order_info['final_amount'];
				$money  = $member['money'] + $refund_money;
				$profit = $member['profit'] - $order_info['profit_amount'];
				if($money >= 0) {
					$mem_res = $mMem->edit(" user_id = '{$order_info['user_id']}' ", array('money'=>$money, 'profit'=>$profit));
					if(!$mem_res) {
						$this->show_message("操作失败，请重新操作！");
						return ;
					}
					$refund_log = &mr('orderrefundlog');
					//加入订单撤销日志
					$data_ref[] = array(//余额
						'name'       => '订单返回支付金额'.$refund_money.'元 ',
						'order_id'   => $order_info['order_id'],
						'type'       => 4,
						'cash_money' => $refund_money,
						'user_id'    => $order_info['user_id'],
						'add_time'   => gmtime(),
					);
					$data_ref[] = array(//撤销收益
						'name'       => '撤销当前用户'.$order_info['profit_amount'].'收益',
						'order_id'   => $order_info['order_id'],
						'type'       => 0,
						'cash_money' => $order_info['profit_amount'],
						'user_id'    => $order_info['user_id'],
						'add_time'   => gmtime(),
					);
					if($data_ref) {
						$refund_log->add(addslashes_deep($data_ref));
					}
					//修改订单状态为已退款
					$order_res = $order_mod->edit($order_id, array('status' => ORDER_YINGTUIKUAN));

					//加入日志并通知用户
					//include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
					//$push = new XingeMeg();
					//$push->toMasterXinApp($member['user_token'], '【Mfd】退款通知', '订单-'.$order_info['order_sn'].' 退款成功，金额已退回至账号余额，请查收', array('url_type'=>'system', 'location_id'=>''));
				}
			}
        }
        $this->show_message("操作成功！");
    }
}

?>
