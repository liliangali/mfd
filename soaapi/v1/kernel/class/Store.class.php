<?php
	/**
	 * 裁缝
	 * @author liang.li <1184820705@qq.com>
	 * @version $Id: Store.class.php 14317 2016-04-13 05:07:26Z tangsj $
	 * @copyright Copyright 2014 mfd.com
	 * @package app
	 */
	class Store extends Result
	{
		var $wdwl_url = '';
		var $error = '';
		var $token = '';
        var $real_dir = '';
        var $_auth_web_img = '';

        /**
     * 构造函数
	 * @param string $username
     *  可设置当前用户
	 * @access protected
	 * @return void
	 */
	function __construct() {
        $this->real_dir = ROOT_PATH."/upload/auth/";
		$this->_auth_web_img = SITE_URL."/upload/auth/";
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
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页； int $filter 排序方式；int $subject_id 属性id-服务排序（0：不选择 1：上门量体 2：到店服务）;int $city_id 城市id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function index($pageSize, $pageIndex, $filter, $subject_id, $city_id)
	{
		global $json;
		$arr = array();
		$conditions = ' 1=1 ';
		//服务筛选
		if($subject_id) {
			$store_attr_mod = m('storeattr');
			$cond     = "type_id = 1 AND content_id = $subject_id ";

			$store_ids = $store_attr_mod->find(array(
				'conditions' => $cond,
			));
			$store_id = array();
			if ($store_ids) {
				foreach ($store_ids as $k=>$v) {
					$store_id[$v['store_id']] = $v['store_id'];
				}
			} else {
				$return = array(
					'statusCode' => 0,
					'error'      => array(
						'errorCode' => 100,
						'msg'       => '无裁缝',
					),
				);
				return $json->encode($return);
			}
			$id = "";
			if ($store_id) {
				foreach ($store_id as $k=>$v) {
					$id .= $k.",";
				}
			}
			$id = trim($id, ',');

			$conditions .= " AND store_id in (".$id.") ";
		}

		//城市筛选
		if($city_id) {
			$conditions .= " AND city_id = $city_id";
		}

		//排序
		$order = "";
		if ($filter == 1) {
			$order = " popularity DESC";//人气
		} elseif ($filter == 2) {//综合
			$order = " popularity DESC, fans DESC, service_num DESC, level DESC ";
		}

		$store_mod  = m('store');
		$store_list = $store_mod->find(array(
			'conditions' => $conditions,
			'fields'     => 'store_id, store_name, popularity, service_num, store_logo',
			'limit'		 => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'count'      => true,
			'order'		 => $order,
			'index_key'	 => '',
		));

		//获得店铺的好评率
		if(!empty($store_list)) {
			$comment_mod = m('ordercomment');
			foreach($store_list as $key=>$val) {
				$comment_total = $comment_mod->get(array(
					'conditions'=>'status=1 and tailor_id='.$val['store_id'],
					'fields'    =>'count(*) as c',
					'index_key'	=> '',
				));
				$hplv =$comment_mod->get(array(
					'conditions'=>"status=1 and tailor_id='".$val['store_id']."' AND approve=4 OR approve=5",
					'fields'    =>'count(*) as c',
				));
				$hplv = $hplv['c']/$comment_total['c'];
				$store_list[$key]['hplv'] = $hplv;

				//store logo
				$store_logo = LOCALHOST1.'/'.$store_list[$key]['store_logo'];
				$store_list[$key]['store_logo'] = !empty($store_logo) ? $store_logo : '';
			}
		}

		//获得总数
		$store_count =   $store_mod->get(array(
			'conditions' => $conditions,
			'fields'     =>'count(*) as count',
	    ));

		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'store_list' => $store_list,
				'count'      => $store_count,
			),
		);
		return $json->encode($return);

	}

	/**
     * 获取网站基本信息
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function service()
	{
		global $json;
		$store_allow = include ROOT_PATH.'/data/settings.inc.php';//获得配置信息
		$data = array(
			'service_phone' => !empty($store_allow['service_phone']) ? $store_allow['service_phone'] :'',
		);
		
		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'service' => $data,
			),
		);
		return $json->encode($return);
	}
	
	/**
     * 裁缝的主题列表
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function subject()
	{
		global $json;
		$store = include_once PROJECT_PATH.'includes/data/config/store.php';
		$subject = $store['subject']['item'];
		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'subject' => $subject,
			),
		);
		return $json->encode($subject);
	}

	/**
     * 修改裁缝头像
	 *
     * @param string $token 用户标识; $logo_data logo数据
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function editStoreLogo($token,$store_id, $path)
	{
		global $json;
		$m  = m('member');
		$up_time = time();
		require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		
		$path = '/upload/avatar/'.$path;
		$res = $m->edit("user_id = $store_id", array("avatar" => $path, "avatar_time" => $up_time ));
		$avatar   = $objAvatar->avatar_show($store_id, 'big');
		$logo_url = $avatar.'?'.$up_time;//加入头像时间，用于app及时更新头像
		$user_info = $m->get("user_id= '$store_id' ");
                $code = $user_info['invite'];
		if($res){
		    
			//重新更新二维码
//			//Qrcode('store', $store_id, 'http://wap.mfd.cn/member-register.html?ret_url=%2Fmember-index.html&type='.$type.'&er_invite='.$code, $avatar);
//                        $share_url = $user_info['share_url'] = 'http://m.mfd.ds.cotte.cn/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code;
//                        Qrcode('store', $store_id,$share_url, $avatar);
//			$mqrcode = getQrcodeImage('store', $store_id, 4);
//			$erweima_url = '/upload/phpqrcode/'.$mqrcode.'?'.$up_time;
//			// 更新二维码 到 数据库
//			$m->edit("user_id = $store_id", array("erweima_url" => $erweima_url ));
//			$mqrcode = SITE_URL.'/upload/phpqrcode/'.$mqrcode.'?'.$up_time;
			
			$return = array(
				'statusCode' => 1,
				'result' => array(
					'success'   => '头像修改成功',
					'logo_data' => $logo_url,
					'erweima_url' => $mqrcode,
				),
			);
                    return $json->encode($return);
		}else{
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '头像修改失败',
				),
			);
                    return $json->encode($return);
		}
	     

	}

	/**
     * 裁缝详情
	 *
     * @param Int $store_id  裁缝的对应id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function info($store_id)
	{
		global $json;
		$store_mod  = m('store');
		$conditions = "s.store_id = $store_id";
		$store_info = '';
		$store_info = $store_mod->get(array(
			'conditions' => $conditions,
			'fields'     => 'store_id, store_name, store_logo, description, fw_description, fw_logo, popularity, service_num, fans, level, im_qq, im_wx, region_name, address, tel, owner_name',
		));

		//获得属性
		$store_attr = m('storeattr');
        $attr_list  = $store_attr->find(array('conditions'=>'store_id=' . $store_id));
		$service    = '';
		$style      = '';
        //type_id 1、服务2、风格
		if($attr_list) {
			foreach($attr_list as $val){
				if($val['type_id'] == 1){
					$service .= $val['attr_name'].'、';
				}elseif($val['type_id'] == 2){
					$style   .= $val['attr_name'].'、';
				}
			}
		}
		//图片地址转换
		$store_logo = LOCALHOST1.'/'.$store_info['store_logo'];
		$fw_logo    = LOCALHOST1.'/'.$store_info['fw_logo'];
		$store_info['store_logo'] = !empty($store_logo) ? $store_logo : '';
		$store_info['fw_logo']    = !empty($fw_logo) ? $fw_logo : '';

        $service = rtrim($service, '、');
        $style   = rtrim($style, '、');

		$return = array(
			'statusCode' => 1,
			'result'      => array(
				'store_info' => $store_info,
				'service'    => $service,
				'style'      => $style,
			),
		);

		return $json->encode($return);
	}

	/**
     * 裁缝分类服务和主题
	 *
     * @param NUll
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function getCate()
	{
		global $json;
		$cate = include_once PROJECT_PATH.'includes/data/config/store.php';
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'cate' => $cate,
			),
		);
		return $json->encode($return);
	}

	
	
	/**
     * 裁缝获取订单列表
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页； string $token 用户标识；string $order_sn 订单号；int $status 订单状态
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function storeOrderList($data)
	{

	    global $json;
	    $db = db();
	    $return = array();

        $token      = isset($data->token) ? $data->token : '';
        $status     = $data->status != "" ? $data->status : -1;//所有-1
        $where      = isset($data->where) ? $data->where : '';
        $pageSize   = !empty($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex  = !empty($data->pageIndex) ? $data->pageIndex : 1 ;

        if (!$token) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => 'token错误',
                ),
            );
            echo $json->encode($arr);die;
        }
	    $status     = $data->status = ''  ? -1 :$status;//所有-1

		$conditions = "";

	    $user_info = getToken($token);
	    if(empty($user_info)) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '无效token',
				),
			);
	        return $json->encode($return);
	    }
	    $store_id   = $user_info['user_id'];
	    $order_mod = m('order');
	    if($status > -1 ) {
	    	if($status == 60 ) {
	        $conditions .= " AND ( status = 15 OR status ='$status' )";
	   		}elseif($status == 13) {
	   		$conditions .= " AND ( status = 20 OR status = 19 OR status ='$status' )";
	   		}else{
	        $conditions .= " AND status ='$status'";
	        }
	    }

        if($where){
            $conditions .= " AND ( o.cloth = '{$where}' || o.order_sn = '{$where}' || FROM_UNIXTIME(o.add_time,'%Y/%m/%d') = '{$where}' || o.kh_name LIKE '%{$where}%'
            || kh_mobile = '{$where}')";
        }

        //分页
		$page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
        //拼sql语句
		$order_sql = "SELECT o.order_id, o.order_sn, o.order_amount,o.store_name,o.add_time, o.kh_name, o.status, o.cloth,o.fabric, o.user_id,m.nickname,c.cst_image 
		    FROM ".DB_PREFIX."order o LEFT JOIN 
		    ".DB_PREFIX."member m  ON o.user_id=m.user_id LEFT JOIN 
		    ".DB_PREFIX."part p ON o.fabric=p.part_name
		   LEFT JOIN ".DB_PREFIX."customs c ON c.cst_id IN (p.link_cst) WHERE status!=0 AND o.user_id=".$store_id." ".$conditions."  order by o.add_time DESC limit ". $page_list ."";
		$orders = $order_mod->getAll($order_sql);
        //print_r($orders);die;
		$order_count = "SELECT count('order_id') as count FROM ".DB_PREFIX."order as o  WHERE user_id = ".$store_id . $conditions;
		$count = $db->getRow($order_count);

		//获得订单总数
		//$count  = $order_mod->get(array(
		//	'fields'	 => "count('order_id') as count",
		//	'conditions' => "store_id = ".$store_id . $conditions,
		//));
		//为空返回空
	    if(!$orders) {
			$return = array(
				'statusCode' => 1,
				'result' => array(
					'orders'  => '',
				),
			);
	        return $json->encode($return);
	    }
	    $gc = m('gcategory');
	    foreach ($orders as $key=>$val) {
			//获得品类名称
            if($val['cloth'] == '0003') {
                $orders[$key]['cloth']='西服';
            }
            if($val['cloth'] == '0004') {
                $orders[$key]['cloth']='西裤';
            }
            if($val['cloth'] == '0006') {
                $orders[$key]['cloth']='衬衫';
            }
            if($val['cloth'] == '0005') {
                $orders[$key]['cloth']='马甲';
            }
            if($val['cloth'] == '0007') {
                $orders[$key]['cloth']='大衣';
            }
			if($val['status'] == ORDER_PENDING) {
				$orders[$key]['statusName']='待付款';
			}
			if($val['status']==STORE_ACCEPTED) {
				$val['status'] = ORDER_CHECKING;
			}
			if($val['status']==ORDER_ACCEPTED) {
				$val['status'] = ORDER_CHECKING;
			}
			if($val['status'] == ORDER_CHECKING) {
				$orders[$key]['statusName']='审核中';
			}
			if($val['status'] == ORDER_CHECKFAIL) {
				$orders[$key]['statusName']='已取消';//审核失败
			}
			if($val['status'] == ORDER_CHECKED) {
				$val['status'] =ORDER_PRODUCTION;
			}
			if($val['status'] == ORDER_PRODUCTION) {
				$orders[$key]['statusName']='生产中';
			}
			if($val['status']==ORDER_SHIPPED) {
				$orders[$key]['statusName']='平台已发货';
			}
			if($val['status']==STORE_RECEIVED) {
				$orders[$key]['statusName']='已收货';
			}
			if($val['status']==STORE_SHIPPED) {
				$orders[$key]['statusName']='已发货';
			}
			if($val['status']==ORDER_FINISHED) {
				$orders[$key]['statusName']='交易成功';
			}
			if($val['status']==ORDER_CANCELED) {
				$orders[$key]['statusName']='已取消';//已作废
			}
			if($val['status']==ORDER_FIGURE) {
				$orders[$key]['statusName']='待量体';
			}
			if($val['status']==ORDER_TUIKUANZHONG) {
				$orders[$key]['statusName']='订单退款中';
			}
			if($val['status']==ORDER_YINGTUIKUAN) {
				$orders[$key]['statusName']='已取消';//订单退款成功
			}
            if($val['status']==ORDER_FANXIUZHONG) {
                $orders[$key]['statusName']='返修中';
            }
		}
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'orders' => !empty($orders) ? $orders : '',
				'count'  => $count['count'],
			),
		);

	    return $json->encode($return);
	}



	/**
     * 裁缝获取当前订单详情
	 *
     * @param string  $token 用户标识；int $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function storeOrderInfo($token, $order_id)
	{
		global $json;
        $db = db();
		$order_mod       = m('order');
		$order_goods_mod = m('ordergoods');
		$order_figure    = m('orderfigure');
		$member          = m('member');
		$userinfo        = getToken($token);

		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				),
			);
			return $json->encode($return);
		}
    	$store_id    = $userinfo['user_id'];

		//获得订单详情
		$order_info = $order_mod->get(array(
            'conditions' => 'user_id= '.$store_id.' and order_id = '.$order_id,
			'fields'     => 'kh_pay_time,user_id, order_id, order_sn, order_amount, cloth,add_time,kh_sex, kh_name, status, fabric, payment_id, kh_addr, kh_mobile, embs, craft, ship_name, ship_area,ship_addr,ship_mobile,erweima_url,erweimaUrl ',
		));
		if(!$order_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '无对应订单',
				),
			);
			return $json->encode($return);
		}
        if($order_info['status'] == 40 ){ //交易成功的订单生成二维码
            if(!$order_info['erweima_url'] || !$order_info['erweimaUrl']) {
                Qrcode('order', $order_id, LOCALHOST2);
                $mqrcode = getQrcodeImage('order', $order_id, 4);
                $erweimaUrl  = SITE_URL.'/phpqrcode/image/'.$mqrcode;
                $order_mod->edit($order_id, array("erweima_url" => $mqrcode,"erweimaUrl" => $erweimaUrl));
                $order_info['erweima_url'] = $mqrcode;
                $order_info['erweimaUrl']  = $erweimaUrl;
            }
        }
		//获取对应订单产品品类

		$sql = "SELECT * FROM ".DB_PREFIX."jpjz_gcategory where cate_id='{$order_info['cloth']}'";
        $gc_info = $db->getAll($sql);
        foreach($gc_info as $val){
            $gc = $val;
        }
        if($gc) {
            $order_info['cloth_name'] = $gc['cate_name'];
        }
 
		//获得对应支付方式
		$pay = m('payment');
		$pay_info = $pay->get("payment_id='".$order_info['payment_id']."'");
		$order_info['payment_name'] = !empty($pay_info['payment_name']) ? $pay_info['payment_name'] : '';

		//处理获得刺绣信息
		if($order_info['embs']) {
			$embs  = $this->_get_order_embs($order_info['cloth'], $order_info['embs']);
		}

		//处理获得工艺信息
		if($order_info['craft']) {
			$craft  = $this->_get_order_craft($order_info['cloth'], $order_info['craft']);
			$craft2 = $this->_get_order_craft2($order_info['cloth'], $order_info['craft']);
		}
		unset($order_info['embs']);
		unset($order_info['craft']);
		//获得订单对应的量体数据
		$figure_info = $order_figure->get(array(
            'conditions' => 'order_id = '.$order_id,
		));

		//处理获得体型和风格
		$_get_body_type = $this->_get_body_type($order_info['cloth']);
	
		$style   = array();//风格
		$feature = array();//特体
		//处理风格数据
		foreach($_get_body_type['style'] as $style_key => $style_val) {
			$nm = $style_val['info']['nm'];
			foreach($style_val['list'] as $lkey => $lval ) {
				if( $lkey == $figure_info[$nm]) {
					//$style[$nm] = $lval['name'];
					$style[] = array(
						'name' => $lval['clothName'],
						'val'  => $lval['name'],
					);
				}
			}
		}

		//处理特体数据
		foreach($_get_body_type['feature'] as $feature_key => $feature_val) {
			$nm = $feature_val['info']['nm'];
			foreach($feature_val['list'] as $fkey => $fval ) {
				if( $fkey == $figure_info[$nm]) {
					//$feature[$nm] = $lval['name'];
					$feature[] = array(
						'name' => $fval['cateName'],
						'val'  => $fval['name'],
					);
				}
			}
		}
					
		/*获得订单消费者信息
		$member_info = $member->get(array(
            'conditions' => 'user_id='.$order_info['user_id'],
			'fields'     => 'user_id, nickname, gender, phone_mob, phone_tel, height, weight, region_name',
		));*/
		//print_r($order_info);exit;
		$member_info = array(
			'user_id'     => $order_info['user_id'],
			'nickname'    => $order_info['kh_name'],
			'gender'      => $order_info['kh_sex']==2 ? '女' : '男',
			'phone_mob'   => $order_info['kh_mobile'],
			'phone_tel'   => $order_info['kh_mobile'],
			'region_name' => $order_info['kh_addr'],
			'height'      => $figure_info['height'],
			'weight'      => $figure_info['weight'],
		);

		/*
		//获得订单下面对应产品
		$order_goods_list = array();
		if ($order_info)
		{
			$order_goods_list = $order_goods_mod->find(array(
				'conditions'	=> "order_id=$order_id",
				'index_key'	 => '',
			));
		}*/

		// 交易状态
		if($order_info['status'] == ORDER_PENDING) {
			$order_info['statusName']='待付款';
		}
		if ($order_info['status']==STORE_ACCEPTED) {
			$order_info['status'] = ORDER_CHECKING;
		}
		if ($order_info['status']==ORDER_ACCEPTED) {
			$order_info['status'] = ORDER_CHECKING;
		}
		if($order_info['status'] == ORDER_CHECKING) {
			$order_info['statusName']='审核中';
		}
		if($order_info['status'] == ORDER_CHECKFAIL) {
			$order_info['statusName']='审核失败';
		}
		if($order_info['status'] == ORDER_CHECKED) {
			$order_info['status']=ORDER_PRODUCTION;
		}
		if($order_info['status'] == ORDER_PRODUCTION) {
			$order_info['statusName']='生产中';
		}
		if($order_info['status']==ORDER_SHIPPED) {
			$order_info['statusName']='平台已发货';
		}
		if($order_info['status']==STORE_RECEIVED) {
			$order_info['statusName']='已收货';
		}
		if($order_info['status']==STORE_SHIPPED) {
			$order_info['statusName']='已发货';
		}
		if($order_info['status']==ORDER_FINISHED) {
			$order_info['statusName']='交易成功';
		}
		if($order_info['status']==ORDER_CANCELED) {
			$order_info['statusName']='已作废';
		}
		if($order_info['status']==ORDER_FIGURE) {
			$order_info['statusName']='待量体';
		}
		if($order_info['status']==ORDER_TUIKUANZHONG) {
			$order_info['statusName']='订单退款中';
		}
		if($order_info['status']==ORDER_YINGTUIKUAN) {
			$order_info['statusName']='订单已退款';
		}
        /*
        $fabric = $order_info['fabric'];
        $part_mod = m('part');
        $part = $part_mod->get("part_name = '{$fabric}'");
        //print_r($part);die;
        if($part){
            $cus_mod = m('customs');
            $fabric = $part['link_cst'];
            //print_r($fabric);die;
            $links = $cus_mod->find(array(
                'conditions' => "cst_id IN ('{$fabric}')",
            ));
            foreach($links as $val){
                $cst_image = $val['cst_image'];
            }
        }*/
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'order_info'  => !empty($order_info) ? $order_info : '',
				'figure_info' => !empty($figure_info) ? $figure_info : '',
				'feature'     => !empty($feature) ? $feature : '',
				'style'       => !empty($style) ? $style : '',
				'member_info' => !empty($member_info) ? $member_info : '',
				//'ship_addr'   => !empty($ship_addr) ? $ship_addr : '',
               // 'cst_image'   => $cst_image,
                'body_type'   => !empty($body_type) ? $body_type : '',
				'embs'        => !empty($embs) ? $embs : '',
				'craft'       => !empty($craft) ? $craft : '',
				'craft2'      => !empty($craft2) ? $craft2 : '',
				//'goods_list'  => !empty($order_goods_list) ? $order_goods_list : '',
			),
		);
		return $json->encode($return);
	}

	/**
     * 删除订单：只有已废除或已取消订单才可以删除
	 *
     * @param string  $token 用户标识；int $order_id 订单id;
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function storeDelOrder($token, $order_id)
	{
		global $json;
		$userinfo = getToken($token);
		$store_id = $userinfo['user_id'];
		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '密码错误 请重新登陆',
				),
			);
			return $json->encode($return);
		}
		$return = array(
		    'statusCode' => 0,
		    'error' => array(
		        'errorCode' => 103,
		        'msg'       => '屏蔽',
		    ),
		);
		return $json->encode($return);
		$order_mod  = m('order');
		$order_info = $order_mod->get(array(
            'conditions' => 'order_id='.$order_id .' and store_id ='.$store_id,
		));

		if(!$order_info){
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '订单记录不存在',
				),
			);
			return $json->encode($return);
		}

		//当订单状态为已取消：0；情况才可删除订单
		if($order_info['status']==0) {
			$res = $order_mod->drop("order_id=".$order_info['order_id']);
			if($res) {
				$return = array(
					'statusCode' => 1,
					'result' => array(
						'success' => '当前订单删除成功',
					)
				);

			} else {
				$return = array(
					'statusCode' => 0,
					'error' => array(
						'errorCode' => 103,
						'msg'       => '删除失败',
					)
				);

			}
		} else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 104,
					'msg'       => '当前订单不可删除',
				),
			);
		}

		return $json->encode($return);
	}
	
	/**
     * 延期收获
	 *
     * @param string  $token 用户标识；int $order_id 订单id; int status 订单状态
	 *
     * @access protected
     * @author liuchao <280181131@qq.com>
     * @return void
     */ 
	public function delay_ship($token, $order_id)
	{
		global $json;
		$userinfo = getToken($token);
		$model_setting = &af('settings');
		$setting = $model_setting->getAll(); // 载入系统设置数据
		
		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '密码错误 请重新登陆',
				),
			);
			return $json->encode($return);
		}
		$user_id = $userinfo['user_id'];
		
		$order_mod  = m('order');
		$order_info = $order_mod->get("order_id = {$order_id} and user_id = {$user_id} ");
		
		if(!$order_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 101,
					'msg'       => '未发现此订单',
				),
			);
			return $json->encode($return);
		}
		
		$mOsd = &m('ordershipdelay');
		$has_delay  = $mOsd->findAll("order_id = ".$order_id);
		
		if(count($has_delay) >= 2) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 109,
					'msg'       => '您延期收货超过限定次数！',
				),
			);
			return $json->encode($return);
		}

		$data['user_id']    = $user_id;
		$data['order_id']   = $order_id;
		$data['delay_time'] = gmtime();
		$data['delay_days'] = $setting['delay_days'];//延期天数
		
		$add_res = $mOsd->add($data);
		
		if($add_res) {
			$return = array(
				'statusCode' => 1,
				'result' => array(
					'success' => '订单收货延期成功！',
				)
			);
			return $json->encode($return);
		} else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '订单收获延期失败，请重新操作！',
				),
			);
			return $json->encode($return);
		}
	}

	/**
     * 修改订单状态
	 *
     * @param string  $token 用户标识；int $order_id 订单id; int status 订单状态
	 *
     * @access protected
     * @author liuchao <280181131@qq.com>
     * @return void
     */ 
	public function editOrderStatus($token, $order_id, $status, $client = '')
	{
		global $json;
		$userinfo = getToken($token);
		$auth_status = 0;//是否对创业者进行认证弹窗
	
		if(!$userinfo) 
		{
			return $this->tresult();
		}
		$user_id = $userinfo['user_id'];
		
		$order_mod  = m('order');
		$order_info = $order_mod->get("order_id = {$order_id} and user_id = {$user_id} ");

		if(!$order_info) 
		{
		    return $this->eresult('未发现此订单');
		}
		
		$result = '';
		//取消--待付款状态下（11）  或者 确认收货--已发货（30）  两种状态下可以修改订单状态
		if($order_info['status']  == ORDER_PENDING || $order_info['status'] == ORDER_SHIPPED) 
		{
			//=============取消订单操作  START==========
			if($status == ORDER_CANCELED) 
			{
			    $res_msg = "订单取消成功！";
			    imports("orders.lib");
			    $orderLib = new Orders();//=====  调用订单取消的公共方法  =====
			    $res = $orderLib->cancelById($order_id,$order_info);
				$behavior = 'cancel'; 
			}
			//=============取消订单操作  END==========
			
			//=============交易完成  START==========
			if ($status == ORDER_FINISHED) 
			{
			    $res_msg = "订单确认后货成功!";
				$behavior = 'update';
				//用户交易完成，查看是否实名认证条件：当前用户是创业者身份、当前用户没有实名认证、弹三次（次数有app记录）
				$auth_mod  = m('auth');
				$auth_info = $auth_mod->get("user_id = $user_id ");

				if($userinfo['member_lv_id'] > 1 && $auth_info['status'] == 0 ) {
					$auth_status = 1;//需要进行弹窗
				}
	
				$transaction = $order_mod->beginTransaction();
				$res = $order_mod->submit($order_id);
				if (!$res['code'])
				{
				    $order_mod->rollback();
				    return $this->eresult($res['msg']);
				}
				$order_mod->commit($transaction);
				
			}
			//=============交易完成  END==========
			//订单取消加入到系统日志
			imports("orderLogs.lib");
			$oLogs = new OrderLogs();
			$oLogs->_record(array(
				'order_id' => $order_id,
				'op_id'    => $order_info['user_id'],
				'op_name'  => $order_info['user_name'],
				'behavior' => $behavior,
				'from'     => $order_info['status'],
				'to'       => $status,
				'remark'  => '用户主动在'.$client.' App端修改订单',
			));
			$result = $order_mod->edit('order_id='.$order_id , array('status' => $status));
			
		} 
		else 
		{
		    return $this->eresult('此订单状态不可修改！');
		}
			
		if($result) 
		{
			$return = array(
				'statusCode' => 1,
				'result' => array(
					'success' => $res_msg,
					'status'      => $status,
					'auth_status' => $auth_status,//是否对创业者进行认证弹窗；1：需要  0：不需要
				)
			);
			return $json->encode($return);
		} 
		else 
		{
		    return $this->eresult('订单状态修改失败');
		}

	}
	
	
	/**
     * 用户申请退款
	 *
     * @param string  $token 用户标识；int $order_id 订单id; string refund_reason 退款原因；string logistics_num 商品退还物流单号；
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */ 
	public function applyOrderRefund($token, $order_id, $refund_reason, $logistics_num)
	{
		global $json;
	    $user_info = getUserInfo($token);
		if(!$user_info) {
			return $this->tresult();
	    }
		$user_id = $user_info['user_id'];
		
		$order_mod  = m('order');
		$order_info = $order_mod->get("order_id = {$order_id} and user_id = {$user_id} ");
		
		if(!$order_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 101,
					'msg'       => '未发现此订单',
				),
			);
			return $json->encode($return);
		}
		
		if($order_info['status'] != ORDER_TUIKUANZHONG) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 101,
					'msg'       => '当前订单状态不能申请退款！',
				),
			);
			return $json->encode($return);
		}
		
		$orderrefund_mod  = & m('orderrefund');
		$orderrefund_info = $orderrefund_mod->get("order_id=$order_id");
		
		if($orderrefund_info['apply_time']) {//申请时间不为空
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 101,
					'msg'       => '当前订单已提交退款申请，请耐心等待审核！',
				),
			);
			return $json->encode($return);
		}
		
		$data_refund = array(
			'apply_time'    => gmtime(),
			'refund_reason' => $refund_reason,
			'logistics_num' => $logistics_num,
		);
		$result_edit = $orderrefund_mod->edit($orderrefund_info['id'], $data_refund);
		if($result_edit) {
			$return = array(
				'statusCode' => 1,
				'result' => array(
					'success' => '退款申请已提交，1~2 个工作日后将支付额退回您的余额账户',
				)
			);
			return $json->encode($return);
		} else {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '退款申请失败，请重新操作或联系管理员！',
				),
			);
			return $json->encode($return);
		}
		
	}

        /**
         * 上传作品
         *
         * @param string  $token 用户标识;string $des 简介; ;int $voting 是否参加评选0否1是;int $cloth 作品分类
         * string $imgs 作品图片; int $cover 是否为封面
         * @access protected
         * @author liuchao <280181131@qq.com>
         * @return void
         */
        public function uploadWork($token,$cloth,$des,$imgs,$cover,$voting)
        {
            global $json;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $store_mod = m('store');
            $exist     = $store_mod->get('store_id = '.$user_id);
            if(!$exist){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '只有创业者才能发布作品',
                    ),
                );
                return $json->encode($return);
            }
            $data['store_id']    = $user_id;
            $data['cloth']       = $cloth;
            $data['source_from'] = 'app';
            $data['description'] = $des;
            $data['voting']      = $voting;
            $data['add_time']    = time();
            $data['status']      = 0;
            $data['is_diy']      = 0;
            //创建作品
            $works_mod = m('works');
            $id = $works_mod->add($data);
            if($id){
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'  => '作品创建成功',
                        'work_info' => $data,
                    )
                );
                //把图片添加到作品对应的图片表
                $img_arr = explode(',',$imgs);
                $img_mod = m('workimgs');
                //选择封面图片
                if($cover){
                    if(!in_array($cover,$img_arr)){
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 104,
                                'msg'       => '请选择正确的图片为封面图片',
                            ),
                        );
                        return $json->encode($return);
                    }
                }else{
                    //voting为空，默认第一张图片为封面图片
                    $cover = $img_arr['0'];
                }
                foreach($img_arr as $val){
                    $imgdata['work_id']     = $id;
                    $imgdata['store_id']    = $user_id;
                    $imgdata['description'] = $des;
                    $imgdata['add_time']    = time();
                    $imgdata['img_name']    = $val;
                    $img_url = $val;
                    $imgdata['img_url']     = $img_url;
                    //查看默认图片
                    if($cover && $cover == $val){
                        $imgdata['iscover'] = 1;
                    }else{
                        $imgdata['iscover'] = 0;
                    }
                    $img_mod->add($imgdata);
                }
                return $json->encode($return);
            }else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '作品创建失败',
                    ),
                );
                return $json->encode($return);
            }
        }
        /**
         * 推荐款添加到作品
         *
         * @param string  $token 用户标识; ;int $voting 是否参加评选0否1是;int $cloth 作品分类0基本款1套装;int $id 基本款或套装id;
         * @access protected
         * @author liuchao <280181131@qq.com>
         * @return void
         */
        public function addWork($token,$cloth,$id,$voting){
            global $json;
            $user_info = getToken($token);
            if(!$user_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '未找到该用户  ',
                    ),
                );
                return $json->encode($return);
            }
            $user_id   = $user_info['user_id'];
            $store_mod = m('store');
            $exist     = $store_mod->get('store_id = '.$user_id);
            if(!$exist){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '只有创业者才能添加作品',
                    ),
                );
                return $json->encode($return);
            }
            $data['store_id'] = $user_id;
            $data['source_from'] = 'app';
            $data['voting']      = $voting;
            $data['add_time']    = time();
            if($cloth=='0003' || $cloth=='0004' || $cloth=='0005' || $cloth=='0006' || $cloth=='0007'){
                //添加基本款到作品
                $cus_mod = m('custom');
                $cus_info = $cus_mod->get($id);
                $data['cus_id']   = $id;
                $data['cloth'] = $cus_info['category'];
                $data['description'] = $cus_info['brief'];
                $data['is_diy']      = 2;
                $work_mod = m('works');
                $work_id =$work_mod->add($data);
                //添加图片
                if($work_id){
                    $imgdata['work_id'] = $work_id;
                    $imgdata['store_id']    = $user_id;
                    $imgdata['description'] = $cus_info['brief'];
                    $imgdata['add_time']    = time();
                    $imgdata['img_url']     = $cus_info['source_img'];
                    $imgdata['iscover']     = 1;
                    $img_mod = m('workimgs');
                    $img_mod->add($imgdata);
                    $data['id'] = $work_id;
                    $return = array(
                        'statusCode' => 1,
                        'result' => array(
                            'success'  => '推荐款添加到作品成功',
                            'work_info' => $data,
                        )
                    );
                }else{
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode'  => 103,
                            'msg' => '推荐款添加到作品失败',
                        )
                    );
                    return $json->encode($return);
                }
            }else{
                //添加套装到作品
                $dis_mod = m('suitlist');
                $dis_info = $dis_mod->get($id);
                $data['cloth']    = 'pcs';
                $data['cus_id']   = $id;
                $data['is_suit']  = 1;
                $data['is_diy']   = 2;
                $data['description'] = $dis_info['jianjie'];
                $work_mod = m('works');
                $work_id =$work_mod->add($data);
                //添加图片
                if($work_id){
                    $imgdata['work_id'] = $work_id;
                    $imgdata['store_id']    = $user_id;
                    $imgdata['description'] = $dis_info['jianjie'];
                    $imgdata['add_time']    = time();
                    $imgdata['img_url']     = $dis_info['image'];
                    $imgdata['iscover']     = 1;
                    $img_mod = m('workimgs');
                    $img_mod->add($imgdata);
                    $data['id'] = $work_id;
                    $return = array(
                        'statusCode' => 1,
                        'result' => array(
                            'success'  => '套装添加到作品成功',
                            'work_info' => $data,
                        )
                    );
                }else{
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode'  => 103,
                            'msg' => '套装添加到作品失败',
                        )
                    );
                    return $json->encode($return);
                }
            }
            return $json->encode($return);
        }
        /*
        *更改作品状态
        * @param string $token ;string $work_id 作品id
        * @param string $type
        * @author liuchao <280181131@qq.com>
        * @2015-3-26
        */
        public function editWork( $work_id,$status){
            global $json;
            $work_mod = m('works');
            $arr = array(
                'voting_status' => 2,
            );
            if($work_mod->edit($work_id,$arr)){
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'  => '修改成功',
                    )
                );
            }else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode'  => 103,
                        'msg' => '套装添加到作品失败',
                    )
                );
            }
            return $json->encode($return);
        }
        /*
        *删除作品
        * @param string $token ;string $work_id 作品id
        * @param string $type
        * @author liuchao <280181131@qq.com>
        * @2015-3-26
        */
        public function delWork($token, $work_id){
            global $json;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $ids       = explode(',',$work_id);
            $works_mod = m('works');
            $imgs_mod  = m('workimgs');
            $sql  = "SELECT i.img_url FROM ".DB_PREFIX."work_imgs i LEFT JOIN ".DB_PREFIX."works w ON i.work_id=w.id WHERE w.store_id = '{$user_id}' AND w.id IN ({$work_id})";
           // 获得图片路径
            $imgs = $imgs_mod->getAll($sql);
            //print_r($imgs);die;
            foreach($imgs  as $val){
                $img_arr = ROOT_PATH.$val['img_url'];
                unlink($img_arr);
            }
            //删除works表 和work_imgs表数据
            foreach($ids as $val){
                $works_mod->drop("store_id = '{$user_id}' AND id = '{$val}'");
                $imgs_mod->drop("work_id = '{$val}'");
            }
            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '作品删除成功',
                ),
            );
            return $json->encode($return);
        }
        /*
        *删除分类下所有作品
        * @param string $token ;ing $cloth 作品分类
        * @param string $type
        * @author liuchao <280181131@qq.com>
        * @2015-3-26
        */
        public function delAllWork($token, $cloth){
            global $json;
            $db = &db();
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];

            //条件
            $conditions = "w.store_id = '{$user_id}' AND w.cloth = '{$cloth}'";
            //获得图片路径
            $sql1 = "SELECT i.img_url FROM ".DB_PREFIX."work_imgs i LEFT JOIN ".DB_PREFIX."works w ON w.id = i.work_id WHERE ".$conditions."";
            $img_mod = m('workimgs');
            $imgs = $img_mod->getAll($sql1);
            //删除图片
            foreach($imgs as $key=>$val){
                $img_url = ROOT_PATH.$val['img_url'];
                unlink($img_url);
            }
            //删除works表 和work_imgs表数据
            $sql = "DELETE w.*,i.* FROM ".DB_PREFIX."works w INNER JOIN ".DB_PREFIX."work_imgs i ON w.id = i.work_id WHERE ".$conditions."";
            $delete = $db->query($sql);
            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '作品删除成功',
                ),
            );
            return $json->encode($return);
        }
        /*
         *上传作品图片
         * @param string $token
	     * @param string $type
         * @author liuchao <280181131@qq.com>
         * @2015-3-26
         */
        public function uploadWorkImg($token, $work_img)
        {
            global $json;
            if(!$work_img) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '图片上传失败',
                    )
                );
                return $json->encode($return);
            }
            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '图片上传成功',
                    'name'    => $work_img,
                ),
            );
            return $json->encode($return);
        }

        /**
         * 作品列表
         *
         * @param string  $token 用户标识；int $cloth 作品分类
         *
         * @access protected
         * @author liu_chao <280181131@qq.com>
         * @return void
         */
        public  function workList($token,$cloth,$pageSize,$pageIndex){
            global $json;
            $db = db();
            $user_info = getToken($token);
            $user_id = $user_info['user_id'];
            $work_mod = m('work');
            //条件
            if($cloth=='0003' || $cloth=='0004' || $cloth=='0005' || $cloth=='0006' || $cloth=='0007'){
                $conditions = 'AND w.store_id = '.$user_id.' AND w.cloth = '.$cloth;
            }else{
                $conditions = "AND w.store_id = '$user_id' AND (w.cloth LIKE '%$cloth%' OR w.cloth=1)";
            }
            //print_r($conditions);die;
            //分页
            $limit = $pageSize*($pageIndex-1).','.$pageSize;
            //拼sql语句
            $sql = "SELECT w.id, w.work_name, w.cloth, w.description,w.comment_num, i.img_name,i.img_url from ".DB_PREFIX."works w LEFT JOIN ".DB_PREFIX."work_imgs i ON w.id=i.work_id WHERE w.status=1 AND i.iscover=1 ".$conditions."  order by w.add_time DESC limit ". $limit ."";
           // print_r($sql);die;
            $works = $db->getAll($sql);
            if ($works) 
            {
                foreach ($works as $key => $value) 
                {
                    $works[$key]['tiao_url'] = "http://m.cy.mfd.cn/work-".$value['id'].".html";;
                }
            }
            
            
			//处理图片地址
			if($works) {
				foreach($works as  $key=>$val) {
                    if(!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$works[$key]['img_url'])){
                        $works[$key]['img_url'] = LOCALHOST1.$val['img_url'];
                    }
                    /*
                    if(!preg_match('/.jpg+/',$works[$key]['img_url'])){
                        $works[$key]['img_url'] .='.jpg';
                    }
                    */
				}
               // print_r($works);die;
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'works' => $works,
                    )
                );
			}else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未找到作品',
                    ),
                );
            }
            return $json->encode($return);
        }

        /**
         * 作品详情
         *
         * @param string  $token 用户标识；int work_id 作品id
         *
         * @access protected
         * @author liu_chao <280181131@qq.com>
         * @return void
         */
        public  function workInfo($token,$work_id,$pageSize,$pageIndex){
            global $json;
            $user_info = getToken($token);
            $user_id = $user_info['user_id'];
            $works_mod = m('works');
            //获得作品数据
            $work = $works_mod->get("store_id ='{$user_id}' AND id = '{$work_id}'");
            $imgs_mod = m('workimgs');
            $cloth = $work['cloth'];
            //生存api_auth_code
            do{
                $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{42}$/u", $api_token));

            $work['diy_url'] = 'http://m.cy.mfd.cn/custom-diy-'.$cloth.'-'.$api_token.'-'.$work_id.'.html';
            $imgs = $imgs_mod->findAll(array(
                'conditions' => "work_id = '{$work_id}'",
                'fields'     => 'img_name,img_url',
            ));
            $k = 0;
            foreach($imgs as $key=>$val){
                /*
                if(!preg_match('/.jpg+/',$imgs[$key]['img_url'])){
                    $imgs[$key]['img_url'] .= '.jpg';
                }
                */
                if(!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$imgs[$key]['img_url'])){
                    $val['img_url'] = "http://cy.dev.mfd.cn".$imgs[$key]['img_url'];
                }
                $img[] = $val;
            }
            //print_r($img);die;
            if(!$work){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '未找到作品',
                    ),
                );
            }else{
                //获取作品展示价格5.2
                $work_custom = m("workCustom");
                $custom = $work_custom->find("work_id=$work_id");
                foreach($custom as $val){
                    $work['price'] = $val['price'];
                }
                print_exit($work);
            //获取作品评论
                $com_mod = m('workcomment');
                //分页
                $limit = $pageSize*($pageIndex-1).','.$pageSize;
                //条件
                $conditions = "store_id ='{$user_id}' AND work_id = '{$work_id}' AND status=1";
                //拼sql语句
                $sql = "SELECT c.member_id,c.addtime,c.content,c.approve,m.nickname,m.avatar FROM ".DB_PREFIX."work_comment c LEFT JOIN ".DB_PREFIX."member m on c.member_id=m.user_id WHERE ".$conditions." order by c.addtime DESC limit ". $limit ."";
                $comments = $com_mod->getAll($sql);
				//循环处理用户头像
				if($comments) {
					foreach($comments as $key=>$val) {
						$comments[$key]['avatar'] = LOCALHOST1.$val['avatar'];
					}
				}

                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'work_info' => $work,
                        'imgs'      => $img,
                        'comments'  => $comments,
                    ),
                );
            }
            return $json->encode($return);
        }

        /**
         * 作品添加购物车
         * @version 1.0.0
         * @author liuchao <280181131@qq.com>
         * @2015-5-22
         */
        function addWorkCart($token,$work_id,$size)
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
            $dis_arr[$work_id] = $size;
            //print_exit($dis_arr);
            $cart_mod = m('cart');
            $fab_mod  = m('fabric');
            $cf_mod   = m('workFab');
            $work_mod = m('works');
            $wi_mod   = m('workimgs');
            $data['user_id'] = $user_id;
            $work = $work_mod->find("id=$work_id");
            foreach($work as $key=>$val){
                if($val['cloth'] == 'pcs'){
                    $data['type'] = 'tz';
                }else{
                    $data['type'] = 'jb';
                }
            }
            $data['type'] = 'work';
            $data['source_from'] = 'app';

            $customs_mod = m('workCustom');
            if ($dis_arr)
            {
                foreach ($dis_arr as $key => $value)
                {
                    $info = $customs_mod->get("work_id=$key");
                    $work_info = $wi_mod->get("work_id=$key AND iscover=1");

                    if (!$info)
                    {
                        continue;
                    }
                    $data['image']    = $work_info['img_url'];
                    $data['cloth']    = $info['category'];
                    $data['goods_id'] = $info['id'];
                    $data['price']    = $info['base_price'];
                    $data['fabric']   = $info['fabric_num'];
                    $data['size']     = $value;
                    $data['ident']    = $this->gen_ident();
                    //获取面料编号
                    $cf = $cf_mod->find("custom_id=".$info['id']." AND is_default=1");
                    
                    foreach($cf as $val){
                        $fab_info = $fab_mod->find("id=".$val['item_id']);
                        foreach($fab_info as $vv){
                            $fab_num = $vv['CODE'];
                        }
                    }
                    $data['fabric'] = $fab_num;

                    $cart_mod->add($data);
                }
            }
            else
            {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '参数为空',
                    ),
                );
                return $return;
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
         * 评选列表
         * @param string  $token 用户标识;int $status 评选状态
         * @access protected
         * @author liu_chao <280181131@qq.com>
         * @return void
         */
        public function votingList($token,$status,$pageSize,$pageIndex){
            //print_r('123');die;
            global $json;
            $user_info = getToken($token);
            if(!$user_info){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = 'token标识错误';
                return $json->encode($return);
            }
			$img_url = LOCALHOST1;
            $user_id   = $user_info['user_id'];
            $works_mod = m('works');
            $conditions = " AND w.store_id = '{$user_id}' AND w.voting = 1 AND w.status = '{$status}'";
            $limit = $pageSize*($pageIndex-1).','.$pageSize;

            /*$list = $works_mod->findAll(array(
                'conditions' => $conditions,
                'fields'     => 'this.*',
                'count'      => true,
                'order'      => 'add_time DESC',
                'limit'      => $limit,
            ));*/
            $sql = "SELECT w.id, w.cloth, w.description, w.voting_status,w.fail_reason,w.comment_num,w.like_num,w.views,w.add_time,i.img_url from ".DB_PREFIX."works w LEFT JOIN ".DB_PREFIX."work_imgs i ON w.id=i.work_id WHERE w.status=1 AND i.iscover=1 ".$conditions."  order by w.add_time DESC limit ". $limit ."";
            $list = $works_mod->getAll($sql);
            $lists = array();
            foreach($list as $key=>$val){
                if(!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$list[$key]['img_url'])){
                    $list[$key]['img_url'] = LOCALHOST1.$val['img_url'];
                }
                if($list[$key]['cloth'] ==3){
                    $list[$key]['cloth_name'] = '西服';
                }
                if($list[$key]['cloth'] ==2000){
                    $list[$key]['cloth_name'] = '西裤';
                }
                if($list[$key]['cloth'] ==3000){
                    $list[$key]['cloth_name'] = '衬衫';
                }
                if($list[$key]['cloth'] ==4000){
                    $list[$key]['cloth_name'] = '马甲';
                }
                if($list[$key]['cloth'] ==6000){
                    $list[$key]['cloth_name'] = '大衣';
                }
                if(preg_match('/pcs+/',$list[$key]['cloth'])){
                    $list[$key]['cloth_name'] = '套装';
                }
                $lists[] = $val;
            }
           // print_r($lists);die;
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'lists' => $list,
                )
            );
            return $json->encode($return);
        }

	/**
     * 获取处理刺绣信息
	 *
     * @param string  $token 用户标识；int $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function _get_order_embs($clothID = 0 ,$embs = '')
	{
        $arr = unserialize($embs);
        foreach ((array)$arr as $val){
            if(intval($val)){
                $dbin[] = $val;
            }else{
                $con['e_tname'] = 'emb_con';
                $con['e_name'] = $val;
            }
        }
        $mEmb   = &m('mtmemb');
        $return = $mEmb->find(array(
			'conditions' => db_create_in($dbin,'e_id'),
			'fields'     => 'e_name, e_tname',
			'index_key'	 => '',
        ));
		$return[-1] = $con;
		$mEmb_arr = array();
		if($return) {
			foreach($return as $val) {
				$mEmb_arr[$val['e_tname']] = $val['e_name'];
				/*
				if($val['e_tname']=='emb_site') {
					//$mEmb_arr[$val['e_tname']] = '位置：'.$val['e_name'];
					//$mEmb_arr[$val['e_tname']] = '位置：'.$val['e_name'];
				}
				if($val['e_tname']=='emb_color') {
					$mEmb_arr[$val['e_tname']] = '颜色：'.$val['e_name'];
				}
				if($val['e_tname']=='emb_font') {
					$mEmb_arr[$val['e_tname']] = '字体：'.$val['e_name'];
				}
				if($val['e_tname']=='emb_con') {
					$mEmb_arr[$val['e_tname']] = '内容：'.$val['e_name'];
				}*/
			}
		}

        return $mEmb_arr;
    }
	/**
	 * 根据品类id获得对应体形及风格
	 *
	 * @param string store_id 裁缝ID; string $token 裁缝token
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	public function  _get_body_type($clothId){
		$_mod_mtm_bt = &m("mtmbodytype");
		$body_type_tm = $_mod_mtm_bt->find("clothId = '{$clothId}'");
		foreach ($body_type_tm as $row){
				$body_type['style'][$row['clothId']]['info']['name'] = $row['clothName'];
				$body_type['style'][$row['clothId']]['info']['id']   = $row['cateID'];
				$body_type['style'][$row['clothId']]['info']['nm']   = 'body_type_'.$row['clothId'];
				$body_type['style'][$row['clothId']]['list'][$row['id']] = $row;

		}
		$body_type_ts = $_mod_mtm_bt->find("clothId = '0'");
		foreach ($body_type_ts as $row){

			$body_type['feature'][$row['cateID']]['info']['name'] = $row['cateName'];
			$body_type['feature'][$row['cateID']]['info']['id']   = $row['cateID'];
			$body_type['feature'][$row['cateID']]['info']['nm']   = 'body_type_'.$row['cateID'];
			$body_type['feature'][$row['cateID']]['list'][$row['id']] = $row;

		}
		return $body_type;
	}

	/**
     * 获取处理工艺信息
	 *
     * @param string  $token 用户标识；int $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function _get_order_craft($clothID = 0 ,$crafts = '')
	{
        $arr = unserialize($crafts);
        foreach ($arr as $key=>$val){
            $pt[$key] = $key;
        }
        $mCraft = &m('mtmcraft');
        $mCraft_parent = &m('mtmcraftparent');
        $parent = $mCraft_parent->find(array(
            'conditions' => db_create_in($pt,'id'),
			//'index_key'	 => '',
        ));
        $craft = $mCraft->find(array(
			'conditions' => db_create_in($arr,'code'),
			'index_key'	 => '',
        ));
        foreach ($parent as $row){
            $parent[$row['id']] = $row;
            unset($row);
        }
        foreach ($craft as &$row){
            $row['pname'] = $parent[$row['parentId']]['name'];
        }
		//格式化格式
		$craft_arr = array();
		$i = 1;
		if($craft) {
			foreach($craft as $c) {
				//$craft_arr['cr'.$i] = $c['pname'].'：'.$c['name'];
				$craft_arr['cr'.$i] = $c['name'];
				$i++;
			}
		}

        return $craft_arr;
    }

	/**
     * 获取处理工艺信息
	 *
     * @param string  $token 用户标识；int $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function _get_order_craft2($clothID = 0 ,$crafts = '')
	{
        $arr = unserialize($crafts);
        foreach ($arr as $key=>$val){
            $pt[$key] = $key;
        }
        $mCraft = &m('mtmcraft');
        $mCraft_parent = &m('mtmcraftparent');
        $parent = $mCraft_parent->find(array(
            'conditions' => db_create_in($pt,'id'),
			//'index_key'	 => '',
        ));
        $craft = $mCraft->find(array(
			'conditions' => db_create_in($arr,'code'),
			'index_key'	 => '',
        ));
        foreach ($parent as $row){
            $parent[$row['id']] = $row;
            unset($row);
        }
        foreach ($craft as &$row){
            $row['pname'] = $parent[$row['parentId']]['name'];
        }
		//格式化格式
		$craft_arr = array();
		$i = 1;
		if($craft) {
			foreach($craft as $c) {
				$craft_arr['cr'.$i] = $c['pname'].'：'.$c['name'];
				//$craft_arr['cr'.$i] = $c['name'];
				$i++;
			}
		}

        return $craft_arr;
    }

	/**
     * 获取分类数据
	 *
     * @param string  $token 用户标识；int $order_id 订单id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function _get_gcategory($conditions='')
	{
		$gcategoryMod = m('gcategory');
		$conditions = empty($conditions) ? "1=1" : $conditions;
		$p_c_data = $gcategoryMod->find(array('conditions'=>$conditions));
		return $p_c_data;
	}

	/**
	 * 生成嵌套格式的树形数组
	 * @param arrary 	$gcategories	数据源
	 * @param int 		$root			父节点
	 * @return array|false				array(..."children"=>array(..."children"=>array(...)))
	 * @author
	 * <code>
	 * $gcategories = $this->_get_gcategory();
	 * $this->deep_tree($gcategories,3);
	 * </code>
	 */
	function deep_tree($gcategories,$root=0){
		if(!$gcategories){
			return FALSE;
		}
		$pk="cate_id";
		$parentKey="parent_id";
		$childrenKey="children";
		$tree=array();//最终数组
		$refer=array();//存储主键与数组单元的引用关系
		//遍历
		foreach($gcategories as $k=>$v){
			if(!isset($v[$pk]) || !isset($v[$parentKey]) || isset($v[$childrenKey])){
				unset($gcategories[$k]);
				continue;
			}
			$refer[$v[$pk]]=&$gcategories[$k];//为每个数组成员建立引用关系
		}
		//遍历子节点
		foreach($gcategories as $k=>$v){
			if($v[$parentKey]==$root){//根分类直接添加引用到tree中
				$tree[$gcategories[$k]['cate_id']]=&$gcategories[$k];
			}else{
				if(isset($refer[$v[$parentKey]])){
					$parent=&$refer[$v[$parentKey]];//获取父分类的引用
					$parent[$childrenKey][$gcategories[$k]['cate_id']]=&$gcategories[$k];//在父分类的children中再添加一个引用成员
				}
			}
		}
		return $tree;
	}

	/**
    * 身份认证
    * @version 1.0.0
    * @author liuchao <280181131@qq.com>
    * @2015-3-12
    *
    public function auth($token)
    {
        global $json;
        /* 获取用户信息 *
        $user_info = getToken($token);

        //===== 获取是个人认证还是企业认证 =====
        $user_id = $user_info['user_id'];
        $business_auth_mod = m('businessauth');
        $person_auth_mod = m('personauth');
        $business_info = $business_auth_mod->get("user_id=" . $user_id);
        $person_info = $person_auth_mod->get("user_id=" . $user_id);

        if ($person_info)// ====显示个人认证情况======
        {
            if ($person_info['status'] == 0) {
                $status = '审核中';

                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $person_info['status'];
                /*
                 $return['result']['card_name']     = $person_info['card_name'];
                 $return['result']['card']          = $person_info['card'];
                 $return['result']['card_due_time'] = $person_info['card_due_time'];
                *
            } elseif ($person_info['status'] == 1) {
                $status = '审核成功';
                if ($person_info['is_long'] == 1) {
                    $person_info['card_due_time'] = '长期';
                }
                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $person_info['status'];
                $return['result']['card_name'] = $person_info['card_name'];
                $return['result']['card'] = $person_info['card'];
                $return['result']['card_due_time'] = $person_info['card_due_time'];
            } elseif ($person_info['status'] == 2) {
                $status = '审核失败';
                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $person_info['status'];
                $return['result']['fail_reason'] = $person_info['fail_reason'];
            }

            return $json->encode($return);
        } elseif ($business_info)// ====显示企业认证情况======
        {

            if ($business_info['status'] == 0) {

                $status = '审核中';
                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $business_info['status'];
                /*
                if($business_info['is_long'] == 1)
                {
                    $bussiness_info['card_due_time'] = '长期';
                }
                $return['result']['firm_name'] = $business_info['firm_name'];
                $return['result']['licence_num'] = $business_info['licence_num'];
                $return['result']['business_life'] = $business_info['business_life'];
                $return['result']['org_code']   = $business_info['org_code'];
               *
            } elseif ($business_info['status'] == 1) {
                $status = '审核成功';
                if ($business_info['is_long'] == 1) {
                    $bussiness_info['card_due_time'] = '长期';
                }
                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $business_info['status'];
                $return['result']['firm_name'] = $business_info['firm_name'];
                $return['result']['licence_num'] = $business_info['licence_num'];
                $return['result']['business_life'] = $business_info['business_life'];
                $return['result']['org_code'] = $business_info['org_code'];
            } elseif ($business_info['status'] == 2) {
                $status = '审核失败';
                $return['statusCode'] = 1;
                $return['result']['status'] = $status;
                $return['result']['statusCode'] = $business_info['status'];
                $return['result']['fail_reason'] = $business_info['fail_reason'];
            }

        } elseif (!$business_info && !$person_info) { // ====返回没有认证======
            $return['statusCode'] = 1;
            $return['result']['statusCode'] = 3;
            $return['result']['success'] = '您还没有进行身份认证';
        }
        return $json->encode($return);
    }
     */
    /**
     * 获得银行
     * @version 1.0.0
     * @author liuchao <280181131@qq.com>
     * @2015-3-20
     */
    public  function bank(){
        global $json;
        $bank_arr = include_once ROOT_PATH.'/soaapi/v1/includes/libraries/arrayfile/bank.arrayfile.php';
        foreach($bank_arr as $k=>$v){
            $banks[] =array(
                'bank_id' => $k,
                'bank'    => $v,
            );
        }
        $return['statusCode'] 	 	  = 1;
        $return['result']['banks'] = $banks;
        return $json->encode($return);
    }


    /**
    * 绑定银行卡
    * @version 1.0.0
    * @author liuchao <280181131@qq.com>
    * @2015-3-24
    */
    public function bingbank($token,$bank_address,$bank_id,$card,$code,$bank)
    {

        global $json;
    	$user_info   = getToken($token);
    	$user_id    = $user_info['user_id'];
        $bank_mod   = m('member_bank');
        $sms_mod    = m('SmsRegTmp');
        //验证短信
        if ($code) {
            $res = $sms_mod->get(array(
                'conditions' => "code='{$code}' AND phone='{$user_info['phone_mob']}'  AND type = 'bingBank'" ,
                'order' => "id  DESC",
                'fields' => '*',
            ));
            //print_r($res);exit;
            if ($res['phone']) {
                if (time() > $res['fail_time']) {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 102,
                            'msg' => '验证码已过期',
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg' => '验证码不正确',
                    )
                );
                return $json->encode($return);
            }

        } else {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg' => '验证码不能为空',
                )
            );
            return $json->encode($return);
        }
        $bank_info = $bank_mod->get('user_id = '.$user_id);
        if($bank_info )
        {
            $return['statusCode'] 	 	     = 0;
            $return['error']['errorCode']    = 103;
            $return['error']['msg']          = '您已绑定银行卡，请勿重复提交';
            return $json->encode($return);
        }
        else//添加数据到数据库
        {
            $data['user_id']       = $user_id;
            $data['bank_card']     = $card;//卡号
            $data['bank_id']       = $bank_id;//银行卡id
            $data['bank']          = $bank;
            $data['bank_address']  = $bank_address;//开户行地址
            $data['add_time']      = time();
            if($bank_mod->add($data) >= 0 )
            {   //在member表中的默认银行卡添加资料
                $member_mod = m('member');
                $arr = "df_bankcard = '{$card}'";
                $member_mod->edit($user_id,$arr);
                $return['statusCode']              = 1;
                $return['result']['bank_info'] = $data;
                $return['result']['success']   = '成功提交资料';
                return $json->encode($return);
            }else{
                $return['statusCode'] 	 	     = 0;
                $return['error']['errorCode']    = 105;
                $return['error']['msg']          = '提交资料失败';
                return $json->encode($return);
            }

        }
  	}
        /**
         * 确认收到金额
         * @version 1.0.0
         * @author liuchao <280181131@qq.com>
         * @2015-3-23
         */
        public  function get_money($token,$get_money){
            global $json;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $auth_mod  = m('auth');
            $exist = $auth_mod->get("user_id = '{$user_id}' AND status = 1 AND get_money = '{$get_money}'");
            if(!$exist){
                $return['statusCode'] 	 	     = 0;
                $return['error']['errorCode']    = 102;
                $return['error']['msg']          = '认证失败';
            }else{//认证成功status改为4
                $arr=array('status'=>4);
                $auth_mod->edit($exist['id'],$arr);
                $return['statusCode']              = 1;
                $return['result']['success']   = '认证成功';
            }
            return $json->encode($return);
        }


        /**
         * 实名认证
         * @version 1.0.0
         * @author liuchao <280181131@qq.com>
         * @2015-4-27
         */
        public function real_auth($token,$real_name,$bank_id,$bank,$bank_address,$bank_card,$code,$card_face_img,$card_back_img)
        {
            global $json;
            $user_info = getToken($token);
            if(!$user_info){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = '密码错误 请重新登陆';
                return $json->encode($return);
            }
            $user_id  = $user_info['user_id'];
            $auth_mod   = m('auth');
            $store_mod  = m('store');
            $phone = $user_info['phone_mob'];
            $exist = $store_mod->get('store_id = '.$user_id);
            if(!$exist){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '102';
                $return['error']['msg'] 	  = '只有创业者可以申请认证';
                return $json->encode($return);
            }

            if ($auth_mod->get("user_id=$user_id AND status=0"))
            {
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您已提交审核!请勿重复提交';
                return $json->encode($return);
            }
            $auth = $auth_mod->get("user_id=$user_id AND status=2");
            if ($auth)
            {
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您的认证信息审核失败';
                $return['error']['reason']    = $auth['fail_reason'];
                return $json->encode($return);
            }
            $sms_mod    = m('SmsRegTmp');
            //验证短信
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$phone}'  AND type = 'auth'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                if ($res['phone']) {
                    if (time() > $res['fail_time']) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 104,
                                'msg' => '验证码已过期',
                            )
                        );
                        return $json->encode($return);
                    }

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 104,
                            'msg' => '验证码不正确',
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg' => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }
            $data['user_id']         = $user_id;
            $data['bank']            = $bank;
            $data['bankcard_address']    = $bank_address;
            $data['bankcard']        = $bank_card;
            $data['realname']        = $real_name;
            $data['card_face_img']   = $card_face_img;
            $data['card_back_img']   = $card_back_img;
            $data['last_update_time']= time();
            $auth_id = $auth_mod->add($data);
            if($auth_id)
            {
                //发后后台消息
                import("notice.lib");
                $msg = new Notice();
                $msg->send(array(
                    "content" => "创业者-实名认证(ID-".$auth_id."),<a href=\"http://www.dev.mfd.cn/admin/index.php?app=authperson&act=index\">查看详情</a>",
                    'node'    => 'real_auth',
                ));

                //在member表中的默认银行卡添加资料
                $member_mod = m('member');
                $arr = "df_bankcard = '{$bank_card}'";
                $member_mod->edit($user_id,$arr);
                //把银行卡信息添加到member_bank表中
                $bank_arr['user_id']         = $user_id;
                $bank_arr['bank']            = $bank;
                $bank_arr['bank_id']         = $bank_id;
                $bank_arr['bank_address']    = $bank_address;
                $bank_arr['bank_card']       = $bank_card;
                $bank_arr['card_name']       = $real_name;
                $bank_arr['add_time']        = time();
                //print_r($bank_arr);die;
                $bank_mod = m('member_bank');
                $bank_mod->add($bank_arr);
                $return['statusCode']        = 1;
                $return['result']['success'] = '成功提交认证资料';
            }
            else
            {
                $return['statusCode'] 	 	     = 0;
                $return['error']['errorCode']    = 105;
                $return['error']['msg']          = '提交认证资料失败';
            }
            return $json->encode($return);

        }

        /**
         * 放弃认证
         * @version 1.0.0
         * @author liuchao <280181131@qq.com>
         * @2015-3-16
         */
        public function giveUpAuth($token){
            global $json;
            $user_info = getToken($token);
            $store_mod = m('store');
            $user_id   = $user_info['user_id'];
            $exist     = $store_mod->get('store_id = '.$user_id);
            if(!$exist){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 102;
                $return['error']['msg']       = '只有创业者才能放弃认证';
                return $json->encode($return);
            }
            $mod = m('auth');
            $m_info = $mod->get('user_id = '.$user_id);
            if(!$m_info){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 103;
                $return['error']['msg']       = '没有认证信息';
                return $json->encode($return);
            }
            $imgs = array($m_info['card_face_img'], $m_info['card_back_img']);

            if($mod->drop('user_id = '.$user_id)){
                //删除member_bank表中信息
                $bank_mod = m('member_bank');
                $bank_mod->drop('user_id = '.$user_id);
                /******删除上传的认证图片******/
                foreach($imgs as $img){
                    $real_dir = $this->real_dir.$img;
                    @unlink($real_dir);
                }

                $return['status']            = 1;
                $return['result']['success'] = '您已成功放弃此次认证';
                return $json->encode($return);
            }else{

                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '放弃认证失败';
                return $json->encode($return);
            }

        }
        /*
         *上传身份证正面
         * @param string $token
	     * @param string $type
         * @author liuchao <280181131@qq.com>
         * @2015-3-23
         */
        public function uploadFaceImg($token,$card_face)
        {
            global $json;
            if(!$card_face) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '图片上传失败',
                    )
                );
                return $json->encode($return);
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '图片上传成功',
                    'name'    => $card_face,
                ),
            );
            return $json->encode($return);
        }
        /*
         *上传身份证背面
         * @param string $token
	     * @param string $type
         * @author liuchao <280181131@qq.com>
         * @2015-3-23
         */
        public function uploadbackImg($token, $card_back)
        {
            global $json;
            if(!$card_back) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '图片上传失败',
                    )
                );
                return $json->encode($return);
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '图片上传成功',
                    'name'    => $card_back,
                ),
            );
            return $json->encode($return);
        }
        /*
         *上传认证图片
         * @param string $token
	     * @param string $type
         * @author liuchao <280181131@qq.com>
         * @2015-3-13
         *
        public function uploadAuthImg($token,$type){
            global $json;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $store_mod = m('store');
            $exist   = $store_mod->get("store_id = $user_id");
            if(!$exist){
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 102;
                $return['error']['msg']       = '只有创业者才能上传认证图片';
                return $json->encode($return);
            }

            $real_dir = ROOT_PATH."/upload/auth/";
            $web_dir = SITE_URL."/upload/auth/";
            include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
            $imageTool = new ImageTool();
            $imageTool->_upload_dir = $real_dir;
            $dir = $imageTool->uploadImage($type);
            if(!$dir){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 103;
                $return['error']['msg']       = '认证图片上传失败';
                return $json->encode($return);
            }

            $imgUrl = $web_dir.$dir;
            $return['statusCode'] 	 	  = 1;
            $return['result']['success']  = '文件上传成功';
            $return['result']['name']     = $dir;
            $return['result']['src']      = $imgUrl;

            return $json->encode($return);

        }*/

        /*
        *删除认证图片
        * @param string $token
        * @param string $type
        * @author liuchao <280181131@qq.com>
        * @2015-3-16
        */
        public  function delAuthImg($token,$field,$img){
            global $json;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $real_dir = $this->real_dir.$img;
            /********如果数据库有内容修改数据库**********/
            $mod = m('auth');
            $m_info = $mod->get('user_id = '.$user_id);
            if($m_info)
            {
                $auth_id = $m_info['id'];
                if ($mod->edit($auth_id, array($field => '')) !== false) {
                    $return['result']['success1'] = '数据库修改成功';
                }else{
                    $return['statusCode'] = 0;
                    $return['error']['errorCode'] = 104;
                    $return['error']['msg'] = '数据库修改失败';
                    return $json->encode($return);
                }
            }

            /********删除图片**********/
            if(@unlink($real_dir)){
                $return['statusCode']         = 1;
                $return['result']['success']  = '认证图片删除成功';
                return $json->encode($return);
            }else{
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 105;
                $return['error']['msg']       = '认证图片删除失败';
                return $json->encode($return);
            }
        }







        

	}

