<?php

class ShirttypeApp extends BackendApp
{
    function __construct()
    {
         parent::__construct();
    }
    
    function index(){
        $ids   = $_GET['ids'];
        $search=$_POST['search'];
        
        $conditions = "category='0006'";//衬衣id
         if($search){
	        $conditions .= " AND name LIKE '%".$search."%' ";
         }
        
        $mod = &m("custom");
        $page = $this->_get_page(7);
        $list = $mod->find(array(
            "conditions" =>"{$conditions} AND to_site='app'",
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
        $this->display("shirttype.index.html");
    }
  
}

?>
