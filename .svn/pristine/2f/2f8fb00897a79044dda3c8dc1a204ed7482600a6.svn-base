<?php
/**
 *    优惠券管理控制器
 */
class CouponApp extends BackendApp{
    var $_coupon_mod;
    function __construct(){
        $this->CouponApp();
    }
    function CouponApp(){
        parent::BackendApp();
        $this->_coupon_mod      =& m('coupon');
    }
    function index(){
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'cpn_name',
                'equal' => 'like',
            ),
        ));
        //update order
        if (isset($_GET['sort']) && isset($_GET['order'])){
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))){
                 $sort  = 'cpn_id';
                 $order = 'desc';
            }
        }else{
            $sort  = 'cpn_id';
            $order = 'desc';
        }
        $page = $this->_get_page(30);
        $coupon_list = $this->_coupon_mod->find(array(
            'conditions' => "1 = 1 ".$conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));
        $this->assign('coupon_list', $coupon_list);
        $page['item_count'] = $this->_coupon_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->display('promotion/coupon/index.html');
    }
    //add
    function add(){
        $this->assign('action','add');
        $this->_editor();
    }
    //edit
    function edit(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $coupon=$this->_coupon_mod->get($id);
        if(!empty($coupon)){
            $this->assign('data',$coupon);
            $this->assign('action','edit');
        }else{
            $this->assign('action','add');
        }
        $this->_editor($id);
    }
    //editor
    function _editor(){
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        
        $status=array(
            '0' => '否',
            '1' => '是',
        );
        $this->assign('status',$status);
        $this->display('promotion/coupon/form.html');
    }
    //save
    function toAdd(){
        $stime        = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
        $etime        = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
        $cpn_name     = trim($_POST['cpn_name']);
        $cpn_content  = trim($_POST['cpn_content']);
        $cpn_money    = intval($_POST['cpn_money']);
        $cpn_prefix   = trim($_POST['cpn_prefix']);
        $cpn_point    = intval($_POST['cpn_point']);
        $cpn_rank     = intval($_POST['cpn_rank']);
        if($stime && $etime){
            if($stime >= $etime){
				$this->show_message("结束时间必须大于开始时间！");
				return;
            }
        }
        while(strlen($cpn_prefix)<4){
        	$cpn_prefix.="0";
        }

        
        $data = array(
            'cpn_name'        => $cpn_name,
        	'cpn_money'	      => $cpn_money,
            'cpn_content'     => $cpn_content,
        	'start_time'	  => $stime,
        	'end_time'        => $etime,
            'cpn_prefix'      => $cpn_prefix,
            'cpn_point'       => $cpn_point,
            'cpn_status'      => $_POST['cpn_status'],
            'add_time'        => time(),
            'cpn_rank'        => $cpn_rank,
        );
//         print_exit($data);
        
        if($_POST['cpn_id']===''){//add
            $data['add_time'] = time();
            $id=$this->_coupon_mod->add($data);//return add id
        }else{//edit
            $id=$this->_coupon_mod->edit($_POST['cpn_id'],$data);//return 1/0
        }
        
        if (!$id){
           $this->show_warning('操作失败!');
           return;
        }else{
           $this->show_message('操作成功!',
                   //'continue_add', 'index.php?app=customs&amp;act=add',
                   'back_list',    'index.php?app=coupon'
           );
        }
        
    }

    /* 
     * drop
     */
    function drop(){
        
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id){
            $this->show_warning('请选择要删除的数据!');
            return;
        }
        
//         $this->_coupon_mod->drop('coupon_id in ('.$id.')');
        $this->_coupon_mod->drop(db_create_in($id,'cpn_id'));
        
        $this->show_message('删除成功!','back_list', 'index.php?app=coupon&page=' . $ret_page);
    }
    
    
    /**
     * 给会员发放优惠券
     * @author Ruesin
     */
    function send(){
    	if(!IS_POST){
    	    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	    $coupon=$this->_coupon_mod->get($id);
    	    if(!empty($coupon)){
    	        $this->assign('data',$coupon);
    	        $this->display('promotion/coupon/send.html');
    	    }else{
    	        $this->show_message('参数错误!','点击返回优惠券列表', 'index.php?app=coupon');
    	    }
    	    
    	}else{
    	    $state   = 1;
    	    $members = trim($_POST['member_id']);
    	    $cpn_id  = intval($_POST['cpn_id']);
    	    
    	    if($members == '') $this->show_message('请选择会员!','返回', 'index.php?app=coupon&act=send');
    	    
    	    $memArr  = explode(',', $members);
    	    $fCou =& f('coupon');
    	    
    	    foreach ($memArr as $val){
    	        $add = $fCou -> addCoupon($cpn_id,$val,$msg='管理员发放优惠券!',$type='cpn');
    	        if(!$add){
    	        	$state = 0;
    	        	break;
    	        }
    	    }
    	    if($state == 1)
    	        $this->show_message('发放成功!','返回列表', 'index.php?app=coupon');
    	    else 
    	        $this->show_message('发放失败!','返回', 'index.php?app=coupon&act=send');  		
    	}
    }
    /**
     * Ajax 获取发放会员的信息
     * @author Ruesin
     */
    function ajax_send_member(){
        
        $ids = isset($_GET['ids']) ? trim($_GET['ids']) : '';
        if($ids == '')exit;
        $mMem =& m('member');
        
        $data = $mMem->find(array('conditions' => " user_id in({$ids})"));
        $this->assign('data',$data);
        $this->display('promotion/coupon/send_ajax.html');
        
    }
  
}


