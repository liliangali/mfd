<?php/*关于我们控制器  shao * */class HelpsApp extends MallbaseApp{	var $_article_mod;
	var $_acategory_mod;    function __construct(){        parent::__construct();        header("Content-Type:text/html;charset=" . CHARSET);        $this->_article_mod =& m('article');
        $this->_acategory_mod =& m('acategory');    }       function index()    {     	$cate_id=47;//常见问题文章分类id写死
    	$cate=$this->_acategory_mod->find(array(
    			'conditions' => "parent_id='{$cate_id}'",
    			'fields'=>"cate_name",
    			'order'	=>'cate_id',
    	));    	    	$list = array();     	$cat_id = i_array_column($cate, 'cate_id');
     	$condition = db_create_in($cat_id,'cate_id');     	$article=$this->_article_mod->find(array(     			'conditions'=>$condition,     	));     	$newcate=array();    	foreach($article as $key=>$val){    		$newcate[$val['cate_id']]['data'] = $cate[$val['cate_id']];    		$newcate[$val['cate_id']]['list'][$key] = $val;    		//$article[$key]['cate_name']=$cate[$val['cate_id']]['cate_name'];    	}    	foreach($article as $key=>$val){
    		if($key && $val['content']){
    			$article_mr = $val['content'];
    			break;
    		}
    	
    	}    	    	$this->assign('article_mr',$article_mr);    	$this->assign('newcate',$newcate);        $this->display('helps.index.html');    }        function jsion()
    {
    	$cur=$_POST['cur'];
    	$articles=$this->_article_mod->get(array(
    			'conditions' =>"article_id='{$cur}'",
    	));  
    	$this->json_result($articles);
    }}?>