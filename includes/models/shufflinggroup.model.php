<?php

/**
 *   轮播图分组模型类
 *
 *    @author    Garbin
 *    @usage    none
 */
class ShufflinggroupModel extends BaseModel
{
    var $table      =   'shuffling_group';
    var $prikey     =   'id';
    var $_name      =   'shuffling_group';
    var $show_location=array(
    		1=>'pc',
    		2=>'app',
    		3=>'wap'
    );
   /**
   *获得option拼接起来的字符串
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月13日
   *@return 
   */
    function get_options_html($parent_id,$cur_region_id)
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