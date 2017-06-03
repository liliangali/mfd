<?php
/**
 * 即拍即做订单
 * 
 * @author Ruesin
 */
class OrderjApp extends BackendApp
{
    var $_mod_order;
    public $_mod_part;
    public $_mod_craft;
    public $_mod_craft_parent;
    public $_mod_emb;
    public $_embs;
    public $_mod_order_figure;
    public $_mod_order_goods;
    public $_mod_mtm_bt;
    public $_mod_member_figure;
    
    function __construct(){
        $this->OrderjApp();
    }
    function OrderjApp(){
        parent::__construct();
        $this->_mod_order = &m('order');
        $this->_mod_part = &m('part');
        $this->_mod_craft = &m('mtmcraft');
        $this->_mod_craft_parent = &m('mtmcraftparent');
        $this->_mod_emb = &m('mtmemb');
        $this->_mod_order_figure = &m('orderfigure');
        $this->_mod_order_goods = &m('ordergoods');
        $this->_mod_mtm_bt = &m("mtmbodytype");
        $this->_mod_member_figure = &m('member_figure');
        $this->_embs = array(
                'emb_site' => '位置',
                'emb_font' => '字体',
                'emb_color' => '颜色',
        );
    }

    function index(){
        $oid = isset($_GET['oid']) ? intval($_GET['oid']) : 0;
        $jid = isset($_GET['jid']) ? intval($_GET['jid']) : 0;
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0 ;
        
        if(!($jid && $uid) && !$oid) return ;

        if($oid){
            $data = $this->_mod_order->get($oid);
            if(!$data) return ;
        }elseif ($jid){
            
            $data = $this->_mod_order->get(" jp_id = '{$jid}'");
            
            if(!$data){
                $mMem  = &m('member');
                $mData = $mMem->get($uid);
                
                $data = array(
                        'user_id'      => $mData['user_id'],
                        'user_name'    => $mData['user_name'],
                        'kf_id'        => $_SESSION['admin_info']['user_id'],
                        'kf_name'      => $_SESSION['admin_info']['user_name'],
                        'jp_id'        => $jid,
                );
            }
        }
        
        $step = $_SESSION['_craft_order'][$_SESSION['admin_info']['user_id']][$data['order_id']];
        
        $step = intval($step) ? intval($step) : 1;
        
        if($data['order_id']){
            $data['figure'] = $this->_mod_order_figure->get(" order_id = '{$data['order_id']}'");
            //这个是不是可以放在ajax里
            $goods = $this->_mod_order_goods->get(" order_id = '{$data['order_id']}'");
            $data['fabric'] = $goods['fabric'];
        }
        
        $cloth = isset($data['cloth']) ? $data['cloth'] : 3;
        $this->assign('ckCloth',$cloth);
        $this->assign('cloths',$this->_get_cloth_attr());
        $this->assign('data',$data);

        $this->assign('figureList',$this->_mod_member_figure->_positions());
        $this->assign('body_type',$this->_get_body_type());

        $this->assign('step',$step);
        $this->display('orderj/craft_order.html');
    }
    
    
    /**
     * 保存数据
     * 
     * @author Ruesin
     */
    function orderSave(){
        $step = isset($_POST['step']) ? intval($_POST['step']) : 0 ;
        
        if(!$step){
            $this->json_error('参数错误');
            die();
        }
        if($step == '1'){
            $order_id = $this->craftOrderFirst($_POST);
        }elseif ($step == '2'){
            $order_id = $this->craftOrderSecond($_POST);
        }
        
        if($order_id){
            $_SESSION['_craft_order'][$_SESSION['admin_info']['user_id']][$order_id] = $_POST['next'];
            $this->json_result($order_id,'提交成功!');
            die();
        }
        $this->json_error('提交失败');
        die();
    }
    
    /**
     * 工艺下单第一步
     * 
     * @author Ruesin
     */
    function craftOrderFirst($post = array()){
        
        $cid = isset($post['clothingID']) ? intval($post['clothingID']) : 0;
        $fbc = isset($post['fabric']) ? trim($post['fabric']) : '';
        $oid = isset($post['order_id']) ? intval($post['order_id']) : '';
        
        $craft = is_array($post['craft']) ? $post['craft'] : array();
        $embs = is_array($post['embs']) ? $post['embs'] : array();
        
        if(!$cid || !$fbc){
            $this->json_error('参数错误');
            die();
        }
        
        foreach ((array)$craft as $key => $row){
            $data[$key]['craft'] = json_encode($row);
        }
        
        foreach ((array)$embs as $key => $row){
            $data[$key]['embs'] = json_encode($row);
        }
        
        $post['data'] = $data;
        if($oid){//有订单的更新
            
            $transaction = $this->_mod_order->beginTransaction();
            $oData = $this->_mod_order->get($oid);
            if($oData['cloth'] == $cid){
                //同品类  更新
                foreach ($data as $key =>$row){
                    $item = array(
                            'fabric'		=> $fbc,
                            'embs'		    => $row['embs'],
                            'crafts'        => $row['craft'],
                    );
                    $where = " order_id = '{$oid}' AND cloth = '{$key}'";
                    $res =  $this->_mod_order_goods->edit($where,$item);
                    if ($res<0)
                        break;
                }
                
            }else{
                //品类不同 改 删 增  order  order_goods
                
                $where = " order_id = '{$oid}'";
                $this->_mod_order->edit($where,array('cloth'=>$cid));
                
                $this->_mod_order_goods->drop($where);
                
                $cloths = $this->_get_cloth_attr();
                foreach ((array)$data as $key => $value){
                    $items[] = array(
                            'order_id'      => $oid,
                            'goods_name'    => $cloths[$key]['name'],
                            'goods_image'   => '',
                            'type' 		    => 'kf',
                            'cloth'         => $key,
                            'dis_ident'     => $cid,
                            'fabric'		=> $fbc,
                            'embs'		    => $value['embs'],
                            'crafts'        => $value['craft'],
                            'size'          => 'diy',
                    );
                }
                 
                $res = $this->_mod_order_goods->add(addslashes_deep($items));
            
            }
            if($res){
                $this->_mod_order->commit($transaction);
                return $oid;
            }
            $this->_mod_order->rollback();
        }elseif ($post['jp_id']){
            $post['amount'] = 0;
            $order_id = $this->save($post,'c');
            if($order_id)
                return $order_id;
            
        }
        
        $this->json_error('保存失败');
        die();
    }
    
    /**
     * 工艺订单第二步
     * 
     * @author Ruesin
     */
    function craftOrderSecond($post = array()){
        $oid = isset($post['order_id']) ? intval($post['order_id']) : '';
        if(!$oid){
            $this->json_error('参数错误!');
            die();
        }
        
        $where = " order_id = '{$oid}'";
        
        $res = $this->_mod_order->edit($where,$post['ship']);
        
        if ($res >= 0){
            $figure = array_merge($post['figure'],$post['body_type']);
            $res = $this->_mod_order_figure->edit($where,$figure);
        }
        if($res >= 0){
            return $oid;
        }
        $this->json_error('保存失败');
        die();
    }
    
    /**
     * 工艺下单第三步渲染
     * 
     * @author Ruesin
     */
    function ajax_see(){
        $oid = isset($_POST['oid']) ? intval($_POST['oid']) : 0;
        if(!$oid){
            $this->json_error('参数错误!');
            die();
        }
        
        $order = $this->_mod_order->get($oid);
        $order['figure'] = $this->_mod_order_figure->get(" order_id = '{$oid}'");
        
        $cloth = $this->_get_cloth_attr($order['cloth']);
        $arrPrt = $this->_mod_craft_parent->find(db_create_in($cloth['son'],'clothingID'));
        $arrEmb = $this->_mod_emb->find(db_create_in($cloth['son'],'clothingID')." AND e_tname IN ('emb_color','emb_site','emb_font')");
        
        foreach ($arrPrt as $row) {
            $pCraft[$row['id']] = $row;
            $ids[$row['id']] = $row['id'];
        }
        $arrCraft = $this->_mod_craft->find(db_create_in($ids, 'parentId'));
        foreach ($arrCraft as $row) {
            $row['parentName'] = $pCraft[$row['parentId']]['name'];
            $craft[$row['code']] = $row;
        }
        
        foreach ($arrEmb as $row) {
            $embs[$row['e_id']] = $row;
        }
        
        $order['goods']  = $this->_mod_order_goods->find(" order_id = '{$oid}'");
        
        foreach ($order['goods'] as &$row){
            $row['embs']   = json_decode($row['embs'],1);
            $row['crafts'] = json_decode($row['crafts'],1);
            
            $row['cloth'] = $this->_get_cloth_attr($row['cloth']);
            foreach ($row['crafts'] as $k=>$v){
                $row['crafts'][$k]['data'] = $craft[$v['code']];
                $row['crafts'][$k]['data']['value'] = $v['value'];
            }
            foreach ($row['embs'] as $k=>$v){
                if($k != 'emb_con'){
                    $row['embs'][$k] = $embs[$v]['e_name'];
                }
            }
        }
        
        foreach ($this->_get_body_type() as $k=>$r){
            $bodytype[$k]['cname'] = $r['info']['name']; 
            $bodytype[$k]['name']  = $r['list'][$order['figure'][$r['info']['nm']]]['name'];
        }
        $this->assign('figureList',$this->_mod_member_figure->_positions());
        $this->assign('body_type',$bodytype);
        $this->assign('embs',array('emb_site'=>'刺绣位置','emb_font'=>'刺绣字体','emb_color'=>'刺绣颜色','emb_con'=>'刺绣内容'));
        $this->assign('order',$order);
        $content = $this->_view->fetch('orderj/items/info.html');
        $this->json_result($content,'');
        die();
    }
    
    /**
     * 生成支付链接
     * 
     * @author Ruesin
     */
    function goSave(){
        $amount    = $_POST['order_amount'];
        $id   = $_POST['id'];
        $name = $_POST['name'];
        $jpid = $_POST['jpid'];
        $kf_id = $_POST['kf_id'];
        $kf_name = $_POST['kf_name'];
        $cloth = $_POST['cloth'];
        
        if($this->_mod_order->get(" jp_id = '{$_POST['jp_id']}' ")){
            //有订单的进行更新
            
            return;
        }else{
            $this->save($_POST,'p');
            return ;
        }
    }
    
    /**
     * 保存即拍订单
     *
     * @author Ruesin
     */
    public function save($post,$t = 'p'){
        $this->_mod_pay = &m("payment");
        $payment = $this->_mod_pay->get(array(
                'conditions' => "enabled=1 AND ismobile = 1",
                'order'      => "sort_order DESC"
        ));
    
        $order_sn = $this->_gen_order_sn();
    
        $time = gmtime();
        
        if($t == 'p'){
            $status = ORDER_PENDING;
        }else{
            $status = ORDER_SAVED;
        }
    
        $odata = array(
                'order_sn'     => $order_sn,
                'extension'    => 'jp',
                'discount'     => '0',
                'goods_amount' => $post['amount'],
                'order_amount' => $post['amount'],
                'cloth'        => $post['clothingID'],
                'user_id'      => $post['user_id'],
                'user_name'    => $post['user_name'],
                'kf_id'        => $_SESSION['admin_info']['user_id'],
                'kf_name'      => $_SESSION['admin_info']['user_name'],
                'jp_id'        => $post['jp_id'],
                'status'       => $status,
                'add_time'     => $time,
                'payment_id'   => $payment['payment_id'],
                'payment_name' => $payment['payment_name'],
                'payment_code' => $payment['payment_code'],
                'last_modified'=> $time,
                //'memo'         => '',
                'source_from'  => 'app',
        );
        $transaction = $this->_mod_order->beginTransaction();
        
        //保存订单
        $oid = $this->_mod_order->add($odata);
        
        //保存量体
        if ($oid){
            $fData = array(
                    'order_id' => $oid,
                    'userid' => $post['user_id']
            );
            $res = $this->_mod_order_figure->add($fData);
        }
        
        //保存货品
        if($post['data']){
            $cloths = $this->_get_cloth_attr();
            foreach ((array)$post['data'] as $key => $value){
                $items[] = array(
                        'order_id'      => $oid,
                        'goods_name'    => $cloths[$key]['name'],
                        'goods_image'   => '',
                        'type' 		    => 'kf',
                        'cloth'         => $key,
                        'dis_ident'     => $post['clothingID'],
                        'fabric'		=> $post['fabric'],
                        'embs'		    => $value['embs'],
                        'crafts'        => $value['craft'],
                        'size'          => 'diy',
                );
            }
             
            $res = $this->_mod_order_goods->add(addslashes_deep($items));
        }
        
        
        if (!$oid || !$res){
            $this->_mod_order->rollback();
            return false;
        }
        
        $this->_mod_order->commit($transaction);
        return $oid;
    }
    
    
    
    
    
    /**
     * 根据品类获取面料
     *
     * @author Ruesin
     */
    function ajax_Fresult ()
    {
        $clothId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($clothId == 0) {
            $this->json_error('params_error');
            return;
        }
        $attr = $this->_get_cloth_attr($clothId);
        $fabric = $this->_mod_part->find(array(
                        'conditions' => " cate_id = '{$attr['fabric']}' AND code <> '' ",
                        'fields' => 'code'
                    ));
    
        if (empty($fabric)) {
            $this->json_error('no_data');
            return;
        }
        foreach ($fabric as $row) {
            $content[] = $row;
        }
        $this->json_result(array(
                        'content' => $content
                ));
        return;
    }
    
    /**
     * Ajax通过clothID获取工艺信息
     * 
     * @author Ruesin
     */
    function ajax_craft ()
    {
        $clothId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $oid     = isset($_POST['oid']) ? intval($_POST['oid']) : 0;
        
        if (!$clothId) {
            $this->json_error('params_error');
            return;
        }
        
        $cloth = $this->_get_cloth_attr($clothId);
        $arr = $this->_mod_craft_parent->find(db_create_in($cloth['son'],'clothingID'));
        
        foreach ($arr as $row) {
            $crafts[$row['id']] = $row;
            $ids[$row['id']] = $row['id'];
        }
        $arrCraft = $this->_mod_craft->find(db_create_in($ids, 'parentId'));
        foreach ($arrCraft as $row) {
            //$crafts[$row['parentId']]['list'][$row['id']] = $row;
            $row['parentName'] = $crafts[$row['parentId']]['name'];
            $data[$row['clothingID']][] = $row;
            $craft[$row['code']] = $row;
        }
        
        if (! empty($arrCraft)) {
            
            if($oid){
                $oGoods = $this->_mod_order_goods->find(" order_id = '{$oid}'");
                foreach ($oGoods as $row){
                    //$allCrf[$row['cloth']]['embs']   = json_decode($row['embs'],true);  //没法几种放在一起了，那就分开吧  反正不多这一次查询
                    //$allCrf[$row['cloth']]['crafts'] = json_decode($row['crafts'],true);
                    $cArr[$row['cloth']] = json_decode($row['crafts'],true);
                }
                foreach ($cArr as $key=>$row){
                    $allCrf[$key] = $this->_get_cloth_attr($key);
                    foreach ($row as $k=>$v){
                        $allCrf[$key]['data'][$k] = $craft[$v['code']];
                        $allCrf[$key]['data'][$k]['value'] = $v['value'];
                    }
                }
            }else{
                foreach ($cloth['son'] as $val){
                    $allCrf[$val] = $this->_get_cloth_attr($val);
                }
            }
            
            $this->assign('crafts',$allCrf);
            $content = $this->_view->fetch('orderj/items/craft.html');
            $this->json_result(array(
                            'content' => $content,
                            'data'    => $data,
                    ));
            die();
        } else {
            $this->json_error('no_data');
            die();
        }
    }
    
    /**
     * Ajax通过clothID获取刺绣信息
     *
     * @author Ruesin
     */
    function ajax_emb ()
    {
        $clothId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $oid     = isset($_POST['oid']) ? intval($_POST['oid']) : 0;
        
        if (! $clothId) {
            $this->json_error('params_error');
            return;
        }
        $cloths = $this->_get_cloth_attr();
        $cloth = $cloths[$clothId];
        
        $arr = $this->_mod_emb->find(db_create_in($cloth['son'],'clothingID')." AND e_tname IN ('emb_color','emb_site','emb_font')");
        if (empty($arr)) {
            $this->json_error('no_data');
            return;
        }
        foreach ($arr as $row) {
            $embs[$row['clothingID']]['data'][$row['e_tname']]['tname'] = $this->_embs[$row['e_tname']];
            $embs[$row['clothingID']]['data'][$row['e_tname']]['id']    = $row['e_type'];
            //$embs[$row['clothingID']][$row['e_tname']]['clothingID'] = $clothId;
            $embs[$row['clothingID']]['data'][$row['e_tname']]['list'][$row['e_id']] = $row;
        }
        //放在上面有点消耗资源还是在下面循环吧
        foreach ($cloths as $row){
            if($embs[$row['id']]){
                $embs[$row['id']]['name'] = $row['name'];
            }
        }
        
        if($oid){
            $oGoods = $this->_mod_order_goods->find(" order_id = '{$oid}'");
            
            foreach ($oGoods as $row){
                $embs[$row['cloth']]['checked'] = json_decode($row['embs'],true);
            }
        }
        
        $this->assign('embs', $embs);
        $content = $this->_view->fetch('orderj/items/emb.html');
        $this->json_result(
                array(
                        'content' => $content
                ));
        return;
    }
    
    
    /**
     * Ajax检验面料并获取库存
     * 
     * @author Ruesin
     */
    function ajax_fabric ()
    {
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        $clothId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($code == '' || $clothId == 0) {
            $this->json_error('params_error');
            return;
        }
        $attr = $this->_get_cloth_attr($clothId);
        $fabric = $this->_mod_part->get(" cate_id = '{$attr['fabric']}' AND code = '{$code}' ");
        
        if (empty($fabric)) {
            $this->json_error('no_data');
            return;
        }
        // 检查库存，需要接口
        // ..
        $stock = 2000;
        if ($stock < 0) {
            $this->json_error('no_data');
            return;
        }
        $this->json_result($stock);
        return;
    }
    
    function _get_embs_id($clothID = 0){
        $embs = array(
                3 => array(
                        'emb_con'   => '421',//'内容'
                        'emb_site'  => '1218',//'位置'
                        'emb_font'  => '518',//'字体'
                        'emb_color' => '422',//'颜色'
                ),
                2000 => array(
                        'emb_con'   => '2207',//'内容'
                        'emb_site'  => '2507',//'位置'
                        'emb_font'  => '2523',//'字体'
                        'emb_color' => '2213',//'颜色'
                ),
                3000 => array(
                        'emb_con'   => '3676',//'内容'
                        'emb_site'  => '3201',//'位置'
                        'emb_font'  => '3248',//'字体'
                        'emb_color' => '3631',//'颜色'
                        //'emb_size' => '3259',//'绣字大小'
                ),
                4000 => array(
                        'emb_con'   => '4149',//'内容'
                        'emb_site'  => '4550',//'位置'
                        'emb_font'  => '4155',//'字体'
                        'emb_color' => '4150',//'颜色'
                ),
                6000 => array(
                        'emb_con'   => '6396',//'内容'
                        'emb_site'  => '6976',//'位置'
                        'emb_font'  => '6413',//'字体'
                        'emb_color' => '6404',//'颜色'
                ),
        );
        return $embs[$clothID];
    }
    
    function _get_cloth_attr($id = 0){
        $res = array(
                1 => array(
                        'id' => 1,
        	           'name' => '套装(2pcs)',
                        'son' => array(3,2000),
                        'fabric' => 8001,
                ),
                2 => array(
                        'id' => 2,
                        'name' => '套装(3pcs)',
                        'son' => array(3,4000,2000),
                        'fabric' => 8001,
                ),
                3 => array(
                        'id' => 3,
                        'name' => '西服',
                        'son' => array(3),
                        'fabric' => 8001,
                ),
                2000 => array(
                        'id' => 2000,
                        'name' => '西裤',
                        'son' => array(2000),
                        'fabric' => 8001,
                ),
                3000 => array(
                        'id' => 3000,
                        'name' => '衬衣',
                        'son' => array(3000),
                        'fabric' => 8030,
                ),
                4000 => array(
                        'id' => 4000,
                        'name' => '马夹',
                        'son' => array(4000),
                        'fabric' => 8001,
                ),
                6000 => array(
                        'id' => 6000,
                        'name' => '大衣',
                        'son' => array(6000),
                        'fabric' => 8050,
                ),
        );
        if($id) return $res[$id];
        return $res;
    }
   
    /**
     * 特体+着装风格
     * 
     * @author Ruesin
     */
    function _get_body_type(){
        $body_type_tm = $this->_mod_mtm_bt->find();
        foreach ($body_type_tm as $row){
            if($row['cateID'] != 32){
                $body_type[$row['cateID']]['info']['name'] = $row['cateName'];
                $body_type[$row['cateID']]['info']['id']   = $row['cateID'];
                $body_type[$row['cateID']]['info']['nm']   = 'body_type_'.$row['cateID'];
                $body_type[$row['cateID']]['list'][$row['id']] = $row;
            }else{
                $body_type[$row['clothId']]['info']['name'] = $row['cateName']."&nbsp;".$row['clothName'];
                $body_type[$row['clothId']]['info']['id']   = $row['cateID'];
                $body_type[$row['clothId']]['info']['nm']   = 'body_type_'.$row['clothId'];
                $body_type[$row['clothId']]['list'][$row['id']] = $row;
            }
        }
        return $body_type;
    }
    
    
    
    
    
    
    /**
     * 获取订单号
     * 
     * @author Ruesin
     */
    function _gen_order_sn()
    {
        mt_srand((double) microtime() * 1000000);
        $timestamp = gmtime();
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = 'JP'.$y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    
        $orders = $this->_mod_order->find("order_sn='{$order_sn}'");
        if (empty($orders)){
            return $order_sn;
        }
        return $this->_gen_order_sn();
    }

}

