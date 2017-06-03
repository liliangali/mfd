<?php
define('IN_ECM',1);
define('DB_PREFIX', "cf_");
define('ROOT_PATH', dirname(__FILE__));
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