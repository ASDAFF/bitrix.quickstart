JCCatalogSection = function (arParams)
{
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
		DISPLAY_PROP_DIV: ''
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
		id: 0
	};
	
	this.defaultPict = {
		pict: null,
		secondPict: null
	};
	
	this.ajaxPath = '';

	this.checkQuantity = false;
	this.maxQuantity = 0;
	this.stepQuantity = 1;
	this.isDblQuantity = false;
	this.canBuy = true;
	this.canSubscription = true;

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
	
	this.errorCode = 0;
	
	if ('object' == typeof(arParams))
	{
		this.productType = parseInt(arParams.PRODUCT_TYPE);
		this.showQuantity = arParams.SHOW_QUANTITY;
		this.showAbsent = arParams.SHOW_ABSENT;
		if (!!arParams.SECOND_PICT)
			this.secondPict = true;
		if (!!arParams.SHOW_OLD_PRICE)
			this.showOldPrice = true;
		if (!!arParams.SHOW_DISCOUNT_PERCENT)
			this.showPercent = true;
		if (!!arParams.SHOW_SKU_PROPS)
			this.showSkuProps = true;
		this.visual = arParams.VISUAL;
		this.ajaxPath = arParams.AJAX_PATH;
		switch (this.productType)
		{
			case 1://product
			case 2://set
				if (!!arParams.PRODUCT && 'object' == typeof(arParams.PRODUCT))
				{
					if (this.showQuantity)
					{
						this.product.checkQuantity = arParams.PRODUCT.CHECK_QUANTITY;
						this.product.isDblQuantity = arParams.PRODUCT.QUANTITY_FLOAT;
						if (this.product.checkQuantity)
							this.product.maxQuantity = (this.product.isDblQuantity
									? parseFloat(arParams.PRODUCT.MAX_QUANTITY)
									: parseInt(arParams.PRODUCT.MAX_QUANTITY)
							);
						this.product.stepQuantity = (this.product.isDblQuantity
							? parseFloat(arParams.PRODUCT.STEP_QUANTITY)
							: parseInt(arParams.PRODUCT.STEP_QUANTITY)
						);
						
						this.checkQuantity = this.product.checkQuantity;
						this.isDblQuantity = this.product.isDblQuantity;
						this.maxQuantity = this.product.maxQuantity;
						this.stepQuantity = this.product.stepQuantity;
					}
					this.product.canBuy = arParams.PRODUCT.CAN_BUY;
					this.product.canSubscription = arParams.PRODUCT.SUBSCRIPTION;
					
					this.canBuy = this.product.canBuy;
					this.canSubscription = this.product.canSubscription;
					
					this.product.name = arParams.PRODUCT.NAME;
					this.product.pict = arParams.PRODUCT.PICT;
					this.product.id = arParams.PRODUCT.ID;
				}
				else
				{
					this.errorCode = -1;
				}
				break;
			case 3://sku
				if (!!arParams.OFFERS && BX.type.isArray(arParams.OFFERS))
				{
					this.offers = arParams.OFFERS;
					this.offerNum = 0;
					if (!!arParams.OFFER_SELECTED)
						this.offerNum = parseInt(arParams.OFFER_SELECTED);
					if (isNaN(this.offerNum))
						this.offerNum = 0;
					if (!!arParams.TREE_PROPS)
						this.treeProps = arParams.TREE_PROPS;
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
	}
	if (0 == this.errorCode)
	{
		BX.ready(BX.delegate(this.Init,this));
	}
};

JCCatalogSection.prototype.Init = function()
{
	var i = 0;
	this.obProduct = BX(this.visual.ID);
	if (!this.obProduct)
		this.errorCode = -1;
	this.obPict = BX(this.visual.PICT_ID);
	if (!this.obPict)
		this.errorCode = -2;
	if (this.secondPict && !!this.visual.SECOND_PICT_ID)
	{
		this.obSecondPict = BX(this.visual.SECOND_PICT_ID);
	}
	this.obPrice = BX(this.visual.PRICE_ID);
	if (!this.obPrice)
		this.errorCode = -16;
	if (this.showQuantity && !!this.visual.QUANTITY_ID)
	{
		this.obQuantity = BX(this.visual.QUANTITY_ID);
		if (!this.obQuantity)
			this.errorCode = -32;
		if (!!this.visual.QUANTITY_UP_ID)
		{
			this.obQuantityUp = BX(this.visual.QUANTITY_UP_ID);
			if (!this.obQuantityUp)
				this.errorCode = -64;
		}
		if (!!this.visual.QUANTITY_DOWN_ID)
		{
			this.obQuantityDown = BX(this.visual.QUANTITY_DOWN_ID);
			if (!this.obQuantityDown)
				this.errorCode = -128;
		}
	}
	if (3 == this.productType)
	{
		if (!!this.visual.TREE_ID)
		{
			this.obTree = BX(this.visual.TREE_ID);
			if (!this.obTree)
				this.errorCode = -256;
			var strPrefix = this.visual.TREE_ITEM_ID;
			for (i = 0; i < this.treeProps.length; i++)
			{
				this.obTreeRows[i] = {
					LEFT: BX(strPrefix+this.treeProps[i].ID+'_left'),
					RIGHT: BX(strPrefix+this.treeProps[i].ID+'_right'),
					LIST: BX(strPrefix+this.treeProps[i].ID+'_list'),
					CONT: BX(strPrefix+this.treeProps[i].ID+'_cont')
				};
				if (!this.obTreeRows[i].LEFT || !this.obTreeRows[i].RIGHT || !this.obTreeRows[i].LIST || !this.obTreeRows[i].CONT)
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
	if (!!this.visual.BUY_ID)
	{
		this.obBuyBtn = BX(this.visual.BUY_ID);
		if (!this.obBuyBtn)
		{
			
		}
	}
	
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
	
	if (0 == this.errorCode)
	{
		if (this.showQuantity)
		{
			BX.bind(this.obQuantityUp, 'click', BX.delegate(this.QuantityUp, this));
			BX.bind(this.obQuantityDown, 'click', BX.delegate(this.QuantityDown, this));
			BX.bind(this.obQuantity, 'change', BX.delegate(this.QuantityChange, this));
		}
		switch (this.productType)
		{
			case 1://product
				break;
			case 3://sku
				var TreeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
				if (!!TreeItems && 0 < TreeItems.length)
				{
					for (i = 0; i < TreeItems.length; i++)
					{
						BX.bind(TreeItems[i], 'click', BX.delegate(function(e){this.SelectOfferProp(e); }, this));
					}
				}
				for (i = 0; i < this.obTreeRows.length; i++)
				{
					BX.bind(this.obTreeRows[i].LEFT, 'click', BX.delegate(function(e){this.RowLeft(e); }, this));
					BX.bind(this.obTreeRows[i].RIGHT, 'click', BX.delegate(function(e){this.RowRight(e); }, this));
				}
				this.SetCurrent();
				break;
		}
		if (!!this.obBuyBtn)
			BX.bind(this.obBuyBtn, 'click', BX.delegate(this.Basket, this));
	}
};

JCCatalogSection.prototype.QuantityUp = function()
{
	var curValue = 0;
	var boolSet = true;
	if (0 == this.errorCode && this.showQuantity)
	{
		curValue = (
			this.isDblQuantity
			? parseFloat(this.obQuantity.value)
			: parseInt(this.obQuantity.value)
		);
		if (!isNaN(curValue))
		{
			curValue += this.stepQuantity;
			if (this.checkQuantity)
			{
				if (curValue > this.maxQuantity)
					boolSet = false;
			}
			if (boolSet)
			{
				this.obQuantity.value = curValue;
			}
		}
	}
};

JCCatalogSection.prototype.QuantityDown = function()
{
	var curValue = 0;
	var boolSet = true;
	if (0 == this.errorCode && this.showQuantity)
	{
		curValue = (
			this.isDblQuantity
			? parseFloat(this.obQuantity.value)
			: parseInt(this.obQuantity.value)
		);
		if (!isNaN(curValue))
		{
			curValue -= this.stepQuantity;
			if (curValue < this.stepQuantity)
				boolSet = false;
			if (boolSet)
			{
				this.obQuantity.value = curValue;
			}
		}
	}
};

JCCatalogSection.prototype.QuantityChange = function()
{
	var curValue = 0;
	var boolSet = true;
	if (0 == this.errorCode && this.showQuantity)
	{
		curValue = (
			this.isDblQuantity
			? parseFloat(this.obQuantity.value)
			: parseInt(this.obQuantity.value)
		);
		if (!isNaN(curValue))
		{
			if (this.checkQuantity)
			{
				if (curValue > this.maxQuantity)
				{
					boolSet = false;
					curValue = this.maxQuantity;
				}
				else if (curValue < this.stepQuantity)
				{
					boolSet = false;
					curValue = this.stepQuantity;
				}
			}
			if (!boolSet)
			{
				this.obQuantity.value = curValue;
				//
			}
		}
		else
		{
			this.obQuantity.value = this.stepQuantity;
			//
		}
	}
};

JCCatalogSection.prototype.QuantitySet = function(index)
{
	if (0 == this.errorCode)
	{
		this.canBuy = this.offers[index].CAN_BUY;
		if (this.showQuantity)
		{
			this.isDblQuantity = this.offers[index].QUANTITY_FLOAT;
			this.checkQuantity = this.offers[index].CHECK_QUANTITY;
			this.maxQuantity = (this.isDblQuantity
				? parseFloat(this.offers[index].MAX_QUANTITY)
				: parseInt(this.offers[index].MAX_QUANTITY)
			);
			this.stepQuantity = (this.isDblQuantity
				? parseFloat(this.offers[index].STEP_QUANTITY)
				: parseInt(this.offers[index].STEP_QUANTITY)
			);
			this.obQuantity.value = this.stepQuantity;
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
	}
};

JCCatalogSection.prototype.SelectOfferProp = function(e)
{
	if (!e) e = window.event;
	var target = BX.proxy_context;
	if (!!target && target.hasAttribute('data-treevalue'))
	{
		var strTreeValue = target.getAttribute('data-treevalue');
		var arTreeItem = strTreeValue.split('_');
		if (this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1]))
		{
			var RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
			if (!!RowItems && 0 < RowItems.length)
			{
				for (i = 0; i < RowItems.length; i++)
				{
					value = RowItems[i].getAttribute('data-onevalue');
					if (value == arTreeItem[1])
						BX.addClass(RowItems[i], 'bx_active');
					else
						BX.removeClass(RowItems[i], 'bx_active');
				}
			}
		}
	}
};

JCCatalogSection.prototype.SearchOfferPropIndex = function(strPropID, strPropValue)
{
	var index = -1;
	for (var i = 0; i < this.treeProps.length; i++)
	{
		if (this.treeProps[i].ID == strPropID)
		{
			index = i;
			break;
		}
	}

	if (-1 < index)
	{
		var arFilter = {};
		for (i = 0; i < index; i++)
		{
			var strName = 'PROP_'+this.treeProps[i].ID;
			arFilter[strName] = this.selectedValues[strName];
		}
		var strName = 'PROP_'+this.treeProps[index].ID;
		var arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
			return false;
		if (!BX.util.in_array(strPropValue, arShowValues))
			return false;
		arFilter[strName] = strPropValue;
		for (i = index+1; i < this.treeProps.length; i++)
		{
			strName = 'PROP_'+this.treeProps[i].ID;
			var arShowValues = this.GetRowValues(arFilter, strName);
			if (!arShowValues)
				return false;
			if (this.showAbsent)
			{
				var arCanBuyValues = [];
				var tmpFilter = []; 
				tmpFilter = BX.clone(arFilter, true);
				for (var j = 0; j < arShowValues.length; j++)
				{
					tmpFilter[strName] = arShowValues[j];
					if (this.GetCanBuy(tmpFilter))
						arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
			else
			{
				var arCanBuyValues = arShowValues;
			}
			if (!!this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues))
				arFilter[strName] = this.selectedValues[strName];
			else
				arFilter[strName] = arCanBuyValues[0];
			this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
		}
		this.selectedValues = arFilter;
		this.ChangeInfo();
	}
	return true;
};

JCCatalogSection.prototype.RowLeft = function(e)
{
	if (!e) e = window.event;
	var target = BX.proxy_context;
	if (!!target && target.hasAttribute('data-treevalue'))
	{
		var strTreeValue = target.getAttribute('data-treevalue');
		var index = -1;
		for (var i = 0; i < this.treeProps.length; i++)
		{
			if (this.treeProps[i].ID == strTreeValue)
			{
				index = i;
				break;
			}
		}
		if (-1 < index && 5 < this.showCount[index])
		{
			if ((5 - this.showStart[index]) < this.showCount[index])
			{
				this.showStart[index]--;
				BX.adjust(this.obTreeRows[index].LIST, { style: { marginLeft: this.showStart[index]*20+'%' }});
			}
		}
	}
};

JCCatalogSection.prototype.RowRight = function(e)
{
	if (!e) e = window.event;
	var target = BX.proxy_context;
	if (!!target && target.hasAttribute('data-treevalue'))
	{
		var strTreeValue = target.getAttribute('data-treevalue');
		var index = -1;
		for (var i = 0; i < this.treeProps.length; i++)
		{
			if (this.treeProps[i].ID == strTreeValue)
			{
				index = i;
				break;
			}
		}
		if (-1 < index && 5 < this.showCount[index])
		{
			if (0 > this.showStart[index])
			{
				this.showStart[index]++;
				BX.adjust(this.obTreeRows[index].LIST, { style: { marginLeft: this.showStart[index]*20+'%' }});
			}
		}
	}
};

JCCatalogSection.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID)
{
	var i = 0;
	var value;
	var countShow = 0;
	var strNewLen = '';
	if (-1 < intNumber && intNumber < this.obTreeRows.length)
	{
		var RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: 'li'}, false);
		if (!!RowItems && 0 < RowItems.length)
		{
			countShow = showID.length;
			strNewLen = (5 < countShow ? (100/countShow)+'%' : '20%');
			obData = {
				props: { className: '' },
				style: {
					width: strNewLen
				}
			};
			if ('E' == this.treeProps[intNumber].TYPE)
				obData.style.paddingTop = strNewLen;
			for (i = 0; i < RowItems.length; i++)
			{
				value = RowItems[i].getAttribute('data-onevalue');
				if (BX.util.in_array(value, canBuyID))
				{
					if (value == activeID)
						obData.props.className = 'bx_active';
					else
						obData.props.className = '';
				}
				else
				{
					if (value == activeID)
						obData.props.className = 'bx_active bx_missing';
					else
						obData.props.className = 'bx_missing';
				}
				if (BX.util.in_array(value, showID))
					obData.style.display = '';
				else
					obData.style.display = 'none';
				BX.adjust(RowItems[i], obData);					
			}
			obData = {
				style: {
					width: (5 < countShow ? 20*countShow : 100)+'%',
					marginLeft: '0%'
				} 
			};
			BX.adjust(this.obTreeRows[intNumber].LIST, obData);
			if ('E' == this.treeProps[intNumber].TYPE)
				BX.adjust(this.obTreeRows[intNumber].CONT, {props: {className: (5 < countShow ? 'bx_item_detail_scu full' : 'bx_item_detail_scu')}});
			else
				BX.adjust(this.obTreeRows[intNumber].CONT, {props: {className: (5 < countShow ? 'bx_item_detail_size full' : 'bx_item_detail_size')}});
			if (5 < countShow)
			{
				BX.adjust(this.obTreeRows[intNumber].LEFT, {style: {display: ''}});
				BX.adjust(this.obTreeRows[intNumber].RIGHT, {style: {display: ''}});
			}
			else
			{
				BX.adjust(this.obTreeRows[intNumber].LEFT, {style: {display: 'none'}});
				BX.adjust(this.obTreeRows[intNumber].RIGHT, {style: {display: 'none'}});				
			}
			this.showCount[intNumber] = countShow;
			this.showStart[intNumber] = 0;
		}
	}
};

JCCatalogSection.prototype.GetRowValues = function(arFilter, index)
{
	var arValues = [];
	var boolSearch = false;
	if (0 == arFilter.length)
	{
		for (var i = 0; i < this.offers.length; i++)
		{
			if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
				arValues[arValues.length] = this.offers[i].TREE[index];
		}
		boolSearch = true;
	}
	else
	{
		for (var i = 0; i < this.offers.length; i++)
		{
			var boolOneSearch = true;
			for (var j in arFilter)
			{
				if (arFilter[j] != this.offers[i].TREE[j])
				{
					boolOneSearch = false;
					break;
				}
			}
			if (boolOneSearch)
			{
				if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
					arValues[arValues.length] = this.offers[i].TREE[index];
				boolSearch = true;
			}
		}
	}
	return (boolSearch ? arValues : false);
};

JCCatalogSection.prototype.GetCanBuy = function(arFilter)
{
	var boolSearch = false;
	for (var i = 0; i < this.offers.length; i++)
	{
		var boolOneSearch = true;
		for (var j in arFilter)
		{
			if (arFilter[j] != this.offers[i].TREE[j])
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

JCCatalogSection.prototype.SetCurrent = function()
{
	var arFilter = {};
	var current = this.offers[this.offerNum].TREE;
	for (var i = 0; i < this.treeProps.length; i++)
	{
		var strName = 'PROP_'+this.treeProps[i].ID;
		var arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
			break;
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
			var arCanBuyValues = [];
			var tmpFilter = []; 
			tmpFilter = BX.clone(arFilter, true);
			for (var j = 0; j < arShowValues.length; j++)
			{
				tmpFilter[strName] = arShowValues[j];
				if (this.GetCanBuy(tmpFilter))
					arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
			}
		}
		else
		{
			var arCanBuyValues = arShowValues;
		}
		this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
	}
	this.selectedValues = arFilter;
	this.ChangeInfo();
};

JCCatalogSection.prototype.ChangeInfo = function()
{
	var index = -1;
	for (var i = 0; i < this.offers.length; i++)
	{
		var boolOneSearch = true;
		for (var j in this.selectedValues)
		{
			if (this.selectedValues[j] != this.offers[i].TREE[j])
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
	if (-1 < index)
	{
		if (!!this.obPict)
		{	
			if (!!this.offers[index].PREVIEW_PICTURE)
				BX.adjust(this.obPict, {style: {backgroundImage: 'url('+this.offers[index].PREVIEW_PICTURE.SRC+')'}});
			else
				BX.adjust(this.obPict, {style: {backgroundImage: 'url('+this.defaultPict.pict.SRC+')'}});
		}
		if (this.secondPict && !!this.obSecondPict)
		{
			if (!!this.offers[index].PREVIEW_PICTURE_SECOND)
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.offers[index].PREVIEW_PICTURE_SECOND.SRC+')'}});
			else if (!!this.offers[index].PREVIEW_PICTURE.SRC)
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.offers[index].PREVIEW_PICTURE.SRC+')'}});
			else if (!!this.defaultPict.secondPict)
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.defaultPict.secondPict.SRC+')'}});
			else
				BX.adjust(this.obSecondPict, {style: {backgroundImage: 'url('+this.defaultPict.pict.SRC+')'}});
		}
		if (this.showSkuProps && !!this.obSkuProps)
		{
			if (0 == this.offers[index].DISPLAY_PROPERTIES.length)
			{
				BX.adjust(this.obSkuProps, {style: {display: 'none'}, html: ''});
			}
			else
			{
				BX.adjust(this.obSkuProps, {style: {display: ''}, html: this.offers[index].DISPLAY_PROPERTIES});
			}
		}
		if (!!this.obPrice)
		{
			var strPrice = this.offers[index].PRICE.PRINT_DISCOUNT_VALUE;
			if (this.showOldPrice && (this.offers[index].PRICE.DISCOUNT_VALUE != this.offers[index].PRICE.VALUE))
				strPrice += ' <span>'+this.offers[index].PRICE.PRINT_VALUE+'</span>';
			BX.adjust(this.obPrice, {html: strPrice});
			if (this.showPercent)
			{
				if (this.offers[index].PRICE.DISCOUNT_VALUE != this.offers[index].PRICE.VALUE)
				{
					var obData = {
						style: {
							display: ''
						},
						html: this.offers[index].PRICE.DISCOUNT_DIFF_PERCENT
					};
				}
				else
				{
					var obData = {
						style: {
							display: 'none'
						},
						html: ''
					};
				}
				if (!!this.obDscPerc)
					BX.adjust(this.obDscPerc, obData);
				if (!!this.obSecondDscPerc)
					BX.adjust(this.obSecondDscPerc, obData);
			}
		}
		this.offerNum = index;
		if (this.showQuantity)
		{
			this.QuantitySet(this.offerNum);
		}
	}
};

JCCatalogSection.prototype.Basket = function()
{
	if (!this.canBuy)
		return;
	var ajaxParams = {
		sessid: BX.bitrix_sessid(),
		action: 'ADD2BASKET',
		ajax_basket: 'Y'
	};
	switch (this.productType)
	{
	case 1://product
	case 2://set
		ajaxParams.id = this.product.id;
		if (this.showQuantity)
			ajaxParams.quantity = this.obQuantity.value;
		break;
	case 3://sku
		ajaxParams.id = this.offers[this.offerNum].ID;
		if (this.showQuantity)
			ajaxParams.quantity = this.obQuantity.value;
		break;
	default:
		return;
	}
	BX.ajax.loadJSON(
		this.ajaxPath,
		ajaxParams,
		BX.delegate(this.ShowBasketPopup, this)
	);
};

JCCatalogSection.prototype.ShowBasketPopup = function(arResult)
{
	var strContent = '';
	if ('object' == typeof(arResult))
	{
		if ('OK' == arResult.STATUS)
		{
			strName = '';
			strPict = '';
			switch(this.productType)
			{
			case 1://
			case 2://
				strName = this.product.name;
				strPict = this.product.pict.SRC;
				break;
			case 3:
				strName = this.offers[this.offerNum].NAME;
				strPict = this.offers[this.offerNum].PREVIEW_PICTURE.SRC;
				break;
			}
			strContent = '<p>'+BX.message('ADD_TO_BASKET_OK')+'</p>';
			strContent += '<img src="'+strPict+'" height="130"><p>'+strName+'</p>';
		}
		else
		{
			
		}
	}
	else
	{
		
	}

    var popup = BX.PopupWindowManager.create('CatalogSectionBasket'+this.visual.ID, null, {
        autoHide: false,
        //    zIndex: 0,
        offsetLeft: 0,
        offsetTop: 0,
        overlay : true,
        draggable: {restrict:true},
        closeByEsc: true,
        closeIcon: { right : "12px", top : "10px"},
        content: '' +
            '<div style="width:300px;text-align: center;padding-top:5px; margin-bottom: 10px;">' +
            strContent+
            '<a class="bx_bt_blue bx_medium" href="'+BX.message("setButtonBuyUrl")+'"><span class="bx_icon_cart"></span><span>'+BX.message("setButtonBuyName")+'</span></a>'+
            '</div>'
	    });

    popup.show();
};