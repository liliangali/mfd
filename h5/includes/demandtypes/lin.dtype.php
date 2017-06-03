<?php
/**
 *   面料过来需求
 */
class LinDemand extends BaseDemand
{
    public $_type;
    function __construct()
    {
        parent::__construct();
        $this->LinDemand();
    }
    function LinDemand(){
        $this->_type = 'lin';
    }
    
    function _check_param($arg = array()){
        $gid = intval($arg[1]);
        if(!$gid) return 0;
        $mParts = &m('part');
        $parts = $mParts->get($gid);
        if(empty($parts)) return 0;
        return $parts;
    }
    
    function _get_item_data($arg = array()){
        
        $data = parent::_get_item_data();

        $reset['list'][-1]['id']   = -$arg['part_id'];
        $reset['list'][-1]['name'] = $arg['part_name'];
        $reset['list'][-1]['cate'] = 3;
        
        $data[3] = $reset; //换掉面料
        return $data;
    }
    
    function add($post)
    {
        $params = $this->_get_params($post['params']);
        $post['params']  = $params;
        $post['md_type'] = $this->_type;
        $post['md_type_id']  = $post['type_id'];
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
                $items[$val]['cate'] = 3;
                $mParts = &m('part');
                $parts = $mParts->get(abs($val));
                $items[$val]['name'] = $parts['part_name'];
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
        
        parent::_format_info($info);
        
        if($info['md_type_id']){
            $pid = $info['md_type_id'];
        }else{
            if(abs($info['params'][3]['id'])){
                $pid = abs($info['params'][3]['id']);
            }
        }
        $mParts = &m('part');
        $info['part'] = $mParts->get($pid);
        
        return $info;
    }
    
}

