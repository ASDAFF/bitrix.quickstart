/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 * 
 * Requires: 1.2.2+
 */
(function(f){function b(h){var d=h||window.event,l=[].slice.call(arguments,1),j=0,k=0,i=0,h=f.event.fix(d);h.type="mousewheel";d.wheelDelta&&(j=d.wheelDelta/120);d.detail&&(j=-d.detail/3);i=j;d.axis!==void 0&&d.axis===d.HORIZONTAL_AXIS&&(i=0,k=-1*j);d.wheelDeltaY!==void 0&&(i=d.wheelDeltaY/120);d.wheelDeltaX!==void 0&&(k=-1*d.wheelDeltaX/120);l.unshift(h,j,k,i);return(f.event.dispatch||f.event.handle).apply(this,l)}var g=["DOMMouseScroll","mousewheel"];if(f.event.fixHooks){for(var a=g.length;a;){f.event.fixHooks[g[--a]]=f.event.mouseHooks}}f.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var c=g.length;c;){this.addEventListener(g[--c],b,false)}}else{this.onmousewheel=b}},teardown:function(){if(this.removeEventListener){for(var c=g.length;c;){this.removeEventListener(g[--c],b,false)}}else{this.onmousewheel=null}}};f.fn.extend({mousewheel:function(c){return c?this.bind("mousewheel",c):this.trigger("mousewheel")},unmousewheel:function(c){return this.unbind("mousewheel",c)}})})(jQuery);