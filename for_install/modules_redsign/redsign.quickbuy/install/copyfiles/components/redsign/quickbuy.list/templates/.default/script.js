/* some params */
var SECOND_IN_DAY = 86400;
var SECOND_IN_HOUR = 3600;
var SECOND_IN_MINUTE = 60;
var ts = 0;
var TIME_LIMIT = 0;
var TIME_INTERVAL = 0;

/* selectors name with data */
var div_data_selector = ".quickbuy-js-item-data";
var div_data_item = ".quickbuy-item";
var div_data_d = ".quickbuy-js-d";
var div_data_h = ".quickbuy-js-h";
var div_data_m = ".quickbuy-js-m";
var div_data_s = ".quickbuy-js-s";
var div_data_d_mess = ".quickbuy-js-d-mess";
var div_data_h_mess = ".quickbuy-js-h-mess";
var div_data_m_mess = ".quickbuy-js-m-mess";
var div_data_s_mess = ".quickbuy-js-s-mess";

function redsign_quickbuy_init()
{
	if($('.quickbuy').length>0)
	{
		ts = Math.round((new Date()).getTime()/1000);
		$(div_data_selector).each(function(index){
			var DATE_TO = $(div_data_selector+':eq('+index+')').data('date_to');
			TIME_LIMIT = DATE_TO - ts;
			$(div_data_selector+':eq('+index+')').attr('data-time_limit', TIME_LIMIT);
			
			var C_DAYS = Math.floor(TIME_LIMIT/SECOND_IN_DAY);
			var C_HOUR = Math.floor((TIME_LIMIT-C_DAYS*SECOND_IN_DAY)/SECOND_IN_HOUR);
			var C_MINUTE = Math.floor((TIME_LIMIT-C_DAYS*SECOND_IN_DAY-C_HOUR*SECOND_IN_HOUR)/SECOND_IN_MINUTE);
			var C_SECOND = Math.floor(TIME_LIMIT-C_DAYS*SECOND_IN_DAY-C_HOUR*SECOND_IN_HOUR-C_MINUTE*SECOND_IN_MINUTE);
			
			if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_d).length>0)
			{
				if(C_DAYS<10)
					C_DAYS="0"+C_DAYS;
				$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_d).html( C_DAYS );
			}
			
			if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_h).length>0)
			{
				if(C_HOUR<10)
					C_HOUR="0"+C_HOUR;
				$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_h).html( C_HOUR );
			}
			
			if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_m).length>0)
			{
				if(C_MINUTE<10)
					C_MINUTE="0"+C_MINUTE;
				$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_m).html( C_MINUTE );
			}
			
			if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_s).length>0)
			{
				if(C_SECOND<10)
					C_SECOND="0"+C_SECOND;
				$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_s).html( C_SECOND );
			}
		});
		
		$(div_data_selector).each(function(index){
			TIME_INTERVAL = setInterval(function(){
				redsign_quickbuy_counter(index);
			},1000);
		});
	}
}

function redsign_quickbuy_counter(index)
{
	var C_DAYS = $(div_data_selector+':eq('+index+')').find(div_data_d);
	var C_HOUR = $($(div_data_selector+':eq('+index+')')).find(div_data_h);
	var C_MINUTE = $($(div_data_selector+':eq('+index+')')).find(div_data_m);
	var C_SECOND = $($(div_data_selector+':eq('+index+')')).find(div_data_s);
	
	var TIME_LIMIT_ = $(div_data_selector+':eq('+index+')').attr('data-time_limit');
	TIME_LIMIT_ = TIME_LIMIT_ - 1;
	$(div_data_selector+':eq('+index+')').attr('data-time_limit', TIME_LIMIT_);
	
	var C_DAYS = Math.floor(TIME_LIMIT_/SECOND_IN_DAY);
	var C_HOUR = Math.floor((TIME_LIMIT_-C_DAYS*SECOND_IN_DAY)/SECOND_IN_HOUR);
	var C_MINUTE = Math.floor((TIME_LIMIT_-C_DAYS*SECOND_IN_DAY-C_HOUR*SECOND_IN_HOUR)/SECOND_IN_MINUTE);
	var C_SECOND = Math.floor(TIME_LIMIT_-C_DAYS*SECOND_IN_DAY-C_HOUR*SECOND_IN_HOUR-C_MINUTE*SECOND_IN_MINUTE);
	
	if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_d).length>0)
	{
		if(C_DAYS<10)
			C_DAYS="0"+C_DAYS;
		$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_d).html( C_DAYS );
	}
	
	if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_h).length>0)
	{
		if(C_HOUR<10)
			C_HOUR="0"+C_HOUR;
		$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_h).html( C_HOUR );

	}
	
	if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_m).length>0)
	{
		if(C_MINUTE<10)
			C_MINUTE="0"+C_MINUTE;
		$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_m).html( C_MINUTE );
	}
	
	if($(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_s).length>0)
	{
		if(C_SECOND<10)
			C_SECOND="0"+C_SECOND;
		$(div_data_selector+':eq('+index+')').parent(div_data_item).find(div_data_s).html( C_SECOND );
	}
}

$(document).ready(function(){
	redsign_quickbuy_init();
});