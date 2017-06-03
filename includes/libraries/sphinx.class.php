<?php
require ( "sphinxapi.php" );
class Sphinx{
	public $host = SPHINX_HOST;
	public $port = 9312;
	static public $instance = null;
	
	static function inst(){
		if(self::$instance)
			return self::$instance;
		
		return new self();
	}
	
	function __construct(){
		$this->conn = new SphinxClient ();
			
		$mode = SPH_MATCH_ALL;
		$this->conn->SetServer ( $this->host, $this->port );
		//最大搜索时间
		$this->conn->SetMaxQueryTime(10);
		$this->conn->SetConnectTimeout ( 1 );
		$this->conn->SetArrayResult ( true );
		$this->conn->SetWeights ( array ( 100, 1 ) );
		$this->conn->SetMatchMode ( $mode );
		
	}
	function queryPage($keyword,$index , $page , $page_num, $sortby = '',$filter = '', $groupby = '',$groupsort = '',$filtervals= array(),$distinct = '',$sortexpr= '',$select = ''){
		$page = intval($page);
		if(!$page || $page < 0)
			exit('page is null ~or < 0');
		
		$page_num = intval($page_num);
		if(!$page_num || $page_num < 0)
			exit('page_num is null ~or < 0');
		
		$cnt = $this->cnt($keyword,$index , $sortby ,$filter , $groupby ,$groupsort ,$filtervals ,$distinct ,$sortexpr,$select );
		if(!$cnt)
			return 0;
		
		$maxPage = ceil(  $cnt / $page_num  );
		if($page > $maxPage)
			$page = $maxPage;
		
		$start = $page * $page_num - $page_num;
		$res = $this->query($keyword,$index , $start ,$page_num, $sortby ,$filter , $groupby ,$groupsort ,$filtervals ,$distinct ,$sortexpr,$select );
		return $res;
// 		var_dump($res);
// 		$this->out($res);
		
	}
	
	function cnt($keyword,$index ,  $sortby = '',$filter = '', $groupby = '',$groupsort = '',$filtervals= array(),$distinct = '',$sortexpr= '',$select = ''){
		$rs =$this->query($keyword,$index , 0 ,1,  $sortby = '',$filter = '', $groupby = '',$groupsort = '',$filtervals= array(),$distinct = '',$sortexpr= '',$select = '');
		return $rs['total_found'];
	}
	
	function query($keyword,$index , $limit_start = '',$limit_end = 1000,$sortby = '',$filter = '', $groupby = '',$groupsort = '',$filtervals= array(),$distinct = '',$sortexpr= '',$select = ''){
		if(!$keyword)
			exit('keyword is null');
		
		if(!$index)
			exit('index is null');
		
		
		$ranker = SPH_RANK_PROXIMITY_BM25;
		
		if ( count($filtervals) )	$this->conn->SetFilter ( $filter, $filtervals );//检索哪个字段的值
		if ( $groupby )				$this->conn->SetGroupBy ( $groupby, SPH_GROUPBY_ATTR, $groupsort );//分组
		//排序
		// SPH_SORT_RELEVANCE 模式, 按相关度降序排列（最好的匹配排在最前面）
		// SPH_SORT_ATTR_DESC 模式, 按属性降序排列 （属性值越大的越是排在前面）
		// SPH_SORT_ATTR_ASC 模式, 按属性升序排列（属性值越小的越是排在前面）
		// SPH_SORT_TIME_SEGMENTS 模式, 先按时间段（最近一小时/天/周/月）降序，再按相关度降序
		// SPH_SORT_EXTENDED 模式, 按一种类似SQL的方式将列组合起来，升序或降序排列。
		// SPH_SORT_EXPR 模式，按某个算术表达式排序
		if ( $sortby )				$this->conn->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		if ( $sortexpr )			$this->conn->SetSortMode ( SPH_SORT_EXPR, $sortexpr );
		//去重
		if ( $distinct )			$this->conn->SetGroupDistinct ( $distinct );
		//设置返回的字段
		if ( $select )				$this->conn->SetSelect ( $select );
		
		$limit_start = intval($limit_start);
		if ( !$limit_start && $limit_start !== 0  )
			exit("please set limit number!");

		if ($limit_start < 0 )
			exit("please input Positive number!");
		
		//2000:max_matchs,最大索引值就到2000条记录
		$this->conn->SetLimits ( $limit_start, $limit_end , 1000);
			
		$this->conn->SetRankingMode ( $ranker );//指定范围
		$res = $this->conn->Query ( $keyword, $index );
		if(!$res){
			return $this->conn->GetLastError();
		}
		
		if ( $this->conn->GetLastWarning() )
			print "WARNING: " . $this->conn->GetLastWarning() ;
		
		
		//$res['fields']:检索的字段值
		//attrs:返回的字段值
		//total_found:实际找到的数据总数
		//total:返回总数
		//time:执行时间
		//words:关键字       array(doc,hits);
		//matches:匹配结果 array(id,weight)
		
		return $res;
	}

	function out($res){

		echo 'total:'.$res['total_found'] ."<br/>";
		foreach($res['matches'] as $k=>$v){
			echo $k;
			var_dump($v);echo "<br/><br/>";
		}

		exit;
	}
}
