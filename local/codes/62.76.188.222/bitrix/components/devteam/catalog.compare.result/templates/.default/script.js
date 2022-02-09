$(document).ready(function() {
    maxHeight("b-compare__image");
    maxHeight("b-compare__link");
    $(".b-compare-header__checkbox").height($(".b-compare-header__product").height());

    $("#scrollbarX").tinyscrollbar({
        axis: "x",
        scroll: false
    });
 
    $("#b-compare__table").height($("#b-compare__table").children().height());
    
    $('.b-compare__delete').bind('click', function(){
        
        id = $(this).data('id');
        
        location.href="?remove=" + id;
    })
	
	var compare_header_height = $(".b-compare-header__wrapper").height();
	$(".b-compare-header__wrapper").height(compare_header_height);
});

var maxHeight = function(classname) {
    var divs = $("." + classname);
    var max = 0;

    divs.each(function() {
        max = Math.max(max, $(this).height());
    });
    divs.css("height", max);

    return max;
}