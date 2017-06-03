<?php

/**
*栏目位置模型类
*@version 1.0
*@author cyrus <2621270755@qq.com>
*@date 2016年5月16日
*@return 
*/
class MotifgroupModel extends BaseModel
{
    var $table      =   'motif_group';
    var $prikey     =   'id';
    var $_name      =   'motif_group';
    var $alias ='mg';
    var $show_location=array(
    		1=>'pc',
    		2=>'app',
    		3=>'wap'
    );
   /**
   *获得option拼接起来的字符串
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月16日
   *@return 
   */
    function get_options_html($parent_id)
    {
    	$res = array();
    	$html = "<option value='0' >请选择</option>";
    	$groups = $this->find(array(
    			'conditions'=>'site_id='.$parent_id
    	));
    	if ($groups)
    	{
    		foreach ($groups as $key => $value)
    		{
    			
    			$html .= "<option value=".$key.">".$value['name']."</option>";
    		}
    	}
    	return $html;
    }
    //END function

}

?>