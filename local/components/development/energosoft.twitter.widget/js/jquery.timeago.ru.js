/*
######################################################
# Name: energosoft.twitter                           #
# File: jquery.timeago.ru.js                         #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
*/

jQuery.timeago.settings.strings = {
	prefixAgo: null,
	prefixFromNow: "через",
	suffixAgo: "назад",
	suffixFromNow: null,
	seconds: "меньше минуты",
	minute: "минуту",
	minutes: function(value) { return jQuery.esTimeFormatRU(value, "%d минута", "%d минуты", "%d минут"); },
	hour: "час",
	hours: function(value) { return jQuery.esTimeFormatRU(value, "%d час", "%d часа", "%d часов"); },
	day: "день",
	days: function(value) { return jQuery.esTimeFormatRU(value, "%d день", "%d дн€", "%d дней"); },
	month: "мес€ц",
	months: function(value) { return jQuery.esTimeFormatRU(value, "%d мес€ц", "%d мес€ца", "%d мес€цев"); },
	year: "год",
	years: function(value) { return jQuery.esTimeFormatRU(value, "%d год", "%d года", "%d лет"); }
};

(function(jQuery){
	jQuery.extend({
		esTimeFormatRU: function(n, f, s, t)
		{
			var n10 = n % 10;
			if((n10 == 1) && ((n == 1) || (n > 20))) {
				return f;
			} else if ((n10 > 1) && (n10 < 5) && ((n > 20) || (n < 10))) {
				return s;
			} else
				return t;
		}
	});
})(jQuery);