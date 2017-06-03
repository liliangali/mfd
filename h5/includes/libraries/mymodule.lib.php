<?php
class MyModule {
	var $_data_mod,$conditions,$modelname,$editdata,$adddata,$app;
	function __construct($model,$app)
	{
		$this->_data_mod = m($model);
		$this->modelname=$model;
		$this->app=$app;
		
		$this->app->assign('modelname', $this->modelname);
		$this->app->assign('prikey', $this->_data_mod->prikey);
		
		$this->conditions='1=1';
		
		//$this->_curitem('service');
		$this->app->_curitem($this->modelname);
		$this->app->_curmenu($this->modelname);

		//$this->user_id = $this->app->visitor->get('user_id');
		//var_dump($this->app->visitor->get('idserve'));
	}
	function index()
    {
    	
    	
        $conditions = $this->app->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
    	
    	
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = $this->_data_mod->prikey;
             $order = 'desc';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = $this->_data_mod->prikey;
                $order = 'desc';
            }
        }
        
        $page = $this->app->_get_page();
        $datas = $this->_data_mod->find(array(
        	'conditions' => $this->conditions . $conditions,    
        	'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

        //var_dump($datas);

        $this->app->assign('datas', $datas);
        $page['item_count'] = $this->_data_mod->getCount();
        $this->app->_format_page($page);

        $this->app->assign('page_info', $page);
        
        
        $this->app->display($this->modelname.'.index.html');
    }
    
    
	function edit()
    {
        $args = $this->app->get_params();
    	
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        
        
        if (!IS_POST)
        {
            /* 是否存在 */
            $data = $this->_data_mod->get_info($id);
            if (!$data||$data['serve_userid']!=$this->user_id)
            {
                $this->app->show_warning('user_empty');
                return;
            }
			
            
            $this->app->assign('data', $data);
			
            /* 导入jQuery的表单验证插件 */
            $this->app->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
        	$this->app->assign('acc',LANG::get('edit'));
        	
        	
        	
            $this->app->display($this->modelname.'.form.html');
        }
        else
        {
        	/*
            $data = array(
                'sex'    => $_POST['sex'],
                'mobile'    => $_POST['mobile'],
				'id_number'    => $_POST['id_number'],
				'mark'    => $_POST['mark'],
            );
			*/
            $data=$this->editdata;
            
            /* 修改本地数据 */
            $this->_data_mod->edit($this->conditions.' and '.$this->_data_mod->prikey.'='.$id, $data);
			
            if($this->url)
            {
            	//var_dump($this->url);exit;
            	$this->app->show_message('修改成功','go_back',$this->url);
            }else 
            {
            	$this->app->show_message('修改成功');
            }
            
            
        }
    }
    
	function add()
    {
        if (!IS_POST)
        {
            
            /* 导入jQuery的表单验证插件 */
            $this->app->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
			
        	$this->app->assign('acc',LANG::get('add'));
        	$this->app->assign('do','add');

            $this->app->display($this->modelname.'.form.html');
        }
        else
        {

            
			
            /*
            $data = array(
                'employee_name' => $_POST['employee_name'],
                'sex'    => $_POST['sex'],
                'job_number'     => $_POST['job_number'],
                'mobile'    => $_POST['mobile'],
				'id_number'    => $_POST['id_number'],
				'mark'    => $_POST['mark'],
            	'idserve'    => $this->app->visitor->get('idserve'),
            );
            */
			
			$data=$this->adddata;
			
            $iddata=$this->_data_mod->add($data);
            
            
            if (!$iddata)
            {
                $this->app->show_warning($this->_data_mod->get_error());
                return;
            }
            if($this->url)
            {
            	//var_dump($this->url);exit;
            	$this->app->show_message('添加成功','go_back',$this->url);
            }else 
            {
            
            	$this->app->show_message('添加成功');
            }
        }
    }
    
	function drop()
    {
    	$args = $this->app->get_params();
    	
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        //$id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->app->show_warning('no_user_to_drop');
            return;
        }
        

        $ids = explode(',', $id);

        $ids=$this->conditions.' and '.$this->_data_mod->prikey.'='.$id;
        
        if (!$this->_data_mod->drop($ids))
        {
            $this->app->show_warning($this->_data_mod->get_error());

            return;
        }

        $this->app->show_message('drop_ok');
    }
    
    
}