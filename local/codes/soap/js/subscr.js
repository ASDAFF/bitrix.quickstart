/* Function for ours ajax inquiry  */
function ajaxpostshow(urlres, datares, wherecontent){
       $.ajax({
           type: "POST",
           url: urlres,
           data: datares,
           dataType: "html",
           beforeSend: function(){
                var elementheight = $(wherecontent).height();
                $(wherecontent).prepend('<div class="ajaxloader"></div>');
                $('.ajaxloader').css('height', elementheight);
                $('.ajaxloader').prepend('<img class="imgcode" src="ajax/ajax-loader.gif">');
            },
           success: function(fillter){
                $(wherecontent).html(fillter);
 
           }
      });
}
 
   /* For subscribe */
  $(".b-footer-subcribe form #mailing-submit").live("click",function(){
        var formsubscrube = $(this).parents("form").serialize();
        formsubscrube = formsubscrube + '&action=ajax';
        ajaxpostshow("/includes/subscribe.php", formsubscrube, "#mailing-submit" );
        return false;
   });

  $(document).bind("click.myEvent", function (e) {
		if ($(e.target).closest(".b-fast_order").length == 0) {
			$(".b-fast_order").remove();
		}
	}); 

      /* For fust_order */
  $(".fust_order form #fust_order-submit").live("click",function(){
        var formfastorder = $(this).parents("form").serialize();
        formfastorder = formfastorder + '&action=ajax&el='+$(this).attr('el');
        ajaxpostshow("/includes/fust_order.php", formfastorder, "#fust_order-submit" );
	$(this).parent().parent().remove();
        return false;
   });
