/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/login/block.json":
/*!******************************!*\
  !*** ./src/login/block.json ***!
  \******************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"leira-auth/login","title":"Leira Auth – Login Form","category":"widgets","icon":"lock","description":"Displays a login form with configurable options.","textdomain":"leira-auth","supports":{"className":true,"align":["wide","full"],"spacing":{"margin":true,"padding":true,"blockGap":true},"color":{"text":true,"background":true,"link":true},"typography":{"fontSize":true,"lineHeight":true},"anchor":true},"editorScript":"file:./edit.js","style":["wp-block-library","wp-block-buttons","wp-block-button","file:./style.css"],"editorStyle":["wp-block-library","wp-block-buttons","wp-block-button","file:./editor.css"],"attributes":{"redirect":{"type":"string","default":""},"username_label":{"type":"string","default":"Username or Email Address"},"username_placeholder":{"type":"string","default":""},"password_label":{"type":"string","default":"Password"},"password_placeholder":{"type":"string","default":""},"remember":{"type":"boolean","default":true},"remember_default":{"type":"boolean","default":false},"remember_label":{"type":"string","default":"Remember Me"},"submit_text":{"type":"string","default":""},"submit_alignment":{"type":"string","default":"left"},"forgot_password_show":{"type":"boolean","default":true},"forgot_password_label":{"type":"string","default":""},"register_login":{"type":"boolean","default":true},"register_label":{"type":"string","default":""}}}');

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!***************************!*\
  !*** ./src/login/edit.js ***!
  \***************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./block.json */ "./src/login/block.json");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







/**
 * Register the Login block
 *
 * @return {JSX.Element}
 * @constructor
 */

(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_5__, {
  /**
   * Block editor
   *
   * @param {object} attributes - The attributes of the block
   * @param {string} attributes.redirect - Register URL
   * @param {string} attributes.form_id - Form ID
   * @param {string} attributes.username_label - Username label
   * @param {string} attributes.username_placeholder - Username input placeholder
   * @param {string} attributes.password_label - Password label
   * @param {string} attributes.password_placeholder - Password input placeholder
   * @param {boolean} attributes.remember - Show "Remember me" checkbox
   * @param {string} attributes.remember_label - "Remember me" label
   * @param {boolean} attributes.remember_default - "Remember me" default value (checked or not)
   * @param {string} attributes.submit_text - Log In button text
   * @param {boolean} attributes.forgot_password_show - Show "Forgot password" link
   * @param {string} attributes.forgot_password_label - "Forgot password" link label
   * @param {boolean} attributes.register_login - Show "Register" link
   * @param {string} attributes.register_label - "Register" link label
   * @param {function} setAttributes - Function to update the block attributes.
   * @return {JSX.Element}
   */
  edit: ({
    attributes,
    setAttributes
  }) => {
    // Block props
    const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)();

    /**
     * Render
     */
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('General Settings', 'leira-auth'),
          initialOpen: false,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Redirect URL', 'leira-auth'),
            type: "url",
            value: attributes.redirect,
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('URL to redirect to after login. Leave empty to redirect to the default page.', 'leira-auth'),
            onChange: value => setAttributes({
              redirect: value
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Username', 'leira-auth'),
          initialOpen: false,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label', 'leira-auth'),
            type: "text",
            value: attributes.username_label,
            onChange: value => setAttributes({
              username_label: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Placeholder', 'leira-auth'),
            type: "text",
            value: attributes.username_placeholder,
            onChange: value => setAttributes({
              username_placeholder: value
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Password', 'leira-auth'),
          initialOpen: false,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label', 'leira-auth'),
            value: attributes.password_label,
            onChange: value => setAttributes({
              password_label: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Placeholder', 'leira-auth'),
            type: "text",
            value: attributes.password_placeholder,
            onChange: value => setAttributes({
              password_placeholder: value
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remember me', 'leira-auth'),
          initialOpen: false,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label', 'leira-auth'),
            value: attributes.remember_label,
            placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remember me', 'leira-auth'),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The label for remember me checkbox.', 'leira-auth'),
            onChange: value => setAttributes({
              remember_label: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show checkbox', 'leira-auth'),
            checked: attributes.remember,
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show the remember me checkbox.', 'leira-auth'),
            onChange: value => setAttributes({
              remember: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Checked', 'leira-auth'),
            checked: attributes.remember_default,
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The default checkbox status.', 'leira-auth'),
            onChange: value => setAttributes({
              remember_default: value
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Log In button', 'leira-auth'),
          initialOpen: false,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Text', 'leira-auth'),
            value: attributes.submit_text,
            placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Log in', 'leira-auth'),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The text of the submit button.', 'leira-auth'),
            onChange: value => setAttributes({
              submit_text: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Alignment', 'leira-auth'),
            value: attributes.submit_alignment || 'left',
            options: [{
              label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Left', 'leira-auth'),
              value: 'left'
            }, {
              label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Center', 'leira-auth'),
              value: 'center'
            }, {
              label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Right', 'leira-auth'),
              value: 'right'
            }, {
              label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Full width', 'leira-auth'),
              value: 'full'
            }],
            onChange: value => setAttributes({
              submit_alignment: value
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Links', 'leira-auth'),
          initialOpen: false,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show forgot password', 'leira-auth'),
            checked: attributes.forgot_password_show,
            onChange: value => setAttributes({
              forgot_password_show: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Forgot password label', 'leira-auth'),
            value: attributes.forgot_password_label,
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The forgot password link text.', 'leira-auth'),
            onChange: value => setAttributes({
              forgot_password_label: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show register link', 'leira-auth'),
            checked: attributes.register_login,
            onChange: value => setAttributes({
              register_login: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Register link label', 'leira-auth'),
            value: attributes.register_label,
            onChange: value => setAttributes({
              register_label: value
            })
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        ...blockProps,
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default()), {
          block: _block_json__WEBPACK_IMPORTED_MODULE_5__.name,
          attributes: attributes
        })
      })]
    });
  },
  /**
   * Save function
   *
   * @return {null}
   */
  save: () => null // Dynamic block
});
})();

/******/ })()
;
//# sourceMappingURL=edit.js.map