<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Region
{
    
    function __construct($param = []){
        
    }
    
    /**
    *商品列表
    *@author  liang.li
    */
    function lists() 
    {
       $regionMdl = m('region');
       $regionList = $regionMdl->find(array(
           'conditions' => "parent_id != 0",
       ));
       return $regionList;
    }
    
    /**
    *content
    *@author  liang.li
    */
    function getInfo($regionId) 
    {
        $regionMdl = m('region');
        $regionInfo = $regionMdl->get_info($regionId);
        return $regionInfo;
    }
    
    /**
     *content
     *@author  liang.li
     */
    function getInfoByCode($cityCode)
    {
        if (!$cityCode) 
        {
            return ;
        }
        $regionMdl = m('region');
        $regionInfo = $regionMdl->get("citycode = $cityCode");
        return $regionInfo;
    }
    
    
    /**
     *content
     *@author  liang.li
     */
    function getInfoByPid($pid)
    {
        if (!$pid)
        {
            return ;
        }
        $regionMdl = m('region');
        $regionInfo = $regionMdl->find(array(
            'conditions' => "parent_id = $pid"
        ));
        return $regionInfo;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}