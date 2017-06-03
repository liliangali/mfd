<?php

/**
NS ADD 用户中心
*/

class MemberApp extends MemberbaseApp
{
    var $_feed_enabled = false;
    var $_store_id;
    var $_store_mod;
    var $_uploadedfile_mod;
    var $_store_attr_mod;
    var $_auth_web_img;
    var $_mod_member;
    var $_user_message;

    function __construct()
    {
        $this->MemberApp();
    }
    function MemberApp()
    {
        parent::__construct();
    	  include ROOT_PATH.'h5/includes/check.class.php';
        $ms =& ms();
        $this->_feed_enabled = $ms->feed->feed_enabled();
        $this->_store_id  = intval($this->visitor->get("has_store"));
        $this->_store_mod =& m('store');
        $this->_store_attr_mod =& m('store_attr');
        $this->_store_service_mod =& m('store_service');
        $this->_uploadedfile_mod =& m('uploadedfile');
		$this->_mod_member=&m('member');
		$this->_lv_mod =& m('memberlv');
		$this->_user_message=&m('usermessage');
        $this->assign('feed_enabled', $this->_feed_enabled);

        $this->_auth_web_img = SITE_URL."/upload/auth/";
    }

    /**
     * 默认首页
     */
    public function index()
    {

      $user = $_SESSION['user_info'];
      if (!$user['user_id'])
      {
         /*  if ($_SESSION['openid'] && !$this->visitor->has_login) //=====  解决session丢失的问题  =====
          {
              $openid = $_COOKIE['openid'];
              $mod = m('member');
              $user = $mod->get("openid = '$openid'");
              if ($user)
              {
                  $this->_do_login($user['user_id'],$user['user_name'],$user['password']);
              }
          } */
      }
      //徽章数字
      //券的数目
        $time = time();
        $conditons = "binding_user_id = {$user['user_id']} ";
        $conditons .= "AND use_status = 0 AND end_time > $time ";
        $debit_mod=&m("voucher");
        $list = $debit_mod->get(array(
            'conditions' => $conditons,
            'fields' => "count(*) as num"
        ));
        $this->assign('debit_num',$list['num']);

        $model_collect =& m('collect');
        $collect_custom = $model_collect->get(array(
            'conditions' => "user_id = " . $user['user_id'] ." and type= 'goods'",
            'fields' => "count(*) as num"
        ));
        $this->assign('collent_num',$collect_custom['num']);




	  $order_goods=m('ordergoods');
	   $order_mod = m('order');
        $conditions = "user_id='{$user['user_id']}'  ";
      
        $order_count_unpay = $order_mod->get(array(
            'conditions' => $conditions." AND status = ".ORDER_PENDING,
            'fields' => "count(*) as num"
        ));

     
        $order_count_shipped = $order_mod->get(array(
            'conditions' => $conditions." AND (status = ".ORDER_SHIPPED.")",
            'fields' => "count(*) as num"
        ));

       //待评价个数
	   $orderids=$order_mod->find(array(
		       'conditions'=>'status=40 AND user_id='.$user['user_id'],
		 ));
		 if($orderids){
			 $orderidss= array_column($orderids, 'order_id');
			 $orderid=implode(',',$orderidss);
		 }
		if($orderid){
			$ordergoods=$order_goods->find(array(
		       'conditions'=>'comment =0 AND order_id in ('.$orderid.')',
			   
		));
		}
		
        $order_count['order_count_assess'] =count($ordergoods);
        $order_count['order_count_unpay'] = $order_count_unpay['num'];
        $order_count['order_count_shipped'] = $order_count_shipped['num'];
      
        $this->assign('order_count_num',$order_count);
      $user_mod =& m('member');
      $info = $user_mod->get(array(
          'conditions' => "user_id='{$user['user_id']}'",
              'fields'	=> "*",
      ));
      $lv = $this->_lv_mod->get(array(
          'conditions' => 'member_lv_id='.$info['member_lv_id'],
          //'fields' => 'name',
      ));
      require(ROOT_PATH . '/h5/includes/avatar.class.php');     //基础控制器类
      $objAvatar = new Avatar();
      //获取头像
      $avatar = $objAvatar->avatar_show($user['user_id'],'big');
	  if($info['avatar'])
	  {
		  $avatar = $info['avatar'];
	  }	
		
      $this->assign('avatar', $avatar);

      //获取邀请人
      // $member_invite = m("memberinvite");
      // $invite_info = $member_invite->get("invitee='".$user['user_id']."'");
      // $user['invite_name'] = empty($invite_info)?'':$invite_info['nickname'];

      //判断是否有未读消息
      $messages='';
      $messages=$this->_user_message->find(array(
      		'conditions'=>"to_user_id='{$user['user_id']}' and is_read=0"
      ));
      $this->assign('has_message',!empty($messages)?1:0);
      
      $order = m("order");
      $order_info = $order->get(array(
         'conditions' => "user_id='{$user['user_id']}'",
         'fields'     => "count(*) as num",
      ));
      $this->assign("order_num",$order_info['num']);
      //ns add 当前app，用于前端样式的判断
      $this->assign('app',APP);
      $this->assign('title','用户中心');
      $this->assign('lv',$lv);
      $this->assign('name','用户中心');
      $this->assign('user', $user);
      $this->display('member.index.html');
    }
    /*激活优惠券
     */
    function jihuo(){
        $debit_mod=&m("voucher");
        $batch_mod=&m("voucher_batch");
      
        $actnum = isset($_POST['code']) ? strtoupper($_POST['code']) : 0;//激活码
        $user_info = $_SESSION['user_info'];
        if (!$user_info)
        {
            $this->json_error('账号不存在');
        				return ;
        }
        if(!$_POST){
    
            $this->display('member.jihuo.html');
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
            $use_debit=$debit_mod->get(array(
            'conditions' =>"code='{$actnum}' AND binding_user_id != 0",
            ));
            if($use_debit){
                $this->json_error('券已被激活！');
                return;
            }
            $debit=$debit_mod->get(array(
                'conditions' =>"code='{$actnum}' AND use_status=0 AND start_time < '{$time}'  AND  '{$time}' < end_time  AND binding_user_id=0",
            ));
    
            if(!$debit){
                $this->json_error('券不存在或已失效！');
                return;
            }else{
                if($debit['batch_id']){
                    $debit1=$batch_mod->get(array(
                        'conditions' =>" status=1 AND id='{$debit['batch_id']}'",
                    ));
                    if(!$debit1){
                        $this->json_error('批次日志不存在');
                        return;
                    }
                }
                
            }
           
            $datt=array(
                'binding_user_id'=>$user_info['user_id'],
                'binding_time'=>time(),
                'binding_username'=>$user_info['user_name'],
                'source'=>1,
            );
             
            if(!$debit_mod->edit("code='{$actnum}'",$datt)){
            
                $this->json_error('券激活失败');
                return;
            }

            $this->json_result();
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
    /*我的优惠券
     */
    function quan(){
         
        $args = $this->get_params();
        $type = $args[0];
        $this->assign('type',$type);
         $member=&m('member');
         $lv_mod =& m('memberlv');
        $user = $_SESSION['user_info'];
        $info =  $member->get($user['user_id']);
        $user['portrait'] = portrait($user['user_id'], $info['portrait'], 'middle');
       // $this->formatDebit($user['user_id']);
        $num_list = $this->debitNum($user);
       
       /* $lv_info = $lv_mod->get(array(
            'conditions' => "lv_type = 'supplier' and member_lv_id= {$user['member_lv_id']} ",
        ));*/
        $conditons = "binding_user_id = {$user['user_id']} ";
       
        $time=time();
        if ($type == 1)
        {
            $conditons .= "AND use_status = 0 AND end_time > $time ";
			
        }
        elseif ($type == 2)
        {
            $conditons .= " AND use_status = 1 ";
        }
        elseif ($type == 3)
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
           // 'limit'         => $page['limit'],  //获取当前页的数据
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
               // $datetimes=ceil($datetime/3600/24);
				   if($datetime>0){
			    $datetimes=ceil($datetime/3600/24);
		           }else{
			    $datetimes='';
		           }
                $list[$key]['datetime']=$datetimes;
                $value['id'] = ApiAuthcode($value['id'],'ENCODE');
                $list[$key]['start_time'] = date("Y/m/d",$value['start_time']);
                $list[$key]['end_time'] = date("Y/m/d",$value['end_time']);
               
                if($value['category']=='1'){
                    $value['category'] = '限定制商品使用';
                }elseif($value['category']=='2'){
                    $value['category'] = '限普通商品使用';
                }else{
                    $value['category'] = '通用';
                }
                if($value['order_money'] > 0 && $value['source'] == 3)
                {
                    $value['category'] = '狗民网专属券满50元可用';
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
   
	   $this->assign("xianshi", $args[0]);
        $this->assign("status", $args[1]);
        $this->assign("num_list",$num_list);
        $this->assign('user', $user);
        $this->assign('act',ACT);
    
        $this->display('member.quan.html');
    }
/**
     *个人资料
     *@author shaozizhen
     *@2016.1.4
     */
    function person()
    {
    	$mod = m('member');
    	$sex = array(0=>'未知',1=>'男',2=>'女');
    
    	$user = $mod->get_info($this->visitor->get('user_id'));

    	/* $user_id=1611;
    	$user=$mod->get_info($user_id); */
    	$user_name = $user['user_name'];
    	if (!IS_POST)
    	{   // 查看是否有最新量体数据 状态是已量体完成


			require(ROOT_PATH . '/h5/includes/avatar.class.php');     //基础控制器类
			$objAvatar = new Avatar();
			//获取头像
			$avatar = $objAvatar->avatar_show($user['user_id'],'big');
			if($user['avatar'])
			{
				$avatar = $user['avatar'];
			}


			// $user['gender'] = $sex[$user[gender]];
    	    $user['avatar'] = $avatar;



			$user['erweima_url'] = pc_url().$user['erweima_url'];
            $this->assign('sex',$sex);
    		$this->assign('user',$user);
    		$this->display('member.persons.html');
    	}
    	else
    	{
    		/* if ($_POST['phone_mob'])
    		{
    			if(!preg_match("/^1[34578]\d{9}$/", $_POST['phone_mob']))
    			{
    				$this->show_warning('请输入正确的手机号');
    				return ;
    			}
    		}

    		if ($_POST['email'])
    		{
    			if (!preg_match("/^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$/",$_POST['email']))
    			{
    				$this->show_warning('请输入正确的邮箱');
    				return ;
    			}
    		} */
     		if ($_POST['phone_mob'])
    		{
    			$data['phone_mob'] = $_POST['phone_mob'];
    		}
    		/*if ($_POST['im_qq'])
    		{
    			$data['im_qq'] = $_POST['im_qq'];
    		}
    		if ($_POST['wechat'])
    		{
    			$data['wechat'] = $_POST['wechat'];
    		}
    		if ($_POST['email'])
    		{
    			$data['email'] = $_POST['email'];
    		}
    		if ($_POST['height'])
    		{
    			$data['height'] = $_POST['height'];
    		}
    		if ($_POST['weight'])
    		{
    			$data['weight'] = $_POST['weight'];
    		}
    		if ($_POST['birthday'])
    		{
    			$data['birthday'] = $_POST['birthday'];
    		} */
    	    if ($_POST['email'])
            {
                $data['email'] = $_POST['email'];
            }
    	    if ($_POST['nickname'])
    	    {
    	        $data['nickname'] = $_POST['nickname'];
    	    }
            if ($_POST['gender'])
            {
                $data['gender'] = $_POST['gender'];
            }

    		if ($data)
    		{
    			$res=$mod->edit($this->visitor->get('user_id'),$data);
                if($res){
                    foreach ($data as $key => $value) {
                        $_SESSION['user_info'][$key]=$value;
                    }
                }
    		}

    		$this->show_message('修改成功','跳回用户中心','/member.html');
    		return ;
    	}
    }
    /**
     *我的顾客
     *@author shaozizhen
     *@2016.1.19
     */
    function customer()
    {
    	$mod = m('member');
    	$figure_mod = & m('customer_figure');
    	$user = $mod->get_info($this->visitor->get('user_id'));
    	$user_name = $user['user_name'];
    	$sourcename = isset($_GET['sourcename']) ? trim($_GET['sourcename']) : '';
    	if($sourcename){
    		$conditions=" AND ( customer_name LIKE '%{$_GET['sourcename']}%' or customer_mobile LIKE '%{$_GET['sourcename']}%' or remark LIKE '%{$_GET['sourcename']}%' or remark_phone LIKE '%{$_GET['sourcename']}%' )";
    	}
    	  // 查看是否有最新量体数据 状态是已量体完成
    			$figure_all = $figure_mod->find(array(
					'conditions' => "storeid = '{$user['user_id']}' AND type_cus <> 0 AND userid <> '{$user['user_id']}' AND customer_name <> ''".$conditions,
					'fields'	 => "figure_sn, customer_mobile,customer_name,lasttime,storeid,userid,remark,remark_phone",
					//'order'      => "customer_name DESC",
					//'limit'	     => $limit,
			        'index_key'  => "",
			));
    			$custos = array();
    			if($figure_all){
    				foreach ($figure_all as $key=>$row) {
    					if($row['userid']){
    						$members=$mod->get(array(
    								'conditions'=>"user_id='{$row['userid']}'",
    								'fields'=>"avatar",
    						));
    						if($members['avatar']){
    							$figure_all[$key]['avatar']=LOCALHOST1.$members['avatar'];
    						}else{
    							$figure_all[$key]['avatar']=LOCALHOST1."/upload/../avatar/noavatar_big.gif";
    						}
    					}else{
    						$figure_all[$key]['avatar']=LOCALHOST1."/upload/../avatar/noavatar_big.gif";
    					}

    					$customers = $figure_all[$key];
    					$customer_name=$row['customer_name'];
    					$snameFirstc = $this->_getFirstc($customer_name);
    					$custos[$snameFirstc][$row['figure_sn']] = $customers;
    				}
    			}
    			if($custos)
    			{
    				ksort($custos);//对数据进行ksort排序，以key的值升序排序
    			}
    			//var_dump($custos);exit;
    			//ksort(custos); //对数据进行ksort排序，以key的值升序排序
    		$this->assign('figure_all',$figure_all);
    		$this->assign('custos',$custos);
    		$this->display('member.customer.html');

    }
    //顾客详情
    function concustomer(){
    	$mod = m('member');
    	$figure_mod = & m('customer_figure');
    	$order_mod=m("order");
    	$user_id= intval($_GET['user_id']);
    	$figure_sn= intval($_GET['figure_sn']);
    	$member_invite= m('memberinvite');
    	$phone=$figure_mod->get(array(
    			'conditions'=>"figure_sn='{$figure_sn}'",
    			'fields'=>'customer_mobile,userid,customer_name,remark,remark_phone',
    	));
    	$this->assign('user_id',$user_id);

    	$this->assign('phone',$phone['customer_mobile']);
    	$this->assign('figure_sn',$figure_sn);
    	if($user_id)
    	{
    		$members=$mod->get(array(
    				'conditions'=>"user_id='{$user_id}'",
    				'fields'  =>"user_id,nickname,user_name,gender,birthday,last_login,final_amount_num,avatar,order_num",
    		));


    	}
    	 if($phone['remark']){
    		$members['nickname']=$phone['remark'] ? $phone['remark'] : '';
    		$members['user_name']=$phone['remark_phone'] ? $phone['remark_phone'] : '';
    	}else{
    		$members['nickname']=$phone['customer_name'] ? $phone['customer_name'] : '';
    		$members['user_name']=$phone['customer_mobile'] ? $phone['customer_mobile'] : '';
    	}
    	if(!$user_id){
    		$users = $mod->get_info($this->visitor->get('user_id'));
    		$users['erweima_url']=LOCALHOST1."/".$users['erweima_url'];
    		$this->assign('user',$users);
    		$this->assign('customer',$members);
    	}
    	if(empty($members['birthday'])){
    		$members['birthday']='未知';
    	}

    	if(!empty($members['last_login'])){
    		$members['last_login']=date('Y-m-d',$members['last_login']);
    	}else{
    		$members['last_login']= '' ;
    	}

    	$invites=$member_invite->get(array(
    			'conditions'=>"invitee='{$phone['userid']}'",
    			'fields'  =>"add_time",
    	));
    	if(!empty($members['avatar'])){
    		$members['avatar']= LOCALHOST1.'/upload/avatar/'.$members['avatar'];
    	}else{
    		$members['avatar']=LOCALHOST1."/upload/../avatar/noavatar_big.gif";
    	}
    	if(!empty($invites['add_time'])){
    		$members['add_time']=date('Y-m-d',$invites['add_time']);
    	}else{
    		$members['add_time']=0;
    	}

    	$counts = $figure_mod->find(array(
    			'conditions' =>"customer_mobile='{$phone['customer_mobile']}' AND figure_state=1 AND id_serve !=0 group by liangti_id,customer_mobile",
    			'fields'	 => "figure_sn",
    	));
    	$count=count($counts);
    	if(!empty($count)){
    		$members['state']=1;
    		$members['liangti_num']=$count;
    	}else{
    		$members['state']=0;
    		$members['liangti_num']=0;
    	}

    	if($members['user_id'])
    	{
    		$orders=$order_mod->get(array(
    				'conditions' =>"user_id='{$members['user_id']}' AND (status = 40 or status = 30 or status = 60)",
    				'fields'    =>"sum(final_amount) as num1,sum(money_amount) as num2,sum(coin) as num3,count(order_id) as count",
    				'index_key' =>"",
    		));
    		if($orders)
    		{
    			$orders['num']=$orders['num1'] + $orders['num2'] + $orders['num3'];
    			$members['final_amount_num']=$orders['num'] ? $orders['num'] : '0';
    			$members['order_num']=$orders['count'] ? $orders['count'] : '0';
    		}
    	}
    	$this->assign('members',$members);
    	$this->display('member.concustomer.html');
    }
    //添加顾客
    function addcustomers(){
    	$name=$_POST['name'];
    	$phone=$_POST['phone'];
    	$customer_mod    = m('customer_figure');
    	$mod          = m('member');
    	$member_invite= m('memberinvite');
    	$user = $mod->get_info($this->visitor->get('user_id'));
    	if($_POST){
    		if(!$name)
    		{
    			$this->json_error('昵称不能为空');
    			return;
    		}
    		if(!$phone)
    		{
    			$this->json_error('手机号不能为空');
    			return;
    		}
    		if(!preg_match('/^1[3458][0-9]{9}$/', $phone))
    		{
    			$this->msg('手机号格式错误');
    			//$this->json_error('手机号格式错误');
    			return;
    		}
    		$storeid=$user['user_id'];

    		$type_cus=1;
    		if (!$customer_mod->unique($phone,$storeid,$type_cus))
    		{
    			$this->json_error('手机号已存在!');
    			return;
    		}
    		$mems=$mod->get(array(
    				'conditions' =>"serve_type <> 2 AND user_name='{$phone}'",
    				'fields'  =>"user_id",
    		));
    		if($mems){
    			$invites=$member_invite->get(array(
    					'conditions'=>"invitee='{$mems['user_id']}'",
    			));
    			if($invites){
    				$this->json_error('该顾客已被邀请过!');
    				return;
    			}

    		}
    		$data=array(
    				'customer_name' => $name,
    				'customer_mobile' =>$phone,
    				'storeid' =>$user['user_id'],
    				'type_cus' =>1,
    				'firsttime' => time(),
    		);
    		if($mems && !$invites){
    			$data['userid']=$mems['user_id'];
    		}
    		$cusmod=$customer_mod->add($data);

    		if($cusmod === false){
    			$this->json_error('添加失败!');
    			return;
    		}
    		$this->json_result();
    	}else{
    		$this->display('member.addcustomer.html');
    	}
    }

    //量体列表
    function figurelists(){
    	$phone=$_GET['phone'];
    	$serve=m("serve");
       if($phone){
				$sql="SELECT figure_sn,liangti_id,id_serve,lasttime,liangti_name,service_mode FROM(select * from cf_customer_figure where customer_mobile = '{$phone}' and figure_state = 1 and id_serve !=0 ORDER BY figure_sn DESC) AS T
                      GROUP BY T.liangti_id,T.customer_mobile ORDER BY T.figure_sn DESC";
				$db = &db();
				$r=$db->query($sql);
				$figure = array();
				while($row=@mysql_fetch_assoc($r)){
					$figure[]=$row;
				}
			}
			if($figure){
			    $ins=0;
				foreach($figure as $key=>$val){

					$serves=$serve->get(array(
							'conditions' =>"idserve='{$val['id_serve']}'",
							'fields' =>"linkman,serve_name",
					));

					if(!empty($val['liangti_name'])){
						$figures[$ins]['name']=$val['liangti_name'];
					}else{
						if(!empty($val['liangti_id']))
						{
							$mems=$mem_mod->get(array(
									'conditions'=>"user_id='{$val['liangti_id']}'",
									'fields'=>"real_name",
							));
							if(!$mems['real_name']){
								if($val['service_mode']== 4){
									$figures[$ins]['name']=$val['liangti_name'];
								}else{
									$figures[$ins]['name']='';
								}

							}else{
								$figures[$ins]['name']=$mems['real_name'];

							}
						}else{
						if(!$serves['linkman']){
								$figures[$ins]['name']='';
							}else{
								$figures[$ins]['name']=$serves['linkman'];
							}
						}
					}

						if(!$serves['serve_name']){

							if($val['service_mode']== 4){

								$figures[$ins]['serve_name']='后台录入';

							}else{
								$figures[$ins]['serve_name']='';
							}

						}else{
							$figures[$ins]['serve_name']=$serves['serve_name'];
						}
					if(!$val['lasttime']){
						$figures[$ins]['lasttime']=0;
					}else{
					$figures[$ins]['lasttime']=date('Y-m-d H:i:s',$val['lasttime']);
					}
					$figures[$ins]['figure_sn']=$val['figure_sn'];
					$ins +=1;
				}

			}
			$this->assign('figures',$figures);
    	$this->display('member.figurelist.html');
    }
    //量体列详情
    function figurecontents(){
    	$member_figure_mod = m('customer_figure');
    	$order_mod        = m('order');
    	$figure_sn=$_GET['figure_sn'];
    	if($figure_sn){
    		$figure=$member_figure_mod->get(array(
    				'conditions'  => "figure_sn='{$figure_sn}' AND figure_state=1",
    		));
    	}

    	if($figure){
    		$all_order_num = $order_mod->get(array(
    				'conditions' => "user_name = {$figure['customer_mobile']} and status != 0 and extension = 'news' ",
    				'fields'	 => "count(order_id) as count",
    		));
    		$worker = array(
    				'customer_name' => $figure['customer_name'],
    				'customer_mobile'   => $figure['customer_mobile'],
    				'order_num'   => !empty($all_order_num['count']) ? $all_order_num['count'] : 0,
    				'lasttime'   => date('Y-m-d',$figure['lasttime']),
    				'firsttime'   => $figure['firsttime'],
    				'height'  => $figure['height'],
    				'weight'  => $figure['weight'],
    		);
    		$_member_figure_mod = & m('customer_figure');
    		$figure_info = $this->_figure_positions($figure);

    		//获得风格特体
    		$json_data = file_get_contents('http://api.figure.mfd.cn/soap/club.php?act=getSpecialFields');
    		$json_data = json_decode($json_data,true);
    		if(!empty($figure)) {
    			//特殊体形
    			if ($json_data['result']['data']['public']) {
    				foreach ($json_data['result']['data']['public'] as $pvalue) {

    					if ($figure[$pvalue['value_name']]) {
    						foreach($pvalue['item'] as $ipvalue) {
    							if($ipvalue['value'] == $figure[$pvalue['value_name']]) {
    								$feature[] = array(
    										'name' => $pvalue['cateName'],
    										'val'  => $ipvalue['name'],
    								);

    							}
    						}
    					}
    				}
    			}
    			//风格
    			if ($json_data['result']['data']['special']) {
    				foreach ($json_data['result']['data']['special'] as $svalue) {
    					if ($figure[$pvalue['value_name']]) {
    						foreach($svalue['item'] as $isvalue) {
    							if($isvalue['value'] == $figure[$svalue['value_name']]) {
    								$style[] = array(
    										'name' => $svalue['cateName'],
    										'val'  => $isvalue['name'],
    								);

    							}
    						}

    					}
    				}
    			}
    		}

    	}
    	$this->assign('figure_info',$figure_info);
    	$this->assign('feature',$feature);
    	$this->assign('style',$style);
    	$this->assign('worker',$worker);
    	$this->display('member.figurecontent.html');
    }
    //备注信息
    function remarks(){
    	$mod = m('member');
    	$figure_mod = & m('customer_figure');
    	$order_mod=m("order");
    	$figure_sn= intval($_GET['figure_sn']);
    		$figures=$figure_mod->get(array(
    				'conditions'=>"figure_sn='{$figure_sn}'",
    				'fields'=>"remark,remark_phone",
    		));
    		$this->assign('figure',$figures);
    	if($_POST){
    		$remark= trim($_POST['remark']);
    		$remark_phone= trim($_POST['remark_phone']);
    		if($remark){
    			$data['remark']=$remark;
    		}
    		if($remark_phone){
    			$data['remark_phone']=$remark_phone;
    		}
    		if($figure_sn && $data){
    			$remarks=$figure_mod->edit($figure_sn,$data);
    			if(!$remarks){
    				$this->json_error('修改备注失败!');
    				return;
    			}
    		}
    		$this->json_result();
    	}else{
    		$this->display('member.remark.html');
    	}

    }
    /**
     * 取汉字的第一个字的首字母
     * @param type $str
     * @return string|null
     * shaozz
     */
    public function _getFirstc($str){
    	if(empty($str)){return '';}
    	$fchar=ord($str{0});

    	if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    	$s1 = iconv("UTF-8", "GBK//IGNORE", $str);
    	//$s1=iconv('UTF-8','GB2312',$str);
    	//$s2=iconv('gb2312','UTF-8',$s1);
    	$s2=iconv('gbk','UTF-8',$s1);
    	$s=$s2==$str?$s1:$str;
    	$asc=ord($s{0})*256+ord($s{1})-65536;
    	if($asc>=-20319&&$asc<=-20284) return 'A';
    	if($asc>=-20283&&$asc<=-19776) return 'B';
    	if($asc>=-19775&&$asc<=-19219) return 'C';
    	if($asc>=-19218&&$asc<=-18711) return 'D';
    	if($asc>=-18710&&$asc<=-18527) return 'E';
    	if($asc>=-18526&&$asc<=-18240) return 'F';
    	if($asc>=-18239&&$asc<=-17923) return 'G';
    	if($asc>=-17922&&$asc<=-17418) return 'H';
    	if($asc>=-17417&&$asc<=-16475) return 'J';
    	if($asc>=-16474&&$asc<=-16213) return 'K';
    	if($asc>=-16212&&$asc<=-15641) return 'L';
    	if($asc>=-15640&&$asc<=-15166) return 'M';
    	if($asc>=-15165&&$asc<=-14923) return 'N';
    	if($asc>=-14922&&$asc<=-14915) return 'O';
    	if($asc>=-14914&&$asc<=-14631) return 'P';
    	if($asc>=-14630&&$asc<=-14150) return 'Q';
    	if($asc>=-14149&&$asc<=-14091) return 'R';
    	if($asc>=-14090&&$asc<=-13319) return 'S';
    	if($asc>=-13318&&$asc<=-12839) return 'T';
    	if($asc>=-12838&&$asc<=-12557) return 'W';
    	if($asc>=-12556&&$asc<=-11848) return 'X';
    	if($asc>=-11847&&$asc<=-11056) return 'Y';
    	if($asc>=-11055&&$asc<=-10247) return 'Z';
    	return null;
    }
    function getFigurebyuser()
    {

        $mem_mod = & m('member');
        $figure_mod = & m('customer_figure');
        $serve_mod  = & m('serve');
        $figurecus_mod = & m('figurecus');

        $user = $mem_mod->get_info($this->visitor->get('user_id'));
        $user_name = $user['user_name'];
           // 这里需要 筛选出 最晚更新的那条量体数据
        $figure = $figure_mod->get(array(
            'conditions'=> "customer_mobile = '{$user_name}' AND figure_state=1",
            'order' => "lasttime DESC ",

        ));


        $s_info = $serve_mod->get("idserve ={$figure['id_serve']} ");

        if($figure['liangti_id']){
            $liangti_info = $mem_mod->get("user_id={$figure['liangti_id']}");
            $lt_name      = $liangti_info['real_name'];

        }else{

            $liangti_info = $serve_mod->get("idserve={$figure['id_serve']}");
            $lt_name      = $liangti_info['linkman'];
        }

        $figure_info = $this->_figure_positions($figure);
        $style   = array();//风格
        $feature = array();//特体
        //获得风格特体
        if(!empty($figure)) {
            //处理获得体型和风格
            $_get_body_type = $this->_get_body_type(3);

            //处理风格数据
            foreach($_get_body_type['style'] as $style_key => $style_val) {
                $nm = $style_val['info']['nm'];

                foreach($style_val['list'] as $lkey => $lval ) {
                    if( $lkey == $figure[$nm]) {
                        $style[] = array(
                            'name' => $lval['clothName'],
                            'val'  => $lval['name'],
                        );
                    }
                }
            }

            //处理特体数据
            foreach($_get_body_type['feature'] as $feature_key => $feature_val) {
                $nm = $feature_val['info']['nm'];

                foreach($feature_val['list'] as $fkey => $fval ) {
                    if( $fkey == $figure[$nm]) {
                        $feature[] = array(
                            'name' => $fval['cateName'],
                            'val'  => $fval['name'],
                        );
                    }
                }
            }
        }

        $lasttime = date("Y-m-d", $figure['lasttime']);
        $this->assign('lasttime',$lasttime);
        $this->assign('figure',$figure_info);
        $this->assign('feature',$feature);
        $this->assign('style',$style);
        $this->assign('serve_name',$s_info['serve_name']);
        $this->assign('liangti_name',$lt_name);

        $this->display('member.figure.html');






    }

    /**
     * 处理量体数据返回像   22项量体信息
     *
     * @param array  $figure 量体数据
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function _figure_positions($figure)
    {
        $_mod_member_figure = &m('customer_figure');
        $_figure = $_mod_member_figure->_positions();

        $unit = $figure['unit'];
        $wunit = 'kg';

        //重新处理返回数据
        $figure_info = array(
            array('name' => $_figure['lw']['zname'], 'val' => !empty($figure['lw']) ? $figure['lw'].$unit: '0'.$unit),
            array('name' => $_figure['xw']['zname'], 'val' => !empty($figure['xw']) ? $figure['xw'].$unit: '0'.$unit),
            array('name' => $_figure['zyw']['zname'], 'val' => !empty($figure['zyw']) ? $figure['zyw'].$unit: '0'.$unit),
            array('name' => $_figure['tw']['zname'], 'val' => !empty($figure['tw']) ? $figure['tw'].$unit: '0'.$unit),
            array('name' => $_figure['zww']['zname'], 'val' => !empty($figure['zww']) ? $figure['zww'].$unit: '0'.$unit),
            array('name' => $_figure['yww']['zname'], 'val' => !empty($figure['yww']) ? $figure['yww'].$unit: '0'.$unit),
            array('name' => $_figure['sbw']['zname'], 'val' => !empty($figure['sbw']) ? $figure['sbw'].$unit: '0'.$unit),
            array('name' => $_figure['zjk']['zname'], 'val' => !empty($figure['zjk']) ? $figure['zjk'].$unit: '0'.$unit),
            array('name' => $_figure['zxc']['zname'], 'val' => !empty($figure['zxc']) ? $figure['zxc'].$unit: '0'.$unit),
            array('name' => $_figure['yxc']['zname'], 'val' => !empty($figure['yxc']) ? $figure['yxc'].$unit: '0'.$unit),
            array('name' => $_figure['qjk']['zname'], 'val' => !empty($figure['qjk']) ? $figure['qjk'].$unit: '0'.$unit),
            array('name' => $_figure['hyjc']['zname'], 'val' => !empty($figure['hyjc']) ? $figure['hyjc'].$unit: '0'.$unit),
            array('name' => $_figure['hyc']['zname'], 'val' => !empty($figure['hyc']) ? $figure['hyc'].$unit: '0'.$unit),
            array('name' => $_figure['qyj']['zname'], 'val' => !empty($figure['qyj']) ? $figure['qyj'].$unit: '0'.$unit),
            array('name' => $_figure['yw']['zname'], 'val' => !empty($figure['yw']) ? $figure['yw'].$unit: '0'.$unit),
            array('name' => $_figure['tgw']['zname'], 'val' => !empty($figure['tgw']) ? $figure['tgw'].$unit: '0'.$unit),
            array('name' => $_figure['td']['zname'], 'val' => !empty($figure['td']) ? $figure['td'].$unit: '0'.$unit),
            array('name' => $_figure['hyg']['zname'], 'val' => !empty($figure['hyg']) ? $figure['hyg'].$unit: '0'.$unit),
            array('name' => $_figure['qyg']['zname'], 'val' => !empty($figure['qyg']) ? $figure['qyg'].$unit: '0'.$unit),
            array('name' => $_figure['zkc']['zname'], 'val' => !empty($figure['zkc']) ? $figure['zkc'].$unit: '0'.$unit),
            array('name' => $_figure['ykc']['zname'], 'val' => !empty($figure['ykc']) ? $figure['ykc'].$unit: '0'.$unit),
            array('name' => $_figure['xiw']['zname'], 'val' => !empty($figure['xiw']) ? $figure['xiw'].$unit: '0'.$unit),
            array('name' => $_figure['jk']['zname'], 'val' => !empty($figure['jk']) ? $figure['jk'].$unit: '0'.$unit),
            array('name' => $_figure['dkzkc']['zname'], 'val' => !empty($figure['dkzkc']) ? $figure['dkzkc'].$unit: '0'.$unit),
            array('name' => $_figure['dkykc']['zname'], 'val' => !empty($figure['dkykc']) ? $figure['dkykc'].$unit: '0'.$unit),
            array('name' => $_figure['lheight']['zname'], 'val' => !empty($figure['height']) ? $figure['height'].$unit: '0'.$unit),
            array('name' => $_figure['lweight']['zname'], 'val' => !empty($figure['weight']) ? $figure['weight'].$wunit: '0'.$wunit),
        );
        return $figure_info;
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
    public function  _get_body_type($clothId){
        $_mod_mtm_bt = &m("mtmbodytype");
        $body_type_tm = $_mod_mtm_bt->find("clothId != '0'");

        foreach ($body_type_tm as $row){
                $body_type['style'][$row['clothId']]['info']['name'] = $row['clothName'];
                $body_type['style'][$row['clothId']]['info']['id']   = $row['cateID'];
                $body_type['style'][$row['clothId']]['info']['nm']   = 'body_type_'.$row['clothId'];
                $body_type['style'][$row['clothId']]['list'][$row['id']] = $row;

        }

        $body_type_ts = $_mod_mtm_bt->find("clothId = '0'");
        foreach ($body_type_ts as $row){

            $body_type['feature'][$row['cateID']]['info']['name'] = $row['cateName'];
            $body_type['feature'][$row['cateID']]['info']['id']   = $row['cateID'];
            $body_type['feature'][$row['cateID']]['info']['nm']   = 'body_type_'.$row['cateID'];
            $body_type['feature'][$row['cateID']]['list'][$row['id']] = $row;

        }
        return $body_type;
    }



    function readLabel(){
       $id = intval($_GET['item']);
       $message_mod = &m("usermessage");
       $message_mod->edit("id='{$id}' && to_user_id='{$this->visitor->get("user_id")}'",array("is_read" => 1));
       $this->json_result();
       die();
    }

    function dropMsg(){
       $ids = trim($_GET['ids']);
       $idsArr = explode(",", $ids);
       if(empty($idsArr)){
           die($this->json_error('非法操作'));
       }

       foreach($idsArr as $key => $val){
           $idsArr[$key] = intval($val);
       }

       $message_mod = &m("usermessage");
       $conditions = "id ".db_create_in($idsArr) . " && to_user_id='{$this->visitor->get("user_id")}'";
       $message_mod->drop($conditions);
       $this->json_result();
       die();
    }

    /**
    *用户详情
    *@author liang.li <1184820705@qq.com>
    *@2015年9月2日
    */
    function info()
    {
        if (!IS_POST)
        {
            $user = $this->visitor->get();
            $this->assign('user',$user);
            $this->display('member.info.html');
        }
    }


      /**
     * 上传文件
     *
     */
    function _upload_files()
    {
        import('uploader.lib');
        $data      = array();
        /* store_logo */
        $file = $_FILES['store_logo'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            //$uploader->allowed_size(SIZE_STORE_LOGO); // 20KB
            $uploader->allowed_size('1024000'); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['store_logo'] = $uploader->save('upload_user_photo/files/store_' . $this->_store_id . '/other', time());
        }

        /* fw_logo */
        $file = $_FILES['fw_logo'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size('1024000'); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['fw_logo'] = $uploader->save('upload_user_photo/files/store_' . $this->_store_id . '/other', time());
        }

        //ns add banner
        $file = $_FILES['banner'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size('1024000'); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['banner'] = $uploader->save('upload_user_photo/files/store_' . $this->_store_id . '/other', time());
        }

        //print_r($_FILES); exit;
        return $data;
    }
        /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;
        $file = $this->_uploadedfile_mod->get($file_id);
        if ($file_id && $file['store_id'] == $this->visitor->get('manage_store') && $this->_uploadedfile_mod->drop($file_id))
        {
            $this->json_result('drop_ok');
            return;
        }
        else
        {
            $this->json_error('drop_error');
            return;
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
        $ucmember_mod = m('ucmember');
        //缺省值 显示找回密码页面
        if($opt){
            $type = $_REQUEST['account_type'];
            if(!$type)
            {
               
                $this->json_error('找回密码类型为空');
            }
    
            //此处为了融合处理
            $account = ($_REQUEST['account']) ? $_REQUEST['account'] :$_REQUEST['email'] ;
            if(!$account)
            {
               
                $this->json_error('手机号为空');
            }
    
            //验证手机验证码;发送找回密码邮件
            if($opt == 2){
                if('phone' == $type){
                    $code = $_REQUEST['authcode'];
                 
                    $ucinfo = $ucmember_mod->get("username='{$account}'");
                    $uc_id = $ucinfo['uid'];
                    if (empty($ucinfo))
                    {
                      
                        $this->json_error('该手机号并未绑定任何账号');
                    }
    
                    $sms_reg_tmp_mod =& m('sms_reg_tmp');
                  
                    $conditions = array('conditions'=>" category = 'findps' and type = 'findps' and phone = '$account' and code='$code' for update ");
                    $sms_log = $sms_reg_tmp_mod->get($conditions);
                  
                    if(!$sms_log['id'])
                    {
                      
                        $this->json_error('并未发送短信给您.........');
                    }
    
                     if($sms_log['fail_time'] < time())
                     {
                        //$this->msg('验证码已过期!');
                        $this->json_error('验证码已过期!');
                     } 
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
                   
                    $this->json_error('验证码为空');
                }
    
                $model_member =& m('member');
                $info = $model_member->get("user_name='{$account}' or phone_mob = '$account' or email = '$account' ");
                if (!$info)
                {
                   
                    $this->json_error('请先注册成为会员!');
                }
    
                if('phone' == $type){
    
                    $sms_reg_tmp_mod =& m('sms_reg_tmp');
                    $conditions = array('conditions'=>" category = 'findps' and type = 'findps' and phone = '$account' and code='$code' for update ");
                    $sms_log = $sms_reg_tmp_mod->get($conditions);
    
                    if(!$sms_log['id'])
                    {
                       
                        $this->json_error('并未发送短信给您...');
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
                    if ($passlen < 6 || $passlen > 32)
                    {
                       
                        $this->json_error('6-20个大小写英文字母、符号或数字');
                    }
                    
                    include_once ROOT_PATH."/includes/passports/uc.passport.php";
                    $UcPassportUser = new UcPassportUser();
                 
                  /*   $model_member =& m('member');
                    
                    $ucinfo = $ucmember_mod->get("username='{$_REQUEST['account']}'");
                   
                    $uc_id = $ucinfo['uid'];
                    $salt = substr(uniqid(rand()), -6);
                    
                    $ucpassword = md5(md5($ps).$salt);
                  
                    $ucs = $ucmember_mod->edit($uc_id, array("password" => $ucpassword,'salt'=>$salt));
                    
                    $newpassword = $ucpassword;
                    $result = $model_member->edit($uc_id, array("password" => $newpassword)); */
                    
                    ////////////////////////////////////////
                    /* 修改密码 */
                 
                    $info = $UcPassportUser->get($_REQUEST['account'], true);
                    
                    $result = $UcPassportUser->edit($info['user_id'], '', array('password'  => $ps),1);
                    if (!$result)
                    {
                        /* 修改不成功，显示原因 */
                        //$this->msg($ms->user->get_error());
                        $this->json_error($UcPassportUser->get_error());
                    }
                    ///////////////////////////////////////////
                    
                   /*  if (!$result || !$ucs)
                    {
                       
                        $this->json_error('修改失败!');
                    } */
    
                    $this->json_result('修改成功!');
    
                }
    
            }else{
                $this->msg('状态码错误...');
            }
        }else{
            $this->_config_seo('title', Lang::get('find_password') . ' - ' . Conf::get('site_title'));
            $this->display('member.findps.html');
        }
    }
    
    
    function myerweima(){
        
        $m =&m('member');
        $user_id = $this->visitor->get('user_id');
        $user_info =$m->get($user_id);
        $ewm = 'http://mfd.ds.cotte.cn/'.$user_info['erweima_url'];
        $this->assign('user',$user_info);
        $this->assign('erweimaurl',$ewm);
        $this->display('member.erweima.html');
        return false;
        
    }



    /*
     * 添加邀请码
     * */
    function addinviter(){

        if(!$this->visitor->has_login){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 200;
            $return['error']['msg'] 	  = '请先登录';
            echo json_encode($return);
            return false;
        }


        if(!IS_POST){
            $this->display('member.invite.html');
            return false;
        }


        $m =&m('member');
        $user_id = $this->visitor->get('user_id');
        $user_info =$m->get($user_id);
        $invite = $_REQUEST['invite'];



        if(empty($user_info['user_token'])){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 122;
            $return['error']['msg'] 	  = '参数错误';
            echo json_encode($return);
            return false;
        }

        if(!$invite){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 123;
            $return['error']['msg'] 	  = '邀请码不能为空';
            echo json_encode($return);
            return false;
        }

        if ($_SERVER['HTTP_HOST'] == 'm.mfd.p.day900.com'){
            $this->_url = "http://api.mfd.p.day900.com";
        }elseif($_SERVER['HTTP_HOST']=='m.local.mfdb2c.com'){
            $this->_url = "http://api.local.mfd.cn";
        }


        $url = $this->_url.'/soap/tang.php?act=addinviter&token='.$user_info['user_token'].'&invite='.$invite;
        $re = file_get_contents($url);
        echo $re;
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
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    function register()
    {
        if ($this->visitor->has_login)
          {
              $this->show_warning('has_login','返回用户中心', '/member.html');
              return;
          }

          if(isset($_POST['step2'])) {
            
              //注册的最后一步(手机注册)
              $check_step3 = $this->formStep3();
              if ($check_step3['err'])
              {
                  $this->json_error($check_step3['msg']);
                  return;
              }


              //diy分享作品
              $m       = & m('member');

              $password=$_POST['password'];
              $phone = $_POST['phone'];
              $code = isset($_REQUEST['code'])?trim($_REQUEST['code']):'';
              $invite =$_REQUEST['invite'];

              //1 hy
              $sign = '';
              if($code !=''){
                  $invite = $code;
                
              }

              if(!empty($invite)){
                  if(strlen($invite)==9)$sign=1;
              }

              if($sign ==1){
                  $member  = $m->get("serve_type=1 and invite = '".$invite."'");
                  $_type = 0;
                  //邀请码
                  $invite_nickname =$member['nickname'];
                  $inviter = $member['user_id'];
                  if( empty($member)) {
                      $this->json_error('邀请码错误!');
                      return;
                  }
              }
              
              include_once ROOT_PATH."/includes/passports/uc.passport.php";
              $UcPassportUser = new UcPassportUser();
              
              /*会员默认等级*/
              $member_lv_mod =& m('memberlv');
              /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
              $m_lv = $member_lv_mod->get_default_level();
          
              $invite = make_member_invite();
            
           
              $user_id = $UcPassportUser->register($phone, $password,$email ='',array('phone_mob'=>$phone,'invite'=>$invite,'member_lv_id'=>1,'come_from'=>'wap','serve_type'=>1,'nickname'=>'麦富迪初体验','user_token'=>md5($phone.$password)));
            
              //奖励
              $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
              $this->_debit_mod = &m("debit");
              $_cate_mod = & bm('gcategory', array('_store_id' => 0));

              $g_url ='';
              
              if($user_id){
                  do{
                      $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
                  } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
              }else{
                  $api_token = 'mfd';
              }
              
              if($member){
                  //邀请关系 一个表
                  $member_invite = m("memberinvite");
                  $invite_data = array(
                      'inviter'  => $inviter, //邀请人
                      'invitee'  => $user_id,
                      'nickname' => $invite_nickname,     //邀请人昵称
                      'type'      => $_type,
                      'come_from'=>'wap|register',
                      'add_time' => time(),
                  );
                  $member_invite->add($invite_data);
              }
              
              $user = $m->get($user_id);
              if($user){
                  if($user['serve_type'] == 1){
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
              $this->_do_login($user_id,$_POST['phone'],$password);

              /* 同步登录外部系统 */
              $synlogin = $UcPassportUser->synlogin($user_id);
              
              $this->json_result(array('content'=>$sign,'g_url'=>$g_url),'注册成功!');

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
                      $_SESSION['step2'] = 1;
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
              $code = isset($_GET['er_invite'])?trim($_GET['er_invite']):'';
              
              if (!empty($_GET['ret_url']))
              {
                  $ret_url = trim($_GET['ret_url']);
              }
              else
              {
              
                  if (isset($_SERVER['HTTP_REFERER']))
                  {
                      $ret_url = SITE_URL . 'member-index.html';
                  }
                  else
                  {
                      $ret_url = SITE_URL . '/default-index.html';
                  }
              }
                 
              $m =&m('member');
              $invite_nickname = '';
 
              if( $code !=''){
  				
                  $member  = $m->get("serve_type=1 and invite = '".$code."'");

              }
              if($member){
                  $invite_nickname =$member['nickname'];
              }
              $this->assign('invite_nickname', $invite_nickname);
              $this->assign('code', $code);
              $this->assign('ret_url', $ret_url);
              $this->_curlocal(LANG::get('user_register'));
              $this->_config_seo('title', Lang::get('user_register') . ' - ' . Conf::get('site_title'));
              $this->display('member.register.html');

          }
    }
    /**
     * 注册用户，外部通过url验证邮箱邮件
     *
     * local.caifeng.com/member-eValidation.html?email=xiao5_ge@163.com&code=d715213ee84ed72c660c495f6f80665f
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
        	//重定向浏览器 会员中心
        	header('Location:' . SITE_URL."/member.html");
        	exit;
        }

        $email_code_mod =& m('email_code');
        $conditions = array('conditions'=>" code = '$check_code'");
        $sms = $email_code_mod->get($conditions);
        if(!$sms)
        {
            $this->show_warning('并未发送邮件给您...');
            return;
        }
        $nickname = $this->getCode(10);

        if(check::isPrescription($sms['fail_time'],'email'))
        {
            $this->show_warning('邮件已失效，请重新注册...');
            return;
        }

        /*会员默认等级*/
        $member_lv_mod =& m('memberlv');
        /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
        $m_lv = $member_lv_mod->get_default_level();

        $ms =& ms(); //连接用户中心
        $user_id = $ms->user->register($uname, $sms['ps'], $uname,array('nickname'=>$nickname,'member_lv_id'=>$m_lv['member_lv_id']));

        if (!$user_id)
        {
            $this->show_warning($ms->user->get_error());

            return;
        }
        $this->_hook('after_register', array('user_id' => $user_id));
        //登录
        $this->_do_login($user_id);


        // webchat
        $user_mod =& m('member');
        $info = $user_mod->get_info($user_id);


//                setcookie("webchatId", $objAvatar->uc_authcode( $user_id, 'ENCODE', 'gaofei' ), time()+3600*24*14,'/');
//                $webchatCode = array(
//                                    'mid'=>$info['user_id'],
//                                    'sType'=>$info['serve_type'],
//                                    'name'=>$info['user_name'],
//                                    'passwd'=>$info['password'],
//                                    'nickname'=>$info['nickname'],
//                                    'mail'=>$info['email'],
//                                );


        /* 同步登录外部系统 */
        $synlogin = $ms->user->synlogin($user_id);
        $this->assign('email', $uname);
        $this->assign('name', $nickname);
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

        /* 注册并登录 */
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
            return array('msg'=>'验证码错误!','err'=>1);
        }

        if($sms_log['fail_time']<gmtime())
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
        if(empty($user_info['avatar'])){
            $avatar_tmp = '/avatar/noavatar_big.gif';
        }
        //生成获得店铺二维码
        $avatar_time = time();
        $code = $user_info['invite'];
        Qrcode('store', $user_id, 'http://wap.mfd.cn/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code,$avatar);
        $mqrcode = getQrcodeImage('store', $user_id, 2);
        $mqrcode = '/upload/phpqrcode/'.$mqrcode.'?'.$avatar_time;
    
        $arr = array(
            'avatar'=>$avatar_tmp,
            'avatar_time'=>$avatar_time,
            'erweima_url'=>$mqrcode
        );
        $model_user->edit($user_id,$arr);
         
    
    
    }
    /**

     *    修改基本信息
     *
     *    @author    Hyber
     *    @usage    none
     */
    function profile($type=0){

        $this->assign('ac', empty($type) ? 'index' : 'tailor');
        $user = $this->visitor->get();
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
            $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
            $this->assign('profile',$profile);


            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();

            //获取头像
            $avatar = $objAvatar->avatar_show_src($user_id,'big');

            $this->assign('avatar', $avatar);
            /* 头像 add by xiao5 END */

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
            //获取地区
            $region_mod =& m('region');
            $this->assign('site_url', site_url());
            $this->assign('regions', $region_mod->get_options(0));

            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_profile'));
            $this->display('member.profile.html');
        }
        else
        {

            $data = array(
                'nickname'  => $_POST['nickname'],
                'gender'    => $_POST['gender'],
                'birthday'  => $_POST['birthday'],
                'height'    => $_POST['height'],
                'weight'    => $_POST['weight'],
                'memo'      => $_POST['memo'],
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
                $this->show_message('昵称已存在!');
                return;
            }

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
     * 修改头像
     *
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
   public function upavater($type=0){
        $this->assign('ac', empty($type) ? 'index' : 'tailor');
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
     *    修改密码
     *
     *    @usage    none
     */
    function password($type=0){
        $this->assign('ac', empty($type) ? 'index' : 'tailor');
        $user_id = $this->visitor->get('user_id');
        $mod = m('member');
        if (!IS_POST)
        {
            
            $user = $this->visitor->get();
            $this->assign('user',$user);
            $this->display('member.password.html');
        }
        else
        {
            //$ms =& ms();    //连接用户系统
            include_once ROOT_PATH."/includes/passports/uc.passport.php";
            $UcPassportUser = new UcPassportUser();
            /* 两次密码输入必须相同 */
            $orig_password      = $_POST['orig_password'];
            $new_password       = $_POST['new_password'];
            $confirm_password   = $_POST['confirm_password'];
            
            
           /*  if (!$ms->user->auth_info($this->visitor->get('user_name'),$orig_password))
            {
                $this->json_error('您输入的旧密码不正确');
                return;
            } */
            
            $user = $mod->get_info($this->visitor->get('user_id'));
            
            $user_id = $UcPassportUser->auth($user['user_name'], $orig_password,true);  //验证用户
            
            if (!$user_id)
            {
                $this->json_error('您输入的旧密码不正确');
                return;
            }
            
            if ($new_password != $confirm_password)
            {
                $this->json_error('两次输入的密码不相符');
                return;
            }
            if (!$new_password)
            {
                $this->json_error('没有输入新密码');
                return;
            }
            $passlen = strlen($new_password);
            if ($passlen < 6 || $passlen > 32)
            {
                $this->json_error('密码长度错误，应保持在6-32位之间');
                return;
            }

            /* 修改密码 */

            $result = $UcPassportUser->edit($this->visitor->get('user_id'), $orig_password, array(
                'password'  => $new_password
            ));

            if (!$result)
            {
                /* 修改不成功，显示原因 */
                $this->json_error('密码修改失败');
                return;
            }
            $this->json_result('','密码修改成功');
            return;
        }
    }
	/* 修改支付密码
	 *
	 * @author daniel
	 * @2015-10-19
	 * */
    function pay_password(){
    	$user = $this->visitor->get();
    	if(!IS_POST){
    		$this->assign('pass',$user['pay_password']);
    		$this->display('member.pay-password.html');
    	}else{

    		/* 两次密码输入必须相同 */
    		$orig_password      = $_POST['orig_password'];
    		$new_password       = $_POST['new_password'];
    		$confirm_password   = $_POST['confirm_password'];
    		if ($orig_password && md5($orig_password)!=$user['pay_password'])
    		{
    			$this->json_error('您输入的旧密码不正确');
    			return;
    		}
    		if ($new_password != $confirm_password)
    		{
    			$this->json_error('两次输入的密码不相符');
    			return;
    		}
    		if (!$new_password)
    		{
    			$this->json_error('没有输入新密码');
    			return;
    		}
    		$passlen = strlen($new_password);
    		if ($passlen < 8 || $passlen > 16)
    		{
    			$this->json_error('密码长度错误，应保持在8-16位之间');
    			return;
    		}
 			if($orig_password==$new_password){
 				$result=1;
 			}else{
 				$result=$this->_mod_member->edit($user['user_id'],array('pay_password'=>md5($new_password)));
 			}
    		/* 修改密码 */


    		if (!$result)
    		{
    			/* 修改不成功，显示原因 */
    			$this->json_error('密码修改失败');
    			return;
    		}
    		$this->json_result('','密码修改成功');
    		return;
    	}
    }
    /**
    * 个人认证
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-17
    */
    function auth_person_info($type=0)
    {
     	$this->assign('ac', empty($type) ? 'index' : 'tailor');
        $user_id = $this->visitor->get('user_id');
        $business_auth_mod = m('businessauth');
        $person_auth_mod = m('personauth');
        if ($business_auth_mod->get("user_id=$user_id"))
        {
        	$this->show_warning('您已提交审核!请勿重复提交');
        	return;
        }
        $person_auth_info = $person_auth_mod->get("user_id=$user_id");

        //===== 职业 =====
        $job = include_once ROOT_PATH.'/includes/arrayfiles/job.arrayfile.php';
        $this->assign('job',$job);
        if (!IS_POST)
        {
            $this->assign('type', 'user_set');
            /* 当前用户中心菜单 */
            $this->assign('_curitem', 'auth');
            $this->assign('_member_menu', $this->_get_member_menu('user_set'));
            //ns add 获取用户信息
            $user = $this->visitor->get();
            $this->assign('user',$user);
            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();

            //获取头像
            $avatar = $objAvatar->avatar_show($user_id,'big');
            $this->assign('avatar', $avatar);

            //===== 获得基本的数据 =====
            if($person_auth_info)
            {
            	//===== 时间搓=====
            	if (!$person_auth_info['is_long'])
            	{
            		$person_auth_info['card_due_time'] = date("Y-m-d",$person_auth_info['card_due_time']);
            	}

            	//===== 图片 =====
            	if ($person_auth_info['card_face_img'])
            	{
            		$person_auth_info['card_face_img_data'] = $person_auth_info['card_face_img'];
            		$person_auth_info['card_face_img']	   = $this->_auth_web_img.$person_auth_info['card_face_img'];
            	}
            	if ($person_auth_info['card_back_img'])
            	{
            		$person_auth_info['card_back_img_data'] = $person_auth_info['card_back_img'];
            		$person_auth_info['card_back_img']	   = $this->_auth_web_img.$person_auth_info['card_back_img'];
            	}

            	$this->assign('auth',$person_auth_info);
            }
            else
            {
            	$this->assign('auth',array('id'=>0));
            }

            $this->_config_seo('title', Lang::get('user_center') . ' - ' . '身份认证');
            $this->display('member.person.html');
        }
        else
        {
        	//===== 正则验证身份证 =====
        	$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
        	$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
        	$card = $_POST['card'];
//         print_exit(preg_match($preg18, $card));
        	if (!preg_match($preg18, $card) && !preg_match($preg15, $card))
        	{
        		$this->show_warning('请输入正确的身份证件号码');
        		return;
        	}
        	if (!$_POST['hid_face_img'] || !$_POST['hid_back_img'])
        	{
        		$this->show_warning('身份证图片必须上传');
        		return;
        	}

        	$data['card'] = $card;
        	$data['card_name'] = $_POST['card_name'];
        	$data['sex'] = in_array($_POST['sex'], array(1,2)) ? $_POST['sex'] : 1;
        	$data['is_long'] = isset($_POST['is_long']) ? $_POST['is_long'] : 0;
        	$data['card_due_time'] = !empty($_POST['card_due_time']) ? strtotime($_POST['card_due_time']) : '';
        	$data['job_id']  = $_POST['job_id'];
        	$data['mobile']	 = $_POST['mobile'];
        	$data['address'] = $_POST['address'];
        	$data['card_face_img']	= $_POST['hid_face_img'];
        	$data['card_back_img']	= $_POST['hid_back_img'];
        	$data['user_id']	= $this->visitor->get('user_id');

        	//===== 添加 person表 =====
        	$person_auth_mod = m('personauth');

        	if($person_auth_info)
        	{
        		if($person_auth_mod->edit($person_auth_info['id'],$data) !== false)
        		{
        			header("Location:/member-tailor-auth_success.html");
        		}
        	}
        	else
        	{
        		if($person_auth_mod->add($data))
        		{
        			$this->show_message('成功提交资料');
        		}
        	}


        }
    }

    /**
     * 企业人认证
     * @version 1.0.0
     * @author liang.li <1184820705@qq.com>
     * @2015-1-17
     */
    function auth_business_info($type=0)
    {
    	$this->assign('ac', empty($type) ? 'index' : 'tailor');
    	$user_id = $this->visitor->get('user_id');
    	$business_auth_mod = m('businessauth');
    	$person_auth_mod = m('personauth');
    	$region_mod = m('region');
    	$business_info = $business_auth_mod->get(array(
    		'conditions'	=> "user_id=$user_id",
    		"fields"		=> "*",
    	));
    	if ($person_auth_mod->get("user_id=$user_id"))
    	{
    		$this->show_warning('您已申请个人认证！不能重复认证');
    	}
    	if (!IS_POST)
    	{
    		//===== 获得认证的步骤 =====
    		$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 1;
    		$this->assign('type', 'user_set');
    		/* 当前用户中心菜单 */
    		$this->assign('_curitem', 'auth');
    		$this->assign('_member_menu', $this->_get_member_menu('user_set'));
    		//ns add 获取用户信息
    		$user = $this->visitor->get();
    		$this->assign('user',$user);
    		/* 头像 add by xiao5 START */
    		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
    		$objAvatar = new Avatar();

    		//获取头像
    		$avatar = $objAvatar->avatar_show($user_id,'big');
    		$this->assign('avatar', $avatar);

    		//===== 获得基本的数据 =====
    		if ($business_info)
    		{
    			//===== 初始化基本数据 =====
    			if ($business_info['region_id'])
    			{
    				//===== 地区 =====
    				$region_ids = explode("　", $business_info['region_id']);
    				$business_info['region_parent_id'] = $region_ids[0];
    				$business_info['region_son_list'] = $region_mod->get_options($business_info['region_parent_id']);
    				$business_info['region_son_id']	   = $region_ids[1];

    				//===== 时间戳 =====
    				if (!$business_info['is_long'])
    				{
    					$business_info['business_life'] = date("Y-m-d",$business_info['business_life']);
    				}
    				if (!$business_info['legal_card_long'])
    				{
    					$business_info['legal_card_due'] = date("Y-m-d",$business_info['legal_card_due']);
    				}

    				//===== 图片 =====
    				if ($business_info['org_img'])
    				{
    					$business_info['org_img_data'] = $business_info['org_img'];
    					$business_info['org_img']	   = $this->_auth_web_img.$business_info['org_img'];
    				}
    				if ($business_info['business_img'])
    				{
    					$business_info['business_img_data'] = $business_info['business_img'];
    					$business_info['business_img']	   = $this->_auth_web_img.$business_info['business_img'];
    				}
    				if ($business_info['business_seal_img'])
    				{
    					$business_info['business_seal_img_data'] = $business_info['business_seal_img'];
    					$business_info['business_seal_img']	   = $this->_auth_web_img.$business_info['business_seal_img'];
    				}
    				if ($business_info['legal_card_face_img'])
    				{
    					$business_info['legal_card_face_img_data'] = $business_info['legal_card_face_img'];
    					$business_info['legal_card_face_img']	   = $this->_auth_web_img.$business_info['legal_card_face_img'];
    				}
    				if ($business_info['legal_card_back_img'])
    				{
    					$business_info['legal_card_back_img_data'] = $business_info['legal_card_back_img'];
    					$business_info['legal_card_back_img']	   = $this->_auth_web_img.$business_info['legal_card_back_img'];
    				}

    			}
    			$this->assign('auth',$business_info);
    		}
    		else
    		{
    			$this->assign('auth',array('id'=>0));
    		}


    		$this->_config_seo('title', Lang::get('user_center') . ' - ' . '身份认证');

    		if ($step == 1)
    		{
    			//===== 地区列表 =====
    			$region_list = $region_mod->get_options(2);
    			$this->assign('region_parent',$region_list);
    			$this->display('member.business_auth_1.html');
    		}
    		else
    		{
//   print_exit($business_info);
    			$this->display('member.business_auth_2.html');
    		}

    	}
    	else
    	{
//     	print_exit($_POST);
    		$step = isset($_POST['step']) ? $_POST['step'] : 1;
    		$data = array();
    		if ($step == 1)
    		{
    			//===== 如果已经审核过了 不允许进入这个方法 =====
    			/* if ($business_auth_mod->get("user_id=$user_id") || $person_auth_mod->get("user_id=$user_id"));
    			{
    				$this->show_warning('您已提交审核!请勿重复提交');
    				return;
    			} */

    			$region_mod = m('region');
    			$data['firm_name'] = $_POST['firmName'];
    			$data['licence_num'] = $_POST['licenceNum'];
    			$region_prent_id  = $_POST['region_parent_id'];
    			$region_son_id = $_POST['region_son_id'];
    			$region_parent_name = $region_mod->getRegionName($region_prent_id);
    			$region_son_name = $region_mod->getRegionName($region_son_id);
    			$data['region_id'] = $region_prent_id.'　'.$region_son_id;
    			$data['region_name'] = $region_parent_name.'　'.$region_son_name;
    			$data['common_address'] = $_POST['commonAddress'];
    			$data['business_life'] = !empty($_POST['businessLife']) ? strtotime($_POST['businessLife']) : '';
    			$data['is_long']     = !empty($_POST['is_long']) ? $_POST['is_long'] : 0;
    			$data['business_scope'] = $_POST['business_scope'];
    			$data['org_code']	= $_POST['org_code'];
    			$data['link_mob']	= $_POST['link_mob'];
    			$data['tax_code']	= $_POST['tax_code'];
    			$data['org_img'] = $_POST['hid_org_img'];
    			$data['business_img'] = $_POST['hid_business_img'];
    			$data['business_seal_img'] = $_POST['hid_business_seal_img'];
    			$data['user_id']	= $user_id;
//    print_exit($data);

    			//===== 如果审核已经存在是修改 否则是添加 =====
    			if ($business_info)
    			{
    				$business_auth_mod->edit($business_info['id'],$data);
    				header("Location: /member-tailor-auth_business_info.html?step=2");
    				return;
    			}
    			//===== 添加 business表 =====
    			if($business_auth_mod->add($data))
    			{
    				//===== 添加成功 要修改 member对应的字段 标识这个会员是个人 还是企业=====
    				$member_mod = m('member');
    				$member_mod->edit($data['user_id'],array('is_ident'=>2));
    				header("Location: /member-tailor-auth_business_info.html?step=2");
    				return;
    			}

    		}
    		elseif ($step == 2)
    		{
    			if (!$business_info)
    			{
    				$this->show_warning('请先填写第一步');
    				return;
    			}
    			$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
    			$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
    			$card = $_POST['legal_card'];
    			if (!preg_match($preg18, $card) && !preg_match($preg15, $card))
    			{
    				$this->show_warning('请输入正确的身份证件号码');
    				return;
    			}

    			$data['legal_real_name'] = $_POST['legal_real_name'];
    			$data['legal_card'] = $card;
    			$data['legal_card_due'] =  !empty($_POST['legal_card_due']) ? strtotime($_POST['legal_card_due']) : 0;
    			$data['legal_card_long'] = !empty($_POST['legal_card_long']) ? 1 : 0;
    			$data['legal_card_face_img'] = $_POST['hid_legal_card_face_img'];
    			$data['legal_card_back_img'] = $_POST['hid_legal_card_back_img'];

    			if ($business_auth_mod->edit($business_info['id'],$data))
    			{
    				$this->show_message('提交审核成功');
    				return;
    			}
    			else
    			{
    				$this->show_warning('失败');
    				return;
    			}

    		}

       }
    }


    /**
    * 个人认证
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-21
    */
    function auth()
    {
    	$this->assign('type', 'user_set');
    	/* 当前用户中心菜单 */
    	$this->assign('_curitem', 'auth');
    	$this->assign('_member_menu', $this->_get_member_menu('user_set'));
    	//ns add 获取用户信息
    	$user = $this->visitor->get();
    	$this->assign('user',$user);
    	/* 头像 add by xiao5 START */
    	require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
    	$objAvatar = new Avatar();

    	//获取头像
    	$avatar = $objAvatar->avatar_show($user_id,'big');

    	$this->assign('avatar', $avatar);


    	$this->display("member.auth.index.html");
    }

    /**
    * 认证成功提示页
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-26
    */
    function auth_success()
    {

    	$this->assign('type', 'user_set');
    	/* 当前用户中心菜单 */
    	$this->assign('_curitem', 'auth');
    	$this->assign('_member_menu', $this->_get_member_menu('user_set'));
    	//ns add 获取用户信息
    	$user = $this->visitor->get();
    	$this->assign('user',$user);
    	/* 头像 add by xiao5 START */
    	require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
    	$objAvatar = new Avatar();

    	//获取头像
    	$avatar = $objAvatar->avatar_show($user_id,'big');
    	$this->assign('avatar', $avatar);


    	$this->display("member.auth.success.html");
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



    //随机字符串
    function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /*
     * yusw
     * 补发没送券用户
     * */


    function yusw(){

        return false;
        //奖励
        $store_allow = include  ROOT_PATH.'../data/settings.inc.php';
        $this->_debit_mod = &m("debit");
        $member_invite = m("memberinvite");


        $member_invite_info = $member_invite->find(array(
            'conditions'=>'type=0',

        ));
        foreach($member_invite_info as $k=>$v){
            $user_id =$v['invitee'];
            $debit_info = $this->_debit_mod->find("user_id='{$user_id}'");

            if(empty($debit_info)){
                if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type'])){

                    if($store_allow['debit_cate']==1){
                        $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                    }else{
                        $expire_time =$store_allow['debit_time'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name'],
                        'debit_sn'=>time().$this->createNonceStr(8),
                        'money'=>$store_allow['debit_num'],
                        'user_id'=>$user_id,
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type'],
                        'expire_time'=>$expire_time,
                    );

                    $d_ret1= $this->_debit_mod->add($data);
                }

                //礼券 -self
                if(!empty($store_allow['debit_cate2']) && !empty($store_allow['debit_time2'])&& !empty($store_allow['debit_name2'])&&!empty($store_allow['debit_num2']) && !empty($store_allow['debit_type2'])){
                    if($store_allow['debit_cate2']==1){
                        $expire_time2 =strtotime('+'.$store_allow['debit_time2'].' days') - date('Z');
                    }else{
                        $expire_time2 =$store_allow['debit_time2'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name2'],
                        'debit_sn'=>time().$this->createNonceStr(8),
                        'money'=>$store_allow['debit_num2'],
                        'user_id'=>$user_id,
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type2'],
                        'expire_time'=>$expire_time2,
                    );

                    $d_ret2= $this->_debit_mod->add($data);
                }
                echo 'ret1:'.$d_ret1.'<br/>';
                echo 'ret2:'.$d_ret2.'<br/>';
            }

        }

    }
    

    /*
     * 注册协议
     *  */
    function agreement(){
    	$id=$_GET['id'];
    	$article_mod      = &m('article');
    	$art_info = $article_mod->get("article_id ='$id' ");
    	//var_dump($art_info);exit();
    	$this->assign('art_info',$art_info);
    	$this->display('agreement.html');
    }
}

?>