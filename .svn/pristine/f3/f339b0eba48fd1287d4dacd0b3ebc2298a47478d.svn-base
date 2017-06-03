<?php
define('IN_ECM',1);
define('DB_PREFIX', "cf_");
define('ROOT_PATH', dirname(__FILE__));
//define('SITE_URL', 'http://www.dev.mfd.cn');
define('SITE_URL', 'http://local.mfd.com');
//define('SITE_URL', 'http://192.168.1.13/');
require '../eccore/model/mysql.php';
function connection(){
    $charset = 'utf8';
    $cfg = array(
        'host'  => "127.0.0.1",
        "port"  => "3306",
        "user"  => "root",
        "pass"  => "root",
        "path"  => "local_mfd",
    );
    $db = new cls_mysql();
    $db->cache_dir = '../temp/query_caches/';
    $db->connect($cfg['host']. ':' .$cfg['port'], $cfg['user'], $cfg['pass'], $cfg['path'], $charset);
    return $db;
}
?>