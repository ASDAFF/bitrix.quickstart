$(function() {
    'use strict';
    var ajaxData = BX.localStorage.get('ajax_data'),
        key;

    if(ajaxData) {

        if(typeof ajaxData === 'string' || ajaxData instanceof String) {
            ajaxData = JSON.parse(ajaxData);
        }

        for(key in ajaxData) {
            $(".webform [name=" + key +"]").val(ajaxData[key]);
        }

    }
});
