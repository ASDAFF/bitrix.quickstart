
; /* Start:/bitrix/templates/marketplace-1c-bitrix-new/components/bx/marketplace.catalog.detail/v2/script.js*/
function AddComentFormShow()
{
	BX('add_comment_form').style.display = 'block';
	BX('module_COMMENT').focus();
	BX('id_com').value = '';

}

function Answer(id, name)
{
	AddComentFormShow();
	BX('module_COMMENT').value = name+', ';
	BX('id_com').value = id;
}

var CMarketPlace = {
    ModuleDetail:{
	MouseOverRating: function(rating)
	{
	    if(-1 == rating)
	    {
		var RatingField = BX('module_rating').value;
		RatingField = parseInt(RatingField);
		if(!isNaN(RatingField))
			rating = RatingField;
		else
			rating = 0;
	    }

	    for(i=1;i<=rating;i++)
	    {
		var star = BX('vote_star_'+i)
		if(star)
			star.src = '/images/marketplace/mp_star.png';
	    }

	    for(i=5;i>rating;i--)
	    {
		var star = BX('vote_star_'+i)
		if(star)
			star.src = '/images/marketplace/mp_star_na.png';
	    }
	},
        SetRating: function(rating)
        {
	    var RatingField = BX('module_rating');
            if(RatingField)
                RatingField.value = rating;

	    this.MouseOverRating(rating);
        },
        SlideDescription: function(obj, btnlnk)
        {
            if(obj.style.overflow == 'hidden')
            {
		BX('mp-detail-descripiption-fade').style.display = 'none';
                obj.style.height = '';
                obj.style.overflow = 'visible';
                btnlnk.className = 'mp-more-description-btn-close';
            }
            else
            {
		BX('mp-detail-descripiption-fade').style.display = 'block';
                obj.style.height = '100px';
                obj.style.overflow = 'hidden';
                btnlnk.className = 'mp-more-description-btn';
            }
        }
    }
}

var enterUrlWindow = {
    form_window: null,
    form_window_id : "enter-site-url",
    login_field_id : "enterURL",

    ShowLoginForm : function()
    {
            if (!this.form_window)
            {
                    this.form_window = document.getElementById(this.form_window_id);
                    if (!this.form_window)
                            return false;

                    try {document.body.appendChild(this.form_window);}
                    catch (e){}
            }

		authFormWindow.CreateOverlay();
		if(authFormWindow.overlay)
			authFormWindow.overlay.onclick = function() {enterUrlWindow.CloseLoginForm()};
            this.form_window.style.display = "block";

            var res = jsUtils.GetWindowSize();
            this.form_window.style.top = parseInt(res['scrollTop'] + this.form_window.offsetHeight) + 'px';

            var loginField = document.getElementById(this.login_field_id);
            if (loginField)
            {
                    loginField.focus();
                    loginField.select();
            }

            return false;
    },

    CloseLoginForm : function()
    {
		authFormWindow.CloseLoginForm();

            if (this.form_window)
                    this.form_window.style.display = "none";
            return false;
    },

    GetWindowScrollSize : function(pDoc)
    {
            var width, height;
            if (!pDoc)
                    pDoc = document;

            if ( (pDoc.compatMode && pDoc.compatMode == "CSS1Compat"))
            {
                    width = pDoc.documentElement.scrollWidth;
                    height = pDoc.documentElement.scrollHeight;
            }
            else
            {
                    if (pDoc.body.scrollHeight > pDoc.body.offsetHeight)
                            height = pDoc.body.scrollHeight;
                    else
                            height = pDoc.body.offsetHeight;

                    if (pDoc.body.scrollWidth > pDoc.body.offsetWidth ||
                            (pDoc.compatMode && pDoc.compatMode == "BackCompat") ||
                            (pDoc.documentElement && !pDoc.documentElement.clientWidth)
                    )
                            width = pDoc.body.scrollWidth;
                    else
                            width = pDoc.body.offsetWidth;
            }
            return {scrollWidth : width, scrollHeight : height};
    }
}

var enterURL1 = '';
function AddModuleEx()
{
        enterU = document.getElementById('enterURL').value;
        var module = document.getElementById('module').value;
        if(enterU)
        {
                var enterURL1 = enterU + "/bitrix/admin/update_system_partner.php?addmodule=#MODULE#";
                enterURL1 = enterURL1.replace("#MODULE#", module);
                window.open(enterURL1);
                enterUrlWindow.CloseLoginForm();
        }
}

/*POP UP*/
/*
var authPreloadImages = ["close.gif"];
for (var imageIndex = 0; imageIndex < authPreloadImages.length; imageIndex++)
{
	var imageObj = new Image();
	imageObj.src = "/bitrix/components/bx/bitrix.image.popup/templates/.default/images/" + authPreloadImages[imageIndex];
}
authPreloadImages = null;
*/

		$(document).ready(function(){
			/*Обработка всплывающих изображений с помощью funcybox */
			$("a.screenshot-image").fancybox({
				'transitionIn': 'elastic',
				'transitionOut': 'elastic'
			});

			$("a.screenshot-video").fancybox({
				'transitionIn': 'elastic',
				'transitionOut': 'elastic',
                                'type': 'inline',
                                'href': '#module-video',
                                'onStart': function(){
                                    $("#module-video").show();
                                },
                                'onClosed': function(){
                                    $("#module-video").hide();
                                }
			});

			$(".bug-report").bind('click', function(){
                            $('#popup').css({top:'50%',left:'50%',margin:'-'+($('#popup').height() / 2)+'px 0 0 -'+($('#popup').width() / 2)+'px'});
				$(".error-message-popup").show();
			});

                        $(document).keyup(function(e){
                            if(e.keyCode === 27)
                                $(".error-message-popup").hide();
                        });

                        $("#popup-close-btn").bind('click', function(){
                            $(".error-message-popup").hide();
                        });

                        $("#scrollable-screenshot").jCarouselLite({
                            btnNext: ".screenshot-next",
                            btnPrev: ".screenshot-prev",
                            mouseWheel: true,
                            circular: false,
                            afterEnd: function() {
//                                if ($(".screenshot-next").hasClass('disabled')) {
//                                    var t = $(this);
//                                    $("#scrollable-screenshot ul").animate({left: -((t.find("li").width() + 10) * (t.find("li").length - 3) + 82)}, 300);
//                                }
                            }
                        });

                        $("#scrollable").jCarouselLite({
                            btnNext: ".solutions-next",
                            btnPrev: ".solutions-prev",
                            mouseWheel: true,
                            circular: false,
                            afterEnd: function() {
                                if ($(".solutions-next").hasClass('disabled')) {
                                    var t = $(this);
//                                    $("#scrollable ul").animate({left: -((t.find("li").width() + 20) * (t.find("li").length - 3) + 105)}, 300);
                                }
                            }
                        });
                    });


function closeListBndl ()
{
    BX('buy_bundle_list').style.display = 'none';
    BX.unbind(document.body, 'click', closeListBndl);
}
function ShowBundleList ()
{
    if (BX('buy_bundle_list').style.display == 'none')
    {
        BX('buy_bundle_list').style.display = 'block';
        BX.defer(BX.bind)(document.body, 'click', closeListBndl);
        BX.bind(BX('open_list'), 'click', BX.eventCancelBubble);
        BX.bind(BX('buy_bundle_list'), 'click', BX.eventCancelBubble);
    }
    else
    {
        BX('buy_bundle_list').style.display = 'none';
    }
}

BX.ready(function(){
	var buy_bndl_btn = BX('buy_bndl_btn');
	var bndl_url = buy_bndl_btn.href;
	var chosed_edition = BX('chosed_edition');
	var list = {};
	var total_bndl_sum = BX('total_bndl_sum');
	var total_bndl_sum_old = BX('total_bndl_sum_old');
	chose_edit = BX('chose_edit');

	var editions = BX.findChildren(chose_edit, {tag: 'LI'}, true);
	for (var i = 0; i < editions.length; i++)
	{
		BX.bind(editions[i], 'click', function(e){
			BX.PreventDefault(e);
			var link = BX.findChild(this, {tag: 'A'}, true);
			var addlink = link.rel;
			buy_bndl_btn.href = bndl_url.replace(/&PRD=[0-9]+/, '')+addlink;
			chosed_edition.innerHTML = this.innerHTML;
			BX.removeClass(BX.findChild(chosed_edition, {class: 'mp_order_release_title'}, true), 'noarrow');
			BX.toggleClass(list, 'active');
			total_bndl_sum.innerHTML = bndl_price[link.id];
			if (total_bndl_sum_old)
				total_bndl_sum_old.innerHTML = bndl_price[link.id+'old'];
		})
	}
	BX.bind(chose_edit, 'click', function(e){
		BX.PreventDefault(e);
		list = BX.findChild(this, {tag: 'UL'}, true);
		BX.toggleClass(list, 'active');
	})

	BX.bind(document.body, 'click', function(){
		BX.removeClass(list, 'active');
	})
})


/* End */
;
; /* Start:/bitrix/templates/marketplace-1c-bitrix-new/components/bitrix/main.share/big_icons/script.js*/
function ShowShareDialog(counter)
{
	var div = document.getElementById("share-dialog"+counter);
	if (!div)
		return;

	if (div.style.display == "block")
	{
		div.style.display = "none";
	}
	else
	{
		div.style.display = "block";
	}
	return false;
}

function CloseShareDialog(counter)
{
	var div = document.getElementById("share-dialog"+counter);

	if (!div)
		return;

	div.style.display = "none";
	return false;
}

function __function_exists(function_name) 
{
	if (typeof function_name == 'string')
	{
		return (typeof window[function_name] == 'function');
	} 
	else
	{
		return (function_name instanceof Function);
	}
}

/* End */
;
; /* Start:/bitrix/templates/marketplace-1c-bitrix-new/components/bx/marketplace.catalog.list/v2_detail_slider_new/script.js*/
function SliderElements (elements_id, minPos, maxPos, offsetPos) {
    var changed = false;
    var sliding = false;
    var prevSlide = 0;
    var nextSlide = 1;
    var slides = BX.findChildren(BX(elements_id), {tag: 'UL'});
    this.slide_move_left = function () { // right click
	    BX('mtop-sl').style.overflow = 'hidden';
	    var startPos = parseInt(BX(elements_id).style.left.replace('%', ''), 10);
        if (sliding)
            return;

        nextSlide = prevSlide + 1;
        if (nextSlide == slides.length)
            return;

        changed = false;
        sliding = true;
        var endPos = startPos - offsetPos;
        slides[nextSlide].style.opacity = 1;
        (new BX.easing({
            duration : 1000,
            start:{left: startPos},
            finish:{left: endPos},
            transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
            step : function(state){
                BX(elements_id).style.left = state.left+'%';
                if (state.left == endPos && !changed)
                {
                    slides[prevSlide].style.opacity = 0;
                    changed = true;
                }
            },
            complete: function () {
                prevSlide = nextSlide;
                sliding = false;
	            BX('mtop-sl').style.overflow = 'visible';
            }
        })).animate();
    }

    this.slide_move_right = function () {
	    BX('mtop-sl').style.overflow = 'hidden';
        var startPos = parseInt(BX(elements_id).style.left.replace('%', ''), 10);
        if (sliding)
            return;

        nextSlide = prevSlide - 1;
        if (nextSlide == -1)
            return;

        changed = false;
        sliding = true;
        var endPos = startPos + offsetPos;
        slides[nextSlide].style.opacity = 1;
        (new BX.easing({
            duration : 1000,
            start:{left: startPos},
            finish:{left: endPos},
            transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
            step : function(state){
                BX(elements_id).style.left = state.left+'%';
                if (state.left == endPos && !changed)
                {
                    slides[prevSlide].style.opacity = 0;
                    changed = true;
                }
            },
            complete: function () {
                prevSlide = nextSlide;
                sliding = false;
	            BX('mtop-sl').style.overflow = 'visible';
            }
        })).animate();
    }
}
/* End */
;
; /* Start:/bitrix/components/bx/marketplace.rating/templates/.default/script.js*/
function AddRatingFormShow(e)
{
	BX('point_select').style.visibility = 'visible';
	BX('rating_comment').style.display = 'block';
	BX('rating_COMMENT').focus();
}

var selectBox = function(e) {
	BX.PreventDefault(e);
	var el = BX.nextSibling(this);
	if (BX.hasClass(el, 'active'))
	{
		BX.removeClass(BX.nextSibling(this), 'active');
	}
	else
	{
		BX.addClass(BX.nextSibling(this), 'active');
	}
}

BX.ready(function() {
	BX.bind(document.body, 'click', function() {
		var selectBoxes = BX.findChildren(BX('rat_form'), {class: 'wa_popup_input'}, true);
		for (var i = 0; i < selectBoxes.length; i++)
		{
			BX.removeClass(selectBoxes[i], 'active');
		}
	});

	var rat_point = BX('rat_point');
	var rat_version = BX('rat_version');
	BX.bind(rat_point, 'click', selectBox);
	BX.bind(rat_version, 'click', selectBox);

	var balls = BX.findChildren(BX.nextSibling(rat_point), {tag: 'A', class: 'wa_ppp_link'}, true);
	for (var i = 0; i < balls.length; i++)
	{
		BX.bind(balls[i], 'click', function(e) {
			BX.PreventDefault(e);
			console.log(this);
			console.log(BX.findChild(this, {tagName: 'SPAN'}).innerHTML);
			BX('point').value = BX.findChild(this, {tag: 'SPAN'}).innerHTML;
			var text = this.innerHTML.split('</span> ')
			rat_point.innerHTML = text[1];
			rat_point.click();
		})
	}

	/*
	var vers = BX.findChildren(BX.nextSibling(rat_version), {tag: 'A', class: 'wa_ppp_link'}, true);
	for (var i = 0; i < vers.length; i++)
	{
		BX.bind(vers[i], 'click', function(e) {
			BX.PreventDefault(e);
			BX('ver').value = this.innerText;
			rat_version.innerHTML = BX('ver').value;
			rat_version.click();
		})
	}
	*/
});
/* End */
;; /* /bitrix/templates/marketplace-1c-bitrix-new/components/bx/marketplace.catalog.detail/v2/script.js*/
; /* /bitrix/templates/marketplace-1c-bitrix-new/components/bitrix/main.share/big_icons/script.js*/
; /* /bitrix/templates/marketplace-1c-bitrix-new/components/bx/marketplace.catalog.list/v2_detail_slider_new/script.js*/
; /* /bitrix/components/bx/marketplace.rating/templates/.default/script.js*/
