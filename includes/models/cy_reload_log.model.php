<?php

/* 推荐安装 */
class Cy_reload_logModel extends BaseModel
{
    var $table  = 'cy_reload_log';
    var $prikey = 'id';
    var $_name  = 'cy_reload_log';
    
    
    var $_relation = array(
    
        // 一个记录属于一个 会员 
        'belongs_to_user' => array(
            'model'         => 'member',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'user_id',
            'reverse'       => 'has_reload',
        ),
        
    );


}

?>