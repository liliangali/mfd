<?php

/**
 * 品牌数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: brand.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package brand.model.php
 */
/**
 * 品牌数据模型
 * @see BrandModel
 * @version 1.0.0 (2014-11-18)
 * @author liang.li <1184820705@qq.com>
 * @package brand.model.php
 */
class BrandModel extends BaseModel
{
    var $table  = 'brand';
    var $prikey = 'brand_id';
    var $alias  = 'b';
    var $_name  = 'brand';
	
    var $_relation = array(
    		// 一个品牌有多个组件
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
        'brand_name' => array(
            'required'  => true,    //必填
            'min'       => 1,       //最短1个字符
            'max'       => 100,     //最长100个字符
            'filter'    => 'trim',
        ),
        'sort_order'  => array(
            'filter'    => 'intval',
        )
    );

    /**
     *    删除商品品牌
     *
     *    @author    Hyber
     *    @param     string $conditions
     *    @param     string $fields
     *    @return    void
     */
    function drop($conditions, $fields = 'brand_logo')
    {
        $droped_rows = parent::drop($conditions, $fields);
        if ($droped_rows)
        {
            restore_error_handler();
            $droped_data = $this->getDroppedData();
            foreach ($droped_data as $key => $value)
            {
                if ($value['brand_logo'])
                {
                    @unlink(ROOT_PATH . '/' . $value['brand_logo']);  //删除Logo文件
                }
            }
            reset_error_handler();
        }

        return $droped_rows;
    }

        /*
     * 判断名称是否唯一
     */
    function unique($brand_name, $brand_id = 0)
    {
        $conditions = "brand_name = '" . $brand_name . "' AND brand_id != ".$brand_id."";
        //dump($conditions);
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    /* 按标签分类取得所有的品牌   */
    
    function getAllBrands()
    {
        $sql = "SELECT group_concat(brand_id) as brand_ids,COUNT(*) as count,tag FROM {$this->table} WHERE if_show = 1 GROUP BY tag ORDER BY count DESC";
        return $this->db->getAll($sql);
    }
    
    function getIntroBrands(){
    	$sql = "SELECT * FROM {$this->table} WHERE recommended = 1 ORDER BY sort_order ASC LIMIT 10";
    	return $this->db->getAll($sql);
    }
}

?>