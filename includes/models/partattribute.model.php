<?php

/* 组件属性表模型 */
class PartAttributeModel extends BaseModel
{
    var $table  = 'part_attribute';
    var $prikey = 'attr_id';
    var $alias  = 'g';
    var $_name  = 'partattribute';
    var $temp; // 临时变量
    
    var $_relation = array(
    		// 一个组件对应多个属性
        'has_partattri' => array(
            'model'         => 'partattr',
            'type'          => HAS_MANY,
            'foreign_key'   => 'attr_id',
            'dependent'     => true
        ),
    );
    
    var $_autov = array(
        'attr_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
    );
	
    /*
     * 判断名称是否唯一
    */
    function unique($attr_name, $type_id = 0)
    {
    	$conditions = "attr_name = '" . $attr_name . "' AND type_id = ".$type_id."";
    	//dump($conditions);
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }

    /* 清除缓存 */
    function clear_cache($goods_id)
    {
        $cache_server =& cache_server();
        $keys = array('page_of_goods_' . $goods_id);
        foreach ($keys as $key)
        {
            $cache_server->delete($key);
        }
    }

    function edit($conditions, $edit_data)
    {
        /* 清除缓存 */
        $goods_list = $this->find(array(
            'fields'     => 'attr_id',
            'conditions' => $conditions,
        ));
        foreach ($goods_list as $goods)
        {
            $this->clear_cache($goods['attr_id']);
        }

        return parent::edit($conditions, $edit_data);
    }

    function drop($conditions, $fields = '')
    {
        /* 清除缓存 */
        $goods_list = $this->find(array(
            'fields'     => 'attr_id',
            'conditions' => $conditions,
        ));
        foreach ($goods_list as $goods)
        {
            $this->clear_cache($goods['attr_id']);
        }
        /* 清除店铺商品数缓存 */
        /* $cache_server =& cache_server();
        $cache_server->delete('goods_count_of_store');*/
 
        return parent::drop($conditions, $fields);
    }

   
}


?>