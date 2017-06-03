<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Shipping
{
    
    function __construct($param = [])
    {
        $this->shippingAreaMdl = m('shippingarea');
        $this->shippingMdl = m("shipping");
    }
    
    /**
    *商品列表
    *@author  liang.li
    */
    function lists($limit,$conditions="1=1 ",$cateId=[],$order=0,$sort="") 
    {
        
    }
    
    
    function areaList($condition = "1=1",$inde_key="area_id") 
    {
       
        $areaList = $this->shippingAreaMdl->find(array(
            'conditions' => $condition,
            'index_key'  => $inde_key,
        ));
        return $areaList;
    }
    
    function areaInfo($condition = "1=1")
    {
        $areaList = $this->shippingAreaMdl->get($condition);
        return $areaInfo;
    }
    
    /**
    *content
    *@author  liang.li
    */
    function info($condition = "1=1")
    {
        $areaInfo = $this->shippingMdl->get($condition);
        return $areaInfo;
    }
    
    /**
    *content
    *@author  liang.li
    */
    function del($conditions = "") 
    {
        if (!$conditions) 
        {
            return false;
        }
        $this->shippingAreaMdl->drop($conditions);
    }
    
    /**
     *content
     *@author  liang.li
     */
    function delShippingByPk($shippingId)
    {
        if (!$shippingId)
        {
            return false;
        }
        $this->shippingMdl->drop($shippingId);
        $this->shippingAreaMdl->drop("shipping_id=$shippingId");
    }
    
    /**
    *content
    *@author  liang.li
    */
    function setdefault($shippingId) 
    {
        if (!$shippingId)
        {
            return false;
        }
        $this->shippingMdl->edit("1=1",['is_default'=>0]);
        $this->shippingMdl->edit($shippingId,['is_default'=>1]);
    }
    
    
    /**
    *content
    *@author  liang.li
    */
    function add($data) 
    {
//    echo '<pre>';print_r($data);exit; 
        $this->shippingAreaMdl->add($data);
    }
    
    /**
    *content
    *@author  liang.li
    */
    function edit($shipping_id,$data) 
    {
        if (!$shipping_id) 
        {
           return false ;
        }
        $this->shippingMdl->edit($shipping_id,$data);
    }
    
    
    
    
    
    
    
    
    
    
    
    
}