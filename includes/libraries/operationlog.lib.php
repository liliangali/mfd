<?php
/**
 * 订单日志
 *
 * @author zxr
 */
class OperationLog extends Object
{
	var $db;
    
    function __construct()
    {
        
        $this->db = &db();

    }
    
	 function operation_log($conditions,$type,$mark=''){
    	if (defined('IN_BACKEND'))
    	{
    		$visitor =& env('visitor');
    		include(ROOT_PATH.'/mfd/includes/operation.inc.php');
    		Lang::load(lang_file('mfd/admin'));
    		$logdata = array();
    		if (is_array($conditions)){
    			$logdata['conditions'] = serialize($conditions);
    		}else{
    			$logdata['conditions'] = addslashes($conditions);
    		}
    		if (!$visitor->info['user_id']){
    			$sql = "SELECT * FROM cf_member $conditions";
    			$row = $this->getRow($sql);
    			$logdata['username'] = $row['user_name'];
    			$logdata['uid'] = $row['user_id'];
    			$logdata['ip'] = $row['last_ip'];
    		}else {
    			$logdata['username'] = $visitor->info['user_name'];
    			$logdata['uid'] = $visitor->info['user_id'];
    			$logdata['ip'] = $visitor->info['last_ip'];
    		}
    		$logdata['dateline'] = time();
    		$logdata['operate_type'] = $type;
    		/* 会有一些问题 app会有重复的情况 修复配置权限*/
			$flag=0;
			$logdata['operate_key']='';
/* 			header("Content-type: text/html; charset=utf-8"); */
    		if ($menu_data){
    			foreach ($menu_data as $k=>$v){
    				if($flag==1){
    					break;
    				}
    				foreach ($v as $key=>$r){
						if($flag==1){
							break;
						}
    				    //$r为数组是strstr参数报错,暂时写了个这玩意儿,但不是长久之计,如果再多一层数组怎么办~  还是抽空写个递归吧... //Ruesin
    				    if(is_array($r)){
    				    	foreach ($r as $kk=>$rr){
    				    	    if(!is_array($rr)){
    				    	        if (substr(str_replace($_SESSION['operate_app'],'',$rr),0,1)=='|' && substr(strstr($rr,$_SESSION['operate_act'],true),-1)=='|'){
    				    	            $logdata['operate_key'] = $GLOBALS['__ECLANG__'][$k].':'.$GLOBALS['__ECLANG__'][$key].':'.$GLOBALS['__ECLANG__'][$kk];
    				    	            $flag=1;
    				    	            break;
    				    	        }
    				    	    }
    				    	}
    				    }else{
    				        if (substr(str_replace($_SESSION['operate_app'],'',$rr),0,1)=='|' && substr(strstr($rr,$_SESSION['operate_act'],true),-1)=='|'){
    				            $logdata['operate_key'] = $GLOBALS['__ECLANG__'][$k].':'.$GLOBALS['__ECLANG__'][$key];
    				            $flag=1;
    				            break;
    				        }
    				    }
						
//     					exit;
    				}
    			}
    		}
    		if(!$logdata['operate_key']){
    			$logdata['operate_key']="缺少配置项,控制器为{$_SESSION['app']},方法为{$_SESSION['act']}";
    		}
    		$typedata = array('add'=>'新建','edit'=>'修改','drop'=>'删除');

    		if ($logdata['operate_key'] == '网站设置:后台登录'){
    		    $logdata['operate_key']='网站后台';
    		    $typedata['edit'] = '登录';
    		}
//     		var_dump($mark);
			if($mark){
				$logdata['memo']=addslashes($mark);
			}else{
				$logdata['memo']='操作人员：'.$logdata['username'].'在['.date('Y-m-d H:i:s',$logdata['dateline']).']对-'.$logdata['operate_key'].'-执行了'.$typedata[$type].'操作.';
			}  		
    		$sql="select * from cf_operatorlog_logs  where username='{$logdata['username']}' and uid='{$logdata['uid']}' 
    		          and ip='{$logdata['ip']}' and dateline='{$logdata['dateline']}' and operate_key='{$logdata['operate_key']}' and operate_type='{$logdata['operate_type']}'";
    		$i=$this->db->query($sql);
    		$res=mysql_fetch_array($i);
//     		header("Content-Type: text/html; charset=utf-8");
//      		var_dump($logdata);
//      		var_dump($res);
    		if(!$res){
         		$insert_info = $this->_getInsertInfo($logdata);
//          		var_dump($logdata['memo']);exit();
        		$sql="INSERT INTO cf_operatorlog_logs  {$insert_info['fields']} VALUES{$insert_info['values']}";
//         		var_dump($insert_info['fields']);
//         		var_dump($insert_info['values']);
         		//var_dump($insert_info['values']);
        		$i=$this->db->query($sql);
    		}else{
    		    return ;
    		}
    	}

    }
    /**
     *  获取插入的数据SQL
     *
     *  @author Garbin
     *  @param  array $data
     *  @return string
     */
    function _getInsertInfo($data)
    {
    	reset($data);
    	$fields = array();
    	$values = array();
    	$length = 1;
    	if (key($data) === 0 && is_array($data[0]))
    	{
    		$length = count($data);
    		foreach ($data as $_k => $_v)
    		{
    			foreach ($_v as $_f => $_fv)
    			{
    				$is_array = is_array($_fv);
    				($_k == 0 && !$is_array) && $fields[] = $_f;
    				!$is_array && $values[$_k][] = "'{$_fv}'";
    			}
    			$values[$_k] = '(' . implode(',', $values[$_k]) . ')';
    		}
    	}
    	else
    	{
    		foreach ($data as $_k => $_v)
    		{
    			$is_array = is_array($_v);
    			!$is_array && $fields[] = $_k;
    			!$is_array && $values[] = "'{$_v}'";
    		}
    		$values = '(' . implode(',', $values) . ')';
    	}
    	$fields = '(' . implode(',', $fields) . ')';
    	is_array($values) && $values = implode(',', $values);
    
    	return compact('fields', 'values', 'length');
    }
    
}

