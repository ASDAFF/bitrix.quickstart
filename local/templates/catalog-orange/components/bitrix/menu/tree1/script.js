function OpenMenuNode(oThis)
{
	if (oThis.parentNode.className == '')
		oThis.parentNode.className = 'close';
	else
		oThis.parentNode.className = '';
	return false;
}