<?php


class ServiceApp extends BackendApp
{
	var $_service;
    function __construct()
    {
    	parent::__construct();
        $this->_service=m('servedetail');
    }

    
    
	function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $service = $this->_service->get(array('conditions' => 'idserve='.$id , ));
            if (!$service)
            {
            	$this->_service->add(array('idserve'=>$id));
            	$service = $this->_service->get(array('conditions' => 'idserve='.$id , ));
            }

            //var_dump($service);
            $this->assign('service', $service);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'introduce',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $this->assign('state', array(
	            '0'   => LANG::get('state_0'),
	            '1' => LANG::get('state_1'),
	            '2'     => LANG::get('state_2'),
        	));
        	$this->assign('acc',LANG::get('edit'));
            $this->display('service.form.html');
        }
        else
        {
        	
            $data = array(
                'portrait' => $_POST['portrait'],
                'synopsis'    => $_POST['synopsis'],

                'introduce'     => $_POST['introduce'],
                'longitude'    => $_POST['longitude'],
				'latitude'    => $_POST['latitude'],

            );

            /* 修改本地数据 */
            $this->_service->edit('idserve='.$id, $data);

            

            $this->show_message('修改成功',
                'back_list',    'index.php?app=serve',
                '再次编辑',   'index.php?app=service&amp;act=edit&amp;id=' . $id
            );
        }
    }
	
}

?>
