$(function() {
	$('.main-profile').each(function() {
		var domElement = $(this);
		
		domElement.find('input[name="EMAIL"]').change(function() {
			domElement.find('input[name="LOGIN"]').val(this.value);
		});
		
		domElement.find('input[name="NEW_PASSWORD"]').change(function() {
			domElement.find('input[name="NEW_PASSWORD_CONFIRM"]').val(this.value);
		});
	});
});