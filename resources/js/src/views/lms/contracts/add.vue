<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Nhập tên trung tâm để giới hạn phạm vi tìm kiếm"
                    :options="html.branches.list"
                    v-model="html.branches.item"
                    :searchable="true"
                    language="tv-VN"
                     @input="saveBranch"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4" v-if="contract.branch_id">
              <label> Chọn học sinh <span class="text-danger"> (*)</span></label>
              <search
                  :endpoint="filter.search.link"
                  :suggestStudents="filter.search.find"
                  :onSelectStudent="filter.search.action">
              </search>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phụ huynh</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_name1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.student_branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ec_leader_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giám đốc trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ceo_branch_name"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin đăng ký chương trình học</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Loại hợp đồng <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="contract.type" >
                <option value="">Chọn loại hợp đồng</option>
                <option value="0">Học thử</option>
                <option value="1">Chính thức</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Chương trình học</label>
              <vue-select
                    label="name"
                    placeholder="Chọn chương trình học"
                    :options="html.products.list"
                    v-model="html.products.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveProduct"
                    :disabled="!contract.student_id"
                ></vue-select>
            </div>
          </div>
        </div>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row mt-5">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
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
        calling:false,
        filter: {
          search: {
              link: 0,
              display: 'hidden',
              find: keyword => this.searchSuggestStudent(keyword),
              action: student => this.selectStudent(student)
          }
        },
        html:{
          branches: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
        },
        contract:{
          branch_id:'',
          student_id: '',
          product_id: '',
          type:'',
        },
        student_info:{

        },
        alert:{
          active: false,
          body: '',
          color:'',
        }
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.html.branches.list = response.data
      })
      axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
    },
    methods: {
      searchSuggestStudent(keyword) {
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/students/search-contract`,{
              branch_id: this.contract.branch_id,
              keyword: keyword
            }).then((response) => {
                const resp = response.data.length ? response.data : [{
                    label: 'Không tìm thấy',
                    branch_name: 'Không có kết quả nào phù hợp'
                }]
                this.calling = false
                resolve(resp)
            }).catch(e => console.log(e))
          })
        }
      },
      selectStudent(student) {
        this.student_info = student
        this.contract.student_id = student.student_id
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.contract.branch_id = branch_id
        }else{
          this.contract.branch_id = ""
        }
        this.student_info = {}
        this.contract.student_id=""
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.contract.product_id = product_id
        }else{
          this.contract.product_id = ""
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.parent.gender == "") {
          mess += " - Danh xưng không được để trống<br/>";
          resp = false;
        }
        if (this.parent.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 != "" && !u.vld.phone(this.parent.mobile_1)) {
          mess += " - Số điện thoại không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.mobile_2 != "" && !u.vld.phone(this.parent.mobile_2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.owner_id == "") {
          mess += " - Người phụ trách không được để trống<br/>";
          resp = false;
        }
        if (this.parent.source_id == "") {
          mess += " - Nguồn không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/crm/parents/add",this.parent)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/crm/parent')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>