<?php
class DiyApp extends MallbaseApp
{
	function __construct(){
		parent::__construct();
		header("Content-Type:text/html;charset=" . CHARSET);
	}
function index(){
		$custome_cate_arr = array();
		$user_info = $this->visitor->get();
		if($user_info['user_id']){
			do{
				$api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
			} while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
		}else{
		    $api_token = 'mfd';
		}
		
		$diy_cate = array(
				'0001' => array('套装'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress'),'img'=>"static/images/pic1.png"),
				'0003' => array('西服'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress'),'img'=>"static/images/pic2.png"),
				'0004' => array('西裤'=>array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress'),'img'=>"static/images/pic3.png"),
				'0005' => array('马甲'=> array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress'),'img'=>"static/images/pic4.png"),
				'0006' => array('衬衣'=>array('正装'=>'bformal','休闲'=>'bcasual','礼服'=>'bdress'),'img'=>"static/images/pic5.png"),
				'0007' => array('大衣'=>array('正装'=>'bformal','休闲'=>'bcasual'),'img'=>"static/images/pic6.png"),
		);
		$n=0;
		if($diy_cate) {
			foreach($diy_cate as $key=>$cate) {
			   
				foreach($cate as $kk=>$vv){
				    if ($kk == 'img')
				    {
				        continue;
				    }
					$custome_cate_arr[$n] = array(
							'cate_name' => $kk,
							'cate_id'   => $key,
					        'img'       => $cate['img'],
					);
					$m=0;
					foreach($vv as $k=>$v){
						$custome_cate_arr[$n]['diy_url'][$m]['style']=$k;
//						$custome_cate_arr[$n]['diy_url'][$m]['url']=SITE_URL.'custom-diy-'.$key.'-'.$v.'-'.$api_token.'.html';
						$custome_cate_arr[$n]['diy_url'][$m]['url']='/custom-diy2-'.$key.'-'.$v.'-'.$api_token.'.html';
						$m++;
					}
					$n++;
				}
			}
		}
		$this->assign('cuscate',$custome_cate_arr);
		$this->display('getdiycate.html');
	}
}