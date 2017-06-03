<?php

define('ROOT_PATH', str_replace('h5/index.php', '', str_replace('\\', '/', __FILE__)));
//header("Location: index.html\n"); exit;
define('PROJECT_PATH', dirname(__FILE__));
//define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . 'h5/eccore/rctailor.php');
include(ROOT_PATH . 'vendor/autoload.php');


/* 定义配置信息 */
ecm_define(ROOT_PATH . 'h5/data/config.inc.php');


RCTailor::startup(array(
    'default_app'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  ROOT_PATH . 'h5/app',
    'external_libs' =>  array(
        ROOT_PATH . 'h5/includes/constants.base.php',
        ROOT_PATH . 'h5/includes/global.lib.php',
        ROOT_PATH . 'includes/global.fun.php',
        ROOT_PATH . 'h5/includes/libraries/time.lib.php',
        ROOT_PATH . 'h5/includes/libraries/request.lib.php',
        ROOT_PATH . 'h5/includes/ecapp.base.php',
        ROOT_PATH . 'h5/includes/plugin.base.php',
        ROOT_PATH . 'h5/app/frontend.base.php',
        ROOT_PATH . 'h5/includes/functions.lib.php',      //公用函数
    ),
));

?>
