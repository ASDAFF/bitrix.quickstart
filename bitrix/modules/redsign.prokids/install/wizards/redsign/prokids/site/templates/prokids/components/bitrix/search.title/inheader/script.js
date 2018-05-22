function RSGoPro_SearchTitle()
{
	if( $('.title-search-result').length>0 && $('.title-search-result').is(':visible') )
	{
		var trueH = 32, needAdd = false;
		$('.title-search-result').find('.item.catitem').each(function(i){
			if( $(this).outerHeight()>trueH )
			{
				needAdd = true;
				return false;
			}
		});
		if( $('.title-search-result').find('.stitle').hasClass('twolines') && !needAdd  )
		{
			$('.title-search-result').find('.stitle').removeClass('twolines');
		} else if( !$('.title-search-result').find('.stitle').hasClass('twolines') && needAdd )
		{
			$('.title-search-result').find('.stitle').addClass('twolines');
		}
	}
}

$(document).ready(function(){
	
	setInterval(RSGoPro_SearchTitle, 500);
	
});