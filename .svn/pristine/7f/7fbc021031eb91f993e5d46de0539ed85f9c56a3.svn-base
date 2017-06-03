<?php
	/**
	 *  购物券相关的接口
	 * @author liang.li <1184820705@qq.com>
	 * @version $Id: profit.class.php 323 2015-04-27 02:27:30Z gaofei $
	 * @package app
	 */
	class Profit extends Result
	{
	
	    var $style = array();
	    function __construct()
	    {
	        parent::__construct();
	        $this->style = array(
	            "1" => "正装",
	            "2" => "休闲",
	            "3" => "礼服"
	        );
	    }
	 
	  /**
	  *抵用券列表
	  *@author liang.li <1184820705@qq.com>
	  *@2015年5月30日
	  */
	  function debitList($data) 
	  {
	      $type_limit = array(0,1,100);
	      $token = isset($data->token) ? $data->token : '';
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      $type = isset($data->type) ? $data->type : 0;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      
	      $user_info = getUserInfo($token);
	      $user_id = $user_info['user_id'];
	      if (!$user_info) 
	      {
	          return $this->tresult();
	      }
	      if (!in_array($type, $type_limit)) 
	      {
	          $this->msg = 'type参数必须是0或者1';
	          
	      }
	      $conditions = " (user_id = $user_id || from_uid = $user_id )";
	      if ($type != 100) 
	      {
	          if ($type == 0 || $type == 1) 
	          {
	              $conditions .= " AND is_used = $type AND is_invalid = 0";
	          }
	          else 
	          {
	              $conditions .= " AND is_invalid = 1 ";
	          }
	         
	      }
	      
	      
	      $debit_mod = m('debit');
	      
	      $list = $debit_mod->find(array(
	          'conditions' => $conditions,
	          'limit' => $limit,
	          'index_key' => '',
	          'order'   => "id DESC",
	      ));
		  
		  $member = m('member');
		    $cateList =  array(
			    '0001' => '套装',
				'0003' => '西服',
				'0004' => '西裤',
				'0005' => '马甲',
				'0006' => '衬衣',
				'0007' => '大衣',
            );
			
		  //处理组装数据
		  foreach($list as $key => $val) {
			  $list[$key]['donation_mes'] = '';
			  $list[$key]['cate_name']    = '';
			  $list[$key]['soon_time']    = '';
			  if ($list[$key]['source'] == 'admin_add') 
			  {
			      $list[$key]['source_name'] = "线上赠送";
			  }
			  elseif ($list[$key]['source'] == 'invite')
			  {
			      $list[$key]['source_name'] = "平台赠送";
			  } 
			  elseif ($list[$key]['source'] == 'active')
			  {
			     $list[$key]['source_name'] = "线下";
			  }
			 
			  if($cateList[$val['cate']]) {
				  $list[$key]['cate_name'] = $cateList[$val['cate']];
			  }
			  	  
			  if($val['from_uid'] && $val['user_id'] ) {//已转赠
			    if($user_id == $val['from_uid']) {//转赠人回去抵用券信息
					$memberinfo = $member->get($val['user_id']);
					$list[$key]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
				}
				
				if($user_id == $val['user_id']) {//被转赠人回去抵用券信息
					$memberinfo = $member->get($val['from_uid']);
					$list[$key]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
				}
			  }
			    //计算即将过期时间--10天以内显示
				if($val['expire_time'] - time() > 0) { 
					$days = floor(($val['expire_time'] - time())/86400);
					//$days = round(($val['expire_time'] - time())/86400)+1;
					if($days <= 10) {
						$list[$key]['soon_time']    = "(仅剩".$days."天)";
						if ($days == 0) //=====  最后一天要按照小时算  =====
						{
						    $hour = floor(($val['expire_time'] - time())/3600);
						    
						    $list[$key]['soon_time']    = "(仅剩".$hour."小时)";
						}
					} 
				}
				
				//显示线上线下标识
				if($val['source_from'] == 'active') {//线下
					$list[$key]['source_name'] = "线下";
				}

		  }
	      $this->result = $list;
	      return $this->sresult();
	  }
	  
	  /**
	  *抵用券个数
	  *@author liang.li <1184820705@qq.com>
	  *@2015年7月17日
	  */
	  function debitNum($data) 
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $user_info = getUserInfo($token);
	      $user_id = $user_info['user_id'];
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      $debit_mod = m('debit');
	      $this->formatDebit($user_id);
	      
	      $notUse = $debit_mod->get(array(
	          'conditions' => "(user_id = '{$user_id}' or from_uid = '{$user_id}')  AND is_used = 0 AND is_invalid = 0",
	          'fields' => "count(*) as num",
	      ));
	      
	      $haveUsed = $debit_mod->get(array(
	          'conditions' => "(user_id = '{$user_id}' or from_uid = '{$user_id}')  AND is_used = 1",
	          'fields' => "count(*) as num",
	      ));
	      
	      $haveInvalid = $debit_mod->get(array(
	          'conditions' => "(user_id = '{$user_id}' or from_uid = '{$user_id}')  AND is_invalid = 1",
	          'fields' => "count(*) as num",
	      ));
	      
	      $list['notUse'] = $notUse['num'];
	      $list['haveUsed'] = $haveUsed['num'];
	      $list['haveInvalid'] = $haveInvalid['num'];
	      $this->result = $list;
	      return $this->sresult();
	  }
	
	  /**
	  *格式化抵用券已经使用的
	  *@author liang.li <1184820705@qq.com>
	  *@2015年7月17日
	  */
	  function formatDebit($user_id) 
	  {
	      $time = time();
	      $debit_mod = m('debit');
	      $debit_list = $debit_mod->find(array(
	          'conditions' => "(user_id = $user_id OR from_uid = $user_id) AND is_invalid = 0",
	      ));
	      
	      
	      if ($debit_list) 
	      {
	          foreach ($debit_list as $key => $value) 
	          {
	              if ($value['expire_time'] < $time) 
	              {
	                  $debit_mod->edit($value['id'],array('is_invalid' => 1));
	              }
	          }
	      }
	      
	  }
	  
	  /**
	  *红包列表
	  *@author liang.li <1184820705@qq.com>
	  *@2015年5月30日
	  */
	  function giftList($data) 
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      // 	      $type = isset($data->type) ? $data->type : 1;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      
	      $user_info = getUserInfo($token);
	      $user_id = $user_info['user_id'];
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      
	      $gift_mod = m('gift');
	      $list = $gift_mod->find(array(
	          'conditions' => "user_id = $user_id ",
	          'limit' => $limit,
	          'index_key' => "",
	      ));
	      $this->result = $list;
	      return $this->sresult();
	  }
	  
	  function goodsWorldInfo($data)
	  {
	      $id = isset($data->id) ? $data->id : 0;
	      $token = isset($data->token) ? $data->token : '';
	      $f_id = isset($data->f_id) ? $data->f_id : '';
	      $user_info = getUserInfo($token);
	  
	      $dis_count = 0;
	      if ($user_info['dis_count'])
	      {
	          $dis_count = $user_info['dis_count'];
	      }
	      $img_url = LOCALHOST1;
	      $suit_list_mod      = m('suitlist');
	      $suit_rel_mod = m('suitrelat');
	      $custom_mod = m('custom');
	      $custom_fabirc = m('cusFab');
	      $fabric_mod = m('fabric');
	      $dict_mod = m('dict1');
	      $custom_gallery_mod = m('customgallery');
	      $suit_gallery_mod = m('suitgallery');
	      $fabricgallery_mod = m('fabricgallery');
	      $model_collect =& m('collect');
	      $fabricpricecf_mod = m('fabricpricecf');
	      $fabric_info_mod = m('fabricinfo');
	      $fabric_rel_attr_mod = m('fabricrelattr');
	       
	      $path = dirname(__FILE__);
	      if (file_exists($path. '/../../includes/data/config/pinlei.php'))
	      {
	          $pinlie = include $path.'/../../includes/data/config/pinlei.php';
	      }
	      else
	      {
	          return $this->eresult('品类无法获得');
	      }
	      $aData = $this->processPrice($user_info['user_id']);
	      $suit_list_info = $suit_list_mod->get_info($id);
	      if (!$suit_list_info['is_sale'])
	      {
	          return $this->eresult('无此商品或者禁止查看');
	      }
	      if ($suit_list_info['theme'] != 11) 
	      {
	          return $this->eresult('此商品不是全球首发');
	      }
	       
	      if(!in_array($suit_list_info['category'], $aData) && $suit_list_info["is_first"] == 1)
	      {
	          switch ($suit_list_info['category'])
	          {
	              case "0003": //西装
	                  $suit_list_info["price"] = "999.00";
	                  break;
	              case "0006": //男衬衣
	                  $suit_list_info["price"] = "199.00";
	                  break;
	              case "0016":  //女衬衣
	                  $suit_list_info["price"] = "199.00";
	                  break;
	          }
	      }
	      //=====  点击数加1  =====
	      $suit_list_mod->setInc($id,'click_num');
	       
	      $suit_info = $suit_rel_mod->get("tz_id = $id");
	      $cus_list = array();
	      $gallery = array();
	      $fabricgallery_list = array();
	      $size_img = array();
	      if (!$suit_info)
	      {
	          return  $this->eresult('空数据');
	      }
	      $cus_ids = $suit_info['jbk_id'];
	      //=====  样衣列表  =====
	      $cus_list = $custom_mod->find(array(
	          'conditions' => "id IN ($cus_ids)",
	          'index_key' => "",
	      ));
	      //=====  如果是f_code参数不为空 说明是全球首发过来的商品  全球首发只允许出现一个商品  =====
          $fabricpricecf_info =  $fabricpricecf_mod->find(array(
              'conditions' => "fabric_id = $f_id",
              'index_key' => "category",
          ));
          if (!$fabricpricecf_info)
          {
              return  $this->eresult('面料价格不存在');
          } 
	       
	      //=====  custom_fabric表的面料列表 如果是全球首发则取cf_fabric_info表的面料信息  =====
	      $custom_fabric_list = $fabric_info_mod->get(array(
	              'conditions' => "fabric_id = $f_id",
	               
	      ));
	      if (!$custom_fabric_list) 
	      {
	          return  $this->eresult('面料不存在');
	      }
	      
	      //=====  相册表 关联相册  =====
	      $url = SITE_URL;
	      //=====  如果套装有相册就去套装的相册  =====
	      $gallery = $suit_gallery_mod->find(array(
	          'conditions' => "suit_id=".$id,
	          'index_key'  => "",
	      ));
	      if (!$gallery)
	      {
	          $cus_ids_arr = explode(",", $cus_ids);
	          if ($cus_ids_arr)
	          {
	              foreach ($cus_ids_arr as $key => $value)
	              {
	                  if (!$value)
	                  {
	                      continue;
	                  }
	                  $gallery_list = $custom_gallery_mod->find(array(
	                      "fields" => "CONCAT(small_img,'$url') as small_img,CONCAT(middle_img,'$url') as middle_img,CONCAT(source_img) as source_img,sort,custom_id,id",
	                      'conditions' => "custom_id = $value",
	                      'index_key' => "",
	                      'order'     => "sort asc",
	                  ));
	                  $gallery = array_merge($gallery,$gallery_list);
	              }
	          }
	      }
	      $fabric_img['small_img'] = $custom_fabric_list['fabric_img'];
	      $fabric_img['middle_img'] = $custom_fabric_list['fabric_img'];
	      $fabric_img['source_img'] = $custom_fabric_list['fabric_img'];
	      $fabric_img['custom_id'] = 0;
	      $fabric_img['id'] = 0;
	      array_unshift($gallery, $fabric_img);
	      $suit_list_info['gallery_list'] = $gallery;
	      $dict_list = array();
	      //=====  取得面料和面料属性  =====
	      if ($custom_fabric_list)
	      {
	          $code = $custom_fabric_list['fabric_sn'];
	          if ($code) 
	          {
	              //=====  获得面料相关  =====
	              $fabric_list = $fabric_mod->get(array(
	                  'conditions' => "CODE = '$code' ",
	              ));
	              //=====  获取面料相册相关  =====
	              $fabric_id = $fabric_list['ID'];
	              if ($fabric_list) 
	              {
    	              $fabricgallery_list = $fabricgallery_mod->find(array(
    	                  'conditions' => "fabric_id = $fabric_id",
    	                  'index_key'  => "fabric_id",
    	              ));
    	               
    	              $dict_id_arr = array($fabric_list['COMPOSITIONID'],$fabric_list['COLORID'],$fabric_list['FLOWERID']);
    	              $dict_ids = db_create_in($dict_id_arr,"ID");
    	              $dict_list = $dict_mod->find(array(
    	                  'conditions' => "$dict_ids",
    	              ));
	              }
	              $custom_fabric_list['chengfen'] = !empty($fabric_list['chengfen']) ? $fabric_list['chengfen'] : ''; //=====  砂纸  =====
	              $custom_fabric_list['yanse'] = !empty($fabric_list['color']) ? $fabric_list['color'] : (!empty($dict_list[$fabric_list['COLORID']]['NAME']) ? $dict_list[$fabric_list['COLORID']]['NAME']: ''); //=====  颜色  =====
	              $custom_fabric_list['huaxing'] = !empty($dict_list[$fabric_list['FLOWERID']]['NAME']) ? $dict_list[$fabric_list['FLOWERID']]['NAME'] : ''; //=====  花型  =====
	              $custom_fabric_list['shazhi'] = !empty($fabric_list['SHAZHI']) ? $fabric_list['SHAZHI'] : ''; //=====  砂纸  =====
	              $custom_fabric_list['fabric'] = !empty($fabric_list['CODE']) ? $fabric_list['CODE'] : ''; //=====  面料编号  =====
	              $custom_fabric_list['fabric_img'] = !empty($fabricgallery_list[$fabric_list['ID']]['source_img']) ? $fabricgallery_list[$fabric_list['ID']]['source_img'] : ''; //=====  面料编号  =====
	          }
	         
	      }
// 	  return $fabric_list;
// 	   return $dict_list;
	      $linkAttrs = array();
	      if ($cus_list)
	      {
	          $suit_fabices=SITE_URL."/static/img/fabices/".$suit_list_info['fabices'].".png";
	          //=====  取得尺码 和面料相关  =====
	          foreach ($cus_list as $key => $value)
	          {
	              //=====  尺码助手的相册  =====
	              $res = $this->getSizeByCate($value['category'],$value['size_id']);
	              $size_img[] = $res['size_exp'];
	              $cus_list[$key]['size'] = $res['size'];
	  
	              $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格
	              $cus_list[$key]['size_url'] = "http://m.mfd.cn/article/spec.html";
	              $cus_list[$key]['cate_name'] = $pinlie[$value['category']]['name'];
	              //=====  面料属性  =====
	              $cus_list[$key]['chengfen'] = $custom_fabric_list['chengfen'];
	              $cus_list[$key]['yanse']    = $custom_fabric_list['yanse'];
	              $cus_list[$key]['huaxing']  = $custom_fabric_list['huaxing'];
	              $cus_list[$key]['shazhi']   = $custom_fabric_list['shazhi'];
	              $cus_list[$key]['fabric']   = $custom_fabric_list['fabric'];
	              $cus_list[$key]['style'] = $this->style[$value['style']];
	              $cus_list[$key]['fabric_img'] = $custom_fabric_list['fabric_img'];
	              $category = $value['category'];
	          }
	  
	      }
	      if (count($cus_list) == 2)//=====  全球首两件套取套装的价格  ===== 
	      {
	          $price = $fabricpricecf_info[10]['price'];
	      }
	      else 
	      {
	          $price = $fabricpricecf_info[$pinlie[$category]['alis_id']]['price'];
	      }
	      $suit_list_info['price'] = _format_price_int($price);
	      //=====  相关搭配  =====
	      $suit_link_list = array();
	      $suit_link_mod = m('suitlink');
	      $suit_link_list = $suit_link_mod->find(array(
	          'conditions' => "s_id = $id AND suit_list.is_sale = 1",
	          'join'       => "belongs_to_suit",
	          'fields'     => "suit_list.*",
	          'index_key' => "",
	      ));
	      $time = time();
	      if ($suit_link_list)
	      {
	          foreach ($suit_link_list as $key => $value)
	          {
	              //=====  价格  =====
	              $dis_count = 0;
	              if ($dis_count)
	              {
	                  $suit_link_list[$key]['price'] = _format_price_int($value['price'] * $dis_count);
	              }
	              else
	              {
	                  $suit_link_list[$key]['price'] = _format_price_int($value['price']);
	              }
	              
	              if($value['is_promotion'] == 1 && $time >= $value['star_time'] && $time <= $value['over_time'])
	              {
	                  $suit_link_list[$key]["price"] = $value["promotion_price"];
	              }
	              
	              $suit_link_list[$key]["woman_price"] = '';
	              if ($value['theme'] == 16) //=====  女装要打折  =====
	              {
	                  $suit_link_list[$key]["woman_price"] = "";
	              }
	              
	          }
	      }
	  
	      //=====  评论数量  =====
	      $detai_comment_mod = m('detail_comment');
	      $detail = $detai_comment_mod->get(array(
	          'conditions' => "comment_id = $id AND cate= 'suit' ",
	          'fields' => "count(*) as num",
	  
	      ));
	      $num = isset($detail['num']) ? $detail['num'] : 0;
	  
	      // 添加 此商品是否 背收藏过 标识  条件 是 这个 用户必须登录了之后 token 有值 才可以看到  auth tangsj
	      $user_id = $user_info['user_id'];
	      $is_collect = $model_collect->get("item_id ='{$id}' and user_id='{$user_id}'");
	      if($is_collect){
	          $this->result['is_collect'] = 1;
	      }else{
	          $this->result['is_collect'] = 0;
	      }
	  
	  
	      $this->result['suit_fabices'] = $suit_fabices;
	      $this->result['list_cus'] = $cus_list;
	      $this->result['list_attr'] = $linkAttrs;
	      $this->result['suit_info'] = $suit_list_info;
	      $this->result['suit_link'] = $suit_link_list;
	      $this->result['size_img'] = $size_img;
	      $this->result['comment_num'] = $num;
	      return $this->sresult();
	  }
	  
	  
	  
	  /**
	  *   产品详情
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月1日
	  *list URL new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
	  *info URL  new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
	  */
	  //http://local.soaapi.mfd.com/soap/profit.php?act=goodsInfo&token=eda6b5ebb68702e0e49841cb9aeb50d3&id=63
	  function goodsInfo($data) 
	  {
	      //=====  全球首发  =====
	      $f_id = isset($data->f_id) ? $data->f_id : '';
	      if ($f_id)
	      {
	          return $this->goodsWorldInfo($data);
	      }
	      
	      $id = isset($data->id) ? $data->id : 0;
	      $token = isset($data->token) ? $data->token : '';
	      $user_info = getUserInfo($token);
          $time = time();
	      $dis_count = 0;
	      if ($user_info['dis_count']) 
	      {
	          $dis_count = $user_info['dis_count'];
	      }
	      $img_url = LOCALHOST1;
	      $suit_list_mod      = m('suitlist');
	      $suit_rel_mod = m('suitrelat');
	      $custom_mod = m('custom');
	      $custom_fabirc = m('cusFab');
	      $fabric_mod = m('fabric');
	      $dict_mod = m('dict1');
	      $custom_gallery_mod = m('customgallery');
	      $suit_gallery_mod = m('suitgallery');
	      $fabricgallery_mod = m('fabricgallery');
	      $model_collect =& m('collect');
	      
	      
	      $path = dirname(__FILE__); 
	      if (file_exists($path. '/../../includes/data/config/pinlei.php')) 
	      {
	          $pinlie = include $path.'/../../includes/data/config/pinlei.php';
	      }
	      else 
	      {
	          $this->msg = '品类无法获得';
	          return $this->eresult();
	      }
	      $aData = $this->processPrice($user_info['user_id']);
	      $suit_list_info = $suit_list_mod->get_info($id);
	      if (!$suit_list_info['is_sale']) 
	      {
	          $this->msg = '无此商品或者禁止查看';
	          return $this->eresult();
	      }
	      
	      if($suit_list_info['is_promotion'] == 1 && $time >= $suit_list_info['star_time'] && $time <= $suit_list_info['over_time'])
	      {
	          $suit_list_info["price"] = $suit_list_info["promotion_price"];
	      }
	      
	     
	      $dis_count = 0;
	      if ($dis_count) 
	      {
	          $suit_list_info['price'] = _format_price_int($suit_list_info['price'] * $dis_count);
	      }
	      else 
	      {
	          $suit_list_info['price'] = _format_price_int($suit_list_info['price']);
	      }
	      
	      
	      
	      if(!in_array($suit_list_info['category'], $aData) && $suit_list_info["is_first"] == 1)
	      {
	          switch ($suit_list_info['category'])
	          {
	              case "0003": //西装
	                  $suit_list_info["price"] = "999.00";
	                  break;
	              case "0006": //男衬衣
	                  $suit_list_info["price"] = "199.00";
	                  break;
	              case "0016":  //女衬衣
	                  $suit_list_info["price"] = "199.00";
	                  break;
	          }
	      }
	      //=====  点击数加1  =====
	      $suit_list_mod->setInc($id,'click_num');
	      
	      $suit_info = $suit_rel_mod->get("tz_id = $id");
	      $cus_list = array();
	      $gallery = array();
	      $fabricgallery_list = array();
	      $size_img = array();
	      if ($suit_info) 
	      {
	          $cus_ids = $suit_info['jbk_id'];
	          //=====  样衣列表  =====
	          $cus_list = $custom_mod->find(array(
	              'conditions' => "id IN ($cus_ids)",
	              'index_key' => "",
	          ));
	          
	          //=====  custom_fabric表的面料列表  =====
	          $custom_fabric_list = $custom_fabirc->find(array(
	              'conditions' => "custom_id IN ($cus_ids) AND is_default = 1",
	              'index_key'  => "custom_id",
	              
	          ));
	          
	          
	          //=====  相册表 关联相册  =====
	          $url = SITE_URL;
	         
	          //=====  如果套装有相册就去套装的相册  =====
	          $gallery = $suit_gallery_mod->find(array(
	              'conditions' => "suit_id=".$id,
	              'index_key'  => "",
	          ));
	          if (!$gallery)
	          {
	              $cus_ids_arr = explode(",", $cus_ids);
	              if ($cus_ids_arr)
	              {
	                  foreach ($cus_ids_arr as $key => $value)
	                  {
	                      if (!$value)
	                      {
	                          continue;
	                      }
	                      $gallery_list = $custom_gallery_mod->find(array(
	                          "fields" => "CONCAT(small_img,'$url') as small_img,CONCAT(middle_img,'$url') as middle_img,CONCAT(source_img) as source_img,sort,custom_id,id",
	                          'conditions' => "custom_id = $value",
	                          'index_key' => "",
	                          'order'     => "sort asc",
	                      ));
	                      $gallery = array_merge($gallery,$gallery_list);
	                  }
	              }
	          }
	      }
	      $suit_list_info['gallery_list'] = $gallery;
	      //=====  取得面料和面料属性  =====
	      if ($custom_fabric_list) 
	      {
	          
	          //=====  获得面料相关  =====
	          $fabric_id_arr = i_array_column($custom_fabric_list,'item_id');
	          $fabric_ids = db_create_in($fabric_id_arr,"ID");
	          $fabric_list = $fabric_mod->find(array(
	              'conditions' => "$fabric_ids",
	          ));
	          
	          //=====  获取面料相册相关  =====
	          $fabric_ids_g = db_create_in($fabric_id_arr,"fabric_id");
	          $fabricgallery_list = $fabricgallery_mod->find(array(
	              'conditions' => "$fabric_ids_g",
	              'index_key'  => "fabric_id", 
	          ));
	          
	          
	          //=====  获取面料属性相关  =====
	          $COMPOSITIONID = i_array_column($fabric_list, 'COMPOSITIONID');
	          $COLORID = i_array_column($fabric_list, 'COLORID');
	          $FLOWERID = i_array_column($fabric_list, 'FLOWERID');
	          $dict_id_arr = array_merge($COMPOSITIONID,$COLORID,$FLOWERID);
	          $dict_ids = db_create_in($dict_id_arr,"ID");
	          $dict_list = $dict_mod->find(array(
	              'conditions' => "$dict_ids",
	          ));
	           
	          foreach ($custom_fabric_list as $key => $value) 
	          {
	              
	              $custom_fabric_list[$key]['chengfen'] = $fabric_list[$value['item_id']]['chengfen']; //=====  砂纸  =====
	              $custom_fabric_list[$key]['yanse'] = !empty($dict_list[$fabric_list[$value['item_id']]['COLORID']]['NAME']) ? $dict_list[$fabric_list[$value['item_id']]['COLORID']]['NAME'] : (!empty($fabric_list[$value['item_id']]['color']) ? $fabric_list[$value['item_id']]['color'] : ''); //=====  颜色  =====
	              $custom_fabric_list[$key]['huaxing'] = $dict_list[$fabric_list[$value['item_id']]['FLOWERID']]['NAME']; //=====  花型  =====
	              $custom_fabric_list[$key]['shazhi'] = $fabric_list[$value['item_id']]['SHAZHI']; //=====  砂纸  =====
	              $custom_fabric_list[$key]['fabric'] = $fabric_list[$value['item_id']]['CODE']; //=====  面料编号  =====
	              $custom_fabric_list[$key]['fabric_img'] = $fabricgallery_list[$value['item_id']]['source_img']; //=====  面料编号  =====
	          }
	      }
	      $linkAttrs = array();
          if ($cus_list) 
          {
          	$suit_fabices=SITE_URL."/static/img/fabices/".$suit_list_info['fabices'].".png";
            //=====  取得尺码 和面料相关  =====
            foreach ($cus_list as $key => $value) 
            {
                //=====  尺码助手的相册  =====
                $res = $this->getSizeByCate($value['category'],$value['size_id']);
                $size_img[] = $res['size_exp'];
                $cus_list[$key]['size'] = $res['size'];
                
                $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格
                $cus_list[$key]['size_url'] = "http://m.mfd.cn/article/spec.html";
                $cus_list[$key]['cate_name'] = $pinlie[$value['category']]['name'];
                //=====  面料属性  =====
                $chengfen = "";
                if ($custom_fabric_list[$value['id']]['chengfen'])
                {
                    $chengfen = $custom_fabric_list[$value['id']]['chengfen'];
                }
                $cus_list[$key]['chengfen'] = $chengfen;
                $cus_list[$key]['yanse']    = $custom_fabric_list[$value['id']]['yanse'];
                $cus_list[$key]['huaxing']  = $custom_fabric_list[$value['id']]['huaxing'];
                $cus_list[$key]['shazhi']   = $custom_fabric_list[$value['id']]['shazhi'];
                $cus_list[$key]['fabric']   = $custom_fabric_list[$value['id']]['fabric'];
                $cus_list[$key]['style'] = $this->style[$value['style']];
                $cus_list[$key]['fabric_img'] = $custom_fabric_list[$value['id']]['fabric_img'];
            }
            
          }
          
          //=====  相关搭配  =====
          $suit_link_list = array();
          $suit_link_mod = m('suitlink');
          $suit_link_list = $suit_link_mod->find(array(
              'conditions' => "s_id = $id AND suit_list.is_sale = 1",
              'join'       => "belongs_to_suit",
              'fields'     => "suit_list.*",
              'index_key' => "",
          ));
//        return $id;
          if ($suit_link_list) 
          {
              foreach ($suit_link_list as $key => $value) 
              {
                  
                  //=====  价格  =====
                  $dis_count = 0;
                  if ($dis_count)
                  {
                      $suit_link_list[$key]['price'] = _format_price_int($value['price'] * $dis_count);
                  }
                  else
                  {
                      $suit_link_list[$key]['price'] = _format_price_int($value['price']);
                  }
                  
                  if($value['is_promotion'] == 1 && $time >= $value['star_time'] && $time <= $value['over_time'])
                  {
                      $suit_link_list[$key]["price"] = $value["promotion_price"];
                  }
                  
                  $suit_link_list[$key]["woman_price"] = '';
                  if ($value['theme'] == 16) //=====  女装要打折  ===== 
                  {
                      $suit_link_list[$key]["woman_price"] = '';
                  }
              }
          }
          
          //=====  评论数量  =====
          $detai_comment_mod = m('detail_comment');
          $detail = $detai_comment_mod->get(array(
              'conditions' => "comment_id = $id AND cate= 'suit' ",
              'fields' => "count(*) as num",
              
          ));
          $num = isset($detail['num']) ? $detail['num'] : 0;
          
          // 添加 此商品是否 背收藏过 标识  条件 是 这个 用户必须登录了之后 token 有值 才可以看到  auth tangsj
          $user_id = $user_info['user_id'];
          $is_collect = $model_collect->get("item_id ='{$id}' and user_id='{$user_id}'");
          if($is_collect){
              $this->result['is_collect'] = 1;
          }else{
              $this->result['is_collect'] = 0;
          }
          
          
          $this->result['suit_fabices'] = $suit_fabices;
          $this->result['list_cus'] = $cus_list;
          $this->result['list_attr'] = $linkAttrs; 
          $this->result['suit_info'] = $suit_list_info;
          $this->result['suit_link'] = $suit_link_list;
          $this->result['size_img'] = $size_img;
          $this->result['comment_num'] = $num;
          return $this->sresult();
	  }
	  
	  /**
	  *获得尺码信息
	  *@author liang.li <1184820705@qq.com>
	  *@2015年12月25日
	  */
	  function getSizeByCate($cate,$key=0) 
	  {
	      $return = array();
	      $size = array();
	      if ($cate != '0021' && $cate != '0012' && $cate != '0011' && $cate != '0032')//=====  女大衣 特殊处理  ===== 
	      {
	          $filename = PROJECT_PATH.'includes/data/config/size_json/'.$cate.'_10205.size.json';
	          if ($cate == "0016") 
	          {
	              $filename = PROJECT_PATH.'includes/data/config/size_json/'.$cate.'_10205_11004.size.json';
	          }
	          if (file_exists($filename))
	          {
	              $jsonString = file_get_contents($filename);
	              $jsonData = json_decode($jsonString,true);
	              $size_list = $jsonData['sizeAll'] ;
	              foreach ($size_list as $key1 => $value1)
	              {
	                  $size[] = $value1['Id'];
	              }
	          }
	          
	          $size_exp = SITE_URL."/static/img/".$cate.".png";
	      }
	      else 
	      {
	          $size_table_mod = m('sizetable');
	          $size_style_mod = m('sizestyle');
	          $size_style_info = $size_style_mod->get_info($key);
	          if (!$size_style_info) 
	          {
	              return $return;
	          }
	          $size_exp = $size_style_info['img'];
	          $size_table_list = $size_table_mod->find(array(
	              'conditions' => "name_id = '$key' ",
	          ));
	          if ($size_table_list) 
	          {
	              foreach ($size_table_list as $key => $value) 
	              {
	                  $size[] = $value['standard_code'];
	              }
	          }
	          
	      }
	      $return['size'] = $size;
	      $return['size_exp'] = $size_exp;
	      return $return;
	  }
	  
	  
	  /**
	  *收入记录
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月1日
	  */
	  function incomeList($data) 
	  {
	      $type_limit = array(0,1,100);
	      $token = isset($data->token) ? $data->token : '';
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      $type = isset($data->type) ? $data->type : 100;
//$type = isset($data->type) ? $data->type : 1;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      if (!in_array($type, $type_limit)) 
	      {
	          $this->msg = 'type参数错误';
	          return $this->eresult();
	      }
	      
	      $user_info = getUserInfo($token);
	      $user_id = $user_info['user_id'];
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      
	      
	      $order_mod = m('ordercashlog');
	      
	      $conditions = "  user_id = $user_id AND is_point = 0";
	      if ($type != 100) 
	      {
	          $conditions .= " AND minus = $type ";
	      }
	      
	      
	      $list = $order_mod->find(array(
	          'conditions' => $conditions,
	          'index_key' => "",
	          'limit' => $limit,
	      ));
// dump($list);
         $this->result = $list;
         return $this->sresult();
	      
	  }
	  
	  
	  /**
	  *  首页推荐样衣
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月2日
	  */
	  function indexList($data) 
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $user_info = getUserInfo($token);
	      if ($user_info['dis_count'])
	      {
	          $dis_count = $user_info['dis_count'];
	      }
	      
	      $config = include_once PROJECT_PATH.'includes/data/config/index.custom.php';
	      $ids = i_array_column($config, 'id');
// 	dump($ids);
          $ids = db_create_in($ids,"id");
	      
	      $suit_list_mod  = m('suitlist');
	      $suit_list = $suit_list_mod->find(array(
	          'conditions' => $ids,
	      ));
	      
	      
	      if ($config) 
	      {
	          foreach ($config as $key => $value) 
	          {
	              if ($suit_list[$value['id']]) 
	              {
	                  $config[$key]['name'] = $suit_list[$value['id']]['suit_name'];
	                  $config[$key]['likes'] = $suit_list[$value['id']]['click_num'];
	                  $price = $suit_list[$value['id']]['price'];
	                  if ($dis_count) 
	                  {
	                      $price = _format_price_int($price * $dis_count);
	                  }
	                  else 
	                  {
	                      $price = _format_price_int($price);
	                  }
	                  $config[$key]['price'] = $price;
	              }
	              else 
	              {
	                  $config[$key]['name'] = "";
	                  $config[$key]['likes'] = "";
	                  $config[$key]['price'] = "";
	              }
	          }
	      }
	      $this->result = $config;
	      return $this->sresult();
	  }
	  
	  
	  /**
	  *订单列表
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  *@url 
	  */
	  function orderList($data) 
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $status = isset($data->status) ? $data->status : -1;
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      $pageSize = 1000;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      
	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      $user_id = $user_info['user_id'];
	      
	      $order_val = include PROJECT_PATH.'includes/data/config/order.php';
// dump($order_val);	      
	      $order_goods_mod = m('ordergoods');
	      $order_suit_mod = m('ordersuit');
		  $orderrefund_mod  = & m('orderrefund');
		  $mOsd = m('ordershipdelay');
		  $model_setting = &af('settings');
		  $setting = $model_setting->getAll(); // 载入系统设置数据
	      $conditions = "user_id = $user_id AND extension != 'fabricbook' ";
	      if ($status != -1)
	      {		
			  //已付款、生产中、已发货 将已将订单状态改为“退款中”,如果查询这三种状态，则将退款中也一并查询
	          $status  = intval($status);
	          if($status == ORDER_WAITFIGURE || $status == ORDER_ACCEPTED){//待量体、已支付、退款中
				  
	              $conditions .= " AND status IN (".ORDER_WAITFIGURE.",".ORDER_ACCEPTED.",".ORDER_TUIKUANZHONG.")";
				  
	          } elseif($status == ORDER_PRODUCTION) { //成产中、备货中、退款中
			  
				  $conditions .= " AND status IN (".ORDER_PRODUCTION.",".ORDER_STOCKING.",".ORDER_TUIKUANZHONG.")";
				  
			  } elseif($status == ORDER_FINISHED) { //已完成、返修中、已退款
			  
				  $conditions .= " AND status IN (".ORDER_FINISHED.",".ORDER_REPAIR.",".ORDER_YINGTUIKUAN.")";
				  
			  } elseif($status == ORDER_SHIPPED) { //已发货、退款中
			  
				  $conditions .= " AND status IN (".ORDER_SHIPPED.",".ORDER_TUIKUANZHONG.")";
				  
			  } else {
	              $conditions .= " AND status = $status";
	          }
	      }else{
              $conditions .= " AND status != 0 ";
          }

	      $list_diy = $order_goods_mod->find(array(
	          'conditions' => $conditions . " AND type = 'diy' ",
	          'join' => "belongs_to_order",
	          'limit' => $limit,
	          'fields' => "order_alias.order_id,user_name,order_sn,goods_image,size,status,final_amount,add_time,ship_time,ship_mobile,ship_name,has_measure",
	          'order' => "order_alias.order_id DESC",
	      ));
	      $list_suit = $order_suit_mod->find(array(
	          'conditions' => $conditions . " AND type = 'suit' ",
	          'join' => "belongs_to_order",
	          'limit' => $limit,
	          'fields' => "order_alias.order_id,user_name,order_sn,goods_image,goods_name,price,dis_ident,goods_id,quantity,status,final_amount,add_time,ship_time,ship_mobile,ship_name,has_measure",
	          'order' => "order_alias.order_id DESC",
	      ));
	      
	      $list_suit_size = $order_goods_mod->find(array(
	          'conditions' => $conditions . " AND type = 'suit' ",
	          'join' => "belongs_to_order",
	          'limit' => $limit,
	          'fields' => "suit_id,size",
	          'order' => "order_alias.order_id DESC",
	          'index_key' => "suit_id",
	      ));
	      
	      $list_return = array();
	      $order_info = array();
	      $list = $list_diy;
	      if ($list) 
	      {
	          foreach ($list as $key => $value) 
	          {		
					$refund_status = 0;//退款申请按钮显示
					//====订单状态为退款进行特殊处理  START=====
					if($value['status'] == ORDER_TUIKUANZHONG) {
						//=====申请退款按钮  START=====
						//已付款、生产中、已发货 这几个状态下可以操作退款
						$orderrefund_info = $orderrefund_mod->get("order_id='{$value['order_id']}'");
						//查看当前退款订单之前状态是否是当前查询状态
						if($status != $orderrefund_info['order_status']) {
							continue;
						}
						
						//如果订单处于退款中，并且用户尚未操作申请退款,则出现退款按钮，并且保持原订单状态不变
						if(empty($orderrefund_info['apply_time'])) {
							$value['status'] = $orderrefund_info['order_status'];
							$refund_status = 1;
						}
						
					}
					$list_return[$value['order_id']]['refund_status'] =  $refund_status;
					//====订单状态为退款进行特殊处理  END=====
					
					//出现显示的顾客名称
					$user_name = '';
					if($value['has_measure']) {//量体显示量体姓名
						$figureorderm_mod = m('figureorderm');
						
						$figure_info = $figureorderm_mod->get(array(
							'conditions' => " order_id = {$value['order_id']}",
							'fields'     => 'liangti_id',
						));
						if($figure_info['liangti_id']) {
							$member_mod = m('member');
							$figure_member = $member_mod->get($figure_info['liangti_id']);
							$user_name = $figure_member['real_name'] . "(" .  $figure_member['phone_mob'] . ")";
						}
						
					} else {//标准尺码显示收获信息中用户名
						$user_name = $value['ship_name'] . "(" .  $value['ship_mobile'] . ")";
					}
					$user_name = $value['ship_name'] . "(" .  $value['ship_mobile'] . ")";
				  
	              $list_return[$value['order_id']]['order_sn'] =  $value['order_sn'];
	              $list_return[$value['order_id']]['add_time'] =  $value['add_time'];
	              $list_return[$value['order_id']]['user_name'] =  $user_name;
	              $list_return[$value['order_id']]['order_id'] =  $value['order_id'];
	              $list_return[$value['order_id']]['final_amount'] =  $value['final_amount'];
	              $list_return[$value['order_id']]['status'] =  $value['status'];
	              $statusname = "";
				  
	              if ($order_val[$value['status']]) 
	              {		
					if(($status == ORDER_PRODUCTION || $status == ORDER_STOCKING) ) {
						$status_code = ORDER_PRODUCTION;
						$statusname = $order_val[$status_code]; 
					} else {
						$statusname = $order_val[$value['status']];
					}
	              }
				  
	              $list_return[$value['order_id']]['statusname'] =  $statusname;
	              
	              $tmp['img_url'] = $value['goods_image'];
	              $tmp['type'] = 'diy';
	              $tmp['goods_name'] = $value['goods_name'];
	              $tmp['goods_id'] = $value['goods_id'];
	              $tmp['quantity'] = $value['quantity'];
	              $tmp['price'] = $value['price'];
	              if ($value['size'] == 'diy') 
	              {
	                  $tmp['szie'] = '量体定制';
	              }
	              $tmp['szie'] = $value['size'];
	              $tmp['order_goods_id'] = $value['rec_id'];
	             
	              $list_return[$value['order_id']]['item'][] =  $tmp;
				  
				//判断此订单是否已评论完毕(已完成)
				$list_return[$value['order_id']]['comment_status'] = 0;
				if($value['status'] == ORDER_FINISHED) {
					$order_suit = $order_suit_mod->findAll(array(
						"conditions" => "comment = 0 AND order_id = " . $value['order_id'],
					));
					$order_goods = $order_goods_mod->findAll(array(
						"conditions" => "comment = 0 AND goods_id = 0 AND order_id = " . $value['order_id'],
					));
					if($order_goods || $order_suit) {
						$list_return[$value['order_id']]['comment_status'] = 1; 
					}
				}
				
				//=====延期收获  START=====
				$list_return[$value['order_id']]['ship_delay'] = 0;
				if($value['status'] == ORDER_SHIPPED ) {
					$ship_delay = $this->do_delay($value);
					$list_return[$value['order_id']]['ship_delay'] = $ship_delay['ship_delay'];
				}
				//=====延期收获  END=====
				
	          }
	      }
	      
	      $list = $list_suit;
		  $now_time = time();
	      if ($list)
	      {
	          foreach ($list as $key => $value)
	          {
					$refund_status = 0;//退款申请按钮显示
					//====订单状态为退款进行特殊处理  START=====
					if($value['status'] == ORDER_TUIKUANZHONG) {
						
						//=====申请退款按钮  START=====
						//已付款、生产中、已发货 这几个状态下可以操作退款
						$orderrefund_info = $orderrefund_mod->get("order_id='{$value['order_id']}'");
						//查看当前退款订单之前状态是否是当前查询状态
						if($status != $orderrefund_info['order_status']) {
							continue;
						}
						
						//如果订单处于退款中，并且用户尚未操作申请退款,则出现退款按钮，并且保持原订单状态不变
						if(empty($orderrefund_info['apply_time'])) {
							$value['status'] = $orderrefund_info['order_status'];
							$refund_status = 1;
						}
					}
					$list_return[$value['order_id']]['refund_status'] =  $refund_status;
					//====订单状态为退款进行特殊处理  END=====
					
					//出现显示的顾客名称
					$user_name = '';
					if($value['has_measure']) {//量体显示量体姓名
						$figureorderm_mod = m('figureorderm');
						
						$figure_info = $figureorderm_mod->get(array(
							'conditions' => " order_id = {$value['order_id']}",
							'fields'     => 'liangti_id',
						));
						if($figure_info['liangti_id']) {
							$member_mod = m('member');
							$figure_member = $member_mod->get($figure_info['liangti_id']);
							$user_name = $figure_member['real_name'] . "(" .  $figure_member['phone_mob'] . ")";
						} 
						
					} else {//标准尺码显示收获信息中用户名
						$user_name = $value['ship_name'] . "(" .  $value['ship_mobile'] . ")";
					}
				  $user_name = $value['ship_name'] . "(" .  $value['ship_mobile'] . ")";
	              $list_return[$value['order_id']]['order_sn'] =  $value['order_sn'];
	              $list_return[$value['order_id']]['add_time'] =  $value['add_time'];
	              $list_return[$value['order_id']]['user_name'] =  $user_name;
	              $list_return[$value['order_id']]['order_id'] =  $value['order_id'];
	              $list_return[$value['order_id']]['final_amount'] =  $value['final_amount'];
	              $list_return[$value['order_id']]['status'] =  $value['status'];
	              $statusname = "";
	              if ($order_val[$value['status']])
	              {
					  if($status == ORDER_PRODUCTION || $status == ORDER_STOCKING ) {
						 $status_code = ORDER_PRODUCTION;
						$statusname = $order_val[$status_code]; 
					  }else{
						$statusname = $order_val[$value['status']];
					  }
	              }
	              $list_return[$value['order_id']]['statusname'] =  $statusname;
	               
	              $tmp['img_url'] = $value['goods_image'];
	              $tmp['type'] = 'suit';
	              $tmp['goods_name'] = $value['goods_name'];
	              $tmp['goods_id'] = $value['goods_id'];
	              $tmp['quantity'] = $value['quantity'];
	              $tmp['price'] = $value['price'];
	              if ($list_suit_size[$value['goods_id']]['size'] == 'diy') 
	              {
	                  $tmp['szie'] = '量体定制';
	              }
	              $tmp['szie'] = $list_suit_size[$value['goods_id']]['size'];
                  $dis_ident = $value['dis_ident'];
                  $dis_ident_list = $order_goods_mod->find(array(
                      'conditions' => "dis_ident = '$dis_ident' ",
                      'fields' => "rec_id,dis_ident",
                  ));
                  $ids = i_array_column($dis_ident_list, 'rec_id');
                  $ids_str = implode(",", $ids);
                  $tmp['order_goods_id'] = $ids_str;
	              
	              $list_return[$value['order_id']]['item'][] =  $tmp;
				 
				//判断此订单是否已评论完毕(已完成)
				$list_return[$value['order_id']]['comment_status'] = 0;
				if($value['status'] == ORDER_FINISHED) {
					$order_suit = $order_suit_mod->findAll(array(
						"conditions" => "comment = 0 AND order_id = " . $value['order_id'],
					));
					$order_goods = $order_goods_mod->findAll(array(
						"conditions" => "comment = 0 AND goods_id = 0 AND order_id = " . $value['order_id'],
					));
					
					if($order_goods || $order_suit) {
						$list_return[$value['order_id']]['comment_status'] = 1;
					}
				}
				
				//=====延期收获  START=====
				$list_return[$value['order_id']]['ship_delay'] = 0;
				if($value['status'] == ORDER_SHIPPED ) {
					$ship_delay = $this->do_delay($value);
					$list_return[$value['order_id']]['ship_delay'] = $ship_delay['ship_delay'];
				}
				//=====延期收获  END=====
	          }
	      }
	      
          usort($list_return, function($a, $b) {
              $al = $a['order_id'];
              $bl = $b['order_id'];
              if ($al == $bl)
                  return 0;
              return ($al > $bl) ? -1 : 1;
          });

         $this->result = $list_return;
         return $this->sresult();
	  }
	  
	  
	  /**
	   *订单列表
	   *@author liang.li <1184820705@qq.com>
	   *@2015年6月4日
	   *@url
	   */
	  //11.26加订单待评价shao
	  //http://local.soaapi.mfd.com/soap/profit.php?act=orderList_v2&token=eda6b5ebb68702e0e49841cb9aeb50d3&status=40&orsuit=0
	  function orderList_v2($data)
	  {
	  	  $orsuit=isset($data->orsuit) ? $data->orsuit : 0;
	      $token = isset($data->token) ? $data->token : '';
	      $status = isset($data->status) ? $data->status : -1;
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      if(!in_array($orsuit,array(0,1))){
	      	$this->msg = 'orsuit参数错误';
	      	return $this->eresult();
	      }
	     
	      $user_id = $user_info['user_id'];
	       
	      $order_val = include PROJECT_PATH.'includes/data/config/order.php';
	      $order_goods_mod = m('ordergoods');
	      $order_mod = m('order');
	      $order_suit_mod = m('ordersuit');
	      $fabirc_mod = m('fabric');
	      $orderrefund_mod  = & m('orderrefund');
	      $mOsd = m('ordershipdelay');
	      $model_setting = &af('settings');
	      $setting = $model_setting->getAll(); // 载入系统设置数据
	      $conditions = "user_id = $user_id AND extension = 'news' ";
	     
	      if ($status != -1)
	      {
	          //已付款、生产中、已发货 将已将订单状态改为“退款中”,如果查询这三种状态，则将退款中也一并查询
	          $status  = intval($status);
	          if($status == ORDER_WAITFIGURE ){//待量体、已支付、退款中
	  
	              $conditions .= " AND status IN (".ORDER_WAITFIGURE.")";
	  
	          } elseif($status == ORDER_PRODUCTION) { //成产中、备货中、退款中
	              	
	              $conditions .= " AND status IN (".ORDER_PRODUCTION.",".ORDER_ACCEPTED.",".ORDER_STOCKING.",".ORDER_TUIKUANZHONG.")";
	  
	          } elseif($status == ORDER_FINISHED) { //已完成、返修中、已退款
	              	
	              $conditions .= " AND status IN (".ORDER_FINISHED.",".ORDER_REPAIR.",".ORDER_YINGTUIKUAN.")";
	  
	          } elseif($status == ORDER_SHIPPED) { //已发货、退款中
	              	
	              $conditions .= " AND status IN (".ORDER_SHIPPED.",".ORDER_TUIKUANZHONG.")";
	  
	          } else {
	              $conditions .= " AND status = $status";
	          }
	      }
	      else
	      {
	          $conditions .= "  ";
	      }
	      if($orsuit ==1){
	      	$order_st = $order_mod->find(array(
	      			'conditions' => $conditions,
	      			'order'     => "order_id DESC",
	      			'index_key' => "",
	      	));
	      	$order_ds = i_array_column($order_st, 'order_id');
	      	$conditions_ct = db_create_in($order_ds,'order_id');
	      	//=====  获取评论状态   =====
	      	$order_suit_comment = $order_suit_mod->find(array(
	      			"conditions" => "comment = 0 AND type= 'suit' AND ".$conditions_ct,
	      			"index_key"  => "order_id",
	      			'fields'     => "comment,order_id",
	      	));
	      	$order_goods_comment = $order_goods_mod->find(array(
	      			"conditions" => "comment = 0 AND type= 'diy' AND ".$conditions_ct,
	      			'index_key'  => "order_id",
	      			'fields'     => "comment,order_id",
	      	));
	      	$order_comments=array_merge($order_suit_comment,$order_goods_comment);
	      	$order_ids = i_array_column($order_comments, 'order_id');
	      	$conditions = db_create_in($order_ids,'order_alias.order_id');
	      	$conditions_comment = db_create_in($order_ids,'order_id');
	      }
	      
	      $order_list = $order_mod->find(array(
	         'conditions' => $conditions,
	          'limit'     => $limit,
	          'order'     => "order_id DESC",
	          'index_key' => "",
	      ));
	      
// 	return $order_list;
      if($orsuit !=1){
    	$order_ids = i_array_column($order_list, 'order_id');
    	$conditions = db_create_in($order_ids,'order_alias.order_id');
    	$conditions_comment = db_create_in($order_ids,'order_id');
      }
	       
	      $list_goods = $order_goods_mod->find(array(
	          'conditions' => $conditions ,
	          'join' => "belongs_to_order",
	          'fields' => "order_goods.fabric,goods_name,type,suit_id,quantity,price,dis_ident,order_alias.order_id,user_name,order_sn,goods_image,size,status,final_amount,add_time,ship_time,ship_mobile,ship_name,has_measure",
	          'order' => "order_alias.order_id DESC",
	      ));
	      $list_suit = $order_suit_mod->find(array(
	          'conditions' => $conditions . " AND type = 'suit' ",
	          'join' => "belongs_to_order",
	          'fields' => "price,order_alias.order_id,user_name,order_sn,goods_image,goods_name,price,dis_ident,goods_id,quantity,status,final_amount,add_time,ship_time,ship_mobile,ship_name,has_measure",
	          'order' => "order_alias.order_id DESC",
	      ));
	      //=====  后台允许返修  有数据则显示返修入口 没有数据： status 0:显示返修入口   1： =====
	      $_order_serve_mod = & m('orderserve');
	      $fx_list = $_order_serve_mod->find(array(
	          'conditions'=>"type=1 AND ".$conditions_comment,
	          'index_key' => "order_id",
	      ));
	      
	      //=====  格式化order_suit数据  =====
	      $suit = array();
	      if ($list_suit) 
	      {
	          foreach ($list_suit as $key => $value) 
	          {
	              $item_suit['goods_name'] = $value['goods_name'];
	              $item_suit['goods_id'] = $value['goods_id'];
	              $item_suit['goods_image'] = $value['goods_image'];
	              $item_suit['type'] = 'suit';
	              $item_suit['quantity'] = $value['quantity'];
	              $item_suit['price'] = $value['price'];
	              $suit[$value['order_id']][$value['goods_id']] = $item_suit;
	          }
	      }
	      $item_diy = array();
	      $item_list = array();
	      $order_goods_id = array();
	      $order_goods_list = array();
	      $size = array();
	      if ($list_goods)
	      {
	      
	          $fabirc_arr = array();
	          foreach ($list_goods as $key => $value)
	          {
	              $order_id = $value['order_id'];
	              $suit_id = $value['suit_id'];
	              $item['type']    = $value['type'];
	              $item['goods_name'] = $value['goods_name'];
	              $item['goods_id'] = $suit_id;
	              $item['quantity'] = $value['quantity'];
	              $item['img_url'] = $value['goods_image'];
	              $item['order_id'] = $order_id;
	              $item['dis_ident'] = $value['dis_ident'];
	              
	              if ($value['type'] == 'diy')
	              {
	                  $item['price'] = $value['price'];
	                  $item['fabric'] = $value['fabric'];
	                  $item['order_goods_id'] = $key;
	                  $fabirc_arr[] = $value['fabric'];
	                  if ($value['size'] == 'diy')
	                  {
	                      $item['size'] = '量体定制';
	                  }
	                  else 
	                  {
	                      $item['size'] = $value['goods_name'].":".$value['size'];
	                  }
	                  $item_diy[$key] = $item;
	              }
	              else
	              {
	                  $item['goods_name'] = $suit[$order_id][$suit_id]['goods_name'];
	                  $item['price'] = $suit[$order_id][$suit_id]['price'];
	                  $item['img_url'] = $suit[$order_id][$suit_id]['goods_image'];
	                  $size[$value['order_id']][$value['dis_ident']][$value['goods_name']] = $value['size'];
	                  $order_goods_id[$value['order_id']][$value['dis_ident']][] = $key;
	                  $item_list[$value['order_id']][$value['dis_ident']] = $item;
	              }
	          }
	          //=====  格式化$item_list (是suit数据 一定会有dis_ident字段) 获得order_goods_id 和size字段  =====
	          if ($item_list)
	          {
	              foreach ($item_list as $key => &$value)
	              {
	                  
	                  if ($size[$key]) //=====  拼接尺码字符串  =====
	                  {
	                      foreach ($size[$key] as $key1 => &$value1) 
	                      {
	                          $size_str = "";
	                          $order_goods_id_str = implode(",", $order_goods_id[$key][$key1]);
	                          if ($value1) 
	                          {
	                             foreach ($value1 as $key2 => $value2) 
	                             {
	                                 if ($value2 == 'diy')//=====  量体定制  =====
	                                 {
	                                     $size_str = "量体定制";
	                                     break 1;
	                                 }
	                                 else
	                                 {
	                                     $size_str .= $key2.":".$value2." ";
	                                 }
	                             }
	                          }
	                          $item_list[$key][$key1]['size'] = trim($size_str);
	                          $item_list[$key][$key1]['order_goods_id'] = $order_goods_id_str;
	                      }
	                  }
	                  $order_goods_list[$key] = $value;
	              }
	          }
	          //=====  格式化item_diy取得面面料名称  =====
	          if ($fabirc_arr && $item_diy)
	          {
	              $fabirc_code_arr = i_array_column($item_diy, 'fabric');
	              $fabirc_arr_str = db_create_in($fabirc_arr,'CODE');
	              $fabric_list = $fabirc_mod->find(array(
	                  'conditions' => $fabirc_arr_str,
	                  'index_key' => "CODE",
	              ));
	              foreach ($item_diy as $key => &$value)
	              {
	                  $value['goods_name'] = $value['goods_name'].$fabric_list[$value['fabric']]['tname'];
	                  unset($value['fabric']);
	                  $order_goods_list[$value['order_id']][$key] = $value;
	              }
	          }
	      }
//           $order_goods_list = array_values($order_goods_list);
	      //=====  组装返回数据格式  =====
	      $list_return = array();
	      if ($order_list) 
	      {
	          //=====  获取评论状态   =====
	          $order_suit_comment = $order_suit_mod->find(array(
	              "conditions" => "comment = 0 AND type= 'suit' AND ".$conditions_comment,
	              "index_key"  => "order_id",
	              'fields'     => "comment,order_id",
	          ));
	          $order_goods_comment = $order_goods_mod->find(array(
	              "conditions" => "comment = 0 AND type= 'diy' AND ".$conditions_comment,
	              'index_key'  => "order_id",
	              'fields'     => "comment,order_id",
	          ));
	          foreach ($order_list as $key => $value) 
	          {
	              $return = array();
	              $return['order_sn'] = $value['order_sn'];
	              $return['add_time'] = $value['add_time'];
	              $user_name = $value['ship_name'] . "(" .  $value['ship_mobile'] . ")";
	              $return['user_name'] = $user_name;
	              $return['order_id'] = $value['order_id'];
	              $return['final_amount'] = _format_price_int($value['final_amount']);
	              $return['order_amount'] = _format_price_int($value['order_amount']);
	              $return['status'] = $value['status'];
	              $return['waybillno'] = $value['waybillno']?$value['waybillno']:'';
	              $return['add_time'] = $value['add_time'];
	              $statusname = $order_val[$value['status']]?$order_val[$value['status']]:'';
	              $return['statusname'] = $statusname;
	              
	              //=====  是否显示返修入口  =====
	              $return['is_repair'] = 0;
	              if ($fx_list[$value['order_id']]) 
	              {
	                  $return['is_repair'] = 1;
	              }
	              $return['repair_status'] = '';
	              $return['repair'] =  $fx_list[$value['order_id']]['status']?$fx_list[$value['order_id']]['status']:0;
	              $return['sign'] =  $fx_list[$value['order_id']]['sign']?$fx_list[$value['order_id']]['sign']:0;
	              $return['free'] =  $fx_list[$value['order_id']]['free']?$fx_list[$value['order_id']]['free']:0;
	              if ($fx_list[$value['order_id']]['status'] >=1 && $fx_list[$value['order_id']]['status'] < 12)
	              {
	                  $return['repair_status'] = '返修中';
	              }
	              elseif ($fx_list[$value['order_id']]['status'] == 12)
	              {
	                  $return['repair_status'] = '返修完成';
	              }
	              else 
	              {
	                  $return['repair_status'] = '未开始返修';
	              }
	              
	              //=====  判定评论状态  =====
	             
	              $return['comment_status'] = 0;
	              if ($value['status'] == ORDER_FINISHED) 
	              {
	                  if ($order_suit_comment[$value['order_id']] || $order_goods_comment[$value['order_id']])
	                  {
	                  	
	                      $return['comment_status'] = 1;
	                  }/* else{
	                  	if($orsuit ==1)
	                  	{
	                  		$set_int[]=$value['order_id'];
	                  	}
	                  	
	                  } */
	              }
	             
	              //=====延期收获  START=====
	              $return['ship_delay'] = 0;
	              if($value['status'] == ORDER_SHIPPED ) {
	                  $ship_delay = $this->do_delay($value);
	                  $return['ship_delay'] = $ship_delay['ship_delay'];
	              }
	              //=====延期收获  END=====
	              
	              $return['item'] = array_values($order_goods_list[$value['order_id']]);
	              $list_return[$value['order_id']] = $return;
	          }
	      }
	      /* if($set_int){
	      	foreach($set_int as $k=>$v)
	      	{
	      		 unset($list_return[$v]); 
	      	}
	      	 
	      } */
	     $this->result = array_values($list_return);
	     return $this->sresult();     
	  }
	  
	/**
	  *延期收获
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	function do_delay($value) {
		$mOsd = m('ordershipdelay');
		$model_setting = &af('settings');
		$setting    = $model_setting->getAll(); // 载入系统设置数据
		$now_time   = time();
		$data = array('ship_delay'=>0, 'mes'=>'');
		
		$delay_list  = $mOsd->findAll(array(
			'conditions' => "order_id=" . $value['order_id'],
			'order'      => 'delay_time desc',
			'count'      => true,
			'index_key'	 => '',
		));
		
		$count = $mOsd->getCount();//获取统计的数据
		
		if($delay_list) {//如果有操作延期收获，则使用延期后的时间比较
			$delay_time_total = 0;
			foreach($delay_list as $val) {
				$delay_time_total = $delay_time_total + $val['delay_days'];
			}
			
			//收货操作按钮出现时间：发货时间+延期收货操作按钮出现时间+加上多次延长的时间
			$after     = $value['ship_time'] + $setting['ship_days']*86400 + $delay_time_total*86400;
			//系统自动确认时间：发货时间+自动确认收货时间（15天）+加上多次延长的时间
			$auto_time = $value['ship_time'] + 15*86400 + $delay_time_total*86400;
			//已发货后确认收货提示语
			$mes   = '您已延期收货，请在 '.date('Y-m-d H:s', $auto_time).' 前来确认收货,超时订单将自动确认收货';
			
		} else {//已发货后的第10天，可以出现 延期收货 按钮，15天后自动确认收获
			$after     = $value['ship_time'] + $setting['ship_days']*86400;	
			$auto_time = $value['ship_time'] + 15*86400;	
			$mes = '请在 '.date('Y-m-d H:s', $auto_time).' 前来确认收货,超时订单将自动确认收货';			
		}
		
		if($now_time >= $after && $value['status'] == ORDER_SHIPPED && $count < 2 ) {
			$data['ship_delay'] = 1;
		}
		$data['mes'] =  $mes;
		
		return $data;
	}
	  
	  
	  /**
	  *订单详情
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	  function orderInfo($data) 
	  {
	      
	      $token = isset($data->token) ? $data->token : '';
	      $orderId = isset($data->orderId) ? intval($data->orderId) : 0;
	     
	      if (!$orderId) 
	      {
	          $this->eresult();
	      }
	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      
	      $user_id = $user_info['user_id'];
	      $order_goods_mod = m('ordergoods');
	      $order_suit_mod = m('ordersuit');
	      $order_debit_mod = m('orderdebit');
	      $serve_mod = m('serve');
	      
	      $order_mod = m('order');
	      $order_figure_mod = m('orderfigure');
	      
	      $order_info = $order_mod->get(array(
	         'conditions' => $orderId,
	          'fields'    => "*",
	      ));
	      if ($order_info['user_id'] != $user_id) 
	      {
	          $this->msg = '此订单不存在 或你无权查看';
	          return $this->eresult();
	      }
	      $debit_info = $order_debit_mod->get(array(
	          'conditions' => "order_id = $orderId",
	          'fields' => "sum(d_money) as d_money",
	      ));
	      $order_info['debit'] = "";
	      if ($debit_info) 
	      {
	          $order_info['debit'] = $debit_info['d_money']; //=====  抵用券总的钱数  =====
	      }
	      
	      //=====  发票信息  =====
	      if ($order_info['invoice_need']) 
	      {
	          if ($order_info['invoice_com'] == 3) 
	          {
	              $order_info['invoice_title'] = json_decode($order_info['invoice_title'],true);
	          }
	      }
	      
	      
	      
	      $order_figure_info = $order_figure_mod->get("order_id = $orderId");
	      $order_figure = array();
	      if ($order_figure_info) 
	      {
	          $figure_state = 0;
	          if ($order_figure_info['lw']) 
	          {
	              $figure_state = 1;
	          }
	          //=====  量体项  =====
	          $json = file_get_contents(PROJECT_PATH.'includes/data/config/size_json/figure.json');
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
	                      if ($figure_state) 
	                      {
	                          $tmp['value'] = $value1['name'];
	                      }
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
	                      if ($figure_state) 
	                      {
	                          $tmp2['value'] = $value1['name'];
	                      }
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
	              $tmp3['value'] = $val."cm";
	              $figure_item[] = $tmp3;
	          }
	           
	          $custom_item = array();//=====  客户信息  =====
	          $custom_item['realname'] = $order_figure_info['realname'];
	          $custom_item['gender'] = $order_figure_info['gender'];
	          $custom_item['phone'] = $order_figure_info['phone'];
	          $custom_item['area'] = $order_figure_info['area'];
	          $custom_item['lheight'] = $order_figure_info['lheight']."cm";
	          $custom_item['lweight'] = $order_figure_info['lweight']."kg";
	          if ($order_figure_info['server_id'] && !$order_figure_info['area']); 
	          {
	              $serve_info = $serve_mod->get_info($order_figure_info['server_id']);
	              $region_mod = m('region');
	              $region_info = $region_mod->get_info($serve_info['region_id']);
	              $custom_item['area'] = $region_info['region_name'];
	          }
	          
	          
	          $order_figure['special'] = $special;
	          $order_figure['style'] = $style;
	          $order_figure['figure'] = $figure_item;
	          $order_figure['custom'] = $custom_item;
	      }
	      //=====  商品相关  =====
	      $order_goods_list = $order_goods_mod->find(array(
	          'conditions' => "order_id = $orderId",
	          'index_key'  => "",
	      ));
	      
	      $params_diy = i_array_column($order_goods_list, 'params');
	      $embs_diy = i_array_column($order_goods_list, 'embs');
          $params_ids = "";
          $embs_ids   = "";
          
          if ($params_diy) 
          {
              foreach ($params_diy as $key => $value) 
              {
                  $arr = json_decode($value,true);
                  if ($arr) 
                  {
                      foreach ($arr as $key1 => $value1) 
                      {
                          $params_ids .= $key1.",";
                          $value1_val = explode("|", $value1);
                          if (count($value1_val) > 1) 
                          {
                              $params_ids .= $value1_val[0].",";
                          }
                          else 
                          {
                              $params_ids .= $value1.",";
                          }
                      }
                  }
              }
          }
          
          if ($embs_diy)//===== embs字段 刺绣相关的  =====
          {
              foreach ($embs_diy as $key => $value)
              {
                  $arr = json_decode($value,true);
                  if ($arr)
                  {
                      foreach ($arr as $key1 => $value1)
                      {
                          if (!$key1) 
                          {
                              continue;
                          }
                          $embs_ids .= $key1.",";
                          if (is_array($value1))
                          {
                              $value1 = array_unique($value1);
                              foreach ($value1 as $key5 => $value5) 
                              {
                                  $embs_ids .= $value5.",";
                              }
                          }
                          else
                          {
                              if (is_numeric($value1))
                              {
                                  $embs_ids .= $value1.",";
                              }
                          }
                      }
                  }
              }
          }
          
          $params_ids = trim($params_ids.$embs_ids,",");
          
	      $order_suit_list = $order_suit_mod->find(array(
	          'conditions' => "order_id = $orderId",
	          'index_key'  => "goods_id",
	      ));
	      
	      
	      $dict_mod = m('dict');
	      $dict_mod1 = m('dict1');
	      $dict_embs_parent_mod = m('dictembsparent');
	      $dict_embs_mod = m('dictembs');
	      
	      $dict_list = array();
	      $dict_list1 = array();
	      if ($params_ids) 
	      {
	          $dict_list = $dict_mod->find(array(
	              'conditions' => "id IN ($params_ids)",
	          ));
	          $dict_list1 = $dict_mod1->find(array(
	              'conditions' => "ID IN ($params_ids)"
	          ));
	      }
	      $pinlie = include PROJECT_PATH.'includes/data/config/pinlei.php';
	      if ($order_goods_list) 
	      {
	          foreach ($order_goods_list as $key => $value) 
	          {
				  //套装处理(app在评论时，都是发送goods_id,当是套装时goods_id赋值为suit_id)
				  if($value['type'] == 'suit') {
					 $order_goods_list[$key]['goods_id'] = $value['suit_id'];
				  }
				  
			  
	              //=====    类型   =====
	              $order_goods_list[$key]['cloth_name'] = $pinlie[$value['cloth']];
	              
	              //=====  工艺相关  =====
	              $params = json_decode($value['params'],true);
	              $order_goods_list[$key]['parmas_value'] = "";
	              if ($params) 
	              {
	                  $tmp = array();
	                 foreach ($params as $key1 => $value1) 
	                 {
	                     $value1_val = explode("|", $value1);
	                     
	                     
	                     if ($value['type'] == 'diy') 
	                     {
	                         $a['p_name'] = $dict_list[$key1]['name'];
	                         
	                         if (count($value1_val) > 1)
	                         {
	                             if (strpos($dict_list[$value1_val[0]]['name'], $value1_val[2]) !== false) 
	                             {
	                                  $a['s_name'] = $dict_list[$value1_val[0]]['name'];
	                             }
	                             else 
	                             {
	                                 $a['s_name'] = $dict_list[$value1_val[0]]['name'].$value1_val[2];
	                             }
	                            
	                         }
	                         else 
	                         {
	                             $a['s_name'] = $dict_list[$value1]['name'];
	                         }
	                     }
	                     else 
	                     {
	                         $a['p_name'] = $dict_list1[$key1]['NAME'];
	                         if (count($value1_val) > 1)
	                         {
	                             
	                             $a['s_name'] = $dict_list1[$value1_val[0]]['NAME'].$value1_val[2];
	                         }
	                         else
	                         {
	                             $a['s_name'] = $dict_list1[$value1]['NAME'];
	                         }
	                     }
	                     
	                     $tmp[] = $a;
	                 }
	                 $order_goods_list[$key]['parmas_value'] = $tmp;
	                 unset( $order_goods_list[$key]['params']);
	                
	              }
	              
	              //=====  刺绣相关  =====
	             $embs = json_decode($value['embs'],true);
	             $order_goods_list[$key]['embs_value'] = "";
	             
	             if ($embs) 
	             { 
	                 $tmp = array();
	                 foreach ($embs as $key3 => $value3) 
	                 {
	                     if (!is_array($value3)) 
	                     {
	                         //=====  如果statusid字段值为10008 则说明是刺绣内容   =====
	                         if ($dict_list[$key3]['statusid'] == 10008) 
	                         {
	                             $a['p_name'] = $dict_list[$key3]['name'];
	                             $a['s_name'] = $value3;
	                         }
	                         else
	                         {
	                             $a['p_name'] = $dict_list[$key3]['name'];
	                             $a['s_name'] = $dict_list[$value3]['name'];
	                         }
	                     }
	                     else 
	                     {
	                         $a['p_name'] = $dict_list[$key3]['name'];
	                         $s_name = "";
	                         if ($value3) 
	                         {
	                             foreach ($value3 as $key4 => $value4) 
	                             {
	                                 $s_name .= $dict_list[$value4]['name'].",";
	                             }
	                         }
	                         $s_name = trim($s_name,",");
	                         $a['s_name'] = $s_name;
	                     }
	                     $tmp[] = $a;
	                 }
	                 $order_goods_list[$key]['embs_value'] = $tmp;
	             }
	             
	             //=====  套装处理  =====
	             if ($value['type'] == 'suit')
	             {	
					$dis_ident_list = $order_goods_mod->find(array(
						'conditions' => "order_id = $orderId and dis_ident =  '".$value['dis_ident']."'",
                        'fields'     => "rec_id,dis_ident",
					));
					$ids = i_array_column($dis_ident_list, 'rec_id');
					$ids_str = implode(",", $ids);
					$order_goods_list[$key]['order_goods_id'] = $ids_str;
					
	                 $order_goods_list[$value['dis_ident']]['item'][] = $order_goods_list[$key];
					 $order_goods_list[$value['dis_ident']]['comment'] = $order_suit_list[$value['suit_id']]['comment']; 
	                 $order_goods_list[$value['dis_ident']]['goods_name'] = $order_suit_list[$value['suit_id']]['goods_name'];
	                 $order_goods_list[$value['dis_ident']]['goods_image'] = $order_suit_list[$value['suit_id']]['goods_image'];
	                 $order_goods_list[$value['dis_ident']]['price'] = $order_suit_list[$value['suit_id']]['price'];
	                 $order_goods_list[$value['dis_ident']]['quantity'] = $order_suit_list[$value['suit_id']]['quantity'];
	                 $order_goods_list[$value['dis_ident']]['size'] = $value['size'];;
	                 unset($order_goods_list[$key]);
	             }
	             else 
	             {
					 //$order_goods_list[$key]['order_goods_id'] = $order_goods_list[$key]['rec_id'];
					 
	                 $order_goods_list[$value['rec_id']]['item'][] = $order_goods_list[$key];
	                 $order_goods_list[$value['rec_id']]['goods_name'] = $value['goods_name'];
					 $order_goods_list[$value['rec_id']]['comment'] = $value['comment']; 
	                 $order_goods_list[$value['rec_id']]['goods_image'] = $value['goods_image'];
	                 $order_goods_list[$value['rec_id']]['price'] = $value['subtotal'];
	                 $order_goods_list[$value['rec_id']]['quantity'] = $value['quantity'];
	                 $order_goods_list[$value['rec_id']]['size'] = $value['size'];
	                 unset($order_goods_list[$key]);
	             }
	              
	          }
	      }

			//判断此订单是否已评论完毕(已完成)
			$order_info['comment_status'] = 0;
			if($order_info['status'] == ORDER_FINISHED) {
				$order_suit = $order_suit_mod->findAll(array(
					"conditions" => "comment = 0 AND order_id = " . $orderId,
				));
				$order_goods = $order_goods_mod->findAll(array(
					"conditions" => "comment = 0 AND goods_id = 0 AND order_id = " . $orderId,
				));
				if($order_goods || $order_suit) {
					$order_info['comment_status'] = 1;
				}
			}
			
			//订单类型
			if($order_info['is_gift']) {
				$order_info['order_type_name'] = '礼品';
			} else {
				$order_info['order_type_name'] = '普通(非礼品)';
			}
			
			//=====延期收货提示语  START=====
			$order_info['delay_info'] = '';
			if($order_info['status'] == ORDER_SHIPPED ) {
				$do_delay = $this->do_delay($order_info);
				$order_info['delay_info'] = $do_delay['mes'];
			}
			//=====延期收货提示语  END=====

	      $this->result['order_info'] = $order_info;
	      $this->result['order_figure'] = $order_figure;
	      $this->result['order_goods_list'] = array_values($order_goods_list);
// 	 dump($this->result);
	     return $this->sresult();     
	      
	  }
	  
	  /**
	   * 首单价格
	   * @param unknown $userid
	   * @return multitype:|multitype:unknown
	   */
	  function processPrice($userid){
	      if(!$userid) return array();
	      $mod_first = m("orderfirstlog");
	      $list = $mod_first->find(array(
	          "conditions" => "user_id='{$userid}'  AND is_active=1",
	      ));
	       
	      $aData = array();
	       
	      foreach((array) $list as $key => $val){
	          $aData[] = $val["cloth"];
	      }
	       
	      return $aData;
	  }
	  
	  
	  /**
	  * 量体项
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月5日
	  */
	    /* 量体信息各部位数据 */
    function _positions()
    {
    
        $return = array(
             'lw' => array('zname'=>'颈围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'xw' => array('zname'=>'胸围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
//             'fw' => array('zname'=>'腹围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            'zyw' => array('zname'=>'中腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            //'stw' => array('zname'=>'上臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'sbw' => array('zname'=>'上臂围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'zjk' => array('zname'=>'肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qjk' => array('zname'=>'前肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'zww' => array('zname'=>'左腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            'yww' => array('zname'=>'右腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            /* 'zxc' => array('zname'=>'左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'yxc' => array('zname'=>'右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyc' => array('zname'=>'后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'), */
            //
            'yw' => array('zname'=>'腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'tw' => array('zname'=>'臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'tgw' => array('zname'=>'腿根围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'td' => array('zname'=>'通裆','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qyg' => array('zname'=>'前腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyg' => array('zname'=>'后腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qyj' => array('zname'=>'前腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyjc' => array('zname'=>'后腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'zkc' => array('zname'=>'左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'ykc' => array('zname'=>'右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'xiw' => array('zname'=>'膝围','isshow'=>0,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'jk' => array('zname'=>'脚口','isshow'=>0,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            //'dxzxc' => array('zname'=>'短袖左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            //'dxyxc' => array('zname'=>'短袖右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
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
	  

    /**
    *收益提现
    *@author liang.li <1184820705@qq.com>
    *@2015年7月24日
    */
    function profitCash($data) 
    {
        $token = isset($data->token) ? $data->token : '';
        $bank_id =  isset($data->bank_id) ? $data->bank_id : 0;
        $cash_money = isset($data->cash_money) ? $data->cash_money : 0;
        $cash_limit = 500;
        $cash_tax = 0.2;
        
        
        $user_info = getUserInfo($token);
        $user_id = $user_info['user_id'];
        if (!$user_info)
        {
            return $this->tresult();
        }
        
        //判断次用户是否已认证，认证后方可提现
        if(empty($user_info['df_bankcard'])) 
        {
            $this->msg = "您还未绑定银行卡，暂时不能提现！";
            return $this->eresult();
        }
        
        //根据选择银行卡id，获得银行信息
        $member_bank = m('member_bank');
        $selectbank  = $member_bank->get($bank_id);
        if (!$selectbank) {
            $this->msg = "请选择银行卡";
            return $this->eresult();
        }
        
        $_data['bank_address'] = $selectbank['bank_address'];
        $_data['bank_id']      = $selectbank['bank_id'];
        $_data['bank_card']    = $selectbank['bank_card'];
        
        if ($cash_money < 500) 
        {
            $this->msg = '提现金额必须大于500';
            return $this->eresult();
        }
        //验证提现金额
        if (!$cash_money || $cash_money < 0 ) 
        {
            $this->msg = '金额必须是整数';
            return $this->eresult();
        }
        if ($cash_money > $user_info['profit']) 
        {
            $this->msg = '你的可用资金不足！请重新提交';
            return $this->eresult();
        }
        $_data['cash_money']  = $cash_money;
        $_data['user_id']     = $user_info['user_id'];
        $_data['create_time'] = gmtime();
        $_data['type'] = 1;
        
        $cash_mod   = m('cash');
        $cash_id = $cash_mod->add($_data);
        if (!$cash_id) 
        {
            return $this->eresult();
        }
        
        //=====  冻结资金  =====
        $member_mod = m('member');
        $member_mod->setDec("user_id=$user_id","profit",$cash_money);
        $member_mod->setInc("user_id=$user_id","profit_frozen",$cash_money);
        
        //发后后台消息
        $msg = new Notice();
        $msg->send(array(
            "content" => "创业者-提现申请(ID-".$cash_id."),<a href=\"http://www.dev.mfd.cn/admin/index.php?app=cash&act=edit&id=".$cash_id."\">查看详情</a>",
            'node'    => 'real_auth',
        ));
        /*事务暂时关闭 上线开启
         $transaction = $cash_mod->beginTransaction();
        
         if (!$cash_mod->submit($data)) {
         $cash_mod->rollback();
         $return = array(
         'statusCode' => 0,
         'error' => array(
         'errorCode' => 101,
         'msg'       => '提交申请失败',
         ),
         );
         return $json->encode($return);
         }
         $cash_mod->commit($transaction);
        */
        //组装返回数据
        $this->msg = '成功提交提现申请,款项预计3~5个工作日到账';
        return $this->sresult();
    }
	  
    /**
    *收益提现列表
    *@author liang.li <1184820705@qq.com>
    *@2015年7月24日
    */
    function profitCashList($data) 
    {
        $token = isset($data->token) ? $data->token : '';
        $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	    $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	    $type = isset($data->type) ? $data->type : 100;
	    $limit = $pageSize*($pageIndex-1).','.$pageSize;
	    $cash_tax = 0.2;
	    
	    $user_info = getUserInfo($token);
	    $user_id = $user_info['user_id'];
	    if (!$user_info)
	    {
	        return $this->tresult();
	    }
	    if (!in_array($type, array(0,1,2,100))) 
	    {
	        return $this->eresult();
	    }
	    
	    $cash_mod = m('cash');
	    $conditions = "user_id = $user_id AND type=1 ";
	    if ($type != 100) 
	    {
	        $conditions .= " AND status = $type ";
	    }
	    
	    $list = $cash_mod->find(array(
	        'conditions' => $conditions,
	        'index_key'  => "",
	        'limit'      => $limit,
	    ));
	    
	    if ($list) 
	    {
	        foreach ($list as $key => $value) 
	        {
	            $list[$key]['real_money'] = $value['cash_money']*(1-$cash_tax);
	        }
	    }
	    
	    $this->result = $list;
	    return $this->sresult();
    }
	  
	/**
	*收益列表
	*@author liang.li <1184820705@qq.com>
	*@2015年7月24日
	*/
	function profitList($data) 
	{
	    $token = isset($data->token) ? $data->token : '';
	    $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	    $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	    $type = isset($data->type) ? $data->type : 100;
	    $limit = $pageSize*($pageIndex-1).','.$pageSize;
	    
	    $user_info = getUserInfo($token);
	    $user_id = $user_info['user_id'];
	    if (!$user_info)
	    {
	        return $this->tresult();
	    }
	    
	    $order_cash_log_mod = m('ordercashlog');
	    $contidions = "user_id = $user_id AND type = 0";
	    if ($type != 100) 
	    {
	        if ($type == 0) 
	        {
	            $contidions .= " AND minus = $type";
	        }
	        elseif ($type == 1)
	        {
	            $contidions .= " AND (minus = 1 OR minus = 2)";
	        }
	        
	    }
	    
	    $list = $order_cash_log_mod->find(array(
	        'conditions' => $contidions,
	        'limit'      => $limit,
	        'index_key'  => '',
	    ));
	    
	    $this->result = $list;
	    return $this->sresult();
	    
	}
	  
	
	/**
	*收益转余额
	*@author liang.li <1184820705@qq.com>
	*@2015年8月4日
	*/
	function profitToMoney($data) 
	{
	    $token = isset($data->token) ? $data->token : '';
	    $profit = isset($data->profit) ? $data->profit : 0;
	    $user_info = getUserInfo($token);
	    $user_id = $user_info['user_id'];
	    if (!$user_info)
	    {
	        return $this->tresult();
	    }
	    $m_mod = m('member');
	    $order_cash_mod = m('ordercashlog');
	    
	    if ($profit <= 0  || !is_numeric($profit)) 
	    {
	        $this->msg = '收益必须是整数';
	        return $this->eresult();
	    }
	    
	    $coin = $profit * (ORDER_TAX/100);
	    $money = $profit - $coin;
	    $order_sn = $this->gen_ident();
	    $time = time();
	    $transaction = $m_mod->beginTransaction();//=====  开启事物  =====
	    if ($profit > $user_info['profit']) 
	    {
	        $m_mod->rollback();
	        $this->msg = '收益不足';
	        return $this->eresult();
	    }
	    
	    //=====  mfd币log  =====
	    $_data_coin['add_time'] = $time;
	    $_data_coin['name'] = '收益转余额获得mfd币';
	    $_data_coin['type'] = 3;
	    $_data_coin['share'] = ORDER_TAX;
	    $_data_coin['user_id'] = $user_id;
	    $_data_coin['cash_money'] = $coin;
	    $_data_coin['order_money'] = $profit;
	    $_data_coin['order_sn'] = $order_sn;
	    if (!$order_cash_mod->add($_data_coin)) 
	    {
	        $m_mod->rollback();
	        $this->msg = 'mfd币log记录失败';
	        return $this->eresult();
	    }
	    
	    //=====  收益log  =====
	    $_data_profit['add_time'] = $time;
	    $_data_profit['name'] = '收益转余额扣除收益';
	    $_data_profit['type'] = 0;
	    $_data_profit['minus'] = 2;
	    $_data_profit['share'] = ORDER_TAX;
	    $_data_profit['user_id'] = $user_id;
	    $_data_profit['cash_money'] = $profit;
	    $_data_profit['order_money'] = $profit;
	    $_data_profit['order_sn'] = $order_sn;
	    if (!$order_cash_mod->add($_data_profit)) 
	    {
	        $m_mod->rollback();
	        $this->msg = '收益log记录失败';
	        return $this->eresult();
	    }
	    
	    //=====  余额log  =====
	    $_data_money['add_time'] = $time;
	    $_data_money['name'] = '收益转余额获得余额';
	    $_data_money['type'] = 4;
	    $_data_money['minus'] = 0;
	    $_data_money['share'] = ORDER_TAX;
	    $_data_money['user_id'] = $user_id;
	    $_data_money['cash_money'] = $money;
	    $_data_money['order_money'] = $profit;
	    $_data_money['order_sn'] = $order_sn;
	    if (!$order_cash_mod->add($_data_money))
	    {
	        $m_mod->rollback();
	        $this->msg = '余额log记录失败';
	        return $this->eresult();
	    }
	 
	    
	    if(!$m_mod->setInc($user_id, 'money', $money))
	    {
	        $m_mod->rollback();
	        $this->msg = '添加余额失败';
	        return $this->eresult();
	    }
	    if(!$m_mod->setDec($user_id, 'profit', $profit))
	    {
	        $m_mod->rollback();
	        $this->msg = '扣除收益失败';
	        return $this->eresult();
	    }
	    if(!$m_mod->setInc($user_id, 'coin', $coin))
	    {
	        $m_mod->rollback();
	        $this->msg = '添加mfd币失败';
	        return $this->eresult();
	    }
	    $m_mod->commit($transaction);
	    
	    return $this->sresult();
	}
	  
	function gen_ident($id = 0){
	    $cart_mod = m('ordercashlog');
	    do{
	        $str='abcdefghigklmnopqrstuvwxyz0123456789';
	        $code ='';
	        for($i=0;$i<5; $i++){
	            $code .= $str[mt_rand(0, strlen($str)-1)];
	        }
	        $il = strlen($id);
	        for ($i=$il;$i<10;$i++){
	            $id = '0'.$id;
	        }
	        $ident =  $code.$id;
	    }
	    while($cart_mod->get("order_sn = '{$ident}'"));
	    return $ident;
	}
	  
	 /**
	 * 格式化抵用券
	 *@author liang.li <1184820705@qq.com>
	 *@2015年8月11日
	 */
	 function bugDebit() 
	 {
	     $debit_mod = m('debit');
	     $list = $debit_mod->find();
	     $time = time();
	     foreach ($list as $key => $value) 
	     {
	         if ($value['expire_time'] > $time)
	         {
	             $debit_mod->edit($value['id'],array('is_invalid' => 0));
	         }
	     }
	     $list = $debit_mod->find();
	     dump($list);
	 } 
	 
	 /**
	 *订单管理
	 *@author liang.li <1184820705@qq.com>
	 *@2015年8月18日
	 */
	 function orderCash($data) 
	 {
	     $status = 40;
	     $order_id =  isset($data->order_id) ? $data->order_id : '';
	     if (!$order_id) 
	     {
	         return $this->eresult();
	     }
	     $order_mod = m('order');
	     $member_mod = m('member');
	     $order_info = $order_mod->get_info($order_id);
	     $user_info = $member_mod->get_info($order_info['user_id']);
	     $token = $user_info['user_token'];
	     $res = $this->editOrderStatus($token, $order_id, $status);
	     return $res;
	     
	 }
	 
	 
	 /**
	  * 修改订单状态
	  *
	  * @param string  $token 用户标识；int $order_id 订单id; int status 订单状态
	  *
	  * @access protected
	  * @author liuchao <280181131@qq.com>
	  * @return void
	  */
	 public function editOrderStatus($token, $order_id, $status, $client = '')
	 {
	     global $json;
	     $userinfo = getToken($token);
	     $auth_status = 0;//是否对创业者进行认证弹窗
	 
	     if(!$userinfo) {
	         $return = array(
	             'statusCode' => 0,
	             'error' => array(
	                 'errorCode' => 100,
	                 'msg'       => '密码错误 请重新登陆',
	             ),
	         );
	         return $json->encode($return);
	     }
	     $user_id = $userinfo['user_id'];
	 
	     $order_mod  = m('order');
	     $order_info = $order_mod->get("order_id = {$order_id} and user_id = {$user_id} ");
	 
	     if(!$order_info) {
	         $return = array(
	             'statusCode' => 0,
	             'error' => array(
	                 'errorCode' => 103,
	                 'msg'       => '未发现此订单',
	             ),
	         );
	         return $json->encode($return);
	     }
	 
	     $result = '';
	     //取消--待付款状态下（11）  或者 确认收货--已发货（30）  两种状态下可以修改订单状态
	     if( $order_info['status'] == ORDER_SHIPPED) 
	     {
	         	
	         if ($status == ORDER_FINISHED) 
	         {
	             $behavior = 'update';
	             //用户交易完成，查看是否实名认证条件：当前用户是创业者身份、当前用户没有实名认证、弹三次（次数有app记录）
	             $auth_mod  = m('auth');
	             $auth_info = $auth_mod->get("user_id = $user_id ");
	 
	             if($userinfo['member_lv_id'] > 1 && $auth_info['status'] == 0 ) {
	                 $auth_status = 1;//需要进行弹窗
	             }
	 
	             $transaction = $order_mod->beginTransaction();
	             $res = $order_mod->submit($order_id);
	             // 	return array($res);
	             if (!$res['code'])
	             {
	                 $order_mod->rollback();
	                 $return = array(
	                     'statusCode' => 0,
	                     'error' => array(
	                         'errorCode' => 106,
	                         'msg'       => $res['msg'],
	                     ),
	                 );
	 
	                 return $json->encode($return);
	             }
	             $order_mod->commit($transaction);
	 
	         }
	         //=============交易完成  END==========
	         	
	         //订单取消加入到系统日志
	         imports("orderLogs.lib");
	         $oLogs = new OrderLogs();
	         	
	         $oLogs->_record(array(
	             'order_id' => $order_id,
	             'op_id'    => $order_info['user_id'],
	             'op_name'  => $order_info['user_name'],
	             'behavior' => $behavior,
	             'from'     => $order_info['status'],
	             'to'       => $status,
	             'remark'  => '系统通过脚本任务修改订单',
	             //ip
	         ));
	         	
	         //修改状态
	         $result = $order_mod->edit('order_id='.$order_id , array('status' => $status));
	         	
	         //==============加入站内信  STAART========
	         //判断是否为第一笔订单，发送首单系统消息
	         $one_order_info = $order_mod->findAll(array(
	             'conditions'=>"status in(30,40) and user_id='{$user_id}'",
	         ));
	     } 
	     else 
	     {
	         $return = array(
	             'statusCode' => 0,
	             'error' => array(
	                 'errorCode' => 104,
	                 'msg'       => '此订单状态不可修改！',
	             ),
	         );
	         return $json->encode($return);
	         	
	     }
	     	
	     if($result) 
	     {
	         $return = array(
	             'statusCode' => 1,
	             'result' => array(
	                 'success' => '订单状态修改成功',
	                 'status'      => $status,
	                 'auth_status' => $auth_status,//是否对创业者进行认证弹窗；1：需要  0：不需要
	             )
	         );
	         return $json->encode($return);
	     } 
	     else 
	     {
	         $return = array(
	             'statusCode' => 0,
	             'error' => array(
	                 'errorCode' => 103,
	                 'msg'       => '订单状态修改失败',
	             ),
	         );
	         return $json->encode($return);
	     }
	 
	 }
	 
	 /**
	 *激活酷卡
	 *@author liang.li <1184820705@qq.com>
	 *@2015年10月9日
	 */
	 function activDebit($data) 
	 {
	     $token = isset($data->token) ? $data->token : '';
	     $activSn = isset($data->activSn) ? strtoupper($data->activSn) : '';
	     
	     if (!$activSn)
	     {
	         $this->eresult('缺少激活码参数 ');
	     }
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         return $this->tresult();
	     }
	     $user_id = $user_info['user_id'];
	     $debit_line_mod = m('debitlines');
	     $debit_mod = m('debit');
	     $transaction = $debit_line_mod->beginTransaction();//=====  开启事物  =====
	     $item = $debit_line_mod->get(array(
	         'conditions' => "active_sn = '$activSn' "
	     ));
	     if (!$item) 
	     {
	         $debit_line_mod->rollback();
	         return $this->eresult('此激活码不存在');
	     }
	     if ($item['is_active'] == 1) 
	     {
	         $debit_line_mod->rollback();
	         return  $this->eresult('此卡已激活！请勿重复操作');
	     }
	     $debit_data['debit_name'] = "酷券";
	     $debit_data['debit_sn'] = $item['d_sn'];
	     $debit_data['money'] = $item['money'];
	     $debit_data['user_id'] = $item['user_id'];
	     $debit_data['source'] = 'active';
	     $debit_data['add_time'] = time();
	     $debit_data['cate'] = $item['cate'];
	     $debit_data['expire_time'] = $item['expire_time'];
	     $debit_id = $debit_mod->add($debit_data);
	     if (!$debit_id) 
	     {
	         $debit_line_mod->rollback();
	         return $this->eresult('加入抵用券失败');
	     }
	     if (!$debit_line_mod->edit($item['id'],array('d_id'=>$debit_id,'is_active'=>1))) 
	     {
	         $debit_line_mod->rollback();
	         return $this->eresult('激活卡失败');
	     }
	     
	     $debit_line_mod->commit($transaction);
	     return $this->sresult();
	 }
	 
	 
	
	 
	 /**
	  *订单详情第一步
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	 function orderInfoF($data)
	 {
	     $token = isset($data->token) ? $data->token : '';
	     $orderId = isset($data->orderId) ? intval($data->orderId) : 0;
	     if (!$orderId)
	     {
	         $this->eresult();
	     }
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         return $this->tresult();
	     }
	     $user_id = $user_info['user_id'];
	     $order_goods_mod = m('ordergoods');
	     $order_suit_mod = m('ordersuit');
	     $order_debit_mod = m('orderdebit');
	     $serve_mod = m('serve');
	     $fabirc_mod = m('fabric');
	     $order_mod = m('order');
	     $order_figure_mod = m('orderfigure');
	     $order_logs_mod = m('orderlogs');
	      
	     $order_info = $order_mod->get(array(
	         'conditions' => $orderId,
	         'fields'    => "*",
	     ));
	     $order_info['order_amount'] = _format_price_int($order_info['order_amount']);
	     $order_info['final_amount'] = _format_price_int($order_info['final_amount']);
	     $order_info['goods_amount'] = _format_price_int($order_info['goods_amount']);
	     if ($order_info['user_id'] != $user_id)
	     {
	         $this->msg = '此订单不存在 或你无权查看';
	         return $this->eresult();
	     }
	     
	     //=====  订单备注  =====
	     if (!$order_info['memo']) 
	     {
	         $order_info['memo'] = "无";
	     }
	     
	     
	     $order_val = include PROJECT_PATH.'includes/data/config/order.php';
	     $order_info['statusname'] = $order_val[$order_info['status']];
	     $debit_info = $order_debit_mod->get(array(
	         'conditions' => "order_id = $orderId",
	         'fields' => "sum(d_money) as d_money",
	     ));
	     $order_info['debit'] = "";
	     if ($debit_info)
	     {
	         $order_info['debit'] = isset($debit_info['d_money']) ? $debit_info['d_money'] : '0.00'; //=====  抵用券总的钱数  =====
	     }
	      
	     //=====  发票信息  =====
	     if ($order_info['invoice_need'])
	     {
	         if ($order_info['invoice_com'] == 3)
	         {
	             $order_info['invoice_title'] = json_decode($order_info['invoice_title'],true);
	         }
	     }
	     
	     $conditions = "order_id = $orderId";
	     $list_goods = $order_goods_mod->find(array(
	         'conditions' => $conditions ,
	         'fields' => "son_sn,fabric,goods_name,type,suit_id,quantity,price,dis_ident,order_id,goods_image,size",
	         'order' => "order_id DESC",
	     ));
	     $list_suit = $order_suit_mod->find(array(
	         'conditions' => $conditions . " AND type = 'suit' ",
	         'fields' => "price,order_id,goods_image,goods_name,dis_ident,goods_id,quantity",
	         'order' => "order_id DESC",
	     ));
	     
	     //=====  量体信息获得  =====
	     $measure = array(
	       '2'  => "到店量体",
	       '6'  => "指派量体师",
	       '5'  => "现有量体"
	     );
	     $order_figure_list = array();
	     $son_sn_arr = i_array_column($list_goods, "son_sn");
	     if ($son_sn_arr) 
	     {
	         $son_sn_str = db_create_in($son_sn_arr,"son_sn");
	         $order_figure_list = $order_figure_mod->find(array(
	             'conditions' => $son_sn_str,
	             'index_key'  => 'son_sn', 
	         ));
	     }
	      
	     //=====  后台允许返修  有数据则显示返修入口  =====
	     $_order_serve_mod = & m('orderserve');
	     $fx_list = $_order_serve_mod->get(array(
	          'conditions'=>"type=1 AND order_id=".$orderId,
	          'index_key' => "order_id",
	     ));
	      //=====  是否显示返修入口  =====
	      $order_info['is_repair'] = 0;
	      if ($fx_list)
	      {
	          $return['is_repair'] = 1;
	      }
	      $order_info['repair_status'] = '';
	      $order_info['repair'] =  isset($fx_list['status']) ? $fx_list['status'] : '';
	      if ($fx_list['status'] >=1 && $fx_list['status'] < 8)
	      {
	          $order_info['repair_status'] = '返修中';
	      }
	      elseif ($fx_list['status'] == 10)
	      {
	          $order_info['repair_status'] = '返修完成';
	      }
	      else
	      {
	          $order_info['repair_status'] = '未开始返修';
	      }
	      //=====  判定评论状态  =====
	      $order_suit_comment = $order_suit_mod->get(array(
	          "conditions" => "comment = 0 AND type= 'suit' AND order_id = ".$orderId,
	          'fields'     => "comment,order_id",
	      ));
	      $order_goods_comment = $order_goods_mod->get(array(
	          "conditions" => "comment = 0 AND type= 'diy' AND order_id = ".$orderId,
	          'fields'     => "comment,order_id",
	      ));
	      $order_info['comment_status'] = 0;
	      if ($order_info['status'] == ORDER_FINISHED)
	      {
	          if ($order_suit_comment || $order_goods_comment)
	          {
	              $order_info['comment_status'] = 1;
	          }
	      }
	     
	     
	     //=====  格式化order_suit数据  =====
	     $suit = array();
	     if ($list_suit)
	     {
	         foreach ($list_suit as $key => $value)
	         {
	             $item_suit['goods_name'] = $value['goods_name'];
	             $item_suit['goods_id'] = $value['goods_id'];
	             $item_suit['goods_image'] = $value['goods_image'];
	             $item_suit['type'] = 'suit';
	             $item_suit['quantity'] = $value['quantity'];
	             $item_suit['price'] = _format_price_int($value['price']);
	             $suit[$value['goods_id']] = $item_suit;
	         }
	     }
	     $item_diy = array();
	     $item_list = array();
	     $order_goods_id = array();
	     $order_goods_list = array();
	     $size = array();
	     if ($list_goods)
	     {
	          
	         $fabirc_arr = array();
	         foreach ($list_goods as $key => $value)
	         {
	             $order_id = $value['order_id'];
	             $suit_id = $value['suit_id'];
	             $item['type']    = $value['type'];
	             $item['goods_name'] = $value['goods_name'];
	             $item['goods_id'] = $suit_id;
	             $item['quantity'] = $value['quantity'];
	             $item['img_url'] = $value['goods_image'];
	             $item['order_id'] = $order_id;
	             $item['dis_ident'] = $value['dis_ident'];
	             $item['son_sn']  = $value['son_sn']; 
	             //=====  是否有量体信息  =====
	             $item['is_measure'] = 0;
	             if($order_figure_list[$value['son_sn']] && $value['size'] == 'diy')
	             {
	                 $item['is_measure'] = 1;
	             }
	             
	             if ($value['type'] == 'diy')
	             {
	                 $item['sn'] = $key;
	                 $item['price'] = _format_price_int($value['price']);
	                 $item['fabric'] = $value['fabric'];
	                 $item['order_goods_id'] = $key;
	                 $fabirc_arr[] = $value['fabric'];
	                 if ($value['size'] == 'diy')
	                 {
	                    $measure_name = $measure[$order_figure_list[$value['son_sn']]['measure']];
	                    $real_name    = $order_figure_list[$value['son_sn']]['realname'];
	                    $item['size'] = '量体定制'."(".$measure_name."/".$real_name.")";
	                 }
	                 else
	                 {
	                     $item['size'] = $value['goods_name'].":".$value['size'];
	                 }
	                 $item_diy[$key] = $item;
	             }
	             else
	             {
	                 $item['sn'] = $suit_id;
	                 $item['goods_name'] = $suit[$suit_id]['goods_name'];
	                 $item['price'] = _format_price_int($suit[$suit_id]['price']);
	                 $item['img_url'] = $suit[$suit_id]['goods_image'];
	                 $size[$value['dis_ident']][$value['goods_name']] = $value['size'];
	                 $order_goods_id[$value['dis_ident']][] = $key;
	                 $item_list[$value['dis_ident']] = $item;
	             }
	         }
	         
	         //=====  格式化$item_list 获得order_goods_id 和size字段  =====
	         if ($item_list)
	         {
	             foreach ($item_list as $key => &$value)
	             {
	                 $order_goods_id_str = implode(",", $order_goods_id[$key]);
	                 $size_str = "";
	                 if ($size[$key]) //=====  拼接尺码字符串  =====
	                 {
	                     foreach ($size[$key] as $key1 => $value1)
	                     {
	                         if ($value1 == 'diy')//=====  量体定制  =====
	                         {
	                             $measure_name = $measure[$order_figure_list[$value['son_sn']]['measure']];
	                             $real_name    = $order_figure_list[$value['son_sn']]['realname'];
	                             $size_str = '量体定制'."(".$measure_name."/".$real_name.")";
	                             break 1;
	                         }
	                         else
	                         {
	                             $size_str .= $key1.":".$value1." ";
	                         }
	                          
	                     }
	                 }
	                 $value['size'] = trim($size_str);
	                 $value['order_goods_id'] = $order_goods_id_str;
	                 $order_goods_list[] = $value;
	             }
	         }
	         //=====  格式化item_diy取得面面料名称  =====
	         if ($fabirc_arr && $item_diy)
	         {
	             $fabirc_code_arr = i_array_column($item_diy, 'fabric');
	             $fabirc_arr_str = db_create_in($fabirc_arr,'CODE');
	             $fabric_list = $fabirc_mod->find(array(
	                 'conditions' => $fabirc_arr_str,
	                 'index_key' => "CODE",
	             ));
	             foreach ($item_diy as $key => &$value)
	             {
	                 $value['goods_name'] = $value['goods_name']."(面料:".$fabric_list[$value['fabric']]['tname'].")";
	                 unset($value['fabric']);
	                 $order_goods_list[] = $value;
	             }
	         }
	     }
	     
	     //=====  订单状态  =====
	     $order_log_list = $order_logs_mod->find(array(
	        'conditions' => "order_id = $order_id",
	         'fields'    => "log_text,op_id,alttime",
	         'index_key' => "",
	     ));
	     if ($order_log_list) 
	     {
	         foreach ($order_log_list as $key => &$value) 
	         {
	             $op_name = "系统";
	             if ($value['op_id'] == $user_id) //=====  op_id是自己的话就是客户自己修改 否则是系统  ===== 
	             {
	                 $op_name = "客户";
	             }
	             $value['op_name'] = $op_name;
	         }
	     }
	     
	     //=====  组装返回数据  =====
	     $return['order_info'] = $order_info;
	     $return['order_goods_list'] = $order_goods_list;
	     $return['op_list'] = $order_log_list;
	     $this->result = $return;
	     return $this->sresult();
	 }
	 
	 /**
	  *订单详情(量体相关)
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	 function orderInfoS($data)
	 {
	     $token = isset($data->token) ? $data->token : '';
	     $son_sn = isset($data->son_sn) ? $data->son_sn : '';
	     if (!$son_sn) 
	     {
	        return  $this->eresult('son_sn是必填参数');
	     }
	     $order_figure_mod = m('orderfigure');
	     $serve_mod = m('serve');
	     $order_figure_info = $order_figure_mod->get("son_sn = $son_sn");
	     $order_figure = array();
	     if ($order_figure_info)
	     {
	         $figure_state = 0;
	         if ($order_figure_info['lw'])//=====  通过量体其中一个字段来判断此条量体数据是否完整  =====
	         {
	             $figure_state = 1;
	         }
	         
	         $url = FIGUREURL."/soap/club.php?act=getSpecialFields";
	         $json = file_get_contents($url);
	         $json = json_decode($json,true);
	         $json = $json['result']['data'];
	         $special = array();//=====  特体  =====
	         foreach ($json['public'] as $key => $value)
	         {
	             $tmp['name'] = $value['cateName'];
	             $val = $order_figure_info[$value['value_name']];
	 
	             foreach ($value['item'] as $key1 => $value1)
	             {
	                 $tmp['value'] = '';
	                 if ($val == $value1['value'])
	                 {
	                     if ($figure_state)
	                     {
	                         $tmp['value'] = $value1['name'];
	                     }
	                     break;
	                 }
	             }
	             $special[] = $tmp;
	         }
	         $style = array();//=====  着装风格  =====
	         foreach ($json['special'] as $key => $value)
	         {
	             $tmp2['name'] = $value['cateName'];
	              
	             $val = $order_figure_info[$value['value_name']];
	              
	             foreach ($value['item'] as $key1 => $value1)
	             {
	                 $tmp2['value'] = '';
	                 if ($val == $value1['value'])
	                 {
	                     if ($figure_state)
	                     {
	                         $tmp2['value'] = $value1['name'];
	                     }
	                     break;
	                 }
	             }
	             $style[] = $tmp2;
	         }
            $url = FIGUREURL."/soap/club.php?act=getFigureFields";
            $json = file_get_contents($url);
            $json = json_decode($json,true);
            $json = $json['result']['data'];
	         $figure_item = array();//=====  量体参数  =====
	         foreach ($json as $key => $value)
	         {
	             $tmp3['name'] = $value['zname'];
	             $val = "";
	             if ($order_figure_info[$key])
	             {
	                 $val = $order_figure_info[$key];
	             }
	             $cm = "cm";
	             if ($key == 'lweight') 
	             {
	                 $cm = "kg";
	             }
	             $tmp3['value'] = $val.$cm;
	             $figure_item[] = $tmp3;
	         }
             $time_noon_arr = array('am'=>"上午",'pm'=>"下午");
	         $custom_item = array();//=====  客户信息  =====
	         $serve_info = $serve_mod->get_info($order_figure_info['server_id']);//=====  门店信息  =====
	         $custom_item['realname'] = $order_figure_info['realname'];
	         $custom_item['phone'] = $order_figure_info['phone'];
	         //=====  量体时间  =====
	         $modi_time = "";
	         if ($order_figure_info['modi_time']) 
	         {
	             $modi_time = date("Y年m月d日 H时i分",$order_figure_info['modi_time']);
	         }
	         elseif ($order_figure_info['time'])
	         {
	             $time = strtotime($order_figure_info['time']);
	             $modi_time = date("Y年m月d日",$time)." ".$time_noon_arr[$order_figure_info['time_noon']];
	         }
	         $custom_item['moditime'] = $modi_time;
	         $custom_item['serve_name'] = $serve_info['serve_name'];
             //=====  量体师姓名  =====
             $lianti_name = "";
             if ($order_figure_info['liangti_id']) 
             {
                 $member_mod = m('member');
                 $liangti_info = $member_mod->get_info($order_figure_info['liangti_id']);
                 $lianti_name = $liangti_info['real_name'];
             }
             else 
             {
                 $lianti_name = $serve_info['linkman'];
             }
             $custom_item['liangti_name'] = $lianti_name;
             //=====  量体状态  =====
             $custom_item['liangti_state_name'] = '待量体';
             if ( $order_figure_info['liangti_state'] >= 2 || $order_figure_info['measure'] == 5) 
             {
                 $custom_item['liangti_state_name'] = '已完成';
             }
             $custom_item['is_liangti'] = $figure_state;
	         $order_figure['special'] = $special;
	         $order_figure['style'] = $style;
	         $order_figure['figure'] = $figure_item;
	         $order_figure['custom'] = $custom_item;
	     }
	     $this->result = $order_figure;
         return $this->sresult();	     
	 }
	 
	 /**
	  *订单详情(量体相关)
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	 function orderInfoS_v3($data)
	 {
	     $token = isset($data->token) ? $data->token : '';
	     $son_sn = isset($data->son_sn) ? $data->son_sn : '';
	     if (!$son_sn)
	     {
	         return  $this->eresult('son_sn是必填参数');
	     }
	     $serve_mode_money= array(  //=====  量体方式和费用配置  =====
	         '1' => '上门量体', //=====    =====
	         '2' => '到店量体', //=====    =====
	         '3' => '线下采集',//=====    =====
	         '4' => '后台录入',//=====    =====
	         '6' => '指派量体师',//=====    =====
	     );
	     $order_figure_mod = m('orderfigure');
	     $serve_mod = m('serve');
	     $order_figure_info = $order_figure_mod->get("son_sn = $son_sn");
	     $order_figure = array();
	     if ($order_figure_info)
	     {
	         $figure_state = 0;
	         if ($order_figure_info['lw'])//=====  通过量体其中一个字段来判断此条量体数据是否完整  =====
	         {
	             $figure_state = 1;
	         }
	         $special = array();//=====  特体  =====
	         $style = array();//=====  着装风格  =====
	         $figure_item = array();//=====  量体参数  =====
	         if ($figure_state) 
	         {
	             $url = FIGUREURL."/soap/club.php?act=getSpecialFields";
	             $json = file_get_contents($url);
	             $json = json_decode($json,true);
	             $json = $json['result']['data'];
	             foreach ($json['public'] as $key => $value)
	             {
	                 $tmp['name'] = $value['cateName'];
	                 $val = $order_figure_info[$value['value_name']];
	             
	                 foreach ($value['item'] as $key1 => $value1)
	                 {
	                     $tmp['value'] = '';
	                     if ($val == $value1['value'])
	                     {
	                         if ($figure_state)
	                         {
	                             $tmp['value'] = $value1['name'];
	                         }
	                         break;
	                     }
	                 }
	                 $special[] = $tmp;
	             }
	             foreach ($json['special'] as $key => $value)
	             {
	                 $tmp2['name'] = $value['cateName'];
	                  
	                 $val = $order_figure_info[$value['value_name']];
	                  
	                 foreach ($value['item'] as $key1 => $value1)
	                 {
	                     $tmp2['value'] = '';
	                     if ($val == $value1['value'])
	                     {
	                         if ($figure_state)
	                         {
	                             $tmp2['value'] = $value1['name'];
	                         }
	                         break;
	                     }
	                 }
	                 $style[] = $tmp2;
	             }
	             $url = FIGUREURL."/soap/club.php?act=getFigureFields";
	             $json = file_get_contents($url);
	             $json = json_decode($json,true);
	             $json = $json['result']['data'];
	             foreach ($json as $key => $value)
	             {
	                 $tmp3['name'] = $value['zname'];
	                 $val = "";
	                 if ($order_figure_info[$key])
	                 {
	                     $val = $order_figure_info[$key];
	                 }
	                 $cm = "cm";
	                 if ($key == 'lweight')
	                 {
	                     $cm = "kg";
	                 }
	                 $tmp3['value'] = $val.$cm;
	                 $figure_item[] = $tmp3;
	             }
	         }
	         
	         $time_noon_arr = array('am'=>"上午",'pm'=>"下午");
	         $custom_item = array();//=====  客户信息  =====
	         $serve_info = $serve_mod->get_info($order_figure_info['server_id']);//=====  门店信息  =====
	         $custom_item['customer_name'] = $order_figure_info['realname'];
	         $custom_item['customer_mobile'] = $order_figure_info['phone'];
	         $custom_item['souce_from'] = $serve_mode_money[$order_figure_info['measure']];
	         //=====  量体时间  =====
	         $modi_time = "";
	         if ($order_figure_info['modi_time'])
	         {
	             //$modi_time = date("Y年m月d日 H时i分",$order_figure_info['modi_time']);
	             $modi_time = $order_figure_info['modi_time'];
	         }
	         elseif ($order_figure_info['time'])
	         {
	             $time = strtotime($order_figure_info['time']);
	             //$modi_time = date("Y年m月d日",$time)." ".$time_noon_arr[$order_figure_info['time_noon']];
	             $modi_time = $time;
	         }
	        // $custom_item['moditime'] = $modi_time;
	         $custom_item['serve_name'] = $serve_info['serve_name'];
	         //=====  量体师姓名  =====
	         $lianti_name = "";
	         if ($order_figure_info['liangti_id'])
	         {
	             $member_mod = m('member');
	             $liangti_info = $member_mod->get_info($order_figure_info['liangti_id']);
	             $lianti_name = $liangti_info['real_name'];
	             $lianti_phone = $liangti_info['user_name'];
	         }
	         else
	         {
	             $lianti_name = $serve_info['linkman'];
	             $lianti_phone = $serve_info['mobile'];
	         }
	         $custom_item['real_name'] = $lianti_name;
	         $custom_item['serve_name'] = $serve_info['serve_name'];
	         $custom_item['serve_address'] = $serve_info['serve_address'];
	         $custom_item['store_mobile'] = $serve_info['store_mobile'];
	         $custom_item['lasttime'] = $serve_info['serve_name'];
	         $custom_item['lasttime'] = ($modi_time);
	         //=====  量体状态  =====
	       /*   $custom_item['liangti_state_name'] = '待量体';
	         if ( $order_figure_info['liangti_state'] >= 2 || $order_figure_info['measure'] == 5)
	         {
	             $custom_item['liangti_state_name'] = '已完成';
	         } */
	         //$custom_item['is_liangti'] = $figure_state;
	         $order_figure['special'] = $special;
	         $order_figure['style'] = $style;
	         $order_figure['figure'] = $figure_item;
	         $order_figure['cus'] = $custom_item;
	     }
	     $this->result = $order_figure;
	     return $this->sresult();
	 }
	 
	 /**
	  *订单详情(工艺相关)
	  *@author liang.li <1184820705@qq.com>
	  *@2015年6月4日
	  */
	 function orderInfoT($data)
	 {
	     $orderId = isset($data->orderId) ? intval($data->orderId) : 0;
	     $sn = isset($data->sn) ? intval($data->sn) : 0;
	     $type = isset($data->type) ? $data->type : 0;
	     if (!$orderId || !$sn || !$type)
	     {
	         return $this->eresult('缺少必填参数');
	     }
	     $res = $this->getDict($orderId, $sn, $type);
	     $return['dict'] = $res['dict'];
	     $return['embs'] = array_values($res['embs']);
	     //=====  刺绣相关  =====
	     $this->result = $return;
	     return $this->sresult();
	 }
	  
	 
	 function getDict($orderid,$sn,$type){
	 
	     $this->cData = array(
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
	     $embs = $this->outEmbs($embs);
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
	     $return['dict'] = $result;
	     $return['embs'] = $embs;
	     return $return;
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
	     
	     //=====  格式化out  =====
	     if ($embs) 
	     {
	         foreach ($embs as $key => &$value) 
	         {
	             if ($value['out']) 
	             {
	                 $value['out'] = array_values($value['out']);
	             }
	             else 
	             {
	                 unset($embs[$key]);
	             }
	         }
	     }
	         
	     
	   return $embs;
	     //$this->assign("embs", $embs);
	 }
	 
	 /**
	 *快递查询
	 *@author liang.li <1184820705@qq.com>
	 *@2015年12月1日
	 */
	 function orderInfoK($data) 
	 {
	     $id = isset($data->id) ? $data->id : '';
	     $typeCom ="shunfeng";//快递公司
	     $typeNu = "603159372806";  //快递单号
	     
	     //echo $typeCom.'<br/>' ;
	     //echo $typeNu ;
	     
	     $AppKey='234eeb36235cd1e4';//请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
	     $url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
	return $url;     
	     //请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
	     $powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
	     //$url = 'http://www.kuaidi100.com/query?id=1&type=shunfeng&postid=199499465449';
	     
	     //优先使用curl模式发送数据
	     if (function_exists('curl_init') == 1){
// 	    return 3;
	         $curl = curl_init();
	         curl_setopt ($curl, CURLOPT_URL, $url);
	         curl_setopt ($curl, CURLOPT_HEADER,0);
	         curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	         curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	         curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	         $get_content = curl_exec($curl);
	         curl_close ($curl);
	     }else{
	         include(PROJECT_PATH.'includes/snoopy.php');
	         $snoopy = new snoopy();
	         $snoopy->referer = 'http://www.google.com/';//伪装来源
	         $snoopy->fetch($url);
	         $get_content = $snoopy->results;
	     }
	     
	     return $get_content;
	     print_r($get_content . '<br/>' . $powered);
	     exit();
	 }
	 

	 /**
	  *添加收藏
	  *@author tangshoujian <963830611@qq.com>
	  *@2015年12月1日
	  */
	 
	 function addCollect($data){
	     
	     $token = isset($data->token) ? $data->token : '';
	     $id = isset($data->id) ? $data->id : '';
	     $type = isset($data->type) ? $data->type : 'goods';
	     $keyword = empty($data->keyword)  ? '' : trim($data->keyword);
	     
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         return $this->tresult();
	     }
	     $user_id = $user_info['user_id'];
	     
	     if (!$id)
	     {
	         $this->msg = '收藏不存在！';
	         return $this->eresult();
	     }
	     
	     $collect_mod =& m("collect");
	     if($type =='goods'){
	         $collect  = $collect_mod->find(array(
	             "conditions" => "user_id = " . $user_id ." and type='".$type."' and item_id=".$id,
	         ));
	         
	         if($collect){
	              $this->msg = '您已经收藏过该商品！';
	              return $this->eresult();
	         }
	         
	         $_data['user_id'] = $user_id;
	         $_data['type'] = $type;
	         $_data['item_id'] = $id;
	         $_data['keyword'] = $keyword;
	         $_data['add_time'] = time();
	         
	         $collect_id = $collect_mod->add($_data);
	         if(!$collect_id){
	             $this->msg = '收藏商品失败!';
	             return $this->eresult();
	         }
	         
	         return $this->sresult(); 
	     }  
	 }
	 /**
	  *删除收藏
	  *@author tangshoujian <963830611@qq.com>
	  *@2015年12月1日
	  */
	 
	 function dropCollect($data){
	     
	     $token = isset($data->token) ? $data->token : '';
	     $item_id = isset($data->id) ? $data->id : '';
	     
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         return $this->tresult();
	     }
	     $user_id = $user_info['user_id'];
	     
	     if (empty($item_id))
	     {
	         $this->msg = '删除收藏不存在！';
	         return $this->eresult();
	     }
	     
	     $item_id = explode(",", $item_id);
	     
	     $collect_mod =& m('collect');
	     
	     
	     $conditions = " user_id = '{$user_id}' AND item_id " . db_create_in($item_id);
	     
	     $is_delete = $collect_mod->drop($conditions);
	     
	     if(!$is_delete){
	         $this->msg = '删除收藏失败！';
	         return $this->eresult();
	     }
	     
	     return $this->sresult();
	      
	 }
	 
	 /**
	  *收藏 列表
	  *@author tangshoujian <963830611@qq.com>
	  *@2015年12月1日
	  */
	 
	 function myCollect($data){
	     
	     $token = isset($data->token) ? $data->token : '';
	     $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	     $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	     $limit = $pageSize*($pageIndex-1).','.$pageSize;
	     $goods_mod =& m('goods');
	     $goods_promotion_mod =& m('goods_prorule');
	     $member_lvs=m("memberlv");
	     $goods_prorel_mod =& m('goods_prorel');
	     $goodslink_mod = &m('goods_prolink');
	     
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         return $this->tresult();
	     }
	     $user_id = $user_info['user_id'];
	     
	     $model_collect =& m('collect');
	     
	     $collect_custom = $model_collect->find(array(
	         'conditions' => "user_id = " . $user_id ." and type='goods' ",
	         'count' => true,
	         'order' => 'collect.add_time DESC',
	         'limit' => $limit,
	     ));
	     
	     $cids = array();
	     $cls = array();
	     foreach($collect_custom as $val){
	         $cls[$val["item_id"]] =$val;
	         $cids[] = $val["item_id"];
	     }
	     
	     $new_time=time();
	     if($user_info){
	     
	         $member_lv=$user_info['member_lv_id'];
	         $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
	     
	     }else{
	         $user_infos=$member_lvs->get(array(
	             'conditions' =>"1=1",
	     
	         ));
	         $member_lv=$user_infos['member_lv_id'];
	         $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
	     }
	     
	     
	         
	     $sorts="p_order";
	     
	     $orders="DESC";
	     
	     $goodlists=$goods_mod->find(array(
	         'conditions'=>"1=1 and goods_id".db_create_in($cids),
	         'limit'	  => $limit,
	         'count' => true,
	         'order'=>$sorts." ".$orders,
	         'index_key'=>"",
	     ));
	     
	     $new_time=time();
	     $goods_promotion= $goods_promotion_mod->find(array(
	         'conditions'=>"is_open = 1 AND find_in_set(2,site_id)  AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
	         'order'  =>"level ASC",
	         'index_key'=>"",
	     ));
	     
	     
	     if($goodlists){
	         foreach($goodlists as $key=>$val){
	             $goodlists[$key]['order_price']=intval($val['price']);
	             $goodlists[$key]['price']=intval($val['price']);
	             $goodlists[$key]['mktprice']=intval($val['mktprice']);
	             $goodlists[$key]['intro']=base64_encode($val['intro']);
	         }
	     }
	     
	     $iny=0;
	     if($member_lv){
	     
	         if($goods_promotion){
	     
	             $len=count($goods_promotion);
    	             for($i=1;$i<$len;$i++)
    	             {
    	                 for($k=0;$k<$len-$i;$k++)
	                     {
    	                     if($goods_promotion[$k]['if_ex']<$goods_promotion[$k+1]['if_ex'])
    	                     {
        	                     $tmp=$goods_promotion[$k+1];
        	                     $goods_promotion[$k+1]=$goods_promotion[$k];
        	                     $goods_promotion[$k]=$tmp;
    	                     }
	                     }
    	              }
	     
	              }
	                   
	                     if($goodlists){
	                     foreach($goodlists as $key=>$val){
	                     	
	                     if($goods_promotion){
	               
	                     foreach($goods_promotion as $k1=>$v1){
	     
					if($v1['favorable']==1){
	     
	     					    $goodlink=$goodslink_mod->find(array(
	     					        'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
	     					            'fields'=>"favorable_value",
	     					            'index_key'=>"",
	                         ));
	                             if($goodlink){
	                                 foreach($goodlink as $k2=>$v2){
	                                 $goods_lists[]=$v2['favorable_value'];
	                             }
	                             }
	                             if(in_array($val['type_id'],$goods_lists)){
	     
	                                 if($v1['yhcase']==1){
	                                 $goodlists[$key]['yhcase_id']=1;
	                                 $goodlists[$key]['yhcase_name']='促销';
	                                 $goodlists[$key]['yhcase']=intval($val['price']*($v1['yhcase_value']/100));
	                                 $goodlists[$key]['order_price']=intval($val['price']*($v1['yhcase_value']/100));
	                                 }elseif($v1['yhcase']==2){
	                                 $goodlists[$key]['yhcase_id']=2;
	                                 $goodlists[$key]['yhcase_name']='促销';
	                                 $goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
	                                 $goodlists[$key]['order_price']=intval($v1['yhcase_value']);
							}elseif($v1['yhcase']==3){
	     							$goodlists[$key]['yhcase_id']=3;
	     							    $goodlists[$key]['yhcase_name']='促销';
	     											$goodlists[$key]['yhcase']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	     											$goodlists[$key]['order_price']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
							}elseif($v1['yhcase']==4){
	     							$goodlists[$key]['yhcase_id']=4;
	     							$goodlists[$key]['yhcase_name']='促销';
	     							$goodlists[$key]['yhcase']=intval($val['price']-$v1['yhcase_value']);
	     							$goodlists[$key]['order_price']=intval($val['price']-$v1['yhcase_value']);
	     							}elseif($v1['yhcase']==5){
	     							    $goodlists[$key]['yhcase_name']='免邮';
	     							    $goodlists[$key]['yhcase_id']=5;//免邮
	     							    $goodlists[$key]['yhcase']=0;
	     							}
	     							        	
	     							}else{
	     							$goodlists[$key]['yhcase_id']=0;
	     							    $goodlists[$key]['yhcase']=0;
	     							}
	     							}elseif($v1['favorable']==2){
	     							$goods_lists=$goods_prorel_mod->get(array(
	     							    'conditions'=>"c_id='{$val['goods_id']}' AND d_id='{$v1['id']}'",
	                                 ));
	     
	                                     if($goods_lists){
	                                     if($v1['yhcase']==1){
	                                     $goodlists[$key]['yhcase_id']=1;
	                                     $goodlists[$key]['yhcase_name']='促销';
	                                         $goodlists[$key]['yhcase']=intval($val['price']*($v1['yhcase_value']/100));
	                                         $goodlists[$key]['order_price']=intval($val['price']*($v1['yhcase_value']/100));
	                                         }elseif($v1['yhcase']==2){
	                                             $goodlists[$key]['yhcase_id']=2;
	                                             $goodlists[$key]['yhcase_name']='促销';
	                                                 $goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
	                                                 $goodlists[$key]['order_price']=intval($v1['yhcase_value']);
	                                 }elseif($v1['yhcase']==3){
	                                 $goodlists[$key]['yhcase_id']=3;
	                                 $goodlists[$key]['yhcase_name']='促销';
	                                 $goodlists[$key]['yhcase']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	                                 $goodlists[$key]['order_price']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	                                 }elseif($v1['yhcase']==4){
	                                     $goodlists[$key]['yhcase_id']=4;
	                                         $goodlists[$key]['yhcase_name']='促销';
	                                             $goodlists[$key]['yhcase']=intval($val['price']-$v1['yhcase_value']);
	                                             $goodlists[$key]['order_price']=intval($val['price']-$v1['yhcase_value']);
	                                             }elseif($v1['yhcase']==5){
	                                             $goodlists[$key]['yhcase_name']='免邮';
	                                             $goodlists[$key]['yhcase_id']=5;//免邮
	                                                 $goodlists[$key]['yhcase']=0;
	                                             }
	                                             }else{
	                                             $goodlists[$key]['yhcase_id']=0;
	                                                 $goodlists[$key]['yhcase']=0;
	                                             }
	                                             }elseif($v1['favorable']==3){
	                                                 $goodlink=$goodslink_mod->find(array(
	                                                     'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
	                                             ));
	                                             if($goodlink){
	                                             foreach($goodlink as $k2=>$v2){
	                                             $goodlinks[]=$v2['favorable_value'];
	                                             }
	                                 }
	                                 if(in_array($val['cat_id'],$goodlinks)){
	     
	                                     if($v1['yhcase']==1){
	                                         $goodlists[$key]['yhcase_id']=1;
	                                         $goodlists[$key]['yhcase_name']='促销';
	                                             $goodlists[$key]['yhcase']=intval($val['price']*($v1['yhcase_value']/100));
	                                             $goodlists[$key]['order_price']=intval($val['price']*($v1['yhcase_value']/100));
	                                 }elseif($v1['yhcase']==2){
	                                     $goodlists[$key]['yhcase_id']=2;
	                                     $goodlists[$key]['yhcase_name']='促销';
	                                     $goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
	                                     $goodlists[$key]['order_price']=intval($v1['yhcase_value']);
	                                     }elseif($v1['yhcase']==3){
	                                     $goodlists[$key]['yhcase_id']=3;
	                                     $goodlists[$key]['yhcase_name']='促销';
	                                         $goodlists[$key]['yhcase']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	                                         $goodlists[$key]['order_price']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	                                         }elseif($v1['yhcase']==4){
	                                             $goodlists[$key]['yhcase_id']=4;
	                                             $goodlists[$key]['yhcase_name']='促销';
	                                                 $goodlists[$key]['yhcase']=intval($val['price']-$v1['yhcase_value']);
	                                                 $goodlists[$key]['order_price']=intval($val['price']-$v1['yhcase_value']);
	                                                 }elseif($v1['yhcase']==5){
	     											$goodlists[$key]['yhcase_name']='免邮';
	     										$goodlists[$key]['yhcase_id']=5;//免邮
	     										$goodlists[$key]['yhcase']=0;
	     										}
	     										}else{
	     								$goodlists[$key]['yhcase_id']=0;
	     								$goodlists[$key]['yhcase']=0;
	     							}
	     							}elseif($v1['favorable']==4){
	     											if($v1['yhcase']==1){
	     											$goodlists[$key]['yhcase_id']=1;
	     											$goodlists[$key]['yhcase_name']='促销';
	     													$goodlists[$key]['yhcase']=intval($val['price']*($v1['yhcase_value']/100));
	     															$goodlists[$key]['order_price']=intval($val['price']*($v1['yhcase_value']/100));
	     													}elseif($v1['yhcase']==2){
	     															$goodlists[$key]['yhcase_id']=2;
	     															$goodlists[$key]['yhcase_name']='促销';
	     													$goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
	     													$goodlists[$key]['order_price']=intval($v1['yhcase_value']);
	     					}elseif($v1['yhcase']==3){
	     														$goodlists[$key]['yhcase_id']=3;
	     														$goodlists[$key]['yhcase_name']='促销';
	     														$goodlists[$key]['yhcase']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	     											$goodlists[$key]['order_price']=intval($val['price']-$val['price']*($v1['yhcase_value']/100));
	     													}elseif($v1['yhcase']==4){
	     														$goodlists[$key]['yhcase_id']=4;
	     														$goodlists[$key]['yhcase_name']='促销';
	     														$goodlists[$key]['yhcase']=intval($val['price']-$v1['yhcase_value']);
	     														$goodlists[$key]['order_price']=intval($val['price']-$v1['yhcase_value']);
	     														}elseif($v1['yhcase']==5){
	     															$goodlists[$key]['yhcase_name']='免邮';
	     																$goodlists[$key]['yhcase_id']=5;//免邮
	     																$goodlists[$key]['yhcase']=0;
	     														}
	     														}
	     								
	     														}
	     														}else{
	     															$goodlists[$key]['yhcase_id']=0;
	     															$goodlists[$key]['yhcase']=0;
	     														}
	     
	     														}
	     			}
	     			}else{
	     				foreach($goodlists as $key=>$val){
	     					$goodlists[$key]['yhcase_id']=0;
	     					$goodlists[$key]['yhcase']=0;
	     				}
	     			}
	     
	     
	     $this->result = $goodlists;
         return $this->sresult();  
         
	 }
	 
	 
	 
	
}

