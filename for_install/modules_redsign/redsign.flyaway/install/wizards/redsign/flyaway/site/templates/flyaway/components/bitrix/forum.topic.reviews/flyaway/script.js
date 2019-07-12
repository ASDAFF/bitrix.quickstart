/**var RSGoPro_Separator = ":SEPARATOR:";

function RSGoPro_SummComment(forma)
{
	var $reviewform = $(forma);
	var newVal = $reviewform.find('input[name="REVIEW_TEXT_rate"]').val() + RSGoPro_Separator + 
		$reviewform.find('textarea[name="REVIEW_TEXT_plus"]').val() + RSGoPro_Separator + 
		$reviewform.find('textarea[name="REVIEW_TEXT_minus"]').val() + RSGoPro_Separator + 
		$reviewform.find('textarea[name="REVIEW_TEXT_comment"]').val();
	if($reviewform.find('textarea[name="REVIEW_TEXT_comment"]').val()=="")
		newVal = '';
	$reviewform.find('textarea[name="REVIEW_TEXT"]').val( newVal );
	if( newVal=='' )
	{
		$reviewform.find('textarea[name="REVIEW_TEXT_comment"]').css('border','1px solid red');
		setTimeout(function(){
			$reviewform.find('textarea[name="REVIEW_TEXT_comment"]').css('border','');
		},1200);
		return false;
	} else {
		return true;
	}
}

$(document).ready(function(){
	
	$(document).on('click','.form-buttons', function(){
		var cantpost = false;
		valComm = $.trim($('.form-review').find('.comment').val());
		if(valComm.length < 3){
			$('.form-review').find('.comment').addClass('error_input');
			cantpost = true;
		}
		if($('#captcha_word').length > 0 && $('#captcha_word').val().length < 3){
			$('.form-review').find('#captcha_word').addClass('error_input');
			cantpost = true;
		}
		if(cantpost){
			return false;
		}
	});

	$(document).on('focus', '.form-review .comment', function(){
		if($('.comment').hasClass('error_input'))
			$('.form-review').find('.comment').removeClass('error_input');
	}).on('focus', '.form-review #captcha_word', function(){
		if($('#captcha_word').hasClass('error_input'))
			$('.form-review').find('#captcha_word').removeClass('error_input');
	});
  
  $(document).on("click", "[href='#form_reviews']", function(e) {
      if($(window).width() <= rsFlyaway.breakpoints.sm) {
          $("#form_reviews").toggle();  
      }
  });

	// rating - click
	$(document).on('click','.form-review__rating a',function(){
		var $thisA = $(this);
		var this_id = $thisA.data('id');
		if(!$thisA.hasClass('rating_message'))
		{
			$thisA.parents('.form-review__rating').find('a').each(function(index){
				$(this).addClass('rating__label_empty');
				if( $(this).data('id')<=this_id )
				{
					$(this).removeClass('rating__label_empty');
				}
			});
			$thisA.parents('.form-review__rating').find('input[name="REVIEW_TEXT_rate"]').val( this_id );
		}
		return false;
	});	
});
**/






$(function () {
	
	var separator = ":SEPARATOR:",
		$stars = $(".js-stars > .star"),
		$starsWrapper = $stars.parent(),
		selectedRating = 0;
	
	$(".form-review").on("submit", function (e) {
		
		var $form = $(this),
      reviewPlus = $form.find("[name=REVIEW_TEXT_plus]").val(),
			reviewMinus = $form.find("[name=REVIEW_TEXT_minus]").val(),
			reviewComment = $form.find("[name=REVIEW_TEXT_comment]").val(),
			$reviewText = $form.find("[name=REVIEW_TEXT]");
			
		$reviewText.val(
			selectedRating +
			separator +
			reviewPlus +
			separator +
			reviewMinus + 
			separator + 
			reviewComment
		);
	});
	
	$stars.on("mouseenter", function () {
		var $this = $(this),
			index = $this.data('index');
		$starsWrapper.removeClass("rating-" + selectedRating);
		
		$stars.filter(":lt(" + index + ")").addClass("selected");
	});
	
	$stars.on("mouseleave", function () {
		var $this = $(this);
		$stars.removeClass("selected");
		$starsWrapper.addClass("rating-" + selectedRating);
	});
	
	$stars.on("click", function () {
		var $this = $(this),
			index = $this.data('index');
			
		if(selectedRating == index) {
			$starsWrapper.removeClass("rating-" + selectedRating);
			selectedRating = 0;
			return false;
		}
		if(selectedRating != 0) {
			$starsWrapper.removeClass("rating-" + selectedRating);
		}
		$starsWrapper.addClass("rating-" + index);
		selectedRating = index;
	});
	
});