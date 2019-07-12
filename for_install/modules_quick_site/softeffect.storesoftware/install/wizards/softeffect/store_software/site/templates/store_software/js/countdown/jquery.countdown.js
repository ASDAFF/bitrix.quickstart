/**
 * @name		jQuery Countdown Plugin
 * @author		Martin Angelov
 * @version 	1.0
 * @url			http://tutorialzine.com/2011/12/countdown-jquery/
 * @license		MIT License
 */
(function($){
	
	// Kolichestvo sekund v kajdom vremennom otrezke
	var days	= 24*60*60,
		hours	= 60*60,
		minutes	= 60;
	
	// Sozdaem plagin
	jQuery.fn.countdown = function(prop){
		
		var options = jQuery.extend({
			callback	: function(){},
			timestamp	: 0
		},prop);
		
		var left, d, h, m, s, positions;

		// inicializiruem plagin
		init(this, options);
		
		positions = this.find('.position');
		
		(function tick(){
			
			// Ostalos' vremeni
			left = Math.floor((options.timestamp - (new Date())) / 1000);
			
			if(left < 0){
				left = 0;
			}
			
			// Осталось дней
			d = Math.floor(left / days);
			updateDuo(0, 1, d);
			left -= d*days;
			
			// Ostalos' dney
			h = Math.floor(left / hours);
			updateDuo(2, 3, h);
			left -= h*hours;
			
			// Ostalos' minut
			m = Math.floor(left / minutes);
			updateDuo(4, 5, m);
			left -= m*minutes;
			
			// Ostalos' sekund
			s = left;
			updateDuo(6, 7, s);
			
			// Vyzyvaem vozvratnuyu funkciyu pol'zovatelya
			options.callback(d, h, m, s);
			
			// Planiruem sleduyuschiy vyzov dannoy funkcii cherez 1 sekundu
			setTimeout(tick, 1000);
		})();
		
		// Dannaya funkciya obnovlyaet dve ciforovye pozicii za odin raz
		function updateDuo(minor,major,value){
			switchDigit(positions.eq(minor),Math.floor(value/10)%10);
			switchDigit(positions.eq(major),value%10);
		}
		
		return this;
	};


	function init(elem, options){
		elem.addClass('countdownHolder');

		// Sozdaem razmetku vnutri konteynera
		jQuery.each(['Days','Hours','Minutes','Seconds'],function(i){
			/*jQuery('<span class="count'+this+'">').html(
				'<span class="position">\
					<span class="digit static">0</span>\
				</span>\
				<span class="position">\
					<span class="digit static">0</span>\
				</span>'
			).appendTo(elem);*/
			
			// skylion4ik edition, for IE8
			elem.append('<span class="count'+this+'"></span>');
			elem.find('.count'+this).html(
				'<span class="position">\
					<span class="digit static">0</span>\
				</span>\
				<span class="position">\
					<span class="digit static">0</span>\
				</span>'
			);
			
			if(this!="Seconds"){
				elem.append('<span class="countDiv countDiv'+i+'"></span>');
			}
		});

	}

	// Sozdaem animirovannyy perehod mejdu dvumya ciframi
	function switchDigit(position,number){
		var digit = position.find('.digit')
		
		if(digit.is(':animated')){
			return false;
		}
		
		if(position.data('digit') == number){
			// My uje vyveli dannuyu cifru
			return false;
		}
		
		position.data('digit', number);
		
		var replacement = jQuery('<span>',{
			'class':'digit',
			css:{
				top:'-2.1em',
				opacity:0
			},
			html:number
		});

		// Klass .static dobavlyaetsya, kogda zavershaetsya animaciya.
		// Vypolnenie idet bolee plavno.
		
		digit
			.before(replacement)
			.removeClass('static')
			.animate({top:'2.5em',opacity:0},'fast',function(){
				digit.remove();
			})

		replacement
			.delay(100)
			.animate({top:0,opacity:1},'fast',function(){
				replacement.addClass('static');
			});
	}
})(jQuery);