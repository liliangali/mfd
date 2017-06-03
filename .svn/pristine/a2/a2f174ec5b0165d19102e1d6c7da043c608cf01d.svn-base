<?php

/*获取客服列表*/
function chatSeverList($user_id) 
{
	$db = connection();
	//获得系统客服列表信息
	$sql_admin_info = "SELECT m.nickname, m.user_id, m.avatar FROM ".DB_PREFIX."member m left join ".DB_PREFIX."user_priv up ON m.user_id = up.user_id where up.store_id = 0";
	$admin_info = $db->getAll($sql_admin_info);

	$online_sort = array();
	//获得当前用户与每个客服的聊天记录（未读消息）
	foreach($admin_info as $key => $info ) {
		
		if(empty($info['user_id']) || $info['user_id'] == $user_id || check_system_manager($info['user_id']) ) {//去空&&剔除超级管理员&&去除当前登录用户同时为管理员情况
			unset($admin_info[$key]);
			continue;
		}
		$admin_info[$key]['online'] = 0;//默认不在线
		//获得管理员头像
		$admin_info[$key]['avatar'] = avatar_show_src($info['user_id'], 'big');
	
		//获得当前客服与当前用户的未读消息记录
		$sql_noread = "SELECT * FROM ".DB_PREFIX."usermessage where is_read = '0' AND to_user = '1' AND to_user_id ={$user_id}  AND from_user_id ={$info['user_id']} ";
		$noread_list = $db->getAll($sql_noread);
		$noread_count = count($noread_list);//未读数
			
		//获得当前客服是否在线
		$sql_online = "SELECT * FROM ".DB_PREFIX."online where user_id = {$info['user_id']}";
		$online = $db->getRow($sql_online);
		
		$online_sort[$key] = 0;
		if($online) {
			$admin_info[$key]['online'] = 1;
		}
		
		$admin_info[$key]['noread_count'] = $noread_count;
		$admin_info[$key]['noread_list']  = $noread_list;
		
		$no_count = $noread_count-1;
		$no_sort[$key]  = $no_count >= 0 ?  $noread_list[$no_count]['dateline'] : $admin_info[$key]['online'];//获得最新更新一条时间

		$admin_info[$key]['lastmsg_date'] = !empty($noread_list[$no_count]['dateline']) ? $noread_list[$no_count]['dateline'] :'';
		$admin_info[$key]['lastmsg_con']  = !empty($noread_list[$no_count]['content']) ? $noread_list[$no_count]['content'] :'';
			
	}
	
	//if(count($admin_info) > 1) {
		array_multisort($no_sort, SORT_DESC, $admin_info);//排序，将按照信息更新时间
	//}

	return $admin_info;
}

/*
 * 判断是否是初始管理员
 */
function check_system_manager($user_id)
{
	$db = connection();
	$sql_check = "SELECT privs FROM ".DB_PREFIX."user_priv where user_id in (" . $user_id . ") AND store_id = '0'";
	$check_list = $db->getAll($sql_check);

	foreach ($check_list as $key => $val)
	{
		if ($val['privs'] == 'all')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

//处理头像地址
function avatar_show_src($uid, $size='small') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'small';
	if (empty($uid))
	{
		return SITE_URL.'/../avatar/noavatar_'.$size.'.gif';
	}else {
		$avatarfile = avatar_file($uid, $size);
		if ( @fopen( SITE_URL.'/upload/avatar/'.$avatarfile, 'r' ) )
		{
			return SITE_URL.'/upload/avatar/'.$avatarfile;
		}
		
		return SITE_URL.'/../avatar/noavatar_'.$size.'.gif';
	}
}

//得到头像
function avatar_file($uid, $size) {
	global $_SGLOBAL, $_SCONFIG;

	$type = empty($_SCONFIG['avatarreal'])?'virtual':'real';
	$var = "avatarfile_{$uid}_{$size}_{$type}";
	if(empty($_SGLOBAL[$var])) {
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		$_SGLOBAL[$var] = $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
	}
	return $_SGLOBAL[$var];
}


?>