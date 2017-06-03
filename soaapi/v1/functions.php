<?php
use Cyteam\Message\SendMessage;
/* 订单状态 */
define('ORDER_PENDING', 11); //待付款
define('ORDER_WAITFIGURE', 12);//待量体  //只有订单需要量体时才会有这个状态  //在前台展示为已支付
define('ORDER_ACCEPTED', 20);//已支付
define('ORDER_PRODUCTION', 60);//生产中
//define('ORDER_CHECKING', 13);  //订单审核
define('ORDER_STOCKING', 61);//备货中
define('ORDER_SHIPPED', 30);  //已发货
define('ORDER_FINISHED', 40); //已完成
define('ORDER_REPAIR', 41);//返修中
//define('ORDER_CANCEL', 42);//已取消
define('ORDER_CANCELED', 0);//已取消
define('ORDER_ABNORMAL', 43);//订单异常
define('ORDER_WAITEDIT',   72); //订单重下(修改)

///====Ruesin Bgn
define('ORDER_SAVING', 12);     //保存中 1
///====Ruesin End

//=====添加几个订单状态 liang.li Beg====
define('ORDER_FIGURE', 50);                    //待量体
define('ORDER_TUIKUANZHONG', 70);                //订单退款中
define('ORDER_YINGTUIKUAN', 80);                //订单已退款
define('ORDER_FANXIUZHONG', 90);                //订单返修中
//====== end ===========
 
//=====  有关收益的几个常量配置  =====
define('PROFIT_X', 30);                //创业者收益 销售价  百分比
define('PROFIT_h', 0);                //创业者收益 活动款  百分比 (10.28 edit 活动款不再返收益和积分)
define('ORDER_POINT', 10);             //订单返积分 百分比
define('ORDER_TAX', 0);             //收益转余额需扣除的税 12.28 liang.li 不再扣除税负
define('VIP_ID', 9);             //收益转余额需扣除的税 12.28 liang.li 不再扣除税负
define('MEMBER_TYPE', 'default');
//define('LOCALHOST1', 'http://192.168.1.26');   // 可删除换为:$_SERVER['HTTP_HOST']
include(ROOT_PATH . '/vendors/Cyteam/Goods/Goods.php');
include(ROOT_PATH . '/vendors/Cyteam/Goods/Gcategory.php');

define ( "SMS_FAIL_TIME", 60 ); // 短信失效时间
/**
 *    连接会员系统
 *
 *    @author    Garbin
 *    @return    Passport 会员系统连接接口
 */
function &ms()
{
    static $ms = null;
    if ($ms === null)
    {
        include(ROOT_PATH . '/includes/passport.base.php');
        include(ROOT_PATH . '/includes/passports/' . MEMBER_TYPE . '.passport.php');
        $class_name  = ucfirst(MEMBER_TYPE) . 'Passport';
        $ms = new $class_name();
    }

    return $ms;
}


 if($_SERVER['HTTP_HOST'] == 'api.dev.mfd.cn'){
    define('LOCALHOST1', 'http://www.dev.mfd.cn:8080/');
    define('DIYURL', 'http://m.dev.mfd.cn/custom-diy2-');
    define('SHAREURL', 'http://wap.dev.mfd.cn/custom-diy-');
    define('FIGUREURL', 'http://api.figure.dev.mfd.cn:8080');
    define('FIGUREPC', 'http://figure.dev.mfd.cn:8080/');
    define('THESHAREURL', 'http://wap.dev.mfd.cn:8080/');
    define('SHOPURL', 'http://shop.diy.dev.mfd.cn:8080/');
}
elseif ($_SERVER['HTTP_HOST'] == 'api.mfd.cn'){
    define('LOCALHOST1', 'http://www.mfd.cn/');
    define('DIYURL', 'http://m.mfd.cn/custom-diy2-');
    define('SHAREURL', 'http://wap.mfd.cn/custom-diy-');
    define('FIGUREURL', 'http://api.figure.mfd.cn/');
    define('FIGUREPC', 'http://figure.mfd.cn/');
    define('THESHAREURL', 'http://wap.mfd.cn/');
    define('SHOPURL', 'http://shop.diy.mfd.cn/');
} 
elseif ($_SERVER['HTTP_HOST'] == 'api.mfd.com'){
    define('LOCALHOST1', 'http://mfd.mfd.com');
    define('DIYURL', 'http://m.mfd.cn/custom-diy2-');
    define('SHAREURL', 'http://wap.mfd.cn/custom-diy-');
    define('FIGUREURL', 'http://api.figure.com/');
    define('FIGUREPC', 'http://figure.mfd.com/');
    define('SHOPURL', 'http://shop.diy.dev.mfd.cn:8080/');
}
elseif ($_SERVER['HTTP_HOST'] == 'api.test.mfd.cn'){
    define('LOCALHOST1', 'http://www.test.mfd.cn/');
    define('DIYURL', 'http://m.test.mfd.cn/custom-diy2-');
    define('SHAREURL', 'http://wap.test.mfd.cn/custom-diy-');
    define('FIGUREURL', 'http://api.figure.test.mfd.cn');
    define('FIGUREPC', 'http://figure.test.mfd.cn/');
    define('THESHAREURL', 'http://wap.test.mfd.cn/');
    define('SHOPURL', 'http://shop.diy.test.mfd.cn/');
}
 elseif ($_SERVER['HTTP_HOST'] == 'local.api.mfd.com'){
     define('LOCALHOST1', 'http://local.m.mfd.com');
     define('MHOST', 'http://local.m.mfd.com');
     define('DIYURL', 'http://m.test.mfd.cn/custom-diy2-');
     define('FIGUREURL', 'http://figure.mfd.cn/');
 } elseif ($_SERVER['HTTP_HOST'] == 'api.mfd.p.day900.com'){
     define('MHOST', 'http://m.mfd.p.day900.com');
 }
 else
 {
     define('MHOST', 'http://h5.myfoodiepet.com');
 }


define('LOCALABOUT', 'http://new.m.dev.mfd.cn');
define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/upload/phpqrcode/');//定义二维码地址
define('PRODUCT_SHOW_SCALE', 5.2);//定义商品展示价格比例
define('M_URL', 'http://new.m.dev.mfd.cn/');
define('ADMIN_SITE_URL', 'http://new.www.dev.mfd.cn/admin/index.php?');

/*客户财务统计类型  */
define('FINANCE_GOODS_BUY',1);//发货：商品购买(西装、大衣、料册等)
define('FINANCE_WITHDRAW_DEPOSIT',2);//发货：提现
define('FINANCE_CASH_RECHARGE', 3);//收款：现金充值
define('FINANCE_INCOME_BALANCE', 4);//收益转余额
define('FINANCE_PAY_CASH', 5);//收款：现金
define('FINANCE_COIN_BUY',6);//收款：麦富迪币购买
define('FINANCE_BACKEND_RECHARGE',7);//后台会员账户调整

/* app常用标记 */
define('APP_BANNER_CODE', 'app_homepage');
define('APP_AD_CODE', 'app_ad_location');

function modeledit($table,$conditions,$type='edit')
{
    $table_arr = [
        'cf_order','cf_order_goods','cf_member',
    ];
    if(!in_array($table,$table_arr))
    {
        return;
    }

    $dir = ROOT_PATH."/upload/table/".$table;
    if($type == 'edit')
    {
        $dir = $dir."/up";
        if(!is_dir($dir))
        {
            @mkDirs($dir, 0755);
        }
        @file_put_contents($dir."/".trim(str_replace("WHERE","",$conditions)),$conditions);
    }
    else
    {
        $dir = $dir."/add";
        if(!is_dir($dir))
        {
            @mkDirs($dir, 0755);
        }
        @file_put_contents($dir."/".trim(str_replace("WHERE","",$conditions)),$conditions);
    }

}


function give_debit($amount,$user_id){

    $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
    $_cate_mod = & bm('gcategory', array('_store_id' => 0));

    $debit_mod = &m("debit");
    $member_mod = &m("member");

    if(!empty($store_allow['debit_cate_o']) && !empty($store_allow['debit_time_o']) && !empty($store_allow['debit_name_o']) && !empty($store_allow['debit_num_o']) && !empty($store_allow['debit_type_o']) && !empty($store_allow['debit_order_o']) && !empty($store_allow['debit_open_o'])){
        if($store_allow['debit_cate_o']==1){
            $expire_time =strtotime('+'.$store_allow['debit_time_o'].' days') - date('Z');
        }else{
            $expire_time =$store_allow['debit_time_o'];
        }
        $gcategories = $_cate_mod->get_child_cateid($store_allow['debit_type_o']);
        if(empty($gcategories)){
            $gcategories = $store_allow['debit_type_o'];
        }


        if($amount >= $store_allow['debit_order_o']){

            $data =array(
                'debit_name'=>$store_allow['debit_name_o'],
                'debit_t_id'=>$store_allow['debit_type_o'],
                'debit_sn'=>time().createNonceStr(8),
                'money'=>$store_allow['debit_num_o'],
                'user_id'=>$user_id,
                'source'=>'order',
                'add_time'=>time(),
                'cate'=>$gcategories,
                'expire_time'=>$expire_time,
            );

            $debit_mod->add($data);

        }
         
    }

}
/**
 * 字符串加密以及解密函数
 * @param string $string 原文或者密文
 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
 * @param string $key 密钥
 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
 * @see uc_authcode
 * @version 1.0.0 (2014-11-9)
 * @author Xiao5
 */
function ApiAuthcode($string, $operation = 'DECODE', $key = 'API-mfd', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}


/**
 * liang.li
 * @param unknown $url
 * @param string $data
 * @return mixed
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    $output = json_decode($output,true);
    return $output;
}

/**
 * 成为创业者
 * @author yusw   2015-12-24
 * @return    result( 1  成功 0 失败)     auth-认证(1 成功 0 失败)  invite-邀请（1成功 0失败） coin-酷币（1成功 2失败）
 */
function change_lv($user_id){
    $m = & m('member');
    $auth_mod =& m('auth');
    $member_invite = &m('memberinvite');
    $order_mod =&m("order");


    $data = array();
    $result =array("result"=>0,'invite'=>1,"auth"=>1,"coin"=>1);

    $user_info =$m->get("user_id=$user_id");
    $invite_info = $member_invite->get("invitee='{$user_id}' and type=1");
    $auth_log =$auth_mod->get("user_id='{$user_id}'");
    $coin_log = $order_mod->get("extension='coin' and  user_id={$user_id}");


    empty($coin_log)&&$result['coin']=0;
    empty($invite_info)&&$result['invite']=0;
    empty($auth_log)&&$result['auth']=0;


    if($user_info['member_lv_id']==1&&$coin_log['status']==20&&!empty($invite_info)&& $auth_log['status']==1){
        $data['member_lv_id']=2;
        $data['lv_time']= $data['cy_time']=time();
        if($user_info['invite'] == '') $data['invite']=make_order_card();

        $ret = $m->edit("user_id =".$user_id,$data);
        if(!$ret)$m->edit("user_id =".$user_id,$data);

        //站内信  12是会员升级
//        sendSystem($user_id, 12, '恭喜，您已成为麦富迪达人！', '尊敬的麦富迪会员,恭喜您已升级为麦富迪达人！') ;


        //积分
        reloadMember($user_id);
        editerweimaUrl($user_id);
        $result['result']=1;
    }
    return $result;

}

/**
 *会员加减积分之后重新生成会员等级
 *@author liang.li <1184820705@qq.com>
 *@2015年7月6日
 */
function reloadMember($user_id)
{

    $mod = m('member');
    $m_info = $mod->get_info($user_id);

    if (!$m_info || $m_info['serve_type'] !=1 || $m_info['member_lv_id'] <=1|| $m_info['member_lv_id'] >3)
    {
        return false;
    }

    $point = $m_info['point'];
    $lv_mod =& m('memberlv');
    $list = $lv_mod->find("lv_type='supplier'");
    //    array_pop($list);
    $lv_id = 0;
    if ($list)
    {
        foreach ($list as $key => $value)
        {
            $experience = $value['experience'];
            if ($point >= $experience && $key>$m_info['member_lv_id'] )
            {
                $lv_id = $key;
                $lv_name = $value['name'];
            }
        }
    }

    if ($lv_id)
    {
        $mod->edit($user_id,array('member_lv_id'=>$lv_id));
        

        //=====  给会员发信鸽  =====
        sendSystem($user_id, 12,
            '恭喜，您已成为'.$lv_name.'！',  '尊敬的麦富迪会员，您的积分已达到'.$point.',恭喜您已升级为'.$lv_name.'。随着积分的累计,您的等级头衔将提升，从而享受更优惠的折扣，拥有更多的特权。请密切关注你的积分变化。') ;
    }

    return true;
}
/**
 *由于会员等级变化 ，同步相应的 二维码跳转链接
 *@author tangsj <963830611@qq.com>
 *@2016年1月31日
 */

function editerweimaUrl($user_id){
    $m = &m('member');
    $user_info = $m->get($user_id);
    
    if($user_info['member_lv_id']==1){
        $type= 3;
        $code = $user_id;
    }elseif($user_info['member_lv_id']>1){
        $type= 2;
        $code = $user_info['invite'];
    }
    
    if($type == 3) {
        $share_url =$user_info['share_url'] = 'http://m.mfd.cn/invite.html';
        Qrcode('store', $user_id, $share_url, $avatar);
    } else {
        $share_url = $user_info['share_url'] = THESHAREURL.'member-register.html?ret_url=%2Fmember-index.html&type='.$type.'&er_invite='.$code;
        Qrcode('store', $user_id,$share_url, $avatar);
    }
    $mqrcode = getQrcodeImage('store', $user_id, 10);
    
    //修改 数据库 二维码 url
    $m->edit($user_id,array("erweima_url" => $mqrcode));
    
    $mqrcode = SITE_URL.'/upload/phpqrcode/'.$mqrcode.'?'.$user_info['avatar_time'];
    $user_info['erweima_url'] = !empty($mqrcode) ? $mqrcode : '';
}


function &cache_server()
{
	import('cache.lib');
	static $CS = null;
	if ($CS === null)
	{
		switch (CACHE_SERVER)
		{
			case 'memcached':
				list($host, $port) = explode(':', CACHE_MEMCACHED);
				$CS = new MemcacheServer(array(
						'host'  => $host,
						'port'  => $port,
				));
				break;
			default:
				$CS = new PhpCacheServer;
				$CS->set_cache_dir(PROJECT_PATH . '/temp/caches');
				break;
		}
	}

	return $CS;
}


/**
 * 通过token 过去用户信息
 * 截取UTF-8编码下字符串的函数
 *
 * @param   int      $user_id        用户的user_id
 *
 * @return  string
 */
function getToken($token){
    global $json;
    $return=array();
    $m = &m('member');
    if(empty($token)){
        $return['statusCode']=1;
        $return['msg']='失败';
    }else{
        $res=$m->get("serve_type=1 and user_token='$token'");

        if($res)
        {
            return $res;

        }
    }

}

/**
 *    获取商品类型对象
 *
 *    @author    Garbin
 *    @param     string $type
 *    @param     array  $params
 *    @return    void
 */
function &gt($type, $params = array())
{
    static $types = array();
    if (!isset($types[$type]))
    {
        /* 加载订单类型基础类 */
        include_once(PROJECT_PATH . '/includes/goods.base.php');
        include(PROJECT_PATH . '/includes/goodstypes/' . $type . '.gtype.php');
        $class_name = ucfirst($type) . 'Goods';

// return $class_name;
        $types[$type]   =   new $class_name($params);
    }

    return $types[$type];
}

/**
 *对象转换成数组
 *@author liang.li <1184820705@qq.com>
 *@2015年5月9日
 */
function objectToArray($e){
    $e=(array)$e;
    foreach($e as $k=>$v){
        if( gettype($v)=='resource' ) return;
        if( gettype($v)=='object' || gettype($v)=='array' )
            $e[$k]=(array)objectToArray($v);
    }
    return $e;
}


//随机字符串
function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}
/*邀请码*/
function make_order_card(){
    $m       = &m('member');
    $info = $m->get(array(
        'conditions'=>"invite LIKE '%CY%'",
        'fields'=>'max(invite) as invite',
    ));
    $invite = substr($info['invite'], 2,6);
    $invite_num = 'CY'.sprintf("%06d", $invite+1);

    return $invite_num;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    }
}




// 比较两个数组大小
function maxMin(Array $arr) {
     $cmpTime = 0;
     $count = count($arr);
     $biggest = $smallest = $arr[$count - 1];
     #每次取出两个元素，比较两个元素的大小再与最大值和最小值比较
     for($i = 0; $i < $count - 1; $i += 2) {
         $cmpTime++;
         if($arr[$i] > $arr[$i + 1]) {
            $bigger = $arr[$i];
            $smaller = $arr[$i + 1];
         } else {
            $bigger = $arr[$i + 1];
            $smaller = $arr[$i];
         }
         $cmpTime++;
         if($bigger > $biggest) {
             $biggest = $bigger;
         }
         $cmpTime++;
         if($smaller < $smallest) {
            $smallest = $smaller;
         }
     }

    return $biggest;
 }


/**
 * 获取用户上传图片 绝对地址
 * $path_name ： shaidan
 * @return void
 * @version 1.0.0 (2014-22-22)
 * @author Xiao5
 */
function getUserPhotoUrl($path_name,$file_name,$size = '500',$type = 'big'){
/*     if(!$file_name)
        return getDefAlbumPic($type); */

    if($size == '500'){
        $path = "520x685";
    }elseif($size ==  '200'){
        $path = "235x315";
    }else{
        $path = "original";
    }
    //$src= get_domain() . "/upload_user_photo/".$path_name."/$path/".$file_name;
	$src = LOCALHOST1."/upload_user_photo/".$path_name."/$path/".$file_name;
    return $src;
}


/*短信发送*/
function SendSms($phone, $msg, $type, $r, $fail_time=60) {
	    $err = array('-1'=>'传递参数错误','-2'=>'用户id或密码错误','-3'=>'通道id错误','-4'=>'手机号码错误','-5'=>'短信内容错误','-6'=>'余额不足错误=','-7'=>'绑定ip错误',
            '-8'=>'未带签名','-9'=>'签名字数不对','-10'=>'通道暂停','-11'=>'该时间禁止发送','-12'=>'时间戳错误','-13'=>'编码异常','-4'=>'发送被限制');

		//获得配置项
		//$conf = include ROOT_PATH.'/data/settings.inc.php';//获得配置信息
		//$user_id   = $conf['msg_pid']; // sms9平台用户id
		//$pass      = $conf['msg_key']; // 用户密码
		//$channelid = $conf['channelid']; // 发送频道id
		
		//后台无配置地方，暂时写死--
        $timestamp = time();
        $channelid = 12852 ; // 发送频道id
        $cpid = 11664;
        $ps = md5("mfd123_".$timestamp."_topsky");

        $msg = iconv("UTF-8",'GBK',$msg);
        //$url = "http://admin.sms9.net/houtai/sms.php?cpid={$cpid}&password={$ps}&channelid={$channelid}&tele={$phone}&timestamp={$timestamp}&msg={$msg}";

        //$rs = file_get_contents($url);

        //        $back = substr($rs,6);
        
        $pre ='success';
        //$r = '111111';
        $rs = true;
         
         
        if($pre != 'success') {
            return false;
        }
        
        //$pre = substr($rs,0,7);
       /*  if($pre != 'success') {
            return false;
        } */

        //$type = !empty($type) ? $type : "demand";
        $sms_reg_mod = m('SmsRegTmp');
        $sms_reg_mod->drop("type='$type' AND phone='$phone' ");
        $data['type'] = $type;
        $data['code'] = $r;
        $data['add_time'] = time();
        $data['phone'] = $phone;
        $data['fail_time'] = time() + $fail_time;
        if($sms_reg_mod->add($data))
        {
        	return $rs;
        }
        else
        {
        	return false;
        }

}

/**
 * 手机验证逻辑处理
 *
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author
 */
function smsAuthCode($phone, $category, $type, $send_opt, $way, $ps = '', $temp = array())
{
    $sms_reg_tmp_mod = & m ( 'sms_reg_tmp' );
    $reset_time = SMS_FAIL_TIME;
    $conditions = array (
        'conditions' => " category = '$category' and type = '$type' and phone = '$phone' for update "
    );
    // 从新索引下标
    $sms_log = array_values ( $sms_reg_tmp_mod->findAll ( $conditions ) );
    // 正确的来讲，同一种类型下，只能一条数据
    if (count ( $sms_log ) > 1) {
        $sms_reg_tmp_mod->drop ( " category = '$category' and type = '$type' and phone = '$phone' " );
        $sms_log = null;
    }

    
    $rs = array (
        'err' => 0
    );
    if ($sms_log) {
        // send_opt:get第一次获取一个码,res:重新发送一个验证码
        if (! $send_opt || $send_opt == 'get') {
            if($sms_log[0]['fail_time'] > time()){
                //还未失效，不需要再次发送
                if($way != 'pc'){
                    return outinfo($reset_time."秒内，请不要重复发送...验证码",1,$way);
                }
            }else{//已失效，直接重新发送
                $sms_reg_tmp_mod->drop(" category = '$category' and type='$type' and phone = '$phone' ");
                $rs = smsCode($phone,$category,$type,$ps,$temp);
            }
        } else { // 重置一个码
            if ($sms_log [0] ['fail_time'] > time ()) {
                // 还未失效，不需要再次发送
                $str = $reset_time . "秒之后才可重置...";
                return outinfo ( $str, 1, $way );
            } else {
                // $rs = smsCode($phone,$category,$type,$sms_log[0]['ps'],$temp);
                $rs = smsCode ( $phone, $category, $type, $ps, $temp );
            }
        }
    } else {
        $rs = smsCode ( $phone, $category, $type, $ps, $temp );
    }

    // return $rs;
    // $rs = intval($rs);

    if (isset ( $rs ['err'] ) && $rs ['err']) {
        return $rs;
    } else {

        return outinfo ( $reset_time, 0, $way );
    }
}

/**
 * 手机验证逻辑处理-记录log
 *
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function smsCode($phone, $category, $type, $ps = '', $temp) {
    // $desc = getSmsEmailDesc($category,$type,$temp);
    include_once ROOT_PATH.'/vendor/autoload.php';
    $rand = rand ( 1000, 9999 );
    $sendMessage_obj = new SendMessage ();
    switch ($category) {
        case 'findps' :
            $params = "{'code':'$rand','time':'" . SMS_FAIL_TIME . "'}";
            $code = 'SMS_12910731';
            break;
        case 'binduser' :
            $params = "{'code':'$rand','time':'" . SMS_FAIL_TIME . "'}";
            $code = 'SMS_12910731';
            break;
        case 'reg' :
            $params = "{'code':'$rand','time':'" . SMS_FAIL_TIME . "'}";
            $code = 'SMS_12910731';
            break;
        case 'deliver':
            if($temp){
                $params = "{'order_sn':'".$temp[order_sn]."','express':'" . $temp['express']. "'}";
                $code = 'SMS_12811895';;
            }else{
                return false;
            }
            break;
        default :
            return false;
    }
    // $rand = "{\"order_sn\":\"11111\",\"product\":\"heihei\"}";
    // $rand = "{'code':'11111','product':'heihei'}";
    // $msg = str_replace("<#code>",$rand,$desc);
    $rs = $sendMessage_obj->sendCode ( $params, strval($phone), $code );
    $err=array(
        'isv.OUT_OF_SERVICE'=>'业务停机',
        'isv.PRODUCT_UNSUBSCRIBE'=>'产品服务未开通',
        'isv.ACCOUNT_NOT_EXISTS'=>'账户信息不存在',
        'isv.ACCOUNT_ABNORMAL'=>'账户信息异常',
        'isv.SMS_TEMPLATE_ILLEGAL'=>"模板不合法",
        'isv.SMS_SIGNATURE_ILLEGAL'=>"签名不合法",
        'isv.MOBILE_NUMBER_ILLEGAL'=>"手机号码格式错误",
        'isv.MOBILE_COUNT_OVER_LIMIT'=>"手机号码数量超过限制",
        'isv.TEMPLATE_MISSING_PARAMETERS'=>"短信模板变量缺少参数",
        'isv.INVALID_PARAMETERS'=>"参数异常",
        'isv.BUSINESS_LIMIT_CONTROL'=>"触发业务流控限制",
        'isv.INVALID_JSON_PARAM'=>'JSON参数不合法',
        'isp.SYSTEM_ERROR'=>'系统错误',
        'isv.BLACK_KEY_CONTROL_LIMIT'=>'模板变量中存在黑名单关键字',
        'isv.PARAM_NOT_SUPPORT_URL'=>'不支持url为变量',
        'isv.PARAM_LENGTH_LIMIT'=>'变量长度受限',
        'isv.AMOUNT_NOT_ENOUGH'=>'余额不足',
    );
    if (isset ( $rs->code )) {
        $sub_code=strval($rs->sub_code);
        // var_dump($rs);
        return array(
            'err'=>1,
            'msg'=>$err[$sub_code],
        );
    } else {

        $sms_reg_tmp_mod = & m ( 'sms_reg_tmp' );
        $sms_reg_tmp_mod->drop( " category = '$category' and type='$type' and phone = '$phone' " );
        $data = array (
            'code' => $rand,
            'type' => $type,
            'fail_time' => time () + SMS_FAIL_TIME,
            'add_time' => time (),
            // 'uid'=> $this->visitor->info['user_id'],
            'category' => $category,
            'phone' => $phone,
            'ps' => $ps
        );

        return $sms_reg_tmp_mod->add ( $data );
    }
}



//发送短信接口-wangdy
function get2($url, $referer = ""){
	$info = null;
	$ch = curl_init($url);
	$options = array(
			CURLOPT_RETURNTRANSFER => true,         // return web page
			CURLOPT_HEADER         => false,        // don't return headers
			CURLOPT_FOLLOWLOCATION => true,         // follow redirects
			CURLOPT_ENCODING       => "",           // handle all encodings
			CURLOPT_USERAGENT      => "",     // who am i
			CURLOPT_AUTOREFERER    => true,         // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
			CURLOPT_TIMEOUT        => 120,          // timeout on response
			CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
			CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
			CURLOPT_SSL_VERIFYPEER => false,        //
			CURLOPT_REFERER        =>$referer,
			CURLOPT_SSLVERSION =>3,
	);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


/**
 * 发送系统消息，并信鸽推送
 *
 * @param int $status
 * @param string $msg
 * @param mixed $data
 * @param string $dialog
 */
function sendSystem($to_user_id, $type, $title_mes, $content_mes, $send_type = "all") {
	$_message_mod = &m("usermessage");
	$m_mod        = m('member');
	$arr = array(
	   'from_user_id'  => 1,
	   'to_user_id'    => $to_user_id,
	   'from_nickname' => '系统',
	   'type'          => $type,
	   'title'         => $title_mes,
	   'content'       => $content_mes,
	   'add_time'      => time(),
	   'location_url'   =>'',
	);
	$res = $_message_mod->add($arr);
	//发送信鸽推送
	if($res) {
		//信鸽推送最多70字，限制其推送长度
		$content_mes = mb_substr($content_mes, 0, 60, 'utf8');

		include_once(ROOT_PATH.'/includes/xinge/xinggemeg.php');
		$inviter_info = $m_mod->get($to_user_id);
		$push = new XingeMeg();
		$result =  $push->tomfdXinApp($inviter_info['user_token'], $title_mes, $content_mes, array('url_type' => 'system', 'location_id' => $res), $send_type);
	
		return $result;
	}
	return false;
}

/**
 * AJAX返回数据标准
 *
 * @param int $status
 * @param string $msg
 * @param mixed $data
 * @param string $dialog
 */
function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
	header('Content-Type:text/html; charset=utf-8');
	exit(json_encode(array(
			'status' => $status,
			'msg' => $msg,
			'data' => $data,
			'dialog' => $dialog,
	)));

}



/**
 *    基本款 接口映射
 *
 *    @author
 *    @return    Custom 基本款接口
 */
function &cs($fname = '')
{
	static $cs = null;
	if ($cs === null)
	{
		include(PROJECT_PATH . '/includes/libraries/custom'.$fname.'.lib.php');
		$classname  = 'Custom'.$fname;
		$cs = new $classname();
	}

	return $cs;
}

/**
 *    获取数组文件对象
 *
 *    @author    Garbin
 *    @param     string $type
 *    @param     array  $params
 *    @return    void
 */
function &af($type, $params = array())
{
    static $types = array();
    if (!isset($types[$type]))
    {
        /* 加载数据文件基础类 */
        include_once(ROOT_PATH . '/includes/arrayfile.base.php');
        include(ROOT_PATH . '/includes/arrayfiles/' . $type . '.arrayfile.php');
        $class_name = ucfirst($type) . 'Arrayfile';
        $types[$type]   =   new $class_name($params);
    }

    return $types[$type];
}

/**
 * 发送邮件接口执行
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function sendmail($email,$msg){
    $model_setting = &af('settings');
    $setting = $model_setting->getAll(); //载入系统设置数据
    //$email_from = Conf::get('site_name');
    $email_type = $setting['email_type'];
    $email_host = $setting['email_host'];
    $email_port = $setting['email_port'];
    $email_addr = $setting['email_addr'];
    $email_id   = $setting['email_id'];
    $email_pass = $setting['email_pass'];
    $email_test = $email;				//接收人email
    $email_subject = '用户帐号激活';	//邮件标题
    include_once(ROOT_PATH.'/soaapi/v1/includes/libraries/mailer.lib.php');
    $mailer = new Mailer($email_from, $email_addr, $email_type, $email_host, $email_port, $email_id, $email_pass);
    $res = $mailer->send($email_test, $email_subject, $msg, CHARSET, 1);

    if ($mailer->errors)
    {
        return  array('err'=>1,'msg'=>$mailer->errors[0]);
    }
    return array('err'=>0);
}
/**
 * 发送邮件-邮件url
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getEmailcode($email,$category){
    $code = getCode($email);
    $url = getEmailbackUrl($category);
    $url .= "&email=$email&code=".$code."&account_type=e";
    return $url;
}

/**
 * 获取加密邮件code
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getCode($email){
    return md5("alicaifeng".$email);
}
/**
 * 验证加密邮件
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function authemail($email,$code){
    $authcode = md5("alicaifeng".$email);
    if($authcode == $code)
        return 1;

}
/**
 * 获取邮件返回链接地址
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getEmailbackUrl($category){
    $arr = array(
        'findps'=>site_url()."/member-find_password.html?opt=3",
//         'bind'=>site_url()."index.php/kuke-bindemail.html?opt=3",
//         'email'=>site_url()."index.php/kuke-upemail.html?opt=4",
        'editEmail'=>site_url()."/member-bingEmail.html?",
        'reg'=>site_url()."/member-eValidation.html?",
    );
    return $arr[$category];
}

/**
 * 根据邮件类型获取中文描述
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getEmailDesc($category,$url,$temp){
    $dfstr = "尊敬的 用户 :<br/>&nbsp;&nbsp;&nbsp;您好！<br/>";
    $arr = array(
        'findps'=>'您在'.date( "Y/m/d H:i:s" ).'提交了邮箱找回密码请求，请点击下面的链接修改密码:<a href='.$url.'>'.$url.'</a>',
        'reg'=>'欢迎您注册阿里裁缝，请点击以下链接完成邮箱验证:<a href='.$url.'>'.$url.'</a>',
        'test'=>'欢迎您注册阿里裁缝<{$test}>，请点击以下链接完成邮箱验证:<a href='.$url.'>'.$url.'</a>',
    );
    /* add by xiao5 替换模版变量 */
    if(!empty($temp)){
        foreach ($temp as $k=>$v){
            $arr[$category] = str_replace("<{"."$".$k."}>",$v,$arr[$category]);
        }
    }
    return $dfstr.$arr[$category]."<br/> <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如果您无法点击此链接，请将它复制到浏览器地址栏后访问.";
}
/**
 * 邮箱匿名
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getHideEmail($email){
    $l = strpos($email,"@");
    return substr( $email, 0,$l - 4). "****". substr( $email, $l);

}
/**
 * 手机匿名
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getHidePhone($phone){
    return substr( $phone, 0,3). "****". substr( $phone, 7);

}
/**
 * 匹配邮箱地址
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getEmailHref($email){
    $a_email = array(
        'sina'=>'mail.sina.com.cn',
        '163'=>'mail.163.com',
        'qq'=>'mail.qq.com',
        '126'=>'mail.126.com',
        'hotmail'=>'login.live.com',
        'gmail'=>'mail.google.com',
        'yahoo'=>'mail.aliyun.com',
        'aliyun'=>'mail.aliyun.com'
    );

    preg_match_all('/@(.*?)\./',$email,$a_href);
    if( isset($a_href[1][0])){
        if(  isset(  $a_email[$a_href[1][0]] ) ){
            return $a_email[$a_href[1][0]];
        }
    }

    return "#";
}

























/**
 * 喜欢接口
 */
function setLike($uid,$like_id,$cate,$away = 'pc'){
	//checkCate($cate);

	$m = m('like');
	$like = getLikeByUid($uid,$like_id,$cate);
	if($like){
		return outinfo('已喜欢',1,$away);
	}

	$data = array(
			'add_time'=>time(),
			'uid'=>$uid,
			'like_id'=>$like_id,
			'cate'=>$cate,
	);

	$m->add($data);
	$member = m('member');
	$member->setInc(" user_id = $uid " , 'like_num');

	return outinfo('ok',0,$away);
}

/**
 * 喜欢接口
 */
function setUnLike($uid,$like_id,$cate,$away = 'pc'){
	//checkCate($cate);

	$m = m('like');
	$conditions = "uid=$uid AND like_id=$like_id AND cate='$cate' ";
	$m->drop($conditions);

	$member = m('member');
	$member->setInc(" user_id = $uid " , 'like_num',-1);

	return outinfo('ok',0,$away);
}

function getLikeByUid($uid ,$like_id , $cate){
	if(!$uid || !$like_id || !$cate)
		return 0;

	$arr = array('design'=>'酷客首页-个人设计','userphoto'=>'酷客首页-街拍');
	$like_mod = m('like');
	$conditions = "uid = $uid and cate = '$cate' and like_id =$like_id ";
	$like = $like_mod->get(array(
			'conditions' => $conditions,
			));
	if($like)
		return 1;
	return 0;
}

function outinfo($msg,$err = 1 ,$way = 'pc'){
	$rs = array('err'=>$err,'msg'=>$msg);
	if($way == 'ajax'){
		echo json_encode($rs);
		exit;
	}elseif($way == 'pc'){
		return $rs;
	}else{
		if($err){
			echo 'false';
		}else
			echo 'true';
	}
}




function getAPICate(){
	$arr = array
	(
			//李亮
			'like_project'=>array('desc'=>'喜欢项目的积分','type'=>'like','key'=>'like_project'),
	);
	return $arr;
}


/*通过会员的photoUrl字段格式化会员的图片地址*/
function getFoucsUrl($photoUrl,$type,$boolen=TRUE)
{
	if ($boolen)
	{
		return SITE_URL."/upload_user_photo/user/original/".$photoUrl."_$type.png";
	}
	else
	{
		return ROOT_PATH."/upload_user_photo/user/original/".$photoUrl."_$type.png";
	}
}



function setComment($uid,$to_uid,$cate,$content,$project_id){
	if( $uid ){

		$m = m('comment');
		$data = array(
				'uid'=>$uid,
				'to_uid'=>$to_uid,
				'cate'=>$cate,
				'content'=>$content,
				'add_time'=>microtime_float(),
				'project_id'=>$project_id
		);
		$new_id = $m->add($data);

	}else{//匿名评论
		$m = m('comments');
		$data = array(
				'uid'=>$uid,
				'to_uid'=>$to_uid,
				'cate'=>$cate,
				'content'=>$content,
				'add_time'=>microtime_float(),
				'project_id'=>$project_id
		);
		$new_id = $m->add($data);
	}

	return $new_id;
}

/*获得简历|动态的Url地址*/
function getProjectUrl($img_url,$boolen=true)
{
	if ($boolen)
	{
		return SITE_URL."upload_user_photo/project/original/".$img_url;
	}
	else
	{
		return ROOT_PATH."/upload_user_photo/project/original/".$img_url;
	}

}


//PHP stdClass Object转array
function object_array($array) {
	if(is_object($array)) {
		$array = (array)$array;
	} if(is_array($array)) {
		foreach($array as $key=>$value) {
			$array[$key] = object_array($value);
		}
	}
	return $array;
}

/*团队管理消息*/
function getTeamUrl($img_url,$boolen=true)
{
	if ($boolen)
	{
		return SITE_URL."/data/files/team/".$img_url;
	}
	else
	{
		return ROOT_PATH."/data/files/team/".$img_url;
	}

}
















function curl_http_header($url,$post_data){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_URL,$url);

	curl_setopt($ch, CURLOPT_HEADER, 1);
	// 	curl_setopt($ch, CURLOPT_USERAGENT, "local.test.com");
	// 	curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.txt'); //保存
	// 	curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.txt'); //读取

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	ob_start();
	try {
		curl_exec($ch);
	}catch (Exception $e){
		var_dump($e);
	}
	$result = ob_get_contents() ;
	ob_end_clean();

	return  $result;
}

/**
 * 获取一个业务模型
 *
 * @param string $model_name
 * @param array $params
 * @param bool $is_new
 * @return object
 */
function &bm($model_name, $params = array(), $is_new = false)
{
	static $models = array();
	$model_hash = md5($model_name . var_export($params, true));
	if ($is_new || !isset($models[$model_hash]))
	{
		$model_file = ROOT_PATH . '/includes/models/' . $model_name . '.model.php';
		if (!is_file($model_file))
		{
			/* 不存在该文件，则无法获取模型 */
			return false;
		}
		include_once($model_file);
		$model_name = ucfirst($model_name) . 'BModel';
		if ($is_new)
		{
			return new $model_name($params, db());
		}
		$models[$model_hash] = new $model_name($params, db());
	}

	return $models[$model_hash];
}

function print_exit($str){
	echo '<pre>';
	print_r($str);
	exit;
}


/**
 * getConf 获取配置文件
 * @param  $file    配置文件
 * @param  $key		下标
 * @param  $val		值
 * @return string
 * @author Ruesin
 */
function getConf($file='',$key=''){
    $config=array();
    if($file=='' || $key=='')return;
   	$dir=PROJECT_PATH . '/includes/data/config/'.$file.'.php';
   	$str = "<?php exit(); ?>";
   	if(is_file($dir)){
       	$fileStr=file_get_contents($dir);
       	$config=unserialize(str_replace($str, '', $fileStr));

       	return $config[$key];
   	}else{
   	    return;
   	}
}
/**
 * 根据token 获得user_info
 */
function getUserInfo($token)
{
	if (!$token)
	{
		return array();
	}
	$user_mod = m("member");
	$member_lv_mod = m('memberlv');
	$user_info = $user_mod->get(array(
			'conditions'	=>	"serve_type=1 and user_token= '$token'",
	));
	
	//=====  取得会员对应的折扣  =====
	if ($user_info)
	{
	    $lv = $user_info['member_lv_id'];
	     
	     
	    $lv_info = $member_lv_mod->get("member_lv_id = $lv AND lv_type = 'supplier' ");
	    if ($lv_info)
	    {
	        $user_info['dis_count'] = $lv_info['dis_count']/10;
	    }
	    else
	    {
	        $user_info['dis_count'] = 0;
	    }
	}
	
	
	/* if ($user_info['photoUrl'])
	{
		$user_info['photoUrl'] = SITE_URL.'upload_user_photo/user/original/'.$user_info['user_id']."_160.png";
	} */

	return $user_info;
}

/**
 * 获得网站的URL地址
 *
 * @return  string
 */
function site_url()
{
	return get_domain() . substr(PHP_SELF, 0, strrpos(PHP_SELF, '/'));
}

/**
 *    导入一个类
 *
 *    @author    Garbin
 *    @return    void
 */
function import()
{
	$c = func_get_args();
	if (empty($c))
	{
		return;
	}
	array_walk($c, create_function('$item, $key', 'include_once(PROJECT_PATH . \'/includes/\' . $item . \'.php\');'));
}

/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function gmtime()
{
	//return (time() - date('Z'));
	return time(); 
}

/**
 * 重写
 * 递归方式的对变量中的特殊字符进行转义
 * @access  public
 * @param   mix     $value
 * @return  mix
 */
function addslashes_deep($value,$htmlspecialchars=false)
{
	if (empty($value))
	{
		return $value;
	}
	else
	{
		if(is_array($value))
		{
			foreach($value as $key => $v)
			{
				unset($value[$key]);

				if($htmlspecialchars==true)
				{
					$key=get_magic_quotes_gpc()? addslashes(stripslashes(htmlspecialchars($key,ENT_NOQUOTES))) : addslashes(htmlspecialchars($key,ENT_NOQUOTES));
				}
				else{
					$key=get_magic_quotes_gpc()? addslashes(stripslashes($key)) : addslashes($key);
				}

				if(is_array($v))
				{
					$value[$key]=addslashes_deep($v);
				}
				else{
					if($htmlspecialchars==true)
					{
						$value[$key]=get_magic_quotes_gpc()? addslashes(stripslashes(htmlspecialchars($v,ENT_NOQUOTES))) : addslashes(htmlspecialchars($v,ENT_NOQUOTES));
					}
					else{
						$value[$key]=get_magic_quotes_gpc()? addslashes(stripslashes($v)) : addslashes($v);
					}
				}
			}
		}
		else{
			if($htmlspecialchars==true)
			{
				$value=get_magic_quotes_gpc()? addslashes(stripslashes(htmlspecialchars($value,ENT_NOQUOTES))) : addslashes(htmlspecialchars($value,ENT_NOQUOTES));
			}
			else{
				$value=get_magic_quotes_gpc()? addslashes(stripslashes($value)) : addslashes($value);
			}
		}

		return $value;
	}
}


/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串,如果为字符串时,字符串只接受数字串
 * @param    string   $field_name     字段名称
 * @author   wj
 *
 * @return   void
 */
function db_create_in($item_list, $field_name = '')
{
	if (empty($item_list))
	{
		return $field_name . " IN ('') ";
	}
	else
	{
		if (!is_array($item_list))
		{
			$item_list = explode(',', $item_list);
			foreach ($item_list as $k=>$v)
			{
				$item_list[$k] = intval($v);
			}
		}

		$item_list = array_unique($item_list);
		$item_list_tmp = '';
		foreach ($item_list AS $item)
		{
			if ($item !== '')
			{
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty($item_list_tmp))
		{
			return $field_name . " IN ('') ";
		}
		else
		{
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}

/**
 * array_column()函数兼容低版本
 *
 * 获取二维数组中的元素
 *
 * @return void
 * @version 1.0.0 (2014-22-22)
 * @author Xiao5
 */

function i_array_column($input, $columnKey, $indexKey = null)
{
    if (! function_exists('array_column')) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
        $indexKeyIsNull = (is_null($indexKey)) ? true : false;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
        $result = array();
        foreach ((array) $input as $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && ! empty($tmp)) ? current($tmp) : null;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
            }
            if (! $indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && ! empty($key)) ? current($key) : null;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    } else {
        return array_column($input, $columnKey, $indexKey);
    }
}



/**
 *  获取一个模型
 *
 *  @author Garbin
 *  @param  string $model_name
 *  @param  array  $params
 *  @param  book   $is_new
 *  @return object
 */
function &m($model_name, $params = array(), $is_new = false)
{
	
	static $models = array();
	$result = false;
	$model_hash = md5($model_name . var_export($params, true));
	if ($is_new || !isset($models[$model_hash]))
	{

		$model_file = PROJECT_PATH . 'includes/models/' . $model_name . '.model.php';

		if (is_file($model_file))
		{
			include_once($model_file);
			$model_name = ucfirst($model_name) . 'Model';
			if ($is_new)
			{
				$result =  $model_name($params, db());
			}else{
				$result = new $model_name($params, db());
			}
			
		}
		return $result;

	}

	return $models[$model_hash];
}

/**
 *  获取一个模型---连接的是ucenter数据库
 *
 *  @author Garbin
 *  @param  string $model_name
 *  @param  array  $params
 *  @param  book   $is_new
 *  @return object
 */
function &um($model_name, $params = array(), $is_new = false)
{

	static $models = array();
	$model_hash = md5($model_name . var_export($params, true));
	if ($is_new || !isset($models[$model_hash]))
	{

		$model_file = PROJECT_PATH . 'includes/models/' . $model_name . '.model.php';

		if (!is_file($model_file))
		{
			return false;
		}
		include_once($model_file);
		$model_name = ucfirst($model_name) . 'Model';
		if ($is_new)
		{
			return new $model_name($params, udb());
		}
		$models[$model_hash] = new $model_name($params, udb());
	}

	return $models[$model_hash];
}

/**
 * 创建MySQL数据库对象实例
 *
 * @author  wj
 * @return  object
 */
function &db()
{

	static $db = null;
	if ($db === null)
	{
		$cfg = parse_url(DB_CONFIG);

		if ($cfg['scheme'] == 'mysql')
		{
			if (empty($cfg['pass']))
			{
				$cfg['pass'] = '';
			}
			else
			{
				$cfg['pass'] = urldecode($cfg['pass']);
			}
			$cfg ['user'] = urldecode($cfg['user']);

			if (empty($cfg['path']))
			{
				trigger_error('Invalid database name.', E_USER_ERROR);
			}
			else
			{
				$cfg['path'] = str_replace('/', '', $cfg['path']);
			}

			$charset = (CHARSET == 'utf-8') ? 'utf8' : CHARSET;
			$db = new ec_cls_mysql();
			$db->cache_dir = ROOT_PATH. '/temp/query_caches/';
			
			$db->connect($cfg['host']. ':' .@$cfg['port'], $cfg['user'],
					$cfg['pass'], $cfg['path'], $charset);
		}
		else
		{
			trigger_error('Unkown database type.', E_USER_ERROR);
		}
	}

	return $db;
}


/**
 * 创建MySQL数据库对象实例 ----连接的是ucenter数据库
 *
 * @author  wj
 * @return  object
 */
function &udb()
{

	static $db = null;
	if ($db === null)
	{
		$cfg = parse_url(UDB_CONFIG);

		if ($cfg['scheme'] == 'mysql')
		{
			if (empty($cfg['pass']))
			{
				$cfg['pass'] = '';
			}
			else
			{
				$cfg['pass'] = urldecode($cfg['pass']);
			}
			$cfg ['user'] = urldecode($cfg['user']);

			if (empty($cfg['path']))
			{
				trigger_error('Invalid database name.', E_USER_ERROR);
			}
			else
			{
				$cfg['path'] = str_replace('/', '', $cfg['path']);
			}

			$charset = (CHARSET == 'utf-8') ? 'utf8' : CHARSET;
			$db = new ec_cls_mysql();
			$db->cache_dir = ROOT_PATH. '/temp/query_caches/';
			$db->connect($cfg['host']. ':' .$cfg['port'], $cfg['user'],
					$cfg['pass'], $cfg['path'], $charset);
		}
		else
		{
			trigger_error('Unkown database type.', E_USER_ERROR);
		}
	}

	return $db;
}

/**
 * 获得当前的域名
 *
 * @return  string
 */
function get_domain()
{
	/* 协议 */
	$protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		/* 端口 */
		if (isset($_SERVER['SERVER_PORT']))
		{
			$port = ':' . $_SERVER['SERVER_PORT'];

			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
			{
				$port = '';
			}
		}
		else
		{
			$port = '';
		}

		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'] . $port;
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'] . $port;
		}
	}

	return $protocol . $host;
}

//获得分类名称
function getCateName($cate_id)
{
	if ($cate_id)
	{
		$cate_ids = explode(',', $cate_id);
		$g_mod = m('gcategory');
		$cate_name = '';
		foreach ($cate_ids as $v)
		{
			$g_info = $g_mod->get($v);
			if ($g_info)
			{
				$cate_name .= $g_info['cate_name'].' ';
			}
		}
	}
	else
	{
		return '';
	}
	return trim($cate_name);
}

/*通过uid获得会员信息*/
function getUinfoByUid($uid)
{
	$m = m('member');
	$m_info = $m->get($uid);
	/* if ($m_info['photoUrl'])
	{
		$m_info['photoUrl'] = SITE_URL.'upload_user_photo/user/original/'.$uid."_160.png";
	} */
    //    获取用户等级
    $lv_mod =& m('memberlv');
    $lvs = $lv_mod->get(array(
        'fields' => "name,experience",
        'conditions' => "lv_type='supplier' and member_lv_id=".$m_info['member_lv_id'],
    ));

    $m_info['now_member_lv_id'] = $m_info['member_lv_id'];
    $m_info['now_member_lv_name'] = $lvs['name'];
	return $m_info;
}

/*通过会员的photoUrl字段格式化会员的图片地址*/
function getPhotoUrl($photoUrl,$boolen=TRUE)
{
	if ($boolen)
	{
		return SITE_URL."/upload_user_photo/user/original/".$photoUrl."_160.png";
	}
	else
	{
		return ROOT_PATH."/upload_user_photo/user/original/".$photoUrl.".png";
	}
}

/*通过region_id获得地区信息*/
function getRegionName($region)
{
	$m = m('region');
	$region_info = $m->get($region);
	if ($region_info)
	{
		return $region_info['region_name'];
	}
	else
	{
		return '';
	}


}

/*分割sql语句*/
function split_arr_sql($arr,$keyName = ''){
	$str = "";
	if(!$keyName){
		foreach($arr as $k=>$v){
			$str .= "'" . $v . "',";
		}
	}else{
		foreach($arr as $k=>$v){
			$str .= "'" .$v[$keyName] . "',";
		}
	}
	return substr($str, 0 , strlen($str) - 1);
}




function getSpeUrl($img_url,$boolen=true)
{
	if ($boolen)
	{
		return SITE_URL."/data/files/special/".$img_url;
	}
	else
	{
		return ROOT_PATH."/data/files/special/".$img_url;
	}

}



/**
 * 生成二维码
 * @param  $type	类型 ：goods商品图|...
 * @param  $id		文件名称加密id
 * @param  $text	生成 文字或是链接
 * @param  $size	1:33*33;2:66*66;3:99*99;4:132*132;10:400*400
 * @return 没有返回值
 */
function Qrcode($type='goods',$id=1000,$text='http://rctailor.ec51.com.cn/', $avatar=''){
//
//	/* 引用phparcode */
//	include (ROOT_PATH.'/phpqrcode/full/qrlib.php');
//	/* 定义二维码图片存放路径 */
//	//define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/phpqrcode/image/');
//
//	$errorCorrectionLevel = 'H';
//// 	$matrixPointSize = 4;
//
//	foreach (array(1,2,3,4,10) as $size){
//		$matrixPointSize = $size;
//
//		$tempDir = EXAMPLE_TMP_SERVERPATH;
//		$codeContents = $id;
//		/* 扩展 类型 ：goods 商品id */
//		$fileName = $type."/".$type.'_'.md5($codeContents).'_'.$size.'.png';
//		$pngAbsoluteFilePath = $tempDir.$fileName;
//
//		/**
//		 * 第一个参数$text，就是上面代码里的URL网址参数，
//		 * 第二个参数$outfile默认为否，不生成文件，只将二维码图片返回，否则需要给出存放生成二维码图片的路径
//		 * 第三个参数$level默认为L，这个参数可传递的值分别是L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）。
//		 * 			这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比。利用二维维码的容错率，我们可以将头像放置在生成的二维码图片任何区域。
//		 * 第四个参数$size，控制生成图片的大小，默认为4
//		 * 第五个参数$margin，控制生成二维码的空白区域大小
//		 * 第六个参数$saveandprint，保存二维码图片并显示出来，$outfile必须传递图片路径
//		 */
//		@ecm_mkdir(EXAMPLE_TMP_SERVERPATH.'/'.$type);
//
//		@chmod($fileName, 0777);
//
//		if(empty($avatar)) {
//            $logo = $avatar;
//		}
//
//		$QR = $tempDir.$fileName;
//
//		if(file_exists($QR)){ // 已存在删除文件
//			unlink($QR);
//		}
//
//		QRcode::png($text, $pngAbsoluteFilePath, $errorCorrectionLevel, $matrixPointSize);
//
//		/* 二维码合成 logo */
//		if($logo !== FALSE){
//		$QR = imagecreatefromstring(file_get_contents($QR));
//		$logo = imagecreatefromstring(file_get_contents($logo));
//		$QR_width = imagesx($QR);
//		$QR_height = imagesy($QR);
//		$logo_width = imagesx($logo);
//		$logo_height = imagesy($logo);
//		$logo_qr_width = $QR_width / 5;
//		$scale = $logo_width / $logo_qr_width;
//		$logo_qr_height = $logo_height / $scale;
//		$from_width = ($QR_width - $logo_qr_width) / 2;
//		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
//		}
//
//		@imagepng($QR,$tempDir.$fileName);
//// 		return SITE_URL.'/phpqrcode/image/'.$fileName;
//
//// 		return false;
//	}
}

/**
 * 获取二维码 图片路径
 * @param  $type	二维码类型
 * @param  $id		标记id
 * @param  $size	尺寸
 * @return string
 */
function getQrcodeImage($type,$id,$size){
	$size = empty($size) ? 4 : $size;
	//define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/phpqrcode/image/');
	$fileName = $type.'/'.$type.'_'.md5($id).'_'.$size.'.png';
	if(file_exists(EXAMPLE_TMP_SERVERPATH.$fileName)){ // 已存在删除文件
		return $fileName;
	}
	return '';
}





/**
 * 创建目录（如果该目录的上级目录不存在，会先创建上级目录）
 * 依赖于 ROOT_PATH 常量，且只能创建 ROOT_PATH 目录下的目录
 * 目录分隔符必须是 / 不能是 \
 *
 * @param   string  $absolute_path  绝对路径
 * @param   int     $mode           目录权限
 * @return  bool
 */
function ecm_mkdir($absolute_path, $mode = 0777)
{
	if (is_dir($absolute_path))
	{
		return true;
	}

	$root_path      = ROOT_PATH;
	$relative_path  = str_replace($root_path, '', $absolute_path);
	$each_path      = explode('/', $relative_path);
	$cur_path       = $root_path; // 当前循环处理的路径
	foreach ($each_path as $path)
	{
		if ($path)
		{
			$cur_path = $cur_path . '/' . $path;
			if (!is_dir($cur_path))
			{
				if (@mkdir($cur_path, $mode))
				{
					fclose(fopen($cur_path . '/index.htm', 'w'));
				}
				else
				{
					return false;
				}
			}
		}
	}

	return true;
}














function DZLogin($uname,$ps){
	if(!$uname || !$ps)return 0;
	$ul =
	'http://bbs.0708.com/member.php'.
	'?mod=logging&action=login&loginsubmit=yes&infloat=yes&lssubmit=yes&inajax=1';

	$arr = array(
			'fastloginfield' =>	'13522536459',
			'handlekey' =>	'ls',
			'password'=>	$ps,
			'quickforward'=>	'yes',
			'username'=>	$uname,
	);

	$rs = curl_http_header($ul,$arr);
// 	var_dump($rs);echo "<Br/>";exit;
	// list($header, $body) = explode("\r\n\r\n", $rs);
	// var_dump($header);exit;
	$matches = array();
	preg_match_all("/set\-cookie:([^\r\n]*)/i", $rs, $matches);
	foreach ($matches[0] as $k=>$v){
		$v = str_replace(' ', '', $v);
		$v = str_replace('Set-Cookie:', '', $v);

		$arr = explode(';', $v);
		// 	foreach ($arr as $k2=>$v2){
		$tmp = explode('=', $arr[0]);
		if($tmp[0] && $tmp[1]){
			$tmp[1] = urldecode($tmp[1]);
// 			echo $tmp[0] . " " . $tmp[1] ."<br/>";
			setcookie( $tmp[0], $tmp[1] , time() + 3600 , '/', '.0708.com');
		}
		// 	}
	}
}

function curl_http($url,$post_data){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_URL,$url);

	//传递一个作为HTTP “POST”操作的所有数据的字符串。
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	ob_start();
	try {
		curl_exec($ch);
	}catch (Exception $e){
		var_dump($e);
	}
	$result = ob_get_contents() ;
	ob_end_clean();

	return  $result;
}

function getSoapClient($class,$method,$para){
	$AppID = 101;
	$key = 'gamebean';
	$r = rand(1, 10000);
	try{
		$wsdlUrl = "http://dev.ecm.com/soaapi/soap/WSDL/SoapService.wsdl";
		$SoapUrl = "http://dev.ecm.com/soaapi/soap/ServiceSoap.php";

		$AppTime = date("U");
		$AppCtid = md5($key.$AppID.$AppTime);
		$client = new SoapClient( $wsdlUrl ,array( 'trace' => 1 ) );
		//头验证信息
		$headInfo= array(
				'AppID'=>$AppID,
				'AppCtid'=>$AppCtid,
				'AppTime'=>$AppTime,
		);
		$headers = new SoapHeader($SoapUrl,"Authorized",array($headInfo),false, SOAP_ACTOR_NEXT);
		// 	echo "Request :<br/>".htmlspecialchars($client->__getLastRequest())."<br/>";
		//  echo "Response :<br/>".htmlspecialchars($client->__getLastResponse())."<br/>";
		$client->__setSoapHeaders(array($headers));
		$result = $client->Router($class,$method,$para);
		return $result;
	}catch (Exception $e){
		var_dump($e->getMessage());
	}
}

function __autoload($class){
	include_once $class .".class.php";
}
function key_turn_arr($arr){
	$rs = array();
	foreach($arr as $k=>$v){
		$rs[] = $k;
	}
	return $rs;
}

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name, $method='', $args=array()) {
	static $_instance = array();
	$identify = empty($args) ? $name . $method : $name . $method . to_guid_string($args);
	if (!isset($_instance[$identify])) {
		if (class_exists($name)) {
			$o = new $name();
			if (method_exists($o, $method)) {
				if (!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				} else {
					$_instance[$identify] = $o->$method();
				}
			}
			else
				$_instance[$identify] = $o;
		}
		else
			ExceptionFrame::halt("new class:". $name);
// 			halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
	}
	return $_instance[$identify];
}
//切分
function split_arr($arr,$keyName = ''){
	$str = "";
	if(!$keyName){
	foreach($arr as $k=>$v){
			$str .= $v . ",";
		}
	}else{
		foreach($arr as $k=>$v){
			$str .= $v[$keyName] . ",";
		}
	}
	return substr($str, 0 , strlen($str) - 1);
}

//
function arr_in_arr($arr,$key,$node){
	$f = 0;
	foreach($arr as $k=>$v){
		if($node  == $v[$key]){
			$f = 1;
			break;
		}
	}

	return $f;
}
// 获取和设置语言定义(不区分大小写)
function L($name=null, $value=null) {
	static $_lang = array();
	// 空参数返回所有定义
	if (empty($name))
		return $_lang;
	// 判断语言获取(或设置)
	// 若不存在,直接返回全大写$name
	if (is_string($name)) {
		$name = strtoupper($name);
		if (is_null($value))
			return isset($_lang[$name]) ? $_lang[$name] : $name;
		$_lang[$name] = $value; // 语言定义
		return;
	}
	// 批量定义
	if (is_array($name))
		$_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
}

// 获取配置值
function C($name=null, $value=null) {
	static $_config = array();
	// 无参数时获取所有
	if (empty($name))   return $_config;
	// 优先执行设置获取或赋值
	if (is_string($name)) {
		if (!strpos($name, '.')) {
			$name = strtolower($name);
			if (is_null($value))
				return isset($_config[$name]) ? $_config[$name] : null;
			$_config[$name] = $value;
			return;
		}
		// 二维数组设置和获取支持
		$name = explode('.', $name);
		$name[0]   =  strtolower($name[0]);
		if (is_null($value))
			return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
		$_config[$name[0]][$name[1]] = $value;
		return;
	}
	// 批量设置
	if (is_array($name)){
		return $_config = array_merge($_config, array_change_key_case($name));
	}
	return null; // 避免非法参数
}
// 获取客户端IP地址
function get_client_ip() {
	static $ip = NULL;
	if ($ip !== NULL) return $ip;
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos =  array_search('unknown',$arr);
		if(false !== $pos) unset($arr[$pos]);
		$ip   =  trim($arr[0]);
	}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
	return $ip;
}
//判断长度
function slen($value,$max,$min = 1,$unicode = 'utf8'){
	$str = mb_strlen($value , $unicode);
	if( $str > $max || $str < $min){
		return 0;
	}

	return 1;
}
//GET POST REQUEST
//function _g()
function _g($name='', $type = '')
{
	 /**$contents = '{"passWord":"123456","phoneNum":"13146294015"}';
	// 	$contents = '{"pageIndex":1,"keyWord":"因为从有到无","pageSize":20}';
	//$contents = '{"consigneeId":"2","delivery":0,"payWay":0,"remark":"18610352845","goodsCountList":[{"quantity":1,"specId":"19","goodsId":"18"},{"quantity":1,"specId":"20","goodsId":"18"},{"quantity":2,"specId":"21","goodsId":"19"}],"token":"ec046e44f1cb60518050cf601a89f101"}';
	//$contents = '{"token":"ec046e44f1cb60518050cf601a89f101","orderId":5,"cardCode":222}';
	//$contents = @file_get_contents("php://input");
		if (empty($contents)) return false;
	$ret = json_decode($contents);
	**/

	/* 数据过滤 */
        if (!get_magic_quotes_gpc())
        {
        	/****$_GET、$_POST、$_COOKIE、$_REQUEST****/
        	$_GET  		= addslashes_deep($_GET,true);
        	$_POST 		= addslashes_deep($_POST,true);
        	$_COOKIE   	= addslashes_deep($_COOKIE,true);
        	$_REQUEST  	= addslashes_deep($_REQUEST,true);

        }


	global $json;
	if(!$name){
		if($_POST)	return json_decode($json->encode($_POST));
		else if($_GET) return json_decode($json->encode($_GET));
		else if($_REQUEST) return json_decode($json->encode($_REQUEST));
	}

	if (isset($_POST[$name]))      $ret = $_POST[$name];
	elseif (isset($_GET[$name]))   $ret = $_GET[$name];
	elseif (isset($_REQUEST[$name]))   $ret = $_REQUEST[$name];
	else $ret = false;
	if ($ret !== false && $type != '') {
		if ($type == 'int') $ret = intval($ret);
		elseif ($type == 'str') $ret = strval($ret);
		else settype($ret, $type);
	}

	return $ret;
}


//发送HTTP状态
function send_http_status($code) {
	static $_status = array(
	// Success 2xx
			200 => 'OK',
			// Redirection 3xx
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ',  // 1.1
			// Client Error 4xx
			400 => 'Bad Request',
			403 => 'Forbidden',
			404 => 'Not Found',
			// Server Error 5xx
			500 => 'Internal Server Error',
			503 => 'Service Unavailable',
	);
	if(isset($_status[$code])) {
		header('HTTP/1.1 '.$code.' '.$_status[$code]);
		// 确保FastCGI模式下正常
		header('Status:'.$code.' '.$_status[$code]);
	}
}
//取得SMARTY实例
function getSmartyObject($path = ''){
	include_once SMART_URL . "Smarty.class.php";
	$SmartyClass = Singleton('Smarty');


	if(!$path){
		$SmartyClass->setTemplateDir("./view");
		$SmartyClass->setCompileDir("./view_c");
	}else{
		$SmartyClass->setTemplateDir($path);
	}

	$SmartyClass->debugging = false;
	$SmartyClass->caching = false;

	return $SmartyClass;

}
//判断是否有权限
function file_mode_info($file_path) {
	/* 如果不存在，则不可读、不可写、不可改 */
	if (!file_exists($file_path)) return false;
	$mark = 0;
	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
		/* 测试文件 */
		$test_file = $file_path . '/cf_test.txt';
		/* 如果是目录 */
		if (is_dir($file_path)){
			/* 检查目录是否可读 */
			$dir = @opendir($file_path);
			if ($dir === false){
				return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
			}
			if (@readdir($dir) !== false){
				$mark ^= 1; //目录可读 001，目录不可读 000
			}
			@closedir($dir);
			/* 检查目录是否可写 */
			$fp = @fopen($test_file, 'wb');
			if ($fp === false){
				return $mark; //如果目录中的文件创建失败，返回不可写。
			}
			if (@fwrite($fp, 'directory access testing.') !== false){
				$mark ^= 2; //目录可写可读011，目录可写不可读 010
			}
			@fclose($fp);
			@unlink($test_file);
			/* 检查目录是否可修改 */
			$fp = @fopen($test_file, 'ab+');
			if ($fp === false){
				return $mark;
			}
			if (@fwrite($fp, "modify test.\r\n") !== false){
				$mark ^= 4;
			}
			var_dump($mark);
// 			@fclose($fp);
// 			/* 检查目录下是否有执行rename()函数的权限 */
// 			if (@rename($test_file, $test_file) !== false){
// 				$mark ^= 8;
// 			}
			@unlink($test_file);
		}elseif (is_file($file_path)){/* 如果是文件 */
			/* 以读方式打开 */
			$fp = @fopen($file_path, 'rb');
			if ($fp)
			{
				$mark ^= 1; //可读 001
			}
			@fclose($fp);
			/* 试着修改文件 */
			$fp = @fopen($file_path, 'ab+');
			if ($fp && @fwrite($fp, '') !== false)
			{
				$mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
			}
			@fclose($fp);
			/* 检查目录下是否有执行rename()函数的权限 */
			if (@rename($test_file, $test_file) !== false){
				$mark ^= 8;
			}
		}
	}else{
		clearstatcache();
		if (@is_readable($file_path)){
			$mark ^= 1;
		}
		if (@is_writable($file_path)){
			$mark ^= 14;
		}
	}

	return $mark;
}
//取得数据库实例
function getDb($dbName){
	static $dbLink = array();
	if(!$dbLink[$dbName]){
		foreach($GLOBALS['db_config'] as $k=>$v){
			if($k == $dbName){
				$f = 1;
				$config = $v;
			}
		}
		if(!$f){
			ExceptionFrame::throwException('DB_config error','no');
		}
// 		include 'Model.class.php';
		$db = new Model($config);
		$dbLink[$dbName] =  $db;
	}
	return $dbLink[$dbName];
}
//取得数据库实例
function getDb2($dbName){
	static $dbLink = array();
	if(!$dbLink[$dbName]){
		foreach($GLOBALS['db_config'] as $k=>$v){
			if($k == $dbName){
				$f = 1;
				$config = $v;
			}
		}
		if(!$f){
			ExceptionFrame::throwException('DB_config error','no');
		}
		// 		include 'Model.class.php';
		$db = new DbTest($config);
		$dbLink[$dbName] =  $db;
	}
	return $dbLink[$dbName];
}
//单实例模式
function Singleton ($className){
	static $_instens = array();
	if(!isset($_instens[$className])){
		$_instens[$className] = new $className;
	}
	return $_instens[$className];
}
// 根据PHP各种类型变量生成唯一标识号
function to_guid_string($mix) {
	if (is_object($mix) && function_exists('spl_object_hash')) {
		return spl_object_hash($mix);
	} elseif (is_resource($mix)) {
		$mix = get_resource_type($mix) . strval($mix);
	} else {
		$mix = serialize($mix);
	}
	return md5($mix);
}
/**
 * 工厂模式实例化模块
 * @param id: 模块ID
 * @param instanceID: 对象ID,如uid-用户ID， tid-球队ID等
 * return class
 */
function getElementObject($id, $instanceID = NULL) {
	if (array_key_exists($id, self::$modules)) {
		$module = self::$modules[$id];
		if (class_exists($module[2])) {
			return new $module[2]($id, $instanceID, $module[0], $module[1], $module[4]);
		} else {
			error_log("Ftonline Element Class for ID $id does not exist");
		}
	} else {
		error_log("Ftonline Module ID $id does not exist!");
	}
	return false;
}

// 设置和获取统计数据
function N($key, $step=0) {
	static $_num = array();
	if (!isset($_num[$key])) {
		$_num[$key] = 0;
	}
	if (empty($step))
		return $_num[$key];
	else
		$_num[$key] = $_num[$key] + (int) $step;
}

// 记录和统计时间（微秒）
function G($start,$end='',$dec=4) {
	static $_info = array();
	if(is_float($end)) { // 记录时间
		$_info[$start]  =  $end;
	}elseif(!empty($end)){ // 统计时间
		if(!isset($_info[$end])) $_info[$end]   =  microtime(TRUE);
		return number_format(($_info[$end]-$_info[$start]),$dec);
	}else{ // 记录时间
		$_info[$start]  =  microtime(TRUE);
	}
}
// 显示运行时间
function showTime() {
	G('beginTime',$GLOBALS['beginTime']);
	$showTime   =   'Process: '.G('beginTime','viewEndTime').'s ';
	// 显示详细运行时间
// 	$showTime .= '( Load:'.G('beginTime','loadTime').'s Init:'.G('loadTime','initTime').'s Exec:'.G('initTime','viewStartTime').'s Template:'.G('viewStartTime','viewEndTime').'s )';
	// 显示数据库操作次数
	$showTime .= ' | DB :'.N('db_query').' queries '.N('db_write').' writes ';
	// 显示内存开销
	$showTime .= ' | UseMem:'. number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024).' kb';
	$showTime .= ' | LoadFile:'.count(get_included_files());
	$fun  =  get_defined_functions();
	$showTime .= ' | CallFun:'.count($fun['user']).','.count($fun['internal']);
	return $showTime;
}
/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
	$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
	if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
	{
		if (preg_match($chars, $user_email))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
/**
 * 对象转数组
 * @param unknown_type $o
 * @return void|array
 */
function objToArr($o){
    $o=(array)$o;
    foreach($o as $k=>$v){
        if( gettype($v)=='resource' ) return;
        if( gettype($v)=='object' || gettype($v)=='array' )
            $o[$k]=(array)objToArr($v);
    }
    return $o;
}



function getLevel($lid){
    $db = &db();

    $sql = "select * from rc_member_lv where member_lv_id = ".$lid;
    return $db->getRow($sql);
}

function getAvatarByFile($name,$size = ""){
    if(!$name)
        return AVATAR_DEF;

    if($size == '162'){
        $path = "162";
    }elseif($size == '20'){
        $path = "20";
    }elseif($size == '48'){
        $path = "48";
    }else{
        $path = "162";
    }

    $src= get_domain() . "/upload_user_photo/avatar/".$path."/".$name;
    return $src;
}

function getAreaByRid($rid){
    $db = &db();

    $sql = "select * from rc_region where region_id = ".$rid;
    return $db->getRow($sql);
}

function getPhotoDetailLink($id,$cate){
    if(!$id)
        return "";

    if($cate == 1)
        $url = "/index.php/club-personaldesign-{$id}.html";
    else
        $url = "/index.php/club-streetinfo-{$id}.html";

    return $url;
}
function getDesignUrl($file_name,$size = '500',$type = 'big'){
    if(!$file_name)
        return getDefAlbumPic($type);

    if($size == '500'){
        $path = "520x685";
    }elseif($size ==  '200'){
        $path = "235x315";
    }else{
        $path = "original";
    }


    $src= get_domain(). "/upload_user_photo/sheji/$path/".$file_name;

    return $src;
}

function getComment($uid,$comment_id,$cate,$limit = '' ,$desc = 'add_time'){
    $arr = array('personaldesign'=>'个人设计详情页','streetinfo'=>'街拍详情页','order_goods'=>'基本款订单评论', 'dis' => "主题系列",'serve'=>'服务点');

    $db = &db();

    if(!$uid || !$comment_id || !$cate)
        exit(" func <getComment>: uid or comment_id or cate  is null");

    $where = " uid = $uid and comment_id = $comment_id and cate = '$cate' ";

    if($limit)
        $limit = " limit ".$limit;

    $sql = "select * from rc_comments where $where  order by $desc desc $limit ";

    $rs = $db->getAll($sql);
    return $rs;
}
function getCommentByGid($gid,$comment_id = 0 ,$cate,$limit = ''){
    $db = &db();

    $where = " goods_id = $gid and cate = '$cate' ";
    if($comment_id)
        $where .=  "  and comment_id = $comment_id ";
    if($limit)
        $limit = " limit ".$limit;

    $sql = "select * from rc_comments where $where $limit ";
    $rs = $db->getAll($sql);
    return $rs;
}
function getCommentPage($comment_id,$cate,$page = 1,$desc = 'add_time'){
    $arr = array('personaldesign'=>'个人设计详情页','streetinfo'=>'街拍详情页');

    $db = &db();
    $where = " comment_id = $comment_id and cate = '$cate' ";

    $sql = "select count(*) as total from rc_comments where $where";
    $total = $db->getRow($sql);
    if($total['total']){
        include 'includes/libraries/pageSimple.lib.php';
        $page = new PageSimple($total['total'] , $page,5);
        $page->execPage();

        $sql = "select * from rc_comments where $where  order by $desc desc limit ".$page->mLimit[0]." , " .$page->mLimit[1];
        $rs = $db->getAll($sql);
        $rs = array('data'=>$rs,'page'=>$page);
    }

    return $rs;

}

function getCommentwidthoutuid($comment_id,$cate,$limit = '' ,$desc = 'add_time'){
    $arr = array('personaldesign'=>'个人设计详情页','streetinfo'=>'街拍详情页','order_goods'=>'基本款订单评论', 'dis' => "主题系列",'serve'=>'服务点');

    $db = &db();

    if( !$comment_id || !$cate)
        exit(" func <getComment>: uid or comment_id or cate  is null");

    $where = "  comment_id = $comment_id and cate = '$cate' ";

    if($limit)
        $limit = " limit ".$limit;

    $sql = "select * from rc_comments where $where  order by $desc desc $limit ";

    $rs = $db->getAll($sql);
    return $rs;
}
function getCameraUrl($file_name,$size = '500',$type = 'big'){
    if(!$file_name)
        return getDefAlbumPic($type);

    if($size == '500'){
        $path = "520x685";
    }elseif($size ==  '200'){
        $path = "235x315";
    }else{
        $path = "original";
    }
    $src= get_domain() . "/upload_user_photo/jiepai/$path/".$file_name;
    return $src;
}

function checkCate($cate)
{
	if(!$cate)
		return false;

	$paction_mod = m('paction');
	$arr = $paction_mod->find();
	$f = 0;
	$key_arr =null;
	foreach($arr as $k=>$v){

		if($v['cate'] == $cate){
			$f = 1;
			$key_arr  = $v['num'];
			break;
		}
	}

	if(!$f)
		exit('cate not in arr');

	return $key_arr;
}

function setPoint($uid,$cate,$author = 'system',$msg = '',$way = 'pc',$auto_id = 0){
	$num = checkCate($cate);

	if(!$uid)
		return 0;

	$m = m('pointlog');
	$data = array(
			'num'=>$num,
			'uid'=>$uid,
			'cate'=>$cate,
			'add_time'=>microtime_float(),
			'msg'=>$msg,
			'author'=>$author,
	);

	if($auto_id)
		$data['auto_id'] = $auto_id;

	$m->add($data);
	$db = &db();

	$sql = "update ecm_member set point = point + ".$num." where user_id = ".$uid;
	$db->query($sql);
	return outinfo('ok',0 , $way);
}


function setCoin($uid,$num,$opt,$cate,$author = 'system',$msg,$way = 'pc'){
	$cate_arr = checkCate($cate);
	if(!$uid)
		return 0;

	// $cate = order 订单;
	$m = m('coin_log');
	$data = array(
			'num'=>$num,
			'uid'=>$uid,
			'cate'=>$cate,
			'add_time'=>time(),
			'msg'=>$msg,
			'author'=>$author,
			'opt'=>$opt,
			'type'=>$cate_arr['type'],
	);
	$rs = $m->add($data);
	$db = &db();
	if($opt != 'add'){
		$num = - $num;
	}

	$sql = "update rc_member set coin = coin + ".$num." where user_id = ".$uid;
	$db->query($sql);

	$_SESSION['user_info']['coin'] = $_SESSION['user_info']['coin'] + $num;

	return outinfo('ok',0 , $way);
}

function setExpe($uid,$num,$opt,$cate,$author = 'system',$msg = '' ,$way = 'pc'){
	$m = m('expe_log');
	$data = array(
			'num'=>$num,
			'uid'=>$uid,
			'cate'=>$cate,
			'add_time'=>time(),
			'msg'=>$msg,
			'author'=>$author,
			'opt'=>$opt,
	);
	$m->add($data);
	$db = &db();
	if($opt != 'add'){
		$num = - $num;
	}

	$sql = "update rc_member set coin = experience + ".$num." where user_id = ".$uid;
	$db->query($sql);

	$member_lv_mod =& m('memberlv');
	$member_lv_mod->auto_level($uid,'member',$num);

	$_SESSION['user_info']['experience'] = $_SESSION['user_info']['experience'] + $num;

	return outinfo('ok',0 , $way);

}




function getLikeByUids($uids ,$like_id , $cate){
    $db = &db();
    $sql = " select * from rc_like where uid in ( $uids ) and cate = '$cate' and like_id =  $like_id";
    $like = $db->getAll($sql);
    $uids_arr = explode(",", $uids);
    if($like){
        $rs = array();
        foreach($uids_arr as $k=>$v){
            $f = 0;
            foreach($like as $k2=>$v2){
                if($v == $v['uid']){
                    $f = 1;
                    break;
                }
            }

            if($f)
                $rs[$v] = 1;
            else
                $rs[$v] = 0;
        }
    }else{
        foreach($uids_arr as $k=>$v){
            $rs[$v] = 0;
        }
    }

    return $rs;
}




/*批处理上传图片  add liliang*/
function pro_img_multi($file_name,$RESIZEWIDTH=100,$RESIZEHEIGHT=100,$FILENAME="image.thumb"){
	if($file_name['size']){
		if($file_name['type'] == "image/pjpeg" || $file_name['type'] == 'image/jpeg' ){
// 		echo $file_name['tmp_name'];exit;
			$im = imagecreatefromjpeg($file_name['tmp_name']);
		}elseif($file_name['type'] == "image/png"){
			$im = imagecreatefrompng($file_name['tmp_name']);
		}elseif($file_name['type'] == "image/gif"){
			$im = imagecreatefromgif($file_name['tmp_name']);
		}else{
			exit('file error ');
		}

		if($im){
			if(file_exists("$FILENAME.jpg")){
				unlink("$FILENAME.jpg");
			}
			ResizeImage($im,$RESIZEWIDTH,$RESIZEHEIGHT,$FILENAME);
			ImageDestroy ($im);
		}
	}
}

function ResizeImage($im,$maxwidth,$maxheight,$name){
    $width = imagesx($im);
    $height = imagesy($im);
// 	echo $maxwidth . " " .$maxheight . " " .$width ." " . $height;
    if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)){
        if($maxwidth && $width > $maxwidth){
            $widthratio = $maxwidth/$width;
            $RESIZEWIDTH=true;
        }
        if($maxheight && $height > $maxheight){
            $heightratio = $maxheight/$height;
            $RESIZEHEIGHT=true;
        }
        if($RESIZEWIDTH && $RESIZEHEIGHT){
            if($widthratio < $heightratio){
                $ratio = $widthratio;
            }else{
                $ratio = $heightratio;
            }
        }elseif($RESIZEWIDTH){
            $ratio = $widthratio;
        }elseif($RESIZEHEIGHT){
            $ratio = $heightratio;
        }
        $newwidth = $width * $ratio;
        $newheight = $height * $ratio;
        if(function_exists("imagecopyresampled")){
// 			echo $newwidth . " " . $newheight;
            $newim = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }else{
            $newim = imagecreate($newwidth, $newheight);
            imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }
        ImageJpeg ($newim,$name );
        ImageDestroy ($newim);
    }else{
        ImageJpeg ($im,$name );
    }
}
function delUserPhoto($pic_id){
    $m_photo = m('userphoto');
    $m_album =  m('album');
    $db = &db();
    $photo = $m_photo->getById($pic_id);
    if(!$photo)
        return 0;

    $sql = "update rc_member set pic_num = pic_num - 1 where user_id = ".$photo['uid'];
    $db->query($sql);

    $rs = $m_photo->delById($pic_id);
    if($photo['album_id']){
        $album = $m_album->getByID($photo['album_id']);
        if($album){
            if($album['top_url'] == $photo['url']){
                $m_album->edit($photo['album_id'],array('top_url'=>''));
            }
            $sql = "update rc_album set pic_num = pic_num - 1 where id = ".$photo['album_id'];
            $db->query($sql);
        }
    }
    $_SESSION['user_info']['pic_num'] = $_SESSION['user_info']['pic_num'] - 1;
    return 1;
}

//ns add 晒单
function getSingleUrl($file_name,$size = '500',$type = 'big'){
    if(!$file_name)
        return getDefAlbumPic($type);

    if($size == '500'){
        $path = "520x685";
    }elseif($size ==  '200'){
        $path = "235x315";
    }else{
        $path = "original";
    }
    $src= LOCALHOST1 . "/upload_user_photo/shaidan/$path/".$file_name;
    return $src;
}

/**
 * 价格格式处理（保留小数点后两位00）
 *
 * @param String $price 
 *
 * @access protected
 * @author xuganglong <781110641@qq.com>
 * @return void
 */
function _format_price_int($price = 0.00)
{
    return intval($price);
	return sprintf('%.2f',intval($price));
}


//  获取客户财富数据获取调用方法
//  @param $user_id 用户id   必填
//   @param $finance_sn 订单号或流水号 必填
//   @param $minus 1 FINANCE_GOODS_BUY(发货：商品)
//   								2 FINANCE_WITHDRAW_DEPOSIT（发货：提现）
//  								 3 FINANCE_CASH_RECHARGE（收款：现金充值）
//   								4 FINANCE_INCOME_BALANCE（收益转余额）
//  								 5 FINANCE_PAY_CASH（收款：现金）
//   								6 FINANCE_COIN_BUY（收款：麦富迪币购买） 必填
//   								7 FINANCE_BACKEND_RECHARGE （后台会员账户调整）
//   @param $trans_amount 本次交易金额 ，默认为0
//   @param $abstract  其他信息备注，默认为0,如收货人姓名:张三，余额消费:100，麦富迪币:100，现金：100（如果$minus=1,需要做这样的备注）
//   1用户在平台（APP和PC）购买实物商品的总额，统计节点：已发货
//   5购买商品包含：西服、西裤、衬衫、大衣、马甲、面料、料册等，统计节点：已付款
//  @return void
//  @version 1.0.0 (2015-11-13)
//  @author zxr

function get_client_finance($user_id,$finance_sn,$minus,$trans_amount=0,$abstract=array()){
	$cfinance_mod=&m('clientfinancedetail');
	if(in_array($minus, array(FINANCE_GOODS_BUY,FINANCE_WITHDRAW_DEPOSIT))){
		$data['type']=1;
		$data['mark']='-';
	}else if(in_array($minus,array(FINANCE_CASH_RECHARGE,FINANCE_INCOME_BALANCE,FINANCE_PAY_CASH,FINANCE_COIN_BUY,FINANCE_BACKEND_RECHARGE))){
		$data['type']=2;
		$data['mark']='+';
	}else{
		$data['type']=0;
		$data['mark']='未知操作';
	}

	
	$data['user_id']=!empty($user_id)?$user_id:'0';
	$data['finance_sn']=!empty($finance_sn)?$finance_sn:'0';
	$data['minus']=!empty($minus)?$minus:0;
	$data['add_time']=gmtime();
	$data['trans_amount']=$trans_amount;
	if($user_id){
		
		$finance_info=$cfinance_mod->get(array(
				'conditions'=>"user_id={$user_id} order by add_time DESC",				
		));
		if($finance_info){
			$data['start_balance']=$finance_info['end_balance'];
		}else{
			$data['start_balance']=0;
		}
		if($data['type']==1){
			$data['end_balance']=$data['start_balance']-$trans_amount;
		}else {
			$data['end_balance']=$data['start_balance']+$trans_amount;
		}
		
	}else {
		$data['start_balance']=0;
		if($data['type']==1){
			$data['end_balance']=$data['start_balance']-$trans_amount;
		}else if($data['type']==2){
			$data['end_balance']=$data['start_balance']+$trans_amount;
		}else{
			$data['end_balance']=$data['start_balance']+$trans_amount;
		}
		
	}
	$data['abstract']=!empty($abstract)?join(',',$abstract):'';
	if($data['type']==1){
		switch ($data['minus']){
			case 1:
				$data['abstract']="发货：,".$data['abstract'];
				break;
			case 2:
				$data['abstract']="发货：提现,".$data['abstract'];
				break;
		}
	}
	if($data['type']==2){
		switch ($data['minus']){
			case 3:
				$data['abstract']="收款：现金充值,".$data['abstract'];
				break;
			case 4:
				$data['abstract']="收益转余额,".$data['abstract'];//有税收 20%
				break;
			case 5:
				$data['abstract']="收款：现金,".$data['abstract'];
				break;
			case 6:
				$data['abstract']="收款：麦富迪币购买,".$data['abstract'];
				break;
			case 7:
				$data['abstract']="后台会员账户充值,".$data['abstract'];
				break;
/* 			case 7:
				$data['abstract']="收益转麦富迪币,".$data['abstract']; //没有税
				break; */
		}
	}
	if(!$data['abstract']){
		$data['abstract']=0;
	}
	if(!$user_id && !$minus){
		$data['abstract']='参数错误';
		$cfinance_mod->add($data);
		return false;
	}
	$res=$cfinance_mod->add($data);
	if($res){
		return true;
	}else{
		return false;
	}
}

/*
 *  发送消息接口
 *  @param $type 消息使用标记，最好由英文和下划线组成
 *  @param $user_ids 发送的用户id（数组格式，默认为空数组)
 *  @param $temp 对应消息内容模版里的smarty标签的数组
 *  @param $info_type 对应发送消息的类型，存入cf_usermessage,仅限信鸽使用，默认为0
 *  	1.交易| 2.评论晒单| 3.评论订单|4.顾客发需求|5.裁缝发作品、报价|13订单发货|12用户等级提升|14红包相关|15抵用券相关|16提现|17返修
 *  @return 二维数组包含消息标题和内容
 *  @author Daniel
 *   */
function send_message($type,$user_ids=array(),$temp=array(),$info_type=0){
	$message_mod=m('messagetemplate');
	$data=$message_mod->find(array(
			'conditions'  => "mt.mt_type='{$type}'",
			'field'=>'*',
			// 			'join'    => 'belongs_to_icategory',
	));
	if(empty($user_ids)){
		return false;
	}
	if(is_numeric($user_ids)){
		$user_ids=array(
				'0'=>$user_ids
		);
	}else if(is_string($user_ids) && strstr($user_ids, ',')){
		$user_ids=explode(',', $user_ids);
	}
	if($data){
		$tmp=array_values($data);
		$data=$tmp[0];
		//html_entity_decode函数有解析乱码的时候，而且用这个函数的时候还需要指定编码类型,有中文的时候，最好用 htmlspecialchars ，否则可能乱码
		$content=htmlspecialchars_decode(htmlspecialchars_decode($data['mt_content']));//双重转义
		if(!empty($temp)){
			foreach ($temp as $k=>$v){
				$content=str_replace("<{"."$".$k."}>",$v,$content);
			}
		}
		$content = preg_replace('/(<{\$+[a-zA-Z0-9_-]+}>)/','', $content);
		$parent_id=$data['parent_id'];
		$conditions=stripslashes($data['mt_send_member']);
		$title=$data['mt_title'];
		$is_special=$data['is_special'];
		$message_type=!empty($data['mt_code'])?$data['mt_code']:'none';
		$category=!empty($data['mt_module'])?$data['mt_module']:'none';
		/* var_dump($data);exit(); */

		if ($parent_id==1){//短信通知
			if($is_special){
				return strip_tags($content);
			}
			$result=0;
			$member=&m('member');
				
			$memberAll = $member->find(array(
					'conditions' => $conditions,
					'fields'     => 'user_id, user_name, user_token,serve_type,member_lv_id,phone_mob',
			));
			$send_members=array();
			foreach ($user_ids as $k=>$v){
				$res=0;
				if($memberAll[$v]){
					/* var_dump($memberAll[$v]); */
					if(validate_phone($memberAll[$v]['phone_mob'])){
						$res=Send_Sms($memberAll[$v]['phone_mob'], $category,$message_type,strip_tags($content));
					}
				}
				if($res){
					$result+=1;
				}
			}
		}else if(in_array($parent_id,array(2,3))){//系统通知
			/* var_dump(1);
			 echo '<hr/>'; */
			$result=push_app_message($conditions,$user_ids,$title,$content,$data['send_type'],$info_type);
		}
		//$conditions=stripslashes($data['mt_send_member'])
		/* var_dump(2);
		 echo '<hr/>'; */

		return $result;
	}else{
		return false;
	}
}
/*daniel 推送app系统消息 */
function push_app_message($conditions,$user_ids,$title,$content,$send_type,$info_type){
	/* 设置最长执行时间为10分钟 */
	@set_time_limit(600);
	$member=& m('member');

	$memberAll = $member->find(array(
			'conditions' => $conditions,
			'fields'     => 'user_id, user_name, user_token,serve_type,member_lv_id',
	));
	$send_members=array();

	foreach ($user_ids as $k=>$v){

		if($memberAll[$v]){
			$send_members[]=$memberAll[$v];
		}
	}
	if(!$send_members) {
		/* $this->show_warning('发送失败，指定会员不存在！');
		 return; */
		return false;
	}
	//注册发送任务
	/* 	var_dump(3);
	 echo '<hr/>'; */

	$send_num = 0;
	$sleep_num = 0;
	$send_member_arr   = array();
	$send_member_token = array();
	$send_member_ids   = array();
	foreach($send_members as $val ) {
		$sleep_num++;
		$send_num++;

		$send_member_token[] = $val['user_token'];
		$send_member_ids[]   = $val['user_id'];

		//记录手机号
		/* 		if($obj_type  == 0) {
			$send_member_arr[] = $val['user_name'];
		} */

		if($sleep_num >= 100) {
				
			$result = doCreatePush($send_member_token, $send_member_ids, $info_type, $title, $content, $send_type);

			sleep(2);
			$sleep_num = 0;
			$send_member_token = array();
			$send_member_ids   = array();
		}
	}
	// 	var_dump(4);
	// 	echo '<hr/>';
	//执行发送多余号码
	if($send_member_token) {
		$result = doCreatePush($send_member_token, $send_member_ids,$info_type, $title, $content, $send_type);
	}
	/* 	var_dump($send_num);exit();
	 echo '<hr/>'; */
	//记录日志
	/* $_lv_mod =& m('memberlv');
	 $user_lv=$this->_lv_mod->find(array(
	 'conditions'=>'1=1',
	 'fields'=>'*'
	 ));
	 $data['title']       = $title;
	 $data['content']     = $content;
	 $data['send_time']   = time();
	 $data['send_type']   = $send_type;//发送类型
	 $data['send_obj']    = $obj_type;//发送会员类型
	 $data['send_member'] = json_encode($send_member_arr);//指定会员时，记录发送的手机号
	 $data['send_num']    = $send_num;
	 $data['admin_name']  = $this->visitor->info['user_name'];
	 $data['admin_id']    = $this->visitor->info['user_id'];

	 $id = $this->sys_push->add($data);

	 if (!$id)  {
		$this->show_warning('发送失败');
		return;
		} */
	if(!$send_num){
		return false;
	}
	return true;
}

/*
 *  移动电话验证：大陆11位 支持港澳台 香港移动号8位5、6、9开头，澳门移动号8位6开头，台湾移动号：10位09XXXXXXXX
 * @author zxr
 * */
function validate_phone($phone){
	//大陆移动电话验证
	$rule="/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/";

	//香港移动电话 发送短信前要加00852
	$xrule="/^00852[569]\d{7}$/";
	//澳门移动电话 发送短信前要加00853
	$arule="/^008536\d{7}$/";
	//台湾移动电话 发送短信前要加00886
	$trule = "/^(00886)?09\d{8}$/";
	if(preg_match($rule, $phone)){
		return $phone;
	}else if(preg_match($xrule, $phone)){
		return $phone;
	}else if(preg_match($arule, $phone)){
		return $phone;
	}else if(preg_match($trule, $phone)){
		if(preg_match("/^00886\d{10}$/", $phone)){
			return $phone;
		}else {
			return "00886".$phone;
		}
	}else{
		return false;
	}
}

/**
 * 发送短信
 *

 */
/*zxr edit短信接口专用短信发送方法 */
function Send_Sms($phone,$category, $type,$msg) {
	$err = array('-1'=>'传递参数错误','-2'=>'用户id或密码错误','-3'=>'通道id错误','-4'=>'手机号码错误','-5'=>'短信内容错误','-6'=>'余额不足错误=','-7'=>'绑定ip错误',
			'-8'=>'未带签名','-9'=>'签名字数不对','-10'=>'通道暂停','-11'=>'该时间禁止发送','-12'=>'时间戳错误','-13'=>'编码异常','-4'=>'发送被限制');

	//获得配置项
	//$conf = include ROOT_PATH.'/data/settings.inc.php';//获得配置信息
	//$user_id   = $conf['msg_pid']; // sms9平台用户id
	//$pass      = $conf['msg_key']; // 用户密码
	//$channelid = $conf['channelid']; // 发送频道id
	//后台无配置地方，暂时写死--
	$timestamp = time();
	$channelid = 12852 ; // 发送频道id
	$cpid = 11664;
	$ps = md5("mfd123_".$timestamp."_topsky");

	$msg = iconv("UTF-8",'gbk//ignore',$msg);
	$url = "http://admin.sms9.net/houtai/sms.php?cpid={$cpid}&password={$ps}&channelid={$channelid}&tele={$phone}&timestamp={$timestamp}&msg={$msg}";

	$rs = file_get_contents($url);

	//$back = substr($rs,6);
	$pre = substr($rs,0,7);
	if($pre != 'success') {
		return false;
	}
	$sms_reg_mod = m('sms_reg_tmp');
	$sms_reg_mod->drop("category='$category' AND type='$type' AND phone='$phone' ");
	$data['type'] = $type;
	$data['code'] =0;
	$data['category']=$category;
	$data['add_time'] = time();
	$data['phone'] = $phone;
	$data['ps']='';
	$data['fail_time'] = time();
	$data['content']=$content;
	$data['is_success']=1;
	if($sms_reg_mod->add($data))
	{
		//            return true;
		return $rs;
	}
	else
	{
		return false;
	}
	return true;
}



    function get_real_ip(){

        $IPaddress='';

        if (isset($_SERVER)){

            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){

                $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];

            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {

                $IPaddress = $_SERVER["HTTP_CLIENT_IP"];

            } else {

                $IPaddress = $_SERVER["REMOTE_ADDR"];

            }

        } else {

            if (getenv("HTTP_X_FORWARDED_FOR")){

                $IPaddress = getenv("HTTP_X_FORWARDED_FOR");

            } else if (getenv("HTTP_CLIENT_IP")) {

                $IPaddress = getenv("HTTP_CLIENT_IP");

            } else {

                $IPaddress = getenv("REMOTE_ADDR");

            }

        }

        return $IPaddress;
    }
/**
 * 注册批量发送任务
 *
 * @param int $status
 * @param string $msg
 * @param mixed $data
 * @param string $dialog
 * @param int $type     1.交易| 2.评论晒单| 3.评论订单|4.顾客发需求|5.裁缝发作品、报价|13订单发货|12用户等级提升|14红包相关|15抵用券相关|16提现|17返修
 */
function doCreatePush($send_member_token, $send_member_ids, $type, $title_mes, $content_mes, $send_type = "all") {
	$_message_mod = &m("usermessage");
	$m_mod        = &m('member');
	//发送信鸽推送
	include_once(ROOT_PATH . '/includes/xinge/xinggemeg.php');
	$push = new XingeMeg();
	$result = $push->doCreateMultipush($send_member_token, $title_mes, $content_mes, array('url_type' => 'system', 'location_id' => 0), $send_type);
	if ($result){
		//发送成功。循环插入数据库
		foreach($send_member_ids as $val ) {
			$arr[] = array(
					'from_user_id'  => 1,
					'to_user_id'    => $val,
					'from_nickname' => '系统',
					'type'          => $type,
					'title'         => $title_mes,
					'content'       => $content_mes,
					'add_time'      => time(),
					'location_url'   =>'',
			);
		}
		$res=$_message_mod->add($arr);
		return $res;
	}
	return false;

}


?>
