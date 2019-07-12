/*Sergey Zaragulov skype: deeserge icq: 287295769 sergeland@mail.ru*/
(function($, global){ /*start sharesl*/
"use strict";

	$.fn.sharesl = function(){		
		return this.each(function(){ //return jQuery obj		
			var el = $(this),
				url = el.attr("data-url"),
				title = el.attr("data-title") || document.title,
				image = el.attr("data-image") || "",
				description = el.attr("data-description") || $("meta[name=\"description\"]").attr("content") || "";

			url = encodeURIComponent(url);
			title = encodeURIComponent(title).replace('\'', '%27');
			image = encodeURIComponent(image);
			description = encodeURIComponent(description);				
			
			$(".vk", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open("http://vk.com/share.php?url=" + url + "&title=" + title + "&image=" + image + '&description=' + description, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});
			$(".fb", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open('http://www.facebook.com/sharer.php?s=100&p[url]=' + url + '&p[title]=' + title + '&p[summary]=' + description + '&p[images][0]=' + image, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});
			$(".twi", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open('https://twitter.com/intent/tweet?text=' + title + '&url=' + url, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});
			$(".odkl", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open('http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' + url + '&title=' + title, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});
			$(".mail", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open('http://connect.mail.ru/share?url=' + url + '&title=' + title + '&description=' + description + '&imageurl=' + image, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});
			$(".google", el).attr({"rel":"nofollow", "target":"_blank", "href":"#"}).click(function(){
					window.open('https://plus.google.com/share?url=' + url, "_blank", "scrollbars=0, resizable=1, menubar=0, left=100, top=100, width=550, height=440, toolbar=0, status=0");
					return false;			
			});			
		});
	};
	
})(jQuery, window); /*and sharesl*/