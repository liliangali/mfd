<?php
class DfabricApp extends BackendApp
{
    private $_mod_Fabs;
    private $_mod_Fabricprice;
    private $_mod_dict1;
    private $_mod_fabricgallery;
    
    var $sw = 120;
    var $sh = 150;
    var $mw = 300;
    var $mh = 300;
    function __construct()
    {
         $this->_mod_Fabs = &m("fabs");
         $this->_mod_Fabricprice = &m("fabricprice");
         $this->_mod_dict1 = &m('dict1');
         $this->_mod_fabricgallery=&m('fabricgallery');
         /* 手动调整成分与前端展示一致切忌 */		$this->_composition = array (				8001 => array (						128251 => '毛',						128099 => '毛丝', //丝						128100 => '麻',						128101 => '棉',						128187 => '涤'				),
				8030 => array (						128412 => '棉',						128413 => '麻',						52794 => '丝麻',//97S3LY 						12158 => '涤棉',//40C60T						52789 => '丝棉'//75%棉25%桑蚕丝				),
				8050 => array (						128214 => '毛',						128218 => '羊绒',						200520 => '毛棉',//73%棉23.44%尼龙%3.55%氨纶						200521 => '毛丝',//72.99%棉23.45%尼龙3.56%氨纶				)		);
         parent::__construct();
    }

    function index()
    {
//     	ALTER TABLE `diy_fabric` ADD `sort` INT(3) NOT NULL DEFAULT '0' COMMENT '排序' AFTER `CODE`, ADD `is_sale` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '上下架' AFTER `sort`, ADD INDEX (`sort`, `is_sale`) ;
    	$conditions = $this->_get_query_conditions(array(    			array(    					'field' => 'CODE',    					'name'  => 'field_CODE',    					'equal' => 'like',    			),
    			array(    					'field' => 'tname',    					'name'  => 'field_CODE',    					'equal' => 'like',
    					'assoc' => 'OR',               			)    	));
    	
    	//更新排序    	if (isset($_GET['sort']) && isset($_GET['order']))    	{    		$sort  = strtolower(trim($_GET['sort']));    		$order = strtolower(trim($_GET['order']));    		if (!in_array($order,array('asc','desc')))    		{    			$sort  = 'is_first';    			$order = 'desc';    		}    	}    	else    	{    		$sort  = 'sort';    		$order = 'desc';    	}
//     	echo "$sort $order";
    	$_GET['CATEGORYID'] = empty($_GET['field_CATEGORYID']) ? '8001' : $_GET['field_CATEGORYID'];
        $page = $this->_get_page(30);
        $f_list = $this->_mod_Fabs->find(array(
        	"conditions" =>'1=1 and CATEGORYID='.$_GET['CATEGORYID'].$conditions,
        	'fields'     => "f.*",
            'limit' => $page['limit'],
            'count' => true,
            'order'         => "$sort $order",
//          	'join' =>"has_gallery_id",
        ));
//        echo '1=1 and CATEGORYID='.$_GET['CATEGORYID'].$conditions;
        //获取 面料价格        $ids = array();        $chenfen = i_array_column($f_list, 'COMPOSITIONID');//=====  面料成分  =====
        $dict1_list = array();
        if ($chenfen) 
        {
            $chenfen = array_unique($chenfen);
// print_exit(db_create_in($chenfen,"COMPOSITIONID"));
            $dict1_list = $this->_mod_dict1->find(array(
                'conditions' => db_create_in($chenfen,"ID"),
            ));
//  print_exit($dict1_list);
            foreach ($f_list as $key => &$value) 
            {
                /* $value['chengfen'] = "";
                if ($dict1_list[$value['COMPOSITIONID']]['NAME']) 
                {
                    $value['chengfen'] = $dict1_list[$value['COMPOSITIONID']]['NAME'];
                } */
            }
        }
               /*  $fp_list = $this->_mod_Fabricprice->findAll(array(        		'conditions'    => db_create_in($pids,'FABRICCODE').' and AREAID =20151',    //地区默认 20151
        		'index_key' => 'FABRICCODE',        ));
        foreach ($f_list as &$r){
        	$r['rmbprice'] = '0.00';
        	if ($fp_list[$r['CODE']]){
        		$r['rmbprice'] =  $fp_list[$r['CODE']]['RMBPRICE'];
        	}
        } */
        /* 展示面料相册图片 和增删改 by zxr*/
/*         $param = array(
        		'dir' => 'gallery',
        		'button_text' => "上传相册图片"
        );
        $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件 */
        $f_ids=join(',', i_array_column($f_list, 'ID'));
        $gallery_list = $this->_mod_fabricgallery->find(array(
        		'conditions' => "1=1 AND ".db_create_in($f_ids,'fabric_id'),
        		'fields'=>'*',
        ));
        foreach ($f_list as $key=>$value){
        	foreach ($gallery_list as $k=>$v){
        		if($v['fabric_id']==$value['ID']){
        			$f_list[$key]['gallery'][]=$v;
        		}
        	}
        }
        /* END */
        $page['item_count'] = $this->_mod_Fabs->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('dfpid', $_GET['CATEGORYID']);
        $this->assign("f_list", $f_list);
        $this->assign("c_list",  $this->_composition[$_GET['CATEGORYID']]);
        $this->assign("tname", $this->_mod_Fabs->_typeid);
        $this->import_resource(array(        		'script' => 'jqtreetable.js,inline_edit_admin.js',        		'style'  => 'res:style/jqtreetable.css')        );
        
        $this->display('dfabric.index.html');
    }

    //异步修改数据    function ajax_col()    {	    	$id     = empty($_GET['id']) ? 0 : trim($_GET['id']);    	$column = empty($_GET['column']) ? '' : trim($_GET['column']);    	$value  = isset($_GET['value']) ? trim($_GET['value']) : '';
    	    	if (in_array($column ,array('SHAZHI','chengfen','color','price', 'sort','is_sale','is_first','activity','activity_price_0003','activity_price_0004','activity_price_0005','activity_price_0006','activity_price_0007','tname','STOCK','VCOMPOSITIONID')))    	{
    		if ($column == 'SHAZHI')
    		{
    		    $conditions = 'CODE = "'.$id.'"';
    			$this->_mod_Fabs->edit($conditions, array('SHAZHI'=>$value));
    			if(!$this->_mod_Fabs->has_error())
    			{
    				echo ecm_json_encode(true);
    			}
    		}
    		elseif ($column == 'chengfen')
    		{
    		    $conditions = 'CODE = "'.$id.'"';
    		    $fabs_info = $this->_mod_Fabs->get($conditions);
    		    if (!$fabs_info) 
    		    {
    		        echo ecm_json_encode(false);
    		        return;
    		    }
    		    $this->_mod_Fabs->edit($conditions,array("chengfen"=>$value));
    		    echo ecm_json_encode(true);
    		    
    		    
    		    //$COMPOSITIONID = $fabs_info['COMPOSITIONID'];
    		    //$dict1_info = $this->_mod_dict1->get_info($COMPOSITIONID);
    		    /* if ($dict1_info) 
    		    {
    		        $this->_mod_dict1->edit($COMPOSITIONID, array('NAME'=>$value));
    		        if(!$this->_mod_dict1->has_error())
    		        {
    		            echo ecm_json_encode(true);
    		        }else{
    		        	echo ecm_json_encode(false);
    		        }
    		    }else{
    		    	echo ecm_json_encode(false);
    		    }
    		     */
    		    
    		}    		elseif ($column == 'color')
    		{
    			$conditions = 'CODE = "'.$id.'"';
    			$fabs_info = $this->_mod_Fabs->get($conditions);
    			if (!$fabs_info)
    			{
    				echo ecm_json_encode(false);
    				return;
    			}
    			$this->_mod_Fabs->edit($conditions,array("color"=>$value));
    			echo ecm_json_encode(true);
    		}    	}    	else    	{    		return ;    	}    	return ;    }
    
    /* 异步上传面料相册 */
    function ajax_upload_gallery(){
    	$picname = $_FILES['mypic']['name'];
    	$picsize = $_FILES['mypic']['size'];
    	if ($picname != "") {
    		if ($picsize > 5120000) { //限制上传大小
//     			echo '图片大小不能超过500k';
//     			exit;
    			return $this->json_error('图片大小不能超过500k');
    		}
    		$type = strstr($picname, '.'); //限制上传格式
    		if ($type != ".gif" && $type != ".jpg" && $type!=".png") {
//     			echo '图片格式不对！';
//     			exit;
    			return $this->json_error('图片格式不对！');
    		}
    		$rand = rand(100, 999);
    		$pics = date("YmdHis") . $rand . $type; //命名图片名称
    		//上传路径
    		if(!is_dir(ROOT_PATH."/upload/images/dfabric/".date("Ymd"))){
    			$a=mkdir(ROOT_PATH."/upload/images/dfabric/".date("Ymd"),0777,true);
    		}
    		$pic_path = ROOT_PATH."/upload/images/dfabric/".date("Ymd")."/". $pics;
    		move_uploaded_file($_FILES['mypic']['tmp_name'], $pic_path);
    	}
    	$size = round($picsize/1024,2); //转换成kb
    	$source_url="/upload/images/dfabric/".date("Ymd")."/". $pics;
    	$id=$this->addGallery($_POST['id'],$_POST['CODE'],SITE_URL.$source_url);
    	if(!$id){
    		return $this->json_error('添加数据库失败');
    	}
    	$arr = array(
    			'file_id'=>$id,
    			'name'=>$picname,
    			'pic'=>$pics,
    			'size'=>$size,
    			'pic_path'=>$source_url,
    	);
    	return $this->json_result($arr);
//     	echo json_encode($arr); //输出json数据
    }
    
    /* 上传面料缩略图片 */
    function addGallery($id,$code,$path){
    	//相册图片
    	 
    	import("image.func");
    	$gallery = array();
    	$f=dirname($path);
    	$fname = str_replace($f, '',$path);
    	$t = date("Ymd");
    	if(!is_dir(ROOT_PATH."/upload/dfabric/s")){
    		mkdir(ROOT_PATH."/upload/dfabric/s",0777,true);
    	}
    	if(!is_dir(ROOT_PATH."/upload/dfabric/m")){
    		mkdir(ROOT_PATH."/upload/dfabric/m",0777,true);
    	}
    	$sPath = '/upload/dfabric/s/'.$t.$fname;
    	$mPath = '/upload/dfabric/m/'.$t.$fname;
    
    	$s_img = make_thumb($path, ROOT_PATH.$sPath,$this->sw, $this->sh);
    	$m_img = make_thumb($path, ROOT_PATH.$mPath,$this->mw, $this->mh);
    	if($s_img && $m_img){
    		$gallery[] = array(
    				'fabric_id' => $id,
    				'CODE'=>$code,
    				'small_img'  => $sPath,
    				'middle_img'  => $mPath,
    				'source_img'    => $path,
    		);
    	}
    	if(!empty($gallery)){
    		$res=$this->_mod_fabricgallery->add($gallery);
    		if($res){
    			return $res;
    		} else{
    			return false;
    		}
    	}
    	return false;
    }
    
    /* 删除面料图片 */
    function drop_gallery(){
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    	$img = $this->_mod_fabricgallery->get_info($id);
    
    	if($img){
    		if(is_file(ROOT_PATH."/".$img['small_img'])){
    			unlink(ROOT_PATH."/".$img['small_img']);
    		}
    
    		if(is_file(ROOT_PATH."/".$img['middle_img'])){
    			unlink(ROOT_PATH."/".$img['middle_img']);
    		}
    	}
    
    	if ($this->_mod_fabricgallery->drop($id))
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
    
}

?>