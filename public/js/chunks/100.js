(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[100],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/list.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ItemGridView: function ItemGridView() {
      return __webpack_require__.e(/*! import() */ 5).then(__webpack_require__.bind(null, /*! ./components/ItemGridView.vue */ "./resources/js/src/views/law/components/ItemGridView.vue"));
    },
    ItemListView: function ItemListView() {
      return __webpack_require__.e(/*! import() */ 17).then(__webpack_require__.bind(null, /*! ./components/ItemListView.vue */ "./resources/js/src/views/law/components/ItemListView.vue"));
    }
  },
  data: function data() {
    return {
      // Filter Sidebar
      isFilterSidebarActive: true,
      clickNotClose: true,
      currentItemView: 'item-grid-view',
      listItem: [],
      keyword: '',
      itemsRate: [5, 4, 3, 2, 1]
    };
  },
  computed: {
    toValue: function toValue() {
      return function (value, range) {
        return [value.min !== null ? value.min : range.min, value.max !== null ? value.max : range.max];
      };
    },
    // GRID VIEW
    isInCart: function isInCart() {
      var _this = this;
      return function (itemId) {
        return _this.$store.getters['eCommerce/isInCart'](itemId);
      };
    },
    isInWishList: function isInWishList() {
      var _this2 = this;
      return function (itemId) {
        return _this2.$store.getters['eCommerce/isInWishList'](itemId);
      };
    },
    windowWidth: function windowWidth() {
      return this.$store.state.windowWidth;
    }
  },
  watch: {
    windowWidth: function windowWidth() {
      this.setSidebarWidth();
    }
  },
  methods: {
    setSidebarWidth: function setSidebarWidth() {
      if (this.windowWidth < 992) {
        this.isFilterSidebarActive = this.clickNotClose = false;
      } else {
        this.isFilterSidebarActive = this.clickNotClose = true;
      }
    },
    // GRID VIEW - ACTIONS
    toggleFilterSidebar: function toggleFilterSidebar() {
      if (this.clickNotClose) return;
      this.isFilterSidebarActive = !this.isFilterSidebarActive;
    },
    toggleItemInWishList: function toggleItemInWishList(item) {
      this.$store.dispatch('eCommerce/toggleItemInWishList', item);
    },
    additemInCart: function additemInCart(item) {
      this.$store.dispatch('eCommerce/additemInCart', item);
    },
    cartButtonClicked: function cartButtonClicked(item) {
      this.isInCart(item.objectID) ? this.$router.push('/law/checkout')["catch"](function () {}) : this.additemInCart(item);
    },
    loadListItem: function loadListItem() {
      var _this3 = this;
      this.$vs.loading();
      this.$http.post('/api/law/list', {
        'keyword': this.keyword
      }).then(function (response) {
        _this3.listItem = response.data.list;
        _this3.$vs.loading.close();
      })["catch"](function (error) {
        console.log(error);
      });
    }
  },
  created: function created() {
    this.setSidebarWidth();
    this.loadListItem();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316 ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "ais-InstantSearch",
    attrs: {
      id: "algolia-instant-search-demo"
    }
  }, [_c("div", {
    staticClass: "algolia-header mb-4"
  }, [_c("div", {
    staticClass: "flex md:items-end items-center justify-between flex-wrap"
  }, [_c("feather-icon", {
    staticClass: "inline-flex lg:hidden cursor-pointer mr-4",
    attrs: {
      icon: "MenuIcon"
    },
    on: {
      click: function click($event) {
        $event.stopPropagation();
        return _vm.toggleFilterSidebar.apply(null, arguments);
      }
    }
  }), _vm._v(" "), _c("p", {
    staticClass: "lg:inline-flex hidden font-semibold algolia-filters-label"
  }, [_vm._v("Filters")]), _vm._v(" "), _c("div", {
    staticClass: "flex justify-between items-end flex-grow"
  }, [_c("div", {
    staticClass: "ais-Stats"
  }), _vm._v(" "), _c("div", {
    staticClass: "flex flex-wrap"
  }, [_c("div", [_c("feather-icon", {
    staticClass: "p-2 shadow-drop rounded-lg d-theme-dark-bg cursor-pointer",
    attrs: {
      icon: "GridIcon",
      svgClasses: {
        "text-primary stroke-current": _vm.currentItemView == "item-grid-view"
      }
    },
    on: {
      click: function click($event) {
        _vm.currentItemView = "item-grid-view";
      }
    }
  }), _vm._v(" "), _c("feather-icon", {
    staticClass: "p-2 ml-4 shadow-drop rounded-lg d-theme-dark-bg cursor-pointer hidden sm:inline-flex",
    attrs: {
      icon: "ListIcon",
      svgClasses: {
        "text-primary stroke-current": _vm.currentItemView == "item-list-view"
      }
    },
    on: {
      click: function click($event) {
        _vm.currentItemView = "item-list-view";
      }
    }
  })], 1)])])], 1)]), _vm._v(" "), _c("div", {
    staticClass: "relative clearfix",
    attrs: {
      id: "algolia-content-container"
    }
  }, [_c("vs-sidebar", {
    staticClass: "items-no-padding vs-sidebar-rounded background-absolute",
    attrs: {
      parent: "#algolia-content-container",
      "click-not-close": _vm.clickNotClose,
      "hidden-background": _vm.clickNotClose
    },
    model: {
      value: _vm.isFilterSidebarActive,
      callback: function callback($$v) {
        _vm.isFilterSidebarActive = $$v;
      },
      expression: "isFilterSidebarActive"
    }
  }, [_c("div", {
    staticClass: "p-6 filter-container"
  }, [_c("h6", {
    staticClass: "font-bold mb-3"
  }, [_vm._v("Tỉnh/Thành Phố")]), _vm._v(" "), _c("div", {
    staticClass: "multi-range-price"
  }, [_c("ul", {
    staticClass: "list-unstyled price-range",
    attrs: {
      id: "price-range"
    }
  }, [_c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceAll",
      name: "price-range",
      checked: ""
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceAll"
    }
  }, [_vm._v("Tất cả")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange1",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange1"
    }
  }, [_vm._v("Hà Nội")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange2",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange2"
    }
  }, [_vm._v("Hồ Chí Minh")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceARange3",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceARange3"
    }
  }, [_vm._v("Đà Nẵng")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange4",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange4"
    }
  }, [_vm._v("Hải Phòng")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange4",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange4"
    }
  }, [_vm._v("Thái Nguyên")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange4",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange4"
    }
  }, [_vm._v("Nghệ An")])])]), _vm._v(" "), _c("li", {
    staticClass: "flex items-center cursor-pointer py-1"
  }, [_c("div", {
    staticClass: "custom-control custom-radio"
  }, [_c("input", {
    staticClass: "custom-control-input",
    attrs: {
      type: "radio",
      id: "priceRange4",
      name: "price-range"
    }
  }), _vm._v(" "), _c("label", {
    staticClass: "custom-control-label",
    attrs: {
      "for": "priceRange4"
    }
  }, [_vm._v("Phú Thọ")])])])])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("h6", {
    staticClass: "font-bold mb-3"
  }, [_vm._v("Lĩnh Vực")]), _vm._v(" "), _c("div", {
    staticClass: "ais-RefinementList"
  }, [_c("div", [_c("ul", [_c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Tất cả")])]), _vm._v(" "), _c("span", [_vm._v("1746")])]), _vm._v(" "), _c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Đất đai")])]), _vm._v(" "), _c("span", [_vm._v("746")])]), _vm._v(" "), _c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Bảo hiểm")])]), _vm._v(" "), _c("span", [_vm._v("633")])]), _vm._v(" "), _c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Doanh nghiệp")])]), _vm._v(" "), _c("span", [_vm._v("591")])]), _vm._v(" "), _c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Hình sự")])]), _vm._v(" "), _c("span", [_vm._v("523")])]), _vm._v(" "), _c("li", {
    staticClass: "mb-2 flex items-center justify-between"
  }, [_c("div", {
    staticClass: "vs-component con-vs-checkbox ml-0 vs-checkbox-primary vs-checkbox-default"
  }, [_c("input", {
    staticClass: "vs-checkbox--input",
    attrs: {
      type: "checkbox",
      value: "false"
    }
  }), _c("span", {
    staticClass: "checkbox_x vs-checkbox",
    staticStyle: {
      border: "2px solid rgb(180, 180, 180)"
    }
  }, [_c("span", {
    staticClass: "vs-checkbox--check"
  }, [_c("i", {
    staticClass: "vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
  }, [_vm._v("check")])])]), _c("span", {
    staticClass: "con-slot-label"
  }, [_vm._v("Hôn nhân gia đình")])]), _vm._v(" "), _c("span", [_vm._v("625")])])])]), _vm._v(" "), _c("vs-divider"), _vm._v(" "), _c("h6", {
    staticClass: "font-bold mb-3"
  }, [_vm._v("Đánh giá")]), _vm._v(" "), _c("div", {
    attrs: {
      attribute: "rating"
    }
  }, [_c("ul", _vm._l(_vm.itemsRate, function (item) {
    return _c("li", {
      key: item,
      staticClass: "mb-2"
    }, [_c("div", {
      staticClass: "flex justify-between items-center"
    }, [_c("div", {
      staticClass: "flex items-center flex-wrap"
    }, [_vm._l([1, 2, 3, 4, 5], function (index) {
      return _c("feather-icon", {
        key: index,
        attrs: {
          icon: "StarIcon",
          svgClasses: [{
            "text-warning": index <= item,
            "text-grey": !index <= item,
            "ml-1": index
          }, "cursor-pointer stroke-current h-6 w-6"]
        }
      });
    }), _vm._v(" "), _c("span", {
      staticClass: "ml-2"
    }, [_vm._v("& up")])], 2), _vm._v(" "), _c("span", [_vm._v("(200)")])])]);
  }), 0)])], 1)], 1)]), _vm._v(" "), _c("div", {
    "class": {
      "sidebar-spacer-with-margin": _vm.clickNotClose
    }
  }, [_c("div", [_c("div", {
    staticClass: "relative mb-8"
  }, [_c("vs-input", {
    staticClass: "w-full vs-input-shadow-drop vs-input-no-border d-theme-input-dark-bg",
    attrs: {
      placeholder: "Search here",
      size: "large"
    },
    model: {
      value: _vm.keyword,
      callback: function callback($$v) {
        _vm.keyword = $$v;
      },
      expression: "keyword"
    }
  }), _vm._v(" "), _c("div", {
    staticClass: "absolute top-0 right-0 py-4 px-6",
    attrs: {
      slot: "submit-icon"
    },
    on: {
      click: function click($event) {
        return _vm.loadListItem();
      }
    },
    slot: "submit-icon"
  }, [_c("feather-icon", {
    attrs: {
      icon: "SearchIcon",
      svgClasses: "h-6 w-6"
    }
  })], 1)], 1)]), _vm._v(" "), _c("div", [_vm.currentItemView == "item-grid-view" ? [_c("div", {
    staticClass: "items-grid-view vx-row match-height"
  }, _vm._l(_vm.listItem, function (item) {
    return _c("div", {
      key: item.objectID,
      staticClass: "vx-col lg:w-1/3 sm:w-1/2 w-full"
    }, [_c("item-grid-view", {
      attrs: {
        item: item
      }
    }, [_c("template", {
      slot: "action-buttons"
    }, [_c("div", {
      staticClass: "flex flex-wrap"
    }, [_c("div", {
      staticClass: "item-view-primary-action-btn p-3 flex flex-grow items-center justify-center cursor-pointer",
      on: {
        click: function click($event) {
          return _vm.toggleItemInWishList(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "HeartIcon",
        svgClasses: [{
          "text-danger fill-current": _vm.isInWishList(item.objectID)
        }, "h-4 w-4"]
      }
    }), _vm._v(" "), _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("WISHLIST")])], 1), _vm._v(" "), _c("div", {
      staticClass: "item-view-secondary-action-btn bg-primary p-3 flex flex-grow items-center justify-center text-white cursor-pointer",
      on: {
        click: function click($event) {
          return _vm.cartButtonClicked(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "ShoppingBagIcon",
        svgClasses: "h-4 w-4"
      }
    }), _vm._v(" "), _vm.isInCart(item.objectID) ? _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("VIEW IN CART")]) : _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("ADD TO CART")])], 1)])])], 2)], 1);
  }), 0)] : _vm._l(_vm.listItem, function (item) {
    return _c("div", {
      key: item.objectID,
      staticClass: "items-list-view mb-base"
    }, [_c("item-list-view", {
      attrs: {
        item: item
      }
    }, [_c("template", {
      slot: "action-buttons"
    }, [_c("div", {
      staticClass: "item-view-primary-action-btn p-3 rounded-lg flex flex-grow items-center justify-center cursor-pointer mb-3",
      on: {
        click: function click($event) {
          return _vm.toggleItemInWishList(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "HeartIcon",
        svgClasses: [{
          "text-danger fill-current": _vm.isInWishList(item.objectID)
        }, "h-4 w-4"]
      }
    }), _vm._v(" "), _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("WISHLIST")])], 1), _vm._v(" "), _c("div", {
      staticClass: "item-view-secondary-action-btn bg-primary p-3 rounded-lg flex flex-grow items-center justify-center text-white cursor-pointer",
      on: {
        click: function click($event) {
          return _vm.cartButtonClicked(item);
        }
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "ShoppingBagIcon",
        svgClasses: "h-4 w-4"
      }
    }), _vm._v(" "), _vm.isInCart(item.objectID) ? _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("VIEW IN CART")]) : _c("span", {
      staticClass: "text-sm font-semibold ml-2"
    }, [_vm._v("ADD TO CART")])], 1)])], 2)], 1);
  })], 2)])], 1)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "#algolia-instant-search-demo .algolia-header .algolia-filters-label {\n  width: calc(260px + 2.4rem);\n}\n#algolia-instant-search-demo #algolia-content-container .vs-sidebar {\n  position: relative;\n}\n[dir=ltr] #algolia-instant-search-demo #algolia-content-container .vs-sidebar {\n  float: left;\n}\n[dir=rtl] #algolia-instant-search-demo #algolia-content-container .vs-sidebar {\n  float: right;\n}\n[dir] #algolia-instant-search-demo .algolia-search-input-right-aligned-icon {\n  padding: 1rem 1.5rem;\n}\n#algolia-instant-search-demo .algolia-price-slider {\n  min-width: unset;\n}\n#algolia-instant-search-demo .item-view-primary-action-btn {\n  color: #2c2c2c !important;\n  min-width: 50%;\n}\n[dir] #algolia-instant-search-demo .item-view-primary-action-btn {\n  background-color: #f6f6f6;\n}\n#algolia-instant-search-demo .item-view-secondary-action-btn {\n  min-width: 50%;\n}\n[dir] .theme-dark #algolia-instant-search-demo #algolia-content-container .vs-sidebar {\n  background-color: #10163a;\n}\n@media (min-width: 992px) {\n[dir] .vs-sidebar-rounded .vs-sidebar {\n    border-radius: 0.5rem;\n}\n[dir] .vs-sidebar-rounded .vs-sidebar--items {\n    border-radius: 0.5rem;\n}\n}\n@media (max-width: 992px) {\n#algolia-content-container .vs-sidebar {\n    position: absolute !important;\n}\n[dir] #algolia-content-container .vs-sidebar {\n    float: none !important;\n}\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--8-2!./node_modules/sass-loader/dist/cjs.js??ref--8-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=style&index=0&id=657d9316&lang=scss */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss");

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

/***/ "./resources/js/src/views/law/list.vue":
/*!*********************************************!*\
  !*** ./resources/js/src/views/law/list.vue ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./list.vue?vue&type=template&id=657d9316 */ "./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316");
/* harmony import */ var _list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./list.vue?vue&type=script&lang=js */ "./resources/js/src/views/law/list.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./list.vue?vue&type=style&index=0&id=657d9316&lang=scss */ "./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__["render"],
  _list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/law/list.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/law/list.vue?vue&type=script&lang=js":
/*!*********************************************************************!*\
  !*** ./resources/js/src/views/law/list.vue?vue&type=script&lang=js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--8-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--8-3!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=style&index=0&id=657d9316&lang=scss */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=style&index=0&id=657d9316&lang=scss");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_8_2_node_modules_sass_loader_dist_cjs_js_ref_8_3_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_style_index_0_id_657d9316_lang_scss__WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316":
/*!***************************************************************************!*\
  !*** ./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316 ***!
  \***************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=template&id=657d9316 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/law/list.vue?vue&type=template&id=657d9316");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_657d9316__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);