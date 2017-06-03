<?php
/*
 * @author tangsj
 *
 */
class Client_examineAPP extends BackendApp {
	var $_user_mod;
	var $_lv_mod;
	var $_member_accountlog_mod;
	var $_mod_accountGallery;
	var $member_accountlog_mod;
	var $_mod_client_finance_detail;
	function __construct() {
		$this->ClientExamineApp ();
	}
	function ClientExamineApp() {
		parent::__construct ();
		
		$this->_user_mod = & m ( 'member' );
		$this->_lv_mod = & m ( 'memberlv' );
		$this->member_accountlog_mod = & m ( 'member_accountlog' );
		$this->_mod_accountGallery = &m ( "member_accountgallery" );
		$this->_mod_client_finance_detail = &m ( 'clientfinancedetail' );
	}
	/* 客户明细表 */
	function index() {
		$name = isset ( $_POST ['name'] ) ? $_POST ['name'] : '';
		$conditions = "1=1 AND (status =1 OR status =0)";
		if ($name) {
			$info = $this->_user_mod->get ( "user_name ='{$name}' AND serve_type =1" );
			if ($info) {
				$conditions .= " AND user_id=" . $info ['user_id'];
			} else {
				$conditions .= " AND user_id=0";
			}
		}
		
		if (isset ( $_GET ['sort'] ) && isset ( $_GET ['order'] )) {
			$sort = strtolower ( trim ( $_GET ['sort'] ) );
			$order = strtolower ( trim ( $_GET ['order'] ) );
			if (! in_array ( $order, array (
					'asc',
					'desc' 
			) )) {
				$sort = 'id';
				$order = 'desc';
			}
		} else {
			$sort = 'id';
			$order = 'desc';
		}
		
		// 等级信息
		$_lv_mod_list = $this->_lv_mod->find ( array (
				'conditions' => "lv_type = 'supplier'",
				'fields' => 'name' 
		) );
		
		// 获取后台更改记录
		$page = $this->_get_page ( 30 );
		// $member_accountlog_mod =& m('member_accountlog');
		$accountlog = $this->member_accountlog_mod->find ( array (
				'conditions' => $conditions,
				'count' => true,
				'limit' => $page ['limit'],
				'order' => 'add_time DESC' 
		) );
		foreach ( $accountlog as $key => $value ) {
			$user_info = $this->_user_mod->get ( $value ['user_id'] );
			$accountlog [$key] ['user_name'] = $user_info ['user_name'];
			
			$accountlog [$key] ['oldmoney'] = $user_info ['money'];
			$accountlog [$key] ['oldcoin'] = $user_info ['coin'];
			$accountlog [$key] ['oldpoint'] = $user_info ['point'];
			
			$accountlog [$key] ['oldlv'] = $_lv_mod_list [$user_info ['member_lv_id']] ['name'];
			$accountlog [$key] ['newlv'] = empty ( $value ['lv_id'] ) ? '无变化' : $_lv_mod_list [$value ['lv_id']] ['name'];
			$imgs = $this->_mod_accountGallery->find ( "custom_id = '{$value['id']}'" );
			$accountlog [$key] ['images'] = $imgs;
		}
		
		$page ['item_count'] = $this->member_accountlog_mod->getCount ();
		$this->_format_page ( $page );
		$this->assign ( 'page_info', $page );
		$this->assign ( 'accountlog', $accountlog );
		$this->assign ( 'name', $name );
		$this->display ( 'clientfinance/client_examine.index.html' );
	}
	function getaccount() {
		$id = intval ( $_GET ['id'] );
		$member_mod = & m ( 'member' );
		if (! $id) {
			$this->json_error ( "没有要审核的账户调整信息" );
			return;
		}
		
		$info = $this->member_accountlog_mod->get ( $id );
		// 等级信息
		$_lv_mod_list = $this->_lv_mod->find ( array (
				'conditions' => "lv_type = 'supplier'",
				'fields' => 'name' 
		) );
		
		$u = $member_mod->get ( $info ['user_id'] );
		$info ['user_name'] = $u ['user_name'];
		$info ['oldlv'] = $_lv_mod_list [$u ['member_lv_id']] ['name'];
		$info ['newlv'] = empty ( $info ['lv_id'] ) ? '无变化' : $_lv_mod_list [$info ['lv_id']] ['name'];
		$imgs = $this->_mod_accountGallery->find ( "custom_id='{$info['id']}'" );
		
		$this->assign ( 'info', $info );
		$this->assign ( 'imgs', $imgs );
		$content = $this->_view->fetch ( "clientfinance/account.lib.html" );
		die ( $this->json_result ( $content ) );
	}
	// 通过审核 auth tangsj
	function edit() {
		$id = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : 0;
		$status = isset ( $_REQUEST ['status'] ) ? $_REQUEST ['status'] : 0;
		if (! $id) {
			$this->json_error ( "没有要审核的信息" );
			return;
		}
		if (! $status) {
			$this->json_error ( "审核状态不对!" );
			return;
		}
		$oper_id = $this->visitor->get ( 'user_id' ); // 审核人
		$now_time = time ();
		$data = array (
				'status' => $status,
				'auditer' => $oper_id,
				'audit_time' => $now_time 
		);
		
		$transaction = $this->member_accountlog_mod->beginTransaction ();
		
		$is_edit = $this->member_accountlog_mod->edit ( $id, $data, 0 );
		if (! $is_edit) {
			$this->_mod_order->rollback ();
			$this->json_error ( '审核失败' );
			return;
		}
		
		$accountinfo = $this->member_accountlog_mod->get ( $id );
		
		$this->account_common ( $accountinfo, $id );
		// 如果有用户等级调整 需要重新生成 等级二维码跳转链接
		// editerweimaUrl($accountinfo['user_id']);
		
		$this->member_accountlog_mod->commit ( $transaction );
		$this->json_result ( $is_edit );
		
		/*
		 * if($is_edit = $this->member_accountlog_mod->edit($id,$data,0) == false){
		 *
		 * $this->json_error("审核失败");
		 * return ;
		 * }else{
		 * $this->json_result(1);
		 * return;
		 * }
		 */
	}
	// 下方的通过审核
	function through() {
		$id = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : 0;
		$ids = explode ( ',', $id );
		foreach ( $ids as $v ) {
			$info = $this->member_accountlog_mod->get ( $v );
			if ($info ['status'] == 1) {
				$user = $this->_user_mod->get ( $info ['user_id'] );
				
				$this->show_message ( '用户' . $user ['user_name'] . '已通过审核,请勿重复提交!', 'back_list', 'index.php?app=client_examine' );
				return;
			}
		}
		if (empty ( $ids )) {
			$this->json_error ( "没有要审核的信息" );
			return;
		}
		
		$oper_id = $this->visitor->get ( 'user_id' ); // 审核人
		$status = 1; // 审核通过
		$now_time = time ();
		$data = array (
				'status' => $status,
				'auditer' => $oper_id,
				'audit_time' => $now_time 
		);
		
		$transaction = $this->member_accountlog_mod->beginTransaction ();
		
		$is_edit = $this->member_accountlog_mod->edit ( db_create_in ( $ids, 'id' ), $data );
		
		if (! $is_edit) {
			$this->_mod_order->rollback ();
			$this->json_error ( '审核失败' );
			return;
		}
		foreach ( $ids as $value ) {
			$accountinfo = $this->member_accountlog_mod->get ( $value );
			$z = $accountinfo;
			$this->account_common ( $z, $ids );
		}
		$this->member_accountlog_mod->commit ( $transaction );
		
		if ($is_edit) {
			$this->show_message ( '通过审核!', 'back_list', 'index.php?app=client_examine' );
			return;
		} else {
			$this->show_message ( '审核失败!', 'back_list', 'index.php?app=client_examine' );
			return;
		}
	}
	/*
	 * 驳回
	 */
	function reject() {
		$id = isset ( $_REQUEST ['id'] ) ? $_REQUEST ['id'] : 0;
		$status = isset ( $_REQUEST ['status'] ) ? $_REQUEST ['status'] : 0;
		$fail_reason = isset ( $_REQUEST ['fail_reason'] ) ? $_REQUEST ['fail_reason'] : '';
		
		if (! $id) {
			$this->json_error ( "没有要驳回的信息" );
			return;
		}
		if (! $status) {
			$this->json_error ( "驳回状态不对!" );
			return;
		}
		if (empty ( $fail_reason )) {
			$this->json_error ( "驳回原因不能为空!" );
			return;
		}
		
		$oper_id = $this->visitor->get ( 'user_id' ); // 审核人
		$now_time = time (); // 审核时间
		
		$data = array (
				'status' => $status,
				'auditer' => $oper_id,
				'audit_time' => $now_time,
				'fail_reason' => $fail_reason 
		);
		
		if ($is_edit = $this->member_accountlog_mod->edit ( $id, $data, 0 ) == false) {
			
			$this->json_error ( "审核失败" );
			return;
		} else {
			$this->json_result ( 1 );
			return;
		}
	}
	
	/*
	 * 审核通过返回 详情页面
	 */
	function gettginfo() {
		$id = intval ( $_GET ['id'] );
		$member_mod = & m ( 'member' );
		if (! $id) {
			$this->json_error ( "没有要审核的账户调整信息" );
			return;
		}
		
		$info = $this->member_accountlog_mod->get ( $id );
		// 等级信息
		$_lv_mod_list = $this->_lv_mod->find ( array (
				'conditions' => "lv_type = 'supplier'",
				'fields' => 'name' 
		) );
		
		$u = $member_mod->get ( $info ['user_id'] );
		$editerinfo = $member_mod->get ( $info ['auditer'] );
		
		$info ['auditer'] = $editerinfo ['user_name'];
		$info ['user_name'] = $u ['user_name'];
		$info ['oldlv'] = $_lv_mod_list [$u ['member_lv_id']] ['name'];
		$info ['newlv'] = empty ( $info ['lv_id'] ) ? '无变化' : $_lv_mod_list [$info ['lv_id']] ['name'];
		$imgs = $this->_mod_accountGallery->find ( "custom_id='{$info['id']}'" );
		
		$this->assign ( 'info', $info );
		$this->assign ( 'imgs', $imgs );
		$content = $this->_view->fetch ( "clientfinance/tginfo.lib.html" );
		die ( $this->json_result ( $content ) );
	}
	
	/* 用户财务操作。公共方法 */
	private function account_common($data, $ac_log_ids) {
		$cash_mod = & m ( "ordercashlog" );
		// 修改等级
		if ($data ['lv_id']) {
			
			$this->_user_mod->edit ( $data ['user_id'], array (
					'member_lv_id' => $data ['lv_id'] 
			), 0 );
		}
		if ($data ['money'] > 0) {
			$mData = array (
					"user_id" => $data ["user_id"],
					"type" => "4", // 余额
					"minus" => "7", // 后台管理员增加（或减少）余额
					"cash_money" => $data ["money"],
					"admin_id" => $data ['admin_id'],
					"add_time" => time () 
			);
			// 添加余额
			if ($data ['money_type'] == 1) {
				$mData ['mark'] = "+";
				$mData ['name'] = "后台添加余额";
				// 添加余额
				$res = $this->_user_mod->setInc ( "user_id='{$data["user_id"]}'", "money", $data ["money"] );
				if ($res === false) {
					return false;
				}
			}			// 删减余额
			elseif ($data ['money_type'] == 2) {
				$mData ['mark'] = "-";
				$mData ['name'] = "后台减少余额";
				// 减除余额
				$res = $this->_user_mod->setDec ( "user_id='{$data["user_id"]}'", "money", $data ["money"] );
				if ($res === false) {
					return false;
				}
			}
			// 添加日志
			if (! $cash_mod->add ( $mData, false, 0 )) {
				return false;
			}
		}
		if ($data ['point'] > 0) {
			$pData = array (
					"user_id" => $data ["user_id"],
					"type" => "1", // 积分
					"minus" => "2", // 后台管理员增加（或减少）积分
					"cash_money" => $data ["point"],
					"admin_id" => $data ['admin_id'],
					"add_time" => time () 
			);
			// 添加积分
			if ($data ['point_type'] == 1) {
				$pData ['mark'] = "+";
				$pData ['name'] = "后台添加积分";
				// 添加积分
				$res_point = $this->_user_mod->setInc ( "user_id='{$data["user_id"]}'", "point", $data ["point"] );
				// reloadMember($data["user_id"]);
				if ($res_point === false) {
					return false;
				}
				$cfd_data = array (
						'user_id' => $data ["user_id"],
						'finance_sn' => "后台调整会员账户日志id为:{$ac_log_ids}",
						'type' => 2,
						'minus' => 7,
						'mark' => '+',
						'add_time' => time (),
						'trans_amount' => $data ["point"],
						'abstract' => "后台会员增加积分：{$data['point']}" 
				);
				
				$finance_info = $this->_mod_client_finance_detail->get ( array (
						'conditions' => "user_id={$data ["user_id"]} order by add_time DESC" 
				) );
				if ($finance_info) {
					$cfd_data ['start_balance'] = $finance_info ['end_balance'];
				} else {
					$cfd_data ['start_balance'] = 0;
				}
				$cfd_data ['end_balance'] = $cfd_data['start_balance'] + $data ["point"];
				$res=$this->_mod_client_finance_detail->add($cfd_data,false,0);
				// 升级会员更新等级
				// $relo = reloadMember($data["user_id"]);
			}			// 删减积分
			elseif ($data ['point_type'] == 2) {
				$pData ['mark'] = "-";
				$pData ['name'] = "后台减少积分";
				// 删减积分
				$res_point = $this->_user_mod->setDec ( "user_id='{$data["user_id"]}'", "point", $data ["point"] );
				reloadMember ( $data ["user_id"] );
				if ($res_point === false) {
					return false;
				}
				$cfd_data = array (
						'user_id' => $data ["user_id"],
						'finance_sn' => "后台调整会员账户日志id为:{$ac_log_ids}",
						'type' => 1,
						'minus' => 7,
						'mark' => '-',
						'add_time' => time (),
						'trans_amount' => $data ["point"],
						'abstract' => "后台会员减少积分：{$data['point']}"
				);
				
				$finance_info = $this->_mod_client_finance_detail->get ( array (
						'conditions' => "user_id={$data ["user_id"]} order by add_time DESC"
				) );
				if ($finance_info) {
					$cfd_data ['start_balance'] = $finance_info ['end_balance'];
				} else {
					$cfd_data ['start_balance'] = 0;
				}
				$cfd_data ['end_balance'] = $cfd_data ['start_balance'] -  $data ["point"];
				$res=$this->_mod_client_finance_detail->add($cfd_data,false,0);
				// 进行降级的会员等级更新
				// reloadDowngradeMember($data["user_id"]);
			}
			// 添加日志
			if (! $cash_mod->add ( $pData, false, 0 )) {
				return false;
			}
		}
		if ($data ['coin'] > 0) {
			$cData = array (
					"user_id" => $data ["user_id"],
					"type" => "2", // 麦富迪币
					"minus" => "7", // 后台管理员增加（或减少）积分
					"cash_money" => $data ["coin"],
					"admin_id" => $data ['admin_id'],
					"add_time" => time () 
			);
			// 添加麦富迪币
			if ($data ['coin_type'] == 1) {
				$cData ['mark'] = "+";
				$cData ['name'] = "后台添加麦富迪币";
				// 添加麦富迪币
				$res_coin = $this->_user_mod->setInc ( "user_id='{$data["user_id"]}'", "coin", $data ["coin"] );
				if ($res_coin === false) {
					return false;
				}
			} // 删减麦富迪币
			else if ($data ['coin_type'] == 2) {
				$cData ['mark'] = "-";
				$cData ['name'] = "后台减少麦富迪币";
				// 删减麦富迪币
				$res_coin = $this->_user_mod->setDec ( "user_id='{$data["user_id"]}'", "coin", $data ["coin"] );
				if ($res_coin === false) {
					return false;
				}
			}
			// 添加日志
			if (! $cash_mod->add ( $cData, false, 0 )) {
				return false;
			}
		}
		return true;
	}
}
























