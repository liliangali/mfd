<?php
namespace Cyteam\Goods;

class Gcategory
{
    
    function __construct($param = []){
        
    }
    
    /**
    *商品列表
    *@author  liang.li
    */
    function lists($pId) 
    {
       $goodsMdl = m('gcategory');
       $list = $goodsMdl->get_list($pId,true);
       return $list;
    }
    
   
}