<?php
/**
 *   套装需求
 */
class SuitDemand extends BaseDemand
{
    public $_type;
    function __construct()
    {
        parent::__construct();
        $this->SuitDemand();
    }
    function SuitDemand(){
        $this->_type = 'suit';
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

        $res[1]['cat'] = $this->item_cate[1];
        $res[1]['val'] = '套装';
        $res[1]['id']  = -1;
        
        $res[2]['cat'] = $this->item_cate[2];
        $res[2]['val'] = '套装';
        $res[2]['id']  = -1;
        
        $res[3]['cat'] = $this->item_cate[3];
        $res[3]['val'] = '';
        $res[3]['id']  = -1;
        
        
        $price = $this->_mod_demanditem->get($post['price']);
        
        $res[4]['cat'] = $this->item_cate[4];
        $res[4]['val'] = $price['name'];
        $res[4]['id']  = $post['price'];
        
        
        $res[5]['cat'] = $this->item_cate[5];
        $res[5]['val'] = '';
        $res[5]['id']  = -1;

        asort($res);
        return serialize($res);
    }
    
    function _format_info($info = array()){
        $m = &m('dissertation');
        $info['goods'] = $m->get($info['md_type_id']);
        
        parent::_format_info($info);
//         print_r($info);
        return $info;
    }
    
}

