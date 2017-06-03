<?php
class DownApp extends MallbaseApp
{
   var $active_mod;
    function __construct(){
        parent::__construct();
        $this->active_mod = &m("activeimg");
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function index()
    {
    	$this->_config_seo('title', '麦富迪APP下载 - mfd麦富迪');
        $this->display("down.html");    
    }
}
