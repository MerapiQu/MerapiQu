/*! For license information please see index.js.LICENSE.txt */
"use strict";!function(e,t){if("object"==typeof exports&&"object"==typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var r=t();for(var n in r)("object"==typeof exports?exports:e)[n]=r[n]}}(self,(()=>(self.webpackChunk=self.webpackChunk||[]).push([[536],{870:(e,t,r)=>{r.r(t),r.d(t,{default:()=>i});var n=r(41),o=r(85);function a(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=Array(t);r<t;r++)n[r]=e[r];return n}const i={name:"dashboard",meta:JSON.parse('{"title":"Dashboard","description":"This page is dashboard"}'),component:function(){var e,t,r=(e=(0,n.useState)(!1),t=2,function(e){if(Array.isArray(e))return e}(e)||function(e,t){var r=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=r){var n,o,a,i,l=[],s=!0,c=!1;try{if(a=(r=r.call(e)).next,0===t){if(Object(r)!==r)return;s=!1}else for(;!(s=(n=a.call(r)).done)&&(l.push(n.value),l.length!==t);s=!0);}catch(e){c=!0,o=e}finally{try{if(!s&&null!=r.return&&(i=r.return(),Object(i)!==i))return}finally{if(c)throw o}}return l}}(e,t)||function(e,t){if(e){if("string"==typeof e)return a(e,t);var r={}.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?a(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()),i=r[0];return r[1],(0,o.jsx)(o.Fragment,{children:(0,o.jsxs)("div",{className:"d-flex justify-content-between align-items-center",children:[(0,o.jsx)("h1",{children:"Dashboard"}),(0,o.jsx)("div",{children:(0,o.jsx)("button",{className:"btn ".concat(i?"btn-primary":"btn-secondary"),onClick:function(){},children:i?"Save Changes":"Customize Widgets"})})]})})}}},197:(e,t)=>{var r=Symbol.for("react.transitional.element"),n=Symbol.for("react.fragment");function o(e,t,n){var o=null;if(void 0!==n&&(o=""+n),void 0!==t.key&&(o=""+t.key),"key"in t)for(var a in n={},t)"key"!==a&&(n[a]=t[a]);else n=t;return t=n.ref,{$$typeof:r,type:e,key:o,ref:void 0!==t?t:null,props:n}}t.Fragment=n,t.jsx=o,t.jsxs=o},85:(e,t,r)=>{e.exports=r(197)}},e=>e(e.s=870)])));