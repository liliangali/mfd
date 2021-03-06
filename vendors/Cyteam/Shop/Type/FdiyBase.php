<?php
namespace Cyteam\Shop\Type;

use Cyteam\Shop\Type\Base;
use Cyteam\Db\Led;

/**
  * 普通商品
  */
class FdiyBase extends Base {
    
    //base64格式图片 存储 路径定义死
    function saveImg($imgstr,$clothingID,$is_dir=0){

        $imgtype = ".png";
        if(strpos($imgstr,"data:image/jpeg") !== false)
        {
            $imgtype = ".jpeg";
        }
        $imgstr = str_replace("data:image/jpeg;base64,", "", $imgstr);
        $imgstr = str_replace("data:image/png;base64,", "",$imgstr);

        $path = ROOT_PATH.'/upload/images/diy/';
        $img = base64_decode($imgstr);

        /* 品类id＋时间戳 md5 */
        $t = md5($clothingID.time()).$imgtype;
        $save = file_put_contents($path.$t,$img);

        if ($save){
            return '/upload/images/diy/'.$t;
        }else{
            return false;
        }
    }

    //初始化基础属性ID
    public function getParams()
    {
        $arr = [
          'quanzhong' => 21,
          'shengzhang' => 34,
           'gongxiao' => 1,
            'kouwei' => 6,
            'baozhuang' => 16,
            'guige' => 11
        ];
        return $arr;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Cyteam\Shop\Type\Base::_formatPost()
     */
      public function _formatPost($post = array()){
          $res = [];
          $params   = isset($post['params']) ? json_decode( stripslashes_deep($post['params']) ,true) : array();  //由于入口文件做了字符转义  所以需要替换一下
          //检验影响价格的必传参数   //犬种&&犬期，规格
          if (!$params[34]) 
          {
              $this->_error("请选择犬期!");
              return false;
          }
          if (!$params[11])
          {
              $this->_error("请选择规格!");
              return false;
          }
          if (!$params[21])
          {
              $this->_error("请选择犬种!");
              return false;
          }

          
          if (!$params[1])
          {
              $this->_error("请选择功效!");
              return false;
          }
          $gx = explode("|",trim($params[1],'"'));
          if(count($gx) > 3)
          {
              $this->_error("功效最多选择3种!");
              return false;
          }
          
          if (!$params[6])
          {
              $this->_error("请选择口味!");
              return false;
          }
          
          if (!$params[16])
          {
              $this->_error("请选择包装!");
              return false;
          }

	      $price_arr = $this->fmoatPrice($params[21],$params[34],$params[16],$params[11],$params[1],1);
          
          if($post['time_id'])
          {
              $wpost['is_feed'] = 1;
              $wpost['cat_id'] = $params[21];
              $wpost['age_id'] = $params[34];
              $weight_arr = $this->fmoatFeed($wpost);
              if(isset($weight_arr['time_list']))
              {
                  foreach ((array)$weight_arr['time_list'] as $index => $item)
                  {
                      if($item['time_id'] == $post['time_id']) 
                      {
                          if($post['weight'] < $item['wt_min'])
                          {
                              $post['weight'] = $item['wt_min'];
                          }
                          if($post['weight'] > $item['wt_max'])
                          {
                              $post['weight'] = $item['wt_max'];
                          }
                      }
                  }
              }
          }

     
	      if ($price_arr === false)
	      {
	          $this->_error("此组合无法匹配价格");
	          return false;
	      }
	      if($post['is_try'])//试吃订单必须只能是一单
	      {
              $res['quantity'] = 1;
              //检查是有有有效的订单或者购物车数据
              $tres = $this->_checkTry($_SESSION['user_info']['user_id']);
              if(!$tres)
              {
                  $this->_error("亲，0元定制的活动只能体验1次，如果未购买过本次活动产品，麻烦在我的->用户中心->待付款中查看是否已经有本次活动的订单了");
                  return false;
              }
	      }
          $res['goods_id'] = isset($post['gid']) ? ($post['gid']) : 0;
          $res['product_id'] = isset($post['pid']) ? trim($post['pid']) : 0;
          $res['quantity'] = isset($post['num']) ? intval($post['num']) : 1;
          $res['is_try'] = isset($post['is_try']) ? intval($post['is_try']) : 0;
          $res['rec_id']   = isset($post['rec_id']) ? intval($post['rec_id']) : 0;
          $res['type'] = 'fdiy';
          if (isset($post['fpic']) && $post['fpic']){
              $res['fpic'] = $this->saveImg($post['fpic'],$post['cid']);
          }
          elseif (isset($post['img_dir']) && $post['img_dir'])
          {
              $res['fpic'] = $post['img_dir'];
          }


          $res['btype']    = 'fdiy';
          $fpic = $post['fpic'];
          $param = array();
          if ($params)
          {
              $itmes = array();
              foreach ($params as $key => $value) //=====  格式化参数于diy保持一致  =====
              {
                  if ($value)
                  {
                      $value = trim($value,'"');
                      $value2_arr = explode("|", $value);
                      foreach ($value2_arr as $key1 => $value1)
                      {
                          if (count($value2_arr) == 2) //=====   客户指定  =====
                          {
                              $str = $key.":".$value2_arr[0].",".$value2_arr[1];
                          }
                          elseif(count($value2_arr) == 1)
                          {
                              $str = $key.":".$value2_arr[0];
                          }
                          elseif(count($value2_arr) == 3)
                          {
                              $str = $key.":".$value2_arr[0].",".$value2_arr[1].",".$value2_arr[2];
                          }
                          if ($value1)
                          {
                              $value1 = str_replace('"', '',$value1);
                              $itmes[] = intval($value1);
                          }
                      }
                  }
                  $param[$key] = $str;
              }
          }
          $res['params'] = $param;
          $res['items'] = $itmes;
          $res['image'] = $fpic;
          $res['cloth'] = $post['cid'];
          $res['size']   = isset($post['params']) ? json_decode( stripslashes_deep($post['size']) ,true) : array();
          $res['dog_name'] = isset($post['dog_name']) ? $post['dog_name'] : '';
          $res['dog_date'] = isset($post['dog_date']) ? $post['dog_date'] : '';
          $res['dog_desc'] = isset($post['dog_desc']) ? $post['dog_desc'] : '';
          $res['body_condition'] = isset($post['body_condition']) ? $post['body_condition'] : 0;
          $res['run_time'] = isset($post['run_time']) ? $post['run_time'] : 0;
          $res['weight'] = isset($post['weight']) ? $post['weight'] : 0;
          $res['time_id'] = isset($post['time_id']) ? $post['time_id'] : 0;
          $res['dog_nums'] = isset($post['dog_nums']) ? $post['dog_nums'] : 0;

          return $res;
      }
      
      /**
       * (non-PHPdoc)
       * @see \Cyteam\Shop\Type\Base::add()
       */
    function add($post = array())
    {
        $time = gmtime();
        $sizes = array();
        $dis_ident = $first = $ident = '';

        $cartM = m("cart");
        if(!$post['params'] || count($post['params']) <1){
            $this->_error("没有商品!");
            return false;
        }

        
        $dis_id = 0;
        if(count($post['params']) > 1 ){
            $dis_ident = $this->genIdent($post['user_id']);
        }else{
            $ident = $this->genIdent($post['user_id']);
            $dis_ident = $ident;
            $cid = key($post['params']);
        }

        $data = array(
                'user_id'      => $post['user_id'],
                'dis_ident'    => $dis_ident ? $dis_ident : '',
                'suit_id'      => 0,
                'goods_id'     => 0,
                'type'         => 'fdiy',
                'quantity'     => $post['quantity'],
                'items'     => implode(',', array_unique($post['items'])),
                'time'         => $time,
                'first'        => $first,
                'is_try'        => $post['is_try'],
                'figure'       => '',
                'source_from'  =>  $post['source_from'],
                'session_id' => SESS_ID,
            
                'dog_name' => isset($post['dog_name']) ? $post['dog_name'] : '',
                'dog_date' => isset($post['dog_date']) ? $post['dog_date'] : '',
                'dog_desc' => isset($post['dog_desc']) ? $post['dog_desc'] : '',
            'body_condition' => isset($post['body_condition']) ? $post['body_condition'] : 0,
            'run_time' => isset($post['run_time']) ? $post['run_time'] : 0,
            'weight' => isset($post['weight']) ? $post['weight'] : 0,
            'time_id' => isset($post['time_id']) ? $post['time_id'] : 0,
            'dog_nums' => isset($post['dog_nums']) ? $post['dog_nums'] : 0,
        );
      
            //=====  去包装ID 拼接图片  =====
            $diy_img  = '/diy/images/cptu.jpg';
            $params = $post['params'];
            if (isset($params[16]))
            {
                $bz_id = trim(explode(":",$params[16])[1],'"');
                $diy_img = "/diy/images/dzjs_".$bz_id.".png";;
            }

            $rData = array(
                    'ident'  => $dis_ident,
                    'dis_ident' => $dis_ident,
                    'cloth'  => $post['cloth'],
                    'fabric' => '',
                    'lining' => '',
                    'button' => '',
                    'style'  => '',
                    'embs'   => '',
                    'syline' => '',
                    'params' => serialize($post['params']),
                    'size'   => 'diy',
                    'image'  => $diy_img,
                    'style'  => $post['fpic'],      //包装图片
            );
            
            $aSave[] = array_merge($data,$rData);

        
            $res = $cartM->add($aSave);
            if(!$res)
            {
                $this->_error("add_to_cart_error");
                return false;
            }
        
        return $res;
    }

    public function _checkTry($user_id)
    {
        if(!$user_id)
        {
            return false;
        }
        $cartM = m("cartc");
        $orderM = m('order');
        $memberM = m('member');
        $cart_info = $cartM->get("user_id=$user_id AND is_try=1");
        $order_info = $orderM->get("user_id=$user_id AND is_try=1 AND status !=0");
        $member_info = $memberM->get_info($user_id);
        if(!$member_info['openid'])//只允许微信用户参加试吃活动
        {
            return false;
        }
        if($cart_info || $order_info)
        {
            return false;
        }
        return true;
    }
    
    //根据犬期ID和犬种ID计算功效编码
    function fmoatCode($params_arr)
    {
        $fbcategory = &m("fbcategory");
        $mod = &m("basematerial");
        $material_mod = m('material');
        $effect_age_price_mod = m("effectageprice");

        if (!isset($params_arr[21]) || !isset($params_arr[34]) || !isset($params_arr[1]) || !isset($params_arr[6])) //犬种
        {
            return false;
        }
        //口味
        $kouwei_arr = explode(":", $params_arr[6]);
        $kouwei_id = $kouwei_arr[1];
       
        $quanzhong_arr = explode(":",$params_arr[21]);
        $age_arr = explode(":",$params_arr[34]);
        
        $quanzhong_id = $quanzhong_arr[1];
        $age_id = $age_arr[1];
        $fblist1 = $fbcategory->get_info($quanzhong_id);
        $fblist2 = $fbcategory->get_info($age_id);
        $quanzhong_info = $fblist1;
        $quanqi_info = $fblist2;
        if ($quanzhong_info['parent_id'] == 60) //杂毛
        {
            $pcate_id = $quanzhong_info['cate_id'];
        }
        else
        {
            $pcate_id = $quanzhong_info['parent_id'];
        }
        $f_price = 0;
        $b_price = 0;
        if(!$pcate_id){
            return false;
        }
        $info = $mod->get("cate_id=$pcate_id AND age_id=$age_id");
//        echo '<pre>';print_r($info);exit;

        if (!$info) //基料编码不存在
        {
           return false;
        }
        else
        {
            $jiliao_code = $info['base_code'];
        }
       
        $ga_arr = explode(":", $params_arr[1]);
        $gongxiao_arr = explode(",", $ga_arr[1]);
        $gongxiao_num = count($gongxiao_arr);
//   print_exit($params_arr);
        $is_gongxiao = 0;
        if (isset($gongxiao_arr[0])) 
        {
            $a0 = trim($gongxiao_arr[0],"\"");
            if($a0 == 107)
            {
                $is_gongxiao = 1;
            }
            $einfo = $effect_age_price_mod->get("effect_id = '$a0' AND age_id='$age_id' ");
            if (!$einfo) 
            {
                return false;
            }
            $a0 = $einfo['ea_code'];
        }
        if (isset($gongxiao_arr[1]))
        {
            $a1 = trim($gongxiao_arr[1],"\"");
            $einfo = $effect_age_price_mod->get("effect_id = '$a1' AND age_id='$age_id' ");
            if (!$einfo)
            {
                return false;
            }
            $a1 = $einfo['ea_code'];
        }
        if (isset($gongxiao_arr[2]))
        {
            $a2 = trim($gongxiao_arr[2],"\"");
            $einfo = $effect_age_price_mod->get("effect_id = '$a2' AND age_id='$age_id' ");
            if (!$einfo)
            {
                return false;
            }
            $a2 = $einfo['ea_code'];
        }
// echo '<pre>';print_r($jiliao_code);exit;
        if ($gongxiao_num == 3) 
        {
            $a = $a0.",".$a1.",".$a2;
            $conditions = "bm_code = '$jiliao_code' AND taste_id=$kouwei_id AND FIND_IN_SET(ea_code_first,'$a') AND FIND_IN_SET(ea_code_second,'$a') AND FIND_IN_SET(ea_code_third,'$a')";
        }
        elseif  ($gongxiao_num == 2)
        {
            $a = $a0.",".$a1;
            $conditions = "bm_code = '$jiliao_code' AND taste_id=$kouwei_id AND FIND_IN_SET(ea_code_first,'$a') AND FIND_IN_SET(ea_code_second,'$a')";
        }
        elseif  ($gongxiao_num == 1)
        {
            $a = $a0;
            $conditions = "bm_code = '$jiliao_code' AND taste_id=$kouwei_id AND FIND_IN_SET(ea_code_first,'$a') ";
            if($is_gongxiao)
            {
                $conditions = "taste_id=$kouwei_id AND FIND_IN_SET(ea_code_first,'$a') ";
            }
        }
        $material_info = $material_mod->get($conditions);
//        echo '<pre>';print_r($conditions = "bm_code = '$jiliao_code' AND taste_id=$kouwei_id AND FIND_IN_SET(ea_code_first,'$a') ");exit;
        
//        echo '<pre>';print_r($material_info);exit;
        
        if (!$material_info) 
        {
            return false;
        }
        return $material_info['material_code'];
        
        
        
        
        
        
       
        $fb_bz_info = $mod_fbcategory->find(array(
            'conidtions' => "cate_id IN ({$order_goods_list['items']})",
            'index_key' => "parent_id",
        ));
    }
    //根据犬期id和犬种id 获饲喂量建议
    function fmoatFeed($arr)
    {
        $fbcategory = &m("fbcategory");
        $feedmod =& m('feedamount');
        if(!isset($arr['cat_id'] ) || !isset($arr['age_id']) )
        {
            return false;
        }
        $cat_id = $arr['cat_id'];//=====  犬期id  =====
        $age_id = $arr['age_id'];//=====  犬种id  =====
        $cat_info = $fbcategory->get_info($cat_id);
        if(!$cat_info || !$age_id)
        {
            return false;
        }
        $time_list = $feedmod->getTime();

        $ftype = $cat_info['ftype'];
        $conditions = "fbtype = $ftype AND age_id = $age_id";

        if($arr['is_feed'])//=====  计算时间  =====
        {

            $feed_list = $feedmod->find(array(
                'conditions' => $conditions,
                'index_key' => '',
            ));
            $ftime_list = [];
            foreach ((array)$feed_list as $index => $item)
            {
                $ft = [];
                $feed_list[$index]['time_name'] = $time_list[$item['time_id']];
                $ft['time_id'] = $item['time_id'];
                $ft['time_name'] = $time_list[$item['time_id']];
                $ft['wt_min'] = $item['wt_min'];
                $ft['wt_max'] = $item['wt_max'];
                $ft['default_weight'] = $item['default_weight'];
                $ftime_list[$item['time_id']] = $ft;
            }
            return ['feed_list'=>$feed_list,'time_list'=>array_values($ftime_list),'time_num'=>count($ftime_list)];
        }
        else
        {
            $body_condition = isset($arr['body_condition']) ? intval($arr['body_condition']) : 0;//=====  体况id  =====
            $run_time = isset($arr['run_time']) ? intval($arr['run_time']) : 0;//=====  运动量id  =====
            $weight = isset($arr['weight']) ? $arr['weight'] : 0;//===== 体重  =====
            $time_id = isset($arr['time_id']) ? intval($arr['time_id']) : 0;//===== 时间  =====
            $dog_nums = isset($arr['dog_nums']) ? intval($arr['dog_nums']) : 0;//===== 哺乳小狗数目  =====
            $gongxiao = isset($arr['gongxiao']) ? ($arr['gongxiao']) : 0;//=====  功效id  =====
            $kcal = "kcal";
            if($gongxiao)
            {
                $gongxiao_arr = explode(",",$gongxiao);
                if(in_array(107,$gongxiao_arr)) //=====  减肥瘦身  =====
                {
                    $kcal = "redkcal";
                }

            }

            if($body_condition)
            {
                $conditions .= " AND body_condition = $body_condition";
            }
            if($run_time)
            {
                $conditions .= " AND run_time = $run_time";
            }
            if($time_id)
            {
                $conditions .= " AND time_id = $time_id";
            }

            $feed_list = $feedmod->get(array(
                'conditions' => $conditions,
            ));

            if(!$feed_list)
            {
                return false;
            }
            $nums = $feed_list['nums'];
            $is_feed = $feed_list['feed'];
            $enesum = $feed_list['enesum'];//能量参数乘积
            $kcal = $feed_list[$kcal];


            if(!$kcal)
            {
                return false;
            }

            $en = 0;
            if($age_id == 36)//=====  哺乳期特殊处理  =====
            {
                if($dog_nums >= 1 && $dog_nums <= 4)
                {
                    $en = (145 * pow($weight,0.75))+(24 * $dog_nums * $weight);
                }
                elseif ($dog_nums >= 5)
                {
                    $en = (145 * pow($weight,0.75))+(96 * $weight) + (12 * ($dog_nums -4) * $weight);
                }
                else
                {
                    $en = 0;
                    $nums = 0;
                }
            }
            else
            {
                $en = $enesum * pow($weight,0.75);//=====  能量需求  =====
            }
            $feed = ceil($en/$kcal * 1000);//=====  饲喂量  =====
            $return['nums'] = $nums;
            $return['feed'] = $is_feed;
            $return['feed_w'] = $feed;
            return   $return;
        }



    }
    
    //根据犬期id和犬种id 获得基料的价格
    function fmoatPrice($cate_id,$age_id,$baozhuang,$guige,$gongxiao,$is_check=0,$is_try=0)
    {
        $mod = &m("basematerial");
        $smod = &m('standardpackage');
        $fbcategory = &m("fbcategory");

        
        $alone_fblist = $fbcategory->find(array(
            'conditions' =>"is_alone  = 1",
        ));
        $alone_arr = i_array_column($alone_fblist, "cate_id");
        $fblist1 = [];
        if($cate_id)
        {
            $fblist1 = $fbcategory->get_info($cate_id);
        }
        $fblist2 = $fbcategory->get_info($age_id);
        $quanzhong_info = $fblist1;
        $quanqi_info = $fblist2;

        $is_youhui = 1;
        $parr = [24,73];//优惠的犬种父ID
        $sarr = [64,65];//优惠的犬种父ID
        if(in_array($quanzhong_info['parent_id'],$parr) || in_array($quanzhong_info['cate_id'],$sarr))
        {
            $is_youhui = 0.9;
        }

        if($is_try == 1)
        {
            $is_youhui = 0;
        }


        if ($quanzhong_info['parent_id'] == 60) //杂毛
        {
            $pcate_id = $quanzhong_info['cate_id'];
        }
        else 
        {
            $pcate_id = $quanzhong_info['parent_id'];
        }

        $f_price = 0;
        $b_price = 0;
        $pcate_id = intval($pcate_id);
        $age_id = intval($age_id);
        $info = $mod->get("cate_id=$pcate_id AND age_id=$age_id");

        if (!$info) 
        {
            if ($is_check) 
            {
                return false;
            }
           $f_price =  0;
        }
        else 
        {
            $f_price = $info['base_price'];
        }


        //包装规格表
        if (!$baozhuang || !$guige) 
        {
            $b_price = 0;
        }
        else 
        {
            $sinfo = $smod->get("standard_id = $guige AND package_id=$baozhuang");
            if (!$sinfo) 
            {
                if ($is_check)
                {
                    return false;
                }
                $b_price = 0;
            }
            else 
            {
                $b_price = $sinfo['sp_price'];
            }
            
            
        }
        

        

        $is_alone = 0;
        //犬期 功效价格
        $gp_arr = [];
        $effect_age_price_mod = m("effectageprice");
        if (!$gongxiao || !$age_id)
        {
            $gongxiao_price = [];
        }
        else
        {
            $gongxiao_arr = explode("|", trim($gongxiao,"|"));
            if (count($gongxiao_arr) == 1 && in_array($gongxiao_arr[0], $alone_arr)) //如果只选了一个功效，并且是独立功效
            {
                $is_alone = 1;
                
            }
            if ($gongxiao_arr)
            {
                foreach ((array)$gongxiao_arr as $key => $value)
                {
                    $value = intval(trim($value,'"'));
                    if (!$value)
                    {
                        continue;
                    }
                    $einfo = $effect_age_price_mod->get("effect_id = '$value' AND age_id='$age_id' ");
                    if (!$einfo)
                    {
                        if ($is_check)
                        {
                            return false;
                        }
                        $gp_arr[$value] = 0;
                    }
                    else
                    {
                        $gp_arr[$value] = $einfo['ea_price'];
                    }
                }
                 
            }
            
        }

        $ret['jiliao_price'] = $f_price;
        $ret['guige_price'] = $b_price;
        $ret['gp_price'] = $gp_arr;
        $ret['is_alone'] = $is_alone;
        $ret['is_youhui'] = $is_youhui;
        return $ret;
    }

    /**
     * mes推送成功 发微信消息 提醒上传标签图
     *
     * @author liang.li
     * @return
     */
    public static function f_wxo($order_info,$item)
    {
        $member_mod = &m ( "member" );
        //=====  如果是微信订单要发送消息  =====
        $member_info = $member_mod->get_info($order_info['user_id']);
        if($member_info['openid'])
        {
            $mod = m('accesstoken');
            $access_token = $mod->get_token();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $data['touser'] = $member_info['openid'];
            $data['template_id'] = 'UgrJMUtMBP4ErAoAccRXWbLT_uZddbDPIQV1byo-8w4';
            $data['url'] = "http://h5.myfoodiepet.com/my_order-orderimg-".$item['rec_id'].".html";

            $data['data']['first']['value'] = "[麦富迪高端定制]提醒您尽快为您爱宠的定制主粮上传个性化标签图片";
            $data['data']['first']['color'] = "#173177";
            $data['data']['orderno']['value'] = $order_info['order_sn']."(子订单:".$item['rec_id'].")";
            $data['data']['orderno']['color'] = "#173177";

            $data['data']['amount']['value'] = $order_info['final_amount'];
            $data['data']['amount']['color'] = "#173177";

            $data['data']['remark']['value'] = "请您尽快上传个性化标签图片，一旦订单开始生产后，您将无法修改您订单的图片信息，谢谢!";
            $data['data']['remark']['color'] = "#173177";

            $res = https_request($url,json_encode($data));
            if($res['errcode'] == 0)
            {
                $order_goods_mod = m('ordergoods');
                $order_goods_mod->edit("rec_id = ".$item['rec_id'],['is_send'=>1]);
            }
            else
            {
                $access_token = $mod->settoken();
                $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
                $res = https_request($url,json_encode($data));
            }

        }
    }

    //ns add 添加新的推送
    public function newMesf($order_id)
    {
        $order = m('order');
        $order_info = $order->get_info($order_id);
        // if(($order_info['mes_status'] == 1) || ($order_info['status'] != 20) || ($order_info['status'] == 1))
        // {
        //     return false;
        // }
        $url = "http://222.175.7.188:8060/Services/GambolSalesOrderService.asmx/UploadSO";
        $feed_mod =& m('feedamount');
        $type_list = $feed_mod->getType();
        $time_list = $feed_mod->getTime();
        $body_list = $feed_mod->getBody();
        $run_list  = $feed_mod->getRun();
        $order_goods = m('ordergoods');
        $mod_fbcategory = &m("fbcategory");
        $region_mod = m('region');
        $order_goods_list = $order_goods->find(array(
            'conditions' => "order_id = $order_id",
        ));
        $ship_area_id = $order_info['ship_area_id'];
        $region_list = $region_mod->find(array(
            'conditions' => "region_id IN ($ship_area_id) ",
            'index_key' => '',
        ));
        $region_name = "中国;".$region_list[1]['region_name'].";".$region_list[2]['region_name'].";";
      
        
        if($order_info['ship_tel'])
        {
            $phone = $order_info['ship_tel'];
        }
        if($order_info['ship_mobile'])
        {
            $phone = $order_info['ship_mobile'];
        }
        $mes['soId'] = $order_info['order_id'];
        $mes['clientName'] = $order_info['ship_name'];//=====  客户姓名  =====
        $mes['soDate'] = date('Y-m-d H:i:s',$order_info['add_time']);//=====  下单时间  =====
        $mes['clientAddress'] = $region_name.$order_info['ship_addr'];//=====  客户地址  =====
        $mes['saleOrg'] = '麦富迪';//=====  销售组织  =====
        $mes['remark'] = $order_info['memo'];//=====  订单备注  =====
        $mes['contact'] = $phone;//=====  订单备注  =====
        $type = "custom";
        import('shopCommon');
        foreach ((array)$order_goods_list as $index => $item)
        {
            $mesgoods = array();
            $params = $item['params'];
            $params_arr = json_decode($params,true);

            $code = "";
            $petFeedQty = "";
            $petAge = "";
            $petBreed = "";
            $petFeedFrq = "";
            $matFunc = "";
            $shengzhang = "";
            if ($item['size'] == "diy")
            {
                if(!$item['style'] && ($item['is_send'] == 0)) //=====    =====
                {
                    $this->f_wxo($order_info,$item);
                }

                
                $type = "diy";
                $soiType = "D";
                $fb_bz_info = [];
                if (!$item['items'])
                {
                  return;
                }
                $fb_bz_info = $mod_fbcategory->find(array(
                    'conditions' => "cate_id IN ({$item['items']})",
                    'index_key' => "parent_id",
                ));

                //=====  犬种三级分类特殊处理  =====
                $dog_list = $mod_fbcategory->get_list(21);
                foreach ((array)$dog_list as $index1 => $item1)
                {
                    if(isset($fb_bz_info[$index1]))
                    {
                        $fb_bz_info[21] = $fb_bz_info[$index1];
                        unset($fb_bz_info[$index1]);
                    }
                }


                $baozhuang = $fb_bz_info[16]['cate_name'];
                $kezhong   = $fb_bz_info[11]['ve']/10;
                $petAge   = $fb_bz_info[34]['cate_name'];
                $petBreed   = $fb_bz_info[21]['cate_name'];

                //物料编码
                $fidybase = new FdiyBase();
                $code = $fidybase->fmoatCode($params_arr);

                $gongxiao_a = json_decode($item['params'],1);
                $gongxiao_str = $gongxiao_a[1];
                $gongxiao_arr = explode(":",$gongxiao_str);
                if($gongxiao_arr[1])
                {
                    $g_conditions_str = $gongxiao_arr[1];
                    $gongxiao_list = $mod_fbcategory->find(array(
                        'conditions' => "cate_id IN ($g_conditions_str) ",
                    ));
                    foreach ((array)$gongxiao_list as $index1 => $item1)
                    {
                        $matFunc .= $item1['cate_name'].",";
                    }
                    $matFunc = trim($matFunc,",");
                }
                $gongxiao = trim($item['params'],'"');
                
                //=====  生长阶段  =====
                if($item['time_id'])
                {
                    $shengzhang = $time_list[$item['time_id']];
                }

                //=====  计算饲喂量  =====
                $feed_arr['gongxiao'] = $gongxiao;//=====  功效  =====
                $feed_arr['cat_id'] = $fb_bz_info[21]['cate_id'];//=====  犬种id  =====
                $feed_arr['age_id'] = $fb_bz_info[34]['cate_id'];//=====  犬期id  =====
                $feed_arr['is_feed'] = 0;//=====  计算 =====
                $feed_arr['body_condition'] = $item['body_condition'];
                $feed_arr['run_time'] = $item['run_time'];
                $feed_arr['weight'] = $item['weight'];
                $feed_arr['time_id'] = $item['time_id'];
                $feed_arr['dog_nums'] = $item['dog_nums'];
                $price_arr = $this->fmoatFeed($feed_arr);
//                echo '<pre>';print_r($price_arr);exit;
                
                if ($price_arr)
                {
                    if($price_arr['feed'])
                    {
                        $petFeedQty = "自由采食";
                    }
                    else
                    {
                        $petFeedQty = $price_arr['feed_w'];
                    }
                    $petFeedFrq = $price_arr['nums'];
                }

            }
            else
            {
                $soiType = "N";
                $spec_desc = unserialize($params_arr['oProducts']['spec_desc']);
                $spec_value = $spec_desc['spec_value'];
                $baozhuang = isset($spec_value[6]) ? $spec_value[6] : '';
                $kezhong   = isset($params_arr['oProducts']['weight']) ? $params_arr['oProducts']['weight']/10 : '';
                $code = $params_arr['oGoods']['bn'];
                $matFunc = $params_arr['oGoods']['name'];
            }
            
//            echo '<pre>';print_r($item);exit;
            
            $dog_name = urlencode($item['dog_name']);
            $dog_date = $item['dog_date'];
            $dog_desc = urlencode($item['dog_desc']);
            $quantity = $item['quantity'];
            $weight   = $item['weight'];
            $style   = !empty($item['style']) ? PC_URL.$item['style'] : '';
            $petExercise   = isset($run_list[$item['run_time']]) ? $run_list[$item['run_time']] : '';
            
            
            if(!$code)//=====  没有物流编码,不推mes  =====
            {
                return false;
            }


            $mesgoods['soiId'] = $item['rec_id'];//=====  物料id  =====
            $mesgoods['matId'] = $code;//=====  物料id  =====
            $mesgoods['soiType'] = $soiType;//=====  D diy N normal  =====
            $mesgoods['pckMode'] = $baozhuang;//=====  包装方式  =====
            $mesgoods['unitQty'] = $kezhong;//=====  克重  =====
            $mesgoods['petPeriod'] = $shengzhang;//=====  生长阶段  =====
            $mesgoods['matFunc'] = $matFunc;//=====  功效  =====
            $mesgoods['quantity'] = $quantity;//=====  数量  =====
            $mesgoods['petName'] = $dog_name;//=====  宠物姓名  =====
            $mesgoods['petAge'] = $petAge;//===== 宠物周期  =====
            $mesgoods['petBirthday'] = $dog_date;//=====  宠物生日  =====
            $mesgoods['petWeight'] = $weight;//=====  宠物重量  =====
            $mesgoods['petImageURL'] = $style;//=====  物料图片  =====
            $mesgoods['petBreed'] = $petBreed;//=====  宠物种类  =====
            $mesgoods['petExercise'] = $petExercise;//=====  宠物运动量  =====
            $mesgoods['petFeedQty'] = $petFeedQty;//=====  宠物饲喂量  =====
            $mesgoods['petFeedFrq'] = $petFeedFrq;//=====  宠物饲喂频率  =====
            $mesgoods['masterQuote'] = $dog_desc;//=====  主人寄语  =====

            $mes['soItems'][] = $mesgoods;
        }

        if($type == 'custom')
        {
            return false;
        }
        $data = [$mes];

        //ns add 赠品的加入，删减库存
        $goods_giveaway_mod =& m('goods_giveaway');
        $product_mod =& m('products');
        $goods_mod =& m('goods');
        $goods_package_mod =& m('goods_package');


        if($order_info['is_giveaway']){
            $giveaway_list = json_decode($order_info['json_giveaway']);
            //
            $ct = count($data[0]['soItems']) - 1;
            //常规商品
            $cList = explode(",",$giveaway_list->cList);
            if($cList){
              foreach($cList as $cid){
                if($cid){
                  $ct ++;
                  //进行商品的查询
                  $goods = $goods_mod->get("goods_id=$cid");
                  $product = $product_mod->get("goods_id=$cid");

                  $data[0]['soItems'][$ct]['matId'] = $goods['bn'];
                  $data[0]['soItems'][$ct]['soiType'] = 'G';

                  $spec_desc_c = unserialize($product['spec_desc']);
                  $spec_value_c = $spec_desc_c['spec_value'];
                  $data[0]['soItems'][$ct]['pckMode'] = isset($spec_value_c[6]) ? $spec_value_c[6] : '';

                  $data[0]['soItems'][$ct]['unitQty'] = isset($product['weight']) ? $product['weight']/10 : '';
                  $data[0]['soItems'][$ct]['quantity'] = 1;
                  $data[0]['soItems'][$ct]['matFunc'] = $goods['name'];
                  //删减库存
                  if($product){
                      $product_mod->edit($product['product_id'],array('store'=>$product['store']-1));
                  }
                }
              }
            }
            
            //非常规减库存
            $fList = explode(",",$giveaway_list->fList);
            $type_arr = array(1=>'袋装',2=>'桶装',3=>'盒装',4=>'罐装',5=>'支',);
            if(!empty($fList)){
              foreach($fList as $fid){
                if($fid){
                  $ct ++;
                  $giveaway = $goods_giveaway_mod->get("goods_id=$fid");
                  $data[0]['soItems'][$ct]['matId'] = $giveaway['bn'];
                  $data[0]['soItems'][$ct]['soiType'] = 'G';
                  $data[0]['soItems'][$ct]['pckMode'] = isset($type_arr[$giveaway['type_id']]) ? $type_arr[$giveaway['type_id']] : '';
                  $data[0]['soItems'][$ct]['unitQty'] = isset($giveaway['weight']) ? $giveaway['weight']/10 : '';
                  $data[0]['soItems'][$ct]['quantity'] = 1;
                  $data[0]['soItems'][$ct]['matFunc'] = $giveaway['name'];
                  //删减库存
                  if($giveaway){
                      $goods_giveaway_mod->edit($giveaway['goods_id'],array('store'=>$giveaway['store']-1));
                  }
                }
              }
            }
            //礼包
            $gList = explode(",",$giveaway_list->gList);
            if($gList){
              foreach($gList as $gid){
                if($gid){
                $package = $goods_package_mod->get("id=$gid");
                if($package){
                    //判断是否有常规
                    if($package['c_list']){
                        $g_cList = explode(",",$package['c_list']);
                        foreach($g_cList as $g_cid){
                          if($g_cid){
                              $ct ++;
                              //进行商品的查询
                              $g_goods = $goods_mod->get("goods_id=$g_cid");
                              $g_product = $product_mod->get("goods_id=$g_cid");

                              $data[0]['soItems'][$ct]['matId'] = $g_goods['bn'];
                              $data[0]['soItems'][$ct]['soiType'] = 'G';

                              $spec_desc_c = unserialize($g_product['spec_desc']);
                              $spec_value_c = $spec_desc_c['spec_value'];
                              $data[0]['soItems'][$ct]['pckMode'] = isset($spec_value_c[6]) ? $spec_value_c[6] : '';

                              $data[0]['soItems'][$ct]['unitQty'] = isset($g_product['weight']) ? $g_product['weight']/10 : '';
                              $data[0]['soItems'][$ct]['quantity'] = 1;
                              $data[0]['soItems'][$ct]['matFunc'] = $g_goods['name'];
                              //删减库存
                              if($g_product){
                                  $product_mod->edit($g_product['product_id'],array('store'=>$g_product['store']-1));
                              }
                          }
                        }
                    }
                    //判断非常规
                    if($package['f_list']){
                        $g_fList = explode(",",$package['f_list']);
                        foreach($g_fList as $g_fid){
                          if($g_fid){
                          $ct ++;
                          $g_giveaway = $goods_giveaway_mod->get("goods_id=$g_fid");
                          $data[0]['soItems'][$ct]['matId'] = $g_giveaway['bn'];
                          $data[0]['soItems'][$ct]['soiType'] = 'G';
                          $data[0]['soItems'][$ct]['pckMode'] = isset($type_arr[$g_giveaway['type_id']]) ? $type_arr[$g_giveaway['type_id']] : '';
                          $data[0]['soItems'][$ct]['unitQty'] = isset($g_giveaway['weight']) ? $g_giveaway['weight']/10 : '';
                          $data[0]['soItems'][$ct]['quantity'] = 1;
                          $data[0]['soItems'][$ct]['matFunc'] = $g_giveaway['name'];

                          //删减库存
                          if($g_giveaway){
                              $goods_giveaway_mod->edit($g_giveaway['goods_id'],array('store'=>$g_giveaway['store']-1));
                          }

                          }
                        }
                    }

                }
                }

              }
                
            }

        }
        if($_GET['test'] == 1)
        {
            echo '<pre>';print_r($data);exit;
            
        }
//        echo '<pre>';print_r($data);exit;
        $res = $this->post_to_url($url, array('jsonData'=>json_encode($data)));
        $mes_data = serialize($mes);
        if($res['err'])
        {
            $order->edit($order_id,['mes_status'=>2,'mes_res'=>$res['msg'],'me_data'=>$mes_data]);
        }
        else
        {
            if($res['data']['resultCode'] == 200)//=====  成功  =====
            {
                $order->edit($order_id,['mes_status'=>1,'mes_res'=>$res['data']['resultMsg'][0],'me_data'=>$mes_data]);
            }
            else
            {
                $order->edit($order_id,['mes_status'=>2,'mes_res'=>$res['data']['resultCode'].$res['data']['resultMsg'][0],'me_data'=>$mes_data]);
            }
        }
        return $res;
    }




    /**
     * Created by PhpStorm.
     * User: liang
     * Date: 16/11/17
     * Time: 下午4:23
     * http://222.175.7.188:8060/Services/GambolSalesOrderService.asmx
     */
    public function mesf($order_id)
    {
        $order = m('order');
        $order_info = $order->get_info($order_id);
        if(($order_info['mes_status'] == 1) || ($order_info['status'] != 20))
        {
            return false;
        }
        $url = "http://222.175.7.188:8060/Services/GambolSalesOrderService.asmx/UploadSO";
        $feed_mod =& m('feedamount');
        $type_list = $feed_mod->getType();
        $time_list = $feed_mod->getTime();
        $body_list = $feed_mod->getBody();
        $run_list  = $feed_mod->getRun();
        $order_goods = m('ordergoods');
        $mod_fbcategory = &m("fbcategory");
        $region_mod = m('region');
        $order_goods_list = $order_goods->find(array(
            'conditions' => "order_id = $order_id",
        ));
        $ship_area_id = $order_info['ship_area_id'];
        $region_list = $region_mod->find(array(
            'conditions' => "region_id IN ($ship_area_id) ",
            'index_key' => '',
        ));
        $region_name = "中国;".$region_list[1]['region_name'].";".$region_list[2]['region_name'].";";
      
        
        if($order_info['ship_tel'])
        {
            $phone = $order_info['ship_tel'];
        }
        if($order_info['ship_mobile'])
        {
            $phone = $order_info['ship_mobile'];
        }
        $mes['soId'] = $order_info['order_id'];
        $mes['clientName'] = urlencode($order_info['ship_name']);//=====  客户姓名  =====
        $mes['soDate'] = date('Y-m-d H:i:s',$order_info['add_time']);//=====  下单时间  =====
        $mes['clientAddress'] = urlencode($region_name.$order_info['ship_addr']);//=====  客户地址  =====
        $mes['saleOrg'] = '麦富迪';//=====  销售组织  =====
        $mes['remark'] = urlencode($order_info['memo']);//=====  订单备注  =====
        $mes['contact'] = $phone;//=====  订单备注  =====
        $type = "custom";
        import('shopCommon');
        foreach ((array)$order_goods_list as $index => $item)
        {
            $mesgoods = array();
            $params = $item['params'];
            $params_arr = json_decode($params,true);

            $code = "";
            $petFeedQty = "";
            $petAge = "";
            $petBreed = "";
            $petFeedFrq = "";
            $matFunc = "";
            $shengzhang = "";
            if ($item['size'] == "diy")
            {
                if(!$item['style'] && ($item['is_send'] == 0)) //=====    =====
                {
                    $this->f_wxo($order_info,$item);
                }

                
                $type = "diy";
                $soiType = "D";
                $fb_bz_info = [];
                if (!$item['items'])
                {
                  return;
                }
                $fb_bz_info = $mod_fbcategory->find(array(
                    'conditions' => "cate_id IN ({$item['items']})",
                    'index_key' => "parent_id",
                ));

                //=====  犬种三级分类特殊处理  =====
                $dog_list = $mod_fbcategory->get_list(21);
                foreach ((array)$dog_list as $index1 => $item1)
                {
                    if(isset($fb_bz_info[$index1]))
                    {
                        $fb_bz_info[21] = $fb_bz_info[$index1];
                        unset($fb_bz_info[$index1]);
                    }
                }


                $baozhuang = $fb_bz_info[16]['cate_name'];
                $kezhong   = $fb_bz_info[11]['ve']/10;
                $petAge   = $fb_bz_info[34]['cate_name'];
                $petBreed   = $fb_bz_info[21]['cate_name'];

                //物料编码
                $fidybase = new FdiyBase();
                $code = $fidybase->fmoatCode($params_arr);

                $gongxiao_a = json_decode($item['params'],1);
                $gongxiao_str = $gongxiao_a[1];
                $gongxiao_arr = explode(":",$gongxiao_str);
                if($gongxiao_arr[1])
                {
                    $g_conditions_str = $gongxiao_arr[1];
                    $gongxiao_list = $mod_fbcategory->find(array(
                        'conditions' => "cate_id IN ($g_conditions_str) ",
                    ));
                    foreach ((array)$gongxiao_list as $index1 => $item1)
                    {
                        $matFunc .= $item1['cate_name'].",";
                    }
                    $matFunc = trim($matFunc,",");
                }
                $gongxiao = trim($item['params'],'"');
                
                //=====  生长阶段  =====
                if($item['time_id'])
                {
                    $shengzhang = $time_list[$item['time_id']];
                }

                //=====  计算饲喂量  =====
                $feed_arr['gongxiao'] = $gongxiao;//=====  功效  =====
                $feed_arr['cat_id'] = $fb_bz_info[21]['cate_id'];//=====  犬种id  =====
                $feed_arr['age_id'] = $fb_bz_info[34]['cate_id'];//=====  犬期id  =====
                $feed_arr['is_feed'] = 0;//=====  计算 =====
                $feed_arr['body_condition'] = $item['body_condition'];
                $feed_arr['run_time'] = $item['run_time'];
                $feed_arr['weight'] = $item['weight'];
                $feed_arr['time_id'] = $item['time_id'];
                $feed_arr['dog_nums'] = $item['dog_nums'];
                $price_arr = $this->fmoatFeed($feed_arr);

                if ($price_arr)
                {
                    if($price_arr['feed'])
                    {
                        $petFeedQty = "自由采食";
                    }
                    else
                    {
                        $petFeedQty = $price_arr['feed_w'];
                    }
                    $petFeedFrq = $price_arr['nums'];
                }

            }
            else
            {
                $soiType = "N";
                $spec_desc = unserialize($params_arr['oProducts']['spec_desc']);
                $spec_value = $spec_desc['spec_value'];
                $baozhuang = isset($spec_value[6]) ? $spec_value[6] : '';
                $kezhong   = isset($params_arr['oProducts']['weight']) ? $params_arr['oProducts']['weight']/10 : '';
                $code = $params_arr['oGoods']['bn'];
                $matFunc = $params_arr['oGoods']['name'];
            }
            
            $dog_name = urlencode($item['dog_name']);
            $dog_date = $item['dog_date'];
            $dog_desc = urlencode($item['dog_desc']);
            $quantity = $item['quantity'];
            $weight   = $item['weight'];
            $style   = !empty($item['style']) ? PC_URL.$item['style'] : '';
            $petExercise   = isset($run_list[$item['run_time']]) ? $run_list[$item['run_time']] : '';
            
            
            if(!$code)//=====  没有物流编码,不推mes  =====
            {
                return false;
            }


            $mesgoods['soiId'] = $item['rec_id'];//=====  物料id  =====
            $mesgoods['matId'] = $code;//=====  物料id  =====
            $mesgoods['soiType'] = $soiType;//=====  D diy N normal  =====
            $mesgoods['pckMode'] = $baozhuang;//=====  包装方式  =====
            $mesgoods['unitQty'] = $kezhong;//=====  克重  =====
            $mesgoods['petPeriod'] = $shengzhang;//=====  生长阶段  =====
            $mesgoods['matFunc'] = $matFunc;//=====  功效  =====
            $mesgoods['quantity'] = $quantity;//=====  数量  =====
            $mesgoods['petName'] = $dog_name;//=====  宠物姓名  =====
            $mesgoods['petAge'] = $petAge;//===== 宠物周期  =====
            $mesgoods['petBirthday'] = $dog_date;//=====  宠物生日  =====
            $mesgoods['petWeight'] = $weight;//=====  宠物重量  =====
            $mesgoods['petImageURL'] = $style;//=====  物料图片  =====
            $mesgoods['petBreed'] = $petBreed;//=====  宠物种类  =====
            $mesgoods['petExercise'] = $petExercise;//=====  宠物运动量  =====
            $mesgoods['petFeedQty'] = $petFeedQty;//=====  宠物饲喂量  =====
            $mesgoods['petFeedFrq'] = $petFeedFrq;//=====  宠物饲喂频率  =====
            $mesgoods['masterQuote'] = $dog_desc;//=====  主人寄语  =====

            $mes['soItems'][] = $mesgoods;
        }

        if($type == 'custom')
        {
            return false;
        }
        $data = [$mes];
        if($_GET['test'] == 1)
        {
            echo '<pre>';print_r($data);exit;
            
        }
        $res = $this->post_to_url($url, array('jsonData'=>json_encode($data)));
        $mes_data = serialize($mes);
        if($res['err'])
        {
            $order->edit($order_id,['mes_status'=>2,'mes_res'=>$res['msg'],'me_data'=>$mes_data]);
        }
        else
        {
            if($res['data']['resultCode'] == 200)//=====  成功  =====
            {
                $order->edit($order_id,['mes_status'=>1,'mes_res'=>$res['data']['resultMsg'][0],'me_data'=>$mes_data]);
//                $this->mescallback($order_id);//回调处理
            }
            else
            {
                $order->edit($order_id,['mes_status'=>2,'mes_res'=>$res['data']['resultCode'].$res['data']['resultMsg'][0],'me_data'=>$mes_data]);
            }
        }
        return $res;
    }

    /**
     * 推送成功的回调函数
     */
    public function mescallback($ordre_id)
    {
        $app_key = DINGO_APPKEY;
        $time = time();
        $sign = makeSign($time);
        $data['sign'] = $sign;
        $data['timestamp'] = $time;
        $data['appid'] = $app_key;
        //推送海报
        return https_request(DINGO_URL."/api/auth/img/$ordre_id",$data);
    }


    //查询mes订单状态
    function sta($order_id)
    {
        if(!$order_id)
        {
            return false;
        }
        $url = "http://222.175.7.188:8060/Services/GambolSalesOrderService.asmx/GetSOStatus";
        $data = array($order_id);
        $res = $this->post_to_url($url, array('jsonData'=>json_encode($data)));
//        echo '<pre>';print_r($res);exit;
        
        if(isset($res['data']['resultMsg']))
        {
            $rarr = $res['data']['resultMsg'];
            $ra = $rarr[0];
            $ra_arr = explode(":",$ra);
            if($ra_arr[0] == $order_id && $ra_arr[1])
            {
                return $ra_arr[1];
            }
        }
        return false;
    }

    //mes上传标签图
    function mesimg($order_goods_id,$img)
    {
        if(!$order_goods_id)
        {
            return false;
        }
        $url = "http://222.175.7.188:8060/Services/GambolSalesOrderService.asmx/UpLoadImage";
        $arr['soiId'] = $order_goods_id;
        $arr['petImageURL'] = $img;
        $data = array($arr);
        $res = $this->post_to_url($url, array('jsonData'=>json_encode($data)));
//var_dump($res);exit;
        if($res['err'])
        {
            return false;
        }
        else
        {
            if($res['data']['resultCode'] == 200)//=====  成功  =====
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }




    function post_to_url($url, $data) {
        $fields = '';
        foreach($data as $key => $value) {
            $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');


        $post = curl_init();
        $aHeader = array(
            "Password:password",
            "Username:eCommerce"
        );
        curl_setopt($post, CURLOPT_HTTPHEADER, $aHeader);
//        curl_setopt($post,CURLOPT_PROXY,'127.0.0.1:8888');//Charles/
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($post);

//$error_mod = m('error');
//$error_mod->add(['content'=>json_encode($result)]);

        if (curl_error($post)){
            $rs = array('err'=>1,'msg'=>"curl error:".curl_error($post) );//-1:代表CURL请求过程中的错误~
        }else{
            $rs = array('err'=>0,'data'=>json_decode($result,1));
        }
// echo iconv("UTF-8","GBK", $data);
//        echo $result;
//        echo "\r\n";exit;


        curl_close($post);
        return $rs;
    }


}