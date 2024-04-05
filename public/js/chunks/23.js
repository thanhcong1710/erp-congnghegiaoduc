(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[23],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {},
  data: function data() {
    return {
      amount_vip: 100000,
      user: {
        is_vip: this.$store.state.AppActiveUser.is_vip,
        end_vip: this.$store.state.AppActiveUser.is_vip,
        num_vip: 0
      },
      popup: {
        active: false,
        company: '',
        contact_name: '',
        phone: '',
        content: '',
        error: ''
      },
      records: [],
      pagination: {
        url: "/api/records/list",
        id: "",
        style: "line",
        "class": "",
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 10,
        pages: [],
        init: 0
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
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p("/api/records/list", {
        pagination: this.pagination
      }).then(function (response) {
        _this.$vs.loading.close();
        _this.records = response.data.list;
        _this.pagination = response.data.paging;
        _this.pagination.init = 1;
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    changePage: function changePage() {
      if (this.pagination.init) {
        this.getData();
      }
    },
    changePageLimit: function changePageLimit(limit) {
      this.pagination.cpage = 1;
      this.pagination.limit = limit;
      this.getData();
    },
    openConfirmDelete: function openConfirmDelete(id, file_name) {
      this.delete_id = id;
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: "Th\xF4ng b\xE1o",
        text: 'Bạn muốn xóa bản ghi lưu trữ phòng họp ' + file_name,
        accept: this.acceptDelete,
        acceptText: 'Xóa',
        cancelText: 'Hủy'
      });
    },
    acceptDelete: function acceptDelete() {
      var _this2 = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].g("/api/records/delete/".concat(this.delete_id)).then(function (response) {
        _this2.$vs.loading.close();
        _this2.getData();
      })["catch"](function (error) {
        console.log(error);
        _this2.$vs.loading.close();
      });
    },
    showModalContact: function showModalContact() {
      this.popup.active = true;
      this.resetModalContact();
    },
    resetModalContact: function resetModalContact() {
      this.popup.company = '';
      this.popup.contact_name = '';
      this.popup.phone = '';
      this.popup.content = '';
      this.popup.error = '';
    },
    addContact: function addContact() {
      var _this3 = this;
      if (!this.popup.phone) {
        this.popup.error = 'Số điện thoại liên hệ không để trống';
        return false;
      } else {
        this.$vs.loading();
        _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p("/api/user/contact", {
          company: this.popup.company,
          contact_name: this.popup.contact_name,
          phone: this.popup.phone,
          note: this.popup.content
        }).then(function (response) {
          _this3.$vs.loading.close();
          _this3.popup.active = false;
          _this3.resetModalContact();
          _this3.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          });
        })["catch"](function (error) {
          console.log(error);
          _this3.$vs.loading.close();
        });
      }
    },
    addPayment: function addPayment() {
      var _this4 = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p("/api/user/payment-add", {
        amount: this.amount_vip
      }).then(function (response) {
        _this4.$vs.loading.close();
      })["catch"](function (error) {
        console.log(error);
        _this4.$vs.loading.close();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true":
/*!********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true ***!
  \********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("section", {
    attrs: {
      id: "pricing-plan"
    }
  }, [_vm._m(0), _vm._v(" "), _c("div", {
    staticClass: "vx-row pricing-card mt-10",
    staticStyle: {
      "justify-content": "center",
      display: "flex",
      width: "100%"
    }
  }, [_c("div", {
    staticClass: "md:w-10/12 w-full"
  }, [_c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    "class": !_vm.user.is_vip ? "vx-col md:w-1/3 w-full mb-4 active" : "vx-col md:w-1/3 w-full mb-4"
  }, [_vm._m(1)]), _vm._v(" "), _c("div", {
    "class": _vm.user.is_vip ? "vx-col md:w-1/3 w-full mb-4 active" : "vx-col md:w-1/3 w-full mb-4"
  }, [_c("div", {
    staticClass: "vx-card text-center"
  }, [_c("div", {
    staticClass: "card-body p-5"
  }, [_c("h3", {
    staticClass: "mt-5",
    staticStyle: {
      color: "#65c600"
    }
  }, [_vm._v("VIP")]), _vm._v(" "), _c("div", {
    staticClass: "annual-plan"
  }, [_c("div", {
    staticClass: "plan-price mt-4 mb-4"
  }, [_c("span", {
    staticClass: "pricing-basic-value font-weight-bolder text-primary"
  }, [_vm._v(_vm._s(_vm._f("formatCurrency")(_vm.amount_vip)))]), _vm._v(" "), _c("sub", {
    staticClass: "font-medium-1 font-weight-bold text-primary"
  }, [_vm._v("đ")]), _vm._v(" "), _c("sub", {
    staticClass: "pricing-duration text-body font-medium-1 font-weight-bold"
  }, [_vm._v("/tháng")])])]), _vm._v(" "), _vm._m(2), _vm._v(" "), _vm.user.end_vip && _vm.user.num_vip > 3 ? _c("div", {
    staticClass: "text-success text-center mb-3"
  }, [_vm._v("(Hạn dùng: " + _vm._s(_vm.user.end_vip) + ")")]) : _vm._e(), _vm._v(" "), _vm.user.end_vip && _vm.user.num_vip <= 3 && _vm.user.num_vip > 0 ? _c("div", {
    staticClass: "text-warning text-center mb-3"
  }, [_vm._v("(Hạn dùng: " + _vm._s(_vm.user.end_vip) + ")")]) : _vm._e(), _vm._v(" "), _vm.user.end_vip && _vm.user.num_vip == 0 ? _c("div", {
    staticClass: "text-danger text-center mb-3"
  }, [_vm._v("(Tài khoản hết hạn)")]) : _vm._e(), _vm._v(" "), _c("vs-button", {
    staticClass: "w-full",
    attrs: {
      color: "primary",
      type: "filled"
    },
    on: {
      click: _vm.addPayment
    }
  }, [_vm._v(_vm._s(_vm.user.end_vip ? "Gia hạn ngay" : "Nâng cấp ngay"))])], 1)])]), _vm._v(" "), _c("div", {
    staticClass: "vx-col md:w-1/3 w-full mb-4"
  }, [_c("div", {
    staticClass: "vx-card text-center"
  }, [_c("div", {
    staticClass: "card-body p-5"
  }, [_c("h3", {
    staticClass: "mt-5",
    attrs: {
      tyle: "color: #ff901c;"
    }
  }, [_vm._v("Doanh Nghiệp")]), _vm._v(" "), _c("p", {
    staticClass: "card-text mt-3",
    staticStyle: {
      "text-align": "justify"
    }
  }, [_vm._v(" Liên hệ với chúng tôi để biết thêm thông tin về mức giá và hơn thế nữa ")]), _vm._v(" "), _vm._m(3), _vm._v(" "), _c("vs-button", {
    staticClass: "w-full",
    attrs: {
      color: "primary",
      type: "border"
    },
    on: {
      click: function click($event) {
        return _vm.showModalContact();
      }
    }
  }, [_vm._v("Liên hệ ngay")])], 1)])])])])]), _vm._v(" "), _c("vs-popup", {
    attrs: {
      title: "Liên hệ tư vấn",
      active: _vm.popup.active
    },
    on: {
      "update:active": function updateActive($event) {
        return _vm.$set(_vm.popup, "active", $event);
      }
    }
  }, [_c("div", {
    staticClass: "vx-col w-full mt-2"
  }, [_c("vs-input", {
    staticClass: "w-full",
    attrs: {
      label: "Tên công ty"
    },
    model: {
      value: _vm.popup.company,
      callback: function callback($$v) {
        _vm.$set(_vm.popup, "company", $$v);
      },
      expression: "popup.company"
    }
  })], 1), _vm._v(" "), _c("div", {
    staticClass: "vx-col w-full mt-2"
  }, [_c("vs-input", {
    staticClass: "w-full",
    attrs: {
      label: "Người liên hệ"
    },
    model: {
      value: _vm.popup.contact_name,
      callback: function callback($$v) {
        _vm.$set(_vm.popup, "contact_name", $$v);
      },
      expression: "popup.contact_name"
    }
  })], 1), _vm._v(" "), _c("div", {
    staticClass: "vx-col w-full mt-2"
  }, [_c("vs-input", {
    staticClass: "w-full",
    attrs: {
      label: "Số điện thoại liên hệ"
    },
    model: {
      value: _vm.popup.phone,
      callback: function callback($$v) {
        _vm.$set(_vm.popup, "phone", $$v);
      },
      expression: "popup.phone"
    }
  })], 1), _vm._v(" "), _c("div", {
    staticClass: "vx-col w-full mt-4"
  }, [_c("vs-textarea", {
    staticClass: "w-full",
    attrs: {
      label: "Ghi chú"
    },
    model: {
      value: _vm.popup.content,
      callback: function callback($$v) {
        _vm.$set(_vm.popup, "content", $$v);
      },
      expression: "popup.content"
    }
  })], 1), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.popup.error))]), _vm._v(" "), _c("div", {
    staticClass: "vx-col w-full mt-5 text-right"
  }, [_c("vs-button", {
    attrs: {
      color: "dark",
      type: "border"
    },
    on: {
      click: function click($event) {
        _vm.popup.active = false;
      }
    }
  }, [_vm._v("Hủy")]), _vm._v(" "), _c("vs-button", {
    attrs: {
      color: "primary",
      type: "filled"
    },
    on: {
      click: function click($event) {
        return _vm.addContact();
      }
    }
  }, [_vm._v("Gửi")])], 1)])], 1);
};
var staticRenderFns = [function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "text-center"
  }, [_c("h1", {
    staticClass: "mt-5"
  }, [_vm._v(" Chính sách giá ")])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "vx-card text-center"
  }, [_c("div", {
    staticClass: "card-body p-5"
  }, [_c("h3", {
    staticClass: "mt-5",
    staticStyle: {
      color: "#07d5c0"
    }
  }, [_vm._v("Miễn Phí")]), _vm._v(" "), _c("div", {
    staticClass: "annual-plan"
  }, [_c("div", {
    staticClass: "plan-price mt-4 mb-4"
  }, [_c("span", {
    staticClass: "pricing-basic-value font-weight-bolder text-primary"
  }, [_vm._v("0")]), _vm._v(" "), _c("sub", {
    staticClass: "font-medium-1 font-weight-bold text-primary"
  }, [_vm._v("đ")]), _vm._v(" "), _c("sub", {
    staticClass: "pricing-duration text-body font-medium-1 font-weight-bold"
  }, [_vm._v("/tháng")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list"
  }, [_c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Cuộc họp video "), _c("span", {
    staticStyle: {
      color: "#d5072a",
      "font-size": "14px"
    }
  }, [_vm._v("không giới hạn thời gian họp")])])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Người tham gia (100 người)")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Ghi lại cuộc họp (lưu trữ 3 ngày)")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Bảng trắng")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Chia sẻ màn hình")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Trình bày slide")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Trò chuyện nhóm")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Bảng tổng kết sau cuộc họp")])])])])])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "vs-list"
  }, [_c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v(" Đầy đủ tính năng của gói miễn phí")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Người tham gia "), _c("span", {
    staticStyle: {
      color: "#d5072a",
      "font-size": "14px"
    }
  }, [_vm._v("không giới hạn")])])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Ghi lại cuộc họp "), _c("span", {
    staticStyle: {
      color: "#d5072a",
      "font-size": "14px"
    }
  }, [_vm._v("lưu trữ không giới hạn")])])])])]);
}, function () {
  var _vm = this,
    _c = _vm._self._c;
  return _c("div", {
    staticClass: "vs-list"
  }, [_c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Đầy đủ tính năng của gói VIP")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("Máy chủ dùng riêng")])])]), _vm._v(" "), _c("div", {
    staticClass: "vs-list--item"
  }, [_c("div", {
    staticClass: "vs-list--icon"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale feather icon-check null",
    staticStyle: {
      "font-size": "20px !important",
      color: "#0fc305"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "list-titles"
  }, [_c("div", {
    staticClass: "vs-list--subtitle"
  }, [_vm._v("API kết nối")])])])]);
}];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "[dir] .vs-list--item[data-v-243617fb]{\n  border-bottom: none;\n}\n.pricing-card .annual-plan .plan-price span[data-v-243617fb] {\n  font-size: 32px;\n  line-height: .8;\n}\n.pricing-card .annual-plan .plan-price sub[data-v-243617fb]{\n  font-size: 16px;\n}\n.pricing-card .annual-plan .plan-price .font-weight-bolder[data-v-243617fb] {\n  font-weight: 600!important;\n}\n.pricing-card .vs-list--subtitle[data-v-243617fb]{\n  font-size: 14px;\n}\n[dir] .vx-row.pricing-card[data-v-243617fb] {\n  margin: 10px 0px;\n}\n[dir=ltr] .vs-list--item .list-titles[data-v-243617fb]{\n  text-align: left;\n}\n[dir=rtl] .vs-list--item .list-titles[data-v-243617fb]{\n  text-align: right;\n}\n[dir] #pricing-plan .vx-col.active .vx-card[data-v-243617fb]{\n  border: 2px solid rgba(var(--vs-primary), 1) ;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--7-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader??ref--7-1!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/vue-loader/lib??vue-loader-options!./upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./resources/js/src/views/users/upgrade.vue":
/*!**************************************************!*\
  !*** ./resources/js/src/views/users/upgrade.vue ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./upgrade.vue?vue&type=template&id=243617fb&scoped=true */ "./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true");
/* harmony import */ var _upgrade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./upgrade.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css */ "./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _upgrade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__["render"],
  _upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "243617fb",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/upgrade.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js":
/*!**************************************************************************!*\
  !*** ./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./upgrade.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css ***!
  \**********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader??ref--7-1!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/vue-loader/lib??vue-loader-options!./upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=style&index=0&id=243617fb&scoped=true&lang=css");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_7_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_style_index_0_id_243617fb_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true":
/*!********************************************************************************************!*\
  !*** ./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true ***!
  \********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./upgrade.vue?vue&type=template&id=243617fb&scoped=true */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/upgrade.vue?vue&type=template&id=243617fb&scoped=true");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_upgrade_vue_vue_type_template_id_243617fb_scoped_true__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);