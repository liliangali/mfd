<?php

class CyApp extends MallbaseApp{

    var $_mod;
	function __construct()
	{
	    $this->_mod = m('cy');
		parent::__construct();
	}
	
	/**
	*content
	*@author liang.li <1184820705@qq.com>
	*@2015年4月29日
	*/
	function index() 
	{
	    $region_mod = m('region');
	    $list1 = $region_mod->get_options(2);
	    $this->assign('region1',$list1);
	    $list2 = $region_mod->get_options(246);
	    $this->assign('region2',$list2);
	    
	    if (!IS_POST) 
	    {
	        $ask = $this->_mod->getAsk();

	        $id = isset($_GET['id']) ? $_GET['id'] : 0;
	        if ($id) 
	        {
	           $this->assign('info',$info);
	        }
	        $this->assign('ask',$ask);
	       $this->display("cy/index.html");
	    }
	    else 
	    {
	        $post = $_POST;
	        
	        if (!$post['phone'] || !$post['weixin'] || !$post['name']) 
	        {
	             $this->display('cy/error.html');
	             return;
	        }
	        
	        
	        $post['add_time'] = time();
	        if ($this->_mod->add($post)) 
	        {
	            
	            $this->display('cy/msg.html');
	            return;
	        }	        
	        
	        $this->display('cy/error.html');
	        return;
	    }
	}
	
	/**
	*ajax获得三级联动
	*@author liang.li <1184820705@qq.com>
	*@2015年4月29日
	*/
	function get_region() 
	{
	    $region_mod = m('region');
	    $pid = $_POST['pid'];
	    if (!$pid) 
	    {
	        $this->json_error('失败');
	    }
	    
	    $list = $region_mod->get_options_html($pid,0);
	    $this->json_result($list);
	    
	}
	
	/**
	*testdata
	*@author liang.li <1184820705@qq.com>
	*@2015年4月30日
	*/
	function testdate() 
	{
	    echo date('Y-m-d H:i:s',1430362342);
	}

	
}
?>