/*Sergey Zaragulov skype: deeserge icq: 287295769 sergeland@mail.ru*/
(function($, global){ /*start countdownsl*/
"use strict";

	$.fn.countdownsl = function(options){	
		options = options || {};	
		return this.each(function(){ //return jQuery obj
			new Sergelandcountdown($(this), options);
		});			
	};	
	
	function Sergelandcountdown(container, options){

		var self = this, config = {},
			time = new Date().getTime(),
			countdownSaleTo = new Date(options).getTime(),
			timeCounter = countdownSaleTo - time,
			second = 1000,
			minute = second * 60,
			hour = minute * 60,
			day = hour * 24,
			seconds = 0,
			minutes = 0,
			hours = 0,
			days = 0;
				
		if(timeCounter > 0){ // localisation
		
			days = Math.floor(timeCounter / day);
			timeCounter = timeCounter % day;

			hours = Math.floor(timeCounter / hour);
			timeCounter = timeCounter % hour;

			minutes = Math.floor(timeCounter / minute);
			timeCounter = timeCounter % minute;
			
			seconds = Math.floor(timeCounter / second);			
		}
			
		this.days 	 = container.children(".days"); 
		this.hours 	 = container.children(".hours");
		this.minutes = container.children(".minutes");
		this.seconds = container.children(".seconds");
		
		this.td = days    || options.days 	 || parseInt(this.days.html())    || 0;
		this.th = hours   || options.hours   || parseInt(this.hours.html())   || 0;
		this.tm = minutes || options.minutes || parseInt(this.minutes.html()) || 0;
		this.ts = seconds || options.seconds || parseInt(this.seconds.html()) || 0;

		this.td < 10 ? this.days.html("0" + this.td)    : this.days.html(this.td);		
		this.th < 10 ? this.hours.html("0" + this.th)   : this.hours.html(this.th); 
		this.tm < 10 ? this.minutes.html("0" + this.tm) : this.minutes.html(this.tm); 
		this.ts < 10 ? this.seconds.html("0" + this.ts) : this.seconds.html(this.ts);	
		
		this.ticTac = function(){
			var self = this;			
			if(--self.ts < 0){
				self.ts = 59;
				if(--self.tm < 0){
					self.tm = 59;
					if(--self.th < 0){
						self.th = 23;
						if(--self.td < 0){
							clearInterval(self.interv);	
						}
					}
				}
			}				
			if(self.td >= 0){
				self.td < 10 ? self.days.html("0" + self.td)  	: self.days.html(self.td);
				self.th < 10 ? self.hours.html("0" + self.th) 	: self.hours.html(self.th);
				self.tm < 10 ? self.minutes.html("0" + self.tm) : self.minutes.html(self.tm);
				self.ts < 10 ? self.seconds.html("0" + self.ts) : self.seconds.html(self.ts);						
			}			
		};
				
		this.interv = setInterval(function(){self.ticTac()}, 1000);				
	};

})(jQuery, window); /*and countdownsl*/