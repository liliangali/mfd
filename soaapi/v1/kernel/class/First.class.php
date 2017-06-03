<?php
	/**
	 *  首单体验款
	 * @author 小五 <xiao5.china@gmail.com>
	 * @version $Id: First.class.php 7530 2015-08-19 01:03:38Z liuc $
	 * @package app
	 */
	class First extends Result
	{
	
	 
	  /**
	  * 轮播图
	  *@author 小五 <xiao5.china@gmail.com>
	  *@2015年5月30日
	  */
	  function shuffling($data) 
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $user_info = getUserInfo($token);
	      $user_id = $user_info['user_id'];
	      if (!$user_info) 
	      {
// 	          return $this->tresult();
	      }
	      $shuffling_mod = m('shuffling');
          $sql = "SELECT s.name,s.img,s.link_url,s.link_clothId,s.link_articleId FROM cf_shuffling s LEFT JOIN cf_shuffling_group
                   g ON s.groups=g.id WHERE status=0 AND g.name LIKE 'app' ORDER BY s.sort_order ASC";
          $db = db();
          $article_mod = &m('article');
          $list = $db->getAll($sql);
          if ($_SERVER['SERVER_NAME'] == 'api.mfd.cn'){
              $h = 'http://www.mfd.cn/';
          }elseif($_SERVER['SERVER_NAME'] == 'api.test.mfd.cn'){
              $h = 'http://www.test.mfd.cn/';
          }elseif($_SERVER['SERVER_NAME'] == 'api.dev.mfd.cn'){
              $h = 'http://www.dev.mfd.cn/';
          }elseif($_SERVER['SERVER_NAME'] == 'api.mfd.com'){
              $h = 'http://local.mfd.com/';
          }
          $lists = array();
          foreach($list as $key=>$val){
              if($val['link_articleId']){
                  $article = $article_mod->find('article_id='.$val['link_articleId']);
                  foreach($article as $kk=>$vv){
                      $content = base64_encode($vv['content']);
                  }
                  $val['link_url'] = $content;
              }
              $val['img'] = $h.$val['img'];
              $lists[] = $val;
          }
//          print_exit($lists);

          /*
	      $sh = 'http://r.cotte.cn/cotte/app/';
	      $h = 'http://new.m.dev.mfd.cn/';
	      $list = array(
	      		    array('title'=>'抢新尝鲜','img'=>$sh.'slide/01.jpg','url'=>'','clothing_id'=>'4'),
	      		    array('title'=>'限量抢购','img'=>$sh.'slide/02_限量抢购.jpg','url'=>$sh.'slide/02_限量抢购页.jpg','clothing_id'=>'0003'),
	      		    array('title'=>'校园特惠','img'=>$sh.'slide/04_校园特惠.jpg','url'=>$h.'custom-diy-0006-bformal-token.html','clothing_id'=>'0006'),
	      		    array('title'=>'特惠狂欢','img'=>$sh.'slide/04.jpg','url'=>'','clothing_id'=>'3'),
	      		);*/
	      $this->result = $lists;
	      return $this->sresult();
	  }
	
	  
	  /**
	  *女衬衣
	  *@author 小五 <xiao5.china@gmail.com>
	  *@2015年5月30日
	  */
	  function blouseList($data) 
	  {
	  	$token = isset($data->token) ? $data->token : '';
	  	$pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	  	$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	  	$limit = $pageSize*($pageIndex-1).','.$pageSize;
	  	
	  	$user_info = getUserInfo($token);
	  	if (!$user_info)
	  	{
// 	  		return $this->tresult();
	  	}

	  	$mod = m('suitlist');
	  	/* 改从套装 － 女装写死 */
	  	$conditions = "cat_id = 128494 AND is_first=1 AND is_sale=1 ";
	  	$list = $mod->find(array(
	  			'conditions' => $conditions,
	  			'limit' => $limit,
	  			'index_key'=>''
	  	));
	  	$disCount = 0;
 	  	if ($user_info['serve_type'] == 1 && $user_info['member_lv_id']){//创业者
	  		$member_lv_mod =& m('memberlv');
	  		$disCount = $member_lv_mod->get_leve_dis_count($user_info['member_lv_id']);
	  	}
	  	$data = array();
	  	foreach ((array)$list as $key=>$r){
	  		/* 成本价＊系数 */
	  		$list[$key]['last_price'] = _format_price_int($r['price']);
// 	  		$list[$key]['last_price'] = _format_price_int($r['price'] * PRODUCT_SHOW_SCALE);
	  		if ($disCount){
	  			/* 成本价＊系数＊会员价格 */
	  			$list[$key]['last_price'] = _format_price_int($r['price'] *$disCount/10);
// 	  			$list[$key]['last_price'] = _format_price_int($r['price'] * PRODUCT_SHOW_SCALE*$disCount/10);
	  		}
	  	}
	  	
	  	$this->result = $list;
	  	return $this->sresult();
	  }
	  
	  /**
	   *女衬衣
	   *@author 小五 <xiao5.china@gmail.com>
	   *@2015年5月30日
	   */
	  function blouseInfo($data)
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $id = isset($data->id) ? $data->id : 0;
	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          return $this->tresult();
	      }
	      $conditions = "1=1 ";

	      $cus_mod = m('custom');
	      $cinfo = $cus_mod->get($id);
	      if (!$cinfo)
	      {
	          $this->result->msg = '此数据不存在';
	          return $this->result->eresult();
	      }
	      $this->result = $cinfo;
	      return $this->sresult();
	  }

	}

