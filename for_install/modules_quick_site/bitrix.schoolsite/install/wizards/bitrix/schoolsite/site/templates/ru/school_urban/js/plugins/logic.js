$(function(){
//    searchInputValueToggle
    $(".search input").focus(function(){if(this.value==this.defaultValue)this.value='';}).bind('blur',function(){ if(this.value=='')this.value=this.defaultValue;});

})