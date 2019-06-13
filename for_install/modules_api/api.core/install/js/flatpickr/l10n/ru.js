/* Russian locals for flatpickr */
var flatpickr = flatpickr || { l10ns: {} };
flatpickr.l10ns.ru = {};

flatpickr.l10ns.ru.firstDayOfWeek = 1; // Monday
flatpickr.l10ns.ru.scrollTitle = BX.message('API_CORE_FLATPICKR_SCROLLTITLE');
flatpickr.l10ns.ru.toggleTitle = BX.message('API_CORE_FLATPICKR_TOGGLETITLE');

flatpickr.l10ns.ru.weekdays = {
	shorthand: BX.message('API_CORE_FLATPICKR_WEEKDAYS_SHORTHAND'),
	longhand: BX.message('API_CORE_FLATPICKR_WEEKDAYS_LONGHAND')
};

flatpickr.l10ns.ru.months = {
	shorthand: BX.message('API_CORE_FLATPICKR_MONTHS_SHORTHAND'),
	longhand: BX.message('API_CORE_FLATPICKR_MONTHS_LONGHAND')
};
if (typeof module !== "undefined") module.exports = flatpickr.l10ns;