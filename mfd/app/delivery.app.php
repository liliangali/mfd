<?php
/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class DeliveryApp extends BackendApp
{
     public static $search_options = array(            
                'delivery_id'   => '物流单号',
                'tid'   => '订单号',
                'receiver_name'   => '收货人姓名',
                'receiver_mobile'   => '收货人手机',

        );
    function __construct(){
    	$this->DeliveryApp();
    }
    function DeliveryApp(){
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
        $conditions = $this->_delivery_conditions($_GET);

        
        $model_delivery =& m('delivery');
       
        $page   =   $this->_get_page(30);    //获取分页信息
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['delivery']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $delivery = strtolower(trim($_GET['delivery']));
            if (!in_array($delivery,array('asc','desc')))
            {
             $sort  = 't_begin';
             $delivery = 'desc';
            }
        }else{
            $sort  = 't_begin';
            $delivery = 'desc';
        }
//         echo $conditions;
        $deliverys = $model_delivery->find(array(
            'conditions'    => " 1 = 1 " . $conditions,
            'limit'         => $page['limit'],  //获取当前页的数据
            'delivery'         => "$sort $delivery",
            'count'         => true             //允许统计
        ));


        $dq_time = time();
        $page['item_count'] = $model_delivery->getCount();   //获取统计的数据
        $this->_format_page($page);
        
        $this->assign('conditions',$conditions);
        $this->assign('query',$_GET);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('search_options', self::$search_options);
        $this->assign('page_info', $page); 
        $this->assign('dqtime', $dq_time);
        $this->assign('deliverys', $deliverys);
        $_SESSION['admin_info']['delivery_reurl'] = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER["SERVER_PORT"] == '80' ? '' : (':'.$_SERVER["SERVER_PORT"])).$_SERVER["REQUEST_URI"];
        
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('delivery.index.html');
    }
    
    function _delivery_conditions($get = array()){
        array_key_exists($get['field'], self::$search_options) && $field = $get['field'];
        /* rcmtm订单号 查询用like 如果效率慢可以考虑 find_in_set  *///1111
        $conditions = $this->_get_query_conditions(array(array(
            'field' => $field,       //客户姓名，客户手机，收货人姓名，收货人手机进行搜索
            'equal' => '=',
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
        return $conditions;
    }
    /**
     * 发货单打印
     * @return boolean
     */
    
        function deliveryprint(){
            header("Content-type:text/html;charset=utf-8");
            $res = $this->_getData();
            if (isset($res['err']) && !empty($res['err'])){
                $this->show_warning($res['msg']);
                return ;
            }
            
//             var_dump(__FILE__);
//             print_exit($res);
        
            $this->assign("orderArr", $res['res']);
            $this->assign("action_user", $_SESSION['admin_info']["user_name"]);
            $this->assign("print_time", gmtime());
            $this->display("delivery.print.html");
        }
    
        /**
         * 根据 发货单 ids 获取详细信息
         * @return multitype:number string |multitype:number unknown
         */
         function _getData(){
             $_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
              
             $model_ordergoods  =&m("ordergoods");
             
             if(empty($_ids)){
                 return array('err'=>1,'msg'=>'请选择要打印的订单号！');
             }
             
             $model_delivery =& m('delivery');
              
             $model_order = &m("order");
              
             $model_ordersuit = &m("ordersuit");
              
             $deliverys = $model_delivery->find(array(
                 "conditions" => "delivery_id IN ({$_ids})",
             ));
             
             $order_sns = i_array_column($deliverys, 'tid');
             
             
             $orders = $model_order->find(array(
                 "conditions" => "order_sn IN (".implode(',', $order_sns).")",
                 "index_key" => 'order_id'
             ));
             
             $order_ids = i_array_column($orders, 'order_id');
             
             $goods = $model_ordergoods->find(array(
                 "conditions" => "order_id IN (".implode(',', $order_ids).")",
             ));
             
             //加载diy类
             imports("diys.lib");
             $this->_Diys = new Diys();
             
             foreach($goods as $key => $val){
                 if (isset($val['params'])){
                     $item = '';
                     $item = json_decode($val['params'],true);
                     if ('fdiy' == $val['type'])
                     {
                         $val['pstr'] =  $this->_Diys->_format_params($item);
                         $val['goods_name'] =  $this->_Diys->get_cname($val['cloth'],'/','fdiy_management');
                     }else {
                     
                         $val['pstr'] =  $item['oProducts']['spec_info'];
                     }
                 }
            
                 if(isset($orders[$val['order_id']])){
                     $orders[$val['order_id']]['goods'][] = $val;
                 }
             }
              
             $sData = array();
             foreach($deliverys as $key =>$val){
                 foreach ($orders as $v){
                     if($val['tid'] == $v['order_sn']){
                         $deliverys[$key]['oinfo'] = $v;
                     }
                 }
                  
             }
             
            return array('err'=>0,'res'=>$deliverys);
         }
    /**
     * 发货处理
     */
    function view()
    {
        $deliveryId = $_GET['id'];
        if (!$deliveryId)
        {
            $this->show_warning('发货单信息有误!');
            return ;
        }
        $delivery_mod = m('delivery');
        $delivery_info = $delivery_mod->get($deliveryId);

        $this->assign('order_info',$delivery_info);
        $this->display('order/order_deliver.info.html');
   
    }


    


    /**
     * 导出订单
     * @author Ruesin
     */
    function export(){
        $request = $this->_getData();
        if (isset($request['err']) && !empty($request['err'])){
            $this->show_warning($request['msg']);
            return ;
        }

        $deliverys = $request['res'];
        
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
            ORDER_ABNORMAL   => '订单异常', //(订单推送)
            ORDER_SHIPERROR  => '物流异常',
        );
        
        foreach ($deliverys as $row){
            
            $delivery[$row['delivery_id']] = array(
                    'add_time'    => date('Y/m/d H:i',$row['oinfo']['add_time']), //下单日期
                    't_begin'    => date('Y/m/d H:i',$row['oinfo']['add_time']), //发货日期
                    'tid'    => $row['tid'],             //订单号
                    'delivery_id'    => $row['delivery_id'],             //发货单号
                    'status'      => $status[$row['oinfo']['status']],      //App状态
                    'ship_name'   => $row['receiver_name'],    //收货人姓名
                    'ship_addr'   => $row['area_names'] .$row['receiver_address'], //收货人地址
                    'user_name'   => $row['oinfo']['user_name'],   //会员名
                    'delivery_amount' => ($row['oinfo']['order_amount']),  //订单金额
                    'final_amount' => ($row['oinfo']['final_amount']),  //在线支付额
                    'payment_name' => $row['oinfo']['payment_name'],  //支付方式
                    'post_fee' => $row['post_fee'],  //运费
                    'source'      => strtoupper($row['oinfo']['source_from']),  //订单来源
            );
            
        }
        
        $res = $delivery;
        
        $fields_name = array('下单日期','发货日期','订单号','发货单号','订单状态','收货人姓名','收货人地址','会员名','订单金额','在线支付额','支付方式','运费','订单来源');
        
        array_unshift($res,$fields_name);
        $this->export_to_csv($res, '发货单-'.date('YmdHis',time()), 'gbk');
    }

    
    
    
    
    
    
}

