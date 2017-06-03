<?php
define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/upload/phpqrcode/');//定义二维码地址
function print_exit($str){
    echo '<pre>';
    print_r($str);
    exit;
}
function print_pre($str){
    echo '<pre>';
    print_r($str);
}

/**
 *会员加减积分之后重新生成会员等级
 *@author liang.li <1184820705@qq.com>
 *@2015年7月6日
 */
// function reloadMember($user_id)
// {

//     $mod = m('member');
//     $m_info = $mod->get_info($user_id);

//     if (!$m_info || $m_info['serve_type'] !=1 || $m_info['member_lv_id'] <=1|| $m_info['member_lv_id'] >3)
//     {
//         return false;
//     }

//     $point = $m_info['point'];
//     $lv_mod =& m('memberlv');
//     $list = $lv_mod->find("lv_type='supplier'");
//     //    array_pop($list);
//     $lv_id = 0;
//     if ($list)
//     {
//         foreach ($list as $key => $value)
//         {
//             $experience = $value['experience'];
//             if ($point >= $experience && $key>$m_info['member_lv_id'] )
//             {
//                 $lv_id = $key;
//                 $lv_name = $value['name'];
//             }
//         }
//     }

//     if ($lv_id)
//     {
//         $mod->edit($user_id,array('member_lv_id'=>$lv_id));

//         //=====  给会员发信鸽  =====
//         sendSystem($user_id, 12,
//         '恭喜，您已成为'.$lv_name.'！',  '尊敬的麦富迪会员，您的积分已达到'.$point.',恭喜您已升级为'.$lv_name.'。随着积分的累计,您的等级头衔将提升，从而享受更优惠的折扣，拥有更多的特权。请密切关注你的积分变化。') ;
//     }

//     return true;
// }

/**
 * 加密算法 修复密码中带+ / & 导致无法传参的问题
 * @version 1.0.0
 * @author liang.li <1184820705@qq.com>
 * @2015-9-18
 */
function ApiAuthcode($string, $operation = 'DECODE', $key = 'mfd-CODE', $expiry = 0){

    if($operation == 'DECODE') {
        $string = str_replace('[a]','+',$string);
        $string = str_replace('[b]','&',$string);
        $string = str_replace('[c]','/',$string);
    }
    $ckey_length = 4;
    $key = md5($key ? $key : 'livcmsencryption ');
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
        $ustr = $keyc.str_replace('=', '', base64_encode($result));
        $ustr = str_replace('+','[a]',$ustr);
        $ustr = str_replace('&','[b]',$ustr);
        $ustr = str_replace('/','[c]',$ustr);
        return $ustr;
    }
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
                $CS->set_cache_dir(ROOT_PATH . '/temp/caches');
            break;
        }
    }

    return $CS;
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
        include_once(ROOT_PATH . '/includes/goods.base.php');
        include(ROOT_PATH . '/includes/goodstypes/' . $type . '.gtype.php');
        $class_name = ucfirst($type) . 'Goods';
        $types[$type]   =   new $class_name($params);
    }

    return $types[$type];
}


/**
 *    获取订单类型对象
 *
 *    @author    Garbin
 *    @param    none
 *    @return    void
 */
function &ot($type, $params = array())
{
    static $order_type = null;
    if ($order_type === null)
    {
        /* 加载订单类型基础类 */
        include_once(ROOT_PATH . '/includes/order.base.php');
        include(ROOT_PATH . '/includes/ordertypes/' . $type . '.otype.php');
        $class_name = ucfirst($type) . 'Order';
        $order_type = new $class_name($params);
    }

    return $order_type;
}


/**
 *    扣减余额
 *
 *    @author    yhao.bai
 *    @param    none
 *    @return    void
 */
function &ac($log , $field)
{
	$member  =& m('member');

	$account =& m('account');

	$member->changeAccount($log["user_id"], $field);

	$account->add($log);
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
		include(ROOT_PATH . '/includes/libraries/custom'.$fname.'.lib.php');
		$classname  = 'Custom'.$fname;
		$cs = new $classname();
	}

	return $cs;
}


/**
 *    获取用户头像地址
 *
 *    @author    Garbin
 *    @param     string $portrait
 *    @return    void
 */
function portrait($user_id, $portrait, $size = 'small')
{
    switch (MEMBER_TYPE)
    {
        case 'uc':
            return UC_API . '/avatar.php?uid=' . $user_id . '&amp;size=' . $size;
        break;
        default:
            return empty($portrait) ? Conf::get('default_user_portrait') : $portrait;
        break;
    }
}

/**
 *    获取环境变量
 *
 *    @author    Garbin
 *    @param     string $key
 *    @param     mixed  $val
 *    @return    mixed
 */
function &env($key, $val = null)
{
    !isset($GLOBALS['EC_ENV']) && $GLOBALS['EC_ENV'] = array();
    
    $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'EC_ENV\']') : '$GLOBALS[\'EC_ENV\']';
    if ($val === null)
    {
        /* 返回该指定环境变量 */
        $v = eval('return isset(' . $vkey . ') ? ' . $vkey . ' : null;');

        return $v;
    }
    else
    {
        /* 设置指定环境变量 */
        eval($vkey . ' = $val;');

        return $val;
    }
}

/**
 *    获取订单状态相应的文字表述
 *
 *    @author    Garbin
 *    @param     int $order_status
 *    @return    string
 */
function order_status($order_status)
{
    $lang_key = '';
    switch ($order_status)
    {
        case ORDER_PENDING:
            $lang_key = 'order_pending';
        break;
        case ORDER_SUBMITTED:
            $lang_key = 'order_submitted';
        break;
        case ORDER_ACCEPTED:
            $lang_key = 'order_accepted';
        break;
        case ORDER_SHIPPED:
            $lang_key = 'order_shipped';
        break;
        case ORDER_FINISHED:
            $lang_key = 'order_finished';
        break;
        case ORDER_CANCELED:
            $lang_key = 'order_canceled';
        break;
        case ORDER_FIGURE:
        	$lang_key = 'order_figure';
        break;
       case ORDER_PRODUCTION:
        	$lang_key = 'order_production';
        break;
       case ORDER_TUIKUANZHONG:
        	$lang_key = 'order_tuikuanzhong';
        break;
       case ORDER_YINGTUIKUAN:
        	$lang_key = 'order_yingtuikuan';
        break;
    }

    return $lang_key  ? Lang::get($lang_key) : $lang_key;
}

/**
 *    转换订单状态值
 *
 *    @author    Garbin
 *    @param     string $order_status_text
 *    @return    void
 */
function order_status_translator($order_status_text)
{
    switch ($order_status_text)
    {
        case 'canceled':    //已取消的订单
            return ORDER_CANCELED;
        break;
        case 'all':         //所有订单
            return '';
        break;
        case 'pending':     //待付款的订单
            return ORDER_PENDING;
        break;
        case 'submitted':   //已提交的订单
            return ORDER_SUBMITTED;
        break;
        case 'accepted':    //已确认的订单，待发货的订单
            return ORDER_ACCEPTED;
        break;
        case 'shipped':     //已发货的订单
            return ORDER_SHIPPED;
        break;
        case 'finished':    //已完成的订单
            return ORDER_FINISHED;
        break;
        default:            //所有订单
            return '';
        break;
    }
}

/**
 *    获取邮件内容
 *
 *    @author    Garbin
 *    @param     string $mail_tpl
 *    @param     array  $var
 *    @return    array
 */
function get_mail($mail_tpl, $var = array())
{
    $subject = '';
    $message = '';

    /* 获取邮件模板 */
    $model_mailtemplate =& af('mailtemplate');
    $tpl_info   =   $model_mailtemplate->getOne($mail_tpl);
    if (!$tpl_info)
    {
        return false;
    }

    /* 解析其中变量 */
    $tpl =& v(true);
    $tpl->direct_output = true;
    $tpl->assign('site_name', Conf::get('site_name'));
    $tpl->assign('site_url', SITE_URL);
    $tpl->assign('mail_send_time', local_date('Y-m-d H:i', gmtime()));
    foreach ($var as $key => $val)
    {
        $tpl->assign($key, $val);
    }
    $subject = $tpl->fetch('str:' . $tpl_info['subject']);
    $message = $tpl->fetch('str:' . $tpl_info['content']);

    /* 返回邮件 */

    return array(
        'subject'   => $subject,
        'message'   => $message
    );
}

/**
 *    获取消息内容
 *
 *    @author    Garbin
 *    @param     string $msg_tpl
 *    @param     array  $var
 *    @return    string
 */
function get_msg($msg_tpl, $var = array())
{
    /* 获取消息模板 */
    $ms = &ms();
    $msg_content = Lang::get($msg_tpl);
    $var['site_url'] = SITE_URL; // 给短消息模板中设置一个site_url变量
    $search = array_keys($var);
    $replace = array_values($var);

    /* 解析其中变量 */
    array_walk($search, create_function('&$str', '$str = "{\$" . $str. "}";'));
    $msg_content = str_replace($search, $replace, $msg_content);
    return $msg_content;
}

/**
 *    获取邮件发送网关
 *
 *    @author    Garbin
 *    @return    object
 */
function &get_mailer()
{
    static $mailer = null;
    if ($mailer === null)
    {
        /* 使用mailer类 */
        import('mailer.lib');
        $sender     = Conf::get('site_name');
        $from       = Conf::get('email_addr');
        $protocol   = Conf::get('email_type');
        $host       = Conf::get('email_host');
        $port       = Conf::get('email_port');
        $username   = Conf::get('email_id');
        $password   = Conf::get('email_pass');
        $mailer = new Mailer($sender, $from, $protocol, $host, $port, $username, $password);
    }

    return $mailer;
}

/**
 *    模板列表
 *
 *    @author    Garbin
 *    @param     strong $who
 *    @return    array
 */
function list_template($who)
{
    $theme_dir = ROOT_PATH . '/themes/' . $who;
    $dir = dir($theme_dir);
    $array = array();
    while (($item  = $dir->read()) !== false)
    {
        if (in_array($item, array('.', '..')) || $item{0} == '.' || $item{0} == '$')
        {
            continue;
        }
        $theme_path = $theme_dir . '/' . $item;
        if (is_dir($theme_path))
        {
            if (is_file($theme_path . '/theme.info.php'))
            {
                $array[] = $item;
            }
        }
    }

    return $array;
}

/**
 *    列表风格
 *
 *    @author    Garbin
 *    @param     string $who
 *    @return    array
 */
function list_style($who, $template = 'default')
{
    $style_dir = ROOT_PATH . '/themes/' . $who . '/' . $template . '/styles';
    $dir = dir($style_dir);
    $array = array();
    while (($item  = $dir->read()) !== false)
    {
        if (in_array($item, array('.', '..')) || $item{0} == '.' || $item{0} == '$')
        {
            continue;
        }
        $style_path = $style_dir . '/' . $item;
        if (is_dir($style_path))
        {
            if (is_file($style_path . '/style.info.php'))
            {
                $array[] = $item;
            }
        }
    }

    return $array;
}


/**
 *    获取挂件列表
 *
 *    @author    Garbin
 *    @return    array
 */
function list_widget()
{
    $widget_dir = ROOT_PATH . '/external/widgets';
    static $widgets    = null;
    if ($widgets === null)
    {
        $widgets = array();
        if (!is_dir($widget_dir))
        {
            return $widgets;
        }
        $dir = dir($widget_dir);
        while (false !== ($entry = $dir->read()))
        {
            if (in_array($entry, array('.', '..')) || $entry{0} == '.' || $entry{0} == '$')
            {
                continue;
            }
            if (!is_dir($widget_dir . '/' . $entry))
            {
                continue;
            }
            $info = get_widget_info($entry);
            $widgets[$entry] = $info;
        }
    }

    return $widgets;
}

/**
 *    获取挂件信息
 *
 *    @author    Garbin
 *    @param     string $id
 *    @return    array
 */
function get_widget_info($name)
{
    $widget_info_path = ROOT_PATH . '/external/widgets/' . $name . '/widget.info.php';

    return include($widget_info_path);
}

function i18n_code()
{
    $code = 'zh-CN';
    $lang_code = substr(LANG, 0, 2);
    switch ($lang_code)
    {
        case 'sc':
            $code = 'zh-CN';
        break;
        case 'tc':
            $code = 'zh-TW';
        break;
        default:
            $code = 'zh-CN';
        break;
    }

    return $code;
}

/**
 *    从字符串获取指定日期的结束时间(24:00)
 *
 *    @author    Garbin
 *    @param     string $str
 *    @return    int
 */
function gmstr2time_end($str)
{
    return gmstr2time($str) + 86400;
}

/**
 *    从字符串获取指定日期的结束时间(24:00)
 *
 *    @author    Daniel
 *    @param     string $str
 *    @return    int
 */
function strtotime_end($str)
{
	return strtotime($str) + 86400;
}

/**
 *    获取URL地址
 *
 *    @author    Garbin
 *    @param     mixed $query
 *    @param     string $rewrite_name
 *    @return    string
 */
function mfdurl($query, $rewrite_name = null)
{
    $re_on  = Conf::get('rewrite_enabled');
    $url = '';
    if (!$re_on)
    {
        /* Rewrite未开启 */
        $url = 'index.php?' . $query;
    }
    else
    {
        /* Rewrite已开启 */
        $re =& rewrite_engine();
        $rewrite = $re->get($query, $rewrite_name);

        $url = ($rewrite !== false) ? $rewrite : 'index.php?' . $query;
    }

    return str_replace('&', '&amp;', $url);
}

/**
 * 处理url 地址
 *
 * @author  yhao.bai
 * @param   arr   $arr 数组
 *
 * @return  str
 */
function build_url($arr){
	if(!is_array($arr)) return '';
	$url = "";

	//echo ran
	$base_url = base_request::get_base_url();

	if(isset($arr['app'])){
		$url .= $base_url."/index.php/".$arr['app'];
	}

	if(isset($arr['act']) && $url){
		$url .= "-".$arr['act'];
	}

	foreach($arr as $key => $val){
		if(preg_match("/^arg[0-9]?+$/is", $key) && $url){
			$url .= "-".$val;
		}
	}

	if($url){
		$url .= ".html";
	}

	return $url;
}

/**
 *    获取rewrite engine
 *
 *    @author    Garbin
 *    @return    Object
 */
function &rewrite_engine()
{
    $re_name= Conf::get('rewrite_engine');
    static $re = null;
    if ($re === null)
    {
        include(ROOT_PATH . '/includes/rewrite.base.php');
        include(ROOT_PATH . '/includes/rewrite_engines/' . $re_name . '.rewrite.php');
        $re_class_name = ucfirst($re_name) . 'Rewrite';
        $re = new $re_class_name();
    }

    return $re;
}

/**
 *    转换团购活动状态值
 *
 *    @author    Garbin
 *    @param     string $status_text
 *    @return    void
 */
function groupbuy_state_translator($state_text)
{
    switch ($state_text)
    {
        case 'all':         //全部团购活动
            return '';
        break;
        case 'on':         //进行中的团购活动
            return GROUP_ON;
        break;
        case 'canceled':    //已取消的团购活动
            return GROUP_CANCELED;
        break;
        case 'pending':     //未发布的团购活动
            return GROUP_PENDING;
        break;
        case 'finished':     //已完成的团购活动
            return GROUP_FINISHED;
        break;
        case 'end':     //已完成的团购活动
            return GROUP_END;
        break;
        default:            //全部团购活动
            return '';
        break;
    }
}

/**
 *    获取团购状态相应的文字表述
 *
 *    @author    Garbin
 *    @param     int $group_state
 *    @return    string
 */
function group_state($group_state)
{
    $lang_key = '';
    switch ($group_state)
    {
        case GROUP_PENDING:
            $lang_key = 'group_pending';
        break;
        case GROUP_ON:
            $lang_key = 'group_on';
        break;
        case GROUP_CANCELED:
            $lang_key = 'group_canceled';
        break;
        case GROUP_FINISHED:
            $lang_key = 'group_finished';
        break;
        case GROUP_END:
            $lang_key = 'group_end';
        break;
    }

    return $lang_key  ? Lang::get($lang_key) : $lang_key;
}


/**
 *    计算剩余时间
 *
 *    @author    Garbin
 *    @param     string $format
 *    @param     int $time;
 *    @return    string
 */
function lefttime($time, $format = null)
{
    $lefttime = $time - gmtime();
    if ($lefttime < 0)
    {
        return '';
    }
    if ($format === null)
    {
        if ($lefttime < 3600)
        {
            $format = Lang::get('lefttime_format_1');
        }
        elseif ($lefttime < 86400)
        {
            $format = Lang::get('lefttime_format_2');
        }
        else
        {
            $format = Lang::get('lefttime_format_3');
        }
    }
    $d = intval($lefttime / 86400);
    $lefttime -= $d * 86400;
    $h = intval($lefttime / 3600);
    $lefttime -= $h * 3600;
    $m = intval($lefttime / 60);
    $lefttime -= $m * 60;
    $s = $lefttime;

    return str_replace(array('%d', '%h', '%i', '%s'),array($d, $h,$m, $s), $format);
}


/**
 * 多维数组排序（多用于文件数组数据）
 *
 * @author Hyber
 * @param array $array
 * @param array $cols
 * @return array
 *
 * e.g. $data = array_msort($data, array('sort_order'=>SORT_ASC, 'add_time'=>SORT_DESC));
 */
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}

/**
 * 短消息过滤
 *
 * @return string
 */
function short_msg_filter($string)
{
    $ms = & ms();
    return $ms->pm->msg_filter($string);
}
/**
 * 生成唯一ID产生位数最短的随机唯一ID
 * Enter description here ...
 */
function getRandOnlyId() {
        //新时间截定义,基于世界未日2012-12-21的时间戳。
        $endtime=1356019200;//2012-12-21时间戳
        $curtime=time();//当前时间戳
        $newtime=$curtime-$endtime;//新时间戳
        $rand=rand(0,99);//两位随机
        $all=$rand.$newtime;
        $onlyid=base_convert($all,10,36);//把10进制转为36进制的唯一ID
        return $onlyid;
}

/**
 * 生成二维码
 * @param  $type	类型 ：goods商品图|...
 * @param  $id		文件名称加密id
 * @param  $text	生成 文字或是链接
 * @param  $size	1:33*33;2:66*66;3:99*99;4:132*132
 * @return 没有返回值
 */
function Qrcode($type='goods',$id=1000,$text='http://rctailor.ec51.com.cn/', $avatar=''){

    /* 引用phparcode */
    include (ROOT_PATH.'/phpqrcode/full/qrlib.php');
    /* 定义二维码图片存放路径 */
    //define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/phpqrcode/image/');

    $errorCorrectionLevel = 'H';
// 	$matrixPointSize = 4;

    foreach (array(1,2,3,4,10) as $size){
        $matrixPointSize = $size;

        $tempDir = EXAMPLE_TMP_SERVERPATH;
        $codeContents = $id;
        /* 扩展 类型 ：goods 商品id */
        $fileName = $type."/".$type.'_'.md5($codeContents).'_'.$size.'.png';
        $pngAbsoluteFilePath = $tempDir.$fileName;

        /**
         * 第一个参数$text，就是上面代码里的URL网址参数，
         * 第二个参数$outfile默认为否，不生成文件，只将二维码图片返回，否则需要给出存放生成二维码图片的路径
         * 第三个参数$level默认为L，这个参数可传递的值分别是L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）。
         * 			这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比。利用二维维码的容错率，我们可以将头像放置在生成的二维码图片任何区域。
         * 第四个参数$size，控制生成图片的大小，默认为4
         * 第五个参数$margin，控制生成二维码的空白区域大小
         * 第六个参数$saveandprint，保存二维码图片并显示出来，$outfile必须传递图片路径
         */
        @ecm_mkdir(EXAMPLE_TMP_SERVERPATH.'/'.$type);

        @chmod($fileName, 0777);
        $logo = $avatar;
        if(empty($logo)) {
            $logo = ROOT_PATH.'/phpqrcode/rctailor.png';
        }

        $QR = $tempDir.$fileName;

        if(file_exists($QR)){ // 已存在删除文件
            unlink($QR);
        }

        QRcode::png($text, $pngAbsoluteFilePath, $errorCorrectionLevel, $matrixPointSize);

        /* 二维码合成 logo */
        if($logo !== FALSE){
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }

        @imagepng($QR,$tempDir.$fileName);
// 		return SITE_URL.'/phpqrcode/image/'.$fileName;

// 		return false;
    }
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




//发送短信接口-wangdy
/**
 * setConf 设置配置文件
 * @param  $file    配置文件
 * @param  $key		下标
 * @param  $val		值
 * @param  $reset   是否重置
 * @return boolean
 * @author Ruesin
 */
function setConf($file='',$key='',$val='',$reset=false){
    //本来想把file和key写在一起的,这样参数会少一个,但是后来考虑还是多写个参数比较方便
    //set的时候要不然封装到后台专用的????//ecstore也没封装成仅后台使用,就先这么用吧
    if($file=='' || $key=='')return 0;
    $config=array();
    $dir=ROOT_PATH . '/data/config/'.$file.'.php';
    $str = "<?php exit(); ?>";
    if(is_file($dir)){
        if($reset==false){
            $fileStr=file_get_contents($dir);
            $config=unserialize(str_replace($str, '', $fileStr));
        }
        $config[$key]=$val;
        $str.=serialize($config);
    }else{
        $config[$key]=$val;
        $str.=serialize($config);
    }
    if(file_put_contents($dir, $str))
    return 1;
    return 0;
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
   	$dir=ROOT_PATH . '/data/config/'.$file.'.php';
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
 * 递归创建目录
 * @param $string 目录
 * @return boolean
 * @author Ruesin
 */
function mkDirs($dir){
    if(!is_dir($dir))  {
        if(!mkDirs(dirname($dir))){
            return false;
        }
//         if(mkdir($dir,0777)){
//            chmod($dir, 0777);
//         }else{
//             return false;
//         }
        if(!mkdir($dir,0777)){
            return false;
        }
        if(!chmod($dir, 0777)){
            return false;
        }
    }
    return true;
}
//判断手机移动设备访问【排除ipad】
function is_mobile_request()
{
	$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	$mobile_browser = '0';
// 	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
		$mobile_browser++;
	if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))// 协议法，因为有可能不准确，放到最后判断
		$mobile_browser++;
	if(isset($_SERVER['HTTP_X_WAP_PROFILE']))// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		$mobile_browser++;
	if(isset($_SERVER['HTTP_PROFILE']))
		$mobile_browser++;
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda','xda-'
	);
	if(in_array($mobile_ua, $mobile_agents)) // 从HTTP_USER_AGENT中查找手机浏览器的关键字
		$mobile_browser++;
	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
		$mobile_browser++;
	// Pre-final check to reset everything if the user is on Windows
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
		$mobile_browser=0;
	// But WP7 is also Windows, with a slightly different characteristic
	if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
		$mobile_browser++;
	if($mobile_browser>0)
		return true;
	else
		return false;
}


function fbInfo($ids = array()){
    //$ids = array("7000106", '7000105', '7000104');
    $attribute = &m("partattribute");
    $attrs = $attribute->find(array(
       'conditions' => 'type_id = 1',
        'order' => 'attr_id ASC'
    ));

   $partAttr = &m("partattr");

   $parts = $partAttr->find(array(
      'conditions' => 'part_id '. db_create_in($ids),
   ));

   $fabs = array();

   foreach((array)$parts as $key => $val){
       if(!isset($fabs[$val['part_id']])){
           foreach((array)$attrs as $k => $v){
               $fabs[$val['part_id']][$v['attr_id']] = '';
           }
       }
       $fabs[$val['part_id']][$val['attr_id']] = $val['attr_value'];
   }

   return array('fabs' => $fabs, 'attrs' => $attrs);
}

/**
* 发送站内信息
* @param $arr  array
* @return res
* @access public
* @see sendMessage
* @version 1.0.0 (2015-1-3)
* @author yhao.bai
*
*  $arr(
*     'to_user_id'     => 发送到用户id
*     'location_url'   => 跳转地址
*     'price'          => 3000
*     'type            => 1.交易提醒, 2.晒单评论, 3.订单评论, 4.消费者发需求, 5.裁缝发作品, 6.裁缝发报价,7中标,[8,9,10]投诉处理，11作品评选评论
*  )
*/

function sendMessage($arr){

    $visitor = &env('visitor');
    $addtime = gmtime();
    if(!isset($arr['from_user_id'])){
        $arr['from_user_id'] = $visitor->get("user_id");
        $arr['from_nickname'] = $visitor->get("nickname");
    }
    extract($arr);

    $message = $GLOBALS['__ECLANG__']['Umsg'];

    unset($arr['price']);
    unset($arr['img']);
    unset($arr['reason']);
    unset($arr['c_content']);
    unset($arr['name']);

    switch($type){
        case 6:
            $arr['content'] = sprintf($message[$type], $from_nickname, $price, $location_url);
            break;
        case 8:
            $arr['content'] = sprintf($message[$type], $from_nickname, $c_content);
            break;
        case 9:
            $arr['content'] = sprintf($message[$type], $name, $reason, $c_content,$img);
            break;
        case 10:
            $arr['content'] = sprintf($message[$type], $name, $reason, $c_content,$img);
            break;
        case 11:
            $arr['content'] = sprintf($message[$type], $from_nickname,$c_content);
            break;
        default:
            $arr['content'] = sprintf($message[$type], $from_nickname, $location_url);
            break;

    }

    $arr['add_time'] = $addtime;

    $_message_mod = &m("usermessage");

    if($type == 4 || $type == 5){
        $arr = array();
        $custom_figure_mod = &m("customer_figure");
        $conditions = ' 1 ';
        if($type == 4){
            $conditions .= " AND userid = '{$from_user_id}'";
        }

        if($type == 5){
            $conditions .= " AND storeid = '{$from_user_id}'";
        }

        $tailors = $custom_figure_mod->find(array(
           'conditions' => $conditions,
           'fields'     => "storeid, userid",
        ));

        if($type == 5){
            //$from_user_id
            $uf_mod = &m("userfollow");
            $flist = $uf_mod->find(array(
                'conditions' => "follow_uid='{$from_user_id}'",
            ));
        }

        $_users = array();
        foreach((array)$tailors as $key => $val){
               $_users[] = $val['userid'];
               $arr[] = array(
                   'from_user_id' => $from_user_id,
                   'to_user_id'   => $type == 4 ? $val['storeid'] : $val['userid'],
                   'from_nickname' => $from_nickname,
                   'location_url'  => $location_url,
                   'type' => $type,
                   'content'    => sprintf($message[$type], $from_nickname, $location_url),
                   'add_time'   => $addtime
               );
          }

          foreach((array) $flist as $key => $val){
              if(!in_array($val["uid"], $_users)){
                  $arr[] = array(
                      'from_user_id' => $from_user_id,
                      'to_user_id'   => $val['uid'],
                      'from_nickname' => $from_nickname,
                      'location_url'  => $location_url,
                      'type' => $type,
                      'content'    => sprintf($message[$type], $from_nickname, $location_url),
                      'add_time'   => $addtime
                  );
              }
          }
    }
    $_message_mod->add($arr);
	return array('addtime'=> $addtime, 'type'=> $type);

}

/**
* 发送app 推送信息
* @param $addtime  我的消息添加时间 $type 消息类型
* @return res
* @access public
* @see sendMessage
* @version 1.0.0 (2015-1-3)
* @author yhao.bai
*
*/
function sendXingeApp($addtime, $type){
	//发送消息推送
	//include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
	//$push = new XingeMeg($addtime, $type);
}

/**
* 添加公共数据
* @param int $id 当前数据主键
* @param str $type 类型[fabric:面料,goods:商品,store:裁缝]
* @param str $title 当前数据供搜索内容
* @return void
* @access public
* @see addCommon
* @version 1.0.0 (2015-1-5)
* @author yhao.bai
*/
function addCommon($id, $type, $title){
    if(!$id || !$type || !$title){
        return;
    }

   $common_mod = &m("commondata");
   $conditions = "type='{$type}' AND link_id = '{$id}'";
   $res = $common_mod->_count($conditions);
   if($res){
       $common_mod->edit($conditions,array('title' => $title));
   }else{
       $common_mod->add(array(
           'link_id' => $id,
           'type'    => $type,
           'title'   => $title,
       ));
   }
}
?>