<?php
use Cyteam\Message\SendMessage;
/**
 * 公共函数存放 待整理
 * --
 *
 * @copyright Copyright (c) 2014 – 2015 www.mfd.com
 * @author xiao5 <xiao5.china@gmail.com>
 * @package
 * @subpackage
 * @version 2014-12-13
 */
if ($_SERVER['HTTP_HOST'] == 'm.mfd.cn'){
	define('LOCALHOST1', 'http://www.mfd.cn');
	define('THESHAREURL', 'http://wap.mfd.cn/');
}
elseif ($_SERVER['HTTP_HOST'] == 'local.m.com'){
	define('LOCALHOST1', 'http://local.www.mfd.com');
}

elseif ($_SERVER['HTTP_HOST'] == 'm.test.mfd.cn'){
	define('LOCALHOST1', 'http://www.test.mfd.cn');
	define('THESHAREURL', 'http://wap.test.mfd.cn/');
}
elseif ($_SERVER['HTTP_HOST'] == 'www.dev.mfd.cn:8080'){
	define('LOCALHOST1', 'http://www.dev.mfd.cn');
	define('THESHAREURL', 'http://wap.dev.mfd.cn:8080/');
}
elseif ($_SERVER['HTTP_HOST'] == 'www.myfoodiepet.com'){
    define('LOCALHOST1', 'http://www.myfoodiepet.com');
    define('THESHAREURL', 'http://h5.myfoodiepet.com/');
}


//生成对dingo的签名
function makeSign($time){
    $appsecret = DINGO_APPSECRET;
    $sign = strtolower(md5(strtolower(urlencode(strtolower($time."&".$appsecret)))));
    return $sign;
}

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
/*春节放假短信通知
 * */
function jiaqi($order_id)
{
     
    $order_mod = m("order");
    $shiping_mod = m('shipping');
    $member_mod = &m ( "member" );
    $order_info = $order_mod->get_info($order_id);
    $shipping_info = $shiping_mod->get_info($order_info['shipping_id']);
    if($order_info['ship_tel'])
    {
        $phone = $order_info['ship_tel'];
    }
    if($order_info['ship_mobile'])
    {
        $phone = $order_info['ship_mobile'];
    }

    // $res = send_message("cyz_auth",[$order_info['user_id']],['order_sn'=>$order_info['order_sn'],'yundan'=>$yundan]);
    $res = smsAuthCode($phone,'jiaqi','jiaqi','get','pc','',['order_sn'=>$order_info['order_sn']]);
    //=====  如果是微信订单要发送消息  =====
    $member_info = $member_mod->get_info($order_info['user_id']);
    if($member_info['openid'] && $order_info['is_send'] == 0)
    {
        $mod = m('accesstoken');
        $access_token = $mod->get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $data['touser'] = $member_info['openid'];
        $data['template_id'] = 'vAN4FNeMSVtKb-Y1et7MzVVIOF-NEjf2ZhPQ4W5C6xE';
        $data['url'] = "http://h5.myfoodiepet.com/my_order-detail-".$order_info['order_id'].".html";
        $data['data']['first']['value'] = "春节（订单）通知";
        $data['data']['first']['color'] = "#173177";
        $data['data']['keyword1']['value'] = $order_info['order_sn'];
        $data['data']['keyword1']['color'] = "#173177";
        $data['data']['keyword2']['value'] = $shipping_info['shipping_name'];
        $data['data']['keyword2']['color'] = "#173177";
       // $data['data']['keyword3']['value'] = $yundan;
       // $data['data']['keyword3']['color'] = "#173177";
        $data['data']['remark']['value'] = "预计2月4日下达生产！";
        $data['data']['remark']['color'] = "#173177";
        $res = https_request($url,json_encode($data));
        if($res['errcode'] == 0)
        {
            $order_mod->edit($order_info['order_id'],['is_send'=>1]);
        }
        else
        {
            $access_token = $mod->settoken();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $res = https_request($url,json_encode($data));
        }

    }

}
//付款成功发短信
function fukuan($order_id)
{

    $order_mod = m("order");
    $shiping_mod = m('shipping');
    $member_mod = &m ( "member" );
    $order_info = $order_mod->get_info($order_id);

    $shipping_info = $shiping_mod->get_info($order_info['shipping_id']);
    if($order_info['ship_tel'])
    {
        $phone = $order_info['ship_tel'];
    }
    if($order_info['ship_mobile'])
    {
        $phone = $order_info['ship_mobile'];
    }


    //$res = send_message("cyz_auth",[$order_info['user_id']],['order_sn'=>$order_info['order_sn'],'yundan'=>$yundan]);

    $res = smsAuthCode($phone,'fukuan','deliver','get','pc','',['order_sn'=>$order_info['order_sn']]);


}
 function get_tid($tid,$access_token)
{
    if(!$tid || !$access_token)
    {
        return;
    }
    $gtid = "";
    $mod = m('accesstoken');
//    $access_token = $mod->get_token();
    $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$access_token;
    $data['template_id_short'] = $tid;
    $res = https_request($url,$data);

     echo '<pre>';print_r($res);exit;
     
    if($res['errcode'] == '40001')
    {

        $access_token = $mod->settoken();
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=" . $access_token;
        $res = https_request($url, $data);
        if ($res['template_id'])
        {
            $gtid = $res['template_id'];
        }
    }
    else
    {
        $gtid = $res['template_id'];
    }

    return $gtid;
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



define("SMS_FAIL_TIME", 300);//短信失效时间
define("EMAIL_FAIL_TIME", 60);//邮件失效时间


/**
 * 注册一发送邮件
 * @return void
 * @version 1.0.0 (2014-12-13)
 * @author Xiao5
 */
function emailcode($email,$category,$type , $way = 'ajax' , $opt = 'get',$ps='',$temp=array()){
    $email_code_mod =& m('email_code');
    $reset_time = EMAIL_FAIL_TIME;

    if(!$category)
        return outinfo("category 参数错误",1,$way);

    if(!$type)
        return outinfo("type 参数错误",1,$way);

    if(!$email){
        return outinfo("邮箱地址为空",1,$way);
    }

    if(!check::isEmail($email)){
        outinfo("邮箱格式错误",1,$way);
    }

    $conditions = array('conditions'=>" category = '$category' and type = '$type' and email = '$email' ");
    $where = " category = '$category' and type = '$type' and email = '$email' ";
    //从新索引下标
    $sms = array_values($email_code_mod->findAll($conditions));
    if(count($sms) > 1){
        if(!$ps)
            foreach($sms as $k=>$v){
            if($v['ps']){
                $ps =$v['ps'];
                break;
            }
        }

        $email_code_mod->delete_where(" category = '$category' and type = '$type' and email = '$email' ");
        $sms = null;
    }else{
        if(!$ps)
            $ps = $sms[0]['ps'];
    }
    $rs = array('err'=>0);
    if($sms){
        if(!$opt || $opt == 'get'){//获取一个码
            if($sms[0]['fail_time'] > time()){
                if($way != 'pc')
                    return outinfo("请不要重复发送...验证码",1,$way);
                
                return outinfo("请不要重复发送...验证码",1,$way);
            }else{//已失效，重新发送
                $rs =sendEmail($email, $category, $type, $where,$ps,$temp);
            }
        }else{//重置邮件
             $rs =sendEmail($email, $category, $type, $where,$ps,$temp);
        }
    }else{
         $rs =sendEmail($email, $category, $type, $where,$ps,$temp);
    }
    if ($rs['err'])
    {
        return $rs;
    }
    return outinfo($reset_time,0,$way);
}

/**
 * 返回数据接口
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function outinfo($msg,$err = 1 ,$way = 'ajax'){
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

function _format_price_int($price = 0.00)
{
    return intval($price);
    return sprintf('%.2f',intval($price));
}

/**
 *@author 
 * sign 成为创业者
 * 2015-11-10
 */
function change_lv($user_id){
 
    $m = & m('member');
    $auth_mod =& m('auth');
    $lv_mod =& m('memberlv');
    $member_invite = &m('memberinvite');
    $mod_order       = &m("order");

    $data = array();
    $lvs_name='';
    $user_info =$m->get("user_id=$user_id");
    
    $invite_info = $member_invite->get("invitee='{$user_id}' and type=1");

    if($user_info['member_lv_id']==1 && $auth_mod->get("user_id='{$user_id}' and status=1") && !empty($invite_info)){
        
        // 判断 是否购买过麦富迪币
        $coins = $mod_order->get("user_id ='{$user_id}' and extension='coin' and status=20");
        
        if($coins){
           
            if($user_info['point'] >= 1000 && $user_info['point'] < 3000){
                $data['member_lv_id'] =2;
            }
            
            $data['lv_time']= $data['cy_time']=time();
            if($user_info['invite'] == '') $data['invite']=make_order_card();
            $ret = $m->edit("user_id =".$user_id,$data,0);
            if(!$ret)$m->edit("user_id =".$user_id,$data,0); 
        }
       
        $m_info = $m->get_info($user_id);

        $lvs = $lv_mod->get(array(
            'fields' => "name",
            'conditions' => "member_lv_id='{$m_info['member_lv_id']}' and lv_type='supplier' ",

        ));
        $lvs_name =  $lvs['name'];

        //站内信  12是会员升级
        sendSystem($user_id, 12, '恭喜，您已成为'.$lvs_name.'！', '尊敬的麦富迪达人,恭喜您已升级为'.$lvs_name) ;
    }elseif($user_info['point'] >= 3000 && $user_info['point'] < 5000)
    {
        $data['member_lv_id'] =3;
        // 判断 是否购买过麦富迪币
        $coins = $mod_order->get("user_id ='{$user_id}' and extension='coin' and status=20");
        
        if($coins)
        {
            $data['lv_time']= $data['cy_time']=time();
            if($user_info['invite'] == '') $data['invite']=make_order_card();
            $ret = $m->edit("user_id =".$user_id,$data,0);
            if(!$ret)$m->edit("user_id =".$user_id,$data,0); 
        }
       
        $m_info = $m->get_info($user_id);

        $lvs = $lv_mod->get(array(
            'fields' => "name",
            'conditions' => "member_lv_id='{$m_info['member_lv_id']}' and lv_type='supplier' ",

        ));
        $lvs_name =  $lvs['name'];

        //站内信  12是会员升级
        sendSystem($user_id, 12, '恭喜，您已成为'.$lvs_name.'！', '尊敬的麦富迪达人,恭喜您已升级为'.$lvs_name) ;
    }elseif($user_info['point'] >= 5000)
    {
        $data['member_lv_id'] =4;
         // 判断 是否购买过麦富迪币
        $coins = $mod_order->get("user_id ='{$user_id}' and extension='coin' and status=20");
        
        if($coins)
        {
            $data['lv_time']= $data['cy_time']=time();
            if($user_info['invite'] == '') $data['invite']=make_order_card();
            $ret = $m->edit("user_id =".$user_id,$data,0);
            if(!$ret)$m->edit("user_id =".$user_id,$data,0); 
        }
       
        $m_info = $m->get_info($user_id);

        $lvs = $lv_mod->get(array(
            'fields' => "name",
            'conditions' => "member_lv_id='{$m_info['member_lv_id']}' and lv_type='supplier' ",

        ));
        $lvs_name =  $lvs['name'];

        //站内信  12是会员升级
        sendSystem($user_id, 12, '恭喜，您已成为'.$lvs_name.'！', '尊敬的麦富迪达人,恭喜您已升级为'.$lvs_name) ;
    }

    return $lvs_name;
}

/**
 * 成为创业者
 * @author yusw   2015-12-24
 * @return    result( 1  成功 0 失败)     auth-认证(1 成功 0 失败)  invite-邀请（1成功 0失败） coin-酷币（1成功 2失败）
 */
function changelv($user_id){
    $m = & m('member');
    $auth_mod =& m('auth');
    $lv_mod =& m('memberlv');
    $member_invite = &m('memberinvite');
    $order_mod =&m("order");

    $lvs_name='';
    $data = array();
    $result =array("result"=>0,'invite'=>1,"auth"=>1,"coin"=>1);

    $user_info =$m->get("user_id=$user_id");
    $invite_info = $member_invite->get("invitee='{$user_id}' and type=1");
    $auth_log =$auth_mod->get("user_id='{$user_id}'");
    $coin_log = $order_mod->get("extension='coin' and  user_id={$user_id} and status=20");


    empty($coin_log)&&$result['coin']=0;
    empty($invite_info)&&$result['invite']=0;
    empty($auth_log)&&$result['auth']=0;

    if($user_info['member_lv_id']==1 && $coin_log &&!empty($invite_info)&& $auth_log['status']==1){
        
        $data['member_lv_id']=2;
        $data['lv_time']= $data['cy_time']=time();
        if($user_info['invite'] == '') $data['invite']=make_order_card();

        $ret = $m->edit("user_id =".$user_id,$data);
        if(!$ret)$m->edit("user_id =".$user_id,$data);
        
        //积分
        reloadMember($user_id);
        $m_info = $m->get_info($user_id);

        $lvs = $lv_mod->get(array(
            'fields' => "name",
            'conditions' => "member_lv_id='{$m_info['member_lv_id']}' and lv_type='supplier' ",

        ));
        $lvs_name =  $lvs['name'];

        //站内信  12是会员升级
        sendSystem($user_id, 12, '恭喜，您已成为'.$lvs_name.'！', '尊敬的麦富迪达人,恭喜您已升级为'.$lvs_name) ;

        $result['result']=1;
    }
    return $result;

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




/*邀请码-创业者*/
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
 * 发送邮件接口逻辑
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function sendEmail($email,$category,$type,$where,$ps,$temp=array()){
   
    $email_code_mod =& m('email_code');
    $url = getEmailcode($email,$category);
    $url = getEmailDesc($category,$url,$temp);

    //没有做返回值验证
    $rs = sendmail($email, $url);
    if ($rs['err'])
    {
        return $rs;
    }
    $code = getCode($email);
    $email_code_mod->delete_where($where);
    $data = array(
        'code'=>$code,
        'type'=>$type,
        'fail_time'=>time() + EMAIL_FAIL_TIME,
        'add_time'=>time(),
        // 			'uid'=> $this->visitor->info['user_id'],
        'category'=>$category,
        'email'=>$email,
        'ps'=>$ps,
    );
    $email_code_mod->add($data);
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
    $email_from = Conf::get('site_name');
    $email_type = $setting['email_type'];
    $email_host = $setting['email_host'];
    $email_port = $setting['email_port'];
    $email_addr = $setting['email_addr'];
    $email_id   = $setting['email_id'];
    $email_pass = $setting['email_pass'];
    $email_test = $email;				//接收人email
    $email_subject = '用户帐号激活';	//邮件标题
    import('mailer.lib');
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
 * 手机验证逻辑处理
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function smsAuthCode($phone,$category,$type,$send_opt,$way,$ps='',$temp=array()){
    $sms_reg_tmp_mod =& m('sms_reg_tmp');
    $reset_time = SMS_FAIL_TIME;
    $conditions = array('conditions'=>" category = '$category' and type = '$type' and phone = '$phone' for update ");
    //从新索引下标
    $sms_log = array_values($sms_reg_tmp_mod->findAll($conditions));
    //正确的来讲，同一种类型下，只能一条数据
    if(count($sms_log) > 1){
        $sms_reg_tmp_mod->delete_where(" category = '$category' and type = '$type' and phone = '$phone' ");
        $sms_log = null;
    }
    $rs = array('err'=>0);
    if($sms_log){
        //send_opt:get第一次获取一个码,res:重新发送一个验证码
        if(!$send_opt || $send_opt == 'get'){
            if($sms_log[0]['fail_time'] > time()){
                //还未失效，不需要再次发送
                if($way != 'pc'){
                    return outinfo($reset_time."秒内，请不要重复发送...验证码",1,$way);
                }
            }else{//已失效，直接重新发送
                $sms_reg_tmp_mod->delete_where(" category = '$category' and type='$type' and phone = '$phone' ");
                // var_dump(1);
                $rs = smsCode($phone,$category,$type,$ps,$temp);
            }
        }else{//重置一个码
            if($sms_log[0]['fail_time'] > time()){
                //还未失效，不需要再次发送
                $str = $reset_time."秒之后才可重置...";
                return outinfo($str,1,$way);
            }else{
//                   $rs = smsCode($phone,$category,$type,$sms_log[0]['ps'],$temp);
                // var_dump(2);
                  $rs = smsCode($phone,$category,$type,$ps,$temp);
            }
        }
          // $rs = smsCode($phone,$category,$type,$ps,$temp);
    }else{
        // var_dump(3);
        $rs = smsCode($phone,$category,$type,$ps,$temp);
        // var_dump($rs);
    }
    // var_dump($rs);
    if(isset($rs['err']) && $rs['err'])
    {
        // var_dump(2222);
        return $rs;
    }else{
        // var_dump(3333);
    	return outinfo('发送成功',0,$way);
    }
}
/**
 * 阿里大于发送短信
 * @return void
 * @version 1.0.0 (2016-9-18)
 * @author zhaoxr
 */
function smsCode($phone,$category,$type,$ps='',$temp){
	$rand = rand ( 1000, 9999 );
	$sendMessage_obj = new SendMessage ();
	
    $time=intval(date('i',SMS_FAIL_TIME)).'分钟';
	switch ($category) {
		case 'findps' :
			$params = "{'content':'$rand','time':'" . $time ."'}";
			$code = 'SMS_14965768';
			break;
		case 'reg' :
			$params = "{'content':'$rand','time':'" . $time . "分钟'}";
			$code = 'SMS_14965768';
			break;
	    case 'fukuan' :
			$params = "{'order_sn':'".$temp[order_sn]."'}";
			$code = 'SMS_47715016';
		    break;
		case 'deliver':
			if($temp){
				$params = "{'order_sn':'".$temp[order_sn]."','express':'" . $temp['express']. "'}";
				$code = 'SMS_47800016';
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
    // var_dump($phone);
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
    // var_dump($rs);
	if (isset ( $rs->code )) {
		$sub_code=strval($rs->sub_code);
		// var_dump($rs);
		return array(
				'err'=>1,
				'msg'=>$err[$sub_code],
		);
	} else {
		
		$sms_reg_tmp_mod = & m ( 'sms_reg_tmp' );
		$sms_reg_tmp_mod->delete_where ( " category = '$category' and type='$type' and phone = '$phone' " );
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
/**
 * 手机验证逻辑处理-短信提示内容
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function getSmsEmailDesc($cate,$type,$temp = array()){
    $arr = array(
        'reg' => array(
            'reg'=>'您的验证码为：<#code>',
            'reset'=>'感谢您的注册~您的注册手机验证码为：<#code>~'.SMS_FAIL_TIME.'秒内有效，请及时注册...',
        ),
        'findps' => array(
            'findps'=>'您的验证码为：<#code>',
            'reset'=>'感谢您的注册~您的注册手机验证码为：<#code>~'.SMS_FAIL_TIME.'秒内有效，请及时注册...',
        ),
        'tailor' => array(
            'tailor'=>'您的验证码为：<#code>',
            'reset'=>'感谢您的注册~您的注册手机验证码为：<#code>~'.SMS_FAIL_TIME.'秒内有效，请及时注册...',
        ),
        'demand' => array(
            'demand'=>'尊敬的会员，您发布需求时的验证码为：<#code> ('.SMS_FAIL_TIME.'秒内有效)',
            'reset'=>'感谢您的注册~您的注册手机验证码为：<#code>~'.SMS_FAIL_TIME.'秒内有效，请及时注册...',
        ),
        'suitdiy' => array(
            'suitdiy'=>'尊敬的会员，您发布需求时的验证码为：<#code>  （ '.SMS_FAIL_TIME.'秒内有效 ） ',
            'reset'=>'感谢您的注册~您的注册手机验证码为：<#code>~'.SMS_FAIL_TIME.'秒内有效，请及时注册...',
        )        
    );
    if($arr[$cate][$type]){
        return $arr[$cate][$type];
    }else{
        $config = ROOT_PATH . '/data/config/sms.php';
        if(is_file($config)){
            $list = include_once($config);
        }
        $msg = $list[$cate]['list'][$type]['msg'];
        if(!empty($temp)){
            foreach ($temp as $k=>$v){
                $msg = str_replace("<{"."$".$k."}>",$v,$msg);
            }
        }
        $msg = preg_replace('/(<{\$+[a-zA-Z0-9_-]+}>)/','', $msg);
        return $msg;
    }

}
/**
 * 手机验证逻辑处理-发送短信
 * @return void
 * @version 1.0.0 (2014-12-12)
 * @author Xiao5
 */
function sms_new($phone,$msg){
    $err = array('-1'=>'传递参数错误','-2'=>'用户id或密码错误','-3'=>'通道id错误','-4'=>'手机号码错误','-5'=>'短信内容错误','-6'=>'余额不足错误=','-7'=>'绑定ip错误',
        '-8'=>'未带签名','-9'=>'签名字数不对','-10'=>'通道暂停','-11'=>'该时间禁止发送','-12'=>'时间戳错误','-13'=>'编码异常','-4'=>'发送被限制');
    $timestamp = time();
    $channelid = 12852 ; // 发送频道id
    $cpid = 11477;
    $ps = md5("alicaifeng123_".$timestamp."_topsky");
    $r = rand(1000,99999);
    $msg =iconv("UTF-8",'GBK',$msg);
    //$msg =urlencode(iconv('GBK',"UTF-8",$msg));
    $url = "http://admin.sms9.net/houtai/sms.php?cpid={$cpid}&password={$ps}&channelid={$channelid}&tele={$phone}&timestamp={$timestamp}&msg={$msg}";
	// echo $url;
    $rs = get2($url);
    //    var_dump($rs);
    $back = substr($rs,6);
    $pre = substr($rs,0,7);
    if($pre != 'success')
    {
        return array('err'=>1,'msg'=>$err[$back]);
    }
    return $rs;
}

/**
 * 手机验证逻辑处理-发送短信 curl请求
 * @return void
 * @version 1.0.0 (2014-12-22)
 * @author Xiao5
 */
function get2($url, $referer = ""){
    $info = null;
    $ch = curl_init($url);
    $options = array(
        CURLOPT_RETURNTRANSFER => true,         // return web page
        CURLOPT_HEADER         => false,        // don't return headers
        CURLOPT_FOLLOWLOCATION => true,         // follow redirects
        CURLOPT_ENCODING       => "",           // handle all encodings
        CURLOPT_USERAGENT      => "",           // who am i
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
 * CURL POST
 * @version 1.0.0 (2014-12-22)
 * @高飞
 */
function curlPost($url,$postData)
{
    $data = empty($postData)?array():$postData;
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $ch );
    curl_close ( $ch );
    return $return;
}
/*********************************************************************
函数名称:encrypt
函数作用:加密解密字符串
使用方法:
加密     :encrypt('str','E','key');
解密     :encrypt('被加密过的字符串','D','key');
参数说明:
$string   :需要加密解密的字符串
$operation:判断是加密还是解密:E:加密   D:解密
$key      :加密的钥匙(密匙);
*********************************************************************/
function encrypt($string,$operation,$key='')
{
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++)
    {
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++)
    {
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++)
    {
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D')
    {
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
        {
            return substr($result,8);
        }
        else
        {
            return'';
        }
    }
    else
    {
        return str_replace('=','',base64_encode($result));
    }
}
/**
 * 上传图片
 * @return void
 * @version 1.0.0 (2014-22-12)
 * @author Xiao5
 */
function pro_img($file_name,$RESIZEWIDTH=100,$RESIZEHEIGHT=100,$FILENAME="image.thumb",$waterMark = 0){
    if($_FILES[$file_name]['size']){
        if($_FILES[$file_name]['type'] == "image/pjpeg" || $_FILES[$file_name]['type'] == 'image/jpeg' ){
            $im = imagecreatefromjpeg($_FILES[$file_name]['tmp_name']);
        }elseif($_FILES[$file_name]['type'] == "image/x-png" || $_FILES[$file_name]['type'] == 'image/png'){
            $im = imagecreatefrompng($_FILES[$file_name]['tmp_name']);
        }elseif($_FILES[$file_name]['type'] == "image/gif" || $_FILES[$file_name]['type'] == 'image/gif'){
            $im = imagecreatefromgif($_FILES[$file_name]['tmp_name']);
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

        if($waterMark){
            include_once "includes/libraries/imageWaterMark.class.php";
            ImageWaterMark::make($FILENAME,1,'','rctailor');
        }
    }
}
/**
 * 处理图片大小
 * @return void
 * @version 1.0.0 (2014-22-22)
 * @author Xiao5
 */
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
    $src= get_domain() . "/upload_user_photo/".$path_name."/$path/".$file_name;
    return $src;
}

/**
 * 获取默认封面图
 * 
 * @return void
 * @version 1.0.0 (2014-22-22)
 * @author Xiao5
 */
    /*
 * function getDefAlbumPic($type = 'big'){
 * if($type == 'small')
 * return ALBUM_DEF_SMALL;
 *
 * return ALBUM_DEF_TOP;
 *
 * }
 */
 
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
    
    
/************************************************************************待整理***********************************************************************/
/************************************************************************待整理***********************************************************************/
/************************************************************************待整理***********************************************************************/
/************************************************************************待整理***********************************************************************/
/************************************************************************待整理***********************************************************************/

function stripslashes_array(&$array) {
    while(list($key,$var) = each($array)) {
        if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
            if (is_string($var)) {
                $array[$key] = lib_replace_end_tag($var);
            }
            if (is_array($var))  {
                $array[$key] = stripslashes_array($var);
            }
        }
    }
    return $array;
}
//--------------------------
// 替换HTML尾标签,为过滤服务
//--------------------------
function lib_replace_end_tag($ret)
{
    if (empty($ret)) return false;

    $ret = trim($ret);         		//清理空格字符
    $ret = nl2br($ret);         	//将换行符转化为<br />
    $ret = strip_tags($ret);      	//过滤文本中的HTML标签
    $ret = htmlspecialchars($ret); 	//将文本中的内容转换为HTML实体
    $ret = addslashes($ret);      	//加入字符转义



    return $ret;
}

function __autoload($class){
    if( strpos($class,M_CLASS) !== false){

        $l = strpos($class,M_CLASS);
        $class = substr($class, 0,$l);
        $class = lcfirst($class);
        include_once $class .M_EXT;
    }elseif( strpos($class,LIB_CLASS) !== false){
        $l = strpos($class,LIB_CLASS);
        $class = substr($class, 0,$l);
        $class = lcfirst($class);
        include_once $class .LIB_EXT;
    }
}

function stop($error,$module = '',$back_url = ''){
    exit($error);
}

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

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name, $method='', $args=array()) {
    static $_instance = array();
    if ( ! isset($_instance[$name]) ) {
        if (class_exists($name)) {
            $o = new $name();
            $_instance[$name] = $o;
        }else stop("class not exists:". $name,1,1);
    }
    return $_instance[$name];
}

function isFans($uid,$to_uid){
    if(!$uid || !$to_uid)
        return 0;

    $db = &db();
    $sql = "select * from cf_user_follow where uid = $uid and follow_uid = $to_uid";
    return $db->getRow($sql);
}

function addSearchIndex($index_id,$cate,$keyword){
    $m = m('search');
    $data  = array(
        'keyword'=>$keyword,
        'keyword'=>	$index_id,
        'add_time'=>time(),
        'cate'=>$cate,
    );
    $m->add($data);
}

function search($keyword ,$cate = ''){
    $where = " keyword like '%$keyword%' " ;
    if($cate)
        $where .=" and cate = '$cate' ";

    $sql = "select * from cf_userphoto  ";
}





/*批处理上传图片  add liliang*/
function pro_img_multi($file_name,$RESIZEWIDTH=100,$RESIZEHEIGHT=100,$FILENAME="image.thumb",$waterMark = false){
    if($file_name['size']){
        if($file_name['type'] == "image/pjpeg" || $file_name['type'] == 'image/jpeg' ){
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
//上传用户个人空间-头部个性化图片
function uploadZone($input_name){
    $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/zone/';
    $fileName =  md5( uniqid() . mt_rand(0,255) ) . ".jpg";

    // 	$fileDirName2 = $dir ."375x500/" . $fileName;
    // 	pro_img($input_name,375,500,$fileDirName2);
    // 	$fileDirName3 = $dir ."220x293/" . $fileName;
    // 	pro_img($input_name,220,293,$fileDirName3);
    // 	$fileDirName4 = $dir ."70x92/" . $fileName;
    // 	pro_img($input_name,70,92,$fileDirName4);


    $fileDirName1 = $dir ."original/" . $fileName;
    $rs = move_uploaded_file($_FILES[$input_name]["tmp_name"],$fileDirName1);

    $src1= site_url() . "/upload_user_photo/zone/original/".$fileName;
    //$src2= site_url() . "/upload_user_photo/mianliao/375x500/".$fileName;
    //$src3= site_url() . "/upload_user_photo/mianliao/220x293/".$fileName;
    //$src4= site_url() . "/upload_user_photo/mianliao/70x92/".$fileName;

    // 	$arr = array($src1);
    return $src1;
}

//上传面料
function uploadMianliao($input_name){
    $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/mianliao/';
    $fileName =  md5( uniqid() . mt_rand(0,255) ) . ".jpg";

    $fileDirName2 = $dir ."375x500/" . $fileName;
    pro_img($input_name,375,500,$fileDirName2);
    $fileDirName3 = $dir ."220x293/" . $fileName;
    pro_img($input_name,220,293,$fileDirName3);
    $fileDirName4 = $dir ."70x92/" . $fileName;
    pro_img($input_name,70,92,$fileDirName4);


    $fileDirName1 = $dir ."original/" . $fileName;
    $rs = move_uploaded_file($_FILES[$input_name]["tmp_name"],$fileDirName1);

    $src1= site_url() . "/upload_user_photo/mianliao/original/".$fileName;
    $src2= site_url() . "/upload_user_photo/mianliao/375x500/".$fileName;
    $src3= site_url() . "/upload_user_photo/mianliao/220x293/".$fileName;
    $src4= site_url() . "/upload_user_photo/mianliao/70x92/".$fileName;

    $arr = array($src1,$src2,$src3,$src4);
    return $arr;
}

//上传-流行趋势  liliang edit
function uploadLiuxing($input_name){
    $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/liuxing/';
    $fileName =  md5( uniqid() . mt_rand(0,255) ) . ".jpg";

    //$fileDirName2 = $dir ."450x610/" . $fileName;
    //pro_img_multi($input_name,450,610,$fileDirName2);
    $fileDirName3 = $dir ."225x305/" . $fileName;
    pro_img_multi($input_name,225,305,$fileDirName3);
    $fileDirName4 = $dir ."200x250/" . $fileName;
    pro_img_multi($input_name,200,250,$fileDirName4);
    $fileDirName5 = $dir ."236x353/" . $fileName;
    pro_img_multi($input_name,236,353,$fileDirName5);


    $fileDirName1 = $dir ."original/" . $fileName;
    $rs = move_uploaded_file($input_name["tmp_name"],$fileDirName1);

    $src1= "upload_user_photo/liuxing/original/".$fileName;
    //$src2= site_url() . "/upload_user_photo/liuxing/450x610/".$fileName;
    $src3= "upload_user_photo/liuxing/225x305/".$fileName;
    $src4= "upload_user_photo/liuxing/200x250/".$fileName;
    $src5= "upload_user_photo/liuxing/236x353/".$fileName;

    $arr = array($src1,$src3,$src4,$src5);
    /*只返回$fileName*/
    return $fileName;
    //return $arr;
}

//上传-纽扣
function uploadNiukou($input_name){
    $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/niukou/';
    $fileName =  md5( uniqid() . mt_rand(0,255) ) . ".jpg";

    $fileDirName2 = $dir ."375x450/" . $fileName;
    pro_img($input_name,375,450,$fileDirName2);
    $fileDirName3 = $dir ."220x285/" . $fileName;
    pro_img($input_name,220,285,$fileDirName3);
    $fileDirName4 = $dir ."375x450/" . $fileName;
    pro_img($input_name,375,450,$fileDirName4);


    $fileDirName1 = $dir ."original/" . $fileName;
    $rs = move_uploaded_file($_FILES[$input_name]["tmp_name"],$fileDirName1);

    $src1= site_url() . "/upload_user_photo/niukou/original/".$fileName;
    $src2= site_url() . "/upload_user_photo/niukou/375x450/".$fileName;
    $src3= site_url() . "/upload_user_photo/niukou/220x285/".$fileName;
    $src4= site_url() . "/upload_user_photo/niukou/375x450/".$fileName;

    $arr = array($src1,$src2,$src3,$src4);
    return $arr;
}

function delUserPhoto($pic_id){
    $m_photo = m('userphoto');
    $m_album =  m('album');
    $db = &db();
    $photo = $m_photo->getById($pic_id);
    if(!$photo)
        return 0;

    $sql = "update cf_member set pic_num = pic_num - 1 where user_id = ".$photo['uid'];
    $db->query($sql);

    $rs = $m_photo->delById($pic_id);
    if($photo['album_id']){
        $album = $m_album->getByID($photo['album_id']);
        if($album){
            if($album['top_url'] == $photo['url']){
                $m_album->edit($photo['album_id'],array('top_url'=>''));
            }
            $sql = "update cf_album set pic_num = pic_num - 1 where id = ".$photo['album_id'];
            $db->query($sql);
        }
    }
    $_SESSION['user_info']['pic_num'] = $_SESSION['user_info']['pic_num'] - 1;
    //======= 删除街拍//设计后应把用户的喜欢数据也删除掉. //Ruesin
    $mLike =&m ('like');
    $likeArr = $mLike->findAll(array(
        'conditions' => ' cate in ("sheji_like","jiepai_like") AND like_id = '.$pic_id,
    ));
    foreach ($likeArr as $row){
        $delIds[] = $row['id'];
    }
    $mLike->drop(db_create_in($delIds,'id'));

    return 1;
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
    $src= get_domain() . "/upload_user_photo/shaidan/$path/".$file_name;
    return $src;
}


function sms($phone,$msg){
    // 		$phone = '13522536459';
    $cpid = 9817;
    // 		$msg = "Test123";
    $timestamp = time();
    $ps = md5("rctailor123_".$timestamp."_topsky");
    $channel = 12852;
    $url = "http://admin.sms9.net/houtai/sms.php?cpid=$cpid&password=$ps&channelid=12852&tele=$phone&channelid=$channel&timestamp=$timestamp&msg={$msg}";
    $rs = get2($url);
    return $rs;
}





















function setLike($uid,$like_id,$cate,$away = 'ajax'){
    checkCate($cate);

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

    /* 定制喜欢数量 */
    if($cate == 'dingzhi_like'){
        $_customs_mod      	=& m('customs');
        $_customs_mod->setInc(" cst_id = $like_id",'cst_likes');
    }


    $m = m('userphoto');
    $m->setInc(" id = $like_id",'like_num');

    $_SESSION['user_info']['like_num'] = $_SESSION['user_info']['like_num'] + 1;

    return outinfo('ok',0,$away);
}

function getLikeByUids($uids ,$like_id , $cate){
    $db = &db();
    $sql = " select * from cf_like where uid in ( $uids ) and cate = '$cate' and like_id =  $like_id";
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

function getLikeByUid($uid ,$like_id , $cate){
    if(!$uid || !$like_id || !$cate)
        return 0;

    $arr = array('design'=>'酷客首页-个人设计','userphoto'=>'酷客首页-街拍');

    $db = &db();
    $sql = " select * from cf_like where uid = $uid and cate = '$cate' and like_id =  '{$like_id}'";
    $like = $db->getRow($sql);
    if($like)
        return 1;
    return 0;
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

function getUinfoByUid($uid){
    if(!$uid){
        $avatar = getAvatarByFile("");
        return array('like_num'=>0,'fans'=>0,'avatar'=>$avatar);
    }
    $db = &db();

    $sql = "select * from cf_member where user_id = ".$uid;
    $user = $db->getRow($sql);
    if(!$user)
        return array('like_num'=>0,'fans'=>0,'avatar'=>getAvatarByFile(""),'uid'=>0);

    if($user['province']){
        $province = getAreaByRid($user['province']);
        $user['province_name'] = $province['region_name'];
    }
    if($user['city']){
        $city = getAreaByRid($user['city']);
        $user['city_name'] = $city['region_name'];
    }
    $level = getLevel($user['member_lv_id']);
    if($level){
        $user['level'] = $level['name'];
        if($level['lv_logo']){
            $user['level_logo'] = "/".$level['lv_logo'];
        }else{
            $user['level_logo'] =  '/data/files/mall/lv_logo/1/1.png';
        }

    }else{
        $user['level_logo'] = '/data/files/mall/lv_logo/1/1.png';
    }
    $user['avatar'] = getAvatarByFile($user['avatar']);
    $user['link'] = "/index.php/club-album-{$uid}.html";
    $user['uid'] = $user['user_id'];
    return $user;
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

function getAvatarByUid($uid,$size = '168'){


}

function getLevel($lid){
    $db = &db();

    $sql = "select * from cf_member_lv where member_lv_id = ".$lid;
    return $db->getRow($sql);
}

function getAreaByRid($rid){
    $db = &db();

    $sql = "select * from cf_region where region_id = ".$rid;
    return $db->getRow($sql);
}

function getAPICate(){
    $arr = array(
        //小白
        'order_reward'  =>array('desc'=>'订单消费获得奖励','type'=>'order','key'=>'order_reward'),
        'order_dk_point'  =>array('desc'=>'使用积分抵扣订单额','type'=>'order','key'=>'order_dk_point'),
        'order_dk_coin'  =>array('desc'=>'使用麦富迪币抵扣订单额','type'=>'order','key'=>'order_dk_coin'),

        'sheji_order'  =>array('desc'=>'设计作品产生订单赠送麦富迪币','type'=>'order','key'=>'sheji_order'),
        'jiepai_order'  =>array('desc'=>'街拍作品产生订单赠送麦富迪币','type'=>'order','key'=>'jiepai_order'),
        'series_comment'=>	array('desc'=>'发表主题系列的评论获得奖励','type'=>'series','key'=>'series_comment'),

        //帅
        'shoucang'	=>array('desc'=>'收藏基本款获得奖励','type'=>'shoucang','key'=>'shoucang'),

        //李亮
        'liuxing_like'=>array('desc'=>'喜欢流行趋势获得奖励','type'=>'like','key'=>'liuxing_like'),

        //广信
        'dingzhi_comment'	=>	array('desc'=>'订单消费后进行评论获得奖励','type'=>'comment','key'=>'dingzhi_comment'),
        'goods_comment'		=>	array('desc'=>'订单消费后进行评论获得奖励','type'=>'goods','key'=>'goods_comment'),
        'dingzhi_like'		=>	array('desc'=>'喜欢基本款获得奖励','type'=>'like','key'=>'dingzhi_like'),
        'zhuti_like'		=>	array('desc'=>'喜欢主题获得奖励','type'=>'like','key'=>'zhuti_like'),

        //王东岩&小5
        'jiepai_like'			=>array('desc'=>'喜欢街拍获得奖励','type'=>'like','key'=>'jiepai_like'),
        'sheji_like'			=>array('desc'=>'喜欢设计获得奖励','type'=>'like','key'=>'sheji_like'),

        'jiepai_comment'	=>array('desc'=>'评论街拍作品获得奖励','type'=>'comment','key'=>'jiepai_comment'),
        'sheji_comment'		=>array('desc'=>'评论设计作品获得奖励','type'=>'comment','key'=>'sheji_comment'),

        'login_reward'		=>array('desc'=>'会员登录获得奖励','type'=>'login','key'=>'login_reward'),
        'proecss_profile'	=>array('desc'=>'完善个人资料获得奖励','type'=>'profile','key'=>'proecss_profile'),

        'follow'			=>array('desc'=>'关注酷客获得奖励','type'=>'follow','key'=>'follow'),
        'unfollow'			=>array('desc'=>'取消关注','type'=>'unfollow','key'=>'unfollow'),

        'sheji_reward' =>array('desc'=>'设计上传获得奖励','type'=>'upload','key'=>'sheji_reward'),
        'jiepai_reward' =>array('desc'=>'街拍上传获得奖励','type'=>'upload','key'=>'jiepai_reward'),

        //下面是为了兼容老的<评论>KEY值
        'personaldesign'=>array('desc'=>'个人设计详情页'),
        'streetinfo'=>array('desc'=>'街拍详情页'),
        'order_goods'=>array('desc'=>'基本款订单评论'),
        'dis' => array('desc'=>"主题系列"),
        'serve'=>array('desc'=>'服务点')


    );
    return $arr;
}



function pointTurnNum($cate){
    $num = Conf::get($cate);
    return $num;
}

function checkCate($cate){
    if(!$cate)
        exit('cate null');

    $arr = getAPICate();
    $f = 0;
    $key_arr =null;
    foreach($arr as $k=>$v){
        if($k == $cate){
            $f = 1;
            $key_arr  = $v;
            break;
        }
    }

    if(!$f)
        exit('cate not in arr');

    return $key_arr;
}

function setPoint($uid,$num,$opt,$cate,$author = 'system',$msg = '',$way = 'pc',$auto_id = 0){
    $cate_arr = checkCate($cate);

    if(!$uid)
        return 0;

    $m = m('point_log');
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

    if($auto_id)
        $data['auto_id'] = $auto_id;

    $m->add($data);
    $db = &db();

    if($opt != 'add'){
        $num = - $num;
    }

    $sql = "update cf_member set point = point + ".$num." where user_id = ".$uid;
    $_SESSION['user_info']['point'] = $_SESSION['user_info']['point'] + $num;

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

    $sql = "update cf_member set coin = coin + ".$num." where user_id = ".$uid;
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

    $sql = "update cf_member set coin = experience + ".$num." where user_id = ".$uid;
    $db->query($sql);

    $member_lv_mod =& m('memberlv');
    $member_lv_mod->auto_level($uid,'member',$num);

    $_SESSION['user_info']['experience'] = $_SESSION['user_info']['experience'] + $num;

    return outinfo('ok',0 , $way);

}

function getComment($uid,$comment_id,$cate,$limit = '' ,$desc = 'add_time'){
    $arr = array('personaldesign'=>'个人设计详情页','streetinfo'=>'街拍详情页','order_goods'=>'基本款订单评论', 'dis' => "主题系列",'serve'=>'服务点');

    $db = &db();

    if(!$uid || !$comment_id || !$cate)
        exit(" func <getComment>: uid or comment_id or cate  is null");

    $where = " uid = $uid and comment_id = $comment_id and cate = '$cate' ";
    if($limit)
        $limit = " limit ".$limit;

    $sql = "select * from cf_comments where $where  order by $desc desc $limit ";
    $rs = $db->getAll($sql);
    return $rs;
}

function getCommentPage($comment_id,$cate,$page = 1,$desc = 'add_time'){
    $arr = array('personaldesign'=>'个人设计详情页','streetinfo'=>'街拍详情页');

    $db = &db();
    $where = " comment_id = $comment_id and cate = '$cate' ";

    $sql = "select count(*) as total from cf_comments where $where";
    $total = $db->getRow($sql);
    if($total['total']){
        include 'includes/libraries/pageSimple.lib.php';
        $page = new PageSimple($total['total'] , $page,5);
        $page->execPage();

        $sql = "select * from cf_comments where $where  order by $desc desc limit ".$page->mLimit[0]." , " .$page->mLimit[1];
        $rs = $db->getAll($sql);
        $rs = array('data'=>$rs,'page'=>$page);
    }

    return $rs;

}

function getCommentByUid($uid,$cate,$limit = '' ,$desc = 'add_time'){
    $db = &db();

    $where = " uid = $uid ";
    if($cate)
        $where .= " and cate = '$cate'";

    if($limit)
        $limit = " limit ".$limit;

    $sql = "select * from cf_comments where $where  order by $desc desc $limit ";
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

    $sql = "select * from cf_comments where $where $limit ";
    $rs = $db->getAll($sql);
    return $rs;
}

function getPointLog($uid , $cate , $day = '' , $auto_id = 0){
    $db = &db();
    $where = " uid = $uid and cate = '$cate' ";
    if($day){
        $s_time = strtotime(  date("Y-m-d") . " 00:00:00"  );
        $e_time = $s_time + 24 * 60 * 60;
        $where .= " and ( add_time >= $s_time and  $e_time <= $e_time )  ";
    }

    if($auto_id)
        $where .= " and auto_id = $auto_id ";

    $sql = " select * from cf_point_log where $where ";
    //echo $sql;
    return $db->getRow($sql);
}

function setComment($uid,$to_uid,$comment_id,$cate,$content,$goodid = 0){
    if( $uid ){
        $comment = getComment($uid,$comment_id,$cate);
        if($comment){
            if('serve' != $cate)
                return outinfo('已评论',1,'pc');
        }

        $m = m('comments');
        $data = array(
            'uid'=>$uid,
            'to_uid'=>$to_uid,
            'comment_id'=>$comment_id,
            'cate'=>$cate,
            'content'=>$content,
            'add_time'=>time(),
            'goods_id'=>$goodid
        );
        $new_id = $m->add($data);

        $db = &db();
        $sql = "update cf_member set comment_num = comment_num + 1 where user_id = ".$uid;
        $db->query($sql);

        if( $cate == 'sheji_comment' || $cate == 'jiepai_comment'){
            $sql = "update cf_userphoto set comment_num = comment_num + 1 where id =  ".$comment_id;
            $db->query($sql);
        }

        $_SESSION['user_info']['comment_num'] = $_SESSION['user_info']['comment_num'] + 1;
    }else{//匿名评论
        $m = m('comments');
        $data = array(
            'uid'=>$uid,
            'to_uid'=>$to_uid,
            'comment_id'=>$comment_id,
            'cate'=>$cate,
            'content'=>$content,
            'add_time'=>time(),
            'goods_id'=>$goodid
        );
        $new_id = $m->add($data);
    }

    return $new_id;
}
/**
 * 删除评论
 * @param $comment_id 评论ID
 * @return bool
 * @author Ruesin
 */
function delComment($comment_id){

    $db = &db();

    $_mod_comments =& m('comments');
    $_mod_member   =& m('member');
     
    $data = $_mod_comments->get($comment_id);

    //     $mData = array('comment_num' => 'comment_num - 1',);
    //     $_mod_member->edit($data['uid'],$mData);//return 1/0

    $sql = "update cf_member set comment_num = comment_num - 1 where user_id = ".$data['uid'];
    $db->query($sql);

    //if($data['cate'] == 'sheji_comment' || $data['cate'] == 'jiepai_comment' || $data['cate'] == 'dingzhi_comment' ){
    if($data['cate'] == 'sheji_comment' || $data['cate'] == 'jiepai_comment'){
        $sql = "update cf_userphoto set comment_num = comment_num - 1 where id =  ".$data['comment_id'];
        $db->query($sql);
    }
    $_mod_comments->drop($comment_id);

}


function getCouponById($coupon_id){
    $db = &db();

    $sql = "select * from cf_coupon where coupon_id = ".$coupon_id;
    return $db->getRow($sql);
}
//获取一张照片的，喜欢总数
function getPicLikeNum($pic_id){
    $db = &db();

    $sql = "select count(*) as total from cf_like where like_id = ".$pic_id;
    $rs =  $db->getRow($sql);
    return $rs['total'];
}
function TimeFormat($time=0){
    $curtime=gmtime();
    $diff = $curtime-$time;
    $str = '';
    if($diff<60){
        $str='刚刚';
    }elseif($diff<3600){
        $str=intval($diff/60).'分钟前';
    }elseif($diff<86400){
        $str=intval($diff/3600).'小时前';
    }
    /*elseif($diff>=86400 && date("Y",$curtime)==date("Y",$curtime)){
     $str=date("m月d日 H:i",$time);
     }*/
    else{
        $str=date("Y-m-d H:i",$time);
    }
    return $str;
}

//获取上/下一个月
function getMonth($month){
    //得到系统的年月
    $tmp_date=date("Ym");
    //切割出年份
    $tmp_year=substr($tmp_date,0,4);
    //切割出月份
    $tmp_mon =substr($tmp_date,4,2);
    //	$tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
    $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-$month,1,$tmp_year);
    return $tmp_forwardmonth;
    // 	if($sign==0){
    // 		//得到当前月的下一个月
    // 		return $tmp_nextmonth;
    // 	}else{
    // 		//得到当前月的上一个月
    // 		return $tmp_forwardmonth;
}


//查看TA的设计
function getOtherDesign($limit = 6){
    $sql = "select * from cf_userphoto where cate = 1 order by add_time desc limit ".$limit;
    $db = &db();
    return $db->getAll($sql);
}

//随机字符串
function createNonceStr($length = 16) {
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

//随机字符串
function createNonceNum($length = 16) {
    $chars = "0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}






function autchPhone($value){
    $rule = "/^1[3,5,8]\d{9}$/";

    return 	preg_match($rule,$value)===1;
}



function getUnameByUid($uid){
    $db = &db();
    if(!$uid)
        return "rctailor会员";

    $sql = "select user_name,nickname from cf_member where user_id = ".$uid;
    $user = $db->getRow($sql);

    if(!$user)
        return "游客";

    if($user['nickname'])
        return $user['nickname'];

    if( autchPhone( $user['user_name']))
        return getHidePhone($user['user_name']);

    if( autchemail( $user['user_name']))
        return getHideEmail($user['user_name']);

    return $user['user_name'];


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
	$data['abstract']=!empty($abstract)?$abstract:'';
	if(is_array($data['abstract'])){
		$data['abstract']=join(',',$data['abstract']);
	}
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