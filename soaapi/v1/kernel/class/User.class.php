<?php
use Cyteam\Comment\Comment;
use Cyteam\Member\Member;
    class User extends Result
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
            parent::__construct();
            $this->_auth_web_img = SITE_URL."/upload/followed_beat/";
            
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
         * 普通会员登录
         *
         * @param string $user_name 用户名（手机或Email）
         * @param string $password  用户密码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function login($user_name, $password)
        {
            global $json;


            $m         = m('member');

            $return    = array();
            $user_info = $m->get("user_name = '$user_name'");

            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在'
                    ),
                );
                return $json->encode($return);

            } else {
                if($user_info['password'] == md5($password)) {
                    $user_id = $user_info['user_id'];
                    //更新token
                    $token = md5($user_name.$password);
                    $m->edit($user_id, array("user_token" => $token));
                    //更新登录时间
                    $m->edit($user_id, array("last_login" => gmtime()));


                    $user_info = getToken($token);


                    $user_info['erweima_url'] = LOCALHOST1.'/phpqrcode/image/'.$user_info['erweima_url'];
                    do{
                        $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
                    }while(!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
                    $user_info['api_auth_code'] = $api_token;




                    //组装返回数据
                    $return = array(
                        'statusCode' => 1,
                        'result'     => array(
                            'user'    => $user_info,
                            'success' => '用户登录成功',
                        ),
                    );

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error'      => array(
                            'errorCode' => 101,
                            'msg'       => '密码与手机号（或Email）不匹配，请重试'
                        ),
                    );

                }

                return $json->encode($return);
            }

        }

        /**
         * 裁缝登录接口
         *
         * @param string $phoneNum  电话；string $password  用户密码
         * @access protected
         * @author tangsj <963830611@qq.com>
         * @return void
         */
        public function storeLogin($phoneNum , $password, $access_token,$type,$client)
        {
           
            global $json;
            $db = db();
            $m = m('member');
            $member_money = m('member_money');
            $lv_mod =& m('memberlv');
            $ucmember_mod = m('ucmember');
            $ucconnect_mod = m('ucmember_connect');
            $order=m("order");
            $user_info = [];
            if($phoneNum && $password){
                $user_info = $m->get("serve_type=1 and (user_name='$phoneNum' or phone_mob='$phoneNum')");
                if(!$user_info) {
                    //组装返回数据
                    $return = array(
                        'statusCode' => 0,
                        'error'      => array(
                            'errorCode' => 130,
                            'msg'       => '账号不存在',
                        ),
                    );
                
                    return $json->encode($return);
                }
                $phoneNum = $user_info['user_name'];
               // $ucmember_info = $ucmember_mod->get("usnnifsadffadseadsfdsfd fani ername='{$phoneNum}'");
                Qrcode('store', $user_info['user_id'], 'http//h5.myfoodiepet.com/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$user_info['invite'],$user_info['avatar']);
             
                $passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);

                if(empty($user_info))
                {
                    $status = -1;
                }
                elseif($user_info['password'] != $passwordmd5)
                {
                    $status = -2;
                }
                else
                {
                    $status = $user_info['user_id'];
                    unset($user_info['password']);
                }
            }

            if($access_token && $type){
                $username = md5($access_token);
                if(in_array($type, array(1,2,3,4))){
                    $user_info = $m->get("(user_name= '$username')");
                    $phoneNum = $access_token;
                    $password = $type;
                    if(!$user_info) {
                        //组装返回数据
                        $return = array(
                            'statusCode' => 0,
                            'error'      => array(
                                'errorCode' => 104,
                                'msg'       => '账号不存在',
                            ),
                        );
                        return $json->encode($return);
                    }
                    $status = $user_info['user_id'];
                }else{
                    $return = array(
                        'statusCode' => 0,
                        'error'      => array(
                            'errorCode' => 103,
                            'msg'       => '类型错误,请重新登录',
                        )
                    );
                    
                    return $json->encode($return);
                    
                }
            }
            
       
            if(!$user_info)
            {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 103,
                        'msg'       => '用户不存在',
                    )
                );

                return $json->encode($return);
            }
            //生成api_auth_code111
            do{
                $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
            $user_info['api_auth_code'] = $api_token;

            if($status > 0)
            {
                //更新token  更新登录时间
                $user_id  = $user_info['user_id'];
                $time = time();
                $token    = md5($phoneNum.$password);
                $logins =$user_info['logins']+1;
                $m->edit($user_id, array("user_token"=>$token,"last_login"=>time(),"logins"=>$logins));
                $user_info['user_token']  = $token;
                
                $orders=$order->find(array(
                    'conditions'=>"user_id='{$user_id}' AND extension='news' AND status != 0",
                ));
                
                
                if($user_info['avatar']){
                    
                    if($user_info['type']){
                        
                        $user_info['avatar'] = $user_info['avatar'];
                    }else{
                        $user_info['avatar'] = SITE_URL.$user_info['avatar'];
                    }
                    
                    
                }
                $code = $user_info['invite'];
                $user_info['share_url'] = 'http://h5.myfoodiepet.com/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code;
                $user_info['erweima_url'] = SITE_URL.$user_info['erweima_url'];
                        
                //获取用户等级
                $lvs = $lv_mod->get(array(
                    'fields' => "name,experience",
                    'conditions' => "lv_type='supplier' and member_lv_id=".$user_info['member_lv_id'],
                ));

                $user_info['now_member_lv_id'] = $lvs['member_lv_id'];
                $user_info['now_member_lv_name'] = $lvs['name'];

                //获取邀请人
                $member_invite = m("memberinvite");
                $invite_info = $member_invite->get("invitee='{$user_id}'");

                if($invite_info){
                  
                    $inviter_info  = $m->get("user_id='{$invite_info['inviter']}'");
                    $user_info['invite_name'] = empty($inviter_info)?'':$inviter_info['nickname'];
               
                }else{
                    $user_info['invite_name'] = '';
                }

                $user_info['height']=='0.00'&&$user_info['height']='0';
                $user_info['weight']=='0.00'&&$user_info['weight']='0';

                //资金信息
                $income = $member_money->get(array(
                    'conditions' => "change_money > 0 and user_id='{$user_id}'",
                    'fields'     => 'sum(change_money) as income',
                ));
                $pay = $member_money->get(array(
                    'conditions' => "change_money < 0 and user_id='{$user_id}'",
                    'fields'     => 'sum(change_money) as pay',
                ));

                $user_info['income'] = !empty($income['income']) ? $income['income'] : 0;//收入
                $user_info['pay']    = !empty($pay['pay']) ? $income['income'] : 0;//支出
                $user_info['money']  = _format_price_int($user_info['money']);//余额
                $user_info['point']  = (int)$user_info['point'];
                
                
                $user_info['ord_num'] = count($orders) ? count($orders) :0;
                
                
                //日志
                if($client !=''){
                    $ip = get_real_ip();
                    $memberlogs = m('memberlogs');
                    $logs  =array(
                        "user_id"=>$user_id,
                        "type"=>1,
                        "ip"=>$ip,
                        "source"=>2,
                        "client"=>$client,
                        "add_time"=>time()
                    );
                    $memberlogs->add($logs);
                }


                $return = array(
                    'statusCode' => 1,//登录成功
                    'result'     => array(
                        'resultCode' => 101,
                        'success'    => '登陆成功',
                        'member'     => $user_info,
                    ),
                );

            } else {

                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 102,
                        'msg'       => '密码错误,请重新登录',
                    )
                );
            }

            return $json->encode($return);

        }

        public function isFistorder($token){
            global $json;
            $db = db();
            $m = m('member');
            $order_mod =& m('order');
            $lv_mod =& m('memberlv');
            $return    = array();

            $user_info   = getToken($token);

            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }


            if($user_info['member_lv_id'] >1){
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 102,
                        'msg'       => '用户已经是创业者',

                    ),
                );
                return $json->encode($return);
            }

            $user_id = $user_info['user_id'];
            //是否申请成为创业者(实名认证)
            $auth_mod =& m('auth');
            $if_cy = $auth_mod->get("user_id='{$user_id}'");

            if($if_cy){
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 103,
                        'msg'       => '已申请创业者不能重复申请',
                    ),
                );
                return $json->encode($return);
            }

            //首单
            $order_info = $order_mod->find(array(
                'conditions'=>"status in(30,40) and user_id='{$user_id}'",
            ));

            if(empty($order_info)){
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 104,
                        'msg'       => '还没完成首单,请下完单之后在申请',

                    ),
                );
                return $json->encode($return);
            }


            $lvs = $lv_mod->get(array(
                'fields' => "name,experience",
                'conditions' => "lv_type='supplier' and member_lv_id=".$user_info['member_lv_id'],
            ));

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'resultCode' => 101,
                    'success'    => '验证成功',
                    'order_first'     =>  1,
                    'now_member_lv_name'=>$lvs['name'],
                    'now_member_lv_id'=>$lvs['member_lv_id'],
                ),
            );
            return $json->encode($return);

        }

        /**
         * 获得裁缝等级
         *
         * @param string $point 积分；
         *
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getStorelv($point)
        {
            $credit_mod = & m('creditscore');
            $list = $credit_mod->get(array(
                'conditions' => 'integral>='.$point,
                'fields'      => 'class',
                'order'     => 'integral ASC'
            ));
            return  $list['class'];
        }

        /**
         * 创业者注册
         *
         * @param string $mobile    手机；string $password  用户密码
         *
         *
         * @access protected
         * @author tangsj <963830611@qq.com>
         * @return void
         */
        public function register($mobile, $password, $code, $invite)
        {
            include_once ROOT_PATH.'/vendor/autoload.php';
            global $json;
            $return  = array();
            $m       = m('member');
            $sms_mod = m('SmsRegTmp');
            $ucmember_mod = m('ucmember');
            $ucconnect_mod = m('ucmember_connect');
            

            //=====查看手机号或者用户名是否重复
            if ($m->get("(user_name = $mobile or phone_mob=$mobile) " )) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '此号码已被占用！',
                    ),
                );
                return $json->encode($return);
            }


            //验证验证码
            if($code) {
                $res = $sms_mod->get(array(
                    'conditions'=> "type = 'reg' and  code='$code' AND phone='$mobile'  ",
                    'fields'    => '*',
                    'order'     => 'id DESC',

                ));


                if($res['phone']) {
                    if(gmtime() > $res['fail_time']) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 105,
                                'msg'       => '验证码已过期',
                            )
                        );
                        return $json->encode($return);
                    }

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 106,
                            'msg'       => '验证码不正确',
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }

            //如果用户填写，获取邀请码对应用户信息
            if(!empty($invite)) {
               
                if(strlen($invite) == 9){
                  
                    $member  = $m->get("serve_type=1 and invite = '".$invite."'");
                    if(empty($member)) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 109,
                                'msg'       => '邀请码错误',
                            )
                        );
                        return $json->encode($return);
                    }

                    $_type = 0;
                    //邀请码
                    $invite_nickname =$member['nickname'];
                    $inviter = $member['user_id'];

                }else{
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 200,
                            'msg'       => '您输入的邀请码不正确',
                        )
                    );
                    return $json->encode($return);
                }
            }

            /*会员默认等级*/
            $member_lv_mod =& m('memberlv');
            $m_lv = $member_lv_mod->get_default_level();
           // $ucmember_fields = & m('ucmember_fields');
            
            
            
            // 链接用户中心
            $regip = $_SERVER["REMOTE_ADDR"];
            $salt = substr(uniqid(rand()), -6);
            $ucpassword = md5(md5($password).$salt);
            $sqladd = $uid ? "uid='".intval($uid)."'," : '';
            $sqladd .= $questionid > 0 ? " secques='".$this->quescrypt($questionid, $answer)."'," : " secques='',";
            

            
            $uarr = array(
                'username'=>$mobile,
                'password'=>$ucpassword,
                'regip'   =>$regip,
                'regdate' =>time(),
                 'salt'   =>$salt,
            );
            
           // $uid =   $ucmember_mod->add($uarr);
           // $ucmember_fields->add(array('uid'=>$uid));
            
            
            // 绑定手机号位 第三方账号
     
            $carr = array(
                'access_token'=>$mobile,
                'type'        =>4,
                'uid'         =>$uid,
                'come_from'   =>'mfd|app',
            );
            //$ucconnect_mod->add($carr);
            $data = array();
            $user_token         = md5($mobile.$password.$uid);
            $data['user_name']  = $mobile;
            $data['password']   = md5($password);
            //$data['phone_tel']  = $mobile;
            $data['phone_mob']  = $mobile;
            $data['reg_time']   = time();
            $data['last_login'] = time();
            $data['logins']     = 1;
            $data['serve_type'] = 1;//注册默认类型为：1
            $data['user_token'] = $user_token;
            $data['member_lv_id'] = $m_lv['member_lv_id'];
            $data['come_from'] = 'app';
            $data['last_ip'] = $regip;
            $data['invite'] = $this->make_member_invite();
            $user_id = $m->add($data);

            $member = new Member();
            $member->debit($user_id,$mobile);

            //生成api_auth_code
            do{
                $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));

            //奖励
            $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
            $this->_debit_mod = &m("debit");

            if($member){
                //邀请关系 一个表
                $member_invite = m("memberinvite");
                $invite_data = array(
                    'inviter'  => $inviter, //邀请人
                    'invitee'  => $user_id,
                    'nickname' => $invite_nickname,     //邀请人昵称
                    'type'      => $_type,
                    'come_from'=>'app|register',
                    'add_time' => time(),
                );
                $member_invite->add($invite_data);
            }
            $user = $m->get($user_id);
            if($user){
                if($user['serve_type'] == 1){
                    if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type']) && !empty($store_allow['open'])){
            
                        if($store_allow['debit_cate']==1){
                            $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                        }else{
                            $expire_time =$store_allow['debit_time'];
                        }
                        
                        $gcategories = $_cate_mod->get_child_cateid($store_allow['debit_type']);
                        if(empty($gcategories)){
                            $gcategories = $store_allow['debit_type'];
                        }
                        
            
                        $data =array(
                            'debit_name'=>$store_allow['debit_name'],
                            'debit_t_id'=>$store_allow['debit_type'],
                            'debit_sn'=>time().createNonceStr(8),
                            'money'=>$store_allow['debit_num'],
                            'user_id'=>$user_id,
                            'source'=>'reg',
                            'add_time'=>time(),
                             'cate'=>$gcategories,
                            'expire_time'=>$expire_time,
                        );
                        $this->_debit_mod->add($data);
                    }
            
                }
            }

            ////////////////////////////end////////////////////

            if ($user_id) {
                $user_info  =  $m->get(intval($user_id));
                
                /* 头像 add by xiao5 START */
                require_once(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
                $objAvatar = new Avatar();
                //获得用户头像
                $avatar = $objAvatar->avatar_show($user_id, 'big');
                $avatar = "/avatar/noavatar_big.gif";
                //生成获得店铺二维码
                $avatar_time = time();
                $code = $user_info['invite'];
                Qrcode('store', $user_id, 'http//h5.myfoodiepet.com/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code,$avatar);
                $mqrcode = getQrcodeImage('store', $user_id, 4);
                $mqrcode = '/upload/phpqrcode/'.$mqrcode.'?'.$avatar_time;
                
                $erweima = $m->edit($user_id,array("erweima_url" => $mqrcode));
                if($erweima) {
                    $user_info            = getUinfoByUid($user_id);
                    $user_info['api_auth_code'] = $api_token;
                    $user_info['invite_name'] = $invite_nickname;

                    //注册成功，自动添加两条默认系统消息
                    $_message_mod = &m("usermessage");
                    $article_mod  = &m('article');

                    //获取系统消息分类下所有文章
                    $art_list = $article_mod->findAll(array(
                        'conditions' => "cate_id = 67 and if_show = 1",
                    ));
                    $arr   = array();
                    foreach($art_list as $val) {
                        $arr[] = array(
                            'from_user_id'  => 1,
                            'to_user_id'    => $user_id,
                            'from_nickname' => '系统',
                            'type'          => '8',
                            'title'         => $val['title'],
                            'content'       => addslashes($val['content']),
                            'add_time'      => time(),
                            'location_url' =>'',
                        );
                    }

                    $res = $_message_mod->add($arr);

                    //组装返回数据
                    $return = array(
                        'statusCode' => 1,
                        'result'     => array(
                            'token' => $user_info['user_token'],
                            'user'  => $user_info,
                        ),
                    );

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 103,
                            'msg'       => '注册失败',
                        ),
                    );

                }
                return $json->encode($return);
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '注册失败',
                    ),
                );

                return $json->encode($return);
            }

        }
        
        
        
      public function reg($access_token, $type,$avatar,$nickname)
     {

            include_once ROOT_PATH.'/vendor/autoload.php';
            global $json;
            $return  = array();
            $m       = m('member');
            $ucmember_mod = m('ucmember');
            $ucconnect_mod = m('ucmember_connect');
            //$avatar = urldecode($avatar);// 解码 头像
            
            //=====查看手机号或者用户名是否重复
            $token = md5($access_token);
            if ($m->get("(user_name = '$token' )" )) {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 104,
                            'msg'       => '此号码已被绑定！',
                        ),
                    );
                    return $json->encode($return);
            }

            /*会员默认等级*/
            $member_lv_mod =& m('memberlv');
            $m_lv = $member_lv_mod->get_default_level();
            $ucmember_fields = & m('ucmember_fields');
        
            // 链接用户中心
            $regip = $_SERVER["REMOTE_ADDR"];
            $salt = substr(uniqid(rand()), -6);
            $ucpassword = md5(md5(123456).$salt);
            
            //$transaction = $ucmember_mod->beginTransaction();//=====  开启事物  =====
            // 查看是否 是 第三方 登录 注册  的
            $data = array();
            $user_token         = md5($access_token.time());
            $data['user_name']  = $token;
            $data['password']   = $ucpassword;
            $data['reg_time']   = time();
            $data['last_login'] = time();
            $data['logins']     = 1;
            $data['serve_type'] = 1;//注册默认类型为：1
            $data['user_token'] = $user_token;
            $data['member_lv_id'] = $m_lv['member_lv_id'];
            $data['come_from'] = 'app';
            $data['last_ip'] = $regip;
            $data['invite'] = $this->make_member_invite();
            $data['type']   = $type;
            $data['nickname'] = $nickname;
            $data['avatar'] =  urldecode($avatar);// 解码 头像
            $user_id = $m->add($data);
                
            if(!$user_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 113,
                        'msg'       => '注册失败!',
                    ),
                );
                return $json->encode($return);
            }

            $member = new Member();
            $member->debit($user_id,$nickname);

            //生成api_auth_code
            do{
                $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
        
        
            ////////////////////////////end////////////////////
        
            if ($user_id) {
             
                $user_info  =  $m->get(intval($user_id));
                
                //保存 头像到本地
//                 $avatar = urldecode($avatar);
//                 $img=file_get_contents($avatar);
//                 $filename=date("dMYHis").'.jpg';
//                 $path = ROOT_PATH.'/upload/avatar/part/';
//                 file_put_contents($path.$filename, $img);
                
                $avatar =  SITE_URL.'/upload/avatar/part/'.$filename;// 解码 头像
                         
                //生成获得店铺二维码
                $avatar_time = time();
                $code = $user_info['invite'];
                Qrcode('store', $user_id, 'http://m.mfd.ds.cotte.cn/member-register.html?ret_url=%2Fmember-index.html&er_invite='.$code,$avatar="");
                $mqrcode = getQrcodeImage('store', $user_id, 4);
                $mqrcode = '/upload/phpqrcode/'.$mqrcode.'?'.$avatar_time;
                
                $erweima = $m->edit($user_id,array("erweima_url" => $mqrcode  ));
                
              
                    $user_info            = getUinfoByUid($user_id);
                    $user_info['api_auth_code'] = $api_token;
                    $user_info['invite_name'] = $invite_nickname;
        
                    //注册成功，自动添加两条默认系统消息
                    $_message_mod = &m("usermessage");
                    $article_mod  = &m('article');
        
                    //获取系统消息分类下所有文章
                    $art_list = $article_mod->findAll(array(
                        'conditions' => "cate_id = 67 and if_show = 1",
                    ));
                    $arr   = array();
                    foreach($art_list as $val) {
                        $arr[] = array(
                            'from_user_id'  => 1,
                            'to_user_id'    => $user_id,
                            'from_nickname' => '系统',
                            'type'          => '8',
                            'title'         => $val['title'],
                            'content'       => addslashes($val['content']),
                            'add_time'      => time(),
                            'location_url' =>'',
                        );
                    }
        
                    $res = $_message_mod->add($arr);
        
                    //组装返回数据
                    $return = array(
                        'statusCode' => 1,
                        'result'     => array(
                            'token' => $user_info['user_token'],
                            'user'  => $user_info,
                        ),
                    );
        
        
                return $json->encode($return);
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '注册失败',
                    ),
                );
        
                return $json->encode($return);
            }
        
        }
        
        

        /**
         * 用户手机验证--已存在用户
         *
         * @param string $phoneNum 手机; string $type 短信发送类型
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function verifyPhoneNum($phoneNum, $type)
        {
            global $json;
            $m = m('member');
            $res=$m->find(array(
                'conditions' => 'user_name='.$phoneNum,
                'fields'     => 'user_id',
                'index_key'  => '',
            ));

            if($res) {
                return  $this->sendCode($phoneNum, $type);
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 101,
                        'msg'       => '手机号不存在',
                    )
                );
                return $json->encode($return);
            }
        }

        /**
         * 忘记密码
         *
         * @param string $phoneNum       用户合法标识
         * @param string $newpassword 新密码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function forgetPassword($phoneNum, $newpassword,$newpassword1, $code)
        {
            global $json;
            $m  = m('member');
            $sms_mod = m('SmsRegTmp');
            $ucmember_mod = m('ucmember');
            $db = db();
            $return = array();

            //验证验证码是否有效
            if($code) {
                $res=$sms_mod->get(array(
                    'conditions'=>"code='$code' AND phone='$phoneNum' AND type='findps'",
                    'order'      => "id  DESC",
                    'fields'     => '*',
                ));
                if($res['phone']) {
                    if(gmtime() > $res['fail_time']) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 101,
                                'msg'       => '验证码已过期',
                            )
                        );
                        return $json->encode($return);
                    }

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 101,
                            'msg'       => '验证码不正确',
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }



            //获得用户信息
            $user_info = $m->get("user_name = '$phoneNum' and serve_type=1");

            if($user_info['user_name']=='18678999805'){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '此用户为测试用户不能修改密码',
                    )
                );
                return $json->encode($return);
            }

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            if($newpassword !=$newpassword1) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg'       => '两次输入密码不同',
                    )
                );
                return $json->encode($return);
            }


            $user_id     = $user_info['user_id'];
            // 链接用户系统修改密码
            
           
//            $ucinfo = $ucmember_mod->get("username='{$phoneNum}'");
//            $uc_id = $ucinfo['uid'];
//            $salt = substr(uniqid(rand()), -6);
//            $ucpassword = md5(md5($newpassword).$salt);
//            $ucs = $ucmember_mod->edit($uc_id, array("password" => $ucpassword,'salt'=>$salt));
            
            //
//            $newpassword = $ucpassword;
            $res = $m->edit($user_id, array("password" => md5($newpassword)));
            if($res) {
                //组装返回数据
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'success' => '密码修改成功',
                        'newpassword' => $newpassword1,
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,//系统错误
                        'msg'       => '新密码不能和旧密码相同',
                    )
                );
            }

            return $json->encode($return);
        }

        /**
         * 修改密码
         *
         * @param string $token       用户合法标识； string $newpassword 新密码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function modifyPassword($token, $oldpassword, $newpassword)
        {
            global $json;
            $m  = m('member');
            $ucmember_mod = m('ucmember');
            $db = db();
            $return = array();
            if(empty($token)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            //新密码不能和老密码相同
            if($oldpassword == $newpassword){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '新密码不能和老密码相同',
                    )
                );
                return $json->encode($return);
            }

            $user_info   = getToken($token);
            if($user_info['user_name']=='18678999805'){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '此用户为测试用户不能修改密码',
                    )
                );
                return $json->encode($return);
            }
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未找到此用户标识',
                    )
                );
                return $json->encode($return);
            }

            $user_id     = $user_info['user_id'];
            $user_name   = $user_info['user_name'];
            
 
            
            /// 链接用户系统
//            $ucinfo = $ucmember_mod->get("username='{$user_name}'");
//            if($ucinfo['password'] != md5(md5($oldpassword).$ucinfo['salt'])){
//                $return = array(
//                    'statusCode' => 0,
//                    'error' => array(
//                        'errorCode' => 104,
//                        'msg'       => '旧密码错误',
//                    )
//                );
//                return $json->encode($return);
//            }
//echo '<pre>';print_r(($oldpassword));exit;

//echo '<pre>';print_r($user_info['password']);exit;


            if($user_info['password'] != md5($oldpassword) )
            {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '旧密码错误',
                    )
                );
                return $json->encode($return);
            }
            
            
//            $uc_id = $ucinfo['uid'];
//            $salt = substr(uniqid(rand()), -6);
//            $ucpassword = md5(md5($newpassword).$salt);
//            $ucs = $ucmember_mod->edit($uc_id, array("password" => $ucpassword,'salt'=>$salt));
            $ucpassword = md5($newpassword);
            ///
            $phoneNum    = $user_info['user_name'];
            $token       = md5($phoneNum.$newpassword);
            //$newpassword = md5($newpassword);
            $res = $m->edit($user_id, array("password" => $ucpassword,"user_token"=>$token));
            //print_r($res);die;
            if($res) {
                //组装返回数据
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'success' => '修改密码成功！',
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,//系统错误
                        'msg'       => '系统修改错误，请稍后重试',
                    )
                );
            }

            return $json->encode($return);
        }
        /**
         * 绑定手机号
         * @param string $token 用户合法标识 int $mobile 要绑定的手机号  string $code 短信验证码
         *
         * @author liuchao <280181131@qq.com>
         * @version 1.0.0 (2015-3-19)
         */
        public  function bingPhone($token,$mobile,$code)
        {
            global $json ;
            $user_info = getToken($token);
            $user_id    = $user_info['user_id'];
            $sms_mod = m('SmsRegTmp');
            //验证验证码是否有效
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$mobile}'  AND type = 'bingPhone'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                //print_r($res);exit;
                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
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

            if($user_info['phone_mob']==$mobile) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg' => '此手机号已绑定，不能重复绑定！',
                    )
                );
                return $json->encode($return);
            }

            //修改member表
            $m_mod = m('member');
            //$user_1 = $m_mod->get('93');
            //print_r($user_1);exit;


            $data['phone_mob'] = $mobile;
            if($m_mod->edit($user_id,$data)){
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '手机绑定成功',
                        'bingPhone' => $data['phone'],
                    )
                );
            }else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg' => '手机绑定失败',
                    )
                );
            }
            return $json->encode($return);

        }
        
        
        /**
         * 绑定手机号
         * @param string $token 用户合法标识 int $mobile 要绑定的手机号  string $code 短信验证码
         *
         * @author liuchao <280181131@qq.com>
         * @version 1.0.0 (2015-3-19)
         */
        public  function binduser($access_token,$type,$code,$password,$token)
        {
            global $json ;
            $sms_mod = m('SmsRegTmp');
            $ucmember_mod = m('ucmember');
            $m = m('member');
            $ucconnect_mod = m('ucmember_connect');
            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id    = $user_info['user_id'];
            $username  = $user_info['user_name'];


            //验证验证码是否有效
            if ($code)
            {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$access_token}'  AND type = 'binduser'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                //print_r($res);exit;
                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 107,
                                'msg' => '验证码已过期',
                            )
                        );
                        return $json->encode($return);
                    }

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 108,
                            'msg' => '验证码不正确',
                        )
                    );
                    return $json->encode($return);
                }

            }
            else
            {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 109,
                        'msg' => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }
            $userinfo = $m->get(" (user_name=".$access_token." or phone_mob=".$access_token.")");
            if($userinfo)
            {

                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 111,
                        'msg' => '此账号为已注册账号，不能绑定！',
                    )
                );
                return $json->encode($return);

            }
            if(in_array($type, array(1,2,3,4)))
            {
                $m->edit("user_name='{$username}'",array('phone_mob'=>$access_token,'password'=>md5($password)));
            }
            else
            {
                $m->edit("user_name='{$username}'",array('phone_mob'=>$access_token));
            }

            $return = array(
                'statusCode' => 1,//登录成功
                'result'     => array(
                    'resultCode' => 101,
                    'success'    => '绑定成功',
                ),
            );
            return $json->encode($return);



            if($type ==4){
                if(empty($password)){
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 110,
                            'msg' => '密码不能为空',
                        )
                    );
                    return $json->encode($return);
                }
                


                
//                $phone = $ucmember_mod->get("username = '$access_token'" );
                $userinfo = $m->get(" (user_name=".$access_token." or phone_mob=".$access_token.")");
                
                //$userinfo = $m->get("user_name ='$access_token' ");
                
                if($userinfo)
                {
                    
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 111,
                            'msg' => '此账号为已注册账号，不能绑定！',
                        )
                    );
                    return $json->encode($return);
                    
                }
                
//                $ucinfo = $ucmember_mod->get("username = '$username'" );
//                $uid = $ucinfo['uid'];
//                if(empty($ucinfo)){
//                    $return = array(
//                        'statusCode' => 0,
//                        'error' => array(
//                            'errorCode' => 104,
//                            'msg' => '主账号不存在',
//                        )
//                    );
//                    return $json->encode($return);
//                }
                $transaction = $ucconnect_mod->beginTransaction();//=====  开启事物  =====
                $search = $ucconnect_mod->get(array(
                    'conditions'=>"type = 4 and uid ='$uid'",
                ));
                
                if($search){
                    
                    $carr = array(
                        'access_token'=>$access_token,
                    );
                    $salt = $ucinfo['salt'];
                    $ucpassword = md5(md5($password).$salt);
                    
                    if($ucconnect_mod->edit($search['id'],$carr)){
                    
                        if($ucinfo['password'] ==$ucpassword){
                            $return = array(
                                'statusCode' => 0,
                                'error' => array(
                                    'errorCode' => 113,
                                    'msg' => '输入密码跟原密码相同',
                                )
                            );
                            $ucconnect_mod->rollback();
                            return $json->encode($return);
                        }
                         
                        $editid = $ucmember_mod->edit($uid,array('password'=>$ucpassword));
                    
                         
                        if($editid){
                    
                            $m->edit("user_name='{$username}'",array('phone_mob'=>$access_token));
                    
                            $return = array(
                                'statusCode' => 1,
                                'result' => array(
                                    'success'   => '账号绑定成功',
                                    'binguser' => $access_token,
                                )
                            );
                    
                        }else{
                    
                            $return = array(
                                'statusCode' => 0,
                                'error' => array(
                                    'errorCode' => 106,
                                    'msg' => '账号绑定失败',
                                )
                            );
                            $ucconnect_mod->rollback();
                        }
                    
                         
                    }else{
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 114,
                                'msg' => '账号绑定失败',
                            )
                        );
                        $ucconnect_mod->rollback();
                    
                    }
                    
                    return $json->encode($return);
                }
                
                $carr = array(
                    'access_token'=>$access_token,
                    'type'        =>$type,
                    'uid'         =>$uid,
                    'come_from'   =>'mfd|app',
                );
                
                $salt = $ucinfo['salt'];
                $ucpassword = md5(md5($password).$salt);
            
                if($ucconnect_mod->add($carr)){
                    
                    if($ucinfo['password'] ==$ucpassword){
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 113,
                                'msg' => '输入密码跟原密码相同',
                            )
                        );
                        $ucconnect_mod->rollback();
                        return $json->encode($return);
                    }
                 
                    $editid = $ucmember_mod->edit($uid,array('password'=>$ucpassword));
                    
               
                    if($editid){
                        
                        $m->edit("user_name='{$username}'",array('phone_mob'=>$access_token));
                        
                        $return = array(
                            'statusCode' => 1,
                            'result' => array(
                                'success'   => '账号绑定成功',
                                'binguser' => $access_token,
                            )
                        );
                        
                    }else{
                        
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 106,
                                'msg' => '账号绑定失败',
                            )
                        );
                        $ucconnect_mod->rollback();
                    }
                  
                   
                }else{
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 114,
                            'msg' => '账号绑定失败',
                        )
                    );
                    $ucconnect_mod->rollback();
                
                }
                $ucconnect_mod->commit($transaction);
                return $json->encode($return);
                   
            }
        
        }
        
        
        /**
         * 确认验证码
         * @param string $token 用户合法标识 int $mob 手机号
         *
         * @author liuchao <280181131@qq.com>
         * @version 1.0.0 (2015-5-8)
         */
        public function verify_code($token,$mob,$code){
            global $json;
            $user_info = getToken($token);
            if(!$user_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg' => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $sms_mod = m('SmsRegTmp');
            //验证手机与验证码
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$mob}'  AND type = 'editPhone'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
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
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'   => '验证码正确,下一步',
                )
            );
            return $json->encode($return);
        }
        /**
         * 修改绑定手机号
         * @param string $token 用户合法标识  int $code 短信验证码
         *
         * @author liuchao <280181131@qq.com>
         * @version 1.0.0 (2015-3-19)
         */
        public  function editPhone($token,$newMob,$code)
        {
            global $json ;
            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            $user_id   = $user_info['user_id'];
            $sms_mod = & m('SmsRegTmp');
            $member = & m('member');
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$newMob}'  AND type = 'editPhone'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));
                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
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

            $member_info = $member->get("serve_type=1 and( user_name='{$newMob}' or phone_mob='{$newMob}')");

            if($member_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg' => '此手机号已被占用，不能绑定！',
                    )
                );
                return $json->encode($return);
            }

            if($user_info['phone_mob']==$newMob) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg' => '此手机号已绑定，不能重复绑定！',
                    )
                );
                return $json->encode($return);
            }
            //修改member表
            $m_mod = m('member');
            $data['phone_mob'] = $newMob;
            if($m_mod->edit($user_id,$data)){
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'   => '修改绑定手机号成功',
                        'bingPhone' => $newMob,
                    )
                );
            }else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg' => '修改绑定手机号失败',
                    )
                );
            }
            return $json->encode($return);

        }


        /**
         * 获取消费者基本信息
         *
         * @param string $token 用户合法标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getUserInfo($token)
        {
            global $json;
            $m = m('member');
            $return = array();
            if(empty($token)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,//系统错误
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            $user_id   = $user_info['user_id'];
            $customerinfo = $m->get("user_id='$user_id' AND serve_type=1");
            if($customerinfo) {
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'userinfo' => $customerinfo,
                    ),
                );

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '系统修改错误，请稍后重试',
                    )
                );
            }
            return $json->encode($return);

        }

        /**
         * 获取推荐列表
         *
         * @param int $pageSize 大小 int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function recomStyle($recom_id, $cate, $pageSize, $pageIndex)
        {
            global $json;
            $cus  = &m("custom");
            $suit = &m("suitlist");
            $img_url = LOCALHOST1;
            $_mod_cusGallery = &m("customgallery");
            $style_recommend = & m('style_recommend');
            $customs = $dissertation = array();
            $count_custom = $count_dis = 0;

            $style_info = $style_recommend->get($recom_id);

            //======获得基本款推荐=====
            if($style_info['customs']) {
                $conditions  = "";
                if($cate) {
                    $conditions .= " AND cat_id =$cate ";
                }

                $customs = $cus->find(array(
                    'conditions' => db_create_in($style_info['customs'], 'id') . $conditions,
                    'fields'    => 'id, name, category, price, brief as content, small_img, source_img',
                    'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                    'order'     => 'add_time DESC',
                    'count'     =>true,
                    'index_key'	 => '',
                ));
                //获得基本款相册
                foreach($customs as $key=>$val) {
                    $gallery_list = $_mod_cusGallery->find(array(
                        'conditions' => "custom_id = '{$val['id']}'",
                        'fields'     => "source_img as img",
                        'order'      => 'sort asc',
                        'index_key'	 => '',
                    ));
                    $customs[$key]['gallery'] = !empty($gallery_list) ? $gallery_list : '';
                }

                $count_custom = $cus->getCount();
            }

            //======获取关联的套装=====
            if($style_info['dissertations']) {
                $dissertation = $suit->find(array(
                    'conditions' => db_create_in($style_info['dissertations'], 'id'),
                    'fields'    => 'id, suit_name as name , price, content, image as source_img',
                    'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                    'count'     =>true,
                    'index_key'	 => '',
                ));

                //获得基本款相册
                foreach($dissertation as $key=>$val) {
                    /*暂无相册
				$gallery_list = $_mod_cusGallery->find(array(
					'conditions' => "custom_id = '{$val['id']}'",
					'fields'     => "source_img as img",
					'order'      => 'sort asc',
					'index_key'	 => '',
				));*/
                    $dissertation[$key]['category'] = 1;//设置套装分类，用于添加作品
                    //$dissertation[$key]['gallery']  = !empty($gallery_list) ? $gallery_list : '';
                    $dissertation[$key]['gallery']  = array(array('img'=>$val['source_img']));

                }

                $count_dis = $suit->getCount();
            }

            //合并追加数组
            $recomStyle = array_merge($customs, $dissertation);

            $count = $count_dis + $count_custom;

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'dissertation' => !empty($recomStyle) ? $recomStyle : '',
                    'count'        => $count,
                ),
            );
            return $json->encode($return);
        }

        /**
         * 获取样衣推荐列表-套装
         *
         * @param int $pageSize 大小 int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function recomDisser($pageSize, $pageIndex)
        {
            global $json;
            $dis  = m('dissertation');
            $return = array();
            $dissertation = $dis->find(array(
                'conditions' => 'is_rec = 1',
                'fields'     => 'id, title, small_img, add_time, cat, likes',
                'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                'order'      => 'likes DESC',
                'count'      => true,
                'index_key'	 => '',
            ));

            $count = $dis->getCount();

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'dissertation' => !empty($dissertation) ? $dissertation : '',
                    'count'        => $count,
                ),
            );
            return $json->encode($return);
        }

        /**
         * 获取样衣推荐列表-基本款
         *
         * @param int $type 样衣类型， int $pageSize 大小 int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function recommendSigle($type, $pageSize, $pageIndex)
        {
            global $json;
            $cus = &m("custom");
            $return = array();
            $conditions  = "";
            $conditions .= " AND cat_id =$type ";
            $dissertation = '';

            $dissertation = $cus->find(array(
                //'conditions'=> 'is_rec =1 '.$conditions,
                'conditions'=> '1=1 '.$conditions,
                'fields'    => 'id, name, cat_id, price, content, brief, small_img, source_img',
                'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                'order'     => 'add_time DESC',
                'count'     =>true,
                'index_key'	 => '',
            ));

            $count = $cus->getCount();

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'dissertation' => !empty($dissertation) ? $dissertation : '',
                    'count'        => $count,
                ),
            );
            return $json->encode($return);

        }

        /**
         * 根据分类获得对应列表
         *
         * @param int $type 样衣类型， int $pageSize 大小 int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function recommendList($type, $pageSize, $pageIndex)
        {
            global $json;
            $return = array();
            if(empty($type)) {//返回套装信息
                $dis  = m('dissertation');
                $dissertation = $dis->find(array(
                    'conditions' => 'is_rec = 1',
                    'fields'     => 'id, title, small_img as img, price, brief as description',
                    'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                    'order'      => 'likes DESC',
                    'count'      => true,
                    'index_key'	 => '',
                ));
                //套装暂时无相册--一张主图
                foreach($dissertation as $key=>$val) {
                    $dissertation[$key]['gallery'] = "";
                }

                $count = $dis->getCount();
            } else {//返回单款信息
                $cus = m('customs');
                $conditions  = "";
                $conditions .= " AND cst_cate =$type ";
                $dissertation = $cus->find(array(
                    'conditions'=> 'is_rec =1 '.$conditions,
                    'fields'    => 'cst_cate,cst_id as id, cst_name as title, cst_price as price, cst_image as img, service_fee as price, cst_description as description',
                    'limit'		=>  ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                    'order'     => 'cst_likes DESC',
                    'count'     =>true,
                    'index_key'	 => '',
                ));

                //获得单品相册
                $_gallery_mod = &m("gallery");
                foreach($dissertation as $key=>$val) {
                    $gallery_list = $_gallery_mod->find(array(
                        'conditions' => "cst_id =".$val['id'],
                        'fields'     => "img",
                        'order'      => 'id ASC',
                        'index_key'  => '',
                    ));
                    $dissertation[$key]['gallery'] = !empty($gallery_list) ? $gallery_list : "";
                }
                $count = $cus->getCount();
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'dissertation' => !empty($dissertation) ? $dissertation : '',
                    'count'        => $count,
                ),
            );
            return $json->encode($return);

        }

        /**
         * 编辑用户资料
         *
         * @param array $data 用户资料参数
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function setUserInfo($data)
        {
            global $json;
            $m  = &m('member');
            $return = array();

            $token  = $data->token;
            $user_info = $m->get("user_token = '$token' AND serve_type=1 ");

            if (!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];
            
            if($user_id) {
                $nickname   = isset($data->nickname) ? $data->nickname : '';//昵称
                $gender     = isset($data->gender) ? $data->gender : 0;//性别
                $email       = isset($data->email) ? $data->email : '';//邮箱
              
                $user_data       =array();
          
                if($nickname) {
                    $user_data['nickname'] = $nickname;

                }
                if($gender) {
                    $user_data['gender'] = $gender;
                }
                
                if($email) {
                    $user_data['email'] = $email;
                }
                
                
                $res       = $m->edit("user_id  = '$user_id'", $user_data);


                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '用户资料修改成功',
                    )
                );
                return $json->encode($return);

            }
        }

        /**
         * 获得关键词
         *
         * @param NULL
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getKeyword()
        {
            global $json;
            $return  = array();
            $theme   = array(1=>'礼服', 2=>'职场', 3=>'留学', 4=>'明星款');
            $sorting = array(1=>'人气', 2=>'最新');
            $return['theme']   = $theme;
            $return['sorting'] = $sorting;
            return $json->encode($return);
        }

        /**
         * 获取套系列样衣列表
         *
         * @param int $sorting 排序(1:人气排序 2：综合排序) ;int $pageSize  每页显示的个数；int $pageIndex 第几页；
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getDissertationList($sorting, $pageSize, $pageIndex)
        {
            global $json;
            $dissertation = m('dissertation');
            $return = array();

            if($sorting == 1) {
                $sorting = ' likes DESC ';//人气排序
            }
            if($sorting == 2) {
                $sorting = ' praise_num DESC, likes DESC, is_hot DESC, sort_order DESC ';//综合排序
            }

            $time = gmtime();
            $conditons = " start_time <='$time' AND end_time >='$time'  ";

            $data= $dissertation->find(array(
                'conditions' => $conditons,
                'order'      => $sorting,
                'limit'		 => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                'count'      => true,
                'fields'     => 'id, title, brief, likes, small_img, start_time, end_time, praise_num',
                "index_key"  => '',
            ));

            //获得总数
            $dissertation_count =   $dissertation->get(array(
                'conditions' => $conditons,
                'fields'     =>'count(*) as count',
                'index_key'	=> '',
            ));

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'dissertationList' => !empty($data) ? $data : '',
                    'count'            => $dissertation_count,
                ),
            );
            return $json->encode($return);

        }

        /**
         * 获样衣一级分类
         *
         * @param NULL
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getCustomeCate()
        {
            global $json;
            $custome_cate_arr = array();
            $customs = m('customs');
            $return = array();

            $customs_cate = $customs->getCate();
            if($customs_cate) {
                foreach($customs_cate as $cate) {
                    $custome_cate_arr[] = array(
                        'cate_name' => $cate['cate_name'],
                        'cate_id'   => $cate['cate_id'],
                        'diy_url'   => 'http://m.cy.mfd.cn/custom-diy-'.$cate['cate_id'].'-code.html',
                    );
                }
            }

            //获取面料属性返回
            $conditions = "1=1 and type_id = 1";
            $zujian_attr_mod = & m("partattribute");
            $attr = $zujian_attr_mod->find(array(
                'fields' => 'g.*',
                "conditions"	=>	$conditions,
                'order'	=> 'g.sort_order',
            ));
            $custom_arr = array();
            foreach ($attr AS $key => $val) {
                $attr_values = explode("\n", $val['attr_values']);
                $arr_data = array();
                foreach ($attr_values AS $opt) {
                    $opt    = trim(htmlspecialchars($opt));
                    $arr_data[]['name'] =  $opt;
                }
                if($val['attr_name'] != '品牌' && $val['attr_name'] != '分类') {//过滤品牌
                    $custom_arr[] = array(
                        'type' =>$val['attr_name'],
                        'attr' =>$arr_data,
                    );
                }

            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'cus_cate'   => !empty($custome_cate_arr) ? $custome_cate_arr : '',
                    'custom_arr' => !empty($custom_arr) ? $custom_arr : '',
                    'success'    => '分类获取成功',
                ),
            );
            return $json->encode($return);

        }

        /**
         * DIY下订-分类
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getDiyCate($token)
        {
            global $json;
            $custome_cate_arr = array();
            $return = array();
            $user_info = getToken($token);
            /*
	    if (!$user_info) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => '用户不存在',
				)
			);
	        return $json->encode($return);
	    }*/

            //生存api_auth_code
            if($user_info){
                do{
                    $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
                } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
            }else{
                do{
                    $api_token = ApiAuthcode('0', 'ENCODE', 'kuteiddiy', 0);
                } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
            }

            // print_exit($api_token);
            //$user_info['api_auth_code'] = $api_token;

            $diy_cate = array(
                '0001' => array('套装'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress')),
                '0003' => array('西服'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress')),
                '0004' => array('西裤'=>array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress')),
                '0005' => array('马甲'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress')),
                '0006' => array('衬衣'=>array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress')),
                '0007' => array('大衣'=>array('正装'=>'bformal','休闲'=>'bcasual')),
            );

            $n=0;
            if($diy_cate) {
                foreach($diy_cate as $key=>$cate) {
                    foreach($cate as $kk=>$vv){
                        $custome_cate_arr[$n] = array(
                            'cate_name' => $kk,
                            'cate_id'   => $key,
                        );
                        $m=0;
                        foreach($vv as $k=>$v){
                            $custome_cate_arr[$n]['diy_url'][$m]['style']=$k;
                            $custome_cate_arr[$n]['diy_url'][$m]['url']=DIYURL.$key.'-'.$v.'-'.$api_token.'.html?source=app';
                            $m++;
                        }
                        $n++;
                    }
                }
            }
            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'cus_cate'   => !empty($custome_cate_arr) ? $custome_cate_arr : '',
                    'success'    => '分类获取成功',
                ),
            );
            return $json->encode($return);

        }

        /**
         * 获取某类别下的单件样衣列表
         *
         * @param String $cate  分类； String $order  排序;int $pageSize  每页显示的个数；int $pageIndex 第几页；
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getAllCustomList($cate, $part_arr1, $part_arr2, $part_arr3, $part_arr4, $max_price, $min_price, $cstSex, $order, $pageSize, $pageIndex)
        {
            global $json;
            $db = db();
            $customs = m('customs');
            $return = array();
            if($order == 1) {
                $order = ' cst_likes DESC ';//人气排序
            }
            if($order == 2) {
                $order = ' cst_views DESC, cst_sales DESC, cst_likes DESC ';//综合排序
            }
            $condtions = '';
            //单品分类
            if(!empty($cate)){
                $condtions .= ' AND c.cst_cate = '.$cate;
            }
            //单品性别
            if(!empty($cstSex)){
                $condtions .= ' AND c.cst_sex_cate ='.$cstSex;
            }
            //面料属性搜索
            if(!empty($part_arr1) || !empty($part_arr2) || !empty($part_arr3) || !empty($part_arr4)){
                $indata = "'{$part_arr1}', '{$part_arr2}', '{$part_arr3}', '{$part_arr4}'";
                $condtions .= " AND pa.attr_value in ({$indata})";
            }

            //价格区间
            if(!empty($max_price)) {
                $condtions .= " AND c.cst_price <= {$max_price} AND c.cst_price > {$min_price}";
            }

            $limit = $pageSize*($pageIndex-1).','.$pageSize;
            $custom_sql = "SELECT count(pa.part_id) as count, c.cst_id, c.cst_name, c.cst_price, c.cst_image from ".DB_PREFIX."customs c
		                LEFT JOIN ".DB_PREFIX."customs_parts cp ON cp.cst_id= c.cst_id
						right JOIN ".DB_PREFIX."part_attr pa ON cp.pt_id= pa.part_id
						WHERE 1=1 ".$condtions." group by c.cst_id limit ". $limit;
            $customList = $db->getAll($custom_sql);

            /*
		//属性查询
	    $customList = $customs->find(array(
	        'conditions' => $condtions,
	        'fields'     => 'cst_id, cst_name, cst_dis_image, cst_image, cst_sex_cate, service_fee',
			'limit'		 => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'      => $order,
			'count'      => true,
	        'index_key'  => '',
	    ));


		//获得总数
		$customs_count = $customs->get(array(
			'conditions' => $condtions,
			'fields'     => 'count(*) as count',
	    ));
		*/

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'customList' => $customList,
                    'count'      => $customs_count,
                ),
            );
            return $json->encode($return);

        }

        /**
         * 用户获得邀请码
         *
         * @param string $token    手机；string $type  邀请码类型
         *
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getInviteCode($token)
        {
            global $json;
            $userinfo = getToken($token);
            $user_id = $userinfo['user_id'];
            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            if($userinfo['invite']) {
                //组装返回数据
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'invite' => $userinfo['invite'],
                        'success' => '邀请码获取成功',
                    ),
                );
                return $json->encode($return);

            }
            $code = $this->make_coupon_card();

            $data = array('invite'=>$code);
            $member_mod  = m('member');
            $res = $member_mod->edit("user_id = $user_id",$data);
            if($res) {
                //组装返回数据
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'invite' => $code,
                        'success' => '邀请码获取成功',
                    ),
                );
                return $json->encode($return);
            } else {

                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '邀请码获取失败',
                    )
                );
                return $json->encode($return);
            }

        }

        /**
         * 列出我推荐安装app用户(只有申请入住成功才显示)
         *
         * @param string $token    手机；string $type  邀请码类型
         *
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getInviteUser($token, $pageSize, $pageIndex)
        {
            global $json;
            $db = db();
            $img_url = LOCALHOST1."/";
            $userinfo = getToken($token);

            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $store_id = $userinfo['user_id'];

            $limit = $pageSize*($pageIndex-1).','.$pageSize;
            $custom_sql = "SELECT s.store_id, s.owner_name, s.tel, m.avatar as store_logo, mi.money, mi.add_time from ".DB_PREFIX."store s
		                LEFT JOIN ".DB_PREFIX."member_invite mi ON mi.invitee = s.store_id  LEFT JOIN ".DB_PREFIX."member m ON m.user_id = s.store_id
						WHERE 1=1 and mi.inviter={$store_id} and s.state=1 order by mi.add_time desc limit ". $limit;
            $referees = $db->getAll($custom_sql);

            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            foreach($referees as $key=>$val) {
                //获得用户头像
                $avatar = $objAvatar->avatar_show($val['store_id'], 'big');
                $referees[$key]['store_logo'] = $avatar;
                $referees[$key]['money'] = number_format(floor($val['money']), 2);
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'referees' => !empty($referees) ? $referees :'',
                ),
            );
            return $json->encode($return);
        }

        /**
         * 生成唯一字符串
         *
         * @param string $phoneNum 手机; string $type 短信发送类型
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function make_coupon_card() {
            $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $rand = $code[rand(0,25)]
                .strtoupper(dechex(date('m')))
                .date('d').substr(gmtime(),-5)
                .substr(microtime(),2,5)
                .sprintf('%02d',rand(0,99));
            for(
                $a = md5( $rand, true ),
                $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
                $d = '',
                $f = 0;
                $f < 8;
                $g = ord( $a[ $f ] ),
                $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
                $f++
            );
            return $d;
        }

          function make_member_invite(){
        
            global $json;
            $return  = array();
            $m       = &m('member');
            $info = $m->find(array(
                'conditions'=>"1=1",
                'fields'=>'invite',
            ));
            
            $result = array();
            $temp   = array();
            foreach ($info as $key=>$val){
        
                $result[]= $val['invite'];
                foreach($result as $k=> $v){
                    if(substr($v,0,1) =='Y'){
                        $temp[$k] = $v;
                    }else{
        
                        $invite_num = 'Y00000001';
                    }
                }
            }
            
            $num = array();
            foreach ($temp as $key => $value) {
                $num[] = substr($value, 2,8);
            }
        
            $big = maxMin($num);
            $invite_num = sprintf("%08d", $big+1);
            $invite_num = 'Y'.$invite_num;
            return $invite_num;
        
        }





        /**
         * 套装（主题）详细页面
         *
         * @param Int $id   套装id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getGoodsDetail($id)
        {
            global $json;
            $return = array();
            if(!$id) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '套装不存在',
                    )
                );
                return $json->encode($return);
            }

            $_dis_mod  = m('dissertation');
            $_link_mod = m('links');
            $_cus_mod  = m('customs');
            $info = $_dis_mod->get_info($id);
            if(empty($info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '套装不存在',
                    )
                );
                return $json->encode($return);
            }

            $links=$_link_mod->find(array(
                "conditions" => "d_id='$id'",
            ));

            $link = array();
            $sort = array();
            foreach ($links as $key=>$val) {
                $link[] = $val['c_id'];
                if($val['lorder']) {
                    $sort[$val['lorder']] = $val['c_id'];
                }else{
                    $sort[] = $val['c_id'];
                }
            }
            $customs=$_cus_mod->find(array(
                'conditions' => db_create_in($link,"cst_id"),
            ));

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'customs' => $customs,
                    'link'    => $link,
                    'sort'    => $sort,
                    'info'    => $info,
                )
            );
            return $json->encode($return);

        }

        /**
         * 基本款详情页面
         *
         * @param Int $id   基本款产品id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getCustomsDetail($id)
        {
            global $json;
            $_customs_mod =  m('customs');
            /*
		$cs =& cs();
		//获取整理diy数据
		$data     = $cs->get_basis_info($id, 1);
		$diy_data = $this->_format_mobile_data($data);*/

            //$info = $_customs_mod->get($id);
            $info = $_customs_mod->get(array(
                'conditions' => "cst_id = ({$id})",
                'fields'     => 'cst_id, cst_cate, cst_name, cst_image, cst_price, cst_market, cst_content, cst_description, cst_sn, link_cst',
            ));
            //获得图片相册
            $_gallery_mod = &m("gallery");
            $gallery_list = $_gallery_mod->find(array(
                'conditions' => "cst_id = '{$id}'",
                'fields'     => "img",
                'order'      => 'id ASC',
                'index_key'  => '',
            ));

            /* 关联相同基本款
		$clist = array();
		if($info["link_cst"]) {
			$links = $_customs_mod->find(array(
				'conditions' => "cst_id IN ({$info["link_cst"]})",
				'fields'     => 'cst_id, cst_name, cst_image',
			));
			$num = 0;
			$group =1;
			foreach($links as $key => $val) {
				$num ++;
				$clist[$group][] =$val;
				if($num % 5 == 0){
					$group ++;
				}
			}
		}*/

            //获取标准尺码
            $size =  Constants::$sizelParent[$info['cst_cate']];//尺寸
            $datasize = array();
            foreach($size as $val) {
                $datasize[]['size'] = $val;

            }
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'info'         => $info,//基本款产品简介
                    'gallery_list' => $gallery_list,//基本款产品相册
                    'size'         => $datasize,//基本款产品相册
                    //'clist'    => $clist,//相关基本款
                    //'diy_data' => $diy_data,//diy数据
                )
            );

            return $json->encode($return);
        }

        /**
         * 基本款-diy数据整理格式化
         *
         * @param Array $data  diy数据
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function _format_mobile_data($data)
        {
            $return = array();
            #面料选择|里料设计|纽扣选择|款式设计|个性签名
            foreach ($data['data'] as $key=>$v) {
                foreach ($data['data'][$key] as $partid => $r) {
                    if (in_array($partid, $data['fabric'])) {
                        $temp = array();
                        $k = 1;
                        $return['data'][$k]['name'] = '面料选择';
                        $temp = $r;
                    }

                    if (in_array($partid, $data['material'])) {
                        $temp = array();
                        $k = 2;
                        $return['data'][$k]['name'] = '里料设计';
                        $temp = $r;

                    }
                    if (in_array($partid, $data['buttons'])){
                        $temp = array();
                        $k = 4;
                        $return['data'][$k]['name'] = '纽扣设计';
                        $temp = $r;
                    }
                    if (in_array($key, $data['style'])){
                        $temp = array();
                        $k = 3;
                        $return['data'][$k]['name'] = '款式设计';
                        foreach ($v as $p=>$info) {
                            foreach ($info['part'] as $cid=>$value) {
                                $v[$p]['name'] = $data['category'][$p]['cate_name'];
                                if (!in_array($value['info']['cate_id'], Constants::$graphRuleoutParentCid)){//工艺下不纳入出图规则计算
                                    $v[$p]['part'][$cid]['info']['alias'] = $data['category'][$value['info']['cate_id']]['alias'];
                                }
                            }
                        }
                        $temp['part'] = $v;
                    }

                    if (in_array($partid, Constants::$signatureParent)) {
                        $temp = array();
                        $k = 5;
                        $return['data'][$k]['name'] = '个性签名';
                        $temp = $r;
                    }

                    #别名处理
                    if (3!=$k){
                        foreach ($temp['part'] as $p=>$info) {
                            foreach ($info as $cid=>$value) {

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

            $data['data']  = $return['data'];//关联基本款
            $data['cinfo'] = $return['customs'];//关联基本款
            //$data['size']  = $return['size'];//关联基本款
            //$data['menu']  = range(1,5);//左侧大类
            //$data['imgurl'] = SITE_URL.'/'.IMG_PATH.'/custom/'.$data['customs']['cst_id'].'/';//左侧大类
            //$data['sequence'] = json_encode($data['key_sequence']);//左侧大类
            //$data['consumption'] = json_encode($data['consumption']);//单耗
            //$data['processaa'] = json_encode($data['process']);//工艺
            //print_r($data);exit;
            return $data;
        }



        /**
         * 获取我的收获地址
         *
         * @param int  $user_id；int user_id 用户的id
         *
         * @access protected
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function getConsigneeList($token)
        {

            global $json;
            $m       = m('member');
            $address = m('address');
            $return    = array();
            $user_info = getToken($token);

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);

            }

            $user_id      = $user_info['user_id'];
            $address_list = $address->find(array(
                'join'       => 'belongs_to_member',
                'conditions' => 'address.user_id='.$user_id,
                'fields'     => 'address.*',
                'order'      => 'addr_id DESC',//综合排序
                'index_key'	 => '',

            ));

            //默认地址
            $def_addr = 0;
            if($user_info['def_addr']) {
                $def_addr = $user_info['def_addr'];
            } else {
                $end_id = current($address_list);
                $def_addr = $end_id['addr_id'];
            }

            if($address_list){
                foreach($address_list as $key=>$val) {
                    if($val['addr_id'] == $def_addr) {
                        $address_list[$key]['def_addr'] = 1;
                    }else{
                        $address_list[$key]['def_addr'] = 0;
                    }
                    if($val['region_id']) {
                        $region_mod = m('region');;
                        $region_list = $region_mod->find(array(
                            'conditions' => db_create_in($val['region_id'], 'region_id'),
                            'fields'     => 'region_name',
                            'index_key'  => '',
                        ));
                        $region_name_arr = array();
                        $region_name_str = array();
                        if($region_list) {
                            foreach($region_list as $v ) {
                                $region_name_arr[] = $v['region_name'];
                            }
                            $region_name_str = implode(",", $region_name_arr);
                        }
                        $address_list[$key]['region_name'] = !empty($region_name_str) ? $region_name_str : '';
                    }
                }
            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'address' => !empty($address_list) ? $address_list : '',
                )
            );

            return $json->encode($return);

        }

        /**
         * 添加收获地址
         *
         * @param object $data 地址信息； String $token 用户标识
         *
         * @access protected
         * @author cyrus <2621270755@qq.com>
         * @return void
         */
        //http://local.soaapi.mfd.com/soap/user.php?act=addAddress&token=eda6b5ebb68702e0e49841cb9aeb50d3&al_name=aaaa&userName=bbbbb&region_id=2,3,6&region_name=中国,山东,青岛&address=红领&phonetel=15092283823&phonetmob=15092283823&is_def=0
        public function addAddress($data, $token)
        {
            global $json;
            $add_mod = m('address');

            $m_mod   = m('member');
            $return = array();
            $user_info   = getToken($token);
            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);

            } else {
                $user_id     = $user_info['user_id'];

                $al_name     = isset($data->al_name) ? $data->al_name : '';//地址别名名称
                $userName    = isset($data->consignee) ? $data->consignee : '';//收货人
                $region_id   = isset($data->region_id) ? $data->region_id : '';//国省市id
                $region_name = isset($data->region_name) ? $data->region_name : '';//国,省,市
                $address     = isset($data->address) ? $data->address : '';//详细地址
                $zipcode     = isset($data->zipcode) ? $data->zipcode : '';//邮编
                $phonetel    = isset($data->phone_mob) ? $data->phone_mob : '';//座机
                $phonemob    = isset($data->phone_mob) ? $data->phone_mob : '';//手机
                $email       = isset($data->email) ? $data->email : '';//邮箱地址
                $is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认

                $arr = array();

                $arr['user_id'] = $user_id;
                if($userName) {
                    $arr['consignee'] = $userName;
                }
                if($region_id) {
                    $arr['region_id'] = $region_id;
                }
                if($region_name){
                    //处理region_name
                    $region_name_str = '';
                    $region_name_arr = explode(',', $region_name);
                    if($region_name_arr) {
                        $region_name_str = implode('  ', $region_name_arr);
                    }
                    $arr['region_name'] = $region_name_str;
                }
                if($al_name) {
                    $arr['al_name'] = $al_name;
                }
                if($address) {
                    $arr['address'] = $address;
                }
                if($zipcode) {
                    $arr['zipcode'] = $zipcode;
                }
                if($phonetel) {
                    $arr['phone_tel'] = $phonetel;
                }
                if($phonemob) {
                    $arr['phone_mob'] = $phonemob;
                }
                if($email) {
                    $arr['email'] = $email;
                }
                $res = $add_mod->add($arr);

                //如果设为默认地址
                if($is_def == 1) {
                    $m_mod->edit($user_id, array("def_addr" => $res));
                }

                if($res) {
                    $return = array(
                        'statusCode' => 1,
                        'result' => array(
                            'success'       => '添加成功',
                        )
                    );

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 103,
                            'msg'       => '地址添加失败',
                        )
                    );
                }

                return $json->encode($return);
            }

        }

        /**
         * 修改收获地址
         *
         * @param object $data 地址信息； String $token 用户标识
         *
         * @access protected
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function editAddress($data, $token)
        {
            global $json;
            $add_mod = m('address');
            $m_mod   = m('member');
            $return  = array();
            $token     = $data->token;
            $addr_id   = $data->addr_id;
            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];

            if(empty($user_id)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            if(empty($addr_id)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '没有要修改的地址',
                    )
                );
                return $json->encode($return);

            }
            $al_name     = isset($data->al_name) ? $data->al_name : '';
            $userName    = isset($data->consignee) ? $data->consignee : '';
            $region_id   = isset($data->region_id) ? $data->region_id : '';
            $region_name = isset($data->region_name) ? $data->region_name : '';
            $address     = isset($data->address) ? $data->address : '';
            $zipcode     = isset($data->zipcode) ? $data->zipcode : '';
            $phonetel    = isset($data->phone_tel) ? $data->phone_tel : '';
            $phonemob    = isset($data->phone_mob) ? $data->phone_mob : '';
            $email       = isset($data->email) ? $data->email : '';
            $is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认

            $arr = array();

            if($region_name){
                //处理region_name
                $region_name_str = '';
                $region_name_arr = explode(',', $region_name);
                if($region_name_arr) {
                    $region_name_str = implode('  ', $region_name_arr);
                }
                $arr['region_name'] = $region_name_str;
            }
            if($userName) {
                $arr['consignee'] = $userName;
            }
            if($region_id){
                $arr['region_id'] = $region_id;
            }
            /*  if($region_name) {
	        $arr['region_name'] = $region_name;
	    } */
            if($al_name) {
                $arr['al_name'] = $al_name;
            }
            if($address) {
                $arr['address'] = $address;
            }
            if($zipcode) {
                $arr['zipcode'] = $zipcode;
            }
            if($phonetel) {
                $arr['phone_tel'] = $phonetel;
            }
            if($phonemob) {
                $arr['phone_mob'] = $phonemob;
            }
            if($email) {
                $arr['email'] = $email;
            }

            $res = $add_mod->edit("addr_id='{$addr_id}'",$arr);
            //如果设为默认地址
            if($is_def == 1) {
                $m_mod->edit($user_id, array("def_addr" => $addr_id));
            }
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '地址修改成功',
                )
            );

            return $json->encode($return);

        }

        /**
         * 删除收获地址
         *
         * @param int $addr_id 地址id； String $token 用户标识
         *
         * @access protected
         * @author cyrus <2621270755@qq.com>
         * @return void
         */
        public function delAddress($addr_id, $token)
        {
            global $json;
            $return = array();
            $add_mod = m('address');
            $m_mod = m('member');
            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            if(empty($addr_id)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '参数错误',
                    )
                );
                return $json->encode($return);
            }
            $addr_reg=$add_mod->get(array(
                'conditions' =>"addr_id={$addr_id}",
            ));
            if($addr_reg['user_id'] != $user_info['user_id']){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '删除的不是该会员的地址',
                    )
                );
                return $json->encode($return);
            }

            if($user_info['def_addr'] == $addr_id){

                $nex_addr=$add_mod->get(array(
                    'conditions' =>"user_id='{$user_info['user_id']}' AND addr_id <> '{$addr_id}'" ,
                    'order' => "addr_id DESC",
                ));

                $res=$m_mod->edit($user_info['user_id'], array("def_addr" => $nex_addr['addr_id']));
            }
            $res = $add_mod->drop("addr_id=$addr_id");
            if($res) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '地址删除成功',
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
            return $json->encode($return);

        }

        /**
         * 批量删除收获地址
         *
         * @param stirng $addr_ids 地址id组（2,4,5,6,7）； String $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function delLotAddress($addr_ids, $token)
        {
            global $json;
            $db = db();
            $return = array();
            $add_mod = m('address');

            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            //执行批量删除操作
            $addr_id_arr = array_filter(explode(",", $addr_ids));
            if($addr_id_arr) {
                foreach($addr_id_arr as $addr_id) {
                    $delSql = "DELETE FROM ".DB_PREFIX."address where addr_id =".$addr_id;
                    $res = $db->query($delSql);
                }
            }

            if($res) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '地址批量删除成功',
                    )
                );

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '地址批量删除失败',
                    )
                );

            }
            return $json->encode($return);

        }

        /**
         * 设置默认收货地址
         *
         * @param int $addr_id 地址id； String $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function setDefAddr($addr_id, $token)
        {
            global $json;
            $return = array();
            $m = m('member');
            $user_info = getToken($token);

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );

                return $json->encode($return);
            }
            if($user_info['def_addr'] == $addr_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '已是默认',
                    )
                );
                return $json->encode($return);

            }

            $user_id = $user_info['user_id'];

            $res     = $m->edit('user_id='.$user_id, array('def_addr'=>$addr_id));
            if($res) {
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'success' => '设为默认成功',
                    )
                );

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '默认地址设置错误',
                    )
                );
            }

            return $json->encode($return);

        }
        
        /**
         * 获取我的宠物列表
         *
         * @param string $token
         *
         * @access protected
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function getPetList($token)
        {
        
        	global $json;
        	$m       = m('member');
        	$pet_mod = m('pet');
        	$return    = array();
        	$user_info = getUserInfo($token);
        
        	if(empty($user_info)) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => $token,
        						'msg'       => '账号不存在',
        				)
        		);
        		return $json->encode($return);
        
        	}
       
        	$user_id      = $user_info['user_id'];
        	$pet_list = $pet_mod->find(array(
        			'join'       => 'belongs_to_member',
        			'conditions' => 'pet.user_id='.$user_id,
        			'fields'     => 'pet.*',
        			'order'      => 'pet_id DESC',//综合排序
        			'index_key'	 => '',
        
        	));
        	//默认地址
        	$def_pet = 0;
        	if($user_info['def_pet']) {
        		$def_pet = $user_info['def_pet'];
        	} else {
        		$end_id = current($pet_list);
        		$def_pet = $end_id['pet_id'];
        	}
        
        	if($pet_list){
        		foreach($pet_list as $key=>$val) {
        			if($val['pet_id'] == $def_pet) {
        				$pet_list[$key]['def_pet'] = 1;
        			}else{
        				$pet_list[$key]['def_pet'] = 0;
        			}
/*         			if($val['region_id']) {
        				$pettype_mod = m('pettype');;
        				$type_list = $pettype_mod->find(array(
        						'conditions' => db_create_in($val['region_id'], 'type_id'),
        						'fields'     => 'type_name',
        						'index_key'  => '',
        				));
        				$type_name_arr = array();
        				$type_name_str = array();
        				if($type_list) {
        					foreach($type_list as $v ) {
        						$type_name_arr[] = $v['type_name'];
        					}
        					$type_name_str = implode(",", $type_name_arr);
        				}
        				$pet_list[$key]['type_name'] = !empty($type_name_str) ? $type_name_str : '';
        			} */
        			if($val['region_name']){
        				$region_name=$val['region_name'];
        				$pet_list[$key]['type_name']=strval( end(explode(' ',$region_name )));
        			}else{
        				$pet_list[$key]['type_name']='';
        			}
        			if($val['region_id']){
        				$region_id=$val['region_id'];
        				$pet_list[$key]['type_id']=strval( end(explode(',', $region_id)));
        			}else{
        				$pet_list[$key]['type_id']='';
        			}
        			
        			if(is_null($val['url'])){
        				$pet_list[$key]['url']='';
        			}
        		}
        	}
        
        
        	$return = array(
        			'statusCode' => 1,
        			'result' => array(
        					'pets' => !empty($pet_list) ? $pet_list : array(),
        			)
        	);
        
        	return $json->encode($return);
        
        }
        /**
        *-----------------------------------------------------------
        *上传宠物头像
        *-----------------------------------------------------------
        *@param file $pic 宠物头像图片； String $token 用户标识
        *@access public
        *@author cyrus <2621270755@qq.com>
        *@date 2016年6月3日
        *@version 1.0
        *@return 
        */
        public function addPetAvatar($token,$pic){
	        global $json;
	        $real_dir  = ROOT_PATH."/upload/petAvatar/";
	        include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
	        $imageTool = new ImageTool();
	        $uniqueString = $imageTool->getUniqueString();
	        $new_name     = date('YmdHis').'_' . $uniqueString . strrchr($pic['name'], '.');
	        $sub_dir      = date('Ym');
	        if(!is_dir($real_dir.$sub_dir)) {
	            mkdir($real_dir.$sub_dir, 0777, true);
	        }
	        
	        
	       	$res = file_put_contents($real_dir.$sub_dir.'/'.$new_name, file_get_contents($pic['tmp_name']));
	        if(!$res) {
	            $return = array(
	                'statusCode' => 0,
	                'error' => array(
	                    'errorCode' => 103,
	                    'msg'       => '保存文件失败',
	                ),
	            );
	           return $json->encode($return);
	        }
	        $pic=SITE_URL.'/upload/petAvatar/'.$sub_dir.'/'.$new_name;

	        
	        $return = array(
	        		'statusCode' => 1,
	        		'result'     => array(
	        				'success' => '图片上传成功',
	        				'pic_url'    => $pic,
	        		),
	        );
	        return $json->encode($return);
        }
        
        /**
         * 添加我的宠物
         *
         * @param object $data 地址信息； String $token 用户标识
         *
         * @access protected
         * @author cyrus <2621270755@qq.com>
         * @return void
         */
        //http://local.soaapi.mfd.com/soap/user.php?act=addAddress&token=eda6b5ebb68702e0e49841cb9aeb50d3&al_name=aaaa&userName=bbbbb&region_id=2,3,6&region_name=中国,山东,青岛&address=红领&phonetel=15092283823&phonetmob=15092283823&is_def=0
        public function addPet($data, $token)
        {
        	global $json;
        	$add_mod = m('pet');
        	$pettype_mod=m('pettype');
        	$m_mod   = m('member');
        	$type_ids=array();
        	$return = array();
        	$type_name_arr='';
        	$user_info   = getToken($token);
        	if(!$user_info) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 100,
        						'msg'       => '账号不存在',
        				)
        		);
        		return $json->encode($return);
        
        	} else {
        		$user_id     = $user_info['user_id'];
        
        		$url     = isset($data->url) ? $data->url: '';//宠物头像
        		$name    = isset($data->name) ? $data->name : '';//收货人
        		$type_id   = isset($data->type_id) ? $data->type_id : '';//品种id
        		$gender    = isset($data->gender) ? $data->gender: '';//性别
        		$birthday     = isset($data->birthday) ? $data->birthday: '';//出生日期
        		$summary    = isset($data->summary) ? $data->summary: '';//简介
        		$is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认
        
        		$arr = array();
        
        		$arr['user_id'] = $user_id;
        		if($name) {
        			$arr['name'] = $name;
        		}else{
        			$this->errorCode = 101;
        			$this->msg = '请填写宠物昵称';
        			return $this->eresult();
        		}
        		
        		if($type_id) {
        			$type_ids=$pettype_mod->get_parents($type_id);
        			
        			$type_id=join(',', $type_ids);
        			$arr['region_id'] = $type_id;
        		}else{
        			$this->errorCode = 101;
        			$this->msg = '请选择宠物类型';
        			return $this->eresult();
        		}
        		if($type_ids){
        			//处理region_name
        			foreach ($type_ids as $k=>$v){
        				$type_name=$pettype_mod->getTypeName($v);
        				
        				$type_name_arr.=" ".$type_name;
        			}
        			$arr['region_name'] = trim($type_name_arr);
        			if(!$type_name_arr){
        				$this->errorCode = 101;
        				$this->msg = '没有找到对应品类名称';
        				return $this->eresult();
        			}
        		}
        		if($gender) {
        			$arr['gender'] = $gender;
        		}else{
        			$this->errorCode = 101;
        			$this->msg = '性别不能为空';
        			return $this->eresult();
        		}
        		if($birthday) {
        			$arr['birthday'] = $birthday;
        		}else{
        			$this->errorCode = 101;
        			$this->msg = '生日不能为空';
        			return $this->eresult();
        		}
        		if($summary) {
        			$arr['summary'] = $summary;
        		}else{
        			$this->errorCode = 101;
        			$this->msg = '简介不能为空';
        			return $this->eresult();
        		}
        		if($url) {
        			$arr['url'] = $url;
        		}

        		$res = $add_mod->add($arr);
        
        		//如果设为默认地址
        		if($is_def == 1) {
        			$m_mod->edit($user_id, array("def_pet" => $res));
        		}
        
        		if($res) {
        			$return = array(
        					'statusCode' => 1,
        					'result' => array(
        							'success'       => '添加成功',
        					)
        			);
        
        		} else {
        			$return = array(
        					'statusCode' => 0,
        					'error' => array(
        							'errorCode' => 103,
        							'msg'       => '宠物添加失败',
        					)
        			);
        		}
        
        		return $json->encode($return);
        	}
        
        }
        
        /**
         * 修改我的宠物
         *
         * @param object $data 宠物信息； String $token 用户标识
         *
         * @access protected
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function editPet($data, $token)
        {
        	global $json;
        	$pet_mod = m('pet');
        	$m_mod   = m('member');
        	$pettype_mod=m('pettype');
        	$type_ids=array();
        	$return  = array();
        	$type_name_arr='';
        	$pet_id   = $data->pet_id;
        	$user_info = getToken($token);
        	$user_id   = $user_info['user_id'];
        
        	if(empty($user_id)) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 100,
        						'msg'       => '账号不存在',
        				)
        		);
        		return $json->encode($return);
        	}
        
        	if(empty($pet_id)) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 101,
        						'msg'       => '没有要修改的信息',
        				)
        		);
        		return $json->encode($return);
        
        	}
        	
        	$pet=$pet_mod->get($pet_id);
        	if($pet['user_id']!=$user_id){
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 101,
        						'msg'       => '该宠物不是此用户的',
        				)
        		);
        		return $json->encode($return);
        	}
        
        	$url     = isset($data->url) ? $data->url: '';//宠物头像
        	$name    = isset($data->name) ? $data->name : '';//宠物名称
        	$type_id   = isset($data->type_id) ? $data->type_id : '';//品种id
        	$gender    = isset($data->gender) ? $data->gender: '';//性别
        	$birthday     = isset($data->birthday) ? $data->birthday: '';//出生日期
        	$summary    = isset($data->summary) ? $data->summary: '';//简介
        	$is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认
        
        	$arr = array();
        

        	
        	if($name) {
        		$arr['name'] = $name;
        	}else{
        		$this->errorCode = 101;
        		$this->msg = '宠物名不能为空';
        		return $this->eresult();
        	}
        	if($type_id) {
				$type_ids = $pettype_mod->get_parents ( $type_id );
				$type_id = join ( ',', $type_ids );
				$arr ['region_id'] = $type_id;
			} else {
				$this->errorCode = 101;
				$this->msg = '品种id不能为空';
				return $this->eresult ();
			}
			if ($type_ids) {
				// 处理region_name
				foreach ( $type_ids as $k => $v ) {
					$type_name=$pettype_mod->getTypeName ( $v );
					$type_name_arr .= " " . $type_name;
				}
				$arr ['region_name'] = trim ( $type_name_arr);
				if (! $type_name_arr) {
					$this->errorCode = 101;
					$this->msg = '没有找到对应品种名称';
					return $this->eresult ();
				}
			}
	        if($gender) {
				$arr ['gender'] = $gender;
			} else {
				$this->errorCode = 101;
				$this->msg = '性别不能为空';
				return $this->eresult ();
			}
			if ($birthday) {
				$arr ['birthday'] = $birthday;
			} else {
				$this->errorCode = 101;
				$this->msg = '生日不能为空';
				return $this->eresult ();
			}
			if ($summary) {
				$arr ['summary'] = $summary;
			} else {
				$this->errorCode = 101;
				$this->msg = '简介不能为空';
				return $this->eresult ();
			}
        	if($url) {
        		$arr['url'] = $url;
        	}
        
        	$res = $pet_mod->edit("pet_id='{$pet_id}'",$arr);
        	//如果设为默认地址
        	if($is_def == 1) {
        		$res_def=$m_mod->edit($user_id, array("def_pet" => $pet_id));
        	}
        	if($res || $res_def){
        		$return = array(
        				'statusCode' => 1,
        				'result' => array(
        						'success' => '修改成功',
        				)
        		);
        	}else{
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 102,
        						'msg'       => '修改失败',
        				)
        		);
        	}
        
        	return $json->encode($return);
        
        }
        
        /**
         * 删除我的宠物
         *
         * @param int $pet_id 宠物id； String $token 用户标识
         *
         * @access protected
         * @author cyrus <2621270755@qq.com>
         * @return void
         */
        public function delPet($token,$pet_id)
        {
        	global $json;
        	$return = array();
        	$pet_mod = m('pet');
        	$m_mod = m('member');
        	$user_info = getToken($token);
			
        	if(empty($user_info)) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 100,
        						'msg'       => '账号不存在',
        				)
        		);
        		return $json->encode($return);
        	}
        
        	if(empty($pet_id)) {
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 101,
        						'msg'       => '参数错误',
        				)
        		);
        		return $json->encode($return);
        	}
        	$pet_reg=$pet_mod->get(array(
        			'conditions' =>"pet_id={$pet_id}",
        	));
        	if($pet_reg['user_id'] != $user_info['user_id']){
        		$return = array(
        				'statusCode' => 0,
        				'error' => array(
        						'errorCode' => 101,
        						'msg'       => '删除的不是该用户的宠物',
        				)
        		);
        		return $json->encode($return);
        	}
        
        	if($user_info['def_pet'] == $pet_id){
        
        		$nex_pet=$pet_mod->get(array(
        				'conditions' =>"user_id='{$user_info['user_id']}' AND pet_id <> '{$pet_id}'" ,
        				'order' => "pet_id DESC",
        		));
        
        		$res=$m_mod->edit($user_info['user_id'], array("def_pet" => $nex_pet['pet_id']));
        	}
        	$res = $pet_mod->drop("pet_id=$pet_id");
        	if($res) {
        		$return = array(
        				'statusCode' => 1,
        				'result' => array(
        						'success' => '宠物删除成功',
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
        	return $json->encode($return);
        
        }

        /**
         * 我的消息(只获取订单、咨询、系统相关消息)
         *
         * @param  String $token 用户标识;int $pageSize 页面大小；int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function myMessages($token, $pageSize, $pageIndex)
        {
            global $json;
            $userinfo = getToken($token);

            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id    = $userinfo['user_id'];


            $noread     = 0;//未读消息总数
            $message_mod = m("usermessage");
            $list = $message_mod -> find(array(
                'conditions' => "to_user_id =".$user_id,
                'order'      => "is_read asc,add_time DESC",
                'count'	     =>	"true",
                'limit'      => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                'index_key'	 => '',
            ));

            //循环操作，重新组装数据
            foreach($list as $key=>$val ) {
                //正则去掉a标签
                $str = $val['content'];
                //$str  = preg_replace("/<a[^>]*>(.*)<\/a>/is", "", $val['content']);
                $ti_arr = array('/前往查看/', '/点击此处/', '/查看详情/');

                $str = preg_replace($ti_arr, array("", "", ""), $str);
                $str = rtrim(rtrim($str), '，');
                $str = rtrim(rtrim($str), ', ');
                $list[$key]['content']      = $str;
                $list[$key]['str_content']  = trim(strip_tags($str));

                if($val['type'] == 1) {
                    $list[$key]['url_type']    = 'order';//交易提醒
                }
                if($val['type'] == 3) {
                    $list[$key]['url_type']    = 'single';//订单评论
                }
                if($val['type'] == 8) {
                    $list[$key]['url_type']    = 'html_sys';//默认系统消息
                }
                $sys_status = array(9,10,12,13,14,15,16,17);//系统消息 zxr add 17为返修
                if(in_array($val['type'], $sys_status) ) {
                    $list[$key]['url_type']    = 'sys';
                }

            }
            //print_exit($user_id);
            //获得未读消息总数
            $noread = $message_mod->get(array(
                'conditions' =>"1=1 and is_read =0 AND to_user_id =".$user_id,
                'fields'     => "count(1) as noread",
            ));

            $count = $message_mod->getCount();//获得总数
            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'messages' => !empty($list) ? $list : array(),
                    'count'    => $count,
                    'noread'   => $noread['noread'],
                ),
            );

            return $json->encode($return);

        }
        /**
         * 我的未读消息
         *
         * @param  String $token 用户标识
         *
         * @access protected
         * @author zhaoxr <773938880@qq.com>
         * @return void
         */
        public function myUnreadMessages($token)
        {
            global $json;

            $userinfo = getToken($token);

            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id    = $userinfo['user_id'];
            $message_mod = m("usermessage");

            //print_exit($user_id);
            //获得未读消息总数
            $noread = $message_mod->get(array(
                'conditions' =>"1=1 AND is_read =0 AND to_user_id =".$user_id,
                'count'  =>true,
            ));

            $count = $message_mod->getCount();//获得总数
            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'count'    => !empty($count)?$count:0,
                ),
            );

            return $json->encode($return);

        }
        /**
         * 删除我的消息
         *
         * @param  String $token 用户标识;string $msgtype  信息类型 ；int $pageSize 页面大小；int $pageIndex 当前页
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function delMyMessages($token, $msg_id)
        {
            global $json;
            $userinfo = getToken($token);

            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id = $userinfo['user_id'];
            $msg_id_arr = explode(',', $msg_id);

            $conditions = " to_user_id = {$user_id}  and ".db_create_in($msg_id_arr, 'id');
            $message_mod = m("usermessage");
            $re = $message_mod->drop($conditions);
            //组装返回数据
            if($re){
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '删除我的消息成功',
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '删除失败',
                    ),
                );
            }

            return $json->encode($return);

        }

        /**
         * 查看阅读我的消息
         *
         * @param  String $token 用户标识;int $msgid  信息id ;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function viewMyMessages($token, $msg_id)
        {
            global $json;
            $userinfo = getToken($token);

            if(!$userinfo) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id = $userinfo['user_id'];

            $message_mod = m("usermessage");
            $msg = $message_mod->get($msg_id);
            if(empty($msg)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '无对应消息',
                    ),
                );
                return $json->encode($return);
            }
            $res = $message_mod->edit("id='{$msg_id}' && to_user_id='{$user_id}'",array("is_read" => 1));
            if($res) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'       => '已阅读',
                    )
                );

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '修改失败',
                    ),
                );
            }
            return $json->encode($return);
        }

        /**
         * 即时聊天消息-获得客服列表
         *
         * @param  String $token 用户标识
         *
         * @access public
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function chatSeverList($token)
        {
            global $json;
            $return = array();
            $_admin_mod  = m('userpriv');
            $_mod_member = m("member");
            $_user_msg   = m("user_message");
            $_ol_mod     = m("online");

            $user_info  = getToken($token);

            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }

            /* 头像 add by xiao5 START */
            require(PROJECT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();


            //获得系统客服列表信息
            $admin_info = $_admin_mod->find(array(
                'conditions' => "user_priv.store_id = 0",
                'fields'     => 'member.nickname, member.user_id,  member.avatar',
                'join'       => 'mall_be_manage',
                'index_key'  => '',
                'count'      => true,
            ));

            $admin_info = array_filter($admin_info);
            $no_sort    = array();
            //获得当前用户与每个客服的聊天记录（未读消息）
            foreach($admin_info as $key => $info ) {
                //if(empty($info['user_id']) || $_admin_mod->check_system_manager($info['user_id'])) {//去空&&剔除超级管理员
                if(empty($info['user_id'])) {
                    unset($admin_info[$key]);
                    continue;
                }
                $admin_info[$key]['online'] = 0;//默认不在线
                //获得管理员头像
                $avatar = $objAvatar->avatar_show($val['member_id'], 'big');
                $admin_info[$key]['avatar'] = $avatar;

                //获得当前客服与当前用户的未读消息记录
                $noread_list = $_user_msg->find(array(
                    'conditions' => "is_read = '0' AND to_user = '1' AND to_user_id ={$user_info['user_id']}  AND from_user_id ={$info['user_id']} ",
                    'index_key'  => '',
                    'count'      => true,
                    'order'      => 'dateline ASC',
                ));
                $noread_count = $_user_msg->getCount();//未读数

                //获得当前客服是否在线
                $online = $_ol_mod->get(array(
                    "conditions" => "user_id = {$info['user_id']}",
                ));
                if($online) {
                    $admin_info[$key]['online'] = 1;
                }

                $admin_info[$key]['noread_count'] = $noread_count;
                $admin_info[$key]['noread_list']  = $noread_list;

                $no_count = count($noread_list)-1;
                $no_sort[$key]  = $no_count > 0 ?  $noread_list[$no_count]['dateline'] : $noread_list[0]['dateline'];//获得最新更新一条时间

                $admin_info[$key]['lastmsg_date'] = !empty($noread_list[$no_count]['dateline']) ? $noread_list[$no_count]['dateline'] :'';
                $admin_info[$key]['lastmsg_con']  = !empty($noread_list[$no_count]['content']) ? $noread_list[$no_count]['content'] :'';
            }

            array_multisort($no_sort, SORT_DESC, $admin_info);//排序，将按照信息更新时间

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'    => '客户获取成功',
                    'admin_list' => $admin_info,
                )
            );
            return $json->encode($return);

        }

        /**
         * 即时聊天消息-未读信息
         *
         * @param  String $token 用户标识；Int $serve_id 客服id
         *
         * @access public
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function chatSeverOne($token, $serve_id)
        {
            global $json;
            $return = array();
            $_user_msg = m("user_message");
            if(!$token) {
                $return = array(
                    'statusCode' => 0,
                    'result'     => array(
                        'success' => '用户未登录',
                    )
                );
                return $json->encode($return);
            }
            $user_info  = getToken($token);
            /*
	$_ol_mod     = m("online");
	$list = $_user_msg->find(array(
		'index_key'  => '',
		'count'      => true,
	));
	print_exit($list);*/
            $list = $_user_msg->find(array(
                'conditions' => "is_read = '0' AND to_user = '1' AND to_user_id = {$user_info['user_id']}  AND from_user_id = {$serve_id}",
                'index_key'  => '',
                'count'      => true,
            ));

            $count = $_user_msg->getCount();

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '已阅读',
                    'list'    => $list,//消息列表
                    'count'   => $count,
                )
            );
            return $json->encode($return);

        }

        /**
         * 即时聊天消息-发送图片
         *
         * @param  String $token 用户标识；Int $serve_id 客服id
         *
         * @access public
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function chatSeverImg($token, $chatimg)
        {
            global $json;
            $img_url = LOCALHOST1."/upload/chat/";
            $user_info = getUserInfo($token);

            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'result'     => array(
                        'success' => '用户未登录',
                    )
                );
                return $json->encode($return);
            }

            include PROJECT_PATH."/includes/ImageTool.class.php";
            $imageTool = new ImageTool(ROOT_PATH.'/upload/chat/');
            if ($chatimg) {
                $chatimgUrl = $imageTool->uploadImage($chatimg);
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '发送图片成功',
                    'chatimg'    => !empty($chatimgUrl) ? $img_url.$chatimgUrl : '',//返回图片路径
                )
            );
            return $json->encode($return);

        }

        /**
         * 添加收藏
         *
         * @param int $item_id 地址id； String $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function addCollect($token, $item_id, $type='alicaifeng')
        {
            global $json;
            $db = db();
            $return = array();
            if(!$token) {
                $return = array(
                    'statusCode' => 0,
                    'result'     => array(
                        'success' => '用户未登录',
                    )
                );
                return $json->encode($return);
            }
            if(!$item_id) {
                $return = array(
                    'statusCode' => 0,
                    'result'     => array(
                        'success' => '没有要收藏的项',
                    )
                );
                return $json->encode($return);
            }
            $user_info  = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );

                return $json->encode($return);
            }

            $user_id    = $user_info['user_id'];
            $sql        = "SELECT * FROM ".DB_PREFIX."collect WHERE user_id = $user_id and item_id='$item_id'  ";
            $is_collect = $db->getRow($sql);

            if($is_collect['item_id']) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '已经收藏过项',
                    )
                );
                return $json->encode($return);
            }

            $add_time = gmtime();
            $sql      = "INSERT INTO ".DB_PREFIX."collect (user_id, type, item_id, keyword, add_time) VALUES ('$user_id', '$type', '$item_id', 'cf', $add_time)  ";
            $res      = $db->query($sql);
            if($res) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'       => '收藏成功',
                    )
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '收藏失败',
                    )
                );
            }
            return $json->encode($return);
        }

        /**
         * 取消收藏
         *
         * @param int $item_id 地址id； String $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function delCollect($token, $item_id, $type)
        {
            global $json;
            $db = db();
            $return = array();
            if(!token) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            if(!$item_id) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '没有要取消的收藏',
                    )
                );
                return $json->encode($return);
            }

            $user_info  = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );

                return $json->encode($return);
            }
            $user_id    = $user_info['user_id'];
            $sql        = "SELECT * FROM ".DB_PREFIX."collect WHERE item_id='$item_id' and type='$type' and user_id=$user_id";
            $is_collect = $db->getRow($sql);

            if(!$is_collect['item_id']) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '没有该收藏',
                    )
                );
                return $json->encode($return);
            }

            $collect_mod       = & m('collect');
            $member_single_mod = & m('member_single');

            $res = $collect_mod->drop("item_id=".$item_id." and type='".$type."' and user_id=".$user_id);
            if(!$res) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '取消收藏失败',
                    )
                );
                return $json->encode($return);
            }
            //删除收藏数量
            if($type == 'single') {
                $member_single_mod->setDec(array("id"=>$item_id),"collection_num");
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'       => '取消收藏成功',
                )
            );

            return $json->encode($return);

        }

        /**
         * app首页
         *
         * @param NULL
         *
         * @access public
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function homePage()
        {
            global $json;
            $img_url = SITE_URL;
            $return  = array();
			//轮播图
			$shuffling_mod=&m('shuffling');
			$shufflinggroup_mod=&m('shufflinggroup');
			//首页轮播图
			$app_banner_code=APP_BANNER_CODE;
			$groups=$shufflinggroup_mod->get(array(
					'conditions'=>"code='{$app_banner_code}' and site_id=2"
			));
			$banners=array();
			if($groups){
				$banners=$shuffling_mod->find(array(
						'conditions'=>"status=1 and site_id=2 and groups=".$groups['id'],
						'fields'=>'id,name,site_id,link_url,groups,img,sort_order,status',
						'order'=>'sort_order ASC,id ASC',
				));
			}
			$banners=array_values($banners);
			//广告位
			$motif_mod=&m('motif');
			$motif_rel_content_mod=&m('motifrelcontent');
			$app_ad_code=APP_AD_CODE;
	        $columns = $motif_mod->find ( array (
	    			'conditions' => "m.is_show=1 and m.is_delete=0 and mg.code='{$app_ad_code}'",
	    			'order' => 'm.sort_order ASC,m.id ASC',
	    			'join' => 'has_location',
	    			'fields' =>'m.id,m.title,m.title_switch,m.subhead,m.subhead_switch,m.subhead_link,m.sort_order',
	    	) );
	    
	    	 
	    	if(is_array($columns)){
	    		foreach ($columns as $k=>$v){
	    			$relcontents=$motif_rel_content_mod->find(array(
	    					'conditions'=>"is_show=1 and parent_id=".$v['id'],
	    					'order'=>"sort_order ASC,id ASC",
	    					'fields'=>"mrc.id,mrc.link_url,mrc.title,mrc.intro,mrc.img"
	    			));
	    			$columns[$k]['rc']=array_values($relcontents);
	    		}
	    	}
	    	$columns=array_values($columns);
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'banners'   => !empty($banners) ? $banners: array(),
                    'ads'    => !empty($columns)?$columns:'',
                )
            );
            return $json->encode($return);
        }

        /**
         * 推荐裁缝列表
         *
         * @param NULL
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function storeRecommend()
        {
            global $json;
            $return = array();
            $tailor_tree = array();
            $store_mod   = & m('store');
            $tailor_list = $store_mod->find(array(
                'conditions' => 'state=1',
                'fields'     => 'store_id, store_name, popularity, service_num, store_logo',
                'limit'      => 4,
                'order'      => 'popularity DESC',
                'count'      => true,
            ));

            //获得店铺的好评率
            if(!empty($tailor_list)) {
                $comment_mod = m('ordercomment');
                foreach($tailor_list as $key=>$val) {
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
                    $tailor_list[$key]['hplv'] = $hplv;
                    //store logo
                    $store_logo = LOCALHOST1.'/'.$tailor_list[$key]['store_logo'];
                    $tailor_list[$key]['store_logo'] = !empty($store_logo) ? $store_logo : '';
                }
            }
            /*
        if(!empty($tailor_list)) {
			$group  = 0;
			$num    = 0;
			foreach($tailor_list as $key => $val){
				$num++;
				$tailor_tree[$group]['children'][$val["store_id"]] = $val;
				if($num%5 == 0) $group+=1;
			}
		}*/

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'tailor_tree' => !empty($tailor_list) ? $tailor_list : array(),
                    'success'     => '获取数据成功',
                )
            );

            return $json->encode($return);

        }

        /**
         * 裁缝--我的口碑
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function myMouth($token)
        {
            global $json;
            $db = db();
            $return = array();
            if(!$token) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '还未登录,请先登录',
                    )
                );
                return $json->encode($return);

            }

            $user_info  = getToken($token);

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);

            }
            $user_id    = $user_info['user_id'];
            $store_mod  = m('store');

            $comment_sql = "SELECT c.*, m.user_name, m.nickname, m.member_lv_id from ".DB_PREFIX."order_comment c LEFT JOIN ".DB_PREFIX."member m ON m.user_id= c.member_id WHERE c.status=1 AND c.tailor_id=".$user_id;
            $comments = $db->getAll($comment_sql);

            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();

            //获得用户头像
            foreach($comments as $key=>$val) {
                $avatar = $objAvatar->avatar_show($val['member_id'], 'big');
                $comments[$key]['avatar'] = $avatar;
            }
            $comment_mod = m('ordercomment');
            $comment_total = $comment_mod->get(array(
                'conditions'=>'status=1 and tailor_id='.$user_id,
                'fields'    =>'count(*) as c',
                'index_key'	=> '',
            ));

            $cp =  $comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND approve=1 ",
                'fields'    =>'count(*) as c',
            ));

            $zp = $comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND (approve=2 OR approve=3)",
                'fields'    =>'count(*) as c',
            ));

            $hp =$comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND (approve=4 OR approve=5)",
                'fields'    =>'count(*) as c',
            ));
            $hplv = $hp['c']/$comment_total['c'];
            $zplv = $zp['c']/$comment_total['c'];
            $cplv = $cp['c']/$comment_total['c'];

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'comments'      => !empty($comments) ? $comments : '',
                    'comment_total' => !empty($comment_total) ? $comment_total : '',//总评论数
                    'cp'            => $cp,//差评
                    'zp'            => $zp,//中评
                    'hp'            => $hp,//好评
                    'hplv'          => $hplv,//好评率
                    'zplv'          => $zplv,//中评率
                    'cplv'          => $cplv,//差评率
                )
            );

            return $json->encode($return);

        }

        /**
         * 获得某裁缝的口碑
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getStoreMouth($store_id)
        {
            global $json;
            $db = db();
            $store_mod  = m('store');
            $return = array();
            if(!$store_id) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '参数有误',
                    )
                );
                return $json->encode($return);

            }

            $store_info  = $store_mod->get("store_id=".$store_id);
            if(empty($store_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '无当前裁缝',
                    )
                );
                return $json->encode($return);

            }

            $comment_sql = "SELECT c.*, m.user_name, m.nickname, m.member_lv_id from ".DB_PREFIX."order_comment c LEFT JOIN ".DB_PREFIX."member m ON m.user_id= c.member_id WHERE c.tailor_id=$store_id";
            $comments = $db->getAll($comment_sql);

            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();

            //获得用户头像
            foreach($comments as $key=>$val) {
                $avatar = $objAvatar->avatar_show($val['member_id'], 'big');
                $comments[$key]['avatar'] = $avatar;
            }

            $comment_mod = m('ordercomment');
            $comment_total = $comment_mod->get(array(
                'conditions'=>'status=1 and tailor_id='.$store_id,
                'fields'    =>'count(*) as c',
                'index_key'	=> '',
            ));

            $cp =  $comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND approve=1 ",
                'fields'    =>'count(*) as c',
            ));

            $zp = $comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND approve=2 OR approve=3",
                'fields'    =>'count(*) as c',
            ));

            $hp =$comment_mod->get(array(
                'conditions'=>"status=1 and tailor_id='$user_id' AND approve=4 OR approve=5",
                'fields'    =>'count(*) as c',
            ));
            $hplv = $hp['c']/$comment_total['c'];
            $zplv = $zp['c']/$comment_total['c'];
            $cplv = $cp['c']/$comment_total['c'];

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'comments'      => !empty($comments) ? $comments : '',
                    'comment_total' => !empty($comment_total) ? $comment_total : '',//总评论数
                    'cp'            => $cp,//差评
                    'zp'            => $zp,//中评
                    'hp'            => $hp,//好评
                    'hplv'          => $hplv,//好评率
                    'zplv'          => $zplv,//中评率
                    'cplv'          => $cplv,//差评率
                )
            );

            return $json->encode($return);

        }

        /**
         * 裁缝--我的案例
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function myCase($token)
        {
            global $json;
            $db = db();
            $return = array();
            if(empty($token)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);

            }
            $user_info   = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];

            //获得案例
            $sql        = "SELECT * FROM ".DB_PREFIX."store_service p WHERE p.store_id='$user_id' ";
            $storephoto = $db->getAll($sql);
            if($storephoto){
                foreach($storephoto as $k=>$v){
                    $sqls        = "SELECT * FROM ".DB_PREFIX."storephoto  WHERE album_id='".$v['id']."' and store_id='$user_id'";
                    $store_service = $db->getAll($sqls);
                    if($store_service) {
                        //获取图片路径
                        foreach($store_service as $key=>$val){
                            $storephoto[$k]['photoitem'][]['url'] = getUserPhotoUrl('works', $val['url'],200);
                        }
                    }
                }

            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success'    => '获取成功',
                    'storephoto' => !empty($storephoto) ? $storephoto : '',
                ),
            );
            return $json->encode($return);

        }

        /**
         * 获得即拍即做列表
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function followedBeatList($token, $pageSize, $pageIndex)
        {
            global $json;
            $cate = 3;
            $user_info    = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];
            $page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;

            //获取信息
            $_userphoto_mod    = m('userphoto');
            $data = $_userphoto_mod->find(array(
                'conditions' => 'uid='.$user_id,
                'fields'     => 'id, url, album_id',
                'count'      => true,
                'limit'      => $page_list,
                'order'      => 'add_time desc',
                'index_key'	 => '',
            ));

            foreach($data as $k=>$v){
                if($v['url']) {
                    if($cate == 1) {
                        $data[$k]['url'] =getDesignUrl($v['url']);
                    } elseif($cate == 2) {
                        $data[$k]['url'] = getCameraUrl($v['url']);
                    } elseif($cate == 3) {
                        $data[$k]['url'] = getSingleUrl($v['url']);
                    }
                }
                /*
			if($v['album_id']){
    			$m = m('album');
    			$album = $m->getById($v['album_id']);
    			$data[$k]['album'] = $album['title'];
    		}*/
                //$user = array();
                //$user = getUinfoByUid($v['uid']);

                //$data[$k]['date'] = date('Y-m-d H:i:s',$v['add_time']);
                //$data[$k]['uname'] = $user['user_name'];

            }

            $count = $_userphoto_mod->getCount();

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success'  => '获取成功',
                    'count'    => $count ,
                    'beatinfo' => !empty($data) ? $data : '',
                ),
            );
            return $json->encode($return);

        }

        /**
         * 用户即拍即做创建相册数据
         *
         * @param string $token 用户标识 string $desc 简介留言； string $beatImages 图片名称；
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function addfollowedBeat($token, $desc, $beatImages)
        {
            global $json;
            $user_info    = getToken($token);
            $_userphoto_mod  = & m('userphoto');

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];

            $beatImage_arr = array();
            if(!empty($beatImages)) {
                $beatImage_arr = explode(',', $beatImages);
            }

            $data = array(
                'add_time'       => gmtime(),
                'clothingtypes'  => 0,
                'clothingstyles' => 0,
                'base_info'      => $desc,
                'uid'            => $user_id,
                'cate'           => 3,//即拍即做
                'url'            => $beatImage_arr['0'],
                'url2'           => $beatImage_arr['1'],
                'url3'           => $beatImage_arr['2'],
                'url4'           => $beatImage_arr['3'],
                'url5'           => $beatImage_arr['4'],
                'url6'           => $beatImage_arr['5'],
                'url7'           => $beatImage_arr['6'],
                'url8'           => $beatImage_arr['7'],
            );

            $single_id = $_userphoto_mod->add($data);

            if(empty($single_id)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '即拍即做创建失败！',
                    )
                );
                return $json->encode($return);
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success'    => '即拍即做相册创建成功！',
                ),
            );

            return $json->encode($return);
        }

        /**
         * 获得即拍即做相册详情
         *
         * @param string $token 用户标识；int $pid 相册id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function followedBeatView($token, $pid)
        {
            global $json;
            $user_info    = getToken($token);
            $_userphoto_mod         = & m('userphoto');
            $_userphotocomment_mod  = & m('userphotocomment');
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];

            //获取信息
            $data = $_userphoto_mod->get(array(
                'conditions' => 'id= '.$pid.' AND uid='.$user_id,
                'index_key'	 => '',
            ));

            $imgs[]['url']  = !empty($data['url'])?getSingleUrl($data['url']):'';
            $imgs[]['url'] = !empty($data['url2'])?getSingleUrl($data['url2']):'';
            $imgs[]['url'] = !empty($data['url3'])?getSingleUrl($data['url3']):'';
            $imgs[]['url'] = !empty($data['url4'])?getSingleUrl($data['url4']):'';
            $imgs[]['url'] = !empty($data['url5'])?getSingleUrl($data['url5']):'';
            $imgs[]['url'] = !empty($data['url6'])?getSingleUrl($data['url6']):'';
            $imgs[]['url'] = !empty($data['url7'])?getSingleUrl($data['url7']):'';
            $imgs[]['url'] = !empty($data['url8'])?getSingleUrl($data['url8']):'';

            $data['img'] = array();
            if($imgs){
                foreach($imgs as $val) {
                    if($val['url']) {
                        $data['img'][]['url'] = $val['url'];
                    }
                }
            }


            $data['img']   = array_filter($data['img']);
            $data['count'] = count($data['img']);//图片总数

            //获得在线交流信息
            $comment_data = $_userphotocomment_mod->find(array(
                'conditions' => 'comment_id='.$pid,
                'limit' => 10,
                'order' => "add_time desc",
                'count' => true,
                'index_key'	 => '',
            ));

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '相册获取成功',
                    'userphoto' => !empty($data) ? $data : '',
                    'comment'   => !empty($comment_data) ? $comment_data : '',
                ),
            );

            return $json->encode($return);
        }


        /**
         * 获得即拍即做添加回复
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function addfollowedComments($token, $content, $pid)
        {
            global $json;
            $user_info    = getToken($token);
            $_userphotocomment_mod  = & m('userphotocomment');

            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];

            $arr = array(
                'user_name'  => $user_info['user_name'],
                'content'    => trim($content),
                'add_time'   => gmtime(),
                'comment_id' => $pid,
                'cate'       => 0,
            );
            $this->_userphotocomment_mod->add($arr);

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '添加成功',
                ),
            );

            return $json->encode($return);

        }

        /**
         * 获得即拍即做的时时聊天
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function followedBeatComment($token, $pid, $pageSize, $pageIndex)
        {
            global $json;
            $user_info    = getToken($token);
            $_userphotocomment_mod  = & m('userphotocomment');
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;

            //获得在线交流信息
            $comment_data = $_userphotocomment_mod->find(array(
                'conditions' => 'comment_id='.$pid,
                'limit' => $page_list,
                'order' => "add_time desc",
                'count' => true,
                'index_key'	 => '',
            ));

            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            $_userphoto_mod = & m('member');
            //处理头像路径
            if($comment_data) {
                foreach($comment_data as $key=>$val) {
                    $user_info = $_userphoto_mod->get(array(
                        'conditions' => "user_name='".$val['user_name']."'",
                        'fields'     => 'user_id',
                    ));
                    //获得用户头像
                    $avatar = $objAvatar->avatar_show($user_info['user_id'], 'big');
                    $comment_data[$key]['avatar'] =  $avatar;
                }
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '获取成功',
                    'comment'   => !empty($comment_data) ? $comment_data : '',
                ),
            );

            return $json->encode($return);

        }


        /**
         * 用户即拍即做上传图片
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function uploadBeatImg($token, $beatImage)
        {
            global $json;

            if(!$beatImage) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '图片上传失败',
                    )
                );
                return $json->encode($return);
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success' => '图片上传成功',
                    'name'    => $beatImage,
                ),
            );

            return $json->encode($return);
        }

        /**
         * 用户即拍即做-删除相册
         *
         * @param string $token 用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function delBeatImg($token, $deltype, $photoId, $imgId )
        {
            global $json;
            $followed_beat    = m("followed_beat");
            $followed_beatimg = m("followed_beatimg");
            $user_info   = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            //查看当前相册是否关联订单，关联订单不能删除


            //删除整个相册
            if($deltype == 'photo') {
                $conditions_beat  = "user_id = ".$user_info['user_id']." AND id = $photoId";
                $conditions_img   = "album_id = $photoId";

                $rebeat = $followed_beat->drop($conditions_beat);
                $reimg  = $followed_beatimg->drop($conditions_img);

                if($rebeat) {
                    $return = array(
                        'statusCode' => 1,
                        'result'     => array(
                            'success' => '相册删除成功',
                        ),
                    );
                    return $json->encode($return);
                }
            }
            //删除相册中某张图片
            if($deltype == 'img') {
                $conditions_img   = "album_id = $photoId AND id = $imgId";
                $reimg = $followed_beatimg->drop($conditions_img);
                if($reimg) {
                    $return = array(
                        'statusCode' => 1,
                        'result'     => array(
                            'success' => '图片删除成功',
                        ),
                    );
                    return $json->encode($return);
                }
            }

            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '删除失败',
                )
            );
            return $json->encode($return);
        }
		/**
		*-----------------------------------------------------------
		*获取评论印象列表
		*-----------------------------------------------------------
		*@access public
		*@author cyrus <2621270755@qq.com>
		*@date 2016年5月31日
		*@version 1.0
		*@return 
		*/
        public function getImpressionArray(){
        	global $json;
        	$impression_arr = include_once ROOT_PATH.'/includes/impression.arrayfile.php';
//         	$impression_arr=array_values((array)$impression_arr);
        	$return = array(
        			'statusCode' => 1,
        			'result'     =>array(
        					'impressions'=>$impression_arr,
        			),
        	);
        		
        		
        	return $json->encode($return);
        }
        
        /**
        *-----------------------------------------------------------
        *获取评论中心列表
        *-----------------------------------------------------------
        *@param string $token 用户标识;int $comment 商品评价状态 0待评价 1已评价，int  $pageSize 页面大小, int $pageIndex 页码
        *@access public
        *@author cyrus <2621270755@qq.com>
        *@date 2016年6月1日
        *@version 1.0
        *@return json
        */
		public function getCommentList($token,$comment,$pageSize,$pageIndex){
			include_once ROOT_PATH.'/includes/libraries/diys.lib.php';
			include_once ROOT_PATH.'/vendor/autoload.php';
			
// 			
			global $json;
			$user_info   = getToken($token);
			$order_goods_mod=m('ordergoods');
			$detail_comment_mod    = m('detail_comment');
			if(empty($user_info)) {
				$return = array(
						'statusCode' => 0,
						'error' => array(
								'errorCode' => 100,
								'msg'       => '账号不存在',
						)
				);
				return $json->encode($return);
			}
			$conditions=" AND order_alias.status=40 AND order_goods.comment={$comment}";
			$page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
			$page['limit']=$page_list;
			$comment_obj=new Comment();
			$diys=new Diys();
			$comment_list=$comment_obj->get_order_comment($user_info['user_id'], $conditions, $page,'app');
			if(is_array($comment_list)){
				foreach ($comment_list as $k=>$v){
					if($v['goods_image']=='no pic'){
						$comment_list[$k]['goods_image']='';
					}
					if($v['type']=='fdiy'){
						$comment_list[$k]['spe_name']	=$comment_obj->getGoodParam($v['type'],$v['params']);
						$comment_list[$k]['goods_name']=$diys->get_cname($v['cloth'],'/','fdiy_management');
					}else{
						$comment_list[$k]['spe_name']	=$comment_obj->getGoodParam($v['type'],$v['params']);
					}

				}
				$comment_list=array_values($comment_list);
			}
			
			$return = array(
					'statusCode' => 1,
					'result'     =>array(
							'comment_list'=>$comment_list,
					),
			);
			
			
			return $json->encode($return);
		}
        /**
         * 消费者添加评价
         *
         * @param string $token 用户标识; int order_id 订单id； $pro_id 商品id, $cate 商品分类, $is_hide是否匿名评价, $star 评分, $content 内容, $rec_id 订单商品id,$impress 印象
         *
         * @access protected
         * @author cyrus<2621270755@qq.com>
         * @return void
         */
        public function addComment($token, $order_id, $pro_id, $cate, $is_hide, $star, $content, $rec_id,$impress)
        {
        	$impression_arr = include_once B2C_PATH.'/includes/impression.arrayfile.php';
            global $json;
            $user_info   = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }
            $user_id    = $user_info['user_id'];
            $order_mod  = m('order');
			$products_mod= m('products');
            $order_info = $order_mod->get(array(
                'conditions' => 'user_id='.$user_id.' and order_id='.$order_id,
            ));
			
            if(empty($order_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '不存在此订单',
                    )
                );
                return $json->encode($return);
            }
            if($order_info['status']!=40){
            	$return = array(
            			'statusCode' => 0,
            			'error' => array(
            					'errorCode' => 106,
            					'msg'       => '该商品订单未完成不能被评价',
            			)
            	);
            	return $json->encode($return);
            }
			//货品id获得goods_id
			if($pro_id){
				   $product = $products_mod->get(array(
                     'conditions' => "product_id ='{$pro_id}'",
					
            ));
				   $goods_ids=$product['goods_id'];
			}else{
				 $goods_ids=$pro_id;
			}
            $impressAttr = explode(",", $impress);
            $detail_impresss=array();
            foreach ($impressAttr as $v){
            	$detail_impress[]=$impression_arr[$v];
            }
			
            $impress=join(';', $impressAttr);
            //组装添加评论数据
            $data = array(
                'member_id'	      => $user_id,
                'comment_id'      => $goods_ids,//被评价商品id
                'content'         => htmlspecialchars($content),
                'addtime'         => gmtime(),
                'status'          => 0,
                'nickname'        => $user_info['nickname'],
                'cate'            => $cate,
                'star'      => $star,
                'come_from'       => 'app',
                'order_id'        => $order_id,
                'hide_name'       => $is_hide,
                'rec_id'          => $rec_id,//评价订单商品id
                'impression'	=>$impress,
            );

            $order_com_mod    = m('detail_comment');
            $_order_goods_mod = m('ordergoods');
            $_detailimpress_mod=m('detail_impression');
            $order_good=$_order_goods_mod->get(array(
            		'conditions'=>'rec_id='.$rec_id,
            ));
            if(empty($order_good)) {
            	$return = array(
            			'statusCode' => 0,
            			'error' => array(
            					'errorCode' => 105,
            					'msg'       => '不存在此订单商品',
            			)
            	);
            	return $json->encode($return);
            }
            
            if($order_good['comment']) {
            	$return = array(
            			'statusCode' => 0,
            			'error' => array(
            					'errorCode' => 107,
            					'msg'       => '该订单商品已被评价，不能重复评价',
            			)
            	);
            	return $json->encode($return);
            }
          //存订单商品评论
            $res = $order_com_mod->add($data);
            //评论印象标签
        
    	if($impress && $order_good['goods_id'])
    	{
    		$impression_temp=array();
    		foreach ($impressAttr as $key=>$value)
    		{
    			$impression_data = array(
    					'member_id'        =>$user_id,
    					'comment_id'      =>$goods_ids,
    					'addtime'         => time(),
    					'order_id'        =>$order_id,
    					'c_id'            =>$res,
    					'impression'      =>$value,
    			);
    	
    			$impression_temp[] = $impression_data;
    		}
    	
    		$_detailimpress_mod->add($impression_temp);
    	}
            

            //修改订单评论状态
            if($res) {
               
                $_order_goods_mod->edit($rec_id, array('comment' => 1));
   
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'success'    => '评论添加成功',
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '添加失败',
                    )
                );
            }

            return $json->encode($return);
        }

        /**
         * 获取消费者对商品的评论
         * @param $pro_id 商品id,$cate品类
         * @access protected
         * @author cyrus <2621270755@qq.com>
         * @return void
         */


        public function getComment($rec_id){

            global $json;
            $cus_mod = & m('custom');
            $impression_arr = include_once ROOT_PATH.'/includes/impression.arrayfile.php';
            $order_com_mod    = m('detail_comment');


            $comment_list = $order_com_mod->get(array(
                   'conditions'=>"1=1 AND rec_id='{$rec_id}'",
                   'fields'	=> "id,star,content,impression",
                   'index_key' => '',
              ));
     		if($comment_list){
     			$comment_list['impression']=explode(';', $comment_list['impression']);
     			$return = array(
     					'statusCode' => 1,
     					'result'     => array(
     							'comment'=>$comment_list,
     					),
     			);
     		}else{
     			$return = array(
     					'statusCode' => 0,
     					'error' => array(
     							'errorCode' => 101,
     							'msg'       => '获取评论失败',
     					)
     			);
     		}
             

            return $json->encode($return);


        }

        /**
         * 获得某裁缝的案例
         *
         * @param int $store_id 要
的裁缝id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getStoreCase($store_id)
        {
            global $json;
            $db = db();
            $return = array();
            if(empty($store_id)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '店铺不能为空',
                    )
                );
                return $json->encode($return);
            }

            //获得案例
            $sql        = "SELECT * FROM ".DB_PREFIX."store_service p WHERE p.store_id='$store_id' ";
            $storephoto = $db->getAll($sql);
            if($storephoto){
                foreach($storephoto as $k=>$v){
                    $sqls        = "SELECT * FROM ".DB_PREFIX."storephoto  WHERE album_id='".$v['id']."' and store_id='$store_id'";
                    $store_service = $db->getAll($sqls);
                    if($store_service) {
                        //获取图片路径
                        foreach($store_service as $key=>$val){
                            $storephoto[$k]['photoitem'][]['url'] = getUserPhotoUrl('works', $val['url'],200);
                        }
                    }
                }

            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'success'    => '获取成功',
                    'storephoto' => !empty($storephoto) ? $storephoto : '',
                ),
            );
            return $json->encode($return);

        }

        /**
         * “提交裁缝申请”页面对应的 服务、风格
         *
         * @param NULL
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getAttr()
        {
            global $json;
            $db = db();
            $return     = array();
            $store_attr = include '../includes/data/config/store.php';
            $return = array(
                'statusCode' => 1,//申请成功
                'result'   => array(
                    'serve'   =>  $store_attr['serve'],
                    'subject' => $store_attr['subject'],
                ),
            );

            return $json->encode($return);

        }

        /**
         * 提交裁缝申请
         *
         * @param object $data 申请数据
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function applyStore($data)
        {
            global $json;
            $sms_mod   = m('SmsRegTmp');
            $store_mod = m('store');
            $return    = array();
            $token     = $data->token;
            $user_info = getToken($token);
            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                        'token'      => $token,
                    ),
                );
                return $json->encode($return);

            }
            $store_id  = $user_info['user_id'];

            $store_allow = include '../includes/data/config/settings.inc.php';

            if(!$store_allow['store_allow']) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '店铺申请未开启',
                    ),
                );
                return $json->encode($return);
            }

            $code = isset($data->code) ? $data->code : '';
            $tel  = isset($data->tel) ? $data->tel : '';//手机

            //验证验证码是否有效
            if($code) {
                $res = $sms_mod->get(array(
                    // 'conditions'=>"code='$code' AND phone='$phoneNum' AND type='register'  ",
                    'conditions' => "code='$code' AND phone='$tel'  ",
                    'order'      => "id DESC",
                    'fields'     => '*',
                ));
                if($res['phone']) {
                    if(gmtime() > $res['fail_time']) {
                        $return = array(
                            'statusCode' => 0,
                            'error' => array(
                                'errorCode' => 101,
                                'msg'       => '验证码已过期',
                            )
                        );
                        return $json->encode($return);
                    }

                } else {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 101,
                            'msg'       => '验证码不正确',
                        )
                    );
                    return $json->encode($return);
                }

            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '验证码不能为空',
                    )
                );
                return $json->encode($return);
            }

            $token       = isset($data->token) ? $data->token : '';
            $store_name  = isset($data->store_name) ? $data->store_name : '';// 店铺名称
            $owner_name  = isset($data->owner_name) ? $data->owner_name : ''; // 联系人
            $owner_sex   = isset($data->owner_sex) ? $data->owner_sex : '';//称为
            $city_id     = isset($data->city_id) ? $data->city_id : '';//服务城市id
            $region_id   = isset($data->region_id) ? $data->region_id : '';//服务地区id
            $region_name = isset($data->region_name) ? $data->region_name : '';//城市名称
            $address     = isset($data->address) ? $data->address : '';//详细地址
            $description = isset($data->description) ? $data->description : '';//简介
            $real_name   = isset($data->real_name) ? $data->real_name : '' ;//真实姓名
            $card_num    = isset($data->card_num) ? $data->card_num : '';//身份证号
            $card_face_img = isset($data->card_face_img) ? $data->card_face_img : '' ;
            $card_back_img = isset($data->card_back_img) ? $data->card_back_img : '' ;
            if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$real_name))
            {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 103,
                        'msg'       => '姓名填写不合法',
                    ),
                );
                echo $json->encode($return);
            }
            if($store_id) {
                $store = $store_mod->get('store_id='.$store_id);

                if($store) {
                    if($store['state'] == 0){//申请中
                        $return = array(
                            'statusCode' => 1,//
                            'result'     => array(
                                'success' => '申请已提交，审核中!',
                            ),
                        );
                        return $json->encode($return);

                    } elseif($store['state'] == 1) {//开启
                        $return = array(
                            'statusCode' => 1,
                            'result'     => array(
                                'success' => '裁缝已开通,不可重复申请2222',
                            ),
                        );

                        return $json->encode($return);

                    } elseif($store['state'] == 2 ) {//关闭
                        $return = array(
                            'statusCode' => 1,
                            'result'     => array(
                                'success' => '裁缝已关闭',
                            ),
                        );

                        return $json->encode($return);
                    }
                }
            }
            //店铺联系人重复
            $member_mod  = m('member');
            $member_info = $member_mod->get(array("conditions"=>"user_name='$tel' AND serve_type=1"));
            if($member_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '电话已经被占用不能重复提交',
                    ),
                );

                return $json->encode($return);
            }

            //添加属性
            $store_attr_mod = m('storeattr');
            //先清空之前用户信息
            $store_attr_mod->drop('store_id='.$store_id);

            // 服务风格
            $item_serve    = $data->serve;//风格
            $item_subject  = $data->subject;//服务
            //
            //获得店铺服务、风格
            $store_attr= include '../includes/data/config/store.php';
            $serve   = $store_attr['serve'];
            $subject = $store_attr['subject'];

            if($item_serve) {
                $serveAttr = explode(",", $item_serve);
                foreach($serveAttr  as $key=>$val){
                    foreach ($serve['item'] as $kk=>$vv) {
                        if($val == $vv['id']) {
                            $store_attr_mod->add(array('type_id'=>1, 'content_id'=>$vv['id'], 'store_id'=>$store_id, 'attr_name'=>$vv['name']));
                        }
                    }
                }
            }

            if($item_subject) {
                $subjectAttr = explode(",", $item_subject);
                foreach($subjectAttr  as $key=>$val){
                    foreach ($subject['item'] as $kk=>$vv) {
                        if($val == $vv['id']) {
                            $store_attr_mod->add(array('type_id'=>2, 'content_id'=>$vv['id'], 'store_id'=>$store_id, 'attr_name'=>$vv['name']));
                        }
                    }
                }
            }

            //裁缝默认等级
            $member_lv_mod = m('memberlv');
            $m_lv          = $member_lv_mod->get_default_level(array('lv_type'=>'supplier'));

            $arr = array(
                'store_id'     => $store_id,
                'store_name'   => empty($store_name) ? '小裁缝'.$store_id : $store_name, //必填
                'owner_name'   => $owner_name,//必填
                'owner_sex'    => $owner_sex,//必填
                'city_id'      => $city_id,//必填
                'region_id'    => $region_id,//必填
                'region_name'  => $region_name,
                'address'      => $address,
                'tel'          => $tel,
                'state'        => 0,
                'add_time'     => gmtime(),
                'description'  => $description,
                'level'        => $m_lv['member_lv_id'],
                'real_name'    => $real_name,
                'card_num'     => $card_num,
                'card_face_img'=> $card_face_img,
                'card_back_img'=> $card_back_img,
            );

            //添加申请log 表
            $data_log = array('user_id'=>$store_id, 'apply_type' => 1, 'status' => 0);
            // 判断是否已经申请过
            $caifeng = $store_mod->get("store_id='$store_id'");

            if($caifeng['state'] != '' && $caifeng['state'] == 0) {
                $store_mod->edit("store_id = '$store_id'", $arr);

            } else {
                //添加申请
                $store_add = $store_mod->add($arr);
                // 添加log 表
                $alog_mod  = m('applylog');
                $apply_log = $alog_mod->add($data_log);

                $return = array(
                    'statusCode' => 1,//申请成功
                    'result'   => array(
                        'success' => '申请成功',
                    ),
                );
                return $json->encode($return);
            }

        }

        /**
         * 创业者提现操作所需参数项返回
         *
         * @param object $token 用户token
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function cashParamData($token)
        {
            global $json;
            $user_info = getToken($token);
            if(!$user_info)
            {
                return $this->tresult();
            }

            $cash_mod    = m('cash');
            $member_bank = m('member_bank');
            //获取认证用户的信息
            $auth_mod   = m('auth');
            $authinfo = $auth_mod->get("user_id=".$user_info['user_id']);

            if (empty($authinfo))
            {
                return $this->eresult('尚未实名认证，暂不能提现。');
            }
            else
            {
                if ($authinfo['status']==0)
                {
                    return $this->eresult('实名认证审核中，暂不能提现。');
                }
                elseif($authinfo['status'] == 2)
                {
                    return $this->eresult('实名认证失败，暂不能提现。');
                }
            }

            $cash_exist = $cash_mod->findAll(array(
                'conditions' => "user_id = ".$user_info['user_id'].' AND status = 0',
                'fields'     => 'id',
                'index_key'	 => '',
            ));
            if($cash_exist)
            {//已有提现申请; 正在提现
                return $this->eresult('您已有提现申请正在审核！不能重复申请');
            }
            $card_name = $authinfo['realname'];

            //返回当前用户已存银行卡信息
            $owner_bank = $member_bank->findAll(array(
                'conditions' => "user_id = ".$user_info['user_id'].' AND status=1  group by bank_card',
                'index_key'	 => '',
            ));

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'card_name'   => !empty($card_name) ? $card_name : '',//卡账户人
                    'money'       => _format_price_int($user_info['money']),//余额
                    'owner_bank'  => !empty($owner_bank) ? $owner_bank : '',//我已存银行卡
                ),
            );
            return $json->encode($return);
        }

        /**
         * 用户提交提现操作
         *
         * @param string $token 用户token, int  $bank_id  银行对应id,int $cash_money 提现金额,string $paymentPwd 支付密码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function memberCash($token, $bank_id, $cash_money, $paymentPwd)
        {
            global $json;
            $data = array();
            $user_info = getToken($token);
            if(!$user_info)
            {
                return $this->tresult();
            }

            //判断次用户是否已认证，认证后方可提现
            if(empty($user_info['df_bankcard']))
            {
                return $this->eresult('您还未绑定银行卡，暂时不能提现！');
            }

            //判断次用户是否已设置支付密码
            if(empty($user_info['pay_password']))
            {
                return $this->eresult('尚未设置支付密码，暂不能提现.');
            }

            //判断支付密码是否正确
            if(md5($paymentPwd) != $user_info['pay_password'] )
            {
                return $this->eresult('支付密码错误!');
            }
            //根据选择银行卡id，获得银行信息
            $member_bank = m('member_bank');
            $selectbank  = $member_bank->get($bank_id);
            if (!$selectbank)
            {
                return $this->eresult('请选择银行卡');
            }
            if ($selectbank['user_id'] != $user_info['user_id'])
            {
                return $this->eresult('银行卡参数错误');
            }

            $data['bank_address'] = $selectbank['bank_address'];
            $data['bank_id']      = $selectbank['bank_id'];
            $data['bank_card']    = $selectbank['bank_card'];

            //验证提现金额
            if (!$cash_money || $cash_money < 0)
            {
                return $this->eresult('提现金额必须为正整数');
            }
            if ($cash_money > $user_info['money'])
            {
                return $this->eresult('你的可用资金不足！请重新提交');
            }
            $data['cash_money']  = $cash_money;
            $data['user_info']   = $user_info;
            $data['create_time'] = gmtime();
            $cash_mod    = m('cash');
            $transaction = $cash_mod->beginTransaction();
            if (!$cash_mod->submit($data))
            {
                $cash_mod->rollback();
                return $this->eresult('提现失败');
            }
            $cash_mod->commit($transaction);

            $msg = new Notice();
            $msg->send(array(
                "content" => "创业者-提现申请(ID-".$cash_id."),<a href=\"".LOCALHOST1."admin/index.php?app=cash&act=edit&id=".$cash_id."\">查看详情</a>",
                'node'    => 'real_auth',
            ));
            return $this->sresult('成功提交提现申请,款项预计3~5个工作日到账');
        }

        /**
         * 用户删除保存的银行卡
         *
         * @param string $token 用户token, int  $bid 对应一行卡id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function delBank($token, $bid)
        {
            global $json;
            $user_info = getToken($token);
            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                        'token'      => $token,
                    ),
                );
                return $json->encode($return);
            }

            $member_bank = m('member_bank');
            $drop_count  = $member_bank->drop("user_id = " . $user_info['user_id'] . " AND id = {$bid}");
            if (!$drop_count) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '银行卡删除失败！',
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '银行卡删除成功！',
                    ),
                );
            }
            return $json->encode($return);
        }

        /**
         * 用户提现列表
         *
         * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function cashList($token, $pageSize, $pageIndex)
        {
            global $json;
            $cash_mod = m('cash');

            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id   = $user_info['user_id'];
            //===== 获得总的提现金额 =====
            $cash_money_total = $cash_mod->get(array(
                'conditions'	=> "status < 20 AND user_id = {$user_id}",
                'fields'		=> "sum(cash_money) as total_money",
            ));

            $page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;

            $cash_list = $cash_mod->find(array(
                'conditions' => "user_id = {$user_id}",
                'fields'	 => "*",
                'order'		 => "id desc",
                'count'		 => "true",
                'limit'      => $page_list,
                'index_key'	 => '',
            ));
            $status = array(
                '0' => '审核中',
                '1' => '审核成功',
                '2' => '审核失败',
            );
            //处理状态结果
            foreach($cash_list as $key=>$val) {
                $cash_list[$key]['staus_name'] = $status[$val['status']];
            }

            $count = $cash_mod->getCount();

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'cash_money_total' => !empty($cash_money_total['total_money']) ? $cash_money_total['total_money'] : 0,
                    'cash_list'        => !empty($cash_list) ? $cash_list : '',
                    'count'            => !empty($count) ? $count : 0,
                ),
            );

            return $json->encode($return);
        }

        /**
         * 交易列表
         *
         * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码;int $filter 过滤条件 0：全部 1：支出 2：收入 3:提现记录
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @modify liuchao <280181131@qq.com>
         * @return void
         */
        function payList($token, $filter, $pageSize, $pageIndex)
        {
            global $json;
            $member_money = m('member_money');
            $cash_mod = m('cash');
            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id   = $user_info['user_id'];
            //===== 累计收入 =====
            /* 目前没用了
    	$income_money_total = $member_money->get(array(
    		'conditions'	=> "change_money > 0 AND user_id = {$user_id}",
    		'fields'		=> "sum(change_money) as change_money_income",
    	));
		//===== 累计支出 =====
        /* 目前没用了
		$pay_money_total = $member_money->get(array(
    		'conditions'	=> "change_money < 0  AND user_id = {$user_id}",
    		'fields'		=> "sum(change_money) as pay_money_total",
    	));*/
            $m_conditions = "";
            $c_conditions = "";
            $page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;

            if($filter == 1) {//过滤条件 0：全部 1：支出(要带提现记录) 2：收入 3: 提现记录
                $m_conditions = " AND  change_money < 0";
                $c_conditions = " AND status =1 ";
            }elseif($filter == 2){
                $m_conditions = " AND  change_money > 0";
                $c_conditions = " AND 1!=1"; //不要提现记录
            }elseif($filter == 3){
                $m_conditions = " AND 1!=1"; //不要交易记录
            }

            $member_money_list = $member_money->find(array(
                'conditions' => "user_id = {$user_id}".$m_conditions,
                'fields'	 => "id, user_id,change_money as change_money,addtime,type",
                'order'		 => "id desc",
                'count'		 => "true",
                'limit'      => $page_list,
                'index_key'	 => '',
            ));
            $count = $member_money->getCount();

            $cash_list = $cash_mod->find(array(
                'conditions' => "user_id = {$user_id}".$c_conditions,
                'fields'     => "id,user_id,cash_money as change_money,create_time as addtime,status",
                'order'		 => "id desc",
                'count'		 => "true",
                'limit'      => $page_list,
                'index_key'	 => '',
            ));
            foreach($cash_list as $key=>$val){
                $cash_list[$key]['type'] = 4;
            }
            //把支出记录和提现记录合并
            $count = $count + $cash_mod->getCount();

            $member_money_list = array_merge($member_money_list,$cash_list);
            $time = array();
            foreach ($member_money_list as $val) {
                $time[] = $val['addtime'];
            }
            array_multisort($time, SORT_DESC, $member_money_list);
            $status = array(
                '0' => '审核中',
                '1' => '审核成功',
                '2' => '审核失败',
            );
            //处理状态结果
            foreach($member_money_list as $key=>$val) {
                $member_money_list[$key]['staus_name'] = $status[$val['status']];
            }


            foreach($member_money_list as $key=>$val) {//价格处理
                $member_money_list[$key]['change_money'] = number_format(floor($val['change_money']), 2);
            }

            $type_name = array(
                '1' => '充值',
                '2' => '订单',
                '3' => '推荐',
                '4' => '提现',
            );
            //处理状态结果
            foreach($member_money_list as $key=>$val) {
                $member_money_list[$key]['type_name'] = $type_name[$val['type']];
            }


            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    //'income_money_total' => !empty($income_money_total['change_money_income']) ? number_format(floor($income_money_total['change_money_income']), 2) : 0,
                    //'pay_money_total'    => !empty($pay_money_total['pay_money_total']) ? number_format(floor($pay_money_total['pay_money_total']), 2) : 0,
                    'member_money_list'  => !empty($member_money_list) ? $member_money_list : '',
                    'count'              => !empty($count) ? $count : 0,
                ),
            );

            return $json->encode($return);

        }

        /**
         * 我的财富（收益、麦富迪币）
         *
         * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码;int $type 过滤条件 0：收益 1：积分 2：麦富迪币 3：购物券 4：余额
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         * @type 0 收益  type 1 积分  type 2  我的麦富迪币
         */
        function myWealth($token, $type, $pageSize, $pageIndex)
        {
            global $json;
            $limit     = $pageSize*($pageIndex-1).','.$pageSize;

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

            //获得用户的：收益、麦富迪币（查询所有minus类型）
            $order_cash_log_mod = m('ordercashlog');

            $contidions = "order_cash_log.user_id = $user_id AND  order_cash_log.type = '{$type}'";

            if ($type != 100) {
                if ($type == 0) {//近期收益（消费者下单返回的收益--三个月以内的列表）
                    ///$near_time = strtotime("-3 month");//三个月以内
                    $contidions .= " AND order_cash_log.minus = $type ";
                }

            }

            if ($user_info['member_lv_id'] >= 2) //=====  创业者去收益列表  =====
            {
                $wealth_list = $order_cash_log_mod->find(array(
                    'join'       => "belongs_to_user",
                    'conditions' => $contidions,
                    'fields'     => "id,CONCAT(order_cash_log.mark, order_cash_log.cash_money) as show_cash_money,order_cash_log.mark,order_cash_log.id, order_cash_log.minus,type,order_cash_log.name, order_cash_log.order_sn, order_cash_log.add_time, order_cash_log.cash_money, order_cash_log.turnout, member.user_name, member.user_id",
                    'limit'      => $limit,
                    'order'      => 'id desc',
                    'index_key'  => '',
                ));
            }
            else//=====  消费者取订单列表  =====status  NOT IN(0,11,80) AND
            {

                if ($type == 2 || $type == 1)  //=====  消费者的麦富迪币或积分列表  =====
                {
                    $contidions = "order_cash_log.user_id = $user_id AND  order_cash_log.type = $type";
                    $wealth_list = $order_cash_log_mod->find(array(
                        'join'       => "belongs_to_user",
                        'conditions' => $contidions,
                        'fields'     => "CONCAT(order_cash_log.mark, order_cash_log.cash_money) as show_cash_money,order_cash_log.mark,order_cash_log.id, order_cash_log.minus, order_cash_log.name, order_cash_log.order_sn, order_cash_log.add_time, order_cash_log.cash_money, order_cash_log.turnout, member.user_name, member.user_id",
                        'limit'      => $limit,
                        'order'      => 'id desc',
                        'index_key'  => '',
                    ));
                }
                else
                {
                    $order_mod = m('order');
                    $wealth_list = $order_mod->find(array(
                        //'join'       => "belongs_to_user",
                        'conditions' => "user_id={$user_id} AND status  NOT IN(0,11,80)",
                        'fields'     => "order_amount as show_cash_money, order_sn, add_time, order_amount as cash_money,user_name, user_id",
                        'limit'      => $limit,
                        'order'      => 'order_id desc',
                        'index_key'  => '',
                    ));
                    if ($wealth_list)
                    {
                        foreach ($wealth_list as $key => &$value)
                        {
                            $value['name'] = '下单';
                            $value['turnout'] = 0;
                            $value['type'] = 0;
                        }
                    }
                }

            }



            //获得头像
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            $cash_money_count = 0;
            foreach($wealth_list as  $key=>$val) {
                /* if($val['turnout']==0 && $type == 0 && $val['minus'] == 0) {
				$cash_money_count = $cash_money_count + $val['cash_money'];
			} */
                //获得用户头像
                $avatar = $objAvatar->avatar_show($val['user_id'], 'big');
                $wealth_list[$key]['avatar'] = $avatar;
                $cash_money = $val['cash_money'];
                if ($val['type'] == 2 || $val['type'] == 1) //=====  麦富迪币和积分  =====
                {
                    $cash_money = floor($val['cash_money']);
                }
                else
                {
                    $cash_money = $cash_money;
                }

                $wealth_list[$key]['cash_money'] = $cash_money;

            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'list'              => $wealth_list,
                    'cash_money_count'  => $user_info['profit'],//=====  创业者 收益列表的 收益总计  =====
                ),
            );

            return $json->encode($return);
        }

        /**
         * 我的红包
         *
         * @param string $token 用户token, int  $pageSize 页面大小, int $pageIndex 页码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function myBonus($token, $pageSize, $pageIndex)
        {

            global $json;
            $limit     = $pageSize*($pageIndex-1).','.$pageSize;

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

            //获得用户的红包
            $bonus_log_mod = m('bonuslog');
            $bonus_list = $bonus_log_mod->find(array(
                'conditions' => "user_id='{$user_id}'",
                'limit'      => $limit,
                'order'      => "is_open ASC, expire DESC",
                'index_key'  => '',
            ));
            $now_time = time();
            foreach($bonus_list as $key => $val) {
                $bonus_list[$key]['is_expire '] = 0;//是否过期 0:未过期 1：已过期
                if($val['expire'] < $now_time && $val['is_open'] == 0) {
                    $bonus_list[$key]['is_expire '] = 1;
                    continue;
                }
                if($val['is_open']) {//已拆开，name显示类型
                    if($val['type'] == 1) {
                        $bonus_list[$key]['name'] = $val['name']."(麦富迪币)";
                    } else {
                        $bonus_list[$key]['name'] = $val['name']."(现金)";
                    }
                }
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'list'  => $bonus_list,
                ),
            );

            return $json->encode($return);
        }

        /**
         * 拆红包
         *
         * @param string $token 用户token, int  $bonus_id 红包对应id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function openBonus($token, $bonus_id)
        {
            global $json;
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

            //获得用户的红包
            $bonus_log_mod = m('bonuslog');
            $member_mod    = m('member');
            $bonus_info = $bonus_log_mod->get(array(
                'conditions' => "user_id='{$user_id}' and id='{$bonus_id}'",
            ));
            if(!$bonus_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '未发现此红包！'
                    ),
                );
                return $json->encode($return);
            }
            if($bonus_info['is_open']) {

                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '当前红包已拆，不能重复拆红包！!'
                    ),
                );
                return $json->encode($return);
            }

            //执行拆红包操作，加入事物
            $transaction   = $bonus_log_mod->beginTransaction();
            $return_result = $bonus_log_mod->openBonusFun($bonus_id);//执行拆红包操作
            if (!$return_result['code']) {
                $bonus_log_mod->rollback();
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => $return_result['msg']
                    ),
                );
                return $json->encode($return);

            }
            $bonus_log_mod->commit($transaction);

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  => '成功获得红包！',
                ),
            );

            return $json->encode($return);
        }

        /**
         * 红包记录
         *
         * @param string $token 用户token int  $pageSize 页面大小, int $pageIndex 页码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function bonusRecord($token, $pageSize, $pageIndex)
        {
            global $json;
            $order_cash_mod = m('ordercashlog');
            $user_info = getUserInfo($token);
            if (!$user_info) {
                return $this->tresult();
            }
            $user_id = $user_info['user_id'];
            $bonus_list = $order_cash_mod->findAll(array(
                'conditions' => "(type = 2 or type = 4)  and minus = 2",
                'index_key'  => '',
            ));

            foreach($bonus_list as $key=>$val ) {
                if($val['type'] == 2) {
                    $bonus_list[$key]['name'] = $val['name']."(麦富迪币)";
                } else {
                    $bonus_list[$key]['name'] = $val['name']."(现金)";
                }
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'list'  => $bonus_list,
                ),
            );

            return $json->encode($return);
        }

        /**
         * 收益转出（余额、麦富迪币）
         *
         * @param string $token 用户token;int  $type 转入类型：1余额 2麦富迪币；int  $logid 转入收益id；；
         *     int  $pageSize 页面大小, int $pageIndex 页码
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * edit 11.11 by liang.li
         * @return void
         */
        function coinTurnout($token, $type, $logid )
        {
            global $json;
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

            $member_mod = m('member');
            if (!in_array($type, array(1)))
            {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => 'type参数不正确'
                    ),
                );
                return $json->encode($return);
            }

            //执行收益转出操作，加入事物
            $transaction   = $member_mod->beginTransaction();
            $return_result = $member_mod->coinTurnoutFun($user_id, $logid, $type);//收益转出操作
            // return $return_result;
            if (!$return_result['code']) {
                $member_mod->rollback();
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => $return_result['msg']
                    ),
                );
                return $json->encode($return);

            }
            $member_mod->commit($transaction);

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  => '已转入'.$return_result['type'],
                ),
            );

            return $json->encode($return);
        }

        /**
         * 我的相关财富总数
         *
         * @param string $token 用户token;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function myWealthCount($token)
        {
            global $json;
            $order_cash_log_mod = m('ordercashlog');
            $order_mod          = m('order');
            $bonus_log_mod      = m('bonuslog');
            $debit_mod          = m('debit');
            $special_code_mod   = m('special_code');
            $lv_mod             = m('memberlv');
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

            //上月收益总额
            $last_near_time = strtotime("-1 month");//一个月以内
            $last_wealth_count = $order_cash_log_mod->get(array(
                'conditions' => "type=0 and minus = 0 AND add_time >= '{$last_near_time}' and user_id='{$user_id}'",
                'fields'     => "sum(cash_money) as count",
            ));
            //近期收益总额（三个月以内）
            $threee_near_time = strtotime("-3 month");//三个月以内
            $threee_wealth_count = $order_cash_log_mod->get(array(
                'conditions' => "type=0 and minus = 0 AND add_time >= '{$threee_near_time}' and user_id='{$user_id}'",
                'fields'     => "sum(cash_money) as count, cash_money",
            ));

            //当前收益（没有转出的部分）
            $now_wealth_count = $order_cash_log_mod->get(array(
                'conditions' => "type=0 and minus = 0 AND turnout = 0 and user_id='{$user_id}'",
                'fields'     => "sum(cash_money) as count, cash_money",
            ));

            //累计总收益（含转出的部分）
            $total_wealth_count = $order_cash_log_mod->get(array(
                'conditions' => "type = 0 and minus = 0 and user_id='{$user_id}'",
                'fields'     => "sum(cash_money) as count, cash_money",
            ));


            //近一个月消费
            $last_wealth_consume = $order_mod->get(array(
                'conditions' => "status = 40  AND finished_time >= '{$last_near_time}' and user_id='{$user_id}'",
                'fields'     => "sum(order_amount) as count",
            ));

            //未读红包数
            $bonus_count = $bonus_log_mod->get(array(
                'conditions' => "user_id='{$user_id}' and is_open=0 ",
                'fields'     => "count(id) as count, id",
            ));
            //未使用酷券数
            $debit_count = $debit_mod->get(array(
                'conditions' => "(user_id = '{$user_id}' or from_uid = '{$user_id}') and is_used = 0  and is_invalid = 0",
                'fields'     => "count(id) as count, id",
            ));
            //已使用酷卡数
            $special_count = $special_code_mod->get(array(
                'conditions' => "cate=20 and is_used= 1 and to_id = '{$user_id}'",
                'fields'     => "count(id) as count, id",
            ));

            $cha_point = -1;
            $lvs_name  = '';
            $show_notice  = 0;
            if($user_info['member_lv_id'] != 1) {//1：普通会员； 2、3、4、5  创业者 6:创业顾问

                if($user_info['member_lv_id'] == 5 || $user_info['member_lv_id'] == 6) {//满级
                    $cha_point   = 0;
                    $show_notice = 1;
                } else {
                    if($user_info['member_lv_id'] == 2) {//合并2、3级别会员
                        $user_info['member_lv_id'] = 3;
                    }
                    //根据当前积分计算下一级信息
                    $lvs_infp = $lv_mod->get(array(
                        'fields'     => "name, experience",
                        'conditions' => "member_lv_id > '{$user_info['member_lv_id']}'",
                    ));
                    $user_point = $user_info['point'];
                    //查询一下是否使用了特权码升级的，如果使用了的，将他现有的积分+1000
                    $_special_code_mod = & m('special_code');
                    $special_info = $_special_code_mod->get('cate=1 and is_used=1 and to_id='.$user_info['user_id']);

                    if($special_info) {
                        $user_point = $user_info['point'] + 1000;
                    }

                    $cha_point = $lvs_infp['experience'] - $user_point;
                    $lvs_name  = !empty($lvs_infp['name']) ? $lvs_infp['name'] : '' ;
                    $show_notice  = 1;
                }
            }

            $data = array(
                'money'               => !empty($user_info['money']) ? _format_price_int($user_info['money']) : '0.00',//账户余额
                'coin'                => (int)$user_info['coin'],//麦富迪币
                'point'               => (int)$user_info['point'],//当前积分
                'cha_point'           => (int)$cha_point,//下一级差积分
                'lvs_name'            => $lvs_name,//下一级头衔
                'show_notice'         => $show_notice,//是否显示头衔升级提示
                'last_wealth_count'   => !empty($last_wealth_count['count']) ? _format_price_int($last_wealth_count['count']) :  '0.00',//上月收益总额
                'last_wealth_consume' => !empty($last_wealth_consume['count']) ? _format_price_int($last_wealth_consume['count']) :  '0.00',//近一个月消费
                'threee_wealth_count' => !empty($threee_wealth_count['count']) ? _format_price_int($threee_wealth_count['count']) :  '0.00',//近期收益
                'now_wealth_count'    => !empty($now_wealth_count['count']) ? _format_price_int($now_wealth_count['count']) :  '0.00',//当前收益（没有转出的部分）
                'total_wealth_count'  => !empty($total_wealth_count['count']) ? _format_price_int($total_wealth_count['count']) :  '0.00',//累计总收益（含转出的部分）
                'bonus_count'         => $bonus_count['count'],//未读红包数
                'debit_count'         => $debit_count['count'],//未使用酷券数
                'special_count'       => $special_count['count'],//已使用酷卡数
            );
            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  => $data,
                )
            );

            return $json->encode($return);
        }

        /**
         * 抵用券转赠
         *
         * @param string $token 用户token; int $gift 要转赠的抵用券id; int $to_userid 要转赠给对应用户的id;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function giftDonation($token, $gift, $to_userid)
        {
            global $json;
            $debit_mod  = m('debit');
            $member_mod = m('member');

            $user_info = getUserInfo($token);
            $touser_info = $member_mod->get($to_userid);

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

            if (!$touser_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未发现您要转赠的顾客！'
                    ),
                );
                return $json->encode($return);
            }

            //获得当前抵用券信息
            $debit_info = $debit_mod->get(array(
                'conditions' => "from_uid = '{$user_id}' and is_used = 0 and is_invalid = 0 and id = '{$gift}'",
            ));

            if (!$debit_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未发现此转赠券！'
                    ),
                );
                return $json->encode($return);
            }

            //执行转赠操作
            $debit_edit = $debit_mod->edit($debit_info['id'], array('user_id'=>$touser_info['user_id']));
            if (!$debit_edit) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '抵用券转赠失败！'
                    ),
                );
                return $json->encode($return);
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  => '成功转赠给您顾客！',
                )
            );

            return $json->encode($return);
        }

        /**
         * 返回app端需要显示过滤的订单状态
         *
         * @param null
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function ordersStatusList()
        {
            global $json;
            $ordersStatusList = array();

            $ordersStatusList = array(
                array(
                    'status_id'   => '-1',
                    'status_name' =>'全部',
                ),
                array(
                    'status_id'   => ORDER_PENDING,
                    'status_name' =>'待付款',
                ),
                array(
                    'status_id'   => ORDER_WAITFIGURE,  // STORE_ACCEPTED
                    'status_name' =>'待量体',
                ),
                array(
                    'status_id'   => ORDER_PRODUCTION,
                    'status_name' =>'生产中',
                ),
                array(
                    'status_id'   => ORDER_SHIPPED,
                    'status_name' =>'已发货',
                ),
                array(
                    'status_id'   => ORDER_FINISHED,
                    'status_name' =>'已完成',
                ),
                array(
                    'status_id'   => ORDER_CANCELED,
                    'status_name' =>'已取消',
                ),

            );

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'ordersStatusList' => $ordersStatusList,
                ),
            );

            return $json->encode($return);

        }

        /**
         * 消费者 根据条件获取订单列表
         *
         * @param int $pageSize  每页显示的个数；int $pageIndex 第几页； string $token 用户标识；string $order_sn 订单号；int $status 订单状态
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function userOrdersList($token, $status, $order_sn, $pageIndex, $pageSize)
        {
            global $json;
            $db = db();
            $return = array();
            $conditions = "";

            $user_info = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id   = $user_info['user_id'];

            $order_mod = m('order');
            if($status > -1) {
                $conditions .= " AND status ='$status' ";
            }
            if($order_sn) {
                $conditions .= " AND order_sn ='$order_sn' ";
            }

            if(!empty($pageSize) && !empty($pageIndex)) {
                $page_list = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
            }

            $orders = $order_mod->findAll(array(
                'conditions' => "user_id=".$user_id . $conditions,
                'fields'     => 'order_id, order_sn, order_amount, add_time, kh_name, status, cloth',
                'limit'      => $page_list,
                'order'      => 'add_time DESC',
                'count'      => true,
                'index_key'	 => '',
            ));

            //获得订单总数
            $count  = $order_mod->get(array(
                'fields'	 => "count('order_id') as count",
                'conditions' => "user_id=" . $user_id . $conditions,
            ));

            if(!$orders) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'orders'  => '',
                        'count'   => 0,
                    ),
                );
                return $json->encode($return);
            }
            $gc = m('gcategory');
            foreach ($orders as $key=>$val) {
                //获得品类名称
                $gc_info = $gc->get("cate_id='".$val['cloth']."'");
                $orders[$key]['cloth'] = $gc_info['cate_name'];

                if($val['status'] == ORDER_PENDING) {
                    $orders[$key]['statusName']='待付款';
                }
                if ($val['status']==ORDER_ACCEPTED) {  //STORE_ACCEPTED
                    $orders[$key]['statusName']='已付款';
                }
                if($val['status'] == ORDER_CHECKING) {
                    $orders[$key]['statusName']='审核中';
                }
                if($val['status'] == ORDER_CHECKFAIL) {
                    $orders[$key]['statusName']='审核失败';
                }
                if($val['status'] == ORDER_PRODUCTION) {
                    $orders[$key]['statusName']='生成中';
                }
                if($val['status']==ORDER_SHIPPED) {
                    $orders[$key]['statusName']='平台已发货';
                }
                if($val['status']==STORE_RECEIVED) {
                    $orders[$key]['statusName']='裁缝已收货';
                }
                if($val['status']==STORE_SHIPPED) {
                    $orders[$key]['statusName']='裁缝已发货';
                }
                if($val['status']==ORDER_FINISHED) {
                    $orders[$key]['statusName']='交易成功';
                }
                if($val['status']==ORDER_CANCELED) {
                    $orders[$key]['statusName']='已作废';
                }
            }
            //组装返回数据
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
         * 消费者 根据条件获取订单列表
         *
         * @param string  $token 用户标识；int $order_id 订单id
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function userOrderInfo($token, $order_id)
        {
            global $json;
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
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id    = $userinfo['user_id'];

            //获得订单详情
            $order_info = $order_mod->get(array(
                'conditions' => 'user_id = '.$user_id.' and order_id = '.$order_id ,
                'fields'     => 'user_id, order_id, order_sn, order_amount, add_time, kh_name, status, cloth, fabric, payment_id, kh_addr, kh_mobile, embs, craft ',
            ));

            if(!$order_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '无对应订单',
                    ),
                );
                return $json->encode($return);
            }

            //获取对应订单产品品类
            $gc = m('gcategory');
            $gc_info = $gc->get("cate_id='".$order_info['cloth']."'");
            $order_info['cloth'] = $gc_info['cate_name'];

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
                $craft = $this->_get_order_craft($order_info['cloth'], $order_info['craft']);
            }
            unset($order_info['embs']);
            unset($order_info['craft']);
            //获得订单对应的量体数据
            $figure_info = $order_figure->get(array(
                'conditions' => 'order_id = '.$order_id,
            ));

            //获得订单消费者信息
            $member_info = $member->get(array(
                'conditions' => 'user_id='.$order_info['user_id'],
                'fields'     => 'user_id, nickname, gender, phone_mob, phone_tel, height, weight, region_name',
            ));

            // 交易状态
            if($order_info['status'] == ORDER_PENDING) {
                $order_info['statusName']='待付款';
            }
            if ($order_info['status']==ORDER_ACCEPTED) {  //STORE_ACCEPTED
                $order_info['statusName']='已付款';
            }
            if($order_info['status'] == ORDER_CHECKING) {
                $order_info['statusName']='审核中';
            }
            if($order_info['status'] == ORDER_CHECKFAIL) {
                $order_info['statusName']='审核失败';
            }
            if($order_info['status'] == ORDER_PRODUCTION) {
                $order_info['statusName']='生成中';
            }
            if($order_info['status']==ORDER_SHIPPED) {
                $order_info['statusName']='平台已发货';
            }
            if($order_info['status']==STORE_RECEIVED) {
                $order_info['statusName']='裁缝已收货';
            }
            if($order_info['status']==STORE_SHIPPED) {
                $order_info['statusName']='裁缝已发货';
            }
            if($order_info['status']==ORDER_FINISHED) {
                $order_info['statusName']='交易成功';
            }
            if($order_info['status']==ORDER_CANCELED) {
                $order_info['statusName']='已作废';
            }

            $return = array(
                'statusCode' => 1,
                'result'     => array(
                    'order_info'  => !empty($order_info) ? $order_info : '',
                    'figure_info' => !empty($figure_info) ? $figure_info : '',
                    'member_info' => !empty($member_info) ? $member_info : '',
                    'embs'        => !empty($embs) ? $embs : '',
                    'craft'       => !empty($craft) ? $craft : '',
                    //'goods_list'  => !empty($order_goods_list) ? $order_goods_list : '',
                ),
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
         * 手机发送验证码
         *
         * @param string $phoneNum 手机
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function sendCode($phoneNum, $type)
        {
            global $json;
            $return = array();

            $code = rand(1000, 9999);
            
           // $code = '111111';
            if($type == 'reg')
            {// 判断此用户是否已注册
                $m     = m('member');
                $member = $m->get("(user_name = $phoneNum or phone_mob=$phoneNum) and serve_type=1");

                if(!empty($member)) {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 103,
                            'msg'       => '该手机号已经被占用',
                        )
                    );
                    return $json->encode($return);
                }

            }elseif ($type == 'findps') {
                
                $m     = m('member');
                $member = $m->get("(user_name = $phoneNum or phone_mob=$phoneNum) and serve_type=1");

                if(empty($member)) {
                    $return = array(
                        'statusCode' => 0,
                        'error' => array(
                            'errorCode' => 104,
                            'msg'       => '该用户不存在',
                        )
                    );
                    return $json->encode($return);
                }

                
            }
            $timeout = 300;//验证码过期时间        20分钟

//            $rs   = SendSms($phoneNum, '验证码为' . $code, $type, $code, $timeout);
            $rs = smsAuthCode($phoneNum,$type,$type,'get','pc',$_POST['password']);


            if ($rs) {
                $return = array(
                    'statusCode' => 1,
                    'result'     => array(
                        'success' => '发送成功',
                        'phoneNum' => $phoneNum,
                        'code'     => $code,
                    ),
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '发送失败',
                    )
                );
            }

            return $json->encode($return);
        }

        /**
         * 发送模板短信
         * @param to 手机号码集合,用英文逗号分开
         * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
         * @param $tempId 模板Id
         */
        function sendTemplateSMS($to,$datas,$tempId)
        {
            include_once(ROOT_PATH."/includes/CCPRestSDK.php");
            // 初始化REST SDK
            //主帐号
            $accountSid= '8a48b55148fe4860014926d3ccaa1ae1';

            //主帐号Token
            $accountToken= '2fc3c39634894b0fa2b853d57b8ab05c';

            //应用Id
            $appId='8a48b55148fe4860014926e447d81af2';

            //请求地址，格式如下，不需要写https://
            $serverIP='sandboxapp.cloopen.com';

            //请求端口
            $serverPort='8883';

            //REST版本号
            $softVersion='2013-12-26';

            // global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
            $rest = new REST($serverIP,$serverPort,$softVersion);
            $rest->setAccount($accountSid,$accountToken);
            $rest->setAppId($appId);

            // 发送模板短信

            $result = $rest->sendTemplateSMS($to,$datas,$tempId);
            if($result == NULL )
            {
                return false;
            }
            if($result->statusCode !=0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        /**
         * 发送邮件
         * @param string $email 邮箱
         * @version 1.0.0 (2015-3-19)
         * @author Xiao5
         */
        function sendEmail($token,$email,$category,$type,$ps){

            global $json;
            //include_once(ROOT_PATH . '/soaapi/v1/includes/email.lib.php');
            $user_info = getToken($token);
            $email_code_mod =& m('email_code');
            $url = getEmailcode($email,$category);
            $url = getEmail($category,$url);
            //没有做返回值验证
            $rs = sendmail($email, $url);
            if ($rs['err'])
            {
                $return['status'] = 0;
                $return['error']['errorCode'] = 101;
                $return['error']['msg']       = $rs['msg'];
                return $json->encode($return);
            }
            $code = getCode($email);
            $email_code_mod->drop("type = '{$type}' AND email = '{$email}'");
            $data = array(
                'code'=>$code,
                'type'=>$type,
                'fail_time'=>gmtime() + EMAIL_FAIL_TIME,
                'add_time'=>gmtime(),
                'uid'=> $_SESSION['user_info']['user_id'],
                'category'=>$category,
                'email'=>$email,
                'ps'   => $ps,
            );
            if($email_code_mod->add($data)){
                $return['status'] = 1;
                $return['result']['success'] = '邮件发送成功';
            }else{
                $return['status'] = 0;
                $return['error']['errorCode'] = 102;
                $return['error']['msg']       = '邮件发送失败';
                return $json->encode($return);
            }
            return $json->encode($return);
        }
        /**
         * 设置支付密码
         * @return void
         * @version 1.0.0 (2014-12-12)
         * @author Xiao5
         */
        function setPayPwd($token, $mobile, $code, $pay_ps)
        {
            global $json;
            $user_info       = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }
            $user_id         = $user_info['user_id'];

            /*      $sms_reg_tmp_mod = m('sms_reg_tmp');
        $conditions      = array('conditions'=> "type = 'payPwd' and phone = '$mobile' and code='$code' ");
        $sms_log         = $sms_reg_tmp_mod->get($conditions);

        if (!$sms_log['id']) {
            $return['statusCode'] = 0;
            $return['error']['errorCode'] = 103;
            $return['error']['msg']       = '请重新获取验证码';
            return $json->encode($return);
        }
        if($sms_log['fail_time'] < gmtime()){
            $return['statusCode'] = 0;
            $return['error']['errorCode'] = 103;
            $return['error']['msg']       = '验证码已过期';
            return $json->encode($return);
        } */
            if (!$pay_ps) {
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '请输入支付密码';
                return $json->encode($return);
            }

            if (strlen($pay_ps) < 8 || strlen($pay_ps) > 12) {
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '密码长度不符合规范';
                return $json->encode($return);
            }

            //===== member表修改 =====
            $m_mod = m('member');
            if(md5($pay_ps) == $user_info['pay_password']){
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '新密码与老密码相同';
                return $json->encode($return);
            }
            $data['pay_password'] = md5($pay_ps);
            if($m_mod->edit($user_id, $data)){
                $return['statusCode'] = 1;
                $return['result']['success'] = '支付密码设置成功';
                $return['result']['pwd']      = $pay_ps;
            }else{
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 105;
                $return['error']['msg']       = '支付密码设置失败';
            }
            return $json->encode($return);
        }


        /**
         * 验证验证码是否可以用
         * @return void
         * @version 1.0.0 (2014-12-12)
         * @author Xiao5
         */
        function verifyCode($mobile,$code){
            global $json;
            $sms_mod = & m('sms_reg_tmp');
            //验证手机与验证码
            if ($code) {
                $res = $sms_mod->get(array(
                    'conditions' => "code='{$code}' AND phone='{$mobile}'  AND type = 'payPwd'" ,
                    'order' => "id  DESC",
                    'fields' => '*',
                ));

                if ($res['phone']) {
                    if (gmtime() > $res['fail_time']) {
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
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'   => '验证码正确,下一步',
                )
            );
            return $json->encode($return);
        }

        /**
         * 修改支付密码
         * @return void
         * @version 1.0.0 (2014-12-12)
         * @author Xiao5
         */
        function editPayPwd($token, $mobile , $pay_ps, $oldpay_ps)
        {
            global $json;
            $user_info       = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    ),
                );
                return $json->encode($return);
            }

            $user_id         = $user_info['user_id'];

            if (!$pay_ps) {
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '请输入支付密码';
                return $json->encode($return);
            }

            if (strlen($pay_ps) < 8 ) {
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 104;
                $return['error']['msg']       = '密码长度在8位以上';
                return $json->encode($return);
            }

            //===== member表修改 =====
            $m_mod = m('member');
            $data['pay_password'] = md5($pay_ps);
            if($m_mod->edit($user_id, $data)) {
                $return['statusCode'] = 1;
                $return['result']['success'] = '支付密码修改成功';
                $return['result']['pwd']      = $pay_ps;
            } else {
                $return['statusCode'] = 0;
                $return['error']['errorCode'] = 105;
                $return['error']['msg']       = '支付密码修改失败';
            }

            return $json->encode($return);
        }

        /**
         * 通过token 获取用户信息
         *
         * @param string $token  用户标识
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public  function getTokens($token)
        {
            global $json;
            $return = array();
            $m = m('member');
            $userinfo = $m->get("user_token = '$token'");
            //return $userinfo;

            if($userinfo) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'userinfo' => $userinfo,
                    )
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
            }
            //print_r($return );exit;
            return $json->encode($return);

        }

        /**
         * 获得样衣分类
         *
         * @param string $token  用户标识
         *
         * @access protected
         * @return void
         */
        public function getCate($parentId)
        {
            global $json;
            $gcategory_mod = &bm('gcategory');
            if ($parentId == -1) {
                /* $parent_list = $gcategory_mod->get_list(0);
	  		foreach ($parent_list as $k=>$v)
	  		{
	  			$parent_list[$k]['child_list'] = $gcategory_mod->get_list($v['cate_id']);
	  		}
	  		$g_list = $parent_list; */
                $g_list = $gcategory_mod->find(array(
                    'conditions' => " parent_id != 0 AND if_show=1",
                    'index_key'  => "",
                    'fields'     => "cate_id, cate_name, alias",
                    'order'      => "sort_order asc",
                ));

            } else {
                $g_list = $gcategory_mod->get_list($parentId);
            }

            return $json->encode($g_list);
        }


        /**
         * 获得分类对应的属性
         * @param int $cateId
         */
        public function getCateTemplate($data)
        {
            global $json;
            $token = $data->token;
            $return = array();
            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                $return['statusCode'] = 1;
                $return['msg'] = '用户不存在';
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];
            $cate_id = $user_info['cate_id'];
            if (!$cate_id)
            {
                $return['statusCode'] = 1;
                $return['msg'] = '该会员还没有选择职业分类';
                return $json->encode($return);
            }
            $cate_ids = explode(',', $cate_id);
            $template = 0;
            $gcategory_mod = m('gcategory');
            foreach ($cate_ids as $cate_id)
            {
                $g_info = $gcategory_mod->get($cate_id);
                if ($g_info['template'] == 1)
                {
                    $template = 1;
                }
            }
            $arr['template'] = $template;
            return $json->encode($arr);
        }

        /**
         * 修改职业分类
         */
        public function editCate($cateId,$token)
        {
            global $json;
            $return = array();
            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                $return['statusCode'] = 1;
                $return['msg'] = '用户不存在';
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];
            //=======修改分类========
            $data = array();
            $data['cate_id'] = $cateId;
            $m = m('member');
            $m->edit($user_id,$data);
            $return['statusCode'] = 0;
            $return['msg']        = '修改成功';
            return $json->encode($return);
        }


        public function editMemInfo($data, $photoUrl)
        {
            global $json;
            $return = array();
            $token = $data->token;
            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                $return['statusCode'] = 100;
                $return['msg'] = '账号不存在';
                return $json->encode($return);
            }
            $user_id = $user_info['user_id'];

            $real_name = isset($data->real_name)?$data->real_name:'';
            $sex       = isset($data->sex)?$data->sex:'';
            $birthday  = isset($data->birthday)?$data->birthday:0;
            $shengao   = isset($data->shengao)?$data->shengao:'';
            $tizhong   = isset($data->tizhong)?$data->tizhong:'';
            $xiongwei  = isset($data->xiongwei)?$data->xiongwei:'';
            $yaowei    = isset($data->yaowei)?$data->yaowei:'';
            $tunwei    = isset($data->tunwei)?$data->tunwei:'';
            $techang   = isset($data->techang)?$data->techang:'';
            $im_qq     = isset($data->im_qq)?$data->im_qq:'';
            $cate_id   = isset($data->cate_id)?$data->cate_id:0;

            $arr = array();
            if ($real_name)
            {
                $arr['real_name'] = $real_name;
            }
            if ($sex)
            {
                $arr['gender'] = $sex;
            }
            if ($birthday)
            {
                $arr['birthday'] = $birthday;
            }
            if ($shengao)
            {
                $arr['shengao'] = $shengao;
            }
            if ($tizhong)
            {
                $arr['tizhong'] = $tizhong;
            }
            if ($xiongwei)
            {
                $arr['xiongwei'] = $xiongwei;
            }
            if ($yaowei)
            {
                $arr['yaowei']  = $yaowei;
            }
            if ($tunwei)
            {
                $arr['tunwei'] = $tunwei;
            }
            if ($techang)
            {
                $arr['techang'] = $techang;
            }
            if ($im_qq)
            {
                $arr['im_qq'] = $im_qq;
            }
            if ($cate_id)
            {
                $arr['cate_id'] = $cate_id;
            }
            // 	dump($arr);
            /*修改图像*/
            if ($photoUrl)
            {

                $photoOrg = $user_info['photoUrl'];
                $img_name1 = $user_id.".png";
                $img_name2 = $user_id."_160.png";
                $img_name3 = $user_id."_60.png";
                $imgpath = '/upload_user_photo/user/';
                /*上传原图*/
                $fileName1 = ROOT_PATH.$imgpath.'original/'.$img_name1;
                $fileName2 = ROOT_PATH.$imgpath.'original/'.$img_name2;
                $fileName3 = ROOT_PATH.$imgpath.'original/'.$img_name3;
                //$fileName2 = ROOT_PATH.$imgpath.'35x35/'.$img_name;
                //$fileOrg = ROOT_PATH.$imgpath.'original/'.$photoOrg;
                //====添加之前先删除原来的图片====
                /* if ($photoOrg)
	  		{
	  			@unlink($fileOrg);
	  		} */
                file_put_contents($fileName1, file_get_contents($photoUrl['tmp_name']));
                include ROOT_PATH."/includes/ImageTool.class.php";
                $imgTool = new ImageTool();
                $imgTool->makeThumb($fileName1, 160, 160,$fileName2);
                $imgTool->makeThumb($fileName1, 60, 60,$fileName3);

                $m = m('member');
                $m->edit($user_id,array('photoUrl'=>$user_id));
                //$url=SITE_URL."index.php/club.html-$user_id";
                //QRcode('user',$user_id,$url,$fileName2);
            }

            if (count($arr))
            {
                $m = m('member');
                $m->edit($user_id,$arr);
            }

            $return['statusCode'] = 0;
            $return['msg'] = '修改成功';
            return $json->encode($return);
        }

        /**
         * 会员添加项目经历
         * @param string $token
         * @param stirng $name
         * @param int $time
         * @param  $work_img
         */
        public function addProject($token,$name,$time,$work_img,$role)
        {
            global $json;
            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }

            if (!is_array($work_img))
            {
                $arr = array( 'statusCode'=>1,'msg'=>'传入图片列表无效');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            //=========添加到项目==========
            $data = array();
            $project_mod = m('project');
            $data['user_id'] = $user_id;
            $data['name']  = $name;
            $data['time'] = $time;
            $data['add_time'] = microtime_float();
            $data['type']   = 1;
            $data['role'] = $role;
            $project_id = $project_mod->add($data);

            //=====添加积分=====
            if ($project_id)
            {
                setPoint($user_id, 'addProject');
            }

            $j = 0;
            //=======添加图片到相册===========
            if ($work_img)
            {
                foreach($work_img['error'] as $k=>$v)
                {
                    if ($v) {
                        continue;
                    }
                    $img_name = $user_id  . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
                    $imgpath = '/upload_user_photo/project/';
                    $file['name'] = $work_img['name'][$k];
                    $file['type'] = $work_img['type'][$k];
                    $file['tmp_name'] = $work_img['tmp_name'][$k];
                    $file['error'] = $work_img['error'][$k];
                    $file['size'] = $work_img['size'][$k];
                    /*上传原图*/
                    $fileName1 = ROOT_PATH.$imgpath.'original/'.$img_name;
                    file_put_contents($fileName1, file_get_contents($file['tmp_name']));
                    /* include ROOT_PATH."/includes/ImageTool.class.php";
	  			$imgTool = new ImageTool();
	  			$imgTool->makeThumb($fileName1, 160, 160,$fileName2);
	  			$imgTool->makeThumb($fileName1, 60, 60,$fileName3); */
                    /*上传缩络图*/
                    /* $fileName2 = ROOT_PATH.$imgpath.'235x315/'.$img_name;
	  			 pro_img_multi($file,235,315,$fileName2);
	  			$fileName3 = ROOT_PATH.$imgpath.'520x685/'.$img_name;
	  			pro_img_multi($file,520,685,$fileName3); */
                    /*图片名称添加数据库*/
                    $project_gallery_mod = m('projectgallery');
                    if($img_name){
                        $data = array(
                            'img_url'=>$img_name,
                            'project_id'=>$project_id,
                        );
                        $project_gallery_mod->add($data);

                        $j++;
                    }
                }
            }


            $m = m('member');
            $rs = $m->setInc(" user_id = '{$user_id}' " , 'pic_num',$j);
            $rs = $m->setInc(" user_id = '{$user_id}' " , 'project_num',1);
            $return['statusCode'] = 0;
            $return['msg'] = '添加成功';
            return $json->encode($return);
        }

        /**
         * 会员添加动态
         * @param string $token
         * @param stirng $name
         * @param int $time
         * @param  $work_img
         */
        public function addDynamic($token,$name,$work_img,$role)
        {
            global $json;
            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }

            if (!is_array($work_img))
            {
                $arr = array( 'statusCode'=>1,'msg'=>'传入图片列表无效');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            //=========添加到项目==========
            $data = array();
            $project_mod = m('project');
            $data['user_id'] = $user_id;
            $data['name']  = $name;
            $data['add_time'] = microtime_float();
            $data['type']   = 2;
            $data['role'] = $role;
            $project_id = $project_mod->add($data);

            //=====添加动态=====
            if ($project_id)
            {
                setPoint($user_id, 'addDynamic');
            }

            $j = 0;
            //=======添加图片到相册===========
            if ($work_img)
            {
                foreach($work_img['error'] as $k=>$v)
                {
                    if ($v) {
                        continue;
                    }
                    $img_name = $user_id  . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
                    $imgpath = '/upload_user_photo/project/';
                    $file['name'] = $work_img['name'][$k];
                    $file['type'] = $work_img['type'][$k];
                    $file['tmp_name'] = $work_img['tmp_name'][$k];
                    $file['error'] = $work_img['error'][$k];
                    $file['size'] = $work_img['size'][$k];
                    /*上传原图*/
                    $fileName1 = ROOT_PATH.$imgpath.'original/'.$img_name;
                    file_put_contents($fileName1, file_get_contents($file['tmp_name']));
                    /*上传缩络图*/
                    /* $fileName2 = ROOT_PATH.$imgpath.'235x315/'.$img_name;
	  			 pro_img_multi($file,235,315,$fileName2);
	  			$fileName3 = ROOT_PATH.$imgpath.'520x685/'.$img_name;
	  			pro_img_multi($file,520,685,$fileName3); */
                    /*图片名称添加数据库*/
                    $project_gallery_mod = m('projectgallery');
                    if($img_name){
                        $data = array(
                            'img_url'=>$img_name,
                            'project_id'=>$project_id,
                        );
                        $project_gallery_mod->add($data);

                        $j++;
                    }
                }
            }


            $m = m('member');
            $rs = $m->setInc(" user_id = '{$user_id}' " , 'pic_num',$j);
            $rs = $m->setInc(" user_id = '{$user_id}' " , 'dynamic_num',$j);
            $return['statusCode'] = 0;
            $return['msg'] = '添加成功';
            return $json->encode($return);
        }

        /**
         * 动态|简历 列表
         */
        public function projectList($token,$userId,$type,$pageSize,$pageIndex)
        {
            global $json,$ecs,$db;
            $like_cate = 'like_project';

            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            $conditions = "";
            if ($type)
            {
                $conditions .= " type=$type AND ";
            }
            $conditions .= " user_id=$userId ";
            $project_mod = m('project');
            $project_list = $project_mod->find(array(
                "conditions" => $conditions,
                "order"   => "add_time desc",
                "limit"   => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                "index_key" => '',
            ));

            //=====相册|点赞====
            $pgallery_mod = m('projectgallery');
            $like_mod = m('like');
            foreach ($project_list as $k=>$v)
            {
                $project_id = $v['id'];
                $gallery_list = $pgallery_mod->find(array(
                    'conditions' => "project_id= $project_id ",
                    "fields"     => "img_url",
                    "index_key" => '',
                ));
                foreach ($gallery_list as $k1=>$v1)
                {
                    $project_list[$k]['img_list'][] =  SITE_URL.'upload_user_photo/project/original/'.$v1['img_url'];
                }

                $like_info = $like_mod->get("uid=$user_id AND like_id=$project_id AND cate='$like_cate' ");
                if ($like_info)
                {
                    $project_list[$k]['is_zan'] = 1;
                }
                else
                {
                    $project_list[$k]['is_zan'] = 0;
                }

            }

            $sql = "SELECT count(*) FROM ".$ecs->table('project')." WHERE ".$conditions;
            $count = $db->getOne($sql);
            $page_count = ceil($count/$pageSize);
            if ($pageIndex < $page_count)
            {
                $hasNext = true;
            }
            else
            {
                $hasNext = false;
            }

            return $json->encode(array('hasNext'=>$hasNext,"projectList"=>$project_list,'count'=>$count));
        }


        /**
         * 检查会员是否具备认证条件
         */
        public function checkUserAuth($token)
        {
            global $json,$db,$ecs;
            $return = array();
            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            $status = 1;
            $msg    = '';
            //========判断个人资料是否完整=》判断member_attr对应的user_id是否有数据=====
            $member_attr_mod = m('memberattr');
            $mattr_info = $member_attr_mod->find(array(
                'conditions' => "user_id=$user_id",
            ));
            if (!$mattr_info)
            {
                $status = 0;
                $msg .= '资料不完整-';
            }

            //=========判断是否具备演绎经验=>也就是判断 project表对应的user_id是否有数据====
            $project_mod = m('project');
            $project_info = $project_mod->find(array(
                'conditions' => "user_id=$user_id",
            ));
            if (!$project_info)
            {
                $status = 0;
                $msg .= '无演艺经验-';
            }

            //========判断关注和粉丝数目是否合格      以后补充======

            if ($status)
            {
                $return['statusCode'] = 0;
                $return['msg']        = '具备认证资格';
            }
            else
            {
                $return['statusCode'] = 1;
                $return['msg']        = $msg;
            }

            return $json->encode($return);
        }

        /**
         * 上传认证图片 （身份证照片）
         * @param string $token
         * @param string $authImg
         */
        public function addAuthImg($token,$authImg)
        {
            global $json;
            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            //=======判断是否是正在审核中或者审核失败  如果是正在审核中不允许提交    如果是审核失败要先把老的数据删掉===
            $auth_mod = m('useryellow');
            $auth_info = $auth_mod->get('user_id='.$user_id);
            if ($auth_info)
            {
                if ($auth_info['status'] == 0)
                {
                    $arr = array( 'statusCode'=>1,'msg'=>'正在审核中!请勿重复提交');
                    return $json->encode($arr);
                }
                elseif ($auth_info['status'] == 2)
                {
                    $arr = array( 'statusCode'=>1,'msg'=>'认证已通过!请勿重复提交');
                    return $json->encode($arr);
                }
                elseif ($auth_info['status'] == 1)
                {
                    //==认证失败  要先把老数据删掉
                    $auth_mod->drop('user_id='.$user_id);
                    $imgpath = '/upload_user_photo/auth/';
                    $fileName1 = ROOT_PATH.$imgpath.'original/'.$auth_info['auth_img'];
                    @unlink($fileName1);
                }

            }

            /*上传原图*/
            $img_name = $user_id  . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
            $imgpath = '/upload_user_photo/auth/';
            $fileName1 = ROOT_PATH.$imgpath.'original/'.$img_name;
            file_put_contents($fileName1, file_get_contents($authImg['tmp_name']));

            if ($img_name)
            {
                $data['user_id']  = $user_id;
                $data['auth_img'] = $img_name;
                $auth_mod->add($data);
            }

            $arr = array( 'statusCode'=>0,'msg'=>'添加成功');
            return $json->encode($arr);
        }

        /**
         * 添加认证的辅助信息
         */
        public function authAssist($data)
        {
            global $json;
            $token = $data->token;
            $userInfo = getUserInfo($token);
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];

            $auth_mod = m('useryellow');
            $auth_info = $auth_mod->get('user_id='.$user_id);
            if (!$auth_info)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $arr = array();
            $arr['weibo'] = isset($data->weibo)?$data->weibo:'';
            $arr['baike'] = isset($data->baike)?$data->baike:'';
            $arr['boke'] = isset($data->boke)?$data->boke:'';
            $arr['msg'] = isset($data->msg)?$data->msg:'';
            if (count($arr))
            {
                $auth_mod->edit('user_id='.$user_id,$arr);
            }
            $arr = array( 'statusCode'=>0,'msg'=>'添加辅助信息成功');
            return $json->encode($arr);
        }


        /**
         * 认证状态
         */
        public function authStatus($token)
        {

            global $json;
            $userInfo = getUserInfo($token);
            $return = array();
            if (!$userInfo)
            {
                $arr = array( 'statusCode'=>100,'msg'=>'账号不存在');
                return $json->encode($arr);
            }
            $user_id = $userInfo['user_id'];
            $useryellow = m('useryellow');
            $yellow_info = $useryellow->get("user_id=$user_id");
            if ($yellow_info)
            {
                if ($yellow_info['status'] == 0)
                {
                    $return['statusCode'] = 0;
                    $return['msg']        = '正在认证中';
                }
                elseif ($yellow_info['status'] == 1)
                {
                    $return['statusCode'] = 1;
                    $return['msg']        = '认证失败';
                    $return['error']      = $yellow_info['fail_reason'];
                }
                elseif ($yellow_info['status'] == 2)
                {
                    $return['statusCode'] = 2;
                    $return['msg']        = '认证通过';
                }
            }
            else
            {
                $return['statusCode'] = 3;
                $return['msg']        = '没有发起认证';
            }
            return $json->encode($return);
        }














        public function coin($token,$pageSize,$pageIndex,$type)
        {
            global $json,$db,$ecs;
            if($pageIndex<1)
            {
                $pageIndex = 1;
            }

            $sql = "SELECT user_id,point,coin  FROM " .$ecs->table('member')." WHERE user_token='" . $token . "'";

            $row= $db->getRow($sql);
            $typestr='';
            if($type=='add')
            {
                $typestr=' and opt=\'add\' ';
            }else if($type=='del')
            {
                $typestr=' and opt=\'del\' ';
            }
            else if($type=='order')
            {
                $typestr=' and type=\'order\' ';
            }
            else if($type=='sheji')
            {
                $typestr=' and type=\'sheji\' ';
            }
            else if($type=='jiepai')
            {
                $typestr=' and type=\'jiepai\' ';
            }

            //all，order，sheji，jiepai
            //return $typestr;
            if($row){

                $model_album = m('coin_log');
                $album_data = $model_album->find(array(
                    'conditions' => 'uid = ' . $row['user_id'].$typestr,
                    'count' => true,
                    'order' => 'id DESC',
                    'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                ));
                $arr_tmp['coin']=$row['coin'];
                $arr_tmp['statusCode']=0;
                $arr_tmp['scoreItemList']=$album_data;
            }else{
                $arr_tmp['statusCode']=10000;
            }

            return $json->encode($arr_tmp);
        }
        public function point($token,$pageSize,$pageIndex,$type)
        {
            global $json,$db,$ecs;
            if($pageIndex<1)
            {
                $pageIndex = 1;
            }

            $sql = "SELECT user_id,point,coin  FROM " .$ecs->table('member')." WHERE user_token='" . $token . "'";

            $row= $db->getRow($sql);

            $typestr='';
            if($type=='add')
            {
                $typestr=' and opt=\'add\' ';
            }else if($type=='del')
            {
                $typestr=' and opt=\'del\' ';
            }
            else if($type=='order')
            {
                $typestr=' and type=\'order\' ';
            }
            else if($type=='comment')
            {
                $typestr=' and type=\'comment\' ';
            }
            else if($type=='hudong')
            {
                $typestr=' and type=\'hudong\' ';
            }



            //order,comment,hudong


            if($row){

                $model_album = m('point_log');
                $album_data = $model_album->find(array(
                    'conditions' => 'uid = ' . $row['user_id'].$typestr,
                    'count' => true,
                    'order' => 'id DESC',
                    'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                ));
                $arr_tmp['point']=$row['point'];
                $arr_tmp['statusCode']=0;
                $arr_tmp['scoreItemList']=$album_data;
            }else{
                $arr_tmp['statusCode']=10000;
            }

            return $json->encode($arr_tmp);
        }

        public function coupon($token,$status,$pageSize,$pageIndex)
        {
            global $json,$db,$ecs;
            if($pageIndex<1)
            {
                $pageIndex = 1;
            }

            $sql = "SELECT user_id  FROM " .$ecs->table('member')." WHERE user_token='" . $token . "'";

            $row= $db->getRow($sql);

            if($row){

                $model_album = m('couponsn');
                $album_data = $model_album->find(array(
                    'conditions' => 'uid = ' . $row['user_id'].' AND status='.$status,
                    'count' => true,
                    'order' => 'coupon_sn DESC',
                    'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                ));
                $arr_tmp['statusCode']=0;
                $arr_tmp['datalist']=$album_data;
            }else{
                $arr_tmp['statusCode']=10000;
            }

            return $json->encode($arr_tmp);
        }

        /**
         * 我的关注列表
         *
         * @param String $token  用户标识；int $pageSize  每页显示的个数；int $pageIndex 第几页；
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function follow_list($token, $pageSize, $pageIndex)
        {
            global $json;
            $return =array();
            $mod_follow = m('userfollow');
            $user_info   = getToken($token);
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
                return $json->encode($return);
            }

            $user_id     = $user_info['user_id'];
            $follows = $mod_follow->findAll(array(
                'conditions'    => "uid=" . $user_id ,
                'fields'        => 'this.*',
                'count'         => true,
                'limit'         => $pageSize * ($pageIndex-1) . ','. $pageSize,
                'order'         => 'add_time DESC',
                'index_key'	  => '',
            ));

            #----！切记不要链表！-----#

            //获取 裁缝的基础信息
            $sids = array();
            //$sids = i_array_column($follows, 'follow_uid');
            if($follows) {
                foreach($follows as $val ) {
                    $sids[] = $val['follow_uid'];
                }
            }
            $store_mod  =& m('store');
            $stores = $store_mod->findAll(array(
                'conditions'    => db_create_in($sids,'store_id'),
                'fields'        => 'popularity, published, fans, owner_name, store_logo, level',//人气,作品,粉丝,昵称,logo图,等级
            ));


            //获取 裁缝等级的logo 数组关联，不链表
            $member_lv_mod  =& m('memberlv');
            //$sgradeids = i_array_column($stores, 'level');
            if($stores) {
                foreach($stores as $val ) {
                    $sgradeids[] = $val['level'];
                }
            }
            $lvs = $member_lv_mod->findAll(array(
                'conditions'    => db_create_in($sgradeids, 'member_lv_id'),
                'fields'        => 'name, lv_logo',//等级名称 ，等级logo

            ));
            if ($follows) {
                foreach($follows as &$row){
                    $row['popularity'] = $stores[$row['follow_uid']]['popularity'];
                    $row['published']  = $stores[$row['follow_uid']]['published'];
                    $row['fans']       = $stores[$row['follow_uid']]['fans'];
                    $row['owner_name'] = $stores[$row['follow_uid']]['owner_name'];
                    $row['store_id']   = $stores[$row['follow_uid']]['store_id'];
                    $row['store_logo'] = site_url().'/'.$stores[$row['follow_uid']]['store_logo'];
                    $row['lv_logo']    = '';//默认等级logo
                    $row['lv_name']    = '';
                    if($lvs[$stores[$row['follow_uid']]['level']])
                    {
                        $row['lv_logo'] = site_url().$lvs[$stores[$row['follow_uid']]['level']]['lv_logo'];
                        $row['lv_name'] = $lvs[$stores[$row['follow_uid']]['level']]['name'];
                    }

                }
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'follow_list' => !empty($follows) ? $follows : '',
                    'success'     => '获取成功',
                )
            );

            return $json->encode($return);

        }

        /**
         * 我的收藏列表
         *
         * @param String $token  用户标识；int $pageSize  每页显示的个数；int $pageIndex 第几页；
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function getCollect($token, $pageSize, $pageIndex, $type)
        {
            global $json;

            $user_info = getToken($token);
            $user_id   = $user_info['user_id'];

            if($user_id) {
                $cnf = $this->_get_collect_cnf($type);

                $m   = & m($cnf['mod']);
                $collects = $m->find(array(
                    'join'       => 'be_collect',
                    'fields'     => $cnf['fields'],
                    'conditions' => 'collect.user_id = ' . $user_id,
                    'count'      => true,
                    'order'      => 'collect.add_time DESC',
                    'limit'      => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                ));

                //ns add 获取晒单详细
                if($type == 'single'){
                    $userphoto_mod =& m('userphoto');
                    foreach($collects as $key=>$val){
                        $userphoto = $userphoto_mod->find(array(
                            'conditions' => 'status=1 AND album_id='.$val['id'],
                            'order'      => 'add_time DESC',
                        ));
                        //获取图片的绝对路径
                        foreach($userphoto as $k=>$v){
                            $collects[$key]['userphoto'][$k]['id'] = $v['id'];
                            $collects[$key]['userphoto'][$k]['url'] = getUserPhotoUrl('shaidan',$v['url'],500);
                        }
                    }
                }
                $count = $m->getCount();   //获取统计的数据
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'count'     => $count,
                        'collects' => $collects,
                    )
                );
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在',
                    )
                );
            }

            return $json->encode($return);
        }


        /**
         * 获取收藏配置
         * @return void
         * @access private
         * @version 1.0.0 (2014-12-15)
         * @author Xiao5
         */
        private function _get_collect_cnf($type, $itemid=1)
        {
            switch ($type)
            {
                //面料
                case 'fabric':
                    return array(
                        'mod'    => 'part',                                              //制定M层
                        'fields' => 'collect.id, collect.user_id, collect.item_id, this.goods_sn, this.part_id, this.part_small, this.click_count', //需得到字段
                    );
                    break;
                //晒单
                case 'single':
                    return array(
                        'mod'    => 'member_single',                                     //制定M层
                        'fields' => 'collect.id, collect.user_id, this.description, collect.item_id', //需得到字段
                    );
                    break;
                case 'customs':
                    return array(
                        'mod'    => 'customs',                                              //制定M层
                        'fields' => 'collect.id, collect.user_id, collect.item_id, this.cst_name, this.cst_id, this.cst_image, this.cst_likes', //需得到字段
                    );
                    break;
                default:
                    //TODO
                    break;
            }
        }

        function qrcode($userId)
        {
            global $json;
            //$url=SITE_URL.$view->build_url(array('app'=>'service','act'=>'info','arg'=>$res['idserve']));
            //var_dump($url);exit;

            $url=SITE_URL."index.php/club.html-$userId";

            $mqrcode=getQrcodeImage('user',$userId,4);
            if (!$mqrcode)
            {
                QRcode('user',$userId,$url);
                $mqrcode=getQrcodeImage('user',$userId,4);
            }
            $arr_tmp['statusCode']=0;
            $arr_tmp['mqrcode']=$mqrcode;
            return $json->encode($arr_tmp);
        }


        /**
         * feedback
         *
         * @param String $token用户标识  string $content 反馈内容
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function feedback($token, $content){
            global $json;
            $userInfo = getUserInfo($token);
            $user_id = !empty($userInfo['user_id']) ? $userInfo['user_id'] : 0;

            $data = array();
            $feedback_mod    = m('feedback');
            $data['description']  =	$content;
            $data['add_time'] = gmtime();
            $data['user_id']  = $user_id;
            if ($feedback_mod->add($data)) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success'  => '反馈提交成功',

                    )
                );
                return $json->encode($return);
            } else {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '反馈提交失败',
                    ),
                );
                return $json->encode($return);
            }
        }


        //修改用户信息
        function modifyUserData($userID,$data){
            global $json,$db,$ecs;

            //插入的数据值



            $nikename = array_key_exists('newNickName', $data)?$data['newNickName']:'';
            $constella= array_key_exists('newConstellation', $data)?$data['newConstellation']:'';
            $sex      = array_key_exists('newSex', $data)?$data['newSex']:'';
            $tell     = array_key_exists('newPhone', $data)?$data['newPhone']:'';
            //$addr     = array_key_exists('newAddress', $data)?$data['newAddress']:'';
            $newBlood = array_key_exists('newBloodtype', $data)?$data['newBloodtype']:'';

            $signature = array_key_exists('newsignature', $data)?$data['newsignature']:'';
            //$def_addr = $data['newdef_addr']?$data['newdef_addr']:'';
            $province = array_key_exists('newprovince', $data)?$data['newprovince']:'';
            $city = array_key_exists('newcity', $data)?$data['newcity']:'';
            //$gender = $data['newgender']?$data['newgender']:'';

            $data['newNickName']?$newNickName = "nickname = '".$nikename."',":'';
            $data['newSex']?$newSex = "gender = ".$sex.",":'';
            //$data['newPhone']?$newPhone = "phone_mob = '".$tell."',":'';



            //处理上传头像
            //$img              = $data['newheadImg']?base64_decode($data['newheadImg']):'';
            $img              = $data['newheadImg']?file_get_contents($data['newheadImg']['tmp_name']):'';
            //$img='';
            if ($img)
            {
                $img_name = $userID."_".gmtime().'.jpg';
                $imgpath = '/upload_user_photo/avatar/';

                /*上传原图*/
                $fileName1 = ROOT_PATH.$imgpath.'original/'.$img_name;
                file_put_contents($fileName1,$img);

                /*生成3分缩络图*/
                $fileName2 = ROOT_PATH.$imgpath.'20/'.$img_name;
                pro_img_multi($data['newheadImg'],20,20,$fileName2);
                $fileName3 = ROOT_PATH.$imgpath.'48/'.$img_name;
                pro_img_multi($data['newheadImg'],48,48,$fileName3);
                $fileName4 = ROOT_PATH.$imgpath.'162/'.$img_name;
                pro_img_multi($data['newheadImg'],162,162,$fileName4);


            }
            $data['newheadImg']?$newheadImg = "avatar = '$img_name' ":'';

            $data['newsignature']?$newsignature = "signature = '".$signature."',":'';
            //$data['newdef_addr']?$newdef_addr = "def_addr = '".$def_addr."',":'';
            $data['newprovince']?$newprovince = "province = '".$province."',":'';
            $data['newcity']?$newcity = "city = '".$city."',":'';
            //$data['newgender']?$newgender = "gender = '".$gender."',":'';


            //return $json->encode($data['newheadImg']);

            $str = $newNickName.$newSex.$newBlood.$newheadImg.$newsignature.$newprovince.$newcity;
            $sql_str = substr($str,0,-1);
            $sql  = "UPDATE ".$ecs->table('member')." SET ".$sql_str." where user_id = $userID";
            //$sql = "select nick_name from ecm_member";
            $row = $db->query($sql);
            //$row = $db->getALL($sql);

            if ($row)
            {
                $statusCode = array("statusCode"=>0,"msg"=>"修改成功");
                return $json->encode($statusCode);
                //return $json->encode($sql_str);
            }else{
                $statusCode = array("statusCode"=>1,"msg"=>"修改失败");
                return $json->encode($statusCode);
            }

        }


        /**
         * 获得指定国家的所有省份
         *
         * @access      public
         * @param       int     country    国家的编号
         * @return      array
         */
        protected function _get_regions($region_id = 0)
        {
            global $db,$ecs;
            $sql = 'SELECT region_name FROM ' . $ecs->table('region') .
                " WHERE region_id = '$region_id'";
            return $db->getOne($sql);
        }




        /**
         * 解密函数
         * @param   string  $str    加密后的字符串
         * @param   string  $key    密钥
         * @return  string  加密前的字符串
         */
        private function _decrypt($str, $key = AUTH_KEY)
        {
            $coded = '';
            $keylength = strlen($key);
            $str = base64_decode($str);

            for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength)
            {
                $coded .= substr($str, $i, $keylength) ^ $key;
            }

            return $coded;
        }

        /**
         * 获得当前格林威治时间的时间戳
         *
         * @return  integer
         */
        private function _gmtime()
        {
            return (time() - date('Z'));
        }

        /**
         * 截取UTF-8编码下字符串的函数
         *
         * @param   string      $str        被截取的字符串
         * @param   int         $length     截取的长度
         * @param   bool        $append     是否附加省略号
         *
         * @return  string
         */
        private function _sub_str($str, $length = 0, $append = true)
        {
            $str = trim($str);
            $strlength = strlen($str);

            if ($length == 0 || $length >= $strlength)
            {
                return $str;
            }
            elseif ($length < 0)
            {
                $length = $strlength + $length;
                if ($length < 0)
                {
                    $length = $strlength;
                }
            }

            if (function_exists('mb_substr'))
            {
                $newstr = mb_substr($str, 0, $length, EC_CHARSET);
            }
            elseif (function_exists('iconv_substr'))
            {
                $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
            }
            else
            {
                //$newstr = trim_right(substr($str, 0, $length));
                $newstr = substr($str, 0, $length);
            }

            if ($append && $str != $newstr)
            {
                $newstr .= '...';
            }

            return $newstr;
        }

        /**
         *  测试  app信鸽消息推送(用户删掉)
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        public function toStoreXinApp($addtime, $type)
        {

            $in = m("memberinvite");
            $articles = $in->findAll(array(
                'conditions' => '1=1',
            )); //找出所有符合条件的文章
            print_r($articles);exit;
            //发送消息推送
            include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
            $push = new XingeMeg($addtime, $type);
        }

        /**
         *  创业培训 文章列表
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        public function article($pageIndex, $pageSize)
        {
            global $json;
            //获取固定类文章
            $_acategory_mod  = &m('acategory');
            $_article_mod    = &m('article');
            $cate_ids_kehu   = $_acategory_mod->get_descendant(54);//客户培训手册
            $cate_ids_peixun = $_acategory_mod->get_descendant(57);//培训手册
            $cate_ids = array_merge($cate_ids_kehu, $cate_ids_peixun);

            $conditions = '';
            !empty($cate_ids)&& $conditions = ' AND cate_id ' . db_create_in($cate_ids);
            $articles = $_article_mod->find(array(
                'conditions' => 'if_show=1 AND store_id=0 AND code = ""' . $conditions,
                'fields'     => 'article_id, title, cate_id, content, add_time',
                'limit'		 => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                'order'      => 'add_time DESC, sort_order ASC',
                'count'      => true,  //允许统计
                'index_key'	 => '',
            )); //找出所有符合条件的文章

            $count = $_article_mod->getCount();
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'articles' => !empty($articles) ? $articles : array(),
                    '$count'   => !empty($count) ? $count : 0,
                    'success'  => '获取数据成功',
                )
            );

            return $json->encode($return);
        }

        /**
         *  特权码
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        public function tequan($token,$sn)
        {
            global $json;
            $return = array();
            $user_info   = getToken($token);
            $_meminvite_mod = &m('memberinvite');
            $member_mod  = m('member');
            $_special_code_mod = & m('special_code');


            $lv_name = '';
            $sign = 1;
            $user_id = $user_info['user_id'];
            if($user_info['member_lv_id']>1) $sign=0;

            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }

            //bd码的才能 进行到这一步
            //        $bd_info = $_meminvite_mod->get("invitee = '$user_id' and type=1");
            //        if(empty($bd_info)){
            //            $return = array(
            //                'statusCode' => 0,
            //                'error' => array(
            //                    'errorCode' => 102,
            //                    'msg'       => '未绑定BD码,不能参加此活动',
            //                ),
            //            );
            //            return $json->encode($return);
            //        }


            if(!$sn){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '！特权码不能为空',
                    )
                );
                return $json->encode($return);
            }



            $path = ROOT_PATH."/data/config/special_code.php";
            file_exists($path) && $this->_cate = include $path;

            $info = $_special_code_mod->get('cate<20 and sn="'.strtoupper($sn).'"');//转变大写
            $cate_name = $this->_cate[$info['cate']]['name'];
            $member_lv_id=$this->_cate[$info['cate']]['member_lv_id'];




            if(empty($info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => $cate_name.'无效',
                    )
                );
                return $json->encode($return);
            }


            if($info['expire_time']<time()){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => $cate_name.'已经过期',
                    )
                );
                return $json->encode($return);
            }

            if($info['is_used']==1){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => $cate_name.'已被使用',
                    )
                );
                return $json->encode($return);
            }

            if($user_info['member_lv_id']>$member_lv_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 109,
                        'msg'       => '当前用户等级大于特权码等级',
                    )
                );
                return $json->encode($return);
            }

            // 线上发放的码     绑定到了用户
            if($info['to_id'] !=' ' && $info['to_id'] !=$user_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 108,
                        'msg'       => '无权使用此'.$cate_name.'！',
                    )
                );
                return $json->encode($return);
            }


            //cate in(1,2)  现在只有2种码  两种码互斥   <20
            $info2 = $_special_code_mod->get('is_used=1 and cate<20 and  to_id='.$user_id);

            if($info2){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg'       => '当前用户已经使用过一次同类型特权码！',
                    )
                );
                return $json->encode($return);
            }

            $data = array(
                'to_id'=> $user_info['user_id'],//$user_id
                'user_name'=>$user_info['user_name'],
                'is_used'=>1,
                'source'=>'mfd|app',
                'to_time'=>time(),

            );
            $ret = $_special_code_mod->edit('cate<20 and sn="'.$sn.'"',$data);


            if(!$ret){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '系统异常',
                    )
                );
                return $json->encode($return);
                return;
            }


            switch ($info['cate']){
                case 1:
                    //越级码
                    $lv_name = change_lv(4,$user_id,$user_info['invite'],$sign);
                    break;
                case 2:
                    //初级码
                    $lv_name = change_lv(2,$user_id,$user_info['invite'],$sign);
                    break;
                default:
                    break;
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    //                'point'=>$point,
                    'member_lv_id'=>$member_lv_id,
                    'member_lv_name'=>$lv_name,
                    'name'=>$cate_name,
                    'description'=>$this->_cate[$info['cate']]['description'],
                )
            );

            return $json->encode($return);

        }

        /**
         *  特权码 详细信息
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        function tequan_info($token){
            global $json;
            $user_info   = getToken($token);
            $_special_code_mod = & m('special_code');

            $return = array();
            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }


            $path = ROOT_PATH."/data/config/special_code.php";
            file_exists($path) && $this->_cate = include $path;

            //cate <20  暂时
            $info = $_special_code_mod->find(array(
                'index_key' =>'',
                'conditions'=>'cate<20 and is_used=1 and to_id="'.$user_info['user_id'].'"',
                'fields'=>'sn,cate,user_name,expire_time,to_time'
            ));

            if(empty($info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '无任何特权',
                    )
                );
                return $json->encode($return);
                return;
            }
            foreach($info as $k=>$v){
                $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
                $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
            }
            unset($info[0]['cate']);
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    'data'=>$info
                )
            );


            return $json->encode($return);

        }





        /**
         *激活酷卡
         *@author liang.li <1184820705@qq.com>
         *@2015年10月9日
         */
        function activDebit($token,$activSn)
        {
            global $json;

            if (!$activSn)
            {
                return $this->eresult('缺少激活码参数 ');
            }
            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                return $this->tresult();
            }
            $user_id = $user_info['user_id'];
            $debit_line_mod = m('debitlines');
            $debit_mod = m('debit');
            $transaction = $debit_line_mod->beginTransaction();//=====  开启事物  =====
            $item = $debit_line_mod->get(array(
                'conditions' => "active_sn = '$activSn' "
            ));
            if (!$item)
            {
                $debit_line_mod->rollback();
                return $this->eresult('此激活码不存在');
            }
            if ($item['is_active'] == 1)
            {
                $debit_line_mod->rollback();
                return  $this->eresult('此卡已激活！请勿重复操作');
            }
            $debit_data['debit_name'] = "酷券";
            $debit_data['debit_sn'] = $item['d_sn'];
            $debit_data['money'] = $item['money'];
            $debit_data['user_id'] = $user_id;
            $debit_data['source'] = 'active';
            $debit_data['add_time'] = time();
            $debit_data['cate'] = $item['cate'];
            $debit_data['expire_time'] = $item['expire_time'];
            $debit_id = $debit_mod->add($debit_data);
            if (!$debit_id)
            {
                $debit_line_mod->rollback();
                return $this->eresult('加入抵用券失败');
            }
            if (!$debit_line_mod->edit($item['id'],array('d_id'=>$debit_id,'is_active'=>1)))
            {
                $debit_line_mod->rollback();
                return $this->eresult('激活卡失败');
            }

            $debit_line_mod->commit($transaction);

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    'coin'=>$user_info['coin']+$info['work_num'],
                    'get_coin'=> $info['work_num'],
                    'name'=>$cate_name,
                    'description'=>'激活成功',
                )
            );

            return $json->encode($return);
            // return $this->sresult();
        }


        /**
         *  麦富迪e卡
         *
         * @param  array $arr 消息数组信息
         * add by yu
         * 2015.11.10  edit by liang.li
         * @return  string
         */
        public function kuka_v2($token, $sn)
        {
            global $json;
            $return = array();
            $user_info   = getToken($token);

            $member_mod  = & m('member');
            $_special_code_mod = & m('special_code');
            $user_id = $user_info['user_id'];


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                )
            );


            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }

            //=====  如果首字母是Q开头的代表是酷券  =====
            $f_sn = substr($sn, 0,1);
            if (strtoupper($f_sn) == "Q")
            {
                $res = $this->activDebit($token, $sn);
                return $res;
            }

            if($user_info['member_lv_id']>1) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 200,
                        'msg'       => '创业者不能参与此活动',
                    ),
                );
                return $json->encode($return);
            }


            if(!$sn){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '麦富迪E卡不能为空！',
                    )
                );
                return $json->encode($return);
            }



            $path = ROOT_PATH."/data/config/special_code.php";
            file_exists($path) && $this->_cate = include $path;

            $info = $_special_code_mod->get('cate>19 and sn="'.strtoupper($sn).'"');//转变大写
            $cate_name = $this->_cate[$info['cate']]['name'];


            if(empty($info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '激活码无效,请重新输入！',
                    )
                );
                return $json->encode($return);
            }


            if($info['expire_time']<time()){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => $cate_name.'已经过期'.'！',
                    )
                );
                return $json->encode($return);
            }

            if($info['is_used']==1){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => $cate_name.'已被使用'.'！',
                    )
                );
                return $json->encode($return);
            }

            // 线上发放的码     绑定到了用户
            if($info['to_id'] !=' ' && $info['to_id'] !=$user_id){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 108,
                        'msg'       => '无权使用此'.$cate_name.'！',
                    )
                );
                return $json->encode($return);
            }


            // >=20是麦富迪e卡 后期 要看 麦富迪e卡 互斥不互斥
            //        $info2 = $_special_code_mod->get('is_used=1 and cate>19 and  to_id='.$user_id);
            //        if($info2){
            //            $return = array(
            //                'statusCode' => 0,
            //                'error' => array(
            //                    'errorCode' => 106,
            //                    'msg'       => '！当前用户已经使用过一次麦富迪E卡',
            //                )
            //            );
            //            return $json->encode($return);
            //        }

            switch ($info['cate']){
                case 20:
                    $member_mod->setInc(array('user_id'=>$user_id),'coin',$info['work_num']);
                    //麦富迪币  充值记录
                    $order_cash_log = & m('ordercashlog');
                    $_data_coin = array();
                    $_data_coin['add_time'] = time();
                    $_data_coin['name'] = '麦富迪E卡|'.$info['work_num'].'币';
                    $_data_coin['type'] = 2; //麦富迪币
                    $_data_coin['minus'] = 4;  //麦富迪e卡充值
                    $_data_coin['cash_money'] = $info['work_num'];  //
                    $_data_coin['user_id'] = $user_id;
                    $_data_coin['admin_id'] = 1;
                    $order_cash_log->add($_data_coin);

                    //站内信  14麦富迪e卡   coin  麦富迪币
                    sendSystem($user_id, 14, '恭喜，您已经成功使用麦富迪E卡！', '尊敬的麦富迪达人,恭喜您已成功获取'.$info['work_num'].'麦富迪币') ;
                    break;
                case 21:
                    $member_mod->setInc(array('user_id'=>$user_id),'coin',$info['work_num']);
                    //麦富迪币  充值记录
                    $order_cash_log = & m('ordercashlog');
                    $_data_coin = array();
                    $_data_coin['add_time'] = time();
                    $_data_coin['name'] = '麦富迪E卡|'.$info['work_num'].'币';
                    $_data_coin['type'] = 2; //麦富迪币
                    $_data_coin['minus'] = 4;  //麦富迪e卡充值
                    $_data_coin['cash_money'] = $info['work_num'];  //
                    $_data_coin['user_id'] = $user_id;
                    $_data_coin['admin_id'] = 1;
                    $order_cash_log->add($_data_coin);

                    //站内信  14麦富迪e卡   coin  麦富迪币
                    sendSystem($user_id, 14, '恭喜，您已经成功使用麦富迪E卡！', '尊敬的麦富迪达人,恭喜您已成功获取'.$info['work_num'].'麦富迪币') ;
                    break;
                // 线上 创业 购买的卡 转赠给消费者 激活使用
                case 22:
                    $member_mod->setInc(array('user_id'=>$user_id),'coin',$info['work_num']);
                    //麦富迪币  充值记录
                    $order_cash_log = & m('ordercashlog');
                    $_data_coin = array();
                    $_data_coin['add_time'] = time();
                    $_data_coin['name'] = '麦富迪E卡|'.$info['work_num'].'币';
                    $_data_coin['type'] = 2; //麦富迪币
                    $_data_coin['minus'] = 4;  //麦富迪e卡充值
                    $_data_coin['cash_money'] = $info['work_num'];  //
                    $_data_coin['user_id'] = $user_id;
                    $_data_coin['admin_id'] = 1;
                    $order_cash_log->add($_data_coin);

                    //站内信  14麦富迪e卡   coin  麦富迪币
                    sendSystem($user_id, 14, '恭喜，您已经成功使用麦富迪E卡！', '尊敬的麦富迪达人,恭喜您已成功获取'.$info['work_num'].'麦富迪币') ;
                    break;

                default:
                    break;
            }


            $data = array(
                'to_id'=> $user_id,
                'user_name'=>$user_info['user_name'],
                'is_used'=>1,
                'source'=>'mfd|app',
                'to_time'=>time(),

            );
            $ret = $_special_code_mod->edit('cate>19 and sn="'.$sn.'"',$data);


            if(!$ret){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '系统异常',
                    )
                );
                return $json->encode($return);
                return;
            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    'coin'=>$user_info['coin']+$info['work_num'],
                    'get_coin'=> $info['work_num'],
                    'name'=>$cate_name,
                    'description'=>$this->_cate[$info['cate']]['description'],
                )
            );

            return $json->encode($return);

        }

        /**
         *  麦富迪e卡
         *
         * @param  array $arr 消息数组信息
         * 2015.11.10  edit by liang.li
         * @return  string
         */
        public function kuka($token, $sn)
        {
            global $json;
            $return = array();
            $user_info   = getToken($token);

            $member_mod  = & m('member');
            $_special_code_mod = & m('special_code');
            $user_id = $user_info['user_id'];



            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }

            //=====  如果首字母是Q开头的代表是酷券  =====
            $f_sn = substr($sn, 0,1);
            if (strtoupper($f_sn) == "Q")
            {
                $res = $this->activDebit($token, $sn);
                return $res;
            }

            if($user_info['member_lv_id']>1) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 200,
                        'msg'       => '创业者不能参与此活动',
                    ),
                );
                return $json->encode($return);
            }


            if(!$sn){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '麦富迪E卡不能为空！',
                    )
                );
                return $json->encode($return);
            }

            $info = $_special_code_mod->get('cate>19 and sn="'.strtoupper($sn).'"');//转变大写
            //$cate_name = $this->_cate[$info['cate']]['name'];


            if(empty($info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '激活码无效,请重新输入！',
                    )
                );
                return $json->encode($return);
            }


            $cate_name = "酷卡";
            if($info['expire_time']<time()){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '酷卡已经过期'.'！',
                    )
                );
                return $json->encode($return);
            }

            if($info['is_used']==1){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => $cate_name.'已被使用'.'！',
                    )
                );
                return $json->encode($return);
            }

            // 创业者转增给用户的酷卡激活    此时 to_id已经保存了对应的消费者user_id
            if(intval($info['to_id']))
            {
                if ($info['to_id'] != $user_id)
                {
                    return $this->eresult('此酷卡已激活 请勿重复操作');
                }
            }

            //http://ls.wap.mfd.cn/kuka-kuka_ls.html?&to_id=1611&user_name=%E7%8F%8D%E7%8F%8D&source=app&sn=YJCT1M90EM0BM
            // 验证 并且 激活 ls 酷卡 库
            $username = $user_info['user_name'];
            $url = 'http://www.ls.wap.local.mfd.cn/kuka-kuka_ls.html?&to_id={$user_id}&user_name={$username}&source=app&sn={$sn}';

            $res = file_get_contents($url);

            $data = array(
                'to_id'=> $user_id,
                'user_name'=>$user_info['user_name'],
                'is_used'=>1,
                'source'=>'mfd|app',
                'to_time'=>time(),

            );
            // return $data;
            $ret = $_special_code_mod->edit('cate>19 and sn="'.$sn.'"',$data);


            if(!$ret){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '系统异常',
                    )
                );
                return $json->encode($return);
                return;
            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '激活成功',
                    'coin'=>$user_info['coin']+$info['work_num'],
                    'get_coin'=> $info['work_num'],
                    'name'=>$cate_name,
                    'description'=>$info['content'],
                )
            );

            return $json->encode($return);

        }

        /**
         *  麦富迪e卡 详细信息
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        function kuka_info($token,$type,$pageSize,$pageIndex){
            global $json;
            if($pageIndex<1)
            {
                $pageIndex = 1;
            }
            $type_limit = array(1,2,3,100);
            $user_info   = getToken($token);
            $now_time    = time();
            $member = m('member');
            $_special_code_mod = & m('special_code');
            $_kukaconfig_mod   = & m('kukaconfig');


            $return = array();
            if(!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }

            if (!in_array($type, $type_limit))
            {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 102,
                        'msg'       => 'type 参数错误',
                    ),
                );
                return $json->encode($return);
            }

            $path = ROOT_PATH."/data/config/special_code.php";
            file_exists($path) && $this->_cate = include $path;

            $user_id = $user_info['user_id'];
            $con = " and to_id = '{$user_id}' ";//默认消费者
            if($user_info['member_lv_id'] >= 2 ) {//创业者
                $con = " and from_id = '{$user_id}'";
            }

            if($user_info['member_lv_id'] >= 2 ) {
                switch ($type){
                    case "1":
                        $con .= " and to_id ='' and expire_time>'{$now_time}' ";
                        break;
                    case "2":
                        $con .= " and to_id !='' and expire_time>'{$now_time}' ";
                        break;
                    case "3":
                        $con .= " and expire_time<'{$now_time}' ";
                        break;
                }
            }

            if($user_info['member_lv_id'] < 2 ) {//消费者
                switch ($type){
                    case "1":
                        $con .= " and is_used= 0 and expire_time>'{$now_time}'";
                        break;
                    case "2":
                        $con .= " and is_used=1 and expire_time>'{$now_time}'";
                        break;
                    case "3":
                        $con .= " and expire_time<'{$now_time}' ";
                        break;
                }
            }

            if($user_info['member_lv_id'] >= 2 ) {
                // 未使用
                $unused = $_special_code_mod->get(array(
                    'conditions'=>"cate>19  AND to_id ='' AND from_id = '{$user_id}' AND expire_time>'{$now_time}' ",
                    'fields'       => 'count(*) as unused_number',
                ));
                // 已使用
                $hasused = $_special_code_mod->get(array(
                    'conditions'=>"cate>19  AND to_id !='' AND from_id = '{$user_id}'  AND expire_time>'{$now_time}' ",
                    'fields'       => 'count(*) as hasused_number',
                ));
                // 已过期

                $hasovered = $_special_code_mod->get(array(
                    'conditions'=>"cate>19  AND expire_time< '{$now_time}' AND from_id = '{$user_id}'",
                    'fields'       => 'count(*) as has_over',
                ));
            }

            if($user_info['member_lv_id'] < 2 ) {//消费者
                // 未激活
                $unused = $_special_code_mod->get(array(
                    'conditions'=>"cate>19  and to_id = '{$user_id}' and is_used= 0  and expire_time>'{$now_time}' ",
                    'fields'       => 'count(*) as unused_number',
                ));
                // 已激活
                $hasused = $_special_code_mod->get(array(
                    'conditions'=>"cate>19  and to_id = '{$user_id}' and  is_used=1  and expire_time>'{$now_time}' ",
                    'fields'       => 'count(*) as hasused_number',
                ));
                // 已过期

                $hasovered = $_special_code_mod->get(array(
                    'conditions'=>"cate>19 and to_id = '{$user_id}'  and expire_time< '{$now_time}' ",
                    'fields'       => 'count(*) as has_over',
                ));

            }


            //return $con;

            //cate >=20  暂时
            $info = $_special_code_mod->findAll(array(
                'index_key' =>'',
                'conditions'=>"cate>19 ".$con,
                'order' => "id DESC",
                'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
            ));


            foreach($info as $k=>$v)
            {
                $info[$k]['donation_mes'] = '';
                $info[$k]['cate_name']    = '';
                $info[$k]['is_expire'] = 0;
                $info[$k]['work_num'] = _format_price_int($v['work_num']);
                $info[$k]['price']    = _format_price_int($v['price']);
                if($v['expire_time'] - time() > 0) {
                    $days = floor(($v['expire_time'] - time())/86400)+1;
                    $info[$k]['day'] = $days;
                }
                //=====  获取品类名称  =====
                if ($v['name'])
                {
                    $info[$k]['cate_name'] = $v['name'];
                }
                else
                {
                    if($info[$k]['cate'] = '22')
                    {
                        $kuka_online = $_kukaconfig_mod->get("id = '{$v['log_id']}'");
                        if ($kuka_online['kuka_name'])
                        {
                            $info[$k]['cate_name'] = $kuka_online['kuka_name'];
                        }
                        else
                        {
                            $info[$k]['cate_name'] = "";
                        }
                    }
                    else
                    {
                        $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
                    }
                }
                if ($v['content'])
                {
                    $info[$k]['description'] = $v['content'];
                }
                else
                {
                    $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
                }

                $to_id = trim($v['to_id']);
                if($v['from_id'] && $to_id ) {//已转赠
                    if($user_info['user_id'] == $v['from_id'] && !empty($to_id) ) {//转赠人回去抵用券信息
                        $memberinfo = $member->get($to_id);
                        $info[$k]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
                    }
                    if($user_info['user_id'] == $v['to_id']  ) {//被转赠人回去抵用券信息
                        $memberinfo = $member->get($v['from_id']);
                        $info[$k]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
                    }
                }

            }

            unset($info[0]['cate']);

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    'unused'   =>!empty($unused['unused_number']) ? $unused['unused_number'] : 0,
                    'hasused'  =>!empty($hasused['hasused_number']) ? $hasused['hasused_number'] : 0,
                    'hasover'  =>!empty($hasovered['has_over']) ? $hasovered['has_over'] : 0,
                    'data'     =>!empty($info) ? $info :'',
                    'kuka_cate' => $kuka_categorey,
                )
            );

            return $json->encode($return);
        }
        
        /**
         *  麦富迪e卡 详细信息
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        function debit_info($token,$type,$pageSize,$pageIndex){
            global $json;
            if($pageIndex<1)
            {
                $pageIndex = 1;
            }
            $type_limit = array(1,2,3,100);
            $user_info = getUserInfo($token);
            
            if(empty($user_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 100,
                        'msg'       => '未登录',
                    ),
                );
                return $json->encode($return);
            }
            
            $cate = $this->_get_options();
            $cate = str_replace("&nbsp;", "", $cate);
            $now_time    = time();
            $member = m('member');
            $member_lv = & m('memberlv');
            $debit_mod = m('debit');
            $this->formatDebit($user_info['user_id']);
        
        
            $return = array();
        
        
            if (!in_array($type, $type_limit))
            {
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 102,
                        'msg'       => 'type 参数错误',
                    ),
                );
                return $json->encode($return);
            }
        
            $con = "user_id = '{$user_info['user_id']}'";
            $uid = $user_info['user_id'];
          
            
            if($user_info['user_id']) {
              
                switch ($type){
                    case "1":
                        $con .= " AND is_used = 0 AND is_invalid = 0  ";
                        break;
                    case "2":
                        $con .= " AND is_used = 1 ";
                        break;
                    case "3":
                        $con .= " AND is_invalid = 1 ";
                        break;
                }
            }
             
            if($user_info['user_id'] ) {
                // 未使用
                $unused = $debit_mod->get(array(
                    'conditions'=>" user_id='{$uid}' AND is_used = 0 AND is_invalid = 0 ",
                    'fields'       => 'count(*) as unused_number',
                ));
                // 已使用
                $hasused = $debit_mod->get(array(
                    'conditions'=>" user_id='{$uid}' AND is_used = 1 ",
                    'fields'       => 'count(*) as hasused_number',
                ));
                // 已过期
            
                $hasovered = $debit_mod->get(array(
                    'conditions'=>" user_id='{$uid}' AND is_invalid = 1",
                    'fields'       => 'count(*) as has_over',
                ));
            }
        
            $list = $debit_mod->find(array(
                'conditions' => $con,
                'count'         => true,
                'order'         => "id DESC",
                'index_key'     =>'',
                'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
                
            ));
            
            if ($list)
            {
                foreach ($list as $key => &$value)
                {
                 
                    $value['add_time'] = date("Y/m/d",$value['add_time']);
                    $value['cate'] = $cate[$value['debit_t_id']];
                    if($value['expire_time'] - time() > 0) {
                        $days = floor(($value['expire_time'] - time())/86400)+1;
                        $list[$key]['day'] = $days;
                    }
                    $value['expire_time'] = date("Y/m/d",$value['expire_time']);
                    if ($value['source'] == 'reg')
                    {
                        $value['source'] = '注册券';
                    }
                    else
                    {
                        $value['source'] = '订单满额券';
                    }
            
                }
            
            }
            
            //=====   获取如何使用的说明  =====
            $article = m('article');
            $a_info = $article->get("code='kuquanshuoming'");
            $content = "";
            if ($a_info)
            {
                $content = $a_info['content'];
            }
        
        
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '成功',
                    'unused'   =>!empty($unused['unused_number']) ? $unused['unused_number'] : 0,
                    'hasused'  =>!empty($hasused['hasused_number']) ? $hasused['hasused_number'] : 0,
                    'hasover'  =>!empty($hasovered['has_over']) ? $hasovered['has_over'] : 0,
                    'data'     =>!empty($list) ? $list :'',
                    'content'  =>$content,
                )
            );
        
            return $json->encode($return);
        }
        
        function formatDebit($user_id)
        {
        
            $time = time();
            $debit_mod = m('debit');
            $debit_list = $debit_mod->find(array(
                'conditions' => "user_id = $user_id AND is_invalid = 0 AND is_used = 0",
            ));
             
            //     print_exit($debit_list);
            if ($debit_list)
            {
                foreach ($debit_list as $key => $value)
                {
                    if ($value['expire_time'] < $time)
                    {
                        $debit_mod->edit($value['id'],array('is_invalid' => 1));
                    }
                }
            }
        }

        /**
         *  我的卡券
         *
         * @param null
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        public function myKucard($token)
        {
            global $json;
            $debit_mod          = m('debit');
            $special_code_mod   = m('special_code');
            $now_time = time();

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
            $con = " and to_id = '{$user_id}'  ";//默认消费者
            if($user_info['member_lv_id'] >= 2 ) {//创业者
                $con = " and from_id = '{$user_id}' and to_id = '' ";
            }

            //创业者 未赠送的酷卡张数
            $special_count = $special_code_mod->findAll(array(
                'conditions' => "cate > 19 and is_used= 0 and expire_time > {$now_time} ".$con,
                'fields'     => "id, expire_time",
            ));
            // 酷卡总数
            $special_all = $special_code_mod->findAll(array(
                'conditions' => "cate > 19  ".$con,
                'fields'     => "id, expire_time,work_num",
            ));
            //酷卡总金额
            $special_all_je = $special_code_mod->get(array(
                'conditions' => "cate > 19 ".$con,
                'fields'     => "id, expire_time, sum(work_num) as je",
            ));

            //即将过期酷卡数
            $soon_special_count = 0;
            if($special_count) {
                foreach($special_count as $val) {
                    if($val['expire_time'] - time() > 0) {
                        $days = floor(($val['expire_time'] - time())/86400)+1;
                        if($days <= 10) {
                            $soon_special_count++;
                        }
                    }
                }
            }

            $de_con = " and user_id = '{$user_id}'  ";//默认消费者
            if($user_info['member_lv_id'] >= 2 ) {//创业者
                $de_con = " and (user_id = '{$user_id}' OR from_uid = '$user_id') ";
            }

            //未使用酷券数(未过期)
            $debit_count = $debit_mod->findall(array(
                'conditions' => " is_invalid = 0 AND is_used = 0  ".$de_con,
                //'fields'     => "count(id) as count, id",
                'fields'     => "id, expire_time,from_uid",
            ));

            //即将过期酷券数
            $sonn_debit_count = 0;
            $debit_counts = 0;
            if($debit_count)
            {
                foreach($debit_count as $dval)
                {
                    if(($dval['expire_time'] - time()) > 0) //=====  没有过期  =====
                    {
                        $days = floor(($dval['expire_time'] - time())/86400);
                        if($days <= 10)
                        {
                            $sonn_debit_count++;
                        }
                        $debit_counts++;
                    }
                }
            }

            $data = array(
                'debit_count'         => $debit_counts,//消费者和创业者未使用未过期酷券数
                'sonn_debit_count'    => $sonn_debit_count,//消费者和创业者未使用即将过期酷券数
                'special_count'       => !empty($special_count) ? count($special_count) : 0,//创业者 未赠送的酷卡张数
                'soon_special_count'  => $soon_special_count,//即将过期酷卡数
                'special_all'         => !empty($special_all) ? count($special_all) : 0,//消费者酷卡总数
                'special_je'          => !empty($special_all_je['je']) ? $special_all_je['je'] : 0,//消费者的酷卡总金额
            );


            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  => $data,
                )
            );

            return $json->encode($return);

        }

        /**
         * 酷卡转赠
         *
         * @param string $token 用户token; int $spcode 要转赠的酷卡的唯一sn; int $to_userid 要转赠给对应用户的id;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function kukaDonation($token, $spcode, $to_userid)
        {
            global $json;
            $member_mod = m('member');
            $_special_code_mod = & m('special_code');
            $now_time = time();

            $user_info = getUserInfo($token);
            $touser_info = $member_mod->get($to_userid);

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

            if (!$touser_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未发现您要转赠的顾客！'
                    ),
                );
                return $json->encode($return);
            }

            //获得当前抵用券信息
            $special_code_info = $_special_code_mod->get(array(
                'conditions' => "cate > 19 and from_id = '{$user_id}' and is_used = 0 and sn = '{$spcode}' and to_id = '' and expire_time>{$now_time}",
            ));

            if (!$special_code_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '未发现此酷卡！'
                    ),
                );
                return $json->encode($return);
            }

            $update_arr = array();
            $update_arr['to_id']     = $to_userid;//使用人id
            $update_arr['user_name'] = $touser_info['user_name'];//使用码的用户

            //执行转赠操作
            $_special_code_edit = $_special_code_mod->edit($special_code_info['id'], $update_arr);
            if (!$_special_code_edit) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '酷卡转赠失败！'
                    ),
                );
                return $json->encode($return);
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  => '成功转赠给您顾客！',
                )
            );

            return $json->encode($return);
        }

        /**
         * 酷卡购买列表
         *
         * @param string $token 用户token; int $spcode 要转赠的酷卡的唯一sn; int $to_userid 要转赠给对应用户的id;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function kukalist($token)
        {
            global $json;
            $member_mod        = m('member');
            $_special_code_mod = & m('special_code');

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

            /* $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path; */

            //cate == 21  暂时
            $_speciallist = $_special_code_mod->findAll(array(
                'index_key'  => '',
                'conditions' => "cate = 21 and is_used = 0 and to_id = '' and from_id='' and expire_time > ".time()." group by work_num",
                'fields'     => 'id, sn, cate, work_num, price, user_name, expire_time, log_id ',
                'order'      => "id desc",
            ));

            foreach($_speciallist as $k=>$v){
                $_speciallist[$k]['cate_name']   = $this->_cate[$v['cate']]['name'];
                $_speciallist[$k]['description'] = $this->_cate[$v['cate']]['description'];
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data' => $_speciallist,
                    'msg'  => '数据获取成功！',
                )
            );

            return $json->encode($return);

        }

        function buy_kuka($token)
        {

            global $json;
            $member_mod        = m('member');
            $_kukaconfig_mod = & m('kukaconfig');

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

            $_speciallist = $_kukaconfig_mod->findAll(array(
                'index_key'  => '',
                'conditions' => "is_show = 1 and expire_time > ".time()." ",
                'fields'     => 'id, kuka_name, kuka_num, sale_price, expire_time, add_time ,is_show',
                'order'      => "id desc",
            ));
            foreach ($_speciallist as $k=>$v){
                $_speciallist[$k]['kuka_num'] = _format_price_int($v['kuka_num']);
                $_speciallist[$k]['sale_price'] = _format_price_int($v['sale_price']);
            }

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data' => $_speciallist,
                    'msg'  => '数据获取成功！',
                )
            );

            return $json->encode($return);

        }

        /**
         * 酷币购买列表
         *
         * @param string $token 用户token; int $spcode 要转赠的酷卡的唯一sn; int $to_userid 要转赠给对应用户的id;
         *
         * @access protected
         * @author xuganglong <781110641@qq.com>
         * @return void
         */
        function kubilist($token)
        {
            global $json;
            $coin_logmod = m('coinconfig');
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

            $_coinconfiglist = $coin_logmod->findAll(array(
                'conditions' => "is_show = 1 ",
                'order'      => "id desc",
                'index_key'  => '',
            ));

            //组装返回数据
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data' => $_coinconfiglist,
                    'msg'  => '数据获取成功！',
                )
            );

            return $json->encode($return);

        }

        /**
         * 面料信息接口
         *
         * @param string $code  面料编号;
         *
         * @access protected
         * @author tangsj <963830611@qq.com>
         * @return void
         */

        function getpartinfo($code,$cllk)
        {

            global $json;
            $part_mod    = m('part');
            $fabric_mod  = m('fabric');
            $dict_mod    = m('dict');
            $data        = array();
            $part        = $part_mod->get(array(
                'conditions'=>"code ='$code'",
                'fields'    =>"part_img,img_url,img_url2,img_url3,img_url4,content",
            ));


            $part['img_url'] =  isset($part['img_url']) ? SITE_URL.'/upload/jipaijizuo/'.$part['img_url'] : '';

            if($part['img_url2']){

                $part['img_url2'] = SITE_URL.'/upload/jipaijizuo/'.$part['img_url2'];
            }
            if($part['img_url3']){

                $part['img_url3'] = SITE_URL.'/upload/jipaijizuo/'.$part['img_url3'];
            }
            if($part['img_url4']){

                $part['img_url4'] = SITE_URL.'/upload/jipaijizuo/'.$part['img_url4'];
            }
            $data['img'][0] =  isset($part['part_img']) ? $part['part_img']: '';
            $data['img'][1]  = isset($part['img_url']) ? $part['img_url']: '';
            $data['img'][2]  = isset($part['img_url2']) ? $part['img_url2']: '';
            $data['img'][3]  = isset($part['img_url3']) ? $part['img_url3']: '';
            $data['img'][4]  = isset($part['img_url4']) ? $part['img_url4']: '';
            $data['content'] =  isset($part['content']) ? $part['content']: '';


            $fabric = $fabric_mod->get(array(
                'conditions'=>"CODE ='$code'",
                'fields'    =>"chengfen,COLORID,FLOWERID,SHAZHI,VCOMPOSITIONID",

            ));
            $part['chengfen'] = $fabric['chengfen'];
            $part['shazhi'] = $fabric['SHAZHI'];
            $idarr = "'{$fabric['COLORID']}', '{$fabric['FLOWERID']}', '{$fabric['VCOMPOSITIONID']}' ";
            $dict = $dict_mod->find(array(
                'conditions'=>" id in({$idarr})",
                'fields'    =>"name,code,sequenceno",
                'order'     =>"ecode ASC"
            ));

            $data['attr'][0] ='编号:'. $code;
            $data['attr'][1] ='颜色:'. $dict[$fabric['COLORID']]['name'];
            $data['attr'][2] ='花型:'. $dict[$fabric['FLOWERID']]['name'];
            $data['attr'][3] ='砂支:'. $fabric['SHAZHI'];
            $data['attr'][4] ='成分:'. $fabric['chengfen'];
            $data['vcomposition'] = $dict[$fabric['VCOMPOSITIONID']]['name'];




            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data' => $data,
                    'a'    =>$a,
                    'msg'  => '成功获取面料信息！',
                )
            );
            $jsdata = $json->encode($return);
            return $cllk."($jsdata)";

        }




        /*返修*/
        function fx($token, $order_id)
        {
            global $json;
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

            $fx_reason = array(
                0=>array('id'=>0,'reason'=>'尺寸原因'),
                1=>array('id'=>1,'reason'=>'工艺问题'),
                2=>array('id'=>2,'reason'=>'特殊部位'),
            );



            $_order_serve_mod = & m('orderserve');
            $fx_info = $_order_serve_mod->get(array(
                'conditions'=>"type=1 and order_id={$order_id}  ",
                'fields'=>'store_name,free,price,gy_method,goods_name,address,sign,liangti_id,liangti_name,user_id,serve_id'
            ));



            if(empty($fx_info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '当前订单不支持返修，请联系客服再进行操作！'
                    ),
                );
                return $json->encode($return);
            }



            if($fx_info['user_id'] !=$user_info['user_id']){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '您不能对此订单进行此操作！'
                    ),
                );
                return $json->encode($return);
            }



            //门店量体
            $member_mod =&m('member');
            $_serve_mod =&m('serve');
            $figure_liangti_mod =&m('figure_liangti');

            $address = $_serve_mod->get("idserve='{$fx_info['serve_id']}'");
            if($address){
                $fx_info['store_name'] =$address['serve_name'];
                if($fx_info['sign']==2){
                    $fx_info['address'] =$address['serve_address']."({$address['store_mobile']})"; //$address['region_name'].
                    //        $address= $fx_info['address'];
                    //        $address_arr =explode(')',$address);
                    //        $fx_info['address'] =$address_arr[0].')';
                    //        $fx_info['liangti_name'] = str_replace("   ",'',$address_arr[1]);

                }
            }

            if($fx_info['liangti_id'] ){
                $liangti_info =$member_mod->get("serve_type=2 and user_id={$fx_info['liangti_id']}");
                if($liangti_info){
                    if($liangti_info['figure_type']==2){
                        //lts
                        $figure_liangti = $figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                        $liangti_cart = $figure_liangti['card_number'];
                        $liangti_mob =$liangti_info['phone_mob'];
                        $liangti_name =$liangti_info['real_name'];
                    }else{
                        //店长

                        $liangti_cart =$address['card_number'];
                        $liangti_mob =$address['mobile'];
                        $liangti_name = $address['linkman'];
                    }

                    if($liangti_name){
                        $fx_info['liangti_name']="量体师{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";
                    }else{
                        $fx_info['liangti_name']='';
                    }

                }
            }else{
                $fx_info['liangti_name']='店长指定量体师';
            }


            $fx_info['fx_reason'] =$fx_reason;
            //        $sign_info =array(1=>'线上客服',2=>'线下门店');//sign 业务承接方式

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  => $fx_info,
                )
            );

            return $json->encode($return);
        }





        function fx_add($token,$order_id,$wl_sn,$reason,$to_time){

            global $json;

            $user_info = getUserInfo($token);

            if (!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在！'
                    ),
                );
                return $json->encode($return);
            }


            $_order_serve_mod = & m('orderserve');
            $fx_info = $_order_serve_mod->get(array(
                'conditions'=>"type=1 and order_id={$order_id} ",
                'fields'=>'address,rec_id,sign,user_id,free,status'
            ));



            if(empty($fx_info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '当前订单不支持返修，请联系客服再进行操作！'
                    ),
                );
                return $json->encode($return);
            }

            if($fx_info['user_id'] !=$user_info['user_id']){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg'       => '您不能对此订单进行此操作！'
                    ),
                );
                return $json->encode($return);
            }



            if(($fx_info['free']==1&&$fx_info['status']>=4)||($fx_info['free']==0&&$fx_info['status']>=7)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 130,
                        'msg'       => '已经提交过一次申请！'
                    ),
                );
                return $json->encode($return);
            }


            // 线上物流号不能为空
            if($fx_info['sign'] == 1 && !$wl_sn){
                //如果是
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '请输入物流号！'
                    ),
                );
                return $json->encode($return);
            }


            $status =$fx_info['free']==="1"?1:5;
            $_order_serve_mod->edit("order_id='{$order_id}'",array('wl_sn'=>$wl_sn,'reason'=>$reason,'to_time'=>$to_time,'status'=>$status));
            $_order_serve_info_mod = & m('orderserveinfo');
            $_order_serve_info_mod->edit('order_id="'.$order_id.'"',array('info_status'=>$status));


            if($fx_info['sign'] == 1){
                $msg = '返修组收到货后会第一时间处理返修业务，请耐心等待！';
            }
            if($fx_info['sign'] == 2){
                $time = date('Y-m-d',$to_time);
                $msg = "提交成功，请您带上商品于'{$time}'日前往预约门店进行返修！";


                //短信通知
                if($fx_info['liangti_id']){
                    $_order_mod =&m('order');
                    $order_info = $_order_mod->get("order_id='{$order_id}'");
                    $phone_mob = empty($order_info['ship_mobile'])  ? $order_info['ship_tel'] : $order_info['ship_mobile'];

                    $store_msg ="门店店长，内容：“客户{$fx_info['user_name']}预约{$time}到店返修商品（订单号{$fx_info['order_sn']}），承接量体师{$fx_info['liangti_name']},客户电话{$phone_mob}，请做好接待工作。";
                    $liangti_msg = "您的客户{$fx_info['user_name']}预约{$time}到店返修商品（订单号{$fx_info['orde_sn']}）,客户电话{$phone_mob}，请做好接待工作。";

                    $m_mod =&m('member');
                    $figure_liangti_mod =&m('figure_liangti');
                    $liangti_info =$figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                    $store_id =$liangti_info['manager_id'];
                    $liangti_info = $m_mod->get("user_id='{$fx_info['liangti_id']}' and serve_type=2");
                    $store_info = $m_mod->get("user_id='{$store_id}' and serve_type=2");
                    SendSms($store_info['phone_mob'],$store_msg);
                    SendSms($liangti_info['phone_mob'],$liangti_msg);
                }
            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  => $msg,//线上
                )
            );
            return $json->encode($return);
        }



        function fx_info($token,$order_id){
            global $json;
            $user_info = getUserInfo($token);

            if (!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在！'
                    ),
                );
                return $json->encode($return);
            }


            $fx_reason = array(
                0=>'尺寸原因',
                1=>'工艺问题',
                2=>'特殊部位',
            );

            $_pl_for_str = array(
                '0003'  => 'MXF',//'西服', '上衣'
                '0004' => 'MXK',//'西裤',
                '0005' => 'MMJ',//'马夹',
                '0006'  =>'MCY',// '衬衣',
                '0007' => 'MDY',// '大衣',

                '0018' =>'MXF',//'立领西服',  用上衣（西服）

                '0010'=>'MLF',//'礼服',
                //                '0008'=> '配件',
                '0017'  => 'MDK',//'男短裤',
                '0011'  => 'WXF',//'女西服',
                '0012' => 'WXK',//'女西裤',
                '0016' =>'WCY',// '女衬衣',
                '0021' =>  'WDY',//'女大衣',


                '0022'=>'MDY',//'女风衣短款',   //从这开始直接用大衣编码
                '0023'=>'MDY',//'女风衣长款',
                '0024'=>'MDY',//'男风衣短款',
                '0025'=>'MDY',//'男风衣长款',
            );

            $color_status = array(
                '101'=>'101#-乳白色',
                '102'=>'102#--白色',
                '3750'=>'3750#--银灰色',
                '3747'=>'3747#-浅灰色',
                '3687'=>'3687#--中灰色',
                '432'=>'432#--浅蓝色',
                '599'=>'599#--蓝色',
                '1059'=>'1059#-绿松石',
                '455'=>'455#--深蓝色',
                '3712'=>'3712#-藏蓝色',
                '352'=>'352#--淡黄色',
                '1017'=>'1017#--金色',
                '3172'=>'3172#--黄色',
                '3077'=>'3077#-中黄色',
                '321'=>'321#-橙色',
                '3701'=>'3701#-浅粉色',
                '624'=>'624#-粉色',
                '1034'=>'1034#-深粉色',
                '714'=>'714#-鲜红色',
                '138'=>'138#-红色',
                '177'=>'177#--浅紫色',
                '633'=>'633#-蓝紫',
                '189'=>'189#-紫罗兰',
                '3601'=>'3601#--紫色',
                '3742'=>'3742#-深紫',
                '1093'=>'1093#-青绿色',
                '3334'=>'3334#-黄绿色',
                '262'=>'262#-浅橄榄色',
                '3813'=>'3818#--中绿色',
                '3813'=>'3813#-中绿',
                '598'=>'598#-土褐色',
                '392'=>'392#-卡其色',
                '1142'=>'3099#--浅棕色',
                '1161'=>'1161#--中棕色',
                '1173'=>'1173#-深橄榄色',
                '572'=>'572#--深绿色',
                '144'=>'144#-深红色',
                '145'=>'145#--酒红色',
                '1196'=>'1196#-枣红色',
                '489'=>'489#--深灰色',
                '3655'=>'3655#--海军蓝',
                '3720'=>'3720#-深蓝灰',
                '103'=>'103#--黑色',
                '1041#--鲜肉色'=>'1041#--鲜肉色',
                '1308#--深棕色'=>'1308#--深棕色',
                '1023#--粉色'=>'1023#--粉色',
                '312'=>'312#-蛋黄',
                '1203'=>'1203#-宝石蓝',
                '3694'=>'3694#-墨绿',
                '3618'=>'3618#-灰色',
                'MG2'=>'MG2#-金色',
                'MS1'=>'MS1#-银色',
                '144#--深红色'=>'144#--深红色',
                '1179#--白色'=>'1179#--白色',
                '1180#--黑色'=>'1180#--黑色',
                '1058#--中蓝色'=>'1058#--中蓝色',
                '8032#--杏色'=>'8032#--杏色',
                '1043#--桔黄色'=>'1043#--桔黄色',
                '1285#--浅绿色'=>'1285#--浅绿色',
                '8103#--天蓝色'=>'8103#--天蓝色',
                '1162'=>'1162#-深棕色',
                '3099'=>'3099#--浅棕色',
                '棕褐色'=>'101#棕褐色', //java停用了？
            );
            $_pl_status = array(
                '0003'  => '西服', //MXF
                '0004' => '西裤', //MXK
                '0005' => '马夹',//MMJ
                '0006'  => '衬衣',//MCY
                '0007' =>  '大衣',//MDY
                '0018' =>'立领西服',
                '0010'=>'礼服', //MLF
                //                '0008'=> '配件',
                '0017'  => '男短裤', //MDK
                '0011'  => '女西服', //WXF
                '0012' => '女西裤', //WXK
                '0016' => '女衬衣', //WCY
                '0021' =>  '女大衣',//WDY
                '0022'=>'女风衣短款',
                '0023'=>'女风衣长款',
                '0024'=>'男风衣短款',
                '0025'=>'男风衣长款',
            );



            //返修方案
            $_order_fx_mod =& m('orderfx');
            $_cart_mod=& m('cart');//刺绣
            $_special_question_mod = &m('specialquestion');
            $_order_serve_info_mod = & m('orderserveinfo');


            $fx_info = $_order_serve_info_mod->get(array(
                'join'          => 'has_order_serve',//,has_special
                'conditions'=>"order_serve.type=1 and order_serve_info.order_id={$order_id} and order_serve.user_id={$user_info['user_id']}",
                'fields'=>"*,order_serve_info.rec_id as rec_id,order_serve.rec_id as rec_ids,order_serve.serve_id"
            ));


            if(!$fx_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '当前订单没有申请过返修操作！'
                    ),
                );
                return $json->encode($return);
            }


            if($fx_info['status']>=1){

                    $fx_status_1 = array(1=>'返修中',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成');  //线上

                    if($fx_info['free']==1){
                        $fx_status_2=  array(1=>'等待到店',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
                    }else{
                        $fx_status_2=  array(5=>"待付款",6=>"已取消",7=>'等待到店',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
                    }

                $fx_info['status_name'] = $fx_info['sign']==1?$fx_status_1[$fx_info['status']]:$fx_status_2[$fx_info['status']];
            }





            if($fx_info['status']==1){
                if($fx_info['sign']==2){
                    $fx_method ='请携带商品及时到店接受返修服务。';
                }
                if($fx_info['sign']==1){
                    $fx_method ='业务受理中…';
                }
            }


            $fx_info['reason'] =$fx_reason[$fx_info['reason']];




            //门店量体
            $member_mod =&m('member');
            $_serve_mod =&m('serve');
            $figure_liangti_mod =&m('figure_liangti');
            $address = $_serve_mod->get("idserve='{$fx_info['serve_id']}'");
            if($address){
                $fx_info['store_name'] =$address['serve_name'];
                if($fx_info['sign']==2){
                    $fx_info['address'] =$address['serve_address']."({$address['store_mobile']})"; //$address['region_name'].
                    //        $address= $fx_info['address'];
                    //        $address_arr =explode(')',$address);
                    //        $fx_info['address'] =$address_arr[0].')';
                    //        $fx_info['liangti_name'] = str_replace("   ",'',$address_arr[1]);
                }
            }

            if($fx_info['liangti_id'] ){
                $liangti_info =$member_mod->get("serve_type=2 and user_id={$fx_info['liangti_id']}");

                if($liangti_info){

                    if($liangti_info['figure_type']==2){
                        //lts
                        $figure_liangti = $figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                        $liangti_cart = $figure_liangti['card_number'];
                        $liangti_mob =$liangti_info['phone_mob'];
                        $liangti_name = $liangti_info['real_name'];
                    }else{
                        //店长
                        $liangti_cart =$address['card_number'];
                        $liangti_mob =$address['mobile'];
                        $liangti_name = $address['linkman'];
                    }

                    if($liangti_name){
                        $fx_info['liangti_name']="量体师{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";
                    }else{
                        $fx_info['liangti_name']='';
                    }

                }
            }else{
                $fx_info['liangti_name']='店长指定量体师';
            }



            if($fx_info['free']==1){
            //返修方案
                $has_fx = $_order_fx_mod->find(array(
                    'conditions'=>"order_id='{$fx_info['order_id']}'",
                    'index_key'=>''
                ));
                $has_fx_info =!empty($has_fx)?$has_fx:'';

                // 刺绣信息
                $cx = $_cart_mod->_format_embs();
                $user_fx_info = array();
                //            return $json->encode($has_fx_info);
                if($has_fx_info){
                    foreach($has_fx_info as $ok=>$ov){

                        $ov['content'] =json_decode($ov['content'],1);
                        $cx_info =$cx[$ov['content']['cloth']]?array_values($cx[$ov['content']['cloth']]):array(); //女装不支持cx
                        foreach($ov['content'] as $k=>$v){
                            $user_fx_info[$ok]['cloth'] = $_pl_status[$ov['content']['cloth']];
                            if($k=="cx"){
                                foreach($v as $kk=>$vv){
                                    foreach($cx_info as $kkk=>$vvv){

                                        if($vvv['name']==$kk){
                                            if($kk=="颜色"){
                                                $color_status[$vvv['list'][$vv]['name']]&&$user_fx_info[$ok][$k][$kk] =$color_status[$vvv['list'][$vv]['name']];
                                            }else{
                                                $vvv['list'][$vv]['name']&&$user_fx_info[$ok][$k][$kk] = $vvv['list'][$vv]['name'];
                                            }
                                        }elseif($vvv['name']=='内容'){
                                            $content = explode(":",$v['content']);
                                            $content[1]&&$user_fx_info[$ok][$k]['内容'] = $content[1];
                                        }
                                    }
                                }
                            }
                            if($k=="special"){
                                $special =array();
                                $i = 0;
                                foreach($v as $kk=>$vv){
                                    $i++;
                                    //特殊处理
                                    $special_info = $_special_question_mod->get(array(
                                        'join'          => 'has_solution',//,has_special
                                        'conditions'=>"special_solution.solution_id='{$kk}' and special_solution.ecode='{$vv}' and special_question.status=1 and special_solution.status=1",
                                        'fields'=>'special_solution.solution_name,special_solution.solution',
                                    ));
                                    $special["{$i}、".$special_info['solution_name']] =$special_info['solution'];
                                }
                                $user_fx_info[$ok]['special'] = $special;
                            }
                            if($k == 'lt'){
                                $user_fx_info[$ok]['lt'] = array_values($v);

                            }
                        }


                    }
                }
            }

            $payment_arr=array(
                1=>"支付宝",
                2=>"移动支付宝",
                3=>"银联支付",
            );

            if($fx_info['payment_id']){
                $fx_info['payment_name'] = $payment_arr[$fx_info['payment_id']];
            }

            //=======返修   end==========

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'=>$fx_info,
                    'fx_method'=>$user_fx_info?$user_fx_info:$fx_method,
                )
            );
            return $json->encode($return);


        }


        //返修列表
        function fx_status_list($token){

            global $json;
            $user_info = getUserInfo($token);


            if (!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在！'
                    ),
                );
                return $json->encode($return);
            }





            $order_status =  array(
                '0'  => "已取消",
                '11' => "待付款",
                '12' => "待量体",
                '20' => "已支付",
                '60' => "生产中",
                '30' => "已发货",
                '40' => "已完成",
                '41' => "返修中",
                '43' => "订单异常",
                '61' => "备货中",
                '70' => "退款中",
                '80' => "已退款",
            );

            $measure = array(
                '2'  => "到店量体",
                '6'  => "指派量体师",
                '5'  => "现有量体"
            );
            /*为了方便临时用*/
            $_pl_status = array(
                '0003'  => '西服', //MXF
                '0004' => '西裤', //MXK
                '0005' => '马夹',//MMJ
                '0006'  => '衬衣',//MCY
                '0007' =>  '大衣',//MDY
                '0018' =>'立领西服',
                '0010'=>'礼服', //MLF
                //                '0008'=> '配件',
                '0017'  => '男短裤', //MDK
                '0011'  => '女西服', //WXF
                '0012' => '女西裤', //WXK
                '0016' => '女衬衣', //WCY
                '0021' =>  '女大衣',//WDY
                '0022'=>'女风衣短款',
                '0023'=>'女风衣长款',
                '0024'=>'男风衣短款',
                '0025'=>'男风衣长款',
            );


            //    return $json->encode($order_info);
            $order_goods_mod =&m('ordergoods');
            $fabirc_mod = m('fabric');
            $order_figure_mod = m('orderfigure');
            $_order_serve_info_mod = & m('orderserveinfo');
            $suit_mod = m('ordersuit');




            $fx_info = $_order_serve_info_mod->find(array(
                'join'          => 'has_order_serve',//,has_special
                'conditions'=>"order_serve.status>0 and order_serve.type=1  and order_serve.user_id='{$user_info['user_id']}'",
                'fields'=>"*,order_serve_info.rec_id as rec_id",
                'order' => "order_serve.add_time desc",
            ));



            $fx_status_1 = array(1=>'返修中',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成');  //线上




            if(empty($fx_info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 103,
                        'msg'       => '不存在返修订单！'
                    ),
                );
                return $json->encode($return);
            }

            $order_goods_info =$fx_order_ids =$fx_order =array();

            foreach($fx_info as $k=>$v){
                $fx_order_ids[] =$v['rec_id'];
                $fx_order[$v['order_id']]['order_sn'] =$v['order_sn'];
                $fx_order[$v['order_id']]['add_time'] =date("Y-m-d H:i:s",$v['add_time']);
                $fx_order[$v['order_id']]['order_id'] =$v['order_id'];
                $fx_order[$v['order_id']]['user_name'] =$v['user_name'];
                $fx_order[$v['order_id']]['sign'] =$v['sign'];
                $fx_order[$v['order_id']]['price'] =$v['price'];
                $fx_order[$v['order_id']]['status'] =$v['status'];
                $fx_order[$v['order_id']]['waybillno'] =$v['waybillno'];



                if($v['free']==1){
                    $fx_status_2=  array(1=>'等待到店',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
                }else{
                    $fx_status_2=  array(5=>"待付款",6=>"已取消",7=>'等待到店',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
                }

                $fx_order[$v['order_id']]['status_name'] =$v['sign']==1?$fx_status_1[$v['status']]:$fx_status_2[$v['status']];
                //加入口
                $fx_order[$v['order_id']]['if_receive'] =$v['status']==11?1:0;



            }



            $order_info =$order_goods_mod->find(array(
                "join"=>"belongs_to_order",
                "conditions"=>db_create_in($fx_order_ids,"order_goods.rec_id"),
                "fields"=>'*,order_goods.fabric as fabric'
            ));


            foreach($order_info as $k=>$v){


                $fx_order[$v['order_id']]['order_status'] =$v['status'];
                $fx_order[$v['order_id']]['order_status_name'] =$order_status[$v['status']];

                if($v['size']!='diy'){
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['size'] ="尺码：".$v['size'];
                }else{
                    //兼容老数据
                    $size ="量体定制";

                    $figure_info = $order_figure_mod->get("son_sn='{$v['son_sn']}'");
                    if($measure[$figure_info['measure']]&&$fx_order[$v['order_id']]['user_name']){
                        $size .="({$measure[$figure_info['measure']]}/{$fx_order[$v['order_id']]['user_name']})";
                    }
                    if(!$measure[$figure_info['measure']] &&$fx_order[$v['order_id']]['user_name']){
                        $size .="({$fx_order[$v['order_id']]['user_name']})";
                    }
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['size']=$size;
                }



                if($v['type']=="suit"){
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['fabric'] ='';
                    $suit_info  = $suit_mod->get("order_id='{$v['order_id']}'");
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['img'] =$suit_info['goods_image'];
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['goods_name'] =$suit_info['goods_name'];

                }elseif($v['type']=='diy'){

                    $fabric_info = $fabirc_mod->get("CODE='{$v['fabric']}'");
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['fabric'] =$fabric_info['tname'];

                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['goods_name'] =$v['goods_name'];//."(面料：{$fabric_info['tname']})";
                    $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['img'] =$v['goods_image'];

                }
                $fx_order[$v['order_id']]['fx_info'] =array_values($fx_order[$v['order_id']]['fx_info']);
                $fx_order[$v['order_id']]["order_amount"] =$v['order_amount'];
                $fx_order[$v['order_id']]["price"] =$fx_order[$v['order_id']]['price']?$fx_order[$v['order_id']]['price']:0;

            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  =>array_values($fx_order),
                )
            );

            return $json->encode($return);
        }


        /*返修 确认收货*/
        function fx_receive($token,$order_id){
            global $json;
            $user_info = getUserInfo($token);


            if (!$user_info) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 100,
                        'msg'       => '账号不存在！'
                    ),
                );
                return $json->encode($return);
            }

            $_order_serve_mod = & m('orderserve');
            $_order_serve_info_mod = & m('orderserveinfo');



            $fx_info =$_order_serve_mod->get("user_id='{$user_info['user_id']}' and type=1 and order_id='{$order_id}' ");
            if(!$fx_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '当前用户不能对此订单做返修完成操作！'
                    ),
                );
                return $json->encode($return);
            }

            if($fx_info['status'] <7){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '还未发货，不能操作返修订单已完成！'
                    ),
                );
                return $json->encode($return);
            }
            $ret = $_order_serve_info_mod->edit("order_id='{$order_id}' and type=1",array("info_status"=>12));
            $ret && $ret = $_order_serve_mod->edit("order_id='{$order_id}' and type=1",array('status'=>12));

            if (!$ret) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '系统异常！'
                    ),
                );
                return $json->encode($return);
            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  =>'确认收货成功！',
                )
            );
            return $json->encode($return);

        }


        function order_serve_status($token,$order_id){
            global $json;
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


            $_order_serve_mod = & m('orderserve');
            //后台允许返修  切用户没有提交
            $fx_info = $_order_serve_mod->get(array(
                'conditions'=>"type=1  and order_id={$order_id} and user_id='{$user_info['user_id']}'", // yusw     !!!!user_id='{$user_info['user_id']}'
            ));


            if(!$fx_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg'       => '当前订单不支持返修'
                    ),
                );
                return $json->encode($return);
            }

            if($fx_info['status'] !=0){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 107,
                        'msg'       => '用户已经提交过一体返修'
                    ),
                );
                return $json->encode($return);
            }
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'msg'  =>'返修',
                )
            );

            return $json->encode($return);

        }

        /*全球首发面料  筛选*/
        function qqfabric(){
            global $json;
            $fbcategory_mode =&m('fbcategory');
            $fabricattribute_mod = &m('fabricattribute');
            $pp_cd_info = $fbcategory_mode->find();

            if(empty($pp_cd_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '暂无数据！'
                    ),
                );
                return $json->encode($return);
            }

            $list = $pp = $gj =array();
            foreach($pp_cd_info as $k=>$v){
                if($v['parent_id'] == 0){
                    $gj[$v['cate_id']]['cate_name']=$v['cate_name'];
                    $gj[$v['cate_id']]['region_id']=$v['cate_id'];

                }else{
                    $pp[$v['cate_id']]['cate_name'] =$v['cate_name'];
                    $pp[$v['cate_id']]['brand_id'] =$v['cate_id'];
                }
            }


            //pl
            $fabricattribute_info = $fabricattribute_mod->get("attr_id=4");
            $pl = explode(",",$fabricattribute_info['attr_values']);


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'country'  =>array_values($gj),
                    'brand'  =>array_values($pp),
                    'pl'  =>$pl,
                )
            );

            return $json->encode($return);
        }
        /*全球首发联动*/
        function qqfabricSelect($region_id){
            global $json;
            $fbcategory_mode =&m('fbcategory');
            $pp_info = $fbcategory_mode->find("parent_id='{$region_id}'");

            if(empty($pp_info)) {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '暂无数据！'
                    ),
                );
                return $json->encode($return);
            }

            $brand =array();
            foreach($pp_info as $k=>$v){
                $brand[$k]['brand_id'] = $v['cate_id'];
                $brand[$k]['cate_name'] = $v['cate_name'];
            }

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  =>array_values($brand),
                )
            );

            return $json->encode($return);
        }

        /*面料  全球首发列表*/
        function qqfabriclist($data){
            global $json;


            $fbcategory_mode =&m('fbcategory');
            $fabricattribute_mod = &m('fabricattribute');
            $fabricrelattr_mod =&m('fabricrelattr'); //attr_id是干什么的  对应4
            $fabricprice_mod =&m('fabricpricecf');
            $fabricinfo_mod =&m('fabricinfo');

            $pl_arr =array(
                "西服"=>1,
                "西裤"=>2,
                "衬衣"=>3,
                "大衣"=>4,
                "马甲"=>5,
                "套装"=>10,
            );

            //=====  liang.li.04.21  标示此字段是从快捷下单入口进来的 还是从麦富迪尚品进来的 0是从快捷下单 1是全球首发  =====
            $is_woman = (isset($data->is_woman) ) ? $data->is_woman : 0;
            $region_id = (isset($data->region_id)&&$data->region_id!='') ? $data->region_id : '';
            $brand_id = (isset($data->brand_id)&&$data->brand_id!='') ? $data->brand_id : '';
            $pl = (isset($data->pl) && $data->pl!='')? $data->pl : ''; //汉字

            $pageSize = (isset($data->pageSize)&&$data->pageSize!='') ? $data->pageSize:10;
            $pageIndex = (isset($data->pageIndex) &&$data->pageIndex!='')? $data->pageIndex : 1;
            $limit = $pageSize*($pageIndex-1).','.$pageSize;
            
            $conditions = "fai.is_sale=1  and fa.attr_id=4 ";  //根据品类筛选
            if ($is_woman) //=====  女装  =====
            {
                $conditions .= "and fai.is_frock_limit = 1 ";
            }
            else //=====  男装  =====
            {
                $conditions .= "and fai.is_global = 1 ";
            }
            
            $price_conditions =" 1=1 ";
            if($region_id !=''){
                $conditions .="and fai.region_id='{$region_id}' ";
                if($brand_id !=''){
                    $conditions .=" and fai.brand_id='{$brand_id}' ";
                }
            }


            if($pl !=''){
                $conditions .="and fa.attr_value='{$pl}'";
                $pl_str = $pl_arr[$pl];
                if($pl_str){
                    $price_conditions .="and category='{$pl_str}'";
                }
            }

            $fabricinfo = $fabricrelattr_mod->find(array(
                'join'=>"has_fabric_info",
                'conditions' => $conditions,
                'limit' => $limit,
                'fields'=>"fa.attr_value,fai.fabric_name,fai.is_global,fai.fabric_sn,fai.fabric_img,fai.fabric_id",
                'order'   => "fai.sort_order asc,fa.id desc ",
            ));


            if(empty($fabricinfo)){
                if($pageIndex==1){
                    $msg= "暂无数据！";
                }else{
                    $msg='亲，已加载完了！';
                }
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => $msg
                    ),
                );
                return $json->encode($return);
            }


            $fabric_ids = array();

            $fabric_ids=i_array_column($fabricinfo,"fabric_id");
            $price_conditions .=" and ".db_create_in($fabric_ids,"fabric_id");

            $fabricprice_info =$fabricprice_mod->find(array(
                "conditions"=>$price_conditions,
                //            "index_key"=>"fabric_id",
            ));




            foreach($fabricinfo as $k=>$v){
                foreach($fabricprice_info as $kk=>$vv){
                    if($vv["fabric_id"] ==$v['fabric_id'] && $pl_arr[$v['attr_value']]==$vv['category']){
                        $fabricinfo[$k]['f_price'] =$vv['price'];
                    }
                }

                !$fabricinfo[$k]['f_price']&&$fabricinfo[$k]['f_price']=0;
                $fabricinfo[$k]['fabric_name'].="-".$fabricinfo[$k]['attr_value'];

            }


            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  =>array_values($fabricinfo),
                )
            );

            return $json->encode($return);
        }

        /*全球首发 关联商品列表*/
        function qqsuitList($data){
            global $json;
            //'DSA794A';
            $is_woman = (isset($data->is_woman) ) ? $data->is_woman : 0;
            $pl =(isset($data->attr_value)&&$data->attr_value !='')     ? trim($data->attr_value) : ''; //汉字
            $fabric_id =(isset($data->fabric_id)&&$data->fabric_id !='')     ? trim($data->fabric_id) : '';

            $pageSize = (isset($data->pageSize)&&$data->pageSize!='') ? $data->pageSize:10;
            $pageIndex = (isset($data->pageIndex) &&$data->pageIndex!='')? $data->pageIndex : 1;
            $limit = $pageSize*($pageIndex-1).','.$pageSize;



            if($fabric_id =='' ){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 102,
                        'msg'       => '面料id不能为空！'
                    ),
                );
                return $json->encode($return);
            }




            if($pl =='' ){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => '品类不能为空！'
                    ),
                );
                return $json->encode($return);
            }


            $fabricinfo_mod =&m('fabricinfo');
            $fabricrelattr_mod =&m('fabricrelattr');
            if ($is_woman) 
            {
                $conditions = "fai.is_frock_limit=1  and fa.attr_id=4 and fai.fabric_id={$fabric_id} and fa.attr_value='{$pl}' ";  //根据品类筛选
            }
            else 
            {
                $conditions = "fai.is_global=1  and fa.attr_id=4 and fai.fabric_id={$fabric_id} and fa.attr_value='{$pl}' ";  //根据品类筛选
            }
            


            $fabric_info = $fabricrelattr_mod->get(array(
                'join'=>"has_fabric_info",
                'conditions' => $conditions,
                'fields'=>"fa.attr_value,fai.curr_stock,fai.is_sale,fai.fabric_name,fai.is_global,fai.fabric_sn,fai.fabric_img,fai.fabric_id",
                'order'   => "fai.sort_order asc,fa.id desc ",
            ));
            
// return $conditions;
            if(empty($fabric_info)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 106,
                        'msg'       => '暂无数据f！'
                    ),
                );
                return $json->encode($return);
            }
            $fabric_sn = $fabric_info['fabric_sn'];

            //        $link_mod = m("links");
            //        $suitrelat_mod= m('suitrelat');
            //        $customfabric_mod   = m('customfabric');
            $fabric_mod = m('fabric');
            $suitlist_mod = m('suitlist');

            //全球首发   上架给app    品类
            $conditions="theme =11 AND is_sale=1 AND (to_site='0' OR to_site = 'app')";
            
            if ($is_woman) //=====  女装  =====
            {
                if ($pl == '套装')
                {
                    $conditions .= " AND is_woman = 1 ";
                }
                 $_pl_status = array(
                        '西服'=>'0011',//MXF
                        '西裤'=>'0012', //MXK
                        '衬衣'=>'0016',//MCY
                        '大衣'=>'0021',//MDY
                        '西裙'=>'0032',//MDY
                        '套装'=>0,
                    );
            
            }
            else //=====  男装  =====
            {
                
                if ($pl == '套装')
                {
                    $conditions .= " AND is_woman = 0 ";
                }
                 $_pl_status = array(
                        '西服'=>'0003',//MXF
                        '西裤'=>'0004', //MXK
                        '马夹'=>'0005',//MMJ
                        '马甲'=>'0005',//MMJ
                        '衬衣'=>'0006',//MCY
                        '大衣'=>'0007',//MDY
                        '套装'=> 0,
                    );
            }
            
//             $category = isset($_pl_status[$pl]) ? $_pl_status[$pl] : "";
            if(isset($_pl_status[$pl])){
                $category = $_pl_status[$pl];
                $conditions .="  and category = '{$category}'";
            }else{
                if($pl =='' ){
                    $return = array(
                        'statusCode' => 0, 
                        'error' => array(
                            'errorCode' => 103,
                            'msg'       => '无效的品类！'
                        ),
                    );
                    return $json->encode($return);
                }
            }

// return $conditions;
            $suitArr = $suitlist_mod->find(array(
                "conditions" => $conditions,
                'fields' =>"suit_name,points,image",
                'limit' => $limit,
            ));



            if(empty($suitArr)){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 104,
                        'msg'       => '暂无数据s！'
                    ),
                );
                return $json->encode($return);
            }



            if($fabric_info['is_sale']!=1){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg'       => '面料已经下架！'
                    ),
                );
                return $json->encode($return);
            }

            //已经售罄
            $xj = $fabric_info['curr_stock']>4?0:1;
            foreach($suitArr as $k=>$v){
                $suitArr[$k]['fabric_sn']=$fabric_sn;
                $suitArr[$k]['fabric_id']=$fabric_id;
                $suitArr[$k]['xj']=$xj;
                $suitArr[$k]['c_id']=$v['id'];
            }

            //        $pageIndex =$pageIndex-1;
            //        $data =array_chunk($qq_suit_arr,$pageSize,true);

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'data'  =>array_values($suitArr),
                )
            );

            return $json->encode($return);
        }
        /**
         *  VIP合伙人量体业务支持 选择量体时的帮助信息
         *
         * @param  array $arr 消息数组信息
         *
         * @return  string
         */
        public function chose_figure_help()
        {
            global $json;
            //获取固定类文章
            $_article_mod    = &m('article');
            $article='';
            $article = $_article_mod->get(array(
            		'conditions'=>"code='chose_figure_help'"
            ));//选择量体帮助

            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'content' => !empty($article) ? stripcslashes($article['content']) : '正在维护内容，请稍后！',
                    'success'  => '获取数据成功',
                )
            );

            return $json->encode($return);
        }
        
        function _get_options($except = NULL)
        {
            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
        
            $gcategories = $_cate_mod->get_list();
            $tree =& $this->_tree($gcategories);
        
            return $tree->getOptions(MAX_LAYER - 1, 0, $except);
        }
        
        /* 构造并返回树 */
        function &_tree($gcategories)
        {
            import('tree.lib');
            $tree = new Tree();
            $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
            return $tree;
        }
        /**
         * 地址管理列表
         */
       public function myAddrList($data)
        {
        	global $json;
        	$m       = m('member');
        	$address = m('address');
        	$token     = isset($data->token) ? $data->token: '';
        	$user_info = getToken($token);
        
        	if(empty($user_info)) {
        		$this->errorCode = 100;
        		$this->msg = '账号不存在';
        		return $this->eresult();
        	}

        
        	$user_id      = $user_info['user_id'];
        	$address_list = $address->find(array(
        			'join'       => 'belongs_to_member',
        			'conditions' => 'address.user_id='.$user_id,
        			'fields'     => 'address.*',
        			'order'      => 'addr_id DESC',//综合排序
        			'index_key'	 => '',
        
        	));
        	//默认地址
        	$def_addr = 0;
        	if($user_info['def_addr']) {
        		$def_addr = $user_info['def_addr'];
        	} else {
        		$end_id = current($address_list);
        		$def_addr = $end_id['addr_id'];
        	}
        
        	if($address_list){
        		foreach($address_list as $key=>$val) {
        
        			if($val['addr_id'] == $def_addr) {
        				$address_list[$key]['def_addr'] = 1;
        			}else{
        				$address_list[$key]['def_addr'] = 0;
        			}
        			if($val['region_id']) {
        				$region_mod = m('region');;
        				$region_list = $region_mod->find(array(
        						'conditions' => db_create_in($val['region_id'], 'region_id'),
        						'fields'     => 'region_name',
        						'index_key'  => '',
        				));
        					
        				$region_name_arr = array();
        				$region_name_str = array();
        				if($region_list) {
        					foreach($region_list as $v ) {
        						$region_name_arr[] = $v['region_name'];
        					}
        					$region_name_str = implode(",", $region_name_arr);
        				}
        				$address_list[$key]['region_name'] = !empty($region_name_str) ? $region_name_str : '';
        			}
        		}
        	}
        
        
        	$this->result = $address_list;
        	return $this->sresult();
        }
        
        
        
        /**
         * 添加地址
         *
         */
        public function addMyAddress($data)
        {
        	global $json;
        	$add_mod = m('address');
        	$m_mod   = m('member');
        	$region   = m('region');
        	$token     = isset($data->token) ? $data->token: '';
        	$consignee    = isset($data->consignee) ? $data->consignee : '';
        	$region_id   =isset($data->region_id) ? $data->region_id : '';//市级id
        	$address     = isset($data->address) ? $data->address : '';
        	$phone_mob    = isset($data->region_id) ? $data->phone_mob : '';
        	$is_def      = isset($data->is_def) ? $data->is_def : 0;//是否默认 1:设为默认 0：不设为默认
        	$user_info   = getToken($token);
        	if(!$user_info) {
        		$this->errorCode = 100;
        		$this->msg = '账号不存在';
        		return $this->eresult();
        	}
        
        	if(!$consignee){
        		$this->errorCode = 101;
        		$this->msg = '收件人姓名不能为空';
        		return $this->eresult();
        	}
        	if(!$region_id){
        		$this->errorCode = 101;
        		$this->msg = '没有最后一个地区的id';
        		return $this->eresult();
        	}
        		
        	if(!$address){
        		$this->errorCode = 101;
        		$this->msg = '地址不能为空';
        		return $this->eresult();
        	}
        
        	if(!$phone_mob){
        		$this->errorCode = 101;
        		$this->msg = '手机号不能为空';
        		return $this->eresult();
        	}
        	if(!preg_match("/1[34587]{1}\d{9}$/",$phone_mob))
        	{
        		$this->msg = '请输入11位手机号';
        		return $this->eresult();
        	}
        
        	if(!in_array($is_def,array(0,1))){
        		$this->msg = '参数只为1或0';
        		return $this->eresult();
        	}
        	$regions=$region->get(array(
        			'conditions' =>"region_id='{$region_id}'",
        	));
        
        		$region_name=$region->get(array(
        				'conditions'=>"region_id='{$regions['parent_id']}'",
        		));
        		if($region_name['parent_id'] == 0){
        			$region_names=$region_name['region_name'].' '.$regions['region_name'];
        		}else{
        			$region_na=$region->get(array(
        					'conditions'=>"region_id='{$region_name['parent_id']}'",
        			));
        			$region_names=$region_na['region_name'].' '.$region_name['region_name'].' '.$regions['region_name'];
        		}
        	
        	$region_ids=$region_na['region_id'].','.$region_name['region_id'].','.$regions['region_id'];
        	$user_id  = $user_info['user_id'];
        	$arr=array(
        			'user_id' => $user_id,
        			'consignee' => $consignee,
        			'region_id' => $region_ids,
        			'region_name' => $region_names,
        			'address' => $address,
        			'phone_mob' => $phone_mob,
        	);
        	$arr['user_id'] = $user_id;
        		
        	$arrs=$add_mod->add($arr);
        	//如果设为默认地址
        	if($is_def == 1) {
        		$res=$m_mod->edit($user_id, array("def_addr" => $arrs));
        		if(!$res){
        			$this->msg = '添加默认地址失败';
        			return $this->eresult();
        		}else{
        			$this->msg ='添加默认地址成功';
        			return $this->sresult();
        		}
        	}
        	if($arrs){
        		$this->msg ='添加成功';
        		return $this->sresult();
        	}else{
        		$this->msg = '添加失败';
        		return $this->eresult();
        	}
        		
        }
        
        /**
         * 修改收获地址
         *
         */
        public function editMyAddress($data)
        {
        	global $json;
        	$add_mod = m('address');
        	$m_mod   = m('member');
        	$region   = m('region');
        	$token     = isset($data->token) ? $data->token: '';
        	$addr_id     = isset($data->addr_id) ? $data->addr_id: '';
        	$user_info   = getToken($token);
        	if(!$user_info) {
        		$this->errorCode = 100;
        		$this->msg = '账号不存在';
        		return $this->eresult();
        	}
        	if(!$addr_id){
        		$this->errorCode = 101;
        		$this->msg = '没有地址id';
        		return $this->eresult();
        	}
        	 
        	$addr_reg=$add_mod->get(array(
        			'conditions' =>"addr_id={$addr_id}",
        	));
        	if(!$addr_reg){
        		$this->errorCode = 101;
        		$this->msg = '该用户没有该地址，请查证';
        		return $this->eresult();
        	}
        	if($addr_reg['user_id'] != $user_info['user_id']){
        		$this->errorCode = 101;
        		$this->msg = '此地址不是该用户的';
        		return $this->eresult();
        	}
        
        	if ($data->consignee) {
        		$data_a['consignee'] = $data->consignee;
        	}
        	if ($data->region_id) {
        		$data_a['region_id'] = $data->region_id;
        	}
        	if ($data->address) {
        		$data_a['address'] = $data->address;
        	}
        	if ($data->phone_mob) {
        		$data_a['phone_mob'] = $data->phone_mob;
        	}
        	if ($data->is_def) {
        		$data_m['is_def'] = $data->is_def;
        	}
        
        	if($data_a['region_id']){
        			
        		if(!$data_a['region_id']){
        
        			$this->errorCode = 101;
        			$this->msg = '没有最后一个地区的id';
        			return $this->eresult();
        		}
        			
        		$regions=$region->get(array(
        				'conditions' =>"region_id='{$data_a['region_id']}'",
        		));
        			
        		
        			$region_name=$region->get(array(
        					'conditions'=>"region_id='{$regions['parent_id']}'",
        			));
        			if($region_name['parent_id'] == 0){
        				$region_names=$region_name['region_name'].' '.$regions['region_name'];
        			}else{
        				$region_na=$region->get(array(
        						'conditions'=>"region_id='{$region_name['parent_id']}'",
        				));
        				$data_a['region_name']=$region_na['region_name'].' '.$region_name['region_name'].' '.$regions['region_name'];
        			}
        		
        		$data_a['region_id']=$region_na['region_id'].','.$region_name['region_id'].','.$regions['region_id'];
        	}
        
        	if($data_a['address']){
        			
        		if(!$data_a['address']){
        			$this->errorCode = 101;
        			$this->msg = '地址不能为空';
        			return $this->eresult();
        		}
        	}
        
        	if($data_a['phone_mob']){
        		if(!$data_a['phone_mob']){
        			$this->errorCode = 101;
        			$this->msg = '手机号不能为空';
        			return $this->eresult();
        		}
        
        		if(!preg_match("/1[34587]{1}\d{9}$/",$data_a['phone_mob']))
        		{
        			$this->msg = '请输入11位手机号';
        			return $this->eresult();
        		}
        	}
        
        	$user_id  = $user_info['user_id'];
        	//如果设为默认地址
        	if($data_m['is_def']){
        		if(!in_array($data_m['is_def'],array(0,1))){
        			$this->msg = '参数只为1或0';
        			return $this->eresult();
        		}
        		if($data_m['is_def'] == 1) {
        			$res=$m_mod->edit($user_id, array("def_addr" => $addr_id));
        			if(!$res){
        				$this->msg = '添加默认地址失败';
        				return $this->eresult();
        			}
        		}
        	}
        	$data_a['user_id'] = $user_id;
        	$arrs=$add_mod->edit($addr_id,$data_a);
        	if($arrs || ($data_m['is_def'] && $res)){
        		$this->msg ='编辑成功';
        		return $this->sresult();
        	}else{
        		$this->msg = '编辑失败';
        		return $this->eresult();
        	}
        }
        /**
         * 删除收获地址
         *
         */
        public function dropMyAddress($data)
        {
        	global $json;
        	$add_mod = m('address');
        	$m_mod = m('member');
        	$token     = isset($data->token) ? $data->token: '';
        	$addr_id     = isset($data->addr_id) ? $data->addr_id: '';
        	$user_info   = getToken($token);
        	if(!$user_info) {
        		$this->errorCode = 100;
        		$this->msg = '账号不存在';
        		return $this->eresult();
        	}
        	if(!$addr_id){
        		$this->errorCode = 101;
        		$this->msg = '没有地址id';
        		return $this->eresult();
        	}
        		
        	$addr_reg=$add_mod->get(array(
        			'conditions' =>"addr_id={$addr_id}",
        	));
        	if($addr_reg['user_id'] != $user_info['user_id']){
        		$this->errorCode = 101;
        		$this->msg = '此地址不是该用户的';
        		return $this->eresult();
        	}
        	if($user_info['def_addr'] == $addr_id){
        	  
        		$nex_addr=$add_mod->get(array(
        				'conditions' =>"user_id='{$user_info['user_id']}' AND addr_id <> '{$addr_id}'" ,
        				'order' => "addr_id DESC",
        		));
        
        		$res=$m_mod->edit($user_info['user_id'], array("def_addr" => $nex_addr['addr_id']));
        	}
        
        
        	$res = $add_mod->drop("addr_id=$addr_id");
        	if($res=== false){
        		$this->msg = '地址删除失败';
        		return $this->eresult();
        
        	}else{
        		$this->msg ='地址删除成功';
        		return $this->sresult();
        	}
        
        }
       public  function regionlist($data)
        {
        	global $json;
        	$add_mod = m('address');
        	$redion_m = m('region');
        	$reg_1  =isset($data->reg_1) ? $data->reg_1: '';
        	$list1 = $redion_m->get_options(2);
        	if($reg_1){
        		if(!$list1[$reg_1]){
        			$this->msg = '您给的所选第一级地址不正确';
        			return $this->eresult();
        		}
        		$list2 = $redion_m->get_options($reg_1);
        	}
        	$return = array(
        			'statusCode' => 1,
        			'result'     => array(
        					'list1' => $list1,
        					'list2'   => $list2,
        			),
        	);
        	return $json->encode($return);
        		
        }
        public function articleContent($data){
        	global $json;
        	$article_m = m('article');
        	$code=isset($data->code)?$data->code:'';
        	if(!$code){
        		$this->msg = 'code不能为空';
        		return $this->eresult();
        	}
        	$article=$article_m->get("code='{$code}'");
        	if($article){
        		$return = array(
        				'statusCode' => 1,
        				'result'     => array(
        						'article' => $article,
        				),
        		);
        		return $json->encode($return);
        	}else{
        		$this->msg = '没有找到该文章';
        		return $this->eresult();
        	}
        	
        }

    }

