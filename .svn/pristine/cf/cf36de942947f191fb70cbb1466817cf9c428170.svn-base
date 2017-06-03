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
    private $_mod_gcategory;
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
         $this->_mod_gcategory  = &m("gcategory");
         $this->_mod_cusGallery = &m("customgallery");
         $this->style = array(
             "1" => "正装",
             "2" => "休闲",
             "3" => "礼服"
         );
         $this->catee=array(
         		'0' =>'请选择',
         		'0003' => '男单西',
         		'0004'=>'男单裤',
         		'0030' => '男套西',
         		'0031' => '男套裤',
         		'0032' => '男礼服',
         		'0007'=>'男大衣',
         		'0005' => '男马甲',
         		'0006'=>'男衬衣',
         		'0017'=>'男短裤',
         		'0011' => '女单西',
         		'0012'=>'女单裤',
         		'0033' => '女套西',
         		'0034' => '女套裤',
         		'0021'=>'女大衣',
         		'0035' => '女马甲',
         		'0016'=>'女衬衣',
         
         );
         parent::__construct();
    }
  

    function inventorys()
    {
    	$conditions = ' 1 ';
    	$query['name']     = isset($_POST['name']) ? trim($_POST['name']) : '';
    	$query['to_site']  = isset($_POST['to_site']) ? trim($_POST['to_site']) : '';
    	$query['order_name']  = isset($_POST['order_name']) ? trim($_POST['order_name']) : '';
    	$query['custom_num']  = isset($_POST['custom_num']) ? trim($_POST['custom_num']) : '';
    	$query['specimen']  = isset($_POST['specimen']) ? trim($_POST['specimen']) : '';
    	$inventory  = intval($_POST['inventorys']) ? trim($_POST['inventorys']) : '';
    	$suff  = isset($_POST['suffs']) ? trim($_POST['suffs']) : '';
    	if($query["name"]){
    		$conditions .= " AND name LIKE '%{$query["name"]}%'";
    	}
    	if($query["custom_num"]){
    		$conditions .= " AND custom_num LIKE '%{$query["custom_num"]}%'";
    	}
    	if($query["specimen"]){
    		$conditions .= " AND specimen LIKE '%{$query["specimen"]}%'";
    	}
    	
    	if($query["to_site"]){
    		$conditions .= " AND to_site = '{$query["to_site"]}'";
    	}
    	if(!empty($query["order_name"])){
    		$conditions .= " AND cat_id = '{$query["order_name"]}' ";
    	}
    	
    	//$page = $this->_get_page(30);
    	//setcookie('curr_page',$page["curr_page"]);
    	$custom_list = $this->_mod_custom->find(array(
    			'conditions' => $conditions,
    			//'limit' => $page['limit'],
    			'count' => true,
    			'index_key'=>"",
    			'order' => "id ASC"
    	));
    	
    	$user_array=i_array_column($custom_list,'id');
    	$user_id=db_create_in($user_array,'custom_id');
    	$conditions1="{$user_id}";
    	$fields='inventory_num';
    	//var_dump($_POST['parents']);
    	
    	if(!empty($suff) && !empty($inventory)){
    		if($suff ==1){
    			$adls=$this->_mod_cusAttr->setInc($conditions1,$fields,$inventory);
    			if($adls)
    			{
    				$this->json_result($adls);
    				return;
    			}
    				
    		}elseif($suff ==2)
    		{
    			$attrs=$this->_mod_cusAttr->find(array(
    					'conditions' =>"$conditions1",
    			));
    			 
    			foreach($attrs as $key=>$val)
    			{
    				if($val['inventory_num']<=$inventory)
    				{
    					/* $attr_in[]=array(
    					 'id'=>$val['id'],
    							'inventory_num'=>$val['inventory_num'],
    					); */
    					$attr_in[]=$val['id'];
    				}else{
    					$attr_dec[]=array(
    							'id'=>$val['id'],
    							'inventory_num'=>$val['inventory_num'],
    					);
    					 
    				}
    			}
    			 
    			if($attr_in)
    			{
    				/* $attr_ins=i_array_column($attr_in,'id'); */
    				$attr_id=db_create_in($attr_in,'id');
    				/* $this->_mod_cusAttr->drop($attr_id); */
    				$daly=array(
    						'inventory_num'=>0,
    				);
    				$this->_mod_cusAttr->edit(db_create_in($attr_in,'id'),$daly);
    			}
    			if($attr_dec)
    			{
    				$attr_dec=i_array_column($attr_dec,'id');
    				$attr_decs=db_create_in($attr_dec,'id');
    				$this->_mod_cusAttr->setDec($attr_decs,$fields,$inventory);
    			}
    			$res=1;
    			$this->json_result($res);
    			return;
    		}
    	}
    	
    	
    }
    function index()
    {
        $conditions = ' 1 ';
        $query['name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
        $query['to_site']  = isset($_GET['to_site']) ? trim($_GET['to_site']) : '';
        $query['order_name']  = isset($_GET['order_name']) ? trim($_GET['order_name']) : '';
        $query['custom_num']  = isset($_GET['custom_num']) ? trim($_GET['custom_num']) : '';
        $query['specimen']  = isset($_GET['specimen']) ? trim($_GET['specimen']) : '';
        if($query["name"]){
            $conditions .= " AND name LIKE '%{$query["name"]}%'";
        }
        if($query["custom_num"]){
        	$conditions .= " AND custom_num LIKE '%{$query["custom_num"]}%'";
        }
        if($query["specimen"]){
        	$conditions .= " AND specimen LIKE '%{$query["specimen"]}%'";
        }
        
        if($query["to_site"]){
            $conditions .= " AND to_site = '{$query["to_site"]}'";
        }
         if(!empty($query["order_name"])){
        	$conditions .= " AND cat_id = '{$query["order_name"]}' ";
        }
        $page = $this->_get_page(30);
        
      
        setcookie('curr_page',$page["curr_page"]);
        $custom_list = $this->_mod_custom->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
        	'index_key'=>"",
            'order' => "id ASC"
        ));
      
    
      $query_orders= $this->_mod_gcategory->find(array(
       		'conditions' =>'1=1',
      		'fields' =>"cate_name",
      		"order" =>"sort_order",
       ));
      foreach($query_orders as $key=>$val)
      {
      	$query_order[$key]=$val["cate_name"];
      }
        $page['item_count'] = $this->_mod_custom->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('query', $query);
        
        //print_R($page);
        $index = $page["curr_page"] == 1 ? 1 : ($page["curr_page"]-1)*$page["pageper"]+1;
        foreach($custom_list as $key =>$val){
        	$custom_list[$key]["index"] = $index;
        	$index+=1;
        }
        
        $this->assign("custom_list", $custom_list);
        $this->assign("to_site", $this->to_site);
        $this->assign("options", $this->_get_options());
          $this->assign('query_order',$query_order);
         
          
        $this->display('custom.index.html');
    }
    
    function add(){
        
        if(IS_POST){
            $name      = trim($_POST['name']);
            $brief     = trim($_POST['brief']);
            $style     = trim($_POST['style']);
            $category  = trim($_POST['category']);
            $source_img= trim($_POST['source_img']);
            $size_img= trim($_POST['size_img']);
            $small_img= trim($_POST['small_img']);
            //$cusprice= trim($_POST['cusprice']);
            $price= trim($_POST['price']);
            $is_figure     = trim($_POST['is_figure']);//0标准码1量体定制
            $item          = $_POST['item'];
            $assign          = $_POST['assign'];
          //  $assignPrice  = $_POST["assignPrice"];
            $assignPrice = array();
            $service_price         = $_POST['service_price'];
            $is_default      = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $specimen    = trim($_POST['specimen']);//款式编号
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $to_site       = $_POST['to_site'];
            $is_sale       = intval($_POST['is_sale']);
            $is_new       = intval($_POST['is_new']);
            $is_hot       = intval($_POST['is_hot']);
            $custom_num       = trim($_POST['custom_num']);
            $cat_id       = intval($_POST['cat_id']);
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
            	//'cusprice'=>$cusprice,
            	'is_figure'    => $is_figure,
            	'cat_id'=>$cat_id,
            	'specimen'  => $specimen,
            	'custom_num'  => $custom_num,
                'content'  => $content,
                'source_img'   => $source_img,
            	'size_img'   => $size_img,
                'small_img'    => $source_img,
                //'price'    => $priceArr["price"],
            	'price'    => $price,
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
            if($_POST['is_figure'] != 1)
            {
            	foreach((array)$attr as $key => $val){
            		$attr_id=$key;
            		foreach($val as $k=>$v)
            		{
            			if($v){
            				$attrData[] = array(
            						"attr_id"     => $attr_id,
            						'attr_value'  => $v,
            						'attr_id_son' =>$k,
            						"custom_id"   => $custom_id,
            						'inventory_num' =>1,
            				);
            			}
            		}
            	
            	}
            	if(!empty($attrData)){
            		$this->_mod_cusAttr->add($attrData);
            	}
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
            $query_orders= $this->_mod_gcategory->find(array(
            		'conditions' =>'1=1',
            		'fields' =>"cate_name",
            		"order" =>"sort_order",
            ));
            foreach($query_orders as $key=>$val)
            {
            	$query_order[$key]=$val["cate_name"];
            }
            $this->assign("types", $types);
            $this->assign("design_list", $dsn);
            $this->assign("options", $query_order);
            $this->assign("to_site", $this->to_site);
            $this->assign("style_list", $this->style);
            $this->assign('cat_list',$this->catee);
            $this->display("custom.info.html");
        }
    }
    
    //单品库存
    function inventory()
    {
    	$id=$_GET['id'];
    	if($_POST['inventory_num'])
    	{
    		
    		$inventory_num=$_POST['inventory_num'];
    		if(!empty($inventory_num))
    		{
    			foreach($inventory_num as $key=>$val)
    			{
    				$datte=array(
    						'inventory_num'=>$val,
    				);
    				$this->_mod_cusAttr->edit($key,$datte);
    			}
    			
    			
    			
    		}
    	}
    	$cusattrs=$this->_mod_cusAttr->find(array(
    			'conditions' =>"custom_id='{$id}'",
    	));
    	 $this->assign('cusattr',$cusattrs);
    
    	$this->display('inventory.index.html');
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
            $size_img= trim($_POST['size_img']);
           // $cusprice= trim($_POST['cusprice']);
            $price= trim($_POST['price']);
            $small_img = trim($_POST['small_img']);
            $item          = $_POST['item'];
            $specimen    = trim($_POST['specimen']);//款式编号
            $is_figure     = trim($_POST['is_figure']);//0标准码1量体定制
            $assign          = $_POST['assign'];
           // $assignPrice  = $_POST["assignPrice"];
            //  $assignPrice  = $_POST["assignPrice"];
            $assignPrice = array();
            $service_price         = $_POST['service_price'];
            $is_default      = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $custom_num       = trim($_POST['custom_num']);
            $is_sale       = intval($_POST['is_sale']);
            $is_new       = intval($_POST['is_new']);
            $is_hot       = intval($_POST['is_hot']);
            $cat_id       = intval($_POST['cat_id']);
            $id           = intval($_POST['id']);
            $design_id    = intval($_POST['design_id']);
            $type_id      = intval($_POST['type_id']);
            $attr         = $_POST["attr"];
            //var_dump($attr);exit;
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
            	'cat_id'=>$cat_id,
            	'cusprice'=>$cusprice,
                'content'  => $content,
            	'is_figure'    => $is_figure,
            	'specimen'  => $specimen,
            	'custom_num'  => $custom_num,
                'source_img'   => $source_img,
            	'size_img'   => $size_img,
                'small_img'    => $small_img,
                'is_sale'      => $is_sale,
                'is_hot'       => $is_hot,
                'is_new'       => $is_new,
                //'price'    => "10000", //=====  写死价格  =====
            	'price'    => $price,
                'base_price'    => "10001",
                'assignprice'   => serialize($assignPrice),
                'service_price' => $service_price,
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
           // var_dump($cusData);exit;
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
               //'index_key' =>"attr_id",
            ));
             
            if($attrlist)
            {
            	foreach($attrlist as $key=>$val)
            	{
            		$attrlists[$val['attr_id']][$val['attr_id_son']]=$val['attr_value'];
            		 
            	}	
            }
            $upAttr = array();
            $deAttr = array();
            if($attrlists){
            	foreach($attrlists as $key => $val){
            		
            		if(isset($attr[$key])){
            			
            			foreach($val as $vk => $vv){
            				
            				if(in_array($vv, $attr[$key])){
            					
            					$uk = array_search($vv,$attr[$key]);
            					unset($attr[$key][$uk]);
            	
            				}else{
            					//	$deAttr[] = $vv;
            					$deAttr[] = array(
            							'attr_value'=>$vv,
            							'custom_id' =>$id,
            							'attr_id' =>$key,
            					);
            				}
            			}
            		}else{
            			
            			if($val){
            				foreach($val as $k=>$v){
            					$deAttr[]= array(
            					'attr_value'=>$v,
            					'custom_id' =>$id,
            					'attr_id' =>$key,
            			);
            				}
            				
            			}
            		
            		}
            	}
            }
        
          if(!empty($deAttr))
          {
          	foreach($deAttr as $key=>$val)
          	{
          		$cusattr_id=$this->_mod_cusAttr->get(array(
          				'conditions'=>"attr_value='{$val['attr_value']}' AND custom_id='{$val['custom_id']}' AND attr_id='{$val['attr_id']}'",
          		));
          	
          		$this->_mod_cusAttr->drop($cusattr_id['id']);
          	}
          	
          }
            if(!empty($attr)){
                $newAttr = array();
                foreach($attr as $key => $val){
                
                	foreach($val as $k=>$v)
                	{
                		if(!empty($v)){
                			$newAttr[] = array(
                					'custom_id' => $id,
                					'attr_id'   => $key,
                					'attr_value' => $v,
                					'attr_id_son'=>$k,
                					'inventory_num' =>1,
                			);
                		}
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
            $query_orders= $this->_mod_gcategory->find(array(
            		'conditions' =>'1=1',
            		'fields' =>"cate_name",
            		"order" =>"sort_order",
            ));
            foreach($query_orders as $key=>$val)
            {
            	$query_order[$key]=$val["cate_name"];
            }
        //$this->assign('cat_list',$this->catee);
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
            	
                $linkAttrs[$val['attr_id']][$val['attr_id_son']]= $val["attr_value"];
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
            $this->assign("options",$query_order);
            $this->assign("assignPrice", unserialize($data["assignprice"]));
            $this->assign("items", $items);
            $this->assign("fabrics", $fabrics);
            $this->assign("style_list", $this->style);
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
    
    
    
    function checksuit()
    {
    	$id = intval($_POST['id']);
    	$suit_mod = &m("suitrelat");
    	$suit_list= &m('suitlist');
    	$suits = $suit_mod->find(array(
    			"conditions" => "FIND_IN_SET('{$id}', jbk_id)",
    			'fields'=>"tz_id",
    	));
    	
    	if(!empty($suits)){
    		$suitlists=i_array_column($suits,'tz_id');
    		$suit_ids=db_create_in($suitlists,'id');
    		$suit_name=$suit_list->find(array(
    				'conditions' =>$suit_ids,
    				'fields' =>'suit_name'
    		));
    		$suitlists=i_array_column($suit_name,'suit_name');
    		$suit_names = implode(",", $suitlists);
    	}
    	
    	return $this->json_result($suit_names);
    }
    
    function drop(){
        $id = intval($_POST['id']);
        
        $suit_mod = &m("suitrelat");
        $suit_list= &m("suitlist");
        $custom= &m("custom");
        $suit_gallery=&m("suitgallery");
        $suit_link=&m("suitlink");
       $customs= $custom->get(array(
        		'conditions' =>"id = '{$id}'",
        		'fields' =>"price",
        ));
        $suits = $suit_mod->find(array(
           "conditions" => "FIND_IN_SET('{$id}', jbk_id)", 
        ));
        
        if(!empty($suits)){
        	foreach($suits as $key=>$val)
        	{
        		$jbk_id=explode(',',$val['jbk_id']);
        		
        		foreach($jbk_id as $k=>$v)
        		{
        			if(!empty($v))
        			{
        				$indes+=1;
        				
        			}
        			if($v == $id)
        			{
        				
        				$jbk_id[$k]=0;
        			}
        				
        		}
        		$jbk=implode(',', $jbk_id);
        		if($indes <=1)
        		{
        			//删除套装以及关联关系
        			$suit_re=$suit_mod->drop("id='{$val['id']}'");
        			/* if(!$suit_re)
        			{
        				$this->show_message("删除套装关联关系失败");
        				return false;
        			} */
        			$suit_li=$suit_list->drop("id='{$val['tz_id']}'");
        			/* if(!$suit_li)
        			{
        				$this->show_message("删除套装失败");
        				return false;
        			} */
        			//删除套装相册
        			$suit_ga=$suit_gallery->drop("suit_id='{$val['tz_id']}'");
        			/* if(!$suit_ga)
        			{
        				$this->show_message("删除套装相册失败");
        				return false;
        			} */
        			//删除推荐套装关系
        			$suit_lin=$suit_link->drop("s_id='{$val['tz_id']}'");
        			/* if(!$suit_lin)
        			{
        				$this->show_message("删除推荐套装关系失败");
        				return false;
        			} */
        		}else{
        			//套装关联表改数据
        			$dates=array(
        					'jbk_id' =>$jbk,
        			);
        			$suit_mod->edit($val['id'],$dates);
        			//套装价格修改
        			$conditions="id = '{$val['tz_id']}'";
        			$field='price';
        			$suit_list->setDec($conditions,$field,$customs['price']);
        		}
        	}
        	
        }
        $cusattrs=$this->_mod_cusAttr->get(array(
        		'conditions' =>"custom_id='{$id}'",
        ));
        
        if($cusattrs)
        {
        	$ddrop=$this->_mod_cusAttr->drop("custom_id='{$id}'");
        	if(!$ddrop)
        	{
        		$this->show_message("删除关联库存失败");
        		return false;
        	}
        }
        
        $this->_mod_cusItem->drop("custom_id='{$id}'");
        
        $this->_mod_cusFab->drop("custom_id='{$id}'");
        
       
        
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
