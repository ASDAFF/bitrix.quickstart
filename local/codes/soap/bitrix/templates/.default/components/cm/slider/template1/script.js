$(function(){
    $('.b-button__fast').live('click', function(){
 
       $('.b-fast_order').remove();
 
       $(this).parent()
              .parent() 
              .append('<div class="b-fast_order m-detail-fast_order fust_order"><form method="post" name="fust_order" action="/includes/fust_order.php"><input type="text" name="phone" placeholder="" class="b-cart-field__input"><input type="hidden" value="" name="order"><div class="b-fast_order__text">Быстрый заказ</div><input type="submit" el="'+$(this).attr('el')+'" value="OK" id="fust_order-submit" class="b-button__fast m-fast_order"></form></div>');
 
       $('.b-fast_order').show() 
                         .css('top','154px') 
                         .css('left','70px')
                         .css('z-index','5'); 
 
       return false;
    });
});  