<?php

return array(
    'code'      => 'chinapay',
    'name'      => Lang::get('chinapay'),
    'desc'      => Lang::get('chinapay_desc'),
    'is_online' => '0',
    'author'    => 'RC TEAM',
    'website'   => '',
    'version'   => '1.0',
    'currency'  => Lang::get('chinapay_currency'),
        'config'    => array(
        'chinapay_mer_id'   => array(        //客户号
            'text'  => Lang::get('chinapay_mer_id'),
            'type'  => 'text',
        ),
        'chinapay_pub_Pk'       => array(        //公钥文件
            'text'  => Lang::get('chinapay_pub_Pk'),
            'desc'  => Lang::get('chinapay_pub_Pk_desc'),
            'type'  => 'text',
        ),
        'chinapay_mer_key'   => array(        //私钥文件
            'text'  => Lang::get('chinapay_mer_key'),
                'desc'  => Lang::get('chinapay_mer_key_desc'),
            'type'  => 'text',
        ),
        'chinapay_pay_fee'  => array(         //服务类型
            'text'      => Lang::get('chinapay_pay_fee'),
            //'desc'  => Lang::get('alipay_service_desc'),
            'type'      => 'text',
        ),
    ),
);
