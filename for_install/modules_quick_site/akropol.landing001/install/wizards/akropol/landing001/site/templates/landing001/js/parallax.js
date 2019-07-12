//////////////////////////////////////////////////////  //
//    PARALLAX EFFECT DISABLED ON MOBILE                //
//////////////////////////////////////////////////////  // 

if (document.documentElement.clientWidth > 991) {
(function(){
"use strict";

  var parallax = document.querySelectorAll(".parallax"),
      speed = -0.15;

  window.onscroll = function(){
    [].slice.call(parallax).forEach(function(el,i){

      var windowYOffset = window.pageYOffset,
          elBackgrounPos = "0 " + (windowYOffset * speed) + "px";
      
      el.style.backgroundPosition = elBackgrounPos;

    });
  };

})();
}