<?php
class FigureModel extends BaseModel
{
	var $table  = 'figure';
	var $prikey = 'idfigure';
	var $_name  = 'figure';
	
	
	var $_autov = array(
        
        'lw'=>array(
        	'required'=>true,
			'filter'    => 'trim',
        ),

        
    );

	/*
     * 判断名称是否唯一
     */
    function unique($user_name)
    {
        $conditions = "user_name = '$user_name' ";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
/*
     * 判断名称是否唯一
     */
    function uniquebyid($userid)
    {
        $conditions = "userid = '$userid' ";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
}