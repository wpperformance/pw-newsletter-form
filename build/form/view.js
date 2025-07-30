import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "@wordpress/interactivity":
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
/***/ ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**************************!*\
  !*** ./src/form/view.ts ***!
  \**************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");

/**
 * check email
 * @param {string} text
 * @returns boolean
 */
const isEmail = text => {
  const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(text);
};
const {
  state,
  actions
} = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)('pw-newsletter-form', {
  state: {
    token: '',
    submitted: false,
    message: '',
    showMessage: false
  },
  actions: {
    hideMessage: () => {
      setTimeout(() => {
        state.showMessage = false;
      }, 5000);
    },
    getToken: () => {
      fetch("/wp-json/pw_newsletter_form/getToken").then(res => res.json()).then(res => {
        state.token = res.token;
      }).catch(err => {
        console.error('Error fetching token:', err);
      });
    },
    submitForm: event => {
      const ctx = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      if (form.email.value !== "" && isEmail(form.email.value)) {
        state.submitted = true;
        fetch("/wp-json/pw_newsletter_form/action", {
          method: 'POST',
          body: formData
        }).then(res => res.json()).then(res => {
          const {
            data
          } = res;

          // error before action
          if (data.status !== 200) {
            state.message = ctx.error_status_message || res.message || 'An error occurred';
            state.showMessage = true;
            state.submitted = false;
            actions.hideMessage();
            return;
          }

          // error from brevo
          if (data.error) {
            state.message = ctx.error_message || res.error;
            state.showMessage = true;
            state.submitted = false;
            actions.hideMessage();
            return;
          }

          // success
          state.message = ctx.success_message || res.message || 'Success!';
          state.showMessage = true;
          form.reset();
          state.submitted = false;
          actions.hideMessage();
        }).catch(err => {
          console.error('Error submitting form:', err);
        });
      }
    }
  }
});
})();


//# sourceMappingURL=view.js.map