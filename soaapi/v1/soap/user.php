<?php
    header('Access-Control-Allow-Origin: *');
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class   = 'User';
	$act_arr = array('login','register','editCate','getCate','getCateTemplate','addProject','checkUserAuth','getUserBaseInfo','editMemInfo','addAuthImg','authAssist','subChance','sendCode','checkCode','projectList','addDynamic','authStatus','qrcode','tequan','tequan_info','fx','fx_add','fx_status_list','fx_order_status','fx_receive','fx_info','order_serve_status','qqfabricSelect','qqfabric','qqfabriclist',"qqsuitList",'test','homePage','getImpressionArray','getCommentList','editMyAddress','myAddrList');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

	//http://local.rc.com/soaapi/soap/user.php?act=modifyPassWord&oldPassWord=123456&newPassWord=admin123 示例


	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
		//return false;
		//返回false
	}

	/*******普通会员登录********/
	if ($action == 'login')
	{
		/*phoneNum	String	必填	手机号（或Email）
		  passWord	String	必填	密码*/

		$userName = _g('phoneNum', 'str');
		$passWord = _g('passWord', 'str');
		/* 参数验证 */
		if (empty($userName) || empty($passWord))
		{
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '手机号码和密码不能为空',
				)
			);
			echo $json->encode($arr); die;
		}

		$arr = array(
			'user_name' => $userName,
			'password'  => $passWord
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******普通会员注册********/
	elseif ($action == 'register')
	{

//		/*phoneNum	String	必填	手机号
//		  passWord	String	必填	密码
//		  code	    String	必填	验证码*/
		$data = _g();
		$phoneNum = isset($data->phoneNum) ? $data->phoneNum : '';
		$passWord = isset($data->passWord) ? $data->passWord : '';
		$code     = isset($data->code) ? $data->code : '';
		$invite   = isset($data->invite) ? trim($data->invite) : '';//邀请码
		//$access_token   = isset($data->access_token) ? trim($data->access_token) : '';//第三方登录标识
		//$type     = isset($data->type) ? trim($data->type) : '';//第三方登录 类型 1 qq 2 微信  3 微博
        
		

		/*检测手机合法性*/
		if(!preg_match('/^1[34578][0-9]{9}$/', $phoneNum)){
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 107,
					'msg'       => '手机号码不正确，请重新输入',
				),
			);
			echo $json->encode($arr); die;
	   	}
	   	
/* 	   	$preg = "/^[0-9A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/";
	   	if(!preg_match($preg,$email)){
	   	    $arr = array(
	   	        'statusCode' => 0,
	   	        'error' => array(
	   	            'errorCode' => 108,
	   	            'msg'       => '请填写正确的邮箱',
	   	        ));
	   	    echo $json->encode($arr);die;
	   	}
 */
		//密码不能为空
	   	if(empty($passWord)) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 107,
					'msg'       => '密码不能为空',
				),
			);

	   		echo $json->encode($arr); die;
	   	}

		$arr = array(
			'mobile'   => $phoneNum,
			'password' => $passWord,
			'code'     => $code,
			'invite'   => $invite,
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	
	elseif ($action == 'binduser')
	{
	
	    //		/*phoneNum	String	必填	手机号
	    //		  passWord	String	必填	密码
	    //		  code	    String	必填	验证码*/
	    $data = _g();
	    $token = isset($data->token) ? $data->token : '';
	    $passWord = isset($data->passWord) ? $data->passWord : '';
	    $code     = isset($data->code) ? $data->code : '';
	    $access_token   = isset($data->access_token) ? trim($data->access_token) : '';//第三方登录标识
	    $type     = isset($data->type) ? trim($data->type) : '';//第三方登录 类型 1 qq 2 微信  3 微博 4 手机号
	    
	    if(!$token){
	        $arr = array(
	            'statusCode' => 0,
	            'error' => array(
	                'errorCode' => 100,
	                'msg' => 'token不能为空',
	            ),
	        );
	    }
	
	    if(empty($access_token)){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '绑定账号不能为空',
	            ),
	        );
	    
	        echo $json->encode($arr); die;
	    }
	    if(empty($type)) {
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 102,
	                'msg'       => '绑定账号类型不能为空',
	            ),
	        );
	    
	        echo $json->encode($arr); die;
	    }
	
	
	    /*检测手机合法性*/
	    if(preg_match('/^1[34578][0-9]{9}$/', $access_token)){
	        
	        if(empty($code) && empty($passWord)) {
	            $arr = array(
	                'statusCode' => 0,
	                'error'      => array(
	                    'errorCode' => 103,
	                    'msg'       => '手机验证码和 密码不能为空',
	                ),
	            );
	             
	            echo $json->encode($arr); die;
	        }
	        
	    }
	     
	    $arr = array(
	        'access_token'   => $access_token,
	        'type' => $type,
	        'code'     => $code,
	        'password'   => $passWord,
	        'token' =>$token
	    );
	
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	elseif ($action == 'reg')
	{
	
	    //		/*access_token	String	必填	第三方登录标识
	    //		  type	String	必填	第三方登录类型
	 
	    $data = _g();
	    $access_token   = isset($data->access_token) ? trim($data->access_token) : '';//第三方登录标识
	    $type     = isset($data->type) ? trim($data->type) : '';//第三方登录 类型 1 qq 2 微信  3 微博
	    $avatar     = isset($data->avatar) ? trim($data->avatar) : ''; // 头像
	    $nickname     = isset($data->nickname) ? trim($data->nickname) : ''; // 头像
	
	
	
	    $arr = array(
	        'access_token' =>$access_token,
	        'type'     => $type,
	        'avatar'   =>$avatar,
	        'nickname' =>$nickname,
	        	
	
	    );
	
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******用户获得邀请码********/
	elseif ($action == 'getInviteCode')
	{
		$data = _g();
		$token   = isset($data->token) ? $data->token : '';
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
			'token'   => $token,
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	
		elseif ($action == 'make_order_card')
	{
		$data = _g();
	
		
		$arr = array(
			
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	
	/*******用户获得邀请码********/
	elseif ($action == 'isFistorder')
	{
		$data = _g();
		$token   = isset($data->token) ? $data->token : '';
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
			'token'   => $token,
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******列出我推荐安装app用户********/
	elseif ($action == 'getInviteUser')
	{	
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
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
        }
		
		$arr = array(
			'token'     => $token,
			'pageSize'  => $pageSize,
            'pageIndex' => $pageIndex,

		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******裁缝登录********/
	elseif($action == 'storeLogin') {
	    $phoneNum = _g('phoneNum','str');
	    $passWord = _g('passWord','str');
	    $access_token = _g('access_token','str');
        $access_token = !empty($access_token) ? $access_token : '' ;
        $type = _g('type','str');
        $type = !empty($type) ? $type : '' ;
        
        $client = _g('client','str');
        $client = !empty($client) ? $client : '' ;
	    /*检测手机合法性*/


	/*     if (empty($phoneNum) || empty($passWord))
	    {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '手机号码或密码不能为空',
				)
			);
	        echo $json->encode($arr); die;
	    } */

	    $arr = array(
	        'phoneNum' => $phoneNum,
	        'password' => $passWord,
	        'access_token' => $access_token,
	        'type'     =>$type,
	        'client'   =>$client,

	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******修改会员的个人资料********/
	elseif ($action == 'setUserInfo')
	{
		/*token	    String	必填	用户合法标识
		  nickname  String		    昵称
		  real_name	String		    真实姓名
		  gender    String          性别
		  email     String          邮箱
		  Im_qq   	String          Im_qq
		  Im_msn    String          Im_msn */

	    $data = _g();
		if(empty($data)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '提交数据不能为空',
				)
			);
			echo $json->encode($arr); die;

		}

	    $arr  = array(
	        'data' => $data
	    );
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******提交裁缝申请********/
	elseif($action == 'applyStore') {
	    $data = _g();
	    $arr  = array(
	        'data' => $data,
	    );
		if(empty($data->token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				),
			);

			echo $json->encode($arr); die;
		}
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******创业者提现操作所需参数项返回********/
	elseif($action == 'cashParamData') {
	    $data = _g();
		$token = isset($data->token) ? $data->token : '';
		
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
		
		$arr  = array(
	        $token = $token,
	    );
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******创业者提现操作********/
	elseif($action == 'memberCash') {
	    $data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$bank_id    = isset($data->bank_id) ? intval($data->bank_id) : 0;//用户储存银行卡id
		$cash_money = isset($data->cash_money) ? $data->cash_money : 0;
		$paymentPwd = isset($data->paymentPwd) ? $data->paymentPwd : '';
		
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
		
		if(empty($bank_id) || empty($cash_money) ) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 102,
					'msg'       => '参数错误！',
				),
			);
			echo $json->encode($arr); die;
		}
		
		if(empty($paymentPwd)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103,
					'msg'       => '支付密码不能为空！',
				),
			);
			echo $json->encode($arr); die;
		}

		$arr  = array(
	        'token'      => $token,
			'bank_id'    => $bank_id,
			'cash_money' => $cash_money,
			'paymentPwd' => $paymentPwd,
	    );
	
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******创业者提现操作********/
	elseif($action == 'delBank') {
	    $data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$bid   = isset($data->bid) ? intval($data->bid) : 0;
		
		if(empty($token) ||  empty($bid)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '参数不正确！',
				),
			);

			echo $json->encode($arr); die;
		}
	
		$arr  = array(
	        'token' => $token,
			'bid'   => $bid,
	    );
	
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******提现列表********/
	elseif($action == 'cashList') {
		$data    = _g();
		$token   = isset($data->token) ? $data->token : '';
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex):1;
		if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
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
		
		$arr  = array(
	        'token'      => $token,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	/*******提现列表********/
	elseif($action == 'payList') {
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$filter	    = isset($data->filter) ? intval($data->filter) : 0;//0：全部 1：支出 2：收入
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex):1;
		
		if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg' => 'token不能为空',
                ),
            );
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
		
		$arr  = array(
	        'token'      => $token,
			'filter'     => $filter,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	/*******我的财富（收益、麦富迪币）********/
	elseif($action == 'myWealth') {
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$type	    = isset($data->type) ? intval($data->type) : 100;//0：收益 1：积分 2：麦富迪币 3：购物券 4：余额
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
	
		if(!$token){
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		$arr  = array(
	        'token'      => $token,
			'type'       => $type,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );
			
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	/*******我的红包********/
	elseif($action == 'myBonus') {
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex):1;
	
		if(!$token){
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }

		$arr  = array(
	        'token'      => $token,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );
			
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	
	/*******拆红包********/
	elseif($action == 'openBonus') {
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$bonus_id = isset($data->bonus_id) ? $data->bonus_id : 0;
		
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		if(!$bonus_id) {
			$this->msg = '参数错误';
			echo $this->eresult();die;
        }
		
		$arr  = array(
	        'token'    => $token,
			'bonus_id' => $bonus_id,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	
	/*******红包记录********/
	elseif($action == 'bonusRecord') {
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
		$pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
		
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		$arr  = array(
	        'token' => $token,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	
	/*******收益转出（余额、麦富迪币）********/
	elseif($action == 'coinTurnout') {
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		$type       = isset($data->type) ? $data->type : 1;// 1：余额 2：麦富迪币
		$logid      = isset($data->logid) ? $data->logid : 0;//转入记录id；0：为一键转入
		
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		$arr  = array(
	        'token'      => $token,
			'type'       => $type,
			'logid'      => $logid,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	
	/*******我的相关财富总数********/
	elseif($action == 'myWealthCount') {
		$data = _g();
		$token      = isset($data->token) ? $data->token : '';
		
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		
		$arr  = array(
	        'token'      => $token,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	
	/*******抵用券转赠********/
	elseif($action == 'giftDonation') {
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
		$gift      = isset($data->gift) ? $data->gift : '';
		$to_userid = isset($data->to_userid) ? $data->to_userid : '';
				
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100, 
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		if(!$gift || !$to_userid) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103, 
					'msg'       => '参数错误'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		$arr  = array(
	        'token'     => $token,
			'gift'      => $gift,
			'to_userid' => $to_userid,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
		
	}
	
	/*******“提交裁缝申请”页面对应的 服务、风格 ********/
	elseif($action == 'getAttr') {

	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******“返回app端需要显示过滤的订单状态 ********/
	elseif($action == 'ordersStatusList') {

	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******消费者 根据条件获取订单列表********/
	elseif($action == 'userOrdersList') {
		$data = _g();
		$pageSize   = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex:1;
		$token	    = isset($data->token) ? $data->token : 1;
		$order_sn   = isset($data->order_sn) ? $data->order_sn : 0;
		$status     = isset($data->status) ? $data->status : -1;//默认-1显示左右

		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);

			echo $json->encode($arr); die;
		}

	    $arr = array(
	        'token'     => $token,
	        'status'    => $status,
	        'pageIndex' => $pageIndex,
	        'pageSize'  => $pageSize,
	        'order_sn'  => $order_sn
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******消费者 根据条件获取订单详情********/
	elseif($action == 'userOrderInfo') {
		$data = _g();
		$token    = isset($data->token) ? $data->token : '';
		$order_id = isset($data->order_id) ? $data->order_id : 0;

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

	    $rs = getSoapClients($class, $action, $arr);die($rs);

	}
	/*******手机发送验证码********/
	elseif ($action == 'sendCode')
	{
		/*phoneNum	String	必填	手机号*/
		$data     = _g();
		$phoneNum = isset($data->phoneNum) ? $data->phoneNum : '';
		$type     = isset($data->type) ? $data->type : 'reg';//默认为：register
		/*检测手机合法性*/
		if(!preg_match('/^1[34578][0-9]{9}$/', $phoneNum)){
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '手机号码不正确，请重新输入',
				),
			);
			echo $json->encode($arr); die;
		}

		$arr = array(
			'phoneNum' => $phoneNum,
			'type'     => $type,
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******手机验证--已存在用户********/
	elseif ($action == 'verifyPhoneNum')
	{
	    /*phoneNum	String	必填	手机号
		  type	   String	必填	短信类型*/
	    $data     = _g();
	    $phoneNum = isset($data->phoneNum) ? $data->phoneNum : '';
		$type     = isset($data->type) ? $data->type : 'register';//默认为：register
	    /*检测手机合法性*/
	    if(!preg_match('/^1[34578][0-9]{9}$/',$phoneNum)){
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '手机号码不正确，请重新输入',
				)
			);
	        echo $json->encode($arr); die;
	    }


	    $arr = array(
	        'mobile' => $phoneNum,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******通过token 获取用户信息********/
	elseif($action == 'getToken') {
	    $token=_g('token', 'str');
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
	        echo $json->encode($arr); die;
	    }

	    $arr=array(
	        'token' => $token
	    );

	    $rs=getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******忘记密码********/
	elseif ($action == 'forgetPassword') {

		/*token	      String 必填 用户合法标识
		  newpassword String 必填 新密码 */
	    $data = _g();
	    if($data) {
			$phoneNum    = $data->phoneNum;
	        $newpassword = $data->newpassword;
            $newpassword1 = $data->newpassword1;
			//$sms_id      = $data->sms_id;//验证码标识
			$code        = $data->code;//验证码
	    }



		if(empty($code)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '验证码不能为空',
				)
			);

			echo $json->encode($arr); die;
		}


		if(empty($newpassword1)|| empty($newpassword) || empty($phoneNum)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '手机号或新密码不能为空',
				)
			);

			echo $json->encode($arr); die;
		}

	    $arr = array(
	        'phoneNum'    => $phoneNum,
	        'newpassword' => $newpassword,
	        'newpassword1' => $newpassword1,
			'code'        => $code,
	    );


	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******用户登录后--修改密码********/
	elseif ($action == 'modifyPassword') {
		/*token	      String 必填 用户合法标识
		  newpassword String 必填 新密码 */
	    $data = _g();
	    if($data) {
	        $token       = $data->token;
	        $newpassword = $data->newpassword;
			$oldpassword = $data->oldpassword;
	    }
		if(empty($newpassword) || empty($token) || empty($oldpassword)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token或新密码不能为空',
				)
			);

			echo $json->encode($arr); die;
		}

	    $arr = array(
	        'token'       => $token,
			'oldpassword' => $oldpassword,
            'newpassword' => $newpassword,
	    );
//echo '<pre>';print_r($arr);exit;

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
    /*******绑定手机号********/
    elseif($action == 'bingPhone') {
        $data   = _g();
        $token  = isset($data->token) ? $data->token : '';
        $mobile = isset($data->mobile) ? $data->mobile : '';
        $code   = isset($data->code) ? $data->code : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
        }
        if(!$mobile){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '手机号不能为空',
                ));
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$mobile)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            echo $json->encode($arr);die;
        }
        $arr = array(
            'token'  => $token,
            'mobile' => $mobile,
            'code'   => $code,
        );
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
    }
    /*******确认验证码********/
    elseif($action == 'verify_code'){
        $data = _g();
        $token   = isset($data->token) ? $data->token : '';
        $mob  = isset($data->mob) ? $data->mob : '';
        $code = isset($data->code) ? $data->code : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }
        if(!$mob ){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '手机号不能为空',
                ));
            echo $json->encode($arr);die;
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$mob)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            echo $json->encode($arr);die;
        }
        $arr = array(
            'token'=> $token,
            'mob'  => $mob,
            'code' => $code,
        );
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
    }
    /*******修改绑定手机号********/
    elseif($action == 'editPhone') {
        $data    = _g();
        $token   = isset($data->token) ? $data->token : '';
        $newMob  = isset($data->newMob) ? $data->newMob : '';
        $code    = isset($data->code) ? $data->code : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }
        if(!$newMob ){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '手机号不能为空',
                ));
            echo $json->encode($arr);die;
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$newMob)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            echo $json->encode($arr);die;
        }
        $arr = array(
            'token'  => $token,
            'newMob' => $newMob,
            'code'   => $code,
        );
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
    }
    /*******发送邮件********/
    elseif($action == 'sendEmail') {
        $data = _g();
        $token    = isset($data->token) ? $data->token : '';
        $email    = isset($data->email) ? $data->email : '';
        $type     = isset($data->type) ? $data->type : '';
        $category = isset($data->category) ? $data->category : '';
        $ps       = isset($data->ps)  ? $data->ps : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }
        $preg = "/^[0-9A-Za-zd]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/";
        if(!preg_match($preg,$email)){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的邮箱',
                    ));
            echo $json->encode($arr);die;
        }
        if(!$type || !$category){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '请选择邮件类型',
                ));
            echo $json->encode($arr);die;
        }
        if($type == 'reg' && $category == 'reg'){
            if(!$ps){
                $arr = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '密码不能为空',
                    ));
                echo $json->encode($arr);die;
            }
        }
        $arr = array(
            'token'   => $token,
            'type'    => $type,
            'category'=> $category,
            'email'   => $email,
            'ps'      => $ps,
        );
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
    }elseif($action=='verifyCode'){
         
        $data = _g();
        $mob  = isset($data->mobile) ? $data->mobile : '';
        $code = isset($data->code) ? $data->code : '';
  
        if(!$mob ){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '手机号不能为空',
                ));
            echo $json->encode($arr);die;
        }
        if (!preg_match('/^1[34578][0-9]{9}$/',$mob)) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '请填写正确的手机号',
                ),
            );
            echo $json->encode($arr);die;
        }
        $arr = array(
            'mobile'  => $mob,
            'code' => $code,
        );
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
         
         
    }
	
	/*******修改支付密码********/
    elseif($action == 'editPayPwd') {
        $data      = _g();
        $token     = isset($data->token) ? $data->token : '';
        $mobile    = isset($data->mobile) ? $data->mobile : '';
        $pay_ps    = isset($data->pay_ps) ? $data->pay_ps : '';
		$oldpay_ps = isset($data->oldpay_ps) ? $data->oldpay_ps : '';

        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                )
            );
            echo $json->encode($arr); die;
        }
		
        if(!preg_match('/^1[34578][0-9]{9}$/',$mobile)){
            $arr = array(
                'statusCode'=> 0,
                'error' =>array(
                    'errorCode' => 101,
                    'msg'=>'手机号码不正确，请重新输入'
                )
            );
            echo $json->encode($arr); die;
        }
        $arr = array(
            'token'     => $token,
            'mobile'    => $mobile,
            'pay_ps'    => $pay_ps,
			'oldpay_ps' => $oldpay_ps,
        );
		
        $rs = getSoapClients($class,$action,$arr);
        die($rs);
    }

    /*******设置支付密码********/
    elseif($action == 'setPayPwd') {
        $data   = _g();
        $token  = isset($data->token) ? $data->token : '';
        $mobile = isset($data->mobile) ? $data->mobile : '';
        $code   = isset($data->code) ? $data->code : '';
        $pay_ps = isset($data->pay_ps) ? $data->pay_ps : '';
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                )
            );
            echo $json->encode($arr); die;
        }
        if(!preg_match('/^1[34578][0-9]{9}$/', $mobile)){
            $arr = array(
                'statusCode'=> 0,
                'error' =>array(
                    'errorCode' => 101,
                    'msg'=>'手机号码不正确，请重新输入'
                )
            );
            echo $json->encode($arr); die;
        }
		
        $arr = array(
            'token'  => $token,
            'mobile' => $mobile,
            'code'   => $code,
            'pay_ps' => $pay_ps,
        );
        $rs = getSoapClients($class,$action,$arr);die($rs);
    }

	/*******我的收货地址********/
	elseif($action == 'getConsigneeList') {
		/*token	      String 必填 用户合法标识 */
	    $token = _g('token');
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
			echo $json->encode($arr); die;
		}

	    $arr   = array(
	        'token'=>$token,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******添加收获地址********/
	elseif($action == 'addAddress') {

        $data  = _g();
        $token = _g('token');
	    /* 验证  */
	    /*   if(!preg_match('/^1[3458][0-9]{9}$/',$phonemob)){
	     $arr = array( 'statusCode'=>1,'msg'=>'手机号码不正确，请重新输入');
	     echo $json->encode($arr); die;
	    } */
	    /* 	    if(!preg_match('/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/',$email)){
	     $arr = array( 'statusCode'=>1,'msg'=>'邮箱不正确，请重新输入');
	     echo $json->encode($arr); die;
	     }
	     if(!preg_match('/^[1-9]\d{5}$/',$zipcode)){
	     $arr = array( 'statusCode'=>1,'msg'=>'邮编不正确，请重新输入');
	     echo $json->encode($arr); die;
	     }
	     if(!preg_match('/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/',$phonetel)){
	     $arr = array( 'statusCode'=>1,'msg'=>'电话号码不正确，请重新输入');
	     echo $json->encode($arr); die;
	     }
	    */
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				)
			);
			echo $json->encode($arr); die;
		}

	    $arr = array(
	        'data'  => $data,
	        'token' => $token
	    );
	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******编辑收获地址********/
	elseif($action == 'editAddress') {

	    $data  = _g();
		$token = _g('token');
	    if(empty($data)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '没有要编辑的内容',
				)
			);
	        echo $json->encode($arr); die;
	    }

		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				)
			);
			echo $json->encode($arr); die;
		}

	    $arr = array(
	        'data'  => $data,
			'token' => $token
	    );

	    $rs=getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******删除收获地址********/
	elseif($action == 'delAddress') {
	    $token   = _g('token');
		$addr_id = _g('addr_id');
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				)
			);
			echo $json->encode($arr); die;
		}

		if(empty($addr_id)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '没有要删除的地址',
				)
			);
	        echo $json->encode($arr);
	    }

	    $arr = array(
	        'token'   => $token,
	        'addr_id' => $addr_id
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******批量删除收获地址********/
	elseif($action == 'delLotAddress') {
		$token    = _g('token');
		$addr_ids = _g('addr_ids');
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				)
			);
			echo $json->encode($arr); die;
		}

		if(empty($addr_ids)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '没有要删除的地址',
				)
			);
	        echo $json->encode($arr);
	    }

	    $arr = array(
	        'token'   => $token,
	        'addr_ids' => $addr_ids
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******设置默认收货地址********/
	elseif($action == 'setDefAddr') {
	    $addr_id = _g('addr_id');
	    $token   = _g('token');
		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				)
			);
			echo $json->encode($arr); die;
		}

		if(empty($addr_id)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '没有要设置的地址',
				)
			);
	        echo $json->encode($arr);
	    }

	    $arr=array(
	        'addr_id'=>$addr_id,
	        'token'  =>$token
	    );

	    $rs=getSoapClients($class, $action, $arr);die($rs);

	}
	/*******我的宠物********/
	elseif($action == 'getPetList') {
		/*token	      String 必填 用户合法标识 */
		$token = _g('token');
		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					)
			);
			echo $json->encode($arr); die;
		}
	
		$arr   = array(
				'token'=>$token,
		);
	
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	
	/*******上传宠物头像********/
	elseif($action == 'addPetAvatar') {
	
		$data  = _g();
		$token = _g('token');
		$pic= isset($_FILES['pet_avatar_img']) ? $_FILES['pet_avatar_img'] : '';//身份证前面

		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					)
			);
			echo $json->encode($arr); die;
		}
		
		if(!$pic) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 101,
							'msg'       => '请选择上传图片',
					),
			);
			echo $json->encode($arr); die;
		}
	
		$arr = array(
				'token' => $token,
				'pic'=>$pic
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	
	/*******添加我的宠物********/
	elseif($action == 'addPet') {
	
		$data  = _g();
		$token = _g('token');
		/* 验证  */
		/*   if(!preg_match('/^1[3458][0-9]{9}$/',$phonemob)){
		 $arr = array( 'statusCode'=>1,'msg'=>'手机号码不正确，请重新输入');
		 echo $json->encode($arr); die;
		 } */
		/* 	    if(!preg_match('/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/',$email)){
		 $arr = array( 'statusCode'=>1,'msg'=>'邮箱不正确，请重新输入');
		 echo $json->encode($arr); die;
		 }
		 if(!preg_match('/^[1-9]\d{5}$/',$zipcode)){
		 $arr = array( 'statusCode'=>1,'msg'=>'邮编不正确，请重新输入');
		 echo $json->encode($arr); die;
		 }
		 if(!preg_match('/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/',$phonetel)){
		 $arr = array( 'statusCode'=>1,'msg'=>'电话号码不正确，请重新输入');
		 echo $json->encode($arr); die;
		 }
		 */
		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					)
			);
			echo $json->encode($arr); die;
		}
	
		$arr = array(
				'data'  => $data,
				'token' => $token
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	/*******编辑我的宠物********/
	elseif($action == 'editPet') {
	
		$data  = _g();
		$token = _g('token');
		if(empty($data)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '没有要编辑的内容',
					)
			);
			echo $json->encode($arr); die;
		}
	
		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '用户未登录',
					)
			);
			echo $json->encode($arr); die;
		}
	
		$arr = array(
				'data'  => $data,
				'token' => $token
		);
	
		$rs=getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	/*******删除我的宠物********/
	elseif($action == 'delPet') {
		
		$pet_id     = _g('pet_id');
		$token = _g('token');
		
		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '用户未登录',
					)
			);
			echo $json->encode($arr); die;
		}
	
		if(empty($pet_id)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => '没有要删除的宠物',
					)
			);
			echo $json->encode($arr);
		}
	
		$arr = array(
				'token'   => $token,
				'pet_id' => $pet_id
		);
	
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	/*******我的消息********/
	elseif($action == 'myMessages') {

		$data      = _g();
		$token     = !empty($data->token) ? $data->token : '';
		$pageSize  = !empty($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = !empty($data->pageIndex) ? $data->pageIndex: 1;

		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
	        echo $json->encode($arr);die;
	    }

	    $arr = array(
	        'token'     => $token,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******我的未读消息********/
	elseif($action == 'myUnreadMessages') {
	
		$data      = _g();
		$token     = !empty($data->token) ? $data->token : '';
		
		if(empty($token)) {
			$arr = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 100,
							'msg'       => 'token不能为空',
					)
			);
			echo $json->encode($arr);die;
		}
	
		$arr = array(
				'token'     => $token,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	
	}
	/*******删除我的消息********/
	elseif($action == 'delMyMessages') {

		$data      = _g();
		$token     = !empty($data->token) ? $data->token : '';
		$msg_id    = !empty($data->msg_id) ? $data->msg_id : '';//12，123，5

		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
	        echo $json->encode($arr);die;
	    }
	    if(empty($msg_id)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'msg_id不能为空',
				)
			);
	        echo $json->encode($arr);die;
	    }

	    $arr = array(
	        'token'     => $token,
	        'msg_id'    => $msg_id,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******查看阅读我的消息********/
	elseif($action == 'viewMyMessages') {

		$data      = _g();
		$token     = !empty($data->token) ? $data->token : '';
		$msg_id     = !empty($data->msg_id) ? $data->msg_id : '';

		if(empty($token) || empty($msg_id)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 102,
					'msg'       => '参数错误',
				)
			);
	        echo $json->encode($arr);die;
	    }


	    $arr = array(
	        'token'    => $token,
	        'msg_id'   => $msg_id,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	
	/*******即时聊天消息-获得客服列表********/
	elseif($action == 'chatSeverList')
	{
		$data  = _g();
		$token = !empty($data->token) ? $data->token : '';
		
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
	        echo $json->encode($arr);
	    }
		
		$arr = array(
	        'token'   => $token,
	    );
		
		$rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	
	/*******即时聊天消息-未读信息（用户信鸽推送，直接跳转到对话框）********/
	elseif($action == 'chatSeverOne') {

		$data      = _g();
		$token     = !empty($data->token) ? $data->token : '';
		$serve_id  = !empty($data->serve_id) ? $data->serve_id : '';

		if(empty($token)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
	        echo $json->encode($arr);
	    }
		
		if(empty($serve_id)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '没有当前客服',
				)
			);
	        echo $json->encode($arr);
	    }

	    $arr = array(
	        'token'     => $token,
			'serve_id'  => $serve_id,
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******即时聊天消息-未读信息********/
	elseif($action == 'chatSeverImg') 
	{
		$data = _g();
		$token     = !empty($data->token) ? $data->token : '';
		if (isset($_FILES['chatimg'])) {
			$chatimg = $_FILES['chatimg'];
		} else {
			$chatimg = array();
		}
		
		if(empty($chatimg)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '请选择发送图片',
				)
			);
	        echo $json->encode($arr);die;
	    }
		
		$arr  = array(
			'token'    => $token,
			'chatimg'  => $chatimg,
		);
		
		$rs = getSoapClients($class, $action, $arr);die($rs);
	}
	/*******添加收藏********/
	elseif($action == 'addCollect') {

	    $token   = _g('token');
	    $item_id = _g('item_id');
		$type    = _g('type');//收藏类型
	    $arr=array(
	        'token'   => $token,
	        'item_id' => $item_id,
			'type'    => $type
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******取消收藏********/
	elseif($action == 'delCollect') {
	    $token   = _g('token');
	    $item_id = _g('item_id');
		$type    = _g('type');
	    $arr=array(
	        'token'   => !empty($token) ? $token : '',
	        'item_id' => !empty($item_id) ? $item_id : 0,
			'type'    => !empty($type) ? $type : 'single',
	    );
	    $rs=getSoapClients($class, $action, $arr);die($rs);

	}
	/*******获得用户信息********/
	elseif($action == 'getUserInfo') {
	    $token = _g('token');

	    $arr = array(
	        'token' => $token
	    );

	    $rs=getSoapClients($class, $action, $arr);die($rs);

	}
	/*获得职业分类    待定*/
	elseif ($action == 'getCate')
	{
		$data= _g();
		if($data){
			$parentId = $data->parentId;
		}
		$arr = array(
				'parentId' => $parentId,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*获得职业分类对应的表单    待定*/
	elseif ($action == 'getCateTemplate')
	{
		$data= _g();

		$arr = array(
				'data'    => $data,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*修改职业分类   待定*/
	elseif ($action == 'editCate')
	{
		$data= _g();
		if($data){
			$cateId = $data->cateId;
			$token = $data->token;
		}
		$arr = array(
				'cateId' => $cateId,
				'token'    => $token,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******修改会员的个人资料    待定********/
	elseif ($action == 'editMemInfo')
	{
		$data= _g();
		if (isset($_FILES['photoUrl']))
		{
			$photoUrl = $_FILES['photoUrl'];
		}
		else
		{
			$photoUrl = '';
		}
		$arr = array(
				'data'    => $data,
				'photoUrl' => $photoUrl,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******获取样衣推荐列表-套装********/
	elseif($action == 'recomStyle') 
	{
		$data = _g();
		$recom_id   = isset($data->recom_id) ? $data->recom_id : 0;
		$cate       = isset($data->cate) ? $data->cate : 0;// 0:全部  -1 ：套装
		$pageSize   = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : 1;
		
	    $arr = array(
			'recom_id'  => $recom_id,
			'cate'      => $cate,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
		);
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******获取样衣推荐列表-套装********/
	elseif($action == 'recomDisser') 
	{
		$data = _g();
		$pageSize   = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex  = isset($data->pageIndex) ? $data->pageIndex : 1;
		
	    $arr = array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
		);
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******获取样衣推荐列表-基本款********/
	elseif($action=='recommendSigle')
	{
		$data = _g();
		$type = isset($data->type) ? $data->type : 3;
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
		
	    $arr = array(
			'type'      => $type,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
		);
		
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******根据分类获得对应列表********/
	elseif($action=='recommendList')
	{
		$data = _g();
		$type      = isset($data->type) ? $data->type : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
		
	    $arr = array(
			'type'      => $type,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
		);
		
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******获取主题排序 待定********/
	elseif($action == 'getKeyword'){
	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******获取套系列样衣列表********/
	elseif ($action == 'getDissertationList') {
		$data      = _g();
		$sorting   = isset($data->sorting) ? $data->sorting : 1;
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;

	    $arr = array(
	        'sorting'   => $sorting,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
	    );
	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******获样衣分类********/
	elseif($action == 'getCustomeCate') {
	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******DIY下订-分类********/
	elseif($action == 'getDiyCate') {
		$data      = _g();
		$token   = isset($data->token) ? $data->token : '';
	    $arr = array(
			'token'   => $token,
		);
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******获取某类别下的单件样衣列表********/
	elseif($action == 'getAllCustomList') {
		$data      = _g();
		$cate      = !empty($data->cate) ? $data->cate : 0;
		$part_arr1 = !empty($data->part_arr1) ? $data->part_arr1 : 0;//花型
		$part_arr2 = !empty($data->part_arr2) ? $data->part_arr2 : 0;//成分
		$part_arr3 = !empty($data->part_arr3) ? $data->part_arr3 : 0;//面料
		$part_arr4 = !empty($data->part_arr4) ? $data->part_arr4 : 0;//面料
		$cstSex    = !empty($data->cstSex) ? $data->cstSex : 1;//1：男装 2：女装
		$order     = !empty($data->order) ? $data->order : 1;
		$min_price = !empty($data->min_price) ? $data->min_price : 0;//最小价格
		$max_price = !empty($data->max_price) ? $data->max_price : 9999999;//最大价格
		$pageSize  = !empty($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1;

		if (empty($cate)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '参数错误',
				)
			);
	        echo $json->encode($arr); die;
	    }

	    $arr = array(
	        'cate'      => $cate,
			'part_arr1' => $part_arr1,
			'part_arr2' => $part_arr2,
			'part_arr3' => $part_arr3,
			'part_arr4' => $part_arr4,
			'cstSex'    => $cstSex,
	        'max_price' => $max_price,
			'min_price' => $min_price,
			'order'     => $order,
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******套装详细界面********/
	elseif($action == 'getGoodsDetail') {
	    $id  = _g('id');
	    $arr = array(
	        'id' => $id
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******基本款详细********/
	elseif($action == 'getCustomsDetail'){

	    $id  = _g('id');
	    $arr = array(
	        'id' => $id
	    );

	    $rs = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******首页广告轮播图********/
	elseif($action == 'homePage') {
	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******推荐裁缝列表********/
	elseif($action == 'storeRecommend') {
	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);

	}
	/*******裁缝--我的口碑********/
	elseif($action == 'myMouth') {
	    $token = _g('token');
	    $arr = array(
	        'token' => $token,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******裁缝--我的案例********/
	elseif($action == 'myCase') {
	    $token = _g('token');
	    $arr = array(
	        'token' => $token,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******获得 即拍即做 相册信息********/
	elseif($action == 'followedBeatList') {
		$data   = _g();
		$token      = isset($data->token) ? $data->token : '';
		$pageSize  = !empty($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1;
		
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
	        'token'      => $token,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}
	/*******即拍即做 信息提交保存********/
	elseif($action == 'addfollowedBeat') {
		$data   = _g();
		$token      = isset($data->token) ? $data->token : '';
		$desc       = isset($data->desc) ? $data->desc : '';
		$beatImages = isset($data->beatImages) ? $data->beatImages : '';//使用“,”隔开图片名称

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
		
		if(empty($beatImages)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '上传图片不能为空！',
				),
			);
	        echo $json->encode($arr); die;
	    }
		
	    $arr = array(
	        'token'      => $token,
			'desc'       => $desc,
			'beatImages' => $beatImages,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}
	/*******即拍即做 获得即拍即做相册详情********/
	elseif($action == 'followedBeatView') {
		$data   = _g();
		$token = isset($data->token) ? $data->token : '';
		$pid   = isset($data->pid) ? $data->pid : '';
		
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
	        'token'      => $token,
			'pid'        => $pid,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}
	/*******即拍即做 获得即拍即做添加回复********/
	elseif($action == 'addfollowedComments') {
		$data   = _g();
		$token   = isset($data->token) ? $data->token : '';
		$content = isset($data->content) ? $data->content : '';
		$pid     = isset($data->pid) ? $data->pid : '';
		
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
	        'token'      => $token,
			'content'    => $content,
			'pid'        => $pid,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}
	/*******即拍即做 获得即拍即做的时时聊天********/
	elseif($action == 'followedBeatComment') {
		$data   = _g();
		$token = isset($data->token) ? $data->token : '';
		$pid   = isset($data->pid) ? $data->pid : '';
		$pageSize  = !empty($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1;
		
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
	        'token'      => $token,
			'pid'        => $pid,
			'pageSize'   => $pageSize,
			'pageIndex'  => $pageIndex,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}
	/*******即拍即做 上传图片********/
	elseif($action == 'uploadBeatImg') {
		$data      = _g();
		$token     = isset($data->token) ? $data->token : '';
		$beatImage = $_FILES["beatImage"];
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
		/*
		if(empty($beatImage)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '请选择上传图片',
				),
			);
	        echo $json->encode($arr); die;
	    }*/
		//上传
		$user_info   = getToken($token);
		if(empty($user_info)) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '未找到此用户标识',
				)
			);
	        echo $json->encode($return);die;
		}

		import('uploader.lib');
        $uploader = new Uploader();
		$type = $beatImage['extension'];
        //$uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($beatImage);
        if ($uploader->file_info() === false) {
            return false;
        }

        $uploader->root_dir(ROOT_PATH);
        $rndname  = $uploader->random_filename();
        $uploader->save('/upload_user_photo/shaidan/520x685', $user_info['user_id'].'_'.$rndname);
        $file_arr = $uploader->file_info();
		
		if(empty($file_arr)) {
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
	        'token'     => $token,
			'beatImage' => $type.'_'.$user_id.'_'.$rndname.'.'.$file_arr['extension'],
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
	}

	/*******即拍即做 删除相册********/
	elseif($action == 'delBeatImg') {
		$data     = _g();
		$token    = isset($data->token) ? $data->token : '';
		$deltype  = isset($data->deltype) ? $data->deltype : 'photo';//删除类型。photo：删除整个相册；img：删除相册中某张图片
		$photoId  = isset($data->photoId) ? $data->photoId : 0;//相册id
		$imgId    = isset($data->imgId) ? $data->imgId : 0;//图片id
		
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
		
		if(empty($photoId) || ($deltype=='img' && $imgId==0)) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '参数错误',
				),
			);
	        echo $json->encode($arr); die;
	    }
		
		$arr = array(
	        'token'   => $token,
			'deltype' => $deltype,
			'photoId' => $photoId,
			'imgId'   => $imgId,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);
		
	}
	/*******获得某裁缝的案例********/
	elseif($action == 'getStoreCase') {
	    $store_id = _g('store_id');
	    $arr = array(
	        'store_id' => $store_id,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******获得某裁缝的口碑********/
	elseif($action == 'getStoreMouth') {
	    $store_id = _g('store_id');
	    $arr = array(
	        'store_id' => $store_id,
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}
	/*******获取评论中心列表********/
	elseif($action == 'getCommentList'){
		$data            = _g();
		$token    = isset($data->token) ? $data->token : '';
		$comment        = isset($data->comment) ? $data->comment: 0;//商品评价状态 0待评价 1已评价
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
		
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
	
		$arr  = array(
			'token'   => $token,
			'comment' => $comment,
			'pageSize' => $pageSize,
			'pageIndex'   => $pageIndex,
	
		);
		$rs = getSoapClients($class, $action, $arr); die($rs);
	
	}
	/*******消费者添加评价********/
	elseif($action == 'addComment') {
		$data            = _g();
		$token           = isset($data->token) ? $data->token : '';//用户标识
		$order_id        = isset($data->order_id) ? $data->order_id : '';//订单id
		$pro_id          = isset($data->pro_id) ? $data->pro_id : 0;//被评论的商品id
		$cate            = isset($data->cate) ? $data->cate : '';//类型custom-尚品,fdiy-定制 
		$is_hide         = isset($data->is_hide) ? $data->is_hide : 0;//0-不匿名；1-匿名
		$star   = isset($data->star) ? $data->star : '';//服务评星
		$content         = isset($data->content) ? $data->content : '';//评论内容
		$rec_id          = isset($data->rec_id) ? $data->rec_id : '';//评论订单商品表id，如果是定制，为0
		$impress    =isset($data->impress)?$data->impress:'';//评价选定标签

		if (!$token || !$cate || !$order_id) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 102,
					'msg'       => '参数错误',
				),
			);

			echo $json->encode($arr);die;
		}
		
		if ( !$star) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 102,
					'msg'       => '请对我们商品进行评分',
				),
			);

			echo $json->encode($arr);die;
		}
		
		if (!$content) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 102,
					'msg'       => '评论的内容不能为空',
				),
			);

			echo $json->encode($arr);die;
		}

	    $arr = array(
	        'token'           => $token,
			'order_id'        => $order_id,
			'pro_id'          => $pro_id,
			'cate'            => $cate,
			'is_hide'         => $is_hide,
			'star'   => $star,
			'content'         => $content,
			'rec_id'          => $rec_id,
	    	'impress'    =>$impress
	    );

	    $rs = getSoapClients($class, $action, $arr); die($rs);

	}	
	/*******商品评论页的印象标签********/
	elseif($action == 'getImpressionArray'){
	    $arr = array();
	    $rs  = getSoapClients($class, $action, $arr);
	    die($rs);
	}
	/*******获取商品评论********/
	elseif($action == 'getComment'){
		$data            = _g();
		$rec_id          = isset($data->rec_id) ? $data->rec_id : 0;//被评论的商品id 如果是套装 默认 是 最后一个商品的id 
		
		if ( !$rec_id) {
			$arr = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 102,
					'msg'       => '参数错误',
				),
			);

			echo $json->encode($arr);die;
		}
		
		$arr  = array(
			'rec_id'=>$rec_id,
		
		);
		$rs = getSoapClients($class, $action, $arr); die($rs);
		
	}
	/*添加项目   待定*/
	elseif ($action == 'addProject') {
		$data= _g();
		if($data)
		{
			$token = $data->token;
			$name = $data->name;
			$time = $data->time;
		}
		if (isset($data->role))
		{
			$role = $data->role;
		}
		else
		{
			$role = '';
		}

		if ($_FILES['workImg'])
		{
			$workImg = $_FILES['workImg'];
		}
		else
		{
			$workImg = array();
		}

		$arr = array(
				'token'    => $token,
				'name'     => $name,
				'time'     => $time,
				'work_img' => $workImg,
				'role'     => $role,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}

	/*添加动态 待定*/
	elseif ($action == 'addDynamic')
	{
		$data= _g();
		if($data)
		{
			$token = $data->token;
			$name = $data->name;
		}
		if (isset($data->role))
		{
			$role = $data->role;
		}
		else
		{
			$role = '';
		}

		if ($_FILES['workImg'])
		{
			$workImg = $_FILES['workImg'];
		}
		else
		{
			$workImg = array();
		}

		$arr = array(
				'token'    => $token,
				'name'     => $name,
				'work_img' => $workImg,
				'role'     => $role,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}

	/*项目列表  待定*/
	elseif ($action == "projectList")
	{
		$data= _g();
		if($data)
		{
			$userId = $data->userId;
			$type  = $data->type;
			$pageSize = $data->pageSize;
			$pageIndex = $data->pageIndex;
			$token     = $data->token;
		}

		$arr = array(
				'userId'    => $userId,
				'type'     => $type,
				'pageSize' => $pageSize,
				'pageIndex' => $pageIndex,
				'token'   => $token,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}

	/*项目列表*/

	/*检查会员是否具备认证条件 待定*/
	elseif ($action == 'checkUserAuth')
	{
		$data= _g();
		if($data)
		{
			$token = $data->token;
		}

		$arr = array(
				'token'    => $token,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*******我的关注裁缝列表********/
	elseif ($action == 'follow_list')
	{
		$data      = _g();
		$token     = isset($data->token) ? $data->token : '';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageSize : 1;
		$token     = $data->token;
		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
			echo $json->encode($arr);die;
		}

		$arr = array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
			'token'     => $token,
		);

		$rs = getSoapClients($class, $action, $arr);
		die($rs);

	}
	/*******我的收藏列表********/
	elseif ($action == 'getCollect')
	{
		$data      = _g();
		$token     = isset($data->token) ? $data->token : '';
		$type      = isset($data->type) ? $data->type : 'single';
		$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
		$pageIndex = isset($data->pageIndex) ? $data->pageSize : 1;

		if (!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token不能为空',
				)
			);
			echo $json->encode($arr);die;
		}

		$arr = array(
			'pageSize'  => $pageSize,
			'pageIndex' => $pageIndex,
			'token'     => $token,
			'type'      => $type,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);

	}
	/*添加认证图片（手持身份证） 待定*/
	elseif ($action == 'addAuthImg')
	{
		$data= _g();
		if($data)
		{
			$token = $data->token;
		}

		if (!$_FILES['authImg'])
		{
			$arr = array('statusCode'=>1,'msg'=>'authImg not empty');
			echo $json->encode($arr); die;
		}

		$arr = array(
				'token'    => $token,
				'authImg'  => $_FILES['authImg'],
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*添加认证的辅助信息 此操作必须是在添加照片之后  待定*/
	elseif ($action == 'authAssist')
	{
		$data= _g();
		$arr = array(
				'data'    => $data,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*返回用户的认证状态  待定*/
	elseif ($action == 'authStatus')
	{
		$data= _g();
		$token = $data->token;
		$arr = array(
				'token'    => $token,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*获得useri的二维码图片路径 待定*/
	elseif ($action == 'qrcode')
	{
		$data= _g();
		$userId = $data->userId;
		$arr = array(
				'userId'    => $userId,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/*意见反馈 */
	elseif ($action == 'feedback')
	{
		$data= _g();
		$token    = isset($data->token) ? $data->token : '';
		$content  = isset($data->content) ? $data->content : '';
		
		$arr = array(
			'token'    => $token,
			'content'  => $content,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	/**测试  app信鸽消息推送（可删除）**/
	elseif ($action == 'toStoreXinApp')
	{
		$db=db();
		global $json;
		$addtime = !empty($data->addtime) ? $data->addtime : '';
		$type    = !empty($data->type) ? $data->type : '';
		
		$arr = array(
			'addtime' => $addtime,
			'type'    => $type,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}
	
	/**创业培训 文章列表**/
	elseif ($action == 'article')
	{
		$data= _g();
		$pageSize   = !empty($data->pageSize) ? $data->pageSize : 10 ;
        $pageIndex  = !empty($data->pageIndex) ? $data->pageIndex : 1 ;
		$arr = array(
			'pageSize' => $pageSize,
            'pageIndex'=> $pageIndex,
		);
		$rs = getSoapClients($class, $action, $arr);
		die($rs);
	}

    /**特权码**/
    elseif ($action == 'tequan')
    {
        $data= _g();

        $token    = isset($data->token) ? $data->token : '';
        $sn = !empty($data->sn) ? $data->sn : '';

        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }


        $arr = array(
            'token'=>$token,
            'sn' => $sn,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    /*特权码明细*/
    elseif ($action == 'tequan_info')
    {
        $data= _g();
        $token    = isset($data->token) ? $data->token : '';

        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }

        $arr = array(
            'token'=>$token,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }


    /**kuka**/
    elseif ($action == 'kuka')
    {
        $data= _g();

        $token    = isset($data->token) ? $data->token : '';
        $sn = !empty($data->sn) ? $data->sn : '';

        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }


        $arr = array(
            'token'=>$token,
            'sn' => $sn,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    /**kuka**/
    elseif ($action == 'kuka_v2')
    {
        $data= _g();
    
        $token    = isset($data->token) ? $data->token : '';
        $sn = !empty($data->sn) ? $data->sn : '';
    
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }
    
    
        $arr = array(
            'token'=>$token,
            'sn' => $sn,
        );
    
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    /*kuka明细*/
    elseif ($action == 'kuka_info')
    {
        $data= _g();
        $token    = isset($data->token) ? $data->token : '';
        $type     = isset($data->type)  ? $data-> type : '100';
        $pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
        $pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
        
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }

        $arr = array(
            'token'=>$token,
            'type' =>$type,
            'pageSize'   => $pageSize,
            'pageIndex'  => $pageIndex,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    // 我的麦券
    elseif ($action == 'debit_info')
    {
        $data= _g();
        $token    = isset($data->token) ? $data->token : '';
        $type     = isset($data->type)  ? $data-> type : '100';
        $pageSize   = isset($data->pageSize) ? intval($data->pageSize) : 10;
        $pageIndex  = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
    
        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }
    
        $arr = array(
            'token'=>$token,
            'type' =>$type,
            'pageSize'   => $pageSize,
            'pageIndex'  => $pageIndex,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif ($action == 'myKucard')
    {
        $data= _g();
        $token    = isset($data->token) ? $data->token : '';

        if(!$token){
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => 'token不能为空',
                ));
            echo $json->encode($arr);die;
        }

        $arr = array(
            'token'=>$token,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
	
	elseif ($action == 'kukaDonation')
    {
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
		$spcode      = isset($data->spcode) ? $data->spcode : '';
		$to_userid = isset($data->to_userid) ? $data->to_userid : '';
				
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100, 
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		if(!$spcode || !$to_userid) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 103, 
					'msg'       => '参数错误'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		$arr  = array(
	        'token'     => $token,
			'spcode'      => $spcode,
			'to_userid' => $to_userid,
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    }elseif($action == 'getpartinfo')
    {
    	$data = _g();
		$code     = isset($data->code) ? $data->code : '';
		$cllk     = isset($data->callback) ? $data->callback : '';

				
		if(!$code) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 107, 
					'msg'       => '面料编号为空!'
				),
			);
		    echo $json->encode($arr);die;
        }
    	$arr  = array(
	        'code'     => $code,
	        'cllk'     => $cllk,
			
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    	
    
    }
	
	elseif ($action == 'kukalist')
    {
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
				
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100, 
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		
		$arr  = array(
	        'token'     => $token,
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif ($action == 'buy_kuka')
    {
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
				
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100, 
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		
		$arr  = array(
	        'token'     => $token,
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
	elseif ($action == 'kubilist')
    {
		$data = _g();
		$token     = isset($data->token) ? $data->token : '';
				
		if(!$token) {
			$arr = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100, 
					'msg'       => '账号不存在'
				),
			);
		    echo $json->encode($arr);die;
        }
		
		
		$arr  = array(
	        'token'     => $token,
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif ($action == 'getpartinfo')
    {
		$data = _g();
		$code     = isset($data->code) ? $data->code : '';
	    $callback = $data->callback ? $data->callback :'';
	

		
		
		$arr  = array(
	        'code'     => $code,
		    'callback' =>$callback,
		    
	    );
		$rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif($action=='fx'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';

        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '账号不存在'
                ),
            );
            echo $json->encode($arr);die;
        }



        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '订单号不能为空!'
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr  = array(
            'token'     => $token,
            'order_id'  => $order_id,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }


    elseif($action=='fx_order_status'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';




        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '账号不存在'
                ),
            );
            echo $json->encode($arr);die;
        }



        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '订单号不能为空!'
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr  = array(
            'token'     => $token,
            'order_id'  => $order_id,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }
    elseif($action=='fx_add'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';
        $wl_sn     = isset($data->wl_sn) ? $data->wl_sn : '';
        $reason     = isset($data->reason) ? $data->reason : '';
        $to_time     = isset($data->to_time) ? $data->to_time : '';


        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '账号不存在'
                ),
            );
            echo $json->encode($arr);die;
        }




        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '订单号不能为空!'
                ),
            );
            echo $json->encode($arr);die;
        }

        if($reason == '') {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '请填写返修原因!'
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr  = array(
            'token'     => $token,
            'order_id'     => $order_id,
            'wl_sn'     => $wl_sn,
            'reason'     => $reason,
            'to_time'     => $to_time,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }

    elseif($action=='fx_info'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';



        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '账号不存在'
                ),
            );
            echo $json->encode($arr);die;
        }




        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '订单号不能为空!'
                ),
            );
            echo $json->encode($arr);die;
        }


        $arr  = array(
            'token'     => $token,
            'order_id'     => $order_id,

        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }

    elseif($action=='order_serve_status'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';



        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '用户不存在'
                ),
            );
            echo $json->encode($arr);die;
        }




        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '订单号不能为空!'
                ),
            );
            echo $json->encode($arr);die;
        }


        $arr  = array(
            'token'     => $token,
            'order_id'     => $order_id,

        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif($action=='fx_status_list'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';




        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '用户不存在'
                ),
            );
            echo $json->encode($arr);die;
        }






        $arr  = array(
            'token'     => $token,


        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }

    elseif($action=='qqfabriclist'){
        $data = _g();

        $arr  = array(
            'data'=>$data,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }
    elseif($action=='qqfabricSelect'){
        $data = _g();

        $region_id     = isset($data->region_id) ? $data->region_id : '';
        if(!$region_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '无效的产地id'
                ),
            );
            echo $json->encode($arr);die;
        }


        $arr  = array(
            'region_id'     => $region_id,
        );

        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif($action=='qqfabric'){
        $arr  = array();
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif($action=='qqsuitList'){

        $data = _g();
        $arr  = array(
            "data"=>$data,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }

    elseif($action=='fx_receive'){
        $data = _g();
        $token     = isset($data->token) ? $data->token : '';
        $order_id     = isset($data->order_id) ? $data->order_id : '';




        if(!$token) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '用户不存在'
                ),
            );
            echo $json->encode($arr);die;
        }




        if(!$order_id) {
            $arr = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '订单号不能为空'
                ),
            );
            echo $json->encode($arr);die;
        }




        $arr  = array(
            'token'     => $token,
            'order_id'     => $order_id,


        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }

    elseif($action=='chose_figure_help'){
        $arr  = array();
        $rs = getSoapClients($class, $action, $arr);
        die($rs);
    }
    elseif($action=='myAddrList'){
    	$data = _g();
    
    	$arr  = array(
    			'data'=>$data,
    	);
    	$rs = getSoapClients($class, $action, $arr);
    	die($rs);
    
    }elseif($action=='addMyAddress'){
        $data = _g();

        $arr  = array(
            'data'=>$data,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }elseif($action=='editMyAddress'){
        $data = _g();

        $arr  = array(
            'data'=>$data,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }elseif($action=='dropMyAddress'){
        $data = _g();

        $arr  = array(
            'data'=>$data,
        );
        $rs = getSoapClients($class, $action, $arr);
        die($rs);

    }
    elseif($action=='regionlist'){
    	$data = _g();
    
    	$arr  = array(
    			'data'=>$data,
    	);
    	$rs = getSoapClients($class, $action, $arr);
    	die($rs);
    
    }elseif($action=='articleContent'){
    	$data = _g();
    
    	$arr  = array(
    			'data'=>$data,
    	);
    	$rs = getSoapClients($class, $action, $arr);
    	die($rs);
    
    }
    
    else
	{
		echo "Lack of method ?action";
	}


?>