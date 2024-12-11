!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==typeof exports?exports.brickLayer=e():t.brickLayer=e()}(self,(()=>(()=>{var t={155:(t,e,i)=>{var n=i(222).A;t.exports=n},222:(t,e,i)=>{"use strict";function n(t){return n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(t)}function o(t,e){for(var i=0;i<e.length;i++){var n=e[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,r(n.key),n)}}function r(t){var e=function(t){if("object"!=n(t)||!t)return t;var e=t[Symbol.toPrimitive];if(void 0!==e){var i=e.call(t,"string");if("object"!=n(i))return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(t)}(t);return"symbol"==n(e)?e:e+""}i.d(e,{A:()=>s});const s=function(){return t=function t(e){var i;return function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.error=!1,this.setupRunning=!1,e!==Object(e)?(console.error("Bricklayer options has not been provided correctly."),void(this.error=!0)):void 0===e.container?(console.error("Bricklayer grid container has not been provided."),void(this.error=!0)):(this.options=Object.assign({dynamicContent:!1,waitForImages:!0,gutter:10,itemSelector:"",directChildren:!1,useTransform:!0,callBefore:!1,callAfter:!1,runBefore:"once",runAfter:"once"},e),this.container=this.options.container,this.container="string"==typeof this.container?document.querySelector(this.container):this.container,void(this.container&&(i=this.getItems()).length?(this.items=i,this.observer=!1,this.timeout=null,this.containerWidth=0,this.columnWidth=0,this.columns=[],this.resize=this.onResize.bind(this),this.beforeCall=!0,this.afterCall=!0):this.error=!0))},(e=[{key:"getItems",value:function(){var t=[];return!this.options.directChildren&&this.options.itemSelector?t=Array.prototype.slice.call(this.container.querySelectorAll(this.options.itemSelector)):(t=(t=Array.prototype.slice.call(this.container.children)).filter((function(t){return 1===t.nodeType})),this.options.itemSelector&&(t=t.filter(item.matches(this.options.itemSelector)))),t}},{key:"init",value:function(){this.error||(this.options.waitForImages?this.afterImagesLoaded(this.items,this.setup.bind(this)):this.setup(),window.addEventListener("resize",this.resize),this.options.dynamicContent&&this.observeChanges())}},{key:"setup",value:function(){this.setupRunning&&setTimeout(function(){this.setup()}.bind(this),500),this.setupRunning=!0,this.beforeCall&&"function"==typeof this.options.callBefore&&this.options.callBefore(),this.buildSetupData(),this.containerWidth&&this.addDynamicStyling(this.items),this.afterCall&&"function"==typeof this.options.callAfter&&this.options.callAfter(),this.beforeCall="once"!==this.options.runBefore,this.afterCall="once"!==this.options.runAfter,this.setupRunning=!1}},{key:"buildSetupData",value:function(){var t,e,i=this,n=getComputedStyle(this.container),o=n.paddingLeft,r=n.paddingRight,s=1;t=this.container.clientWidth,this.containerWidth=t?t-(this.getStyleSize(o)+this.getStyleSize(r)):0,this.items=this.items.map((function(t){return i.getItemData(t)})),this.items=this.items.filter((function(t){return!(t.isHidden||0===t.width)}));var a=this.items.reduce((function(t,e){return Math.min(t,e.width)}),this.items[0].width);this.columnWidth=a+this.options.gutter,0!==this.columnWidth&&(s=(this.containerWidth+this.options.gutter)/this.columnWidth,e=Math.abs(this.columnWidth-this.containerWidth/Math.round(s)),s=(s=Math[e&&e<=1?"round":"floor"](s))||1),this.columns=Array.apply(null,Array(s)).map(Number.prototype.valueOf,0)}},{key:"getStyleSize",value:function(t){var e=parseFloat(t);return-1!=t.indexOf("%")||isNaN(e)?0:e}},{key:"getItemData",value:function(t){return t.nodeType?{elem:t,isHidden:this.isHidden(t),width:t.offsetWidth,height:t.offsetHeight}:(t.isHidden=this.isHidden(t.elem),t.width=t.elem.offsetWidth,t.height=t.elem.offsetHeight,t)}},{key:"isHidden",value:function(t){return"none"===getComputedStyle(t).display}},{key:"addDynamicStyling",value:function(t){var e=this;this.container.style.position="relative",t.forEach((function(t){e.positionItem(t)}))}},{key:"positionItem",value:function(t){var e=t.elem,i=this.columnWidth?t.width%this.columnWidth:1,n=i&&i<=1?"round":"ceil",o=this.columnWidth?Math[n](t.width/this.columnWidth):1;o=Math.min(this.columns.length,o);var r=this.getColPosition(o),s=r.height,a=r.index,h=0===s?0:this.options.gutter,l=s+h+"px",u=a*this.columnWidth+"px";e.style.position="absolute",this.options.useTransform?(e.style.transform="translate("+u+", "+l+")",e.style.transition="transform 0.2s ease"):(e.style.top=l,e.style.left=u);for(var c=s+t.height+h,d=a+o,f=a;f<d;f++)this.columns[f]=c;this.container.style.height=Math.max.apply(null,this.columns)+"px"}},{key:"getColPosition",value:function(t){var e,i=this,n=this.columns.length+1-t;if(1===t)return{height:e=this.columns.reduce((function(t,e){return Math.min(t,e)}),this.columns[0]),index:this.columns.indexOf(e)};var o=Array.apply(null,Array(n)).map((function(e,n){var o=i.columns.slice(n,n+t);return o.reduce((function(t,e){return Math.max(t,e)}),o[0])}));return{height:e=o.reduce((function(t,e){return Math.min(t,e)}),o[0]),index:o.indexOf(e)}}},{key:"observeChanges",value:function(){window&&window.MutationObserver&&(!1===this.observer&&(this.observer=new MutationObserver(this.muCallback.bind(this))),this.observer.observe(this.container,{childList:!0,subtree:!0,attributes:!0,attributeFilter:["class"]}))}},{key:"muCallback",value:function(t){this.items=this.getItems(),this.setup()}},{key:"onResize",value:function(){var t=this;this.timeout||(this.timeout=setTimeout((function(){var e,i=getComputedStyle(t.container),n=i.paddingLeft,o=i.paddingRight;(e=(e=t.container.clientWidth)?e-(t.getStyleSize(n)+t.getStyleSize(o)):0)!==t.containerWidth&&t.setup(),t.timeout=null}),400))}},{key:"destroy",value:function(){var t=this;!1!==this.observer&&(this.observer.disconnect(),this.observer=!1),window.removeEventListener("resize",this.resize),this.container.style.position="",this.container.style.height="",this.items.forEach((function(e){var i=e.elem;i.style.position="",t.options.useTransform?(i.style.transform="",i.style.transition=""):(i.style.top="",i.style.left="")})),this.items=[]}},{key:"afterImagesLoaded",value:function(t,e){var i,n=t.length?Array.prototype.slice.call(t):[t],o=0,r=[];n.forEach((function(t){r=r.concat(Array.prototype.slice.call(t.getElementsByTagName("IMG")))})),(i=r.length)||e(),r.forEach((function(t){t.complete&&t.naturalWidth&&0!==t.naturalWidth?++o===i&&e():(t.addEventListener("load",(function(){++o===i&&e()})),t.addEventListener("error",(function(){o++,t.classList.add("errorLoading"),o===i&&e()})))}))}}])&&o(t.prototype,e),Object.defineProperty(t,"prototype",{writable:!1}),t;var t,e}()}},e={};function i(n){var o=e[n];if(void 0!==o)return o.exports;var r=e[n]={exports:{}};return t[n](r,r.exports,i),r.exports}return i.d=(t,e)=>{for(var n in e)i.o(e,n)&&!i.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},i.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),i(155)})()));