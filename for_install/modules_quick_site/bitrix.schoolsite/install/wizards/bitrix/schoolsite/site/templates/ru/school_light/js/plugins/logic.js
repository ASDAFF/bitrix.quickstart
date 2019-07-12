$(function(){
//    searchInputValueToggle
    $(".search input").focus(function(){if(this.value==this.defaultValue)this.value='';}).bind('blur',function(){ if(this.value=='')this.value=this.defaultValue;});

//    sideNav toggle

    $('.contain > a').click(function(){
        $(this).siblings('.level').slideToggle(150).end().parent().toggleClass('current');
        $(this).toggleClass('current');
        return false;
    })

})