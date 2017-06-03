<?php
namespace Cyteam\Member;

class Member
{
    public static $user_id = 0;
    
    function __construct($param = []){
        
    }
    
    public function getUserInfo($user_id = 0) {
        return isset($_SESSION['user_info']) ? $_SESSION['user_info']: [];
    }

    public function debit($user_id,$user_name) {
        $setting = include  ROOT_PATH.'/data/settings.inc.php';
//        $data['forder'] = $_POST['forder'];
//        $data['forder_start_time'] = ($_POST['forder_start_time']);
//        $data['forder_end_time'] = ($_POST['forder_end_time']);
//        $data['forder_money'] = $_POST['forder_money'];
//        $data['forder_name'] = $_POST['forder_name'];
//        $data['forder_order_money'] = $_POST['forder_order_money'];
//        $data['forder_days'] = $_POST['forder_days'];
//        $data['forder_category'] = $_POST['forder_category'];
//echo '<pre>';print_r($setting);exit;

          if($setting['forder'] == 0 || !$user_id || !$user_name)
          {
              return;
          }
          $time = time();
          if($time >= $setting['forder_start_time'] && $time <= $setting['forder_end_time'])
          {
              if($setting['forder_money'] <= 0)
              {
                  return;
              }
              $voucher = m('voucher');
              $data['name'] = $setting['forder_name'];
              $data['money'] = $setting['forder_money'];
              $data['create_time'] = time();
              $data['start_time'] = time();
              $data['end_time'] = time() + $setting['forder_days']*24*60*60;
              $data['binding_time'] = time();
              $data['binding_user_id'] = $user_id;
              $data['binding_username'] = $user_name;
              $data['category'] = $setting['forder_category'];
              $data['source'] = 2;
              $data['order_money'] = $setting['forder_order_money'];
              $voucher->add($data);
          }
          
        



    }
    
    public static function getUserId (){
        return self::$user_id ? self::$user_id : (isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : 0);
    }
    
    public static function setUserId ($user_id = 0){
        return self::$user_id = $user_id;
    }
}