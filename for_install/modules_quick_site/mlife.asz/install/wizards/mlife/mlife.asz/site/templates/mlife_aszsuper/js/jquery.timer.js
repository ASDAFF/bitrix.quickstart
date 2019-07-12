;(function($){
	
	// ���������� ������ � ������ ��������� �������
	var days	= 24*60*60,
		hours	= 60*60,
		minutes	= 60;
	
	// ������� ������
	$.fn.countdown = function(prop){
		
		var options = $.extend({
			callback	: function(){},
			timestamp	: 0,
			showtext : false,
			text_d: false,
			text_h: false,
			text_m: false,
			text_s: false
		},prop);
		
		var left, d, h, m, s, positions;

		// �������������� ������
		
		initcount(this, options);
		
		positions = this.find('.position');
		
		(function tick(){
			
			// �������� �������
			left = Math.floor((options.timestamp - (new Date())) / 1000);
			
			if(left < 0){
				left = 0;
			}
			
			// �������� ����
			d = Math.floor(left / days);
			updateDuo(0, 1, d);
			left -= d*days;
			
			// �������� �����
			h = Math.floor(left / hours);
			updateDuo(2, 3, h);
			left -= h*hours;
			
			// �������� �����
			m = Math.floor(left / minutes);
			updateDuo(4, 5, m);
			left -= m*minutes;
			
			// �������� ������
			s = left;
			updateDuo(6, 7, s);
			
			// �������� ���������� ������� ������������
			options.callback(d, h, m, s);
			
			// ��������� ��������� ����� ������ ������� ����� 1 �������
			setTimeout(tick, 1000);
		})();
		
		// ������ ������� ��������� ��� �������� ������� �� ���� ���
		function updateDuo(minor,major,value){
			switchDigit(positions.eq(minor),Math.floor(value/10)%10);
			switchDigit(positions.eq(major),value%10);
		}
		
		return this;
	};


function initcount(elem, options){
	elem.addClass('countdownHolder');

	// ������� �������� ������ ����������
	$.each(['Days','Hours','Minutes','Seconds'],function(i){
	
		if(options.showtext) {
			if(this=='Days')
				var texxt = options.text_d;
			if(this=='Hours')
				var texxt = options.text_h;
			if(this=='Minutes')
				var texxt = options.text_m;
			if(this=='Seconds')
				var texxt = options.text_s;
		}else{
			var texxt = "";
		}
		if(this=='Days'){
			var texxt = options.text_d;
		}
		else if(this=='Seconds') {
			var texxt = '';
		}
		else{
			var texxt = ':';
		}
		elem.append('<span class="count'+this+'"></span>');
		$('#'+elem.attr('id')+' .count'+this).html('<span class="position"><span class="digit static">0</span></span><span class="position"><span class="digit static">0</span></span><span class="countDiv countDiv'+i+'">'+texxt+'</span>');
	});

}

// ������� ������������� ������� ����� ����� �������
function switchDigit(position,number){
	
	var digit = position.find('.digit')
	
	if(digit.is(':animated')){
		return false;
	}
	
	if(position.data('digit') == number){
		// �� ��� ������ ������ �����
		return false;
	}
	
	position.data('digit', number);
	
	var replacement = $('<span>',{
		'class':'digit',
		css:{
			opacity:0
		},
		html:'<b class="bgc_item_'+number+'">'+number+'</b>'
	});
	
	// ����� .static �����������, ����� ����������� ��������.
	// ���������� ���� ����� ������.
	
	digit
		.before(replacement)
		.removeClass('static')
		.animate({opacity:0},'fast',function(){
			digit.remove();
		})

	replacement
		.delay(100)
		.animate({opacity:1},'fast',function(){
			replacement.addClass('static');
		});
}

})(jQuery);