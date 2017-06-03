<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	//设定允许进行操作的action数组
	$class   = 'Tang';
	$act_arr = array('login','register','editCate','getCate','getCateTemplate','addProject','checkUserAuth','getUserBaseInfo','editMemInfo','addAuthImg','authAssist','subChance','sendCode','checkCode','projectList','addDynamic','authStatus','qrcode','test');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';

	//http://local.rc.com/soaapi/soap/user.php?act=modifyPassWord&oldPassWord=123456&newPassWord=admin123 示例

	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
		//return false;
		//返回false
	}

	/*******普通会员登录********/
	if ($action == 'test')
	{	
		$data = _g();
		$token = $data->token;
		$arr  =  array(
			'token' => $token,
			);
		
		
	}elseif($action == 'activepublic'){
        
        $data = _g();
        $arr  = array(
            'data' => $data
            );
    }elseif($action == 'activegoods'){
        
        $data = _g();
        $arr  = array(
            'data' => $data
            );
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
		
		if (isset($_FILES['avatar']))
	    {
	        $avatar = $_FILES['avatar'];
	        
	    }
	    else
	    {
	        $avatar = '';
	    }

	    $arr  = array(
	        'data' => $data,
	    	'avatar'=>$avatar
	    );
	   
	}elseif($action == 'real_auth'){
	
	    $data = _g();
	    $token        = isset($data->token) ? $data->token : '';
	    $real_name    = isset($data->real_name) ? $data->real_name : '';//真实姓名
        $im_weixin    = isset($data->im_weixin) ? $data->im_weixin : '';//微信
        $email        = isset($data->email) ? $data->email : '';//邮箱
        $address      = isset($data->address) ? $data->address : '';//现居住地址
	    $bank         = isset($data->bank) ? $data->bank : '';//选择银行
	    $bank_id      = isset($data->bank_id) ? $data->bank_id :'';//银行id
	    $bank_address = isset($data->bank_address) ? $data->bank_address : '';//开户行地址
	    $bank_card    = isset($data->bank_card) ? $data->bank_card : '';//银行卡号
	    $code         = isset($data->code) ? $data->code : '';//验证码
	   
	    
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
	    
	 if(!$real_name){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入真实姓名'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
        
	   if(!$bank){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入开户行'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	   if(!$bank_address){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入开户行地址'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	    
	   if(!$bank_card){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入银行卡卡号'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	  if(!$code){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入验证码'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	 
	    
	    $arr = array(
	        'token'         => $token,
	        'real_name'     => $real_name,
            'im_weixin'     => $im_weixin,
            'email'         => $email,
            'address'       => $address,
	        'bank'          => $bank,
	        'bank_id'       => $bank_id,
	        'bank_address'  => $bank_address,
	        'bank_card'     => $bank_card,
	        'code'          => $code,
	     
	    );
	    
	}  /*******放弃认证申请********/
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
     
        $real_dir  = ROOT_PATH."/upload/auth/";
        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
        $imageTool = new ImageTool();
        $uniqueString = $imageTool->getUniqueString();
        $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($card_face['name'], '.');
        $sub_dir      = date('Ym');
        if(!is_dir($real_dir.$sub_dir)) {
            mkdir($real_dir.$sub_dir, 0777, true);
        }
        
        
       	$pic = file_put_contents($real_dir.$sub_dir.'/'.$new_name, file_get_contents($card_face['tmp_name']));
        if(!$pic) {
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
            'card_face' =>'upload/auth/'.$sub_dir.'/'.$new_name,
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
     
        //保存图片
        $real_dir  = ROOT_PATH."/upload/auth/";
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
    }elseif($action == 'real_auth_list'){
        
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
        
    }elseif ($action == 'bank') {
        $data = _g();

        $arr = array(
            'data' => $data,
        );

    }/*******列出我推荐安装app用户********/
	elseif ($action == 'getInviteUser')
	{	
		$data = _g();
		$token     = isset($data->token) ? $data->token : ''; 
		$fliter     = isset($data->fliter) ? $data->fliter : '';//过滤条件，不传为所有用户，用户名或手机号  模糊查询
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
			'fliter'    => $fliter,
			'pageSize'  => $pageSize,
            'pageIndex' => $pageIndex,

		);

		
	}/*******我的邀请人********/
    else if($action=='addinviter')
    {   
        $data = _g();
        $token   = isset($data->token) ?  $data->token:0;
        $invite = isset($data->invite) ?  $data->invite:0;
        if (!$token) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '用户未登录',
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr=array(
            'token'   => $token,
        	'invite'  =>$invite
           
        );

    }/*******我的量体数据********/
    else if($action=='getFigurebyuser')
    {   
        $data = _g();
        $token   = isset($data->token) ?  $data->token:0;
    
        if (!$token) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '用户未登录',
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr=array(
            'token'   => $token,
        
           
        );

    }/*******消费者的量体数据详情-新需求********/
    else if($action=='getFigurebyuserList')
    {   
        $data = _g();
        $token   = isset($data->token) ?  $data->token:0;
    
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
        );

    }else if($action == 'getUserInfo'){
    	
    	$data = _g();
    	$token   = isset($data->token) ?  $data->token:0; 
    	
        if (!$token) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 102,
                    'msg'       => '用户未登录',
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr=array(
            'token'   => $token,
        
           
        );
    	
    	
    	
    	
    } else if($action=='cyzCommendrule')
    {   
        $data = _g();
    	$id   = isset($data->id) ?  $data->id : '';
        if (!$id) {
            $arr = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '文章不存在！',
                ),
            );
            echo $json->encode($arr);die;
        }

        $arr=array(
        	'id'      => $id
         
           
        );

    }elseif($action =='figureComment'){
    	$data = _g();
    	$arr = array(
			'data'     => $data,

		);
    	
    }elseif($action == 'getfigureinfo'){
    	
    	$data = _g();
		$token     = isset($data->token) ? $data->token : '';
	
		
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
		

		);
    	
    	
    }elseif($action == 'cyz_auth'){
	
	    $data = _g();
	 
	   /* if (!$token) {
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 100,
	                'msg'       => 'token错误',
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	    //!$real_name || !$bank || !$bank_address || !$bank_card || !$code || !$card_back_img || !$card_face_img
	    if(!$real_name){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入真实姓名'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	   if(!$bank){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入开户行'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	   if(!$bank_address){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入开户行地址'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	    
	   if(!$bank_card){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入银行卡卡号'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	  if(!$code){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请输入验证码'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	  if(!$card_back_img){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请上传身份证反面'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }
	    
		 if(!$card_face_img){
	        $arr = array(
	            'statusCode' => 0,
	            'error'      => array(
	                'errorCode' => 101,
	                'msg'       => '请上传身份证正面'
	            ),
	        );
	        echo $json->encode($arr);die;
	    }*/
/*	    $arr = array(
	        'token'         => $token,
	        'real_name'     => $real_name,
	        'bank'          => $bank,
	        'bank_id'       => $bank_id,
	        'bank_address'  => $bank_address,
	        'bank_card'     => $bank_card,
            'mobile'        => $mobile,
	        'code'          => $code,
	        'card_face_img' => $card_face_img,
	        'card_back_img' => $card_back_img,
            'province'      => $province,
            'city'          =>$city,
            'region_id'     => $region_id,
            'address'       =>$address
	    );*/
        $arr = array(
            'data'         => $data,
           
        );
	    
	}elseif($action == 'region'){
    	
    	$data = _g();
		$arr  = array(
			'data' => $data
			);
    }
    elseif($action == 'cyz_auth_list'){
        
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
	
	
	else
	{
		echo "Lack of method ?action";
	}

	$rs = getSoapClients($class, $action, $arr);
	die($rs);



?>