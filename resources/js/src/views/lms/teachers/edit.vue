<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
        <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Cập nhật thông tin buổi dạy</h5>
        <div class="mb-6 p-4 rounded-lg shadow-sm" style="background: #72ae7517; border-left: 4px solid #28c76f; font-size: 15px;">
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Trung tâm:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <strong class="text-dark">{{schedule_info.branch_name}}</strong>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Tên lớp học:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <strong class="text-dark">{{schedule_info.class_name}}</strong>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Thời gian:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <span class="text-primary font-medium">{{schedule_info.class_date}} <strong v-if="schedule_info.subject_stt">(Buổi {{schedule_info.subject_stt}})</strong></span>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Giáo viên:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <span>{{schedule_info.teacher_name}}</span>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Trợ giảng:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <span>{{schedule_info.ta_name}}</span>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Sỹ số:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full" :style="schedule_info.num_students >= schedule_info.max_students ? 'color:red' : ''">
                  <span><strong>{{schedule_info.num_students}}</strong>/{{schedule_info.max_students}}</span>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Ca học, phòng học:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <span>{{schedule_info.shift_text}} {{schedule_info.room_text}}</span>
                </div>
            </div>
            <div class="vx-row mb-2">
                <div class="vx-col sm:w-1/4 w-full text-gray-600 font-semibold text-right sm:text-right text-left">
                  <span>Quản lý lớp học:</span>
                </div>
                <div class="vx-col sm:w-3/4 w-full">
                  <span>{{schedule_info.cm_name}}</span>
                </div>
            </div>
        </div>

        <div class="vx-row">
          <div class="vx-col sm:w-1/2 w-full mb-5">
            <label class="text-sm font-semibold mb-2 block text-dark"><i class="fa-solid fa-chalkboard-user mr-1 text-primary"></i> Giáo viên</label>
            <vue-select
              label="label"
              placeholder="Chọn giáo viên"
              :options="html.teachers.list"
              v-model="html.teachers.item"
              :searchable="true"
              language="tv-VN"
              @input="saveTeacher"
              class="w-full"
            ></vue-select>
          </div>
          <div class="vx-col sm:w-1/2 w-full mb-5">
            <label class="text-sm font-semibold mb-2 block text-dark"><i class="fa-solid fa-user-tie mr-1 text-warning"></i> Trợ giảng</label>
            <vue-select
                label="label"
                placeholder="Chọn trợ giảng"
                :options="html.tas.list"
                v-model="html.tas.item"
                :searchable="true"
                language="tv-VN"
                @input="saveTA"
                class="w-full"
            ></vue-select>
          </div>
        </div>
        
        <div class="vx-row">
          <div class="vx-col sm:w-1/2 w-full mb-5">
            <label class="text-sm font-semibold mb-2 block text-dark"><i class="fa-regular fa-comment-dots mr-1 text-success"></i> Ghi chú buổi học</label>
            <vs-input class="w-full" placeholder="Nhập ghi chú" v-model="schedule_info.note" />
          </div>
          <div class="vx-col sm:w-1/2 w-full mb-5">
            <label class="text-sm font-semibold mb-2 block text-dark"><i class="fa-solid fa-video mr-1 text-danger"></i> Link Record</label>
            <vs-input class="w-full" placeholder="Dán link record (Google Drive, Zoom...)" v-model="schedule_info.link_record" />
          </div>
        </div>

        <vs-divider class="my-5" />

        <div class="vx-row mb-6">
          <div class="vx-col w-full">
            <div class="flex items-center mb-4">
              <h5 class="mb-0 font-semibold"><i class="fa-solid fa-clipboard-user mr-2 text-primary"></i> Danh sách học sinh & Điểm danh</h5>
            </div>
            <vs-table stripe :data="schedule_info.students || []" class="w-full shadow-sm rounded-lg" style="border: 1px solid #f0f0f0;">
              <template slot="thead">
                <vs-th><div class="w-full text-center font-bold">STT</div></vs-th>
                <vs-th><div class="w-full text-center font-bold">Mã HS</div></vs-th>
                <vs-th><div class="w-full text-center font-bold">Tên học sinh</div></vs-th>
                <vs-th><div class="w-full text-center font-bold">Trạng thái (Có mặt)</div></vs-th>
              </template>
              <template slot-scope="{ data }">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data" class="hover:bg-gray-50 transition-colors">
                  <vs-td class="text-center font-medium">{{ indextr + 1 }}</vs-td>
                  <vs-td class="font-semibold text-primary text-center">{{ tr.lms_code }}</vs-td>
                  <vs-td class="font-medium text-dark text-center">{{ tr.student_name }}</vs-td>
                  <vs-td class="text-center">
                    <div class="flex items-center justify-center">
                      <span class="mr-2 font-medium cursor-pointer" :class="!tr.is_present ? 'text-danger' : 'text-gray-400'" @click="tr.is_present = false">Không</span>
                      <vs-switch color="success" v-model="tr.is_present" />
                      <span class="ml-2 font-medium cursor-pointer" :class="tr.is_present ? 'text-success' : 'text-gray-400'" @click="tr.is_present = true">Có</span>
                    </div>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </div>
        <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
          <div v-html="alert.body"></div>
        </vs-alert>
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/lms/teachers`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Lưu</vs-button>
        </div>
    </vx-card>
  </div>
</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  
  export default {
    components: {
      "vue-select": select,
    },
    data() {
      return {
        html:{
          teachers:{
            item: '',
            list: []
          },
          tas:{
            item: '',
            list: []
          },
        },
        schedule_info:{
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
      }
    },
    async created() {
      axios.g(`/api/system/teachers/0`)
        .then(response => {
        this.html.teachers.list = response.data
      })
      await axios.g(`/api/system/ta/0`)
        .then(response => {
        this.html.tas.list = response.data
      })
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/teachers/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          const data = response.data
          if (data.students) {
            data.students.forEach(s => {
              this.$set(s, 'is_present', s.attendance_status == 1);
            });
          } else {
            data.students = []
          }
          this.schedule_info = data
          this.html.teachers.item = this.html.teachers.list.filter(item => item.id == response.data.teacher_id)[0]
          this.html.tas.item = this.html.tas.list.filter(item => item.id == response.data.ta_id)[0]
        })
      },
      saveTA(data = null){
        if (data && typeof data === 'object') {
          const ta_id = data.id
          this.schedule_info.ta_id = ta_id
        }else{
          this.schedule_info.ta_id = ""
        }
      },
      saveTeacher(data = null){
        if (data && typeof data === 'object') {
          const teacher_id = data.id
          this.schedule_info.teacher_id = teacher_id
        }else{
          this.schedule_info.teacher_id = ""
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.schedule_info.teacher_id == "") {
          mess += " - Giáo viên không được để trống<br/>";
          resp = false;
        }
        if (this.schedule_info.ta_id == "") {
          mess += " - Trợ giảng không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/teachers/update",this.schedule_info)
          .then((response) => {
            this.$vs.loading.close();
            if (response.data.status) {
              this.$vs.notify({
                title: 'Thành Công',
                text: response.data.message,
                color: 'success',
                iconPack: 'feather',
                icon: 'icon-check'
              })
              this.$router.push('/lms/teachers')
            }else{
              this.$vs.notify({
                title: 'Lỗi',
                text: response.data.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            }
          })
          .catch((e) => {
            console.log(error);
            this.$vs.loading.close();
          });
      },
    },
  }
</script>