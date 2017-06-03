<?php
class DesignerApp extends MallbaseApp
{
    var $gcategory_mod;
    var $_designer_mod;
    var $_mod_custom;
    var $_mod_suit;
    function __construct(){

        parent::__construct();
        define("DESIGN", "designer/");
        $this->gcategory_mod =& m("gcategory");
        $this->_designer_mod =& m('designer');
        $this->_mod_custom = &m("custom");
        $this->_mod_suit = &m("suitlist");
      
       
        

    }

    function index(){

        $arg   = $this->get_params();
        $desginer_id =intval($arg[2]);
         if(empty($desginer_id))
        {
            header("Location:index.php");
            return;
        }
        $cat_id = isset($_GET['cate_id']) ? intval($_GET['cate_id']) : 0;
        $tz_id  = isset($_GET['tz_id']) ? intval($_GET['tz_id']) : 0;

        //////////

        $desgin = $this->_designer_mod->get("id=$desginer_id");
        // 添加设计师浏览量 
        $desgin_popularity = $this->_designer_mod->edit("id='$desginer_id'",array('popularity'=>$desgin['popularity']+1));
        $this->assign("desgin",$desgin);
        $this->assign('desginer_id',$desginer_id);
        /////////////////
        $options = $this->_get_options();
        foreach ($options as $key => $value) {
            $options[$key]['url'] = "?cate_id=".$value['cate_id'];
                 
        }


        $url = "?cate_id=".$cat_id;
        $this->assign("options", $options);
        $this->assign("formatUrl", $url);
        $this->display(DESIGN."designer.html");


    }

    function loadCustomData(){
        $desginid = $_GET['desginid'];
        $cat_id     = $_GET['cate_id'];
         if(empty($cat_id))
        {   
            $conditions= '';
            
        }else
        {
            $conditions= "  AND cat_id='$cat_id' ";

        }
        $page = $this->_get_page(8);
        $custom_list = $this->_mod_custom->find(array(
            'conditions'=>"design_id='$desginid' ".$conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "id DESC"
        ));
        $page['item_count'] = $this->_mod_custom->getCount();
        $this->_format_page($page);
        $this->assign("custom_list", $custom_list);
        $content = $this->_view->fetch(DESIGN."custom.list.html"); 
        $retArr = array(
           'content' => $content,
           'link'    => $page["next_link"],
       );
       die($this->json_result($retArr));
        $this->assign("pages", $page);


    }

    function suit(){
        $arg   = $this->get_params();
        $desginer_id =intval($arg[2]);
        $desgin = $this->_designer_mod->get("id=$desginer_id");
        $this->assign("desgin",$desgin);
        $this->assign('desginer_id',$desginer_id);
        $options = $this->_get_options();
        foreach ($options as $key => $value) {
            $options[$key]['url'] = "?cate_id=".$value['cate_id'];
                 
        }

        $url = "?desginid=".$desginer_id;
        $this->assign("options", $options);
        $this->assign("formatUrl", $url);
        $this->assign("desginer_id", $desginer_id);
        $this->display(DESIGN."suit.html");

    }

    function loadSuitData(){
         $desginid = $_GET['desginid'];
          if(empty($desginid))
        {
            $this->show_warning('param_error');
            return;
        }

        $conditions = " AND design_id='$desginid' ";
        //var_dump($conditions);
        $page = $this->_get_page(8);
        $tz_list = $this->_mod_suit->find(array(
            'conditions'=>'1=1 '.$conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "id DESC"
        ));
     
        $page['item_count'] = $this->_mod_suit->getCount();
        $this->_format_page($page);
        $this->assign("pages", $page);
        $this->assign("tz_list", $tz_list);
        $this->assign("desginer_id", $desginid);
        $content = $this->_view->fetch(DESIGN."suit.list.html"); 
        $retArr = array(
           'content' => $content,
           'link'    => $page["next_link"],
       );
       die($this->json_result($retArr));

    
    }


   
    // 取得男装下的分类
    function _get_options($except = NULL)
    {	
    	$cate_id = '128493';// 男装分类id
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
        $gcategories = $_cate_mod->get_list($cate_id);
        return $gcategories;
    }

  

    function expert(){
    	
    	
      $this->display(DESIGN."expert.html");
    
    }


}