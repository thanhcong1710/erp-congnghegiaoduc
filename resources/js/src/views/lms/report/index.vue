

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col w-full mb-4">
            <label for="" class="vs-input--label" style="font-size: 16px;">Hệ thống báo cáo sẽ được xây dưng theo yêu cầu của công ty, sao cho phù hợp nhất với nghiệp vụ vận hành</label>
            
          </div>
        </div>
      </div>
      
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import u from '../../../until/helper.js';
  import DatePicker from "vue2-datepicker";

  export default {
    components: { 
      vSelect,
      Multiselect,
      DatePicker
    },
    data() {
      return {
      }
    },
    created() {
    },
    methods: {
    },
    filters: {
    },
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input{
    width: 360px;
  }
}
th .sort-th, th .vs-table-text{
  display: contents;
}
.multiselect{
  z-index: 999;
}
.td.vs-table--td{
  vertical-align: top;
}
</style>