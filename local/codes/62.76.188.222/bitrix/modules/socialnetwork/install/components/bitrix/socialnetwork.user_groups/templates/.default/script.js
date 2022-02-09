function AddPopupGroup(e)
{
	if(!e) e = window.event;
	
	if (isLeftClick(e))
	{
		sonetGroupIFramePopup.Add();
		return BX.PreventDefault(e);
	}
}

function isLeftClick(event)
{
	if (!event.which && event.button !== undefined)
	{
		if (event.button & 1)
			event.which = 1;
		else if (event.button & 4)
			event.which = 2;
		else if (event.button & 2)
			event.which = 3;
		else
			event.which = 0;
	}

	return event.which == 1 || (event.which == 0 && BX.browser.IsIE());
};