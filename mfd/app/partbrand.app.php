<?php
	class PartBrandApp extends BackendApp
	{
		var $_partbrand_mod;
		
		function __construct()
		{
			$this->PartBrandApp();
		}
		function PartBrandApp()
		{
			parent::BackendApp();
		
			$this->_partbrand_mod =& m('partbrand');
		}
		
		//品牌列表
		function index() {
			$page = $this->_get_page(10);
			$brands = $this->_partbrand_mod->find(array(
						"conditions" => '1=1',
						"limit"    => $page['limit'],
						'order'       => 'sort_order desc',
						'count'      => true
					));
// 	dump($brands);exit;
			foreach ($brands as $key => $brand)
			{
				$brand['brand_logo']&&$brands[$key]['brand_logo'] = dirname(site_url()) . '/' . $brand['brand_logo'];
			}
			$page['item_count']=$this->_partbrand_mod->getCount();
			/* 导入jQuery的表单验证插件 */
			$this->import_resource(array(
					'script' => 'jqtreetable.js,inline_edit_admin.js',
					'style'  => 'res:style/jqtreetable.css'
			));
			$this->_format_page($page);
			$this->assign("brands",$brands);
			$this->assign("page_info",$page);
			$this->display("partbrand.index.html");
		}
		
		/**
		 *    新增组件品牌
		 *
		 *    @author    liliang
		 *    @return    void
		 */
		function add()
		{
			if (!IS_POST)
			{
				/* 显示新增表单 */
				$brand = array(
						'sort_order' => 255,
						'if_show' => 1,
				);
				$yes_or_no = array(
						1 => Lang::get('yes'),
						0 => Lang::get('no'),
				);
				$this->import_resource(array(
						'script' => 'jquery.plugins/jquery.validate.js'
				));
				$this->assign('yes_or_no', $yes_or_no);
				$this->assign('brand', $brand);
				$this->display('partbrand.form.html');
			}
			else
			{
				$data = array();
				$data['brand_name']     = $_POST['brand_name'];
				$data['sort_order']     = $_POST['sort_order'];
				$data['if_show']    = $_POST['if_show'];
		
				/* 检查名称是否已存在 */
				if (!$this->_partbrand_mod->unique(trim($data['brand_name'])))
				{
					$this->show_warning('name_exist');
					return;
				}
				if (!$brand_id = $this->_partbrand_mod->add($data))  //获取brand_id
				{
					$this->show_warning($this->_partbrand_mod->get_error());
		
					return;
				}
		
				/* 处理上传的图片 */
				$logo       =   $this->_upload_logo($brand_id);
				if ($logo === false)
				{
					return;
				}
				$logo && $this->_partbrand_mod->edit($brand_id, array('brand_logo' => $logo)); //将logo地址记下
		
				$this->show_message('add_brand_successed',
						'back_list',    'index.php?app=partbrand',
						'continue_add', 'index.php?app=partbrand&amp;act=add'
				);
			}
		}
		
		/**
		 *    编辑商品品牌
		 *
		 *    @author    Hyber
		 *    @return    void
		 */
		function edit()
		{
			$brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			if (!$brand_id)
			{
				$this->show_warning('no_such_brand');
				return;
			}
			if (!IS_POST)
			{
				$find_data     = $this->_partbrand_mod->find($brand_id);
				if (empty($find_data))
				{
					$this->show_warning('no_such_brand');
		
					return;
				}
				$brand    =   current($find_data);
				if ($brand['brand_logo'])
				{
					$brand['brand_logo']  =   dirname(site_url()) . "/" . $brand['brand_logo'];
				}
				/* 显示新增表单 */
				$yes_or_no = array(
						1 => Lang::get('yes'),
						0 => Lang::get('no'),
				);
				$this->import_resource(array(
						'script' => 'jquery.plugins/jquery.validate.js'
				));
				$this->assign('yes_or_no', $yes_or_no);
				$this->assign('brand', $brand);
				$this->display('partbrand.form.html');
			}
			else
			{
				$data = array();
				$data['brand_name']     =   $_POST['brand_name'];
				$data['sort_order']     =   $_POST['sort_order'];
				$data['if_show']    =   $_POST['if_show'];
				$logo               =   $this->_upload_logo($brand_id);
				$logo && $data['brand_logo'] = $logo;
				if ($logo === false)
				{
					return;
				}
				/* 检查名称是否已存在 */
				if (!$this->_partbrand_mod->unique(trim($data['brand_name']), $brand_id))
				{
					$this->show_warning('name_exist');
					return;
				}
				$rows=$this->_partbrand_mod->edit($brand_id, $data);
				if ($this->_partbrand_mod->has_error())
				{
					$this->show_warning($this->_partbrand_mod->get_error());
		
					return;
				}
		
				$this->show_message('edit_brand_successed',
						'back_list',        'index.php?app=partbrand',
						'edit_again',    'index.php?app=partbrand&amp;act=edit&amp;id=' . $brand_id);
			}
		}
		
		/**
		 * 删除品牌
		 * @author liliang
		 * @return bool
		 */
		function drop()
		{
			$brand_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
			if (!$brand_ids)
			{
				$this->show_warning('no_such_brand');
		
				return;
			}
			$brand_ids=explode(',',$brand_ids);
			$this->_partbrand_mod->drop($brand_ids);
			if ($this->_partbrand_mod->has_error())    //删除
			{
				$this->show_warning($this->_partbrand_mod->get_error());
		
				return;
			}
		
			$this->show_message('drop_brand_successed');
		}
		
		/* 检查品牌唯一 */
		function check_brand ()
		{
			$brand_name = empty($_GET['brand_name']) ? '' : trim($_GET['brand_name']);
			$brand_id   = empty($_GET['id']) ? 0 : intval($_GET['id']);
			if (!$brand_name) {
				echo ecm_json_encode(false);
			}
			if ($this->_partbrand_mod->unique($brand_name, $brand_id)) {
				echo ecm_json_encode(true);
			}
			else
			{
				echo ecm_json_encode(false);
			}
			return ;
		}
		
		//异步修改数据
		function ajax_col()
		{
			$id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
			$column = empty($_GET['column']) ? '' : trim($_GET['column']);
			$value  = isset($_GET['value']) ? trim($_GET['value']) : '';
			$data   = array();
		
			if (in_array($column ,array('brand_name','sort_order','if_show')))
			{
				$data[$column] = $value;
				if($column == 'brand_name')
				{
					$brand = $this->_partbrand_mod->get_info($id);
		
					if(!$this->_partbrand_mod->unique($value, $id))
					{
						echo ecm_json_encode(false);
						return ;
					}
				}
				$this->_partbrand_mod->edit($id, $data);
				if(!$this->_partbrand_mod->has_error())
				{
					echo ecm_json_encode(true);
				}
			}
			else
			{
				return ;
			}
			return ;
		}
		
		/**
		 *    处理上传标志
		 *
		 *    @author    Hyber
		 *    @param     int $brand_id
		 *    @return    string
		 */
		function _upload_logo($brand_id)
		{
			$file = $_FILES['logo'];
			if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
			{
				return '';
			}
			import('uploader.lib');             //导入上传类
			$uploader = new Uploader();
			$uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
			$uploader->addFile($_FILES['logo']);//上传logo
			if (!$uploader->file_info())
			{
				$this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=partbrand&amp;act=edit&amp;id=' . $brand_id);
				return false;
			}
			/* 指定保存位置的根目录 */
			$uploader->root_dir(ROOT_PATH);
		
			/* 上传 */
			if ($file_path = $uploader->save('data/files/mall/partbrand', $brand_id))   //保存到指定目录，并以指定文件名$brand_id存储
			{
				return $file_path;
			}
			else
			{
				return false;
			}
		}

		
	}