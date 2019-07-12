function JSEshopBasket(ajaxPath, site_id)
{
	this.ajaxPath = ajaxPath;
	this.site_id = site_id;

	var curObj = this;
	BX.addCustomEvent(window, "OnBasketChange", function() {
		curObj.OnBasketChangeHandler();
	});
}

JSEshopBasket.prototype.OnBasketChangeHandler = function()
{
	BX.ajax.post(
		this.ajaxPath,
		{
			sessid: BX.bitrix_sessid(),
			basketChange: "Y",
			site_id: this.site_id
		},
		function(num_products)
		{
			if (document.getElementById('bx_cart_num'))
				document.getElementById('bx_cart_num').innerHTML = (num_products > 0) ? " ("+num_products+")" : "";
		}
	);
}