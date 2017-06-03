<?php

class LinkCustomApp extends BackendApp
{
    private $style;
    private $cat;
    private $init;
    function __construct()
    {
        $this->style = array(
            "1" => "正装",
            "2" => "休闲",
            "3" => "礼服"
        );
         
        $this->init = array(
            /*
             "0001" => array(
                 'name'  => "套装(2pcs)",
                 'items' => array(
                     "0003"     => array("fabric" =>'8001'),
                     '0004'     => array("fabric" =>'8001'),
                 ),
             ),
        "0002" => array(
            'name'  => "套装(3pcs)",
            'items' => array(
                "0003"     => array("fabric" =>'8001'),
                '0004'     => array("fabric" =>'8001'),
                '0005'     => array("fabric" =>'8001'),
            ),
        ),
        */
            "0003" => "西服",
            "0004" => "西裤",
            "0005" => "马甲",
            "0006" => "衬衣",
            "0007" => "大衣",
        );
         parent::__construct();
    }
    
    function index(){
        $ids   = $_GET['ids'];
        if(!$_COOKIE['ids']) {
            setcookie("ids", $ids);
        }else{
            $ids = $_COOKIE['ids'];
        }
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'name',
                'equal' => 'like',
            ),
            array(
                'field' => 'style',
                'equal' => '=',
            ),
            array(
                'field' => 'category',
                'equal' => '=',
            ),
            array(
                'field' => 'to_site',
                'equal' => '=',
            ),
        ));
        $mod = &m("custom");
        $page = $this->_get_page(10);
        $list = $mod->find(array(
            "conditions" => '1=1'.$conditions,
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
        $this->assign("to_site", $this->to_site);
        $this->assign("style_list", $this->style);
        $this->assign("cat_list",   $this->init);
        $this->assign('page_info', $page);
        $this->display("linkcustom.index.html");
    }
}

?>
