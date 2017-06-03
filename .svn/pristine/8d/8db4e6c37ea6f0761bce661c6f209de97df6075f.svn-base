/*
 @ Name：麦富迪面料下单
 @ Author：前端老徐
 @ Date：2015/12/25
 @ Blog:http://www.loveqiao.com/
*/
//扩展数组内置对象（数组去重）
Array.prototype.unique = function(){
	var res = [];
	var json = {};
	for(var i = 0; i < this.length; i++){
		if(!json[this[i]]){
			res.push(this[i]);
			json[this[i]] = 1;
		}
	}
	return res;
}
function Diy(opt){
	this.data=opt.data;//工艺数据
	this.rule=opt.rule;//规则
	this.fs=opt.fabric;//面料列表
	this.fcode=opt.fcode;//当前面料code
	this.cid=opt.cid;//当前品类ID
	this.clothing=[]//品类id
	this.catName={'0001':'套装','0003':'西服','0004':'西裤','0005':'马夹','0006':'衬衣','0007':'大衣'};
	this.cstr={};//缓存工艺数据
}

Diy.prototype={
	init:function(){
		_diy=this;
		_diy.clothing=_diy.cid=='0001'?['0003','0004']:[_diy.cid];
		_diy.ngModule();
		_diy.bind();
		_diy.boxFixed();
		_diy.cixiu();
	},
	cixiu:function(){
		$(function(){
			$('.cixiuBox').html('<div class="col-xs-12 col-sm-6"><input type="text" placeholder="请输入刺绣内容" class="form-control" maxlength="14"></div><div class="col-xs-12 col-sm-6"><p class="text-left form-control-static text-muted">注：最多可输入14个字符或7个汉字</p></div>');	
		})	
	},
	//检测登录状态
	checkLogin:function(callback){
		if($_uid==0){
			loginIn.show(function(res){
				if(res.status>0){
					$_uid=1;
				    setTimeout(callback,120);
				}
			})	
		}else{
			callback();	
		}	
	},

	/*绑定事件*/
	bind:function(){
		//分类切换
		$(document).on('click','.cat .btn',function(){
			$(this).removeClass('btn-default').addClass('btn-primary').siblings('.btn-primary').removeClass('btn-primary').addClass('btn-default');	
		});
		//绑定工艺提示
		$(function(){
			$('[data-toggle="popover"]').popover()
		});
		$('#submit').click(function(){
			_diy.checkLogin(function(){
				var flag=true,cname=[];
				$.each(_diy.getCstr(),function(i,d){
					$.each(d,function(i,d){
						if(d.id==''){
							cname.push(d.name)
							flag=false;
						}
					})
				});
				if(!flag){
					_diy.alert(cname.join('、')+' <span class="text-danger">不能为空</span>');
				}else{
					//中英文统计(一个中文算两个字符)  
					function chEnWordCount(str){  
						var count = str.replace(/[^\x00-\xff]/g,"**").length;  
						return count;  
					} 
					var $cx=$('.cixiuBox input');
					if($cx.length){
						if(chEnWordCount($cx.val())>14){
							_diy.alert('签名信息不能超过14个字符');
							return	
						}
						if(!/^([A-Za-z0-9]|[\u4e00-\u9fa5]|\s)*$/.test($cx.val())){
							_diy.alert('签名内容仅限数字/字母/汉字');
							return	
						}
					}
					//弹出量体前置
					$('#sizePage').attr('src','/measure.html?fn=addCart&cid='+_diy.clothing.join('|'));
					$('#sizeSelect').modal();
				}
			})
		})
	},
	//更新、获取，当前工艺信息
	getCstr:function(){
		if(_diy.cid=='0001'){
			_diy.cstr['0003']={};
			_diy.cstr['0004']={};
		}else{
			_diy.cstr[_diy.cid]={};	
		}
		$('#JS-opt input').each(function(i, e) {
			if(e.type=='button'||e.type=='text'||$(e).hasClass('null')||$(this).parents('.disabled').length>0){return}
			var obj=_diy.cstr[e.getAttribute('data-cid')];
			if(obj[e.name]){
				if(e.checked){
					obj[e.name].id.push(e.value);
				}
			}else{
				obj[e.name]={
					id:e.checked?[e.value]:[],
					name:$('[data-catid='+e.name+']').text()
				};	
			}
		});
		return _diy.cstr;
	},
	/*查找互斥工艺*/
	findMutex:function(callback){
		_diy.getCstr();
		var same=[],//所有的冲突工艺
			ids=[];//已选的冲突工艺
		$.each(_diy.cstr,function(index,value){//循环 品类
			$.each(value,function(index,value){//循环 单品
				if(value.id==''){return}//如果单品某项工艺没有选则检查下一个工艺
				$.each(_diy.rule,function(i,d){//循环冲突规则数据
					var rules=d.rules.split(',');
					if(rules.length==1){//单一互斥处理
						if(value.id==d.rules){
							same.push(d.collection);
							return false
						}
					}else{//多项组合互斥处理
						var arr=[];
						$.each(_diy.cstr,function(index,value){//循环 品类
							$.each(value,function(index,value){//循环 单品
								if(value.id==''){return}//如果单品某项工艺没有选则检查下一个工艺
								$.each(rules,function(i,d){
									if(d==value.id){
										arr.push(value.id);	
									}	
								})
							})
						});
						function sortNumber(a,b){
							return a - b
						}
						if(arr.sort(sortNumber).join()==rules.sort(sortNumber).join()){
							same.push(d.collection);	
						}
					}
				});
			})
		});

		var arr=same.join(',').split(',').unique();
		$.each(arr,function(i,d){
			var obj=document.getElementById('v-'+d);
			if(obj&&obj.checked){
				ids.push([obj.getAttribute('data-title'),d]);
			}	
		})
		if(typeof callback=='function'){
			callback(ids,arr);//已选冲突工艺，所有冲突工艺
		}
	},
	
	/*移除互斥工艺*/
	removeMutex:function(ids,arr){//已选冲突工艺，全部冲突工艺
		//处理工艺冲突
		$('.disabled').removeClass('disabled');
		$.each(arr,function(i,d){
			$('#c-'+d).parent().addClass('disabled').removeClass('cur');	//为冲突工艺置灰、移除选中标识
			try{
				var oInput=document.getElementById('v-'+d);
				if(oInput.checked){//检测有值的隐藏域清空并更新冲突记录
					oInput.checked=false;
				}
			}catch(e){}
		});

		//提示用户系统自动处理掉的已选冲突工艺
		if(ids){
			var t=[];
			$.each(ids,function(i,d){
				t.push(d[0]);	
			});
			alert('系统已为您自动排除【'+t.join('、')+'】冲突工艺！');
		}
	},
	
	/*NG模块控制器*/
	ngModule:function(){
		var module=angular.module('DIY', []);
		module.config(['$interpolateProvider', function($interpolateProvider) {
		  $interpolateProvider.startSymbol('[[');
		  $interpolateProvider.endSymbol(']]');
		}]);
		module.controller('ctrl_1', function($scope, $http) {
			$scope.data = _diy.data;//工艺数据
			$scope.data2= [];//指定工艺数据
			$scope.data3=_diy.fs;//可选面料数据
			$scope.data4=[];//面料详情
			$scope.data5=(function(){//已选工艺
				var arr={};
				if(_diy.cid=='0001'){
					arr['0003']={};
					arr['0004']={};
				}else{
					arr[_diy.cid]={};	
				}	
				return arr
			})();
			$scope.cat_format=function(n){return _diy.catName[n]}//格式化品类名称
			//初始化面料详情
			function getFinfo(f){
				$http.get('/fdiy-agf-'+_diy.cid+'-'+f+'.html').success(function(response) {
					$scope.data4=response;
				})
			}
			if(location.href.match('http://')){
				getFinfo(_diy.fcode);//获取面料信息
			}
			//更换面料
			$scope.editFabric=function(){
				var t=document.getElementById('fabricSelect').value;
				if(t){
					getFinfo(t);//获取面料信息
				}
			}
			//品类选项卡
			$scope.tab=function(n){
				$('#JS-cat li').eq(n).addClass('active').siblings().removeClass('active');
				$('#tabLayer'+n).siblings().addClass('ng-hide').end().removeClass('ng-hide');
				$('.cstrBox>div').eq(n).siblings().hide().end().show();
			}
			//工艺分类选项卡
			$scope.catTab=function(n,e){
				$(e.target).parent().parent().parent().find('.opt').eq(n).removeClass('ng-hide').siblings('.opt').addClass('ng-hide');
			}
			//更新已选工艺
			$scope.updata=function(){
				$scope.data5=(function(){
					var arr={};
					$.each(_diy.cstr,function(key,val){
						arr[key]={}
						$.each(val,function(i,d){
							if(i==367||i==162){return}//屏蔽驳头宽在右侧显示
							if(d.id.length>=1){
								$.each(d.id,function(i,d){
									var obj=$('#v-'+d.split('|||')[0]);
									arr[key][d]={};
									arr[key][d].img=obj.attr('data-img');
									arr[key][d].txt=obj.attr('data-title');	
								})
							}	
						})	
					});
					return arr	
				})();//更新数据
			}
			//选择工艺
			$scope.assign=function(opt){
				var obj=$('#c-'+opt.id);
				if(obj.parent().hasClass('disabled')){return}
				if(opt.assign>0){ //指定工艺
					$http.get('/fdiy-agc-'+opt.cid+'-'+opt.ecode+'.html').success(function(response) {
						$scope.data2=response;
						$('#assign').modal();
						$('#assign .row').off('click').on('click','.item',function(){
							obj.parent().addClass('cur').siblings('[data-assign=0]').removeClass('cur');
							$(':radio[name='+opt.pid+']:checked').each(function(i, e) {
								e.checked=false;
							});
							obj.find(':checkbox')[0].checked=true;
							obj.find(':checkbox').val(opt.id+'|||'+$(this).data('code'))
							obj.find('img').attr('src',$(this).find('img').attr('src'));
							$('#v-'+opt.id).attr('data-img',$(this).find('img').attr('src'))
							$('#assign').modal('hide');
							_diy.getCstr();
							//动画
							_diy.animate({
								pid:opt.pid,
								img:obj.find('img').attr('src'),
								top:obj.offset().top,
								left:obj.offset().left
							});
							setTimeout(function(){
								$('#updata').click();
							},800)
						})
					});
					return;//指定工艺应该不涉及工艺冲突暂时 return
				}else{
					var input=obj.find('input[name='+opt.pid+']');
					obj.parent().addClass('cur').siblings('.cur').removeClass('cur');
					input[0].checked=true;
					$(':checkbox[name='+opt.pid+']:checked').each(function(i, e) {
						e.checked=false;
					});
				}
				//工艺冲突处理
				_diy.findMutex(function(ids,arr){//已选冲突工艺，所有冲突工艺
					if(ids.length>0){
						var t=[];
						$.each(ids,function(i,d){
							t.push(d[0])	
						});
						obj.parent().addClass('error');
						if(window.confirm('该工艺与已选的【'+t.join('、')+'】相冲突\n应用当前工艺系统会自动取消冲突工艺，是否应用当前工艺？')){
							obj.parent().removeClass('error');
							_diy.removeMutex(ids,arr);
							if(opt.assign<=0){
								//动画
								_diy.animate({
									pid:opt.pid,
									img:obj.find('img').attr('src'),
									top:obj.offset().top,
									left:obj.offset().left
								});
								setTimeout(function(){
									$('#updata').click();
								},800)
							}	
						}else{
							obj.parent().removeClass('error');
							document.getElementById('v-'+opt.id).checked=false;//清除隐藏域赋值
							obj.parent().removeClass('cur');//移除标记
						}	
					}else{
						_diy.removeMutex(0,arr);//禁用未选择冲突工艺
						if(opt.assign<=0){
							//动画
							_diy.animate({
								pid:opt.pid,
								img:obj.find('img').attr('src'),
								top:obj.offset().top,
								left:obj.offset().left
							});
							setTimeout(function(){
								$('#updata').click();
							},800)
						}	
					}
				});	
			}
		});
	
	},
	animate:function(opt){
		if(opt.pid==367||opt.pid==162){return}//屏蔽驳头宽动画
		//翻转动画
		if($('.cstrBox').hasClass('out')){
			$('.rightBox').trigger('active');	
		}
		//抛物线动画
		var $img=$('<img src="'+opt.img+'">');
		$img.css({position:'absolute',top:opt.top,left:opt.left,width:90,background:'#eee'});
		$('body').append($img);
		var offset=$('#JS-fixed').offset();	
		$img.animate({top:offset.top,left:offset.left},800,function(){
			$(this).animate({top:offset.top+160,left:offset.left+160,width:89,height:89,opacity:0},600,function(){
				$(this).remove();	
			})
		});
	},
	alert:function(con){
		$('#alert .modal-body').html(con);
		$('#alert').modal()	
	},
	//右侧悬浮
	boxFixed:function(){
		var obj=$('.fixed-1'),t=obj.offset().top;
		$(window).scroll(function(){
			var st=$(window).scrollTop(),h=obj.height();
			
			if(st>=t){
				obj.css({position:'fixed',width:$('.catTab').width()});
				$('.fixed-2').css({position:'fixed',width:$('.cat').width()});
				$('#JS-fixed').css({marginTop:st-100});
			}else{
				obj.removeAttr('style');	
				$('.fixed-2').removeAttr('style');
				$('#JS-fixed').removeAttr('style');
			}
		})
	}
}

//弹层登录
var loginIn={
	callback:null,
	show:function(fn){
		$('#loginPop').modal();
		loginIn.callback=fn;
	}
}

//登录成功关闭弹层
var luck={
	close:function(){
		$('#loginPop').modal('hide');	
	}	
}

//加入购物车
function addCart(e){
	var data=[];
	$.ajax({
		url:'/cart-add.html',
		type:'POST',
		timeout:5000,
		dataType:"json",
		data:{
			type:'fdiy',
			params:JSON.stringify(_diy.getCstr()),//工艺信息
			cixiu:$('.cixiuBox input').val(),//刺绣内容
			fpic:$('.fabricShow img').attr('src'),//面料图片
			fcode:_diy.fcode,//面料code
			size:e//尺码
		},
		success:function(data){
			if(data.done){
				window.location.href='/cart.html'
			}else{
				$('#sizeSelect').modal('hide');
				_diy.alert(data.msg);
			}
		},
		error:function(){
			alert('加入购物车请求出错')
		}
	});	
}

//翻转动画
;(function(){
	// 在前面显示的元素，隐藏在后面的元素
	var eleBack = null, eleFront = null,
		eleList = $(".flip");
	
	// 确定前面与后面元素
	var funBackOrFront = function() {
		eleList.each(function() {
			if ($(this).hasClass("out")) {
				eleBack = $(this);
			} else {
				eleFront = $(this);
			}
		});
	};
	funBackOrFront();
	$(".rightBox").bind("active", function() {
		// 切换的顺序如下
		// 1. 当前在前显示的元素翻转90度隐藏, 动画时间225毫秒
		// 2. 结束后，之前显示在后面的元素逆向90度翻转显示在前
		// 3. 完成翻面效果
		eleFront.addClass("out").removeClass("in");
		setTimeout(function() {
			eleBack.addClass("in").removeClass("out");
			// 重新确定正反元素
			funBackOrFront();
		}, 225);
		return false;
	});
	$(document).on('click','.flip-active',function(){
		$(".rightBox").trigger('active');
		return false;	
	})
})()