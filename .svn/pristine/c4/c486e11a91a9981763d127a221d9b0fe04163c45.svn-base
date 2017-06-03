<?php
	/**
	 * 裁缝
	 * @author liang.li <1184820705@qq.com>
	 * @version $Id: Club.class.php 13338 2016-01-05 00:03:57Z shaozz $
	 * @copyright Copyright 2014 mfd.com
	 * @package app
	 */
	class Club
	{
		var $wdwl_url = '';
		var $error = '';
		var $token = '';

	  /**
	   * 构造函数
	   * @param string $username
	   *  可设置当前用户
	   * @access protected
	   * @return void
	   */
	  function __construct() {

	  }

	  /**
	   * 设置参数
	   */
	  public function set($key, $value) {
	    $this->$key = $value;
	  }

	  /**
	   * 获取参数
	   */
	  public function get($key) {
	    return isset($this->$key) ? $this->$key : NULL;
	  }

	  /**
	   * 裁缝列表
	   */
	public function index($pageSize,$pageIndex,$filter,$subject_id)
	{
		global $json;
		$arr = array();
		$store_attr_mod = m('storeattr');
		$conditions = "type_id=2 AND content_id=$subject_id";
		$store_ids = $store_attr_mod->find(array(
				'conditions'	=> $conditions,
		));
		$store_id = array();
		if ($store_ids)
		{
			foreach ($store_ids as $k=>$v)
			{
				$store_id[$v['store_id']] = $v['store_id'];
			}
		}
		$id = "";
		if ($store_id)
		{
			foreach ($store_id as $k=>$v)
			{
				$id .= $k.",";
			}
		}

		$id = trim($id,',');

		$conditions = "store_id in (".$id.")";
		$store_mod = m('store');
		$store_list = $store_mod->find(array(
			'conditions'	=>	$conditions,
// 			'fields'			=> 'store_name,owner_name,owner_sex,region_name,address,tel,small_banner,personality,im_qq,im_wx,popularity,fans',
			'fields'			=> '*',
			'limit'			=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
		));

		return $json->encode($store_list);
	}

	/**
	 * 需求列表
	 */
	public function demand_item($data)
	{
		global $json;
		$arr = array();
		$type = isset($data->type) ? $data->type : 'normal';
		$type_id = isset($data->type_id) ? intval($data->type_id ): 0;
		if ($type != 'normal' && !$type_id)
		{
			$return = array(
				'statusCode' => 0,
				'error'		 => array(
					'errorCode' => 100,
					'msg'       => '如果不是普通需求,type_id参数必传',
				)
			);

			return $json->encode($return);
		}

		$res  = $this->_check_type($type, $type_id);
		if ($res['statusCode'] == 1)
		{
			return $res;
		}

		$deman_item_mod = m('demanditem');
		$init_list = $deman_item_mod->_item_cates();
		//筛选类型
		if ($type == 'lin')
		{
			unset($init_list[3]);
		}

		$item_list = $deman_item_mod->find();
// 	dump($item_list);
		$demand_list = array();
		$init = array();
		foreach ($init_list as $k=>$v)
		{
			$init[$k]['cate'] = $k;
			$init[$k]['cate_name'] = $v;
			$init[$k]['item']	= array();
		}
// dump($init);
		if ($item_list)
		{

			foreach ($item_list as $k=>$v)
			{
				$init[$v['cate']]	['item'][] = $v;
			}
		}
		$return = array(
					'statusCode' => 1,
					'result'     => array(
						'init' => $init,
						'type_info'  => $res['info'],
					),
				);
		return $json->encode($return);

	}

	/**
     * 裁缝端--获得需求列表
	 *
     * @param array $data  参数集合
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function demand_list($data)
	{
		global $json;
		$db = db();
		$filter    = isset($data->filter) ? $data->filter : 0;//过滤条件：1：未报价 2：我已报价 3：已中标
		//$order     = $data->order == '' ? 1 : $data->order ;//时间排序：1：降序 2：升序
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;//页面大小
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;//当前第几页
		$token     = isset($data->token) ? $data->token : '';//裁缝用户标识
		$damSn     = isset($data->damSn) ? $data->damSn : '';//需求标题或sn
		//$user_id   = isset($data->user_id) ? $data->user_id : 0;//如果传了user_id说明是要具体查看某个user_id的需求列表

		if(empty($token)) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
			return $json->encode($return);

		}
		$user_info = getUserInfo($token);//获得当前裁缝
		if (!$user_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
			return $json->encode($return);
		}
		$user_id = $user_info['user_id'];

		//过滤条件
		$conditions = "";
		//=====如果传了user_id说明是要具体查看某个user_id的需求列表=====
		/*
		if ($user_id) {
			$conditions .= " AND user_id = $user_id ";
		}*/

		$demand_offer = '';
		$demand_offer_in = array();
		$demand_offer_mod = m('demandoffer');
		$demand_mod = m('demand');
		//获得我已报价需求id
		$demand_offer_ids = $demand_offer_mod->find(array(
				'conditions' => 'cf_id ='.$user_id.' AND dmdof.status = 1 ',
				'fields'	 => 'md_id',
				'index_key'  => ''
		));
		if ($filter == 1) {//未报价,获取未报价需求
			$demand_offer = $demand_offer_mod->find(array(
				'conditions' => 'cf_id =' .$user_id,
				'fields'	 => 'md_id',
				'index_key'  => ''
			));
			if($demand_offer) {
				foreach($demand_offer as $offer) {
					$demand_offer_in[] = $offer['md_id'];
				}
			}

			if($demand_offer_in) {
				$demand_offer_str = implode(',', array_unique($demand_offer_in));
				$conditions .= " AND ((dmd.md_id not in(" . $demand_offer_str . ")) AND (dmdof.status = 1 AND dmdof.cf_id=".$user_id." )) OR (dmd.take_in=0 AND dmd.status <> 4 AND dmd.status <> 0 )";
			} else {
				$conditions .= " AND dmd.md_id not in('') ";
			}

		} elseif ($filter == 2) {//我已报价
			//组装sql
			$demand_offer_in = array();
			if($demand_offer_ids) {
				foreach($demand_offer_ids as $offer) {
					$demand_offer_in[] = $offer['md_id'];
				}
			}
			if($demand_offer_in) {
				$demand_offer_str = implode(',', array_unique($demand_offer_in));
				$conditions .= " AND dmd.md_id in(" . $demand_offer_str . ") AND dmdof.status != 2";
			} else {
				$conditions .= " AND dmd.md_id in('') ";
			}

		} elseif ($filter == 3) {//已中标
			$conditions .= ' AND dmdof.cf_id = '. $user_id .' AND dmdof.status = 2';
		}

		//需求标题或sn
		if($damSn) {
			$conditions .= " AND (dmd.md_sn like
			 '%$damSn%' or dmd.md_title like '%$damSn%')";
		}


		$limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
		//print_r($limit);die;
		$demand_mod = m('demand');
		$comment_sql = "SELECT dmd.user_id, dmd.md_id, dmd.md_title, dmd.take_in, dmd.add_time, dmd.params,dmdof.status  from ".DB_PREFIX."demand dmd LEFT JOIN ".DB_PREFIX."demand_offer dmdof ON dmd.md_id = dmdof.md_id WHERE 1=1 ".$conditions." GROUP BY md_id order by dmd.last_time desc limit ".$limit."";
		//print_r($comment_sql);die;
		$demand_list = $demand_mod->getALL($comment_sql);
		//print_r($demand_list);die;
		//获得总数
		$count_sql   = "SELECT count(dmd.md_id) as count from ".DB_PREFIX."demand dmd LEFT JOIN ".DB_PREFIX."demand_offer dmdof ON dmd.md_id = dmdof.md_id WHERE 1=1 ".$conditions."  ";
		$demand_count = $demand_mod->getALL($count_sql);

		$demand_list_arr = array();
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		//获得我已报价的需求id
		if($demand_offer_ids) {
			foreach($demand_offer_ids as $offer) {
				$demand_in[] = $offer['md_id'];
			}
		}
		
		if($demand_list) {
			foreach($demand_list as $val) {
				$params = unserialize($val['params']);

				if($params) {
					foreach($params as $v) {
						if($v['key'] == '定制预算' || $v['cat'] == '定制预算') {
							$budget = $v['val'];
						}
					}
				}
				$offer_status = 0;
				if(in_array($val['md_id'], $demand_in)) {
					$offer_status = 1;
				}
				//获得用户头像
				$avatar = $objAvatar->avatar_show($val['user_id'], 'big');
				$demand_list_arr[] = array(
					'md_id'    => $val['md_id'],
					'user_id'  => $val['user_id'],
					'md_title' => $val['md_title'],
					'take_in'  => $val['take_in'],
					'add_time' => $val['add_time'],
					'status'   => $val['status'],
					'avatar'   => $avatar,
					'offer_status' => $offer_status,
					'budget'       => $budget,
				);
			}
		}
		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'count'       => $demand_count['0']['count'],
				'demand_list' => !empty($demand_list_arr) ? $demand_list_arr : '',
			),
		);


		return $json->encode($return);
	}

	/**
	 * 需求详情
	 */
	public function demand_info($data)
	{
		global $json;
		$img_url = LOCALHOST1."/upload/offer/";
		$md_id = isset($data->id) ? $data->id : 0;
		$token = isset($data->token) ? $data->token : '';//裁缝用户标识

		if(empty($token)) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
			return $json->encode($return);

		}
		$user_info = getUserInfo($token);//获得当前裁缝
		if (!$user_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
			return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		if (!$md_id) {
			$return= array(
				'statusCode' => 0,
				'error' 	 => array(
					'errorCode' => 100,
					'msg'       => '参数错误',
				)
			);
			return $json->encode($return);
		}
		$demand_mod  = m('demand');
		$demand_info = $demand_mod->get($md_id);

		//=====格式化需求=====
		if ($demand_info) {
			//=====动态参数=====
			if ($demand_info['params']) {
				$params = unserialize($demand_info['params']);
				if($params) {
					foreach($params as $par) {
						if($par['key'] == '主题风格' || $par['cat'] == '主题风格') {
							$demand_info['p_theme'] = $par['val'];
						}
						if($par['key'] == '品类' || $par['cat'] == '品类' ) {
							$demand_info['p_cate'] = $par['val'];
						}
						if($par['key'] == '面料' || $par['cat'] == '面料') {
							$demand_info['p_fabric'] = $par['val'];
						}
						if($par['key'] == '定制预算' || $par['cat'] == '定制预算') {
							$demand_info['p_budget'] = $par['val'];
						}
						if($par['key'] == '尺寸号码' || $par['cat'] == '尺寸号码') {
							$demand_info['p_size'] = $par['val'];
						}
					}
				}
			}
			unset($demand_info['params']);
			/*
			//获得需求类型数据
			$item_info = $this->_check_type($demand_info['md_type'], $demand_info['md_type_id']);
			$demand_info['item_info'] = $item_info['info'];
			*/
			//=====报价人的列表=====
			$offer_mod  = m('demandoffer');
			$offer_list = $offer_mod->find(array(
				'conditions' => " md_id = ".$md_id,
				'index_key'  => ''
			));
			$off_ids = array();
			if($offer_list) {
				foreach($offer_list as $key=>$val) {
					$off_ids[] = $val['cf_id'];
					//处理图片
					$offer_list[$key]['url']   = !empty($val['url']) ? $img_url.$val['url'] : '';
					$offer_list[$key]['url2']  = !empty($val['url2']) ? $img_url.$val['url2'] : '';
					$offer_list[$key]['url3']  = !empty($val['url3']) ? $img_url.$val['url3'] : '';
					$offer_list[$key]['url_thumb']   = !empty($val['url_thumb']) ? $img_url.$val['url_thumb'] : '';
					$offer_list[$key]['url2_thumb']  = !empty($val['url2_thumb']) ? $img_url.$val['url2_thumb'] : '';
					$offer_list[$key]['url3_thumb']  = !empty($val['url3_thumb']) ? $img_url.$val['url3_thumb'] : '';
				}
			}
			$offer_status = 0;
			$cf_id = 0;
			if(in_array($user_id, $off_ids)) {
				$offer_status = 1;
			}
			$demand_info['offer_status'] = empty($offer_status) ? 0 : $offer_status;
			//如果已中标返回中标裁缝id和裁缝姓名，否则返回0
			$other    = $offer_mod->get("md_id = $md_id AND status = 2");
			//print_r($other);die;
			$other_id = $other['cf_id'];
			$cf_id    = $other_id;
			$demand_info['cf_id']  = empty($cf_id) ? 0 : $cf_id;

			if($demand_info['cf_id']){
				$m = m('store');
				$cf_info = $m -> get("store_id = ".$demand_info['cf_id']);
				$demand_info['cf_name'] = $cf_info['owner_name'];
			}

		} else {
			$return= array(
				'statusCode' => 0,
				'error'		 => array(
					'errorCode' => 100,
					'msg'       => '参数错误',
				)
			);

			return $json->encode($return);
		}
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'demand_info' => !empty($demand_info) ? $demand_info : '',
				'offer_list'  => !empty($offer_list) ? $offer_list : '',
			),
		);
		return $json->encode($return);
	}

	/**
	 * 发布需求发送短信
	 * @param unknown_type $phone
	 * @param unknown_type $msg
	 * @return boolean|Ambigous <void, mixed>
	 */
	function SendDemandSms($phone,$msg){
		$err = array('-1'=>'传递参数错误','-2'=>'用户id或密码错误','-3'=>'通道id错误','-4'=>'手机号码错误','-5'=>'短信内容错误','-6'=>'余额不足错误=','-7'=>'绑定ip错误',
				'-8'=>'未带签名','-9'=>'签名字数不对','-10'=>'通道暂停','-11'=>'该时间禁止发送','-12'=>'时间戳错误','-13'=>'编码异常','-4'=>'发送被限制');

		$timestamp = time();
		//$pass = 9817; // 用户密码
		$channelid = 12852 ; // 发送频道id
		$cpid = 9817;
		$ps = md5("rctailor123_".$timestamp."_topsky");

		$r = rand(1000,99999);
		$msg = iconv("UTF-8",'GBK',$msg);
		$url = "http://admin.sms9.net/houtai/sms.php?cpid={$cpid}&password={$ps}&channelid={$channelid}&tele={$phone}&timestamp={$timestamp}&msg={$msg}";
		$rs = get2($url);
		$back = substr($rs,6);
		$pre = substr($rs,0,7);
		if($pre != 'success')
		{
			return false;
		}

		//=====加入数据库=====


		return $rs;
	}

	/**
	 * 检查参数是否正确
	 * @param unknown_type $type
	 * @param unknown_type $type_id
	 */
	public function _check_type($type,$type_id)
	{
		$return = array();
		if ($type == 'normal')
		{
			$return['statusCode'] = 1;
			$return['result']['info'] = array();
			$return['result']['msg'] = '';
		}
		elseif ($type == 'lin')
		{
			//面料
			$part_mod = m('part');
			$part_info = $part_mod->get($type_id);
			if (!$part_info)
			{
				$return['statusCode'] = 0;
				$return['error']['errorCode'] = '101';
				$return['error']['msg'] = '面料不存在';
				$return['info'] = array();
			}
			else
			{
				$part['id'] = $part_info['part_id'];
				$part['name']	= $part_info['part_name'];

				$return['statusCode'] = 1;
				$return['result']['info'] = $part;
				$return['result']['msg'] = '';
			}
		}
		elseif ($type == 'shop')
		{
			//商品
			$customs_mod = m('customs');
			$customs_info = $customs_mod->get($type_id);
			if (!$customs_info)
			{
				$return['statusCode'] = 0;
				$return['error']['errorCode'] = '102';
				$return['error']['msg'] = '商品不存在';
				$return['info'] = array();
			}
			else
			{
				$customs['id'] = $customs_info['cst_id'];
				$customs['name']	= $customs_info['cst_name'];

				$return['statusCode'] = 1;
				$return['result']['info'] = $customs;
				$return['result']['msg'] = '';
			}
		}
		else
		{
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] = 'type参数错误';
			$return['info'] = array();
		}
		return $return;
	}

	/**
	 *  消费者发布需求
	 */
	public function demand_add($data)
	{
		global $json;
		$arr 				= array();
		$token 				= isset($data->token) ? $data->token : '';

		$arr['md_sn']	    = $this->_gen_sn();
		$arr['md_title']    = isset($data->title) ? $data->title : '';
		$arr['md_type']     = isset($data->md_type) ? $data->md_type : 'normal';
		$arr['md_type_id']  = isset($data->md_type_id) ? $data->md_type_id : 0;
		$arr['uname']       = isset($data->name) ? $data->name : '';
		$arr['region_id']   = isset($data->region_id) ? $data->region_id : '';
		$arr['region_name'] = isset($data->region_name) ? $data->region_name : '';
		$arr['email'] 		= isset($data->email) ? $data->email : '';
		$arr['remark'] 	 	= isset($data->remark) ? $data->remark : '';//简介
		$arr['mobile'] 		= isset($data->mobile) ? $data->mobile : '';
		$arr['qq'] 		    = isset($data->qq) ? $data->qq : '';
		$arr['source'] 		= 'app';

		$code		        = isset($data->code) ? $data->code : '';// 验证码
		$item 				= isset($data->item) ? $data->item : "";
		$arr['region_name'] = str_replace(",", " ", $arr['region_name']);

		if (!$token || !$arr['md_title'] || !$arr['region_id'] || !$arr['region_name'] || !$arr['mobile'] ) {
			$return['statusCode'] = 0;
			$return['msg'] = '参数错误';
			return $json->encode($return);
		}

		//验证验证码是否有效
		$sms_mod   = m('SmsRegTmp');
   	    if($code) {
   	        $res = $sms_mod->get(array(
   	           // 'conditions'=>"code='$code' AND phone='$phoneNum' AND type='register'  ",
			    'conditions' => "code='$code' AND phone= ".$arr['mobile'],
				'order'      => "id DESC",
   	            'fields'     => '*',
   	        ));
   	        if($res['phone']) {
   	            if(time() > $res['fail_time']) {
					$return = array(
						'statusCode' => 0,
						'error' => array(
							'errorCode' => 100,
							'msg'       => '验证码已过期',
						)
					);
   	                return $json->encode($return);
   	            }

   	        } else {
				$return = array(
					'statusCode' => 0,
					'error' => array(
						'errorCode' => 100,
						'msg'       => '验证码不正确',
					)
				);
   	            return $json->encode($return);
   	        }

   	    } else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '验证码不能为空',
				)
			);
   	        return $json->encode($return);
   	    }


		//判断当前用户是否登录
		$user_info = getUserInfo($token);
		if (!$user_info)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 100;
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}
		$arr['user_id'] = $user_info['user_id'];


		if (!in_array($arr['md_type'],array('normal', 'suit', 'diy','shop','show','lin')))
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 101;
			$return['error']['msg']		  = 'type参数错误';
			return $json->encode($return);
		}
		if ($arr['md_type'] != 'normal' && !$arr['md_type_id'])
		{
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = 102;
			$return['error']['msg'] 	  = '如果你发布的不是普通需求 type_id参数必须填写';
			return $json->encode($return);
		}
		//=====查选item传过来的数据是否合法=====


		$arr['params'] = $this->_get_params($item);

		$demand_mod = m('demand');
		$arr['add_time']   = time();
		$arr['last_time']  = time();
		$demand_mod->add($arr);
		//给裁缝发送消息
		$user_id    = $user_info['user_id'];
		$unickname  = $user_info['nickname'];
		$cus_mod    = m('customer_figure');
		$cus        = $cus_mod->get("userid = $user_id");
		$store_id   = $cus['storeid'];
		$_message_mod = &m("usermessage");
		$arr = array();
		$arr[] = array(
                      'from_user_id'  => $user_id,
                      'to_user_id'    => $store_id,
                      'from_nickname' => $unickname,
                      'type'          => '4',
                      'content'       => "$unickname 新发布了新的定制需求，请查看 ",
                      'add_time'      => time()
                  );
		$return['statusCode']		 = 1;
		$return['result']['success'] = '发布成功';
		return $json->encode($return);

	}

	/**
     * 消费者发布需求--对应分类
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function demand_cate()
	{
        global $json;
		require(ROOT_PATH . '/includes/demand.base.php');
		$demand_cl = new BaseDemand();
		$cates   = $demand_cl->_get_item_data();
		$item_cate = array(
			1 => '主题风格',
			2 => '品类',
			3 => '面料',
			4 => '定制预算',
			5 => '尺寸号码',
        );

		$demand_cates = array();
		if($cates) {
			foreach($cates as $key => $cate) {
				foreach($cate['list'] as $k => $v) {
					$demand_cates[$key][] = array(
						'key'  => $item_cate[$key],
						'id'   => $v['id'],
						'name' => $v['name'],
					);
				}
			}

		}

		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'demand_cates' => $demand_cates,
				'success'      => '返回成功',
			),
		);
		return $json->encode($return);

	}

	/**
	 * 处理item参数
	 * @param unknown_type $post
	 * @return string
	 */
	function _get_params($post)
	{

		$item_str = explode(",", $post);
		$item = array();

		foreach ($item_str as $k=>$v)
		{
			$temp =explode(":",$v);
			$item[$temp[0]] = $temp[1];
		}
		$deman_item_mod = m('demanditem');
		$item_cate = $deman_item_mod->_item_cates();
		$items = $deman_item_mod->find(array(
			'conditions' => 'id'.db_create_in($post),
		));
		foreach ($items as $key => $row)
		{
			$res[$row['cate']]['key'] = $item_cate[$row['cate']];
			$res[$row['cate']]['val'] = $row['name'];
			$res[$row['cate']]['id']  = $row['id'];
		}
		return serialize($res);
	}

	/**
	 * 获得发布需求的唯一编号
	 * @return string
	 */
	function _gen_sn()
	{
		$deman_item_mod = m('demand');
		mt_srand((double) microtime() * 1000000);
		$timestamp = gmtime();
		$y = date('y', $timestamp);
		$z = date('z', $timestamp);
		$sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

		$data = $deman_item_mod->find("md_sn = '{$sn}'");
		if (empty($data))
		{
			return $sn;
		}
		return $this->_gen_sn();
	}

	/**
	 * 地区三级联动
	 */
	function region($data)
	{
		global $json;
		$region_id = isset($data->region_id) ? intval($data->region_id) :0;
		$region_mod = m('region');
		$conditions = "parent_id = $region_id";
		$region_list = $region_mod->find(array(
		'conditions'	=> $conditions,
		'index_key'	=> '',
		));
		$return['statusCode'] = 1;
		$return['result']['region_list'] = $region_list;
		return $json->encode($return);
	}
	
	/**
	 * 宠物品种三级联动
	 */
	function pettype($data)
	{
		global $json;
		$type_id = isset($data->type_id) ? intval($data->type_id) :0;
		$pettype_mod = m('pettype');
		$conditions = "parent_id = $type_id";
		$type_list = $pettype_mod->find(array(
				'conditions'	=> $conditions,
				'index_key'	=> '',
		));
		$return['statusCode'] = 1;
		$return['result']['type_list'] = $type_list;
		return $json->encode($return);
	}
	/**
	 * 裁缝添加消费者到会员发送验证码
	 * @param unknown_type $data
	 */
	function sendCode2Cus($data)
	{
		global $json;
		$return = array();
		$phone  = isset($data->phone) ? $data->phone : 0;
		$cus_name = isset($data->cus_name) ? $data->cus_name : '消费者';
		$token  = isset($data->token) ? $data->token : '';
		if(empty($token)){
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => '100',
					'msg'       => 'token不能为空',
				)
			);
			return $json->encode($return);
		}
		$user_info = getUserInfo($token);
		$user_id   = $user_info['user_id'];
		$store_mod = m('store');
		$store = $store_mod->get("store_id = $user_id");
		$store_name = $store['store_name'];
		$sms_reg_mod = m('SmsRegTmp');
        $sms_reg_mod->drop("type='$addcus' AND phone='$phoneNum' ");
		$r 		= rand(1000,99999);
		$msg 	= "尊敬的".$cus_name."，".$store_name."将添加您为他的消费者，请将验证码".$r."告诉他。";
		$sms 	= SendSms($phone, $msg, 'addcus',$r);
		$data['type'] = 'addcus';
        $data['code'] = $r;
        $data['add_time']  = time();
        $data['phone']     = $phone;
        $data['fail_time'] = 1621377359;
		$sms_reg_mod->add($data);
		if ($sms)
		{
			$return['statusCode'] = 1;
			$return['result']['msg'] = '短信发送成功';
		}
		else
		{
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = '101';
			$return['error']['msg'] = '短信发送失败';
		}
		return $json->encode($return);
	}
	/**
	 * 发布需求发送验证码
	 * @param unknown_type $data
	 */
	function sendCode($data)
	{
		global $json;
		$return = array();
		$phone  = isset($data->phone) ? $data->phone : 0;
		$r 		= rand(1000,99999);
		$msg 	= '【Alicaifeng】发布需求验证码为'.$r."请在60秒内输入";
		$sms 	= SendSms($phone, $msg, 'demand',$r);
		if ($sms)
		{
			$return['statusCode'] = 1;
			$return['result']['msg'] = '短信发送成功';
		}
		else
		{
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] = '短信发送失败';
		}
		return $json->encode($return);
	}

	/**
	 * 裁缝报价
	 * @param unknown_type $data
	 */
	public function offer_add($token, $id, $offer, $del_time, $remark, $url, $url2, $url3)
	{
		global $json;
		$user_info = getUserInfo($token);

		if ($user_info) {
			if ($user_info['serve_type'] != 1) {
				$return['statusCode'] = 0;
				$return['error']['errorCode'] = '101';
				$return['error']['msg'] = '只有裁缝才可以对需求发布报价';
				return $json->encode($return);
			}
			$store_mod  = m('store');
			$store_info = $store_mod->get($user_info['user_id']);
			if (!$store_info) {
				$return['statusCode'] = 0;
				$return['error']['errorCode'] = '102';
				$return['error']['msg'] = '裁缝记录不存在';
				return $json->encode($return);
			}

		} else {
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] = 'token错误';
			return $json->encode($return);
		}
		$store_id = $user_info['user_id'];

		//=====验证参数=====
		if (!$id || !$offer || !$del_time)
		{
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] = '参数错误';
			return $json->encode($return);
		}

		//=====验证需求是否存在  状态是否是完成
		$demand_mod  = m('demand');
		$demand_info = $demand_mod->get($id);
		if ($demand_info) {
			//=====要验证状态是否是已发布 为作废=====
			if($demand_info['status'] == 4) {
				$return['statusCode']		  = 0;
				$return['error']['errorCode'] = '104';
				$return['error']['msg'] 	  = '需求已完成';
				return $json->encode($return);
			}

			if($demand_info['status'] == 0) {
				$return['statusCode']		  = 0;
				$return['error']['errorCode'] = '104';
				$return['error']['msg'] 	  = '需求已作废';
				return $json->encode($return);
			}


		} else {
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] 	  = '当前需求不存在';
			return $json->encode($return);
		}
		
		$data 			  = array();
		$data['md_id']	  = $id;
		$data['offer'] 	  = $offer;
		$data['del_time'] = $del_time;
		$data['remark']   = $remark;
		$data['cf_id']    = $store_id;
		$data['cf_name']  = $store_info['store_name'];
		$data['sub_time'] = time();
        $data['mobile']   = $user_info['phone_mob'];
		$offer_mod 	  	  = m('demandoffer');

		//=====查询是否已经报过价了=====
		$offer_info = $offer_mod->get("md_id = $id AND cf_id=$store_id ");
		if ($offer_info) {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '103';
			$return['error']['msg'] 	  = '您已报过价了,不能重复报价';
			return $json->encode($return);
		}
		
		////===== 报价图片 组装处理图片信息=====
		include PROJECT_PATH."/includes/ImageTool.class.php";
		$imageTool = new ImageTool( ROOT_PATH.'/upload/offer/');
		
		if ($url) {
			$data['url'] = $imageTool->uploadImage($url);
			$data['url_thumb'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url'], 200, 200);
		}
		if ($url2) {
			$data['url2'] = $imageTool->uploadImage($url2);
			$data['url2_thumb'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url2'], 200, 200);
		}
		if ($url3) {
			$data['url3'] = $imageTool->uploadImage($url3);
			$data['url3_thumb'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url3'], 200, 200);
		}

		if($offer_mod->add($data)) {
			//给消费者发送消息
			$demand_mod    = m('demand');
			$demand        = $demand_mod->get("md_id = $id");
			$cus_id        = $demand['user_id'];
			$store_name    = $store_info['store_name'];
			$_message_mod  = &m("usermessage");
			$arr = array(
                      'from_user_id'  => $store_id,
                      'to_user_id'    => $cus_id,
                      'from_nickname' => $store_name,
                      'location_url'  => "demand-".$id.".html",
                      'type'          => '5',
                      'content'       => "$store_name 裁缝为你提供了".$offer."元的报价，<a href=\"demand-".$id.".html\">查看详情</a> ",
                      'add_time'      => time()
                  );
			$_message_mod-> add($arr);

			//=====需求表的参与人数要加1=====
			$token_in = $demand_info['take_in'] + 1;
			$demand_mod->edit($id,array('take_in'=>$token_in));

			$return['statusCode'] 		 = 1;
			$return['result']['success'] = '报价成功';
			return $json->encode($return);
		} else {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '105';
			$return['error']['msg'] 	  = '未知错误';
			return $json->encode($return);
		}

	}

	/**
	 * 消费者选定报价人
	 */
	public function sel_offer($data)
	{
		global $json;
		$token     = isset($data->token) ? $data->token : '';
		$md_id 	   = isset($data->md_id) ? $data->md_id :0;
		$offer_id  = isset($data->offer_id) ? $data->offer_id :0;
		$user_info = getUserInfo($token);
		if ($user_info) {
			$user_id = $user_info['user_id'];
		} else {
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = 100;
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}

		//=====检查报价参数====
		$offer_mod  = m('demandoffer');
		$offer_info = $offer_mod->get($offer_id);
		if (!$offer_info) {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 101;
			$return['error']['msg']  	  = '要选定的报价记录不存在';
			return $json->encode($return);
		}

		$demand_mod = m('demand');
		//=====查看该token是否是md_id的主人=====
		$demand_info = $demand_mod->get($md_id);
		if ($demand_info['user_id'] != $user_id) {
			$return['statusCode']     	  = 0;
			$return['error']['errorCode'] = 102;
			$return['error']['msg'] 	  = '你只能选定自己发布的需求';
			return $json->encode($return);
		}
		$re=$offer_mod->edit("md_id=$md_id and of_id=$offer_id" , array('status' => '2'));
		if($re){
			$offer_mod->edit("md_id=$md_id and status = 1",array('status' => '3'));
		}
		$return['statusCode']  		 = 1;
		$return['result']['success'] = '选定成功';
		return $json->encode($return);
	}


	/**
	 * 消费者关注裁缝
	 */
	public function addfollow($data)
	{
		global $json;
		$token     = isset($data->token) ? $data->token : '';
		$store_id  = isset($data->user_id) ? $data->user_id :0;
		$user_info = getUserInfo($token);
		$user_id   = $user_info['user_id'];

		$store_info = getUinfoByUid($store_id);
		if ($store_info['serve_type']  != 1) {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 102;
			$return['error']['msg']       = '消费者只能关注裁缝,不能关注其他会员类型';
			return $json->encode($return);
		}

		$follow_mod = m('userfollow');
		//=====查询是否已经关注=====
		$follow_info = $follow_mod->get(array('uid'=>$user_id, 'follow_uid'=>$store_id));

		if ($follow_info) {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 103;
			$return['error']['msg']       = '已经关注过了,不能重复关注';
			return $json->encode($return);
		}

		//他是否已经关注我
		$data = array();
	   	$map  = array('uid'=>$store_id, 'follow_uid'=>$user_id);
	   	$isfollow_me = $follow_mod->get($map);

	   	if ($isfollow_me) {
	   	    $data['mutually'] = 1; //互相关注+
	   	    $follow_mod->edit("uid=$store_id and follow_uid= $user_id", array('mutually'=>1)); //更新他关注我的记录为互相关注
	   	}

		$data['uid'] 		= $user_id;
		$data['follow_uid'] = $store_id;
		$data['add_time'] 	= time();
		if($follow_mod->add($data)) {
			//增加我的关注人数
			$m = m('member');
			$follows = $store_info['follows'] + 1;
			$m->edit($store_id, array('follows'=>$follows));

			//增加 裁缝的关注数
			$store_mod  =& m('store');
			$store_mod->setInc(array('store_id' => $store_id),'fans');

			$return['statusCode']		 = 1;
			$return['result']['success'] = '关注成功';
			return $json->encode($return);
		} else {
			$return['statusCode'] 	      = 0;
			$return['error']['errorCode'] = 104;
			$return['error']['msg']       = '未知错误';
			return $json->encode($return);
		}

	}

	/**
	 * 消费者取消关注裁缝
	 */
	public function delfollow($data)
	{
		global $json;
		$token 	   = isset($data->token) ? $data->token : '';
		$store_id  = isset($data->user_id) ? $data->user_id :0;
		$user_info = getToken($token);

		$store_info = getUinfoByUid($store_id);
		if ($store_info['serve_type']  != 1) {
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = 100;
			$return['error']['msg']       = 'token错误';
			return $json->encode($return);
		}
		$user_id    = $user_info['user_id'];
		$follow_mod = m('userfollow');
		//=====查询是否已经关注=====
		$follow_info = $follow_mod->get(" uid=$user_id AND follow_uid=$store_id");
		if (!$follow_info) {
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = 102;
			$return['error']['msg']       = '这个关注 关系不存在';
			return $json->encode($return);
		}

		$data  			    = array();
		$data['uid'] 		= $user_id;
		$data['follow_uid'] = $store_id;
		$data['add_time']   = time();

		if($follow_mod->drop(" uid=$user_id AND follow_uid=$store_id ")) {
			//他是否已经关注我
	   	    $map = array('uid'=>$store_id, 'follow_uid'=>$user_id);
	   	    $isfollow_me = $follow_mod->get($map);
	   	    if ($isfollow_me) {
	   	        $follow_mod->edit("uid=$store_id and follow_uid= $user_id",array('mutually'=>0)); //更新他关注我的记录为互相关注
	   	    }
			$m = m('member');
	   	    //减少我的被关注人数
	   	    $m->setDec(array('user_id'=>$store_id),'follows');

	   	    //减少裁缝的关注数
	   	    $store_mod  = & m('store');
	   	    $store_mod->setDec(array('store_id'=>$store_id),'fans');

			$return['statusCode'] 		 = 1;
			$return['result']['success'] = '取消关注成功';
			return $json->encode($return);
		} else {
			$return['statusCode'] = 0;
			$return['error']['errorCode'] = 103;
			$return['error']['msg']       = '未知错误';
			return $json->encode($return);
		}

	}

	/**
	 * 获取裁缝的作品列表
	 */
	public function store_demand($data)
	{
		global $json;
		$token 		= isset($data->token) ? $data->token : '';
		$store_id   =  isset($data->user_id) ? $data->user_id :0;
		$pageSize   = isset($data->pageSize) ? $data->pageSize :10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex :1;
		$store_info =getUinfoByUid($store_id);
		if ($store_info)
		{
			if ($store_info['serve_type'] != 1)
			{
				$return['statusCode'] 		  = 0;
				$return['error']['errorCode'] = '103';
				$return['error']['msg'] 	  = '要查询的user_id不是裁缝';
				return $json->encode($return);
			}
		}

		$conditions = "cf_id=$store_id";
		$offer_mod  = m('demandoffer');
		$offer_list = $offer_mod->find(array(
			'conditions'	=> $conditions,
		));

		//拼接需求id组合
		$md_ids = "";
		if ($offer_list)
		{
			foreach ($offer_list as $k=>$v)
			{
				$md_ids .= $v['md_id'].",";
			}
		}
		else
		{
			return array();
		}
		$md_ids = trim($md_ids,',');
		$md_ids = "(".$md_ids.")";

		$conditions  = "md_id in $md_ids";
		$limit 		 =  ($pageSize * ($pageIndex-1)) . ','. $pageSize;
		$demand_mod  = m('demand');
		$demand_list = $demand_mod->find(array(
			'statusCode' 	=> 1,
			'result' 		=>array(
			'conditions'	=>	$conditions,
			'limit'			=> $limit,
			'index_key'		=> '',
			),
		));
		if ($demand_list)
		{
			foreach ($demand_list as $k=>$v)
			{
				$demand_list[$k]['params']    = unserialize($v['params']);
				$item_info					  = $this->_check_type($demand_list[$k]['md_type'], $demand_list[$k]['md_type_id']);
				$demand_list[$k]['item_info'] = $item_info['info'];
			}
		}
		return $json->encode($demand_list);

	}


	/**
	 * 获得定制详情
	 */
	public function getBasis($data)
	{
		global $json;
		$id  = $data->id;
		/*测试开始*/
		/* 实例化基本款的公共接口 */
		$cs  =& cs();
// 	dump($cs);
		$arr =  $cs->get_basis_info($id,'a');
// echo 'aa';exit;
// dump($arr);
// 		$return = $this->_format_mobile_data($data);
// 	dump($return);

		foreach ($arr['data'] as $k1=>$v1)
		{
			foreach ($arr['style'] as $k2=>$v2)
			{
				if ($k1 == $v2)
				{
					foreach ($v1 as $k3=>$v3)
					{
						foreach ($v3['part'] as $k4=>$v4)
						{
							$s_img = $v4['info']['s_img'];
							$s_info = $this->check_remote_file_exists($s_img);
							if (!$s_info)
							{
								$arr['data'][$k1][$k3]['part'][$k4]['info']['s_img'] = $arr['data'][$k1][$k3]['part'][$k4]['info']['part_small'];
							}
						}
					}
					break 1;
				}
			}

			/*纽扣*/
			foreach ($arr['design'] as $k5=>$v5)
			{

				if ($k1 == $v5)
				{

					foreach ($v1 as $k6=>$v6)
					{
						foreach ($arr['buttons'] as $k7=>$v7)
						{
							if ($k6 == $v7)
							{

								foreach ($v6['part'] as $k8=>$v8)
								{

									$s_info = $this->check_remote_file_exists($v8['info']['s_img']);
									//$s_info = file_exists($v8['info']['s_img']);
									//   								var_dump($s_info);
									//   								echo $v8['info']['s_img'];exit;
									if (!$s_info)
									{
										//echo $k6;
										//echo '<pre>';var_dump($arr['data'][$k1][$k6]);exit;
										$arr['data'][$k1][$k6]['part'][$k8]['info']['s_img'] = $arr['data'][$k1][$k6]['part'][$k8]['info']['part_small'];
									}
								}
							}
							break 1;
						}
					}

					break 1;
				}
			}
		}

		/*纽扣*/


		return $json->encode($arr);
	}

	function _format_mobile_data($data){
		$return = array();
		#面料选择|里料设计|纽扣选择|款式设计|个性签名
		foreach ($data['data'] as $key=>$v){
			foreach ($data['data'][$key] as $partid => $r){

				if (in_array($partid, $data['fabric'])){
					$temp 						= array();
					$k 							= 1;
					$return['data'][$k]['name'] = '面料选择';
					$temp 						= $r;
				}

				if (in_array($partid, $data['material'])){
					$temp 						= array();
					$k 							= 2;
					$return['data'][$k]['name'] = '里料设计';
					$temp 						= $r;

				}
				if (in_array($partid, $data['buttons'])){
					$temp 						= array();
					$k 							= 4;
					$return['data'][$k]['name'] = '纽扣设计';
					$temp 						= $r;
				}
				if (in_array($key, $data['style'])){
					$temp 						= array();
					$k 							= 3;
					$return['data'][$k]['name'] = '款式设计';
					foreach ($v as $p=>$info){
						foreach ($info['part'] as $cid=>$value){
							$v[$p]['name'] = $data['category'][$p]['cate_name'];
							if (!in_array($value['info']['cate_id'], Constants::$graphRuleoutParentCid)){//工艺下不纳入出图规则计算
								$v[$p]['part'][$cid]['info']['alias'] = $data['category'][$value['info']['cate_id']]['alias'];
							}
						}
					}
					$temp['part'] = $v;
				}

				if (in_array($partid, Constants::$signatureParent)){
					$temp 						= array();
					$k 							= 5;
					$return['data'][$k]['name'] = '个性签名';
					$temp 						= $r;
				}

				#别名处理
				if (3!=$k){
				foreach ($temp['part'] as $p=>$info){
					foreach ($info as $cid=>$value){

					if (1 == $k){
					$temp['part'][$p]['info']['alias'] = $data['category'][$value['top_cate']]['alias'];
					}
					if (4 == $k){
					$temp['part'][$p]['info']['alias'] = $data['category'][$value['cate_id']]['alias'];
					}
					}

					}
					}

							$return['data'][$k]['part'] = $temp['part'];
					}

					}

				return $return;
//     				 print_r($data['customs']);exit();
					$this->assign("data", $return['data']);	//关联基本款
					$this->assign("cinfo", $data['customs']);	//关联基本款
					$this->assign("size", $data['size']);	//关联基本款
					$this->assign("menu", range(1,5));		//左侧大类
					$this->assign("sequence", json_encode($data['key_sequence']));		//左侧大类
					$this->assign("consumption", json_encode($data['consumption']));	//单耗
					$this->assign("processaa", json_encode($data['process']));		//工艺
									//     				 $this->assign("process", $data['process']);		//工艺
									//     				 var_dump(json_encode($data['process']));exit();
					$this->assign("imgurl", SITE_URL.'/'.IMG_PATH.'/custom/'.$data['customs']['cst_id'].'/');		//左侧大类
									//     	print_r($return['data']);exit();
									//     	print_r(ksort($return['data']));exit();
	}


	function check_remote_file_exists($url) {
		$curl = curl_init($url);
		// 不取回数据
		curl_setopt($curl, CURLOPT_NOBODY, true);
		// 发送请求
		$result = curl_exec($curl);
		$found  = false;
		// 如果请求没有发送失败
		if ($result !== false) {
			// 再检查http响应码是否为200
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ($statusCode == 200) {
				$found = true;
			}
		}
		curl_close($curl);

		return $found;
	}

	/**
	 * 我的消费者列表
	 */
	public function cus_list($data)
	{
		global $json;
		$token     = isset($data->token) ? $data->token: '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;

		$user_info = getUserInfo($token);
		if (!$user_info)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}
		$store_id = $user_info['user_id'];
		if ($user_info['serve_type'] != 1)
		{
			$return['statusCode'] 	 	  = 0;
			$return['error']['errorCode'] = '101';
			$return['error']['msg'] 	  = '必须当前裁缝才能查看自己的消费者';
			return $json->encode($return);
		}

		$limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
		$conditions = "storeid=".$store_id;
		$member_figure_mod = m('customer_figure');

		$figure_list =$member_figure_mod->find(array(
			'fields'			=> "figure_sn,storeid,userid,customer_name,customer_mobile,order_num,figure,is_apply",
			'conditions' 		=> $conditions,
			'limit'				=> $limit,
			'order'			    => 'lasttime DESC',
			'index_key'			=> '',
		));
		$count  = $member_figure_mod->get(array(
			'fields'			=>'count(figure_sn) as count',
			'conditions' 		=> $conditions,
		));

		$return = array(
			'statusCode'=> 1,
			'result'    => array(
				'figure_list' => !empty($figure_list) ? $figure_list : '',
				'count'       => $count['count'],
			),
		);
	return $json->encode($return);
	}
	/**
     * 添加消费者(是会员)
     * @access
     * @author liuchao <280181131@qq.com>
     * @return void
     */
	public function add_cus($data)
	{
		global $json;
		$token 			 = isset($data->token) ? $data->token : '' ;
		$nickname  		 = isset($data->nickname) ? $data->nickname : '' ;
		$customer_name   = isset($data->customer_name) ? $data->customer_name : '' ;
		$customer_mobile = isset($data->customer_mobile) ? $data->customer_mobile : 0 ;
		$remark			 = isset($data->remark) ? $data->remark : '' ;

		$user_info = getUserInfo($token);
		$store_id  = $user_info['user_id'];
		if(!$user_info)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}
		if(!$nickname)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] 	  = '昵称不能为空';
			return $json->encode($return);
		}
		if(!preg_match('/^1[3458][0-9]{9}$/', $customer_mobile))
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '101';
			$return['error']['msg'] 	  = '不是正确的手机号';
			return $json->encode($return);
	   	}
	   	if(!$customer_name)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '102';
			$return['error']['msg'] 	  = '消费者姓名不能为空';
			return $json->encode($return);
		}
		$member_mod = m('member');
		$isexists   = $member_mod->find(array('conditions'=>"nickname = '$nickname'" ));
		if(!$isexists)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '105';
			$return['error']['msg'] 	  = '消费者不是会员';
			return $json->encode($return);
		}
		$customer_figure_mod = m('customer_figure');
		$conditions 	     = "customer_name = '$customer_name' && customer_mobile = '$customer_mobile' && storeid = '$store_id'";
		$isexist 		     = $customer_figure_mod->find(array('conditions'=>$conditions,));
		if($isexist)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '103';
			$return['error']['msg'] 	  = '消费者已经存在';
			return $json->encode($return);
		}
		$arr['customer_name']   = $customer_name;
		$arr['customer_mobile'] = $customer_mobile;
		$arr['userid']			= $isexists['user_id'];
		$arr['remark'] 		    = $remark;
		$arr['lasttime'] 		= time();
		$arr['storeid'] 	    = $store_id;
    	if(isset($data->lw)){$customerarr['lw']=$data->lw;}else{$customerarr['lw']='';}
    	if(isset($data->xw)){$customerarr['xw']=$data->xw;}else{$customerarr['xw']='';}
    	if(isset($data->zyw)){$customerarr['zyw']=$data->zyw;}else{$customerarr['zyw']='';}
    	if(isset($data->tw)){$customerarr['tw']=$data->tw;}else{$customerarr['tw']='';}
		if(isset($data->stw)){$customerarr['stw']=$data->stw;}else{$customerarr['stw']='';}
		if(isset($data->zjk)){$customerarr['zjk']=$data->zjk;}else{$customerarr['zjk']='';}
		if(isset($data->zxc)){$customerarr['zxc']=$data->zxc;}else{$customerarr['zxc']='';}
		if(isset($data->yxc)){$customerarr['yxc']=$data->yxc;}else{$customerarr['yxc']='';}
		if(isset($data->qjk)){$customerarr['qjk']=$data->qjk;}else{$customerarr['qjk']='';}
		if(isset($data->hyjc)){$customerarr['hyjc']=$data->hyjc;}else{$customerarr['hyjc']='';}
		if(isset($data->hyc)){$customerarr['hyc']=$data->hyc;}else{$customerarr['hyc']='';}
		if(isset($data->qyj)){$customerarr['qyj']=$data->qyj;}else{$customerarr['qyj']='';}
		if(isset($data->yw)){$customerarr['yw']=$data->yw;}else{$customerarr['yw']='';}
		if(isset($data->tgw)){$customerarr['tgw']=$data->tgw;}else{$customerarr['tgw']='';}
		if(isset($data->td)){$customerarr['td']=$data->td;}else{$customerarr['td']='';}
		if(isset($data->hyg)){$customerarr['hyg']=$data->hyg;}else{$customerarr['hyg']='';}
		if(isset($data->qyg)){$customerarr['qyg']=$data->qyg;}else{$customerarr['qyg']='';}
		if(isset($data->zkc)){$customerarr['zkc']=$data->zkc;}else{$customerarr['zkc']='';}
		if(isset($data->ykc)){$customerarr['ykc']=$data->ykc;}else{$customerarr['ykc']='';}
		if(isset($data->xiw)){$customerarr['xiw']=$data->xiw;}else{$customerarr['xiw']='';}
		if(isset($data->jk)){$customerarr['jk']=$data->jk;}else{$customerarr['jk']='';}
		if(isset($data->part_label_10726)){$customerarr['part_label_10726']=$data->part_label_10726;}else{$customerarr['part_label_10726']='';}
		if(isset($data->part_label_10725)){$customerarr['part_label_10725']=$data->part_label_10725;}else{$customerarr['part_label_10725']='';}
		if(isset($data->hd)){$customerarr['hd']=$data->hd;}else{$customerarr['hd']='';}
		if(isset($data->kk)){$customerarr['kk']=$data->kk;}else{$customerarr['kk']='';}
		if(isset($data->body_type_19)){$customerarr['body_type_19']=$data->body_type_19;}else{$customerarr['body_type_19']='';}
		if(isset($data->body_type_20)){$customerarr['body_type_20']=$data->body_type_20;}else{$customerarr['body_type_20']='';}
		if(isset($data->body_type_24)){$customerarr['body_type_24']=$data->body_type_24;}else{$customerarr['body_type_24']='';}
		if(isset($data->body_type_25)){$customerarr['body_type_25']=$data->body_type_25;}else{$customerarr['body_type_25']='';}
		if(isset($data->body_type_26)){$customerarr['body_type_26']=$data->body_type_26;}else{$customerarr['body_type_26']='';}
		if(isset($data->body_type_3)){$customerarr['body_type_3']=$data->body_type_3;}else{$customerarr['body_type_3']='';}
		if(isset($data->body_type_2000)){$customerarr['body_type_2000']=$data->body_type_2000;}else{$customerarr['body_type_2000']='';}
		if(isset($data->body_type_3000)){$customerarr['body_type_3000']=$data->body_type_3000;}else{$customerarr['body_type_3000']='';}
		if(isset($data->body_type_4000)){$customerarr['body_type_4000']=$data->body_type_4000;}else{$customerarr['body_type_4000']='';}
		if(isset($data->body_type_6000)){$customerarr['body_type_6000']=$data->body_type_6000;}else{$customerarr['body_type_6000']='';}
		if(isset($data->body_type_90000)){$customerarr['body_type_90000']=$data->body_type_90000;}else{$customerarr['body_type_90000']='';}
		if(isset($data->styleLength)){$customerarr['styleLength']=$data->styleLength;}else{$customerarr['styleLength']='';}
		if(isset($data->styleDY)){$customerarr['styleDY']=$data->styleDY;}else{$customerarr['styleDY']='';}
		if(isset($data->height)){$customerarr['height']=$data->height;}else{$customerarr['height']='';}
		if(isset($data->weight)){$customerarr['weight']=$data->weight;}else{$customerarr['weight']='';}
		if(isset($data->part_label_10130)){$customerarr['part_label_10130']=$data->part_label_10130;}else{$customerarr['part_label_10130']='';}
		if(isset($data->part_label_10131)){$customerarr['part_label_10131']=$data->part_label_10131;}else{$customerarr['part_label_10131']='';}
		$arr['lasttime']=time();

		if($customer_figure_mod->add($arr))
		{
			$return['statusCode'] 		  = 1;
			$return['result']['success']  = '消费者添加成功';
			return $json->encode($return);
		}else{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] 	  = '消费者添加失败';
			return $json->encode($return);
		}

	}
	/**
     * 添加消费者(不是会员)
     * @access
     * @author liuchao <280181131@qq.com>
     * @return void
     */
	public function addCus2Member($data)
	{
   	    global $json;
   	    $token 		= isset($data->token) ? $data->token : '' ;
   	    $nickname  		 = isset($data->nickname) ? $data->nickname : '' ;
		$customer_name   = isset($data->customer_name) ? $data->customer_name : '' ;
		$customer_mobile = isset($data->customer_mobile) ? $data->customer_mobile : 0 ;
		$code		= isset($data->code) ? $data->code : '' ;
   	    $return     = array();
   	    //=====实例化
   	    $customer_mod    = m('customer_figure');
   	    $m          = m('member');
   	    $store_mod  = m('store');
   	    $sms_mod    = m('SmsRegTmp');
   	    //=====验证token
   	    $user_info  = getUserInfo($token);
		$store_id   = $user_info['user_id'];
		$store      = $store_mod->get("store_id=$store_id");
		$store_name = $store['store_name'];
		if(!$user_info)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}
		if(!$nickname)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] 	  = '昵称不能为空';
			return $json->encode($return);
		}
		$isexists   = $m->find(array('conditions'=>"nickname = '$nickname'" ));
		if($isexists)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '107';
			$return['error']['msg'] 	  = '昵称重复，请重新命名';
			return $json->encode($return);
		}
		if(!$customer_name)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '102';
			$return['error']['msg'] 	  = '消费者姓名不能为空';
			return $json->encode($return);
		}
		if(!preg_match('/^1[3458][0-9]{9}$/', $customer_mobile))
		{
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => '101',
					'msg'       => '不是正确的手机号',
				),
			);
			return $json->encode($return);
	   	}
   	    //=====查看手机号或者用户名是否重复
   	    if ($m->get("nickname = '$nickname'")) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '此号码已注册！',
				),
			);
   	    	return $json->encode($return);
   	    }
		//验证验证码
   	    if($code) {
   	        $res = $sms_mod->get(array(
   	            'conditions'=> "code='$code' AND phone='$customer_mobile'  ",
   	            'fields'    => '*',
				'order'     => 'id DESC',

   	        ));
   	        if($res['phone']) {
   	            if(time() > $res['fail_time']) {
					$return = array(
						'statusCode' => 0,
						'error' => array(
							'errorCode' => 105,
							'msg'       => '验证码已过期',
						)
					);
   	                return $json->encode($return);
   	            }

   	        }/*else {
				$return = array(
					'statusCode' => 0,
					'error' => array(
						'errorCode' => 106,
						'msg'       => '验证码不正确',
					)
				);
   	            return $json->encode($return);
   	        }*/

   	    } else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 104,
					'msg'       => '验证码不能为空',
				)
			);
   	        return $json->encode($return);
   	    }


   	    //=====生成一个唯一的`yyghid`=====
   	    $arr = array();
   	    //$user_token         = md5($mobile.$password);
   	    $password          = substr($customer_mobile, -6);
		$user_token        = md5($customer_mobile.$password);
   	    $arr['user_name']  = $customer_mobile;
   	    $arr['password']   = md5($password);
   	    $arr['phone_mob']  = $customer_mobile;
   	    $arr['phone_tel']  = $customer_mobile;
   	    $arr['reg_time']   = time();
   	    $arr['last_login'] = time();
   	    $arr['logins']     = 1;
   	    $arr['user_token'] = $user_token;
   	    $arr['nickname']   = $nickname;

   	   	$user_id    = $m->add($arr);
   	   	$user_info  = getUinfoByUid($user_id);


		$customerarr =array();
		$customerarr['customer_name']   = $customer_name;
		$customerarr['customer_mobile'] = $customer_mobile;
		$customerarr['lasttime'] 	    = time();
		$customerarr['storeid'] 	    = $store_id;
		$customerarr['userid'] 		    = $user_info['user_id'];
    	if(isset($data->lw))$customerarr['lw']=$data->lw;
    	if(isset($data->xw))$customerarr['xw']=$data->xw;
    	if(isset($data->zyw))$customerarr['zyw']=$data->zyw;
    	if(isset($data->tw))$customerarr['tw']=$data->tw;
		if(isset($data->stw))$customerarr['stw']=$data->stw;
		if(isset($data->zjk))$customerarr['zjk']=$data->zjk;
		if(isset($data->zxc))$customerarr['zxc']=$data->zxc;
		if(isset($data->yxc))$customerarr['yxc']=$data->yxc;
		if(isset($data->qjk))$customerarr['qjk']=$data->qjk;
		if(isset($data->hyjc))$customerarr['hyjc']=$data->hyjc;
		if(isset($data->hyc))$customerarr['hyc']=$data->hyc;
		if(isset($data->qyj))$customerarr['qyj']=$data->qyj;
		if(isset($data->yw))$customerarr['yw']=$data->yw;
		if(isset($data->tgw))$customerarr['tgw']=$data->tgw;
		if(isset($data->td))$customerarr['td']=$data->td;
		if(isset($data->hyg))$customerarr['hyg']=$data->hyg;
		if(isset($data->qyg))$customerarr['qyg']=$data->qyg;
		if(isset($data->zkc))$customerarr['zkc']=$data->zkc;
		if(isset($data->ykc))$customerarr['ykc']=$data->ykc;
		if(isset($data->xiw))$customerarr['xiw']=$data->xiw;
		if(isset($data->jk))$customerarr['jk']=$data->jk;
		if(isset($data->part_label_10726))$customerarr['part_label_10726']=$data->part_label_10726;
		if(isset($data->part_label_10725))$customerarr['part_label_10725']=$data->part_label_10725;
		if(isset($data->hd))$customerarr['hd']=$data->hd;
		if(isset($data->kk))$customerarr['kk']=$data->kk;
		if(isset($data->body_type_19))$customerarr['body_type_19']=$data->body_type_19;
		if(isset($data->body_type_20))$customerarr['body_type_20']=$data->body_type_20;
		if(isset($data->body_type_24))$customerarr['body_type_24']=$data->body_type_24;
		if(isset($data->body_type_25))$customerarr['body_type_25']=$data->body_type_25;
		if(isset($data->body_type_26))$customerarr['body_type_26']=$data->body_type_26;
		if(isset($data->body_type_3))$customerarr['body_type_3']=$data->body_type_3;
		if(isset($data->body_type_2000))$customerarr['body_type_2000']=$data->body_type_2000;
		if(isset($data->body_type_3000))$customerarr['body_type_3000']=$data->body_type_3000;
		if(isset($data->body_type_4000))$customerarr['body_type_4000']=$data->body_type_4000;
		if(isset($data->body_type_6000))$customerarr['body_type_6000']=$data->body_type_6000;
		if(isset($data->body_type_90000))$customerarr['body_type_90000']=$data->body_type_90000;
		if(isset($data->styleLength))$customerarr['styleLength']=$data->styleLength;
		if(isset($data->styleDY))$customerarr['styleDY']=$data->styleDY;
		if(isset($data->height))$customerarr['height']=$data->height;
		if(isset($data->weight))$customerarr['weight']=$data->weight;
		if(isset($data->part_label_10130))$customerarr['part_label_10130']=$data->part_label_10130;
		if(isset($data->part_label_10131))$customerarr['part_label_10131']=$data->part_label_10131;
		$customer_id = $customer_mod->add($customerarr);

		if ($user_id) {
   	        Qrcode('user', $user_id, SITE_URL);
   	        $mqrcode = getQrcodeImage('user', $user_id, 4);
   	    	$erweima = $m->edit($user_id,array("erweima_url" => $mqrcode));

			if($erweima && $customer_id) {
				$mod_msglog = m('msglog');
				$mod_msglog->drop("to_mobile='$customer_mobile'");
				$content = "尊敬的".$customer_name."，您已成为".$store_name."的消费者，已为您开通阿里裁缝账号：".$customer_mobile."，密码：".$password."，请登录麦富迪ID体验定制服务。";
				$re = SendSms($customer_mobile,$content);
				$add_msglog = array(
    				'user_id' => $store_id,
    				'user_name' => $customer_name,
    				'to_mobile' => $customer_mobile,
    				'content' => $content,
    				'time' => time(),
    	         );
    			$mod_msglog->add($add_msglog);

				$user  = array(
					'user_id'         => $user_info['user_id'],
					'headImgUrl'      => $user_info['avatar'],
					'nickname'        => $user_info['nickname'],
					'phoneNum'        => $user_info['phone_mob'],
					'password'        => $user_info['password'],
					'Score'           => $user_info['point'],
					'user_token'      => $user_info['user_token'],
					'lastUpdateTime ' => $user_info['last_login'],
				);
				//组装返回数据
				$return = array(
					'statusCode' => 1,
					'result'     => array(
						'token'    => $user_info['user_token'],
						'user'     => $user,
						'customer_info' => $customerarr,
					),
				);

   	    	} else {
				$return = array(
					'statusCode' => 0,
					'error' => array(
						'errorCode' => 103,
						'msg'       => '消费者添加失败',
					),
				);

			}
			return $json->encode($return);
   	    } else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '消费者添加失败',
				),
			);
			return $json->encode($return);
		}
	}

	/**
     * 修改消费者
     */

	public function edit_cus($data)
	{
		global $json;
		$token 			 = isset($data->token) ? $data->token : '' ;
		$customer_name   = isset($data->customer_name) ? $data->customer_name : '' ;
		$customer_mobile = isset($data->customer_mobile) ? $data->customer_mobile : 0 ;
		$remark			 = isset($data->remark) ? $data->remark : '' ;
		$figure_sn		 = isset($data->figure_sn) ? intval($data->figure_sn ): '' ;
		$user_info       = getUserInfo($token);
		$storeid 		 = $user_info['user_id'];
		if(!$user_info)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '100';
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}

		$conditions     = "storeid = '$storeid' && figure_sn = '$figure_sn'";
		$customer_figure_mod = m('customer_figure');
		if(!$customer_figure_mod->find(array('conditions'=>$conditions)))
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '103';
			$return['error']['msg'] 	  = '消费者不存在';
			return $json->encode($return);
		}
		if(!preg_match('/^1[3458][0-9]{9}$/', $customer_mobile)){
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '101';
			$return['error']['msg'] 	  = '不是正确的手机号';
			return $json->encode($return);
	   	}
	   	if(!$customer_name)
		{
			$return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '102';
			$return['error']['msg'] 	  = '消费者姓名不能为空';
			return $json->encode($return);
		}
		$arr['customer_name']   = $customer_name;
		$arr['customer_mobile'] = $customer_mobile;
		$arr['remark'] 		    = $remark;
		$arr['lasttime'] 		= time();
		if(isset($data->lw))$arr['lw']=$data->lw;
    	if(isset($data->xw))$arr['xw']=$data->xw;
    	if(isset($data->zyw))$arr['zyw']=$data->zyw;
    	if(isset($data->tw))$arr['tw']=$data->tw;
		if(isset($data->stw))$arr['stw']=$data->stw;
		if(isset($data->zjk))$arr['zjk']=$data->zjk;
		if(isset($data->zxc))$arr['zxc']=$data->zxc;
		if(isset($data->yxc))$arr['yxc']=$data->yxc;
		if(isset($data->qjk))$arr['qjk']=$data->qjk;
		if(isset($data->hyjc))$arr['hyjc']=$data->hyjc;
		if(isset($data->hyc))$arr['hyc']=$data->hyc;
		if(isset($data->qyj))$arr['qyj']=$data->qyj;
		if(isset($data->yw))$arr['yw']=$data->yw;
		if(isset($data->tgw))$arr['tgw']=$data->tgw;
		if(isset($data->td))$arr['td']=$data->td;
		if(isset($data->hyg))$arr['hyg']=$data->hyg;
		if(isset($data->qyg))$arr['qyg']=$data->qyg;
		if(isset($data->zkc))$arr['zkc']=$data->zkc;
		if(isset($data->ykc))$arr['ykc']=$data->ykc;
		if(isset($data->xiw))$arr['xiw']=$data->xiw;
		if(isset($data->jk))$arr['jk']=$data->jk;
		if(isset($data->part_label_10726))$arr['part_label_10726']=$data->part_label_10726;
		if(isset($data->part_label_10725))$arr['part_label_10725']=$data->part_label_10725;
		if(isset($data->hd))$arr['hd']=$data->hd;
		if(isset($data->kk))$arr['kk']=$data->kk;
		if(isset($data->body_type_19))$arr['body_type_19']=$data->body_type_19;
		if(isset($data->body_type_20))$arr['body_type_20']=$data->body_type_20;
		if(isset($data->body_type_24))$arr['body_type_24']=$data->body_type_24;
		if(isset($data->body_type_25))$arr['body_type_25']=$data->body_type_25;
		if(isset($data->body_type_26))$arr['body_type_26']=$data->body_type_26;
		if(isset($data->body_type_3))$arr['body_type_3']=$data->body_type_3;
		if(isset($data->body_type_2000))$arr['body_type_2000']=$data->body_type_2000;
		if(isset($data->body_type_3000))$arr['body_type_3000']=$data->body_type_3000;
		if(isset($data->body_type_4000))$arr['body_type_4000']=$data->body_type_4000;
		if(isset($data->body_type_6000))$arr['body_type_6000']=$data->body_type_6000;
		if(isset($data->body_type_90000))$arr['body_type_90000']=$data->body_type_90000;
		if(isset($data->styleLength))$arr['styleLength']=$data->styleLength;
		if(isset($data->styleDY))$arr['styleDY']=$data->styleDY;
		if(isset($data->height))$arr['height']=$data->height;
		if(isset($data->weight))$arr['weight']=$data->weight;
		if(isset($data->part_label_10130))$arr['part_label_10130']=$data->part_label_10130;
		if(isset($data->part_label_10131))$arr['part_label_10131']=$data->part_label_10131;
		$customer_id = $customer_figure_mod->edit("storeid = '$storeid' && figure_sn = '$figure_sn'",$arr);
		if($customer_id)
		{
            $return['statusCode'] 		  = 1;
			$return['result']['success']  = '消费者修改成功';
			return $json->encode($return);
      	}else{
            $return['statusCode'] 		  = 0;
			$return['error']['errorCode'] = '104';
			$return['error']['msg'] 	  = '消费者修改失败';
			return $json->encode($return);
		}
	}
	/**
     * 我的定制需求
	 *
     * @param String $token  用户标识；int $status 状态过滤（状态：1进行中；2已完成；3已关闭（已作废）；为空，所有定制需求）
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */

	public function demandCustomsList($token, $status)
	{
		global $json;
		$user_info = getUserInfo($token);

		if (!$user_info) {
			$return['statusCode'] = 0;
			$return['msg']        = 'token错误';
			return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		$conditions = '';
		$conditions = "user_id = '$user_id' ";

		if ($status == 1) {
			$conditions = " AND status = 2";//2已发
		} elseif ($status == 2) {
			$conditions = " AND status = 4";//已完成
		} elseif ($status == 3) {
			$conditions = " AND status = 0";//已作废
		}

		$demand_mod = m('demand');
		$demand_list = $demand_mod->find(array(
			'conditions' => $conditions,
			'fields'     => 'md_id, md_sn, md_title, price_rank, take_in, last_time, params ',
			'order'		 => 'last_time DESC',
			'index_key'	 => '',
		));

		$demand_list_arr = array();
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();

		if($demand_list) {
			foreach($demand_list as $val) {
				$params = unserialize($val['params']);
				if($params) {
					foreach($params as $v) {
						if($v['key'] == '定制预算') {
							$budget = $v['val'];
						}
					}

				}
				//获得用户头像
				$avatar = $objAvatar->avatar_show($val['user_id'], 'big');
				$demand_list_arr[] = array(
					'md_id'    => $val['md_id'],
					'user_id'  => $val['user_id'],
					'md_title' => $val['md_title'],
					'take_in'  => $val['take_in'],
					'add_time' => $val['add_time'],
					'avatar'   => $avatar,
					'budget'   => $budget,
				);
			}

		}

		$count = $demand_mod->getCount();

		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'demand_list' => !empty($demand_list_arr) ? $demand_list_arr : '',
				'count'       => $count,
			),
		);

		return $json->encode($return);
	}
	
	/**
	* 即拍即做
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function jipai($token,$baseImg,$backImg,$inImg,$detailImg,$order_info) 
	{
		global $json;
		$user_info = getUserInfo($token);
		
		if (!$user_info) 
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token错误',
					)
			);
			
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		include PROJECT_PATH."/includes/ImageTool.class.php";

		$imageTool = new ImageTool( ROOT_PATH.'/upload/jipaijizuo/');
		$data = array();
		
		//===== 正 反 内 面图 =====
		if ($baseImg) 
		{
			$data['url'] = $imageTool->uploadImage($baseImg);
			$data['url_thumb1'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url'], 400, 400);
	
		}
		if ($backImg)
		{
			$data['url2'] = $imageTool->uploadImage($backImg);
			$data['url2_thumb1'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url2'], 400, 400);
		}
		if ($inImg)
		{
			$data['url3'] = $imageTool->uploadImage($inImg);
			$data['url3_thumb1'] = $imageTool->makeThumb($imageTool->_upload_dir.$data['url3'], 400, 400);
		}
		
		//===== 添加数据表 =====
		$data['order_info'] = $order_info;
		$data['uid'] = $user_id;
		$data['add_time'] = time();
		$data['cate'] = 3;
		$userphoto_mod = m('userphoto');
// print_exit($data);
		$userphoto_id = $userphoto_mod->add($data);
		if (!$userphoto_id) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '上传失败',
				)
			);
	        return $json->encode($return);
		}
		
		//=====  处理相册数据 =====
		$data_gallery = array();
		$data_gallery['project_id'] = $userphoto_id;
		$photo_gallery_mod = m('PhotoGallery');
		if ($detailImg)
		{
			$gallery_image_names = $imageTool->multiUpload($detailImg);
			if ($gallery_image_names) 
			{
				foreach ($gallery_image_names as $key => $value)
				{
					if (!$value) 
					{
						continue;
					}
					$data_gallery['img_url'] = $value;
					$photo_gallery_mod->add($data_gallery);
				}
			}
		}
		
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '成功',
				'data'    => array('id'=>$userphoto_id),
			)
		);
		return $json->encode($return);
	}
	
	/**
	* 即拍列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function jipai_list($token,$pageIndex,$pageSize) 
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		$user_info = getUserInfo($token);
		
		if (!$user_info) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		$conditions = "uid = $user_id";
		$userphoto_mod = m('userphoto');
		$photo_list = $userphoto_mod->find(array(
			'fields'	=> "id,CONCAT('$img_url',url_thumb1) as url",	
			'conditions' =>  $conditions,
			'limit'			=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'		=> 'id desc',	
			'index_key'	=> "",
		));
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '获取成功',
				'data'    => $photo_list,
			)
		);
		return $json->encode($return);
	}
	
	
	/**
	* 即拍详情
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function jipai_info($id,$token) 
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		$user_info = getUserInfo($token);
		
		if (!$user_info) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
	        return $json->encode($return);
		}
		if (!$id)
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'ID不能为空',
				)
			);
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		$userphoto_mod = m('userphoto');
		$photo_gallery_mod = m('PhotoGallery');
		$conditions = "id = $id";
		$fileds = "id,paystate,status,order_info,uid,CONCAT('$img_url',url) as url,CONCAT('$img_url',url2) as url2,CONCAT('$img_url',url3) as url3";
		$photo_info = $userphoto_mod->get(array(
			'conditions'	=>	$conditions,
			'fields'	=> $fileds,
		));
		if ($photo_info['uid'] != $user_id) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '错误',
				)
			);
	        return $json->encode($return);
		}
		$data_img_arr = array($photo_info['url'],$photo_info['url2'],$photo_info['url3']);
		$data_img_arr = array_filter($data_img_arr); 
		//===== 取出相册的图片 =====
		$conditions = "project_id = $id AND cate='jipai' ";
		$gallery_list = $photo_gallery_mod->find(array(
			'conditions'	=> $conditions,
			'fields'	    => "img_url",
		));
		if ($gallery_list) 
		{
			foreach ($gallery_list as $key => $value)
			{
				$url = $img_url.$value['img_url'];
				$data_img_arr[] = $url;
			}
		}
		
		$return_data['jipai']['order_info'] = $photo_info['order_info'];
		$return_data['jipai']['jipai_id'] = $photo_info['id'];
		$return_data['jipai']['paystate'] = $photo_info['paystate'];
		$return_data['jipai']['status']   = $photo_info['paystate'];
		if ($photo_info['paystate'] == 2) 
		{
			$order_jpjz_mod = m('orderjpjz');
			$order_jpjz_info = $order_jpjz_mod->get('jp_id = '.$photo_info['id']);
			if ($order_jpjz_info) 
			{
				$return_data['jipai']['pay_url'] = "http://m.kuteid.com/order-paycenter.html?id=".$order_jpjz_info['order_sn'];
			}
		}
		
		$return_data['jipai']['galler_list'] = $data_img_arr;
		
		//===== 取出评论的前十条 =====
		
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '获取成功',
				'data'    => $return_data,
			)
		);
		return $json->encode($return);
	}
	
	/**
	 *删除即拍
	 * @version 1.0.0
	 * @author liang.li <1184820705@qq.com>
	 * @2015-3-28
	 */
	function jipai_del($id,$token)
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		$user_info = getUserInfo($token);
	
		if (!$user_info)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token错误',
					)
			);
			return $json->encode($return);
		}
		if (!$id)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'ID不能为空',
					)
			);
			return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		$userphoto_mod = m('userphoto');
		$photo_gallery_mod = m('PhotoGallery');
		$id = intval($id);
		if (!$id) 
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '此数据不存在',
					)
			);
			return $json->encode($return);
		}
		
		$conditions = "id = $id";
		$fileds = "id,order_info,uid,CONCAT('$img_url',url) as url,CONCAT('$img_url',url2) as url2,CONCAT('$img_url',url3) as url3";
		$photo_info = $userphoto_mod->get(array(
				'conditions'	=>	$conditions,
				'fields'	=> $fileds,
		));
		
		
		if ($photo_info['uid'] != $user_id)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '此数据不存在',
					)
			);
			return $json->encode($return);
		}
		
		
		
		$userphoto_mod->drop($id);
		/* $data_img_arr = array($photo_info['url'],$photo_info['url2'],$photo_info['url3']);
	
		
		$conditions = "project_id = $id AND cate='jipai' ";
		$gallery_list = $photo_gallery_mod->find(array(
				'conditions'	=> $conditions,
				'fields'	    => "img_url",
		));
		if ($gallery_list)
		{
			foreach ($gallery_list as $key => $value)
			{
				$url = $img_url.$value['img_url'];
				$data_img_arr[] = $url;
			}
		}
	
		$return_data['jipai']['order_info'] = $photo_info['order_info'];
		$return_data['jipai']['jipai_id'] = $photo_info['id'];
		$return_data['jipai']['galler_list'] = $data_img_arr; */
	
		//===== 取出评论的前十条 =====
	
		$return = array(
				'statusCode' => 1,
				'result' => array(
						'success' => '成功',
				)
		);
		return $json->encode($return);
	}
	
	
	
	/**
	* 发表评论
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function subComment($token,$id,$content,$commentImg) 
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		$user_info = getUserInfo($token);
		
		if (!$user_info) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		if (!$id)
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'ID不能为空',
				)
			);
	        return $json->encode($return);
		}
		$userphoto_mod = m('userphoto');
		if(!$userphoto_mod->get($id))
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '此作品不存在',
				)
			);
	        return $json->encode($return);
		}
		
		
		
		$comment_mod = m('CommentsPhoto');
		
		$data['uid'] = $user_id;
		$data['user_name'] = $user_info['user_name'];
		$data['content'] = $content;
		$data['comment_id'] = $id;
		$data['cate'] = 0;
		$data['add_time'] = time();
		$comment_id = $comment_mod->add($data);
		if (!$comment_id)
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '添加评论表失败',
				)
			);
	        return $json->encode($return);
		}
		
		//=====  处理相册数据 =====
		$data_gallery = array();
		$data_gallery['project_id'] = $comment_id;
		$data_gallery['cate'] = 'msg';
		$photo_gallery_mod = m('PhotoGallery');
		include PROJECT_PATH."/includes/ImageTool.class.php";
		$imageTool = new ImageTool( ROOT_PATH.'/upload/jipaijizuo/');
		
		if ($commentImg)
		{
			$gallery_image_names = $imageTool->multiUpload($commentImg);
			if ($gallery_image_names)
			{
				foreach ($gallery_image_names as $key => $value)
				{
					if (!$value)
					{
						continue;
					}
					$data_gallery['img_url'] = $value;
					$photo_gallery_mod->add($data_gallery);
				}
			}
		}
		
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '成功',
			)
		);
		return $json->encode($return);
	}
	
	
	
	/**
	* 评论列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function comment_list($token,$pageIndex,$pageSize,$id) 
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		$user_info = getUserInfo($token);
		
		if (!$user_info) 
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				)
			);
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		//获取用户图像
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		//获取头像
		//$avatar = $objAvatar->avatar_show($user_id,'big');
		if (!$id)
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'ID不能为空',
				)
			);
	        return $json->encode($return);
		}
		$userphoto_mod = m('userphoto');
		if(!$userphoto_mod->get($id))
		{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '此作品不存在',
				)
			);
	        return $json->encode($return);
		}
		$comment_mod = m('CommentsPhoto');
		$photo_gallery_mod = m('PhotoGallery');
		$conditions  = "comment_id = $id";
		$comment_list = $comment_mod->find(array(
			'conditions' => $conditions,
			'fields'	=> "*",
			'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'     => "add_time DESC",
			'index_key'	=> "",
		));
		
		if ($comment_list) 
		{
			foreach ($comment_list as $key => $value)
			{
				$project_id = $value['id'];
				$gallery_list = $photo_gallery_mod->find(array(
					'conditions' => "project_id=$project_id AND cate='msg' ",
					'fields'	=> "CONCAT('$img_url',img_url) as url",		
				));
				$url = array();

				if ($gallery_list) 
				{
					foreach ($gallery_list as $key1 => $value1)
					{
						$url[] = $value1['url'];
					}
				}
				$comment_list[$key]['img_url'] = $url;
				
				$avatar = $objAvatar->avatar_show($value['uid'],'big');
				$comment_list[$key]['avatar']  = $avatar;
				
			}
		}
		
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '成功',
				'data'    => $comment_list,
			)
		);
		return $json->encode($return);
	}
	
	
	/**
	* 衬衣列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function chen_list($pageSize,$pageIndex,$caizhi,$color,$order,$sex,$yinhua) 
	{
		global $json;
		$sample_mod = m('shirt');
		$conditions = "";
		if ($caizhi) 
		{
			$conditions .= "v_figure = '$caizhi' AND ";
		}
		if ($color) 
		{
			$conditions .= " v_color = '$color' AND ";
		}
		if ($sex)
		{
			$conditions .= "v_sex = '$sex' AND ";
		}
		if ($yinhua)
		{
			$conditions .= "v_yinhua = '$yinhua' AND ";
		}
		
		$order = "";
		if ($order == 1) 
		{
			$order = "v_price asc";
		}
		elseif ($order == 2)
		{
			$order = "v_price desc";
		}
		else 
		{
			$order = "v_clicknum desc";
		}
// echo $order;exit;
		$filename = PROJECT_PATH.'includes/data/config/size_json/0006.size.json';
		$jsonString = file_get_contents($filename);
		$jsonData = json_decode($jsonString,true);
		$size_list = $jsonData['sizeAll'] ;
		$size = array();
		foreach ($size_list as $key => $value) 
		{
		    $size[] = $value['Id'];
		}
		
		
		$conditions = trim(trim($conditions," "),"AND");
		
		//$size = array('38/XXS','39/XS','40/S','41/M','42/L','43/XL','44/XXL','45/XXXL','46/XXXXL');
		$sample_list = $sample_mod->find(array(
			'conditions' => $conditions,
			'fields' => "*",
			'index_key' => "",
		    'limit'			=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order' => $order,
		));
		if ($sample_list) 
		{
			
			$emb_mod = m('embs');
			$condition_color = "type = 21 AND emb='emb_color' ";
			$condition_font = "type = 21 AND emb='emb_font' ";
			$condition_site = "type = 21 AND emb='emb_site' ";
			$emb_color_list = $emb_mod->_embs($condition_color);
			$emb_font_list  = $emb_mod->_embs($condition_font);
			$emb_site_list  = $emb_mod->_embs($condition_site);
			foreach ($sample_list as $key => $value)
			{
				//===== 推荐相关 =====
				$v_yinhua = $value['v_yinhua'];
				$link_list = $sample_mod->find(array(
				'conditions' => "v_yinhua = $v_yinhua AND  v_id != {$value['v_id']} ",
				'fields' => "*",
				'index_key' => "",
			    'limit'			=> 10,
				'order' => $order,
				));
				$sample_list[$key]['link_list'] = $link_list;
				$sample_list[$key]['size'] = $size;
				$sample_list[$key]['emb_color'] = $emb_color_list;
				$sample_list[$key]['emb_font'] = $emb_font_list;
				$sample_list[$key]['emb_site'] = $emb_site_list;
				
				
// 				$rs = $sample_mod->setInc(" v_id = {$value['v_id']} " , 'v_clicknum',1);
			}
		}

		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'data' => !empty($sample_list) ? $sample_list :array(),
			),
		);
		return $json->encode($return);
	}
	
	
	/**
	 *衬衣喜欢数加1
	 *@author liang.li <1184820705@qq.com>
	 *@2015年4月30日
	 */
	function chen_likes($id)
	{
	    global $json;
	    $dis_mod = m('shirt');
	     
	    // 	 print_exit($dis_mod);
	    $dis_mod->setInc(array('id'=>$id),'v_clicknum');
	    $info = $dis_mod->get($id);
	    //组装返回数据
	    $return = array(
	        'statusCode' => 1,
	        'result'     => array(
	            'data' => array($info['v_clicknum']),
	        ),
	    );
	    return $json->encode($return);
	     
	}
	
	
	
	/**
	* 印花分类列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function yh_list() 
	{
		global $json;
		$yh_category_mod = m('YhCategory');
		$yh_list = $yh_category_mod->find(array(
			'fields' => "*",
			'index_key' => "",	
		));
		
		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'data' => !empty($yh_list) ? $yh_list :'',
			),
		);
		return $json->encode($return);
	}
	
	
	/**
	* 印花列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function yh_template($cate_id,$pageSize,$pageIndex) 
	{
		global $json;
		$yh_mod = m('YhTemplate');
		$conditions = "";
		if ($cate_id) 
		{
			$conditions = "category=$cate_id";
		}
		$yh_list = $yh_mod->find(array(
			'conditions' => $conditions,
			'fields' => "*",
			'index_key' => "",
            'order' => "id DESC",
			'limit'			=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
		));
		
		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'data' => !empty($yh_list) ? $yh_list :'',
			),
		);
		return $json->encode($return);
	}
	
	
	
	/**
	* 品类列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function pinlie_list() 
	{
		global $json;
		
		$config = array (
              0 => 
              array (
                'name' => '影视热点',
                'img' => 'http://cy.dev.mfd.cn/static/img/cimg_2.png',
                'link' => '',
              ),
              1 => 
              array (
                'name' => '时尚大叔',
                'img' => 'http://cy.dev.mfd.cn/static/img/cimg_3.png',
                'link' => '',
              ),
              2 => 
              array (
                'name' => '大牌同款',
                'img' => 'http://cy.dev.mfd.cn/static/img/cimg_4.png',
                'link' => '',
              ),
              3 => 
              array (
                'name' => '休闲春夏',
                'img' => 'http://cy.dev.mfd.cn/static/img/cimg_5.png',
                'link' => '',
              ),
              4 => 
              array (
                'name' => '亲子系列',
                'img' => 'http://cy.dev.mfd.cn/static/img/cimg_6.png',
                'link' => '',
              ),
            );
		//$config = include PROJECT_PATH.'/includes/data/config/config.php';
		//$config = include ROOT_PATH . '/data/dissertation/jpjz_config.php';

		/* $config[100] =  array(    
        'name' => '衬衫印花',
        'img' =>   'http://cy.dev.mfd.cn/static/img/cimg_1.png',
        'link' => '',
        ); */

		$arr = array(    
        'name' => '衬衫印花',
        'img' =>   'http://cy.dev.mfd.cn/static/img/cimg_1.png',
        'link' => '',
		 'id' => '100',
        );
		

		foreach ($config as $key => $value)
		{
			$config[$key]['id'] = $key;
		} 

		
		array_unshift($config, $arr);


		//组装返回数据
		$return = array(
				'statusCode' => 1,
				'result'     => array(
						'data' => !empty($config) ? $config :'',
				),
		);
		return $json->encode($return);
	}
	
	/**
	* 主题列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-3-28
	*/
	function dis_list($type,$pageSize,$pageIndex) 
	{
		global $json;
		$img_url = LOCALHOST1;
		$dis_mod      = m('JpjzDissertation');
		$custom_attr = m('cusAttr');
		$conditions = "";
		$config = include PROJECT_PATH.'/includes/data/config/config.php';
		if (!$type) 
		{
			$type = 0;
		}
		$conditions .= " AND cat ='{$type}' ";
	  	$data = $dis_mod->find(array(
	  			'conditions' => '1=1 '.$conditions,
	  			'limit'		 =>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
	  			'order' => 'sort_order DESC',
	  			'index_key' => "",
	  	));
	  	
		$_mod_cusGallery = &m("customgallery");
		foreach($data as $key=>$val) {
			$gallery_list = $_mod_cusGallery->find(array(
                'conditions' => "custom_id = '{$val['id']}'",
				'fields'=>"CONCAT('$img_url',middle_img) as middle_img, source_img",
				'order' => 'sort asc',
				'index_key' => '',
            ));
			$data[$key]['gallery'] = $gallery_list;			
		}
	  	$ids='';
		$link_mod = m('links');

	  	foreach ($data as $k=>$v)
	  	{
	  		$c_id = $v["id"];
	  		$cus_list = $link_mod->find(array(
	  		 'conditions' => "cst.id != 'NULL' AND  d_id=$c_id",
	  		  'join' =>"has_custom",
	  		   'fields' => "*",
	  			'order' => 'lorder asc',
	  			'index_key' => '',
	  		));

	  		if ($cus_list) 
	  		{
	  		    foreach ($cus_list as $key => $value) 
	  		    {

	  		        $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格
                    $data[$k]['price'] += $cus_list[$key]['price'];
	  		        $filename = PROJECT_PATH.'includes/data/config/size_json/'.$value['category'].'.size.json';
	  		        $jsonString = file_get_contents($filename);
	  		        $jsonData = json_decode($jsonString,true);
	  		        $size_list = $jsonData['sizeAll'] ;
	  		        $size = array();
	  		        foreach ($size_list as $key1 => $value1)
	  		        {
	  		            $size[] = $value1['Id'];
	  		        }
	  		        
	  		        
	  		        $cus_list[$key]['size'] = $size;
	  		        $cus_list[$key]['size_url'] = "http://m.cy.mfd.cn/size.html";
					//获得相册
					$gallery_list = $_mod_cusGallery->find(array(
						'conditions' => "custom_id = '{$value['id']}'",
						'fields'     => "source_img as img",
						'order'      => 'sort asc',
						'index_key'	 => '',
					));

					$cus_list[$key]['gallery'] = !empty($gallery_list) ? $gallery_list : '';
					
					//获得对应属性
					$linkAttr = $custom_attr->find(array(
					   'conditions' => "custom_id = '{$value['id']}'", 
					));       
					$linkAttrs = array();					
					foreach((array) $linkAttr as $kw => $val){
						$linkAttrs[$val["attr_id"]] = $val["attr_value"];
					}
					
					$cus_list[$key]['style_name'] =  !empty($linkAttrs['5']) ? $linkAttrs['5'] :  '';
					$cus_list[$key]['caizhi'] =  !empty($linkAttrs['1']) ? $linkAttrs['1'] :  '';

	  		    }
				
	  	
	  		}

	  		$data[$k]['cst_list'] = $cus_list;
	  		
	  		
	  	}

		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'data' => !empty($data) ? $data :'',
			),
		);
		return $json->encode($return);
	}
	
	/**
	*content
	*@author liang.li <1184820705@qq.com>
	*@2015年4月30日
	*/
	function dis_likes($id) 
	{
	    global $json;
	    $dis_mod = m('JpjzDissertation');
	    
// 	 print_exit($dis_mod);
	    $dis_mod->setInc(array('id'=>$id),'click_num');
	    $info = $dis_mod->get($id);
	    //组装返回数据
	    $return = array(
	        'statusCode' => 1,
	        'result'     => array(
	            'data' => array($info['click_num']),
	        ),
	    );
	    return $json->encode($return);
	    
	}
	
	
	
	/**
	 * 衬衣添加购物车
	 * @param unknown $param
	 */
	function yh_cart($token,$yhid,$vid) 
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
		
		$user_info = getUserInfo($token);
		if (!$user_info) 
		{
			$return = array(
				'statusCode' => 100,
				'msg' =>'token错误'
			);
	        return $json->encode($return);
		}
		$user_id = $user_info['user_id'];
		
		$yh_cart_mod = m('YhCart');
		$data['uid'] = $user_id;
		$data['v_id'] = $vid;
		$data['yh_id'] = $yhid;
		$data['add_time'] = time();
		$yh_cart_mod->add($data);
		$return = array(
				'statusCode' => 0,
				'msg' =>  '成功',
		);
		return $json->encode($return);
		
	}
	
	
	function getSize() 
	{
		global $json;
		$sizelParent = array(
		    '0003'=>array('165/84A','165/96B','165/100B','165/104D','170/84A','170/92A',
		        '170/96A','170/104C','170/108D','175/88A','175/92A','175/100B','175/104C',
		        '175/112D','180/88A','180/96A','180/100B','180/108C','185/96A','185/104B',
		    ),
		    '0004'=>array('165/74A','165/84A','165/90A','165/94A','170/76A','170/82A','170/88A',
		        '170/98A','170/104A','175/80A','175/84A','175/92A','175/100A','175/108A','180/80A',
		        '180/90A','180/92A','180/104A','185/90A','185/100A'
		    ),
		    '0006'=>array('165/84A','165/96B','165/100B','165/104D','170/84A','170/92A','170/96A','170/104C',
		        '170/108D','175/88A','175/92A','175/100B','175/104C','175/112D','180/88A','180/96A','180/100B',
		        '180/108C','185/96A','185/104B',
		    )
		    
		);
		return $sizelParent;
		
		$arr = array('165/92Y','170/96Y','175/100Y','180/104Y','185/108Y','165/96A','170/100A','175/104A','180/108A','185/112A','165/100B','170/104B','175/108B','180/112B','185/116B','165/100C','170/104C','175/108C','180/112C','185/116C','165/88Y','170/92Y','175/96Y','180/100Y','185/104Y','165/92A','170/96A','175/100A','180/104A','185/108A','165/96B','170/100B','175/104B','180/108B','185/112B');
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '成功',
				'data'    => $arr,
			)
		);
		return $json->encode($return);
	}
	
	/**
	 * 用户自己添加上传印花
	 * @param unknown $param
	 */
	function yh_add($token,$yhImg,$chen_id)
	{
		global $json;
		$img_url = LOCALHOST1."/upload/jipaijizuo/";
	
		$sample_mod = m('sample');
		$chen_info = $sample_mod->get($chen_id);
		if (!$chen_info)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 1,
							'msg'       => '此衬衣不存在',
					),
			);
			return $return;
		}
	
		$user_info = getUserInfo($token);
		if (!$user_info)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token错误',
					),
			);
			return $return;
		}
		$user_id = $user_info['user_id'];
	
		include PROJECT_PATH."/includes/ImageTool.class.php";
		$imageTool = new ImageTool( ROOT_PATH.'/upload/jipaijizuo/');
		$data = array();
	
		if ($yhImg)
		{
			$data['images'] = $img_url.$imageTool->uploadImage($yhImg);
			$data['uid'] = $user_id;
			$data['source'] = 2;
			$data['goods_id'] = $chen_id;
			$data['add_time'] = time();
			$yh_mod = m('YhTemplate');
			$yh_id = $yh_mod->add($data);
				
				
			$return = array(
					'statusCode' => 1,
					'data' =>  array('id'=>$yh_id),
			);
			return $return;
		}
	
		$return = array(
				'statusCode' => 0,
				'msg' =>  '请上传印花',
		);
		return $return;
	
	}
	
	/**
	* 添加购物车
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-4-10
	* url http://api.cy.mfd.cn/soap/club.php?act=addCart&token=b338438c60bdc79e873400fdfefd0172&size=161/48A&yhid=43&chen_id=341
	*/
	function addCart($token,$chen_id,$yhid,$quantity,$cixiu,$size,$yhImg) 
	{
		global $json;
		
		$item['yhid'] = $yhid;
		if ($yhImg) 
		{
			$res = $this->yh_add($token, $yhImg, $chen_id);
			if ($res['statusCode'] == 1) 
			{
				$item['yhid'] = $res['data']['id'];
			}
			else 
			{
				return $json->encode($res);
			}
		}
		
		$user_info = getUserInfo($token);
		if ($user_info) {
			$user_id = $user_info['user_id'];
		} else {
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = 100;
			$return['error']['msg'] 	  = 'token错误';
			return $json->encode($return);
		}
		$sample_mod = m('shirt');
		$sample_info = $sample_mod->get($chen_id);
		if (!$sample_info) 
		{
			$return['statusCode']		  = 0;
			$return['error']['errorCode'] = 1;
			$return['error']['msg'] 	  = '此衬衣不存在';
			return $json->encode($return);
		}
		
		if (!$size) 
		{
		     $return['statusCode']		  = 0;
			$return['error']['errorCode'] = 1;
			$return['error']['msg'] 	  = '请选择尺码';
			return $json->encode($return);
		}
		
		
		
		//$cixiu = '{"518:528","1218:427","422:430","刺绣内容"}';
		//=====  格式刺绣  =====
		$c_tmp = array();
		if ($cixiu) 
		{
		    //=====  去除字符串左右两边的大括号  =====
		    $cixiu = trim($cixiu,"}");
		    $cixiu = trim($cixiu,"{");
		    
		    $cixiu = explode(',', $cixiu);
		    if ($cixiu) 
		    {
               foreach ($cixiu as $key => $value) 
               {
                  $v = explode(":",$value);
                  if (count($v) > 1) 
                  {
                      
                      //=====  如果有任务一项值为空 那么这个刺绣就不成立  =====
                      if (!$v[0] || !$v[1]) 
                      {
                          $c_tmp = array();
                          break;
                      }
                      
                      //=====  3201 衬衫刺绣的固定id  =====
                      if ($v[0] == 3201) 
                      {
                          $c_tmp['3201'] = array("{$v[1]}"=>"{$v[1]}");
                      }
                      else 
                      {
                          $c_tmp[$v[0]] = $v[1];
                      }
                      
                      continue;
                  }
                  
                  //=====  如果有任务一项值为空 那么这个刺绣就不成立  =====
                  if (!$v[0]) 
                  {
                      $c_tmp = array();
                      break;
                  }
                  //=====  如果是刺绣内容这拼上衬衫刺绣内容的固定父id 3676  =====
                  $c_tmp['3676'] = $v[0];
                  
               }        
		    }
		    
		}
		
		
		
		
		$cart_mod = m('cart');
		$data['user_id'] = $user_id;
		$data['goods_id'] = $chen_id;
		$data['quantity'] = $quantity;
		$data['image'] = $sample_info['v_dis_image'];
		$data['source_from'] = 'app';
		$data['size'] = $size;
		$data['type'] = 'chen';
		$data['cloth'] = '0006';
		$data['items'] = json_encode($item);
		$data['embs'] = json_encode($c_tmp,JSON_UNESCAPED_UNICODE);
		
		
		if ($sample_info['v_custom_id'])
		{
		    include_once PROJECT_PATH.'includes/goods.base.php';
		
		    //=====  按说每个衬衣都应该有一个对应的样衣的 但是这了为了保持项目稳定 所以暂时这样写   =====
		    $custom_mod = m('custom');
		    if (!($custom_info = $custom_mod->get($sample_info['v_custom_id'])))
		    {
		        $return['statusCode']		  = 0;
		        $return['error']['errorCode'] = 1;
		        $return['error']['msg'] 	  = '此衬衣数据无工艺';
		        return $json->encode($return);
		    }
// 		echo 22;exit;
		    $goods_base_mod = new BaseGoods();
// 		print_exit($goods_base_mod);
		    $params = $goods_base_mod->_group_info($sample_info['v_custom_id']);
		    
		    
// print_exit($params);
		    $data['params'] = json_encode($params['process']);
		    if (!$params['oFabric']['CODE']) 
		    {
		        $return['statusCode']		  = 0;
		        $return['error']['errorCode'] = 1;
		        $return['error']['msg'] 	  = '找不到面料';
		        return $json->encode($return);
		    }
		    
		    $data['fabric'] = $params['oFabric']['CODE'];
		}
		
		
		
		//=====  查询唯一性  =====
        $cart_info = $cart_mod->get("goods_id={$data['goods_id']} AND type='chen' AND cloth='0006' AND embs = '{$data['embs']}' AND items = '{$data['items']}' AND user_id = $user_id");
        if ($cart_info)
        {
            $cart_mod->setInc(array('rec_id'=>$cart_info['rec_id']),'quantity');
            
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '成功',
                )
            );
            return $json->encode($return);
            
        }
        
		
		$data['ident'] = $this->gen_ident();
		$cart_id = $cart_mod->add($data);
	
		if ($cart_id) 
		{
		    $return = array(
				'statusCode' => 1,
				'result' => array(
						'success' => '成功',
				)
		  );
		}
		else 
		{
		    $return['statusCode']		  = 0;
			$return['error']['errorCode'] = 1;
			$return['error']['msg'] 	  = '添加失败';
		}
		
		
		
		
		return $json->encode($return);
		
	}
    /**
     * 获取购物车内商品数量
     * @version 1.0.0
     * @author liuchao <280181131@qq.com>
     * @2015-7-28
     */
        function getGoodsNum($token){
            global $json;

            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                $this->errorCode = 100;
                $this->msg = '账号不存在';
                return $this->eresult();
            }
            $user_id = $user_info['user_id'];
            $cart_mod = m('cart');
            $goodsNum = $cart_mod->find(array(
                'conditions' => "user_id=".$user_id,
                'count'      => 'ture',
            ));
            $count = $cart_mod->getCount();

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'count'  => $count,
                ),
            );
            return $json->encode($return);
        }
	
	
	/**
	* 主题 添加购物车 套装添加购物车
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-4-11
	*/
	function addDisCart($token,$dis,$suit_id) 
	{
		global $json;
		
		$user_info = getUserInfo($token);
		if (!$user_info)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '账号不存在',
					),
			);
			return $return;
		}
		if (!$suit_id) 
		{
		   $return['statusCode']		  = 0;
		    $return['error']['errorCode'] = 1;
		    $return['error']['msg'] 	  = 'suit_id参数必传';
		    return $json->encode($return);
		}
// dump($user_info);		
		$user_id = $user_info['user_id'];
		$dis = stripslashes($dis);
		//$ids = '{"3":"167/18A","4":"198/32A"}';
		$dis_arr = json_decode($dis,true);
		$cart_mod = m('cart');
		$customs_mod = m('custom');
		$suit_list_mod   = m('suitlist');
		$suit_rel_mod = m('suitrelat');
		
		
		$suit_info = $suit_rel_mod->get("tz_id = $suit_id");
		if (!$suit_info)
		{
		    $this->msg = '无此数据';
		    return $this->eresult();
		}
		$cus_ids = $suit_info['jbk_id'];
		$cus_ids_arr = explode(',', $cus_ids);
		if ($cus_ids_arr) 
		{
		    $arr_cus_ids =array();
		    foreach($cus_ids_arr as $i=>$d)
		    {
		        if($d==0)
		        continue;//if($d!=0){$arr[]=$d}
		        $arr_cus_ids[]=$d;
		    }
		    sort($arr_cus_ids);
		}
// dump($cus_ids_arr);		
		
		//=====  格式化数据  =====
		$ids = array_keys($dis_arr);
		sort($ids);
		if (!$ids) 
		{
		    $return['statusCode']		  = 0;
		    $return['error']['errorCode'] = 1;
		    $return['error']['msg'] 	  = '无数据';
		    return $json->encode($return);
		}
		
		//=====  前段传过来的id和套装对应的id不对应 参数不对  =====
		if ($ids != $arr_cus_ids) 
		{
		    $return['statusCode']		  = 0;
		    $return['error']['errorCode'] = 1;
		    $return['error']['msg'] 	  = 'id无法对应';
		    return $json->encode($return);
		}
		
		$conditions = db_create_in($ids,'id');
		$custom_list = $customs_mod->find(array(
		    'conditions' => $conditions,
		));
// dump($custom_list);		
		if (!$custom_list) 
		{
		    $return['statusCode']		  = 0;
		    $return['error']['errorCode'] = 1;
		    $return['error']['msg'] 	  = '无数据';
		    return $json->encode($return);
		}
	
		include_once PROJECT_PATH.'includes/goods.base.php';
		$goods_base_mod = new BaseGoods();
		$data['user_id'] = $user_id;
		$data['type'] = 'suit';
		$data['source_from'] = 'app';
		$data['dis_ident'] = $this->dis_ident();
		$data['suit_id'] = $suit_id;
		foreach ($custom_list as $key => $value) 
		{
		    if (!$dis_arr[$key]) 
		    {
		        $return['statusCode']		  = 0;
		        $return['error']['errorCode'] = 1;
		        $return['error']['msg'] 	  = '请选择尺码';
		        return $json->encode($return);
		    }
// dump($value);	
// return $value['small_img'];	    
		    $data['image'] = $value['small_img'];
		    $data['cloth'] = $value['category'];
		    $data['goods_id'] = $value['id'];
		    $data['size'] = $dis_arr[$key];
		    
		    //=====  取得工艺  =====
		    $params = $goods_base_mod->_group_info($key);
// dump($params);
		    
		    $data['params'] = json_encode($params['process']);
		    if (!$params['oFabric']['CODE'])
		    {
		        $return['statusCode']		  = 0;
		        $return['error']['errorCode'] = 1;
		        $return['error']['msg'] 	  = '找不到面料';
		        return $json->encode($return);
		    }
		    
		    $data['fabric'] = $params['oFabric']['CODE'];
		    
		    //=====    首推  =====
		    $first = 0;
		    if (count($custom_list) == 1)
		    {
		        //=====  查询此面料是否是首单面料  =====
		        $fab_mod = &m("fabs");
		        if ($fab_mod->get("CODE = '{$data['fabric']}' AND is_first = 1 "))
		        {
		            $pinlei_frist_arr = array('0006','0016','0003');//=====  男女衬衣和男西服才是首单  =====
		            if(in_array($value['category'], $pinlei_frist_arr))
		            {
		                //=====  购物车是否已经存在此首单面料  =====
		                $has = $cart_mod->get("user_id = '$user_id'  AND first = '{$value['category']}' ");
		                if(!$has)
		                {
		                    $aData = $this->processPrice($user_info['user_id']);
	                        if(!in_array($value['category'], $aData))
	                        {
	                            $dis_id = $first = $value['category'];
	                            //$dis_id = $first = '0016';
	                        }
	                    }
	                }
		           // }
		        }
		    }
		   
		    $data['first'] = $first;
		    
		    $data['ident'] = $this->gen_ident();
		    $cart_mod->add($data);
		    //=====  加1   =====
		   /*  $cart_info = $cart_mod->get("goods_id={$data['goods_id']} AND type='suit'  AND size='$value' AND user_id=$user_id ");
		    if ($cart_info)
		    {
		        $cart_mod->setInc(array('rec_id'=>$cart_info['rec_id']),'quantity');
		    }
		    else
		    {
		    } */
		}
		
		$return = array(
				'statusCode' => 1,
				'result' => array(
						'success' => '成功',
				)
		);
		
		
		return $json->encode($return);
		
	}
	
	/**
	 * 首单价格
	 * @param unknown $userid
	 * @return multitype:|multitype:unknown
	 */
	function processPrice($userid){
	    if(!$userid) return array();
	    $mod_first = m("orderfirstlog");
	    $list = $mod_first->find(array(
	        "conditions" => "user_id='{$userid}' AND is_active=1",
	    ));
	
	    $aData = array();
	
	    foreach((array) $list as $key => $val){
	        $aData[] = $val["cloth"];
	    }
	
	    return $aData;
	}
	
	
	/**
	 * 购物车套装的dis_ident
	 * @param number $id
	 * @return string
	 */
	function dis_ident($id = 0)
	{
	    $cart_mod = m('cart');
	    do{
	        $str='abcdefghigklmnopqrstuvwxyz0123456789';
	        for($i=0;$i<8; $i++){
	            $code .= $str[mt_rand(0, strlen($str)-1)];
	        }
	        $il = strlen($id);
	        for ($i=$il;$i<10;$i++){
	            $id = '0'.$id;
	        }
	        $ident =  $code.$id;
	    } while($cart_mod->get(" dis_ident = '{$ident}'"));
	    return $ident;
	}
	
	
	
	function addWorks($token,$work_name,$cloth,$description,$img)
	{
		global $json;
		
		$user_info = getUserInfo($token);
		if (!$user_info)
		{
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token错误',
					),
			);
			return $return;
		}
		$user_id = $user_info['user_id'];
		
		$work_mod = m('works');
		$work_img_mod = m('workimgs');
		$data_work['work_name'] = $work_name;
		$data_work['cloth'] = $cloth;
		$data_work['store_id'] = $user_id;
		$data_work['description'] = $description;
		$data_work['source_from'] = 'app';
		$data_work['add_time'] = time();
		$work_id = $work_mod->add($data_work);
// dump($img);
		$img = stripslashes($img);
		$img = explode(',', $img);
		if ($img) 
		{
			foreach ($img as $v)
			{
				$work_img_mod->add(array('img_url'=>$v,'work_id'=>$work_id));
			}
		}
		
		
		
		
		$return = array(
				'statusCode' => 1,
				'result' => array(
						'success' => '成功',
				)
		);
		
		
		return $json->encode($return);
		
		
	}
	
	function gen_ident($id = 0){
		$cart_mod = m('cart');
		do{
			$str='abcdefghigklmnopqrstuvwxyz0123456789';
            $code ='';
			for($i=0;$i<5; $i++){
				$code .= $str[mt_rand(0, strlen($str)-1)];
			}
			$il = strlen($id);
			for ($i=$il;$i<10;$i++){
				$id = '0'.$id;
			}
			$ident =  $code.$id;
		}
		while($cart_mod->get("ident = '{$ident}'"));
		return $ident;
	}
	
	
	/**
	* 刺绣相关接口 
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-4-21
	*/
	function notMad() 
	{
		$res = file_put_contents(ROOT_PATH . '/data/dissertation/jpjz_config.php', file_get_contents(ROOT_PATH."/t-config.php"));
	dump($res);	
	}
	
	/**
	  * app强制更新配置获取
	  * @author xuganglong <781110641@qq.com>
	  * @param string $app 平台（mfd，figure）；string $flat 包类型（iosc:ios企业版  ioss:AppStore版，android:安卓）；string $version 当前手机安装版本号
	  * @2015年6月4日
	  * @url 
	  */
	function getSetting($app, $flat, $version) {
		global $json;
		if(empty($app) || empty($flat) || empty($version) ){
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => '103',
					'msg'       => '参数错误',
				)
			);
			return $json->encode($return);
		}
		
		$model_setting = &af('appsetting');
		$setting = $model_setting->getAll(); // 载入系统设置数据
		$if_update = 0;
		$to_update = 0;
		$now_version     = $v_version = $setting['software_supdate'][$app][$flat]['version'];//当前最新版本号
		$now_description = $setting['software_supdate'][$app][$flat]['description'];//当前最新版本号
		$now_link        = $setting['software_supdate'][$app][$flat]['link'];//当前最新版本下载地址
		$if_update       = $setting['software_supdate'][$app][$flat]['if_update'];//是否强制更新
		
		//ios版本号需要特殊出处理(1.4.3)
		if($flat=="iosc" || $flat=="ioss" ) {
			$v_version = implode("",explode(".", $now_version));
			$version   = implode("",explode(".", $version));
		}

		if($v_version > $version) {	
			$to_update   = 1;
		}

		$return = array(
			'statusCode'=> 1,
			'result'    => array(
				'if_update'   => $if_update,//是否强制更新，1：强制 0：不强制
				'to_update'   => $to_update,//是否需要更新
				'version'     => !empty($now_version) ? $now_version : 0,//当前最新版本
				'description' => !empty($now_description) ? $now_description : '',//更新备注
				'link'        => !empty($now_link) ? $now_link : '',//下载连接
			),
		);
		return $json->encode($return);
	}
	
	/**
	 * app强制更新配置获取
	 * @author shaozizhen
	 * @param string $app 平台（mfd，figure）；string $flat 包类型（iosc:ios企业版  ioss:AppStore版，android:安卓）；string $version 当前手机安装版本号
	 * @2015年9月22日
	 * @url
	 */
	function appset($app, $flat, $version) {
		//现在只有mfd
		$app='mfd';
		global $json;
		if(empty($app) || empty($flat) || empty($version) ){
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => '103',
							'msg'       => '参数错误',
					)
			);
			return $json->encode($return);
		}
	     $versions=&m("appversion");
	     $app_ver = $versions->get(array(
	     		'conditions' =>"version='{$version}' AND type='{$flat}' AND app='{$app}'",
	     ));
	     $app_sion=$versions->get(array(
	     		'conditions' =>"type='{$flat}' AND app='{$app}'",
	     		'order' =>"id DESC",
	     ));
	     $v_version=$app_sion['version'];
	     $large=$app_sion['large'];
	     $description=$app_sion['description'];
	     $link=$app_sion['link'];
	    // return $json->encode($version);
	    if($flat == 'android'){
	    	$updatecode=$app_sion['updatecode'];
	    }else{
	    	if($version >= $v_version){
	    		$updatecode=0;
	    	}else{
	    		$updatecode=$app_sion['updatecode'];
	    	}	
	    }
	     
	     
		$return = array(
				'statusCode'=> 1,
				'result'    => array(
						'updatecode'   => $updatecode,//是否强制更新，1：选择更新 2强制更新
						'version'     => !empty($v_version) ? $v_version : 0,//当前最新版本
						'description' => !empty($description) ? $description : '',//更新备注
						'updateurl'        => !empty($link) ? $link : '',//下载连接
						'large'       => !empty($large) ? $large : '',//大小
				),
		);
		return $json->encode($return);
	}	
	
	
	
	
}

