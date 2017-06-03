<?php
   /**
   *量体前置
   *@author liang.li <1184820705@qq.com>
   *@2015年10月22日
   */
    class Cart extends Result
    {
        function __construct()
        {
            parent::__construct();
        }
        
        /** 
        *地区列表
        *@author liang.li <1184820705@qq.com>
        *@2015年10月22日
        */
        function regionList($data) 
        {
            $serve_mod = &m("serve");
            $servelist = $serve_mod->find(array(
                "conditions" => " 1 ",
            ));
            $rData = array();
            foreach($servelist as $key => $val)
            {
                $rData[] = $val["region_id"];
            }
            $region_mod = &m("region");
            $citylist = array();
            if ($rData) 
            {
                $citylist = $region_mod->find(array(
                    "conditions" => "region_id ".db_create_in($rData),
                    'index_key' => "",
                ));
               if ($citylist)
               {
                   foreach ($citylist as $key => &$value) 
                   {
                       if ($value['parent_id'] == 3)
                       {
                           $value['region_name'] = "北京".$value['region_name'];
                       }
                       elseif ($value['parent_id'] == 61) 
                       {
                           $value['region_name'] = "重庆".$value['region_name'];
                       }
                       elseif ($value['parent_id'] == 41)
                       {
                           $value['region_name'] = "上海".$value['region_name'];
                       }
                       elseif ($value['parent_id'] == 22)
                       {
                           $value['region_name'] = "天津".$value['region_name'];
                       }
                   }
               }
            }
            $this->result = $citylist;
            return $this->sresult();
        }
        
        /**
        *门店列表
        *@author liang.li <1184820705@qq.com>
        *@2015年10月22日
        */
        function serveList($data) 
        {
            $region_id = isset($data->region_id) ? $data->region_id : '';
            $token = isset($data->token) ? $data->token : '9e0cb5e638ab06edab708b14a4c9ba73';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            $user_name = $user_info['user_name'];
            $membner_lv_id = $user_info['member_lv_id'];
            if (!$user_info)
            {
                return $this->tresult();
            }
            if (!$region_id) 
            {
                $this->eresult('缺少region_id参数');
            }
            
            $serve_mod = &m("serve");
            $member_mod = m('member');
            if ($membner_lv_id == 1) //=====  消费者  =====
            {
                $member_invite_mod = m('memberinvite');
                $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
                if ($invite_info) 
                {
                    $inviter_id = $invite_info['inviter'];
                    $invi_info = $member_mod->get_info($inviter_id);
                    $invi_member_lv_id = $invi_info['member_lv_id'];
                }
            }
            //=====  如果自己或者对应创业者是vip  =====
            $conditions = "region_id = '{$region_id}' AND virtual = '0' AND shop_type IN (1,2)";
            //=====  此时不显示自营门店  =====
            
            $regions = $serve_mod->find(array(
                "conditions" => $conditions,
                'index_key'  => "",
                'order'      => "storetype DESC"
            ));
            $free_list = array();
            if ($regions) 
            {
                foreach ($regions as $key => $value) 
                {
                    
                    $regions[$key]['money_name'] = 100;
                    if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
                    {
                        $regions[$key]['is_free'] = 1;
                        if ($value['storetype'] == 2) //=====    自营门店  =====
                        {
                            $regions[$key]['is_free'] = 0;
                            /* if (($value['mobile'] == $user_name) || ($value['mobile'] == $invi_info['user_name']))
                            {
                                $regions[$key]['is_free'] = 0;
                            }
                            else
                            {
                                
                               // unset($regions[$key]);
                            }*/
                        } 
                    }
                    else 
                    {
                        $regions[$key]['is_free'] = 0;
                        if ($value['storetype'] == 2) //=====    自营门店  =====
                        {
                          // unset($regions[$key]);
                        }
                    }
                    
                }
            }
            
            $this->result = array_values($regions);
            return $this->sresult();
        }
        
        /**
        *历史量体数据
        *@author liang.li <1184820705@qq.com>
        *@2015年10月22日
        */
        function getFigure($data) 
        {
            $keyword = isset($data->keyword) ? $data->keyword : '';
            $token = isset($data->token) ? $data->token : '9e0cb5e638ab06edab708b14a4c9ba73';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            $membner_lv_id = $user_info['member_lv_id'];
            $member_mod = m('member');
            if (!$user_info)
            {
                return $this->tresult();
            }
            
            if ($membner_lv_id == 1) //=====  消费者  =====
            {
                $member_invite_mod = m('memberinvite');
                $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
                if ($invite_info)
                {
                    $inviter_id = $invite_info['inviter'];
                    $invi_info = $member_mod->get_info($inviter_id);
                    $invi_member_lv_id = $invi_info['member_lv_id'];
                }
            }
            
            $customer_mod = &m("customer_figure");
            $order_figure_mod = m('orderfigure');
            $order_mod = m('order');
            $member_invite_mod = m('memberinvite');
            $server_mod = &m("serve");
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            $inviter_id = $invite_info['inviter'];
            if(!empty($keyword))
            {
                $conditions = " figure_state = 1 AND firsttime > 1447171200 AND  (customer_name = '{$keyword}' OR customer_mobile = '{$keyword}')";
            }
            else
            {
                $conditions = "figure_state=1 AND firsttime > 1447171200 AND (customer_mobile='{$user_info['phone_mob']}' OR storeid = $user_id ) ";
            } 
            
            $list = $customer_mod->find(array(
                "conditions" => $conditions,
                'order'      => "is_first DESC,lasttime DESC",
                'limit'      => "50",
            ));
            
            $aData = array();
            if ($list) 
            {
                //=====  获得量体字段项目  =====
              
                foreach((array)$list as $key => $val)
                {
                    $ids[$val["id_serve"]]   = $val["id_serve"];
                    $aData[] = $val;
                }
                //=====  获取历史量体数据对应的所有门店信息  =====
                $servers = $server_mod->find(array(
                    'conditions' => "idserve ".db_create_in($ids),
                ));
                
                foreach($aData as $key => $val)
                {
                    if (!$val['liangti_name'])
                    {
                        $aData[$key]['liangti_name'] = $servers[$val["id_serve"]]['linkman'];
                    }
                    $aData[$key]['address'] = isset($servers[$val["id_serve"]]) ? $servers[$val["id_serve"]]['serve_address'] : '';
                    $aData[$key]['money_name'] = 100;
                    if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
                    {
                        if ($val['is_first'] == 0 && $val['service_mode'] == 3)
                        {
                            //=====  根据手机号匹配   =====
                            $aData[$key]['is_free'] = 1;
                            if ($servers[$val["id_serve"]]['storetype'] == 2)
                            {
                                $aData[$key]['is_free'] = 0;
                            }
                        
                        }
                        else 
                        {
                            $aData[$key]['is_free'] = 0;
                        }
                    }
                    else 
                    {
                        $aData[$key]['is_free'] = 0;
                    }
                    
                    
                }
                
            }
            $this->result = $aData;
            return $this->sresult();
        }
        
        /**
        *历史量体数据详情
        *@author liang.li <1184820705@qq.com>
        *@2016年1月18日
        */
        function getFigureInfo($data) 
        {
            $figure_sn = isset($data->figure_sn) ? $data->figure_sn : '';
            $customer_mod = &m("customer_figure");
            $serve_mod = m('serve');
            $member_mod = m('member');
            $order_figure_info = $customer_mod->get_info($figure_sn);
            $serve_mode_money= array(  //=====  量体方式和费用配置  =====
                '1' => '上门量体', //=====    =====
                '2' => '到店量体', //=====    =====
                '3' => '线下采集',//=====    =====
                '4' => '后台录入',//=====    =====
                '6' => '指派量体师',//=====    =====
            );
            $cus['customer_name'] = $order_figure_info['customer_name'];
            $cus['customer_mobile'] = $order_figure_info['customer_mobile'];
            $cus['souce_from'] = $serve_mode_money[$order_figure_info['service_mode']];
            $cus['real_name'] = '';
            $cus['phone'] = '';
            $serve_info = $serve_mod->get_info($order_figure_info['id_serve']);
            if($order_figure_info['liangti_id'])
            {
                $liangti_id = $order_figure_info['liangti_id'];
                $liangti_info = $member_mod->get_info($liangti_id);
                if ($liangti_info) 
                {
                    $cus['real_name'] = $order_figure_info['liangti_name'];
                    $cus['phone'] = $liangti_info['user_name'];
                }
            }
            else
            {
                if ($serve_info) 
                {
                    $cus['real_name'] = $serve_info['linkman'];
                    $cus['phone'] = $serve_info['mobile'];
                }
            }
            $cus['serve_name'] = $serve_info['serve_name'];
            $cus['serve_address'] = $serve_info['serve_address'];
            $cus['store_mobile'] = $serve_info['store_mobile'];
            $cus['lasttime'] = $order_figure_info['lasttime'];
            
            if (!$order_figure_info) 
            {
                return $this->eresult('无此数据');
            }
            $order_figure = array();
            $url = FIGUREURL."/soap/club.php?act=getSpecialFields";
            $json = file_get_contents($url);
            $json = json_decode($json,true);
            $json = $json['result']['data'];
            $special = array();//=====  特体  =====
            $figure_state = 1;
            foreach ($json['public'] as $key => $value)
            {
                $tmp['name'] = $value['cateName'];
                $val = $order_figure_info[$value['value_name']];
            
                foreach ($value['item'] as $key1 => $value1)
                {
                    $tmp['value'] = '';
                    if ($val == $value1['value'])
                    {
                        if ($figure_state)
                        {
                            $tmp['value'] = $value1['name'];
                        }
                        break;
                    }
                }
                $special[] = $tmp;
            }
            $style = array();//=====  着装风格  =====
            foreach ($json['special'] as $key => $value)
            {
                $tmp2['name'] = $value['cateName'];
                 
                $val = $order_figure_info[$value['value_name']];
                 
                foreach ($value['item'] as $key1 => $value1)
                {
                    $tmp2['value'] = '';
                    if ($val == $value1['value'])
                    {
                        if ($figure_state)
                        {
                            $tmp2['value'] = $value1['name'];
                        }
                        break;
                    }
                }
                $style[] = $tmp2;
            }
            $url = FIGUREURL."/soap/club.php?act=getFigureFields";
            $json = file_get_contents($url);
            $json = json_decode($json,true);
            $json = $json['result']['data'];
            $json['weight'] = $json['lweight'];
            $json['height'] = $json['lheight'];
            unset($json['lweight']);
            unset($json['lheight']);
            $figure_item = array();//=====  量体参数  =====
            foreach ($json as $key => $value)
            {
                $tmp3['name'] = $value['zname'];
                $val = "";
                if ($order_figure_info[$key])
                {
                    $val = $order_figure_info[$key];
                }
                $cm = "cm";
                if ($key == 'weight')
                {
                    $cm = "kg";
                }
                $tmp3['value'] = $val;
                $tmp3['unit'] = $cm;
                $figure_item[] = $tmp3;
            }
            $order_figure['special'] = $special;
            $order_figure['style'] = $style;
            $order_figure['figure'] = $figure_item;
            $order_figure['cus'] = $cus;
            $this->result = $order_figure;
            return $this->sresult();
        }
        
        /**
        *获得量体字段项目
        *@author liang.li <1184820705@qq.com>
        *@2016年1月14日
        */
        function getLiangtiFigure() 
        {
            $url1 = FIGUREURL."soap/club.php?act=getFigureFields";
            $url2 = FIGUREURL."soap/club.php?act=getSpecialFields";
            $fields = json_decode(file_get_contents($url1),true)['result']['data'];
            $special_fields = file_get_contents($url2);
         return $fields;
        }
        
        /**
        *获得量体师列表
        *@author liang.li <1184820705@qq.com>
        *@2015年10月22日
        */
        function getLiangti($data) 
        {
            $region_id = isset($data->region_id) ? $data->region_id : '';
            $token = isset($data->token) ? $data->token : '9e0cb5e638ab06edab708b14a4c9ba73';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            $user_name = $user_info['user_name'];
            $membner_lv_id = $user_info['member_lv_id'];
            if (!$user_info)
            {
                return $this->tresult();
            }
            if (!$region_id)
            {
                $this->eresult('缺少region_id参数');
            }
            
            $serve_mod = &m("serve");
            $member_mod = m('member');
            if ($membner_lv_id == 1) //=====  消费者  =====
            {
                $member_invite_mod = m('memberinvite');
                $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
                if ($invite_info)
                {
                    $inviter_id = $invite_info['inviter'];
                    $invi_info = $member_mod->get_info($inviter_id);
                    $invi_member_lv_id = $invi_info['member_lv_id'];
                }
            }
            
            if (! empty($keyword)) {
                $conditions = " AND (member.real_name = '{$keyword}' OR member.phone_mob = '{$keyword}')";
            }
            
            $servelist = $serve_mod->find(array(
                "conditions" => "region_id='$region_id'  AND shop_type IN (1,2)",
                'index_key' => 'userid',
            ));
            $title;
            $userids = array();
            $address = array();
            foreach ($servelist as $key => $val) 
            {
                
                if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
                {
                    $servelist[$key]['is_free'] = 1;
                    if ($val['storetype'] == 2) //=====    自营门店  =====
                    {
                        $servelist[$key]['is_free'] = 0;
                       /*  if (($val['mobile'] == $user_name) || ($val['mobile'] == $invi_info['user_name']))
                        {
                            $servelist[$key]['is_free'] = 0;
                        }
                        else
                        {
                            //unset($servelist[$key]);
                        } */
                    }
                }
                else
                {
                    $servelist[$key]['is_free'] = 0;
                    if ($val['storetype'] == 2) //=====    自营门店  =====
                    {
                        //unset($servelist[$key]);
                    }
                }
                
               /*  $servelist[$key]['is_free'] = 1;
                if ($val['storetype'] == 2) //=====    自营门店  =====
                {
                    //=====  根据手机号匹配   =====
                    if (($membner_lv_id == VIP_ID && $val['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $val['mobile'] == $invi_info['user_name']))
                    {
                        $servelist[$key]['is_free'] = 0;
                    }
                    else
                    {
                        unset($servelist[$key]);
                    }
                } */
                
                if (! empty($servelist[$key]["region_id"])) {
                    $address[$servelist[$key]['userid']] = $servelist[$key]["serve_address"]; // 店长+地址
                    $userids[] = $servelist[$key]["userid"];
                }
            }
//  return $servelist;           
            $region_mod = &m("region");
            
            $rinfo = $region_mod->get("region_id='$region_id'");
            
            $title = $rinfo["region_name"];
            
            $member_mod = &m("member");
            // 量体师
            $members = $member_mod->find(array(
                "conditions" => "figure_liangti.alone=1 AND figure_liangti.manager_id " . db_create_in($userids) . $conditions,
                'join' => "has_lt",
                'fields' => "member.real_name,member.avatar,member.user_id, member.phone_mob, figure_liangti.manager_id"
            ));
            // 加个店长，需求变更，只能这么解决了 
             $managers = $member_mod->find(array(
                "conditions" => "alone=1 AND user_id " . db_create_in($userids),
                 'fields' => "member.real_name, member.user_id,member.avatar,member.phone_mob",
            )); 
            $rData = array();
            foreach ((array) $members as $key => $val) 
            {
                $val['address'] = $address[$val["manager_id"]];
                if ($val['avatar']) 
                {
                    $val['avatar'] = FIGUREPC.$val['avatar'];
                }
                $val['is_free'] = $servelist[$val['manager_id']]['is_free'];
                $val['serve_name'] = $servelist[$val['manager_id']]['serve_name'];
                $val['money_name'] = 100;
                $rData[] = $val;
            }
            
             foreach ((array) $managers as $key => $val)
             {
                $val['address'] = $address[$val["user_id"]];
                if ($val['avatar']) 
                {
                    $val['avatar'] = FIGUREPC.$val['avatar'];
                }
                $val['is_free'] = $servelist[$key]['is_free'];
                $val['serve_name'] = $servelist[$key]['serve_name'];
                $val['money_name'] = 100;
                $rData[] = $val;
            }  
            $sort = array(
                'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field'     => 'is_free',       //排序字段
            );
            $arrSort = array();
            foreach($rData AS $uniqid => $row)
            {
                foreach($row AS $key=>$value)
                {
                    $arrSort[$key][$uniqid] = $value;
                }
            }
            if($sort['direction'])
            {
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $rData);
            }
            $this->result = $rData;
            return $this->sresult();
        }
        
        /**
         * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
         * @param array $arr 将要排序的数组
         * @param string $keys 指定排序的key
         * @param string $type 排序类型 asc | desc
         * @return array
         */
        function arraySort($arr, $keys, $type = 'asc') {
            $keysvalue = $new_array = array();
            foreach ($arr as $k => $v){
                $keysvalue[$k] = $v[$keys];
            }
//           return $keysvalue;
            $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
            reset($keysvalue);
            foreach ($keysvalue as $k => $v) {
                $new_array[$k] = $arr[$k];
            }
//         return $keys;
            return $new_array;
        }
        
        function arr_sort($array,$key,$order="asc")
        {//asc是升序 desc是降序
            $arr_nums=$arr=array();
            foreach($array as $k=>$v){
                $arr_nums[$k]=$v[$key];
            }
            if($order=='asc'){
                asort($arr_nums);
            }else{
                arsort($arr_nums);
            }
            foreach($arr_nums as $k=>$v){
                $arr[$k]=$array[$k];
            }
            return $arr;
        }
        
        
        /**
        *添加购物车
        *@author liang.li <1184820705@qq.com>
        *@2015年10月23日
        */
        function addCart($data) 
        {
            $token = isset($data->token) ? $data->token : '';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            if (!$user_info)
            {
                return $this->tresult();
            }
            unset($data->token);
            unset($data->act); 
            $post_arr = object_array($data);
            $post_arr['type'] = 'suit'; 
            if ($post_arr['figureid'] && !$post_arr['figurerid']) 
            {
                $post_arr['figurerid'] = $post_arr['figureid'];
            }
            $goods = &gt($post_arr['type']);
            $f_id = $post_arr['f_id'];
            $post = $goods->_format_post($post_arr);
            $post['f_id'] = $f_id;
            if(!$post) 
            {
                return $this->eresult($goods->get_error());
            }
            $post['user_id'] = $user_id; 
            
            $res = $goods->add($post);
            if (!$res) 
            {
                return $this->eresult($goods->get_error());
            }
            return $this->sresult();
        }
        
        /**
        *diy加入购物车
        *@author liang.li <1184820705@qq.com>
        *@2015年11月26日
        */
        function addCartDiy($data) 
        {
            $token = isset($data->token) ? $data->token : '';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            if (!$user_info)
            {
                return $this->tresult();
            }
            //生成api_auth_code
            do{
                $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
            
            unset($data->token);
            unset($data->act);
            $post_data= object_array($data);
     
            $size = $post_data['size'];
            $figuretype = $post_data['figuretype'];
            if ($figuretype != -2) 
            {
                $size = "diy";
            }
            
            //=====  统一处理参数名  =====
            if ($post_data['serveid']) 
            {
                $post_data['server_id'] = $post_data['serveid'];
            }
            if ($post_data['figureid'] && !$post_data['figurerid']) 
            {
                $post_data['figurerid'] = $post_data['figureid']; 
            }
            $post_data['params'] = str_replace('&amp;source=app', "", $post_data['params']);
           $post_data['params'] = stripslashes_deep($post_data['params']);
            //$post_data = str_replace('\\"', '"', $post_data);
            $post['type'] = "diy";
            $post['v'] = 2; 
            $post['params'] = $post_data['params'];
            unset($post_data['params']);
            //$size = $post_data['size'];
            $post_data['size'] = $size;
            if ($figuretype == -2) //=====  标准码  =====
            {
                $size = array();
                //size=0003|160/84,0004|160/88&params=11&figuretype=-2
                $size_str = $post_data['size'];
                $size_arr = explode(",", $size_str);
//           return $size_arr;
                if ($size_arr) 
                {
                    foreach ($size_arr as $key => $value) 
                    {
                        if (!$value) 
                        {
                            continue;
                        }
                        $value_arr = explode("|", $value);
//            return $size_arr;
                        if (count($value_arr) != 2) //=====  数据传错  =====
                        {
                            continue;
                        }
                        $size[$value_arr[0]] = $value_arr[1];
                    }
                }
//         return $size;
//                 $size['size'] = $size;
                unset($post_data['size']);
//         return $size;
                $post_data['size'] = $size;
            }
//      return $post_data;
            $post['size'] = json_encode($post_data);
            $url = SHOPURL."cart-add.html?token=".$api_token."&source=app";
           // return $this->sresult();
//     return $post;
// return $url;
//             $res = $this->send_post($url,$post);
            $res = $this->http_post_data($url,$post);
//    return $res;
            if (!$res) 
            {
               return $this->eresult('没有任何返回值');
            }
       
// return array(0=>$res);
//             $res = '{"done":true,"msg":"\u6dfb\u52a0\u8d2d\u7269\u8f66\u6210\u529f","retval":{"goods_num":107,"amount":72326}}';
//             return $res;
            $res2 = json_decode($res,true);
// return $res2;
            if ($res2['done']) 
            {
                return $this->sresult($res2['msg']);
            }
            else 
            {
                return $this->eresult($res2['msg']);
            }
      
    /*         $post_arr['params'] = '{"0003":{"cstr":["35:37","50:51","212:213","189:194","143:145","164:165","177:179","350:1344","34978
:34979","34981:34982","35491:35492"],"fabric":"DBN328A`","embroidery":[],"image":"http://storage.diy.dev
.mfd.cn:8080/uploads/DBN328A/10004/fd5616df066249eb987d57034f982907.png"},"0004":{"cstr":["2032:2034"
,"2046:2049","2068:2345","2122:2124","2090:2094","2095:2098","2186:2615","2192:2613"],"fabric":"DBN328A"
,"embroidery":[],"image":"http://storage.diy.dev.mfd.cn:8080/uploads/DBN328A/10004/f04af2a50519b056bf198c7021d197ee
.png"}}'; */
           /*  $size = '{"size":"diy","region":"248","realname":"fasdf","phone":"13999999999","dateline":"2015-11-27","timepart"
:"am","server_id":"169","figuretype":"2"}'; */
            // 单品
            // 标准码
            //         $_POST = array(
            //             'type' => 'diy',
            //             'v'    => '2',
            //             'params' => '{"0003":{"cstr":["35:37","50:51","82:97","212:213","189:194","128:36411","143:145","164:165","375:1343|||A038","363:1345|||064C","350:1344|||064A","36187:36188","313:1339","331:332|||FLLVPX116","218:219","225:226","177:179","1039:1819","1118:65","34886:34890","75:898"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"},"0004":{"cstr":["2032:2034","2142:2146","2046:2049","2068:2345","2084:2088","2090:2094","2100:2101","2095:2098","2122:2124","2134:2136","2138:2140","2192:2613|||P045"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"}}',
            //             'size' => '{"size":{"0003":"160/88","0004":"165/84"},"figuretype":"-2"}',
            //         );
            // 现有量体
            //         $_POST = array(
            //             'type' => 'diy',
            //             'v'    => '2',
            //             'params' => '{"0003":{"cstr":["35:37","50:51","82:97","212:213","189:194","128:36411","143:145","164:165","375:1343|||A038","363:1345|||064C","350:1344|||064A","36187:36188","313:1339","331:332|||FLLVPX116","218:219","225:226","177:179","1039:1819","1118:65","34886:34890","75:898"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"},"0004":{"cstr":["2032:2034","2142:2146","2046:2049","2068:2345","2084:2088","2090:2094","2100:2101","2095:2098","2122:2124","2134:2136","2138:2140","2192:2613|||P045"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"}}',
            //             'size' => '{"size":"diy","figureid":"1243","figuretype":"5"}',
            //         );
            // 附近门店
            //         $_POST = array(
            //             'type' => 'diy',
            //             'v'    => '2',
            //             'params' => '{"0003":{"cstr":["35:37","50:51","82:97","212:213","189:194","128:36411","143:145","164:165","375:1343|||A038","363:1345|||064C","350:1344|||064A","36187:36188","313:1339","331:332|||FLLVPX116","218:219","225:226","177:179","1039:1819","1118:65","34886:34890","75:898"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"},"0004":{"cstr":["2032:2034","2142:2146","2046:2049","2068:2345","2084:2088","2090:2094","2100:2101","2095:2098","2122:2124","2134:2136","2138:2140","2192:2613|||P045"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"}}',
            //             'size' => '{"size":"diy","region":"248","realname":"测试附近","phone":"13333333333","dateline":"2015-10-27","timepart":"am","server_id":"176","figuretype":"2"}',
            //         );
            // 指定量体师
            //         $_POST = array(
            //             'type'   => 'diy',
            //             'v'    => '2',
            //             'params' => '{"0003":{"cstr":["35:37","50:51","82:97","212:213","189:194","128:36411","143:145","164:165","375:1343|||A038","363:1345|||064C","350:1344|||064A","36187:36188","313:1339","331:332|||FLLVPX116","218:219","225:226","177:179","1039:1819","1118:65","34886:34890","75:898"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"},"0004":{"cstr":["2032:2034","2142:2146","2046:2049","2068:2345","2084:2088","2090:2094","2100:2101","2095:2098","2122:2124","2134:2136","2138:2140","2192:2613|||P045"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"}}',
            //             'size'   => '{"size":"diy","region":"248","address":"指定量体师地址","realname":"指定sin","phone":"13333333333","dateline":"2015-10-27","timepart":"1","figurerid":"1591","figuretype":"6"}',
            //         );
            // 操作记录
            //         $_POST = array(
            //             'type' => 'diy',
            //             'v'    => '2',
            //             'params' => '{"0003":{"cstr":["35:37","50:51","82:97","212:213","189:194","128:36411","143:145","164:165","375:1343|||A038","363:1345|||064C","350:1344|||064A","36187:36188","313:1339","331:332|||FLLVPX116","218:219","225:226","177:179","1039:1819","1118:65","34886:34890","75:898"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"},"0004":{"cstr":["2032:2034","2142:2146","2046:2049","2068:2345","2084:2088","2090:2094","2100:2101","2095:2098","2122:2124","2134:2136","2138:2140","2192:2613|||P045"],"fabric":"DBN328A","embroidery":[],"image":"http://diy.storage.dev.mfd.cn/uploads/DBN343A/10004/e6544b0f5dd80b0f779c61e917823401.png"}}',
            //             'size' => '{"size":"diy","history":"1","history_id":"0","figuretype":"5","figureid":"1243"}',
            //         );
            
        }
        
        /**
         * 发送post请求
         * @param string $url 请求地址
         * @param array $post_data post键值对数据
         * @return string
         */
        function send_post($url, $post_data) 
        {
        
            $postdata = http_build_query($post_data);
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    //'header' => 'Content-type:application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'timeout' => 15 * 60 // 超时时间（单位:s）
                )
            );
//         return $options;
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            return $result;
        }
        
        function http_post_data($url, $data_string) {
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
        
            curl_setopt($ch, CURLOPT_POST, true);
//             curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_string));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_USERAGENT, 'mfdApp');
            
            return  curl_exec($ch);
            
            ob_start();
            curl_exec($ch);
            if($error=curl_error($ch)){
                return array('asd'=>$error);
            }
            $return_content= ob_get_contents();
            
            ob_end_clean();
            
            //return  array('info'=>curl_getinfo($ch));
            return $return_content;
        
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            return array($return_code, $return_content);
        }
            
        
        
        /**
        *格式化app传来的参数
        *@author liang.li <1184820705@qq.com>
        *@2015年11月26日
        */
        function formatParams($post_arr) 
        {
            //http://api.dev.mfd.cn:8080/soap/cart.php?act=addCart&token=41fe68ad474cb43e40865631bdd58318&quantity=1 &size=0003|160/84,0004|160/88& figuretype=-2&params=
            //{"size":{"0003":"160/84","0004":"160/88"},"figuretype":"-2"}
            //{"size":"diy","region":"248","realname":"fasdf","phone":"13999999999","dateline":"2015-11-27","timepart":"am","server_id":"169","figuretype":"2"}
            //http://api.dev.mfd.cn:8080/soap/cart.php?act=addCart&5631bdd58318&size=0004|160/84,0003|160/88
            $figuretype = $post_arr['figuretype'];
            if ($figuretype == -2)//=====  标准码  ===== 
            {
                $size = $post_arr['size'];
                
            }
            
        }
        
        /**
        *我的财富
        *@author liang.li <1184820705@qq.com>
        *@2015年10月30日
        */
        function wealth($data) 
        {
            $token = isset($data->token) ? $data->token : '';
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            if (!$user_info)
            {
                return $this->tresult();
            }
            $lv_id = $user_info['member_lv_id'];
            $order_cash_log_mod = m('ordercashlog');
            $bonuslog_mod = m('bonuslog'); 
            $member_lv_mod = m('memberlv');
            $order_mod = m('order');
            $debit_mod          = &m('debit');
            $special_code_mod   = &m('special_code');
            
            $return = array();
            $return['profit'] = $user_info['profit'];
            $return['money']  = $user_info['money'];
            $return['coin']   = floor($user_info['coin']);
            $return['point']  = floor($user_info['point']);
            $order_cash_item = $order_cash_log_mod->get(array(//=====   总的收益  =====
               'conditions' => "user_id = $user_id AND type=0 AND minus=0 " ,
                'fields'    => "sum(cash_money) as total_profit",
            ));
          
            $return['total_profit'] = $order_cash_item['total_profit'] ? $order_cash_item['total_profit'] : '0.00';
            
            $bonuslog_item = $bonuslog_mod->get(array(//=====   红包个数  =====
                'conditions' => "user_id = $user_id AND is_open = 0",
                'fields'     => "count(*) as bonus_num",
            ));
           
            $return['bonus_num'] = $bonuslog_item['bonus_num'] ? $bonuslog_item['bonus_num'] : 0;
            
            $list = $member_lv_mod->find(array(
                'conditions' => "member_lv_id < 5",//=====   大于5是创业顾问和BD  =====
                'fields' => "*",
                'order'  => "member_lv_id ASC",
            ));
           
           
            
            $secd = 0;
            $secd_name = "";
            $secd_point = 0;
            $debit_count = 0;//=====  未使用抵用券  =====
            $special_count = 0;//=====  未使用酷卡数  =====
            $order_money = 0;
            $order_amount = 0.00;
            $secd_now_name = $list[$user_info['member_lv_id']]['name'];
            if ($list)
            {
                foreach ($list as $key => $value)
                {
                    if ($value['lv_type'] == 'supplier')
                    {
                       if ($value['member_lv_id'] > $user_info['member_lv_id']) 
                       {
                           $secd = $value['member_lv_id'];
                           break;
                       }
                    }
                }
            }
            if ($secd) 
            {
                $secd_name = $list[$secd]['name'];
                $secd_point = $list[$secd]['experience'] - $user_info['point'];
                if ($secd_point < 0) 
                {
                    $secd_point = 0;
                }
            }
            if ($user_info['member_lv_id'] <=1) //=====  消费者不显示  =====
            {
                 $secd_name = "";
                 $secd_point = 0;
                 //=====  消费者酷卡和酷券的数目  =====
                 //未使用酷券数
                 $debit_count = $debit_mod->get(array(
                     'conditions' => "(user_id = '{$user_id}' or from_uid = '{$user_id}') and is_used = 0  and is_invalid = 0",
                     'fields'     => "count(id) as count, id",
                 ));
                
                 //已激活酷卡数
                 $special_count = $special_code_mod->get(array(
                     'conditions' => "cate >= 20 and to_id = '{$user_id}'",
                     'fields'     => "count(id) as count, id",
                 ));
                 $debit_count = $debit_count['count'];//未使用酷券数
                 $special_count = $special_count['count'];//已使用酷卡数
                 $moth_time = 2592000; 
                 $cha_time = time() - $moth_time;
                 $order_item = $order_mod->get(array(
                    'conditions' => "user_id = $user_id AND status  NOT IN(0,11,80) AND add_time>$cha_time", //=====  未支付 已取消 已退货的订单不算总的消费  =====
                    'fields'     => "sum(order_amount) as order_amount",
                 ));
                 $order_amount = $order_item['order_amount'];
            }
            
            //=====  卡券个数 总业绩   =====
            $conditions_debit = "user_id =".$user_id;
            $conditions_special_code = "to_id = '{$user_id}'";
            if ($lv_id > 1)
            {
                $member_invite = m('memberinvite');
                $conditions = "inviter = $user_id AND type=0";
                $list = $member_invite->find(array(
                    'fields'        => "*",
                    'conditions'    => $conditions,
                ));
                if ($list)
                {
                    $invite_id_arr = i_array_column($list, 'invitee');
                }
                $conditions_debit = "from_uid = $user_id AND user_id != 0 ";//=====  已经赠送出去的酷券数量  =====
                $conditions_special_code = "from_id = '{$user_id}' AND to_id != 0 ";//=====  已经赠送出去的酷券数量  =====
            }
            $invite_id_arr[] = $user_id;
            $user_id_str = db_create_in($invite_id_arr,'user_id');
            $conditions = " AND status NOT IN (".ORDER_PENDING.",".ORDER_CANCELED.")";
            //=====  总业绩  =====
            $order_info = $order_mod->get(array(
                'conditions' => $user_id_str.$conditions,
                'fields'   => "sum(order_amount) as total",
            ));
          
            $return['total_performance'] = isset($order_info['total']) ? $order_info['total'] : 0.00;//=====  总业绩  =====
            
           
            //=====  抵用券个数  =====
            $debit_info = $debit_mod->get(array(
                'conditions' => $conditions_debit,
                'fields'     => "count(*) as num",
            ));
           
           
            $return['debit_num'] = $debit_info['num'];
            $weath['invite_num'] = count($invite_id_arr) - 1;//=====  邀请会员总数  =====
            
            //=====  已发送酷卡个数  =====
            $special_code_info = $special_code_mod->get(array(
                'conditions' => $conditions_special_code,
                'fields'     => "count(*) as num ,sum(work_num) as money",
            ));
            $return['s_num'] = $special_code_info['num'];
            
            //=====   订单数量  =====
            $order_num_info = $order_mod->get(array(
                'conditions' => "user_id = $user_id".$conditions,
                'fields'     => "count(*) as num",
            ));
            $return['order_num'] = $order_num_info['num'];
            
            $return['secd_name'] = $secd_name;
            $return['secd_point'] = $secd_point;
            $return['secd_now_name'] = $secd_now_name;
            $return['member_lv_id'] = $user_info['member_lv_id'];
            $return['debit_count'] = $debit_count;
            $return['special_count'] = $special_count;
            $return['order_amount'] = $order_amount ? $order_amount : '0.00';
            $this->result = $return; 
            return $this->sresult(); 
        }
        
        /**
        *content
        *@author liang.li <1184820705@qq.com>
        *@2015年11月26日
        */
        function getSize($data) 
        {
            $pinlei =isset($data->pinlei) ? $data->pinlei : '';
            if (!$pinlei) 
            {
                return $this->eresult('参数错误');
            }
            $pinlei_arr = explode(",",$pinlei);
            if (!$pinlei_arr) 
            {
                return $this->eresult('参数错误');
            }
            $pinlei_size = array();
            $pinlei_name_arr = include_once PROJECT_PATH.'includes/data/config/pinlei.php';
// return $pinlei_arr;            
            foreach ($pinlei_arr as $key => $value) 
            {
                $tmp = array();
                $filename = PROJECT_PATH.'includes/data/config/size_json/'.$value.'_10205.size.json';
                if (file_exists($filename)) 
                {
                    $jsonString = file_get_contents($filename);
                    $jsonData = json_decode($jsonString,true);
                    $size_list = $jsonData['sizeAll'] ;
                    $size = array();
                    foreach ($size_list as $key1 => $value1)
                    {
                        $size[] = $value1['Id'];
                    }
                    $tmp['size'] = $size;
                    $tmp['size_name'] = $pinlei_name_arr[$value]['name'];
                    $tmp['pinlei'] = $value;
                    $pinlei_size[] = $tmp;
                };
            }
            
            $this->result = $pinlei_size;
            return $this->sresult();
        }
        
        /**
        *content
        *@author liang.li <1184820705@qq.com>
        *@2016年1月19日
        */
        function getFigureMoney() 
        {
            $this->result = 100;
            return $this->sresult();
        }
        
        
        
    }
   