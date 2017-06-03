<?php

class MsgModule extends AdminbaseModule
{
    function __construct()
    {
        $this->MsgModule();
    }

    function MsgModule()
    {
        parent::__construct();	
		$this->mod_msg =& m('msg');
		$this->mod_msglog =& m('msglog');
    }

 	function index()
    {
		$condition = $this->_get_query_conditions(array(array(
                'field' => 'to_mobile',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
            ),
        ));
		/*$condition2 = $this->_get_query_conditions(array(array(
                'field' => 'time',         //可搜索字段time
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
            ),
        ));*/
		$page = $this->_get_page(10);		
		$index=$this->mod_msglog->find(array(
	        'conditions' => 'type=0 AND code =0 '.$condition,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
		$page['item_count'] = $this->mod_msglog->getCount();
        $this->_format_page($page);
	    $this->assign('page_info', $page);
	    $this->assign('index', $index);//传递到风格里
       $this->display('index.html');
	   return;
	}

	function user()
    {
		$condition = $this->_get_query_conditions(array(array(
                'user_name' => 'user_name',         //可搜索字段user_name
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
            ),
        ));
		$page = $this->_get_page(10);		
		$user=$this->mod_msg->find(array(
	        'conditions' => '1=1'.$condition,
            'limit' => $page['limit'],
			'order' => "id desc",
			'count' => true));
		$page['item_count'] = $this->mod_msg->getCount();
        $this->_format_page($page);
		
		$checked_functions = $functions = array();
        $functions = $this->_get_functions();
        $tmp = explode(',', $user[1]['functions']);
        if ($functions)
        {
             foreach ($functions as $func)
             {
                 $checked_functions[$func] = in_array($func, $tmp);
             }
        }
		$this->assign('functions', $functions);	
		$this->assign('checked_functions', $checked_functions);
	    $this->assign('page_info', $page);
	    $this->assign('user', $user);//传递到风格里
       $this->display('user.html');
	   return;
	}
	   
 	function add()
    {
		if(!IS_POST)
		{
			$user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
			$user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
			if(!empty($user_id))
			{
				$data = $this->mod_msg->find('user_id='.$user_id);
			}
			$this->assign('data', $data);
			$this->display('add.html');
		}
		else
		{
			$user_name= trim($_POST['user_name']);
		   $num_edit= trim($_POST['num']);
		   $jia_or_jian= trim($_POST['jia_or_jian']);
		   $log_text= trim($_POST['log_text']);	
		   $time = time();
		   if(empty($user_name) or empty($num_edit) or empty($jia_or_jian))
		   {
				$this->show_warning('cuowu_bunengweikong');
				return;
		   }  
		   if (preg_match("/[^0.-9]/",$num_edit))
		   {
			   $this->show_warning('cuowu_not_num'); 
			   return;
		   } 
		   $row_msg=$this->mod_msg->getrow("select * from ".DB_PREFIX."msg where user_name='$user_name'");	
		   if($row_msg)
		   {
			   $num_old = $row_msg['num']; 
			   $id = $row_msg['user_id'];
			   if($jia_or_jian=="jia")
			   {
					$num_new = $num_old+$num_edit;
			   }
			   else
			   {
				   if($num_old>=$num_edit)
				   {	   
						$num_new = $num_old-$num_edit;
				   }
				   else
				   {
						$this->show_warning('cuowu_num_smaller');
						return;
				   }
			   } 
			   $edit_msg = array(
			   		'num' => $num_new,
			   );
			   $edit_msglog = array(
			   		'user_id' => $id,
					'user_name' => $user_name,
					'content' => $log_text,
					'type' => 1,
					'time' => $time, 
			   );
			   $this->mod_msg->edit("user_name='$user_name'",$edit_msg);
			   $this->mod_msglog->add($edit_msglog);
			   $this->show_message('add_msgnum_successed',
                'back_list',    'index.php?module=msg&amp;act=user',
                'continue_add', 'index.php?module=msg&amp;act=add'
            );
		   }
		   else
		   {
			   $this->show_warning('cuowu_no_user'); 
			   return;
		   }
		   
		}
	}
	
	function send()
    {

        if (!IS_POST)
		{
			$this->display('send.html');
        }
        else
        {			
			$mobile	 = $_POST['to_mobile'];	//号码
			$smsText = trim($_POST['msg_content']);		//内容
			
			$time = time();
			if($mobile == '')
			{
				$this->show_message('cuowu_shoujihaomabunengweikong', 'go_back', 'index.php?module=msg');
				return;
			}
			if($smsText == '')
			{
				$this->show_message('cuowu_neirongbunengweikong', 'go_back', 'index.php?module=msg');
				return;
			}
			
			//九天接口
			$this->SendSms($mobile,$smsText);
			
			//中国网建接口
			/*$url='http://utf8.sms.webchinese.cn/?Uid='.SMS_UID.'&Key='.SMS_KEY.'&smsMob='.$mobile.'&smsText='.$smsText; 
			$res = $this->Sms_Get($url);
			if($res == '')
			{
				$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?module=msg');
				return;
			}
			else if($res>0)
			{
				$add_msglog = array(
					'user_id' => 0,
					'user_name' => 'admin',
					'to_mobile' => $mobile,
					'content' => $smsText,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->show_message('send_msg_successed', 'go_back', 'index.php?module=msg');
				return;
			}
			else
			{
				$add_msglog = array(
					'user_id' => 0,
					'user_name' => 'admin',
					'to_mobile' => $mobile,
					'content' => $smsText,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?module=msg');
				return;
			}*/
        }
    }
	
	function setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); //载入系统设置数据
        if (!IS_POST)
        {
            $this->assign('setting', $setting);
            $this->display('setting.html');
        }
        else
        {
            //建网参数
            //             $data['msg_pid']     = $_POST['msg_pid'];
            //             $data['msg_key']     = $_POST['msg_key'];
            	
            //9天参数
            $data['msg_pid']     = $_POST['user_id'];
            $data['msg_key']     = $_POST['pass'];
            $data['channelid']     = $_POST['channelid'];
            $model_setting->setAll($data);

            $this->show_message('setting_successed');
        }
    }
	
	/**
     *    中国网建接口
     *
     *    @author    andcpp
     *    @return    array
     */
	/*function Sms_Get($url)
	{
		if(function_exists('file_get_contents'))
		{
			$file_contents = file_get_contents($url);
		}
		else
		{
			$ch = curl_init();
			$timeout = 5;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}
		return $file_contents;
	}*/

    /**
     * 九天企信王
     * php调用sms9短信接口的函数：
     * 使用方法：sendsms("手机号码","发送内容"); 多个手机号码可以用,连接，或者传入数组。
     * 如果发送成功，则返回：success:本次发送短信编号
     * 如果发送失败，则返回：error:错误描述
     * 错误描述:传递参数错误=-1 用户id或密码错误=-2 通道id错误=-3 手机号码错误=-4 短信内容错误=-5 余额不足错误=-6 绑定ip错误=-7 未带签名=-8 签名字数不对=-9 通道暂停=-10 该时间禁止发送=-11 时间戳错误=-12
     * 发送短信示例： (请注意填写相应的channelid，上面红色字体已经标明可用的channelid)
     * http://admin.sms9.net/houtai/sms.php?cpid=6136&password=04af655cdf1d962b7c8360a77aa1ec55&channelid=1&tele=13800138000,13101234567&msg=testcontent&timestamp=1372993284
     */
    
    function SendSms($mobile,$content,$return=FALSE,$user_id=0,$user_name='admin')
    {
    	//vdump('111');
    	//动态后台设置
    	$user_id = SMS_UID; // sms9平台用户id
    	$pass = SMS_KEY; // 用户密码
    	$channelid = SMS_CHANNELID; // 发送频道id
    
    	if(!$mobile || !$content || !$user_id || !$pass || !$channelid) return false;
    
    	if(is_array($mobile)) $mobile = implode(",",$mobile);
    
    
    	$db_content = $content;
    
    	//utf8需要转码
    	$content = iconv("utf-8","gbk//ignore",$content);
    	$content = urlencode($content);
    	$api = "http://admin.sms9.net/houtai/sms.php?cpid={$user_id}&password={$pass}&channelid={$channelid}&tele={$mobile}&msg={$content}";
    	$res = file_get_contents($api);
    	$rs = strpos($res,'success') === false ? explode(":",$res) : array('succeed',1);
    
    	$add_msglog = array(
    			'user_id' => $user_id,
    			'user_name' => $user_name,
    			'to_mobile' => $mobile,
    			'content' => $db_content,
    			'state' => $rs[1],
    			'time' => time(),
    	);
    	$this->mod_msglog->add($add_msglog);
    
    	//调用是否需要返回值，返回特定的提示和跳转 .默认是后台发送短信
    	if ($return) return $rs[1];
    
    	if('error' == $rs[0])
    	{
    		$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?module=msg');
    		return;
    	}else{
    			
    		$this->show_message('send_msg_successed', 'go_back', 'index.php?module=msg');
    		return;
    	}
    }
//     function SendSms($mobile,$content,$return=FALSE,$user_id=0,$user_name='admin')
//     {
//     	//vdump('111');
//     	//动态后台设置
//     	$user_id = SMS_UID; // sms9平台用户id
//     	$pass = SMS_KEY; // 用户密码
//     	$channelid = SMS_CHANNELID; // 发送频道id
    
//     	if(!$mobile || !$content || !$user_id || !$pass || !$channelid) return false;
    
//     	if(is_array($mobile)) $mobile = implode(",",$mobile);
    
    
//     	$db_content = $content;
    
//     	//utf8需要转码
//     	$content = iconv("utf-8","gbk//ignore",$content);
//     	$content = urlencode($content);
//     	$api = "http://42.121.122.61:18002/send.do?ua={$user_id}&pw={$pass}&mb={$mobile}&ms={$content}";
//     	$res = file_get_contents($api);
//     	$rs = explode(",",$res);
//     	$add_msglog = array(
//     			'user_id' => $user_id,
//     			'user_name' => $user_name,
//     			'to_mobile' => $mobile,
//     			'content' => $db_content,
//     			'state' => $rs[0],
//     			'time' => time(),
//     	);
//     	$this->mod_msglog->add($add_msglog);
    
//     	//调用是否需要返回值，返回特定的提示和跳转 .默认是后台发送短信
//     	if ($return) return empty($rs[0]) ? '1' : $rs[0];
    
//     	if('0' == $rs[0]){
//     		$this->show_message('send_msg_successed', 'go_back', 'index.php?module=msg');
//     		return;
//     	}else{
//     		$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?module=msg');
//     		return;
//     	}
//     }
	/**
     *    获取可用功能列表
     *
     *    @author    andcpp
     *    @return    array
     */
    function _get_functions()
    {
        $arr = array();        
        $arr[] = 'buy'; //来自买家下单通知   
        $arr[] = 'send'; //卖家发货通知买家   
		$arr[] = 'check';//来自买家确认通知   
        return $arr;
    }	
}
?>