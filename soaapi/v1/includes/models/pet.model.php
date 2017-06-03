<?php

/* 宠物管理 pet */
class PetModel extends BaseModel
{
    var $table  = 'pet';
    var $prikey = 'pet_id';
    var $_name  = 'pet';

    /* 表单自动验证 */
    // var $_autov = array(
    //     'user_id'   => array(
    //         'required'  => true,
    //     ),
    //     'summary'   => array(
    //         'required'  => true,
    //         'filter'    => 'trim',
    //     ),
    //     'nature_id' => array(
    //         'required'  => true,
    //         'filter'    => 'trim',
    //     ),
    //     'nature_name'   => array(
    //         'required'  => true,
    //         'filter'    => 'trim',
    //     ),
    // );

    /* 关系列表 */
    var $_relation  = array(
        // 一个收货地址只能属于一个会员
        'belongs_to_member' => array(
            'model'             => 'member',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'user_id',
            'reverse'           => 'has_pet',
        ),
    );
}

?>