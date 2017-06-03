/**
 * @fileOverview CNC分享组件
 * @copyright (c) 2014 (condenast.com.cn)
 * @author jianjun.wang@condenast.com.cn
 * @version 1.0
 * @depend jquery.js
 */
 
if(typeof CNC=="undefined" || !CNC){
	var CNC = {};
} 

(function($){
	/**
	 * @class 分享组件
	 * @name CNC.Share
	 * @param {object} config { }
	 */
	CNC.Share = function(selector, config){
		var cfg;
		// Handle $(""), $(null), or $(undefined)
		if( !selector ){
			return ;
		}
		this.shareCnt = null;
		if( selector.nodeType || typeof selector === "string" || selector.selector !== undefined ){
			this.shareCnt = $(selector);
			cfg = config;
		}else{
			cfg = selector;
		}

		this.config = {
			site: "",  //1、vogue 2、self 3、gq 4、adstyle 5、cnt 6、feature(专题)
			catalog: "0", //栏目id
			iconSize: 16,

			sTitle: document.title,
			sText: "",
			sPic: "",
			sUrl: window.location.href,
			sDesc: "",

			appkey: {
				tsina: "",
				tqq: ""
			}
			/*ralateUid: "",
			image: {
				tag: "",
				type: "", //list  icon
				pos: "", //top bottom
				bgColor: "" //black
			},
			shareBtns: ""
			 */
		}
		$.extend(this.config, cfg);
		this.init();
	}
	
	CNC.Share.baseUrl = "http://application.self.com.cn/share/front/";
	CNC.Share.paramUri = {
	    "tqq": "collect?platform=tqq&title={title}&url={url}&pic={pic}&site={site}&cat={cat}&appkey={appkey}&hash={hash}",
	    "tsina": "collect?platform=tsina&title={title}&url={url}&pic={pic}&site={site}&cat={cat}&ralateUid={ralateUid}&appkey={appkey}&hash={hash}",
	    "qzone": "collect?platform=qzone&title={title}&desc={text}&summary={desc}&url={url}&pics={pic}&site={site}&cat={cat}&hash={hash}",
	    "renren": "collect?platform=renren&title={title}&description={text}&resourceUrl={url}&pic={pic}&site={site}&cat={cat}&hash={hash}",
	    "douban": "collect?platform=douban&title={title}&text={text}&url={url}&image={pic}&site={site}&cat={cat}&hash={hash}",
	    "weixin": "collect?platform=weixin&title={title}&url={url}&pic={pic}&site={site}&cat={cat}&hash={hash}"		
	};
	CNC.Share.iconsTmpl = '<div class="cnc-share cnc-share-button-{iconSize}">'
			+'<label class="cnc-share-imgshare-lbl">分享到：</label>'
			+'<a class="cnc-share-tsina" data-cmd="tsina" title="分享到新浪微博" href="javascript:;"></a>'
			+'<a class="cnc-share-tqq" data-cmd="tqq" title="分享到腾讯微博" href="javascript:;"></a>'
			+'<a class="cnc-share-qzone" data-cmd="qzone" title="分享到QQ空间" href="javascript:;"></a>'
			+'<a class="cnc-share-renren" data-cmd="renren" title="分享到人人网" href="javascript:;"></a>'
			+'<a class="cnc-share-douban" data-cmd="douban" title="分享到豆瓣" href="javascript:;"></a>'
			+'<a class="cnc-share-weixin" data-cmd="weixin" title="分享到微信朋友圈" href="javascript:;" onclick="return false;"></a>'
			+'<a class="cnc-share-count" data-cmd="count" title="累计分享0次" href="javascript:;">0</a>'
		+'</div>';
	CNC.Share.wxDlgTmpl = '<div class="cnc-share-dialog">'
			+'<div class="cnc-share-dialog-hd">分享到微信朋友圈</div>'
			+'<span class="cnc-share-dialog-close"></span>'
			+'<div class="cnc-share-dialog-bd">'
			+'	<img src="" width="215px" height="215px"/>'
			+'</div>'
			+'<div class="cnc-share-dialog-info">打开微信，点击底部的“发现”，使用“扫一扫”即可将网页分享到我的朋友圈</div>'
		+'</div>';

	CNC.Share.reflux = function(){
		var refer = document.referrer;
		var url = document.location.href;
		var param = location.search;
		var ua = navigator.userAgent;

		if( param.indexOf("tsina-") > -1 || param.indexOf("tqq-") > -1 || param.indexOf("qzone-") > -1 
			|| param.indexOf("renren-") > -1 || param.indexOf("douban-") > -1 || param.indexOf("weixin-") > -1){
			$.getJSON(CNC.Share.baseUrl + "reflux?" + "callback=?", {
				url: url,
				ua: ua,
				param: param
			});
		}
	}

	CNC.Share.prototype={
		init: function(){
			var self = this, cfg = this.config;

			if( self.shareCnt ){
				self.shareCnt.append( self.createIcons(cfg.iconSize) );
				self.shareCnt.click(function(event){
					var target = event.target, cmd = $(target).attr("data-cmd");
					if(cmd && cmd !== "count"){
						self.openShare(cmd);
					}
				})
				self.countObj = self.shareCnt.find(".cnc-share-count");
			    self.getTotal();
			}

			if( cfg.image ){
				self.imageShare();
			}

			if( cfg.shareBtns ){
				self.btnShare();
			}

			self.creatWxDialog();
		},
		createIcons: function(size){
			var t = CNC.Share.iconsTmpl;
			t = t.replace(/\{iconSize\}/, size);
			return t;
		},
		creatWxDialog: function(){
			var dlgMask = $('<div class="cnc-share-mask"></div>');
			var wxDlg = $(CNC.Share.wxDlgTmpl);
			$("body").append( dlgMask );
			$("body").append( wxDlg );

			var self = this;
			self.mask = dlgMask;
			self.wxDialog = wxDlg;
			self.wxQr = self.wxDialog.find("img");

			self.mask.click(function(){
				self.mask.hide();
				self.wxDialog.hide();
			});
			
			$(window).bind('resize', function(){
				self.mask.css({ height: $(document).height() });
			});	
					
			self.wxDialog.find(".cnc-share-dialog-close").click(function(){
				self.mask.hide();
				self.wxDialog.hide();
			});
		},
		showWxDialog: function(){
			var self = this;
			self.mask.css({ height: $(document).height() });
			self.mask.show();
			self.wxDialog.show();
		},
		imageShare: function(){
			var self = this, imgCfg = self.config.image, imgs = $( "img[data-tag=" + imgCfg.tag + "]" );

			self.imgShareCnt = $('<div class="cnc-share-imgshare-cnt"><div class="cnc-share-imgshare-bg"></div></div>');
			self.imgShareCnt.append( self.createIcons( 16 ) );
			self.imgShareCnt.find(".cnc-share-count").remove();

			if( imgCfg.bgColor ){
				self.imgShareCnt.find(".cnc-share-imgshare-bg").css("backgroundColor", imgCfg.bgColor);
			}

			self.imgShareCnt.click(function(event){
				var target = event.target, cmd = $(target).attr("data-cmd");
				if(cmd && cmd !== "count"){
					self.openShare(cmd, "img");
				}
			});
			$("body").append(self.imgShareCnt);

			imgs.mouseenter(function(){
				var target = $(this), pos = target.offset();
				self.imgShareCnt.css({
					width: target.width(),
					left: pos.left,
					top: imgCfg.pos !== "bottom" ? pos.top : (pos.top + target.height() - 36)
				}).show();

				self.trigger = target;
			});
			imgs.mouseleave(function(event){
				var to = $(event.relatedTarget);

				if( to.hasClass("cnc-share") || to.hasClass("cnc-share-imgshare-bg") ){
					return ;
				}
				self.imgShareCnt.hide();
			});
			self.imgShareCnt.mouseleave(function(event){
				self.imgShareCnt.hide();
				self.trigger = null;
			});
		},
		btnShare: function(){
			var self = this, btns = $( self.config.shareBtns );

			self.btnShareCnt = $('<div class="cnc-share-btnshare-cnt"></div>');
			self.btnShareCnt.append( self.createIcons( this.config.iconSize ) );
			self.btnShareCnt.find(".cnc-share-imgshare-lbl").remove();
			self.btnShareCnt.find(".cnc-share-count").remove();

			self.btnShareCnt.click(function(event){
				var target = event.target, cmd = $(target).attr("data-cmd");
				if(cmd && cmd !== "count"){
					self.openShare(cmd, "btn");
				}
			});

			$("body").append(self.btnShareCnt);

			var enterShare = false, timer = null;
			btns.mouseenter(function(){
				var target = $(this), pos = target.offset();

				if(timer){
					clearTimeout(timer);
				}
				self.btnShareCnt.css({
					left: pos.left,
					top: pos.top + target.outerHeight() + 5 + "px"
				}).show();

				self.trigger = target;
			});

			btns.mouseleave(function(event){
				timer = setTimeout(function(){
					if( enterShare ){
						return ;
					}
					self.btnShareCnt.hide();
					timer = null;
				}, 200);
			});
			self.btnShareCnt.mouseenter(function(){
				enterShare = true;
			});
			self.btnShareCnt.mouseleave(function(event){
				self.btnShareCnt.hide();
				self.trigger = null;
				enterShare = false;
			});
		},
		getShareUrl: function( cmd ){
			var self = this, 
				cfg = self.config, cmdUrl, params = {};

			params.title = encodeURIComponent( cfg.sTitle );
			params.url = encodeURIComponent( cfg.sUrl );
			params.pic = encodeURIComponent( cfg.sPic );
			params.text = encodeURIComponent( cfg.sText );
			params.desc = encodeURIComponent( cfg.sDesc );
			params.site = cfg.site;
			params.cat = cfg.catalog;
			params.hash = cfg.hash;

			switch(cmd){
				case "tsina":
					params.title = encodeURIComponent( cfg.sText );
					params.ralateUid = cfg.ralateUid;
					params.appkey = cfg.appkey[cmd];
					break ; 
				case "tqq":
					params.title = encodeURIComponent( cfg.sText );
					params.appkey = cfg.appkey[cmd];
					break ; 
			}

			cmdUrl = CNC.Share.baseUrl + self.formatUri(CNC.Share.paramUri[cmd], params);
			return cmdUrl;
		},
		getBtnShareUrl: function(cmd, type){
			var self = this,
				cfg = self.config, cmdUrl, params = {};

			var sUrl = self.trigger.attr("data-sUrl");
			var sPic = self.trigger.attr("data-sPic");
			var sText = self.trigger.attr("data-sText");
			var sTitle = self.trigger.attr("data-sTitle"); 

			params.title = encodeURIComponent( sTitle );
			params.url = encodeURIComponent( sUrl );
			params.pic = encodeURIComponent( sPic );
			params.text = encodeURIComponent( sText );
			params.desc = "";
			params.site = cfg.site;
			params.cat = cfg.catalog;
			params.hash = cfg.hash;

			if( type == "img" ){
				params.pic = encodeURIComponent( self.trigger.attr("src") );
			}

			switch(cmd){
				case "tsina":
					params.title = encodeURIComponent( sText );
					params.ralateUid = cfg.ralateUid;
					params.appkey = cfg.appkey[cmd];
					break ; 
				case "tqq":
					params.title = encodeURIComponent( sText );
					params.appkey = cfg.appkey[cmd];
					break ; 
			}

			cmdUrl = CNC.Share.baseUrl + self.formatUri(CNC.Share.paramUri[cmd], params);
			return cmdUrl;
		},
		formatUri: function(uri, params){
			var reg, uri = uri;
			for(var k in params){
				reg = new RegExp("{" + k + "}", "g");
				uri = uri.replace(reg, params[k]);
			}
			return uri;
		},
		getTotal: function(){
			var self = this, cfg = self.config;

		    $.getJSON(CNC.Share.baseUrl + "total?callback=?", {
		    	url: cfg.sUrl
		    }, function(data) {
				self.countObj.attr("title", "累计分享" + data.total + "次")
				self.countObj.html(data.total);
				//初始化分享次数并赋值
				cfg.hash = data.url_hash;
		    });
		},
		openShare: function(cmd, type){
			var self = this, total;

			if( !type ){
				if(cmd === "weixin"){
					self.wxShare(cmd);
				}else{
					window.open( self.getShareUrl(cmd) );
				}
				// +1
				total = parseInt(self.countObj.html(), 10) + 1;
				self.countObj.attr("title", "累计分享" + total + "次")
				self.countObj.html(total);
			}else{
				if(cmd === "weixin"){
					self.wxShare(cmd, type);
				}else{
					window.open( self.getBtnShareUrl(cmd, type) );
				}
			}
		},
		wxShare: function(cmd, type){
			var self = this, url = self.getShareUrl(cmd);

			if(type){
				url = self.getBtnShareUrl(cmd, type);
			}
			$.getJSON(url + "&callback=?", function(data) {
			    self.wxQr.attr("src", data.erweima);
				self.showWxDialog();
				if(navigator.appVersion.indexOf("MSIE 6.0") != -1){
					self.mask.hide();
					self.wxDialog.css("top", $(window).height()/2 + $(window).scrollTop() + "px");
				}
		    });
		},
		resetShareData: function(data){
			$.extend(this.config, data);
		}
	}
	//回流统计
	CNC.Share.reflux();
})(jQuery);