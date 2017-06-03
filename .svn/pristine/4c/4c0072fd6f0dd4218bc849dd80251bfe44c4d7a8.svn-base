<?php
/**
 *
 * 第三方登录 sina
 * @author yusw 2015-04-14
 *
 */
define( "WB_CALLBACK_URL" , "http://www.kuteid.com/sina_login-sina_callback.html" );
class Sina_loginApp extends FrontendApp {

    private $sina_app_key='448094528';
    private $sina_app_secret='3302f34a78f516bd49c9315614662ea7';
    private $o;


	function __construct() {
        require_once(ROOT_PATH."/includes/third/sina_api/weibooauth.php");
        $this->o = new SaeTOAuthV2( $this->sina_app_key , $this->sina_app_secret );
        parent::__construct();
	}


    /**
     * sina登录
     */
    function index(){
        if ($this->visitor->has_login)
        {
            $this->visitor->logout();
        }

        session_start();
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION['sina_state'] = $state;  //比完之后清理
        $login_url = $this->o->getAuthorizeURL( WB_CALLBACK_URL,'code',$state);
        header("Location:$login_url");
        exit();
    }

    /**
     * sina回调
     */
	function sina_callback()
	{
        session_start();

        if (!isset($_REQUEST['code']) ||  $_SESSION['sina_state']  != $_REQUEST['state']) {
            $this->show_message('login_failed','back_before_register','member-register.html');
        }
        $keys = array();
        $keys['code'] = $_REQUEST['code'];
        $keys['redirect_uri'] = WB_CALLBACK_URL;
        $token = $this->o->getAccessToken( 'code', $keys ) ;
        unset($_SESSION["sina_state"]);

		$third_name='sina';
        $access_token=$token['access_token'];
		$user_id=$token['uid'];
		$user_name=$third_name.'_'.$user_id;

		//是否已存在该用户
		$third_mod=&m("third_login");
		$where=" user_name='$user_name' and third_name='$third_name'";
		$user = $third_mod->find (array ('conditions' => $where,'limit' => 1) );

		 //存在时
		if($user)
		{
			$user=current($user);
            $data=array(
                'token'=>$access_token,
                'openid'=>$user_id,
                'update_time'=>gmtime(),
            );
			$third_mod->edit("id=".$user["id"],$data);

			 //登录
	        $this->_do_login($user["member_id"]);

	        //跳转
	        $this->show_message('login_successed',
	            'enter_member_center', '/index.php?app=member',
	            'apply_store', 'index.php?app=apply'
	        );
		}
		else
		{

            //注册用户
            $nickname = $user_name.'_'.$this->getCode(5);

            /*会员默认等级*/
            $member_lv_mod =& m('memberlv');
            /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
            $m_lv = $member_lv_mod->get_default_level();

            //连接用户中心
            $ms =& ms();
            $member_id = $ms->user->register($user_name, '','',array('nickname'=>$nickname,'member_lv_id'=>$m_lv['member_lv_id']));

            if (!$member_id)
            {

                $error = current($ms->user->get_error());
                $this->show_warning(Lang::get($error['msg']));
                return;
            }


			//添加到third_login表
            $data=array(
                'third_name'=>$third_name,
                'token'=>$access_token,
                'openid'=>$user_id, //此处统一
                'user_name'=>$user_name,
                'member_id'=>$member_id,
                'add_time'=>gmtime(),
                'update_time'=>gmtime(),
            );
			$third_mod->add($data);

            //登录
            $this->_hook('after_register', array('user_id' => $member_id));
            $this->_do_login($member_id);

	        //跳转
	        $this->show_message('login_successed',
	            'enter_member_center', '/index.php?app=member',
	            'apply_store', 'index.php?app=apply'
	        );

		}
	}

}