<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Goods
{
    
    function __construct($param = []){
        
    }
    
    /**
    *商品列表
    *@author  liang.li
    */
    function lists($limit,$conditions="1=1 ",$cateId=[],$order=0,$sort="",$source="pc",$maketabel=1)
    {
        $goodsMdl = m('goods');
        $sortOrder = "$sort DESC";
        if ($order == 1 && $sort)//===== 销量  ===== 
        {
            $sortOrder = "$sort ASC ";
        }
        if (!$sort) 
        {
            $sortOrder = "p_order ASC, buy_count DESC ,price ASC, uptime DESC ";
        }
        if ($cateId) 
        {
            $gcategoryGoodsMdl = m('categorygoods');
            $cgList = $gcategoryGoodsMdl->find(array(
                'conditions' => db_create_in($cateId,'cate_id'),
            ));
            if (!$cgList)
            {
                return [];
            }
            foreach ($cgList as $key => $value)
            {
                $goodsId[] = $value['goods_id'];
            }
            if ($goodsId)
            {
                $conditions = " ".db_create_in($goodsId,"goods_id");
            }
           if (!$conditions)
           {
               return [];
           }
        }
        if($maketabel)
        {
            $conditions .= " AND marketable = 1";
        }

        if ($source == "pc") 
        {
            $conditions .= " AND is_pc = 1";
        }
        elseif ($source == "app")
        {
            $conditions .= " AND is_app = 1";
        }
        elseif ($source == "app")
        {
            $conditions .= " AND is_wap = 1";
        }
        elseif ($source == "all")
        {

        }
        $list = $goodsMdl->find(array(
        'conditions'    => $conditions,
        'limit'         => $limit,
        'order'         => "$sortOrder",
        'count'         => true
        ));
//         $promotionLib = new Promotion();
       
        
       return $list;
        
        
    }
    
    public function getGoodsInfo($goodsId = 0) 
    {
        if (!$goodsId) 
        {
            return [];
        }
        $goodsMdl = m('goods');
        $goodsGalleryMdl = m('goodsgallery');
        $productsLib = new Products();
        $productsList = $productsLib->getFProducts($goodsId);
        $goodsInfo = $goodsMdl->get_info($goodsId);
       // $galleryList = $goodsGalleryMdl->find("goods_id=$goodsId");
        //---相册----////
        $galleryList=$goodsGalleryMdl->find(array(
            'conditions' =>"goods_id='{$goodsId}'",
            'index_key'=>'',
            'order'=>'sort ASC ,img_id ASC',
        
        ));
        $goodsInfo['gallery_list'] = $galleryList;
        
        $goosAttrLib = new Attr();
        $goodsAttrList = $goosAttrLib->getAttrByGoods($goodsId);
        $goodsInfo['attr_list'] = $goodsAttrList;
        
        $ret['goods'] = $goodsInfo;
        $ret['products'] = $productsList;
        return $ret;
    }
    
    /**
    *关联商品
    *@author  liang.li
    */
    public function getGoodsLink($goodsInfo,$limit = 4)
    {
        if (!$goodsInfo)
        {
            return [];
        }
        $goodsId = $goodsInfo['goods_id'];
        $cat_id = $goodsInfo['cat_id'];
        $gcategoryGoodsMdl = m('categorygoods');
        $cgList = $gcategoryGoodsMdl->find(array(
            'conditions' => "cate_id = $cat_id",
        ));
        
        $cgArr = i_array_column($cgList, "goods_id");
        if ($cgArr) 
        {
            $goodsList = $this->lists($limit,db_create_in($cgArr,"goods_id"));
        }
        return $goodsList;
    }
    
}