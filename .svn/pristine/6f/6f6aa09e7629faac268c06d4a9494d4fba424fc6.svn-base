<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Products
{
    
    function __construct($param = []){
        
    }
    
    
    /**
    *content
    *@author  liang.li
    */
    function getProducts($goodsId,$is_romotion = 1) 
    {
        $productsMdl = m('products');
        $productsList = $productsMdl->find(array(
            'conditions' => "goods_id = $goodsId"
        ));
        
        if ($is_romotion) 
        {
            $productsList = $this->fPromotion($productsList, $goodsId);
        }
       return $productsList;
    }
    
    /**
    *content
    *@author  liang.li
    */
    function fPromotion($productsList,$goodsId,$user_id = 0) 
    {
        $goodsMdl = m('goods');
        $goodsInfo = $goodsMdl->get_info($goodsId);
        import("promotion.lib");
        $promotionLib = new \Promotion();
        if (!$user_id)
        {
            $user_id = $_SESSION['user_info']['user_id'];
            $item['user_id'] = $user_id;
        }
//  print_exit($productsList);
        if ($productsList)
        {
            foreach ($productsList as $key => &$value)
            {
                $item['goods_id'] = $goodsId;
                $item['params']['oProducts'] = $value;
                $item['params']['oGoods'] = $goodsInfo;
//       print_exit($item);
                $res = $promotionLib->getGoodsPromotion($item);
//     print_exit($item);
                $value['price'] = $item['params']['oProducts']['price'];
                $value['is_pro'] = $item['params']['oProducts']['is_pro'];
                if ($item['params']['oProducts']['is_pro'])
                {
                    $end_time = $item['promotion']['endtime'];
                    $value['end_time'] = $end_time;
                    $value['pro_info'] = $item['promotion'];
                }
            }
        }
        return $productsList;
    }
    
    /**
     * 
     * @param number $goodsId
     */
    
    public function getFProducts($goodsId,$is_romotion = 1) 
    {
        if (!$goodsId) 
        {
            return [];
        }
        $specificationMdl = m("specification");
        $specValuesMdl = m("specvalues");
        $productsList = $this->getProducts($goodsId,$is_romotion);
        $speList = $specificationMdl->find();
        $imgSpecList = $specValuesMdl->find();
        $dList = [];
        $productDefault = [];
        foreach ((array)$productsList as $key => $value) 
        {
            if ($value['spec_desc']) 
            {
                $spec = unserialize($value['spec_desc']);
                $productsList[$key]['spec_desc'] = $spec;
                foreach ($spec['spec_value_id'] as $key1 => $value1) 
                {
                   $bak = [];
                   $tmp[$key1]['name'] = $speList[$key1]['spec_name'];
                   if ($speList[$key1]['spec_type'] == "image") 
                   {
                       $speImg[$value1] = $value1;
                   } 
                   $tmp[$key1]['spec_type'] = $speList[$key1]['spec_type'];
                   $tmp[$key1]['value'][$value1]['value_name'] = $spec['spec_value'][$key1];
                   if ($value['is_default']) 
                   {
                       $tmp[$key1]['value'][$value1]['is_default'] = $value['is_default'];
                       $default_product_id = $key;
                   }
                   if ($speList[$key1]['spec_type'] == 'image') 
                   {
                       $tmp[$key1]['value'][$value1]['iamge'] = $imgSpecList[$key1]['spec_image'];
                   }
                }
                
               
            }
           
            
        }
        
        if ($speImg) 
        {
            $imgSpecList = $specValuesMdl->find(array(
                'conditions' => db_create_in($speImg,"spec_value_id"),
                "fields"     => "spec_image",
            ));
            foreach ((array)$imgSpecList as $key => $value) 
            {
                $img[$key] = $value['spec_image'];
            }
            
        }
        
        foreach ((array)$tmp as $key => $value) 
        {
            foreach ($value as $key1 => $value1) 
            {
                ;
            }
        }
        $ret['pd'] = $productsList;
        $ret['spe'] = $tmp;
        $ret['default_product_id'] = $default_product_id;
        
        return $ret;
    }
    
    
    function getInfoById($productId) 
    {
        $productsMdl = m('products');
        return $productsMdl->get_info($productId);
    }
    
}