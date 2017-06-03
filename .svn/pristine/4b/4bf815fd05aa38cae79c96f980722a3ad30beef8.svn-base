<?php
/**
 * 扫描下载app
 *
 **/
class inviteApp extends MallbaseApp
{
    public function index()
    {
		
		$args = $this->get_params();

		//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
		if( !empty($args)) {
			//处理获得参数
			$store_id = empty($args[0]) ? 0 : intval($args[0]);//创业者id
			$invite   = empty($args[1]) ? 0 : $args[1];//创业着邀请码
		}
        
		$user_mod = & m('member');
        $info = $user_mod->get($store_id);
		if($info['invite'] == $invite) {
			$this->assign('invite',$invite);

		}
		$app_linking = &af('applinking');
		$linking = $app_linking->getAll(); // 载入系统设置数据
		$this->assign('android_mfd_link',$linking['app_link']['android_mfd_link']);
		$this->assign('ios_mfd_link',$linking['app_link']['ios_mfd_link']);
		$this->assign('title', 'APP下载');
        $this->display('inviteapp.html');
    }
    public function reg()
    {
		
		$args = $this->get_params();

		//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
		if( !empty($args)) {
			//处理获得参数
			$store_id = empty($args[0]) ? 0 : intval($args[0]);//创业者id
			$invite   = empty($args[1]) ? 0 : $args[1];//创业着邀请码
		}
        
		$user_mod = & m('member');
        $info = $user_mod->get($store_id);
		if($info['invite'] == $invite) {
			$this->assign('invite',$invite);

		}
		$app_linking = &af('applinking');
		$linking = $app_linking->getAll(); // 载入系统设置数据
		$this->assign('android_share_link',$linking['app_link']['android_share_link']);
		$this->assign('ios_share_link',$linking['app_link']['ios_share_link']);
		$this->assign('title', 'APP下载');
        $this->display('inviteapp2.html');
    }
    public function figure()
    {
    
    	$args = $this->get_params();
    
    	//如果用户没有登录，则视为app嵌套wap，执行验证,及自动登录操作
//     	if( !empty($args)) {
//     		//处理获得参数
//     		$store_id = empty($args[0]) ? 0 : intval($args[0]);//创业者id
//     		$invite   = empty($args[1]) ? 0 : $args[1];//创业着邀请码
//     	}
    
//     	$user_mod = & m('member');
//     	$info = $user_mod->get($store_id);
//     	if($info['invite'] == $invite) {
//     		$this->assign('invite',$invite);
    
//     	}
    	$app_linking = &af('applinking');
    	$linking = $app_linking->getAll(); // 载入系统设置数据
    	$this->assign('android_liangti_link',$linking['app_link']['android_liangti_link']);
    	$this->assign('ios_liangti_link',$linking['app_link']['ios_liangti_link']);
    	$this->assign('title', 'APP下载');
    	$this->display('inviteapp3.html');
    }
}

?>
