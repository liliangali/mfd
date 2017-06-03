<?php
/**
 * 相册数据模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: goodsgallery.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package goodsgallery.model.php
 */
class GoodsGalleryModel extends BaseModel
{
    var $table  = 'goods_gallery';
    var $prikey = 'img_id';
    var $alias  = 'gallery';
    var $_name  = 'goodsgallery';

    /* 关系列表 */
    var $_relation  = array(
    		// 一个商品图片只能属于一个商品
    		'belongs_to_goods' => array(
    				'model'         => 'goods',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'goods_id',
    				'reverse'       => 'has_goodsgallery',
    		),
    );
    
    
}

?>