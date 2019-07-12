/* http://keith-wood.name/countdown.html
 * Russian initialisation for the jQuery countdown extension
 * Written by Sergey K. (xslade{at}gmail.com) June 2010. */
(function($) {
        $.countdown.regionalOptions['ru'] = {
		labels: [BX.message('COUNTDOWN_YEAR0'), BX.message('COUNTDOWN_MONTH0'), BX.message('COUNTDOWN_WEAK0'), BX.message('COUNTDOWN_DAY0'), BX.message('COUNTDOWN_HOUR'), BX.message('COUNTDOWN_MIN'), BX.message('COUNTDOWN_SEC')],
		labels1: [BX.message('COUNTDOWN_YEAR1'), BX.message('COUNTDOWN_MONTH1'), BX.message('COUNTDOWN_WEAK1'), BX.message('COUNTDOWN_DAY1'), BX.message('COUNTDOWN_HOUR'), BX.message('COUNTDOWN_MIN'), BX.message('COUNTDOWN_SEC')],
		labels2: [BX.message('COUNTDOWN_YEAR2'), BX.message('COUNTDOWN_MONTH2'), BX.message('COUNTDOWN_WEAK2'), BX.message('COUNTDOWN_DAY2'), BX.message('COUNTDOWN_HOUR'), BX.message('COUNTDOWN_MIN'), BX.message('COUNTDOWN_SEC')],
		compactLabels: ['л', 'м', 'н', 'д'], compactLabels1: ['г', 'м', 'н', 'д'],
		whichLabels: function(amount) {
			var units = amount % 10;
			var tens = Math.floor((amount % 100) / 10);
			return (amount == 1 ? 1 : (units >= 2 && units <= 4 && tens != 1 ? 2 :
				(units == 1 && tens != 1 ? 1 : 0)));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['ru']);
})(jQuery);