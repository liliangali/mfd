<?php
class AboutApp extends MallbaseApp
{
	var $about_article_code='about_us';
	var $_article_mod;
	function __construct()
	{
		$this->_article_mod=&m('article');
		parent::__construct();
	}

	function index(){
		$article = $this->_article_mod->get("code='{$this->about_article_code}'");
    	if (!$article)
    	{
    		$this->show_warning('没有该文章');
    		return;
    	}
    	
    	$this->assign('article', $article);
    	$this->display('article/about.html');
	}
}