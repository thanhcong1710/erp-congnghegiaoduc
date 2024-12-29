

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col w-full mb-4">
            <h4 class="w-full mb-4"><i class="fa-solid fa-file-contract mr-1"></i> DANH SÁCH BÁO CÁO TÀI CHÍNH KẾ TOÁN</h4>
            <hr class="mb-3" style="border: 0.5px solid #ccc;">
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/finance/report-01'">
                <p>01. BÁO CÁO CHI TIẾT DOANH SỐ</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/finance/report-02'">
                <p>02. BÁO CÁO DOANH SỐ THEO TRUNG TÂM</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/finance/report-03'">
                <p>03. BÁO CÁO DOANH THU THỰC HIỆN</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/finance/report-04'">
                <p>04. BÁO CÁO DOANH SỐ THEO NHÂN VIÊN</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/finance/report-05'">
                <p>05. BÁO CÁO CHI TIẾT ĐƠN HÀNG</p>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import u from '../../../../until/helper.js';
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
.link-report:hover{
  text-decoration: underline;
}
.link-report:hover p{
  color: rgba(var(--vs-primary), 1);
}
.link-report p{
  margin-bottom: 4px;
  color: #252525;
}
</style>