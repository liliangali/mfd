<?php
namespace Cyteam\Message;
class ExpressMessage{
	private $key='fYgHSpmg2046';
	private $customer='BE3A5D9D03C941D8C2A8B5B7B6D88D8A';
	function __construct(){
	}
	//获取快递信息
	function getExpressInfo($comCode,$num){
		$post_data = array();
		$post_data["customer"] = $this->customer;
		$key= $this->key ;
		$post_data["param"] = '{"com":"'.$comCode.'","num":"'.$num.'"}';
		$url='http://poll.kuaidi100.com/poll/query.do';
		$post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
		$post_data["sign"] = strtoupper($post_data["sign"]);
		$o=""; 
		foreach ($post_data as $k=>$v)
		{
		    $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
		}
		$post_data=substr($o,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		ob_start();  
		$result = curl_exec($ch);
		$data = ob_get_contents() ;  
		ob_end_clean(); 
		$data = str_replace("\&quot;",'"',$data );
		$data = json_decode($data,true);
		curl_close($ch) ;  
		return $data;
	}
	//获取快递公司编码
	function getCompanyCode($expressNum){
        if(!$expressNum){
        	return false;
        }
        $url='http://www.kuaidi100.com/autonumber/auto?num='.$expressNum.'&key='.$this->key;
        $ch = curl_init();

        // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\&quot;",'"',$result );
        $data = json_decode($data,true);
        curl_close($ch) ;
        // var_dump($data);
       	 return $data;
    }
    //获取所需要的快递数据
    function checkExpressInfo($num=0,$comCode=''){
        if(!$num){
            return '快递号不能为空';
        }
        if(!$comCode){
            $comCode=$this->getCompanyCode($num);
            $comCode=$comCode[0]['comCode'];
            if(!$comCode){
                return '查不到快递公司编号';
            }
        }
        $expressInfo=$this->getExpressInfo($comCode,$num);
        if(isset($expressInfo['result']) && !$expressInfo['result']){
            return '查不到快递信息';
        }
        //物流状态
        $state='';
        switch ($expressInfo['state']) {
            case 0:
                $state='在途中';
                break;
            case 1:
                $state='已揽收';
                break;
            case 2:
                $state='疑难';
                break;
            case 3:
                $state='已签收';
                break;
            case 4:
                $state='退签';
                break;
            case 5:
                $state='同城派送中';
                break;
            case 6:
                $state='退回';
                break;
            case 7:
                $state='转单';
                break;
        }
        $expressInfo['state']=$state;
        //物流公司
        $_mod_shipping=m('shipping');
        $express=$_mod_shipping->get("code='{$expressInfo['com']}'");
        if($express){
            $expressInfo['com']=$express['shipping_name'];
        }
        // var_dump($expressInfo);
        return $expressInfo;
    }
}
