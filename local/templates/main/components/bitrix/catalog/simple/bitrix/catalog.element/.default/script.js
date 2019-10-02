JCCatalogElement = function (arParams)
{
	this.productType = 0;
	this.showQuantity = true;
	this.showAbsent = true;
	this.checkQuantity = false;
	this.maxQuantity = 0;
	this.stepQuantity = 1;
	this.isDblQuantity = false;
	this.canBuy = true;
	this.canSubscription = true;

	this.showOldPrice = false;
	this.showPercent = false;
	this.showSkuProps = false;
	this.showOfferGroup = false;
	
	this.visual = {
		ID: '',
		PICT_ID: '',
		QUANTITY_ID: '',
		QUANTITY_UP_ID: '',
		QUANTITY_DOWN_ID: '',
		PRICE_ID: '',
		OLD_PRICE_ID: '',
		DISCOUNT_VALUE_ID: '',
		DISCOUNT_PERC_ID: '',
		OFFER_GROUP: ''
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
	this.ajaxPath = '';
	this.mess = {};
	
	this.defaultPict = {
		preview: null,
		detail: null
	};

	this.offers = [];
	this.offerNum = 0;
	this.treeProps = [];
	this.obTreeRows = [];
	this.showCount = [];
	this.showStart = [];
	this.selectedValues = {};
	this.sliders = [];

	this.obProduct = null;
	this.obQuantity = null;
	this.obQuantityUp = null;
	this.obQuantityDown = null;
	this.obPict = null;
	this.obPrice = {
		price: null,
		full: null,
		discount: null,
		percent: null
	};
	this.obTree = null;
	this.obBuyBtn = null;
	this.obSkuProps = null;
	this.obSlider = null;
	this.obMeasure = null;
	this.obQuantityLimit = {
		all: null,
		value: null
	};
	
	this.obZoom = {
		cont: null,
		pict: null
	};
	this.enableZoom = false;
	this.showZoom = false;
	
	this.errorCode = 0;
	
	if ('object' == typeof(arParams))
	{
		this.productType = parseInt(arParams.PRODUCT_TYPE);
		this.showQuantity = arParams.SHOW_QUANTITY;
		if (!!arParams.SHOW_DISCOUNT_PERCENT)
			this.showPercent = true;
		if (!!arParams.SHOW_OLD_PRICE)
			this.showOldPrice = true;
		if (!!arParams.SHOW_SKU_PROPS)
			this.showSkuProps = true;
		if (!!arParams.OFFER_GROUP)
			this.showOfferGroup = true;
		this.visual = arParams.VISUAL;
		this.ajaxPath = arParams.AJAX_PATH;
		if (!!arParams.MESS)
			this.mess = arParams.MESS;
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
					this.product.buyUrl = arParams.PRODUCT.BUY_URL;
					
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
						this.defaultPict.preview = arParams.DEFAULT_PICTURE.PREVIEW_PICTIRE;
						this.defaultPict.detail = arParams.DEFAULT_PICTURE.DETAIL_PICTURE;
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

JCCatalogElement.prototype.Init = function()
{
	var i = 0;
	var j = 0;
	this.obProduct = BX(this.visual.ID);
	if (!this.obProduct)
		this.errorCode = -1;
	
	if (!!this.visual.BUY_ID)
	{
		this.obBuyBtn = BX(this.visual.BUY_ID);
	}

    if (!!this.obBuyBtn)
        BX.bind(this.obBuyBtn, 'click', BX.delegate(this.Basket, this));
};

JCCatalogElement.prototype.Basket = function()
{
	if (!this.canBuy)
		return;

    var strBasket = this.product.buyUrl;
    if (this.showQuantity)
        strBasket += '&quantity='+this.obQuantity.value;
    location.href=strBasket;
};
