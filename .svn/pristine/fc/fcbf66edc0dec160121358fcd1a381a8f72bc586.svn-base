<?php
use Cyteam\Goods\Orders;
use Cyteam\Comment\Comment;
/* 我的订单 */
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
    function __construct()
    {
       parent::__construct();
       $this->_status = array(
 				'all'=>'全部',
        'unpay'=>'未付款',
        'payed'=>'待量体',
        'onstream'=>'生产中',
        'shipped'=>'已发货',
        'finished'=>'已完成',
       // 'exception'=>'异常订单',
        'cancel'=>'已取消',
    	);
       $port = ($_SERVER['SERVER_PORT'] == 80) ? '' : ':'.$_SERVER['SERVER_PORT'];
       $url = 'http://'.str_replace('m.', 'shop.diy.', $_SERVER['HTTP_HOST']).$port."/";
       $this->assign('diy_url',$url);
    }
    function index()
    {
        $arg = $this->get_params();
        $list = $this->_get_orders($arg[1]);
        $this->assign("list", $list);
        $this->assign("status", $arg[1]);
        $this->assign('status_list', $this->_status);
        $this->assign("payments", $payments);
        $this->display('my_order.index.html');
    }

    //瀑布流 读取信息
    function ajax_order_list(){
      $new_list = $this->_get_orders($_GET['status'],$_GET['limit']);
      if ($new_list)
       {
          $this->json_result($new_list);
          return;
      }else{
        $this->json_error('无数据了！');
        return;
      }
    }

    //获取列表信息
    function _get_orders($type = null,$page = '0'){
        
        $status = array('unpay', 'payed', 'onstream', 'shipped', 'finished', 'cancel', 'exception');
        
        $conditons = "user_id='{$this->visitor->get("user_id")}'  ";
        //$conditons .= " AND (status = ".ORDER_ABNORMAL." || status = ".ORDER_SHIPERROR.")";
        if(in_array($type, $status)){
            switch ($type){
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
//   echo '<pre>';print_r($type);exit;
        $res = $orderLib->getOrderList($conditons, $page["limit"]);
        foreach ((array)$res as $key => $value) 
        {
            $count = count($value['item']);
            $res[$key]['goods_num'] = $count;
        }
// echo '<pre>';print_r($res);exit;
        return $res;
        
     /*    
      $status = array('unpay', 'payed', 'onstream', 'shipped', 'finished', 'cancel', 'exception');
      $page = $page > 10 ? ($page-10).','.$page : '0,10';
      $conditons = "user_id='{$this->visitor->get("user_id")}' AND extension = 'news'";
      if(in_array($type, $status)){
          switch ($type){
              case "unpay":
                  $conditons .= " AND status = ".ORDER_PENDING;
                  break;
              case "payed":
                  $conditons .= " AND ( status = ".ORDER_WAITFIGURE.")";
                  break;
              case "onstream":
                  $conditons .= " AND (status = ".ORDER_PRODUCTION." || status = ".ORDER_ACCEPTED.")" ;
                  break;
              case "shipped":
                  $conditons .= " AND status = ".ORDER_SHIPPED;
                  break;
              case "finished":
                  $conditons .= " AND status = ".ORDER_FINISHED;
                  break;
              case "cancel":
                  $conditons .= " AND status = ".ORDER_CANCELED;
                  break;
              case "exception":
                  $conditons .= " AND (status = ".ORDER_ABNORMAL." || status = ".ORDER_SHIPERROR.")";
                  break;
          }
      }
      $payments = $this->payments();
      $order_mod = &m("order");
      $order_goods_mod = &m("ordergoods");
      $list = $order_mod->find(array(
            "conditions" => $conditons,
            'fields'     => "order_id, order_sn, user_id, user_name, payment_code, ship_name, add_time, status, final_amount, source_from",
            'order'      => "add_time DESC",
            'limit' => $page,
            'count'      => true,
      ));
        $ids = array();
        foreach($list as $key => $val){
            $val["_opt_pay"] = 0;
            $val["_opt_cancel"] = 0;
            $val["_opt_finish"] = 0;
            if($val['status'] == ORDER_PENDING){
                $val["_opt_pay"] = 1;
                $val["_opt_change"] = 0;
                $change = $this->pay_exist($payments, $val["payment_code"]);
                if(!$change){
                    $val["_opt_change"] = 1;
                }
                $val["_opt_cancel"] = 1;
            }

            if($val['status'] == ORDER_SHIPPED){
                $val["_opt_finish"] = 1;
            }
            $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
            //查询状态，转换中文
            $val['status_lang'] = Lang::get('ORDER_STATUS')[$val['status']];
            $list[$key] = $val;
            $ids[] = $val["order_id"];
        }

        $gData = $order_goods_mod->find(array(
            "conditions" => "order_id ".db_create_in($ids),
            'fields'     => "goods_image, price, order_id, goods_id, goods_name, size, suit_id, dis_ident, cloth, quantity",
        ));

        $oids = array();
        $sids = array();
        $osSize = array();
        foreach((array)$gData as $key => $val){
            if($val["suit_id"]){
                $oids[] = $val["order_id"];
                $sids[] = $val["suit_id"];
                if($val['size'] == "diy"){
                   $osSize[$val["order_id"]][$val["dis_ident"]] = "diy";
                }else{
                   $osSize[$val["order_id"]][$val['dis_ident']][] = $this->cData[$val["cloth"]].":".$val["size"];
                }
            }else{
                if(isset($list[$val["order_id"]])){
                    if($val['size'] != "diy"){
                        $val['formatsize'] = array($val['size']);
                    }
                    $list[$val["order_id"]]['goods'][] = $val;
                }
            }
        }
        if(!empty($oids) && !empty($sids)){
            $order_suit_mod = &m("ordersuit");
            $sData =  $order_suit_mod->find(array(
               "conditions" => "order_id ".db_create_in($oids) . " AND goods_id ".db_create_in($sids),
            ));
            foreach($sData as $key => $val){
               if($osSize[$val["order_id"]][$val["dis_ident"]] == "diy"){
                   $val['size'] = "diy";
               }else{
                   $val["formatsize"] = $osSize[$val["order_id"]][$val["dis_ident"]];
               }
               $list[$val["order_id"]]['goods'][] = $val;
            }
        }
        //处理商品统计
        foreach($list as $key=>$val){
            $i = 1;
            if ($val['goods'])
            {
                foreach($val['goods'] as $v){
                    $list[$key]['goods_num'] = $i;
                    $i++;
                }
            }

        } */
        return $list;
    }

    //订单详情
    function detail(){
        $arg = $this->get_params();
        $orderid = intval($arg[0]);
        $orderLib = new Orders();
        $res = $orderLib->getOrderInfo($orderid);
        $this->commentObj =new Comment();
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
        $this->display('my_order.detail.html');
        exit;
        
        
        $arg = $this->get_params();
        $orderid = intval($arg[0]);
        $order_mod = &m("order");
        $order_goods_mod = &m("ordergoods");
        $order_suit_mod = &m("ordersuit");
        $order_debit_mod = &m("orderdebit");
        $order_logs_mod = &m("orderlogs");
        $order = $order_mod->get("order_id='{$orderid}' AND user_id='{$this->visitor->get("user_id")}' AND extension = 'news'");

        if(empty($order)){
            $this->show_warning("参数错误！");
            return;
        }

        $logs = $order_logs_mod->find(array(
           "conditions" => "order_id='{$orderid}' AND to_status != ''",
           'fields'     => "to_status, alttime",
           "order"      => "alttime ASC",
        ));

        $status = array();
        foreach((array)$logs as $key => $val){
            $status[$val["to_status"]] = $val["alttime"];
        }

        $progress = array(
            'unpay' => 0,
            'pay'   => 0,
            'onstream' => 0,
            'ship'     => 0,
            'finish'   => 0,
            'cancel'   => 0,
        );

        $options = array(
            "pay"       => 0,
            "cancel"    => 0,
            'finish'    => 0,
            'change'    => 0,
        );

        $progressTime = array();

        $payments = $this->payments();

        switch ($order['status']){
            case ORDER_PENDING:
                $progress['unpay'] = 1;
                $options["pay"] = 1;
                $change = $this->pay_exist($payments, $order["payment_code"]);
                if(!$change){
                    $options["change"] = 1;
                }
                array_push($progressTime,$status[ORDER_PENDING]);
                break;
            case ORDER_WAITFIGURE:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED]);
                break;
            case ORDER_ACCEPTED:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED]);
                break;
            case ORDER_PRODUCTION:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_PRODUCTION]);
                break;
            case ORDER_ABNORMAL:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_ABNORMAL]);
                break;
            case ORDER_STOCKING:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_STOCKING]);
                break;
            case ORDER_SHIPPED:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                $progress['ship']     = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_PRODUCTION], $status[ORDER_SHIPPED]);
                break;
            case ORDER_SHIPERROR:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                $progress['ship']     = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_PRODUCTION], $status[ORDER_SHIPPED]);
                break;
            case ORDER_FINISHED:
                $progress['unpay'] = 1;
                $progress['pay']   = 1;
                $progress['onstream']   = 1;
                $progress['ship']     = 1;
                $progress['finish']   = 1;
                array_push($progressTime,$status[ORDER_PENDING],$status[ORDER_ACCEPTED],$status[ORDER_PRODUCTION], $status[ORDER_SHIPPED], $status[ORDER_FINISHED]);
                break;
        }
        $gData = $order_goods_mod->find(array(
            "conditions" => "order_id ='{$orderid}'",
            'fields'     => "goods_image, price, order_id, type, rec_id, goods_id, goods_name, size, suit_id, dis_ident, cloth, quantity, son_sn ,comment",
        ));

        $sids = array();
        $goods = array();
        $osSize = array();
        $soData = array();
        foreach((array)$gData as $key => $val){
            if($val["suit_id"]){
                $sids[] = $val["suit_id"];
                if($val['size'] == "diy"){
                    $osSize[$val["dis_ident"]] = "diy";
                    $soData[$val["suit_id"]] = $val["son_sn"];
                }else{
                    $osSize[$val['dis_ident']][] = $this->cData[$val["cloth"]].":".$val["size"];
                }
            }else{
                if($val['size'] != "diy"){
                    $val['formatsize'] = array($val['size']);
                }
                $goods[] = $val;
            }
        }

        if(!empty($sids)){
            $sData =  $order_suit_mod->find(array(
                "conditions" => "order_id = '{$orderid}' AND goods_id ".db_create_in($sids),
            ));

            foreach($sData as $key => $val){
                if($osSize[$val["dis_ident"]] == "diy"){
                    $val['size'] = "diy";
                    $val["son_sn"] = $soData[$val["goods_id"]];
                }else{
                    $val["formatsize"] = $osSize[$val["dis_ident"]];
                }
                $goods[] = $val;
            }
        }

        $invoice = array();
        if($order["invoice_need"] == 1){
            $invoice['title'] = $order["invoice_type"];
            $invoice["content"] = $order["invoice_com"] == 3 ? json_decode($order["invoice_title"],1) : $order["invoice_title"];
        }

        $debit = $order_debit_mod->find(array(
            "conditions" => "order_id='{$orderid}'",
            'fields'     => "SUM(d_money) AS money",
        ));

        $this->assign("invoice_lang", array(
                    'com' => '单位名称',
                    'sn' => '识&nbsp;&nbsp;别&nbsp;&nbsp;码',
                    'addr' => '注册地址',
                    'tel' => '注册电话',
                    'bank' => '开户银行',
                    'bank_num' => '银行账户',
        ));

        // 评论印象  tangsj
        $impression_arr = include_once ROOT_PATH.'/includes/impression.arrayfile.php';
        foreach ($impression_arr as $k=>$v)
        {
            $impress[] = array(
                'impress_id'    => $k,
                'impress_name'  => $v,
            );
        }
        $retUrl = '';
        if($order["waybillno"]){
            $url = "http://www.kuaidi100.com/applyurl?key=234eeb36235cd1e4&com=shunfeng&nu={$order["waybillno"]}&order=asc";
            $retUrl = file_get_contents($url);
        }

        if(isset($payments[$order["payment_id"]])){
           unset($payments[$order["payment_id"]]);
        }
        $this->assign("retUrl", $retUrl);
        $this->assign("options", $options);
        $this->assign('impress', $impress);
        $this->assign("status", $status);
        $this->assign("debit", current($debit));
        $this->assign("goodslist", $goods);
        $this->assign("invoice", $invoice);
        $this->assign("progress", $progress);
        $this->assign("progressTime", $progressTime);
        $this->assign("currentTime", end($progressTime));
        $this->assign("order", $order);
        $this->assign("app", "my_order");
        $this->assign("payments", $payments);
        $this->display('my_order.detail.html');
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
            $orderLib->canlOrder($orderid, $this->visitor->get("user_id"),"WeChat");
        }
        else 
        {
            $orderLib->subOrder($orderid, $this->visitor->get("user_id"),"WeChat");
        }
        header("Location:my_order-detail-{$orderid}.html");
        return ;
        
        $order_mod = &m('order');
        $oData = $order_mod->get_info($orderid);
        $url = $this->soapApi();
        $user_id = $_SESSION['user_info']['user_id'];
        $mod = m('member');
        $user = $mod->get_info($user_id);
        $url = $url."soap/store.php?act=editOrderStatus&token=".$user['user_token']."&order_id=".$orderid."&status=";
        if($option == "cancel")
        {
            $url = $url."0";
            $result = file_get_contents($url);
            $result = json_decode($result,1);
            if($result["statusCode"] == 0)
            {
                $this->show_warning($result["error"]["msg"]);
                return;
            }

            header("Location:my_order.html");
        }
        if($option == "finish")
        {
            $url = $url.ORDER_FINISHED;
            $result = file_get_contents($url);
            $result = json_decode($result,1);
            if($result["statusCode"] == 0)
            {
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
        $config =  include ROOT_PATH.'/m/data/config.inc.php';
        if (stripos($config['SITE_URL'],"m.dev.mfd.cn"))
        {
            $url = "http://api.dev.mfd.cn:8080/";
        }
        elseif (stripos($config['SITE_URL'],"m.test.mfd.cn"))
        {
            $url = "http://api.test.mfd.cn/";
        }
        elseif (stripos($config['SITE_URL'],"m.mfd.cn"))
        {
            $url = "http://api.mfd.cn/";
        }
        elseif (stripos($config['SITE_URL'],"m.mfd.com"))
        {
            $url = "http://api.mfd.com/";
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

    //获取量体数据
    function figure()
    {
      $arg = $this->get_params();
      $order_figure_mod = m('orderfigure');
      $orderId=$arg[0];
      $serve=m('serve');
      $order_figure_info = $order_figure_mod->get("order_id = $orderId");
      $order_figure = array();
      if ($order_figure_info)
      {
        if($order_figure_info['liangti_state'] ==0){
          $order_figure=array();
        }
        else{
              //=====  量体项  =====
              $json = file_get_contents(PROJECT_PATH.'/includes/arrayfiles/size_json/figure.json');
              $json = json_decode($json,true);

              $special = array();//=====  特体  =====

              foreach ($json['public'] as $key => $value)
              {
                $tmp['name'] = $value['cateName'];
                $tmp['spe_name'] = $value['value_name'];

                $val = $order_figure_info[$value['value_name']];

                foreach ($value['item'] as $key1 => $value1)
                {
                  $tmp['value'] = '';
                  if ($val == $value1['value'])
                  {
                    $tmp['value'] = $value1['name'];
                    break;
                  }
                }
                $special[] = $tmp;
              }

              $style = array();//=====  风格  =====
              foreach ($json['special'] as $key => $value)
              {
                $tmp2['name'] = $value['cateName'];
                $tmp2['sty_name'] = $value['value_name'];

                $val = $order_figure_info[$value['value_name']];

                foreach ($value['item'] as $key1 => $value1)
                {
                  $tmp2['value'] = '';
                  if ($val == $value1['value'])
                  {
                    $tmp2['value'] = $value1['name'];
                    break;
                  }
                }
                $style[] = $tmp2;
              }
              $figure_item = array();//=====  量体参数  =====
              $item = $this->_positions();
              foreach ($item as $key => $value)
              {
                $tmp3['name'] = $value['zname'];
                $tmp3['figure_name'] = $key;
                $val = "";
                if ($order_figure_info[$key])
                {
                  $val = $order_figure_info[$key];
                }
                $tmp3['value'] = $val;
                $figure_item[] = $tmp3;
              }
              $order_figure['special'] = $special;
              $order_figure['style'] = $style;
              $order_figure['figure'] = $figure_item;
        }
      }

      if($order_figure_info['server_id'])
      {
        $serve_name=$serve->get(array(
            'conditions' =>"idserve='{$order_figure_info['server_id']}'",
            'fields' =>"serve_name",
        ));
        $order_figure_info['serve_name']=$serve_name['serve_name'];
      }
      $this->assign('order_figure_info',$order_figure_info);
      $this->assign('order_figure',$order_figure);
      $this->display('my_order.figure.html');
   }

       /* 量体信息各部位数据 */
     function _positions()
     {
     $return = array(
         'lw' => array('zname'=>'领围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'xw' => array('zname'=>'胸围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'zyw' => array('zname'=>'中腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'tw' => array('zname'=>'臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'stw' => array('zname'=>'上臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'zjk' => array('zname'=>'总肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'zxc' => array('zname'=>'左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'yxc' => array('zname'=>'右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'qjk' => array('zname'=>'前肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'hyjc' => array('zname'=>'后腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'hyc' => array('zname'=>'后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'qyj' => array('zname'=>'前腰节','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'yw' => array('zname'=>'腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'tgw' => array('zname'=>'腿根围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'td' => array('zname'=>'通档','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'hyg' => array('zname'=>'后腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'qyg' => array('zname'=>'前腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'zkc' => array('zname'=>'左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'ykc' => array('zname'=>'右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'xiw' => array('zname'=>'膝围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'jk' => array('zname'=>'脚口','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'part_label_10726' => array('zname'=>'马甲后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'part_label_10725' => array('zname'=>'大衣后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'hd' => array('zname'=>'横档','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         // 按照类别 左右 袖长 后衣长
         'dkzkc' => array('zname'=>'短裤左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'dkykc' => array('zname'=>'短裤右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'syzxc' => array('zname'=>'上衣左袖长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'cyzxc' => array('zname'=>'衬衣左袖长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'dyzxc' => array('zname'=>'大衣左袖长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'syyxc' => array('zname'=>'上衣右袖长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'cyyxc' => array('zname'=>'衬衣右袖长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'dyyxc' => array('zname'=>'大衣右袖长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'syhyc' => array('zname'=>'上衣后衣长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'cyhyc' => array('zname'=>'衬衣后衣长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
         'dyhyc' => array('zname'=>'大衣后衣长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),

     );
     return $return;
 }


    //获取工艺信息
    function craftwork(){
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
      //获取签名信息
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

      //获取工艺信息
      $out = $this->outDict($dicts);
      $result = array();
      foreach($out as $k => $v){
          $result[$k]["name"] = $v["name"];
          $this->aData = array();
          $this->lastLevel($v["children"]);
          $result[$k]["items"] = $this->aData;
      }
       $this->assign("result", $result);  //工艺信息
       $this->display('my_order.craftwork.html');
    }



}

?>
