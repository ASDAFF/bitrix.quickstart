BX.namespace("BX.Iblock.Catalog");

BX.Iblock.Catalog.CompareClass = (function()
{
	var CompareClass = function(wrapObjId)
	{
		this.wrapObjId = wrapObjId;
                console.log(this.wrapObjId);
	};

	CompareClass.prototype.MakeAjaxAction = function(url)
	{
		rsFlyaway.darken($("#" + this.wrapObjId));
		BX.ajax.post(
			url,
			{
				ajax_action: 'Y'
			},
			BX.proxy(function(result)
			{
				rsFlyaway.darken($("#" + this.wrapObjId));
				BX(this.wrapObjId).innerHTML = result;
			}, this)
		);
	};

	return CompareClass;
})();



$(function() {
    "use strict";

    $(".compare-result__scroll").scroll(function() {
        var scroll = $(this).scrollLeft() - 10 > 0 ? $(this).scrollLeft() - 10 : 0;
        $(".compare-result__table-row .compare-result__table-property").css({
            '-webkit-transform' : 'scale(' + scroll + 'px)',
            '-moz-transform': 'translateX(' + scroll + 'px)',
            '-ms-transform': 'translateX(' + scroll + 'px)',
            '-o-transform': 'translateX(' + scroll + 'px)',
            'transform': 'translateX(' + scroll + 'px)'
        });

    });

});
