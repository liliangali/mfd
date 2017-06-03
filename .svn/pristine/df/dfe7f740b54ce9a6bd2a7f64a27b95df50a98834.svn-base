<?php
/**
 *    组件管理控制器
 */
define('MAX_LAYER', 2);
class PartTypeApp extends BackendApp
{
    var $_parttype_mod;
    var $part_type;

    function __construct()
    {
    	define("TYPE", "type/");
    	//$this->part_type = include ROOT_PATH . '/data/type.inc.php';
        $this->PartTypeApp();
        
    }
    function PartTypeApp()
    {
        parent::BackendApp();

        $this->_parttype_mod =& m('parttype');
    }

    /* 商品列表 */
    function index()
    {
    	$gcategory_mod = & m("gcategory");
    	$gcategory_list = $gcategory_mod->get_list(-1);

        $this->assign('gcategory_list',$gcategory_list);
        $this->import_resource(array('script' => 'mlselection.js,inline_edit_admin.js'));
        $this->assign('enable_radar', Conf::get('enable_radar'));
        $this->display(TYPE.'parttypeg.index.html');
    }
	
    /**
     * 列出属性列表
     */
    function listattr() {
    	$type_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
    	if(!$type_id) {
    		$this->show_warning("请先选择类型");
    	}
    	$cate_id = $this->get_cate($type_id);
    	$conditions = "and type_id = ".$type_id;
    	$zujian_attr_mod = & m("partattribute");
    	$page = $this->_get_page(10); //获取分页信息
    	$attr_lists = $zujian_attr_mod->find(array(
    	'fields' => 'g.*',
    	'limit' => $page['limit'],
    	'conditions' => ' 1=1 '.$conditions,
    	'count'   => true   //允许统计
    	));
    	
    	/* foreach ($this->part_type as $k=>$v) {
    		if ($k == $type_id) {
    			$type_name = $v;
    		}
    	} */
    	$page['item_count']=$zujian_attr_mod->getCount();
  		$tmp = $attr_lists;
    	foreach ($tmp as $k=>$v) {
    		$attr_lists[$k]['type_name'] = $type_name;
    		
    		$attr_values = $v['attr_values'];
    		$tmp = explode("\r\n", $attr_values);
    		$tmp1 = implode(",", $tmp);
    		$attr_lists[$k]['attr_values'] = $tmp1;
    	}
    	
    	//将属性 可选值列表的 可选值格式化
    	$this->import_resource(array(
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));
//    echo "<pre>";var_dump($attr_lists);exit;
    	$this->_format_page($page);
    //var_dump($page);exit;	
//     dump($this->_get_type_list($cate_id));
    	$this->assign("cate_id",$cate_id);
    	$this->assign("type_id",$type_id);
    	$this->assign("attr_lists",$attr_lists);
    	$this->assign('zujiantype', $this->_get_options($cate_id,3));
    	$this->assign("page_info",$page);
    	$this->display(TYPE.'partattr.index.html');
    }
    
    /**
     * 类型列表
     */
    function listtype()
    {
    	$tmp_cate_id = array_keys($this->_get_options1());
    	$cate_id = isset($_GET['cate_id'])?intval($_GET['cate_id']):$tmp_cate_id[0];
    	if (!$cate_id)
    	{
    		$this->show_warning("请先选择分类");
    	}
    	$gcategory_mod = & m("gcategory");
    	$g_info = $gcategory_mod->get_info($cate_id);
    	$gcategories = $this->_parttype_mod->get_list(0,$cate_id);

    	$tree =& $this->_tree($gcategories);
	
    	/* 先根排序 */
    	foreach ($gcategories as $key => $val)
    	{
    		$gcategories[$key]['switchs'] = 0;
    		if ($this->_parttype_mod->get_list($val['type_id'],$cate_id))
    		{
    			$gcategories[$key]['switchs'] = 1;
    		}
    	}
		
		$this->assign("cate_id",$cate_id);
		$this->assign("cate_name",$g_info['cate_name']);
		$this->assign("parents",$this->_get_options1());
    	$this->assign('gcategories', $gcategories);
    	/* 构造映射表（每个结点的父结点对应的行，从1开始） */
    	
    	$this->assign('max_layer', MAX_LAYER);
    	
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));
    	$this->display(TYPE.'parttype.index.html');
    }
    
    /**
     * 添加类型
     */
    function addtype()
    {
    	$cid = empty($_GET['c_id']) ? 0 : intval($_GET['c_id']);
    	if (!IS_POST)
    	{
    		/* 参数 */
    		$pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
    		$gcategory = array(
    				'parent_id' => $pid, 
    				'sort_order' => 255,
    				'map_seq'	=> 0,
    				'if_show' => 1,
    				'is_store'=>1,
    				'part_width'=>68,
    				'part_height'=>68,
    				);
    		$this->assign('gcategory', $gcategory);
    		$this->assign("cate_id",$cid);
//     dump($this->_get_options($cid));
    		$this->assign('parents', $this->_get_options($cid));
    		$this->assign("parents1", $this->_get_options1());
    		/* 导入jQuery的表单验证插件 */
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->display(TYPE.'type.form.html');
    	}
    	else 
    	{
    		$data = array();
    		$data["type_name"] = trim($_POST['type_name']);
    		$data["cate_id"] = intval($_POST['cate_id']);
    		$data['parent_id'] = intval($_POST['parent_id']);
    		$data['re_name']	= trim($_POST['re_name']);
    		$data['is_store']	=	intval($_POST['is_store']);
    		$data['type_img']	=	trim($_POST['type_img']);
    		$data["part_width"] = intval($_POST['part_width']);
    		$data["part_height"] = intval($_POST['part_height']);
    		$data["map_seq"]	= intval($_POST['map_seq']);
//     	dump($data);	
    		if(!$this->_parttype_mod->add($data))
    		{
    			$this->show_message($this->_parttype_mod->_errors);
    		}
    		
    		$this->show_message('添加成功',
    				'返回类型列表',    'index.php?app=parttype&amp;act=listtype&amp;cate_id='.$cid,
    				'continue_add', 'index.php?app=parttype&amp;act=addtype&amp;c_id='.$cid
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edittype()
    {
    	$type_id = intval($_GET['id']);
    	if (!IS_POST)
    	{
    		
    		if (!$type_id)
    		{
    			$this->show_warning("你要修改的类型不存在");
    		}
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$type_info = $this->_parttype_mod->get_info($type_id);
//     	dump($type_info);
    		$this->assign("gcategory",$type_info);
    		$this->assign("cate_id",$type_info['cate_id']);
    		$this->assign('parents', $this->_get_options($type_info['cate_id']));
    		$this->assign("parents1", $this->_get_options1());
    		$this->display(TYPE."type.form.html");
    	}
    	else
    	{
    		$data = array();
    		$data["type_name"] = trim($_POST['type_name']);
    		$data["cate_id"] = intval($_POST['cate_id']);
    		$data["sort_order"] = intval($_POST['sort_order']);
    		$data['parent_id'] = intval($_POST['parent_id']);
    		$data['re_name']	= trim($_POST['re_name']);
    		$data['is_store']	=	intval($_POST['is_store']);
    		$data['type_img']	=	trim($_POST['type_img']);
    		$data["part_width"] = intval($_POST['part_width']);
    		$data["part_height"] = intval($_POST['part_height']);
    		$data["map_seq"]	= intval($_POST['map_seq']);
//     		dump($data);
    		if($this->_parttype_mod->edit($type_id,$data) === false)
    		{
    			$this->show_message($this->_parttype_mod->_errors);
    		}
    
    		$this->show_message('修改成功',
    				'返回类型列表',    'index.php?app=parttype&amp;act=listtype&amp;cate_id='.$data['cate_id']
    		);
    
    	}
    }
    
    /* 异步去商品分类子元素 */
    function ajax_cate()
    {
    	if(!isset($_GET['id']) || empty($_GET['id']))
    	{
    		echo ecm_json_encode(false);
    		return;
    	}
//     	$this->_gcategory_mod =& bm('gcategory');
    	$cate = $this->_parttype_mod->get_list($_GET['id']);
// dump($cate);
		/* $gcategory_mod = & m("gcategory");
		$gcategory_mod->get_info($cate[0]); */
    	foreach ($cate as $key => $val)
    	{
    		$gcategory_mod = & m("gcategory");
    		$g_info = $gcategory_mod->get_info($val['c_id']);
    		$cate[$key]['c_name'] = $g_info['cate_name'];
    		$cate[$key]['attr_list'] = "属性列表";
    		$child = $this->_parttype_mod->get_list($val['cate_id']);
    		$lay = $this->_parttype_mod->get_layer($val['cate_id']);
    		if ($lay >= MAX_LAYER)
    		{
    			$cate[$key]['add_child'] = 0;
    		}
    		else
    		{
    			$cate[$key]['add_child'] = 1;
    		}
    		if (!$child || empty($child) )
    		{
    
    			$cate[$key]['switchs'] = 0;
    		}
    		else
    		{
    			$cate[$key]['switchs'] = 1;
    		}
    	}
//   dump($cate);
    	header("Content-Type:text/html;charset=" . CHARSET);
    	echo ecm_json_encode(array_values($cate));
    	return ;
    }
    /* 检查分类名的唯一*/
    function check_parttype()
    {
    	$cate_name = empty($_GET['cate_name']) ? '' : trim($_GET['cate_name']);
    	$parent_id = empty($_GET['parent_id']) ? 0  : intval($_GET['parent_id']);
    	$cate_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	if (!$cate_name)
    	{
    		echo ecm_json_encode(true);
    		return ;
    	}
    	if ($this->_gcategory_mod->unique($cate_name, $parent_id, $cate_id))
    	{
    		echo ecm_json_encode(true);
    	}
    	else
    	{
    		echo ecm_json_encode(false);
    	}
    	return ;
    }
    
	/**
	 * @删除类型
	 * @return bool 
	 */
    function droptype()
    {
    	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    	if (!$id)
    	{
    		$this->show_warning('你要删除的类型不存在');
    		return;
    	}
    	
    	
    
    	$ids = explode(',', $id);
//     	dump($ids);
    	//检查每个类型是否有子级分类 如果有不允许删除
    	foreach ($ids as $v)
    	{
    		$parttype = $this->_parttype_mod->get(array(
    				"conditions" => "parent_id=$v",
    				));
    		if ($parttype)
    		{
    			$this->show_warning('你要删除的类型有子级分类,请先删除子级分类');
    			return ;
    		}
    	}
    	
    	//检查每个类型下是否存在对应组件  如果存在则不允许删除
    	foreach ($ids as $v)
    	{
    		$part_mod = & m("part");
    		$part_info = $part_mod->get(array(
    				"conditions"	=> "type_id =$v"
    		));
    		if ($part_info)
    		{
    			$this->show_warning("你要删除的类型存在对应的组件 不允许删除");
    			return ;
    		}
    	}
    	
    	//检查每个类型下是否存在属性  如果存在属性 则不允许删除
    	foreach ($ids as $v)
    	{
    		$part_attribute_mod = & m("partattribute");
    		$part_attribute_info = $part_attribute_mod->get(array(
    				"conditions"	=>	"type_id=$v",
    				));
    		if($part_attribute_info)
    		{
    			$this->show_warning("你要删除的类型 存在对应的属性 不允许删除");
    			return ;
    		}
    	}
    	
    	
    	if (!$this->_parttype_mod->drop($ids))
    	{
    		$this->show_warning($this->_gcategory_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
    
    /**
     * 添加属性
     */
    function addattr() {
    	$type_id = empty($_GET['type_id']) ? 0 : intval($_GET['type_id']);
    	$cate_id = $this->get_cate($type_id);
    	if (!IS_POST) 
    	{
    		/*显示新增表单*/
	    	/*if(!$type_id) {
	    		exit("请先选择组件类型");
	    	} */
    		$yes_or_no = array(
    				0 => "手工录入",
    				1 => "从下面的列表中选择（一行代表一个可选值）",
    		);
    		
    		$attr_type = array(
    				0 => "唯一属性",
    				2 => "复选属性",
    		);
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign("zujiantype",$this->_get_options($cate_id,3));
    		$this->assign("yes_or_no",$yes_or_no);
    		$this->assign("attr_type",$attr_type);
    		$this->assign("type_id",$type_id);
    		$this->display(TYPE.'partattr.add.html');
    	}
    	else 
    	{
    		
    		$data = array();
    		$data['type_id'] = $_POST['type_id'];
    		$data['attr_name'] = $_POST['attr_name'];
    		$data['attr_values'] = trim($_POST['attr_values']);
    		$data['sort_order'] = $_POST['sort_order'];
    		$data['attr_type']  = $_POST['attr_type'];
    		$data['attr_input_type'] = $_POST['attr_input_type'];
    		$zujian_attr_mod = & m("partattribute");
//     	dump($zujian_attr_mod);exit;
    		if (!$attr_id = $zujian_attr_mod->add($data)) {
    			$this->show_warning($zujian_attr_mod->get_error());
    			return;
    		}
    		
    		$this->show_message('add_attr_successed',
    				'back_list',    'index.php?app=parttype&amp;act=listattr&amp;id='.$type_id,
    				'continue_add', 'index.php?app=parttype&amp;act=addattr&amp;type_id='.$type_id
    		);
    	}
    }
    
    function _get_type_list($cate_id)
    {
    	$part_type_list = $this->_parttype_mod->find(array(
    				"conditions"	=> 	"1=1 and cate_id=$cate_id",
    	));
//     	dump($part_type_list);
    	$tmp = array();
    	foreach ($part_type_list as $v)
    	{
    		$tmp[$v['type_id']] = $v['type_name'];
    	}
//     dump($tmp);
    	return $tmp;
    }
    /**
     * 修改属性
     */
    function editattr() {
    	$type_id = empty($_GET['type_id']) ? 0 : intval($_GET['type_id']);
    	$cate_id = $this->get_cate($type_id);
    	
    	if (!IS_POST)
    	{
    		
    		$attr_id = empty($_GET['attr_id']) ? 0 : intval($_GET['attr_id']);
    		// 查看属性是否有对应的组件 如果有  则不允许删除
    		$part_attr_mod = & m("partattr");
    		/* $part_attr_info = $part_attr_mod->get(array(
    				"conditions"	=>	"attr_id = $attr_id"
    				));
    		if ($part_attr_info) {
    			$this->show_warning("你要修改的属性存在对应的组件  不允许修改");
    			return ;
    		} */
    		$zujian_attr_mod = & m("partattribute");
    		$attr_row = $zujian_attr_mod->get("attr_id=".$attr_id);
//echo "<pre>";var_dump($attr_row);exit;
    		$attr_type = array(
    				0 => "唯一属性",
    				2 => "复选属性",
    		);
    		$yes_or_no = array(
    				0 => "手工录入",
    				1 => "从下面的列表中选择（一行代表一个可选值）",
    		);
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign("zujiantype",$this->_get_options($cate_id,3));
    		$this->assign("attr_row",$attr_row);
    		$this->assign("yes_or_no",$yes_or_no);
    		$this->assign("attr_type",$attr_type);
    		$this->assign("type_id",$type_id);
    		$this->display(TYPE.'partattr.edit.html');
    	}
    	else
    	{
    		$data = array();
    		$attr_id = intval($_POST['attr_id']);
    		$data['type_id'] = $_POST['type_id'];
    		$data['attr_name'] = $_POST['attr_name'];
    		$data['attr_values'] = trim($_POST['attr_values']);
    		$data['sort_order'] = $_POST['sort_order'];
    		$data['attr_type']  = $_POST['attr_type'];
    		$data['attr_input_type'] = $_POST['attr_input_type'];
    		$zujian_attr_mod = & m("partattribute");
    		$conditions = "attr_id=".$attr_id;
//    echo $conditions; dump($data);exit;
    		$zujian_attr_mod->edit($conditions,$data);
    		$this->show_message('edit_attr_successed',
    				'back_list',    'index.php?app=parttype&amp;act=listattr&amp;id='.$type_id
    				
    		);
    	}
    }
    
    /**
     * 根据type_id获得cate_id
     */
    function get_cate($type_id)
    {
    	$type_info = $this->_parttype_mod->get_info($type_id);
    	return $type_info['cate_id'];
    }
    
    /**
     * 检查属性名称是否唯一
     */
    function check_attr_name() {
    	$attr_name = empty($_GET['attr_name']) ? '' : trim($_GET['attr_name']);
    	$type_id   = empty($_GET['type_id']) ? 0 : intval($_GET['type_id']);
    	if (!attr_name) {
    		echo ecm_json_encode(false);
    	}
    	$partattr_mod = & m("partattribute");
    	if ($partattr_mod->unique($attr_name, $type_id)) {
    		echo ecm_json_encode(true);
    	}
    	else
    	{
    		echo ecm_json_encode(false);
    	}
    	return ;
    }
    
    /* 推荐商品到 */
    function recommend()
    {
        if (!IS_POST)
        {
            /* 取得推荐类型 */
            $recommend_mod =& bm('recommend', array('_store_id' => 0));
            $recommends = $recommend_mod->get_options();
            if (!$recommends)
            {
                $this->show_warning('no_recommends', 'go_back', 'javascript:history.go(-1);', 'set_recommend', 'index.php?app=recommend');
                return;
            }
            $this->assign('recommends', $recommends);
            $this->display('goods.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $recom_id = empty($_POST['recom_id']) ? 0 : intval($_POST['recom_id']);
            if (!$recom_id)
            {
                $this->show_warning('recommend_required');
                return;
            }

            $ids = explode(',', $id);
            $recom_mod =& bm('recommend', array('_store_id' => 0));
            $recom_mod->createRelation('recommend_goods', $recom_id, $ids);
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('recommend_ok',
                'back_list', 'index.php?app=goods&page=' . $ret_page,
                'view_recommended_goods', 'index.php?app=recommend&amp;act=view_goods&amp;id=' . $recom_id);
        }
    }
    
    /* 删除属性 */
    function drop()
    {
    //var_dump($_REQUEST);exit;
    		$type_id   = empty($_GET['type_id']) ? 0 : intval($_GET['type_id']);
            $attr_id = isset($_REQUEST['attr_id']) ? trim($_REQUEST['attr_id']) : '';
            if (!$attr_id)
            {
                $this->show_warning('要删除的属性不存在');
                return;
            }
            
            $attr_ids = explode(',', $attr_id);
   //var_dump($attr_ids);exit;
            // drop
            foreach ($attr_ids as $v)
            {
            	//检查要删除的属性是否有对应的组件  如果有 则不允许删除
            	$part_attr_mod = & m("partattr");
            	$part_attr_info = $part_attr_mod->get(array(
            			"conditions"	=> "attr_id=$v"
            			));
            	if ($part_attr_info) {
            		$this->show_warning("你要删除的属性存在对应的组件  不允许删除");
            		return ;
            	}
            }
            $zujian_attr_mod = & m("partattribute");
            //$zujian_attr_mod->drop_data($ids);
//  var_dump($attr_ids);exit;
            $zujian_attr_mod->drop($attr_ids);
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('drop_ok',
                'back_list', 'index.php?app=parttype&act=listattr&id='.$type_id.'&page=' . $ret_page);
       
    }
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL,$max=MAX_LAYER)
    {
    	$gcategories = $this->_parttype_mod->get_list(-1,$except);
    	$tree =& $this->_tree($gcategories);
    	return $tree->getOptions($max - 1, 0, $except);
    }
    
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'type_id', 'parent_id', 'cate_name');
    	return $tree;
    }
    
    /* 构造并返回树 */
    function &_tree1($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options1($except = NULL)
    {
    	/* $gcategory_mod = & m("gcategory");
    	$gcategories = $gcategory_mod->get_list(); */
    	$part_mod = & m("part");
    	$gcategories = $part_mod->get_cate();
    	//        dump($gcategories);
    	$tree =& $this->_tree1($gcategories);
    
    	return $tree->getOptions(2, 0, $except);
    }
}

?>
