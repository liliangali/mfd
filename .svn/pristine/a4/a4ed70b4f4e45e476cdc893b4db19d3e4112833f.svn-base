<?php

/* 会员 member */
class MemberModel extends BaseModel
{
    var $table = 'member';
    var $prikey = 'user_id';
    var $_name = 'member';
    var $_obj_search = 'user_name|用户名';
    var $_obj_fields = 'user_id|ID,user_name|用户名';
 
    /* 与其它模型之间的关系 */
    var $_relation = array(
        // 一个会员拥有一个店铺，id相同
        'has_store' => array(
            'model' => 'store', //模型的名称
            'type' => HAS_ONE, //关系类型
            'foreign_key' => 'store_id', //外键名
            'dependent' => true //依赖
        ),
    		// 一个量体师在一个门店下
    		'has_figurel' => array(
    				'model' => 'figure_liangti', //模型的名称
    				'type' => HAS_ONE, //关系类型
    				'foreign_key' => 'liangti_id', //外键名
    				'dependent' => true //依赖
    		),
        // 一个会员拥有cy_reload_log表记录
        'has_reload' => array(
            'model' => 'cy_reload_log', //模型的名称
            'type' => HAS_ONE, //关系类型
            'foreign_key' => 'user_id', //外键名
            'dependent' => true //依赖
        ),
    	// 一个会员拥有一个个人认证，id相同
    	'has_person' => array(
    		'model' => 'personauth', //模型的名称
    		'type' => HAS_ONE, //关系类型
    		'foreign_key' => 'user_id', //外键名
    		'dependent' => true //依赖  删除会员对应的认证记录和会删除
    	),
        //一个用户  暂时一个特权码 yusw
        'has_special' => array(
            'model' => 'special_code', //模型的名称
            'type' => HAS_ONE, //关系类型
            'foreign_key' => 'to_id', //外键名
            'dependent' => true
        ),
    	// 一个用户只能被一个人邀请
        'has_member' => array(
            'model' => 'memberinvite',
            'type' => HAS_ONE,
            'foreign_key' => 'invitee',
            'dependent' => true
        ),

        // 一个会员拥有一创业者认证，id相同
    	'has_auth' => array(
    		'model' => 'auth', //模型的名称
    		'type' => HAS_ONE, //关系类型
    		'foreign_key' => 'user_id', //外键名
    		'dependent' => true //依赖  删除会员对应的认证记录和会删除
    	),
        // 一个量体师拥有一个个人认证，id相同
        'has_figure' => array(
            'model' => 'figureauth', //模型的名称
            'type' => HAS_ONE, //关系类型
            'foreign_key' => 'user_id', //外键名
            'dependent' => true //依赖  删除会员对应的认证记录和会删除
        ),
    		// 一个会员拥有一个个人认证，id相同
    	'has_business' => array(
    		'model' => 'businessauth', //模型的名称
    		'type' => HAS_ONE, //关系类型
    		'foreign_key' => 'user_id', //外键名
    		'dependent' => true //依赖  删除会员对应的认证记录和会删除
    	),
        // 一个会员拥有一个个人认证，id相同
        'has_invi' => array(
            'model' => 'memberinvite', //模型的名称
            'type' => HAS_ONE, //关系类型
            'foreign_key' => 'invitee', //外键名
            'dependent' => true //依赖  删除会员对应的认证记录和会删除
        ),
        'manage_mall' => array(
            'model' => 'userpriv',
            'type' => HAS_ONE,
            'foreign_key' => 'user_id',
            'ext_limit' => array('store_id' => 0),
            'dependent' => true
        ),
        'group_mall' => array(
            'model' => 'usergroup',
            'type' => HAS_ONE,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        // 一个会员拥有多个收货地址
        'has_address' => array(
            'model' => 'address',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        // 一个会员拥有多个宠物
        'has_pet' => array(
            'model' => 'pet',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
    		// 一个会员拥有多个提现记录
    	'has_cash' => array(
    			'model' => 'cash',
    			'type' => HAS_MANY,
    			'foreign_key' => 'user_id',
    			'dependent' => true
    	),
        // 一个会员拥有多个交易记录
        'has_money' => array(
            'model' => 'membermoney',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        
        // 一个会员拥有多个交易记录
        'has_order_cash' => array(
            'model' => 'ordercashlog',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        // 一个会员拥有多个评论
        'has_comment' => array(
            'model' => 'com_comment',
            'type' => HAS_MANY,
            'foreign_key' => 'member_id',
            'dependent' => true
        ),
        // 一个会员拥有多个投诉
        'has_complaint' => array(
            'model' => 'complaint',
            'type' => HAS_MANY,
            'foreign_key' => 'from_id',
            'dependent' => true
        ),

        // 一个用户有多个订单
        'has_order' => array(
            'model' => 'order',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        // 一个用户有多条收到的短信
        'has_received_message' => array(
            'model' => 'message',
            'type' => HAS_MANY,
            'foreign_key' => 'to_id',
            'dependent' => true
        ),
        // 一个用户有多条发送出去的短信
        'has_sent_message' => array(
            'model' => 'message',
            'type' => HAS_MANY,
            'foreign_key' => 'from_id',
            'dependent' => true
        ),
        // 作品和会员是多对多的关系（会员点击喜欢或者顶) --yusw
      
		// 作品和会员是多对多的关系（会员收藏晒单）
        // 'collect_userwork' => array(
        //     'model' => 'userwork',
        //     'type' => HAS_AND_BELONGS_TO_MANY,
        //     'middle_table' => 'collect', //中间表名称
        //     'foreign_key' => 'user_id',
        //     'ext_limit' => array('type' => 'userwork'),
        //     'reverse' => 'be_collect', //反向关系名称
        // ),
        
        // 邀请人和被邀请人是多对多的关系（会员收藏晒单）
//        'gen_invite' => array(
//            'model' => 'generalize_member',
//            'type' => HAS_AND_BELONGS_TO_MANY,
//            'middle_table' => 'generalize_invite', //中间表名称
//            'foreign_key' => 'id',
//
//            'reverse' => 'be_invite', //反向关系名称
//        ),
        // 晒单和会员是多对多的关系（会员收藏晒单） add by xiao5
        // 'collect_single' => array(
        //     'model' => 'member_single',
        //     'type' => HAS_AND_BELONGS_TO_MANY,
        //     'middle_table' => 'collect', //中间表名称
        //     'foreign_key' => 'user_id',
        //     'ext_limit' => array('type' => 'single'),
        //     'reverse' => 'be_collect', //反向关系名称
        // ),
        // 面料和会员是多对多的关系（会员收藏面料） add by xiao5
        'collect_fabric' => array(
            'model' => 'part',
            'type' => HAS_AND_BELONGS_TO_MANY,
            'middle_table' => 'collect', //中间表名称
            'foreign_key' => 'user_id',
            'ext_limit' => array('type' => 'fabric'),
            'reverse' => 'be_collect', //反向关系名称
        ),
    		// 样衣和会员是多对多的关系（会员收藏面料） add by xiao5
    		'collect_customs' => array(
    		'model' => 'customs',
    		'type' => HAS_AND_BELONGS_TO_MANY,
    		'middle_table' => 'collect', //中间表名称
    		'foreign_key' => 'user_id',
    		'ext_limit' => array('type' => 'customs'),
    		'reverse' => 'be_collect', //反向关系名称
    		),
        // 会员和店铺是多对多的关系（会员拥有店铺权限）
        'manage_store' => array(
            'model' => 'store',
            'type' => HAS_AND_BELONGS_TO_MANY,
            'middle_table' => 'user_priv',
            'foreign_key' => 'user_id',
            'reverse' => 'be_manage',
        ),
        // 会员和好友是多对多的关系（会员拥有多个好友）
//        'has_friend' => array(
//            'model'        => 'member',
//            'type'         => HAS_AND_BELONGS_TO_MANY,
//            'middle_table' => 'friend',
//            'foreign_key'  => 'owner_id',
//            'reverse'      => 'be_friend',
//        ),
        // 会员提现申请是一对多
        'has_apply' => array(
            'model' => 'apply',
            'type' => HAS_MANY,
            'foreign_key' => 'user_id',
            'dependent' => true
        ),
        // 好友是多对多的关系（会员拥有多个好友）
//        'be_friend' => array(
//            'model'        => 'member',
//            'type'         => HAS_AND_BELONGS_TO_MANY,
//            'middle_table' => 'friend',
//            'foreign_key'  => 'friend_id',
//            'reverse'      => 'has_friend',
//        ),
        //用户与商品咨询是一对多的关系，一个会员拥有多个商品咨询
//        'user_question' => array(
//            'model' => 'goodsqa',
//            'type' => HAS_MANY,
//            'foreign_key' => 'user_id',
//        ),
        //会员和优惠券编号是多对多的关系
//        'bind_couponsn' => array(
//            'model'        => 'couponsn',
//            'type'         => HAS_AND_BELONGS_TO_MANY,
//            'middle_table' => 'user_coupon',
//            'foreign_key'  => 'user_id',
//            'reverse'      => 'bind_user',
//        ),
        // 会员和团购活动是多对多的关系（会员收藏商品）
//         'join_groupbuy' => array(
//             'model'        => 'groupbuy',
//             'type'         => HAS_AND_BELONGS_TO_MANY,
//             'middle_table' => 'groupbuy_log',    //中间表名称
//             'foreign_key'  => 'user_id',
//             'reverse'      => 'be_join', //反向关系名称
//         ),
//         // 一个会员发起一个团购
//         'start_groupbuy' => array(
//             'model'         => 'groupbuy',
//             'type'          => HAS_ONE,
//             'foreign_key'   => 'store_id',
//             'dependent'   => true
//         ),
        'has_serve' => array(
            'model' => 'serve',
            'type' => HAS_ONE,
            'foreign_key' => 'userid',
            'refer_key' => 'user_id'
        ),
        
        'has_lt' => array(
            "model"       => "figure_liangti",
            'type'        => HAS_ONE,
            'foreign_key' => 'liangti_id',
            'refer_key'   => 'user_id'
        ),
    );

    var $_autov = array(
        /* 'user_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ), */
//         'password' => array(
//             'required' => true,
//             'filter' => 'trim',
//             'min' => 6,
//         ),
    );

    /*
     * 判断名称是否唯一
     */
    function unique($user_name, $user_id = 0)
    {
        $conditions = "user_name = '" . $user_name . "'";
        $user_id && $conditions .= " AND user_id <> '" . $user_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }

    /*
     * 判断名称是否唯一
     */
    function uniqueNickname($nickname, $user_id = 0)
    {
        $conditions = "nickname = '" . $nickname . "'";
        $user_id && $conditions .= " AND user_id <> '" . $user_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    /*
     * 判断真实姓名是否唯一
    */
    function uniqueuser($username, $user_id = 0)
    {
    	$conditions = "(user_name = '" . $username . "' OR phone_mob='".$username."') AND serve_type=2";
    	
    	$user_id && $conditions .= " AND user_id <> '" . $user_id . "'";
    	
//     	echo $conditions;exit;
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }

    function drop($conditions, $fields = 'portrait',$flag=1)
    {
        if ($droped_rows = parent::drop($conditions, $fields,$flag))
        {
            restore_error_handler();
            $droped_data = $this->getDroppedData();
            foreach ($droped_data as $row)
            {
                $row['portrait'] && @unlink(ROOT_PATH . '/' . $row['portrait']);
            }
            reset_error_handler();
        }
        return $droped_rows;
    }

    function getById($id){
    	$db = &db();
    	$sql = "select * from cf_member where user_id = ".$id;
    	return $db->getRow($sql);
    }

    function changeAccount($user_id, $field){
    	if(empty($user_id) || empty($field)){
    		return false;
    	}

    	return $this->db->query("UPDATE {$this->table} SET {$field} WHERE user_id = '{$user_id}'");
    }

    function check_phone($phone_mob) {
    	$conditions = "phone_mob = '" . $phone_mob . "'";
    	$c = count($this->find(array('conditions' => $conditions)));
    	if ($c) {
    		return false;
    	} else {
    		return true;
    	}

    }

    function getDaren($limit = 4){
    	$db = &db();

    	$sql = "select * from cf_member where serve_type = 0 or serve_type = 4 order by experience desc limit $limit ";
    	return $db->getAll($sql);
    }

    function pageDaren($curr_page = 1 , $page = 10){
    	$db = &db();

    	if(!$curr_page)
    		$curr_page = 1;

    	$sql = "select count(*) as total from cf_member where serve_type = 0 or serve_type = 4 limit 200 ";
    	$cnt = $db->getRow($sql);


    	if($cnt['total']){
    		include 'includes/libraries/page_new.lib.php';
    		$page = new PageNew($curr_page,$cnt['total'] , $page);
    		$page->moduleSymbol = "/index.php/club-coolerlist.html";
    		$page->execPage();

    		$sql = "select * from cf_member where serve_type = 0 or serve_type = 4 order by experience desc limit  ".$page->mLimit[0]." , " .$page->mLimit[1];
    		$member = $db->getAll($sql);
    		foreach($member as $k=>$v){
    			$member[$k]['avatar'] = getAvatarByFile($v['avatar']);
    		}

    		return array('err'=>0,'page'=>$page,'data'=>$member);
    	}

    	return $db->getAll($sql);
    }
    //查询达人 ns add 模仿wdy
    function search_pageDaren($curr_page = 1 , $page = 10 , $keyword = ''){
        $db = &db();

        if(!$curr_page)
            $curr_page = 1;
        if($keyword){
            $commentstype = "nickname LIKE '%{$keyword}%' AND";
            $commentstype_s = " AND nickname LIKE '%{$keyword}%'";
        }

        $sql = "select count(*) as total from cf_member where ".$commentstype." serve_type = 0 or serve_type = 4".$commentstype_s;
        $cnt = $db->getRow($sql);

        if($cnt['total']){
            include 'includes/libraries/page_new.lib.php';
            $page = new PageNew($curr_page,$cnt['total'] , 12);
            $page->moduleSymbol = "/index.php/search-member.html";
            $page->execPage();

            $sql = "select * from cf_member where " . $commentstype . " serve_type = 0 or serve_type = 4" . $commentstype_s . " order by experience desc limit  " . $page->mLimit[0] . " , " . $page->mLimit[1];
            $member = $db->getAll($sql);
            foreach ($member as $k => $v) {
                $member[$k]['avatar'] = getAvatarByFile($v['avatar']);
                if ($this->visitor->info['user_id'])
                    if ($v['user_id'] == $this->visitor->info['user_id']) {
                        $member[$k]['self'] = 1;
                    } else {
                        $member[$k]['self'] = 0;
                        $isFans = isFans($this->visitor->info['user_id'], $v['user_id']);
                        if ($isFans)
                            $member[$k]['isFans'] = 1;
                    }

            }

            return array('err' => 0, 'page' => $page, 'data' => $member);
        }

        return $db->getAll($sql);
    }
	
	/**
	 * 充值余额
	 * @author xuganglong <781110641@qq.com>
	 * @2015年6月2日
	 */
	function recharge_money($info = array()) {
		
		$earr = array('code' => 1,'msg' => '');
		$sarr = array('code' => 0,'msg' => '');
		$member_info = $this->get(array(
        	'conditions' => "user_id =".$info['member_id'],
        ));

		if(empty($member_info)) {
			$earr['msg'] = "无对应充值用户";
			return $earr;
        }
		if($info['amount'] < 0) {
			$earr['msg'] = "充值金额小于零";
			return $earr; 
        }
        if ($info['status'] == 'TRADE_SUCCESS') 
        {
            return $sarr;
        }
        
        $money = $member_info['money'] + $info['amount'];
		
		//修改余额，并加入日志
		$res_edit = $this->edit($info['member_id'],  array('money' => $money));
		if(!$res_edit) {
			$earr['msg'] = "充值资金失败";
			return $earr;
        }
			 
		$cashLog = array(
			'name'        => '用户使用'.$info["pay_name"].'进行充值',
			'user_id'     => $info['member_id'],
			'minus'       => 4,
			'cash_money'  => $info['amount'],
			'add_time'    => time(),
			'order_money' => $money,//帐号余额
			'type'        => 4,
			'admin_id'    => 0, 
		);
        $mCashLog = & m('ordercashlog');
        $res_add  = $mCashLog->add($cashLog);  
        get_client_finance($info['member_id'],$info['payment_sn'],3,$info['amount']);

        if(!$res_add) {
			$earr['msg'] = "充值资金失败";
			return $earr;
        }
		return $sarr;
    }
    
    /**
     * 购买酷卡、酷币
     * @author xuganglong <781110641@qq.com>
     * @2015年6月2日
     */
    function recharge_coin($info = array()) {
    
        $earr = array('code' => 1,'msg' => '');
        $sarr = array('code' => 0,'msg' => '');
        $member_info = $this->get(array(
            'conditions' => "user_id =".$info['member_id'],
        ));
    
        if(empty($member_info)) {
            $earr['msg'] = "无对应购买用户";
            return $earr;
        }
    
        if($info['type'] == 'kuka') {//处理购买酷卡
            $result = $this->dokk($info);
        } else {//处理购买酷币
            $result = $this->dokuki($info);
        }
    
        if(!$result) {
            $earr['msg'] = "购买失败";
            return $earr;
        }
        return $sarr;
    }
    
    /**
     * 购买酷卡new 处理
     * @author tangshoujian <963830611@qq.com>
     * @2015年10月28日
     */
    function dokk($info = array()) {
    
        $_special_code_mod = & m('special_code');
        $_mod_ordergoods   = & m('ordergoods');
        $_kukaconfig_mod = & m('kukaconfig');
        $member_info = $this->get(array(
            'conditions' => "user_id =".$info['member_id'],
        ));
        $kuka = $_mod_ordergoods->get("order_id='{$info['order_id']}'");
         
        if(!$kuka) {
            return false;
        }
         
        $path = array (
    
            22 => array(
                'sign'=>'C',
                'name'=>'麦富迪E卡',
                'work'=>'用户激活后，面值金额以麦富迪币形式体现在麦富迪币账户，可用于购物结算。',//等级不变  积分在用户原来基础上+  ！！！
                'description'=>'用户激活后，面值金额以麦富迪币形式体现在麦富迪币账户，可用于购物结算。', //for app
                'message'=>'您已获得创业者麦富迪E卡特权，输入麦富迪E卡，直接享受麦富迪币福利', // for user
            ),
        );
        $this->_cate = $path;
        $kk   = $_kukaconfig_mod->get("id ='{$kuka['goods_id']}'");
        $num = $kuka['quantity'];
    
        $sn_arr =$_special_code_mod->sn1($this->_cate,'22','C',$num);
    
        if(empty($sn_arr))$sn_arr =$_special_code_mod->sn1($this->_cate,'22','C',$num);
    
        for($i=0;$i<$num;$i++){
    
            $data1 = array(
                'sn'=> $sn_arr[$i], //$sn
                'cate'=> 22, //生成一批干嘛的码
                'log_id'=>$kuka['goods_id'],
                'price'=>$kk['sale_price'],
                'expire_time'=> $kk['expire_time'],
                'work_num'=>$kk['kuka_num'],
                'add_time'=> gmtime(),
                'from_id' => $info['member_id'],
                'from_name' => $member_info['nickname'],
                'sign'      =>'1', // 判断 是否是线上 购买
                'name'    =>$kk['kuka_name'], //酷卡名称
                'type'    =>$kk['type'],      // 酷卡品类
                'content' =>$kk['content']  // 酷卡描述
    
            );
            $data2[] = $data1;
        }
    
        $res = $_special_code_mod->add($data2);
        if(!$res)
        {
            return false;
        }
    
        return true;
    }
    
    /**
     * 购买酷卡处理
     * @author xuganglong <781110641@qq.com>
     * @2015年6月2日
     */
    function dokuka($info = array()) {
        $_special_code_mod = & m('special_code');
        $_mod_ordergoods   = &m('ordergoods');
        $member_info = $this->get(array(
            'conditions' => "user_id =".$info['member_id'],
        ));
    
        $kuka = $_mod_ordergoods->get("order_id='{$info['order_id']}'");
        if(!$kuka) {
            return false;
        }
        $_speciallist = $_special_code_mod->findAll(array(
            'conditions' => "is_used = 0 and to_id = '' and from_id='' and expire_time > ".time()." and log_id = {$kuka['goods_id']}",
            'fields'     => 'id',
            'order'      => 'id desc',
            'limit'		 =>  '0,'. $kuka['quantity'],
        ));
    
        if(!$_speciallist) {
            return false;
        }
    
        $coin = 0;
        foreach($_speciallist as $val) {
            $res_edit = $_special_code_mod->edit($val['id'],  array('from_id' => $member_info['user_id'], 'from_name' => $member_info['nickname']));
        }
    
        if(!$res_edit) {
            return false;
        }
        return true;
    }
    
    /**
     * 购买酷币处理
     * @author xuganglong <781110641@qq.com>
     * @2015年6月2日
     */
    function dokuki($info = array()) {
        $coin_logmod = m('coinconfig');
        $_mod_ordergoods   = &m('ordergoods');
        $member_info = $this->get($info['member_id']);
        $kubi = $_mod_ordergoods->get("order_id='{$info['order_id']}'");
    
        $coin_info   = $coin_logmod->get(array(
            'conditions' => "id = '{$kubi['goods_id']}'",
        ));
    
        if($coin_info) {
            //修改余额，并加入日志
            $coin_num = $coin_info['coin_num'] + $member_info['coin'];
            $point    = $coin_info['point'] + $member_info['point'];
    
            $res_edit = $this->edit($info['member_id'],  array('coin' => $coin_num, 'point' => $point));
    
            if(!$res_edit) {
                return false;
            }
    
            $cashLog = array(
                'name'        => '用户购买'.$coin_info['coin_num'].'个酷币',
                'user_id'     => $info['member_id'],
                'minus'       => 4,// 购买麦富迪币
                'cash_money'  => $coin_info['coin_num'],
                'add_time'    => time(),
                'order_money' => '',
                'type'        => 2,
            );
            $mCashLog = m('ordercashlog');
            $res_add  = $mCashLog->add($cashLog);
    
            if(!$res_add) {
                return false;
            }
        }
        return true;
    }
    
}
?>