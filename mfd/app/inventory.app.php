<?php

class CustomApp extends BackendApp
{
    private $_mod_custom;
    private $_mod_cusItem;
    private $_mod_cusFab;
    private $_mod_designer;
    private $style;
    private $cat;
    private $init;
    private $_mod_type;
    private $_mod_attribute;
    private $_mod_cusAttr;
    private $_mod_cusGallery;
    var $sw = 80;
    var $sh = 80;
    var $mw = 300;
    var $mh = 300;
    var $coefficient = 5.5;
    function __construct()
    {
         $this->_mod_custom = &m("custom");
         $this->_mod_cusItem = &m("cusItem");
         $this->_mod_cusFab = &m("cusFab");
         $this->_mod_designer = &m("designer");
         $this->_mod_type     = &m("customtype");
         $this->_mod_attribute     = &m("customattribute");
         $this->_mod_cusAttr  = &m("cusAttr");
         $this->_mod_cusGallery = &m("customgallery");
         $this->style = array(
             "1" => "正装",
             "2" => "休闲",
             "3" => "礼服"
         );
         
         $this->init = array(
             /*
             "0001" => array(
                    'name'  => "套装(2pcs)",
                    'items' => array(
                        "0003"     => array("fabric" =>'8001'),
                        '0004'     => array("fabric" =>'8001'),
                     ),
              ),
             "0002" => array(
                 'name'  => "套装(3pcs)",
                 'items' => array(
                     "0003"     => array("fabric" =>'8001'),
                     '0004'     => array("fabric" =>'8001'),
                     '0005'     => array("fabric" =>'8001'),
                 ),
             ),
             */
             "0003" => array(
                  "name"  => "男西服",  
                  'mldh' => "1.75" ,
                  'lldh' => "0", 
                   "ll" => "313",
                   'dict' => array(
                        array(
                             "name" => "缝制工艺 ",
                             "list" => array(
                                  array(
                                      "name"  => "机器缝制",
                                      'price' => "360",
                                  ),array(
                                      "name"  => "半手工缝制",
                                      'price' => "1260",
                                  ),array(
                                      "name"  => "全手工缝制",
                                      "price" => "1700"
                                  ),
                              )      
                        ),
                        array(
                             "name" => "衬工艺&nbsp; ",
                              "list"  => array(
                                   array(
                                       "name" => "粘合衬工艺",
                                       'price' => "160",
                                   ),
                                   array(
                                       "name" => "半麻衬工艺",
                                       "price" => "500"
                                   ),
                                   array(
                                       "name"  => "全麻衬工艺",
                                       "price" => "700",
                                   ),
                              )
                        ),
                    ),
                   "items" => array(
                     "0003"     => array("design" =>'24', "deep" => "298"),
                  )
             ),
             
             "0004" => array(
                 "name"  => "男西裤",
                 'mldh' => "1.15" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                  ),
                 "items" => array(
                     "0004"     => array("design" =>'2021', "deep" => "2157"),
                 )
             ),
             "0017" => array(
                 "name"  => "男短裤",
                 'mldh' => "0.85" ,
                 'lldh' => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0004"     => array("design" =>'2021', "deep" => "2157"),
                 )
             ),
             "0005" => array(
                 "name"  => "男马甲",
                 'mldh' => "0.7" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "600"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0005"     => array("design" =>'4016', "deep" => "4075"),
                 )
             ),
             
             "0006" => array(
                 "name"  => "男衬衣",
                 'mldh' => "1.49" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "180",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "480"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0006"     => array("design" =>'3016', "deep" => "3184"),
                 )
             ),
             
             "0007" => array(
                 "name"  => "男大衣",
                 'mldh' => "2.3" ,
                 'lldh' => "0", 
                 'll'   => "6291",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "1260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "1760"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0007"     => array("design" =>'6007', 'deep' => "298"),
                 )
             ),
             
             //-------------------------------------------女

             "0011" => array(
                 "name"  => "女西装",
                 'mldh' => "1.75" ,
                 'lldh' => "0",
                 'll'   => "95272",
                   'dict' => array(
                        array(
                             "name" => "缝制工艺 ",
                             "list" => array(
                                  array(
                                      "name"  => "机器缝制",
                                      'price' => "360",
                                  ),array(
                                      "name"  => "半手工缝制",
                                      'price' => "1260",
                                  ),array(
                                      "name"  => "全手工缝制",
                                      "price" => "1700"
                                  ),
                              )      
                        ),
                        array(
                             "name" => "衬工艺&nbsp; ",
                              "list"  => array(
                                   array(
                                       "name" => "粘合衬工艺",
                                       'price' => "160",
                                   ),
                                   array(
                                       "name" => "半麻衬工艺",
                                       "price" => "500"
                                   ),
                                   array(
                                       "name"  => "全麻衬工艺",
                                       "price" => "700",
                                   ),
                              )
                        ),
                 ),
                 "items" => array(
                    
                 )
             ),
             
             
             
             "0012" => array(
                 "name"  => "女西裤",
                 'mldh' => "1.15" ,
                 'lldh' => "0",
                 'll'   => "98201",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     
                 )
             ),
             
             
             "0016" => array(
                 "name"  => "女衬衣",
                 'mldh' => "1.49" ,
                 'lldh' => "0",
                 'll'   => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "180",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "480"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                    
                 )
             ),
             
             "0021" => array(
                 "name"  => "女大衣",
                 'mldh' => "2.3" ,
                 'lldh' => "0",
                 'll'   => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "1260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "1760"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
             
                 )
             ),
             
         );
         parent::__construct();
    }


    function index()
    {
        $conditions = ' 1 ';
        $query['name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
        $query['to_site']  = isset($_GET['to_site']) ? trim($_GET['to_site']) : '';

        if($query["name"]){
            $conditions .= " AND name LIKE '%{$query["name"]}%'";
        }
        
        if($query["to_site"]){
            $conditions .= " AND to_site = '{$query["to_site"]}'";
        }

        $page = $this->_get_page(30);
        setcookie('curr_page',$page["curr_page"]);
        $custom_list = $this->_mod_custom->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "id DESC"
        ));
        
        $page['item_count'] = $this->_mod_custom->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('query', $query);
        $this->assign("custom_list", $custom_list);
        $this->assign("to_site", $this->to_site);
        $this->display('custom.index.html');
    }
    
    function add(){
        
        if(IS_POST){
            $name      = trim($_POST['name']);
            $brief     = trim($_POST['brief']);
            $style     = trim($_POST['style']);
            $category  = trim($_POST['category']);
            $source_img= trim($_POST['source_img']);
            $small_img= trim($_POST['small_img']);
            $item          = $_POST['item'];
            $assign          = $_POST['assign'];
          //  $assignPrice  = $_POST["assignPrice"];
            $assignPrice = array();
            $service_price         = $_POST['service_price'];
            $is_default      = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $to_site       = $_POST['to_site'];
            $is_sale       = intval($_POST['is_sale']);
            $is_new       = intval($_POST['is_new']);
            $is_hot       = intval($_POST['is_hot']);
            $cat_id       = 0;
            $design_id    = intval($_POST['design_id']);
            $type_id      = intval($_POST['type_id']);
            $is_first      = intval($_POST['is_first']);
            $attr         = $_POST['attr'];
            
            
            $priceArr = $this->processPrice(array(
                'category'      => $category,
                'service_price' => $service_price,
                'fabric'        => $fab,
                'items'         => $item,
                'assignPrice'   => $assignPrice,
                'default'       => $is_default,
            ));
            
            $cusData = array(
                'name'     => $name,
                'brief'    => $brief,
                'style'    => $style,
                'category' => $category,
                'content'  => $content,
                'source_img'   => $source_img,
                'small_img'    => $small_img,
                'price'    => $priceArr["price"],
                'base_price'    => $priceArr["base_price"],
                'assignprice'   => serialize($assignPrice),
                'service_price' => $service_price,
                'is_sale'  => $is_sale,
                'is_new'   => $is_new,
                'is_hot'   => $is_hot,
                'cat_id'   => $cat_id,
                'design_id' => $design_id,
                'type_id'  => $type_id,
                'add_time' => gmtime(),
                'last_time' => gmtime(),
                'to_site'  => $to_site,
                'is_first' => $is_first,
            );
            
            $custom_id = $this->_mod_custom->add($cusData);
            
            $itemData = array();
            foreach((array)$item as $key => $val){
               $itemData[] = array(
                   'custom_id' => $custom_id,
                   'item_id'   => $key,
                   'menu_id'   => $val,
                   'is_default'=> isset($is_default[$key]) && $is_default[$key] == $key ? 1 : 0,
                   'assign'    => $assign[$key],
               );
            }
            
            if(!empty($itemData)){
                $this->_mod_cusItem->add($itemData);
            }
            
            $fabData = array();
            foreach((array)$fabs as $key => $val){
                $fabData[] = array(
                    'custom_id'  => $custom_id,
                    'item_id'  => $val,
                    'is_default' => $fab == $val ? 1 : 0,
                );
            }
            
            if(!empty($fabData)){
                $this->_mod_cusFab->add($fabData);
            }
            
            $attrData = array();
            
            foreach((array)$attr as $key => $val){
                if($val){
                    $attrData[] = array(
                        "attr_id"     => $key,
                        'attr_value'  => $val,
                        "custom_id"   => $custom_id,
                    );
                }
            }
            
            if(!empty($attrData)){
                $this->_mod_cusAttr->add($attrData);
            }

            
            $this->addGallery($custom_id);
            
            $this->addLink($custom_id);

            $this->show_message("样衣发布成功",'back_list', 'index.php?app=custom');
        }else{
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));
            
            $this->assign("options", $this->_get_options());
            
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );

            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            
            $design_list = $this->_mod_designer->find();
            
            $dsn = array();
            foreach($design_list as $key => $val){
                $dsn[$val["id"]] = $val["username"];
            }
            $typelist = $this->_mod_type->find();
            $types = array();
            foreach($typelist as $key => $val){
                $types[$val["type_id"]] = $val["type_name"];
            }
            $this->assign("types", $types);
            $this->assign("design_list", $dsn);
            $this->assign("to_site", $this->to_site);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->display("custom.info.html");
        }
    }
    
    function loadItem(){
        $v     = trim($_GET['v']);
        $category  = trim($_GET['cat']);
        $mod_dict = &m("dict1");
        $list = $mod_dict->find(array(
            "conditions" => "CODE REGEXP '^{$category}' AND ECODE != ''",
            //'limit'      => "10",
            'fields'     => "ID as id, NAME as name, ECODE as ecode, PARENTID as parentid, STATUSID as statusid"
        ));
        
        $ids = array();

        foreach($list as $key =>$val){
            $ids[] = $val["parentid"];
        }
        
        $parent = $mod_dict->find(array(
            "conditions" => "ID ".db_create_in($ids),
            'fields'     => "ID as id, NAME as name, ECODE as ecode"
        ));
        
        foreach($list as $key => $val){
            if(isset($parent[$val['parentid']])){
                $list[$key]["value"] = $parent[$val['parentid']];
            }
        }
        
        $ret = array();
        
        foreach($list as $key => $val){
            $str = mb_substr($val["name"],-1,1);
            if($str == "-"){
                $val["statusid"] = "10008";
            } 
            $data = array(
                "name" => $val["value"]["name"].":".$val["name"],
                "id"   => $val["id"],
                'code' => $val["ecode"],
                'pid'  => $val["parentid"],
                'sid'  => $val["statusid"],
            );
            $ret[] = $data;
        }
        die($this->json_result($ret));
    }
    
    function loadAssign(){
          $category  = trim($_GET['cat']);
          $this->assign("priceList", $this->init[$category]["dict"]);
          $content = $this->_view->fetch("assign.lib.html");
          die($this->json_result($content));
    }
    
    function itemInfo(){
        $ids  = trim($_POST['ids']);
        $type = trim($_POST['type']);
        $m = &m("dict");
        $list = $m->find(array(
            'conditions' => "id ". db_create_in($ids),
            'fields'     => "id,name,parentid",
        ));
        
        $res = array();
        foreach((array)$list as $key => $val){
            $res[$val["parentid"]]['children'][] = $val;
        }
        
        $this->assign("list", $res);
        $this->assign("hasC", $_POST['hasC']);
        $this->assign("type", $_POST['type']);
        $content = $this->_view->fetch("iteminfo.html");
        $this->json_result($content);
        die();
    }
    
    function fabInfo(){
        $ids = trim($_POST['ids']);
        $m = &m("fabs");
        $list = $m->find(array(
            'conditions' => "ID ". db_create_in($ids),
        ));
        $this->assign("list", $list);
        $content = $this->_view->fetch("fabinfo.html");
        $this->json_result($content);
        die();
    }
    
    function edit(){
        if(IS_POST){
            $name      = trim($_POST['name']);
            $brief     = trim($_POST['brief']);
            $style     = trim($_POST['style']);
            $category  = trim($_POST['category']);
            $source_img= trim($_POST['source_img']);
            $small_img = trim($_POST['small_img']);
            $item          = $_POST['item'];
            $assign          = $_POST['assign'];
           // $assignPrice  = $_POST["assignPrice"];
            //  $assignPrice  = $_POST["assignPrice"];
            $assignPrice = array();
            $service_price         = $_POST['service_price'];
            $is_default      = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $is_sale       = intval($_POST['is_sale']);
            $is_new       = intval($_POST['is_new']);
            $is_hot       = intval($_POST['is_hot']);
            $id           = intval($_POST['id']);
            $cat_id       = 0;
            $design_id    = intval($_POST['design_id']);
            $type_id      = intval($_POST['type_id']);
            $attr         = $_POST["attr"];
            $to_site      = $_POST['to_site'];
            $is_first      = intval($_POST['is_first']);
            $priceArr = $this->processPrice(array(
                'category'      => $category,
                'service_price' => $service_price,
                'fabric'        => $fab,
                'items'         => $item,
                'assignPrice'   => $assignPrice,
                'default'       => $is_default,
            ));
            
            $cusData = array(
                'name'     => $name,
                'brief'    => $brief,
                'style'    => $style,
                'category' => $category,
                'content'  => $content,
                'source_img'   => $source_img,
                'small_img'    => $small_img,
                'is_sale'      => $is_sale,
                'is_hot'       => $is_hot,
                'is_new'       => $is_new,
                'price'    => $priceArr["price"],
                'base_price'    => $priceArr["base_price"],
                'assignprice'   => serialize($assignPrice),
                'service_price' => $service_price,
                'cat_id'   => $cat_id,
                'design_id' => $design_id,
                'type_id'   => $type_id,
                'last_time' => gmtime(),
                'to_site'  => $to_site,
                'is_first' => $is_first,
            );
            
            if($is_sale == 0){
                $suit_mod = &m("suitrelat");
                
                $suits = $suit_mod->find(array(
                    "conditions" => "FIND_IN_SET('{$id}', jbk_id)",
                ));
                
                if(!empty($suits)){
                    $this->show_message("该样衣已关联套装，请解除关联后再执行下架操作!");
                    return false;
                }
            }
        
            $itemData = array();
            foreach((array)$item as $key => $val){
                $itemData[$key] = array(
                    'custom_id' => $id,
                    'item_id'   => $key,
                    'menu_id'   => $val,
                    'is_default'=> isset($is_default[$key]) && $is_default[$key] == $key ? 1 : 0,
                    'assign'    => $assign[$key],
                );
            }
            
            $fabData = array();
            foreach((array)$fabs as $key => $val){
                $fabData[$val] = array(
                    'custom_id'  => $id,
                    'item_id'  => $val,
                    'is_default' => $fab == $val ? 1 : 0,
                );
            }
            $this->_mod_custom->edit($id, $cusData);
            
            /********************************************************************************************/
            $items = $this->_mod_cusItem->find(array(
                'conditions' => "custom_id = '{$id}'",
            ));
            $fData = $this->formatData($items, $itemData);
            

            //新增
            if(!empty($fData["adData"])){
                $this->_mod_cusItem->add($fData["adData"]);
            }
            
            //移除
            if(!empty($fData["deData"])){
                $this->_mod_cusItem->drop($fData["deData"]);
            }
            
            //取消
            if(!empty($fData["dnData"])){
   
                $this->_mod_cusItem->edit($fData["dnData"], array("is_default" => 0));
            }
            
            //设置
            if(!empty($fData["upData"])){
                $this->_mod_cusItem->edit($fData["upData"], array("is_default" => 1));
            }
            
            //更新。临时解决办法
            if(!empty($fData['asData'])){
                foreach($fData['asData'] as $key => $val){
                    $this->_mod_cusItem->edit($val["id"], array("assign" => $val["assign"]));
                }
            }
            
            /********************************************************************************************/
            
            /********************************************************************************************/
            $items = $this->_mod_cusFab->find(array(
                'conditions' => "custom_id = '{$id}'",
            ));
            

            
            $fData = $this->formatData($items, $fabData);

            //新增
            if(!empty($fData["adData"])){
                $this->_mod_cusFab->add($fData["adData"]);
            }
            
            //移除
            if(!empty($fData["deData"])){
                $this->_mod_cusFab->drop($fData["deData"]);
            }
            
            //取消
            if(!empty($fData["dnData"])){
                $this->_mod_cusFab->edit($fData["dnData"], array("is_default" => 0));
            }
            
            //设置
            if(!empty($fData["upData"])){
                $this->_mod_cusFab->edit($fData["upData"], array("is_default" => 1));
            }
            /********************************************************************************************/
            
            $attrlist = $this->_mod_cusAttr->find(array(
               "conditions" => "custom_id='{$id}'", 
            ));
            
            $upAttr = array();
            $deAttr = array();
            foreach((array) $attrlist as $key => $val){
                if(isset($attr[$val['attr_id']]) && !empty($attr[$val['attr_id']])){
                    if($attr[$val['attr_id']] != $val['attr_value']){
                        $upAttr[$val["attr_id"]] = $attr[$val['attr_id']];
                    }
                    unset($attr[$val["attr_id"]]);
                }else{
                    $deAttr[] = $val['id'];
                }
            }
            
            if(!empty($deAttr)){
                $this->_mod_cusAttr->drop($deAttr);
            }
            
            if(!empty($upAttr)){
                foreach($upAttr as $key => $val){
                    $this->_mod_cusAttr->edit("custom_id='{$id}' AND attr_id = '{$key}'", array("attr_value" => $val));
                }
            }
            
            if(!empty($attr)){
                $newAttr = array();
                foreach($attr as $key => $val){
                        if(!empty($val)){
                            $newAttr[] = array(
                                'custom_id' => $id,
                                'attr_id'   => $key,
                                'attr_value' => $val,
                            );
                         }
                   }
                   if(!empty($newAttr)){
                       $this->_mod_cusAttr->add($newAttr);
                   }
            }
            
            $this->addGallery($id);
            $this->addLink($id);
            
            $this->show_message("样衣编辑成功",'back_list', 'index.php?app=custom&page='.$_COOKIE['curr_page']);
        }else{
            import("dict.lib");
           
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $id = intval($_GET["id"]);
            $data = $this->_mod_custom->get($id);

            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));
            
            $fabrics = $this->_mod_cusFab->find(array(
                "conditions" => "custom_id = '{$data["id"]}'",
                'join'       => "belongs_to_fab",
                'fields'     => "fab.*,f.CODE,f.ID"
            ));
            
            $items = $this->_mod_cusItem->find(array(
               'conditions' => "custom_id = '{$data["id"]}'", 
               'join'       => "belongs_to_dict",
               'fields'     => "im.*,c.name,c.ecode"
            ));
            
            $ids = array();
            foreach((array) $items as $key => $val){
                $ids[] = $val['menu_id'];
            }
            
            $mod_dict = &m("dict");
            $list = $mod_dict->find(array(
                "conditions" => "id ".db_create_in($ids),
                'fields'     => "name",
            ));
            
            foreach($items as $key => $val){
                if(isset($list[$val["menu_id"]])){
                    $items[$key]['name'] = $list[$val["menu_id"]]["name"].":".$val["name"];
                }
            }
            
            $this->assign("options", $this->_get_options());
            
            $design_list = $this->_mod_designer->find();
            
            $dsn = array();
            foreach($design_list as $key => $val){
                $dsn[$val["id"]] = $val["username"];
            }
            
            $this->assign("design_list", $dsn);
            
            $typelist = $this->_mod_type->find();
            $types = array();
            foreach($typelist as $key => $val){
                $types[$val["type_id"]] = $val["type_name"];
            }
            
            $this->assign("types", $types);
            
            $link_mod = &m("cusLink");
            
            $links = $link_mod->find(array(
               "conditions" => "link.custom_id='{$id}'",
               'join'       => "belongs_to_custom",
               'fields'     => "c.id as cid,c.name,link.id as linkid",
            ));

            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
            $info = $_cate_mod->get_info($data["cat_id"]);
            $attrlist = array();
            if($info['attrid']){
                $attrlist = $this->_mod_attribute->find(array(
                    "conditions" => "attr_id IN ({$info["attrid"]})",
                    'order'      => "sort_order ASC",
                ));
            }
            
            foreach($attrlist as $key => $val){
                $attrlist[$key]['values'] = explode(",", $val['attr_values']);
            }

            $linkAttr = $this->_mod_cusAttr->find(array(
               'conditions' => "custom_id = '{$id}'", 
            ));
            
            $linkAttrs = array();
            
            $gallery_list = $this->_mod_cusGallery->find(array(
                'conditions' => "custom_id = '{$id}'",
                'order' => 'sort ASC',
            ));
            
            $this->assign("gallery_list", $gallery_list);
            
            foreach((array) $linkAttr as $key => $val){
                $linkAttrs[$val["attr_id"]] = $val["attr_value"];
            }
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );
            //$this->assign("priceList", $this->init[$data["category"]]["dict"]);
            $this->assign("to_site", $this->to_site);
            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->assign("attrlist", $attrlist);
            $this->assign("linkattrs", $linkAttrs);
            $this->assign("assignPrice", unserialize($data["assignprice"]));
            $this->assign("items", $items);
            $this->assign("fabrics", $fabrics);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->assign("links",      $links);
            $this->assign("data", $data);
            $this->display("custom.info.html");
        }
    }

        // 添加图片排序功能 tangshoujain   start 
      function gallarysort(){
        $gllary = $_POST['photo_id'];
        $tmpAttr = explode(",", $gllary);
        $tmpAttrArray = array();
        $this->_mod_cusGallery = &m("customgallery");

        foreach ($tmpAttr as $key => $val) {
            $tmpV = explode(":", $val);
            $tmpAttrArray[$tmpV[0]] = $tmpV[1];

        }
      
        $arr = array();
        foreach ($tmpAttrArray as $k => $v) {
          
            $arr['sort']= $v;

            $gallery_sort = $this->_mod_cusGallery->edit("id='$k'",$arr);
        }

        if($gallery_sort){

            echo "add success";
        }

    }
    /// end
    
    function formatData($sData, $adData){
        $upData = array(); //设置默认
        $dnData = array(); //取消默认
        $deData = array();
        $asData = array();
        foreach((array)$sData as $key => $val){
            if(isset($adData[$val["item_id"]])){
                $is_defalut = $adData[$val['item_id']]['is_default'];
                if($val['is_default'] < $is_defalut){
                    $upData[] = $val["id"];
                }
        
                if($val["is_default"] > $is_defalut){
                    $dnData[] = $val["id"];
                }
                
                if($val["assign"] != $adData[$val["item_id"]]["assign"]){
                    $asData[] = array(
                        'id'     => $val["id"],
                        'assign' => $adData[$val["item_id"]]["assign"],
                    );
                }
                
                unset($adData[$val["item_id"]]);
            }else{
                $deData[] = $val["id"];
            }
        }
      
       $newArr = array();
       foreach($adData as $key => $val){
           $newArr[] = $val;
       }
       return array(
           'adData' => $newArr,
           'upData' => $upData,
           'dnData' => $dnData,
           'deData' => $deData,
           'asData' => $asData,
       );
    }
    
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
        
        $gcategories = $_cate_mod->get_list();
        $tree =& $this->_tree($gcategories);
    
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
    
    function drop(){
        $id = intval($_GET['id']);
        
        $suit_mod = &m("suitrelat");
        
        $suits = $suit_mod->find(array(
           "conditions" => "FIND_IN_SET('{$id}', jbk_id)", 
        ));
        
        if(!empty($suits)){
            $this->show_message("该样衣已关联套装，请解除关联后再执行删除操作!");
            return false;
        }
        $this->_mod_cusItem->drop("custom_id='{$id}'");
        
        $this->_mod_cusFab->drop("custom_id='{$id}'");
        
        $this->_mod_cusAttr->drop("custom_id='{$id}'");
        
        $cusLink = &m("cusLink");
        
        $cusLink->drop("custom_id='{$id}'");
        
        $gallerys = $this->_mod_cusGallery->find(array(
            'conditions' => "custom_id='{$id}'",
        ));
        
        foreach($gallerys as $key => $img){
            if($img){
                if(is_file(ROOT_PATH."/".$img['small_img'])){
                    unlink(ROOT_PATH."/".$img['small_img']);
                }
            
                if(is_file(ROOT_PATH."/".$img['middle_img'])){
                    unlink(ROOT_PATH."/".$img['middle_img']);
                }
            }
        }
        
        $this->_mod_cusGallery->drop("custom_id='{$id}'");
        $this->_mod_custom->drop($id);
       $this->show_message("样衣删除成功");
    }
    
    
    function loadAttr(){
        $id = intval($_GET['id']);
        
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
        $info = $_cate_mod->get_info($id);
        if(empty($info)){
            $this->json_result();
            die();
        }
        $attrlist = $this->_mod_attribute->find(array(
           "conditions" => "attr_id IN ({$info["attrid"]})", 
           'order'      => "sort_order ASC",      
        ));
        
        foreach($attrlist as $key => $val){
            $attrlist[$key]['values'] = explode(",", $val['attr_values']);
        }
        $this->assign("attrlist", $attrlist);
        $content = $this->_view->fetch("attrinfo.html");
        $this->json_result($content);
        die();
    }
    
    function addGallery($id){
        //相册图片
        import("image.func");
        $gallery = array();
        foreach((array)$_POST['gallery'] as $k => $v){
            $f=dirname($v);
            $fname = str_replace($f, '',$v);
            $t = date("YmdHi");
            $sPath = '/upload/thumb/s/'.$t.$fname;
            $mPath = '/upload/thumb/m/'.$t.$fname;
        
            $s_img = make_thumb($v, ROOT_PATH.$sPath,$this->sw, $this->wh);
        
            $m_img = make_thumb($v, ROOT_PATH.$mPath,$this->mw, $this->mh);
        
            if($s_img && $m_img){
                $gallery[] = array(
                    'custom_id' => $id,
                    'small_img'  => $sPath,
                    'middle_img'  => $mPath,
                    'source_img'    => $v,
                );
            }
        }
        
        if(!empty($gallery)){
            $this->_mod_cusGallery->add($gallery);
        }
    }
    
    function drop_gallery(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
        $img = $this->_mod_cusGallery->get_info($id);
    
        if($img){
            if(is_file(ROOT_PATH."/".$img['small_img'])){
                unlink(ROOT_PATH."/".$img['small_img']);
            }
    
            if(is_file(ROOT_PATH."/".$img['middle_img'])){
                unlink(ROOT_PATH."/".$img['middle_img']);
            }
        }
    
        if ($this->_mod_cusGallery->drop($id))
        {
            $this->json_result('drop_ok');
            return;
        }
        else
        {
            $this->json_error('drop_error');
            return;
        }
    }
    
    function loadCustom(){
        $ids = trim($_GET['ids']);
        if(!$ids){
            $this->json_error('非法操作');
            return;
        }
        
        $list = $this->_mod_custom->find(array(
            "conditions" => "id IN ($ids)",
            'fields'     => "id,name"
        ));
        
        $retArr = array();
        
        foreach($list as $key => $val){
            $retArr[] = $val;
        }
        
        $this->json_result($retArr);
        die();
    }
    
    function addLink($custom_id){
        $recommend = $_POST['newRecommend'];
        $link_mod = &m("cusLink");
        $data = array();
        $arr  = array();
       
        $link_mod = &m("cusLink");
        $links    = $link_mod->find(array(
           "conditions" => "custom_id='{$custom_id}'", 
        ));
        
        $deLink = array();
        foreach((array) $links as $key => $val){
            if(isset($recommend[$val['link_id']])){
                unset($recommend[$val["link_id"]]);
            }else{
                $deLink[] = $val['id'];
            }
        }

        if(!empty($deLink)){
            $link_mod->drop($deLink);
        }
        
        if(!empty($recommend)){
                foreach((array)$recommend as $key => $val){
                    if($custom_id != $val && !in_array($val, $arr)){
                        $data[] = array(
                            "custom_id"   => $custom_id,
                            'link_id'     => $val,
                        );
                    }
                    $arr[] = $val;
                }
                
                if(!empty($data)){
                    $link_mod->add($data);
                }
          }
    }
    
    function removeLink(){
        $id = intval($_GET["id"]);
        $link_mod = &m("cusLink");
        $link_mod->drop($id);
        die($this->json_result());
    }
    
    
    function processPrice($data = array()){
        
        $coef = include_once ROOT_PATH . '/data/coef.inc.php';
        
        $mod_fabric = &m("fabs");
        $mod_dict   = &m("dict");
        $category = $this->init[$data["category"]];
        
        //面料价格---------------------------------------
        $fabricePrice = 0;
        if($data["fabric"]){
            $info= $mod_fabric->find(array(
                "conditions" => "fp.AREAID = '20151' AND f.ID='{$data["fabric"]}'",
                'join'       => "belongs_to_price",
                'fields'     => "fp.RMBPRICE"
            ));
            
            $info = current($info);
            
            if(!empty($info)){
                $fabricePrice = $info["RMBPRICE"];
            }
        }
        
        $fabricePrice *= $category["mldh"];
        
        //里料价格---------------------------------------
        $llPrice = 0;
        $llId = 0;

        foreach((array) $data["items"] as $key => $val){
            if($val == $category["ll"] && isset($data['default'][$key])){
                $llId = $key;
            }
        }
        if($llId){
            $ll = $mod_dict->get("id='{$llId}'");
            if($ll){
                $llPrice = $ll["price"];
            }
        }
        
        $llPrice *= $category["lldh"];
        
        //指定工艺-----------------------------------------
        $assignPrice =0;
        foreach((array)$data["assignPrice"] as $key => $val){
            //$assignPrice += $val;
        }
        
        $base_price = $fabricePrice + $llPrice + $assignPrice + $data["service_price"];
        
        $price = $fabricePrice * $coef["fabric"] + $data["service_price"] * $coef["service"];
        
        //echo $fabricePrice."*".$coef["fabric"]."+".$data["service_price"]."*".$coef["service"];
        //die();
        return array("base_price" => $base_price, 'price' => round($price,0));
    }
}

?>
