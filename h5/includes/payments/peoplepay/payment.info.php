<?php
//ns add 全民支付配置文件
return array(
    'code'      => 'people',
    'name'      => Lang::get('people'),
    'desc'      => Lang::get('people_desc'),
    'is_online' => '1',
    'author'    => 'RC TEAM',
    'website'   => '',
    'version'   => '1.0',
    'currency'  => Lang::get('people_currency'),
    'config'    => array(
        'people_account'   => array(        //账号
            'text'  => Lang::get('people_account'),
            'desc'  => Lang::get('people_account_desc'),
            'type'  => 'text',
        ),
        'people_mer_key'   => array(        //私钥文件
            'text'  => Lang::get('people_mer_key'),
            'desc'  => Lang::get('people_mer_key_desc'),
            'type'  => 'text',
        ),
        'people_pay_fee'  => array(         //服务类型
            'text'      => Lang::get('people_pay_fee'),
            'type'      => 'text',
        ),
    ),
);

?>