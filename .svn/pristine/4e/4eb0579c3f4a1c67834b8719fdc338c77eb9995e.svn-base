<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class   = 'Store';
	$arr = array();
	$act_arr = array();
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}

	/*******根据条件获得品牌商列表********/
	if ($action == 'index')
	{
		$data = _g();
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex):1;
		$filter	    = isset($data->filter) ? intval($data->filter) : 1;
		$subject_id = isset($data->subject_id) ? intval($data->subject_id) : 0;//0：不选择 1：上门量体 2：到店服务
		$city_id    = isset($data->city_id) ? intval($data->city_id) : 0;

		if (!in_array($filter, array(1, 2))) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '筛选参数错误',
				),
			);
			echo $json->encode($arr);die;
		}

		if (!$pageSize || $pageSize < 0 || $pageIndex <= 0) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '分页参数错误',
				),
			);

			echo $json->encode($arr);die;
		}

		$arr = array(
			'subject_id' => $subject_id,
			'city_id'    => $city_id,
			'filter'	 => $filter,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
		);

	}
	/*******获取网站基本信息********/
	elseif ($action == 'service')
	{
		$arr = array();
	}
	/*******品牌商的主题列表********/
	elseif ($action == 'subject')
	{
		$arr = array();
	}
		/*******修改品牌商头像********/
	elseif ($action == 'test')
	{
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$logo_data  = $_FILES["logo_data"];
		if(empty($logo_data)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '文件数据为空',
				),
			);
			echo $json->encode($arr); die;

		}
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				),
			);
	        echo $json->encode($arr); die;
	    }

		$user_info = getToken($token);
	    if(empty($user_info)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '无效token',
				),
			);
	        echo $json->encode($arr); die;
	    }
		$store_id = $user_info['user_id'];
		/* 头像  */

		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		$home = $objAvatar->get_home($store_id);
		$path_ao =  ROOT_PATH.'/upload/avatar/'.$home;

		$path_up  = 'data/files/store_'.$store_id.'/other';
		//监察并创建目录
		ecm_mkdir($path_ao);

		$path  = $_SERVER['DOCUMENT_ROOT']. '/'  . $path_up.'/';

		$filename   = $logo_data["tmp_name"];
		$image_size = getimagesize($filename);
		$pinfo  = pathinfo($logo_data["name"]);
		$ftype  = $pinfo['extension'];

		$file_name = substr($store_id, -2).'_avatar_big.'.$ftype;
		$destination = $path_ao.'/'.$file_name;


		if (file_exists($destination)) {  //同名文件已存在，删除已有文件
			@unlink ($destination);
		}
		//移动保存文件
		if(!move_uploaded_file($filename, $destination)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '保存文件失败',
				),
			);
			echo $json->encode($arr); die;
		}

		$arr = array(
			'store_id'     => $store_id,
			'path_up_name' => $path_up.'/'.$file_name,

		);

	}
	/*******修改品牌商头像********/
	elseif ($action == 'editStoreLogo')
	{
	   
	  
		$data = _g();
		$m  = m('member');
		$token      = isset($data->token) ? $data->token : '';
		$logo_data  = $_FILES["logo_data"];
		
		if(empty($logo_data)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 111,
					'msg'       => '文件数据为空',
				),
			);
			echo $json->encode($arr); die;

		}
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				),
			);
	        echo $json->encode($arr); die;
	    }

		$user_info = getToken($token);
	    if(empty($user_info)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '无效token',
				),
			);
	        echo $json->encode($arr); die;
	    }
		$store_id = $user_info['user_id'];
	    /////////////////////////////////////
	    require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
		$image_name = $objAvatar->uploadAvatar($logo_data, 220, 220, $store_id, 'big');
		
		//移动保存文件
		if(empty($image_name)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '保存文件失败',
				),
			);
			echo $json->encode($arr); die;
		}
		
		/////////////////////////////////////
		
		$arr = array(
			'token'       => $token,
			'store_id'    => $store_id,
			'path' => $image_name,

		);
		
		
	}
	/*******品牌商详情********/
	elseif ($action == 'info')
	{
		$data = _g();
		$store_id = isset($data->store_id) ? $data->store_id: 0;
		if (!$store_id) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => 'store不能为空',
				),
			);

			echo $json->encode($arr);die;
		}

		$arr = array(
			'store_id' => $store_id,
		);

	}
	/*******品牌商分类服务和主题********/
	elseif ($action == 'getCate')
	{
		$data = _g();
		$arr = array();
	}
	/*******品牌商获取订单列表********/
	elseif ($action == 'storeOrderList')
	{
		$data       = _g();
		$arr = array(
			'data'	    => $data,
		);

	}
	/*******品牌商获取当前订单详情********/
	elseif ($action == 'storeOrderInfo')
	{
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$order_id = isset($data->order_id) ? $data->order_id : '';

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

		$arr = array(
				'order_id' => $order_id,
				'token'	   => $token,
		);

	}
	/*******品牌商 删除订单********/
	elseif ($action == 'storeDelOrder')
	{
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$order_id = isset($data->order_id) ? $data->order_id : '';

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

		$arr = array(
			'order_id' => $order_id,
			'token'	   => $token,
		);

	}
		/*******延期收获********/
	elseif ($action == 'delay_ship')
	{
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$order_id = isset($data->order_id) ? $data->order_id : '';

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
		if (empty($order_id)) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 102,
					'msg'       => '参数错误',
				),
			);
			echo $json->encode($arr);die;
		}
		$arr = array(
			'order_id' => $order_id,
			'token'	   => $token,
		);

	}
	
	/*******品牌商修改当前订单状态********/
	elseif ($action == 'editOrderStatus')
	{
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$order_id = isset($data->order_id) ? $data->order_id : '';
		$status   = isset($data->status) ? $data->status : '';
		$client   = isset($data->client) ? $data->client : '';
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
		if (empty($order_id)) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 103,
					'msg'       => '参数错误',
				),
			);
			echo $json->encode($arr);die;
		}
		$arr = array(
			'order_id' => $order_id,
			'token'	   => $token,
			'status'   => $status,
			'client'   => $client,
		);

	}
	
	/*******用户申请退款********/
	elseif ($action == 'applyOrderRefund')
	{
		$data = _g();
		$token         = isset($data->token) ? $data->token : '';
		$order_id      = isset($data->order_id) ? $data->order_id : '';
		$refund_reason = isset($data->refund_reason) ? $data->refund_reason : '';
		$logistics_num = isset($data->logistics_num) ? $data->logistics_num : '';
		
		if (empty($refund_reason) || empty($logistics_num)) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 103,
					'msg'       => '参数错误',
				),
			);
			echo $json->encode($arr);die;
		}
		$arr = array(
			'token'	        => $token,
			'order_id'      => $order_id,
			'refund_reason' => $refund_reason,
			'logistics_num' => $logistics_num,
		);

	}

    /*******个人认证********/
    elseif ($action == 'bank') {
        $data = _g();

        $arr = array(
            'data' => $data,
        );

    }

	/*******绑定银行卡********/
	elseif ($action == 'bingBank')
	{
		$data = _g();
		$token         = isset($data->token) ? $data->token : '';
        $bank_address  = isset($data->bank_address) ? $data->bank_address : '';//开户行地址
        $bank_id       = isset($data->bank_id) ? $data->bank_id : ''; //银行id
        $bank          = isset($data->bank) ? $data->bank : ''; //银行名称
        $card          = isset($data->card) ? $data->card : '';//银行卡号
        $code          = isset($data->code ) ? $data->code  : '';//验证码

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

		if(!$card || !$bank_address || !$bank_id){
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 101,
					'msg'       => '数据不能为空',
				),
			);
			echo $json->encode($arr);die;
		}
		$arr = array(
			'token'         => $token,
            'bank_address'  => $bank_address,
            'card'          => $card,
            'bank_id'       => $bank_id,
            'bank'          => $bank,
            'code'          => $code,
		);
	}
    /*******确认得到金额********/
    elseif ($action == 'get_money')
    {
        $data = _g();
        $token     = isset($data->token) ? $data->token : '' ;
        $get_money = isset($data->get_money) ? $data->get_money : '' ;
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => 'token错误',
                ),
            );
            echo $json->encode($arr);die;
        }
        if(!$get_money){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请输入您收到的金额',
                ),
            );
            echo $json->encode($arr);die;
        }
        $arr = array(
            'token'     => $token,
            'get_money' => $get_money,
        );

    }
	/*******实名认证********/
	elseif ($action == 'real_auth')
    {
		$data = _g();
		$token        = isset($data->token) ? $data->token : '';
        $real_name    = isset($data->real_name) ? $data->real_name : '';//真实姓名
        $bank         = isset($data->bank) ? $data->bank : '';//选择银行
        $bank_id      = isset($data->bank_id) ? $data->bank_id :'';//银行id
        $bank_address = isset($data->bank_address) ? $data->bank_address : '';//开户行地址
        $bank_card    = isset($data->bank_card) ? $data->bank_card : '';//银行卡号
        $code         = isset($data->code) ? $data->code : '';//验证码
        $card_face_img= isset($data->card_face_img) ? $data->card_face_img : '';//身份证正面
        $card_back_img= isset($data->card_back_img) ? $data->card_back_img : '';//身份证背面面

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
        /*===== 正则验证身份证 =====
        $preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
        $preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
        if (!preg_match($preg18, $legal_card) && !preg_match($preg15, $legal_card))
        {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '请输入正确的身份证号',
                ),
            );
            echo $json->encode($arr);die;
        }
        if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$real_name))
        {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 103,
                    'msg'       => '姓名填写不合法',
                ),
            );
            echo $json->encode($arr);die;
        }
        */
        if(!$real_name || !$bank || !$bank_address || !$bank_card || !$code || !$card_back_img || !$card_face_img){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '参数不能为空'
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr = array(
            'token'         => $token,
            'real_name'     => $real_name,
            'bank'          => $bank,
            'bank_id'       => $bank_id,
            'bank_address'  => $bank_address,
            'bank_card'     => $bank_card,
            'code'          => $code,
            'card_face_img' => $card_face_img,
            'card_back_img' => $card_back_img,
        );
    }
    /*******放弃认证申请********/
    elseif($action == 'giveUpAuth'){
        $data = _g();
        $token = isset($data->token) ? $data->token : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
        }
        $arr = array(
            'token' => $token,
        );

    }
    /*******上传身份证前面********/
    elseif($action == 'uploadFaceImg') {
        $data      = _g();
        $token     = isset($data->token) ? $data->token : '';
       //print_r($token);die;
        $card_face = isset($_FILES['card_face_img']) ? $_FILES['card_face_img'] : '';//身份证前面
        //获得传过来图片信息
        if(empty($token)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }

        if(!$card_face) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '请选择上传图片',
                ),
            );
            echo $json->encode($arr); die;
        }
        //上传
        /*
        $user_info   = getToken($token);
        if(empty($user_info)) {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '未找到此用户标识',
                )
            );
            echo $json->encode($return);die;
        }
*/
        //保存图片
        $real_dir  = ROOT_PATH."/upload/card/";
        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
        $imageTool = new ImageTool();
        $uniqueString = $imageTool->getUniqueString();
        $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($card_face['name'], '.');
        $sub_dir      = date('Ym');
        if(!is_dir($real_dir.$sub_dir)) {
            mkdir($real_dir.$sub_dir, 0777, true);
        }
        if(!move_uploaded_file($card_face["tmp_name"], $real_dir.$sub_dir.'/'.$new_name)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '保存文件失败',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'     => $token,
            'card_face' => $sub_dir.'/'.$new_name,
        );

    }
    /*******上传身份证背面********/
    elseif($action == 'uploadBackImg') {
        $data      = _g();
        $token     = isset($data->token) ? $data->token : '';
        $card_back = isset($_FILES['card_back_img']) ? $_FILES['card_back_img'] : '';//身份证后面
        //获得传过来图片信息
        if(empty($token)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }

        if(!$card_back) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '请选择上传图片',
                ),
            );
            echo $json->encode($arr); die;
        }
        //上传
        /*
        $user_info   = getToken($token);
        if(empty($user_info)) {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '未找到此用户标识',
                )
            );
            echo $json->encode($return);die;
        }
*/
        //保存图片
        $real_dir  = ROOT_PATH."/upload/card/";
        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
        $imageTool = new ImageTool();
        //$imageTool->_upload_dir = $real_dir;
        //$beatImagedir = $imageTool->uploadImage($beatImage);
        $uniqueString = $imageTool->getUniqueString();
        $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($card_back['name'], '.');
        $sub_dir      = date('Ym');

        if(!is_dir($real_dir.$sub_dir)) {
            mkdir($real_dir.$sub_dir, 0777, true);
        }

        if(!move_uploaded_file($card_back["tmp_name"], $real_dir.$sub_dir.'/'.$new_name)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '保存文件失败',
                ),
            );
            echo $json->encode($arr); die;
        }

        $arr = array(
            'token'     => $token,
            'card_back' => $sub_dir.'/'.$new_name,
        );

    }
    /*******更改作品状态********/
    elseif($action == 'editWork'){
        $data = _g();
        $work_id = isset($data->work_id) ? $data->work_id : '';
        $status  = isset($data->status) ? $data->status : '';

        $arr = array(
            'work_id' => $work_id,
            'status'  => $status,
        );
    }
    /*******上传认证图片********
    elseif($action == 'uploadAuthImg'){
        $data = _g();
        $token = isset($data->token) ? $data->token : '';
        $type = isset($_FILES['card_face_img']) ? $_FILES['card_face_img'] : '';//身份证前面
        $type = isset($_FILES['card_back_img']) ? $_FILES['card_back_img'] : '';//身份证后米娜
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
        }
        if(!$type){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '上传文件不能为空',
                ),
            );
        }
        $arr = array(
            'token'   => $token,
            'type'    => $type,
        );
    }
    /*******删除认证图片********/
    elseif($action == 'delAuthImg'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $field   = isset($data->field) ? $data->field : '';
        $img     = isset($data->img) ? $data->img : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
        }
        if(!$field){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '字段不能为空',
                ),
            );
        }
        if(!$img){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg' => '图片名不能为空',
                ),
            );
        }
        $arr = array(
            'token'   => $token,
            'field'   => $field,
            'img'     => $img,
        );
    }
    /*******上传作品********/
    elseif($action == 'uploadWork') {
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $cloth   = isset($data->cloth) ? $data->cloth : '';
        $des     = isset($data->des) ? $data->des  : '';
        $voting  = !empty($data->voting) ? $data->voting : 0;
        $imgs    = isset($data->imgs) ? $data->imgs : '';
        $cover   = isset($data->cover) ? $data->cover : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
        }
        if(!$cloth || !$des || !$imgs){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '参数不能为空',
                ),
            );
        }
        $arr = array(
            'token' => $token,
            'cloth' => $cloth,
            'des'   => $des,
            'voting'=> $voting,
            'imgs'  => $imgs,
            'cover' => $cover,
        );
    }
    /*******删除作品********/
    elseif($action == 'addWork'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $cloth   = isset($data->cloth) ? $data->cloth : '';
        $voting  = !empty($data->voting) ? $data->voting : 0;
        $id      = isset($data->id) ? $data->id : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
        }
        if($cloth!=0 || $cloth!=1 || !$id){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '请填写正确参数',
                ),
            );
        }
        $arr = array(
            'token' => $token,
            'cloth' => $cloth,
            'voting'=> $voting,
            'id'    => $id,
        );

    }
    /*******删除作品********/
    elseif($action == 'delWork'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $work_id = isset($data->work_id) ? $data->work_id : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }
        if(!$work_id){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '请选择要删除的作品',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'   => $token,
            'work_id' => $work_id,
        );
    }
    /*******删除分类下所有作品********/
    elseif($action == 'delAllWork'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $cloth   = isset($data->cloth) ? $data->cloth : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }
        if(!$cloth){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '请选择要删除分类',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'   => $token,
            'cloth' => $cloth,
        );
    }
    /*******作品列表********/
    elseif($action == 'workList') {
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $cloth   = !empty($data->cloth) ? $data->cloth : 3;
        $pageSize  = !empty($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1 ;
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'    => $token,
            'cloth'    => $cloth,
            'pageSize' => $pageSize,
            'pageIndex'=> $pageIndex,
        );
    }
    /*******作品列表********/
    elseif($action == 'workInfo'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $work_id = !empty($data->work_id) ? $data->work_id : '';
        $pageSize  = !empty($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1 ;
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }
        if(!$work_id){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg' => '请选择作品',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'   => $token,
            'work_id' => $work_id,
            'pageSize' => $pageSize,
            'pageIndex'=> $pageIndex,
        );
    }
    /*******作品添加购物车********/
    elseif ($action == 'addWorkCart')
    {
        $data = _g();
        $token = isset($data->token) ? $data->token : "";
        $work_id  = isset($data->work_id) ? $data->work_id : '';
        $size  = isset($data->size) ? $data->size : '';
        $arr = array(
            'token'   => $token,
            'work_id' => $work_id,
            'size'    => $size,
        );
    }
    /*******上传作品图片********/
    elseif($action == 'uploadWorkImg') {
        $data    = _g();
        $token   = isset($data->token) ? $data->token : '';
        $work_img = isset($_FILES['work_img']) ? $_FILES['work_img'] : '';
        //获得传过来图片信息
        if(empty($token)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }

        if(!$work_img) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '请选择上传图片',
                ),
            );
            echo $json->encode($arr); die;
        }
        //保存图片
        $real_dir  = ROOT_PATH."/upload/work/";
        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
        $imageTool = new ImageTool();
        //$imageTool->_upload_dir = $real_dir;
        //$beatImagedir = $imageTool->uploadImage($beatImage);
        $uniqueString = $imageTool->getUniqueString();
        $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($work_img['name'], '.');
        $sub_dir      = date('Ym');
        if(!is_dir($real_dir.$sub_dir)) {
            mkdir($real_dir.$sub_dir, 0777, true);
        }

        if(!move_uploaded_file($work_img["tmp_name"], $real_dir.$sub_dir.'/'.$new_name)) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '保存文件失败',
                ),
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'    => $token,
            'work_img' => SITE_URL.'/upload/work/'.$sub_dir.'/'.$new_name,
        );

    }
    /*******评选列表********/
    elseif($action == 'votingList'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $status  = isset($data->status) ? $data->status : 0;
        $pageSize  = !empty($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1 ;
        if(empty($token)) {
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
            'token' => $token,
            'status'=> $status,
            'pageSize' => $pageSize,
            'pageIndex'=> $pageIndex,
        );
    }
	else
	{
		echo "Lack of method ?action";
	}

	$rs = getSoapClients($class, $action, $arr);
	die($rs);


?>