<?php
//wangdy
class PageSimple {
	public $mCurrPage ;//当前页
	public $mTotalPage ;//总页数
	public $mEveryPage ;//每一页显示多少条记录
	public $mLimit ;//LIMIT
	
	public $mData ;//需要分页的数据
	
	function __construct($mTotalData, $mCurrPage, $mShowPageNum = 20 ){
		$this->mTotalDataNum = $mTotalData;//总数组条数
		$this->mEveryPage = $mShowPageNum;//初始化是20条每页
		if(!$mCurrPage)
			$this->mCurrPage = 1;
		else
			$this->mCurrPage = $mCurrPage;
	}
	//执行分页处理
	function execPage(){
		//计算一共有多少页
		$this->mTotalPage = ceil($this->mTotalDataNum / $this->mEveryPage);
		if($this->mCurrPage > $this->mTotalPage)
			$this->mCurrPage = $this->mTotalPage;
		
		$this->setLimit();
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
}
