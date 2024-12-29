

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col w-full mb-4">
            <h4 class="w-full mb-4"><i class="fa-solid fa-file-contract mr-1"></i> DANH SÁCH BÁO CÁO VẬN HÀNH</h4>
            <hr class="mb-3" style="border: 0.5px solid #ccc;">
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-01'">
                <p>01. BÁO CÁO CHI TIẾT HỌC SINH FULL FEE ACTIVE</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-02a'">
                <p>02. BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-02b'">
                <p>03. BÁO CÁO TỔNG HỢP HỌC SINH TÁI PHÍ</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-02c'">
                <p>04. BÁO CÁO THEO EC HỌC SINH TÁI PHÍ</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-05'">
                <p>05. BÁO CÁO HỌC SINH PENDING</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-06'">
                <p>06. BÁO CÁO HỌC SINH BẢO LƯU</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-07'">
                <p>07. BÁO CÁO LỚP HỌC</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-08'">
                <p>08. CHỈ SỐ VẬN HÀNH ACS</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-09'">
                <p>09. BÁO CÁO TỶ LỆ ACS CỦA TỪNG AF</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-10'">
                <p>10. BÁO CÁO TỶ LỆ ACS CỦA TỪNG TRUNG TÂM</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-11'">
                <p>11. BÁO CÁO HỌC VIÊN TRIAL</p>
              </router-link>
            </div>
            <div class="mb-2">
              <router-link class="link-report" :to="'/lms/reports/operate/report-12'">
                <p>12. BÁO CÁO HỌC VIÊN ĐẶT CỌC</p>
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