function QB_get_timer(dateto) {
	var datenow = new Date; //сегодн¤шн¤¤ дата
	datenow = (Date.parse(datenow))/1000; //вычисл¤ет в секундах???
	var diff = dateto - datenow; //до конца акции в секундах
	/* вычисл¤ем дни */
	var days = parseInt((diff / (60 * 60 ))/24);
	if (days < 10) {
		days = "0" + days;
	}
	days = days.toString(); 
	/* вычисл¤ем часы */
	var hours = parseInt((diff / (60 * 60 )) % 24);
	if (hours < 10) {
		hours = "0" + hours;
	}
	hours = hours.toString();    
	/* вычисл¤ем минуты */			
	var minutes = parseInt(diff / (60)) % 60;
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	minutes = minutes.toString();
	/* вычисл¤ем секунды */
	var seconds = parseInt(diff) % 60;
	if (seconds < 10) {
		seconds = "0" + seconds;
	}
	seconds = seconds.toString();
	/* результаты всех вычислений */
	var array = {'days' : days, 'hours' : hours, 'minutes' : minutes, 'seconds' : seconds};
	return array;    
}

function QB_timer(obj) {
	obj.addClass('inited');
	time = QB_get_timer(obj.find(".digits").data('dateto'));
	obj.find(".js-days").html(time.days).data('time', time.days);
	obj.find(".js-hours").html(time.hours).data('time', time.hours);
	obj.find(".js-minutes").html(time.minutes).data('time', time.minutes);
	obj.find(".js-seconds").html(time.seconds).data('time', time.seconds);
	
	if (obj.find(".js-seconds").length!=0) {
		setInterval(function() {
			var hours = parseInt(obj.find(".js-hours").data("time"));
			var minutes = parseInt(obj.find(".js-minutes").data("time"));
			var seconds = parseInt(obj.find(".js-seconds").data("time"));
			seconds--;
			if (seconds<0) {
				seconds = 59;
				minutes--;
			}
			if (minutes<0) {
				minutes = 59;
				hours--;
			}
			if (seconds<10) {
				obj.find(".js-seconds").html("0"+seconds);
			} else if (seconds>=10) {
				obj.find(".js-seconds").html(seconds);			
			}
			if (minutes<10) {
				obj.find(".js-minutes").html("0"+minutes);
			} else if (minutes>=10) {
				obj.find(".js-minutes").html(minutes);
			}
			if (hours<10) {
				obj.find(".js-hours").html("0"+hours);
			} else if (hours>=10) {
				obj.find(".js-hours").html(hours);
			}		
			obj.find(".js-seconds").data("time", seconds).attr("data-time", seconds);
			obj.find(".js-minutes").data("time", minutes).attr("data-time", minutes);
			obj.find(".js-hours").data("time", hours).attr("data-time", hours);
		}, 1000);
	} else {
		var days = obj.find(".js-days").data("time");
		var hours = obj.find(".js-hours").data("time");
		var minutes = obj.find(".js-minutes").data("time");
		if (minutes<10) {
			obj.find(".js-minutes").html("0"+minutes);
		} else if (minutes>=10) {
			obj.find(".js-minutes").html(minutes);
		}
		if (hours<10 && hours.charAt(0)!="0") {
			obj.find(".js-hours").html("0"+hours);
		} else if (hours>=10) {
			obj.find(".js-hours").html(hours);
		}
		if (days<10) {
			obj.find(".js-days").html("0"+days);
		} else if (days>=10) {
			obj.find(".js-days").html(days);
		}
	}
}