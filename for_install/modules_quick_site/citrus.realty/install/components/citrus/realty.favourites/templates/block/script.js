// nook determine in template.php
var rootFolder = "/bitrix/components/citrus/realty.favourites/templates/block/";

var popupOnOpen = function() {
    $('.closebutton').click(function () {
        $.fancybox.close();
    });
    $('button[data-url]').click(function () {
        window.location = $(this).data('url');
    });
};

$(function () {
    function getPosition(elem) {
        var el = $(elem).get(0);
        var p = {x: el.offsetLeft, y: el.offsetTop};
        while (el.offsetParent) {
            el = el.offsetParent;
            p.x += el.offsetLeft;
            p.y += el.offsetTop;
            if (el != document.body && el != document.documentElement) {
                p.x -= el.scrollLeft;
                p.y -= el.scrollTop;
            }
        }
        return p;
    };
    window.citrusRealtyFavUpdateCount = function(count) {
        if (count > 0)
            $('.realty-favourites').html(BX.message('CITRUS_REALTY_FAV') + ' (' + count.toString() + ')');
        else
            $('.realty-favourites').html(BX.message('CITRUS_REALTY_FAV') + ' (0)');
    }
    window.citrusRealtyMark = function ($element, type) {
        /*var id = $element.data('id');
        if ($element.parents('.case-add').length)
            $element.replaceWith('<span data-id="' + id + '">' + BX.message('CITRUS_REALTY_FAV_ADDED_TO') + '</span>');
        else
            $element.replaceWith('<span data-id="' + id + '">' + BX.message('CITRUS_REALTY_FAV_ADDED') + '</span>');*/
		if (type == 'add')
		{
			var msgIdx = 'CITRUS_REALTY_FAV_REMOVE';
	        if ($element.parents('.case-add').length)
	        	msgIdx = 'CITRUS_REALTY_FAV_REMOVE_FROM';
			$element.addClass('added').html(BX.message(msgIdx)).attr('title', BX.message('CITRUS_REALTY_FAV_REMOVE_TITLE'));
		}
		else
		{
			var msgIdx = 'CITRUS_REALTY_2FAV';
	        if ($element.parents('.case-add').length)
	        	msgIdx = 'CITRUS_REALTY_ADD_TO_FAV';
			$element.removeClass('added').html(BX.message(msgIdx)).removeAttr('title');
		}
    }
    $('.add2favourites[data-id]').on("click", function (e) {
        e.preventDefault();

        var $this = $(this),
            id = $this.data('id'),
            type = $this.hasClass('added') ? 'remove' : 'add',
            flyingObject = $(this).parents('.td-price').siblings('.td-img').find('img'),
            cart = $('.realty-favourites');

        if (id <= 0)
            return;

        if (flyingObject.length <= 0)
            flyingObject = $this;
        var btPos = getPosition(flyingObject),
            //cartPos = getCenterPos(flyingObject, cart),
            cartPos = getPosition(cart);

		var cssX = cartPos.x, cssY = cartPos.y,
			animateX = btPos.x, animateY = btPos.y,
			animateWidth = flyingObject.width();
		if (type == 'add')
		{
			cssX = btPos.x; cssY = btPos.y;
			animateX = cartPos.x; animateY = cartPos.y;
			animateWidth = 150;
		}
        
        $(flyingObject)
            .clone(true)
            .appendTo('body')
            .css({'top': cssY + 'px', 'position': 'absolute', 'left': cssX + 'px', 'opacity': 0.75, 'white-space': 'nowrap'})
            .animate({
                width: animateWidth + 'px',
                top: animateY + 'px',
                left: animateX + 'px'
            }, 700).fadeOut(500, function () {
                $(this).remove();
            });

        $.getJSON(
            rootFolder + "json.php",
            {
                type: type,
                id: id
            },
            function (data) {
                if (typeof(data) !== 'object')
                    return;
                if (typeof(data.error) !== 'undefined') {
                    alert(data.error);
                }
                else {
                    window.citrusRealtyFavUpdateCount(data.count);
                    window.citrusRealtyMark($this, data.type);
                    if (typeof(data.popup) !== 'undefined') {
                        $.fancybox.open(data.popup, {
                            autoSize: false,
                            fitToView: false,
                            scrolling: 'no',
                            closeBtn: false,
                            width: 750,
                            minHeight: 666,
                            margin: 0,
                            padding: 0,
                            afterShow: popupOnOpen
                        });
                    }
                }
            }
        );
    });

    if ("object" === typeof(window.citrusRealtyFav)) {
        $('a[data-id]').each(function() {
            var $this = $(this),
                id = $this.data('id');
            if (window.citrusRealtyFav.indexOf(id) != -1)
                window.citrusRealtyMark($this, 'add');
        });
    }
});