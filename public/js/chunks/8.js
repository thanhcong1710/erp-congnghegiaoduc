(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[8],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserSettingsGeneral_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserSettingsGeneral.vue */ "./resources/js/src/views/users/UserSettingsGeneral.vue");
/* harmony import */ var _UserSettingsChangePassword_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserSettingsChangePassword.vue */ "./resources/js/src/views/users/UserSettingsChangePassword.vue");
/* harmony import */ var _UserSettingsInfo_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./UserSettingsInfo.vue */ "./resources/js/src/views/users/UserSettingsInfo.vue");



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    UserSettingsGeneral: _UserSettingsGeneral_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    UserSettingsChangePassword: _UserSettingsChangePassword_vue__WEBPACK_IMPORTED_MODULE_1__["default"],
    UserSettingsInfo: _UserSettingsInfo_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  data: function data() {
    return {};
  },
  computed: {
    isSmallerScreen: function isSmallerScreen() {
      return this.$store.state.windowWidth < 768;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      old_password: "",
      new_password: "",
      confirm_new_password: "",
      alert: {
        status: '',
        show: false,
        message: ''
      }
    };
  },
  computed: {
    activeUserInfo: function activeUserInfo() {
      return this.$store.state.AppActiveUser;
    },
    validateForm: function validateForm() {
      return !this.errors.any() && this.old_password !== "" && this.new_password !== "" && this.confirm_new_password !== "";
    }
  },
  methods: {
    changePass: function changePass() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p("/api/user/change-password", {
        old_password: this.old_password,
        new_password: this.new_password
      }).then(function (response) {
        _this.$vs.loading.close();
        _this.alert.show = true;
        _this.alert.status = response.data.status;
        _this.alert.message = response.data.message;
        _this.alert.color = response.data.status ? 'success' : 'danger';
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    reset: function reset() {
      this.old_password = "";
      this.new_password = "";
      this.confirm_new_password = "";
    },
    logout: function logout() {
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p('/api/auth/logout', {
        token: localStorage.getItem('accessToken')
      }).then(function (response) {});
      // if user is logged in via auth0
      if (this.$auth.profile) this.$auth.logOut();

      // If JWT login
      if (localStorage.getItem('accessToken')) {
        localStorage.removeItem('accessToken');
        this.$router.push('/pages/login')["catch"](function () {});
      }

      // Change role on logout. Same value as initialRole of acj.js
      this.$acl.change('admin');
      localStorage.removeItem('userInfo');

      // This is just for demo Purpose. If user clicks on logout -> redirect
      this.$router.push('/pages/login')["catch"](function () {});
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js":
/*!**********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js ***!
  \**********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      phone: this.$store.state.AppActiveUser.phone,
      name: this.$store.state.AppActiveUser.name,
      email: this.$store.state.AppActiveUser.email,
      alert: {
        status: '',
        show: false,
        message: ''
      }
    };
  },
  computed: {
    activeUserInfo: function activeUserInfo() {
      console.log();
      return this.$store.state.AppActiveUser;
    }
  },
  methods: {
    updateUser: function updateUser() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p('/api/user/update-info', {
        data: {
          'name': this.name,
          'email': this.email
        }
      }).then(function (response) {
        _this.$store.dispatch('updateUserInfo', response.data.userData);
        _this.$vs.loading.close();
        _this.alert.show = true;
        _this.alert.status = response.data.status;
        _this.alert.message = response.data.message;
        _this.alert.color = response.data.status ? 'success' : 'danger';
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    reset: function reset() {
      this.phone = this.$store.state.AppActiveUser.phone;
      this.name = this.$store.state.AppActiveUser.name;
      this.email = this.$store.state.AppActiveUser.email;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../../http/axios.js */ "./resources/js/src/http/axios.js");
/* harmony import */ var vue_flatpickr_component__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-flatpickr-component */ "./node_modules/vue-flatpickr-component/dist/vue-flatpickr.js");
/* harmony import */ var vue_flatpickr_component__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_flatpickr_component__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flatpickr_dist_flatpickr_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flatpickr/dist/flatpickr.css */ "./node_modules/flatpickr/dist/flatpickr.css");
/* harmony import */ var flatpickr_dist_flatpickr_css__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flatpickr_dist_flatpickr_css__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_3__);




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    flatPickr: vue_flatpickr_component__WEBPACK_IMPORTED_MODULE_1___default.a,
    vSelect: vue_select__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  data: function data() {
    return {
      note: this.$store.state.AppActiveUser.note,
      birthday: this.$store.state.AppActiveUser.birthday,
      address: this.$store.state.AppActiveUser.address,
      gender: this.$store.state.AppActiveUser.gender,
      alert: {
        status: '',
        show: false,
        message: ''
      }
    };
  },
  computed: {
    activeUserInfo: function activeUserInfo() {
      return this.$store.state.AppActiveUser;
    }
  },
  methods: {
    updateUser: function updateUser() {
      var _this = this;
      this.$vs.loading();
      _http_axios_js__WEBPACK_IMPORTED_MODULE_0__["default"].p('/api/user/update-info', {
        data: {
          'note': this.note,
          'birthday': this.birthday,
          'address': this.address,
          'gender': this.gender
        }
      }).then(function (response) {
        _this.$store.dispatch('updateUserInfo', response.data.userData);
        _this.$vs.loading.close();
        _this.alert.show = true;
        _this.alert.status = response.data.status;
        _this.alert.message = response.data.message;
        _this.alert.color = response.data.status ? 'success' : 'danger';
      })["catch"](function (error) {
        console.log(error);
        _this.$vs.loading.close();
      });
    },
    reset: function reset() {
      this.note = this.$store.state.AppActiveUser.note;
      this.birthday = this.$store.state.AppActiveUser.birthday;
      this.address = this.$store.state.AppActiveUser.address;
      this.gender = this.$store.state.AppActiveUser.gender;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf":
/*!*************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf ***!
  \*************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("vs-tabs", {
    key: _vm.isSmallerScreen,
    staticClass: "tabs-shadow-none",
    attrs: {
      position: _vm.isSmallerScreen ? "top" : "left",
      id: "profile-tabs"
    }
  }, [_c("vs-tab", {
    attrs: {
      "icon-pack": "feather",
      icon: "icon-user",
      label: !_vm.isSmallerScreen ? "Tổng Quan" : ""
    }
  }, [_c("div", {
    staticClass: "tab-general md:ml-4 md:mt-0 mt-4 ml-0"
  }, [_c("user-settings-general")], 1)]), _vm._v(" "), _c("vs-tab", {
    attrs: {
      "icon-pack": "feather",
      icon: "icon-lock",
      label: !_vm.isSmallerScreen ? "Đổi Mật Khẩu" : ""
    }
  }, [_c("div", {
    staticClass: "tab-change-pwd md:ml-4 md:mt-0 mt-4 ml-0"
  }, [_c("user-settings-change-password")], 1)]), _vm._v(" "), _c("vs-tab", {
    attrs: {
      "icon-pack": "feather",
      icon: "icon-info",
      label: !_vm.isSmallerScreen ? "Thông Tin" : ""
    }
  }, [_c("div", {
    staticClass: "tab-info md:ml-4 md:mt-0 mt-4 ml-0"
  }, [_c("user-settings-info")], 1)])], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac":
/*!***************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac ***!
  \***************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("vx-card", {
    attrs: {
      "no-shadow": ""
    }
  }, [_c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "required|min:6|max:10",
      expression: "'required|min:6|max:10'"
    }],
    staticClass: "w-full mt-8",
    attrs: {
      "label-placeholder": "Old Password",
      name: "old_password",
      "data-vv-validate-on": "blur",
      "data-vv-as": "Old Password",
      type: "password"
    },
    model: {
      value: _vm.old_password,
      callback: function callback($$v) {
        _vm.old_password = $$v;
      },
      expression: "old_password"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("old_password")))]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "required|min:6|max:10",
      expression: "'required|min:6|max:10'"
    }],
    ref: "new_password",
    staticClass: "w-full mt-8",
    attrs: {
      "label-placeholder": "New Password",
      name: "new_password",
      "data-vv-validate-on": "blur",
      "data-vv-as": "New Password",
      type: "password"
    },
    model: {
      value: _vm.new_password,
      callback: function callback($$v) {
        _vm.new_password = $$v;
      },
      expression: "new_password"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("new_password")))]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "min:6|max:10|confirmed:new_password",
      expression: "'min:6|max:10|confirmed:new_password'"
    }],
    staticClass: "w-full mt-8",
    attrs: {
      "label-placeholder": "Confirm Password",
      name: "confirm_new_password",
      "data-vv-validate-on": "blur",
      "data-vv-as": "New Password",
      type: "password"
    },
    model: {
      value: _vm.confirm_new_password,
      callback: function callback($$v) {
        _vm.confirm_new_password = $$v;
      },
      expression: "confirm_new_password"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm mt-4 mb-4"
  }, [_vm._v(_vm._s(_vm.errors.first("confirm_new_password")))]), _vm._v(" "), _c("vs-alert", {
    staticClass: "mt-4 mb-4",
    attrs: {
      color: _vm.alert.color,
      active: _vm.alert.show,
      closable: "",
      "icon-pack": "feather",
      "close-icon": "icon-x"
    },
    on: {
      "update:active": function updateActive($event) {
        return _vm.$set(_vm.alert, "show", $event);
      }
    }
  }, [_vm._v("\n    " + _vm._s(_vm.alert.message) + " "), _vm.alert.status ? _c("vs-button", {
    staticClass: "ml-20",
    attrs: {
      type: "border",
      color: "success"
    },
    on: {
      click: _vm.logout
    }
  }, [_vm._v("Đăng nhập lại")]) : _vm._e()], 1), _vm._v(" "), _c("div", {
    staticClass: "flex flex-wrap items-center justify-end mt-4"
  }, [_c("vs-button", {
    staticClass: "ml-auto mt-2",
    attrs: {
      disabled: !_vm.validateForm
    },
    on: {
      click: _vm.changePass
    }
  }, [_vm._v("Đổi mật khẩu")]), _vm._v(" "), _c("vs-button", {
    staticClass: "ml-4 mt-2",
    attrs: {
      type: "border",
      color: "warning"
    }
  }, [_vm._v("Hủy")])], 1)], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839":
/*!********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839 ***!
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
  return _c("vx-card", {
    attrs: {
      "no-shadow": ""
    }
  }, [_c("div", {
    staticClass: "flex flex-wrap items-center mb-base"
  }, [_c("vs-avatar", {
    staticClass: "mr-4 mb-4",
    attrs: {
      src: _vm.activeUserInfo.photoURL,
      size: "70px"
    }
  }), _vm._v(" "), _c("div", [_c("vs-button", {
    staticClass: "mr-4 sm:mb-0 mb-2"
  }, [_vm._v("Upload photo")]), _vm._v(" "), _c("vs-button", {
    attrs: {
      type: "border",
      color: "danger"
    }
  }, [_vm._v("Remove")]), _vm._v(" "), _c("p", {
    staticClass: "text-sm mt-2"
  }, [_vm._v("Allowed JPG, GIF or PNG. Max size of 800kB")])], 1)], 1), _vm._v(" "), _c("vs-input", {
    staticClass: "w-full mb-base",
    attrs: {
      "label-placeholder": "Số điện thoại",
      disabled: "true"
    },
    model: {
      value: _vm.phone,
      callback: function callback($$v) {
        _vm.phone = $$v;
      },
      expression: "phone"
    }
  }), _vm._v(" "), _c("vs-input", {
    staticClass: "w-full mb-base",
    attrs: {
      "label-placeholder": "Họ tên"
    },
    model: {
      value: _vm.name,
      callback: function callback($$v) {
        _vm.name = $$v;
      },
      expression: "name"
    }
  }), _vm._v(" "), _c("vs-input", {
    staticClass: "w-full",
    attrs: {
      "label-placeholder": "Email"
    },
    model: {
      value: _vm.email,
      callback: function callback($$v) {
        _vm.email = $$v;
      },
      expression: "email"
    }
  }), _vm._v(" "), _c("vs-alert", {
    staticClass: "mt-4 mb-4",
    attrs: {
      active: _vm.alert.show,
      color: _vm.alert.color,
      closable: "",
      "icon-pack": "feather",
      "close-icon": "icon-x"
    },
    on: {
      "update:active": function updateActive($event) {
        return _vm.$set(_vm.alert, "show", $event);
      }
    }
  }, [_vm._v("\n    " + _vm._s(_vm.alert.message) + "\n  ")]), _vm._v(" "), _c("div", {
    staticClass: "flex flex-wrap items-center justify-end mt-4"
  }, [_c("vs-button", {
    staticClass: "ml-auto mt-2",
    on: {
      click: function click($event) {
        return _vm.updateUser();
      }
    }
  }, [_vm._v("Cập nhật")]), _vm._v(" "), _c("vs-button", {
    staticClass: "ml-4 mt-2",
    attrs: {
      type: "border",
      color: "warning"
    },
    on: {
      click: function click($event) {
        return _vm.reset();
      }
    }
  }, [_vm._v("Hủy")])], 1)], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6":
/*!*****************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6 ***!
  \*****************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function render() {
  var _vm = this,
    _c = _vm._self._c;
  return _c("vx-card", {
    attrs: {
      "no-shadow": ""
    }
  }, [_c("div", {
    staticClass: "mt-5"
  }, [_c("label", {
    staticClass: "text-sm"
  }, [_vm._v("Ngày sinh")]), _vm._v(" "), _c("flat-pickr", {
    staticClass: "w-full mt-2",
    attrs: {
      config: {
        dateFormat: "d/m/Y"
      }
    },
    model: {
      value: _vm.birthday,
      callback: function callback($$v) {
        _vm.birthday = $$v;
      },
      expression: "birthday"
    }
  })], 1), _vm._v(" "), _c("div", {
    staticClass: "mt-5"
  }, [_c("label", {
    staticClass: "text-sm"
  }, [_vm._v("Giới tính")]), _vm._v(" "), _c("div", {
    staticClass: "mt-2"
  }, [_c("vs-radio", {
    staticClass: "mr-4",
    attrs: {
      "vs-value": "male"
    },
    model: {
      value: _vm.gender,
      callback: function callback($$v) {
        _vm.gender = $$v;
      },
      expression: "gender"
    }
  }, [_vm._v("Nam")]), _vm._v(" "), _c("vs-radio", {
    staticClass: "mr-4",
    attrs: {
      "vs-value": "female"
    },
    model: {
      value: _vm.gender,
      callback: function callback($$v) {
        _vm.gender = $$v;
      },
      expression: "gender"
    }
  }, [_vm._v("Nữ")]), _vm._v(" "), _c("vs-radio", {
    attrs: {
      "vs-value": "other"
    },
    model: {
      value: _vm.gender,
      callback: function callback($$v) {
        _vm.gender = $$v;
      },
      expression: "gender"
    }
  }, [_vm._v("Khác")])], 1)]), _vm._v(" "), _c("div", {
    staticClass: "mt-5"
  }, [_c("label", {
    staticClass: "text-sm"
  }, [_vm._v("Địa chỉ")]), _vm._v(" "), _c("vs-input", {
    staticClass: "w-full mt-2",
    attrs: {
      "label-placeholder": ""
    },
    model: {
      value: _vm.address,
      callback: function callback($$v) {
        _vm.address = $$v;
      },
      expression: "address"
    }
  })], 1), _vm._v(" "), _c("div", {
    staticClass: "mt-5"
  }, [_c("label", {
    staticClass: "text-sm"
  }, [_vm._v("Giới thiệu")]), _vm._v(" "), _c("vs-textarea", {
    staticClass: "w-full mt-2",
    attrs: {
      placeholder: ""
    },
    model: {
      value: _vm.note,
      callback: function callback($$v) {
        _vm.note = $$v;
      },
      expression: "note"
    }
  })], 1), _vm._v(" "), _c("vs-alert", {
    staticClass: "mt-4 mb-4",
    attrs: {
      active: _vm.alert.show,
      color: _vm.alert.color,
      closable: "",
      "icon-pack": "feather",
      "close-icon": "icon-x"
    },
    on: {
      "update:active": function updateActive($event) {
        return _vm.$set(_vm.alert, "show", $event);
      }
    }
  }, [_vm._v("\n    " + _vm._s(_vm.alert.message) + "\n  ")]), _vm._v(" "), _c("div", {
    staticClass: "flex flex-wrap items-center justify-end mt-4"
  }, [_c("vs-button", {
    staticClass: "ml-auto mt-2",
    on: {
      click: function click($event) {
        return _vm.updateUser();
      }
    }
  }, [_vm._v("Cập nhật")]), _vm._v(" "), _c("vs-button", {
    staticClass: "ml-4 mt-2",
    attrs: {
      type: "border",
      color: "warning"
    },
    on: {
      click: function click($event) {
        return _vm.reset();
      }
    }
  }, [_vm._v("Hủy")])], 1)], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "[dir] #profile-tabs .vs-tabs--content {\n  padding: 0;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss");

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

/***/ "./resources/js/src/views/users/UserSettings.vue":
/*!*******************************************************!*\
  !*** ./resources/js/src/views/users/UserSettings.vue ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserSettings.vue?vue&type=template&id=66374bdf */ "./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf");
/* harmony import */ var _UserSettings_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserSettings.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss */ "./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _UserSettings_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/UserSettings.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettings.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=style&index=0&id=66374bdf&lang=scss");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_style_index_0_id_66374bdf_lang_scss__WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettings.vue?vue&type=template&id=66374bdf */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettings.vue?vue&type=template&id=66374bdf");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettings_vue_vue_type_template_id_66374bdf__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsChangePassword.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsChangePassword.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac */ "./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac");
/* harmony import */ var _UserSettingsChangePassword_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserSettingsChangePassword.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UserSettingsChangePassword_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/UserSettingsChangePassword.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsChangePassword_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsChangePassword.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsChangePassword_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsChangePassword.vue?vue&type=template&id=a4a5d6ac");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsChangePassword_vue_vue_type_template_id_a4a5d6ac__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsGeneral.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsGeneral.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserSettingsGeneral.vue?vue&type=template&id=70df6839 */ "./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839");
/* harmony import */ var _UserSettingsGeneral_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserSettingsGeneral.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UserSettingsGeneral_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/UserSettingsGeneral.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsGeneral_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsGeneral.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsGeneral_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839":
/*!********************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839 ***!
  \********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsGeneral.vue?vue&type=template&id=70df6839 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsGeneral.vue?vue&type=template&id=70df6839");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsGeneral_vue_vue_type_template_id_70df6839__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsInfo.vue":
/*!***********************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsInfo.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserSettingsInfo.vue?vue&type=template&id=570ddba6 */ "./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6");
/* harmony import */ var _UserSettingsInfo_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserSettingsInfo.vue?vue&type=script&lang=js */ "./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UserSettingsInfo_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/users/UserSettingsInfo.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js":
/*!***********************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsInfo_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsInfo.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsInfo_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6":
/*!*****************************************************************************************!*\
  !*** ./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6 ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserSettingsInfo.vue?vue&type=template&id=570ddba6 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/users/UserSettingsInfo.vue?vue&type=template&id=570ddba6");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_UserSettingsInfo_vue_vue_type_template_id_570ddba6__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);