$(document).ready(function() {
	$(".openvideo").fancybox({
		maxWidth	: 800,
		maxHeight	: 500,
		width		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
    	helpers: {
   			overlay: {
    		locked: false 
   					}
 				 }
	}
);
});