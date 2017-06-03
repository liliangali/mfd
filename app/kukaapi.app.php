<?php
class KukaapiApp extends MallbaseApp
{
   var $_special_code_mod;
   var $_special_log_mod;
    function __construct(){
        parent::__construct();
        $this->_special_code_mod = &m("special_code");
        $this->_special_log_mod = &m("special_log");
        header("Content-Type:text/html;charset=" . CHARSET);
    }
    
    function index()
    {
        $args = $_REQUEST;
        if(md5('mfd') != $args['appid'])
        {
            $this->api_return('', 40009, 'Verify account fail.');
            return;

        }
        unset($args['appid']);
        ksort($args, SORT_STRING);
		$sn = $_REQUEST['sn'];
		$s_data = array(
			'to_id'=>$_REQUEST['to_id'],
			'user_name'=>$_REQUEST['user_name'],
			'is_used'  =>$_REQUEST['is_used'],
			'to_time'  =>$_REQUEST['to_time'],
			'source'   =>$_REQUEST['source'],
		);
		
	    $ret = $this->_special_code_mod->edit('cate>19 and sn="'.$sn.'"',$s_data);
        if(!$ret)
        {
            $this->json_error('系统异常');
            return;
        }

        return $ret;
    	
    	
    }

    function get()
    {
        $args = $_REQUEST;
        if(md5('mfd') != $args['appid'])
        {
            $this->api_return('', 40009, 'Verify account fail.');
            return;

        }
        unset($args['appid']);
        ksort($args, SORT_STRING);
        $sn = $_REQUEST['sn'];
        $info =  $this->_special_code_mod->get('cate>19 and sn="'.strtoupper($sn).'"');//转变大写

        if(empty($info))
        {
            $this->json_error('不存在此酷卡');
            return;

        }
        $this->api_return($info);
        


    }
    
    function get_special_count()
    {
    	$args = $_REQUEST;
        
        if(md5('mfd') != $args['appid'])
        {
            $this->api_return('', 40009, 'Verify account fail.');
            return;

        }
        unset($args['appid']);
        ksort($args, SORT_STRING);
        $v = $args['v'];
      
        $info = $this->_special_code_mod->find(array(
                'conditions' => "cate=".$v,
                'count' => true,
            ));
        if(empty($info))
        {
        	$this->json_error('酷卡信息不存在');
            return;
        }
        
        $this->api_return($info);
    }
    
    function is_used_count(){
    	
    	$args = $_REQUEST;
        
        if(md5('mfd') != $args['appid'])
        {
            $this->api_return('', 40009, 'Verify account fail.');
            return;

        }
        unset($args['appid']);
        ksort($args, SORT_STRING);
        $k = $args['k'];
      
        $info = $this->_special_code_mod->find(array(
                'conditions' => "is_used=1 and cate=".$k,
                'count' => true,
            ));
        if(empty($info))
        {
        	$this->json_error('酷卡信息不存在');
            return;
        }
        
        $this->api_return($info);
    }
    
    function get_log_info(){
    	
    	$args = $_REQUEST;
        
        if(md5('mfd') != $args['appid'])
        {
            $this->api_return('', 40009, 'Verify account fail.');
            return;

        }
        unset($args['appid']);
        ksort($args, SORT_STRING);
        $conditions = $args['conditions'];
        $cate       = $args['cate'];
        $page		= $args['page'];
        $order 		= $args['order'];
        $sort		= $args['sort'];
        
        $list = $this->_special_log_mod->find(array(
            'conditions' => $conditions." and  cate='{$cate}'",
            'limit' => $page,
            'count' => true,
            'order' => "$sort $order",
        ));
        
        if(empty($list))
        {
        	$this->json_error('暂无数据');
            return;
        }
        
        $this->api_return($list);
    
    }
    
    

    // 定义返回方式
    function api_return($data, $status=0, $error='')
    {
     
        echo json_encode(array('data'=>$data, 'code'=>$status, 'message'=>$error));
    }

}


