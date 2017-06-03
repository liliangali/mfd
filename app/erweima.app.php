<?php
class ErweimaApp extends MallbaseApp {
	var $_article_mod;
	var $_acategory_mod;
	var $_ACC; // 系统文章cate_id数据
	var $_cate_ids; // 当前分类及子孙分类cate_id
	function __construct() {
		parent::__construct ();
		$this->_article_mod = &m ( 'article' );
		$this->_acategory_mod = &m ( 'acategory' );
		/* 获得系统分类cate_id数据 */
		// $this->_ACC = $this->_acategory_mod->get_ACC();
	}
    /**
     *    二维码 的生成
     *
     *    @author    tangsj
     *    @return    void
     */
    function index()
    {	
    	
    	$arg = $_REQUEST['text'];
    	if(!$arg){
    		$this->show_warning('参数为空');
            return;
    	}
    
    	Qrcode('store', 1558, $arg, $avatar);
    	$mqrcode = getQrcodeImage('store', 1558, 10);
        $randCharObj = $this->getRandChar();
        $mqrcode = SITE_URL.'/upload/phpqrcode/'.$mqrcode.'?'.$randCharObj;
       
	
		
        header("location: $mqrcode");
    	
    
    }
    function getRandChar($length=5){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
    
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
    
        return $str;
    }
}