$(function() {
	if ( window.BX ) {
		BX.addCustomEvent( "onFrameDataReceived", function () {
			$(".bj-logo-space [data-toggle='tooltip']").tooltip();
			/*$("#personal_menu").click(function(e) {
				//$(this).tooltip();
				alert("@");
			});*/
		});
	}
});