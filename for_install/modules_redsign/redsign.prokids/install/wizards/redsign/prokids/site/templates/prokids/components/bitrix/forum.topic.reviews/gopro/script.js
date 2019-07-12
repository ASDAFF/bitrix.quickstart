var RSGoPro_Separator = ":SEPARATOR:";

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
	
	// rating - click
	$(document).on('click','.rating a',function(){
		var $thisA = $(this);
		var this_id = $thisA.data('id');
		$thisA.parents('.rating').find('a').each(function(index){
			$(this).removeClass('selected');
			if( $(this).data('id')<=this_id )
			{
				$(this).addClass('selected');
			}
		});
		$thisA.parents('.rating').find('input[name="REVIEW_TEXT_rate"]').val( this_id );
		return false;
	});
	// rating - hover
	$(document).on('mouseenter','.rating a',function(){
		var $thisA = $(this);
		var this_id = $thisA.data('id');
		$thisA.parents('.rating').find('a').removeClass('hover').each(function(index){
			if( $(this).data('id')<=this_id )
			{
				$(this).addClass('hover');
			}
		});
	}).on('mouseleave','.rating a',function(){
		$(this).parents('.rating').find('a').removeClass('hover');
	});
	
});