<?php
/**
 * 商品列表、商品详情
 * @author yusw
 *
 */
class CustomApp extends MallbaseApp
{
    var $_custom_mod;
    var $_attribute_mod;
    var $_customAttr_mod;
    var $_question_mod;
    var $_customLink_mod;
    var $_designer_mod;
    var $_comment_mod;
    var $_answer_mod;
    function __construct(){
        parent::__construct();
        $this->_custom_mod    = &m("custom");
        $this->_attribute_mod = &m("customattribute");
        $this->_customAttr_mod= &m("cusAttr");
        $this->_question_mod  = &m("question");
        $this->_customLink_mod  = &m("cusLink");
        $this->_designer_mod    = &m("designer");
        $this->_comment_mod = &m("detail_comment");
        $this->_answer_mod = &m('detail_answer');
    }


    function diy(){
    	$menus = array('0001'=>'套装','0003'=>'西服','0004'=>'西裤','0005'=>'马夹','0006'=>'衬衣','0007'=>'大衣');
    	$styles = array('bcasual'=>'休闲','bdress'=>'礼服','bformal'=>'正装');    	$args = $this->get_params();
    	$user = $this->visitor->get();
    	$uid = 0;    	if($user['user_id']){
    		$uid = $user['user_id'];    		do{    			$api_token = ApiAuthcode($user['user_id'], 'ENCODE', 'kuteiddiy', 0);    		} while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));    	}else{    		$api_token = 'mfd';    	}
    	
    	/* description、keywords、title */    	$this->_config_seo('title', $styles[$args[1]].$menus[$args[0]] . ' - ' . Conf::get('site_title'));    	     	$this->assign('tk', $api_token);
    	
    	$this->assign('pargs', $args);    	$this->assign('ms', $menus);    	$this->assign('ss', $styles);    	$this->assign('uid', $uid);    	$this->display('diy/index.html');    }
    
    function stable(){    	$this->_init_view();    	$str = $this->_view->fetch("diy/sizetable.html");    	$this->json_result($str);    }

	  //列表页
	  function getList($data)
	  {
		  $topic     =  isset($data->t)         ? intval($data->t) : 0;
		  $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
		  $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
		  if($pageIndex < 1) $pageIndex = 1;
		  if($pageSize < 1) $pageSize = 1;
		  $limit = $pageSize*($pageIndex-1).','.$pageSize;

		  $dis_mod = m('JpjzDissertation');
		  $link_mod = m("links");
		  $topicArr = $dis_mod->find(array(
			  'order'      => "sort_order ASC",
			  "limit"      => 4,
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
			 "conditions" => "links.d_id = '{$current["id"]}' AND suit_list.is_sale=1",
			 "join"       => "app_custom",
			 'order'      => "links.lorder ASC",
			 'limit'      => $limit,
		  ));

		  foreach((array) $customArr as $key => $val){
			  $result['custom'][] = $val;
		  }

		  $this->result = $result;
		  return $this->sresult();
	  }



	  /**
	  *   产品详情
	  *@author yusw
	  *@2015年6月11日
	  */
	  function goodsInfo($data)
	  {
	      $id = isset($data->id) ? $data->id : 0;
	      $token = isset($data->token) ? $data->token : '';
	      $user_info = getUserInfo($token);
	      if ($user_info['dis_count'])
	      {
	          $dis_count = $user_info['dis_count'];
	      }
// dump($user_info);
	      $img_url = LOCALHOST1;
	      $suit_list_mod      = m('suitlist');
	      $suit_rel_mod = m('suitrelat');
	      $custom_mod = m('custom');
	      $pinlie = include PROJECT_PATH.'includes/data/config/pinlei.php';

	      $custom_attr = m('cusAttr');

	      $suit_list_info = $suit_list_mod->get_info($id);
	      if (!$suit_list_info['is_sale'])
	      {
	          $this->msg = '无此商品或者禁止查看';
	          return $this->eresult();
	      }


	      if ($dis_count)
	      {
	          $suit_list_info['price'] = _format_price_int($suit_list_info['price'] * $dis_count);
	      }
	      else
	      {
	          $suit_list_info['price'] = _format_price_int($suit_list_info['price']);
	      }

	      //=====  点击数加1  =====
	      $suit_list_mod->setInc($id,'click_num');

	      $suit_info = $suit_rel_mod->get("tz_id = $id");
	      $cus_list = array();
	      if ($suit_info)
	      {
	          $cus_ids = $suit_info['jbk_id'];
	          $cus_list = $custom_mod->find(array(
	              'conditions' => "id IN ($cus_ids)",
	              'index_key' => "",
	          ));
	      }
// dump($cus_list);

	      $linkAttrs = array();
          if ($cus_list)
          {
              //=====  取得属性工艺尺码  =====
              $cus_attr_id = $cus_list[0]['id'];
              $cus_attr_list = $custom_attr->find(array(
                  'conditions' => "custom_id = $cus_attr_id",
                  'join' => "belongs_to_attribute",
              ));
              if ($cus_attr_list)
              {
                  foreach((array) $cus_attr_list as $kw => $val)
                  {
                      if ($val)
                      {
                          $tmp['name'] = $val["attr_name"];
                          $tmp['value'] = $val['attr_value'];
                          $linkAttrs[] = $tmp;
                      }

                     /*  $linkAttrs[$val["attr_id"]]['name'] = $val["attr_name"];
                      $linkAttrs[$val["attr_id"]]['value'] = $val["attr_value"]; */
                  }
              }
// dump($linkAttrs);
            //=====  取得尺码  =====
            foreach ($cus_list as $key => $value)
            {
                $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格
                $filename = PROJECT_PATH.'includes/data/config/size_json/'.$value['category'].'.size.json';
                $jsonString = file_get_contents($filename);
                $jsonData = json_decode($jsonString,true);
                $size_list = $jsonData['sizeAll'] ;
                $size = array();
                foreach ($size_list as $key1 => $value1)
                {
                    $size[] = $value1['Id'];
                }
                $cus_list[$key]['size'] = $size;
                $cus_list[$key]['size_url'] = M_URL."article/spec.html";
                $cus_list[$key]['cate_name'] = $pinlie[$value['category']]['name'];

            }

          }
          $this->result['list_cus'] = $cus_list;
          $this->result['list_attr'] = $linkAttrs;
          $this->result['suit_info'] = $suit_list_info;
          return $this->sresult();
	  }






	function index(){
	    $arg = $this->get_params();
	    $page = $this->_get_page(5);
	    $id = intval($arg[0]);
	    $cate=$arg[1];
	    $info = $this->_custom_mod->get("id='{$id}' && is_sale=1");
	    $star=$score=$one=$two=$three=$four=$five=0;
	    if(empty($info)){
	        $this->show_message("访问的商品不存在或者已下架");
	        return false;
	    }

	    $attrs = $this->_customAttr_mod->find(array(
	       "conditions" => "at.custom_id='{$id}'",
	       'join'       => "belongs_to_attribute",
	       'fields'     => "at.attr_value, attr.attr_name",
	    ));

	    $links = $this->_customLink_mod->find(array(
	        "conditions" => "link.custom_id='{$id}'",
            'join'       => "belongs_to_custom",
            'fields'     => "c.id as cid,c.name, c.small_img, c.price",
	    ));
	    $design = array();
	    if($info['design_id']){
	        $design = $this->_designer_mod->get($info["design_id"]);
	        $count = $this->_custom_mod->_count("design_id='{$info["design_id"]}' AND is_sale = 1");
	        $design["count"] = $count;
	    }

	    $this->_config_seo('title', $info["name"].' - ' . Conf::get('site_title'));
	    $this->assign("page_info",$page);
	    $size  = $this->size[$info["category"]];
	    $jsonString = file_get_contents(ROOT_PATH.$size["file"]);
	    $sizeArray = json_decode($jsonString,true);
	    $this->assign("size",   $sizeArray['sizeAll']);
	    $this->assign("design", $design);
        $this->assign("attrs", $attrs);
	    $this->assign("info",  $info);
	    $this->assign("links", $links);
	    $this->display("custom.html");
	}

	function ask(){
	    $custom_id = intval($_GET['custom_id']);
	   	import("question.lib");
	    $question = new Question();
	    $page = $this->_get_page(5);
	    $question->set_param(array(
	        "id"   => $custom_id,
	        "page" => $page,
	        'type' => "custom",
	    ));

	    $result = $question->load();

	    $this->_format_page($result['page']);

	    $this->assign("page_info", $result['page']);
	    $this->assign("list",      $result["list"]);
	    $content = $this->_view->fetch("question.lib.html");
	    $this->json_result($content);
	    die();
	}

	function commit(){
	    $id = intval($_POST['custom_id']);
	    import("question.lib");
	    $question = new Question();
	    $question->set_param(array(
	        "id"   => $id,
	        'type' => "custom",
	    ));
	    $info = $this->_custom_mod->get("id='{$id}' AND is_sale=1");
	    $question->set_data($info);
	    $res = $question->commit();
	    if($res){
	        $this->json_result("咨询已提交成功，客服会尽快回复!");
	        die();
	    }else{
	        $this->json_error("意外错误，请重试");
	        die();
	    }
	}











}

?>
