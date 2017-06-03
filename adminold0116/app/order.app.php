<?php
use Cyteam\Message\ExpressMessage;
use Cyteam\Shop\Type\FdiyBase;
use Cyteam\Shop\Type\Types;
use Cyteam\Goods\Orders;
use Cyteam\Comment\Comment;
/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class OrderApp extends BackendApp
{
     public static $search_options = array(            
                'order_sn'   => '订单号',
//                 'r_order_id'   => 'RCMTM单号',
                'ship_name'   => '收货人姓名',
                'ship_mobile'   => '收货人手机',
                'user_name'    => '用户名',
                //'ship_name'    => "客户姓名",
        );
//     public static $body_options = array('定制方式','has_measure_0'=>'标准号','has_measure_1'=>'量体');
    private $mtm_logs_status = array(10030=> '制版', 10031=>'生产', '10032' =>'备货', 10034 =>'已发货'); 
    
    public static $_process_parents = array(
        1=>array('id'=>"1",'name'=>'功效'),
        6=>array('id'=>"6",'name'=>'口味'),
        11=>array('id'=>"11",'name'=>'规格'),
        16=>array('id'=>"16",'name'=>'包装'),
        21=>array('id'=>"21",'name'=>'犬种'),
        34=>array('id'=>"34",'name'=>'犬期'),
    
    );

    function __construct(){
        $mes_status_list = array(
          0=>"尚未推送",
          1=>"推送成功",
          2=>"推送失败",
        );
        $this->mes_status_list = $mes_status_list;
    	$this->OrderApp();
    }
    function OrderApp(){
    	parent::__construct();
    }
    /**
     *    管理
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function index()
    {
        if($_GET['mes_status'] == -1)
        {
            unset($_GET['mes_status']);
        }
        $conditions = $this->_order_conditions($_GET);
        $order_mtm_log = & m('ordermtmlogs');
        	if($_GET['add_time_go'] && $_GET['add_time_do'])
        	{
        		$addtimeg=strtotime($_GET['add_time_go']);
        		$addtimed=strtotime($_GET['add_time_do']);
        		if($addtimeg>$addtimed){
        			$this->show_message("搜索起始时间不能大于终止时间");
        			return false;
        		}
        		$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
        		GROUP BY T.order_id";
        		$db = &db();
        		$sql_c=$db->query($sql_count);
        		$mtm_timey = array();
        		while($rows=@mysql_fetch_assoc($sql_c)){
        		$mtm_timey[]=$rows;
        	}
        		$mtm_timeys=i_array_column($mtm_timey,'order_id');
        		$generaliyy=db_create_in($mtm_timeys,'order_id');
        				$conorder.=" AND ".$generaliyy;
        	}else{
        	if($_GET['add_time_go']){
        		$addtimeg1=strtotime($_GET['add_time_go']);
        			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >='{$addtimeg1}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
        			GROUP BY T.order_id";
        			$db = &db();
        			$sql_c=$db->query($sql_count);
        			$mtm_time1 = array();
        			while($rows=@mysql_fetch_assoc($sql_c)){
        			$mtm_time1[]=$rows;
        			}
        			
        			$mtm_times=i_array_column($mtm_time1,'order_id');
        			 $generaliys1=db_create_in($mtm_times,'order_id');
        			$conorder.=" AND ".$generaliys1;
        			}elseif($_GET['add_time_do']){
        			$addtimed=strtotime($_GET['add_time_do']);
        			$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date <='{$addtimed}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
        			GROUP BY T.order_id";
        			$db = &db();
        			$sql_c=$db->query($sql_count);
        			$mtm_time2 = array();
        			while($rows=@mysql_fetch_assoc($sql_c)){
        			$mtm_time2[]=$rows;
        			}
        		
        			$mtm_times2=i_array_column($mtm_time2,'order_id');
        			$generaliys2=db_create_in($mtm_times2,'order_id');
        			$conorder.=" AND ".$generaliys2;
        			}
        			}
        
        $model_order =& m('order');
        $order_goods_mod =& m('ordergoods');
        $order_logs_mod = & m('orderlogs');
        $page   =   $this->_get_page(30);    //获取分页信息
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'add_time';
             $order = 'desc';
            }
        }else{
            $sort  = 'add_time';
            $order = 'desc';
        }



     
        $orders = $model_order->find(array(
            'conditions'    => " 1 = 1 AND extension = 'news' " . $conditions . $conorder,
            'limit'         => $page['limit'],  //获取当前页的数据
            'order'         => "$sort $order",
            'count'         => true             //允许统计
        ));

        //=====  11.05 liang.li Beg 获取品类显示  =====
        $order_goods_pinlei = array();
        $order_id_arr = i_array_column($orders, 'order_id');
        $order_ids_str = db_create_in($order_id_arr,"order_id");
        if ($order_ids_str) 
        {
            $order_goods_list = $order_goods_mod->find(array(
                'conditions' => $order_ids_str,
            ));
            if ($order_goods_list) 
            {
                foreach ($order_goods_list as $key => $value) 
                {
                    if($value['type'] == 'fdiy')
                    {
                        $orders[$value['order_id']]['type'] = "fdiy";
                    }

                    $order_goods_pinlei[$value['order_id']][] = $value['goods_name'];
                    //=====  去除重复元素  =====
                    $order_goods_pinlei[$value['order_id']] = array_unique($order_goods_pinlei[$value['order_id']]);
                }
            }
        }
        //=====  liang.li End  =====
        foreach ($orders as $key=>$value){
        	if($value['order_id']){
                //获取时间
        		$mtm_time = $order_mtm_log->get(array(
        				'conditions'=>"order_id ='{$value['order_id']}'",
        				'order'=>"delivery_date DESC",
        		));
                if($mtm_time){
                    $orders[$key]['mtm_delivery_date'] = date("Y-m-d H:i",$mtm_time['delivery_date']);
                }
                elseif ($value['ship_time'])
                {
                    $orders[$key]['mtm_delivery_date'] = date("Y-m-d H:i",$value['ship_time']);
                }
        	}
        }

//echo '<pre>';print_r($this->mes_status_list);exit;

        $dq_time = time();
        $page['item_count'] = $model_order->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign('mes_status_list',$this->mes_status_list);
        $this->assign('conditions',$conditions);
        $this->assign('query',$_GET);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('search_options', self::$search_options);
        $this->assign('page_info', $page); 
        $this->assign('dqtime', $dq_time);
        $this->assign('orders', $orders);
        $_SESSION['admin_info']['order_reurl'] = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER["SERVER_PORT"] == '80' ? '' : (':'.$_SERVER["SERVER_PORT"])).$_SERVER["REQUEST_URI"];
        
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('order.index.html');
    }

    /**
     * 推送订单
     */
    public function mesf()
    {
        $orderId = intval($_GET['id']);
        $order = m('order');
        $goods = Types::createObj("fdiy");
        $res = $goods->mesf($orderId);
        if($res['err'])
        {
            $this->show_warning('err推送失败!'.$res['msg']);
        }
        else
        {
            if($res['data']['resultCode'] == 200)//=====  成功  =====
            {
                $this->show_warning('推送成功!');
            }
            else
            {
                $this->show_warning('rese推送失败!'.$res['data']['resultMsg'][0]);
            }
        }
    }
    
    /**
     * 发货处理
     */
    function onDeliver()
    {
        $orderId = intval($_GET['id']);
        
        if (!$orderId)
        {
            $this->show_warning('订单信息有误!');
            return ;
        }
        $order_mod = m('order');
        $region_mod = m('region');
        $order_info = $order_mod->get($orderId);
        
        $regionData = explode(',', $order_info['ship_area_id']);
        foreach ((array)$regionData as $key=>$id){
            if ($key == 0){
                $this->assign('region_'.$key,$region_mod->get_options(0));
            }else {
                $this->assign('region_'.$key,$region_mod->get_options($regionData[$key-1]));
            }
            $this->assign('def_rid_'.$key,$id);
        }
        
        
        imports("orders.lib");
        $orderLib = new Orders();
        
        $ship_init = $orderLib->getShipInit($order_info);
//         var_dump($order_info);
        $this->assign('ships',$ship_init['shippings']);
        $this->assign('def',$ship_init['defship']);
        
        
        if (!IS_POST)
        {
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
                'style'  => 'res:style/jqtreetable.css'
            ));
            
            $this->assign('order_info',$order_info);
            $this->display('order/order_deliver.form.html');
            
        }else{
            echo '<pre>';print_r($_POST);exit;
            
            //事务 存发货单 更改订单状态
            $rs = $order_mod ->ondeliver($_POST,$this->visitor->info['user_name']);
           
            if ($rs)
            {
                $type='edit';
                $op_name=$_SESSION['admin_info']['user_name'];
                $order_sn=$order_info['order_sn'];
                $remark="操作人员{$op_name}将订单号为{$order_sn}的订单发货，发货单号：".$rs;
                
                //订单日志
                imports("orderLogs.lib");
                $oLogs = new OrderLogs();
                $oLogs->_record(array(
                    'order_id' => $order_info['order_id'],
                    'op_id'    => $_SESSION['admin_info']['user_id'],
                    'op_name'  => $_SESSION['admin_info']['user_name'],
                    'behavior' => 'delivery',
                    'from'     => $order_info['status'],
                    'to'       => ORDER_SHIPPED,
                    'remark'  => $remark,
                ));
                
                //操作人员日志
                imports("operationlog.lib");
                $operation=new OperationLog();
                $operation->operation_log('',$type,$remark);
                
                //抵扣券
                $mODebit = &m('orderdebit');
                $debits = $mODebit->find("order_id = '{$order_info['order_id']}'");
                $sn = '';
                foreach ($debits as $row){
                    $sn .= $row['d_sn'].':'.$row['d_money'].'-';
                }
                $sn = empty($sn) ? '无' : $sn;
                /* 用户在平台（APP和PC）购买实物商品的总额，统计节点：已发货 */
                $abstract = '正常商品订单,收货人'.$order_info['ship_name'].',优惠券：'.$order_info['discount'].',优惠编号：'.rtrim($sn,'-').',第三方：'.$order_info['final_amount'].'。';
                
                get_client_finance($order_info['user_id'],$rs,1,$order_info['final_amount'],$abstract);
                
                //给用户发送已发货短信通知
                smsCode($_POST['ship_mobile'], 'deliver', 'background','', array('order_sn'=>$order_info['order_sn'],'express'=>$rs));
                
                $this->show_message('发货成功单号：['.$rs.']！',
                    					'订单列表', 'index.php?app=delivery');
                						return;
                
                $this->json_result(array('rs'=>$rs));
                return;
            }else {
                $this->show_message('发货失败！',
                    '重新发货', 'index.php?app=order&act=onDeliver&id='.$orderId);
                return;
            }
        }
    }
    //发货 选择物流公司计算价格
    function a_deliver(){
        $orderId = intval($_POST['oid']);
        $deliverId = intval($_POST['did']);
        
        if (!$orderId || !$deliverId)
        {
            $this->json_error('订单信息有误!');
            return ;
        }
        $order_mod = m('order');
        $order_info = $order_mod->get($orderId);
        imports("orders.lib");
        $orderLib = new Orders();
        $ship_init = $orderLib->getShipInit($order_info);
        $this->json_result($ship_init['defship']);
        
    }
    /**
    *格式化新老收货地址的log日志
    *@author liang.li <1184820705@qq.com>
    *@2015年7月28日
    */
    function formatAddress($arr) 
    {
        $ship_fields = array(
            'ship_area' => "收货地区",
            'ship_addr' => '收货地址',
            'ship_name' => '收货人',
            'ship_tel'  => '客户电话',
        );
        $serve = m('serve');
        $str = "";
        if ($arr['shipping_id'] == 2) 
        {
            $serve_info = $serve->get_info($arr['ship_store']);
            $str = "门店:".$serve_info['serve_name'].",客户姓名:".urldecode($arr['ship_name']).",客户电话:".urldecode($arr['ship_tel']);
        }
        else 
        {
            $arr_bak = $arr;
            unset($arr['shipping_id']);
            unset($arr['ship_area']);
            unset($arr['ship_addr']);
            foreach ($arr as $key => $value) 
            { 
                $value = urldecode($value);
                $str .= $ship_fields[$key].":".$value.",";
            }
            $arr_bak['ship_area'] = str_replace(",", " ", $arr_bak['ship_area']);
            $str .= "收货地址:".$arr_bak['ship_area']." ".$arr_bak['ship_addr'];
            
        }
        $str = trim($str,",");
        return $str;
    }
    
    /**
    *修改订单收货地址
    *@author liang.li <1184820705@qq.com>
    *@2015年7月21日
    */
    function editAddress() 
    {
        $ship_type = array('1'=>"快递服务",'2'=>"门店自提");
        
        $this->assign('ship_type',$ship_type);
        $order_mod = m('order');
        $region_mod = m('region');
        $order_address_mod = m('orderaddresslog');
        $member_mod = m('member');
        
        if (!IS_POST) 
        {
            $id = $_GET['id'];
            
            //=====  修改日志  =====
            $address_list = $order_address_mod->find(array(
                'conditions' => "order_id = $id",
                'order'      => "id DESC"
            ));
            if ($address_list) 
            {
                foreach ($address_list as $key => $value) 
                {
                    $address_list[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
                    $admin_info = $member_mod->get_info($value['admin_id']);
                    $address_list[$key]['admin_name'] = $admin_info['user_name'];
                    $old_new = json_decode(stripslashes_deep(urldecode($value['old_new'])),true);
                   $old_new_str = "<font style='color:red'>老数据</font>:".$this->formatAddress($old_new['old'])."<br>"."<font style='color:red'>新数据</font>:".$this->formatAddress($old_new['new']);
                    $address_list[$key]['old_new_str']= $old_new_str;
                }
            }
            $this->assign('address_list',$address_list);
            
            $order_info = $order_mod->get_info($id);

            $regionData = explode(',', $order_info['ship_area_id']);
            foreach ((array)$regionData as $key=>$id){
               if ($key == 0){
                   $this->assign('region_'.$key,$region_mod->get_options(0));
               }else {
                   $this->assign('region_'.$key,$region_mod->get_options($regionData[$key-1]));
               }
                $this->assign('def_rid_'.$key,$id);
            }

            
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
                'style'  => 'res:style/jqtreetable.css'
            ));
            
            $this->assign('order_info',$order_info);
            $this->display('order/order_address1.form.html');
        }
        else 
        {
            $id = $_GET['id'];
            $shipping_id = $_POST['shipping_id'];
            $order_info = $order_mod->get_info($id);
            if (!$order_info) 
            {
                $this->show_warning('ERROR');
                return ;
            }
            
            /* if (!in_array($order_info['status'], array(ORDER_PENDING,ORDER_WAITFIGURE,ORDER_ACCEPTED,ORDER_PRODUCTION,ORDER_STOCKING)))
            {
                $this->show_warning('订单已推送至RCM或已发货 无法修改收货地址 ');
                return ;
            } */
            
         
                $old_data['ship_name'] = urlencode($order_info['ship_name']);
                $old_data['ship_mobile']  = urlencode($order_info['ship_mobile']);
                $old_data['order_amount']  = $order_info['order_amount'];
                
                $old_data['ship_area']  = urlencode(str_replace('	', ",", $order_info['ship_area']));
                
                $old_data['ship_addr'] = urlencode($order_info['ship_addr']);
           
       
                for($i=0;$i<3;$i++){
                    if (isset($_POST['region_id_'.$i]) && !empty($_POST['region_id_'.$i]))
                    {
                        $region_name[$i] = $region_mod->getRegionName($_POST['region_id_'.$i]);
                    }
                }
                
                
                
                $data['ship_name']  = $_POST['ship_name'];
                $data['ship_area'] = implode(" ", $region_name);
                $data['ship_addr'] = $_POST['ship_addr'];
                $data['order_amount']  = $_POST['order_amount'];
                $new_data = $data;
                $new_data['ship_mobile'] = $_POST['ship_mobile'];
                $new_data['shipping'] = '快递服务';
     
                $new_data['shipping_id'] = 1;

                $order_mod->edit($id,$new_data,0);
            
            $fields['old'] = $old_data;
     
                $data['ship_name']  = urlencode($_POST['ship_name']);
                $data['shipping_id']  = urlencode($shipping_id);
                $data['ship_mobile']  = urlencode($_POST['ship_mobile']);
                $data['ship_addr'] = urlencode($_POST['ship_addr']);
                $data['ship_area'] = urlencode(implode(" ", $region_name));
            
            $fields['new'] = $data;
            $data_log['old_new'] = json_encode($fields);
            $data_log['add_time'] = time();
            $data_log['admin_id'] = $this->visitor->info['user_id'];
            $data_log['order_id'] = $id;
             import('operationlog.lib');
            $operate_log=new OperationLog();
            $type='edit';
            $remark=addslashes("操作人员：{$this->visitor->info['user_name']}修改了订单号为{$order_info['order_sn']}的配送地址<a href='index.php?app=order&amp;act=editAddress&amp;id={$id}&amp;cur_page={$shipping_id}'>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>详情&gt;&gt;</b></font></a>");
            $operate_log->operation_log('',$type,$remark); 
            $order_address_mod->add($data_log,false,0);
            $this->show_message('收货信息修改成功');
        }
        
    }
    
    /**
    *获得服务店
    *@author liang.li <1184820705@qq.com>
    *@2015年7月22日
    */
//     function get_serve() 
//     {
//         $mod = m('serve');
//         $pid = $_POST['pid'];
//         $list = $mod->find("region_id = '{$pid}'");
//         if ($list) 
//         {
//             foreach ($list as $key => $value) 
//             {
//                 $str .= '<tr class=serve >
//                 <th class="paddingT15"> '.$value['serve_name'].':</th>
//                 <td class="paddingT15 wordSpacing5">
//                 <input class="infoTableInput2" id="ship_name" type="radio" name="ship_store" value="'.$key.'" />
//                 </td>
//               </tr>';
//             }
//         }
        
//         $this->json_result($str);
        
//     }
    
    /**
     *ajax获得三级联动
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function get_region()
    {
        $region_mod = m('region');
        $pid = $_POST['pid'];
        if (!$pid)
        {
            $this->json_error('失败');
        }
         
        $list = $region_mod->get_options_html($pid,0);
        $this->json_result($list);
         
    }
    
    function _order_conditions($get = array()){
        $field = 'order_sn';
        array_key_exists($get['field'], self::$search_options) && $field = $get['field'];
        /* rcmtm订单号 查询用like 如果效率慢可以考虑 find_in_set  *///1111
        $conditions = $this->_get_query_conditions(array(array(
                'field' => $field,       //客户姓名，客户手机，收货人姓名，收货人手机进行搜索
                'equal' => 'LIKE',
                'name'  => 'search_name',
        ),
                array(
                        'field' => 'status',
                        'equal' => '=',
                        'type'  => 'numeric',
                ),
             array(
                 'field' => 'mes_status',
                 'equal' => '=',
                 'type'  => 'numeric',
             ),
            array(
                        'field' => 'add_time',
                        'name'  => 'add_time_from',
                        'equal' => '>=',
                        'handler'=> 'gmstr2time',
                ),array(
                        'field' => 'add_time',
                        'name'  => 'add_time_to',
                        'equal' => '<=',
                        'handler'   => 'gmstr2time',
                ),array(
                        'field' => 'order_amount',
                        'name'  => 'order_amount_from',
                        'equal' => '>=',
                        'type'  => 'numeric',
                ),array(
                        'field' => 'order_amount',
                        'name'  => 'order_amount_to',
                        'equal' => '<=',
                        'type'  => 'numeric',
                ),
        ));
        /* 量体信息查询 */
        if($get['has_measure']){
            $conditions .= " AND has_measure=".substr($get['has_measure'],-1);
        }
        return $conditions;
    }
    
    //为方便测试，修改订单价格为一毛钱
    function updateprice(){
    	
        //判断是否是管理员
        if($_SESSION['admin_info']['user_name'] != 'admin'){
            $this->show_warning('你不是超级管理员，不能进行此操作！');
            return;
        }
        $id = $_REQUEST['id'];
        $sn = $_REQUEST['ordersn'];
        //$amount = $_REQUEST['amount'];
		$amount = '0.01';
        if(!$id)
            exit('order id is null');

        if(!$amount)
            exit("amount is null");
		$_mod_order=&m('order');
		$order_info=$_mod_order->get($id);
        $sql = "update `cf_order` set final_amount = $amount where order_id = $id limit 1 ";
        $db = &db();
        $rs = $db->query($sql);

        imports("orderLogs.lib");
        $oLogs = new OrderLogs();
        $oLogs->_record(array(
                'order_id' => $id,
                'op_id'    => $_SESSION['admin_info']['user_id'],
                'op_name'  => $_SESSION['admin_info']['user_name'],
        		'beforedata'=>$order_info['final_amount'],
                'behavior' => 'quickPrice',
        ));
        imports("operationlog.lib");
        $operation=new OperationLog();
        $type='edit';
        $op_name=$_SESSION['admin_info']['user_name'];
        $order_sn=$order_info['order_sn'];
        $beforedata=$order_info['final_amount'];
        $remark="操作人员{$op_name}将订单号为{$order_sn}的订单价格由{$beforedata}改为0.01";
        $operation->operation_log('',$type,$remark);
        $this->show_warning('价格修改为RMB0.01元的修改操作成功 ');
    }

    /**
     * 修改订单状态
     * @author liang.li
     */
    public function updateOrder()
    {
        imports("orders.lib");
        $orderLib = new Orders();
        imports("orderLogs.lib");
        $oLogs = new OrderLogs();
        $order_log_mod = &m('orderlogs');
    	if (!IS_POST)
    	{
    	    $orderId = intval($_GET['id']);
    	    
    	    $list = $order_log_mod->find(array(
    	        'conditions' => "order_id = $orderId AND (behavior='update' OR behavior='autoCancel' OR behavior='cancel' OR behavior='remarks') ",
    	    ));
    	    
    	    if ($list) 
    	    {
    	        foreach ($list as $key => &$value) 
    	        {
    	            $value['alttime'] = date("Y-m-d H:i:s",$value['alttime']);
    	        }
    	    }
    	    $this->assign('order_log_list',$list);
    		
    		$cur_page= intval($_GET['cur_page']);
    		$order_mod = m('order');
    		$order_info = $order_mod->get($orderId);
    		$appoint = false;
    		if (isset($_GET['by']) && !empty($_GET['by'])){
    		    $appoint = str_replace('id_', '', $_GET['by']);
    		    $this->assign('defst',$order_info['status']);
    		    $order_info['status'] = $appoint;
    		    $this->assign('appoint',$appoint);
    		}
    		$status_list = $this->getStatus($order_info['status']);
    		$order_info['cur_status'] = $status_list[$order_info['status']];
    		$this->assign('order_info',$order_info);
    		$this->assign('status_list',$status_list);
    		$this->assign('cur_page',$cur_page);
    		$this->display("order.update.html");
    		
    	}
    	else
    	{
    	   
    		$order_id = intval($_POST['order_id']);
    		$cur_page = intval('cur_page');
    		$status_list = $this->getStatus($order_info['status']);
    		$status = isset($_POST['modify_id']) ? $_POST['modify_id'] : $_POST['status'];
    		$remark = trim($_POST['remark']);
    		$order_mod = m('order');
    		$order_info = $order_mod->get($order_id);
			$mes = '修改成功！';
			
    		$behavior = 'update';
    		if (!$remark) 
    		{
    		    $this->show_warning('备注是必填字段');
    		    return ;
    		}
    		$arr = array();
    		//=====  如果订单状态为交易成功  那么这里要触发返现或者送抵用券的的接口  Beg liang.li  =====
    		if ($order_info['status'] != $status)
    		{
    		    if ($status == ORDER_REMARKS)
    		    {
    		        $behavior = 'remarks';
    		        $msg = "订单（".$order_info['order_sn']."）添加备注，信息：".$remark;
    		    }
				//add 11.04 liang.li 当修改为已发货状态的时候 给用户发短信和系统消息 Beg
// 				if ($status == ORDER_SHIPPED)
//     		    {
// //     		        $order_info['waybillno'] = 11111111111;
//     		        if (!$order_info['waybillno']) 
//     		        {
//     		            $this->show_warning('订单缺少运单号');
//     		            return ;
//     		        }
// 					$arr['ship_time'] = gmtime();
// 					$behavior = 'delivery';
// 					//$msg = "您的订单（".$order_info['order_sn']."）已发货，物流单号".$order_info['waybillno']."，请注意签收，有质量问题请及时联系在线客服或拨打400-891-9867。";
// 					$msg = "订单（".$order_info['order_sn']."）已发货，运单号".$order_info['waybillno']."，若有问题请联系客服400-891-9867。";
					
// 					$phoneNum = $order_info['ship_mobile'];
// 					$user_id = $order_info['user_id'];
// 				    if ($phoneNum != ''){
// 					    $rs   = SendSms1($phoneNum, $msg);
// 					}
// 					if ($user_id){
// 					    sendSystem($user_id, '13', '订单通知', $msg);
// 					}
					
// 				}
				//=====  liang.li End  =====
				
				
				//管理员编辑退款中状态
// 				if ($status == ORDER_TUIKUANZHONG)
//     		    {	
// 					//已付款、生产中、已发货 这几个状态下可以操作退款
// 					$is_status = array(ORDER_SHIPPED, ORDER_ACCEPTED, ORDER_PRODUCTION);
// 					if(!in_array($order_info['status'], $is_status)) {
// 						$this->show_message('当前订单状态不可修改为退款中！',
//     					'back_list', 'index.php?app=order&page=' . $cur_page);
// 						return;
// 					}
// 					$user = $this->visitor->get();
// 					$order_refund_mod = m('orderrefund');
// 					$member_mod = m('member');
// 					$userInfo = $member_mod->get($order_info['user_id']);
// 					$data = array(
// 						'order_id'   => $order_info['order_id'],
// 						'action_id'  => $user['user_name'],
// 						'user_id'    => $order_info['user_id'],
// 						'user_name'  => $userInfo['user_name'],
// 						'order_status' => $order_info['status'],
// 						'add_time'   => gmtime(),
// 					);
// 					$orderrefund_info = $order_refund_mod->get("order_id=".$order_info['order_id']);
// 					if(!$orderrefund_info) {
// 						$order_refund = $order_refund_mod->add($data,false,0);
// 					} else {
// 						$order_refund = $order_refund_mod->edit($orderrefund_info['id'], $data,0);
// 					}
					
// 					if($order_refund) {
// 						$mes = 'APP端退款按钮已生成，请提示用户操作！';
// 						//生成退款按钮后给APP端发系统通知
// 						$dz = $member_mod->get($order_info['user_id']);
// 						include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
// 						$push = new XingeMeg();
// 						$push->toMasterXinApp($dz['user_token'], '【Mfd】退款通知', '系统已为您生成退款按钮，请点击提交退款原因。', array('url_type'=>'system', 'location_id'=>''));
// 					}
										
// 				}
// 				if($status == ORDER_WAITFIGURE)
// 				{
// 					$figure_order_m = m('figureorderm');
// 					$orderfigure= m('orderfigure');
// 					$orfigure=$orderfigure->get(array(
// 							'conditions' =>"order_id='{$order_id}'",
// 							'fields' =>"measure",
// 					));
// 					$figure_id = $figure_order_m->get("order_id='{$order_id}'");
// 					if($orfigure['measure'] == 6){
// 						$data['liangti_state']=1;
// 						$figure_order_m->edit($figure_id['id'],$data,0);
// 					}else{
						
// 						$data['liangti_state']=0;
// 						$figure_order_m->edit($figure_id['id'],$data,0);
// 					}
// 				}
    		   /*  if ($status == ORDER_FINISHED)
    		    {
    		        $transaction = $order_mod->beginTransaction();
    		        $res = $order_mod->submit($order_id,$this->visitor->info['user_id']);
    		        if (!$res['code'])
    		        {
    		            $order_mod->rollback();
    		            $this->show_warning($res['msg']);
    		            return;
    		        }
    		        $order_mod->commit($transaction);
    		    }
    		    
    			//=====  如果订单状态为交易成功  那么这里要触发返现或者送抵用券的的接口  Beg liang.li  =====
    			if ($status == ORDER_FINISHED) 
    			{
    			    $transaction = $order_mod->beginTransaction();
    			    $res = $order_mod->submit($order_id,$this->visitor->info['user_id']);
    			    if (!$res)
    			    {
    			       $this->show_warning('此订单返现或者送抵用券的操作失败!无法处理!请联系管理员');
    			       return;
    			    }
    			    $order_mod->commit($transaction);
    			} */
    			//=====  End  liang.li  =====
    			
    			if($status == ORDER_CANCELED){
    			    $res = $orderLib->cancelById($order_id,$order_info);
                    $behavior = 'cancel';
    			}
    			
    			$oLogs->_record(array(
    			        'order_id' => $order_info['order_id'],
    			        'op_id'    => $_SESSION['admin_info']['user_id'],
    			        'op_name'  => $_SESSION['admin_info']['user_name'],
    			        'behavior' => $behavior,
    			        'from'     => $order_info['status'],
    			        'to'       => $status,
    			        'remark'  => $remark,
    			        //ip
    			));
    			
    			
    			$old_status = $order_info['status'];
				$arr['status'] = ($status == 99) ? $order_info['status'] : $status;
				
    			$order_mod->edit($order_id,$arr,0);
    			
    			imports("operationlog.lib");
    			$operation=new OperationLog();
    			$type='edit';
    			$op_name=$_SESSION['admin_info']['user_name'];
    			$order_sn=$order_info['order_sn'];
    			$from=LANG::get(ORDER_STATUS)["{$order_info['status']}"];
    			$to=LANG::get(ORDER_STATUS)["{$status}"];
    			$remark=addslashes("操作人员".$op_name."将订单号{$order_sn}的订单状态从[{$from}]更新到[{$to}]<a href='index.php?app=order&amp;act=updateOrder&id={$order_info['order_id']}'>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>详情&gt;&gt;</b></font></a>");
    			$operation->operation_log('',$type,$remark);
    			
    			$this->show_message($mes,
    					'back_list', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order&page=' . $cur_page
    					);
    		}
    		else
    		{
    			$this->show_message('本次操作失败，没有修改订单状态',
    					'back_list', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order&page=' . $cur_page);
    		}
    	}
    }
    
    /**
    *修改订单的发货时间
    *@author liang.li <1184820705@qq.com>
    *@2015年8月27日
    */
    function updateShipTime() 
    {
        $orderId = intval($_GET['id']);
        $order_mod = m('order');
        $order_mod->edit($orderId,array("ship_time"=>"1238036041"));
    }

    /**
     * 根据现在的订单状态 获得可以修改的状态
     * @author liang.li
     * @param int $status
     */
    public function getStatus($status)
    {
    	return  array(
    			'11' => '等待买家付款',
    			'20' => '买家已付款，等待商家发货',
    			'30' => '商家已发货',
    			'40' => '交易成功',
    			'0'  => '交易已取消',
    			'50' => '待量体',
    			'60' => '订单生产中',
    			'99' => '订单备注',
    			'72' => '订单重下(修改)',
    			//'80' => '订单已退款',
    			);
    }
    
    function views(){
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $orderLib = new Orders();
        $order_info = $orderLib->getOrderInfo($order_id);

        $figure = $this->_get_order_figure($order_id);
        
        $mOg = &m('ordergoods');
        $goods = $mOg->find("order_id = '{$order_id}'");
        
        $oSt = &m('ordersuit');
        $oSuit = $oSt->find("order_id = '{$order_id}'");
        
        foreach ($goods as $row){
            $sIds[$row['suit_id']] = $row['suit_id'];
        }

        $mOl = &mr('orderlogs');
        $oLogs = $mOl->find("order_id = '{$order_id}'");
        
        
        echo '<pre>';
        echo "cf_order<br>";
        print_r($order_info);
        echo "cf_order_figure<br>";
        print_r($figure);
        echo "cf_order_goods<br>";
        print_r($goods);
        echo "cf_order_suit<br>";
        print_r($oSuit);
        echo "cf_order_logs<br>";
        print_r($oLogs);
        
    }

    function changetime()
    {
    	$figure_orderc2m=m("figure_orderc2m");
    	$order_figure =& m('orderfigure');
    	$figure_orderm=& m('figureorderm');
    	$figurechange=&m('figurechange');
        $custom_figure=m('customer_figure');
    	$figureliangti=&m('figure_liangti');
    	$member=m("member");
    	$members=$this->visitor->get();
    	$lt_state  = array(0=>'还未指派',1=>'已经指派',2=>'量体结束',3=>'已完成',7=>'已取消');
    	$serve_mod = &m('serve');
    	
    	$serves=$serve_mod->find(array(
    			'conditions' =>"1=1",
    			'fields' =>"idserve,serve_name",
    	));
    	foreach($serves as $key=>$val){
    		$serves[$key]=$val['serve_name'];
    	}
    	$ltime=array(am=>'上午',pm=>'下午');
    	$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    	$addtime = isset($_POST['addtime']) ? $_POST['addtime'] : 0;
    	$time = isset($_POST['time']) ? $_POST['time'] : 0;
    	$state_id = isset($_POST['state_id']) ?($_POST['state_id']) : 0;
    	$serve_id = isset($_POST['serve_id']) ? $_POST['serve_id'] : 0;
    	$cards = isset($_POST['cards']) ? trim($_POST['cards']) : 0;
    	$son_sn = isset($_POST['son_sn']) ? trim($_POST['son_sn']) : 0;
    	if(!$addtime)
    	{
    		return $this->json_error("没选时间");
    	}
    	if(!$time)
    	{
    		return $this->json_error("没选时间段");
    	}
    	$figure_old=$figure_orderm->get(array(
    			   'conditions'=>"son_sn='{$son_sn}'",
    			   'fields' =>'liangti_state,figure_liangti.liangti_id,figure_liangti.card_number as f_num,time,time_noon,server_id,son_sn,order_sn',
    			   'join'=>'has_liangti',
    			));
    	$memberr=$member->get(array(
    			'conditions' =>"user_id='{$figure_old['liangti_id']}'",
    			'fields' =>"real_name",
    	));
    	if($figure_old['liangti_state'] != $state_id)//量体状态
    	{
    		
    		if($figure_old['liangti_state'] ==2){
    			$data_cus['figure_state']=0;
    		}
    		
    		$data['liangti_state']=$state_id;
    		$data_cus['liangti_state']=$state_id;
    	}
    	if($figure_old['f_num'] != $cards && $cards)//量体师证件号
    	{
    		
    		$figureli=$figureliangti->get(array(
    				'join' =>'has_member',
    				'conditions'=>"card_number='{$cards}'",
    				'fields' =>'real_name,figure_liangti.liangti_id',
    		));
    		if($figureli){
    			$data['liangti_id']=$figureli['liangti_id'];
    			$data['liangti_name']=$figureli['real_name'];
    			$data_cus['liangti_id']=$figureli['liangti_id'];
    			$data_cus['liangti_name']=$figureli['real_name'];
    		}else{
    			return $this->json_error("量体师中没有这个证件号");
    		}
    		
    	}
    	if($figure_old['time'] != $addtime)//时间
    	{
    		$data['time']=$addtime;
    	}
    	if($figure_old['time_noon'] != $time)//时间段
    	{
    		$data['time_noon']=$time;
    	}
    	if($figure_old['server_id'] != $serve_id)//门店id
    	{
    		$data['server_id']=$serve_id;
    		$data_cus['id_serve']=$serve_id;
    	}
    	
    	if($order_id){
    		
    		 if($data){
    			$figure1=$order_figure->edit("son_sn='{$son_sn}'",$data);
    			$figure2=$figure_orderm->edit("son_sn='{$son_sn}'",$data);
    			$lists="update figure_orderc2m set '{$data}'  where order_id='{$order_id}'";
    			$db = &db();
    			$r=$db->query($lists);
    			$list = array();
    			while($row=@mysql_fetch_assoc($r)){
    				$list[]=$row;
    			}
    		}
    		if($data_cus && $figure_old['son_sn']){
    			$figure1=$custom_figure->edit("son_sn='{$figure_old['son_sn']}'",$data_cus);
    		}  
    		
    			$content='';
    			if(is_numeric($data['liangti_state'])){
    				$content .="[量体状态]从{$lt_state[$figure_old['liangti_state']]}更新为{$lt_state[$data['liangti_state']]}";
    			}
    			if($data['liangti_id']){
    			
    				$content .="[量体师]从{$memberr['real_name']}更新为{$data['liangti_name']}";
    			}
    			if($data['time']){
    				$content .= "[预约时间]从{$figure_old['time']}更新为{$data['time']}";
    			}
    			if($data['time_noon']){
    				$content .= "[预约时间段]从{$ltime[$figure_old['time_noon']]}更新为{$ltime[$data['time_noon']]}";
    			}
    			if($data['server_id']){
    				$content .= "[门店]从{$serves[$figure_old['server_id']]}更新为{$serves[$data['server_id']]}";
    			}
    			
    			$datte=array(
    				'order_id' =>$order_id,	//订单id
    				'son_sn' =>$son_sn,//标识
    				'mo_time' =>time(),//修改的时间
    				'mo_id' =>$members['user_id'],//修改人
    				'mo_name' =>$members['user_name'],//修改人姓名
    				'chancontent' =>$content,//修改内容
    			);
    			if($content){
    				$figuchange=$figurechange->add($datte);
    				if(!$figuchange){
    					return $this->json_error("修改记录存储失败");
    				}
    			}
    			
    			
    			return $this->json_result("修改成功");
    		
    	}
    	
    }
    
    
    
    
    /**
     *    查看
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function view(){
        
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$order_id){
            $this->show_warning('no_such_order');
            return;
        }
        $orderLib = new Orders();
        $res = $orderLib->getOrderInfo($order_id);

        $this->assign('order_info',$res);

        /*add by 小五 订单日志*/
        $order_log_mod = &m('orderlogs');
        $orderId = intval($_GET['id']);
        $list = $order_log_mod->find(array(
                'conditions' => "order_id = $orderId group by behavior,to_status order by log_id desc",
        ));

        $this->assign('order_log_list',$list);

        /* 获取订单信息 */
        $model_order =& m('order');
        $order_info = $model_order->get($order_id);
	
        if (!$order_info){
            $this->show_warning('no_such_order');
            return;
        }
        $order_info['amount'] = 0;
        //=====  11.05 liang.li Beg 折扣的价格 和 最终支付的价格  add by 小五 已在前端屏蔽 华仔要求 =====
        $order_info['zhekou'] = $order_info['goods_amount'] - $order_info['order_amount'];
        $bill_mod = m('paymentbills');
        if ($order_info['out_trade_sn']) 
        {
            $out_trade_sn = $order_info['out_trade_sn'];
            $bill_info = $bill_mod->get("payment_sn = $out_trade_sn");
            if ($bill_info['status']) 
            {
                $order_info['amount'] = $bill_info['amount'];
            }
        }
     
        
         //=====  liang.li End  =====
         
        
        /* add by 小五 查询是否为有效支付 start  */
        // `end_time` >0 视为有效支付金额 （sin确定）
        $real = false;
        $real_amount = '0.00';
        $real = $bill_mod->get(" `end_time` > 0 and order_sn = '{$order_info['order_sn']}'");
        $this->assign('real',isset($real['payment_sn']) ? true : false);
        $this->assign('real_amount',$real_amount);
        /* add by 小五 查询是否为有效支付 end  */
        
        //=====  商品相关  =====
        $order_goods_mod = m('ordergoods');
        $order_goods_list = $order_goods_mod->find(array(
            'conditions' => "order_id = $order_id",
            'index_key'  => "",
        ));
        
      
        $i=0;
       // print_exit($order_goods_list);
        foreach($order_goods_list as $key=>$value){
            $params[$key] = json_decode($value['params'],true);
        }

       
        $order['data']['goods']  = $this->_get_order_goods($order_id);
        $n = 0;

        foreach($order['data']['goods'] as $key=>$value){
            $order['data']['goods'][$key]['params_value'] = $order_goods_list[$n]['params_value'];
            $n++;
        }
        //获取刺绣信息
        $embs = array();
        $str = '';
        $num = 0;

        //加载diy类
        imports("diys.lib");
        $this->_Diys = new Diys();
 
            foreach($order['data']['goods'] as $key=>$val){
                if ('fdiy' == $val['type'])
                {
                   $order['data']['goods'][$key]['pstr'] =  $this->_Diys->_format_params($val['params']);
                   $order['data']['goods'][$key]['goods_name'] =  $this->_Diys->get_cname($val['cloth'],'/','fdiy_management');
                }else {
                    $order['data']['goods'][$key]['pstr'] =  $val['params']['oProducts']['spec_info'];
                }
        

            }
           
           
        $this->assign("operation" , $this->operation($order_info));
        //获取快递列表
        $shipping_mod=&m('shipping');
        $shiplists=$shipping_mod->find("enabled =1");
        if($shiplists){
            foreach($shiplists as $k1=>$v1){
                $shiplist[$v1['shipping_id']]=$v1['shipping_name'];
            }
            $this->assign("shiplist", $shiplist);
        }
        
        //抵扣券
        $mODebit = &m('orderdebit');
        $debits = $mODebit->find("order_id = '{$order_id}'");
        foreach ($debits as $row){
            $order_info['debit_amount'] += $row['d_money'];
        }
        
        $this->assign('dlist',$debits);
        
        //酷卡 add by 小五  （edit by liang.li 2016.02.17  酷卡增加线上线下标示）
        $mOKuka = &m('orderkuka');
        $special_code_mod = m('special_code');
        $kukas = $mOKuka->find("order_id = '{$order_id}' and is_active =1");
        if ($kukas) 
        {
            $k_id = i_array_column($kukas, 'k_id');
            $kuka_list = $special_code_mod->find(array(
                'conditions' => db_create_in($k_id,'id'),
            ));
            foreach ($kukas as &$row){
                $order_info['kuka_amount'] += $row['k_money'];
                $row['is_line'] = "线下";
                if ($kuka_list[$row['k_id']]['sign']) //=====  线上卡  =====
                {
                    $row['is_line'] = "线上";
                }
            }
        }
        
        $this->assign('kuka',$kukas);
        
        if($order_info["shipping_id"] == 2){
            $serve_mod = &m("serve");
            $store_info = $serve_mod->get("idserve='{$order_info["ship_store"]}'");
            
            $this->assign("store_info", $store_info);
        }
        
        if($order_info['invoice_com'] == 3){
            $order_info['invoice_title'] = json_decode($order_info['invoice_title'],1);
            $invoice = array(
                    'com' => '单位名称',
                    'sn' => '识别码',
                    'addr' => '注册地址',
                    'tel' => '注册电话',
                    'bank' => '开户银行',
                    'bank_num' => '银行账户',
            );
            $this->assign('invoice',$invoice);
        }
		
		//收货延期
		$mOsd = m('ordershipdelay');
		$has_delay  = $mOsd->get(array(
			'conditions' => "order_id=$order_id",
			'order'      => 'delay_time desc',
			'count'      => true,
			'index_key'	 => '',
		));
//echo '<pre>';print_r($order_info);exit;

		
        $mSet = &af('settings');
        $ck = $mSet->getOne('order_if_review');
        $this->assign('has_check',$ck);
		$this->assign('has_delay',$has_delay);
        $this->assign('order', $order_info);
        $this->assign('data',$order['data']);
        $this->assign('reUrl',$_SESSION['admin_info']['order_reurl']); //$_SERVER['HTTP_REFERER']
        $this->display('order.view.html');
    }

    function viewDelivery(){
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
//        $figure_change=&m("figurechange");
        if (!$order_id){
            $this->show_warning('no_such_order');
            return;
        }

        $orderLib = new Orders();

        $order_info = $orderLib->getOrderInfo($order_id);
//echo '<pre>';print_r($res);exit;

        if(!in_array($order_info['status'],array(30,40,0))){
            $this->show_warning('该订单不是发货状态');
            return;
        }
        $this->assign('expressInfo',$order_info);


//        $deliveryMdl = m('delivery');
//        $deliveryList = $deliveryMdl->get(array(
//            'conditions' => "tid = {$order_info['order_sn']}",
//            'index_key'  => "tid",
//        ));
//        $order_info['delivery'] = $deliveryList;
//        $message_obj=new ExpressMessage();
//        $expressInfo=$message_obj->checkExpressInfo($order_info['delivery']['logi_no'],$order_info['delivery']['corp_code']);
//        if(!is_array($expressInfo)){
//            return $this->show_warning($expressInfo);
//        }
//        $this->assign('expressInfo',$expressInfo);
        $this->display('order.express.html');
    }

    function changecon(){
    	$serve_mod = &m('serve');
    	$serves=$serve_mod->find(array(
    			'conditions' =>"1=1",
    			'fields' =>"idserve,serve_name",
    	));
    	foreach($serves as $key=>$val){
    		$servels[$key]=$val['serve_name'];
    	}
    	
    	
    	$lt_state  = array(0=>'还未指派',1=>'已经指派',2=>'量体结束',3=>'已完成',7=>'已取消');
    	$return=array(
    			'servels'=>$servels,
    			'lt_state'=>$lt_state,
    	);
    	
    	return $this->json_result($return);
    }
    
    function _get_order_figure($order_id){
        $figure_mod = m('orderfigure');
        $figure_info = $figure_mod->get('order_id = '.$order_id);
        if($figure_info)
            $figure_info['modi_time']=date('Y-m-d H:i:s',$figure_info['modi_time']);
        return $figure_info;
    }
    
    function _get_order_goods($order_id){
        
        $women = array(
                '0011' => array(
                        'cate_id' => '95000',
                        'cate_name' => '女西服',
                        'fabric_m' => '2.1',
                        'lining_m' => '2',
                        'gender' => 10041,
                ),
                '0012' => array(
                        'cate_id' => '98000',
                        'cate_name' => '女西裤',
                        'fabric_m' => '1.5',
                        'lining_m' => '0',
                        'gender' => 10041,
                ),
                '0016' => array(
                        'cate_id' => '11000',
                        'cate_name' => '女衬衣',
                        'fabric_m' => '2.8',
                        'lining_m' => '0',
                        'gender' => 10041,
                ),
                '0021' => array(
                        'cate_id' => '103000',
                        'cate_name' => '女大衣',
                        'fabric_m' => '1.68',
                        'lining_m' => '2',
                        'gender' => 10041,
                ),
        );
        $mOg = &m('ordergoods');
        $mOs = &m('ordersuit');
        $figure_mod = m('orderfigure');
        $customer_figure = m('customer_figure');     //顾客量体
        $goods = $mOg->find("order_id = '{$order_id}'");
        $figure_change = m('figurechange');
     
        foreach($goods as &$row){
        
        	/* 是在没办法，订单商品表 套装没有去order_suit读取，工艺、量体 哎!  add by 小五*/
        	if ($row['goods_image'] == 'no pic'){
        		$m_sinfo = $mOs->get("order_id = '{$order_id}' AND goods_id= '{$row['suit_id']}'");
        		$row['goods_image'] = isset($m_sinfo['id']) ? $m_sinfo['goods_image'] : '';
        	}
        	$figure_info = $history_data =$cnt_data = array();
        	/* add by 小五 量体信息明细 20151118 START */
        	if ($row['son_sn']){
        		$figure_progress= $this->_get_order_figure_progress($row['order_id'],$row['son_sn']);
        		
        		$figure_changes= $figure_change->find(array(
        				'conditions' =>"son_sn='{$row['son_sn']}'",
        		));
        		$figure_info = $figure_mod->get("son_sn = '{$row['son_sn']}'");
        		/* measure字段  1上门2门店 5历史量体  6指派量体师 */
        		$row['measure'] = $figure_info['measure'];
        		if (isset($figure_info['measure']) && $figure_info['measure'] == 5){
        			$history_data = $customer_figure->get("figure_sn = '{$figure_info['history_id']}'");
        			/* service_mode 1上门2门店3线下采集4后台录入 */
        			$row['service_mode'] = $history_data['service_mode'];
        			/* 统计使用次数 order_figure crount(history_id) 量体数据使用次数. order_id <= 当前order_id */
        			$cnt_data = $figure_mod->find(array(
        			"conditions" =>'1=1 and history_id='.$figure_info['history_id'].' and id <='.$figure_info['id'],
        			'count' => true,
        			));
        			$row['_cnt'] = $figure_mod->getCount();
        		}
        	}else{
        		$figure_changes= $figure_change->find(array(
        				'conditions' =>"order_id='{$row['order_id']}'",
        		));
        	}
        	/* add by 小五 量体信息明细 20151118 END */
        	if (!$figure_info){
        		$figure_info = $figure_mod->get("order_id = '{$row['order_id']}'");
        	}
//         	var_dump(json_decode($row['embs'],1));//vdm
        	$row['figure'] = $figure_info;
        	$row['figure_changes'] = $figure_changes;
        	$row['figure_progress'] = $figure_progress;
            $row['embs'] = json_decode($row['embs'],1);
            $row['params'] = json_decode($row['params'],1);
            if($women[$row['cloth']] && $row['params']){
                foreach ($row['params'] as $k=>&$v){
                    if($k) $dc[$k] = $k;
                    if($v) {
                        $zd[$k] = explode('|sin|', $v);
                        if(count($zd[$k]) == 1){
                            $dc[$v] = $v;
                        }else{
                            $dc[$zd[$k][0]] = $zd[$k][0];
                            $dcVal[$zd[$k][0]] = $zd[$k][1];
                            $v = $zd[$k][0];
                        }
                    }
                }
                unset($zd);
                $row['gender'] = 10041;
            }
        }
        if($dc){
            //$mDict = &m('dict');
            $mDict = &m('dict1');
            $this->_dict_info = $mDict->find(db_create_in($dc,'id'));
            $this->assign('dict_info',$this->_dict_info);
            $this->assign('dict_value',$dcVal);
        }
        return $goods;
        
    }
    
    
    function _get_order_figure_progress($order_id,$son_sn){
    
        $order_mod = &m('order');
    	$fOm = &m('figureorderm');
    	$serve_mod = &m('serve');
    	$liangti_mod = &m('figure_liangti');
    	$member_mod = &m('member');
    	$order_figure= &m('orderfigure');
        $lt_state  = array(0=>'还未指派',1=>'已经指派',2=>'量体结束',3=>'已完成',7=>'已取消');
        $order_info = $order_mod->get("order_id = '$order_id' ");
        
        if(!empty($order_info)){
            $figure_info = $fOm->get('son_sn ='.$son_sn);
        }
    	if($figure_info['liangti_id']){
            // 该订单指派的量体师的信息
            $lts = $liangti_mod ->get('liangti_id = '.$figure_info['liangti_id']);

        }
    	
        if($lts['liangti_id']){

            $lts_info = $member_mod->get('user_id ='.$lts['liangti_id']);
        }
    	if($figure_info['server_id']){
            // 该订单指派的门店信息
            $mendian = $serve_mod->get('idserve ='.$figure_info['server_id']);
        }
    	
    	
        if($mendian['userid']){

            $mendian_info = $member_mod->get('user_id ='.$mendian['userid']);
        }
      
        // 查看 量体派工 进度详情
   
        $data['measure']     = $order_info['measure'];
        $data['order_id']     = $figure_info['order_id'];
    	$data['liangti_state'] = $lt_state[$figure_info['liangti_state']];
    	$data['serve_id']=$figure_info['server_id'];
    	$data['liangti_state_id'] = $figure_info['liangti_state'];
        $data['serve_name']    = isset($mendian['serve_name']) ? $mendian['serve_name'] : '还未指派店铺' ;
        $data['s_card']        = isset($mendian['card_number']) ?  $mendian['card_number'] : '无';
        $data['linkman']    = isset($mendian['linkman']) ? $mendian['linkman'] : '' ;
        if($figure_info['liangti_state']==0)
        {
        	  $data['real_name']='还未指派量体师';
        }else
        {
        	if($figure_info['liangti_id'])
        	{
	        	  $data['real_name']    = $lts_info['real_name'];
	        	  $data['l_card']       = isset($lts['card_number']) ?  $lts['card_number'] : '无';
	        	  $data['phone_mob']    = isset($lts_info['phone_mob']) ? $lts_info['phone_mob'] : '' ;
        	}else
        	{
	        	  $data['real_name']    = $mendian['linkman'];
	        	  $data['l_card']       = isset($mendian['card_number']) ?  $mendian['card_number'] : '无';
	        	  $data['phone_mob']    = isset($mendian['mobile']) ? $mendian['mobile'] : '' ;
        	
        	}
        }
        
        
        
   
        $data['assign_time']    = $figure_info['assign_time'];
        //$data['phone_mob']    = isset($lts_info['phone_mob']) ? $lts_info['phone_mob'] : '' ;
        $data['time']    = isset($figure_info['time']) ? $figure_info['time'] : '' ;
        $data['time_noon']    = isset($figure_info['time_noon']) ? $figure_info['time_noon'] : '' ;
        $data['modi_time']    = $figure_info['modi_time'];
       
        if($figure_info['modi_time']){
        	
        $data['modi_time'] = date('Y-m-d H:i:s',$figure_info['modi_time']);
        }
        if($figure_info['assign_time']){
        
        $data['assign_time']    =  date('Y-m-d H:i:s',$figure_info['assign_time'] );
        
        }
        return $data;

    	
    
    
    }

    function checks(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $tp = isset($_GET['tp']) ? trim($_GET['tp']) : '';
        if(!$id || $tp==''){
            $this->show_warning('参数错误');
            return;
        }

        $this->_mod_order =& m('order');
        $order = $this->_mod_order->get($id);
        if(!$order || $order['status'] != ORDER_ACCEPTED || $order['has_measure']){
            $this->show_warning('参数错误');
            return;
        }
        if($tp == 'goes'){
    	    $mOc = &m('ordercron');
    	    $res = $mOc->_addToList($id);
    	    if($res){
    	        $status = ORDER_PRODUCTION;   // 生产中
    	        
    	        imports("orderLogs.lib");
    	        $oLogs = new OrderLogs();
    	        $oLogs->_record(array(
    	                'order_id' => $id,
    	                'op_id'    => $_SESSION['admin_info']['user_id'],
    	                'op_name'  => $_SESSION['admin_info']['user_name'],
    	                'behavior' => 'update',
    	                'from'     => $order['status'],
    	                'to'       => $status,
    	                'remark'  => "标准码已支付订单，被用户".$_SESSION['admin_info']['user_name']."在订单详情中操作了手动推送",
    	                //ip
    	        ));
    	        imports("operationlog.lib");
    	        $operation=new OperationLog();
    	        $type='edit';
    	        $op_name=$_SESSION['admin_info']['user_name'];
    	        $order_sn=$order['order_sn'];
    	        $from=LANG::get(ORDER_STATUS)["{$order['status']}"];
    	        $to=LANG::get(ORDER_STATUS)["{$status}"];
    	        $remark=addslashes("操作人员".$op_name."将订单号{$order_sn}的订单状态从[{$from}]更新到[{$to}]<a href='index.php?app=order&amp;act=updateOrder&id={$id}'>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>详情&gt;&gt;</b></font></a>");
    	        $operation->operation_log('',$type,$remark);
    	        $res = $this->_mod_order->edit($id,array('status' => $status));
    	        if($res){
    	            $this->show_message('操作成功');
    	            return;
    	        }
    	    }
        }
                
        $this->show_warning('操作失败');
        return ;

    }

    function check(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $tp = isset($_GET['tp']) ? trim($_GET['tp']) : '';
        if(!$id || $tp==''){
            $this->show_warning('参数错误');
            return;
        }
        $this->_mod_order =& m('order');
        $order = $this->_mod_order->get($id);
        if(!$order || $order['status'] != ORDER_CHECKING){
            $this->show_warning('参数错误');
            return;
        }
        $do = 1;
        switch ($tp){
            case 'go':
                
                $mSet = &af('settings');
                $ck = $mSet->getOne('order_if_review');
                if($ck == 'yes'){//不用审核的付款之后直接加入到队列表并把状态改为生产中
                    $res = $this->_mod_ordercron->_addToList($id);
                    if($res){
                        $data['status'] = ORDER_PRODUCTION;   // 生产中
                    }
                }
                
                
                
                //...这里还要对接rcmtm,但是暂时先这么写吧.
                $dj = 1;
                $transaction = $this->_mod_order->beginTransaction();
                $oInfo = $this->_check_go($order);
                if (!$oInfo){
                    $this->_mod_order->rollback();
                    $do = 0;
                    break;
                }
                $this->_mod_order->commit($transaction);
//                 if($dj){
                    $status = ORDER_PRODUCTION;
                    $member=& m('member');
                    $store=& m('store');
                    $tailor=$member->get(array('conditions'=>"user_id={$order['store_id']}"));
                    $store_tailor=$store->get(array('conditions'=>"store_id={$order['store_id']}"));
                    smsAuthCode($order['kh_mobile'], 'order', 'order_check_client', 'reset', 'pc','',array('store_name'=>$store_tailor['store_name'],'order_sn'=>$order['order_sn']));
                    smsAuthCode($tailor['phone_mob'], 'order', 'order_check_tailor', 'reset', 'pc','',array('tailor_nickname'=>$tailor['nickname'],'order_sn'=>$order['order_sn']));

//                 }else{
//                     $status = ORDER_CHECKFAIL;
//                 }
                break;
            case 'gose':
                $res = $this->_mod_ordercron->_addToList($id);
                if($res){
                    $status = ORDER_PRODUCTION;   // 生产中
                }
                break;
            case 'bc':
                $status = ORDER_CHECKFAIL;
                break;
            case 'no':
                $status = ORDER_CANCELED;
                break;
            case 'fini':
                $status = ORDER_FINISHED;
                break;
        }
        if($do){
            //这里问题很大啊,如果对接成功但是没有更新状态咋弄?应该有个中间表做记录吧?
            $res = $this->_mod_order->edit($id,array('status' => $status));
            if($res){
                $this->show_message('操作成功');
                return;
            }
        }else{
            $this->show_warning('操作失败');
            return ;
        }


    }

    /**
     * 订单审核通过时做的
     *
     * @version 1.0.0 (Jan 26, 2015)
     * @author Ruesin
     */
    function _check_go($order = array()){
        //同步会员量体信息
        $res = $this->_check_go_figure($order);
        if(!$res) return false;
        //同步裁缝服务数
        $store_mod =& m('store');
        $res = $store_mod->setInc(array('store_id'=>$order['store_id']),'service_num');
        if(!$res) return false;
        //同步订单数据到mtm

        return true;
    }

    /**
     * 同步量体信息
     *
     * @version 1.0.0 (Jan 26, 2015)
     * @author Ruesin
     */
    function _check_go_figure($order = array()){
        $mMf    =& m('member_figure');
        $mOf    =& m('orderfigure');
        $figure = $mOf->get("order_id = '{$order['order_id']}'");
        $mfigure = $mMf->get("userid = '{$figure['userid']}' AND storeid = '{$figure['storeid']}'");
        $data = $figure;
        $data['lasttime']     = gmtime();
        unset($data['id']);
        unset($data['order_id']);
        if($mfigure){
            $data['order_num']    = $mfigure['order_num'] + 1;
            $data['order_amount'] = $mfigure['order_amount'] + $order['kh_order_amount'];
            $where = " figure_sn = '{$mfigure['figure_sn']}' ";

            $res = $mMf->edit($where,$data);
            //`alias``remark`

        }else{
            $data['order_num']    = 1;
            $data['order_amount'] = $order['kh_order_amount'];

            $res = $mMf->add($data);
        }
        if($res < 0){
            return false;
        }else{
            return true;
        }

    }
    /**
     * 量体信息修改
     * @author 小五
     */
    function ufigure(){
    	
    	$order_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $user=$this->visitor->get();
    	if(!$order_id)
    	{
    		$this->show_warning('Hacking Attempt');
    		return ;
    	}
    	
    	$this->_mod_order =& m('order');
    	$order = $this->_mod_order->get($order_id);
    
    	if(!$order || !in_array($order['status'], array(11,12,20)) ){
    		$this->show_warning('该订单此状态下不允许更改量体数据!');
    		return;
    	}
    	
    	$figure = $this->_get_order_figure($order_id);
    
    	if(!$figure)
    	{
    		$this->show_warning('Hacking Attempt');
    	}
    	
    	if(!IS_POST)
    	{
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
    				'style'  => 'res:style/jqtreetable.css'
    		));
    		$this->assign("figure", $figure);
    		$this->display('figure/ofigure.form.html');
    	}
    	else
    	{
    		$data = array();
    		foreach ((array)$_POST as $k=>$r){
    			if (is_numeric($r) && !empty($r)){
    				$data[$k] = $r;
    			}
    		}
            
    		if (!$data){
    			$this->show_message("缺少量体细节数据", '返回订单列表',$_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order');
    			return;
    		}
    		$figure_mod = m('orderfigure');
    		$figure_orderm = m('figureorderm');
    		$customer_figure = m('customer_figure');
    	    
    		$res = $figure_mod->edit($figure['id'],$data);
    		// 这地方 修改一个字段 其他两个表的字段 都需要同步修改 
    		if($res)
    		{
	    		$transaction = $figure_orderm->beginTransaction();
		        $lt_zd = $figure_orderm->edit("order_id = $order_id",$data);
		        if(!$lt_zd){
		               	
		           $figure_orderm->rollback();
		        }
		        $figure_orderm->commit($transaction);
    			
    		}
    		
	        


    		if(!$res)
    		{
    			$msg = $figure_mod->get_error();
    			$this->show_warning($msg);
    			return;
    		}
    		
    		$this->show_message("修改量体数据成功！", '返回订单列表', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order');
    	}
    }
/*
 * 导出量体订单
 * @author shao
 * */
    function figure_export(){
    	$conditions = " 1 = 1 AND extension ='news'";
    	$order_mtm_log = & m('ordermtmlogs');
    	if($_GET['add_time_go'] && $_GET['add_time_do'])
    	{
    		$addtimeg=strtotime($_GET['add_time_go']);
    		$addtimed=strtotime($_GET['add_time_do']);
    		if($addtimeg>$addtimed){
    			$this->show_message("搜索起始时间不能大于终止时间");
    			return false;
    		}
    		$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
    		GROUP BY T.order_id";
    		$db = &db();
    		$sql_c=$db->query($sql_count);
    		$mtm_timey = array();
    		while($rows=@mysql_fetch_assoc($sql_c)){
    		$mtm_timey[]=$rows;
    	}
    	$mtm_timeys=i_array_column($mtm_timey,'order_id');
    		$generaliyy=db_create_in($mtm_timeys,'order_id');
    				$conorder.=" AND ".$generaliyy;
    	}else{
    	if($_GET['add_time_go']){
    	$addtimeg1=strtotime($_GET['add_time_go']);
    	$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >='{$addtimeg1}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
    	GROUP BY T.order_id";
    	$db = &db();
    	$sql_c=$db->query($sql_count);
    	$mtm_time1 = array();
    	while($rows=@mysql_fetch_assoc($sql_c)){
    	$mtm_time1[]=$rows;
    	}
    	
    	$mtm_times=i_array_column($mtm_time1,'order_id');
    	$generaliys1=db_create_in($mtm_times,'order_id');
    	$conorder.=" AND ".$generaliys1;
    	}elseif($_GET['add_time_do']){
    	$addtimed=strtotime($_GET['add_time_do']);
    	$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date <='{$addtimed}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
    	GROUP BY T.order_id";
    	$db = &db();
    			$sql_c=$db->query($sql_count);
    					$mtm_time2 = array();
    					while($rows=@mysql_fetch_assoc($sql_c)){
    					$mtm_time2[]=$rows;
    	}
    	
    	$mtm_times2=i_array_column($mtm_time2,'order_id');
    	$generaliys2=db_create_in($mtm_times2,'order_id');
    					$conorder.=" AND ".$generaliys2;
    	}
    	}
    	if($_GET['sinexporttype'] == 'fall')
    	{
    		unset($_GET['sinexporttype']);
    		$conditions .= $this->_order_conditions($_GET);
    	}elseif($_GET['sinexporttype'] == 'fsel')
    	{
    		if(!$_GET['ids']){
    			return ;
    		}
    		$conditions .= " AND order_id IN ({$_GET['ids']})";
    		 
    	}
    	$order_mod = m('order');
    	$mSv = &m('serve');
    	$mOrderFigure = &m('orderfigure');
    	$mMem = &m('member');
    	$figure_liangti=m("figure_liangti");
    	$figureorderm= &m('figureorderm');
    	$order_goods= &m('ordergoods');
    	$orders = $order_mod->findAll(array(
    			'conditions' => $conditions." AND has_measure =1".$conorder,
    			//'fields' => 'order_id,add_time,order_sn,r_order_id,user_name,ship_name,ship_mobile,ship_addr,source_from',
    	));
    	$serves=$mSv->find(array(
    			'conditions' =>"1=1",
    			'fields' =>'userid,serve_name,linkman,mobile,card_number',
    			'index_key' => 'idserve',
    			
    	));
    	//下单日期	APP订单号	rcmtm订单号品类	收货人姓名	量体人姓名	量体人手机号	指派门店	门店负责人	量体师	量体师证号	量体师联系电话	是否量体下单	客户预约时间	量体到位时间	店长指派时间
    	//是否量体下单	 是		
    	//order:下单日期	APP订单号	rcmtm订单号	收货人姓名
    	//serve:
    	//figure_liangti 量体师证号
    	//order_figure: 客户预约时间  指派门店  门店负责人  量体师 量体人姓名  量体人手机号
    	// figure_order_m: 量体到位时间  店长指派时间:assign_time
    	//member:量体师联系电话
    	//order_goods :品类
    	if($orders)
    	{
    		foreach($orders as $key=>$val)
    		{
    			$ids[$val['order_id']] = $val['order_id'];
    		}
    		if($ids){
    			$of = $mOrderFigure->find(array(
    					"conditions"=>db_create_in($ids,'order_id'),
    					//'index_key'=>'order_id'
    					
    			));
    			$orderg=$order_goods->find(array(
    					'conditions' =>db_create_in($ids,'order_id')." AND son_sn != '' ",
    					'index_key' =>'son_sn',
    			));
    			$figureom=$figureorderm->find(array(
    					'conditions' =>db_create_in($ids,'order_id')." AND son_sn != '' ",
    					'index_key' =>'son_sn',
    			));
    			
    		}
    		$times=array(
    				'am' =>'上午',
    				'bm' =>'下午',
    		);
    		$lange=array(
    				'11' => '待付款', //11 待付款
    				'12' => '待量体', //12 待量体
    				'20' => '已支付', //20 已支付
    				'60' => '生产中', //60 //生产中
    				'61' => '备货中',   // 61
    				'30' => '已发货', //30
    				'41' => '返修中', //41
    				'40' => '已完成', //40
    				'70' => '退款中',//70
    				'80' => '已退款',//70
    				'0' => '已取消', //0
    				'43' => '订单异常', //43 订单异常(订单推送异常)
    				'44' => '物流异常', //44 订单异常(物流推送异常)
    				'am' =>'上午',
    				'bm' =>'下午',
    		);
    	 	if($of){
    			foreach($of as $fk=>$fv)
    			{
    				
    				if($fv['liangti_id'] !=0){
    				 $figurel=$figure_liangti->get(array(
    						'conditions' =>"liangti_id = '{$fv['liangti_id']}' ",
    						'join'=>'has_member',
    						'index_key'=>'liangti_id',
    				 ));
    				
    				 $car_num=$figurel['card_number'];
    				 $user_name=$figurel['user_name'];
    				 $liangti_name=$figurel['real_name'];
    				}else{
    				$car_num=$serves[$fv['server_id']]['card_number'];
    				$user_name=$serves[$fv['server_id']]['mobile'];
    				$liangti_name=$serves[$fv['server_id']]['linkman'];
    				}
    				$order[$fv['id']] = array(
    						
    						'add_time' => date('Y-m-d',$orders[$fv['order_id']]['add_time']), //下单日期
    						'order_sn'    => "'".$orders[$fv['order_id']]['order_sn'],    //	APP订单号
    						'order_status' =>$lange[$orders[$fv['order_id']]['status']],//app订单状态
    						'r_order_id'  => $orders[$fv['order_id']]['r_order_id'] ? implode(',',array_filter(explode(',', $orders[$fv['order_id']]['r_order_id']))) : '', //RCMTM订单号
    						'cloth' => $orderg[$fv['son_sn']]['goods_name'],//品类
    						'ship_name' =>$orders[$fv['order_id']]['ship_name'],//收货人姓名
    						'realname_c'=>$fv['realname'],//量体人姓名  
    						'phone'=>$fv['phone'],//量体人手机号
    						'serve_name'=>$serves[$fv['server_id']]['serve_name'],//指派门店
    						'linkman'=>$serves[$fv['server_id']]['linkman'],//门店负责人
    						'liangti_name'=>$liangti_name,//量体师
    						'card_number'=>$car_num,//量体师证号
    						'user_name'=>$user_name,//量体师联系电话
    						'is_figure' => '是',//是否量体下单
    						'time_custom'=>$fv['time'].$times[$fv['time_noon']],//客户预约时间 	
    						'modi_time' => date('Y-m-d',$figureom[$fv['son_sn']]['modi_time']),//量体到位时间
    						'assign_time' =>date('Y-m-d',$figureom[$fv['son_sn']]['assign_time']),//店长指派时间
    						);
    			}
    			$fields_name = array('下单日期','APP订单号','APP订单状态','RCMTM订单号','品类','收货人姓名','量体人姓名','量体人手机号','指派门店','门店负责人','量体师','量体师证号','量体师联系电话','是否量体下单','客户预约时间','量体到位时间','店长指派时间');
    			array_unshift($order,$fields_name);
    			$this->export_to_csv($order, 'order', 'gbk');
    		} 
    		
    	}else{
    		return;
    	}
    	
    	
    	}
    	
    

    /**
     * 导出订单
     * @author Ruesin
     */
    function export(){
        $conditions = " 1 = 1 AND extension ='news' ";
//        $order_mtm_log = & m('ordermtmlogs');
        if($_GET['add_time_go'] && $_GET['add_time_do'])
        {
        	$addtimeg=strtotime($_GET['add_time_go']);
        	$addtimed=strtotime($_GET['add_time_do']);
        	if($addtimeg>$addtimed){
        		$this->show_message("搜索起始时间不能大于终止时间");
        		return false;
        	}
        	$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >= '{$addtimeg}' AND delivery_date <= '{$addtimed}' ORDER BY delivery_date DESC) AS T
        	GROUP BY T.order_id";
        	$db = &db();
        	$sql_c=$db->query($sql_count);
        	$mtm_timey = array();
        	while($rows=@mysql_fetch_assoc($sql_c)){
        	   $mtm_timey[]=$rows;
            }
             $mtm_timeys=i_array_column($mtm_timey,'order_id');
        	$generaliyy=db_create_in($mtm_timeys,'order_id');
        			$conorder.=" AND ".$generaliyy;
        	}
        else{
        	if($_GET['add_time_go']){
        	$addtimeg1=strtotime($_GET['add_time_go']);
        	$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date >='{$addtimeg1}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
        	GROUP BY T.order_id";
        	$db = &db();
        	$sql_c=$db->query($sql_count);
        	$mtm_time1 = array();
        	while($rows=@mysql_fetch_assoc($sql_c)){
        	$mtm_time1[]=$rows;
        	}

        	$mtm_times=i_array_column($mtm_time1,'order_id');
        	$generaliys1=db_create_in($mtm_times,'order_id');
        	$conorder.=" AND ".$generaliys1;
        	}elseif($_GET['add_time_do']){
        	$addtimed=strtotime($_GET['add_time_do']);
        	$sql_count="SELECT order_id FROM(select * from cf_order_mtm_logs where delivery_date <='{$addtimed}' AND delivery_date <> 0 ORDER BY delivery_date DESC) AS T
        			GROUP BY T.order_id";
        			$db = &db();
        			$sql_c=$db->query($sql_count);
        			$mtm_time2 = array();
        			while($rows=@mysql_fetch_assoc($sql_c)){
        			$mtm_time2[]=$rows;
        			}

        			$mtm_times2=i_array_column($mtm_time2,'order_id');
        			$generaliys2=db_create_in($mtm_times2,'order_id');
        			$conorder.=" AND ".$generaliys2;
        	}
      }
        if($_GET['sinexporttype'] == 'all'){
            unset($_GET['sinexporttype']);
            if($_GET['mes_status'] == -1)
            {
                unset($_GET['mes_status']);
            }
            $conditions .= $this->_order_conditions($_GET);
        }elseif($_GET['sinexporttype'] == 'sel'){
            if(!$_GET['ids']){
                return ;
            }
            $conditions .= " AND order_id IN ({$_GET['ids']})";
        }
        
        $order_mod = m('order');
//    echo '<pre>';print_r($conditions);exit;
    
        $orders = $order_mod->find(array(
            'conditions' => $conditions.$conorder,
            'order' => "order_id DESC",
        ));
        
        foreach ($orders as $row){
            
            $ids[$row['order_id']] = $row['order_id'];
            
            $uIds[$row['user_id']] = $row['user_id'];
            
            if($row['shipping_id'] == '2'){
                $svIds[$row['ship_store']] = $row['ship_store'];
            }
            
        }
        
        $mOgoods = &m('ordergoods');
        $gdTem = $mOgoods->find(array(
            'conditions' => db_create_in($ids,'order_id'),
            'order'  => "order_id DESC",
        ));
        
        $order_goods_mod = m('ordergoods');
        $mod_fbcategory = &m("fbcategory");
        
        foreach ($gdTem as $row){
            $order_sn = $orders[$row['order_id']]['order_sn'];
            $add_time = $orders[$row['order_id']]['add_time'];
            $ship_name = $orders[$row['order_id']]['ship_name'];
            $ship_address = $orders[$row['order_id']]['ship_area'].$orders[$row['order_id']]['ship_addr'];
            if($orders[$row['order_id']]['ship_mobile'])
            {
                $ship_tel   = $orders[$row['order_id']]['ship_mobile'];
            }
            if($orders[$row['order_id']]['ship_tel'])
            {
                $ship_tel   = $orders[$row['order_id']]['ship_tel'];
            }

            //计算基料编码
            
            $order_goods_list = $row;
            $params = $order_goods_list['params'];
            $params_arr = json_decode($params,true);
            $baozhuang = "";
            $quantity = 0;
            $code = "";
            if ($order_goods_list['size'] == "diy") 
            {
                $fb_bz_info = [];
                if ($order_goods_list['items']) 
                {
                    $fb_bz_info = $mod_fbcategory->find(array(
                        'conditions' => "cate_id IN ({$order_goods_list['items']})",
                        'index_key' => "parent_id",
                    ));
                }
                
                $baozhuang = $fb_bz_info[16]['cate_name'];
                $kezhong   = $fb_bz_info[11]['cate_name'];
                $quantity = $order_goods_list['quantity'];
                //物料编码
                $fidybase = new FdiyBase();
                $code = $fidybase->fmoatCode($params_arr);
                    
            }
            else 
            {
                   $spec_desc = unserialize($params_arr['oProducts']['spec_desc']);
                   $spec_value = $spec_desc['spec_value'];

                   $baozhuang = isset($spec_value[6]) ? $spec_value[6] : '';
                   $kezhong   = isset($spec_value[5]) ? $spec_value[5] : '';
                   $quantity = $order_goods_list['quantity'];
            }
            $dog_name = $row['dog_name'];
            $dog_date = $row['dog_date'];
            $dog_desc = $row['dog_desc'];
            
            $order[$row['order_id']] = array(
                    'add_time'    => date('Y/m/d H:i',$add_time), //日期
                    'order_sn'    => $order_sn,             //APP订单号
                    'ship_name'   => $ship_name,    //收货人姓名
                    'ship_tel'   => $ship_tel,    //收货人姓名
                    'ship_addr'   => $ship_address, //收货人地址
                    'xiaoshouzuzhi' => '麦富迪',
                    'wuliaocode' => $code,
                    'headerremark' =>"",
                    'goods_sn' =>"",
                    'baozhuang' =>$baozhuang,
                    'danwu' => $kezhong,
                    'shuliang' => $quantity,
                    'style_img' => SITE_URL.$order_goods_list['style'],
                    'chongwu_name' => $dog_name,
                    'chongwu_sex' => "",
                    'chongwu_age' => $dog_date,
                    'jiyu' => $dog_desc,
                    'remark' => "",
            );
            
        }
        $res = $order;
        $fields_name = array('日期','订单编码','客户名称','客户电话','送货地址', '销售组织','物料编码','Header备注',' 产品编码', '包装方式','重量单位','数量','个性化图片文件','宠物名称','宠物性别','宠物生日','主人寄语','行项目备注');
        array_unshift($res,$fields_name);
        $this->export_to_csv($res, '订单跟进-'.date('YmdHis',time()), 'gbk');
    }

    
    
    
    
    
    //========================== 即拍即做订单 Bgn =======================//
//     public function jpjz(){
//         $this->_mod_order =& m('order');
//         $search_options = array(
//                 'order_sn' => '订单号',
//                 'user_name' => '用户',
//                 'kf_name' => '客服'
//         );
//         $field = 'order_sn';
//         array_key_exists($_GET['field'], $search_options) && $field = $_GET['field'];
//         $conditions = $this->_get_query_conditions(array(array(
//                 'field' => $field,
//                 'equal' => 'LIKE',
//                 'name'  => 'search_name',
//         ),array(
//                 'field' => 'status',
//                 'equal' => '=',
//                 'type'  => 'numeric',
//         ),array(
//                 'field' => 'add_time',
//                 'name'  => 'add_time_from',
//                 'equal' => '>=',
//                 'handler'=> 'gmstr2time',
//         ),array(
//                 'field' => 'add_time',
//                 'name'  => 'add_time_to',
//                 'equal' => '<=',
//                 'handler'   => 'gmstr2time_end',
//         ),array(
//                 'field' => 'order_amount',
//                 'name'  => 'order_amount_from',
//                 'equal' => '>=',
//                 'type'  => 'numeric',
//         ),array(
//                 'field' => 'order_amount',
//                 'name'  => 'order_amount_to',
//                 'equal' => '<=',
//                 'type'  => 'numeric',
//         ),
//         ));
//         $page   =   $this->_get_page(30);
//         if (isset($_GET['sort']) && isset($_GET['order'])){
//             $sort  = strtolower(trim($_GET['sort']));
//             $order = strtolower(trim($_GET['order']));
//             if (!in_array($order,array('asc','desc'))){
//                 $sort  = 'add_time';
//                 $order = 'desc';
//             }
//         }else{
//             $sort  = 'add_time';
//             $order = 'desc';
//         }
        
//         $orders = $this->_mod_order->find(array(
//                 'conditions'    => " 1 = 1 AND extension = 'jp' " . $conditions,
//                 'limit'         => $page['limit'],
//                 'order'         => "$sort $order",
//                 'count'         => true
//         ));
        
//         $page['item_count'] = $this->_mod_order->getCount();
//         $this->_format_page($page);
//         $this->assign('filtered', $conditions? 1 : 0);
        
//         $this->assign('search_options', $search_options);
//         $this->assign('page_info', $page);
//         $this->assign('orders', $orders);
//         $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
//                 'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
//         $this->display('orderj/index.html');
//     }
    
//     public function add_jpjz(){
//         $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js'));
//         $this->assign('build_editor',$this->_build_editor(array('belogn'=>BELONG_GOODS,'area_id'=>'content')));
//         $this->display('order.jpjz.form.html');
//     }
//     public function save_jpjz(){
//         $mOrder  = $this->_mod_order =& m('order');
//         $mOrderJ = &m('order_jpjz');
//         $mMember = &m('member');
//         $this->_mod_pay = &m("payment");
        
//         $payment = $this->_mod_pay->get(array(
//                 'conditions' => "enabled=1 AND ismobile = 1",
//                 'order'      => "sort_order DESC"
//         ));
        
//         $user = $mMember->get($_POST['user_id']);
        
//         $order_sn = $this->_gen_order_sn();
        
//         $time = gmtime();
//         $amount = $_POST['order_amount'];
//         $data = array(
//                 'order_sn'     => $order_sn,
//         	    'order_amount' => $_POST['order_amount'],
//                 'user_id'      => $_POST['user_id'],
//                 'jp_id'        => $_POST['jp_id'],
//                 'kf'           => $_POST['kf'],
//                 'add_time'     => $time,
//                 'content'      => $_POST['content'],
//         );
//         $odata = array(
//                 'order_sn'     => $order_sn,
//                 'discount'     => '0',
//                 'goods_amount' => $amount,
//                 'order_amount' => $amount,
//                 //'kh_order_amount' => $amount,
//                 //'kh_ship_pay'  => '',
//                 //'figure_type'  => '',
//                 //'cloth'        => '',
//                 //'fabric'       => '',
//                 //'craft'        => '',
//                 //'embs'         => '',
//                 //'source'       => '',
//                 //'source_id'    => '',
//                 //'store_id'     => $user['user_id'],
//                 //'store_name'   => $user['user_name'],
//                 'user_id'      => $user['user_id'],
//                 'user_name'    => $user['user_name'],
//                 'status'       => ORDER_PENDING,
//                 'add_time'     => $time,
//                 'payment_id'   => $payment['payment_id'],
//                 'payment_name' => $payment['payment_name'],
//                 'payment_code' => $payment['payment_code'],
//                 //'out_trade_sn' => '',
//                 //'pay_time'     => '',
//                 //'pay_message'  => '',
//                 //'kh_payment_id'   => $payment['payment_id'],
//                 //'kh_payment_name' => $payment['payment_name'],
//                 //'kh_payment_code' => $payment['payment_code'],
//                 //'kh_out_trade_sn' => '',
//                 //'kh_pay_time'     => '',
//                 //'kh_pay_message'  => '',
        
//                 //'ship_name'    => '',
//                 //'ship_area'    => '',
//                 //'ship_addr'    => '',
//                 //'ship_zip'     => '',
//                 //'ship_tel'     => '',
//                 //'ship_mobile'  => '',
//                 //'ship_email'   => '',
        
//                 //'kh_addr'      => '',
//                 //'kh_name'      => '',
//                 //'kh_sex'       => '',
//                 //'kh_mobile'    => '',
//                 //'ship_time'    => '',
//                 'last_modified'=> $time,
//                 //'memo'         => '',
//                 'source_from'  => 'app',
//         );
//         $transaction = $mOrder->beginTransaction();
        
//         $id = $mOrderJ->add($data);
//         $oid = $mOrder->add($odata);
//         if (!$id || !$oid){
//             $mOrder->rollback();
//             $this->show_warning('操作失败');
//             return;
//         }
//         $mOrder->commit($transaction);
        
//         $this->show_message('操作成功');
//         return;
//     }
//     public function ajax_user(){
//         $mM = &m('member');
//         $ms = $mM->get($_POST['u_id']);
//         echo $ms['user_name'];
//     }
//     public function ajax_jp(){
//         if($_POST['u_id']){
//             $this->assign('uid',$_POST['u_id']);
//             $this->display('order.jpjz.ajax.html');
//         	//echo '{input_obj post="" type="radio" filter="uid" arg0="" callback="jCallBack" name="jp_id" class="zuopin" value="" id="zuopin" model="userphoto" text="请选择作品"}';
//         }else{
//             echo '请选择作品';
//         }
//     }
//     public function ajax_hasjp(){
//         $mOrderJ = &m('order_jpjz');
//         $data = $mOrderJ->get($_POST['id']);
//         if($data){
//             //$this->json_error('已生成过订单',$data['order_sn']);
//             echo $data['order_sn'];
//             die();
//         }
//     }
//     function _gen_order_sn()
//     {
//         mt_srand((double) microtime() * 1000000);
//         $timestamp = gmtime();
//         $y = date('y', $timestamp);
//         $z = date('z', $timestamp);
//         $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    
//         $orders = $this->_mod_order->find("order_sn='{$order_sn}'");
//         if (empty($orders))
//         {
//             return $order_sn;
//         }
//         return $this->_gen_order_sn();
//     }
    
    //========================== 即拍即做订单 End =======================//
    /*原来手动推送物流，现在直接从rcmtm获取物流单号，好像没用了，前台页面入口还未屏蔽*/
    function opts(){
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $order_mod =& m('order');
       
        if(empty($order_id)){
            $this->show_warning("参数错误！");
            return false;
        }
        $order_info=$order_mod->get($order_id);
        if(isset($_POST['ship'])){
            $kuaidi_type = isset($_POST["kuaidi"]) ? trim($_POST['kuaidi']) : '';
            $express = isset($_POST["waybillno"]) ? trim($_POST['waybillno']) : '';
            if(empty($express)){
                $this->show_warning("请填写快递单号");
                return false;
            }
            $data = array(
                    'status'         => ORDER_SHIPPED,
                    'ship_time'      => gmtime(),
                    'waybillno'        => $express,
                    'deliver_name'   => $_SESSION['admin_info']['user_name'],
                    'shipping_id'    =>$kuaidi_type,
            );
        }
        
        if(empty($data)){
            $this->show_warning("参数错误！");
            return false;
        }
        
        $res = $order_mod->edit($order_id, $data,0);
        if($res){


     
            imports("orderLogs.lib");
            $oLogs = new OrderLogs();
            $oLogs->_record(array(
                    'order_id' => $order_id,
                    'op_id'    => $_SESSION['admin_info']['user_id'],
                    'op_name'  => $_SESSION['admin_info']['user_name'],
                    'behavior' => 'delivery',
                    'remark'   => "后台管理员操作去发货按钮",
            ));
            imports("operationlog.lib");
            $operation=new OperationLog();
            $type='edit';
            $op_name=$_SESSION['admin_info']['user_name'];
            $order_sn=$order_info['order_sn'];
			$from=LANG::get(ORDER_STATUS)["{$order_info['status']}"];
			$to=LANG::get(ORDER_STATUS)[ORDER_SHIPPED];
            $remark=addslashes("操作人员".$op_name."将订单号{$order_sn}的订单状态从[{$from}]更新到[{$to}],快递单号为{$express}<a href='index.php?app=order&amp;act=updateOrder&id={$order_id}'>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>详情&gt;&gt;</b></font></a>");
            $operation->operation_log('',$type,$remark);
            
            //=====  发货  =====
            fahuo($order_id);
            
            $this->show_message("操作成功！");
            return;
        }else{
            $this->show_warning("操作失败，请重试!");
            return;
        }
    }
    
    function orderprint(){
         $order_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
         
         $model_ordergoods  =&m("ordergoods");
         header("Content-type:text/html;charset=utf-8");
         if(empty($order_ids)){
             $this->show_message("请选择要打印的订单号！");
             return false;
         }
         
         $model_order = &m("order");
         
         $model_ordersuit = &m("ordersuit");
         
         $orders = $model_order->find(array(
             "conditions" => "order_id IN ({$order_ids})",
         ));

         $self   = array();
        
         foreach((array) $orders as $key => $val){

            if($val["shipping_id"] == 2){
                $self[$val["ship_store"]] = $val["ship_store"];
            }
         }

         $goods = $model_ordergoods->find(array(
            "conditions" => "order_id IN ({$order_ids})",
         ));

         $suit = array();
         foreach($goods as $key => $val){
             if($val['suit_id']){
                 $suit[] = $val["suit_id"];
             }else{
                 if(isset($orders[$val['order_id']])){
                     $orders[$val['order_id']]['goods'][] = $val;
                 }
             }
         }
         
         $suits = $model_ordersuit->find(array(
             "conditions" => "order_id IN ({$order_ids}) AND goods_id ".db_create_in($suit),
         ));
         
         $sData = array();
         foreach($suits as $key =>$val){
             if(isset($orders[$val['order_id']])){
                 $orders[$val['order_id']]['goods'][] = $val;
             }
         }
//          print_r($suit);
//          die();
//         $this->assign('invoice', array(
//                 'com' => '单位名称',
//                 'sn' => '识别码',
//                 'addr' => '注册地址',
//                 'tel' => '注册电话',
//                 'bank' => '开户银行',
//                 'bank_num' => '银行账户',
//         ));

        if(!empty($self)){
            $serve_mod = &m("serve");
            $serves = $store_info = $serve_mod->find(array(
                    "conditions" => "idserve ".db_create_in($self),
                ));
            foreach($orders as $key => $val){
                if(isset($serves[$val["ship_store"]])){
                    $orders[$key]['self'] = $serves[$val['ship_store']];
                }
            }            
        }

        $this->assign("orderArr", $orders);
        $this->assign("action_user", $_SESSION['admin_info']["user_name"]);
        $this->assign("print_time", gmtime());
        $this->display("order.print.html");
    }
    
    function opt(){
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        
        if(empty($order_id)){
            $this->show_warning("参数错误！");
            return false;
        }
        
        if(isset($_POST['ship'])){
            $express = isset($_POST["express"]) ? trim($_POST['express']) : '';
            if(empty($express)){
                $this->show_warning("请填写快递单号");
                return false;
            }
            
            $data = array(
                'status'         => BOOKORDER_SHIPPING,
                'ship_time'      => gmtime(),
                'express'        => $express,
            );
        }
        
        if(isset($_POST["returned"])){
            $data = array(
                'status' => BOOKORDER_RETURNED
            );
        }
        
        
        if(isset($_POST['cancel'])){
            $data = array(
                'status' => ORDER_CANCELED
            );
        }
        if(empty($data)){
            $this->show_warning("参数错误！");
            return false;
        }
        $order_mod =& m('order');
        $info = $order_mod->get_info($order_id);
        $res = $order_mod->edit($order_id, $data);
      //  $res = 1;
        if($res){
            if(isset($_POST["returned"])){
                $member_mod    = &m("member");
                $member_money  = &m("membermoney");
                $member = $member_mod->get_info($info['user_id']);
                $member_money->add(array(
                   "user_id" => $info["user_id"],
                   "money"   => $member['money']+$info['order_amount'],
                   "change_money" => $info['order_amount'],
                   "addtime"  => gmtime(),
                    "reason"   => "面料册订单退货",
                    "remark"   => "面料册订单退货",
                    "type"     => "2",
                    "operator" => $_SESSION['admin_info']["user_id"],
                ));
                
                $member_mod->edit($info["user_id"], array("money" => $member['money']+$info['order_amount']));
            }
            
            if($_POST["ship"]){
                get_client_finance($info["user_id"], $info["out_trade_sn"], 1, $info["order_amount"], array("面料册购买！-余额:{$info["money_amount"]} -麦富迪币：{$info["coin"]} -第三方:{$info["final_amount"]}"));
            }
            
            $this->show_message("操作成功！");
            return;
        }else{
            $this->show_warning("操作失败，请重试!");
            return;
        }
    }

    private function operation($order){
        if($order['extension'] != "fabricbook"){
            return '';
        }else{
            $opt = array();
            switch ($order['status']){ 
                case ORDER_PENDING: //未支付
                    $opt['cancel'] = "1"; //取消
                    break;
                case ORDER_ACCEPTED: //已支付
                    $opt["ship"] = 1;    //发货
                    break;
                case BOOKORDER_REFUND:
                    $opt['ship'] = 1;
                    break; 
                case BOOKORDER_RETURN:
                    $opt['returned'] = 1;
                    break;
            }
            
            return $opt;
        }
    }
    
    //临时方法  梳理订单表数据
    function fmt_goods_image(){
        $safe     = isset($_GET['safe']) ? trim($_GET['safe']) : '';
        if(!$safe || $safe != 'ruesin') die();
        $mOg = &m('ordergoods');
        $mCst = &m('custom');
        $cst = $mCst->find();
        $goods = $mOg->find("goods_id <> 0");
        
        foreach ($goods as $row){
            if($cst[$row['goods_id']] && $cst[$row['goods_id']]['small_img']){
                $res = $mOg->edit("rec_id = '{$row['rec_id']}'",array('goods_image'=>$cst[$row['goods_id']]['small_img']));
                if($res>=0){
                    echo $row['rec_id'].'<br>';
                }
            }
        }
    }
}

