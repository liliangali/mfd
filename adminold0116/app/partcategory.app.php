<?php
/**
 *    组件管理控制器
 */
define('MAX_LAYER', 4);
class PartCategoryApp extends BackendApp
{
    var $_partcategory_mod;

    function __construct()
    {
        $this->PartCategoryApp();
    }
    function PartCategoryApp()
    {
        parent::BackendApp();

        $this->_partcategory_mod =& m('partcategory');
    }

    /* 组件分类列表 */
    function index()
    {
    /* 取得商品分类 */
        $gcategories = $this->_partcategory_mod->get_list(0);
        $tree =& $this->_tree($gcategories);
// dump($gcategories);exit;
        /* 先根排序 */
        foreach ($gcategories as $key => $val)
        {
            $gcategories[$key]['switchs'] = 0;
            if ($this->_partcategory_mod->get_list($val['cate_id']))
            {
               $gcategories[$key]['switchs'] = 1;
            }
        }
        
//     dump($gcategories);exit;
        $this->assign('gcategories', $gcategories);
        /* 构造映射表（每个结点的父结点对应的行，从1开始） */

        $this->assign('max_layer', 4);

        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
      
        $this->display('partcategory.index.html');
    }
	
    /* 异步取商品分类子元素 */
    function ajax_cate()
    {
    	if(!isset($_GET['id']) || empty($_GET['id']))
    	{
    		echo ecm_json_encode(false);
    		return;
    	}
    	$cate = $this->_partcategory_mod->get_list($_GET['id']);
    	foreach ($cate as $key => $val)
    	{
    		$child = $this->_partcategory_mod->get_list($val['cat_id']);
    		$lay = $this->_partcategory_mod->get_layer($val['cat_id']);
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
    	if (in_array($column ,array('cate_name', 'if_show', 'sort_order')))
    	{
    		$data[$column] = $value;
    		if($column == 'cate_name')
    		{
    			$gcategory = $this->_partcategory_mod->get_info($id);
    			if (!$this->_partcategory_mod->unique($value, $gcategory['parent_id'], $id))
    			{
    				echo ecm_json_encode(false);
    				return ;
    			}
    		}
    		$this->_partcategory_mod->edit($id, $data);
    		if(!$this->_partcategory_mod->has_error())
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
     * 添加分类
     */
/* 新增 */
    function add()
    {
        if (!IS_POST)
        {
            /* 参数 */
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $gcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
            $this->assign('gcategory', $gcategory);
            $this->assign('parents', $this->_get_options());
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('partcategory.add.html');
        }
        else
        {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
                'if_show'    => $_POST['if_show'],
            	'keywords'    => $_POST['keywords'],
            	'cat_desc'   => $_POST['cat_desc'],
            );

            /* 检查名称是否已存在 */
            if (!$this->_partcategory_mod->unique(trim($data['cate_name']), $data['parent_id']))
            {
                $this->show_warning('name_exist');
                return;
            }
            /* 检查级数 */
            $ancestor = $this->_partcategory_mod->get_ancestor($data['parent_id']);
            if (count($ancestor) >= MAX_LAYER)
            {
                $this->show_warning('超过最大级别数');
                return;
            }
            /* 保存 */
            $cate_id = $this->_partcategory_mod->add($data);
            if (!$cate_id)
            {
                $this->show_warning($this->_partcategory_mod->get_error());
                return;
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=partcategory',
                'continue_add', 'index.php?app=partcategory&amp;act=add&amp;pid=' . $data['parent_id']
            );
        }
    }
    
    /* 编辑 */
    function edit()
    {
    	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
    
    	if (!IS_POST)
    	{
    		/* 是否存在 */
    		$gcategory = $this->_partcategory_mod->get_info($id);
    		if (!$gcategory)
    		{
    			$this->show_warning('gcategory_empty');
    			return;
    		}
    		$this->assign('gcategory', $gcategory);
    		/* 导入jQuery的表单验证插件 */
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$this->assign('parents', $this->_get_options());
    		$this->display('partcategory.add.html');
    	}
    	else
    	{
    		$data = array(
    				'cate_name'  => $_POST['cate_name'],
    				'parent_id'  => $_POST['parent_id'],
    				'sort_order' => $_POST['sort_order'],
    				'if_show'    => $_POST['if_show'],
    				'keywords'   => $_POST['keywords'],
    				'cat_desc'   => $_POST['cat_desc'],
    		);
    
    		/* 检查名称是否已存在 */
    		if (!$this->_partcategory_mod->unique(trim($data['cate_name']), $data['parent_id'], $id))
    		{
    			$this->show_warning('name_exist');
    			return;
    		}
    
    		/* 检查级数 */
    		$depth    = $this->_partcategory_mod->get_depth($id);
    		$ancestor = $this->_partcategory_mod->get_ancestor($data['parent_id']);
    		if ($depth + count($ancestor) > MAX_LAYER)
    		{
    			$this->show_warning('max_layer_error');
    			return;
    		}
    
    		/* 保存 */
    		$old_data = $this->_partcategory_mod->get_info($id); // 保存前的数据
    		$rows = $this->_partcategory_mod->edit($id, $data);
    		if ($this->_partcategory_mod->has_error())
    		{
    			$this->show_warning($this->_partcategory_mod->get_error());
    			return;
    		}
    
    		/* 如果改变了上级分类，更新商品表中相应记录的cate_id_1到cate_id_4 */
    		if ($old_data['parent_id'] != $data['parent_id'])
    		{
    			// 执行时间可能比较长，所以不限制了
    			_at(set_time_limit, 0);
    
    			// 清除商城商品分类缓存
    			$cache_server =& cache_server();
    			$cache_server->delete('goods_category_0');
    
    			// 取得当前分类的所有子孙分类（包括自身）
    			$cids = $this->_partcategory_mod->get_descendant_ids($id, true);
    
    			// 找出这些分类中有商品的分类
    			$mod_goods =& m('goods');
    			$mod_gcate =& $this->_partcategory_mod;
    			$sql = "SELECT DISTINCT cate_id FROM {$mod_goods->table} WHERE cate_id " . db_create_in($cids);
    			$cate_ids = $mod_goods->getCol($sql);
    
    			// 循环更新每个分类的cate_id_1到cate_id_4
    			foreach ($cate_ids as $cate_id)
    			{
    				$cate_id_n = array(0,0,0,0);
    				$ancestor = $mod_gcate->get_ancestor($cate_id, true);
    				for ($i = 0; $i < 4; $i++)
    				{
    				isset($ancestor[$i]) && $cate_id_n[$i] = $ancestor[$i]['cate_id'];
    				}
    				$sql = "UPDATE {$mod_goods->table} " .
    				"SET cate_id_1 = '{$cate_id_n[0]}', cate_id_2 = '{$cate_id_n[1]}', cate_id_3 = '{$cate_id_n[2]}', cate_id_4 = '{$cate_id_n[3]}' " .
    				"WHERE cate_id = '$cate_id'";
    				$mod_goods->db->query($sql);
    			}
    			}
    
    			$this->show_message('edit_ok',
    			'back_list',    'index.php?app=partcategory',
    			'edit_again',   'index.php?app=partcategory&amp;act=edit&amp;id=' . $id
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
    		if (!$this->_partcategory_mod->drop($ids))
    		{
    			$this->show_warning($this->_partcategory_mod->get_error());
    			return;
    		}
    	
    		$this->show_message('drop_ok');
    	}
    
    /**
     * 检查分类名称是否唯一
     */
    function check_category() {
    	$cat_name = empty($_GET['cat_name']) ? '' : trim($_GET['cat_name']);
    	$parent_id   = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);
    	/* if (!attr_name) {
    		echo ecm_json_encode(false);
    	}
    	$zujianattr_mod = & m("zujianattr");
    	if ($zujianattr_mod->unique($attr_name, $type_id)) {
    		echo ecm_json_encode(true);
    	}
    	else
    	{
    		echo ecm_json_encode(false);
    	} */
    	echo ecm_json_encode(true);
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
        $gcategories = $this->_partcategory_mod->get_list();
        $tree =& $this->_tree($gcategories);

        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
}

?>
