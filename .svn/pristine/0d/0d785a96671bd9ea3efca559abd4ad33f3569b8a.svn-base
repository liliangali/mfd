<?php

/**
 * RCTailor: 网站后台操作管理
 */

if (!defined('IN_ECM'))
{
    trigger_error('Hacking attempt', E_USER_ERROR);
}
$menu_data = array
(
	/* 网站设置 */
    'mall_setting' => array
    (
        'default'     => 'default|index|logout',//后台登录
        /* 网站设置 */
         'setting'     => array(
         		'systemsetting'=>'setting|base_setting',//系统设置
         		'baseinfo'=>'setting|base_infomation',//基本信息
         		'emailset'=>'setting|email_setting',//email
         		'captchaset'=>'setting|captcha_setting',//验证码
         		'storeset'=>'setting|store_setting',//开店设置
         		'creditset'=>'setting|credit_setting',//信用评价
         		'subdomain'=>'setting|subdomain_setting',//二级域名
         		'creditscore'=>'setting|creditscore_setting',//订单交易额设定
         		'profit'=>'setting|profit_setting',//收益设定
         ),
         'region'       => 'region|index',//地区设置
         'payment'    => 'payment|index',//支付方式
         'shipping' =>'shipping|index',//配送方式
         'sitestyle'=>'template|index',//网站风格
/*          'mailtemplate'=>'mailtemplate|index',//通知模版 */
         'module'=>'module|manage',//模块管理
         'widget'=>'widget|index',//挂件管理
         'sitecity'=>'sitecity|index',//站点管理
/*          'testsite'=>'testsite|index',//体验店管理 */
     	'operation'  => 'operation|index',//操作日志
    	/*'operationv2'  => 'operation|view',//操作日志 */
    	'sysmessage'=>'sysmessage|index',//系统通知
    	'messagetemplateicategory'=>'icategory|index',//消息模版分类
    	'messagetemplate'=>'messagetemplate|index',//消息模版管理
//          'theme'     => 'theme|all',//主题设置
//          'mailtemplate' => 'mailtemplate|all',//邮件模板
//          'template'  => 'template|all',//模板编辑
//          'shipping'  => 'shipping|all',//模板编辑
//          'operation'  => 'operation|all',//操作日志
    ),
	/* 面料工艺 */
    // 'goods_admin' => array
    // (
    //     'partfabric'    => 'part|index',//面料管理
    //     'partlining' => 'part|partlining', //里料采购申请
    //     'partmat'    => 'part|partmat',//工艺搭配
    //     'parttype'    => 'parttype|all',//属性设置
    // ),
    /* 产品样衣 */
	'bgoods' => array
	(

		//'customs'    => 'customs|all',
		'custom'    => 'custom|index',//样衣管理
		'fab' => 'fab|all',//面料管理
		'swfupload' => 'swfupload|all', //图片上传
		'suit' => 'suit|index', //套装管理
		//'dissertation'    => 'dissertation|all',//主题系列
		'gcategory'    => 'gcategory|index',//产品分类
		'customtype' => 'customtype|index',//样衣属性
		'cfabric' => 'cfabric|index',//DIY面料
		'dswitch' => 'dswitch|index',//DIY开关
		'fabricbook'=>'fabricbook|index',//面料册管理
	),
	/* 会员管理 */
	'member' => array
	(
		'user_manage'  =>array(
			'user_add'=>'user|add',//会员添加
			'user_edit'=>'user|edit',	//会员编辑
			'account_log' => 'account_log|add', //账户管理
		) ,//会员管理 包括｛普通会员|供应商|服务点|加盟商|设计师｝切勿修改.
		'lvmanage' => array(
			'lv_add'=>'lv|add',//会员等级添加
			'lv_edit'=>'lv|edit',//会员等级编辑
		),
		'admin'=>'admin|index',//管理员管理
		//'demand_items' => 'demand|items',//需求发布选项
		//'demand' => 'demand|index',//消费者需求列表

		//'followedbeat' => 'followedbeat|index',//会员拍照下单
		'authcyz' =>array(
				'authcyz_edit'=>'authcyz|edit',//创业者认证审核
		),//创业者认证管理
		//'authbusiness' => 'authbusiness|index',//企业认证管理
		'createcash' =>array(
				'cash_check'=>'cash|edit',//创业者提现审核
		),//创业者提现审核
		'dosysmsg'=>array(
				'dosysmsg_add'=>'dosysmsg|addSend',//发送系统消息
		)
		//'designer' => 'designer|all',//品牌设计师
	),
	/*设计师 管理*/
	// 'store_manage' => array
	// (
	// 	'store'  => 'store|index',//设计师列表
	// 	'sgrade' => 'sgrade|all',//供应商等级
	// 	'store_fabric' => 'store|store_fabric',//设计师采购申请
    //   'store_dispatching' => 'store|store_dispatching',//设计师派工量体申请
	// ),
	/*门店管理*/
	'partners' => array(
		'serve' => 'serve|indexManager', //门店管理
		//'subscribe' => 'subscribe|all', //顾客预约量体
		'figure' => 'figure|index', //门店量体数据
		'shopowner' => 'shopowner|index',//店长管理
		'quantity' => 'serve|quantity', //量体师管理
		'authfigure' => 'authfigure|index',//量体师实名认证
    	'liangticash' => 'figurecash|index',//量体师提现申请
		//'figureorderlog' => 'figureorderlog|all', //门店收益
		//'partners_list' => 'serve|all', //加盟商管理
		//'figureorderlog_3' => 'figureorderlog|all', //加盟商收益
	),
	/*内容发布*/
	'website' => array(
		'article_gl' => 'article|index',//文章管理
		'acategory' => 'acategory|index', //文章分类
		'fashion' => 'fashion|index', //潮流发布
		'partner' => 'partner|index',//友情链接
		'active' => array(
				'activeindex'=>'active|index', //活动发布管理页
				'addactive'=>'active|add',//添加活动
				'addlocation'=>'active|add_location',//添加活动位置
		),
		'folly' => array(
				'follyindex'=>'folly|index', //广告发布管理页
				'follyadd'=>'folly|add',//添加广告
				'follyaddtpl'=>'folly|add_tpl',//添加页面
				'follyaddlocation'=>'folly|add_location',//添加广告位
		),
		'style_recommend' => 'style_recommend|index', //产品推荐
        'jpjz_dissertation' =>array(
        		'jpjz_dissertationindex'=>'jpjz_dissertation|index',//主题管理
        		'jpjz_dissertationadd'=>'jpjz_dissertation|add',//添加主题
        		'jpjz_dissertation_edit'=>'jpjz_dissertation|edit',//编辑主题
        		'jpjz_dissertation_drop'=>'jpjz_dissertation|drop',//删除主题
        		'jpjz_discat'=>'jpjz_discat',//主题分类管理
        ),
        'yh_template' => array(
        		'yh_templateindex'=>'yh_template|index', //印花管理
        		'yh_templatecustom'=>'yh_template|custom',//用户上传印花
        		'yh_templateadd'=>'yh_template|add',//添加印花模版
        		'yh_categoryindex'=>'yh_category|index',//印花分类
        		'yh_categoryadd'=>'yh_category|add',//添加印花分类
        		'shirt'=>'shirt|index',//所有衬衫
        		'shirtadd'=>'shirt|add',//添加衬衫
        ),
		//'trends' => 'trends|all', //流行趋势列表
		//'trendscategory' => 'trendscategory|all', //流行趋势分类
			'designer'  => array(
					'designerManage'  => 'disigner|index',//设计师管理
					'designerAdd'   => 'designer|add',//设计师维护
			),
	),

	/*信息管理*/
	'yxgl' => array(
		'detail_comments' => 'detail_comments|index', //订单评论
		'com_comments' => 'com_comments|index',//印花|主题评论
		'figure_comments' => 'figure_comments|index',//量体服务评论
		'question' => 'question|index',//会员咨询管理
		'feedback' => 'feedback|index', //意见反馈
		'complaint' => 'complaint|index', //举报管理
	),

    /*营销活动*/
    'yingxiao' => array(
    	'ask' => 'ask|index', //线下需求收集
    	'askv2'=>'askv2|index',//线下需求收集v2
    	'cy' => 'cy|index', //创业者注册申请
		'favorable_register' => 'promotion|proReg',//新会员营销
		'favorable_frist' => 'promotion|proFirst',//初次定制营销
		'favorable_bonus' => 'coupon|index',//优惠券管理
	    'special_code' => 'special_code|index',//特权码管理
	    'kuka' => 'special_code|index',//酷卡管理
        'favorable_onegift' => 'promotion|onegift',//买一赠一
    ),

    /*订单管理*/
    'trade' => array(
		'order_manage' => array(
			'order_editaddress'=>'order|editAddress',//修改地址
// 			'order_updateprice'=>'order|updateprice',//修改价格
			'order_updateorder'=>'order|updateOrder',//修改状态
// 			'order_manualworking'=>'order|checks',//手动推送订单
// 			'order_manualexpress'=>'order|opts',//手动物流单号
			//'order_ufigure'=>'order|ufigure',//量体
		), //订单列表
        'delivery_manage' => array(
           
        ), //发货单列表
// 		'craft' => 'craft|parents',//订单工艺可选项
// 		'jpjz_order' => 'order|jpjz',//即拍即做订单
    ),
		/*统计报表*/
		'num_stat' => array(
				'numerical_stat' => 'numerical_stat|all',//购买清单及复购
		),

   	/*看图下单*/
    'ktxd' => array(
         'user_upload_img' => 'userdemand|index',//看图下单
		 'user_work_img' => 'user|work',//我的作品
		 'jpjzpart' => 'jpjzpart|index', //知识库-面料
         'process' =>array(
         		'processindex'=>'process|index',//管理
         		'processadd'=>'process|add',//新增
         		'pcategoryindex'=>'pcategory|index',//工艺分类
         ),
    ),
    /*众创推广*/
    'zctg' => array(
    	'zz_manager' => 'zzm|index', //组织管理
    	'tgy_manager' => 'generalize_member|index', //创业顾问管理
    	'jx_manager' => 'jx|index', //绩效管理
    ),
	/* 财务管理 */
	'finance_manage'=>array(
		'client_finance_detail'=>'client_finance|all',//客户明细账
		'client_finance_table'=>'client_finance|all',//客户明细表
		'estp_finance_detail'=>'client_finance|all',//创业者收益明细
	),
	/* 面料管理 */
	'fabric_manage'=>array(
// 			'fabric_brand_manage'=>'fabric_brand|index',//面料品牌分类页
			'fabric_brand_add'=>'fabric_brand|add',//面料分类添加
			'fabric_brand_eidt'=>'fabric_brand|edit',//面料分类编辑
			'fabric_info_add'=>'fabric_info|add',//添加面料
			'fabric_info_edit'=>'fabric_info|edit',//面料详情编辑
			'fabric_info_location'=>'fabric_info|location_top'//面料置顶
	),	
);


?>