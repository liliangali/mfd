<?php

/**
 *    促销管理控制器
 *
 *    @author    Tangsj
 *    @usage    none
 */
class DiscountApp extends BackendApp
{
    var $_goods_promotion_mod;
    var $_goodstype_mod;
    var $_gcategory_mod;
    var $_goodslink_mod;
    
    function __construct()
    {
        $this->DiscountApp();
    }

    function DiscountApp()
    {
        parent::BackendApp();

        $this->_goods_promotion_mod =& m('goods_prorule');
        $this->_goodstype_mod =& m('goodstype'); //商品类型
        $this->_gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
        $this->_link_mod =& m('goods_prorel');
        $this->_goodslink_mod = &m('goods_prolink');
        $this->_user_mod =& m('member');
        $this->_lv_mod =& m('memberlv');
        $this->_fdiy_management_mod =& m('fdiy_management');
     
        $this->site = array(
            '1' => 'PC',
            '2' => 'APP',
            '3' => 'WAP',
        );
        $this->fa = array(
            '1' => ' 符合条件的商品以固定折扣出售',
            '2' => ' 符合条件的商品以固定价格出售',
            '3' => ' 符合条件的商品减去固定折扣出售',
            '4' => ' 符合条件的商品减去固定价格出售',
            '5' => ' 符合条件的商品免邮',
			'6' => ' 符合条件的商品进行满减优惠',
			'7' => ' 符合条件的商品进行买送优惠',
			'8' => ' 符合条件的商品有赠品优惠',
        );
        
        $this->yhtj = array(
            '1' => '  商品类型',
            '2' => '  指定商品',
            '3' => '  商品分类',
            '4' => '  所有商品',
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
        $lists= $this->_goods_promotion_mod->find(array(
            'conditions'=>"1=1",
            'field'=>'*',
            'order'=>'add_time desc',
            'limit'=>$page['limit'],
            'count'=>true
        ));
        $page['item_count']=$this->_goods_promotion_mod->getCount();
        $this->_format_page($page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css')
        );
        foreach ($lists as $k=>$v){
            $lists[$k]['yhcase'] =  $this->fa[$v['yhcase']];
            $lists[$k]['favorable'] = $this->yhtj[$v['favorable']];
        }
        
        $this->assign('page_info', $page);
        $this->assign('list', $lists);
        $this->display('cx/goods_promotion.index.html');
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
            $goods_types = $this->_goodstype_mod->find();
            $gcategories = $this->_gcategory_mod->get_list(0);
            
            $rules = $this->_goods_promotion_mod->find();
            $lvs = $this->_lv_mod->find();
            $this->assign('lvs',$lvs);
            $this->assign('parents', $this->_get_options());
            $this->assign('diym', $this->getdiym());
            $this->assign('goods_type',$goods_types);
            $this->assign('rules',$rules);
            $this->assign('gcategories', $gcategories);
            $this->display('cx/goods_promotion.form.html');
            
        }
        else
        { 
          
            $name = $_POST['name'];
            $introduce  = $_POST['introduce'];
            $is_open  = $_POST['is_open'];
            $level  = $_POST['level'];
            $site_name  = $_POST['site_name'];
            $if_ex  = $_POST['if_ex'];
            $member_lv = $_POST['member_lv'];
            $stime = isset($_POST['add_time_from']) ? strtotime($_POST['add_time_from']) : 0;
            $etime = isset($_POST['add_time_to']) ? strtotime($_POST['add_time_to']) : 0;
            $favorable = $_POST['favorable'];
            $goods_type= $_POST['goods_type'];
            $goods_cate= $_POST['goods_cate'];
            $diym_cate = $_POST['diym_cate'];
            
           // var_dump($_POST);die();
            if(empty($site_name)){
                $this->show_warning("请选择生效平台","back");
                return false;
            }
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

            if(empty($member_lv)){
                $this->show_warning("请选择会员等级","back");
                return false;
            }
           
            
            $site_name = implode(",", $site_name);
            $member_lv = implode(",", $member_lv);
            
            // 优惠条件
          
            
            if(!in_array($favorable, array(1,2,3,4))){
                $this->show_warning("请选择优惠条件","back");
                return false;
                
            }
            //优惠方案
            $yhcase = $_POST['yhcase'];
            if(!in_array($yhcase, array(1,2,3,4,5,6,7,8))){
                $this->show_warning("请选择优惠方案","back");
                return false;
            
            }
            $casevalue = $_POST['casevalue'];
			$casevalue2 = $_POST['casevalue2'];
			 if($yhcase !=5 && $yhcase !=8){
                if(!$casevalue){
                    $this->show_warning("请填写优惠方案","back");
                    return false;
                }
            }
            if($yhcase =='6' or $yhcase =='7'){
                if(!$casevalue2){
                    $this->show_warning("请填写优惠方案2","back");
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
                'add_time' =>time(),
                
            );
            
            $r = $this->_goods_promotion_mod->add($data);
            $id = $r;
            
            if($id){
                
                if($favorable==1){
                    
                    $arr = array();
                    foreach ($goods_type as $key => $value) {

                        $links = array(
                        'rules_id'=>$id,
                        'favorable_id'=>$favorable,
                        'favorable_value'=>$value,
                        );
                        $arr[] = $links;
                    }
                    $this->_goodslink_mod->add($arr);
                    
                     
                }
                if($favorable==2){

                    if($_POST['linkid']){
                        $links = explode(",",$_POST['linkid']);
                    
                        foreach($links as $key => $val){
                            $this->_link_mod->add(array("d_id" => $id, "c_id" => $val, 'lorder' => $_POST['lorder'][$val]));
                        }
                      
                    }
                }
                if($favorable==3){

                    $brr = array();
                    foreach ($goods_cate as $key => $value) {

                        $links = array(
                        'rules_id'=>$id,
                        'favorable_id'=>$favorable,
                        'favorable_value'=>$value,
                        );
                        $brr[] = $links;
                    }
                    $this->_goodslink_mod->add($brr);
                    
                    if($diym_cate){
                        $crr = array();
                        foreach ($diym_cate as $k=>$v){
                            $links = array(
                                'rules_id'=>$id,
                                'favorable_id'=>$favorable,
                                'favorable_value'=>$v,
                                'type'=>1,
                            );
                            $crr[] = $links;
                            
                        }
                        $this->_goodslink_mod->add($crr);
                    }
                    
                    
                }
                
                
                $cases = array(
                    'yhcase'=>$yhcase,
                    'yhcase_value'=>$casevalue,
					'yhcase_value2'=>$casevalue2,
                );
                
                $this->_goods_promotion_mod->edit($id,$cases);
                
            }
            
             if($id){
                $this->show_message('添加成功','back_list', 'index.php?app=discount&act=index&amp;cate_id=0');
            }else{
                $this->show_message('添加失败','<<返回', 'index.php?app=discount&act=add&amp;cate_id=0');
            }  
              
        }
    }
    
    function edit(){

        $id=$_REQUEST['id'];
        $rule= $this->_goods_promotion_mod->get($id);
        $rule_link =  $this->_goodslink_mod->find("rules_id='{$id}' AND favorable_id='{$rule[favorable]}'");
        $rule['links'] = $rule_link;
        $rules = $this->_goods_promotion_mod->find();

        
        if(empty($rule)){
            $this->show_warning("促销规则不存在","back");
            return false;
        }
        
        //关联商品
        $_olink=$this->_link_mod->find("d_id = {$id}");
        foreach ($_olink as $row){
            $link[]=$row['c_id'];
        }
        if(!IS_POST){

            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $lvs = $this->_lv_mod->find();
            $member_lv=explode(',', $rule['member_lv_id']);

            $sof=explode(',', $rule['site_id']);
            $goods_types = $this->_goodstype_mod->find();
            $gcategories = $this->_gcategory_mod->get_list(0);
            
       
            $suitArr = array();
            if(isset($link) && !empty($link)){
                $linkid=implode(',', $link);
         
                $suitArr = $this->_link_mod->find(array(
                    "conditions" => "c_id ".db_create_in($link) . " AND d_id='{$id}'",
                    "join"       => "has_goods",
                ));
               
            
            }else{
                $linkid='';
            }
            
           
            //
            $this->assign('rules',$rules);
            $this->assign('linkid',$linkid);
            $this->assign('suitArr',$suitArr);
            $this->assign('member_lv', $member_lv);
            $this->assign('lvs',$lvs);
            $this->assign('parents', $this->_get_options());
            $this->assign('goods_type',$goods_types);
            $this->assign('gcategories', $gcategories);
            $this->assign('sof', $sof);
            $this->assign('cof', $rule_link);
            $this->assign('rule', $rule);
            $this->assign('diym', $this->getdiym());
            $this->display('cx/goods_promotion.form.html');

        }else{
           
            $id = $_POST['id'];
          
            $name = $_POST['name'];
            $introduce  = $_POST['introduce'];
            $is_open  = $_POST['is_open'];
            $level  = $_POST['level'];
            $site_name  = $_POST['site_name'];
            $if_ex  = $_POST['if_ex'];
            $stime = isset($_POST['add_time_from']) ? strtotime($_POST['add_time_from']) : 0;
            $etime = isset($_POST['add_time_to']) ? strtotime($_POST['add_time_to']) : 0;
            $yhcase = $_POST['yhcase'];
            $casevalue = $_POST['casevalue'];
            $favorable = $_POST['favorable'];
            $goods_type= $_POST['goods_type'];
			$casevalue2 = $_POST['casevalue2'];
            $goods_cate= $_POST['goods_cate'];
            $member_lv = $_POST['member_lv'];
            $diym_cate = $_POST['diym_cate'];
            $_olink=$this->_link_mod->find("d_id = {$id}");
            
           
            if(empty($site_name)){
                $this->show_warning("请选择生效平台","back");
                return false;
            }
            $site_name = implode(",", $site_name);
       
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


            if(empty($member_lv)){
                $this->show_warning("请选择会员等级","back");
                return false;
            }
            $member_lv = implode(",", $member_lv);


            // 优惠条件
            if(!in_array($favorable, array(1,2,3,4))){
                $this->show_warning("请选择优惠条件","back");
                return false;
                
            }
             //优惠方案
            if(!in_array($yhcase, array(1,2,3,4,5,6,7,8))){
                $this->show_warning("请选择优惠方案","back");
                return false;
            
            }
            
           
			 if($yhcase !=5 && $yhcase !=8){
                if(!$casevalue){
                    $this->show_warning("请填写优惠方案","back");
                    return false;
                }
            }
            if($yhcase =='6' or $yhcase =='7'){
                if(!$casevalue2){
                    $this->show_warning("请填写优惠方案2","back");
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
                
            );

            // 修改 规则
            $r = $this->_goods_promotion_mod->edit($id,$data);

            if($r)
            {
                if($favorable==1)
                {
                    $dropid =  $this->_goodslink_mod->drop("rules_id='{$id}'");

                    $arr = array();
                    foreach ($goods_type as $key => $value) {

                        $res = array(
                            'rules_id'=>$id,
                            'favorable_id'=>$favorable,
                            'favorable_value'=>$value,
                        );
                        $arr[] = $res;

                    }

                    $this->_goodslink_mod->add($arr);   
                       
                }elseif ($favorable ==2) {
                    
                    if(!empty($_POST['linkid'])){
                    
                        $links = explode(",",$_POST['linkid']);
                    
                        $_ls = array();
                        foreach($links as $val){
                            $_ls[$val] = $val;
                        }
                    
                        $eds = array();
                    
                        if(!empty($_olink)){
                            foreach($_olink as  $val){
                                if(!isset($_ls[$val["c_id"]])){
                                    $this->_link_mod->drop($val["id"]);
                                }else{
                                    $eds[] = $_ls[$val["c_id"]];
                                    unset($_ls[$val["c_id"]]);
                                }
                            }
                    
                        }
                    
                        if(!empty($_ls)){
                            foreach($_ls as $val){
                                $this->_link_mod->add(array("d_id" => $id, "c_id" => $val, 'lorder' => $_POST['lorder'][$val]));
                            }
                        }
                    
                        if(!empty($eds)){
                            foreach($eds as $key => $val){
                                $this->_link_mod->edit("d_id='{$id}' AND c_id='{$val}'", array('lorder' => $_POST['lorder'][$val]));
                            }
                        }
                    
                    }else{
                        $this->_link_mod->drop("d_id = '{$id}'");
                    }
                   
                }elseif ($favorable ==3) {

                    $dropid =  $this->_goodslink_mod->drop("rules_id='{$id}'");
                    if($goods_cate){
                        
                        $brr = array();
                        foreach ($goods_cate as $key => $value) {
                        
                            $res = array(
                                'rules_id'=>$id,
                                'favorable_id'=>$favorable,
                                'favorable_value'=>$value,
                            );
                            $brr[] = $res;
                        }
                        $this->_goodslink_mod->add($brr);
                        
                    }
                    
                    if($diym_cate){
                        $crr = array();
                        foreach ($diym_cate as $k => $v) {
                        
                            $res = array(
                                'rules_id'=>$id,
                                'favorable_id'=>$favorable,
                                'favorable_value'=>$v,
                                'type'   =>1,
                            );
                            $crr[] = $res;
                        }
                        $this->_goodslink_mod->add($crr);
                        
                    }
                }elseif ($favorable ==4) {
                    $dropid =  $this->_goodslink_mod->drop("rules_id='{$id}'");
                }
                // 修改优惠方案
                $cases = array(
                    'yhcase'=>$yhcase,
                    'yhcase_value'=>$casevalue,
					'yhcase_value2'=>$casevalue2,
                );
                
                $this->_goods_promotion_mod->edit($id,$cases);
            }

            if($r){
                $this->show_message('修改成功','back_list', 'index.php?app=discount&act=index');
            }else{
                $this->show_message('修改失败','<<返回', 'index.php?app=discount&act=edit');
            }  

        }


    }
    function loadgoods(){
        
        $ids = trim($_GET['ids']);
        
        $did = intval($_GET["did"]);
        if(!$ids){
            $this->json_error('非法操作');
            return;
        }
        $mod = &m("goods");
        $list = $mod->find(array(
            "conditions" => "goods_id IN ($ids)",
            'fields'     => "goods_id,name as name"
        ));
        
        $retArr = array();
        
        $links = $this->_link_mod->find(array(
            "conditions" => "d_id='{$did}'",
        ));
        
        $lorder = array();
        foreach((array)$links as $key => $val){
            $lorder[$val["c_id"]] = $val["lorder"];
        }
        
        foreach((array)$list as $key => $val){
            $list[$key]['lorder'] = isset($lorder[$val['goods_id']]) ? $lorder[$val["goods_id"]] : 0;
        }
        foreach((array)$list as $key => $val){
            $retArr[] = $val;
        }
      
        $this->json_result($retArr);
        die();
    }
    
    function drop(){
        $id=$_REQUEST['id'];
        $rule= $this->_goods_promotion_mod->get($id);
        
        if(empty($rule)){
            $this->show_warning("促销规则不存在","back");
            return false;
        }
        
        $c_id = $this->_goods_promotion_mod->drop($id);
        if($c_id){
            $this->_goodslink_mod->drop("rules_id='{$id}'");
        }
        
        
        $this->show_message('促销规则删除成功','<<返回','index.php?app=discount&act=index&amp;cate_id=0');
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $gcategories = $this->_gcategory_mod->find(array(
            'conditions'=>"parent_id=0",
            'fields'=>"cate_id,cate_name,parent_id",
            'order' =>"cate_id ASC"
        ));
        foreach ($gcategories as $k=>$v){
            $second = $this->_gcategory_mod->find(array(
            'conditions'=>"parent_id='$k'",
            'fields'=>"cate_id,cate_name,parent_id",
            ));
          
            $gcategories[$k]['second'] = $second;
        }
        //print_r($gcategories);die();
    
        return $gcategories;
    }
    
    
    /* 取得diy商品分类*/
    function getdiym($except = NULL)
    {
        $gcategories = $this->_fdiy_management_mod->find(array(
            'conditions'=>"parent_id=0",
            'fields'=>"cate_id,cate_name,parent_id",
            'order' =>"cate_id ASC"
        ));
        foreach ($gcategories as $k=>$v){
            $second = $this->_fdiy_management_mod->find(array(
                'conditions'=>"parent_id='$k'",
                'fields'=>"cate_id,cate_name,parent_id",
            ));
    
            $gcategories[$k]['second'] = $second;
        }
        //print_r($gcategories);die();
    
        return $gcategories;
    }
    
    
}

?>