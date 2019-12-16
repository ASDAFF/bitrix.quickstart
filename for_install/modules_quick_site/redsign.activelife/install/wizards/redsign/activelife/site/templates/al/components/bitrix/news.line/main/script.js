$(document).ready(function(){

  var newslineUpdate = function(options) {

    var extOwlOptions = {
        dots: false,
        nav: false,
        items: 2,
        margin: 8,
        stagePadding: 18,
        responsive: {}
      };
    
    if (appSLine.pageWidth < appSLine.grid.md) {

      extOwlOptions.responsive[appSLine.grid.sm] = {
        items: 3
      };

      extOwlOptions.responsive[appSLine.grid.md] = {
        items: 4
      };

      extOwlOptions.responsive[appSLine.grid.lg] = {
        items: 5
      };
      
      $('.js-newsline').owlCarousel($.extend({}, appSLine.owlOptions, extOwlOptions));

    } else {
      $('.js-newsline.owl-carousel').trigger('destroy.owl.carousel').removeClass('owl-carousel').removeAttr('style');
    }
  }

  newslineUpdate();

  $(window).resize(function(){
    newslineUpdate();
  });

});