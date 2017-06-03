<?php

class CyzApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
        $v   = trim($_GET['v']);
        $ids   = $_GET['ids'];
        
        if(!$_COOKIE['suits']) {
            setcookie("suits", $ids);
        }else{
            $ids = $_COOKIE['suits'];
        }
    
        $mod = &m("member");
        $page = $this->_get_page(10);
        $list = $mod->find(array(
            "conditions" => "serve_type=1",
            'limit'  => $page['limit'],
            'count'  => true,
        ));


        $ids = explode(",", $ids);
        foreach($list as $key => $val){
            $list[$key]['checked'] = in_array($val["user_id"], $ids) ? 1 : 0;
        }

        $page['item_count'] = $mod->getCount();
        $this->_format_page($page);
        $this->assign("vid",$v);
       // print_r($list);die;
        $this->assign("list", $list);
        $this->assign('page_info', $page);
        $this->display("cyz.index.html");
    }
}

?>
