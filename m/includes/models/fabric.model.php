<?php

/* 商品数据模型 */
class FabricModel extends BaseModel
{
    var $table  = 'fabric';
    var $prikey = 'ID';
    var $alias  = 'f';
    var $_name  = 'fabric';
    var $_prefix = "diy_";
    var $temp; // 临时变量
    var $_relation = array(
    		
        // 一个商品对应多个规格
        'has_goodsspec' => array(
            'model'         => 'goodsspec',
            'type'          => HAS_MANY,
            'foreign_key'   => 'goods_id',
            'dependent'     => true
        ),
            //一对一从属面料本
            'belongs_to_ftype' => array(
                    'model'         => 'fabrictype',
                    'type'          => HAS_AND_BELONGS_TO_MANY,
                    'index_key'     => 'cate_id',
                    'foreign_key'   => 'type_id',
//                     'reverse'       => 'has_part',
//                     
            ),
        
    );

    var $_autov = array(
    );

  	/**
  	 * @author liliang
  	 * @param string $part_name 组件名称
  	 * @return bool
  	 */
    function unique($part_name,$part_id=0) {
    	$conditions = "name = '$part_name'". " AND fabric_id != ".$part_id."";
    	return (count($this->find($conditions)) == 0);
    }
    
    /**
     * @author liliang
     * @param string $part_sn 组件编号
     */
    function unique_sn($part_sn,$part_id=0) 
    {
    	$conditions = "code = '$part_sn'". " AND fabric_id != ".$part_id."";
    	return (count($this->find($conditions)) == 0);
    	
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
        return parent::edit($conditions, $edit_data);
    }

    function drop($conditions, $fields = '')
    {
        return parent::drop($conditions, $fields);
    }

    
}


?>