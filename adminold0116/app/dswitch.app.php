<?php

class DswitchApp extends BackendApp
{
    private $_mod_Fabs;
    private $_mod_Fabricprice;
    private $_levels =  array(0=>'顶级',1=>'一级',2=>'二级',3=>'三级',4=>'四级',5=>'五级');
    private $_categorys = array(3=>'西服',3000=>'衬衣',2000=>'西裤',4000=>'马甲',6000=>'大衣',11000=>'女衬衣');
    function __construct()
    {
         $this->_mod_Tdict = &m("tmp_dict");
         $this->_mod_Dict = &m("dict");
         parent::__construct();
    }

    function index()
    {
//     	ALTER TABLE `diy_dict` ADD `sort` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序' AFTER `ecode`, ADD `is_display` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否显示' AFTER `sort`, ADD INDEX (`sort`, `is_display`) ;
    	$cid = empty($_GET['field_CATEGORYID']) ? '3' : $_GET['field_CATEGORYID'];
    	$this->_mod_Tdict->table=$this->_mod_Tdict->_prefix.'tmp_dict_'.$cid;
    	
    	$_GET['field_level'] = empty($_GET['field_level']) ? '0' : $_GET['field_level'];
    	$conditions = $this->_get_query_conditions(array(
    			array(

        $page = $this->_get_page(30);
        $t_list = $this->_mod_Tdict->find(array(
        	"conditions" =>'1=1 '.$conditions,
        	'fields'     => "tdict.*",
            'limit' => $page['limit'],
            'count' => true,
            'order' => "c_sno ASC"
        ));
        //获取 面料价格
        foreach ($t_list as &$r){
        	$r['price'] = '0.00';
        	if ($dicts[$r['c_id']]){
        		$r['rmbprice'] =  $dicts[$r['c_id']]['price'];
        		$r['sort'] =  $dicts[$r['c_id']]['sort'];
        		$r['is_display'] =  $dicts[$r['c_id']]['is_display'];
        		$r['lname'] =  $this->_levels[$r['c_level']];
        	}
        }
        $page['item_count'] = $this->_mod_Tdict->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign("f_list", $t_list);
        $this->assign("tname", $this->_categorys);
        
        $l_list = $this->_mod_Tdict->find(array(
        $this->assign("level", array_slice($this->_levels,0,count($l_list)));
        $this->import_resource(array(
        $this->display('diy/dswitch.index.html');
    }

    //异步修改数据
    		if ($column == 'price'){
    			$conditions = 'id = "'.$id.'"';
    			$this->_mod_Dict->edit($conditions, array('price'=>$value));
    		}elseif ($column == 'sort'){
    			$conditions = 'id = "'.$id.'"';
    		}elseif ($column == 'is_sale'){
    			$value = ($value == '否') ? 0 : 1;
    			$conditions = 'id = "'.$id.'"';
    			$this->_mod_Dict->edit($conditions, array('is_display'=>$value));
    		}
    
    
}

?>