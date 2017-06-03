<?php
use Cyteam\Message\ExpressMessage;
use Cyteam\Shop\Type\FdiyBase;
use Cyteam\Shop\Type\Types;
use Cyteam\Goods\Orders;
use Cyteam\Comment\Comment;
/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class BannerimgApp extends BackendApp
{


    function __construct(){
        $mes_status_list = array(
          0=>"尚未推送",
          1=>"推送成功",
          2=>"推送失败",
        );
        $this->mes_status_list = $mes_status_list;
        $this->OrderApp();
    }
    function OrderApp(){
        parent::__construct();
    }
 /**
     * 标签审单功能
     */
    function index()
    {
        $ordergoods_mob = m('ordergoods');
		$page   =   $this->_get_page(30);    //获取分页信息
		
        $ordergoodslist=$ordergoods_mob->find(array(
            'conditions'=>" type='fdiy' AND style != '' ",
			 'limit'         => $page['limit'],  //获取当前页的数据
            'order'     =>"rec_id desc",
        ));
        if($ordergoodslist){
            foreach($ordergoodslist as $key=>$val){
                $ordergoodslist[$key]['style']=SITE_URL.$val['style'];
            }
        }
		$ordercount=$ordergoods_mob->find(array(
            'conditions'=>" type='fdiy' AND style != '' ",
			
        ));
         $page['item_count'] = count($ordercount);   //获取统计的数据
		
		 $this->_format_page($page);
		
		  $this->assign('page_info', $page); 
        $this->assign('ordergoodslist',$ordergoodslist);
        $this->display('order.banimg.html');
    }


	    /**
     * 标签审单查看
     */
    public function view_img()
    {
		$order_goods_mod = m('ordergoods');
		if(IS_POST){
			
			$id=$_POST['id']?$_POST['id']:'';
			$status=$_POST['status']?$_POST['status']:'0';
			$dog_name=$_POST['dog_name']?trim($_POST['dog_name']):'';//狗狗名
			$dog_desc=$_POST['dog_desc']?trim($_POST['dog_desc']):'';//寄语
			$dog_date=$_POST['dog_date']?trim($_POST['dog_date']):'';//生日
			$style=$_POST['style']?$_POST['style']:'';//图片
				
					$order_goods_info = $order_goods_mod->get_info($id);
					$order_goods_data=array(
					      'status'=>$status,
						  'dog_name'=>$dog_name,
						  'dog_desc'=>$dog_desc,
						  'dog_date'=>$dog_date,
						  'style'=>$style,
					);
			        $order_goods_mod->edit("rec_id={$id}",$order_goods_data);
			        $this->show_message("审核修改成功！", '返回审核列表', 'index.php?app=bannerimg');
			      			
		}else{
			$order_id = intval($_GET['order_id']);
				$ordergoodslist=$order_goods_mod->find(array(
					  'conditions'=>" order_id = $order_id",
					 
				));
				if($ordergoodslist){
					foreach($ordergoodslist as $key=>$val){
						$ordergoodslist[$key]['style']=SITE_URL.$val['style'];
					}
				}
			
		    $this->assign('data',$ordergoodslist);
		    $this->display('order.viewimg.html');
		}
       
    }

   
}

