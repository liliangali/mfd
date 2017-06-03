<?php

!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    需求基类
 *
 *    @author    Ruesin
 *    
 */
class BaseDemand extends Object
{
    public $_mod_demand;
    public $_mod_demanditem;
    public $item_cate;
    function __construct()
    {
        $this->BaseDemand();
        
    }
    
    function BaseDemand()
    {
        
        $this->_mod_demand = &m('demand');
        $this->_mod_demanditem = &m('demanditem');
        $this->item_cate = $this->_mod_demanditem->_item_cates();
    }
    
    function add($post)
    {
        $sn     = $this->_gen_sn();
        
        $rank = $this->_price_rank($post['params']);
        
        $data = array(
                'md_sn'       => $sn,
                'md_title'    => $post['md_title'],
                'md_type'     => $post['md_type'],
                'md_type_id'  => $post['md_type_id'],
                'params'      => $post['params'],
                'price_rank'  => $rank,
                'user_id'     => $post['user_id'],
                'status'      => $post['status'],
                'uname'       => $post['uname'],
                'region_id'   => $post['region']['id'],
                'region_name' => $post['region']['name'],
                'region_code' => $post['region_code'],
                'mobile'      => $post['contact']['mobile'],
                'qq'          => $post['contact']['qq'],
                'email'       => $post['contact']['email'],
                'remark'      => $post['remark'],
                'adjunct'     => $post['adjunct'],
                'add_time'    => gmtime(),
                'last_time'   => gmtime(),
        );
        $res = $this->_mod_demand->add($data);
        return $res;
    }
    
    function _check_param(){
        
        return true;
    }
    
    function _get_item_data($arg = array()){
    
        $items = $this->_mod_demanditem->find(array(
                'conditions' => '1 = 1 ',
        ));
    
        foreach ($items as $key=>$item){
            $data[$item['cate']]['list'][$key] = $item;
        }
        return $data;
    }
    function _format_info(&$info = array()){
        $info['params']   = unserialize($info['params']);
        
    }
    
    function _price_rank($params){
        $arr = unserialize($params);
        $row = $this->_mod_demanditem->get($arr[4]['id']);
        $rank = $row['rank'];
        return $rank;
    }
    
    function _gen_sn(){
        mt_srand((double) microtime() * 1000000);
        $timestamp = gmtime();
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    
        $data = $this->_mod_demand->find("md_sn = '{$sn}'");
        if (empty($data))
        {
            return $sn;
        }
        return $this->_gen_sn();
    }

}
