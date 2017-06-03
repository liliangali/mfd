<?php

/**
 *    前台控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class FrontendApp extends ECBaseApp
{
    var $man   = 128493; //男装分类，暂时写死
    var $woman = 128494;
    function __construct()
    {
       $this->FrontendApp();
        $this->_source_from = (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) ? 'app' : 'pc';
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
        # 在运行action之前，无法访问到visitor对象

        /***************** 城市切换 ***********************/
        $city_list = $this->get_site_city();

        $cityCode   = $_COOKIE['cityCode'];
        $cityName   = $_COOKIE['cityName'];

        include_once(ROOT_PATH . '/includes/libraries/ipCity.class.php');
        $city = new ipCity();

        if(!$cityCode || !$cityName){
            //读取默认城市
            $ip = real_ip();
            if($ip == "127.0.0.1"){
                $ip = "218.58.54.237";
            }
            //$ip = "218.58.54.237";
            $addr = $city->getCity($ip);
            $cityCode = $addr['content']['address_detail']['city_code'];
            $cityName = $addr['content']['address_detail']['city'];
            setcookie("cityCode", $cityCode, time()+24*3600*30);
            setcookie("cityName", $cityName, time()+24*3600*30);
        }

        if($this->visitor->has_login){
//            setcookie("hasLogin", 1);
            setcookie("hasLogin", 1,0,'/','www.myfoodiepet.com');
        }else{
//            setcookie("hasLogin", 0);
            setcookie("hasLogin", 0,0,'/','www.myfoodiepet.com');
        }

        $this->assign('top_city_list', $city_list);

        $this->assign('current_city', $cityName);

        //===== 分类 =====
        $customs_mod = m('customs');
        $cate = $customs_mod->getTopCate();
        $this->assign('cate',$cate);
        $this->assign('sex_cate',$customs_mod->getSexCate());

        $store_mod = m('store');
        $tailor_list = $store_mod->find(array(
        		'conditions' => 'state=1',
        		'limit' => '10',
        ));

        $this->assign('tailor_data',$tailor_list);

    }
    function _config_view()
    {
        parent::_config_view();
        $this->_view->template_dir  = ROOT_PATH . '/view/'.LANG.'/';
        $this->_view->compile_dir   = ROOT_PATH . '/temp/compiled/mall';
        $this->_view->res_base      = SITE_URL . '/view/'.LANG.'/';
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

        $dis = m('jpjz_dissertation');
        $mData = $dis->find(array(
            "conditions" => "(cat_id='{$this->man}' OR cat_id='{$this->woman}')  AND is_show = 1 AND title<>'全球首发' AND title<>'快捷下单'",
            'order'      => "sort_order ASC",
        ));
        $this->assign("mData", $mData);
        
        //$this->assign('cart_goods_kinds', $cart->get_kinds(SESS_ID, $this->visitor->get('user_id')));
        $cartGoodsNum = 0;

        if($this->visitor->has_login){
//            print_exit( $this->visitor->get('user_id'));
             $cartGoodsNum = is_object($cart) && is_object($this->visitor) ? $cart->get_kinds( $this->visitor->get('user_id'),"pc") : 0;
        }
//        print_exit($cartGoodsNum);
        $this->assign('cart_goods_num', $cartGoodsNum);

		/* 新消息 */
        //$this->assign('new_message', isset($this->visitor) ? $this->_get_new_message() : '');
        //获取首页导航位
        $navigates=$this->getNavigates();
        $this->assign('navigates',$navigates);
        $this->assign("msgCount", $msgCount);
        $this->assign("msgTypes", $msgTypes);
        $this->assign('currentApp', APP);
        $this->assign("currentAct" , ACT);
        //$this->assign('navs', $this->_get_navs());  // 自定义导航
		$this->assign('helps', $this->_get_helps());  // 帮助中心
		//$this->assign('cate', $this->_get_goodscats1());  // 顶部导航1级
		//$this->assign('arr0', $this->_get_goodscats2());  // 顶部导航2级
        $this->assign('acc_help', ACC_HELP);        // 帮助中心分类code
		$gcategorys = $this->_list_gcategorys();
		$this->assign('gcategorys', $this->_list_gcategorys());  // 所有商品分类

        $this->assign('site_title', Conf::get('site_title'));
        $this->assign('site_logo', Conf::get('site_logo'));
        $this->assign('statistics_code', Conf::get('statistics_code')); // 统计代码
        $current_url = explode('/', $_SERVER['REQUEST_URI']);
        $count = count($current_url);
        $this->assign('current_url',  $count > 1 ? $current_url[$count-1] : $_SERVER['REQUEST_URI']);// 用于设置导航状态(以后可能会有问题)
		//$this->assign('hot_keywords', $this->_get_hot_keywords()); //热门搜索 by ancpp

        /***************** 城市切换 ***********************/
//         $site = $this->get_site_city();

//         $this->assign('city_list', $site['city']);

//         $this->assign('shops', json_encode($site['shop']));

		//智齿客服用户参数传输
        if(isset($_SESSION['user_info'])){
        	$this->assign('zc_userid',$_SESSION['user_info']['user_id']);
        	$this->assign('zc_uname',$_SESSION['user_info']['nickname']);
        	$this->assign('zc_tel',$_SESSION['user_info']['phone_mob']);
        	$this->assign('zc_email',$_SESSION['user_info']['email']);
            $this->assign('avatar',$_SESSION['user_info']['avatar']);
           
        }
        /* 平台来源 默认pc add by 小五 20151109 */
        $p_source = 'pc';
        #排除ipad 访问跳转到wap站
        if (is_mobile_request()){
        	$p_source = 'mobile';
        }elseif(preg_match('/(ipad|ipod)/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
        	$p_source = 'ipad';
        }
        
        $this->assign('p_source', $p_source);
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

        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');

            return;
        }
        if (!IS_POST)
        {
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

            /* 防止登录成功后跳转到登录、退出的页面 */
            $ret_url = strtolower($ret_url);
            if (str_replace(array('-login', '-logout','-register','-find_password'), '', $ret_url) != $ret_url)
            {
                $ret_url = SITE_URL . '/member-index.html';
            }


            $this->assign('ret_url', $ret_url);
            $this->_curlocal(LANG::get('user_login'));
            $this->_config_seo('title','欢迎登录麦富迪');
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
        	/* add xiao5 压力测试屏蔽登录验证码 */
           /*  if ($_SESSION['logincode'] != strtolower($_POST['captcha']))
            {
                $this->json_error('captcha_failed');

                return;
            }
            unset($_SESSION['logincode']);
 */
            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];

            $ms =& ms();

            $user_id = $ms->user->auth($user_name, $password,true);  //验证用户
//            var_dump($user_id);die;
            if (!$user_id)
            {

                /* 未通过验证，提示错误信息 */
                $this->json_error($ms->user->get_error());

                return;
            }
            else
            {
            	$user_mod =& m('member');
            	$info = $user_mod->get_info($user_id);

            	//=====  add by liang.li Beg 判断用户是否存在token  不存在则要添加token  =====
            	if (!$info['user_token'])
            	{
            	    $user_token = md5($user_name.$password);
            	    $user_mod->edit($user_id, "user_token = '$user_token'");
            	}
            	//=====  liang.li End  =====

				/*add by yusw*/
                if($info['serve_type'] !=1){
                    $this->json_error('该账号不存在，有疑问请拨打 （客服电话）');
                    return;
                }



            	/* add by xiao5 账户后台冻结 */
            	if ($info['state_info'] == 2)
            	{
            		$this->json_error('该账号因违规，已被冻结，有疑问请拨打 （客服电话）');
            		return;
            	}

                /* 通过验证，执行登录操作 */
                $this->_do_login($user_id);
                // webchat
                setcookie("wkey", encrypt( $user_id .'|||'. md5($password), 'E', 'gaofei' ) , time()+3600*24*14 , '/');

                /* 同步登录外部系统 */
                $synlogin = $ms->user->synlogin($user_id);

                $_SESSION['user_info']['member_lv_id']=$info['member_lv_id'];
                $_SESSION['user_info']['phone_mob']=$info['phone_mob'];
                $_SESSION['user_info']['email']=$info['email'];

                /* 记住密码（两星期内有效） */
                if ($_POST['recall'] && $_POST['recall'] == 'checked')
                {
                    //如果用户选择了，记录登录状态就把用户名和加了密的密码放到cookie里面
                    setcookie("username", $user_name, time()+3600*24*14);
                    setcookie("password", $password, time()+3600*24*14);
                }


                $ip = get_real_ip();
                //for pc
                $client = getBrowser();
                $memberlogs = m('memberlogs');
                $logs  =array(
                    "user_id"=>$user_id,
                    "type"=>1,
                    "ip"=>$ip,
                    "source"=>1,
                    "client"=>empty($client)?'':$client[0].'/'.$client[1],
                    "add_time"=>time()
                );
                $memberlogs->add($logs);


                $ret_url =$_POST['ret_url'] !=''?$_POST['ret_url']:'/member-index.html';
                $this->json_result($ret_url,'登录成功跳转');
            }


//             $this->show_message(Lang::get('login_successed') . $synlogin,
//                 'back_before_login', rawurldecode($_POST['ret_url']),
//                 'enter_member_center', 'index.php?app=member'
//             );
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
            'fields'        => 'user_id, user_name, reg_time, last_login, last_ip, store_id, def_addr,nickname,user_token,avatar',
        ));

        $t = time() +15 * 24 * 60 * 60;
//         setcookie('uname', $user_info['user_name'], $t);

//        $db = &db();
//        $sql = "select * from ".DB_PREFIX."member_lv where member_lv_id = '{$user_info['member_lv_id']}'";
//        $level = $db->getRow($sql);
//        $user_info['level'] = $level;

        /* 店铺ID */
        $my_store = empty($user_info['store_id']) ? 0 : $user_info['store_id'];


        /* 保证基础数据整洁 */
        //unset($user_info['store_id']);
//		$user_info['avatar'] = getAvatarByFile($user_info['avatar']);
//
//		$sql = "select count(*) as total from ".DB_PREFIX."coupon_sn where uid = {$user_info['user_id']}";
//		$coupon_sn = $db->getRow($sql);
//		$user_info['coupon'] = $coupon_sn['total'];
        /* 分派身份 */
        $this->visitor->assign($user_info);

        /* 更新用户登录信息 */
        $mod_user->edit("user_id = '{$user_id}'", "last_login = '" . gmtime()  . "', last_ip = '" . real_ip() . "', logins = logins + 1");

        /* 更新购物车中的数据 */
        $mod_cart =& m('cart');
//         $mod_cart->edit("user_id = '{$user_id}' OR session_id = '" . SESS_ID . "'", array(
//             'user_id'    => $user_id,
//             'session_id' => SESS_ID,
//         ));

        /* 去掉重复的项 */
        $cart_items = $mod_cart->find(array(
            'conditions'    => "user_id='{$user_id}'",
            'fields'        => 'rec_id',
        ));
        if (!empty($cart_items))
        {
            foreach ($cart_items as $rec_id => $cart_item)
            {
                if ($cart_item['spec_count'] > 1)
                {
                    $mod_cart->drop("user_id='{$user_id}' AND rec_id <> {$cart_item['rec_id']}");
                }
            }
        }
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
		return $res;
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
    function _format_page(&$page, $num = 7 ,$pam=array(),$ajax_act ='',$parameter='')
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


    	$link =$ajax_act !=''? array('app' => APP, 'act' => $ajax_act):array('app' => APP, 'act' => ACT);

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
    			$page['page_links'][$i] = $this->_view->build_url($link).$parameter;
    	}


    	if (($page['curr_page'] - $from) < ($page['curr_page'] -1) && $page['page_count'] > $num)
    	{		$link['arg'.$argkey] = 1;
		    	$page['first_link'] = $this->_view->build_url($link).$parameter;

		    	if (($page['curr_page'] -1) - ($page['curr_page'] - $from) != 1)
			    			{
		    		$page['first_suspen'] = '..';
		    	 }
    	}

    	if (($to - $page['curr_page']) < ($page['page_count'] - $page['curr_page']) && $page['page_count'] > $num)
    	{
    		$link['arg'.$argkey] = $page['page_count'];
    		$page['last_link'] = $this->_view->build_url($link).$parameter;
	    	if (($page['page_count'] - $page['curr_page']) - ($to - $page['curr_page']) != 1)
	    	{
	    		$page['last_suspen'] = '..';
	    	}
    	}

    	if($page['curr_page'] > $from)
    	{
	    	$link['arg'.$argkey] = $page['curr_page'] - 1;
	    	$page['prev_link'] = $this->_view->build_url($link).$parameter;
   	    }
	    else
	    {
		    $page['prev_link'] = '';
	    }

	    if($page['curr_page'] < $to)
	    {
	    	$link['arg'.$argkey] = $page['curr_page'] + 1;
	    	$page['next_link'] = $this->_view->build_url($link).$parameter;
	    }
	    else
	    {
    			$page['next_link'] = '';
    	}
   }
	/**
	*-----------------------------------------------------------
	*获取头部导航栏
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月21日
	*@version 1.0
	*@return 
	*/
   function getNavigates(){
   	$_satnav_mod=&m('satnav');
   	$navigates=$_satnav_mod->find(array(
   			'conditions'=>'if_show=1',
   			'order'=>'sort_order ASC,satnav_id ASC'
   	));
   	$temp_nav=$navigates;
//    	$preg_domain = "[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+.?";
   	if ($navigates){
   		foreach ($navigates as $key=>$val){
   			if(isset($val['parent_id']) && $val['parent_id']){
   				unset($navigates[$key]);
   				continue;
   			}
   			$link=$val['link'];
   			//    		if(!preg_match("\b([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}\b",$link)){
   			if(strstr($val['link'], APP)){
   				$navigates[$key]['curr']=1;
   				$navigates['curr']=1;
   			}
   			//在该导航下的子页面中，导航栏没有标示该导航，子页面没有和导航栏使用相同的控制器，只能添加判断处理
   			if(APP=='activegoods' && strstr($val['link'], 'activepublic')){
   				$navigates[$key]['curr']=1;
   				$navigates['curr']=1;
   			}
   			//    		}
   			foreach ($temp_nav as $k=>$v){
   				if($val['satnav_id']==$v['parent_id']){
   					$navigates[$key]['children'][]=$v;
   				}
   			}
   		}
   	}
   
   	return $navigates;
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

    var $size = array(
        "0003" => array(
            'name' => "西装",
            'file' => "/cron/size_json/0003.size.json",
        ),
        "0004" => array(
            'name' => "西裤",
            'file' => "/cron/size_json/0004.size.json",
        ),
        "0005" => array(
            'name' => "马甲",
            'file' =>"/cron/size_json/0005.size.json",
        ),
        "0006" => array(
            'name' => "衬衣",
            'file' => "/cron/size_json/0006.size.json",
        ),
        "0007" => array(
            'name' => "大衣",
            'file' => "/cron/size_json/0007.size.json",
        ),
    );
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

        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    /* 取得支付方式实例 */
    function _get_payment($code, $payment_info)
    {
        include_once(ROOT_PATH . '/includes/payment.base.php');
        include(ROOT_PATH . '/includes/payments/' . $code . '/' . $code . '.payment.php');
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
 * 购物流程
 * 
 * @date 2015-8-21 上午9:09:07
 * @author Ruesin
 */
class ShoppingbaseApp extends FrontendApp
{
	var $_mod_cart;
	public $_user_id;
	public $_measure_way = array(/*'1'=>'预约上门量体',*/'2'=>'去附近门店量体','5'=>'现有量体数据','6'=>'指定量体师',/* '3'=>'现有量体数据' *//*,'4'=>'标准尺码'*/);//标准尺码没算法  先屏蔽
	public $_invoice_com = array(1=>'普通发票',3=>'增值税专用',2=>'增值税普通');
	public $_invoice_need = array(0=>'不需要',1=>'需要');
	public $_invoice_type = array(1=>'普通发票',3=>'增值税专用',2=>'增值税普通'); 
	public $_invoice_title = '';
	public $_params_type = 'get';
    var $_source_from;
	
	function __construct()
	{
		$this->_mod_cart = &m('cart');
		parent::__construct();
		if($this->visitor->has_login){
		    $this->_user_id = $this->visitor->get('user_id');
		}
        //token做识别

	}

    /**
     * 购物流程权限控制
     * 
     * @date 2015-9-16 下午4:21:28
     * @author Ruesin
     */
    function _run_action()
    {
        $postActs = array('create');//POST列表,非POST不可访问
        if(!IS_POST && in_array(ACT, $postActs)){
            return ;
        }
        $guestActs = array('get_order_review');//游客允许列表
        if (!$this->visitor->has_login && !in_array(ACT, $guestActs)){
            $view = &v();
            $url = $view->build_url(array("app" => "member","act" => "login" ));
            if (!IS_AJAX){
                header('Location:'.$url.'?ret_url=' . rawurlencode($view->build_url(array("app" => "cart" ))));
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
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    /**
     * 购物车数据
     *
     * @author Ruesin
     */
    function _cart_main($fmt = 0){
    
        $goods =  $this->_mod_cart->_cart_main($this->_user_id,$this->cartCookieGet('check'));
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
//       $goods =  $this->_mod_cart->_cart_check($this->_user_id,$this->cartCookieGet('check'));
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
     * 获取品类集合
     * 
     * @date 2015-10-20 下午3:58:57
     * @author Ruesin
     */
    function _cart_cloth($isCheck = 0,$fields = array()){
        
        $condition = " user_id = '{$this->_user_id}'";
        
        if($isCheck){
            $condition .= " AND ".db_create_in($_SESSION['_cart']['check'],"ident");
        }else{
            $condition = '';
        }
        $data = $this->_mod_cart->find(array(
                'conditions' => $condition,
                'fields' => 'ident,cloth' . (isset($fields) ? ','.(implode(',', $fields)) : ''),
                'index_key' => 'ident',
        ));
        if ($fields)
            return $data;
        foreach ($data as $val){
            $return[$val['cloth']] = $val['cloth'];
        }
        return $return;
    }

    /**
     * 初始化订单信息
     *
     * @author Ruesin
     */
    function _order_info(){

        $order = isset($_SESSION['_order']) ? $_SESSION['_order'] : array();

        /* 预约量体信息 */
        if($order['measure']){
            $order['measure']['data'][$order['measure']['type']]['type_name'] = $this->_measure_way[$order['measure']['type']];
        }
// print_exit($order);
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
//  print_exit($order);

        //$def = 36;
        $region_id = $order["shipping"]["address"]["region_id"];
        if ($region_id)
        {
            $region_id_arr = explode(",", $region_id);
            if ($region_id_arr[1] == 246)
            {
              //  $def = 36;
            }
        }

// print_exit($order) ;
        /* 默认物流公司 */
//     	if(!$order['ships']){

        imports("orders.lib");
        $orderLib = new Orders();
        $shipInit = $orderLib->getShipInit(['ship_area_id'=>$order['shipping']['address']['region_id'],'weight'=>''],$def);
        $_SESSION['_order']['ships']['defship'] = $shipInit['defship'];
//     	}
// print_exit$shipInit();
        /* 可用减免 */
//     	if($order['pmt']['coin'] && intval($order['pmt']['coin'])>0){
//     	    unset($order['debit']);
//     	}

        /* 优惠券用户等级限制 */
//     	if ($this->visitor->get('member_lv_id') >1 ){
//     	    unset($order['debit']);
//     	}
        //优惠价格
        if (isset($order['debit']['data'])){
            foreach ((array)$order['debit']['data'] as $row){
                $order['discount'] += $row['money'];
            }
            //         discount
        }

        /* 支付及配送 */
//     	if(!$order['pay']){
//     		$order['pay']['type']  = '1';
//     	}
//     	if(!$order['ship']){
//     	    $order['ship']['type'] = '1';
//     	    //$order['shippay']['data'] = array();
//     	}

        /*发票*/
        if(!$order['invoice']){
            $order['invoice'] = array();
        }
// print_exit($order);
        return $order;
    }
    
    /**
     * 支付列表(pc端剔除wxpay)
     */
    function payments(){
        $mPayment = &m("payment");
    	return $mPayment->find(array(
    				'conditions' => "enabled=1 AND ismobile=0 and payment_code != 'wxpay'",
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

    protected function getServeRegion(){
        $region_mod = m('region');
        $region_list = $region_mod->find("parent_id > 2 AND is_serve = 1"); //这里有问题  直辖市下的服务点定位在区里的 如果下拉只出最后一个，那么会只出个区的
        $zxs = array(3=>'北京市',22=>'天津市',41=>'上海市',61=>'重庆市');
        foreach ($region_list as &$row){
            if(isset($zxs[$row['parent_id']])){
                $row['region_name'] = $zxs[$row['parent_id']] .' '.$row['region_name'];
            }
        }
        return $region_list;
    }
    
    protected function getParams(){
        if ($this->_params_type == 'get'){
            return $_GET;
        }
        return $_POST;
    }
    
    //===========先写在这里  稍后封装类
    public function cartChoiceSet($data){
        
    }
    public function cartCookieSet($key,$val){
        
        $cart = md5($this->_user_id.'_cart');
        
        $data = $this->cartCookieGet();
        
        $data[$key] = $val;
// print_exit($cart);
        setcookie($cart, serialize($data), time()+86400);
    }
    
    public function cartCookieGet($key = ''){
        
        $cart = md5($this->_user_id.'_cart');
        
        $data = $_COOKIE[$cart];
        
        $return = $data ? unserialize(stripslashes_deep($data)) : array();
        
        if ($key)
            return isset($return[$key]) ? $return[$key] : array();
        
        return $return;
        
    }
    
}



class PaycenterbaseApp extends MallbaseApp
{
	function __construct()
	{
		parent::__construct();

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
        if (!$this->visitor->has_login &&
                !in_array(ACT, array(
                    'login', 'register', 'check_user','eValidation','ActivationEail',
                    'mbregister','verifycode','check_account','check_verifycode',
                    'findpsSMSCode','findpsEmail','findpsRestSMSCode','findpsResetEmail','upload',
                    'resetSMSCode','find_password','set_password','register_confirm','customer'))
        ){

         if (!IS_AJAX)
         {
                //ns add
                $link = array("app" => "member","act" => "login" );
                $_view = &v();
                $url = $_view->build_url($link);
                header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['REQUEST_URI'] . '?' . $_SERVER['QUERY_STRING']));
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
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/user";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
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
        //个人中心        
        $menu['info'] = array(
            'name'  => 'my_userInfo',
            'text'  => '资料管理',
            'submenu' => array(
                // 我的个人中心
                'my_profile'  => array(
                    'text'  => '个人资料',
                    'url'   => 'member-profile.html',
                    'name'  => 'my_profile',
                ),
                //地址管理
                'my_address'  => array(
                    'text'  => '地址管理',
                    'url'   => 'my_address.html',
                    'name'  => 'my_address',
                ),
                //量体数据
                'my_body' => array(
                    'text'  => '量体数据',
                    'url'   => 'my_body.html',
                    'name'  => 'my_body',
                ),
            		//我的顾客
            		'customer'  => array(
            				'text'  => '我的顾客',
            				'url'   => 'customer.html',
            				'name'  => 'customer',
            		),
                // 我的消息
                // 'newpm'  => array(
                //     'text'  => lang::get('message'),
                //     'url'   => 'message-newpm.html',
                //     'name'  => 'message',
                // ),
                // 密码修改
                'my_password'  => array(
                    'text'  => Lang::get('my_password'),
                    'url'   => 'member-password.html',
                    'name'  => 'my_password',
                ),

            ),
        );

        
        //交易管理
        $menu['im_buyer'] = array(
            'name'  => 'im_buyer',
            //'text'  => Lang::get('im_buyer'),
            'text' => '交易管理',
            'submenu' => array(
                 // 我的订单
                'my_order'  => array(
                    'text'  => Lang::get('my_order'),
                    'url'   => 'buyer_order.html',
                    'name'  => 'my_order',
                    'icon'  => 'ico5',
                ),
                //我的评论
                'my_detail' => array(
                    'text' => '我的评论',
                    'url' => 'my_detail.html',
                    'name' => 'my_detail',
                ),
                // 我的收藏
                'my_favorite'  => array(
                    'text'  => '我的收藏',
                    'url'   => 'my_favorite.html',
                    'name'  => 'my_favorite',               
                ),
                //发布需求
                'demand_details' =>array(
                    'text' => '发布需求',
                    'url' => 'demand_details-sue.html',
                    'name' => 'demand_details',
                ),
                //我的需求
               'my_demand'  => array(
                    'text'  => Lang::get('my_demand'),
                    'url'   => 'my_demand.html',
                    'name'  => 'my_demand',
                ),
             
                //我的晒单
                // 'my_sunsingle'  => array(
                //         'text'  => lang::get('my_sunsingle'),
                //         'url'   => 'my_single.html',
                //         'name'  => 'my_sunsingle',
                // ),

            ),
        );

        // 账户管理
        //     量体数据
        //     个人资料
        //     我的消息
        //     密码修改 

        //个人中心
        // $menu['info'] = array(
        //     'name'  => 'my_userInfo',
        //     'text'  => Lang::get('my_userInfo'),
        //     'submenu' => array(
        //     //会员概括
        //     // '1'  => array(
        //     // 'text'  => Lang::get('overview'),
        //     // 'url'   => 'member.html',
        //     // 'name'  => 'overview',
        //     // ),
        //     //个人资料
        //     '2'  => array(
        //     'text'  => Lang::get('my_profile'),
        //     'url'   => 'member-profile.html',
        //     'name'  => 'my_profile',
        //     ),
        //     //我的订单
        //     'my_order'  => array(
        //             'text'  => Lang::get('my_order'),
        //             'url'   => 'buyer_order.html',
        //             'name'  => 'my_order',
        //             'icon'  => 'ico5',
        //         ),
        //     //个人收藏
        //     'my_favorite'  => array(
        //             'text'  => lang::get('my_favorite'),
        //             'url'   => 'my_favorite.html',
        //             'name'  => 'my_favorite',
        //     ),
        //     //量体信息
        //     'my_body' => array(
        //             'text'  => lang::get('my_body'),
        //             'url'   => 'my_body.html',
        //             'name'  => 'my_body',
        //     ),

        //     //配送地址
        //     'my_address'  => array(
        //             'text'  => lang::get('my_address'),
        //             'url'   => 'my_address.html',
        //             'name'  => 'address',
        //     ),
        //     //会员政策
        //     'my_vip'=> array(
        //             'text'  => lang::get('my_vip'),
        //             'url'   => 'my_vip.html',
        //             'name'  => 'my_vip',
        //     ),


        //     //站内消息
        //     // '3'  => array(
        //     // 'text'  => lang::get('message'),
        //     // 'url'   => 'message-newpm.html',
        //     // 'name'  => 'newpm',
        //     // ),
        //     // '4'  => array(
        //     //      'text'  => '安全设置',
        //     //      'url'   => 'kuke-safeset.html',
        //     //      'name'  => 'safeset',
        //     // ),

        //     ),
        // );
        /* 交易管理 */
        // $menu['im_buyer'] = array(
        //     'name'  => 'im_buyer',
        //     'text'  => Lang::get('im_buyer'),
        //     'submenu'   => array(
        //         // 'my_order'  => array(
        //         //     'text'  => Lang::get('my_order'),
        //         //     'url'   => 'buyer_order.html',
        //         //     'name'  => 'my_order',
        //         //     'icon'  => 'ico5',
        //         // ),
        //         'my_favorite'  => array(
        //             'text'  => lang::get('my_favorite'),
        //             'url'   => 'my_favorite.html',
        //             'name'  => 'favorite',
        //         ),
        //         'my_address'  => array(
        //             'text'  => lang::get('my_address'),
        //             'url'   => 'my_address.html',
        //             'name'  => 'address',
        //         ),
        //     ),
        // );

        //账户管理

        
        if (!$this->visitor->get('has_store') && Conf::get('store_allow'))
        {
            $menu['overview'] = array(
                'text' => Lang::get('apply_store'),
                'url'  => 'index.php?app=apply',
            );
        }

        // if ($this->visitor->get('manage_store'))
        // {
        //     //ns up 手机信息
        //     /* 指定了要管理的店铺 */
        //     $menu['im_seller'] = array(
        //         'name'  => 'im_seller',
        //         'text'  => Lang::get('im_seller'),
        //         'submenu'   => array(),
        //     );
        //     //产品管理
        //     $menu['im_seller']['submenu']['my_goods'] = array(
        //             'text'  => Lang::get('my_goods'),
        //             'url'   => $_view->build_url(array("app" => "my_goods")),
        //             'name'  => 'my_goods',
        //             'icon'  => 'ico8',
        //     );
        //     //分类管理
        //     $menu['im_seller']['submenu']['my_category'] = array(
        //             'text'  => Lang::get('my_category'),
        //             'url'   => $_view->build_url(array("app" => "my_category")),
        //             'name'  => 'my_category',
        //             'icon'  => 'ico9',
        //     );
        //     //订单管理
        //     // $menu['im_seller']['submenu']['order_manage'] = array(
        //     //         'text'  => Lang::get('order_manage'),
        //     //         'url'   => $_view->build_url(array("app" => "seller_order")),
        //     //         'name'  => 'order_manage',
        //     //         'icon'  => 'ico10',
        //     // );
        //     //店铺设置
        //     $menu['im_seller']['submenu']['my_store']  = array(
        //             'text'  => Lang::get('my_store'),
        //             'url'   => $_view->build_url(array("app" => "my_store")),
        //             'name'  => 'my_store',
        //             'icon'  => 'ico11',
        //     );
        //     //主题设置
        //     $menu['im_seller']['submenu']['my_theme']  = array(
        //             'text'  => Lang::get('my_theme'),
        //             'url'   => $_view->build_url(array("app" => "my_theme")),
        //             'name'  => 'my_theme',
        //             'icon'  => 'ico12',
        //     );
        // }

        return $menu;
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
            $this->show_warning('您不是普通会员，不能发布需求哦！');
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
