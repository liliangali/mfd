<?php
/**
 *    组件管理控制器
 *    @author liliang
 */
define('MAX_LAYER', 4);
class PartApp extends BackendApp
{
    var $_part_mod;
	var $part_type;
    function __construct()
    {
    	define("PART", "part/");
        $this->PartApp();
    }
    function PartApp()
    {
        parent::BackendApp();

        $this->_part_mod =& m('part');
    }
	
    /**
     * 面料列表
     */
    function index() {
		
    	$this->import_resource(array('script' => 'inline_edit_admin.js'));
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'part_name',
    			'equal' => 'LIKE',
    			'assoc' => 'AND',
    			'name'  => 'part_name',
    			'type'  => 'string',
    	),
    	
    	));
    	
    	/* $tmp_cate_id = array_keys($this->_get_options());
    	$cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : $tmp_cate_id[0];
    	$cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : 0;
		if ($cate_id)
		{
			$conditions .= " AND cate_id=$cate_id ";
			$this->assign("cate_id",$cate_id);
		}
		*/
		$store_id = isset($_GET['store_id']) ? $_GET['store_id']:0;
		if ($store_id)
		{
			$conditions .= " AND store_id=$store_id";
			$this->assign("store_id",$store_id);
		} 
		
    	/*更新排序*/
    	if (isset($_GET['sort']) && isset($_GET['order'])){
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc'))){
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}else{
    		$sort  = 'part_id';
    		$order = 'desc';
    	}
    	
    	$page = $this->_get_page(15);
    	$part = $this->_part_mod->find(array(
    			'conditions' => "1=1 and fabric_id !=0 and state=1".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    			));
    	$page['item_count']=$this->_part_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	$this->_format_page($page);
    	$this->assign("page_info",$page);
    	$this->assign("cate_id",$cate_id);
    	$this->assign("parents",$this->_get_options());
    	$this->assign("parts",$part);		
		$this->assign("site_url",SITE_URL);
    	$this->assign("store_list",$this->_part_mod->get_store());
    	$this->display(PART."part.index.html");
    }
    
    /**
     * 里料列表
     */
    function partlining() {
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'part_name',
    			'equal' => '=',
    			'assoc' => 'AND',
    			'name'  => 'part_name',
    			'type'  => 'string',
    	),
    	));
    	
    	$tmp_cate_id = array_keys($this->_get_options());
    	$cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : $tmp_cate_id[0];
    	if ($cate_id)
    	{
    		$conditions .= " AND cate_id=$cate_id ";
    		$this->assign("cate_id",$cate_id);
    	}
    	
    	$store_id = isset($_GET['store_id']) ? $_GET['store_id']:0;
    	if ($store_id)
    	{
    		$conditions .= " AND store_id=$store_id";
    		$this->assign("store_id",$store_id);
    	}
    	
    	if (isset($_GET['sort']) && isset($_GET['order'])){
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc'))){
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}else{
    		$sort  = 'part_id';
    		$order = 'desc';
    	}
    	
    	$page = $this->_get_page(15);
    	$part = $this->_part_mod->find(array(
    			'conditions' => "1=1 and is_mat=2 and state=1".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    	));
    	$page['item_count']=$this->_part_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	$this->_format_page($page);
    	$this->assign("parts",$part);
    	$this->assign("cate_id",$cate_id);
    	$this->assign("parents",$this->_get_options());
    	$this->assign("page_info",$page);
    	$this->assign("store_list",$this->_part_mod->get_store());
    	$this->display(PART."partlining.index.html");
    }
    
    /**
     * 配搭列表
     */
    function partmat() {
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'part_name',
    			'equal' => 'LIKE',
    			'assoc' => 'AND',
    			'name'  => 'part_name',
    			'type'  => 'string',
    	),
    	));
    	
    	/* $tmp_cate_id = array_keys($this->_get_options());
    	$cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : 0;
    	if ($cate_id)
    	{
    		$conditions .= " AND cate_id=$cate_id ";
    		$this->assign("cate_id",$cate_id);
    	}
    	
    	$store_id = isset($_GET['store_id']) ? $_GET['store_id']:0;
    	if ($store_id)
    	{
    		$conditions .= " AND store_id=$store_id";
    		$this->assign("store_id",$store_id);
    	} */
    	
    	if (isset($_GET['sort']) && isset($_GET['order'])){
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc'))){
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}else{
    		$sort  = 'part_id';
    		$order = 'desc';
    	}
    	$page = $this->_get_page(15);
    	$part = $this->_part_mod->find(array(
    			'conditions' => "1=1 and fabric_id = 0 and state=1".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    	));
    	$page['item_count']=$this->_part_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	
    	$cs =& cs();
    	$gcategories = $cs->_get_gcategory();
    	
    	$tree = $cs->_tree($gcategories);
    	//递归整理 所属类别
    	if ($part){
    		foreach ($part as $k=>$v){
    			$attribution = '';
    			$gdata = array();
    			$gdata = $tree->getParents($v['cate_id']);
    			@array_push($gdata,$v['cate_id']);
    			$attribution = $this->_format_c($gdata,$gcategories);
    			$part[$k]['attribution'] = $attribution;
    		}
    	}
    	
    	
    	$this->_format_page($page);
    	$this->assign("parts",$part);
    	$this->assign("cate_id",$cate_id);
    	$this->assign("parents",$this->_get_options());
    	$this->assign("page_info",$page);
    	$this->assign("store_list",$this->_part_mod->get_store());
    	$this->display(PART."partmat.index.html");
    }
    function _format_c($data,$gcategory,$style=0){
    	$str = '';
    	if ($data){
    		foreach ($data as $v){
    			if ($v){
    				if ($style == 1){
    					if ($v){
    						$strs[] = array('cid'=>$gcategory[$v]['cate_id'],'cname'=>$gcategory[$v]['cate_name']);
    					}
    				}else {
    					$str .=$gcategory[$v]['cate_name']."-";
    					$strs = substr($str,0,-1);
    				}
    				
    			}
    		}
    	}
    	return $strs;
    }
    /**
     * 款式风格列表
     */
    function partstyle()
    {
    	$conditions = $this->_get_query_conditions(array(array(
    			'field' => 'part_name',
    			'equal' => 'LIKE',
    			'assoc' => 'AND',
    			'name'  => 'part_name',
    			'type'  => 'string',
    	),
    	));
    	
    	$tmp_cate_id = array_keys($this->_get_options());
    	$cate_id = isset($_REQUEST['cate_id']) ? $_REQUEST['cate_id'] : $tmp_cate_id[0];
    	if ($cate_id)
    	{
    		$conditions .= " AND cate_id=$cate_id";
    		$this->assign("cate_id",$cate_id);
    	}
    	
    	/*更新排序*/
    	if (isset($_GET['sort']) && isset($_GET['order'])){
    		$sort  = strtolower(trim($_GET['sort']));
    		$order = strtolower(trim($_GET['order']));
    		if (!in_array($order,array('asc','desc'))){
    			$sort  = 'part_id';
    			$order = 'desc';
    		}
    	}else{
    		$sort  = 'part_id';
    		$order = 'desc';
    	}
    	
    	$page = $this->_get_page(15);
    	$part = $this->_part_mod->find(array(
    			'conditions' => "1=1 and is_mat=4 ".$conditions,
    			'limit'      => $page['limit'],
    			'order'      => "$sort $order",
    			'count'      => true,
    			));
    	$page['item_count']=$this->_part_mod->getCount();
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	
    	$this->_format_page($page);
    	$this->assign("parts",$part);
    	$this->assign("cate_id",$cate_id);
    	$this->assign("parents",$this->_get_options());
    	$this->assign("page_info",$page);
    	$this->display(PART."partstyle.index.html");
    }
    

    /**
     * 添加面料
     * 
     */
    function add()
    {
		
    	if (!IS_POST)
    	{
		    $part_brand_mod = & m("brand");
		    $brands = $part_brand_mod->find(array(
		    			"conditions" => "1=1",
		    			"order"      => 'sort_order',
		    			"fields"     => 'brand_id,brand_name'
		    		));
		    $tmp = array();
		    foreach ($brands as $v) {
		    	$brands[$v['brand_id']] = $v['brand_name'];
		    }
		    $this->import_resource(array(
		    		'script' => 'jquery.plugins/jquery.validate.js'
		    ));
		    $this->assign('build_editor', $this->_build_editor(array(
		    		'name' => 'content',
		    		'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
		    )));
		    $this->assign("brands",$brands);
    		$this->assign("parents",$this->_get_options());
    		$this->display(PART.'part.form.html');
    	} 
    	else 
    	{
    		$data['part_name'] = $_POST['goods_name'];
    		$data['code']   = $_POST['code'];
    		$data['cate_id']  = intval($_POST['cat_id']);
    		$data['brand_id']  = intval($_POST['brand_id']);
    		$data['price']     = intval($_POST['price']);
    		$data['maket_price']     = intval($_POST['maket_price']);
    		$data['cost_price']     = intval($_POST['cost_price']);
    		$data['part_number'] = intval($_POST['goods_number']);
    		$data['warn_number'] = intval($_POST['warn_number']);
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['material'] = $_POST['material'];
    		$data['flower'] = $_POST['flower'];
    		$data['place'] = $_POST['place'];
    		$data['fabric_style'] = $_POST['fabric_style'];
    		$data['washing_instruct'] = $_POST['washing_instruct'];
    		$data['design_concept'] = $_POST['design_concept'];
    		$data['is_new']     = isset($_POST['is_new']) ? 1:0;
    		$data['is_hot']     = isset($_POST['is_hot']) ? 1:0;
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['zujian_brief']= $_POST['goods_brief'];
    		$data['type_id']	= intval($_POST['goods_type']);
    		$data['part_small'] = trim($_POST['part_small']);
    		$data['part_img']	= trim($_POST['part_img']);
    		$data['part_thumb'] = trim($_POST['part_thumb']);
    		$data['fabric']		= 1;
    		if (!$part_id = $this->_part_mod->add($data)) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    		
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) 
    		{
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    					$part_attr_mod->add(array(
    							'part_id' => $part_id,
    							'attr_id' => $_POST['attr_id_list'][$k],
    							'attr_value' => $attr_value,
    							));
    			}
    		}
    		
    		
    		
    		$this->show_message('add_zujian_successed',
    				'back_list', 'index.php?app=part&amp;act=index&amp;cate_id='.$data['cate_id']
    		);
    	}
    }
    
    function _basic_category(){	
    	$regions = Constants::$categoriesNameParent;
    	$this->assign('gcategorys', $regions);
    }
    
    /**
     * 添加里料
     *
     */
    function addlining()
    {
    
    	if (!IS_POST)
    	{
    		$tmp = array();
    		foreach ($brands as $v) {
    			$brands[$v['brand_id']] = $v['brand_name'];
    		}
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign('build_editor', $this->_build_editor(array(
    				'name' => 'content',
    				'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
    		)));
    		$this->assign("brands",$brands);
    		$this->assign("parents",$this->_get_options());
    		$this->assign("zujiantype",$this->part_type);
    		$this->display(PART.'partlining.form.html');
    	}
    	else
    	{
    		
    		$data['part_name'] = $_POST['goods_name'];
    		$data['part_sn']   = $_POST['goods_sn'];
    		$data['cate_id']  = intval($_POST['cat_id']);
    		$data['brand_id']  = intval($_POST['brand_id']);
    		$data['price']     = intval($_POST['price']);
    		$data['maket_price']     = intval($_POST['maket_price']);
    		$data['cost_price']     = intval($_POST['cost_price']);
    		$data['part_number'] = intval($_POST['goods_number']);
    		$data['warn_number'] = intval($_POST['warn_number']);
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['material'] = $_POST['material'];
    		$data['flower'] = $_POST['flower'];
    		$data['place'] = $_POST['place'];
    		$data['fabric_style'] = $_POST['fabric_style'];
    		$data['washing_instruct'] = $_POST['washing_instruct'];
    		$data['design_concept'] = $_POST['design_concept'];
    		$data['is_new']     = isset($_POST['is_new']) ? 1:0;
    		$data['is_hot']     = isset($_POST['is_hot']) ? 1:0;
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['zujian_brief']= $_POST['goods_brief'];
    		$data['type_id']	= intval($_POST['goods_type']);
    		$data['part_small'] = trim($_POST['part_small']);
    		$data['part_img']	= trim($_POST['part_img']);
    		$data['part_thumb'] = trim($_POST['part_thumb']);
    		$data['is_lining']		= 1;
    		if (!$part_id = $this->_part_mod->add($data)) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    				$part_attr_mod->add(array(
    						'part_id' => $part_id,
    						'attr_id' => $_POST['attr_id_list'][$k],
    						'attr_value' => $attr_value,
    				));
    			}
    		}
    
    		$this->show_message('add_zujian_successed',
    				'back_list', 'index.php?app=part&amp;act=partlining&amp;cate_id='.$data['cate_id']
    		);
    	}
    }
    
    /**
     * 添加配搭
     *
     */
    function addmat()
    {
    	if (!IS_POST)
    	{
    		$tmp = array();
    		

    		$this->_basic_category();
    		
    		
    		$partcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
    		));
    	
    		$this->assign('build_editor', $this->_build_editor(array(
    				'name' => 'content',
    				'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
    		)));
    		$this->assign("parents",$this->_get_options());
    		$this->assign("zujiantype",$this->part_type);
    		$this->display(PART.'partmat.form.html');
    	}
    	else
    	{
    		$data['part_id'] = $_POST['part_id'];
    		$data['part_name'] = $_POST['part_name'];
    		$data['ecode'] = $_POST['ecode'];
    		$data['code'] = $_POST['part_sn'];
    		$data['part_sn']   = $_POST['part_sn'];
    		$data['cate_id']   = intval($_POST['gcategory_id']);
    		$data['price']     = intval($_POST['price']);
    		$data['maket_price']     = intval($_POST['maket_price']);
    		$data['cost_price']     = intval($_POST['cost_price']);
    		$data['part_number'] = intval($_POST['goods_number']);
    		$data['warn_number'] = intval($_POST['warn_number']);
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['type_id']	= intval($_POST['goods_type']);
    		$data['part_small'] = trim($_POST['part_img']);
    		$data['part_img'] = trim($_POST['part_img']);
    		$data['is_mat']		= 1;
    		$data['state']		= 1;				//默认审核
    		
    		if (!$part_id = $this->_part_mod->add($data)) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    
    
    		$this->show_message('add_zujian_successed',
    				'back_list', 'index.php?app=part&amp;act=partmat&amp;cate_id='.$data['cate_id']
    		);
    	}
    }
    
    /**
     * 添加款式风格
     *
     */
    function addstyle()
    {
    
    	if (!IS_POST)
    	{
    
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign('build_editor', $this->_build_editor(array(
    				'name' => 'content',
    				'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
    		)));
    		$this->assign("parents",$this->_get_options());
    		
    		$this->display(PART.'partstyle.form.html');
    	}
    	else
    	{
    		$data['part_name'] = $_POST['goods_name'];
    		$data['part_sn']   = $_POST['goods_sn'];
    		$data['cate_id']  = intval($_POST['cat_id']);
    		$data['brand_id']  = intval($_POST['brand_id']);
    		$data['price']     = intval($_POST['price']);
    		$data['maket_price']     = intval($_POST['maket_price']);
    		$data['cost_price']     = intval($_POST['cost_price']);
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['type_id']	= intval($_POST['goods_type']);
    		$data['part_small'] = trim($_POST['part_small']);
    		$data['is_mat']     = 4;
    		if (!$part_id = $this->_part_mod->add($data)) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    				$part_attr_mod->add(array(
    						'part_id' => $part_id,
    						'attr_id' => $_POST['attr_id_list'][$k],
    						'attr_value' => $attr_value,
    				));
    			}
    		}
    
    
    
    		$this->show_message('add_zujian_successed',
    				'back_list', 'index.php?app=part&amp;act=partstyle&amp;cate_id='.$data['cate_id']
    		);
    	}
    }
    
    
    /**
     * 修改面料
     * Modify By Ruesin 2014-07-29 10:48:22
     */
    function edit(){
    	if (!IS_POST){
    		$part_id = empty($_GET['id']) ? 0 :intval($_GET['id']);
    		
    		$part_info = $this->_part_mod->get_info($part_id);

    		if (substr($part_info['part_img'],0,4) != "http"){
    			$part_info['part_img'] = SITE_URL.'/'.$part_info['part_img'];
    		}
    		
    		$cate_id = $part_info['cate_id'];
    		$partcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		//$this->assign("parents",$this->_get_options());
    		//========Ruesin
    		$res = array('8001'=>'面料');
    		$this->assign("parents",$res);
    		$stores  =$this->_part_mod->get_store();
    		$this->assign("stores",$stores);
    		//========Ruesin
    		
    		$this->assign('build_editor', $this->_build_editor(array(
    		    'name' => 'content',
    		    'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
    		)));
    		$this->assign("part",$part_info);
    		$this->assign("goods_attr_html",$this->build_attr_html($part_info['type_id'],$part_info['part_id']));
    		$this->assign("zujiantype",$this->_get_options1($cate_id));
    		$this->display(PART.'part.edit.html');
    	}else{
    	    
    		$data['part_name']       = $_POST['goods_name'];
    		$data['code']            = $_POST['code'];
    		$data['goods_sn']        = $_POST['code'];
    		//$cate_id = intval($_POST['cat_id']);
    		$data['cate_id']		= intval($_POST['cate_id']);
		  	$data['state']           = 1;
    		$data['weight']		     = intval($_POST['weight']);
    		$data['shazhi']		     = intval($_POST['shazhi']);
    		$data['price']           = $_POST['price'];
    		$data['maket_price']     = $_POST['maket_price'];
    		$data['cost_price']      = $_POST['cost_price'];
    		$data['part_number']     = $_POST['goods_number'];
    		$data['warn_number']     = $_POST['warn_number'];
    		$data['sort_order']      = intval($_POST['sort_order']);
    		$data['type_id']	     = intval($_POST['goods_type']);
    		$data['part_small']	     = trim($_POST['part_img']);
			$data['part_img']	     = trim($_POST['part_img']);
            $data['zujian_brief']     = trim($_POST['goods_brief']);
    		$data['link_cst']        = trim($_POST['link_cst']);
    		$data['keywords']        = trim($_POST['keywords']);
    		$data['content']        = trim($_POST['content']);
            $data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
            $data['store_id']     = $_POST['store_id'];
    		$part_id                 = $_POST['part_id'];
    		$goods_id                = intval($_POST['goods_id']);
    		$gdata = array(
    		    'store_id'     => $_POST['store_id'],
    		    'type'         => $_POST['goods_type'],
    		    'goods_name'   => $_POST['goods_name'],
    		    'cate_id'      => $_POST['cate_id'],
    		    'last_update'  => time(),
    		    'default_image'=> $_POST['part_img'],
    		    'cate_id_1'    => $_POST['cate_id'],
    		    'price'        => $_POST['price'],
    		    'cost_price'   => $_POST['cost_price'],
    		    'fabric_number'=> $_POST['goods_sn'],
    		    'is_mat'       => 1,
    		    'state'        => 1,    //审核通过
    		);

    		if($goods_id<=0){  //add
		        $goods_mod = m('goods');
		        $goods_id = $goods_mod->add($gdata);
		        
		        $data['goods_id'] = $goods_id;
		        
    		}else{
    		    $goods_mod = m('goods');
    		    $goods_mod->edit("goods_id=".$goods_id,$gdata);
    		}
    		
    		if ($this->_part_mod->edit("part_id=".$part_id,$data) ===false) {
    		    $this->show_warning($this->_part_mod->get_error());
    		    return;
    		}
    		
    		

			/*添加属性之前先删除*/
			$part_attr_mod = & m("partattr");
			$part_attr_mod->drop("part_id=".$part_id);
    			
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    				
    					$part_attr_mod->add(array(
    							'part_id' => $part_id,
    							'attr_id' => $_POST['attr_id_list'][$k],
    							'attr_value' => $attr_value,
    					));
    				
    			}
    		}
    		
    		 
    		$this->show_message('edit_zujian_successed',
    				'back_list', 'index.php?app=part&act=index&amp;cate_id='.$cate_id
    		);
    	}
    }
    /**
     * Ajax 获取面料关联基本款信息
     * @param $ids 基本款ids字符串
     * @return 
     * @author Ruesin
     */
    function ajax_customs_info(){
        $custom =& m('customs');
        $ids=$_GET['ids'];
        $pid = intval($_GET['pid']);
        $cData = $custom -> findAll("cst_id IN ({$ids})");
        $this->assign('goods_list',$cData);
        $this->display(PART.'part.fratc.html');
    }
    
    /**
     * 修改里料
     *
     */
    function editlining()
    {
    	if (!IS_POST)
    	{
    		$part_id = empty($_GET['id']) ? 0 :intval($_GET['id']);
    
    		$part_info = $this->_part_mod->get_info($part_id);
    		if (substr($part_info['part_img'],0,4) != "http")
    		{
    			$part_info['part_img'] = SITE_URL.'/'.$part_info['part_img'];
    		}
    		$cate_id = $part_info['cate_id'];
    
    		$partcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign("parents",$this->_get_options());
    		$this->assign("part",$part_info);
    		$this->assign("goods_attr_html",$this->build_attr_html($part_info['type_id'],$part_info['part_id']));
    		$this->assign("zujiantype",$this->_get_options1($cate_id));
    		$this->display(PART.'partlining.edit.html');
    	}
    	else
    	{
    		$data['part_name'] = $_POST['goods_name'];
    		$data['part_sn']	= $_POST['part_sn'];
    		$data['goods_sn']   = $_POST['goods_sn'];
    		//$cate_id = intval($_POST['cat_id']);
    		$data['weight']		= intval($_POST['weight']);
    		$data['shazhi']		= intval($_POST['shazhi']);
        	//$data['cate_id']		= intval($_POST['cat_id']); 
		  	$data['state'] = 1;
    		$data['price']       = intval($_POST['price']);
    		$data['maket_price']       = intval($_POST['maket_price']);
    		$data['cost_price']       = intval($_POST['cost_price']);
    		$data['part_number'] = intval($_POST['goods_number']);
    		$data['warn_number']  = intval($_POST['warn_number']);
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['type_id']	= intval($_POST['goods_type']);
    		$data['part_img']	= trim($_POST['part_img']);
    		$part_id = $_POST['part_id'];
    
    		$part_type = intval($_POST['goods_type']);
    		if ($this->_part_mod->edit("part_id=".$part_id,$data) ===false) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    
    		/*添加属性之前先删除*/
    		$part_attr_mod = & m("partattr");
    		$part_attr_mod->drop("part_id=".$part_id);
    		 
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    
    				$part_attr_mod->add(array(
    						'part_id' => $part_id,
    						'attr_id' => $_POST['attr_id_list'][$k],
    						'attr_value' => $attr_value,
    				));
    
    			}
    		}
    
    		 
    		$this->show_message('edit_zujian_successed',
    				'back_list', 'index.php?app=part&act=partlining&amp;cate_id='.$cate_id
    		);
    	}
    }
    
    /**
     * 修改配搭
     *
     */
    function editmat()
    {
    	if (!IS_POST)
    	{
    		$part_id = empty($_GET['id']) ? 0 :intval($_GET['id']);
    
    		
    		$part_info = $this->_part_mod->get_info($part_id);
    		if (substr($part_info['part_img'],0,4) != "http")
    		{
    			$part_info['part_img'] = SITE_URL.'/'.$part_info['part_img'];
    		}
    		$cate_id = $part_info['cate_id'];
    		
    		
    		
    		
    		$this->_basic_category();
    		
    		
    		
    		$cs =& cs();
    		$gcategories = $cs->_get_gcategory();
    		 
    		$tree = $cs->_tree($gcategories);
    		//递归整理 所属类别
    		$attribution = '';
    		$gdata = array();
    		$gdata = $tree->getParents($cate_id);
    		$_regions = $this->_format_c($gdata,$gcategories,1);
    		$this->assign("_regions", $_regions);
    		
    		
    		
    		
    		$partcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
    		));
    		$this->assign("parents",$this->_get_options());
    		$this->assign("part",$part_info);
    		$this->assign("goods_attr_html",$this->build_attr_html($part_info['type_id'],$part_info['part_id']));
    		$this->assign("zujiantype",$this->_get_options1($cate_id));
    		$this->display(PART.'partmat.edit.html');
    	}
    	else
    	{
    		
    		$data['part_id'] = $_POST['def_part_id'];
    		$data['part_name'] = $_POST['goods_name'];
    		$data['part_sn']	= $_POST['part_sn'];
    		$data['goods_sn']	= $_POST['goods_sn'];
    		$data['ecode']	= $_POST['ecode'];
    		$data['weight']		= intval($_POST['weight']);
    		$data['shazhi']		= intval($_POST['shazhi']);
  			$data['state'] = 1;

    		$data['price']       = intval($_POST['price']);
    		$data['maket_price']       = intval($_POST['maket_price']);
    		$data['cost_price']       = intval($_POST['cost_price']);
    		$data['part_number'] = intval($_POST['goods_number']);
    		$data['warn_number'] = intval($_POST['warn_number']);
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['part_small'] = trim($_POST['part_small']);
    		$part_id = $_POST['part_id'];
    
    		$part_type = intval($_POST['goods_type']);
    		if ($this->_part_mod->edit("part_id=".$part_id,$data) ===false) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    		
    		/*如果组件对应有供应商商品 再把part_price存回去 */
    		$part_info = $this->_part_mod->get_info($part_id);
    		$goods_id = $part_info['goods_id'];
    		$price  = intval($_POST['price']);
    		if ($goods_id)
    		{
    			$goods_mod = m('goods');
    			$goods_mod->edit("goods_id=".$goods_id,array('part_price'=>$price));
    		}
    		
    		/*添加属性之前先删除*/
    		$part_attr_mod = & m("partattr");
    		$part_attr_mod->drop("part_id=".$part_id);
    		 
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    
    				$part_attr_mod->add(array(
    						'part_id' => $part_id,
    						'attr_id' => $_POST['attr_id_list'][$k],
    						'attr_value' => $attr_value,
    				));
    
    			}
    		}
    
    		 
    		$this->show_message('edit_zujian_successed',
    				'back_list', 'index.php?app=part&act=partmat'
    		);
    	}
    }
    
    /**
     * 修改款式风格
     *
     */
    function editstyle()
    {
    
    	if (!IS_POST)
    	{
    		$part_id = empty($_GET['id']) ? 0 :intval($_GET['id']);
    
    		$part_info = $this->_part_mod->get_info($part_id);
    		$cate_id = $part_info['cate_id'];
    
    		$partcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign("parents",$this->_get_options());
    		$this->assign("part",$part_info);
    		$this->assign("goods_attr_html",$this->build_attr_html($part_info['type_id'],$part_info['part_id']));
    		$this->assign("zujiantype",$this->_get_options1($cate_id));
    		$this->display(PART.'partstyle.edit.html');
    	}
    	else
    	{
    		$data['part_name'] = $_POST['goods_name'];
    		$data['part_sn']   = $_POST['part_sn'];
    		$data['cate_id']     = intval($_POST['cat_id']);
		  	$data['state'] = 1;

    		$cate_id     = intval($_POST['cat_id']);
    		$data['price']       = intval($_POST['price']);
    		$data['maket_price']       = intval($_POST['maket_price']);
    		$data['cost_price']       = intval($_POST['cost_price']);
    		$data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1:0;
    		$data['sort_order'] = intval($_POST['sort_order']);
    		$data['part_small'] = trim($_POST['part_small']);
    		$part_id = $_POST['part_id'];
    		
    		$part_type = intval($_POST['goods_type']);
    		if ($this->_part_mod->edit("part_id=".$part_id,$data) ===false) {
    			$this->show_warning($this->_part_mod->get_error());
    			return;
    		}
    
    		/*添加属性之前先删除*/
    		$part_attr_mod = & m("partattr");
    		$part_attr_mod->drop("part_id=".$part_id);
    		 
    		/*添加属性*/
    		if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (!empty($_POST['attr_id_list']) && !empty($_POST['attr_value_list']))) {
    			$part_attr_mod = & m("partattr");
    			foreach ($_POST['attr_value_list'] as $k=>$attr_value) {
    
    				$part_attr_mod->add(array(
    						'part_id' => $part_id,
    						'attr_id' => $_POST['attr_id_list'][$k],
    						'attr_value' => $attr_value,
    				));
    
    			}
    		}
    
    		 
    		$this->show_message('edit_zujian_successed',
    				'back_list', 'index.php?app=part&act=partstyle&amp;cate_id='.$cate_id
    		);
    	}
    }
  
    /**
     * ajax根据分类id获得对应的类型id
     */
    function get_type(){
    	$cate_id   = intval($_POST['cate_id']);
    	if (!$cate_id){
    		echo json_encode(false);
    		return ;
    	}
    	$type_list = $this->_get_options1($cate_id);
    	
    	//========= Ruesin
//     	$mCst =& m('customs');
//     	$cCates  = $mCst->getCate();
//     	foreach ($cCates as  $key => $row){
//     	    if($row['parts_type']['fabric']['id'] == $cate_id){
//     	        $type_list = $this->_get_options1($key);
// //     	        if($key == 3)break;
//     	        break;
//     	    }
//     	}
    	//========= Ruesin
    	
		$tmp = array();
		foreach ($type_list as $k=>$v){
			$tmp[] = array("type_id"=>$k,"type_name"=>$v);
		}
    	echo json_encode($tmp);
    	
    }
    
    /**
     * ajax根据分类id获得对应的类型id
     */
    function get_type_style()
    {
    	$cate_id   = intval($_POST['cate_id']);
    	if (!$cate_id)
    	{
    		echo json_encode(false);
    		return ;
    	}
    	$type_list = $this->_get_options_style($cate_id);
    	 
    	$tmp = array();
    	foreach ($type_list as $k=>$v)
    	{
    		$tmp[] = array("type_id"=>$k,"type_name"=>$v);
    	}
    	echo json_encode($tmp);
    	 
    }
    
   
  
    /**
     * ajax 根据类型取得属性
     */
    function get_attr() {
    	$goods_id   = empty($_POST['goods_id']) ? 0 : intval($_POST['goods_id']);
    	$goods_type = empty($_POST['type_id']) ? 0 : intval($_POST['type_id']);
    	
    	$content    = $this->build_attr_html($goods_type, $goods_id);
    	
    	echo json_encode($content);
    }
    
 
	
    /**
     * 异步修改数据
     */
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('part_name', 'part_sn', 'price','sort_order','part_number','is_on_sale')))
       {
           $data[$column] = $value;
       		if($column == 'part_name')
			{
				$brand = $this->_part_mod->get_info($id);
	
				if(!$this->_part_mod->unique($value, $id))
				{
					echo ecm_json_encode(false);
					return ;
				}
			}
			elseif ($column == 'part_sn')
			{
				if(!$this->_part_mod->unique_sn($value, $id))
				{
					echo ecm_json_encode(false);
					return ;
				}
			}
           $this->_part_mod->edit($id, $data);
           if(!$this->_part_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }
	
   /**
    * 检查组件名字是否唯一
    * 暂时不需要 注释掉 
    */
    function check_part_name() {
   /*	$goods_name = isset($_POST['goods_name'])?$_POST['goods_name']:'';
   		$part_id = isset($_POST['part_id'])?$_POST['part_id']:0;
   		$cat_id = $_POST['cat_id'];
   		if (!$goods_name) {
   			echo false;
   		}
   		if (!$cat_id) {
   			echo false;
   		}
   		if ($this->_part_mod->unique($goods_name,$cat_id,$part_id)) {
   			echo true;
   		} else {
   			echo false;
   		} */
   	
   		echo ecm_json_encode(true);
   }
   
   /* ajax 验证组件 */
   function check_part_id() {
   	 $part_id = isset($_REQUEST['part_id'])?$_REQUEST['part_id']:0;
   	 $def_part_id = isset($_REQUEST['def_part_id'])?$_REQUEST['def_part_id']:0;
   	
   	if (!$part_id) {
   		echo ecm_json_encode(false);
   		return ;
   	}
   	
   	if ($def_part_id){
	   	if ($part_id == $def_part_id){
	   		echo ecm_json_encode(true);
	   		return ;
	   	}else {
	   		$part_id = $def_part_id;
	   	}
   	}
   	
   	if ($this->_part_mod->get($part_id)) {
   		echo ecm_json_encode(false);
   		return ;
   	}else {
   		echo ecm_json_encode(true);
   		return ;
   	} 
   
   	
   }
   
   
   
    /**
     * 检查组件CODE是否唯一
     * @param  
     * @return 
     * @author Ruesin
     */
   function check_code(){
      	$code = isset($_GET['code'])?trim($_GET['code']):'';
      	$part_id = isset($_GET['part_id'])?intval($_GET['part_id']):0;

      	if ($code){
      	    if ($this->_part_mod->unique_code($code,$part_id)){
      	        echo true;
      	    } else {
      	        echo ecm_json_encode(false);
      	    }
      	}
   }
   
   /**
    * 检查组件编号是否唯一
    */
   function check_part_sn_name()
   {
   	$part_sn = isset($_GET['part_sn'])?trim($_GET['part_sn']):'';
   	$part_id = isset($_GET['part_id'])?intval($_GET['part_id']):0;
   	
   	if ($part_sn) {
   		if ($this->_part_mod->unique_sn($part_sn,$part_id)) {
   			echo true;
   		} else {
   			echo ecm_json_encode(false);
   		}
   	}
   }
   
    /**
     * 删除属性 
     */ 
    function drop()
    {
      
            $id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
            $del_act = isset($_REQUEST['del_act']) ? trim($_REQUEST['del_act']) : 'index';
            if (!$id)
            {
                $this->show_warning('删除组件不存在');
                return;
            }
            $ids = explode(',', $id);
            $this->_part_mod->drop_data($ids);
			$id1 = $ids[0];
			$tmp = $this->_part_mod->get_info($id1);
			$cate_id = $tmp['cate_id'];
            foreach ($ids as $v) {
            	$customs_parts_mod = & m("customsparts");
            	$cus_info = $customs_parts_mod->get(array(
            			"conditions"	=> "pt_id=$v"
            			));
            	if ($cus_info) {
            		$this->show_warning("你要删除的组件存在对应的基本款,不允许删除");
            		return ;
            	}
            	$this->_part_mod->drop($v);
            }
            $this->show_message('drop_ok',
                'back_list', 'index.php?app=part&act='.$del_act.'&cate_id='.$cate_id);
       
    }
    
    /**
     * 根据属性数组创建属性的表单
     *
     * @access  public
     * @param   int     $cat_id     分类编号
     * @param   int     $goods_id   商品编号
     * @return  string
     */
    function build_attr_html($cat_id, $goods_id = 0)
    {
    	/* $zujian_attr_mod = & m("partattr");
    	$attr = $zujian_attr_mod->find(array(
    			"fields" => "g.*,v.attr_value",
    			"conditions"	=> "g.type_id=$cat_id and v.part_id=$goods_id",
    			"join"	=>	"belongs_to_partattr",
    			));
    	if ($goods_id)
    	{
    		$part_info = $this->_part_mod->get_info($goods_id);
    		$part_type = $part_info['type_id'];
    		if ($part_type != $cat_id)
    		{
    			$goods_id = 0;
    		}
    	} */
    	
   		if ($goods_id){
    		$conditions = "1=1 and type_id = $cat_id and v.part_id=$goods_id";
    		$zujian_attr_mod = & m("partattr");
    		$attr = $zujian_attr_mod->find(array(
    				'fields'	=>	'g.*,v.attr_value',
    				'conditions'=>$conditions,
    				'join'	=>	'belongs_to_partattr',
    				'order'=> 'g.sort_order',
    		));
    	}else{
    		$conditions = "1=1 and type_id = $cat_id";
    		$zujian_attr_mod = & m("partattribute");
    		$attr = $zujian_attr_mod->find(array(
    					'fields' => 'g.*',
    					"conditions"	=>	$conditions,
    					'order'	=> 'g.sort_order',
    				));
    	} 
    	
    	$html = '<table width="100%" id="attrTable">';
    	$spec = 0;
    
    	foreach ($attr AS $key => $val)
    	{
    		$html .= "<tr><td class='label'>";
    		if ($val['attr_type'] == 1 || $val['attr_type'] == 2)
    		{
    			$html .= ($spec != $val['attr_id']) ?
    			"<a href='javascript:;' onclick='addSpec(this)'>[+]</a>" :
    			"<a href='javascript:;' onclick='removeSpec(this)'>[-]</a>";
    			$spec = $val['attr_id'];
    		}
    
    		$html .= "$val[attr_name]</td><td><input type='hidden' name='attr_id_list[]' value='$val[attr_id]' />";
    
    		if ($val['attr_input_type'] == 0)
    		{
    			$html .= '<input name="attr_value_list[]" type="text" value="' .htmlspecialchars($val['attr_value']). '" size="40" /> ';
    		}
    		else
    		{
    			$html .= '<select name="attr_value_list[]">';
    			$html .= '<option value="">请选择</option>';
    
    			$attr_values = explode("\n", $val['attr_values']);
    
    			foreach ($attr_values AS $opt)
    			{
    				$opt    = trim(htmlspecialchars($opt));
    
    				$html   .= ($val['attr_value'] != $opt) ?
    				'<option value="' . $opt . '">' . $opt . '</option>' :
    				'<option value="' . $opt . '" selected="selected">' . $opt . '</option>';
    			}
    			$html .= '</select> ';
    		}
    
    		
    
    		$html .= '</td></tr>';
    	}
    
    	$html .= '</table>';
    
    	return $html;
    }
    
    /* 取得可以作为上级的类型数据 */
    //Modify By Ruesin 2014-07-29 10:32:11
    function _get_options1($except = NULL){
        //========= Ruesin
        $mCst =& m('customs');
        $cCates  = $mCst->getCate();
        foreach ($cCates as  $key => $row){
            if($row['parts_type']['fabric']['id'] == $except){
                $cId =$key;
//     	        if($key == 3)break;
                break;
            }
        }
        //========= Ruesin
    	$parttype_mod = & m("parttype");
    	$gcategories = $parttype_mod->get_list(-1,$cId);
    	$tree =& $this->_tree1($gcategories);
    	return $tree->getOptions(MAX_LAYER - 1, 0, $cId);
    }
    
    /* 取得可以作为上级的类型数据 */
    function _get_options_style($except = NULL)
    {
    	$parttype_mod = & m("parttype");
    	 
    	$gcategories = $parttype_mod->get_list_style(-1,$except);
    	$tree =& $this->_tree1($gcategories);
    
    	return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    
    /* 构造并返回树 */
    function &_tree1($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'type_id', 'parent_id', 'cate_name');
    	return $tree;
    }
    
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
    	$gcategories = $this->_part_mod->get_cate();
    	$tree =& $this->_tree($gcategories);
    	return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /**
     *    处理上传图片
     *
     *    @author    liliang
     *    @param     int $brand_id
     *    @return    string
     */
    function _upload_logo($part_id,$file)
    {
    	if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
    	{
    		return '';
    	}
    	import('uploader.lib');             //导入上传类
    	$uploader = new Uploader();
    	$uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
    	$uploader->addFile($file);//上传logo
    	if (!$uploader->file_info())
    	{
    		$this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=part&amp;id=' . $part_id);
    		return false;
    	}
    	/* 指定保存位置的根目录 */
    	$uploader->root_dir(ROOT_PATH);
    
    	/* 上传 */
    	if ($file_path = $uploader->save('data/files/mall/part', $part_id))   //保存到指定目录，并以指定文件名$brand_id存储
    	{
    		return $file_path;
    	}
    	else
    	{
    		return false;
    	}
    }
    
	/**
     * 导出面料
     * @author liliang
     */
    function export(){

        $order_mod = m('part');
        $orders = $order_mod->findAll(array(
        	"conditions" => "fabric_id > 0",
        	'fields' => 'part_id,part_name,price,part_number',
        ));
        $fields_name = array('面料id','面料名称','价格','库存');
        array_unshift($orders,$fields_name);
        $this->export_to_csv($orders, 'part', 'gbk');
    }
    
}

?>
