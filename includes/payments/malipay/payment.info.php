<?php

return array(
    'code'      => 'malipay',
    'name'      => '手机支付宝',
    'desc'      => '手机支付宝，用于手机wap,app 支付',
    'is_online' => '1',
    'author'    => 'RC TEAM',
    'website'   => '',
    'version'   => '1.0',
    'currency'  => Lang::get('alipay_currency'),
    'config'    => array(
        'alipay_account'   => array(        //账号
            'text'  => '支付宝账号',
            'type'  => 'text',
        ),
        'alipay_key'       => array(        //密钥
            'text'  => 'MD5密钥',
            'desc'  => '交易安全检验码',
            'type'  => 'text',
        ),
        'alipay_partner'   => array(        //合作者身份ID
            'text'  => '合作者身份ID',
            'type'  => 'text',
        )
    ),
);

?>