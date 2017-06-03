<?php
	class Gallery extends Result
	{
	
	  function getList($data) 
	  {
	      $topic     = isset($data->t)         ? intval($data->t) : 0;
	      $token     = isset($data->token)     ? trim($data->token) : '';
	      $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
	      $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
	      if($pageIndex < 1) $pageIndex = 1;
	      if($pageSize < 1) $pageSize = 1;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      
	      
	      $aData = $this->processPrice($user_info['user_id']);

	      $dis_mod = m('JpjzDissertation');
	      $link_mod = m("links");
	      $topicArr = $dis_mod->find(array(
	      	  'conditions' =>"is_show =1",
	          'order'      => "sort_order ASC",
	          "limit"      => 20,
	      ));
	      $result = array("topic" => array(), "custom" => array());
	      foreach((array)$topicArr as $key => $val){
	          $result["topic"][] = $val;
	      }
	      if(empty($topic)){
	          $current = $result['topic'][0];
	      }else{
	          $current = $topicArr[$topic];
	      }
	      
	      $customArr = $link_mod->find(array(
	         "conditions" => "links.d_id = '{$current["id"]}' AND suit_list.is_sale=1 AND (suit_list.to_site = '0' OR suit_list.to_site = 'app')",
	         "join"       => "app_custom",
	         'order'      => "links.lorder ASC",
	         'limit'      => $limit,
	      ));
	      
	      foreach((array) $customArr as $key => $val){
	          if($val['is_promotion']){
	              $val["price"] = $val["promotion_price"];
	          }
	          if(!empty($user_info)){
	            //  $val['price'] = _format_price_int($val['price'] * $user_info['dis_count']);
	              if(!in_array($val['category'], $aData) && $val["is_first"] == 1){
	                  switch ($val['category']){
	                      case "0003": //西装
	                          $val["price"] = "999";
	                          break;
	                      case "0006": //男衬衣
	                          $val["price"] = "199";
	                          break;
	                      case "0016":  //女衬衣
	                          $val["price"] = "199";
	                          break;
	                  }
	              }
	          }
	          
	          $val['woman_price'] = 0;
	          if ($val['d_id'] == 16) //=====  判断如果是女装系列 则商品价格是八折  ===== 
	          {
	              $val['woman_price'] = $val['price'] * 0.8;
	          }
	          
	          $result['custom'][] = $val;
	      }
	      
	      $this->result = $result;
	      return $this->sresult();
	  }
	  
	  function processPrice($userid){
	      if(!$userid) return array();
	      $mod_first = m("orderfirstlog");
	      $list = $mod_first->find(array(
	          "conditions" => "user_id='{$userid}' AND is_active=1",
	      ));
	      
	      $aData = array();
	      
	      foreach((array) $list as $key => $val){
	          $aData[] = $val["cloth"];
	      }
	      
	      return $aData;
	  }
	  
}

