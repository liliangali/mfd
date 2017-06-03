<?php
/**
 *   普通需求
 */
class NormalDemand extends BaseDemand
{
    function __construct()
    {
        parent::__construct();
        $this->NormalDemand();
    }
    function NormalDemand(){
        $this->_type = 'normal';
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
        foreach ($items as $key => $row){
//             $res[$key]['key'] = $this->item_cate[$row['cate']];
//             $res[$key]['val'] = $row['name'];
            $res[$row['cate']]['key'] = $this->item_cate[$row['cate']];
            $res[$row['cate']]['val'] = $row['name'];
            $res[$row['cate']]['id']  = $row['id'];
        }
        return serialize($res);
    }
    
    function _format_info($info = array()){
        
        parent::_format_info($info);
        
        return $info;
    }
    
}

