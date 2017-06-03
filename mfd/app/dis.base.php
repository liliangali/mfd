<?php

/**
 *    主题管理基类
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class DisApp extends BackendApp
{
    function __construct()
    {
        parent::BackendApp();
    }

    function disCats(){
    	return array(
    			'婚庆系列' => "婚礼礼服",
    			'校园系列'  => "花样美男",
    			'职场系列'  => "风云领袖",
    			'休闲系列'  => "闲情雅致",
    			'儿童系列'  => "绅士童年",
    			'明星同款'  => "明星同款",
    		);
    }
	function jpjz_disCats(){
		return array('影视热点','时尚大叔','大牌同款','休闲春夏','亲子系列');
	}
}



?>