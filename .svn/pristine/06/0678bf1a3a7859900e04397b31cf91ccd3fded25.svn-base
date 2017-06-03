<?php

class FigureordermModel extends BaseModel
{
    var $table  = 'figure_order_m';
    var $prikey = 'id';
    var $_name  = 'figure_order_m';


    var $_relation = array(
       'has_order' => array(
                'model'         => 'order',
                'type'          => HAS_ONE,
                'foreign_key'   => 'order_id',
                //'dependent'     => true
            ),
    		'has_liangti' => array(
    				'model'         => 'figure_liangti',
    				'type'          => HAS_ONE,
    				'foreign_key'   => 'liangti_id',
    				'refer_key'     =>'liangti_id',
    				//'dependent'     => true
    		),
        
    );
  

}

