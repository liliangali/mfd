<?php
	/**
	 * 
	 * @author tangshoujian
	 * @version v1
	 * @copyright Copyright 2014 figure.com
	 * @package app
	 */
use Cyteam\Goods\Goods;
use Cyteam\Goods\Gcategory;
	class Tang
	{
		var $return = array();
		var $json;
		var $real_dir = '';
        var $_auth_web_img = '';
     
		function __construct()
		{
			global $json;
			$this->json = $json;
			//=====返回结果=====
			$this->return['statusCode'] = 0;
			$this->return['msg']             = '';
			$this->real_dir = ROOT_PATH.'/';
			$this->_auth_web_img = SITE_URL."/upload/auth/";
		}
		

		
		/**
		*测试接口
		*@author liang.li <1184820705@qq.com>
		*@2015年4月29日
		*/
	public 	function test($token) 
		{
		   echo $token;die;
		    
		}
		
	public  function getUserInfo($token){
		
		global $json;
        $m  = &m('member');
        $return = array();
        
		$user_info = getToken($token);
		$user_info['erweima_url'] = EXAMPLE_TMP_SERVERPATH.'/'.$user_info['erweima_url'];
		$user_info['avatar'] = LOCALHOST1.'/'.$user_info['avatar'];
	    if (!$user_info) {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '用户不存在',
                )
            );
            return $json->encode($return);
        }
        
         $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '用户资料获取成功',
         			'user_info'=>$user_info
                )
            );
          return $json->encode($return);
        
	
	
	
	}

	    /**
     * 编辑用户资料
     *
     * @param array $data 用户资料参数
     *
     * @access protected
     * @author tangshoujian <963830611@qq.com>
     * @return void
     */
    public function setUserInfo($data,$avatar)
    {
        global $json;
        $m  = &m('member');
        $return = array();
        $token  = $data->token;
        $user_info = getToken($token);
        
     
        if (!$user_info) {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '用户不存在',
                )
            );
            return $json->encode($return);
        }
        
        
        $user_id = $user_info['user_id'];
        
  
        if($user_id) {
        	
                if($avatar){
        
        		$photoOrg = $userinfo['avatar'];
                $real_dir  = ROOT_PATH."/upload/avatar/";
                include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
                $imageTool = new ImageTool();
                $uniqueString = $imageTool->getUniqueString();
                $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($avatar['name'], '.');
                $sub_dir      = date('Ym');
                if(!is_dir($real_dir.$sub_dir)) {
                    mkdir($real_dir.$sub_dir, 0777, true);
                }
               
                
               //$pic = move_uploaded_file($avatar["tmp_name"], $real_dir.$sub_dir.'/'.$new_name);
             
               $pic = file_put_contents($real_dir.$sub_dir.'/'.$new_name, file_get_contents($avatar['tmp_name']));
             if(!$pic){
             
              $return = array(
            'statusCode' => 0,
            'error' => array(
                'errorCode' => 105,
                'msg'       => '图片上传失败',
            )
        );
        		return $json->encode($return);
          }
           
                
                $is_success = $m->edit($user_id, array(
                    'avatar' =>'/upload/avatar/'.$sub_dir.'/'.$new_name,
                ));
                
    }
        	
        	
        
        	$email   	= isset($data->email) ? $data->email : '';//邮箱
            $nickname   = isset($data->nickname) ? $data->nickname : '';//昵称
            $im_qq    	= isset($data->im_qq) ? $data->im_qq : '';//qq
            $birthday   = isset($data->birthday) ? $data->birthday : '';//msn
         
            $user_data       =array();
            
            if($nickname) {
                $user_data['nickname'] = $nickname;
            }
            if($email) {
                $user_data['email'] = $email;
            }
            if($im_qq){
            	$user_data['im_qq'] = $im_qq;
            }
            if($birthday) {
                $user_data['birthday'] = $birthday;
            }
         
        
      

    
            $res       = $m->edit("user_id  = '$user_id'", $user_data);
       
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '用户资料修改成功',
            		'im_qq'   =>$im_qq,
            		'data'    =>$user_data,
            		'path'    =>$real_dir,
                )
            );
            return $json->encode($return);

        }
    }
        /**
         * 实名认证
         * @version 1.0.0
         * @author tangshoujian <280181131@qq.com>
         * @2015-4-27
         */
        
        public function real_auth($token,$real_name,$im_weixin,$email,$address,$bank_id,$bank,$bank_address,$bank_card,$code){
            global $json;
            $member_mod = m('member');
            $memberlv_mod = m('memberlv');
            $return = array();
            $user_info = getToken($token);
        
            
            if(empty($user_info)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = 'token错误';
                return $json->encode($return);
            }
            $user_id  = $user_info['user_id'];
            $auth_mod   = m('auth');
            $phone = $user_info['phone_mob'];

            if ($auth_mod->get("user_id=$user_id "))
            {  
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您已提交审核!请勿重复提交';
                return $json->encode($return);
            }
            $auth = $auth_mod->get("user_id=$user_id AND status=2");
    /*        if ($auth)
            {
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您的认证信息审核失败';
                $return['error']['reason']    = $auth['fail_reason'];
                return $json->encode($return);
            }*/
            $sms_mod    = m('SmsRegTmp');
          
          
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$phone}'  AND type = 'auth'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                
             
                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
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
                            'errorCode' => 105,
                            'msg' => '验证码不正确',
                            'code'=> $code,
                            'res' =>$res,
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg' => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }
            
            $data['user_id']         = $user_id;
            $data['bank']            = $bank;
            $data['status']            = 1;
            $data['bankcard_address'] = $bank_address;
            $data['bankcard']        = $bank_card;
            $data['realname']        = $real_name;
            $data['last_update_time']= gmtime();
            $data['im_weixin']       = $im_weixin;
            $data['email']           = $email;
            $data['address']         = $address;
            // 成为创业者认证失败的处理
  /*          if($auth){
            $data['status'] = 0;
            $data['fail_reason'] ='';
            $cash_id = $auth_mod->edit("user_id = '$user_id'",$data);
             //在member表中的默认银行修改资料
                $member_mod = m('member');
                $arr = "df_bankcard = '{$bank_card}'";
                $member_mod->edit($user_id,$arr);
             //把银行卡信息更新到member_bank表中
                $bank_arr['user_id']         = $user_id;
                $bank_arr['bank']            = $bank;
                $bank_arr['bank_id']         = $bank_id;
                $bank_arr['bank_address']    = $bank_address;
                $bank_arr['bank_card']       = $bank_card;
                $bank_arr['card_name']       = $real_name;
                $bank_arr['add_time']        = time();
            $figure_auth =$auth_mod->get("user_id='$user_id'");
            $bank_mod = m('member_bank');
                $bank_mod->edit("user_id ='$user_id'",$bank_arr);
            $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '成功提交认证资料',
                        'status' =>$figure_auth['status'],
                    )
                );
                
                $msg = new Notice();
                $href = ADMIN_SITE_URL."app=authperson&act=edit&id=".$figure_auth['id'];
                $msg->send(array(
                    "content" => "成为创业者-重新实名认证（$figure_auth[id]-）<a href= $href >点击查看</a>",
                    'node'    => 'auth',
                ));
                
                 return $json->encode($return);
            
 
            }*/
            // 提交申请
            if(($cash_id = $auth_mod->add($data)))
            {
                //在member表中的默认银行卡添加资料
                $member_mod = m('member');
                $arr = array(
                    'df_bankcard'=>$bank_card,
                );

                $member_mod->edit($user_id,$arr);
                //把银行卡信息添加到member_bank表中
                $bank_arr['user_id']         = $user_id;
                $bank_arr['bank']            = $bank;
                $bank_arr['bank_id']         = $bank_id;
                $bank_arr['bank_address']    = $bank_address;
                $bank_arr['bank_card']       = $bank_card;
                $bank_arr['card_name']       = $real_name;
                $bank_arr['add_time']        = gmtime();
                //print_r($bank_arr);die;
                $figure_auth =$auth_mod->get("user_id='$user_id'");
                
                $bank_mod = m('member_bank');
                $bank_mod->add($bank_arr);

                $memlv = $memberlv_mod->get("member_lv_id = 2");


                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '成功提交认证资料',
                        'status' =>$figure_auth['status'],
                		'member_lv'=>$memlv['name'],
                        'sign'=>$sign,
                    )
                );
                
                
                $msg = new Notice();
                $href = ADMIN_SITE_URL."app=authperson&act=edit&id=".$cash_id;
                $msg->send(array(
                    "content" => "成为创业者-实名认证（$cash_id-）<a href= $href >点击查看</a>",
                    'node'    => 'auth',
                ));
                
                 return $json->encode($return);
            }
            else
            {
            /*    $return['statusCode'] 	 	     = 0;
                $return['error']['errorCode']    = 105;
                $return['error']['msg']          = '提交认证资料失败';*/
            	 $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg' => '提交认证资料失败',
                    )
                );
            	 
            	 
            	 
                return $json->encode($return);
            }
           
       
        }
        
        /**
         * 认证详情
         * @version 1.0.0
         * @author tangshoujian <280181131@qq.com>
         * @2015-3-16
         */
        public function real_auth_list($token){
            
            global $json;
            $member_mod = &m('member');
            $return = array();
            $user_info = getToken($token);
            if(empty($user_info)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = 'token错误';
                return $json->encode($return);
            }
            $user_id  = $user_info['user_id'];
            $auth_mod   = m('auth');
            $auth_list = $auth_mod->get("user_id = '$user_id'");
            if(empty($auth_list)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '101';
                $return['status']             = '未认证';
                $return['error']['msg'] 	  = '认证资料不存在';
                return $json->encode($return);
                
            }
            $sta = array(0=>'审核中',1=>'认证成功',2=>'认证失败');
            $auth_list['temp_status'] = $auth_list['status'];
            $auth_list['status'] = $sta[$auth_list['status']];
         
            $return['statusCode']        = 1;
            $return['result']['auth_list']            = $auth_list;
            $return['result']['status']            = $auth_list['status'];
            $return['result']['success'] = '认证详情';
            
            return $json->encode($return);
            
         
            
            
            
        }
        

        
        /**
         * 放弃认证
         * @version 1.0.0
         * @author tangshoujian <280181131@qq.com>
         * @2015-3-16
         */
        
        public function giveUpAuth($token){
            
            global $json;
            $member_mod = &m('member');
            $return = array();
            $user_info = getToken($token);
            $user_id = $user_info['user_id'];
            if(empty($user_info)){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 102;
                $return['error']['msg']       = '只有店长或者量体师才能放弃认证';
                return $json->encode($return);
            }
            $auth_mod   = m('auth');
            $m_info = $auth_mod->get('user_id = '.$user_id);
            if(!$m_info){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = 103;
                $return['error']['msg']       = '没有认证信息';
                return $json->encode($return);
            }
            
            $imgs = array($m_info['card_face_img'], $m_info['card_back_img']);
            
            if($auth_mod->drop('user_id = '.$user_id)){
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
         * @author tangshoujian <280181131@qq.com>
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
        public function uploadBackImg($token, $card_back)
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
         *删除认证图片
         * @param string $token
         * @param string $type
         * @author liuchao <280181131@qq.com>
         * @2015-3-16
         */
        public  function delAuthImg($token,$field,$img){
        	
            global $json;
            $member_mod = &m('member');
            $return = array();
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];
            $real_dir = $this->real_dir.$img;
            /********如果数据库有内容修改数据库**********/
            $auth_mod   = m('auth');
            $m_info = $auth_mod->get('user_id = '.$user_id);
            if($m_info)
            {
                $auth_id = $m_info['id'];
                if ($auth_mod->edit($auth_id, array($field => '')) !== false) {
                    $return['result']['success1'] = '数据库修改成功';
                }else{
                    $return['statusCode'] = 0;
                    $return['error']['errorCode'] = 104;
                    $return['error']['msg'] = '数据库修改失败';
                    return $json->encode($return);
                }
            }
            //echo $real_dir,"~~~",$img;die;
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
     * 列出我推荐安装app用户(只有申请入住成功才显示)
	 *
     * @param string $token     $fliter 滤条件，不传为所有推荐用户，用户名或手机号  模糊查询
     *
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function getInviteUser($token, $fliter, $pageSize, $pageIndex)
	{
		global $json;
		$return = array();
		$meminvite_mod = &m('memberinvite');
		$mem_mod =& m('member');
		$this->_debit_mod = &m("debit");
        $this->_gift_mod = &m("gift");
		$img_url = LOCALHOST1."/";
		$user_info = getToken($token);
	
		
		if(!$user_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				),
			);
			return $json->encode($return);
		}
		$user_id = $user_info['user_id'];

		$limit = $pageSize*($pageIndex-1).','.$pageSize;
		//模糊查询
		$conditions = '';
		if($fliter) {
			$conditions = " and (member.nickname LIKE '%$fliter%' || member.user_name LIKE '%$fliter%' )";
		}
		
		$list = $mem_mod->find(array(
		    'conditions'=> 'member_invite.inviter =' . $user_id . $conditions,
			'join'      => 'has_member_invite',
			'fields'    => 'member_invite.inviter, member_invite.invitee, member_invite.add_time, member_invite.come_from, member.user_id, member.nickname, member.user_name, member.avatar, member.phone_mob, member.real_name',
			'limit'     => $limit,
			'order'     => 'add_time DESC',
			'index_key' => '',
		));

		if(empty($list)){
		
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 101,
					'msg'       => '您还没有推荐的用户',
				),
			);
			return $json->encode($return);
		
		}
	
		/* 头像 add by xiao5 START */
		require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
		$objAvatar = new Avatar();
	    foreach ($list as $key=> $val){
	    	$list[$key]['add_time'] =  date('y-m-d',$val['add_time']);
	    	if($val['avatar'] == ''){
	    	   
	    	    $list[$key]['avatar'] = $objAvatar->avatar_show($val['invitee'], 'big');//头像
	    	}else{
	    	    $list[$key]['avatar'] = SITE_URL.'upload/avatar/'.$val['avatar'];//头像
	    	}
		
            
	    	$gift_info = $this->_gift_mod->find(array(
	    		'conditions'=>"user_id =".$val['inviter'],
	    		'fields'    =>'money',
	    		'index_key' => '',
	    	
	    	));
	    	
	    	
            $result = array();
            foreach ($gift_info as $keys=> $value) {
               $result['money'] = $value['money'];
            }
	    	$list[$key]['money'] = isset($result['money'])? $result['money'] : 0;
			
			//获得当前顾客已获得购物券信息
			$count_debit = $this->_debit_mod->get(array(
	    		'conditions'=>"from_uid = {$user_id} AND user_id ={$val['invitee']}",
	    		'fields'    =>'sum(money) as count_debit, count(id) as count_id',
	    	));
			//未使用赠送券金额
			$ucount_debit = $this->_debit_mod->get(array(
	    		'conditions' => "from_uid = {$user_id} AND user_id ={$val['invitee']} and is_used = 0 ",
	    		'fields'     => 'sum(money) as ucount_debit',
	    	));
	
			$list[$key]['count_id']     = !empty($count_debit['count_id']) ? $count_debit['count_id'] : 0;//统计已转增的券数
			$list[$key]['count_debit']  = _format_price_int($count_debit['count_debit']);//统计已转增的券总额
			$list[$key]['ucount_debit'] = _format_price_int($ucount_debit['ucount_debit']);//统计已转增的未使用的券总额
	    }

		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success' => '推荐用户列表',
				'referees'=>$list,
			)
		);
		return $json->encode($return);
	}

	/**
     * 消费者的量体数据详情
     *
     * @param string $token 用户标识；string $item_id  查看量体数据id
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function getFigurebyuser($token)
    {
        global $json;
        $db = db();
        $mem_mod = & m('member');
        $figure_mod = & m('customer_figure');
        $serve_mod  = & m('serve');
        $figurecomment_mod = & m('figurecomment');

        
        $userInfo = getToken($token);
        if (!$userInfo) {
            $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '找不该用户',
                ),
            );
            return $json->encode($return);
        }
        $user_name = $userInfo['user_name'];
    
        // 这里需要 筛选出 最晚更新的那条量体数据
        $figure = $figure_mod->get(array(
	        'conditions'=> "customer_mobile ='{$user_name}' AND figure_state=1",
        	'order' => "lasttime DESC ",
        ));
        if(empty($figure)){
        
        	 $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '暂无量体数据',
                ),
            );
            return $json->encode($return);
        
        
        }
        $s_info = $serve_mod->get("idserve ={$figure['id_serve']} ");
 
        if($figure['liangti_id']){
        	$liangti_info = $mem_mod->get("user_id={$figure['liangti_id']}");
        	$lt_name      = $liangti_info['real_name'];
        	$phone  = $liangti_info['user_name'];
        }else{
        
        	$liangti_info = $serve_mod->get("idserve={$figure['id_serve']}");
        	$lt_name	  = $liangti_info['linkman'];
        	$phone	  = $liangti_info['mobile'];
        }
        
        if($s_info){
        	$serve_name = $s_info['serve_name'];
        	$serve_address = $s_info['serve_address'];
        	$store_mobile = $s_info['store_mobile'];
        }
        // 判断是否已经评论完毕
   
       if($figurecomment_mod->get("figure_sn=".$figure['figure_sn']))
       {
       		$is_comment = 1;
       }else
       {
       		$is_comment = 0;
       }
        
        
   
      /*  $sql = "SELECT s.store_name, m.* from ".DB_PREFIX."member_figure m LEFT JOIN ".DB_PREFIX."store s ON s.store_id=m.storeid WHERE m.userid=$user_id AND figure_sn=$item_id";
        $figure = $db->getRow($sql);*/
        

        //$_member_figure_mod = & m('member_figure');

        $figure_info = $this->_figure_positions($figure);
      
        $style   = array();//风格
        $feature = array();//特体
        $arr = array('0A02' =>'穿大衣套西服','0A01'=>'穿大衣不套西服');
        //获得风格特体
        if(!empty($figure)) {
            //处理获得体型和风格
            $_get_body_type = $this->_get_body_type(3);
            
            //处理风格数据
            foreach($_get_body_type['style'] as $style_key => $style_val) {
                $nm = $style_val['info']['nm'];
                
                foreach($style_val['list'] as $lkey => $lval ) {
                    if( $lkey == $figure[$nm]) {
                        $style[] = array(
                            'name' => $lval['clothName'],
                            'val'  => $lval['name'],
                        );
                    }
                }
            }
            $style[] = array('name'=>'大衣着装习惯','val'=>$arr[$figure['styleDY']]);
        
            //处理特体数据
            foreach($_get_body_type['feature'] as $feature_key => $feature_val) {
                $nm = $feature_val['info']['nm'];
              
                foreach($feature_val['list'] as $fkey => $fval ) {
                    if( $fkey == $figure[$nm]) {
                        $feature[] = array(
                            'name' => $fval['cateName'],
                            'val'  => $fval['name'],
                        );
                    }
                }
            }
        }
        $serve_mode= array(
        		'1' => '上门量体', //=====    =====
        		'2' => '到店量体', //=====    =====
        		'3' => '线下采集',//=====    =====
        		'4' => '后台录入',//=====    =====
        		'6' => '指派量体师',//=====    =====
        );
        $return = array(
            'statusCode' => 1,
            'result'     => array(
            	'customer_name' => $userInfo['nickname'],
            	'customer_mobile'   => $userInfo['user_name'],
            	'souce_from'=>$serve_mode[$figure['service_mode']],//量体方式
            	'phone'  => $phone?$phone:'',//量体师电话
            	'serve_address'  => $serve_address?$serve_address:'',//门店地址
            	'store_mobile'  => $store_mobile?$store_mobile:'',//门店电话
                'time'     => date("Y-m-d", $figure['lasttime']),
        		'serve_name'=>$serve_name?$serve_name:'',//门店名字
        		'liangti_name'=>$lt_name,//量体师name
        		'comment_type'=>$is_comment,
                'figure'   => $figure_info,
                'feature'  => $feature,
                'style'    => $style,
                'success'  => '获取成功',
            ),
        );
        return $json->encode($return);

    }
	
	/**
     * 消费者的量体数据详情-新需求，
     *
     * @param string $token 用户标识；
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function getFigurebyuserList($token)
    {
		global $json;
        $member_mod       = m('member');
        $serve_mod        = m('serve');
		$figureorderm_mod = m('figureorderm');
		
	    $user_info = getUserInfo($token);
	    if (!$user_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '账号不存在'
				),
			);
		    return $json->encode($return);
	    }
		$user_id = $user_info['user_id'];
		

		$figure_list = $figureorderm_mod->findAll(array(
	        'conditions' => "userid='{$user_id}' and " .db_create_in(array(0,1,2), 'liangti_state'),
			'join'       => 'has_order',
			'fields'     => 'figureorderm.*, order.ship_area, order.ship_addr',
			'order'      => 'id desc',
			'index_key'  => '',
	    ));
	
		$_get_body_type = $this->_get_body_type(3);
		
		$status_name = array('0'=>'未开始', '1'=>'进行中', '2'=>'已完成');
		$time_gs   = array('am'=>'上午','pm'=>'下午');
		foreach($figure_list as $key=>$val) {
			$measure_str = '到店';

			if($val['measure']==2 || $val['measure']==0 ) {//到店
				$s_info = $serve_mod->get("idserve ={$val['server_id']} ");
				$figure_list[$key]['address']     = "(到店)".$s_info['serve_name'];//预约地址
			} elseif($val['measure']==1) {//上门
				$figure_list[$key]['address']     = "(上门)".$val['area'].$val['addr'];//预约地址
			} elseif($val['measure'] ==6){
			    $figure_list[$key]['address']     = "(指派量体师)".$s_info['serve_name'];//预约地址
			}
			//指派--未指派
			if($val['liangti_state'] == 0) {
				$figure_list[$key]['liangti_name'] =  '未指派' ;
			}elseif($val['liangti_state'] ==1){
				$liangt_name = $member_mod->get(array(
					'conditions' => "user_id='{$val['liangti_id']}' ",
					'fields'     => 'real_name',
				));
				$figure_list[$key]['liangti_name'] = !empty($liangt_name) ? $liangt_name['real_name']: '' ;
			}elseif($val['liangti_state'] ==2){
				$liangt_name = $member_mod->get(array(
					'conditions' => "user_id='{$val['liangti_id']}' ",
					'fields'     => 'real_name',
				));
				$serve_name = $serve_mod->get(array(
					'conditions' => "idserve='{$val['server_id']}' ",
					'fields'     => 'linkman',
				));
				$figure_list[$key]['liangti_name'] = !empty($liangt_name) ? $liangt_name['real_name']: $serve_name['linkman'] ;
			}
			
			$figure_list[$key]['state_name']   = $status_name[$val['liangti_state']];
			$figure_list[$key]['time']         = $val['time'].$time_gs[$val['time_noon']];//预约时间 
       
		    $style   = array();
			$feature = array();
			$arr = array('0A02' =>'穿大衣套西服','0A01'=>'穿大衣不套西服');
			//特殊体形
	/* 	    if ($json_data['public']) {
		        foreach ($json_data['public'] as $pvalue) {
		            if ($val[$pvalue['value_name']]) {
					    foreach($pvalue['item'] as $ipvalue) {
							if($ipvalue['value'] == $val[$pvalue['value_name']]) {
								$feature[] = array(
									'name' => $pvalue['cateName'],
									'val'  => $ipvalue['name'],
								);
								
							}
						}
		            }
		        }
		    }

			//风格
			if ($json_data['special']) {
		        foreach ($json_data['special'] as $svalue) {
		            if ($val[$pvalue['value_name']]) {
					    foreach($svalue['item'] as $isvalue) {
							if($isvalue['value'] == $val[$svalue['value_name']]) {
								$style[] = array(
									'name' => $svalue['cateName'],
									'val'  => $isvalue['name'],
								);
								
							}
						}
		            }
		        }
		    } */
			
			//处理风格数据
			foreach($_get_body_type['style'] as $style_key => $style_val) {
			    $nm = $style_val['info']['nm'];
			
			    foreach($style_val['list'] as $lkey => $lval ) {
			        if( $lkey == $val[$nm]) {
			            $style[] = array(
			                'name' => $lval['clothName'],
			                'val'  => $lval['name'],
			            );
			        }
			    }
			}
			$style[] = array('name'=>'大衣着装习惯','val'=>$arr[$val['styleDY']]);
			
			//处理特体数据
			foreach($_get_body_type['feature'] as $feature_key => $feature_val) {
			    $nm = $feature_val['info']['nm'];
			
			    foreach($feature_val['list'] as $fkey => $fval ) {
			        if( $fkey == $val[$nm]) {
			            $feature[] = array(
			                'name' => $fval['cateName'],
			                'val'  => $fval['name'],
			            );
			        }
			    }
			}
	
			//处理量体数据
			$figure_info = $this->_figure_positions1($val);
			
			$figure_list[$key]['feature']  = $feature;
			$figure_list[$key]['style']    = $style;
			$figure_list[$key]['figure']   = $figure_info;
		}	

        $return = array(
            'statusCode' => 1,
            'result'     => array(
				'list'  => $figure_list,
            ),
        );
        return $json->encode($return);
	}

        /**
     * 处理量体数据返回像   22项量体信息
     *
     * @param array  $figure 量体数据
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function _figure_positions1($figure)
    {
        $_mod_member_figure = m('customer_figure');
        $_figure = $_mod_member_figure->_positions();
     
        $unit = $figure['unit'];
        $wunit = 'kg';

        //重新处理返回数据
        $figure_info = array(
            array('name' => $_figure['lw']['zname'], 'val' => !empty($figure['lw']) ? $figure['lw'].$unit: '0'.$unit),
            array('name' => $_figure['xw']['zname'], 'val' => !empty($figure['xw']) ? $figure['xw'].$unit: '0'.$unit),
            array('name' => $_figure['zyw']['zname'], 'val' => !empty($figure['zyw']) ? $figure['zyw'].$unit: '0'.$unit),
            array('name' => $_figure['tw']['zname'], 'val' => !empty($figure['tw']) ? $figure['tw'].$unit: '0'.$unit),
            array('name' => $_figure['zww']['zname'], 'val' => !empty($figure['zww']) ? $figure['zww'].$unit: '0'.$unit),
            array('name' => $_figure['yww']['zname'], 'val' => !empty($figure['yww']) ? $figure['yww'].$unit: '0'.$unit),
            array('name' => $_figure['sbw']['zname'], 'val' => !empty($figure['sbw']) ? $figure['sbw'].$unit: '0'.$unit),
            array('name' => $_figure['zjk']['zname'], 'val' => !empty($figure['zjk']) ? $figure['zjk'].$unit: '0'.$unit),
          
            array('name' => $_figure['qjk']['zname'], 'val' => !empty($figure['qjk']) ? $figure['qjk'].$unit: '0'.$unit),
            array('name' => $_figure['hyjc']['zname'], 'val' => !empty($figure['hyjc']) ? $figure['hyjc'].$unit: '0'.$unit),
       
            array('name' => $_figure['qyj']['zname'], 'val' => !empty($figure['qyj']) ? $figure['qyj'].$unit: '0'.$unit),
            array('name' => $_figure['yw']['zname'], 'val' => !empty($figure['yw']) ? $figure['yw'].$unit: '0'.$unit),
            array('name' => $_figure['tgw']['zname'], 'val' => !empty($figure['tgw']) ? $figure['tgw'].$unit: '0'.$unit),
            array('name' => $_figure['td']['zname'], 'val' => !empty($figure['td']) ? $figure['td'].$unit: '0'.$unit),
            array('name' => $_figure['hyg']['zname'], 'val' => !empty($figure['hyg']) ? $figure['hyg'].$unit: '0'.$unit),
            array('name' => $_figure['qyg']['zname'], 'val' => !empty($figure['qyg']) ? $figure['qyg'].$unit: '0'.$unit),
            array('name' => $_figure['zkc']['zname'], 'val' => !empty($figure['zkc']) ? $figure['zkc'].$unit: '0'.$unit),
            array('name' => $_figure['ykc']['zname'], 'val' => !empty($figure['ykc']) ? $figure['ykc'].$unit: '0'.$unit),
            array('name' => $_figure['xiw']['zname'], 'val' => !empty($figure['xiw']) ? $figure['xiw'].$unit: '0'.$unit),
            array('name' => $_figure['jk']['zname'], 'val' => !empty($figure['jk']) ? $figure['jk'].$unit: '0'.$unit),
            array('name' => $_figure['dkzkc']['zname'], 'val' => !empty($figure['dkzkc']) ? $figure['dkzkc'].$unit: '0'.$unit),
            array('name' => $_figure['dkykc']['zname'], 'val' => !empty($figure['dkykc']) ? $figure['dkykc'].$unit: '0'.$unit),
            array('name' => $_figure['syzxc']['zname'], 'val' => !empty($figure['syzxc']) ? $figure['syzxc'].$unit: '0'.$unit),
            array('name' => $_figure['cyzxc']['zname'], 'val' => !empty($figure['cyzxc']) ? $figure['cyzxc'].$unit: '0'.$unit),
            array('name' => $_figure['dyzxc']['zname'], 'val' => !empty($figure['dyzxc']) ? $figure['dyzxc'].$unit: '0'.$unit),
            array('name' => $_figure['syyxc']['zname'], 'val' => !empty($figure['syyxc']) ? $figure['syyxc'].$unit: '0'.$unit),
            array('name' => $_figure['cyyxc']['zname'], 'val' => !empty($figure['cyyxc']) ? $figure['cyyxc'].$unit: '0'.$unit),
            array('name' => $_figure['dyyxc']['zname'], 'val' => !empty($figure['dyyxc']) ? $figure['dyyxc'].$unit: '0'.$unit),
            array('name' => $_figure['syhyc']['zname'], 'val' => !empty($figure['syhyc']) ? $figure['syhyc'].$unit: '0'.$unit),
            array('name' => $_figure['cyhyc']['zname'], 'val' => !empty($figure['cyhyc']) ? $figure['cyhyc'].$unit: '0'.$unit),
            array('name' => $_figure['dyhyc']['zname'], 'val' => !empty($figure['dyhyc']) ? $figure['dyhyc'].$unit: '0'.$unit),
            array('name' => $_figure['lheight']['zname'], 'val' => !empty($figure['lheight']) ? $figure['lheight'].$unit: '0'.$unit),
            array('name' => $_figure['lweight']['zname'], 'val' => !empty($figure['lweight']) ? $figure['lweight'].$wunit: '0'.$wunit),
            


        );
        return $figure_info;
    }

    
    /**
     * 处理量体数据返回像   22项量体信息
     *
     * @param array  $figure 量体数据
     *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function _figure_positions($figure)
    {
        $_mod_member_figure = m('customer_figure');
        $_figure = $_mod_member_figure->_positions();
     
        $unit = $figure['unit'];
        $wunit = 'kg';

        //重新处理返回数据
        $figure_info = array(
            array('name' => $_figure['lw']['zname'], 'val' => !empty($figure['lw']) ? $figure['lw'].$unit: '0'.$unit),
            array('name' => $_figure['xw']['zname'], 'val' => !empty($figure['xw']) ? $figure['xw'].$unit: '0'.$unit),
            array('name' => $_figure['zyw']['zname'], 'val' => !empty($figure['zyw']) ? $figure['zyw'].$unit: '0'.$unit),
            array('name' => $_figure['tw']['zname'], 'val' => !empty($figure['tw']) ? $figure['tw'].$unit: '0'.$unit),
            array('name' => $_figure['zww']['zname'], 'val' => !empty($figure['zww']) ? $figure['zww'].$unit: '0'.$unit),
            array('name' => $_figure['yww']['zname'], 'val' => !empty($figure['yww']) ? $figure['yww'].$unit: '0'.$unit),
            array('name' => $_figure['sbw']['zname'], 'val' => !empty($figure['sbw']) ? $figure['sbw'].$unit: '0'.$unit),
            array('name' => $_figure['zjk']['zname'], 'val' => !empty($figure['zjk']) ? $figure['zjk'].$unit: '0'.$unit),
         
            array('name' => $_figure['qjk']['zname'], 'val' => !empty($figure['qjk']) ? $figure['qjk'].$unit: '0'.$unit),
            array('name' => $_figure['hyjc']['zname'], 'val' => !empty($figure['hyjc']) ? $figure['hyjc'].$unit: '0'.$unit),
        
            array('name' => $_figure['qyj']['zname'], 'val' => !empty($figure['qyj']) ? $figure['qyj'].$unit: '0'.$unit),
            array('name' => $_figure['yw']['zname'], 'val' => !empty($figure['yw']) ? $figure['yw'].$unit: '0'.$unit),
            array('name' => $_figure['tgw']['zname'], 'val' => !empty($figure['tgw']) ? $figure['tgw'].$unit: '0'.$unit),
            array('name' => $_figure['td']['zname'], 'val' => !empty($figure['td']) ? $figure['td'].$unit: '0'.$unit),
            array('name' => $_figure['hyg']['zname'], 'val' => !empty($figure['hyg']) ? $figure['hyg'].$unit: '0'.$unit),
            array('name' => $_figure['qyg']['zname'], 'val' => !empty($figure['qyg']) ? $figure['qyg'].$unit: '0'.$unit),
            array('name' => $_figure['zkc']['zname'], 'val' => !empty($figure['zkc']) ? $figure['zkc'].$unit: '0'.$unit),
            array('name' => $_figure['ykc']['zname'], 'val' => !empty($figure['ykc']) ? $figure['ykc'].$unit: '0'.$unit),
            array('name' => $_figure['xiw']['zname'], 'val' => !empty($figure['xiw']) ? $figure['xiw'].$unit: '0'.$unit),
            array('name' => $_figure['jk']['zname'], 'val' => !empty($figure['jk']) ? $figure['jk'].$unit: '0'.$unit),
            array('name' => $_figure['dkzkc']['zname'], 'val' => !empty($figure['dkzkc']) ? $figure['dkzkc'].$unit: '0'.$unit),
            array('name' => $_figure['dkykc']['zname'], 'val' => !empty($figure['dkykc']) ? $figure['dkykc'].$unit: '0'.$unit),
            array('name' => $_figure['syzxc']['zname'], 'val' => !empty($figure['syzxc']) ? $figure['syzxc'].$unit: '0'.$unit),
            array('name' => $_figure['cyzxc']['zname'], 'val' => !empty($figure['cyzxc']) ? $figure['cyzxc'].$unit: '0'.$unit),
            array('name' => $_figure['dyzxc']['zname'], 'val' => !empty($figure['dyzxc']) ? $figure['dyzxc'].$unit: '0'.$unit),
            array('name' => $_figure['syyxc']['zname'], 'val' => !empty($figure['syyxc']) ? $figure['syyxc'].$unit: '0'.$unit),
            array('name' => $_figure['cyyxc']['zname'], 'val' => !empty($figure['cyyxc']) ? $figure['cyyxc'].$unit: '0'.$unit),
            array('name' => $_figure['dyyxc']['zname'], 'val' => !empty($figure['dyyxc']) ? $figure['dyyxc'].$unit: '0'.$unit),
            array('name' => $_figure['syhyc']['zname'], 'val' => !empty($figure['syhyc']) ? $figure['syhyc'].$unit: '0'.$unit),
            array('name' => $_figure['cyhyc']['zname'], 'val' => !empty($figure['cyhyc']) ? $figure['cyhyc'].$unit: '0'.$unit),
            array('name' => $_figure['dyhyc']['zname'], 'val' => !empty($figure['dyhyc']) ? $figure['dyhyc'].$unit: '0'.$unit),
            array('name' => $_figure['lheight']['zname'], 'val' => !empty($figure['height']) ? $figure['height'].$unit: '0'.$unit),
            array('name' => $_figure['lweight']['zname'], 'val' => !empty($figure['weight']) ? $figure['weight'].$wunit: '0'.$wunit),
            
         

        );
        return $figure_info;
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
        $body_type_tm = $_mod_mtm_bt->find("clothId != '0'");
        
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
    
    public function addinviter($token ,$invite){
    
    	global $json;
   	    $return  = array();
   	    $m       = &m('member');
   	    $meminvite_mod = &m('memberinvite');
   	    $user_info = getToken($token);
   	    $user_id = $user_info['user_id'];
   	    
   	    if(!$user_info)
   	    {
   	    	$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token 错误',
				),
			);
			
   	    	return $json->encode($return);
   	    }


        if(!$invite) {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 101,
                    'msg'       => '请输入邀请码',
                )
            );
            return $json->encode($return);

        }


        if($meminvite_mod->get("invitee = '$user_id'")){
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '已被邀请过！',
                ),
            );
            return $json->encode($return);
        }


        if(strlen($invite)==9)
        {
            $member = $m->get( "serve_type=1 and invite = '".strtoupper($invite)."'");
            if($member['user_id'] == $user_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '不能邀请自己',
                    ),
                );
                return $json->encode($return);
            }

            if(empty($member))
            {

                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '邀请码错误',
                    )
                );
                return $json->encode($return);
            }

            $_type =0;
            //邀请码
            if(empty($member['nickname'])){
                if(empty($member['real_name'])){
                    $invite_nickname =$member['user_name'];
                }else{
                    $invite_nickname =$member['real_name'];
                }
            }else{
                $invite_nickname =$member['nickname'];
            }

            $inviter = $member['user_id'];
        }

        //邀请关系    邀请码 db码都放一个表
        $member_invite = m("memberinvite");
        $invite_data = array(
            'inviter'  => $inviter, //邀请人
            'invitee'  => $user_id,
            'nickname' => $invite_nickname,     //邀请人昵称
            'type'      => $_type,
            'add_time' => time(),
            'come_from'=>'app|addinviter',
        );
        $member_invite->add($invite_data);

  
         $return = array(
            'statusCode' => 1,
            'result' => array(
                'success' => '邀请成功',
                'inviter'=>$invite_nickname,
            )
        );
        return $json->encode($return);

     }

    

    
    //创业者推荐规则接口
    function cyzCommendrule($id){
    	global $json;
   	    $return  = array();
   	    $mem_mod = &m('member');
   	    $article_mod      = &m('article');

		$art_info = $article_mod->get("article_id ='$id' ");
		
      	if(empty($art_info)) 
	   	{
	   		
		$return = array(
			'statusCode' => 0,
			'error' => array(
				'errorCode' => 101,
				'msg'       => '规则不存在',
			)
		);
		return $json->encode($return);
		}

        $art_info['content'] = base64_encode($art_info['content']);
	  	$return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '获取成功',
                    'cyzruleinfo'=>$art_info,
                )
            );
            
      return $json->encode($return);

    
    }
    
    
	  
    // 获取 用户最新量体数据
    
    function getfigureinfo($token){
    
    	global $json;
        $db = db();
        $mem_mod = & m('member');
        $figure_mod = & m('customer_figure');
        $liangti_mod = & m('figure_liangti');
		$serve_mod = & m('serve');

        $userInfo = getToken($token);
        if (!$userInfo) {
            $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '找不该用户',
                ),
            );
            return $json->encode($return);
        }
        $user_name = $userInfo['user_name'];
        // 这里需要 筛选出 最晚更新的那条量体数据
        $figure = $figure_mod->get(array(
	        'conditions'=> "customer_mobile ='{$user_name}' AND figure_state=1",
        	'order' => "lasttime DESC ",
        	
        ));
           
        if(empty($figure)){
        
        	 $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '暂无量体数据',
                ),
            );
            return $json->encode($return);
        
        
        }
        // 所属量体师  
        $liangti_id = $figure['liangti_id'];
        //所属服务点
        $id_serve = $figure['id_serve'];
        
        if($liangti_id)
        {
        	
       		 $liangti_info = $mem_mod->get("user_id ='{$liangti_id}'");
       		 
       		 if($liangti_info['avatar'])
       		 {
       		 	$avatar = FIGUREURL.$liangti_info['avatar'];
       		 }else
       		 {
       		 	$avatar = '';
       		 }
       	 	 if($liangti_info['serve_type']==2 && $liangti_info['figure_type']==2){
       	 	     
       	 	     $liangti_mess = $liangti_mod->get('liangti_id='.$liangti_id);
       	 	     $card_number = $liangti_mess['card_number'];
       		 }elseif($liangti_info['serve_type']==2 && $liangti_info['figure_type']==1){
       		     $liangti_mess = $serve_mod->get('userid='.$liangti_id);
       		     $card_number = $liangti_mess['card_number'];
       		 }
       	
        	 $ltname = $liangti_info['real_name'];
        
        	 $liangti_id = $liangti_id;
        	 $customer_mobile = $figure['customer_mobile'];
        	 $customer_name   = $figure['customer_name'];
        	 $order_id        = $figure['figure_sn'];
        	 
        }else
        {
        	
         	 $serve_info  = $serve_mod ->get("idserve=".$id_serve);
        	 $s_userid  = $serve_info['userid'];
        	 $s_info    = $mem_mod->get("user_id ='{$s_userid}' ");
        	 if($s_info['avatar'])
        	 {
        	 	$avatar = FIGUREURL.$s_info['avatar'];
        	 }else
        	 {
        	 	$avatar = '';
        	 }
        	 $ltname = $s_info['real_name'];
        	 $card_number = $s_info['card_number'];
        	 $liangti_id = $id_serve;
        	 $customer_mobile = $figure['customer_mobile'];
        	 $customer_name   = $figure['customer_name'];
        	 $order_id        = $figure['figure_sn'];
        
        }
       
     
         $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '获取成功',
                    'ltname'=>$ltname,
         			'avatar'=>$avatar,
         			'job_number'=>$card_number,
         			'liangti_id'=>$liangti_id,
         			'customer_mobile'    =>$customer_mobile,
         			'customer_name'      =>$customer_name,
         			'order_id'           =>$order_id,
                )
            );
            
      	return $json->encode($return);
        	
    
    }
    
    //对量体师 服务进行评价
    function figureComment($data){
    
    	global $json;
        $db = db();
        $mem_mod = & m('member');
        $figurecomment_mod = & m('figurecomment');
        $figure_mod = & m('customer_figure');
        
        $token      =  $data->token;
        $liangti_id = isset($data->liangti_id)? $data->liangti_id : '';
        $star       = $data->star;
        $content    = $data->content;
        $figure_sn  =  $data->order_id;
   
        $userInfo = getToken($token);
        
        if (!$userInfo){
            $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '该用户未登录或者不存在',
                ),
            );
            return $json->encode($return);
        }
        if(empty($star)||empty($content)){
        	
        	   $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '评论内容为空或者评价积分未选择',
                ),
            );
            return $json->encode($return);
        
        }
  
        $figure_info = $figure_mod->get("figure_sn = '{$figure_sn}'");
        if(empty($figure_info)){
        	
        	   $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '要评论的量体服务不存在',
                ),
            );
            return $json->encode($return);
        
        }
        if(!$figure_info['figure_state']){
               $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '量体服务还未结束请勿评论',
                ),
            );
            return $json->encode($return);


        }
        
        $num = $figurecomment_mod->get("figure_sn = '{$figure_sn}' ");
        if($num){
        	
        	   $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '已经评论完毕, 请勿重复评论',
                ),
            );
            return $json->encode($return);
        
        }
  		$_data = array();
        $_data['measure_id'] = $liangti_id;
        $_data['member_id']  = $userInfo['user_id'];
        $_data['nickname']  = $userInfo['nickname'];
        $_data['star']       = $star;
        $_data['content']    = $content;
        $_data['status']       = 0;
        $_data['addtime']   = gmtime();
    	$_data['figure_sn']   = $figure_sn;
    	$_data['come_from']   = 'mfd 创业者APP';
        if($liangti_id){
        	 $figurecomment_mod->add($_data);
        	 
        	   $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '评论成功',
         			
                )
            );
            
      	return $json->encode($return);
        	 
        }else{
        	
        	 $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 101,
                    'msg'       => '要评论的量体师不存在',
                ),
            );
            return $json->encode($return);
        
        }
        
    
    }
    
    
    /**
     * 实名认证 接口
     * @version 1.0.0
     * @author tangshoujian <280181131@qq.com>
     * @2015-8-3
     */
    //$token,$real_name,$bank_id,$bank,$bank_address,$bank_card,$mobile,$code,$card_face_img,$card_back_img,$province,$city,$region_id,$address
      public function cyz_auth($data){
            
            global $json;
            $member_mod = m('member');
            $region_mod = m('region');
            $_message_mod = &m("usermessage");
			$article_mod  = &m('article');
            $return = array();
            $token        = isset($data->token) ? $data->token : '';
            $real_name    = isset($data->real_name) ? $data->real_name : '';//真实姓名
            $bank         = isset($data->bank) ? $data->bank : '';//选择银行
            $bank_id      = isset($data->bank_id) ? $data->bank_id :'';//银行id
            $bank_address = isset($data->bank_address) ? $data->bank_address : '';//开户行地址
            $bank_card    = isset($data->bank_card) ? $data->bank_card : '';//银行卡号
            $mobile       = isset($data->mobile) ? $data->mobile : '';//手机号
            $code         = isset($data->code) ? $data->code : '';//验证码
            $card_face_img= isset($data->card_face_img) ? $data->card_face_img : '';//身份证正面
            //$card_back_img= isset($data->card_back_img) ? $data->card_back_img : '';//身份证背面面
            $country     = isset($data->country) ? $data->country : '';//联系地址国家
            $province     = isset($data->province) ? $data->province : '';//联系地址省份
            $city         = isset($data->city) ? $data->city : '';//联系地址市
            $address         = isset($data->address) ? $data->address : '';//联系详细地址
            $card        = isset($data->card) ? $data->card : '';//身份证号码
            
            
            $card_face_img = str_replace(SITE_URL,"",$card_face_img);
            $country = $region_mod->getRegionName($country);
            $p = $region_mod->getRegionName($province);
            $c	  = $region_mod->getRegionName($city);
            
            
            $user_info = $member_mod->get("user_token = '$token' AND serve_type=1");
            
            
            if(empty($user_info)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = 'token错误';
                return $json->encode($return);
            }
            $user_id  = $user_info['user_id'];
            $auth_mod   = m('auth');
            $phone = $user_info['phone_mob'];
            
            if ($auth_mod->get("user_id=$user_id AND status=0"))
            {  
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您已提交审核!请勿重复提交';
                return $json->encode($return);
            }
            if(!$real_name){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入真实姓名!';
                return $json->encode($return);
            }
            
            if(!$code){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入验证码!';
                return $json->encode($return);
            }
            $sms_mod    = m('SmsRegTmp');
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$mobile}'  AND type = 'auth'" ,
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
              if(!$card){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入身份证号码!';
                return $json->encode($return);
            }
           if(!$bank){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入开户行!';
                return $json->encode($return);
            }
           if(!$bank_address){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入开户行地址!';
                return $json->encode($return);
            }
            
           if(!$bank_card){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请输入银行卡卡号!';
                return $json->encode($return);
            }
     

            
             if(!$card_face_img){
                $return['statusCode']         = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg']       = '请上传身份证正面!';
                return $json->encode($return);
            }

            $auth = $auth_mod->get("user_id=$user_id AND (status=2 OR status=1)");
     /*       if ($auth)
            {
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '103';
                $return['error']['msg'] 	  = '您的认证信息审核失败';
                $return['error']['reason']    = $auth['fail_reason'];
                return $json->encode($return);
            }*/
            
        

         
            $_data['user_id']         = $user_id;
            $_data['bank']            = $bank;
            $_data['bankcard_address'] = $bank_address;
            $_data['bankcard']        = $bank_card;
            $_data['realname']        = $real_name;
            $_data['mobile']          = $mobile;
            $_data['province'] 		  = $province;
            $_data['city'] 		  	  = $city;
            $_data['address']         = $address;
            $_data['card_face_img']   = $card_face_img;
            //$_data['card_back_img']   = $card_back_img;
            $_data['last_update_time']= time();
            $_data['card']			  = $card;


             //当审核失败的时候对 认证信息的处理
            if($auth){
				
            	$_data['status'] = 0;
            	$_data['fail_reason'] ='';
            	$cash_id = $auth_mod->edit("user_id = '$user_id'",$_data);
             	 //在member表中的默认银行修改资料
                $member_mod = m('member');
                $arr = "df_bankcard = '{$bank_card}'";
                $member_mod->edit($user_id,$arr);
             	//把银行卡信息更新到member_bank表中
                $bank_arr['user_id']         = $user_id;
                $bank_arr['bank']            = $bank;
                $bank_arr['bank_id']         = $bank_id;
                $bank_arr['bank_address']    = $bank_address;
                $bank_arr['bank_card']       = $bank_card;
                $bank_arr['card_name']       = $real_name;
                $bank_arr['add_time']        = time();
            	$figure_auth =$auth_mod->get("user_id='$user_id'");
            	$bank_mod = m('member_bank');
                $bank_mod->edit("user_id ='$user_id'",$bank_arr);

                $sign=0;
                $cy_status = change_lv($user_id);
                if($cy_status['result']==0){
                    if($cy_status['auth']==1 && $cy_status['invite']==1 &&$cy_status['coin']==0) $sign=1;//提示绿色通道
                }

            	$return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '成功提交认证资料',
                        'status' =>$figure_auth['status'],
                        'sign'=>$sign,
                    )
                );
                
                $auth_mod->setInc("user_id='$user_id'",'num');
                $msg = new Notice();
                $href = ADMIN_SITE_URL."app=authfigure&act=edit&id=".$figure_auth['id'];
                $msg->send(array(
                    "content" => "量体派工-重新实名认证（$figure_auth[id]-）<a href= $href >点击查看</a>",
                    'node'    => 'figure',
                ));
                
                 return $json->encode($return);
            
            }
            // 添加认证资料 
            if(($cash_id = $auth_mod->add($_data)))
            {
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

                $figure_auth =$auth_mod->get("user_id='$user_id'");



                $bank_mod = m('member_bank');
                $bank_mod->add($bank_arr);

                $sign=0;
                $cy_status = change_lv($user_id);
                if($cy_status['result']==0){
                    if($cy_status['auth']==1 && $cy_status['invite']==1 &&$cy_status['coin']==0) $sign=1;//提示绿色通道
                }
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '成功提交认证资料',
                        'status' =>$figure_auth['status'],
                        'sign'=>$sign,
                    )
                );
                $auth_mod->setInc("user_id='$user_id'",'num');
                
                $msg = new Notice();
                $href = ADMIN_SITE_URL."app=authfigure&act=edit&id=".$cash_id;
                $msg->send(array(
                    "content" => "量体派工-实名认证（$cash_id-）<a href= $href >点击查看</a>",
                    'node'    => 'figure',
                ));
                
                 return $json->encode($return);
            }
            else
            {
            	 $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg' => '提交认证资料失败',
                    )
                );
            	 

                return $json->encode($return);
            }
           
       
        }
        
        /**
         * 认证详情
         * @version 1.0.0
         * @author tangshoujian <280181131@qq.com>
         * @2015-3-16
         */
        public function cyz_auth_list($token){
            
            global $json;
            $member_mod = &m('member');
            $region_mod = m('region');
            $bank_arr = include_once ROOT_PATH.'/soaapi/v1/includes/libraries/arrayfile/bank.arrayfile.php';
            $bank_arr = array_flip($bank_arr);
 
            $return = array();
            $user_info = $member_mod->get("user_token = '$token' AND serve_type=1");
            if(empty($user_info)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '100';
                $return['error']['msg'] 	  = 'token错误';
                return $json->encode($return);
            }
            $user_id  = $user_info['user_id'];
            $auth_mod   = m('auth');
            $auth_list = $auth_mod->get("user_id = '$user_id'");
            if(empty($auth_list)){
                $return['statusCode'] 	 	  = 0;
                $return['error']['errorCode'] = '101';
                $return['status']             = '未认证';
                $return['error']['msg'] 	  = '认证资料不存在';
                return $json->encode($return);
                
            }
            $sta = array(0=>'审核中',1=>'认证成功',2=>'认证失败');
        
            $auth_status = $sta[$auth_list['status']];
            $auth_list['p_id'] = $auth_list['province'];
            $auth_list['c_id'] = $auth_list['city'];
            $auth_list['bank_id'] = $bank_arr[$auth_list['bank']];
            $auth_list['card_face_img'] =  SITE_URL.$auth_list['card_face_img'];
         	$auth_list['province'] = $region_mod->getRegionName($auth_list['province']);
            $auth_list['city'] = $region_mod->getRegionName($auth_list['city']);
            // 给认证失败 原因 加入序号
            $strarr = explode(";",$auth_list['fail_reason']);
            foreach ($strarr as $k=>$v){
               $strarr[$k] = ($k+1).'、'.$v;
            }
            
            $strarr = implode(";",$strarr);
            $auth_list['fail_reason'] = $strarr;
            $return['statusCode']        = 1;
            $return['result']['auth_list']            = $auth_list;
            $return['result']['status']            = $auth_status;
            $return['result']['success'] = '认证详情';
        
            return $json->encode($return);
            
         
            
            
            
        }
        
			/**
			 * 三级联动
			 */
		public 	function region($data)
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

        public  function activepublic()
        {
                global $json;
                $region_id = isset($data->region_id) ? intval($data->region_id) :0;
                $region_mod = m('region');
                $conditions = "parent_id = $region_id";

                $motif_mod = & m('motif');
                $motif_group_mod = & m('motifgroup');
                $motif_rel_content_mod=&m('motifrelcontent');

                    // 活动特惠  
                $site= $motif_group_mod->get(array(
                    'conditions'=>" site_id=2 and code='app_active_public'",
                ));



                $site_id = $site['id'];

                if($site_id){
                
                $list  = $motif_mod->get(array(
                    'conditions'=>"is_show=1 and is_delete=0 and site_id=2 and location_id=".$site_id,
                ));
               
                $list['images'] =  $motif_rel_content_mod->find(array(
                    'conditions'=>"is_show=1 and parent_id=".$list['id'],
                    'index_key' =>'',
                ));
                 
                }else{
                    $list = array();
                }

                $return['statusCode'] = 1;
                $return['result']['success'] = "获取活动列表成功!";
                $return['result']['list'] = $list;
                return $json->encode($return);
        }

        public function activegoods($data){

           global $json;
           $id = isset($data->id) ? intval($data->id) :10;
           $pageSize  = !empty($data->pageSize) ? $data->pageSize : 10 ;
           $pageIndex = !empty($data->pageIndex) ? $data->pageIndex : 1 ;
           $mdlGoods =& m('goods');
           $goods_promotion_mod =& m('goods_prorule');
           $goodstype_mod =& m('goods_type'); //商品类型
           $gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
           $link_mod =& m('goods_prorel');
           $goodslink_mod = &m('goods_prolink');
           $user_mod =& m('member');
           $lv_mod =& m('memberlv');

            $rule = $goods_promotion_mod->get($id);
            if(empty($rule)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg' => '促销商品不存在!',
                    )
                );
                
                return $json->encode($return);
            }


        $conditions ="1=1";
        if($rule['favorable']==1){
            
            $rule_value =  $goodslink_mod->find(array(
                'conditions'=>"rules_id='{$id}' AND favorable_id=1 AND type=0",
            ));
            foreach ($rule_value as $k=>$v){
                
                $type[] = $v['favorable_value'];
                
            }
            $conditions .="  AND ".db_create_in($type,"type_id");
            
               
        }
        if($rule['favorable']==2){
            $goodsIds = $link_mod->find(array(
                'conditions'=>"d_id='{$id}'",
            ));
            foreach ($goodsIds as $value){
                $gIds[] = $value['c_id'];
            }
            
            $conditions .= "  AND ".db_create_in($gIds,"goods_id");
            
        }
        if($rule['favorable'] ==3){
           
            $rule_value =  $goodslink_mod->find(array(
                'conditions'=>"rules_id='{$id}' AND favorable_id=3 AND type=0",
            ));
           
            foreach ($rule_value as $v){
                $catId[] = $v['favorable_value'];
            }  
        }
        
        $order =  isset($data->order) ? $data->order : 0;
        $sort  =  isset($data->sort) ? $data->sort : '';
        $goodsLib = new Goods();
        $gcategoryLib = new Gcategory();
        $limit = $pageSize*($pageIndex-1).','.$pageSize;
        $goodsList = $goodsLib->lists($limit,$conditions,$catId,$order,$sort);
        $goodsList = array_merge($goodsList);
        $yhcase = $rule['yhcase'];
        $yhcasevalue = $rule['yhcase_value'];
        
        foreach ($goodsList as $k=>$v){
            if($yhcase==1){
                $goodsList[$k]['yhcase']= $v['price'] * $yhcasevalue * 0.01;
                $goodsList[$k]['yhcase_name']= '促销';
            }elseif($yhcase ==2){
                $goodsList[$k]['yhcase']= $yhcasevalue;
                $goodsList[$k]['yhcase_name']= '促销';
                 
            }elseif($yhcase ==3){
        
                $goodsList[$k]['yhcase']= $v['price'] - $v['price'] * $yhcasevalue * 0.01;
                $goodsList[$k]['yhcase_name']= '促销';
        
            }elseif($yhcase==4){
        
                $goodsList[$k]['yhcase']= $v['price'] - $yhcasevalue;
                $goodsList[$k]['yhcase_name']= '促销';
            }elseif($yhcase ==5){
                $goodsList[$k]['yhcase']= $v['price'];
                $goodsList[$k]['yhcase_name']= '包邮';
            }
            $goodsList[$k]['yhcase_id']= $yhcase;
            $goodsList[$k]['id']= $id;
        
        }
        $return['statusCode'] = 1;
        $return['result']['success'] = "获取活动商品成功!";
        $return['result']['list'] = $goodsList;
        return $json->encode($return);
        }







    

}