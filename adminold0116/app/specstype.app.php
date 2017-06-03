<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
/**---------------------------------------------------------------------
 *    规格生成货品控制器
* ---------------------------------------------------------------------
* @author shaozz<shaozizhen94@163.com>
* 2016-5-18
*/
class SpecstypeApp extends BackendApp
{
	var $_goodsspec_mod;
	var $_goodstypespec_mod;
	var $_specvalues_mod;
	
    function __construct()
    {
    	
         parent::__construct();
         $this->_goodsspec_mod =& m('specification');
         $this->_goodstypespec_mod =& m('goodstypespec');
         $this->_specvalues_mod =& m('specvalues');
         
    }
    
    function index(){
    	
        $ids   = $_GET['ids'];
        $goodsspec=$this->_goodstypespec_mod->find(array(
        		"conditions"=>"type_id = {$ids}",
        		'join' =>'goods_type_specs',
        ));
        foreach($goodsspec as $key=>$val){
        	$goodsspec[$key]['spec_value']=$this->_specvalues_mod->find(array(
        			'conditions'=>"spec_id='{$val['spec_id']}'",
        	));
        	
        }
        
       $this->assign('spec',$goodsspec);
        
      
        
        
        $this->display("specstype.index.html");
    }
}

?>
