<?php
class Bookorder extends Result
{
      //http://api.mfd.com/soap/bookorder.php?act=orderlist&token=922d6a5e7e55aa
      function orderlist($data){
          
          $token     = isset($data->token) ? $data->token : '';
          
          $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
          
          $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
          
          if($pageIndex < 1) $pageIndex = 1;
          
          if($pageSize < 1)  $pageSize  = 1;
          
          $limit     = $pageSize*($pageIndex-1).','.$pageSize;
          
          $user_info = getUserInfo($token);
          if (!$user_info)
          {
              return $this->tresult();
          }
          $user_id = $user_info['user_id'];
          
          
          $order_goods_mod = m('ordergoods');
          
          $list = $order_goods_mod->find(array(
              "conditions" => "order_alias.user_id='{$user_id}' AND order_alias.extension = 'fabricbook' AND order_alias.status != 0",
              "join"       => "app_book,app_order",
              'fields'     => "fabricbook.id, fabricbook.name, fabricbook.small_img, fabricbook.brief ,fabricbook.category,order_alias.order_amount, order_alias.order_id, order_alias.order_sn, order_alias.status, order_alias.express",
			  'order'      => "rec_id DESC",
			  'limit'      => $limit,
           ));

          $books = array();
          foreach((array) $list as $key => $val){
              $val["operation"]         = $this->actions($val);
              $val['format_status']     = $this->status($val);
              $books[] = $val;
          }
          
          $this->result = $books;
          
          return $this->sresult();
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
      
      //api.mfd.com/soap/bookorder.php?act=operation&token=922d6a5e7e55aa&order_id=236&opt=finished
      function operation($data){
          $token     = isset($data->token)     ? $data->token : '';
          
          $order_id  = isset($data->order_id)  ? intval($data->order_id)  : 0;
          
          $opt       = isset($data->opt)       ? trim($data->opt)  : '';
          
          $user_info = getUserInfo($token);
          
          $order_mod = m('order');
          
          $info = $order_mod->get("order_id='{$order_id}' AND user_id = '{$user_info['user_id']}' AND extension = 'fabricbook'");
          
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
          
          if(empty($data)){
              return $this->eresult();
          }
          
          $res = $order_mod->edit($order_id, $aData);
          
      	  if($res){
	           return $this->sresult();
	      }else{
	           $this->msg = "意外错误！";
	           return $this->eresult(); 
	      }
      }
      
      function status($val){
          $_s = array(
              "11"   => "未付款",
              "20"   => "待发货",
              "30"   => "已发货",
              "40"   => "已收货",
              "50"   => "已申请换货",
              "0"    => "已取消",
              "60"   => "已取消申请换货",
              "70"   => "已申请退货",
              "80"   => "退货完成"
          );
          
          return $_s[$val['status']];
      }
      
      //api.mfd.com/soap/bookorder.php?act=refund&token=922d6a5e7e55aa&address=3333333333&consigne=ttttttt&phone=18510332121&order_id=237
	  function refund($data)
	  {
	      $token     = isset($data->token)     ? trim($data->token)       : '';
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

