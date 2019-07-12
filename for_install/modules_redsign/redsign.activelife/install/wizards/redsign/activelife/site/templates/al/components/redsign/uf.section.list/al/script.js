$(document).ready(function(){

  var extOwlOptions = {
      dots: false,
      items: 3,
      margin: 8,
      stagePadding: 16,
      responsive: {}
    };

  extOwlOptions.responsive[appSLine.grid.xs] = {
    items: 4
  };
  extOwlOptions.responsive[appSLine.grid.sm] = {
    items: 5
  };
  extOwlOptions.responsive[appSLine.grid.md] = {
    items: 7
  };
  extOwlOptions.responsive[appSLine.grid.lg] = {
    items: 9
  };

  $('.js-carousel_section').owlCarousel($.extend({}, appSLine.owlOptions, extOwlOptions));

});