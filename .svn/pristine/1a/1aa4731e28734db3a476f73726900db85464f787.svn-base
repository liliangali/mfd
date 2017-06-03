<?php

/**
 * 流行趋势分类模型
 * @author liliang
 */
class TrendsCategoryModel extends BaseModel
{
    var $table  = 'trends_category';
    var $prikey = 'cate_id';
    var $alias  = 'tc';
    var $_name  = 'trendscategory';
	
    var $_relation = array(
    		
    		'has_part' => array(
    				'model'         => 'part',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'brand_id',
    				'dependent' => true
    		),
            'belongs_to_store' => array(
            'model'         => 'store',
            'type'          => HAS_ONE,
            'foreign_key'   => 'brand_id',
             ),
    );
    /* 添加编辑时自动验证 */
    var $_autov = array(
        'cate_name' => array(
            'required'  => true,    //必填
            'filter'    => 'trim',
        ),
        'sort_order'  => array(
            'filter'    => 'intval',
        )
    );
	
    
    /* function unique($cate_name, $cate_id = 0)
    {
    	$conditions = "cate_name = '" . $cate_name. "' AND cate_id != ".$cate_id."";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    } */
    
    
    /**
     * 获得分类列表
     * @return array  返回一个维度的数字
     */
    function get_cate()
    {
    	$cate_list = $this->find(array(
    			"conditions"	=>	"1=1",
    			"fields"		=>  "*",
    			"order"			=>  "sort_order asc",
    			));
    	foreach ($cate_list as $k=>$v)
    	{
    		$cate_list[$v['cate_id']] = $v['cate_name'];
    	}
    	return $cate_list;
    }

}

?>