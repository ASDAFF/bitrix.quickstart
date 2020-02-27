$(function(){

  // Fixing Webkit that not clearing input/textarea when get focus
  $('input, textarea').on('focus',function(){
      if ( $(this).attr('placeholder') ) $(this).data('placeholder', $(this).attr('placeholder')).removeAttr('placeholder');
    }).on('blur', function(){
        if ( $(this).data('placeholder') ) $(this).attr('placeholder', $(this).data('placeholder')).removeData('placeholder');
    });


  // текущий рейтинг
  var rate = $('.rating-stars_active').data('rate');

  // выбрали новое значение
  $('.rating-stars_active .rating-stars__star_active').on('click', function() {
    rate = $(this).index()+1;
  });

  // убрали курсор с блока рейтинга
  $('.rating-stars_active').on('mouseleave', function() {
    $(this).attr('data-rate', rate);
  });

  // заполняем звездочки при наведении
  $('.rating-stars_active .rating-stars__star_active').on('mouseleave, mouseenter', function() {
    var rating_hover = $(this).index()+1;
    $(this).parent('.rating-stars_active').attr('data-rate', rating_hover);
  });

  
});


