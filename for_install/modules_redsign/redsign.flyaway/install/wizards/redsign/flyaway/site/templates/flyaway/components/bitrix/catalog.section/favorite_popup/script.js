;(function ($, window, document) {
    'use strict';
  
    $(document).ready(function() {

        $(document).on("click", "#inheadfavorite", function(e) {

            if($(window).width < 740) {
                return true;
            }

            e.preventDefault();
            $("#dropdown_favorite").show();

            $(document).on("click.dropdown-favorite", function(e) {
                if(!$(e.target).parents().andSelf().is("#dropdown_favorite")){
                    $("#dropdown_favorite").hide();
                    $(document).off("click.dropdown-favorite");
                }
            });
        });
    });
    
    $(document).on("updateFavorite.rs.flyaway", function() {
      
        $.ajax({
            dataType: 'html',
            data: {
                action: "favoriteinhead_update"
            },
            success: function(data) {
                $("#dropdown_favorite").html(data);
            },
            error: function() {
                console.error("Favorite popup -> no load");
            }
        });
    });
    
}(jQuery, window, document));



