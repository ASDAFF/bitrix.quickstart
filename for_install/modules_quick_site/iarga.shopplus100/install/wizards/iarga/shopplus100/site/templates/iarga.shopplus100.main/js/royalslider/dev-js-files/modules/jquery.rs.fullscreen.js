(function($) {
	/**
	 *
	 * RoyalSlider fullscreen module
	 * @version 1.0:
	 * 
	 */
	$.extend($.rsProto, {
		_initFullscreen: function() {
			var self = this;

			self._fullscreenDefaults = {
				enabled: false,
				keyboardNav: true,
				buttonFS: true,
				nativeFS: false,
				doubleTap: true
			};
			self.st.fullscreen = $.extend({}, self._fullscreenDefaults, self.st.fullscreen);

			if(self.st.fullscreen.enabled) {
				self.ev.one('rsBeforeSizeSet', function() {
					self._setupFullscreen();
				});
			}
			
		},
		_setupFullscreen: function() {
			var self = this;
			self._fsKeyboard = (!self.st.keyboardNavEnabled && self.st.fullscreen.keyboardNav);

			if(self.st.fullscreen.nativeFS) {
				// Thanks to John Dyer http://j.hn/
			    self._fullScreenApi = {
			            supportsFullScreen: false,
			            isFullScreen: function() { return false; },
			            requestFullScreen: function() {},
			            cancelFullScreen: function() {},
			            fullScreenEventName: '',
			            prefix: ''
			        };
			    var browserPrefixes = 'webkit moz o ms khtml'.split(' ');
			    // check for native support
			    if (typeof document.cancelFullScreen != 'undefined') {
			         self._fullScreenApi.supportsFullScreen = true;
			    } else {
			        // check for fullscreen support by vendor prefix
			        for (var i = 0; i < browserPrefixes.length; i++ ) {
			             self._fullScreenApi.prefix = browserPrefixes[i];
			 
			            if (typeof document[ self._fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
			                 self._fullScreenApi.supportsFullScreen = true;
			 
			                break;
			            }
			        }
			    }
			 
			    // update methods to do something useful
			    if ( self._fullScreenApi.supportsFullScreen) {
			         self._fullScreenApi.fullScreenEventName =  self._fullScreenApi.prefix + 'fullscreenchange.rs';
			 
			         self._fullScreenApi.isFullScreen = function() {
			            switch (this.prefix) {
			                case '':
			                    return document.fullScreen;
			                case 'webkit':
			                    return document.webkitIsFullScreen;
			                default:
			                    return document[this.prefix + 'FullScreen'];
			            }
			        }
			         self._fullScreenApi.requestFullScreen = function(el) {
			            return (this.prefix === '') ? el.requestFullScreen() : el[this.prefix + 'RequestFullScreen']();
			        }
			         self._fullScreenApi.cancelFullScreen = function(el) {
			            return (this.prefix === '') ? document.cancelFullScreen() : document[this.prefix + 'CancelFullScreen']();
			        }
			    } else {
			    	self._fullScreenApi = false;
			    }
			}


			if(self.st.fullscreen.buttonFS) {
				self._fsBtn = $('<div class="rsFullscreenBtn"><div class="rsFullscreenIcn"></div></div>')
					.appendTo(self.st.controlsInside ? self._sliderOverflow : self.slider)
					.on('click.rs', function() {
						if(self.isFullscreen) {
							self.exitFullscreen();
						} else {
							self.enterFullscreen();
						}
					});
			}
		},
		enterFullscreen: function(preventNative) {
			var self = this;
			if( self._fullScreenApi ) {
				if(!preventNative) {
					self._doc.on( self._fullScreenApi.fullScreenEventName, function(e) {
						if(!self._fullScreenApi.isFullScreen()) {
							self.exitFullscreen(true);
						} else {
							self.enterFullscreen(true);
						}
					});
					self._fullScreenApi.requestFullScreen($('html')[0]);
					return;
				} else {
					self._fullScreenApi.requestFullScreen($('html')[0]);
				}
			}

			if(self._isFullscreenUpdating) {
				return;
			}
			self._isFullscreenUpdating = true;

			self._doc.on('keyup.rsfullscreen', function(e) {
				if(e.keyCode === 27) {
					self.exitFullscreen();
				}
			});
			if(self._fsKeyboard) {
				self._bindKeyboardNav();
			}

			self._htmlStyle = $('html').attr('style');
			self._bodyStyle = $('body').attr('style');
			self._sliderStyle = self.slider.attr('style');
			

			$('body, html').css({
				overflow: 'hidden',
				height: '100%',
				width: '100%',
				margin: '0',
				padding: '0'
			});
			self.slider.addClass('rsFullscreen');
			//setTimeout(function(){
			//
			var item,
				i;
			for(i = 0; i < self.numSlides; i++) {
				item = self.slides[i];

				


				
				item.isRendered = false;
				if(item.bigImage) {

					item.isMedLoaded = item.isLoaded;
					item.isMedLoading = item.isLoading;
					item.medImage = item.image;
					item.medIW = item.iW;
					item.medIH = item.iH;
					item.slideId = -99;

					if(item.bigImage !== item.medImage) {
						item.sizeType = 'big';
					}

					item.isLoaded = item.isBigLoaded;
					item.isLoading = item.isBigLoading;
					
					item.image = item.bigImage;
					item.iW = item.bigIW;
					item.iH = item.bigIH;

					item.contentAdded = false;
					
					var newHTML = !item.isLoaded ? '<a class="rsImg" href="'+item.image+'"></a>' : '<img class="rsImg" src="'+item.image+'"/>';
					if(item.content.hasClass('rsImg')) {
						item.content = $(newHTML);
					} else {
						item.content.find('.rsImg').replaceWith(newHTML);
					}
				}
				
			}

			
			self.isFullscreen = true;
			
			setTimeout(function() {
				self._isFullscreenUpdating = false;
				self.updateSliderSize();
			}, 100);
			
		},
		exitFullscreen: function(preventNative) {
			var self = this;

			if( self._fullScreenApi ) {
				if(!preventNative) {
					self._fullScreenApi.cancelFullScreen($('html')[0]);
					return;
				}
				self._doc.off( self._fullScreenApi.fullScreenEventName );
			}
			if(self._isFullscreenUpdating) {
				return;
			}
			self._isFullscreenUpdating = true;

			self._doc.off('keyup.rsfullscreen');
			if(self._fsKeyboard) {
				self._doc.off('keydown.rskb');
			}

			$('html').attr('style', self._htmlStyle || '');
			$('body').attr('style', self._bodyStyle || '');
			self.slider.removeClass('rsFullscreen');

			
			
			var item,
				i;
			for(i = 0; i < self.numSlides; i++) {
				item = self.slides[i];
				
				
				item.isRendered = false;
				if(item.bigImage) {
					
					item.slideId = -99;
					item.isBigLoaded = item.isLoaded;
					item.isBigLoading = item.isLoading;
					item.bigImage = item.image;
					item.bigIW = item.iW;
					item.bigIH = item.iH;
					item.isLoaded = item.isMedLoaded;
					item.isLoading = item.isMedLoading;
					item.image = item.medImage;
					item.iW = item.medIW;
					item.iH = item.medIH;

					item.contentAdded = false;

					var newHTML = !item.isLoaded ? '<a class="rsImg" href="'+item.image+'"></a>' : '<img class="rsImg" src="'+item.image+'"/>';
					if(item.content.hasClass('rsImg')) {
						item.content = $(newHTML);
					} else {
						item.content.find('.rsImg').replaceWith(newHTML);
					}
					if(item.holder) {
						item.holder.html(item.content);
					}
					
					if(item.bigImage !== item.medImage) {
						item.sizeType = 'med';
					}
				}
					
					
					
				
			}
			
			self.isFullscreen = false;
			//self._updateBlocksContent();


			
			setTimeout(function() {
				self._isFullscreenUpdating = false;
				self.updateSliderSize();
			}, 100);
		}
	});
	$.rsModules.fullscreen = $.rsProto._initFullscreen;
})(jQuery);
