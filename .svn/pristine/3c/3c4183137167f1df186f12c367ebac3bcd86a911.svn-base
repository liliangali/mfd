<?php

class AskApp extends MallbaseApp{

    var $_mod;
	function __construct()
	{
	    $this->_mod = m('ask');
	    $this->_v2_mod=m('askv2');
		parent::__construct();
	}
	
	
	/**
	 * <select validate="selecta" name="region_son_id" id="region_son_id">
                          <option value="">--请选择城市--</option>
                          {html_options options=$data.region_son_list selected=$data.region_son_id}
                        </select>
                        
                        
                        $.ajax({
					   type: "POST",
					   url: url,
					   data: "goods_id="+goods_id+"&measure=1&quantity="+goods_num+"&ticket="+ticket+"&btype="+btype,
					   success: function(msg)
					   {
							 msg = strToJson(msg);
						     if(msg.done == true)
					    	 {
						    	 window.location.href = tourl ;
					    	 }
						     else
					    	 {
						    	 _layer(msg.msg, 8);
					    	 }
					   }
					});
	 */
	
	/**
	*content
	*@author liang.li <1184820705@qq.com>
	*@2015年4月29日
	*/
	function index() 
	{
	    $region_mod = m('region');
	    $list1 = $region_mod->get_options(2);
	    $this->assign('region1',$list1);
	    $list2 = $region_mod->get_options(246);
	    $this->assign('region2',$list2);
	    
	    if (!IS_POST) 
	    {
	        $ask = $this->_mod->getAsk();
// print_exit($ask);
	        
	        $id = isset($_GET['id']) ? $_GET['id'] : 0;
	        if ($id) 
	        {
	           $info = $this->_mod->get($id);
	           if ($info['fabrci']) 
	           {
	               $info['fabrci'] = explode(',', $info['fabric']);
	           }
	           if ($info['style'])
	           {
	               $info['style'] = explode(',', $info['style']);
	           }
	           if ($info['color'])
	           {
	               $info['color'] = explode(',', $info['color']);
	           }
	           
	        }
	        if ($_SESSION['ident']) 
	        {
	            $info['ident'] = $_SESSION['ident'];
	        }
	        $this->assign('info',$info);
	        $this->assign('ask',$ask);
	       $this->display("ask/index.html");
	    }
	    else 
	    {
// print_exit($_POST);
	        $post = $_POST;
	        if ($post['fabric']) 
	        {
	            $post['fabric'] = implode(',', $post['fabric']);
	        }
	        if ($post['style'])
	        {
	            $post['style'] = implode(',', $post['style']);
	        }
	        if ($post['color'])
	        {
	            $post['color'] = implode(',', $post['color']);
	        }
	        
	        if (!$post['ident'] || !$post['name']) 
	        {
	             $this->display('ask/error.html');
	             return;
	        }
	        
	        if (!$post['phone'] && !$post['weixin'])
	        {
	            $this->display('ask/error.html');
	            return;
	        }
	        
	        $_SESSION['ident'] = $post['ident'];
	        
	        
	        $post['add_time'] = time();
	        if ($this->_mod->add($post)) 
	        {
	            
	            $this->display('ask/msg.html');
	            return;
	        }
	        
	        
	        $this->display('ask/error.html');
	        return;
	    }
	}
	
	/**
	*ajax获得三级联动
	*@author liang.li <1184820705@qq.com>
	*@2015年4月29日
	*/
	function get_region() 
	{
	    $region_mod = m('region');
	    $pid = $_POST['pid'];
	    if (!$pid) 
	    {
	        $this->json_error('失败');
	    }
	    
	    $list = $region_mod->get_options_html($pid,0);
	    $this->json_result($list);
	    
	}
	
	/**
	*testdata
	*@author liang.li <1184820705@qq.com>
	*@2015年4月30日
	*/
	function testdate() 
	{
	    echo date('Y-m-d H:i:s',1430362342);
	}
	/**
	 *content
	 *@author zhaoxr <773938880@qq.com>
	 *@2015年4月29日
	 */
	function v2(){
		if(!IS_POST){
			$id=isset($_GET['id'])?$_GET['id']:0;
			if($id){
				$info=$this->_v2_mod->get($id);
			}
			if($_SESSION['consultant']){
				$info['consultant']=$_SESSION['consultant'];
			}
			$this->assign('info',$info);
			$this->display('ask/index_v2.html');
		}else{
			$data=$_POST;
// 			var_dump($data['phone']);exit();
			if(!$data){
				$this->display('ask/error_v2.html');
				return ;
			}
			$_SESSION['consultant']=$data['consultant'];
			$data['add_time']=time();
// 			print_r($data);die();
			if($this->_v2_mod->add($data)){
				$this->display('ask/msg_v2.html');
				return;
			}
			$this->display('ask/error_v2.html');
			return ;
		}
	}
	
}
?>