<?php
// define('UC_API', 'http://localhost/ucflash');
// define('UC_ROOT', ereg_replace("[\\/]{1,}","/",dirname(__FILE__)).'/');
// define('UC_DATADIR', UC_ROOT.'upload');
// define('UC_DATAURL', UC_API.'/upload');
//session初始化
// session_start();
require_once('../includes/avatar.class.php');
$objAvatar = new Avatar();
// var_dump($_SESSION['user_info']['user_id']);
// $_SESSION['v5_user_id'] = 1;

if ($objAvatar->getgpc('m') == 'user'){

	unset($GLOBALS, $_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);

	$_GET		= $objAvatar->daddslashes($_GET, 1, TRUE);
	
	$_POST		= $objAvatar->daddslashes($_POST, 1, TRUE);
	
	$_COOKIE	= $objAvatar->daddslashes($_COOKIE, 1, TRUE);
	$_SERVER	= $objAvatar->daddslashes($_SERVER);
	$_FILES		= $objAvatar->daddslashes($_FILES);
	$_REQUEST	= $objAvatar->daddslashes($_REQUEST, 1, TRUE);
	
	$a = $objAvatar->getgpc('a');
	$release = intval($objAvatar->getgpc('release'));
	
	$method = 'on'.$a;

	if(method_exists($objAvatar, $method) && $a{0} != '_') {
		$data = $objAvatar->$method();
		echo is_array($data) ? $objAvatar->serialize($data, 1) : $data;
		exit;
	} elseif(method_exists($control, '_call')) {
		$data = $control->_call('on'.$a, '');
		echo is_array($data) ? $control->serialize($data, 1) : $data;
		exit;
	} else {
		exit('Action not found!');
	}
}

//获取头像
// $avatar = $objAvatar->avatar_show($_SESSION['v5_user_id'],'big');