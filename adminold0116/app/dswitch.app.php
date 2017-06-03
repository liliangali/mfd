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
    	$conditions = $this->_get_query_conditions(array(    			array(    					'field' => 'c_path_name',    					'name'  => 'field_CODE',    					'equal' => 'like',    			),
    			array(    					'field' => 'c_level',     //可搜索字段title    					'equal' => '=',                  //等价关系,可以是LIKE, =, <, >, <>    					'assoc' => 'AND',           //关系类型,可以是AND, OR    					'name'  => 'field_level',         //GET的值的访问键名    					'type'  => 'string',        //GET的值的类型    			),    	));

        $page = $this->_get_page(30);
        $t_list = $this->_mod_Tdict->find(array(
        	"conditions" =>'1=1 '.$conditions,
        	'fields'     => "tdict.*",
            'limit' => $page['limit'],
            'count' => true,
            'order' => "c_sno ASC"
        ));
        //获取 面料价格        $ids = array();        $ids = i_array_column($t_list, 'c_id');        $dicts = $this->_mod_Dict->findAll(array(        		'conditions'    => db_create_in($ids,'id'),        ));
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
        
        $l_list = $this->_mod_Tdict->find(array(        		"conditions" =>'1=1 GROUP BY c_level',        		'fields'     => "c_level"        ));
        $this->assign("level", array_slice($this->_levels,0,count($l_list)));
        $this->import_resource(array(        		'script' => 'jqtreetable.js,inline_edit_admin.js',        		'style'  => 'res:style/jqtreetable.css')        );
        $this->display('diy/dswitch.index.html');
    }

    //异步修改数据    function ajax_col()    {    	$id     = empty($_GET['id']) ? 0 : trim($_GET['id']);    	$column = empty($_GET['column']) ? '' : trim($_GET['column']);    	$value  = isset($_GET['value']) ? trim($_GET['value']) : '';    	if (in_array($column ,array('price', 'sort','is_sale')))    	{
    		if ($column == 'price'){
    			$conditions = 'id = "'.$id.'"';
    			$this->_mod_Dict->edit($conditions, array('price'=>$value));    			    		if(!$this->_mod_Dict->has_error())    				    		{    				    			echo ecm_json_encode(true);    				    		}
    		}elseif ($column == 'sort'){
    			$conditions = 'id = "'.$id.'"';    			$this->_mod_Dict->edit($conditions, array('sort'=>$value));    			if(!$this->_mod_Dict->has_error())    			{    				echo ecm_json_encode(true);    			}
    		}elseif ($column == 'is_sale'){
    			$value = ($value == '否') ? 0 : 1;
    			$conditions = 'id = "'.$id.'"';
    			$this->_mod_Dict->edit($conditions, array('is_display'=>$value));    			if(!$this->_mod_Dict->has_error())    			{    				echo ecm_json_encode(true);    			}
    		}    	}    	else    	{    		return ;    	}    	return ;    }
    
    
}

?>