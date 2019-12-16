BX.Sale.OrderPaymentChange = (function()
{
	var classDescription = function(params)
	{
		this.ajaxUrl = params.url;
		this.accountNumber = params.accountNumber || {};
		this.paymentNumber = params.paymentNumber || {};
		this.wrapperId = params.wrapperId || "";
		this.templateFolder = params.templateFolder;
		this.wrapper = document.getElementById('bx-sopc'+ this.wrapperId);

		this.paySystemsContainer = this.wrapper.getElementsByClassName('sale-order-payment-change-pp')[0];
		BX.ready(BX.proxy(this.init, this));
	};
	
	classDescription.prototype.init = function()
	{

		var listPaySystems = this.wrapper.getElementsByClassName('sale-order-payment-change-pp-list')[0];
		new BX.easing(
		{
			duration: 500,
			start: {opacity: 0, height: 50},
			finish: {opacity: 100, height: 'auto'},
			transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
			step: function(state)
			{
				listPaySystems.style.opacity = state.opacity / 100;
				listPaySystems.style.height = listPaySystems.height / 450 + 'px';
			},
			complete: function()
			{
				listPaySystems.style.height = 'auto';
			}
		}).animate();

		BX.bindDelegate(this.paySystemsContainer, 'click', { 'className': 'sale-order-payment-change-pp-company' }, BX.proxy(
			function(event)
			{
				var targetParentNode = event.target.parentNode;
				var hidden = targetParentNode.getElementsByClassName("sale-order-payment-change-pp-company-hidden")[0];
				BX.ajax(
					{
						method: 'POST',
						dataType: 'html',
						url: this.ajaxUrl,
						data:
						{
							sessid: BX.bitrix_sessid(),
							paySystemId: hidden.value,
							accountNumber: this.accountNumber,
							paymentNumber: this.paymentNumber
						},
						onsuccess: BX.proxy(function(result)
						{
							this.paySystemsContainer.innerHTML = result;
							var detailPaimentImage = this.wrapper.parentNode.previousElementSibling.getElementsByClassName("sale-order-detail-payment-options-methods-image-element")[0];
							if (detailPaimentImage !== undefined)
							{
								detailPaimentImage.style.backgroundImage = event.target.style.backgroundImage;
							}
						},this),
						onfailure: BX.proxy(function()
						{
							return this;
						}, this)
					}, this
				);
				return this;
			}, this)
		);
		return this;
	};

	return classDescription;
})();