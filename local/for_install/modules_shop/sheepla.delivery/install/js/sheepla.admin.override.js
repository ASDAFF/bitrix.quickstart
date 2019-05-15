/**
 * http://sheepla.ru
 * User: Evgeniy Khodakov
 * Date: 21.03.2013
 *
 * JavaScript for override bitrix admin interface functions
 */
if (typeof(sheepla.admin) !== 'undefined') {

    /* Override get_city() function */
    sheepla.admin.get_city = function() {
        return 'Санкт-Петербург';
    };

}