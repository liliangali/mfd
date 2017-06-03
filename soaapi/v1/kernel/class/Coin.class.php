<?php
class Coin extends Result
{

     function index($data) 
     {
         $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
         $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
         if($pageIndex < 1) $pageIndex = 1;
         if($pageSize < 1) $pageSize = 1;
         $limit = $pageSize*($pageIndex-1).','.$pageSize;
         
         $coin_mod = m('cotcoin');
         $coinArr = $coin_mod->find(array(
             'conditions' =>"is_sale =1",
             'order'      => "add_time DESC",
             'limit'      => $limit,
         ));
         
         $aData = array();
         foreach((array)$coinArr as $key => $val){
             $aData[] = array(
                 'id'          => $val["id"],
                 "facevalue"   => $val["facevalue"],
                 "price"       => $val["price"],
                 "integral"    => $val["integral"]
             );
         }
         
         $this->result = $aData;
         return $this->sresult();
     }

     function notice(){
         $this->result= array("notice" => '麦富迪财富快车，享折扣绿色通道：创业者可以一次性购买不同面值的麦富迪币实现更高级别的晋升，并享受相应平台售价的折扣，具体性详情，请联系平台拓展经理。');
         return $this->sresult();
     }
}

