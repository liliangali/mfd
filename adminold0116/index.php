<?php
/* 应用根目录 */
define('APP_ROOT', dirname(__FILE__));          //该常量只在后台使用
define('ROOT_PATH', dirname(APP_ROOT));   //该常量是ECCore要求的
define('IN_BACKEND', true);
include(ROOT_PATH . '/eccore/admin.php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');



/* START ： PHP Debug Bar http://phpdebugbar.com/docs/base-collectors.html */require '../vendor/autoload.php';// use DebugBar\StandardDebugBar;// $debugbar = new StandardDebugBar();// $debugbarRenderer = $debugbar->getJavascriptRenderer();


// define('DS', "/");
// defined('M_EXT') or define('M_EXT', '.model.php');
// defined('M_DIR_NAME') or define('M_DIR_NAME', 'model');
// defined('M_CLASS') or define('M_CLASS', 'Model');

// defined('LIB_DIR_NAME') or define('LIB_DIR_NAME','lib');
// defined('LIB_EXT') or define('LIB_EXT','.lib.php');
// defined('LIB_CLASS') or define('LIB_CLASS','Lib');

// set_include_path(get_include_path() . PATH_SEPARATOR. dirname(dirname(__FILE__))  .DS.'lib');
// set_include_path(get_include_path() .PATH_SEPARATOR . dirname(dirname(__FILE__))  .DS.'model');

RCTailor::startup(array(
    'default_app'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  APP_ROOT . '/app',
    'external_libs' =>  array(
        ROOT_PATH . '/includes/constants.base.php',
        ROOT_PATH . '/includes/global.lib.php',
        ROOT_PATH . '/includes/global.fun.php',
        ROOT_PATH . '/includes/libraries/time.lib.php',
        ROOT_PATH . '/includes/libraries/request.lib.php',
        ROOT_PATH . '/includes/ecapp.base.php',
        ROOT_PATH . '/includes/plugin.base.php',
        APP_ROOT . '/app/backend.base.php',
        APP_ROOT . '/app/dis.base.php',
        ROOT_PATH . '/includes/functions.lib.php',
    ),
));

//$debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));
?>
