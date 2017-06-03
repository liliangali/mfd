<?php
/**
 *
 * 第三方登录
 * @author yusw 2015-04-14
 *
 */
define("CLASS_PATH",ROOT_PATH."/includes/third/qq_api2.0/API/class/");
class QQ_loginApp extends FrontendApp {

	private  $qc;

	function __construct() {
		require_once(CLASS_PATH."QC.class.php");
		$this->qc = new QC();
		parent::__construct();
	}

	/**
	 * QQ登录
	 */
	function index()
	{
        if ($this->visitor->has_login)
        {
            $this->visitor->logout();
        }

		session_start();
        $this->qc->qq_login();
        exit();
	}


	/**
	 *
	 * QQ登录回调
	 */
	function qq_callback()
	{
		session_start();
        $access_token = $this->qc->qq_callback($_GET['state'],$_GET['code']);
        $openid = $this->qc->get_openid();

        //过滤调取失败情况
        if(!$access_token || !$openid){
            $this->show_message('login_failed','back_before_register','member-register.html');
        }


        $qc = new QC($access_token,$openid);
        $arr = $qc->get_user_info();
//        echo '用户信息';var_dump($arr);die;

        $third_name='qq';
        $nick = ($arr["nickname"])?$third_name.'_'.$arr["nickname"]:'';
        $user_name = $third_name.'_'.$openid;


		//是否已存在该用户
		$third_mod=&m("third_login");
		$where=" user_name='$user_name' and third_name='$third_name'";
		$user = $third_mod->find (
			 array (
			 'conditions' => $where,
			 'limit' => 1)
		 );


		if($user)
		{
			$user=current($user);

			$data=array(
			'token'=>$access_token,
			'openid'=>$openid,
			'update_time'=>gmtime(),
			);

			$third_mod->edit("id=".$user["id"],$data);


	        $this->_do_login($user["member_id"]);
	        $this->show_message('login_successed',
	            'enter_member_center', '/index.php?app=member',
	            'apply_store', 'index.php?app=apply'
	        );
		}
		else
		{
            $nickname = $nick.'_'.$this->getCode(5);
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
                'openid'=>$openid,
                'user_id'=>'',
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