$(document).ready(function() {

  $('.menu_vml, .menu_vml__sub').each(function(){

    $(this).menuAim({
      activate: function activateSubmenu(row) {
        var $row = $(row);
        $row.addClass("is-hover");
      },
      deactivate: function deactivateSubmenu(row) {
        var $row = $(row);
        $row.removeClass("is-hover");
      },
      exitMenu: function(menu){
          $(menu.activeRow).add(
            $(menu.activeRow).find('is-hover')
          ).removeClass("is-hover");
          menu.activeRow = null;
      },
    });
  });
  
});