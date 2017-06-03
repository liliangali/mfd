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
    function dSnNew() 
    {
        $new_item = $this->get(array(
            'conditions' => "1=1",
            'order'  => "id DESC",
        ));
        $f_sn = 10000;
        if ($new_item) 
        {
            $new_sn = $new_item['d_sn'];
            $str_long = strlen($new_sn);
            $n_s = $str_long - 6;//=====  获得从10000开始每次加1现在的数字位数  =====
            $f_sn = substr($new_sn, 1,$n_s) + 1;
        }
        return $f_sn;
    }
    
    
    /**
     * 
     */
    function sn(){
        $f_sn = $this->dSnNew();
        $rand_n = createNonceNum(5);
        $d_sn = "C".$f_sn.$rand_n;//=====  生成随机数  =====
        
        //$sn =$cates[$cate]['sign'].$type.createNonceStr(10);
    
        $info = $this->get("d_sn='{$d_sn}'");
        //$sn是不是唯一，不是重新生成
        if(!empty($info))
        {
            $d_sn =$this->sn();
        }
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