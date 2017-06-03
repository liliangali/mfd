<?php
use Cyteam\Member\Member;
/**
 *    前台控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class FrontendApp extends ECBaseApp
{
    function __construct()
    {
       $this->FrontendApp();
    }
    
    function clear_urlcan($url){
        $rstr='';
        $tmparr=parse_url($url);
        $rstr=empty($tmparr['scheme'])?'http://':$tmparr['scheme'].'://';
        $rstr.=$tmparr['host'].$tmparr['path'];
        return $rstr; 
    }
    
    function weixin()
    {
        require_once "weipay/lib/WxPay.Api.php";
        require_once "weipay/WxPay.JsApiPay.php";
        require_once 'weipay/log.php';            //=====  创建支付订单  =====
        $tools = new JsApiPay();
        $mod = m('member');
        
        if (!$_COOKIE['is_check_openid']) 
        {
            $openid = $tools->GetOpenid();
            if (!$openid)//=====  重定向  =====
            {
                $url = SITE_URL;
                header("Location:$url");
                exit();
            }
            else
            {

                //通过openid来确定是否关注
                $accmod = m('accesstoken');
                $access_token = $accmod->get_token();
                $openurl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN ";
                $openres = https_request($openurl);
                if(isset($openres['errcode']) && $openres['errcode'] > 0) //出错有可能是access_token无效
                {
                    $access_token = $accmod->settoken();
                    $openurl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN ";
                    $openres = https_request($openurl);
                }
                
                if(isset($openres['subscribe']) && $openres['subscribe'] == 0) //没有关注
                {
                    $this->display('fweixin.html');
                    die();
                }

                $m_info = $mod->get("openid = '$openid'");
                setcookie("is_check_openid", 1);
                if ($m_info)
                {

                    $this->_do_login($m_info['user_id']);
                    $this->visitor->has_login = true;
                    return ;
                }
                $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $url = $this->clear_urlcan($url);
                header("Location:$url");
            }
        }
       
        
        
        
        $user_info = $tools->GetOpenid2();
        if ($user_info && $user_info['openid']) 
        {
            $openid = $user_info['openid'];
            $m_info = $mod->get("openid = '$openid'");
            if (!$m_info) //注册
            {
                $nicname = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $user_info['nickname']);
                if (!get_magic_quotes_gpc())
                {
                    $nicname = addslashes($nicname);
                }
                if(!$nicname)
                {
                    $nicname = "**";
                }
                $mdata['openid'] = $openid;
                $mdata['nickname'] = $nicname;
                $mdata['user_name'] = $nicname;
                $mdata['reg_time'] = time();
                $mdata['avatar'] = $user_info['headimgurl'];
                $user_id = $mod->add($mdata);
                $this->_do_login($user_id);
                $member = new Member();
                $member->debit($user_id,$user_info['nickname']);
                setcookie("first_login", 1);
            }
            else 
            {
                $user_id = $m_info['user_id'];
                $this->_do_login($user_id);
            }
            $this->visitor->has_login = true;
        }
         
            
        
    }
    
    //如果openid已经存在则，
    function autologin()
    {
        
    }

    function FrontendApp()
    {

        Lang::load(lang_file('common'));
        Lang::load(lang_file(APP));
        parent::__construct();

        // 判断商城是否关闭
        if (!Conf::get('site_status'))
        {
            $this->show_warning(Conf::get('closed_reason'));
            exit;
        }

        if((APP == 'paynotifyw') || (APP == 'paynotifys'))//支付被动请求,过滤
        {
            
        }
        else
        {
            if((!is_mobile_request() || (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false)) &&  (MFD_DEBUG_MODEL == 0))//非手机访问.屏蔽.被动请求过
            {
                $this->display('weixin.html');
                die();
                return;
            }
        }



        if (!isset($_SESSION['user_info']['user_id']) ||  !$_SESSION['user_info']['user_id'])
        {

            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) //=====  如果是微信浏览器打开 并且没有code要通过outh机制 获取code  =====
            {
                //解决cookie在部分微信浏览器失效的问题
                if(IS_AJAX)//ajax数据不做跳转因为无法跳转
                {
                    if(isset($_REQUEST['mfd_reload_mid']) && $_REQUEST['mfd_reload_mid'])
                    {
                        $this->_do_login($_REQUEST['mfd_reload_mid']);

                    }
                }
                else
                {
                    $this->weixin();
                }

            }
        }




            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false ) //=====  如果不是微信打开  =====
            {
                if(isset($_GET['token']))
                {
                    $user_id = ApiAuthcode($_GET['token'], 'DECODE', 'kuteiddiy', 0);
                    if(!$user_id)
                    {
                        return;
                    }
                    if($_SESSION['user_info']['user_id'] && ($_SESSION['user_info']['user_id'] == $user_id))
                    {
                        return;
                    }

                    $_SESSION['source'] = $_GET['source'];
                    $this->visitor->logout();
                    $this->visitor->has_login = false;
                    if ($user_id)
                    {
                        $this->_do_login($user_id);
                        $this->visitor->has_login = true;
                        return ;
                    }
                }
            }
        
        
       
        # 在运行action之前，无法访问到visitor对象

        /***************** 城市切换 ***********************/
        
//         $token= isset($_GET['token'])   ?  trim($_GET['token']) : '';
//         $uid = intval(ApiAuthcode($token, 'DECODE', 'kuteiddiy',0)); //c9e5N2EUYiVWUUkwop9AgiQsGu3s1FPyluDLzB4Cwg
//         if($uid){
//             $this->visitor->has_login = true;
//             $this->_do_login($uid);
//         }

        if($_SESSION['user_info']['user_id']){
            setcookie("hasLogin", 1,0,'/','h5.myfoodiepet.com');
        }

    }
    
    
    
    function _config_view()
    {
        parent::_config_view();
        $this->_view->template_dir  = ROOT_PATH . '/h5/view/'.LANG.'/';
        $this->_view->compile_dir   = ROOT_PATH . '/h5/temp/compiled/mall';
        $this->_view->res_base      = SITE_URL . '/h5/view/'.LANG.'/';
        $this->_config_seo(array(
            //'title' => Conf::get('site_title'),
            'description' => Conf::get('site_description'),
            'keywords' => Conf::get('site_keywords')
        ));
    }

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

	//中国网建接口 by andcpp
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
    /*短信发送*/
	function SendSms($mobile,$content,$return=FALSE,$user_id=0,$user_name='admin',$code=0){
		$user_id = SMS_UID; // sms9平台用户id
		$pass = SMS_KEY; // 用户密码
		$channelid = SMS_CHANNELID; // 发送频道id

		if(!$mobile || !$content || !$user_id || !$pass || !$channelid) return false;

		if(is_array($mobile)) $mobile = implode(",",$mobile);

		$db_content = $content;

    	//utf8需要转码
		$content = iconv("utf-8","gbk//ignore",$content);
		$content = urlencode($content);

		#模拟发送短信-add by v5
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
					 'code' => $code);
		$this->mod_msglog =& m('msglog');

		$this->mod_msglog->add($add_msglog);

 		//调用是否需要返回值，返回特定的提示和跳转 .默认是后台发送短信
		if ($return) return $rs[1];


		if('error' == $rs[0]){

			$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?module=msg');
    		return;
    	}else{
    		$this->show_message('send_msg_successed', 'go_back', 'index.php?module=msg');
    		return;
    	}
    }

    function getCode ($length = 32, $mode = 0)
    {
    	switch ($mode) {
    		case '1':
    			$str = '1234567890';
    			break;
    		case '2':
    			$str = 'abcdefghijklmnopqrstuvwxyz';
    			break;
    		case '3':
    			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    			break;
    		case '4':
    			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';break;
    		case '5':
    			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    			break;
    		case '6':
    			$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
    			break;
    		default:
    			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    			break;
    	}
    	$randString = '';
    	$len = strlen($str)-1;
    	for($i = 0;$i < $length;$i ++){
    		$num = mt_rand(0, $len);
    		$randString .= $str[$num];
    	}
    	return $randString ;
    }

    function display($tpl)
    {
        $cart =& m('cart');

        $msgTypes = array(
            'sys' => 0,
            'cpt' => 0,
            'cmt' => 0,
            'syc' => 0,
        );
        $msgCount = 0;

        if($this->visitor->has_login){
            $conditions = "to_user_id = '{$this->visitor->get("user_id")}' AND is_read=0";
            $message_mod = &m("usermessage");

            $msglist = $message_mod->find(array(
                'conditions' => $conditions,
                'order' => "add_time DESC",
                'count'	=>	"true",
                'fields' => "type",
            ));



            foreach($msglist as $key => $val){
                if($val['type'] == 1){
                    $msgTypes['sys'] +=1;
                }elseif($val['type'] == 2 || $val['type'] == 3){
                    $msgTypes['cmt'] +=1;
                }elseif(in_array($val['type'],array(8,9,10))){
                    $msgTypes['cpt'] +=1;
                }else{
                    $msgTypes['syc'] +=1;
                }
            }

            $msgCount = $message_mod->getCount();
        }

        $this->assign('cart_goods_num', is_object($cart) && is_object($this->visitor) ? $cart->get_kinds( $this->visitor->get('user_id')) : 0);
//        $url = "https://www.sobot.com/chat/h5/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&groupId=84c09075813e45f68eae414cf91e897a";
        $url = "https://www.sobot.com/chat/frame/js/entrance.js?sysNum=2b17cdee375a475e963aeed478c37fbf";
        $str = "groupId=84c09075813e45f68eae414cf91e897a";
        $userInfo = $_SESSION['user_info'];
        if($userInfo['user_id'])
        {
            $userInfo = m("member")->get_info($userInfo['user_id']);
            $str .="&partnerId={$userInfo['user_id']}&uname={$userInfo['nickname']}&realname={$userInfo['user_name']}&tel={$userInfo['phone_mob']}&face={$userInfo['avatar']}";
        }
        $this->assign('kefu_url', $url);
        $this->assign('kefu_uinfo', $str);

		/* 新消息 */

        $this->assign("msgCount", $msgCount);
        $this->assign("msgTypes", $msgTypes);



        $this->assign('currentApp', APP);
        $this->assign("currentAct" , ACT);
		$this->assign('helps', $this->_get_helps());  // 帮助中心
        $this->assign('acc_help', ACC_HELP);        // 帮助中心分类code

        $this->assign('site_title', Conf::get('site_title'));
        $this->assign('site_logo', Conf::get('site_logo'));
        $this->assign('statistics_code', Conf::get('statistics_code')); // 统计代码
        $current_url = explode('/', $_SERVER['REQUEST_URI']);
        $count = count($current_url);
        $this->assign('current_url',  $count > 1 ? $current_url[$count-1] : $_SERVER['REQUEST_URI']);// 用于设置导航状态(以后可能会有问题)
        parent::display($tpl);
    }

    function get_site_city()
    {
        $cityModel = &m("sitecity");
        $list = $cityModel->find(array(
            "order" => "sort_order DESC",
        ));

        $arr = array();
        foreach($list as $key => $val){
            $arr[$val["city_code"]] = $val;
        }
       return $arr;
    }


	/* 热门搜索提到全局 by andcpp */
	function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }
	/*end*/


    function login()
    {
//        if ($this->visitor->has_login)
//        {
//             //ns add 添加一个返回地址
//             $this->show_warning('has_login','返回用户中心', '/member.html');           
//             return;
//        }
        
       if (!IS_POST)
       {
           
           if (!empty($_GET['ret_url']))
           {
              $ret_url = str_replace('/index.php/','/', trim($_GET['ret_url']));
           }
           else
           {
               if (isset($_SERVER['HTTP_REFERER']))
               {
                   $ret_url = str_replace('/index.php/','/', $_SERVER['HTTP_REFERER']);
                   
               }
               else
               {
                   $ret_url = SITE_URL . '/default-index.html';
               }
           }

           $ret_url =strtolower($ret_url);
           if (str_replace(array('member-login', 'member-logout','member-find_password','member-register'), '', $ret_url) != $ret_url)
           {
               $ret_url = SITE_URL . '/default-index.html';
           }

           if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)  && is_mobile_request()) //=====  如果是微信浏览器打开 不要走登陆页面 =====
           {
               header("Location: $ret_url");
               return;
           }


			$this->assign('ret_urll',$ret_url);
           $this->_curlocal(LANG::get('user_login'));
           $this->_config_seo('title', Lang::get('user_login') . ' - ' . Conf::get('site_title'));
           $this->display('login.html');
           /* 同步退出外部系统 */
           if (!empty($_GET['synlogout']))
           {
               $ms =& ms();
               echo $synlogout = $ms->user->synlogout();
           }
          
       }
       else
       {

           $user_mod =& m('member');
//           include_once ROOT_PATH."/includes/passports/default.passport.php";
//           $UcPassportUser = new DefaultPassport();
           $user_name = trim($_POST['user_name']);
           $password  = $_POST['password'];
           //$ucmember_mod =& m('ucmember');
         
        
           // 同步用户中心
  /*          $ucmember_info = $ucmember_mod->get("username='{$user_name}'");
          
        
           $passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
           if(empty($ucmember_info)) {
               $status = -1;
           
           } elseif($ucmember_info['password'] != md5($passwordmd5.$ucmember_info['salt'])) {
               $status = -2;
                
           }else{
               $status = $ucmember_info['uid'];
               $user_id = $ucmember_info['uid'];
                
           } */
           
           /////
           //$user_id = $UcPassportUser->auth($user_name, $password,true);  //验证用户
           $password = md5($password);
           $u_info = $user_mod->get("user_name='$user_name' AND password='$password' ");

           $user_id = 0;
           if($u_info)
           {
               $user_id = $u_info['user_id'];
           }
//           echo '<pre>';print_r($user_id);exit;
          /*  $ms =& ms();
           $user_id = $ms->user->auth($user_name, $password,true); */

           if (!$user_id)
           {

               /* 未通过验证，提示错误信息 */
               $this->json_error('账号或者密码错误');

               return;
           }
           else
           {
           
             

           	$info = $user_mod->get_info($user_id);
            
               //=====  临时补救没哟user_token的数据
               if (!$info['user_token'])
               {
                   $user_token = md5($user_name.$password);
                   $user_mod->edit($user_id, "user_token = '$user_token'");
               }

               //serve_type==1时，才可登录
           	if($info['serve_type'] !=1) {
           		$this->json_error('该账号不是会员，有疑问请拨打 （客服电话）');
           		return;
           	}	
           	/* add by xiao5 账户后台冻结 */
           	if ($info['state_info'] == 2)
           	{
           		$this->json_error('该账号因违规，已被冻结，有疑问请拨打 （客服电话）');
           		return;    
           	}

               /* 通过验证，执行登录操作 */
               $this->_do_login($user_id,$user_name,$password);

               
               // webchat
               setcookie("wkey", encrypt( $user_id .'|||'. md5($password), 'E', 'gaofei' ) , time()+3600*24*14 , '/');

               /* 同步登录外部系统 */
             //  $synlogin = $UcPassportUser->synlogin($user_id);
               $_SESSION['user_info']['member_lv_id']=$info['member_lv_id'];
               $_SESSION['user_info']['phone_mob']=$info['phone_mob'];
              
               do{
                   $api_token = ApiAuthcode($info['user_id'], 'ENCODE', 'kuteiddiy', 0);
                  
               }while(!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
               $_SESSION['user_info']['tokens']=$api_token;

               $token    = md5($user_name.$password);

               
               /* add by 小5 diy页面登陆返回uid加密串用于api交互 -START */
               if($user_id){
                
               	do{
               		$api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
               	} while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
               
               }else{
               	$api_token = 'mfd';
               }
               /* add by 小5 diy页面登陆返回uid加密串用于api交互 -END */
               
               $user_mod->edit($user_id, array("user_token"=>$token,"last_login"=>time()));
               
               $this->json_result($api_token,'登录成功!');
           }

       }
    }

    function pop_warning ($msg, $dialog_id = '',$url = '')
    {
        if($msg == 'ok')
        {
            if(empty($dialog_id))
            {
                $dialog_id = APP . '_' . ACT;
            }
            if (!empty($url))
            {
                echo "<script type='text/javascript'>window.parent.location.href='".$url."';</script>";
            }
            echo "<script type='text/javascript'>window.parent.js_success('" . $dialog_id ."');</script>";
        }
        else
        {
            header("Content-Type:text/html;charset=".CHARSET);
            $msg = is_array($msg) ? $msg : array(array('msg' => $msg));
            $errors = '';
            foreach ($msg as $k => $v)
            {
                $error = $v[obj] ? Lang::get($v[msg]) . " [" . Lang::get($v[obj]) . "]" : Lang::get($v[msg]);
                $errors .= $errors ? "<br />" . $error : $error;
            }
            echo "<script type='text/javascript'>window.parent.js_fail('" . $errors . "');</script>";
        }
    }

    function logout()
    {
        $this->visitor->logout();

        /* 跳转到登录页，执行同步退出操作 */
//         header("Location: index.php?app=member&act=login&synlogout=1");
        header("Location:/");
        return;
    }

    /* 执行登录动作 */
    function _do_login($user_id)
    {
        $mod_user =& m('member');

        $user_info = $mod_user->get(array(
            'conditions'    => "user_id = '{$user_id}'",
            'join'          => 'has_store',                 //关联查找看看是否有店铺
            'fields'        => 'user_id, user_name, reg_time, last_login, last_ip, store_id, def_addr,nickname',
        ));



        $t = time() +15 * 24 * 60 * 60;
//         setcookie('uname', $user_info['user_name'], $t);

//        $db = &db();
//        $sql = "select * from ".DB_PREFIX."member_lv where member_lv_id = '{$user_info['member_lv_id']}'";
//        $level = $db->getRow($sql);
//        $user_info['level'] = $level;

        /* 店铺ID */

        /* 分派身份 */
        $this->visitor->assign($user_info);


        /* 更新用户登录信息 */
        $mod_user->edit("user_id = '{$user_id}'", "last_login = '" . gmtime()  . "', last_ip = '" . real_ip() . "', logins = logins + 1");
        /* 更新购物车中的数据 */
        /* $mod_cart =& m('cart');
        $mod_cart->edit("user_id = '{$user_id}' OR session_id = '" . SESS_ID . "'", array(
            'user_id'    => $user_id,
            'session_id' => SESS_ID,
        )); */

        /* 去掉重复的项 
        $cart_items = $mod_cart->find(array(
            'conditions'    => "user_id='{$user_id}' GROUP BY spec_id",
            'fields'        => 'COUNT(spec_id) as spec_count, spec_id, rec_id',
        ));
        if (!empty($cart_items))
        {
            foreach ($cart_items as $rec_id => $cart_item)
            {
                if ($cart_item['spec_count'] > 1)
                {
                    $mod_cart->drop("user_id='{$user_id}' AND spec_id='{$cart_item['spec_id']}' AND rec_id <> {$cart_item['rec_id']}");
                }
            }
        }*/
    }

    /* 取得导航 */
    function _get_navs()
    {
        $cache_server =& cache_server();
        $key = 'common.navigation';
        $data = $cache_server->get($key);
        if($data === false)
        {
            $data = array(
                'header' => array(),
                'middle' => array(),
                'footer' => array(),
            );
            $nav_mod =& m('navigation');
            $rows = $nav_mod->find(array(
                'order' => 'type, sort_order',
            ));
            foreach ($rows as $row)
            {
                $data[$row['type']][] = $row;
            }
            $cache_server->set($key, $data, 86400);
        }

        return $data;
    }

	/* 取的帮助中心 */
    function _get_helps()
    {
		$articleModel = &m("article");
		$res = $articleModel->find(array(
		   'conditions' => "parent_id = '47' AND article.if_show =1 ORDER BY article.sort_order ASC",
		   'join' => "belongs_to_acategory",

		));

		$arr = array();
		foreach ($res AS $key => $row)
		{
			$arr[$row['cate_id']]['cate_name']                    = $row['cate_name'];
			$arr[$row['cate_id']]['article'][$key]['article_id']  = $row['article_id'];
			$arr[$row['cate_id']]['article'][$key]['title']       = $row['title'];
		}


		return $arr;
    }

	/* 取的顶部商品导航 */
    function _get_goodscats1()
    {
		$db = &db();
		$cat = $db->getall("select * from ".DB_PREFIX."gcategory where parent_id = '0' and store_id=0 order by cate_id desc");

		return $cat;
	}
    function _get_goodscats2()
    {
		$db = &db();
		$cat = $db->getall("select * from ".DB_PREFIX."gcategory where parent_id = '0' and store_id=0 order by cate_id desc");
		foreach($cat as $k=>$val){
			$arr1_id[]=$val['cate_id'];
		}
		$_cateid1 = empty($arr1_id) ? 0 : implode(",",$arr1_id);
		$arr0=$db->getall("select * from ".DB_PREFIX."gcategory where parent_id in ($_cateid1)");

		return $arr0;
	}

	/* 取所有商品分类 */
    function _list_gcategorys()
    {
        $cache_server =& cache_server();
        $key = 'page_goods_category';
        $data = $cache_server->get($key);
        if ($data === false)
        {
            $gcategory_mod =& bm('gcategory', array('_store_id' => 0));
            $gcategories = $gcategory_mod->get_list(-1,true);

            import('tree.lib');
            $tree = new Tree();
            $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
            $data = $tree->getArrayList(0);
            $cache_server->set($key, $data, 3600);
        }


        return $data;
    }

    /**
     *    获取JS语言项
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function jslang($lang="")
    {
        $lang = Lang::fetch(lang_file('jslang'));
        parent::jslang($lang);
    }

    /**
     *    视图回调函数[显示小挂件]
     *
     *    @author    Garbin
     *    @param     array $options
     *    @return    void
     */
    function display_widgets($options)
    {
        $area = isset($options['area']) ? $options['area'] : '';
        $page = isset($options['page']) ? $options['page'] : '';
        if (!$area || !$page)
        {
            return;
        }
        include_once(ROOT_PATH . '/includes/widget.base.php');

        /* 获取该页面的挂件配置信息 */
        $widgets = get_widget_config($this->_get_template_name(), $page);

        /* 如果没有该区域 */
        if (!isset($widgets['config'][$area]))
        {
            return;
        }

        /* 将该区域内的挂件依次显示出来 */
        foreach ($widgets['config'][$area] as $widget_id)
        {
            $widget_info = $widgets['widgets'][$widget_id];
            $wn     =   $widget_info['name'];
            $options=   $widget_info['options'];

            $widget =& widget($widget_id, $wn, $options);
            $widget->display();
        }
    }

    /**
     *    获取当前使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        return 'default';
    }

    /**
     *    获取当前使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        return 'default';
    }

    /**
     *    当前位置
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _curlocal($arr)
    {
        $curlocal = array(array(
            'text'  => Lang::get('index'),
            'url'   => SITE_URL . '/index.php',
        ));
        if (is_array($arr))
        {
            $curlocal = array_merge($curlocal, $arr);
        }
        else
        {
            $args = func_get_args();
            if (!empty($args))
            {
                $len = count($args);
                for ($i = 0; $i < $len; $i += 2)
                {
                    $curlocal[] = array(
                        'text'  =>  $args[$i],
                        'url'   =>  $args[$i+1],
                    );
                }
            }
        }

        $this->assign('_curlocal', $curlocal);
    }

    function _init_visitor()
    {
        $this->visitor =& env('visitor', new UserVisitor());
    }


    /**
     *    获取分页信息
     *
     *    @author    yhao.bai
     *    @return    array
     */
    function _get_page($page_per = 10,$params = array())
    {
        /* edit by Xiao5 融合参数 并且指定 分页 第几位置 */
        if ($params)
        {
            $page = empty($params['args'][$params['pagekey']]) || !is_numeric($params['args'][$params['pagekey']]) ? 1 : intval($params['args'][$params['pagekey']]);
        }else {
            $args = $this->get_params();
            $page = empty($args[0]) || !is_numeric($args[0]) ? 1 : intval($args[0]);
        }

    	$start = ($page -1) * $page_per;


    	$this->assign('p', $page);

    	return array('limit' => "{$start},{$page_per}", 'curr_page' => $page, 'pageper' => $page_per);
    }


    /**
     * 格式化分页信息 - link 方式
     * @author yhao.bai
     * @param   array   $page
     * @param   int     $num    显示几页的链接
     */
    function _format_page(&$page, $num = 7 ,$pam=array())
    {

    	//var_dump($this->_view);

    	$page['page_count'] = ceil($page['item_count'] / $page['pageper']);
    	$mid = ceil($num / 2) - 1;
    	if ($page['page_count'] <= $num)
    	{
    		$from = 1;
    		$to   = $page['page_count'];
    	}
    	else
    	{
    		$from = $page['curr_page'] <= $mid ? 1 : $page['curr_page'] - $mid + 1;
    		$to   = $from + $num - 1;
    		$to > $page['page_count'] && $to = $page['page_count'];
    	}

    	$args = $this->get_params();

    	$page['page_links'] = array();
    	$page['first_link'] = ''; // 首页链接
    	$page['first_suspen'] = ''; // 首页省略号
    	$page['last_link'] = ''; // 尾页链接
    	$page['last_suspen'] = ''; // 尾页省略号

    	$link = array('app' => APP, 'act' => ACT);

    	/* edit by Xiao5 融合参数 并且指定 分页 第几位置 */
    	if ($pam){
    	    foreach($pam['args'] as $key => $val){
    	        $link['arg'.$key] = $val;
    	    }
    	    $argkey = $pam['pagekey'];
    	}else {
    	    $argkey = 0;
    	    if($args){
    	        unset($args[0]);
    	        foreach($args as $key => $val){
    	            $link['arg'.$key] = $val;
    	        }
    	    }
    	}


    	for ($i = $from; $i <= $to; $i++)
    	{
    			$link['arg'.$argkey] = $i;
    			$page['page_links'][$i] = $this->_view->build_url($link);
    	}


    	if (($page['curr_page'] - $from) < ($page['curr_page'] -1) && $page['page_count'] > $num)
    	{		$link['arg'.$argkey] = 1;
		    	$page['first_link'] = $this->_view->build_url($link);

		    	if (($page['curr_page'] -1) - ($page['curr_page'] - $from) != 1)
			    			{
		    		$page['first_suspen'] = '..';
		    	 }
    	}

    	if (($to - $page['curr_page']) < ($page['page_count'] - $page['curr_page']) && $page['page_count'] > $num)
    	{
    		$link['arg'.$argkey] = $page['page_count'];
    		$page['last_link'] = $this->_view->build_url($link);
	    	if (($page['page_count'] - $page['curr_page']) - ($to - $page['curr_page']) != 1)
	    	{
	    		$page['last_suspen'] = '..';
	    	}
    	}

    	if($page['curr_page'] > $from)
    	{
	    	$link['arg'.$argkey] = $page['curr_page'] - 1;
	    	$page['prev_link'] = $this->_view->build_url($link);
   	    }
	    else
	    {
		    $page['prev_link'] = '';
	    }

	    if($page['curr_page'] < $to)
	    {
	    	$link['arg'.$argkey] = $page['curr_page'] + 1;
	    	$page['next_link'] = $this->_view->build_url($link);
	    }
	    else
	    {
    			$page['next_link'] = '';
    	}
   }

}
/**
 *    前台访问者
 *
 *    @author    Garbin
 *    @usage    none
 */
class UserVisitor extends BaseVisitor
{
    var $_info_key = 'user_info';

    /**
     *    退出登录
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function logout()
    {
        /* 将购物车中的相关项的session_id置为空 */
//         $mod_cart =& m('cart');
//         $mod_cart->edit("user_id = '" . $this->get('user_id') . "'", array(
//             'session_id' => '',
//         ));

        /* 退出登录 */
        parent::logout();
    }
}
/**
 *    商城控制器基类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MallbaseApp extends FrontendApp
{

    function _run_action()
    {

        /* 只有登录的用户才可访问 */
        // if (!$this->visitor->has_login && in_array(APP, array('apply')))
        //{
            //ns add
        	//$link = array("app" => "member","act" => "login" );
            //$_view = &v();
            //$url = $_view->build_url($link);
            //header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
            //header('Location: index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

            //return;
        //}

        parent::_run_action();
    }

    function _config_view()
    {
        parent::_config_view();

        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/h5/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/h5/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/h5/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    /* 取得支付方式实例 */
    function _get_payment($code, $payment_info)
    {
        include_once(ROOT_PATH . '/h5/includes/payment.base.php');
        include(ROOT_PATH . '/h5/includes/payments/' . $code . '/' . $code . '.payment.php');
        $class_name = ucfirst($code) . 'Payment';

        return new $class_name($payment_info);
    }
    //为了不影响之前的支付新开个方法吧
    function _get_paymentm($code, $payment_info)
    {
        include_once(ROOT_PATH . '/h5/includes/payment.base.php');
        include(ROOT_PATH . '/h5/includes/payments/' . $code . '/' . $code . '.payment.php');
        $class_name = ucfirst($code) . 'Payment';
    
        return new $class_name($payment_info);
    }

    /**
     *   获取当前所使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $template_name = Conf::get('template_name');
        if (!$template_name)
        {
            $template_name = 'default';
        }

        return $template_name;
    }

    /**
     *    获取当前模板中所使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $style_name = Conf::get('style_name');
        if (!$style_name)
        {
            $style_name = 'default';
        }

        return $style_name;
    }
    /* 获取当前店铺所使用的主题 */
    function _get_theme()
    {
    	$model_store =& m('store');
    	$store_info  = $model_store->get($this->visitor->get('manage_store'));
    	$theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
    	list($curr_template_name, $curr_style_name) = explode('|', $theme);
    	return array(
    			'template_name' => $curr_template_name,
    			'style_name'    => $curr_style_name,
    	);
    }
}


/**
 *    购物流程子系统基础类
 *
 *    @author    yhao.bai
 */
class ShoppingbaseApp extends FrontendApp
{
	var $_mod_figure;
	var $_mod_pay;
	public $_user_id;
	var $_measure_way = array(/*'1'=>'预约上门量体',*/'5'=>'现有量体数据','2'=>'去附近门店量体','6'=>'指定量体师',/* '3'=>'现有量体数据' *//*,'4'=>'标准尺码'*/);//标准尺码没算法  先屏蔽
	var $_mod_cart;
	
	public $_invoice_com = array(1=>'普通发票',3=>'增值税专用',2=>'增值税普通');
	public $_invoice_need = array(0=>'不需要',1=>'需要');
	public $_invoice_type = array(1=>'普通发票',3=>'增值税专用',2=>'增值税普通');
	public $_invoice_title = '';
	var $_source_from;
	
	/**
	 * 初始比相关模型
	 * @author yaho.bai
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->_mod_figure = &m("figure");
		$this->_mod_pay = &m("payment");
		$this->_mod_ship = &m("shipping");
		$this->_mod_subscribe  = &m("subscribe");
		$this->_mod_cart = &m('cart');
		
		$this->_pay_way = array('1'=>'在线支付','2'=>'货到付款');
		$this->_ship_way  = array('1'=>'快递服务','2'=>'门店自提');
		$this->_shipping_type = array('address'=>'收货地址','store'=>'门店自提');

        
		if (empty($this->_user_id) && $_SESSION['user_info']['user_id']){
		    $this->_user_id = $_SESSION['user_info']['user_id'];

		}
		
		//token做识别
		$this->_source_from = (isset($_SESSION['source']) && !empty($_SESSION['source']) ) ? $_SESSION['source'] : (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) ? 'app' : 'wap';
	}

    /**
     * 购物流程权限设置
     * 
     * @return void
     */
    function _run_action()
    {
        $guestActs = array();
//         var_dump($this->visitor->has_login);exit();
        if(isset($_SESSION['user_info']['user_id']) && $_SESSION['user_info']['user_id'])
        {
            $this->visitor->has_login = true;
        }
        if (!$this->visitor->has_login && !in_array(ACT, $guestActs)){
        //if (!$this->_user_id && !in_array(ACT, $guestActs)){
            $view = &v();
            $url = $view->build_url(array("app" => "member","act" => "login" ));
            if (!IS_AJAX){
              $url = 'http://'.$_SERVER['HTTP_HOST'].$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
                header('Location:'.$url);
                return;
            }else{
                $this->json_error('login_please','login');
                return;
            }
        }
        parent::_run_action();
    }

    function _config_view()
    {
    	parent::_config_view();

        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/h5/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/h5/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/h5/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
    
    
    /**
     * 购物车数据
     *
     * @author Ruesin
     */
    function _cart_main($fmt = 0){
    
        $goods =  $this->_mod_cart->_cart_main($this->_user_id,$_SESSION['_cart']['check']);
        if($fmt)
            return $this->_cart_format($goods);
        return $goods;
    }
    
    /**
     * 购物车已选择提交的数据
     *
     * @author Ruesin
     */
    function _cart_check($fmt = 0 ){
  
        $goods =  $this->_mod_cart->_cart_check($this->_user_id,$_SESSION['_cart']['check']);
        if($fmt)
            return $this->_cart_format($goods);
        return $goods;
    }
    
    /**
     * 格式化购物车数据
     *
     * @date 2015-8-26 上午9:58:21
     * @author Ruesin
     */
    function _cart_format($goods = array()){
    
        if(empty($goods['object'])) return $goods;
    
        foreach ($goods['object'] as $row){
            //if($row['type'] == 'suit'){
            if($row['list']){
                foreach ($row['list'] as $arr){
                    $result[$arr['ident']] = $arr;
                }
            }else{
                $result[$row['ident']] = $row;
            }
        }
    
        $goods['object'] = $result;
        return $goods;
    }
    
    
    /**
     * 拆分格式化购物项
     *
     * @author Ruesin
     */
    function _cart_main_format($carts = array()){
        if(!$carts) return false;
        foreach ((array)$carts['object'] as $row){
            //if($row['type'] == 'suit'){
            if($row['list']){
                foreach ($row['list'] as $arr){
                    $result[$arr['ident']] = $arr;
                }
            }else{
                $result[$row['ident']] = $row;
            }
        }
        $carts['object'] = $result;
        return $carts;
    }
    
    /**
     * 校验库存
     *
     * @author Ruesin
     */
    function _check_stock($carts = array()){
        
        foreach ($carts as $row){
            $data[$row['fabric']][$row['cloth']]['cloth'] = $row['cloth'];
            $data[$row['fabric']][$row['cloth']]['fabric'] = $row['fabric'];
            $data[$row['fabric']][$row['cloth']]['quantity'] += $row['quantity'];
        }

        $ck = $this->_mod_cart->_check_stock($data);
        if($ck !== true){
            $this->json_error($ck.'库存不足!');
            die();
        }
        return true;
    }

    /**
     * 订单统计(由于此项目无优惠积分等,所以此操作等于是无改变)
     * 
     * @author Ruesin
     */
    function _total($order = array()){
    	$result = array(
    		'object'        => array(),
    		'goods_amount'  => 0,
	        'order_amount'  => 0,
	        'final_amount'  => 0,
    		'goods_num'     => 0,
    		'discount'      => 0,
    		'weight'        => 0,
    		'point_g'       => 0,
	        'debit_fee'     => 0,
	        'kuka_fee'      => 0,
    	);
    	if(!$_SESSION['_cart']['check']){
    	    return $result;
    	}
    	
    	$main    = $this->_cart_check();

    	if(empty($main['object'])){
    		return $result;
    	}
    	
    	$result  = $main;

    	
    	
    	
    	if($order['kuka']['data']){
    	    foreach($order['kuka']['data'] as $key => $val){
    	        $result['discount']  += $val['work_num'];
    	        $result['kuka_fee']  += $val['work_num'];
    	    }
    	    $result['final_amount'] -= $result['kuka_fee'];
    	}
    	
    	if($main['has_activity'] != 'yes'){
    	    
    	    //抵扣券  // 这个是笼统的算法 不够严谨 是会有算法漏洞的
    	    if($order['debit']['data']){
    	        foreach($order['debit']['data'] as $key => $val){
    	            $result['discount'] += $val['money'];
    	            $result['debit_fee'] += $val['money'];
    	        }
    	        $result['final_amount'] -= $result['debit_fee'];
    	    }
    	    
    	    if($order['pmt']['coin'] > 0){
    	        $result['coin'] += $order['pmt']['coin'];
    	        $result['final_amount'] -= $result['coin'];
    	    }
    	    
    	}else{
    	    if ($this->visitor->get('member_lv_id') >1 ){
    	        //麦富迪币
    	        if($order['pmt']['coin'] > 0){
    	            $result['coin'] += $order['pmt']['coin'];
    	            $result['final_amount'] -= $result['coin'];
    	        }
    	    }
    	}
    	//余额
    	if($order['pmt']['money'] > 0){
    	    $result['money_amount'] += $order['pmt']['money'];
    	    $result['final_amount'] -= $result['money_amount'];
    	}
    	
    	
    	$result['final_amount'] = $result['final_amount'] >= 0 ? $result['final_amount'] : 0;
    	
    	
    	//使用抵扣券不给积分
    	//=====  add by liang.li 麦富迪币也算到积分里面  =====
    	$result['point_g'] = ($result['final_amount'] + $result['money_amount'] + $result['coin']) * 0.1;
    	
    	$_SESSION['_order'] = $order;

    	return $result;
    }

    /**
     * 初始化订单信息
     * 
     * @author Ruesin
     */
    function _order_info($aCart=[]){
        
    	$order = isset($_SESSION['_order']) ? $_SESSION['_order'] : array();
//        $order = [];
    	/* 预约量体信息 */
    	if($order['measure']){
    	    $order['measure']['data'][$order['measure']['type']]['type_name'] = $this->_measure_way[$order['measure']['type']];
    	}
    	/* 默认地址 */
    	if(!$order['shipping']){
    	    
    	    $order['shipping']['type'] = 'address';
    		$order['shipping']['address'] = array();
    		//$this->visitor->get("def_addr");
    		$mAddr = &m('address');
    		$addrs = $mAddr->find("user_id = '{$this->_user_id}'");
    		if($addrs){
    		    $mMem = &m('member');
    		    $mem = $mMem->get($this->_user_id);
    		    if($mem['def_addr']){
    		        $dft = $addrs[$mem['def_addr']];
    		        if(!$dft){
    		            $dft = array_shift($addrs);
    		        }
    		    }else{
    		        $dft = array_shift($addrs);
    		    }
    		}
			if($dft){
				$order['shipping']['address'] = $dft;
			}else{
				$order['shipping']['type'] = 'no_choice';
			}
    		
    	}

    	/* 默认物流公司 */
//     	if(!isset($order['ships']) || !$order['ships']){
//        echo '<pre>';print_r($aCart);exit;
    	    imports("orders.lib");
    	    $orderLib = new Orders();
    	    $shipInit = $orderLib->getShipInit(['ship_area_id'=>$order['shipping']['address']['region_id'],'weight'=>$aCart['weight']]);
            $order['ships']['defship'] = $shipInit['defship'];
    

             if($aCart['is_try'] == 1)
             {

                 $order['ships']['defship']['post_fee'] += 9.9;

             }
//echo '<pre>';print_r($order);exit;

             $_SESSION['_order']['ships']['defship'] = $shipInit['defship'];
//     	}
//echo '<pre>';print_r($shipInit);exit;

        if(isset($_REQUEST['did'])) //传参搞优惠券
        {
            $did = intval($_REQUEST['did']);
            $time = time();
            $mDbt = &m('voucher');
            $debits = $mDbt->find(" binding_user_id = '".$this->_user_id."' AND use_status = 0  AND end_time >'{$time}' AND (id =$did)");

            $diy_price = $aCart['diy_price'];
            $custom_price = $aCart['custom_price'];
            foreach ((array)$debits as $index => &$d)
            {
                $money = $d['money'];
                if($d['category'] == '1')
                {
                    if($diy_price < $money)
                    {
                        $debits[$index]['money'] = $diy_price;
                    }
                }
                elseif ($d['category'] == '2')
                {
                    if($custom_price < $money)
                    {
                        $debits[$index]['money'] = $custom_price;
                    }
                }
            }
            $order['debit']['data'] = $debits;
            $order['discount'] += $debits[$did]['money'];//优惠价格
        }
    	/*发票*/
    	if(!$order['invoice']){
            $order['invoice'] = array();
    	}
    	return $order;
    }

    /**
     * 量体数据列表
     * @author yaho.bai
     * @return array
     */
    function _figureList()
    {
    	if(!$this->visitor->has_login) return array();

    	$_where = "userid = '{$this->visitor->get('user_id')}'";

    	$list = $this->_mod_figure->find(array(
    				'conditions' => $_where,
    				'order'      => "userid DESC, idfigure ASC",
    				'limit'      => "0,1",
    			));
    	return $list;
    }

    /**
     * 获取量体数据
     * @param int $id 量体数据id
     * @return array
     */
    function _figureInfo($id)
    {
    	$id = intval($id);
    	if($id < 1) return false;

    	$data = $this->_mod_figure->get_info($id);

    	$data['figure'] = $data["idfigure"];
    	$data['service'] = $data['idserve'];

    	if(!$data["userid"] || ($data["userid"] != $this->visitor->get('user_id'))){
    		return false;
    	}
    	return $data;
    }


    /**
     * 支付列表
     */
    function payments(){
    	return $this->_mod_pay->find(array(
    				'conditions' => "enabled=1 AND ismobile=0",
    				'order'      => "sort_order DESC"
    			));
    }

    /**
     * 支付详细信息
     */
    function payInfo($id){
    	$pay = $this->_mod_pay->get(array(
    			"conditions" => "payment_id = '{$id}' AND enabled = 1"));
    	if(!$pay){
    		return array();
    	}
    	return $pay;
    }


    /* 取得支付方式实例 */
    function _get_payment($code, $payment_info)
    {
    	include_once(ROOT_PATH . '/includes/payment.base.php');
    	include(ROOT_PATH . '/includes/payments/' . $code . '/' . $code . '.payment.php');
    	$class_name = ucfirst($code) . 'Payment';

    	return new $class_name($payment_info);
    }
    //为了不影响之前的支付新开个方法吧
    function _get_paymentm($code, $payment_info)
    {
        include_once(ROOT_PATH . '/h5/includes/payment.base.php');
        include(ROOT_PATH . '/h5/includes/payments/' . $code . '/' . $code . '.payment.php');
        $class_name = ucfirst($code) . 'Payment';
    
        return new $class_name($payment_info);
    }

    /* 获取当前用户可用优惠券 */
    function couponlist(){

    	$objCou = &f("coupon");

    	$_order = $this->_order_info();

    	$cart_coupons = array();

    	foreach((array)$_order['coupons'] as $key => $val){
    		$cart_coupons[] = $val["sn"];
    	}

    	$list  = $objCou->getUserCouponList($this->visitor->get("user_id"), 0);

    	foreach((array)$list as $key => $val){
    		if(in_array($val['coupon_sn'], $cart_coupons)){
    			$list[$key]['checked'] = true;
    		}
    	}
    	$this->assign("item_list", $list);
    	$str = $this->_view->fetch("coupon.cart.item.html");

    	$this->json_result($str);
    }   
    /**
     * 字符串加密以及解密函数
     * @param string $string 原文或者密文
     * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
     * @param string $key 密钥
     * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
     * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
     * @see uc_authcode
     * @version 1.0.0 (2014-11-9)
     * @author Xiao5
     */
    function ApiAuthcode($string, $operation = 'DECODE', $key = 'API-mfd', $expiry = 0) {
    
        $ckey_length = 4;
    
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
    
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
    
        $result = '';
        $box = range(0, 255);
    
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
    
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
    
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
    
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
        
}



/**
 *    支付中心子系统基础类
 *
 *    @author    yhao.bai
 */
class PaycenterbaseApp extends MallbaseApp
{
	/**
	 * 初始比相关模型
	 * @author yaho.bai
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

	}

	/**
	 * 购物流程权限设置
	 * @author yaho.bai
	 * @return void
	 */
	function _run_action()
	{
		$postActs = array('goToPay','change_payment');//POST列表,非POST不可访问
        if(!IS_POST && in_array(ACT, $postActs)){
            return ;
        }
        $guestActs = array('notify', 'accountnotify','index','fxanotify');//游客允许列表
        if (!$_SESSION['user_info']['user_id'] && !in_array(ACT, $guestActs)){
            $view = &v();
            $url = $view->build_url(array("app" => "member","act" => "login" ));
            if (!IS_AJAX){
                //$this->show_warning('login_please');
                header('Location:'.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
                return;
            }else{
                $this->json_error('login_please','login');
                return;
            }
        }

        /* $usersActs = array();//普通会员禁止列表 //普通会员可以支付了，故把权限改下
        $user = $this->visitor->get();
        if(!$user['has_store'] && in_array(ACT, $usersActs)){
            if (!IS_AJAX){
                $this->show_warning('you_are_not_store');
                return ;
            }else{
                $this->json_error('you_are_not_store','you_are_not_store');
                return;
            }
        } */
        parent::_run_action();
	}



	/**
	 * @author yhao.bai
	 * @param int $order_id 订单id
	 * @return arr
	 */
	function _get_order($order_sn){

		$order_model =& m('order');
		$where = "order_sn = '{$order_sn}'";
		if($this->visitor->has_login){
			$where .= "  AND user_id='{$this->visitor->get('user_id')}'";//订单中store_id字段已作废，只使用user_id关联用户
			
			/*
		    if($this->visitor->get('has_store')){
		        $where .= "  AND store_id='{$this->visitor->get('user_id')}'";
		    }else{
		        $where .= "  AND user_id='{$this->visitor->get('user_id')}'";
		    }*/
		}
		$order  = $order_model->get($where);

		if (empty($order))
		{
			$this->assign('error_msg', '未找到对应订单');
			$this->display('order/error.html');
			return false;
		}

		return $order;
	}
}





/**
 *    用户中心子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MemberbaseApp extends MallbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$_SESSION['user_info']['user_id'] &&
                !in_array(ACT, array(
                    'login', 'register', 'check_user','eValidation','ActivationEail',
                    'mbregister','verifycode','check_account','check_verifycode',
                    'findpsSMSCode','findpsEmail','findpsRestSMSCode','findpsResetEmail','upload',
                    'resetSMSCode','find_password','set_password','register_confirm', 'rechargeToApp','agreement'))
        ){

         if (!IS_AJAX)
         {
                //ns add
                $link = array("app" => "member","act" => "login" );
                $_view = &v();
                $url = $_view->build_url($link);

                header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }
        $this->assign('has_store', $this->visitor->get('has_store'));
        $this->assign('ac', ACT);
        parent::_run_action();
    }
    /**
     * 裁缝中心 初始控制器
     * @return void
     * @access public
     * @version 1.0.0 (2014-11-17)
     * @author Xiao5
     */
    public function tailor()
    {
    	define('AC', 'tailor');
        if (!$this->visitor->get('has_store'))
        {
            $this->show_warning('has_login');
            return;
        }
        $args = $this->get_params();
        $internalType = empty($args[0]) ? '1' : $args[0];
        switch ($internalType)
        {
            //新增
            case 'add':
                $this->add();
                break;
                //修改
            case 'edit':
                $this->edit();
                break;
                //裁缝中心-录入
            case 'entry':
                $this->entry();
                break;
                //删除
            case 'drop':
                $this->drop();
                break;
                //默认首页
            case 'view':
                $this->view();
                break;
            default:
                $this->index();
                break;
        }

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
        $this->_view->template_dir = ROOT_PATH . "/h5/view/".LANG."/mall/{$template_name}/user";
        $this->_view->compile_dir  = ROOT_PATH . "/h5/temp/compiled/mall/{$template_name}/user";
        $this->_view->res_base     = SITE_URL . "/h5/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }


    /**
     *    当前选中的菜单项
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curitem($item)
    {
		// if($_SESSION['user_info']['serve_type'] == '0')
		// {$this->assign('is_member', 1);}

        $this->assign('_member_menu', $this->_get_member_menu());
        $this->assign('_curitem', $item);
        $user = $this->visitor->get();
        $this->assign('user', $user);
    }
    /**
     *    当前选中的子菜单
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curmenu($item)
    {
        $_member_submenu = $this->_get_member_submenu();
        foreach ($_member_submenu as $key => $value)
        {
            $_member_submenu[$key]['text'] = $value['text'] ? $value['text'] : Lang::get($value['name']);
        }
		if($_SESSION['user_info']['serve_type'] == '0')
		{$this->assign('is_member', 1);}

        $this->assign('_member_submenu', $_member_submenu);
        $this->assign('_curmenu', $item);
    }
    /**
     *    获取子菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_submenu()
    {
        return array();
    }
    /**
     *    获取用户中心全局菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_menu($t='')
    {
        $menu = array();
        $_view = &v();
        $args = $this->get_params();
        if (!$t)
        {
            $t = empty($args[0]) ? 'user' : $args[0];
        }

        //用户中心-默认
        $menu['user']['trading'] = array(
            'name'  => 'my_userInfo',
            'text'  => Lang::get('trading'),
            'submenu' => array(
            //我的订单
            'my_order'  => array(
                    'text'  => Lang::get('my_order'),
                    'url'   => 'buyer_order.html',
                    'name'  => 'my_order',
                    'icon'  => 'ico5',
                ),
           //我的需求
           'my_demand'  => array(
                    'text'  => Lang::get('my_demand'),
                    'url'   => 'my_demand.html',
                    'name'  => 'my_demand',
                    'icon'  => 'ico5',
                ),
            //我的晒单
            'my_sunsingle'  => array(
                    'text'  => lang::get('my_sunsingle'),
                    'url'   => 'my_single.html',
                    'name'  => 'my_sunsingle',
            ),
            //量体信息
            'my_body' => array(
                    'text'  => lang::get('my_body'),
                    'url'   => 'my_body.html',
                    'name'  => 'my_body',
            ),

            //收货地址
            'my_address'  => array(
                    'text'  => lang::get('my_address'),
                    'url'   => 'my_address.html',
                    'name'  => 'my_address',
            ),
            )
        );
         $menu['user']['relation'] = array(
            'name'  => 'relation',
            'text'  => Lang::get('relation'),
            'submenu' => array(
                //我的关注
                'my_follow'  => array(
                    'text'  => Lang::get('my_follow'),
                    'url'   => 'my_follow.html',
                    'name'  => 'my_follow',
                    'icon'  => 'ico5',
                )
            )
        );
        //投诉管理
        $menu['user']['complaint'] = array(
            'name'  => 'complaint',
            'text'  => Lang::get('complaint'),
            'submenu' => array(
                //我的投诉
                'my_follow'  => array(
                    'text'  => Lang::get('my_complaint'),
                    'url'   => 'my_complaint.html',
                    'name'  => 'my_complaint',
                    'icon'  => 'ico5',
                )
            )
        );
         //用户中心-账户设置
         $menu['user_set']['epay_set'] = array(
             'name'  => 'relation',
             'text'  => Lang::get('epay_set'),
             'submenu' => array(
                 //个人资料
                 'my_profile'  => array(
                     'text'  => Lang::get('my_profile'),
                     'url'   => 'member-'.ACT.'-profile.html',
                     'name'  => 'my_profile',
                     'icon'  => 'ico5',
                 ),

                 //安全设置
                /*  'safety'  => array(
                     'text'  => Lang::get('safety'),
                     'url'   => '',
                     'name'  => 'safety',
                     'icon'  => 'ico5',
                 ), */
                 //修改密码
                 'my_password'  => array(
                     'text'  => Lang::get('my_password'),
                     'url'   => 'member-'.ACT.'-password.html',
                     'name'  => 'my_password',
                     'icon'  => 'ico5',
                 ),
                 //修改密码
                 'auth'  => array(
                 		'text'  => '身份认证',
                 		'url'   => 'member-'.ACT.'-auth.html',
                 		'name'  => 'auth',
                 		'icon'  => 'ico5',
                 )

             )
         );
         //用户中心-服务通知
         $menu['user_message']['message'] = array(
             'name'  => 'serve_message',
             'text'  => Lang::get('serve_message'),
             'submenu' => array(
                 //交易提醒
                 't_reminding'  => array(
                     'text'  => Lang::get('t_reminding'),
                     'url'   => 'member-'.ACT.'-user_message-business.html',
                     'name'  => 'business',
                     'icon'  => 'ico5',
                 ),
				 'c_reminding'  => array(
                     'text'  => Lang::get('c_reminding'),
                     'url'   => 'member-'.ACT.'-user_message-complaint.html',
                     'name'  => 'complaint',
                     'icon'  => 'ico5',
                 ),
             )
         );
         //用户中心-互动通知
         $menu['user_message']['i_notification'] = array(
             'name'  => 'i_notification',
             'text'  => Lang::get('i_notification'),
             'submenu' => array(
                 //评论
                 'comment'  => array(
                     'text'  => Lang::get('comment'),
                     'url'   => 'member-'.ACT.'-user_message-comment.html',
                     'name'  => 'comment',
                     'icon'  => 'ico5',
                 ),
                 //互动提醒
                 'i_remind'  => array(
                     'text'  => Lang::get('i_remind'),
                     'url'   => 'member-'.ACT.'-user_message-sync.html',
                     'name'  => 'sync',
                     'icon'  => 'ico5',
                 )

             )
         );
         //用户中心-我的收藏
         $menu['user_favorite']['favorite'] = array(
             'submenu' => array(
                 //收藏的成衣
                 'favorite_custom'  => array(
                     'text'  => Lang::get('favorite_custom'),
                     'url'   => 'member-'.ACT.'-user_favorite-1-1.html',
                     'name'  => 'favorite_custom',
                     'icon'  => 'ico5',
                 ),
                 //收藏的晒单
                 'favorite_sunsingle'  => array(
                     'text'  => Lang::get('favorite_sunsingle'),
                     'url'   => 'member-'.ACT.'-user_favorite-1-2.html',
                     'name'  => 'favorite_sunsingle',
                     'icon'  => 'ico5',
                 ),
                 //收藏的面料
                 'favorite_fabric'  => array(
                     'text'  => Lang::get('favorite_fabric'),
                     'url'   => 'member-'.ACT.'-user_favorite-1-3.html',
                     'name'  => 'favorite_fabric',
                     'icon'  => 'ico5',
                 )

             )
         );

        if (!$this->visitor->get('has_store') && Conf::get('store_allow'))
        {
            $menu['overview'] = array(
                'text' => Lang::get('apply_store'),
                'url'  => 'index.php?app=apply',
            );
        }
        if ($this->visitor->get('manage_store')&& ACT == 'tailor' || $this->visitor->get('manage_store')&& AC == 'tailor')
        {
            unset($menu['user']['trading']);
            unset($menu['user']['relation']);
            unset($menu['user']['complaint']);
            //裁缝中心-默认
            $menu['user']['service_manage'] = array(
                'name'  => 'service_manage',
                'text'  => Lang::get('service_manage'),
                'submenu' => array(
                    //服务信息
                    'service_info'  => array(
                        'text'  => Lang::get('service_info'),
                        'url'   => 'member-tailor.html',
                        'name'  => 'service_info',
                        'icon'  => 'ico5',
                    ),
                    //我的订单
                    'my_order'  => array(
                        'text'  => Lang::get('my_order'),
                        'url'   => 'tailor_order-tailor.html',
                        'name'  => 'my_order',
                        'icon'  => 'ico5',
                    ),
                    //提交订单
                    'order-add' => array(
                        'text'  => '提交订单',
                        'url'   => 'order-add.html',
                        'name'  => 'order-add',
                        'icon'  => 'ico5',
                    ),
                    //我的消费者
                    'my_customer'  => array(
                        'text'  => Lang::get('my_buyer'),
                        'url'   => 'tailor_customer-tailor.html',
                        'name'  => 'my_customer',
                        'icon'  => 'ico5',
                    ),
                    //报价记录
                    'demand_offer' => array(
                        'text'  => '报价记录',
                        'url'   => 'demand_offer-tailor.html',
                        'name'  => 'demand_offer',
                        'icon'  => 'ico5',
                    ),
                    //派人量体
                    'dispatching' => array(
                        'text'  => '申请派工量体',
                        'url'   => 'dispatching-tailor.html',
                        'name'  => 'dispatching',
                        'icon'  => 'ico5',
                    )
                )
            );
            //裁缝中心-主页管理
            $menu['user']['home_manage'] = array(
                'name'  => 'home_manage',
                'text'  => Lang::get('home_manage'),
                'submenu' => array(
                    //我的作品
                    'my_works'  => array(
                        'text'  => Lang::get('my_works'),
                        'url'   => 'my_works-tailor.html',
                        'name'  => 'my_works',
                        'icon'  => 'ico5',
                    ),
                    //模板管理
                    // 'my_theme' => array(
                    //     'text'  => Lang::get('my_theme'),
                    //     'url'   => 'my_theme-tailor.html',
                    //     'name'  => 'my_theme',
                    //     'icon'  => 'ico5',
                    // )
                )
            );
            $menu['user']['relation'] = array(
                'name'  => 'relation',
                'text'  => Lang::get('relation'),
                'submenu' => array(
                    //我的需求
                    'my_fans'  => array(
                        'text'  => Lang::get('fans'),
                        'url'   => 'my_fans-tailor.html',
                        'name'  => 'my_fans',
                        'icon'  => 'ico5',
                    )
                )
            );
            //面料管理
            $menu['user']['fabric_manage'] = array(
                'name'  => 'fabric_manage',
                'text'  => Lang::get('fabric_manage'),
                'submenu' => array(
                //面料查询
                'fabric_query'  => array(
                        'text'  => Lang::get('fabric_query'),
                        'url'   => 'fabric_query-tailor.html',
                        'name'  => 'fabric_query',
                        'icon'  => 'ico5',
                    ),
                    //面料申请
               'fabric_apply'  => array(
                        'text'  => '采购申请',
                        'url'   => 'fabric_apply-tailor.html',
                        'name'  => 'fabric_apply',
                        'icon'  => 'ico5',
                    )
                )
            );

        }

        // add by xiao5 暂时只显示用户中心的菜单
//         unset($menu['im_seller']);
        return $menu[$t];
    }
}

/**
 *    店铺管理子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StoreadminbaseApp extends MemberbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user')))
        {
            if (!IS_AJAX)
            {
            //ns add
            $link = array("app" => "member","act" => "login" );
            $_view = &v();
            $url = $_view->build_url($link);
            header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
                // header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }
        $referer = $_SERVER['HTTP_REFERER'];
        if (strpos($referer, 'act=login') === false)
        {
            $ret_url = $_SERVER['HTTP_REFERER'];
            $ret_text = 'go_back';
        }
        else
        {
            $ret_url = SITE_URL . '/index.php';
            $ret_text = 'back_index';
        }

        /* 检查是否是店铺管理员 */
        if (!$this->visitor->get('manage_store'))
        {
            /* 您不是店铺管理员 */
            $this->show_warning(
                'not_storeadmin',
                'apply_now', 'apply.html',
                $ret_text, $ret_url
            );

            return;
        }

        /* 检查是否被授权 */
        $privileges = $this->_get_privileges();

        /* 检查店铺开启状态 */
        $state = $this->visitor->get('state');
        if ($state == 0)
        {
            $this->show_warning('apply_not_agree', $ret_text, $ret_url);

            return;
        }
        elseif ($state == 2)
        {
            $this->show_warning('store_is_closed', $ret_text, $ret_url);

            return;
        }

        /* 检查附加功能 */
        if (!$this->_check_add_functions())
        {
            $this->show_warning('not_support_function', $ret_text, $ret_url);
            return;
        }

        parent::_run_action();
    }
    function _get_privileges()
    {
        $store_id = $this->visitor->get('manage_store');
        $privs = $this->visitor->get('s');

        if (empty($privs))
        {
            return '';
        }

        foreach ($privs as $key => $admin_store)
        {
            if ($admin_store['store_id'] == $store_id)
            {
                return $admin_store['privs'];
            }
        }
    }

    /* 获取当前店铺所使用的主题 */
    function _get_theme()
    {
        $model_store =& m('store');
        $store_info  = $model_store->get($this->visitor->get('manage_store'));
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($curr_template_name, $curr_style_name) = explode('|', $theme);
        return array(
            'template_name' => $curr_template_name,
            'style_name'    => $curr_style_name,
        );
    }

    function _check_add_functions()
    {
        $apps_functions = array( // app与function对应关系
//             'seller_groupbuy' => 'groupbuy',
            'coupon' => 'coupon',
        );
        if (isset($apps_functions[APP]))
        {
            $store_mod =& m('store');
            $settings = $store_mod->get_settings($this->_store_id);
            $add_functions = isset($settings['functions']) ? $settings['functions'] : ''; // 附加功能
            if (!in_array($apps_functions[APP], explode(',', $add_functions)))
            {
                return false;
            }
        }
        return true;
    }
}

/**
 *    店铺控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StorebaseApp extends FrontendApp
{
    var $_store_id;

    /**
     * 设置店铺id
     *
     * @param int $store_id
     */
    function set_store($store_id)
    {
        $this->_store_id = intval($store_id);

        /* 有了store id后对视图进行二次配置 */
        $this->_init_view();
        $this->_config_view();
    }

    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        //ns up 修改路径
        $this->_view->template_dir = ROOT_PATH . "/view/sc-utf-8/store/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/store/{$template_name}";
        //修改样式路径
        $this->_view->res_base     = SITE_URL . "/view/sc-utf-8/store/{$template_name}/styles/{$style_name}";
    }

    /**
     * 取得店铺信息
     */
    function get_store_data()
    {
        $cache_server =& cache_server();
        $key = 'function_get_store_data_' . $this->_store_id;
        $store = $cache_server->get($key);
        if ($store === false)
        {
            $store = $this->_get_store_info();
            if (empty($store))
            {
                $this->show_warning('the_store_not_exist');
                exit;
            }
            if ($store['state'] == 2)
            {
                $this->show_warning('the_store_is_closed');
                exit;
            }
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store_mod =& m('store');
            $store['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($store['credit_value'], $step);

            empty($store['store_logo']) && $store['store_logo'] = Conf::get('default_store_logo');
            $store['store_owner'] = $this->_get_store_owner();
            $store['store_navs']  = $this->_get_store_nav();
            $goods_mod =& m('goods');
            $store['goods_count'] = $goods_mod->get_count_of_store($this->_store_id);
            $store['sgrade'] = $this->_get_store_grade('grade_name');
            $functions = $this->_get_store_grade('functions');
            $store['functions'] = array();
            if ($functions)
            {
                $functions = explode(',', $functions);
                foreach ($functions as $k => $v)
                {
                    $store['functions'][$v] = $v;
                }
            }
            $cache_server->set($key, $store, 1800);
        }

        return $store;
    }

    /* 取得店铺信息 */
    function _get_store_info()
    {
        if (!$this->_store_id)
        {
            /* 未设置前返回空 */
            return array();
        }
        static $store_info = null;
        if ($store_info === null)
        {
            $store_mod  =& m('store');
            $store_info = $store_mod->get_info($this->_store_id);
        }

        return $store_info;
    }

    /* 取得店主信息 */
    function _get_store_owner()
    {
        $user_mod =& m('member');
        $user = $user_mod->get($this->_store_id);

        return $user;
    }

    /* 取得店铺导航 */
    function _get_store_nav()
    {
        $article_mod =& m('article');
        return $article_mod->find(array(
            'conditions' => "store_id = '{$this->_store_id}' AND cate_id = '" . STORE_NAV . "' AND if_show = 1",
            'order' => 'sort_order',
            'fields' => 'title',
        ));
    }

    /*  取的店铺等级   */
    function _get_store_grade($field)
    {
        $store_info = $store_info = $this->_get_store_info();
        $sgrade_mod =& m('sgrade');
        $result = $sgrade_mod->get_info($store_info['sgrade']);
        return $result[$field];
    }

    /**
     *    获取当前店铺所设定的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $template_name;
    }

    /**
     *    获取当前店铺所设定的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $style_name;
    }
}

/* 实现消息基础类接口 */
class MessageBase extends MallbaseApp {};

/* 实现模块基础类接口 */
class BaseModule  extends FrontendApp {};

/* 消息处理器 */
require(ROOT_PATH . '/eccore/controller/message.base.php');

class DemandBase extends FrontendApp
{
    function __construct()
    {
        parent::__construct();
    }
    function _config_view()
    {
        parent::_config_view();
        $this->_view->template_dir  = ROOT_PATH . '/view/'.LANG.'/mall/default';
    }
    /**
     * 定制需求权限
     * @author Ruesin
     * @return void
     */
    function _run_action()
    {
        $postActs = array('subsue','offer','ajaxcode');//POST列表,非POST不可访问
        if(!IS_POST && in_array(ACT, $postActs)){
            return ;
        }
        $guestActs = array('sue','subsue','offer','ajaxcode','ajaxoffer');//游客禁止列表
        if (!$this->visitor->has_login && in_array(ACT, $guestActs)){ //没有登录,这些方法不可用
            $view = &v();
            $url = $view->build_url(array("app" => "member","act" => "login" ));
            if (!IS_AJAX){
                header('Location:'.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
                return;
            }else{
                $this->json_error('login_please','login');
                return;
            }
        }

        $storeActs = array('sue','subsue','ajaxoffer');//裁缝禁止列表
        $usersActs = array('offer');//普通会员禁止列表
        $user = $this->visitor->get();

        if($user['has_store'] && in_array(ACT, $storeActs)){//如果是裁缝,这些方法不可访问
            $this->show_warning('您是裁缝，不能发布需求哦！');
            return;
        }
        if(!$user['has_store'] && in_array(ACT, $usersActs)){//如果是普通会员,这些方法不可访问
            return ;
        }
        parent::_run_action();
    }
    function display($tpl){
        $this->assign('current', 'demand');
        parent::display($tpl);
    }
    function _get_type($type){
        include_once(ROOT_PATH . '/includes/demand.base.php');
        include(ROOT_PATH . '/includes/demandtypes/' . $type . '.dtype.php');
        $class_name = ucfirst($type) . 'Demand';
        return new $class_name($payment_info);
    }
    /**
     * 获取报价列表
     * @access public
     * @see _get_offer_list
     * @version 1.0.0 (2014-12-18)
     * @author Ruesin
     */
    function _get_offer_list($info = array(),$page = 1){
//         if($info['status'] == 2)//未选
//         {
            $page = abs($page);
            $lm = 10;
            $limit = ($page-1)*$lm . " , " . $page*$lm;
            $offer['list'] = $this->_mod_demandoffer->find(array(
                'conditions' => "md_id = {$info['md_id']} order by sub_time desc  ",
                'count' => true,
                'limit'      => ' '.$limit,
            ));
            $count = $this->_mod_demandoffer->getCount();

            $offer['page'] = $page;
            $offer['pageCount'] = ceil($count/$lm);

//         }
        if($info['status'] == 3)//已选
        {
            //$offer['data'] = $this->_mod_demandoffer->get($info['offer_id']);
            $offer['data'] = $this->_mod_demandoffer->get(" md_id = '{$info['md_id']}' AND status = 2");
        }
        $offer['status'] = $info['status'];

        $user = $this->visitor->get();
        if($user['has_store']){
            $cf = $this->_mod_demandoffer->get(" md_id = {$info['md_id']} AND cf_id = {$user['user_id']} ");
            if(!empty($cf)){
                $offer['type'] = 3;//已报价裁缝
            }else{
                $offer['type'] = 1;//未报价裁缝
            }
        }else{
            if($info['user_id'] == $user['user_id']){
                $offer['type'] = 2;//本需求的会员
            }else{
                $offer['type'] = 3;//其他普通会员
            }
        }
        $offer['md_id'] = $info['md_id'];
        $offer['now'] = gmtime();
        return $offer;
    }

    function _get_bdCode($id = ''){
        if(!id)return 0;
        $mRe = &m('region');
        $ct = $mRe->get($id);
        if($ct['citycode'] == 0){
            $cts = $mRe->get($ct['parent_id']);
            if($cts['citycode'] != 0){
                return $cts['citycode'];
            }else{
                return 0;
            }
        }
        return $ct['citycode'];
    }


}