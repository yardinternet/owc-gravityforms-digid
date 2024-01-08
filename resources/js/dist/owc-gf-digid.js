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
		define([], factory);
	else if(typeof exports === 'object')
		exports["CountdownDigiD"] = factory();
	else
		root["CountdownDigiD"] = factory();
})((typeof self !== "undefined" ? self : this), () => {
return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/index.js":
/*!*******************************!*\
  !*** ./resources/js/index.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _lib_countdown__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./lib/countdown */ \"./resources/js/lib/countdown.js\");\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({\n  CountdownDigiD: _lib_countdown__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n});\n\n//# sourceURL=webpack://CountdownDigiD/./resources/js/index.js?");

/***/ }),

/***/ "./resources/js/lib/countdown.js":
/*!***************************************!*\
  !*** ./resources/js/lib/countdown.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ CountdownDigiD)\n/* harmony export */ });\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\nvar CountdownDigiD = /*#__PURE__*/function () {\n  function CountdownDigiD(sessionTTL, lastActivity) {\n    var _this = this;\n    _classCallCheck(this, CountdownDigiD);\n    _defineProperty(this, \"timerInit\", function () {\n      return _this.timerInterval = setInterval(_this.checkSessionStatus, _this.second);\n    });\n    _defineProperty(this, \"sessionHeartbeat\", function () {\n      return setInterval(_this.maybeKeepSessionAlive, _this.minute);\n    });\n    _defineProperty(this, \"checkSessionStatus\", function () {\n      if (Date.now() - _this.lastActivity > _this.modalShouldOpen) {\n        clearInterval(_this.timerInterval);\n        _this.openModal();\n      }\n    });\n    _defineProperty(this, \"maybeKeepSessionAlive\", function () {\n      if (_this.lastActivityIsUpdated) {\n        _this.keepSessionAlive();\n      }\n      _this.lastActivityIsUpdated = false;\n    });\n    _defineProperty(this, \"openModal\", function () {\n      _this.modalTimer();\n      var modalWrapperDigiD = document.getElementById('modalWrapperDigiD');\n      var modalDialogDigiD = document.getElementById('modalDialogDigiD');\n\n      // change state like in hidden modal.\n      if (modalWrapperDigiD !== null) {\n        modalWrapperDigiD.classList.add('show');\n        modalWrapperDigiD.setAttribute('aria-hidden', 'false');\n        modalWrapperDigiD.style.cssText = 'display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;';\n      }\n      if (modalDialogDigiD !== null) {\n        modalDialogDigiD.style.cssText = 'max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;';\n      }\n    });\n    _defineProperty(this, \"closeModal\", function () {\n      _this.stopModalTimer();\n      var modal = document.getElementById('modalWrapperDigiD');\n      var modalDialogDigiD = document.getElementById('modalDialogDigiD');\n      if (modal !== null) {\n        modal.classList.remove('show');\n        modal.setAttribute('aria-hidden', 'true');\n        modal.style.cssText = 'display: none;';\n      }\n      if (modalDialogDigiD !== null) {\n        modalDialogDigiD.style.cssText = '';\n      }\n    });\n    _defineProperty(this, \"updateLastActivity\", function () {\n      _this.lastActivity = Date.now();\n      _this.lastActivityIsUpdated = true;\n    });\n    _defineProperty(this, \"logout\", function () {\n      window.location = '/digid/logout';\n    });\n    _defineProperty(this, \"keepSessionAlive\", function () {\n      fetch('/digid/keep_alive');\n    });\n    _defineProperty(this, \"a11yClick\", function (e) {\n      var SPACE_KEY = 32;\n      if (e.type !== 'click' || e.type !== 'keypress') return false;\n      if (e.type === 'keypress') {\n        var code = e.charCode || e.keyCode;\n        if (code !== SPACE_KEY) return false;\n      }\n      return true;\n    });\n    this.second = 1000;\n    this.minute = 60 * this.second;\n    var tenSeconds = 10 * this.second;\n    sessionTTL = sessionTTL * this.second - tenSeconds; // js session should end 10 seconds before php session expires\n\n    this.modalTTL = this.minute;\n    this.modalShouldOpen = sessionTTL - this.modalTTL;\n    this.lastActivity = lastActivity * this.second;\n    this.insertModalHTML();\n    this.modalTimeout = undefined;\n    this.timerInterval = undefined;\n  }\n  _createClass(CountdownDigiD, [{\n    key: \"insertModalHTML\",\n    value: function insertModalHTML() {\n      var gfWrapper = document.getElementsByClassName('gform_wrapper');\n      if (gfWrapper) {\n        gfWrapper[0].insertAdjacentHTML('beforeend', \"\\n\\t\\t\\t<div class='modal fade owc-gf-digid-hidden' id='modalWrapperDigiD' tabindex='-1' role='dialog' aria-labelledby='modalWrapperDigiD' aria-modal='true' aria-hidden='true'>\\n\\t\\t\\t\\t<div id='modalDialogDigiD' class='modal-dialog' role='document'>\\n\\t\\t\\t\\t\\t<div class='modal-content'>\\n\\t\\t\\t\\t\\t\\t<div class='modal-header'>\\n\\t\\t\\t\\t\\t\\t\\t<h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t\\t<div class='modal-body | mb-4'>\\n\\t\\t\\t\\t\\t\\t\\tUw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.\\n\\t\\t\\t\\t\\t\\t\\tKies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t\\t<div class='modal-footer | d-flex justify-content-end' >\\n\\t\\t\\t\\t\\t\\t\\t<form action=\\\"/digid/logout\\\" method=\\\"dialog\\\">\\n\\t\\t\\t\\t\\t\\t\\t\\t<button type='submit' id='js-abortSession-DigiD' tabindex='0' role='button' class='btn btn-outline-primary mr-2' data-dismiss='modal'>Sluiten</button>\\n\\t\\t\\t\\t\\t\\t\\t</form>\\n\\t\\t\\t\\t\\t\\t\\t<button type='button' id='js-resumeSession-DigiD' tabindex='0' role='button' class='btn btn-primary'>Verlengen</button>\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t</div>\\n\\t\\t\\t</div>\");\n      }\n    }\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.registerEventHandlers();\n      this.sessionHeartbeat();\n      this.timerInit();\n    }\n  }, {\n    key: \"registerEventHandlers\",\n    value: function registerEventHandlers() {\n      var _this2 = this;\n      var resume = document.getElementById('js-resumeSession-DigiD');\n      var abort = document.getElementById('js-abortSession-DigiD');\n      resume.addEventListener('click', function (e) {\n        return _this2.sessionResume(e);\n      });\n      resume.addEventListener('keydown', function (e) {\n        return _this2.a11yClick(e);\n      });\n      abort.addEventListener('click', function (e) {\n        return _this2.logout(e);\n      });\n      abort.addEventListener('keydown', function (e) {\n        return _this2.a11yClick(e);\n      });\n      document.addEventListener('keydown', function (e) {\n        var ESCAPE_KEY = 27;\n        var modal = document.getElementById('modalWrapperDigiD');\n        if (e.keyCode === ESCAPE_KEY && modal.classList.contains('show')) {\n          _this2.logout();\n        }\n      });\n      document.addEventListener('mousemove', function (e) {\n        return _this2.updateLastActivity(e);\n      });\n      document.addEventListener('keydown', function (e) {\n        return _this2.updateLastActivity(e);\n      });\n    }\n  }, {\n    key: \"modalTimer\",\n    value: function modalTimer() {\n      var _this3 = this;\n      this.modalTimeout = setTimeout(function () {\n        _this3.logout();\n      }, this.modalTTL);\n    }\n  }, {\n    key: \"stopModalTimer\",\n    value: function stopModalTimer() {\n      if (this.modalTimeout) {\n        clearTimeout(this.modalTimeout);\n      }\n    }\n  }, {\n    key: \"sessionResume\",\n    value: function sessionResume() {\n      this.closeModal();\n      this.updateLastActivity();\n      this.keepSessionAlive();\n      this.timerInit();\n    }\n\n    /**\n     * Add keypress event to modal buttons.\n     *\n     * @param {object} e\n     */\n  }]);\n  return CountdownDigiD;\n}();\n\n\n//# sourceURL=webpack://CountdownDigiD/./resources/js/lib/countdown.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./resources/js/index.js");
/******/ 	__webpack_exports__ = __webpack_exports__["default"];
/******/ 	
/******/ 	return __webpack_exports__;
/******/ })()
;
});