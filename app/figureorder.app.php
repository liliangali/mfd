<?php


class FigureorderApp extends MemberbaseApp
{
	var $_figure,$user_id;
    function __construct()
    {
    	parent::__construct();
        $this->_figure=m('figure');
        
        /* 当前用户中心菜单 */
        $this->_curitem('figureorder');
    	$this->user_id = $this->visitor->get('user_id');
    	$this->idserve = $this->visitor->get('idserve');
    	//var_dump($this->idserve);exit;
    	$this->assign('title', 'RCTAILOR-酷客中心-服务点的订单');
    }

    function index()
    {
    	
    	$this->_get_orders();
    	
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        $this->display('figureorder.index.html');
    }
	
    
	
    
	/**
     *    获取订单列表
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_orders()
    {
        $page = $this->_get_page(10);
        $model_order =& m('order');
        !$_GET['type'] && $_GET['type'] = 'all_orders';
        $con = array(
            array(      //按订单状态搜索
                'field' => 'status',
                'name'  => 'type',
                'handler' => 'order_status_translator',
            ),
            array(      //按店铺名称搜索
                'field' => 'seller_name',
                'equal' => 'LIKE',
            ),
            array(      //按下单时间搜索,起始时间
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),
            array(      //按下单时间搜索,结束时间
                'field' => 'add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'=> 'gmstr2time_end',
            ),
            array(      //按订单号
                'field' => 'order_sn',
            ),
        );
        $conditions = $this->_get_query_conditions($con);
        /* 查找订单 */
        $orders = $model_order->findAll(array(
            //'conditions'    => "buyer_id=" . $this->visitor->get('user_id') . "{$conditions}",
            'conditions'    => " order_figure.serviceid=$this->idserve " . "{$conditions}",
            'fields'        => 'this.*,order_figure.*',
            'join'=>'has_orderfigure',
            //'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',

        ));
        
        foreach ($orders as $key1 => $order)
        {
        	
        	/*
            foreach ($order['order_goods'] as $key2 => $goods)
            {
                empty($goods['goods_image']) && $orders[$key1]['order_goods'][$key2]['goods_image'] = Conf::get('default_goods_image');
            }//figure
            */
        	/*
            $figure_mod=m('figure');
            $res=$figure_mod->uniquebyid($orders[$key1]['buyer_id']);
            $orders[$key1]['isfigure']=$res;
            //var_dump($orders[$key1]['isfigure']);
            //var_dump($res);false不需要补,true需要补录量体数据
            */
        	
            if($orders[$key1]['figure']=='-1'||$orders[$key1]['figure']=='-2')
            {
            	 $orders[$key1]['isfigure']=true;
            }else 
            {
            	$orders[$key1]['isfigure']=false;
            }
            
			//var_dump($orders[$key1]['figure']);
        	//exit;
        }

        $page['item_count'] = $model_order->getCount();
        $this->assign('types', array('all'     => Lang::get('all_orders'),
                                     'pending' => Lang::get('pending_orders'),
                                     'submitted' => Lang::get('submitted_orders'),
                                     'accepted' => Lang::get('accepted_orders'),
                                     'shipped' => Lang::get('shipped_orders'),
                                     'finished' => Lang::get('finished_orders'),
                                     'canceled' => Lang::get('canceled_orders')));
        $this->assign('type', $_GET['type']);
        $this->assign('orders', $orders);
        $this->_format_page($page);
        $this->assign('page_info', $page);
    }
    
}

?>
