(function($) {
	/**
	 *
	 * RoyalSlider auto play module
	 * @version 1.0.2:
	 * 
	 */
	$.extend($.rsProto, {
		_initAutoplay: function() {
			var self = this,
				del;

			self._autoPlayDefaults = {
				enabled: false,
		     	stopAtAction: true,
		     	pauseOnHover: true,
		     	delay: 2000
			};
			self.st.autoPlay = $.extend({}, self._autoPlayDefaults, self.st.autoPlay);

			if(self.st.autoPlay.enabled) {
				self.ev.on('rsBeforeParseNode', function(e, content, obj) {
					content = $(content);
					del = content.attr('data-rsDelay');
					if(del) {
						obj.customDelay = parseInt(del, 10);
					}
				});
				self.ev.one('rsAfterInit', function() {
					self._setupAutoPlay();
				});
				self.ev.on('rsBeforeDestroy', function() {
					self.stopAutoPlay();
				});
			}
		},
		_setupAutoPlay: function() {
			var self = this;
				
			self.startAutoPlay();

			self.ev.on('rsAfterContentSet', function(e, slideObject) {
				if(!self._isDragging && !self._isAnimating && self._autoPlayEnabled && slideObject === self.currSlide ) {
					self._play();
				}
			});
			self.ev.on('rsDragRelease', function() {
				if(self._autoPlayEnabled && self._autoPlayPaused) {
					self._autoPlayPaused = false;
					self._play();
				}
			});
			self.ev.on('rsAfterSlideChange', function() {
				if(self._autoPlayEnabled) {
					if(self._autoPlayPaused) {
						self._autoPlayPaused = false; 

						if(self.currSlide.isLoaded) {
							self._play();
						}
					}
				}
			});
			self.ev.on('rsDragStart', function() {
				if(self._autoPlayEnabled) {
					if(self.st.autoPlay.stopAtAction) {
						self.stopAutoPlay();
					} else {
						self._autoPlayPaused = true;
						self._pause();
					}
				}
			});
			self.ev.on('rsBeforeMove', function(e, type, userAction) {
				if(self._autoPlayEnabled) {
					if(userAction && self.st.autoPlay.stopAtAction) {
						self.stopAutoPlay();
					} else {
						self._autoPlayPaused = true;
						self._pause();
					}
				}
			});

			self._pausedByVideo = false;
			self.ev.on('rsVideoStop', function() {
				if(self._autoPlayEnabled) {
					self._pausedByVideo = false;
					self._play();
				}
			});
			self.ev.on('rsVideoPlay', function() {
				if(self._autoPlayEnabled) {
					self._autoPlayPaused = false;
					self._pause();
					self._pausedByVideo = true;
				} 
			});

			if(self.st.autoPlay.pauseOnHover) {
				self._pausedByHover = false;
				self.slider.hover(
					function() {
						if(self._autoPlayEnabled) {
							self._autoPlayPaused = false;
							self._pause();
							self._pausedByHover = true;
						} 
					},
					function() {
						if(self._autoPlayEnabled) {
							self._pausedByHover = false;
							self._play();
						}
					}
				);	
			}
			
		},
		toggleAutoplay: function() {
			var self = this;
			if(self._autoPlayEnabled) {
				self.startAutoPlay();
			} else {
				self.stopAutoPlay();
			}
		},
		startAutoPlay: function() {
			var self = this;
			self._autoPlayEnabled = true;
			if(self.currSlide.isLoaded) {
				self._play();
			}
		},
		stopAutoPlay: function() {
			var self = this;
			self._pausedByVideo = self._pausedByHover = self._autoPlayPaused = self._autoPlayEnabled = false;
			self._pause();
		},
		_play: function() {
			var self = this;
			if(!self._pausedByHover && !self._pausedByVideo) {
				self._autoPlayRunning = true;
				if(self._autoPlayTimeout) {
					clearTimeout(self._autoPlayTimeout);
				}
				self._autoPlayTimeout = setTimeout(function() {
					var changed;
					if(!self._loop && !self.st.loopRewind) {
						changed = true;
						self.st.loopRewind = true;
					}
					self.next(true);
					if(changed) {
						changed = false;
						self.st.loopRewind = false;
					}
				}, !self.currSlide.customDelay ? self.st.autoPlay.delay : self.currSlide.customDelay);
			}
			
		},
		_pause: function() {
			var self = this;
			if(!self._pausedByHover && !self._pausedByVideo) {
				self._autoPlayRunning = false;
				if(self._autoPlayTimeout) {
					clearTimeout(self._autoPlayTimeout);
					self._autoPlayTimeout = null;
				}
			}
		}
	});
	$.rsModules.autoplay = $.rsProto._initAutoplay;
})(jQuery);
