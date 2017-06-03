<?php

class EffectAgeApp extends BackendApp
{
    private $_mod_effectage;
    private $_mod_fbcategory;
    private $p_effect_id='1';//功效id
    private $p_age_id='34';//犬期id
    function __construct()
    {
         $this->_mod_effectage = &m("effectageprice");
         $this->_mod_fbcategory=&m('fbcategory');
         $gongxiao_list = $this->_mod_fbcategory->get_list(1);
         $gongiao = [];
         foreach ((array)$gongxiao_list as $index => $item)
         {
             $gongiao[$index] = $item['cate_name'];
         }

        $age_list = $this->_mod_fbcategory->get_list(34);
        $age = [];
        foreach ((array)$age_list as $index => $item)
        {
            $age[$index] = $item['cate_name'];
        }
        $this->assign('gongxiao_list',$gongiao);
        $this->assign('age_list',$age);

         parent::__construct();
    }


    function index()
    {
    	$conditions = ' 1=1 ';
    	// $query['base_name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
    	
    	// if($query["base_name"]){
    		// $conditions .= " AND bm.base_name LIKE '%{$query["base_name"]}%'";
    	// }
        $fbtype = isset($_REQUEST['fbtype']) ? $_REQUEST['fbtype'] : 0;
        $age_id= isset($_REQUEST['age_id']) ? $_REQUEST['age_id'] : 0;
        $this->assign('fbtype',$fbtype);
        $this->assign('age_id',$age_id);
        $conditions = '1=1';
        if($fbtype)
        {
            $conditions .= " AND effect_id = $fbtype";
        }
        if($age_id)
        {
            $conditions .= " AND age_id = $age_id";
        }

    	 
    	$page = $this->_get_page(30);
    	// setcookie('curr_page',$page["curr_page"]);
        $sql="select ea.*,fb1.cate_name as ename,fb2.cate_name as aname from cf_effect_age_price ea LEFT JOIN cf_fbcategory fb1 ON fb1.cate_id=ea.effect_id  LEFT JOIN cf_fbcategory fb2 ON fb2.cate_id=ea.age_id where {$conditions} ORDER BY ea_id   ASC LIMIT {$page['limit']}";
    	$list = $this->_mod_effectage->getAll($sql);
    	$sql="select count(*) as num from cf_effect_age_price ea where {$conditions}";
        $page['item_count'] =$this->_mod_effectage->getOne($sql);
    	$this->_format_page($page);
    	$this->assign('list', $list);
        // var_dump($basematerial_list);
    	$this->assign('page_info', $page);
        $this->display('effectage/effectage.index.html');
    }
    
     function add(){
        if(IS_POST){
            // $base_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $ea_price     = !empty($_POST['price'])?$_POST['price']:0;
            $ea_code     = !empty($_POST['code'])?trim($_POST['code']):'';
            $effect_id     = !empty($_POST['effect_id'])?$_POST['effect_id']:0;
            $age_id     = !empty($_POST['age_id'])?$_POST['age_id']:0;
            if(!$ea_price){
                return $this->show_warning('功效犬期价格不能为空');
            }else if(!$ea_code){
                return $this->show_warning('功效编码不能为空');
            }else if(!$effect_id){
                return $this->show_warning('请选择功效');
            }else if(!$age_id){
                return $this->show_warning('请选择犬期');
            }
            if($this->_mod_effectage->get("effect_id={$effect_id} AND age_id={$age_id}")){
                return $this->show_warning('该功效和犬期组合已被使用');
            }
            $data=array(
            		// 'base_name'=>$base_name,
            		'ea_price'=>$ea_price ,
                    'ea_code'=>$ea_code ,
            		'effect_id'=>$effect_id,
            		'age_id'=>$age_id,
            );
            $ea_id =  $this->_mod_effectage->add($data);
            if($ea_id){
            	$this->show_message("价格添加成功",'back_list', 'index.php?app=EffectAge');
            }
        }else{
        	$list1 = $this->_mod_fbcategory->get_options($this->p_effect_id);
        	$this->assign('effects',$list1);
        	$list2 = $this->_mod_fbcategory->get_options($this->p_age_id);
        	$this->assign('ages',$list2);
            $this->display("effectage/effectage.info.html");
        }
    }
    function edit(){
    	$id = intval($_GET["id"]);
    	if(IS_POST){
    		// $base_name      = !empty($_POST['name'])?trim($_POST['name']):'';
            $ea_price     = !empty($_POST['price'])?$_POST['price']:0;
            $ea_code     = !empty($_POST['code'])?trim($_POST['code']):'';
            $effect_id     = !empty($_POST['effect_id'])?$_POST['effect_id']:0;
            $age_id     = !empty($_POST['age_id'])?$_POST['age_id']:0;
            if(!$ea_price){
                return $this->show_warning('功效犬期价格不能为空');
            }else if(!$ea_code){
                return $this->show_warning('功效编码不能为空');
            }else if(!$effect_id){
                return $this->show_warning('请选择功效');
            }else if(!$age_id){
                return $this->show_warning('请选择犬期');
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
            $exist_id=$this->_mod_effectage->get("effect_id={$effect_id} AND age_id={$age_id}");
            if($exist_id && $id!=$exist_id['ea_id']){
                return $this->show_warning('该功效和犬期已被使用');
            }
    		$data=array(
                    'ea_price'=>$ea_price,
                    'ea_code'=>$ea_code,
                    'effect_id'=>$effect_id,
                    'age_id'=>$age_id,
    		);
    		$id =  $this->_mod_effectage->edit($id,$data);
    		if($id){
    			$this->show_message("功效犬期价格编辑成功",'back_list', 'index.php?app=EffectAge');
    		}
    
    	}else{
    		
    		$effectages=$this->_mod_effectage->get(array(
    				'conditions'=>"ea_id='{$id}'",
    		));
    		
    		
    		$list1 = $this->_mod_fbcategory->get_options($this->p_effect_id);
            $this->assign('effects',$list1);
            $list2 = $this->_mod_fbcategory->get_options($this->p_age_id);
            $this->assign('ages',$list2);
            $this->assign('data',$effectages);
    		// var_dump($list2);
    		$this->display("effectage/effectage.info.html");
    	}
    }
    
    function drop(){
    	$id = intval($_GET['id']);
        $this->_mod_effectage->drop($id);
    	$this->show_message("价格删除成功");
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
