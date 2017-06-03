<?php

return array(
    'code'      => 'upop',
    'name'      => Lang::get('upop'),
    'desc'      => Lang::get('upop_desc'),
    'is_online' => '0',
    'author'    => '小五哥',
    'website'   => '',
    'version'   => '1.0',
    'currency'  => Lang::get('upop_currency'),
        'config'    => array(
        'upop_mer_id'   => array(        //客户号
            'text'  => Lang::get('upop_mer_id'),
            'type'  => 'text',
        ),
        'upop_pub_Pk'       => array(        //公钥文件
            'text'  => Lang::get('upop_pub_Pk'),
            'desc'  => Lang::get('upop_pub_Pk_desc'),
            'type'  => 'text',
        ),
        'upop_mer_key'   => array(        //私钥文件
            'text'  => Lang::get('upop_mer_key'),
                'desc'  => Lang::get('upop_mer_key_desc'),
            'type'  => 'text',
        ),
        'upop_pay_fee'  => array(         //服务类型
            'text'      => Lang::get('upop_pay_fee'),
            //'desc'  => Lang::get('alipay_service_desc'),
            'type'      => 'text',
        ),
    ),
);
