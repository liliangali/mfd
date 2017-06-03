<?php
class AjaxApp extends MallbaseApp{

	function __construct(){
		parent::__construct();
	}

	/**
	 * 注册用户重新发送邮件
	 * @return void
	 * @version 1.0.0 (2014-12-12)
	 * @author Xiao5
	 */
	function resetEmail(){
	    $email = $_REQUEST['email'];
	    if(!$email)
	        outinfo('邮箱为空',1);

        $rs = emailcode($email,'reg','reg' , 'ajax' , 'reset' , "");
        if ($rs['err'])
        {
            $this->json_error($rs['msg']);
        }
        $this->json_result($rs['msg']);
	}


    /**
	 * 绑定邮箱发送邮件
	 * @return void
	 * @version 1.0.0 (2014-12-12)
	 * @author Xiao5
	 */
	function editEmail(){
		$email = $_REQUEST['email'];
		if(!$email)
			outinfo('邮箱为空',1);

		$rs = emailcode($email,'editEmail','editEmail' , 'ajax' , 'reset' , "");
		if ($rs['err'])
		{
			$this->json_error($rs['msg']);
		}
		$this->json_result($rs['msg']);
	}


	/**
	 * 生成图像验证码
	 * @return void
	 * @version 1.0.0 (2014-12-12)
	 * @author Xiao5
	 */
	function getAuthCode(){

	    $args = $this->get_params();
	    $key = empty($args[0]) ? 'auth_code' : $args[0];
	    include 'includes/libraries/imageAuthCode.lib.php';
	    $img= new ImageAuthCode();
	    $img->showImg();
	    $_SESSION[$key] = strtolower($img->code);
	}

	/**
	 * 注册用户重新发送手机验证码
	 * @return void
	 * @version 1.0.0 (2014-12-12)
	 * @author Xiao5
	 */
	function resetSMSCode(){
	    $category = $_REQUEST['category'];
	    $type = $_REQUEST['type'];
	    $phone = $_REQUEST['phone'];
	    $opt=isset($_REQUEST['opt'])?$_REQUEST['opt']:'reset';
	    $rs = smsAuthCode($phone,$category, $type , $opt,'ajax' );
	    if ( is_array($rs) && $rs['err'])
	    {
            return outinfo($rs['msg'],1);
	    }
        return outinfo($rs['msg'],0);
	}

    /*注册发送验证码*/
    function regSMSCode(){
        $rs = smsAuthCode($_POST['phone'],'reg','reg','get','pc');
        if ($rs['err'])
        {
            $this->json_error($rs['msg']);
        }
        $this->json_result($rs['msg']);
    }



    /*发送短信验证码*/
    public function sendCode()
    {

        $phoneNum = $_REQUEST['phone'];
        $type = isset($_REQUEST['type'])?$_REQUEST['type']:'reg';
        $code = rand(1000, 9999);


        if($type == 'reg') {// 判断此用户是否已注册
            $m     = m('member');
            $member = $m->get("(user_name = $phoneNum or phone_mob=$phoneNum) and serve_type=1");

            if(!empty($member)) {
                $this->json_error('当前手机号码已经被占用，不可重复注册');
                return ;
            }

        }
        $timeout= 60;
        $rs = SendSms($phoneNum, '验证码为' . $code, $type, $code, $timeout);
        if ($rs) {
            $this->json_result('发送成功');
        } else {
            $this->json_error('发送失败');
        }

    }


	/**
	* 绑定手机号码发送短信验证码
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-18
	*/
	function bingMobileCode()
	{
		$category = $_REQUEST['category'];
	    $type = $_REQUEST['type'];
	    $opt = $_REQUEST['opt'];
	    $phone = $_REQUEST['phone'];


	    $rs = smsAuthCode($phone,$category, $type , 'reset','ajax' );
	    if ($rs['err'])
	    {
	        $this->json_error($rs['msg']);
	    }
	    $this->json_result($rs['msg']);
	}
	/**
	 * 发送手机短信验证码之前 验证用户是否存在
	 * @return void
	 * @version 1.0.0 (2015-1-6)
	 * @author tangsj
	 */
	function resetCode(){

	    $phone = isset($_REQUEST['phone'])?$_REQUEST['phone']:'';

        if(!$phone){
            return outinfo("手机号不能为空",1);
        }
	    $ms =& ms();
	    if ($ms->user->check_username($phone))
	    {
            return outinfo("该手机号并未绑定任何帐号",1);
	    }else
	    {
	        $this->resetSMSCode();
	    }


	}
	/**
	 * 注册协议
	 */
	function provision(){
        $this->_article_mod = &m('article');
        $find_data     = $this->_article_mod->get("code='member_register' and if_show='1'");
		$this->assign('content',$find_data['content']);
        $this->display('user/provision.html');
	}
	
	
	/**
	 * 关于麦富迪币
	 */
	function coin(){
		$this->_article_mod = &m('article');
		$find_data     = $this->_article_mod->get("code='about_coin' and if_show='1'");
		$this->assign('content',$find_data['content']);
		$this->display('user/provision.html');
	}
	function msg($msg){
	    //         echo $msg;
	    $this->json_error($msg);
	    exit();
	}

	/**
	 * ajax登录操作
	 * @return void
	 * @version 1.0.0 (2014-12-16)
	 * @author Xiao5
	 */
	function  ajaxLogin(){
	    $ret_url = rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
	    $user_name = trim($_POST['name']);
	    $password  = $_POST['pwd'];
	    $remember = $_POST['memory']; //记住密码
	    $ms =& ms();
	    $user_id = $ms->user->auth($user_name, $password,true);

	    if (!$user_id)
	    {/* 验证失败 */
	        ajaxReturn('-1', Lang::get('login_error'), array());
	    }


	    $mod_user =& m('member');

	    $user_info = $mod_user->get(array(
	        'conditions' => $user_id,
	        'join'       => 'manage_mall',
	        'fields'     => '*'
	    ));

	    /* add by xiao5 账户后台冻结 */
	    if ($user_info['state_info'] == 2)
	    {
	    	ajaxReturn('-1', '该账号因违规，已被冻结，有疑问请拨打 （客服电话）', array());
	    	return;
	    }

	    // if (!$user_info['privs'])
	    // {
	    //     echo ecm_json_encode(array('flag' => '-1'));
	    //     return;
	    // }

	    /* 分派身份 */
	    $this->visitor->assign($user_info);

	    //记住密码
	    if ($remember) {
	        //如果用户选择了，记录登录状态就把用户名和加了密的密码放到cookie里面- 两周
	        setcookie("username", $user_name, time()+3600*24*14);
	        setcookie("password", $password, time()+3600*24*14);
	    }


//	    setcookie("hasLogin", 1);
            setcookie("hasLogin", 1,0,'/','myfoodiepet.com');


	    /* 更新登录信息 */
	    $time = gmtime();
	    $ip   = real_ip();
	    $mod_user->edit($user_id, "last_login = '{$time}', last_ip='{$ip}', logins = logins + 1");

	    /* 更新购物车中的数据 */
	    $mod_cart =& m('cart');
	    $mod_cart->edit("user_id = '{$user_id}' OR session_id = '" . SESS_ID . "'", array(
	        'user_id'    => $user_id,
	        'session_id' => SESS_ID,
	    ));

        if($user_id){
            do{
                $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
        }else{
            $api_token = 'mfd';
        }

	    ajaxReturn(1,$api_token, array('ret_url'=>$ret_url));
	}

	/**
	 * 关注
	 *
	 * 用户表 关注数，粉丝数
	 * user_follow 关注索引表-mutually 1是双方关注 0 是单方
	 *
	 * A-B 关注的逻辑
	 *
	 * 1.自己不能关注自己
	 * 2.已关注不能重复
	 * 3.B是否关注A，如果是將mutually更改为1
	 * 4.A为uid insert索引表
	 * 5.A的关注+1
	 * 6.B的粉丝+1
	 *
	 * @author v5
	 * @return JSON
	 */
	function ajaxFollow(){

	   	$uid = $_GET['uid'];

	   	!$uid && ajaxReturn(0, Lang::get('follow_invalid_user'));


	   	$user = $this->visitor->get();
	   	// 成为品牌商 就不能发起关注
	   	if ($user['has_store'])
	   	{
	   	    //ajaxReturn(0, Lang::get('tailor_can_not_follow'));
	   	}

	   	$uid == $user['user_id'] && ajaxReturn(0, Lang::get('follow_self_not_allow'));

	   	$this->m =& m('member');

	   	$follow_user = $this->m->get($uid);
	   	if (!$follow_user) {
	   	    ajaxReturn(0, Lang::get('follow_invalid_user'));
	   	}

	   	//只能关注品牌商
	   	if ($follow_user['serve_type'] != 1)
	   	{
	   	    ajaxReturn(0, Lang::get('not_tailor'));
	   	}

	   	$user_follow_mod =& m('userfollow');

	   	//已经关注？
	   	$is_follow = $user_follow_mod->get(array('uid'=>$user['user_id'], 'follow_uid'=>$uid));

	   	$is_follow && ajaxReturn(0, Lang::get('user_is_followed'));


	   	//关注动作
	   	$return = 1;
	   	//他是否已经关注我
	   	$map = array('uid'=>$uid, 'follow_uid'=>$user['user_id']);
	   	$isfollow_me = $user_follow_mod->get($map);
	   	$data = array('uid'=>$user['user_id'], 'follow_uid'=>$uid, 'add_time'=>time());
	   	if ($isfollow_me) {
	   	    $data['mutually'] = 1; //互相关注
	   	    $user_follow_mod->edit($map,array('mutually'=>1)); //更新他关注我的记录为互相关注
	   	    $return = 2;
	   	}
	   	if (!$user_follow_mod->add($data)) ajaxReturn(0, Lang::get('follow_user_failed'));

	   	//增加我的关注人数
	   	$this->m->setInc(array('user_id'=>$user['user_id']),'follows');

	   	//增加Ta的粉丝人数
// 	   	$this->m->setInc(array('user_id'=>$uid),'fans');

	   	//增加 品牌商的关注数
	   	$store_mod  =& m('store');
	   	$store_mod->setInc(array('store_id'=>$uid),'fans');
	   	//提醒被关注的人 - 预留接口
	   	//add_tip($uid, 类型);

	   	//把他的微薄推送给我
	   	//TODO...是否有必要？
	   	ajaxReturn(1, Lang::get('follow_user_success'), $return);
	}

	/**
	 * 取消关注
	 *
	 * @author v5
	 * @return JSON
	 */
	public function ajaxUnfollow() {
	   	$uid = $_GET['uid'];

	   	!$uid && ajaxReturn(0,Lang::get('unfollow_invalid_user'));

	   	$user_follow_mod =& m('userfollow');

	   	$this->m =& m('member');
	   	$user = $this->visitor->get();

	   	if ($user_follow_mod->drop('uid='.$user['user_id'].' and follow_uid='.$uid)) {
	   	    //他是否已经关注我
	   	    $map = array('uid'=>$uid, 'follow_uid'=>$user['user_id']);
	   	    $isfollow_me = $user_follow_mod->get($map);
	   	    if ($isfollow_me) {
	   	        $user_follow_mod->edit($map,array('mutually'=>0)); //更新他关注我的记录为互相关注
	   	    }
	   	    //减少我的关注人数
	   	    $this->m->setDec(array('user_id'=>$user['user_id']),'follows');

	   	    //减少品牌商的关注数
	   	    $store_mod  =& m('store');
	   	    $store_mod->setDec(array('store_id'=>$uid),'fans');

	   	    ajaxReturn(1,Lang::get('unfollow_user_success'));

	   	} else {
	   	    ajaxReturn(0,Lang::get('unfollow_user_failed'));
	   	}
	}


    function matchWord(){
        $word = htmlspecialchars(trim($_GET['w']));
        $res = array('his' => array(), 'match' => array());
        if(empty($word)){
            $json = json_encode($res);
            die("displayRes({$json})");
        }

        //***********************************************
        $sh = @unserialize(stripslashes($_COOKIE['sh']));
        $pattern = "/^{$word}/";
        $his = array();
        $tempHis = array();
        foreach((array)$sh as $key => $val){
            if(preg_match($pattern, $val)){
                $tempWord = preg_replace($pattern, '', $val);
                $his[] = array(
                    'name' => $word."<b>".$tempWord."</b>",
                    'key'  => $word.$tempWord
                );

                $tempHis[] = $word.$tempWord;
            }
        }

        //***********************************************

        $ctm_mod =& m('customs');
        $sto_mod =& m('store');
        $dis_mod =& m('dissertation');
        $fab_mod =& m('part');
        $dmd_mod =& m('demand');
        $tempMatch = array();
        $cds = array(
            'sto' => "store_name LIKE '{$word}%' AND state=1",
            'ctm' => "cst_name LIKE '{$word}%' AND is_active=1",
            'dis' => "title LIKE '{$word}%'",
            'fab' => "fabric_id !=0 AND state=1 AND is_on_sale=1 AND part_name LIKE '{$word}%'",
            'dmd' => "md_title LIKE '{$word}%'AND status = 2 AND region_code = '{$_COOKIE['cityCode']}'"
        );

        //------------------------------
        $ctmArr = $ctm_mod->find(array(
            'conditions' => $cds["ctm"],
            'fields' => 'cst_name',
            'limit' => 5,
        ));


        foreach((array)$ctmArr as $key => $val){
            if(!in_array($val['cst_name'], $tempHis)){
                $tempMatch[] = $val['cst_name'];
            }
        }
        //------------------------------

        //------------------------------
        $stoArr = $sto_mod->find(array(
            'conditions' => $cds["sto"],
            'fields' => 'store_name',
            'limit' => 5,
        ));


        foreach((array)$stoArr as $key => $val){
            if(!in_array($val['store_name'], $tempHis)){
                $tempMatch[] = $val['store_name'];
            }
        }
        //------------------------------

        //------------------------------
        $disArr = $dis_mod->find(array(
            'conditions' => $cds["dis"],
            'fields' => 'title',
            'limit' => 5,
        ));


        foreach((array)$disArr as $key => $val){
            if(!in_array($val['title'], $tempHis)){
             $tempMatch[] = $val['title'];
            }
        }
        //------------------------------

        //------------------------------
        $fabArr = $fab_mod->find(array(
            'conditions' => $cds["fab"],
            'fields' => 'part_name',
            'limit' => 5,
        ));


        foreach((array)$fabArr as $key => $val){
            if(!in_array($val['part_name'], $tempHis)){
                $tempMatch[] = $val['part_name'];
            }
        }
        //------------------------------

        //------------------------------
        $dmdArr = $dmd_mod->find(array(
            'conditions' => $cds["dmd"],
            'fields' => 'md_title',
            'limit' => 5,
        ));


        foreach((array)$dmdArr as $key => $val){
            if(!in_array($val['md_title'], $tempHis)){
                $tempMatch[] = $val['md_title'];
            }
        }
        //------------------------------

        //***********************************************

        rsort($tempMatch);

        $tempMatch = array_slice($tempMatch, 0, 5);
        $match = array();
        foreach((array)$tempMatch as $key => $val){
            if(preg_match($pattern, $val)){
                $tempWord = preg_replace($pattern, '', $val);
                $match[] = array(
                    'name' => $word."<b>".$tempWord."</b>",
                    'key'  => $word.$tempWord
                );
            }
        }

        $res['his']   = $his;
        $res['match'] = $match;
        $json = json_encode($res);
        die("displayRes({$json})");
    }
}
?>