<?php

class WomanApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
    }
   
    function index()
    {
   
        $this->display('woman/index.html');
    }



}

?>