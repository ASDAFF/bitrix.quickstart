var bGuestAlert = false;

$(function () {
	if (typeof(asd_fav_afterload)!='undefined' && asd_fav_afterload == 'Y') {
		asd_fav_afterload = 'N';
		$.get('/bitrix/tools/asd_favorite.php', {
			id : asd_fav_IDs,
			action : 'getlike',
			type : sType
		},
		function(data){
			for(i in data.ELEMENTS) {
				$('#asd_count_' + i).html(data.ELEMENTS[i].COUNT);
				if (data.ELEMENTS[i].FAVED) {
					$('#asd_fav_' + i).addClass('asd_'+sButton+'ed');
					$('#asd_fav_' + i).attr('title', sTitleDelFav);
				}
				sSessId = data.OPTIONS.SESSID;
				bGuest = data.OPTIONS.BGUEST;
			}
		});
	}


	$('.asd_' + sButton + '_button').live('click',function(){
		if (bGuest) {
			if (!bGuestAlert) {
				alert(sMessDeniedGuest);
			}
			bGuestAlert = true;
			return false;
		}

		var id = $(this).attr('id').substr(8);
		//var count = parseInt($('#asd_count_'+id).html());
		var curBtn = $(this);

		if ($(this).hasClass('asd_'+sButton +'ed')) {
			$('#asd_count_'+id).html('<small>...<small>');
			$.get('/bitrix/tools/asd_favorite.php', {
				id : id,
				action : 'unlike',
				sessid : sSessId,
				type : sType,
				key : $(this).attr('data-skey')
			}, function(data) {
				$(curBtn).removeClass('asd_'+sButton+'ed');
				$(curBtn).attr('title', sTitleAddFav);
				$('#asd_count_'+id).html(data.COUNT);
			});
		}
		else
		{
			$('#asd_count_'+id).html('<small>...<small>');
			$.get('/bitrix/tools/asd_favorite.php', {
				id : id,
				action : 'like',
				sessid : sSessId,
				type : sType,
				key : $(this).attr('data-skey')
			}, function(data) {
				$(curBtn).addClass('asd_'+sButton+'ed');
				$(curBtn).attr('title', sTitleDelFav);
				$('#asd_count_'+id).html(data.COUNT);
			});
		}
	});
});