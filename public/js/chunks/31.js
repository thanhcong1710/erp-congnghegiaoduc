(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[31],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {},
  data: function data() {
    return {
      subjects: [],
      grade_info: ''
    };
  },
  methods: {
    getData: function getData() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p('/api/quizs/subjects', {
        grade_id: this.$route.params.id
      }).then(function (response) {
        _this.subjects = response.data.list;
        _this.grade_info = response.data.grade_info;
        _this.$vs.loading.close();
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    redirectSubjectDetail: function redirectSubjectDetail(id) {
      this.$router.push({
        name: 'subject',
        params: {
          id: id
        }
      })["catch"](function () {});
    }
  },
  created: function created() {
    this.getData();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8":
/*!******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8 ***!
  \******************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", [_c("div", {
    staticClass: "router-header flex flex-wrap items-center mb-6"
  }, [_c("div", {
    staticClass: "vx-breadcrumb ml-4"
  }, [_c("ul", {
    staticClass: "flex flex-wrap items-center"
  }, [_c("li", {
    staticClass: "inline-flex items-end"
  }, [_c("router-link", {
    attrs: {
      to: "/"
    }
  }, [_c("feather-icon", {
    attrs: {
      icon: "HomeIcon",
      svgClasses: "h-5 w-5 mb-1 stroke-current text-primary"
    }
  })], 1), _vm._v(" "), _c("span", {
    staticClass: "breadcrumb-separator mx-2"
  }, [_c("feather-icon", {
    attrs: {
      icon: _vm.$vs.trl ? "ChevronsLeftIcon" : "ChevronsRightIcon",
      svgClasses: "w-4 h-4"
    }
  })], 1)], 1), _vm._v(" "), _c("li", {
    staticClass: "inline-flex items-center"
  }, [_c("span", {
    staticClass: "cursor-default"
  }, [_vm._v("Kho bài tập")]), _vm._v(" "), _c("span", {
    staticClass: "breadcrumb-separator mx-2 flex items-start"
  }, [_c("feather-icon", {
    attrs: {
      icon: _vm.$vs.trl ? "ChevronsLeftIcon" : "ChevronsRightIcon",
      svgClasses: "w-4 h-4"
    }
  })], 1)]), _vm._v(" "), _c("li", {
    staticClass: "inline-flex"
  }, [_c("span", {
    staticClass: "cursor-default"
  }, [_vm._v(_vm._s(_vm.grade_info.name))])])])])]), _vm._v(" "), _c("div", {
    attrs: {
      id: "page-subjects"
    }
  }, [_c("div", {
    staticClass: "vx-row"
  }, _vm._l(_vm.subjects, function (item) {
    return _c("div", {
      key: item.id,
      staticClass: "vx-col w-full md:w-1/3 sm:w-1/2 mb-base",
      on: {
        click: function click($event) {
          return _vm.redirectSubjectDetail(item.id);
        }
      }
    }, [_c("vx-card", {
      staticClass: "text-center cursor-pointer"
    }, [_c("h4", {
      staticClass: "mb-2"
    }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), _c("small", [_c("strong", [_vm._v(_vm._s(item.chapter_count))]), _vm._v(" chủ đề "), _c("strong", [_vm._v(_vm._s(item.question_count))]), _vm._v(" câu\n            hỏi")])])], 1);
  }), 0)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/src/views/quizs/grade.vue":
/*!************************************************!*\
  !*** ./resources/js/src/views/quizs/grade.vue ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grade.vue?vue&type=template&id=959c85a8 */ "./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8");
/* harmony import */ var _grade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grade.vue?vue&type=script&lang=js */ "./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__["render"],
  _grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/quizs/grade.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js":
/*!************************************************************************!*\
  !*** ./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./grade.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/quizs/grade.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8":
/*!******************************************************************************!*\
  !*** ./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8 ***!
  \******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./grade.vue?vue&type=template&id=959c85a8 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/quizs/grade.vue?vue&type=template&id=959c85a8");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_grade_vue_vue_type_template_id_959c85a8__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);