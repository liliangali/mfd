<?php
class DemanditemModel extends BaseModel{
    var $table  = 'demand_item';
    var $prikey = 'id';
    var $_name  = 'demand_item';
    var $alias  = 'demand_item';
    

    function _item_cates(){
        
        $return = array(
                1 => '主题风格',
                2 => '品类',
                3 => '面料',
                4 => '定制预算',
                5 => '尺寸号码',
        );
        return $return;
    }
}
