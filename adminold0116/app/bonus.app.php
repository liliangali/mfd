<?php

/* 会员控制器 */
class BonusApp extends BackendApp
{
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->mod =& m('bonus');
        $this->member_lv = array(
            '100' => '所有创业者',
            '2' => "&nbsp;麦富迪达人",
            '3' => '&nbsp;麦富迪精英',
            '4' => '&nbsp;麦富迪领袖',
            '5' => '&nbsp;麦富迪内部人',
            '1' => '消费者',
        );
        $this->type = array(0=>'金额',1=>'麦富迪币');
        $this->assign('obj_type',$this->member_lv);
        $this->assign('type',$this->type);
    }

    /**
     *列表
     *@author liang.li <1184820705@qq.com>
     *@2015年8月17日
     */
    function index()
    {
        $bonus_log_mod = m('bonuslog');
    	$conditions = "";
        $page = $this->_get_page();
        $list = $this->mod->find(array(
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        if ($list) 
        {
            foreach ($list as $key => $value) 
            {
                $log = $bonus_log_mod->get(array(
                    'conditions' => "bonus_id = {$value['id']} ",
                    'fields'    => "count(*) as num,sum(cash_money) as total_money",
                    'index_key' => " ",
                ));
                $list[$key]['num'] = $log['num'];
                $list[$key]['total_money'] = $log['total_money'];
                $list[$key]['type'] = $this->type[$value['type']];
                $list[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
                $list[$key]['expire'] = date("Y-m-d H:i:s",$value['expire']);
            }
        }
        
        $this->assign('list', $list);
        $page['item_count'] = $this->mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('bonus/index.html');
    }
    
    /**
    *红包详情
    *@author liang.li <1184820705@qq.com>
    *@2015年8月17日
    */
    function info()
    {
        
        $id = $_GET['id'];
        $bonus_log_mod = m('bonuslog');
        $member_mod = m('member');
    	$conditions = "bonus_id = $id";
        $page = $this->_get_page();
        $list = $bonus_log_mod->find(array(
            'conditions' => '1=1 AND ' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        
        $user_ids = implode(",", i_array_column($list, "user_id"));
        $user_list = $member_mod->find(array(
            'conditions' => "user_id IN ($user_ids)",
        ));
        
        if ($list) 
        {
            foreach ($list as $key => $value) 
            {
                $list[$key]['type'] = $this->type[$value['type']];
                $list[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
                $list[$key]['expire'] = date("Y-m-d H:i:s",$value['expire']);
                $list[$key]['user_name'] = $user_list[$value['user_id']]['user_name'];
            }
        }
        
        $this->assign('list', $list);
        $page['item_count'] = $bonus_log_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('bonus/info.html');
    }
    
    /**
    *检查用户是否存在
    *@author liang.li <1184820705@qq.com>
    *@2015年8月14日
    */
    function checkm() 
    {
        $phone = $_POST['phone'];
        $m_mod = m('member');
        $info = $m_mod->get(array(
            'conditions' => " (user_name = $phone OR phone_mob=$phone) AND serve_type=1",
        ));
        if (!$info) 
        {
            $this->json_error();
            return;
        }
        $this->json_result($info['user_id']);
        
        
    }
    
    /**
    *添加
    *@author liang.li <1184820705@qq.com>
    *@2015年8月13日
    */
    function add() 
    {
        
        if(!IS_POST)
        {
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js',
                'style'  => 'res:style/jqtreetable.css'
            ));
            $this->display("bonus/form.html");
        }
        else 
        {
            
          $data['obj_type'] = $_POST['obj_type'];
          if ($data['obj_type'] == 0)
          {
              if (!$_POST['obj_val']) 
              {
                  $this->show_warning('缺少必填参数');
                  return;
              }
              $data['obj_val'] = implode(',', $_POST['obj_val']);
          }
          //=====  参数验证  =====
          if (!$_POST['money'] || !$_POST['response_text'] || !$_POST['expire'] || !$_POST['remark'] || !$_POST['name']) 
          {
              $this->show_warning('缺少必填参数');
              return ;
          }
          $data['name'] = $_POST['name'];
          $data['money'] = $_POST['money'];
          $data['money_type'] = $_POST['money_type'];
          $data['type'] = $_POST['type'];
          $data['response_type'] = $_POST['response_type'];
          $data['response_text'] = $_POST['response_text'];
          $data['expire'] = strtotime($_POST['expire']);
          $data['remark'] = $_POST['remark'];
          $data['add_time'] = time();
          $data['admin_name'] = $this->visitor->info['user_name'];
          
          //=====  分发红包  =====
          $transaction = $this->mod->beginTransaction();
          $id = $this->mod->add($data);
          if (!$id)
          {
              $this->mod->rollback();
              $this->show_warning('bonus表添加失败');
              return;
          }
          $res = $this->formatBonus($data,$id);
          if (!$res['code']) 
          {
              $this->mod->rollback();
              $this->show_warning($res['msg']);
              return;
          }
          $this->mod->commit($transaction);
          $this->show_message('发送红包成功',
              '返回红包列表', 'index.php?app=bonus&amp;act=index',
              '继续发红包', 'index.php?app=bonus&amp;act=add'
          );
        }
    }
    
    /**
    *获得红包
    *@author liang.li <1184820705@qq.com>
    *@2015年8月17日
    */
    function formatBonus($data,$id) 
    {
        $earr = array('code' => 0,'msg'=>'ERROR');
        $sarr = array('code' => 1,'msg'=>'');
        $member_mod = m('member');
        $bonus_log_mod = m('bonuslog');
        $_data['bonus_id'] = $id;
        $_data['name'] = $data['name'];
        $_data['add_time'] = $data['add_time'];
        $_data['type'] = $data['type'];
        $_data['expire'] = $data['expire'];
        $response_type = $data['response_type'];
//      echo $response_type;exit;
        $response_text = $data['response_text'];
        
        if ($data['money_type'] == 0) //=====  固定金额 =====
        {
            if ($data['obj_type'] == 0) //=====  指定会员   =====
            {
                $obj_val = $data['obj_val'];
                $member_list = $member_mod->find(array(
                    'conditions' => "user_id IN ($obj_val)",
                    'fields'     => "user_id,phone_mob",
                ));
                
            }
            elseif ($data['obj_type'] > 0) //=====  指定会员类型  =====
            {
                $m_lv = $data['obj_type'];
                $conditions = "";
                if ($m_lv == 100) 
                {
                    $conditions = "member_lv_id >= 2 ";
                }
                elseif (in_array($m_lv, array(1,2,3,4)))
                {
                    $conditions = "member_lv_id = $m_lv ";
                }
                
                
                $member_list = $member_mod->find(array(
                    'conditions' => $conditions,
                ));
                
            }
            
            foreach ($member_list as $key => $value)
            {
                $_data['user_id'] = $value['user_id'];
                $_data['cash_money'] = $data['money'];
                if (!$bonus_log_mod->add($_data))
                {
                    $earr['msg'] = "bonus_log添加失败";
                    return $earr;
                }
                
                //=====  发送通知  =====
                if ($response_type) 
                {
                    $res = SendSms($value['phone_mob'], $response_text);
                }
                else 
                {
                    sendSystem($value['user_id'], 14, "系统红包", $response_text);
                }
            }
             
        }
        else  //=====  随机金额  =====
        {
            if ($data['obj_type'] == 0) //=====  指定会员   =====
            {
                $obj_val = $data['obj_val'];
//       dump($data);
                $member_list = $member_mod->find(array(
                    'conditions' => "user_id IN ($obj_val)",
                    'fields'     => "user_id,phone_mob",
                    'index_key' => '',
                ));
            }
            elseif ($data['obj_type'] > 0) //=====  指定会员类型  =====
            {
                $m_lv = $data['obj_type'];
                $conditions = "";
                if ($m_lv == 100) 
                {
                    $conditions = "member_lv_id >= 2 ";
                }
                elseif (in_array($m_lv, array(1,2,3,4,5)))
                {
                    $conditions = "member_lv_id = $m_lv ";
                }
                
                $member_list = $member_mod->find(array(
                    'conditions' => $conditions,
                    'fields'     => "user_id,phone_mob",
                    'index_key' => '',
                ));
                
            }
            $total = $data['money']; //=====  红包总额  =====
            $num = count($member_list);//=====  要领取红包的总人数  =====
            $min=0.01;//=====  每个人最少能收到0.01元  =====
            if ($num * $min > $total) //=====  总人数*最小额度 必须小于 红包总额  =====
            {
                $earr['msg'] = '领取红包总人数*最小额度 必须小于 红包总额';
                return $earr;
            }
            $bonus_data = array(); //=====  红包集合  =====
            for ($i=1;$i<$num;$i++)
            {
                $safe_total = ($total-($num-$i)*$min)/($num-$i);//随机安全上限
                $money = mt_rand($min*100,$safe_total*100)/100;
                $total = $total-$money;
                $bonus_data[] = $money;
            // echo '第'.$i.'个红包：'.$money.' 元，余额：'.$total.' 元 <br/>';
            }
            $bonus_data[] = $total;
//       dump($member_list);
            foreach ($member_list as $key => $value)
            {
                $_data['user_id'] = $value['user_id'];
                $_data['cash_money'] = $bonus_data[$key];
                if (!$bonus_log_mod->add($_data))
                {
                    $earr['msg'] = "bonus_log添加失败";
                    return $earr;
                }
                
                //=====  发送通知  =====
                if ($response_type)
                {
                    $res = SendSms1($value['phone_mob'], $response_text);
                }
                else
                {
                    sendSystem($value['user_id'], 14, "系统红包", $response_text);
                }
            }
        }
        
        return $sarr;
    }
    
    
    
}

?>
