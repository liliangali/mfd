<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<title>搜索</title>
		<script type="text/javascript" src="static/js/jquery-1.7.2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="public/static/wap/css/public.css" />
		<link rel="stylesheet" type="text/css" href="public/static/wap/css/products.css" />
	</head>
	<body>

		<div class="ss_section">
			<div class="ss_select">
				<div class="ss_slt1 c_db c_bac c_bpc">
					<input type="text" name="search" placeholder="请输入产品关键字" />
					<div class="ss_slt1_1 clearfix searchsub">
						<img src="public/static/wap/images/select_1.png" />
					</div>
				</div>
			</div>

			<div class="ss_rmss">
				<div class="goods_title c_bs c_db c_bac c_bpj">
					<a href="" class="gt_left">热门推荐</a>
				</div>
				<div class="ss_tj">
				<?php $_from = $this->_var['keywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
					<a href="<?php echo $this->build_url(array('app'=>'product','act'=>'index')); ?>?search=<?php echo $this->_var['item']; ?>" class="ss_tj_1 act"><?php echo $this->_var['item']; ?></a>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

				</div>
			</div>
		</div>

	</body>
<script>
	$(".searchsub").click(function () {
		var search = $("input[name=search]").val();
		if (!search)
		{
			return ;
		}
		window.location.href = "<?php echo $this->build_url(array('app'=>'product','act'=>'index')); ?>?search="+search;
	})
</script>

</html>