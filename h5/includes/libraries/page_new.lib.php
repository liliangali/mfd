<?php
//wangdy
class PageNew {
	public $mNextPage ;//下一页
	public $mPrevPage ;//上一页
	public $mFirstPage ;//首页
	public $mLastPage ;//尾页
	
	public $mCurrPage ;//当前页
	public $mTotalPage ;//总页数
	public $mTotalDataNum ;//总数组条数
	public $mEveryPage ;//每一页显示多少条记录
	public $mLimit ;//LIMIT
	
	public $mData ;//需要分页的数据
	public $mShowPageNum; //  取中间位置的时候，共显示多少条
	
	public $mResult;
	public $mShowHTML;//最终生成的HTML
	public $moduleSymbol = "/index.php/kuke-coin.html";
	
	public $ctrl = "";
	public $ac = "";
	
	function __construct($page,$mTotalData, $mShowPageNum = 20 ){
		$this->mCurrPage = $page;//当前页
		$this->mTotalDataNum = $mTotalData;//总数组条数
		$this->mEveryPage = $mShowPageNum;//初始化是20条每页
		$this->mShowPageNum = 15;//默认同上
	}
	//执行分页处理
	function execPage(){
		//计算一共有多少页
		$this->mTotalPage = ceil($this->mTotalDataNum / $this->mEveryPage);
		$this->setLimit();
		
		if($this->mTotalPage > 1){//如果 总页数只有一页：不显示 分页HTML
			$this->mShowHTML = 
					  			  $this->getPrevPageModule()." ".
					  			  $this->getMiddlePageModule()." ".
					  			  $this->getNextPageModule()." ".
									"";
		}else{
// 			$this->mShowHTML = $this->getEverypageModule();
		}
		
	}
	//取得首页模块
	function getFirstPageModule(){
		if(!$this->mCurrPage || 1 == $this->mCurrPage){
			$imgUrl = '首页';
		}else{
			$imgUrl = '首页';
		}
		
		return $this->getHtmlLink(1,$imgUrl);
	}
	//取得尾页模块
	function getLastPageModule(){
		if($this->mCurrPage == $this->mTotalPage || 1 == $this->mTotalPage){
			$imgUrl = '尾页';
		}else{
			$imgUrl = '尾页';
		}
		
		return $this->getHtmlLink($this->mTotalPage,$imgUrl);
	}
	//取得上一页模块
	function getPrevPageModule(){
		if($this->mCurrPage > 1){
			$css = "";
			$page = $this->mCurrPage - 1;
			$imgUrl = '';
		}else{
			$page = 1;
			$imgUrl = '';
		}
		
		$html = $this->getHtmlLink($page,$imgUrl,'sanyy');
		
		return $html;
	}
	//取得下一页模块
	function getNextPageModule(){
		if($this->mCurrPage < $this->mTotalPage){
			$page = $this->mCurrPage + 1;
			$imgUrl = '';
		}else{
			$page = $this->mTotalPage;
			$imgUrl = '';
		}
		
		$html = $this->getHtmlLink($page,$imgUrl,'xiayy');
		return $html;
	}
	//
	function getHtmlLink($page,$info,$class = ''){
		$url = $this->getLink($page);
		if(!$class){
			$html = "<a href='$url'>$info</a>";
		}else{
			$html = "<a class='$class' href='$url'>$info</a>";
		}
		return $html;		
	}
	//取得中间模块
	function getMiddlePageModule(){
		$firstLoca = 1;
		if($this->mTotalPage <= $this->mShowPageNum){
			$lastLoca = $this->mTotalPage;
		}else{
			if($this->mCurrPage > 3){
				$firstLoca = $this->mCurrPage - 3;
				if($this->mTotalPage - $firstLoca >= $this->mShowPageNum){
					$firstLoca = $firstLoca+1;
					$lastLoca = $firstLoca + $this->mShowPageNum-1;
				}else{
					$lastLoca = $this->mTotalPage;
					$firstLoca = $lastLoca - ($this->mShowPageNum - 1 );
				}
			}else{
				$lastLoca = $this->mShowPageNum;
			}
		}
		$html = "";
		for($i=$firstLoca;$i<=$lastLoca;$i++){
			if($i == $this->mCurrPage){
				$info = $i;
				$html .= " ".$this->getHtmlLink($i,$info,'curr');
			}else{
				$info = $i;
				$html .= " ".$this->getHtmlLink($i,$info);
			}
		}
		
		return $html;
	}
	
	function getLink($page){
			$urlPara = $_SERVER["QUERY_STRING"];
			if(!$urlPara){
				$url = $this->moduleSymbol."?page=$page"."&";
			}else{
				$l = strpos($urlPara,"page"); 
				if(false === $l ){
					$url = "{$this->moduleSymbol}?$urlPara&page=".$page;
				}else{
					//$urlPara = preg_replace("/page=\w&/","",$urlPara);
					$urlPara = substr($urlPara, 0,$l -1);
					$url = "{$this->moduleSymbol}?$urlPara&page=".$page;
				}
			}
		
		return $url;
	}
	//取得当前页
	function getCurrPage(){
		$currPage = $this->noNoticeGet("page");
		if(!$currPage){
			$currPage = 1;
		}
		
		if($currPage > $this->mTotalPage){
			$currPage = $this->mTotalPage;
		}
		
		return $currPage;
	}
	//GET值，没有NOTICE
	function noNoticeGet($paraName){
		$getValue = 0;
		if(isset($_GET[$paraName])){
			$getValue = $_GET[$paraName];
		}
		
		return $getValue;
	}
	//limit
	function setLimit(){
		if(1 == $this->mTotalPage ){
			$this->mLimit = array(0,$this->mTotalDataNum );
		}elseif($this->mCurrPage == $this->mTotalPage){
			$firstLoca =$this->mEveryPage * ($this->mTotalPage - 1) ;
			$this->mLimit = array($firstLoca,$this->mTotalDataNum - $firstLoca );
		}else{
			$firstLoca = ($this->mCurrPage - 1) * $this->mEveryPage ; 
			$this->mLimit = array($firstLoca,$this->mEveryPage );
		}
		
	}
	
	function getLimit(){
		if($this->mLimit){
			return $this->mLimit;
		}else{
			$this->execPage();
			return $this->mLimit;
		}
	}
	
	function no_notice_get($str){
		$rs = "";
		if(isset($_GET[$str])){
			$rs = $_GET[$str];
		}
		
		return $rs;
	}
	
	function __destruct(){  
		unset($this->mData);  
	}
	
}
//
//
//
//		$arr = array(
//				array("id"=>1,"name"=>"a","time"=>"2010"),
//				array("id"=>2,"name"=>"b","time"=>"2010"),
//				array("id"=>3,"name"=>"c","time"=>"2010"),
//				array("id"=>4,"name"=>"d","time"=>"2010"),
//				array("id"=>5,"name"=>"e","time"=>"2010"),
//				array("id"=>6,"name"=>"f","time"=>"2010"),
//				array("id"=>7,"name"=>"g","time"=>"2010"),
//				array("id"=>8,"name"=>"h","time"=>"2010"),
//				array("id"=>9,"name"=>"i","time"=>"2010"),
//				array("id"=>10,"name"=>"i","time"=>"2010"),
//				array("id"=>11,"name"=>"i","time"=>"2010"),
//				array("id"=>12,"name"=>"i","time"=>"2010"),
//				array("id"=>13,"name"=>"i","time"=>"2010"),
//				array("id"=>14,"name"=>"i","time"=>"2010"),
//				array("id"=>15,"name"=>"i","time"=>"2010"),
//				array("id"=>16,"name"=>"i","time"=>"2010"),
//				array("id"=>17,"name"=>"i","time"=>"2010"),
//				array("id"=>18,"name"=>"i","time"=>"2010"),
//				array("id"=>19,"name"=>"i","time"=>"2010"),
//				array("id"=>20,"name"=>"i","time"=>"2010"),
//				array("id"=>21,"name"=>"i","time"=>"2010"),
//				array("id"=>22,"name"=>"i","time"=>"2010"),
//				array("id"=>23,"name"=>"i","time"=>"2010"),
//				array("id"=>24,"name"=>"i","time"=>"2010"),
//				array("id"=>25,"name"=>"i","time"=>"2010")
//		);
//$page = new page(25);
//$limit = $page->getLimit();
//var_dump($limit);
//echo $page->mResult['showHtml'];

?>