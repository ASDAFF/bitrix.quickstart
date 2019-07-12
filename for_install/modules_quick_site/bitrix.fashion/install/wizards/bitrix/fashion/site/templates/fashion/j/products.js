$(document).ready(function(){
    $("#price-min").val($(".price-range").slider("values",0));
    $("#price-max").val($(".price-range").slider("values",1));

    $(".catalog .item").mouseenter(function(){
        $(this).find(".hover").show();
    }).mouseleave(function(){
        $(this).find(".hover").hide();
    });
    
    $("#price-min").change(function(){
        $(".price-range").slider("values",0,$("#price-min").val());
    });

    $("#price-max").change(function(){
        $(".price-range").slider("values",1,$("#price-max").val());
    });

    $(".page-limit-select").selectBox();

    $(".hide-label").focus(function(){
        $(this).prev().hide();
    }).blur(function(){
        if(!$(this).val())
            $(this).prev().show();
    }).each(function(){
        if(!$(this).val())
            $(this).prev().show();
    });
    
    $("#footer-nav > li").hover(function(){
        if(!$(this).hasClass("active-state")) {
            $(this).parents("ul").find("li").removeClass("active-state");
        }
        $(this).toggleClass("active-state");
    });
    
    $(".categories-link a").click(function(){
        $("#categories").toggle();
        return false;
    });


    width = $("#categories ul.sub-menu").width() * $("#categories ul.menu>li").length;
    var cat_width = $("#categories").width()-180;
    
    if(width<cat_width) {
        $("#categories").css("width",width+125);
        $("#categories .nav").hide();
        $("#categories .cut").addClass("no-nav");
    }
    width = width - $(".categories-full .cut").width();
    
    $("#categories").hide().css("visibility","visible");

    var factor = 2.5;
    var elapsed = width * factor;

    $("#categories .prev").mouseenter ( function () {
            $("#categories ul.menu").stop(true);
            indent = - parseInt ( $("#categories ul.menu").css("margin-left") );
            $("#categories ul.menu").animate({ marginLeft: "26px"}, elapsed - ( width - indent ) * factor );
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

    $(".auth span").click(function(){
        $("#auth").show();
    });
    
    
    $("html").click(function(){
        $("#categories").hide();
        $("#header .menu > ul > li").removeClass("selected");
    });
    
    $("#header .menu li:not(.categories-link)").hover(function() {
            $(this).addClass("selected");
        }, function() {
            $(this).removeClass("selected");
    });
    
    $("#cart-confirm .close").click(function(){
        $("#cart-confirm").hide();
        $("#overlay").hide();
        return false;
    });
	
	$("#cart-confirm-set .close").click(function(){
        $("#cart-confirm-set").hide();
        $("#overlay").hide();
        return false;
    });
    
    $("#message .close").click(function(){
        $("#message").hide();
        $("#overlay").hide();
        return false;
    })
    
    $(".review-count").click(function(){
        $.scrollTo($(this).attr("href"),700);
        return false;
    });
    
    // making selectable list
    $(".selectable li").bind({
        mouseup: function(e){
            var t = $(this);
            t.parent().find("li").removeClass("selected");
            t.addClass("active selected");
        },
        mouseover: function(){
            var t = $(this);
            t.parent().find("li").removeClass("active");
            t.addClass("active");
        },
        mouseout: function(){
            var t = $(this);
            t.parent().find("li").removeClass("active");
            t.parent().find(".selected").addClass("active");
        }
    });
    
    
    // changing data on color select
    $("#color a").bind({
        click: function(){
            hide_box();
            var t = $(this),
                color = t.attr("rel");
            
            $("#sku [rel="+color+"]").show();
            $("#size [rel="+color+"]").show();
            $("#size li").removeClass("active selected");
            var size = $("#size [rel="+color+"]").show().eq(0).addClass("active selected").find("span").attr("class");
            $("#"+color+"-"+size).show();
            $("#thumbs li").removeClass("active selected");
            var arLi = $("#thumbs [rel="+color+"]");
            arLi.show().eq(0).addClass("active selected").find("a").trigger("click");
            if (arLi.length < 2) {
                arLi.hide();
            }
            hide_show_video("hide");
			$(".wishlist").attr("product-id", size);
            checkWishList();
			$(".change-set").hide();
            $("#change-set"+size).show();
            return false;
        }
    });
    
    // changing data on size select
    $("#size span").bind({
        click: function(){
            $(".price").hide();
            
            var t = $(this),
                color = $("#color .selected a").attr("rel"),
                size = t.attr("class");
            
            $("#"+color+"-"+size).show();
			$(".wishlist").attr("product-id", size);
            checkWishList();
			$(".change-set").hide();
            $("#change-set"+size).show();
        }
    });
    
    // switch to video
    function hide_show_video (e) {
        if (e === "hide" && $("#video-tab:visible").html()) {
            $("#video-tab").hide();
            $(".shortcuts").show();
            $(".big-image").show();
        }
        if (e === "show") {
            $("#video-tab").show();
            $(".shortcuts").hide();
            $(".big-image").hide();
        }
    }
    
    $("#video a").click(function(){
        hide_show_video("show");
        return false;
    });
    
    $("#thumbs li").bind({
        mouseup: function(e){
            e.stopPropagation();
            hide_show_video("hide");
        }
    });
    
    function hide_box () {
        $(".price").hide();
        $("#sku span").hide();
        $("#size li").hide();
        $("#thumbs li").hide();
    }
});