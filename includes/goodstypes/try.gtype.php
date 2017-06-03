<?php
/**
 * 试衣间商品购物车类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: try.gtype.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package normal.gtype.php
 */
class TRyGoods extends BaseGoods
{
    public $mGoods;
    public $mFabric;
    public $mfType;
    function __construct()
    {
        $param['_name']         = 'try';
        $param['_order_type']   = 'try';
        $this->mGoods  = &m('goods');
        $this->mFabric = &m('fabric');
        $this->mfType  = &m('fabrictype');
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
        $res['goods']     = isset($post['goods']) ? $post['goods'] : array();
        return $res;

    }
    /**
     * 定制商品加入购物车
     * @param array $post post数据
     * 
     * @version 1.0.0 (2014-11-23)
     * @author Ruesin
     */
    function add($post)
    {
        //验证数据啊   比如 面料
        //..
        $pData = $this->_product_info($post);
        if(!$pData) return false;
        foreach ($pData as $Row){
            
            $post['product_id'] = $Row['product_id'];
            $post['goods_id']   = $Row['goods_id'];
            $post['try_items']  = $Row['goods_spec'];
            $post['fabric']     = $post['goods'][$post['goods_id']]['fid']; 
            $cartInfo = $this->_cart->get(array(
                    'conditions' => " fabric = '{$post['fabric']}' AND city_id = '".$post['city_id']."' AND product_id = '".$post['product_id']."' AND user_id = '".$post['user_id']."'"
            ));
            //更新|追加
            if(!empty($cartInfo)){
                $post['quantity']   = $cartInfo['quantity']+1;
                $post['id']         = $cartInfo['id'];
                $resUp = $this->update($post);
                if($resUp)continue;
                //return $res;
            }
            $stock = $this->_check_stock($post);
            if($stock < 0){
                $this->_error("goods_stock_error");
            }
            $aSave[] = array(
                    'params'       => $this->_generateParams($post),
                    'goods_id'     => $post['goods_id'],
                    'product_id'   => $post['product_id'],
                    'fabric'       => $post['fabric'],
                    'user_id'      => $post['user_id'],
                    'city_id'      => $post['city_id'],
                    'type'         => $this->_name,
                    'quantity'     => $post['quantity'],
                    'time'         => gmtime(),
                    'need_measure' => 1, //需要量体
            );
            
        }
        
        $res = $this->_cart->add($aSave);
        if(!$res && !$resUp)
        {
            $this->_error("add_to_cart_error");
        }
        return true;
    	
    }
    /**
     * 删除定制商品购物车项
     * @param array $post Post数据
     * 
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function drop($post){
    
        $droped_rows = $this->_cart->drop("id='".$post['id']."' AND user_id = '".$post['user_id']."' ");
        
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
        
        return true;
    }
    /**
     * 获取货品信息
     * 
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _product_info ($post = array())
    {
        if(empty($post))return;
        $ps = $this->_mod_product->find(db_create_in($post['goods_id'],'goods_id'));
        
        foreach ($post['goods'] as $row){
            foreach ($row['trys'] as $r){
                $spec[$r['attr_id']] = $r['attr_value'];
            }
            ksort($spec);
            $tj[$row['gid']] = serialize($spec);
        }
        
        foreach ($ps as $r){
            if($tj[$r['goods_id']] == $r['goods_spec']){
                $return[$r['product_id']] = $r;
            }
        }
        return $return;
    }
    /**
     * 获取购物车项小计
     * 
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _get_subtotal(&$item){
        $cates = array(19 => '上衣',20 => '西裤',21 => '衬衣',22 => '马夹',23 => '大衣');
        //线上分类 //这个动了会有问题
        $cat = array(
                19 => 'fabric_price_sy',
                20 => 'fabric_price_xk',
                21 => 'fabric_price_cy',
                22 => 'fabric_price_mj',
                23 => 'fabric_price_dy',
        );
//         //本地测试使用
//         $cat = array(
//                 1 => 'fabric_price_sy',
//                 2 => 'fabric_price_xk',
//                 3 => 'fabric_price_cy',
//                 4 => 'fabric_price_mj',
//                 15 => 'fabric_price_dy',
//         );
        
        $gs = $this->mGoods->get($item['goods_id']);
        
        //$fs = $this->mFabric->get($item['fabric']);
        //$ft = $this->mfType->get($fs['cate_id']);
        $ft = $this->mfType->get(array(
            'conditions' => "f.fabric_id = '{$item['fabric']}'",
            'join'       => 'has_fabric'
        ));
        $dj = $ft[$cat[$gs['type']]];
        
        $item['subtotal']  = $item['quantity'] * $dj;
    }
    
    
    /**
     * 获取商品信息
     * 
     * @version 1.0.0 (Jan 7, 2015)
     * @author Ruesin
     */
    function _get_goods($ids = ''){
        $a1 = @explode(',', $ids);
        foreach ((array)$a1 as $r){
            $a2 = @explode('_', $r);
            if(!isset($a2[1]) || $a2[1] == null){
                $this->_error('params_error');
                return;
            }
            $goods[$a2[0]]['gid'] = $a2[0];
            $goods[$a2[0]]['fid'] = $a2[1];
        }
        return $goods;
    }
    /**
     * 获取工艺(属性)信息
     * 
     * @version 1.0.0 (Jan 7, 2015)
     * @author Ruesin
     */
    function _get_crafts(&$goods,$aid,$s){
        
        if($s != ''){
            $aid = array_merge(explode(',', $aid),$this->_get_aid_from_s($s));
        }

        $mga = &m('goodsattr');
        $ga = $mga->find(db_create_in(array_map('array_shift', $goods),'goods_id'));
        $gas = $mga->find(db_create_in($aid,'goods_attr_id'));
        foreach ($gas as $r){
            $goods[$r['goods_id']]['trys'][$r['attr_id']] = $r;
        }
        foreach ($ga as $k=>$r){
            if(empty($goods[$r['goods_id']]['trys'][$r['attr_id']])){
                if($r['is_default']){
                    $goods[$r['goods_id']]['trys'][$r['attr_id']] = $r;
                }
            }
        }
        
    }
    /**
     * 通过筛选项目s获取属性id
     * 
     * @version 1.0.0 (Jan 7, 2015)
     * @author Ruesin
     */
    function _get_aid_from_s($s){
        $a1 = @explode(',', $s);
        foreach ((array)$a1 as $r1){
            $a2 = @explode('_', $r1);
            if(count($a2) != 2) continue;
            foreach ((array)$a2 as $r2){
                $a3 = @explode('|', $r2);
                foreach ((array)$a3 as $r3){
                    $a4 = @explode('-', $r3);
                    if( isset($a4[1]) && (string)$a4[1] != '' && intval($a2[0]) ){
                        $sql[] = " goods_id = '{$a2[0]}' AND attr_id = '{$a4[0]}' AND attr_value = '{$a4[1]}'";
                    }
                }
            }
        }
        if(!empty($sql)){
            $mga = &m('goodsattr');
            foreach ($sql as $r){
                $v = $mga->get(array(
                        'conditions' => $r,
                        'fields'     => 'goods_attr_id',
                ));
                $aid[] = $v['goods_attr_id'];
            }
        }
        return $aid;
        
    }
}
