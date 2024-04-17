<template>
  <div class="suggetion-search">
    <input type="hidden" :v-model="selectedStudent" />
    <autocomplete
      url="/api/students/suggest/"
      anchor="label"
      label="branch_name"
      name="search_sugget_student"
      id="sugget_student"
      className="search-autocomplete"
      :title="title"
      :classes="{ wrapper: 'form-wrapper', input: 'vs-inputx vs-input--input normal', list: 'data-list', item: 'data-list-item' }"
      :min="3"
      :options="response"
      :debounce="10"
      :filterByAnchor="true"
      :placeholder="placeholderStudent"
      :onShouldGetData="suggestStudents"
      :onSelect="selectItem">
    </autocomplete>
    <!-- <div :class="{active: isLoading}" class="icon-group loading"><img src="/static/img/images/loading/tgl.gif"></div> -->
    <div :class="{active: !isLoading}" class="icon-group search"><i class="fa fa-search"></i></div>
  </div>
</template>

<style>

.suggetion-search {
  width:100%;
  position: relative;
}
.suggetion-search .icon-group {
  position: absolute;
  right: 0;
  padding:1px;
  height: 35px;
  width: 43px;
  top:0;
  display: none;
}
.suggetion-search .icon-group.active {
  display: block;
}
.suggetion-search .icon-group.loading {
  padding: 4px 0 0 0;
  text-align: center;

}
.suggetion-search .icon-group.search {
  text-align: center;
  padding: 7px 4px 0 5px;
}

.autocomplete ul{
position: absolute;
list-style: none;
background: #eaeef1;
padding: 0;
border-bottom: 1px solid #c2cfd6;
border-left: 1px solid #c2cfd6;
border-right: 1px solid #c2cfd6;
margin: 0;
width: 100%;
z-index: 99999;
display: inline-block;
min-width: 15%;
}
.autocomplete ul:before{
content: "";
display: block;
position: absolute;
height: 0;
width: 0;
border: 1px solid #029c11;
background: #076d00;
left: 46%;
top: 0;
}
.autocomplete ul li a {
text-decoration: none;
display: block;
background: #f7f7f7;
color: #2b2b2b;
padding: 5px;
padding-left: 10px;
border-bottom:1px solid #d2dbe0;
}
.autocomplete ul li a b {
color:#333;
font-weight: 500;
font-size: 12px;
}
.autocomplete ul li a span,
.autocomplete ul li a .autocomplete-anchor-label{
display: block;
margin-top: 3px;
color: #666666;
font-weight: 400;
font-size: 11px;
}
.autocomplete ul li:hover, .autocomplete ul li.focus-list, .autocomplete ul li.focus-list a {
background: #f02929;
}
.autocomplete ul li a:hover, .autocomplete ul li:hover a b, .autocomplete ul li a:hover b, .autocomplete ul li.focus-list a b {
background: #f02929;
color: #ffffff!important;
text-shadow: 0 1px 1px #111;
}
.autocomplete ul li.focus-list a span {
color: #f9f9f9!important;
text-shadow: 0 1px 0 #111;
}
.autocomplete ul li a:hover .autocomplete-anchor-label,
.autocomplete ul li.focus-list a span,
.autocomplete ul li a:hover .autocomplete-anchor-label,
.autocomplete ul li.focus-list a span{
color: white;
}
</style>

<script>
import Autocomplete from 'vue2-autocomplete-js'

export default {
  props:{
    title: String,
    endpoint: {
      type: Number,
      default: 0
    },
    placeholderStudent: {
      type: String,
      default: 'Tìm kiếm học sinh theo tên hoặc mã LMS'
    },
    response: {
      type: Array,
      default: () => []
    },
    suggestStudents: {
      type: Function,
      default: null
    },
    selectedStudent:  {
      type: Object,
      default: null
    },
    onSelectStudent:  {
      type: Function,
      default: null
    },
    beforeSearchStudent:  {
      type: Function,
      default: null
    },
    afterSearchStudent:  {
      type: Function,
      default: null
    }
  },
  data () {
    return {
      name: '',
      isLoading: false,
      searchClass: {
        active: this.ajaxloading ? false : true,
        'icon-group': true,
        search: true
      }
    }
  },
  components: {
    Autocomplete
  },
  watch: {
    endpoint() {
      console.log(`Endpoint: ${this.endpoint}`)
    }
  },
  methods: {
    selectItem(student) {
      this.$emit('doSelectStudent', student)
      this.onSelectStudent ? this.onSelectStudent(student) : null
    }
  }
}
</script>
