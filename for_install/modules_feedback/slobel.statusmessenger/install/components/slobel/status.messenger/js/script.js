function slobelshow(){
	$.ajax({url: document.location.href,
		type: "POST",			
		data: {socialAjax: "Y", html: $(".messenger").get(0).outerHTML},
		dataType: "json",
		async: false,
		success: function(html){
				if(html.data){
					$(".messenger").html(html.data.replace(/<[\/]*div(.*?)>/g, ''));
				};
			}
	})
}  
$(document).ready(function(){ 
    setInterval('slobelshow()',20000);  
});