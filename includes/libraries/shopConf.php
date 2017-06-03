<?php
/**
 * 购物公共配置类
 * 
 * @author ruesin
 */
// require ( "shopBase.php" );
class ShopConf extends Object//ShopBase
{
    public static $measureWays  = array(/*'1'=>'预约上门量体',*/'5'=>'现有量体数据','2'=>'去附近门店量体','6'=>'指定量体师',/* '3'=>'现有量体数据' *//*,'4'=>'标准尺码'*/);
    public static $cartTypes    = array("diy",'suit','diys','diy2');
    public static $orderTypes   = array("news");
    public static $shippingWays = array(1=>array('code'=>'address','name'=>'收货地址','id'=>1),2=>array('code'=>'store','name'=>'门店自提','id'=>2));
    public static $invoiceTypes = array(1=>'普通发票',3=>'增值税专用',2=>'增值税普通');
    public static $deliveryTime = array(1=>'不限送货时间',2=>'工作日送货',3=>'双休日、假日送货');
    
    public static $invoiceFields = array(
        'com' => '单位名称', 
        'sn' => '识别码',
        'addr' => '注册地址',
        'tel' => '注册电话',
        'bank' => '开户银行',
        'bank_num' => '银行账户',
    );
    
    
    function __construct()
    {
        $this->ShopConf();
    }
    function ShopConf()
    {
    }
    
    //面料DIY 品类对应
    public static $fDiyCloth = [
        2 => '0001',
        3 => '0003',
        4 => '0004',
        130 => '0003', //套装西服
        131 => '0004', //套装西裤
        355 => '0018', //立领西服
        340 => '0006',//衬衣
        346 => '0005',//马甲
        347 => '0007',//大衣
    ];
    
    /**
     * 所有品类的基数
     *
     * @date 2015年12月7日 下午2:12:39
     *
     * @author Ruesin
     */
    public static function _customs ($cloth = '')
    {
        
        imports("diys.lib");
        $oLogs = new Diys();
        $res = $oLogs->_customs();
        return ($cloth && isset($res[$cloth])) ? $res[$cloth] : $res;
        
        $res = array(
                '0003' => array(
                        'cate_id' => '3',
                        'cate_name' => '西服',
                        //'fabric_m' => '1.8', // 面料单号基数
                        'fabric_m' => '1.75', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '2', // 里料单号基数
                        'craft' => array(
                                'id' => '435',
                                'name' => '工艺类型',
                                'son' => array(
                                        '1230' => '手工艺',
                                        '431' => '衬类型'
                                )
                        ),
                        'gender' => 10040,
                        //'process_fee' => '275',   //加工费
                        'process_fee' => '380',   //加工费 2015-10-08 10.71版本
                        'one_fee' => '230',       //买一赠一加工费
                ),
                '0004' => array(
                        'cate_id' => '2000',
                        'cate_name' => '西裤',
                        //'fabric_m' => '1.2',
                        'fabric_m' => '1.15', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '0',
                        'craft' => array(
                                'id' => '2224',
                                'name' => '工艺选择'
                        ),
                        'gender' => 10040,
                        //'process_fee' => '145',   //加工费
                        'process_fee' => '200',   //加工费
                        'one_fee' => '70',       //买一赠一加工费
                ),
                '0005' => array(
                        'cate_id' => '4000',
                        'cate_name' => '马夹',
                        //'fabric_m' => '0.78',
                        'fabric_m' => '0.7', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '0',
                        'gender' => 10040,
                         
                        'process_fee' => '160',   //加工费
                        'one_fee' => '70',       //买一赠一加工费
                ),
                '0006' => array(
                        'cate_id' => '3000',
                        'cate_name' => '衬衣',
                        //'fabric_m' => '1.6',
                        'fabric_m' => '1.49', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '0',
                        'gender' => 10040,
                         
                        'process_fee' => '100',   //加工费
                        'one_fee' => '51',       //买一赠一加工费
                ),
                '0007' => array(
                        'cate_id' => '6000',
                        'cate_name' => '大衣',
                        //'fabric_m' => '2.58',
                        'fabric_m' => '2.3', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '2',
                        'craft' => array(
                                'id' => '6409',
                                'name' => '工艺类别'
                        ),
                        'gender' => 10040,
                        'process_fee' => '380',   //加工费
                        'one_fee' => '270',       //买一赠一加工费
                ),
                '0017' => array(
                        'cate_id' => '15000',
                        'cate_name' => '男短裤',
                        'fabric_m' => '0.85',
                        //'lining_m' => '2',
                        'gender' => 10040,
                        'process_fee' => '145',   //加工费
                        'one_fee' => '70',       //买一赠一加工费
                ),
                '0011' => array(
                        'cate_id' => '95000',
                        'cate_name' => '女西服',
                        //'fabric_m' => '1.8',
                        'fabric_m' => '1.75', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '2',
                        'gender' => 10041,
                         
                        'process_fee' => '380',   //加工费
                        'one_fee' => '230',       //买一赠一加工费
                ),
                '0012' => array(
                        'cate_id' => '98000',
                        'cate_name' => '女西裤',
                        //'fabric_m' => '1.2',
                        'fabric_m' => '1.15', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '0',
                        'gender' => 10041,
                         
                        'process_fee' => '200',   //加工费
                        'one_fee' => '70',       //买一赠一加工费
                ),
                '0016' => array(
                        'cate_id' => '11000',
                        'cate_name' => '女衬衣',
                        //'fabric_m' => '1.6',
                        'fabric_m' => '1.49', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '0',
                        'gender' => 10041,
                         
                        'process_fee' => '100',   //加工费
                        'one_fee' => '51',       //买一赠一加工费
                ),
                '0021' => array(
                        'cate_id' => '103000',
                        'cate_name' => '女大衣',
                        //'fabric_m' => '2.58',
                        'fabric_m' => '2.3', // 面料单号基数 2015-10-08 10.71版本
                        'lining_m' => '2',
                        'gender' => 10041,
                         
                        'process_fee' => '380',   //加工费
                        'one_fee' => '270',       //买一赠一加工费
                ),
                 
        );
        
        return ($cloth && isset($res[$cloth])) ? $res[$cloth] : $res;
    }
    
    /**
     * 通过接口获取量体项
     *
     * @date 2015年12月7日 下午2:41:05
     *
     * @author Ruesin
     */
    public static function figureFields(){
        $cloudData = file_get_contents('http://api.figure.mfd.cn/soap/club.php?act=getFields');
        //$cloudData = file_get_contents('http://api.figure.dev.mfd.cn:8080/soap/club.php?act=getFields');
    
        if (!$cloudData) return [];
    
        $fData = json_decode($cloudData, 1);
        if (!$fData || !isset($fData['result']['data'])) {
            return [];
        }
        return $fData['result']['data'];
    }
    
    
    
}
