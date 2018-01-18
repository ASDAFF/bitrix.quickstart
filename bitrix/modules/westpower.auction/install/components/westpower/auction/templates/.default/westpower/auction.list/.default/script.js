function auctionTimer() 
{	
	$(".timer-val").each(function (el) 
	{
		var stamp = $(this).text()-1;
		$(this).text(stamp);
		var timerId = $(this).attr('id');
		var timeStr = '';
		
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
				$('#timer_'+timerId).addClass('end');
			
			if (d > 0)
				timeStr = _d+':';
			
			timeStr += _h+':'+_m+':'+_s;
			
			$('#timer_'+timerId).text(timeStr);
		}
		else
		{
			$('#timer_'+timerId).text(BX.message('À_CONFIRM'));
		}
	});
	setTimeout(auctionTimer, 1000);
}

$(document).ready(function() {
	auctionTimer();
});