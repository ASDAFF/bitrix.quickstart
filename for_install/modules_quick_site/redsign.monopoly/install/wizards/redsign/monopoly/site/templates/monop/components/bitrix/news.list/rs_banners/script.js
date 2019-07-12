$(document).ready(function() {
  
    var options = $(".js-banner-options"),
        $jsBanners = $(".js-banners");
    
    var isAutoAdjustHeight = false;
    if($(".rs-banners .rs-banners_banner:eq(0)").height() == 0) {
        isAutoAdjustHeight = true;
    }
    
    $jsBanners.redsignBanners({
        sliderAdapter: "owlAdapter",
        isAutoAdjustHeight: options.data('isAutoAdjustHeight') !== undefined ? options.data('isAutoAdjustHeight') : false,
        isAdjustBlocks: true,
        height:  options.data('height') !== undefined ? options.data('height') : 400,
        params: {
            items: 1,
            loop: $(".rs-banners .rs-banners_banner").length > 1 ? true : false,
            nav: true,
            dots: true,
            video: true,
            autoplay: options.data('autoplay') !== undefined ? options.data('autoplay') : true,
            autoplayTimeout:  options.data('autoplayTimeout') !== undefined ? options.data('autoplayTimeout') : 7000,
            autoplaySpeed:  options.data('autoplaySpeed') !== undefined ? options.data('autoplaySpeed') : 2000,
            smartSpeed:  options.data('autoplaySpeed') !== undefined ? options.data('autoplaySpeed') : 2000,
        }
    });
    
    if(isAutoAdjustHeight) {
        $(".js-preloader").css(
          "height", 
          $jsBanners.data("plugin_redsignBanners").getAdjustHeight()
        );
    }
    
    $jsBanners.on("rs.banners.images:load", function() {
      
        setTimeout(function() {
            $(".js-preloader").hide();
            $jsBanners.show();
            $(".js-sidebanners.js-sidebanners_selected").show();
        }, 30)
        
        setTimeout(function() {
            $(".js-mainbanners-container").css("opacity", 1);
        }, 250);
        
    });
    
    $(".js-banners").on("rs.banners.adjustblocks", function() {
        $(".rs-banners_infowrap").css("opacity", 1);
    });
});