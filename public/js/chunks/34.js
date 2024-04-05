(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[34],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js":
/*!****************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js ***!
  \****************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {},
  data: function data() {
    return {
      payment: {
        amount: '100000',
        code: '',
        status: ''
      }
    };
  },
  created: function created() {
    this.getData();
  },
  methods: {
    getData: function getData() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].g("/api/user/payments/".concat(this.$route.params.id)).then(function (response) {
        _this.$vs.loading.close();
        _this.payment = response.data;
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    paySuccess: function paySuccess() {
      var _this2 = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p("/api/user/payment-transfer", {
        id: this.$route.params.id
      }).then(function (response) {
        _this2.$vs.loading.close();
        _this2.payment.status = 1;
        _this2.$vs.notify({
          title: 'Thành công',
          text: response.data.message,
          iconPack: 'feather',
          icon: 'icon-check-circle',
          color: 'success'
        });
      })["catch"](function (error) {
        console.log(error);
        _this2.$vs.loading.close();
      });
    },
    copyText: function copyText(textCopy, message) {
      var thisIns = this;
      this.$copyText(textCopy).then(function () {
        thisIns.$vs.notify({
          title: 'Copy',
          text: message,
          color: 'success',
          iconPack: 'feather',
          icon: 'icon-check-circle'
        });
      }, function () {
        thisIns.$vs.notify({
          title: 'Failed',
          text: 'Error in copying text',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        });
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4":
/*!**************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4 ***!
  \**************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", [_c("vx-card", {
    staticClass: "text-center",
    staticStyle: {
      "max-width": "480px",
      margin: "auto"
    },
    attrs: {
      "card-background": "linear-gradient(120deg, #7f7fd5, #86a8e7, #91eae4)"
    }
  }, [_c("h5", {
    staticStyle: {
      "font-size": "24px",
      color: "#fff"
    }
  }, [_vm._v("THÔNG TIN CHUYỂN KHOẢN")]), _vm._v(" "), _c("h2", {
    staticClass: "mt-5 mb-8",
    staticStyle: {
      "font-size": "48px",
      color: "#fff"
    }
  }, [_vm._v(_vm._s(_vm._f("formatCurrency")(_vm.payment.amount)))]), _vm._v(" "), _c("div", {
    staticStyle: {
      padding: "20px",
      "font-size": "16px",
      background: "#fff",
      color: "#110111",
      "border-radius": "6px"
    }
  }, [_c("img", {
    attrs: {
      width: "100%",
      src: "https://img.vietqr.io/image/VPB-64308987-compact.png?amount=" + _vm.payment.amount + "&addInfo=" + _vm.payment.code + "&accountName=LUONG THANH CONG"
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-3"
  }, [_c("span", {
    staticStyle: {
      "margin-top": "6px"
    }
  }, [_vm._v("Ngân hàng")]), _vm._v(" "), _c("span", [_c("img", {
    attrs: {
      src: "/static/img/logo_vpb.svg",
      height: "25px"
    }
  })])]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-3",
    on: {
      click: function click($event) {
        return _vm.copyText("64308987", "64308987");
      }
    }
  }, [_c("span", [_vm._v("Số tài khoản:")]), _vm._v(" "), _c("span", [_c("strong", {
    staticStyle: {
      "font-weight": "500"
    }
  }, [_vm._v("64308987")]), _vm._v(" "), _c("feather-icon", {
    staticStyle: {
      cursor: "pointer",
      "margin-left": "8px"
    },
    attrs: {
      icon: "CopyIcon",
      svgClasses: "h-5 w-5"
    }
  })], 1)]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-3",
    on: {
      click: function click($event) {
        return _vm.copyText("LUONG THANH CONG", "LUONG THANH CONG");
      }
    }
  }, [_c("span", [_vm._v("Chủ tài khoản:")]), _vm._v(" "), _c("span", [_c("strong", {
    staticStyle: {
      "font-weight": "500"
    }
  }, [_vm._v("LUONG THANH CONG")]), _vm._v(" "), _c("feather-icon", {
    staticStyle: {
      cursor: "pointer",
      "margin-left": "8px"
    },
    attrs: {
      icon: "CopyIcon",
      svgClasses: "h-5 w-5"
    }
  })], 1)]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-3",
    on: {
      click: function click($event) {
        _vm.copyText(_vm.payment.amount.toString(), _vm.payment.amount.toString());
      }
    }
  }, [_c("span", [_vm._v("Số tiền chuyển:")]), _vm._v(" "), _c("span", [_c("strong", {
    staticStyle: {
      "font-weight": "500"
    }
  }, [_vm._v(_vm._s(_vm._f("formatCurrency")(_vm.payment.amount)))]), _vm._v(" "), _c("feather-icon", {
    staticStyle: {
      cursor: "pointer",
      "margin-left": "8px"
    },
    attrs: {
      icon: "CopyIcon",
      svgClasses: "h-5 w-5"
    }
  })], 1)]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-3",
    on: {
      click: function click($event) {
        return _vm.copyText(_vm.payment.code, _vm.payment.code);
      }
    }
  }, [_c("span", [_vm._v("Nội dung chuyển:")]), _vm._v(" "), _c("span", [_c("strong", {
    staticStyle: {
      "font-weight": "500"
    }
  }, [_vm._v(_vm._s(_vm.payment.code))]), _vm._v(" "), _c("feather-icon", {
    staticStyle: {
      cursor: "pointer",
      "margin-left": "8px"
    },
    attrs: {
      icon: "CopyIcon",
      svgClasses: "h-5 w-5"
    }
  })], 1)]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between flex-wrap mt-5"
  }, [_c("span", [_vm._v("Trạng thái:")]), _vm._v(" "), _c("span", [_c("strong", {
    "class": _vm.payment.status == 2 ? "text-success" : _vm.payment.status == 1 ? "text-warning" : "text-primary",
    staticStyle: {
      "font-weight": "500"
    }
  }, [_vm._v(_vm._s(_vm.payment.status == 2 ? "hoàn thành" : _vm.payment.status == 1 ? "chờ xác nhận" : "chưa chuyển tiền"))])])]), _vm._v(" "), _c("div", [_vm.payment.status != 2 ? _c("vs-button", {
    staticClass: "w-full mt-4",
    attrs: {
      color: "success"
    },
    on: {
      click: _vm.paySuccess
    }
  }, [_vm._v("Đã chuyển khoản")]) : _vm._e()], 1)])]), _vm._v(" "), _vm._m(0)], 1);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticStyle: {
      margin: "auto",
      "margin-top": "25px",
      "max-width": "680px",
      "text-align": "justify"
    }
  }, [_c("ul", {
    staticStyle: {
      "list-style-type": "disc"
    }
  }, [_c("li", [_c("i", [_vm._v("Vui lòng chuyển khoản 24/7 và đảm bảo đúng nội dung chuyển khoản để giao dịch được xử lý nhanh nhất.")])]), _vm._v(" "), _c("li", [_c("i", [_vm._v("Khi chuyển khoản, vui lòng chọn hình thức Người chuyển trả phí để chúng tôi nhận được chính xác số tiền đã chuyển.")])]), _vm._v(" "), _c("li", [_c("i", [_vm._v("Sau khi chuyển khoản, vui lòng chọn xác nhận đã chuyển khoản.")])])])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/src/views/users/paymentDetail.vue":
/*!********************************************************!*\
  !*** ./resources/js/src/views/users/paymentDetail.vue ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./paymentDetail.vue?vue&type=template&id=9c3a3dd4 */ "./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4");
/* harmony import */ var _paymentDetail_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./paymentDetail.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _paymentDetail_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__["render"],
  _paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/paymentDetail.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js":
/*!********************************************************************************!*\
  !*** ./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_paymentDetail_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./paymentDetail.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/paymentDetail.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_paymentDetail_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4 ***!
  \**************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./paymentDetail.vue?vue&type=template&id=9c3a3dd4 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/paymentDetail.vue?vue&type=template&id=9c3a3dd4");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_paymentDetail_vue_vue_type_template_id_9c3a3dd4__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);