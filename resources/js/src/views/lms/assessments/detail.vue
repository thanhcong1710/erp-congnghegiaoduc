<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm </label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Chương trình học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.product_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Khóa học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.program_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lớp học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.class_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Gói phí</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.meta_data.student_info.tuition_fee_name"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin nhận xét & đánh giá</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Tiêu đề</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="assessment.title"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Nội dung</label>
              <textarea class="vs-inputx vs-input--input normal" rows="8" v-model="assessment.description"  disabled="true"></textarea>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/assessments`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
            </router-link>
          </div>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';
  import search from '../../../components/StudentSearch'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      search
    },
    data() {
      return {
        assessment:{
          branch_id:'',
          student_id: '',
          title:'',
          description:'',
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
      }
    },
    created() {
      this.loadDetail(this.$route.params.id);
    },
    methods: {
      loadDetail(id){
        this.$vs.loading();
        axios.g(`/api/lms/assessments/show/${id}`)
          .then(response => {
          this.$vs.loading.close();
          this.assessment = response.data
        })
      },
    }
  }
</script>