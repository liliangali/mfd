<?php
/**
 * 特权码
 *
 * @author  yusw 2015-07-17
 */
class Special_codeApp extends BackendApp
{
    //分类 功能
    private $_cate ;
    private  $_member_mod;
    private $_lv_mod;
    private  $_special_code_mod;

    private $_is_used = array(
        'all'=>'全部',
        0=>'未激活',
        1=>'已激活'
    );
    private $_source = array(
        0=>array(
            'name'=>'线上',
            'type'=>'-短信',
            'suoxie'=>'A'
        ),
        1=>array(
            'name'=>'线上',
            'type'=>'-app推送信息',
            'suoxie'=>'B'
        ),
        2=>array(
          'name'=>'线下',
          'type'=>'',
          'suoxie'=>'C'
        ),
    );




    function __construct()
    {
        parent::__construct();
        $path = array( 
            20 => array(
           'sign'=>'C',
           'name'=>'麦富迪E卡',
           'work'=>'用户激活后，可用于购物结算。',//等级不变  积分在用户原来基础上+  ！！！
           'description'=>'用户激活后,可用于购物结算。', //for app
           'message'=>'您已获得创业者麦富迪E卡特权，输入麦富迪E卡，直接享受麦富迪币福利', // for user
            ),
//             21 => array(
//                 'sign'=>'C',
//                 'name'=>'麦富迪E卡',
//                 'work'=>'用户激活后，面值金额以麦富迪币形式体现在麦富迪币账户，可用于购物结算。',//等级不变  积分在用户原来基础上+  ！！！
//                 'description'=>'用户激活后，面值金额以麦富迪币形式体现在麦富迪币账户，可用于购物结算。', //for app
//                 'message'=>'您已获得创业者麦富迪E卡特权，输入麦富迪E卡，直接享受麦富迪币福利', // for user
//             ),
            
        );
        
        $this->_cate =  $path;

        $this->_member_mod = & m('member');
        $this->_lv_mod =& m('memberlv');
        $this->_special_code_mod = & m('special_code');
        $this->_special_log_mod = & m('special_log');
        $this->type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
            //'0109' => "套装",
        );
        $this->assign('type',$this->type);
       // $this->assign('phone',substr_replace($this->valite_phone,'****',3,4));
        define("TYPE", "special_code/");
    }

    /**
     * 首页
     *
     */
    function index(){


        $sign =$_REQUEST['sign'];

        foreach($this->_cate as $k=>$v){
            if($sign == 1){
                //码
                if($k<20) {
                    unset($this->_cate[$k]);
                    continue;
                }
            }else{
                //酷卡
                if($k>19) {
                    unset($this->_cate[$k]);
                    continue;
                }
            }


            //发放总数
            $info = $this->_special_code_mod->find(array(
                'conditions' => "cate=".$k,
                'count' => true,
            ));

            $this->_cate[$k]['all_count'] = count($info);
            if($this->_cate[$k]['all_count'] == 0){
                $this->_cate[$k]['baifen']=0;
                continue;
            }

            //使用总数
            $use_info = $this->_special_code_mod->find(array(
                'conditions' => "is_used=1 and cate=".$k,
                'count' => true,
            ));

            $this->_cate[$k]['use_count'] = count($use_info);
            $this->_cate[$k]['baifen']    = round($this->_cate[$k]['use_count']/$this->_cate[$k]['all_count']*100,1);
        }


        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->assign('sign', $sign);
        $this->assign('list', $this->_cate);
        $this->display(TYPE.'index.html');

    }

    /**
     * 详情页
     *
     */
    function info()
    {

        $id =intval($_REQUEST['id']);
        if(!$id){
            $this->show_warning('id不能为空');
            return;
        }

        //特权码
        $sn = isset($_REQUEST['sn'])?trim($_REQUEST['sn']):'';
        $is_used = isset($_REQUEST['isused'])?$_REQUEST['isused']:'all';
        $user_name = isset($_REQUEST['user_name'])?trim($_REQUEST['user_name']):'';

        $start_time = isset($_REQUEST['start_time'])?$_REQUEST['start_time']:'';
        $end_time = isset($_REQUEST['end_time'])?$_REQUEST['end_time']:'';
        $cate =intval($_REQUEST['cate']);

        if($start_time >$end_time){
            $this->show_warning("开始时间不能大于结束时间");
            return;
        }


        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }


        $conditions = '1=1 ';
        if($sn !=''){
            $conditions .=" AND sn='{$sn}'";
        }

        if($start_time && $end_time){
            $start_time1 = strtotime($start_time)-8*3600;
            $end_time1 = strtotime($end_time)+16*3600;
            $conditions .=" AND to_time between '{$start_time1}' and '{$end_time1}'";
        }
        if($user_name !=''){
            $conditions .=" AND user_name='{$user_name}'";
        }
        if($is_used !='all'){
            $conditions .=" AND is_used='{$is_used}'";
        }


        //暂时根据用户id来查询
        $page = $this->_get_page(30);
        $conditions .= "and log_id='{$id}'";

        $list = $this->_special_code_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));

        foreach($list as $k=>$v){
            $list[$k]['expire_time'] =local_date('Y-m-d H:i:s',$v['expire_time']);
            $list[$k]['to_time'] =local_date('Y-m-d H:i:s',$v['to_time']);
        }
        $page['item_count'] = $this->_special_code_mod->getCount();
        $this->_format_page($page);


        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));


        $this->assign('page_info', $page);
        $this->assign('sn', $sn);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('user_name', $user_name);
        $this->assign('log_id', $id);
        $this->assign('cate', $cate);
        $this->assign('is_used', $is_used);
        $this->assign('_is_used', $this->_is_used);
        $this->assign('list', $list);
        $this->display(TYPE.'info.html');

    }
    /**
     * log
     *
     */
    function log(){
        $cate = $_REQUEST['cate'];
        if(!$cate){
            $this->show_message("id不能为空");
            return;
        }

        $conditions = '1=1 ';
        $start_time = isset($_REQUEST['start_time'])?$_REQUEST['start_time']:'';
        $end_time = isset($_REQUEST['end_time'])?$_REQUEST['end_time']:'';


        if($start_time >$end_time){
            $this->show_warning("开始时间不能大于结束时间");
            return;
        }

        if($start_time && $end_time){
            $start_time1 = strtotime($start_time)-8*3600;
            $end_time1 = strtotime($end_time)+16*3600;
            $conditions .=" AND add_time between '{$start_time1}' and '{$end_time1}'";
        }

        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }


        $page = $this->_get_page(30);

        $list = $this->_special_log_mod->find(array(
            'conditions' => $conditions." and  cate='{$cate}'",
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));

        $all_use_count = 0;

        $del_arr = array();
        foreach($list as $k=>$v){
            //用户使用数
            $info = $this->_special_code_mod->find(array(
                'conditions'=>"to_id > 0 and log_id='{$k}'",
            ));

            $use_count = count($info);
            $all_use_count =$use_count+$all_use_count;

            $list[$k]['use_count'] = $use_count;
            $list[$k]['cate_name'] =$this->_cate[$v['cate']]['name'];
            $list[$k]['source_name'] =$this->_source[$v['source']]['name'].''.$this->_source[$v['source']]['type'];
            $list[$k]['expire_time'] =local_date('Y-m-d H:i:s',$v['expire_time']);
            $list[$k]['add_time'] =local_date('Y-m-d H:i:s',$v['add_time']);
            $list[$k]['description'] =mb_substr($list[$k]['description'],0,30,'utf-8');

            if($list[$k]['num'] == 0){
                $del_arr[] =$k;
                unset($list[$k]);
            }else{
                $list[$k]['baifen']  = round($use_count/$list[$k]['num']*100,1);
            }

        }



        //因为解绑入口多  直接在这批量删除日志 num=0的数据
        $this->_special_log_mod->drop(db_create_in($del_arr,'id'));


        $page['item_count'] = $this->_special_log_mod->getCount();
        $this->_format_page($page);

        //因为老数据影响  统一用实际的发放总数
        $use_info = $this->_special_code_mod->find(array(
            'conditions' => "cate=".$cate,
            'count' => true,
        ));
        $all_count = count($use_info);
        if($all_count == 0){
            $all_baifen = 0;
        }else{
            $all_baifen=round($all_use_count/$all_count*100,2);
        }

        $this->assign('all_baifen',$all_baifen);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('page_info', $page);
        $this->assign('cate', $cate);
        $this->assign('list', $list);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display(TYPE.'log.html');
      }



    /**
     * 导出excel
     * @author yusw
     * @2015-07-17
     *
     */
    function export (){

        $log_id  = intval($_REQUEST['id']);
        $cate = intval($_REQUEST['cate']);
        $conditions = "log_id='{$log_id}'";
        $list = $this->_special_code_mod->find(array(
            'conditions' => $conditions,
            'count' => true,
        ));

        $log = $this->_special_log_mod->get('id='.$log_id);

        $cate_name = $this->_cate[$cate]['name'];

        foreach ($list as &$row){
            $row['add_time']=local_date("Y-m-d H:i:s",$row['add_time']);
            $row['expire_time']=local_date("Y-m-d H:i:s",$row['expire_time']);
            $row['source'] = $this->_source[$_REQUEST['source']]['name'];
            $row['work'] = $this->_cate[$row['cate']]['work'];
            $row['cate'] = $cate_name;
            $row['is_used'] = $row['is_used']==0?'未使用':'已使用';

            if($row['to_time'] !=' '){
                $row['to_time'] = local_date('Y-m-d H:i:s',$row['to_time']);
            }
            $row['admin'] =$log['admin'];
            if($cate != 21)unset($row['price']);
            if($cate>19)unset($row['source']);

            unset($row['log_id']);
            unset($row['to_id']);
            unset($row['add_time']);
        }

        if($cate==1 ||$cate==2) $work_name='越级等级';
        if($cate>19) $work_name='麦富迪币面值';
        $fields_name = array('ID','SN',$work_name,'购买价格',$cate_name,'购卡人（创业者）','购卡人ID','发放用户','发放时间','是否激活','过期时间','来源','作用','发放者');
        if($cate != 21)unset($fields_name[3]);
        if($cate>19)unset($fields_name[11]);
        array_unshift($list,$fields_name);
        $this->export_to_csv($list, "【特权码】-{$cate_name}-".date('Y-m-d',time()), 'gbk');
    }




   /**
    * 导数据发放   1 多用户id导入|导入excel文件 2 消息发放
    *
    * */

    function send(){

        $cate =$_REQUEST['cate'];
        if($cate >19){
            echo  '暂时不开放麦富迪E卡线上发放';
            return false;
        }

        if($cate ==1){
            echo  '暂时不开放越级码线上发放';
            return false;
        }
        if(!IS_POST){
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('cate_name', $this->_cate[$cate]['name']);
            $this->assign('message', $this->_cate[$cate]['message']);
            $this->assign('work', $this->_cate[$cate]['work']);

            $this->assign('cate', $cate);
            $this->display(TYPE.'send.html');
            return;
        }

        //会员id
        $ids = $_REQUEST['ids'];

        if($ids == ''){
            $this->show_warning('请添加要操作的用户');
            return;
        }
        $ids = array_unique(explode(',', $ids)); //去重
        $num = count($ids);
        $expire_time = strtotime($_REQUEST['expire_time'])+57599;//16*3600-1;

        $description = htmlspecialchars(trim($_REQUEST['description']));


        if($expire_time<=gmtime()){
            $this->show_warning('过期时间不能小于当前时间');
            return;
        }

        //短信  app
        $tongzhi = $_REQUEST['tongzhi'];
        $content = htmlspecialchars($_REQUEST['contents']);

        $data = array(
            'cate'=> $cate,
            'source'=>$tongzhi,
            'description'=>$description,
            'num'=>$num,
            'expire_time'=> $expire_time,
            'admin'=>$_SESSION['admin_info']['user_name'],
            'add_time'=> gmtime(),
        );


        $log_id = $this->_special_log_mod->add($data);
        $suoxie =$this->_source[$tongzhi]['suoxie'];


        $transaction = $this->_special_code_mod->beginTransaction();
        $res = $this->_special_code_mod->submit($ids,$this->_cate,$_REQUEST['work_num'],$cate,$suoxie,$log_id,$expire_time,$tongzhi,$content);
        if (!$res)
        {
            $this->_special_code_mod->rollback();
        }
        $ret = $this->_special_code_mod->commit($transaction);


        if(!$ret){
            $this->show_warning("系统异常");
            return false;
        }
        $this->show_warning("生成成功",
            '查看详情',    "index.php?app=special_code&act=log&cate={$cate}"
        );

    }


    /**
     * 检查推广员唯一性  和是不是用过此类型码
     *
     */
    function  check_user()
    {

        $user_name = empty($_REQUEST['user_name']) ? null : trim($_REQUEST['user_name']);
        $cate_name =$this->_cate[$_REQUEST['cate']]['name'];

        if (!$user_name)
        {
            echo ecm_json_encode(array('done'=>false,'msg'=>'用户不能为空'));
            return ;
        }

        if(strlen($user_name)!=11 || !is_numeric($user_name)){
            echo ecm_json_encode(array('done'=>false,'msg'=>'请填写11位手机号'));
            return ;
        }


        //同类型的用户只能用一次特权码
        if ($info=$this->_member_mod->get("user_name=$user_name  AND serve_type=1" ))
        {
            //现在只有cate 1  2是同一种类型 互斥
            $info2 = $this->_special_code_mod->get('to_id='.$info['user_id']);
            if($info2){
                $return = array(
                    'done' => false,
                    'msg'       => '当前用户已经通过线上，绑定过一次此类型的'.$cate_name.'（'.$info2['sn'].'）!!  ',
                );
                echo  ecm_json_encode($return);
                return;
            }
            if($info2['is_used'] == 1){
                $return = array(
                    'done' => false,
                    'msg'       => '当前用户已经使用过一次此类型的'.$cate_name.'（'.$info2['sn'].'）!!  ',
                );
                echo  ecm_json_encode($return);
                return;
            }

            echo ecm_json_encode(array('done'=>true,'msg'=>$info['user_id']));
            return;
        }

        echo ecm_json_encode(array('done'=>false,'msg'=>'当前创业者不存在'));

    }
    /**
     * 线下  批量生成
     *
     */
    function add(){
        $cate = $_GET['cate'];//分类  包括作用描述  什么码
        if(!$cate){
            $this->show_message("id不能为空");
            return;
        }

        if($cate ==1){
            echo  '暂时不开放初级码线上发放';
            return false;
        }
        
        if (!IS_POST) {

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('list', $this->_cate[$cate]);
            $this->assign('cate', $cate);
            $this->display(TYPE.'add.html');
            return;
        }

        $expire_time = strtotime($_REQUEST['expire_time'])+86399;//16*3600-1;
        $description = $_REQUEST['description'];
        $name = $_POST['name'];
        $type = $_POST['type'];
        if (!$type) 
        {
            $this->show_warning('品类必选');
            return;
        }
        $type = implode(',', $type);

        $num = $_REQUEST['num'];
        if($cate ==1 )$work_num =4;
        if($cate ==2) $work_num =2;
        if($cate >19) $work_num =$_REQUEST['work_num']?$_REQUEST['work_num']:' ';
        $price =isset($_REQUEST['price'])?$_REQUEST['price']:'';

        if($cate == 21&& $price==''){
            $this->show_warning("请填写价格");
            return;
        }
// print_exit($_POST);
        $data = array(
            'cate'=> $cate,
            'source'=>2,
            'description'=>$description,
            'num'=>$num,
            'price'=>$price,
            'expire_time'=> $expire_time,
            'admin'=>$_SESSION['admin_info']['user_name'],
            'work_num'=>$work_num,
            'add_time'=> gmtime(),
        );

        $log_id = $this->_special_log_mod->add($data);
        if (!$log_id) 
        {
            $this->show_warning('加入sepcial_log表失败');
            return;
        }
        $data2 =array();
        $sn_arr =$this->_special_code_mod->sn1($this->_cate,$cate,'C',$num);
        if(empty($sn_arr))$sn_arr =$this->_special_code_mod->sn1($this->_cate,$cate,'C',$num);
// print_exit($sn_arr);
        for($i=0;$i<$num;$i++){
            //生成特权码 --线下 暂时写死
//            $sn =$this->_special_code_mod->sn($this->_cate,$cate,'C');
            $data1 = array(
                'sn'=> $sn_arr[$i], //$sn
                'name' => $name,
                'type' => $type,
                'cate'=> $cate, //生成一批干嘛的码
                'log_id'=>$log_id,
                'price'=>$price,
                'expire_time'=> $expire_time,
                'work_num'=>$work_num,
                'add_time'=> gmtime(),
                'content' => $description,
            );
            $data2[] = $data1;
        }
        $res = $this->_special_code_mod->add($data2);

        if(!$res){
            $this->show_warning("添加失败");
            return;
        }

        $this->show_warning("生成成功",
            '导出excel文件',    "index.php?app=special_code&act=export&id={$log_id}&cate={$cate}&source=2",
            '查看详情',    "index.php?app=special_code&act=log&cate={$cate}"
        );

    }




    /*特权码解绑*/
    function del(){

        return;
        $res = $this->_special_code_mod->find("to_id='668'");


        $res = i_array_column($res,'log_id');
//        $ret = $this->_special_code_mod->drop(db_create_in($to_id,'to_id'));
        $ret = $this->_special_code_mod->drop("to_id='668'");
        $ret = $this->_special_log_mod->drop(db_create_in($res,'id'));

        if($ret){
            echo 'yes';
            return;
        }
        echo 'no';

    }



}

?>
