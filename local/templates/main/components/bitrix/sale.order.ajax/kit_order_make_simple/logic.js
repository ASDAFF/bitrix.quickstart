(function() {
    'use strict';

    BX.OrderAjaxLogic = {

        options: {},
        orderSaveAllowed: false,

        init: function(parameters)
		{
			this.result = parameters.result || {};
            this.params = parameters.params || {};

            //console.log(this.result);

			this.signedParamsString = parameters.signedParamsString || '';
			this.siteId = parameters.siteID || '';
			this.ajaxUrl = parameters.ajaxUrl || '';
			this.templateFolder = parameters.templateFolder || '';

			this.orderBlockNode = BX(parameters.orderBlockId);
			this.totalBlockNode = BX(parameters.totalBlockId);
			
			this.savedFilesBlockNode = BX('bx-soa-saved-files');
			this.orderSaveBlockNode = BX('kit-bx-soa-orderSave');
			//this.mainErrorsNode = BX('bx-soa-main-notifications');

            this.basketBlockNode = BX(parameters.basketBlockId);
            this.basketRowsNode = BX(parameters.basketRowsId);
            this.kitSoaBlock = BX(parameters.kitSoaBlockId);

			if (this.result.SHOW_AUTH)
			{
				this.authGenerateUser = this.result.AUTH.new_user_registration_email_confirmation != 'Y';
			}

			if (this.totalBlockNode)
			{
                this.totalInfoBlockNode = this.totalBlockNode;
			}

			this.options.totalPriceChanged = false;

			/*if (!this.result.IS_AUTHORIZED || typeof this.result.LAST_ORDER_DATA.FAIL !== 'undefined')
				this.initFirstSection();*/

			this.initOptions();
			this.editOrder();

            // BX.bind(this.orderSaveBlockNode.querySelector('[data-save-button]'), 'click', BX.proxy(this.clickOrderSaveAction, this)); for order save

			/*if (this.params.USER_CONSENT === 'Y')
			{
				this.initUserConsent();
			}*/
        },

        editCoupons: function(basketItemsNode)
		{
			var couponsList = this.getCouponsList(true),
				couponsLabel = this.getCouponsLabel(true),
				couponsBlock = BX.create('DIV', {
					props: {className: 'bx-soa-coupon-block'},
					children: [
						BX.create('DIV', {
							props: {className: 'bx-soa-coupon-input main_order_block_feedback_input fonts__small_text'},
							children: [
								BX.create('INPUT', {
									props: {
										className: 'form-control feedback-input',
										type: 'text'
									},
									events: {
										change: BX.delegate(function(event) {
											var newCoupon = BX.getEventTarget(event);
											if (newCoupon && newCoupon.value)
											{
												this.sendRequest('enterCoupon', newCoupon.value);
												newCoupon.value = '';
											}
                                        }, this),
                                        keydown: function(e) {
                                            if(e.which == 13 || e.keyCode == 13) {
                                                e.preventDefault();
                                                BX.fireEvent(this, 'change');
                                            }
                                        }
									}
								})
							]
						}),
						BX.create('DIV', {props: {className: 'bx-soa-coupon-item fonts__middle_comment'}, children: couponsList})
					]
                });

			basketItemsNode.appendChild(
				BX.create('DIV', {
					props: {className: 'bx-soa-coupon'},
					children: [
						couponsLabel,
						couponsBlock
					]
				})
			);
        },

        getCouponsList: function(active)
		{
			var couponsList = [], i;

			for (i = 0; i < this.result.COUPON_LIST.length; i++)
			{
				if (active || (!active && this.result.COUPON_LIST[i].JS_STATUS == 'APPLIED'))
				{
					couponsList.push(this.getCouponNode({
						text: this.result.COUPON_LIST[i].COUPON,
						desc: this.result.COUPON_LIST[i].JS_CHECK_CODE,
						status: this.result.COUPON_LIST[i].JS_STATUS
					}, active));
				}
			}

			return couponsList;
        },

        getCouponNode: function(coupon, active)
		{
			var couponName = BX.util.htmlspecialchars(coupon.text) || '',
				couponDesc = coupon.desc && coupon.desc.length
					? coupon.desc.charAt(0).toUpperCase() + coupon.desc.slice(1)
					: BX.message('SOA_NOT_FOUND'),
				couponStatus = coupon.status || 'BAD',
				couponItem, tooltip, faClass;

			switch (couponStatus.toUpperCase())
			{
				case 'ENTERED': couponItem = 'used'; tooltip = 'warning'; faClass = 'fa-times-circle'; break;
				case 'BAD': couponItem = tooltip = 'danger'; faClass = 'fa-times-circle'; break;
				default: couponItem = tooltip  = 'success'; faClass = 'fa-check-circle';
			}

			return BX.create('STRONG', {
				attrs: {
					'data-coupon': couponName,
					className: 'bx-soa-coupon-item-' + couponItem
				},
				children: active ? [
                    BX.create('I', {
						props: {className: 'fas ' + faClass}
					}),
					'&nbsp;' + couponName || '',
					BX.create('SPAN', {
						props: {className: 'bx-soa-coupon-remove fas'},
						events: {
							click: BX.delegate(function(e){
								var target = e.target || e.srcElement,
									coupon = BX.findParent(target, {tagName: 'STRONG'});

								if (coupon && coupon.getAttribute('data-coupon'))
								{
									this.sendRequest('removeCoupon', coupon.getAttribute('data-coupon'))
								}
							}, this)
						}
					}),
					BX.create('SPAN', {
						props: {
							className: 'bx-soa-tooltip bx-soa-tooltip-coupon bx-soa-tooltip-' + tooltip + ' tooltip top'
						},
						children: [
							BX.create('SPAN', {props: {className: 'tooltip-arrow'}}),
							BX.create('SPAN', {props: {className: 'tooltip-inner'}, text: couponDesc})
						]
					})
				] : [couponName]
			});
        },

        getCouponsLabel: function(active)
		{
			return BX.create('DIV', {
				props: {className: 'main_order_block_feedback_comment fonts__small_text'},
				children: active
					? [BX.create('LABEL', {html: this.params.MESS_USE_COUPON + ':'})]
					: [this.params.MESS_COUPON + ':']
			});
        },

        addCoupon: function(coupon)
		{
            var couponListNodes = this.orderBlockNode.querySelectorAll('.bx-soa-coupon .bx-soa-coupon-item');

			for (var i = 0; i < couponListNodes.length; i++)
			{
				if (couponListNodes[i].querySelector('[data-coupon="' + BX.util.htmlspecialchars(coupon) + '"]'))
					break;

				couponListNodes[i].appendChild(this.getCouponNode({text: coupon}, true, 'bx-soa-coupon-item-danger'));
			}
		},

		removeCoupon: function(coupon)
		{
            var couponNodes = this.orderBlockNode.querySelectorAll('[data-coupon="' + BX.util.htmlspecialchars(coupon) + '"]'), i;

			for (i in couponNodes)
			{
				if (couponNodes.hasOwnProperty(i))
				{
					BX.remove(couponNodes[i]);
				}
			}
        },



        sendRequest: function(action, actionData)
		{
            //console.log('action ' + action + ', actionData ' + actionData);

			if (!this.startLoader())
				return;

			this.firstLoad = false;

			action = BX.type.isNotEmptyString(action) ? action : 'refreshOrderAjax';

			if (action === 'saveOrderAjax')
			{
				BX.ajax.submit(BX('ORDER_FORM'), BX.proxy(this.saveOrder, this));
			}
			else
			{
				BX.ajax({
					method: 'POST',
					dataType: 'json',
					url: this.ajaxUrl,
					data: this.getData(action, actionData),
					onsuccess: BX.delegate(function(result) {

                        //console.log('result');
                        //console.log(result);

						if (result.redirect && result.redirect.length)
							document.location.href = result.redirect;

						switch (action)
						{
							case 'refreshOrderAjax':
								this.refreshOrder(result);
								break;
							case 'showAuthForm':
								this.firstLoad = true;
								this.refreshOrder(result);
								break;
							case 'enterCoupon':
								if (result && result.order)
								{
									this.deliveryCachedInfo = [];
                                    this.refreshOrder(result);
								}
								else
								{
									this.addCoupon(result);
								}

								break;
							case 'removeCoupon':
								if (result && result.order)
								{
									this.deliveryCachedInfo = [];
                                    this.refreshOrder(result);
								}
								else
								{
									this.removeCoupon(result);
								}

								break;
						}
						BX.cleanNode(this.savedFilesBlockNode);
						this.endLoader();
					}, this),
					onfailure: BX.delegate(function() {
						this.endLoader();
					}, this)
				});
			}
        },

        getData: function(action, actionData)
		{
			var data = {
				order: this.getAllFormData(),
				sessid: BX.bitrix_sessid(),
				via_ajax: 'Y',
				SITE_ID: this.siteId,
				signedParamsString: this.signedParamsString
			};

			data[this.params.ACTION_VARIABLE] = action;

			if (action === 'enterCoupon' || action === 'removeCoupon')
				data.coupon = actionData;

			return data;
        },
        
        getAllFormData: function()
		{
			var form = BX('ORDER_FORM'),
				prepared = BX.ajax.prepareForm(form),
				i;

			for (i in prepared.data)
			{
				if (prepared.data.hasOwnProperty(i) && i == '')
				{
					delete prepared.data[i];
				}
			}

			return !!prepared && prepared.data ? prepared.data : {};
        },
        
        refreshOrder: function(result)
		{
            this.result = result.order;
            this.deliveryLocationInfo = {};

            this.initOptions();
            this.editOrder();
            this.mapsReady && this.initMaps();
            BX.saleOrderAjax && BX.saleOrderAjax.initDeferredControl();

			return true;
        },

        initOptions: function()
		{
			var total;

			if (this.result.TOTAL)
			{
				total = this.result.TOTAL;
				this.options.showOrderWeight = total.ORDER_WEIGHT && parseFloat(total.ORDER_WEIGHT) > 0;
				this.options.showPriceWithoutDiscount = parseFloat(total.ORDER_PRICE) < parseFloat(total.PRICE_WITHOUT_DISCOUNT_VALUE);
				this.options.showDiscountPrice = total.DISCOUNT_PRICE && parseFloat(total.DISCOUNT_PRICE) > 0;
				this.options.showTaxList = total.TAX_LIST && total.TAX_LIST.length;
				this.options.showPayedFromInnerBudget = total.PAYED_FROM_ACCOUNT_FORMATED && total.PAYED_FROM_ACCOUNT_FORMATED.length;
			}
		},
        
        editOrder: function()
		{
			if (!this.orderBlockNode || !this.result)
				return;
            
            var active = false;

            this.editBasketBlock(active);
            this.editTotalBlock();
        },

        editBasketBlock: function(active)
		{
			if (!this.basketBlockNode/* || !this.basketHiddenBlockNode || !this.result.GRID*/)
				return;

			//BX.remove(BX.lastChild(this.basketBlockNode));

			this.editActiveBasketBlock(active);
        },
        
        editActiveBasketBlock: function(activeNodeMode)
		{
			var node = this.basketBlockNode,
				basketContent;
            
            basketContent = node.querySelector('.bx-soa-section-content');

            // update all blocks with order price
            var orderPrice = this.kitSoaBlock.querySelectorAll('.kit_soa_order_price');
            if (orderPrice.length)
                for (var i = 0; i < orderPrice.length; i++)
                    orderPrice[i].innerHTML = this.result.TOTAL.ORDER_PRICE_FORMATED;

            // update discount in total block
            var priceOrderBlock = this.kitSoaBlock.querySelector('.price_order_block');
            var discountTotalBlock = this.kitSoaBlock.querySelector('.kit_soa_discount_total_block');
            var discountValue = this.kitSoaBlock.querySelector('.kit_soa_discount_value');

            if (this.result.TOTAL.DISCOUNT_PRICE > 0)
            {
                if(discountValue)
                    discountValue.innerHTML = this.result.TOTAL.DISCOUNT_PRICE_FORMATED;
                else
                {
                    priceOrderBlock.insertBefore(
                        BX.create('DIV', {
                            props: {className: 'kit_soa_discount_total_block price_order_block__item fonts__small_text'},
                            children: [
                                BX.create('span', {
                                    text: BX.message('SOA_TEMPL_SUM_DISCOUNT') + ' '
                                }),
                                BX.create('b', {
                                    props: {className: 'kit_soa_discount_value'},
                                    text: this.result.TOTAL.DISCOUNT_PRICE_FORMATED
                                })
                            ]
                        }),
                        priceOrderBlock.children[1]
                    );
                }
            }
            else
            {
                BX.remove(discountTotalBlock);
            }

            // update block with order total price
            var orderTotalPrice = this.kitSoaBlock.querySelector('.kit_soa_order_total_price');
            orderTotalPrice.innerHTML = this.result.TOTAL.ORDER_TOTAL_PRICE_FORMATED;
            


            // update table with products
            var products = this.result.GRID.ROWS;
            for (var key in products)
            {
                //console.log(products[key]);

                var productSumBlock = this.basketRowsNode.querySelector('div.kit_soa_product_sum_block_' + key);
                var productSum = this.basketRowsNode.querySelector('div.kit_soa_product_sum_' + key);
                var oldProductSum = this.basketRowsNode.querySelector('div.kit_soa_old_product_sum_' + key);

                var discountPrice = this.basketRowsNode.querySelector('div.kit_soa_discount_price_' + key);

                var priceBlock = this.basketRowsNode.querySelector('div.kit_soa_price_block_' + key);
                var price = this.basketRowsNode.querySelector('div.kit_soa_price_' + key);
                var oldPrice = this.basketRowsNode.querySelector('div.kit_soa_old_price_' + key);

                productSum.innerHTML = products[key].data.SUM;
                discountPrice.innerHTML = products[key].data.DISCOUNT_PRICE_PERCENT_FORMATED;
                price.innerHTML = products[key].data.PRICE_FORMATED;

                if (products[key].data.DISCOUNT_PRICE > 0)
                {
                    if(oldPrice)
                        oldPrice.innerHTML = products[key].data.BASE_PRICE_FORMATED;
                    else
                    {
                        priceBlock.appendChild(
                            BX.create('DIV', {
                                props: {className: 'kit_soa_old_price_' + key + ' main_order_block__item_price_old fonts__middle_comment'},
                                text: products[key].data.BASE_PRICE_FORMATED
                            })
                        );
                    }
                }
                else
                {
                    BX.remove(oldPrice);
                }

                if (products[key].data.DISCOUNT_PRICE > 0)
                {
                    if(oldProductSum)
                        oldProductSum.innerHTML = products[key].data.SUM_BASE_FORMATED;
                    else
                    {
                        productSumBlock.appendChild(
                            BX.create('DIV', {
                                props: {className: 'kit_soa_old_product_sum_' + key + ' main_order_block__item_price_old fonts__middle_comment'},
                                text: products[key].data.SUM_BASE_FORMATED
                            })
                        );
                    }
                }
                else
                {
                    BX.remove(oldProductSum);
                }
            }
            

            if (!basketContent)
            {
                basketContent = this.getNewContainer();
                node.appendChild(basketContent);
            }
            else
            {
                BX.cleanNode(basketContent);
            }

            if (this.params.SHOW_COUPONS_BASKET === 'Y')
            {
                this.editCoupons(basketContent);
            }
		},
        
        getNewContainer: function(notFluid)
		{
			return BX.create('DIV', {props: {className: 'bx-soa-section-content' + (!!notFluid ? '' : ' container-fluid')}});
        },
        
        editTotalBlock: function()
		{
			if (!this.totalInfoBlockNode || !this.result.TOTAL)
				return;

			var total = this.result.TOTAL,
				priceHtml, params = {},
				discText, valFormatted, i,
				curDelivery, deliveryError, deliveryValue,
				showOrderButton = this.params.SHOW_TOTAL_ORDER_BUTTON === 'Y';

			BX.cleanNode(this.totalInfoBlockNode);

			if (parseFloat(total.ORDER_PRICE) === 0)
			{
				priceHtml = this.params.MESS_PRICE_FREE;
				params.free = true;
			}
			else
			{
				priceHtml = total.ORDER_PRICE_FORMATED;
            }

			if (this.options.showPriceWithoutDiscount)
			{
				priceHtml += '<br><span class="bx-price-old">' + total.PRICE_WITHOUT_DISCOUNT + '</span>';
			}

			this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_SUMMARY'), priceHtml, params));

			if (this.options.showOrderWeight)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_WEIGHT_SUM'), total.ORDER_WEIGHT_FORMATED));
			}

			if (this.options.showTaxList)
			{
				for (i = 0; i < total.TAX_LIST.length; i++)
				{
					valFormatted = total.TAX_LIST[i].VALUE_MONEY_FORMATED || '';
					this.totalInfoBlockNode.appendChild(
						this.createTotalUnit(
							total.TAX_LIST[i].NAME + (!!total.TAX_LIST[i].VALUE_FORMATED ? ' ' + total.TAX_LIST[i].VALUE_FORMATED : '') + ':',
							valFormatted
						)
					);
				}
			}

			params = {};
			//curDelivery = this.getSelectedDelivery();
			//deliveryError = curDelivery && curDelivery.CALCULATE_ERRORS && curDelivery.CALCULATE_ERRORS.length;

			if (deliveryError)
			{
				deliveryValue = BX.message('SOA_NOT_CALCULATED');
				params.error = deliveryError;
			}
			else
			{
				if (parseFloat(total.DELIVERY_PRICE) === 0)
				{
					deliveryValue = this.params.MESS_PRICE_FREE;
					params.free = true;
				}
				else
				{
					deliveryValue = total.DELIVERY_PRICE_FORMATED;
				}

				if (
					curDelivery && typeof curDelivery.DELIVERY_DISCOUNT_PRICE !== 'undefined'
					&& parseFloat(curDelivery.PRICE) > parseFloat(curDelivery.DELIVERY_DISCOUNT_PRICE)
				)
				{
					deliveryValue += '<br><span class="bx-price-old">' + curDelivery.PRICE_FORMATED + '</span>';
				}
            }

			if (this.result.DELIVERY)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_DELIVERY'), deliveryValue, params));
            }

			if (this.options.showDiscountPrice)
			{
				discText = this.params.MESS_ECONOMY;
				if (total.DISCOUNT_PERCENT_FORMATED && parseFloat(total.DISCOUNT_PERCENT_FORMATED) > 0)
					discText += total.DISCOUNT_PERCENT_FORMATED;

				this.totalInfoBlockNode.appendChild(this.createTotalUnit(discText + ':', total.DISCOUNT_PRICE_FORMATED, {highlighted: true}));
			}

			if (this.options.showPayedFromInnerBudget)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED));
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_PAYED'), total.PAYED_FROM_ACCOUNT_FORMATED));
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_LEFT_TO_PAY'), total.ORDER_TOTAL_LEFT_TO_PAY_FORMATED, {total: true}));
			}
			else
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_SUM_IT'), total.ORDER_TOTAL_PRICE_FORMATED, {total: true}));
			}

			if (parseFloat(total.PAY_SYSTEM_PRICE) >= 0 && this.result.DELIVERY.length)
			{
				this.totalInfoBlockNode.appendChild(this.createTotalUnit(BX.message('SOA_PAYSYSTEM_PRICE'), '~' + total.PAY_SYSTEM_PRICE_FORMATTED));
			}
        },
        
        createTotalUnit: function(name, value, params)
		{
			var totalValue, className = 'bx-soa-cart-total-line';

			name = name || '';
			value = value || '';
			params = params || {};

			if (params.error)
			{
				totalValue = [BX.create('A', {
					props: {className: 'bx-soa-price-not-calc'},
					html: value,
					events: {
						click: BX.delegate(function(){
							//this.animateScrollTo(this.deliveryBlockNode);
						}, this)
					}
				})];
			}
			else if (params.free)
			{
				totalValue = [BX.create('SPAN', {
					props: {className: 'bx-soa-price-free'},
					html: value
				})];
			}
			else
			{
				totalValue = [value];
            }

			if (params.total)
			{
				className += ' bx-soa-cart-total-line-total';
			}

			if (params.highlighted)
			{
				className += ' bx-soa-cart-total-line-highlighted';
			}

			return BX.create('DIV', {
				props: {className: className},
				children: [
					BX.create('SPAN', {props: {className: 'bx-soa-cart-t'}, text: name}),
					BX.create('SPAN', {
						props: {
							className: 'bx-soa-cart-d' + (!!params.total && this.options.totalPriceChanged ? ' bx-soa-changeCostSign' : '')
						},
						children: totalValue
					})
				]
			});
		},

        startLoader: function()
		{
			if (this.BXFormPosting === true)
				return false;

			this.BXFormPosting = true;

			if (!this.loadingScreen)
			{
				this.loadingScreen = new BX.PopupWindow('loading_screen', null, {
					overlay: {backgroundColor: 'white', opacity: 1},
					events: {
						onAfterPopupShow: BX.delegate(function() {
							BX.cleanNode(this.loadingScreen.popupContainer);
							BX.removeClass(this.loadingScreen.popupContainer, 'popup-window');
							this.loadingScreen.popupContainer.appendChild(
								BX.create('IMG', {props: {src: this.templateFolder + '/images/loader.gif'}})
							);
							this.loadingScreen.popupContainer.removeAttribute('style');
							this.loadingScreen.popupContainer.style.display = 'block';
						}, this)
					}
				});
				BX.addClass(this.loadingScreen.overlay.element, 'bx-step-opacity');
			}

			this.loadingScreen.overlay.element.style.opacity = '0';
			this.loadingScreen.show();
			this.loadingScreen.overlay.element.style.opacity = '0.6';

			return true;
		},
		
		endLoader: function()
		{
			this.BXFormPosting = false;

			if (this.loadingScreen && this.loadingScreen.isShown())
			{
				this.loadingScreen.close();
			}
        },

        /* ------------------------------ */

        
        /*clickOrderSaveAction: function(event)
		{
			if (this.isValidForm())
			{
				this.allowOrderSave();

				if (this.params.USER_CONSENT === 'Y' && BX.UserConsent)
				{
					BX.onCustomEvent('bx-soa-order-save', []);
				}
				else
				{
					this.doSaveAction();
				}
			}

			return BX.PreventDefault(event);
		},

		doSaveAction: function()
		{
			if (this.isOrderSaveAllowed())
			{
				//this.reachGoal('order');
				this.sendRequest('saveOrderAjax');
			}
        },
        
        isOrderSaveAllowed: function()
		{
			return this.orderSaveAllowed === true;
		},
        
        allowOrderSave: function()
		{
			this.orderSaveAllowed = true;
		},

		disallowOrderSave: function()
		{
			this.orderSaveAllowed = false;
		},
        
        initUserConsent: function()
		{
			BX.ready(BX.delegate(function(){
				var control = BX.UserConsent && BX.UserConsent.load(this.orderBlockNode);
				if (control)
				{
					BX.addCustomEvent(control, BX.UserConsent.events.save, BX.proxy(this.doSaveAction, this));
					BX.addCustomEvent(control, BX.UserConsent.events.refused, BX.proxy(this.disallowOrderSave, this));
				}
			}, this));
        }*/
    };
})();