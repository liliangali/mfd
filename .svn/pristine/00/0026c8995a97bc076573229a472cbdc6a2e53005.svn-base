<?php

class DictApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
        $query = array(
            "id"  => intval($_GET['id']),
            'ids' => $_GET['ids'],
            'name' => trim($_GET['name']),
            'type' => $_GET['type'],
        );

        if(!$_COOKIE['ids']) {
            setcookie("ids", $query["ids"]);
        }else{
            $query["ids"] = $_COOKIE['ids'];
        }
        
        $mod = &m("dict");
        $page = $this->_get_page(10);
        $conditions = "parentid='{$query["id"]}'";
        if($query["name"]){
            $conditions .= " AND name LIKE '%{$query["name"]}%'";
        }
        
        $list = $mod->find(array(
            "conditions" => $conditions,
            'limit'  => $page['limit'],
            'count'  => true,
            'fields' => "id,name,code,isshow, iselement",
        ));
        
        $page['item_count'] = $mod->getCount();
        $this->_format_page($page);
        $hasC = 0;
        $ids = explode(",", $query["ids"]);
        
        foreach($list as $key => $val){
            if($val["isshow"] || $val["id"] == "422" || $val["id"] == "518"){
                $hasC = 1;
            }
            $list[$key]['checked'] = in_array($val["id"], $ids) ? 1 : 0;
        }
        
        $this->assign("hasC", $hasC);
        $this->assign("list", $list);
        $this->assign('page_info', $page);
        $this->assign("query", $query);
        $this->display("dict.index.html");
    }
}

?>
