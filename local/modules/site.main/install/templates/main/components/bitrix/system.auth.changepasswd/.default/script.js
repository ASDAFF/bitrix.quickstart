$(function() {
	$('.system-auth-changepasswd').each(function() {
		var domElement = $(this);
		
		domElement.find('input[name="USER_PASSWORD"]').change(function() {
			domElement.find('input[name="USER_CONFIRM_PASSWORD"]').val(this.value);
		});
	});
});