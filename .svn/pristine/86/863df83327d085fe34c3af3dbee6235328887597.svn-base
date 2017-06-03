 <?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class = 'Club';
	$act_arr = array('index','demand_add','demand_item','demand_list','demand_info','sendCode','region','offer_add','sel_offer','addfollow',
		'delfollow','store_demand','getBasis','cus_list');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}

	//消费者发布需求
	if ($action == 'demand_add')
	{
		$data = _g();
		$arr  =  array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//消费者发布--对应分类
	if ($action == 'demand_cate')
	{

		$arr  =  array();
		$rs   = getSoapClients($class, $action, $arr);
		die($rs);
	}

	//需求的参数列表
	elseif ($action == 'demand_item')
	{
		$type    = _g('type','str');
		$type_id = _g('type_id');
		$arr     = array(
			'type'    => $type,
			'type_id' => $type_id,
			);
		$rs      = getSoapClients($class,$action,$arr);
		die($rs);
	}
	//====裁缝端--获得需求列表=====
	elseif ($action == 'demand_list')
	{
		$data = _g();
		$arr  = array(
			'data' => $data,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
 	}

	//=====查看需求详情=====
	elseif ($action == 'demand_info')
	{
		$data = _g();
		$arr  = array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	//====裁缝添加消费者到会员发送验证码====
	elseif ($action == 'sendCode2Cus')
	{
		$data = _g();
		$phone = _g('phone');
		if(!preg_match('/^1[34578][0-9]{9}$/',$phone)){
	    $arr = array( 'statusCode'=>1,'msg'=>'手机号码不正确，请重新输入');
			echo $json->encode($arr);
			die;
	   	}
		$arr  = array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//====发布需求发送验证码====
	elseif ($action == 'sendCode')
	{
		$data = _g();
		$phone = _g('phone');
		if(!preg_match('/^1[34578][0-9]{9}$/',$phone)){
	    $arr = array( 'statusCode'=>1,'msg'=>'手机号码不正确，请重新输入');
			echo $json->encode($arr);
			die;
	   	}
		$arr  = array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====地区三级联动=====
	elseif ($action == "region")
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	//=====宠物品种三级联动=====
	elseif ($action == "pettype")
	{
		$data = _g();
		$arr  = array(
				'data' => $data
		);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====裁缝报价=====
	elseif ($action == 'offer_add')
	{
		$data = _g();
		
		$token     = isset($data->token) ? $data->token : '';//用户标识
		$id        = isset($data->id) ? $data->id : 0;//报价的需求id
		$offer     = isset($data->offer) ? $data->offer : 0;//报价
		$del_time  = isset($data->offer) ? $data->del_time : 0;//交付日期
		$remark    = isset($data->remark) ? $data->remark : '';//备注
		
		if (isset($_FILES['url'])) {
			$url = $_FILES['url'];
		} else {
			$url = array();
		}
		
		if (isset($_FILES['url2'])) {
			$url2 = $_FILES['url2'];
		} else {
			$url2 = array();
		}
		
		if (isset($_FILES['url3'])) {
			$url3 = $_FILES['url3'];
		} else {
			$url3 = array();
		}
		
		$arr  = array(
			'token'    => $token,
			'id'       => $id,
			'offer'    => $offer,
			'del_time' => $del_time,
			'remark'   => $remark,
			'url'      => $url,
			'url2'     => $url2,
			'url3'     => $url3,
		);
		
		$rs = getSoapClients($class,$action,$arr);die($rs);
	}

	//=====消费者选择报价=====
	elseif ($action == 'sel_offer')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====消费者关注裁缝=====
	elseif ($action == 'addfollow')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
		);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====消费者取消关注裁缝=====
	elseif ($action == 'delfollow')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====裁缝获取自己的作品列表=====
	elseif ($action == 'store_demand')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====样衣列表=====
	elseif ($action == 'getBasis')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}

	//=====我的消费者列表=====
	elseif ($action == 'cus_list')
	{
		$data = _g();
		$arr  = array(
			'data' => $data
			);
		$rs = getSoapClients($class,$action,$arr);
		die($rs);
	}
	//=====添加消费者=====
	elseif ($action == 'add_cus')
	{
		$data = _g();
		$arr  = array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
 	}
 	elseif ($action == 'addCus2Member')
	{
		$data = _g();
		$arr  = array(
			'data' => $data,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
 	}
 	elseif ($action == 'edit_cus')
	{
		$data = _g();
		$arr  = array(
			'data' => $data,
			);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
 	}
	//=====消费者我的定制需求 列表=====
	elseif ($action == 'demandCustomsList')
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$status = isset($data->status) ? $data->status : '';

		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				),
			);

			echo $json->encode($arr); die;
		}

		$arr = array(
			'status' => $status,
			'token'  => $token,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == 'jipai')
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : '';
		$order_info = isset($data->order_info) ? $data->order_info : '';
// print_exit($_FILES);		
		//===== 正面图 =====
		if (isset($_FILES['baseImg']))
		{
			$baseImg = $_FILES['baseImg'];
		}
		else
		{
			$baseImg = array();
		}
		
		//===== 背面图 =====
		if (isset($_FILES['backImg']))
		{
			$backImg = $_FILES['backImg'];
		}
		else
		{
			$backImg = array();
		}
		
		//===== 里面图 =====
		if (isset($_FILES['inImg']))
		{
			$inImg = $_FILES['inImg'];
		}
		else
		{
			$inImg = array();
		}
		
		//===== 正面图 =====
		if (isset($_FILES['detailImg']))
		{
			$detailImg = $_FILES['detailImg'];
		}
		else
		{
			$detailImg = array();
		}

		$arr  = array(
				'token' => $token,
				'baseImg' => $baseImg,
				'backImg' => $backImg,
				'inImg' => $inImg,
				'detailImg' => $detailImg,
				'order_info' => $order_info,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	//===== 列表 =====
	elseif ($action == 'jipai_list')
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : '';
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : '';

		if (!$token) 
		{
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				),
			);

			echo $json->encode($arr); die;
		}

		$arr = array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,				
			'token'  => $token,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	
	//===== 即拍详情 =====
	elseif ($action == 'jipai_info')
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$id = isset($data->id) ? $data->id : '';
	
		if (!$token)
		{
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					),
			);
	
			echo $json->encode($arr); die;
		}
	
		$arr = array(
				'id'	=> $id,
				'token'  => $token,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "jipai_del")
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$id = isset($data->id) ? $data->id : '';
		
		if (!$token)
		{
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					),
			);
		
			echo $json->encode($arr); die;
		}
		
		$arr = array(
				'id'	=> $id,
				'token'  => $token,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	//===== 发表评论 =====
	elseif ($action == 'subComment')
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$id = isset($data->id) ? $data->id : '';
		$content = isset($data->content) ? $data->content : '';
		//===== 正面图 =====
		if (isset($_FILES['commentImg']))
		{
			$commentImg = $_FILES['commentImg'];
		}
		else
		{
			$commentImg = array();
		}
		
	
		if (!$token)
		{
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					),
			);
	
			echo $json->encode($arr); die;
		}
	
		$arr = array(
				'id'	=> $id,
				'token'  => $token,
				'content' => $content,
				'commentImg' => $commentImg,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	
	//===== 发表评论 =====
	elseif ($action == 'comment_list')
	{
		$data = _g();
		$token  = isset($data->token) ? $data->token : '';
		$id = isset($data->id) ? $data->id : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : '';
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : '';
	
		if (!$token)
		{
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					),
			);
	
			echo $json->encode($arr); die;
		}
		
		
		$arr = array(
				'pageSize'  => $pageSize,
				'pageIndex' => $pageIndex,
				'token'  => $token,
				'id'	=> $id,
		);
	
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	
	elseif ($action == 'chen_list')
	{
		$data = _g();
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : 1;
		$color = isset($data->color) ? $data->color : '';
		$caizhi = isset($data->caizhi) ? $data->caizhi : '';
		$order =  isset($data->order) ? $data->order : '';
		$sex  = isset($data->sex) ? $data->sex : '男款';
		$yinhua = isset($data->yinhua) ? $data->yinhua : '';
		
		$arr = array(
				'pageSize'  => $pageSize,
				'pageIndex' => $pageIndex,
				'color' => $color,
				'caizhi' => $caizhi,
				'order' => $order,
				'sex' => $sex,
				'yinhua' => $yinhua,
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	
	elseif ($action == "chen_likes")
	{
	    $data = _g();
	    $id = isset($data->id) ? $data->id : 0;
	    $arr = array(
	        'id' => $id,
	    );
	
	    $rs   = getSoapClients($class,$action,$arr);
	    die($rs);
	}
	
	elseif ($action == 'yh_list')
	{
		$data = _g();
		
		$arr = array(
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "yh_template")
	{
		$data = _g();
		$cate_id = isset($data->cate_id) ? $data->cate_id : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : 1;
		$arr = array(
				'pageSize'  => $pageSize,
				'pageIndex' => $pageIndex,
				'cate_id' => $cate_id,
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "pinlie_list")
	{
		$data = _g();
		
		$arr = array(
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "dis_list")
	{
		$data = _g();
		$type = isset($data->type) ? $data->type : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : 1;
		$arr = array(
			'type' => $type,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
		);
	
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "dis_likes")
	{
	    $data = _g();
	    $id = isset($data->id) ? $data->id : 0;
	    $arr = array(
	        'id' => $id,
	    );
	
	    $rs   = getSoapClients($class,$action,$arr);
	    die($rs);
	}
	
	
	elseif ($action == "yh_add")
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : "";
		$chen_id = isset($data->chen_id) ? $data->chen_id : 0;
	
		if (isset($_FILES['yhImg']))
		{
			$inImg = $_FILES['yhImg'];
		}
		else
		{
			$inImg = array();
		}
		
		$arr = array(
				'chen_id'=> $chen_id,
				'token' => $token,
				'yhImg' => $inImg,
		);
		
	
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	
	elseif ($action == "yh_cart")
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : "";
		$vid   = isset($data->vid) ? $data->vid : "";
		$yhid   = isset($data->yhid) ? $data->yhid : "";
		
	
		$arr = array(
				'token' => $token,
				'vid' => $vid,
				'yhid'=> $yhid,
		);
	
	
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	elseif ($action == "getSize")
	{
		$data = _g();
		
		$arr = array(
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
    //===== 取购物车内商品数量 =====
    elseif ($action == 'getGoodsNum'){

        $data = _g();
        $token = isset($data->token) ? $data->token : "";
        $arr = array(
            'token' => $token,
        );
        $rs   = getSoapClients($class,$action,$arr);
        die($rs);

    }
	
	
	//===== 添加购物车 =====
	elseif ($action == 'addCart')
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : "";
		$chen_id   = isset($data->chen_id) ? $data->chen_id : 0;
		$yhid   = isset($data->yhid) ? $data->yhid : 0;
		$quantity = isset($data->quantity) ? $data->quantity : 1;
		$cixiu  = isset($data->cixiu) ? $data->cixiu : '';
		$size = isset($data->size) ? $data->size : '';
		
	

		if (isset($_FILES['yhImg']))
		{
			$inImg = $_FILES['yhImg'];
		}
		else
		{
			$inImg = array();
		}
		
		
		
		$arr = array(
				'token' => $token,
				'chen_id' => $chen_id,
				'yhid'=> $yhid,
				'quantity' => $quantity,
				'cixiu' => $cixiu,
				'size' => $size,
				'yhImg' => $inImg,
		); 
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	//===== 添加购物车 =====
	elseif ($action == 'addDisCart')
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : "";
		$dis  = isset($data->dis) ? $data->dis : '';
		$suit_id = isset($data->suit_id) ? $data->suit_id : '';
		
		$arr = array(
				'token' => $token,
				'dis'   => $dis,
		        'suit_id' => $suit_id,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	// 作品
	elseif ($action == 'addWorks')
	{
		$data = _g();
		$token = isset($data->token) ? $data->token : "";
		$work_name   = isset($data->work_name) ? $data->work_name : "";
		$cloth  = isset($data->cloth) ? $data->cloth : "";
		$description  = isset($data->description) ? $data->description : "";
		$img = isset($data->img) ? $data->img : "";
	
	
		$arr = array(
				'token' => $token,
				'work_name' => $work_name,
				'cloth'=> $cloth,
				'description' => $description,
				'img' => $img,
		);
	
	
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	/*刺绣相关*/
	elseif ($action == 'notMad')
	{
		$data = _g();
		
		$arr = array(
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	
	/*app强制更新配置获取*/
	elseif ($action == 'getSetting')
	{
		$data = _g();
		$app     = isset($data->app) ? $data->app : "";
		$flat    = isset($data->flat) ? $data->flat : "";
		$version = isset($data->version) ? $data->version : "";
		
		$arr = array(
			'app'     => $app,
			'flat'    => $flat,
			'version' => $version,
		);
		
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}
	/*app强制更新配置获取*/
	elseif ($action == 'appset')
	{
		$data = _g();
		$app     = isset($data->app) ? $data->app : "";
		$flat    = isset($data->flat) ? $data->flat : "";
		$version = isset($data->version) ? $data->version : "";

		$arr = array(
				'app'     => $app,
				'flat'    => $flat,
				'version' => $version,
		);
		$rs   = getSoapClients($class,$action,$arr);
		die($rs);
	}

	else
	{
		echo "Lack of method ?action";
	}


?>