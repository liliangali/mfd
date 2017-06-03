/*
 @ name：产品DIY
 @ author：前端老徐
 @ email:442413729@qq.com
 @ date：2015/7/13
 @ 最终提交数据: 
 @ _diy.param.strData:{
	 0003:{
		cstr:[pid:id,pid:id...],
		embroidery:['刺绣内容',pid:id,pid:id...],
		fabric:SDDFDF, 
		sizes:"165/84",
		image:'a.png'
     },
	 0004:{...}
 }
*/
function nofind(obj,id){
	if(id&&_diy.smallPic[id]){
		obj.src=_diy.smallPic[id];
	}else{
		obj.src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OUFBMEQ0RjcyRUIxMTFFNTkzQjI4QzhBNzZEMUI1QzkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OUFBMEQ0RjgyRUIxMTFFNTkzQjI4QzhBNzZEMUI1QzkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5QUEwRDRGNTJFQjExMUU1OTNCMjhDOEE3NkQxQjVDOSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5QUEwRDRGNjJFQjExMUU1OTNCMjhDOEE3NkQxQjVDOSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	}
	obj.onerror=null;//控制不要一直跳动
} 
function Custom(options){
	this.showArea=$('#'+options.showArea);//展示区
	this.categoryId=[];//保存分类id集合
	this.categoryName=[];//保存分类名称
	this.diyData={};//当前diy大图url
	this.smallPic={};//保存指定锁眼颜色小图
	this.worksID=null;//作品ID-url获取
	this.addressforShare=false;//判断是否是分享过来的连接
	this.uf=null;//0:未完成首单体验款 1:是-异步获取
	this.addCode=null//在集合内的id都需要拼接code-异步获取
	this.checkDomain=function(){
		var url=window.location.href;
		if(url.indexOf('wap.cotte.cn')>=0){
			return 1 //线上环境
		}else if(url.indexOf('test.cotte.cn')>=0){
			return 2 //测试环境
		}else{
			return 3 //开发环境
		}
	};
	this.api=function(){
		switch(this.checkDomain()){
			case 1:
				return 'http://api.diy.ls.wap.cotte.cn/v3/';//线上环境	
			break;
			case 2:
				return 'http://api.diy.ls.wap.cotte.cn/v3/';//测试环境
			break;
			case 3:
				return 'http://api.diy.ls.wap.cotte.cn/v3/';//开发环境
		}
	}
	this.log=true;//log开关
	this.cache=this.checkDomain()==1?"":1;//控制ajax全局缓存
	this.csql=this.checkDomain()==1?"":1;
	this['access-token']='101-token';
	this.pwd=null//加密串 url截取
	this.style=null//风格 url截取
	this.clothing=null,//定义初始的id
	this.param={
		clothing:null,//当前diy的clothing
		strData:{},//diy工艺数据
	}	
}

Custom.prototype={
/*========================*
		  初始化
*=========================*/
	init:function(data){
		_diy=this;
		if(window.location.href.indexOf('custom-diy-')>=0){
			//截取clothing
			var id=window.location.href.split('custom-diy-')[1].split('-')
			_diy.clothing=id[0];
			_diy.param.clothing=id[0];
			//截取风格
			_diy.style=id[1];
			try{
				//截取加密串、作品id
				if(id.length==3){
					if($utk!='cotte'){
						_diy.pwd=$utk
					}else{
						_diy.pwd=id[2].split('.')[0];
					}
				}else if(id.length>=4){
					if($utk!='cotte'){
						_diy.pwd=$utk
					}else{
						_diy.pwd=id[2];
					}
					_diy.worksID=id[3].split('.')[0];
				}
				//截取是否是分享来源
				if(window.location.href.indexOf('share')>=0){
					_diy.addressforShare=true;
				}
			}
			catch(e){}
		}
		//打印log
		_diy.writeLog('加密串',_diy.pwd);
		_diy.writeLog('作品id',_diy.worksID);
		_diy.writeLog('来源for分享',_diy.addressforShare);

		//视图大小控制
		window.onresize=delayFn;
		var delay=null;
		function delayFn(){
			if(delay){clearTimeout(delay)}
			delay=setTimeout(setShowAreaH,120)	
		}
		function setShowAreaH(){
			var winHeight=$(window).height(),num=winHeight-296;
			$('.main').height($(window).height()).addClass('show');
			$('#showArea').css({height:num});
			//$('.sizeTable .scrollBox').height(winHeight-143);
			//$('.sizeReferTable .scrollBox').height(winHeight-50);
		}
		setShowAreaH();
		
		//获取初始化数据
		_diy.loading.show(1);
		_diy.getDefault(_diy.getInitData);
		//下单按钮
		_diy.order();
		//保存
		_diy.saveWorks();
		//分享
		_diy.diyShare();
	},
	
	
	
/*========================*
	    渲染数据 
*=========================*/
	newUpData:function(url,rotate){
		var img=new Image;
		img.src=url;
		img.onload=function(){
			_diy.showArea[0].innerHTML='';
			_diy.showArea[0].appendChild(img);
			_diy.loading.hide();//关闭loading
			//同步diy数据
			if(!rotate){//如果不是旋转则更新数据，防止保存、下单生成侧身图片
				_diy.diyData[_diy.param.clothing]=url;
			}
		}	
	},
	/*收集工艺参数*/
	cstr:function(){
		var arr=[];
		$.each(_diy.param.strData[_diy.param.clothing],function(name,value){
			if(name=='cstr'){
				$.each(value,function(index,value){
					arr.push(value);
				})
			}
		});
		//console.log(arr)
		return arr.join('|');
	},
	//切换面料DIY视图
	getDiyData:function(){
		_diy.loading.show();
		//发送请求
		$.ajax({
			url:_diy.api()+'inits/display',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
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
	},
	//切换工艺DIY视图（公共）
	getDiyData2:function(obj){
		_diy.loading.show();
		//发送请求
		$.ajax({
			url:_diy.api()+'selects/all',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
				 style:_diy.style,
				 clothing:_diy.param.clothing,
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
	},	
	
/*============================*
		   获取价格 
*=============================*/
	getPrice:function(){
			var arr=[];//0003:DBM584A|0004:DBM584A|...
			for(n in _diy.param.strData){
				var data=_diy.param.strData[n],str=[n];
				str.push(':');
				str.push(data.fabric);
				arr.push(str.join(''))
			}
			$.ajax({
				url:_diy.api()+'inits/pcnt',
				data:{
					cache:1,
					csql:1,
					clothingID:_diy.param.clothing,
					codestr:arr.join('|'),
					'access-token':_diy['access-token'],
					token:_diy.pwd
				},
				dataType:"jsonp",
				success:function(data){
					//alert(JSON.stringify(data))
					$('#price').html(data.price);
					//定义是否首单
					if(!_diy.uf){
						_diy.uf=data.u_f;
					}
				},
				error:function(){
					alert('获取价格失败')	
				}
			});
	},
	

		
/*========================*
		获取初始化数据 
*=========================*/
	getInitData:function(){
		function setInitData(d){
			var data=d.data,bigNav=[],len=data.length,flag=true,isWorks=_diy.worksID&&d.sysprocess;
			
			//设置作品默认值
			if(isWorks){
				_diy.param.strData=d.sysprocess;
				_diy.writeLog('作品数据',d.sysprocess);
			}
			
			//获取价格
			_diy.getPrice();
			
			//初始化数据处理
			if(len>1){
				for(var i=0;i<len;i++){
					bigNav.push('<span data-id="'+data[i].nav.i+'">'+data[i].nav.n+'</span>');
					_diy.categoryId.push(data[i].nav.i);
					_diy.categoryName.push(data[i].nav.n);
				}
				$('#tzBtnBox').html(bigNav.join('')).css('top',($('#showArea').height()-$('#tzBtnBox').height())/2+30);
				$('.tzBtnBox span').click(function(){
					$(this).addClass('cur').siblings('.cur').removeClass('cur');
					_diy.param.clothing=this.getAttribute('data-id');
					upData($(this).index());
				}).eq(0).trigger('click');
			}else{
				_diy.categoryName.push(data[0].nav.n);
				$('#tzBtnBox').hide();
				_diy.param.clothing=data[0].nav.i;
				_diy.categoryId.push(data[0].nav.i);
				upData(0);
			}
			
			//大导航点击处理
			function upData(g){
				_diy.getDiyData();
				var smallNav=[],opt=[];
				for(var n in data[g]){
					if(n!='nav'){
						smallNav.push('<li class="nav-'+n+data[g].nav.i+" nav-"+n+'" id="nav-'+n+'" data-id="'+data[g][n].i+'"><i class="nav-ico"></i>'+data[g][n].n+'</li>');
						opt.push(_diy.setOptHtml(n,data[g][n]));
					}
				}
				//更新组件模版
				var nn=8-smallNav.length
				for(var j=0;j<nn;j++){
					smallNav.push('<li class="null"></li>');
						
				}
				$('#cat1').html(smallNav.join('')).show();
				$('#cat2').html(opt.join('')).hide();
				//绑定组件切换事件
				_diy.selectCat();
			}
		}

		$.ajax({
			url:_diy.api()+'inits/nav',
			data:{
				cache:1,
				clothingID:_diy.clothing,
				style:_diy.style,
				worksID:_diy.worksID,
				'access-token':_diy['access-token'],
				token:_diy.pwd,
				share:_diy.addressforShare?1:''
			},
			dataType:"jsonp",
			success:function(data){
				setInitData(data);
			},
			error:function(){
				alert('获取初始化数据错误')
			}
		});
	},

	//组件选项模版
	setOptHtml:function(name,obj){
		function setHtml(){
			var str='<div id="opt-'+name+'">\
					<div class="titBar"><span class="tit fl js-back">'+obj.n+'</span></div>\
					<div class="scrollBox">\
						<div class="subCat">\
							<ul class="cat2-1" data-type="'+name+'">';
							for(var i=0,len=obj.l.length;i<len;i++){
								str+='<li'+(obj.l[i].rt?" data-rt='1'":"")+' data-pid="'+obj.l[i].p+'" data-api="'+obj.l[i].u+'">'+obj.l[i].n+'</li>';	
							}
							/*if(len%4!=0){
								for(var g=0;g<(4-len%4);g++){
									str+='<li class="null">&nbsp;</li>'	
								}
							}*/
				str+=		'</ul>\
							 <div class="cat2-2"></div>\
						</div>\
						<div class="itemList cstrBox">';
							for(var n=0,len2=obj.l.length;n<len2;n++){
								if(n==0){
									str+='<div><div class="loading">加载中...</div></div>';	
								}else{
									str+='<div class="hide"><div class="loading">加载中...</div></div>';	
								}
							}
				str+='</div>\
					</div>\
				</div>';
			return str;	
		}
		
		switch(name){
			case 'fabric':
				return '<div id="opt-fabric">\
							<div class="titBar"><span class="tit fl js-back">'+obj.n+'</span></div>\
							<div class="scrollBox">\
								<div class="subCat">\
									<ul class="cat2-1"></ul>\
									<div class="cat2-2"></div>\
								</div>\
								<div class="itemList">\
									<div class="loading">加载中...</div>\
								</div>\
							</div>\
						</div>';
			break;
			case 'embroidery':
				return '<div id="opt-embroidery">\
							<div class="titBar"><span class="tit fl js-back">'+obj.n+'</span><span class="del fr">取消</span><span class="save fr">保存</span></div>\
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
						</div>'
			break;
			default:
			return setHtml();
		}
	},
	
	
/*========================*
		组件分类切换 
*=========================*/
	
	selectCat:function(){
		//返回事件
		$('#cat2').off('click').on('click','.js-back',function(){
			$('#cat2').hide();
			$('#cat1').show();
		});
		//切换事件
		$('#cat1').off('click').on('click','li',function(){
			if($(this).hasClass('null'))return;
			var id=this.id,flag=this.getAttribute('flag')!=1;
			$('#cat1').hide();
			$('#cat2').show().children('div').eq($(this).index()).siblings().hide().end().show();
			switch(id){
				case 'nav-fabric':
					if(flag){
						_diy.getFabricCat(this.getAttribute('data-id'));
					}
				break;
				case 'nav-embroidery':
					if(flag){
						_diy.getCxColorFont(this.getAttribute('data-id'));
					}
				break
				default:
					var objBox=$('#'+id.replace('nav','opt'));
					if(flag){
						_diy.selectCat2(objBox);
					}
					objBox.find('.cat2-1').children('li').eq(0).click();	
					
			}
			if(flag){
				this.setAttribute('flag',1)
			}
		});
	},
	selectCat2:function(obj){
		//组件二级分类选择
		obj.find('.cat2-1').off('click').on('click','li',function(){
			var _self=this;
			if(_self.className=='null')return;
			$(_self).addClass('cur').siblings().removeClass('cur');
			var box=$(_self).parents('.scrollBox').children('.itemList').children('div').eq($(_self).index()).html('<div class="loading">加载中...</div>'),
				//定义是否为西服或者衬衣的扣线颜色-特殊处理
				kxLineColor=(($(_self).attr('data-pid')==345)&&($(_self).parents('.cat2-1').attr('data-type')=='kx'))||(($(_self).attr('data-pid')==3202)&&($(_self).parents('.cat2-1').attr('data-type')=='kx')),
				//定义是否为西服高端设计里的扣眼颜色-特殊处理
				gdLineColor=(($(_self).attr('data-pid')==200861)&&($(_self).parents('.cat2-1').attr('data-type')=='gd')),
				key=$(_self).parent('.cat2-1').attr('data-type');
			box.siblings().hide().end().show();
			//圆下摆、直下摆接口自带cstr避免重复在此【特殊处理】
			var api=(function(url){
				if(url.indexOf('cstr=')>=0){
					return {u:url.split('cstr=')[0],c:url.split('cstr=')[1]}
				}
				return {u:url,c:''}
			})(_self.getAttribute('data-api'))
			$.ajax({
				url:_diy.api()+api.u,
				dataType:"jsonp",
				data:{
					 cache:_diy.cache,
					 csql:_diy.csql,
					 style:_diy.style,
					 pkey:key,
					 cstr:_diy.cstr()+'|'+api.c, //工艺
					 clothing:_diy.param.clothing,
					 'access-token':_diy['access-token']
				},
				success: function(d){
					var str='',data=d.imges||d.options,checkTit={flag:false,tit:[],html:{}};
					
					//西服-高端工艺-插花眼线色-特殊处理
					if(gdLineColor){
						$.ajax({
							url:_diy.api()+'simgs/line?clothing=0003&access-token=101-token',
							data:{
								clothing:_diy.param.clothing,
								cache:_diy.cache,
								csql:_diy.csql,
								'access-token':_diy['access-token']
							},
							success: function(d){
								$.each(d,function(index,value){
									var pid=data[0].parentid,id=data[0].id+'|||'+value.ecode;
									//设置默认值
									var checkFocus=_diy.checkDefault({
										pid:pid,
										id:id,
										selected:false
									});
									str+='<li'+(checkFocus?checkFocus:"")+' data-pid="'+pid+'" data-id="'+id+'" data-code="'+value.ecode+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></li>'
									
								})
								box.html('<ul>'+str+'</ul>')
							}
						})
						return;	
					}

					//标题处理
					$.each(data,function(index,value){
						if(value.level==2){
							checkTit.flag=true;
						}
						if(value.level==1){
							checkTit.tit.push(value)	
						}
					});
					if(checkTit.flag){
						$.each(data,function(index,value){
							if(value.level==1)return;
							if(!checkTit.html[value.parentid]){
								checkTit.html[value.parentid]=[];	
							}
							checkTit.html[value.parentid].push(value);
						});
						
						//西服、衬衣-扣线设计-扣眼颜色-特殊处理
						if(kxLineColor){
							$.each(checkTit.tit,function(index,value){
								if(index!=0){
									str+='<div class="minTit">'+value.name+'</div>';
								}
								if(index==0){
									str+='<div class="hide"><ul>'
									$.each(checkTit.html[value.id],function(index,value){
										str+='<li data-ecode="'+(value.ecode?value.ecode:'')+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></li>';
									})
									str+='</ul></div>';
								}else if(checkTit.html[value.id]){
									str+='<ul>';
									$.each(checkTit.html[value.id],function(index,value){
										//设置默认值
										var checkFocus=_diy.checkDefault({
											pid:value.parentid,
											id:value.id,
											selected:false
										});
										if(!value.imgurl){
											str+='<li class="'+(checkFocus?"cur ":"")+'null kxSelect" data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="a.jpg" onerror="nofind(this,\''+value.parentid+'_'+value.id+'\')"></li>';
										}else{
											str+='<li class="'+(checkFocus?"cur ":"")+'null kxSelectDel" data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'(顺色)</p></li>'
										}
									});
									str+='</ul>'
								}
							});
							box.html('<div class="kyLineColor">'+str+'</div>');
							$('.kxSelectDel').click(function(){
								var kxSelect=$(this).siblings('.kxSelect');
								$(this).addClass('cur').siblings('.kxSelect').removeClass('cur');
								nofind(kxSelect.children('img')[0]);
								kxSelect.removeAttr('data-code');
								_diy.upDataCstr($(this).attr('data-pid'),$(this).attr('data-id'));
							})
							$('.kxSelect').off('click').on('click',function(){
								var _self=$(this),line=_self.parents('.kyLineColor').find('ul:eq(0)');
								line.find('.cur').removeClass('cur');
								line.find('[data-ecode="'+_self.attr('data-code')+'"]').addClass('cur');
								$('.main').append('<div class="lineLayer"><ul class="popLineColor">'+line.html()+'</ul><button>取消</button></div>');
								$('.lineLayer button').click(function(){
									$('.lineLayer').remove();	
								})
								$(document).off('click').on('click','.popLineColor img',function(){
									var code=$(this).parent().attr('data-ecode');
									_self.children('img').attr('src',this.src).end().attr('data-code',code);
									_self.addClass('cur').siblings().removeClass('cur');
									_diy.upDataCstr(_self.attr('data-pid'),_self.attr('data-id'),code);
									_diy.smallPic[_self.attr('data-pid')+'_'+_self.attr('data-id')]=this.src;//保存小图
									$('.lineLayer').remove();	
								})
							})
							return	
						
						}

						//console.log(html)
						$.each(checkTit.tit,function(index,value){
							str+='<div class="minTit">'+value.name+'</div><ul>';
							if(checkTit.html[value.id]){
								$.each(checkTit.html[value.id],function(index,value){
									//设置默认值
									var checkFocus=_diy.checkDefault({
										pid:value.parentid,
										id:value.id,
										selected:value.selected
									});
									str+='<li'+(checkFocus?checkFocus:'')+' data-pid="'+value.parentid+'" data-id="'+value.id+'" data-code="'+(value.ecode?value.ecode:"")+'"><img onerror="nofind(this)" src="'+value.imgurl+'"><p>'+value.name+'</p></li>'
									//获取微调信息
									if($(_self).attr('data-rt')&&checkFocus){
										_diy.getWeitiao(value.parentid,$(_self).attr('data-pid'),value.id,box);
									}	
								})
							}
							str+='</ul>';
						});
						box.html(str)
					}else{
						checkTit=null;
						$.each(data,function(index,value){
							//alert(JSON.stringify(value))
							//设置默认值
							var checkFocus=_diy.checkDefault({
								pid:value.parentid,
								id:value.id,
								selected:value.selected
							});
							str+='<li'+(checkFocus?checkFocus:'')+' data-pid="'+value.parentid+'" data-id="'+value.id+'" data-code="'+(value.ecode?value.ecode:"")+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></li>'
						})
						box.html('<ul>'+str+'</ul>')	
					}
				},
				error:function(){alert('error')}
			});
		})
		//组件选择
		$('.cstrBox').off('click').on('click','li',function(){
			var _self=this;
			/*
			 * 由于西服的锁眼线色Pid受外观设计里的袖口数量影响，导致更新袖口数量后锁眼线色的默认值不能清空上一个默认值问题处理
			 * 特殊处理/S
			 */
			 if($(_self).attr('data-pid')==189){
				$.each(_diy.param.strData,function(name,value){
					if(name=='0003'){
						$.each(value.cstr,function(index,val){
							if(val){
								var pid=val.split(':')[0];
								if(pid==36185||pid==36187||pid==36190){
									value.cstr.splice(index,1);	
								}
							}
						})	
					}	
				})
			 }
			/*
			 * 特殊处理/E
			 */
			if($(_self).hasClass('kxSelect'))return;//特殊处理
			$(_self).addClass('cur').siblings().removeClass('cur');
			var pid=_self.getAttribute('data-pid'),
				id=_self.getAttribute('data-id'),
				code=_self.getAttribute('data-code');
			//更新cstr
			_diy.upDataCstr(pid,id,code);
			/*
			 * 切换西服的领型、圆下摆直下摆，与驳头宽联动,应用默认的驳头宽
			 * 特殊处理/S
			 */
			 var _pid=$(_self).attr('data-pid')
			 if(_pid==50||_pid==35){
				var _ids=null;
				if(_pid==50){
					_ids=$(_self).attr('data-id');
				}else if(_pid==35){	
					$.each(_diy.param.strData,function(name,value){
						if(name=='0003'){
							$.each(value.cstr,function(index,val){
								if(val){
									var pid=val.split(':')[0];
									if(pid==82){
										value.cstr.splice(index,1);	
									}
									if(pid==50){
										_ids=val.split(':')[1];	
									}
								}
							})
						}
					});
				}
				$.ajax({
					url:_diy.api()+'selects/all',
					data:{
						 cache:_diy.cache,
						 csql:_diy.csql,
						 ids:_ids,
						 menuId:1119,
						 parentID:50,
						 style:_diy.style,
						 cstr:_diy.cstr(),
						 clothing:_diy.param.clothing,
						 'access-token':_diy['access-token']
					},
					dataType:"jsonp",
					success: function(d){
						$.each(d.options,function(i,d){
							if(d.selected){
								_diy.upDataCstr(d.parentid,d.id);
							}	
						})
					}
				});
			 }
			/*
			 * 特殊处理/E
			 */
			//带微调的组件-特殊处理
			var menu=$(_self).parents('.scrollBox').find('.cat2-1').children('.cur'),
				p={
					mid:menu.attr('data-pid'),
					ids:$(_self).attr('data-id'),
					pid:$(_self).attr('data-pid')
				};
			if(menu.attr('data-rt')&&!$(_self).hasClass('null')){
				_diy.getWeitiao(p.pid,p.mid,p.ids,$(_self).parents('div'))	
			}
			//衬衣-细节-肩袖设计-特殊处理（自动更新袖头型默认值）
			if(pid==3027){
				$.ajax({
					url:_diy.api()+'simgs/all',
					data:{
						cache:_diy.cache,
					    csql:_diy.csql,
						style:_diy.style,
						parentID:3093,
						cstr:_diy.cstr(), //工艺
						clothing:_diy.param.clothing,
						'access-token':_diy['access-token']	
					},
					dataType:"jsonp",
					success:function(data){
						$.each(data.imges,function(i,v){
							if(v.selected){
								var pid=v.parentid,
								id=v.id,
								str=pid+':'+id,
								flag=null,
								n=0,
								arr=_diy.param.strData[_diy.param.clothing].cstr,
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
			//禁止切换大图
			if($(_self).hasClass('null'))return;
			//更新diy视图
			_diy.getDiyData2({
				pid:pid,
				ids:id,
				mid:$(_self).parents('.scrollBox').find('.cat2-1').find('.cur').attr('data-pid')
			});
		})
	},
	getWeitiao:function(pid,mid,ids,box){
		$.ajax({
			url:_diy.api()+'selects/all',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
				 ids:ids,
				 menuId:mid,
				 parentID:pid,
				 style:_diy.style,
				 cstr:_diy.cstr(),
				 clothing:_diy.param.clothing,
				 'access-token':_diy['access-token']
			},
			dataType:"jsonp",
			success: function(d){
				//console.log(d);
				var str='',obj=box.find('.weitiao').length>0;
				$.each(d.options,function(index,value){
					//设置默认值
					var checkFocus=_diy.checkDefault({
						pid:value.parentid,
						id:value.id,
						selected:value.selected
					});
					if(index>0){
						str+='<li'+(checkFocus?" class='cur null'":" class='null'")+' data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="a.jpg" onerror="nofind(this)"><p>'+value.name+'</p></li>'
					}
				});
				if(obj){
					box.find('.weitiao').html('<div class="minTit">'+d.options[0].name+'</div><ul>'+str+'</ul>');
				}else{
					box.append('<div class="weitiao"><div class="minTit">'+d.options[0].name+'</div><ul>'+str+'</ul></div>');
				}
			},
			error:function(){}
		})		
	},
	

/*========================*
		获取面料分类
*=========================*/
	getFabricCat:function(pid){
		function addHtml(data){
			var data=data[1],cat=[],len=data.length;
			for(var i=0;i<len;i++){
				//组织面料一级分类
				if(i==0){cat.push('<li id="allFabric" class="cur">全部</li>')}
				cat.push('<li data-pid="'+data[i].parentid+'" data-id="'+data[i].id+'">'+data[i].name+'</li>');
			}
	
			//渲染面料一级分类并绑定事件
			$('#opt-fabric .cat2-1').html(cat.join('')).find('li').unbind('click').click(function(){
				if($(this).hasClass('null'))return;
				var index=$(this).index();
				$(this).addClass('cur').siblings().removeClass('cur');
				if(index==0){
					 $('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
					 _diy.getFabric(20,{
						flowerID:'',
						colorID:'',
						compositionID:'',
					});
					return
				}else{
					$('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
					_diy.getFabric(20,{
						flowerID:'',
						colorID:'',
						compositionID:$(this).attr('data-id'),
					});	
				}
			});
			
			//初始化全部面料
			$('#allFabric').trigger('click');
			
			//绑定面料切换事件
			_diy.selectFabric();
		}
		
		//数据请求
		$.ajax({
			url:_diy.api()+'inits/menu',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				parentID:pid,
				level:2,
				'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success:function(data){
				//alert(JSON.stringify(data))
				addHtml(data)
			},
			error:function(){
				alert('error')	
			}
		});
	},
	
	//获取面料数据
	getFabric:function(n,options){
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
					code:data[i].code,
					selected:data[i].selected
				}),hd_price='',hd_flag="",hd_ico="";

				if(_diy.uf==0&&data[i].is_first==1){//设置限量（首单体验款面料）		
					if(_diy.clothing=='0003'||_diy.clothing=='0004'||_diy.clothing=='0006'){
						//hd_price="<br>￥"+data[i]['activity_price_'+_diy.param.clothing];
						hd_flag=" data-first='"+data[i]['activity_price_'+_diy.param.clothing]+"'";
						hd_ico="<span class='ico-first'></span>";
					}
				}else if(data[i].activity==1){//活动面料（校园）
					//hd_price="<br>￥"+data[i]['activity_price_'+_diy.param.clothing];
					hd_ico="<span class='ico-hd'></span>"	
				}else if(data[i].is_import==1){//进口面料
					hd_ico="<span class='ico-import'></span>"		
				}
				
				html.push('<li'+(checkFocus?checkFocus:'')+hd_flag+' data-code="'+data[i].code+'"><img src="'+data[i].imgurl+'" ><p>'+data[i].tname+hd_price+'</p>'+hd_ico+'</li>');					
				
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
			url:_diy.api()+'fabrics/all',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				fabricid:$('#nav-fabric').attr('data-id'),
				flowerID:options.flowerID,
				colorID:options.colorID,
				compositionID:options.compositionID,
				style:_diy.style,
				cp:0,
				ps:0,
				'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success:function(data){
				if(data.length>0){
					ajaxPage(data)
				}else{
					obj.html('<div class="status">暂无库存，请稍后关注！</div>');
				}
			},
			error:function(){
				alert('面料数据error')	
			}
		});
	},
	selectFabric:function(){
		function fn(){
			var $this=$(this);
			if($this.hasClass('null'))return;
			$this.addClass('cur').siblings().removeClass('cur');
			$.each(_diy.param.strData,function(index,value){
				value.fabric=$this.attr('data-code');
			});
			_diy.getDiyData();
		if((_diy.clothing=="0006"||_diy.clothing=="0003"||_diy.clothing=="0004") && $this.attr('data-first')){
				$('#price').text($this.attr('data-first'))
				return	
			}
			_diy.getPrice();
		}
		$('#opt-fabric .itemList').off('click').on('click','li',fn);
	},
	
/*========================*
		 获取刺绣信息
*=========================*/
	
	getCxColorFont:function(pid){
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
					type:'embroidery',
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
			$('#cxCon').val(_diy.param.strData[_diy.param.clothing].embroidery[0])
			$('#cxFont').html('<ul>'+fontHtml.join('')+'</ul>');
			$('#cxColor').html('<ul>'+colorHtml.join('')+'</ul>');
			
			//获取签名位置
			_diy.getCxPosition(data[1].id,ids,pid);//字体id、选中字体id、签名id
		}
		//数据请求
		$.ajax({
			url:_diy.api()+'simgs/all',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
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
	},
	getCxPosition:function(pid,ids,menuId){//字体id、选中字体id、签名id
		function addHtml(d){
			//alert(JSON.stringify(d))
			var data=d.options,len=data.length,optionHtml=[];
			for(var i=0;i<len;i++){
				if(data[i].level!=1){
					//设置默认值
					var checkFocus=_diy.checkDefault({
						type:'embroidery',
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
			url:_diy.api()+'selects/all',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				style:_diy.style,
				clothing:_diy.param.clothing,
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
	},

	//签名
	setCixiu:function(){
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
			if($.trim($con.val())==''){
				setTimeout(function(){
					luck.open({content:'签名信息不能为空',time:1500});
				},500);
				return
			}
			if(chEnWordCount($con.val())>14){
				setTimeout(function(){
					luck.open({content:'签名信息不能超过14个字符',time:1500});
				},500);
				return	
			}
			if(!/^([A-Za-z0-9]|[\u4e00-\u9fa5]|\s)*$/.test($con.val())){
				setTimeout(function(){
					luck.open({content:'仅限数字/字母/汉字',time:1500});
				},500);
				return	
			}
			if(oPosition.length<=0){
				setTimeout(function(){
					luck.open({content:'请选择刺绣位置',time:1500});
				},500);
				return	
			}
			if(oColor.length<=0){
				setTimeout(function(){
					luck.open({content:'请选择刺绣颜色',time:1500});
				},500);
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
			_diy.param.strData[_diy.param.clothing].embroidery=arr;
			//alert(arr.join(','))
			luck.open({content:'签名信息保存成功',time:1500});
			setTimeout(function(){
				$('#cat2').hide();
				$('#cat1').show();
			},200);
	
		});
		//删除签名信息
		$('#opt-embroidery .titBar .del').unbind('click').click(function(){
			$('#cxCon').val('');
			$('#cxFont .cur,#cxPosition .cur,#cxColor .cur').removeClass('cur');
			_diy.param.strData[_diy.param.clothing].embroidery=[];
			setTimeout(function(){
				$('#cat2').hide();
				$('#cat1').show();
			},200)
		})
	},


/*===============================*
			尺码表
*================================*/

	getSizeTable:function(){
		var data=_diy.categoryId,tit=_diy.categoryName,tab=[],layer=[];
		$.each(data,function(i,value){
			tab.push('<li data-id="'+value+'">'+tit[i]+'</li>');
			layer.push('<div class="itemList'+(i>0?' hide':'')+'"><div class="loading">加载中...</div></div>');
			getSize(value,i);
		})
		//tab
		$('#sizeTable .tabBar').html(tab.join('')).children('li').click(function(){
			var n=$(this).index();
			$(this).siblings().removeClass('cur').end().addClass('cur');
			$('#sizeTable .itemList').eq(n).siblings().hide().end().show();
		}).eq(0).trigger('click');
		
		//layer
		$('#sizeTable .scrollBox').html(layer.join('')).on('click','li',function(){
			$(this).addClass('cur').siblings('.cur').removeClass('cur');
			//更新尺码
			_diy.param.strData[$('#sizeTable .tabBar .cur').attr('data-id')].sizes=$(this).attr('data-id');
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
				url:_diy.api()+'sizes/qsizeinfo',
				data:{
					cache:_diy.cache,
					csql:_diy.csql,
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
		$('#sizeOk').click(function(){
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
				luck.open({content:'请选择'+$('#sizeTable .tabBar li').eq(n).text()+'尺码',time:1500})
			}
		})
	},
/*======================================*
				下单
*=======================================*/
	order:function(){
		//下单按钮
		$('#orderDown').click(function(){
			if($uid==0){//登录弹层
				_diy.loginIn();
				return
			}

			if(!this.getAttribute('data-flag')){
				_diy.getSizeTable();
				this.setAttribute('data-flag',1)
			}
			$('#bottomSize').addClass('show');
			$('#zhezhaoLayer,#bottomSize .close').unbind('click').click(function(){
				$('#bottomSize').removeClass('show');
				_diy.loading.hide();	
			});
			$('#zhezhaoLayer').show();
		});
		
		//选择标准码
		$('#bottomSize .base').click(function(){
			$('#sizeTable').addClass('show');
			$('#bottomSize').removeClass('show');
			$('#zhezhaoLayer,#sizeTable .back').unbind('click').click(function(){
				$('#sizeTable').removeClass('show');
				_diy.loading.hide();	
			});
		});
		//尺码助手
		$('#sizeRefer').click(function(){
			$('#sizeReferTable').addClass('show');
			var box=$('#sizeReferTable .scrollBox');
			$.ajax({
				url:'/custom-stable.html',
				beforeSend: function(){
					box.html('<div class="loading">加载中...</div>');	
				},
				dataType:"json",
				success: function(data){
					box.html($(data.retval).filter('.size_'+$('#sizeTable .tabBar .cur').attr('data-id')));
				}
			})
			$('#sizeReferTable .back').unbind('click').click(function(){
				$('#sizeReferTable').removeClass('show');
			});	
		})

		//量体定制
		$('#bottomSize .amount').click(function(){
			var data=_diy.param.strData
			for(var n in data){
				data[n].sizes='diy'
			}
			_diy.underOrder()	
		});
	},
	underOrder:function(){
		_diy.loading.show(2);
		$('#zhezhaoLayer').unbind('click').css('z-index',110);
		_diy.generatePic(function(n){
			_diy.param.strData[_diy.categoryId[n]].image=_diy.diyData[_diy.categoryId[n]];
			if(n ==_diy.categoryId.length-1){
				$.ajax({
					url:'/cart-add.html?token='+_diy.pwd,
					type:'POST',
					timeout:5000,
					dataType:"json",
					data:{
						cache:_diy.cache,
						csql:_diy.csql,
						type:'diy',
						hj:'abc',
						params:JSON.stringify(_diy.param.strData)
					},
					success:function(data){
						if(data.done){
							window.location.href='/cart.html?token='+_diy.pwd
						}else{
							luck.open({content:data.msg,time:1500});
							_diy.loading.hide();
							$('#zhezhaoLayer').removeAttr('style');
						}
					},
					error:function(){
						$('#zhezhaoLayer').removeAttr('style');
						_diy.loading.hide();
						luck.open({content: '订单提交失败',time:1500});
					}
				});	
			}	
		})
	},
	
/*======================================*
				生成封面图
*=======================================*/

	generatePic:function(fn){
		var id=_diy.categoryId,i=0,len=id.length;
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
				url:_diy.api()+'inits/all',
				data:{
					cache:_diy.cache,
					csql:_diy.csql,
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
					luck.open({content: '获取数据失败',time:1500});
				}
			})
		}
		getBaseData(0);
	},

/*===========================*
		默认值处理 
*============================*/
	
	//获取默认工艺 
	getDefault:function(callback){
		$.ajax({
			url:_diy.api()+'inits/dft',	
			data:{
				cache:_diy.cache,
				clothingID:_diy.clothing,
				style:_diy.style,
				'access-token':_diy['access-token'],
			},
			dataType:"jsonp",
			success:function(data){
				//初始化默认值
				if(window.localStorage&&localStorage.diyData){
					_diy.param.strData=localStorage.diyData
					localStorage.diyData="";
				}else{
					_diy.param.strData=data.data
				}
				_diy.writeLog('默认值',data.data);
				_diy.addCode=data.ccolor;
				_diy.writeLog('需要拼接code的id',data.ccolor);
				if(typeof callback == 'function'){
					callback();	
				}
			},
			error:function(){
				alert('获取默认工艺数据错误')
			}
		});
	},

	//匹配默认值
	checkDefault:function(obj){
		//检测全局里有没有默认值，没有则走默认
		var data = _diy.param.strData[_diy.param.clothing],type=obj.type;
		if(type=='fabric'){
			if(data[type]){
				return defaults()
			}else if(obj.selected){
				return ' class="cur"'	
			}
		}else if(type=='embroidery'){
			if(data.embroidery.join(',').indexOf(obj.pid+':')>=0){
				return defaults()	
			}else if(obj.selected){
				return ' class="cur"'	
			}
		}else{
			if(data.cstr.join(',').indexOf(obj.pid+':')>=0){
				return defaults()	
			}else if(obj.selected){
				return ' class="cur"'	
			}
		}
		function defaults(){
			if(type=='fabric'){
				if(data.fabric==obj.code){
					return ' class="cur"'
				}
			}else if(type=='embroidery'){
				if(data.embroidery.join(',').indexOf(obj.pid+':'+obj.id)>=0){
					return ' class="cur"'
				}	
			}else{
				if(data.cstr.join(',').indexOf(obj.pid+':'+obj.id)>=0){
					return ' class="cur"'
				}
			}
		}
	},
	
	//更新默认值
	upDataCstr:function(pid,id,code){
			var str=pid+':'+id,
				flag=null,
				n=0,
				arr=_diy.param.strData[_diy.param.clothing].cstr,
				len=arr.length,
				checkCode=(function(pid){
					var success=false;
					$.each(_diy.addCode,function(index,value){
						if(value==pid){
							success=true;
							return	
						}	
					});
					return success;
				})(pid);
			if(code&&checkCode){
				str+='|||'+code	
			}
			for(var i=0;i<len;i++){
				if(arr[i].indexOf(pid+':')==0){
					flag=true;
					n=i;
					break
				}
			}
			flag?(arr[n]=str,flag=null):(arr.push(str));
			//alert(arr.join())
	},
	
/*===========================*
	      保存作品 
*============================*/
	saveWorks:function(){
		function closeLayer(){
			$('#zhezhaoLayer').fadeOut('fast');
		}
		$('#saveWorks').click(function(){
			if($uid==0){//未登录跳转
				_diy.loginIn()
				return
			}
			_diy.prompt({
				title:'作品将保存在APP的“我的--我的作品”里查看，可分享，可继续设计。',
				text:{
					required:true,
					tip:"给作品取个名吧"	
				},
				textarea:{
					tip:'您的设计理念',	
				},
				ok:function(oTxt){
					oTxt.type='works';
					_diy.save(oTxt);
				}
			})
		});
	},
	save:function (obj){
		_diy.generatePic(function(n){
			if(n ==_diy.categoryId.length-1){
				function closeLayer(){
					$('#zhezhaoLayer').fadeOut('fast');
				}
				var data={},oData,isWorks=(obj.type=="works");
				data.sysprocess=_diy.param.strData;
				data.cnt=$('#price').text();
				if(isWorks){
					oData={
						cache:1,
						ustr:_diy.pwd,
						code:JSON.stringify(data),
						img:JSON.stringify(_diy.diyData),
						clothingID:_diy.clothing,
						title:obj.tit,	
						style:_diy.style,
						describe:obj.con
					}
					if(_diy.worksID){
						oData.works=_diy.worksID	
					}
				}else{
					oData={
						cache:1,
						ustr:_diy.pwd,
						code:JSON.stringify(data),
						img:JSON.stringify(_diy.diyData),
						clothingID:_diy.clothing,
						share:1,
						title:'',	
						style:_diy.style,
						describe:''
					}
				}
				//alert(JSON.stringify(data))
				$.ajax({
					url:_diy.api()+"inits/sdiy?access-token="+_diy['access-token'],
					type:'POST',
					data:oData,
					success:function(data){
						closeLayer();
						if(isWorks){
							setTimeout(function(){
								luck.open({content:'保存成功',time:1500});
							},500);
						}else{
							if(typeof obj=='function'){
								obj('http://'+location.host+'/'+data.url);
							}
						}
					},
					error:function(){
						closeLayer();
						if(isWorks){
							setTimeout(function(){
								luck.open({content:'保存失败',time:1500});
							},500);
						}else{
							alert('分享保存失败')
						}
					}
				})
			}
		})
	},

/*===========================*
	      分享 
*============================*/
	diyShare:function(){
		var _cotte_share_config = {
			Site:'cotte个性化定制',
			Text:'Cotte·麦富迪',	
			Desc:'',	
			Url :'',
			Pic :'',
		},
		shareUrl={
			sina:function(){
				return 'http://service.weibo.com/share/share.php?url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&pic='+_cotte_share_config.Pic
				},
			qzone:function(){
				return 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&desc='+_cotte_share_config.Desc+'&summary=&site='+_cotte_share_config.Site+'&pic='+_cotte_share_config.Pic
			},
			tqq:function(){
				return 'http://share.v.t.qq.com/index.php?c=share&a=index&url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&appkey='	
			},
			sqq:function(){
				return 'http://connect.qq.com/widget/shareqq/index.html?url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&desc='+_cotte_share_config.Desc+'&summary=&site='+_cotte_share_config.Site+'&pics='+_cotte_share_config.Pic
			}
		};
		$('.share-btn').click(function(){
			$('#cotteshare').addClass('show');
			$('#zhezhaoLayer').show();
			$('#zhezhaoLayer,#cotteshare .back').unbind('click').click(function(){
				$('#cotteshare').removeClass('show');
				_diy.loading.hide();	
			});
		});

		$('.cotteshare').on('click','a',function(){
			var t=$(this).attr('data-cmd'),url='';
			_diy.save(function(str){
				$('#cotteshare').removeClass('show');
				_cotte_share_config.Url=str//更新URL
				_cotte_share_config.Pic=(function(){//更新图片
					var picList=[];
					$.each(_diy.diyData,function(index,value){
						picList.push(value);
					});
					return picList.join('||')
				})();
				switch(t){
					case 'tsina':
						url=shareUrl.sina();
					break;
					case 'qzone':
						url=shareUrl.qzone();
					break
					case 'tqq':
						url=shareUrl.tqq();
					break;
					case 'sqq':
						url=shareUrl.sqq();
					break;
					default :
						alert('未定义分享')
				}
				window.location.href=url;	
			})	
		})
		
	},
	
/*========================*
		  辅助功能
*=========================*/
	
	//loading
	loading:{
		show:function(n){
			var obj=$('#loadingTip');
			switch(n){
				case 1:
					obj.html('<i>准</i><i>备</i><i>数</i><i>据</i><i>中</i>');
				break;
				case 2:
					obj.html('<i>订</i><i>单</i><i>提</i><i>交</i><i>中</i>');
				break;
				default:
					obj.html('<i>3D</i><i>渲</i><i>染</i><i>中</i><i>...</i>');
			}
			obj.show();
			$('#zhezhaoLayer').show();
		},
		hide:function(){
			$('#loadingTip').hide();
			$('#zhezhaoLayer').hide();
		}
	},
	
	//log输出
	writeLog:function(name,obj){
		if(_diy.log){
			console.info(name)
			console.log(obj);
		}
	},
	//输入框
	prompt:function(options){
		var ran=Math.round(Math.random()*100),
			html=[];
			html.push('<div id="pop'+ran+'" class="prompt">');
			options.title?html.push('<p class="tit">'+options.title+'</p>'):'';
			options.text?html.push('<input type="text" placeholder="'+options.text.tip+'">'):'';
			options.textarea?html.push('<textarea placeholder="'+options.textarea.tip+'"></textarea>'):'';	
			html.push('<div class="layerbtn"><span class="ok">确定</span><span class="no">取消</span></div></div>');	
					
		$('.main').append(html.join(''));
		var obj=$('#pop'+ran),text=obj.find('input'),textarea=obj.find('textarea');
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
			var data={tit:'',con:''};
			if(text){
				if($.trim(text.val())==''&&options.text.required){
					luck.open({content:options.text.tip,time:1500});
					return
				}
				data.tit=text.val();
			}
			if(textarea){
				if($.trim(textarea.val())==''&&options.textarea.required){
					luck.open({content:options.textarea.tip,time:1500});
					return
				}
				data.con=textarea.val();
			}
			if(options.ok){
				options.ok(data);
			}
			closeFn()
		});
		obj.find('.no').unbind('click').click(function(){
			if(options.no){
				options.no(text.val());
			}
			closeFn()
		})
	},
	
	//登录
	loginIn:function(){
		//清楚障碍物
		$('#sizeReferTable,#sizeTable').removeClass('show');
		//登录相关
		var obj=$('#loginPop');
		obj.addClass('show');
		$('#zhezhaoLayer,#loginPop .back').unbind('click').click(function(){
			obj.removeClass('show');
			_diy.loading.hide();
		}).show();
		obj.find('.downBtn').unbind('click').click(function(){
			var name=$('#username').val(),pwd=$('#userpwd').val();
			if(!name){
				luck.open({
					content:'用户名不能为空',
					time:'1000'	
				});
				return
			}
			if(!pwd){
				luck.open({
					content:'密码不能为空',
					time:'1000'	
				});
				return
			}
			$.ajax({
			    url:'/member-login.html',
			    type:'post',
			    dataType:"json",
				data:{
					user_name:name,//'13573228635',
					password:pwd//'111111'	
				},
				success: function(d){
					if(d.done){
						obj.removeClass('show');
						_diy.loading.hide();
						$utk=_diy.pwd=d.retval;
						$uid=111;
						_diy.getPrice();
					}
					luck.open({
						content:d.msg,
						time:1500
					});	
				},
				error:function(e){
					luck.open({
						content:'请求出错！',
						time:2000
					});
					obj.removeClass('show');
					_diy.loading.hide();
				}
			})
		})
	}	
}
//微信浏览器隐藏头部
function is_weixin(){
	var ua = navigator.userAgent.toLowerCase();
	if(ua.match(/MicroMessenger/i)=="micromessenger") {
		return true;
 	} else {
		return false;
	}
}
if(is_weixin()){
	$('.topbar').hide();	
}