//searchInfoWindow.open(marker); //在marker上打开检索信息串口
    function mGetSearchInfoWindow(m_title,logo,address,mobile,synopsis){
	var m_content = '<div style="margin:0;line-height:20px;padding:2px;">' +
                    '<img src="'+logo+'" alt="" style="float:right;zoom:1;overflow:hidden;width:100px;height:100px;margin-left:3px;"/>' +
                    '地址1：'+address+'<br/>电话：'+mobile+'<br/>简介：'+synopsis+'。' +
                  '</div>';
		m_searchInfoWindow = new BMapLib.SearchInfoWindow(map, m_content, {
			title  : m_title,      //标题
			width  : 290,             //宽度
			height : 105,              //高度
			panel  : "panel",         //检索结果面板
			enableAutoPan : true,     //自动平移
			searchTypes   :[
				BMAPLIB_TAB_SEARCH,   //周边检索
				BMAPLIB_TAB_TO_HERE,  //到这里去
				BMAPLIB_TAB_FROM_HERE //从这里出发
			]
		});
		return m_searchInfoWindow;
	}
    function mAddMarker(m_poi,m_searchInfoWindow)
	{
		var m_marker = new BMap.Marker(m_poi); //创建marker对象
		m_marker.enableDragging(); //marker可拖拽
		m_marker.addEventListener("click", function(e){
			m_searchInfoWindow.open(m_marker);
		});
		m_marker.addEventListener("dragging", function(e){
			//console.log(e.point);
			if($("#longitude").length>0){
				$("#longitude").val(e.point.lng);
				$("#latitude").val(e.point.lat);
			}
		});
		map.addEventListener("click", function(e){
			if($("#longitude").length>0){
				$("#longitude").val(e.point.lng);
				$("#latitude").val(e.point.lat);
				
				m_marker.setPosition(e.point);
				
			}
		});

		map.addOverlay(m_marker); //在地图中添加marker
		return m_marker;
	}
    
    
    
    

// 创建CityList对象，并放在citylist_container节点内
var myCl = new BMapLib.CityList({container : "citylist_container", map : map});

// 给城市点击时，添加相关操作
myCl.addEventListener("cityclick", function(e) {
	// 修改当前城市显示
	document.getElementById("curCity").innerHTML = e.name;

	// 点击后隐藏城市列表
	document.getElementById("cityList").style.display = "none";
	map.setZoom(16);
	
	if(typeof(map_region)!='undefined')
	{
		map_region(e.name);
	}
	
});

// 给“更换城市”链接添加点击操作
document.getElementById("curCityText").onclick = function() {
	var cl = document.getElementById("cityList");
	if (cl.style.display == "none") {
		cl.style.display = "";
	} else {
		cl.style.display = "none";
	}	
};

// 给城市列表上的关闭按钮添加点击操作
document.getElementById("popup_close").onclick = function() {
	var cl = document.getElementById("cityList");
	if (cl.style.display == "none") {
		cl.style.display = "";
	} else {
		cl.style.display = "none";
	}	
};




