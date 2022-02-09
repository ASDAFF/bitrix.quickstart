$(function(){
    
    $('.b-catalog-sort__link').bind('click', function(){
        $('.b-catalog-sort__link').each(function(){ 
            $('#' + $(this).data('block')).hide();  
        });
        $('#' + $(this).data('block')).show();
        return false;   
    });
    
 
    $('.b-basket-link__del').bind('click', function(){
        id = $(this).data('id');
        $.ajax({
            url: '/api/',
            data: {'action': 'delete_from_cart', 
                   'id': id }, 
            success: function(){
                location.reload();
            }
        });
        return false;
    });
 
    $('.delay_').bind('click', function(){
        id = $(this).data('id');
        $.ajax({
            url: '/api/',
            data: {'action': 'cart_delay', 
                   'id': id }, 
            success: function(){
                location.reload();
            }
        });
        return false; 
    });
 
    $('.undelay_').bind('click', function(){
        id = $(this).data('id');
        $.ajax({
            url: '/api/',
            data: {'action': 'cart_undelay',   'id': id }, 
            success: function(){ 
                location.reload();
            }
        });
        return false;  
    });
 
 
    $('.m-inc').bind('click', function(){
        id = $(this).data('id');
        input = $(this).parent().find('.b-basket-item-count__text');
        val = parseInt(input.val());
        val++;
        $.ajax({
            url: '/api/',
            data: {'action': 'cart_change_count',   'id': id , 'count': val}, 
            success: function(){ 
                location.reload();
            }
        });
        return false;
    });
    
    $('.m-dec').bind('click', function(){
        id = $(this).data('id');
        input = $(this).parent().find('.b-basket-item-count__text');
        val = parseInt(input.val());
        if(val > 0)
            val--;
        $.ajax({
            url: '/api/',
            data: {'action': 'cart_change_count',   'id': id , 'count': val}, 
            success: function(){ 
                location.reload();
            }
        });
        return false;
    }); 

});