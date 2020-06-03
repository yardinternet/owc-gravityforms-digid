(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["Countdown"] = factory();
	else
		root["Countdown"] = factory();
})((typeof self !== "undefined" ? self : this), function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./resources/js/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/index.js":
/*!*******************************!*\
  !*** ./resources/js/index.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _lib_countdown__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./lib/countdown */ \"./resources/js/lib/countdown.js\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (_lib_countdown__WEBPACK_IMPORTED_MODULE_0__[\"Countdown\"]);\n\n//# sourceURL=webpack://Countdown/./resources/js/index.js?");

/***/ }),

/***/ "./resources/js/lib/countdown.js":
/*!***************************************!*\
  !*** ./resources/js/lib/countdown.js ***!
  \***************************************/
/*! exports provided: Countdown */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"Countdown\", function() { return Countdown; });\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\nvar Countdown = /*#__PURE__*/function () {\n  function Countdown(sessionTTL, resumeSessionTTL) {\n    var _this = this;\n\n    _classCallCheck(this, Countdown);\n\n    _defineProperty(this, \"sessionEnd\", function () {\n      var logoutLink = document.getElementById('logoutLink').href;\n      return window.location.href = logoutLink;\n    });\n\n    _defineProperty(this, \"createTTL\", function (seconds) {\n      return {\n        value: seconds,\n        expiry: Date.now() + seconds * 1000\n      };\n    });\n\n    _defineProperty(this, \"initCountdown\", function () {\n      return _this.countDownInterval = setInterval(_this.countdown, 1000);\n    });\n\n    _defineProperty(this, \"countdown\", function () {\n      var countdownElem = document.getElementById('js-countdown');\n      if (!localStorage.sessionTTL) return;\n      var now = new Date().getTime();\n      var distance = new Date(_this.parseJSON()) - now; // Time calculations for minutes and seconds.\n\n      var minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));\n      var seconds = Math.floor(distance % (1000 * 60) / 1000);\n      seconds = Math.round(seconds * 100) / 100;\n      minutes = Math.round(minutes * 100) / 100;\n\n      if (!!countdownElem) {\n        countdownElem.textContent = \"Resterende tijd: \".concat((minutes < 10 ? \"0\" : \"\") + minutes, \":\").concat((seconds < 10 ? \"0\" : \"\") + seconds);\n      }\n    });\n\n    _defineProperty(this, \"stopCountdown\", function () {\n      var countdownElem = document.getElementById('js-countdown');\n      clearInterval(_this.countDownInterval);\n\n      if (!!countdownElem) {\n        countdownElem.textContent = 'Verlopen';\n      }\n    });\n\n    _defineProperty(this, \"parseJSON\", function () {\n      return JSON.parse(localStorage.sessionTTL).expiry;\n    });\n\n    _defineProperty(this, \"initTimer\", function () {\n      return _this.timerInterval = setInterval(_this.beginTimer, 1000);\n    });\n\n    _defineProperty(this, \"beginTimer\", function () {\n      if (undefined === localStorage.sessionTTL && !_this._logoutClicked) {\n        _this.openModal();\n      }\n\n      if (Date.now() > _this.parseJSON() && localStorage.sessionTTL) {\n        _this.openModal();\n      }\n\n      return;\n    });\n\n    _defineProperty(this, \"stopTimer\", function () {\n      return clearInterval(_this.timerInterval);\n    });\n\n    _defineProperty(this, \"openModal\", function () {\n      _this.stopTimer();\n\n      _this.stopCountdown();\n\n      localStorage.removeItem('sessionTTL');\n      var modalWrapper = document.getElementById('modalWrapper');\n      var modalDialog = document.getElementById('modalDialog'); // change state like in hidden modal.\n\n      if (modalWrapper !== null) {\n        modalWrapper.classList.add('show');\n        modalWrapper.setAttribute('aria-hidden', 'false');\n        modalWrapper.style.cssText = 'display: block; position: fixed; top: 0; bottom: 0; left: 0; right: 0; background-color: #666; opacity: 1; z-index: 1272;';\n      }\n\n      if (modalDialog !== null) {\n        modalDialog.style.cssText = 'max-width: 500px; margin: 5rem auto; background-color: #ffffff; padding: 2rem;';\n      }\n\n      _this.startResumeCheck();\n    });\n\n    _defineProperty(this, \"closeModal\", function (e) {\n      var ESCAPE_KEY = 27;\n      var modal = document.getElementById('modalWrapper');\n\n      if (e.keyCode === ESCAPE_KEY && modal.classList.contains('show')) {\n        _this.sessionEnd();\n\n        var _modal = document.getElementById('modalWrapper');\n\n        var modalDialog = document.getElementById('modalDialog');\n\n        if (_modal !== null) {\n          _modal.classList.remove('show');\n\n          _modal.setAttribute('aria-hidden', 'true');\n\n          _modal.style.cssText = 'display: none;';\n        }\n\n        if (modalDialog !== null) {\n          modalDialog.style.cssText = \"\";\n        }\n      }\n    });\n\n    _defineProperty(this, \"a11yClick\", function (e) {\n      var SPACE_KEY = 32;\n      if (e.type !== 'click' || e.type !== 'keypress') return false;\n\n      if (e.type === 'keypress') {\n        var code = e.charCode || e.keyCode;\n        if (code !== SPACE_KEY) return false;\n      }\n\n      return true;\n    });\n\n    _defineProperty(this, \"logout\", function () {\n      return localStorage.removeItem('sessionTTL');\n    });\n\n    this.options = {\n      sessionTTL: sessionTTL,\n      resumeSessionTTL: resumeSessionTTL\n    };\n    this.insertModalHTML();\n    this.timerInterval;\n    this.countDownInterval;\n  }\n  /**\n   * Insert the session about to expire modal.\n   */\n\n\n  _createClass(Countdown, [{\n    key: \"insertModalHTML\",\n    value: function insertModalHTML() {\n      var gfWrapper = document.getElementsByClassName('gform_wrapper');\n\n      if (gfWrapper) {\n        gfWrapper[0].insertAdjacentHTML('beforeend', \"\\n\\t\\t\\t<div class='modal fade' id='modalWrapper' tabindex='-1' role='dialog' aria-labelledby='modalWrapper' aria-modal='true' aria-hidden='true' style='display:none;'>\\n\\t\\t\\t\\t<div id='modalDialog' class='modal-dialog' role='document'>\\n\\t\\t\\t\\t\\t<div class='modal-content'>\\n\\t\\t\\t\\t\\t\\t<div class='modal-header'>\\n\\t\\t\\t\\t\\t\\t\\t<h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t\\t<div class='modal-body | mb-4'>\\n\\t\\t\\t\\t\\t\\t\\tUw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.\\n\\t\\t\\t\\t\\t\\t\\tKies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t\\t<div class='modal-footer | d-flex justify-content-end'>\\n\\t\\t\\t\\t\\t\\t<button type='button' id='js-abortSession' tabindex='0' role='button' class='btn btn-outline-primary mr-2' data-dismiss='modal'>Sluiten</button>\\n\\t\\t\\t\\t\\t\\t\\t<button type='button' id='js-resumeSession' tabindex='0' role='button' class='btn btn-primary'>Verlengen</button>\\n\\t\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t\\t</div>\\n\\t\\t\\t\\t</div>\\n\\t\\t\\t</div>\");\n      }\n    }\n    /**\n     * Initialize the plugin.\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      if (!localStorage.sessionTTL) {\n        this.sessionStart();\n      } else {\n        this.sessionResume();\n      }\n\n      this.registerEventHandlers();\n    }\n    /**\n     * Start the local storage session.\n     * This is only a visual representation for the real session that goes on in the back-end.\n     */\n\n  }, {\n    key: \"sessionStart\",\n    value: function sessionStart() {\n      var format = this.createTTL(this.options.sessionTTL);\n      localStorage.sessionTTL = JSON.stringify(format);\n      this.initTimer();\n      this.initCountdown();\n    }\n    /**\n     * Resume the current session, calculate the time left.\n     */\n\n  }, {\n    key: \"sessionResume\",\n    value: function sessionResume() {\n      var parse = JSON.parse(localStorage.sessionTTL);\n      var now = new Date().getTime();\n      var distance = new Date(parse.expiry) - now;\n      var seconds = Math.floor(distance % (1000 * 60) / 1000);\n      this.options.sessionTTL = seconds;\n      this.initTimer();\n      this.initCountdown();\n    }\n    /**\n        * End session and go to logout page.\n        */\n\n  }, {\n    key: \"registerEventHandlers\",\n\n    /**\n     * Register event handlers.\n     */\n    value: function registerEventHandlers() {\n      var _this2 = this;\n\n      var resume = document.getElementById('js-resumeSession');\n      var abort = document.getElementById('js-abortSession');\n      var logout = document.getElementById('logoutLink');\n      resume.addEventListener('click', function (e) {\n        return _this2.sessionResume(e);\n      });\n      resume.addEventListener('keydown', function (e) {\n        return _this2.a11yClick(e);\n      });\n      abort.addEventListener('click', function (e) {\n        return _this2.sessionEnd(e);\n      });\n      abort.addEventListener('keydown', function (e) {\n        return _this2.a11yClick(e);\n      });\n      document.addEventListener('keydown', function (e) {\n        return _this2.closeModal(e);\n      });\n\n      if (logout) {\n        logout.addEventListener('click', function (e) {\n          return _this2.logout(e);\n        });\n      }\n    }\n    /**\n     * Format TTL object for local storage.\n     *\n     * @param {int} seconds\n     */\n\n  }, {\n    key: \"startResumeCheck\",\n\n    /**\n     * When modal is visible this function launches a timer.\n     */\n    value: function startResumeCheck() {\n      var that = this;\n      var resumeCheck = setInterval(function () {\n        if (undefined === localStorage.sessionTTL) {\n          that.sessionEnd();\n          clearInterval(resumeCheck);\n        } else {\n          clearInterval(resumeCheck);\n        }\n      }, 10000);\n    }\n    /**\n     * Add keypress event to modal buttons.\n     *\n     * @param {object} e\n     */\n\n  }]);\n\n  return Countdown;\n}();\n\n//# sourceURL=webpack://Countdown/./resources/js/lib/countdown.js?");

/***/ })

/******/ })["default"];
});