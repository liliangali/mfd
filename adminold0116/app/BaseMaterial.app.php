<?php

class BaseMaterialApp extends BackendApp
{
    private $_mod_basematerial;
    private $_mod_fbcategory;
    private $p_cate_id='21';//犬种父id
    private $p_age_id='34';//犬期父id
    private $mix_cate_id='60';//混血犬种id
    function __construct()
    {
         $this->_mod_basematerial = &m("basematerial");
         $this->_mod_fbcategory=&m('fbcategory');
         parent::__construct();
    }


    function index()
    {
    	$conditions = ' 1=1 ';
    	$query['base_name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
    	
    	if($query["base_name"]){
    		$conditions .= " AND bm.base_name LIKE '%{$query["base_name"]}%'";
    	}
    	 
    	$page = $this->_get_page(30);
    	setcookie('curr_page',$page["curr_page"]);
        $sql="select bm.*,fb1.cate_name as cname,fb2.cate_name as aname from cf_base_material bm LEFT JOIN cf_fbcategory fb1 ON fb1.cate_id=bm.cate_id  LEFT JOIN cf_fbcategory fb2 ON fb2.cate_id=bm.age_id where {$conditions} ORDER BY base_id   ASC LIMIT {$page['limit']}";
    	$basematerial_list = $this->_mod_basematerial->getAll($sql);
    	$sql="select count(*) as num from cf_base_material bm  where {$conditions}";
        $page['item_count'] =$this->_mod_basematerial->getOne($sql);
    	$this->_format_page($page);
    	$this->assign('basematerial_list', $basematerial_list);
        // var_dump($basematerial_list);
    	$this->assign('page_info', $page);
        $this->display('basematerial/basematerial.index.html');
    }
    
     function add(){
     	$region_mod = m('fbcategory');
        if(IS_POST){
            $base_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $base_price     = !empty($_POST['price'])?$_POST['price']:0;
            $base_code     = !empty($_POST['code'])?trim($_POST['code']):'';
            $cate_id     = !empty($_POST['cate_id'])?$_POST['cate_id']:0;
            $age_id     = !empty($_POST['age_id'])?$_POST['age_id']:0;
            $weight     = !empty($_POST['weight'])?$_POST['weight']:0;
            if(!$base_name){
                return $this->show_warning('基料名称不能为空');
            }else if(!$base_price){
                return $this->show_warning('基料价格不能为空');
            }else if(!$base_code){
                return $this->show_warning('基料编码不能为空');
            }else if(!$cate_id){
                return $this->show_warning('请选择犬种');
            }else if(!$age_id){
                return $this->show_warning('请选择犬期');
            }
            if($cate_id==$this->mix_cate_id && !$weight){
                return $this->show_warning('请选择犬重');
            }
            $cate_id=($cate_id==$this->mix_cate_id)?$weight:$cate_id;
            if($this->_mod_basematerial->get("cate_id={$cate_id} AND age_id={$age_id}")){
                return $this->show_warning('该犬种和犬期组合已被使用');
            }
            $data=array(
            		'base_name'=>$base_name,
            		'base_price'=>$base_price ,
                    'base_code'=>$base_code ,
            		'cate_id'=>$cate_id,
            		'age_id'=>$age_id,
            );
            $basematerial_id =  $this->_mod_basematerial->add($data);
            if($basematerial_id){
            	$this->show_message("基料添加成功",'back_list', 'index.php?app=BaseMaterial');
            }
        }else{
        	$list1 = $this->_mod_fbcategory->get_options($this->p_cate_id);
        	$this->assign('cates',$list1);
        	$list2 = $this->_mod_fbcategory->get_options($this->p_age_id);
        	$this->assign('ages',$list2);
            $this->display("basematerial/basematerial.info.html");
        }
    }
    function edit(){
    	$id = intval($_GET["id"]);
    	if(IS_POST){
    		$base_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $base_price     = !empty($_POST['price'])?$_POST['price']:0;
            $base_code     = !empty($_POST['code'])?trim($_POST['code']):'';
            $cate_id     = !empty($_POST['cate_id'])?$_POST['cate_id']:0;
            $age_id     = !empty($_POST['age_id'])?$_POST['age_id']:0;
            $weight     = !empty($_POST['weight'])?$_POST['weight']:0;
            if(!$base_name){
                return $this->show_warning('基料名称不能为空');
            }else if(!$base_price){
                return $this->show_warning('基料价格不能为空');
            }else if(!$base_code){
                return $this->show_warning('基料编码不能为空');
            }else if(!$cate_id){
                return $this->show_warning('请选择犬种');
            }else if(!$age_id){
                return $this->show_warning('请选择犬期');
            }
            $cate_id=($cate_id==$this->mix_cate_id)?$weight:$cate_id;
    		
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
            $exist_id=$this->_mod_basematerial->get("cate_id={$cate_id} AND age_id={$age_id}");
            if($exist_id && $id!=$exist_id['base_id']){
                return $this->show_warning('该犬种和犬期已被使用');
            }
    		$data=array(
    				'base_name'=>$base_name,
                    'base_price'=>$base_price,
                    'base_code'=>$base_code,
                    'cate_id'=>$cate_id,
                    'age_id'=>$age_id,
    		);
    		$basematerial_id =  $this->_mod_basematerial->edit($id,$data);
    		if($basematerial_id){
    			$this->show_message("基料编辑成功",'back_list', 'index.php?app=BaseMaterial');
    		}
    
    	}else{
    		
    		$basematerials=$this->_mod_basematerial->get(array(
    				'conditions'=>"base_id='{$id}'",
    		));
    		
    		
    		$list1 = $this->_mod_fbcategory->get_options($this->p_cate_id);
            $this->assign('cates',$list1);
            $list2 = $this->_mod_fbcategory->get_options($this->p_age_id);
            $this->assign('ages',$list2);
    		if (!isset($list1[$basematerials['cate_id']]))
    		{
    			$weight = $this->_mod_fbcategory->get_options($this->mix_cate_id);
                $basematerials['weight']=$basematerials['cate_id'];
                $basematerials['cate_id']=$this->mix_cate_id;
                $this->assign('weight',$weight);
    		}
            $this->assign('data',$basematerials);
    		// var_dump($list2);
    		$this->display("basematerial/basematerial.info.html");
    	}
    }
    
    function drop(){
    	$id = intval($_GET['id']);
        $this->_mod_basematerial->drop($id);
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
     *ajax获得犬种重量
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
