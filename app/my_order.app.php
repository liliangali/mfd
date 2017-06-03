<?php
use Cyteam\Goods\Orders;
use Cyteam\Comment\Comment;
/* 买家咨询管理控制器 */
class My_orderApp extends MemberbaseApp
{
    var $cData = array(
	        '0003' => "西服",
	        '0004' => "西裤",
	        '0005' => "马夹",
	        '0006' => "衬衣",
	        '0007' => "大衣",
	        '0011' => "西装",
	        '0012' => "西裤",
	        '0016' => "衬衣",
	        '0021' => "大衣",
	        '0017' => "短裤"
	    );
    var $commentObj;
    function __construct()
    {
    	$this->commentObj =new Comment();
       parent::__construct();
    }
    function index()
    {
        $arg = $this->get_params();
        $status = array('unpay', 'payed', 'onstream', 'shipped', 'finished', 'cancel', 'exception');
        $user_id = $this->visitor->get("user_id");
        $conditons = "user_id='$user_id'  ";

        if(in_array($arg[1], $status)){
            switch ($arg[1]){
                case "unpay": //===== 待付款  =====
                    $conditons .= " AND status = ".ORDER_PENDING;
                    break;
                case "payed": //===== 已支付 待发货  =====
                    $conditons .= " AND (status = ".ORDER_ACCEPTED.")";
                    break;
                case "shipped": //===== 已发货 待收货  =====
                    $conditons .=   " AND (status = ".ORDER_SHIPPED.")";
                    break;
                case "finished": //===== 已完成  =====
                    $conditons .= " AND status = ".ORDER_FINISHED;
                    break;
            }
        }
        $page =$this->_get_page(6);
        $orderLib = new Orders();
        $res = $orderLib->getOrderList($conditons, $page["limit"]);

        $payments = $this->payments();
        $order_mod = &m("order");
        $order_daifuk = $order_mod->get(array(
            'conditions' => "user_id='$user_id' AND status = 11",
            'fields' => "count(*) as num",
        ));
        $order_daifah = $order_mod->get(array(
            'conditions' => "user_id='$user_id' AND status = 20",
            'fields' => "count(*) as num",
        ));

        $order_daishouf = $order_mod->get(array(
            'conditions' => "user_id='$user_id' AND status = 30",
            'fields' => "count(*) as num",
        ));
        $order_queren = $order_mod->get(array(
            'conditions' => "user_id='$user_id' AND status = 40",
            'fields' => "count(*) as num",
        ));
        $order_num['order_daifuk'] = $order_daifuk['num'];
        $order_num['order_daifah'] = $order_daifah['num'];
        $order_num['order_daishouf'] = $order_daishouf['num'];
        $order_num['order_queren'] = $order_queren['num'];
        $this->assign("order_num", $order_num);
        $page['item_count'] = $order_mod->getCount();   //获取统计的数据11
        $this->_format_page($page);
        $impress=$this->commentObj->get_impression_arr();
        $this->assign('impress',$impress);
        $this->assign("list", $res);
        $this->assign('page_info',$page);
        $this->_config_seo('title', Lang::get('member_center') . ' - 我的订单');
        $this->assign("app", "my_order");
        $this->assign("status", $arg[1]);
        $this->assign("payments", $payments);
        $this->display('my_order.index.html');
    }
    
    function detail(){
        
        
        
        $arg = $this->get_params();
        $orderid = intval($arg[0]);
        $orderLib = new Orders();
        $res = $orderLib->getOrderInfo($orderid);
        $impress=$this->commentObj->get_impression_arr();
        $this->assign('impress',$impress);
        $this->assign("order", $res);
        $this->assign("invoice_lang", array(
            'com' => '单位名称',
            'sn' => '识&nbsp;&nbsp;别&nbsp;&nbsp;码',
            'addr' => '注册地址',
            'tel' => '注册电话',
            'bank' => '开户银行',
            'bank_num' => '银行账户',
        ));

        $invoice = array();
        if($res["invoice_need"] == 1){
            $invoice['title'] = $res["invoice_type"];
            $invoice["content"] = $res["invoice_com"] == 3 ? json_decode($res["invoice_title"],1) : $res["invoice_title"];
        }
        $this->assign('invoice',$invoice);

        $this->display('my_order.detail.html');
    }
    
    function getFigure(){
        $orderid = intval($_GET['orderid']);
        $sn      = trim($_GET['sn']);
        $order_figure_mod = &m("orderfigure");
        $order_goods_mod = &m("ordergoods");
        $aData = $order_figure_mod->get("order_id='{$orderid}' AND son_sn = '{$sn}'");
        
        $serve_mod = &m("serve");
        $sData = $serve_mod->get_info($aData['server_id']);
        $stateLang = array(
            0 => "未指派",
            1 => "已指派",
            2 => "已完成",
            3 => "已到达",
            4 => "已保存",
            7 => "已取消"
        );
        $bodytypes = array(
            '10087'=>'正常-A',
            '10093'=>'轻微溜肩-B',
            '10068'=>'中度溜肩-C',
            '10069'=>'严重溜肩-D',
            '10094'=>'轻微耸肩-E',
            '10072'=>'中度耸肩-F',
            '10073'=>'严重耸肩-G',
            '10088'=>'正常-A',
            '10095'=>'轻微溜肩-B',
            '10070'=>'中度溜肩-C',
            '10071'=>'严重溜肩-D',
            '10096'=>'轻微耸肩-E',
            '10074'=>'中度耸肩-F',
            '10075'=>'严重耸肩-G',
            '10097'=>'正常-A',
            '10098'=>'轻微驼背-B',
            '10099'=>'中度驼背-C',
            '10100'=>'严重驼背-D',
            '10090'=>'正常',
            '10079'=>'凸肚',
            '10091'=>'正常',
            '10080'=>'手臂靠前',
            '10089'=>'轻微手臂靠后',
            '10081'=>'严重手臂靠后',
            '10092'=>'正常',
            '10082'=>'严重凸臀',
            '10083'=>'严重坠臀',
            '10084'=>'严重平臀',
            '10284'   => '正常',
            '10280' => '非常修身',
            '10281' => '很修身',
            '10282' => '修身',
            '10283' => '正常偏瘦',
            '10285' => '正常偏肥',
            '10286' => '宽松',
            '10287' => '很宽松',
            '10288' => '非常宽松'
        );
        
        $this->assign('bodytypes', $bodytypes);
        
        $formatCloths = array(
            1 => '0001',
            2 => '0002',
            3 => '0003',
            2000 => '0004',
            4000 => '0005',
            3000 => '0006',
            6000 => '0007',
            5000 => '0008',
            4 => '0009',
            90000 => '0010',
            95000 => '0011',
            98000 => '0012',
            5 => '0013',
            6 => '0014',
            7 => '0015',
            11000 => '0016',
            15000 => '0017',
            18000 => '0018',
            19 => '0019',
            100000 => '0020',
            103000 => '0021',
        );
        
        $formatCloths = array_flip($formatCloths);
        
        $cloths = $order_goods_mod->find(array(
            "conditions" => "son_sn = '{$sn}' AND order_id = '{$orderid}'",
            'fields'     =>"cloth",
        ));
        
        $bodys = array();
        
       // print_R($cloths);
        //die();
        foreach($cloths as $key => $val){
            $bodys[] = $this->cData[$val["cloth"]].":".$bodytypes[$aData["body_type_".$formatCloths[$val["cloth"]]]];
        }
        $this->assign("bodys", $bodys);
        $this->assign("sData", $sData);
        $this->assign("aData", $aData);
        $this->assign("slang", $stateLang);
        $content = $this->_view->fetch("order_figure.lib.html");
        die($this->json_result($content));
    }
    
    function getDict(){
        $orderid = intval($_GET['orderid']);
        $sn      = intval($_GET['sn']);
        $type    = trim($_GET["type"]);
        
        if(!in_array($type, array("suit", "diy"))){
            $type = "suit";
        }
        $conditions = "order_id = '{$orderid}'";
        
        $order_goods_mod = &m("ordergoods");
        
        if($type == "suit"){
            $conditions .= " AND suit_id = '{$sn}'";
        }else{
            $conditions .= " AND rec_id = '{$sn}'";
        }
        
        $aData = $order_goods_mod->find(array(
            "conditions" => $conditions,
        ));
        
        $assign = array();
        $_tmp = array();
        $embs = array();
        foreach($aData as $key => $val){
            $embs[$val["cloth"]]["name"]  = $this->cData[$val["cloth"]];
            $params = json_decode($val["params"],1);
            foreach((array)$params as $k =>$v){
                $_value = explode("|sin|", $v);
                if(count($_value) > 1){
                    $assign[$_value[0]] = $_value[1];
                    $_tmp[$k] = $_value[0];
                }else{
                    $_tmp[$k] = $v;
                }
            }
        
            $embs[$val["cloth"]]["items"] = json_decode($val["embs"],1);
        }
        
        $this->outEmbs($embs);
        $this->dicts = array();
        
        if($type == "suit"){
            $this->formatSuitDict($_tmp);
        }else{
            $this->formatDiyDict($_tmp);
        }

        $dicts = array();
        foreach($this->dicts as $key =>$val){
            $dicts[$val["id"]] = $val;
            $dicts[$val["id"]]["assign"] = isset($assign[$val["id"]]) ? $assign[$val["id"]] : '';
        }
        
        $out = $this->outDict($dicts);
        $result = array();
        foreach($out as $k => $v){
            $result[$k]["name"] = $v["name"];
            $this->aData = array();
            $this->lastLevel($v["children"]);
            $result[$k]["items"] = $this->aData;
        }

        $this->assign("result", $result);

        $content = $this->_view->fetch("order_dict.lib.html");
        die($this->json_result($content));
    }
    
    function formatSuitDict($ids){
         if(empty($ids)) return;
         $dict1_mod = &m("dict1");
         $dict =  $dict1_mod->find(array(
             "conditions" => "ID ".db_create_in($ids),
             "fields"     => "ID as id, NAME as name, PARENTID as parentid",
         ));
         
         $this->dicts = array_merge($this->dicts,$dict);
         
         $ids = array();
         
         foreach($dict as $key => $val){
             if($val["parentid"]){
                $ids[] = $val["parentid"];
             }
         }
         $this->formatSuitDict($ids);
    }
    
    function formatDiyDict($ids){
        if(empty($ids)) return;
        $dict1_mod = &m("dict");
        $dict =  $dict1_mod->find(array(
            "conditions" => "id ".db_create_in($ids),
            "fields"     => "id, name, parentid",
        ));
        
        $this->dicts = array_merge($this->dicts,$dict);
         
        $ids = array();
         
        foreach($dict as $key => $val){
            if($val["parentid"]){
                $ids[] = $val["parentid"];
            }
        }
        $this->formatDiyDict($ids);
    }
    
    function outDict($dict, $pid=0, $name=""){
        $tree = array();
        foreach($dict as $key => $val){
            if($val["parentid"] == $pid){
                $val["fname"] = "";
                if($pid > 0){
                    $val["fname"] = !$val["assign"] ? $name ? $name.":".$val["name"] : $val["name"] : $name.":".$val["name"].$val["assign"];
                }
                $children=$this->outDict($dict, $val["id"], $val['fname']);
                $val["children"] = $children;
                $tree[] = $val;
            }
        }
        return $tree;
    }
    
    function lastLevel($out){
        
        foreach($out as $key => $val){
             if(empty($val["children"])){
                 $this->aData[] = $val;
             }else{
                 $this->lastLevel($val["children"]);
             }
        }
    }
    
    function outEmbs($embs){
        $cart_mod = &m("cart");
        $sourceEmbs = $cart_mod->_format_embs();
        
        foreach($embs as $key => $val){
            if(isset($sourceEmbs[$key])){
                foreach((array)$val["items"] as $ek => $ev){
                    if(isset($sourceEmbs[$key][$ek])){
                        $embs[$key]["out"][$ek]["name"] = $sourceEmbs[$key][$ek]["name"];
                        $embs[$key]["out"][$ek]["_v"] = 0;
                        if(!empty($sourceEmbs[$key][$ek]["list"])){
                            if(is_array($ev)){
                                $aev = current($ev);
                                $embs[$key]["out"][$ek]["_v"] = 1;
                                $embs[$key]["out"][$ek]["value"] = $sourceEmbs[$key][$ek]["list"][$aev]["name"];
                                $embs[$key]["out"][$ek]["image"] = $sourceEmbs[$key][$ek]["list"][$aev]["image"];
                            }else{
                                $embs[$key]["out"][$ek]["value"] = $sourceEmbs[$key][$ek]["list"][$ev]["name"];
                                $embs[$key]["out"][$ek]["image"] = $sourceEmbs[$key][$ek]["list"][$ev]["image"];
                            }
                        }else{
                            $embs[$key]["out"][$ek]["_v"] = 1;
                            $embs[$key]["out"][$ek]["value"] = $ev;
                        }
                    }
                }
            }
        }
        
        $this->assign("embs", $embs);
    }
    
    function operation(){
        $arg = $this->get_params();
        $option = trim($arg[0]);
        $orderid = intval($arg[1]);
        
        if(!in_array($option, array("cancel", "finish"))){
            $this->show_warning("参数错误~");
            return;
        }
        
        $orderLib = new Orders();
        if ($option == "cancel")
        {
            $orderLib->canlOrder($orderid, $this->visitor->get("user_id"),"PC");
        }
        else
        {
            $orderLib->subOrder($orderid, $this->visitor->get("user_id"),"PC");
        }
        header("Location:my_order-detail-{$orderid}.html");
        return ;
        
        
        
        
        
        $order_mod = &m('order');
        $oData = $order_mod->get_info($orderid);
        
        import("orders.lib");
        import("orderLogs.lib");
        $oslib = new Orders();
        $lsLib = new OrderLogs();
        if($option == "cancel"){
            if($oData['status'] != ORDER_PENDING){
                $this->show_warning("该订单不是未支付状态！");
                return;
            }

            if($oData["user_id"] != $this->visitor->get("user_id")){
                $this->show_warning("非法操作！");
                return;
            }
            
            $oslib->cancelById($orderid, $oData);
            $lsLib->_record(array(
                "order_id" => $orderid,
                "op_id"    => $this->visitor->get("user_id"),
                "op_name"  => $this->visitor->get("user_name"),
                "behavior" => "cancel",
                "from" => $oData["status"],
                "to" => ORDER_CANCELED,
            ));
            
            $order_mod->edit($orderid,array(
                'status' => ORDER_CANCELED,
            ));
            
            header("Location:my_order.html");
        }
        
        if($option == "finish"){
            
            if($oData['status'] != ORDER_SHIPPED){
                $this->show_warning("该订单不是已发货状态！");
                return;
            }
            
            if($oData["user_id"] != $this->visitor->get("user_id")){
                $this->show_warning("非法操作！");
                return;
            }
            
           $url = $this->soapApi();
           $member_mob = &m("member");
           $mData = $member_mob->get_info($this->visitor->get("user_id"));
           $result = file_get_contents("{$url}/soap/store.php?act=editOrderStatus&token={$mData["user_token"]}&order_id={$orderid}&status=".ORDER_FINISHED);
           $result = json_decode($result,1);
           //echo "{$url}/soap/store.php?act=editOrderStatus&token={$mData["user_token"]}&order_id={$orderid}&status=".ORDER_FINISHED;
           if($result["statusCode"] == 0){
               $this->show_warning($result["error"]["msg"]);
               return;
           }
           
           header("Location:my_order-detail-{$orderid}.html");
        }
    }
    
    function payments(){
        $mPayment = &m("payment");
    	return $mPayment->find(array(
    				'conditions' => "enabled=1 AND ismobile=0 and payment_code != 'wxpay'",
    				'order'      => "sort_order DESC"
    			));
    }
    
    function pay_exist($payments, $pay){
        $exist = false;
        foreach((array)$payments as $key => $val){
            if($pay == $val["payment_code"]){
                $exist = true;
            }
        }
        return $exist;
    }
    
    function soapApi()
    {
        $config =  include ROOT_PATH.'/data/config.inc.php';
        if (stripos($config['SITE_URL'],"www.dev.mfd.cn"))
        {
            $url = "http://api.dev.mfd.cn:8080";
        }
        elseif (stripos($config['SITE_URL'],"www.test.mfd.cn"))
        {
            $url = "http://api.test.mfd.cn";
        }
        elseif (stripos($config['SITE_URL'],"www.mfd.cn"))
        {
            $url = "http://api.mfd.cn";
        }
         
        return $url;
    }
    
    function changePay(){
        $orderid = isset($_POST["orderid"]) ? intval($_POST["orderid"]) : 0;
        $payid   = isset($_POST["payid"])   ? intval($_POST["payid"])   : 0;
        $mPayment = &m("payment");
        $mOrder   = &m("order");
        $payinfo = $mPayment->get_info($payid);
        
        if(empty($payinfo)){
            $this->json_error("意外错误~");
            die();
        }
        
        $mData = $mOrder->get("order_id='{$orderid}' AND user_id='{$this->visitor->get("user_id")}'");
        
        if($mData["status"] != ORDER_PENDING){
            $this->json_error("非法操作~");
            die();
        }
        
        $res = $mOrder->edit("order_id='{$orderid}'",array(
                'payment_id'    => $payinfo["payment_id"],
                'payment_name'  => $payinfo["payment_name"],
                'payment_code'  => $payinfo["payment_code"],
        ));
        
        if($res){
           $this->json_result(array("sn" => $mData["order_sn"]));
           die();
        }else{
            $this->json_error('意外错误！');
            die();
        }
    }
}

?>