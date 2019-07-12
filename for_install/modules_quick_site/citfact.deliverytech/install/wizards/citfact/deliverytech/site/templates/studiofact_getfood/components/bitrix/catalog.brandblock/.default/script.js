;(function(window) {
	if (window.JCIblockBrands)
		return;

	JCIblockBrands = function (params)
	{
		this.brandBlockObj = null;
		this.brandBlockOverObj = null;
		this.timeoutID = null;

		this.DELAY_BEFROE_HIDE_POPUP = 350; //ms
	};

	JCIblockBrands.prototype.itemOver = function(obj)
	{
		this.brandBlockOverObj = obj;

		if(this.brandBlockObj == obj && this.timeoutID)
		{
			clearTimeout(this.timeoutID);
			this.timeoutID = null;
		}
		else if(this.brandBlockObj != obj && this.timeoutID)
			return;

		if(!BX.hasClass(obj, "hover"))
		{
			this.brandBlockObj = obj;
			BX.addClass(obj, "hover");
			this.correctPopup(obj);
		}
	};

	JCIblockBrands.prototype.itemOut = function(obj)
	{
		this.brandBlockOverObj = null;

		if(this.brandBlockObj && this.brandBlockObj != obj)
			return;

		if(!BX.hasClass(obj, "hover"))
			return;

		var _this = this;

		this.timeoutID = setTimeout(function() {

			BX.removeClass(obj, "hover");
			_this.timeoutID = null;
			_this.brandBlockObj = null;

			if (_this.brandBlockOverObj)
				_this.itemOver(_this.brandBlockOverObj);

			}, this.DELAY_BEFROE_HIDE_POPUP);
	};

	JCIblockBrands.prototype.getItemPopup = function(obj)
	{
		return BX.findChild(obj, {'className': 'bx_popup'}, true);
	};

	JCIblockBrands.prototype.correctPopup = function(obj)
	{
		var popupObj = this.getItemPopup(obj);

		if(popupObj)
		{
			var popupParams = BX.pos(popupObj);

			if(popupParams.height > 40)
				popupObj.style.top = "-1px";
			else
			{
				popupObj.style.top = "50%";
				popupObj.style.marginTop = "-"+popupParams.height/2+"px";
			}
		}
	};

})(window);