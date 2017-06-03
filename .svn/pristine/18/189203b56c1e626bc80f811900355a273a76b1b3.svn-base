<html>
<head>
	<title>HTTP-Client</title>
</head>
</html>
<?php
// http状态:

// 1001:ip不允许访问
// 1002:POST值为空
// 2001:XML请求格式错误
// 2002:非法的请求:请求的APPID错误
// 2003:非法的请求:KEY错误
// 2004:非法的请求:md5值错误

// 3001:此类不允许访问
// 3002:类不存在
// 3003:方法不存在
// 3004:用户传输的参数个数与实际的个数不同
// 3005:参数名有误
// 3006:参数值有问题

// 返回信息会有几种错误，错误信息会保存在：<errorMsg></errorMsg>标签中
// 1 字段名错误
// 2 添加字段时，有几个字段是不能为空，必填定
// 3 值错误
// 4 添加字段时，用户已存在
// 5 更新的时候，有些值是不允许更新的

//返回值分为头跟内容，其中头部中的：<status></status>200,就证明此次访问成功
//返回值的数据存在:<bidy></body>中

// include '../class/auth.class.php';
$appID = "101";//应用程序的ID
$key = "gamebean";//密钥
$curr_time = time();//提交的时间
$CTID = md5($key.$appID.$curr_time);

//添加用户
//必填写字段：'username','password','regtime','lasttime'
//选填:'money','email','regip','lastip','access','maze','channelid','gid','sub_channelid','source','point','integral','level'
/*
zj518849
xcy2012
jixiancpm
qiyou001
yite001
1717gs001
shaile001
 */
$ps = md5(123456);
$uname = '1717gs001';
$xmlAdd = <<<EOD
<?xml version="1.0" ?>
<msg>
	<Head>
		<appid>$appID</appid>
	    <ctid>$CTID</ctid>
	    <submittime>$curr_time</submittime>
	    <class>User</class>
	    <method>add</method>
	    <parameter type="simple_array" name="data">
	    	<element name="username">$uname</element>
	    	<element name="password">$ps</element>
	    	<element name="regip"></element>
	    	<element name="idcard">152123198712290634</element>

	    	<element name="phone"></element>
	    	<element name="money"></element>
	    	<element name="lastip"></element>
	    	<element name="access"></element>
	    	<element name="channelid"></element>
	    	<element name="gid"></element>
	    	<element name="sub_channelid"></element>
	    	<element name="source"></element>
	    	<element name="point"></element>
	    	<element name="level"></element>
	    </parameter>
	    <parameter type="simple_array" name="detail">
	    	<element name="nickname">zhifu</element>
	    </parameter>
  	</Head>
</msg>
EOD;
//根据用户名删除用户
$xmlDel = <<<EOD
<?xml version="1.0" ?>
<msg>
<Head>
<appid>$appID</appid>
<ctid>$CTID</ctid>
<submittime>$curr_time</submittime>
<class>User</class>
<method>delByUName</method>
<parameter name="uname">mqzhifui234i</parameter>
</Head>
</msg>
EOD;
//根据用户名更新用户信息,其中:username，禁止修改
$xmlUp = <<<EOD
<?xml version="1.0" ?>
<msg>
<Head>
<appid>$appID</appid>
<ctid>$CTID</ctid>
<submittime>$curr_time</submittime>
<class>User</class>
<method>upByUNAME</method>
<parameter type="simple_array" name="data">
	    	<element name="email">mm</element>
</parameter>
<parameter name="uname">mqzhifu</parameter>
</Head>
</msg>
EOD;
//用户登录
$ps = md5("mqzhifu");//注意这里要md5,安全
$xmlLogin = <<<EOD
<?xml version="1.0" ?>
<msg>
<Head>
<appid>$appID</appid>
<ctid>$CTID</ctid>
<submittime>$curr_time</submittime>
<class>User</class>
<method>loginuser</method>
<parameter name="uname">mqzhifu</parameter>
<parameter name="ps">$ps</parameter>
</Head>
</msg>
EOD;
//用户名是否已存在
$xmlEqual = <<<EOD
<?xml version="1.0" ?>
<msg>
<Head>
<appid>$appID</appid>
<ctid>$CTID</ctid>
<submittime>$curr_time</submittime>
<class>User</class>
<method>unameEqualWeb</method>
<parameter name="uname">mqzhifu</parameter>
</Head>
</msg>
EOD;

$xmlTest = <<<EOD
<?xml version="1.0" ?>
<msg>
<Head>
<appid>$appID</appid>
<ctid>$CTID</ctid>
<submittime>$curr_time</submittime>
<class>User</class>
<method>test</method>
<parameter name="uname">mqzhifu</parameter>
</Head>
</msg>
EOD;


$post_data = array(
		"xmlData=$xmlTest"
);

$post_data = implode('&',$post_data);
//$url='http://121.101.215.14/zhifu/webserver/http/httpService.php';
 $url='http://127.0.0.1/mytest/myFrame/webserver/http/httpService.php';
$ch = curl_init();
echo "in client...POST data Server...<br/>";
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

echo $result;
