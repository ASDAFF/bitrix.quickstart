function REDSIGNGoPro_CompareShowHideAndCopy(){
	if( $('#compare').find('a').length<1 )
	{
		$('.mix').find('.compareandpaginator').hide();
		$('.smartfilter').find('.filtercompare').hide();
	} else {
		var textForCopy = $('#compare').find('.comparelistinner a').html();
		var linkForCopy = $('#compare').find('.comparelistinner a').attr('href');
		if( $('#compare').find('.comparelistinner').length>0 && $('.smartfilter').find('.filtercompare').length>0 )
		{
			$('.smartfilter').find('.filtercompare a').attr('href', linkForCopy );
			$('.smartfilter').find('.filtercompare a').html( textForCopy );
		}
	}
}

$(document).ready(function(){
	
	REDSIGNGoPro_CompareShowHideAndCopy();
	
});