<?php

/**
 *    模块运行控制器
 *
 *    @author    shaozizhen
 *    @usage    none
 */
class AppsetApp extends BackendApp
{
    /**
     *   管理列表
     *
   */
    function index()
    {
        $version= &m("appversion");
       /*  if(!empty($_GET['val_pt']) && $_GET['val_pt'] !='all' && $_GET['val_pt'] != 'undefined'){
        	$conditions = " AND type='{$_GET['val_pt']}'";
        }
        if(!empty($_GET['val_yy']) && $_GET['val_yy'] !='all' && $_GET['val_yy'] != 'undefined'){
        	$conditions .= " AND app='{$_GET['val_yy']}'";
        } */
        $app_versions=$version->find(array(
        		'conditions'=>"1=1",
        		'order' =>"id DESC",
        ));
        foreach($app_versions as $key=>$val)
        {
        	$app_versions[$key]['add_time']=date("Y-m-d H:i",$val['add_time']);
        }
        $this->assign('versions',$app_versions);
        $this->display('appset.index.html');
    }
    function ajax_index()
    {
    	$version= &m("appversion");
    	if(!empty($_POST['val_pt']) && $_POST['val_pt'] !='all' && $_POST['val_pt'] != 'undefined'){
    		$conditions = " AND type='{$_POST['val_pt']}'";
    	}
    	if(!empty($_POST['val_yy']) && $_POST['val_yy'] !='all' && $_POST['val_yy'] != 'undefined'){
    		$conditions .= " AND app='{$_POST['val_yy']}'";
    	}
    	$app_versions=$version->find(array(
    			'conditions'=>"1=1".$conditions,
    			'order' =>"id DESC",
    			'index_key' =>'',
    	));
    	foreach($app_versions as $key=>$val)
    	{
    		$app_versions[$key]['add_time']=date("Y-m-d H:i",$val['add_time']);
    	}
    	$this->json_result($app_versions);
    	return;
    }
    /**
     *  添加
     *
     */
    function addess()
    {
       $version= &m("appversion");
       if(!$_POST){
       	$this->display('appset.info.html');
       }
       else{
    		$data = array(
    				'version'	=>	trim($_POST['version']),
    				'large'	=>	trim($_POST['large']),
    				'app'	=>	trim($_POST['apps']),
    				'updatecode'	=>	trim($_POST['updatecode']),
    				'link'	=>	trim($_POST['link']),
    				'type'	=>	trim($_POST['type']),
                    'add_time' =>time(),
    		        'description'=>trim($_POST['description']),
    		);
    		//var_dump($data);exit;
    		$vers=$version->add($data);
    		if (!$vers)
    		{
    			$this->show_warning($version->get_error());
    			return;
    		}
    		
    		$this->show_message('添加成功',
    				'back_list',    'index.php?app=appset&amp;act=index',
     				'continue_add', 'index.php?app=appset&amp;act=addess'
    		);
       }
    
    }
 
    
    /**
     * 编辑
     */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	$version= &m("appversion");
    	if (!IS_POST)
    	{
    		 
    	    $app_versions=$version->get(array(
    			'conditions'=>"id='{$id}'",
    			'order' =>"id DESC",
    	));
    	$this->assign('versions',$app_versions);
    	$this->display('appset.info.html');
    	}
    	else
    	{
    		$data = array(
    				'version'	=>	trim($_POST['version']),
    				'large'	=>	trim($_POST['large']),
    				'app'	=>	trim($_POST['apps']),
    				'updatecode'	=>	trim($_POST['updatecode']),
    				'link'	=>	trim($_POST['link']),
    				'type'	=>	trim($_POST['type']),
                    'add_time' =>time(),
    		        'description'=>trim($_POST['description']),
    		);
    		
    		$vers=$version->edit($id,$data);
    
    		if (!$vers)
    		{
    			$this->show_warning($version->get_error());
    			return;
    		}
    		$this->show_message('编辑成功',
    				'back_list',    'index.php?app=appset&amp;act=index'
    		);
    	}
    }
    
    /**
     *   删除
     *
     */
    function drop()
    {
    	$version= &m("appversion");
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if(!$id){
    		$this->message('没有id');
    		return;
    	}
    	$vers=$version->drop($id);
    	$this->show_message('删除成功',
    				'back_list',    'index.php?app=appset&amp;act=index'
    		);
    }   


}

?>
