<?php
namespace Cyteam\Shop\Type;

use Cyteam\Shop\Type\Base;
use Cyteam\Db\Led;
/**
  * 普通商品
  */
class CustomBase extends Base {
    
    /**
     * 格式化psot
     * @see \Cyteam\Shop\Type\Base::_formatPost()
     */
      public function _formatPost($post = array()){
        $res['goods_id'] = isset($post['gid']) ? trim($post['gid']) : 0;
    	$res['product_id'] = isset($post['pid']) ? trim($post['pid']) : 0;
    	$res['quantity'] = isset($post['num']) ? intval($post['num']) : 1;
    	$res['type'] = 'custom';
    	return $res;
      }
      
      function _baseInfo($goods_id){
          $goodsM = m("goods");
          $data = $goodsM->get($goods_id);
      
          /* 基本款不存在 */
          if(!$data){
              $this->_error('diy_goods_not_exist');
              return false;
          }
          /* 商品扩展分类 */
          $cgs = Led::table('category_goods')->where(['goods_id'=>$goods_id])->orderBy('id','DESC')->get();
          if ($cgsa)
          {
          	   $data['cat_id'] = implode(',', array_unique(i_array_column($cgs, 'cate_id')));
          }

          /* 检查基本款是否上架销售 */
          if(!$data['marketable']){
              $this->_error('diy_goods_not_sale');
              return false;
          }
      
          return $data;
      }
      
      /**
       * 加入购物车
       * @see \Cyteam\Shop\Type\Base::add()
       */
      public function add($post)
      {
          $_Bdata = $this->_baseInfo(intval($post["goods_id"]));
          if(!$_Bdata){
              $this->_error('商品参数错误!'.$post["goods_id"].$this->get_error());
              return false;
          }
          $_Gdata = $this->_groupInfo($post["goods_id"],$post["product_id"]);

          if(empty($_Gdata['oProducts'])){
              $this->_error('该商品没有匹配到货品信息!');
              return false;
          }
          $stock = $this->_checkStock($post);
          if($stock < 0 || empty($stock)){
              $this->_error("该商品已卖完！");
              return false;
          }

          $_Cgoods = [];
          if(!isset($_REQUEST['mfd_cart_is_check']))
          {
              $_Cgoods = $this->_cartInfo($post);

              if($_Cgoods){
                  $post['quantity'] += $_Cgoods['quantity'];
                  $post['ident']    = $_Cgoods['ident'];
                  return $this->update($post);
              }
          }





          unset($_Gdata['oGoods']['intro']);
          /* 生成入库数据 */
          $cData = array(
              'user_id'    => $post['user_id'],
              'ident'      => $this->genIdent($post['user_id']),
              'goods_id'   => $_Gdata['oProducts']["product_id"],
              'price'      => $_Gdata['price']["price"],
              'type'       => $post['type'],
              'cloth'      => $_Bdata["cat_id"],
              'params'      => serialize($_Gdata),
              'quantity'   => $post['quantity'],
              'size'       => $post['size'],
              'goods_name' => $_Bdata["name"],
              'image'      => $_Bdata["thumbnail_pic"],
              'time'       => time(),
              'session_id' => SESS_ID,
              'source_from'   => $post['source_from'],
          );

          $cartM = m("cart");
//          echo '<pre>';print_r($cData);exit;
          
          $res = $cartM->add($cData);
          if(!$res){
              $this->_error("add_to_cart_error");
          }
          return $res;
      }

}