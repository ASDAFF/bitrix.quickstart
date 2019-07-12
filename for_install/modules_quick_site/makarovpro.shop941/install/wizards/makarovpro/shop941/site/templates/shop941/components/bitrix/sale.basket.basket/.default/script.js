function leftScroll(prop, id)
{
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft);
		if (curVal < 0)
			el.style.marginLeft = curVal + 20 + '%';
	}
}

function rightScroll(prop, id)
{
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft);
		if (curVal >= 0)
			el.style.marginLeft = curVal - 20 + '%';
	}
}

function checkOut()
{
	BX("basket_form").submit();
	return true;
}

/*
function enterCoupon()
{
	recalcBasket();
}
*/

function updateQuantity(controlId, basketId, ratio)
{
	var oldVal = BX(controlId).defaultValue,
		newVal = parseFloat(BX(controlId).value) || 0;
		bValidChange = false; // if quantity is correct for this ratio

	if (ratio == 0 || ratio == 1)
	{
		bValidChange = true;
	}
	else
	{
		var newValInt = newVal * 10000,
			ratioInt = ratio * 10000,
			reminder = newValInt % ratioInt;

		if (reminder == 0)
			bValidChange = true;
	}

	if (bValidChange)
	{
		newVal = (ratio == 0 || ratio == 1) ? parseInt(newVal) : parseFloat(newVal).toFixed(2);

		BX(controlId).defaultValue = newVal;

		// update values of both controls (text input field and mobile quantity select) simultaneously
		BX("QUANTITY_INPUT_" + basketId).value = newVal;

	    var option,
	    	options = BX("QUANTITY_SELECT_" + basketId).options,
	    	i = options.length;
	    while (i--)
	    {
			option = options[i];
			if (parseFloat(option.value).toFixed(2) == parseFloat(newVal).toFixed(2))
				option.selected = true;
	    }

		// set hidden real quantity value (will be used in POST)
		BX("QUANTITY_" + basketId).value = newVal;

		//todo: recalcBasket();
	}
	else
	{
		BX(controlId).value = oldVal;
	}
}

function setQuantity(basketId, ratio, sign)
{
	var curVal = parseFloat(BX("QUANTITY_INPUT_" + basketId).value),
		newVal;

	newVal = (sign == 'up') ? curVal + ratio : curVal - ratio;

	if (newVal < 0)
		newVal = 0;

	newVal = newVal.toFixed(2);

	BX("QUANTITY_INPUT_" + basketId).value = newVal;
	BX("QUANTITY_INPUT_" + basketId).defaultValue = newVal;

	updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio);

	//todo:  recalcBasket();
}

// function recalcBasket()
// {
// 	var coupon = BX("COUPON").value;

// 	BX.showWait();

// 	//todo
// 	BX.ajax.post(
// 		"/bitrix/components/bitrix/sale.basket.basket/component.php",
// 		{ajax_request: 'Y'},
// 		recalcBasketResult
// 	);
// }

// function recalcBasketResult(result)
// {
// 	//todo
// 	BX.closeWait();
// }

function showBasketItemsList(val)
{
	BX.removeClass(BX("basket_toolbar_button"), "current");
	BX.removeClass(BX("basket_toolbar_button_delayed"), "current");
	BX.removeClass(BX("basket_toolbar_button_subscribed"), "current");
	BX.removeClass(BX("basket_toolbar_button_not_available"), "current");

	BX("normal_count").style.display = 'inline-block';
	BX("delay_count").style.display = 'inline-block';
	BX("subscribe_count").style.display = 'inline-block';
	BX("not_available_count").style.display = 'inline-block';

	if (val == 2)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
		{
			BX("basket_items_delayed").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_delayed"), "current");
			BX("delay_count").style.display = 'none';
		}
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
	else if(val == 3)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
		{
			BX("basket_items_subscribed").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_subscribed"), "current");
			BX("subscribe_count").style.display = 'none';
		}
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
	else if (val == 4)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
		{
			BX("basket_items_not_available").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_not_available"), "current");
			BX("not_available_count").style.display = 'none';
		}
	}
	else
	{
		if (BX("basket_items_list"))
		{
			BX("basket_items_list").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button"), "current");
			BX("normal_count").style.display = 'none';
		}
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
}

