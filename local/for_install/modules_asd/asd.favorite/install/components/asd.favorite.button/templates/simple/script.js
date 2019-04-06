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
			for (i in data.ELEMENTS) {
				if (data.ELEMENTS[i].FAVED) {
					$('#asd_fav_' + i).addClass('asd_faved');
					$('#asd_fav_' + i).html(sTitleDelFav);
				}
				sSessId = data.OPTIONS.SESSID;
				bGuest = data.OPTIONS.BGUEST;
			}
		});
	}

	$('.asd_fav_simple').live('click',function(){
		if (bGuest) {
			if (!bGuestAlert) {
				alert(sMessDeniedGuest);
			}
			bGuestAlert = true;
			return false;
		}
		var id = $(this).attr('id').substr(8);
		var curBtn = $(this);
		if ($(this).hasClass('asd_faved')) {
			$.get('/bitrix/tools/asd_favorite.php', {
				id : id,
				action : 'unlike',
				sessid : sSessId,
				type : sType,
				key : $(this).data('skey')
			}, function() {
				$(curBtn).removeClass('asd_faved');
				$(curBtn).html(sTitleAddFav);
			});
		} else {
			$.get('/bitrix/tools/asd_favorite.php', {
				id : id,
				action : 'like',
				sessid : sSessId,
				type : sType,
				key : $(this).data('skey')
			}, function(data) {
				$(curBtn).addClass('asd_faved');
				$(curBtn).html(sTitleDelFav);
			});
		}
		return false;
	});
});