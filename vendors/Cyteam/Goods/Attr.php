<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Attr
{
    
    function __construct($param = []){
        
    }
    
    
    public function getAttrByGoods($goodsId = 0) 
    {
        if (!$goodsId) 
        {
            return [];
        }
        $goodsAttrMdl = m('goodsattr');
        $goodsAttributeMdl = m('goodsattribute');
        $goodsGalleryMdl = m('goodsgallery');
        
        $goodsAttrList = $goodsAttrMdl->find(array(
            'conditions' => "goods_id = $goodsId"
        ));
        $ret = [];
        if ($goodsAttrList) 
        {
            $goodsAttributeList = $goodsAttributeMdl->find();
            foreach ($goodsAttrList as $key => $value) 
            {
                $p['p_name'] = $goodsAttributeList[$value['attr_id']]['attr_name'];
                $p['s_name'] = $value['attr_value'];
                $ret[] = $p;
            }
        }
        return $ret;
    }
    
}