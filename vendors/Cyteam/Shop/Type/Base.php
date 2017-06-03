<?php
namespace Cyteam\Shop\Type;
use Cyteam\Shop\Shop;
abstract class Base
{
    /**
     * 抽象方法（不能包含函数体）    【必须在继承类中实现】
     * 格式化POST数据
     * @param array $post   $_POST传值
     */
    abstract public function _formatPost($post = array());
    
    
    /**
     * 抽象方法（不能包含函数体）    【必须在继承类中实现】
     * 加入购物车
     * @param array $post   $_POST传值
     */
    abstract public function add($post);
    
    
    function _error($msg, $obj=""){
        $this->error = $msg;
    }
    
    function get_error(){
        return $this->error;
    }
    
    /**
     * 选择购物车项
     *
     * @date 2015-8-25 下午1:36:53
     * @author Ruesin
     */
    function choice($post = array(),$ck='y'){
        $cartM = m("cart");
     
        $carts = $cartM->find(array('conditions'=> " user_id = '{$post['user_id']}' AND ident = '{$post['ident']}' " , 'index_key'=>'ident'));
      
        if(!$carts){
            $this->_error('选择失败!');
            return false;
        }
        $is_check = 0;
        if($ck == 'y')
        {
            $is_check = 1;
        }

        foreach ($carts as $key=>$val){
            $res[$key] = $key;
            $cartM->edit($val['rec_id'],['is_check'=>$is_check]);//选中
        }
    
        return $res;
    }
    
    /**
     * 删除购物车项
     *
     * @author Ruesin
     */
    function drop($post){
        $cartM = m("cart");
        $droped_rows = $cartM->drop("ident = '".$post['ident']."' AND user_id = ".$post['user_id'] );
         
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
         
        return true;
    }
    
    /**
     * 获取组件信息
     *
     */
    function _groupInfo($goods_id,$product_id=0){
         
        /* 获取组件数据 */
        $res = array(
            'oProducts'     => array(), //货品
            'oGoods'     => array(), //商品信息
        );
         
        $goodsM = m("goods");
        $productM = m("products");
        $data = $goodsM->get($goods_id);
         
        $res['oGoods'] = $data;
        $wherep = !empty($product_id) ? " and product_id = '{$product_id}' " : '';
        $res['oProducts'] = current($productM->find(array(
            "conditions" => "goods_id = '{$data["goods_id"]}' $wherep",
        )));
    
        return $res;
    }
    
    function _cartInfo($post = array()){
    
        $conditions = "goods_id = '{$post['product_id']}' AND user_id = '{$post['user_id']}' AND type='{$post['type']}' AND size='{$post['size']}' AND source_from ='{$post['source_from']}' ";
         
        if($post['ident']){
            $conditions .= " AND ident = '{$post['ident']}'";
        }
        $cartM = m("cart");
        $item = $cartM->get(array(
        				'conditions' => $conditions,
        ));
        return $item;
    }
    /**
     * 更新购物车数据
     *
     */
    function update($post){

    if(!isset($post['fav_id']) && !isset($post['zhek_id'])){
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return ;
        }
    }
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."'";
        $cartM = m("cart");
        $cartInfo = $cartM->get($where);
        if($cartInfo['is_try'] == 1 && $post["quantity"] >= 2)
        {
            $tres = $this->_checkTry($cartInfo['user_id']);
            if(!$tres)
            {
                $this->_error("试吃产品只能购买一次");
                return ;
            }
        }
     
        if(isset($post['fav_id'])){
            $datas=array('favorable_id' => $post["fav_id"]);
        
        }elseif(isset($post['zhek_id'])){
            $datas=array('zhek_id' => $post["zhek_id"]);
        }else{
            $datas=array('quantity' => $post["quantity"]);
        }
        
        $res   = $cartM->edit($where, $datas);
    
        if(!$res)
        {
            $this->_error("update_error");
        }
         
        return $cartInfo['rec_id'];
    }
    
    /**
     * 校验面料库存
     *
     */
    function _checkStock($data){
        

        
        $stock = Shop::checkFabricStock(array($data));
        if ($stock !== true){
            $this->_error("面料".$stock."库存不足");
            return false;
        }
        return true;
    }
    /**
     * 生成唯一key
     */
    function genIdent($id = 0){
        $cartM = m("cart");
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
        } while($cartM->get("ident = '{$ident}'"));
        return $ident;
    }
}