$(document).ready(function() {
    "use strict";
    var templateId = $(".js-sorterajax").data("catalog-template");
    
    $("body").on("click", ".js-sorterajax a:not(.notajax)", function(e) {
        var $template = $("#" + templateId);
      
        if($template.length > 0) {
            e.preventDefault();
            
            RSMONOPOLY_Area2Darken($template);
            var url = $(this).attr("href") + "&AJAX_CALL=Y&action=UPDATE_ITEMS";
            
            $.post(url, {})
              .success(function(json) {
                json = BX.parseJSON(json);
                $template.html(json[templateId]);
                $("#" + templateId + "_sorter").html(json[templateId+"_sorter"]);
                RSMONOPOLY_SetSet();
              })
              .always(function() {
                RSMONOPOLY_Area2Darken($template);
              });
        }
      
    });
	
	
});