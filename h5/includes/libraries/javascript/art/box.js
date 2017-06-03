/* author @yaho.bai 
 *  alert 对话框
 * */
artDialog.alert = function (content) {
	return artDialog({
		id: 'Alert',
		icon: 'warning',
		fixed: true,
		lock: true,
		allowdb:false, //重写artDialog 双击关闭罩层
		content: content,
		ok: true
	});
}; 

/* author @yaho.bai 
 *  alert 对话框  
 *  返回用callback 这样也可以。。。
 * */
artDialog.confirm = function (content, callback) {
	return artDialog({
		id: 'Confirm',
		icon: 'question',
		fixed: true,
		lock: true,
		opacity: .5,
		allowdb:false,
		content: content,
		ok: callback,
		cancel: function () {
			this.close() //目前固定取消为关闭窗口
		}
	});
}; 