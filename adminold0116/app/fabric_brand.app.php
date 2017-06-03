<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */


define('MAX_LAYER', 3);

/**
 |--------------------------------------------------------------------------
 | 定制商品属性管理
 |--------------------------------------------------------------------------
 |
 |
 | @author 小五 <xiao5.china@gmail.com>
 |
 */

class Fabric_BrandApp extends BackendApp
{
	var $_fbcategory_mod;
	var $template;
	var $show_items;
	/**
	 * 构造函数
	 */
	function __construct()
	{
		$this->Fabric_BrandApp();
	}
	function Fabric_BrandApp()
	{
		parent::__construct();
		$this->template="fabric";
		$this->_fbcategory_mod =& m('fbcategory');

		$this->show_items=array(
				'0'=>'否',
				'1'=>'是'
		);
		$this->def_items=array(
		    '0'=>'否',
		    '1'=>'是'
		);
		$this->box_items=array(
		    '0'=>'否',
		    '1'=>'是'
		);
		$this->alone_itms=array(
		    '0'=>'否',
		    '1'=>'是'
		);
	}
	
	/* 管理 */
	function index()
	{
		/* 取得商品分类 */
		$acategories = $this->_fbcategory_mod->get_list();
		$tree =& $this->_tree($acategories);
		
		/* 先根排序 */
		$sorted_acategories = array();
		$cate_ids = $tree->getChilds();
		foreach ($cate_ids as $id)
		{
		    $parent_children_valid = $this->_fbcategory_mod->parent_children_valid($id);
		    $sorted_acategories[] = array_merge($acategories[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>$parent_children_valid));
		}

		$this->assign('acategories', $sorted_acategories);
		
		/* 构造映射表（每个结点的父结点对应的行，从1开始） */
		$row = array(0 => 0);   // cate_id对应的row
		$map = array();         // parent_id对应的row
		foreach ($sorted_acategories as $key => $fdiy_management)
		{
		    $row[$fdiy_management['cate_id']] = $key + 1;
		    $map[] = $row[$fdiy_management['parent_id']];
		}

//		echo '<pre>';print_r($map);exit;

		$this->assign('map', ecm_json_encode($map));
		
		$this->assign('max_layer', MAX_LAYER);
		
		$this->import_resource(array(
		    'script' => 'jqtreetable.js,inline_edit_admin.js',
		    'style'  => 'res:style/jqtreetable.css')
		    );

		$this->display("fabric/fabric_brand.index.html");
	}
	/* 异步去商品分类子元素 */
	function ajax_cate()
	{
		if(!isset($_GET['id']) || empty($_GET['id']))
		{
			echo ecm_json_encode(false);
			return;
		}
		$cate = $this->_fbcategory_mod->get_list($_GET['id']);
		foreach ($cate as $key => $val)
		{
			$child = $this->_fbcategory_mod->get_list($val['cate_id']);
			$lay = $this->_fbcategory_mod->get_layer($val['cate_id']);
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
		header("Content-Type:text/html;charset=" . CHARSET);
		echo ecm_json_encode(array_values($cate));
		return ;
	}
	
	//异步修改数据
	function ajax_col()
	{
		$id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
		$column = empty($_GET['column']) ? '' : trim($_GET['column']);
		$value  = isset($_GET['value']) ? trim($_GET['value']) : '';
		$data   = array();
		if (in_array($column ,array('cate_name','is_def', 'if_show', 'sort_order','is_alone')))
		{
			$data[$column] = $value;
			if($column == 'cate_name')
			{
				$gcategory = $this->_fbcategory_mod->get_info($id);
				if (!$this->_fbcategory_mod->unique($value, $gcategory['parent_id'], $id))
				{
					echo ecm_json_encode(false);
					return ;
				}
			}
			$this->_fbcategory_mod->edit($id, $data);
			if(!$this->_fbcategory_mod->has_error())
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
	
	/* 新增 */
	function add()
	{
		if (!IS_POST)
		{
			$pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
			/* 参数 */
			$region=$this->_fbcategory_mod->get_options();

            $def_data['ve'] = 0;
            $def_data['uprice'] = 0;
            $def_data['fprice'] = 0;
            $def_data['btype'] = 1;
            $this->assign('show_items',$this->show_items);
            $this->assign('def_items',$this->def_items);
            $this->assign('def_items',$this->def_items);
            $this->assign('box_items',$this->box_items);
            $this->assign('alone_itms',$this->alone_itms);
			$this->assign('region',$region);
			$this->assign('brand',$def_data);
			$this->assign('pid',$pid);
			$this->assign('build_editor', $this->_build_editor(array(
					'name' => 'content',
					'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
			)));
	
	
			/* 导入jQuery的表单验证插件 */
			$this->import_resource(array(
					'script' => 'jquery.plugins/jquery.validate.js'
			));
	
			$this->display('fabric/fabric_brand.form.html');
		}
		else
		{
		    
				$cate_name=isset($_POST['brand_name'])?$_POST['brand_name']:'';
				$sort_order=isset($_POST['sort_order'])?$_POST['sort_order']:'255';
				$content = isset($_POST['content']) ? addslashes($_POST['content']): '';
				
				$data = array(
						'cate_name'  => $cate_name,
                        'ftype'      =>   $_POST['ftype'],
				        've'         => isset($_POST['ve'])?addslashes($_POST['ve']):'',
						'parent_id'  => $_POST['region_id'],
						'sort_order' => $sort_order,
						'letter_retrieval'=>isset($_POST['letter_retrieval'])?strtoupper($_POST['letter_retrieval']):'',
						'if_show'    => isset($_POST['if_show'])?$_POST['if_show']:'1',
				        'is_def'    => isset($_POST['is_def'])?$_POST['is_def']:'1',
				        'is_box'    => isset($_POST['is_box'])?$_POST['is_box']:'1',
				        'is_alone'    => isset($_POST['is_alone'])?$_POST['is_alone']:'0',
				        'if_common'    => isset($_POST['if_common'])?$_POST['if_common']:'1',
				        'uprice'    => $_POST['uprice'],
				        'fprice'    => $_POST['fprice'],
						'small_img'     => isset($_POST['small_img'])?addslashes($_POST['small_img']):'',
				        'source_img'     => isset($_POST['source_img'])?addslashes($_POST['source_img']):'',
 						'gongxiao_content'=>isset($_POST['gongxiao_content'])?addslashes($_POST['gongxiao_content']):'',
				);
				
				/* 检查名称是否已存在 */
				if (!$this->_fbcategory_mod->unique(trim($data['cate_name']), $data['parent_id']))
				{
					$this->show_warning('name_exist');
					return;
				}
				/* 检查级数 */
				$ancestor = $this->_fbcategory_mod->get_ancestor($data['parent_id'],1);
				
				if (count($ancestor) >= MAX_LAYER)
				{
					$this->show_warning('max_layer_error');
					return;
				}
				
			/* 保存 */
			$cate_id = $this->_fbcategory_mod->add($data);
			if (!$cate_id)
			{
				$this->show_warning($this->_fbcategory_mod->get_error());
				return;
			}
		
				$this->show_message('add_ok',
						'back_list',    'index.php?app=fabric_brand',
						'continue_add', 'index.php?app=fabric_brand&amp;act=add&amp;pid=' . $data['parent_id']
				);
			
			
		}
	}
	
	/* 新增 1122*/
	function edit()
	{
		if (!IS_POST)
		{
			$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
			if(!$id){
				$this->show_warning("参数错误");
				return ;
			}
			
			/* 参数 */
			$brand_info=$this->_fbcategory_mod->get($id);
			$brand_info['logo']=stripslashes($brand_info['logo']);
			$brand_info['content']=stripslashes($brand_info['content']);
		
			$region=$this->_fbcategory_mod->get_options();
			if(!$region){
				$this->show_warning('不存在该分类');
				return ;
			}
		
			$this->assign('region',$region);
			$this->assign('pid',empty($brand_info['parent_id']) ? $brand_info['cate_id'] : $brand_info['parent_id']);
			$this->assign('build_editor', $this->_build_editor(array(
					'name' => 'content',
					'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
			)));
			/* 导入jQuery的表单验证插件 */
			$this->import_resource(array(
					'script' => 'jquery.plugins/jquery.validate.js'
			));
			$this->assign('show_items',$this->show_items);
			$this->assign('def_items',$this->def_items);
			$this->assign('box_items',$this->box_items);
			$this->assign('alone_items',$this->alone_itms);
			$this->assign('brand',$brand_info);
			$this->display('fabric/fabric_brand.form.html');
		}
		else
		{
		    $id = isset($_POST['brand_id'])?$_POST['brand_id']:'';
		    $cate_name=isset($_POST['brand_name'])?$_POST['brand_name']:'';
		    $sort_order=isset($_POST['sort_order'])?$_POST['sort_order']:'255';
		    $content = isset($_POST['content']) ? addslashes($_POST['content']): '';
		    
		    $data = array(
		        'cate_name'  => $cate_name,
                'ftype'      =>   $_POST['ftype'],
                'parent_id'      =>   $_POST['region_id'],
		        've'         => isset($_POST['ve'])?addslashes($_POST['ve']):'',
		        'sort_order' => $sort_order,
		        'letter_retrieval'=>isset($_POST['letter_retrieval'])?strtoupper($_POST['letter_retrieval']):'',
		        'if_show'    => isset($_POST['if_show'])?$_POST['if_show']:'1',
		        'is_def'    => isset($_POST['is_def'])?$_POST['is_def']:'1',
		        'is_box'    => isset($_POST['is_box'])?$_POST['is_box']:'1',
		        'is_alone'    => isset($_POST['is_alone'])?$_POST['is_alone']:'0',
		        'if_common'    => isset($_POST['if_common'])?$_POST['if_common']:'1',
		        'uprice'    => $_POST['uprice'],
		        'fprice'    => $_POST['fprice'],
		        'small_img'     => isset($_POST['small_img'])?addslashes($_POST['small_img']):'',
		        'source_img'     => isset($_POST['source_img'])?addslashes($_POST['source_img']):'',
                'gongxiao_content'=>isset($_POST['gongxiao_content'])?addslashes($_POST['gongxiao_content']):'',
		        // 						'content'=>$content,
		    );

				/* 检查名称是否已存在 */
				if (!$this->_fbcategory_mod->unique(trim($data['cate_name']), $data['parent_id'],$id))
				{
					$this->show_warning('name_exist');
					return;
				}

			
			/* 保存 */
			$cate_id = $this->_fbcategory_mod->edit($id,$data);
			
			if (!$cate_id)
			{
				$this->show_warning($this->_fbcategory_mod->get_error());
				return;
			}
		
				$this->show_message('edit_brand_ok',
						'back_list',    'index.php?app=fabric_brand',
						'again_edit', "index.php?app=fabric_brand&amp;act=edit&amp;id={$id}"
				);
			
				
		}
	}
	
	/* 删除 */
	function drop()
	{
		$id = isset($_GET['id']) ? trim($_GET['id']) : '';
		if (!$id)
		{
			$this->show_warning('no_gcategory_to_drop');
			return;
		}
	
		$ids = explode(',', $id);
		if (!$this->_fbcategory_mod->drop($ids))
		{
			$this->show_warning($this->_fbcategory_mod->get_error());
			return;
		}
	
		$this->show_message('drop_ok');
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
		$gcategories = $this->_fbcategory_mod->get_list();
		$tree =& $this->_tree($gcategories);
	
		return $tree->getOptions(MAX_LAYER - 1, 0, $except);
	}
	
	function ajax_check_region(){
		$id=!empty($_GET['id'])?$_GET['id']:0;
		$cate_name=!empty($_GET['region_name'])?$_GET['region_name']:'';
		if(is_null($cate_name)){
			echo 0;
			exit();
		}
		$conditions=" AND parent_id=0 AND cate_name='{$cate_name}'";
		if($id){
			$conditions.=" AND cate_id<>{$id}";
		}
		$res=$this->_fbcategory_mod->find(array(
				'conditions'=>"1=1 ".$conditions,
		));
		if($res){
			echo 0;exit();
		}else {
			echo 1;exit();
		}
	}
	
	function ajax_check_brand(){
		$id=!empty($_GET['id'])?$_GET['id']:0;
		$cate_name=!empty($_GET['brand_name'])?$_GET['brand_name']:'';
		if(is_null($cate_name)){
			echo 0;
			exit();
		}
		$conditions=" AND parent_id<>0 AND cate_name='{$cate_name}'";
		if($id){
			$conditions.=" AND cate_id<>{$id}";
		}
		$res=$this->_fbcategory_mod->find(array(
				'conditions'=>"1=1 ".$conditions,
		));
		if($res){
			echo 0;exit();
		}else {
			echo 1;exit();
		}
	}
	
	

}