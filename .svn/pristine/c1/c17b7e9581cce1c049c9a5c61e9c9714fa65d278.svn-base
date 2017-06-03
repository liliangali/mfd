<?php

/* 商品分类 gcategory */
class FbcategoryModel extends BaseModel
{
    var $table  = 'fbcategory';
    var $prikey = 'cate_id';
     var $alias  = 'fbc';
    var $_name  = 'fbcategory';
    var $_relation  = array(

    );

    var $_autov = array(
        'cate_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
        'parent_id' => array(
        ),
        'sort_order' => array(
            'filter'    => 'intval',
        ),
        'if_show' => array(
        ),
    );

    /**
     * 获取工艺信息
     * @return array()
     * 'conditions'=>db_create_in($cateIds,'cate_id'),
     * $conditions .= " AND parent_id = '$parent_id'";
     */
    public  function _get_data($conditions='',$cache=''){
        $res = $p_c_data = array();
        if ($cache)
        {
            $conditions = empty($conditions) ? " if_show = 1 " : $conditions;
            $res = $this->find(array('conditions'=>$conditions,'fields'=> "*", 'order' => 'sort_order asc, cate_id'));
            return $res;
        }

        $cache_server =& cache_server();
        $key = 'page_of_fdiy_fbcategory_'.md5($conditions);
        $p_c_data = $cache_server->get($key);
        $p_c_data = false;
        if ($p_c_data === false){
            $conditions = empty($conditions) ? "  if_show = 1 " : $conditions;
            $res = $this->find(array('conditions'=>$conditions,'fields'=> "*",'order' => 'sort_order asc, cate_id'));
            $cache_server->set($key, $res, 1800);
            return $res;
        }
        return $p_c_data;
    }
    
    /**
     * 生成嵌套格式的树形数组
     * @param arrary 	$gcategories	数据源
     * @param int 		$root			父节点
     * @return array|false				array(..."children"=>array(..."children"=>array(...)))
     * @author 小五
     */
    function deep_tree($gcategories,$root=0){
        //     	var_dump($gcategories);exit();
        if(!$gcategories){
            return FALSE;
        }
        $pk="cate_id";
        $parentKey="parent_id";
        $childrenKey="children";
        $tree=array();//最终数组
        $refer=array();//存储主键与数组单元的引用关系
        //遍历
        foreach($gcategories as $k=>$v){
            if(!isset($v[$pk]) || !isset($v[$parentKey]) || isset($v[$childrenKey])){
                unset($gcategories[$k]);
                continue;
            }
            $refer[$v[$pk]]=&$gcategories[$k];//为每个数组成员建立引用关系
        }
        //遍历子节点
        foreach($gcategories as $k=>$v){
            if($v[$parentKey]==$root){//根分类直接添加引用到tree中
                $tree[$gcategories[$k]['cate_id']]=&$gcategories[$k];
            }else{
                if(isset($refer[$v[$parentKey]])){
                    $parent=&$refer[$v[$parentKey]];//获取父分类的引用
                    $parent[$childrenKey][$gcategories[$k]['cate_id']]=&$gcategories[$k];//在父分类的children中再添加一个引用成员
                }
            }
        }
        return $tree;
    }
    /**
     * 取得分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @param bool $shown 只取要显示的分类
     * @return array
     */
    function get_list($parent_id = -1, $shown = false)
    {
        $conditions = "1 = 1";
        if($parent_id>0){
        	$conditions .= " AND parent_id = '$parent_id'";
        }else if($parent_id==0){
        	$conditions .= " AND parent_id > 0";
        }
        $shown && $conditions .= " AND if_show = 1 ";
        return $this->find(array(
            'conditions' => $conditions,
            'order' => 'sort_order ASC, cate_id ASC',
        ));
    }

    /**
     * 取得分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @param bool $shown 只取要显示的分类
     * @return array
     */
    function get_dog_list()
    {
        $dog_list = $mod_fbcategory->find(array(
            'conditions' => "parent_id = 21 ",
            'index_key' => "cate_id",
        ));
    }


    
    /**
     * 取得分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @param bool $shown 只取要显示的分类
     * @return array
     */
    function get_list_goods($parent_id = -1, $shown = false)
    {
    	$conditions = "1 = 1";
    	$parent_id >= 0 && $conditions .= " AND (parent_id = '$parent_id' OR cate_id=$parent_id)";
    	$shown && $conditions .= " AND if_show = 1 ";
    	return $this->find(array(
    			'conditions' => $conditions,
    			'order' => 'sort_order, cate_id',
    	));
    }

    function get_options($parent_id = -1, $shown = false)
    {
        $options = array();
        $rows = $this->get_list($parent_id, $shown);
        foreach ($rows as $row)
        {
            $options[$row['cate_id']] = $row['cate_name'];
        }

        return $options;
    }
    /*
     * 判断是否可以有上下级分类
     */
    function parent_children_valid($parent_id)
    {
        $fdiy_management = $this->get_info($parent_id);
        if($fdiy_management['code'] == ACC_SYSTEM || $fdiy_management['code'] == ACC_NOTICE || $fdiy_management['code'] == ACC_HELP)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    

    /**
     * 取得某分类的所有子孙分类id（不推荐使用，但是因为调用模块和专题模块中用到了，所以暂时保留，推荐使用业务模型中的相关函数）
     */
    function get_descendant($id)
    {
        $ids = array($id);
        $ids_total = array();
        $this->_get_descendant($ids, $ids_total);
        return array_unique($ids_total);
    }
    function _get_descendant($ids, &$ids_total)
    {
        $childs = $this->find(array(
            'fields' => 'cate_id',
            'conditions' => "parent_id " . db_create_in($ids)
        ));
        $ids_total = array_merge($ids_total, $ids);
        $ids = array();
        foreach ($childs as $child)
        {
            $ids[] = $child['cate_id'];
        }
        if (empty($ids))
        {
            return ;
        }
        $this->_get_descendant($ids, $ids_total);
    }
    /*
     * 判断产地是否已被使用，不可重复添加
     */
    function unuser_region($region_items)
    {
    	$arr=$this->find(array(
    			'fields'=>'DISTINCT cate_name',
    	));
    	foreach ($arr as $k=>$v){
    		$cate_name=$v['cate_name'];
    		if($region_items[$cate_name]){
    			unset($region_items[$cate_name]);
    		}
    	}
    	return $region_items;
    }
    /* 获取上级分类地区 */
    function get_region($id){
    	$region=$this->get("cate_id={$id}");
    	if(empty($region)){
    		return false;
    	}
    	return $region['cate_name']; 
    }
    
    /*
     * 判断名称是否唯一
     */
    function unique($cate_name, $parent_id, $cate_id = 0)
    {
    	$conditions = "parent_id = '$parent_id' AND cate_name = '$cate_name'";
    	$cate_id && $conditions .= " AND cate_id <> '" . $cate_id . "'";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    /**
     * 取得某分类的深度（没有子节点深度为1）
     *
     * @param   int     $id     分类id（需保证是存在的）
     * @return  int     深度
     */
    function get_depth($id)
    {
    	$depth = 0;
    
    	$pids = array($id);
    	while ($pids)
    	{
    		$depth++;
    		$sql  = "SELECT cate_id FROM {$this->table} " . $this->_getConditions("parent_id " . db_create_in($pids));
    		$pids = $this->getCol($sql);
    	}
    
    	return $depth;
    }
    
    /**
     * 取得某分类的子分类
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(
     *              array('cate_id' => 1, 'cate_name' => '数码产品'),
     *              array('cate_id' => 2, 'cate_name' => '手机'),
     *              ...
     *          )
     */
    function get_children($id, $cached = false)
    {
    	$res = array();
    
    	if ($cached)
    	{
    		$data = $this->_get_structured_data();
    		if (isset($data[$id]))
    		{
    			$cids = $data[$id]['cids'];
    			foreach ($cids as $id)
    			{
    				$res[$id] = array(
    						'cate_id'   => $id,
    						'cate_name' => $data[$id]['name'],
    				);
    			}
    		}
    	}
    	else
    	{
    		$sql = "SELECT cate_id, cate_name FROM {$this->table} WHERE parent_id = '$id' and if_show =1 ORDER BY sort_order ASC";
    		$res = $this->getAll($sql);
    	}
    
    	return $res;
    }
    
    /**
     * 取得某分类的祖先分类（包括自身，按层级排序）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(
     *              array('cate_id' => 1, 'cate_name' => '数码产品'),
     *              array('cate_id' => 2, 'cate_name' => '手机'),
     *              ...
     *          )
     */
    function get_ancestor($id, $cached = false)
    {
    	$res = array();
    
    	if ($cached)
    	{
    		$data = $this->_get_structured_data();
    		if ($id > 0 && isset($data[$id]))
    		{
    			while ($id > 0)
    			{
    				$res[] = array('cate_id' => $id, 'cate_name' => $data[$id]['name']);
    				$id    = $data[$id]['pid'];
    			}
    		}
    	}
    	else
    	{
    		while ($id > 0)
    		{
    			$sql = "SELECT cate_id, cate_name, parent_id FROM {$this->table} WHERE cate_id = '$id'";
    			$row = $this->getRow($sql);
    			if ($row)
    			{
    				$res[] = array('cate_id' => $row['cate_id'], 'cate_name' => $row['cate_name']);
    				$id    = $row['parent_id'];
    			}
    		}
    	}
    
    	return array_reverse($res);
    }
    
    function get_parent_id($id){
    	$sql = "SELECT parent_id FROM {$this->table} WHERE cate_id = '$id'";
    	$row = $this->getRow($sql);
    	if($row["parent_id"] == 0){
    		return $id;
    	}else{
    		return $this->get_parent_id($row["parent_id"]);
    	}
    }
    
    /**
     * 取得某分类的层级（从1开始算起）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  int     层级     当分类不存在或不显示时返回false
     */
    function get_layer($id, $cached = false)
    {
    	$ancestor = $this->get_ancestor($id, $cached);
    	if (empty($ancestor))
    	{
    		return false; //分类不存在或不显示
    	}
    	else
    	{
    		return count($ancestor);
    	}
    }
    
    /**
     * 取得某分类的子孙分类id（包括自身id）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(1, 2, 3, ...)
     */
    function get_descendant_ids($id, $cached = false)
    {
    	$res = array($id);
    
    	if ($cached)
    	{
    		$data = $this->_get_structured_data();
    		if ($id > 0 && isset($data[$id]))
    		{
    			$i = 0;
    			while ($i < count($res))
    			{
    				$id  = $res[$i];
    				$res = array_merge($res, $data[$id]['cids']);
    				$i++;
    			}
    		}
    	}
    	else
    	{
    		$cids = array($id);
    		while (!empty($cids))
    		{
    			$sql  = "SELECT cate_id FROM {$this->table} WHERE parent_id " . db_create_in($cids);
    			$cids = $this->getCol($sql);
    			$res  = array_merge($res, $cids);
    		}
    	}
    
    	return $res;
    }
    
    /**
     * 获得option拼接起来的字符串
     * @version 1.0.0
     * @author liang.li <1184820705@qq.com>
     * @2015-1-21
     */
    function get_options_html($parent_id,$cur_region_id)
    {
    	$res = array();
    	$html = "";
    	$regions = $this->get_list($parent_id);
    	if ($regions)
    	{
    		foreach ($regions as $key => $value)
    		{
    			if ($key == $cur_region_id)
    			{
    				$html .= "<option value=".$key." selected>".$value['cate_name']."</option>";
    			}
    			else
    			{
    				$html .= "<option value=".$key.">".$value['cate_name']."</option>";
    			}
    
    		}
    	}
    	return $html;
    }
    
    /**
     * 取得结构化的分类数据（不包括不显示的分类，数据会缓存）
     *
     * @return array(
     *      0 => array(                             'cids' => array(1, 2, 3))
     *      1 => array('name' => 'abc', 'pid' => 0, 'cids' => array(2, 3, 4)),
     *      2 => array('name) => 'xyz', 'pid' => 1, 'cids' => array()
     * )
     *    分类id        分类名称             父分类id     子分类ids
     */
    function _get_structured_data()
    {
    	$cache_server =& cache_server();
    	$key  = 'goods_category_0';
    	$data = $cache_server->get($key);
    	if ($data === false)
    	{
    		$data = array(0 => array('cids' => array()));
    
    		$cate_list = $this->get_list(-1, true);
    		foreach ($cate_list as $id => $cate)
    		{
    			$data[$id] = array(
    					'name' => $cate['cate_name'],
    					'pid'  => $cate['parent_id'],
    					'cids' => array()
    			);
    		}
    
    		foreach ($cate_list as $id => $cate)
    		{
    			$pid = $cate['parent_id'];
    			isset($data[$pid]) && $data[$pid]['cids'][] = $id;
    		}
    
    		$cache_server->set($key, $data, 1800);
    	}
    
    	return $data;
    }
    
}

/* 商品分类业务模型 */
class FbcategoryBModel extends FbcategoryModel
{
    var $_store_id = 0;

    /*
     * 判断名称是否唯一
     */
    function unique($cate_name, $parent_id, $cate_id = 0)
    {
        $conditions = "parent_id = '$parent_id' AND cate_name = '$cate_name'";
        $cate_id && $conditions .= " AND cate_id <> '" . $cate_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    /*
     * 判断产地是否已被使用，不可重复添加
     */
    function unuser_region($region_items)
    {
    	$arr=$this->find(array(
    		'fields'=>'DISTINCT cate_name',	
    	));
    	foreach ($arr as $k=>$v){
    		$cate_name=$v['cate_name'];
    		if($region_items[$cate_name]){
    			unset($region_items[$cate_name]);
    		}
    	}
    	return $region_items;
    }
    
    
    /* 覆盖基类方法 */
    function add($data, $compatible = false)
    {
        $this->clear_cache();

        return parent::add($data, $compatible);
    }

    function edit($conditions, $edit_data)
    {
        $this->clear_cache();

        return parent::edit($conditions, $edit_data);
    }

    function drop($conditions, $fields = '')
    {
        $this->clear_cache();

        return parent::drop($conditions, $fields);
    }

    /* 覆盖基类方法 */
    function _getConditions($conditions, $if_add_alias = false)
    {
        $alias = '';
        if ($if_add_alias)
        {
            $alias = $this->alias . '.';
        }
        $res = parent::_getConditions($conditions, $if_add_alias);
        return $res ? $res : "";
    }
    
    function is_leaf($id)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} " . $this->_getConditions("parent_id = '$id'");

        return $this->getOne($sql) == 0;
    }

    /**
     * 取得某分类的深度（没有子节点深度为1）
     * 
     * @param   int     $id     分类id（需保证是存在的）
     * @return  int     深度
     */
    function get_depth($id)
    {
        $depth = 0;

        $pids = array($id);
        while ($pids)
        {
            $depth++;
            $sql  = "SELECT cate_id FROM {$this->table} " . $this->_getConditions("parent_id " . db_create_in($pids));
            $pids = $this->getCol($sql);
        }

        return $depth;
    }

    /**
     * 取得某分类的子分类
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(
     *              array('cate_id' => 1, 'cate_name' => '数码产品'),
     *              array('cate_id' => 2, 'cate_name' => '手机'),
     *              ...
     *          )
     */
    function get_children($id, $cached = false)
    {
        $res = array();

        if ($cached)
        {
            $data = $this->_get_structured_data();
            if (isset($data[$id]))
            {
                $cids = $data[$id]['cids'];
                foreach ($cids as $id)
                {
                    $res[$id] = array(
                        'cate_id'   => $id, 
                        'cate_name' => $data[$id]['name'],
                    );
                }
            }
        }
        else
        {
            $sql = "SELECT cate_id, cate_name FROM {$this->table} WHERE parent_id = '$id' ORDER BY sort_order ASC";
            $res = $this->getAll($sql);
        }

        return $res;
    }

    /**
     * 取得某分类的祖先分类（包括自身，按层级排序）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(
     *              array('cate_id' => 1, 'cate_name' => '数码产品'),
     *              array('cate_id' => 2, 'cate_name' => '手机'),
     *              ...
     *          )
     */
    function get_ancestor($id, $cached = false)
    {
        $res = array();

        if ($cached)
        {
            $data = $this->_get_structured_data();
            if ($id > 0 && isset($data[$id]))
            {
                while ($id > 0)
                {
                    $res[] = array('cate_id' => $id, 'cate_name' => $data[$id]['name']);
                    $id    = $data[$id]['pid'];
                }
            }
        }
        else
        {
            while ($id > 0)
            {
                $sql = "SELECT cate_id, cate_name, parent_id FROM {$this->table} WHERE cate_id = '$id'";
                $row = $this->getRow($sql);
                if ($row)
                {
                    $res[] = array('cate_id' => $row['cate_id'], 'cate_name' => $row['cate_name']);
                    $id    = $row['parent_id'];
                }
            }
        }

        return array_reverse($res);
    }

    function get_parent_id($id){
        $sql = "SELECT parent_id FROM {$this->table} WHERE cate_id = '$id'";
        $row = $this->getRow($sql);
        if($row["parent_id"] == 0){
            return $id;
        }else{
            return $this->get_parent_id($row["parent_id"]);
        }
    }
    
    /**
     * 取得某分类的层级（从1开始算起）
     * 
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  int     层级     当分类不存在或不显示时返回false
     */
    function get_layer($id, $cached = false)
    {
        $ancestor = $this->get_ancestor($id, $cached);
        if (empty($ancestor))
        {
            return false; //分类不存在或不显示
        }
        else
        {
            return count($ancestor);
        }        
    }
    
    /**
     * 取得某分类的子孙分类id（包括自身id）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(1, 2, 3, ...)
     */
    function get_descendant_ids($id, $cached = false)
    {
        $res = array($id);

        if ($cached)
        {
            $data = $this->_get_structured_data();
            if ($id > 0 && isset($data[$id]))
            {
                $i = 0;
                while ($i < count($res))
                {
                    $id  = $res[$i];
                    $res = array_merge($res, $data[$id]['cids']);
                    $i++;
                }
            }
        }
        else
        {
            $cids = array($id);
            while (!empty($cids))
            {
                $sql  = "SELECT cate_id FROM {$this->table} WHERE parent_id " . db_create_in($cids);
                $cids = $this->getCol($sql);
                $res  = array_merge($res, $cids);
            }
        }

        return $res;
    }


    /**
     * 取得结构化的分类数据（不包括不显示的分类，数据会缓存）
     *
     * @return array(
     *      0 => array(                             'cids' => array(1, 2, 3))
     *      1 => array('name' => 'abc', 'pid' => 0, 'cids' => array(2, 3, 4)),
     *      2 => array('name) => 'xyz', 'pid' => 1, 'cids' => array()
     * )
     *    分类id        分类名称             父分类id     子分类ids
     */
    function _get_structured_data()
    {
        $cache_server =& cache_server();
        $key  = 'goods_category_0';
        $data = $cache_server->get($key);
        if ($data === false)
        {
            $data = array(0 => array('cids' => array()));

            $cate_list = $this->get_list(-1, true);
            foreach ($cate_list as $id => $cate)
            {
                $data[$id] = array(
                    'name' => $cate['cate_name'],
                    'pid'  => $cate['parent_id'],
                    'cids' => array()
                );
            }

            foreach ($cate_list as $id => $cate)
            {
                $pid = $cate['parent_id'];
                isset($data[$pid]) && $data[$pid]['cids'][] = $id;
            }

            $cache_server->set($key, $data, 1800);
        }

        return $data;
    }

    /* 清除缓存 */
    function clear_cache()
    {
    	// ns add 缓存暂时不需要
    	return null;
        $cache_server =& cache_server();
        $keys = array('goods_category_' . $this->_store_id, 'page_goods_category', 'function_get_store_data_' . $this->_store_id);
        foreach ($keys as $key)
        {
            $cache_server->delete($key);
        }
    }
}

?>