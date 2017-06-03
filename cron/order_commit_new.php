<?php
/**
 * 套装推送融合处理.


echo "Have been abandoned";exit();


chdir(dirname(__FILE__));// cd 到php脚本所在的目录


$end = "\r\n";
echo "start:time".date("Y-m-d H:i:s").$end;
$start_time = time();
include_once 'order_func.php';
/* 定制所需配置一些信息 */
include('../includes/constants.base.php');
initDb();

$sql = "select * from cf_order_cron where status = 0 ";
/* 测试 5件商品 2件diy套装 2件样衣套装 一件单品  */
// $sql = "SELECT * FROM `cf_order_cron` WHERE `fabric_id` IN (7018,7017,7071,7070,6996) ORDER BY `fabric_id` DESC ";
/* 测试 diy 套装推送 */
//$sql = "SELECT * FROM `cf_order_cron` WHERE `fabric_id` IN (7018,7017) ORDER BY `fabric_id` DESC ";
$data_list = getAlls($sql);
if(!$data_list)
    exit(" no data process...".$end);

echo " data count:".count($data_list).$end;

$url = "http://api.rcmtm.com:7070/order-api/resources/orderService";


/* 做溶于处理.是否套装 */
$rec_ids = i_array_column($data_list, 'fabric_id');

$sql = "SELECT * FROM `cf_order_goods` WHERE `rec_id` IN ('".implode(array_unique($rec_ids), "','")."')";

$goods_data = getAll($sql);
$itmes = array();
foreach ($goods_data as $gk=>$gv){
	/* 套装条件：diy ｜西服＋西裤 ｜数量1件（多件匹配有问题） */
	if ($gv['type'] == 'diy' && in_array($gv['cloth'], array('0003','0004')) && $gv['quantity'] == 1){
		$itmes[$gv['type'].'_'.$gv['dis_ident'].'_'.$gv['fabric']][$gv['cloth']] = $gv;
// 		$itmes[$gv['type'].'_'.$gv['dis_ident'].'_'.$gv['fabric']][$gv['cloth']] = $gv['rec_id'];
	}else{
		$itmes[$gv['type'].'_'.$gv['cloth'].'_'.$gv['goods_id']][$gv['cloth']] =$gv;
// 		$itmes[$gv['type'].'_'.$gv['cloth'].'_'.$gv['goods_id']][$gv['cloth']] =$gv['rec_id'];
	}
}
foreach ($itmes as $k=>$v){
	
	if (count($v) == 2 && md5(array_keys($v)) == md5(array('0003','0004')) ){
		$rid =  i_array_column($v, 'rec_id');
		$oid = array_unique(i_array_column($v, 'order_id'));
		$data = getXml2psc($v);
		$v['order_id'] = current($oid);
	}else{
		$v = current($v);
		$rid =  array($v['rec_id']);
		$data = getXml($v);
	}
	
	$content = addslashes($data);
	$sql = "INSERT INTO cf_rcmtm_log (order_id, rec_id, content) VALUES ('{$v['order_id']}', '".implode(array_unique($rid), ",")."', '{$content}')";
	mysql_query($sql);
	
}

/**
 * 套装推送
 * @param array $order
 * @return sing   $xml 
 */
function getXml2psc($order){
	$defCloth = '0003';
	$xml_data = array();
	$tunwei = array();
	$amount = 0;
	foreach ($order as $k=>$v){
		$xml_arr = array();
		$xml_arr = XML2Array(simplexml_load_string(getXml($v)));
		/*三件套或二件套时，臀围(10108)的传值格式为：10108:西服臀围值|西裤臀围值 */
		if ($xml_arr['OrderInformation']['ClothingSize']){
			$tunwei[$k] = strCutByStr($xml_arr['OrderInformation']['ClothingSize'],'10108:',',');
			$xml_arr['OrderInformation']['ClothingSize'] = str_replace('10108:'.strCutByStr($xml_arr['OrderInformation']['ClothingSize'],'10108:',',').',','',$xml_arr['OrderInformation']['ClothingSize']);
		}
		$amount += $xml_arr['OrderInformation']['amount'] ;
		$xml_data[$k]  = $xml_arr;
		
	}


	$xml = <<<EOT
<Order>		
	<!--订单信息-->	
	<OrderInformation>	
		<OrderDate>{$xml_data[$defCloth]['OrderInformation']['OrderDate']}</OrderDate><!--提交订单时间-->
		<Createman>{$xml_data[$defCloth]['OrderInformation']['Createman']}</Createman><!--用户名-->
		<Password>{$xml_data[$defCloth]['OrderInformation']['Password']}</Password><!--密码 可不填-->
		<ClothingID>1</ClothingID><!-- 服装种类(套装2psc、套装3psc、上衣、西裤、衬衣、马夹、配件、大衣)-->
		<SizeCategoryID>{$xml_data[$defCloth]['OrderInformation']['SizeCategoryID']}</SizeCategoryID><!--尺寸分类(标准号加减、成衣尺寸、净体量体)-->
		<SizeAreaID>{$xml_data[$defCloth]['OrderInformation']['SizeAreaID']}</SizeAreaID><!--尺寸区域码(亚码、欧码、英美码、澳码)--><!--尺寸分类为标准号加减时必填，其他可不填-->
		<Fabrics>{$xml_data[$defCloth]['OrderInformation']['Fabrics']}</Fabrics><!--面料-->
		<SizeUnitID>{$xml_data[$defCloth]['OrderInformation']['SizeUnitID']}</SizeUnitID><!--尺寸单位(厘米、英寸)-->
		<ClothingStyle>{$xml_data[$defCloth]['OrderInformation']['ClothingStyle']}</ClothingStyle><!--版型风格(正常款，长款，短款) 默认：20100 无长短款的分类，添20100即可-->
		<CustormerBody>{$xml_data[$defCloth]['OrderInformation']['CustormerBody']}</CustormerBody><!--客户体型(左右肩、背、肚、手臂、臀)--><!--尺寸分类为标准号加减时可不填，其他必填-->
		<ClothingSize>{$xml_data[$defCloth]['OrderInformation']['ClothingSize']},{$xml_data['0004']['OrderInformation']['ClothingSize']},10108:{$tunwei['0003']}|{$tunwei['0004']}</ClothingSize><!--量体部位尺寸信息 格式遵从: 部位ID:尺寸,部位ID:尺寸....--><!-- 三件套或二件套时，臀围(10108)的传值格式为：10108:西服臀围值|西裤臀围值 -->
		<From>{$xml_data[$defCloth]['OrderInformation']['From']}</From>
		<Save>{$xml_data[$defCloth]['OrderInformation']['Save']}</Save><!--不填表示直接提交到生产，填1则先保存到下单系统，不提交到生产-->
		<dy></dy><!-- 如果服装分类是大衣，此字段表示：大衣是否套西服（0A02套 0A01不套），不填表示套西服，其他服装此字段无意义-->
		<UserNo></UserNo><!-- 客户订单号，适用于客户需要保存自己订单号的情况，可为空且可删除此项 -->
		<amount>{$amount}</amount><!-- 订单金额，北京电商用 -->
	</OrderInformation>	
	<!--订单上顾客基本信息-->	
	<CustomerInformation>	
		<CustomerName>{$xml_data[$defCloth]['CustomerInformation']['CustomerName']}</CustomerName><!--顾客姓名-->
		<Height>{$xml_data[$defCloth]['CustomerInformation']['Height']}</Height><!--身高-->
		<HeightUnitID>{$xml_data[$defCloth]['CustomerInformation']['HeightUnitID']}</HeightUnitID><!--身高单位(厘米、英寸)-->
		<Weight>{$xml_data[$defCloth]['CustomerInformation']['Weight']}</Weight><!--体重--><!--可不填-->
		<WeightUnitID>{$xml_data[$defCloth]['CustomerInformation']['WeightUnitID']}</WeightUnitID><!--体重单位(千克、英镑)--><!--可不填-->
		<Email>{$xml_data[$defCloth]['CustomerInformation']['Email']}</Email><!--邮件--><!--可不填-->
		<Address>{$xml_data[$defCloth]['CustomerInformation']['Address']}</Address><!--地址--><!--可不填-->
		<Tel>{$xml_data[$defCloth]['CustomerInformation']['Tel']}</Tel><!--电话--><!--可不填-->
		<GenderID>{$xml_data[$defCloth]['CustomerInformation']['GenderID']}</GenderID><!--性别(男，女)-->
	</CustomerInformation>	

	<OrderDetails>	
	<OrderDetailsTag>		
	</OrderDetails>				
</Order>	
EOT;
	
	

	    $str = '';
			$str .="<OrderDetail id = '{$k}'><!-- 订单和刺绣信息存在多个时用id来区分-->
			<!--单条订单明细信息-->		
			<OrderDetailInformation>		
				<SizeSpecChest>{$val['OrderDetails']['OrderDetail']['OrderDetailInformation']['SizeSpecChest']}</SizeSpecChest><!--胸围规格--><!--可不填-->	
				<SizeSpecHeight>{$val['OrderDetails']['OrderDetail']['OrderDetailInformation']['SizeSpecHeight']}</SizeSpecHeight><!--衣服大小号--><!--尺寸分类为标准号加减时必填，其他可不填-->	
				<Categories>{$val['OrderDetails']['OrderDetail']['OrderDetailInformation']['Categories']}</Categories><!--服装种类(上衣) -->	
				<Quantity>{$val['OrderDetails']['OrderDetail']['OrderDetailInformation']['Quantity']}</Quantity><!--数量 --> 	
				<BodyStyle>{$val['OrderDetails']['OrderDetail']['OrderDetailInformation']['BodyStyle']}</BodyStyle><!--着装风格-->	
			</OrderDetailInformation>";
			if (isset($val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Content']) && !empty($val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Content'])){
				$str .="	<!--订单明细刺绣信息 如果没有刺绣信息，则EmbroideryProcess整个标签必须删掉-->	
    			<EmbroideryProcess>		
    				<Embroidery id = '1'>	
    				    <Location>{$val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Location']}</Location><!--位置 -->	
    					<Font>{$val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Font']}</Font><!--字体-->
    					<Color>{$val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Color']}</Color><!--颜色 -->
    					<Content>{$val['OrderDetails']['OrderDetail']['EmbroideryProcess']['Embroidery']['Content']}</Content><!--内容 -->
    					<Size></Size><!--绣字大小（衬衣） -->
    				</Embroidery>
    			</EmbroideryProcess>";
			}		
			$str .= "<OrdersProcess>{$val['OrderDetails']['OrderDetail']['OrdersProcess']}</OrdersProcess><!--单条订单明细工艺信息 -->	
			<OrdersProcessContent>{$val['OrderDetails']['OrderDetail']['OrdersProcessContent']}</OrdersProcessContent><!--面料标、商标、客户指定等内容 -->	
		</OrderDetail>";
			$k++;
	return $xml;


function getXml($order){
	//订单信息
	$orderInfo   = array();
	$orderFigure = array();
	$orderExtm   = array();
	$orderGoods  = array();
	
	/* 扣子、锁眼线、钉扣线、袖里搭配   默认顺色id*/
	$separate_process = array(1343,1344,1345,2613,2614,2615,4648,4649,4650,3707,52532,3618,3706,6346,6357,1339);

	$sql = "SELECT * FROM cf_order WHERE order_id = '{$order['order_id']}'";
	$data = getAlls($sql);
	$orderInfo = current($data);

	//配送信息 $orderInfo['shipping_id']  1是物流 ， 2是门店
// 	$orderExtm['consignee'] = iconv('GBK','UTF-8',$orderInfo['ship_name']);
	$orderExtm['address'] = $orderInfo['ship_addr'];
	
	if ($orderInfo['shipping_id'] == 2){
		$sql = "SELECT * FROM `cf_serve` WHERE `idserve` = '{$orderInfo['ship_store']}'";
		$orderExtm['consignee'] = $orderInfo['ship_name'];
		if (empty($orderExtm['consignee'])){
			$orderExtm['consignee'] = $data['linkman'];
		}

	
// 	$sql = "SELECT * FROM cf_order_goods WHERE order_id = '{$order['order_id']}' AND rec_id = '{$order['fabric_id']}'";
	
	$orderDate      = date("Y-m-d", $orderInfo['add_time']);
	
	/* 计算价格由 信 统一处理 2015.10.20 */
	$markprice         = sprintf("%.2f", $orderGoods['markprice']);
	
	
	//量体数据
	$_cs = array();
	
// 	if($orderInfo['has_measure'] == 1 && $orderGoods['size'] == 'diy'){
		$short = getShortMark($orderGoods['cloth'],$paramsData);
		
		if($orderFigure){
			//女衬衣
			//男短裤
			$_cs['15000'] = array('figure' => array(

		$height = $orderFigure['lheight'];
			/* 10097 背：默认值  body_type_3000 衬衣着装风格*/
			
			$styleDY = '';
			if ('6000' == $clothingID){
				$styleDY = $orderFigure['styleDY'];
			}
		
		/* 获取量体人员资格证 */
		if (isset($orderFigure['liangti_id']) && !empty($orderFigure['liangti_id'])){
			$sql = "SELECT * FROM `cf_figure_liangti` WHERE `liangti_id` = '{$orderFigure['liangti_id']}'";
			$ltname = $ltdata['card_number'];
		}elseif (isset($orderFigure['server_id']) && !empty($orderFigure['server_id'])){//店长
			$sql = "SELECT * FROM `cf_serve` WHERE `idserve` = '{$orderFigure['server_id']}'";
		}
		
	
	

	if ($orderGoods['size'] != 'diy'){
		$sizeAreaID = 10205;
		$jsonData = getSizeData($orderGoods['cloth'],$sizeAreaID,$paramsData);		
		$figureTmp = array();
		foreach ($jsonData['standardAll'][$orderGoods['size']] as $s){
			$figureTmp[] = $s['Id'].':'.$s['DefaultValue'];
		}
		$_cs[$clothingID]['figure'] = $figureTmp;
		
		$size = explode("/",$orderGoods['size']);
		$weight = ereg_replace("[^0-9\.-]",'',$size[1]);
		
		$clothingSize   =  @implode(",",$_cs[$clothingID]['figure']);
	}


	//刺绣信息
	//---
	//---   主题加入购物车失败：因为没有测秀信息. 类型：dis
	//---
	if ($orderGoods['embs']){
		$embsData = array();
		$embsData = json_decode($orderGoods['embs'],1);
		foreach ($embsData as $emk => $emv){
			if (in_array($emk, Constants::$fontParent)){//字体
				$font = $emv;
			}elseif (in_array($emk, Constants::$colorParent)){//颜色
				$color = $emv;
			}elseif (in_array($emk, Constants::$locationParent)){//位置 这里 需要调整下，有的位置没有经过转化 35954->34270
				foreach ($emv as $lemv){
					$location[] = $lemv;
				}
			}else{//刺绣内容
				$content = $emv;
			}
		}
	}
		//工艺信息
		$paramsData = array();
		$process_content = $graph = array();
		
		
		/*客户指定价格｜吊牌上打印价格｜吊牌上打印面料成分*/
		$dfData['11000']['defProcess'] = '11332,11333';
		$dfData['95000']['defProcess'] = '95212,95245';
		$dfData['98000']['defProcess'] = '98149,98134';
		$dfData['15000']['defProcess'] = '70335, 4991';
		
		
			$paramsData = json_decode($orderGoods['params'],1);

			foreach ($paramsData as $pak => $pav){
				
				if (strstr($pav,'|sin|')){
					$pavdata = array();
					$pavdata = explode('|sin|', $pav);
					$pav = str_replace('|sin|',':',$pav);
// 				var_dump($pavdata[0]);
					 if (!in_array($pavdata[0], explode(',', $dfData[$clothingID]['defProcess']))){
					 	if (in_array($pavdata[0], $separate_process)){ //锁眼线默认是单独的工艺，非客户指定
					 		
					 		$graph[] = $pavdata[0];
					 	}else{ 
// 					 		var_dump($pak);
					 		if ($orderGoods['goods_id']){//样衣
					 			$process_content[] = $pav;
					 		}else{
					 			/* 变态 袖粒扣的pid没有ecode，子id有，崩溃 */
					 			if (in_array($pak, array(36187,36185,36190,200861))){
					 				$pak = $pavdata[0];
					 			}
					 			$process_content[] = $pak.':'.$pavdata[1];
					 		}
					 	}
					 }
				}else{
					if (!in_array($pav, explode(',', $dfData[$clothingID]['defProcess']))){
				}
			}
		}
		//锁眼线，订单只能传过去一个
			if (strstr($sylineData[1],'|sin|')){
// 				$separate_process
				$pavdata = array();
				
				
					if (in_array($pavdata[0], $separate_process)){ //锁眼线默认是单独的工艺，非客户指定
						$sylineData[0] = str_replace('bty-','',$sylineData[0]); //会有多个-
						$graph[] = $pavdata[0];
					}else{
						
						if ($orderGoods['goods_id']){//样衣
    						$graph[] = $sylineData[0];
					
					}
					$sylineData[0] = str_replace('bty-','',$sylineData[0]); 会有多个-
		}
	
// 		var_dump($graph);
// 		var_dump($process_content);
	//工艺，目前是排除个性签名和面料
// 	var_dump($graphstr);
	$pcstr = implode(array_unique($process_content), ',');
	/* 3D下单需要转换一下BLeocde  */
	if ($orderGoods['type'] == 'diy'){
	
		$tmp_graphstr = explode(",",$graphstr);
		$sql = "SELECT * FROM `diy_dict` WHERE `id` IN ($graphstr)";
// 		echo $sql;
		$dict_data = getAll($sql);
		if ($dict_data){
			foreach ($dict_data as $dk=>$d){
				/* 过滤 ecode 为空的工艺 */
				if (empty($d['ecode'])){
					unset($tmp_graphstr[array_search($d['id'] , $tmp_graphstr)]);
					unset($dict_data[$dk]);
				}
			}
		}
		$code_ids = i_array_column($dict_data, 'ecode');
// 		echo implode(array_unique($code_ids), "','");
		/* 对应表中获取id */
		$sql = "SELECT * FROM `diy_dicttoxml_".$orderGoods['cloth']."` WHERE `bleode` IN ('".implode(array_unique($code_ids), "','")."')";
// 	echo $sql;
		$dicttoxml_data = getAll($sql);
		
		/* 处理组合工艺转换 */
		$effective_code_ids = i_array_column($dicttoxml_data, 'bleode');
		$combination_code = array_diff($code_ids,$effective_code_ids);
// 		var_dump($combination_code);
			$combination_str = '';//组合工艺转化后到blecode
			$t1 = microtime(true);
								$combination_str [] = $ev ['ECODE'];
			
			$t2 = microtime(true);

		$dicttoxml_id_data = i_array_column($dicttoxml_data, 'id');
		/* 是否有遗漏呢？ */
		$graphstr = implode(array_unique($dicttoxml_id_data), ',');
// 		var_dump ( $graphstr );
		if ($combination_str){//组合工艺
			$sql = "SELECT * FROM `diy_dicttoxml_".$orderGoods['cloth']."` WHERE `bleode` IN ('".implode(array_unique($combination_str), "','")."')";
			$combination_xml_str = i_array_column($dicttoxml_data2, 'id');
			$graphstr .=','.implode(array_unique($combination_xml_str), ',');
		}
// 		var_dump($graphstr);

// echo $pcstr;
        /* 指定工艺转化成BLeocde */
        if ($pcstr){
        	$tmp_pcstr = explode(",",$pcstr);
        	$pcstr_t = '';
        	foreach ($tmp_pcstr as $tpv){
        		$tmp_pcstr_v = explode(":",$tpv);
        		$s_pcstr[] = $tmp_pcstr_v[0];
        		foreach ($dict_data as $dv){
        			if ($dv['id'] == $tmp_pcstr_v[0]){
        				foreach ($dicttoxml_data as $dxv){
        					if ($dxv['bleode'] == $dv['ecode']){
        						$pcstr_t.= $dxv['id'].':'.$tmp_pcstr_v[1].',';
        					}
        				}
        			}
        		}
        	}
        	$pcstr = rtrim($pcstr_t, ",");
        }
	}
	

	/* 追加默认值 */
	
	//客户指定价格  == 订单价格 
	$sql = "SELECT * FROM `diy_fabric` WHERE `CODE` = '{$orderGoods['fabric']}'";
	$sql = "SELECT * FROM `diy_dict` WHERE `id` = '{$fdata['COMPOSITIONID']}'";
	
	
	/* 转换面料 */
	$sFabric = getSpecifyFabric($orderGoods['fabric']);
	
	$xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<Order>
	<!--订单信息-->
	<OrderInformation>
		<OrderDate>{$orderDate}</OrderDate><!--提交订单时间-->
		<Createman>QC2C</Createman><!--用户名-->
		<Password>369147</Password><!--密码 可不填-->
		<ClothingID>{$clothingID}</ClothingID><!-- 服装种类(套装2psc、套装3psc、上衣、西裤、衬衣、马夹、配件、大衣)-->
		<SizeCategoryID>{$sizeCategory}</SizeCategoryID><!--尺寸分类(标准号加减、成衣尺寸、净体量体)-->
		<SizeAreaID>{$sizeAreaID}</SizeAreaID><!--尺寸区域码(亚码、欧码、英美码、澳码)--><!--尺寸分类为标准号加减时必填，其他可不填-->
		<Fabrics>{$sFabric}</Fabrics><!--面料-->
		<SizeUnitID>10266</SizeUnitID><!--尺寸单位(厘米、英寸)-->
		<ClothingStyle>20100</ClothingStyle><!--版型风格(正常款，长款，短款) 默认：20100-->
		<CustormerBody>{$custormerBody}</CustormerBody><!--客户体型(左右肩、背、肚、手臂、臀)--><!--尺寸分类为标准号加减时可不填，其他必填-->
		<ClothingSize>{$clothingSize}</ClothingSize><!--量体部位尺寸信息 格式遵从: 部位ID:尺寸,部位ID:尺寸....--><!-- 三件套或二件套时，臀围(10108)的传值格式为：10108:西服臀围值|西裤臀围值 -->
		<From>2</From>
		<Save>1</Save><!--不填表示直接提交到生产，填1则先保存到下单系统，不提交到生产-->
		<dy>{$styleDY}</dy><!-- 如果服装分类是大衣，此字段表示：大衣是否套西服（0A02套 0A01不套），不填表示套西服，其他服装此字段无意义-->
		<UserNo></UserNo><!--客户订单号-->
		<amount>{$amount}</amount><!-- 订单商品总金额-->
	</OrderInformation>
	<!--订单上消费者基本信息-->
	<CustomerInformation>
		<CustomerName>{$customerName}</CustomerName><!--消费者姓名-->
		<Height>{$height}</Height><!--身高-->
		<HeightUnitID>10266</HeightUnitID><!--身高单位(厘米、英寸)-->
		<Weight>{$weight}</Weight><!--体重--><!--可不填-->
		<WeightUnitID>10261</WeightUnitID><!--体重单位(千克、英镑)--><!--可不填-->
		<Email>{$email}</Email><!--邮件--><!--可不填-->
		<Address>{$address}</Address><!--地址--><!--可不填-->
		<Tel>{$tel}</Tel><!--电话--><!--可不填-->
		<GenderID>10040</GenderID><!--性别(男，女)-->
		<LTName>{$ltname}</LTName><!--量体人员资格证-->
	</CustomerInformation>

	<OrderDetails>
		<!--单条订单明细-->
		<OrderDetail id = '1'><!-- 订单和刺绣信息存在多个时用id来区分-->
			<!--单条订单明细信息-->
			<OrderDetailInformation>
				<SizeSpecChest></SizeSpecChest><!--胸围规格--><!--可不填-->
				<SizeSpecHeight>{$sizeSpecHeight}</SizeSpecHeight><!--衣服大小号--><!--尺寸分类为标准号加减时必填，其他可不填-->
				<Categories>{$clothingID}</Categories><!--服装种类(上衣) -->
				<Quantity>{$quantity}</Quantity><!--数量 -->
				<BodyStyle>{$bodyStyle}</BodyStyle><!--着装风格-->
			</OrderDetailInformation>
			<!--订单明细刺绣信息-->
			<EmbroideryProcess>
				<EmbTag>
			</EmbroideryProcess>
			<OrdersProcess>{$graphstr}</OrdersProcess><!--单条订单明细工艺信息 -->
			<OrdersProcessContent>{$pcstr}</OrdersProcessContent><!--面料标、商标、客户指定等内容 -->
			<Style></Style><!-- 固化款式号 可不填 -->
		</OrderDetail>
	</OrderDetails>
</Order>
EOT;
	
	/*刺绣信息 内容id*/
	$dfEmbroidery = array();
	$dfEmbroidery['3'] = array('pos' => '421');
	$dfEmbroidery['11000'] = array('pos' => '11528');
	$dfEmbroidery['95000'] = array('pos' => '95653');
	$dfEmbroidery['15000'] = array('pos' => '15795');
	
/* 	由于工厂支持特殊字符刺绣 特殊处理 ［正则匹配 非中文字符 只保留中文数字字母非特殊字符］ */
// 	  " */1A-X=这是a2c123~!@#$%c红领^&*()_+{}:|<>?,./;'┮集团 ┞[]\-=`456" －> "1AX这是a2c123c红领集团456"
	$content = preg_replace("/[^0-9a-z\x{4e00}-\x{9fa5}]+/iu",'',$content);
	
	if($location){
		$k = 1;
		foreach($location as $key => $val){
			$str = "<Embroidery id = '{$k}'>
	<Location>{$val}</Location><!--位置 -->
	<Font>{$font}</Font><!--字体-->
	<Color>{$color}</Color><!--颜色 -->
	<Content>{$dfEmbroidery[$clothingID]["pos"]}:{$content}</Content><!--内容 -->
	<Size>3261</Size><!--绣字大小（衬衣） -->
	</Embroidery>";
			$k++;
		}
	}else{
		$str = '<Embroidery id = "1">
		<Location></Location><!--位置 -->
		<Font></Font><!--字体-->
		<Color></Color><!--颜色 -->
		<Content></Content><!--内容 -->
		<Size></Size><!--绣字大小（衬衣） -->
		</Embroidery>';
	}

	$xml = preg_replace("/<EmbTag>/", $str, $xml);
	return $xml;
}