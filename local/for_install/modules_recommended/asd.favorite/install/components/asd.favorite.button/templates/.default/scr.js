$(document).ready(function(){
	$('.asd_'+sButton+'_button').live('click',function(){
		if (bGuest)
		{
			alert(sMessDeniedGuest);
			return false;
		}
		var id = $(this).attr('id').substr(8);
		var count = parseInt($('#asd_count_'+id).html());
		if ($(this).hasClass('asd_'+sButton+'ed'))
		{
			$('#asd_count_'+id).html(count-1);
			$(this).removeClass('asd_'+sButton+'ed');
			$(this).attr('title', sTitleAddFav);
			$.get('/bitrix/tools/asd_favorite.php', {id : id, action : 'unlike', sessid : sSessId, type : sType, key : sKey});
		}
		else
		{
			$('#asd_count_'+id).html(count+1);
			$(this).addClass('asd_'+sButton+'ed');
			$(this).attr('title', sTitleDelFav);
			$.get('/bitrix/tools/asd_favorite.php', {id : id, action : 'like', sessid : sSessId, type : sType, key : sKey});
		}
	});
});
