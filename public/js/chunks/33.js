(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[33],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js":
/*!*******************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js ***!
  \*******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @chenfengyuan/vue-countdown */ "./node_modules/@chenfengyuan/vue-countdown/dist/vue-countdown.js");
/* harmony import */ var _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./../../http/axios.js */ "./resources/js/src/http/axios.js");


/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      room: {
        id: '',
        title: '',
        pass: '',
        code: ''
      },
      input: {
        name: '',
        pass: ''
      },
      error: {
        name: '',
        pass: ''
      }
    };
  },
  components: {
    'countdown': _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_0___default.a
  },
  created: function created() {
    this.getRoomInfo();
  },
  methods: {
    getRoomInfo: function getRoomInfo() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_1__["default"].g("/api/room/info/".concat(this.$route.params.code)).then(function (response) {
        _this.$vs.loading.close();
        if (response.data.status) {
          _this.room = response.data.data;
        } else {
          _this.$router.push({
            name: 'page-error-404'
          })["catch"](function () {});
          _this.$vs.notify({
            title: 'Lỗi',
            text: response.data.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          });
        }
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    join: function join() {
      var _this2 = this;
      this.error.name = '';
      if (!this.input.name) {
        this.error.name = "Tên truy cập là bắt buộc";
      } else {
        this.$vs.loading();
        _http_axios_js__WEBPACK_IMPORTED_MODULE_1__["default"].p("/api/room/join", {
          code: this.room.code,
          name: this.input.name,
          pass: this.input.pass
        }).then(function (response) {
          if (response.data.status) {
            window.location.href = response.data.redirect_url;
            _this2.$vs.loading.close();
          } else {
            _this2.$vs.loading.close();
            _this2.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            });
          }
        })["catch"](function (error) {
          console.log(error);
          _this2.$vs.loading.close();
        });
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb":
/*!*****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb ***!
  \*****************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "h-screen flex w-full bg-img vx-row no-gutter justify-center items-center"
  }, [_c("div", {
    staticClass: "vx-col sm:w-1/2 md:w-1/2 lg:w-2/5 m-4"
  }, [_c("vx-card", {
    staticClass: "pt-8 pb-8"
  }, [_c("img", {
    staticStyle: {
      width: "200px",
      margin: "auto"
    },
    attrs: {
      src: "/static/logo.png",
      alt: ""
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "vx-card__title mb-6 mt-8"
  }, [_c("p", {
    staticClass: "mb-1"
  }, [_vm._v("Bạn đã được mời tham gia")]), _vm._v(" "), _c("h3", [_vm._v(_vm._s(_vm.room.title))])]), _vm._v(" "), _c("div", {
    staticClass: "subscription"
  }, [_c("div", {
    staticClass: "mb-3"
  }, [_c("label", {
    staticClass: "vs-input--label",
    attrs: {
      "for": ""
    }
  }, [_vm._v("Tên truy cập")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.input.name,
      expression: "input.name"
    }],
    staticClass: "vs-inputx vs-input--input normal mt-1",
    attrs: {
      type: "text"
    },
    domProps: {
      value: _vm.input.name
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.input, "name", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.error.name))])]), _vm._v(" "), _vm.room.pass ? _c("div", {
    staticClass: "mb-5"
  }, [_c("label", {
    staticClass: "vs-input--label",
    attrs: {
      "for": ""
    }
  }, [_vm._v("Mã truy cập")]), _vm._v(" "), _c("input", {
    directives: [{
      name: "model",
      rawName: "v-model",
      value: _vm.input.pass,
      expression: "input.pass"
    }],
    staticClass: "vs-inputx vs-input--input normal mt-1",
    attrs: {
      type: "text"
    },
    domProps: {
      value: _vm.input.pass
    },
    on: {
      input: function input($event) {
        if ($event.target.composing) return;
        _vm.$set(_vm.input, "pass", $event.target.value);
      }
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.error.pass))])]) : _vm._e(), _vm._v(" "), _c("vs-button", {
    staticClass: "w-full",
    on: {
      click: function click($event) {
        return _vm.join();
      }
    }
  }, [_vm._v(" Tham gia cuộc họp")])], 1)])], 1)]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/src/views/rooms/join.vue":
/*!***********************************************!*\
  !*** ./resources/js/src/views/rooms/join.vue ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./join.vue?vue&type=template&id=75350feb */ "./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb");
/* harmony import */ var _join_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./join.vue?vue&type=script&lang=js */ "./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _join_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__["render"],
  _join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/rooms/join.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js":
/*!***********************************************************************!*\
  !*** ./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_join_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./join.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/rooms/join.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_join_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb":
/*!*****************************************************************************!*\
  !*** ./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb ***!
  \*****************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./join.vue?vue&type=template&id=75350feb */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/rooms/join.vue?vue&type=template&id=75350feb");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_join_vue_vue_type_template_id_75350feb__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);