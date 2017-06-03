<?php
date_default_timezone_set('PRC');
@ini_set('display_errors', 0);
@ini_set('memory_limit', '1024M');
ini_set("soap.wsdl_cache_enabled", "0");
error_reporting(E_ALL);


define('PROJECT_PATH', str_replace('config.php', '', str_replace('\\', '/', __FILE__)));//指到v1


define('APP_NAME', 'webservice');

define("KERNEL_PATH",PROJECT_PATH.'kernel/');//核心代码的目录
define('IN_ECM', true);
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));   //指到soaapi
define('LOG_PATH',  './logs/'); //日记目录
define('APP_DEBUG',1);//调试模式
define('CHARSET', "utf8");//数据库字符集
define('LANG','utf-8');
define('IMG_PATH','upload');

//=====引入mysql配置文件 并且初始化 =====
include_once KERNEL_PATH.'config/db_config.php'; //mfd mysql配置
define('DB_CONFIG',"mysql://$db_user:$db_pass@$db_host/$db_name");//db()
define('DB_PREFIX',$prefix);//mfd表前缀

//=====设置路径====
set_include_path(get_include_path() . PATH_SEPARATOR . KERNEL_PATH . 'class');
set_include_path(get_include_path() . PATH_SEPARATOR . KERNEL_PATH . 'config');

//====加载公共的类
include_once PROJECT_PATH.'includes/result.class.php';//这个类是集中处理返回值的
include_once PROJECT_PATH.'includes/model.base.php';//必须要在functions.php 前面 否则m方法不能用
include_once PROJECT_PATH.'includes/libraries/notice.lib.php';
include_once PROJECT_PATH.'includes/app.base.php';
include_once(PROJECT_PATH . 'includes/mysql.php');  //db要引入的mysql类
include_once(PROJECT_PATH . 'includes/constants.base.php');  //db要引入的mysql类
include_once PROJECT_PATH.'functions.php';//公共函数
include_once PROJECT_PATH.'includes/Log.class.php';//日志
include_once PROJECT_PATH.'includes/cls_json.php'; //JSON配置
include_once(PROJECT_PATH.'includes/lang.php');//语言项管理
include_once(PROJECT_PATH.'includes/global.fun.php');
$json = new JSON;







//引入公共的方法
include_once (PROJECT_PATH . 'includes/auth.class.php'); //引用验证文件
include_once 'includes/cls_common.php'; //引用公共API文件 todo:这个文件处理所有的请求/返回


 define('BELONG_ARTICLE',    1);
 define('BELONG_GOODS',      2);
 define('BELONG_STORE',      3);//GOODS 和 STORE 暂时先保留,代码更新完毕后是要删的