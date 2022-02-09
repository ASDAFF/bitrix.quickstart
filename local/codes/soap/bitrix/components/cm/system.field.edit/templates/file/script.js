function addElement(Name, thisButton)
{
	var parentElement = document.getElementById('main_' + Name);
	var clone = document.getElementById('main_add_' + Name);
	if(parentElement && clone)
	{
		clone = clone.cloneNode(true);
		clone.id = '';
		clone.style.display = '';
		parentElement.appendChild(clone);
	}
	return;
}