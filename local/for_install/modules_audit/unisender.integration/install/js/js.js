$(document).ready(function(){
	if ($(".uni_export_form").length)
	{
		$("#export_form").submit(function() {
			var submit = false;
			$('input.groups').each(function(index) {
				if ($(this).is(':checked')) {
					submit = true;
				}
			});
			if (submit == false) {
				alert("Выберите группу пользователей для переноса!");
			}

			return submit;
		});
	}
});
