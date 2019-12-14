(function (window) {

if (!!window.JCCatalogBigdataProducts)
{
	return;
}

window.JCCatalogBigdataProducts = function (arParams)
{
/*
	this.productType = 0;
	this.showQuantity = true;
	this.showAbsent = true;
	this.secondPict = false;
	this.showOldPrice = false;
	this.showPercent = false;
	this.showSkuProps = false;
	this.visual = {
		ID: '',
		PICT_ID: '',
		SECOND_PICT_ID: '',
		QUANTITY_ID: '',
		QUANTITY_UP_ID: '',
		QUANTITY_DOWN_ID: '',
		PRICE_ID: '',
		DSC_PERC: '',
		SECOND_DSC_PERC: '',
		DISPLAY_PROP_DIV: '',
		BASKET_PROP_DIV: ''
	};
	this.product = {
		checkQuantity: false,
		maxQuantity: 0,
		stepQuantity: 1,
		isDblQuantity: false,
		canBuy: true,
		canSubscription: true,
		name: '',
		pict: {},
		id: 0,
		addUrl: '',
		buyUrl: ''
	};
	this.basketData = {
		useProps: false,
		emptyProps: false,
		quantity: 'quantity',
		props: 'prop',
		basketUrl: ''
	};

	this.defaultPict = {
		pict: null,
		secondPict: null
	};

	this.checkQuantity = false;
	this.maxQuantity = 0;
	this.stepQuantity = 1;
	this.isDblQuantity = false;
	this.canBuy = true;
	this.canSubscription = true;
	this.precision = 6;
	this.precisionFactor = Math.pow(10,this.precision);

	this.offers = [];
	this.offerNum = 0;
	this.treeProps = [];
	this.obTreeRows = [];
	this.showCount = [];
	this.showStart = [];
	this.selectedValues = {};

	this.obProduct = null;
	this.obQuantity = null;
	this.obQuantityUp = null;
	this.obQuantityDown = null;
	this.obPict = null;
	this.obSecondPict = null;
	this.obPrice = null;
	this.obTree = null;
	this.obBuyBtn = null;
	this.obDscPerc = null;
	this.obSecondDscPerc = null;
	this.obSkuProps = null;
	this.obMeasure = null;

	this.obPopupWin = null;
	this.basketUrl = '';
	this.basketParams = {};

	this.treeRowShowSize = 5;
	this.treeEnableArrow = { display: '', cursor: 'pointer', opacity: 1 };
	this.treeDisableArrow = { display: '', cursor: 'default', opacity:0.2 };

	this.lastElement = false;
	this.containerHeight = 0;

	this.errorCode = 0;

	if ('object' === typeof arParams)
	{
		this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
		this.showQuantity = arParams.SHOW_QUANTITY;
		this.showAbsent = arParams.SHOW_ABSENT;
		this.secondPict = !!arParams.SECOND_PICT;
		this.showOldPrice = !!arParams.SHOW_OLD_PRICE;
		this.showPercent = !!arParams.SHOW_DISCOUNT_PERCENT;
		this.showSkuProps = !!arParams.SHOW_SKU_PROPS;

		this.visual = arParams.VISUAL;
		switch (this.productType)
		{
			case 1://product
			case 2://set
				if (!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT))
				{
					if (this.showQuantity)
					{
						this.product.checkQuantity = arParams.PRODUCT.CHECK_QUANTITY;
						this.product.isDblQuantity = arParams.PRODUCT.QUANTITY_FLOAT;
						if (this.product.checkQuantity)
						{
							this.product.maxQuantity = (this.product.isDblQuantity ? parseFloat(arParams.PRODUCT.MAX_QUANTITY) : parseInt(arParams.PRODUCT.MAX_QUANTITY, 10));
						}
						this.product.stepQuantity = (this.product.isDblQuantity ? parseFloat(arParams.PRODUCT.STEP_QUANTITY) : parseInt(arParams.PRODUCT.STEP_QUANTITY, 10));

						this.checkQuantity = this.product.checkQuantity;
						this.isDblQuantity = this.product.isDblQuantity;
						this.maxQuantity = this.product.maxQuantity;
						this.stepQuantity = this.product.stepQuantity;
						if (this.isDblQuantity)
						{
							this.stepQuantity = Math.round(this.stepQuantity*this.precisionFactor)/this.precisionFactor;
						}
					}
					this.product.canBuy = arParams.PRODUCT.CAN_BUY;
					this.product.canSubscription = arParams.PRODUCT.SUBSCRIPTION;

					this.canBuy = this.product.canBuy;
					this.canSubscription = this.product.canSubscription;

					this.product.name = arParams.PRODUCT.NAME;
					this.product.pict = arParams.PRODUCT.PICT;
					this.product.id = arParams.PRODUCT.ID;
					if (!!arParams.PRODUCT.ADD_URL)
					{
						this.product.addUrl = arParams.PRODUCT.ADD_URL;
					}
					if (!!arParams.PRODUCT.BUY_URL)
					{
						this.product.buyUrl = arParams.PRODUCT.BUY_URL;
					}
					if (!!arParams.BASKET && 'object' === typeof(arParams.BASKET))
					{
						this.basketData.useProps = !!arParams.BASKET.ADD_PROPS;
						this.basketData.emptyProps = !!arParams.BASKET.EMPTY_PROPS;
					}
				}
				else
				{
					this.errorCode = -1;
				}
				break;
			case 3://sku
				if (!!arParams.OFFERS && BX.type.isArray(arParams.OFFERS))
				{
					if (!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT))
					{
						this.product.name = arParams.PRODUCT.NAME;
						this.product.id = arParams.PRODUCT.ID;
					}
					this.offers = arParams.OFFERS;
					this.offerNum = 0;
					if (!!arParams.OFFER_SELECTED)
					{
						this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
					}
					if (isNaN(this.offerNum))
					{
						this.offerNum = 0;
					}
					if (!!arParams.TREE_PROPS)
					{
						this.treeProps = arParams.TREE_PROPS;
					}
					if (!!arParams.DEFAULT_PICTURE)
					{
						this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;
						this.defaultPict.secondPict = arParams.DEFAULT_PICTURE.PICTURE_SECOND;
					}
				}
				else
				{
					this.errorCode = -1;
				}
				break;
			default:
				this.errorCode = -1;
		}
		if (!!arParams.BASKET && 'object' === typeof(arParams.BASKET))
		{
			if (!!arParams.BASKET.QUANTITY)
			{
				this.basketData.quantity = arParams.BASKET.QUANTITY;
			}
			if (!!arParams.BASKET.PROPS)
			{
				this.basketData.props = arParams.BASKET.PROPS;
			}
			if (!!arParams.BASKET.BASKET_URL)
			{
				this.basketData.basketUrl = arParams.BASKET.BASKET_URL;
			}
		}
		this.lastElement = (!!arParams.LAST_ELEMENT && 'Y' === arParams.LAST_ELEMENT);
	}
	if (0 === this.errorCode)
	{
		BX.ready(BX.delegate(this.Init,this));
	}
*/
};

/**
 * @deprecated
 * @param obj
 * @param productId
 * @constructor
 */
window.JCCatalogBigdataProducts.prototype.RememberRecommendation = function(obj, productId)
{
	var rcmContainer = BX.findParent(obj, {'className':'bigdata_recommended_products_items'});
	var rcmId = BX.findChild(rcmContainer, {'attr':{'name':'bigdata_recommendation_id'}}, true).value;

	this.RememberProductRecommendation(rcmId, productId);
};

window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation = function(recommendationId, productId)
{
	// save to RCM_PRODUCT_LOG
	var plCookieName = BX.cookie_prefix+'_RCM_PRODUCT_LOG';
	var plCookie = getCookie(plCookieName);
	var itemFound = false;

	var cItems = [],
		cItem;

	if (plCookie)
	{
		cItems = plCookie.split('.');
	}

	var i = cItems.length;

	while (i--)
	{
		cItem = cItems[i].split('-');

		if (cItem[0] == productId)
		{
			// it's already in recommendations, update the date
			cItem = cItems[i].split('-');

			// update rcmId and date
			cItem[1] = recommendationId;

			cItem[2] = BX.current_server_time;

			cItems[i] = cItem.join('-');
			itemFound = true;
		}
		else
		{
			if ((BX.current_server_time - cItem[2]) > 3600*24*30)
			{
				cItems.splice(i, 1);
			}
		}
	}

	if (!itemFound)
	{
		// add recommendation
		cItems.push([productId, recommendationId, BX.current_server_time].join('-'));
	}

	// serialize
	var plNewCookie = cItems.join('.');

	var cookieDate = new Date(new Date().getTime() + 1000*3600*24*365*10);
	document.cookie=plCookieName+"="+plNewCookie+"; path=/; expires="+cookieDate.toUTCString()+"; domain="+BX.cookie_domain;
};

})(window);

function getCookie(name) {
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

/**
 * @deprecated see ajax.php
 * @param rcm_items_cont
 */
function bx_rcm_recommendation_event_attaching(rcm_items_cont)
{
	return null;
}

function bx_rcm_adaptive_recommendation_event_attaching(items, uniqId)
{
	// onclick handler
	var callback = function (e)  {

		var link = BX(this), j;

		for (j in items)
		{
			if (items[j].productUrl == link.getAttribute('href'))
			{
				window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation(
					items[j].recommendationId, items[j].productId
				);

				break;
			}
		}
	};

	// check if a container was defined is the template
	var itemsContainer = BX(uniqId);

	if (!itemsContainer)
	{
		// then get all the links
		itemsContainer = document.body;
	}

	var links = BX.findChildren(itemsContainer, {tag:'a'}, true);

	// bind
	if (links)
	{
		var i;
		for (i in links)
		{
			BX.bind(links[i], 'click', callback);
		}
	}
}

function bx_rcm_get_from_cloud(injectId, rcmParameters, localAjaxData)
{
	var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
	var data = BX.ajax.prepareData(rcmParameters);

	if (data)
	{
		url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
	}

	var onready = function(response) {

		if (!response.items)
		{
			response.items = [];
		}
		BX.ajax({
			url: '/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id}),
			method: 'POST',
			data: localAjaxData,
			dataType: 'html',
			processData: false,
			start: true,
			onsuccess: function (html) {
				var ob = BX.processHTML(html);

				// inject
				BX(injectId).innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
				
				appSLine.gridInit();
			}
		});
	};

	BX.ajax({
		'method': 'GET',
		'dataType': 'json',
		'url': url,
		'timeout': 3,
		'onsuccess': onready,
		'onfailure': onready
	});
}