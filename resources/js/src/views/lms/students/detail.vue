<template>

  <div id="page-parent-detail">
    <vx-card no-shadow class="mb-base">
      <div class="flex flex-wrap items-center">
        <div class="me-7 mb-4">
          <img alt="image" width="160px" src="@assets/images/common/avatar-girl.svg"  v-if="student_info.gender == 'F'"/>
          <img alt="image" width="160px" src="@assets/images/common/avatar-boy.svg"  v-else/>
        </div>
       <div class="flex-grow-1">
          <!--begin::Title-->
          <div class="flex justify-content-between align-items-start flex-wrap mb-2">
            <!--begin::User-->
            <div >
              <!--begin::Name-->
              <div class="flex mb-2">
                <h4>{{student_info.name}}</h4>
              </div>
              <div class="flex mb-4">
                <div class="mr-5"><i class="fa-solid fa-user"></i> <span>{{student_info.lms_code}}</span></div>
                <div class="mr-5"><i class="fa-solid fa-phone"></i> <span>{{student_info.gud_mobile1}}</span></div>
                <div><i class="fa-solid fa-envelope"></i> <span>{{student_info.gud_email1}}</span></div>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap">
            <!--begin::Wrapper-->
            <div class="flex flex-wrap">
              <!--begin::Stats-->
              <div class="box-item-student border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                <div class="flex align-items-center">
                  <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="4500"
                    data-kt-countup-prefix="$" data-kt-initialized="1">15.000.000</div>
                </div>
                <div class="fw-semibold fs-6 text-gray-400">Phí đã đóng (VNĐ)</div>
              </div>
              <div class="box-item-student border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                <div class="flex align-items-center">
                  <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="75"
                    data-kt-initialized="1">96</div>
                </div>
                <div class="fw-semibold fs-6 text-gray-400">Tổng số buổi</div>
              </div>
              <div class="box-item-student border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                <!--begin::Number-->
                <div class="flex align-items-center">
                  <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="60"
                    data-kt-countup-prefix="%" data-kt-initialized="1">54</div>
                </div>
                <div class="fw-semibold fs-6 text-gray-400">Số buổi còn lại</div>
              </div>
            </div>
            <div class="box-item-process">
              <div class="w-200">
                <span class="fw-semibold fs-6 text-gray-400">Đã học</span>
                <span class="fw-bold fs-6" style="float:right">40%</span>
              </div>
              <vs-progress :height="8" :percent="40" color="success"></vs-progress>
            </div>
          </div>
        </div>
      </div>
    </vx-card>

    <vx-card no-shadow class="mt-5">
      <vs-tabs v-model="active_tab">
        <vs-tab label="Thông tin">
          <div class="tab-text">
             <student-info :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Gói phí">
          <div class="tab-text">
          </div>
        </vs-tab>
        <vs-tab label="Buổi học">
          <div class="tab-text">
          </div>
        </vs-tab>
        <vs-tab label="Đang chờ duyệt">
          <div class="tab-text">
          </div>
        </vs-tab>
        <vs-tab label="Chăm sóc">
          <div class="tab-text">
          </div>
        </vs-tab>
        <vs-tab label="Lịch sử cập nhật">
          <div class="tab-text">
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>

  </div>

</template>

<script>
  import Multiselect from "vue-multiselect";
  import axios from '../../../http/axios.js';
  import u from '../../../until/helper.js'
  import moment from 'moment';
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'
  import studentInfo from './components/studentInfo.vue'

  export default {
    components: {
      "vue-select": select,
      Multiselect,
      moment,
      datepicker,
      studentInfo
    },
    data() {
      return {
        active_tab: 0,
        student_info:{}
      };
    },
    async created() {
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/students/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.student_info = response.data
          }else{
            this.$router.push({ path: `/lms/students` });
          }
        })
      },
    },
    filters: {
    },
  }
</script>
<style>
.box-item-student{
  border-color: #ccc;
  margin-right: 15px;
}
.box-item-student .counted{
  font-size: 25px;
}
.box-item-process{
  width: 200px;
  margin-left: 30px ;
}
</style>