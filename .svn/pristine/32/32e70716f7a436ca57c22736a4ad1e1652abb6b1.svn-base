<?php
class DemandofferModel extends BaseModel{
    var $table  = 'demand_offer';
    var $prikey = 'of_id';
    var $alias  = 'dmdof';
    var $_relation = array(
            // 一个商品属性只能属于一个商品
            'belongs_to_demand' => array(
                    'model'         => 'demand',
                    'type'          => BELONGS_TO,
                    'foreign_key'   => 'md_id',
                    'reverse'       => 'has_demandoffer',
            ),
            
    );

}
