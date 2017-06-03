<?php

/**
 * 后台批量发送信息自动同步
 **/

chdir(dirname(__FILE__));// cd 到php脚本所在的目录

error_reporting(0);
$end = "\r\n";
echo "start:time".date("Y-m-d H:i:s").$end;
$start_time = time();
include_once 'order_func.php';
initDb();



$sql = "select * from cf_sys_push where send_member!='' and send_time=0";
$data_list = getAlls($sql);


if(!$data_list){
    exit(" no data process...".$end);
}


echo " data count:".count($data_list).$end;


$res = array();
foreach($data_list as $k=>$v){

    echo '('.$v['title'].'=>'.$v['content'].')'.$end;

	$conditions ="serve_type=1 ";
    $send_members =json_decode($v['send_member'],1);

    if(empty($send_members)){
       echo  "no send_member".$end;
       exit();
    }

    $send_members_str = implode(',',$send_members);
    $conditions .=" AND user_id IN ({$send_members_str})";

	$sql_member = "select user_id, user_name, user_token from cf_member where ".$conditions."group by user_id";
	$member_arr = getAlls($sql_member);
    $push_id =$v['id'];

    $data = $member_list =array();
    if(empty($member_arr)){
        $data['send_member'] ='';
        $sql = update($data, " id= $push_id",'cf_sys_push');
        mysql_query($sql);
        echo 'message:'.$push_id.':delete'.$send_members_str.$end;
        exit();
    }else{
        foreach($member_arr as $mk=>$mv){
            $member_list[$mv['user_id']]=$member_arr[$mk];
        }



        $data =$send_member_token = $send_member_ids =array();





        //模拟
    //    foreach($member_list as $kkk=>$vvv){
    //        //过滤已经发送的用户   记录日志信息
    //        $member_list[$kkk] = array(
    //            "user_id"=>'3285',
    //            "user_name"=>'麦富迪初体验',
    //            "user_token"=>'dfffb5e177403b5e81e59e2eb65efeb4',
    //        );
    //    }



        $i=0;
        $send_member_token = $send_member_ids =array();

        if(!empty($member_list)){
            foreach($member_list as $kk=>$vv){
                $i++;
                if($i>100){
                    break;
                }

                $send_member_token[] = $member_list[$kk]['user_token'];
                $send_member_ids[] = $member_list[$kk]['user_id'];
            }
        }

        //   模拟
        //    foreach($send_members as $kkkk=>$vvvv){
        //        $send_members[$kkkk]='3285';
        //    }

        //发送信鸽信息
        $ret =0;
        if(!empty($send_member_token) && !empty($send_member_ids)){
            $ret = doCreatePush($send_member_token, $send_member_ids, 13, $v['title'], $v['content'], $v['send_type'],$v['id']);
        }

        if(!$ret){
            echo "xinge failed".$end;
            exit();
        }
        $data =array();
        $diff_ids_arr = array_diff($send_members,$send_member_ids);



        if(!empty($diff_ids_arr)){
            $data['send_member'] =json_encode($diff_ids_arr);

        }else{
            //记录发送完毕的时间
            $data['send_member'] ='';
            $data['send_time'] = time();
        }



        $sql = update($data, " id= $push_id",'cf_sys_push');
        mysql_query($sql);
        echo 'message:'.$push_id.':success!!!!'.$end;






    }



}



/**
 * 注册批量发送任务
 *
 * @param int $status
 * @param string $msg
 * @param mixed $data
 * @param string $dialog
 * @param int $type     1.交易| 2.评论晒单| 3.评论订单|4.顾客发需求|5.实名认证|13订单发货|12用户等级提升|14红包相关|15抵用券相关|16提现|17返修
 */
function doCreatePush($send_member_token, $send_member_ids, $type, $title_mes, $content_mes, $send_type = "all",$push_id) {


    //发送信鸽推送
	include_once( '../includes/xinge/xinggemeg.php');
	$push = new XingeMeg();
	$result = $push->doCreateMultipush($send_member_token, $title_mes, $content_mes, array('url_type' => 'system', 'location_id' => 0), $send_type);


	if ($result){

		//发送成功。循环插入数据库
		foreach($send_member_ids as $val ) {
			$arr = array(
					'from_user_id'  => 1,
					'to_user_id'    => $val,
					'from_nickname' => 'system',
					'type'          => $type,
					'title'         => mysql_real_escape_string($title_mes),
					'content'       => mysql_real_escape_string($content_mes),
					'add_time'      => time(),
					'location_url'   =>' ',
                    'push_id'=>$push_id
			);
            $sql = add('cf_user_message', $arr);
            $res =mysql_query($sql);
		}




		return $res;
	}
	return false;

}




function add($table,$data)
{
    $str_fields = "(";
    $str_value = "(";
    if ($data)
    {
        foreach ($data as $key => $value)
        {
            $str_fields .= "`".$key."`".",";
            $str_value  .= "'$value'".",";
        }
        $str_fields = trim($str_fields,',');
        $str_value  = trim($str_value,",");
        $str_fields = $str_fields.")";
        $str_value  = $str_value.")";
        $str = $str_fields." VALUES ".$str_value;
        $sql = "INSERT INTO `$table` ".$str;
        return $sql;
    }
    return '';
}

function dump($arr)
{
	echo '<pre>';
	var_dump($arr);
	exit;
}