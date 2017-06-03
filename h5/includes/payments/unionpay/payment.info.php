<?php

return array(
    'code'      => 'unionpay',
    'name'      => Lang::get('unionpay'),
    'desc'      => Lang::get('unionpay_desc'),
    'is_online' => '0',
    'author'    => 'RC TEAM',
    'website'   => '',
    'version'   => '1.0',
    'currency'  => Lang::get('unionpay_currency'),
        'config'    => array(
        'unionpay_mer_id'   => array(        //客户号
            'text'  => Lang::get('unionpay_mer_id'),
            'type'  => 'text',
        ),
//         'unionpay_pub_Pk'       => array(        //公钥文件
//             'text'  => Lang::get('unionpay_pub_Pk'),
//             'desc'  => Lang::get('unionpay_pub_Pk_desc'),
//             'type'  => 'text',
//         ),
        'unionpay_mer_key'   => array(        //私钥文件
            'text'  => Lang::get('unionpay_mer_key'),
                'desc'  => Lang::get('unionpay_mer_key_desc'),
            'type'  => 'text',
        ),
        'unionpay_pay_fee'  => array(         //服务类型
            'text'      => Lang::get('unionpay_pay_fee'),
            //'desc'  => Lang::get('alipay_service_desc'),
            'type'      => 'text',
        ),
    ),
);
