function Beono_Banner_Rotation (settings) {

	this.id = settings.id;
	this.transition_speed = settings.transition_speed;
	this.transition_interval = settings.transition_interval;
	this.effect = settings.effect;
	this.stop_on_focus = settings.stop_on_focus;
	this._interval_pointer = null;

	var __instance = this;
	
	var __context = $('#' + this.id);
	this.context = __context;
	
	this.getActiveBanner = function () {
		return $('div.beono-banner_slider-item', __context).index($('div.beono-banner_slider-item:visible', __context).get(0));
	};
	
	this.getNextBanner = function () {
		var active_banner_index = this.getActiveBanner();
		var next_banner_index = active_banner_index + 1;
		
		if (!$('div.beono-banner_slider-item:eq('+next_banner_index+')', __context).length) {
			next_banner_index = 0;
		}
		return next_banner_index;
	};

	this.hideBanner = function (banner_index) {

		if (this.effect == 'slide_h') {
			if (this.next_banner_index < banner_index) {
				var next_banner_position = '100%';
				var next_banner_position_new = '-=100%';
			} else {
				var next_banner_position = '-100%';
				var next_banner_position_new = '+=100%';
			}
			$('div.beono-banner_slider-item:eq('+banner_index+')', __context)
			.animate(
				{left: next_banner_position}, 
				this.transition_speed,
				function () {
					$('div.beono-banner_slider-item:eq('+banner_index+')', __context).hide();
				}
			);	
		} else if (this.effect == 'slide_v') {
			$('div.beono-banner_slider-item:eq('+banner_index+')', __context)
				.animate(
					{top: '+=100%'}, 
					this.transition_speed,
					function () {
						$('div.beono-banner_slider-item:eq('+banner_index+')', __context).hide();
					}
				);			
		} else {
			$('div.beono-banner_slider-item:eq('+banner_index+')', __context).fadeOut(this.transition_speed);
		}
		return true;
	};

	this.showBanner = function (next_banner_index) {
		
		this.next_banner_index = next_banner_index;
		
		if (typeof(this.onBeforeShowBanner) == 'function') {
			this.onBeforeShowBanner(next_banner_index);
		}
		
		var active_banner_index = this.getActiveBanner();
		
		if (active_banner_index != next_banner_index) {
			
			if (this.effect == 'slide_h') {
				if (next_banner_index > active_banner_index) {
					var next_banner_position = '100%';
					var next_banner_position_new = '-=100%';
				} else {
					var next_banner_position = '-100%';
					var next_banner_position_new = '+=100%';
				}
				this.hideBanner(active_banner_index);
				$('div.beono-banner_slider-item:eq('+next_banner_index+')', __context)
					.css('left', next_banner_position)
					.show()
					.animate(
						{left: next_banner_position_new}, 
						this.transition_speed
					);
			} else if (this.effect == 'slide_v') {
				this.hideBanner(active_banner_index);
				$('div.beono-banner_slider-item:eq('+next_banner_index+')', __context)
					.css('top', '-100%')
					.show()
					.animate(
						{top: '+=100%'}, 
						this.transition_speed
					);		
			} else {
				this.hideBanner(active_banner_index);
				$('div.beono-banner_slider-item:eq('+next_banner_index+')', __context).fadeIn(this.transition_speed);	
			}
		}
		
		if (this.hasPager) {
			$('div.beono-banner_slider-pager a', __context).removeClass('active prev next');
			$('div.beono-banner_slider-pager a:eq('+next_banner_index+')', __context).addClass('active');
			
			if ($('div.beono-banner_slider-pager a:eq('+(next_banner_index-1)+')', __context).length) {
				$('div.beono-banner_slider-pager a:eq('+(next_banner_index-1)+')', __context).addClass('prev');
			}
			
			if ($('div.beono-banner_slider-pager a:eq('+(next_banner_index+1)+')', __context).length) {
				$('div.beono-banner_slider-pager a:eq('+(next_banner_index+1)+')', __context).addClass('next');
			}
		}
		
		if (typeof(this.onAfterShowBanner) == 'function') {
			this.onAfterShowBanner(next_banner_index);
		}
		
	};

	this.startRotation = function  ()	{		
		this._interval_pointer = setInterval (function () {
			var next_banner_index = __instance.getNextBanner();
			__instance.showBanner(next_banner_index);
			
		}, this.transition_interval);
	};

	this.stopRotation = function  ()	{ 
		clearInterval(this._interval_pointer);
	};

	this.init = function () {
		
		this.hasPager = new Boolean($('div.beono-banner_slider-pager').length);
		$('div.beono-banner_slider-wrapper div.beono-banner_slider-item', __context).hide();
		$('div.beono-banner_slider-wrapper div.beono-banner_slider-item:first', __context).show();
		 
		this.showBanner(0);
		this.startRotation();	
		
		if(this.stop_on_focus) {
			$('div.beono-banner_slider-item', __context).mouseenter(function () {
				__instance.stopRotation();
			}).mouseleave(function () {
				__instance.startRotation();
			});
		}
		
		if (this.hasPager) {
			$('div.beono-banner_slider-pager a', __context).click(function () {
				if($(this).hasClass('active') || $('div.beono-banner_slider-item:animated', __context).length) {
					return false;
				} else {
					if ($(this).is('[href=#stop]')) {
						$(this).attr('href' , '#start').text('Start');
						__instance.stopRotation();
					} else if ($(this).is('[href=#start]')) {
						$(this).attr('href' , '#stop').text('Stop');
						__instance.startRotation();
					} else {
						var next_banner_index = $('div.beono-banner_slider-pager a', __context).index(this);
						__instance.stopRotation();
						__instance.showBanner(next_banner_index);
						setTimeout(function () {
							__instance.startRotation();
						}, __instance.transition_speed);
						return false;
					}
				}
			});
		}
	};
}