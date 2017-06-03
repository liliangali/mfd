<?php
/** * 订单自动收货 *  * -------------------------------------------------------- * @author       小五 * $Id: order_commit_new_pc.php 12012 2015-11-25 09:32:11Z xiao5 $ * $Date: 2015-11-25 17:32:11 +0800 (星期三, 25 十一月 2015) $ * -------------------------------------------------------- * @example */
    chdir(dirname(__FILE__));// cd 到php脚本所在的目录
    //error_reporting(E_ALL ^ E_DEPRECATED);//=====  由于本地的myql版本过高 不能用mysql_connect方法 这里设置错误级别  =====
    $end = "\r\n";
    //echo "start:time".date("Y-m-d H:i:s").$end;
    $start_time = time();
    include_once 'order_func.php';
    initDb();
    define('ORDER_PENDING', 11); //待付款
    define('ORDER_WAITFIGURE', 12);//待量体  //只有订单需要量体时才会有这个状态  //在前台展示为已支付
    define('ORDER_ACCEPTED', 20);//已支付
    define('ORDER_PRODUCTION', 60);//生产中
    //define('ORDER_CHECKING', 13);  //订单审核
    
    define('ORDER_STOCKING', 61);//备货中
    define('ORDER_SHIPPED', 30);  //已发货
    define('ORDER_FINISHED', 40); //已完成
    define('ORDER_REPAIR', 41);//返修中
    //define('ORDER_CANCEL', 42);//已取消
    define('ORDER_CANCELED', 0);//已取消
    define('ORDER_ABNORMAL', 43);//订单异常
    define('ORDER_WAITEDIT', 72); //订单重下(修改)
    
    $limit_time = 15*24*3600;//=====  30*24*60*60  =====
    $time = $start_time-$limit_time;
    /* 取当前时间 15天之前订单状态为 已发货  */
    $sql = "SELECT order_id,ship_time,from_unixtime(ship_time,'%Y-%m-%d') as u ,curdate() as n, from_unixtime($time,'%Y-%m-%d') as x from cf_order WHERE ship_time >0 and  status IN(30) AND is_back_money = 0  AND ship_time < $time ";
//     $sql = "SELECT order_id,ship_time,from_unixtime(ship_time,'%Y-%m-%d') as u ,curdate() as n, from_unixtime($time,'%Y-%m-%d') as x from cf_order WHERE order_id = 4270 ";
    $list = getAlls($sql);
    echo 'sql:'.$sql."\r\n\t";
    echo 'count:'.count($list)."\r\n\t";
    $url = furl();
    if ($list) 
    {
        foreach ($list as $key => $value) 
        {
            if (!$value['ship_time']) 
            {
                continue;
            }
            $r_url = $url.$value['order_id'];
            echo 'http:'.$r_url."\r\n\t";
            $res = https_request($r_url);
            echo 'res:'.$res['statusCode']."\r\n\t";
            if ($res['statusCode'] == 0) 
            {
                $data['add_time'] = time();
                $data['order_id'] = $value['order_id'];
                $data['desc'] = $res['error']['msg'];
                $sql = add("cf_order_cash_error_log", $data);
                mysql_query($sql);
            }
        }
    }
    
    function dump($arr)
    {
        echo '<pre>';
        var_dump($arr);
        exit;
    }
    
    /**
     * liang.li
     * @param unknown $url
     * @param string $data
     * @return mixed
     */
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
    }
    
    function furl()
    {
        $data = include "../data/config.inc.php";
        $url = $data['SITE_URL'];
        $url_arr = parse_url($url);
        $u =  $url_arr['host'];
        $api_url = "";
        if ($u == "www.dev.mfd.cn") 
        {
            $api_url = "api.dev.mfd.cn:8080/soap/profit.php?act=orderCash&order_id=";
        }
        elseif ($u == "mfd.mfd.com")
        {
            $api_url = "api.mfd.com/soap/profit.php?act=orderCash&order_id=";
        }
        elseif ($u == "www.test.mfd.cn")
        {
            $api_url = "api.test.mfd.cn/soap/profit.php?act=orderCash&order_id=";
        }
        elseif ($u == "www.mfd.cn")
        {
            $api_url = "api.mfd.cn/soap/profit.php?act=orderCash&order_id=";
        }
        return $api_url;
//         dump(parse_url($url));
    }
    

    
    /**
    *获得一条记录 一维数组
    *@author liang.li <1184820705@qq.com>
    *@2015年8月7日
    */
    function getOne($sql) 
    {
        mysql_query("SET NAMES 'utf8';");
        $res = mysql_query($sql);
        if(!$res)
            return 0;
        
        $rs = getDbOne($res);
        return $rs;
    }
    
    /**
    *单条记录
    *@author liang.li <1184820705@qq.com>
    *@2015年8月7日
    */
    function getDbOne($db_res) 
    {
        $row = array();
        if($db_res) 
        {
            $row = mysql_fetch_assoc($db_res);
        }
        return $row;
    }
    
    /**
    *删除操作
    *@author liang.li <1184820705@qq.com>
    *@2015年8月7日
    */
    function drop($where , $table = '',$limit = '' ) 
    {
        if ($limit) 
        {
            $limit = " LIMIT $limit";
        }
        mysql_query("SET NAMES 'UTF8';");
        $sql = "DELETE FROM  `" . $table . "`" . " where " . $where.$limit;
        return $sql;
    }
    
    
    /**
    *添加操作
    *@author liang.li <1184820705@qq.com>
    *@2015年8月7日
    */
    function add($table,$data) 
    {
        $str_fields = "(";
        $str_value = "(";
        if ($data) 
        {
            foreach ($data as $key => $value) 
            {
                $str_fields .= "`".$key."`".",";
                $str_value  .= "'$value'".",";
            }
            $str_fields = trim($str_fields,',');
            $str_value  = trim($str_value,",");
            $str_fields = $str_fields.")";
            $str_value  = $str_value.")";
            $str = $str_fields." VALUES ".$str_value;
            $sql = "INSERT INTO `$table` ".$str;
            return $sql;
        }
        return '';
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    