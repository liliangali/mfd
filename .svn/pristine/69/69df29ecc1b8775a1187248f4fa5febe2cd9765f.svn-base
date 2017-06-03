<?php

class StartApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function index()
    {
        $region_mod = m('region');
        $list1 = $region_mod->get_options(2);
        $this->assign('region1',$list1);
        $list2 = $region_mod->get_options(246);
        $this->assign('region2',$list2);
         
        $mod = m('cy');
        $ask = $mod->getAsk();
            // print_exit($ask);
        $this->assign('ask',$ask);

        $this->display('start.html');
    }

    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }

}

?>