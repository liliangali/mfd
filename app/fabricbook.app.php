<?php

/**
 * 面料册控制器
 *
 * @param NULL
 *
 * @access protected
 * @author xuganglong <781110641@qq.com>
 * @return void
 */
class FabricbookApp extends MemberbaseApp
{
    var $_fabricbook_mod;
    
    function __construct()
    {	
		parent::__construct();
		header("Content-Type:text/html;charset=" . CHARSET);
		Lang::load(lang_file('common'));
        $this->_fabricbook_mod  = &m('fabricbook');
        $this->_mod_bookGallery = &m("bookgallery");
    }
	
	/**
	 * 面料册列表
	 *
	 * @param int category 筛选面料册分类
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function index()
    {
        $query["category"] = !empty($_GET['category'])? intval($_GET['category']) : 0;
        $conditions        = " ";
  
        if (!empty($query["category"])) {
            $conditions .= " AND category='{$query["category"]}'";
        }
        
        $page    =   $this->_get_page(20);
        $fabrics =   $this->_fabricbook_mod->findAll(array(
            'conditions'  => 'is_sale=1 '.$conditions,
            'limit'       => $page['limit'],
            'order'       => 'id DESC', 
            'count'       => true,
			'index_key'	 => '',
        ));

        $page['item_count']=$this->_fabricbook_mod->getCount();
        $this->_format_page($page);
		
		$this->_config_seo('title', Conf::get('site_title').'料册管理');
        $this->assign('query', $query); 
        $this->assign('page_info', $page);
        $this->assign('fabrics', $fabrics);
        $this->assign('app',APP);
		$this->assign('i', 0);
        $this->assign("category", $this->_fabricbook_mod->getCategory());
        $this->display('fabricbook.index.html');
    }
	
	/**
	 * 面料册详情
	 *
	 * @param int id 面料册id
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function info()
    {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $this->show_warning('参数错误');
            return;
        }
		
		$find_data     = $this->_fabricbook_mod->get_info($id);
		if (empty($find_data)) {
			$this->show_warning('参数错误');

			return;
		}

		$this->assign('find_data', $find_data);
		$this->assign("category", $this->_fabricbook_mod->getCategory());
		$gallery_list = $this->_mod_bookGallery->find(array(
			'conditions' => "book_id = '{$id}'",
		));
		$gallery_count = count($gallery_list);
		$this->_config_seo('title', Conf::get('site_title').'料册详情');
		$this->assign("category", $this->_fabricbook_mod->getCategory());
		$this->assign("gallery_list", $gallery_list);
		$this->assign("gallery_count", $gallery_count);
		$this->display('fabricbook.info.html');
    }
	
	/**
	 * 面料申请记录
	 *
	 * @param NULL
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function applyRecord()
    {
        $model_order = & m('order');
		$mOg = &m('ordergoods');
        $page   =   $this->_get_page(20);    //获取分页信息
     
        $orders = $model_order->find(array(
            'conditions'    => " 1 = 1 AND extension = 'fabricbook' AND user_id = '{$this->visitor->get("user_id")}'" ,
			'fields'        => "order_id, order_sn, status",
            'limit'         => $page['limit'],  //获取当前页的数据
            'order'         => "add_time DESC",
            'count'         => true             //允许统计
        ));

        foreach ($orders as &$row){//面料申请都是单商品
			$goods = $mOg->get("order_id = '{$row['order_id']}'");
			$row['goods']  = $goods;
			$row["actions"] = $this->actions($row);
        }

        $page['item_count'] = $model_order->getCount();   //获取统计的数据
        $this->_format_page($page);
        
		$this->_config_seo('title', Conf::get('site_title').'面料申请记录');
        $this->assign('page_info', $page); 
        $this->assign('orders', $orders);
        $this->display("apply_record.index.html");
    }
    
    function actions($val){
        $opt = array();
        switch ($val['status']){
            case "11":
                $opt["pay"]    = 1;
                $opt['cancel'] = 1;
                break;
            case "30":
                $opt["finished"] = 1;
                break;
            case "40":
                $opt["refund"] = 1;
                $opt["return"] = 1;
                break;
            case "50":
                $opt['cancelrefund'] = 1;
                break;
            case "60":
                $opt["refund"] = 1;
                break;
        }
        return $opt;
    }
    
    function operation(){
        $arg = $this->get_params();
        
        $order_id  = isset($arg[0])  ? intval($arg[0])  : 0;
        $opt       = isset($arg[1])       ? trim($arg[1])  : '';
        
        $user_id = $this->visitor->get('user_id');
        
        $order_mod = m('order');
        
        $info = $order_mod->get("order_id='{$order_id}' AND user_id = '{$user_id}' AND extension = 'fabricbook'");
        
        $aData = array();
        
        if($opt == "finished" && $info['status'] == "30"){
            $aData = array(
                "finished_time" => gmtime(),
                'status'        => 40,
            );
        }
        
        if($opt == "cancel" && $info['status'] == "11"){
            $aData = array(
                "last_modified" => gmtime(),
                'status'        => 0,
            );
        }
        
        if($opt == "return" && $info['status'] == "40"){
            $aData = array(
                "last_modified" => gmtime(),
                'status'        => 70,
            );
        }
        
        if($opt == "cancelrefund" && $info['status'] == "50"){
            $aData = array(
                "last_modified" => gmtime(),
                'status'        => 60,
            );
        
            $refund_mod = m("bookrefund");
            $refund_mod->edit("order_id='{$order_id}' AND user_id = '{$user_info['user_id']}' AND status=0",  array("status" => 2));
        }
        
        
        $res = $order_mod->edit($order_id, $aData);
        
        if($res){
            header('Location:fabricbook-applyRecord.html');
        } else {
            $this->show_warning('意外错误！');
            return;
        }
    }
    
    
	function refund()
	{
	      if(!IS_POST){
	          
	      }else{
    	      $order_id  = isset($data->order_id)  ? intval($data->order_id)  : 0;
    	      $address   = isset($data->address)   ? trim($data->address)     : '';
    	      $phone     = isset($data->phone)     ? trim($data->phone)       : '';
    	      $consignee = isset($data->consignee) ? trim($data->consignee) : '';
    	      
    	      $user_info = getUserInfo($token);
    	      
    	      if(empty($address) || empty($phone) || empty($consignee) || empty($order_id)){
    	          return $this->eresult();
    	      }
    	  	  
    	  	  if (!$user_info)
    	      {
    	          return $this->tresult();
    	      }
    	      
    	      $refund_mod = m("bookrefund");
    	      
    	      $order_mod = m('order');
    	      
    	      $info = $order_mod->get("order_id='{$order_id}' AND user_id = '{$user_info['user_id']}' AND extension = 'fabricbook'");
    	      
    	      if($info['status'] != "40" && $info['status'] != "60"){
    	          return $this->eresult();
    	      }
    	      
    	      $count = $refund_mod->_count("order_id='{$order_id}' AND user_id = '{$user_info['user_id']}' AND status=0");
    
    	      if($count){
    	          $this->msg = "该面料册订单已提交过申请！";
    	          return $this->eresult();
    	      }
    	      
    	      
    	      $res = $refund_mod->add(array(
    	          'user_id'      => $user_info['user_id'],
    	          'user_name'    => $user_info["user_name"],
    	          'address'      => $address,
    	          'phone'        => $phone,
    	          'consignee'    => $consignee,
    	          "add_time"     => gmtime(),
    	          "order_id"     => $order_id,
    	          'status'       => 0,
    	      ));
    	      
    	      if($res){
    	           $order_mod->edit($order_id, array('status' => "50"));
    	           return $this->sresult();
    	      }else{
    	           $this->msg = "意外错误！";
    	           return $this->eresult(); 
    	      }
	      }
	}
    
}

?>