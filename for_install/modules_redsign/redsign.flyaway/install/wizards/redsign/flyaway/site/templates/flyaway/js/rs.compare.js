!function(global) {
  'use strict';

  function Compare(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.__construct();
  };

	Compare.prototype.__construct = function __construct() {
		this.$box = this.$element.find('.js-compare-box');
		this.$switcher = this.$element.find('.js-compare-switcher');
		this.$count = this.$element.find('.js-compare-count');
		this.url =  this.$element.find('.js-compare-label').attr('href'),
		this.id =  parseInt(this.$element.data('elementid'));
		this.name =  this.$element.find('.js-compare-name'),
		
		this.classActive = this.params.classActive || 'active';
		this.classHidden = this.params.classHidden || 'compare_hidden';

		this._init();
	};

	Compare.prototype._init = function _init() {
		var _this = this;

		this.$switcher.on('click.js-compare', function(e){
			_this.getData.apply(_this, []);
			return false;
		});

		this.ready();
		
		this.setCompared();
	};

	Compare.prototype.ready = function ready() {
		this.$element
			.addClass('js-compare-ready')
			.addClass(this.classReady);

		this.$element.trigger('jscompareready', this);
	};

	Compare.prototype.setCount = function setCount(count) {
            if (rsFlyaway.count_compare >= 0) {
                jQuery('.js-comparelist').removeClass(this.classHidden);
                $('.js-comparelist-count').html(count);
            }

            if (rsFlyaway.count_compare == 0) {
                jQuery('.informer').addClass('informer_unactive');
                jQuery('.js-comparising-list').addClass('hidden');
            } else {
                jQuery('.informer').removeClass('informer_unactive');			
                jQuery('.js-comparising-list').removeClass('hidden');
            }
	};
	
	Compare.prototype.setCompared = function setCompared(id) {
		var element_id;

		jQuery('.js-elementid' + id).find('.js-compare-box').removeClass(this.classActive);
		
		for (element_id in rsFlyaway.compare) {
			if (rsFlyaway.compare[element_id] == 'Y' && $('.js-elementid' + element_id).find('.js-compare-box').length > 0) {
				jQuery('.js-elementid' + element_id).find('.js-compare-box').addClass(this.classActive).find('.js-compare-count').html(' ('+ rsFlyaway.count_compare +')');
			}
		}
		
		this.$box.not('.' + this.classActive).find('.js-compare-count').html('');
	};

	Compare.prototype.getData = function getData() {
		var _this = this,
				action = '';
		
		if (this.id > 0) {
			if (this.url.indexOf('?') == -1) {
				this.url = this.url + '?';
			}
			
			if (rsFlyaway.compare[this.id] == 'Y' || parseInt(rsFlyaway.compare[this.id]) > 0) {
				action = 'DELETE_FROM_COMPARE_LIST';
			} else {
				action = 'ADD_TO_COMPARE_LIST';
			}

			this.url = this.url + 'AJAX_CALL=Y&action=' + action + '&id=' + this.id;

			rsFlyaway.darken(this.$box);
			rsFlyaway.darken(jQuery('.js-informer-status'));
			
			jQuery.getJSON(_this.url, {}, function(json) {
				if (json.TYPE == "OK") {
					var str = _this.name.text();
					
					jQuery('.js-compare-product').html(str);
					jQuery('.js-compare-add').addClass('hidden');
					jQuery('.js-compare-del').addClass('hidden');
											
					if (action == 'DELETE_FROM_COMPARE_LIST') { // delete from compare
						delete rsFlyaway.compare[_this.id];
						jQuery('.js-compare-del').removeClass('hidden');
					} else {
						// add to compare
						rsFlyaway.compare[_this.id] = 'Y';
						jQuery('.js-compare-add').removeClass('hidden');
					}
					
					rsFlyaway.count_compare = json.COUNT;
					
					_this.setCount(json.COUNT_WITH_WORD);
				} else {
					console.warn('compare - error responsed');
				}
			}).fail(function(data) {
					console.warn( 'compare - fail request' );
			}).always(function() {
					rsFlyaway.darken(_this.$box);
					rsFlyaway.darken(jQuery('.js-informer-status'));
					_this.setCompared(_this.id);
					
					ajaxBasket('fav', $('.js-favorite_in'));
			});
		}
		return false;
	};
  /*--/Compare--*/

  global.Compare = Compare;
}(this);
