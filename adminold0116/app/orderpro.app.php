<?php

/**
 *    促销管理控制器
 *
 *    @author    Tangsj
 *    @usage    none
 */
class OrderproApp extends BackendApp
{
    var $_order_promotion_mod;
    var $_goodstype_mod;
    var $_gcategory_mod;
    var $_orderlink_mod;
    
    function __construct()
    {
        $this->OrderproApp();
    }

    function OrderproApp()
    {
        parent::BackendApp();

        $this->_order_promotion_mod =& m('order_prorule');
        $this->_goodstype_mod =& m('goodstype'); //商品类型
        $this->_gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
        $this->_link_mod =& m('links');
        $this->_goodslink_mod = &m('goods_prolink');
        $this->_user_mod =& m('member');
        $this->_lv_mod =& m('memberlv');
     
        $this->site = array(
            '1' => 'PC',
            '2' => 'APP',
            '3' => 'WAP',
        );
        $this->fa = array(
            '1' => ' 订单免运费',
            '2' => ' 订单以固定折扣出售',
            '3' => ' 订单以固定价格购买',
            '4' => ' 订单减去固定折扣出售',
            '5' => '  订单减去固定价格购买',
        );
        $this->yh = array(
            '1'=>'当订单商品总价满X时，对所有商品优惠',
            '2'=>'当订单商品数量满X时，给予优惠',
            '3'=>'对所有订单给予优惠',
        );
 
        $this->assign('site',$this->site);
    }

    /**
     *    商品促销索引
     *
     *    @author    tangsj
     *    @return    void
     */
    function index()
    {
        $page=$this->_get_page(30);
        $lists= $this->_order_promotion_mod->find(array(
            'conditions'=>"1=1",
            'field'=>'*',
            'order'=>'add_time desc',
            'limit'=>$page['limit'],
            'count'=>true
        ));
        $page['item_count']=$this->_order_promotion_mod->getCount();
        $this->_format_page($page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css')
        );
        foreach ($lists as $k=>$v){
            $lists[$k]['yhcase'] =  $this->fa[$v['yhcase']];
            $lists[$k]['favorable'] = $this->yh[$v['favorable']];
        }
        
        $this->assign('page_info', $page);
        $this->assign('list', $lists);
        $this->display('cx/order_promotion.index.html');
    }
     /**
     *    新增促销规则
     *
     *    @author    tangsj
     *    @return    void
     */
    function add()
    {
        if (!IS_POST)
        {
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            
            $rules =  $this->_order_promotion_mod->find();
            $this->assign('rules',$rules);
            
            $lvs = $this->_lv_mod->find();
            $this->assign('lvs',$lvs);
            $this->display('cx/order_promotion.form.html');
            
        }
        else
        {
            // 基本信息
         
            $name = $_POST['name'];
            $introduce  = $_POST['introduce'];
            $is_open  = $_POST['is_open'];
            $level  = $_POST['level'];
            $site_name  = $_POST['site_name'];
            $if_ex  = $_POST['if_ex'];
            $member_lv = $_POST['member_lv'];
          
            $stime = isset($_POST['add_time_from']) ? strtotime($_POST['add_time_from']) : 0;
            $etime = isset($_POST['add_time_to']) ? strtotime($_POST['add_time_to']) : 0;
            if($stime && $etime){
                if($stime >= $etime){
                    $this->show_warning("活动结束时间必须大于开始时间！","back");
                    return false;
                }
                if($etime<=time()){
                    $this->show_warning("活动结束时间必须大于当前时间！","back");
                    return false;
                }
            }
            if(empty($site_name)){
                $this->show_warning("请选择生效平台","back");
                return false;
            }
            if(empty($member_lv)){
                $this->show_warning("请选择会员等级","back");
                return false;
            }
            
            $site_name = implode(",", $site_name);
            $member_lv = implode(",", $member_lv);
            
            
            // 优惠条件
            $favorable = $_POST['favorable'];
            //$favorablevalue = $_POST['favorablevalue'];
            $favorablevalue1 = $_POST['favorablevalue1'];
            $favorablevalue2 = $_POST['favorablevalue2'];
            if(!in_array($favorable, array(1,2,3,4))){
                $this->show_warning("请选择优惠条件","back");
                return false;
                
            }
            if($favorable==1){
                $favorablevalue=$favorablevalue1;
            }
            if($favorable==2){
                $favorablevalue=$favorablevalue2;
            }
            if($favorable==3){
                $favorablevalue='all';
            }
            //优惠方案
            $yhcase = $_POST['yhcase'];
            if(!in_array($yhcase, array(1,2,3,4,5))){
                $this->show_warning("请选择优惠方案","back");
                return false;
            
            }
            $casevalue = $_POST['casevalue'];

            if(in_array($yhcase, array(2,3,4,5))){
                if(!$casevalue){
                    $this->show_warning("请填写优惠方案","back");
                    return false;
                }
            }
            
            $data = array(
                'name'=>$name,
                'introduce'=>$introduce,
                'is_open'  =>$is_open,
                'level'    =>$level,
                'site_id'  =>$site_name,
                'if_ex'    =>$if_ex,
                'starttime'=>$stime,
                'endtime'  =>$etime,
                'member_lv_id'=>$member_lv,
                'favorable'=>$favorable,
                'favorable_value'=>$favorablevalue,
                'yhcase'   =>$yhcase,
                'yhcase_value'=>$casevalue,
                'add_time' =>time(),
                
            );
           
            $r = $this->_order_promotion_mod->add($data);
            
             if($r){
                $this->show_message('添加成功','back_list', 'index.php?app=orderpro&act=index&amp;cate_id=0');
            }else{
                $this->show_message('添加失败','<<返回', 'index.php?app=orderpro&act=add&amp;cate_id=0');
            }  
              
        }
    }

    function edit(){

        $id=$_REQUEST['id'];
        $rule= $this->_order_promotion_mod->get($id);
        
        if(empty($rule)){
            $this->show_warning("促销规则不存在","back");
            return false;
        }

        if(!IS_POST){
            $lvs = $this->_lv_mod->find();
            $cof=explode(',', $rule['site_id']);
            $member_lv=explode(',', $rule['member_lv_id']);
            $rules =  $this->_order_promotion_mod->find();
            $this->assign('rules',$rules);
            $this->assign('cof', $cof);
            $this->assign('member_lv', $member_lv);
            $this->assign('rule', $rule);
            $this->assign('lvs',$lvs);
            $this->display('cx/order_promotion.form.html');

        }else{
            $id = $_POST['id'];
            $name = $_POST['name'];
            $introduce  = $_POST['introduce'];
            $is_open  = $_POST['is_open'];
            $level  = $_POST['level'];
            $site_name  = $_POST['site_name'];
            $if_ex  = $_POST['if_ex'];
            $member_lv = $_POST['member_lv'];
          
            $stime = isset($_POST['add_time_from']) ? strtotime($_POST['add_time_from']) : 0;
            $etime = isset($_POST['add_time_to']) ? strtotime($_POST['add_time_to']) : 0;
            if($stime && $etime){
                if($stime >= $etime){
                    $this->show_warning("活动结束时间必须大于开始时间！","back");
                    return false;
                }
                if($etime<=time()){
                    $this->show_warning("活动结束时间必须大于当前时间！","back");
                    return false;
                }
            }
            
            if(empty($site_name)){
                $this->show_warning("请选择生效平台","back");
                return false;
            }
            
            if(empty($member_lv)){
                $this->show_warning("请选择会员等级","back");
                return false;
            }
            
            $site_name = implode(",", $site_name);
            $member_lv = implode(",", $member_lv);
            
            // 优惠条件
            $favorable = $_POST['favorable'];
            $favorablevalue1 = $_POST['favorablevalue1'];
            $favorablevalue2 = $_POST['favorablevalue2'];
            if(!in_array($favorable, array(1,2,3,4))){
                $this->show_warning("请选择优惠条件","back");
                return false;
                
            }
            if($favorable==1){
                $favorablevalue=$favorablevalue1;
            }
            if($favorable==2){
                $favorablevalue=$favorablevalue2;
            }
            if($favorable==3){
                $favorablevalue='all';
            }
            //优惠方案
            $yhcase = $_POST['yhcase'];
            $casevalue = $_POST['casevalue'];
            if(!in_array($yhcase, array(1,2,3,4,5))){
                $this->show_warning("请选择优惠方案","back");
                return false;
            
            }
         
            if(in_array($yhcase, array(2,3,4,5))){
                if(!$casevalue){
                    $this->show_warning("请填写优惠方案","back");
                    return false;
                }
            }
            
            $data = array(
                'name'=>$name,
                'introduce'=>$introduce,
                'is_open'  =>$is_open,
                'level'    =>$level,
                'site_id'  =>$site_name,
                'if_ex'    =>$if_ex,
                'starttime'=>$stime,
                'endtime'  =>$etime,
                'member_lv_id'=>$member_lv,
                'favorable'=>$favorable,
                'favorable_value'=>$favorablevalue,
                'yhcase'   =>$yhcase,
                'yhcase_value'=>$casevalue,
                
            );
          
            $r = $this->_order_promotion_mod->edit($id,$data);

            if($r){
                $this->show_message('修改成功','back_list', 'index.php?app=orderpro&act=index');
            }else{
                $this->show_message('修改失败','<<返回', 'index.php?app=orderpro&act=edit');
            }  
        }
    }
    
    function drop(){
        $id=$_REQUEST['id'];
        $rule= $this->_order_promotion_mod->get($id);
        
        if(empty($rule)){
            $this->show_warning("促销规则不存在","back");
            return false;
        }
        
        $c_id = $this->_order_promotion_mod->drop($id);
   
        $this->show_message('促销规则删除成功','<<返回','index.php?app=orderpro&act=index&amp;cate_id=0');
    }
    
}

?>