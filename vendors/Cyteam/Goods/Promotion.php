<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Promotion
{
    
    function __construct($param = [])
    {
        
    }
    
    //取得所有规则
    //pc段：1  app：2 wap 3
    function getGoodsPromotion(&$item,$platform='1')
    {
        if (!isset($item['goods_id'])) return ;
        if (!isset($item['user_id'])) 
        {
            $member_lv_id = 1;
        }
        else 
        {
            $mMod = m('member');
            $mInfo = $mMod->get_info($item['user_id']);
            $member_lv_id = $mInfo['member_lv_id'];
        }
    
        
        $mRule = & m('goods_prorule');
        $time = time();
        $res = $mRule->find(array(
            'conditions' => "is_open=1 and find_in_set('".$member_lv_id."',member_lv_id) and find_in_set('".$platform."',site_id) and starttime < ".$time." and endtime > ".$time,
            'order'=>'level desc',
        ));
        $defArr = ['4'=>'all','3'=>'category','1'=>'type','2'=>'goods',];
        //优惠条件(1:商品类型2指定商品3商品分类4所有商品)
        foreach ((array)$res as $row)
        {
            if (isset($defArr[$row['favorable']]))
            {
                if (isset($rules[$defArr[$row['favorable']]]))
                {
                    foreach ($rules[$defArr[$row['favorable']]] as $val)
                    {
                        if ($val['if_ex'] && 1 == $val['if_ex']) continue 2;
                    }
                }
    
                if ($row['if_ex'])
                {
                    unset($rules[$defArr[$row['favorable']]]);
                }
                $rules[$defArr[$row['favorable']]][$row['level']] = $row;
    
            }
        }
        foreach ($defArr as $k=>$v)
        {
            if (isset($rules[$v]))
            {
                $key = array_search(max($rules[$v]), $rules[$v]);
                $item['promotion'] = $rules[$v][$key];
                $item['promotion']['ty'] = $v;
                $item['promotion']['tid'] = $k;
                break;
            }
        }
        if ($item['promotion']['ty'] !== 'all')
        {
            if(!$this->isPromotion($item,$item['promotion']))
            {
                return ;
            }
        }
        $data = $this->formatPromotion($item);
        $item['promotion']['yhcase_name'] = $data['name'];
        $item['params']['oProducts']['price'] = $data['price'];
    }
    
    
    
}