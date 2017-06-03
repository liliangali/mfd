<?php

/* 组件数据模型 */
class PartModel extends BaseModel
{
    var $table  = 'part';
    var $prikey = 'part_id';
    var $alias  = 'p';
    var $_name  = 'part';
    var $_obj_search='part_name|名称,part_sn|编号,code|CODE,ecode|ECODE|equal';
    var $_obj_fields='part_id|ID,part_name|名称,code|编号,price|售价';//,part_small|小图
    //var $_obj_images = 'part_small';
    var $temp; // 临时变量
    var $_relation = array(
    		
    	// 一个组件只能属于一个品牌
    	'belongs_to_brand' => array(
    		'model'         => 'brand',
    		'type'          => BELONGS_TO,
    		'foreign_key'   => 'brand_id',
    		'reverse'       => 'has_part',
    	),
    		
    	// 一个组件对应多个中间表
    	/* 'has_customsparts' => array(
    		'model'         => 'customsparts',
    		'type'          => HAS_ONE,
    		'foreign_key'   => 'cst_id',
    		'refer_key'		=>'cst_id'
    	),
    		// 一个组件对应多个中间表
    		'has_customs' => array(
    				'model'         => 'customs',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'cst_id',
    				'refer_key'		=>'cst_id'
    		), */
    		'be_collect' => array(
    				'model'         => 'customs',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'customs_parts',
    				'foreign_key'   => 'pt_id',
    				
    				'reverse'       => 'collect_customs',
    		),
    		
    );

    var $_autov = array(
        'part_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
    );

  	/**
  	 * @author liliang
  	 * @param string $part_name 组件名称
  	 * @return bool
  	 */
    function unique($part_name,$cate_id,$part_id=0) {
    	$conditions = "part_name = '$part_name'". " AND part_id != ".$part_id." AND cate_id=$cate_id";
    	return (count($this->find($conditions)) == 0);
    }
    
    /**
     * @author liliang
     * @param string $part_sn 组件编号
     */
    function unique_sn($part_sn,$part_id=0) 
    {
    	$conditions = "part_sn = '$part_sn'". " AND part_id != ".$part_id."";
    	return (count($this->find($conditions)) == 0);
    	
    }
    /**
     * 组件code必须唯一
     * @param  
     * @return 
     * @author Ruesin
     */
    function unique_code($code,$part_id=0){
        $conditions = "code = '$code'". " AND part_id != ".$part_id."";
        return (count($this->find($conditions)) == 0);
    }
    /**
     * 获得分类
     */
 	function get_cate($cstId=0,$cateId=0){
    $_gcategory_mod      =& m('gcategory');
        if($cstId!=0){
            if($cateId!=0){
                $cst=$this->get($cstId);
                return $_gcategory_mod->get($cst['sec_cate']);
            }else{
                $cst=$this->get($cstId);
                return $_gcategory_mod->get($cst['cst_cate']);
            }
        }else{
            //ns add 添加是否显示条件
            return  $_gcategory_mod->findAll(array('conditions'=>'parent_id='.$cateId.' AND store_id=0 AND if_show=1 AND categoryid = 1 AND cate_id !=5000','order'=>'sort_order ASC'));
        }
    }
    
    /**
     * 获取店铺列表
     */
    function get_store()
    {
    	$store_mod = m("store");
    	$store_list = $store_mod->find(array(
    			"conditions"	=>	"1=1 ",
    			"fields"		=>  "store_name",
    			"order"			=>	"sort_order asc",
    			));
    	$temp = array();
    	foreach ($store_list as $k=>$v)
    	{
    		$temp[$k] = $v['store_name'];
    	}
    	return $temp;
    }
    
    /**
     * 格式化分类名称
     *
     * @param string $cate_name 用tab键隔开的多级分类名称
     * @return 把tab换成换行符，并且分级缩进
     */
    function format_cate_name($cate_name)
    {
        $arr = explode("\t", $cate_name);
        if (count($arr) > 1)
        {
            for ($i = 0; $i < count($arr); $i++)
            {
                $arr[$i] = str_repeat("&nbsp;", $i * 4) . htmlspecialchars($arr[$i]);
            }
            $cate_name = join("\n", $arr);
        }

        return $cate_name;
    }

   
    /**
     * 删除组件相关数据：包括商品图片、商品缩略图，要在删除商品之前调用 和属性
     *
     * @param   string  $part_ids  组件id，用逗号隔开
     */
    function drop_data($part_ids)
    {

        $images = $this->find(array(
        		
            'conditions' => 'part_id ' . db_create_in($part_ids),
            'fields' => 'part_img, part_thumb',
        ));

        foreach ($images as $image)
        {
            if (!empty($image['part_img']) && trim($image['part_img']) && substr($image['part_img'], 0, 4) != 'http' && file_exists(ROOT_PATH . '/' . $image['part_img']))
            {
                _at(unlink, ROOT_PATH . '/' . $image['part_img']);
            }
            if (!empty($image['part_thumb']) && trim($image['part_thumb']) && substr($image['part_thumb'], 0, 4) != 'http' && file_exists(ROOT_PATH . '/' . $image['part_thumb']))
            {
                _at(unlink, ROOT_PATH . '/' . $image['part_thumb']);
            }
        }
        $part_attr_mode = & m("partattr");
       
        $part_attr_mode->drop("part_id ".db_create_in($part_ids));
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