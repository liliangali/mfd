<?php

/**
 *    我的收藏控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class My_favoriteApp extends MemberbaseApp
{
    /**
     *    收藏列表
     *
     *    @author    Garbin
     *    @return    void
     */
    function index()
    {
        $cls = $this->_get_goods();
        $this->assign('collect_custom', $cls);
        $this->display('my_favorite.goods.index.html');
    }

    //获取瀑布流数据
    function ajax_list(){
      $new_list = $this->_get_goods($_GET['limit'],$_GET['type']);
      if ($new_list)
       {
          $this->json_result($new_list);
          return;
      }else{
        $this->json_error('无数据了！');
        return;
      }
    }

    function _get_goods($page = '0,20',$type = 'goods'){
      $model_collect =& m('collect');
     
      $collect_custom = $model_collect->find(array(
          'conditions' => "user_id = " . $this->visitor->get('user_id') ." and type='".$type ."'",
          'order' => 'collect.add_time DESC',
          'limit' => $page,
          //'count'      => true,
      ));
    
      $cids = array();
      $cls = array();
      foreach($collect_custom as $val){
          $cls[$val["item_id"]] =$val;
          $cids[] = $val["item_id"];
      }

      //获取商品
       $custom_mod = &m("goods");
       $customs  = $custom_mod->find(array(
            "conditions" => "goods_id ".db_create_in($cids),
        ));
      if ($customs) //=====  add by liang.li 01.04   格式化价格  =====
      {
          $time = time();
          foreach ($customs as $key => &$value)
          {
              if($value['is_promotion'] && $time >= $value['star_time'] && $time <= $value['over_time'])
              {
                  $value["price"] = $value["promotion_price"];
              }
              $value["price"] = _format_price_int($value['price']);

              $value["woman_price"] = '';
              // if ($value['theme'] == 16) //=====  女装要打折  =====
              // {
              //     $value["woman_price"] = _format_price_int($value["price"] * 0.8);
              // }

          }
      }
      //融合在一起
      foreach($cls as $key => $val){
          $cls[$key]["ctm"] = $customs[$key];
          //区价格为整数
          $cls[$key]["ctm"]['price'] = (int)($cls[$key]["ctm"]['price']);
      }
      return $cls;
    }



    //列表收藏的作品  ns add
    function userwork_list(){
        $model_collect =& m('collect');
        $page = $this->_get_page(9);    //获取分页信息
        $collect_userwork = $model_collect->find(array(
            'conditions' => "user_id = " . $this->visitor->get('user_id') ." and type='userwork' ",
            'count' => true,
            'order' => 'collect.add_time DESC',
            'limit' => $page['limit'],
        ));
        $cids = array();
        $cls = array();
        foreach($collect_userwork as $val){
            $cls[$val["item_id"]] =$val;
            $cids[] = $val["item_id"];
        }

        //获取作品
        $works_mod =& m('userwork');
        $works  = $works_mod->find(array(
            "conditions" => "id ".db_create_in($cids),
        ));
        $userworkimg_mod =& m('userworkimg');
        //获取作品图片
        foreach($works as $k=>$v){
            $img_url = $userworkimg_mod->get(array(
                'conditions' => 'work_id=' . $v['id'],
                'fields'     => 'img_url',
            ));
            $works[$k]['imgurl'] = $img_url['img_url'];
        }

        //融合在一起
        foreach($cls as $key => $val){
            $cls[$key]["ctm"] = $works[$key];
        }

        $page['item_count'] = $model_collect->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign('collect_works', $cls);

        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user_set'));
        $this->assign('_curitem', 'my_favorite');

        $this->assign('page_info', $page);
        $this->display('my_favorite.userwork.index.html');
    }

    //套装列表
    function suit_list(){
        $model_collect =& m('collect');
        $page   =   $this->_get_page(9);    //获取分页信息
        $collect_custom = $model_collect->find(array(
            'conditions' => "user_id = " . $this->visitor->get('user_id') ." and type='suit' ",
            'count' => true,
            'order' => 'collect.add_time DESC',
            'limit' => $page['limit'],
        ));

        $cids = array();
        $cls = array();
        foreach($collect_custom as $val){
            $cls[$val["item_id"]] =$val;
            $cids[] = $val["item_id"];
        }

        //获取商品
        $suitlist_mod = &m("suitlist");
        $suitlist  = $suitlist_mod->find(array(
            "conditions" => "id ".db_create_in($cids),
        ));

        //融合在一起
        foreach($cls as $key => $val){
            $cls[$key]["ctm"] = $suitlist[$key];
        }

        $page['item_count'] = $model_collect->getCount();   //获取统计的数据
        $this->_format_page($page);
        $this->assign('collect_suit', $cls);

        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user_set'));
        $this->assign('_curitem', 'my_favorite');

        $this->assign('page_info', $page);
        $this->display('my_favorite.suit.index.html');

    }



    //删除商品 ns add
    function dropCollect(){
           $id = empty($_POST['item_id'])  ? 0 : intval($_POST['item_id']);
        if (!$id)
        {
            $this->json_error('删除收藏不存在！');
            return;
        }
		
        $collect_mod =& m('collect');
        $collect_mod->drop("id=".$id." and user_id=".$this->visitor->get('user_id'));
        if($collect_mod->has_error()){
            $this->json_error('删除收藏失败！');
            return;
        }

        $this->json_result();
    }


    /**
     *    收藏项目
     *
     *    @author    Garbin
     *    @return    void
     */
    function add()
    {

        $type = empty($_POST['type'])    ? 'custom' : trim($_POST['type']);
        $id = empty($_POST['gid'])  ? 0 : intval($_POST['gid']);
        $keyword = empty($_POST['keyword'])  ? '' : trim($_POST['keyword']);
        if (!$id)
        {
            $this->json_error('收藏不存在！');
            return;
        }
        if($type == 'custom'){
            //商品收藏
            $this->_add_collect_custom($id,$type,$keyword);
        }elseif($type == 'userwork'){
            //收藏作品
            $this->_add_collect_userwork($id,$type,$keyword);
        }elseif($type == "suit"){
            //收藏套装
            $this->_add_collect_suit($id,$type,$keyword);
        }elseif($type == 'goods'){
            //收藏商品 new
            $this->_add_collect_goods($id,$type,$keyword);
        }
    }

    //收藏套装
    function _add_collect_suit($id,$type,$keyword){
        if (!$id)
        {
            $this->json_error('收藏套装不存在！');
            return;
        }
        //获取套装
        $collect_mod =& m("collect");
        $collect  = $collect_mod->find(array(
            "conditions" => "user_id = " . $this->visitor->get('user_id') ." and type='".$type."' and item_id=".$id,
        ));
        if($collect){
            $this->json_error('您已经套装过该作品！');
            return;
        }
        $data['user_id'] = $this->visitor->get('user_id');
        $data['type'] = $type;
        $data['item_id'] = $id;
        $data['keyword'] = $keyword;
        $data['add_time'] = time();
        $collect_mod->add($data);
        if($collect_mod->has_error()){
            $this->json_error('收藏套装失败！');
            return;
        }
        $this->json_result('', '收藏套装成功！');
        return;
    }

    //收藏作品 ns add 分开写利于以后添加功能
    function _add_collect_userwork($id,$type,$keyword){
        if (!$id)
        {
            $this->json_error('收藏作品不存在！');
            return;
        }
        //获取商品
        $collect_mod =& m("collect");
        $collect  = $collect_mod->find(array(
            "conditions" => "user_id = " . $this->visitor->get('user_id') ." and type='".$type."' and item_id=".$id,
        ));
        if($collect){
            $this->json_error('您已经收藏过该作品！');
            return;
        }
        $data['user_id'] = $this->visitor->get('user_id');
        $data['type'] = $type;
        $data['item_id'] = $id;
        $data['keyword'] = $keyword;
        $data['add_time'] = time();
        $collect_mod->add($data);
        if($collect_mod->has_error()){
            $this->json_error('收藏作品失败！');
            return;
        }
        $this->json_result('', '收藏作品成功！');
        return;
    }

        //收藏商品
    function _add_collect_goods($id,$type,$keyword){
        if (!$id)
        {
            $this->json_error('收藏不存在！');
            return;
        }
        //获取商品
        $collect_mod =& m("collect");
        $collect  = $collect_mod->find(array(
            "conditions" => "user_id = " . $this->visitor->get('user_id') ." and type='".$type."' and item_id=".$id,
        ));
        if($collect){
            $this->json_error('您已经收藏过该商品！');
            return;
        }
        $data['user_id'] = $this->visitor->get('user_id');
        $data['type'] = $type;
        $data['item_id'] = $id;
        $data['keyword'] = $keyword;
        $data['add_time'] = time();

        $collect_mod->add($data);
        if($collect_mod->has_error()){
            $this->json_error('收藏作品失败！');
            return;
        }
        $this->json_result('', '收藏成功！');
        return;
    }


    //收藏商品
    function _add_collect_custom($id,$type,$keyword){
        if (!$id)
        {
            $this->json_error('收藏不存在！');
            return;
        }
        //获取商品
        $collect_mod =& m("collect");
        $collect  = $collect_mod->find(array(
            "conditions" => "user_id = " . $this->visitor->get('user_id') ." and type='".$type."' and item_id=".$id,
        ));
        if($collect){
            $this->json_error('您已经收藏过该商品！');
            return;
        }
        $data['user_id'] = $this->visitor->get('user_id');
        $data['type'] = $type;
        $data['item_id'] = $id;
        $data['keyword'] = $keyword;
        $data['add_time'] = time();

        $collect_mod->add($data);
        if($collect_mod->has_error()){
            $this->json_error('收藏作品失败！');
            return;
        }
        $this->json_result('', '收藏成功！');
        return;
    }

    /**
     *    收藏基本款
     *
     *    @author    Garbin
     *    @param     int    $cst_id
     *    @param     string $keyword
     *    @return    void
     */
    function _add_collect_customs($cst_id, $keyword)
    {

        /* 验证要收藏的基本款是否存在 */
        $model_customs =& m('customs');
        $customs_info  = $model_customs->get($cst_id);
        if (empty($customs_info))
        {
            /* 基本款不存在 */
            return;
        }

        $model_customs =& m('customs');
        $conditions = " AND collect.item_id=".$cst_id;
        $collect_customs = $model_customs->find(array(
            'join'  => 'be_collect',
            'conditions' => 'collect.user_id = ' . $this->visitor->get('user_id') . $conditions,
        ));
        if($collect_customs){
            $this->json_result('', '您已经收藏过该基本款');
            return;
        }

        $model_user =& m('member');
        $model_user->createRelation('collect_customs', $this->visitor->get('user_id'), array(
            $cst_id   =>  array(
                'keyword'   =>  $keyword,
                'add_time'  =>  gmtime(),
            )
        ));
        $this->send_feed('customs_collected', array(
            'user_id'   => $this->visitor->get('user_id'),
            'user_name'   => $this->visitor->get('user_name'),
            'collected_url'   => SITE_URL . '/index.php/custom-info-' . $cst_id.'.html',
            'collected_name'   => $customs_info['cst_name'],
        ));

        //添加积分
        $p_num = pointTurnNum('shoucang');
        setPoint($this->visitor->get('user_id'), $p_num, 'add', 'shoucang');
        /* 收藏成功 */
        $this->json_result('', 'collect_customs_ok');
    }


}

?>
