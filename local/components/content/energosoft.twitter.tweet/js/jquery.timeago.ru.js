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
	prefixFromNow: "�����",
	suffixAgo: "�����",
	suffixFromNow: null,
	seconds: "������ ������",
	minute: "������",
	minutes: function(value) { return jQuery.esTimeFormatRU(value, "%d ������", "%d ������", "%d �����"); },
	hour: "���",
	hours: function(value) { return jQuery.esTimeFormatRU(value, "%d ���", "%d ����", "%d �����"); },
	day: "����",
	days: function(value) { return jQuery.esTimeFormatRU(value, "%d ����", "%d ���", "%d ����"); },
	month: "�����",
	months: function(value) { return jQuery.esTimeFormatRU(value, "%d �����", "%d ������", "%d �������"); },
	year: "���",
	years: function(value) { return jQuery.esTimeFormatRU(value, "%d ���", "%d ����", "%d ���"); }
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