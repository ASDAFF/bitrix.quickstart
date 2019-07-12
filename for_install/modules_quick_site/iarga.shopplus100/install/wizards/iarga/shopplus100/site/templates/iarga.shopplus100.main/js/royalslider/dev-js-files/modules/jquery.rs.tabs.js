(function($) {
	/**
	 *
	 * RoyalSlider tabs module
	 * @version 1.0.1:
	 *
	 * 1.0.1:
	 * - Dynamic adding/removing tabs.
	 */ 
	$.extend($.rsProto, {
		_initTabs: function() {
			var self = this;
			if(self.st.controlNavigation === 'tabs') {
				self.ev.on('rsBeforeParseNode', function(e, content, obj) {
					content = $(content);
					obj.thumbnail = content.find('.rsTmb').remove();
					if(!obj.thumbnail.length) {

						obj.thumbnail = content.attr('data-rsTmb');
						if(!obj.thumbnail) {
							obj.thumbnail = content.find('.rsImg').attr('data-rsTmb');
						}
						if(!obj.thumbnail) {
							obj.thumbnail = '';
						} else {
							obj.thumbnail = '<img src="'+obj.thumbnail+'"/>';
						}
					} else {
						obj.thumbnail = $(document.createElement('div')).append(obj.thumbnail).html();
					}
				});

				self.ev.one('rsAfterPropsSetup', function() {
					self._createTabs();
				});

				self.ev.on('rsOnAppendSlide', function(e, parsedSlide, index) {
					if(index >= self.numSlides) {
						self._controlNav.append('<div class="rsNavItem rsTab">' + parsedSlide.thumbnail + '</div>');
					} else {
						self._controlNavItems.eq(index).before('<div class="rsNavItem rsTab">' + item.thumbnail + '</div>');
					}
					self._controlNavItems = self._controlNav.children();
				});
				self.ev.on('rsOnRemoveSlide', function(e, index) {
					var itemToRemove = self._controlNavItems.eq(index);
					if(itemToRemove) {
						itemToRemove.remove();
						self._controlNavItems = self._controlNav.children();
					}
				});	

				self.ev.on('rsOnUpdateNav', function() {
					var id = self.currSlideId,
						currItem,
						prevItem;
					if(self._prevNavItem) {
						self._prevNavItem.removeClass('rsNavSelected');
					}
					currItem = $(self._controlNavItems[id]);

					currItem.addClass('rsNavSelected');
					self._prevNavItem = currItem;
				});
			}
			
		},
		_createTabs: function() {
			var self = this, 
				out = '',
				item;

			self._controlNavEnabled = true;
			out += '<div class="rsNav rsTabs">';
			for(var i = 0; i < self.numSlides; i++) {
				if(i === self.numSlides - 1) {
					style = '';
				}

				item = self.slides[i];
				out += '<div class="rsNavItem rsTab">'+item.thumbnail+'</div>';
			}

			out += '</div>';
			out = $(out);

			self._controlNav = out;
			self._controlNavItems = out.find('.rsNavItem');
			
			self.slider.append(out);

			self._controlNav.click(function(e) {
				var item = $(e.target).closest('.rsNavItem');
				if(item.length) {
					self.goTo(item.index());
				}
			});
		}
	});
	$.rsModules.tabs = $.rsProto._initTabs;
})(jQuery);
