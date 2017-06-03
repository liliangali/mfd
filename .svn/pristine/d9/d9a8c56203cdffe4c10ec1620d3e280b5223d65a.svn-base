<?php
/**
 * @name input object 
 * @author Ruesin
 */
class InputobjApp extends BackendApp{
	var $_object_mod;
	var $search;
    function __construct(){
        $this->InputobjApp();
    }

    function InputobjApp(){

        /* ajax 分页类 */
        include(ROOT_PATH . '/includes/minupage.class.php');
        parent::__construct();
        $model=$_REQUEST['objmodel'];
        $this->assign('objmodel',$model);
        $this->_object_mod      =& m($model);
        if(empty($this->_object_mod) || !isset($this->_object_mod))exit;
        $filter=$this->get_other_filter($_REQUEST['filter']);
        $param='&filter='.$_REQUEST['filter'];
        $this->assign('filter',$param);
        //*********** 基本款选组件 Ruesin Bgn*************//
//         if($model == 'part'){
//             $ss = $_REQUEST['filter'];
//             $arr=explode(',', $ss);
//             print_exit($arr);
//         }
        //*********** 基本款选组件 Ruesin End*************//
        
        $this->search = $this -> fmtSearch($this->_object_mod->_obj_search);
        $searchCond = $this->getSeachCond($this->search);

        $conditions=' 1 = 1 ';
        
        $conditions .= $this->_get_query_conditions($searchCond);


        if ($_REQUEST['ids'] && $_REQUEST['act'] == 'ajax_customs_info'){
        	$conditions .= ' and '.db_create_in($_REQUEST['ids'],'cst_id');
        }
        //$to_site = $model == 'customs'?"AND to_site='app'":'';
        $page = $this->_get_page();
        $goods_list = $this->_object_mod->find(array(
            'fields' => implode($this->get_fields(0), ','),
            'conditions' => $conditions.$filter,
            'count' => true,
            'order' => $this->_object_mod->_order,
            'limit' => $page['limit'],
        ));
        //判断图片
        $imgArr=explode(',', $this->_object_mod->_obj_images);
        $fields=array();
        foreach ($this->get_fields(0) as $v){
        	$fields[$v]['value']=$v;
        	foreach ($imgArr as $i){
        		if($i == $v){
        		    $fields[$v]['img'] = $v;
        		}
        	}
        }
        $this->assign('image_prev',$this->_object_mod->_obj_image_prev);
        $this->assign('fields',$fields);
        $this->assign('title',$this->get_fields(1));
       
        /* 默认选中 */
        $def_ids = $_POST['def_ids'];
      
        $def_ids_arr = explode(",",$def_ids);
 
        if ($def_ids_arr){
        	foreach ($def_ids_arr as $v){
        		foreach ($goods_list as &$r){
        				$r['def'] = 0;
        			if($r['cst_id'] == $v){
        				$r['def'] = 1;
        			}
        		}
        	}
        }

        $this->assign('goods_list', $goods_list);
         
         
        $ajaxpage=new minupage(array(
            'total'=>$this->_object_mod->getCount(),
            'perpage'=>10,
            'ajax'=>'ajax_page',
            'page_name'=>'page',
            'nowindex'=>empty($_REQUEST['page']) ? 1 : $_REQUEST['page'],
            'url'=>"/admin/index.php?app=inputobj&act=ajax_get&objmodel={$model}".$param
        ));

        if ($this->_object_mod->getCount()){
            $this->assign('pages', $ajaxpage->show(4));
        }
        
    }

    function index(){
    	$this->assign('search',$this->search);
        $this->display('common/input/object/index.html');
    }
    
    /**
     * 后台关联基本款默认页面 - 第一页 直接读取数据
     */
    function getData(){
        echo 
    	$content = $this->display("common/input/object/list.html");
    }
    /**
     * 后台关联基本款默认页面 - ajax 分页 读取数据
     */
    function ajax_get(){
    	$content = $this->_view->fetch("common/input/object/item.html");
    	echo json_encode(array(
			    			'status' => $status,
			    			'msg' => $msg,
			    			'data' => $content,
			    			'dialog' => $dialog,
			    	));
    }
    
    /**
     * 根绝选中的基本款ids（string）返回基本款的信息
     */
    function ajax_customs_info(){
    	$this->display('common/input/object/rel.item.html');
    	die();
    }
    
    function get_fields($key){
        $fields=explode(',', $this->_object_mod->_obj_fields);
        foreach ($fields as $val){
            $arr=explode('|',$val);
            $return[0][]=$arr[0];
            $return[1][]=$arr[1];
        }
        return $return[$key];
    }
    function get_other_filter($str=''){
    	if($str=='')return;
    	
    	$arr=explode(',', $str);
    	foreach ($arr as $key=> $row){
    		if(!empty($row)){
    		    $tem=explode('|', $row);
    		    
    		    $ret.=" AND {$tem[0]} = '{$tem[1]}' ";
    		    
//     		    $ret.=' AND '.$tem[0].'='.$tem[1].' ';
    		}
    	}
    	return $ret; 
    }
    
    function fmtSearch($search){
        if($search=='')return;
        $arr=explode(',', $search);
        foreach ($arr as  $row){
            $return[] = explode('|', $row);
        }
        return $return;
    }
    function getSeachCond($arr){
    
        foreach ($arr as $K => $r){
            $cond['field'] = $r[0];
            $cond['name'] = $r[0];
            if(isset($r[2]) && $r[2]=='equal'){
                $cond['equal'] = '=';
            }else{
                $cond['equal'] = 'LIKE';
            }
            $return[] = $cond;
        }
        return $return;
    }
}

?>
