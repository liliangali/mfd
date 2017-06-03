<?php
use Cyteam\Member\Member;
/**
* 用户信息
*/

class MemberApp extends MemberbaseApp
{
    var $_feed_enabled = false;
    function __construct()
    {
        $this->MemberApp();
    }
    function MemberApp()
    {
        
        parent::__construct();
        $ms =& ms();
        $this->real_dir = ROOT_PATH."/upload/auth/";
        $this->web_dir = SITE_URL."/upload/auth/";
    
        $this->_feed_enabled = $ms->feed->feed_enabled();
        $this->assign('feed_enabled', $this->_feed_enabled);
        $this->_lv_mod =& m('memberlv');
        $this->_user_mod =& m('member');
        $this->_auth_mod =& m('auth');
        $this->_order_mod =& m('order');
        $this->_goods_mod =& m('goods');
        $this->_suitlist_mod =& m('suitlist');
        $this->region_mod = &m('region');
        $this->_customer_figure=m('customer_figure');
        $this->_serve=m('serve');
        $this->_figureliangti_mod = &m('figure_liangti');
        $this->_figure_mod = &m('figureorderm');
        $this->_figure_edit=m('figure_edit');
        $this->_detail_mod =& m('detail_comment');
        $this->_debit_mod =& m('debit');
        $this->assign($list1 = $this->region_mod->get_options(2));
        $this->assign('region1',$list1);
        $ms =& ms();
      
    }
    function index()
    {

        $args = $this->get_params();
        /* 清除新短消息缓存 */
        $cache_server =& cache_server();
        $cache_server->delete('new_pm_of_user_' . $this->visitor->get('user_id'));
      
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        //获取会员等级信息-创业者
        $lv_info = $this->_lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
            //'fields' => 'name',
        ));
        // 帐号安全级别 
        $this->level($user['user_id']);
        $this->assign('lv_info', $lv_info);
        //===== Beg liang.li  财富相关的字段 =====
        $user['profit'] = $this->visitor->get("profit");
        $this->assign('weath',$this->weath($user));
        //=====  End liang.li  =====
        
        //ns add 获取推荐商品列表
        $goods_list = $this->_goods_mod->find(array(
            // 'conditions' => "is_sale=1 AND (to_site='pc' || to_site = '0') ORDER BY rand()",
            // 'fields'=>'id,suit_name,image,price',
            'conditions' => 'marketable=1 ORDER BY rand()',
            'fields' => 'goods_id,name,thumbnail_pic,price',
            'limit' => '4',
        ));
        foreach ($goods_list as $k=>$v){
            
           $comment=  $this->_detail_mod->get(array(
                'conditions'=>"comment_id =".$v['goods_id'],
                'fields'    =>"count(*) as num"
            ));
           $goods_list[$k]['comment_num'] = intval($comment['num']);
        }
     
        $this->assign('goods_list', $goods_list);

        //获取订单状态
         $order_about = $this->_order_mod->getOrderabout($user['user_id']);

         $this->assign('order_about',$order_about);
         
         

       //返修订单数
        $order_serve_mod = & m('orderserve');

        $fx_info = $order_serve_mod->get(array(
            'conditions'=>"status>0 and type=1  and user_id='{$user['user_id']}'",
            'fields'=>"count(*) as count",
        ));
        $this->assign('fx_info',$fx_info);


   
        $avatar =  SITE_URL.$info['avatar'];

        $this->assign('avatar', $avatar);
        
        // 麦券张数
        $debit =  $this->_debit_mod->get(array(
            'conditions'=>" user_id='{$user['user_id']}'  ",
            'fields'       => 'count(*) as number',
        ));


        /* 当前位置 */
        $this->assign('debitnum', $debit['number']);
        $this->assign('ac', ACT);
        $this->assign('app', APP);
        /* 当前用户中心菜单 */
        $this->_curitem('overview');
        $this->_config_seo('title','我的麦富迪 - 账户预览');
        $authinfo =$this->_auth_mod->get("user_id=".$user['user_id']);
       
        $this->assign('authinfo', $authinfo);
        $this->display('member.index.html');
    }
    
    /**
    *会员财富相关
    *@author liang.li <1184820705@qq.com>
    *@2015年12月7日
    */
    function weath($user_info) 
    {
        $lv_id = $user_info['member_lv_id'];
        $user_id = $user_info['user_id'];
        //=====  总业绩  =====
        $order_mod = m('order');
        $debit_mod = m('debit');
        $special_code_mod = m('special_code');
        $invite_id_arr = array();
        $conditions_debit = "user_id = $user_id";
        $conditions_special_code = "to_id = $user_id";
        if ($lv_id > 1) 
        {
            $member_invite = m('memberinvite');
            $conditions = "inviter = $user_id AND type=0";
            $list = $member_invite->find(array(
                'fields'        => "*",
                'conditions'    => $conditions,
            ));
            if ($list) 
            {
                $invite_id_arr = i_array_column($list, 'invitee');
            }
            $conditions_debit = "from_uid = $user_id AND user_id != 0 ";//=====  已经赠送出去的优惠券数量  =====
            $conditions_special_code = "from_id = $user_id AND to_id != 0 ";//=====  已经赠送出去的优惠券数量  =====
        }
        $invite_id_arr[] = $user_id;
        $user_id_str = db_create_in($invite_id_arr,'user_id');
        $conditions = " AND status NOT IN (".ORDER_PENDING.",".ORDER_CANCELED.")";
        //=====  总业绩  =====
        $order_info = $order_mod->get(array(
            'conditions' => $user_id_str.$conditions,
            'fields'   => "sum(order_amount) as total",
        ));
        $weath['order_amount'] = $order_info['total'];

        //=====   订单数量  =====
        $order_num_info = $order_mod->get(array(
            'conditions' => "user_id = $user_id".$conditions,
            'fields'     => "count(*) as num",
        ));
        $weath['order_num'] = $order_num_info['num'];
     
        //=====  抵用券个数  =====
        $debit_info = $debit_mod->get(array(
            'conditions' => $conditions_debit,
            'fields'     => "count(*) as num ,sum(money) as money",
        ));
        $weath['debit_num'] = $debit_info['num'];
        $weath['debit_money']     = $debit_info['money'];
        $weath['invite_num'] = count($invite_id_arr) - 1;
        
        //=====  已发送酷卡个数  =====
        $special_code_info = $special_code_mod->get(array(
            'conditions' => $conditions_special_code,
            'fields'     => "count(*) as num ,sum(work_num) as money",
        ));
        $weath['s_num'] = $special_code_info['num'];
        $weath['s_money'] = $special_code_info['money'];
        return $weath;
    }

    //获取商品列表-随即4个
    function getGoodsList(){
        $goods_mod = m('goods');
        $list = $goods_mod->find(array(
            'conditions' => 'marketable=1 ORDER BY rand()',
            'fields' => 'goods_id,name,thumbnail_pic as small_pic,price',
            'limit' => '4',
        ));
        if($list){
            $this->json_result($list);
        }else{
            $this->json_error('无商品可获取！');
        }
    }
    
    /**
    *账户余额
    *@author liang.li <1184820705@qq.com>
    *@2015年8月27日
    */
    function money() 
    {   
		$userInfo = $this->visitor->get();
        $args = $this->get_params();
        $minus = !$args[0] ? 0 : $args[0];
        $params = array('args'=>$args,'pagekey'=>1);
		$page    =   $this->_get_page(10,$params);
		//提现状态数据
		$cash_status = array(
		    '0' => '提现中',
		    '1' => '提现成功',
		    '2' => '提现失败',
		);
		
		if($minus != 5) {
			$contidions = "type = 4 AND user_id = {$userInfo['user_id']}";
			if ($minus) {//收支明细
				$contidions .= " and minus = {$minus}";
			}
		
			$order_cash_log_mod = m('ordercashlog');
			$cash_list = $order_cash_log_mod->findAll(array(
				'conditions' => $contidions,
				'fields'     => "id, name, mark, add_time, cash_money",
				'limit'      => $page['limit'],
				'order'      => 'id desc',
				'count'      => true,
				'index_key'  => '',
			));
			$page['item_count']= $order_cash_log_mod->getCount();
		} else {
	
			//获得提现记录cash
			$cash_mod = m('cash');
			$cash_list = $cash_mod->findAll(array(
				'conditions' => "user_id = {$userInfo['user_id']}",
				'fields'     => "id, msg, create_time, cash_money, status",
				'limit'      => $page['limit'],
				'order'      => 'id desc',
				'count'      => true,
				'index_key'  => '',
			));
			if ($cash_list) 
			{
			    foreach ($cash_list as $key => &$value) 
			    {
			        $value['status'] = $cash_status[$value['status']];
			    }
			}

			$page['item_count']= $cash_mod->getCount();
		}
	
       $this->_format_page($page, 10, $params);
		$authinfo =$this->_auth_mod->get("user_id=".$userInfo['user_id']);
		$this->assign('authinfo', $authinfo);
		$this->_config_seo('title', '账户余额');
		$this->assign('act', 'log');
		$this->assign('page_info', $page);
		$this->assign('minus', $minus);
		$this->assign('cash_status', $cash_status);
        $this->assign("userInfo", $userInfo);
		$this->assign("cash_list", $cash_list);
		$this->assign('act',ACT);
        $this->display('member.money.html');
    }
	
	/**
    *用户提现
    *@author xuganglong <781110641@qq.com>
    *@2015年8月27日
    */
    function put_cash() 
    {   
		$user_id = $this->visitor->get('user_id');
		$cash_mod    = m('cash');
		$member_bank = m('member_bank');
		$auth_mod    = m('auth');
		$mMem        = &m('member');
		$user_info = $mMem->get($user_id);
		
		//=====  只有实名认证的用户才能提现  =====
		$auth_mod   = m('auth');
		$authinfo   = $auth_mod->get("user_id=".$user_info['user_id']);
		
		if (empty($authinfo)) 
		{
		    $this->show_warning('尚未实名认证，暂不能提现。');
		    return;
		} 
		else 
		{
		    if ($authinfo['status']==0) 
		    {
		        $this->show_warning('实名认证审核中，暂不能提现。');
		        return;
		    }
		    elseif($authinfo['status'] == 2) 
		    {
		        $this->show_warning('实名认证失败，暂不能提现。');
		        return;
		    }
		}
		
		
		if (!IS_POST) {
			//获取认证用户的信息
			$cash_exist = $cash_mod->findAll(array(
				'conditions' => "user_id = {$user_info['user_id']} AND status = 0",
				'fields'     => 'id',
				'index_key'	 => '',
			));
			
			if($cash_exist) 
			{//已有提现申请; 正在提现
				$this->show_warning('您已有提现申请正在审核！不能重复申请');
				return;
			}
			$card_name = $authinfo['realname'];

			//返回当前用户已存银行卡信息
			$owner_bank = $member_bank->get(array(
				'conditions' => "user_id = {$user_info['user_id']} AND status=1  group by bank_card",
				'index_key'	 => '',
			));
			//银行卡处理***
			$length = strlen($authinfo['bankcard']);	
			$authinfo['bank_card'] = substr_replace($authinfo['bankcard'], '**************', 4, $length-8);
			$this->_config_seo('title', Conf::get('site_title').'账户余额提现');

			$this->assign("user_info", $user_info);
			$this->assign("owner_bank", $authinfo);
			$this->assign("card_name", $card_name);
			$this->display('member.cash.html');
			
		}
		 else 
		 {
			$bank_id     = isset($_POST['bank_id']) ? $_POST['bank_id'] : 0;
			$cash_money  = isset($_POST['cash_money']) ? $_POST['cash_money'] : 0;
			$paymentPwd  = isset($_POST['paymentPwd']) ? $_POST['paymentPwd'] : 0;
            
			//判断次用户是否已认证，认证后方可提现
			if(empty($user_info['df_bankcard'])) 
			{
				$this->show_warning('您还未绑定银行卡，暂时不能提现！');
				return;
			}
	
			//判断次用户是否已设置支付密码
			if(empty($user_info['pay_password'])) 
			{
				$this->show_warning('尚未设置支付密码，暂不能提现');
				return;
			}
			
			//判断支付密码是否正确
			if(md5($paymentPwd) != $user_info['pay_password'] ) 
			{
				$this->show_warning('支付密码错误');
				return;
			}
			
			//根据选择银行卡id，获得银行信息
			$selectbank  = $member_bank->get($bank_id);
			if (!$selectbank) 
			{
				$this->show_warning('请选择银行卡');
				return;
			}

			$data['bank_address'] = $selectbank['bank_address'];
			$data['bank_id']      = $selectbank['bank_id'];
			$data['bank_card']    = $selectbank['bank_card'];

			//验证提现金额
			if (!$cash_money || $cash_money < 0) {
				$this->show_warning('提现金额必须为正整数');
				return;
			}
			if ($cash_money > $user_info['money']) 
			{
				$this->show_warning('你的可用资金不足！请重新提交');
				return;
			}
			$data['cash_money']  = $cash_money;
			$data['user_info']     = $user_info;
			$data['create_time'] = time();
			$cash_mod    = m('cash');
			$transaction = $cash_mod->beginTransaction();
			$res = $cash_mod->submit($data);
        	if (!$res['code']) 
        	{
        		$cash_mod->rollback();
        		$this->show_warning($res['msg']);
        		return;
        	}
        	$cash_mod->commit($transaction);
			//发后后台消息
			import("notice.lib");
			$msg = new Notice();
			$msg->send(array(
				"content" => "创业者-提现申请(ID-".$cash_id."),<a href=\"http://www.dev.mfd.cn/admin/index.php?app=cash&act=edit&id=".$cash_id."\">查看详情</a>",
				'node'    => 'real_auth',
			));
			
			$this->show_message('成功提交提现申请,款项预计3~5个工作日到账','go_back', 'member-money.html');
		}
		
    }
	
    /**
     *ajax获得三级联动
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function get_region()
    {

        $region_mod = m('region');
        $pid = $_POST['pid'];
        if (!$pid)
        {
            $this->json_error('失败');
        }

        $list = $region_mod->get_options_html($pid,0);
        $this->json_result($list);

    }
    
    /**
    *我的积分 
    *@author liang.li <1184820705@qq.com>
    *@2015年8月27日
    */
    function point() 
    {
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $order_cash_log_mod = m('ordercashlog');
        $page   =   $this->_get_page(10);
        $list = $order_cash_log_mod->find(array(
            'conditions' => "user_id = {$user['user_id']} AND type = 1 ",
            'limit'         => $page['limit'],  //获取当前页的数据
            'count'         => true,
            'order'         => "id DESC",
        ));
        $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign('list',$list);
        $this->assign('page_info', $page);
        $this->assign('user', $user);
        $this->assign('act',ACT);
        $this->_config_seo('title','我的麦富迪 - 我的积分');
        $this->display("member.point.html");
    }
    
    /**
    *mfd币
    *@author liang.li <1184820705@qq.com>
    *@2015年9月8日
    */
    function coin() 
    {
        $search_arr= array(
            '1' => "最近三个月的记录",
            '2' => "最近一个月的记录",
            '3' => "最近一周的记录",
            '4' => "最近三天的记录",
        );
        $search = isset($_GET['search']) ? $_GET['search'] : 1;
        //         echo $search   ;exit;
        if ($search == 1)
        {
            $limit_time = 90*24*60*60;
        }
        elseif ($search == 2)
        {
            $limit_time = 30*24*60*60;
        }
        elseif ($search == 3)
        {
            $limit_time = 7*24*60*60;
        }
        elseif ($search == 4)
        {
            $limit_time = 3*24*60*60;
        }
        else
        {
            $this->show_warning('参数错误');
            return ;
        }
        $l_time = gmtime() - $limit_time;
        
        
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $order_cash_log_mod = m('ordercashlog');
        $page   =   $this->_get_page(10);
        $list = $order_cash_log_mod->find(array(
            'conditions' => "user_id = {$user['user_id']} AND type = 2 AND add_time > $l_time",
            'limit'         => $page['limit'],  //获取当前页的数据
            'count'         => true,
            'order'         => "id DESC",
        ));
        if ($list)
        {
            foreach ($list as $key => &$value)
            {
                $value['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
            }
        }
        
        $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
        $this->_format_page($page);
        
        $article_mod = m('article');
        $coinhuoqu   = "";
        $coinshiyong = "";
        $coinjieshao = "";
        $coinhuoqu_arr = $article_mod->get("code='coinhuoqu'");
        $coinshiyong_arr = $article_mod->get("code='coinshiyong'");
        $coinjieshao_arr = $article_mod->get("code='coinjieshao'");
        if ($coinhuoqu_arr) 
        {
            $coinhuoqu = $coinhuoqu_arr['content'];
        }
        if ($coinshiyong_arr)
        {
            $coinshiyong = $coinshiyong_arr['content'];
        }
        if ($coinjieshao_arr)
        {
            $coinjieshao = $coinjieshao_arr['content'];
        }
        
        $this->assign('coinhuoqu',$coinhuoqu);
        $this->assign('coinshiyong',$coinshiyong);
        $this->assign('coinjieshao',$coinjieshao);
        $this->_config_seo('title', '我的麦富迪币');
        $this->assign('search',$search_arr);
        $this->assign('list',$list);
        $this->assign('page_info', $page);
        $this->assign('user', $user);
        $this->assign('act',ACT);
        $this->display("member.coin.html");
    }
    
    /**
     *收益
     *@author liang.li <1184820705@qq.com>
     *@2015年9月8日
     */
    function profit()
    {
        $search_arr= array(
            '1' => "最近三个月的记录",
            '2' => "三个月以前",
            
        );
        $search = isset($_GET['search']) ? $_GET['search'] : 1;
        $this->assign('search_id',$search);
        $limit_time = 90*24*60*60;
        $l_time = gmtime() - $limit_time;
        if ($search == 1)
        {
           $s_conditons = "add_time > ".$l_time;
        }
        elseif ($search == 2)
        {
            $s_conditons = "add_time < ".$l_time;
        }
        else
        {
            $this->show_warning('参数错误');
            return ;
        }
        
    
    
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $order_cash_log_mod = m('ordercashlog');
        $order_mod = m('order');
        $page   =   $this->_get_page(10);
        $list = $order_cash_log_mod->find(array(
            'conditions' => "user_id = {$user['user_id']} AND type = 0 AND minus=0 AND turnout=0  AND ".$s_conditons,
            'limit'         => $page['limit'],  //获取当前页的数据
            'count'         => true,
            'order'         => "id DESC",
        ));
        $order_id_arr = i_array_column($list, "order_id");//=====  查询所有订单  =====
        if ($list)
        {
            if ($order_id_arr) 
            {
                $order_ids = implode(",", $order_id_arr);
            }
            $order_list = $order_mod->find(array(
                'conditions' => "order_id IN($order_ids)",
            ));
            
            foreach ($list as $key => &$value)
            {
                $value['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
                $value['user_name'] = $order_list[$value['order_id']]['user_name'];
            }
        }
    
        $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->_config_seo('title', '我的收益');
        $this->assign('search',$search_arr);
        $this->assign('list',$list);
        $this->assign('page_info', $page);
        $this->assign('user', $info);
        $this->assign('act',ACT);
        $this->display("member.profit.html");
    }
    
   
    
    /**
    *转入余额 
    *@author liang.li <1184820705@qq.com>
    *@2015年9月8日
    */
    function profit_into() 
    {
        $this->json_error('收益转余额功能暂未开通，敬请继续关注');
        return;
        $user = $this->visitor->get();
        $id = isset($_GET['id']) ? $_GET['id'] : 0;//=====  0表示一键转入 非0代表order_cash_log表对应的主键id   =====
        $ajax = isset($_GET['ajax']) ? $_GET['ajax'] : 0;
        $into_type = $_GET['into_type'];
        $order_cash_mod = m('ordercashlog');
        $m_mod = m('member');
        $user_id = $user['user_id'];
        
        $transaction = $m_mod->beginTransaction();//=====  开启事物  =====
        $user_info = $m_mod->get_info($user_id);
        if($user_info['profit'] <= 0)
        {
            $m_mod->rollback();
            $this->json_error('收益不足');
            return;
        }
        
        
        if ($into_type == 1) //=====   余额  =====
        {
            $turnout = 2;
        }
        else 
        {
            $this->json_error('参数错误');
            return;
        }
        
        if ($id) 
        {
            $order_cash_item = $order_cash_mod->get_info($id);
            if (!$order_cash_item)
            {
                $m_mod->rollback();
                $this->json_error('无此数据');
                return;
            }
            if ($order_cash_item['change_into'] == 1)
            {
                $m_mod->rollback();
                $this->json_error('此数据已经转入');
                return;
            }
            if ($order_cash_item['minus'] != 0)
            {
                $m_mod->rollback();
                $this->json_error('数据校验失败');
                return;
            } 
            $order_cash_mod->edit($id,array('turnout'=>$turnout));
            $profit = $order_cash_item['cash_money']; //=====  需要扣除的收益  =====
        }
        else 
        {
            $order_cash_mod->edit("user_id = $user_id AND type=0 AND minus=0 AND mark='+' ",array('turnout'=>$turnout));
            $profit = $user_info['profit']; //=====  需要扣除的收益 全部转入  =====
        }
        
        if (!in_array($into_type, array(1))) //=====  验证转入类型(余额或者麦富迪币 )  =====
        {
            $m_mod->rollback();
            $this->json_error('参数错误');
            return;
        } 
        
        if ($into_type == 1) //=====  转入余额   =====
        {
            $type_name = "余额";
            $coin = 0;//=====  所得麦富迪币（税不再转成麦富迪比）   =====
            $money = $profit - $profit * (ORDER_TAX/100); //=====  所得余额 扣除20%的税 =====
        }
        else //=====  转入麦富迪币  =====
        {
            $type_name = "麦富迪币";
            $coin = $profit;//=====  所得麦富迪币   =====
            $money = 0; //=====  所得余额  =====
        }
       
       
        $order_sn = $this->gen_ident(); //=====  唯一编号  =====
        $time = gmtime();
      
        if ($profit > $user_info['profit'])
        {
            $m_mod->rollback();
            $this->json_error('收益不足');
            return;
        }
        $f_id = "";
        $_data_coin['name'] = '收益转麦富迪币获得麦富迪币';
        //=====  余额log  =====
        if ($money)
        {
            $_data_money['add_time'] = $time;
            $_data_money['name'] = '收益转余额获得余额';
            $_data_money['type'] = 4;
            $_data_money['mark'] = '+';
            $_data_money['minus'] = 6;
            $_data_money['share'] = ORDER_TAX;
            $_data_money['user_id'] = $user_id;
            $_data_money['cash_money'] = $money;
            $_data_money['order_money'] = $profit;
            $_data_money['order_sn'] = $order_sn;
            $id = $order_cash_mod->add($_data_money);
            $f_id .= $id.",";
            if (!$id)
            {
                $m_mod->rollback();
                $this->json_error('余额log记录失败');
                return;
            }
             
            if(!$m_mod->setInc($user_id, 'money', $money))
            {
                $m_mod->rollback();
                $this->json_error('加余额失败');
                return;
            }
            $_data_coin['name'] = '收益转余额获得麦富迪币';
        }
        
        
         
        //=====  mfd币log  =====
        $_data_coin['add_time'] = $time;
        $_data_coin['mark'] = '+';
        $_data_coin['type'] = 2;
        $_data_coin['minus'] = 5;
        $_data_coin['share'] = ORDER_TAX;
        $_data_coin['user_id'] = $user_id;
        $_data_coin['cash_money'] = $coin;
        $_data_coin['order_money'] = $profit;
        $_data_coin['order_sn'] = $order_sn;
        if ($coin) 
        {
            if (!$order_cash_mod->add($_data_coin))
            {
                $m_mod->rollback();
                $this->json_error('mfd币log记录失败');
                return;
            }
        }
        
         
        //=====  收益log  =====
        $_data_profit['add_time'] = $time;
        $_data_profit['name'] = '收益转入'.$type_name.',扣除对应收益。';
        $_data_profit['type'] = 0;
        $_data_profit['mark'] = '-';
        $_data_profit['minus'] = 1;
        $_data_profit['share'] = ORDER_TAX;
        $_data_profit['user_id'] = $user_id;
        $_data_profit['cash_money'] = $profit;
        $_data_profit['order_money'] = $profit;
        $_data_profit['order_sn'] = $order_sn;
        $id = $order_cash_mod->add($_data_profit);
        $f_id .= $id.",";
        if (!$id)
        {
            $m_mod->rollback();
            $this->json_error('收益log记录失败');
            return;
        }
         
        if(!$m_mod->setDec($user_id, 'profit', $profit))
        {
            $m_mod->rollback();
            $this->json_error('扣除收益失败');
            return;
        }
        if ($coin) 
        {
            if(!$m_mod->setInc($user_id, 'coin', $coin))
            {
                $m_mod->rollback();
                $this->json_error('添加mfd币失败');
                return;
            }
        }
        $new_user_info = $m_mod->get_info($user_id);
        $m_mod->commit($transaction);
        
        get_client_finance($user_id,'',4,$money);
        $this->json_result($new_user_info['profit']);
    }
    
    function gen_ident($id = 0){
        $cart_mod = m('ordercashlog');
        do{
            $str='abcdefghigklmnopqrstuvwxyz0123456789';
            $code ='';
            for($i=0;$i<5; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = '0'.$id;
            }
            $ident =  $code.$id;
        }
        while($cart_mod->get("order_sn = '{$ident}'"));
        return $ident;
    }
    /*
	*激活麦券
	@author shaozz
	  2016.12.27
	*/
	function activation()
	{
		$debit_mod=&m("voucher");
		$batch_mod=&m("voucher_batch");
			 $actnum = isset($_POST['code']) ? strtoupper($_POST['code']) : 0;//激活码
		     $user = $this->visitor->get();
             $user_info = $this->_user_mod->get_info($user['user_id']);

			if (!$user_info)
			{
				
				$this->json_error('账号不存在');
				return;
			}
			if(!$_POST){
				$this->assign('ac','activation');
	             $this->display('member.activation.html');	
               }else{
				  
	              if (!$actnum )
			     {
				   $this->json_error('激活码不存在或已失效！');
				   return;
			     }
			
			      if(strlen($actnum) != 10){
			        $this->json_error('激活码格式错误');
			        return;
			      }
			$time=time();
			$debit=$debit_mod->get(array(
		    'conditions' =>"code='{$actnum}' AND use_status=0 AND start_time < '{$time}' AND source= 1  AND  '{$time}' < end_time  AND binding_user_id=0",	
			));
			if(!$debit){
			    $this->json_error('券不存在或已失效');
			    return;
			}
			if($debit['batch_id']){
			    $debit1=$batch_mod->get(array(
			    'conditions' =>" status=1 AND id='{$debit['batch_id']}'",
			    ));
			}
	
			if(!$debit1){
				$this->json_error('券不存在或已失效');
				return;
			}
				
			$datt=array(
                  'binding_user_id'=>$user_info['user_id'],	
				  'binding_time'=>time(),
				  'binding_username'=>$user_info['user_name'],
				  'source'=>1,
			);
			    
          if(!$debit_mod->edit("code='{$actnum}'",$datt)){
		      $this->json_error('券激活失败');
	       }

         $this->json_result();
            }
			
	}
	
	
    /**
    *我的麦券
    *@author tangsj <963830611@qq.com>
    *@2016年5月18日
    */
    function debit() 
    {
		
        $cate = $this->_get_options();
        $args = $this->get_params();
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $this->assign('type',$type);
        
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $this->formatDebit($user['user_id']);
        $num_list = $this->debitNum($user);
     
        $lv_info = $this->_lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
            //'fields' => 'name',
        ));
        
        $conditons = "binding_user_id = {$user['user_id']} ";
        
        $time=time();
        if ($type == 0) 
        {
            $conditons .= "AND use_status = 0 AND end_time > $time ";
        }
        elseif ($type == 1)
        {
            $conditons .= "AND use_status = 1 ";
        }
        elseif ($type == 2)
        {
            $conditons .= "AND end_time < $time ";
        }
        else 
        {
            $this->show_warning('参数错误');
            return;
        }
        $page   =   $this->_get_page(10);    //获取分页信息
       // $debit_mod = m('debit');
		$debit_mod=&m("voucher");
		
        $list = $debit_mod->find(array(
            'conditions' => $conditons,
            'limit'         => $page['limit'],  //获取当前页的数据
            'count'         => true,
            'order'         => "id DESC",  
        ));
	
        $page['item_count'] = $debit_mod->getCount();   //获取统计的数据
        $this->_format_page($page);
        
        //=====   格式化时间  =====
        $user_ids = array();
     
        
        if ($list) 
        {
            foreach ($list as $key => &$value) 
            {
                $datetime=$value['end_time']-$time;
			
               
                $list[$key]['datetime']=$datetimes;
                $value['id'] = ApiAuthcode($value['id'],'ENCODE');
                $value['start_time'] = date("Y/m/d",$value['start_time']);
                 $value['end_time'] = date("Y/m/d",$value['end_time']);
           if($datetime>0){

			    $datetimes=ceil($datetime/3600/24);
		   }else{

			    $datetimes='';
		   }

				if($value['category']=='1'){
					 $value['category'] = '限定制商品使用';
				}elseif($value['category']=='2'){
					$value['category'] = '限普通商品使用';
				}else{
					$value['category'] = '通用';
				}
               
                
                
            }
          
        }
       
        //=====   获取如何使用的说明  =====
        $article = m('article');
        $a_info = $article->get("code='麦券使用说明'");
        $content = "";
        if ($a_info) 
        {
            $content = $a_info['content'];
        }
      
        $this->assign('article_info',$content);       
        $this->_config_seo('title', '我的麦券');
        $this->assign('type',$type);
        $this->assign('list',$list);
        $this->assign('page_info', $page);
        $this->assign("num_list",$num_list);
        $this->assign('user', $user);
        $this->assign('act',ACT);
        $this->display('member.debit.html');
    }
    
    /**
    *赠送劵下我的顾客选择
    *@author liang.li <1184820705@qq.com>
    *@2015年9月17日
    */
    function debit_cus() 
    {
        
        $user = $this->visitor->get();
        if ($user['member_lv_id'] <= 1)
        {
            $this->show_warning('只有创业者才可以送券');
            return ;
        }
        $member_invite = m('memberinvite');
        $debit_mod = m('debit');
        if (IS_POST) 
        {
            $id = $_POST['id'];
            $d_id = ApiAuthcode($_POST['d_id']);
            $user_info = $this->_user_mod->get_info($id);
            if (!$user_info) 
            {
                $this->json_error('此用户不存在');
                return ;
            }
         
            //=====  获取创业者下面的消费者  =====
            $invite_info = $member_invite->get("inviter={$user['user_id']} AND invitee=$id");
            if (!$invite_info) 
            {
                $this->json_error('此赠送关系不存在');
                return ;
            }
            $debit_info = $debit_mod->get_info($d_id);
            if ($debit_info['from_uid'] != $user['user_id'] || $debit_info['user_id'] != 0) 
            {
                $this->json_error('非法操作');
                return ;
            }
            
            if (!$debit_mod->edit($d_id,array('user_id'=>$id)))
            {
                $this->json_error('转赠失败');
                return ;
            }
            $this->json_result();
        }
        else 
        {
            $keyword = $_GET['keyword'];
            $arg = $this->get_params();
            $this->assign('d_id',$arg[0]);
            
            $conditions = "inviter = {$user['user_id']} AND type=0";
            if ($keyword) 
            {
                $conditions .= " AND (member.user_name like '%$keyword%' OR member.nickname like '%$keyword%') ";
                $this->assign('keyword',$keyword);
            }
            $page   =   $this->_get_page(5);    //获取分页信息
            $list = $member_invite->find(array(
                'fields'        => "memberinvite.*,member.user_name,member.nickname",
                'conditions'    => $conditions,
                'limit'         => $page['limit'],  //获取当前页的数据
                'join'          => 'belongs_to_user',
                'count'         => true,
                'order'         => "id DESC",
                'index_key'     => "invitee"
            ));
            if ($list) //=====  获得已赠送和已使用的券的总数目  =====
            {
                require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
                $objAvatar = new Avatar();
                foreach ($list as $key => &$value) 
                {
                    if (!$value['invitee']) 
                    {
                        unset($list[$key]);
                        continue;
                    }
                    $avatar = $objAvatar->avatar_show($value['invitee'],'big');
                    $list[$key]['avatar'] = $avatar;
                    $list[$key]['total_money'] = '0.00';
                    $list[$key]['total_money_noused'] = '0.00';
                }
                $user_ids = i_array_column($list, 'invitee');
                if ($user_ids)
                {
                    $user_ids_arr = implode(",", $user_ids);
                    $debit_list = $debit_mod->find(array(
                        'conditions' => "user_id IN ($user_ids_arr) AND from_uid={$user['user_id']}",
                    ));
                    if ($debit_list)
                    {
                        foreach ($debit_list as $key => $value)
                        {
                            $list[$value['user_id']]['total_money'] += $value['money'];//=====  总券的额度  =====
                            if (!$value['is_used']) //=====  未使用的券的总额  =====
                            {
                                $list[$value['user_id']]['total_money_noused'] += $value['money'];
                            }
                        }
                    }
                }
            }
            
            $page['item_count'] = $member_invite->getCount();   //获取统计的数据
            $this->_format_page($page);
            $this->assign('list',$list);
            $this->assign('page_info', $page);
            $this->assign('user', $user);
            $this->assign('act','debit');
            $this->display('member.cus.html');
        }
    }
    
    /**
     *抵用券个数
     *@author tangsj <963830611@qq.com>
     *@2016年5月18日
     */
    function debitNum($user)
    {
        $user_id = $user['user_id'];
        $debit_mod = m('voucher');
        $this->formatDebit($user_id);
        $times=time();
        $conditions1 = "binding_user_id = $user_id  AND use_status = 0 AND end_time > $times";
        $conditions2 = "binding_user_id = $user_id  AND use_status = 1";
        $conditions3 = "binding_user_id = $user_id  AND end_time < $times";
        
        $notUse = $debit_mod->get(array(
            'conditions' => $conditions1,
            'fields' => "count(*) as num",
        ));
         
        $haveUsed = $debit_mod->get(array(
            'conditions' => $conditions2,
            'fields' => "count(*) as num",
        ));
         
        $haveInvalid = $debit_mod->get(array(
            'conditions' => $conditions3,
            'fields' => "count(*) as num",
        ));
//  echo $conditions3;exit;        
        $list['notUse'] = $notUse['num'];
        $list['haveUsed'] = $haveUsed['num'];
        $list['haveInvalid'] = $haveInvalid['num'];
// print_exit($list);
        return $list;
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
    
        $gcategories = $_cate_mod->get_list();
        $tree =& $this->_tree($gcategories);
    
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
    
    /**
     *格式化抵用券已经使用的
     *@author liang.li <1184820705@qq.com>
     *@2015年7月17日
     */
    function formatDebit($user_id)
    {
        
        $time = time();
        $debit_mod = m('debit');
        $debit_list = $debit_mod->find(array(
            'conditions' => "user_id = $user_id AND is_invalid = 0 AND is_used = 0",
        ));
         
//     print_exit($debit_list);     
        if ($debit_list)
        {
            foreach ($debit_list as $key => $value)
            {
                if ($value['expire_time'] < $time)
                {
                    $debit_mod->edit($value['id'],array('is_invalid' => 1));
                }
            }
        }
    }

    /**
     * 找回密码
     * @return void
     * @access public
     * @see register
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function find_password(){
        $opt = $_REQUEST['opt'];
    
        //缺省值 显示找回密码页面
        if($opt){
            $type = $_REQUEST['account_type'];
            if(!$type)
            {
                $this->msg('找回密码类型为空');
            }
    
            //此处为了融合处理
            $account = ($_REQUEST['account']) ? $_REQUEST['account'] :$_REQUEST['email'] ;
            if(!$account)
            {
                $this->msg('手机号或者邮箱号为空');
            }
    
            //验证手机验证码;发送找回密码邮件
            if($opt == 2){
                if('phone' == $type){
                    $code = $_REQUEST['authcode'];
                    $ms =& ms();
                    if ($ms->user->check_username($account))
                    {
                        return '该手机号并未绑定任何账号';
                    }
    
                    $sms_reg_tmp_mod =& m('sms_reg_tmp');
                    $conditions = array('conditions'=>" category = 'findps' and type = 'findps' and phone = '$account' and code='$code' for update ");
                    $sms_log = $sms_reg_tmp_mod->get($conditions);
    
                    if(!$sms_log['id'])
                    {
                        $this->msg('并未发送短信给您...');
                    }
    
                    /* if($sms['fail_time'] < time())
                     {
                     $this->msg('验证码已过期!');
                     } */
                    $this->assign('data',$_POST);
                    $content = $this->_view->fetch('member.findps-reset.html');
                    $this->json_result(array('content'=>$content));
                }else{
                    if ($_SESSION['findps'] != strtolower($_POST['authcode']))
                    {
                        $this->msg("验证码错误");
                    }
    
                    $ms =& ms();
                    if ($ms->user->check_username($account))
                    {
                        return '该邮箱并未绑定任何账号';
                    }
    
                    //发送邮件
                    $res = emailcode($account,'findps','findps','pc','get');
                    if ($res['err'])
                    {
                        $this->json_error('邮件服务器繁忙!');
                        return;
                    }else{
                        unset($_SESSION['findps']);
                        $this->assign('email',getHideEmail($account));
                        $this->assign('emailurl',getEmailHref($account));
                        $this->assign('t',EMAIL_FAIL_TIME);
                        $this->assign('data',$_POST);
                        $content = $this->_view->fetch('member.findps-email-tips.html');
                        $this->json_result(array('content'=>$content));
                    }
                }
    
                //手机找回密码执行修改;邮箱找回密码外链验证与执行密码修改
            }elseif($opt == 3){
    
                $code = $_REQUEST['code'];
                $ps =  empty($_REQUEST['ps']) ? 0 : $_REQUEST['ps'];
    
                if(!$code)
                {
                    $this->msg('验证码为空');
                }
    
                $model_member =& m('member');
                $info = $model_member->get("user_name='{$account}' or phone_mob = '$account' or email = '$account' ");
                if (!$info)
                {
                    return '请先注册成为会员!';
                }
    
                if('phone' == $type){
    
                    $sms_reg_tmp_mod =& m('sms_reg_tmp');
                    $conditions = array('conditions'=>" category = 'findps' and type = 'findps' and phone = '$account' and code='$code' for update ");
                    $sms_log = $sms_reg_tmp_mod->get($conditions);
    
                    if(!$sms_log['id'])
                    {
                        $this->msg('并未发送短信给您...');
                    }
    
                    /* if($sms['fail_time'] < time())
                     {
                     $this->msg('验证码已过期!');
                     } */
    
                }else{//外链邮箱找回密码验证;通过邮箱修改密码 ps:和手机找回密码 部分code可以整合
    
                    $rs = authemail($account,$code);
                    if(!$rs)
                    {
                        $this->msg('邮箱验证码错误');
                    }
    
                    if (!check::isEmail($account))
                    {
                        $this->msg('邮箱格式错误');
                    }
    
    
                    $email_code_mod =& m('email_code');
                    $conditions = array('conditions'=>" category = 'findps' and type = 'findps' and email = '$account' and code='$code'");
                    $email_log = $email_code_mod->get($conditions);
    
                    if(!$email_log['id'])
                    {
                        $this->msg('并未发送邮件给您...');
                    }
    
                    /* if($email_log['fail_time'] < time())
                     {
                     $this->msg('邮件已失效!');
                     } */
    
    
                    if (!$ps)
                    {
                        $_REQUEST['account'] = $_REQUEST['email'] ;
                        $_REQUEST['account_type'] = 'email';
                        $this->assign('data',$_REQUEST);
                        //刷大页面
                        $this->display('member.findps-reset-email.html');
    
                    }
                }
    
                /* 修改密码 */
                if ($ps)
                {
                    $passlen = strlen($ps);
                    if ($passlen < 6 || $passlen > 20)
                    {
                        $this->msg('6-20个大小写英文字母、符号或数字');
                    }
    
                 
                    /* 修改密码 */
                    $ms =& ms();    //连接用户系统
                    $info = $ms->user->get($_REQUEST['account'], true);

                    $result = $ms->user->edit($info['user_id'], '', array('password'  => $ps),1);
                    if (!$result)
                    {
                        /* 修改不成功，显示原因 */
                        $this->msg($ms->user->get_error());
                    }
    
                    $content = $this->_view->fetch('member.findps-success.html');
                    $this->json_result(array('content'=>$content));
    
                }
    
            }else{
                $this->msg('状态码错误...');
            }
        }else{
            $this->_config_seo('title', Lang::get('find_password') . ' - ' . Conf::get('site_title'));
            $this->display('member.findps.html');
        }
    }
    /**
     * ajax 错误输出
     * @return void
     * @access public
     * @see register
     * @version 1.0.0 (2014-12-15)
     * @author Xiao5
     */
    function msg($msg){
//         echo $msg;
        $this->json_error($msg);
        exit();
    }


    /**
     * 注册一个新用户
     * @return void
     * @access public
     * @see register
     * @version 1.0.0 (2016-06-03)
     * @author tangsj
     */
    function register()
    {
        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');
    
            return;
        }
        if(isset($_SESSION['step2']) && isset($_POST['step2'])) {
    
            //注册的最后一步(手机注册)
            $check_step3 = $this->formStep3();
            if ($check_step3['err'])
            {
                $this->json_error($check_step3['msg']);
                return;
            }
    
            /*会员默认等级*/
            $member_lv_mod =& m('memberlv');
            /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
            $m_lv = $member_lv_mod->get_default_level();
    
            $nickname = $this->getCode(10);
            $ms =& ms(); //连接用户中心
            $invite = make_member_invite();

            $user_id = $ms->user->register($_POST['phone'], $check_step3['data']['ps'],$email ='',array('phone_mob'=>$_POST['phone'],'invite'=>$invite,'member_lv_id'=>1,'come_from'=>'pc','serve_type'=>1,'nickname'=>'麦富迪初体验','user_token'=>md5($_POST['phone'].$check_step3['data']['ps'])));

            
            
            if (!$user_id)
            {
                $this->json_error($ms->user->get_error());
                return;
            }

            
            ////////////////////////////start//////////////////// tangsj
            //邀请码
            $invite =$_REQUEST['invite'];
            if(!empty($invite)){
            
                if(strlen($invite)==9){
                    $m       = & m('member');
                    $memberinvite  = $m->get("serve_type=1 and invite = '".$invite."'");
                    $_type = 0;
                    //邀请码
                    $invite_nickname =$memberinvite['nickname'];
                    $inviter = $memberinvite['user_id'];
            
                    if( empty($memberinvite)) {
                        $this->json_error('邀请码错误!');
                        return;
                    }
                }
            
                if($memberinvite){
                    //邀请关系
                    $member_invite = m("memberinvite");
                    $invite_data = array(
                        'inviter'  => $inviter, //邀请人
                        'invitee'  => $user_id,
                        'nickname' => $invite_nickname,     //邀请人昵称
                        'type'      => $_type,
                        'come_from'=>'pc|register',
                        'add_time' => time(),
                    );
                    $member_invite->add($invite_data);
                }
            
            
            }
            
            //奖励
            $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
            $this->_debit_mod = &m("debit");
            $this->_member_mod = &m("member");
            $member = $this->_member_mod->get($user_id);
            $_cate_mod = & bm('gcategory', array('_store_id' => 0));

            $cy_member = new Member();
            $cy_member->debit($user_id,$_POST['phone']);
         
            //绑定注册  券
            if($member['user_id']){
                if($member['serve_type'] == 1){
                    if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type']) && !empty($store_allow['open'])){
            
                        if($store_allow['debit_cate']==1){
                            $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                        }else{
                            $expire_time =$store_allow['debit_time'];
                        }
                        //取得某分类所以子孙分类id
                        $gcategories = $_cate_mod->get_child_cateid($store_allow['debit_type']);
                        if(empty($gcategories)){
                            $gcategories = $store_allow['debit_type'];
                        }
                        
            
                        $data =array(
                            'debit_name'=>$store_allow['debit_name'],
                            'debit_t_id'=>$store_allow['debit_type'],
                            'debit_sn'=>time().createNonceStr(8),
                            'money'=>$store_allow['debit_num'],
                            'user_id'=>$user_id,
                            'source'=>'reg',
                            'add_time'=>time(),
                            'cate'=>$gcategories,
                            'expire_time'=>$expire_time,
                        );
                        $this->_debit_mod->add($data);
                    }
            
                }
            }
            $this->editErweima($user_id);
            ////////////////////////////end////////////////////
    
            $this->_hook('after_register', array('user_id' => $user_id));
            //登录
            $this->_do_login($user_id);
    
            /* 同步登陆外部系统 */
            $synlogin = $ms->user->synlogin($user_id);
            $this->assign('name', '麦富迪初体验');
    
            $content = $this->_view->fetch('member.finish-h.html');
            $this->json_result(array('content'=>$content),'注册成功!');
            unset($_SESSION['step2'], $_SESSION['step1']);
        } else if(isset($_SESSION['step1']) && isset($_POST['step1'])) {
            //注册的第二步
           
            $check_step2 = $this->_formStep2();
      
            if ($check_step2)
            {
                $this->json_error($check_step2);
                return;
            }
            if (check::isEmail($_POST['user_name']))//发送邮件
            {
                $res = emailcode($_POST['user_name'],'reg','reg','pc','get',$_POST['password']);
                if ($res['err'])
                {
                    $this->json_error('邮件服务器繁忙!');
                    return;
                }else{
                    unset($_SESSION['regcode']);
                    $this->assign('email',getHideEmail($_POST['user_name']));
                    $this->assign('emailurl',getEmailHref($_POST['user_name']));
                    $this->assign('t',EMAIL_FAIL_TIME);
                    $this->assign('data',$_POST);
                    $content = $this->_view->fetch('member.register-1-email.html');
                    $this->json_result(array('content'=>$content),'发送邮件成功'.$res['msg']);
                }
            }elseif (check::isMobile($_POST['user_name'])) {//发送手机验证码
                //发送短信
               
                $res = smsAuthCode($_POST['user_name'],'reg','reg','get','pc',$_POST['password']);
                
            
                if ($res['err'])
                {  
                    $this->json_error($res['msg']);
                    return;
                }else{
                  
                    $_SESSION[step2] = 1;
                    $this->assign('hphone',getHidePhone($_POST['user_name']));
                    $this->assign('t',SMS_FAIL_TIME);
                    $this->assign('data',$_POST);
                    $content = $this->_view->fetch('member.register-1-phone.html');
                    $this->json_result(array('content'=>$content));
                }
    
            }else{//不合法提示
                $this->json_error('用户名不合法!');
                return;
            }

        } else {
            //注册的第一步
            $_SESSION['step1'] = 1;
            if (!empty($_GET['ret_url']))
            {
                $ret_url = trim($_GET['ret_url']);
            }
            else
            {
                if (isset($_SERVER['HTTP_REFERER']))
                {
                    $ret_url = $_SERVER['HTTP_REFERER'];
                }
                else
                {
                    $ret_url = SITE_URL . '/index.php';
                }
            }
    
            $this->assign('ret_url', rawurlencode($ret_url));
            $this->_curlocal(LANG::get('user_register'));
            $this->_config_seo('title', Lang::get('user_register') . ' - ' . Conf::get('site_title'));
            $this->display('member.register.html');
    
        }
    }


    /**
     * 注册用户，外部通过url验证邮箱邮件
     *
     *
     * 同意条款、验证码、用户名长度、密码长度
     * @return json
     * @access public
     * @see formStep2
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    public function eValidation()
    {
        $cate = 'reg';
        $type = 'reg';
        $uname = $_REQUEST['email'];
        $check_code = $_REQUEST['code'];
        if(!$uname)
        {
            $this->show_warning('用户名为空');
            return;
        }

        if(!$check_code)
        {
            $this->show_warning('验证码为空');
            return;
        }

        $rs = authemail($uname,$check_code);
    
        if(!$rs)
        {
            $this->show_warning('邮箱验证码错误',1,"/member-register.html");
            return;
        }

        $ms =& ms();
     
        if (!$ms->user->check_username($uname))
        {
            $this->show_warning('user_exists');
            return;
        }

        $email_code_mod =& m('email_code');
        $conditions = array('conditions'=>" code = '$check_code'");
        $sms = $email_code_mod->get($conditions);
        if(!$sms)
        {
            $this->show_warning('并未发送邮件给您...');
            return;
        }
      
        /*会员默认等级*/
        $member_lv_mod =& m('memberlv');
        /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
        $m_lv = $member_lv_mod->get_default_level();

        $nickname = $this->getCode(10);
        $ms =& ms(); //连接用户中心
        $invite = make_member_invite();
       
        $user_id = $ms->user->register($uname, $sms['ps'], $uname,array('invite'=>$invite,'member_lv_id'=>1,'come_from'=>'pc','serve_type'=>1,'nickname'=>'麦富迪初体验','user_token'=>md5($uname.$sms['ps'])));
        if (!$user_id)
        {
            $this->json_error($ms->user->get_error());
            return;
        }
        
        ////////////////////////////start//////////////////// tangsj
        //邀请码
        $invite =$_REQUEST['invite'];
        if(!empty($invite)){
        
            if(strlen($invite)==9){
                $m       = & m('member');
                $memberinvite  = $m->get("serve_type=1 and invite = '".$invite."'");
                $_type = 0;
                //邀请码
                $invite_nickname =$memberinvite['nickname'];
                $inviter = $memberinvite['user_id'];
        
                if( empty($memberinvite)) {
                    $this->json_error('邀请码错误!');
                    return;
                }
            }
        
            if($memberinvite){
                //邀请关系
                $member_invite = m("memberinvite");
                $invite_data = array(
                    'inviter'  => $inviter, //邀请人
                    'invitee'  => $user_id,
                    'nickname' => $invite_nickname,     //邀请人昵称
                    'type'      => $_type,
                    'come_from'=>'pc|register',
                    'add_time' => time(),
                );
                $member_invite->add($invite_data);
            }
        
        
        }
        
        //奖励
        $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
        $this->_debit_mod = &m("debit");
        $this->_member_mod = &m("member");
        $member = $this->_member_mod->get($user_id);
        
        //绑定注册  券
        if(!empty($member)){
            if($member['serve_type'] == 1){
                if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type']) && !empty($store_allow['open'])){
        
                    if($store_allow['debit_cate']==1){
                        $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                    }else{
                        $expire_time =$store_allow['debit_time'];
                    }
        
                    $data =array(
                        'debit_name'=>$store_allow['debit_name'],
                        'debit_sn'=>time().createNonceStr(8),
                        'money'=>$store_allow['debit_num'],
                        'user_id'=>$user_id,
                        'source'=>'reg',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type'],
                        'expire_time'=>$expire_time,
                    );
                    $this->_debit_mod->add($data);
                }
        
            }
        }
//        $this->editErweima($user_id);
        ////////////////////////////end////////////////////
        
        
            
        $this->_hook('after_register', array('user_id' => $user_id));
        //登录
        $this->_do_login($user_id);

        /* 同步登陆外部系统 */
        $synlogin = $ms->user->synlogin($user_id);
        $this->assign('name', '麦富迪初体验');
        $this->_config_seo('title', Lang::get('register_form') . ' - ' . Conf::get('site_title'));
        $this->display('member.finish-e.html');

    }
    
    /**
     * 注册第一步验证
     *
     * 同意条款、验证码、用户名长度、密码长度
     * @return json
     * @access public
     * @see formStep2
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    public function _formStep2()
    {
       
        if (!$_POST['agree'])
        {
            return 'agree_first';
    
        }
        if ($_SESSION['regcode'] != strtolower($_POST['captcha']))
        {
            return 'captcha_failed';
        }
    
        /* 注册并登陆 */
        $user_name = trim($_POST['user_name']);
        $password  = $_POST['password'];
        $passlen = strlen($password);
        $user_name_len = strlen($user_name);
        if ($user_name_len < 3 || $user_name_len > 25)
        {
            return 'user_name_length_error';
        }
        if ($passlen < 6 || $passlen > 20)
        {
            return 'password_length_error';
        }
        $ms =& ms();

        if (!$ms->user->check_username($user_name))
        {
            return 'user_exists';
        }
    }
    

    /**
     * 注册第一步验证
     *
     * 同意条款、验证码、用户名长度、密码长度
     * @return json
     * @access public
     * @see formStep2
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    public function _formStep21()
    {
        if (!$_POST['xy'])
        {
          return 'agree_first';

        }
//        if ($_SESSION['regcode'] != strtolower($_POST['code']))
//        {
//             return 'captcha_failed';
//        }

        /* 注册并登陆 */
        $user_name = trim($_POST['phone']);
        $password  = $_POST['pwd1'];
        $password1  = $_POST['pwd2'];
        $passlen = strlen($password);
        $user_name_len = strlen($user_name);

        if ($user_name_len !=11)
        {
            return 'user_name_length_error';
        }
        if ($passlen < 6 || $passlen > 20)
        {
            return 'password_length_error';
        }
        if($password !==$password1){
            return 'two_password_diffrent';
        }
        $ms =& ms();
//        DefaultPassportUser   已经过滤 serve_type=1
        if (!$ms->user->check_username($user_name))
        {
            return 'user_exists';
        }
    }

    /**
     * 注册第二步验证 手机验证码
     *
     * 手机号、手机+验证码、有效时间
     * @return json
     * @access public
     * @see formStep2
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    public function formStep3(){
        $user_name = trim($_POST['phone']);
        $pcode  = $_POST['uname'];
        if (!check::isMobile($user_name))
        {
            return array('msg'=>'手机号码不合法!','err'=>1);
        }
        $sms_reg_tmp_mod =& m('sms_reg_tmp');
        $reset_time = SMS_FAIL_TIME;
        $conditions = array('conditions'=>"type = 'reg' and phone = '$user_name' and code='$pcode' ");
        $sms_log = $sms_reg_tmp_mod->get($conditions);
        if (!$sms_log['id'])
        {
            return array('msg'=>'请重新获取验证码!','err'=>1);
        }


//        echo date('Y-m-d H:i:s',$sms_log['fail_time']);
//        echo '--=';
//        echo date('Y-m-d H:i:s',time());
        if($sms_log['fail_time'] < time())
        {
            return array('msg'=>'验证码已过期!','err'=>1);
        }

        return array('msg'=>'','err'=>0,'data'=>$sms_log);
    }
    /**
     *    检查用户是否存在
     *
     *    @author    Garbin
     *    @return    void
     */
    function check_user()
    {
        $user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);
        if (!$user_name)
        {
            echo ecm_json_encode(false);

            return;
        }
        $ms =& ms();

        echo ecm_json_encode($ms->user->check_username($user_name));
    }

/**
 *    头像
 *
 *    @author    Hyber
 *    @usage    none
 */
function avatar(){
    $this->assign('ac','index');
    $user = $this->visitor->get();
    $this->assign('act', ACT);
    //$user['coin'] = floor($user['coin']);
    if($user['height'] == 0){
        $user['height'] = 0;
    }
    if($user['weight'] == 0){
        $user['weight'] = 0;
    }
    $this->assign('user', $user);
    $user_id = $this->visitor->get('user_id');
    if (!IS_POST)
    {

        $this->assign('type', 'user_set');
        /* 当前用户中心菜单 */
        $this->assign('_curitem', 'my_profile');
        $this->assign('_member_menu', $this->_get_member_menu('user_set'));

        $ms =& ms();    //连接用户系统
        $edit_avatar = $ms->user->set_avatar($this->visitor->get('user_id')); //获取头像设置方式
        $model_user =& m('member');
        $profile    = $model_user->get_info(intval($user_id));
        //获取会员等级信息-创业者
        $lv_info = $this->_lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
            //'fields' => 'name',
        ));
        // 帐号安全级别
        $this->level($user['user_id']);
        $this->assign('lv_info', $lv_info);

        //===== Beg liang.li 获取会员的抵用券个数  =====
        $debit_mod = m('debit');
        $num = $debit_mod->get(array(
            'conditions' => "user_id = {$user['user_id']} OR from_uid = {$user['user_id']}",
            'fields'     => "count(*) as num"
        ));
        $user['debit_nums'] = $num['num'];
        //=====  End liang.li  =====
        $this->assign('debit_num', $num['num']);


        $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
        $profile['erweima_url'] = SITE_URL.$profile['erweima_url'];
        $this->assign('profile',$profile);

        //获取会员等级信息-创业者
        $lv_info = $this->_lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
            //'fields' => 'name',
        ));
        $this->assign('lv_info', $lv_info);

        $avatar =SITE_URL.$profile['avatar'];
        $this->assign('avatar', $avatar);
        /* 头像 add by xiao5 END */

        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
        //获取地区
        $region_mod =& m('region');
        $this->assign('site_url', site_url());
        $this->assign('regions', $region_mod->get_options(0));

        $this->_config_seo('title', '我的麦富迪 - 个人资料');
        $this->assign('ac', ACT);
        $this->assign('app', APP);
        $this->display('member.avatar.html');
    }
    else
    {
        if(!$_POST['avatar'])
        {
            $this->show_message('请上传头像');
            return;
        }
        $data = array(
            'avatar'  => $_POST['avatar'],
            //'memo'      => $_POST['memo'],
        );
        $_SESSION['user_info']['avatar'] = $_POST['avatar'];
//        echo '<pre>';print_r($data);exit;
        
        $model_user =& m('member');
        $model_user->edit($user_id , $data);
        if ($model_user->has_error())
        {
            $this->show_warning($model_user->get_error());

            return;
        }

        $this->show_message('编辑头像成功');
    }
}

    /**
     *    修改基本信息
     *
     *    @author    Hyber
     *    @usage    none
     */
    function profile(){
        $this->assign('ac','index');
        $user = $this->visitor->get();
        $this->assign('act', ACT);
        //$user['coin'] = floor($user['coin']);
        if($user['height'] == 0){
            $user['height'] = 0;
        }
        if($user['weight'] == 0){
            $user['weight'] = 0;
        }
        $this->assign('user', $user);
        $user_id = $this->visitor->get('user_id');
        if (!IS_POST)
        {

            $this->assign('type', 'user_set');
            /* 当前用户中心菜单 */
            $this->assign('_curitem', 'my_profile');
            $this->assign('_member_menu', $this->_get_member_menu('user_set'));

            $ms =& ms();    //连接用户系统
            $edit_avatar = $ms->user->set_avatar($this->visitor->get('user_id')); //获取头像设置方式
            $model_user =& m('member');
            $profile    = $model_user->get_info(intval($user_id));
            //获取会员等级信息-创业者
            $lv_info = $this->_lv_mod->get(array(
                'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
                //'fields' => 'name',
            ));
            // 帐号安全级别
            $this->level($user['user_id']);
            $this->assign('lv_info', $lv_info);

            //===== Beg liang.li 获取会员的抵用券个数  =====
            $debit_mod = m('debit');
            $num = $debit_mod->get(array(
                'conditions' => "user_id = {$user['user_id']} OR from_uid = {$user['user_id']}",
                'fields'     => "count(*) as num"
                    ));
            $user['debit_nums'] = $num['num'];
            //=====  End liang.li  =====
            $this->assign('debit_num', $num['num']);

            
            $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
            $profile['erweima_url'] = SITE_URL.$profile['erweima_url'];
            $this->assign('profile',$profile);

            //获取会员等级信息-创业者
            $lv_info = $this->_lv_mod->get(array(
                'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
                //'fields' => 'name',
            ));
            $this->assign('lv_info', $lv_info);

            $avatar =SITE_URL.$profile['avatar'];
            $this->assign('avatar', $avatar);
            /* 头像 add by xiao5 END */

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
            //获取地区
            $region_mod =& m('region');
            $this->assign('site_url', site_url());
            $this->assign('regions', $region_mod->get_options(0));

            $this->_config_seo('title', '我的麦富迪 - 个人资料');
            $this->assign('ac', ACT);
            $this->assign('app', APP);
            $this->display('member.profile.html');
        }
        else
        {
            $data = array(
                'nickname'  => $_POST['nickname'],
                'real_name' => $_POST['real_name'],
                'phone_mob' => $_POST['phone_mob'],
                'email' => $_POST['email'],
                'gender'    => $_POST['sex'],
                'birthday'  => $_POST['birthday'],
                'height'    => $_POST['height'],
                'weight'    => $_POST['weight'],
                //'memo'      => $_POST['memo'],
            );
           $data_cus=array(
           		'customer_name'  => $_POST['nickname'],
            );
            if ($_POST['region_name'] && $_POST['region_id'])
            {
                $data['region_id'] = $_POST['region_id'];
                $data['region_name'] = $_POST['region_name'];
            }

            $model_user =& m('member');

            if (!$model_user->uniqueNickname($_POST['nickname'],$user_id))
            {
                if(empty($_POST['nickname'])){
                    $this->show_message('昵称不能为空');
                    return;
                }
            }

            $model_user->edit($user_id , $data);
            if ($model_user->has_error())
            {
                $this->show_warning($model_user->get_error());

                return;
            }

            $this->_customer_figure->edit("userid='{$user_id}'",$data_cus);
            $this->show_message('edit_profile_successed');
        }
    }
    /**
     * 手机发送验证码
     *
     * @param string $phoneNum 手机
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function sendCode()
    {
        $phone = $_POST['phone'];
        $code = rand(1000, 9999);
        //$type = 'pay_password';
        $type   = $_POST['type'];
        $timeout= 1200;//验证码过期时间        20分钟
        $rs   = SendSms($phone, '验证码为' . $code, $type, $code, $timeout);
        if (!$rs) {
            return $this->json_error('send_false');
        }
        return $this->json_result('send_success');

    }


    /**
     *    修改详细信息
     *
     *    @author    chao.liu<280181131@qq.com>
     *    @usage    none
     */
    function detailed(){
        $this->assign('ac','index');
        $user = $this->visitor->get();
        $this->assign('user', $user);
        $user_id = $this->visitor->get('user_id');
        $this->assign('act', ACT);
        if (!IS_POST)
        {

            $this->assign('type', 'user_set');
            /* 当前用户中心菜单 */
            $this->assign('_curitem', 'my_profile');
            $this->assign('_member_menu', $this->_get_member_menu('user_set'));

            $ms =& ms();    //连接用户系统
            $edit_avatar = $ms->user->set_avatar($this->visitor->get('user_id')); //获取头像设置方式

            $model_user =& m('member');
            $profile    = $model_user->get_info(intval($user_id));
            //抵用券数量
            $debit_mod = m('debit');
            $num = $debit_mod->get(array(
                'conditions' => "user_id = {$user['user_id']}",
                'fields'     => "count(*) as num"
            ));
            $profile['debit_num'] = $num['num'];
            $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
            $profile['erweima_url'] = SITE_URL.$profile['erweima_url'];
            $this->assign('profile',$profile);
            //获取邀请人
            $inv_mod = & m('memberinvite');
            $g = & m('generalize_member');
            $inviter = $inv_mod->get('invitee = '.$user_id);
            if($inviter){
            
                if($inviter['type']==1){
                    $inviter_info = $g->get("id='{$inviter['inviter']}'");
                    $inviter['nickname'] = empty($inviter_info)?'':$inviter_info['name'];
                }else{
                    $inviter_info  = $model_user->get("user_id='{$inviter['inviter']}'");
                    $inviter['nickname'] = empty($inviter_info)?'':$inviter_info['nickname'];
                }
            }else{
                 $inviter['nickname'] = '';
            }
            $this->assign('inviter',$inviter);
            //获取会员等级信息-创业者
            $lv_info = $this->_lv_mod->get(array(
                'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
                //'fields' => 'name',
            ));
            // 帐号安全级别
            $this->level($user['user_id']);
            $this->assign('lv_info', $lv_info);
            $avatar = SITE_URL.$profile['avatar'];
            $this->assign('avatar', $avatar);
            /* 头像 add by xiao5 END */

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
            //获取地区
            $region_mod = m('region');
            $region_id = $profile['region_id'];

            if ($region_id)
            {
                $region_info = $region_mod->get($region_id);
                $this->assign('p_region_id',$region_info['parent_id']);
                $list2 = $region_mod->get_options($region_info['parent_id']);
                //          dump($list2);
                $this->assign('region2',$list2);
            }

            $this->_config_seo('title', '我的麦富迪 - 个人资料');
            $this->assign('ac', ACT);
            $this->assign('app', APP);
            $this->display('member.detailed.html');
        }
        else
        {
            $data = array(
                'im_qq'  => $_POST['im_qq'],
                'wechat' => $_POST['wechat'],
                'birthday' => $_POST['birthday'],
                'region_id'=> $_POST['region_id'],
            );
            $invite = $_POST['inviter'];
            if($invite)
            {
                $this->addinviter($invite);
            }
           
            $model_user =& m('member');
            $model_user->edit($user_id , $data);
            if ($model_user->has_error())
            {
                $this->show_warning($model_user->get_error());

                return;
            }
            $this->show_message('edit_profile_successed');
        }
    }
    /**
     *    用户中心个人资料添加邀请码的 接口 
     *
     *    @author   tangshoujian<963830611@qq.com>
     *    @usage    none
     */
    
    function addinviter($invite){
        
        $m       = &m('member');
        $meminvite_mod = &m('memberinvite');
        $user_info = $this->visitor->get();
        $user_id = $this->visitor->get('user_id');
       
   
        //绑定一个邀请码
        if($meminvite_mod->get("invitee = '$user_id'"))
        {
           $this->msg('已被邀请过!');
           return;
        }
    
       
        if(strlen($invite)==9)
        {
            $member = $m->get( "serve_type=1 and invite = '".strtoupper($invite)."'");

            if($member['user_id'] == $user_id){
               
                $this->msg('不能邀请自己!');
                return;
            }
    
            if(empty($member))
            {
                $this->msg('邀请码错误!');
                return;
            }
    
            $_type =0;
            //邀请码
            if(empty($member['nickname']))
            {
                if(empty($member['real_name']))
                {
                    $invite_nickname =$member['user_name'];
                }else
                {
                    $invite_nickname =$member['real_name'];
                }
            }else
            {
                $invite_nickname =$member['nickname'];
            }
    
            $inviter = $member['user_id'];
        }
    
        $member_invite = m("memberinvite");
        $invite_data = array(
            'inviter'  => $inviter, //邀请人
            'invitee'  => $user_id,
            'nickname' => $invite_nickname,     //邀请人昵称
            'type'      => $_type,
            'add_time' => time(),
            'come_from'=>'pc|addinviter',
        );
        $member_invite->add($invite_data);
        
    }
    
    /**
     *    安全设置
     *
     *    @author   chao.liu<280181131@qq.com>
     *    @usage    none
     */
    function safe(){
        $user = $this->visitor->get();
        $user_id = $this->visitor->get('user_id');
        $profit = $this->_user_mod->get($user_id);
        $this->_config_seo('title', '我的麦富迪 - 安全设置');
        $this->assign('act', ACT);
        $this->assign('app', APP);
        if($profit['pay_password']){
            $button = '1';
        }else{
            $button = '0';
        }
        $phone_mob = $user['phone_mob'];
        $user['phone_mob']=substr_replace($phone_mob,'*****',3,5);
        $this->level($user_id);
        $auth_state = $this->_auth_mod->get("user_id='{$user_id}'");

        $this->assign('button',$button);
        $this->assign('user', $user);
        $this->assign('auth_state',$auth_state);
        $this->assign('profit', $profit);
        $this->display('member.safe.html');

    }
    /**
     *    账户安全级别
     *    @author   tangsj<963830611@qq.com>
     *    @usage    none
     */
    function level($user_id)
    {
     	$level = 1;
     	$info = $this->_user_mod->get($user_id);
        if($info['phone_mob'] && $info['phone_mob'] != $info['user_name']){

            $level = $level+1;
            $very  = 1;
        }else{
              $very  = 1;
        }
        if($info['email']){
            $level = $level +1;
        }
    /*     if($info['pay_password']){

            $level = $level+1;
        } */
    /*     $is_auth = $this->_auth_mod->get("user_id='$user_id' AND status =1");
        if($is_auth){

            $level = $level+1;
        } */
        if($level ==1 || $level ==0){
            $safe_level = '较低';
            $percent    = 30;
            //ns add color
            $color = '#ff5500';
        }elseif($level ==2){
             $safe_level = '一般';
			 $percent    = 50;
             $color = '#e66800';
        }elseif($level ==3){
           $safe_level  = '较高';
           $percent    = 100;
           $color = '#39d37c';
        }
        $this->assign('very', $very);
        $this->assign('percent',$percent);
        $this->assign('color',$color);
        $this->assign('safe_level',$safe_level);
        
    	
    }
    
    /**
     *    启用支付密码
     *    @author   chao.liu<280181131@qq.com>
     *    @usage    none
     */
    function pay_password_1(){
        $user = $this->visitor->get();
        $phone = substr_replace($user['phone_mob'],'*****',3,5);
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->display('pay_password.1.html');
    }

    function pay_password_2(){
        $user = $this->visitor->get();
        $phone = $_POST['phone'];
        $code  = $_POST['code'];
        $type  = 'pay_password';
        $this->checkCode($code,$phone,$type);
        $content = $this->_view->fetch('pay_password.2.html');
        $this->json_result(array('content'=>$content));
     
    }
    function pay_password_3(){
        $user = $this->visitor->get();
        if(IS_POST){
            $password   = $_POST['password'];
            $repassword = $_POST['repassword'];
         
            if($password != $repassword){
                
                $this->msg('两次密码不一样');
            }
            $password = md5($password);
            $this->_user_mod->edit($user['user_id'],array('pay_password'=>$password));
        }
        $this->assign('user',$user);
        $content = $this->_view->fetch('pay_password.3.html');
        $this->json_result(array('content'=>$content));
        
    }
    function  checkCode($code,$phone,$type){
        $sms_mod = m('sms_reg_tmp');
        //验证验证码是否有效
        if($code) {
            $res=$sms_mod->get(array(
                'conditions'=>"code='$code' AND phone='$phone' AND type='$type'",
                'order'      => "id  DESC",
                'fields'     => '*',
            ));
            if($res['phone']) {
                if(gmtime() > $res['fail_time']) {
                  
                    $this->msg('验证码已过期');
                }

            } else {
               
                $this->msg('验证码不正确');

            }
        } else {
           
            $this->msg('验证码不能为空');

        }
    }
    /**
     *    启用支付密码
     *    @author   chao.liu<280181131@qq.com>
     *    @usage    none
     */
    function edit_pay_password_1(){
        $user = $this->visitor->get();
        $phone = substr_replace($user['phone_mob'],'*****',3,5);
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->display('edit_pay_password.1.html');
    }

    function edit_pay_password_2(){
        $user = $this->visitor->get();
        $phone = $_POST['phone'];
        $code  = $_POST['code'];
        $type  = 'pay_password';
        $this->checkCode($code,$phone,$type);
        $content = $this->_view->fetch('edit_pay_password.2.html');
        $this->json_result(array('content'=>$content));

    }
    function edit_pay_password_3(){
        $user = $this->visitor->get();
        if(IS_POST){
            $password   = $_POST['password'];
            $repassword = $_POST['repassword'];
        
            if(strlen($password)<8 || strlen($repassword)<8){
                $this->msg('密码长度在8位以上');
            }
            if($password != $repassword){

                $this->msg('两次密码不一样');
            }
            $password = md5($password);
            $this->_user_mod->edit($user['user_id'],array('pay_password'=>$password));
        }
        $this->assign('user',$user);
        $content = $this->_view->fetch('edit_pay_password.3.html');
        $this->json_result(array('content'=>$content));

    }

    /**
     *    修改登录密码
     *    @author   tangsj<963830611@qq.com>
     *    @usage    none
     */

    function login_password_1()
    {
        $user = $this->visitor->get();
        $phone = substr_replace($user['phone_mob'],'*****',3,5);
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->display('login_password.1.html');

    }

    function login_password_2()
    {
        $user = $this->visitor->get();
        $phone = $_POST['phone'];
        $code  = $_POST['code'];
        $type  = 'findps';
        $this->checkCode($code,$phone,$type);
        //$this->display('login_password.2.html');
        $content = $this->_view->fetch('login_password.2.html');
        $this->json_result(array('content'=>$content));


    }

    function login_password_3(){
        $user = $this->visitor->get();
        if(IS_POST){
        $password   = $_POST['password'];
        $repassword = $_POST['repassword'];
        if($password != $repassword)
        {
            $this->msg('两次密码不一样');
        }
        $ms =& ms();    //连接用户系统
        $info = $ms->user->get($user['user_name'], true);
      
        if(md5(md5($password).$info['salt']) == $info['password']){
            
            $this->msg('不能和当前密码重复');
        }
        $result = $ms->user->edit($info['user_id'], '', array('password'  => $password),1);
        
        
        $editinfo = $ms->user->get($user['user_name'], true);
        
        $this->_user_mod->edit($user['user_id'],array('password'=>$editinfo['password']));
        }
        $this->assign('user',$user);
        $content = $this->_view->fetch('login_password.3.html');
        $this->json_result(array('content'=>$content));

    }
    /**
     *    修改手机绑定
     *    @author   tangsj<963830611@qq.com>
     *    @usage    none
     */
    function phone_bind_1()
    {
        $user = $this->visitor->get();
        $phone = substr_replace($user['phone_mob'],'*****',3,5);
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->display('phone_bind.1.html');

    }

    function phone_bind_2(){
        $user = $this->visitor->get();
        $password = $_POST['password'];
        if(!$password)
        {
        	
        	 $this->msg('登录密码不能为空');
        }
        $ms =& ms();    //连接用户系统
        $user_id = $ms->user->auth($user['user_name'], $password,true);  //验证用户
        
        
        if(!$user_id)
        {
            $this->msg('密码错误,请重新输入');
        }
        $this->assign('user',$user);
        $content = $this->_view->fetch('phone_bind.2.html');
        $this->json_result(array('content'=>$content));
       


    }

    function phone_bind_3()
    {
        $user = $this->visitor->get();
        $phone = $_POST['tel'];
        $code  = $_POST['code'];
        $type  = 'phone_bind';
      
        if(IS_POST)
        {
            if(!$phone)
            {
                $this->msg('手机号不能为空');
            }
            if(!$code){
            	
            	$this->msg('验证码不能为空');
            }
            $is_username = $this->_user_mod->get("user_name ='$phone' AND serve_type =1"); 
            if($is_username)
            {
                $this->msg('此手机号为已有账号,不允许绑定');

            } 
            $is_phone_mob = $this->_user_mod->get("phone_mob ='$phone' AND serve_type =1"); 
            if($is_phone_mob)
            {
                
                $this->msg('此手机号已经绑定不能重复绑定');
                
            }

            if($code)
            {
                  $this->checkCode($code,$phone,$type);
            }
            $data['phone_mob'] = $phone;

            if($this->_user_mod->edit($user['user_id'],$data))
            {
                $this->assign('phone',$phone);
                $content = $this->_view->fetch('phone_bind.3.html');
        		$this->json_result(array('content'=>$content));
                

            }else
            {
                $this->msg('手机号绑定失败');
               
            }

        }
       
    }
     /**
     *    邮箱验证
     *    @author   tangsj<963830611@qq.com>
     *    @usage    none
     */
    
    function email_bind_1()
    {
    	$user = $this->visitor->get();
        $phone = substr_replace($user['phone_mob'],'*****',3,5);
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->display('email_bind.1.html');
    	
    }
    function email_bind_2(){
    	
    	$user = $this->visitor->get();
        $phone = $_POST['phone'];
        $code  = $_POST['code'];
        $type  = 'email_bind';
        $this->checkCode($code,$phone,$type);
        $content = $this->_view->fetch('email_bind.2.html');
        $this->json_result(array('content'=>$content));
      
    	
    }
    
    function email_bind_3()
    {  
    	
    	if(IS_POST)
    	{  
    	   $ms =& ms();
    	   $c_email = $this->check_user_email($_POST['email']);
    	   if($c_email)
    	   {
                
                  $this->msg(Lang::get('js_remote'));
                 
                 
            }
            $code = md5("mfd" . $_POST['email']);
            $url = SITE_URL."/member-ActivationEail.html?uid=".$_SESSION['user_info']['user_id']."&email=$_POST[email]&code=$code";
            $email_from = Conf::get('site_name');
            $email_type = Conf::get('email_type');
            $email_host = Conf::get('email_host');
            $email_port = Conf::get('email_port');
            $email_addr = Conf::get('email_addr');
            $email_id   = Conf::get('email_id');
            $email_pass = Conf::get('email_pass');
            $email = $_POST['email'];
            $email_subject = Lang::get('email_subjuect');
            $email_content = sprintf(Lang::get('email_content'),$url);
            import('mailer.lib');
            $mailer = new Mailer($email_from, $email_addr, $email_type, $email_host, $email_port, $email_id, $email_pass);
            $mail_result = $mailer->send($email, $email_subject, $email_content, CHARSET, 1);
            $email_href = getEmailHref($email);
            $this->assign('email',$email);
            $this->assign('url','http://'.$email_href);
            $content = $this->_view->fetch('email_bind.3.html');
            $this->json_result(array('content'=>$content));
            
    		
    	}
    	
    }
    
    function email_bind_4(){
    	$content = $_REQUEST['url'];
    	$email   = $_REQUEST['email'];
    
    	if(!$content || !$email){
    		
    		$this->msg('邮件发送失败');
    		
    	}
    	$this->assign('url',$content);
        $this->assign('email',$email);
        $this->display('email_bind.3.html');
    	
    }
    
 	function check_user_email($email)
 	{
        if (!$email)
        {
            return false;
        }
        $ms =& ms();
        $res= $ms->user->check_email($email);
     
        if($res)
        {
            return false;
        }
        else
        {
            return true;
        } 
    }
    
    //激活邮箱
    function ActivationEail()
    {
        $to_code = $_GET['code'];
        $to_mail = $_GET['email'];
        $to_uid = $_GET['uid'];
        $ms =& ms();
        if($to_code != md5("mfd".$to_mail) || empty($to_mail)){
            $this->msg('此邮箱无效，请从新激活邮箱！');
        }else{
           $up_email = $ms->user->editemail($to_uid,array('email' => $to_mail),true);
       	   if (!$up_email)
            {
               
                $this->msg('邮箱激活失败！');
                return;
            }
        $this->assign('to_mail',$to_mail);
        $this->display('email_bind.4.html');
        }
    }
    function getImg(){
        $user_id = $this->visitor->get('user_id');
        $file = $_FILES['avatar'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            $this->json_error();
        }
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->json_error();
        }
        $uploader->root_dir(ROOT_PATH);
//        $root = str_replace('\\','/',LOCALHOST1);
        $path = SITE_URL.'/'.$uploader->save('data/files/mall/tmp/' . ceil($user_id / 500), $user_id);
        $this->json_result($path);
    }

    function editAvatar(){
        $img   = $_POST['avatar'];
        $data = str_replace("\\",'',$_POST['data']);
        $data = json_decode($data,true);
        $src_x = $data['x'];
        $src_y = $data['y'];
        $src_h = $data['h'];
        $src_w = $data['w'];
        $ys_w  = $data['imgw'];
        $ys_h  = $data['imgh'];
        $user_id = $this->visitor->get('user_id');

        require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();

        $result['avatar'] = $objAvatar->editAvatar($img,$src_w,$src_h,$ys_w,$ys_h,$src_x,$src_y,$user_id,'big');
        $result['erweima_url'] = $this->geterweima($user_id);
        $this->editErweima($user_id);
        if($result){
            $this->json_result($result);
        }else{
            $this->json_error('头像修改失败');
        }

    }

     /**
     *    实名认证
     *
     *    @author   tangsj<963830611@qq.com>
     *    @usage    none
     */

    function cyz_auth_1()
    {   
        $auth_mod =& m('auth');
        $region_mod =& m('region');
        $user = $this->visitor->get();
        $user_id = $user['user_id'];
        $auth_info = $auth_mod->get("user_id ='$user_id'");
       /* 头像 add by xiao5 START */
        require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();

        //获取头像
        $avatar = $objAvatar->avatar_show($user_id,'big');
        $this->assign('avatar', $avatar);
        /* 头像 add by xiao5 END */

        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
   
        if($auth_info)
        {   
          

            $country =$this->region_mod->getRegionName(2);
            $p =  $this->region_mod->getRegionName($auth_info['province']);
            $c    =$this->region_mod->getRegionName($auth_info['city']);
            $this->assign('country',$country);
            $this->assign('p',$p);
            $this->assign('c',$c);
            $this->assign('auth_info',$auth_info);
            $this->assign('user',$user);
            $this->display('cyz_auth.4.html');
            
        }else
        {
            $region_mod =& m('region');

            $region_info = $region_mod->get(247);
            $this->assign('p_region_id',$region_info['parent_id']);
            $list2 = $region_mod->get_options($region_info['parent_id']);
          
            $this->assign('region2',$list2);

            $phone = $user['phone_mob'];
            $code = $_POST['code'];
            $this->assign('phone',$phone);
            $this->assign('user',$user);
            $this->display('cyz_auth.1.html');
            
        }
       



    }

    /**
    * ajax上传图片
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-21
    */
    function upload()
    {
        sleep(1);
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";

        $imageTool = new ImageTool();
        $imageTool->_upload_dir = $this->real_dir;
        $dir = $imageTool->uploadImage($_FILES[$type]);
        if(!$dir)
        {
            $this->json_error('上传失败!');
            return;
        }
        $imgUrl = $this->web_dir.$dir;
        $this->json_result(array('name'=>$dir,'src'=>$imgUrl,'type'=>$type),'上传成功!');
        return;
    }

    /**
    * ajax删除图片
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-21
    */
    function del_file()
    {
        $type = isset($_POST['type']) ? $_POST['type'] : 0;
        $mode = isset($_POST['model']) ? $_POST['model'] : '';
        $field = isset($_POST['field']) ? $_POST['field'] : '';
        $auth_id = isset($_POST['id']) ? $_POST['id'] : 0;
        $user_id = $this->visitor->get('user_id');
        $img = isset($_POST['img']) ? $_POST['img'] : '';
        $real_dir = ROOT_PATH.'/'.$img;
      
        if (!$img)
        {
            $this->json_error('图片地址不能为空删除失败');
            return;
        }

        if ($auth_id)
        {
            if (!$mode || !$field)
            {
                $this->json_error('删除失败 找不到模型或者字段');
                return;
            }
            $mod = m($mode);

            $m_info = $mod->get($auth_id);
            if ($m_info['user_id'] != $user_id)
            {
                $this->json_error('只能删除自己上传的图片');
                return;
            }
            if(!$mod->edit($auth_id,array($field=>'')))
            {
                $this->json_error('数据库修改失败');
                return;
            }

        }


        @unlink($real_dir);
        $this->json_result();

    }
   //重新认证 的链接
    function cyz_auth_4()
    {
        $region_mod =& m('region');
        $user = $this->visitor->get();
        $user_id = $user['user_id'];
        $region_info = $region_mod->get(247);
        $this->assign('p_region_id',$region_info['parent_id']);
        $list2 = $region_mod->get_options($region_info['parent_id']);
      
        $this->assign('region2',$list2);
        $auth_info =  $this->_auth_mod->get("user_id='$user_id'");

        if($auth_info){
        	$auth_info['card_face_img_data'] = $auth_info['card_face_img'];
            $auth_info['card_face_img']     = SITE_URL.'/'.$auth_info['card_face_img'];
        	$this->assign('auth_info',$auth_info);
        }
     
        $phone = $user['phone_mob'];
        $code = $_POST['code'];
        $this->assign('phone',$phone);
        $this->assign('user',$user);
        $this->assign('auth_info',$auth_info);
        $this->display('cyz_auth.1.html');


    }
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

    function cyz_auth_2()
    {
       
        $user = $this->visitor->get();
        $bank_arr = include_once ROOT_PATH.'/soaapi/v1/includes/libraries/arrayfile/bank.arrayfile.php';
        foreach($bank_arr as $k=>$v){
            $banks[] =array(
                'bank_id' => $k,
                'bank'    => $v,
            );
        }
        $bankid_arr = array_flip($bank_arr);
        $realname = isset($_POST['realname']) ? $_POST['realname'] : '';
        $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
        $code = isset($_POST['code']) ? $_POST['code'] : '';
        $province = isset($_POST['province']) ? $_POST['province'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $card = isset($_POST['card']) ? $_POST['card'] : '';
        $card_face_img = isset($_POST['hid_face_img']) ? $_POST['hid_face_img'] : '';

        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $province = isset($_POST['p_region_id']) ? $_POST['p_region_id'] : '';
        $city     = isset($_POST['region_id']) ? $_POST['region_id'] : '';
     
        if(!$realname)
        {
            $this->msg('请输入真实姓名');
        }
        if(!$card)
        {
            $this->msg('请输入身份证号码');
            
    
        }
        if(!($this->isCreditNo($card)))
        {
                
            $this->msg('身份证号码输入有误');
            
        }
       
        if(!$code){
            $this->msg('请输入验证码');
        
        }

       if(!$card_face_img){
            $this->msg('请上传身份证正面');
     
        }
        if(!$address){
              $this->msg('请输入详细地址');
     
        }
        $type  = 'auth';
        $this->checkCode($code,$mobile,$type);

        $this->assign('realname',$realname);
        $this->assign('mobile',$mobile);
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('address',$address);
        $this->assign('card',$card);
        $this->assign('card_face_img',$card_face_img);
        $this->assign('country',$country);
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('user',$user);
        $this->assign('banks',$banks);
        $auth_info =  $this->_auth_mod->get("user_id='{$user['user_id']}'");
        $auth_info['bank_id'] = $bankid_arr[$auth_info['bank']];
        if($auth_info){
        	
        	 $this->assign('auth_info',$auth_info);
        }
        $content = $this->_view->fetch('cyz_auth.2.html');
        $this->json_result(array('content'=>$content));
       


    }

    function cyz_auth_3()
    {   
    	$bank_arr = include_once ROOT_PATH.'/soaapi/v1/includes/libraries/arrayfile/bank.arrayfile.php';
        $region_mod =&m('region');
        $user = $this->visitor->get();
        $user_id = $user['user_id'];
        
        $bank_id = isset($_POST['bank_id']) ? $_POST['bank_id'] : '';
        $bank = $bank_arr[$bank_id];
        $bankcard_address = isset($_POST['bankcard_address']) ? $_POST['bankcard_address'] : '';
        $bankcard  = isset($_POST['bankcard']) ? $_POST['bankcard'] : '';
        // 这里是hidden 隐藏的值 带过来 
        $realname = isset($_POST['realname']) ? $_POST['realname'] : '';
        $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
     
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $province = isset($_POST['province']) ? $_POST['province'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';
        $card = isset($_POST['card']) ? $_POST['card'] : '';
        $card_face_img = isset($_POST['card_face_img']) ? $_POST['card_face_img'] : '';
        $card_face_img = 'upload/auth/'.$card_face_img;
        

        $country = $region_mod->getRegionName($country);
        $p = $region_mod->getRegionName($province);
        $c    = $region_mod->getRegionName($city);
    
        if(!$bankcard_address){
            $this->msg('请输入开卡地区');
     
        }
        if(!$bankcard){
              $this->msg('请输入银行卡号');
     
        }
        if(!$this->luhmCheck($bankcard))
        {
            $this->msg("银行卡号码输入有误");
            
        }
        // 数据组装
        $_data['user_id']         = $user_id;
        $_data['bank']            = $bank;
        $_data['bankcard_address'] = $bankcard_address;
        $_data['bankcard']        = $bankcard;
        $_data['realname']        = $realname;
        $_data['mobile']          = $mobile;
        $_data['province']        = $province;
        $_data['city']            = $city;
        $_data['address']         = $address;
        $_data['card_face_img']   = $card_face_img;
        //$_data['card_back_img']   = $card_back_img;
        $_data['last_update_time']= time();
        $_data['card']            = $card;

       

        $auth = $this->_auth_mod->get("user_id= '$user_id' AND (status=2 OR status=1)");
        
          //当审核失败 或者 未审核的时候对 认证信息的处理
        if($auth){
         
            $_data['status'] = 0;
            $_data['fail_reason'] ='';
            $cash_id =  $this->_auth_mod->edit("user_id = '$user_id'",$_data);
             //在member表中的默认银行修改资料
            $member_mod = &m('member');
            $arr = "df_bankcard = '{$bankcard}'";
            $member_mod->edit($user_id,$arr);
            //把银行卡信息更新到member_bank表中
            $bank_arr['user_id']         = $user_id;
            $bank_arr['bank']            = $bank;
            $bank_arr['bank_id']         = $bank_id;
            $bank_arr['bank_address']    = $bankcard_address;
            $bank_arr['bank_card']       = $bankcard;
            $bank_arr['card_name']       = $realname;
            $bank_arr['add_time']        = time();
         
            $figure_auth = $this->_auth_mod->get("user_id='$user_id'");
            $bank_mod = &m('member_bank');
             
            $bank_mod->edit("user_id ='$user_id'",$bank_arr);
        
            
             $this->_auth_mod->setInc("user_id='$user_id'",'num');
       
            
           
        
        }

        // 判断认证信息为空的时候才能新申请新的认证
        $is_auth =   $this->_auth_mod->get("user_id=$user_id");

        if(!$is_auth)
        {
                 // 添加认证资料 
            if(($cash_id =  $this->_auth_mod->add($_data)))
            {
                //在member表中的默认银行卡添加资料
                $member_mod =  &m('member');
                $arr = "df_bankcard = '{$bankcard}'";
                $member_mod->edit($user_id,$arr);
                //把银行卡信息添加到member_bank表中
                $bank_arr['user_id']         = $user_id;
                $bank_arr['bank']            = $bank;
                $bank_arr['bank_id']         = $bank_id;
                $bank_arr['bank_address']    = $bankcard_address;
                $bank_arr['bank_card']       = $bankcard;
                $bank_arr['card_name']       = $realname;
                $bank_arr['add_time']        = time();
              
                $figure_auth = $this->_auth_mod->get("user_id='$user_id'");
                
                $bank_mod = &m('member_bank');
                $bank_mod->add($bank_arr);
            
             
                 $this->_auth_mod->setInc("user_id='$user_id'",'num');
                
          
                
            }
            else
            {
                $this->msg('提交认证资料失败');
            }
        }

        $auth_info = $this->_auth_mod->get("user_id='$user_id'");
        $this->assign('auth_info',$auth_info);
        $this->assign('country',$country);
        $this->assign('p',$p);
        $this->assign('c',$c);
        $content = $this->_view->fetch('cyz_auth.3.html');
        $this->json_result(array('content'=>$content));



    }
    
     /**
           *  身份证号码 验证
           *
       Copy and translate from JAVASCRIPT code by Yuri 2014-08-12 *

     */

    function isCreditNo($vStr)
    {
    
        $vCity = array(
              '11', '12', '13', '14', '15', '21', '22',
              '23', '31', '32', '33', '34', '35', '36',
              '37', '41', '42', '43', '44', '45', '46',
              '50', '51', '52', '53', '54', '61', '62',
              '63', '64', '65', '71', '81', '82', '91'
          );
       if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
       if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
       $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
       $vLength = strlen($vStr);
       if ($vLength == 18)
       {
          $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
       } else {
          $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
       }
       if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
       if ($vLength == 18)
       {
            $vSum = 0;
            for ($i = 17 ; $i >= 0 ; $i--)
            { // www.jbxue.com
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }
            if($vSum % 11 != 1) return false;
       }
       return true;
    
    
    }
     /**
           *  银行卡号验证
           *
       Copy and translate from JAVASCRIPT code by Yuri 2014-08-12 *

     */
    function luhmCheck($bankno)
    {
        
        $banknolen = mb_strlen($bankno);

          //长度
        if ($banknolen < 16 || $banknolen > 20) {
          return false;
        }

          //银行卡号必须全为数字
        if(!preg_match('/^\d*$/',$bankno)){
              return false;
        }

          //开头6位 银行卡号开头6位规范
        $strBin = "10,18,30,35,37,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,58,60,62,65,68,69,84,87,88,94,95,98,99";
        if(!strpos($strBin, substr($bankno, 0, 2))){
            return false;
        }

          //取出最后一位（与luhm进行比较）
        $lastNum = substr($bankno, mb_strlen($bankno)-1,1);
        $first15Num = substr($bankno, 0, mb_strlen($bankno)-1);

        $newArr = Array();
        for($i = mb_strlen($first15Num)-1; $i > -1; $i--){    //前15或18位倒序存进数组
            array_push($newArr, substr($first15Num, $i, 1));
        }
        $arrJiShu =Array();  //奇数位*2的积 <9
        $arrJiShu2 = Array(); //奇数位*2的积 >9

        $arrOuShu = Array();  //偶数位数组
        for($j=0; $j < count($newArr); $j++){
            if(($j + 1) % 2 == 1){//奇数位
                if(intval($newArr[$j])*2 < 9){
                    array_push($arrJiShu, intval($newArr[$j])*2);
                }else{
                    array_push($arrJiShu2, intval($newArr[$j])*2);
                }
            }
            else {//偶数位
                array_push($arrOuShu, $newArr[$j]);
            }
        }

        $jishu_child1 = Array();//奇数位*2 >9 的分割之后的数组个位数
        $jishu_child2 = Array();//奇数位*2 >9 的分割之后的数组十位数
        for($h = 0; $h < count($arrJiShu2); $h++){
            array_push($jishu_child1, intval($arrJiShu2[$h])%10);
            array_push($jishu_child2, intval($arrJiShu2[$h])/10);
        }

        $sumJiShu = 0; //奇数位*2 < 9 的数组之和
        $sumOuShu = 0; //偶数位数组之和
        $sumJiShuChild1 = 0; //奇数位*2 >9 的分割之后的数组个位数之和
        $sumJiShuChild2 = 0; //奇数位*2 >9 的分割之后的数组十位数之和
        $sumTotal = 0;
        for($m = 0; $m < count($arrJiShu); $m++){
            $sumJiShu = $sumJiShu + intval($arrJiShu[$m]);
        }

        for($n=0;$n < count($arrOuShu); $n++){
            $sumOuShu = $sumOuShu + intval($arrOuShu[$n]);
        }

        for($p = 0; $p < count($jishu_child1); $p++){
            $sumJiShuChild1 = $sumJiShuChild1 + intval($jishu_child1[$p]);
            $sumJiShuChild2 = $sumJiShuChild2 + intval($jishu_child2[$p]);
        }
          //计算总和
        $sumTotal = intval($sumJiShu) + intval($sumOuShu) + intval($sumJiShuChild1) + intval($sumJiShuChild2);

          //计算Luhm值
        $k = intval($sumTotal)%10 == 0 ? 10 : intval($sumTotal)%10;
        $luhm = 10 - $k;

        if($lastNum == $luhm){
              //验证通过
            return true;
        }
        else{
              //银行卡号必须符合Luhm校验
            return false;
        }
        
    }
   

    function geterweima($id){

        $model_user =& m('member');
        $user_info  = $model_user->get_info(intval($id));
        $user_id = $user_info['user_id'];
        /* 头像 add by xiao5 START */
        require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获得用户头像
        $avatar = $objAvatar->avatar_show($user_id, 'big');
        
        //生成获得店铺二维码
     
        $code = $user_info['invite'];
        Qrcode('store', $user_id, 'http://wap.mfd.cn/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code,$avatar);
        $mqrcode = getQrcodeImage('store', $user_id, 2);
        $mqrcode = SITE_URL.'/upload/phpqrcode/'.$mqrcode.'?'.$user_info['avatar_time'];
        return $mqrcode;
    }
    
    function editErweima($id){
 
        //////////////////////////
        
        $model_user =& m('member');
        $user_info  = $model_user->get_info(intval($id));
        $user_id = $user_info['user_id'];
        /* 头像 add by xiao5 START */
        require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获得用户头像
        $avatar = $objAvatar->avatar_show($user_id, 'big');
        $avatar_tmp = $objAvatar->avatar_file($user_id, 'big');
        $avatar_tmp = '/upload/avatar/'.$avatar_tmp;  
        if(!$user_info['avatar']){
            $avatar_tmp = '/avatar/noavatar_big.gif';
        }   
        //生成获得店铺二维码
        $avatar_time = time();
//        $code = $user_info['invite'];
//        Qrcode('store', $user_id, 'http://h5.myfoodiepet.com/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code,$avatar);
//        $mqrcode = getQrcodeImage('store', $user_id, 2);
//        $mqrcode = '/upload/phpqrcode/'.$mqrcode.'?'.$avatar_time;
        
        $arr = array(
            'avatar'=>$avatar_tmp,
            'avatar_time'=>$avatar_time,
//            'erweima_url'=>$mqrcode
        );
        $model_user->edit($user_id,$arr);
       
        
    
    }
    /**
     *    修改密码
     *
     *    @author    Hyber
     *    @usage    none
     */
    function password(){
        $user_id = $this->visitor->get('user_id');
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('edit_password'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_password');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_password');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->_config_seo('title', Lang::get('user_center') . ' - ' . Lang::get('edit_password'));
            $this->display('member.password.html');
        }
        else
        {
            /* 两次密码输入必须相同 */
            $orig_password      = $_POST['orig_password'];
            $new_password       = $_POST['new_password'];
            $confirm_password   = $_POST['confirm_password'];
            if ($new_password != $confirm_password)
            {
                $this->show_warning('twice_pass_not_match');

                return;
            }
            if (!$new_password)
            {
                $this->show_warning('no_new_pass');

                return;
            }
            $passlen = strlen($new_password);
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            /* 修改密码 */
            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'password'  => $new_password
            ));
            if (!$result)
            {
                /* 修改不成功，显示原因 */
                $this->show_warning($ms->user->get_error());

                return;
            }

            $this->show_message('edit_password_successed');
        }
    }
    /**
     *    修改电子邮箱
     *
     *    @author    Hyber
     *    @usage    none
     */
    function email(){
        $user_id = $this->visitor->get('user_id');
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('edit_email'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('edit_email');
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->_config_seo('title', Lang::get('user_center') . ' - ' . Lang::get('edit_email'));
            $this->display('member.email.html');
        }
        else
        {
            $orig_password  = $_POST['orig_password'];
            $email          = isset($_POST['email']) ? trim($_POST['email']) : '';
            if (!$email)
            {
                $this->show_warning('email_required');

                return;
            }
            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }

            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'email' => $email
            ));
            if (!$result)
            {
                $this->show_warning($ms->user->get_error());

                return;
            }

            $this->show_message('edit_email_successed');
        }
    }

    /**
     * Feed设置
     *
     * @author Garbin
     * @param
     * @return void
     **/
    function feed_settings()
    {
        if (!$this->_feed_enabled)
        {
            $this->show_warning('feed_disabled');
            return;
        }
        if (!IS_POST)
        {
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),  'index.php?app=member',
                             LANG::get('feed_settings'));

            /* 当前用户中心菜单 */
            $this->_curitem('my_profile');

            /* 当前所处子菜单 */
            $this->_curmenu('feed_settings');
            $this->_config_seo('title', Lang::get('user_center') . ' - ' . Lang::get('feed_settings'));

            $user_feed_config = $this->visitor->get('feed_config');
            $default_feed_config = Conf::get('default_feed_config');
            $feed_config = !$user_feed_config ? $default_feed_config : unserialize($user_feed_config);

            $buyer_feed_items = array(
                'store_created' => Lang::get('feed_store_created.name'),
                'order_created' => Lang::get('feed_order_created.name'),
                'goods_collected' => Lang::get('feed_goods_collected.name'),
                'store_collected' => Lang::get('feed_store_collected.name'),
                'goods_evaluated' => Lang::get('feed_goods_evaluated.name'),
//                 'groupbuy_joined' => Lang::get('feed_groupbuy_joined.name')
            );
            $seller_feed_items = array(
                'goods_created' => Lang::get('feed_goods_created.name'),
                'groupbuy_created' => Lang::get('feed_groupbuy_created.name'),
            );
            $feed_items = $buyer_feed_items;
            if ($this->visitor->get('manage_store'))
            {
                $feed_items = array_merge($feed_items, $seller_feed_items);
            }
            $this->assign('feed_items', $feed_items);
            $this->assign('feed_config', $feed_config);
            $this->display('member.feed_settings.html');
        }
        else
        {
            $feed_settings = serialize($_POST['feed_config']);
            $m_member = &m('member');
            $m_member->edit($this->visitor->get('user_id'), array(
                'feed_config' => $feed_settings,
            ));
            $this->show_message('feed_settings_successfully');
        }
    }

     /**
     *    三级菜单
     *
     *    @author    Hyber
     *    @return    void
     */
    function _get_member_submenu()
    {
        $submenus =  array(
            array(
                'name'  => 'basic_information',
                'url'   => 'index.php?app=member&amp;act=profile',
            ),
            array(
                'name'  => 'edit_password',
                'url'   => 'index.php?app=member&amp;act=password',
            ),
            array(
                'name'  => 'edit_email',
                'url'   => 'index.php?app=member&amp;act=email',
            ),
        );
        if ($this->_feed_enabled)
        {
            $submenus[] = array(
                'name'  => 'feed_settings',
                'url'   => 'index.php?app=member&amp;act=feed_settings',
            );
        }

        return $submenus;
    }

     /**
     * 修改头像
     *
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    function upavater(){
        $user_id = $this->visitor->get('user_id');
        if (!IS_POST)
        {
            $this->assign('type', 'user_set');
            /* 当前用户中心菜单 */
            $this->assign('_curitem', 'my_profile');
            $this->assign('_member_menu', $this->_get_member_menu('user_set'));
            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();

            //获取头像
            $avatar = $objAvatar->avatar_show($user_id,'big');
           $uc_avatarflash = $objAvatar->uc_avatar($user_id);
            $this->assign('avatar', $avatar);
            $this->assign('uc_avatarflash', $uc_avatarflash);

            $this->_config_seo('title', Lang::get('user_center') . ' - ' . Lang::get('edit_password'));
            $this->display('member.avater.html');
        }
        else
        {
            /* 两次密码输入必须相同 */
            $orig_password      = $_POST['orig_password'];
            $new_password       = $_POST['new_password'];
            $confirm_password   = $_POST['confirm_password'];
            if ($new_password != $confirm_password)
            {
                $this->show_warning('twice_pass_not_match');

                return;
            }
            if (!$new_password)
            {
                $this->show_warning('no_new_pass');

                return;
            }
            $passlen = strlen($new_password);
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            /* 修改密码 */
            $ms =& ms();    //连接用户系统
            $result = $ms->user->edit($this->visitor->get('user_id'), $orig_password, array(
                'password'  => $new_password
            ));
            if (!$result)
            {
                /* 修改不成功，显示原因 */
                $this->show_warning($ms->user->get_error());

                return;
            }

            $this->show_message('edit_password_successed');
        }
    }



    /**
     * 上传头像
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_portrait($user_id)
    {
        $file = $_FILES['portrait'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=member&amp;act=profile');
            return false;
        }
        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/portrait/' . ceil($user_id / 500), $user_id);
    }
}

?>
