<?php
use Cyteam\Shop\Type\FdiyBase;
/**
 * Diy 相关操作
 * 
 * @date 2015-9-6 下午2:25:09
 * @author Ruesin
 */
class Diys extends Object
{	
    function __construct()
    {
        $this->Diys();
    }
    function Diys()
    {
        
    }
    
    public $coeff_fabric  = 5;
    public $coeff_fabric_than  = 3; //面料费 ≥ 102元时，面料系数 = 3
    public $coeff_fabric_less  = 2; //面料费 ＜102元时，面料系数=1 2
    public $coeff_process = 4;
    
    public $coefficient = 5; // 溢价系数 2015-10-08 10.71版本
    public $coeff       = 4; // 溢价系数 2015-10-08 10.71版本
    public static $_lining_parents = array(313,6291,103136);   //里料父ID  //不确定....
   // public static $_process_parents = array(1230,431,2224,4992); //工艺父ID
    
    public static $_process_parents = array(
        1=>array('id'=>"1",'name'=>'功效'),
        6=>array('id'=>"6",'name'=>'口味'),
        11=>array('id'=>"11",'name'=>'规格'),
        16=>array('id'=>"16",'name'=>'包装'),
        21=>array('id'=>"21",'name'=>'犬种'),
        34=>array('id'=>"34",'name'=>'犬期'),
        
    ); //工艺父ID
    
    /**
     * 根据面料和品类计算价格
     * 
     * params 二维数组 品类=>面料 [0=>['0003'=>'BNDS123','0004'=>'SNDGS12'],1=>['0005'=>'FDJDO123']];
     * process 工艺 (暂时无用)
     * lining  里料 (暂时无用)
     * @date 2015-10-28 上午9:36:04
     * @author Ruesin
     */
    function _calcPrice($params = array(),$process = array(),$lining = array()){ 
        //$params = [123=>['0006'=>'SDA349A','0003'=>'SDA351A'],124=>['0005'=>'SDA352A']];
        //$params = [123=>['0003'=>'DBP742A']];
        foreach ($params as $pRow){
            foreach ($pRow as $cKey=>$cVal){
                $fbs[] = $cVal;
            }
        }
        if (!$fbs) return false;
        
        $mFabricPrice = &m('fabricprice');
        $mFabric      = &m('fabric');
        
        $fabricPrice = $mFabricPrice->find(array(
                'fields' => 'FABRICCODE code,RMBPRICE price',
                'conditions' => " 1 = 1 AND AREAID = '20151' AND ".db_create_in($fbs,'FABRICCODE'),
                'index_key' => 'code',
        ));
        
        $fabrics = $mFabric->find(array('conditions'=>db_create_in($fbs,'CODE') ,'index_key'=>'CODE')); //. " AND is_sale = 1"
        
        foreach ($fabricPrice as &$row){
            $row['is_sale'] = $fabrics[$row['code']]['is_sale'];
            if($fabrics[$row['code']]['activity'] == '1' && ($fabrics[$row['code']]['activity_price_0003']>0 || $fabrics[$row['code']]['activity_price_0004']>0 || $fabrics[$row['code']]['activity_price_0005']>0 || $fabrics[$row['code']]['activity_price_0006']>0 || $fabrics[$row['code']]['activity_price_0007']>0)){
    
                $row['activity'] = $fabrics[$row['code']]['activity'];
                $row['activity_price_0003'] = $fabrics[$row['code']]['activity_price_0003'];
                $row['activity_price_0004'] = $fabrics[$row['code']]['activity_price_0004'];
                $row['activity_price_0005'] = $fabrics[$row['code']]['activity_price_0005'];
                $row['activity_price_0006'] = $fabrics[$row['code']]['activity_price_0006'];
                $row['activity_price_0007'] = $fabrics[$row['code']]['activity_price_0007'];
            }
            elseif ($fabrics[$row['code']]['sales__price_0003'] > 0 || $fabrics[$row['code']]['sales__price_0004'] > 0 || $fabrics[$row['code']]['sales__price_0004'] > 0 || $fabrics[$row['code']]['sales__price_0005'] > 0 || $fabrics[$row['code']]['sales__price_0006'] > 0 || $fabrics[$row['code']]['sales__price_0007'] > 0) //=====  单独销售的面料  =====
            {
                $row['sales__price'] = 1;
                $row['sales__price_0003'] = $fabrics[$row['code']]['sales__price_0003'];
                $row['sales__price_0004'] = $fabrics[$row['code']]['sales__price_0004'];
                $row['sales__price_0005'] = $fabrics[$row['code']]['sales__price_0005'];
                $row['sales__price_0006'] = $fabrics[$row['code']]['sales__price_0006'];
                $row['sales__price_0007'] = $fabrics[$row['code']]['sales__price_0007'];
            }
        }
        $customs = $this->_customs();
        $return = array();
        foreach ($params as $key=>$rows){
            $p = 0;
            $is_sale = true;
            foreach ($rows as $k=>$v){
                if (isset($fabricPrice[$v]['activity']) && $fabricPrice[$v]['activity'] == '1' && isset($fabricPrice[$v]['activity_price_'.$k]) && $fabricPrice[$v]['activity_price_'.$k] >0){
                    $p += $fabricPrice[$v]['activity_price_'.$k];
                }elseif(isset($fabricPrice[$v]['sales__price']) && $fabricPrice[$v]['sales__price'] == 1 && isset($fabricPrice[$v]['sales__price_'.$k]) && $fabricPrice[$v]['sales__price_'.$k] > 0){
                    $p += $fabricPrice[$v]['sales__price_'.$k];
                }else{
                    
                    //$p += (($customs[$k]['fabric_m'] * $fabricPrice[$v]['price']) * $this->coeff_fabric+ $customs[$k]['process_fee'] * $this->coefficient);  // 面料费 * 单耗 * 系数1 + 加工费 * 系数2
                    
                    if ($fabricPrice[$v]['price'] >= 102){
                        $p += (($customs[$k]['fabric_m'] * $fabricPrice[$v]['price']) * $this->coeff_fabric_than + $customs[$k]['process_than'] * $this->coeff);  // (面料 + 里料 + 工艺) * 系数1 + 加工费 * 系数2
                    }else{
                        $p += (($customs[$k]['fabric_m'] * $fabricPrice[$v]['price']) * $this->coeff_fabric_less + $customs[$k]['process_less'] * $this->coeff);  // (面料 + 里料 + 工艺) * 系数1 + 加工费 * 系数2
                    }
                    
                }
                
                if($fabricPrice[$v]['is_sale'] != 1) $is_sale = false;
            }
            
            $return[$key] = array('price'=>sprintf('%.2f',intval($p)),'is_sale'=> $is_sale);
        }
        return $return;
        $res = array(
                'basePrice' => ($customs['fabric_m'] * $item['fabric_p']) + $item['process_p'],  //面料 + 里料 + 工艺 + 加工费
                'markprice' => $this->_format_price_int($mPrice),
                //'price'     => $this->_format_price_int($mPrice * $this->_memDisCount),
        
        );
        
    }
    
    
    /**
     * 根据面料和品类计算价格
     *
     * params 二维数组 品类=>面料 [0=>['0003'=>'BNDS123','0004'=>'SNDGS12'],1=>['0005'=>'FDJDO123']];
     * process 工艺 (暂时无用)
     * lining  里料 (暂时无用)
     * @date 2016-01-15 
     * @author liang.li
     */
    function _getPrice($params = array(),$process = array(),$lining = array()){
        //$params = [123=>['0006'=>'SDA349A','0003'=>'SDA351A'],124=>['0005'=>'SDA352A']];
        //$params = [123=>['0003'=>'DBP742A']];
        foreach ($params as $pRow){
            foreach ($pRow as $cKey=>$cVal){
                $fbs[] = $cVal;
            }
        }
        if (!$fbs) return false;
    
        $mFabricPrice = &m('fabricprice');
        $mFabric      = &m('fabric');
    
        $fabricPrice = $mFabricPrice->find(array(
            'fields' => 'FABRICCODE code,RMBPRICE price',
            'conditions' => " 1 = 1 AND AREAID = '20151' AND ".db_create_in($fbs,'FABRICCODE'),
            'index_key' => 'code',
        ));
        if (!$fabricPrice) 
        {
            return false;
        }
//  print_exit($fabricPrice);   
        $fabrics = $mFabric->find(array('conditions'=>db_create_in($fbs,'CODE') ,'index_key'=>'CODE')); //. " AND is_sale = 1"
    
        $customs = $this->_customs();
        $return = array();
        foreach ($params as $key=>$rows){
            $p = 0;
            $is_sale = true;
            foreach ($rows as $k=>$v)
            {
                 if ($fabricPrice[$v]['price'] >= 102)
                 {
                    $p += (($customs[$k]['fabric_m'] * $fabricPrice[$v]['price']) * $this->coeff_fabric_than + $customs[$k]['process_than'] * $this->coeff);  // (面料 + 里料 + 工艺) * 系数1 + 加工费 * 系数2
                 }
                 else
                 {
                    $p += (($customs[$k]['fabric_m'] * $fabricPrice[$v]['price']) * $this->coeff_fabric_less + $customs[$k]['process_less'] * $this->coeff);  // (面料 + 里料 + 工艺) * 系数1 + 加工费 * 系数2
                 }
        
                if($fabricPrice[$v]['is_sale'] != 1) $is_sale = false;
            }
    
            $return[$key] = array('price'=>sprintf('%.2f',intval($p)),'is_sale'=> $is_sale);
        }
        return $return;
        $res = array(
            'basePrice' => ($customs['fabric_m'] * $item['fabric_p']) + $item['process_p'],  //面料 + 里料 + 工艺 + 加工费
            'markprice' => $this->_format_price_int($mPrice),
            //'price'     => $this->_format_price_int($mPrice * $this->_memDisCount),
    
        );
    
    }
    
    /**
     * DIY面料价格计算
     *
     * @date 2016年1月16日 下午4:30:48
     *
     * @author Ruesin
     */
    public function calcDiyPrice($ids,$custom){
        // 系统后台diy相关配置
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); 
        $mPrice = 0;
        $bPrice = 0;
        $gongxiao_id = "";
        if ($ids && is_array(explode(',', $ids)))
        {
            $mFbcategory     = &m('fbcategory');
            $fPrice = $mFbcategory->find(array(
                'fields' => 'cate_id,cate_name,uprice,fprice,parent_id,ve',
                'conditions' => " 1 = 1 AND if_show = 1 AND ".db_create_in(explode(',', $ids),'cate_id'),
                'index_key' => 'cate_id',
            ));
           foreach ((array)$fPrice as $key => $value) 
           {
               if (isset(self::$_process_parents[$value['parent_id']])) 
               {
                   
                   if (self::$_process_parents[$value['parent_id']]['id'] == 34) //犬期
                   {
                       $age_id = $value['cate_id'];
                   }
                   
                   if (self::$_process_parents[$value['parent_id']]['id'] ==11) //规格id
                   {
                       $guige_id = $value['cate_id'];
                   }
                   
                   if (self::$_process_parents[$value['parent_id']]['id'] == 16) //包装id
                   {
                       $baozhuang_id = $value['cate_id'];
                   }
                   
                   if (self::$_process_parents[$value['parent_id']]['id'] == 1) //功效id
                   {
                       $gongxiao_id .= $value['cate_id']."|";
                   }
               }
               else //犬种
               {
                   $cate_id = $key;
               }
               
           }
           $fidy_base = new FdiyBase();
           $is_try = isset($custom['is_try']) ? $custom['is_try'] : 0;;
           $jiliao_price_arr = $fidy_base->fmoatPrice($cate_id, $age_id,$baozhuang_id,$guige_id,$gongxiao_id,1,$is_try);

           if (!$jiliao_price_arr) 
           {
               return false;
           }
           $b = $jiliao_price_arr['jiliao_price'];//基料价格
           $d = $jiliao_price_arr['guige_price'];//包装价格
           $gp_price = $jiliao_price_arr['gp_price'];
           $is_jianfei = $jiliao_price_arr['is_alone'];
           $is_youhui = $jiliao_price_arr['is_youhui'];
            /* 动态分类id 以后扩展还需要根据 大分类id （猫狗）  */
           //总量是属性维护那里的规格，      
            $aPid = 11;
           //基本料指的是口味，             
            $bPid = 6; 
           //功能料指功效，                 
            $cPid = 1;
           //包装价格也是属性管理那          
            $dPid = 16;
            
            $aprice = isset($setting["diy_aprice"]) ? $setting["diy_aprice"] : 1.5;     //功能料均价
            $ratio = isset($setting["diy_ratio"]) ? $setting["diy_ratio"] : 25;         //单个功能料占比 /100 
            $maxnum = isset($setting["diy_maxnum"]) ? $setting["diy_maxnum"] : 3;       //功能料种数上限 
    
                    
            /*总价格 = （商品总量（克） * 基料价格（元每克） * （1-25%*N） ）  +
            （
            商品总量（克）* 25% * N * 功能料平均价格（元每克）*
            （功能料A的价格系数+功能料B的价格系数+ ....）/功能料的数量
            
            ）+ 包装价格 + 运输价格。 */
            $i = 1;
            foreach ((array)$fPrice as $r){
                if ($r['parent_id'] == $aPid)
                {
                    $a = $r['ve'];//重量
                    continue;
                }
                if ($r['parent_id'] == $bPid)
                {
                    //$b = $r['uprice'];//口味
                    continue;
                }
                if ($r['parent_id'] == $cPid && $i<=$maxnum)
                {
//                     if ($r['cate_id'] == 107) //减肥瘦身
//                     {
//                         $is_jianfei = 1;
//                     }
                    //$c[$i] =sprintf("%.2f",$r['uprice']/$aprice);
                    $c[$i] =sprintf("%.2f",$gp_price[$r['cate_id']]/$aprice);
                    $i++;
                }
                if ($r['parent_id'] == $dPid)
                {
                   // $d = $r['fprice'];//包装
                    continue;
                }
                
            }
        }
        
        if ($is_jianfei) //减肥瘦身  功能料占比100 基料为0
        {
            $ratio = 100;
            $b = 0;
        }
        
        //计算基料价格
        
//   print_exit($ids);
//         var_dump($a,$b,$c,$d);
//         echo "a : ".$a."<br/>\t\n";
//         echo "b : ".$b."<br/>\t\n";
//         echo "c : ".var_dump($c)."<br/>\t\n";
//         echo "d : ".$d."<br/>\t\n";
//         echo "aprice : ".$aprice."<br/>\t\n";
        $maxnum = (count($c)) ;
//         var_dump($maxnum,$ratio);
        $mPrice = $a*$b*(1-$ratio/100*$maxnum);
//         echo "mPrice : ".$mPrice."<br/>\t\n";
        $cnt = 0;
        foreach ((array)$c as $val)
        {
            $cnt += $val;
        }
// print_exit($cnt);
//         echo "cnt : ".$cnt."<br/>\t\n";
        @$mPrice += $a*$ratio/100*$maxnum*$aprice*$cnt/$maxnum;
//         echo "$maxnum";
//         echo "mPrice : ".$mPrice."<br/>\t\n";
        $mPrice += $d;
        $mPrice = $mPrice * $is_youhui;
//         echo "mPrice : ".$mPrice."<br/>\t\n";
        return [
            'markprice' => $mPrice,
            'baseprice' => $mPrice,
            'process_p' => '',
            'weight'    => $a,
        ];
    
    }
    public function get_cname($ids,$ph='-',$m='fbcategory')
    {
        if (!$ids) return ;
        $mFb = &m($m);
        $cn = [];
        $fbcategorys = $mFb->find(array('conditions'=>db_create_in(explode(',', $ids),'cate_id') ,'index_key'=>'cate_id'));
  
                foreach ($fbcategorys as $key=>$val){
                    $cn[] = $val['cate_name'];
                }
    
            return implode($ph, $cn);
    }
    
    //获取名称
    public function get_cname_arr($ids,$ph='-',$m='fbcategory')
    {
        if (!$ids) return ;
        $mFb = &m($m);
        $cn = [];
        $cns = [];
        $fbcategorys = $mFb->find(array('conditions'=>db_create_in(explode(',', $ids),'cate_id') ,'index_key'=>'cate_id'));
//  print_exit($fbcategorys);   
        foreach ($fbcategorys as $key=>$val){
            if (isset(self::$_process_parents[$val['parent_id']])) 
            {
//                 echo 1;exit;
// print_exit($val);
                $cns[self::$_process_parents[$val['parent_id']]['name']] .= $val['cate_name']."/";
// print_exit($cns);
            }
            else 
            {
                $cns[self::$_process_parents[21]['name']] .= $val['cate_name']."/";
            }
            
            //$cn[] = $val['cate_name'];
        }
        foreach ((array)$cns as $key => $value)
        {
            $cns[$key] = trim($value,"/");
        }
       return $cns;
    }
    
    public function _format_params($pData)
    {
        if (!$pData || !is_array($pData))
        {
            return ;
        }
        $data = $this->_fd($pData);
        
        if ($data['ids'])
        {
            $mFb = &m('fbcategory');
            $fbcategorys = $mFb->find(array('conditions'=>db_create_in($data['ids'],'cate_id') ,'index_key'=>'cate_id'));
        }
        unset($data);
        $data = $this->_fd($pData,$fbcategorys);
      
        return $data['str'];
    }
    protected function _fd($pData,$data=array())
    {
        $ids = [];
        $str = '';
        foreach ((array)$pData as $item)
        {
            $rows = $r = [];
            $rows = explode(":", $item);
            
            if ($data && $data[$rows[0]])
            {
                $str .= $data[$rows[0]]['cate_name'].":";
            }
            
            $ids[] =$rows[0];
            $rows[1] = str_replace('"', '',$rows[1]);
            $r = explode("|", $rows[1]);
            foreach ((array)$r as $val)
            {
                $ids[] = $val;
                if ($data && $data[$val])
                {
                    $str .= $data[$val]['cate_name'].'、';
                }
            }
        }
        
        return ['ids'=>$ids,'str'=>rtrim($str,'、')];
    }
    
    /**
     * DIY 所有品类集合
     * 
     * 麦富迪众创定价规则，2016.1.1日正式生效：
     * 
     * 基础公式：加工费×4+面料费×单耗×面料系数（比如5）
     * 
     * 1）加工费取决于面料费和品类，  2）面料系数取决于面料费,   3）单耗取决于品类
     * 
     * 单耗：套装=3.5米；上衣=2.0米；裤子=1.5米；马甲=1米；衬衣=1.5米；大衣=2.5米
     * 单耗：上衣=1.75米；裤子=1.15米；马甲=0.7米；衬衣=1.49米；大衣=2.3米 
     * 当 面料费 ≥ 102元时，面料系数 = 3，工艺：半毛衬
     * 加工费：套装=1180；上衣=780；裤子=400；马甲=250；衬衣=150   160；大衣=1000
     * 
     * 当面料费 ＜ 102元时，面料系数=1，工艺：粘合衬  2 
     * 加工费：套装=880；上衣=580；裤子=300；马甲=200；衬衣=100 130；大衣=800
     * 
     * @date 2015-10-28 上午9:35:10
     * @author Ruesin
     */
    function _customs(){
        return array(
                '0001' =>array(
                        'cate_id' => '1',
                        'cate_name' => '套装(2pcs)',
                        'fabric_m' => '3.5',
                        'lining_m' => '2',
                        'gender' => 10040,
                        'process_fee' => '580',
                        'one_fee' => '300',
                        'process_than' => '1180',
                        'process_less' => '880',
                        'fcate' => 10,
                ),
                '0003' => array(
                        'cate_id' => '3',
                        'cate_name' => '西服',
                        'fabric_m' => '2',
                        'lining_m' => '2', // 里料单号基数
                        'gender' => 10040,
                        'process_fee' => '380',   //加工费 2015-10-08 10.71版本
                        'one_fee' => '230', // 买一赠一加工费
                        'emb' => 417, // 刺绣设计
                        'process_than' => '780',
                        'process_less' => '580',
                        'fcate' => 1,
                ),
                '0004' => array(
                        'cate_id' => '2000',
                        'cate_name' => '西裤',
                        'fabric_m' => '1.5',
                        'lining_m' => '0',
                        'gender' => 10040,
                        'process_fee' => '200',   //加工费
                        'one_fee' => '70', // 买一赠一加工费
                        'process_than' => '400',
                        'process_less' => '300',
                        'fcate' => 2,
                ),
                '0005' => array(
                        'cate_id' => '4000',
                        'cate_name' => '马夹',
                        'fabric_m' => '1',
                        'lining_m' => '0',
                        'gender' => 10040,
                        
                        'process_fee' => '160', // 加工费
                        'one_fee' => '70', // 买一赠一加工费
                        'emb' => 4147, 
                        'process_than' => '250',
                        'process_less' => '200',
                        'fcate' => 5,
                ),
                '0006' => array(
                        'cate_id' => '3000',
                        'cate_name' => '衬衣',
                        'fabric_m' => '1.5',
                        'lining_m' => '0',
                        'gender' => 10040,
                        'process_fee' => '100', // 加工费
                        'one_fee' => '51', // 买一赠一加工费
                        'emb' => 3246,
                        'process_than' => '160',
                        'process_less' => '130',
                        'fcate' => 3,
                ),
                '0007' => array(
                        'cate_id' => '6000',
                        'cate_name' => '大衣',
                        'fabric_m' => '2.5',
                        'lining_m' => '2',
                        'craft' => array(
                                'id' => '6409',
                                'name' => '工艺类别'
                        ),
                        'gender' => 10040,
                        'process_fee' => '380', // 加工费
                        'one_fee' => '270', // 买一赠一加工费
                        'emb' => 6395,
                        'process_than' => '1000',
                        'process_less' => '800',
                        'fcate' => 4,
                ),
                '0017' => array(
                        'cate_id' => '15000',
                        'cate_name' => '男短裤',
                        'fabric_m' => '0.85',
                        // 'lining_m' => '2',
                        'gender' => 10040,
                        'process_fee' => '145', // 加工费
                        'one_fee' => '70',
                        'fcate' => '',
                ),
                '0018' => array(
                    'cate_id' => '18000',
                    'cate_name' => '立领西服',
                    'fabric_m' => '2',//'1.75',//'2',
                    'lining_m' => '2',
                    'gender' => 10040,
                    'process_fee' => '380',
                    'one_fee' => '230',
                    'emb' => 417,
                    'process_than' => '780',
                    'process_less' => '580',
                    'fcate' => 7,
                ),
                         
                '0011' => array(
                        'cate_id' => '95000',
                        'cate_name' => '女西服',
                        'fabric_m' => '2', 
                        'lining_m' => '2',
                        'gender' => 10041,
                        'process_fee' => '380',
                        'one_fee' => '230',
                        'process_than' => '780',
                        'process_less' => '580',
                        'fcate' => 1,
                ),
                '0012' => array(
                        'cate_id' => '98000',
                        'cate_name' => '女西裤',
                        'fabric_m' => '1.5',
                        'lining_m' => '0',
                        'gender' => 10041,
                        'process_fee' => '200',
                        'one_fee' => '70',
                        'process_than' => '400',
                        'process_less' => '300',
                        'fcate' => 2,
                ),
                '0016' => array(
                        'cate_id' => '11000',
                        'cate_name' => '女衬衣',
                        'fabric_m' => '1.5', 
                        'lining_m' => '0',
                        'gender' => 10041,
                        'process_fee' => '100', // 加工费
                        'one_fee' => '51',
                        'process_than' => '160',
                        'process_less' => '130',
                        'fcate' => 3,
                ),
                '0021' => array(
                        'cate_id' => '103000',
                        'cate_name' => '女大衣',
                        'fabric_m' => '2.5',
                        'lining_m' => '2',
                        'gender' => 10041,
                        'process_fee' => '380',
                        'one_fee' => '270',
                        'process_than' => '1000',
                        'process_less' => '800',
                        'fcate' => 4,
                ),
                '0037' => array(
                        'cate_id' => '222000',
                        'cate_name' => '女马夹',
                        'fabric_m' => '1',
                        'lining_m' => '0',
                        'gender' => 10041,
                        'process_fee' => '160',
                        'one_fee' => '70',
                        'emb' => 4147,
                        'process_than' => '250',
                        'process_less' => '200',
                        'fcate' => 5,
                ),
                '0032' => array(
                        'cate_id' => '13000',
                        'cate_name' => '女西裙',
                        'fabric_m' => '1.5',
                        'lining_m' => '0',
                        'gender' => 10041,
                        'process_fee' => '100', // 加工费
                        'one_fee' => '51',
                        'process_than' => '150',
                        'process_less' => '100',
                        'fcate' => 6,
                ),
        );
         
    }
    
}
