<?php

/* 主题分类 */
class DiscatApp extends DisApp
{
	var $_config;
    function __construct()
    {
    	$this->_config = ROOT_PATH . '/data/dissertation/config.php';
        parent::__construct();
    }

    function index()
    {
 		$cats = $this->disCats();
		if(is_file($this->_config)){
			$_tmp = include_once($this->_config);
		}else{
			$_tmp = array();
			foreach($cats as $key => $val){
				$_tmp[$key]['name'] = $val;
				$_tmp[$key]['img'] = '';
				$_tmp[$key]['link'] = '';
			}
		}
		if(!IS_POST)
		{
			$this->assign('_tmp_list', $_tmp);
	 		$this->display("discat.index.html");
		}
		else
		{
			$_data = array();
			foreach($cats as $key => $val)
			{
				$_data[$key]['name'] = $val;
				$_data[$key]['img'] = $_POST[$key];
				$_data[$key]['link'] = $_POST["link"][$key];
			}
			
			$php_data = "<?php\n\nreturn " . var_export($_data, true) . ";\n\n?>";

			
			file_put_contents($this->_config, $php_data, LOCK_EX);
			
			$this->show_message('保存成功!',
    					'back_list', 'index.php?app=discat');
		}
    }
}

?>
