<template>

  <div id="page-parent-detail">
    <vx-card no-shadow class="mb-base">
      <div class="flex flex-wrap items-center">
        <div class="me-7 mb-4 pr-2" style="width:160px; text-align: center">
          <img alt="image" width="100%" :src="student_info.avatar_url"/>
          <label for="account-upload" style="padding: 5px 10px;" class="vs-component vs-button vs-button-primary vs-button-filled mb-2">Upload Avatar</label>
          <input type="file" ref="file" multiple="multiple" id="account-upload" hidden accept="image/*" @change="submitFiles"/>
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
                <div class="fw-semibold fs-6 text-gray-400"> Học phí (VNĐ)</div>
                <div class="flex align-items-center">
                  <div class="fs-2 fw-bold counted"><span class="text-success">{{student_info.left_amount | formatNumber}}</span>
                    <span style="font-size: 16px;">/ {{student_info.init_total_charged | formatNumber}}</span>
                  </div>
                </div>
              </div>
              <div class="box-item-student border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                <!--begin::Number-->
                <div class="fw-semibold fs-6 text-gray-400">Số buổi học</div>
                <div class="flex align-items-center">
                  <div class="fs-2 fw-bold counted"><span class="text-success">{{student_info.left_sessions}}</span> 
                    <span style="font-size: 16px;">/ {{student_info.summary_sessions}}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-item-process" v-if="student_info.done_sessions">
              <div class="w-200">
                <span class="fw-semibold fs-6 text-gray-400">Đã học</span>
                <span class="fw-bold fs-6" style="float:right" >{{Math.round(student_info.done_sessions* 100 /student_info.summary_sessions)}}%</span>
              </div>
              <vs-progress :height="8" :percent="Math.round(student_info.done_sessions* 100 /student_info.summary_sessions)" color="success"></vs-progress>
            </div>
          </div>
        </div>
      </div>
    </vx-card>

    <vx-card no-shadow class="mt-5">
      <vs-tabs v-model="active_tab">
        <vs-tab label="Hồ sơ">
          <div class="tab-text">
             <student-info :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Gói phí">
          <div class="tab-text">
            <student-contracts :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Buổi học">
          <div class="tab-text">
            <student-sessions :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Vận hành">
          <div class="tab-text">
            <student-operating :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Đánh giá & nhận xét">
          <div class="tab-text">
            <student-assessments :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Lịch sử">
          <div class="tab-text">
            <student-logs :student_info="student_info" />
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
  import studentLogs from './components/studentLogs.vue'
  import studentContracts from './components/studentContracts.vue'
  import studentSessions from './components/studentSessions.vue'
  import studentAssessments from './components/studentAssessments.vue'
  import studentOperating from './components/studentOperating.vue'

  export default {
    components: {
      "vue-select": select,
      Multiselect,
      moment,
      datepicker,
      studentInfo,
      studentLogs,
      studentContracts,
      studentSessions,
      studentAssessments,
      studentOperating
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
      submitFiles() {
        if(this.$refs.file.files.length){
          this.$vs.loading()
          const formData = new FormData();
          for (var i = 0; i < this.$refs.file.files.length; i++) {
            let file = this.$refs.file.files[i];
            formData.append('files[' + i + ']', file);
          }
          formData.append('student_id', this.$route.params.id);
          axios.p('/api/lms/students/upload-avatar', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              },
            }).then((response) => {  
              this.loadDetail();
            })
          .catch((error)   => { console.log(error); this.$vs.loading.close(); })
        }
      },
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