<?php
    chdir(dirname(__FILE__));// cd 到php脚本所在的目录
    error_reporting(E_ALL ^ E_DEPRECATED);//=====  由于本地的myql版本过高 不能用mysql_connect方法 这里设置错误级别  =====
    $end = "\r\n";
    //echo "start:time".date("Y-m-d H:i:s").$end;
    $start_time = time();
    include_once 'order_func.php';
    initDb();
    
    $limit_time = 7776000;//=====  90*24*60*60  =====
    $sql = "SELECT cy_time,user_id FROM cf_member WHERE cy_check = 0 AND member_lv_id > 1 AND member_lv_id < 5";//=====  创业顾问不降级  =====
    $m_list = getAlls($sql);
//     dump($m_list);
    if ($m_list) 
    {
        foreach ($m_list as $key => $value) 
        {
            $cy_time = $value['cy_time'];
            if (!$cy_time) 
            {
                continue;
            }
           
            $end_time = $cy_time + $limit_time;
            $user_id = $value['user_id'];
            $time = time();
            //=====  如果当前时间已经超过90天的限制 则查询order_cash_log有没有对应的积分记录  没有则需要降级  =====
            if ($end_time < $time) 
            {
                $sql = "SELECT id FROM cf_order_cash_log WHERE user_id = $user_id AND type=1 AND  add_time > $cy_time ";
                $order_cash_res = getOne($sql);
                if ($order_cash_res) //=====  存在则只需要把member的check_cy置为1  =====
                {
                    $data['cy_check'] = 1;
                    $sql = update($data, "user_id = $user_id",'cf_member');
                    mysql_query($sql);
                }
                else //=====  取消资格  =====
                {
                    jb_invite($value);
                }
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
    *
    *@author liang.li <1184820705@qq.com>
    *@2015年8月7日
    */
    function jb_invite($user_info) 
    {
        $user_id = $user_info['user_id'];
        if (!$user_id)
        {
            return false;
        }
        $sql = "SELECT * FROM cf_member_invite WHERE invitee = $user_id AND type = 1";
        $invite = getOne($sql);
        
        $sql = "SELECT * FROM cf_special_code WHERE to_id = $user_id AND cate<20";
        $log_lists  = getOne($sql);
        
        //=====  查询是否实名认证  =====
        /* $sql = "SELECT * FROM cf_auth WHERE user_id = $user_id";
        $auth  = getOne($sql); */
        
        $data_log['old_member_lv_id'] = $user_info['member_lv_id'];
        if(!empty($invite)){  //=====  删除创业者和消费者的绑定关系  =====
        
            $sql = "SELECT * FROM cf_member_invite WHERE inviter = $user_id  AND type = 0";
            $inviter_list = getAlls($sql);
            if ($inviter_list)
            {
                $data_log['inviter'] = json_encode($inviter_list);
            }
            $data_log['invitee'] = json_encode($invite);
            $data_log['member_lv_id'] = 1;
        
            $invite_id = $invite['id'];
            $sql = drop("id = $invite_id AND type = 1",'cf_member_invite');//=====  清除创业者和递推人员的关系  =====
            mysql_query($sql);
        
            $sql = drop("inviter = $user_id  AND type = 0",'cf_member_invite');//=====  清除创业者和消费者的关系  =====
            mysql_query($sql);
        
            $sql = update(array('member_lv_id'=>'1','lv_time'=>time(),'cy_check'=>1), "user_id = $user_id",'cf_member');
            mysql_query($sql);
        }
        if(!empty($log_lists))
        {
            if($log_lists['cate']==1)
            {
                $data_log['member_lv_id'] = 2;
                $data['member_lv_id'] = 2;
                /* if ($auth)
                {
                    $data_log['member_lv_id'] = 3;
                    $data['member_lv_id'] = 3;
                } */
                $data['cy_check'] = 1;
                $data['lv_time'] = time();
                $sql = update($data, "user_id = $user_id",'cf_member');
                mysql_query($sql);
            }
            elseif($log_lists['cate'] == 2)//初级
            {
                $data_log['member_lv_id'] = 1;
                $sql = drop("to_id = $user_id",'cf_special_code');
                mysql_query($sql);
                $log_id = $log_lists['log_id'];
        
                $sql = update(array('num'=>'num - 1'),"id = $log_id",'cf_special_log');
                mysql_query($sql);
        
                $sql = update(array('member_lv_id'=>'1','lv_time'=>time(),'cy_check'=>1), "user_id = $user_id",'cf_member');
            }
            $data_log['special_code'] = json_encode($log_lists);
        }
        $data_log['user_id'] = $user_id;
        $data_log['add_time'] = time();
        $sql = add('cf_cy_reload_log', $data_log);
        //       echo $sql;exit;
        mysql_query($sql);
        return true;
    }
    
    /**
     * 会员邀请关系解绑  包含bd
     * user_id:创业者
     */
    function jb_invite_bak($user_id='')
    {
        if (!$user_id)
        {
            return false;
        }
    
        
        
        $this->_member_mod =& m('member');
        $this->_special_code_mod = & m('special_code');
        $this->_special_log_mod = & m('special_log');
    
        $invite = $this->geninvite_mod->get('invitee='.$user_id);
        $log_lists  = $this->_special_code_mod->get('to_id='.$user_id);
    
    
        if(!empty($log_lists)){
            if($log_lists['cate']==1){
                //越级
                $this->_member_mod->edit('user_id='.$user_id,array('member_lv_id'=>'2'));
            }elseif($log_lists['cate']==2){
                //初级
                $d_special_num  = $this->_special_code_mod->drop('to_id='.$user_id);
                $this->_special_log_mod->setDec('id='.$log_lists['log_id'],'num',$d_special_num);
                $this->geninvite_mod->drop('id='.$invite['id']);
                $this->_member_mod->edit('user_id='.$user_id,array('member_lv_id'=>'1'));
            }
        }
        return true;
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    