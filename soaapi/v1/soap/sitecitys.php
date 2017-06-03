<?php

	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class   = 'Sitecitys';
	$act_arr = array('login','register','editCate','getCate','getCateTemplate','addProject','checkUserAuth','getUserBaseInfo','editMemInfo','addAuthImg','authAssist','subChance','sendCode','checkCode','projectList','addDynamic','authStatus','qrcode','test');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

	//
	$data=_g();
	$arr=array();
	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
		//return false;
		//返回false
	}
	/******* 待定********/
	if($action == 'site'){
		$arr=array(
		'pageSize'=>$data->pageSize,
		'pageIndex'=>$data->pageIndex,
		'lat'=>$data->lat,
		'lng'=>$data->lng,
		);

	}
	/******* 待定********/
	else if($action=='sitename')
	{
		$arr=array(
			'pageSize'=>$data->pageSize,
			'pageIndex'=>$data->pageIndex,
			'mcity'=>$data->city,
		);
	}
	/*******品牌商 面料查询列表********/
	else if($action == 'part')
	{
		$arr = array(
			'pageSize'   => isset($data->pageSize) ? $data->pageSize : 10,
			'pageIndex'  => isset($data->pageIndex) ? $data->pageIndex : 1,
			'goods_name' => isset($data->goods_name) ? $data->goods_name : '',
		);

	}
	/*******客户--搜索获得面料列表********/
	else if($action == 'getByfilterPart')
	{
		$pageSize  = isset($data->pageSize) ?  $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ?  $data->pageIndex : 1;
		$order	   = isset($data->order) ?  $data->order : 1;//排序方式 1人气 2销量 3最新
		$attr      = isset($data->attr) ?  $data->attr : 1;
		if (!in_array($order, array(1, 2, 3))) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '排序参数有误',
				),
			);
			echo $json->encode($arr);die;
		}

		$arr = array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
			'order'     => $order,
			'attr'      => $attr,
		);

	}
	/*******获得面料搜索列表条件属性********/
	else if($action == 'getPartAtrr')
	{
		$arr = array();
	}
	/*******获得面料详情********/
	else if($action == 'getPartInfo')
	{
		$part_id   = isset($data->part_id) ?  $data->part_id : 0;
		if(!$part_id) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '面料号不得为空',
				),
			);
	  		return $json->encode($arr);

		}
		$arr=array(
			'part_id' => $part_id,
		);

	}
	/*******品牌商 消费者的量体数据 测********/
	else if($action=='figurelist')
	{
		$arr=array(
			'pageSize'=>$data->pageSize,
			'pageIndex'=>$data->pageIndex,
			'token'=>$data->token,
		);
	}
	/*******我的量体数据********/
	else if($action == 'figurelistbyuser')
	{
		$pageSize   = isset($data->pageSize) ?  $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ?  $data->pageIndex:1;
		$token      = isset($data->token) ?  $data->token:0;
		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			echo $json->encode($arr);die;
		}

		$arr=array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
			'token'     => $token,
		);

	}
	/*******我的量体数据详情********/
	else if($action=='getFigurebyuser')
	{
		$token   = isset($data->token) ?  $data->token:0;
		$item_id = isset($data->item_id) ?  $data->item_id:0;
		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			echo $json->encode($arr);die;
		}

		$arr=array(
			'token'   => $token,
			'item_id' => $item_id,
		);

	}
	/*******某品牌商获取某消费者的量体数据********/
	else if($action=='getFigure')
	{
		$token     = isset($data->token) ? $data->token:0;
		$figure_sn = isset($data->figure_sn) ? $data->figure_sn:0;
		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			echo $json->encode($arr);die;
		}

		$arr = array(
			'token'     => $token,
			'figure_sn' => $figure_sn,
		);

	}
	/*******某品牌商修改消费者量体数据********/
	else if($action == 'editFigure')
	{
		$arr=array(
			'data' => $data,
		);

	}
	/*******面料订购查询********/
	else if($action == 'fabrics')
	{
		$pageSize   = isset($data->pageSize) ? $data->pageSize:10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex:1;
		$token      = isset($data->token) ? $data->token:0;
		$goods_sn   = isset($data->goods_sn) ? $data->goods_sn:0;
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			return $json->encode($arr);
		}
		$arr = array(
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
			'token'      => $token,
			'goods_sn'   => $goods_sn,
		);
	}
	/*******面料订购申请********/
	else if($action=='fabricsadd')
	{
		$token       = isset($data->token) ? $data->token:0;
		$goods_sn    = isset($data->goods_sn) ? $data->goods_sn:'';
		$num         = isset($data->num) ? $data->num : 1;
		$phone       = isset($data->phone) ?  $data->phone:'';
		$owner_name  = isset($data->owner_name) ?  $data->owner_name:'';
		$description = isset($data->description) ?  $data->description:'';
		$date        = isset($data->date) ? $data->date:'';
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			return $json->encode($arr);
		}

		$arr = array(
			'token'       => $token,
			'goods_sn'    => $goods_sn,
			'num'         => $num,
			'phone'       => $phone,
			'owner_name'  => $owner_name,
			'description' => $description,
			'date'        => $date,
		);
	}
	/*******量体派工申请********/
	else if($action == 'applybody')
	{
		$token      = isset($data->token) ? $data->token:'';
        $user_id    = isset($data->user_id) ? $data->user_id:'';
		$address    = isset($data->address) ? $data->address:'';
        $type       = isset($data->type) ? $data->type:0;
        $serve      = isset($data->serve) ? $data->serve :'';
        $region_id  = isset($data->region_id) ? $data->region_id :'';
        $apply_date = isset($data->apply_date) ? $data->apply_date : 0;
        $date       = isset($data->date) ? $data->date : '';

		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			echo $json->encode($arr); die;
		}
        if(!$user_id){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '用户id不能为空',
                ),
            );
            echo $json->encode($arr); die;
        }
        if( !$address || !$region_id || !$date){
			
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '参数不能为空',
                ),
            );
			
            echo $json->encode($arr); die;
        }

		$arr=array(
			'token'      => $token,
            'user_id'    => $user_id,
			'address'    => $address,
            'type'       => $type,
            'serve'      => $serve,
            'region_id'  => $region_id,
            'apply_date' => $apply_date,
            'date'       => $date,
		);
	}
    /*******得到服务点********/
    else if($action == 'get_serve'){
        $region_id = isset($data->region_id) ? $data->region_id :'';
        $pageSize  = isset($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1 ;
        $arr = array(
          'region_id' => $region_id,
            'pageSize'=> $pageSize,
            'pageIndex'=> $pageIndex,
        );

    }
    /*******历史量体数据********/
    else if($action == 'history_figure'){
        $token = isset($data->token) ? $data->token :'';
        $mob   = isset($data->mob) ? $data->mob : '' ;
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '用户未登录',
                ),
            );
            return $json->encode($arr);
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$mob)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            return $json->encode($arr);
        }
        $arr = array(
            'token' => $token,
            'mob'   => $mob,
        );
    } /*******添加顾客并预约量体********/
    else if($action == 'add_cus'){
        $token      = isset($data->token) ? $data->token:'';
        $name       = isset($data->name) ? $data->name:'';
        $mob        = isset($data->mob) ? $data->mob:'';
        $apply      = $data->apply==1 ? $data->apply:0;
        $address    = isset($data->address) ? $data->address:'';
        $type       = isset($data->type) ? $data->type:'';
        $serve      = isset($data->serve) ? $data->serve :'';
        $region_id  = isset($data->region_id) ? $data->region_id :'';
        $apply_date = isset($data->apply_date) ? $data->apply_date : 0;
        $date       = isset($data->date) ? $data->date : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => 'token错误',
                ),
            );
            return $json->encode($arr);
        }
        if(!$name){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写顾客姓名',
                ),
            );
            return $json->encode($arr);
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$mob)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            return $json->encode($arr);
        }
        $arr = array(
            'token'      => $token,
            'name'       => $name,
            'mob'        => $mob,
            'apply'      => $apply,
            'address'    => $address,
            'type'       => $type,
            'serve'      => $serve,
            'region_id'  => $region_id,
            'apply_date' => $apply_date,
            'date'       => $date,
        );
    }
    /*******删除顾客********/
    else if($action == 'del_cus'){
        $token      = isset($data->token) ? $data->token:'';
        $cus_id     = isset($data->cus_id) ? $data->cus_id:'';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => 'token错误',
                ),
            );
            return $json->encode($arr);
        }
        if(!$cus_id){
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请选择用户',
                ),
            );
            return $json->encode($arr);
        }
        $arr = array(
            'token'  => $token,
            'cus_id' => $cus_id,
        );
    }
	else
	{
		echo "Lack of method ?action";
	}

	$rs = getSoapClients($class, $action, $arr);
	die($rs);
