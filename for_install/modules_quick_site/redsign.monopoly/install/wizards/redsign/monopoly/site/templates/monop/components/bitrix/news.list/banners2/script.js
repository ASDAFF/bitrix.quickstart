;(function($) {
  
  $(document).ready(function() {
    
      var $mainBanners = $(".js-mainbanners"),
        $mainBannersItems = $mainBanners.find(".js-mainbanners_items"),
        $preload = $mainBanners.find(".js-mainbanners_preloader");
        
      var owlConfig = {
        items: 1,
            margin: 0,
            autoplay: false,
            autoplaySpeed: 2000,
            autoplayTimeout: 8000,
            smartSpeed: 2000,
            onInitialized: function() {
              moveNav(this.$element);
            },
            onResized: function() {
                moveNav(this.$element);
            }
      };
        
      loadImages()
        .then(function() {
          $preload.hide();
          
          owlInit($mainBannersItems, owlConfig);
          
          $mainBannersItems.show();
                $mainBanners.find(".js-mainbanners_sidebanners").addClass("js-show");
        });
      
      function loadImages() {
        var images = [],
          promises = [];
        
        $(".js-mainbanners_image").each(function(key, item) {
          images.push($(item).data("img-src"));
        });
            
        images.forEach(function(image) {
          
                if(!image) {
                    return;
                }
          
          promises.push(
            $.Deferred(function(promise) {
              $('<img>')
                .attr('src', image)
                .load(function() {
                  promise.resolve();
                });
            })
          )
        });
        
        return $.when.apply( $, promises );
      }
      
      function moveNav($owl) {
        var carouselHeight = $owl.find('.owl-stage-outer').height();
        $owl.find('.owl-nav').find('div').css('bottom', (Math.round(carouselHeight/2)-27)+'px');
      }
  });
	
})($);