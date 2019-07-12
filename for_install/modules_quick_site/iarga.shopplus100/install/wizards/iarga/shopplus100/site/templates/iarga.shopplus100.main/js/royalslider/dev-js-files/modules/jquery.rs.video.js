(function($) {
	/**
	 *
	 * RoyalSlider video module
	 * @version 1.0.3:
	 *
	 * 1.0.3:
	 * - Added rsOnDestroyVideoElement event
	 */
	$.extend($.rsProto, {
		_initVideo: function() {
			var self = this;
			self._videoDefaults = {
				autoHideArrows: true,
				autoHideControlNav: false,
				autoHideBlocks: false,
				youTubeCode: '<iframe src="http://www.youtube.com/embed/%id%?rel=1&autoplay=1&showinfo=0&autoplay=1" frameborder="no"></iframe>',
				vimeoCode: '<iframe src="http://player.vimeo.com/video/%id%?byline=0&amp;portrait=0&amp;autoplay=1" frameborder="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'
			};

			self.st.video = $.extend({}, self._videoDefaults, self.st.video);

			self.ev.on('rsBeforeSizeSet', function() {
				if(self._isVideoPlaying) {
					setTimeout(function() {
						var content = self._currHolder;
						content = content.hasClass('rsVideoContainer') ? content : content.find('.rsVideoContainer');
						self._videoFrameHolder.css({
							width: content.width(),
							height: content.height()
						});
					}, 32);
				}
			});
			var isFF = $.browser.mozilla;
			self.ev.on('rsAfterParseNode', function(e, content, obj) {
				var jqcontent = $(content),
					tempEl,
					hasVideo;

				if(obj.videoURL) {
					if(!hasVideo && isFF) {
						hasVideo = true;
						self._useCSS3Transitions = self._use3dTransform = false;
					}
					var wrap = $('<div class="rsVideoContainer"></div>'),
						playBtn = $('<div class="rsBtnCenterer"><div class="rsPlayBtn"><div class="rsPlayBtnIcon"></div></div></div>');
					if(jqcontent.hasClass('rsImg')) {
						obj.content = wrap.append(jqcontent).append(playBtn);
					} else {
						obj.content.find('.rsImg').wrap(wrap).after(playBtn);
					}
				}
			});

		},
		toggleVideo: function() {
			var self = this;
			if(!self._isVideoPlaying) {
				return self.playVideo();
			} else {
				return self.stopVideo();
			}
		},
		playVideo: function() {
			var self = this;
			if(!self._isVideoPlaying) {
				var currSlide = self.currSlide;
				if(!currSlide.videoURL) {
					return false;
				}

				
				var content = self._currVideoContent = currSlide.content;
				var url = currSlide.videoURL,
					videoId,
					regExp,
					match;

				if( url.match(/youtu\.be/i) || url.match(/youtube\.com\/watch/i) ) {
					regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
				    match = url.match(regExp);
				    if (match && match[7].length==11){
				        videoId = match[7];
				    }

					if(videoId !== undefined) {
						self._videoFrameHolder = self.st.video.youTubeCode.replace("%id%", videoId);
					}
				} else if(url.match(/vimeo\.com/i)) {
					regExp = /\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
					match = url.match(regExp);
					if(match) {
						videoId = match[2];
					}
					if(videoId !== undefined) {
						self._videoFrameHolder = self.st.video.vimeoCode.replace("%id%", videoId);
					}
				}
				self.videoObj = $(self._videoFrameHolder);

				self.ev.trigger('rsOnCreateVideoElement', [url]);


				if(self.videoObj.length) {
					self._videoFrameHolder = $('<div class="rsVideoFrameHolder"><div class="rsPreloader"></div><div class="rsCloseVideoBtn"><div class="rsCloseVideoIcn"></div></div></div>');
					self._videoFrameHolder.find('.rsPreloader').after(self.videoObj);
					var content = content.hasClass('rsVideoContainer') ? content : content.find('.rsVideoContainer');
					self._videoFrameHolder.css({
						width: content.width(),
						height: content.height()
					}).find('.rsCloseVideoBtn').off('click.rsv').on('click.rsv', function(e) {
						self.stopVideo();
						e.preventDefault();
						e.stopPropagation();
						return false;
					});
					content.append(self._videoFrameHolder);
					if(self.isIPAD) {
						content.addClass('rsIOSVideo');
					}

					

					if(self._arrowLeft && self.st.video.autoHideArrows) {
						self._arrowLeft.addClass('rsHidden');
						self._arrowRight.addClass('rsHidden');
						self._arrowsAutoHideLocked = true;
					}
					if(self._controlNav && self.st.video.autoHideControlNav) {
						self._controlNav.addClass('rsHidden');
					}
					if(self.st.video.autoHideBlocks && self.currSlide.animBlocks) {
						self.currSlide.animBlocks.addClass('rsHidden');
					}

					setTimeout(function() {
						self._videoFrameHolder.addClass('rsVideoActive');
					}, 10);

					self.ev.trigger('rsVideoPlay');
					self._isVideoPlaying = true;
				}
				return true;
			}
			return false;
		},
		stopVideo: function() {
			var self = this;
			if(self._isVideoPlaying) {
				//self._videoContainer.css('display', 'none');
				if(self.isIPAD) {
					self.slider.find('.rsCloseVideoBtn').remove();
				}
				if(self._arrowLeft && self.st.video.autoHideArrows) {
					self._arrowLeft.removeClass('rsHidden');
					self._arrowRight.removeClass('rsHidden');
					self._arrowsAutoHideLocked = false;
				}
				if(self._controlNav && self.st.video.autoHideControlNav) {
					self._controlNav.removeClass('rsHidden');
				}
				if(self.st.video.autoHideBlocks && self.currSlide.animBlocks) {
					self.currSlide.animBlocks.removeClass('rsHidden');
				}

				setTimeout(function() {
					self.ev.trigger('rsOnDestroyVideoElement', [self.videoObj]);
					self._videoFrameHolder.remove();
				}, 16);
				self.ev.trigger('rsVideoStop');
				//self._currVideoContent.fadeIn();
				self._isVideoPlaying = false;
				return true;
			} 
			return false;
		}
	});
	$.rsModules.video = $.rsProto._initVideo;
})(jQuery);
