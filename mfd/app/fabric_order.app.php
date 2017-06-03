<?php
/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class Fabric_orderApp extends BackendApp
{
	

    public static $search_options = array(            
                'order_sn'   => '订单号',
                'r_order_id'   => 'RCMTM订单号',
                'ship_name'   => '收货人姓名',
                'ship_mobile'   => '收货人手机',
                'user_name'    => '用户名',
        );
    public static $body_options = array('量体筛选','has_measure_0'=>'标准号','has_measure_1'=>'量体定制');
    /**
     *    管理
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function index()
    {
   
    	
        $conditions = $this->_order_conditions($_GET);
        
        $model_order =& m('order');
        $figure_orderm = & m('figureorderm');
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
            'conditions'    => " 1 = 1 AND extension = 'fabricbook' " . $conditions,
            'limit'         => $page['limit'],  //获取当前页的数据
            'order'         => "$sort $order",
            'count'         => true             //允许统计
        ));
        foreach ($orders as &$row){
           if($row['r_order_id']){
               $row['mtm_ids'] = array_filter(explode(',', $row['r_order_id']));
           }
        }
        foreach ($orders as $key=>$value){
        	if($value['order_id']){
        		$figureinfo = $figure_orderm->get("order_id='{$value['order_id']}'");
        		if($figureinfo){
        		
        			$orders[$key]['y_time'] = strtotime($figureinfo['time']);
        		}
        		
        	}
        
        }
        $dq_time = time();
        $page['item_count'] = $model_order->getCount();   //获取统计的数据
        $this->_format_page($page);
        
        $this->assign('conditions',$conditions);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('search_options', self::$search_options);
        $this->assign('body_options', self::$body_options);
        $this->assign('page_info', $page); 
        $this->assign('dqtime', $dq_time);
        $this->assign('orders', $orders);
        
        $_SESSION['admin_info']['order_reurl'] = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER["SERVER_PORT"] == '80' ? '' : (':'.$_SERVER["SERVER_PORT"])).$_SERVER["REQUEST_URI"];
        
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display("fabric_order.index.html");
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
//      dump($arr_bak);
            foreach ($arr as $key => $value) 
            { 
                $value = urldecode($value);
                $str .= $ship_fields[$key].":".$value.",";
            }
//       dump($str);      
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
//         dump($old_new);            
                   $old_new_str = "<font style='color:red'>老数据</font>:".$this->formatAddress($old_new['old'])."<br>"."<font style='color:red'>新数据</font>:".$this->formatAddress($old_new['new']);
//                dump($old_new_str);    
                    $address_list[$key]['old_new_str']= $old_new_str;
                }
            }
            $this->assign('address_list',$address_list);
            
            $get_ship_id = $_GET['ship_id'];
            $order_info = $order_mod->get_info($id);
            $ship_id = $order_info['shipping_id'];
            if ($order_info['shipping_id'] == 1) 
            {
                $ship_area = $order_info['ship_area'];
            }
         
            if ($get_ship_id) 
            {
                $ship_id = $get_ship_id;
            }
           $this->assign('shipping_id',$ship_id);
            
            $list1 = $region_mod->get_options(2);
            $this->assign('region1',$list1);
            
            $region_id = $serve['region_id'];
            if ($region_id)
            {
                $region_info = $region_mod->get($region_id);
                $this->assign('p_region_id',$region_info['parent_id']);
                $list2 = $region_mod->get_options($region_info['parent_id']);
                $this->assign('region2',$list2);
            }
            
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
                'style'  => 'res:style/jqtreetable.css'
            ));
            
            $this->assign('order_info',$order_info);
            $this->display('order/order_address'.$ship_id.'.form.html');
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
            
            if (!in_array($order_info['status'], array(ORDER_PENDING,ORDER_WAITFIGURE,ORDER_ACCEPTED,ORDER_PRODUCTION,ORDER_STOCKING)))
            {
                $this->show_warning('订单已推送至RCM或已发货 无法修改收货地址 ');
                return ;
            }
            
            if ($order_info['shipping_id'] == 1) //=====  快递送货  =====
            {
                $old_data['ship_name'] = urlencode($order_info['ship_name']);
                $old_data['ship_tel']  = urlencode($order_info['ship_tel']);
                
                $old_data['ship_area']  = urlencode(str_replace('	', ",", $order_info['ship_area']));
//       var_dump($order_info['ship_area']);echo '<br>';echo $a;exit;          
                
                $old_data['ship_addr'] = urlencode($order_info['ship_addr']);
            }
            else 
            {
                $old_data['shipping_id'] = $order_info['shipping_id'];
                $old_data['ship_store'] = $order_info['ship_store'];
                $old_data['ship_name'] = urlencode($order_info['ship_name']);
                $old_data['ship_tel'] = urlencode($order_info['ship_tel']);
            }
            
            if ($shipping_id == 1) //=====  快递送货  =====
            {
                $p_region_name = $region_mod->getRegionName($_POST['p_region_id']);
                $s_region_name = $region_mod->getRegionName($_POST['region_id']);
                $data['ship_name']  = $_POST['ship_name'];
                $data['ship_tel']  = $_POST['ship_tel'];
                $data['ship_area'] = "中国	$p_region_name	$s_region_name";
                $data['ship_addr'] = $_POST['ship_addr'];
                $new_data = $data;
                $new_data['ship_mobile'] = $data['ship_tel'];
                $new_data['shipping'] = '快递服务';
            }
            else
            {
                if (!$_POST['ship_store']) 
                {
                    $this->show_warning('请选择门店');
                    return ;
                }
                $data['shipping_id'] = $shipping_id;
                $data['ship_store'] = $_POST['ship_store'];
                $data['ship_name'] = urlencode($_POST['ship_name']);
                $data['ship_tel'] = $_POST['ship_tel'];
                $new_data['ship_store'] = $_POST['ship_store'];
                $new_data['ship_name'] = $_POST['ship_name'];
                $new_data['ship_tel']  = $_POST['ship_tel'];
                $new_data['ship_mobile'] = $data['ship_tel'];
                $new_data['shipping'] = '门店自提';
            }
            $new_data['shipping_id'] = $shipping_id;
//         dump($new_data);
            $order_mod->edit($id,$new_data);
            
//    dump($old_data)  ;       
            $fields['old'] = $old_data;
            if ($shipping_id == 1) 
            {
                $data['ship_name']  = urlencode($_POST['ship_name']);
                $data['shipping_id']  = urlencode($shipping_id);
                $data['ship_tel']  = urlencode($_POST['ship_tel']);
                $data['ship_addr'] = urlencode($_POST['ship_addr']);
                $data['ship_area'] = urlencode("中国,$p_region_name,$s_region_name");
            }
//         dump($data);    
            $fields['new'] = $data;
            $data_log['old_new'] = json_encode($fields);
            $data_log['add_time'] = time();
            $data_log['admin_id'] = $this->visitor->info['user_id'];
            $data_log['order_id'] = $id;
            $order_address_mod->add($data_log);
            $this->show_message('收货信息修改成功');
//           dump($_POST);  
       
            
            
        }
        
    }
    
    /**
    *获得服务店
    *@author liang.li <1184820705@qq.com>
    *@2015年7月22日
    */
    function get_serve() 
    {
        $mod = m('serve');
        $pid = $_POST['pid'];
        $list = $mod->find("region_id = '{$pid}'");
        if ($list) 
        {
            foreach ($list as $key => $value) 
            {
                $str .= '<tr class=serve >
                <th class="paddingT15"> '.$value['serve_name'].':</th>
                <td class="paddingT15 wordSpacing5">
                <input class="infoTableInput2" id="ship_name" type="radio" name="ship_store" value="'.$key.'" />
                </td>
              </tr>';
            }
        }
        
        $this->json_result($str);
        
    }
    
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
        /* rcmtm订单号 查询用like 如果效率慢可以考虑 find_in_set  */
        $conditions = $this->_get_query_conditions(array(array(
                'field' => $field,       //客户姓名，客户手机，收货人姓名，收货人手机进行搜索
                'equal' => 'LIKE',
                'name'  => 'search_name',
        ),
                array(
                        'field' => 'status',
                        'equal' => '=',
                        'type'  => 'numeric',
                ),array(
                        'field' => 'add_time',
                        'name'  => 'add_time_from',
                        'equal' => '>=',
                        'handler'=> 'gmstr2time',
                ),array(
                        'field' => 'add_time',
                        'name'  => 'add_time_to',
                        'equal' => '<=',
                        'handler'   => 'gmstr2time_end',
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
            $this->show_warning('你不是管理员，不能进行操作！');
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

        $sql = "update `cf_order` set final_amount = $amount where order_id = $id limit 1 ";
        $db = &db();
        $rs = $db->query($sql);

        imports("orderLogs.lib");
        $oLogs = new OrderLogs();
        $oLogs->_record(array(
                'order_id' => $id,
                'op_id'    => $_SESSION['admin_info']['user_id'],
                'op_name'  => $_SESSION['admin_info']['user_name'],
                'behavior' => 'quickPrice',
        ));
        
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
    	        'conditions' => "order_id = $orderId AND behavior='update' ",
    	    ));
    	    $a ;
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
    		$status = intval($_POST['status']);
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
				//发货，更新发货时间
				if ($status == ORDER_SHIPPED)
    		    {
					$arr['ship_time'] = gmtime();
					$behavior = 'delivery';
				}
				
				//管理员编辑退款中状态
				if ($status == ORDER_TUIKUANZHONG)
    		    {	
					//已付款、生产中、已发货 这几个状态下可以操作退款
					$is_status = array(ORDER_SHIPPED, ORDER_ACCEPTED, ORDER_PRODUCTION);
					if(!in_array($order_info['status'], $is_status)) {
						$this->show_message('当前订单状态不可修改为退款中！',
    					'back_list', 'index.php?app=order&page=' . $cur_page);
						return;
					}
					$user = $this->visitor->get();
					$order_refund_mod = m('orderrefund');
					$member_mod = m('member');
					$userInfo = $member_mod->get($order_info['user_id']);
					$data = array(
						'order_id'   => $order_info['order_id'],
						'action_id'  => $user['user_name'],
						'user_id'    => $order_info['user_id'],
						'user_name'  => $userInfo['user_name'],
						'order_status' => $order_info['status'],
						'add_time'   => gmtime(),
					);
					$orderrefund_info = $order_refund_mod->get("order_id=".$order_info['order_id']);
					if(!$orderrefund_info) {
						$order_refund = $order_refund_mod->add($data);
					} else {
						$order_refund = $order_refund_mod->edit($orderrefund_info['id'], $data);
					}
					
					if($order_refund) {
						$mes = 'APP端退款按钮已生成，请提示用户操作！';
						//生成退款按钮后给APP端发系统通知
						$dz = $member_mod->get($order_info['user_id']);
						include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
						$push = new XingeMeg();
						$push->toMasterXinApp($dz['user_token'], '【Mfd】退款通知', '系统已为您生成退款按钮，请点击提交退款原因。', array('url_type'=>'system', 'location_id'=>''));
					}
										
				}
				
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
				$arr['status'] = $status;
    			$order_mod->edit($order_id,$arr);
    			
    			
    			
//     			$user_info  = $this->visitor->get();
//     			$arr = array(
//     					'order_id' => $order_id,
//     					'operator' => $user_info['user_name'],
//     					'order_status' => $status_list[$old_status],
//     					'changed_status' => $status_list[$status],
//     					'remark' => $remark,
//     					'log_time' => time(),
//     					);
//     			$order_log = m('orderlog');
//     			$order_log->add($arr);
    			$this->show_message($mes,
    					'back_list', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order&page=' . $cur_page
    					);
    		}
    		else
    		{
    			$this->show_message('本次操作没有修改订单状态',
    					'back_list', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order&page=' . $cur_page);
    		}
    	}
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
    			'70' => '订单退款中',
    			'80' => '订单已退款',
    			);
    }
    
    function views(){
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $model_order =& m('order');
        $order_info = $model_order->get($order_id);
        
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

        /* 获取订单信息 */
        $model_order =& m('order');
        $order_info = $model_order->get($order_id);

        if (!$order_info){
            $this->show_warning('no_such_order');
            return;
        }
        //=====  商品相关  =====
        $order_goods_mod = m('ordergoods');
        $order_goods_list = $order_goods_mod->find(array(
            'conditions' => "order_id = $order_id",
            'index_key'  => "",
        ));
        //=====  工艺相关  =====
        $dict_mod = m('dict');
        $dict_mod1 = m('dict1');
        $i=0;
       // print_exit($order_goods_list);
        foreach($order_goods_list as $key=>$value){
            $params[$key] = json_decode($value['params'],true);
        }
        if ($params)
        {
            $tmp = array();
            foreach ((array)$params as $key1 => $value1)
            {
				$value1 = @array_filter($value1);//去除工艺中的空值
                foreach((array)$value1 as $key2 => $value2){
                    $value1_val = explode("|", $value2);
                    if ($value['type'] == 'diy')
                    {
                        $dict2_info = $dict_mod->get("id=".$key2);
                        $a[$key1][$i]['p_name'] =$dict2_info['name'];
                        $dict1_info = $dict_mod->get("id=".$value1_val[0]);
                        if (count($value1_val) > 1)
                        {
                            $a[$key1][$i]['s_name'] = $dict1_info['name'] .$value1_val[2];
                        }
                        else
                        {
                            $a[$key1][$i]['s_name'] = $dict1_info['name'];
                        }
                    }
                    else
                    {
                        $dict2_info = $dict_mod1->get("id=".$key2);;
                        $a[$key1][$i]['p_name'] = $dict2_info['NAME'];
                        $dict1_info = $dict_mod1->get("id=".$value1_val[0]);
                        if (count($value1_val) > 1)
                        {
                            $a[$key1][$i]['s_name'] = $dict1_info['NAME'].$value1_val[2];
                        }
                        else
                        {
                            $a[$key1][$i]['s_name'] = $dict1_info['NAME'];
                        }
                    }
                    $i++;
                }
                $order_goods_list[$key1]['params_value'] = $a[$key1];
            }
        }
//print_exit($order_goods_list);
        $order['data']['figure'] = $this->_get_order_figure($order_id);
    
        $order['data']['goods']  = $this->_get_order_goods($order_id);
        $n = 0;
        foreach($order['data']['goods'] as $key=>$value){
            $order['data']['goods'][$key]['params_value'] = $order_goods_list[$n]['params_value'];
            $n++;
        }
        // 添加选择量体派工的订单的 量体数据进度查询   auth tangshoujian
        $figure_progress= $this->_get_order_figure_progress($order_id);
        $mCart =& m('cart');
        $this->assign('embs',$mCart->_format_embs());
        
        
        $cloudData = file_get_contents('http://api.figure.mfd.cn/soap/figure.php?act=getFields');
        $jFlag = 0;
        if(isset($cloudData)){
            $fData = json_decode($cloudData,1);
            if($fData && isset($fData['result']['data'])){
                $jFlag = 1;
            }
        }
        if($jFlag){
            $this->assign('figure',$fData['result']['data']);
        }else{
            $mMf = &m('member_figure');
            $this->assign('figure',$mMf->_positions());
        }
        
        $this->assign("operation" , $this->operation($order_info));
        
        $mODebit = &m('orderdebit');
        $debits = $mODebit->find("order_id = '{$order_id}'");
        foreach ($debits as $row){
            $order_info['debit_amount'] += $row['d_money'];
        }
        
        if($order_info["shipping_id"] == 2){
            $serve_mod = &m("serve");
            $store_info = $serve_mod->get("idserve='{$order_info["ship_store"]}'");
            
            $this->assign("store_info", $store_info);
        }
		
		//收货延期
		$mOsd = m('ordershipdelay');
		$has_delay  = $mOsd->get(array(
			'conditions' => "order_id=$order_id",
			'order'      => 'delay_time desc',
			'count'      => true,
			'index_key'	 => '',
		));
//   print_exit($order['data']);
        $mSet = &af('settings');
        $ck = $mSet->getOne('order_if_review');
        $this->assign('has_check',$ck);
		$this->assign('has_delay',$has_delay);
        $this->assign('figure_progress', $figure_progress);
        $this->assign('order', $order_info);
        //var_dump($order['data']);exit;
        $this->assign('data',$order['data']);
        
        $this->assign('reUrl',$_SESSION['admin_info']['order_reurl']); //$_SERVER['HTTP_REFERER']
        
        $this->display('order.view.html');
    }

    function _get_order_figure($order_id){
        $figure_mod = m('orderfigure');
        $figure_info = $figure_mod->get('order_id = '.$order_id);
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
        $goods = $mOg->find("order_id = '{$order_id}'");
        foreach($goods as &$row){
            $row['embs'] = json_decode($row['embs'],1);
            $row['params'] = json_decode($row['params'],1);
            if($women[$row['cloth']]){
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
    
    
    function _get_order_figure_progress($order_id){
    	
        $order_mod = &m('order');
    	$fOm = &m('figureorderm');
    	$serve_mod = &m('serve');
    	$liangti_mod = &m('figure_liangti');
    	$member_mod = &m('member');
        $lt_state  = array(0=>'还未指派',1=>'已经指派',2=>'量体结束',3=>'订单完成');
        $order_info = $order_mod->get("order_id = '$order_id' ");

        if(!empty($order_info)){
            $figure_info = $fOm->get('order_id ='.$order_id);
 
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
        $data['serve_name']    = isset($mendian['serve_name']) ? $mendian['serve_name'] : '还未指派店铺' ;
        $data['s_card']        = isset($mendian['card_number']) ?  $mendian['card_number'] : '无';
        $data['linkman']    = isset($mendian['linkman']) ? $mendian['linkman'] : '' ;
        $data['real_name']    = isset($lts_info['real_name']) ? $lts_info['real_name'] : '还未指派量体师' ;
        $data['l_card']        = isset($lts['card_number']) ?  $lts['card_number'] : '无';
        $data['assign_time']    = $figure_info['assign_time'];
        $data['phone_mob']    = isset($lts_info['phone_mob']) ? $lts_info['phone_mob'] : '' ;
        $data['time']    = isset($figure_info['time']) ? $figure_info['time'] : '' ;
        $data['time_noon']    = isset($figure_info['time_noon']) ? $figure_info['time_noon'] : '' ;
        $data['modi_time']    = $figure_info['modi_time'];
        if($figure_info['modi_time']){
        	
        $data['modi_time'] = date('Y-m-d h:i:s',$figure_info['modi_time']);
        }
        if($figure_info['assign_time']){
        
        $data['assign_time']    =  date('Y-m-d h:i:s',$figure_info['assign_time'] );
        
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
    		$res = $figure_mod->edit($figure['id'],$data);
    		
    		if(!$res)
    		{
    			$msg = $figure_mod->get_error();
    			$this->show_warning($msg);
    			return;
    		}
    		
    		$this->show_message("修改量体数据成功！", '返回订单列表', $_SESSION['admin_info']['order_reurl'] ? $_SESSION['admin_info']['order_reurl'] : 'index.php?app=order');
    	}
    }


    /**
     * 导出订单
     * @author Ruesin
     */
    function export(){
        $conditions = " 1 = 1 AND extension ='fabricbook' ";
        if($_GET['sinexporttype'] == 'all'){
            unset($_GET['sinexporttype']);
            $conditions .= $this->_order_conditions($_GET);
        }else{
            if(!$_GET['ids']){
                return ;
            }
            $conditions .= " AND order_id IN ({$_GET['ids']})";
        }
        $order_mod = m('order');
        $orders = $order_mod->findAll(array(
                'conditions' => $conditions,
        		'order'   =>"add_time DESC",
        	//'fields' => 'order_id,add_time,order_sn,r_order_id,user_name,ship_name,ship_mobile,ship_addr,source_from',
        ));
        foreach ($orders as $row){
            $ids[$row['order_id']] = $row['order_id'];
            if($row['shipping_id'] == '2'){
                $svIds[$row['ship_store']] = $row['ship_store'];
            }
        }
        $mSv = &m('serve');
        if ($svIds){
            $store = $mSv->find(db_create_in($svIds,'idserve'));
        }
        
        $mOrderFigure = &m('orderfigure');
        $of = $mOrderFigure->find(array("conditions"=>db_create_in($ids,'order_id'),'index_key'=>'order_id'));
        
        foreach ((array)$of as $k=>$v){
            if($v['server_id']){
                $fSvId[$v['server_id']] = $v['server_id'];
            }
        }
        if($fSvId){
            $serve = $mSv->find(db_create_in($fSvId,'idserve'));
        }
        
        $cloudData = file_get_contents('http://api.figure.mfd.cn/soap/figure.php?act=getFields');
        if(isset($cloudData)){
            $fData = json_decode($cloudData,1);
            if($fData && isset($fData['result']['data'])){
                $figureItem = $fData['result']['data'];
            }
        }
                
        $status = array(
                ORDER_PENDING    => '待付款',
                ORDER_WAITFIGURE => '待量体',
                ORDER_ACCEPTED   => '已支付',
                ORDER_PRODUCTION => '生产中',
                ORDER_STOCKING   => '备货中',
                ORDER_SHIPPED    => '已发货',
                ORDER_FINISHED   => '已完成',
                ORDER_REPAIR     => '返修中',
                ORDER_CANCELED   => '已取消',
                ORDER_ABNORMAL   => '订单异常',
        );
        $measure = array('1'=>'是','0'=>'否');
        $noon    = array('am'=>'上午','pm'=>'下午');
        
        foreach ($orders as $row){
        	$good_is  = $this->_get_order_goods($row['order_id']);
        	$goods_name='';
            if($good_is){
            	foreach($good_is as $kk=>$vv){
            		$goods_name .=$vv['goods_name']."|";
            	}
            }
            if($row['goods_amount']>0){
            	$paytype=$row['goods_amount'];
            }
           
            //基本信息
            $order[$row['order_id']] = array(
            		'order_sn'    => "'".$row['order_sn'],     //后台订单号
                    'add_time'    => date('Y-m-d',$row['add_time']),     //下单日期
                    'user_name'   => $row['user_name'],   //用户名
                    'ship_name'   => $row['ship_name'],    //客户姓名
                    'status'      => $status[$row['status']],      //订单状态
            		'paytype'      => $paytype?$paytype:0,      //零售总价
            		'money'       =>$row['order_amount'],//订单总价
            		'goods_name'      => $goods_name,      //商品名称
            );
            
           
        }
        //基本抬头
        $order_name = array('后台订单号','下单日期','会员名','收件人','订单状态','零售总价','订单金额','商品名称');
        
        array_unshift($order,$order_name);
        $this->export_to_csv($order, '面料册-'.date('YmdHis',time()), 'gbk');
    }

    //========================== 即拍即做订单 Bgn =======================//
    public function jpjz(){
        $this->_mod_order =& m('order');
        $search_options = array(
                'order_sn' => '订单号',
                'user_name' => '用户',
                'kf_name' => '客服'
        );
        $field = 'order_sn';
        array_key_exists($_GET['field'], $search_options) && $field = $_GET['field'];
        $conditions = $this->_get_query_conditions(array(array(
                'field' => $field,
                'equal' => 'LIKE',
                'name'  => 'search_name',
        ),array(
                'field' => 'status',
                'equal' => '=',
                'type'  => 'numeric',
        ),array(
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
        ),array(
                'field' => 'add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
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
        $page   =   $this->_get_page(30);
        if (isset($_GET['sort']) && isset($_GET['order'])){
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))){
                $sort  = 'add_time';
                $order = 'desc';
            }
        }else{
            $sort  = 'add_time';
            $order = 'desc';
        }
        
        $orders = $this->_mod_order->find(array(
                'conditions'    => " 1 = 1 AND extension = 'jp' " . $conditions,
                'limit'         => $page['limit'],
                'order'         => "$sort $order",
                'count'         => true
        ));
        
        $page['item_count'] = $this->_mod_order->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0);
        
        $this->assign('search_options', $search_options);
        $this->assign('page_info', $page);
        $this->assign('orders', $orders);
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('orderj/index.html');
    }
    
    public function add_jpjz(){
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js'));
        $this->assign('build_editor',$this->_build_editor(array('belogn'=>BELONG_GOODS,'area_id'=>'content')));
        $this->display('order.jpjz.form.html');
    }
    public function save_jpjz(){
        $mOrder  = $this->_mod_order =& m('order');
        $mOrderJ = &m('order_jpjz');
        $mMember = &m('member');
        $this->_mod_pay = &m("payment");
        
        $payment = $this->_mod_pay->get(array(
                'conditions' => "enabled=1 AND ismobile = 1",
                'order'      => "sort_order DESC"
        ));
        
        $user = $mMember->get($_POST['user_id']);
        
        $order_sn = $this->_gen_order_sn();
        
        $time = gmtime();
        $amount = $_POST['order_amount'];
        $data = array(
                'order_sn'     => $order_sn,
        	    'order_amount' => $_POST['order_amount'],
                'user_id'      => $_POST['user_id'],
                'jp_id'        => $_POST['jp_id'],
                'kf'           => $_POST['kf'],
                'add_time'     => $time,
                'content'      => $_POST['content'],
        );
        $odata = array(
                'order_sn'     => $order_sn,
                'discount'     => '0',
                'goods_amount' => $amount,
                'order_amount' => $amount,
                //'kh_order_amount' => $amount,
                //'kh_ship_pay'  => '',
                //'figure_type'  => '',
                //'cloth'        => '',
                //'fabric'       => '',
                //'craft'        => '',
                //'embs'         => '',
                //'source'       => '',
                //'source_id'    => '',
                //'store_id'     => $user['user_id'],
                //'store_name'   => $user['user_name'],
                'user_id'      => $user['user_id'],
                'user_name'    => $user['user_name'],
                'status'       => ORDER_PENDING,
                'add_time'     => $time,
                'payment_id'   => $payment['payment_id'],
                'payment_name' => $payment['payment_name'],
                'payment_code' => $payment['payment_code'],
                //'out_trade_sn' => '',
                //'pay_time'     => '',
                //'pay_message'  => '',
                //'kh_payment_id'   => $payment['payment_id'],
                //'kh_payment_name' => $payment['payment_name'],
                //'kh_payment_code' => $payment['payment_code'],
                //'kh_out_trade_sn' => '',
                //'kh_pay_time'     => '',
                //'kh_pay_message'  => '',
        
                //'ship_name'    => '',
                //'ship_area'    => '',
                //'ship_addr'    => '',
                //'ship_zip'     => '',
                //'ship_tel'     => '',
                //'ship_mobile'  => '',
                //'ship_email'   => '',
        
                //'kh_addr'      => '',
                //'kh_name'      => '',
                //'kh_sex'       => '',
                //'kh_mobile'    => '',
                //'ship_time'    => '',
                'last_modified'=> $time,
                //'memo'         => '',
                'source_from'  => 'app',
        );
        $transaction = $mOrder->beginTransaction();
        
        $id = $mOrderJ->add($data);
        $oid = $mOrder->add($odata);
        if (!$id || !$oid){
            $mOrder->rollback();
            $this->show_warning('操作失败');
            return;
        }
        $mOrder->commit($transaction);
        
        $this->show_message('操作成功');
        return;
    }
    public function ajax_user(){
        $mM = &m('member');
        $ms = $mM->get($_POST['u_id']);
        echo $ms['user_name'];
    }
    public function ajax_jp(){
        if($_POST['u_id']){
            $this->assign('uid',$_POST['u_id']);
            $this->display('order.jpjz.ajax.html');
        	//echo '{input_obj post="" type="radio" filter="uid" arg0="" callback="jCallBack" name="jp_id" class="zuopin" value="" id="zuopin" model="userphoto" text="请选择作品"}';
        }else{
            echo '请选择作品';
        }
    }
    public function ajax_hasjp(){
        $mOrderJ = &m('order_jpjz');
        $data = $mOrderJ->get($_POST['id']);
        if($data){
            //$this->json_error('已生成过订单',$data['order_sn']);
            echo $data['order_sn'];
            die();
        }
    }
    function _gen_order_sn()
    {
        mt_srand((double) microtime() * 1000000);
        $timestamp = gmtime();
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    
        $orders = $this->_mod_order->find("order_sn='{$order_sn}'");
        if (empty($orders))
        {
            return $order_sn;
        }
        return $this->_gen_order_sn();
    }
    
    //========================== 即拍即做订单 End =======================//
    
    function opts(){
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
                    'status'         => ORDER_SHIPPED,
                    'ship_time'      => gmtime(),
                    'express'        => $express,
                    'deliver_name'   => $_SESSION['admin_info']['user_name'],
            );
        }
    
        if(empty($data)){
            $this->show_warning("参数错误！");
            return false;
        }
        $order_mod =& m('order');
        $res = $order_mod->edit($order_id, $data);
        if($res){
            
            imports("orderLogs.lib");
            $oLogs = new OrderLogs();
            $oLogs->_record(array(
                    'order_id' => $order_id,
                    'op_id'    => $_SESSION['admin_info']['user_id'],
                    'op_name'  => $_SESSION['admin_info']['user_name'],
                    'behavior' => 'delivery',
            ));
            
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
         
        $orders = $model_ordergoods->find(array(
           "conditions" => "order_goods.order_id IN ({$order_ids})",
           'join'       => "belongs_to_order",
           'order'      => "order_goods.order_id DESC",
        ));
        
        $orderArr = array();
        $self     = array();
        foreach((array) $orders as $key => $val){
            if(!isset($orderArr[$val["order_id"]])){
                $orderArr[$val["order_id"]] = $val;
            }
          //  $val["r_order_id"] = ","."sdff";
            $val["r_order_id"] = str_replace(",", " ", $val["r_order_id"]);
            $orderArr[$val['order_id']]["children"][] = $val;
            
            if($val["shipping_id"] == 2){
                $self[$val["ship_store"]] = $val["ship_store"];
            }
        }

        if(!empty($self)){
            $serve_mod = &m("serve");
            $serves = $store_info = $serve_mod->find(array(
                    "conditions" => "idserve ".db_create_in($self),
                ));
            foreach($orderArr as $key => $val){
                if(isset($serves[$val["ship_store"]])){
                    $orderArr[$key]['self'] = $serves[$val['ship_store']];
                }
            }            
        }

        $this->assign("orderArr", $orderArr);
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
        $res = $order_mod->edit($order_id, $data);
      //  $res = 1;
        if($res){
            if(isset($_POST["returned"])){
                $member_mod    = &m("member");
                $member_money  = &m("membermoney");
                $info = $order_mod->get_info($order_id);
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

