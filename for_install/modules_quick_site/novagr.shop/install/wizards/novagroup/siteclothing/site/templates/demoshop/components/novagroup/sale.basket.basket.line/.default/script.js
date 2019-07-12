
    function UpdateBasketAfterLoadOrderList()
    {
        $.get(JAVASCRIPT_SITE_DIR+"include/ajax/basket.php", function(data) {
            $('#cart_line_1').html($(data).html());
            $('.hide-1').click(function () {
                $(this).siblings(".list-basket").slideToggle("slow");
                $('#slider5').tinycarousel({ axis: 'y' });
                return false;
            });
        });
    }
    function UpdateBasketCatalog()
    {
        $.get(JAVASCRIPT_SITE_DIR+"include/catalog/element/basket.php", function(data) {
            $('#cart_line_12').html($(data).html());
        });
    }