<?php
/**
 * 定制中心
 * @author yhao.bai
 *
 */
class SuitApp extends MallbaseApp
{
    var $_custom_mod;
    var $_suitlist_mod;
    var $_designer_mod;
    var $_question_mod;
    var $_mod_suitrelation;
    var $_comment_mod;
    var $_answer_mod;
    
    function __construct(){
        parent::__construct();
        $this->_custom_mod       = &m("custom");
        $this->_suitlist_mod     = &m("suitlist");
        $this->_designer_mod     = &m("designer");
        $this->_question_mod     = &m("question");
        $this->_mod_suitrelation = &m("suitrelat");
        $this->_comment_mod = &m("detail_comment");
        $this->_answer_mod = &m('detail_answer');
    }

	function index(){
	    $arg = $this->get_params();
	    $id = intval($arg[0]);
	    $info = $this->_suitlist_mod->get("id='{$id}' && is_sale=1");
	    $cate=$arg[1];
	    if(empty($info)){
	        $this->show_message("访问的商品不存在或者已下架");
	        return false;
	    }
	    
	    $design = array();
	    if($info['design_id']){
	        $design = $this->_designer_mod->get($info["design_id"]);
	        $count = $this->_custom_mod->_count("design_id='{$info["design_id"]}' AND is_sale = 1");
	        $design["count"] = $count;
	    }
	    
	    $recommends = $this->_suitlist_mod->find(array(
	        "conditions"  => "id != '{$id}' AND is_sale = 1 AND design_id = '{$info['design_id']}'",
	        'limit'       => "5",
	        'fields'      => "id, suit_name, image, price"
	    ));
	    
	    $links = $this->_mod_suitrelation->get("tz_id='{$id}'");
	    $customArray = array();
	    $sizeArray        = array();
	    if($links){
	        $linkArr = explode(",", $links["jbk_id"]);
	        foreach($linkArr as $key => $val){
	            if(empty($val)){
	                unset($linkArr[$key]);
	            }else{
	                $linkArr[] = $val;
	            }
	        }
	        if(!empty($linkArr)){
    	        $customArray = $this->_custom_mod->find(array(
    	           "conditions" => "id ".db_create_in($linkArr),
    	           'fields'     => "category, id",
    	        ));
	        }
	        $count = count($customArray);
	        foreach($customArray as $key => $val){
	            $sizeArray[$val["category"]]["num"] = $count;
	            $sizeArray[$val['category']]["id"]  = $val["id"];
	            $size  = $this->size[$val["category"]];
	            $jsonString = file_get_contents(ROOT_PATH.$size["file"]);
	            $sizeArr = json_decode($jsonString,true);
	            $sizeArray[$val["category"]]['name'] = $size['name'];
	            $sizeArray[$val["category"]]['list'] = $sizeArr['sizeAll'];
	            $count -=1;
	        }
	    }
	    $this->_config_seo('title', $info["suit_name"].' - ' . Conf::get('site_title'));
	    $this->assign("size", $sizeArray);
	    $this->assign("recommends", $recommends);
	    $this->assign("design", $design);
	    $this->assign("info", $info);
	    $this->display("suit.html");
	}

	function ask(){
	    import("question.lib");
	    $question = new Question();
	    $id = intval($_GET['id']);
	    $page = $this->_get_page(5);
	    $question->set_param(array(
	        "id"   => $id,
	        "page" => $page,
	        'type' => "suit",
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
	    $id = intval($_POST['id']);
	    import("question.lib");
	    $question = new Question();
	    $question->set_param(array(
	        "id"   => $id,
	        'type' => "suit",
	    ));
	    $info = $this->_suitlist_mod->get("id='{$id}' AND is_sale=1");
	    $info["name"] = $info["suit_name"];
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
