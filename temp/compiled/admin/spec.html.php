<!DOCTYPE html>
<html slick-uniqueid="3"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="shortcut icon" href="http://dev.ecos.com/favicon.gif" type="image/gif">

<link href="static/css/spec/style_min.css" rel="stylesheet" media="screen, projection"><link href="static/css/spec/singlepage_min.css" rel="stylesheet" media="screen, projection">
<!-- 引进app的singlepage页面自定义css-@lujy -->
<script src="static/js/spec/loader.js"></script><script src="static/js/spec/moo_min.js"></script><script src="static/js/spec/tools_min.js"></script>

<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/jquery-1.7.2.min.js'; ?>" charset="utf-8"></script>


</head>
<body class="single-page ">

<div class="body" id="body">
  <div style="height: 728px; width: 1536px;" class="container clearfix" id="container">
    <div class="workground" style="width: 1523px; left: 0px;" id="workground">
      <div class="toggler-left flt hide" id="leftToggler">
        <div class="toggler-left-inner">&nbsp;</div>
      </div>
      <div class="content-head" style="_font-size:0;height:0;"></div>
      <div style="height: 728px; width: 1523px;" class="content-main" id="main">
      <script src="static/js/spec/md5.js"></script>


<form action="" method="post" class="specification" id="specification" onsubmit="return validate_form(this)">
  <div class="tt"><strong>商品规格</strong>点击选择当前商品需要的规格。</div>
  <div class="td">
    <div id="update_news" class="clearfix">
      <div class="type-wrap">
 <!--  <ul class="typelist">
          <li class="act" data-spec-type="image" data-spec-id="1">颜色(<i>0</i>)</li>
          <li class="" data-spec-type="text" data-spec-id="3">尺码(<i>0</i>)</li>
          <li class="" data-spec-type="text" data-spec-id="5">净含量(<i>0</i>)</li>
      </ul> -->
</div>
<div class="spec-wrap">
<?php $_from = $this->_var['spec_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['item']['spec_type'] == 'image'): ?>
    <div class="switchable">
    <div class="selectAll"><label for="selectAll1"><?php echo $this->_var['item']['spec_name']; ?></label></div>
    <ul class="speclist" data-spec-name="<?php echo $this->_var['item']['spec_name']; ?>" data-spec-type="image" data-spec-id="<?php echo $this->_var['key']; ?>">
    <?php $_from = $this->_var['item']['val']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'item1');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['item1']):
?>
            <li class="spec-item">
        <input id="s1" title="<?php echo $this->_var['item1']['spec_value']; ?>" name="14639931061" <?php if ($this->_var['item1']['is_check']): ?>checked<?php endif; ?>  value="<?php echo $this->_var['item1']['spec_value_id']; ?>" p-id="<?php echo $this->_var['key']; ?>"  p-name="<?php echo $this->_var['item']['spec_name']; ?>" type="checkbox">
        <label for="s1">
                    <span class="spec-colorbox">
            <input key="spec_image_1" value="c6fc23ebed6d9e27b971e8b06607b2a5" type="hidden">
            <img key="spec_img_1" src="<?php echo $this->_var['item1']['spec_image']; ?>">
          </span>
                    <span class="spec-name"><?php echo $this->_var['item1']['spec_value']; ?></span>
          <input name="spec[1]" id="" class="x-input" value="<?php echo $this->_var['item1']['spec_value']; ?>" disabled="disabled" type="text"></label>
      </li>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>
    
  </div>
  <?php else: ?>
    <div class="switchable" style="">
    <div class="selectAll"><label for="selectAll3"><?php echo $this->_var['item']['spec_name']; ?></label></div>
    <ul class="speclist" data-spec-name="<?php echo $this->_var['item']['spec_name']; ?>" data-spec-type="text"  data-spec-id="<?php echo $this->_var['key']; ?>">
       <?php $_from = $this->_var['item']['val']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key2', 'item2');if (count($_from)):
    foreach ($_from AS $this->_var['key2'] => $this->_var['item2']):
?>
            <li class="spec-item">
        <input id="s27" title="<?php echo $this->_var['item2']['spec_value']; ?>" name="146399310627" <?php if ($this->_var['item2']['is_check']): ?>checked<?php endif; ?> value="<?php echo $this->_var['item2']['spec_value_id']; ?>" p-id="<?php echo $this->_var['key']; ?>" p-name="<?php echo $this->_var['item']['spec_name']; ?>"  type="checkbox">
        <label for="s27">
                    <span class="spec-name"><?php echo $this->_var['item2']['spec_value']; ?></span>
          <input name="spec[27]" id="" class="x-input" value="<?php echo $this->_var['item2']['spec_value']; ?>"  disabled="disabled" type="text"></label>
      </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>     
          </ul>
 
  </div>
  <?php endif; ?>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </div>
    </div>
   <input type="button" class="test11" value="生成所有货品" />
    <div class="action-btns"><button class="btn btn-primary btn-createAllGoods" type="button"><span><span>生成所有货品aa</span></span></button></div>
  <!--   <div style="margin-top:1em;">共<b class="product-number">0</b>件货品</div> -->
    <div class="spec-tree">
      <table class="gridlist" id="goods-table" border="0" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th>规格值</th>
            <th style="width:auto;">货号</th>
            <th style="width:6%;">库存</th>
            <th style="width:6%;">冻结库存</th>
            <th style="width:15%;">销售价</th>
            <th style="width:8%;">市场价</th>
            <th style="width:8%;">重量(g)</th>
            <th style="width:5%;">默认货品</th>
            <th style="width:4%;">操作</th>
          </tr>
        </thead>
        <tbody id="dataNode">
        <?php $_from = $this->_var['product_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <?php echo $this->_var['item']['fname']; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div>
  <input type="hidden" name="type_id" value="<?php echo $this->_var['type_id']; ?>" />
  <input type="hidden" name="goods_id" value="<?php echo $this->_var['goods_id']; ?>" />
  <input type="submit" value="保存" />
   <!--  <button class="btn save-action" type="button"><span><span>保存</span></span></button>   -->
    </div>
</form>

<script type="text/javascript" id="__eval_scripts__">
var type_id = <?php echo $this->_var['type_id']; ?>;
var goods_id = <?php echo $this->_var['goods_id']; ?>;

$(".test11").click( function () {

	 this.specElements = [];
	 //var speclist = $(".speclist input[type=checkbox]:checked");
	 var id = "";
	 var spstr = "";
	 var mycars=new Array()
	 $(".speclist input[type=checkbox]:checked").each(function(){
		   // alert($(this).val())
		    //alert($(this).attr("p-id"))
		    //spstr[$(this).attr("p-id")
		   /*  mycars[$(this).attr("p-id")] = new Array();
		    mycars[$(this).attr("p-id")]['s_id'] =  $(this).val();
		    mycars[$(this).attr("p-id")]['s_value'] =  $(this).attr("title"); */
		    spstr += $(this).attr("p-id")+"_"+$(this).attr("p-name") + "-" + $(this).val() + "-" + $(this).attr("title") + ",";
		   // mycars[$(this).attr("p-id")]['s_id'] =  $(this).val();
	  });
	if(!spstr)
    {
		alert("请选择规格");
		return;
    }
	 $.post("index.php?app=products&act=fspec", {name: spstr,type_id:type_id,goods_id:goods_id},
	   function(res)
		{
		 if(!res.done)
	     {
			 alert(res.msg);
			 return;
	     }
		 
		 var pstr = "";
		 $.each(res.retval, function(index, content)
		  { 
		 		pstr += content.fname;
		  });
		 
		 $("#dataNode tr").each(function(){
			   // alert($(this).val())
			    //alert($(this).attr("p-id"))
			    //spstr[$(this).attr("p-id")
			   /*  mycars[$(this).attr("p-id")] = new Array();
			    mycars[$(this).attr("p-id")]['s_id'] =  $(this).val();
			    mycars[$(this).attr("p-id")]['s_value'] =  $(this).attr("title"); */
			    if($(this).attr("data-type") == 'new')
			    {
			    	$(this).empty();
			    }
			   // mycars[$(this).attr("p-id")]['s_id'] =  $(this).val();
		  });
		 
		 $("#dataNode").append(pstr);
		},
	 'json'
	 );
	 
return;
 	//alert("fds")
     this.products = [];
     var length = this.specElements.length;
     if(length == this.specLength) {
         this.processProducts(this.specElements, 0, length);
     }
     else if(needs !== false) {
         alert('每个规格项至少选中一项，才能组合成完整的货品信息。');
     }
     this.number.set('text', this.products.length);
     this.createGoodsGrid();

     this.count = 0;
     if(Object.getLength(this.products) && Object.getLength(goods_info)) {
         this.products.some(function(v, i) {
             if(v.bn) {
                 var n = v.bn.match(/^.+\-(\d+)$/);
                 if(n && n[1]) {
                     this.count = Math.max(this.count, Number(n[1]) + 1);
                     return false;
                 }
             }
             return true;
         }, this);
     }

});


function delspe(obj,pid)
{
	//alert($(obj).parents("tr").attr("data-type"));
	if(pid)
	{
		if(window.confirm('你确认要删除此条货品吗？不可恢复！'))
		{
			$.post("index.php?app=products&act=dspec", {pid: pid},
			function(data){
				$(obj).parent().parent().empty();
			});
			$(obj).parents("tr").empty();
			
	     }
		else
		{
	      
	    }
	}
	else
	{
		$(obj).parents("tr").empty();;
	}
	//$(obj).parent().parent().empty();
	
}

function validate_form(thisform)
{
	
	
	
}





//窗口视觉最大化
try{
    if(window.getSize().x < screen.availWidth || window.getSize().y < screen.availHeight){
        moveTo(0,0);
        resizeTo(screen.availWidth, screen.availHeight);
    }
}catch(e){}

//Class的另一种简单实现
var Klass = function(o) {
    var F = function() {
        typeof this.init === 'function' && this.init.apply(this, arguments);
    };
    F.prototype = o || {};
    F.prototype.setOptions = function() {
        this.options = Object.merge.apply(null, [{}, this.options].append(arguments));
        return this;
    };
    F.prototype.fireEvent = function(type, args) {
        type = this.options[type] || this.options['on' + type.capitalize()];
        typeof type === 'function' && type.apply(this, args);
        return this;
    };
    return F;
};

//table翻页组件
var Pager = new Klass({
    options: {
        tpl: 'goodsTpl',
        pager: 'pager',
        current: 1,
        paging: 10
    },
    init: function(container, data, options) {
        this.container = $(container);
        if(!this.container) return;
        this.setOptions(options);
        this.data = data;
        if(!this.data) return;
        this.total = this.data.length;
        this.paging = this.options.paging;
        this.totalPage = Math.ceil(this.total/this.paging);
        this.tpl = $(this.options.tpl).value;
        this.pager = $(this.options.pager);
        this.current = this.options.current;
        this.render(this.current);
        this.attach();
    },
    render: function(n) {
        this.goPage(n);
        this.updatePager();
    },
    attach: function() {
        this.pager.removeEvents('click').addEvents({
            'click': function(e) {
                var target = $(e.target);
                var n;
                if(target.hasClass('disabled')) {
                    return;
                }
                if(target.hasClass('prev')) {
                    n = this.current - 1;
                }
                else if(target.hasClass('next')) {
                    n = this.current + 1;
                }
                else if(target.hasClass('flip')) {
                    n = $(e.target).get('text').toInt();
                }
                else {
                    return;
                }
                this.render(n);
            }.bind(this)
        });
    },
    goPage: function(n) {
        var html = [];
        if(n < 1) n = 1;
        else if(n > this.totalPage) n = this.totalPage;
        if(this.total) {
            for(var i = this.paging * (n - 1), j = this.paging * n, l = this.total, d; i < j && i < l; i++) {
                d = this.data[i];
                Object.each(d, function(v, k) {
                    if(typeOf(v) === 'object') d[k] = JSON.encode(v);
                });
                html.push(this.tpl.substitute(d));
            }
            this.current = n;
        }
        this.container.set('html', html);
    },
    updatePager: function() {
        if(this.total > 10) {
            this.pager.setStyle('display','');
            var pageHtml = this.createLink();
            this.pager.innerHTML = pageHtml;
        }
        else this.pager.setStyle('display', 'none');
    },
    pageLink:function(from, to){
        var links = [];
        for(var i = from; i <= to; i++){
            links.push(this.current == i ? '<span class="current">'+i+'</span>' : '<a class="flip" href="javascript:void(0)">'+i+'</a>');
        }
        return links.join(' ');
    },
    createLink: function() {
        var prev = this.pager.getElement('.prev'),
            next = this.pager.getElement('.next'),
            links = [],
            t=this.totalPage,
            c=this.current;
        if(c == 1) {
            prev.addClass('disabled');
            next.removeClass('disabled');
        }
        else if(c == t) {
            prev.removeClass('disabled');
            next.addClass('disabled');
        }
        else {
            prev.removeClass('disabled');
            next.removeClass('disabled');
        }
        if(t<=10){
            links.push(this.pageLink(1,t));
        }else{
            if(t-c<8){
                links.push(this.pageLink(1,3));
                links.push(this.pageLink(t-8,t));
            }else if(c<8){
                links.push(this.pageLink(1,Math.max(c+3,8)));
                links.push(this.pageLink(t-1,t));
            }else{
                links.push(this.pageLink(1,3));
                links.push(this.pageLink(c-2,c+3));
                links.push(this.pageLink(t-1,t));
            }
        }
        return prev.outerHTML + links.join('...') + next.outerHTML;
    }
});

//用json存取数据
var GoodsSpec;
var goods_info = {"status":"true","product_id":"","price":"","bn":"","weight":"","store":"","freez":"","store_place":"","cost":"","mktprice":"","member_lv_price":{"1":"","2":"","3":"","4":""}};
var Products = {};
var Spec = {};
var activeProducts = null;
//展示货品数据
var goodsSpecTree = new Klass({
    options: {
        speclist:'.speclist',
        specIMG: '.spec-img',
        specMap: 'dataNode',
        switchTrigger: '.typelist',
        switchContent: '.spec-wrap',
        number: '.product-number'
    },
    count: 0,
    init: function(container, options){
        this.setOptions(options);
        this.container = $(container);
        if(!this.container) return;
        this.speclist = this.container.getElements(this.options.speclist);
        this.specLength = this.speclist.length;
        if(!this.specLength) return;
        this.specIMG = this.container.getElements(this.options.specIMG);
        this.specMap = $(this.options.specMap);
        this.switchTriggers = this.container.getElement(this.options.switchTrigger).getChildren();
        this.switchPanels = this.container.getElement(this.options.switchContent).getChildren();
        this.selectAll = this.container.getElements('.selectAll input[type=checkbox]');
        this.number = this.container.getElement(this.options.number);
        this.newProduct = {};
        this.attach();
        // this.build(this.speclist);
        this.createAllGoods(false);
    },
    attach: function() {
        var self = this;
        for(var i = 0; i < this.specLength; i++) {
            (function(i) {
                var trigger = this.switchTriggers[i],
                    panel = this.switchPanels[i],
                    sel = this.selectAll[i],
                    list = this.speclist[i];
                trigger.addEvent('click', function(e) {
                    this.addClass('act').getSiblings('.act').removeClass('act');
                    panel.show().getSiblings().hide();
                });
                var chks = list.getElements('input[type=checkbox]');
                sel.addEvent('change', function(e) {
                    chks.filter(function(el){return !el.disabled;}).set('checked', this.checked);
                    self.build(chks, list, i, trigger);
                })
                chks.addEvent('change', function(e) {
                    self.build(this, this.getParent(self.options.speclist), i, trigger);
                });
            }).call(this, i);
        }
        this.container.addEvents({
            'click:relay(.btn-createAllGoods)': function(e) {
            	alert('ff');
                this.createAllGoods();
            }.bind(this),
            'click:relay(.chooseSpecImage)': function(e) {
                var target = this;
                Ex_Loader('modedialog',function(){
                    new imgDialog('&quot;index.php?app=desktop&act=alertpages&goto=index.php%3Fapp%3Dimage%26ctl%3Dadmin_manage%26act%3Dimage_broswer&quot;',{
                        handle: target,
                        onCallback: function(img,imgsrc){
                            target.getPrevious('span').getElement('img').src = imgsrc;
                            // target.getParent('tr').getElement('input[name*="[spec_image]"]').value = img;
                            var id = target.getParent('tr').get('data-id').split('_');
                            Spec[id[0]].option[id[1]].spec_image = img;
                        }
                    });
                });
            },
            'click:relay(.sel-albums-images)': function(e) {
                var tNode = this.getParent('tr');
                var imgArea = tNode.getElement('.sel-imgs-area');
                var selImgs = imgArea.getNext('.spec_goods_images');
                var data = 'selImgs='+selImgs.get('value');
                var tpl = '<img src="{imgsrc}">';
                if($('goods_spec_images')) data += '&' + $('goods_spec_images').toQueryString();
                new Dialog('index.php?app=b2c&ctl=admin_goods_editor&act=selAlbumsImg',{
                    title:'关联商品图片',
                    ajaxoptions:{data:data, method:'post'},
                    callback:function(sel_img_data,src){
                        var html = '';
                        var id = tNode.get('data-id').split('_');
                        Spec[id[0]].option[id[1]].spec_goods_images = [];
                        src.length && src.each(function(v, i){
                            if(i < 4) html += '<img src="' + v + '">';
                            else if(i == 4) html += '...';
                            Spec[id[0]].option[id[1]].spec_goods_images.push({
                                image_id: sel_img_data[i],
                                image_url: v
                            });
                        });
                        imgArea.set('html',html);
                        selImgs.set('value',sel_img_data.join());
                        // tNode.getElement('input[name*="[spec_goods_images]"]').value = sel_img_data.join();
                    }
                });
            },
            'click:relay(.memberprice)': function(e) {
                var p=this.getParent('tr');
                var pid = p.get('data-pid');
                var mlvp = p.getElement('input.member-lv-price');
                var info = 'product_id=' + pid;
                var vl = mlvp.value;
                if(vl) {
                    vl = Object.toQueryString(JSON.decode(vl), 'level');
                    info += '&' + vl;
                }
                window.fbox = new Dialog('index.php?app=b2c&ctl=admin_goods_editor&act=set_mprice',{ajaxoptions:{data:info,method:'post'},modal:true});
                window.fbox.onSelect = function(arr){
                    var mlvprice = {};
                    var uid = mlvp.name.split('[')[0];
                    arr.each(function(el,i){
                        mlvprice[el.name] = el.value;
                    });
                    self.saveNewProduct(uid);
                    mlvp.value = mlvprice;
                    Products[uid].member_lv_price = mlvprice;
                }
            },
            'click:relay(.clean:not(.disabled))': function(e) {
                var p = this.getParent('tr');
                p.getElements('input[type=text]').set('value', '');
                p.getElements('input[type=checkbox]').set('value', 'false').set('checked', false);
                var uid = this.get('data-uid');
                self.products.each(function(p, i) {
                    if(p.idx == uid) {
                        self.products[i] = {
                            idx: uid,
                            product_id: p.product_id,
                            spec: p.spec
                        };
                        // self.products.erase(p);
                    }
                });
                var oldProduct = Object.merge({}, Products[uid]);
                Products[this.get('data-uid')] = {
                    product_id: 'new',
                    status: 'false',
                    spec_desc: oldProduct.spec_desc
                };

                // delete Products[uid];
            },
            'click:relay(.save-action)': function(e) {
                var keys = [], i = 0, j = self.products.length, k, l = self.props.length, p, attr, flag;
                for(; i < j; i++) {
                    flag = false;
                    p = self.products[i];
                    if(!p.bn) {
                        for(k = 0; k < l; k++) {
                            attr = self.props[k];
                            if(attr === 'product_id' || attr === 'bn' || !p[attr] || p[attr] === 'false') {
                                flag = true;
                                continue;
                            }
                            alert('请填写"' + p.spec + '"商品货号。');
                            try{
                                self.specMap.getElement('input[name="' + p.idx + '[bn]"]').focus();
                            } catch(e) {}
                            return;
                            // flag = 1;
                            // break;
                        }
                    }
                    // if(flag === 1) return;
                    if(flag === true) continue;
                    else keys.push(p.idx);
                }
                // if(flag === 1) return;
                Object.each(Spec, function(v, k) {
                    Object.each(v.option, function(l, m) {
                        if(l.checked === false) delete Spec[k].option[m];
                    });
                });
                Object.each(Products, function(v, k){
                    if(keys.indexOf(k) == -1) delete Products[k];
                });
                self.saveNewProduct(keys);

                new Request.JSON({
                    url: 'index.php?app=b2c&ctl=admin_products&act=save_editor',
                    method: 'post',
                    data: 'spec=' + encodeURIComponent(JSON.encode(Spec)) + '&products=' + encodeURIComponent(JSON.encode(Products)) + "&goods[goods_id]=",
                    onRequest: function() {
                      new MessageBox('正在加载...', {
                          type: 'notice'
                      });
                    },
                    onSuccess: function(rs) {
                        if(rs.result == 'success') {
                            MessageBox.success('操作成功');
                            if(rs.data.is_new == '1' && window.opener && window.opener.isNew){
                                window.opener.isNew(JSON.encode(rs.data));
                            }else if(window.opener && window.opener.rendSpec){
                                window.opener.rendSpec(rs.data.used_spec,rs.data.productNum);
                            }
                            if(confirm('保存成功,是否关闭窗口?')) window.close();
                        }else{
                            Products = Object.merge({}, Products);
                            return MessageBox.error(rs.msg);
                        }
                    }
                }).send();
            }
        });
        this.specIMG.addEvents({
            'change:relay(input[type=text])': function(e) {
                var str = this.name;
                var id = str.match(/\[([^\]]*)\]/)[1];
                id = id.split('_');
                Spec[id[0]].option[id[1]].spec_value = this.value;
            }
        });
        this.specMap.addEvents({
            'change:relay(input[type=checkbox])': function(e) {
                var str = this.name;
                var uid = str.split('[')[0];
                var prop = str.match(/\[([^\]]*)\]/)[1];
                var sib;
                if(prop == 'is_default') {
                    for(var i = 0, j = self.products.length, k; i < j; i ++) {
                        k = self.products[i];
                        if(k[prop] == 'true') {
                            k[prop] = 'false';
                            k[prop + '.checked'] = '';
                            if(Products[k.idx]) Products[k.idx][prop] = 'false';
                            break;
                        }
                    }
                    this.getParent('tr').getSiblings().some(function(el){
                        sib = el.getElement('input[name*="[' + prop + ']"]:checked');
                        if(sib) {
                            sib.checked = false;
                            sib.value = 'false';
                            return true;
                        }
                        else return false;
                    });
                }
                this.value = this.checked ? 'true' : 'false';
                self.storeData(this, uid, prop, sib);
            },
            'blur:relay(input[type=text])': function(e) {
                if(!self.validate(this)) {
                    (function(){this.focus()}).delay(0, this);
                    return false;
                }
            },
            'change:relay(input[type=text])': function(e) {
                var str = this.name;
                var uid = str.split('[')[0];
                var prop = str.match(/\[([^\]]*)\]/)[1];
                self.storeData(this, uid, prop);
            }
        });
    },
    build: function(element, parent, i, trigger) {
        // var d = new Date();
        var id = parent.get('data-spec-id');
        if(element.length) element.each(function(el){
            this.storeSpec(el, id, parent);
        }, this);
        else this.storeSpec(element, id, parent);
        trigger && trigger.getElement('i').set('text', parent.getElements('input[type=checkbox]:checked').length);
        this.createSpecGrid(parent, i, id);
        // this.createAllGoods();
        // console.log(new Date() - d + 'ms');
    },
    storeSpec: function(el, id, parent) {
        var sid = el.value;
        if(el.checked) {
            if(!Spec[id]) {
                Spec[id] = {
                    spec_name: parent.get('data-spec-name'),
                    spec_id: id,
                    spec_type: parent.get('data-spec-type'),
                    option: {}
                };
            }
            if(!Spec[id].option[sid]) {
                Spec[id].option[sid] = {
                    private_spec_value_id: el.name,
                    spec_value: el.title,
                    spec_value_id: sid
                };
                if(Spec[id].spec_type == 'image') Object.merge(Spec[id].option[sid], {
                    spec_image: parent.getElement('input[key=spec_image_' + sid + ']').value,
                    spec_image_url: parent.getElement('img[key=spec_img_' + sid + ']').src
                });
            }
            else delete Spec[id].option[sid].checked;
        }
        else if(Spec[id]) {
            Spec[id].option[sid].checked = false;
        }
    },
    createAllGoods: function(needs) {
//alert('aa');
        this.specElements = [];
        this.speclist.each(function(item, index) {
            var checkBox = item.getElements('input[type=checkbox]:checked');
            if(checkBox.length > 0) {
                this.specElements.push({
                    id: item.get('data-spec-id'),
                    name: item.get('data-spec-name'),
                    input: checkBox
                });
            }
        }, this);

        this.products = [];
        var length = this.specElements.length;
        if(length == this.specLength) {
            this.processProducts(this.specElements, 0, length);
        }
        else if(needs !== false) {
            alert('每个规格项至少选中一项，才能组合成完整的货品信息。');
        }
        this.number.set('text', this.products.length);
        this.createGoodsGrid();

        this.count = 0;
        if(Object.getLength(this.products) && Object.getLength(goods_info)) {
            this.products.some(function(v, i) {
                if(v.bn) {
                    var n = v.bn.match(/^.+\-(\d+)$/);
                    if(n && n[1]) {
                        this.count = Math.max(this.count, Number(n[1]) + 1);
                        return false;
                    }
                }
                return true;
            }, this);
        }
    },
    createSpecGrid: function(list, i, spec_id) {
        var spec = Spec[spec_id];
        var html = ['<thead>'];
        html.push('<tr>');
        html.push('<th>规格</th>');
        if(spec.spec_type == 'image') html.push('<th>规格图片</th>');
        html.push('<th>关联商品图片</th>');
        html.push('</tr>');
        html.push('</thead>');
        html.push('<tbody>');
        Object.each(spec.option, function(v, k) {
            if(v.checked !== false) {
                html.push('<tr data-id="' + spec_id + '_' + v.spec_value_id + '">');
                html.push('<td>');
                if(spec.spec_type == 'image') {
                    html.push('<span class="spec-colorbox"><img src="' + list.getElement('img[key=spec_img_' + v.spec_value_id + ']').src + '"></span>');
                }
                html.push('<input class="x-input" type="text" name="spec_value[' + spec_id + '_' + v.spec_value_id + ']" id="dom_el_cd01927" value="' + v.spec_value + '" />');
                html.push('</td>');
                if(spec.spec_type == 'image') html.push('<td><span class="specImage"><img src="' + v.spec_image_url + '" /></span><b class="chooseSpecImage"></b></td>');
                var imgid = [];
                var imgurl = [];
                v.spec_goods_images && v.spec_goods_images.each(function(img, i){
                    imgid.push(img.image_id);
                    if(i < 4) imgurl.push('<img src="' + img.image_url + '">');
                    else if(i == 4) imgurl.push('...');
                });
                imgid = imgid.join();
                imgurl = imgurl.join('');
                html.push('<td><!--input type="hidden" name="spec[' + spec_id + '][' + v.spec_value_id + '][spec_goods_images]" value="' + imgid + '"--> <span class="sel-albums-images lnk">选择图片</span> <span class="sel-imgs-area">' + imgurl + '</span><input class="spec_goods_images" type="hidden" value="' + imgid + '"></td>');
                html.push('</tr>');
            }
        })
        html.push('</tbody>');

        var table = new Element('table.gridlist').set('html', html);
        table.getElements('input[type=text]').set('disabled', false);
        table.inject(this.specIMG[i].erase('html'));

        html = null;
    },
    props: ['product_id', 'status', 'bn', 'store', 'freez' ,'price', 'member_lv_price', 'cost', 'mktprice', 'weight', 'store_place', 'is_default'],
    processProducts: function(arr, index, length, id, name, value, pvid) {
        var specid = {}, spec_name = [arr[index].name], specvalue = {}, specpvid={}, sname, spec_id = arr[index].id, uid;
        if(name) {
            spec_name = name.concat(spec_name);
        }
        if(value) specvalue = value;
        // if(id) specid = id;
        arr[index].input.each(function(a, i){
            specid[spec_id] = a.value;
            specpvid[spec_id] = a.name;
            specvalue[spec_id] = Spec[spec_id].option[a.value].spec_value;
            if(id) {
                Object.merge(specid, id);
            }
            if(pvid) {
                Object.merge(specpvid, pvid);
            }
            if(index < length - 1) {
                this.processProducts(arr, index + 1, length, specid, spec_name, specvalue, specpvid);
            } else if(index == length - 1) {
                uid = getUniqueID(Object.values(specid).join(';'));
                sname = [];
                spec_name.each(function(s, j) {
                    spec_id = arr[j].id;
                    if(Products[uid] && Products[uid].spec_desc) Products[uid].spec_desc.spec_value[spec_id] = specvalue[spec_id];
                    sname.push(s + ':' + specvalue[spec_id]);
                });
                sname = sname.join('，');
                this.mapping(uid, sname, specvalue, specid, specpvid);
            }
        }, this);
    },
    mapping: function(uid, specname, specvalue, specid, specpvid) {
        var arr = {};
        this.props.each(function(p, i) {
            if(Products[uid] && Products[uid][p]) {
                arr[p] = Products[uid][p];
            }
            else if(goods_info[p]) {
                arr[p] = goods_info[p];
                if(p == 'product_id') {
                    arr[p] = 'new';
                }
                else if (p == 'bn') {
                    arr[p] = arr[p] + '-' + this.count;
                    this.count ++;
                }
            }
            // arr[p] = Products[uid] ? Products[uid][p] || '' : '';
            if((p == 'status' || p == 'is_default') && arr[p] == 'true') {
                arr[p + '.checked'] = 'checked';
            }
            else if(p == 'product_id' && !arr[p]) {
                arr[p] = 'new';
            }
        }, this);
        if(!Products[uid]) {
            this.newProduct[uid] = Object.merge({}, arr);
            this.newProduct[uid].spec_desc = {};
            this.newProduct[uid].spec_desc.spec_private_value_id = Object.merge({}, specpvid);
            this.newProduct[uid].spec_desc.spec_value = Object.merge({}, specvalue);
            this.newProduct[uid].spec_desc.spec_value_id = Object.merge({}, specid);
        }

        if(Products[uid] && activeProducts && activeProducts.length && activeProducts.indexOf(Products[uid].product_id) > -1) {
            arr.unavailable = 'disabled';
            arr.title = '尚有未处理的订单，不能清除此货品。';
        }
        else {
            arr.title = '不生成此货品';
        }
        arr.idx = uid;
        if(specname) arr.spec = specname;
        this.products.push(arr);
    },
    createGoodsGrid: function() {
        this.specMap.store('instance', this);
        // if(!arr) return this.specMap.erase('html');
        var current = this.container.getElement('.current');
        current = current ? parseInt(current.get('text')) : 1;
        this.pager = new Pager(this.specMap, this.products, {current: current, paging: 10});
    },
    saveNewProduct: function(uid) {
        if(typeOf(uid) === 'array' && uid.length) {
            if(Object.getLength(this.newProduct)) {
                Object.each(this.newProduct, function(v, k) {
                    if(uid.indexOf(k) == -1) {
                        delete this.newProduct[k];
                    }
                }, this);
            }
        }
        else if(uid) {
            if(!Products[uid]) {
                Products[uid] = Object.merge({}, this.newProduct[uid]);
                delete this.newProduct[uid];
            }
            return;
        }
        if(Object.getLength(goods_info) && Object.getLength(this.newProduct)) {
            Object.each(this.newProduct, function(v, k) {
                if(v) Products[k] = Object.merge({}, v);
            });
            this.newProduct = {};
        }
    },
    storeData: function(el, uid, prop, sib) {
        this.saveNewProduct(uid);
        Products[uid][prop] = el.value;

        if(prop == 'bn' && Object.getLength(goods_info)) {
            var n = Products[uid][prop].match(/^.+\-(\d+)$/);
            if(n && n[1]) {
                this.count = Math.max(this.count, Number(n[1]) + 1);
            }
        }

        for(var i = 0, j = this.products.length, k; i < j; i++) {
            k = this.products[i];
            if(k.idx === uid) {
                k[prop] = el.value;
                if(el.type == 'checkbox') {
                    k[prop + '.checked'] = el.checked ? 'checked' : '';
                }
                break;
            }
        }
    },
    validate: function(el) {
        var str = el.name;
        var uid = str.split('[')[0];
        var prop = str.match(/\[([^\]]*)\]/)[1];
        if(prop == 'bn' && Products[uid] && activeProducts && activeProducts.indexOf(Products[uid].product_id) > -1) {
            var vtype = el.get('vtype');
            if(!vtype || vtype.indexOf('required') == -1) el.set('vtype', 'required' + (vtype ? '&&' + vtype : ''));
        }
        return validate(el);
    }
});

function getUniqueID(str) {
    return hex_md5(str).substr(0, 10);
}

if(opener && opener.$('new_goods_spec') && opener.$('new_goods_spec').value){
    GoodsSpec = JSON.decode(opener.$('new_goods_spec').value);
    if(GoodsSpec) {
        Products = GoodsSpec.product;
        Spec = GoodsSpec.spec;
        new Request.HTML({
            url:'index.php?app=b2c&ctl=admin_products&act=ajax_set_spec&type_id=5',
            method:'post',
            data: 'spec=' + encodeURIComponent(JSON.encode(Spec)) + '&products=' + encodeURIComponent(JSON.encode(Products)),
            update: 'update_news',
            onSuccess:function(re){
                new goodsSpecTree('specification');
            }
        }).send();
    }
}
else {
    new goodsSpecTree('specification');
}
</script>      </div>
      <div class="content-foot" style="_font-size:0;height:0;"></div>
    </div>
    <div class="side-r hide" id="side-r">
      <div class="side-r-resize" id="side-r-resize">&nbsp;</div>
      <div class="side-r-top clearfix">
        <b class="side-r-title flt f-14"></b>
        <span class="frt side-r-close pointer"><img src="th_files/finder_drop_arrow_close.gif" app="desktop"></span>
      </div>
      <div class="side-r-head" style="border-bottom:1px #999 solid;padding:2px 0 2px 0;font-size:0;height:0;"></div>
      <div id="side-r-content" class="side-r-content" conatainer="true" style="overflow:auto"></div>
      <div class="side-r-foot" style="font-size:0;height:0;"></div>
    </div>
  </div>
</div>

<script hold="true">

var LAYOUT = {
    container: $('container'),
    side: $('side'),
    workground: $('workground'),
    content_main: $('main'),
    content_head: $E('#workground .content-head'),
    content_foot: $E('#workground .content-foot'),
    side_r: $('side-r'),
    side_r_content:$('side-r-content')
};

var initDefaultPart = function(){
    //fixSideLeft = $empty;
    window.resizeLayout = fixLayout = fixSideLeft =function(){

        var winSize = window.getSize();
        var _NUM = function(num){
           num =  isNaN(num)?0:num;
           if(num<0)num=0;
           return num;
        }

            var containerHeight = winSize.y;
        var mw=0,mh=0;

        LAYOUT.container.setStyle('height',_NUM(containerHeight-LAYOUT.container.getPatch().y));
            LAYOUT.container.setStyle('width',_NUM(winSize.x.limit(960, 2000)));


        if(!LAYOUT.side.hasClass('hide')){
              LAYOUT.side.setStyle('width',_NUM( (winSize.x * 0.12).limit(150,winSize.x)));
        }

        LAYOUT.workground.setStyle('width',_NUM(
          (winSize.x - LAYOUT.workground.getPatch().x)
          -LAYOUT.side.getSize().x
          -LAYOUT.side_r.getSize().x)
        ).setStyle('left',LAYOUT.side.offsetWidth);

        LAYOUT.content_main.setStyles({'height':
            (mh=_NUM(containerHeight -
            LAYOUT.content_head.getSize().y  -
            LAYOUT.content_foot.getSize().y -
            LAYOUT.workground.getPatch().y
            )),
            'width':(mw=_NUM(LAYOUT.workground.getSize().x-LAYOUT.workground.getPatch().x))
          }).fireEvent('resizelayout',[mw,mh]);

        if(!LAYOUT.side_r.hasClass('hide')){
            if(!LAYOUT.side_r.get('widthset'))
                LAYOUT.side_r.setStyle('width',_NUM((winSize.x*0.15).limit(150,winSize.x)));
          LAYOUT.side_r_content.setStyle('height',_NUM(
            containerHeight
          -LAYOUT.side_r.getElement('.side-r-top').getSize().y
          -LAYOUT.side_r.getElement('.side-r-head').getSize().y
          -LAYOUT.side_r.getElement('.side-r-foot').getSize().y
          -LAYOUT.side_r_content.getPatch().y-LAYOUT.side_r.getPatch().y));
          LAYOUT.side_r.setStyle('left',winSize.x - LAYOUT.side_r.offsetWidth);
        }
    };

    /*MODAL PANEL*/
    MODALPANEL = {
      createModalPanel:function(){
        var mp = new Element('div',{'id':'MODALPANEL'});
            var mpStyles = {
                'position': 'absolute',
                'background': '#333333',
                'width': '100%',
          'display':'none',
                'height': window.getScrollSize().y,
                'top': 0,
                'left': 0,
                'zIndex': 65500,
                'opacity': .4
            };
        this.element = mp.setStyles(mpStyles).inject(document.body);
        return this.element;
      },
      show:function(){
        var panel = this.element = this.element||this.createModalPanel();
        panel.setStyles({
          'width': '100%',
                  'height': window.getScrollSize().y
        }).show();
      },hide:function(){
        if(this.element)this.element.hide();
      }
    };





      var windowResizeTimer = 0;
      window.addEvent('resize',function(){
       $clear(windowResizeTimer);
       windowResizeTimer = window.resizeLayout.delay(200);

       if(MODALPANEL.element&&MODALPANEL.element.style.display!='none'){
            MODALPANEL.element.setStyles({
                 'height':window.getScrollSize().y
            });
        }
       }).fireEvent('resize');

       EventsRemote = new Request({url:'index.php?ctl=default&act=desktop_events'});


           W = new Wpage({update:document.body,'singlepage':true});
           W.render(document.body);
       W.onComplete();
       Xtip = new Tips({tip:'tip_Xtip',fixed:true,offset: {x: 24, y: -15},onBound:function(bound){
              if(bound.x2){
                  this.tip.getElement('.tip-top').addClass('tip-top-right');
                  this.tip.getElement('.tip-bottom').addClass('tip-bottom-right');
              }else {
                  this.tip.getElement('.tip-top').removeClass('tip-top-right');
                  this.tip.getElement('.tip-bottom').removeClass('tip-bottom-right');
              }
           }});


    Side_R = new Class({
        Implements: [Options, Events],
        options: {

            onShow: $empty,
            onHide: $empty,
            onReady: $empty,
        isClear:true,
        width:false

        },
        initialize: function(url, opts) {
            this.setOptions(opts);
            this.panel = $('side-r');
            this.container = $('side-r-content');
        var trigger = this.options.trigger;

        if(trigger&&!trigger.retrieve('events',{})['dispose']){
                    trigger.addEvent('dispose',function(){

                 $('side-r').addClass('hide');
                 $('side-r-content').empty();
                 $('side-r').removeProperty('widthset').store('url','');

              });
        }

        if(this.panel.retrieve('url','') == url)return;

        if (url) {
                this.showSide(url);
            } else {
                throw Exception('NO TARGET URL');
                return;
            }

           var btn_close = this.panel.getElement('.side-r-close');
         var _title = this.panel.getElement('.side-r-title');

           _title.set('text',this.options.title||"")

          if(btn_close){
                  btn_close.removeEvents('click').addEvent('click', this.hideSide.bind(this));
              }

        },
        showSide: function(url) {
            this.cleanContainer();

            var _this = this;
        if(_this.options.width&&!_this.panel.get('widthset')){
            _this.panel.set({'widthset':_this.options.width,styles:{width:_this.options.width}});
         }
        _this.panel.removeClass('hide');
         _this.fireEvent('show');
        window.resizeLayout();

        if(this.cache)return;
              W.page(url,{
                  update:_this.container,
            render:false,
                  onRequest: function() {
                      _this.panel.addClass('loading');
                  },

                  onComplete: function() {
                      _this.panel.removeClass('loading');
                      _this.fireEvent('ready', $splat(arguments));
                      _this.panel.store('url',url);
            _this.container.style.height = (_this.container.style.height.toInt()-_this.container.getPrevious().getSize().y-_this.container.getNext().getSize().y)+'px';
                  }
              });

        },
        hideSide: function() {

            this.panel.addClass('hide');
            window.resizeLayout();
            this.cleanContainer();
            this.fireEvent('hide');

        },
        cleanContainer: function() {
            this.panel.store('url','');
            if(this.options.isClear)this.container.empty();
        }

    });

    new Drag($('side-r-resize'), {
      modifiers: {
        'x': 'left',
        'y':false
      },
      onBefore:function(el){
        el.addClass('side-r-resize-ing');
      },
      onDrag: function(el) {

        el.addClass('side-r-resize-ing');

      },
      onComplete: function(el) {
        el.removeClass('side-r-resize-ing');

        var left = el.getStyle('left');
          left = left.toInt();
        var _w =  LAYOUT.side_r.style.width.toInt()-(left-(-5));
        LAYOUT.side_r.style.width = _w+'px';
        LAYOUT.side_r.set('widthset',_w);
        el.style.left = '-5px';
        resizeLayout();
      }
    });

    $exec($("__eval_scripts__").get("html"));
   };

  window.addEvent('domready',initDefaultPart);
</script>
<input id="radar_lincense_id" value="" type="hidden">
                 <input id="radar_product_key" value="ecstore" type="hidden">
                 <input id="radar_sign_key" value="A7A82555A7AF9964FD79D60C0D091046" type="hidden">

</body></html>