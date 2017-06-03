<?php
class My_body_figureApp extends MemberbaseApp
{
	function __construct()
    {
    	parent::__construct();
    	$this->user_id = $this->visitor->get('user_id');
    	$this->_curitem('my_body_figure');
    	
    	$this->_figure=m('figure');
    	
    	Lang::load(lang_file('figure'));
    }
    
    function index(){
    		/* 是否存在 */
            $figure = $this->_figure->get(array(
            	'conditions' => 'userid='.$this->user_id,
            	'order'=>'idfigure desc',
            	)
            );
            if (!$this->user_id)
            {
                $this->show_warning('user_empty');
                return;
            }
			
            
            $this->assign('figure', $figure);
			
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('figure_type', array(
	            '0'   => LANG::get('figure_type_0'),
	            '1' => LANG::get('figure_type_1'),
        	));
        	$this->assign('acc',LANG::get('edit'));
        	
        	$figure_mode=array('1'=>LANG::get('figure_mode_1'),'2'=>LANG::get('figure_mode_2'));
            
            $this->assign('figure_mode', $figure_mode);
            
            if($figure)
            {
        	
   	 		$employee_mod=m('employee');
        		$employee_data=$employee_mod->find(array(
        		'fields'=>'employee_name',
        		'conditions' => ' idserve ='.$figure['idserve']  ,    
	        	'limit' => '0,10',
	            'order' => "idemployee desc",
        		));
        		
        		
        		foreach ($employee_data as $k=>$v)
        		{
					 $employee_name_arr[$employee_data[$k]['employee_name']]=$employee_data[$k]['employee_name'];		
        		}
            $this->assign('employee_name_arr',$employee_name_arr);
            }
            $this->display('my_body_figure/my_body_figure.form.html');
    	
    }
    
    
    
}