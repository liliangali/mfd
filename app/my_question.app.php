<?php
/* 买家咨询管理控制器 */
class My_questionApp extends MemberbaseApp
{
    function __construct()
    {
        $this->My_questionApp();
    }
    function My_questionApp()
    {
        parent::__construct();
    }
    function index()
    {
        $page =$this->_get_page(8);
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                         LANG::get('my_question'), 'index.php?app=my_question',
                         LANG::get('my_question_list'));
        
        $question_mod = &m("question");
        $suit_mod     = &m("suitlist");
	    $list = $question_mod->find(array(
	        "conditions" => "user_id='{$this->visitor->get("user_id")}' AND type='ask'",
	        'order'      => "dateline DESC",
	        'limit'      => $page["limit"],
	        'count'      => true,
	    ));
	    
	    $replyids = array();
	    $goodsids = array();
	    foreach($list as $key => $val){
	        $replyids[] = $val["id"];
	        $goodsids[] = $val["custom_id"];
	    }
	     
	    $replylist = $question_mod->find(array(
	        "conditions" => "type='answer' AND parent_id ".db_create_in($replyids),
	    ));
	    
	    $suits = $suit_mod->find(array(
	        'conditions' => "id ".db_create_in($goodsids),
	        'fields'     => "image",
	    ));
	    
	    foreach($replylist as $key =>$val){
	        if(isset($list[$val["parent_id"]])){
	            $list[$val["parent_id"]]['reply'] = $val;
	        }
	    }
	    
	    foreach($list as $key => $val){
	        if(isset($suits[$val["custom_id"]])){
	            $list[$key]['goods'] = $suits[$val["custom_id"]];
	        }
	    }
        $page['item_count'] = $question_mod->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign("list", $list);
        $this->assign('page_info',$page);
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_question'));
        $this->assign("app", "my_question");
        $this->display('my_question.index.html');
    }
}

?>