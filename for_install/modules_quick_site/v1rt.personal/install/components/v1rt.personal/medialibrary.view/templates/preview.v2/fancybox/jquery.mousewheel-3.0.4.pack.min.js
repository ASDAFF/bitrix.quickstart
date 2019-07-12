/*! Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
* Licensed under the MIT License (LICENSE.txt).
*
* Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
* Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
* Thanks to: Seamus Leahy for adding deltaX and deltaY
*
* Version: 3.0.4
*
* Requires: 1.2.2+
*/
(function(c){function a(f){var d=f||window.event,g=[].slice.call(arguments,1),l=0,j=0,k=0;f=c.event.fix(d);f.type="mousewheel";if(f.wheelDelta){l=f.wheelDelta/120}if(f.detail){l=-f.detail/3}k=l;if(d.axis!==undefined&&d.axis===d.HORIZONTAL_AXIS){k=0;j=-1*l}if(d.wheelDeltaY!==undefined){k=d.wheelDeltaY/120}if(d.wheelDeltaX!==undefined){j=-1*d.wheelDeltaX/120}g.unshift(f,l,j,k);return c.event.handle.apply(this,g)}var b=["DOMMouseScroll","mousewheel"];c.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var d=b.length;d;){this.addEventListener(b[--d],a,false)}}else{this.onmousewheel=a}},teardown:function(){if(this.removeEventListener){for(var d=b.length;d;){this.removeEventListener(b[--d],a,false)}}else{this.onmousewheel=null}}};c.fn.extend({mousewheel:function(d){return d?this.bind("mousewheel",d):this.trigger("mousewheel")},unmousewheel:function(d){return this.unbind("mousewheel",d)}})})(jQuery);