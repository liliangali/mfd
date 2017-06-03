<?php

class OnegiftModel extends BaseModel
{
    var $table  = 'onegift';
    var $prikey = 'one_id';
    var $_name  = 'onegift';
    
    public function algorithms(){
        
        return array(
                '1' => '去低取高',
                '2' => '折中对半',
                '3' => '去高取低',
        );
        
    }
    
    
    public function algoAmount($price = array(),$id = 0){
        $info =  $this->get($id);
        
        if($info['algo'] == '1'){
            
            if(($price[1]-$price[2]) >0 ){
                $amount = $price[1];
                $discount = $price[2];
            }else{
                $amount = $price[2];
                $discount = $price[1];
            }
            
        }elseif ($info['algo'] == '2'){
            
            $amount = $discount = ($price[1]+$price[2])/2; //未抹零
            
        }elseif ($info['algo'] == '3'){
            
            if(($price[1]-$price[2]) <0 ){
                $amount = $price[1];
                $discount = $price[2];
            }else{
                $amount = $price[2];
                $discount = $price[1];
            }
        }
        
        return array('amount'=>$amount,'discount'=>$discount);
    }
    
}
