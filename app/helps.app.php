<?php
	var $_acategory_mod;
        $this->_acategory_mod =& m('acategory');
    	$cate=$this->_acategory_mod->find(array(
    			'conditions' => "parent_id='{$cate_id}'",
    			'fields'=>"cate_name",
    			'order'	=>'cate_id',
    	));
     	$condition = db_create_in($cat_id,'cate_id');
    		if($key && $val['content']){
    			$article_mr = $val['content'];
    			break;
    		}
    	
    	}    	
    {
    	$cur=$_POST['cur'];
    	$articles=$this->_article_mod->get(array(
    			'conditions' =>"article_id='{$cur}'",
    	));
    	$this->json_result($articles);
    }