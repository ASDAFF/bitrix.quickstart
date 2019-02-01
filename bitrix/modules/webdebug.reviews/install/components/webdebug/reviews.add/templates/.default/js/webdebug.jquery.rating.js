/**
 *	This jQuery-plugin easy creates rating stars from <div>.
 *	Webdebug web-studio, 2012
 *	www.webdebug.ru
 *	E-mail: info@webdebug.ru
 *	ICQ: 93-37-67
 *	City: Khabarovsk
 */

(function($){
	$.fn.rating = function(action) {
		var options = $.extend({}, $.fn.rating.defaults, action);
		
		var elmRated;
		var jRating;
		var intRatedValue;
		var MoveX, MoveY, MoveRating;
		var RatingID = 1;
		var RatingOffset;
		
		return this.each(function(){
			var DefaultRatingInput = $(this).find("input[type=hidden]");
			var CurrentDefaultRating = options.defaultRating;
			if (DefaultRatingInput.size()>0) {
				DefRating = parseInt(DefaultRatingInput.eq(0).val());
				if (!isNaN(DefRating) && DefRating>0) {
					CurrentDefaultRating = DefRating;
				}
			}
			$(this).html("<input type='hidden' name='"+$(this).attr("id")+"' value='' />");
			$(this).removeAttr("id");
			RatingOffset = $(this).offset();
			RatingOffset = RatingOffset.left;
			jRating = $(this).addClass("rating-stars").css("width", options.starWidth * options.maxRating).attr("id", "rating-" + RatingID);
			RatingID++;
			intRatedValue = jRating.attr("title");
			if (intRatedValue < 1 || isNaN(intRatedValue)) intRatedValue = CurrentDefaultRating;
			if (intRatedValue > options.maxRating) intRatedValue = options.maxRating;
			$(this).find("input[type=hidden]").val(intRatedValue);
			elmRated = $("<div></div>").addClass("rating-stars-rated").attr("rating", intRatedValue).css("width", options.starWidth * intRatedValue);
			jRating.append(elmRated).attr("title", intRatedValue + "/" + options.maxRating);
			
			this.setRating = function(value){
				intValue = parseInt(value);
				if (!isNaN(intValue) && intValue>0) {
					$(this).attr("rating", intValue).find(".rating-stars-rated").attr("rating", intValue).css("width", intValue * options.starWidth);
					$(this).find("input[type=hidden]").val(intValue);
				}
			}
			
			if (options.active) {
				jRating.mousemove(function(event){
					MoveX = event.pageX - RatingOffset;
					MoveRating = parseInt(MoveX/options.starWidth) + 1;
					if (isNaN(MoveRating)) MoveRating = options.defaultRating;
					$(this).attr("title", MoveRating + "/" + options.maxRating).attr("rating", MoveRating);
					$(this).find(".rating-stars-rated").css("width", MoveRating * options.starWidth);
				});
				jRating.mouseout(function(){
					$(this).find(".rating-stars-rated").css("width", parseInt($(this).find(".rating-stars-rated").attr("rating")) * options.starWidth);
				});
				jRating.click(function(){
					$(this).find(".rating-stars-rated").css("width", parseInt($(this).attr("rating")) * options.starWidth);
					if($.isFunction(options.onClick)) {
						options.onClick.call(this,$(this).attr("rating"), $(this).find(".rating-stars-rated").attr("rating"));
					}
				});
			}
		});
		
	};  

	// Default Properties and Events
	$.fn.rating.defaults = {
		maxRating: 10,
		defaultRating: 5,
		starWidth: 12,
		active: false,
		onClick: null
	};    

})(jQuery);

// Apply plugin
$(".webdebug-rating").rating({
	active:true,
	maxRating:5,
	starWidth:16,
	defaultRating:5,
	onClick:function(ratingNew, ratingOld) {
		this.setRating(ratingNew);
	}
});