<?php
/**
 * 定制商品购物车类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: diy.gtype.php 6349 2015-07-09 07:09:14Z liugx $
 * @copyright Copyright 2014 mfd
 * @package normal.gtype.php
 */
class DiysGoods extends BaseGoods
{
	
    function __construct()
    {
        $param['_name']         = 'diy';
        $param['_order_type']   = 'diy';
        parent::__construct($param);
    }
    /**
     * 格式化POST数据
     *
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        
        $res['btype']    = $this->_name;
        $res['params']   = isset($post['params']) ? json_decode(str_replace('\\', '', str_replace('|||', '|sin|', $post['params'])) ,true) : array();
        
        return $res;
    }
    
    /**
     * 定制商品加入购物车
     * 
     * @version 1.0.0 (2014-11-23)
     * @author Ruesin
     */
    function add($post)
    {
        //查是否存在  //diy的就不要更新处理了吧?
        
        //更新|追加

        if(!$post['params'] || count($post['params']) <1){
            $this->_error("没有商品!");
            return false;
        }
        
        
        //查库存
        foreach ((array)$post['params'] as $key=>$row){
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
        
        
        $dis_id = 0;
        if(count($post['params']) >1 ){
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
                //'rec_id'       => '',
                'user_id'      => $post['user_id'],
                'dis_ident'    => $dis_ident,
                'suit_id'      => $dis_id,
                'goods_id'     => $post['goods_id'],
                'type'         => $this->_name,
                'quantity'     => $post['quantity'],
                'time'         => gmtime(),
                'first'        => $first,
        );
                
        //刺绣蛋疼的要兼容购物车列表!
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
                
        foreach ((array)$post['params'] as $key=>$row){
            
            //刺绣信息一项为空，全部为空
            $ncx = 0;
            foreach ((array)$row['embroidery'] as $k=>$v){
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
                //unset($row['cixiu'][$k]);
            }
            //unset($row['embroidery']);
            
            //将数据格式化成与PC一致
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
                    'size'   => $row['sizes'] ? $row['sizes'] : 'diy',    //size 先放这   diy页面删掉了还没加上
                    'image'  => str_replace('.png', '_178.png', $row['image']),
            );
            
            $aSave[] = array_merge($data,$rData);
        }
        $res = $this->_cart->add($aSave);
        if(!$res)
        {
            $this->_error("add_to_cart_error");
            return false;
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
    
    
    
    
    function gen_ident($id = 0){
        do{
            $str='abcdefghigklmnopqrstuvwxyz0123456789';
            for($i=0;$i<5; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = '0'.$id;
            }
            $ident =  $code.$id;
        } while($this->_cart->get("ident = '{$ident}'"));
        return $ident;
    }
    
    function dis_ident($id = 0){
        do{
            $str='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for($i=0;$i<8; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = 'D'.$id;
            }
            $ident =  $code.$id;
        } while($this->_cart->get(" dis_ident = '{$ident}'"));
        return $ident;
    }

}
