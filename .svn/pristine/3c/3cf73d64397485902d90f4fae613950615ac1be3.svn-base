<?php


class DebitlinesModel extends BaseModel
{
    var $table  = 'debit_line_s';
    var $prikey = 'id';
    var $_name  = 'debitlines';
    
    var $_relation = array(
            
    );
    
    /**
    *获得最新的d_sn数字
    *@author liang.li <1184820705@qq.com>
    *@2015年10月8日
    */
    function dSnNew($d_sn_arr) 
    {
        $new_item = $this->get(array(
            'conditions' => "1=1",
            'order'  => "id DESC",
        ));
//   print_exit($new_item);
// print_exit($new_sn);
        $new_sn = $new_item['d_sn'];
        $f_sn = 2000000000;
        if ($d_sn_arr) 
        {
            $new_sn = array_pop($d_sn_arr);
            $f_sn = substr($new_sn, 1) + 1;
        }
        elseif($new_item && substr($new_sn, 1,1) == 2) 
        {
       
// print_exit($new_item);
            //$str_long = strlen($new_sn);
            //$n_s = $str_long - 6;//=====  获得从10000开始每次加1现在的数字位数  =====
            $f_sn = substr($new_sn, 1) + 1;
        }
          
        return $f_sn;
    }
    
    
    /**
     * 
     */
    function sn($d_sn_arr){
        $f_sn = $this->dSnNew($d_sn_arr);
        //$rand_n = createNonceNum(5);
        $d_sn = "C".$f_sn;//=====  生成随机数  =====
        
        //$sn =$cates[$cate]['sign'].$type.createNonceStr(10);
//     echo $d_sn;exit;
        $info = $this->get("d_sn='{$d_sn}'");
        //$sn是不是唯一，不是重新生成
        if(!empty($info))
        {
            $d_sn = $this->sn($d_sn_arr);
        }
        
//   print_exit($info);
        return $d_sn;
    }
    
    /**
    *激活码
    *@author liang.li <1184820705@qq.com>
    *@2015年10月8日
    */
    function activ_sn() 
    {
        
        $activ_sn = "QC".createNonceStr(10);        
        $info = $this->get("active_sn='{$activ_sn}'");
        //$sn是不是唯一，不是重新生成
        if(!empty($info))
        {
            $activ_sn =$this->activ_sn();
        }
        return $activ_sn;
    }
    
    
    
    
    
    
    
    
    
}

?>