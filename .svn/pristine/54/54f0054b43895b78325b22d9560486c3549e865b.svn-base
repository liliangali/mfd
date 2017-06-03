<?php
/**
 * 特权码
 *
 * @author  yusw 2015-07-17
 */
class Debit_lineApp extends BackendApp
{
    //分类 功能
    private $_cate ;
    private  $_member_mod;
    private $_lv_mod;
    private  $_special_code_mod;
    private $_debitline_mod;
    private $_debitlines_mod;
    private $type;

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
        /* $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path; */
        $arr  = array( 
       100 => array(
           'sign'=>'C',
           'name'=>'优惠券线下',
           'work'=>'用户激活后，面值金额以优惠券存在，可用于购物结算.',//等级不变  积分在用户原来基础上+  ！！！
           'description'=>'用户激活后，面值金额以酷卡存在，可用于购物结算。', //for app
           'message'=>'您已获得创业者酷卡特权，输入酷卡，直接享受酷卡福利', // for user
       ),);
        $this->_cate = $arr;
        $this->_member_mod = & m('member');
        $this->_lv_mod =& m('memberlv');
        $this->_debitline_mod = & m('debitline');
        $this->_debitlines_mod = & m('debitlines');
        $this->_special_log_mod = & m('special_log');
        $this->type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
            '0109' => "套装",
        );
        $this->assign('type',$this->type);
        define("TYPE", "debit_line/");
    }

    /**
     * 首页
     *
     */
    function index(){

        $sign =$_REQUEST['sign'];
        
        $line_nums = $this->_debitline_mod->get(array(
           'conditions' => "1=1",
            'fields' => "sum(num) as num", 
        ));
        $line_num = $line_nums['num'];
        
        $lines_num_activ = $this->_debitlines_mod->get(array(
            'conditions' => "is_active = 1",
            'fields' => "count(*) as num"
        ));
        $lines_num_activ = $lines_num_activ['num'];
        $rate = "0";
        if ($line_num > 0) 
        {
            $rate = floor($lines_num_activ/$line_num*100);
        }
        $this->assign('rate',$rate);
        $this->assign('line_num',$line_num);

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

        $list = $this->_debitline_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));
// print_exit($list);
        $all_use_count = 0;

        $del_arr = array();
        foreach($list as $k=>&$v){
            
            $debit_line_s_item = $this->_debitlines_mod->get(array(
                'conditions' => "dline_id = {$k} AND is_active = 1",
                'fields' => "count(*) as num",
            ));
            
            $v['active_num'] = $debit_line_s_item['num'];
            $v['acvive_rate'] = floor($v['active_num']/$v['num']*100);
            
            //用户使用数
            /* $info = $this->_debitlines_mod->find(array(
                'conditions'=>"is_used=1 and log_id='{$k}'",
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
            } */

        }



        //因为解绑入口多  直接在这批量删除日志 num=0的数据


        $page['item_count'] = $this->_debitline_mod->getCount();
        $this->_format_page($page);


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

        $id  = intval($_REQUEST['id']);
        $conditions = "dline_id = $id";
        $list = $this->_debitlines_mod->find(array(
            'conditions' => $conditions,
            'count' => true,
        ));

        $log = $this->_debitline_mod->get('id='.$id);

        foreach ($list as &$row)
        {
            $row['add_time']=local_date("Y-m-d H:i:s",$row['add_time']);
            $row['expire_time']=local_date("Y-m-d H:i:s",$row['expire_time']);
            $row['money'] = $row['money'];
            $cate = explode(",", $row['cate']);
            $cate_str = "";
            foreach ($cate as $key => $value) 
            {
                $cate_str .= $this->type[$value].",";
            }
            
            $row['cate'] = trim($cate_str,",");
            $row['is_active'] = $row['is_active']==0?'未激活':'已激活';
            unset($row['dline_id']);
            unset($row['d_id']);
        }
        $fields_name = array('ID','券号',"激活码","过期时间","面值","品类","状态","添加时间");
        array_unshift($list,$fields_name);
        $this->export_to_csv($list, "酷卡线下"."_$id_".date('Y-m-d',time()), 'gbk');
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
        
        $type = $_POST['type'];
        if (!$type) 
        {
            $this->show_warning('请选择酷卡种类');
            return;
        }

        $expire_time = strtotime($_REQUEST['expire_time'])+86399;//16*3600-1;
        $description = $_REQUEST['description'];
        $num = $_REQUEST['num'];
        $money = $_REQUEST['money'];
        //$work_num =$_REQUEST['work_num']?$_REQUEST['work_num']:' ';
        $cate = implode(',', $type);
        $data = array(
            'remark'=>$description,
            'num'=>$num,
            'expire_time'=> $expire_time,
            'admin'=>$_SESSION['admin_info']['user_name'],
            'money'=>$money,
            'add_time'=> gmtime(),
            'cate'   => $cate,
        );
// print_exit($data);
        $line_id = $this->_debitline_mod->add($data);
        if (!$line_id) 
        {
            $this->show_warning('ERROR');
            return ;
        }
        $lines_arr = array();
        $d_sn_arr = array();
        $active_sn_arr = array();
        $sn_arr['dline_id'] = $line_id;
        $sn_arr['expire_time'] = $expire_time;
        $sn_arr['money'] = $money;
        $sn_arr['cate'] = $cate;
//  echo $num;exit;
        for ($i=0;$i<$num;$i++)
        {
            $d_sn = $this->_debitlines_mod->sn($d_sn_arr);
     /*  if ($i == 1) 
      {
          echo $d_sn;exit;
      } */
            while (in_array($d_sn, $d_sn_arr))
            {
                $d_sn = $this->_debitlines_mod->sn();
            }
//  echo $d_sn;exit;
            $d_sn_arr[] = $d_sn; //=====  放到一个数组里面做校验用  =====
            $sn_arr['d_sn'] = $d_sn;
             
            //=====  激活码  =====
            $active_sn = $this->_debitlines_mod->activ_sn();
            while (in_array($active_sn, $active_sn_arr))
            {
                $active_sn = $this->_debitlines_mod->activ_sn();
            }
            $active_sn_arr[] = $active_sn;
            $sn_arr['active_sn'] = $active_sn;
            
            $lines_arr[] = $sn_arr;
        }
// print_exit($lines_arr);
        if ($lines_arr) 
        {
            foreach ($lines_arr as $key => $value) 
            {
                $res = $this->_debitlines_mod->add($value);
            }
        }
        //$res = $this->_debitlines_mod->add($lines_arr);
        if (!$res) 
        {
            $this->show_warning('ERROR');
            return;
        }

        $this->show_warning("生成成功",
            '导出excel文件',    "index.php?app=debit_line&act=export&id=$line_id",
            '查看详情',    "index.php?app=debit_line&act=log&cate={$cate}"
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
