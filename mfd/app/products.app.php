<?php
use Cyteam\Goods\Descartes;
use Cyteam\Goods\Products;
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
class ProductsApp extends BackendApp
{
	var $_product_mod;
    function __construct()
    {
        $this->ProductApp();
    }

    function ProductApp()
    {
        parent::__construct();
        $this->_product_mod =& m('products');
        define("PRODUCT", "product/");
    }

    function index()
    {
    	/* 是否存在商品id */
    	$goods_id = intval($_GET['goods_id']);
    	if (!$goods_id)
    	{
    		$this->show_warning('请先选择产品');
    		exit();
    	}
    	$db = db();
    	/* 取出商品信息 */
    	$goodsObject = m('goods');
    	$goods = $goodsObject->get($goods_id);
    	if (empty($goods))
    	{
    		$this->show_warning('找不到该产品');
    		exit;
    	}
    	$this->assign('sn', $goods['goods_sn']);
    	$this->assign('price', $goods['price']);
    	$this->assign('goods_name', $goods['goods_name']);
    	$this->assign('goods_sn', $goods['goods_sn']);
    	$this->assign('goods_id',     $goods_id);
    	
    	
    	/* 获取商品规格列表 */
    	$attribute = $this->get_goods_specifications_list($goods_id);
    	if (empty($attribute))
    	{
    		$this->assign('该产品没有规则');
    	}
//    dump($attribute);
    	foreach ($attribute as $attribute_value)
    	{
    		//转换成数组
    		$_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
    		$_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
    		$_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    	}
//    dump($_attribute);
    	$attribute_count = count($_attribute);
    	
    	$this->assign('attribute_count',          $attribute_count);
    	$this->assign('attribute_count_3',        ($attribute_count + 3));
    	$this->assign('attribute',                $_attribute);
    	$this->assign('product_sn',               $goods['goods_sn'] . '_');
    	$this->assign('product_number',           1);
    	
    	/* 取商品的货品 */
    	$product = $this->product_list($goods_id, '');
//     dump($product);
    	$this->assign('product_list', $product['product']);
    	
//     	$smarty->assign('ur_here',      $_LANG['18_product_list']);
    	/* $smarty->assign('action_link',  array('href' => 'goods.php?act=list', 'text' => '航拍列表'));
    	$smarty->assign('product_list', $product['product']);
    	$smarty->assign('product_null', empty($product['product']) ? 0 : 1);
    	$smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    	$smarty->assign('filter',       $product['filter']);
    	$smarty->assign('full_page',    1); */
        $this->display(PRODUCT.'product.index.html');
    }
    
    /**
    *content
    *@author  liang.li
    */
    function setspec() 
    {
        $typeId = isset($_REQUEST['type_id']) ? $_REQUEST['type_id'] : 0;
        $goodsId = isset($_REQUEST['goods_id']) ? $_REQUEST['goods_id'] : 0;
        $productsMdl = m('products');
        $goodsMdl = m('goods');

        if (IS_POST) 
        {
            if(!$typeId)
            {
                $this->show_warning('请先编辑商品类型');
                return;
            }
            $goodstypespecMdl = m("goodstypespec");
            $specificationMdl = m("specification");
            $specvaluesMdl = m('specvalues');
            $goodstypespecList = $goodstypespecMdl->find(array(
                'conditions' => "type_id = $typeId",
            ));
            $specarr = i_array_column($goodstypespecList, "spec_id");
            $conditions = db_create_in($specarr,"spec_id");
            if ($goodstypespecList)
            {
                $speciList = $specificationMdl->find(array(
                    'conditions' =>  $conditions,
                ));
                $specValList = $specvaluesMdl->find(array(
                    'conditions' =>  "spec_values.".$conditions,
                ));
                
            }

            if (!$_POST['bn'])
            {
                $this->show_warning('请录入数据');
                return;
            }


            //===== 必须有默认值  =====
            if (!isset($_POST['is_default'])) 
            {
                $this->show_warning('请设置默认值');
                return ;
            }
            if (count($_POST['is_default']) > 1)
            {
                $this->show_warning('默认值只能设置一个');
                return;
            }
            
            
            $product_id = $_POST['product_id'];
            foreach ($product_id as $key => $value) 
            {
                if (!$_POST['bn'][$key]) 
                {
                   continue;
                }

                
                $data['bn'] = $_POST['bn'][$key];
                $data['store'] =  $_POST['store'][$key];
                $data['price'] =  $_POST['price'][$key];
                $data['mktprice'] =  $_POST['mktprice'][$key];
                $data['weight'] =  $_POST['weight'][$key];
                $data['is_default'] =  $_POST['is_default'][$key];
                
                //===== spec Beg  =====
                $spec_value_id_arr = explode(",",trim($key,"'"));
                $spec_value_id = [];
                $spec_value    = [];
                //颜色：黑色、尺码：34、净含量：100g
                $spec_info = "";
                foreach ($spec_value_id_arr as $key1 => $value1)
                {
                    $pid = $specValList[$value1]['spec_id'];
                    $spec_value_id[$pid] = $value1;
                    $spec_value[$pid] = $specValList[$value1]['spec_value'];
                    $spec_info = $spec_info.$speciList[$pid]['spec_name']."：".$specValList[$value1]['spec_value']."、";
                }
                $spec_desc['spec_value'] = $spec_value;
                $spec_desc['spec_value_id'] = $spec_value_id;
                $data['spec_desc'] = serialize($spec_desc);
                $data['spec_info'] = trim($spec_info,"、");
                //===== spec End  =====
                $bn = $data['bn'];
                
                
                //=====  更新价格  =====
                if ($_POST['is_default'][$key] && $goodsId)
                {
                    $price = $_POST['price'][$key];
                    $goodsMdl->edit($goodsId,['price'=>$price]);
                }

                if ($value) //===== 更新   =====
                {
                    if($productsMdl->get("bn='$bn' AND product_id != $value"))
                    {
                        $this->show_warning('此货号已存在');
                        return;
                    }
                    
                    $productsMdl->edit($value,$data);
                }
                else  //===== 添加  =====
                {
                    
                    if($productsMdl->get("bn='$bn'"))
                    {
                        $this->show_warning('此货号已存在');
                        return;
                    }
                    
                    
                    if ($goodsId) 
                    {
                        $data['goods_id'] = $goodsId;
                        $productsMdl->add($data);
                    }
                }
            }
            $this->show_message('更新成功');
        }
        else 
        {
            if ($goodsId) 
            {
               
                $goodsInfo = $goodsMdl->get_info($goodsId);
                $typeId = $goodsInfo['type_id'];
                
                //===== 获取规格  =====
                $productLib = new Products();
                $productsList = $productLib->getFProducts($goodsId,0);
                foreach ($productsList['pd'] as $key => $value) 
                {
                    if (!$value['spec_desc']) 
                    {
                        continue;
                    }
                    $id = implode(",", $value['spec_desc']['spec_value_id']);
                    if (!$value['spec_desc']) 
                    {
                        continue;
                    }
                    $store = $value['store'];
                    $checked = "";
                    if ($value['is_default']) 
                    {
                        $checked = "checked";
                    }
                    $bn = $value['bn'];
                    $str = "<td>
		 	      <input type='hidden' name=product_id['".$id."'] value=".$key.">
		 	      <input class='x-input bn' type='text' value='$bn' name=bn['".$id."'] maxlength='30' id='dom_el_ce56040'>
		 	      </td>
                    
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$value['store']."' name=store['".$id."'] id='dom_el_ce56041'>    </td>
		 	    <td>
		 	          </td>
		 	    <td nowrap=''>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$value['price']."' name=price['".$id."'] id='dom_el_ce56042'>
		 	      </td>
                    
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$value['mktprice']."' name=mktprice['".$id."'] id='dom_el_ce56044'>    </td>
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$value['weight']."' name=weight['".$id."'] size='4' id='dom_el_ce56045'>
              </td>
		 	    <td>
		 	      <input type='checkbox' value='1' ".$checked." name=is_default['".$id."'] size='4'>
		 	    </td>
		 	    <td><a href='javascript:void(0);' class='clean ' title='不生成此货品' data-uid='58cd062ec7' onclick=delspe(this,".$key.")>清除</a></td>";
                  $productsList['pd'][$key]['fname'] = "<tr data-pid='".$id."' data-type='add'>" . "<td>".$value['spec_info']."</td>".$str."</tr>" ;
                }
                $this->assign('product_list',$productsList['pd']);
                
            }
            
            if ($typeId)
            {
                $goodstypespecMdl = m("goodstypespec");
                $specificationMdl = m("specification");
                $specvaluesMdl = m('specvalues');
                $goodstypespecList = $goodstypespecMdl->find(array(
                    'conditions' => "type_id = $typeId",
                ));
                $specarr = i_array_column($goodstypespecList, "spec_id");
                $conditions = db_create_in($specarr,"spec_id");
                if ($goodstypespecList)
                {
                    $speciList = $specificationMdl->find(array(
                        'conditions' =>  $conditions,
                    ));
                    $specValList = $specvaluesMdl->find(array(
                        'conditions' =>  "spec_values.".$conditions,
                    ));
                    foreach ($specValList as $key => $value)
                    {
                        if ($productsList['spe'][$value['spec_id']]['value'][$key]) 
                        {
                            $value['is_check'] = 1;
                        }
                        if (isset($speciList[$value['spec_id']]))
                        {
                            $speciList[$value['spec_id']]['val'][] = $value;
                        }
                    }
                }
            
                $this->assign('spec_list',$speciList);
                $this->assign('type_id',$typeId);
                $this->assign('goods_id',$goodsId);
            }
            
            $this->display(PRODUCT.'spec.html');
        }
       
    }
    
    /**
    *content
    *@author  liang.li
    */
    function fspec() 
    {
        $val = $_POST['name'];
        $type_id = $_POST['type_id'];
        $goods_id = $_POST['goods_id'];
        $valArr = explode(",", $val);
        $sp = [];
        foreach ((array)$valArr as $key => $value) 
        {
            if (!$value) 
            {
                continue;
            }
            $va = explode("-", $value);
            $pva = explode("_", $va[0]);
            $sp[$pva[0]][] = $va[1];
            $vname[$va[1]]['name'] = $va[2];
            $vname[$va[1]]['pname'] =$pva[1];
            $p[$pva[0]] = $pva[1];
        }

        $goodstypespecMdl = m("goodstypespec");
        $goodstypespecList = $goodstypespecMdl->find(array(
            'conditions' => "type_id = $type_id",
        ));
        $specarr = i_array_column($goodstypespecList, "spec_id");
        
        if (array_diff($specarr, array_keys($p))) 
        {
            $this->json_error('每个规格项至少选中一项，才能组合成完整的货品信息。');
            return;
        }
        
        //===== 过滤已经存在的货品  =====
        $productLib = new Products();
        $productSpecList = [];
        if ($goods_id) 
        {
            $productList = $productLib->getFProducts($goods_id);
            
            if (count($productList['pd']) == 1 && !array_values($productList['pd'])[0]['spec_desc'])
            {
                $defaultProduct = array_values($productList['pd'])[0];
            }
            else 
            {
                foreach ((array)$productList['pd'] as $key => $value)
                {
                    $spec_value_id = $value['spec_desc']['spec_value_id'];
                    $productSpecList[] = implode(",", $spec_value_id);
                }
            }
            
        }
        
        $result = [];
        $descartes = new Descartes(array_values($sp), $result);
        $descartes->calcDescartes(0, $result);
        $res = $descartes->resultArray;
        $ab = [];
        $i = 0;
//     echo '<pre>';print_r($defaultProduct);exit; 
        foreach ($res as $key => $value) 
        {
           
            $vid = "";
            $vn = "";
           foreach ($value as $key1 => $value1) 
           {
               $vid = $vid.",".$value1;
               $vn = $vn."、".$vname[$value1]['pname']."：".$vname[$value1]['name'];
           }
           $id = $a['id'] = trim($vid,",");
           $vnames = trim($vnames,",");
           if (in_array($id, $productSpecList)) 
           {
               continue;
           }
           $defchecked = "";
           $defcheckedval = 0;
           if ($defaultProduct) 
           {
               $a['name'] = trim($vn,"、");
               $bn = $defaultProduct['bn']."-".$i;
              
               $tstr = " <input type='hidden' name=product_id['".$id."'] value='0'> ";
               if (!$i) 
               {
                   $defchecked = "checked";
                   $defcheckedval = 1;
                   $tstr = " <input type='hidden' name=product_id['".$id."'] value='".$defaultProduct['product_id']."'> ";
               }
               $str = "<td>".$tstr."
		 	      <input class='x-input bn' type='text' value='".$bn."' name=bn['".$id."'] maxlength='30' id='dom_el_ce56040'>
		 	      </td>
		 	
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$defaultProduct['store']."' name=store['".$id."'] id='dom_el_ce56041'>    </td>
		 	    <td>
		 	          </td>
		 	    <td nowrap=''>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$defaultProduct['price']."' name=price['".$id."'] id='dom_el_ce56042'>
		 	      </td>
      
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$defaultProduct['mktprice']."' name=mktprice['".$id."'] id='dom_el_ce56044'>    </td>
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='".$defaultProduct['weight']."' name=weight['".$id."'] size='4' id='dom_el_ce56045'>
              </td>
		 	    <td>
		 	      <input type='checkbox' value='1' name=is_default['".$id."'] size='4' ".$defchecked.">
		 	    </td>
		 	    <td><a href='javascript:void(0);' class='clean ' title='不生成此货品' data-uid='58cd062ec7' onclick=delspe(this,0)>清除</a></td>";
               $a['fname'] = "<tr data-pid='".$id."' data-type='new'>" . "<td>".$a['name']."</td>".$str."</tr>" ;
           }
           else 
           {
               $a['name'] = trim($vn,"、");
               $str = "<td>
		 	      <input type='hidden' name=product_id['".$id."'] value='0'>
		 	      <input class='x-input bn' type='text' value='' name=bn['".$id."'] maxlength='30' id='dom_el_ce56040'>
		 	      </td>
		 	
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='' name=store['".$id."'] id='dom_el_ce56041'>    </td>
		 	    <td>
		 	          </td>
		 	    <td nowrap=''>
		 	      <input class='x-input' type='text' vtype='unsigned' value='' name=price['".$id."'] id='dom_el_ce56042'>
		 	      </td>
      
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='' name=mktprice['".$id."'] id='dom_el_ce56044'>    </td>
		 	    <td>
		 	      <input class='x-input' type='text' vtype='unsigned' value='' name=weight['".$id."'] size='4' id='dom_el_ce56045'>
              </td>
		 	    <td>
		 	      <input type='checkbox' value='1' name=is_default['".$id."'] size='4' >
		 	    </td>
		 	    <td><a href='javascript:void(0);' class='clean ' title='不生成此货品' data-uid='58cd062ec7' onclick=delspe(this,0)>清除</a></td>";
               $a['fname'] = "<tr data-pid='".$id."' data-type='new'>" . "<td>".$a['name']."</td>".$str."</tr>" ;
           }
//        echo '<pre>';print_r($vnames);exit;     
           $i++;
           $ab[] = $a;
        }
        
       
//       echo '<pre>';print_r($ab);exit; 
//         echo '<pre>';print_r($descartes->resultArray);exit;
        $this->json_result($ab);return;
//     echo '<pre>';print_r($ab);exit; 
        return;
//    echo '<pre>';print_r($p);exit; 
        /*  foreach ($p as $key => $value) 
         {
             if ($sp[$key]) 
             {
                 foreach ($sp[$key] as $i => $_a )
                 {
                     
                     foreach ($sp[3] as $ii => $_b )
                     {
                         foreach ($sp[5] as $iii => $_c )
                         {
                             $abk['value'] = $p[1].":".$_a.",".$p[3].":".$_b.",".$p[5].":".$_c;
                             $abk['id'] = $i.",".$ii.",".$iii;
                 
                             // $d[] = $p[1].":".$_a.",".$p[3].":".$_b.",".$p[5].":".$_c;
                             $d[] = $abk;
                         }
                     }
                 }
             }
            
         }  */
         
         foreach ($sp[1] as $i => $_a ){
            foreach ($sp[3] as $ii => $_b ){
                foreach ($sp[5] as $iii => $_c ){
                    $abk['value'] = $p[1].":".$_a.",".$p[3].":".$_b.",".$p[5].":".$_c;
                    $abk['id'] = $i.",".$ii.",".$iii;
                    
                   // $d[] = $p[1].":".$_a.",".$p[3].":".$_b.",".$p[5].":".$_c;
                   $d[] = $abk;
                }
            }
        } 
        
        
     echo '<pre>';print_r($d);exit; 
    }
    
    /**
    *删除
    *@author  liang.li
    */
    function dspec() 
    {
        $pid = $_POST['pid'];
        if (!$pid) 
        {
            $this->json_error('参数必传');
            return;
        }
        $productMdl = m('products');
        $productInfo = $productMdl->get_info($pid);
        $goodsId = $productInfo['goods_id'];
        $productList = $productMdl->find(array(
            'conditions' => "goods_id=".$goodsId
        ));
        if (count($productList) == 1)  //===== 如果只有最后一个货品 那么 不要删除    =====
        {
           $fp = array_values($productList);
           if ($fp[0]['product_id'] == $pid) 
           {
               $data['spec_info'] = "";
               $data['spec_desc'] = "";
               $productMdl->edit($pid,$data);
           }
        }
        else 
        {
            $productMdl->drop($pid);
        }
        $this->json_result('删除成功');
        
    }
    
   
    function add()
    {
//     	dump($_POST);
		$db = db();
    	$product['goods_id']        = intval($_POST['goods_id']);
    	$product['attr']            = $_POST['attr'];
    	$product['product_sn']      = $_POST['product_sn'];
    	$product['product_number']  = $_POST['product_number'];
    	
    	/* 是否存在商品id */
    	if (empty($product['goods_id']))
    	{
    		return false;exit;
    	}
    	
    	/* 判断是否为初次添加 */
    	$insert = true;
    	if ($this->product_number_count($product['goods_id']) > 0)
    	{
    		$insert = false;
    	}
    	
    	/* 取出商品信息 */
    	$goodsObject = m('goods');
    
    	$goods = $goodsObject->get($product['goods_id']);
    	if (empty($goods))
    	{
    		return false;exit;
    	}
    	
    	foreach($product['product_sn'] as $key => $value)
    	{
    		//不考虑库存  暂时都设为1
    		$product['product_number'][$key] = 1;
    	
    		//获取规格在商品属性表中的id
    		foreach($product['attr'] as $attr_key => $attr_value)
    		{
    			/* 检测：如果当前所添加的货品规格存在空值或0 */
    			if (empty($attr_value[$key]))
    			{
    				continue 2;
    			}
    	
    			$is_spec_list[$attr_key] = 'true';
    	
    			$value_price_list[$attr_key] = $attr_value[$key] . chr(9) . ''; //$key，当前
    	
    			$id_list[$attr_key] = $attr_key;
    		}
    		$goods_attr_id = $this->handle_goods_attr($product['goods_id'], $id_list, $is_spec_list, $value_price_list);
    		/* 是否为重复规格的货品 */
    		$goods_attr = $this->sort_goods_attr_id_array($goods_attr_id);
    		$data = array();
    		foreach ($goods_attr['row'] as $k=>$v)
    		{
    			$data[$k] = $v['attr_value'];
    		}
    		$goods_attr = serialize($data);
    		if ($this->check_goods_attr_exist($goods_attr, $product['goods_id']))
    		{
    			continue;
    		}
    	
    		//货品号不为空
    		if (!empty($value))
    		{
    			/* 检测：货品货号是否在商品表和货品表中重复 */
    			if ($this->check_goods_sn_exist($value))
    			{
    				continue;
    				//sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_goods_sn'], 1, array(), false);
    			}
    			if ($this->check_product_sn_exist($value))
    			{
    				continue;
    				//sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_product_sn'], 1, array(), false);
    			}
    		}
    	
    		/* 插入货品表 */
    		$sql = "INSERT INTO " . DB_PREFIX .'products' . " (goods_id, goods_spec, product_sn, product_number)  VALUES ('" . $product['goods_id'] . "', '$goods_attr', '$value', '" . $product['product_number'][$key] . "')";
    		if (!$db->query($sql))
    		{
    			continue;
    			//sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_add_products'], 1, array(), false);
    		}
    	
    		//货品号为空 自动补货品号
    		if (empty($value))
    		{
    			$sql = "UPDATE " . DB_PREFIX .'products' . "
                    SET product_sn = '" . $goods['goods_sn'] . "g_p" . $db->insert_id() . "'
                    WHERE product_id = '" . $db->insert_id() . "'";
    			$db->query($sql);
    		}
    		
    		/* 修改商品表库存 */
    		/* $product_count = product_number_count($product['goods_id']);
    		if (update_goods($product['goods_id'], 'goods_number', $product_count))
    		{
    			//记录日志
    			admin_log($product['goods_id'], 'update', 'goods');
    		} */
    	}
    	$this->show_message('更新货品成功',
    			'返回货品列表', 'index.php?app=products&goods_id=' . $product['goods_id'],
    			'返回商品列表', 'index.php?app=goods');
    }
    
    /**
     * 修改产品的货号
     */
    function edit_product_sn()
    {
    	$db = db();
	    $product_id       = intval($_POST['id']);
	    $product_sn       = $this->json_str_iconv(trim($_POST['val']));
	
	    if ($this->check_product_sn_exist($product_sn, $product_id))
	    {
	        $this->make_json_error('货号已存在');
	    }
	
	    /* 修改 */
	    $sql = "UPDATE " . DB_PREFIX . 'products' . " SET product_sn = '$product_sn' WHERE product_id = '$product_id'";
	    $result = $db->query($sql);
	    if ($result)
	    {
	        $this->make_json_result($product_sn);
	    }
    }
    
    /**
     * 修改产品的库存
     */
    function edit_product_number()
    {
    	$db = db();
    	$product_id       = intval($_POST['id']);
    	$product_number      = $this->json_str_iconv(trim($_POST['val']));
    
    
    	/* 修改 */
    	$sql = "UPDATE " . DB_PREFIX . 'products' . " SET product_number = '$product_number' WHERE product_id = '$product_id'";
    	$result = $db->query($sql);
    	if ($result)
    	{
    		$this->make_json_result($product_number);
    	}
    }
    
    /**
     * 删除货品
     */
    function product_remove()
    {
    	$db = db();
    	/* 是否存在商品id */
    	if (empty($_REQUEST['id']))
    	{
    		$this->make_json_error('product id is null');
    	}
    	else
    	{
    		$product_id = intval($_REQUEST['id']);
    	}
    	
    	/* 货品库存 */
    	//$product = get_product_info($product_id, 'product_number, goods_id');
    	$productInfo = $this->_product_mod->get($product_id);
    	$goodsId = $productInfo['goods_id'];
    	/* 删除货品 */
    	$sql = "DELETE FROM " . DB_PREFIX . 'products' . " WHERE product_id = '$product_id'";
    	$result = $db->query($sql);
    	if ($result)
    	{
    		/* 修改商品库存 */
    		/* if (update_goods_stock($product['goods_id'], $product_number - $product['product_number']))
    		{
    			admin_log('', 'update', 'goods');
    		}
    	
    		admin_log('', 'trash', 'products'); */
    	
    		/* $url = 'index.php?app=products&act=index&goods_id='.$goodsId;
    		header("Location: $url"); */
    		 $this->make_json_result(1);
    		exit;
    	}
    	else
    	{
    		 $this->make_json_result(0);
    	}
    }
    
    /**
     * 自定义 header 函数，用于过滤可能出现的安全隐患
     *
     * @param   string  string  内容
     *
     * @return  void
     **/
    function ecs_header($string, $replace = true, $http_response_code = 0)
    {
    	if (strpos($string, '../upgrade/index.php') === 0)
    	{
    		echo '<script type="text/javascript">window.location.href="' . $string . '";</script>';
    	}
    	$string = str_replace(array("\r", "\n"), array('', ''), $string);
    
    	if (preg_match('/^\s*location:/is', $string))
    	{
    		@header($string . "\n", $replace);
    
    		exit();
    	}
    
    	if (empty($http_response_code) || PHP_VERSION < '4.3')
    	{
    		@header($string, $replace);
    	}
    	else
    	{
    		@header($string, $replace, $http_response_code);
    	}
    } 
    /**
     * 创建一个JSON格式的错误信息
     *
     * @access  public
     * @param   string  $msg
     * @return  void
     */
    function make_json_error($msg)
    {
    	$this->make_json_response('', 1, $msg);
    }
    /**
     * 创建一个JSON格式的数据
     *
     * @access  public
     * @param   string      $content
     * @param   integer     $error
     * @param   string      $message
     * @param   array       $append
     * @return  void
     */
    function make_json_response($content='', $error="0", $message='', $append=array())
    {
    	include_once(ROOT_PATH . '/includes/libraries/cls_json.php');
    
    	$json = new JSON;
    
    	$res = array('error' => $error, 'message' => $message, 'content' => $content);
    
    	if (!empty($append))
    	{
    		foreach ($append AS $key => $val)
    		{
    			$res[$key] = $val;
    		}
    	}
    
    	$val = $json->encode($res);
    
    	exit($val);
    }
    
    /**
     *
     *
     * @access  public
     * @param
     * @return  void
     */
    function make_json_result($content, $message='', $append=array())
    {
    	$this->make_json_response($content, 0, $message, $append);
    }
    
    
    /**
     * 商品的货品规格是否存在
     *
     * @param   string     $goods_attr        商品的货品规格
     * @param   string     $goods_id          商品id
     * @param   int        $product_id        商品的货品id；默认值为：0，没有货品id
     * @return  bool                          true，重复；false，不重复
     */
    function check_goods_attr_exist($goods_attr, $goods_id, $product_id = 0)
    {
    	$db = db();
    	$goods_id = intval($goods_id);
    	if (strlen($goods_attr) == 0 || empty($goods_id))
    	{
    		return true;    //重复
    	}
    
    	if (empty($product_id))
    	{
    		$sql = "SELECT product_id FROM " . DB_PREFIX . 'products' ."
    		WHERE goods_spec = '$goods_attr'
    		AND goods_id = '$goods_id'";
    	}
    	else
    	{
    		$sql = "SELECT product_id FROM " . DB_PREFIX . 'products' ."
    		WHERE goods_spec = '$goods_attr'
    		AND goods_id = '$goods_id'
    		AND product_id <> '$product_id'";
    	}
    
    	$res = $db->getOne($sql);
    
    	if (empty($res))
    	{
    		return false;    //不重复
    	}
    	else
    	{
    		return true;    //重复
    	}
    }
    
    /**
     * 将 goods_attr_id 的序列按照 attr_id 重新排序
     *
     * 注意：非规格属性的id会被排除
     *
     * @access      public
     * @param       array       $goods_attr_id_array        一维数组
     * @param       string      $sort                       序号：asc|desc，默认为：asc
     *
     * @return      string
     */
    function sort_goods_attr_id_array($goods_attr_id_array, $sort = 'asc')
    {
    	$db = db();
    	if (empty($goods_attr_id_array))
    	{
    		return $goods_attr_id_array;
    	}
    
    	//重新排序
    	$sql = "SELECT a.attr_type, v.attr_value, v.goods_attr_id
            FROM " .DB_PREFIX . 'attribute' . " AS a
            LEFT JOIN " .DB_PREFIX . 'goods_attr' . " AS v
                ON v.attr_id = a.attr_id
                AND a.attr_type = 1
            WHERE v.goods_attr_id " . db_create_in($goods_attr_id_array) . "
                ORDER BY a.attr_id $sort";
    	$row = $db->GetAll($sql);
    
    	$return_arr = array();
    	foreach ($row as $value)
    	{
    		$return_arr['sort'][]   = $value['goods_attr_id'];
    
    		$return_arr['row'][$value['goods_attr_id']]    = $value;
    	}
    
    	return $return_arr;
    }
    
    
    /**
     * 插入或更新商品属性
     *
     * @param   int     $goods_id           商品编号
     * @param   array   $id_list            属性编号数组
     * @param   array   $is_spec_list       是否规格数组 'true' | 'false'
     * @param   array   $value_price_list   属性值数组
     * @return  array                       返回受到影响的goods_attr_id数组
     */
    function handle_goods_attr($goods_id, $id_list, $is_spec_list, $value_price_list)
    {
    	$goods_attr_id = array();
    	$db = db();
    	/* 循环处理每个属性 */
    	foreach ($id_list AS $key => $id)
    	{
    		$is_spec = $is_spec_list[$key];
    		if ($is_spec == 'false')
    		{
    			$value = $value_price_list[$key];
    			$price = '';
    		}
    		else
    		{
    			$value_list = array();
    			$price_list = array();
    			if ($value_price_list[$key])
    			{
    				$vp_list = explode(chr(13), $value_price_list[$key]);
    				foreach ($vp_list AS $v_p)
    				{
    					$arr = explode(chr(9), $v_p);
    					$value_list[] = $arr[0];
    					$price_list[] = $arr[1];
    				}
    			}
    			$value = join(chr(13), $value_list);
    			$price = join(chr(13), $price_list);
    		}
    
    		// 插入或更新记录
    		$sql = "SELECT goods_attr_id FROM " . DB_PREFIX . 'goods_attr' . " WHERE goods_id = '$goods_id' AND attr_id = '$id' AND attr_value = '$value' LIMIT 0, 1";
    		$result_id = $db->getOne($sql);
    		if (!empty($result_id))
    		{
    			$sql = "UPDATE " . DB_PREFIX . 'goods_attr' . "
    			SET attr_value = '$value'
    			WHERE goods_id = '$goods_id'
    			AND attr_id = '$id'
    			AND goods_attr_id = '$result_id'";
    
    			$goods_attr_id[$id] = $result_id;
    		}
    		else
    		{
    			$sql = "INSERT INTO " . DB_PREFIX .'goods_attr' . " (goods_id, attr_id, attr_value, attr_price) " .
    					"VALUES ('$goods_id', '$id', '$value', '$price')";
    		}
    
    		$db->query($sql);
    
    		if ($goods_attr_id[$id] == '')
    		{
    			$goods_attr_id[$id] = $db->insert_id();
    		}
    	}
    
    	return $goods_attr_id;
    }
    
    /**
     * 获得商品的货品有无数据
     *
     * @access      public
     * @params      integer     $goods_id       商品id
     * @params      string      $conditions     sql条件，AND语句开头
     * @return      string number
     */
    function product_number_count($goods_id, $conditions = '')
    {
    	$db = db();
    	if (empty($goods_id))
    	{
    		return -1;  //$goods_id不能为空
    	}
    
    	/* $sql = "SELECT SUM(product_number)
            FROM " . DB_PREFIX . 'products' . "
                WHERE goods_id = '$goods_id'
                " . $conditions;
    	$nums = $db->getOne($sql);
    	$nums = empty($nums) ? 0 : $nums; */
    	$num = 0;
    	$product = $this->_product_mod->get("goods_id=$goods_id");
    	if ($product)
    	{
    		$nums = 1;
    	}
    
    	return $nums;
    }
    
    /**
     * 商品货号是否重复
     *
     * @param   string     $goods_sn        商品货号；请在传入本参数前对本参数进行SQl脚本过滤
     * @param   int        $goods_id        商品id；默认值为：0，没有商品id
     * @return  bool                        true，重复；false，不重复
     */
    function check_goods_sn_exist($goods_sn, $goods_id = 0)
    {
    	$db = db();
    	$goods_sn = trim($goods_sn);
    	$goods_id = intval($goods_id);
    	if (strlen($goods_sn) == 0)
    	{
    		return true;    //重复
    	}
    
    	if (empty($goods_id))
    	{
    		$sql = "SELECT goods_id FROM " . DB_PREFIX .'goods' ."
    		WHERE goods_sn = '$goods_sn'";
    	}
    	else
    	{
    		$sql = "SELECT goods_id FROM " . DB_PREFIX .'goods' ."
    		WHERE goods_sn = '$goods_sn'
    		AND goods_id <> '$goods_id'";
    	}
    
    	$res = $db->getOne($sql);
    
    	if (empty($res))
    	{
    		return false;    //不重复
    	}
    	else
    	{
    		return true;    //重复
    	}
    
    }
    
    /**
     * 商品的货品货号是否重复
     *
     * @param   string     $product_sn        商品的货品货号；请在传入本参数前对本参数进行SQl脚本过滤
     * @param   int        $product_id        商品的货品id；默认值为：0，没有货品id
     * @return  bool                          true，重复；false，不重复
     */
    function check_product_sn_exist($product_sn, $product_id = 0)
    {
    	$db = db();
    	$product_sn = trim($product_sn);
    	$product_id = intval($product_id);
    	if (strlen($product_sn) == 0)
    	{
    		return true;    //重复
    	}
    
    	if (empty($product_id))
    	{
    		$sql = "SELECT product_id FROM " . DB_PREFIX . 'products' ."
    		WHERE product_sn = '$product_sn'";
    	}
    	else
    	{
    		$sql = "SELECT product_id FROM " . DB_PREFIX . 'products' ."
    		WHERE product_sn = '$product_sn'
    		AND product_id <> '$product_id'";
    	}
    
    	$res = $db->getOne($sql);
    
    	if (empty($res))
    	{
    		return false;    //不重复
    	}
    	else
    	{
    		return true;    //重复
    	}
    }
    
    
    /**
     * 获得商品已添加的规格列表
     *
     * @access      public
     * @params      integer         $goods_id
     * @return      array
     */
    function get_goods_specifications_list($goods_id)
    {
    	$db = &db();
    	if (empty($goods_id))
    	{
    		return array();  //$goods_id不能为空
    	}
    
    	$sql = "SELECT g.goods_attr_id, g.attr_value, g.attr_id, a.attr_name
            FROM " .DB_PREFIX . 'goods_attr' . " AS g
                LEFT JOIN " . DB_PREFIX . 'attribute' . " AS a
                    ON a.attr_id = g.attr_id
                    WHERE goods_id = '$goods_id'
                    AND a.attr_type = 1
                    ORDER BY g.attr_id ASC";
                    $results = $db->getAll($sql);
    
        return $results;
    }
    
    /**
     * 获得商品的货品列表
     *
     * @access  public
     * @params  integer $goods_id
     * @params  string  $conditions
     * @return  array
     */
    function product_list($goods_id, $conditions = '')
    {
    	$db = db();
    	/* 过滤条件 */
    	$param_str = '-' . $goods_id;
    	$result = $this->get_filter($param_str);
    	if ($result === false)
    	{
    		$day = getdate();
    		$today = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
    
    		$filter['goods_id']         = $goods_id;
    		$filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
    		$filter['stock_warning']    = empty($_REQUEST['stock_warning']) ? 0 : intval($_REQUEST['stock_warning']);
    
    		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    		{
    			$filter['keyword'] = $this->json_str_iconv($filter['keyword']);
    		}
    		$filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'product_id' : trim($_REQUEST['sort_by']);
    		$filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    		$filter['extension_code']   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    		$filter['page_count'] = isset($filter['page_count']) ? $filter['page_count'] : 1;
    
    		$where = '';
    
    		/* 库存警告 */
    		if ($filter['stock_warning'])
    		{
    			$where .= ' AND goods_number <= warn_number ';
    		}
    
    		/* 关键字 */
    		if (!empty($filter['keyword']))
    		{
    			$where .= " AND (product_sn LIKE '%" . $filter['keyword'] . "%')";
    		}
    
    		$where .= $conditions;
    
    		/* 记录总数 */
    		$sql = "SELECT COUNT(*) FROM " .DB_PREFIX .'products'. " AS p WHERE goods_id = $goods_id $where";
    		$filter['record_count'] = $db->getOne($sql);
    
    		$sql = "SELECT product_id, goods_id, goods_spec, product_sn, product_number
                FROM " . DB_PREFIX .'products' . " AS g
                    WHERE goods_id = $goods_id $where
                    ORDER BY $filter[sort_by] $filter[sort_order]";
    
    		$filter['keyword'] = stripslashes($filter['keyword']);
    		$this->set_filter($filter, $sql, $param_str);
    	}
    	else
    	{
    		$sql    = $result['sql'];
    		$filter = $result['filter'];
    	}
    	$row = $db->getAll($sql);
    
    	/* 处理规格属性 */
    	/* $goods_attr = $this->product_goods_attr_list($goods_id);
    	foreach ($row as $key => $value)
    	{
    		$_goods_attr_array = explode('|', $value['goods_spec']);
    		if (is_array($_goods_attr_array))
    		{
    			$_temp = '';
    			foreach ($_goods_attr_array as $_goods_attr_value)
    			{
    				$_temp[] = $goods_attr[$_goods_attr_value];
    			}
    			$row[$key]['goods_spec'] = $_temp;
    		}
    	} */
//     dump($row);
    	foreach ($row as  $key=>$value)
    	{
    		$row[$key]['goods_spec'] = unserialize($value['goods_spec']);
    	}
    
    	return array('product' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
    
    /**
     * 获得商品的规格属性值列表
     *
     * @access      public
     * @params      integer         $goods_id
     * @return      array
     */
    function product_goods_attr_list($goods_id)
    {
    	$db = db();
    	if (empty($goods_id))
    	{
    		return array();  //$goods_id不能为空
    	}
    
    	$sql = "SELECT goods_attr_id, attr_value FROM " . DB_PREFIX .'goods_attr' . " WHERE goods_id = '$goods_id'";
    	$results = $db->getAll($sql);
    
    	$return_arr = array();
    	foreach ($results as $value)
    	{
    		$return_arr[$value['goods_attr_id']] = $value['attr_value'];
    	}
    
    	return $return_arr;
    }
    
    
    /**
     * 取得上次的过滤条件
     * @param   string  $param_str  参数字符串，由list函数的参数组成
     * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
     */
    function get_filter($param_str = '')
    {
    	$filterfile = basename(PHP_SELF, '.php');
    	if ($param_str)
    	{
    		$filterfile .= $param_str;
    	}
    	if (isset($_GET['uselastfilter']) && isset($_COOKIE['ECSCP']['lastfilterfile'])
    			&& $_COOKIE['ECSCP']['lastfilterfile'] == sprintf('%X', crc32($filterfile)))
    	{
    		return array(
    				'filter' => unserialize(urldecode($_COOKIE['ECSCP']['lastfilter'])),
    				'sql'    => base64_decode($_COOKIE['ECSCP']['lastfiltersql'])
    		);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * 将JSON传递的参数转码
     *
     * @param string $str
     * @return string
     */
    function json_str_iconv($str)
    {
    	if (EC_CHARSET != 'utf-8')
    	{
    		if (is_string($str))
    		{
    			return $this->ecs_iconv('utf-8', EC_CHARSET, $str);
    		}
    		elseif (is_array($str))
    		{
    			foreach ($str as $key => $value)
    			{
    				$str[$key] = $this->json_str_iconv($value);
    			}
    			return $str;
    		}
    		elseif (is_object($str))
    		{
    			foreach ($str as $key => $value)
    			{
    				$str->$key = $this->json_str_iconv($value);
    			}
    			return $str;
    		}
    		else
    		{
    			return $str;
    		}
    	}
    	return $str;
    }
    
    function ecs_iconv($source_lang, $target_lang, $source_string = '')
    {
    	static $chs = NULL;
    
    	/* 如果字符串为空或者字符串不需要转换，直接返回 */
    	if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
    	{
    		return $source_string;
    	}
    
    	if ($chs === NULL)
    	{
    		require_once(ROOT_PATH . 'includes/libraries/cls_iconv.php');
    		$chs = new Chinese(ROOT_PATH);
    	}
    
    	return $chs->Convert($source_lang, $target_lang, $source_string);
    }
    
    /**
     * 保存过滤条件
     * @param   array   $filter     过滤条件
     * @param   string  $sql        查询语句
     * @param   string  $param_str  参数字符串，由list函数的参数组成
     */
    function set_filter($filter, $sql, $param_str = '')
    {
    	$filterfile = basename(PHP_SELF, '.php');
    	if ($param_str)
    	{
    		$filterfile .= $param_str;
    	}
    	setcookie('ECSCP[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
    	setcookie('ECSCP[lastfilter]',     urlencode(serialize($filter)), time() + 600);
    	setcookie('ECSCP[lastfiltersql]',  base64_encode($sql), time() + 600);
    }
    
    
    
    
    
    
    
}

?>
