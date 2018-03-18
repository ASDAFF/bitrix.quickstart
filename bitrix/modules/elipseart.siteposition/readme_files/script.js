function AddComentFormShow()
{
	BX('add_comment_form').style.display = 'block';
	BX('module_COMMENT').focus();
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
                                if ($(".screenshot-next").hasClass('disabled')) {
                                    var t = $(this);
                                    $("#scrollable-screenshot ul").animate({left: -((t.find("li").width() + 10) * (t.find("li").length - 3) + 82)}, 300);
                                }
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
                                    $("#scrollable ul").animate({left: -((t.find("li").width() + 20) * (t.find("li").length - 3) + 105)}, 300);
                                }
                            }
                        });
                    });