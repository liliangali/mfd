<?php

class HelpApp extends MallbaseApp
{
	var $help_ids=array('47','54');//帮助中心和培训手册
    var $_article_mod;
    var $_acategory_mod;
    var $_ACC; //系统文章cate_id数据
    var $_cate_ids; //当前分类及子孙分类cate_id
    function __construct()
    {
        parent::__construct();
        $this->_article_mod = &m('article');
        $this->_acategory_mod = &m('acategory');
    }

    function index()
    {
    	$ids=$cate_ids=$this->help_ids;
    	$help_acategory=$this->_acategory_mod->find(array(
    		"conditions"=>"1=1 AND ".db_create_in($cate_ids,'parent_id'),
    			"fields"=>'*'	
    	));
    	if($help_acategory){
    		$tmp_ids=i_array_column($help_acategory, 'cate_id');
    		$ids=array_merge($cate_ids,$tmp_ids);
    	}
    	
    	$articles=array();
    	$article_lists='';
    	$articles_list='';
    	$article_lists=$this->_article_mod->find(array(
    			"conditions"=>db_create_in($ids,'cate_id')." AND if_show=1",
    			"fields"=>"*",
    			"order"=>"sort_order ASC,add_time DESC"
    	));
    	
    	if(!empty($article_lists)){
    		foreach ($article_lists as $key=>$v){
    			$articles[]=$v;
    		}
    	}    	
/*     	if(!empty($help_acategory)){
    		foreach ($help_acategory as $key=>$acategory){
    			$articles_list=$this->_article_mod->find(array(
    					"conditions"=>"cate_id={$acategory['cate_id']} AND if_show=1",
    					"fields"=>"*",
    					"order"=>"sort_order ASC,add_time DESC"
    			));
    			if(!empty($articles_list)){
    				foreach ($articles_list as $k=>$article){
    					$articles[]=$article;
    				}
    			}   			
    		}
    	} */
    	foreach ($articles as $key=>$v){
    		$num=$this->number2Chinese($key+1);
    		$articles[$key]['title']=$num.'、'.$v['title'];
    	}
    	$this->assign('articles',$articles);
       $this->display('help/help.index.html');
    }
    function info(){
    	$arg = $this->get_params();
    	$article_id = empty($arg[0]) ? 0 : intval($arg[0]);
    	
    	$article = $this->_article_mod->get("article_id='{$article_id}' AND  if_show=1");
    	if (!$article)
    	{
    		$this->show_warning('no_such_article');
    		return;
    	}
    	
    	$this->assign('article', $article);
    	$this->assign('act_article_id',$article_id);
    	$this->display('help/help.info.html');
    }
    function number2Chinese($num, $m = 1) {
    	switch($m) {
    		case 0:
    			$CNum = array(
    			array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖'),
    			array('','拾','佰','仟'),
    			array('','萬','億','萬億')
    			);
    			break;
    		default:
    			$CNum = array(
    			array('零','一','二','三','四','五','六','七','八','九'),
    			array('','十','百','千'),
    			array('','万','亿','万亿')
    			);
    			break;
    	}
    	// $cNum = array('零','一','二','三','四','五','六','七','八','九');
    
    	if (is_integer($num)) {
    		$int = (string)$num;
    	} else if (is_numeric($num)) {
    		$num = explode('.', (string)floatval($num));
    		$int = $num[0];
    		$fl  = isset($num[1]) ? $num[1] : FALSE;
    	}
    	// 长度
    	$len = strlen($int);
    	// 中文
    	$chinese = array();
    
    	// 反转的数字
    	$str = strrev($int);
    	for($i = 0; $i<$len; $i+=4 ) {
    		$s = array(0=>$str[$i], 1=>$str[$i+1], 2=>$str[$i+2], 3=>$str[$i+3]);
    		$j = '';
    		// 千位
    		if ($s[3] !== '') {
    			$s[3] = (int) $s[3];
    			if ($s[3] !== 0) {
    				$j .= $CNum[0][$s[3]].$CNum[1][3];
    			} else {
    				if ($s[2] != 0 || $s[1] != 0 || $s[0]!=0) {
    					$j .= $CNum[0][0];
    				}
    			}
    		}
    		// 百位
    		if ($s[2] !== '') {
    			$s[2] = (int) $s[2];
    			if ($s[2] !== 0) {
    				$j .= $CNum[0][$s[2]].$CNum[1][2];
    			} else {
    				if ($s[3]!=0 && ($s[1] != 0 || $s[0]!=0) ) {
    					$j .= $CNum[0][0];
    				}
    			}
    		}
    		// 十位
    		if ($s[1] !== '') {
    			$s[1] = (int) $s[1];
    			if ($s[1] !== 0) {
    				$j .= $CNum[0][$s[1]].$CNum[1][1];
    			} else {
    				if ($s[0]!=0 && $s[2] != 0) {
    					$j .= $CNum[0][$s[1]];
    				}
    			}
    		}
    		// 个位
    		if ($s[0] !== '') {
    			$s[0] = (int) $s[0];
    			if ($s[0] !== 0) {
    				$j .= $CNum[0][$s[0]].$CNum[1][0];
    			} else {
    				// $j .= $CNum[0][0];
    			}
    		}
    		$j.=$CNum[2][$i/4];
    		array_unshift($chinese, $j);
    	}
    	$chs = implode('', $chinese);
    	if ($fl) {
    		$chs .= '点';
    		for($i=0,$j=strlen($fl); $i<$j; $i++) {
    			$t = (int)$fl[$i];
    			$chs.= $str[0][$t];
    		}
    	}
    	return $chs;
    }
}

?>
