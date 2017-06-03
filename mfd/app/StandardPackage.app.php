<?php

class StandardPackageApp extends BackendApp
{
    private $_mod_standardpackage;
    private $_mod_fbcategory;
    private $standard_id='11';//规格id
    private $package_id='16';//包装id
    function __construct()
    {
         $this->_mod_standardpackage = &m("standardpackage");
         $this->_mod_fbcategory=&m('fbcategory');

        $guige_list = $this->_mod_fbcategory->get_list(11);
        $guige = [];
        foreach ((array)$guige_list as $index => $item)
        {
            $guige[$index] = $item['cate_name'];
        }

        $baozhuang_list = $this->_mod_fbcategory->get_list(16);
        $baozhuang = [];
        foreach ((array)$baozhuang_list as $index => $item)
        {
            $baozhuang[$index] = $item['cate_name'];
        }
        $this->assign('guige_list',$guige);
        $this->assign('baozhuang_list',$baozhuang);


         parent::__construct();
    }


    function index()
    {
    	$conditions = ' 1=1 ';
        $guige = isset($_REQUEST['guige']) ? $_REQUEST['guige'] : 0;
        $baozhuang= isset($_REQUEST['baozhuang']) ? $_REQUEST['baozhuang'] : 0;
        $this->assign('guige',$guige);
        $this->assign('baozhuang',$baozhuang);
        $conditions = '1=1';
        if($guige)
        {
            $conditions .= " AND standard_id = $guige";
        }
        if($baozhuang)
        {
            $conditions .= " AND package_id = $baozhuang";
        }



        $page = $this->_get_page(30);
    	setcookie('curr_page',$page["curr_page"]);
        $sql="select sp.*,fb1.cate_name as sname,fb2.cate_name as pname from cf_standard_package sp LEFT JOIN cf_fbcategory fb1 ON fb1.cate_id=sp.standard_id  LEFT JOIN cf_fbcategory fb2 ON fb2.cate_id=sp.package_id where {$conditions} ORDER BY sp_id   ASC LIMIT {$page['limit']}";
    	$standardpackage_list = $this->_mod_standardpackage->getAll($sql);
    	$sql="select count(*) as num from cf_standard_package sp  where {$conditions}";
        $page['item_count'] =$this->_mod_standardpackage->getOne($sql);
    	$this->_format_page($page);
    	$this->assign('standardpackage_list', $standardpackage_list);
        // var_dump($standardpackage_list);
    	$this->assign('page_info', $page);
        $this->display('standardpackage/standardpackage.index.html');
    }
    
     function add(){
     	$region_mod = m('fbcategory');
        if(IS_POST){
            $sp_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $sp_price     = !empty($_POST['price'])?$_POST['price']:0;
            $standard_id     = !empty($_POST['standard_id'])?$_POST['standard_id']:0;
            $package_id     = !empty($_POST['package_id'])?$_POST['package_id']:0;
            if(!$sp_price){
                return $this->show_warning('价格不能为空');
            }else if(!$standard_id){
                return $this->show_warning('请选择规格');
            }else if(!$package_id){
                return $this->show_warning('请选择包装');
            }
            if($this->_mod_standardpackage->get("standard_id={$standard_id} AND package_id={$package_id}")){
                return $this->show_warning('该规格和包装组合已被使用');
            }
            $data=array(
            		'sp_name'=>$sp_name,
            		'sp_price'=>$sp_price ,
            		'standard_id'=>$standard_id,
            		'package_id'=>$package_id,
            );
            $standardpackpackage_id =  $this->_mod_standardpackage->add($data);
            if($standardpackpackage_id){
            	$this->show_message("价格添加成功",'back_list', 'index.php?app=StandardPackage');
            }
        }else{
        	$list1 = $this->_mod_fbcategory->get_options($this->standard_id);
        	$this->assign('standards',$list1);
        	$list2 = $this->_mod_fbcategory->get_options($this->package_id);
        	$this->assign('packages',$list2);
            $this->display("standardpackage/standardpackage.info.html");
        }
    }
    function edit(){
    	$id = intval($_GET["id"]);
    	if(IS_POST){
    		$sp_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $sp_price     = !empty($_POST['price'])?$_POST['price']:0;
            $standard_id     = !empty($_POST['standard_id'])?$_POST['standard_id']:0;
            $package_id     = !empty($_POST['package_id'])?$_POST['package_id']:0;
            if(!$sp_price){
                return $this->show_warning('价格不能为空');
            }else if(!$standard_id){
                return $this->show_warning('请选择规格');
            }else if(!$package_id){
                return $this->show_warning('请选择包装');
            }
    		
    		// if($region){
    		// 	$p_region=$region_mod->get(array(
    		// 			'conditions'=>"region_id='{$region['parent_id']}'",
    		// 	));
    		// 	if($p_region['region_id'] == 3 || $p_region['region_id'] == 22 || $p_region['region_id'] == 41 || $p_region['region_id'] == 61 )
    		// 	{
    		// 		$region_id=$p_region['region_id'];
    		// 	}
    		// }
    		/*
    		if($region && $p_region){
    			
    			$region_name=$p_region['region_name']." ".$region['region_name'];
    		}*/
            $exist_id=$this->_mod_standardpackage->get("standard_id={$standard_id} AND package_id={$package_id}");
            if($exist_id && $id!=$exist_id['sp_id']){
                return $this->show_warning('该规格和包装已被使用');
            }
    		$data=array(
    				'sp_name'=>$sp_name,
                    'sp_price'=>$sp_price ,
                    'standard_id'=>$standard_id,
                    'package_id'=>$package_id,
    		);
    		$standardpackpackage_id =  $this->_mod_standardpackage->edit($id,$data);
    		if($standardpackpackage_id){
    			$this->show_message("价格编辑成功",'back_list', 'index.php?app=StandardPackage');
    		}
    
    	}else{
    		
    		$standardpackages=$this->_mod_standardpackage->get(array(
    				'conditions'=>"sp_id='{$id}'",
    		));
    		
    		
    		$list1 = $this->_mod_fbcategory->get_options($this->standard_id);
            $this->assign('standards',$list1);
            $list2 = $this->_mod_fbcategory->get_options($this->package_id);
            $this->assign('packages',$list2);
            $this->assign('data',$standardpackages);
    		// var_dump($list2);
    		$this->display("standardpackage/standardpackage.info.html");
    	}
    }
    
    function drop(){
    	$id = intval($_GET['id']);
        $this->_mod_standardpackage->drop($id);
    	$this->show_message("基料删除成功");
    	}
    /**
     *ajax获得三级联动
     *@author shaozz
     *@2015年7月14日
     */
    function get_region()
    {
    	$region_mod = m('region');
    	$pid = $_POST['pid'];
    	if (!$pid)
    	{
    		$this->json_error('失败');
    	}
    	$list = $region_mod->get_options_html($pid,0);
    	$this->json_result($list);
    }

    /**
     *ajax获得规格重量
     *@author shaozz
     *@2015年7月14日
     */
    function get_weight()
    {
        $list='';
        $cate = empty($_POST['cate'])?0:$_POST['cate'];
        if (!$cate)
        {
            return $this->json_error('参数不能为空');
        }
        if($cate==$this->mix_cate_id){
            $list = $this->_mod_fbcategory->get_options_html($this->mix_cate_id,0);
        }
        
        if($list){
            return $this->json_result($list);
        }else{
            return $this->json_error('获取失败');
        }
        
    }
 
}

?>
