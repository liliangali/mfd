<?php
class CfabricApp extends BackendApp
{
	
    private $_mod_Fabs;
    private $_mod_Fabricprice;
    private $_tags;
    function __construct()
    {
         $this->_mod_Fabs = &m("fabs");
         $this->_mod_Fabricprice = &m("fabricprice");
         /* 手动调整成分与前端展示一致切忌 */
						0=>'请选择',
				8030 => array (
						0=>'请选择',
				        128251 => '国产',
				8050 => array (
						0=>'请选择',
				
		$this->_tags=array(
				'0'=>'请选择',
				'校园'=>'校园面料',
				'进口'=>'进口面料',
				'圣诞'=>'圣诞活动',
				'元旦'=>'元旦款',
				'新年'=>'新年款',
				'情人节'=>'情人节',
				'活动'=>'活动款',
				'限量'=>'限量款',
		);
		$this->_mstyle = array (
         parent::__construct();
    }

    function index()
    {
//     	ALTER TABLE `diy_fabric` ADD `is_warning` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否预警' AFTER `tname`, ADD INDEX (`is_warning`) ;
//     	ALTER TABLE `diy_fabric` ADD `sort` INT(3) NOT NULL DEFAULT '0' COMMENT '排序' AFTER `CODE`, ADD `is_sale` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '上下架' AFTER `sort`, ADD INDEX (`sort`, `is_sale`) ;
    	$conditions = $this->_get_query_conditions(array(
    			array(
    					'assoc' => 'OR',           
    	
    	//更新排序
//     	echo "$sort $order";
    	$_GET['CATEGORYID'] = empty($_GET['field_CATEGORYID']) ? '8001' : $_GET['field_CATEGORYID'];
        $page = $this->_get_page(30);
        $f_list = $this->_mod_Fabs->find(array(
        	"conditions" =>'1=1 and CATEGORYID='.$_GET['CATEGORYID'].$conditions,
        	'fields'     => "f.*",
            'limit' => $page['limit'],
            'count' => true,
            'order'         => "$sort $order",
        ));
//        echo '1=1 and CATEGORYID='.$_GET['CATEGORYID'].$conditions;
        //获取 面料价格
        		'index_key' => 'FABRICCODE',
        foreach ($f_list as &$r){
        	$r['rmbprice'] = '0.00';
        	if ($fp_list[$r['CODE']]){
        		$r['rmbprice'] =  $fp_list[$r['CODE']]['RMBPRICE'];
        	}
        }
        
        $page['item_count'] = $this->_mod_Fabs->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('dfpid', $_GET['CATEGORYID']);
        $this->assign("f_list", $f_list);
        $this->assign("c_list",  $this->_composition[$_GET['CATEGORYID']]);
        $this->assign('t_list',$this->_tags);
        $this->assign("s_list",  $this->_mstyle);
        $this->assign("tname", $this->_mod_Fabs->_typeid);
        $this->import_resource(array(
        
        $this->display('cfabric.index.html');
    }

    //异步修改数据
    	
    	$id     = empty($_GET['id']) ? 0 : trim($_GET['id']);
    	if (in_array($column ,array('price', 'sort','d_s','is_sale','is_first','is_warning','activity','activity_price_0003','activity_price_0004','activity_price_0005','activity_price_0006','activity_price_0007','tname','STOCK','VCOMPOSITIONID','tags','mstyle','sales__price_0003','sales__price_0004','sales__price_0005','sales__price_0006','sales__price_0007')))
    		if ($column == 'price'){
    			$conditions = 'FABRICCODE = "'.$id.'" and AREAID =20151';
    			$this->_mod_Fabricprice->edit($conditions, array('RMBPRICE'=>$value));
    		}elseif ($column == 'sort'){
    			$conditions = 'CODE = "'.$id.'"';
    		}elseif ($column == 'is_warning'){
    			$value = (empty($value)) ? 0 : 1;
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('is_warning'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'is_sale'){
    			$value = ($value == '否') ? 0 : 1;
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('is_sale'=>$value));
    		}elseif ($column == 'is_first'){
    			$value = ($value == '否') ? 0 : 1;
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('is_first'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'activity'){
    			$value = ($value == '否') ? 0 : 1;
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('activity'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'd_s'){
    		}elseif (in_array($column, array('activity_price_0003','activity_price_0004','activity_price_0005','activity_price_0006','activity_price_0007','sales__price_0003','sales__price_0004','sales__price_0005','sales__price_0006','sales__price_0007'))){
    			
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array("$column"=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'tname'){
    			
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('tname'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'STOCK'){
    			
    			$conditions = 'CODE = "'.$id.'"';
    			
    			$this->_mod_Fabs->edit($conditions, array('STOCK'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}elseif ($column == 'VCOMPOSITIONID'){ //面料成分
    			$conditions = 'CODE = "'.$id.'"';
    		}elseif ($column == 'mstyle'){//
    			$conditions = 'CODE = "'.$id.'"';
    		}elseif ($column == 'tags'){//
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('tags'=>addslashes($value)));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}
    
    
}

?>