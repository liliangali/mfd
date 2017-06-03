// JavaScript Document
/**
 * Name：COTTE-DIY
 * Author：前端老徐
 * Date：2015/10/6
 * E-mail：442413729@qq.com
 * Blog:www.loveqiao.com
 *
 */
function Diy(){
	_diy=this;
	_diy.showArea=$('#showArea');//展示区
	_diy.diyData={};//当前diy数据
	_diy.catName=[];//保存当前品类的id/名称键值对
	_diy.pic={
		isNew:false,//标记当前图片数据是否是最新
		data:{}	//储存Canvas合并后的图片数据
	}
	_diy.viewAngle='a';//旋转角度
	_diy.smallPic={};//保存指定锁眼颜色小图
	_diy.works={
		id:null,//作品id
		name:'',//作品名称
		desc:''	//作品描述
	};
	_diy.uf=null;//0:未完成首单体验款 1:是-异步获取
	_diy.addressforShare=false;//标记是否是分享过来的连接
	_diy.addCode=null;//在集合内的id都需要拼接code-异步获取
	_diy.api='';//接口地址 js动态赋值
	_diy.log=true;//log开关
	_diy.cache='';//控制ajax全局缓存
	_diy.csql='';
	_diy['access-token']='101-token';
	_diy.pwd=null;//加密串 url截取
	_diy.style=null;//风格 url截取
	_diy.clothing=null;//定义初始的id
	_diy.clothingCur=null;//当前diy的clothing
	_diy.strData={};//diy工艺数据
}

Diy.prototype={
	init:function(){
		_diy.loading.show(1);
		_diy.setApi();//接口路由
		_diy.setHeight();//视图大小控制
		_diy.getDefForUrl();//截取url参数
		_diy.getDefault(_diy.getInitData);//初始化数据
		_diy.getCur();//可视化操作
		_diy.fnBtn();//功能按钮
		_diy.catNav();//分类导航
		_diy.diyShare();//分享功能
		_diy.addCart();//加入购物车流程
		//视图大小控制
		window.onresize=_diy.setHeight;
		window.onload=_diy.setHeight;
	},
/*===========================*
		分类导航
*============================*/
	catNav:function(){
		$('.catMenu dt').click(function(){
			$(this).parent().siblings().removeClass('cur').end().addClass('cur');	
		})
	},

	
/*===========================*
		检测环境、接口路由
*============================*/
	checkDomain:function(){
		var url=window.location.href;
		if(url.indexOf('www.cotte.cn')>=0){
			return 1 //线上环境
		}else if(url.indexOf('test.cotte.cn')>=0){
			return 2 //测试环境
		}else{
			return 3 //开发环境
		}
	},
	setApi:function(){
		switch(_diy.checkDomain()){
			case 1:
				_diy.api='http://api.diy.cotte.cn/v5/';
				_diy.cache='';
				_diy.csql='';
			break;
			case 2:
				_diy.api='http://api.diy.test.cotte.cn/v5/';
			break;
			case 3:
				_diy.api='http://api.diy.dev.cotte.cn:8080/v5/';
		}
	},
	
/*===========================*
		截取url参数 
*============================*/

	getDefForUrl:function(){
		if(window.location.href.indexOf('custom-diy-')>=0){
			//截取clothing
			var id=window.location.href.split('custom-diy-')[1].split('-');
			_diy.clothing=_diy.clothingCur=id[0];
			
			if(id.length==2){
				_diy.style=id[1].split('.')[0];//截取风格	
			}else if(id.length>=3){
				_diy.style=id[1];//截取风格
				_diy.works.id=id[2].split('.')[0];//作品id
			}
			_diy.pwd=$utk;
		}
		//截取是否是分享来源
		if(window.location.href.indexOf('share')>=0){
			_diy.addressforShare=true;
		}
	},
	
/*===========================*
		高度动态计算 
*============================*/
	setHeight:function(){
		var winH=$(window).height(),n1=winH-200,h1=n1>600?600:n1;
		_diy.showArea.css({height:h1,width:h1,marginTop:(winH-h1-135)/2});
		$('#rightBox').height(winH);
		$('#menu li').height(winH/10);
		$('.itemList').height(winH-142);
	},
	
	
/*===========================*
		底部功能按钮 
*============================*/
	fnBtn:function(){
		$('.subMenu').on('click','li',function(){
			switch(this.id){
				case 'reset':
					history.go(0);
				break
				case 'rotate':
					_diy.rotate();
				break;
				case 'saveWork':
					_diy.saveWorks();
				break;
				case 'diyinfo':
					_diy.showInfo();
				break;
				case 'fullScreen':
					;(function launchFullscreen(element) {
						if(element.requestFullscreen) {
							element.requestFullscreen();
						} else if(element.mozRequestFullScreen) {
							element.mozRequestFullScreen();
						} else if(element.webkitRequestFullscreen) {
							element.webkitRequestFullscreen();
						} else if(element.msRequestFullscreen) {
							element.msRequestFullscreen();
						}else{
							luck.poptip({
								con:'您的浏览器不支持全屏功能!'	
							})	
						}
					})(document.documentElement);
				break;
			}	
		});
		$('.subMenu-2 .setbg').click(function(){
			luck.alert('系统提示','炫酷背景正在设计中，请继续关注!',1)
		})
	},
	
/*===========================*
		显示详细信息 
*============================*/

	showInfo:function(){
		$.ajax({
			url:_diy.api+'inits/dl?access-token='+_diy['access-token'],
			type:'POST',
			data:{
				jdata:JSON.stringify({sysprocess:_diy.strData})
			},
			success: function(d){
				var html=[],tit={};
				$.each(_diy.catName,function(index,value){
					tit[value.id]=value.name;	
				})
				$.each(d,function(name,value){
					html.push('<h2>'+tit[name]+'</h2><ul>');
					$.each(value.cstr_n,function(index,value){
						html.push('<li>'+value+'</li>');
					})
					html.push('</ul>')
				});
				luck.open({
					title:'工艺信息',
					width:'500px',
					height:'450px',
					addclass:'cotte-luck',
					content:'<div class="infoLayer">'+html.join('')+'</div>',
					callback:function(obj){
						$(obj).find('.infoLayer').mCustomScrollbar({scrollInertia:500});	
					}
				})
			},
			error: function(){
				alert('error')	
			}	
		})	
	},


/*===========================*
		保存作品 
*============================*/
	saveWorks:function(){
		_diy.checkLogin(function(){
			var luckLayer=luck.open({
				title:'保存作品',
				width:'435px',
				addclass:'cotte-luck',
				content:'<input type="text" class="luck-prompt-tit" placeholder="作品标题" value="'+(_diy.works.name?_diy.works.name:'')+'"><textarea class="luck-prompt" placeholder="请输入作品设计理念">'+(_diy.works.desc?_diy.works.desc:'')+'</textarea><div class="luck-btn"><button class="luck-yes">保存</button></div>'
			});
			var oLayer=$(luckLayer),tit=oLayer.find('.luck-prompt-tit'),con=oLayer.find('.luck-prompt');
			oLayer.find('.luck-yes').click(function(){
				if(tit.val()==''){
					luck.alert('系统提示','标题不能为空',1)	
				}else{
					_diy.save({tit:tit.val(),con:con.val(),type:'works'});
					luck.close(luckLayer);
					_diy.loading.show(3);
				}
			})
		})
	},
	save:function (obj){
		_diy.canvasPicEdit(function(){//合成图片
			var data={},oData,isWorks=(obj.type=="works");
			data.sysprocess=_diy.strData;
			data.cnt=$('#price').text();
			if(isWorks){
				oData={
					cache:1,
					ustr:_diy.pwd,
					code:JSON.stringify(data),
					img:JSON.stringify(_diy.pic.data),
					clothingID:_diy.clothing,
					title:obj.tit,	
					style:_diy.style,
					describe:obj.con
				}
				if(_diy.works.id&&window.location.href.indexOf('share')<0){
					oData.works=_diy.works.id	
				}
			}else{
				oData={
					cache:1,
					ustr:_diy.pwd,
					code:JSON.stringify(data),
					img:JSON.stringify(_diy.pic.data),
					clothingID:_diy.clothing,
					share:1,
					title:'',	
					style:_diy.style,
					describe:''
				}
			}
			$.ajax({
				url:_diy.api+"inits/sdiy?access-token="+_diy['access-token'],
				type:'POST',
				data:oData,
				//timeout:1000,
				dataType:"json",
				//timeout:1500,
				success:function(data){
					_diy.loading.hide();
					if(isWorks){
						if(data.status){luck.close()}
						luck.alert('系统提示',data.msg,6);
					}else{
						if(typeof obj=='function'){
							var domain='';
							switch(_diy.checkDomain()){
								case 1:
									domain='http://www.cotte.cn/';
								break;
								case 2:
									domain='http://www.test.cotte.cn/';
								break;
								default:
									domain='http://www.dev.cotte.cn:8080/';
							}
							obj({shareUrl:domain+data.url,pic:data.pic});
						}
					}
				},
				error:function(){
					alert('error')
				}
			})
		})
	},
/*======================================*
			HTML5图片合成
*=======================================*/
	canvasPicEdit:function(callback){
		if(_diy.pic.isNew){//如果是最新的图片则直接走回调
			callback();
			return;	
		}
		_diy.generatePic(function(n){
			if((n+1)==_diy.catName.length){
				var w=400,//定义生成图片宽度
					h=500,//定义生成图片高度
					num=0;
				$.each(_diy.diyData,function(name,value){
					//创建画布
					var can=document.createElement('canvas');
					can.width=w;
					can.height=h;
					var ctx=can.getContext('2d');
					ctx.fillStyle='#fff';
					ctx.fillRect(0,0,w,h);
					//数组排序
					function sortNumber(a,b){
						return a.zindex - b.zindex
					}
					value.sort(sortNumber);
					//合成
					var count=0,len=value.length;
					function draw(){
						var img=new Image();
						img.crossOrigin = 'Anonymous'; //解决跨域
						img.src=value[count].imgurl;
						img.onload=function(){
							ctx.drawImage(img,-50,0,h,h);
							count++;
							if(count==len){
								//window.open(can.toDataURL('image/jpeg',5))
								_diy.pic.data[name]=can.toDataURL('image/jpeg',5);
								num++
								if(num==_diy.catName.length){
									_diy.pic.isNew=true;
									if(typeof callback == 'function'){
										callback();
									}
								}	
							}else{
								draw()
							}
						}
					}
					draw();
				})
			}
		})
	},
	
/*======================================*
				生成封面图
*=======================================*/
	generatePic:function(fn){
		var id=_diy.catName,i=0,len=id.length;
		function getBaseData(n){
			if(_diy.diyData[id[n].id]){//从缓存读数据
				fn(i)
				if(i<len-1){
					i++
					getBaseData(i);
				}
				return	
			}
			$.ajax({//服务器取数据
				url:_diy.api+'inits/display',
				data:{
					 cache:_diy.cache,
					 csql:_diy.csql,
					 clothingID:id[n].id,
					 style:_diy.style,
					 strFabricCode:_diy.strData[_diy.clothingCur].fabric,
					 cstr:_diy.cstr(), //工艺
					 'access-token':_diy['access-token']
				},
				dataType:"jsonp",
				success:function(data){
					_diy.diyData[_diy.catName[n].id]=data;
					fn(i)
					if(i<len-1){
						i++
						getBaseData(i);
					}
				},
				error:function(){
					alert('error');
				}
			})
		}
		getBaseData(0);
	},
	
/*======================================*
				旋转
*=======================================*/
	rotate:function(){
		switch (_diy.viewAngle) {
			  case "a":
				_diy.viewAngle="b"
				_diy.getRotateData(10013)
				break;
			  case "b":
				_diy.viewAngle='c'
				_diy.getRotateData(10005)
				break;
			  case "c":
				_diy.viewAngle='a'
				_diy.getRotateData(10004)
				break;
		}	
	},
	//获取旋转数据更新视图
	getRotateData:function(g){
		_diy.loading.show();
		$.ajax({
			url:_diy.api+"inits/rotation",
			data:{
				cache:_diy.cache,
				clothingID:_diy.clothingCur,
				style:_diy.style,
				strFabricCode:_diy.strData[_diy.clothingCur].fabric,
				viewID:g,
				cstr:_diy.cstr(), //工艺
				'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success: function(data){
				//alert(JSON.stringify(data))
				_diy.newUpData(data)
			},
			error:function(){
				alert('旋转失败')	
			}	
		})	
	},
	
/*========================*
	    渲染数据 
*=========================*/
	newUpData:function(data){
		var pic=[]
		_diy.diyData[_diy.clothingCur]=data;
		_diy.pic.isNew=false;
		$.each(data,function(index,value){
			pic.push('<img src="'+value.imgurl+'" width="600" height="600" data-id="'+value.id+'" style="z-index:'+value.zindex+'">');	
		});
		_diy.showArea.html(pic.join(''));
		_diy.loading.hide();
	},
	/*收集工艺参数*/
	cstr:function(){
		var arr=[];
		$.each(_diy.strData[_diy.clothingCur],function(name,value){
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
			url:_diy.api+'inits/display',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
				 clothingID:_diy.clothingCur,
				 style:_diy.style,
				 strFabricCode:_diy.strData[_diy.clothingCur].fabric,
				 cstr:_diy.cstr(), //工艺
				 'access-token':_diy['access-token']
			},
			dataType:"jsonp",
			success:function(data){
				_diy.newUpData(data);
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
			url:_diy.api+'selects/all',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
				 style:_diy.style,
				 clothing:_diy.clothingCur,
				 fabricCode:_diy.strData[_diy.clothingCur].fabric,
				 parentID:obj.pid,
				 ids:obj.ids,
				 menuId:obj.mid,
				 cstr:_diy.cstr(), //工艺
				 'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success:function(data){
				_diy.newUpData(data.p);
			},
			error:function(){
				alert('基本款数据错误')	
			}
		})	
	},	
	
	
/*============================*
		   可视化操作 
*=============================*/

	//绑定获取焦点
	getCur:function(){
		if(document.createElement('canvas').getContext){
			var delay=true;
			_diy.showArea.mousemove(function(e){
				$(this).find('.Highlight').remove().end().removeAttr('data-pt');
				$('#TipInfo').remove();
				if(delay){clearTimeout(delay)}
				var size=$(this).height();
				delay=setTimeout(function(){
					_diy.getFocus(e.offsetX,e.offsetY,size);
				},100);
			}).mouseleave(function(){
				$(this).find('.Highlight').remove();	
			}).click(function(){
				var arr=$(this).attr('data-pt').split('-');
				$('#menu-'+arr[0]).click();
				setTimeout(function(){
					$('#opt-'+arr[0]).find('.tabTit [data-pid='+arr[1]+']').click();
				},300);
			});
		}
	},

	//通过色值匹配获取焦点
	getFocus:function(x,y,size){
		var arr=[],data=_diy.diyData[_diy.clothingCur],len=data.length;
		$.each(data,function(name,value){
			var can=document.createElement('canvas');
				ctx=can.getContext('2d');
				can.width=size;
				can.height=size;
			var img=new Image();
				img.crossOrigin = 'Anonymous'; //解决跨域
				img.src=value.imgurl;
				img.onload=function(){
					ctx.drawImage(img,0,0,size,size);
					//$('body').append(can)
					var imgData = ctx.getImageData(0,0,size,size).data,
					    _step = (y * size + x) * 4,
					    data={
							rgba:[imgData[0 + _step],imgData[1 + _step],imgData[2 + _step],imgData[3 + _step]].join(),
							imgurl:value.imgurl,
							zindex:value.zindex,
							id:value.id,
							pt:value.pt,
							cn:value.cn
						};
					arr.push(data);
					if(arr.length==len){
						$.each(arr,function(index,value){
							if(value.rgba!='0,0,0,0'&&value.pt){
								_diy.showArea.find('.Highlight').remove().end();
								//alert(value.imgurl);
								var can=document.createElement('canvas'),
									ctx=can.getContext('2d');
									can.width=size;
									can.height=size;
									can.className='Highlight';
									can.style.zIndex=value.zindex;
								var img=new Image();
								img.crossOrigin = 'Anonymous'; //解决跨域
								img.src=value.imgurl;
								img.onload=function(){
									ctx.drawImage(img,0,0,size,size);
									ctx.globalCompositeOperation="source-atop";
									ctx.fillStyle='#fff'
									ctx.fillRect(0,0,size,size);
									_diy.showArea.append(can);//追加高光层
									_diy.showArea.attr('data-pt',value.pt);
									//追加Tip提示
									var info=$('<div>');
									info[0].id='TipInfo';
									info.text(value.cn);
									$('body').append(info);
									info.css({left:x+_diy.showArea.offset().left-info.width()/2-15,top:y+_diy.showArea.offset().top-45,visibility:'visible'});
									
								}
								return false	
							}	
						})	
					}
				}
		});
	},
	
	
/*============================*
		   获取价格 
*=============================*/
	getPrice:function(){
			var arr=[];//0003:DBM584A|0004:DBM584A|...
			for(n in _diy.strData){
				var data=_diy.strData[n],str=[n];
				str.push(':');
				str.push(data.fabric);
				arr.push(str.join(''))
			}
			$.ajax({
				url:_diy.api+'inits/pcnt',
				data:{
					cache:1,
					csql:1,
					clothingID:_diy.clothingCur,
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
			var data=d.data,bigNav=[],len=data.length,flag=true,isWorks=_diy.works.id&&d.sysprocess;
			
			//设置作品默认值
			if(isWorks){
				_diy.works.name=d.work_name;//存储作品名称
				_diy.works.desc=d.description;//存储作品描述
				_diy.strData=d.sysprocess;
				_diy.writeLog('作品数据',d.sysprocess);
			}
			
			//获取价格
			_diy.getPrice();
			
			//初始化数据处理
				for(var i=0;i<len;i++){
					bigNav.push('<button data-id="'+data[i].nav.i+'">'+data[i].nav.n+'</button>');
					_diy.catName.push({id:data[i].nav.i,name:data[i].nav.n});
				}
				if(len>1){
				$('#tzTab').html(bigNav.join(''));
				$('.tzTab button').click(function(){
					$(this).addClass('cur').siblings('.cur').removeClass('cur');
					_diy.clothingCur=this.getAttribute('data-id');
					upData($(this).index());
				}).eq(0).trigger('click');
			}else{
				$('#tzTab').hide();
				_diy.clothingCur=data[0].nav.i;
				upData(0);
			}
			
			//大导航点击处理
			function upData(g){
				_diy.getDiyData();
				var smallNav=[],opt=[];
				for(var n in data[g]){
					if(n!='nav'){
						smallNav.push('<li class="menu-'+n+data[g].nav.i+" menu-"+n+'" id="menu-'+n+'" data-id="'+data[g][n].i+'"><span><i class="ico"></i>'+data[g][n].n.substring(0,2)+'</span></li>');
						opt.push(_diy.setOptHtml(n,data[g][n]));
					}
				}
				//更新组件模版
				$('#menu').html(smallNav.join('')).show();
				$('#opt').html(opt.join(''));
				//绑定组件切换事件
				_diy.selectCat();
				//更新高度
				_diy.setHeight();
			}
		}

		$.ajax({
			url:_diy.api+'inits/nav',
			data:{
				cache:1,
				clothingID:_diy.clothing,
				style:_diy.style,
				worksID:_diy.works.id,
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
			var str='<div class="hide" id="opt-'+name+'">\
						<div class="tabTit" data-type="'+name+'">'
							for(var i=0,len=obj.l.length;i<len;i++){
								str+='<span'+(obj.l[i].rt?" data-rt='1'":"")+' data-pid="'+obj.l[i].p+'" data-api="'+obj.l[i].u+'">'+obj.l[i].n+'</span>';	
							}
				str+=	'</div>\
						<div class="itemList cstrBox"><div class="loading">加载中...</div></div>\
				</div>';
			return str;
		}
		switch(name){
			case 'fabric':
				return '<div id="opt-fabric">\
							<div class="tabTit" data-type="'+name+'"></div>\
							<div class="itemList"><div class="loading">加载中...</div></div>\
						</div>';
			break;
			case 'embroidery':
				return '<div class="hide" id="opt-embroidery">\
							<div class="saveCxInfo">保存刺绣信息</div>\
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
		$('#menu').off('click').on('click','li',function(){
			$(this).addClass('cur').siblings().removeClass('cur');
			var id=this.id,flag=(this.getAttribute('flag')!=1);
			$('#opt').children('div').eq($(this).index()).siblings().hide().end().show();
			switch(id){
				case 'menu-fabric':
					if(flag){
						_diy.getFabricCat(this.getAttribute('data-id'));
					}
				break;
				case 'menu-embroidery':
					if(flag){
						_diy.getCxColorFont(this.getAttribute('data-id'));
					}
				break
				default:
					var objBox=$('#'+id.replace('menu','opt'));
					if(flag){
						_diy.selectCat2(objBox);
					}
					objBox.find('.tabTit').children('span').eq(0).click();	
					
			}
			if(flag){
				this.setAttribute('flag',1)
			}
		});
		$('#menu-fabric').click();//初始化面料数据
	},
	selectCat2:function(obj){
		//组件二级分类选择
		obj.find('.tabTit').off('click').on('click','span',function(){
			var _self=this;
			$(_self).addClass('cur').siblings().removeClass('cur');
			var box=obj.children('.itemList').html('<div class="loading">加载中...</div>'),
				//定义是否为西服或者衬衣的扣线颜色-特殊处理
				kxLineColor=(($(_self).attr('data-pid')==345)&&($(_self).parents('.tabTit').attr('data-type')=='kx'))||(($(_self).attr('data-pid')==3202)&&($(_self).parents('.tabTit').attr('data-type')=='kx')),
				//定义是否为西服高端设计里的扣眼颜色-特殊处理
				gdLineColor=(($(_self).attr('data-pid')==200861)&&($(_self).parents('.tabTit').attr('data-type')=='gd')),
				key=$(_self).parent('.tabTit').attr('data-type');
			//圆下摆、直下摆接口自带cstr避免重复在此【特殊处理】
			var api=(function(url){
				if(url.indexOf('cstr=')>=0){
					return {u:url.split('cstr=')[0],c:url.split('cstr=')[1]}
				}
				return {u:url,c:''}
			})(_self.getAttribute('data-api'))
			$.ajax({
				url:_diy.api+api.u,
				dataType:"jsonp",
				data:{
					 cache:_diy.cache,
					 csql:_diy.csql,
					 style:_diy.style,
					 pkey:key,
					 cstr:_diy.cstr()+'|'+api.c, //工艺
					 clothing:_diy.clothingCur,
					 'access-token':_diy['access-token']
				},
				success: function(d){
					box.mCustomScrollbar('destroy');//卸载滚动条美化
					var str='',data=d.imges||d.options,checkTit={flag:false,tit:[],html:{}};
					//西服-高端工艺-插花眼线色-特殊处理
					if(gdLineColor){
						$.ajax({
							url:_diy.api+'simgs/line?clothing=0003&access-token=101-token',
							data:{
								clothing:_diy.clothingCur,
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
									str+='<div class="'+checkFocus+'item" data-pid="'+pid+'" data-id="'+id+'" data-code="'+value.ecode+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></div>'
									
								})
								box.html(str)
								box.mCustomScrollbar({scrollInertia:500});//滚动条美化
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
									str+='<div class="hide">'
									$.each(checkTit.html[value.id],function(index,value){
										str+='<div class="item" data-ecode="'+(value.ecode?value.ecode:'')+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></div>';
									})
									str+='</div>';
								}else if(checkTit.html[value.id]){
									$.each(checkTit.html[value.id],function(index,value){
										//设置默认值
										var checkFocus=_diy.checkDefault({
											pid:value.parentid,
											id:value.id,
											selected:false
										});
										if(!value.imgurl){
											str+='<div class="'+checkFocus+'null kxSelect item" data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="a.jpg" onerror="nofind(this,\''+value.parentid+'_'+value.id+'\')"></div>';
										}else{
											str+='<div class="'+checkFocus+'null kxSelectDel item" data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'(顺色)</p></div>'
										}
									});
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
								var _self=$(this),line=_self.parents('.kyLineColor').find('div:eq(0)');
								line.find('.cur').removeClass('cur');
								line.find('[data-ecode="'+_self.attr('data-code')+'"]').addClass('cur');
								$('.main').append('<div class="lineLayer"><div class="itemList">'+line.html()+'</div></div>');
								$('.lineLayer .itemList').height($(window).height()-130).mCustomScrollbar({scrollInertia:500});
								$('.lineLayer').off('click').on('click','.item',function(){
									var code=$(this).attr('data-ecode');
									_self.children('img').attr('src',$(this).children('img')[0].src).end().attr('data-code',code);
									_self.addClass('cur').siblings().removeClass('cur');
									_diy.upDataCstr(_self.attr('data-pid'),_self.attr('data-id'),code);
									_diy.smallPic[_self.attr('data-pid')+'_'+_self.attr('data-id')]=$(this).children('img')[0].src;//保存小图
									$('.lineLayer').remove();	
								})
							})
							return	
						}
						$.each(checkTit.tit,function(index,value){
							str+='<div class="minTit">'+value.name+'</div>';
							if(checkTit.html[value.id]){
								$.each(checkTit.html[value.id],function(index,value){
									//设置默认值
									var checkFocus=_diy.checkDefault({
										pid:value.parentid,
										id:value.id,
										selected:value.selected
									});
									str+='<div class="'+checkFocus+'item" data-pid="'+value.parentid+'" data-id="'+value.id+'" data-code="'+(value.ecode?value.ecode:"")+'"><img onerror="nofind(this)" src="'+value.imgurl+'"><p>'+value.name+'</p></div>'
									//获取微调信息
									if($(_self).attr('data-rt')&&checkFocus){
										_diy.getWeitiao(value.parentid,$(_self).attr('data-pid'),value.id,box);
									}	
								})
							}
						});
						box.html('<div id="pid-'+_self.getAttribute('data-pid')+'">'+str+'</div>')
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
							str+='<div class="'+checkFocus+'item" data-pid="'+value.parentid+'" data-id="'+value.id+'" data-code="'+(value.ecode?value.ecode:"")+'"><img src="'+value.imgurl+'" onerror="nofind(this)"><p>'+value.name+'</p></div>'
						})
						box.html('<div id="pid-'+_self.getAttribute('data-pid')+'">'+str+'</div>')	
					}
				    //滚动条美化
					box.mCustomScrollbar({scrollInertia:500});
				},
				error:function(){alert('error')}
			});
		})
		//组件选择
		$('.cstrBox').off('click').on('click','.item',function(){
			var _self=this;
			/*
			 * 由于西服的锁眼线色Pid受外观设计里的袖口数量影响，导致更新袖口数量后锁眼线色的默认值不能清空上一个默认值问题处理
			 * 特殊处理/S
			 */
			 if($(_self).attr('data-pid')==189){
				$.each(_diy.strData,function(name,value){
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
					$.each(_diy.strData,function(name,value){
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
					url:_diy.api+'selects/all',
					data:{
						 cache:_diy.cache,
						 csql:_diy.csql,
						 ids:_ids,
						 menuId:1119,
						 parentID:50,
						 style:_diy.style,
						 cstr:_diy.cstr(),
						 clothing:_diy.clothingCur,
						 'access-token':_diy['access-token']
					},
					dataType:"jsonp",
					success: function(d){
						//alert(JSON.stringify(d.options))
						$.each(d.options,function(i,d){
							if(d.selected){
								//alert(d.parentid+'---'+d.id)
								_diy.upDataCstr(d.parentid,d.id);
							}	
						})
					}
				});
			 }
			/*
			 * 特殊处理/E
			 */
			 
			/*
			 * 切换西服的里衬-挂面型影响里子造型默认值
			 * 特殊处理/S
			 */
			 if($(_self).attr('data-pid')==218){
				 $.ajax({
					url:_diy.api+'simgs/all?parentID=35459',
					dataType:"jsonp",
					data:{
						 cache:_diy.cache,
						 csql:_diy.csql,
						 style:_diy.style,
						 pkey:'lc',
						 cstr:_diy.cstr(), //工艺
						 clothing:_diy.clothingCur,
						 'access-token':_diy['access-token']
					},
					success: function(d){
						$.each(d.imges,function(i,d){
							if(d.selected){
								//更新cstr
								_diy.upDataCstr(d.parentid,d.id,d.ecode);
							}	
						})
					}
				 })
			}
			 
			/*
			 * 特殊处理/E
			 */  
			//带微调的组件-特殊处理
			var menu=$(_self).parents('.itemList').parent().find('.tabTit').children('.cur'),
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
					url:_diy.api+'simgs/all',
					data:{
						cache:_diy.cache,
					    csql:_diy.csql,
						style:_diy.style,
						parentID:3093,
						cstr:_diy.cstr(), //工艺
						clothing:_diy.clothingCur,
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
								arr=_diy.strData[_diy.clothingCur].cstr,
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
			if($(_self).hasClass('null')||$(_self).parents('#opt-lc').length)return;
			//更新diy视图
			_diy.getDiyData2({
				pid:pid,
				ids:id,
				mid:$(_self).parents('.itemList').parent().find('.tabTit').find('.cur').attr('data-pid')
			});
		})
	},
	getWeitiao:function(pid,mid,ids,box){
		$.ajax({
			url:_diy.api+'selects/all',
			data:{
				 cache:_diy.cache,
				 csql:_diy.csql,
				 ids:ids,
				 menuId:mid,
				 parentID:pid,
				 style:_diy.style,
				 cstr:_diy.cstr(),
				 clothing:_diy.clothingCur,
				 'access-token':_diy['access-token']
			},
			dataType:"jsonp",
			success: function(d){
				//console.log(d);
				var str='',obj=box.find('.mCSB_container').find('.weitiao').length>0;
				$.each(d.options,function(index,value){
					//设置默认值
					var checkFocus=_diy.checkDefault({
						pid:value.parentid,
						id:value.id,
						selected:value.selected
					});
					if(index>0){
						str+='<div class="'+checkFocus+'null item" data-pid="'+value.parentid+'" data-id="'+value.id+'"><img src="a.jpg" onerror="nofind(this)"><p>'+value.name+'</p></div>'
					}
				});
				if(obj){
					box.find('.mCSB_container').find('.weitiao').html('<div class="minTit">'+d.options[0].name+'</div>'+str);
				}else{
					box.find('.mCSB_container').append('<div class="weitiao"><div class="minTit">'+d.options[0].name+'</div>'+str);
				}
				box.mCustomScrollbar('update');
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
				if(i==0){cat.push('<span id="allFabric" class="cur">全部</span>')}
				cat.push('<span data-pid="'+data[i].parentid+'" data-id="'+data[i].id+'">'+data[i].name+'</span>');
			}
			cat.push('<span id="customFabric">自定义</span>');
			//渲染面料一级分类并绑定事件
			$('#opt-fabric .tabTit').html(cat.join('')).find('span').unbind('click').click(function(){
				var index=$(this).index();
				$(this).addClass('cur').siblings().removeClass('cur');
				/**
				 * 自定义面料
				 **/
				if(this.id=='customFabric'){
					var fra=document.createElement('div'),
						tit=document.createElement('div'),
						box=document.createElement('div'),
						txt=document.createElement('input'),
						btn=document.createElement('button'),
						tipInfo=document.createElement('div');
						tipInfo.className='tipInfo';
						info=document.createElement('ul');
						info.innerHTML='<li>如果面料图选区没有你想要的面料时，可在这里输入面料编号；</li><li>更换面料号后，以此面料号来下订单，款型、外观等个性化设计将继续保留</li>';
						tit.className='minTit';
						tit.innerHTML='输入面料号';
						txt.type='text';
						box.className="inputBox";
						txt.value=_diy.strData[_diy.catName[0].id].fabric;
						btn.innerHTML='更换面料';
					fra.className="customFabric";
					fra.appendChild(tit);
					box.appendChild(txt);
					box.appendChild(btn);
					fra.appendChild(box);
					fra.appendChild(tipInfo);
					fra.appendChild(info);
					txt.onfocus=function(){
						tipInfo.innerHTML='';	
					}
					btn.onclick=function(){
						if(txt.value==''){
							tipInfo.innerHTML='<font color="red">请输入面料号</font>';
							return
						}
						btn.innerHTML='处理中..';
						btn.disabled=true;
						var arr=[];//0003:DBM584A|0004:DBM584A|...
						for(n in _diy.strData){
							var data=_diy.strData[n],str=[n];
							str.push(':');
							str.push(txt.value);
							arr.push(str.join(''))
						}
						$.ajax({
							url:_diy.api+'inits/pcnt',
							data:{
								cache:1,
								csql:1,
								clothingID:_diy.clothingCur,
								codestr:arr.join('|'),
								'access-token':_diy['access-token'],
								token:_diy.pwd
							},
							dataType:"jsonp",
							success:function(data){
								//alert(JSON.stringify(data))
								if(data.price!=0){
									$.each(_diy.strData,function(i,d){
										d.fabric=txt.value;
									});
									$('#price').text(data.price);
									tipInfo.innerHTML='<font color="green">更换面料成功!</font>';
								}else{
									tipInfo.innerHTML='<font color="red">面料号不存在！</font>'	
								}
								btn.innerHTML='更换面料';
								btn.disabled=false;
							},
							error:function(){
								alert('error')	
							}
						});						
					}
					$('#opt-fabric .itemList').html('');	
					$('#opt-fabric .itemList')[0].appendChild(fra);
					return	
				}
				if(index==0){
					 $('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
					 _diy.getFabric(24,{
						flowerID:'',
						colorID:'',
						compositionID:'',
					});
					return
				}else{
					$('#opt-fabric .itemList').html('<div class="loading">加载中...</div>');
					_diy.getFabric(24,{
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
			url:_diy.api+'inits/menu',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				parentID:pid,
				level:2,
				'access-token':_diy['access-token']	
			},
			dataType:"jsonp",
			success:function(data){
				addHtml(data)
			},
			error:function(){
				alert('error')	
			}
		});
	},
	
	//获取面料数据
	getFabric:function(n,options){
		var data=null,page=0,obj=$('#opt-fabric .itemList'),
			color={
				blue:'圣诞、元旦、新年、情人',
				green:'特惠、活动',
				orange:'限量、进口'
			};
		//组织数据
		function ranColor(t){
			var cla='blue';
			$.each(color,function(i,d){
				if(d.match(t.substring(0,2))){
					cla=i;
					return false;	
				}	
			});
			return cla;	
		}
		function addHtml(start,end){
			var html=[];
			function fabricStatus(n){//面料状态标签
				return n>0?'<div class="ds-'+n+'"></div>':'';
			}
			for(var i=start;i<end;i++){
				//设置默认值
				var checkFocus=_diy.checkDefault({
					type:'fabric',
					code:data[i].code,
					selected:data[i].selected
				}),hd_flag="",hd_ico="";

				if(_diy.uf==0&&data[i].is_first==1){//设置限量（首单体验款面料）		
					if(_diy.clothing=='0003'||_diy.clothing=='0004'||_diy.clothing=='0006'){
						hd_flag=" data-first='"+data[i]['activity_price_'+_diy.clothingCur]+"'";
						//hd_ico="<i class='ico orange'>限量</i>";
					}
				}/*else if(data[i].activity==1){//活动面料（校园）
					hd_ico="<i class='ico green'>校园</i>"	
				}else if(data[i].is_import==1){//进口面料
					hd_ico="<i class='ico blue'>进口</i>"		
				}*/
				if(data[i].tags&&data[i].tags!='0'){
					hd_ico="<i class='ico "+ranColor(data[i].tags)+"'>"+data[i].tags+"</i>"
				}
				
				html.push('<div class="'+checkFocus+(fabricStatus(data[i].d_s)?"null ":"")+'item"'+hd_flag+' data-code="'+data[i].code+'"><img src="'+data[i].imgurl+'" ><p>'+data[i].tname+'</p>'+hd_ico+(data[i].code=="SKP025A"||data[i].code=="SAK567A"?"<span class='ico-mt'></span>":"")+fabricStatus(data[i].d_s)+'<i class="see hide"></i></div>');	//新增免烫面料标签					
				
			}
			if(page==0){
				obj.html(html.join(''));
			}else{
				obj.find('.mCSB_container').append(html.join(''));
			}
			page=end;
		}
		//分页处理
		function ajaxPage(d){
			data=d;//同步请求数据到上层作用域
			var len=data.length,box=$("#opt-fabric .itemList");
			//显示第一页数据
			addHtml(page,len>n?n:len)
			//滚动条美化
			box.mCustomScrollbar({
				scrollInertia:500,
				//advanced:{ updateOnContentResize:true},
				callbacks:{
					onTotalScroll:function(){//无限加载
						if(len-page>0){
							var end=(len-page>=n)?page+n:len
							addHtml(page,end);
							box.mCustomScrollbar('update');
						}else{
							box.find('.mCSB_container').find('.loadTip').remove().end().append('<div class="loadTip">加载完毕</div>');
						}
					}	
				}	
			});
		}
		//数据请求
		$.ajax({
			url:_diy.api+'fabrics/all',
			data:{
				cache:1,
				csql:1,
				fabricid:$('#menu-fabric').attr('data-id'),
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
		function fn(e){
			var $this=$(this);
			if($this.hasClass('null'))return;
			$this.addClass('cur').siblings().removeClass('cur');
			$.each(_diy.strData,function(index,value){
				value.fabric=$this.attr('data-code');
			});
			_diy.getDiyData();
		if((_diy.clothing=="0006"||_diy.clothing=="0003"||_diy.clothing=="0004") && $this.attr('data-first')){
				$('#price').text($this.attr('data-first'))
				return	
			}
			_diy.getPrice();
		}
		$('#opt-fabric .itemList').off('click').on('click','.item',fn);
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
					fontHtml.push('<div class="'+checkFocus+'item" data-id='+data[i].id+' data-pid='+data[i].parentid+' data-font='+data[i].name+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'"><p>'+data[i].name+'</p></div>');
					if(checkFocus){
						ids=data[i].id
					}
					
				}
				if(data[i].parentid==data[colorIndex].id){//颜色
					colorHtml.push('<div class="'+checkFocus+'item" data-id='+data[i].id+' data-pid='+data[i].parentid+' data-color='+data[i].name+'><img alt="'+data[i].name+'" src="'+data[i].imgurl+'" width="63" height="63"><p>'+data[i].name+'</p></div>');
				}
			}
			$('#cxCon').val(_diy.strData[_diy.clothingCur].embroidery[0])
			$('#cxFont').html(fontHtml.join(''));
			$('#cxColor').html(colorHtml.join(''));
			
			//获取签名位置
			_diy.getCxPosition(data[1].id,ids,pid);//字体id、选中字体id、签名id
		}
		//数据请求
		$.ajax({
			url:_diy.api+'simgs/all',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				style:_diy.style,
				parentID:pid,
				cstr:_diy.cstr(), //工艺
				clothing:_diy.clothingCur,
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
	
					optionHtml.push('<p class="'+checkFocus+'" data-id="'+data[i].id+'" data-pid="'+data[i].parentid+'" data-position="'+data[i].name+'"><span></span>'+data[i].name+'</p>');
				}
			}
			$('#cxPosition').html(optionHtml.join(''));
			$('#opt-embroidery .itemList').mCustomScrollbar({scrollInertia:500});//滚动条美化
			
			//绑定签名操作事件
			_diy.setCixiu();
		}
		
		//数据请求
		$.ajax({
			url:_diy.api+'selects/all',
			data:{
				cache:_diy.cache,
				csql:_diy.csql,
				style:_diy.style,
				clothing:_diy.clothingCur,
				parentID:pid,
				ids:ids,
				menuId:menuId,
				fabricCode:_diy.strData[_diy.clothingCur].fabric,
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
		$font.off('click').on('click','.item',function(){
			$(this).addClass('cur').siblings().removeClass('cur');
		})
		//选择签名位置
		$position.off('click').on('click','p',function(){
			$(this).addClass('cur').siblings().removeClass('cur');
		})
		//选择签名颜色
		$color.off('click').on('click','.item',function(){
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
		$('#opt-embroidery .saveCxInfo').unbind('click').click(function(){
			var oPosition=$position.children('.cur')
				oFont=$font.find('.cur'),
				oColor=$color.find('.cur');
			if($.trim($con.val())==''){
				luck.alert('系统提示','签名信息不能为空',1);
				return
			}
			if(chEnWordCount($con.val())>14){
				luck.alert('系统提示','签名信息不能超过14个字符',1);
				return	
			}
			if(!/^([A-Za-z0-9]|[\u4e00-\u9fa5]|\s)*$/.test($con.val())){
				luck.alert('系统提示','仅限数字/字母/汉字',1);
				return	
			}
			if(oPosition.length<=0){
				luck.alert('系统提示','请选择刺绣位置',1);
				return	
			}
			if(oColor.length<=0){
				luck.alert('系统提示','请选择刺绣颜色',1);
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
			_diy.strData[_diy.clothingCur].embroidery=arr;
			//alert(arr.join(','))
			luck.alert('系统提示','签名信息保存成功',1);
			setTimeout(function(){
				$('#cat2').hide();
				$('#cat1').show();
			},200);
	
		});
		//删除签名信息
		$('#opt-embroidery .titBar .del').unbind('click').click(function(){
			$('#cxCon').val('');
			$('#cxFont .cur,#cxPosition .cur,#cxColor .cur').removeClass('cur');
			_diy.strData[_diy.clothingCur].embroidery=[];
			setTimeout(function(){
				$('#cat2').hide();
				$('#cat1').show();
			},200)
		})
	},
/*===========================*
		默认值处理 
*============================*/
	
	//获取默认工艺 
	getDefault:function(callback){
		$.ajax({
			url:_diy.api+'inits/dft',	
			data:{
				cache:_diy.cache,
				clothingID:_diy.clothing,
				style:_diy.style,
				'access-token':_diy['access-token'],
			},
			dataType:"jsonp",
			success:function(data){
				_diy.strData=data.data
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
		var data = _diy.strData[_diy.clothingCur],type=obj.type;
		if(type=='fabric'){
			if(data[type]){
				return defaults()
			}else if(obj.selected){
				return 'cur '
			}
		}else if(type=='embroidery'){
			if(data.embroidery.join(',').indexOf(obj.pid+':')>=0){
				return defaults()	
			}else if(obj.selected){
				return 'cur '	
			}
		}else{
			if(data.cstr.join(',').indexOf(obj.pid+':')>=0){
				return defaults()	
			}else if(obj.selected){
				return 'cur '	
			}
		}
		function defaults(){
			if(type=='fabric'){
				if(data.fabric==obj.code){
					return 'cur '
				}
			}else if(type=='embroidery'){
				if(data.embroidery.join(',').indexOf(obj.pid+':'+obj.id)>=0){
					return 'cur '
				}	
			}else{
				if(data.cstr.join(',').indexOf(obj.pid+':'+obj.id)>=0){
					return 'cur '
				}
			}
			return '';
		}
		return '';
	},
	
	//更新默认值
	upDataCstr:function(pid,id,code){
			var str=pid+':'+id,
				flag=null,
				n=0,
				arr=_diy.strData[_diy.clothingCur].cstr,
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
				return 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&desc='+_cotte_share_config.Desc+'&summary=&site='+_cotte_share_config.Site+'&pics='+_cotte_share_config.Pic
			},
			tqq:function(){
				return 'http://share.v.t.qq.com/index.php?c=share&a=index&url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&appkey=&pic='+_cotte_share_config.Pic	
			},
			sqq:function(){
				return 'http://connect.qq.com/widget/shareqq/index.html?url='+_cotte_share_config.Url+'&title='+_cotte_share_config.Text+'&desc='+_cotte_share_config.Desc+'&summary=&site='+_cotte_share_config.Site+'&pics='+_cotte_share_config.Pic
			}
		};

		$('.shareList').on('click','a',function(){
			_diy.loading.show(1);
			var t=$(this).attr('data-cmd'),url='';
			if(t!='wx'){
				var sharePop=window.open(url,'share');
				sharePop.document.write("<div style='text-align:center; line-height:200px;font-size:40px'>加载中...</div>");
			}
			_diy.save(function(res){
				_cotte_share_config.Url=res.shareUrl//更新URL
				_cotte_share_config.Pic=JSON.parse(res.pic).join('||');//分享图片
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
					case 'wx':
						_diy.loading.hide();
						luck.open({
							title:'微信扫一扫',
							width:'300px',
							height:'340px',
							addclass:'cotte-luck',
							content:'<img src="http://qr.liantu.com/api.php?text='+res.shareUrl+'" width="265" height="265" style="display:block;margin:0 auto">'
						});
						return
					break;
					default :
						alert('未定义分享')
				}
				_diy.loading.hide();
				window.open(url,'share');	
			})	
		})
		
	},
	
/*===========================*
	      加入购物车弹层 
*============================*/
	addCart:function(){
		window.addCartCallback=function(e){
			luck.close();
			_diy.loading.show(2);
			_diy.canvasPicEdit(function(){
				$.each(_diy.pic.data,function(name,value){
					_diy.strData[name].image=value;//同步图片数据	
				});
				$.ajax({
					url:'/cart-add.html',
					type:'POST',
					timeout:5000,
					dataType:"json",
					data:{
						cache:_diy.cache,
						csql:_diy.csql,
						type:'diy',
						params:JSON.stringify(_diy.strData),
						size:e
					},
					success:function(data){
						if(data.done){
							window.location.href='/cart.html'
						}else{
							_diy.loading.hide();
							luck.alert('系统提示',data.msg,1);
						}
					},
					error:function(){
						_diy.loading.hide();
						alert('error')
					}
				});	
			})
		}
		//绑定加入购物车事件
		$('#rightBox .addCart').click(function(){
			_diy.checkLogin(function(){
				var cid=[];
				$.each(_diy.catName,function(index,value){
					cid.push(value.id);
				});
				luck.open({
					title:'加入购物车',
					content:'<iframe width="420" height="550" style="display:block" src="measure.html?fn=addCartCallback&cid='+cid.join('|')+'" frameborder="0"></iframe>',
					addclass:'cotte-luck addCartlayer'
				});
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
					obj.html('<i>正</i><i>在</i><i>加</i><i>入</i><i>...</i>');
				break;
				case 3:
					obj.html('<i>保</i><i>存</i><i>数</i><i>据</i><i>中</i>');
				break;
				case 4:obj.html('');
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
	//检测登录状态
	checkLogin:function(callback){
		if(getCookie('hasLogin')==0){
			loginIn.show(function(res){
				if(res.status>0){
					$utk=_diy.pwd=res.msg;
				    setTimeout(callback,120);
				}
			})	
		}else{
			callback();	
		}	
	}
}
//弹层登录
var loginIn={
	callback:null,
	show:function(fn){
		luck.open({
			title:'用户登录',
			width:'580px',
			height:'480px',
			content:'<iframe width="580" height="500" style="display:block" src="view/sc-utf-8/mall/default/user/ajax_login.html" frameborder="0"></iframe>',
			addclass:'cotte-luck',
		});
		loginIn.callback=fn;
	}
}
//图片容错处理
function nofind(obj,id){
	if(id&&_diy.smallPic[id]){
		obj.src=_diy.smallPic[id];
	}else{
		obj.src="http://r.cotte.cn/cotte/diy/pc/images/alpha.gif";
	}
	obj.onerror=null;//控制不要一直跳动
} 

/*--表单验证/S--*/
;(function($){
	$.fn.validate=function(options){
		//提交按钮状态
		var submitBtn={
			flag:false,
			id:$(this).find("input[type='submit']"),
			txt:'提交中...'
		}
		//验证规则
		var validate={
				required:function(e){
					if(e.val()==''){
						_error(e,'required');
						return false
					}
				},
				email:function(e){
					var reg=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'email');
						return false
					}
				},
				phone:function(e){
					var reg=/^1[3|5|8|7]\d{9}$/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'phone');
						return false
					}
				},
				phone_emall:function(e){
					var reg1=/^\w+@([0-9a-zA-Z]+[.])+[a-z]{2,4}$/,reg2=/^1[3|5|8|7]\d{9}$/,val=e.val();
					if((val&&!reg1.test(val))&&(val&&!reg2.test(val))){
						_error(e,'email');
						return false
					}
				},
				number:function(e){
					var val=e.val()
					if(val&&isNaN(e.val())){
						_error(e,'number');
						return false
					}
				},
				url:function(e){
					var reg=/^http:\/\/.+\./,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'url');
						return false
					}
				},
				maxlength:function(e){
					var len=Number(e.attr('maxlength')),val=e.val();
					if(val&&(val.length>len)){
						_error(e,'maxlength');
						return false
					}
				},
				minlength:function(e){
					var len=Number(e.attr('minlength')),val=e.val();
					if(val&&val.length<len){
						_error(e,'minlength');
						return false
					}
				},
				idcard:function(e){
					var reg=/(^\d{15}$)|(^\d{17}([0-9]|X|x)$)/,val=e.val();
					if(val&&!reg.test(val)){
						_error(e,'idcard');
						return false
					}
				}

		}
		//提示信息
		messages = {
			required: "不能为空",
			email: "格式不正确",
			phone:'格式不正确',
			url: "格式不正确",
			number: "请输入数字",
			maxlength: '超出字数限制',
			minlength: '字数长度不够',
			idcard:'格式不正确'
		}
		//合并对象
		if(options){
			if(options.messages){
				messages=$.extend(messages,options.messages)
			}
			if(options.validate){
				validate=$.extend(validate,options.validate)
			}
			if(options.submitBtn){
				submitBtn=$.extend(submitBtn,options.submitBtn)
			}
		}
		//错误提示
		_error=function(obj,error){
			if(options.error){
				options.error(obj,messages[error]);
				return
			}
			alert(messages[error])
		}
		//校验
		function check(){
			 var rule=$(this).attr('validate').split('|'),
					len=rule.length;
				for(var i=0;i<len;i++){
					if(validate.hasOwnProperty(rule[i])){
						if(validate[rule[i]]($(this))==false){
							return false
							break
						}
					}
				}
		}
		$('[validate]',this).off('focus').on('focus',function(e) {
			$(this).removeClass('error-css').siblings('.error').hide();
		})
		$('[validate]',this).off('blur').on('blur',function(e) {
			check.call(e.currentTarget)
        });

		function yanzheng(){
			var success=true;
			$(this).find('[validate]').each(function(i, e) {
			   if(check.call(e)==false){
					success=false
					return false
			   }
            });
			if(success){
				if(submitBtn.flag){
					submitBtn.id.val(submitBtn.txt)
				}
			}
			return success
		}

		if(options.acticle){
			return yanzheng.call(this)
		}else{
			$(this).submit(yanzheng);
		}
	}
})(jQuery);
/*--表单验证/E--*/

//导航
$('.topNav dt').click(function(){
	$(this).next('dd').addClass('show');
	$('.logo').animate({marginLeft:110},'slow');	
});
$('.topNav .close').click(function(){
	$(this).parent('dd').removeClass('show');
	$('.logo').animate({marginLeft:0},'slow');	
});
//cookie操作
function getCookie(c_name){
if (document.cookie.length>0){
 var c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1){ 
    c_start=c_start + c_name.length+1 
    var c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
	return ""
}
function setCookie(c_name,value,expiredays){
	var exdate=new Date()
	exdate.setDate(exdate.getDate()+expiredays)
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}
//温馨提示
function tishi(obj){
	if(obj){
		if(getCookie('tishi')){
			return	
		}
	}
	var tishi=luck.open({
		content:'<h2>温馨提示</h2><p>3D视觉效果与实际面料可能存在色差<br>请以实物为准</p><button>我知道了</button>',
		width:'450px',
		height:'300px',
		addclass:'luck-tishi',
		cloBtn:false,
		callback:function(e){
			$(e).find('button').click(function(){
				$(e).find('.luck-box').animate({width:10,height:10,left:170,opacity:0,top:$(window).height()-40},'slow',function(){
					luck.close(tishi);
					setCookie('tishi',1,365)	
				});
			})	
		}	
	})
	
}
tishi(1);
$('.tishi').click(function(){
	tishi(0);
})


















