<?php
namespace Cyteam\Config;
class Config
{

    function __construct(){


    }
    
    /**
    *-----------------------------------------------------------
    *获取配置项
    *-----------------------------------------------------------
    *@access public
    *@param $user_id 用户id  $conditions条件 $page分页
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月28日
    *@version 1.0
    *@return 
    */
    function get_config() 
    {
    	/* 载入配置项 */
    	$model_setting = &af('settings');
    	$setting = $model_setting->getAll();
    	return $setting;
    	
    }
    
    function get_settings()
    {
    	return array(
    			'sites'=>array(
    					1=>'pc',
    					2=>'app',
    					3=>'wap',
    			),
    			'url_rule'=>array(
    					1=>array(
    							'name'=>'商品列表页',
    							'value'=>'productlist',
    					),
    					2=>array(
    							'name'=>'商品列表分类页',
    							'value'=>'productlist/分类ID',
    					),
    					3=>array(
    							'name'=>'商品详情页',
    							'value'=>'product/商品ID'
    					),
    					4=>array(
    							'name'=>'我要定制默认页',
    							'value'=>'diy',
    					),
    					5=>array(
    							'name'=>'我要定制指定页',
    							'value'=>'diy/分类ID',
    					),
    					6=>array(
    							'name'=>'活动特惠页',
    							'value'=>'activity'
    					),
    					7=>array(
    							'name'=>'活动列表页',
    							'value'=>'activitylist/活动ID'
    					),
    					/*         			8=>array(
    					 'name'=>'活动专题页',
    							'value'=>'activity/html名称'
    					), */
    					9=>array(
    							'name'=>'文章页',
    							'value'=>'article/文章ID'
    					),
                    10=>array(
                        'name'=>'会员中心',
                        'value'=>'usercenter'
                    )
    			),
    			'help_center_code'=>'classroom',//麦富迪讲堂code
                'professor_online_code'=>'professor_online',//专家在线code
    			'wap_banners'=>'wap_homepage_banners',//wap首页轮播图code
    			'wap_ad_local'=>'wap_advertise_location',//wap广告位code
                'wap_hot'=>'wap_hot',//wap广告位code
                'goods_banner'=>'goods_banner',//wap广告位code
    	);
    }

}