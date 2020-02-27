function newsLoader(p){
	var o = this;
	this.root = $(p.root);
	this.newsBlock = $(p.newsBlock, this.root);
	this.newsLoader = $(p.newsLoader);
	this.ajaxLoader = $(p.ajaxLoader);	
	
	this.curPage = 1;	
	this.loadSett = (p.loadSett);
		
	// загружаем дополнительные новости
	this.loadMoreNews = function(){
		var loadUrl = location.href;
			if(location.search != ''){
				loadUrl += '&';
			}
			else{
				loadUrl += '?';
			}
			loadUrl  += 'PAGEN_'+ o.loadSett.navNum +'=' + (++o.curPage);
			
			o.ajaxLoader.show();
	
		$.ajax({
				url: loadUrl,
				type: "POST",
				data:{
					AJAX: 'Y'					
				}
			})
			.done(function(html){
				 o.newsBlock.append(html);
				 o.ajaxLoader.hide();

				 if(o.curPage == o.loadSett.endPage){
					 o.newsLoader.parent().hide(); 
				 }				
			});
	}
	
	this.init = function(){
		o.newsLoader.click(function(event){
			o.loadMoreNews();
			event.preventDefault();
		})
	}

}