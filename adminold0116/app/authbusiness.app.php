<?php
/**
 *个人认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package authperson.app.php
*/
class AuthBusinessApp extends BackendApp
{
	var $_mod;
	var $_auth_web_url;
	var $_status;
	var $_is_long;
	var $_job;
	function __construct()
	{
		define("DIR", "business/");
		$this->_auth_web_url = SITE_URL."/upload/auth/";
		$this->_mod = & m('businessauth');
		$this->_status = array(1=>'审核成功',2=>'审核失败');
		$this->_is_long = array(0=>'否',1=>'是');
		$this->assign('is_long',$this->_is_long);
		$this->assign('status',$this->_status);
		
		$job = include_once ROOT_PATH.'/includes/arrayfiles/job.arrayfile.php';
		$this->_job = $job;
		
		$this->PersonApp();
	}
	function PersonApp()
	{
		parent::BackendApp();
	}
	
	/**
	* 认证列表
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-28
	*/
	function index() 
	{
		$_GET['status'] = isset($_GET['status']) ?  $_GET['status'] : 0;
		
		$conditions = $this->_get_query_conditions(array(
				array(
						'field' => 'firm_name',
						'equal' => 'like',
				),
				array(
						'field' => 'status',
						'equal' => '=',
				),
				
		));
		if (isset($_GET['sort']) && isset($_GET['order'])) {
			$sort = strtolower(trim($_GET['sort']));
			$order = strtolower(trim($_GET['order']));
			if (!in_array($order, array('asc', 'desc'))) {
				$sort = 'id';
				$order = 'desc';
			}
		} else {
			$sort = 'id';
			$order = 'desc';
		}
		$page = $this->_get_page(30);
		
		$person_list = $this->_mod->find(array(
			'conditions'	=> "1=1 ".$conditions,
			'fields'		=> "*,CONCAT('$this->_auth_web_url',legal_card_face_img) as legal_card_face_img,CONCAT('$this->_auth_web_url',org_img) as org_img,CONCAT('$this->_auth_web_url',business_img) as business_img,CONCAT('$this->_auth_web_url',business_seal_img) as business_seal_img,CONCAT('$this->_auth_web_url',legal_card_back_img) as legal_card_back_img",
			'count'			=> true,
			'limit' => $page['limit'],
		));
		
		$page['item_count'] = $this->_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info', $page);
		
		$this->assign("list",$person_list);
		$this->display(DIR."index.html");
	}
	
	/**
	* 审核
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-29
	*/
	function edit() 
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
		$info = $this->_mod->get(array(
				'conditions'	=> 	"id=".$id,
				'join'			=> "belongs_to_user",
				'fields'		=> "business_auth.*,CONCAT('$this->_auth_web_url',legal_card_face_img) as legal_card_face_img,CONCAT('$this->_auth_web_url',org_img) as org_img,CONCAT('$this->_auth_web_url',business_img) as business_img,CONCAT('$this->_auth_web_url',business_seal_img) as business_seal_img,CONCAT('$this->_auth_web_url',legal_card_back_img) as legal_card_back_img",
		));
		
		if(!IS_POST)
		{
			$region_mod = m('region');
			$region_ids = explode("　", $info['region_id']);
			$region_list = $region_mod->get_options(2);
			$this->assign('region_parent_list',$region_list);
			$info['region_parent_id'] = $region_ids[0];
			$info['region_son_list'] = $region_mod->get_options($info['region_parent_id']);
			$info['region_son_id']	   = $region_ids[1];
			
			//===== 如果此时状态为0未审核 那么应该修改为3 已受理 =====
			if ($info['is_accept'] == 0)
			{
				$this->_mod->edit($id,array('is_accept'=>1));
			}
			
			$this->assign('data',$info);
			$this->display(DIR."edit.html");
		}
		else
		{
			
			$region_mod = m('region');
			$data['firm_name'] = $_POST['firm_name'];
			$data['licence_num'] = $_POST['licence_num'];
			$region_prent_id  = $_POST['region_parent_id'];
			$region_son_id = $_POST['region_son_id'];
			$region_parent_name = $region_mod->getRegionName($region_prent_id);
			$region_son_name = $region_mod->getRegionName($region_son_id);
			$data['region_id'] = $region_prent_id.'　'.$region_son_id;
			$data['region_name'] = $region_parent_name.'　'.$region_son_name;
			$data['common_address'] = $_POST['common_address'];
			$data['business_life'] = !empty($_POST['business_life']) ? strtotime($_POST['business_life']) : '';
			$data['is_long']     = !empty($_POST['is_long']) ? $_POST['is_long'] : 0;
			$data['business_scope'] = $_POST['business_scope'];
			$data['org_code']	= $_POST['org_code'];
			$data['link_mob']	= $_POST['link_mob'];
			$data['tax_code']	= $_POST['tax_code'];
			$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
			$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
			$card = $_POST['legal_card'];
			if (!preg_match($preg18, $card) && !preg_match($preg15, $card))
			{
				$this->show_warning('请输入正确的身份证件号码');
				return;
			}
			
			$data['legal_real_name'] = $_POST['legal_real_name'];
			$data['legal_card'] = $card;
			$data['legal_card_due'] =  !empty($_POST['legal_card_due']) ? strtotime($_POST['legal_card_due']) : 0;
			$data['legal_card_long'] = !empty($_POST['legal_card_long']) ? 1 : 0;
			
			$data['status'] = $_POST['status'];
			$data['fail_reason'] = isset($_POST['fail_reason']) ? $_POST['fail_reason'] : '';
			/* if ($stauts == 2 && !$fail_reason)
			{
				$this->show_warning('请填写失败原因');
				return ;
			} */
			
			
			if ($this->_mod->edit($id,$data) !== false)
			{
				$member_mod = m('member');
				$member_mod->edit($info['user_id'],array('is_ident'=>2));
				
				$this->show_message('操作成功!',
						'back_list', 'index.php?app=authbusiness&act=index'
				);
			}
			
		}
		
	}
	
	
	/**
	* 删除审核记录      后期要屏蔽这个方法 危险操作
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-29
	*/
	function drop() 
	{
		$id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id) {
            $this->show_warning('请选择要删除的数据!');
            return;
        }
        
        //===== 删除对应的审核上传的图片 =====
        #TDDO
        
        
        $this->_mod->drop(db_create_in($id, 'id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=authbusiness&act=index&page=' . $ret_page);
	}
	
	
	
	
	
	
	
	
	
	
	
	
}




































