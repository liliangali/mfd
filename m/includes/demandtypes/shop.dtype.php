<?php
/**
 *   商品过来需求
 */
class ShopDemand extends BaseDemand
{
    public $_type;
    function __construct()
    {
        parent::__construct();
        $this->ShopDemand();
    }
    function ShopDemand(){
        $this->_type = 'shop';
    }
    
    function _check_param($arg = array()){
        $gid = intval($arg[1]);
        if(!$gid) return 0;
        $mGoods = &m('goods');
        $goods = $mGoods->get($gid);
        if(empty($goods)) return 0;
        return $goods;
    }
    
    function _get_item_data($arg = array()){
        
        $data = parent::_get_item_data();
        
        $mCate = &m('gcategory');
        $cate  = $mCate->get($arg['cate_id']);

        $reset['list'][-1]['id']   = -$arg['cate_id'];
        $reset['list'][-1]['name'] = $cate['cate_name'];
        $reset['list'][-1]['cate'] = 2;
        
        $data[2] = $reset;
        return $data;
    }
    
    function add($post)
    {
        $params = $this->_get_params($post['params']);
        $post['params']  = $params;
        $post['md_type'] = $this->_type;
        $res = parent::add($post);
        return $res;
    }
    
    function _get_params($post){
        $items = $this->_mod_demanditem->find(array(
                'conditions' => 'id'.db_create_in($post),
        ));
        //..还有通过商品id校验分类id未做
        foreach ($post as $val){
            if($val < 0){
                $items[$val]['id']   = $val;
                $items[$val]['cate'] = 2;
                $mCate = &m('gcategory');
                $cate  = $mCate->get(abs($val));
                $items[$val]['name'] = $cate['cate_name'];
            }
        }
        foreach ($items as $row){
            $res[$row['cate']]['cat'] = $this->item_cate[$row['cate']];
            $res[$row['cate']]['val'] = $row['name'];
            $res[$row['cate']]['id']  = $row['id'];
        }
        asort($res);
        return serialize($res);
    }
    
    function _format_info($info = array()){

        $mGoods = &m('goods');
        $goods = $mGoods->get($info['md_type_id']);
        $info['goods'] = $goods;
        
        parent::_format_info($info);
        
        return $info;
    }
    
}

