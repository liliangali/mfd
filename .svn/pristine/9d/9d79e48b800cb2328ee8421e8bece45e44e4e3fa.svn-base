<?php
/**
 *财富
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package auth.app.php
*/
class WealthApp extends MemberbaseApp
{
	function __construct()
	{
		$this->AuthApp();
	}

	/**
	* content
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-17
	*/
	function AuthApp()
	{
	    $this->_lv_mod =& m('memberlv');
	    $this->_user_mod =& m('member');
	    $path = ROOT_PATH."/data/config/special_code.php";
	    file_exists($path) && $this->_cate = include $path;
	    $this->_mod =& m('kukaconfig');
	    $this->type = array(
	        '0003' => '西服',
	        '0004' => '西裤',
	        '0005' => '马甲',
	        '0006' => '衬衣',
	        '0007' => '大衣',
	    
	    );
	    $this->assign('type',$this->type);
	    parent::__construct();
	    define("DIR", "wealth/");
	}


	/**
	*财富首页
	*@author liang.li <1184820705@qq.com>
	*@2015年10月9日
	*/
	function index() 
	{
	    $user = $this->visitor->get();
	    $user_id = $user['user_id'];
	    $time = time() - 30*24*60*60;
	    //=====  统计近一个月的消费记录  =====
	    $order_mod = m('order');
	    $debit_mod = m('debit');
	    $mod = m('member');
	    $order_item = $order_mod->get(array(
	       'conditions' => "user_id = $user_id AND status > 11 AND add_time>$time", 
	        'fields'    => "sum(order_amount) as num",
	    ));
	    
	    //=====  优惠券个数  =====
	    if ($user['member_lv_id'] > 1) 
	    {
	        $debit_conditions = "user_id = $user_id OR from_uid=$user_id";
	    }
	    else 
	    {
	        $debit_conditions = "user_id = $user_id";
	    }
	    
	    $debit_item = $debit_mod->get(array(
	       'conditions' => $debit_conditions,
	        'fields'    => "count(*) as num" 
	    ));
	    
	    
	    
	    $this->assign('debit_num',$debit_item['num']);
	    $this->assign('order_num',$order_item['num']);
	    $this->assign('user',$mod->get_info($user_id));
	    $this->display(DIR."index.html");
	}
	
	
	
	/**
	 *我的卡券 分支页面
	 *@author tangsj <963830611@qq.com>
	 *@2016年1月27日
	 */
	
	function mycoupon(){
	  
	    $user = $this->visitor->get();
	    $user_id = $user['user_id'];
	    
	    $member = m('member');
	    $_special_code_mod = & m('special_code');
	    $debit_mod = m('debit');
	    $now_time = time();
	    
	    if ($user['member_lv_id'] > 1)
	    {
	        /*   未赠送的 酷卡张数 start */
	       
	        $wzs = $_special_code_mod->get(array(
	            'conditions'=>"from_id ='{$user_id}' and to_id='' and expire_time>'{$now_time}'",
	            'fields'    =>"count(*) as wzs",
	        ));
	      
	        $this->assign('wzs',$wzs['wzs']);
	        /* end  */
	        /*   共几张优惠券 有几张即将过期 start  */
	        $total = $debit_mod->get(array(
	            'conditions'=>"from_uid = {$user_id} OR user_id = {$user_id}",
	             'fields'   =>"count(*) as total",
	        ));
	        
	        $ygq =  $debit_mod->get(array(
	            'conditions'=>" (from_uid = {$user_id} OR user_id = {$user_id})  and is_invalid = 1 ",
	            'fields'   =>"count(*) as ygq",
	        ));
	        $num = !empty($ygq['ygq']) ? $ygq['ygq'] :0;
	        $this->assign('total',$total['total']);
	        $this->assign('ygq',$num);
	       /*  end */
	        $this->display(DIR."couponcyz.html");
	    }
	    else
	    {
	        //  未激活的酷卡张数
	        $wjh = $_special_code_mod->get(array(
	            'conditions'=>"  to_id='{$user_id}' and is_used= 0 and expire_time>'{$now_time}'",
	            'fields'    =>"count(*) as wjh",
	        ));
	        
	      
	        $this->assign('wjh',$wjh['wjh']);
	        // end 
	        // 共几张优惠券 ，有几张即将过期 start 
	        $total = $debit_mod->get(array(
	            'conditions'=>"user_id = {$user_id}",
	            'fields'   =>"count(*) as total",
	        ));
	        $ygq =  $debit_mod->get(array(
	            'conditions'=>"user_id = {$user_id}  and is_invalid = 1 ",
	            'fields'   =>"count(*) as ygq",
	        ));
	        $num = !empty($ygq['ygq']) ? $ygq['ygq'] :0;
	        $this->assign('total',$total['total']);
	        $this->assign('ygq',$num);
	        // end 
	        
	        $this->display(DIR."couponcustomer.html");
	    }
	    
	    
	}
	
	/**
	*积分列表 
	*@author liang.li <1184820705@qq.com>
	*@2015年10月9日
	*/
	function point() 
	{
	    $user = $this->visitor->get();
	    $user_id = $user['user_id'];
	    $order_cash_log_mod = m('ordercashlog');
	    $page   =   $this->_get_page(10);
	    $list = $order_cash_log_mod->find(array(
	        'conditions' => "user_id = $user_id AND type = 1 ",
	        'limit'         => $page['limit'],  //获取当前页的数据
	        'count'         => true,
	        'order'         => "id DESC",
	    ));
	    if ($list)
	    {
	        foreach ($list as $key => &$value)
	        {
	        }
	    }
	     
	    $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
	    $this->_format_page($page);
	    $this->assign('search',$search_arr);
	    $this->assign('list',$list);
	    $this->assign('user',$user);
	    $this->assign('page_info', $page);
	    $this->display(DIR."point.html");
	}
	
	/**
	 *积分列表
	 *@author liang.li <1184820705@qq.com>
	 *@2015年10月9日
	 */
	function profit()
	{
	    $user = $this->visitor->get();
	    $user_id = $user['user_id'];
	    $order_cash_log_mod = m('ordercashlog');
	    $page   =   $this->_get_page(10);
	    $list = $order_cash_log_mod->find(array(
	        'conditions' => "user_id = $user_id AND type = 0 ",
	        'limit'         => $page['limit'],  //获取当前页的数据
	        'count'         => true,
	        'order'         => "id DESC",
	    ));
	    if ($list)
	    {
	        foreach ($list as $key => &$value)
	        {
	        }
	    }
	
	    $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
	    $this->_format_page($page);
	    $this->assign('search',$search_arr);
	    $this->assign('list',$list);
	    $this->assign('user',m('member')->get_info($user_id));
	    $this->assign('page_info', $page);
	    $this->display(DIR."profit.html");
	}
	
	/**
	*麦富迪币列表
	*@author liang.li <1184820705@qq.com>
	*@2015年10月9日
	*/
	function coin() 
	{
	    $user = $this->visitor->get();
	    $user_id = $user['user_id'];
	    $order_cash_log_mod = m('ordercashlog');
	    $page   =   $this->_get_page(10);
	    $list = $order_cash_log_mod->find(array(
	        'conditions' => "user_id = $user_id AND type = 2 ",
	        'limit'         => $page['limit'],  //获取当前页的数据
	        'count'         => true,
	        'order'         => "id DESC",
	    ));
	    if ($list)
	    {
	        foreach ($list as $key => &$value)
	        {
	        }
	    }
	    
	    $page['item_count'] = $order_cash_log_mod->getCount();   //获取统计的数据
	    $this->_format_page($page);
	    $this->assign('search',$search_arr);
	    $this->assign('list',$list);
	    $this->assign('page_info', $page);
	    $this->assign('user', $user);
	    $this->display(DIR."coin.html");
	}

	
	 /**
    *我的抵用券
    *@author liang.li <1184820705@qq.com>
    *@2015年8月27日
    */
    function debit() 
    {
        $cate = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
        );
        $args = $this->get_params();
        $type = isset($args[1]) ? $args[1] : 1;
        $this->assign('type',$type);
        
        $user = $this->visitor->get();
        $info = $this->_user_mod->get_info($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
        $this->formatDebit($user['user_id']);
        $num_list = $this->debitNum($user);
        $lv_info = $this->_lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id=".$user['member_lv_id'],
        ));
        if ($user['member_lv_id'] > 1) 
        {
            $conditons = "(from_uid = {$user['user_id']} OR user_id = {$user['user_id']}) ";
        }
        else 
        {
            $conditons = "user_id = {$user['user_id']} ";
        }
        
        if ($type == 1) 
        {
            $conditons .= "AND is_used = 0 AND is_invalid = 0 ";
        }
        elseif ($type == 2)
        {
            $conditons .= "AND is_used = 1 ";
        }
        elseif ($type == 3)
        {
            $conditons .= "AND is_invalid = 1 ";
        }
        else 
        {
            $this->show_warning('参数错误');
            return;
        }
        $page   =   $this->_get_page(10);    //获取分页信息
        $debit_mod = m('debit');
  //echo $conditons;
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
        if ($user['member_lv_id'] > 1) //=====  格式化转证券的 获得转赠券的人的user_name  =====
        {
            $user_ids = i_array_column($list, 'user_id');
          
            $user_ids = array_unique($user_ids);//=====  去除重复的值  =====
            unset($user_ids[array_search($user['user_id'],$user_ids)]);//=====  去除当前登录人的user_id  =====

            if ($user_ids)
            {
                $user_id_str = implode(",", $user_ids);
                $user_id_list = $this->_user_mod->find(array(
                    'conditions' => "user_id IN ($user_id_str)",
                ));
            }
        }
        
        
        if ($list) 
        {
            foreach ($list as $key => &$value) 
            {
                $value['id'] = ApiAuthcode($value['id'],'ENCODE');
                $value['cate'] = $cate[$value['cate']];
                if ($value['source'] == 'invite') 
                {
                    $value['source'] = '注册券';
                }
                else 
                {
                   $value['source'] = '麦富迪平台发放';
                }
                
                if ($value['from_uid'] &&   $value['user_id']) 
                {
                    $value['user_name'] = $user_id_list[$value['user_id']]['user_name'];
                }
                if($value['expire_time'] - time() > 0) {
                    $days = floor(($value['expire_time'] - time())/86400)+1;
                    $list[$key]['day'] = $days;
                }
            }
      
            //=====    转赠券  =====
        }
       
        $this->assign('type',$type);
        $this->assign('list',$list);
        $this->assign('page_info', $page);
        $this->assign("num_list",$num_list);
        $this->assign('user', $user);
        $this->assign('act',ACT);
        $this->display(DIR.'debit.html');
    }
    
    /**
     *抵用券个数
     *@author liang.li <1184820705@qq.com>
     *@2015年7月17日
     */
    function debitNum($user)
    {
        $user_id = $user['user_id'];
        $debit_mod = m('debit');
        $this->formatDebit($user_id);
         
        if ($user['member_lv_id'] > 1)
        {
            $conditions1 = "(user_id = $user_id OR from_uid = $user_id) AND is_used = 0 AND is_invalid = 0";;
            $conditions2 = "(user_id = $user_id OR from_uid = $user_id) AND is_used = 1";
            $conditions3 = "(user_id = $user_id OR from_uid = $user_id)  AND is_invalid = 1";
        }
        else
        {
        $conditions1 = "user_id = $user_id  AND is_used = 0 AND is_invalid = 0";
        $conditions2 = "user_id = $user_id  AND is_used = 1";
        $conditions3 = "user_id = $user_id  AND is_invalid = 1";
        }
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
             
            $list['notUse'] = $notUse['num'];
            $list['haveUsed'] = $haveUsed['num'];
            $list['haveInvalid'] = $haveInvalid['num'];
            return $list;
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
	*余额明细
	*@author liang.li <1184820705@qq.com>
	*@2015年10月12日
	*/
	function money() 
	{
	    $userInfo = $this->visitor->get();
	    $page    =   $this->_get_page(10);
	    $user_id = $userInfo['user_id'];
	    $contidions = "type = 4 AND user_id=$user_id";
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
	    $this->_format_page($page);
	    //print_exit($page);
	    $this->assign('page_info', $page);
	    $this->assign('minus', $minus);
	    $this->assign("user", $userInfo);
	    $this->assign("list", $cash_list);
	    $this->assign('act',ACT);
	    $this->display(DIR."money.html");
	}
	/**
	 *余额明细
	 *@author tangshoujian <963830611@qq.com>
	 *@2016年1月4日
	 */
	
	function kuka()
	{
	    $arg = $this->get_params();
	    $status = array(1,2,3);
	    $user=$this->visitor->get();
	    $type = isset($arg[1]) ? $arg[1] : 1;
	    $now_time = time();
	    $member = m('member');
	    $_special_code_mod = & m('special_code');
	    $_kukaconfig_mod   = & m('kukaconfig');
	    $this->assign('type',$type);
	 
	    $user_id = $user['user_id'];
	    $con = " and to_id = '{$user_id}' ";//默认消费者
	    if($user['member_lv_id'] >= 2 ) {//创业者
	        $con = " and from_id = '{$user_id}'";
	    }
	    
	    if($user['member_lv_id'] >= 2 ) {
	        if(in_array($type, $status)){
	        switch ($type){
	            case "1":
	                $con .= " and to_id ='' and expire_time>'{$now_time}' ";
	                break;
	            case "2":
	                $con .= " and to_id !='' and expire_time>'{$now_time}' ";
	                break;
	            case "3":
	                $con .= " and expire_time<'{$now_time}' ";
	                break;
	        }
	      }
	    }
	    
	    if($user['member_lv_id'] < 2 ) {//消费者
	        if(in_array($type, $status)){
	        switch ($type){
	            case "1":
	                $con .= " and is_used= 0 and expire_time>'{$now_time}'";
	                break;
	            case "2":
	                $con .= " and is_used=1 and expire_time>'{$now_time}'";
	                break;
	            case "3":
	                $con .= " and expire_time<'{$now_time}' ";
	                break;
	        }
	      }
	    }

	    $page   =   $this->_get_page(8);    //获取分页信息
	    $special_mod = m('special_code');
	    $info = $special_mod->findAll(array(
	        'conditions'=>"cate>19 ".$con,
	        'order' => "id DESC",
	        'limit'         => $page['limit'],  //获取当前页的数据
	        'count'         => true,
	        'index_key' =>'',
	    ));
	    
	    $page['item_count'] = $special_mod->getCount();   //获取统计的数据
	    $this->_format_page($page);
	    
	    foreach($info as $k=>$v)
	    {
	        $info[$k]['donation_mes'] = '';
	        $info[$k]['cate_name']    = '';
	        $info[$k]['is_expire'] = 0;
	        $info[$k]['work_num'] = _format_price_int($v['work_num']);
	        $info[$k]['price']    = _format_price_int($v['price']);
	        if($v['expire_time'] - time() > 0) {
	            $days = floor(($v['expire_time'] - time())/86400)+1;
	            $info[$k]['day'] = $days;
	        }
	        //=====  获取品类名称  =====
	        if ($v['name'])
	        {
	            $info[$k]['cate_name'] = $v['name'];
	        }
	        else
	        {
	            if($info[$k]['cate'] = '22')
	            {
	                $kuka_online = $_kukaconfig_mod->get("id = '{$v['log_id']}'");
	                if ($kuka_online['kuka_name'])
	                {
	                    $info[$k]['cate_name'] = $kuka_online['kuka_name'];
	                }
	                else
	                {
	                    $info[$k]['cate_name'] = "";
	                }
	            }
	            else
	            {
	                $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
	            }
	        }
	        if ($v['content'])
	        {
	            $info[$k]['description'] = $v['content'];
	        }
	        else
	        {
	            $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
	        }
	    
	        $to_id = trim($v['to_id']);
	        if($v['from_id'] && $to_id ) {//已转赠
	            if($user['user_id'] == $v['from_id'] && !empty($to_id) ) {//转赠人回去抵用券信息
	                $memberinfo = $member->get($to_id);
	                $info[$k]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
	            }
	            if($user['user_id'] == $v['to_id']  ) {//被转赠人回去抵用券信息
	                $memberinfo = $member->get($v['from_id']);
	                $info[$k]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
	            }
	        }
	    
	    }
	   
	    $num_list = $this->kukaNum($user);
	    $this->assign('type',$type);
        $this->assign('info',$info);
        $this->assign('page_info', $page);
        $this->assign("num_list",$num_list);
        $this->assign('user', $user);
        $this->assign('act',ACT);
        if($user['member_lv_id'] >= 2){
            
            $this->display(DIR."kuka-cyz.html");
        }else
        {
            $this->display(DIR."kuka-customer.html");
        }
	    
	    
	}
	
	function kukaNum($user){
	    
	    $user_id = $user['user_id'];
	    $_special_code_mod = m('special_code');
	    $this->formatDebit($user_id);
	    $now_time = time();
	    
	    if($user['member_lv_id'] >= 2 ) {
	        // 未使用
	        $unused = $_special_code_mod->get(array(
	            'conditions'=>"cate>19  AND to_id ='' AND from_id = '{$user_id}' AND expire_time>'{$now_time}' ",
	            'fields'       => 'count(*) as unused_number',
	        ));
	        // 已使用
	        $hasused = $_special_code_mod->get(array(
	            'conditions'=>"cate>19  AND to_id !='' AND from_id = '{$user_id}'  AND expire_time>'{$now_time}' ",
	            'fields'       => 'count(*) as hasused_number',
	        ));
	        // 已过期
	    
	        $hasovered = $_special_code_mod->get(array(
	            'conditions'=>"cate>19  AND expire_time< '{$now_time}' AND from_id = '{$user_id}'",
	            'fields'       => 'count(*) as has_over',
	        ));
	    }
	    
	    if($user['member_lv_id'] < 2 ) {//消费者
	        // 未激活
	        $unused = $_special_code_mod->get(array(
	            'conditions'=>"cate>19  and to_id = '{$user_id}' and is_used= 0  and expire_time>'{$now_time}' ",
	            'fields'       => 'count(*) as unused_number',
	        ));
	        // 已激活
	        $hasused = $_special_code_mod->get(array(
	            'conditions'=>"cate>19  and to_id = '{$user_id}' and  is_used=1  and expire_time>'{$now_time}' ",
	            'fields'       => 'count(*) as hasused_number',
	        ));
	        // 已过期
	    
	        $hasovered = $_special_code_mod->get(array(
	            'conditions'=>"cate>19 and to_id = '{$user_id}'  and expire_time< '{$now_time}' ",
	            'fields'       => 'count(*) as has_over',
	        ));
	    
	    }
	    $list['notUse'] = $unused['unused_number'];
	    $list['haveUsed'] = $hasused['hasused_number'];
	    $list['haveInvalid'] = $hasovered['has_over'];
	    
	    return $list;
	  
	}
	
	/**
	 *  激活
	 *
	 * @param  array $arr 消息数组信息
	 *
	 * @return  string
	 */
	public function kukajh()
	{
	    //没有登陆
	    if (!$this->visitor->has_login)
	    {
	        $this->json_error("请先登录再进行操作");
	        return;
	    }
	
	
	    $return = array();
	    $member_mod  = & m('member');
	    $_special_code_mod = & m('special_code');
	    $sn =isset($_REQUEST['sn'])?trim($_REQUEST['sn']):'';
	    $user_info=$this->visitor->get();
	    $user_id = $user_info['user_id'];
	
	    if(!$sn){
	        $this->json_error("麦富迪E卡不能为空");
	        return;
	    }
	    $url = APIURL."/soap/user.php?act=kuka&token=".$user_info['user_token']."&sn=".$sn;
	    $res = file_get_contents($url);//=====  远程请求api接口  =====
	    $res = json_decode($res,true);
	    if (!$res['statusCode'])
	    {
	        $this->json_error($res['error']['msg']);
	        return ;
	    }
	    $sn_info = $_special_code_mod->get("sn = '$sn' ");
	    $result = array(
	        'html'=>'<tr><td>'.$sn_info['name'].'</td><td>￥'.$sn_info['work_num'].'</td><td>'.$sn.'</td><td>'.local_date('Y-m-d H:i:s',$time).'</td></tr>',
	        'msg'=>'恭喜您，已经成功激活麦富迪E卡',
	    );
	
	    $this->json_result($result);
	
	}
	
	
}