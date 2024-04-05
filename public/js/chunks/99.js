(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[99],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var swiper_dist_css_swiper_min_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! swiper/dist/css/swiper.min.css */ "./node_modules/swiper/dist/css/swiper.min.css");
/* harmony import */ var swiper_dist_css_swiper_min_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(swiper_dist_css_swiper_min_css__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_awesome_swiper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-awesome-swiper */ "./node_modules/vue-awesome-swiper/dist/vue-awesome-swiper.js");
/* harmony import */ var vue_awesome_swiper__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_awesome_swiper__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var algoliasearch_lite__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! algoliasearch/lite */ "./node_modules/algoliasearch/src/browser/builds/algoliasearchLite.js");
/* harmony import */ var algoliasearch_lite__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(algoliasearch_lite__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_star_rating__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-star-rating */ "./node_modules/vue-star-rating/dist/VueStarRating.common.js");
/* harmony import */ var vue_star_rating__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_star_rating__WEBPACK_IMPORTED_MODULE_3__);




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    swiper: vue_awesome_swiper__WEBPACK_IMPORTED_MODULE_1__["swiper"],
    swiperSlide: vue_awesome_swiper__WEBPACK_IMPORTED_MODULE_1__["swiperSlide"],
    StarRating: vue_star_rating__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  data: function data() {
    return {
      algolia_index: algoliasearch_lite__WEBPACK_IMPORTED_MODULE_2___default()('latency', '6be0576ff61c053d5f9a3225e2a90f76').initIndex('instant_search'),
      item_data: null,
      error_occured: false,
      error_msg: '',
      swiperOptionCertificate: {
        slidesPerView: 3,
        spaceBetween: 30,
        breakpoints: {
          1024: {
            slidesPerView: 3,
            spaceBetween: 40
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 30
          },
          640: {
            slidesPerView: 2,
            spaceBetween: 20
          }
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        }
      },
      // Related Products Swiper
      swiperOption: {
        slidesPerView: 5,
        spaceBetween: 55,
        breakpoints: {
          1600: {
            slidesPerView: 4,
            spaceBetween: 55
          },
          1300: {
            slidesPerView: 3,
            spaceBetween: 55
          },
          900: {
            slidesPerView: 2,
            spaceBetween: 55
          },
          640: {
            slidesPerView: 1,
            spaceBetween: 55
          }
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        }
      },
      // Below is data which is common in any item
      // Algolia's dataSet don't provide this kind of data. So, here's dummy data for demo
      opt_color: '#7367F0',
      is_hearted: false,
      related_items: [{
        'name': 'Apple - Apple Watch Series 1 42mm Space Gray Aluminum Case Black Sport Band - Space Gray Aluminum',
        'brand': 'Apple',
        'price': 229,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/01.png',
        'rating': 4,
        'objectID': '5546604'
      }, {
        'name': 'Beats by Dr. Dre - Powerbeats2 Wireless Earbud Headphones - Black/Red',
        'brand': 'Beats by Dr. Dre',
        'price': 199.99,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/08.png',
        'rating': 4,
        'objectID': '5565002'
      }, {
        'name': 'Amazon - Fire TV Stick with Alexa Voice Remote - Black',
        'brand': 'Amazon',
        'price': 39.99,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/03.png',
        'rating': 4,
        'objectID': '5477500'
      }, {
        'name': 'Apple - Apple Watch Nike+ 42mm Silver Aluminum Case Silver/Volt Nike Sport Band - Silver Aluminum',
        'brand': 'Apple',
        'price': 399,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/07.png',
        'rating': 4,
        'objectID': '5547700'
      }, {
        'name': 'Google - Chromecast Ultra - Black',
        'brand': 'Google',
        'price': 69.99,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/05.png',
        'rating': 4,
        'objectID': '5578628'
      }, {
        'name': 'Beats by Dr. Dre - Beats EP Headphones - White',
        'brand': 'Beats by Dr. Dre',
        'price': 129.99,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/02.png',
        'rating': 4,
        'objectID': '5577781'
      }, {
        'name': 'LG - 40" Class (39.5" Diag.) - LED - 1080p - HDTV - Black',
        'brand': 'LG',
        'price': 279.99,
        'image': 'https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/products/09.png',
        'rating': 4,
        'objectID': '5613404'
      }]
    };
  },
  computed: {
    item_qty: function item_qty() {
      var item = this.$store.getters['eCommerce/getCartItem'](this.item_data.objectID);
      return Object.keys(item).length ? item.quantity : 1;
    },
    itemColor: function itemColor() {
      return function (obj) {
        var style_obj = {};
        obj.style.forEach(function (p) {
          style_obj[p] = obj.color;
        });
        return style_obj;
      };
    },
    isInWishList: function isInWishList() {
      var _this = this;
      return function (itemId) {
        return _this.$store.getters['eCommerce/isInWishList'](itemId);
      };
    },
    isInCart: function isInCart() {
      var _this2 = this;
      return function (itemId) {
        return _this2.$store.getters['eCommerce/isInCart'](itemId);
      };
    }
  },
  methods: {
    toggleItemInWishList: function toggleItemInWishList(item) {
      this.$store.dispatch('eCommerce/toggleItemInWishList', item);
    },
    toggleItemInCart: function toggleItemInCart(item) {
      this.$store.dispatch('eCommerce/toggleItemInCart', item);
    },
    fetch_item_details: function fetch_item_details(id) {
      var _this3 = this;
      this.$http.post('/api/law/item/detail', {
        'id': id
      }).then(function (response) {
        _this3.item_data = response.data;
      })["catch"](function (error) {
        console.log(error);
      });
    }
  },
  created: function created() {
    this.fetch_item_details(this.$route.params.item_id);
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0":
/*!*************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0 ***!
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
  return _c("div", {
    attrs: {
      id: "item-detail-page"
    }
  }, [_c("vs-alert", {
    attrs: {
      color: "danger",
      title: "Error Fetching Product Data",
      active: _vm.error_occured
    },
    on: {
      "update:active": function updateActive($event) {
        _vm.error_occured = $event;
      }
    }
  }, [_c("span", [_vm._v(_vm._s(_vm.error_msg) + ". ")]), _vm._v(" "), _c("span", [_c("span", [_vm._v("Check ")]), _c("router-link", {
    staticClass: "text-inherit underline",
    attrs: {
      to: {
        name: "ecommerce-shop"
      }
    }
  }, [_vm._v("All Items")])], 1)]), _vm._v(" "), _vm.item_data ? _c("vx-card", {
    key: _vm.item_data.objectID
  }, [_c("template", {
    slot: "no-body"
  }, [_c("div", {
    staticClass: "item-content"
  }, [_c("div", {
    staticClass: "product-details p-6"
  }, [_c("div", {
    staticClass: "vx-row mt-6"
  }, [_c("div", {
    staticClass: "vx-col md:w-2/5 w-full flex"
  }, [_c("div", {
    staticClass: "product-img-container mx-auto mb-10 md:mb-0"
  }, [_c("img", {
    staticClass: "responsive",
    attrs: {
      src: _vm.item_data.image_url,
      alt: _vm.item_data.name
    }
  })])]), _vm._v(" "), _c("div", {
    staticClass: "vx-col md:w-3/5 w-full"
  }, [_c("h3", [_vm._v(_vm._s(_vm.item_data.name))]), _vm._v(" "), _c("p", {
    staticClass: "my-2"
  }, [_c("span", [_vm._v(_vm._s(_vm.item_data.address))])]), _vm._v(" "), _c("p", {
    staticClass: "flex items-center flex-wrap"
  }, [_c("span", {
    staticClass: "text-2xl leading-none font-medium text-primary mr-4 mt-2"
  }, [_vm._v(_vm._s(_vm._f("formatCurrency")(_vm.item_data.price)))]), _vm._v(" "), _c("span", {
    staticClass: "pl-4 mr-2 mt-2 border border-solid d-theme-border-grey-light border-t-0 border-b-0 border-r-0"
  }, [_c("star-rating", {
    attrs: {
      "show-rating": false,
      rating: _vm.item_data.rating,
      "star-size": 20,
      "read-only": ""
    }
  })], 1), _vm._v(" "), _c("span", {
    staticClass: "cursor-pointer leading-none mt-2"
  }, [_vm._v("424 ratings")])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    domProps: {
      innerHTML: _vm._s(_vm.item_data.description)
    }
  }), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col w-full"
  }, [_c("span", {
    staticClass: "text-xl font-medium"
  }, [_vm._v("Lĩnh vực hành nghề")]), _vm._v(" "), _c("div", {
    staticClass: "vs-row mt-3"
  }, _vm._l(_vm.item_data.arr_law_type, function (type) {
    return _c("div", {
      staticClass: "vs-col vs-sm-6 vs-lg-4 mb-3 pr-3"
    }, [_c("span", {
      staticClass: "flex items-center"
    }, [_c("feather-icon", {
      staticClass: "mr-2",
      "class": "text-" + type.color,
      attrs: {
        icon: "CheckSquareIcon",
        svgClasses: "h-6 w-6"
      }
    }), _vm._v(" "), _c("span", [_vm._v(_vm._s(type.title))])], 1)]);
  }), 0)])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col w-full"
  }, [_c("span", {
    staticClass: "text-xl font-medium"
  }, [_vm._v("Chứng chỉ bằng cấp")]), _vm._v(" "), _c("div", {
    staticClass: "vs-row mt-3"
  }, [_c("swiper", {
    key: _vm.$vs.rtl,
    attrs: {
      options: _vm.swiperOptionCertificate,
      dir: _vm.$vs.rtl ? "rtl" : "ltr"
    }
  }, [_c("swiper-slide", [_c("img", {
    staticClass: "responsive",
    attrs: {
      src: "https://i-law.vn/uploads/users/234/nguyentam.jpg",
      alt: "banner"
    }
  })]), _vm._v(" "), _c("swiper-slide", [_c("img", {
    staticClass: "responsive",
    attrs: {
      src: "https://i-law.vn/uploads/users/234/ls-nguyen-tam.jpg",
      alt: "banner"
    }
  })]), _vm._v(" "), _c("swiper-slide", [_c("img", {
    staticClass: "responsive",
    attrs: {
      src: "https://i-law.vn/uploads/users/234/nguyen-tam.jpg",
      alt: "banner"
    }
  })]), _vm._v(" "), _c("div", {
    staticClass: "swiper-pagination",
    attrs: {
      slot: "pagination"
    },
    slot: "pagination"
  })], 1)], 1)])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col w-full"
  }, [_c("div", {
    staticClass: "flex flex-wrap items-start mb-4"
  }, [!_vm.isInCart(_vm.item_data.objectID) ? _c("vs-button", {
    staticClass: "mr-4 mb-4",
    attrs: {
      "icon-pack": "feather",
      icon: "icon-shopping-cart"
    },
    on: {
      click: function click($event) {
        return _vm.toggleItemInCart(_vm.item_data);
      }
    }
  }, [_vm._v("\n                      ADD TO CART\n                    ")]) : _c("vs-button", {
    staticClass: "mr-4 mb-4",
    attrs: {
      "icon-pack": "feather",
      icon: "icon-shopping-cart"
    },
    on: {
      click: function click($event) {
        _vm.$router.push({
          name: "law-checkout"
        })["catch"](function (err) {});
      }
    }
  }, [_vm._v("\n                      VIEW IN CART\n                    ")]), _vm._v(" "), _vm.isInWishList(_vm.item_data.objectID) ? _c("vs-button", {
    key: "filled",
    staticClass: "mb-4",
    attrs: {
      "icon-pack": "feather",
      icon: "icon-heart",
      color: "danger"
    },
    on: {
      click: function click($event) {
        return _vm.toggleItemInWishList(_vm.item_data);
      }
    }
  }, [_vm._v("\n                      WHISHLIST\n                    ")]) : _c("vs-button", {
    key: "border",
    staticClass: "mb-4",
    attrs: {
      type: "border",
      "icon-pack": "feather",
      icon: "icon-heart",
      color: "danger"
    },
    on: {
      click: function click($event) {
        return _vm.toggleItemInWishList(_vm.item_data);
      }
    }
  }, [_vm._v("\n                      WHISHLIST\n                    ")])], 1)])]), _vm._v(" "), _c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col flex flex-wrap items-center"
  }, [_c("vs-button", {
    staticClass: "mr-4",
    attrs: {
      type: "border",
      "icon-pack": "feather",
      color: "#1551b1",
      icon: "icon-facebook",
      radius: ""
    }
  }), _vm._v(" "), _c("vs-button", {
    staticClass: "mr-4",
    attrs: {
      type: "border",
      "icon-pack": "feather",
      color: "#00aaff",
      icon: "icon-twitter",
      radius: ""
    }
  }), _vm._v(" "), _c("vs-button", {
    staticClass: "mr-4",
    attrs: {
      type: "border",
      "icon-pack": "feather",
      color: "#c4302b",
      icon: "icon-youtube",
      radius: ""
    }
  }), _vm._v(" "), _c("vs-button", {
    staticClass: "mr-4",
    attrs: {
      type: "border",
      "icon-pack": "feather",
      color: "#405DE6",
      icon: "icon-instagram",
      radius: ""
    }
  })], 1)])], 1)])]), _vm._v(" "), _c("div", {
    staticClass: "py-24 mb-16 mt-10 text-center item-features"
  }, [_c("div", {
    staticClass: "vx-row"
  }, [_c("div", {
    staticClass: "vx-col md:w-1/3 w-full"
  }, [_c("div", {
    staticClass: "w-64 mx-auto mb-16 md:mb-0"
  }, [_c("feather-icon", {
    staticClass: "block mb-4",
    attrs: {
      icon: "AwardIcon",
      svgClasses: "h-12 w-12 text-primary stroke-current"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "font-semibold text-lg"
  }, [_vm._v("100% Original")]), _vm._v(" "), _c("p", {
    staticClass: "mt-2"
  }, [_vm._v("Chocolate bar candy canes ice cream toffee cookie halvah.")])], 1)]), _vm._v(" "), _c("div", {
    staticClass: "vx-col md:w-1/3 w-full"
  }, [_c("div", {
    staticClass: "w-64 mx-auto mb-16 md:mb-0"
  }, [_c("feather-icon", {
    staticClass: "block mb-4",
    attrs: {
      icon: "ClockIcon",
      svgClasses: "h-12 w-12 text-primary stroke-current"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "font-semibold text-lg"
  }, [_vm._v("10 Day Replacement")]), _vm._v(" "), _c("p", {
    staticClass: "mt-2"
  }, [_vm._v("Marshmallow biscuit donut dragée fruitcake wafer.")])], 1)]), _vm._v(" "), _c("div", {
    staticClass: "vx-col md:w-1/3 w-full"
  }, [_c("div", {
    staticClass: "w-64 mx-auto"
  }, [_c("feather-icon", {
    staticClass: "block mb-4",
    attrs: {
      icon: "ShieldIcon",
      svgClasses: "h-12 w-12 text-primary stroke-current"
    }
  }), _vm._v(" "), _c("span", {
    staticClass: "font-semibold text-lg"
  }, [_vm._v("1 Year Warranty")]), _vm._v(" "), _c("p", {
    staticClass: "mt-2"
  }, [_vm._v("Cotton candy gingerbread cake I love sugar sweet.")])], 1)])])])]), _vm._v(" "), _c("div", {
    staticClass: "related-products text-center px-6"
  }, [_c("div", {
    staticClass: "related-headings mb-8 text-center"
  }, [_c("h2", {
    staticClass: "uppercase"
  }, [_vm._v("Related Products")]), _vm._v(" "), _c("p", [_vm._v("People also search for this items")])]), _vm._v(" "), _c("swiper", {
    key: _vm.$vs.rtl,
    staticClass: "related-product-swiper px-12 py-6",
    attrs: {
      options: _vm.swiperOption,
      dir: _vm.$vs.rtl ? "rtl" : "ltr"
    }
  }, [_vm._l(_vm.related_items, function (item) {
    return _c("swiper-slide", {
      key: item.objectId,
      staticClass: "p-6 rounded cursor-pointer"
    }, [_c("div", {
      staticClass: "item-heading mb-4"
    }, [_c("p", {
      staticClass: "text-lg font-semibold truncate"
    }, [_vm._v(_vm._s(item.name))]), _vm._v(" "), _c("p", {
      staticClass: "text-sm"
    }, [_c("span", {
      staticClass: "mr-2"
    }, [_vm._v("by")]), _vm._v(" "), _c("span", [_vm._v(_vm._s(item.brand))])])]), _vm._v(" "), _c("div", {
      staticClass: "img-container w-32 mx-auto my-base"
    }, [_c("img", {
      staticClass: "responsive",
      attrs: {
        src: item.image,
        alt: item.name
      }
    })]), _vm._v(" "), _c("div", {
      staticClass: "item-meta"
    }, [_c("star-rating", {
      staticClass: "justify-center",
      attrs: {
        "show-rating": false,
        rating: item.rating,
        "star-size": 14,
        "read-only": ""
      }
    }), _vm._v(" "), _c("p", {
      staticClass: "text-lg font-medium text-primary"
    }, [_vm._v("$" + _vm._s(item.price))])], 1)]);
  }), _vm._v(" "), _c("div", {
    staticClass: "swiper-button-prev",
    attrs: {
      slot: "button-prev"
    },
    slot: "button-prev"
  }), _vm._v(" "), _c("div", {
    staticClass: "swiper-button-next",
    attrs: {
      slot: "button-next"
    },
    slot: "button-next"
  })], 2)], 1)])], 2) : _vm._e()], 1);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "/*=========================================================================================\n    File Name: _variables.scss\n    Description: partial- SCSS varibales\n    ----------------------------------------------------------------------------------------\n    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template\n      Author: Pixinvent\n    Author URL: http://www.themeforest.net/user/pixinvent\n==========================================================================================*/\n\n/*========================================================\n        SPACING\n=========================================================*/\n\n/*========================================================\n        COLORS\n=========================================================*/\n\n/*========================================================\n        TYPOGRAPHY\n=========================================================*/\n\n/*========================================================\n        TYPOGRAPHY\n=========================================================*/\n\n/*========================================================\n        DARK THEME\n=========================================================*/\n#item-detail-page .color-radio {\n  height: 2.28rem;\n  width: 2.28rem;\n}\n#item-detail-page .color-radio > div {\n  top: 50%;\n}\n[dir=ltr] #item-detail-page .color-radio > div {\n  left: 50%;\n  transform: translate(-50%, -50%);\n}\n[dir=rtl] #item-detail-page .color-radio > div {\n  right: 50%;\n  transform: translate(50%, -50%);\n}\n[dir] #item-detail-page .item-features {\n  background-color: #f7f7f7;\n}\n[dir] .theme-dark #item-detail-page .item-features {\n  background-color: #171e49;\n}\n[dir=ltr] #item-detail-page .product-sales-meta-list .vs-list--icon {\n  padding-left: 0;\n}\n[dir=rtl] #item-detail-page .product-sales-meta-list .vs-list--icon {\n  padding-right: 0;\n}\n[dir] #item-detail-page .related-product-swiper .swiper-wrapper {\n  padding-bottom: 2rem;\n}\n[dir] #item-detail-page .related-product-swiper .swiper-wrapper > .swiper-slide {\n  background-color: #f7f7f7;\n  box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.1), 0 5px 12px 0 rgba(0, 0, 0, 0.08) !important;\n}\n[dir] .theme-dark #item-detail-page .related-product-swiper .swiper-wrapper > .swiper-slide {\n  background-color: #262c49;\n}\n#item-detail-page .related-product-swiper .swiper-button-next,\n#item-detail-page .related-product-swiper .swiper-button-prev {\n  filter: hue-rotate(40deg);\n}\n[dir] #item-detail-page .related-product-swiper .swiper-button-next, [dir] #item-detail-page .related-product-swiper .swiper-button-prev {\n  transform: scale(0.5);\n}\n[dir=ltr] #item-detail-page .related-product-swiper .swiper-button-next {\n  right: 0;\n}\n[dir=rtl] #item-detail-page .related-product-swiper .swiper-button-next {\n  left: 0;\n}\n[dir=ltr] #item-detail-page .related-product-swiper .swiper-button-prev {\n  left: 0;\n}\n[dir=rtl] #item-detail-page .related-product-swiper .swiper-button-prev {\n  right: 0;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss");

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

/***/ "./resources/js/src/views/law/ItemDetailView.vue":
/*!*******************************************************!*\
  !*** ./resources/js/src/views/law/ItemDetailView.vue ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ItemDetailView.vue?vue&type=template&id=47c223c0 */ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0");
/* harmony import */ var _ItemDetailView_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ItemDetailView.vue?vue&type=script&lang=js */ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss */ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _ItemDetailView_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__["render"],
  _ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/law/ItemDetailView.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ItemDetailView.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=style&index=0&id=47c223c0&lang=scss");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_style_index_0_id_47c223c0_lang_scss__WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0 ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ItemDetailView.vue?vue&type=template&id=47c223c0 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/ItemDetailView.vue?vue&type=template&id=47c223c0");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_ItemDetailView_vue_vue_type_template_id_47c223c0__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);