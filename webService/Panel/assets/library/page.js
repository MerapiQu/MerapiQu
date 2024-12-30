"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define("Panel", [], factory);
	else if(typeof exports === 'object')
		exports["Panel"] = factory();
	else
		root["Panel"] = root["Panel"] || {}, root["Panel"]["library/page"] = factory();
})(self, () => {
return (self["webpackChunkPanel"] = self["webpackChunkPanel"] || []).push([["library/page"],{

/***/ "../library/page/main.tsx":
/*!********************************!*\
  !*** ../library/page/main.tsx ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ \"../../../node_modules/react/index.js\");\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _styles_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles.scss */ \"../library/page/styles.scss\");\n/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ \"../../../node_modules/react/jsx-runtime.js\");\n\n\n\nvar PageManager = function PageManager() {\n  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(\"div\", {\n    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(\"div\", {\n      className: \"py-2\",\n      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(\"h1\", {\n        children: \"Page Manager\"\n      })\n    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(\"div\", {\n      className: \"page-container\",\n      children: \"Hallo World\"\n    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(\"p\", {\n      children: \"Next you will playing in this page, this page provide service such as create a page edit a page and delete a page. Most important tools is editing tool wich allow user drag and drop block to design her own page.\"\n    })]\n  });\n};\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PageManager);\n\n//# sourceURL=webpack://Panel/../library/page/main.tsx?");

/***/ }),

/***/ "../library/page/styles.scss":
/*!***********************************!*\
  !*** ../library/page/styles.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://Panel/../library/page/styles.scss?");

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["main"], () => (__webpack_exec__("../library/page/main.tsx")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ return __webpack_exports__;
/******/ }
]);
});