/*!
 * jQuery Browser Plugin 0.0.7
 * https://github.com/gabceb/jquery-browser-plugin
 *
 * Original jquery-browser code Copyright 2005, 2015 jQuery Foundation, Inc. and other contributors
 * http://jquery.org/license
 *
 * Modifications Copyright 2015 Gabriel Cebrian
 * https://github.com/gabceb
 *
 * Released under the MIT license
 *
 * Date: 19-05-2015
 */
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],function(b){a(b)})}else{if(typeof module==="object"&&typeof module.exports==="object"){module.exports=a(require("jquery"))}else{a(window.jQuery)}}}(function(b){function a(d){if(d===undefined){d=window.navigator.userAgent}d=d.toLowerCase();var j=/(edge)\/([\w.]+)/.exec(d)||/(opr)[\/]([\w.]+)/.exec(d)||/(chrome)[ \/]([\w.]+)/.exec(d)||/(version)(applewebkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(d)||/(webkit)[ \/]([\w.]+).*(version)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(d)||/(webkit)[ \/]([\w.]+)/.exec(d)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(d)||/(msie) ([\w.]+)/.exec(d)||d.indexOf("trident")>=0&&/(rv)(?::| )([\w.]+)/.exec(d)||d.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(d)||[];var i=/(ipad)/.exec(d)||/(ipod)/.exec(d)||/(iphone)/.exec(d)||/(kindle)/.exec(d)||/(silk)/.exec(d)||/(android)/.exec(d)||/(windows phone)/.exec(d)||/(win)/.exec(d)||/(mac)/.exec(d)||/(linux)/.exec(d)||/(cros)/.exec(d)||/(playbook)/.exec(d)||/(bb)/.exec(d)||/(blackberry)/.exec(d)||[];var k={},e={browser:j[5]||j[3]||j[1]||"",version:j[2]||j[4]||"0",versionNumber:j[4]||j[2]||"0",platform:i[0]||""};if(e.browser){k[e.browser]=true;k.version=e.version;k.versionNumber=parseInt(e.versionNumber,10)}if(e.platform){k[e.platform]=true}if(k.android||k.bb||k.blackberry||k.ipad||k.iphone||k.ipod||k.kindle||k.playbook||k.silk||k["windows phone"]){k.mobile=true}if(k.cros||k.mac||k.linux||k.win){k.desktop=true}if(k.chrome||k.opr||k.safari){k.webkit=true}if(k.rv||k.edge){var c="msie";e.browser=c;k[c]=true}if(k.safari&&k.blackberry){var g="blackberry";e.browser=g;k[g]=true}if(k.safari&&k.playbook){var o="playbook";e.browser=o;k[o]=true}if(k.bb){var m="blackberry";e.browser=m;k[m]=true}if(k.opr){var h="opera";e.browser=h;k[h]=true}if(k.safari&&k.android){var f="android";e.browser=f;k[f]=true}if(k.safari&&k.kindle){var n="kindle";e.browser=n;k[n]=true}if(k.safari&&k.silk){var l="silk";e.browser=l;k[l]=true}k.name=e.browser;k.platform=e.platform;return k}window.jQBrowser=a(window.navigator.userAgent);window.jQBrowser.uaMatch=a;if(b){b.browser=window.jQBrowser}return window.jQBrowser}));