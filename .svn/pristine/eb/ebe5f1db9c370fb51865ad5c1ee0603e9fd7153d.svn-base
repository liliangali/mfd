<?php
/*
 * @author zxr
 *
 */
class Client_financeAPP extends BackendApp {
	var $_client_finance_mod;
	var $_mem_mod;
	var $pagenum;
	var $_auth_mod;
	function __construct() {
		$this->ClientFinanceApp ();
	}
	function ClientFinanceApp() {
		parent::__construct ();
		$this->pagenum =10;
		$this->_client_finance_mod = &m ( 'clientfinancedetail' );
		$this->_mem_mod = &m ( 'member' );
		$this->_auth_mod = &m ( 'auth' );
	}
	/* 客户明细表 */
	function index() {
		$page = $this->_get_page ( $this->pagenum );
		$conditions = '1=1 ';
		$time_flag = 0;
		$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
		
		$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : '';
		$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : '';
		if ($time_from > $time_to) {
			$this->show_warning ( '初始时间不能大于截至时间' );
			return;
		}
		
		if ($username) {
			
			$member_info = $this->_mem_mod->get ( "serve_type=1 AND (user_name='{$username}' OR phone_mob='{$username}')" );
			if (! $member_info) {
				$this->show_warning ( '该用户不存在' );
				return;
			}
			$user_id = $member_info ['user_id'];
			$conditions .= " AND cfd.user_id={$user_id}";
			$time_flag = 1;
		}
		
		if (! $time_from && ! $time_to) {
		} else {
			$time_from = strtotime ( $time_from );
			$time_to = strtotime ( $time_to );
			$time_flag = 1;
			if($time_from){
				$conditions.=" AND cfd.add_time>={$time_from}";
			}
			if($time_to){
				$conditions .= " AND cfd.add_time<={$time_to}";
			}
			
		}
		// var_dump($conditions);
		$finance_list = $this->_client_finance_mod->find ( array (
				'conditions' => $conditions,
				'join' => 'has_client',
				'fields' => 'cfd.*,member.user_name',
				'count' => true 
		) );
		/*
		 * var_dump($finance_list);
		 * var_dump($page);
		 */
		
		/* 借方（支出）expend */
		/* 贷方（收款）earning */
		$array_list = array ();
		$expend_money = 0;
		$earning_money = 0;
		/* 冒泡查询 */
		if ($finance_list) {
			$array_list = $this->count_client_finance ( ( array ) $finance_list );
		}
		$count = count ( $array_list );
		$finance_count = ceil ( $count / $page ['pageper'] );
		$curr_page = $page ['curr_page'] - 1;
		$page_start = $curr_page * $page ['pageper'];
		$page_end = $page_start + $page ['pageper'];
		$client_finance_list = array ();
		if (is_array ( $array_list )) {
			foreach ( $array_list as $k => $v ) {
				$tmp='';
				$v['end_balance']=(string)$v['end_balance'];
				if ($page_start < $k && $k <= $page_end) {
					if(strstr($v['end_balance'],'-')){
						$tmp=str_replace('-', '', $v['end_balance']);
						$v['end_balance']=price_format($tmp,'-￥%s');
					}else{
						$v['end_balance']=price_format($v['end_balance']);
					}
					$client_finance_list [] = $v;
				}
				$expend_money = $expend_money + $v ['expend'];
				$earning_money = $earning_money + $v ['earning'];
			}
		}
		$page ['item_count'] = $count;
		$this->_format_page ( $page );
		$this->assign ( 'page_info', $page );
		$this->assign ( 'count', $count );
		$this->assign ( 'expend_money', $expend_money );
		$this->assign ( 'earning_money', $earning_money );
		$this->assign ( 'finance_list', $client_finance_list );
		$this->assign ( 'filtered', $time_flag ? 1 : 0 );
		$this->display ( 'clientfinance/client_finance.index.html' );
	}
	/* 客户明细账表 */
	function search() {
		if (! IS_POST) {
			$this->display ( 'clientfinance/client_finance.search.html' );
		} else {
			
			$username = ! empty ( $_POST ['client_name'] ) ? $_POST ['client_name'] : '';
			$time_from = ! empty ( $_POST ['time_from'] ) ? $_POST ['time_from'] : 0;
			$time_to = ! empty ( $_POST ['time_to'] ) ? $_POST ['time_to'] : 0;
			$page = $this->_get_page ( $this->pagenum );
			if (! $username) {
				$this->show_warning ( '用户名不能为空' );
				return;
			}
			if ($time_from > $time_to) {
				$this->show_warning ( '初始时间不能大于截至时间' );
				return;
			}
			/* 起始时间和截至时间可以存在一个 */
			if (! $time_from && ! $time_to) {
				
				$time_flag = 0;
			} else {
				$time_from_s = $time_from;
				$time_to_s = $time_to;
				$time_from = strtotime ( $time_from );
				$time_to = strtotime ( $time_to );
				$time_flag = 1;
			}
			$user_info = $this->_mem_mod->get ( "serve_type=1 AND (user_name={$username} OR phone_mob={$username})" );
			if (! $user_info) {
				$this->show_warning ( '该用户不存在' );
				return;
			}
			$user_id = $user_info ['user_id'];
			
			$conditions = "1=1 AND user_id='{$user_id}'";
			if ($time_flag){
				if($time_from){
					$conditions .= " AND add_time>={$time_from}";
				}
				if($time_to){
					$conditions.=" AND add_time<={$time_to}";
				}
			}
			$finance_info = $this->_client_finance_mod->find ( array (
					'conditions' => $conditions,
					'limit' => $page ['limit'],
					'fields' => '*',
					'count' => true 
			) );
			/* 统计借款和收款要所有数据，没想到好办法，先重新获取数据 */
			$finance_tmp = $this->_client_finance_mod->find ( array (
					'conditions' => $conditions,
					'fields' => 'type,trans_amount' 
			) );
			$count = $page ['item_count'] = $this->_client_finance_mod->getCount ();
			$earning_money = 0; // 贷款
			$expend_money = 0; // 借款
			foreach ( $finance_tmp as $k => $v ) {
				if ($v ['type'] == 1) { // 借方
					$expend_money += $v ['trans_amount'];
				}
				if ($v ['type'] == 2) { // 贷方
					$earning_money += $v ['trans_amount'];
				}
			}
			foreach ( $finance_info as $key => $v ) {
				if ($v ['abstract']) {
					$finance_info [$key] ['abstract'] = explode ( ',', $v ['abstract'] );
				}
			}
			$this->_format_page ( $page );
			foreach ( $page ['page_links'] as $key => $value ) {
				$page ['page_links'] [$key] = str_replace ( 'search', 'detail', $value ) . "&amp;user_id={$user_id}&amp;client_name={$username}&amp;time_from={$time_from_s}&amp;time_to={$time_to_s}";
			}
			$page ['next_link'] = str_replace ( 'search', 'detail', $value ) . "&amp;user_id={$user_id}&amp;client_name={$username}&amp;time_from={$time_from_s}&amp;time_to={$time_to_s}";
			$this->assign ( 'page_info', $page );
			$this->assign ( 'client_name', $username );
			$this->assign ( 'user_id', $user_id );
			$this->assign ( 'count', $count );
			$this->assign ( 'time_from', !empty($time_from_s)?$time_from_s:'' );
			$this->assign ( 'time_to', !empty($time_to_s)?$time_to_s:'' );
			$this->assign ( 'expend_money', $expend_money ); // 余额支出
			$this->assign ( 'earning_money', $earning_money ); // 余额收款
			$this->assign ( 'filtered', $time_flag ? 1 : 0 );
			$this->assign ( 'finance_info', $finance_info );
			$this->display ( 'clientfinance/client_finance.info.html' );
		}
	}
	/* 客户明细账详情 */
	function detail() {
		$page = $this->_get_page ( $this->pagenum );
		
		$user_id = isset ( $_GET ['user_id'] ) ? $_GET ['user_id'] : '';
		$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
		
		$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : '';
		$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : '';
		if ($time_from > $time_to) {
			$this->show_warning ( '初始时间不能大于截至时间' );
			return;
		}
		if (! $time_from && ! $time_to) {
			
			$time_flag = 0;
		} else {
			$time_from = strtotime ( $time_from );
			$time_to = strtotime ( $time_to );
			$time_flag = 1;
		}
		
		$conditions = "1=1 AND user_id='{$user_id}'";
		if ($time_flag) {
			if($time_from){
				$conditions .=" AND add_time>={$time_from}";
			}
			if($time_to){
				$conditions .= "  AND add_time<={$time_to}";
			}			
		}
		$finance_info = $this->_client_finance_mod->find ( array (
				'conditions' => $conditions,
				'limit' => $page ['limit'],
				'fields' => '*',
				'count' => true 
		) );
		/* 统计借款和收款要所有数据，没想到好办法，先重新获取数据 */
		$finance_tmp = $this->_client_finance_mod->find ( array (
				'conditions' => $conditions,
				'fields' => 'type,trans_amount' 
		) );
		$count = $page ['item_count'] = $this->_client_finance_mod->getCount (); // 获取统计数据
		$this->_format_page ( $page );
		$earning_money = 0; // 贷款
		$expend_money = 0; // 借款
		foreach ( $finance_tmp as $k => $v ) {
			if ($v ['type'] == 1) { // 借方
				$expend_money += $v ['trans_amount'];
			}
			if ($v ['type'] == 2) { // 贷方
				$earning_money += $v ['trans_amount'];
			}
		}
		$first_key=key($finance_info);
		foreach ( $finance_info as $key => $v ) {
			if($key==$first_key){
				$start_balance=$v['start_balance'];
			}
			if ($v ['abstract']) {
				$finance_info [$key] ['abstract'] = explode ( ',', $v ['abstract'] );
			}
		}
		$this->assign('start_balance',$start_balance);
		//$finance_info=array_merge(array())
		$this->assign ( 'count', $count );
		$this->assign ( 'filtered', $time_flag ? 1 : 0 );
		$this->assign ( 'page_info', $page );
		$this->assign ( 'client_name', $username );
		$this->assign ( 'expend_money', $expend_money ); // 余额支出
		$this->assign ( 'earning_money', $earning_money ); // 余额收款
		$this->assign ( 'user_id', $user_id );
		$this->assign ( 'finance_info', $finance_info );
		$this->assign ( 'url', $_SERVER ['HTTP_REFERER'] );
		$this->display ( 'clientfinance/client_finance.info.html' );
	}
	/* 查询该用户是否存在 */
	function ajax_search_name() {
		$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
		if ($username) {
			$conditons = "AND serve_type=1 AND (user_name = '" . $username . "' OR phone_mob='" . $username . "')";
			
			$count = $this->_mem_mod->find ( array (
					'conditions' => "1=1 " . $conditons,
					'fields' => '*' 
			) );
			if ($count) {
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 0;
		}
	}
	/* 统计客户财富数据 */
	function count_client_finance($finance_list, $array_tmp = array(), $i = 0) {
		if (empty ( $finance_list )) {
			return '';
		} else {
			do {
				foreach ( $finance_list as $k => $v ) {
					$i += 1;
					$array_tmp [$i] = $v;
					/* 起初余额 */
					$array_tmp [$i] ['start_balance'] = $v ['start_balance'];
					$array_tmp [$i] ['expend'] = 0;
					$array_tmp [$i] ['earning'] = 0;
					if ($v ['type'] == 1) {
						$array_tmp [$i] ['expend'] = $array_tmp [$i] ['expend'] + $v ['trans_amount'];
					} else {
						$array_tmp [$i] ['earning'] = $array_tmp [$i] ['earning'] + $v ['trans_amount'];
					}
					$array_tmp [$i] ['end_balance'] = $v ['start_balance'] + $array_tmp [$i] ['earning'] - $array_tmp [$i] ['expend'];
					unset ( $finance_list [$k] );
					
					foreach ( $finance_list as $key => $value ) {
						if ($value ['user_id'] == $array_tmp [$i] ['user_id']) {
							if ($value ['type'] == 1) {
								$array_tmp [$i] ['expend'] = $array_tmp [$i] ['expend'] + $value ['trans_amount'];
							} else {
								$array_tmp [$i] ['earning'] = $array_tmp [$i] ['earning'] + $value ['trans_amount'];
							}
							$array_tmp [$i] ['end_balance'] = $v ['start_balance'] + $array_tmp [$i] ['earning'] - $array_tmp [$i] ['expend'];
							unset ( $finance_list [$key] );
						}
					}
					break;
				}
				$finance_list = array_values ( $finance_list );
			} while ( ! empty ( $finance_list ) );
			return $array_tmp;
		}
	}
	
	/* 创业者收益明细 */
	function estp_index() {
		$page = $this->_get_page ( $this->pagenum );
		$conditions = '1=1 AND cfd.minus=4';
		$time_flag = 0;
		$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
		
		$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : '';
		$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : '';
		if ($time_from > $time_to) {
			$this->show_warning ( '初始时间不能大于截至时间' );
			return;
		}
		
		if ($username) {
			
			$member_info = $this->_mem_mod->get ( "serve_type=1 AND (user_name='{$username}' OR phone_mob='{$username}')" );
			if (! $member_info) {
				$this->show_warning ( '该用户不存在' );
				return;
			}
			if ($member_info ['member_lv_id'] <= 1) {
				$this->show_warning ( '该用户不是创业者' );
				return;
			}
			$user_id = $member_info ['user_id'];
			$conditions .= " AND cfd.user_id={$user_id}";
			$time_flag = 1;
		}
		
		if (! $time_from && ! $time_to) {
		} else {
			$time_from = strtotime ( $time_from );
			$time_to = strtotime ( $time_to );
			$time_flag = 1;
			if($time_from){
				$conditions.=" AND cfd.add_time>={$time_from}";
			}
			if($time_to){
				$conditions .= " AND cfd.add_time<={$time_to}";
			}
			
		}
		// var_dump($conditions);
		$earnings_all_list = $this->_client_finance_mod->find ( array (
				'conditions' => $conditions . " AND member.member_lv_id>1",
				'join' => 'has_client',
				'fields' => 'cfd.*,member.user_name,member.real_name',
				'count' => true 
		) );
		/* var_dump($earnings_all_list); */
		$tmp_list = array ();
		$array_list = array ();
		$earning_all_money = 0; // 收益总额
		$income_tax_all_money = 0; // 个税总额
		/* 冒泡查询 */
		if ($earnings_all_list) {
			$array_list = $this->count_estp_earning ( array_values ( $earnings_all_list ) );
		}
		$count = 0;
		if ($array_list) {
			$count = count ( $array_list );
			$erning_count = ceil ( $count / $page ['pageper'] );
			$curr_page = $page ['curr_page'] - 1;
			$page_start = $curr_page * $page ['pageper'];
			$page_end = $page_start + $page ['pageper'];
			$all_user_ids_tmp = i_array_column ( $array_list, 'user_id' );
			foreach ( $all_user_ids_tmp as $key => $value ) {
				if ($key >= $page_start && $key < $page_end) {
					$all_user_ids [$key] = $value;
				}
			}
			$id_range = join ( ',', $all_user_ids );
			if ($id_range) {
				$user_identity = $this->_auth_mod->find ( array (
						'conditions' => "1=1 AND user_id in ({$id_range})",
						'fields' => 'auth.card,auth.status,auth.realname,user_id' 
				) );
			}
			foreach ( $array_list as $key => $value ) {
				
				if ($key > $page_start && $key <= $page_end) {
					$tmp_list [$key] = $value;
					$tmp_list [$key] ['identity_card'] = '';
					foreach ( $user_identity as $k => $v ) {
						if ($value ['user_id'] == $v ['user_id']) {
							if ($v ['status'] == 1 && $v ['card']) {
								$tmp_list [$key] ['identity_card'] = $v ['card'];
							} else {
								/* 该创业者的认证信息里没有身份证信息或并未审核通过 */
								$tmp_list [$key] ['identity_card'] = "该创业者的认证信息里没有身份证";
							}
							$tmp_list [$key] ['realname'] = $v ['realname'];
							break;
						}
					}
					/* 个人认证表里没有该创业者的认证信息 */
					if (! $tmp_list [$key] ['identity_card']) {
						$tmp_list [$key] ['identity_card'] = "没有该创业者的认证信息";
					}
					if ($tmp_list [$key] ['real_name']) {
						$tmp_list [$key] ['real_name'] = $tmp_list [$key] ['real_name'];
					} else if ($tmp_list [$key] ['realname']) {
						$tmp_list [$key] ['real_name'] = $tmp_list [$key] ['realname'];
					} else {
						$tmp_list [$key] ['real_name'] = '无';
					}
					$tmp_list [$key] ['tax_money'] = $tmp_list [$key] ['earning_money'] / 4;
					$tmp_list [$key] ['all_money'] = $tmp_list [$key] ['earning_money'] + $tmp_list [$key] ['tax_money'];
				}
				$earning_all_money = $earning_all_money + $value ['earning_money'];
				$income_tax_all_money = $income_tax_all_money + $value ['earning_money'] / 4;
			}
		}
		$page ['item_count'] = $count ? $count : 0;
		$this->_format_page ( $page );
		$this->assign ( 'page_info', $page );
		$this->assign ( 'count', $count );
		$this->assign ( 'earning', $earning_all_money );
		$this->assign ( 'income', $income_tax_all_money );
		$this->assign ( 'earning_list', $tmp_list );
		$this->assign ( 'filtered', $time_flag ? 1 : 0 );
		$this->display ( 'clientfinance/estp.index.html' );
	}
	/* 统计创业者收益明细数据 */
	function count_estp_earning($list, $array_tmp = array(), $i = 0) {
		if (empty ( $list )) {
			return '';
		} else {
			do {
				foreach ( $list as $k => $v ) {
					$i += 1;
					$array_tmp [$i] = $v;
					/* 该创业者第一笔收益转余额时间 */
					$array_tmp [$i] ['start_time'] = $v ['add_time'];
					/* 该创业者的总收益 */
					$array_tmp [$i] ['earning_money'] = 0;
					$array_tmp [$i] ['earning_money'] = $array_tmp [$i] ['earning_money'] + $v ['trans_amount'];
					$array_tmp [$i] ['end_time'] = $v ['add_time'];
					unset ( $list [$k] );
					
					foreach ( $list as $key => $value ) {
						if ($value ['user_id'] == $array_tmp [$i] ['user_id']) {
							if ($array_tmp [$i] ['start_time'] > $value ['add_time']) {
								$array_tmp [$i] ['start_time'] = $value ['add_time'];
							}
							/* 截至当前该创业者最近一笔收益转余额的时间 */
							if ($array_tmp [$i] ['end_time'] < $value ['add_time']) {
								$array_tmp [$i] ['end_time'] = $value ['add_time'];
							}
							
							$array_tmp [$i] ['earning_money'] = $array_tmp [$i] ['earning_money'] + $value ['trans_amount'];
							unset ( $list [$key] );
						}
					}
					break;
				}
				$list = array_values ( $list );
			} while ( ! empty ( $list ) );
			return $array_tmp;
		}
	}
	/* 财务报表导出 */
	function export() {
		
		/* 某客户财务详情报表 */
		if ($_GET ['type'] == 'client_finance_detail') {
			$user_id = isset ( $_GET ['user_id'] ) ? $_GET ['user_id'] : '';
			
			$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : 0;
			$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : 0;
			if (! $user_id) {
				$this->show_warning ( '参数错误' );
				return;
			}
			if ($time_from > $time_to) {
				$this->show_warning ( '初始时间不能大于截至时间' );
				return;
			}
			$time_from_s = 0;
			$time_to_s = 0;
			if (! $time_from && ! $time_to) {
				
				$time_flag = 0;
			} else {
				
				$time_from = strtotime ( $time_from );
				$time_to = strtotime ( $time_to );
				$time_from_s = date ( 'Ymd', $time_from );
				$time_to_s = date ( 'Ymd', $time_to );
				$time_flag = 1;
			}
			
			$conditions = "1=1 AND user_id='{$user_id}'";
			$client_info = $this->_mem_mod->get ( $conditions );
			if ($time_flag) {
				if($time_from){
					$conditions.=" AND add_time>={$time_from}";
				}
				if($time_to){
					$conditions .= " AND add_time<={$time_to}";
				}
				
			}
			$finance_info = $this->_client_finance_mod->find ( array (
					'conditions' => $conditions,
					'fields' => '*',
					'count' => true 
			) );
			/* var_dump($finance_info);exit(); */
			
			$res = array ();
			$i = 0;
			if ($finance_info) {
				if (! $time_flag) {
					$first = reset ( $finance_info );
					$time_from_s =  $first ['add_time'];
					$time_to_s = $first ['add_time'] ;
					foreach ( $finance_info as $key => $v ) {
						if ($v ['add_time'] < $time_from_s) {
							$time_from_s =$v ['add_time'];
						}
						if ($v ['add_time'] > $time_to_s) {
							$time_to_s = $v ['add_time'];
						}
					}
					$time_from_s=date('Ymd',$time_from_s);
					$time_to_s=date('Ymd',$time_to_s);
				}
				foreach ( $finance_info as $key => $v ) {
					$i += 1;
					$res [$i] ['year'] = date ( 'Y', $v ['add_time'] );
					$res [$i] ['month'] = date ( 'm', $v ['add_time'] );
					$res [$i] ['day'] = date ( 'd', $v ['add_time'] );
					$res [$i] ['sn'] = $v ['finance_sn'];
					$res [$i] ['abstract'] = $v ['abstract'];
					if ($v ['type'] == 1) {
						$res [$i] ['expend'] = $v ['trans_amount'];
						$res [$i] ['earning'] = 0;
					} else {
						$res [$i] ['expend'] = 0;
						$res [$i] ['earning'] = $v ['trans_amount'];
					}
					$res [$i] ['end_balance'] = $v ['end_balance'];
				}
			} else {
				$time_from_s = date ( 'Ymd', time () );
				$time_to_s = date ( 'Ymd', time () );
				$res [0] = array (
						'year' => 0,
						'month' => 0,
						'day' => 0,
						'sn' => '无',
						'abstract' => '无',
						'expend' => 0,
						'earning' => 0,
						'end_balance' => 0 
				);
			}
			if ($client_info ['real_name']) {
				$user_name = $client_info ['real_name'];
			} else {
				$user_name = $client_info ['user_name'];
			}
			$fields_name = array (
					'年',
					'月',
					'日',
					'订单号',
					'摘要',
					'借方（发货）',
					'贷方（收款）',
					'余额' 
			);
			array_unshift ( $res, $fields_name );
			$table_name = "麦富迪客户{$user_name}从{$time_from_s}至{$time_to_s}的财务详情报表-" . date ( 'YmdHis', time () );
		}
		/* 客户财务汇总报表 */
		if ($_GET ['type'] == 'client_finance_all') {
			$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
			$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : '';
			$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : '';
			$user_ids = ! empty ( $_GET ['user_ids'] ) ? $_GET ['user_ids'] : '';
			$time_from_s = 0; // 起始时间
			$time_to_s = 0; // 截至时间
			if ($time_from > $time_to) {
				$this->show_warning ( '初始时间不能大于截至时间' );
				return;
			}
			$conditions = '1=1 ';
			if ($user_ids) {
				$conditions .= " AND cfd.user_id in ({$user_ids})";
			}
			if ($username && empty ( $user_ids )) {
				
				$member_info = $this->_mem_mod->get ( "serve_type=1 AND (user_name='{$username}' OR phone_mob='{$username}')" );
				if (! $member_info) {
					$this->show_warning ( '该用户不存在' );
					return;
				}
				$user_id = $member_info ['user_id'];
				$conditions .= " AND cfd.user_id={$user_id}";
				$time_flag = 1;
			}
			
			if (! $time_from && ! $time_to) {
			} else {
				$time_from = strtotime ( $time_from );
				$time_to = strtotime ( $time_to );
				$time_from_s = date ( 'Ymd His', $time_from );
				$time_to_s = date ( 'Ymd His', $time_to );
				$time_flag = 1;
				if($time_from){
					$conditions .= " AND cfd.add_time>={$time_from}";
				}
				if($time_to){
					$conditions .= " AND cfd.add_time<={$time_to}";
				}

			}
			// var_dump($conditions);
			$finance_list = $this->_client_finance_mod->find ( array (
					'conditions' => $conditions,
					'join' => 'has_client',
					'fields' => 'cfd.*,member.user_name' 
			) );
			/*
			 * var_dump($finance_list);
			 * var_dump($page);
			 */
			
			/* 借方（支出）expend */
			/* 贷方（收款）earning */
			$array_list = array ();
			/* 冒泡查询 */
			if ($finance_list) {
				$array_list = $this->count_client_finance ( ( array ) $finance_list );
			}
			$res = array ();
			if (is_array ( $array_list )) {
				$i = 0;
				foreach ( $array_list as $k => $v ) {
					$i += 1;
					$res [$i] ['user_name'] = $v ['user_name'];
					if ($time_from_s) {
						$res [$i] ['start_time'] = $time_from_s;
					} else {
						$res [$i] ['start_time'] = date ( 'Ymd His', $v ['add_time'] );
					}
					if ($time_to_s) {
						$res [$i] ['end_time'] = $time_to_s;
					} else {
						$res [$i] ['end_time'] = '至今';
					}
					$res [$i] ['start_balance'] = $v ['start_balance'];
					$res [$i] ['expend'] = $v ['expend'];
					$res [$i] ['earning'] = $v ['earning'];
					$res [$i] ['end_balance'] = $v ['end_balance'];
				}
			} else {
				$this->show_warning('无所需数据，无需导出');
				return ;
			}
			
			$client_finance_list = array ();
			$fields_name = array (
					'客户名',
					'起始时间',
					'截至时间',
					'起初余额',
					'借方（发货）',
					'贷方（收款）',
					'期末余额' 
			);
			array_unshift ( $res, $fields_name );
			$table_name = "麦富迪客户财务汇总报表-" . date ( 'YmdHis', time () );
		}
		/* 创业者收益明细报表 */
		if ($_GET ['type'] == 'estp_earning_all') {
			$username = ! empty ( $_GET ['client_name'] ) ? $_GET ['client_name'] : '';
			$time_from = ! empty ( $_GET ['time_from'] ) ? $_GET ['time_from'] : '';
			$time_to = ! empty ( $_GET ['time_to'] ) ? $_GET ['time_to'] : '';
			$user_ids = ! empty ( $_GET ['user_ids'] ) ? $_GET ['user_ids'] : '';
			$time_from_s = 0; // 起始时间
			$time_to_s = 0; // 截至时间
			if ($time_from > $time_to) {
				$this->show_warning ( '初始时间不能大于截至时间' );
				return;
			}
			$conditions = '1=1 AND cfd.minus=4';
			if ($user_ids) {
				$conditions .= " AND cfd.user_id in ({$user_ids})";
			}
			if ($username && empty ( $user_ids )) {
				
				$member_info = $this->_mem_mod->get ( "serve_type=1 AND (user_name='{$username}' OR phone_mob='{$username}')" );
				if (! $member_info) {
					$this->show_warning ( '该用户不存在' );
					return;
				}
				if ($member_info ['member_lv_id'] <= 1) {
					$this->show_warning ( '该用户不是创业者' );
					return;
				}
				$user_id = $member_info ['user_id'];
				$conditions .= " AND cfd.user_id={$user_id}";
				$time_flag = 1;
			}
			
			if (! $time_from && ! $time_to){
			} else {
				$time_from = strtotime ( $time_from );
				$time_to = strtotime ( $time_to );
				$time_from_s = date ( 'Ymd His', $time_from );
				$time_to_s = date ( 'Ymd His', $time_to );
				$time_flag = 1;
				if($time_from){
					$conditions .= " AND cfd.add_time>={$time_from}";
				}
				if($time_to){
					$conditions .= " AND cfd.add_time<={$time_to}";
				}
				
			}
			// var_dump($conditions);
			$earnings_all_list = $this->_client_finance_mod->find ( array (
					'conditions' => $conditions . " AND member.member_lv_id>1",
					'join' => 'has_client',
					'fields' => 'cfd.*,member.user_name,member.real_name',
					'count' => true 
			) );
			/*
			 * var_dump($finance_list);
			 * var_dump($page);
			 */
			
			/* 借方（支出）expend */
			/* 贷方（收款）earning */
			$array_list = array ();
			$tmp_list = array ();
			/* 冒泡查询 */
			if ($earnings_all_list) {
				$array_list = $this->count_estp_earning ( array_values ( $earnings_all_list ) );
			}
			
			if ($array_list) {
				$all_user_ids = i_array_column ( $array_list, 'user_id' );
				
				$id_range = join ( ',', $all_user_ids );
				if ($id_range) {
					$user_identity = $this->_auth_mod->find ( array (
							'conditions' => "1=1 AND user_id in ({$id_range})",
							'fields' => 'auth.card,auth.status,auth.realname,user_id' 
					) );
				}
				foreach ( $array_list as $key => $value ) {					
					$tmp_list[$key]['start_time']=date('Y-m-d H:i:s',$value['start_time']);
					$tmp_list[$key]['end_time']=date('Y-m-d H:i:s',$value['end_time']);
					$tmp_list[$key]['user_name']=$value['user_name'];
					
					$tmp_list[$key]['real_name']='';
					$tmp_list [$key] ['identity_card'] = '';
					foreach ( $user_identity as $k => $v ) {
						if ($value ['user_id'] == $v ['user_id']) {
							if ($v ['status'] == 1 && $v ['card']) {
								$tmp_list [$key] ['identity_card'] = $v ['card'];
							} else {
								/* 该创业者的认证信息里没有身份证信息或并未审核通过 */
								$tmp_list [$key] ['identity_card'] = "该创业者的认证信息里没有身份证";
							}
							$tmp_name = $v ['realname'];
							break;
						}
					}
					/* 个人认证表里没有该创业者的认证信息 */
					if (! $tmp_list [$key] ['identity_card']) {
						$tmp_list [$key] ['identity_card'] = "没有该创业者的认证信息";
					}
					if ($tmp_list [$key] ['real_name']) {
						$tmp_list [$key] ['real_name'] = $tmp_list [$key] ['real_name'];
					} else if ($tmp_name) {
						$tmp_list [$key] ['real_name'] = $tmp_name;
					} else {
						$tmp_list [$key] ['real_name'] = '无';
					}
					$tmp_list[$key]['earning_money']=$value['earning_money'];
					/* 个税 */
					$tmp_list [$key] ['tax_money'] = $tmp_list [$key] ['earning_money'] / 4;
					/* 价税合计 */
					$tmp_list [$key] ['all_money'] = $tmp_list [$key] ['earning_money'] + $tmp_list [$key] ['tax_money'];
				}
			}
			$res = array ();
			if (is_array ( $tmp_list )) {
				/* $i = 0;
				foreach ( $tmp_list as $k => $v ) {
					$i += 1;
					$res [$i] ['user_name'] = $v ['user_name'];
					if ($time_from_s) {
						$res [$i] ['start_time'] = $time_from_s;
					} else {
						$res [$i] ['start_time'] = date ( 'Ymd His', $v ['add_time'] );
					}
					if ($time_to_s) {
						$res [$i] ['end_time'] = $time_to_s;
					} else {
						$res [$i] ['end_time'] = '至今';
					}
					$res [$i] ['start_balance'] = $v ['start_balance'];
					$res [$i] ['expend'] = $v ['expend'];
					$res [$i] ['earning'] = $v ['earning'];
					$res [$i] ['end_balance'] = $v ['end_balance'];
				} */
				$res=$tmp_list;
			} else {
				$this->show_warning('无收益数据，无需打印');
				return ;
			}
			$client_finance_list = array ();
			$fields_name = array (
					'起始时间',
					'截止时间',
					'用户名',
					'创业者姓名',
					'身份证号',
					'收益净额',
					'个税',
					'价税合计' 
			);
			array_unshift ( $res, $fields_name );
			$table_name = "麦富迪创业者收益汇总报表-" . date ( 'YmdHis', time () );
		}
		$this->export_to_csv ( $res, $table_name, 'gbk' );
	}
}
























