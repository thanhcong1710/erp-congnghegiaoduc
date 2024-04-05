(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[83],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-form-wizard */ "./node_modules/vue-form-wizard/dist/vue-form-wizard.js");
/* harmony import */ var vue_form_wizard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-form-wizard/dist/vue-form-wizard.min.css */ "./node_modules/vue-form-wizard/dist/vue-form-wizard.min.css");
/* harmony import */ var vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_form_wizard_dist_vue_form_wizard_min_css__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _pages_register_RegisterJWT_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./../pages/register/RegisterJWT.vue */ "./resources/js/src/views/pages/register/RegisterJWT.vue");
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./../../http/axios.js */ "./resources/js/src/http/axios.js");
/* harmony import */ var _router__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @/router */ "./resources/js/src/router.js");



var ItemListView = function ItemListView() {
  return __webpack_require__.e(/*! import() */ 17).then(__webpack_require__.bind(null, /*! ./components/ItemListView.vue */ "./resources/js/src/views/law/components/ItemListView.vue"));
};


/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      displayName: '',
      phone: '',
      password: '',
      confirm_password: '',
      isTermsConditionAccepted: true,
      // TAB 2
      fullName: '',
      mobileNum: '',
      pincode: '',
      houseNum: '',
      area: '',
      landmark: '',
      city: '',
      state: '',
      addressType: 1,
      addressTypeOptions: [{
        text: 'Home',
        value: 1
      }, {
        text: 'Office',
        value: 2
      }],
      // TAB 3
      paymentMethod: 'debit-card',
      cvv: ''
    };
  },
  computed: {
    cartItems: function cartItems() {
      return this.$store.state.eCommerce.cartItems.slice().reverse();
    },
    isInWishList: function isInWishList() {
      var _this = this;
      return function (itemId) {
        return _this.$store.getters['eCommerce/isInWishList'](itemId);
      };
    },
    activeUserInfo: function activeUserInfo() {
      return this.$store.state.AppActiveUser;
    },
    validateForm: function validateForm() {
      return !this.errors.any() && this.displayName !== '' && this.phone !== '' && this.password !== '' && this.confirm_password !== '' && this.isTermsConditionAccepted === true;
    }
  },
  methods: {
    // TAB 1
    removeItemFromCart: function removeItemFromCart(item) {
      this.$store.dispatch('eCommerce/toggleItemInCart', item);
    },
    wishListButtonClicked: function wishListButtonClicked(item) {
      // Toggle in Wish List
      if (this.isInWishList(item.objectID)) this.$router.push('/apps/eCommerce/wish-list')["catch"](function () {});else {
        this.toggleItemInWishList(item);
        this.removeItemFromCart(item);
      }
    },
    toggleItemInWishList: function toggleItemInWishList(item) {
      this.$store.dispatch('eCommerce/toggleItemInWishList', item);
    },
    updateItemQuantity: function updateItemQuantity(event, index) {
      var itemIndex = Math.abs(index + 1 - this.cartItems.length);
      this.$store.dispatch('eCommerce/updateItemQuantity', {
        quantity: event,
        index: itemIndex
      });
    },
    addOrder: function addOrder() {
      var _this2 = this;
      if (this.activeUserInfo.displayName) {
        this.$vs.loading();
        _http_axios_js__WEBPACK_IMPORTED_MODULE_3__["default"].p('/api/payment/add', {
          'cartItems': this.cartItems
        }).then(function (response) {
          _this2.$refs.checkoutWizard.nextTab();
          _this2.$store.dispatch('eCommerce/removeAllCart');
          _this2.$vs.loading.close();
        })["catch"](function (error) {
          console.log(error);
          _this2.$vs.loading.close();
        });
      } else {
        this.$refs.checkoutWizard.nextTab();
      }
    },
    // TAB 3
    makePayment: function makePayment() {
      var _this3 = this;
      return new Promise(function () {
        _this3.$validator.validateAll('cvv-form').then(function (result) {
          if (result) {
            // if form have no errors
            _this3.$vs.notify({
              title: 'Success',
              text: 'Payment received successfully',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
          } else {
            _this3.$vs.notify({
              title: 'Error',
              text: 'Please enter valid details',
              color: 'warning',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        });
      });
    }
  },
  components: {
    ItemListView: ItemListView,
    FormWizard: vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__["FormWizard"],
    TabContent: vue_form_wizard__WEBPACK_IMPORTED_MODULE_0__["TabContent"],
    RegisterJwt: _pages_register_RegisterJWT_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  filters: {
    getTotal: function getTotal(items) {
      var total = 0;
      items.forEach(function (element) {
        total = total + element.price * element.quantity;
      });
      var resp = '';
      var number = 0;
      var currency = 'đ';
      if (parseFloat(total) < 100) {
        number = parseFloat(v);
        currency = '%';
        resp = "".concat(number).concat(currency);
      } else {
        number = parseInt(total);
        resp = number > 0 ? "".concat(number.toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').slice(0, -2)).concat(currency) : 0;
      }
      return resp;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      displayName: '',
      phone: '',
      password: '',
      confirm_password: '',
      isTermsConditionAccepted: true
    };
  },
  computed: {
    validateForm: function validateForm() {
      return !this.errors.any() && this.displayName !== '' && this.phone !== '' && this.password !== '' && this.confirm_password !== '' && this.isTermsConditionAccepted === true;
    }
  },
  methods: {
    checkLogin: function checkLogin() {
      // If user is already logged in notify
      if (this.$store.state.auth.isUserLoggedIn()) {
        // Close animation if passed as payload
        // this.$vs.loading.close()

        this.$vs.notify({
          title: 'Login Attempt',
          text: 'You are already logged in!',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'warning'
        });
        return false;
      }
      return true;
    },
    registerUserJWt: function registerUserJWt() {
      // If form is not validated or user is already login return
      if (!this.validateForm || !this.checkLogin()) return;
      var payload = {
        userDetails: {
          displayName: this.displayName,
          phone: this.phone,
          password: this.password,
          confirmPassword: this.confirm_password
        },
        notify: this.$vs.notify,
        redirect_url: this.$store.state.eCommerce.cartItems.length > 0 ? '/law/checkout' : ''
      };
      this.$store.dispatch('auth/registerUserJWT', payload);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
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
    attrs: {
      id: "ecommerce-checkout-demo"
    }
  }, [_c("form-wizard", {
    ref: "checkoutWizard",
    attrs: {
      color: "rgba(var(--vs-primary), 1)",
      title: null,
      subtitle: null,
      "hide-buttons": true
    }
  }, [_c("tab-content", {
    staticClass: "mb-5",
    attrs: {
      title: "Giỏ hàng",
      icon: "feather icon-shopping-cart"
    }
  }, [_vm.cartItems.length ? _c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col lg:w-2/3 w-full relative"
  }, _vm._l(_vm.cartItems, function (item, index) {
    return _c("div", {
      key: item.objectID,
      staticClass: "items-list-view"
    }, [_c("item-list-view", {
      staticClass: "mb-base",
      attrs: {
        item: item
      }
    }, [_c("template", {
      slot: "item-meta"
    }, [_c("h6", {
      staticClass: "item-name font-semibold mb-1 cursor-pointer hover:text-primary",
      on: {
        click: function click($event) {
          _vm.$router.push({
            name: "law-item-detail-view",
            params: {
              item_id: item.objectID
            }
          })["catch"](function () {});
        }
      }
    }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), _c("p", {
      staticClass: "item-description text-sm text-justify"
    }, [_vm._v(_vm._s(item.intro_text))]), _vm._v(" "), _c("p", {
      staticClass: "mt-4 font-bold text-sm"
    }, [_vm._v("Quantity")]), _vm._v(" "), _c("vs-input-number", {
      staticClass: "inline-flex",
      attrs: {
        min: "1",
        max: "10",
        value: item.quantity
      },
      on: {
        input: function input($event) {
          return _vm.updateItemQuantity($event, index);
        }
      }
    })], 1), _vm._v(" "), _c("template", {
      slot: "action-buttons"
    }, [_c("div", {
      staticClass: "item-view-primary-action-btn p-3 rounded-lg flex flex-grow items-center justify-center cursor-pointer mb-3",
      on: {
        click: function click($event) {
          return _vm.removeItemFromCart(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "XIcon",
        svgClasses: "h-4 w-4"
      }
    }), _vm._v(" "), _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("REMOVE")])], 1), _vm._v(" "), _c("div", {
      staticClass: "item-view-secondary-action-btn bg-primary p-3 rounded-lg flex flex-grow items-center justify-center text-white cursor-pointer",
      on: {
        click: function click($event) {
          return _vm.wishListButtonClicked(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "HeartIcon",
        svgClasses: [{
          "text-white fill-current": _vm.isInWishList(item.objectID)
        }, "h-4 w-4"]
      }
    }), _vm._v(" "), _vm.isInWishList(item.objectID) ? _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("WISHLIST")]) : _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("WISHLIST")])], 1)])], 2)], 1);
  }), 0), _vm._v(" "), _c("div", {
    staticClass: "vx-col lg:w-1/3 w-full"
  }, [_c("vx-card", [_c("p", {
    staticClass: "text-grey mb-3"
  }, [_vm._v("Options")]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between"
  }, [_c("span", {
    staticClass: "font-semibold"
  }, [_vm._v("Coupons")]), _vm._v(" "), _c("span", {
    staticClass: "font-medium text-primary cursor-pointer"
  }, [_vm._v("Apply")])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("p", {
    staticClass: "font-semibold mb-3"
  }, [_vm._v("Price Details")]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between mb-2"
  }, [_c("span", {
    staticClass: "text-grey"
  }, [_vm._v("Total MRP")]), _vm._v(" "), _c("span", [_vm._v(_vm._s(_vm._f("getTotal")(_vm.cartItems)))])]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between mb-2"
  }, [_c("span", {
    staticClass: "text-grey"
  }, [_vm._v("Discount")]), _vm._v(" "), _c("span", {
    staticClass: "text-success"
  }, [_vm._v("0đ")])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between font-semibold mb-3"
  }, [_c("span", [_vm._v("Total")]), _vm._v(" "), _c("span", [_vm._v(_vm._s(_vm._f("getTotal")(_vm.cartItems)))])]), _vm._v(" "), _c("vs-button", {
    staticClass: "w-full",
    on: {
      click: function click($event) {
        return _vm.addOrder();
      }
    }
  }, [_vm._v("PLACE ORDER")])], 1)], 1)]) : _c("vx-card", {
    attrs: {
      title: "You don't have any items in your cart."
    }
  }, [_c("vs-button", {
    on: {
      click: function click($event) {
        _vm.$router.push("/law/list")["catch"](function () {});
      }
    }
  }, [_vm._v("Browse Shop")])], 1)], 1), _vm._v(" "), !_vm.activeUserInfo.displayName ? _c("tab-content", {
    staticClass: "mb-5",
    attrs: {
      title: "Tài khoản",
      icon: "feather icon-home"
    }
  }, [_c("div", {
    staticClass: "vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4",
    staticStyle: {
      margin: "auto !important"
    }
  }, [_c("vx-card", [_c("div", {
    staticClass: "full-page-bg-color",
    staticStyle: {
      "padding-bottom": "30px"
    },
    attrs: {
      slot: "no-body"
    },
    slot: "no-body"
  }, [_c("div", {
    staticClass: "vx-row no-gutter"
  }, [_c("div", {
    staticClass: "vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center"
  }, [_c("img", {
    staticClass: "mx-auto",
    attrs: {
      src: __webpack_require__(/*! @assets/images/pages/register.jpg */ "./resources/assets/images/pages/register.jpg"),
      alt: "register"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center d-theme-dark-bg"
  }, [_c("div", {
    staticClass: "px-8 pt-8 register-tabs-container"
  }, [_c("div", {
    staticClass: "vx-card__title mb-4"
  }, [_c("h4", {
    staticClass: "mb-4"
  }, [_vm._v("Create Account")]), _vm._v(" "), _c("p", [_vm._v("Fill the below form to create a new account.")])]), _vm._v(" "), _c("br"), _vm._v(" "), _c("register-jwt")], 1)])])])])], 1)]) : _vm._e(), _vm._v(" "), _c("tab-content", {
    staticClass: "mb-5",
    attrs: {
      title: "Thanh toán",
      icon: "feather icon-credit-card"
    }
  }, [_c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col lg:w-2/3 w-full"
  }, [_c("vx-card", {
    staticClass: "mb-base",
    attrs: {
      title: "Payment Options",
      subtitle: "Be sure to click on correct payment option"
    }
  }, [_c("div", {
    staticClass: "mt-3"
  }, [_c("ul", [_c("li", [_c("div", {
    staticClass: "flex flex-wrap justify-between items-center"
  }, [_c("vs-radio", {
    attrs: {
      "vs-value": "debit-card"
    },
    model: {
      value: _vm.paymentMethod,
      callback: function callback($$v) {
        _vm.paymentMethod = $$v;
      },
      expression: "paymentMethod"
    }
  }, [_c("div", {
    staticClass: "flex items-center"
  }, [_c("img", {
    staticClass: "inline-flex",
    attrs: {
      src: __webpack_require__(/*! @assets/images/pages/eCommerce/bank.png */ "./resources/assets/images/pages/eCommerce/bank.png"),
      alt: "bank-logo",
      height: "40",
      width: "50"
    }
  }), _vm._v(" "), _c("span", [_vm._v("US Unlocked Debit Card 12XX XXXX XXXX 0000")])])]), _vm._v(" "), _c("span", [_vm._v("John Doe")]), _vm._v(" "), _c("span", [_vm._v("11/2020")])], 1), _vm._v(" "), _c("form", {
    attrs: {
      "data-vv-scope": "cvv-form"
    }
  }, [_c("div", {
    staticClass: "flex items-center flex-wrap"
  }, [_c("span", {
    staticClass: "mt-4"
  }, [_vm._v("Enter CVV: ")]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "required|digits:3",
      expression: "'required|digits:3'"
    }],
    staticClass: "mr-3 ml-2 mt-4",
    attrs: {
      name: "cvv"
    },
    model: {
      value: _vm.cvv,
      callback: function callback($$v) {
        _vm.cvv = $$v;
      },
      expression: "cvv"
    }
  }), _vm._v(" "), _c("vs-button", {
    staticClass: "mt-4",
    attrs: {
      disabled: false
    },
    on: {
      click: function click($event) {
        $event.preventDefault();
        return _vm.makePayment.apply(null, arguments);
      }
    }
  }, [_vm._v("CONTINUE")])], 1), _vm._v(" "), _c("span", {
    directives: [{
      name: "show",
      rawName: "v-show",
      value: _vm.errors.has("cvv-form.cvv"),
      expression: "errors.has('cvv-form.cvv')"
    }],
    staticClass: "text-danger ml-24"
  }, [_vm._v(_vm._s(_vm.errors.first("cvv-form.cvv")))])])]), _vm._v(" "), _c("vs-divider", {
    staticClass: "my-6"
  }), _vm._v(" "), _c("li", [_c("vs-radio", {
    attrs: {
      "vs-value": "credit-debit-atmCard"
    },
    model: {
      value: _vm.paymentMethod,
      callback: function callback($$v) {
        _vm.paymentMethod = $$v;
      },
      expression: "paymentMethod"
    }
  }, [_vm._v("Credit / Debit / ATM Card")])], 1), _vm._v(" "), _c("li", {
    staticClass: "mt-2"
  }, [_c("vs-radio", {
    attrs: {
      "vs-value": "netBanking"
    },
    model: {
      value: _vm.paymentMethod,
      callback: function callback($$v) {
        _vm.paymentMethod = $$v;
      },
      expression: "paymentMethod"
    }
  }, [_vm._v("Net Banking")])], 1), _vm._v(" "), _c("li", {
    staticClass: "mt-2"
  }, [_c("vs-radio", {
    attrs: {
      "vs-value": "emi"
    },
    model: {
      value: _vm.paymentMethod,
      callback: function callback($$v) {
        _vm.paymentMethod = $$v;
      },
      expression: "paymentMethod"
    }
  }, [_vm._v("EMI (Easy Installment)")])], 1), _vm._v(" "), _c("li", {
    staticClass: "mt-2"
  }, [_c("vs-radio", {
    attrs: {
      "vs-value": "cashOnDelivery"
    },
    model: {
      value: _vm.paymentMethod,
      callback: function callback($$v) {
        _vm.paymentMethod = $$v;
      },
      expression: "paymentMethod"
    }
  }, [_vm._v("Cash On Delivery")])], 1)], 1), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "inline-flex items-center cursor-pointer"
  }, [_c("feather-icon", {
    staticClass: "mr-2",
    attrs: {
      icon: "PlusSquareIcon",
      svgClasses: "h-6 w-6"
    }
  }), _vm._v(" "), _c("span", [_vm._v("Add Gift Card")])], 1)], 1)])], 1), _vm._v(" "), _c("div", {
    staticClass: "vx-col lg:w-1/3 w-full"
  }, [_c("vx-card", {
    attrs: {
      title: "Price Details"
    }
  }, [_c("div", {
    staticClass: "flex justify-between mb-2"
  }, [_c("span", [_vm._v("Price 3 Items")]), _vm._v(" "), _c("span", {
    staticClass: "font-semibold"
  }, [_vm._v("$699.30")])]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between mb-2"
  }, [_c("span", [_vm._v("Delivery Charges")]), _vm._v(" "), _c("span", {
    staticClass: "text-success"
  }, [_vm._v("Free")])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between"
  }, [_c("span", [_vm._v("Amount Payable")]), _vm._v(" "), _c("span", {
    staticClass: "font-semibold"
  }, [_vm._v("$699.30")])])], 1)], 1)])])], 1)], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6":
/*!*********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6 ***!
  \*********************************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "clearfix"
  }, [_c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "required|min:3",
      expression: "'required|min:3'"
    }],
    staticClass: "w-full",
    attrs: {
      "data-vv-validate-on": "blur",
      "label-placeholder": "Họ tên",
      name: "displayName",
      placeholder: "Họ tên"
    },
    model: {
      value: _vm.displayName,
      callback: function callback($$v) {
        _vm.displayName = $$v;
      },
      expression: "displayName"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("displayName")))]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: {
        required: true,
        regex: /([\+84|84|0]+(3|5|7|8|9))+([0-9]{8})\b/
      },
      expression: "{ required: true, regex: /([\\+84|84|0]+(3|5|7|8|9))+([0-9]{8})\\b/ }"
    }],
    staticClass: "w-full mt-6",
    attrs: {
      "data-vv-validate-on": "blur",
      name: "phone",
      "label-placeholder": "Số điện thoại",
      placeholder: "Số điện thoại"
    },
    model: {
      value: _vm.phone,
      callback: function callback($$v) {
        _vm.phone = $$v;
      },
      expression: "phone"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("phone")))]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "required|min:6|max:10",
      expression: "'required|min:6|max:10'"
    }],
    ref: "password",
    staticClass: "w-full mt-6",
    attrs: {
      type: "password",
      "data-vv-validate-on": "blur",
      name: "password",
      "label-placeholder": "Mật khẩu",
      placeholder: "Mật khẩu"
    },
    model: {
      value: _vm.password,
      callback: function callback($$v) {
        _vm.password = $$v;
      },
      expression: "password"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("password")))]), _vm._v(" "), _c("vs-input", {
    directives: [{
      name: "validate",
      rawName: "v-validate",
      value: "min:6|max:10|confirmed:password",
      expression: "'min:6|max:10|confirmed:password'"
    }],
    staticClass: "w-full mt-6",
    attrs: {
      type: "password",
      "data-vv-validate-on": "blur",
      "data-vv-as": "password",
      name: "confirm_password",
      "label-placeholder": "Mật khẩu",
      placeholder: "Mật khẩu"
    },
    model: {
      value: _vm.confirm_password,
      callback: function callback($$v) {
        _vm.confirm_password = $$v;
      },
      expression: "confirm_password"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "text-danger text-sm"
  }, [_vm._v(_vm._s(_vm.errors.first("confirm_password")))]), _vm._v(" "), _c("vs-checkbox", {
    staticClass: "mt-6",
    model: {
      value: _vm.isTermsConditionAccepted,
      callback: function callback($$v) {
        _vm.isTermsConditionAccepted = $$v;
      },
      expression: "isTermsConditionAccepted"
    }
  }, [_vm._v("I accept the terms & conditions.")]), _vm._v(" "), _c("vs-button", {
    staticClass: "mt-6",
    attrs: {
      type: "border",
      to: "/pages/login"
    }
  }, [_vm._v("Login")]), _vm._v(" "), _c("vs-button", {
    staticClass: "float-right mt-6",
    attrs: {
      disabled: !_vm.validateForm
    },
    on: {
      click: _vm.registerUserJWt
    }
  }, [_vm._v("Register")])], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "#ecommerce-checkout-demo .item-view-primary-action-btn[data-v-e2f54886] {\n  color: #2c2c2c !important;\n}[dir] #ecommerce-checkout-demo .item-view-primary-action-btn[data-v-e2f54886] {\n  background-color: #f6f6f6;\n}\n#ecommerce-checkout-demo .item-name[data-v-e2f54886] {\n  display: -webkit-box;\n  -webkit-box-orient: vertical;\n  overflow: hidden;\n  -webkit-line-clamp: 2;\n}\n[dir] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] {\n  padding-bottom: 0;\n}\n[dir] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] .wizard-header {\n  padding: 0;\n}\n[dir] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] .wizard-tab-content {\n  padding-bottom: 0;\n}\n[dir=ltr] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] .wizard-tab-content {\n  padding-right: 0;\n  padding-left: 0;\n}\n[dir=rtl] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] .wizard-tab-content {\n  padding-left: 0;\n  padding-right: 0;\n}\n[dir] #ecommerce-checkout-demo .vue-form-wizard[data-v-e2f54886] .wizard-tab-content .wizard-tab-container {\n  margin-bottom: 0 !important;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true");

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

/***/ "./resources/assets/images/pages/eCommerce/bank.png":
/*!**********************************************************!*\
  !*** ./resources/assets/images/pages/eCommerce/bank.png ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/images/bank.png?5f6aa47cad6e07be32f187d78f4f1d1c";

/***/ }),

/***/ "./resources/assets/images/pages/register.jpg":
/*!****************************************************!*\
  !*** ./resources/assets/images/pages/register.jpg ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/images/register.jpg?cfd9ef099bfc1f99c6f2970ae05a6a4a";

/***/ }),

/***/ "./resources/js/src/views/law/Checkout.vue":
/*!*************************************************!*\
  !*** ./resources/js/src/views/law/Checkout.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Checkout.vue?vue&type=template&id=e2f54886&scoped=true */ "./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true");
/* harmony import */ var _Checkout_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Checkout.vue?vue&type=script&lang=js */ "./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true */ "./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Checkout_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__["render"],
  _Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "e2f54886",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/law/Checkout.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js":
/*!*************************************************************************!*\
  !*** ./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Checkout.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true ***!
  \**********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=style&index=0&id=e2f54886&lang=scss&scoped=true");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_style_index_0_id_e2f54886_lang_scss_scoped_true__WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true ***!
  \*******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Checkout.vue?vue&type=template&id=e2f54886&scoped=true */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/Checkout.vue?vue&type=template&id=e2f54886&scoped=true");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_Checkout_vue_vue_type_template_id_e2f54886_scoped_true__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/pages/register/RegisterJWT.vue":
/*!***************************************************************!*\
  !*** ./resources/js/src/views/pages/register/RegisterJWT.vue ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RegisterJWT.vue?vue&type=template&id=653637e6 */ "./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6");
/* harmony import */ var _RegisterJWT_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RegisterJWT.vue?vue&type=script&lang=js */ "./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _RegisterJWT_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__["render"],
  _RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/pages/register/RegisterJWT.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterJWT_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterJWT.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterJWT_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6 ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RegisterJWT.vue?vue&type=template&id=653637e6 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/pages/register/RegisterJWT.vue?vue&type=template&id=653637e6");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_RegisterJWT_vue_vue_type_template_id_653637e6__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);