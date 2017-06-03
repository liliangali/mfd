<?php

/* 会员评论 */
class FigureorderlogModel extends BaseModel
{
    var $table  = 'figure_order_log';
    var $prikey = 'idfigureorderlog';
    var $_name  = 'figure_order_log';

 	
    var $_relation = array(
		'has_serve' => array(
            'model'         => 'serve',
            'type'          => HAS_ONE,
            'foreign_key'   => 'idserve',
			'refer_key'		=> 'serviceid'
        ),
     );
    
	/**
	 * 添加分成日志
	 * Enter description here ...
	 * @param unknown_type $order_id
	 * @param unknown_type $serviceid
	 */
    function addlog($order_id,$serviceid)
    {
    	$this->figureorderloglog('添加分成日志开始code:0['.$order_id.']['.$serviceid.']');
    	if(!$order_id||!$serviceid)
    	{
    		$this->figureorderloglog('参数不能为空code:1');
    		//参数不能为空
    		//var_dump('111');exit;
    		return false;
    	}
        $order_mod=m('order');
    	$order_data=$order_mod->get(array(
    	
    	'conditions'=>' order_figure.order_id='.$order_id." and order_figure.serviceid=$serviceid",
    	'join'=>'has_orderfigure',
    	));
    	//var_dump($order_data);exit;
    	
    	if(!$order_data)
    	{
    		//不是本服务点的定单
    		//var_dump('222');exit;
    		$this->figureorderloglog('不是本服务点的定单code:2');
    		return false;
    	}
    	
    	
    	
    	if(!in_array($order_data['status'],array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED)))
    	{
    		//echo $order_data['status'];
    		//订单状态必须为ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED
    		//echo(ORDER_ACCEPTED);exit;
    		$this->figureorderloglog('订单状态必须为ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHEDcode:3');
    		return false;
    	}else 
    	{
    		//echo '1';
    	}
    	
    	
    	
    	//
    	$serve_mod=m('serve');
    	$serve_data=$serve_mod->get(array(
    	'conditions'=>'serve.idserve='.$serviceid,
    	'join'=>'has_member_lv',
    	));
    	
    	
    	
    	//var_dump($serve_data['dis_count']);exit;
    	//MemberLv
    	//var_dump($order_data['order_amount']);exit;
    	
    	
    	
    	$div_amount=$order_data['goods_amount']*($serve_data['dis_count']/10);
    	$user_id=$serve_data['userid'];
    	//var_dump($div_amount);exit;
    	//var_dump($user_id);exit;
    	if($this->unique_order_id($order_id))
    	{
    		
    		
    		
    		$memberlv_mod=m('memberlv');
    		
    		$memberlv_mod->auto_level($user_id,'service');
    		
    		$this->figureorderloglog('添加到数figureorderlog据库code:5');
	    	return $this->add(array('order_id'=>$order_id,
		    	'add_time'=>gmtime(),
		    	'serviceid'=>$serviceid,
		    	'buyer_name'=>$order_data['buyer_name'],
		    	'buyer_id'=>$order_data['buyer_id'],
		    	'order_amount'=>$order_data['goods_amount'],
		    	'order_sn'=>$order_data['order_sn'],
	    		
		    	'div_count'=>$serve_data['dis_count'],
	    		
	    		'div_amount'=>$div_amount,
	    	));
    	}else 
    	{
    		//订单量体只能分成一次
    		//var_dump('444');exit;
    		$this->figureorderloglog('订单量体只能分成一次code:4');
    		return false;
    	}
    }
    
    
    
	/*
     * 判断名称是否唯一
     */
    function unique_order_id($order_id)
    {
        $conditions = "order_id = '$order_id' ";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
	function figureorderloglog($msg)
	{
	    $filename = ROOT_PATH . "/temp/logs/figureorderlog/" .date("Ym"). ".log";
	
	    if (!is_dir('temp/logs/figureorderlog'))
	    {
	        ecm_mkdir(ROOT_PATH . '/' . 'temp/logs/figureorderlog');
	    }
	
	    $handler = null;
	
	    if (($handler = fopen($filename, 'ab+')) !== false)
	    {
	        fwrite($handler, date('r') . "\t$msg\n");
	        fclose($handler);
	    }
	}
    
    
}

?>