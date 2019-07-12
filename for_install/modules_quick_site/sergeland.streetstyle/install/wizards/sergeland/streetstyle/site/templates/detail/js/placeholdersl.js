/*Sergey Zaragulov skype: deeserge icq: 287295769 sergeland@mail.ru*/
(function($, global){ /*start placeholder*/
"use strict";

	//utility methods
	$.fn.extend({
		resetPH: function(){
			this.each(function(){
				$(this).val("");		
				$(this).trigger("blur");			
			});
		}
	});

	$.fn.placeholdersl = function(options){

		options = options || {};
		
		return this.each(function(){ //return jQuery obj
		
		    // если data-value пустое, пропустим инициализацию
			if (!$(this).attr("data-value")) return true;		   

			var opt = $.extend({}, options),
				css = opt.css || {},
			   elem = $(this);

			css.position 	= "absolute";			
			css.top 	 	= 0;
			css.left 	 	= 0;
			
			//css.width  		= css.width  || elem.innerWidth();
			//css.height 		= css.height || elem.innerHeight();
			
			if($(this).is("textarea")) elem.css({"resize":"none"});
			
			var placeholder = $("<div />").addClass("placeholder").addClass(opt.nameClass).css(css).html(elem.attr("data-value")),
				  container = $("<div />").css({"position":"relative", "margin":0, "padding":0}); 
							
			if(elem.val() === "") placeholder.show();
			else placeholder.hide();
				
			elem.before(container);
            $(placeholder).appendTo(container);
			elem.appendTo(container);
			
			if(elem.offset().left !== container.offset().left)
				placeholder.css({"left": elem.offset().left - container.offset().left});
					
			elem.prev().click(function(){
				$(this).hide().next().focus();
			});

			elem.focus(function(){
				elem.prev().hide();
			});
			
			elem.blur(function(){
				if(elem.val() === "")		
					elem.prev(".placeholder").show();					
			});
			
		});
	};
	
})(jQuery, window); /*and placeholder*/