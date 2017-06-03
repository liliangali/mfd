<?php
/**
 * 瀑布流
 *
 */
class WallApp extends MallbaseApp
{

	var $test_data = '';
    function __construct()
    {
//     	distance = 500 			//滚动条距离底部多少像素时触发瀑布流，不能小于底部模板的高度
//     	spage_max = 3 		//每页动态加载次数
//     	spage_size = 10 	//每次加载的商品个数
//     	page_max = 100		//发现页面最多显示页数
        $this->WallApp();
    }
    function WallApp()
    {  
    	
    	
    	$this->test_data = array(0=>array('id'=>0,
    			'uid'=>'0',
    			'uname'=>'设计系小女生',
    			'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    			'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    			'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    			'price'=>'1159.00',
    			'likes'=>'0',
    			 'comments'=>"1",
    'comments_cache'=>'a:1:{i:0;a:4:{s:2:"id";i:2;s:3:"uid";i:21;s:5:"uname";s:7:"test001";s:4:"info";s:8:"评论..";}}',
    'comment_list'=>array(
    		0=>array('id'=>2,'uid'=>21,'uname'=>'test001','info'=>'评论..'),
    		1=>array('id'=>2,'uid'=>21,'uname'=>'test001','info'=>'评论..'),
    		2=>array('id'=>2,'uid'=>21,'uname'=>'test001','info'=>'评论..'))

    	),
    			1=>array('id'=>1,
    					'uid'=>'1',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img04.taobaocdn.com/bao/uploaded/i4/T1vdvYXktiXXb1Opc__104853.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			2=>array('id'=>2,
    					'uid'=>'2',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img03.taobaocdn.com/bao/uploaded/i3/T1ms_UXi0bXXb4juQ1_041036.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			3=>array('id'=>3,
    					'uid'=>'3',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			4=>array('id'=>4,
    					'uid'=>'4',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			5=>array('id'=>5,
    					'uid'=>'5',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			6=>array('id'=>6,
    					'uid'=>'6',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			7=>array('id'=>7,
    					'uid'=>'7',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			),
    			8=>array('id'=>8,
    					'uid'=>'8',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			)
    			,
    			9=>array('id'=>9,
    					'uid'=>'9',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			)
    			,
    			10=>array('id'=>10,
    					'uid'=>'10',
    					'uname'=>'设计系小女生',
    					'title'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'intro'=>'浪漫一身 2012冬装新款 专柜正品 欧美范 羊毛毛纯色呢短外套',
    					'img'=>'http://img02.taobaocdn.com/bao/uploaded/i2/T1DReVXjtbXXcISRE9_104415.jpg_210x1000.jpg',
    					'price'=>'1159.00',
    					'likes'=>'0',
    					'comments'=>'0',
    					'comments_cache'=>'',
    					'comment_list'=>false,
    			)
    	);
    	
        parent::__construct();
    }
    function index()
    {
    	$cs =& cs();
    	$gcategories = $cs->_get_gcategory();
    	$tree = $cs->_tree($gcategories);
//     	$a = $tree->getOptions(0);				//生成嵌套格式的树形
//     	$a = $tree->getChilds(8001);			//所有的子id
    	$a = $tree->getParents(128017);				//查找父id 逐级返回. 不包括本身
//     	$a = $cs->deep_tree($gcategories,3);	//生成嵌套格式的树形数组 
	    $id = 116;
// 	    $code = '8001:609997|24:36|24:100013|24:100037|298:31655|298:376|298:450|298:528|298:427,485';
	    $cs =& cs();
	    $id = 195;
// 	    $code = '8001:612224|313:31655|371:376|24:100017|24:38|24:100041|417:1500|417:450|417:485,499';
// 	    $code = '8001:612224|24:38|24:100013|24:100015|24:100017|24:100037|24:100039|24:100041|298:31655|298:376|298:450|298:528|298:427';
	    $code = '8001:612224|24:38|24:100017|24:100041|298:31655|298:376|298:427';
//     	$b = $cs->parsing_code($id, $code);
    	$b = $cs->parsing_code_base($id);
    	var_dump($b);
//     	$b = $cs->get_basis_info($id, 1);


    	exit();
    	/*会员默认等级*/
//     	$member_lv_mod =& m('memberlv');
//     	/* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
// //     	$m_lv = $member_lv_mod->get_default_level(array('lv_type'=>'joining'));
//     	$m_lv = $member_lv_mod->get_default_level();
//     	var_dump($m_lv);
//     	$m_lv['member_lv_id'];	//sting "1"
    	
    	/* 等级自动升级 */
    	$member_lv_mod =& m('memberlv');
    	$member_lv_mod->auto_level(185,'member');
//     	$member_lv_mod->auto_level(186,'service',100);
//     	$member_lv_mod->auto_level(172,'joining',100);
//     	$member_lv_mod->auto_level(185,'supplier',100);
    	
    	
    	
//     	var_dump($_SESSION['user_info']['user_id']);
//     	<pin:load type="js" href="__STATIC__/js/jquery/plugins/jquery.tools.min.js,__STATIC__/js/jquery/plugins/jquery.masonry.js,__STATIC__/js/jquery/plugins/formvalidator.js,__STATIC__/js/fileuploader.js,__STATIC__/js/pinphp.js,__STATIC__/js/front.js,__STATIC__/js/dialog.js,__STATIC__/js/wall.js,__STATIC__/js/item.js,__STATIC__/js/user.js,__STATIC__/js/album.js" />

//     	__STATIC__/js/jquery/plugins/jquery.tools.min.js,
//     	__STATIC__/js/jquery/plugins/jquery.masonry.js,
//     	__STATIC__/js/jquery/plugins/formvalidator.js,
//     	__STATIC__/js/fileuploader.js,
//     	__STATIC__/js/pinphp.js,
//     	__STATIC__/js/front.js,
//     	__STATIC__/js/dialog.js,
//     	__STATIC__/js/wall.js,
//     	__STATIC__/js/item.js,
//     	__STATIC__/js/user.js,
//     	__STATIC__/js/album.js

    		
    	$this->assign('item_list', $this->test_data);
        $this->display('wall/index.html');
    }
    public function index_ajax() {
    	
//     	$tag = $this->_get('tag', 'trim'); //标签
//     	$sort = $this->_get('sort', 'trim', 'hot'); //排序
//     	switch ($sort) {
//     		case 'hot':
//     			$order = 'hits DESC,id DESC';
//     			break;
//     		case 'new':
//     			$order = 'id DESC';
//     			break;
//     	}
//     	$where = array();
//     	$tag && $where['intro'] = array('like', '%' . $tag . '%');
    	$this->wall_ajax($where, $order);
    }
    
    public function user_ajax() {
    	$this->_init_view();
    	 $resp = $this->_view->fetch("club/card_info.html");
    	 $this->ajaxReturn(1, '', $resp);
    }
    
    /**
     * 瀑布加载
     */
    public function wall_ajax($where = array(), $order = 'id DESC', $field = '') {
    	
//     	$spage_size = C('pin_wall_spage_size'); //每次加载个数
//     	$spage_max = C('pin_wall_spage_max'); //加载次数
//     	$p = $this->_get('p', 'intval', 1); //页码
//     	$sp = $this->_get('sp', 'intval', 1); //子页
    
//     	//条件
//     	$where_init = array('status'=>'1');
//     	$where = array_merge($where_init, $where);
//     	//计算开始
//     	$start = $spage_size * ($spage_max * ($p - 1) + $sp);
//     	$item_mod = M('item');
//     	$count = $item_mod->where($where)->count('id');
//     	$field == '' && $field = 'id,uid,uname,title,intro,img,price,likes,comments,comments_cache';
//     	$item_list = $item_mod->field($field)->where($where)->order($order)->limit($start.','.$spage_size)->select();
//     	foreach ($item_list as $key=>$val) {
//     		//解析评论
//     		isset($val['comments_cache']) && $item_list[$key]['comment_list'] = unserialize($val['comments_cache']);
//     	}
		if ($_GET['sp'] == 2){
			$this->test_data = array_merge($this->test_data,$this->test_data);
// 			var_dump($this->test_data);exit();
		}
    		
    	$this->assign('item_list', $this->test_data);
//     	$resp = $this->fetch('public:waterfall');
    	$resp = $this->_view->fetch("wall/waterfall.html");
   
    	$data = array(
    			'isfull' => 1,
    			'html' => $resp
    	);
//     	$count <= $start + $spage_size && $data['isfull'] = 0;
    	$this->ajaxReturn(1, '', $data);
    }
    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
     function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
    	header('Content-Type:text/html; charset=utf-8');
    	exit(json_encode(array(
    			'status' => $status,
    			'msg' => $msg,
    			'data' => $data,
    			'dialog' => $dialog,
    	)));
    	
    }
}

?>
