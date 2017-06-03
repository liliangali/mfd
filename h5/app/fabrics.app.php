<?php

/* 面料*/
class FabricsApp extends MallbaseApp
{
    var $_fabric_mod;
    var $_type_mod;
    function __construct()
    {
        $this->FabricsApp();
    }
    function FabricsApp()
    {
        parent::__construct();
        $this->_fabric_mod =& m('fabric');
    }
    
    function index()
    {
    	$this->assign('title','西服/西装面料、国际高端面料品牌、男士西装面料大全');
        //ns add 
        $this->assign('keywords','西服面料、西装面料、面料品牌、高端面料');
        $this->assign('description','阿里裁缝，我们让客户自主选择喜欢的面料，并用100%正品进行精心制作，我们的产品有新意更有心意！');
        $arg = $this->get_params();
        $page = intval($_POST['page']);
        if($page){
            $arg[0] = $page;
            $this->set_params($arg);
        }

        $attr   = isset($_GET['attr'])   ? trim($_GET['attr']) : '';
        $order  = isset($_GET['order'])  ? trim($_GET['order']) : 'part_id';
        
        $allowOrder = array('order_count', 'part_id', 'click_count');
        
        if(!in_array($order, $allowOrder))
        {
            $order = "part_id";
        }
//         fbInfo();
//         die();
        $partattr_mod = & m("partattribute");
        
        $attrs = $partattr_mod->find(array(
            'conditions' => "type_id = 1",
            'order' => "attr_id ASC",
        ));
        
        foreach($attrs as $key => $val)
        {
            $attrs[$key]['values'] = explode("\r\n", $val['attr_values']);
        }
        
		/*取得供应商等级*/
// 		$args = $this->get_params();
// 		$conditions = '1=1 AND recommended=1 AND state=1 ';
// 		if ($args[0])
// 		{
// 			$grade_id = $args[0];
// 			$conditions .= "AND sgrade=$grade_id";
// 		}
// 		else 
// 		{
// 			$grade_id = 0;
// 		}
// 		$this->assign("sgrade_id",$grade_id);
// 		$sgrade_mod = m("sgrade");
// 		$sgrade_lists = $sgrade_mod->find(array(
// 				'conditions'	=>	"1=1 AND need_confirm = 1 ",
// 				"order"			=> " sort_order ASC"
// 				));
// 		$this->assign("sgrade_lists",$sgrade_lists);
		
// 		$store_mod = & m("store");
// 		$store_list = $store_mod ->find(array(
// 				"conditions"	=> $conditions,
// 				"order"			=> "sort_order asc"
// 				));

// 		foreach ($store_list as $k=>$v)
// 		{
// 			$store_list[$k]['description'] = strip_tags($v['description']);
// 			$store_list[$k]['is_logo'] = 0;
// 			if (!empty($v['store_logo']))
// 			{
				
// 				$store_list[$k]['is_logo'] = 1;
				
// 				if (substr($v['store_logo'],0,4) != 'http')
// 				{
					
// 					$store_list[$k]['store_logo'] = site_url().$v['store_logo'];
// 				}
// 			}
// 		}
		
		$attr_list = array();
		$tmpAttr = explode(".", $attr);
		$tmpAttrArray = array();
		$searchAttr = array();
		foreach($tmpAttr as $key => $val){
		    $tmpV = explode(":", $val);
		    if(isset($tmpV[1])){
		        $searchAttr[$tmpV[0]] = $tmpV[1];
		        $tmpAttrArray[$tmpV[0]] = $tmpV[1];
		    }
		}
		
		foreach($attrs as $key => $val){
		    $attr_list[$key]['name']   = $val['attr_name'];
		    
		    $attr_list[$key]['select'] = !isset($tmpAttrArray[$key]) ? 1 : 0;
		    foreach($val['values'] as $vk => $vv){
		        $tmpAttrStr = $tmpAttrArray;
		        $attr_list[$key]['children'][$vk]['select'] = $tmpAttrStr[$key] == $vv ? 1 : 0;
		        unset($tmpAttrStr[$key]);
		        $ast = '';
		        foreach($tmpAttrStr as $ak => $av){
		            $ast .= ".{$ak}:{$av}";
		        }
		        $attr_list[$key]['children'][$vk]['name'] = $vv;
		        $attr_list[$key]['children'][$vk]['url'] = "?order={$order}&attr={$key}:{$vv}{$ast}";
		        
		    }
		    $all = $ast ? "&attr=".$ast : '';
		    $attr_list[$key]['all']   = "?order={$order}{$all}";
		}
		
		
		$pidsArray = array();
		
		$partAttrModel = &m("partattr");
		$conditions = ' fabric_id !=0 AND state=1 AND is_on_sale=1';
		if(!empty($searchAttr))
		{
		    $attrCond=' 0 ';
		    $cNum = 0;
		    foreach($searchAttr as $key => $val)
		    {
		        $cNum +=1;
		        $attrCond .= " OR (attr_id='{$key}' AND attr_value = '{$val}')";
		    }
		    
		    $attrCond .= " GROUP BY part_id HAVING num = '{$cNum}'";
		    
		    $goodsids = $partAttrModel->find(array(
		        'conditions' => $attrCond,
		        'fields' => "part_id, count(1) AS num",
		    ));
		
		    foreach($goodsids as $key => $val)
		    {
		        $pidsArray[] = $val['part_id'];
		    }
		
		    if(!empty($pidsArray))
		    {
		        $conditions .= " AND part_id ".db_create_in($pidsArray);
		    }else{
		        $conditions .= " AND part_id IN (0)";
		    }
		
		}
		$part_mod = & m("part");
		
        $page = $this->_get_page(20);
    	
    	$fabric = $part_mod->find(array(
    			'conditions' => $conditions,
    			'limit' => $page['limit'],
    			'fields' => '*',
    			'order' => "$order DESC",
    			'count'	=>	"true",
    			
    	));
    	
    	foreach ($fabric as $k=>$v) {
    		if (!empty($v['part_thumb'])) {
    			$fabric[$k]['part_thumb'] = $v['part_thumb'];
    		}
    	}

    	$page['item_count'] = $part_mod->getCount();
    	
    	$this->_format_page($page);
    	$urlFormat = "?order={$order}&attr={$attr}";
    	$this->assign('page_info', $page);
    	
		$this->assign("attrs", $attr_list);
		$this->assign("gattr", $attr);
		$this->assign("fabric_list", $fabric);
		$this->assign("url", $urlFormat);
		$this->assign("order", $order);
		$this->assign("store_list",$store_list);
        $this->assign('current', 'fabric');
        $this->_config_seo(array(
            'title' => '选择您喜欢的面料进行定制',
            'description' => Conf::get('site_description'),
            'keywords' => Conf::get('site_keywords')
        ));
    	$this->display('fabric_index.html');
    }

    function lists()
    {
    	$args = $this->get_params();
    	if ($args[1])
    	{
    		$brand_id = $args[1];
    		$brand_mod = & m("brand");
    		$brand_info = $brand_mod->get_info($brand_id);
    		$brand_name = $brand_info['brand_name'];
    	}
    	else
    	{
    		$this->show_message("请先选择品牌");
    		exit();
    	}
    	
    	if ($args[2])
    	{
    		$cate_id = $args[2];
    	} 
    	else
    	{
    		$cate_id = 0;
    	}
    	//取得组件分类
    	$part_mod = & m("part");
    	$glist = $part_mod->get_cate();
    	
    	//更新排序
    	if (isset($_GET['sort']) && !empty($_GET['order']))
    	{
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc')))
    		{
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}
    	else
    	{
    		if (isset($_GET['sort']) && empty($_GET['order']))
    		{
    			$sort  = strtolower(trim($_GET['sort']));
    			$order = "";
    		}
    		else
    		{
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}
    	if ($cate_id == 0)
    	{
    		$conditions = "1=1 and is_fabric=1 and brand_id=$brand_id";
    	}
    	else
    	{
    		$conditions = "1=1 and is_fabric=1 and brand_id=$brand_id and cate_id=$cate_id ";
    	}
    	
    	$page = $this->_get_page(12);
    	
    	$fabric = $part_mod->find(array(
    			'conditions' => $conditions,
    			'limit' => $page['limit'],
    			'fields' => '*',
    			'order' => "$sort $order",
    			'count'	=>	"true",
    			
    	));
    //echo "<pre>";var_dump($fabric);exit;
    	foreach ($fabric as $k=>$v) {
    		if (!empty($v['part_thumb'])) {
    			$fabric[$k]['part_thumb'] = $v['part_thumb'];
    		}
    	}
    	$customs_part_mod = & m("customsparts");
    	$arr = $fabric;
    	foreach ($arr as $k=>$v)
    	{
    		foreach ($v as $k1=>$v1)
    		{
    			$tmp = $customs_part_mod->find(array(
    					'conditions' => "cst_pt.pt_id=$k",
    					'join'		 => "belongs_to_customs",
    					'fields'	 => "cst.cst_name,cst.cst_image",
    					));
    			//格式化基本款缩略图
    			foreach ($tmp as $k3=>$v3) {
    				if (!empty($v3['cst_image'])) {
    					$tmp[$k3]['cst_image'] = $v3['cst_image'];
    				}
    			}
    			
    			$fabric[$k]['customs'] = $tmp;
    		}
    	}
//  echo "<pre>";print_r($fabric);exit;
    	$page['item_count'] = $part_mod->getCount();
    	$this->assign("brand_id",$brand_id);
    	$this->assign("cate_id",$cate_id);
    	$this->assign("glist",$glist);
    	$this->assign("brand_name",$brand_name);
    	$this->assign('fabrics', $fabric);
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	
    	//var_dump($page);
	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'mlselection.js'
    	));
    	
        $this->display('fabric_list.html');
    }
    
    function info()
    {
    	$args = $this->get_params();
    	if(!isset($args[0])){
    	    $this->show_warning('非法操作');
    	    return;
    	}
    	
		$part_mod = & m("part");
		$conditions = " part_id = '{$args[0]}' AND fabric_id !=0 AND state=1";
		$fabric=$part_mod->get(array(
				'conditions'	=> $conditions,
				'join'			=> "belongs_to_brand",
				'fields'		=> "p.*,b.brand_name,b.brand_web",
		));
		
		$part_mod->setInc("part_id='{$args[0]}'", 'click_count');
		
		if(empty($fabric)){
		    $this->show_warning('该面料不存在');
		    return;
		}
		
		if($fabric['is_on_sale'] != 1){
		    $this->show_warning('该面料已下架');
		    return;
		}
		
		$partAttrModel = &m("partattr");
		
		$attrs = $partAttrModel->find(array(
		    'conditions' => "part_id = '{$args[0]}'",
		    'join'       => 'belongs_to_partattr'
		));
		
		$firstAttr = array_pop($attrs);
		
		$this->assign("firstAttr", $firstAttr);
		
		$this->assign("attrs", $attrs);
		
		
		//print_R($firstAttr);
		
// 		$customs_part_mod = & m("customsparts");
		
// 		$customs = $customs_part_mod->find(array(
// 					'conditions' => "cst_pt.pt_id={$fabric['part_id']}",
// 					'join'		 => "belongs_to_customs",
// 					'fields'	 => "cst.cst_name,cst.cst_image,cst.cst_price",
// 		));
// 			//格式化基本款缩略图
// 		foreach ($customs as $k=>$v) {
// 			if (!empty($v['cst_image'])) {
// 				$customs[$k]['cst_image'] = $v['cst_image'];
// 			}
// 		}
// 		$this->assign("customs",$customs);

		$part_mod = & m("part");

		$maybe = $part_mod->find(array(
		    'conditions' => "fabric_id !=0 AND state=1 AND part_id != '{$fabric['part_id']}'",
		    'limit' => 15,
		    'order' => 'part_id DESC',
		));
		
		$num = 0;
		$group =1;
		$mlist = array();
		foreach($maybe as $key => $val){
		    $num ++;
		    $mlist[$group][] =$val;
		    if($num % 5 == 0){
		        $group ++;
		    }
		}
		
		$links = array();
		if($fabric["link_cst"]){
		    $custom = &m("customs");
		    $links = $custom->find(array(
		        'conditions' => "cst_id IN ({$fabric["link_cst"]})",
		    ));
		}
		$this->_config_seo(array(
		    'title' => $fabric['part_name'],
		    'description' => Conf::get('site_description'),
		    'keywords' => Conf::get('site_keywords')
		));
		$this->assign('links', $links);
		$this->assign('fabric', $fabric);
		$this->assign('mlist', $mlist);
		$this->assign('current', 'fabric');
    	$this->display('fabric.info.html');
    }
    
    function getcustoms()
    {
    	$customs_mod = & m("customs");
    	$customes_list = $customs_mod->find(array(
    			"conditions"	=>	"1=1 AND is_active=1",
    			"order"			=> "add_time desc,cst_id desc",
    			));
    	//dump($customes_list);
    }

    
}

?>
