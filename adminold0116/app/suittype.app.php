<?php

class SuittypeApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
    	$v   = trim($_GET['v']);
        $ids   = $_GET['ids'];
    
       // $styles =$_GET['styles'];
        $search=trim($_POST['search']);
       
        if(!empty($ids)){
        if(!$_COOKIE['suits']) {
            setcookie("suits", $ids);
        }else{
            $ids = $_COOKIE['suits'];
            }
        }
        //8050 大衣
        //8030 衬衣
        //8001 西装
        $suit = array(
            "0007" => "0007",
            "0006" => "0006",
            "0003" => "0003",
            "0004" => "0004",
            "0005" => "0005",
        	"0011" => "0011",
        	"0012" => "0012",
        	"0016" => "0016",
        	"0017" => "0017",
        	"0021" => "0021",
            "0032" => "0032",
        );
        $conditions = "1 ";
        if($v){
            $conditions .= " AND category='{$suit[$v]}'";
        }
       // if($styles){
        	//$conditions .= " AND style='{$styles}'";
       // }
        if($search){
        	$conditions .= " AND name LIKE '%".$search."%' ";
        }
        $mod = &m("custom");
        $page = $this->_get_page(10);
        $list = $mod->find(array(
            "conditions" => $conditions,
            'limit'  => $page['limit'],
            'count'  => true,
        ));
        foreach($list as $key => $val){
        	if($val["id"]==$ids){
        		$list[$key]['checked'] =1;
        	}else{
        		$list[$key]['checked'] =0;
        	}
        }
        $page['item_count'] = $mod->getCount();
        $this->_format_page($page);
        $this->assign("search", $search);
        $this->assign("vid",$v);
        $this->assign("list", $list);
        $this->assign('page_info', $page);
        $this->display("suittype.index.html");
    }
}

?>
