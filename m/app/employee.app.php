<?php
import('mymodule.lib');
class EmployeeApp extends MemberbaseApp
{
	var $module;
	function __construct()
	{
		parent::__construct();
		$this->module=new MyModule('employee',$this);
		//var_dump($this->_data_mod);
		$this->module->conditions='idserve ='.$this->visitor->get('idserve');
		
		$this->module->adddata=array(
            'employee_name' => $_POST['employee_name'],
            'sex'    => $_POST['sex'],
            'job_number'     => $_POST['job_number'],
            'mobile'    => $_POST['mobile'],
			'id_number'    => $_POST['id_number'],
			'mark'    => $_POST['mark'],
            'idserve'    => $this->visitor->get('idserve'),
        );
        $this->module->editdata=array(
            'sex'    => $_POST['sex'],
            'mobile'    => $_POST['mobile'],
			'id_number'    => $_POST['id_number'],
			'mark'    => $_POST['mark'],
        );
		$sex=array('1'=>'男','2'=>'女',);
        $this->assign('sex',$sex);
        $this->assign('title', 'RCTAILOR-酷客中心-服务点主页');
	}
	
	function index(){
		$this->module->index();
	}
	
	function edit(){
		
		/* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
		$url=$this->_view->build_url(array('app'=>'employee'));
		$this->module->url=$url;
		//var_dump($this->module->url);exit;
		$this->module->edit();
	}
	function add(){
		
		
		
		if(IS_POST)
		{
			$res=$this->module->_data_mod->check_job_number($_POST['job_number'], $this->visitor->get('idserve'));
       		if (!$res)
			{
				$this->show_warning('员工编号已存在！');
				return;
			}
            
			
        	$res=$this->module->_data_mod->check_employee_name($_POST['employee_name'], $this->visitor->get('idserve'));
       		if (!$res)
			{
				$this->show_warning('员工名称已存在！');
				return;
			}
		}
		$url=$this->_view->build_url(array('app'=>'employee'));
		$this->module->url=$url;
		$this->module->add();
	}
	function drop(){
		$this->module->drop();
	}
	
	function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'employee',
            ),
        );
        return $menus;
    }
	
}