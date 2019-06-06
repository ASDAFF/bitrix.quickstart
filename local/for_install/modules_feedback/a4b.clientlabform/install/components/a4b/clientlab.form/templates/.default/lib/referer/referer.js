function searchText(a, b) {
	return !!(a.search(b) + 1)
}

function paramUrl(a) {
	var b = {
		utm_campaign: "",
		utm_term: "",
		utm_medium: "",
		utm_source: ""
	};
	try {
		for (var c = a.split("&"), d = c.length - 1; d >= 0; d--) {
			var e = c[d].split("=");
			b[e[0]] = e[1]
		}
		return b
	} catch (a) {
		return b
	}
	return b
}

function setCookie(a, b, c) {
	c = c || {};
	var d = c.expires;
	if ("number" == typeof d && d) {
		var e = new Date;
		e.setTime(e.getTime() + 1e3 * d), d = c.expires = e
	}
	d && d.toUTCString && (c.expires = d.toUTCString()), b = encodeURIComponent(b);
	var f = a + "=" + b;
	for (var g in c) {
		f += "; " + g;
		var h = c[g];
		h !== !0 && (f += "=" + h)
	}
	document.cookie = f
}

function referer() {
	function h(a) {
		for (var b = unescape("%u0402%u0403%u201A%u0453%u201E%u2026%u2020%u2021%u20AC%u2030%u0409%u2039%u040A%u040C%u040B%u040F%u0452%u2018%u2019%u201C%u201D%u2022%u2013%u2014%u0000%u2122%u0459%u203A%u045A%u045C%u045B%u045F%u00A0%u040E%u045E%u0408%u00A4%u0490%u00A6%u00A7%u0401%u00A9%u0404%u00AB%u00AC%u00AD%u00AE%u0407%u00B0%u00B1%u0406%u0456%u0491%u00B5%u00B6%u00B7%u0451%u2116%u0454%u00BB%u0458%u0405%u0455%u0457"), c = function(a) {
				return a >= 192 && a <= 255 ? String.fromCharCode(a - 192 + 1040) : a >= 128 && a <= 191 ? b.charAt(a - 128) : String.fromCharCode(a)
			}, d = "", e = 0; e < a.length; e++) d += c(a.charCodeAt(e));
		return d
	}
	engines = [{
		start: "http://www.google.",
		query: "q",
		name: "google"
	}, {
		start: "http://yandex.",
		query: "text",
		name: "yandex"
	}, {
		start: "rambler.ru/search",
		query: "query",
		name: "rambler"
	}, {
		start: "http://go.mail.ru/",
		query: "q",
		name: "mailru",
		cp1251: !0
	}, {
		start: "http://www.bing.com/",
		query: "q",
		name: "bing"
	}, {
		start: "search.yahoo.com/search",
		query: "p",
		name: "yahoo"
	}, {
		start: "http://ru.ask.com/",
		query: "q",
		name: "ask"
	}, {
		start: "http://search.qip.ru/search",
		query: "query",
		name: "qip"
	}];
	var d, e, a = document.referrer,
		b = "",
		c = "";
	for (var f in engines)
		if (engines.hasOwnProperty(f) && a.indexOf(engines[f].start) != -1) {
			if (d = a.indexOf("?" + engines[f].query + "="), d == -1 && (d = a.indexOf("&" + engines[f].query + "="), d == -1)) return !1;
			c = engines[f].name, b = engines[f].query, e = engines[f].hasOwnProperty("cp1251")
		}
	if (!c) return !1;
	a = a.substr(d + b.length + 2);
	var g = a.indexOf("&");
	if (g != -1 && (a = a.substr(0, g)), e ? (a = unescape(a), a = h(a)) : a = decodeURIComponent(a), a = a.replace(/[+]+/g, " ")) try {
		setCookie("reftext", a, {
			expires: 2629743,
			path: "/"
		})
	} catch (a) {
		window.addEventListener("load", function() {
			referer()
		})
	}
	return [a, c]
}
var praUrl = paramUrl(location.href.split("?")[1]);
if (document.referrer) {
	var d = document.referrer.split("/");
	if (d[2].indexOf("yandex") != -1) {
		var tw = paramUrl(d[3]);
		setCookie("stext", tw.text, {
			expires: 2629743,
			path: "/"
		})
	}
	console.log(d[2]), searchText(window.location.href, d[2]) || (console.log("true"), setCookie("ref", d[2], {
		expires: 2629743,
		path: "/"
	}))
}
praUrl.utm_source && setCookie("source", praUrl.utm_source, {
	expires: 2629743,
	path: "/"
}), praUrl.utm_medium && setCookie("medium", praUrl.utm_medium, {
	expires: 2629743,
	path: "/"
}), praUrl.utm_term && setCookie("term", praUrl.utm_term, {
	expires: 2629743,
	path: "/"
}), praUrl.utm_campaign && setCookie("campaign", praUrl.utm_campaign, {
	expires: 2629743,
	path: "/"
}), referer();