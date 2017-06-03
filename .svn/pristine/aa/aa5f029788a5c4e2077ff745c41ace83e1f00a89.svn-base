<?php
namespace Cyteam\Module;
use Cyteam\Config\Config;

class Module
{
	var $_shuffling_mod;
	var $_shuffling_group_mod;
	var $_motif_mod;
	var $_motif_group_mod;
	var $_motif_rel_content_mod;
	var $pc_banner_code;
	var $pc_ads_code;
	var $app_banner_code;
	var $app_ads_code;
	
	function __construct($param = []){
		$this->_shuffling_mod = & m('shuffling');
        $this->_shuffling_group_mod = & m('shufflinggroup');
        $this->_motif_mod=&m('motif');
        $this->_motif_group_mod=&m('motifgroup');
        $this->_motif_rel_content_mod=&m('motifrelcontent');
        $this->pc_banner_code='pc_homepage';
        $this->pc_ads_code='pc_ad_location';
        $this->app_banner_code='app_homepage';
        $this->app_ads_code='app_ad_location';
	}

	 /**
    *获取首页轮播图
    *@version 1.0
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月19日
    *@return 
    */
    function getBanners($code,$site_id){
        $groups=$this->_shuffling_group_mod->get(array(
        		'conditions'=>"code='{$code}' and site_id={$site_id}"
        ));
        $ads=array();
        if($groups){
        	$ads=$this->_shuffling_mod->find(array(
        			'conditions'=>"status=1 and site_id={$site_id} and groups=".$groups['id'],
        			'order'=>'sort_order ASC,id ASC',
        			
        	));
        }
        return $ads;
    }
    //END function

    /**
     *获取首页广告位
     *@version 1.0
     *@author cyrus <2621270755@qq.com>
     *@date 2016年5月19日
     *@return
     */
    function getAds($code,$site_id=1){
    	$columns = $this->_motif_mod->find ( array (
    			'conditions' => "m.is_show=1 and m.is_delete=0 and mg.code='{$code}'",
    			'order' => 'm.sort_order ASC,m.id ASC',
    			'join' => 'has_location',
    			'fields' =>'m.*'
    	) );
    
    	 
    	if(is_array($columns)){
    		foreach ($columns as $k=>$v){
    			$relcontents=$this->_motif_rel_content_mod->find(array(
    					'conditions'=>"is_show=1 and parent_id=".$v['id'],
    					'order'=>"sort_order ASC,id ASC"
    			));
    			$columns[$k]['rc']=array_values($relcontents);
    		}
    
    	}
    	 
    	 
    	return $columns;
    }
    //END function
    /**
    *-----------------------------------------------------------
    *固定url规则转换
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年6月16日
    *@version 1.0
    *@return 
    */
    function convertUrl($url){

  
    	if($url==='productlist'){
    		
    	}
    	if($url==='productlist/分类ID'){
    	
    	}
    	if($url==='product/商品ID'){
    	
    	}
    	if($url==='diy'){
    	
    	}
    	if($url==='diy/分类ID'){
    	
    	}
    	if($url==='activity'){
    	
    	}
    	if($url==='activitylist/活动ID'){
    	
    	}
    	if($url==='article/文章ID'){
    	
    	}
    }
    //END function
}