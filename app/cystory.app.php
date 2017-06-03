<?php
class CystoryApp extends MallbaseApp
{
    var $_member_mod;
    var $_designer_mod;
    var $_order_mod;
    function __construct(){

        parent::__construct();
        define("CYSTORY", "cystory/");
        $this->_designer_mod =& m('designer');
        $this->_member_mod   =& m('member');
        $this->_order_mod    =& m('order');

    }

    function index()
    {
	
      
        $this->assign('data',$data);
        $this->display(CYSTORY."story.html");


    }
    function loadData()
    {
    
    	$page = $this->_get_page(4);
        $data_list = $this->_designer_mod->find(array(
            'conditions'=>"1=1",
            'limit' => $page['limit'],
            'count' => true,
            'order' => "id DESC"
        ));
        $page['item_count'] = $this->_designer_mod->getCount();
        $this->_format_page($page);
     	foreach ($data_list as $key => $value)
        {
            if($value['userid'])
            {
                $user_info = $this->_member_mod->get("user_id='{$value['userid']}'");
                $data_list[$key]['nickname'] = $user_info['nickname'];
                $data_list[$key]['reg_time'] = date("Y/m/d",$user_info['reg_time']);
            }else
            {
                $data_list[$key]['nickname'] = '无';
                $data_list[$key]['reg_time'] = '无';
            }
            
        }
        $this->assign("data_list", $data_list);
        $this->assign("pages", $page);
        $content = $this->_view->fetch(CYSTORY."story.list.html"); 
        $retArr = array(
           'content' => $content,
           'link'    => $page["next_link"],
       );

       die($this->json_result($retArr));
      
        
    
    }
    


   


}