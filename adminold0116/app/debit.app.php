<?php

/**
*抵用券管理
*@author liang.li <1184820705@qq.com>
*@2015年8月24日
*/
class DebitApp extends BackendApp
{
    function __construct()
    {
        $this->ApplyApp();
    }

    function ApplyApp()
    {
        parent::__construct();
        $this->mod =& m('debitt');
        $this->member_lv = array(
            '100' => '所有创业者',
            '2' => "&nbsp;麦富迪达人",
            '3' => '&nbsp;麦富迪精英',
            '4' => '&nbsp;麦富迪领袖',
            '5' => '&nbsp;麦富迪内部人',
            '1' => '消费者',
        );
        $this->type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
        );
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
        $debit_mod = m('debit');
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
                $log = $debit_mod->get(array(
                    'conditions' => "debit_t_id = {$value['id']} ",
                    'fields'    => "count(*) as num,sum(money) as total_money",
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

        $this->display('debit/index.html');
    }
    
    /**
    *红包详情
    *@author liang.li <1184820705@qq.com>
    *@2015年8月17日
    */
    function info()
    {
        
        $id = $_GET['id'];
        $debit_mod = m('debit');
        $member_mod = m('member');
        
        $debit_t_info = $this->mod->get_info($id);
    	$conditions = "debit_t_id = $id";
        $page = $this->_get_page();
        $list = $debit_mod->find(array(
            'conditions' => '1=1 AND ' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        
        $user_id_arr = array_merge(i_array_column($list, "from_uid"),i_array_column($list, "user_id"));
        $user_ids = implode(",", $user_id_arr);
        $user_list = array();
        if ($user_ids) 
        {
            $user_list = $member_mod->find(array(
                'conditions' => "user_id IN ($user_ids)",
            ));
        }
        if ($list) 
        {
            foreach ($list as $key => $value) 
            {
                $list[$key]['type'] = $this->type[$value['cate']];
                $list[$key]['add_time'] = date("Y-m-d H:i:s",$value['add_time']);
                $list[$key]['expire'] = date("Y-m-d H:i:s",$value['expire_time']);
                $list[$key]['user_name'] = $user_list[$value['from_uid']]['user_name'];
                
            }
        }
        $this->assign('list', $list);
        $page['item_count'] = $debit_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('debit/info.html');
    }
    
    /**
    *检查用户是否存在
    *@author liang.li <1184820705@qq.com>
    *@2015年8月14日
    */
    function checkm() 
    {
        $phone = $_POST['phone'];
        $cate  = $_POST['cate'];
        $m_mod = m('member');
        $conditions = " (user_name = $phone OR phone_mob=$phone) AND serve_type=1";
        if ($cate == 1) //=====  转赠券只能是创业者获得   =====
        {
            $conditions .=  " AND member_lv_id > 1";
        }
        
        $info = $m_mod->get(array(
            'conditions' => $conditions,
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
            $this->display("debit/form.html");
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
          if (!$_POST['money'] || !$_POST['response_text'] || !$_POST['expire'] || !$_POST['remark'] ) 
          {
              $this->show_warning('缺少必填参数');
              return ;
          }
          $data['name'] = $_POST['name'];
          $data['money'] = $_POST['money'];
          $data['cate'] = $_POST['cate'];
          $data['type'] = $_POST['type'];
          $data['response_type'] = $_POST['response_type'];
          $data['response_text'] = $_POST['response_text'];
          $data['expire'] = strtotime($_POST['expire']) + 86399; //=====  到一天的末尾才算结束  =====
          $data['remark'] = $_POST['remark'];
          $data['add_time'] = time();
          $data['admin_name'] = $this->visitor->info['user_name'];
          $data['admin_id'] = $this->visitor->info['user_id'];
          //=====  分发红包  =====
          $transaction = $this->mod->beginTransaction();
          $id = $this->mod->add($data);
          if (!$id)
          {
              $this->mod->rollback();
              $this->show_warning('debit_t表添加失败');
              return;
          }
          $res = $this->formatDebit($data,$id);
          if (!$res['code']) 
          {
              $this->mod->rollback();
              $this->show_warning($res['msg']);
              return;
          }
          $this->mod->commit($transaction);
          $this->show_message('发送抵用券',
              '返回列表', 'index.php?app=debit&amp;act=index',
              '继续发券', 'index.php?app=debit&amp;act=add'
          );
        }
    }
    
    /**
    *获得红包
    *@author liang.li <1184820705@qq.com>
    *@2015年8月17日
    */
    function formatDebit($data,$id) 
    {
        $earr = array('code' => 0,'msg'=>'ERROR');
        $sarr = array('code' => 1,'msg'=>'');
        $member_mod = m('member');
        $debit_mod = m('debit');
        $_data['cate'] = $data['type'];
        $_data['debit_t_id'] = $id;
        $_data['add_time'] = $data['add_time'];
        $_data['debit_name'] = $data['name'];
        $_data['expire_time'] = $data['expire'];
        $_data['add_time'] = time();
        $_data['source'] = "admin_add";
        $response_type = $data['response_type'];
        $response_text = $data['response_text'];
        
        $conditions = "1=1  ";
        if ($data['obj_type'] == 0) //=====  指定会员   =====
        {
            $obj_val = $data['obj_val'];
            $conditions .= " AND user_id IN ($obj_val)";
        }
        elseif ($data['obj_type'] > 0) //=====  指定会员类型  =====
        {
            $m_lv = $data['obj_type'];
            if ($m_lv == 100) 
            {
                $conditions .= " AND  member_lv_id >= 2 ";
            }
            elseif (in_array($m_lv, array(1,2,3,4,5)))
            {
                $conditions .= " AND member_lv_id = $m_lv ";
            }
        }
        if ($data['cate'] == 1)//=====  转赠券只能创业者拥有  ===== 
        {
            $conditions .= " AND member_lv_id >= 2 ";
        }
        else //=====  普通券只能消费者拥有  ===== 
        {
            $conditions .= " AND member_lv_id = 1 ";
        }
//       dump($conditions);
//         $conditions .= " AND member_lv_id <= 4"; //=====  目前创业者等级只有4以上  =====
        $member_list = $member_mod->find(array(
            'conditions' => $conditions,
        ));
        
        foreach ($member_list as $key => $value)
        {
            if ($data['cate'] == 1)//=====  转赠券只能创业者拥有  =====
            {
                $_data['from_uid'] = $value['user_id'];
            }
            else 
            {
                $_data['user_id'] = $value['user_id'];
            }
            
           
            $_data['money'] = $data['money'];
            $_data['debit_sn'] = time().$this->createNonceStr(8);
            if (!$debit_mod->add($_data))
            {
                $earr['msg'] = "debit添加失败";
                return $earr;
            }
            
            //=====  发送通知  =====
            if ($response_type) 
            {
                $res = SendSms($value['phone_mob'], $response_text);
            }
            else 
            {
                sendSystem($value['user_id'], 15, "抵用券", $response_text);
            }
       }
        
        return $sarr;
    }
    
    //随机字符串
    function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
    
}

?>
