$(document).ready(function() {

    $(document).on("mouseenter", ".mainmenu__root-item", function() {
        var $submenu = $(this).find(".dropdown-submenu"),
            blockWidth = 0;

        if($submenu.length === 0) return;

        if($submenu.find(".mainmenu__linecolumns").length > 0) {
            blockWidth += $submenu.find(".mainmenu__linecolumns").outerWidth();
        }

        $.each($submenu.children(".js-mainmenu__column:visible"), function(index, item) {
           blockWidth += $(item).outerWidth();
        });

        if($submenu.position().left + blockWidth + 5 > $(".mainJS").width()) {
            $submenu.css({
                right: 0,
            });
        }
    });

    $(document).on("mouseleave", ".mainmenu__root-item", function() {
        $(this).find(".dropdown-submenu").css({
            right: 'auto',
        });
    });

});
