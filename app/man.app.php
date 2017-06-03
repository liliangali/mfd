<?php

class ManApp extends MallbaseApp
{
    var $_custom_mod;
    var $_category_mod;
    var $_attribute_mod;
    var $_customAttr_mod;

    function __construct(){
        parent::__construct();
        $this->_custom_mod    = &m("custom");
        $this->_category_mod  = &bm("gcategory");
        $this->_attribute_mod = &m("customattribute");
        $this->_customAttr_mod= &m("cusAttr");
        $this->_dis_mod =& m('jpjz_dissertation');
    }
    function index()
    {
        $args = $this->get_params();
        $t = intval($args[0]);
        $info = $this->_dis_mod->get($t);
        $this->_get_filter($t);
        $this->_config_seo('title', '男装-麦富迪尚品-'.$info["title"]."-".Conf::get("site_name")."-".Conf::get('site_title'));
        $this->assign("t", $t);
        $this->_mod_cusGallery = &m("customgallery");
        $gallery_list = $this->_mod_cusGallery->find(array(
        		'conditions' => "custom_id = '{$t}'",
        		'order' => 'sort ASC',   		 
        ));
        $this->assign("banner", $gallery_list);
        $this->display('man/index.html');
    }
    
   private function _get_filter($t){
       $p = $this->deCode();
       $cat   = isset($p['cat'])    ? intval($p['cat']) : 0;
       $attr  = isset($p['a'])      ?  $p['a']  : array();
       $order = isset($p['order'])  ? trim($p['order']) : "reorder";
       $sort  = isset($p['sort'])   ? trim($p['sort'])  : "DESC";
       
       //当前分类下子分类
       $p_id = $this->man;
       if ($t == 16) 
       {
           $p_id = $this->woman;
       }
       $categorys = $this->_category_mod->get_children($p_id);
       $ins=count($categorys);
       $categorys[$ins]=array(
       		'cate_id'=>strval($p_id),
       		'cate_name'=>'全部',
       		'intro'=>'0',
       
       );
       foreach($categorys as $key=>$val){
       	$cate_id[]=$val['cate_id'];
       }
       array_multisort($cate_id, SORT_ASC, $categorys);
       foreach($categorys as $key => $val){
           if(empty($cat)){
               $cat = $val["cate_id"];
           }
           if($val['cate_id'] == $cat){
               $categorys[$key]['selected'] = 1;
           }
           
           $categorys[$key]['url'] = $this->enCode(array("cat" => $val["cate_id"]));
       }
       $attr_list = $this->_get_attribute($cat);
   
       foreach($attr_list as $key => $val)
       {
           $tmpAttr = $attr;
           if(isset($tmpAttr)){
             unset($tmpAttr[$key]);
           }
           
           $attr_list[$key]['children']["-1"]['name'] = "全部";
           $attr_list[$key]['children']["-1"]['url'] = $this->enCode(array(
               "cat" => $cat,
               "a"   => $tmpAttr,
           ));
           
           $attr_list[$key]['children']["-1"]['selected'] = !isset($attr[$key]) ? 1 : 0;
           
           $values = explode(",", $val['attr_values']);
           foreach((array) $values as $k => $v){
               $tmpAttr = $attr;
               if($attr[$key] != $v){
                   $tmpAttr[$key] = $v;
               }else{
                   $attr_list[$key]['children'][$k]['selected'] = 1;
               }
               $attrUrlArr = array();
               foreach($tmpAttr as $_k => $_v){
                   $attrUrlArr[$_k] = $_v;
               }
               
               $attr_list[$key]['children'][$k]['name'] = $v;
               $attr_list[$key]['children'][$k]['url']  = $this->enCode(array(
                   "cat" => $cat,
                   "a"   => $attrUrlArr,
               ));
           }
       }
       $this->assign("attr_list", $attr_list);
       $this->assign("categorys", $categorys);
       $this->assign("formatUrl", $this->enCode(array("cat" => $cat, "a" =>$attr)));
       $this->assign("order",     $order);
       $this->assign("sort",      $sort);
   }
   
   private function _get_attribute($cat_id=0){
       //当前分类信息
       $info = $this->_category_mod->get_info($cat_id);
       if(!$info["attrid"]){
           return array();
       }
       $attr_list = $this->_attribute_mod->find(array(
           'conditions' => "attr_id IN ({$info["attrid"]})",
           "order"      => "sort_order DESC",
       ));
       
       return $attr_list;
   }
   
   function loadData(){
       $p = $this->deCode();
       $args = $this->get_params();
       $t = intval($args[1]);
       $cat   = isset($p['cat'])   ? intval($p['cat']) : 0;
       $attr  = isset($p['a'])     ?  $p['a']  : array();
       $order = isset($p['order']) ? trim($p['order']) : "reorder";
       $sort  = isset($p['sort'])  ? trim($p['sort'])  : "DESC";
       if($cat=='128493'){
       $cates=	$this->_category_mod->find(array(
       			'conditions' =>"parent_id='{$cat}'",
       			'fields'=>'cate_id',
       	));
        $cattes=i_array_column($cates, 'cate_id');
       $conditiony=" AND ".db_create_in($cattes,'suit_list.cat_id');
       }elseif($cat=='128494'){
       	$cates=	$this->_category_mod->find(array(
       			'conditions' =>"parent_id='{$cat}'",
       			'fields'=>'cate_id',
       	));
       	$cattes=i_array_column($cates, 'cate_id');
       	$conditiony=" AND ".db_create_in($cattes,'suit_list.cat_id');
       }else{
       	$conditiony=" AND suit_list.cat_id = '{$cat}'";
       }
       $conditions = "suit_list.is_sale=1 AND suit_list.theme = '{$t}' AND (suit_list.to_site = '0' OR suit_list.to_site = 'pc')".$conditiony;
       $suitAttr = &m("suitattr");
       if(!empty($attr)){
           $attrCondition=' 0 ';
           $aNum = 0;
           foreach($attr as $key => $val){
               $aNum +=1;
               $attrCondition .= " OR (attr_id='{$key}' AND attr_value = '{$val}')";
           }
           
           $attrCondition .= " GROUP BY suit_id HAVING num = {$aNum}";
           
           $suitids = $suitAttr->find(array(
               'conditions' => $attrCondition,
               'fields' => "suit_id, count(1) AS num",
           ));
           
           foreach($suitids as $_k => $_v)
           {
               $ids[] = $_v['suit_id'];
           }
            
           if(!empty($ids))
           {
               $conditions .= " AND suit_list.id ".db_create_in($ids);
           }else{
               $conditions .= " AND suit_list.id IN (0)";
           }
       }
       
       $suit = &m("suitlist");
       $page = $this->_get_page(8);
       $_order = "suit_list.{$order} {$sort},suit_list.id DESC";
       $list = $suit->find(array(
           "conditions" => $conditions,
           "order"      => $_order,
           'limit'      => $page['limit'],
           'fields'     => "suit_list.id, suit_list.suit_name, suit_list.side_images,suit_list.image, suit_list.price,suit_list.design_id, designer.username, designer.photo_url",
           'join'       => "belongs_to_design",
           "count"      => true,
       ));
// print_exit($conditions);      
       if ($list)
       {
           foreach ($list as $key => &$value)
           {
               $value['woman_price'] = 0;
               if ($t == 16)
               {
                   $value['woman_price'] = _format_price_int($value['price'] * 0);
               }
               $value['price'] = _format_price_int($value['price']);
           }
       }
     //  die();
       $page['item_count'] = $suit->getCount();
       $this->_format_page($page);
       $this->assign("list",   $list);
       $content = $this->_view->fetch("man/suit.list.html");
       $retArr = array(
           'content' => $content,
           'link'    => $page["next_link"],
       );
       die($this->json_result($retArr));
   }
   function enCode($arr){
       return "?p=".str_replace('+', '%2b', base64_encode(serialize($arr)));
   }
   function deCode(){
       $p = trim($_GET["p"]);
       $order = isset($_GET['order']) ? trim($_GET['order']) : "add_time";
       $sort  = isset($_GET['sort'])  ? trim($_GET['sort'])  : "DESC";
       if(!in_array($order, array("add_time", "price", "sale_num", "reorder"))){
           $order = "reorder";
       }
        
       if(!in_array($sort, array("ASC", "DESC"))){
           $sort = "DESC";
       }

       $p = base64_decode($p);
       if(!$p){
           $p = array();
       }else{
           $p = unserialize($p);
           if(!$p){
               $p = array();
           }
       }
       if(isset($_GET['order'])){
           $p['order'] = $order;
       }
       
       if(isset($_GET['sort'])){
           $p["sort"]  = $sort;
       }
       return $this->stripDeep($p);
   }
   
   function stripDeep($string){
       if(empty($string)){
           return $string;
       }else{
           return is_array($string) ? array_map("self::stripDeep", $string) : addslashes(htmlspecialchars($string));
       }
   }
}


?>