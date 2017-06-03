<?php

class GoodslinkApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
        $ids   = $_GET['ids'];
        if(!$_COOKIE['ids']) {
            setcookie("ids", $ids);
        }else{
            $ids = $_COOKIE['ids'];
        }
        
        $name    = trim($_GET['name']);
        $condition = " 1 ";
        if($name){
            $condition .= " AND goods_name LIKE '%{$name}%'";
        }
  
   
        $mod = &m("goods");
        $page = $this->_get_page(10);
        $list = $mod->find(array(
            "conditions" => $condition,
            'limit'  => $page['limit'],
            'count'  => true,
        ));
        $ids = explode(",", $ids);
       
        foreach($list as $key => $val){
            $list[$key]['checked'] = in_array($val["id"], $ids) ? 1 : 0;
        }
        $page['item_count'] = $mod->getCount();
        $this->_format_page($page);
        $this->assign("list", $list);
        $this->assign('page_info', $page);
        $this->display("goodslink.index.html");
    }
}

?>
