$(document).ready(function(){

  var extOwlOptions = {
      dots: false,
      items: 2,
      margin: 8,
      stagePadding: 16,
      responsive: {}
    };

  extOwlOptions.responsive[appSLine.grid.xs] = {
    items: 3
  };

  extOwlOptions.responsive[appSLine.grid.sm] = {
    items: 4
  };

  extOwlOptions.responsive[appSLine.grid.md] = {
    items: 5
  };

  extOwlOptions.responsive[appSLine.grid.lg] = {
    items: 6
  };
  
  $('.js-carousel_brands').owlCarousel($.extend({}, appSLine.owlOptions, extOwlOptions));
});