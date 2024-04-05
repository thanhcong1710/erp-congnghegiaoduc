(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[32],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/records/list.vue?vue&type=script&lang=js":
/*!*********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/records/list.vue?vue&type=script&lang=js ***!
  \*********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _http_axios_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../http/axios.js */ "./resources/js/src/http/axios.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {},
  data: function data() {
    return {
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
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656":
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656 ***!
  \*******************************************************************************************************************************************************************************************************************************************/
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
    staticClass: "vs-component vs-con-table stripe vs-table-primary"
  }, [_c("header", {
    staticClass: "header-table vs-table--header"
  }), _vm._v(" "), _c("div", {
    staticClass: "con-tablex vs-table--content"
  }, [_c("div", {
    staticClass: "vs-con-tbody vs-table--tbody"
  }, [_c("table", {
    staticClass: "vs-table vs-table--tbody-table"
  }, [_c("thead", {
    staticClass: "vs-table--thead"
  }, [_c("tr", [_c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("STT\n                  ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("Phòng họp\n                  ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("Bắt đầu\n                  ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("Kết thúc\n                  ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("Ngày lưu trữ\n                  ")])]), _vm._v(" "), _c("th", {
    attrs: {
      colspan: "1",
      rowspan: "1"
    }
  }, [_c("div", {
    staticClass: "vs-table-text"
  }, [_vm._v("Thao tác\n                  ")])])])]), _vm._v(" "), _vm._l(_vm.records, function (item, index) {
    return _c("tr", {
      key: index,
      staticClass: "tr-values vs-table--tr tr-table-state-null"
    }, [_c("td", {
      staticClass: "td vs-table--td"
    }, [_vm._v(_vm._s(index + 1 + (_vm.pagination.cpage - 1) * _vm.pagination.limit))]), _vm._v(" "), _c("td", {
      staticClass: "td vs-table--td"
    }, [_vm._v(_vm._s(item.title))]), _vm._v(" "), _c("td", {
      staticClass: "td vs-table--td"
    }, [_vm._v(_vm._s(item.start_time))]), _vm._v(" "), _c("td", {
      staticClass: "td vs-table--td"
    }, [_vm._v(_vm._s(item.end_time))]), _vm._v(" "), _c("td", {
      staticClass: "td vs-table--td"
    }, [item.status == 1 ? _c("span", {
      "class": item.record_date < 3 ? "text-warning" : ""
    }, [_vm._v(_vm._s(item.record_date))]) : _c("span", {
      staticClass: "text-danger"
    }, [_vm._v("Đã bị xóa")])]), _vm._v(" "), _c("td", {
      staticClass: "td vs-table--td"
    }, [item.record_link ? _c("a", {
      attrs: {
        target: "blank",
        href: item.record_link
      }
    }, [_c("feather-icon", {
      attrs: {
        icon: "EyeIcon",
        svgClasses: "h-5 w-5"
      }
    })], 1) : _vm._e(), _vm._v(" "), _c("feather-icon", {
      staticStyle: {
        cursor: "pointer"
      },
      attrs: {
        icon: "Trash2Icon",
        svgClasses: "h-5 w-5"
      },
      on: {
        click: function click($event) {
          return _vm.openConfirmDelete(item.record_id, item.title);
        }
      }
    })], 1)]);
  })], 2)]), _vm._v(" "), Math.ceil(_vm.pagination.total / _vm.pagination.limit) > 1 ? _c("vs-pagination", {
    staticClass: "mt-5",
    attrs: {
      total: Math.ceil(_vm.pagination.total / _vm.pagination.limit),
      max: 7
    },
    on: {
      change: function change($event) {
        return _vm.changePage();
      }
    },
    model: {
      value: _vm.pagination.cpage,
      callback: function callback($$v) {
        _vm.$set(_vm.pagination, "cpage", $$v);
      },
      expression: "pagination.cpage"
    }
  }) : _vm._e()], 1)])]);
};
var staticRenderFns = [];
render._withStripped = true;


/***/ }),

/***/ "./resources/js/src/views/records/list.vue":
/*!*************************************************!*\
  !*** ./resources/js/src/views/records/list.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./list.vue?vue&type=template&id=93f33656 */ "./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656");
/* harmony import */ var _list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./list.vue?vue&type=script&lang=js */ "./resources/js/src/views/records/list.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"],
  _list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__["render"],
  _list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/records/list.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/records/list.vue?vue&type=script&lang=js":
/*!*************************************************************************!*\
  !*** ./resources/js/src/views/records/list.vue?vue&type=script&lang=js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=script&lang=js */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/records/list.vue?vue&type=script&lang=js");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656 ***!
  \*******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??ref--6!../../../../../node_modules/vue-loader/lib??vue-loader-options!./list.vue?vue&type=template&id=93f33656 */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/records/list.vue?vue&type=template&id=93f33656");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_loaders_templateLoader_js_ref_6_node_modules_vue_loader_lib_index_js_vue_loader_options_list_vue_vue_type_template_id_93f33656__WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);