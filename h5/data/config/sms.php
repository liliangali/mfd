<?php

return array (
		'demand' =>
		array (
				'cate_name' => '',
				'list' =>
				array (
						'offer' =>
						array (
								'title' => '',
								'msg' => '报价提醒：<{$cf_name}>裁缝为您的定制报价<{$offer}>元，请及时关注！',
						),
						'success'=>
        		array (
        				'title' => '',
        				'msg' => '中标提醒：<{$username}>消费者已选择您中标，请及时关注！',
        		)
		 )
		),
		'order' =>
		array (
				'cate_name' => '',
				'list' =>
				array (
						'order_create' =>
						array (
								'title' => '',
								'msg' => '您的订单<{$order_sn}>已提交，订单金额：￥<{$money}>元，订单审核中，请及时关注!',
						),
						'order_check_tailor'=>
						array(
								'title'=>'',
								'msg'=>'尊敬的<{$tailor_nickname}>裁缝，您的订单<{$order_sn}>已经审核通过正在生产中，请及时关注！'
						),
						'order_check_client'=>
						array(
								'title'=>'',
								'msg'=>'尊敬的会员，您的订单已提交，裁缝：<{$store_name}>，订单号<{$order_sn}>，正在生产中，请及时关注！'
						)
				)
		),
  'user' =>
  array (
    'cate_name' => '消费者',
    'list' =>
    array (
      'order_create' =>
      array (
        'title' => '订单生成发给消费者短信关于订单的信息',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
      'order_delivery' =>
      array (
        'title' => '订单发货后给消费者发短信通知',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
      'order_death' =>
      array (
        'title' => '订单取消时给消费者发短信通知',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
      'order_coupon' =>
      array (
        'title' => '下订单赠送优惠券给会员时',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
      'admin_coupon' =>
      array (
        'title' => '后台操作给会员发优惠券时',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
    ),
  ),
  'server' =>
  array (
    'cate_name' => '服务点',
    'list' =>
    array (
      'appointment' =>
      array (
        'title' => '有消费者进行了预约服务时，给服务点的联系人发短信通知',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'update' =>
      array (
        'title' => '服务点进行了预约服务的状态变更时，给消费者发短信通知',
        'msg' => '您的订单生成成功!订单号为:<{$order_id}><{$name}>',
      ),
      'finish' =>
      array (
        'title' => '服务点录完了消费者的量体数据后，给消费者发短信通知',
        'msg' => '3）服务点录完了消费者的量体数据后，给消费者发短信通知，提示消费者去会员中心->我的量体数据 查看',
      ),
    ),
  ),
  'business' =>
  array (
    'cate_name' => '合作',
    'list' =>
    array (
      'supplier_deny' =>
      array (
        'title' => '供应商加盟被拒绝后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'supplier_allow' =>
      array (
        'title' => '供应商加盟审核通过后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'stylist_deny' =>
      array (
        'title' => '设计师加盟被拒绝后',
        'msg' => '拒绝设计师!',
      ),
      'stylist_allow' =>
      array (
        'title' => '设计师加盟审核通过后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'server_deny' =>
      array (
        'title' => '服务点加盟被拒绝后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'server_allow' =>
      array (
        'title' => '服务点加盟审核通过后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'alliance_deny' =>
      array (
        'title' => '加盟商加盟被拒绝后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
      'alliance_allow' =>
      array (
        'title' => '加盟商加盟审核通过后',
        'msg' => '1）有消费者进行了预约服务时，给服务点的联系人发短信通知（服务点提交后）',
      ),
    ),
  ),
);

?>