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
         /* 手动调整成分与前端展示一致切忌 */		$this->_composition = array (				8001 => array (
						0=>'请选择',						128251 => '国产',						128099 => '进口', //丝						128100 => '特惠',// 						128101 => '棉',// 						128187 => '涤'				),
				8030 => array (
						0=>'请选择',
				        128251 => '国产',						128412 => '进口',						128413 => '特惠',// 						52794 => '丝麻',//97S3LY // 						12158 => '涤棉',//40C60T// 						52789 => '丝棉'//75%棉25%桑蚕丝				),
				8050 => array (
						0=>'请选择',						128214 => '国产',						128218 => '进口',						200520 => '特惠',//73%棉23.44%尼龙%3.55%氨纶// 						200521 => '毛丝',//72.99%棉23.45%尼龙3.56%氨纶				)		);
				
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
		$this->_mstyle = array (						'bformal' => '正装',						'bcasual' => '休闲',						'bdress' => '礼服'				);
         parent::__construct();
    }

    function index()
    {
//     	ALTER TABLE `diy_fabric` ADD `is_warning` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否预警' AFTER `tname`, ADD INDEX (`is_warning`) ;
//     	ALTER TABLE `diy_fabric` ADD `sort` INT(3) NOT NULL DEFAULT '0' COMMENT '排序' AFTER `CODE`, ADD `is_sale` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '上下架' AFTER `sort`, ADD INDEX (`sort`, `is_sale`) ;
    	$conditions = $this->_get_query_conditions(array(    			array(    					'field' => 'CODE',    					'name'  => 'field_CODE',    					'equal' => 'like',    			),
    			array(    					'field' => 'tname',    					'name'  => 'field_CODE',    					'equal' => 'like',
    					'assoc' => 'OR',               			)    	));
    	
    	//更新排序    	if (isset($_GET['sort']) && isset($_GET['order']))    	{    		$sort  = strtolower(trim($_GET['sort']));    		$order = strtolower(trim($_GET['order']));    		if (!in_array($order,array('asc','desc')))    		{    			$sort  = 'is_first';    			$order = 'desc';    		}    	}    	else    	{    		$sort  = 'sort';    		$order = 'desc';    	}
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
        //获取 面料价格        $ids = array();        $pids = i_array_column($f_list, 'CODE');        $fp_list = $this->_mod_Fabricprice->findAll(array(        		'conditions'    => db_create_in($pids,'FABRICCODE').' and AREAID =20151',    //地区默认 20151
        		'index_key' => 'FABRICCODE',        ));
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
        $this->import_resource(array(        		'script' => 'jqtreetable.js,inline_edit_admin.js',        		'style'  => 'res:style/jqtreetable.css')        );
        
        $this->display('cfabric.index.html');
    }

    //异步修改数据    function ajax_col()    {	
    	
    	$id     = empty($_GET['id']) ? 0 : trim($_GET['id']);    	$column = empty($_GET['column']) ? '' : trim($_GET['column']);    	$value  = isset($_GET['value']) ? trim($_GET['value']) : '';
    	if (in_array($column ,array('price', 'sort','d_s','is_sale','is_first','is_warning','activity','activity_price_0003','activity_price_0004','activity_price_0005','activity_price_0006','activity_price_0007','tname','STOCK','VCOMPOSITIONID','tags','mstyle','sales__price_0003','sales__price_0004','sales__price_0005','sales__price_0006','sales__price_0007')))    	{
    		if ($column == 'price'){
    			$conditions = 'FABRICCODE = "'.$id.'" and AREAID =20151';
    			$this->_mod_Fabricprice->edit($conditions, array('RMBPRICE'=>$value));    			    		if(!$this->_mod_Fabricprice->has_error())    				    		{    				    			echo ecm_json_encode(true);    				    		}
    		}elseif ($column == 'sort'){
    			$conditions = 'CODE = "'.$id.'"';    			$this->_mod_Fabs->edit($conditions, array('sort'=>$value));    			if(!$this->_mod_Fabs->has_error())    			{    				echo ecm_json_encode(true);    			}
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
    			$this->_mod_Fabs->edit($conditions, array('is_sale'=>$value));    			if(!$this->_mod_Fabs->has_error())    			{    				echo ecm_json_encode(true);    			}
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
    		}elseif ($column == 'd_s'){    				$value = in_array($value,array(1,2)) ? $value : 1;    				$conditions = 'CODE = "'.$id.'"';    				$this->_mod_Fabs->edit($conditions, array('d_s'=>$value));    				if(!$this->_mod_Fabs->has_error())    				{    					echo ecm_json_encode(true);    				}
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
    			$conditions = 'CODE = "'.$id.'"';    			$this->_mod_Fabs->edit($conditions, array('VCOMPOSITIONID'=>$value));    			if(!$this->_mod_Fabs->has_error())    			{    				echo ecm_json_encode(true);    			}
    		}elseif ($column == 'mstyle'){//
    			$conditions = 'CODE = "'.$id.'"';    			$this->_mod_Fabs->edit($conditions, array('mstyle'=>$value));    			if(!$this->_mod_Fabs->has_error())    			{    				echo ecm_json_encode(true);    			}
    		}elseif ($column == 'tags'){//
    			$conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('tags'=>addslashes($value)));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}    	}    	else    	{    		return ;    	}    	return ;    }
    
    
}

?>