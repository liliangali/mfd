;!function(win, undefined){

var config = {
    chatlogurl: 'index.php?app=history&ids=',
    aniTime: 200,
    right: -232,
    api: {
		apiAll:'index.php?app=userdemand&act=boardFrind',
        sendurl: "index.php?app=userdemand" //发送消息接口
    },
    user: { //当前用户信息
        name: '游客',
        face: 'images/1.png'
    },
    loadMsgTime:null,
    countMsgTime:null,
    param:{},
    chating: {},
    ws:{},
    hosts: (function(){
        var dk = location.href.match(/\:\d+/);
        dk = dk ? dk[0] : '';
        return 'http://' + document.domain + dk + '/';
    })(),
    stopMP: function(e){
        e ? e.stopPropagation() : e.cancelBubble = true;
    }
}, dom = [$(window), $(document), $('html'), $('body')], webim = {};


//节点
webim.renode = function(){
    var node = webim.node = {
        tabs: $('#xxim_tabs>span'),
        list: $('.xxim_list'),
        xximon: $('#xxim_on'),
        layimFooter: $('#xxim_bottom'),
        xximHide: $('#xxim_hide'),
        layimMin: $('#layim_min')
    }; 
};

//主界面缩放
webim.expend = function(){
    var node = webim.node;
    if(webim.layimNode.attr('state') !== '1'){
        webim.layimNode.stop().animate({right: config.right}, config.aniTime, function(){
            node.xximon.addClass('xxim_off');
            try{
                localStorage.layimState = 1;
            }catch(e){}
            webim.layimNode.attr({state: 1});
            node.layimFooter.addClass('xxim_expend').stop().animate({marginLeft: config.right}, config.aniTime/2);
            node.xximHide.addClass('xxim_show');
        });
    } else {
        webim.layimNode.stop().animate({right: 1}, config.aniTime, function(){
            node.xximon.removeClass('xxim_off');
            try{
                localStorage.layimState = 2;
            }catch(e){}
            webim.layimNode.removeAttr('state');
            node.layimFooter.removeClass('xxim_expend');
            node.xximHide.removeClass('xxim_show');
        });
        node.layimFooter.stop().animate({marginLeft: 0}, config.aniTime);
    }
};

//初始化窗口格局
webim.layinit = function(){
    var node = webim.node;
    
    //主界面
    try{
        if(!localStorage.layimState){       
            config.aniTime = 0;
            localStorage.layimState = 1;
        }
        if(localStorage.layimState === '1'){
            webim.layimNode.attr({state: 1}).css({right: config.right});
            node.xximon.addClass('xxim_off');
            node.layimFooter.addClass('xxim_expend').css({marginLeft: config.right});
            node.xximHide.addClass('xxim_show');
        }
    }catch(e){
        layer.msg(e.message, 5, -1);
    }
};

//聊天窗口
webim.popchat = function(param){
    var node = webim.node, log = {};
    log.success = function(layero){
        layer.setMove();
        //webim.chatbox = layero.find('#layim_chatbox');
        //webim.tabchat(param, webim.chatbox);
		
		webim.chatbox = layero.find('#layim_chatbox');
        log.chatlist = webim.chatbox.find('.layim_chatmore>ul');
		
		log.chatlist.html('<li class="userItem'+ param.id +'"  data-id="'+ param.id +'" type="'+ param.type +'"  id="layim_user'+ param.type + param.id +'"><span>'+ param.name +'</span><span class="xxim_noread noreadhidden">0</span><em>×</em></li>')

        webim.tabchat(param, webim.chatbox);
		
		//最小化聊天窗
        webim.chatbox.find('.layer_setmin').on('click', function(){
            var indexs = layero.attr('times');
            layero.hide();
            node.layimMin.text(webim.nowchat.name).show();
        });

        //关闭窗口
        webim.chatbox.find('.layim_close').on('click', function(){
            var indexs = layero.attr('times');
            layer.close(indexs);
            webim.chatbox = null;
			webim.nowchat = 0;
            config.chating = {};
            config.chatings = 0;
            config.param={};
        });
		
		//关闭某个聊天
        log.chatlist.on('mouseenter', 'li', function(){
            $(this).find('em').show();
        }).on('mouseleave', 'li', function(){
            $(this).find('em').hide();
        });
		log.chatlist.on('click', 'li em', function(e){
            var parents = $(this).parent(), dataType = parents.attr('type');
            var dataId = parents.attr('data-id'), index = parents.index();
            var chatlist = log.chatlist.find('li'), indexs;
            
            config.stopMP(e);
            
            delete config.chating[dataId];
            config.chatings--;
            
            parents.remove();
            $('#layim_area'+ dataType + dataId).remove();
            if(dataType === 'group'){
                $('#layim_group'+ dataType + dataId).remove();
            }
            
            if(parents.hasClass('layim_chatnow')){
                if(index === config.chatings){
                    indexs = index - 1;
                } else {
                    indexs = index + 1;
                }
                //关闭当前窗口后，定位到下一个窗口
                //webim.tabchat(config.chating[chatlist.eq(indexs).attr('type') + chatlist.eq(indexs).attr('data-id')]);
				webim.tabchat(param);
            }
            
            if(log.chatlist.find('li').length === 1){
                log.chatlist.parent().hide();
            } 
        });
		
		//聊天选项卡
        log.chatlist.on('click', 'li', function(){
		
            var othis = $(this), dataType = othis.attr('type'), dataId = othis.attr('data-id');
			//alert(config.chating[dataId]['id']);
            webim.tabchat(config.chating[dataId]);
        });

		
        webim.transmit();
    };
    
    log.html = '<div class="layim_chatbox" id="layim_chatbox">'
            +'<h6>'
            +'<span class="layim_move"></span>'
            +'    <a href="'+ param.url +'" class="layim_face" target="_blank"><img src="'+ param.face +'" ></a>'
            +'    <a href="'+ param.url +'" class="layim_names" target="_blank">'+ param.name +'</a>'
            +'    <span class="layim_rightbtn">'
            +'        <i class="layer_setmin"></i>'
            +'        <i class="layim_close"></i>'
            +'    </span>'
            +'</h6>'
            +'<div class="layim_chatmore" id="layim_chatmore">'
            +'    <ul class="layim_chatlist"></ul>'
            +'</div>'
			+'<div class="layim_groups" id="layim_groups"></div>'
            +'<div class="layim_chat">'
			+'	  <div id="picShow" class="picShow"><span class="close">关闭</span></div>'
            +'    <div class="layim_chatarea" id="layim_chatarea">'
            +'        <ul class="layim_chatview layim_chatthis"  id="layim_area'+ param.type + param.id +'"></ul>'
            +'    </div>'
            +'    <div class="layim_tool">'
            //+'        <i class="layim_addface" title="发送表情"></i>'
            +'      <form id="myForm" enctype="multipart/form-data" method="POST"><a href="javascript:;"><i class="layim_addimage"><input id="upimg_file_layim" name="up_file_layim" type=\"file\" value=\"\"></i></a></form>'
            //+'        <p class="markState">结果：<input type="button" value="满足需求" id="okBtn" class="okBtn"   data-jpid="'+param.jpid+'" data-kfid="'+param.kfid+'" data-kfname="'+param.kfname+'" data-id="'+param.id+'"  data-name="'+param.name+'" ><input type="button" value="暂不满足需求" id="noBtn" class="noBtn"></p>'
            //+'        <p class="markState">记录：<input type="button" value="记录工艺信息" id="record" class="record"></p>'
			+'        <a href="" target="_blank" class="layim_seechatlog"><i></i>聊天记录</a>'
            +'    </div>'
            +'    <div class="layim_write" id="layim_write" data-url="" data-type="text" contenteditable="true"></div>'
            +'    <div class="layim_send">'
            +'        <div class="layim_sendbtn" id="layim_sendbtn">发送</div>'
            +'    </div>'
            +'</div>'
            +'</div>';

    if(config.chatings < 1){
        $.layer({
            type: 1,
            border: [0],
            title: false,
            shade: [0],
            area: ['620px', '493px'],
            move: ['.layim_chatbox .layim_move', true],
            moveType: 1,
            closeBtn: false,
            offset: [(($(window).height() - 493)/2)+'px', ''],
            page: {
                html: log.html
            }, success: function(layero){
                log.success(layero);
            }
        })
    } else {
        // log.chatarea = webim.chatbox.find('#layim_chatarea');
		//对话框左边聊天列表
		log.chatmore = webim.chatbox.find('#layim_chatmore');
        log.chatarea = webim.chatbox.find('#layim_chatarea');
        
        log.chatmore.show();
        
        log.chatmore.find('ul>li').removeClass('layim_chatnow');
        log.chatmore.find('ul').append('<li data-id="'+ param.id +'" type=one id="layim_user' + param.type + param.id +'" class="layim_chatnow userItem'+ param.id +'"><span>'+ param.name +'</span><span class="xxim_noread noreadhidden">0</span><em>×</em></li>');
		
        log.chatarea.find('.layim_chatview').removeClass('layim_chatthis');
        log.chatarea.append('<ul class="layim_chatview layim_chatthis" id="layim_area'+ param.type + param.id +'"></ul>');
		
		//alert(param.id);
		
        webim.tabchat(param);
		
    }
};

//定位到某个聊天队列
webim.tabchat = function(param){
	
    var node = webim.node,keys = param.id,keys_t =param.type + param.id;

	//var node = webim.node, log = {}, keys = param.type + param.id;
    webim.nowchat = param;
	
    webim.chatbox.find('#layim_user'+ keys_t).addClass('layim_chatnow').siblings().removeClass('layim_chatnow');
    webim.chatbox.find('#layim_area'+ keys_t).addClass('layim_chatthis').siblings().removeClass('layim_chatthis');
	
	//初始化聊天记录
    webim.loadMsg();

    //标记未读为已读,并设未读数0
	$('.userItem'+keys).find('.xxim_noread').html(0);
	$('.userItem'+keys).find('.xxim_noread').addClass('noreadhidden');
	
    //头像
    webim.chatbox.find('.layim_face>img').attr('src', param.face);
	//当前用户名
    webim.chatbox.find('.layim_names').text(param.name);
	//当前用户的订单
    //webim.chatbox.find('.layim_order').text(param.order);
    //聊天记录连接
    webim.chatbox.find('.layim_seechatlog').attr('href', config.chatlogurl + param.id);
	
    $('#layim_write').focus();
};

//弹出聊天窗
webim.popchatbox = function(othis){
    var node = webim.node, dataId  = othis.attr('data-id'), param = {
			id: dataId, //用户ID
			name: othis.find('.xxim_onename').text(),  //用户名
			face: othis.find('.xxim_oneface').attr('src'),  //用户头像
			pic:othis.attr('data-pic'),
			type: othis.attr('type'),
			order:othis.text(),
			//sin add
			jpid :othis.attr('data-id'),
			kfid :othis.parent('.xxim_list').attr('data-id'),
			kfname:othis.parent('.xxim_list').attr('data-name')
			//sin add
		}, key = param.type + dataId;
    othis.removeClass("Bounce");
	config.param=param;
	if(!config.chating[dataId]){
        webim.popchat(param);
        config.chatings++;
    } else {
        webim.tabchat(param);
    }
	config.chating[dataId] = param;
    var chatbox = $('#layim_chatbox');
    
    if(chatbox[0]){
        node.layimMin.hide();
        chatbox.parents('.xubox_layer').show();
    }
};

//消息传输 文本
webim.transmit = function(){
    var node = webim.node, log = {};
    node.sendbtn = $('#layim_sendbtn');
    node.imwrite = $('#layim_write');

    //发送
    log.send = function(){
        var data = {
            content: node.imwrite.html(),
            id: webim.nowchat.id,
            sign_key: '', //密匙
            _: +new Date
        };
		
        if(data.content.replace(/\s/g, '') === ''){
            layer.tips('说点啥呗！', '#layim_write', 2);
            node.imwrite.focus();
        } else {
			var sData     = {};
			var ids = webim.nowchat.id;

        	sData.type     = "say";
        	sData.to_user_id     = ids;
        	sData.content = data.content;
        	sData.from_user_id   = userid;
        	sData.user_name      = username;
        	sData.face           = face;
        	sData.to_user        = 1;
			sData.con_type       = 'text';
        	config.ws.send($.toJSON(sData));
			node.imwrite.html('').focus();
        }
    };
    node.sendbtn.on('click', log.send);
    
    node.imwrite.keydown(function(e){
        if(e.keyCode === 13){
            log.send();
        }
    });
};

//消息传输 文件（语音、图片）
webim.transmitfile = function(){
	
	var node = webim.node;
	var keyid = webim.nowchat.id;
	//判断发送类型，如果是图片只发送图片连接，发送图片时不能携带文字
	var sData    = {};
	var content  = $("#layim_write").attr('data-url');
	var con_type = $("#layim_write").attr('data-type');

	if(con_type == 'img') {//发送img
		sData.content  = content;
		sData.con_type = con_type;
	} else {//发送语音 暂无
		sData.content  = content;
		sData.con_type = con_type;
	}
	
	var ids = webim.nowchat.id;

	sData.type         = "say";
	sData.to_user_id   = ids;
	sData.from_user_id = userid;
	sData.user_name    = username;
	sData.face         = face;
	sData.to_user      = 1;
	
	config.ws.send($.toJSON(sData));
	node.imwrite.html('').focus();
	//恢复text发送
	$("#layim_write").attr('data-type', 'text');
}

//事件
webim.event = function(){
    var node = webim.node;

    //折叠面板
    node.xximon.on('click', webim.expend);
    node.xximHide.on('click', webim.expend);

    //弹出聊天窗
    config.chatings = 0;
    node.list.on('click', '.userItem', function(){
        var othis = $(this);
        
        webim.popchatbox(othis);
        
    });
	//标记结果
	$(document).on('click','#okBtn',function(){
        var prt = $(this);
		var jpid = prt.attr('data-jpid'),kfid=prt.attr('data-kfid'),kfname=prt.attr('data-kfname'),id=prt.attr('data-id'),name=prt.attr('data-name');
		webim.popPay(jpid,kfid,kfname,id,name);
	});
	$(document).on('click','#noBtn',function(){
		layer.prompt({title: '请填写不满足的理由',border: [0],type: 3,val:'当前生产系统不能满足客户的个性化定制要求！',length:200}, function(val){
			//模拟提交数据
			layer.msg('提交中...',5,-1);
			setTimeout(function(){
				layer.msg('提交成功',1,1);
			},2000)
		});
	});
	
	//记录工艺
	$(document).on('click','#record',function(){
		alert(23453245)	
	});
	
	//聊天发送图片
	$(document).on('change','#upimg_file_layim',function(){
		var options = {
			url:'index.php?app=userdemand&act=chatUpload&r='+(10000*Math.random()),
			success: function(data) {
				var res = eval( "(" + data + ")" );
				if(res.src){
					//$("#layim_write").append('<img src="'+res.src+'" style="max-width:250px;margin-right:10px" >').focus();
					
					//用户储存img url。配合app显示
					$("#layim_write").attr('data-url', res.src);
					$("#layim_write").attr('data-type', 'img');
					
					//执行自动发送操作
					webim.transmitfile();
				}
			}
		};
		
		$('#myForm').ajaxSubmit(options);//上传图片
	});
};

//请求列表数据
webim.getDates = function(index,idArr){
    var api = config.api.apiAll, node = webim.node, myf = node.list.eq(index);
    myf.addClass('loading');
	$.ajax({
		url: api,
		//dataType: 'json',
		success: function(data){
			data=eval('('+data+')');
			//sin bgn
			myf.attr('data-id',data.kf.user_id);
			myf.attr('data-name',data.kf.user_name);
			//sin end
			datas=data.data;
			if(data.status === 1){
				var len = datas.length,str='', friendHtml = [];
				if(len > 0){
					for(var i = 0; i < len; i++){
						var online = datas[i].online == 1 ? "online" : "";
						str='<li type="one" data-id="'+datas[i].id +'" class="userItem userItem'+datas[i].id +'">';
							str+='<div class="userItemName '+ online+'" ><img src="'+ datas[i].face +'" class="xxim_oneface"><span class="xxim_onename">'+ datas[i].name +'</span></div>';
							//未读消息数
							var noreadhidden = !datas[i].msgcount ? "noreadhidden" : "";
							str+='<span class="xxim_noread '+ noreadhidden+'"  >'+ datas[i].msgcount +'</span>';
						str+='</li>';
						friendHtml.push(str);
					}
					myf.html(friendHtml.join(''));
				}else{
					myf.html('<li class="xxim_errormsg">没有任何数据</li>');	
				}
				myf.removeClass('loading');
				if(idArr){
					$(".xxim_list li").each(function(){
		                 var prt = $(this);
		                 $(this).find("p").each(function(){
                    	       if($(this).data("id") == idArr.did && prt.data("id") == idArr.uid){
                 	        	    $(this).click();
                 	           }
		                  })
		            })
				}
			}else{
				myf.html('<li class="xxim_errormsg">'+ data.msg +'</li>');	
			}
			
		},
		error: function(){alert('请求失败')}
	});
};

//获取未读信息数
webim.countNewMsg = function(){
	clearTimeout(config.countMsgTime);
	var exc = config.param.id ? config.param.id : 0;
    $.get("index.php?app=userdemand",{act:"countNewMsgChat",exc:exc}, function(res){
        var res = eval("("+res+")");
        if(res.status == 1){
            $(".xxim_list li").each(function(){
                 var prt = $(this);
                 $(this).find("p").each(function(){
                         for(var i=0;i<res.data.length;i++){
                    	       if($(this).data("id") == res.data[i].dmd_id && prt.data("id") == res.data[i].from_user_id){
                 	        	    $(this).addClass("Bounce");
//                 	        	    $(this).children("font").remove();
//                 	        	    $(this).append("<font></font>");
                 	         }
                        }
                  })
            })
        }
        //config.countMsgTime = setTimeout(function(){webim.countNewMsg()},5000);
    })
};


//获取会话内容
webim.loadMsg=function(){
	clearTimeout(config.loadMsgTime);
	//$id   = config.param.id;
	//$type = config.param.type;
	$id   = webim.nowchat.id;//当前会话框用户id
	$type = webim.nowchat.type;
	
	//alert($id);
	if($id){
		$.get("index.php?app=userdemand", {act:"loadMsgChat",ids:$id},function(res){
	        var res = eval("("+res+")");
	        var msg = '';

	        if(res.done==true){
	        	for(var i=0;i<res.retval.length;i++){
	        		
		        	msg +='<li class="'+ (res.retval[i].to_user == 1 ? 'layim_chateme' : '') +'"><div class="layim_chatuser">';
	                if(res.retval[i].to_user == 1) {
						//alert(res.retval[i].user_name);
	                    msg +='<span class="layim_chattime">'+ res.retval[i].dateline +'</span><span class="layim_chatname">'+ res.retval[i].user_name +'</span><img src="'+ res.retval[i].face +'" >';
	                } else {
	                	msg += '<img src="'+ res.retval[i].face +'" ><span class="layim_chatname">'+ res.retval[i].user_name +'</span><span class="layim_chattime">'+ res.retval[i].dateline +'</span>';      
	                }
		            msg += '</div>'
					//判断发送信息类型con_type:text 文字；con_type:img 图片 con_type:video 语音
					if(res.retval[i].con_type=='img') {
						msg += '<div class="layim_chatsay"><img src="'+res.retval[i].content+'" style="max-width:250px;margin-right:10px" onclick="window.parent.photo(0,[{src:\''+res.retval[i].content+'\'}])"><em class="layim_zero"></em></div>'
					} else if(res.retval[i].con_type == 'video') {
						msg += '<div class="layim_chatsay">'+res.retval[i].content +'<em class="layim_zero"></em></div>'
					} else {
						msg += '<div class="layim_chatsay">'+res.retval[i].content +'<em class="layim_zero"></em></div>'
					}
		            
		            msg += '</li>'
	        	}
				//alert($id);
				
	        	$('#layim_area'+ $type + $id).append(msg);
	        	var writeArea = webim.chatbox.find('#layim_area'+ $type+ $id);
	        	writeArea.scrollTop(writeArea[0].scrollHeight);
	        }
	       // config.loadMsgTime = setTimeout(function(){webim.loadMsg()}, 5000);
	    });
	}
}

//响应用户需求
webim.active = function(){
	$(document).on('click',".active",function(){
		var id = $(this).data("id");
		$.get("index.php?app=userdemand",{act:"active","id":id}, function(res){
			var res = eval("("+res+")");
	 	     if(res.done == false){
	  	    	if(res.retval == "login"){
	  	  	    	location.reload();
	  	    	}
	 	 	 }else{
	 	 		var idArr = {};
	 	 		idArr.uid = res.retval.user_id;
	 	 		idArr.did = res.retval.dmd_id;
	  	 	    webim.getDates(0,idArr);

	  	 	    loadList();
	 	 	 }
		})
	})
}

//满足需求弹出窗
webim.popPay=function(jpid,kfid,kfname,id,name){
	var html= "<div class='popPay'>"
	+ "    <div class='step'>"
	+ "        <span>可满足需求</span>"
	+ "        <span class='ico'>→</span>"
	+ "        <span class='cur'>让顾客付款</span>"
	+ "        <span class='ico'>→</span>"
	+ "        <span>量体/收货地址</span>"
	+ "        <span class='ico'>→</span>"
	+ "        <span>工艺下单</span>"
	+ "    </div>"
	+ "    <div class='form'>"
	+ "         <input type='hidden' name='jpid' value='"+jpid+"'>"
	+ "         <input type='hidden' name='id' value='"+id+"'>" 
	+ "         <input type='hidden' name='name' value='"+name+"'>" 
	+ "         <input type='hidden' name='kf_id' value='"+kfid+"'>" 
	+ "         <input type='hidden' name='kf_name' value='"+kfname+"'>"
	+ "        <p>订单金额：<input type='text' class='text' name='order_amount'></p>"
	+ "        <p>作品分类：<input type='text' class='text' name='cloth'></p>"
	+ "        <p>所属客服："+kfname+"</p>"
	+ "        <p>当前会员："+name+" <input type='button' value='让他付款' id='getPayUrl' class='butn'></p>"
	+ "        <div class='tip'>*提示：提交付款订单后可到 订单管理—拍图即做订单 查看付款状态</div>"
	+ "    </div>"
	+ "</div>"
	$.layer({
		type: 1,
		border: [0],
		title:'让顾客付款',
		//shade: [0],
		area: ['500px', '380px'],
		//move: ['.layim_chatbox .layim_move', true],
		moveType: 1,
		//closeBtn: false,
		offset: [(($(window).height() - 493)/2)+'px', ''],
		page: {
			html:html
		},
		success:function(){
			$('#getPayUrl').off('click').on('click',function(){
				layer.msg('发送成功',1,1);
			})
		}
	})
	
}

webim.socket = function(){
	if ( typeof(MozWebSocket) == 'function' ){
		config.ws = new MozWebSocket('ws://chat.mfd.cn:9900');
		//config.ws = new MozWebSocket('ws://192.168.1.13:9900');
	}else{
		config.ws = new WebSocket('ws://chat.mfd.cn:9900');
		//config.ws = new WebSocket('ws://192.168.1.13:9900');
	}
	
	if(!userid){
		
		alert("用户未登录");
		return config.ws.close();
		//location.reload();
	}
	
	config.ws.onopen = function(){
		//userid = 354;
		//握手成功增加新接入用户
		var data = {};
		data.from_user_id        = userid;
		data.type           = "login";
		data.to_user        = 1;
		//alert($.toJSON(data));
		config.ws.send($.toJSON(data));
	}
	
	config.ws.onclose = function(){
//		var data = {};
//		data.user_id        = userid;
//		data.user_name      = username;
//		data.face           = face;
//		data.type           = "logout";
//		ws.send($.toJSON(data));
	}
	
	config.ws.onmessage = function(e){
		
		var data = $.evalJSON(e.data);
		switch(data.type){
		    case "login":
				$(".xxim_list li").each(function(){//执行登录提示
					if($(this).data("id") == data.to_user_id){
						//$(this).css("color", "red");
						$(this).find('.userItemName').addClass("online");
					}
				})
				break;
		    case "logout"://登出提示
				$(".xxim_list li").each(function(){
					if($(this).data("id") == data.user_id){
						//$(this).css("color", "blue");
						$(this).find('.userItemName').removeClass("online");
					}
				})
		    	break;
				
		    case "say"://发送消息
    			var msg   = '';
    			var layid   = data.to_user == 1 ? data.to_user_id : data.from_user_id;//to_user为1；视为客服信息发送信息
				var loginid = data.to_user == 1 ? data.from_user_id : data.to_user_id;//接收人的id

				var type  = typeof webim.nowchat != "undefined" ? webim.nowchat.type : '';//如果没有打开窗口
				var nowId = typeof webim.nowchat != "undefined" ? webim.nowchat.id : '';
//alert(nowId+'--'+layid);
				//此处皆为模拟
    			if(nowId == layid && loginid == userid ) {
	    			msg +='<li class="'+ (data.to_user == 1 ? 'layim_chateme' : '') +'"><div class="layim_chatuser">';
	                if(data.to_user == 1){//客服发送信息
	                    msg +='<span class="layim_chattime">'+ data.time +'</span><span class="layim_chatname">'+ data.user_name +'</span><img src="'+ data.face +'" >';
	                } else {//客户发送信息
	                	//console.log(res.retval[i].user_name);
	                	msg += '<img src="'+ data.face +'" ><span class="layim_chatname">'+ data.user_name +'</span><span class="layim_chattime">'+ data.time +'</span>';      
	                }
		            msg += '</div>';
					
					//判断发送信息类型con_type:text 文字；con_type:img 图片 con_type:video 语音
					if(data.con_type=='img') {
						msg += '<div class="layim_chatsay"><img src="'+data.content+'" style="max-width:250px;margin-right:10px" onclick="window.parent.photo(0,[{src:\''+data.content+'\'}])"><em class="layim_zero"></em></div>';
					} else if(data.con_type == 'video') {
						msg += '<div class="layim_chatsay">'+ data.content +'<em class="layim_zero"></em></div>';
					} else {
						msg += '<div class="layim_chatsay">'+ data.content +'<em class="layim_zero"></em></div>';
					}
		            msg += '</li>';
					

	    			$('#layim_area' + type + layid).append(msg);
		            var read = {};
		            if(data.to_user == 0){//如果是客户发的消息，标为已读
			            read.from_user_id = data.to_user_id;
						read.to_user_id = data.from_user_id;
						read.to_user = 1;
			            read.type = "read";
			            config.ws.send($.toJSON(read));
		            }
		            log = {};
	    			log.imarea = webim.chatbox.find('#layim_area'+webim.nowchat.type+layid);
	    			log.imarea.scrollTop(log.imarea[0].scrollHeight);
					
    			} else {//如果当前对话框不是发送人的。添加未读消息提醒
					
					var nowKey  = $('.xxim_list .userItem'+layid).attr('data-id');
					if(typeof nowKey != "undefined") {//存在此好友
						var noCount = parseInt($('.xxim_list .userItem'+layid).find('.xxim_noread').text());
						if(!noCount) {
							$('.userItem'+layid).find('.xxim_noread').removeClass('noreadhidden');
						}
						$('.userItem'+layid).find('.xxim_noread').html(noCount+1);
					} else {
						webim.getDates(0);//重新加载客户列表
					}
    			}
    		break;
		}
	}
	
	
}

//渲染骨架
webim.view = (function(){
    var xximNode = webim.layimNode = $('<div id="xximmm" class="xxim_main">'
            +'<div class="xxim_top" id="xxim_top">'
            +'  <div class="xxim_tabs" id="xxim_tabs">联系人</div>'
            +'  <ul class="xxim_list" style="display:block"></ul>'
            +'</div>'
            +'<ul class="xxim_bottom" id="xxim_bottom">'
				//+'<li class="xxim_mymsg" id="xxim_mymsg" title="您有新消息"><i></i><a href=""></a></li>'
				+'<li class="xxim_hide" id="xxim_hide"><i></i></li>'
				+'<li id="xxim_on" class="xxim_icon xxim_on"></li>'
				+'<div class="layim_min" id="layim_min"></div>'
        	+'</ul>'
    +'</div>');
    dom[3].append(xximNode);
    webim.active();
    webim.renode();
    webim.getDates(0);
    webim.event();
    webim.layinit();
    webim.socket();
    webim.countNewMsg();
}());

}(window);

function loadList(){
	clearTimeout(loadTime);
    $.get("index.php?app=userdemand", {act:"loadlist"},function(res){
        var res = eval("("+res+")");
        if(res.done == true){
            $("#theNew").html(res.retval);
        }
        loadTime = setTimeout(loadList,    30000);
    })
}
var loadTime = null;

/*
//发送图片
$(document).ready(function(){

	$(document).on('change','#upimg_file_layim',function(){
		var options = {
			url:'index.php?app=userdemand&act=upload&r='+(10000*Math.random()),
			success: function(data) {
				var res = eval( "(" + data + ")" );
				if(res.src){
					$("#layim_write").append('<img src="'+res.src+'" style="max-width:250px;margin-right:10px" >').focus();
					//用户储存img url。配合app显示
					$("#layim_write").attr('data-url', res.src);
					$("#layim_write").attr('data-type', 'img');
				}
			}
		};
		
		//webim.transmit();
		$('#myForm').ajaxSubmit(options);

	});
})*/

