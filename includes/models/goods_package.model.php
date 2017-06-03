<?php

/* 赠品礼包模型 */
class Goods_packageModel extends BaseModel
{
    var $table  = 'goods_package';
    var $prikey = 'id';
    var $_name  = 'goods_package';

    
    //获取礼包下的商品
    function getPackageInfo($id){
      $data = array();
      $goods_package_mod =& m('goods_package');
      $goods_mod =& m('goods');
      $goods_giveaway_mod =& m('goods_giveaway');

      $package = $goods_package_mod->get($id); 
      $f_list = explode(',',$package['f_list']); 
      $c_list = explode(',',$package['c_list']); 
      $data['c_title'] = '常规商品：';
      $data['f_title'] = '赠品商品：';
      $c = 1;
      if($c_list){
        foreach($c_list as $cid){
            if($cid){
                $data['c_list'][$cid] = $goods_mod->get(array(
                'conditions'=>"goods_id=".$cid,
                'fields' => 'name,bn,mktprice',
                ));
                $data['c_title'] .= '&#13;'.$c.'、'.$data['c_list'][$cid]['name'];
                $c++;
            }
        }
      }
      $f = 1;
      if($f_list){
        foreach($f_list as $fid){
            if($fid){
                $data['f_list'][$fid] = $goods_giveaway_mod	->get(array(
                'conditions'=>"goods_id=".$fid,
                'fields' => 'name,bn,mktprice',
                ));
                $data['f_title'] .= '&#13;'.$f.'、'.$data['f_list'][$fid]['name'];
                $f++;
            }
        }
      }
      $data['title'] = $data['c_title'].'&#10;&#10;'.$data['f_title'];
      return $data;
    }




}
?>