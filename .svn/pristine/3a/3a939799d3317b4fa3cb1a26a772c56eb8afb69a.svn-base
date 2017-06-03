/*
 @name：产品DIY
 @author：前端老徐
 @email:442413729@qq.com
 @address:www.loveqiao.com
 @date：2015/3/21
 	
 @_diy.param.strData的数据格式，最终提交这个数据
 {
	"0003":{
		fabric:"DBP680A",//面料
		lining:"313:316",//里料
		button:"375:611",//扣子
		styles:["35:37","50:52"],//款式,驳头宽
		cixiu:['签名内容','518：528','1218：427','422：430'],//[签名内容，字体，位置,位置,颜色]
		syLine:['350:357'], //锁眼
		studs:['21:22'],钉扣线
		process:[23:33,34:33]//工艺
	},
	"0004":{....}
 }
*/
//jquery扩展cookie操作
$.extend({
	setCookie:function(c_name,value,expiredays){
		var exdate=new Date()
		exdate.setDate(exdate.getDate()+expiredays)
		document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+';path=/';
	},
	getCookie:function(c_name){
		if (document.cookie.length>0){
			c_start=document.cookie.indexOf(c_name + "=")
			if (c_start!=-1){ 
				c_start=c_start + c_name.length+1 
				c_end=document.cookie.indexOf(";",c_start)
				if (c_end==-1) c_end=document.cookie.length
				return unescape(document.cookie.substring(c_start,c_end))
			} 
		}
		return ""
	},
	clearCookie:function(c_name){
		$.setCookie(c_name,'',0)	
	}
});


function Custom(options){
	this.showArea=this.R(options.showArea);//展示区
	this.angle="a";//a:正面、b：侧面、c背面
	this.categoryId=[];//保存分类id集合
	this.diyData={};//当前diy数据
	this.rateGet=100;//get请求频率（ms）
	this.worksID=null;//作品id
	
	this.uf=1;//0:未完成首单体验款 1:是
	this.firstPrice_0003=999;//套装首单价格
	this.firstPrice_0006=199;//衬衣首单价格
	
	this.flag=window.location.href.indexOf('m.mfd.cn')>=0;//区分线上线下
	this.api=this.flag?'http://api.diy.mfd.cn/v1/':'http://api.diy.dev.mfd.cn:8080/v1/';//接口地址
	this.cartUrl=this.flag?'http://m.mfd.cn/':'http://new.m.dev.mfd.cn/';//购物车地址
	
	this.cache='';//控制ajax全局缓存
	this['access-token']='101-token';
	this.clothing=null,//定义初始的id
	this.pwd=null//加密串 url截取
	this.style=null//风格 url截取
	this.param={
		clothing:null,//当前diy的clothingID
		strData:null,//diy工艺数据
	}	
}

//ID选择器
Custom.prototype.R=function(id){
	return document.getElementById(id)	
}

//初始化
Custom.prototype.init=function(data){
	_diy=this;
	if(window.location.href.indexOf('custom-diy-')>=0){
		//截取clothingID
		var id=window.location.href.split('custom-diy-')[1].split('-')
		_diy.clothing=id[0];
		_diy.param.clothing=id[0];
		//截取风格
		_diy.style=id[1];
		//截取加密串
		try{
			_diy.pwd=id[2].split('.')[0];
		}
		catch(e){}
	}
	
	//视图大小控制
	window.onresize=delayFn;
	var delay=null;
	function delayFn(){
		if(delay){clearTimeout(delay)}
		delay=setTimeout(setShowAreaH,120)	
	}
	function setShowAreaH(){
		var winHeight=$(window).height(),
			winWidth=$(window).width(),
			num=(winHeight-227<=winWidth)?winHeight-227:winWidth;
		$('.main').height(winHeight);
		_diy.R('showArea').style.height=num+'px';
		_diy.R('showArea').style.width=num+'px';
	}
	setShowAreaH();

	//返回事件
	$('#cat2').on('click','.js-back',function(){
		$('#cat2').hide();
		$('#cat1').show();
	})
	//diy切换
	_diy._switch();
	//获取导航菜单
	_diy.getNav();
	//下单按钮
	_diy.order();
	//模特旋转
	//_diy.rotate();
	//保存作品
	//_diy.saveWorks();
	//模拟alert
	_diy.alert(window);
}

/*收集工艺参数*/
Custom.prototype.cstr=function(){
	var arr=[];
	$.each(_diy.param.strData[_diy.param.clothing],function(name,value){
		if(name!='fabric'&&name!='lining'&&name!='cixiu'){
			if(name=='button'){
				arr.push(value)	
			}else{
				$.each(value,function(i,v){
					arr.push(v)	
				})	
			}
		}	
	})
	return arr.join('|')
}



/*DIY分类切换*/
Custom.prototype._switch=function(){
	$('#switch').click(function(){
		var obj=$(this).parents('.opt');
		if(obj.hasClass('show')){
			obj.removeClass('show')	
		}else{
			obj.addClass('show')	
		}
	})	
}

//超时出现刷新提示
Custom.prototype.timeOut=function(){
	if(_diy._timeOut_){
		clearTimeout(_diy._timeOut_)
	}
	_diy._timeOut_=setTimeout(function(){
		$('#refresh').fadeIn('fast');
	},4000)
}

/*loading*/
Custom.prototype.loading={
	show:function(){
		$('body').addClass('showLoading');
		$('#zhezhaoLayer').show();
		_diy.timeOut();
	},
	hide:function(){
		$('body').removeClass('showLoading');
		$('#zhezhaoLayer').hide();
		clearTimeout(_diy._timeOut_)	
		$('#refresh').fadeOut('fast')
	}
}


/*======================================*
			DIY程序初始化 start
*=======================================*/

//组件选项html模版
Custom.prototype.setOptHtml=function(obj){
	var html={
		fabric:'<div id="opt-fabric">\
					<div class="titBar"><span class="tit fl js-back">面料</span></div>\
					<div class="scrollBox">\
						<div class="subCat">\
							<div class="cat2-1"></div>\
							<div class="cat2-2"></div>\
						</div>\
						<div class="itemList">\
							<div class="loading">loading...</div>\
						</div>\
					</div>\
				</div>',
		lining:'<div id="opt-lining">\
					<div class="titBar"><span class="tit fl js-back">里料</span></div>\
					<div class="scrollBox">\
						<div class="itemList">\
							<div class="loading">loading...</div>\
						</div>\
					</div>\
				</div>',
		button:'<div id="opt-button">\
					<div class="titBar"><span class="tit fl js-back">纽扣</span></div>\
					<div class="scrollBox">\
						<div class="itemList">\
							<div class="loading">loading...</div>\
						</div>\
					</div>\
				</div>',
		style:'<div id="opt-style">\
					<div class="titBar"><span class="tit fl js-back">细节</span></div>\
					<div class="scrollBox">\
						<div class="subCat">\
							<div class="cat2-1"></div>\
						</div>\
						<div class="itemList">\
							<div class="loading">loading...</div>\
						</div>\
					</div>\
				</div>',
		embroidery:'<div id="opt-embroidery">\
					<div class="titBar"><span class="tit fl js-back">签名</span><span class="del fr">取消</span><span class="save fr">保存</span></div>\
					<div class="scrollBox">\
						<div class="itemList">\
						<div class="cxCon"><input id="cxCon" type="text" placeholder="可输入7个汉字或14个字母" maxlength="14"></div>\
							<div class="minTit">字体</div>\
							<div class="cxFont" id="cxFont"><div class="loading">加载中...</div></div>\
							<div class="minTit">位置</div>\
							<div class="cxPosition" id="cxPosition"><div class="loading">加载中...</div></div>\
							<div class="minTit">颜色</div>\
							<div class="cxColor" id="cxColor"><div class="loading">加载中...</div></div>\
						</div>\
					</div>\
				</div>',
		keyhole:'<div id="opt-keyhole">\
					<div class="titBar"><span class="tit fl js-back">锁眼线</span></div>\
					<div class="scrollBox">\
						<div class="subCat">\
							<div class="cat2-1"></div>\
						</div>\
						<div class="itemList">\
							<div class="loading">loading...</div>\
						</div>\
					</div>\
				</div>',
		studs:'<div id="opt-studs">\
					<div class="titBar"><span class="tit fl js-back">钉扣线</span></div>\
					<div class="scrollBox">\
						<div class="itemList">\
							<div class="loading">数据准备中...</div>\
						</div>\
					</div>\
				</div>',
		process:'<div id="opt-process">\
					<div class="titBar"><span class="tit fl js-back">工艺</span></div>\
					<div class="scrollBox">\
						<div class="itemList">\
							<div class="loading">数据准备中...</div>\
						</div>\
					</div>\
				</div>'
		
	}
	if(html.hasOwnProperty(obj)){
		return html[obj];
	}else{
		luck.open({content:'预定义html模版不完整',time:1.5});
	}
}


//导航数据处理
Custom.prototype.setupNav =function(d){
	var data=d.data, bigNav=[],len=data.length,flag=true;
	if(len>1){
		for(var i=0;i<len;i++){
			bigNav.push('<span data-id="'+data[i].nav.id+'">'+data[i].nav.name+'</span>');
			_diy.categoryId.push(data[i].nav.id);
		}
		$('#tzBtnBox').html(bigNav.join(''));
		$('.tzBtnBox span').click(function(){
			$(this).addClass('cur').siblings('.cur').removeClass('cur');
			_diy.param.clothing=this.getAttribute('data-id');
			upData($(this).index());
		}).eq(0).trigger('click');
	}else{
		$('#tzBtnBox').hide();
		_diy.param.clothing=data[0].nav.id;
		_diy.categoryId.push(data[0].nav.id);
		upData(0);
	}
	
	//更新尺码表
	_diy.getSizeTable(data);

	//大导航点击处理
	function upData(g){
		var smallNav=[],opt=[];
		for(var n in data[g]){
			if(n!='nav'){
				smallNav.push('<li id="nav-'+n+'" data-id="'+data[g][n].id+'">'+data[g][n].name+'</li>');
				opt.push(_diy.setOptHtml(n));	
			}
		}
		//更新组件导航
		$('#cat1').html(smallNav.join('')).show();
		//更新组件模版
		$('#cat2').html(opt.join('')).hide();
		//定义diy默认值
		if(flag){
			if($.getCookie('diyData')){
				_diy.param.strData=eval('('+$.getCookie('diyData')+')');
				$.clearCookie('diyData');
			}else{
				_diy.param.strData=d.sysprocess;
			}
			flag=false;
		}
		//获取基本款数据
		_diy.getBase();
	}
	//定义默认价格
	$('#price').text(Number(d.cnt).toFixed(2));
	//定义是否首单
	_diy.uf=Number(d.u_f);
}

//获取导航数据
Custom.prototype.getNav=function(){
	var data={
			cache:_diy.cache,
			clothingID:_diy.clothing,
			style:_diy.style,
			'access-token':_diy['access-token'],
			token:_diy.pwd
		},
		arr=window.location.href.split('-');
		if(arr.length>5){
			data.worksID=_diy.worksID=arr[5].split('.')[0];
		}
	$.ajax({
		url:_diy.api+'inits/nav',
		data:data,
		dataType:"jsonp",
		success:function(data){
			//alert(JSON.stringify(data))
			_diy.setupNav(data);
			//零部件分类切换
			_diy.selectCat();
		},
		error:function(){
			alert('获取导航失败')	
		}
	});
}


//零部件分类切换
Custom.prototype.selectCat=function(){
	$('#cat1').off('click').on('click','li',function(){
		var id=this.id;
		$('#cat1').hide();
		$('#cat2').show().children('div').eq($(this).index()).siblings().hide().end().show();;
		switch(id){
			case 'nav-fabric':
				if(this.getAttribute('flag')!=1){
					//获取面料
					_diy.getFabricCat();
					this.setAttribute('flag',1)
				}
			break;
			case 'nav-lining':
				if(this.getAttribute('flag')!=1){
					_diy.getLining(20,this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break;
			case 'nav-button':
				if(this.getAttribute('flag')!=1){
					_diy.getButtons(20,this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break;
			case 'nav-style':
				if(this.getAttribute('flag')!=1){
					_diy.getStyleCat(this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break
			case 'nav-embroidery':
				if(this.getAttribute('flag')!=1){
					_diy.getCxColorFont(this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break
			case 'nav-keyhole':
				if(this.getAttribute('flag')!=1){
					_diy.getSyLine();
					this.setAttribute('flag',1)
				}
			break;
			case 'nav-studs':
				if(this.getAttribute('flag')!=1){
					_diy.getbutLine(20,this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break;
			case 'nav-process':
				if(this.getAttribute('flag')!=1){
					_diy.getCrafts(this.getAttribute('data-id'));
					this.setAttribute('flag',1)
				}
			break;
		}
	});
}

/*======================================*
			DIY程序初始化 end
*=======================================*/


/*======================================*
			获取价格 start
*=======================================*/

Custom.prototype.getPrice=function(){
		var arr=[];//0003:DBM584A,1339,1199,433|0004:...  面料、里料、工艺
		for(n in _diy.param.strData){
			var data=_diy.param.strData[n],str=[n];
			str.push(':');
			str.push(data.fabric);
			if(data.lining){
				str.push(',');
				str.push(data.lining.split(':')[1])	
			}
			if(data.process){
				if(data.process[0]){
					str.push(',');
					str.push(data.process[0].split(':')[1])		
				}
				if(data.process[1]){
					str.push(',');
					str.push(data.process[1].split(':')[1])		
				}
			}
			arr.push(str.join(''))	
		}
		$.ajax({
			url:_diy.api+'inits/pcnt',
			data:{
				cache:_diy.cache,
				codestr:arr.join('|'),
				'access-token':_diy['access-token'],
				token:_diy.pwd
			},
			dataType:"jsonp",
			success:function(data){
				//alert(JSON.stringify(data))
				$('#price').html(data)
			},
			error:function(){
				alert('获取价格失败')	
			}
		});
}

/*======================================*
			获取价格 end
*=======================================*/



/*======================================*
			匹配默认值 start
*=======================================*/

Custom.prototype.checkDefault=function(obj){
	//检测全局里有没有默认值，没有则走默认
	var data = _diy.param.strData[_diy.param.clothing],type=obj.type;
	if(type=='fabric'||type=='lining'||type=='studs'||type=='button'){
		if(data[type]){
			return defaults()
		}else if(obj.selected){
			return ' class="cur"'	
		}
	}
	
	if(type=='styles'||type=='cixiu'||type=='syLine'||type=='process'){
		if(data[type].join(',').indexOf(obj.pid+':')>=0){
			return defaults()	
		}else if(obj.selected){
			return ' class="cur"'	
		}
	}
	
	//匹配默认值
	function defaults(){
		//字符串格式的匹配
		if(type=='fabric' && data[type]==obj.code){
			return ' class="cur"'
		}
		if((type=='lining'&&data[type]==obj.pid+':'+obj.id)||(type=='studs'&&data[type]==obj.pid+':'+obj.id)||(type=='button'&&data[type]==obj.pid+':'+obj.id)){
			return ' class="cur"'
		}
		
		//数组格式的匹配
		if(type=='styles'||type=='cixiu'||type=='syLine'||type=='process'){
			if(data[type].join(',').indexOf(obj.pid+':'+obj.id)>=0){
				return ' class="cur"'
			}
		}
	}
}

/*======================================*
			匹配默认值 end
*=======================================*/



/*======================================*
		影响视图diy的方法 start
*=======================================*/
//渲染数据-后端合成图片
Custom.prototype.newUpData=function(url,rotate){
	var img=new Image;
	img.src=url;
	img.onload=function(){
		_diy.showArea.innerHTML=''
		_diy.showArea.appendChild(img);
		_diy.loading.hide();//关闭loading
		//同步diy数据
		if(!rotate){//如果不是旋转则更新数据，防止保存、下单生成侧身图片
			_diy.diyData[_diy.param.clothing]=url;
		}
	}	
}

//获取基本款
Custom.prototype.getBase=function(){
	_diy.loading.show();
	var data=_diy.diyData[_diy.param.clothing];
	if(data){//从缓存渲染数据
		_diy.newUpData(data);
		return	
	}
/*
  *getDiyData方法是通过工艺信息来获取基本款，页面初始化工艺等同于inits/all接口获取基本款，所以取消inits/all接口统一改为 通过工艺信息获取diy视图
*/
//	if(_diy.worksID){
		_diy.getDiyData();
//		return
//	}
//	var obj={
//			cache:_diy.cache,
//			clothingID:_diy.param.clothing,
//			style:_diy.style,
//			'access-token':_diy['access-token'],
//			}
//	if(_diy.clothing=='0001'||_diy.clothing=='0002'){
//		obj.suitID=_diy.clothing	
//	}
//	$.ajax({
//		url:_diy.api+'inits/all',
//		data:obj,
//		dataType:"jsonp",
//		success:function(data){
//			//alert(JSON.stringify(data))
//			_diy.newUpData(data[0]);
//		},
//		error:function(){
//			alert('基本款数据错误')
//		}
//	})
}

//切换面料DIY视图
Custom.prototype.getDiyData=function(){
	_diy.loading.show();
	//发送请求
	$.ajax({
		url:_diy.api+'inits/display',
		data:{
			 cache:_diy.cache,
			 clothingID:_diy.param.clothing,
			 style:_diy.style,
			 strFabricCode:_diy.param.strData[_diy.param.clothing].fabric,
		     cstr:_diy.cstr(), //工艺
			 'access-token':_diy['access-token']
		},
		dataType:"jsonp",
		success:function(data){
			_diy.newUpData(data[0]);
		},
		error:function(){
			alert('基本款数据错误')	
		}
	})	
}

//切换工艺DIY视图（公共）
Custom.prototype.getDiyData2=function(obj){
	_diy.loading.show();
	//发送请求
	$.ajax({
		url:_diy.api+'selects/all',
		data:{
			 cache:_diy.cache,
			 style:_diy.style,
			 clothingID:_diy.param.clothing,
			 fabricCode:_diy.param.strData[_diy.param.clothing].fabric,
			 parentID:obj.pid,
			 ids:obj.ids,
			 menuId:obj.mid,
		     cstr:_diy.cstr(), //工艺
			 'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			_diy.newUpData(data.p[0]);
		},
		error:function(){
			alert('基本款数据错误')	
		}
	})	
}

//获取旋转数据更新视图
Custom.prototype.getRotateData=function(g){
	_diy.loading.show();
	$.ajax({
		url:_diy.api+"inits/rotation",
		data:{
			cache:_diy.cache,
			clothingID:_diy.param.clothing,
			style:_diy.style,
			strFabricCode:_diy.param.strData[_diy.param.clothing].fabric,
			viewID:g,
		    cstr:_diy.cstr(), //工艺
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success: function(data){
			//alert(JSON.stringify(data))
			_diy.newUpData(data[0],1)
		},
		error:function(){
			alert('旋转失败')	
		}	
	})	
}


/*======================================*
		影响视图diy的方法 end
*=======================================*/



/*======================================*
				面料 start
*=======================================*/

//获取面料分类
Custom.prototype.getFabricCat=function(){
	function addHtml(data){
		var data1=data[0],cat1=[],len1=data1.length,cat2=[];
		for(var i=0;i<len1;i++){
			//组织面料一级分类
			if(i==0){cat1.push('<li id="allFabric" class="cur">全部</li>')}
			cat1.push('<li data-pid="'+data1[i].parentid+'" data-id="'+data1[i].id+'">'+data1[i].name+'</li>');
			//组织面料筛选分类
			var data2=data[i+1],len2=data2.length;
			for(var n=0;n<len2;n++){
				if(n==0){cat2.push('<div class="hide">')}
				cat2.push('<span data-pid="'+data2[n].parentid+'" data-id="'+data2[n].id+'">'+data2[n].name+'</span>');
				if(n==len2-1){cat2.push('</div>')}
			}
		}
		//渲染面料一级分类并绑定事件
		$('#opt-fabric .cat2-1').html('<ul>'+cat1.join('')+'</ul>').find('li').unbind('click').click(function(){
			var index=$(this).index(),obj=$('#opt-fabric .cat2-2 div');
			$(this).addClass('cur').siblings().removeClass('cur');
			if(index==0){
				 obj.find('.cur').removeClass('cur').end().hide();
				 $('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
				 _diy.getFabric(20,{
					flowerID:'',
					colorID:'',
					compositionID:'',
				});
				return
			}
			obj.eq(index-1).siblings().hide().end().show();
		});
		
		//初始化全部面料
		$('#allFabric').trigger('click');

		//渲染面料二级分类并绑定事件
		$('#opt-fabric .cat2-2').html(cat2.join('')).find('span').unbind('click').click(function(){
			var type=$(this).parent('div').index(),obj={};
			$(this).parents('.cat2-2').find('.cur').removeClass('cur');
			$(this).addClass('cur');
			switch(type){
				case 0:
					obj={
						flowerID:$(this).attr('data-id'),
						colorID:'',
						compositionID:'',
					}
				break
				case 1:
					obj={
						flowerID:'',
						colorID:$(this).attr('data-id'),
						compositionID:'',
					}
				break
				case 2:
				obj={
						flowerID:'',
						colorID:'',
						compositionID:$(this).attr('data-id'),
					}	
			}
			//alert(JSON.stringify(obj))
			$('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
			_diy.getFabric(20,obj);
		});
		
		//绑定面料切换事件
		_diy.selectFabricOut();
	}
	
	//数据请求
	$.ajax({
		url:_diy.api+'inits/menu',
		data:{
			cache:_diy.cache,
			parentID:$('#nav-fabric').attr('data-id'),
			level:2,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data)
		},
		error:function(){
			alert('面料分类error')	
		}
	});
}


//获取面料数据
Custom.prototype.getFabric=function(n,options){
	var data=null,page=0,obj=$('#opt-fabric .itemList');
	//组织数据
	function addHtml(start,end){
		var html=[];
		for(var i=start;i<end;i++){
			if(i==0&&page==0){
				html.push('<ul>')	
			}
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'fabric',
				pid:'',
				id:'',
				code:data[i].code,
				selected:data[i].selected
			}),isfirst_price='',isfirst_flag="",isfirst_ico="",hd="";
			
			//设置首单体验款面料
			if(_diy.uf==0&&data[i].is_first==1){			
				if(_diy.clothing=='0003'){
					isfirst_price="<br>￥"+_diy.firstPrice_0003;
					isfirst_flag=" data-first='1'";
					isfirst_ico="<span class='ico-first'></span>"
				}
				if(_diy.clothing=='0006'){
					isfirst_price="<br>￥"+_diy.firstPrice_0006;
					isfirst_flag=" data-first='1'";
					isfirst_ico="<span class='ico-first'></span>"
				}	
			}
			if(data[i].activity==1){
				hd="<span class='ico-hd'></span>"	
			}
			
			html.push('<li'+(checkFocus?checkFocus:'')+isfirst_flag+' data-code="'+data[i].code+'"><img src="'+data[i].imgurl+'" ><p>'+data[i].tname+isfirst_price+'</p>'+isfirst_ico+hd+'</li>');					
			
			if(i==end-1&&page==0){
				html.push('</ul>');
			}
		}
		if(page==0){
			obj.html(html.join(''));
		}else{
			obj.children('ul').append(html.join(''));
		}
		page=end;
	}
	//分页处理
	function ajaxPage(d){
		data=d;//同步请求数据到上层作用域
		var len=data.length,obj=$('#opt-fabric .scrollBox');
		addHtml(page,len>n?n:len)//显示第一页数据
		//无限加载
		var h=obj.height(),
			delay=null,
			advance=20;//提前多少px开始加载
		obj.unbind('scroll').scroll(function(){
			if(delay){clearTimeout(delay)}
			delay=setTimeout(function(){
				var scr=obj[0].scrollTop;
				if(scr>=(obj[0].scrollHeight-h-advance)){
					if(len-page>0){
						var end=(len-page>=n)?page+n:len
						addHtml(page,end)
					}else{
						oUl=obj.find('.itemList ul');
						//oUl.append('<li class="null">加载完毕</li>')	
						obj.unbind('scroll');
					}
				}
			},120)
		})
	}
	//数据请求
	$.ajax({
		url:_diy.api+'fabrics/all',
		data:{
			cache:_diy.cache,
			fabricid:$('#nav-fabric').attr('data-id'),
			flowerID:options.flowerID,
			colorID:options.colorID,
			compositionID:options.compositionID,
			cp:0,
			ps:0,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			if(data.length>0){
				ajaxPage(data)
			}else{
				obj.html('<div class="status">没有数据！</div>');
			}
		},
		error:function(){
			alert('面料数据error')	
		}
	});
}

//绑定面料切换
Custom.prototype.selectFabricOut=function(){
	function fn(){
		var $this=$(this);
		if($this.hasClass('null'))return;
		$this.addClass('cur').siblings().removeClass('cur');
		_diy.param.strData[_diy.param.clothing].fabric=this.getAttribute('data-code');
		_diy.getDiyData();
		if(_diy.clothing=="0003" && $this.attr('data-first')){//如果点击首单体验面料
			$('#price').text(_diy.firstPrice_0003)
			return	
		}
		if(_diy.clothing=="0006" && $this.attr('data-first')){
			$('#price').text(_diy.firstPrice_0006)
			return	
		}
		_diy.getPrice();
	}
	$('#opt-fabric .itemList').off('click').on('click','li',fn);
}

/*======================================*
				面料 end
*=======================================*/



/*======================================*
				里料 start
*=======================================*/

//里料数据
Custom.prototype.getLining=function(n,pid){//每页显示数、parentID
	var data=null,page=0,obj=$('#opt-lining .itemList');
	//组织数据
	function addHtml(start,end){
		var html=[];
		for(var i=start;i<end;i++){
			if(i==0&&page==0){
				html.push('<ul>')	
			}
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'lining',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			html.push('<li'+(checkFocus?checkFocus:'')+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');					
			if(i==page+n-1&&page==0){
				html.push('</ul>');
			}
		}
		if(page==0){
			obj.html(html.join(''));
		}else{
			obj.children('ul').append(html.join(''));
		}
		page=end;
	}
	//分页处理
	function ajaxPage(d){
		data=d.imges;//同步请求数据到上层作用域
		var len=data.length,obj=$('#opt-lining .scrollBox');
		addHtml(page,len>n?n:len)//显示第一页数据
		//无限加载
		var h=obj.height(),
			delay=null,
			advance=20;//提前多少px开始加载
		obj.scroll(function(){
			if(delay){clearTimeout(delay)}
			delay=setTimeout(function(){
				var scr=obj[0].scrollTop;
				if(scr>=(obj[0].scrollHeight-h-advance)){
					if(len-page>0){
						var end=(len-page>=n)?page+n:len
						addHtml(page,end)
					}else{
						oUl=obj.find('.itemList ul');
						//oUl.append('<li class="tip">加载完毕</li>')	
						obj.unbind('scroll');
					}
				}
			},120)
		})
	}
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			ajaxPage(data)
		},
		error:function(){
			alert('里料基础数据error')	
		}
	});
	//绑定里料切换事件
	_diy.selectfabricIn();
}

//里料切换
Custom.prototype.selectfabricIn=function(){
	var flag=true;
	function fn(){
		if(flag){
			$(this).addClass('cur').siblings().removeClass('cur');
			_diy.param.strData[_diy.param.clothing].lining=this.getAttribute('data-pid')+':'+this.getAttribute('data-id');
			_diy.getPrice();
			flag=false;
			setTimeout(function(){
				flag=true	
			},_diy.rateGet);
		}
	}
	$('#opt-lining').off('click').on('click','li',fn)	
}

/*======================================*
				里料 end
*=======================================*/









/*======================================*
				扣子 start
*=======================================*/

//扣子数据
Custom.prototype.getButtons=function(n,pid){
	var data=null,page=0,obj=$('#opt-button .itemList');
	//组织数据
	function addHtml(start,end){
		var html=[];
		for(var i=start;i<end;i++){
			if(i==0&&page==0){
				html.push('<ul>')	
			}
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'button',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			if(data[i].imgurl!=null){
				html.push('<li'+(checkFocus?checkFocus:'')+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'" data-code="'+data[i].ecode+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');	
			}
			if(i==page+n-1&&page==0){
				html.push('</ul>');
			}
		}
		if(page==0){
			obj.html(html.join(''));
		}else{
			obj.children('ul').append(html.join(''));
		}
		page=end;
	}
	//分页处理
	function ajaxPage(d){
		data=d.imges;//同步请求数据到上层作用域
		var len=data.length,obj=$('#opt-button .scrollBox');
		addHtml(page,len>n?n:len)//显示第一页数据
		//无限加载
		var h=obj.height(),
			delay=null,
			advance=20;//提前多少px开始加载
		obj.scroll(function(){
			if(delay){clearTimeout(delay)}
			delay=setTimeout(function(){
				var scr=obj[0].scrollTop;
				if(scr>=(obj[0].scrollHeight-h-advance)){
					if(len-page>0){
						var end=(len-page>=n)?page+n:len
						addHtml(page,end)
					}else{
						oUl=obj.find('.itemList ul');
						//oUl.append('<li class="tip">加载完毕</li>')	
						obj.unbind('scroll');
					}
				}
			},120)
		})
	}
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			ajaxPage(data)
		},
		error:function(){
			alert('扣子基础数据error')	
		}
	});
	//绑定扣子切换事件
	_diy.selectButton();
}

//切换扣子
Custom.prototype.selectButton=function(){
	var flag=true;
	function fn(){
		if(flag){
			$(this).addClass('cur').siblings().removeClass('cur');
			var pid=this.getAttribute('data-pid'),
				id=this.getAttribute('data-id'),
				code=this.getAttribute('data-code'),
				mid=$('#nav-button').attr('data-id');
			//同步全局工艺变量
			_diy.param.strData[_diy.param.clothing].button=pid+':'+id+'|||'+code;
			//获取数据
			_diy.getDiyData2({
				pid:pid,
				ids:id,
				mid:mid	
			});
			flag=false;
			setTimeout(function(){
				flag=true	
			},_diy.rateGet);
		}
	}
	$('#opt-button').off('click').on('click','li',fn);
}

/*======================================*
				扣子 end
*=======================================*/





/*======================================*
				细节 start
*=======================================*/

//款式分类数据
Custom.prototype.getStyleCat=function(pid){
	//组织数据
	function addHtml(data){
		var html=[],html2=[],len=data.length;
		for(var i=0;i<len;i++){
			html.push('<li data-id="'+data[i].id+'">'+data[i].name+'</li>');
			html2.push('<div'+(i!=0?" class='hide'":"")+' id="s'+data[i].id+'"><div class="loading">加载中...</div></div>');
		}
		if(len%3!=0){
			for(var g=0;g<(3-len%3);g++){
				html.push('<li class="null">&nbsp;</li>')	
			}	
		}
		$('#opt-style .cat2-1').html('<ul>'+html.join('')+'</ul>');
		$('#opt-style .itemList').html(html2.join(''));
		
		//绑定款式切换事件
		_diy.selectStyle();
		//初始化第一个款式数据
		$('#opt-style .cat2-1 li:eq(0)').trigger('click');
	}
	//数据请求
	$.ajax({
		url:_diy.api+'inits/menu',
		data:{
			cache:_diy.cache,
			parentID:pid,
			clothingID:_diy.param.clothing,
			level:2,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data)
		},
		error:function(){
			alert('款式分类error')	
		}
	});
}

//获取款式三级分类（驳头宽、下摆型、裤膝）
Custom.prototype.getStyleData3=function(ids,menuId,parentID){
	function btwHtml(data){
		var tit='', html=[],len=data.length;
		for(var i=0;i<len;i++){
			if(data[i].level==1){
				tit='<div class="minTit">'+data[0].name+'</div>';
			}else{
				html.push('<span'+(data[i].selected?" class='cur'":"")+' data-pid="'+data[i].parentid+'" data-id="'+data[i].id+'">'+data[i].name+'</span>');
			}
			if(data[i].selected){//更新str默认值
				(function(j){
					var pid=data[j].parentid,n,flag=null,id=data[j].id,arr=_diy.param.strData[_diy.param.clothing].styles;
					for(var i=0;i<len;i++){
						(arr[i].indexOf(pid+':')>=0) && (flag=true,n=i)
					}
					flag?(arr[n]=pid+':'+id):(arr.push(arr[n]=pid+':'+id));
				})(i)
			}
		}
		if($('#s'+menuId).find('.weitiao').length){
			$('#s'+menuId).find('.weitiao').html(html.join(''));
		}else{
			$('#s'+menuId).append(tit+'<div class="weitiao">'+html.join('')+'</div>');
		}
	}

	//数据请求
	$.ajax({
		url:_diy.api+'selects/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			ids:ids,
			menuId:menuId,
			parentID:parentID,
		    cstr:_diy.cstr(), //工艺
			clothingID:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(d){
			btwHtml(d.options);
		},
		error:function(){
			alert('款式数据error')	
		}
	});

}

Custom.prototype.styleType={
	weitiao:[1119,35459,2133],
	twocat:[176]	
}
//款式二级数据
Custom.prototype.getStyleData=function(n,pid){
	var flag=true;
	//组织数据
	function addHtml(data){
		var html=[],html2=[],html3=[],len=data.length,
		isweitiao=(function(){
			var arr=_diy.styleType.weitiao,len=arr.length;
			for(var i=0;i<len;i++){
				if(pid==arr[i]){
					return true;	
				}	
			}
			return false
		})(),
		istwocat=(function(){
			var arr=_diy.styleType.twocat,len=arr.length;
			for(var i=0;i<len;i++){
				if(pid==arr[i]){
					return true;	
				}	
			}
			return false
		})();
		
		
		for(var i=0;i<len;i++){
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'styles',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected||data[i].isdefault=="10050"
			}),cur='';
			if(checkFocus){
				cur=checkFocus;
				//如果有微调信息&&有选中的工艺获取微调数据
				if(isweitiao&&flag){
					_diy.getStyleData3(data[i].id,pid,data[i].parentid);
					flag=false;
				}
			}
			if(istwocat){//如果有二级分类
				if(data[i].parentid==data[0].id){
					if(data[i].imgurl!=null){
						html2.push('<li'+cur+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
					}
				}
				if(data[i].parentid==data[1].id){
					if(data[i].imgurl!=null){
						html3.push('<li'+cur+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
					}
				}
			}else{
				if(data[i].imgurl!=null){
					html.push('<li'+cur+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
				}else if(data[i].name=='无'){
					html.push('<li'+cur+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="http://m.mfd.cn/app-diy/images/error.png"><p>'+data[i].name+'</p></li>');
				}
			}
		}
		if(istwocat){
			$('#s'+pid).html('<div class="minTit">'+data[0].name+'</div><ul>'+html2.join('')+'</ul><div class="minTit">'+data[1].name+'</div><ul>'+html3.join('')+'</ul>');
		}else{
			if(isweitiao){
				$('#s'+pid).html('<div class="minTit">'+data[0].name+'</div><ul>'+html.join('')+'</ul>');	
			}else{
				$('#s'+pid).html('<ul>'+html.join('')+'</ul>');
			}
		}
	}

	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			//alert(JSON.stringify(data))
			addHtml(data.imges)
		},
		error:function(){
			alert('款式数据error')
		}
	});
}

//款式切换
Custom.prototype.selectStyle=function(){
	//款式二级分类
	$('#opt-style .cat2-1').off('click').on('click',' li',function(){
		if($(this).hasClass('null')){return}//站位li标签
		var n=$(this).index();
		$(this).siblings('li').removeClass('cur').end().addClass('cur');
		
		$('#opt-style .itemList>div').eq(n).siblings().hide().end().show();
		//if($(this).attr('flag')!=1){ //袖口设计影响袖头型所以暂时放开数据缓存
			_diy.getStyleData(10,this.getAttribute('data-id'));
			//$(this).attr('flag',1);
		//}
	});
	var flag=true
	//款式数据选择
	$('#opt-style .itemList').off('click').on('click','ul>li',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
		var pid=this.getAttribute('data-pid'),
			id=this.getAttribute('data-id'),
			str=pid+':'+id,
			flag=null,
			n=0,
			arr=_diy.param.strData[_diy.param.clothing].styles,
			len=arr.length;
			
		//如果有微调则调取数据
		if(_diy.styleType.weitiao.join().indexOf($('#opt-style .cat2-1 .cur').attr("data-id"))>=0){
			_diy.getStyleData3(id,$('#opt-style .cat2-1 .cur').attr("data-id"),pid);
		}
		
		//同步全局工艺变量
		for(var i=0;i<len;i++){
			(arr[i].indexOf(pid+':')>=0) && (flag=true,n=i)
		}
		flag?(arr[n]=str,flag=null):(arr.push(str));
		//alert(arr.join())
		
		//衬衣-细节-肩袖设计-特殊处理（自动更新袖头型默认值）
		if(pid==3027){
			$.ajax({
				url:_diy.api+'simgs/all',
				data:{
					cache:_diy.cache,
					style:_diy.style,
					parentID:3093,
					cstr:_diy.cstr(), //工艺
					clothing:_diy.param.clothing,
					'access-token':_diy['access-token']	
				},
				dataType:"jsonp",
				success:function(data){
					console.log(data);
					$.each(data.imges,function(i,v){
						if(v.selected){
							var pid=v.parentid,
							id=v.id,
							str=pid+':'+id,
							flag=null,
							n=0,
							arr=_diy.param.strData[_diy.param.clothing].styles,
							len=arr.length;
							//同步全局工艺变量
							for(var i=0;i<len;i++){
								(arr[i].indexOf(pid+':')>=0) && (flag=true,n=i)
							}
							flag?(arr[n]=str,flag=null):(arr.push(str));
						}	
					})
				},
				error:function(){
					alert('款式数据error')
				}
			});
		}
		//更新diy视图
		_diy.getDiyData2({
			pid:pid,
			ids:id,
			mid:$('#opt-style .cat2-1 .cur').attr('data-id')
		});
	});
	//切换微调
	$('#opt-style').off('click').on('click','.itemList .weitiao span',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
		var pid=this.getAttribute('data-pid'),
			id=this.getAttribute('data-id'),
			str=pid+':'+id,
			flag=null,
			n=0,
			arr=_diy.param.strData[_diy.param.clothing].styles,
			len=arr.length;
		
		//同步全局工艺变量
		for(var i=0;i<len;i++){
			(arr[i].indexOf(pid+':')>=0) && (flag=true,n=i)
		}
		flag?(arr[n]=str,flag=null):(arr.push(str));
		//alert(arr.join())
	})
}

/*======================================*
				细节 end
*=======================================*/







/*======================================*
				签名 start
*=======================================*/

Custom.prototype.getCxColorFont=function(pid){
	function addHtml(d){
		var fontHtml=[],colorHtml=[],data=d.imges,len=data.length,ids='',fontIndex,colorIndex;
		//alert(JSON.stringify(data))
		for(var g=0;g<2;g++){
			if(data[g].name=='字体'){
				fontIndex=g
			}
			if(data[g].name=='颜色'){
				colorIndex=g	
			}
		}
		
		for(var i=0;i<len;i++){
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'cixiu',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			if(data[i].parentid==data[fontIndex].id){//字体
				fontHtml.push('<li'+(checkFocus?checkFocus:'')+' data-id='+data[i].id+' data-pid='+data[i].parentid+' data-font='+data[i].name+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
				if(checkFocus){
					ids=data[i].id
				}
				
			}
			if(data[i].parentid==data[colorIndex].id){//颜色
				colorHtml.push('<li'+(checkFocus?checkFocus:'')+' data-id='+data[i].id+' data-pid='+data[i].parentid+' data-color='+data[i].name+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'" width="63" height="63"><p>'+data[i].name+'</p></li>');
			}
		}
		$('#cxCon').val(_diy.param.strData[_diy.param.clothing].cixiu[0])
		$('#cxFont').html('<ul>'+fontHtml.join('')+'</ul>');
		$('#cxColor').html('<ul>'+colorHtml.join('')+'</ul>');
		
		//获取签名位置
		_diy.getCxPosition(data[1].id,ids,pid);//字体id、选中字体id、签名id
	}
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data)
		},
		error:function(){
			alert('签名颜色字体error')	
		}
	});
}

Custom.prototype.getCxPosition=function(pid,ids,menuId){//字体id、选中字体id、签名id
	function addHtml(d){
		//alert(JSON.stringify(d))
		var data=d.options,len=data.length,optionHtml=[];
		for(var i=0;i<len;i++){
			if(data[i].level!=1){
				//设置默认值
				var checkFocus=_diy.checkDefault({
					type:'cixiu',
					pid:data[i].parentid,
					id:data[i].id,
					selected:data[i].selected
				});

				optionHtml.push('<p'+(checkFocus?checkFocus:'')+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'" data-position="'+data[i].name+'"><span></span>'+data[i].name+'</p>');
			}
		}
		$('#cxPosition').html(optionHtml.join(''));
		
		//绑定签名操作事件
		_diy.setCixiu();
	}
	
	//数据请求
	$.ajax({
		url:_diy.api+'selects/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			clothingID:_diy.param.clothing,
			parentID:pid,
			ids:ids,
			menuId:menuId,
			fabricCode:_diy.param.strData[_diy.param.clothing].fabric,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data)
		},
		error:function(){
			alert('签名位置数据error')	
		}
	});
}

//签名
Custom.prototype.setCixiu=function(){
	var $con=$('#cxCon'),$font=$('#cxFont'),$color=$('#cxColor'),$position=$('#cxPosition');
	//选择签名字体
	$font.off('click').on('click','li',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
	})
	//选择签名位置
	$position.off('click').on('click','p',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
	})
	//选择签名颜色
	$color.off('click').on('click','li',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
	});
	/**  
	* 中英文统计(一个中文算两个字符)  
	*/  
	function chEnWordCount(str){  
		var count = str.replace(/[^\x00-\xff]/g,"**").length;  
		return count;  
	} 
	//保存签名信息
	$('#opt-embroidery .titBar .save').unbind('click').click(function(){
		var oPosition=$position.children('.cur')
			oFont=$font.find('.cur'),
			oColor=$color.find('.cur');
		if($con.val()==''&&oPosition.length<=0){
			return
		}
		if($.trim($con.val())==''||oPosition.length<=0){
			luck.open({content:'签名信息填写不完整',time:1.5});
			return
		}
		if(chEnWordCount($con.val())>14){
			luck.open({content:'签名信息不能超过14个字符',time:1.5});
			return	
		}
		//同步数据串
		var arr=[]
		//arr.push(html);
		arr.push($con.val());
		arr.push(oFont.attr('data-pid')+':'+oFont.attr('data-id'));
		arr.push(oColor.attr('data-pid')+':'+oColor.attr('data-id'));
		oPosition.each(function(i, e) {
            arr.push(e.getAttribute('data-pid')+':'+e.getAttribute('data-id'))
        });
		_diy.param.strData[_diy.param.clothing].cixiu=arr;
		//alert(arr.join(','))
		luck.open({content:'签名信息保存成功',time:1.5});
		setTimeout(function(){
			$('#cat2').hide();
			$('#cat1').show();
		},200);

	});
	//删除签名信息
	$('#opt-embroidery .titBar .del').unbind('click').click(function(){
		$('#cxCon').val('');
		$('#cxFont .cur,#cxPosition .cur,#cxColor .cur').removeClass('cur');
		_diy.param.strData[_diy.param.clothing].cixiu=[];
		setTimeout(function(){
			$('#cat2').hide();
			$('#cat1').show();
		},200)
	})
}



/*======================================*
				签名 end
*=======================================*/







/*======================================*
				锁眼线色 start
*=======================================*/

//锁眼线数据
Custom.prototype.getSyLine=function(){
	var obj=$('#opt-keyhole .itemList');
	//组织数据
	function addHtml(data){
		var html=[],len=data.length;
		for(var i=0;i<len;i++){
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'syLine',
				pid:$('#opt-keyhole .cat2-1 .current').attr('data-id')+'-'+data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			if(data[i].imgurl){
				if(data[i].name!='无'&&data[i].id!=36182&&data[i].id!=62240){//临时过滤
					html.push('<li'+(checkFocus?checkFocus:'')+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
				}
			}
		}
		obj.html('<ul>'+html.join('')+'</ul>');
		//绑定锁眼线切换事件
		_diy.selectSyLine();
	}
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:$('#nav-keyhole').attr('data-id'),
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data.imges)
		},
		error:function(){
			alert('锁眼线位置数据error')	
		}
	});
}


//锁眼色切换
Custom.prototype.selectSyLine=function(){
	$('#opt-keyhole .itemList').off('click').on('click','li',function(){
		$(this).addClass('cur').siblings().removeClass('cur');
		//同步全局工艺变量
		var pid=this.getAttribute('data-pid'),id=this.getAttribute('data-id'),mid=$('#nav-keyhole').attr('data-id'), str=pid+':'+id;
		_diy.param.strData[_diy.param.clothing].syLine[0]=str;
		//获取数据
		_diy.getDiyData2({
			pid:pid,
			ids:id,
			mid:mid	
		});
	});
}

/*======================================*
				锁眼线色 end
*=======================================*/


/*======================================*
				钉扣线 start
*=======================================*/

Custom.prototype.getbutLine=function(n,pid){
	var data=null,page=0,obj=$('#opt-studs .itemList');
	//组织数据
	function addHtml(start,end){
		var html=[];
		for(var i=start;i<end;i++){
			if(i==0&&page==0){
				html.push('<ul>')	
			}
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'studs',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			if(data[i].imgurl&&data[i].name!='无'){//数据有问题临时过滤
				html.push('<li'+(checkFocus?checkFocus:'')+' data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'"><img src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></li>');
			}
			if(i==page+n-1&&page==0){
				html.push('</ul>');
			}
		}
		if(page==0){
			obj.html(html.join(''));
		}else{
			obj.children('ul').append(html.join(''));
		}
		page=end;
	}
	//分页处理
	function ajaxPage(d){
		data=d.imges;//同步请求数据到上层作用域
		var len=data.length,obj=$('#opt-studs .scrollBox');
		addHtml(page,len>n?n:len)//显示第一页数据
		//无限加载
		var h=obj.height(),
			delay=null,
			advance=20;//提前多少px开始加载
		obj.scroll(function(){
			if(delay){clearTimeout(delay)}
			delay=setTimeout(function(){
				var scr=obj[0].scrollTop;
				if(scr>=(obj[0].scrollHeight-h-advance)){
					if(len-page>0){
						var end=(len-page>=n)?page+n:len
						addHtml(page,end)
					}else{
						oUl=obj.find('.itemList ul');
						//oUl.append('<li class="tip">加载完毕</li>')	
						obj.unbind('scroll');
					}
				}
			},120)
		})
	}
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			ajaxPage(data)
		},
		error:function(){
			alert('钉扣线error')	
		}
	});
	//绑定钉扣线切换事件
	_diy.selectBtnLine();
}
//钉扣线切换
Custom.prototype.selectBtnLine=function(){
	var flag=true;
	function fn(){
		if(flag){
			$(this).addClass('cur').siblings().removeClass('cur');
			var pid=this.getAttribute('data-pid'),
				id=this.getAttribute('data-id'),
				mid=$('#nav-studs').attr('data-id');
			//同步全局工艺变量
			_diy.param.strData[_diy.param.clothing].studs[0]=pid+':'+id;
			//获取数据
			_diy.getDiyData2({
				pid:pid,
				ids:id,
				mid:mid	
			});
			flag=false;
			setTimeout(function(){
				flag=true	
			},_diy.rateGet);
		}
	}
	$('#opt-studs').off('click').on('click','li',fn)	
}

/*======================================*
				钉扣线 end
*=======================================*/


/*======================================*
				工艺 start
*=======================================*/

Custom.prototype.getCrafts=function(pid){
	var html1=[],html2=[],obj=$('#opt-process .itemList');
	//组织数据
	function addHtml(data){
		var len=data.length;
		for(var i=0;i<len;i++){
			//设置默认值
			var checkFocus=_diy.checkDefault({
				type:'process',
				pid:data[i].parentid,
				id:data[i].id,
				selected:data[i].selected
			});
			if(data[0].level==1&&!data[0].imgurl){//如果级别为1并且没有图片就认定为是带标题的
				if(i==0){
					html1.push('<div class="minTit">'+data[i].name+'</div><ul>');
				}
				if(i==1){
					html2.push('<div class="minTit">'+data[i].name+'</div><ul>');
				}
				if(data[i].parentid==data[0].id){//手工艺
					html1.push('<li'+(checkFocus?checkFocus:'')+' data-id='+data[i].id+' data-pid='+data[i].parentid+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'"><span class="tit">'+data[i].name+'</span></li>');
				}
				if(data[i].parentid==data[1].id){//衬类型
					html2.push('<li'+(checkFocus?checkFocus:'')+' data-id='+data[i].id+' data-pid='+data[i].parentid+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'"><span class="tit">'+data[i].name+'</span></li>');
				}
				if(i==len-1){
					html1.push('</ul>');
					html2.push('</ul>');
				}
			}else{
				html1.push('<li'+(checkFocus?checkFocus:'')+' data-id='+data[i].id+' data-pid='+data[i].parentid+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'"><span class="tit">'+data[i].name+'</span></li>');
			}
		}
		if(data[0].level==1&&!data[0].imgurl){
			obj.html(html1.join(''));
			obj.append(html2.join(''));
		}else{
			obj.html('<ul>'+html1.join('')+'</ul>');	
		}
	}
	
	//数据请求
	$.ajax({
		url:_diy.api+'simgs/all',
		data:{
			cache:_diy.cache,
			style:_diy.style,
			parentID:pid,
		    cstr:_diy.cstr(), //工艺
			clothing:_diy.param.clothing,
			'access-token':_diy['access-token']	
		},
		dataType:"jsonp",
		success:function(data){
			addHtml(data.imges)
		},
		error:function(){
			alert('工艺error')	
		}
	});
	//绑定工艺事件
	_diy.selectCrafts();
}
Custom.prototype.selectCrafts=function(){
	var flag=true;
	function fn(){
		if(flag){
			$(this).addClass('cur').siblings().removeClass('cur');
			//同步全局工艺变量
			var pid=this.getAttribute('data-pid'),
				id=this.getAttribute('data-id'),
				mid=$('#nav-process').attr('data-id'),
				str=pid+':'+id,
				b=null,
				n=0,
				arr=_diy.param.strData[_diy.param.clothing].process,
				len=arr.length;
			for(var i=0;i<len;i++){
				(arr[i].indexOf(pid+':')>=0) && (b=true,n=i)
			}
			b?(arr[n]=str,b=null):(arr.push(str));

			//获取数据
			_diy.getDiyData2({
				pid:pid,
				ids:id,
				mid:mid	
			});
			//更新价格
			_diy.getPrice()
			flag=false;
			setTimeout(function(){
				flag=true	
			},_diy.rateGet);
		}
	}
	$('#opt-process').off('click').on('click','li',fn)	
}
/*======================================*
				工艺 end
*=======================================*/



/*======================================*
				旋转 start
*=======================================*/
//模特旋转
Custom.prototype.rotate=function(){
var obj=_diy.R('showArea'),startX,delay=null;
	obj.ontouchstart=function(e){
		//e.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等  
		var touch = e.touches[0]; //获取第一个触点  
		/*var t=''
		for(n in touch){
			t+=n+':'+touch[n]+'\n'
		}
		alert(t)*/
		var x = Number(touch.pageX); //页面触点X坐标  
		//记录触点初始位置  
		startX = x;  
	}
	obj.ontouchmove=function(e){		
		//e.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等
		var touch = e.touches[0]; //获取第一个触点
		var x = Number(touch.pageX); 
		var Left=x - startX
		var n=10//防止误滑动值
		//判断滑动方向
		if(delay){clearTimeout(delay)}
		delay=setTimeout(function(){
			if (Left>n) { //向右滑动
				switch (_diy.angle) {
					  case "a":
					    _diy.angle="b"
						_diy.getRotateData(10013)
						break;
					  case "b":
					    _diy.angle='c'
						_diy.getRotateData(10005)
						break;
					  case "c":
					    _diy.angle='a'
						_diy.getRotateData(10004)
						break;
				}
			}
			if (Left<-n) { //向左滑动
				switch (_diy.angle) {
					  case "a":
					    _diy.angle="c"
						_diy.getRotateData(10005)
						break;
					  case "c":
					    _diy.angle='b'
						_diy.getRotateData(10013)
						break;
					  case "b":
					    _diy.angle='a'
						_diy.getRotateData(10004)
						break;
				}
			}
		},120)
	}	
}

/*======================================*
				旋转 end
*=======================================*/


/*======================================*
			尺码表 start
*=======================================*/

Custom.prototype.getSizeTable=function(data){
	var i,len=data.length,tab=[],layer=[];
	for(i=0;i<len;i++){
		tab.push('<li data-id="'+data[i].nav.id+'">'+data[i].nav.name+'</li>');
		layer.push('<div class="itemList'+(i>0?' hide':'')+'"><div class="loading">加载中...</div></div>');
		getSize(data[i].nav.id,i);
	}
	//tab
	$('#sizeTable .tab').html(tab.join('')).children('li').click(function(){
		var n=$(this).index();
		$(this).siblings().removeClass('cur').end().addClass('cur');
		$('#sizeTable .itemList').eq(n).siblings().hide().end().show();
	}).eq(0).trigger('click');
	
	//layer
	$('#sizeTable .scrollBox').html(layer.join('')).on('click','li',function(){
		$(this).addClass('cur').siblings('.cur').removeClass('cur');
		//更新尺码
		_diy.param.strData[$('#sizeTable .tab .cur').attr('data-id')].sizes=$(this).attr('data-id');
	})	

	//组织数据
	function addHtml(data,n){
		var i,len=data.length,size=[];
		for(i=0;i<len;i++){
			size.push('<li data-id="'+data[i].Id+'">'+data[i].Name+'</li>');
			/*if(data[i].Dv){ //取消默认尺码
				_diy.param.strData[$('#sizeTable .tab li').eq(n).attr('data-id')].sizes=data[i].Id;
			}*/
		}
		$('#sizeTable .itemList').eq(n).html('<ul>'+size.join('')+'</ul>')
	}
	
	//获取
	function getSize(id,n){
		$.ajax({
			url:_diy.api+'sizes/qsizeinfo',
			data:{
				cache:_diy.cache,
				clothingID:id,
				'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success:function(data){
				addHtml(data,n);
			},
			error:function(){
				alert('获取尺码error')	
			}
		});
	}
	//保存尺码
	$('#sizeTable .save').click(function(){
		var flag=true,n=0;
		$('#sizeTable .itemList').each(function(index, element) {
            if($(this).find('.cur').length<=0){
				flag=false;
				n=index;
				return flag;
			}
        });
		if(flag){
			_diy.underOrder()	
		}else{
			luck.open({content:'请选择'+$('#sizeTable .tabBar li').eq(n).text()+'尺码',time:1.5})
		}
	})
}

/*======================================*
			尺码表 end
*=======================================*/


/*======================================*
				下单 start
*=======================================*/
Custom.prototype.order=function(){
	$('#orderDown').click(function(){
		$('#sizeTable').addClass('show');
		$('#zhezhaoLayer').unbind('click').click(function(){
			$('#sizeTable').removeClass('show');
			$(this).fadeOut('fast');	
		}).fadeIn('fast');
	});
	$('#sizeTable .step1 .base').click(function(){
		$('#sizeTable .step1').hide();
		$('#sizeTable .step2').show();
	});
	$('#sizeTable .step1 .amount').click(function(){
		var data=_diy.param.strData
		for(var n in data){
			data[n].sizes='diy'
		}
		_diy.underOrder()	
	});
	$('#sizeTable .step1 .tit').click(function(){
		$('#sizeTable').removeClass('show');
		$('#zhezhaoLayer').fadeOut('fast');		
	});
	$('#sizeTable .step2 .tit').click(function(){
		$('#sizeTable .step2').hide();
		$('#sizeTable .step1').show();
	});
}

Custom.prototype.underOrder=function(){
	if($uid==0){//未登录操作，与app对接跳转登录页面
		$.setCookie('diyData',JSON.stringify(_diy.param.strData),1);
		window.location.href="ios:login";
		app.login()
		return	
	}	
	_diy.loading.show();
	$('#zhezhaoLayer').css('z-index',110);
	_diy.generatePic(function(n){
		_diy.param.strData[_diy.categoryId[n]].image=_diy.diyData[_diy.categoryId[n]];
		if(n ==_diy.categoryId.length-1){
			$.ajax({
				url:_diy.cartUrl+'cart-add.html?token='+_diy.pwd,
				type:'POST',
				dataType:"json",
				data:{
					cache:_diy.cache,
					type:'diy',
					params:JSON.stringify(_diy.param.strData)
				},
				success:function(data){
					if(data.done){
						window.location.href=_diy.cartUrl+'cart.html?token='+_diy.pwd
					}else{
						luck.open({content:data.msg,time:1.5});
						_diy.loading.hide();
					}
				},
				error:function(){
					luck.open({content: '订单提交失败',time:1.5});
				}
			});	
		}	
	})
}

/*======================================*
				下单 end
*=======================================*/


/*======================================*
				生成封面图 start
*=======================================*/

Custom.prototype.generatePic=function(fn){
	var id=_diy.categoryId,i=0,len=id.length,imgData=[];
	function getBaseData(n){
		if(_diy.diyData[id[n]]){//从缓存读数据
			fn(i)
			if(i<len-1){
				i++
				setTimeout(function(){
					getBaseData(i);
				},_diy.rateGet)
			}
			return	
		}
		$.ajax({//服务器取数据
			url:_diy.api+'inits/all',
			data:{
				cache:_diy.cache,
				clothingID:id[n],
				style:_diy.style,
				'access-token':_diy['access-token'],
			},
			dataType:"jsonp",
			success:function(data){
				_diy.diyData[_diy.categoryId[i]]=data[0]
				fn(i)
				if(i<len-1){
					i++
					setTimeout(function(){
						getBaseData(i);
					},_diy.rateGet)
				}
			},
			error:function(){
				luck.open({content: '获取数据失败',time:1.5});
			}
		})
	}
	getBaseData(0);
}


/*======================================*
				生成封面图 end
*=======================================*/


/*======================================*
			模拟alert start
*=======================================*/
Custom.prototype.alert=function(a){
	"use strict"
	var c=document,delay=null;
	//视图
	function view(options){
		var style=options.style,
			content='<div class="luck-con"'+(style?' style="'+style+'"':'')+'>'+options.content+'</div>',
			title=options.title,
			tit='';
			if(title){
				tit='<h3 class="luck-title"'+(title[1]?' style="'+title[1]+'"':"")+'>'+title[0]+'<button id="luck-close">×</button></h3>';	
			}
		return '<div class="luck-table"><div class="luck-table-cell"><div class="luck-child">'+tit+content+'</div></div></div><div id="luck-shade" class="luck-shade"></div>'	
	}
	//主程序
	a.luck={
		open:function(options){
			var tim=options.time,
				d = c.createElement('div');
				d.id='luck';
				d.innerHTML=view(options),
			c.body.appendChild(d);
			c.getElementById('luck-shade').onclick=function(){
				luck.close(d);
			}
			if(tim){
				delay=setTimeout(function(){
					luck.close(d);	
				},tim*1000)	
			}
		},
		close:function(obj){
			if(delay){
				clearTimeout(delay)	
			}
			c.body.removeChild(obj)	
		}
	}
}
/*======================================*
			模拟alert end
*=======================================*/


/*======================================*
			输入框 start
*=======================================*/

/*Custom.prototype.prompt=function(options){
	var ran=Math.round(Math.random()*100),
	    html='<div id="pop'+ran+'" class="prompt">\
				<p class="tit">'+options.tit+'</p>\
				<textarea placeholder="'+options.tip+'"></textarea>\
				<div class="layerbtn"><span class="ok">确定</span><span class="no">取消</span></div>\
			  </div>';
	$('.main').append(html);
	var obj=$('#pop'+ran),text=obj.find('textarea');
	setTimeout(function(){
		obj.addClass('show');	
	},10)
	function closeFn(){
		obj.removeClass('show');
		$('#zhezhaoLayer').fadeOut('fast');
		setTimeout(function(){
			obj.remove()	
		},300)
	}
	$('#zhezhaoLayer').unbind('click').fadeIn('fast').click(function(){
		closeFn();
	});
	obj.find('.ok').unbind('click').click(function(){
		if($.trim(text.val())==''){
			luck.open({content:options.tip,time:1.5});
			return	
		}
		if(options.ok){
			options.ok(text.val());
		}
		closeFn()
	});
	obj.find('.no').unbind('click').click(function(){
		if(options.no){
			options.no(text.val());
		}
		closeFn()
	})
}*/
/*======================================*
			输入框 end
*=======================================*/


/*======================================*
				保存 start
*=======================================*/

//保存作品
/*Custom.prototype.saveWorks=function(){
	function closeLayer(){
		$('#zhezhaoLayer').fadeOut('fast');
		$('#rightLayer').removeClass('show')	
	}
	$('#saveWorks').click(function(){
		var imgdata=_diy.diyData[_diy.param.clothing];
		_diy.prompt({
			tit:'作品将保存到会员中心作品管理，如有需要您还可以对它进行补充设计',
			tip:'请输入作品设计理念',
			ok:function(txt){
				save(imgdata,txt)	
			}
		})
	});
	function save(img,txt){
		var data={}
		data.sysprocess=_diy.param.strData;
		data.cnt=$('#price').text();
		//alert(JSON.stringify(data))
		$.ajax({
			url:_diy.api+"inits/sdiy?access-token="+_diy['access-token'],
			type:'POST',
			data:{
				//cache:_diy.cache,
				ustr:_diy.pwd,
				code:JSON.stringify(data),
				img:img,
				clothingID:_diy.clothing,
				describe:txt
			},
			success:function(data){
				closeLayer();
				luck.open({content:'保存成功',time:1.5});
			},
			error:function(){
				closeLayer();
				luck.open({content:'保存失败',time:1.5});
			}
		});
	}
}*/

/*======================================*
				保存 end
*=======================================*/


/*======================================*
				分享 start
*=======================================*/
/*Custom.prototype.share=function(){
	$('.head .share').click(function(){
		$('#zhezhaoLayer').unbind('click').show().click(function(){
			$('#shareBox').removeClass('show');
			$('#zhezhaoLayer').hide();
		})
		$('.shareBox').addClass('show');
	});
	$('.shareBox .close').click(function(){
		$('#shareBox').removeClass('show');
		$('#zhezhaoLayer').hide();	
	})
	
	$('#shareBox').on('click','a',function(){
		var type=this.getAttribute('data-type'),
			url='',
			title=document.title,
			href=window.location.href;
//		if(cla=='wx'){
//			alert(234)
//			layer.open({
//				title: '分享到微信朋友圈',
//				content:'<div style="padding-left:22px;font-size:12px;text-align:center;color:#666"><img src="http://qr.liantu.com/api.php?text='+href+'" alt="微信二维码" width="260" height="260">使用微信“扫一扫”即可将网页分享到我的朋友圈</div>'
//			});	
//			return
//		}
		switch(type){
			case 'sina':
				url="http://v.t.sina.com.cn/share/share.php?appkey=3273986941&title="+title+"&url="+href;
				break;
			case 'qzone':
				url="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title="+title+"&url="+href;
				break;
		}
		window.open(url,"","height=500, width=600");
		return false
	})
}*/
/*======================================*
				分享 end
*=======================================*/




