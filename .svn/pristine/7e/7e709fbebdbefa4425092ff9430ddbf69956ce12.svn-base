<?php

class MaterialApp extends BackendApp
{
    private $_mod_material;//物料表
    private $_mod_fbcategory;//属性表
    private $_mode_effect_age_price;//功效犬期
    private $_mod_base_material;//基料表
    private $p_taste_id='6';//犬种父id
    function __construct()
    {
         $this->_mod_material = &m("material");
         $this->_mod_fbcategory=&m('fbcategory');
         $this->_mode_effect_age_price = &m("effectageprice");
         $this->_mod_base_material = &m("basematerial");
         parent::__construct();
    }


    function index()
    {
    	$conditions = ' 1=1 ';
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'material_code',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'material_code',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
            array(
                'field' => 'bm_code',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'bm_code',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
        ));
        $conditions.=!empty($_REQUEST['ea_code'])?" AND (ea_code_first LIKE '%{$_REQUEST[ea_code]}%' OR ea_code_second LIKE '%{$_REQUEST[ea_code]}%' OR ea_code_third LIKE '%{$_REQUEST[ea_code]}%')":'';
    	$page = $this->_get_page(30);
    	$material_list = $this->_mod_material->find(array(
                'conditions'=>$conditions,
                'fields'=>'m.*,fbc.cate_name as taste',
                'join'=>'has_taste',
                'count'=>true,
                'limit'=>$page['limit']
            ));
        $page['item_count'] =$this->_mod_material->getCount();
    	$this->_format_page($page);
    	$this->assign('material_list', $material_list);
        // var_dump($material_list);
    	$this->assign('page_info', $page);
        $this->display('material/material.index.html');
    }
    // 物料编码=基料编码+功效编码1+功效编码2+功效编码3+口味
     function add(){
        if(IS_POST){
            $material_code     = !empty($_POST['material_code'])?$_POST['material_code']:0;
            $bm_code     = !empty($_POST['bm_code'])?trim($_POST['bm_code']):'';
            $ea_code_first     = !empty($_POST['ea_code_first'])?trim($_POST['ea_code_first']):'';
            $ea_code_second     = !empty($_POST['ea_code_second'])?trim($_POST['ea_code_second']):'';
            $ea_code_third    = !empty($_POST['ea_code_third'])?trim($_POST['ea_code_third']):'';
            $taste_id     = !empty($_POST['taste_id'])?$_POST['taste_id']:0;
            if(!$material_code){
                return $this->show_warning('请填写物料编码');
            }else if(!$bm_code){
                return $this->show_warning('请选择基料编码');
            }else if(!$ea_code_first){
                return $this->show_warning('至少选择一个功效编码');
            }else if(!$taste_id){
                return $this->show_warning('请选择口味');
            }else if($ea_code_first==$ea_code_second || ($ea_code_second==$ea_code_third && $ea_code_second && $ea_code_third) || $ea_code_first==$ea_code_third){
                return $this->show_warning('不能选择相同的功效编号');
            }
            $code_range="'$ea_code_first'";
            if($ea_code_second){
                $code_range="'$ea_code_first,$ea_code_second'";
                $second_condition=" AND FIND_IN_SET (ea_code_second,$code_range)";
            }else{
                $second_condition='';
            }
            if($ea_code_third){
                $code_range="'$ea_code_first,$ea_code_second,$ea_code_third'";
                $third_condition=" AND FIND_IN_SET (ea_code_second,$code_range)";
            }else{
                $third_condition='';
            }
            // if($region){
            $where="bm_code='{$bm_code}' AND  FIND_IN_SET (ea_code_first,$code_range) AND taste_id={$taste_id}".$second_condition.$third_condition;
            if($this->_mod_material->get($where)){
                return $this->show_warning('该基料、功效、品味组合已被使用');
            }
            $data=array(
            		'material_code'=>$material_code,
            		'bm_code'=>$bm_code ,
                    'ea_code_first'=>$ea_code_first ,
                    'ea_code_second'=>$ea_code_second ,
            		'ea_code_third'=>$ea_code_third,
            		'taste_id'=>$taste_id,
            );
            $material_id =  $this->_mod_material->add($data);
            if($material_id){
            	$this->show_message("物料添加成功",'back_list', 'index.php?app=Material');
            }
        }else{
            $codes=array();
            $ea_codes1=array();
        	$list1 = $this->_mod_fbcategory->get_options($this->p_taste_id);
        	$this->assign('tastes',$list1);
            $codes=$this->_mod_base_material->find();
            if($codes){
                $codes=array_unique(i_array_column($codes,'base_code','base_code'));
            }
            $this->assign('base_codes',$codes);//基料编码
            $ea_codes1=$this->getEffectCode();
        	$this->assign('ea_codes1',$ea_codes1);
            $this->display("material/material.info.html");
        }
    }
    function edit(){
    	$id = intval($_GET["id"]);
    	if(IS_POST){
    		$material_code     = !empty($_POST['material_code'])?$_POST['material_code']:0;
            $bm_code     = !empty($_POST['bm_code'])?trim($_POST['bm_code']):'';
            $ea_code_first     = !empty($_POST['ea_code_first'])?trim($_POST['ea_code_first']):'';
            $ea_code_second     = !empty($_POST['ea_code_second'])?trim($_POST['ea_code_second']):'';
            $ea_code_third    = !empty($_POST['ea_code_third'])?trim($_POST['ea_code_third']):'';
            $taste_id     = !empty($_POST['taste_id'])?$_POST['taste_id']:0;
            if(!$material_code){
                return $this->show_warning('请填写物料编码');
            }
            // else if(!$bm_code){
            //     return $this->show_warning('请选择基料编码');
            // }
            else if(!$ea_code_first){
                return $this->show_warning('至少选择一个功效编码');
            }else if(!$taste_id){
                return $this->show_warning('请选择口味');
            }else if($ea_code_first==$ea_code_second || ($ea_code_second==$ea_code_third && $ea_code_second && $ea_code_third) || $ea_code_first==$ea_code_third){
                return $this->show_warning('不能选择相同的功效编号');
            }
    		$code_range="'$ea_code_first'";
            if($ea_code_second){
                $code_range="'$ea_code_first,$ea_code_second'";
                $second_condition=" AND FIND_IN_SET (ea_code_second,$code_range)";
            }else{
                $second_condition='';
            }
            if($ea_code_third){
                $code_range="'$ea_code_first,$ea_code_second,$ea_code_third'";
                $third_condition=" AND FIND_IN_SET (ea_code_second,$code_range)";
            }else{
                $third_condition='';
            }
    		// if($region){
            $where="bm_code='{$bm_code}' AND  FIND_IN_SET (ea_code_first,$code_range) AND taste_id={$taste_id}".$second_condition.$third_condition;
            $exist_id=$this->_mod_material->get($where);
            if($exist_id && $id!=$exist_id['material_id']){
                return $this->show_warning('该基料、功效、品味组合已被使用');
            }
    		$data=array(
    				'material_code'=>$material_code,
                    'bm_code'=>$bm_code ,
                    'ea_code_first'=>$ea_code_first ,
                    'ea_code_second'=>$ea_code_second ,
                    'ea_code_third'=>$ea_code_third,
                    'taste_id'=>$taste_id,
    		);
    		$material_id =  $this->_mod_material->edit($id,$data);
    		if($material_id){
    			$this->show_message("物料编辑成功",'back_list', 'index.php?app=Material');
    		}
    
    	}else{
    		$codes=array();
            $ea_codes1=array();
            $ea_codes2=array();
            $ea_codes3=array();
    		$material=$this->_mod_material->get($id);
    		
            $list1 = $this->_mod_fbcategory->get_options($this->p_taste_id);
            $this->assign('tastes',$list1);//口味
            $codes=$this->_mod_base_material->find();
            if($codes){
                $codes=array_unique(i_array_column($codes,'base_code','base_code'));
            }
            $this->assign('base_codes',$codes);//基料编码
            $ea_codes1=$this->getEffectCode();
            $this->assign('ea_codes1',$ea_codes1);//功效一
            if($material['ea_code_second']){

                unset($ea_codes1[$material['ea_code_first']]);
                $ea_codes2=$ea_codes1;
                $this->assign('ea_codes2',$ea_codes2);//功效二
            }
            if($material['ea_code_third']){
                unset($ea_codes1[$material['ea_code_second']]);
                $ea_codes3=$ea_codes1;
                $this->assign('ea_codes3',$ea_codes3);//功效三
            }
            $this->assign('data',$material);
    		// var_dump($list2);
    		$this->display("material/material.info.html");
    	}
    }
    
    function drop(){
    	$id = intval($_GET['id']);
        $this->_mod_material->drop($id);
    	$this->show_message("基料删除成功");
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

    function getEffectCode($removecodes=array()){
        $codes=$this->_mode_effect_age_price->find();
        if($codes){
            $codes=array_unique(i_array_column($codes,'ea_code','ea_code'));
        }
        if($removecodes){
            $codes=array_diff($codes, $removecodes);
        }
        return $codes;
        
    }

    function ajaxGetEffect(){
        $removecodes=!empty($_REQUEST['codes'])?trim($_REQUEST['codes']):'';
        if(!$removecodes){
            return $this->json_error('参数不能为空');
        }
        $removecodes=explode(',',$removecodes);
        $codes=$this->getEffectCode($removecodes);
        return $this->json_result($codes,'成功');
    }

    function import(){
        $ids=array();
        // var_dump($_FILES);
        if(!$_FILES){
            return $this->show_warning('请先选择要上传的文件');
        }
        $filename = $_FILES['inputExcel']['name'];
        
        $tmp_name = $_FILES['inputExcel']['tmp_name'];
        //自己设置的上传文件存放路径
        // var_dump(ROOT_PATH);
        $filePath = ROOT_PATH.'/upload/';
        $str = "";   
        //下面的路径按照你PHPExcel的路径来修改includes\libraries
        require_once ROOT_PATH.'/includes/libraries/PHPExcel.php';
        require_once ROOT_PATH.'/includes/libraries/PHPExcel/IOFactory.php';
        require_once ROOT_PATH.'/includes/libraries/PHPExcel/Reader/Excel5.php';
        require_once ROOT_PATH.'/includes/libraries/PHPExcel/Reader/CSV.php';
        require_once ROOT_PATH.'/includes/libraries/PHPExcel/Reader/Excel2007.php';

        //注意设置时区
        $time=date("y-m-d-H-i-s");//去当前上传的时间 
        //获取上传文件的扩展名
        $extend=strrchr ($filename,'.');
        // var_dump($extend);exit();
        //上传后的文件名
        $name=$time.$extend;
        $uploadfile=$filePath.$name;//上传后的文件名地址 
        //move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
        $result=move_uploaded_file($tmp_name,$uploadfile);//假如上传到当前目录下
        //echo $result;
        if($result) //如果上传文件成功，就执行导入excel操作
        {
            if( $extend =='.xlsx')
            {
             // $objReader = new PHPExcel_Reader_Excel2007();
             $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
            }
            else if($extend =='.xls')
            {
             // $objReader = new PHPExcel_Reader_Excel5();
             $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
            }
            else if($extend =='.csv')
            {
                //$objReader = PHPExcel_IOFactory::createReader('CSV');//use excel2007 for 2007 format
                return $this->show_warning('请上传xls或xlsx后缀的文件');
            }
            else{
                return $this->show_warning('请上传xls或xlsx后缀的文件');
            }
            // $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
            $objPHPExcel = $objReader->load($uploadfile); 
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestRow();           //取得总行数 
            $highestColumn = $sheet->getHighestColumn(); //取得总列数
            
            // 第一种方法

            //循环读取excel文件,读取一条,插入一条
            for($j=2;$j<=$highestRow;$j++)                        //从第一行开始读取数据
            { 
                $str='';
                for($k='A';$k<=$highestColumn;$k++)            //从A列读取数据
                { 
                    //这种方法简单，但有不妥，以''合并为数组，再分割为字段值插入到数据库                实测在excel中，如果某单元格的值包含了导入的数据会为空        
                    //
                    $str .=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue().',';//读取单元格
                } 
                //echo $str; die();
                //explode:函数把字符串分割为数组。
                $strs = explode(",",$str);
                $taste=$this->_mod_fbcategory->get("cate_name='$strs[5]'");
                $data=array(
                    'material_code'=>$strs[0],
                    'bm_code'=>$strs[1],
                    'ea_code_first'=>$strs[2],
                    'ea_code_second'=>$strs[3],
                    'ea_code_third'=>$strs[4],
                    'taste_id'=>$taste['cate_id']
                );
                $res=$this->_mod_material->add($data);
                
                if(!$res){
                    $this->_mod_material->drop(array(
                            'conditions'=>db_create_in($ids,'material_id')
                        ));
                    return $this->show_warning('导入失败');
                }else{
                    $ids[]=$res;
                }
            }

            unlink($uploadfile); //删除上传的excel文件
            $msg = "导入成功！";
            return $this->show_message($msg,'back_list', 'index.php?app=Material');
        }
    }
 
}

?>
