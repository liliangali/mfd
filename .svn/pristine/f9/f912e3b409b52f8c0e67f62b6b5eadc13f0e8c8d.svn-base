<?php


class Generalize_inviteModel extends BaseModel
{
    var $table  = 'generalize_invite';
    var $prikey = 'id';
    var $_name  = 'generalize_invite';


     var $_relation = array(


        'belongs_to_generalize' => array(
            'model'             => 'generalize',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'g_id',
            'reverse'           => 'has_g_member',
        ),






     );
}

?>