exports.conditionalClassNames=function(){return[].slice.call(arguments).filter(Boolean).join(" ")},exports.lang=function(n){var e=("undefined"!=typeof BlazervelLang?BlazervelLang:null==globalThis?void 0:globalThis.BlazervelLang).translations,t=n.split("."),l=e;return t.map(function(e){return l=l[e]||n}),l},exports.mergeCssClasses=function(){var n=[];return[].slice.call(arguments).filter(function(n){return["object","array","string"].indexOf(typeof n)>=0}).forEach(function(e){"string"==typeof e&&(e=e.split(" ")),n=n.concat(e)}),n.join(" ").trim()};
//# sourceMappingURL=index.js.map