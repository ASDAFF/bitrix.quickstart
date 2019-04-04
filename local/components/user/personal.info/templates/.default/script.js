$(window).bind('load', function(){
	var sendPersonalInfo = function(event){
		var $form = $("#personalForm");
		var $win = $("#elementError"); 
		$.getJSON(ajaxDir + "/ajax.php?" + $form.serialize(), function(data){
			$win.show().find("p").text(data["message"]).parent().find(".heading").text(data["heading"]);
			data["reload"] ? $win.data("reload", 1) : void 0;
		});
		event.preventDefault();
	
	};

	var windowClose = function(event){
		var $win = $("#elementError");
		$win.data("reload") ? document.location.reload() : $("#elementError").hide();
		event.preventDefault();
	};

	$(document).on("click", ".submit", sendPersonalInfo);
	$(document).on("click", "#elementErrorClose, #elementError .close", windowClose);

});