<?php
/**
 * 量体师表
 */
class figure_liangtiModel extends BaseModel
{
    var $table  = 'figure_liangti';
    var $prikey = 'id';
    var $_name  = 'figure_liangti';
   
    var $_autov = array(
    );
    
    var $_relation = array(
     
        // 一个店铺属于一个会员
        'belongs_to_user' => array(
            'model'         => 'member',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'liangti_id',
            'reverse'       => 'has_liangti',
        ),
    );
}
?>