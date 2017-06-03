<?php
/**
 *
 *    @author   yusw
 *    @usage    none
 */
class FxApp extends BackendApp
{
    //    private $mtm_logs_status = array(10030=> '制版', 10031=>'生产', '10032' =>'备货', 10034 =>'已发货');


    private $_order_mod;
    private $_order_serve_mod;
    private $_order_serve_log_mod;
    private $_order_cash_log_mod;
    private $_order_goods_mod;
    private  $_repair_responsible_mod;
    private  $_order_fx_mod;
    private $_pl_info;
    private $_sign_info =array(1=>'线上客服',2=>'线下门店');

    //order_serve status
    private $_status_info = array(
        0=>'用户未提交申请',
        1=>'方案未提交',
        2=>'提交失败',
        3=>'审核失败',
        4=>'审核中',
        5=>'待付款',
        6=>'已取消',
        7=>'已付款',
        8=>'生产',
        9=>'申请发货失败', //=>壮志 物流推送  失败   7=》9
        10=>'申请发货',
        11=>'已发货',
        12=>'已完成',   //=>李亮
    );

    private $_pl_status = array(
        '0003' => '西服', //MXF
        '0004' => '西裤', //MXK
        '0005' => '马夹',//MMJ
        '0006' => '衬衣',//MCY
        '0007' =>  '大衣',//MDY
        '0018' =>'立领西服',
        '0010' =>'礼服', //MLF
        //                '0008'=> '配件',
        '0017'  => '男短裤', //MDK
        '0011'  => '女西服', //WXF
        '0012' => '女西裤', //WXK
        '0016' => '女衬衣', //WCY
        '0021' =>  '女大衣',//WDY
        '0022'=>'女风衣短款',
        '0023'=>'女风衣长款',
        '0024'=>'男风衣短款',
        '0025'=>'男风衣长款',
    );


    //推送时候 需要品类转码
    private $_pl_for_str = array(
        '0003'  => 'MXF',//'西服', '上衣'
        '0004' => 'MXK',//'西裤',
        '0005' => 'MMJ',//'马夹',
        '0006'  =>'MCY',// '衬衣',
        '0007' => 'MDY',// '大衣',

        '0018' =>'MXF',//'立领西服',  用上衣（西服）

        '0010'=>'MLF',//'礼服',
        //                '0008'=> '配件',
        '0017'  => 'MDK',//'男短裤',
        '0011'  => 'WXF',//'女西服',
        '0012' => 'WXK',//'女西裤',
        '0016' =>'WCY',// '女衬衣',
        '0021' =>  'WDY',//'女大衣',


        '0022'=>'MDY',//'女风衣短款',   //从这开始直接用大衣编码
        '0023'=>'MDY',//'女风衣长款',
        '0024'=>'MDY',//'男风衣短款',
        '0025'=>'MDY',//'男风衣长款',
    );
    private $color_status = array(
        '101'=>'101#-乳白色',
        '102'=>'102#--白色',
        '3750'=>'3750#--银灰色',
        '3747'=>'3747#-浅灰色',
        '3687'=>'3687#--中灰色',
        '432'=>'432#--浅蓝色',
        '599'=>'599#--蓝色',
        '1059'=>'1059#-绿松石',
        '455'=>'455#--深蓝色',
        '3712'=>'3712#-藏蓝色',
        '352'=>'352#--淡黄色',
        '1017'=>'1017#--金色',
        '3172'=>'3172#--黄色',
        '3077'=>'3077#-中黄色',
        '321'=>'321#-橙色',
        '3701'=>'3701#-浅粉色',
        '624'=>'624#-粉色',
        '1034'=>'1034#-深粉色',
        '714'=>'714#-鲜红色',
        '138'=>'138#-红色',
        '177'=>'177#--浅紫色',
        '633'=>'633#-蓝紫',
        '189'=>'189#-紫罗兰',
        '3601'=>'3601#--紫色',
        '3742'=>'3742#-深紫',
        '1093'=>'1093#-青绿色',
        '3334'=>'3334#-黄绿色',
        '262'=>'262#-浅橄榄色',
        '3813'=>'3818#--中绿色',
        '3813'=>'3813#-中绿',
        '598'=>'598#-土褐色',
        '392'=>'392#-卡其色',
        '1142'=>'3099#--浅棕色',
        '1161'=>'1161#--中棕色',
        '1173'=>'1173#-深橄榄色',
        '572'=>'572#--深绿色',
        '144'=>'144#-深红色',
        '145'=>'145#--酒红色',
        '1196'=>'1196#-枣红色',
        '489'=>'489#--深灰色',
        '3655'=>'3655#--海军蓝',
        '3720'=>'3720#-深蓝灰',
        '103'=>'103#--黑色',
        '1041#--鲜肉色'=>'1041#--鲜肉色',
        '1308#--深棕色'=>'1308#--深棕色',
        '1023#--粉色'=>'1023#--粉色',
        '312'=>'312#-蛋黄',
        '1203'=>'1203#-宝石蓝',
        '3694'=>'3694#-墨绿',
        '3618'=>'3618#-灰色',
        'MG2'=>'MG2#-金色',
        'MS1'=>'MS1#-银色',
        '144#--深红色'=>'144#--深红色',
        '1179#--白色'=>'1179#--白色',
        '1180#--黑色'=>'1180#--黑色',
        '1058#--中蓝色'=>'1058#--中蓝色',
        '8032#--杏色'=>'8032#--杏色',
        '1043#--桔黄色'=>'1043#--桔黄色',
        '1285#--浅绿色'=>'1285#--浅绿色',
        '8103#--天蓝色'=>'8103#--天蓝色',
        '1162'=>'1162#-深棕色',
        '3099'=>'3099#--浅棕色',
        '棕褐色'=>'101#棕褐色', //信息部停用
    );


    function __construct()
    {
        parent::__construct();
        define("TYPE", "order_serve/");

        //暂时先放这  品类的暂时用不到了
        //        $path = ROOT_PATH."/includes/libraries/diys.lib.php";
        //        file_exists($path) && $this->_pl_info = include $path;

        //自己建立的表
        $this->_order_serve_mod = & m('orderserve');
        $this->_order_serve_info_mod = & m('orderserveinfo');
        $this->_order_serve_log_mod =& m('orderservelog');
        $this->_order_fx_mod =& m('orderfx');

        $this->_cart_mod=& m('cart');//刺绣
        $this->_member_mod = &m('member');
        $this->_serve_mod = &m('serve');
        $this->_figure_liangti_mod =&m('figure_liangti');

        $this->_rcmtmlog_mod = &m('rcmtmlog'); //type 1
        $this->_orderfigure_mod = &m('orderfigure');
        $this->_order_mod = & m('order');
        $this->_figure_order_mod = & m('figureorderm');// liangti->server_id   982   废弃
        $this->_order_goods_mod =&m('ordergoods'); // pl
        $this->_order_cash_log_mod = & m('ordercashlog');


        //rcmtm
        $this->_size_part_mod = &m('sizepart');     //返修量体部位档案
        $this->_repair_part_mod = &m('repairpart'); //返修量体部位调整价格、换片档案
        $this->_repair_responsible_mod = &m('repairresponsible'); //返修量体部位调整价格、换片档案
        $this->_special_question_mod = &m('specialquestion');
        $this->_special_solution_mod = &m('specialsolution');
        $this->_repair_attachment_mod =&m('repairattachment');

        //权限
        $serve_id = $_SESSION['admin_info']['user_id'];
        $this->assign('admin',$serve_id);
        $serve_info =$this->_serve_mod->get("userid='{$serve_id}'");
        $qx_serve_id =$serve_info['idserve'];
        $this->qx_conditions =$qx_serve_id?" and order_serve.serve_id='{$qx_serve_id}'":'';

    }

    /*
     * 返修详情页
     */
    function info(){
        $id =$_REQUEST['id'];
        if(!$id){
            $this->show_warning('返修id不能为空');
            return;
        }

        //返修原因
        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );


        //        $order_figure = $this->_orderfigure_mod->find("order_id='{$order_id}'");
        $fx_info = $this->_order_serve_info_mod->get(array(
            'join'          => 'has_order_serve',
            'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            'fields'=>"*,order_serve_info.rec_id as rec_id"
        ));


        //品类需要添加
        //$pl_info = $this->_cart_mod->_customs(); //可以提出来
        !empty($fx_info['add_time'])&&$fx_info['add_time'] =date('Y-m-d H:i:s',$fx_info['add_time']);
        !empty($fx_info['to_time']) && $fx_info['to_time'] =date('Y-m-d H:i:s',$fx_info['to_time']);
        !empty($fx_info['old_to_time']) && $fx_info['old_to_time'] =date('Y-m-d H:i:s',$fx_info['old_to_time']);
        !empty($fx_info['send_time']) && $fx_info['send_time'] =date('Y-m-d H:i:s',$fx_info['send_time']);
        !empty($fx_info['status_time']) && $fx_info['status_time'] =date('Y-m-d H:i:s',$fx_info['status_time']);


        if(!empty($fx_info['good_name'])){
            $good_name = explode("(",$fx_info['good_name']);
            $fx_info['good_name'] =$good_name[0];
        }




        //门店量体
        $address = $this->_serve_mod->get("idserve='{$fx_info['serve_id']}'");
        if($address){
            $fx_info['store_name'] =$address['serve_name'];
            if($fx_info['sign']==2){
                $fx_info['address'] =$address['serve_address']."({$address['store_mobile']})";
            }
        }



        if($fx_info['liangti_id'] ){
            $liangti_info =$this->_member_mod->get("serve_type=2 and user_id={$fx_info['liangti_id']}");

            if($liangti_info){
                if($liangti_info['figure_type']==2){
                    //lts
                    $figure_liangti = $this->_figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                    $liangti_cart = $figure_liangti['card_number'];
                    $liangti_mob =$liangti_info['phone_mob'];
                    $liangti_name =$liangti_info['real_name'];
                }else{
                    //店长

                    $liangti_cart =$address['card_number'];
                    $liangti_mob =$address['mobile'];
                    $liangti_name = $address['linkman'];
                }
                if($liangti_name){
                    $fx_info['liangti_name']="{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";
                }else{
                    $fx_info['liangti_name']='';
                }

            }
        }




        if($fx_info['shipping_id']==1){
            //物流
            $fx_info['user_address'] =$fx_info['ship_area'].$fx_info['ship_addr'];

        }else{
            //门店
            $address = $this->_serve_mod->get("idserve='{$fx_info['ship_store']}'");
            if($address){
                $fx_info['ship_serve_name'] =$address['serve_name'];
                $fx_info['ship_addr'] =$address['serve_address']."({$address['store_mobile']})";
            }
            $fx_info['user_address'] =$fx_info['ship_serve_name'].'&nbsp;&nbsp;&nbsp;'.$fx_info['ship_addr'];

        }



        //取当前商品面料
        $goods_info = $this->_order_goods_mod->get("rec_id={$fx_info['rec_id']}");
        $fx_info['fabric'] =$goods_info['fabric']?$goods_info['fabric']:'暂无';
        //            !empty($fx_info[$k]['pl'])&&$fx_info[$k]['pl'] =$pl_info[$v['pl']]['cate_name'];



        if($fx_info['free']==1){
            //返修内容
            $has_fx = $this->_order_fx_mod->get("osi_id='{$id}'");
            $has_fx_info =!empty($has_fx)?json_decode($has_fx['content'],1):'';

            //获取键值  但是推送时候其实用不到
            $cloth_name =explode('(',$fx_info['good_name']);
            $cloth = array_search($cloth_name[0],$this->_pl_status);
            $cloth_java = $this->_pl_for_str["{$cloth}"];

            //联合查询  lt
//            $lt = $this->_size_part_mod->find(array(
//                'join'          => 'has_repair_part',//,has_special
//                'conditions'=>"size_part.cloth='{$cloth_java}'AND repair_part.cloth='{$cloth_java}'  and size_part.size_type='10053'",// 现在只对成衣进行返修    所以客服下单录信息时候要是成衣数据录入
//                'index_key'=>'',
//            ));

            // 刺绣信息  女装不支持
            $cx = $this->_cart_mod->_format_embs();
            $cx_info = $cx[$cloth];
            if($cx_info){
                $cx_info =array_values($cx_info);
            }

            //特殊处理字段  --字体和位置
            if($has_fx_info){
                foreach($has_fx_info as $k=>$v){
                    if($k=="cx"){
                        foreach($v as $kk=>$vv){
                            if($kk !="content"){
                                foreach($cx_info as $kkk=>$vvv){
                                    if($vvv['name']==$kk){
                                        if($kk=="颜色"){
                                            //存的id
                                            $has_fx_info[$k][$kk] =$this->color_status[$vvv['list'][$vv]['name']];
                                        }else{
                                            $has_fx_info[$k][$kk] = $vvv['list'][$vv]['name'];
                                        }
                                    }
                                }
                            }

                        }
                    }
                    if($k=="special"){

                        $special_info  =  array_values($v);
                        if($special_info){
                            $special_info_arr = $this->_special_solution_mod->find(array(
                                'conditions'=>db_create_in($special_info,'ecode'),
                                'fields'=>'solution_name,solution',
                                'index_key'=>'solution_name'
                            ));
                            $i=0;
                            $special =array();
                            foreach($special_info_arr as $k=>$v){
                                $i++;
                                $special["{$i}、".$v['solution_name']] =$v['solution'];
                            }
                            $has_fx_info['special'] = $special;
                        }
                    }
                }
            }

            $this->assign('has_fx_info',$has_fx_info);


            unset($this->_status_info[2]);
            unset($this->_status_info[3]);
            unset($this->_status_info[9]);
            unset($this->_status_info[5]);
            unset($this->_status_info[7]);
        }else{
            unset($this->_status_info[1]);
            unset($this->_status_info[2]);
            unset($this->_status_info[3]);
            unset($this->_status_info[4]);
            unset($this->_status_info[9]);
            unset($this->_status_info[10]);
        }




        //操作日志
        $log = $this->_order_serve_log_mod->find("osi_id={$id} and type=1");
        foreach($log as $k=>$v){
            $log[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
        }

        $this->assign('log',$log);

        if($fx_info['info_status']==6){
            $count_status_info=6;

            if($fx_info['free'] ==1){

                unset($this->_status_info[4]);
            }
            for($i=7;$i<=12;$i++){
                unset($this->_status_info[$i]);
            }
        }else{
            $count_status_info=12;
            unset($this->_status_info[6]);
        }

        $this->assign('count_status_info',$count_status_info);
        $this->assign('status_info',$this->_status_info);
        $this->assign('fx_info',$fx_info);
        $this->assign('fx_reason',$fx_reason);
        $this->display(TYPE.'fx_info.html');
    }

    /*
     * 返修修改页
     */
    function edit(){
        $id =$_REQUEST['id'];

        if(!$id){
            $this->show_warning('返修id不能为空');
            return;
        }

        //责任判定
        $zr_info = $this->_repair_responsible_mod->find(array(
            "conditions"=>"parent_id=0 and status=1",
            "index_key"=>"code"
        ));//传递code   code=>名字
        $zr = i_array_column($zr_info,'name_zh');


        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );

        if (!IS_POST) {



            //地区三级联动
            $region_mod = m('region');
            $list1 = $region_mod->get_options(2);
            $this->assign('region1',$list1);
            $list2 = $region_mod->get_options(246);
            $this->assign('region2',$list2);
            $other_address = $this->other_address();
            $this->assign('other_address',$other_address);


            $fx_info = $this->_order_serve_info_mod->get(array(
                'join'          => 'has_order_serve',//,has_special
                'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            ));

            if($fx_info['info_status'] == 0){
                $this->show_warning('用户未提交,不能编辑此页面提交返修方案');
                return;
            }
            $user_info =$this->_member_mod->get("user_id='{$fx_info['user_id']}'");
            $fx_info['nickname'] =$user_info['user_name'];

            if($fx_info['shipping_id']==2){
                //门店量体
                $address = $this->_serve_mod->get("idserve='{$fx_info['ship_store']}'");
                if($address){
                    $fx_info['ship_serve_name'] =$address['serve_name'];
                    $fx_info['ship_addr'] =$address['serve_address']."({$address['store_mobile']})";
                }
            }

//            shipping_id 1 物流2门店
            if($fx_info['ship_area_id'] && $fx_info['shipping_id']==1){
                $ship_area_id_arr =explode(',',$fx_info['ship_area_id']);
                $fx_info['g'] =$ship_area_id_arr[0];
                $fx_info['s'] =$ship_area_id_arr[1];
                $fx_info['city'] =$ship_area_id_arr[2];
            }

            //品类需要添加
            //        $pl_info = $this->_cart_mod->_customs(); //可以提出来

            !empty($fx_info['to_time']) && $fx_info['to_time'] =date('Y-m-d H:i:s',$fx_info['to_time']);
            !empty($fx_info['send_time']) && $fx_info['send_time'] =date('Y-m-d H:i:s',$fx_info['send_time']);

            //客户地址
            $order_info = $this->_order_mod->get("order_id={$fx_info['order_id']}");
            $fx_info['user_address'] =$order_info['ship_area'].$order_info['ship_addr'];

            //取当前商品面料
            //            $goods_info = $this->_order_goods_mod->get("rec_id={$fx_info['rec_id']}");
            //            $fx_info['fabric'] =$goods_info['fabric']?$goods_info['fabric']:'暂无';


            //所有量体师
            $lt_member_info = $this->_member_mod->find("serve_type=2 and state_info=1");
            $lt_members = i_array_column($lt_member_info,'real_name');

            //尺寸修改
            $good_name =$fx_info['good_name'];
            $good_name_arr = explode('(',$good_name);
            $cloth_name = $good_name_arr[0];

            $cloth = array_search($cloth_name,$this->_pl_status);
            $cloth_java = $this->_pl_for_str["{$cloth}"];


            $this->import_resource(array(
                'script' => 'jquery-1.7.2.min.js,jquery.plugins/jquery.validate.js,jquery.autocomplete.js,jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));



            $this->assign('zr',$zr);
            $this->assign('lt_members',$lt_members); //量体师
            $this->assign('fx_reason',$fx_reason);
            $this->assign('fx_info',$fx_info);
            $this->assign('cloth_java',$cloth_java);
            $this->assign('status_goods_info',$this->_status_info); //返修状态
            $this->assign('color_status',$this->color_status); //颜色配置
            $this->assign('status_info',$this->_status_info);
            $this->display(TYPE.'fx_edit.html');

            return;
        }



        $edit_os_arr = array();
        $lts = isset($_REQUEST['lts'])?$_REQUEST['lts']:''; //量体师 id
        $to_time = isset($_REQUEST['to_time'])?strtotime($_REQUEST['to_time']):'';
        $send_time = isset($_REQUEST['send_time'])?strtotime($_REQUEST['send_time']):'';
        $gy_method = isset($_REQUEST['gy_method'])?trim(htmlspecialchars($_REQUEST['gy_method'])):'';
        $price = isset($_REQUEST['price'])?trim($_REQUEST['price']):'';
        $reason = isset($_REQUEST['reason'])?$_REQUEST['reason']:'';
        $wl_sn = isset($_REQUEST['wl_sn'])?$_REQUEST['wl_sn']:'';
        $s_id =isset($_REQUEST['s_id'])?$_REQUEST['s_id']:'';
        $zipcode =$_REQUEST['zipcode']?$_REQUEST['zipcode']:'';
        $phone_mob =$_REQUEST['phone_mob']?$_REQUEST['phone_mob']:'';
        $user_name = isset($_REQUEST['user_name'])?$_REQUEST['user_name']:'';



        $old_info_status = isset($_REQUEST['old_info_status'])?$_REQUEST['old_info_status']:'';
        $old_lts = isset($_REQUEST['old_lts'])?$_REQUEST['old_lts']:''; //量体师 id
        $old_send_time = isset($_REQUEST['old_send_time'])?strtotime($_REQUEST['old_send_time']):'';
        $old_to_time = isset($_REQUEST['old_to_time'])?$_REQUEST['old_to_time']:'';
        $old_reason = isset($_REQUEST['old_reason'])?$_REQUEST['old_reason']:'';
        $old_wl_sn = isset($_REQUEST['old_wl_sn'])?$_REQUEST['old_wl_sn']:'';
        $old_user_name = isset($_REQUEST['old_user_name'])?$_REQUEST['old_user_name']:'';
        $oldzipcode =$_REQUEST['oldzipcode']?$_REQUEST['oldzipcode']:'';
        $oldphone_mob =$_REQUEST['oldphone_mob']?$_REQUEST['oldphone_mob']:'';
        $old_gy_method = isset($_REQUEST['old_gy_method '])?strtotime($_REQUEST['old_gy_method ']):'';
        $old_price = isset($_REQUEST['old_price'])?strtotime($_REQUEST['old_price']):'';
        $xx_old_address_user1 =$_REQUEST['xx_old_address_user1']?$_REQUEST['xx_old_address_user1']:''; //省市
        $xx_old_address_user2 =$_REQUEST['xx_old_address_user2']?$_REQUEST['xx_old_address_user2']:''; //原来的地址
        $old_sign_user =$_REQUEST['old_sign_user']?$_REQUEST['old_sign_user']:''; //原来的地址
        $sign_user =isset($_REQUEST['sign_user'])?intval($_REQUEST['sign_user']):'';//用户收货方式
        $cloth_java = $_REQUEST['cloth_java']?$_REQUEST['cloth_java']:''; //xcf这种


        if($reason ==''){
             $this->show_warning('请选择返修原因');
             return;
         }

        if($phone_mob ==''){
            $this->show_warning('收货人手机号不能为空');
            return;
        }

        if($zipcode ==''){
            $this->show_warning('收货人邮编不能为空');
            return;
        }

        if($user_name ==''){
            $this->show_warning('收货人姓名不能为空');
            return;
        }
        if($lts==''){
            $this->show_warning('提交方案请先指派量体师');
            return;
        }



        if(!$cloth_java){
            $this->show_warning("商品品类异常");
            return;
        }


        if(!$send_time){
            $this->show_warning("商品交期不能为空");
            return;
        }

        if(!$price){
            $this->show_warning("返修价格不能为空");
            return;
        }

        if(!$gy_method){
            $this->show_warning("返修工艺不能为空");
            return;
        }

        if($sign_user ==1){
            $region_mod = m('region');
            $p_region_id =$_REQUEST['p_region_id']?$_REQUEST['p_region_id']:'';
            $s_region_id_user =$_REQUEST['s_region_id_user']?$_REQUEST['s_region_id_user']:'';
            $region_area_arr =$region_mod->find(array(
                'conditions'=>db_create_in(array($p_region_id,$s_region_id_user),'region_id'),
                'index_key'=>'',
            ));

            $ship_area_id='2,'.$p_region_id.','.$s_region_id_user;
            $ship_area ='中国 '.$region_area_arr[0]['region_name'].' '.$region_area_arr[1]['region_name'] ;
            $ship_addr = $_REQUEST['xx_address_user']?$_REQUEST['xx_address_user']:'';

        }else{
            //截取门店名字和地址  门店id
            $new_address = $address =htmlspecialchars(trim($_REQUEST['address']));
            if(!$address) $old_address = $address =htmlspecialchars((trim($_REQUEST['old_address'])));
            $address_info =explode('】',$address);
            $ship_serve_name =trim($address_info[0],'【');
            $address_info_arr  =explode(',',$address_info[1]);
            $ship_addr =$address_info_arr[0];
            $address_user =$address_info_arr[1]; //store_id
        }


        $fx_info = $this->_order_serve_info_mod->get(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            'fields'=>"order_serve.user_id"
        ));


        if($old_info_status<=7){
            $edit_os_arr['gy_method']=$gy_method;
            $edit_os_arr['price']=$price;
        }



        $user_info = $this->_member_mod->get("user_id='{$lts}'");
        $edit_os_arr['ship_store']=$address_user;//用户
        $edit_os_arr['shipping_id']=$sign_user;
        $edit_os_arr['ship_serve_name']=$ship_serve_name;//用户收获门店
        $edit_os_arr['ship_area_id']=$ship_area_id;//用户收获地区id
        $edit_os_arr['ship_area']=$ship_area;//用户收获地区
        $edit_os_arr['ship_addr']=$ship_addr;//用户收获详细地址
        $edit_os_arr['zipcode']=$zipcode;

        $edit_os_arr['phone_mob']=$phone_mob;
        $edit_os_arr['user_name']=$user_name;
        $edit_os_arr['liangti_id']=$lts;
        $edit_os_arr['liangti_name']=$user_info['real_name'];
        $edit_os_arr['to_time']=$to_time;
        $edit_os_arr['send_time']=$send_time;
        $edit_os_arr["reason"]=$reason;
        $edit_os_arr["wl_sn"]=$wl_sn;
        $this->_order_serve_mod->edit($s_id,$edit_os_arr);


        //发送信息
        $msg = '尊敬的客户,系统已经成功受理您的返修申请,预计5个工作日完成返修,期间您可以在"用户中心-返修/退换货"查看进度。';
        if ($fx_info['user_id']){
            sendSystem($fx_info['user_id'], '17', '返修受理', $msg);
        }



        //日志
        $log_content = '';
        if($old_lts !=$lts)  $log_content .="把量体师\"{$this->_status_info[$old_lts]}\"改成了\"{$this->_status_info[$lts]}\"，";
        if(strtotime($old_to_time) !=$to_time) {
            $to_time = date("Y-m-d H:i:s",$to_time);
            $log_content .="客户预约到店时间\"{$old_to_time}\"改成了\"{$to_time}\"，";
        }
        if($old_reason !=$reason) $log_content .="返修原因\"{$fx_reason[$old_reason]}\"改成了\"{$fx_reason[$reason]}\"，";
        if($old_wl_sn !=$wl_sn) $log_content .="物流单号\"{$old_wl_sn}\"改成了\"{$wl_sn}\"，";
        if($oldzipcode !=$zipcode)$log_content .="用户收获邮编\"{$oldzipcode}\"改成了\"{$zipcode}\"，";
        if($phone_mob !=$oldphone_mob)$log_content .="用户手机号码\"{$oldphone_mob}\"改成了\"{$phone_mob}\"，";
        if($user_name !=$old_user_name)$log_content .="用户收货姓名\"{$old_user_name}\"改成了\"{$user_name}\"，";
        if($send_time !=$old_send_time)$log_content .="商品交期\"{$old_send_time}\"改成了\"{$send_time}\"，";
        if($old_sign_user !=$sign_user){
            $user_wl_arr =array(1=>'物流',2=>'门店自提');
            $log_content .="用户收货方式\"{$user_wl_arr[$old_sign_user]}\"改成了\"{$user_wl_arr[$sign_user]}\"，";
        }
        if(($ship_addr !=$xx_old_address_user2)||($ship_area !=$xx_old_address_user1))$log_content .="用户收货地址\"{$xx_old_address_user1}{$xx_old_address_user2}\"改成了\"{$ship_area}{$ship_addr}\"，";

        if($new_address){
            $log_content .="用户收货地址\"{$old_address}\"改成了\"{$new_address}\"，";
        }

        if($old_gy_method !=$gy_method){

            $log_content .="收费返修工艺方案\"{$old_gy_method}\"改成了\"{$gy_method}\"，";
        }
        if($price !=$old_price){
            $log_content .="收费返修工艺价格\"{$old_price}\"改成了\"{$price}\"，";
        }


        //记录操作日志
        $log_data = array();
        $log_data['content']     = $log_content;
        $log_data['old_status']  =$old_info_status;
        $log_data['admin']        = $_SESSION['admin_info']['user_name'];
        $log_data['type']         = 1;
        $log_data['osi_id']       = $id;
        $log_data['add_time']     = time();

        if($log_content){
            $this->_order_serve_log_mod->add($log_data);
        }


        $this->show_warning("编辑成功",
            '查看详情',    "index.php?app=fx&act=info&id={$id}",
            '返修列表页',    "index.php?app=fx&act=index"
        );

    }

    /*
      * 返修修改页
      */
    function edit_free(){

        $id =$_REQUEST['id'];

        if(!$id){
            $this->show_warning('返修id不能为空');
            return;
        }

        //责任判定
        $zr_info = $this->_repair_responsible_mod->find(array(
            "conditions"=>"parent_id=0 and status=1",
            "index_key"=>"code"
        ));//传递code   code=>名字
        $zr = i_array_column($zr_info,'name_zh');


        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );

        if (!IS_POST) {

            //改衣类型
            $_type=array(
                1=>'成品试衣',
                2=>'半成品试衣',
            );

            //地区三级联动
            $region_mod = m('region');
            $list1 = $region_mod->get_options(2);
            $this->assign('region1',$list1);
            $list2 = $region_mod->get_options(246);
            $this->assign('region2',$list2);
            $other_address = $this->other_address();
            $this->assign('other_address',$other_address);


            //已经提交过的返修信息
            $has_fx = $this->_order_fx_mod->get("osi_id='{$id}'");
            $has_fx_info =!empty($has_fx)?json_decode($has_fx['content'],1):'';

            $fx_info = $this->_order_serve_info_mod->get(array(
                'join'          => 'has_order_serve',//,has_special
                'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            ));


            if($fx_info['info_status'] == 0){
                $this->show_warning('用户未提交,不能编辑此页面提交返修方案');
                return;
            }
            $user_info =$this->_member_mod->get("user_id='{$fx_info['user_id']}'");
            $fx_info['nickname'] =$user_info['user_name'];

            if($fx_info['shipping_id']==2){
                //门店量体
                $address = $this->_serve_mod->get("idserve='{$fx_info['ship_store']}'");
                if($address){
                    $fx_info['ship_serve_name'] =$address['serve_name'];
                    $fx_info['ship_addr'] =$address['serve_address']."({$address['store_mobile']})";
                }
            }

            //            shipping_id 1 物流2门店
            if($fx_info['ship_area_id'] && $fx_info['shipping_id']==1){
                $ship_area_id_arr =explode(',',$fx_info['ship_area_id']);
                $fx_info['g'] =$ship_area_id_arr[0];
                $fx_info['s'] =$ship_area_id_arr[1];
                $fx_info['city'] =$ship_area_id_arr[2];
            }

            //品类需要添加
            //        $pl_info = $this->_cart_mod->_customs(); //可以提出来

            !empty($fx_info['to_time']) && $fx_info['to_time'] =date('Y-m-d H:i:s',$fx_info['to_time']);

            //客户地址
            $order_info = $this->_order_mod->get("order_id={$fx_info['order_id']}");
            $fx_info['user_address'] =$order_info['ship_area'].$order_info['ship_addr'];

            //取当前商品面料
            //            $goods_info = $this->_order_goods_mod->get("rec_id={$fx_info['rec_id']}");
            //            $fx_info['fabric'] =$goods_info['fabric']?$goods_info['fabric']:'暂无';


            //所有量体师
            $lt_member_info = $this->_member_mod->find("serve_type=2 and state_info=1");
            $lt_members = i_array_column($lt_member_info,'real_name');

            //尺寸修改
            $good_name =$fx_info['good_name'];
            $good_name_arr = explode('(',$good_name);
            $cloth_name = $good_name_arr[0];

            $cloth = array_search($cloth_name,$this->_pl_status);
            $cloth_java = $this->_pl_for_str["{$cloth}"];


            //联合查询  lt
            $lt = $this->_size_part_mod->find(array(
                'join'          => 'has_repair_part',//,has_special
                'conditions'=>"size_part.cloth='{$cloth_java}'AND repair_part.cloth='{$cloth_java}'  and size_part.size_type='10053'",// 现在只对成衣进行返修    所以客服下单录信息时候要是成衣数据录入
                'index_key'=>'',
                'count'=>true,
            ));
            $lt_count = $this->_size_part_mod->getcount();


            // 刺绣信息  男短裤什么的没有刺绣信息！！！我们这边订的
            $cx = $this->_cart_mod->_format_embs();
            if($cx[$cloth]){
                $cx_info =array_values($cx[$cloth]);
                $cx_count = count($cx_info);
            }


            //综合量体和刺绣统一处理下有返修提交的时候
            if($has_fx_info){
                //lt
                foreach($lt as $k=>$v){
                    if($has_fx_info['lt'][$v['partid']] !=''){
                        $lt[$k]['edit_size'] =$has_fx_info['lt'][$v['partid']]['size'];
                    }
                }

                //cx
                if($cx_info){
                    foreach($cx_info as $k=>$v){
                        if($has_fx_info['cx'][$v['name']] !=''){
                            //总大类汉字名字   颜色 对应选择 颜色id
                            $cx_info[$k]['fx_select'] =$has_fx_info['cx'][$v['name']];
                        }
                    }
                    $has_fx_content =explode(":",$has_fx_info['cx']['content']);
                    $this->assign("has_fx_content",$has_fx_content[1]);
                }

            }

            //特殊处理
            $special = $this->_special_question_mod->find(array(
                'join'          => 'has_solution',
                'conditions'=>"special_question.cloth='{$cloth_java}' and special_question.status=1 and special_solution.status=1 ",
                'index_key'=>'',
                'count'=>true,
            ));



            $solution_count = $this->_special_question_mod->getCount();
            $special_solution =array();

            foreach($special as $k=>$v){
                if($v['solution']){
                    $special_solution[$v['solution_id']]['solution_id'] =$v['solution_id']; //当前问题id不是solution id
                    $special_solution[$v['solution_id']]['solution_name'] =$v['solution_name'];
                    $special_solution[$v['solution_id']]['reason'] =$v['reason'];
                    $special_solution[$v['solution_id']]['img'] ="http://img.rcmtm.cn/repair/".$v['solution_id'].".JPG";
                    $special_solution[$v['solution_id']]['solution'][$v['ecode']] =$v['solution']; //ecode=>solution
                    if($has_fx_info && $has_fx_info['special'][$v['solution_id']]) $special_solution[$v['solution_id']]['fx_select'] =$has_fx_info['special'][$v['solution_id']];
                }
            }

            //图片  因为图片会有 没有的情况  所以 不能关联
            if($special_solution){
                $special_keys = array_keys($special_solution);
                $special_solution = array_values($special_solution);

                $attachment_info = $this->_repair_attachment_mod->find(array(
                    'conditions'=>db_create_in($special_keys,'host_id')." and status=2",
                    'fields'=>'attach_path,host_id',
                    'index_key'=>'host_id'
                ));
                $attachment_img =i_array_column($attachment_info,'attach_path');
            }

            $this->import_resource(array(
                'script' => 'jquery-1.7.2.min.js,jquery.plugins/jquery.validate.js,jquery.autocomplete.js,jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));


            $this->assign('type',$_type);
            $this->assign('zr',$zr);
            $this->assign('lt_members',$lt_members); //量体师
            $this->assign('fx_reason',$fx_reason);
            $this->assign('fx_info',$fx_info);

            $this->assign('cloth_java',$cloth_java);
            $this->assign('lt',$lt);
            $this->assign('lt_count',$lt_count);
            $this->assign('special_solution',$special_solution);
            $this->assign('solution_count',$solution_count);
            $this->assign('attachment_img',$attachment_img);
            $this->assign('cx',$cx_info); //刺绣相关 0索引
            $this->assign('cx_count',$cx_count); //刺绣相关 0索引

            $this->assign('status_goods_info',$this->_status_goods_info); //返修状态
            $this->assign('color_status',$this->color_status); //颜色配置
            $this->assign('status_info',$this->_status_info);
            $this->display(TYPE.'fx_free_edit.html');

            return;
        }




        $info_status = 4;
        $lts = isset($_REQUEST['lts'])?$_REQUEST['lts']:''; //量体师 id
        $to_time = isset($_REQUEST['to_time'])?strtotime($_REQUEST['to_time']):'';
        $reason = isset($_REQUEST['reason'])?$_REQUEST['reason']:'';
        $wl_sn = isset($_REQUEST['wl_sn'])?$_REQUEST['wl_sn']:'';
        $s_id =isset($_REQUEST['s_id'])?$_REQUEST['s_id']:'';
        $zipcode =$_REQUEST['zipcode']?$_REQUEST['zipcode']:'';
        $phone_mob =$_REQUEST['phone_mob']?$_REQUEST['phone_mob']:'';
        $user_name = isset($_REQUEST['user_name'])?$_REQUEST['user_name']:'';
        $zr_status     = isset($_REQUEST['zr_status'])?$_REQUEST['zr_status']:'';
        $sign_user =isset($_REQUEST['sign_user'])?intval($_REQUEST['sign_user']):'';//用户收货方式
        $cloth_java = $_REQUEST['cloth_java'];  //xcf这种


        $xx_old_address_user1 =$_REQUEST['xx_old_address_user1']?$_REQUEST['xx_old_address_user1']:''; //省市
        $xx_old_address_user2 =$_REQUEST['xx_old_address_user2']?$_REQUEST['xx_old_address_user2']:''; //原来的地址
        $old_sign_user =$_REQUEST['old_sign_user']?$_REQUEST['old_sign_user']:''; //原来的地址
        $old_zr_status = isset($_REQUEST['old_zr_status'])?$_REQUEST['old_zr_status']:'';

        $old_info_status = isset($_REQUEST['old_info_status'])?$_REQUEST['old_info_status']:'';
        $old_lts = isset($_REQUEST['old_lts'])?$_REQUEST['old_lts']:''; //量体师 id
        $old_to_time = isset($_REQUEST['old_to_time'])?$_REQUEST['old_to_time']:'';
        $old_reason = isset($_REQUEST['old_reason'])?$_REQUEST['old_reason']:'';
        $old_wl_sn = isset($_REQUEST['old_wl_sn'])?$_REQUEST['old_wl_sn']:'';
        $old_user_name = isset($_REQUEST['old_user_name'])?$_REQUEST['old_user_name']:'';

        $oldzipcode =$_REQUEST['oldzipcode']?$_REQUEST['oldzipcode']:'';
        $oldphone_mob =$_REQUEST['oldphone_mob']?$_REQUEST['oldphone_mob']:'';


        $log_content = '';
        $edit_osi_arr=$edit_os_arr = array();

        $fx_info = $this->_order_serve_info_mod->get(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            'fields'=>"order_serve.user_id,free,order_serve_info.rcmtm_id,order_serve.user_name,order_serve.liangti_id,order_serve.order_id,order_serve_info.rec_id,min(order_serve_info.info_status) as min"
        ));

        if($fx_info['free']===1){
            $this->show_warning("此返修订单为收费返修");
            return;
        }

        if($reason ==''){
            $this->show_warning('请选择返修原因');
            return;
        }

        if($phone_mob ==''){
            $this->show_warning('收货人手机号不能为空');
            return;
        }

        if($zipcode ==''){
            $this->show_warning('收货人邮编不能为空');
            return;
        }

        if($user_name ==''){
            $this->show_warning('收货人姓名不能为空');
            return;
        }

        if($lts==''){
            $this->show_warning('提交方案请先指派量体师');
            return;
        }


        if(!$zr_status){
            $this->show_warning("请选择责任判定");
            return;
        }

        if(!$cloth_java){
            $this->show_warning("商品品类异常");
            return;
        }

        if($old_info_status>=4){
            $this->show_warning("已经提交审核,不能重复提交");
            return;
        }

        if($sign_user ==1){
            $region_mod = m('region');
            $p_region_id =$_REQUEST['p_region_id']?$_REQUEST['p_region_id']:'';
            $s_region_id_user =$_REQUEST['s_region_id_user']?$_REQUEST['s_region_id_user']:'';
            $region_area_arr =$region_mod->find(array(
                'conditions'=>db_create_in(array($p_region_id,$s_region_id_user),'region_id'),
                'index_key'=>'',
            ));

            $ship_area_id='2,'.$p_region_id.','.$s_region_id_user;
            $ship_area ='中国 '.$region_area_arr[0]['region_name'].' '.$region_area_arr[1]['region_name'] ;
            $ship_addr = $_REQUEST['xx_address_user']?$_REQUEST['xx_address_user']:'';

        }else{
            //截取门店名字和地址  门店id
            $new_address = $address =htmlspecialchars(trim($_REQUEST['address']));
            if(!$address) $old_address = $address =htmlspecialchars((trim($_REQUEST['old_address'])));
            $address_info =explode('】',$address);
            $ship_serve_name =trim($address_info[0],'【');
            $address_info_arr  =explode(',',$address_info[1]);
            $ship_addr =$address_info_arr[0];
            $address_user =$address_info_arr[1]; //store_id
        }


        //量体
        $size_count = isset($_REQUEST['size_count'])?$_REQUEST['size_count']:0; //索引0开始
        $fx = array();
        $fx_lt_str =$add_cx =$fx_cx_str =$fx_special_str ='';
        if($size_count){
            for($i=0;$i<$size_count;$i++){
                if(isset($_REQUEST['size_'.$i]) && $_REQUEST['size_'.$i]&& $_REQUEST['size_'.$i]!='0.0'){
                    $fx['lt'][$_REQUEST['size_partid_'.$i]]['size'] = $_REQUEST['size_'.$i];
                    $fx['lt'][$_REQUEST['size_partid_'.$i]]['part_name'] = isset($_REQUEST['size_part_name_'.$i])?$_REQUEST['size_part_name_'.$i]:'';
                    $fx_lt_str .=$_REQUEST['size_partid_'.$i].':'.$_REQUEST['size_'.$i].',';
                }
            }
        }



        //刺绣
        $cx_count = isset($_REQUEST['cx_count'])?$_REQUEST['cx_count']:0;
        $fx_location = $fx_font =$fx_colot='';
        if($cx_count){
            for($i=0;$i<$cx_count;$i++){
                if($i==2)continue;
                $fx['cx'][$_REQUEST['cx_name_'.$i]] = $_REQUEST['cx_'.$i];  //大类汉字对应=》选择id

                if($_REQUEST['cx_name_'.$i] == "位置") $fx_location = $_REQUEST['cx_'.$i];
                if($_REQUEST['cx_name_'.$i] == "字体") $fx_font = $_REQUEST['cx_'.$i];
                if($_REQUEST['cx_name_'.$i] == "颜色") $fx_color =$_REQUEST['cx_'.$i];
            }
            $fx['cx']['content'] = $_REQUEST['cx_content']?$_REQUEST['cx_content_id'].':'.$_REQUEST['cx_content']:'';
        }




        //特殊处理
        $solution_count = isset($_REQUEST['special_count'])?$_REQUEST['special_count']:0;
        if($solution_count){
            for($i=0;$i<$solution_count;$i++){
                if(isset($_REQUEST['special_radio_'.$i])){
                    $fx['special'][$_REQUEST['special_id_'.$i]] = $_REQUEST['special_radio_'.$i]; //当前问题id=》当解决方法  的要推送的code
                    $fx_special_str .= $_REQUEST['special_id_'.$i].'_'.$_REQUEST['special_radio_'.$i].',';
                }
            }
        }








        $cloth =array_search($cloth_java,$this->_pl_for_str); //取类似003的cloth
        if($fx && $id){
            $fx['cloth'] =$cloth;
            if($this->_order_fx_mod->get("osi_id='{$id}'")){
                $this->_order_fx_mod->edit("osi_id='{$id}'",array('osi_id'=>$id,'content'=>addslashes(json_encode($fx))));
            }else{
                $this->_order_fx_mod->add(array(
                    'order_id'=>$fx_info['order_id'],
                    'osi_id'=>$id,
                    'content'=>addslashes(json_encode($fx)),
                    'add_time'=>time(),
                ));
            }

        }


//        if($old_info_status<4&&$fx_info['free']==1){
            //刺绣方案需要转换成dict1的数据！！！！！
            $rcmtm_id =$fx_info['rcmtm_id'];
            $user_name =$fx_info['user_name'];

            //量体人员 证件号
            $user_info = $this->_member_mod->get("user_id='{$lts}'");
            //量体师
            if($user_info['figure_type']==2){

                $liangti_info = $this->_figure_liangti_mod->get("liangti_id='{$lts}'");
            }

            // 店员
            if($user_info['figure_type']==1){
                $liangti_info =$this->_serve_mod->get("userid='{$lts}'");
            }

            $cart_number =$liangti_info['card_number'];

            //            $order_info = $this->_order_mod->get("order_id='{$fx_info['order_id']}'");
            //            $phone_mob = empty($order_info['ship_mobile'])  ? $order_info['ship_tel'] : $order_info['ship_mobile'];

            //原订单
            $gy_info = $this->_rcmtmlog_mod->get("order_id='{$fx_info['order_id']}' and  rec_id='{$fx_info['rec_id']}' and type=0");


            $data = (array)simplexml_load_string($gy_info['content']);  //原订单有乱码问题
            $OrdersProcess = $data['OrderDetails']->OrderDetail->OrdersProcess;//工艺
            $OrdersProcess_arr = explode(',',$OrdersProcess);


            $rec_id =$_REQUEST['rec_id'];
            $order_goods_info = $this->_order_goods_mod->get("rec_id='{$rec_id}'");

            //diy--- 这里木用 diy返修
            if($order_goods_info['goods_id'] == 0){
                $dicttoxml_mod =  &m("dicttoxml{$cloth}");
                $dict_ecode = $dicttoxml_mod->find(db_create_in($OrdersProcess_arr,'id'));
                $order_ecodes = i_array_column($dict_ecode,'bleode');
            }else{
                //商品的 dict1表取
                $dict_mod = &m('dict1');
                $dict_ecode = $dict_mod->find(db_create_in($OrdersProcess_arr,'id'));
                $order_ecodes = i_array_column($dict_ecode,'ECODE');
            }
            $order_ecodes_str =$cloth_java.':'.implode(',',$order_ecodes);


            //原订单刺绣
            $Location    = (string)$data['OrderDetails']->OrderDetail->EmbroideryProcess->Embroidery->Location;
            $font        = (string)$data['OrderDetails']->OrderDetail->EmbroideryProcess->Embroidery->Font;
            $color       = (string)$data['OrderDetails']->OrderDetail->EmbroideryProcess->Embroidery->color;
            $content     = (string)$data['OrderDetails']->OrderDetail->EmbroideryProcess->Embroidery->Content;
            $Size        = (string)$data['OrderDetails']->OrderDetail->EmbroideryProcess->Embroidery->Size;
            //品类 内容 位置 字体颜色   衬衣大小
            $cx_str='';
            if($Location || $font||$color||$content){
                $cx_str .= $cloth_java.','.$content.','.$Location.'_'.$font.'_'.$color;
            }
            $Size && $cx_str.= '_'.$Size;

            //返修 刺绣信息  字体 颜色 位置 衬衣。。
            if($fx['cx']['content'] || $fx_location || $fx_font || $fx_color){
                $fx_cx_str = $cloth_java.','.$fx['cx']['content'].','.$fx_location.'_'.$fx_font.'_'.$fx_color;
                $Size && $fx_cx_str.= '_'.$Size;
                if(!$cx_str){
                    $add_cx = $fx_cx_str;
                    $fx_cx_str='';
                }
            }


            if(!$fx_lt_str && !$fx_cx_str && !$fx_special_str){
                $this->show_warning("至少编辑一类返修项");
                return;
            }


            $url ="http://api.rcmtm.com:7070/order-api/resources/repairOrderService/importRepairOrder";   //线上
            //$url ="http://172.16.16.166:8081/order-api/resources/repairOrderService/importRepairOrder";             //线下
            $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<repairOrder><!-- 返修单主信息  -->
    <userName>QC2C</userName><!-- rcmtm订单号  -->
	<orderNo>{$rcmtm_id}</orderNo><!-- rcmtm订单号  -->
	<cloth>{$cloth_java}</cloth><!--  原订单服装分类BL编码 -->
	<modifyType>2</modifyType><!--  改衣类型 -->
	<bussinesType>1</bussinesType><!-- 订单类型  -->
	<customerName>{$user_name}</customerName><!-- 客户姓名  -->
	<ltname>{$cart_number}</ltname><!-- 量体资格证编号  -->
	<tel>{$phone_mob}</tel><!--  客户电话 -->
	<price>0</price><!-- 总返修价格  -->
	<orderCraft>{$order_ecodes_str}</orderCraft><!--  原订单工艺信息 -->  id转编码 dict1->ecode
	<orderEmb>{$cx_str}</orderEmb><!-- 原订单刺绣信息  --><!-- 衬衣刺绣  MCY,3676:LI PENG ,3247_3625_3633_3260,;-->  内容id:内容 位置 字体 颜色（id）   衬衣大小
<save>0</save><!-- 保存：1 提交：0  -->
	<repairDetail><!--  返修单明细信息 -->
		<cloth>{$cloth_java}</cloth><!-- 单件服装分类BL编码  -->
		<partInfo>{$fx_lt_str}</partInfo><!-- 返修尺寸信息  -->
		<craftInfo></craftInfo><!-- 返修原订单工艺信息  -->   空
		<addCraft></addCraft><!--  新增工艺信息 -->
		<addCraftText></addCraftText><!-- 新增指定工艺信息  -->
		<embInfo>{$fx_cx_str}</embInfo><!-- 返修原订单刺绣信息  --><!-- 衬衣刺绣  MCY,3676:绣花用字,3249_7153_3645_3265,;-->

		<addEmbInfo>{$add_cx}</addEmbInfo><!-- 新增刺绣信息  -->
		<questionSolution>{$fx_special_str}</questionSolution><!-- 问题ID_解决方案ecode_价格,多个以逗号隔开  -->
		<responsibility>{$zr_status}</responsibility><!-- 一级责任判定信息  -->    code
		<noSize></noSize><!-- 无尺寸返修(10050)  -->
		<formula>0</formula><!-- 扣费明细  -->   100->0
		<price>0</price><!--  返修单价 -->  0
	</repairDetail>
</repairOrder>
EOT;

            $content = addslashes($xml);
            $this->_rcmtmlog_mod->add(array("order_id"=>$fx_info['order_id'],"rec_id"=>$rec_id,"content"=>$content,'type'=>1));

            $respon_data = $this->curl_post_ssl($url, $xml);

            //成功：{"code":"101","rs":"返修单号"} 失败：{"code":"102","rs":"错误信息"}
            //echo json_encode($respon_data);
            if($respon_data['err']==0){
                if($respon_data['data']['code'] == 102){
                    $edit_osi_arr['fail_reason'] = $respon_data['data']['rs'];//['$'];
                    $edit_osi_arr['info_status'] = 2; //提交返修方案失败
                }
                //成功
                if($respon_data['data']['code'] == 101){
                    $edit_osi_arr['info_status']=4;
                    $edit_osi_arr['fx_sn'] = $respon_data['data']['rs'];
                    $log_data['info_status'] = 4; //审核中

                    //发送信息
                    $msg = '尊敬的客户,系统已经成功受理您的返修申请,预计5个工作日完成返修,期间您可以在"用户中心-返修/退换货"查看进度。';

                    if ($fx_info['user_id']){
                        sendSystem($fx_info['user_id'], '17', '返修受理', $msg);
                    }


                }
            }elseif($respon_data['err']==1){
                $edit_osi_arr['info_status'] =2;
                $edit_osi_arr['fail_reason'] = addslashes($respon_data['msg']);
            }


        $edit_osi_arr['zr'] =$zr_status;
        $ret = $this->_order_serve_info_mod->edit($id,$edit_osi_arr);
        if(!$ret ){
            $this->show_warning('系统异常');
            return;
        }

        $new_fx_info = $this->_order_serve_info_mod->get(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>"order_serve.type=1 and order_serve_info.id={$id} ",
            'fields'=>"min(order_serve_info.info_status) as min"
        ));

        //编辑返修商品表
        $edit_os_arr['ship_store']=$address_user;//用户收获门店
        $edit_os_arr['shipping_id']=$sign_user;//用户收获门
        $edit_os_arr['ship_serve_name']=$ship_serve_name;//用户收获门店
        $edit_os_arr['ship_area_id']=$ship_area_id;//用户收获地区id
        $edit_os_arr['ship_area']=$ship_area;//用户收获地区
        $edit_os_arr['ship_addr']=$ship_addr;//用户收获详细地址
        $edit_os_arr['zipcode']=$zipcode;//用户收获门店

        $edit_os_arr['phone_mob']=$phone_mob;
        $edit_os_arr['user_name']=$user_name;
        $edit_os_arr['liangti_id']=$lts;
        $edit_os_arr['liangti_name']=$user_info['real_name'];
        $edit_os_arr['to_time']=$to_time;
        $edit_os_arr["reason"]=$reason;
        $edit_os_arr["wl_sn"]=$wl_sn;
        $edit_os_arr['status'] =$new_fx_info['min'];
        $this->_order_serve_mod->edit($s_id,$edit_os_arr);



        if($old_lts !=$lts)  $log_content .="把量体师\"{$this->_status_goods_info[$old_lts]}\"改成了\"{$this->_status_goods_info[$lts]}\"，";
        if(strtotime($old_to_time) !=$to_time) {
            $to_time = date("Y-m=-d H:i:s",$to_time);
            $log_content .="客户预约到店时间\"{$old_to_time}\"改成了\"{$to_time}\"，";
        }
        if($old_reason !=$reason) $log_content .="返修原因\"{$fx_reason[$old_reason]}\"改成了\"{$fx_reason[$reason]}\"，";
        if($old_wl_sn !=$wl_sn) $log_content .="物流单号\"{$old_wl_sn}\"改成了\"{$wl_sn}\"，";
        if($old_zr_status !=$zr_status) $log_content .="责任判定\"{$old_zr_status}\"改成了\"{$zr[$zr_status]}\"，";
        if($oldzipcode !=$zipcode)$log_content .="用户收获邮编\"{$oldzipcode}\"改成了\"{$zipcode}\"，";
        if($phone_mob !=$oldphone_mob)$log_content .="用户手机号码\"{$oldphone_mob}\"改成了\"{$phone_mob}\"，";
        if($user_name !=$old_user_name)$log_content .="用户收货姓名\"{$old_user_name}\"改成了\"{$user_name}\"，";
        if($old_sign_user !=$sign_user){
            $user_wl_arr =array(1=>'物流',2=>'门店自提');
            $log_content .="用户收货方式\"{$user_wl_arr[$old_sign_user]}\"改成了\"{$user_wl_arr[$sign_user]}\"，";
        }
        if(($ship_addr !=$xx_old_address_user2)||($ship_area !=$xx_old_address_user1))$log_content .="用户收货地址\"{$xx_old_address_user1}{$xx_old_address_user2}\"改成了\"{$ship_area}{$ship_addr}\"，";

        if($new_address){
            $log_content .="用户收货地址\"{$old_address}\"改成了\"{$new_address}\"，";
        }

        //记录操作日志
        $log_data = array();
        $log_data['content']     = $log_content;
        $log_data['old_status']  =$old_info_status;
        $log_data['info_status'] = 4;
        $log_data['admin']        = $_SESSION['admin_info']['user_name'];
        $log_data['type']         = 1;
        $log_data['osi_id']       = $id;
        $log_data['add_time']     = time();

        if($log_content){
            $this->_order_serve_log_mod->add($log_data);
        }

        $this->show_warning("编辑成功",
            '查看详情',    "index.php?app=fx&act=info&id={$id}",
            '返修列表页',    "index.php?app=fx&act=index"
        );

    }


    function edit_sql(){
        return;
        $id  =$_REQUEST['id'];
        $status =$_REQUEST['status']?$_REQUEST['status']:1;;
        $ret=$this->_order_serve_info_mod->edit($id,array('info_status'=>$status));
        if(!$ret){
            echo 'no';
        }
        echo 'yes';
    }


    /*
     * 返修列表页面
     */
    function index(){
        //add_time   status  sign  order_sn   客户名称    门店名称
        $query_fields = array(1=>'订单号',2=>'客户名称',3=>'门店名称');
        $quety_str_arr = array(1=>'order_sn',2=>'user_name',3=>'store_name');


        $conditions = "order_serve.type=1".$this->qx_conditions;
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'order_serve.sign',
                'name' =>  'sign',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'order_serve.status',
                'name' =>  'status',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'order_serve.sign',
                'name' =>  'sign',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'order_serve.add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'order_serve.add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            )

        ));


        $field_name = isset($_REQUEST['field_name']) ? trim($_REQUEST['field_name']) : '';
        $field_value = isset($_REQUEST['field_value']) ? trim($_REQUEST['field_value']) : '';

        if($field_name &&$field_value){

            $val = 'order_serve.'.$quety_str_arr[$field_name];
            $conditions .= " AND {$val}='{$field_value}'";
        }


        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'order_serve.id';
                $order = 'desc';
            }
        } else {
            $sort = 'order_serve.id';
            $order = 'desc';
        }


        $page = $this->_get_page(30);
        $fx_info = $this->_order_serve_info_mod->find(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>$conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",

        ));


        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );


        $arr =array();
        foreach($fx_info as $k=>$v){
            $count = count($arr[$v['s_id']]['serve_info']);
            $arr[$v['s_id']]['serve_info'][$k]['num'] = $count;
            $arr[$v['s_id']]['serve_info'][$k]['fx_sn'] = $v['fx_sn'];
            $arr[$v['s_id']]['serve_info'][$k]['r_sn'] = $v['r_sn'];
            $arr[$v['s_id']]['serve_info'][$k]['good_name'] = $v['good_name'];
            $arr[$v['s_id']]['serve_info'][$k]['info_status'] = $v['info_status'];
            $arr[$v['s_id']]['serve_info'][$k]['zr'] = $v['zr'];
            $arr[$v['s_id']]['serve_info'][$k]['price'] = $v['price'];
            $arr[$v['s_id']]['serve_info'][$k]['rcmtm_id'] = $v['rcmtm_id'];
            $good_name = explode("(",$v['good_name']);
            $arr[$v['s_id']]['serve_info'][$k]['good_name'] = $good_name[0];
            $arr[$v['s_id']]['serve_info'][$k]['fail_reason'] = $v['fail_reason'];
            $arr[$v['s_id']]['serve_info_count'] = count($arr[$v['s_id']]['serve_info']);
            $arr[$v['s_id']]['reason'] = $fx_reason[$v['reason']];
            $arr[$v['s_id']]['user_name'] = $v['user_name'];

            $arr[$v['s_id']]['sign'] = $v['sign'];
            $arr[$v['s_id']]['store_name'] = $v['store_name'];
            $arr[$v['s_id']]['order_sn'] = $v['order_sn'];
            $arr[$v['s_id']]['add_time'] = $v['add_time'] !=''?date('Y-m-d H:i:s',$v['add_time']):'';
            $arr[$v['s_id']]['send_time']= $v['send_time'] !=''?date('Y-m-d',$v['send_time']):'';
            $arr[$v['s_id']]['free']= $v['free'] ;
        }

        $page['item_count'] = $this->_order_serve_info_mod->getCount();
        $this->_format_page($page);




        $this->assign('page_info', $page);
        $this->assign('quety_str_arr', $quety_str_arr);
        $this->assign('query_fields',$query_fields);
        $this->assign('sign_info',$this->_sign_info);
        $this->assign('status_info',$this->_status_info); //专门给单品的状态
        $this->assign('fx_info',$arr);
        //        $this->assign('pl_info',$this->_cart_mod->_customs());



        $this->import_resource(array(
            'script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));

        $this->display(TYPE.'fx_index.html');

    }



    function good_address(){
        $good_info =$_REQUEST['good_info'];
        $good_info =explode(',',$good_info);
        $rec_id =$good_info[1];

        $order_goods_info =$this->_order_goods_mod->get("rec_id='{$rec_id}'");

        $son_sn =$order_goods_info['son_sn'];

        if(!$son_sn){
            $this->json_error('son_sn error'); //数据异常怎么处理
            return;
        }

        $order_figure_info = $this->_ordergigure_mod->get("son_sn='{$son_sn}'");
        if(!$order_figure_info){
            $this->json_error('order_figure  is null');
            return;
        }
        $xianxia_address = $this->_get_address($order_goods_info);
        if(!$xianxia_address){
            $this->json_error('address is null'); //数据异常怎么处理
            return;
        }

        $this->json_result($xianxia_address);
    }
    /*
     * 返修
     * yusw
     *
     */
    function fx()
    {
        //可以选多个单品
        $order_sn = $_REQUEST['order_sn'];
        $order_id = $_REQUEST['order_id'];

        if(!$order_sn){
            $this->show_warning('订单号不能为空');
            return;
        }

        if(!$order_id){
            $this->show_warning('订单id不能为空');
            return;
        }

        if(!IS_POST){
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));


            //获取门店相关信息   先取一个
            $figureinfo = $this->_orderfigure_mod->get("order_id='{$order_id}'");
//            $server_id = $figureinfo['server_id'];
            $xianxia_address = $this->_get_address($figureinfo);  //门店信息
            $address = array('xs'=>'山东省青岛市即墨珠江二路277号物流中心9号门  江文洁 0532-88598236','xx'=>$xianxia_address);
            //其他地区
            $other_address = $this->other_address();


            //pl+编码//diy=>无goods_id->0 , 所以打算用rec_id=>商品名字
            $pl_bm = array(); //rec_id=>array('cate_name',"fabric")
            $order_goods= $this->_order_goods_mod->find("order_id='{$order_id}'");// and size='diy'不排斥标准码
            //            $pl_sign_info =$this->_cart_mod->_customs();
            foreach($order_goods as $k=>$v){
                //                $pl_bm[$k]['cate_name'] =$pl_sign_info[$v['cloth']]['cate_name']; //品类现在 有写数组 可以不这么调用
                $pl_bm[$k]['cate_name'] =$this->_pl_status[$v['cloth']];

                $pl_bm[$k]['fabric'] =$v['fabric'];
                $pl_bm[$k]['rcmtm_id'] =$v['rcmtm_id'];
            }


            $this->assign('pl',$pl_bm); //pl key=>val

            $this->assign('order_goods',$order_goods);
            $this->assign('address',$address);
            $this->assign('order_sn',$order_sn);
            $this->assign('order_id',$order_id);
            $this->assign('other_address',$other_address);
            $this->assign('xx_ys_address',$xianxia_address);
            $this->assign('sign_info',$this->_sign_info);
            $this->display(TYPE.'fx_add.html');
            return;
        }




        $free  = isset($_REQUEST['free'])?$_REQUEST['free']:1;
        $gy_method  = isset($_REQUEST['gy_method'])?htmlspecialchars($_REQUEST['gy_method']):'';
        $price  = $_REQUEST['price']?$_REQUEST['price']:0;

        $liangti_name  = isset($_REQUEST['liangti_name'])?$_REQUEST['liangti_name']:'';
        $liangti_id  = isset($_REQUEST['liangti_id'])?$_REQUEST['liangti_id']:'';


        $rec_info =$_REQUEST['rec_id']; //多个
        $sign =isset($_REQUEST['sign'])?intval($_REQUEST['sign']):'';//承接方式  2线下门店  1线上客服

        $tz = 1; //线上 短信通知

        if($sign ==1){
            $address =htmlspecialchars(trim($_REQUEST['xx_address']));
        }else{
            //截取门店名字和地址
            $address =htmlspecialchars(trim($_REQUEST['address']));
            $address_info =explode('】',$address);
            $store_name =trim($address_info[0],'【');
            $address_info_arr  =explode(',',$address_info[1]);
            $address =$address_info_arr[0];
            $store_id =$address_info_arr[1];


        }


        if($free ===0&&$gy_method==''){
            $this->show_warning('请填写工艺返修方案');
            return;
        }

        if($rec_info == ''){
            $this->show_warning('请选择返修单品');
            return;
        }


        if($sign == ''){
            $this->show_warning('请选择承接方式');
            return;
        }



        if($address == ''){
            $this->show_warning('接受返修地址不能为空');
            return;
        }


        //当前订单提交 过一次返修
        $order_serve = $this->_order_serve_mod->get("order_id='{$order_id}'");
        if(!empty($order_serve)){
            $this->show_warning('当前订单已经提交过一次返修');
            return;
        }

        $rcmtm_id = $rec_ids = $good_names =array();
        foreach($rec_info as $k=>$v){
            $rec_arr = explode(',',$v);
            $good_names[]=$rec_arr[0];
            $rec_ids[]=$rec_arr[1];
            $rcmtm_id[]=$rec_arr[2];

        }

        $rec_id_str = empty($rec_ids)?'':implode(',',$rec_ids);
        $goods_name_str = empty($good_names)?'':implode(',',$good_names);

        //获取用户名和id
        $order_info = $this->_order_mod->get("order_id='{$order_id}'");

        if($order_info){
            $user_id = $order_info['user_id'];
            $user_name = $order_info['ship_name'];
            $zipcode =$order_info['ship_zip'];
            $shipping_id = $order_info['shipping_id'];
            $ship_area_id =$order_info['ship_area_id'];
            $ship_area =$order_info['ship_area'];
            $ship_addr =$order_info['ship_addr'];
            $phone_mob = empty($order_info['ship_mobile'])  ? $order_info['ship_tel'] : $order_info['ship_mobile'];



            if($user_id){
                //发送短信
//                if($tz==1){
                    if($free==1){
                        $msg ="免费的返修提示：您的返修反馈已受理，请打开麦富迪APP，到“我的-订单管理”提交返修申请.";
                    }
                    if($free==0){
                        $msg ="收费的返修提示：您返修的“西服，西裤”已生成返修费用，请打开麦富迪APP，到“我的-订单管理“进行支付。";
                    }

                    SendSms1($phone_mob, $msg);
//                }
            }


            //门店
            if($shipping_id ==2){
                $ship_store_id = $order_info['ship_store'];
                //门店
                $serve_info = $this->_serve_mod->get("idserve='{$order_info['ship_store']}'");
                $user_name= $order_info['ship_name'];
                $ship_serve_name =$serve_info['serve_name'];
                $ship_addr =$serve_info['region_name'].''.$serve_info['serve_address'];
                $zipcode = $serve_info['post_code'];
                $phone_mob = $serve_info['mobile'];
                if (empty($user_name)){
                    $user_name = $serve_info['linkman'];
                }

            }
        }



        //3个品类
        $data = array(
            'sign'=>$sign,
            'rec_id'=>$rec_id_str, //要不要都行
            'goods_name'=>$goods_name_str, //要不要都行
            'order_id'=>$order_id,
            'order_sn'=>$order_sn,
            'serve_id'=>$store_id,
            'address'=>$address,
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'phone_mob'=>$phone_mob,
            'price'=>$price,
            'gy_method'=>trim($gy_method),
            'free'=>$free,
            'ship_store'=>$ship_store_id,//用户收获门店    id
            'shipping_id'=>$shipping_id,//1物流2门店
            'ship_serve_name'=>$ship_serve_name,
            'ship_area_id'=>$ship_area_id,
            'ship_area'=>$ship_area,
            'ship_addr'=>$ship_addr,

            'zipcode'=>$zipcode,
            'liangti_id'=>$liangti_id,
            'liangti_name'=>$liangti_name,
            'tz'=>$tz,
            'store_name'=>$store_name,
            'order_sn'=>$order_sn,
            'type'=>1, //返修
            'add_time'=>time(),
        );


          $s_id = $this->_order_serve_mod->add($data);
        if(!$s_id){
            $this->show_warning('系统异常，请重新操作');
            return;
        }


        $data_all =array();
        foreach($rec_ids as $k=>$v){
            $data = array(
                's_id'=>$s_id,
                'order_sn'=>$order_sn,
                'order_id'=>$order_id,
                'rec_id'=>$v,
                'rcmtm_id'=>$rcmtm_id[$k],
                'good_name'=>$good_names[$k],
                //                'info_status'=>0,//用户未提交 默认的
                'fx_sn' =>$free==1?"":"FXSF".date("ym",time()).createNonceNum(4),
                'sign'=>$sign,
                'type'=>1, //返修
                'add_time'=>time(),
            );
            $data_all[] = $data;
        }
        $ret = $this->_order_serve_info_mod->add($data_all);

        $ret && $this->show_message("操作成功!",'返回列表',    'index.php?app=fx');
    }



    /*
     * 其他地址  线下 详细
     */
    function store_address(){
        $region_id =$_REQUEST['region_id'];
        $source = $_REQUEST['source']?$_REQUEST['source']:0;
        $address =  $this->_get_address(false,$region_id,$source);

        $this->json_result($address);
        return;
    }


    /*
     * all地区
     */
    function other_address(){
        $_region_mod = &m('region');
        $_serve_mod = &m('serve');

        //全部的门店
        $address = $_serve_mod->find(array(
            'conditions'=>'1=1 group by region_id',
            'index_key'=>'region_id'
        ));

        $ids= i_array_column($address,'region_id');
        $region_info  = $_region_mod->find(array('conditions'=>db_create_in($ids, 'region_id')));
        $region_info = i_array_column($region_info,'region_name');
        return $region_info;
    }


    /*
     *返回店铺地址-详细
     *
     */
    function _get_address($order_figure=false,$region_id=false,$source=false){
        $_serve_mod = &m('serve');

        if($region_id){
            $serve_info = $_serve_mod->find("region_id='{$region_id}'");
            $address_info="<br/>";
            if(!empty($serve_info)){
                $address_info="<br/>";

                foreach($serve_info as $kk=>$vv){
                    //serve  id=>店铺信息
                    $address = '【'.$vv['serve_name'].'】'.$vv['serve_address']."&nbsp;&nbsp;({$vv['store_mobile']})";
                    $source==1&&$address.="  店长指定量体师";
                        $address_info .= "<input type='checkbox' name='address' value='{$address},{$vv['idserve']}'>&nbsp;{$address}<br/><br/>";

                }
            }

            return $address_info;
        }



        if($order_figure != false){


            $data = array('address_info'=>'','liangti_name'=>'','liangti_id'=>'');

            //当前订单门店信息
            $server_id =$order_figure['server_id'];
            $address = $_serve_mod->get("idserve='{$server_id}'");


            $address_info ='';
            if(!empty($address)){
                //量体师

                $liangti_id =$order_figure['liangti_id']?$order_figure['liangti_id']:'';
                $liangti_name =$order_figure['liangti_name']?$order_figure['liangti_name']:'';



                if($liangti_id !=''){
                    //lts
                        $figure_liangti = $this->_figure_liangti_mod->get("liangti_id='{$liangti_id}'");

                        $liangti_cart = $figure_liangti['card_number'];
                        $member_info = $this->_member_mod->get("user_id='{$liangti_id}' and serve_type=2");
                        $liangti_mob =$member_info['phone_mob'];
                        $liangti_name =$member_info['real_name'];


                    }else{
                    //店长


                        $liangti_cart =$address['card_number'];


                        $liangti_mob =$address['mobile'];
                        $liangti_name = $address['linkman'];
//                        $liangti_id = '';


                    }


                $address_info = '【'.$address['serve_name'].'】'.$address['serve_address']."({$address['store_mobile']})&nbsp;&nbsp; 量体师{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";


                }

                $data['address_info']=$address_info;
                $data['liangti_name']=$liangti_name;
                $data['liangti_id']=$liangti_id;
                $data['server_id']=$server_id;

            }

        return $data;
    }


    /*
     * 修改返修状态
     */
    function status(){
        $id =$_REQUEST['id'];

        if(!$id){
            $this->show_warning('id不能为空');
            return;
        }

        if (!IS_POST) {
            $fx_info = $this->_order_serve_info_mod->get($id);
            $fx_serve_info = $this->_order_serve_mod->get($fx_info['s_id']);

            $logs =$this->_order_serve_log_mod->find("osi_id ='{$id}' and info_status !=''");

            foreach($logs as $k=>$v){
                $logs[$k]['add_time'] =  date('Y-m-d H:i:s',$v['add_time']+8*60*60);
            }

            if($fx_serve_info['free'] ==1){
                unset($this->_status_info[5]);
                unset($this->_status_info[7]);
            }else{
                unset($this->_status_info[1]);
                unset($this->_status_info[2]);
                unset($this->_status_info[3]);
                unset($this->_status_info[4]);
                unset($this->_status_info[9]);
                unset($this->_status_info[10]);
            }

            $this->assign('status_info',$this->_status_info);
            $this->assign('id',$id);
            $this->assign('logs',$logs);

            $this->assign('waybillno',$fx_serve_info['waybillno']);
            $this->assign('free',$fx_serve_info['free']);
            $this->assign('s_id',$fx_info['s_id']);
            $this->assign('info_status',$fx_info['info_status']);
            $this->display(TYPE.'fx_status.html');
            return;
        }

        $info_status =$_REQUEST['info_status'];
        $old_info_status =$_REQUEST['old_info_status'];
        if(($old_info_status ==3||$old_info_status ==4)&&$info_status==6 ){
            $this->show_warning('免费订单已经提交rcmtm,不能取消');
            return;
        }

        $ret = $this->_order_serve_info_mod->edit($id,array('info_status'=>$info_status));
        $waybillno =$_REQUEST['waybillno']?$_REQUEST['waybillno']:'';
        $s_id =$_REQUEST['s_id']?$_REQUEST['s_id']:'';

        if(!$ret){
            $this->show_warning('系统异常');
            return;
        }

        $fx_info = $this->_order_serve_info_mod->get(array(
            'conditions'=>"type=1 and s_id={$s_id}",
            'fields'=>"s_id,min(info_status) as min"
        ));

        $fx_info && $ret =$this->_order_serve_mod->edit($fx_info['s_id'],array('status'=>$fx_info['min'],"waybillno"=>$waybillno));

        if(!$ret){
            $this->show_warning('系统异常');
            return;
        }



        //记录操作日志
        $old_info_status =$_REQUEST['old_info_status'];
        $log_data = array();
        $log_data['content']     = "把状态\"{$this->_status_info[$old_info_status]}\"改成了\"{$this->_status_info[$info_status]}\"";
        $waybillno!=''&&  $log_data['content'] .=",添加发货物流单号'{$waybillno}'";
        $log_data['old_status']  =$old_info_status;
        $log_data['info_status'] = $info_status;
        $log_data['admin']        = $_SESSION['admin_info']['user_name'];
        $log_data['type']         = 1;
        $log_data['osi_id']       = $id;
        $log_data['add_time']     = time();

        $this->_order_serve_log_mod->add($log_data);



        $this->show_warning("编辑成功",
            '返回上一页',    "index.php?app=fx&act=status&id={$id}",
            '查看详情',    "index.php?app=fx&act=info&id={$id}",
            '返修列表页',    "index.php?app=fx&act=index"
        );


    }


    function excel(){
        //把excel的数据  导入到sql
    }


    function curl_post_ssl($url,$vars){
        $ch = curl_init();

        //curl_setopt($ch,CURLOPT_VERBOSE,'1');
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        // 	curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        // 	curl_setopt($ch,CURLOPT_SSLCERT,$cfg['PEM_PATH'].'public.pem');

        // 	curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        // 	curl_setopt($ch,CURLOPT_SSLKEY,$cfg['PEM_PATH'].'key.pem');

        $aHeader = array(
            "user:QC2C",
            "pwd:". md5("369147"),
            //'xml:1',
            'lan:zh',
            //'Content-Type: text/xml; charset=utf-8',
            'Content-Type:application/xml',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);

        $data = curl_exec($ch);
        if (curl_error($ch)){
            $rs = array('err'=>1,'msg'=>"curl error:".curl_error($ch) );//-1:代表CURL请求过程中的错误~
        }else{
            $rs = array('err'=>0,'data'=>json_decode($data,1));
        }
        //print_R($rs);

        curl_close($ch);
        //echo iconv("UTF-8","GBK", $data);
        //        echo $data;
        //        echo "\r\n";
        return $rs;

    }

}?>