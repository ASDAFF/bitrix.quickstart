function OpenClose(element)
{
	if (BX.hasClass(element, 'close'))
	{
		BX.addClass(element, 'open');
		BX.removeClass(element, 'close');
	}
	else
	{
		BX.addClass(element, 'close');
		BX.removeClass(element, 'open');
	}
}
