<?php
class DemandModel extends BaseModel{
    var $table  = 'demand';
    var $prikey = 'md_id';
    var $alias  = 'dmd';
    var $_relation = array(
        'has_demandoffer' => array(
                'model'         => 'demandoffer',
                'type'          => HAS_MANY,
                'foreign_key'   => 'md_id',
                'dependent'     => true
        ),
    );
    function _status(){
    
        $return = array(
            'all' => '全部状态',
            2 => '进行中',
            3 => '已选择',
            4 => '已完成',
            0 => '已关闭',
        );
        return $return;
    }
    
    function _types(){
        $return = array(
                'normal' => '正常需求',
                'shop'   => '商品需求',
                'lin'    => '面料需求', 
                'show'   => '晒单需求',
                'diy'    => 'Diy需求',
                'suit'   => '套装需求',
        );
        return $return;
    }
    
}
