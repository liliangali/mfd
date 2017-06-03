<?php
namespace Cyteam\Shop;

use Cyteam\Member\Member;
use Cyteam\Cookie\Cookie;

class Shop
{
    public static function cartCookieSet($key,$val){
        
        $cart = md5(Member::getUserId().'_cart');
        
        $data = self::cartCookieGet();
        
        $data[$key] = $val;
//         var_dump($data);
        
        setcookie($cart, serialize($data), time()+86400);
    }
    
    public static function cartCookieGet($key = ''){
//         var_dump($_COOKIE['check']);
        $cart = md5(Member::getUserId().'_cart');
        
        $data = isset($_COOKIE[$cart]) ? $_COOKIE[$cart] : [];
        
        $return = $data ? unserialize(stripslashes($data)) : [];
        
        if ($key)
            return isset($return[$key]) ? $return[$key] : [];
        
        return $return;
    }
    
    public function cartChoiceSet($data){
    
    }
    
    public static function checkFabricStock($data){


        if (!$data) return '';
    
        $isDiy = 0;
        foreach ($data as $key => $row){
            $fbs[$row['product_id']] = $row['product_id'];
            $stc[$row['product_id']] = $row['quantity'];
            $isDiy = $row['type'] == 'fdiy' ? 1 : 0;
        }

        //diy商品 不存在库存
        if ($isDiy)
        {
            return true;
        }
        $mP = &m('products');
        $products = $mP->find(array('conditions'=>db_create_in($fbs,'product_id') ,'index_key'=>'product_id'));

        foreach ($stc as $key=>$val){
            if (!isset($products[$key])){
                return false;
            }
            if($products[$key]['store'] < $val){
                return false;
            }
        }
    
        return true;
    }
}