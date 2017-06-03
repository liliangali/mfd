<?php

class OrderserveinfoModel extends BaseModel
{
    var $table  = 'order_serve_info';
    var $prikey = 'id';
    var $_name  = 'order_serve_info';

    /* 关系列表 */
    var $_relation  = array(

        // 一个返修记录
        'has_order_serve' => array(
            'model' => 'orderserve',
            'type' => HAS_ONE,
            'foreign_key' => 'id',
            'refer_key'       => 's_id',
            'dependent' => true
        ),


        //一个商品返修项目有一个返修工艺明细
        'has_order' => array(
            'model' => 'order',
            'type' => HAS_ONE,
            'foreign_key' => 'order',
            'foreign_key' => 'order_id', //外键名
            'dependent' => true
        ),


        //一个商品返修项目有一个返修工艺明细
        'has_order_fx' => array(
            'model' => 'orderfx',
            'type' => HAS_ONE,
            'foreign_key' => 'id',
            'foreign_key' => 'osi_id', //外键名
            'dependent' => true
        ),


        // 一个会员拥有多个收货地址
//        'has_order_figure' => array(
//            'model' => 'orderfigure',
//            'type' => HAS_ONE,
//            'foreign_key' => 'liangti_id',
//            'refer_key'       => 'liangti_id',
//            'dependent' => true
//        ),

//        'has_member' => array(
//            'model' => 'member',
//            'type' => HAS_ONE,
//            'foreign_key' => 'user_id',
//            'refer_key'       => 'liangti_id',
//            'dependent' => true
//        ),



    );



}

