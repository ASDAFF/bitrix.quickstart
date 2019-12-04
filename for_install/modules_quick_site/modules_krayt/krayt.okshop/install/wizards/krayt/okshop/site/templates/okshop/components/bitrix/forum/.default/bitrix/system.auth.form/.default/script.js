function ForumShowLoginForm(oA)
{
	var div = document.getElementById("forum-login-form-window");
	if (!div)
		return;
	var pos = jsUtils.GetRealPos(oA);
	pos['width'] = (pos['right'] - pos['left']);
	div.style.left = (pos['left'] + (pos['width'] / 2) - 100) + "px";
	div.style.top = (pos['bottom'] + 10) + "px";
	div.style.display = "block";
	document.body.appendChild(div);
	return false;
}

function ForumCloseLoginForm()
{
	var div = document.getElementById("forum-login-form-window");
	if (!div)
		return;

	div.style.display = "none";
	return false;
}