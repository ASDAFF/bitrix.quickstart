$(function(){

//    searchInputValueToggle
    $(".search input").focus(function(){if(this.value==this.defaultValue)this.value='';}).bind('blur',function(){ if(this.value=='')this.value=this.defaultValue;});

//    .nav secondLevel li for ie7
    if ($.browser.msie && $.browser.version <= 7) {
        $('.nav > li').mouseenter(function(){
            var w = $(this).find('.r-border-shape').innerWidth() - parseInt($(this).find('.secondLevel').css('padding-left')) - parseInt($(this).find('.secondLevel').css('padding-right')) - parseInt($(this).find('.secondLevel li').css('padding-right'));
            $(this).find('.secondLevel li').css({'width': w})
        })
    }
})

