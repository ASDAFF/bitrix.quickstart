var RSGoPro_OffersExt_timeout_id = 0;

function RSGoPro_OffersExt_ChangeHTML($elementObj) {
	var element_id = $elementObj.data('elementid');
	if( RSGoPro_OFFERS[element_id] ) {
		// get all selected values
		var arrFullChosed = new Object();
		$elementObj.find('.div_option.selected').each(function(index1){
			var $optionObj = $(this);
			var code = $optionObj.parents('.offer_prop').data('code');
			var value = $optionObj.data('value');
			arrFullChosed[code] = value;
		});

		// get offerID (key=ID)
		var finedOfferID = 0;
		var all_prop_true2 = true;
		for(offerID in RSGoPro_OFFERS[element_id].OFFERS) {
			all_prop_true2 = true;
			for(pCode in arrFullChosed) {
				if( arrFullChosed[pCode] != RSGoPro_OFFERS[element_id].OFFERS[offerID].PROPERTIES[pCode] ) {
					all_prop_true2 = false;
					break;
				}
			}
			if(all_prop_true2) {
				finedOfferID = offerID;
				break;
			}
		}
		
		// article
		if( $elementObj.find('.offer_article') ) {
			if( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].ARTICLE ) {
				$elementObj.find('.offer_article').html( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].ARTICLE ).parent().css('visibility','visible');
			} else if( $elementObj.find('.offer_article').data('prodarticle') ) {
				$elementObj.find('.offer_article').html( $elementObj.find('.offer_article').data('prodarticle') ).parent().css('visibility','visible');
			} else {
				$elementObj.find('.offer_article').parent().css('visibility','hidden');
			}
		}
		
		// set price
		if( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].PRICES ) {
			var PRICES = RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].PRICES;
			for(var PRICE_CODE in PRICES) {
				if( $elementObj.find('.price_pdv_'+PRICE_CODE) ) {
					$elementObj.find('.price_pdv_'+PRICE_CODE).removeClass('new').html( PRICES[PRICE_CODE].PRINT_DISCOUNT_VALUE );
					if( parseInt(PRICES[PRICE_CODE].DISCOUNT_DIFF)>0 ) {
						$elementObj.find('.price_pdv_'+PRICE_CODE).addClass('new');
					}
				}
				if( $elementObj.find('.price_pd_'+PRICE_CODE) ) {
					if( parseInt(PRICES[PRICE_CODE].DISCOUNT_DIFF)>0 ) {
						$elementObj.find('.price_pd_'+PRICE_CODE).html( PRICES[PRICE_CODE].PRINT_DISCOUNT );
					} else {
						$elementObj.find('.price_pd_'+PRICE_CODE).html( '' );
					}
				}
				if( $elementObj.find('.price_pv_'+PRICE_CODE) ) {
					if( parseInt(PRICES[PRICE_CODE].DISCOUNT_DIFF)>0 ) {
						$elementObj.find('.price_pv_'+PRICE_CODE).html( PRICES[PRICE_CODE].PRINT_VALUE );
					} else {
						$elementObj.find('.price_pv_'+PRICE_CODE).html( '' );
					}
				}
			}
		}
		
		// set ratio
		if( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].CATALOG_MEASURE_RATIO && $elementObj.find('.js-quantity') ) {
			$elementObj.find('.js-quantity').data('ratio',RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].CATALOG_MEASURE_RATIO);
			$elementObj.find('.js-quantity').val( $elementObj.find('.js-quantity').data('ratio') );
		}
		if( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].CATALOG_MEASURE_NAME && $elementObj.find('.js-measurename') ) {
			$elementObj.find('.js-measurename').html( RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].CATALOG_MEASURE_NAME );
		}
		
		// daysarticle & quickbuy
		$elementObj.removeClass('qb da2');
		$elementObj.find('.timers .timer').hide();
		if( RSGoPro_OFFERS[element_id].ELEMENT.QUICKBUY || RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].QUICKBUY ) {
			$elementObj.addClass('qb');
			if( $elementObj.find('.timers .qb.js-timer_id'+element_id).length>0 ) {
				$elementObj.find('.timers .qb.js-timer_id'+element_id).css('display','inline-block');
			} else if ( $elementObj.find('.timers .qb.js-timer_id'+finedOfferID).length>0 ) {
				$elementObj.find('.timers .qb.js-timer_id'+finedOfferID).css('display','inline-block');
			}
		}
		if( RSGoPro_OFFERS[element_id].ELEMENT.DAYSARTICLE2 || RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].DAYSARTICLE2 ) {
			$elementObj.removeClass('qb').addClass('da2');
			if( $elementObj.find('.timers .da2.js-timer_id'+element_id).length>0 ) {
				$elementObj.find('.timers .timer').hide();
				$elementObj.find('.timers .da2.js-timer_id'+element_id).css('display','inline-block');
			} else if ( $elementObj.find('.timers .da2.js-timer_id'+finedOfferID).length>0 ) {
				$elementObj.find('.timers .timer').hide();
				$elementObj.find('.timers .da2.js-timer_id'+finedOfferID).css('display','inline-block');
			}
		}
		
		
		// change product id
		$elementObj.find('.js-add2basketpid').val( finedOfferID );
		if(RSGoPro_OFFERS[element_id].OFFERS[finedOfferID].CAN_BUY) {
			$elementObj.find('.add2basketform').removeClass('cantbuy');
		} else {
			$elementObj.find('.add2basketform').addClass('cantbuy');
		}
		
		// stores
		if( $elementObj.find('.stores') && finedOfferID>0 && $elementObj.find('.stores').find('.popupstores') ) {
			if( RSGoPro_STOCK[element_id] ) {
				if( $elementObj.find('.stores').hasClass('gopro_20') ) {
					// change stores
					for(storeID in RSGoPro_STOCK[element_id].JS.SKU[finedOfferID]) {
						var stores = RSGoPro_STOCK[element_id].JS.SKU[finedOfferID];
						var quantity = stores[storeID];
						if( RSGoPro_STOCK[element_id].USE_MIN_AMOUNT==true ) {
							if( quantity < 1 ) {
								$elementObj.find('.stores').find('.store_'+storeID).find('.amount').css('color','#ff0000').html( RSGoPro_STOCK[element_id].MESSAGE_EMPTY );
							} else if( quantity < RSGoPro_STOCK[element_id].MIN_AMOUNT ) {
								$elementObj.find('.stores').find('.store_'+storeID).find('.amount').css('color','').html( RSGoPro_STOCK[element_id].MESSAGE_LOW );
							} else {
								$elementObj.find('.stores').find('.store_'+storeID).find('.amount').css('color','#00cc00').html( RSGoPro_STOCK[element_id].MESSAGE_ISSET );
							}
						} else {
							$elementObj.find('.stores').find('.store_'+storeID).find('.amount').html( quantity );
						}
						if( RSGoPro_STOCK[element_id].SHOW_EMPTY_STORE==false && quantity<1 ) {
							$elementObj.find('.stores').find('.store_'+storeID).hide();
						} else {
							$elementObj.find('.stores').find('.store_'+storeID).show();
						}
					}
				} else {
					console.warn( 'OffersExt_ChangeHTML -> old stores template version' );
					// change stores
					$elementObj.find('.stores').find('.genamount').removeClass('cantopen');
					$elementObj.find('.stores').find('.popupstores').find('.offerstore').hide();
					if( $elementObj.find('.stores').find('.popupstores').find('.offer_'+finedOfferID).length>0 ) {
						$elementObj.find('.stores').find('.popupstores').find('.offer_'+finedOfferID).show();
					} else {
						$elementObj.find('.stores').find('.genamount').addClass('cantopen');
					}
				}
				// change general
				if( RSGoPro_STOCK[element_id].QUANTITY[element_id] ) {
					var quantity = parseInt( RSGoPro_STOCK[element_id].QUANTITY[finedOfferID] );
					if( RSGoPro_STOCK[element_id].USE_MIN_AMOUNT==true ) {
						if( quantity < 1 ) {
							$elementObj.find('.stores').find('.genamount span').css('color','#ff0000').html( RSGoPro_STOCK[element_id].MESSAGE_EMPTY );
						} else if( quantity < RSGoPro_STOCK[element_id].MIN_AMOUNT ) {
							$elementObj.find('.stores').find('.genamount span').css('color','').html( RSGoPro_STOCK[element_id].MESSAGE_LOW );
						} else {
							$elementObj.find('.stores').find('.genamount span').css('color','#00cc00').html( RSGoPro_STOCK[element_id].MESSAGE_ISSET );
						}
					} else {
						$elementObj.find('.stores').find('.genamount span').html( quantity );
					}
				}
			} else {
				console.warn( 'OffersExt_ChangeHTML -> store not found' );
			}
		}
		
		// set buttons "in basket" and "not in basket"
		RSGoPro_SetInBasket();

		// event
		$(document).trigger('RSGoProOnOfferChange',[$elementObj]);
		
	}
}

function RSGoPro_OffersExt_PropChanged($optionObj)
{
	var element_id = $optionObj.parents('.js-element').data('elementid');
	var CURRENT_PROP_CODE = $optionObj.parents('.offer_prop').data('code');
	var value = $optionObj.data('value');
	if( RSGoPro_OFFERS[element_id] && !$optionObj.hasClass('disabled') ) {
		// change styles
		$optionObj.parents('.offer_prop').removeClass('opened').addClass('closed');
		$optionObj.parents('.offer_prop').find('.div_option').removeClass('selected');
		$optionObj.addClass('selected');
		
		// set current value
		if( $optionObj.parents('.offer_prop').hasClass('color') ) { // color 
			$optionObj.parents('.offer_prop').find('.div_selected span').css({'backgroundImage':$optionObj.find('span').css('backgroundImage')});
		} else {
			$optionObj.parents('.offer_prop').find('.div_selected span').html(value);
		}
		
		var next_index = 0;
		var NEXT_PROP_CODE = '';
		var PROP_CODE = '';
		
		// get current values
		var arCurrentValues = new Object();
		for(index in RSGoPro_OFFERS[element_id].SORT_PROPS) {
			PROP_CODE = RSGoPro_OFFERS[element_id].SORT_PROPS[index];
			arCurrentValues[PROP_CODE] = $optionObj.parents('.js-element').find('.prop_'+PROP_CODE).find('.div_option.selected').data('value');
			// save next prop_code
			if(PROP_CODE==CURRENT_PROP_CODE) {
				var next_index = parseInt(index)+1;
				if( RSGoPro_OFFERS[element_id].SORT_PROPS[next_index] )
					NEXT_PROP_CODE = RSGoPro_OFFERS[element_id].SORT_PROPS[next_index];
				else
					NEXT_PROP_CODE = false;
				break;
			}
		}

		// get enabled values for next property
		if(NEXT_PROP_CODE) {
			var allPropTrue1 = true;
			var arNextEnabledProps = new Array();
			for(offerID in RSGoPro_OFFERS[element_id].OFFERS) {
				allPropTrue1 = true;
				for(pCode1 in arCurrentValues) {
					if( arCurrentValues[pCode1] != RSGoPro_OFFERS[element_id].OFFERS[offerID].PROPERTIES[pCode1] ) {
						allPropTrue1 = false;
					}
				}
				if(allPropTrue1) {
					arNextEnabledProps.push( RSGoPro_OFFERS[element_id].OFFERS[offerID].PROPERTIES[NEXT_PROP_CODE] );
				}
			}

			// disable and enable values for NEXT_PROP_CODE
			$optionObj.parents('.js-element').find('.prop_'+NEXT_PROP_CODE).find('.div_option').each(function(i){
				var $option = $(this);
				var emptyInEnabled = true;
				for(inden in arNextEnabledProps) {
					if( $option.data('value') == arNextEnabledProps[inden] ) {
						emptyInEnabled = false;
						break;
					}
				}
				$option.addClass('disabled');
				if(!emptyInEnabled)
					$option.removeClass('disabled');
			});

			// call itself
			var nextOptionObj;
			if(!$optionObj.parents('.js-element').find('.prop_'+NEXT_PROP_CODE).find('.div_option.selected').hasClass('disabled')) {
				nextOptionObj = $optionObj.parents('.js-element').find('.prop_'+NEXT_PROP_CODE).find('.div_option.selected');
			} else {
				nextOptionObj = $optionObj.parents('.js-element').find('.prop_'+NEXT_PROP_CODE).find('.div_option:not(.disabled):first');
			}
			RSGoPro_OffersExt_PropChanged(nextOptionObj);
		} else {
			RSGoPro_OffersExt_ChangeHTML( $optionObj.parents('.js-element') );
		}
	}
}

$(document).ready(function(){
	
	// prop select -> click
	$(document).on('click','.div_option',function(e){
		e.stopPropagation();
		clearTimeout( RSGoPro_OffersExt_timeout_id );
		var $optionObj = $(this);
		if(!$optionObj.hasClass('disabled')) {
			var element_id = $optionObj.parents('.js-element').data('elementid');
			if( element_id > 0 ) {
				var propCode = $optionObj.parents('.offer_prop').data('code');
				var value = $optionObj.data('value');
				if( RSGoPro_OFFERS[element_id] ) {
					RSGoPro_OffersExt_PropChanged($optionObj);
				} else {
					RSGoPro_Area2Darken( $optionObj.parents('.js-element'), 'animashka' );
					var url = SITE_DIR + 'catalog/?AJAX_CALL=Y&action=get_element_json&element_id=' + element_id;
					$.getJSON(url, {}, function(json){
						RSGoPro_OFFERS[element_id] = json;
						RSGoPro_OffersExt_PropChanged($optionObj);
						RSGoPro_Area2Darken( $optionObj.parents('.js-element') );
					}).fail(function(data){
						console.warn( 'Get element JSON -> fail request' );
						RSGoPro_Area2Darken( $optionObj.parents('.js-element') );
					});
				}
			} else {
				console.warn( 'Get element JSON -> element_id is empty' );
			}
		}
		return false;
	});
	$(document).on('click','.div_selected',function(e){
		$('.offer_prop.opened:not(.prop_'+ $(this).parents('.offer_prop').data('code')+')').removeClass('opened').addClass('closed');
		if( $(this).parents('.offer_prop').hasClass('closed') ) { // was closed 
			$(this).parents('.offer_prop').removeClass('closed').addClass('opened');
		} else { // was opened
			$(this).parents('.offer_prop').removeClass('opened').addClass('closed');
		}
		return false;
	});
	// close prop select by click outside
	$(document).on('click',function(e){
		if( $(e.target).parents('.offer_prop').length>0 && !$(e.target).parents('.offer_prop').hasClass('color') ) {

		} else {
			$('.offer_prop').removeClass('opened').addClass('closed');
		}
	});

});