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
(function(f){function g(a){var n=a||window.event,m=[].slice.call(arguments,1),l=0,k=!0,j=0,i=0;return a=f.event.fix(n),a.type="mousewheel",n.wheelDelta&&(l=n.wheelDelta/120),n.detail&&(l=-n.detail/3),i=l,n.axis!==undefined&&n.axis===n.HORIZONTAL_AXIS&&(i=0,j=-1*l),n.wheelDeltaY!==undefined&&(i=n.wheelDeltaY/120),n.wheelDeltaX!==undefined&&(j=-1*n.wheelDeltaX/120),m.unshift(a,l,j,i),(f.event.dispatch||f.event.handle).apply(this,m)}var e=["DOMMouseScroll","mousewheel"];if(f.event.fixHooks){for(var h=e.length;h;){f.event.fixHooks[e[--h]]=f.event.mouseHooks}}f.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var b=e.length;b;){this.addEventListener(e[--b],g,!1)}}else{this.onmousewheel=g}},teardown:function(){if(this.removeEventListener){for(var b=e.length;b;){this.removeEventListener(e[--b],g,!1)}}else{this.onmousewheel=null}}},f.fn.extend({mousewheel:function(b){return b?this.bind("mousewheel",b):this.trigger("mousewheel")},unmousewheel:function(b){return this.unbind("mousewheel",b)}})})(jQuery);