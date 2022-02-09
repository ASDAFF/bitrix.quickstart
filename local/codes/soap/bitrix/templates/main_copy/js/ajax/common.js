$(document).ready(function(){
  // —крипты каталога
  var isCatalog = $("div.catalog")[0];
  if(!isCatalog) return;

  // ¬нешн€€ страница каталога: подгрузка новых товаров при прокрутке вниз
	var showYouWatched = true;
	var link = $("#catlistnavnext").attr("href");
	var loading = true;
    if(link) {
	$("#catlistnavnext span").html("<img src='/i/ajax-loader.gif' style='padding: 0 5px;' />  " + $("#catlistnavnext span").html());
	$("#catlistnavnext").click(                
        function(){
                   $.ajax({
                    url: link,
					success : function(data) {
					  var ul = $("ul.quick_view");
					  $(".b-show_more").remove();
					  console.log(ul.html());
					  ul.html(ul.html() + data);
					  loading=false;
					}
					})
					return false;
				}
			);
	}
});
