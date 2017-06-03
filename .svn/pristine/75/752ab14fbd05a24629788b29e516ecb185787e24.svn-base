<?php

/**
 * Laravel Eloquent Database
 */

namespace Cyteam\Db;
use PDO;
use Illuminate\Database\Capsule\Manager as DB;

class Led {
    public $table = '';
    public static $prefix = 'cf_';
    public $container;
    private static $connect = false;
    function __construct($connect = false) {
        //if ($connect) $this->connect();
    }
    
    public static function table($name) {
        
        if (self::$connect == false) self::connect();
        
        return DB::table($name);
    }
    
    function connect(){
        $db = new DB;
        $db->addConnection(self::getDbConfig());
        $db->setAsGlobal();// 这样就可以使用DB::xxx这样的方法
        $db->bootEloquent();// 启动ORM支持
        /**
         * PDO 类型 默认值【PDO::FETCH_CLASS】
         * 默认情况下 Laravel 的数据库是用 PDO 来操作的，这样能极大化的提高数据库兼容性。
         * 那么默认查询返回的类型是一个对象，也就是如下的默认设置。
         * 如果你需要返回的是一个数组，你可以设置成 'PDO::FETCH_ASSOC'
         */
        $db->setFetchMode(PDO::FETCH_ASSOC);
        self::$connect = true;
    }
    
    function getDbConfig(){
        
        //$config = require '/data/config.inc.php';
        $config = require ROOT_PATH.'/data/languages/config.php';
        
        $db = explode(':', str_replace('/', ':', str_replace('@', ':', str_replace('//', '', $config['sc-utf-8']['db']))));
        return [
                'driver'    => $db[0],
                'host'      => $db[3],
                'database'  => $db[5],
                'username'  => $db[1],
                'password'  => $db[2],
                'charset'   => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix'    => self::$prefix ? self::$prefix : $config['DB_PREFIX'],
        ];
    }
}