<template>
  <div class="student-detail-sessions mt-5" v-if="contract_info.id">
    <div class="vx-row">
      <div class="vx-col md:w-1/4 w-full mb-4">
        <label>Tổng số buổi: <strong>{{contract_info.summary_sessions}}</strong></label>
      </div>
      <div class="vx-col md:w-1/4 w-full mb-4">
        <label>Số buổi đã học: <strong>{{contract_info.done_sessions}}</strong></label>
      </div>
    </div>
    <div>
      <div class="flex flex-wrap mt-5">
        <div :class="item.attendance_status ==1 || item.attendance_status ==3 ? 'box-active' : 'box-danger'" v-for="(item, index) in done_sessions" :key="index">
          <div class="box-item-student border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1">
              <div class="label-box-schedule text-center">{{item.cls_name}}</div>
              <div class="text-date-box-schedule text-center">Buổi {{index+1}} - {{item.class_date | formatDateViewDay}}</div>
              <div class="text-center">
                <span class="box-status">{{item.attendance_status ==1 ? 'Đã học' : (item.attendance_status ==3 ? 'Đã học bù' : 'Chưa học')}}</span>
              </div>
          </div>
        </div>
        <div v-for="(item, index) in next_sessions" :key="index">
          <div class="box-item-student border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1">
              <div class="label-box-schedule text-center">{{item.cls_name}}</div>
              <div class="text-date-box-schedule text-center">Buổi {{index+1}} - {{item.class_date | formatDateViewDay}}</div>
              <div class="text-center"><span class="box-status">Sắp học</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
      },
    },
    data() {
      return {
        done_sessions:[],
        next_sessions:[],
        contract_info:{}
      }
    },
    async created() {
      this.getSessions();
    },
    methods: {
      getSessions(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/sessions', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contract_info = response.data.contract_info
            this.done_sessions = response.data.done_sessions
            this.next_sessions = response.data.next_sessions
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
    }
  }
</script>
<style>
.student-detail-sessions .box-item-student .label-box-schedule{
  font-size: 13px;
}
.student-detail-sessions .box-item-student .text-date-box-schedule{
  font-size: 11px;
}
.student-detail-sessions .box-item-student .box-status{
    font-size: 10px;
    background: rgba(var(--vs-primary),1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.student-detail-sessions .box-item-student{
  border: 1px solid rgba(var(--vs-primary),1);
  background: transparent !important;
}

.student-detail-sessions .box-active .box-item-student{
  border: 1px solid rgba(var(--vs-success), 1);
}
.student-detail-sessions .box-active .box-item-student .box-status{
    background: rgba(var(--vs-success), 1);
}
.student-detail-sessions .box-danger .box-item-student{
  border: 1px solid rgba(var(--vs-danger), 1);
}
.student-detail-sessions .box-danger .box-item-student .box-status{
    background: rgba(var(--vs-danger), 1);
}
</style>