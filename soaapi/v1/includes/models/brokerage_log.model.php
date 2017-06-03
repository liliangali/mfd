<?php
class Brokerage_logModel extends BaseModel
{
	var $table  = 'brokerage_log';
	var $prikey = 'id';
	var $_name  = 'brokerage_log';

	var $_autov = array(
        'amount'=>array(
        	'required'=>true,
			'filter'    => 'trim',
        )
    );
	
    
    /***
     * 添加分成日志
     * @$userid用户ID
     * @$amount总金额
     */
    function add_log($userid,$amount)
    {
    	
    	$_serve_model=m('serve');
		$serve=$_serve_model->get(array(
		'join'    => 'has_brokerage',
		'conditions'=>'userid='.$userid.' and serve.serve_type = brokerage.serve_type ',
		'fields' => 'this.idserve,this.userid,brokerage.serve,brokerage.brokerage_level,this.serve_type',
		));
		if($serve)
		{
			$serve['amount']=$amount;
			$serve['brokerage_amount']=$serve['amount']/100*$serve['serve'];
			$this->add($serve);
			return true;
		}else {
			return false;		
		}
    }
}