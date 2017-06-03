<?php
class ActivepublicApp extends MallbaseApp
{

   var $_motif_mod;
   var $_motif_group_mod;
   var $_motif_rel_content_mod;
   var $_shuffling_mod;
   var $_shuffling_group_mod;
   
    function __construct(){
        parent::__construct();
        $this->_shuffling_mod = & m('shuffling');
        $this->_shuffling_group_mod = & m('shufflinggroup');
        $this->_motif_mod = & m('motif');
        $this->_motif_group_mod = & m('motifgroup');
        $this->_motif_rel_content_mod=&m('motifrelcontent');
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function index()
    {
        if(!$_GET){
            // 轮播图
   
        	$lb_site=$this->_shuffling_group_mod->get(array(
        	    'conditions'=>"site_id=1 and code='active-public'",
        	));
        	
        	if($lb_site){
        	    $banner=$this->_shuffling_mod->find(array(
        		'conditions'=>"groups={$lb_site['id']} and status=1",
        		'order'=>"sort_order ASC",	
        	));
        	  
        	}else{
        	    $banner=array();
        	}
        	
        	// 活动特惠  
        	$site= $this->_motif_group_mod->get(array(
        	    'conditions'=>" site_id=1 and code='active-public'",
        	));
        	$site_id = $site['id'];
        	if($site_id){
        	    
        	    $list  = $this->_motif_mod->get(array(
        	        'conditions'=>"is_show=1 and is_delete=0 and site_id=1 and location_id=".$site_id,
        	    ));
        	    $list['images'] =  $this->_motif_rel_content_mod->find(array(
        	        'conditions'=>"is_show=1 and parent_id=".$list['id'],
        	        'index_key' =>'',
        	    ));
        	     
        	}else{
        	    $list = array();
        	}
        	
        	$this->import_resource(array(
        			'script' => 'jquery-1.8.3.min.js,flickerplate.min.js,html5.js,css3-mediaqueries.js,jquery.plugins/jquery.validate.js,layer/layer.min.js,jquery.cookie.js,jscrollpane.js,change.city.js',
        			'style'  => "layer/skin/layer.css",
        	));
        	$this->assign('banner',$banner);
        	$this->assign("list", $list);
        	$this->_config_seo('title', '活动特惠 - mfd麦富迪');
        	$this->display("active/active.html");
        }
           
    }
}

?>