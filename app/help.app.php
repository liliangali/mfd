<?php
class HelpApp extends MallbaseApp {
	var $_article_mod;
	var $_acategory_mod;
	var $_ACC; // 系统文章cate_id数据
	var $_cate_ids; // 当前分类及子孙分类cate_id
	var $help_ids=array('help_center');//帮助中心、培训手册、创业者培训
	function __construct() {
		parent::__construct ();
		$this->_article_mod = &m ( 'article' );
		$this->_acategory_mod = &m ( 'acategory' );
		/* 获得系统分类cate_id数据 */
		// $this->_ACC = $this->_acategory_mod->get_ACC();
	}
	function index() {
		/* 文章标记 */
		$arg = $this->get_params ();
		$article_code = empty ( $arg [0] ) ? 0 : trim ( $arg [0] );
		//只有登陆后的创业者才能看到创业者培训内容
/* 		if(!$this->visitor->has_login || !$this->visitor->info['member_lv_id']>1){
			array_pop($this->help_ids);
		} */
		$codes = $this->help_ids; // 新版帮助中心分类id写死
		$conditions = '1=1';
		$pids=$this->_acategory_mod->find(array(
				'conditions'=>"{$conditions} and ".db_create_in($codes,'code')
		));

		$pids=i_array_column($pids, 'cate_id');
		$acategory = $this->_acategory_mod->find ( array (
				'conditions' => "{$conditions} AND ".db_create_in($pids,'parent_id'),
				'fields' => 'cate_id,cate_name',
				'order'=>'sort_order ASC,cate_id ASC'
		) );
		$cat_ids = i_array_column ( $acategory, 'cate_id' );
		$condition = db_create_in ( $cat_ids, 'cate_id' );
		$articles = $this->_article_mod->find ( array (
				'conditions' => $condition 
		) );

		$newcate = array ();
		foreach ($cat_ids as $k=>$v){
			$newcate [$v] ['data'] = $acategory  [$v];
			foreach ( $articles as $key => $val ) {
				if(!empty($val['cate_id']) && $val['cate_id']==$v){
					$newcate [$val ['cate_id']] ['list'] [$key] = $val;
				}									
				// $article[$key]['cate_name']=$cate[$val['cate_id']]['cate_name'];
			}
		}	
		$cate_id=0;
		if ($article_code) {
			$article = $this->_article_mod->find ( array (
					'conditions' => "code='{$article_code}'" 
			) );
			
			if ($article) {
				foreach ( $article as $k => $v ) {
					$article_id = $v ['article_id'];
				}
				foreach ( $newcate as $key => $value ) {
					if ($value['list']){
						foreach ( $value ['list'] as $k => $v ) {
						
							if ($v ['article_id'] == $article_id) {
								$cate_id = $value ['data'] ['cate_id'];
								break;
							}
						}
					}
					
				}
				$temp=current(i_array_column($article,'article_id'));
				$article=$article["$temp"];
			} else {
				$article_id = '';
				$article = array (
						'title' => '内容正在建设中，敬请期待！',
						'content' => ''
				);
			}
		} else {
			foreach ( $articles as $key => $value ) {
				if ($key && $value ['content'] && $value['cate_id']==key($acategory)) {
					$article = $value;
					$cate_id=$value['cate_id'];
					break;
				}
			}
			$article_id = '';
		}
		$first=array_shift(current($acategory));
		$this->assign ( 'cate_id', $cate_id );

		$this->assign ( 'article_id', $article_id );
		$this->assign ( 'newcate', $newcate );
		$this->_config_seo('title', '帮助中心 - '.$acategory[$first]['cate_name'].' - mfd.麦富迪：酷享本色，特立独行！');
		$this->assign ( 'article', $article );
		$this->display ( 'help.index.html' );
	}
	function ajax_article() {
		$title=empty($_POST['title'])?'':$_POST['title'];
		$article_id = empty ( $_POST ['id'] ) ? '0' : $_POST ['id'];
		if (! $article_id) {
			$this->json_error ( '内容正在建设中，敬请期待！' );
			return;
		}
		$article = $this->_article_mod->get ( array (
				'conditions' => "article_id='{$article_id}'" 
		) );
		$title='帮助中心 - '.$title.' - mfd.麦富迪：酷享本色，特立独行！';
		$this->json_result (array('article'=>$article,'title'=>$title));
	}
}

?>
