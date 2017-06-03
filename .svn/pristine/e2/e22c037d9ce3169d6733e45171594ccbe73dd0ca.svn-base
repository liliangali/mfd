<?php

class DiyGoods extends BaseGoods
{
    function __construct(){
        $param['_name']         = 'diy';
        parent::__construct($param);
    }
    
    /**
     * 格式化POST数据
     * 
     * @date 2015-10-15 下午1:57:18
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        $res['btype']    = $this->_name;
        //$res['params']   = isset($post['params']) ? json_decode( str_replace('\\"', '"', str_replace('\\\\"', '"', str_replace('\\\\\\"', '"', str_replace('|||', '|sin|', $post['params'])))) ,true) : array();
        
        //$res['params']   = isset($post['params']) ? json_decode( str_replace('\\\\"', '\\"', str_replace('\\"', '"', str_replace('|||', '|sin|', $post['params']))) ,true) : array();  //由于入口文件做了字符转义  所以需要替换一下
        $res['params']   = isset($post['params']) ? json_decode( stripslashes_deep(str_replace('|||', '|sin|', $post['params'])) ,true) : array();  //由于入口文件做了字符转义  所以需要替换一下
        //$res['params']   = isset($post['params']) ? json_decode( str_replace('|||', '|sin|', $post['params']) ,true) : array();  //由于入口文件做了字符转义  所以需要替换一下
        
        $res['size']   = isset($post['params']) ? json_decode( stripslashes_deep($post['size']) ,true) : array();
        
        return $res;
    }
    
    /**
     * 校验并格式化量体信息
     *
     * @date 2015-10-6 下午3:52:21
     * @author Ruesin
     */
    function _format_figure($sizes){
        
        $figure = array();
        if(!isset($sizes['figuretype'])){
            $this->_error('请选择量体方式');
            return false;
        }

        ## 2 5 6
        if($sizes['figuretype'] == 2 || $sizes['figuretype'] == 6){
            ##地区
            $region_mod = &m('region');
            $region = $region_mod->get(" region_id = '{$sizes['region']}' AND is_serve = 1");
            if(!$region){
                $this->_error('地区错误!');
                return false;
            }
            $sizes['region_name'] = $region['region_name'];
            ##手机号
            if(!preg_match("/^1[34578]\d{9}$/", $sizes['phone'])){
                $this->_error('手机号错误!');
                return false;
            }
            ##预约时间 简易匹配
            if(!preg_match("/^2\d{3}-(1[0-2])|(0[0-9])-([012][0-9])|(3[01])$/", $sizes['dateline'])){
                $this->_error('预约时间错误!');
                return false;
            }
        }
    
        if ($sizes['figuretype'] == 2){
    
            ##门店
            $msv = m('serve');
            $server = $msv->get("region_id = '{$sizes['region']}' AND idserve = '{$sizes['server_id']}'");
            if(!$server){
                $this->_error('服务门店错误!');
                return false;
            }

            $figure = array(
                    'type_name' => '去附近门店量体',
                    'region_id' => $sizes['region'], // 服务地区
                    'region_name' => $sizes['region_name'], // 服务地区
                    'realname'  => $sizes['realname'], // 真实姓名
                    'gender'    => $sizes['gender'] ? (in_array($sizes['gender'], array(10040,10041)) ? $sizes['gender'] : 10040 ): 10040, // 性别 10040男 10041女
                    'phone'     => $sizes['phone'], // 手机号码
                    'time'      => $sizes['dateline'], // 预约时间
                    'time_noon' => $sizes['timepart'] ? (in_array($sizes['timepart'], array('am','pm')) ? $sizes['timepart'] : 'am') : 'am', // 预约时间段
                    'server_id' => $sizes['server_id'], // 门店ID
                    
                    'customer_mobile'    => $sizes['phone'],
                    'customer_name'      => $sizes['realname'],
                    
            );
            
        } elseif ($sizes['figuretype'] == 5){
            ## 已有量体
            $mCf  = &m('customer_figure');
            $customer = $mCf->get(" figure_sn = '{$sizes['figureid']}' AND figure_state = 1"); //storeid = '{$this->_user_id}' AND customer_mobile = '{$_POST['data']['phone']}'
    
            if(!$customer){
                $this->_error('现有量体错误!');
                return false;
            }
            
            $figure = array(
                    'customer_mobile'      => $customer['customer_mobile'],
                    'customer_name'      => $customer['customer_name'],
                    'history_id' => $sizes['figureid'],  //历史量体数据ID
                    'type_name'  => '现有量体数据'
            );
            
        } elseif ($sizes['figuretype'] == 6){
    
            ##量体师  门店/地区
            $ltId = isset($sizes['figurerid']) ? intval($sizes['figurerid']) : 0;

            $mLiangti = &mr('figure_liangti');
            $liangti = $mLiangti->get("liangti_id = '{$ltId}' AND alone = '1' ");
            
            $mMember = &m('member');
            if (!$liangti){
                $dz = $mMember->get(" user_id = '{$ltId}' AND alone='1' AND figure_type = '1' AND serve_type = '2' ");
                $dId = $dz['user_id'];
            }else{
                $dId = $liangti['manager_id'];
            }
            
            if(!$dId){
                $this->_error('该量体师不可独立量体!');
                return false;
            }

            $mServe = &m('serve');
            $server = $mServe->get("userid = '{$dId}' AND region_id = '{$sizes['region']}' AND shop_type IN (1,2)"); // AND virtual = '0'
            if(!$server){
                $this->_error('量体师所在门店错误!');
                return false;
            }
    
            $users = $mMember->get(" user_id = '{$ltId}'");
            if(!$users){
                $this->_error('该量体师会员信息缺失!');
                return false;
            }

            $figure = array(
                    'region_id' => $sizes['region'], // 服务地区
                    'region_name' => $sizes['region_name'], // 服务地区
                    'liangti'   => $sizes['figurerid'], // 指定量体师ID
                    'realname'  => $sizes['realname'], // 真实姓名
                    'phone'     => $sizes['phone'], // 联系电话
                    'time'      => $sizes['dateline'], // 预约时间
                    'time_noon' => $sizes['timepart'] ? (in_array($sizes['timepart'], array('am','pm')) ? $sizes['timepart'] : 'am') : 'am', // 预约时间段
                    'liangti_name' => $users['real_name'], // 指定量体师姓名
                    'server_id' => $server['idserve'], // 指定量体师所在门店
                    'type_name' => '指定量体师', // 量体方式类型
                    
                    'customer_mobile'    => $sizes['phone'],
                    'customer_name'      => $sizes['realname'],
                    
                    //$sizes['address']
            );
        }
        if (isset($figure['phone']) && $figure['phone'] != ''){
            $figure['phone'] = $figure['phone'].'';
        }
        $figure['type_id'] = $sizes['figuretype'];
        if(!$sizes['history']){
            $cki = $sizes;
            unset($cki['goods_id']);
            unset($cki['quantity']);
            $this->_cki = $cki;
        }
        
        return $figure;
    }
    
        
    function add($post = array())
    {
        $time = gmtime();
        $sizes = array();
        $dis_ident = $first = '';
        
        if(!$post['params'] || count($post['params']) <1){
            $this->_error("没有商品!");
            return false;
        }
        
        ## 校验库存 Bgn
        foreach ($post['params'] as $key=>$row){
            $stk[$row['fabric']][$key] = array(
                    'cloth'    => $key,
                    'fabric'   => $row['fabric'],
                    'quantity' => $post['quantity'],
            );
            $fbs[$row['fabric']] = $row['fabric'];
        }
        
        $fCart = $this->_cart->find("user_id = '{$post['user_id']}' AND " .db_create_in($fbs,'fabric'));
        
        foreach ($fCart as $row){
            $stk[$row['fabric']][$row['cloth']]['cloth']    = $row['cloth'];
            $stk[$row['fabric']][$row['cloth']]['fabric']   = $row['fabric'];
            $stk[$row['fabric']][$row['cloth']]['quantity'] += $row['quantity'];
        }
        
        $stock = $this->_check_stock($stk);
        if(!$stock){
            $this->_error($this->get_error());
            return false;
        }
        ## 校验库存 End
        
        $dis_id = 0;
        if(count($post['params']) > 1 ){
            $dis_ident = $this->dis_ident($post['user_id']);
        }else{
            $fCloth = $this->_cart->firstCloth();
            $cid = key($post['params']);
            if($fCloth[$cid]){
                $has = $this->_cart->get("user_id = '{$post['user_id']}' AND first = '{$cid}'");
                if(!$has){  //购物车中目前没有
                    $mOfl = &m('orderfirstlog');
                    $has  = $mOfl->find("user_id = '{$post['user_id']}'  AND cloth = '{$cid}' AND is_active = '1'");
                    if (!$has){  //之前订单也没有享受过
                        if($fCloth[$cid]['fabric'][$post['params'][$cid]['fabric']]){  //验证是首推面料
                            $first = $cid;
                        }
                    }
                }
            }
        }
        
        $data = array(
                'user_id'      => $post['user_id'],
                'dis_ident'    => $dis_ident,
                'suit_id'      => 0,
                'goods_id'     => 0,
                'type'         => $this->_name,
                'quantity'     => $post['quantity'],
                'time'         => $time,
                'first'        => $first,
                'figure'       => '',
                'source_from'  => 'app',
                'session_id' => SESS_ID,
        );
        
        ## 量体信息
        $sizes = $post['size'];
        if ($sizes['size'] == 'diy'){
            $figure = $this->_format_figure($sizes);
            if (!$figure){
                $this->_error('量体方式有误!');
                return false;
            }
            $data['figure'] = json_encode($figure,JSON_UNESCAPED_UNICODE);
        }
         
        ## 刺绣准备信息
        $mEp  = &m('dict_embs_parent');
        $p = $mEp->find(array(
                'conditions' => " is_show = '1' AND statusid IN ('10008','10002')",
        ));
        foreach ($p as $v){
            if($v['statusid'] == 10008){
                $embNr[$v['cCode']] = $v;
            }else{
                $embWz[$v['cCode']] = $v;
            }
        }
        foreach ($post['params'] as $key=>$row){
            
            ## 尺码
            $row['size'] = 'diy';
            if (isset($sizes['size']) && $sizes['size'] != 'diy' ){
                if (!$sizes['size'][$key]){
                    $this->_error('尺码错误!');
                    return false;
                }
                $row['size'] = $sizes['size'][$key];
            }
            ## 图片
            $row['image_url'] = '/upload/order/diy/'.md5(serialize($row['cstr']).'-f-'.$row['fabric'].'-u-'.$post['user_id'].'-c-'.$key).'.jpg';
            if (!is_file(ROOT_PATH.$row['image_url']))
                file_put_contents(ROOT_PATH.$row['image_url'] , base64_decode(str_replace('data:image/jpeg;base64,', '', $row['image'])));
            
            ## 刺绣信息 一项为空，全部为空
            if(isset($row['embroidery']) && !empty($row['embroidery'])){
                $ncx = 0;
                foreach ($row['embroidery'] as $k=>$v){
                    $ear = @explode(':', $v);
                    if(count($ear) == 1){
                        $row['cixiu'][$embNr[$key]['id']] = $v;
                        if(!$v) $ncx = 1;
                    }else{
                        if($embWz[$key]['id'] == $ear[0]){
                            $row['cixiu'][$ear[0]][$ear[1]] = $ear[1];
                        }else{
                            $row['cixiu'][$ear[0]] = $ear[1];
                        }
                        if(!$ear[1]) $ncx = 1;
                    }
                    if($ncx){
                        $row['cixiu'] = array();
                        break;
                    }
                }
            }
            
            
            ## 工艺信息
            foreach ($row['cstr'] as $pk=>$pv){
                $vt = explode(':', $pv);
                $row['prm'][$vt[0]] = $vt[1];
            }
            
            $rData = array(
                    'ident'  => $this->gen_ident($post['user_id']),
                    'cloth'  => $key,
                    'fabric' => $row['fabric'],
                    'lining' => $row['lining'],
                    'button' => $row['button'],
                    'style'  => '',
                    'embs'   => json_encode($row['cixiu'],JSON_UNESCAPED_UNICODE),
                    'syline' => '',
                    'params' => json_encode($row['prm'],JSON_UNESCAPED_UNICODE),
                    'size'   => $row['size'] ? $row['size'] : 'diy',
                    'image'  => SITE_URL.$row['image_url'],
            );
            
            $aSave[] = array_merge($data,$rData);
            
        }
        $res = $this->_cart->add($aSave);
        
        if(!$res)
        {
            $this->_error("add_to_cart_error");
            return false;
        }
        if(isset($this->_cki)){
            $cooked = isset($_COOKIE['suit_history']) ? json_decode(base64_decode($_COOKIE['suit_history']),1) : array();
            while (count($cooked)>=6){
                array_shift($cooked);
            }
            $cookie = array_merge($cooked,array($this->_cki));
            setcookie('suit_history',base64_encode(json_encode($cookie,JSON_UNESCAPED_UNICODE)),strtotime(date('Y-m-d',strtotime('+1 day'))));
        }
        
        return $res;
    }
    
    
    
    function gen_ident($id = 0){    	do{    		$str='abcdefghigklmnopqrstuvwxyz0123456789';    		for($i=0;$i<5; $i++){    			$code .= $str[mt_rand(0, strlen($str)-1)];    		}    		$il = strlen($id);    		for ($i=$il;$i<10;$i++){    			$id = '0'.$id;    		}    		$ident =  $code.$id;    	} while($this->_cart->get("ident = '{$ident}'"));    	return $ident;    }
    

    
    /**
     * 更新购物车数据
     *
     * @author Ruesin
     */
    function update($post){
        
        $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}'",'index_key'=>'ident'));
        
        if(!$carts[$post['ident']]){
            $this->_error("update_error");
            return false;
        }
        
        foreach ($carts as $row){
            //$carts[$post['ident']]['fabric']
            if($row['fabric'] == $carts[$post['ident']]['fabric']){
                if($row['ident'] == $post['ident']){
                    $row['quantity'] = $post['quantity'];
                }
                $stk[$row['fabric']][$row['cloth']]['cloth'] = $row['cloth'];
                $stk[$row['fabric']][$row['cloth']]['fabric'] = $row['fabric'];
                $stk[$row['fabric']][$row['cloth']]['quantity'] += $row['quantity'];
            }
        }
        $stock = $this->_check_stock($stk);
        if(!$stock){
            $this->_error($this->get_error());
            return false;
        }
        
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return false;
        }
    
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."' AND first = '' ";
        
        $res   = $this->_cart->edit($where, array('quantity' => $post["quantity"]));
    
        if(!$res){
            $this->_error("update_error");
        }
         
        return $res;
    }
   
    /**
     * 删除定制商品购物车项
     * 
     * @author Ruesin
     */
    function drop($post){
    
        $droped_rows = $this->_cart->drop("ident='".$post['ident']."' AND user_id = '".$post['user_id']."' ");
        
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
        
        return true;
    }
    
    
   /**
    * 选择购物车项
    * 
    * @date 2015-8-25 下午1:47:51
    * @author Ruesin
    */
   function choice($post){
   
       $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}' AND ident = '{$post['ident']}'",'index_key'=>'ident'));
   
       if(!$carts){
           $this->_error('选择失败!');
           return false;
       }
   
       foreach ($carts as $key=>$val){
           $res[$key] = $key;
       }
   
       return $res;
   }
   
   
   
}