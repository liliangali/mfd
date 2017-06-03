<?php
return array(
	//网站设置
    'dashboard' => array(
        'text'      => Lang::get('dashboard'),
        'subtext'   => Lang::get('offen_used'),
        'default'   => 'base_setting',
        'children'  => array(
            'base_setting'  => array(
                'text'  => Lang::get('base_setting'),
                'url'   => 'index.php?app=setting&act=base_setting',
            ),
            'region' => array(
                'text'  => Lang::get('region'),
                'url'   => 'index.php?app=region',
            ),
            'payment' => array(
                'text'  => Lang::get('payment'),
                'url'   => 'index.php?app=payment',
            ),
        	'shipping' => array(
        		'text'  => "配送方式",
        		'url'   => 'index.php?app=shipping',
        	),
            /* 'theme' => array(
                'text'  => Lang::get('theme'),
                'url'   => 'index.php?app=template',
            ), */
            /**'mailtemplate' => array(
                'text'  => Lang::get('noticetemplate'),
                'url'   => 'index.php?app=mailtemplate',
            ),**/
           //'plugin' => array(
           //     'text'  => Lang::get('plugin'),
           //     'url'   => 'index.php?app=plugin',
           // ),

           /*  'module' => array(
                'text'  => Lang::get('module'),
                'url'   => 'index.php?app=module&act=manage',
            ),
            'widget' => array(
                'text'  => Lang::get('widget'),
                'url'   => 'index.php?app=widget',
            ), */
            /**'sitecity' => array(
                'text'  => "站点管理",
                'url'   => 'index.php?app=sitecity',
            ),**/
        	'appset' => array(
        				'text'  => "app版本管理",
        				'url'   => 'index.php?app=appset&act=index',
        		),
       		/**'test' => array(
       				'text'  => '体验店管理',
       				'url'   => 'index.php?app=testsite',
       		),*/
        	'operation' => array(
        			'text'  => Lang::get('operation'),
        			'url'=> 'index.php?app=operation',
        	),
//             'operationv2' => array(
//             		'text'  => Lang::get('operationv2'),
//             		'url'=> 'index.php?app=operation&act=view',
//             ),
            'sysmessage' => array(
                'text'  => "系统通知",
                'url'=> 'index.php?app=sysmessage',
            ),
        	'messagetemplate'=>array(
        		'text'=>'消息模版',
        		'url'=>'index.php?app=messagetemplate',
        	),
        ),
    ),

    // 基础数据

    'module' => array(
        'text'      => '模块设置',
        'default'   => 'navigation',
        'children'  => array(

		    'navigation' => array(
					'text'  => "主导航管理",
					'url'   => 'index.php?app=satnav',
			),
        	'shuffling' => array(
        			'text'  => '轮播图管理',
        			'url'   => 'index.php?app=shuffling',
        	),
        	'motif' => array(
        			'text'  => '栏目管理',
        			'url'   => 'index.php?app=motif',
        	),
        ),
    ),


    // 产品设计
    'bgoods' => array(
        'text'      => '产品管理',
        'default'   => 'bgoods',
        'children'  => array(
//             'base_list' => array(
//                 'text'  => Lang::get("g_list"),
//                 'url'   => 'index.php?app=customs',
//             ),

//             'base_list' => array(
//                 'text'  => Lang::get("g_list"),
//                 'url'   => 'index.php?app=customs',
//             ),

            //主题系列
          /*   'jpjz_dissertation' => array(
                'text'  => '产品主题',
                'url'   => 'index.php?app=jpjz_dissertation',
            ), */
            'bgoods' => array(
                'text'  => "产品列表",
                'url'   => 'index.php?app=goods&act=index',
            ),
            /* 'suit' => array(
                'text'  => '套装管理',
                'url'   => 'index.php?app=suit',
            ), */
//         	'dissertation' => array(
//         		'text'  => Lang::get("dissertation"),
//         		'url'   => 'index.php?app=dissertation',
//         	),
            'gcategory' => array(
                'text'  => Lang::get('gcategory'),
                'url'   => 'index.php?app=gcategory',
            ),
           
        		'goods_type' => array(
        				'text'  => "产品类型",
        				'url'   => 'index.php?app=goods_type',
        		),
        	  'goodsspec' => array(
                'text'  => "产品规格",
                'url'   => 'index.php?app=goodsspec',
            ), 
            'goodsattr' => array(
                'text'  => "产品属性",
                'url'   => 'index.php?app=goodsattribute',
            ),
        		/* 'kucunrool' => array(
        				'text'  => "库存提示规则管理",
        				'url'   => 'index.php?app=kucunrool',
        		), */
			/* 'cfabric' => array(
					'text'  => "DIY面料管理",
					'url'   => 'index.php?app=cfabric',
			),
            'jpjzpart' => array(
                'text'  => "DIY面料详情",
                'url'   => 'index.php?app=jpjzpart',
            ),
			'dswitch' => array(
					'text'  => "DIY选项开关",
					'url'   => 'index.php?app=dswitch',
			),
            'dfabric' => array(
                'text'  => "样衣面料",
                'url'   => 'index.php?app=dfabric',
            ),
            'fabricbook' => array(
                'text'  => "料册及工具",
                'url'   => 'index.php?app=fabricbook',
            ),
            //ns add 尺码数据
             'sizeTable' => array(
                'text' => "尺码数据",
                'url' => 'index.php?app=sizeTable',
             ),
           //ns add 尺码名称
            'sizeStyle' => array(
              'text' => "尺码名称",
              'url' => 'index.php?app=sizeStyle',
            ),
 */
        	//'user_design_img' => array('text' => Lang::get('user_design_img'),'url' => 'index.php?app=user_design',),
        ),
    ),
    
    
    /********** 定制商品管理  add by 小五 面料diy 工艺部分***************/
    'fabric_manage'=>array(
        'text'=>'定制商品',
        'default'=>'fabric_brand_manage',
        'children'=>array(
            'fabric_brand_manage'=>array(
                'text'=>'属性管理',
                'url'=>'index.php?app=fabric_brand&act=index',
            ),
            'diy_manage'=>array(
                'text'=>'分类管理',
                'url'=>'index.php?app=diyM&act=index',
            ),
            'dict_crossrule'=>array(
                'text'=>'互斥管理',
                'url'=>'index.php?app=dictC&act=index',
            ),
            'base_material'=>array(
                'text'=>'基料管理',
                'url'=>'index.php?app=BaseMaterial&act=index',
            ),
            'standard_package'=>array(
                'text'=>'规格包装价格管理',
                'url'=>'index.php?app=StandardPackage&act=index',
            ),
            'effect_age'=>array(
                'text'=>'功效犬期价格管理',
                'url'=>'index.php?app=EffectAge&act=index',
            ),
            'material'=>array(
                'text'=>'物料规格管理',
                'url'=>'index.php?app=Material&act=index',
            ),
            'feed'=>array(
                'text'=>'饲喂量建议',
                'url'=>'index.php?app=feed&act=index',
            ),
        ),
    ),
    
    // 会员管理
    'user' => array(
        'text'      => Lang::get('user'),
        'default'   => 'user_manage',
        'children'  => array(
            'user_manage' => array(
                'text'  => Lang::get('user_manage'),
                //'url'   => 'index.php?app=user&serve_type=1',
                'url' => 'index.php?app=user',
            ),
        	'member_lv' => array('text'  => '会员等级管理','url'=> 'index.php?app=lv',),
        	'admin_manage' => array('text' => Lang::get('admin_manage'), 'url'   => 'index.php?app=admin',),
        		
            // 'user_manage_1' => array(
            //     'text'  => '裁缝/创业者',
            //     'url'   => 'index.php?app=user&serve_type=1',
            // ),
            //'demand_items' => array(
                   // 'text'  => Lang::get('需求发布选项'),
                   // 'url'   => 'index.php?app=demand&act=items',
          //  ),
           // 'demand' => array(
                  //  'text'  => Lang::get('消费者需求列表'),
                   // 'url'   => 'index.php?app=demand&act=index',
           // ),
//            'single_comments' => array(
//                'text'  => Lang::get('会员晒单评论管理'),
//                'url'   => 'index.php?app=comments&act=index&model=single',
//            ),
//            'order_comments' => array(
//                'text'  => Lang::get('会员订单评论管理'),
//                'url'   => 'index.php?app=comments&act=index&model=order',
//            ),




			//'followed_beat' => array('text'  => '会员拍照下单','url'=> 'index.php?app=followedbeat',),
        	//'member_lv' => array('text'  => '会员等级管理','url'=> 'index.php?app=lv',),
        	//'authperson' => array('text'  => '创业者认证管理','url'=> 'index.php?app=authperson',),
        /*     'cyzauth' => array('text'  => '创业者认证审核','url'=> 'index.php?app=authcyz',), */
        	//'authbusiness' => array('text'  => '企业认证管理','url'=> 'index.php?app=authbusiness',),
        	/* 'cash' => array('text'  => '创业者提现审核','url'=> 'index.php?app=cash',),
            'profit' => array('text'  => '创业者收益列表','url'=> 'index.php?app=profit',),
            'paylist' => array('text'  => '创业者收支列表','url'=> 'index.php?app=paylist',), */
            // 'user_notice' => array('text' => Lang::get('user_notice'),'url'  => 'index.php?app=notice',),
        	//'user_apply' => array('text' => "充值提现管理",'url'  => 'index.php?app=apply',),
        		'pettype' => array(
        				'text'  => Lang::get('pettype'),
        				'url'   => 'index.php?app=pettype',
        		),

        ),
    ),

    // 裁缝管理
  /*  'store_manage' => array(
        'text'      => '设计师 管理',
        'default'   => 'store_manage',
        'children'  => array(

           'store_manage'  => array(
                'text'  => '设计师列表',
               'url'   => 'index.php?app=store',
           ),
             'sgrade' => array(
                 'text'  => '模板管理',
                 'url'   => 'index.php?app=sgrade',
             ),
              'scategory' => array(
                  'text'  => '裁缝分类',
                  'url'   => 'index.php?app=scategory',
              ),
			 'figure' => array(
                 'text'  => Lang::get('消费者量体数据'),
                'url'   => 'index.php?app=figure&act=index',
            ),
            //ns add 裁缝申请面料
            'store_fabric' => array(
                    'text'  => "设计师采购申请",
                    'url'   => 'index.php?app=store&act=store_fabric',
            ),
            //ns add 裁缝申请派工量体
            'store_dispatching' => array(
                    'text'  => "设计师派工量体申请",
                    'url'   => 'index.php?app=store&act=store_dispatching',
            ),

             'brand' => array(
                 'text'  => '供应商品牌',
                 'url'   => 'index.php?app=brand',
             ),
        ),
    ),
*/
// 		// 服务点/加盟商
// 		'partners' => array(
// 				'text'      => '门店管理',
// 				'default'   => 'serve',
// 				'children'  => array(
// 						'serve' => array(
// 								'text'  => '门店信息管理',
// 								'url'   => 'index.php?app=serve&act=indexManager',
// 						),
//                         'shopowner' => array(
//                                 'text'  => Lang::get('店长信息管理'),
//                                 'url'   => 'index.php?app=shopowner',
//                         ),

// 						'partners_list' => array(
// 								'text'  => Lang::get('量体师管理'),
// 								'url'   => 'index.php?app=serve&act=quantity',
// 						),

// 						// 'subscribe' => array(
// 						// 		'text'  => Lang::get('顾客预约量体'),
// 						// 		'url'   => 'index.php?app=subscribe&act=index',
// 						// ),
// 						/*
// 						 'brokerage' => array(
// 						 		'text'  => Lang::get('服务点佣金设置'),
// 						 		'url'   => 'index.php?app=brokerage&act=index',
// 						 ),
// 		*/
// 						/*
// 						 'brokerage_log' => array(
// 						 		'text'  => Lang::get('服务点分成记录'),
// 						 		'url'   => 'index.php?app=brokerage_log&act=index',
// 						 ),
// 		*/


// 						'figure' => array(
// 								'text'  => Lang::get('顾客量体数据'),
// 								'url'   => 'index.php?app=figure&act=index',
// 						),

// 						// 'figureorderlog' => array(
// 						// 		'text'  => Lang::get('门店收益'),
// 						// 		'url'   => 'index.php?app=figureorderlog&act=index',
// 						// ),

// 						// 'figureorderlog_3' => array(
// 						// 'text'  => Lang::get('量体师收益'),
// 						// 'url'   => 'index.php?app=figureorderlog&act=index&t=3',
// 						// ),
//                         'figure_auth' => array(
//                         'text'  => Lang::get('量体师实名认证'),
//                         'url'   => 'index.php?app=authfigure&act=index',
//                         ),
// 						'check_cash' => array(
// 							'text'  => Lang::get('量体师提现申请'),
// 							'url'   => 'index.php?app=figurecash&act=index',
// 						),

// 						/*
// 						 'partners_yongjin' => array(
// 						 'text'  => Lang::get('加盟商佣金设置'),
// 						 'url'   => 'index.php?app=brokerage&act=index&t=2',
// 						 ),
// 						 */
// 						/*
// 						 'partners_log' => array(
// 						 'text'  => Lang::get('加盟商收益'),
// 						 'url'   => 'index.php?app=brokerage_log&act=index&t=2',
// 						 ),
// 						 */
// 				),
// 		),

    // 内容发布
    'website' => array(
        'text'      => Lang::get('website'),
        'default'   => 'article',
        'children'  => array(
            'article' => array(
                'text'  => '文章列表',
                'url'   => 'index.php?app=article',
            ),
            'acategory' => array(
                'text'  => Lang::get('acategory'),
                'url'   => 'index.php?app=acategory',
            ),
           /*  'elephotos' => array(
                'text'  => '电子相册',
                'url'   => 'index.php?app=elephotos',
            ), */

         /*   'cooluser_lx' => array(
                'text'  => '流行趋势列表',
                'url'   => 'index.php?app=trends',
            ),
        	'cooluser_la' => array(
        			'text'  => '流行趋势分类',
        			'url'   => 'index.php?app=trendscategory',
        	),*/
           /** 'fashion' => array(
                'text'  => Lang::get('fashion'),
                'url'   => 'index.php?app=fashion',
            ),
            'partner' => array(
                'text'  => Lang::get('partner'),
                'url'   => 'index.php?app=partner',
            ),**/
           /*  'active' => array(
                'text'  => Lang::get('active'),
                'url'   => 'index.php?app=active',
            ),
            'folly' => array(
                'text'  => Lang::get('folly'),
                'url'   => 'index.php?app=folly',
            ), */
            /* 'template' => array(
                'text'  => Lang::get('template'),
                'url'   => 'index.php?app=template',
            ), */
			/* 'shuffling' => array(
                'text'  => '轮播图管理',
                'url'   => 'index.php?app=shuffling',
            ), */
      		/**'yh_template' => array(
        		'text'  => "衬衫印花",
        		'url'   => 'index.php?app=yh_template',
        		),**/
       /*     'designer'  => array(
           		'text'  => '创业故事',
           			'url'   => 'index.php?app=designer',
           	), */

            //'recommend_type' => array(
              //  'text'  => LANG::get('recommend_type'),
             //    'url'   => 'index.php?app=recommend'
            //),
            //'template' => array(
            //    'text'  => Lang::get('template'),
            //    'url'   => 'index.php?app=template',
            //),
        ),
    ),

    //
        // 内容发布
    'contentsite' => array(
        'text'      => '信息管理',
        'default'   => 'detail_comments',
        'children'  => array(
            	'detail_comments' => array(
                'text'  => '商品购买评论',
                'url'   => 'index.php?app=detail_comments&act=index',
            ),
			/*'com_comments' => array(
                'text'  => '印花|主题评论',
                'url'   => 'index.php?app=com_comments&act=index',
            ),*/
			/* 'figure_comments' => array(
                'text'  => '量体服务评论',
                'url'   => 'index.php?app=figure_comments&act=index',
            ),
            'member_question' => array(
                'text'  => '用户咨询',
                'url'   => 'index.php?app=question',
            ), */
               'feedback' => array(
                'text'  => '意见反馈',
                'url'   => 'index.php?app=feedback',
            ),
			/*'complaint' => array(
                'text'  => '举报管理',
                'url'   => 'index.php?app=complaint',
            ),*/
            //ns add 添加查看短信发送code
            'smsRegTmp' => array(
                'text' => '短信码记录',
                'url'=> 'index.php?app=smsRegTmp',
            ),
			'dosysmsg' => array('text'  => '系统消息推送','url'=> 'index.php?app=dosysmsg',),
        	'informations' => array(
        			'text' => '重要资讯推送',
       				'url'=> 'index.php?app=informations',
       		),
        ),
    ),
    //促销活动
    'yingxiao'     => array(
        'text'      => '营销活动',
        'default'   => 'voucher',
        'children'  => array(
      
            'voucher' => array(
                'text'  => '优惠券管理',
                'url'   => 'index.php?app=setting&act=voucher',
            ),
//            'wheat_setting' => array(
//                'text'  => '麦券管理',
//                'url'   => 'index.php?app=setting&act=wheat_setting',
//            ),
            'goods_promotion' => array(
                'text'  => '商品促销',
                'url'   => 'index.php?app=discount',
            ),
       /*      'order_promotion' => array(
                'text'  => '订单促销',
                'url'   => 'index.php?app=orderpro',
            ), */
		/* 	'kuka' => array(
                'text'  => '麦富迪E卡管理',
                'url'   => 'index.php?app=special_code&act=index&sign=1',
            ), */
       /*      'kukaconfig' => array(
                'text'  => '线上酷卡管理',
                'url'   => 'index.php?app=kukaconfig',
            ), */
           /*  'bonus' => array(
                'text'  => '红包管理',
                'url'   => 'index.php?app=bonus&act=index',
            ), */
            
      /*       'debit' => array(
                'text'  => '线上优惠券发放',
                'url'   => 'index.php?app=debit',
            ),
            'debit_line' => array(
                'text'  => '线下优惠券设置',
                'url'   => 'index.php?app=debit_line',
            ), */
         /*    'coinconfig' => array(
                'text'  => '购币升级',
                'url'   => 'index.php?app=coinconfig',
            ), */
           /** 'favorable_order'  => array(
                'text'  => '订单促销',
                'url'   => 'index.php?app=favorable&act=order',
            ),
            'favorable_goods'  => array(
                'text'  => '产品促销',
                'url'   => 'index.php?app=favorable&act=goods',
            ),**/
        ),
    ),
    // 订单管理
    'trade' => array(
        'text'      => Lang::get('trade'),
        'default'   => 'order_manage',
        'children'  => array(
            'order_manage' => array(
                'text'  => '订单列表',
                'url'   => 'index.php?app=order'
            ),
//            'delivery_manage' => array(
//                'text'  => '发货单列表',
//                'url'   => 'index.php?app=delivery'
//            ),
//             'order_refund' => array(
//                 'text'  => '面料册换货',
//                 'url'   => 'index.php?app=bookrefund'
//             ),
// 			'fx' => array(
//                 'text'  => '订单返修申请',
//                 'url'   => 'index.php?app=fx'
//             ),
			// 			'order_refund' => array(
			//                 'text'  => '退款申请',
			//                 'url'   => 'index.php?app=orderrefund'
			//             ),
          //  'craft' => array(
          //          'text'  => "DIY工艺选项",
          //          'url'   => 'index.php?app=craft&act=parents',
          // ),
            /**'jpjz_order' => array(
            	'text' => '即拍即做订单',
                'url' => 'index.php?app=order&act=jpjz',
            ),**/
            //'order_query' => array(
            //    'text'  => '订单查询',
            //    'url'   => 'index.php?app=order&act=query'
            //),
            //'order_word' => array(
            //    'text'  => '订单单据',
            //    'url'   => 'index.php?app=order&act=word'
            //),
        ),
    ),

	// 即拍即做
    /**'jpjz' => array(
        'text'      => '看图下单',
        'default'   => 'userdemand',
        'children'  => array(
         'userdemand' => array(
             'text'  => '看图下单',
             'url'=> 'index.php?app=userdemand'
             ),

			//商品分类
//            'jpjz_gcategory' => array(
//                'text'  => Lang::get('jpjz_gcategory'),
//                'url'   => 'index.php?app=jpjz_gcategory',
//            ),
//             'view'=>array(
//                 'text'=>"样衣管理",
//                 'url'=>'index.php?app=view',
//             ),



            'user_work_img' => array('text'  => '我的作品','url'=> 'index.php?app=user&act=work',),

            'jpjzpart' => array(
                'text'  => "知识库-面料",
                'url'   => 'index.php?app=jpjzpart',
            ),
        		'process' => array(
        				'text'  => Lang::get('process'),
        				'url'   => 'index.php?app=process',
        		),
//         		'category' => array(
//         				'text'  => Lang::get('pcategory'),
//         				'url'   => 'index.php?app=pcategory',
//         		),


        ),
    ),**/


	/* 财务管理 */
	'finance_manage'=>array(
			'text'=>'财务管理',
			'default'=>'client_finance_table',
			'children'=>array(
					'client_finance_detail'=>array(
							'text'=>'客户明细账',
							'url'=>'index.php?app=client_finance&act=search',
					),
					'client_finance_table'=>array(
							'text'=>'客户明细表',
							'url'=>'index.php?app=client_finance&act=index',
					),
					/* 'estp_finance_detail'=>array(
							'text'=>'创业者收益明细',
							'url'=>'index.php?app=client_finance&act=estp_index'
					), */
//    			    'client_mediate_examine'=>array(
//    			        'text'=>'账户调解审核',
//    			        'url'=>'index.php?app=client_examine&act=index'
//    			    ),
			),
	),


		//  统计报表
/* 		'numerical' => array(
				'text'      =>'统计报表',
				'default'   => 'numerical_stat',
				'children'  => array(
						'numerical_stat' => array(
								'text'  => '购买清单及复购',
								'url'   => 'index.php?app=numerical_stat'
						),
						
						
				),
		), */


);

?>
