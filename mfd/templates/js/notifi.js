//var notify={
//        time:0,
//	apiUrl:function(){//接口地址
//            return '/mfd/index.php?app=user&act=upload_ajax&date='+notify.time;
//	},
//	init:function(tim){
//		tim?tim:10;
//		if(window.webkitNotifications||"Notification" in window){
//			notify.getMsg();
//			setInterval(function(){
//				notify.getMsg();
//			},tim*1000);
//		}
//	},
//	showMsg:function(title, content) {
//        if(!title && !content){
//            title = "桌面提醒";
//            content = "您看到此条信息桌面提醒设置成功";
//        }
//        var iconUrl = "logo.png";
//        if (window.webkitNotifications) {
//            //chrome老版本
//            if (window.webkitNotifications.checkPermission() == 0) {
//                var notif = window.webkitNotifications.createNotification(iconUrl, title, content);
//                /*notif.ondisplay = function(event) {
//					setTimeout(function(){
//						event.currentTarget.cancel();	
//					},3000)	
//				}
//                notif.onerror = function() {}
//                notif.onclose = function() {}
//                notif.onclick = function() {this.cancel();}
//                notif.replaceId = 'Meteoric';*/
//                notif.show();
//            } else {
//                window.webkitNotifications.requestPermission($jy.notify);
//            }
//		}
//        else if("Notification" in window){
//            // 判断是否有权限
//            if (Notification.permission === "granted") {
//                var notification = new Notification(title, {
//                    "icon": iconUrl,
//                    "body": content,
//                });
//				/*setTimeout(function(){
//					notification.close()
//				},3000)*/
//            }
//            //如果没权限，则请求权限
//            else if (Notification.permission !== 'denied') {
//                Notification.requestPermission(function(permission) {
//                    // Whatever the user answers, we make sure we store the
//                    // information
//                    if (!('permission' in Notification)) {
//                        Notification.permission = permission;
//                    }
//                    //如果接受请求
//                    if (permission === "granted") {
//                        var notification = new Notification(title, {
//                            "icon": iconUrl,
//                            "body": content,
//                        });
//						/*setTimeout(function(){
//							notification.close()
//						},3000)*/
//                    }
//                });
//            }
//        }
//    },
//	getMsg:function(){
//		var xmlhttp;
//		if (window.XMLHttpRequest) {
//			xmlhttp = new XMLHttpRequest();
//		} else if (window.ActiveXObject) {
//			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
//		}
//		if (xmlhttp != null) {
//			xmlhttp.onreadystatechange = function(){
//				if (xmlhttp.readyState == 4) {
//					if (xmlhttp.status == 200) {
//                                                var date=new Date();
//                                                notify.time = date.getTime()
//						var data=eval('('+xmlhttp.responseText+')');
//						for(var i=0;i<data.length;i++){
//							(function(i){
//								setTimeout(function(){
//									notify.showMsg(data[i].title,data[i].content);
//								},i*1000)
//							})(i)
//						}
//					} else {
//						//alert("Problem retrieving data:" + xmlhttp.statusText);
//					}
//				}
//		};
//			xmlhttp.open("GET",notify.apiUrl(), true);
//			xmlhttp.send(null);
//		}
//	}
//}
//notify.init(10)