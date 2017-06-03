<?php

/* 主题与基本款关连表 */
class Generalize_memberModel extends BaseModel
{
    var $table  = 'generalize_member';
    var $prikey = 'id';
    var $_name  = 'g_member';

    var $_autov = array(
        'name'  => array(
            'filter'    => 'trim',
            'required'  => true,
        ),
        'phone'  => array(
            'reg'   => '/\d{6}/',      //至少6位的数字
            'required'  => true,
        ),
        'g_id'  => array(
            'required'  => true,
        ),

        'type'  => array(
            'required'  => true,
        ),
        'gender'  => array(
            'required'  => true,
        ),
        'status'  => array(
            'required'  => true,
        ),
        'invite' => array(
            'filter'    => 'trim',
            'required'  => true,
        ),
    );


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