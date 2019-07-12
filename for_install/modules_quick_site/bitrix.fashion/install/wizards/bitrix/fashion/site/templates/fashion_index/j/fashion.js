jQuery.extend( jQuery.easing, {
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    }
});

$(document).ready(function(){
    if (($.browser.webkit || $.browser.mozilla) && document.readyState != "complete"){
            setTimeout( arguments.callee, 100 );
            return;
    }
    
    $("#slides").tabs({
        fx: { opacity: 'toggle', duration: 1000, easing: 'easeInOutCubic' },
        create: function(event,ui) {
            doResize();
            $("#slides div.noscript").removeClass("noscript");
        }
    }).tabs("rotate", 3000, true);
    
    function doResize() {
        $("#slides img").css("width","100%").css("height","auto");
        if($("#slides div:visible img").prop("clientHeight") < $("body").height()) {
            $("#slides img").each(function(){
                $(this).css("width","auto").css("height",$("body").height());
            });
        }
    };
    
    var resizeTimer = null;
    $(window).bind('resize', function() {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(doResize, 100);
    });
    
    $("#footer-nav li").hover(function(){
        $(this).addClass("active-state");
    }, function(){
        $(this).removeClass("active-state");
    });
    
    $(".hide-label").focus(function(){
        $(this).prev().hide();
    }).blur(function(){
        if(!$(this).val())
            $(this).prev().show();
    }).each(function(){
        if(!$(this).val())
            $(this).prev().show();
    });
    
    $(".categories-link a").click(function(){
        $("#categories").toggle();
        
        return false;
    });
    
    $(".auth span").click(function(){
        $("#auth").show();
    });
    
    width = $("#categories ul.sub-menu").width() * $("#categories ul.menu>li").length;
    var cat_width = $("#categories").width()-180;
    
    if(width<cat_width) {
        $("#categories").css('width',width+70);
        $("#categories .nav").hide();
        $("#categories .cut").addClass("no-nav");
    }
    width -= $("#content .categories-full .cut").width();
    
    $("#categories").hide().css("visibility","visible");

    var factor = 2.5;
    var elapsed = width * factor;

    $("#categories .prev").mouseenter ( function () {
        $("#categories ul.menu").stop(true);
        indent = - parseInt ( $("#categories ul.menu").css("margin-left") );
        $("#categories ul.menu").animate({ marginLeft: '26px'}, elapsed - ( width - indent ) * factor );
    }).mouseleave ( function () {
        $("#categories ul.menu").stop(true);
    }).click(function(){
        return false;
    });
     
    $("#categories .next").mouseenter ( function () {
            $("#categories ul").stop(true);
            indent = - parseInt ( $("#categories ul.menu").css("margin-left") );
            $("#categories ul.menu").animate({ marginLeft: -width}, ( width - indent ) * factor );
    }).mouseleave ( function () {
            $("#categories ul.menu").stop(true);
    }).click(function(){
        return false;
    });

    $("#auth .close").click(function(){
        $("#auth").hide();
        return false;
    });
    
    $("html").click(function(){
        $("#categories").hide();
    });
});