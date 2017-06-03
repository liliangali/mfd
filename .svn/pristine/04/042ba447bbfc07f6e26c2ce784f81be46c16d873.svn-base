<?php
/**
*作品详情页   选尺码 加入购物车
*@author liang.li <1184820705@qq.com>
*@2015年5月19日
*/
class WorkApp extends MallbaseApp
{

	var $mod;
	var $mod_img;
    function __construct()
    {
        $this->mod = m('works');
        $this->mod_img = m('workimgs');
        $this->WorkApp();
    }
    function WorkApp()
    {  
        parent::__construct();
    }
    function index()
    {
        /* if($_GET['token']){
            $uid = intval($this->ApiAuthcode($_GET['token'], 'DECODE', 'kuteiddiy',0)); //c9e5N2EUYiVWUUkwop9AgiQsGu3s1FPyluDLzB4Cwg
            if($uid){
                $this->_user_id = $uid;
                $this->_do_login($uid);
            }
        }elseif($this->visitor->get('user_id')){
            $this->_user_id = $this->visitor->get('user_id');
        } */
       
        
        $params = $this->get_params();
        $id = intval($params[0]);
        if (!$id) 
        {
            $this->show_warning('参数错误');
            return;
        }
        
        $info = $this->mod->get($id);
        if (!$info) 
        {
            $this->show_warning('数据不存在');
            return ;
        }
        
        
        
        //=====  等待app传入token  暂时自己通过作品表获取user_id  切记 此法只是缓兵之计  =====
        $this->_do_login($info['store_id']);
        /* if (!$this->visitor->get('user_id'))
        {
            
        } */
        
        
        $cate_config = include_once PROJECT_PATH.'/includes/arrayfiles/config.php';
        
        $size = "";
        $this->assign('is_diy',$info['is_diy']);
        $this->assign('is_suit',$info['is_suit']);
        $this->assign('id',$info['id']);
        if ($info['is_diy'] == 2) //=====  样衣  =====
        {
           
            $cus_id = $info['cus_id'];
            $cus_mod = m('custom');
            $size = "";
            //=====  单品样衣   =====
             if ($info['is_suit'] == 0) 
             {
                 $cus_info = $cus_mod->get($cus_id);
                 $size = $cus_info['category'];
                 $img = $this->mod_img->get("work_id = {$info['id']} ");
                 $cus_info['image'][0] = $img['img_url'];
//         dump($cus_info);
                 $this->assign('info',$cus_info);
             }
             else  //=====  套装样衣   ===== 
             {
                 $suit_list_mod = m('suitlist');
                 $suit_info = $suit_list_mod->get($cus_id);
                 $img = $this->mod_img->get("work_id = {$info['id']} ");
                 
                 $suit_info['image'][0] = $img['img_url'];
//          dump($suit_info);        
                  //=====  取得套装尺码  =====
                  $suit_relat_mod = m('suitrelat');
                  $cus_ids = $suit_relat_mod->get("tz_id = {$suit_info['id']} ");
                  if ($cus_ids['jbk_id']) 
                  {
                      $jbk_id = $cus_ids['jbk_id'];
                      $cus_list = $cus_mod->find(array(
                          'conditions' => " id IN($jbk_id)",
                      ));
                      
                      if ($cus_list) 
                      {
                          foreach ($cus_list as $key => $value) 
                          {
                              $size .= $value['category'].":";
                          }
                      }
                  }
                  
                  
                 $suit_info['name'] = $suit_info['suit_name'];
                 $this->assign('info',$suit_info);
             }
             
             $size = trim($size,":");
             $this->assign('size',$size);
        }
        elseif ($info['is_diy'] == 1)
        {
            $user_id = $info['store_id'];
            
            do{
                $api_token = ApiAuthcode($user_id, 'ENCODE', 'kuteiddiy', 0);
            }while(!preg_match("/[a-zA-Z\d]{42}$/u", $api_token));
            
            $cloth = $info['cloth'];
            $url = "http://m.cy.mfd.cn/custom-diy-$cloth-$api_token-$id.html";
            header("Location: $url");
            
        }
        else //=====   普通作品 用户上传的作品  =====
        {
            $this->assign('is_diy',0);
            $img_list = $this->mod_img->find(array(
                'conditions' => "work_id = $id",
            ));
            $img = array();
            if ($img_list) 
            {
                foreach ($img_list as $key => $value) 
                {
                    $img[] = $value['img_url'];
                }
            }
            $info['image'] = $img;
            $this->assign('info',$info);
        }
        
        
        $img_list = $this->mod_img->find(array(
            'conditions' => "work_id = $id",
        ));
        
        $this->assign('img_list',$img_list);
        
        
        
        $this->display('work/demo.html');
    }
    
    /**
    *添加购物车
    *@author liang.li <1184820705@qq.com>
    *@2015年5月20日
    */
    function addCart() 
    {
        if (!$this->visitor->get('user_id'))
        {
            $this->json_error('尚未登录');
            return ;
        }
        $user_id = $this->visitor->get('user_id');
        
        $is_amount = $_POST['is_amount'];
        $is_diy = $_POST['is_diy'];  //0普通     1小五的diy   2 样衣
        $is_suit = $_POST['is_suit'];  
        $_params = $_POST['_params'];// 具体的参数值
        
       
        if ($is_diy == 2) //=====   样衣  ===== 
        {
            if ($is_suit == 0) //=====   单品样衣  ===== 
            {
                if ($is_amount == 0) //=====  标准码定制  ===== 
                {
                    $arr = explode("||", $_params);
                    if (count($arr) != 3) 
                    {
                        $this->json_error('标准码定制!缺少参数');
                        return ;
                    }
                    $arr0 = $arr[0]; //=====  尺码  =====
                    $arr1 = $arr[1]; //=====  品类  =====
                    $arr2 = $arr[2]; //=====  作品id  =====
                    
                    if (!$arr0) 
                    {
                        $this->json_error('请选择尺码');
                    }
                    
                    $_data = $this->getWork($arr2,$arr0);  
                   
                    if (!$this->toCart($_data)) 
                    {
                        $this->json_error('添加购物车失败');
                        return ;
                    }
                }
                else //=====  量体定制  =====
                {
                    $_data = $this->getWork($_params,'diy');
                    if (!$this->toCart($_data))
                    {
                        $this->json_error('添加购物车失败');
                        return ;
                    }
                   
                }
                $this->json_result(array(),'下单成功');
                return;
            }
            else //=====    套装样衣  ===== 
            {
                if ($is_amount == 0) //=====  套装标准码定制  =====
                {
                    $arr = explode("||", $_params);
                    if (count($arr) != 3)
                    {
                        $this->json_error('标准码定制!缺少参数');
                        return ;
                    }
                    $arr0 = $arr[0]; //=====  尺码  =====
                    $arr1 = $arr[1]; //=====  品类  =====
                    $arr2 = $arr[2]; //=====  作品id  =====
                    if (!$arr0)
                    {
                        $this->json_error('请选择尺码');
                    }
                    
                    $size_arr = explode(":", $arr0);
                    $cate_arr = explode(":", $arr1);
                    if (count($cate_arr) != count($size_arr)) 
                    {
                        $this->json_error('套装尺码和品类参数错误');
                        exit;
                    }
                    $size = array();
                    if ($cate_arr) 
                    {
                        foreach ($cate_arr as $key => $value)
                        {
                           $size[$value] = $size_arr[$key];
                        }
                    }
                    
                    
                    $_data = $this->getSuitWork($arr2,$size);
                    if ($_data) 
                    {
                        foreach ($_data as $key => $value) 
                        {
                            if (!$this->toCart($value))
                            {
                                $this->json_error('添加购物车失败');
                                exit;
                            }
                        }
                    }
                    
                }
                else //=====  量体定制  =====
                {
                    $_data = $this->getSuitWork($_params,'diy',1);
                    if ($_data)
                    {
                        foreach ($_data as $key => $value)
                        {
                            if (!$this->toCart($value))
                            {
                                $this->json_error('添加购物车失败');
                                exit;
                            }
                        }
                    }
                    
                }
                $this->json_result(array(),'下单成功');
                return;
            }
        }
        elseif ($is_diy == 0)
        {
            $this->json_error('普通作品暂时无法加入购物车');
        }
        else 
        {
            $this->json_error('diy作品 请进入diy界面');
        }
         
        $this->json_result(array(),'成功');
    }
    
    /**
    *获得样衣的工艺和面料
    *@author liang.li <1184820705@qq.com>
    *@2015年5月20日
    */
    function getCustom($cus_id) 
    {
        include_once PROJECT_PATH.'/includes/goods.base.cym.php';
        //=====  取得工艺  =====
        $goods_base_mod = new BaseGoods();
        $params = $goods_base_mod->_group_info($cus_id);
        $data['params'] = json_encode($params['process']);
        if (!$params['oFabric']['CODE'])
        {
           return false;
        }
        
        $data['fabric'] = $params['oFabric']['CODE'];
        return $data;
    }
    
    /**
    *测试基本款的工艺
    *@author liang.li <1184820705@qq.com>
    *@2015年5月21日
    */
    function test() 
    {
        $params = $this->get_params();
        $id = intval($params[0]);
        if (!$id)
        {
            $this->show_warning('参数错误');
            return;
        }
// echo $id;        
        $data = $this->getCustom($id);
   print_exit($data);
      dump($data);
    }
    
    /**
    *content
    *@author liang.li <1184820705@qq.com>
    *@2015年5月20日
    */
    function  getWork($work_id,$size = '',$cloth = '') 
    {
        $cus_mod = m('custom');
        $suit_list_mod = m('suitlist');
        $suit_relat_mod = m('suitrelat');
        
        $work_info = $this->mod->get($work_id); //=====  作品  =====
        $work_img_info = $this->mod_img->get("work_id = $work_id");
        if (!$work_info)
        {
            $this->json_error('无此作品!');
            exit;
        }
        $cus_info = $cus_mod->get($work_info['cus_id']); //=====  样衣  =====
        if (!$cus_info)
        {
            $this->json_error('此作品无对应的样衣,可能是由于老数据的原因,请清理数据');
            exit;
        }
        $crafts = $this->getCustom($cus_info['id']); //=====  工艺信息   =====
        if (!$crafts)
        {
            $this->json_error('无法获得此样衣工艺信息！');
            exit;
        }
        
        /* $size_arr = explode(":", $arr0);//=====  格式化尺码尺码  =====
         $cate_arr = explode(":", $arr1); //=====  格式化品类  =====
         if (count($size_arr) != count($cate_arr))
         {
         $this->json_error('尺码信息参数有误');
         return;
         } */
       /*  if ($cus_info['category'] != $arr1)
        {
            $this->json_error('传入的品类和样衣的品类不匹配');
            exit;
        } */
        $_data['user_id'] = $this->visitor->get('user_id');
        $_data['ident'] = $this->gen_ident();
        $_data['source_from'] = "work";
        $_data['type'] = "dis";
        $_data['time'] = time();
        $_data['image'] = $work_img_info['img_url'];
        $_data['goods_name'] = $cus_info['name'];
        $_data['size'] = $size;
        $_data['cloth'] = $work_info['cloth'];
        $_data['quantity'] = 1;
        $_data['params'] = $crafts['params'];
        $_data['fabric'] = $crafts['fabric'];
        $_data['goods_id'] = $cus_info['id'];
        return $_data;
    }    
    
    
    /**
     *content
     *@author liang.li <1184820705@qq.com>
     *@2015年5月20日
     */
    function  getSuitWork($work_id,$size = '',$is_diy = 0)
    {
        $cus_mod = m('custom');
        $suit_list_mod = m('suitlist');
        $suit_relat_mod = m('suitrelat');
    
        $work_info = $this->mod->get($work_id); //=====  作品  =====
        $work_img_info = $this->mod_img->get("work_id = $work_id");
        if (!$work_info)
        {
            $this->json_error('无此作品!');
            exit;
        }
        
        $suit_relat_list = $suit_relat_mod->get("tz_id = {$work_info['cus_id']}"); //套装获得样衣id
        $cus_list = $cus_mod->find(array(
            'conditions' => "id IN({$suit_relat_list['jbk_id']})",
        ));
        if (!$is_diy) 
        {
            if (count($cus_list) != count($size))
            {
                $this->json_error('样衣个数和尺码个数不对称');
                exit;
            }
        }
       
        $data = array();
        $dis_ident = $this->dis_ident($work_info['cus_id']);
        foreach ($cus_list as $key => $value) 
        {
            $crafts = $this->getCustom($value['id']); //=====  工艺信息   =====
            $_data['user_id'] = $this->visitor->get('user_id');
            $_data['ident'] = $this->gen_ident();
            $_data['dis_ident'] = $dis_ident;
            $_data['suit_id'] = $work_info['cus_id'];
            $_data['source_from'] = "work";
            $_data['type'] = "suit";
            $_data['time'] = time();
            $_data['image'] = $work_img_info['img_url'];
            $_data['goods_name'] = $value['name'];
            if ($is_diy) 
            {
                $size_value = 'diy';
            }
            else 
            {
                $size_value = $size[$value['category']];
            }
            $_data['size'] = $size_value; //这里是在前面已经把尺码和品类拼装好数组了
            $_data['cloth'] = $value['category'];
            $_data['quantity'] = 1;
            $_data['params'] = $crafts['params'];
            $_data['fabric'] = $crafts['fabric'];
            $_data['goods_id'] = $value['id'];
            $data[] = $_data;
        }
        return $data;
        
        
        
        
        
        $cus_info = $cus_mod->get($work_info['cus_id']); //=====  样衣  =====
        if (!$cus_info)
        {
            $this->json_error('此作品无对应的样衣,可能是由于老数据的原因,请清理数据');
            exit;
        }
        $crafts = $this->getCustom($cus_info['id']); //=====  工艺信息   =====
        if (!$crafts)
        {
            $this->json_error('无法获得此样衣工艺信息！');
            exit;
        }
    
        /* $size_arr = explode(":", $arr0);//=====  格式化尺码尺码  =====
         $cate_arr = explode(":", $arr1); //=====  格式化品类  =====
         if (count($size_arr) != count($cate_arr))
         {
         $this->json_error('尺码信息参数有误');
         return;
         } */
        /*  if ($cus_info['category'] != $arr1)
         {
         $this->json_error('传入的品类和样衣的品类不匹配');
         exit;
         } */
        $_data['user_id'] = $this->visitor->get('user_id');
        $_data['ident'] = $this->gen_ident();
        $_data['source_from'] = "work";
        $_data['type'] = "dis";
        $_data['time'] = time();
        $_data['image'] = $work_img_info['img_url'];
        $_data['goods_name'] = $cus_info['name'];
        $_data['size'] = $size;
        $_data['cloth'] = $work_info['cloth'];
        $_data['quantity'] = 1;
        $_data['params'] = $crafts['params'];
        $_data['fabric'] = $crafts['fabric'];
        $_data['goods_id'] = $cus_info['id'];
        return $_data;
    }
    
   /**
   * 添加购物车表
   *@author liang.li <1184820705@qq.com>
   *@2015年5月20日
   */
   function toCart($_data) 
   {
//       dump($_data);
       $cart_mod = m('cart');
       $cart_info = $cart_mod->get("user_id={$_data['user_id']} AND goods_id={$_data['goods_id']} AND `type` = '{$_data['type']}' AND cloth= '{$_data['cloth']}' AND fabric= '{$_data['fabric']}' AND params = '{$_data['params']}' AND size= '{$_data['size']}' ");
       if ($cart_info) 
       {
           $cart_mod->setInc(array('rec_id'=>$cart_info['rec_id']),'quantity');
           return true;
       }
       
       
       if ($cart_mod->add($_data)) 
       {
           return true;
       }
       return false;
   }
   
   function gen_ident($id = 0){
       $cart_mod = m('cart');
       do{
           $str='abcdefghigklmnopqrstuvwxyz0123456789';
           $code ='';
           for($i=0;$i<5; $i++){
               $code .= $str[mt_rand(0, strlen($str)-1)];
           }
           $il = strlen($id);
           for ($i=$il;$i<10;$i++){
               $id = '0'.$id;
           }
           $ident =  $code.$id;
       }
       while($cart_mod->get("ident = '{$ident}'"));
       return $ident;
   }
   
   
   function dis_ident($id = 0){
       $cart_mod = m('cart');
       do{
           $str='abcdefghigklmnopqrstuvwxyz0123456789';
           for($i=0;$i<8; $i++){
               $code .= $str[mt_rand(0, strlen($str)-1)];
           }
           $il = strlen($id);
           for ($i=$il;$i<10;$i++){
               $id = '0'.$id;
           }
           $ident =  $code.$id;
       } while($cart_mod->get(" dis_ident = '{$ident}'"));
       return $ident;
   }
    
    
    /**
    *根据品类id获得尺码数组
    *@author liang.li <1184820705@qq.com>
    *@2015年5月19日
    */
    function getSize($cate_id) 
    {
//         echo PROJECT_PATH.'/includes/arrayfiles/size_json/'.$cate_id.'.size.json';exit;
        $filename = PROJECT_PATH.'/includes/arrayfiles/size_json/'.$cate_id.'.size.json';
//  dump($filename);
        $jsonString = file_get_contents($filename);
//   echo $jsonString;exit;
        $jsonData = json_decode($jsonString,true);
        $size_list = $jsonData['sizeAll'] ;
//  print_exit($size_list);exit;
        $size = array();
        foreach ($size_list as $key1 => $value1)
        {
            $size[] = $value1['Id'];
        }
//      dump($size);
        return $size;
    }
    
    /**
     *根据品类id获得尺码数组
     *@author liang.li <1184820705@qq.com>
     *@2015年5月19日
     */
    function getAllSize()
    {
        $cate_config = include_once ROOT_PATH.'m/includes/arrayfiles/config.php';
        $params = $this->get_params();
//       dump($params);
        $id = $params[0];
//      echo $id;
        $id_arr = explode(":",$id);
//     dump($id_arr);
        $size = array();
        if ($id_arr) 
        {
            foreach ($id_arr as $key => $value) 
            {
                if (!$value) 
                {
                    continue;
                }
                $size[$value]['item'] = $this->getSize($value);
                $size[$value]['cate_name'] = $cate_config[$value];
            }
        }
        echo json_encode($size);exit;
        
       $size['0003']['item'] = $this->getSize("0003");
       $size['0003']['cate_name'] = '西服';
       
       $size['0004']['item'] = $this->getSize("0004");
       $size['0004']['cate_name'] = '西裤';
       
       $size['0005']['item'] = $this->getSize("0005");
       $size['0005']['cate_name'] = '马甲';
        
       $size['0006']['item'] = $this->getSize("0006");
       $size['0006']['cate_name'] = '衬衣';
       
       $size['0007']['item'] = $this->getSize("0007");
       $size['0007']['cate_name'] = '大衣';
        
       
      /*  $size[]['0003'] = $this->getSize("0003");
       $size[]['cate_name'] = '西服';
       
       $size[]['0003'] = $this->getSize("0003");
       $size[]['cate_name'] = '西服';
       
       $size[]['0003'] = $this->getSize("0003");
       $size[]['cate_name'] = '西服'; */
       
       /* $size[0004] = $this->getSize("0004");
       $size[0005] = $this->getSize("0005");
       $size[0006] = $this->getSize("0006");
       $size[0007] = $this->getSize("0007"); */
       echo json_encode($size,JSON_UNESCAPED_UNICODE);
    }
    
    
    
}

?>
