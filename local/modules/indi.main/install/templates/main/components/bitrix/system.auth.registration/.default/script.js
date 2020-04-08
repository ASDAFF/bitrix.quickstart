$(function() {
	$('.system-auth-registration').each(function() {
		var domElement = $(this);
		
		domElement.find('input[name="USER_EMAIL"]').change(function() {
			domElement.find('input[name="USER_LOGIN"]').val(this.value);
		});
		
		domElement.find('input[name="USER_PASSWORD"]').change(function() {
			domElement.find('input[name="USER_CONFIRM_PASSWORD"]').val(this.value);
		});
	});
});