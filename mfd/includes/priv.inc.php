<?php

/**
 * 网站后台管理左侧菜单数据
 */
if (!defined('IN_ECM')) {
    trigger_error('Hacking attempt', E_USER_ERROR);
}


$menu_data = array
    (
    /* 网站设置 */
    'mall_setting' => array
        (
        'default' => 'default|all', //后台登录
        // 'setting'     => 'setting|all',//网站设置
        // 'region'       => 'region|all',//地区设置
        // 'payment'    => 'payment|all',//支付方式
        // 'theme'     => 'theme|all',//主题设置
        // 'mailtemplate' => 'mailtemplate|all',//邮件模板
        // 'template'  => 'template|all',//模板编辑
        // 'shipping'  => 'shipping|all',//模板编辑
        'operation' => 'operation|all', //操作日志
        'appset' => 'appset|all', //app版本控制
        'messageicategory' => 'icategory|all', //消息分类
        'messagetemplate' => 'messagetemplate|all', //消息模版管理
    ),
    /* 模块管理 */
    'module' => array(
        'satnav' => 'satnav|all', //主导航管理
        'shuffling' => 'shuffling|all', //轮播图管理
        'motif' => 'motif|all', //栏目管理
    ),
    /* 产品管理 */
    'bgoods' => array
        (
        'goods' => 'goods|all', //产品列表
        'gcategory' => 'gcategory|all', //产品分类
        'goods_type' => 'goods_type|all', //产品类型
        'goodsspec' => 'goodsspec|all', //产品规格
        'goodsattribute' => 'goodsattribute|all', //产品属性
        'products' => 'products|all', //编辑货品
    ),
    /* 定制商品 */
    'fabric_manage' => array(
//        'fabric_brand' => 'fabric_brand|all', //属性管理
        'diyM' => 'diyM|all', //分类管理
        'dictC' => 'dictC|all', //互斥管理
        'basematerial' => 'BaseMaterial|all', //基料管理
        'standardpackage' => 'StandardPackage|all', //规格包装价格管理
        'effectage' => 'EffectAge|all', //功效犬期价格管理'
        'material' => 'Material|all', //物料规格管理
    ),
    /* 会员管理 */
    'member' => array
        (
        'user_manage' => 'user|all|serve|all', //会员管理 包括｛普通会员|供应商|服务点|加盟商|设计师｝切勿修改.
        'lv' => 'lv|all', //会员等级管理
//        'authcyz' => 'authcyz|all', //创业者认证审核
//        'cash' => 'cash|all', //创业者提现审核
//        'profit' => 'profit|all', //创业者收益列表
//        'paylist' => 'paylist|all', //创业者收支列表
        'admin_manage' => 'admin|all', //创业者收支列表
        'pettype' => 'pettype|all', //宠物种类管理

//        'dosysmsg' => 'dosysmsg|all', //系统消息推送
//        'account_log' => 'account_log|all', //账户管理
    ),
    /* 内容发布 */
    'website' => array(
        'article_list' => 'article|all', //文章列表
        'acategory' => 'acategory|all', //文章分类
//        'elephotos' => 'elephotos|all', //电子相册
//        'active' => 'active|all', //活动发布
//        'folly' => 'folly|all', //广告发布
//        'designer' => 'designer|all', //创业故事
    ),
    //信息管理
    'contentsite' => array(
        'detail_comments' => 'detail_comments|all', //商品购买评论
//        'figure_comments' => 'figure_comments|all', //量体服务评论
//        'question' => 'question|all', //用户咨询
        'feedback' => 'feedback|all', //意见反馈
        'smsRegTmp' => 'smsRegTmp|all', //短信码记录
        'dosysmsg' => 'dosysmsg|all', //系统消息推送
        'informations' => 'informations|all', //重要资讯推送
    ),
    //营销活动
    'yingxiao' => array(
//        'setting_m' => 'setting|wheat_setting', //麦券管理-设置
//        'setting_f' => 'setting|debit_log', //麦券管理-发放记录
        'setting_voucher' => 'setting|voucher', //优惠券查看
        'discount' => 'discount|all', //商品促销
//        'setting_voucher_create' => 'setting|voucher_create', //优惠券生成
//        'setting_voucher_batch' => 'setting|voucher_batch', //优惠券批次管理
    ),
    //订单管理
    'trade' => array(
        'order' => 'order|all', //订单列表
//        'delivery' => 'delivery|all', //发货单列表
    ),
//    //业务管理
//    'zctg' => array(
//        'zzm' => 'zzm|all', //业务组管理
//        'generalize_member' => 'generalize_member|all', //BD信息管理
//        'jx' => 'jx|all', //BD绑定明细
//        'bd_business' => 'bd_business|all', //BD业绩及提成
//    ),
    //财务管理client_finance
    'finance_manage' => array(
        'client_finance' => 'client_finance|all', //客户明细权限
//        'client_examine' => 'client_examine|all', //账户调解审核
    ),
);

