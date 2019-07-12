$(document).ready(function(){
	$('a.cancel').click(function(e){
		e.preventDefault()
		document.form1.reset();
	});
});
