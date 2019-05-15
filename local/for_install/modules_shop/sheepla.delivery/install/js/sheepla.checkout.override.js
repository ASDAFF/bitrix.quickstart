/**
 * http://sheepla.ru
 * User: Evgeniy Khodakov
 * Date: 21.03.2013
 *
 * JavaScript for override checkout functions
 */
if (typeof(sheepla.checkout) !== 'undefined') {

    /* Override get_city() function */
    sheepla.checkout.get_city = function() {
        return 'Санкт-Петербург';
    };

}