
var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

var prevMonth = 11;
var prevYear = 2012;

var currentMonth = 0;
var currentYear = 2013;
var table = $('#upcoming-events');
var tableDays = table.find('span');

var inAction = false;

$(document).ready(function(){
	reloadCalendar()
})

$('.next').on('click', function(e){
	e.preventDefault();
	if(inAction) return;
	prevMonth = currentMonth;
	currentMonth++;
	
	if(currentMonth > 11) {
		currentMonth = 1;
		prevYear = currentYear;
		currentYear++;
	} else {
		prevYear = currentYear;
	}
	
	switchItem($('#event-header'), -1);
});

$('.prev').on('click', function(e){
	e.preventDefault();
	if(inAction) return;
	prevMonth = currentMonth;
	currentMonth--;
	if(currentMonth < 0) { 
		currentMonth = 11;
		prevYear = currentYear;
		currentYear--;
	} else {
		prevYear = currentYear;
	}
	switchItem($('#event-header'), 1);
	
});

function switchItem(item, dir){
	inAction = true;
	item.animate({ left : '+='+ 50*dir, opacity : 0}, function(){
		item.animate( { left : '+='+ 100*(dir*-1) }, 0, function(){
			item.html(months[currentMonth] + ' ' + currentYear);
			item.animate( { left : '+='+ 50*dir, opacity : 1}, function(){
				inAction = false;
			})
		})
	});
	reloadCalendar()
}



function reloadCalendar(){

	var d = new Date(currentYear,currentMonth, 1 );
	var firstDay = d.getDay();
	firstDay = firstDay == 0 ? 6 : (firstDay-1)
	var maxDay = DaysInMonth(currentMonth, currentYear);
	var prevMax = DaysInMonth(prevMonth, prevYear);
	var className = '';
	var events = GetEvents(currentYear, currentMonth)
	var day = 1;
		$.each(table.find('tr'), function(i,v){
			$.each($(v).find('td span'), function(j,k){	
				$(k).animate({opacity: 0}, function(){
				$(k).attr('class', className);
				
				if(i == 1){
					if(j >= firstDay){
						$(k).html(day);
						if(day < maxDay) {
							day++;
						}
					} else {
						$(k).html(prevMax-(firstDay-j-1));
						$(k).addClass('nextmonth');
					}
				} else {
					$(k).html(day);
					if(day < maxDay){
						day++;
					} else { 
						day = 1;
						className = 'nextmonth';
					};	
				}
					$(k).animate({opacity: 1})
					
					$(k).removeClass('party');
					$(k).parent().removeClass('party-day');

					if(events[day] != undefined && events[day] != ''){

						$(k).html($(k).html()+events[day])
						$(k).addClass('party');
						$(k).parent().addClass('party-day');
					}
				})
				
			});

		});

}

$('.event-info').live('mouseover', function(){
	
	$(this).stop().animate({opacity : 1, height: 316, top : -10})
	
})

$('.event-info').live('mouseout', function(){
	
	$(this).stop().animate({opacity : 0, height: 100, top : - 30})
	
})

function GetEvents(year,month){

	/* this should be returned from api, number of day and html */
	return {12 : '<div class="event-info"><ul class="what-when"><li><strong>Summer Jam all night</strong></li><li><p>12/01/2013</p></li><li><a href="page-event-info.html"><img alt="" src="img/common/poster.png"></a></li></ul><a href="page-event-info.html" class="btn btn-small">View poster</a></div>', 17 : '<div class="event-info"><ul class="what-when"><li><strong>Summer Jam all night</strong></li><li><p>12/01/2013</p></li><li><a href="page-event-info.html"><img alt="" src="img/common/poster.png"></a></li></ul><a href="page-event-info.html" class="btn btn-small">View poster</a></div>'};
}


function DaysInMonth(month, year){return 32 - new Date(year, month, 32).getDate()}