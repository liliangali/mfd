<?php
/**
 * 后台基本款公用操作方法.
 * @author v5
 *
 */
class CommonApp extends BackendApp
{
	var $_apply_mod;
	var $_customs_mod;
    function __construct()
    {
        $this->CommonApp();
    }

    function CommonApp()
    {	
    	/* ajax 分页类 */
    	include(ROOT_PATH . '/includes/minupage.class.php');

        parent::__construct();
        $this->_customs_mod      =& m('customs');
        $this->_apply_mod =& m('apply');
        
        $conditions='';
		$_GET['cst_name'] = $_POST['cst_name'];
        $conditions .= $this->_get_query_conditions(array(
        		array(
        				'field' => 'cst_name',       //可搜索字段title
        				'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
        				'assoc' => 'AND',           //关系类型,可以是AND, OR
        				'name'  => 'cst_name',      //GET的值的访问键名
        				'type'  => 'string',        //GET的值的类型
        		)
        ));

        
//         if ($_REQUEST['ids'] && $_REQUEST['ac'] == 'ajax_customs_info'){
        if ($_REQUEST['ids'] && $_REQUEST['act'] == 'ajax_customs_info'){
        	$conditions .= ' and '.db_create_in($_REQUEST['ids'],'cst_id');
        }
        
        $page = $this->_get_page();
        
        $goods_list = $this->_customs_mod->find(array(
        		'conditions' => "is_active = 1 ".$conditions,
        		'count' => true,
        		'order' => "cst_weight asc",		//根据权重 正序
        		'limit' => $page['limit'],
        ));
       

        /* 默认选中 */
//         $def_ids = $_POST['def_ids'];
      
//         $def_ids_arr = explode(",",$def_ids);
 
//         if ($def_ids_arr){
//         	foreach ($def_ids_arr as $v){
//         		 var_dump($v);
//         		foreach ($goods_list as &$r){
//         				$r['def'] = 0;
//         			if($r['cst_id'] == $v){
//         				$r['def'] = 1;
//         			}
//         		}
//         	}
//         }
       
        
        $this->assign('goods_list', $goods_list);
         
         
        $ajaxpage=new minupage(array('total'=>$this->_customs_mod->getCount(),'perpage'=>10,'ajax'=>'ajax_page','page_name'=>'page','nowindex'=>empty($_REQUEST['page']) ? 1 : $_REQUEST['page'],'url'=>'/admin/index.php?app=common&act=ajax_get'));
        
        if ($this->_customs_mod->getCount())	$this->assign('pages', $ajaxpage->show(4));
        
    }

    /**
     * 后台关联基本款默认页面
     * @see BaseApp::index()
     */
    function index()
    {
    	
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('common.index.html');
    }
    
    /**
     * 后台关联基本款默认页面 - 第一页 直接读取数据
     */
    function getData(){

    	$content = $this->display("ajax/customs.list.html");
    }
    /**
     * 后台关联基本款默认页面 - ajax 分页 读取数据
     */
    function ajax_get(){
    	$content = $this->_view->fetch("ajax/item.index.html");
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
    	$this->display('rel.item.html');
    	die();
    }

}

?>
