<?php

class FabApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
        $v   = trim($_GET['v']);
        $ids   = $_GET['ids'];
        $name = $_GET['name'];
        if(!$_COOKIE['fabids']) {
            setcookie("fabids", $ids);
        }else{
            $ids = $_COOKIE['fabids'];
        }
        //8050 大衣
        //8030 衬衣
        //8001  西装
        $fab = array(
            "0007" => "8050",
            "0006" => "8030",
            "0003" => "8001",
            "0004" => "8001",
            "0005" => "8001",
            "0011" => "8001",
            "0012" => "8001",
            "0016" => "8030",
            '0017' => "8001",
            "0021" => "8050",
            "0032" => "8001",
        );
        
        $mod = &m("fabs");
        $page = $this->_get_page(10);
        $conditions = "CATEGORYID='{$fab[$v]}'";
        if($name){
            $conditions .= " AND CODE LIKE '%{$name}%'";
        }
        $list = $mod->find(array(
            "conditions" => $conditions,
            'limit'  => $page['limit'],
            'count'  => true,
        ));
        $idsArr = explode(",", $ids);
        foreach($list as $key => $val){
            $list[$key]['checked'] = in_array($val["ID"], $idsArr) ? 1 : 0;
        }
        $page['item_count'] = $mod->getCount();
        $this->_format_page($page);
        $this->assign("list", $list);
        $this->assign("v", $v);
        $this->assign("ids", $ids);
        $this->assign("name", $name);
        $this->assign('page_info', $page);
        $this->display("fab.index.html");
    }
}

?>
