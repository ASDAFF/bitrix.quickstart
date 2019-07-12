(function(window) {

	var apiVersion = 2;
	var statServerUrl = (document.location.protocol == "https:" ? "https://" : "http://") + "bitrix.info/bx_stat";

	var timing = getTiming();
	var ajax = getAjax();

	window._baq = window._baq || {};

	window._baq.setResponse = function (response)
	{
		var uidCookie = getCookie('BX_USER_ID');

		if (uidCookie == undefined && !!response.uid)
		{
			var cookieDate = new Date(new Date().getTime() + 1000*3600*24*365*10);
			document.cookie="BX_USER_ID="+response.uid+"; path=/; expires="+cookieDate.toUTCString();
		}
	}

	if (ajax && timing && !(window.BX && window.BX.admin))
	{
		if (timing.domContentLoadedEventStart > 0)
		{
			sendStat();
		}
		else if (document.addEventListener)
		{
			document.addEventListener("DOMContentLoaded", sendStat, false);
		}
	}

	function sendStat()
	{
		if ("withCredentials" in ajax)
		{
			doPost();
		}
		else
		{
			doGet();
		}
	}

	function doGet()
	{
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.async = true;
		script.src = statServerUrl + "?" + getParams();
		var s = document.getElementsByTagName("script")[0];
		s.parentNode.insertBefore(script, s);
	}

	function doPost()
	{
		ajax.open("POST", statServerUrl, true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.withCredentials = true;

		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4 && ajax.status == 200)
			{
				var jsonResponse = JSON.parse(this.responseText);
				window._baq.setResponse(jsonResponse);
			}
		};

		ajax.send(getParams());
	}

	function getParams()
	{
		var start = timing.navigationStart;
		var params =
			"d=" + encodeURIComponent(window.location.host) +
			"&ru=" + encodeURIComponent(window.location.pathname) +
			"&dns=" + (timing.domainLookupEnd-timing.domainLookupStart) +
			"&tcp=" + (timing.connectEnd-timing.connectStart) +
			"&srt=" + (timing.responseStart-timing.requestStart) +
			"&pdt=" + (timing.responseEnd-timing.responseStart) +
			"&rrt=" + (timing.fetchStart-start) +
			"&dit=" + (timing.domInteractive-start) +
			"&clt=" + (timing.domContentLoadedEventStart-start) +
			"&sr=" + window.screen.width + "x" + window.screen.height +
			"&prc=" + (timing.domInteractive-timing.domLoading) +
			"&com=" + (window.frameRequestStart ? "1" : "0") +
			"&tmz=" + new Date().getTimezoneOffset() +
			"&xts=" + new Date().getTime() +
			"&ver=" + apiVersion;

		if (window._ba)
		{
			for (var i = 0; i < window._ba.length; i++)
			{
				params += "&" + window._ba[i][0] + "=" + encodeURIComponent(window._ba[i][1]);
			}
		}

		return params;
	}

	function getAjax()
	{
		if (window.XMLHttpRequest)
		{
			return new XMLHttpRequest();
		}
		else if (window.ActiveXObject)
		{
			return new window.ActiveXObject("Microsoft.XMLHTTP");
		}

		return null;
	}

	function getTiming()
	{
		if (window.performance && window.performance.timing)
		{
			return window.performance.timing;
		}

		return null;
	}

	function getCookie(name) {
		var matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		));
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}


})(window);