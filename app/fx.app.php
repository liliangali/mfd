<?php
/**
 *返修
 *@author yushuangwei
 *@2015年12月31日
 */
class FxApp extends MemberbaseApp
{

	var $mod;
	var $mod_img;
    var $user_info;
    function __construct()
    {

        parent::__construct();
        //没有登陆
        if (!$this->visitor->has_login)
        {
            $this->json_error("请先登录再进行操作");
            return;
        }

        $this->user_info = $this->visitor->get();

        if(!$this->user_info){
            $this->show_warning("账号不存在！");
            return;
        }



        $this->mod = m('works');
        $this->mod_img = m('workimgs');
    }







    /*返修提交页面*/
    function add()
    {


        if(!IS_POST){
            $order_id =$_REQUEST['order_id']?$_REQUEST['order_id']:'';

            $user_info = $this->user_info;


            $fx_reason = array(
                0=>'尺寸原因',
                1=>'工艺问题',
                2=>'特殊部位',
            );



            $_order_serve_mod = & m('orderserve');
            $fx_info = $_order_serve_mod->get(array(
                'conditions'=>"type=1 and order_id={$order_id}  ",
                'fields'=>'store_name,rec_id,free,price,gy_method,goods_name,address,sign,liangti_id,liangti_name,user_id,serve_id'
            ));

            if(empty($fx_info)){
                $this->show_warning("当前订单不支持返修，请联系客服再进行操作！");
                return;
            }

            if($fx_info['user_id'] !=$user_info['user_id']){
                $this->show_warning("您不能对此订单进行此操作!");
                return;
            }



            //门店量体
            $member_mod =&m('member');
            $_serve_mod =&m('serve');
            $figure_liangti_mod =&m('figure_liangti');

            $address = $_serve_mod->get("idserve='{$fx_info['serve_id']}'");
            if($address){
                $fx_info['store_name'] =$address['serve_name'];
                if($fx_info['sign']==2){
                    $fx_info['address'] =$address['serve_address']."({$address['store_mobile']})"; //$address['region_name'].
                }
            }

            if($fx_info['liangti_id'] ){
                $liangti_info =$member_mod->get("serve_type=2 and user_id={$fx_info['liangti_id']}");
                if($liangti_info){
                    if($liangti_info['figure_type']==2){
                        //lts
                        $figure_liangti = $figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
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
                        $fx_info['liangti_name']="量体师{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";
                    }else{
                        $fx_info['liangti_name']='';
                    }

                }
            }else{
                $fx_info['liangti_name']='店长指定量体师';
            }

            $fx_info['fx_reason'] =$fx_reason;
            $order_info  = $this->order_info($fx_info['user_name'],$fx_info['rec_id']);

            $this->assign("order_info",$order_info);
            $this->assign("fx_info",$fx_info);
            $this->assign("order_id",$order_id);
            $this->_config_seo('title','我的麦富迪 - 我的订单返修');
            $this->assign('app', APP);
            $this->display('fx.index.html');

            return;
        }
        $this->fx_add();
       
    }




    /*返修提交*/
    function fx_add()
    {
        $reason = $_REQUEST['fx_reason'] !="all"?(int) $_REQUEST['fx_reason']:'';
        $to_time =$_REQUEST['to_time']?strtotime(trim($_REQUEST['to_time'])):'';
        $order_id =$_REQUEST['order_id']?$_REQUEST['order_id']:'';
        $wl_sn = $_REQUEST['wl_sn']?trim($_REQUEST['wl_sn']):'';
        $user_info = $this->user_info;


        $_order_serve_mod = & m('orderserve');
        $_order_serve_info_mod = & m('orderserveinfo');

        $fx_info = $_order_serve_mod->get(array(
            'conditions'=>"type=1 and order_id={$order_id} ",
            'fields'=>'address,rec_id,sign,user_id,order_id,id'
        ));
        
        if(empty($fx_info)){
            $this->json_error("当前订单不支持返修，请联系客服再进行操作！");
            return;
        }

       if($fx_info['user_id'] !=$user_info['user_id']){
           $this->json_error("您不能对此订单进行此操作！");
           return;
       }

        if($reason === ''){
            $this->json_error("请选择返修原因！");
            return;
        }


        // 线上物流号不能为空
        if($fx_info['sign'] == 1 && !$wl_sn){
            $this->json_error("请输入物流号！");
            return;
        }

        if($fx_info['sign'] == 2 && !$to_time){
            $this->json_error("请选择到店时间！");
            return;
        }

        if(($fx_info['free']==1&&$fx_info['status']>=4)||($fx_info['free']==0&&$fx_info['status']>=7)){
            $this->json_error("已经提交过一次申请！");
            return;
        }


        $status =$fx_info['free']==="1"?1:5;
        $_order_serve_mod->edit("order_id='{$order_id}'",array('wl_sn'=>$wl_sn,'reason'=>$reason,'to_time'=>$to_time,'status'=>$status));
        $_order_serve_info_mod->edit('order_id="'.$order_id.'"',array('info_status'=>$status));

        if($fx_info['sign'] == 1){
            $msg = '返修组收到货后会第一时间处理返修业务，请耐心等待！';
            $result = array('msg'=>$msg,id=>$fx_info['id']);
        }
        if($fx_info['sign'] == 2){
            $time = date('Y-m-d',$to_time);
            $msg = "提交成功，请您带上商品于'{$time}'日前往预约门店进行返修！";
            $result = array('msg'=>$msg,id=>$fx_info['id']);
            //短信通知
            if($fx_info['liangti_id']){
                $_order_mod =&m('order');
                $order_info = $_order_mod->get("order_id='{$order_id}'");
                $phone_mob = empty($order_info['ship_mobile'])  ? $order_info['ship_tel'] : $order_info['ship_mobile'];

                $store_msg ="门店店长，内容：“客户{$fx_info['user_name']}预约{$time}到店返修商品（订单号{$fx_info['order_sn']}），承接量体师{$fx_info['liangti_name']},客户电话{$phone_mob}，请做好接待工作。";
                $liangti_msg = "您的客户{$fx_info['user_name']}预约{$time}到店返修商品（订单号{$fx_info['orde_sn']}）,客户电话{$phone_mob}，请做好接待工作。";

                $m_mod =&m('member');
                $figure_liangti_mod =&m('figure_liangti');
                $liangti_info =$figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                $store_id =$liangti_info['manager_id'];
                $liangti_info = $m_mod->get("user_id='{$fx_info['liangti_id']}' and serve_type=2");
                $store_info = $m_mod->get("user_id='{$store_id}' and serve_type=2");
                SendSms($store_info['phone_mob'],$store_msg);
                SendSms($liangti_info['phone_mob'],$liangti_msg);
            }
        }

        return $this->json_result($result);
    }



    /* add页面 原来订单详情*/
    function order_info($user_name,$rec_id){
        $measure = array(
            '2'  => "到店量体",
            '6'  => "指派量体师",
            '5'  => "现有量体"
        );


        $order_goods_mod =&m('ordergoods');
        $fabirc_mod = m('fabric');
        $order_figure_mod = m('orderfigure');
        $suit_mod = m('ordersuit');


        $order_info =$order_goods_mod->find(array(
            "join"=>"belongs_to_order",
            "conditions"=>"order_goods.rec_id in({$rec_id})",//db_create_in($fx_order_ids,"order_goods.rec_id"),
            "fields"=>'*,order_goods.fabric as fabric'
        ));
        //价格准吗？？？？？yusw
        //echo '<pre>';var_dump($order_info);die;
        $fx_order =array();
        foreach($order_info as $k=>$v){

            if($v['size']!='diy'){
                $fx_order['fx_info'][$v['rec_id']]['size'] ="尺码：".$v['size'];
            }else{
                //兼容老数据
                $size ="量体定制";
                $figure_info = $order_figure_mod->get("son_sn='{$v['son_sn']}'");
                if($measure[$figure_info['measure']]&&$user_name){
                    $size .="({$measure[$figure_info['measure']]}/{$user_name})";
                }
                if(!$measure[$figure_info['measure']] &&$user_name){
                    $size .="({$user_name})";
                }
                $fx_order['fx_info'][$v['rec_id']]['size']=$size;
            }

            if($v['type']=="suit"){
                $fx_order['fx_info'][$v['rec_id']]['fabric'] ='';
                $suit_info  = $suit_mod->get("order_id='{$v['order_id']}'");
                $fx_order['fx_info'][$v['rec_id']]['img'] =$suit_info['goods_image'];
                $fx_order['fx_info'][$v['rec_id']]['goods_name'] =$suit_info['goods_name'];
            }elseif($v['type']=='diy'){
                $fabric_info = $fabirc_mod->get("CODE='{$v['fabric']}'");
                $fx_order['fx_info'][$v['rec_id']]['fabric'] =$fabric_info['tname'];
                $fx_order['fx_info'][$v['rec_id']]['goods_name'] =$v['goods_name'];//."(面料：{$fabric_info['tname']})";
                $fx_order['fx_info'][$v['rec_id']]['img'] =$v['goods_image'];
            }

            $fx_order['fx_info'][$v['rec_id']]["oprice"] =$v['oprice'];//单个物品  实际购买价格
            $fx_order["order_amount"] =$v['order_amount'];
        }

        return $fx_order;
    }



    function fx_info(){

        //查询返修的号码

        $order_id =$_REQUEST['order_id']?$_REQUEST['order_id']:'';
        $user_info = $this->user_info;



        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );

        $_pl_for_str = array(
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

        $color_status = array(
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
            '棕褐色'=>'101#棕褐色', //java停用了？
        );
        $_pl_status = array(
            '0003'  => '西服', //MXF
            '0004' => '西裤', //MXK
            '0005' => '马夹',//MMJ
            '0006'  => '衬衣',//MCY
            '0007' =>  '大衣',//MDY
            '0018' =>'立领西服',
            '0010'=>'礼服', //MLF
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



        //返修方案
        $_order_fx_mod =& m('orderfx');
        $_cart_mod=& m('cart');//刺绣
        $_special_question_mod = &m('specialquestion');
        $_order_serve_info_mod = & m('orderserveinfo');


        $fx_info = $_order_serve_info_mod->get(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>"order_serve.type=1 and order_serve_info.order_id={$order_id} and order_serve.user_id={$user_info['user_id']}",
            'fields'=>"*,order_serve_info.rec_id as rec_id,order_serve.rec_id as rec_ids,order_serve.serve_id"
        ));


        if(!$fx_info){
            $this->show_warning("当前订单没有申请过返修操作！");
            return;
        }

        if($fx_info['status']>=1){
    //        $fx_status_1 = array('等待用户提交返修','方案未提交','返修中','生产中','已发货','已完成');  //线上
    //        $fx_status_2= array('等待用户提交返修','方案未提交','等待到店','返修中','生产中','已发货','已完成'); //线下
            $fx_status_1 = array(1=>'返修中',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'生产中',7=>'生产中',8=>'生产中',9=>'已发货',10=>'已完成');  //线上
            $fx_status_2=  array(1=>'等待到店',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'生产中',7=>'生产中',8=>'生产中',9=>'已发货',10=>'已完成'); //线下


            $fx_info['status_name'] = $fx_info['sign']==1?$fx_status_1[$fx_info['status']]:$fx_status_2[$fx_info['status']];
        }





        if($fx_info['status']==1){
            if($fx_info['sign']==2){
            $fx_method ='请携带商品及时到店接受返修服务。';
            }
            if($fx_info['sign']==1){
                $fx_method ='业务受理中…';
            }
        }


        $fx_info['reason'] =$fx_reason[$fx_info['reason']];
        $fx_info['to_time']=$fx_info['to_time']?date('Y-m-d',$fx_info['to_time']):'';



        //门店量体
        $member_mod =&m('member');
        $_serve_mod =&m('serve');
        $figure_liangti_mod =&m('figure_liangti');
        $address = $_serve_mod->get("idserve='{$fx_info['serve_id']}'");
        if($address){
            $fx_info['store_name'] =$address['serve_name'];
            if($fx_info['sign']==2){
                $fx_info['address'] =$address['serve_address']."&nbsp;&nbsp;({$address['store_mobile']})"; //$address['region_name'].
                //        $address= $fx_info['address'];
                //        $address_arr =explode(')',$address);
                //        $fx_info['address'] =$address_arr[0].')';
                //        $fx_info['liangti_name'] = str_replace("   ",'',$address_arr[1]);
            }
        }

        $fx_info['liangti_mob']='';
        if($fx_info['liangti_id'] ){
            $liangti_info =$member_mod->get("serve_type=2 and user_id={$fx_info['liangti_id']}");

            if($liangti_info){

                if($liangti_info['figure_type']==2){
                    //lts
                    $figure_liangti = $figure_liangti_mod->get("liangti_id='{$fx_info['liangti_id']}'");
                    $liangti_cart = $figure_liangti['card_number'];
                    $liangti_mob =$liangti_info['phone_mob'];
                    $liangti_name = $liangti_info['real_name'];
                }else{
                    //店长
                    $liangti_cart =$address['card_number'];
                    $liangti_mob =$address['mobile'];
                    $liangti_name = $address['linkman'];
                }

                if($liangti_name){
//                    $fx_info['liangti_name']="量体师{$liangti_name}（证号{$liangti_cart}，电话{$liangti_mob}）";
                    $fx_info['liangti_name']="量体师{$liangti_name}（证号{$liangti_cart}）";
                    $fx_info['liangti_mob']=$liangti_mob;

                }else{
                    $fx_info['liangti_name']='';
                }

            }
        }else{
            $fx_info['liangti_name']='店长指定量体师';
        }




        //返修方案
        $has_fx = $_order_fx_mod->find(array(
            'conditions'=>"order_id='{$fx_info['order_id']}'",
            'index_key'=>''
        ));
        $has_fx_info =!empty($has_fx)?$has_fx:'';

        // 刺绣信息
        $cx = $_cart_mod->_format_embs();
        $user_fx_info = array();
    //            return $json->encode($has_fx_info);
        if($has_fx_info){
            foreach($has_fx_info as $ok=>$ov){

                $ov['content'] =json_decode($ov['content'],1);
                $cx_info =array_values($cx[$ov['content']['cloth']]);
                foreach($ov['content'] as $k=>$v){
                    $user_fx_info[$ok]['cloth'] = $_pl_status[$ov['content']['cloth']];
                    if($k=="cx"){
                        foreach($v as $kk=>$vv){
                            foreach($cx_info as $kkk=>$vvv){

                                if($vvv['name']==$kk){
                                    if($kk=="颜色"){
                                        $color_status[$vvv['list'][$vv]['name']]&&$user_fx_info[$ok][$k][$kk] =$color_status[$vvv['list'][$vv]['name']];
                                    }else{
                                        $vvv['list'][$vv]['name']&&$user_fx_info[$ok][$k][$kk] = $vvv['list'][$vv]['name'];
                                    }
                                }elseif($vvv['name']=='内容'){
                                    $content = explode(":",$v['content']);
                                    $content[1]&&$user_fx_info[$ok][$k]['内容'] = $content[1];
                                }
                            }
                        }
                    }
                    if($k=="special"){
                        $special =array();
                        $i = 0;
                        foreach($v as $kk=>$vv){
                            $i++;
                            //特殊处理
                            $special_info = $_special_question_mod->get(array(
                                'join'          => 'has_solution',//,has_special
                                'conditions'=>"special_solution.solution_id='{$kk}' and special_solution.ecode='{$vv}' and special_question.status=1 and special_solution.status=1",
                                'fields'=>'special_solution.solution_name,special_solution.solution',
                            ));
                            $special["{$i}、".$special_info['solution_name']] =$special_info['solution'];
                        }
                        $user_fx_info[$ok]['special'] = $special;
                    }
                    if($k == 'lt'){
                        $user_fx_info[$ok]['lt'] = array_values($v);

                    }
                }


            }
        }

        //=======返修   end==========

//        echo '<pre>';var_dump($fx_info);die;
//        $this->assign('fx_info',$fx_info);
        $fx_info['fx_str'] = $fx_method ;
        $fx_info['fx_method'] = $user_fx_info?$user_fx_info:array();
//        $this->assign("fx_method",$fx_method);
//echo '<pre>';var_dump($fx_info);die;
        return $this->json_result($fx_info);

    }


    //返修列表
    function fx_list()
    {

        //需要分页
        $user_info = $this->user_info;
        $order_status =  array(
            '0'  => "已取消",
            '11' => "待付款",
            '12' => "待量体",
            '20' => "已支付",
            '60' => "生产中",
            '30' => "已发货",
            '40' => "已完成",
            '41' => "返修中",
            '43' => "订单异常",
            '61' => "备货中",
            '70' => "退款中",
            '80' => "已退款",
        );

        $measure = array(
            '2'  => "到店量体",
            '6'  => "指派量体师",
            '5'  => "现有量体"
        );


        $fx_reason = array(
            0=>'尺寸原因',
            1=>'工艺问题',
            2=>'特殊部位',
        );


        /*为了方便临时用*/
        $_pl_status = array(
            '0003'  => '西服', //MXF
            '0004' => '西裤', //MXK
            '0005' => '马夹',//MMJ
            '0006'  => '衬衣',//MCY
            '0007' =>  '大衣',//MDY
            '0018' =>'立领西服',
            '0010'=>'礼服', //MLF
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

        //    return $json->encode($order_info);
        $order_goods_mod =&m('ordergoods');
        $fabirc_mod = &m('fabric');
        $order_figure_mod = &m('orderfigure');
        $_order_serve_info_mod = & m('orderserveinfo');
        $suit_mod = m('ordersuit');


        $page =$this->_get_page(8);
        $fx_info = $_order_serve_info_mod->find(array(
            'join'          => 'has_order_serve',//,has_special
            'conditions'=>"order_serve.status>0 and order_serve.type=1  and order_serve.user_id='{$user_info['user_id']}'",
            'fields'=>"*,order_serve_info.rec_id as rec_id",
            'order' => "order_serve.add_time desc",
            'limit'      => $page["limit"],
            'count'      => true,
        ));


        $fx_status_1 = array(1=>'返修中',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成');  //线上

        if(empty($fx_info)){
            $this->show_warning("不存在返修订单！");
            return;
        }

        $order_goods_info =$fx_order_ids =$fx_order =array();


        foreach($fx_info as $k=>$v){
            $fx_order_ids[] =$v['rec_id'];
            $fx_order[$v['order_id']]['order_sn'] =$v['order_sn'];
            $fx_order[$v['order_id']]['add_time'] =date("Y-m-d H:i:s",$v['add_time']);
            $v['to_time']&&$fx_order[$v['order_id']]['to_time'] =date("Y-m-d H:i:s",$v['to_time']);
            $fx_order[$v['order_id']]['order_id'] =$v['order_id'];
            $fx_order[$v['order_id']]['user_name'] =$v['user_name'];
            $fx_order[$v['order_id']]['sign'] =$v['sign'];
            $fx_order[$v['order_id']]['price'] =$v['price'];
            $fx_order[$v['order_id']]['free'] =$v['free'];



            $fx_order[$v['order_id']]["price"] =$v['price']?$v['price']:0;

            $fx_order[$v['order_id']]['status'] =$v['status'];
            $fx_order[$v['order_id']]['waybillno'] =$v['waybillno'];

            if($v['free']==1){
                $fx_status_2=  array(1=>'等待到店',2=>'返修中',3=>'返修中',4=>'返修中',5=>'返修中',6=>'返修中',7=>'返修中',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
            }else{
                $fx_status_2=  array(5=>"待付款",6=>"已取消",7=>'等待到店',8=>'生产中',9=>'生产中',10=>'生产中',11=>'已发货',12=>'已完成'); //线下
            }

            $fx_order[$v['order_id']]['status_name'] =$v['sign']==1?$fx_status_1[$v['status']]:$fx_status_2[$v['status']];
            //加入口
            $fx_order[$v['order_id']]['if_receive'] =$v['status']==11?1:0;



        }


        $order_info =$order_goods_mod->find(array(
            "join"=>"belongs_to_order",
            "conditions"=>db_create_in($fx_order_ids,"order_goods.rec_id"),
            "fields"=>'*,order_goods.fabric as fabric'
        ));



        foreach($order_info as $k=>$v){


            $fx_order[$v['order_id']]['order_status'] =$v['status'];
            $fx_order[$v['order_id']]['order_status_name'] =$order_status[$v['status']];

            if($v['size']!='diy'){
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['size'] ="尺码：".$v['size'];
            }else{
                //兼容老数据
                $size ="量体定制";

                $figure_info = $order_figure_mod->get("son_sn='{$v['son_sn']}'");
                if($measure[$figure_info['measure']]&&$fx_order[$v['order_id']]['user_name']){
                    $size .="({$measure[$figure_info['measure']]}/{$fx_order[$v['order_id']]['user_name']})";
                }
                if(!$measure[$figure_info['measure']] &&$fx_order[$v['order_id']]['user_name']){
                    $size .="({$fx_order[$v['order_id']]['user_name']})";
                }
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['size']=$size;
            }



            if($v['type']=="suit"){
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['fabric'] ='';
                $suit_info  = $suit_mod->get("order_id='{$v['order_id']}'");
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['img'] =$suit_info['goods_image'];
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['goods_name'] =$suit_info['goods_name'];

            }elseif($v['type']=='diy'){



                $fabric_info = $fabirc_mod->get("CODE='{$v['fabric']}'");
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['fabric'] =$fabric_info['tname'];

                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['goods_name'] =$v['goods_name'];//."(面料：{$fabric_info['tname']})";
                $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]['img'] =$v['goods_image'];

            }


            $fx_order[$v['order_id']]['fx_info'][$v['rec_id']]["oprice"] =$v['oprice'];//单个物品  实际购买价格
            $fx_order[$v['order_id']]['fx_info'] =array_values($fx_order[$v['order_id']]['fx_info']);
            $fx_order[$v['order_id']]["order_amount"] =$v['order_amount'];


        }


        $page['item_count'] = $_order_serve_info_mod->getCount();   //获取统计的数据
        $this->_format_page($page);

        $this->assign('page_info',$page);


//        echo '<pre>';var_dump($fx_order['1177']);die;
//        echo '<pre>';var_dump($fx_order['4740']);die;  //'diy'
//        echo '<pre>';var_dump($order_info);die;
//        echo '<pre>';var_dump($fx_order);die;
        $this->_config_seo('title','我的麦富迪 - 我的返修订单');
        $this->assign('app', APP);
        $this->assign("fx_info",$fx_order);
        $this->display('fx.list.html');

    }




    /*返修 确认收货*/
    function fx_receive(){

        $order_id =$_REQUEST['order_id']?$_REQUEST['order_id']:'';
        $user_info = $this->user_info;


        $_order_serve_mod = & m('orderserve');
        $_order_serve_info_mod = & m('orderserveinfo');



        $fx_info =$_order_serve_mod->get("user_id='{$user_info['user_id']}' and type=1 and order_id='{$order_id}' ");
        if(!$fx_info){
            $this->json_error("当前用户不能对此订单做返修完成操作！");
            return;
        }

        if($fx_info['status'] <7){
            $this->json_error("还未发货，不能操作返修订单已完成！");
            return;
        }
        $ret = $_order_serve_info_mod->edit("order_id='{$order_id}' and type=1",array("info_status"=>12));
        $ret && $ret = $_order_serve_mod->edit("order_id='{$order_id}' and type=1",array('status'=>12));

        if (!$ret) {
            $this->json_error("系统异常！");
            return;
        }


        return $this->json_result('确认收货成功！');
        return;
    }











































}

?>
