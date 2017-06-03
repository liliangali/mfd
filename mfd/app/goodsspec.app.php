<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 *  ---------------------------------------------------------------------
 * @author shaozz<shaozizhen94@163.com>
 * 2016-5-20
 */
class GoodsspecApp extends BackendApp
{
    var $_goodsspec_mod;
    function __construct()
    {
        parent::BackendApp();
        $this->_goodsspec_mod =& m('specification');
    }
    /**
     * 规格列表
     */
    function index()
    {
        $typeId = $_GET['type_id'];
        
        $list = $this->_goodsspec_mod->find();
    	
        $this->assign("list", $list);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'style'  => 'res:style/jqtreetable.css',
    			'script' => 'jquery.plugins/jquery.validate.js'
    	));

    	$this->display('goodsspec/goodsspec.index.html');
    }
    
    /**
     * 添加类型
     */
    function add()
    {
    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    		    'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
    		
    		$this->display('goodsspec/goodsspec.form.html');
    	}
    	else
    	{
//     echo '<pre>';print_r($_POST);exit; 
    		$data = array();
    		$data["spec_name"] = trim($_POST['spec_name']);
    		$data["spec_memo"] = trim($_POST['spec_memo']);
    		$data["spec_type"] = $_POST['spec_type'];
    		$data["spec_show_type"] = $_POST['spec_show_type'];
    		$data["alias"] = trim($_POST['alias']);
    		$data["p_order"] = $_POST['p_order'];
    		$ress=$this->_goodsspec_mod->add($data);
    		if(!$ress)
    		{
    			$this->show_message($this->_goodsspec_mod->_errors);
    		}
    		$specValue = $_POST['spec_value'];
    		if ($specValue) 
    		{
    			$valdata=array();
    		    $specValueMdl = m("specvalues");
        	      $valdata['spec_id'] = $ress;
        		  foreach ($specValue as $key => $value) 
        		  {
        		     $valdata['spec_value'] = $value;
        		    
        		     if ($data['spec_type'] == 'image')  
        		     {
        		         $num = $key + 1;
        		         $valdata['spec_image'] = $_POST['img'.$num];
        		     }
        		     $valdata['p_order'] = $_POST['sort_order'][$key];
        		     $specValueMdl->add($valdata);
        		  }
    		}
    		
    		$this->show_message('添加成功',
    				'返回类型列表',    'index.php?app=goodsspec',
    				'continue_add', 'index.php?app=goodsspec&amp;act=add'
    		);
    		
    	}
    }
    
    /**
     * 修改类型
     */
    function edit()
    {
    	$id = intval($_REQUEST['id']);
    	if (!$id)
    	{
    	    $this->show_warning("你要修改的类型不存在");
    	    return ;
    	}
    	$specValueMdl = m("specvalues");
    	if (!IS_POST)
    	{
    		$specValueList = $specValueMdl->find(array(
    		    'conditions' => "spec_id = $id",
    		    'index_key'  => "",
    		));
    		if ($specValueList) 
    		{
    		    foreach ((array)$specValueList as $key => $value) 
    		    {
    		        $specValueList[$key]['img_name'] = "aimg".$value['spec_value_id'];
    		    }
    		}
    		
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js'
    		));
    		$type_info = $this->_goodsspec_mod->get_info($id);
    		$this->assign("info",$type_info);
    		$this->assign('spec_value_list',$specValueList);
    		$this->display("goodsspec/goodsspec.form.html");
    	}
    	else
    	{
    	    
//     	echo '<pre>';print_r($_POST);exit; 
    		$data = array();
    		$data = array();
    		$data["spec_name"] = trim($_POST['spec_name']);
    		$data["spec_type"] = $_POST['spec_type'];
    		$data["spec_show_type"] = $_POST['spec_show_type'];
    		$data["p_order"] = $_POST['p_order'];
    		if($this->_goodsspec_mod->edit($id,$data) === false)
    		{
    			$this->show_message($this->_goodsspec_mod->_errors);
    		}
    		
    		//===== 处理修改的规格  =====
    		$aspec_value = $_POST['aspec_value'];
    		if ($aspec_value) 
    		{
    		    foreach ($aspec_value as $key => $value) 
    		    {
    		        $valdatae['spec_value'] = $value;
    		        $valdatae['spec_image'] = $_POST["aimg".$key];
    		        $valdatae['p_order'] =   $_POST['asort_value'][$key];
//     		   echo '<pre>';print_r($valdata);exit; 
    		        $specValueMdl->edit($key,$valdatae);
    		    }
    		}
    		
    		
    		$specValue = $_POST['spec_value'];
    		if ($specValue)
    		{
    		    $valdata['spec_id'] = $id;
    		    foreach ($specValue as $key => $value)
    		    {
    		        $valdata['spec_value'] = $value;
    		        if ($data['spec_type'] == 'image')
    		        {
    		            $num = $key + 1;
    		            $valdata['spec_image'] = $_POST['img'.$num];
    		        }
    		        $valdata['p_order'] = $_POST['sort_order'][$key];
//     		  echo '<pre>';print_r($valdata);exit; 
    		        $specValueMdl->add($valdata);
    		    }
    		}
    
    		$this->show_message('修改成功',
    				'返回类型列表',    'index.php?app=goodsspec'
    		);
    
    	}
    }
    
	/**
	 * @删除类型
	 * @return bool 
	 */
    function drop()
    {
    	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    	if (!$id)
    	{
    		$this->show_warning('你要删除的类型不存在');
    		return;
    	}
    	
        
    	if (!$this->_goodsspec_mod->drop($id))
    	{
    		$this->show_warning($this->_goodsspec_mod->get_error());
    		return;
    	}
    
    	$this->show_message('删除类型成功');
    }
    
    
    /**
     *ajax异步加载栏目关联内容页面
     *@access public
     *@author  liang.li
     *@date 2016年5月20日
     *@version 1.0
     *@return
     */
    function load_add_content(){
    
        $num=intval($_GET['num'])+1;
        if(!$num){
            $this->json_error('传参失败');
        }
        $img="img{$num}";
        $this->assign('img',$img);
        $this->assign("num", $num);
        $content = $this->_view->fetch("goodsspec/content.add.html");
        $this->json_result($content);
        die();
    }
    
}

?>
