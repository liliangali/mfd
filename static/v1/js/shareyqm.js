	/*--弹窗登陆/E--*/
function sharefs(obj){
	use('/static/expand/layer/layer.min.js',function(){
		var _reload=true;
		window.loginPop=$.layer({
			type: 2,
			title:'发送邀请码',
			shade: [0.3, '#000'],
			area: ['550px','360px'],
			moveType: 1,
			iframe: {src: '../../view/sc-utf-8/mall/default/share/yqm.html'},
			close: function(index){
				_reload=false
			},
			end: function(){
				if(_reload){
					if(typeof obj=='function'){
						obj()
					}else if(typeof obj=='string'){
						if(/^http:\/\//.test(obj)){
							window.location.href=obj;
						}
					}
					setTimeout(function(){top.location.reload()},1500)
				}
			}
		})
	})
}
/*--弹窗登陆/E--*/
/*--模块加载器/S--*/
function use(module, callback, charset){
	var i = 0, head = document.getElementsByTagName('head')[0];
	var module = module.replace(/\s/g, '');
	var iscss = /\.css$/.test(module);
	var node = document.createElement(iscss ? 'link' : 'script');
	var id = module.replace(/\.|\//g, '');
	if(iscss){
		node.type = 'text/css';
		node.rel = 'stylesheet';
	}
	if(charset){
        node.setAttribute("charset",charset);
    }
	node[iscss ? 'href' : 'src'] =module;
	node.id = id;
	if(!document.getElementById(id)){
		head.appendChild(node);
	}else{
		if(callback){
			callback();
		}
	}
	if(callback){
		if(document.all){
			$(node).ready(callback);
		} else {
			$(node).load(callback);
		}
	}
}
/*--模块加载器/E--*/
/*--公共提示/S--*/
