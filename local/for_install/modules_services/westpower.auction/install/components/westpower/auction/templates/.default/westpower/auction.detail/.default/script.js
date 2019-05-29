var instanse = false;

function auctionTimer() 
{
	var timeStr = '';
	var timeBegin = $('.js-auction-timestamp-begin').text();
	var timeEnd = $('.js-auction-timestamp-end').text();
	var timeCurrent = Math.round(new Date().getTime() / 1000);
	
	if (timeCurrent < timeBegin)
	{
		var stamp = parseInt($('.js-auction-time-begin').text())-1;
		$('.js-auction-time-begin').text(stamp);
		$('.js-auction-params-title').html(BX.message('A_BEGIN'));		
	}
	else
	{
		var stamp = parseInt($('.js-auction-time-end').text())-1;
		$('.js-auction-time-end').text(stamp);
		$('.js-auction-bets').show();
		$('.js-auction-calc').removeClass('end');
		$('.js-auction-params-title').html(BX.message('CT_BETS_ALONE'));
		$('.informer').text(BX.message('CT_AUCTION_INFO'));
	}
	
	if (stamp > 0)
	{
		var _stamp = stamp;
		var d = Math.floor(_stamp/86400);
		var _d = (d < 10 ? '0' : '') + d;
		_stamp = _stamp - d*86400;
		
		var h = Math.floor(_stamp/3600);
		var _h = (h < 10 ? '0' : '') + h;
		_stamp = _stamp - h*3600;
		
		var m = Math.floor(_stamp/60);
		var _m = (m < 10 ? '0' : '') + m;
		_stamp = _stamp - m*60;
		
		var s = _stamp;
		var _s = (s < 10 ? '0' : '') + s;
		
		if (stamp <= 300)
			$('.js-auction-calc').addClass('end');
		
		if (d > 0)
			timeStr = _d+':';
		
		timeStr += _h+':'+_m+':'+_s;
		
		$('.js-auction-calc').text(timeStr);
		
		setTimeout(auctionTimer, 1000);
	}
	else
	{
		$('.js-auction-bets').hide();
		
		var text = '<div class="timer end">'+BX.message('CT_AUCTION_CONFIRM')+'</div>';
		if (parseInt($('.js-auction-count-bets').text()) <= 0)
			text += '<div class="auction-take-off">'+BX.message('A_LOT_DELETE')+'</div>';
			
		$('.js-auction-calc').html(text);
		$('.informer').text(BX.message('CT_AUCTION_END')+" "+BX.message('CT_BETS_USER_NAME')+'!');
	}
}

function sendToChat(message) {
	if(!instanse && auctionId > 0 && productId > 0) {
		state = $('.auction-chat-state').val();
		$.ajax({
			type: "POST",
			url: "/bitrix/admin/westpower_chat.php",
			data: "method=send&auctionId="+auctionId+"&productId="+productId+"&message="+message+"&state="+state,
			success: function(data) {
				instanse = false;

				if (data.status == 200 && data.data)
				{
					$('.auction-chat-area').append(data.data);
					$('.auction-chat-message').val('');
					$('.auction-chat-area').scrollTop(9999);
					$('.auction-chat-state').val(data.state);
				}
			},
		});
	}
}


function updateChat(auctionId, productId) {
	if(!instanse && auctionId > 0 && productId > 0) {
		state = $('.auction-chat-state').val();
		instanse = true;
		$.ajax({
			type: "POST",
			url: "/bitrix/admin/westpower_chat.php",
			data: "method=get&auctionId="+auctionId+"&productId="+productId+"&state="+state,
			success: function(data) {
				instanse = false;
				
				if (data.status == 200 && data.data)
				{
					$('.auction-chat-area').append($(data.data));
					$('.auction-chat-area').scrollTop(9999);
					$('.auction-chat-state').val(data.state);
				}
			},
		});
	}
}

$(document).ready(function() {
	auctionTimer();
	
	if ($('.js-auction-chat').length)
	{
		updateChat(auctionId, productId);
		setInterval(function() {updateChat(auctionId, productId);}, 5000);
		
		$('.js-auction-chat-send').click(function() {
			var text = $('.js-auction-chat-message').val();

			if (text.length > 0)
				state = sendToChat(text);
			
			return false;
		});
		
		$('.auction-chat-message').keyup(function(event) {
			if (event.which == 13) {
				 var text = $('.auction-chat-message').val();

				if (text.length > 0)
					state = sendToChat(text);
				
				return false;
			}
		});
	}
});