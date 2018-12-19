"use strict";(function(a,b){if("function"==typeof define&&define.amd)define(["module"],b);else if("undefined"!=typeof exports)b(module);else{var c={exports:{}};b(c),a.undefined=c.exports}})(void 0,function(a){"use strict";function b(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}function c(a,b){for(var c,d=0;d<b.length;d++)c=b[d],c.enumerable=c.enumerable||!1,c.configurable=!0,"value"in c&&(c.writable=!0),Object.defineProperty(a,c.key,c)}function d(a,b,d){return b&&c(a.prototype,b),d&&c(a,d),a}var e=function(){function a(){b(this,a)}return d(a,null,[{key:"removeFromArray",value:function(a,b){for(var c=0;c<b.length;c++)JSON.stringify(a)==JSON.stringify(b[c])&&b.splice(c,1);return b}}]),a}(),f=function(){function a(){b(this,a)}return d(a,null,[{key:"getTextWithoutChildren",value:function(a,b){var c=a.clone();return c.children().remove(),"undefined"!=typeof b&&!0===b?c.text():c.text().trim()}},{key:"scrollTo",value:function(a){var b=this,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:0,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:0,e=!!(3<arguments.length&&void 0!==arguments[3])&&arguments[3],f=a.getBoundingClientRect(),g=f.top+window.pageYOffset-c;setTimeout(function(){b.elementInViewport(a)&&!0!==e||window.scrollTo({top:g,behavior:"smooth"})},d)}},{key:"elementInViewport",value:function(a){for(var b=a.offsetTop,c=a.offsetLeft,d=a.offsetWidth,e=a.offsetHeight;a.offsetParent;)a=a.offsetParent,b+=a.offsetTop,c+=a.offsetLeft;return b<window.pageYOffset+window.innerHeight&&c<window.pageXOffset+window.innerWidth&&b+e>window.pageYOffset&&c+d>window.pageXOffset}},{key:"getAllParentNodes",value:function(a){for(var b=[];a;)b.unshift(a),a=a.parentNode;return e.removeFromArray(document,b),b}}]),a}(),g=function(){function a(){b(this,a)}return d(a,null,[{key:"addDynamicEventListener",value:function(a,b,c){document.addEventListener(a,function(a){var d=f.getAllParentNodes(a.target);Array.isArray(d)&&d.reverse().forEach(function(d){d&&d.matches(b)&&c(a)})})}}]),a}(),h=function(){function a(){b(this,a)}return d(a,null,[{key:"getParameterByName",value:function(a,b){b||(b=window.location.href),a=a.replace(/[\[\]]/g,"\\$&");var c=new RegExp("[?&]"+a+"(=([^&#]*)|&|#|$)"),d=c.exec(b);return d?d[2]?decodeURIComponent(d[2].replace(/\+/g," ")):"":null}},{key:"addParameterToUri",value:function(a,b,c){a||(a=window.location.href);var d,e=new RegExp("([?&])"+b+"=.*?(&|#|$)(.*)","gi");if(e.test(a))return"undefined"!=typeof c&&null!==c?a.replace(e,"$1"+b+"="+c+"$2$3"):(d=a.split("#"),a=d[0].replace(e,"$1$3").replace(/(&|\?)$/,""),"undefined"!=typeof d[1]&&null!==d[1]&&(a+="#"+d[1]),a);if("undefined"!=typeof c&&null!==c){var f=-1===a.indexOf("?")?"?":"&";return d=a.split("#"),a=d[0]+f+b+"="+c,"undefined"!=typeof d[1]&&null!==d[1]&&(a+="#"+d[1]),a}return a}},{key:"addParametersToUri",value:function(a,b){for(var c in b)b.hasOwnProperty(c)&&(a=this.addParameterToUri(a,c,b[c]));return a}},{key:"removeParameterFromUri",value:function(a,b){//prefer to use l.search if you have a location/link object
var c=a.split("?");if(2<=c.length){//reverse iteration as may be destructive
for(var d=encodeURIComponent(b)+"=",e=c[1].split(/[&;]/g),f=e.length;0<f--;)//idiom for string.startsWith
-1!==e[f].lastIndexOf(d,0)&&e.splice(f,1);return a=c[0]+"?"+e.join("&"),a}return a}},{key:"removeParametersFromUri",value:function(a,b){for(var c in b)b.hasOwnProperty(c)&&(a=this.removeParameterFromUri(a,c));return a}},{key:"replaceParameterInUri",value:function(a,b,c){this.addParameterToUri(this.removeParameterFromUri(a,b),b,c)}},{key:"parseQueryString",value:function(a){return JSON.parse("{\""+decodeURI(a).replace(/"/g,"\\\"").replace(/&/g,"\",\"").replace(/=/g,"\":\"")+"\"}")}}]),a}(),i=function(){function a(){b(this,a)}return d(a,null,[{key:"isTruthy",value:function(a){return"undefined"!=typeof a&&null!==a}},{key:"call",value:function(a){"function"==typeof a&&a.apply(this,Array.prototype.slice.call(arguments,1))}}]),a}();a.exports={arrays:e,dom:f,events:g,url:h,util:i}});