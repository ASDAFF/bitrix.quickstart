// jquery.rs.active-class v1.0
(function(b){b.rsProto._j4=function(){var c,a=this;if(a.st.addActiveClass){a.ev.on("rsBeforeMove",function(){b()});a.ev.on("rsAfterInit",function(){b()});var b=function(){c&&clearTimeout(c);c=setTimeout(function(){a._a4&&a._a4.removeClass("rsActiveSlide");a._d1&&a._d1.addClass("rsActiveSlide");c=null},50)}}};b.rsModules.activeClass=b.rsProto._j4})(jQuery);
