<?php

/* 组件-属性 中间表模型 */
class PartAttrModel extends BaseModel
{
    var $table  = 'part_attr';
    var $prikey = 'part_attr_id';
    var $alias  = 'v';
    var $_name  = 'partattr';
    var $temp; // 临时变量
    
    var $_autov = array(
        'part_id' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
    );
	
    
    var $_relation = array(
    		// 多个中间表数据对应一条数据
    		 // 一个商品属性只能属于一个商品
        'belongs_to_partattr' => array(
            'model'         => 'partattribute',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'attr_id',
            'reverse'       => 'has_partattri',
        ),
    		
    );

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

   
    
}


?>