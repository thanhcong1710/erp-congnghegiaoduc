(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[20],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js":
/*!*************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js ***!
  \*************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _template_quiz_type1_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./template_quiz/type1.vue */ "./resources/js/src/views/guest/tests/template_quiz/type1.vue");
/* harmony import */ var _template_quiz_type2_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./template_quiz/type2.vue */ "./resources/js/src/views/guest/tests/template_quiz/type2.vue");
/* harmony import */ var _template_quiz_type3_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./template_quiz/type3.vue */ "./resources/js/src/views/guest/tests/template_quiz/type3.vue");
/* harmony import */ var _template_quiz_type4_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./template_quiz/type4.vue */ "./resources/js/src/views/guest/tests/template_quiz/type4.vue");
/* harmony import */ var _template_quiz_type5_vue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./template_quiz/type5.vue */ "./resources/js/src/views/guest/tests/template_quiz/type5.vue");
/* harmony import */ var _template_quiz_type6_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./template_quiz/type6.vue */ "./resources/js/src/views/guest/tests/template_quiz/type6.vue");
/* harmony import */ var _template_quiz_type7_vue__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./template_quiz/type7.vue */ "./resources/js/src/views/guest/tests/template_quiz/type7.vue");
/* harmony import */ var _template_quiz_type8_vue__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./template_quiz/type8.vue */ "./resources/js/src/views/guest/tests/template_quiz/type8.vue");
/* harmony import */ var _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @chenfengyuan/vue-countdown */ "./node_modules/@chenfengyuan/vue-countdown/dist/vue-countdown.js");
/* harmony import */ var _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../../http/axios.js */ "./resources/js/src/http/axios.js");










/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    'countdown': _chenfengyuan_vue_countdown__WEBPACK_IMPORTED_MODULE_8___default.a,
    TemplateType1: _template_quiz_type1_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    TemplateType2: _template_quiz_type2_vue__WEBPACK_IMPORTED_MODULE_1__["default"],
    TemplateType3: _template_quiz_type3_vue__WEBPACK_IMPORTED_MODULE_2__["default"],
    TemplateType4: _template_quiz_type4_vue__WEBPACK_IMPORTED_MODULE_3__["default"],
    TemplateType5: _template_quiz_type5_vue__WEBPACK_IMPORTED_MODULE_4__["default"],
    TemplateType6: _template_quiz_type6_vue__WEBPACK_IMPORTED_MODULE_5__["default"],
    TemplateType7: _template_quiz_type7_vue__WEBPACK_IMPORTED_MODULE_6__["default"],
    TemplateType8: _template_quiz_type8_vue__WEBPACK_IMPORTED_MODULE_7__["default"]
  },
  data: function data() {
    return {
      context: {
        componentParent: this
      },
      count_quiz_answer: 0,
      countdownTest: false,
      test: {},
      quizs: []
    };
  },
  created: function created() {
    this.getTestSessionInfo();
  },
  methods: {
    getTestSessionInfo: function getTestSessionInfo() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_9__["default"].g("/api/test/session-info/".concat(this.$route.params.code)).then(function (response) {
        _this.$vs.loading.close();
        if (response.data.status) {
          _this.test = response.data.data;
          _this.countdownTest = true;
          _this.getDataQuiz(_this.test.test_id, _this.test.test_session_id);
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
    getDataQuiz: function getDataQuiz(test_id, test_session_id) {
      var _this2 = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_9__["default"].g("/api/test/session-quiz/".concat(test_id, "/").concat(test_session_id)).then(function (response) {
        _this2.$vs.loading.close();
        _this2.quizs = response.data;
        _this2.getTextCountQuiz();
        setTimeout(function () {
          MathJax.typeset();
        }, 300);
      })["catch"](function (error) {
        console.log(error);
        _this2.$vs.loading.close();
      });
    },
    onCountdownEnd: function onCountdownEnd() {
      alert('123');
    },
    endTest: function endTest() {
      var quiz_not_answer = 0;
      this.quizs.forEach(function (item) {
        if (!item.quiz_info.user_answer) {
          quiz_not_answer++;
        }
      });
      this.openConfirmSubmitTest(quiz_not_answer);
    },
    openConfirmSubmitTest: function openConfirmSubmitTest(quiz_not_answer) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: "N\u1ED9p b\xE0i",
        text: quiz_not_answer ? 'Còn ' + quiz_not_answer + ' câu chưa làm và chưa hết thời làm bạn vẫn muốn nộp bài?' : 'Chưa hết thời làm bạn vẫn muốn nộp bài?',
        accept: this.submitTest,
        acceptText: 'Nộp tiền',
        cancelText: 'Hủy'
      });
    },
    submitTest: function submitTest() {
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_9__["default"].p("/api/test/end", {
        test_session_id: this.test.test_session_id,
        quizs: this.quizs
      }).then(function (response) {
        window.location.href = response.data.redirect_url;
      })["catch"](function (error) {
        console.log(error);
      });
    },
    getTextCountQuiz: function getTextCountQuiz() {
      var count_quiz_answer = 0;
      this.quizs.forEach(function (item) {
        if (item.quiz_info.user_answer) {
          count_quiz_answer++;
        }
      });
      this.count_quiz_answer = count_quiz_answer;
    },
    answerQuiz: function answerQuiz(data) {
      this.getTextCountQuiz();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_9__["default"].p("/api/test/answer-quiz", {
        test_session_id: this.test.test_session_id,
        test_quiz_id: data.id,
        quiz_id: data.quiz_info.id,
        quiz_type: data.quiz_type,
        answer: data.quiz_info.user_answer
      }).then(function (response) {})["catch"](function (error) {
        console.log(error);
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154":
/*!***********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154 ***!
  \***********************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "h-screen flex w-full"
  }, [_c("vs-row", {
    attrs: {
      "vs-w": "12"
    }
  }, [_c("vs-col", {
    staticClass: "p-3",
    attrs: {
      "vs-type": "flex",
      "vs-justify": "center",
      "vs-align": "top",
      "vs-lg": "9",
      "vs-sm": "12"
    }
  }, [_c("vx-card", {
    attrs: {
      "no-shadow": ""
    }
  }, [_c("div", {
    staticClass: "vs-component p-5"
  }, [_c("h3", {
    staticClass: "text-center mb-5"
  }, [_vm._v(_vm._s(_vm.test.title))]), _vm._v(" "), _vm._l(_vm.quizs, function (item, index) {
    return _c("div", {
      key: index,
      staticClass: "ques-item"
    }, [item.quiz_info.parent && item.quiz_info.parent.noi_dung ? _c("div", {
      staticClass: "ques-parent",
      domProps: {
        innerHTML: _vm._s(item.quiz_info.parent.noi_dung)
      }
    }) : _vm._e(), _vm._v(" "), _c("div", {
      staticClass: "vs-component"
    }, [_c("span", {
      staticClass: "con-slot-label"
    }, [_c("strong", {
      staticStyle: {
        "font-size": "16px",
        "font-weight": "600"
      }
    }, [_vm._v("Câu\n                    " + _vm._s(index + 1))])])]), _vm._v(" "), _c("div", {
      staticClass: "mb-base"
    }, [item.quiz_info.type_view == 1 ? _c("template-type-1", {
      attrs: {
        item: item,
        context: _vm.context
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 2 ? _c("template-type-2", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 3 ? _c("template-type-3", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 4 ? _c("template-type-4", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 5 ? _c("template-type-5", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 6 ? _c("template-type-6", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 7 ? _c("template-type-7", {
      attrs: {
        item: item
      }
    }) : _vm._e(), _vm._v(" "), item.quiz_info.type_view == 8 ? _c("template-type-8", {
      attrs: {
        item: item
      }
    }) : _vm._e()], 1)]);
  })], 2)])], 1), _vm._v(" "), _c("vs-col", {
    staticClass: "p-3",
    attrs: {
      "vs-type": "flex",
      "vs-justify": "center",
      "vs-align": "top",
      "vs-lg": "3",
      "vs-sm": "12"
    }
  }, [_c("vx-card", {
    staticClass: "test-overview",
    attrs: {
      "no-shadow": ""
    }
  }, [_c("div", {
    staticClass: "text-center"
  }, [_c("vs-col", {
    staticClass: "p-2 pt-3",
    staticStyle: {
      "border-right": "1px solid #ccc"
    },
    attrs: {
      "vs-justify": "center",
      "vs-align": "center",
      "vs-lg": "6"
    }
  }, [_vm._v("\n              Số câu đã làm "), _c("br"), _vm._v(" "), _c("span", {
    staticStyle: {
      "font-size": "30px"
    }
  }, [_vm._v(_vm._s(_vm.count_quiz_answer) + "/" + _vm._s(_vm.quizs.length))])]), _vm._v(" "), _c("vs-col", {
    staticClass: "p-2 pt-3",
    attrs: {
      "vs-justify": "center",
      "vs-align": "center",
      "vs-lg": "6"
    }
  }, [_vm._v("\n              Thời gian còn lại "), _c("br"), _vm._v(" "), _c("span", {
    staticStyle: {
      "font-size": "30px"
    }
  }, [_vm.countdownTest ? _c("countdown", {
    attrs: {
      time: _vm.test.left_time * 1000
    },
    on: {
      end: _vm.onCountdownEnd
    },
    scopedSlots: _vm._u([{
      key: "default",
      fn: function fn(_ref) {
        var minutes = _ref.minutes,
          seconds = _ref.seconds;
        return [_vm._v("\n                  " + _vm._s(minutes > 9 ? minutes : "0" + minutes) + ":" + _vm._s(seconds > 9 ? seconds : "0" + seconds) + "\n                ")];
      }
    }], null, false, 1443387338)
  }) : _vm._e()], 1)]), _vm._v(" "), _c("hr"), _vm._v(" "), _c("div", {
    staticClass: "text-left box-select-quiz"
  }, _vm._l(_vm.quizs, function (item, index) {
    return _c("div", {
      key: index,
      "class": item.quiz_info.user_answer ? "quiz-icon primary" : "quiz-icon"
    }, [_vm._v(_vm._s(index + 1))]);
  }), 0), _vm._v(" "), _c("div", {
    staticClass: "text-center box-action pr-3 pl-3"
  }, [_c("vs-button", {
    staticClass: "w-full",
    attrs: {
      color: "warning",
      type: "filled"
    },
    on: {
      click: function click($event) {
        return _vm.endTest();
      }
    }
  }, [_vm._v("NỘP BÀI")])], 1)], 1)])], 1)], 1)], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/src/views/guest/tests/take.vue":
/*!*****************************************************!*\
  !*** ./resources/js/src/views/guest/tests/take.vue ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./take.vue?vue&type=template&id=fcd43154 */ "./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154");
/* harmony import */ var _take_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./take.vue?vue&type=script&lang=js */ "./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _take_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__["render"],
  _take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/guest/tests/take.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js":
/*!*****************************************************************************!*\
  !*** ./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_take_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./take.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/guest/tests/take.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_take_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154":
/*!***********************************************************************************!*\
  !*** ./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154 ***!
  \***********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./take.vue?vue&type=template&id=fcd43154 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/guest/tests/take.vue?vue&type=template&id=fcd43154");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_take_vue_vue_type_template_id_fcd43154__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);