<?php

class AboutApp extends MallbaseApp
{
	var $_folly_mod;
    var $_active_mod;
    
    function __construct(){
        parent::__construct();
        $this->_folly_mod = & m('fimg');
      
     
    }

    function index()
    {
    	
  	/*	$list = $this->_folly_mod->find(array(
  				'conditions' => "1=1 AND tpl_child_title='关于我们-中间'",
  				'order'		=> 'id ASC',
  				'count'      => true,
  				'index_key' => '',
  		));
  		
  	    $res = array();
  	    
    	foreach ($list as $key=>$val)
  		{
  			$list[$key]['uploadfiles'] =SITE_URL.'/'.$val['uploadfiles'];
  			
  		}
  	    
  		foreach ($list as $key=>$value)
  		{
  			$list[$key]['uploadfiles'] =SITE_URL.'/'.$value['uploadfiles'];
  			$res[$value['l_order']][$value['id']] = $value;
  		}
  		
  	 
  	    $list2 = $this->_folly_mod->find(array(
  				'conditions' => "1=1 AND tpl_child_title='关于我们-底部'",
  				'order'		=> 'l_order ASC',
  				'count'      => true,
  		));
  		
    	foreach ($list2 as $key=>$value)
  		{
  			$list2[$key]['uploadfiles'] =SITE_URL.'/'.$value['uploadfiles'];
  		}
  	    $this->assign('list', $res);
  	    $this->assign('list2', $list2);*/
    	$this->_config_seo('title', '注册新用户 - 关于我们 - 帮助中心 - mfd麦富迪');
        $this->display('about.html');
    }

    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }

}

?>