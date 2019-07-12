(function (window) {

if (!!window.JCCatalogSection)
{
	return;
}

window.JCCatalogSection = function (arParams)
{
	this.skuVisualParams = {
		SELECT:
		{
			TAG_BIND: 'select',
			TAG: 'option',
			ACTIVE_CLASS: 'active',
			HIDE_CLASS: 'hidden',
			EVENT: 'change',
		},
		LI:
		{
			TAG_BIND: 'li',
			TAG: 'li',
			ACTIVE_CLASS: 'active',
			HIDE_CLASS: 'missing',
			EVENT: 'click',
		}
	};
	this.productType = 0;
	this.showQuantity = true;
	this.showAbsent = true;
	this.secondPict = false;
	this.showOldPrice = false;
	this.showPercent = false;
	this.showSkuProps = false;
	this.basketAction = 'ADD';
	this.basketLinkURL = '';
	this.showClosePopup = false;
	this.useCompare = false;
	this.visual = {
		ID: '',
		PICT_ID: '',
		SECOND_PICT_ID: '',
		QUANTITY_ID: '',
		QUANTITY_UP_ID: '',
		QUANTITY_DOWN_ID: '',
		STORE_QUANTITY: '',
		PRICE_ID: '',
		PRICE_OLD_ID: '',
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

	this.basketMode = '';
	this.basketData = {
		useProps: false,
		emptyProps: false,
		quantity: 'quantity',
		props: 'prop',
		basketUrl: '',
		sku_props: '',
		sku_props_var: 'basket_props',
		add_url: '',
		buy_url: ''
	};

	this.compareData = {
		compareUrl: '',
		comparePath: ''
	};

	this.defaultPict = {
		pict: null,
		secondPict: null
	};

	this.checkQuantity = false;
	this.maxQuantity = 0;
	this.stepQuantity = 1;
	this.defaultCount = 1;
	this.isDblQuantity = false;
	this.canBuy = true;
	this.currentBasisPrice = {};
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
	this.obBasketBtn = null;
	this.obBasketActions = null;
	this.obNotAvail = null;
	this.obDscPerc = null;
	this.obSecondDscPerc = null;
	this.obSkuProps = null;
	this.obMeasure = null;
	this.obCompare = null;

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
		if (!!arParams.ADD_TO_BASKET_ACTION)
		{
			this.basketAction = arParams.ADD_TO_BASKET_ACTION;
		}
		this.showClosePopup = !!arParams.SHOW_CLOSE_POPUP;
		this.useCompare = !!arParams.DISPLAY_COMPARE;

		this.visual = arParams.VISUAL;
		this.defaultCount = arParams.DEFAULT_COUNT;
		this.basketLinkURL = arParams.BASKET_URL;
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
					if (!!arParams.PRODUCT.BASIS_PRICE)
					{
						this.currentBasisPrice = arParams.PRODUCT.BASIS_PRICE;
					}

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
			if (3 === this.productType)
			{
				if (!!arParams.BASKET.SKU_PROPS)
				{
					this.basketData.sku_props = arParams.BASKET.SKU_PROPS;
				}
			}
			if (!!arParams.BASKET.ADD_URL_TEMPLATE)
			{
				this.basketData.add_url = arParams.BASKET.ADD_URL_TEMPLATE;
			}
			if (!!arParams.BASKET.BUY_URL_TEMPLATE)
			{
				this.basketData.buy_url = arParams.BASKET.BUY_URL_TEMPLATE;
			}
			if (this.basketData.add_url === '' && this.basketData.buy_url === '')
			{
				this.errorCode = -1024;
			}
		}
		if (this.useCompare)
		{
			if (!!arParams.COMPARE && typeof(arParams.COMPARE) === 'object')
			{
				if (!!arParams.COMPARE.COMPARE_PATH)
				{
					this.compareData.comparePath = arParams.COMPARE.COMPARE_PATH;
				}
				if (!!arParams.COMPARE.COMPARE_URL_TEMPLATE_DEL)
				{
					this.compareData.compareUrlDel = arParams.COMPARE.COMPARE_URL_TEMPLATE_DEL;
				}
				if (!!arParams.COMPARE.COMPARE_URL_TEMPLATE)
				{
					this.compareData.compareUrl = arParams.COMPARE.COMPARE_URL_TEMPLATE;
				}
				else
				{
					this.useCompare = false;
				}
			}
			else
			{
				this.useCompare = false;
			}
		}

		this.lastElement = (!!arParams.LAST_ELEMENT && 'Y' === arParams.LAST_ELEMENT);
	}
	if (0 === this.errorCode)
	{
		BX.ready(BX.delegate(this.Init,this));
	}
};

window.JCCatalogSection.prototype.Init = function()
{
	var i = 0,
		strPrefix = '',
		TreeItems = null;

	this.obProduct = BX(this.visual.ID);
	if (!this.obProduct)
	{
		this.errorCode = -1;
	}
	this.obPict = BX(this.visual.PICT_ID);
	if (!this.obPict)
	{
		this.errorCode = -2;
	}
	if (this.secondPict && !!this.visual.SECOND_PICT_ID)
	{
		this.obSecondPict = BX(this.visual.SECOND_PICT_ID);
	}
	this.storeQuanity = BX(this.visual.STORE_QUANTITY);
	this.obPrice = BX(this.visual.PRICE_ID);
	this.obPriceOld = BX(this.visual.PRICE_OLD_ID);
	if (!this.obPrice)
	{
		this.errorCode = -16;
	}
	if (this.showQuantity && !!this.visual.QUANTITY_ID)
	{
		this.obQuantity = BX(this.visual.QUANTITY_ID);
		if (!!this.visual.QUANTITY_UP_ID)
		{
			this.obQuantityUp = BX(this.visual.QUANTITY_UP_ID);
		}
		if (!!this.visual.QUANTITY_DOWN_ID)
		{
			this.obQuantityDown = BX(this.visual.QUANTITY_DOWN_ID);
		}
	}
	if (3 === this.productType)
	{
		if (!!this.visual.TREE_ID)
		{
			this.obTree = BX(this.visual.TREE_ID);
			if (!this.obTree)
			{
				this.errorCode = -256;
			}
			strPrefix = this.visual.TREE_ITEM_ID;
			for (i = 0; i < this.treeProps.length; i++)
			{
				this.obTreeRows[i] = {
					LIST: BX(strPrefix+this.treeProps[i].ID+'_list'),
					CONT: BX(strPrefix+this.treeProps[i].ID+'_cont')
				};
				if (!this.obTreeRows[i].LIST || !this.obTreeRows[i].CONT)
				{
					this.errorCode = -512;
					break;
				}
			}
		}
		if (!!this.visual.QUANTITY_MEASURE)
		{
			this.obMeasure = BX(this.visual.QUANTITY_MEASURE);
		}
	}

	this.obBasketActions = BX(this.visual.BASKET_ACTIONS_ID);
	if (!!this.obBasketActions)
	{
		if (!!this.visual.BUY_ID)
		{
			this.obBuyBtn = BX(this.visual.BUY_ID);
		}
		if (!!this.visual.BASKET_LINK)
		{
			this.obBasketBtn = BX(this.visual.BASKET_LINK);
		}
	}
	this.obNotAvail = BX(this.visual.NOT_AVAILABLE_MESS);

	if (this.showPercent)
	{
		if (!!this.visual.DSC_PERC)
		{
			this.obDscPerc = BX(this.visual.DSC_PERC);
		}
		if (this.secondPict && !!this.visual.SECOND_DSC_PERC)
		{
			this.obSecondDscPerc = BX(this.visual.SECOND_DSC_PERC);
		}
	}

	if (this.showSkuProps)
	{
		if (!!this.visual.DISPLAY_PROP_DIV)
		{
			this.obSkuProps = BX(this.visual.DISPLAY_PROP_DIV);
		}
	}

	if (0 === this.errorCode)
	{
		if (this.showQuantity)
		{
			if (!!this.obQuantityUp)
			{
				BX.bind(this.obQuantityUp, 'click', BX.delegate(this.QuantityUp, this));
			}
			if (!!this.obQuantityDown)
			{
				BX.bind(this.obQuantityDown, 'click', BX.delegate(this.QuantityDown, this));
			}
			if (!!this.obQuantity)
			{
				BX.bind(this.obQuantity, 'change', BX.delegate(this.QuantityChange, this));
			}
		}
		switch (this.productType)
		{
			case 1://product
				break;
			case 3://sku
				for(var key in this.skuVisualParams){
					var TreeItems = BX.findChildren(this.obTree, {tagName: this.skuVisualParams[key].TAG_BIND}, true);
					if (!!TreeItems && 0 < TreeItems.length){
						for (i = 0; i < TreeItems.length; i++){
							$(TreeItems[i]).on(this.skuVisualParams[key].EVENT, BX.delegate(this.SelectOfferProp, this));
							//BX.bind(TreeItems[i], this.skuVisualParams[key].EVENT, BX.delegate(this.SelectOfferProp, this));
						}
					}
				}
				this.SetCurrent();
				break;
		}
		// $(this.obProduct).find(".read_more.to-cart").on('click', BX.delegate(this.Add2Basket, this));
		if (!!this.obBuyBtn)
		{
			if (this.basketAction === 'ADD')
			{
				// BX.bind(this.obBuyBtn, 'click', BX.delegate(this.Add2Basket, this));


			}
			else
			{
				BX.bind(this.obBuyBtn, 'click', BX.delegate(this.BuyBasket, this));
			}
		}
		if (this.useCompare){
			this.obCompare = BX(this.visual.COMPARE_LINK_ID);
		}
	}
};

window.JCCatalogSection.prototype.checkHeight = function()
{
	this.containerHeight = parseInt(this.obProduct.parentNode.offsetHeight, 10);
	if (isNaN(this.containerHeight)){
		this.containerHeight = 0;
	}
};

window.JCCatalogSection.prototype.setHeight = function()
{
	if (0 < this.containerHeight){
		BX.adjust(this.obProduct.parentNode, {style: { height: this.containerHeight+'px'}});
	}
};

window.JCCatalogSection.prototype.clearHeight = function()
{
	BX.adjust(this.obProduct.parentNode, {style: { height: 'auto'}});
};

window.JCCatalogSection.prototype.QuantityUp = function()
{
	var curValue = 0,
		boolSet = true,
		calcPrice;

	if (0 === this.errorCode && this.showQuantity && this.canBuy)
	{
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
		if (!isNaN(curValue))
		{
			curValue += this.stepQuantity;
			if (this.checkQuantity)
			{
				if (curValue > this.maxQuantity)
				{
					boolSet = false;
				}
			}
			if (boolSet)
			{
				if (this.isDblQuantity)
				{
					curValue = Math.round(curValue*this.precisionFactor)/this.precisionFactor;
				}
				this.obQuantity.value = curValue;
			}
		}
	}
};

window.JCCatalogSection.prototype.QuantityDown = function()
{
	var curValue = 0,
		boolSet = true,
		calcPrice;

	if (0 === this.errorCode && this.showQuantity && this.canBuy)
	{
		curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value): parseInt(this.obQuantity.value, 10));
		if (!isNaN(curValue))
		{
			curValue -= this.stepQuantity;
			if (curValue < this.stepQuantity)
			{
				boolSet = false;
			}
			if (boolSet)
			{
				if (this.isDblQuantity)
				{
					curValue = Math.round(curValue*this.precisionFactor)/this.precisionFactor;
				}
				this.obQuantity.value = curValue;
			}
		}
	}
};

window.JCCatalogSection.prototype.QuantityChange = function()
{
	var curValue = 0,
		calcPrice,
		intCount,
		count;

	if (0 === this.errorCode && this.showQuantity)
	{
		if (this.canBuy)
		{
			curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
			if (!isNaN(curValue))
			{
				if (this.checkQuantity)
				{
					if (curValue > this.maxQuantity)
					{
						curValue = this.maxQuantity;
					}
				}
				if (curValue < this.stepQuantity)
				{
					curValue = this.stepQuantity;
				}
				else
				{
					count = curValue/this.stepQuantity;
					intCount = parseInt(count, 10);
					if (isNaN(intCount))
					{
						intCount = 1;
						count = 1.1;
					}
					if (count > intCount)
					{
						curValue = (intCount <= 1 ? this.stepQuantity : intCount*this.stepQuantity);
						curValue = Math.round(curValue*this.precisionFactor)/this.precisionFactor;
					}
				}
				this.obQuantity.value = curValue;
			}
			else
			{
				this.obQuantity.value = this.stepQuantity;
			}
		}
		else
		{
			this.obQuantity.value = this.stepQuantity;
		}
	}
};

window.JCCatalogSection.prototype.QuantitySet = function(index)
{
	if (0 === this.errorCode)
	{
		this.canBuy = this.offers[index].CAN_BUY;
		if (this.canBuy)
		{
			if (!!this.obBasketActions)
			{
				BX.style(this.obBasketActions, 'display', '');
			}
			if (!!this.obNotAvail)
			{
				BX.style(this.obNotAvail, 'display', 'none');
			}
		}
		else
		{
			if (!!this.obBasketActions)
			{
				//BX.style(this.obBasketActions, 'display', 'none');
				BX.style(this.obBasketActions, 'opacity', '0');
				BX.style(BX.findParent(BX(this.obQuantity), { 'class':'counter_block' }), 'display', 'none');
			}
			if (!!this.obNotAvail)
			{
				BX.style(this.obNotAvail, 'display', '');
			}
		}
		if (this.showQuantity)
		{
			this.isDblQuantity = this.offers[index].QUANTITY_FLOAT;
			this.checkQuantity = this.offers[index].CHECK_QUANTITY;
			if (this.isDblQuantity)
			{
				this.maxQuantity = parseFloat(this.offers[index].MAX_QUANTITY);
				this.stepQuantity = Math.round(parseFloat(this.offers[index].STEP_QUANTITY)*this.precisionFactor)/this.precisionFactor;
			}
			else
			{
				this.maxQuantity = parseInt(this.offers[index].MAX_QUANTITY, 10);
				this.stepQuantity = parseInt(this.offers[index].STEP_QUANTITY, 10);
			}
			if(!!this.obQuantity){
				this.obQuantity.value = this.defaultCount;
				this.obQuantity.disabled = !this.canBuy;
			}
			if (!!this.obMeasure)
			{
				if (!!this.offers[index].MEASURE)
				{
					BX.adjust(this.obMeasure, { html : this.offers[index].MEASURE});
				}
				else
				{
					BX.adjust(this.obMeasure, { html : ''});
				}
			}
		}
		this.currentBasisPrice = this.offers[index].BASIS_PRICE;
	}
};

window.JCCatalogSection.prototype.SelectOfferProp = function()
{
	var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		RowItems = null,
		target = BX.proxy_context;
	if(typeof target.options !== 'undefined' && typeof target.options[target.selectedIndex] !== 'undefined')
		target = target.options[target.selectedIndex];
	if (!!target && target.hasAttribute('data-treevalue'))
	{
		strTreeValue = target.getAttribute('data-treevalue');
		propModes = target.getAttribute('data-showtype');
		arTreeItem = strTreeValue.split('_');
		if (this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1]))
		{
			RowItems = BX.findChildren(target.parentNode, {tagName: this.skuVisualParams[propModes.toUpperCase()].TAG}, false);

			if (!!RowItems && 0 < RowItems.length)
			{
				for (i = 0; i < RowItems.length; i++)
				{
					value = RowItems[i].getAttribute('data-onevalue');

					// for SELECTBOXES
					if(propModes == 'TEXT'){
						if (value === arTreeItem[1]){
							RowItems[i].setAttribute('selected', 'selected');
						}else{
							RowItems[i].removeAttribute('selected');
						}
					}else{
						if (value === arTreeItem[1]){
							$(RowItems[i]).addClass(this.skuVisualParams[propModes.toUpperCase()].ACTIVE_CLASS);
						}else{
							$(RowItems[i]).removeClass(this.skuVisualParams[propModes.toUpperCase()].ACTIVE_CLASS);
						}
					}
				}
			}
		}
	}
};

window.JCCatalogSection.prototype.SearchOfferPropIndex = function(strPropID, strPropValue)
{
	var strName = '',
		arShowValues = false,
		i, j,
		arCanBuyValues = [],
		index = -1,
		arFilter = {},
		tmpFilter = [];

	for (i = 0; i < this.treeProps.length; i++)
	{
		if (this.treeProps[i].ID === strPropID)
		{
			index = i;
			break;
		}
	}

	if (-1 < index)
	{
		for (i = 0; i < index; i++)
		{
			strName = 'PROP_'+this.treeProps[i].ID;
			arFilter[strName] = this.selectedValues[strName];
		}
		strName = 'PROP_'+this.treeProps[index].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
		{
			return false;
		}
		if (!BX.util.in_array(strPropValue, arShowValues))
		{
			return false;
		}
		arFilter[strName] = strPropValue;
		for (i = index+1; i < this.treeProps.length; i++)
		{
			strName = 'PROP_'+this.treeProps[i].ID;
			arShowValues = this.GetRowValues(arFilter, strName);
			if (!arShowValues)
			{
				return false;
			}
			if (this.showAbsent)
			{
				arCanBuyValues = [];
				tmpFilter = [];
				tmpFilter = BX.clone(arFilter, true);
				for (j = 0; j < arShowValues.length; j++)
				{
					tmpFilter[strName] = arShowValues[j];
					/*if (this.GetCanBuy(tmpFilter))
					{*/
						arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
					//}
				}
			}
			else
			{
				arCanBuyValues = arShowValues;
			}
			if (!!this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues))
			{
				arFilter[strName] = this.selectedValues[strName];
			}
			else
			{
				arFilter[strName] = arCanBuyValues[0];
			}
			this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
		}
		this.selectedValues = arFilter;
		this.ChangeInfo();
	}
	return true;
};

window.JCCatalogSection.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID)
{
	var i = 0,
		showI = 0,
		value = '',
		countShow = 0,
		strNewLen = '',
		obData = {},
		pictMode = false,
		extShowMode = false,
		isCurrent = false,
		selectIndex = 0,
		obLeft = this.treeEnableArrow,
		obRight = this.treeEnableArrow,
		currentShowStart = 0,
		RowItems = null;

	if (-1 < intNumber && intNumber < this.obTreeRows.length){
		propMode = this.treeProps[intNumber].DISPLAY_TYPE;
		RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: this.skuVisualParams[propMode].TAG}, false);
		if (!!RowItems && 0 < RowItems.length){
			selectMode = ('SELECT' === this.treeProps[intNumber].DISPLAY_TYPE);
			countShow = showID.length;
			obData = {
				style: {},
				props: {
					disabled: '',
					selected: '',
				},
			};

			for (i = 0; i < RowItems.length; i++){
				value = RowItems[i].getAttribute('data-onevalue');
				isCurrent = (value === activeID && value !=0);
				/*if (BX.util.in_array(value, canBuyID)){
					obData.props.className = (isCurrent ? this.skuVisualParams[propMode].ACTIVE_CLASS : '');
				}else{
					obData.props.className = (isCurrent ? this.skuVisualParams[propMode].ACTIVE_CLASS+' '+this.skuVisualParams[propMode].HIDE_CLASS : this.skuVisualParams[propMode].HIDE_CLASS);
				}*/
				obData.props.className = (isCurrent ? this.skuVisualParams[propMode].ACTIVE_CLASS : '');

				if(selectMode){
					obData.props.disabled = 'disabled';
					obData.props.selected = (isCurrent ? 'selected' : '');
				}else{
					obData.style.display = 'none';
				}

				if (BX.util.in_array(value, showID)){
					if(selectMode){
						obData.props.disabled = '';
					}else{
						obData.style.display = '';
					}
					if (isCurrent){
						selectIndex = showI;
					}
					showI++;
				}
				BX.adjust(RowItems[i], obData);
			}

			if(selectMode){
				if($(this.obTreeRows[intNumber].LIST).parent().hasClass('ik_select'))
					$(this.obTreeRows[intNumber].LIST).ikSelect('reset');
			}

			this.showCount[intNumber] = countShow;
			this.showStart[intNumber] = currentShowStart;
		}
	}
};

window.JCCatalogSection.prototype.GetRowValues = function(arFilter, index)
{
	var i = 0,
		j,
		arValues = [],
		boolSearch = false,
		boolOneSearch = true;

	if (0 === arFilter.length)
	{
		for (i = 0; i < this.offers.length; i++)
		{
			if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
			{
				arValues[arValues.length] = this.offers[i].TREE[index];
			}
		}
		boolSearch = true;
	}
	else
	{
		for (i = 0; i < this.offers.length; i++)
		{
			boolOneSearch = true;
			for (j in arFilter)
			{
				if (arFilter[j] !== this.offers[i].TREE[j])
				{
					boolOneSearch = false;
					break;
				}
			}
			if (boolOneSearch)
			{
				if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
				{
					arValues[arValues.length] = this.offers[i].TREE[index];
				}
				boolSearch = true;
			}
		}
	}
	return (boolSearch ? arValues : false);
};

window.JCCatalogSection.prototype.GetCanBuy = function(arFilter)
{
	var i = 0,
		j,
		boolSearch = false,
		boolOneSearch = true;

	for (i = 0; i < this.offers.length; i++)
	{
		boolOneSearch = true;
		for (j in arFilter)
		{
			if (arFilter[j] !== this.offers[i].TREE[j])
			{
				boolOneSearch = false;
				break;
			}
		}
		if (boolOneSearch)
		{
			if (this.offers[i].CAN_BUY)
			{
				boolSearch = true;
				break;
			}
		}
	}
	return boolSearch;
};

window.JCCatalogSection.prototype.SetCurrent = function()
{
	var i = 0,
		j = 0,
		arCanBuyValues = [],
		strName = '',
		arShowValues = false,
		arFilter = {},
		tmpFilter = [],
		current = this.offers[this.offerNum].TREE;
	for (i = 0; i < this.treeProps.length; i++)
	{
		strName = 'PROP_'+this.treeProps[i].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
		{
			break;
		}
		if (BX.util.in_array(current[strName], arShowValues))
		{
			arFilter[strName] = current[strName];
		}
		else
		{
			arFilter[strName] = arShowValues[0];
			this.offerNum = 0;
		}
		if (this.showAbsent)
		{
			arCanBuyValues = [];
			tmpFilter = [];
			tmpFilter = BX.clone(arFilter, true);
			for (j = 0; j < arShowValues.length; j++)
			{
				tmpFilter[strName] = arShowValues[j];
				if (this.GetCanBuy(tmpFilter))
				{
					arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
		}
		else
		{
			arCanBuyValues = arShowValues;
		}
		this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
	}
	this.selectedValues = arFilter;
	this.ChangeInfo();
};

window.JCCatalogSection.prototype.ChangeInfo = function()
{
	var i = 0,
		j,
		index = -1,
		compareParams,
		boolOneSearch = true;

	for (i = 0; i < this.offers.length; i++)
	{
		boolOneSearch = true;
		for (j in this.selectedValues)
		{
			if (this.selectedValues[j] !== this.offers[i].TREE[j])
			{
				boolOneSearch = false;
				break;
			}
		}
		if (boolOneSearch)
		{
			index = i;
			break;
		}
	}
	if (-1 < index){
		if (!!this.obPict){
			var obData = {
				attrs: {}
			};

			if (!!this.offers[index].PREVIEW_PICTURE){
				obData.attrs.src = this.offers[index].PREVIEW_PICTURE.SRC;
			}else{
				obData.attrs.src = this.defaultPict.pict.SRC;
			}
			BX.adjust(BX.findChild(this.obPict, {"tag": "img"}), obData);
		}
		if (this.secondPict && !!this.obSecondPict)
		{
			if (!!this.offers[index].PREVIEW_PICTURE_SECOND)
			{
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.offers[index].PREVIEW_PICTURE_SECOND.SRC+')'}});
			}
			else if (!!this.offers[index].PREVIEW_PICTURE.SRC)
			{
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.offers[index].PREVIEW_PICTURE.SRC+')'}});
			}
			else if (!!this.defaultPict.secondPict)
			{
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.defaultPict.secondPict.SRC+')'}});
			}
			else
			{
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.defaultPict.pict.SRC+')'}});
			}
		}
		if (this.showSkuProps && !!this.obSkuProps)
		{
			if (0 === this.offers[index].DISPLAY_PROPERTIES.length)
			{
				BX.adjust(this.obSkuProps, {style: {display: 'none'}, html: ''});
			}
			else
			{
				BX.adjust(this.obSkuProps, {style: {display: ''}, html: this.offers[index].DISPLAY_PROPERTIES});
			}
		}


		$(this.obBuyBtn).data("item", this.offers[index].ID);
		$(this.obBasketBtn).data("item", this.offers[index].ID);

		$(this.obBasketActions).closest('.counter_wrapp').css('opacity', 0);

		var measure = this.offers[index].MEASURE && this.offers[index].SHOW_MEASURE=="Y" ? this.offers[index].MEASURE : '';
		this.setPrice(this.offers[index].PRICE, measure);
		this.setQuantityStore(this.offers[index].MAX_QUANTITY, this.offers[index].AVAILIABLE.TEXT);
		this.offerNum = index;
		this.QuantitySet(this.offerNum);

		$(this.obProduct).find('.item-title a').attr('href', this.offers[this.offerNum].URL);
		$(this.obProduct).find('.image_wrapper_block > a').attr('href', this.offers[this.offerNum].URL);

		/*quantity discount start*/
		if($(this.obProduct).find('.quantity_block .values').length){
			$(this.obProduct).find('.quantity_block .values .item span.value').text(this.offers[index].MAX_QUANTITY).css({'opacity':'1'});
		}
		/*quantity discount end*/

		var obj=this.offers[index],
			th=$(this.obProduct);

		if(typeof arBasketAspro !=="undefined"){
			this.setActualDataBlock(th, obj);
		}else{
			if(typeof window.frameCacheVars !== "undefined"){
				BX.addCustomEvent("onFrameDataReceived", function(){
					window.JCCatalogSection.prototype.setActualDataBlock(th, obj);
				});
			}
		}
	}
};

/*set blocks start*/
window.JCCatalogSection.prototype.setActualDataBlock = function(th, obj)
{
	/*like block start*/
	this.setLikeBlock(th, '.like_icons .wish_item_button', obj, 'DELAY');
	this.setLikeBlock(th, '.like_icons .compare_item_button',obj, 'COMPARE');
	/*like block end*/

	/*buy block start*/
	this.setBuyBlock(th, obj);
	/*buy block end*/
}
/*set blocks end*/

/*set compare/wish*/
window.JCCatalogSection.prototype.setLikeBlock = function(th, className, obj, type)
{
	var block=th;
	if(type=="DELAY"){
		if(obj.CAN_BUY){
			block.find(className).css('display','block');
		}else{
			block.find(className).hide();
		}
	}

	block.find(className).attr('data-item', obj.ID);
	block.find(className).find('span').attr('data-item', obj.ID);
	if(arBasketAspro[type]){
		block.find(className).find('.to').removeClass('added').css('display','block');
		block.find(className).find('.in').hide();

		if(arBasketAspro[type][obj.ID]!==undefined){
			block.find(className).find('.to').hide();
			block.find(className).find('.in').css('display','block');
		}
	}
}

/*set buy*/
window.JCCatalogSection.prototype.setBuyBlock = function(th, obj)
{
	var buyBlock=th.find('.offer_buy_block');

	if(buyBlock.find('.counter_wrapp .counter_block').length){
		buyBlock.find('.counter_wrapp .counter_block').attr('data-item', obj.ID);
	}
	if((obj.CONFIG.OPTIONS.USE_PRODUCT_QUANTITY_DETAIL && obj.CONFIG.ACTION == "ADD") && obj.CAN_BUY){
		var max=(obj.CONFIG.MAX_QUANTITY_BUY>0 ? "data-max='"+obj.CONFIG.MAX_QUANTITY_BUY+"'" : ""),
			counterHtml='<span class="minus">-</span>'+
				'<input type="text" class="text" name="'+obj.PRODUCT_QUANTITY_VARIABLE+'" value="'+obj.CONFIG.MIN_QUANTITY_BUY+'" />'+
				'<span class="plus" '+max+'>+</span>';
		if(arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID]!==undefined){
			if(buyBlock.find('.counter_wrapp .counter_block').length){
				buyBlock.find('.counter_wrapp .counter_block').hide();
			}else{
				buyBlock.find('.counter_wrapp').prepend('<div class="counter_block" data-item="'+obj.ID+'"></div>');
				buyBlock.find('.counter_wrapp .counter_block').html(counterHtml).hide();
			}
		}else{
			if(buyBlock.find('.counter_wrapp .counter_block').length){
				buyBlock.find('.counter_wrapp .counter_block').html(counterHtml).show();
			}else{
				buyBlock.find('.counter_wrapp').prepend('<div class="counter_block" data-item="'+obj.ID+'"></div>');
				buyBlock.find('.counter_wrapp .counter_block').html(counterHtml);
			}
		}
	}else{
		if(buyBlock.find('.counter_wrapp .counter_block').length){
			buyBlock.find('.counter_wrapp .counter_block').hide();
		}
	}
	var className=((obj.CONFIG.ACTION == "ORDER") || !obj.CAN_BUY || !obj.CONFIG.OPTIONS.USE_PRODUCT_QUANTITY_DETAIL || (obj.CONFIG.ACTION == "SUBSCRIBE" && obj.CATALOG_SUBSCRIBE == "Y") ? "wide" : "" ),
		buyBlockBtn=$('<div class="button_block"></div>');

	if(buyBlock.find('.counter_wrapp').find('.button_block').length){
		if(arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID]!==undefined){
			buyBlock.find('.counter_wrapp').find('.button_block').addClass('wide').html(obj.HTML);
			markProductAddBasket(obj.ID);
		}else{
			if(className){
				buyBlock.find('.counter_wrapp').find('.button_block').addClass('wide').html(obj.HTML);
				if(arBasketAspro["SUBSCRIBE"] && arBasketAspro["SUBSCRIBE"][obj.ID]!==undefined){
					markProductSubscribe(obj.ID);
				}
			}else{
				buyBlock.find('.counter_wrapp').find('.button_block').removeClass('wide').html(obj.HTML);
			}
		}
	}else{
		buyBlock.find('.counter_wrapp').append('<div class="button_block '+className+'">'+obj.HTML+'</div>');
		if(arBasketAspro["BASKET"] && arBasketAspro["BASKET"][obj.ID]!==undefined)
			markProductAddBasket(obj.ID);
		if(arBasketAspro["SUBSCRIBE"] && arBasketAspro["SUBSCRIBE"][obj.ID]!==undefined)
			markProductSubscribe(obj.ID);
	}

	if(obj.CONFIG.ACTION !== "NOTHING"){
		if(obj.CONFIG.ACTION == "ADD" && obj.CAN_BUY && obj.SHOW_ONE_CLICK_BUY!="N"){
			var ocb='<span class="transparent big_btn type_block button one_click" data-offers="Y" data-item="'+obj.ID+'" data-iblockID="'+obj.IBLOCK_ID+'" data-quantity="'+obj.CONFIG.MIN_QUANTITY_BUY+'" data-props="'+obj.OFFER_PROPS+'" onclick="oneClickBuy('+obj.ID+', '+obj.IBLOCK_ID+', this)">'+
				'<span>'+obj.ONE_CLICK_BUY+'</span>'+
				'</span>';
			if(buyBlock.find('.wrapp_one_click').length){
				buyBlock.find('.wrapp_one_click').html(ocb);
			}else{
				buyBlock.append('<div class="wrapp_one_click">'+ocb+'</div>');
			}
		}else{
			if(buyBlock.find('.wrapp_one_click').length){
				buyBlock.find('.wrapp_one_click').remove();
			}
		}
	}else{
		if(buyBlock.find('.wrapp_one_click').length){
			buyBlock.find('.wrapp_one_click').remove();
		}
	}

	buyBlock.fadeIn();
}

/*get compare sku*/
window.JCCatalogSection.prototype.CompareCountResult = function(result)
{
	if(result.COMPARE_COUNT){
		for(var i in result.ITEMS){
			if(result.ITEMS[i]==this.offers[this.offerNum].ID){
				this.offers[this.offerNum].COMPARE_ACTIVE=true;
				break;
			}else{
				this.offers[this.offerNum].COMPARE_ACTIVE=false;
			}
		}
		if(this.offers[this.offerNum].COMPARE_ACTIVE){
			$(this.obCompare).find('.compare_item.to').hide();
			$(this.obCompare).find('.compare_item.added').show();
		}else{
			$(this.obCompare).find('.compare_item.added').hide();
			$(this.obCompare).find('.compare_item.to').show();
		}
	}
}

/*get item info*/
window.JCCatalogSection.prototype.ItemInfoResult = function(result)
{
	if(result.HTML){
		$(this.obBasketActions).html(result.HTML);
		$(this.obBasketActions).show();
		this.obBuyBtn = BX(this.visual.BUY_ID);
		this.obBasketBtn = BX(this.visual.BASKET_LINK);
		this.obSubscribeBtn = BX(this.visual.SUBSCRIBE_ID);
		this.obSubscribedBtn = BX(this.visual.SUBSCRIBED_ID);
		BX.bind(this.obBuyBtn, 'click', BX.delegate(this.Add2Basket, this));
		$(this.obBasketActions).removeClass('wide');
		this.ajax_type_item=result.BUYMISSINGGOODS;
		if(result.BUYMISSINGGOODS!="ADD" && !this.canBuy){
			$(this.obBasketActions).addClass('wide');
		}else{
			$(this.obQuantity).css('display','');
		}
	}
	//if(this.canBuy){
		basketParams = {
			ajax_action: 'Y'
		};
		BX.ajax.loadJSON(
			arOptimusOptions['SITE_DIR']+'ajax/get_basket_count.php',
			basketParams,
			BX.delegate(this.BasketCountResult, this)
		);
	//}
}

/*get basket items*/
window.JCCatalogSection.prototype.BasketCountResult = function(result)
{
	//if(result.TOTAL_COUNT){
		for(var i in result.ITEMS){
			if(result.ITEMS[i].PRODUCT_ID==this.offers[this.offerNum].ID){
				this.offers[this.offerNum].BASKET_ACTIVE=true;
				break;
			}else{
				this.offers[this.offerNum].BASKET_ACTIVE=false;
			}
		}
		for(var i in result.SUBSCRIBE_ITEMS){
			if(result.SUBSCRIBE_ITEMS[i].PRODUCT_ID==this.offers[this.offerNum].ID){
				this.offers[this.offerNum].SUBSCRIBE_ACTIVE=true;
				break;
			}else{
				this.offers[this.offerNum].SUBSCRIBE_ACTIVE=false;
			}
		}

		this.BasketStateRefresh();
	//}
}

window.JCCatalogSection.prototype.BasketStateRefresh = function(buy_basket)
{
	/*if(this.offers[this.offerNum].SUBSCRIBE_ACTIVE){
		$(this.obBasketActions).addClass('wide');
		$(this.obSubscribeBtn).hide();
		$(this.obSubscribedBtn).show();
	}else{
		$(this.obBasketActions).addClass('wide');
		$(this.obSubscribedBtn).hide();
		$(this.obSubscribeBtn).show();
	}
	if(this.offers[this.offerNum].BASKET_ACTIVE){
		$(this.obBuyBtn).hide();
		$(this.obBasketBtn).show();
		$(this.obQuantity).closest('.counter_wrapp').find('.counter_block').hide();
		$(this.obBasketActions).addClass('wide');
	}else{
		$(this.obBasketActions).removeClass('wide');
		$(this.obBasketBtn).hide();
		$(this.obBuyBtn).show();
		if(this.ajax_type_item=="ADD" || this.canBuy)
			$(this.obQuantity).closest('.counter_wrapp').find('.counter_block').show();

	}
	if(!this.canBuy){
		$(this.obBasketActions).addClass('wide');
	}*/
	if(this.offers[this.offerNum].SUBSCRIBE_ACTIVE){
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .to-subscribe').hide();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .in-subscribe').show();
	}else{
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .to-subscribe').show();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .in-subscribe').hide();
	}
	if(this.offers[this.offerNum].BASKET_ACTIVE){
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID).addClass('wide');
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .button_block').addClass('wide');
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .to-cart').hide();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .in-cart').show();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .counter_block').hide();
	}else{
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID).removeClass('wide');
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .button_block').removeClass('wide');
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .to-cart').show();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .in-cart').hide();
		$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID+' .counter_block').show();

	}
	BX.style(this.obBasketActions, 'opacity', '1');
	$(this.obBasketActions).closest('.counter_wrapp').css('opacity', 1);
	if(buy_basket!== 'undefined' && buy_basket=="Y"){
		if($("#basket_line .basket_fly").length && $(window).outerWidth()>768)
		{
			// preAnimateBasketFly($("#basket_line .basket_fly"), 200, 333);
			basketFly('open');
		}
		else if($("#basket_line .cart").length)
		{
			if($("#basket_line .cart").is(".empty_cart"))
			{
				$("#basket_line .cart").removeClass("empty_cart").find(".cart_wrapp a.basket_link").removeAttr("href").addClass("cart-call");
				touchBasket('.cart:not(.empty_cart) .basket_block .link');
			}
			reloadTopBasket('add', $('#basket_line'), 200, 2000, 'Y');
			/*if($(window).outerWidth() > 520){
				//if(arOptimusOptions['THEME']['SHOW_BASKET_ONADDTOCART'] !== 'N'){
					preAnimateBasketPopup('', $('.card_popup_frame'), 0, 200);
				//}
			};*/
		}
		animateBasketLine(200);
	}
}

window.JCCatalogSection.prototype.setPrice = function(price, measure)
{
	var strPrice,
		obData;

	if (!!this.obPrice){
		strPrice = BX.Currency.currencyFormat(price.DISCOUNT_VALUE, price.CURRENCY, true);
		if(measure){
			strPrice=(strPrice ? strPrice +'/'+measure : "&nbsp;");
		}
		BX.adjust(this.obPrice, {html: strPrice});
		if (price.DISCOUNT_VALUE !== price.VALUE && this.showOldPrice)
		{
			$(this.obPriceOld).find('strike').show().html(BX.Currency.currencyFormat(price.VALUE, price.CURRENCY, true));
			if(this.showPercent){
				$(this.obPriceOld).closest('.cost').find('.sale_block').show();
				$(this.obPriceOld).closest('.cost').find('.value').html(price.DISCOUNT_DIFF_PERCENT_RAW);
				$(this.obPriceOld).closest('.cost').find('.text span').html(BX.Currency.currencyFormat(price.DISCOUNT_DIFF, price.CURRENCY, true));
			}
		}
		else{
			$(this.obPriceOld).find('strike').hide().html('');
			if(this.showPercent){
				$(this.obPriceOld).closest('.cost').find('.sale_block').hide();
			}
		}
	}
};

/*set store quantity*/
window.JCCatalogSection.prototype.setQuantityStore = function(quantity, text)
{
	if(parseFloat(quantity)>0){
		$(this.storeQuanity).find('.icon').removeClass('order').addClass('stock');
	}else{
		$(this.storeQuanity).find('.icon').removeClass('stock').addClass('order');
	}
	$(this.storeQuanity).find('.icon + span').html(text);
}

window.JCCatalogSection.prototype.Compare = function()
{
	var compareParams, compareLink;
	$(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).data('changed','Y');
	// if($(this.obCompare).find('.compare_item.added').is(':visible')){
	if($(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).find('.compare_item.added').is(':visible')){
		compareLink = this.compareData.compareUrlDel;
		this.compareData.Added = false;
	}else{
		compareLink = this.compareData.compareUrl;
		this.compareData.Added = true;
	}
	if (!!compareLink){
		switch (this.productType){
			case 1://product
			case 2://set
				compareLink = compareLink.replace('#ID#', this.product.id.toString());
				break;
			case 3://sku
				compareLink = compareLink.replace('#ID#', this.offers[this.offerNum].ID);
				break;
		}
		compareParams = {
			ajax_action: 'Y'
		};
		BX.ajax.loadJSON(
			compareLink,
			compareParams,
			BX.proxy(this.CompareResult, this)
		);
	}
};

window.JCCatalogSection.prototype.CompareResult = function(result)
{
	var popupContent, popupButtons, popupTitle;

	if (typeof result !== 'object'){
		return false;
	}

	if (result.STATUS === 'OK'){
		BX.onCustomEvent('OnCompareChange');
		/*if(!this.compareData.Added){
			$(this.obCompare).find('.in').hide();
			$(this.obCompare).find('.to').show();
		}
		else{
			$(this.obCompare).find('.to').hide();
			$(this.obCompare).find('.in').show();
		}*/
		if(!this.compareData.Added){
			$(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).find('.in').hide();
			$(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).find('.to').show();
		}
		else{
			$(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).find('.to').hide();
			$(this.obProduct).find('.like_icons .o_'+this.offers[this.offerNum].ID).find('.in').show();
		}
		jsAjaxUtil.InsertDataToNode(arOptimusOptions['SITE_DIR'] + 'ajax/show_compare_preview_top.php', 'compare_line', false);
	}else{
		console.log(BX.message('ADD_ERROR_COMPARE'));
	}
	return false;
};

window.JCCatalogSection.prototype.CompareRedirect = function()
{
	if (!!this.compareData.comparePath){
		location.href = this.compareData.comparePath;
	}else{
		this.obPopupWin.close();
	}
};

window.JCCatalogSection.prototype.InitBasketUrl = function()
{
	this.basketUrl = (this.basketMode === 'ADD' ? this.basketData.add_url : this.basketData.buy_url);
	switch (this.productType)
	{
		case 1://product
		case 2://set
			this.basketUrl = this.basketUrl.replace('#ID#', this.product.id.toString());
			break;
		case 3://sku
			this.basketUrl = this.basketUrl.replace('#ID#', this.offers[this.offerNum].ID);
			break;
	}
	this.basketParams = {
		'ajax_basket': 'Y'
	};
	if (this.showQuantity)
	{
		// this.basketParams[this.basketData.quantity] = this.obQuantity.value;
		this.basketParams[this.basketData.quantity] = $(this.obProduct).find('.counter_wrapp').find('.counter_block .text').val();
	}
	if (!!this.basketData.sku_props)
	{
		this.basketParams[this.basketData.sku_props_var] = this.basketData.sku_props;
	}
};

window.JCCatalogSection.prototype.FillBasketProps = function()
{
	if (!this.visual.BASKET_PROP_DIV)
	{
		return;
	}
	var
		i = 0,
		propCollection = null,
		foundValues = false,
		obBasketProps = null;

	if (this.basketData.useProps && !this.basketData.emptyProps)
	{
		if (!!this.obPopupWin && !!this.obPopupWin.contentContainer)
		{
			obBasketProps = this.obPopupWin.contentContainer;
		}
	}
	else
	{
		obBasketProps = BX(this.visual.BASKET_PROP_DIV);
	}
	if (!!obBasketProps)
	{
		propCollection = obBasketProps.getElementsByTagName('select');
		if (!!propCollection && !!propCollection.length)
		{
			for (i = 0; i < propCollection.length; i++)
			{
				if (!propCollection[i].disabled)
				{
					switch(propCollection[i].type.toLowerCase())
					{
						case 'select-one':
							this.basketParams[propCollection[i].name] = propCollection[i].value;
							foundValues = true;
							break;
						default:
							break;
					}
				}
			}
		}
		propCollection = obBasketProps.getElementsByTagName('input');
		if (!!propCollection && !!propCollection.length)
		{
			for (i = 0; i < propCollection.length; i++)
			{
				if (!propCollection[i].disabled)
				{
					switch(propCollection[i].type.toLowerCase())
					{
						case 'hidden':
							this.basketParams[propCollection[i].name] = propCollection[i].value;
							foundValues = true;
							break;
						case 'radio':
							if (propCollection[i].checked)
							{
								this.basketParams[propCollection[i].name] = propCollection[i].value;
								foundValues = true;
							}
							break;
						default:
							break;
					}
				}
			}
		}
	}
	if (!foundValues)
	{
		this.basketParams[this.basketData.props] = [];
		this.basketParams[this.basketData.props][0] = 0;
	}
};

window.JCCatalogSection.prototype.Add2Basket = function()
{
	this.basketMode = 'ADD';
	this.Basket();
};

window.JCCatalogSection.prototype.BuyBasket = function()
{
	this.basketMode = 'BUY';
	this.Basket();
};

window.JCCatalogSection.prototype.SendToBasket = function()
{
	if (!this.canBuy){
		return;
	}

	this.InitBasketUrl();
	this.FillBasketProps();

	BX.ajax.loadJSON(
		this.basketUrl,
		this.basketParams,
		BX.delegate(this.BasketResult, this)
	);
};

window.JCCatalogSection.prototype.Basket = function()
{
	var contentBasketProps = '';
	if (!this.canBuy){
		return;
	}

	this.SendToBasket();
};

window.JCCatalogSection.prototype.BasketResult = function(arResult)
{
	var strContent = '',
		strPict = '',
		successful,
		buttons = [];

	if (!!this.obPopupWin){
		this.obPopupWin.close();
	}
	if ('object' !== typeof arResult){
		return false;
	}
	successful = (arResult.STATUS === 'OK');
	if (successful && this.basketAction === 'BUY'){
		this.BasketRedirect();
	}else{
		if (successful){
			getActualBasket();
			markProductAddBasket(this.offers[this.offerNum].ID);
			BX.onCustomEvent('OnBasketChange');
			this.offers[this.offerNum].BASKET_ACTIVE=true;
			this.BasketStateRefresh("Y");
			$(this.obProduct).find('.hover_block .o_'+this.offers[this.offerNum].ID).data('changed','Y');

		}else{
			console.log(BX.message('ADD_ERROR_BASKET'));
		}
	}
};

window.JCCatalogSection.prototype.BasketRedirect = function()
{
	location.href = (!!this.basketData.basketUrl ? this.basketData.basketUrl : BX.message('BASKET_URL'));
};

})(window);