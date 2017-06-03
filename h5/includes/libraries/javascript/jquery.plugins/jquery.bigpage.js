/**
* jquery ajax分页插件
* 实现功能：
* 1、一次性把数据加载到页面内存，在页面进行分页。
* 2、使用jquery的ajax每次从服务器取数据分页。
* 
* @author xiao5 <xiao5.china@gmail.com>
* @version $Id: jquery.bigpage.js 318 2015-04-27 00:21:26Z xiao5 $
* @copyright Copyright 2014 mfd.com
*/

(function($) {
	var bigPage = new function(){
		this.cssWidgets = [];
		this.ajaxpage = function(param){
			this.config ={
				/*data:二维数组，一次性数据传入data进行页面分页，data与ajaxData只有一个生效，data优先
				 * data格式：{data:[["iteye","iteye.com"],["CSDN","csdn.net"],["qq","qq.com"]]}
				*/
				data: null, 
				
				/*url:后台取数据的地址；params：参数
				 * 返回数据格式为：{data:[["iteye","iteye.com"],["CSDN","csdn.net"],["qq","qq.com"]],totalItems:0}
				 * 返回的数据说明：data：为每次返回的记录，totalItems：为总记录数
				*/
				ajaxData:{url:"",params:{}},
				
				//pageSize:每页的大小，默认是10条记录
				pageSize : 10,
				
				//当前页码
				toPage : 1,
				
				//使用的渲染组件。默认定义了一些组件，用户可以自定义组件注入cssWidgets数组中。
				cssWidgetIds:[],
				
				//分页工具条的位置down：表格下方,up:表格上方,both:上下都有
				position:"down",
				
				maxPageNumCount: 10,  //google分页样式时，最大的分页条上页码显示数量，默认10个			
				
				//回调函数，在分页操作执行后调用的函数，比如点击下一页后再调用这个函数
				callback:null
			};
			$.extend(this.config,param);
			
			
			//是否第一页
    	    this.isFirstPage = function(){
    	    	if(this.config.toPage == 1){
    	    		return true;
    	    	}
				return false;
			};
			
			//第一页
    	    this.firstPage = function(){
    	    	if(this.config.toPage == 1){
    	    		return this;
    	    	}
    	    	this.config.toPage = 1;
    	    	this.applyBuildTable();
				return this;
			};
			
			//上一页
			this.prevPage = function(){
				if(this.config.toPage <= 1){
					return this;
				}
				this.config.toPage --;
				this.applyBuildTable();
				return this;
			};
			//下一页
			this.nextPage = function(){
				if(this.config.toPage >= this.config.totalPage){
					return this ;
				}
				this.config.toPage ++;
				this.applyBuildTable();
				return this;
			};
			//最后一页
			this.lastPage = function(){
				if(this.config.toPage == this.config.totalPage){
					return this;
				}
				this.config.toPage = this.config.totalPage;
				this.applyBuildTable();
				return this;
			};
			
			//是否最后一页
			this.isLastPage = function(){
				if(this.config.toPage == this.config.totalPage){
					return true;
				}			
				return false;
			}
			
			
			//跳转到指定页
    	    this.skipPage = function(toPage_){
            	var numberValue = Number(toPage_);
				if(isNaN(numberValue))return;
            	with(this.config){
            		toPage = numberValue;
            		if(toPage < 1 || toPage > totalPage){
            			toPage = toPage < 1? 1 : totalPage;
            		}
            	}
            	this.applyBuildTable();
				return this;
			};	
			
            //得到分页的数据
            this.getSubData = function(){
				if (this.config.data != null && $.isArray(this.config.data)) {
	            	var totalItems = this.config.totalItems;
	            	if(totalItems <= 0){
	            		return [];
	            	}
	            	var startRow = (this.config.toPage - 1) * this.config.pageSize;
	            	var endRow = this.config.toPage * this.config.pageSize;
	            	if(startRow > totalItems){
	            		return [];
	            	}
	            	if(endRow > totalItems){
	            		endRow = totalItems;
	            	}
	            	return this.config.data.slice(startRow,endRow)
				}else if(this.config.ajaxData.data  && $.isArray(this.config.ajaxData.data)){
					return this.config.ajaxData.data;
				}else{
					return [];
				}
            };			
			
			this.search = function(searchParam){
				this.config.ajaxData.params = this.config.ajaxData.params || {};
				$.extend(this.config.ajaxData.params,searchParam);
				this.config.toPage = 1;
				this.applyBuildTable();				
			}
			
			this.applyBuildTable = function(){
				var $table = this;
				var data = this.config.data;
				if (data != null && $.isArray(data)) {
					this.config.totalItems = data.length;
					this.config.totalPage = totalPageFun(data.length, this.config.pageSize);
					buildTable();
				}else if (!bigPage.isNull(this.config.ajaxData.url) ) {//ajax请求数据
					this.config.ajaxData.params = this.config.ajaxData.params || {};
					$.extend(this.config.ajaxData.params,{toPage:this.config.toPage,pageSize:this.config.pageSize});
					
					$.ajax({
					   type: "POST",
					   url: this.config.ajaxData.url,
					   data: this.config.ajaxData.params,
					   dataType:"json",
					   success: function(result){
					   	$table.config.totalItems = result.totalItems;
						$table.config.totalPage = totalPageFun(result.totalItems, $table.config.pageSize);
						$table.config.ajaxData.data = result.data;
						buildTable();
					   },
					   beforeSend:function(){
					   	$table.addClass("bigpage-ajax-loading");
					   },
					   complete:function(){
					   	$table.removeClass("bigpage-ajax-loading");
					   }
					}); 					
				}
	            //总页数计算函数
				function totalPageFun(totalItems,pageSize){
					if(totalItems <= 0)return 0;
					var totalPage = parseInt((totalItems + pageSize -1)/pageSize,10);
					return isNaN(totalPage)? 0 : totalPage;
				};	
				
				function buildTable(){
					bigPage.applyCssWidget($table);
					if ($table.config.callback && $.isFunction($table.config.callback)){
						$table.config.callback($table)
					}
				}				
			}
			
			this.applyBuildTable();
			return this;
		}
	
	
		this.isNull = function(obj){
			if(obj == null || $.trim(obj) == "" || typeof(obj) == "undefined"){
				return true;
			}
			return false;
		}
		
		//向CssWidget数组中添加渲染组件，会覆盖已有的组件。
		this.addCssWidget = function(cssWidget){
			this.cssWidgets.pushEx(cssWidget)
            return this;
		}
		
        //把渲染组件应用到页面的样式上，默认使用appendToTable，ajaxpageBar1两个组件
       this.applyCssWidget = function($table){
	   		var this_ = this;
        	var cssWidgetIds = $table.config.cssWidgetIds;
        	if(cssWidgetIds.length <= 0){
				cssWidgetIds[0] = "appendToTable";
				cssWidgetIds[1] = "ajaxpageBar1";
        	}else{
				var hasAppendToTable = false;
	    		for(var i=0;i<cssWidgetIds.length;i++){
					if(cssWidgetIds[i] == "appendToTable"){
						hasAppendToTable = true;
					}
	    		}
				if(!hasAppendToTable){
					cssWidgetIds = ["appendToTable"].concat(cssWidgetIds);
				}
			}
	
    		for(var i=0;i<cssWidgetIds.length;i++){
				var cssWidget = getCssWidgetById(cssWidgetIds[i]);
				if(cssWidget){
					cssWidget.format($table);
				}
    		}			
			//根据id从CssWidget中取得组件
	        function getCssWidgetById(name) {
	        	if(this_.isNull(name)){
	        		return false;
	        	}
	            var len = this_.cssWidgets.length;
	            for (var i = 0; i < len; i++) {
	                if (this_.cssWidgets[i].id.toLowerCase() == name.toLowerCase()) {
	                    return this_.cssWidgets[i];
	                }
	            }
	            return false;
	        }
        }		
	
		//扩张Array的push()方法，使数组内的数据不重复。
		Array.prototype.pushEx = function(obj){
			var a = true;
			for (var i = 0; i < this.length; i++) {
				if (this[i].id.toLowerCase() == obj.id.toLowerCase()) {
					this[i] = obj;
					a = false;
					break;		
				}
			}
			if(a){
				this.push(obj);
			}		
			return this.length;
		}		
		
	}
	
	$.extend({bigPage: bigPage});
	$.fn.bigPage = bigPage.ajaxpage;
	
	//添加渲染table内容
	$.bigPage.addCssWidget({
		id:"appendToTable",
		format :function($table){
			var subData = $table.getSubData();
			var $tBody = $table.find("tbody:first");
			var trsArray = [];
			for(var i=0;i<subData.length;i++){
				var cellVaues = subData[i];
				var trArray =[];
				trArray.push("<tr>");
				for(var j=0;j<cellVaues.length;j++){
					trArray.push("<td>");
					trArray.push(cellVaues[j]);
					trArray.push("</td>");
				}
				trArray.push("</tr>");
				trsArray.push(trArray.join(""));
			}
			$tBody.html(trsArray.join(""));
		}
	});
	
	
	//添加分页条组件1
	$.bigPage.addCssWidget({
		id:"ajaxpageBar1",
		format :function($table){
			var prevClass = "current prev";
			var nextClass = "current next";
			if($table.config.toPage > 1){
				prevClass = "prev"
			} 
			if($table.config.toPage < $table.config.totalPage){
				nextClass = "next";
			}
			
			var maxCount = $table.config.maxPageNumCount;//google分页样式时，最大的页码显示数量
			var currentOption = $table.config.toPage;
			var endOption = currentOption + parseInt(maxCount/2) ;
			if(endOption > $table.config.totalPage){
				endOption =  $table.config.totalPage;
			}
			var beginOption = endOption - maxCount + 1;
			if(beginOption <= 0){
				beginOption = 1;
			}			
			var as = "";
			for(var i=beginOption;i<=endOption;i++){
				if(currentOption == i){
					as += "<span class='current'>" + i + "</span>";
				}else{
					as += "<a href='javascript:void(0)' ajaxpage='skip' pageNumber=" + i + "  >" + i + "</a>";
				}
			}				
			var footPageHtml = '<div ajaxpage="foot" class="bigpage"><a class="' + prevClass +'" href="javascript:void(0)" ajaxpage="prev" >上一页</a>' + as + '<a class="' + nextClass + '" href="javascript:void(0)" ajaxpage="next"  >下一页</a>共&nbsp;<span ajaxpage="count" style="color: red" >0</span>&nbsp;页 跳转到 <input type="text" size="5" maxlength="5" ajaxpage="text" > 页  <a href="javascript:void(0)" ajaxpage="skipA" >GO</a></div>';
			$table.siblings("div[ajaxpage='foot']").remove();
			if($table.config.position == "up"){
				$table.before(footPageHtml);
			}else if($table.config.position == "both"){
				$table.before(footPageHtml);
				$table.after(footPageHtml);
			}else{
				$table.after(footPageHtml);
			}
			
			$footDiv = $table.siblings("div[ajaxpage='foot']");
			$footDiv.data("table",$table);
			//a链接注册事件
			$footDiv.find("a").click(function(){
				var $a = $(this);
				var table2 = $a.parent().data("table");
				var opType = $a.attr("ajaxpage");
				if(opType == "prev"){
					table2.prevPage();
				}else if(opType == "next"){
					table2.nextPage();
				}else if(opType == "skip"){
					table2.skipPage($a.attr("pageNumber"));
				}else if(opType == "skipA"){
					table2.skipPage($a.siblings(":text[ajaxpage='text']").val());
				};
			});
			//文本框输入页码按回车跳转
			$footDiv.find(":text[ajaxpage='text']").keyup(function(event){
				var k = event.keyCode;
				if(k == 13){
					$(this).siblings("a[ajaxpage='skipA']").click();
				}
			});		
								
			$footDiv.find("a").each(function(i,v){
				var opType = $(v).attr("ajaxpage");
				if(opType == "first" || opType == "prev"){
					$(v).removeClass().addClass(prevClass);
				}else if(opType == "next" || opType == "last"){
					$(v).removeClass().addClass(nextClass);
				}
			})
			$footDiv.find("span[ajaxpage='count']").html($table.config.totalPage);
			$footDiv.find(":text[ajaxpage='text']").val($table.config.toPage);
		}
	});	
	
	//添加分页条组件2
	$.bigPage.addCssWidget({
		id:"ajaxpageBar2",
		format :function($table){
			
			var prevClass = "current prev";
			var nextClass = "current next";
			if($table.config.toPage > 1){
				prevClass = "prev"
			} 
			if($table.config.toPage < $table.config.totalPage){
				nextClass = "next";
			}
			var $footDiv = $table.siblings("div[ajaxpage='foot']");
			if($footDiv.length <= 0){
				var footPageHtml = '<div ajaxpage="foot" class="bigpage">共&nbsp;<span ajaxpage="count" style="color: red" >0</span>&nbsp;页&nbsp;<a class="' + prevClass + '" href="javascript:void(0)" ajaxpage="first"  >第一页</a><a class="' + prevClass +'" href="javascript:void(0)" ajaxpage="prev" >上一页</a><a class="' + nextClass + '" href="javascript:void(0)" ajaxpage="next"  >下一页</a><a class="' + nextClass + '" href="javascript:void(0)" ajaxpage="last"  >末一页</a>当前&nbsp;<span style="color: red" ajaxpage="current"></span>&nbsp;页 跳转到 <input type="text" size="5" maxlength="5" ajaxpage="text" > 页  <a href="javascript:void(0)" ajaxpage="skip" >GO</a></div>';
				if($table.config.position == "up"){
					$table.before(footPageHtml);
				}else if($table.config.position == "both"){
					$table.before(footPageHtml);
					$table.after(footPageHtml);
				}else{
					$table.after(footPageHtml);
				}
				
				$footDiv = $table.siblings("div[ajaxpage='foot']");
				$footDiv.data("table",$table);
				
				//a链接注册事件
				$footDiv.find("a").click(function(){
					var $a = $(this);
					var table2 = $a.parent().data("table");
					var opType = $a.attr("ajaxpage");
					if(opType == "first"){
						table2.firstPage();
					}else if(opType == "prev"){
						table2.prevPage();
					}else if(opType == "next"){
						table2.nextPage();
					}else if(opType == "last"){
						table2.lastPage();
					}else if(opType == "skip"){
						table2.skipPage($a.siblings(":text[ajaxpage='text']").val());
					}
				});
				//文本框输入页码按回车跳转
				$footDiv.find(":text[ajaxpage='text']").keyup(function(event){
					var k = event.keyCode;
					if(k == 13){
						$(this).siblings("a[ajaxpage='skip']").click();
					}
				});						
			}
			$footDiv.find("a").each(function(i,v){
				var opType = $(v).attr("ajaxpage");
				if(opType == "first" || opType == "prev"){
					$(v).removeClass().addClass(prevClass);
				}else if(opType == "next" || opType == "last"){
					$(v).removeClass().addClass(nextClass);
				}
			})
			$footDiv.find("span[ajaxpage='count']").html($table.config.totalPage);
			$footDiv.find("span[ajaxpage='current']").html($table.config.toPage);
			$footDiv.find(":text[ajaxpage='text']").val($table.config.toPage);
			
		}
	});
	
	
})(jQuery);
