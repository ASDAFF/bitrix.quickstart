var jshover = function() {
	var sfEls = document.getElementById("web-blue-tabs-menu").getElementsByTagName("li");
	for (var i=0; i<sfEls.length; i++) 
	{
		sfEls[i].onmouseover=function()
		{
			this.className+=" jshover";
		}
		sfEls[i].onmouseout=function() 
		{
			this.className=this.className.replace(new RegExp(" jshover\\b"), "");
		}
	}
	}

if (window.attachEvent) 
  window.attachEvent("onload", jshover);