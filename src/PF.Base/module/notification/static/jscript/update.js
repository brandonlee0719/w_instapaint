$Core.notification = {
	update: function() {
		setTimeout('$.ajaxCall("notification.update", "", "GET");', 1000);
	},
	
	setTitle: function() {
		if (getParam('notification.notify_ajax_refresh') > 0) {
			setTimeout('$.ajaxCall("notification.update", "", "GET");', (getParam('notification.notify_ajax_refresh') * 60000));
		}
	}
};