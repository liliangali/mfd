<?php
class Channel extends Result
{

     function index($data) 
     {
         $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
         $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
         if($pageIndex < 1) $pageIndex = 1;
         if($pageSize < 1) $pageSize = 1;
         $limit = $pageSize*($pageIndex-1).','.$pageSize;
         
         $dis_mod = m('JpjzDissertation');
         $topicArr = $dis_mod->find(array(
             'conditions' =>"is_show =1",
             'order'      => "sort_order ASC",
             'limit'      => $limit,
         ));
         
         $aData = array();
         foreach((array)$topicArr as $key => $val){
             $aData[] = array(
                 'id'     => $val["id"],
                 "name"   => $val["title"],
                 "image"  => $val["small_img"],
             );
         }
         
         $this->result = $aData;
         return $this->sresult();
     }
  
     function getList($data){
         $topic     = isset($data->t)         ? intval($data->t) : 0;
         $token     = isset($data->token)     ? trim($data->token) : '';
         $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
         $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
         if($pageIndex < 1) $pageIndex = 1;
         if($pageSize < 1) $pageSize = 1;
         $limit = $pageSize*($pageIndex-1).','.$pageSize;
         $user_info = getUserInfo($token);
          
         $aData = $this->processPrice($user_info['user_id']);
         
         $link_mod = m("links");
         $suitrelat_mod= m('suitrelat');
         $customfabric_mod   = m('customfabric');
         $fabric_mod = m('fabric');
         
         $customArr = $link_mod->find(array(
             "conditions" => "links.d_id = '{$topic}' AND suit_list.is_sale=1 AND (suit_list.to_site = '0' OR suit_list.to_site = 'app')",
             "join"       => "app_custom",
             'order'      => "links.lorder ASC",
             'limit'      => $limit,
         ));
         
         $cData = array();
         foreach((array) $customArr as $key => $val){
             if($val['is_promotion']){
                 $val["price"] = $val["promotion_price"];
             }
             if(!empty($user_info)){
                 //  $val['price'] = _format_price_int($val['price'] * $user_info['dis_count']);
                 if(!in_array($val['category'], $aData) && $val["is_first"] == 1){
                     switch ($val['category']){
                         case "0003": //西装
                             $val["price"] = "999";
                             break;
                         case "0006": //男衬衣
                             $val["price"] = "199";
                             break;
                         case "0016":  //女衬衣
                             $val["price"] = "199";
                             break;
                     }
                 }
             }
             
             $val['woman_price'] = '';
             if ($val['d_id'] == 16) //=====  判断如果是女装系列 则商品价格是八折  =====
             {
                 $val['woman_price'] = '';
             }
             $val['price'] = intval($val['price']);
             
             $cData[] = $val;
         }
         //return $cData;
         // 每个套装下面 取出 所有的 单品

         $pids = i_array_column($cData, 'c_id');
         
         $jiben = $suitrelat_mod->findAll(array(
             "conditions"=>db_create_in($pids,'tz_id'),
             'index_key' => '',
         )); 
      
         foreach ($jiben as $kk=>$vv){
             $js = explode(",", $vv['jbk_id']);
             $jbks[$vv['tz_id']] = array_values(array_filter($js));
            
         }
         $temp = $jbks;// 获取套装 和 基本款的关系 数组
         foreach($jbks as $key => $val) {
             foreach($val as $value) {
                 $new_arr[] = $value;
             }
         }
         
         //去除重复的数据
         //$one_arr = array_unique($new_arr);
        
         $custom = $customfabric_mod->findAll(array(
             "conditions" =>db_create_in($new_arr,'custom_id')." AND is_default=1",
             "fields"     => "item_id,custom_id", 
         ));
         
        $tmp_kk = $custom;// 基本款 和 面料ID 关系数组
        $p = i_array_column($custom, 'item_id');
     
        $fs =$fabric_mod->findAll(array(
              "conditions" =>db_create_in($p,'ID')." ",
              "fields"     => "CODE,d_s,is_sale,STOCK",
             
         )); 
      
        $tmp_arr = array();
        foreach ($fs as $key => $value) {
            foreach ($tmp_kk as $k => $v) {
                if($v['item_id']==$key){
                    $tmp_kk[$k]['fabricinfo']=$value;
                    $tmp_arr = $tmp_kk;
                }
            }
        }
        // 合成新数组 面料 详情 和 套装 基本款 
        $ttp = array();
        foreach ($tmp_arr as $kk => $vv) {
            foreach ($temp as $k1 => $v1) {
                if(in_array($vv['custom_id'], $v1)){
                    $tmp_arr[$kk]['suit_id'] = $k1;
                    $ttp = $tmp_arr;
                }
            }
            
        }
      
        // 注释 ： 如果 有多个面料可能会出现 同一面料符合多个 套装（相同的基本款）
        //  组合数组 判断条件
        foreach ($cData as $k => $v) {
             foreach ($ttp as $k2 => $v2) {
                if($v['c_id'] == $v2['suit_id']){
                    $cData[$k]['fabr'] = $v2;
                }
             }
        }
    
        foreach ($cData as $k3 => $v3) {
            if($v3['fabr']){
                if($v3['fabr']['fabricinfo']){
                    if($v3['fabr']['fabricinfo']['d_s'] ==2){
                        $cData[$k3]['xj'] = 2;
                    }elseif($v3['fabr']['fabricinfo']['STOCK'] <5){
                        $cData[$k3]['xj'] = 1;   
                    }
                }
            }
        }
        
        $this->result = $cData;
        return $this->sresult();
     }
     
     function processPrice($userid){
         if(!$userid) return array();
         $mod_first = m("orderfirstlog");
         $list = $mod_first->find(array(
             "conditions" => "user_id='{$userid}' AND is_active=1",
         ));
      
         $aData = array();
      
         foreach((array) $list as $key => $val){
            $aData[] = $val["cloth"];
         }
        
         return $aData;
     }
	  
}

