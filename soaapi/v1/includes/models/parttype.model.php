<?php

/* 类型表 */
class PartTypeModel extends BaseModel
{
    var $table  = 'part_type';
    var $prikey = 'type_id';
    var $alias  = 'pt';
    var $_name  = 'parttype';

    var $_relation = array(
    		// 一个类型只能属于一个分类
    		'belongs_to_gcategory' => array(
    				'model'         => 'gcategory',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'cate_id',
    				'reverse'       => 'has_type',
    		),
    );
    /* 添加编辑时自动验证 */
    var $_autov = array(
        'type_name' => array(
            'required'  => true,    //必填
            'min'       => 1,       //最短1个字符
            'max'       => 100,     //最长100个字符
            'filter'    => 'trim',
        ),
        
    );

    /**
     * 取得分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @param bool $shown 只取要显示的分类
     * @return array
     */
    function get_list($parent_id = -1, $cate_id=0)
    {
    	if ($cate_id)
    	{
    		$conditions = "1 = 1 and cate_id=$cate_id ";
    	}
    	else 
    	{
    		$conditions = "1 = 1";
    	}
    	
    	$parent_id >= 0 && $conditions .= " AND parent_id = '$parent_id'";
    	
    
    	return $this->find(array(
    			"fields"	=> "type_id as cate_id,cate_id as c_id,type_name as cate_name,sort_order,parent_id",
    			'conditions' => $conditions,
    			'order' => 'sort_order, cate_id',
    	));
    }
    
    /**
     * 供应商取得类型列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @return array
     */
    function get_list_store($parent_id = 0, $cate_id=0)
    {
    	if ($cate_id)
    	{
    		$conditions = "1 = 1 and cate_id=$cate_id AND parent_id = 0 AND is_store=1";
    	}
    	else
    	{
    		$conditions = "1 = 1";
    	}
    	 
    	//$parent_id >= 0 && $conditions .= " AND parent_id = 0 ";
    	 
    
    	return $this->find(array(
    			"fields"	=> "type_id as cate_id,cate_id as c_id,re_name as cate_name,sort_order,parent_id",
    			'conditions' => $conditions,
    			'order' => 'sort_order, cate_id',
    	));
    }
    
    /**
     * 款式风格取得类型列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @return array
     */
	function get_list_style($parent_id = -1, $cate_id=0)
    {
    	if ($cate_id)
    	{
    		$conditions = "1 = 1 and cate_id=$cate_id AND is_store=0";
    	}
    	else 
    	{
    		$conditions = "1 = 1";
    	}
    	
    	$parent_id >= 0 && $conditions .= " AND parent_id = '$parent_id'";
    
    	return $this->find(array(
    			"fields"	=> "type_id as cate_id,cate_id as c_id,type_name as cate_name,sort_order,parent_id",
    			'conditions' => $conditions,
    			'order' => 'sort_order, cate_id',
    	));
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
    				$res[] = array('type_id' => $id, 'type_name' => $data[$id]['name']);
    				$id    = $data[$id]['pid'];
    			}
    		}
    	}
    	else
    	{
    		while ($id > 0)
    		{
    			$sql = "SELECT type_id, type_name, parent_id FROM {$this->table} WHERE type_id = '$id' ";
    			$row = $this->getRow($sql);
    			if ($row)
    			{
    				$res[] = array('type_id' => $row['type_id'], 'type_name' => $row['type_name']);
    				$id    = $row['parent_id'];
    			}
    		}
    	}
    
    	return array_reverse($res);
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
    	//$key  = 'goods_category_' . $this->_store_id;
    	$data = $cache_server->get($key);
    	if ($data === false)
    	{
    		$data = array(0 => array('cids' => array()));
    
    		$cate_list = $this->get_list(-1, true);
    		foreach ($cate_list as $id => $cate)
    		{
    			$data[$id] = array(
    					'name' => $cate['type_name'],
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

?>