<?php


class Special_codeModel extends BaseModel
{
    var $table  = 'special_code';
    var $prikey = 'id';
    var $_name  = 's_code';

    var $_relation = array(
        'belongs_to_member' => array(
            'model'             => 'member',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'user_id',
            'reverse'           => 'has_special',
        ),
    );

    //2维
    function get_2w_info($model,$fields='',$condition=''){
        $lv_mod =& m($model);
        $arr = array(
            'fields' => $fields,
        );

//        if($model == 'memberlv'){
//          $arr['conditions'] ="lv_type='supplier'";
//        }
        if($condition !=''){
            $arr['conditions'] =$condition;
        }
        $re = $this->find($arr);
        $result = i_array_column($re,$fields);
        return $result;
    }


    /**
     * 特权码
     */
    function sn($cates,$cate,$type){
        //拼音首字母+发放形式+10位随机数（字母和数字的组合）


        $sn =$cates[$cate]['sign'].$type.createNonceStr(10);

        $info = $this->get("cate=".$cate." and sn='{$sn}'");
        //$sn是不是唯一，不是重新生成
        if(!empty($info))$sn =$this->sn($cates,$cate,$type);
        return $sn;
    }


    function sn1($cates,$cate,$type,$num){
        //拼音首字母+发放形式+10位随机数（字母和数字的组合）      C+C+10位随机数
        $sn_arr =array();
        for($i=0;$i<$num;$i++){
            $sn_arr[] =$cates[$cate]['sign'].$type.createNonceStr(10);
        }

        $exists_arr = $this->find(db_create_in($sn_arr, 'sn') . " and cate=".$cate);
        $exists_sn_arr = i_array_column($exists_arr,'sn');
        return  array_values(array_diff($sn_arr,$exists_sn_arr));
    }

    /**
     * 线上发放
     */
    function submit($ids,$cates,$work_num,$cate,$suoxie,$log_id,$expire_time,$tongzhi,$content){

        $_lv_mod =& m('memberlv');
        $member_mod = & m('member');
        $user_info = $member_mod->find(db_create_in($ids, 'user_id'));

        $sn_arr =$this->sn1($cates,$cate,$suoxie,count($ids));
        foreach($ids as $k=>$v){
            //生成特权码 --线上
//            $sn =$this->sn($cates,$cate,$suoxie);
            $data1 = array(
                'sn'=> $sn_arr[$k],
                'cate'=> $cate, //生成一批干嘛的码
                'log_id'=>$log_id,
                'work_num'=>$work_num,
                'to_id'=> $user_info[$v]['user_id'],
                'user_name'=>$user_info{$v}['user_name'],
//                'to_time'=>gmtime(),

                'expire_time'=> $expire_time,
                'add_time'=> gmtime(),
            );
            $ret = $this->add($data1);
            if(!$ret)return false;


            //信息推送
            if($user_info[$v]['phone_mob'] !='' && $tongzhi==0){
                $rs = SendSms($user_info{$v}['phone_mob'], $content.'您的特权码是'.$sn.',请勿重复输入。');
                if(!$rs) return false;
            }

            //站内信+信鸽
            if($tongzhi ==1){
                //12是会员等级提升
                $ret=  sendSystem($v, 12, '恭喜，您获得特权码', $content.'您的特权码是'.$sn.',请勿重复输入。') ;
                if(!$ret) return false;
            }

        }

        return true;
    }







}

?>