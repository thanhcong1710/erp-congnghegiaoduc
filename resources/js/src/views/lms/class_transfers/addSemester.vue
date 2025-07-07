<template>

  <div id="page-enrolments-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/3 mb-4">
          <label>Trung tâm <span class="text-danger"> (*)</span></label>
          <vue-select
              label="name"
              placeholder="Chọn trung tâm"
              :options="html.branches.list"
              v-model="html.branches.item"
              :searchable="true"
              language="tv-VN"
              @input="saveBranch"
          ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 mb-4">
          <label >Khóa học</label>
          <vue-select
                label="name"
                placeholder="Chọn khóa học"
                :options="html.products.list"
                v-model="html.products.item"
                :searchable="true"
                language="tv-VN"
                @input="saveProduct"
            ></vue-select>
        </div>
      </div>
      <vs-divider/>
      <div class="vx-row">
        <div class="vx-col md:w-1/4 w-full item-first" style="border-right: 1px solid #ccc;">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Lớp chuyển</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <tree
                :data="classes"
                text-field-name="text"
                allow-batch
                @item-click="selectClass"
              >
              </tree>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin lớp học</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text text-center">
                          <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                            <input type="checkbox" v-model="selectAll" class="vs-checkbox--input" >
                            <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                              <span class="vs-checkbox--check">
                                <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                              </span>
                            </span>
                          </div>
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Hợp đồng
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Gói phí
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Buổi học
                          <!---->
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in students" :key="index">
                    <td class="td vs-table--td">
                      <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                        <input type="checkbox" v-model="checked_list" :value="item.contract_id" class="vs-checkbox--input" >
                        <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                          <span class="vs-checkbox--check">
                            <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                          </span>
                        </span>
                      </div>
                    </td>
                    <td class="td vs-table--td">
                      <p><strong>{{item.contract_code}}</strong></p>
                      <p>Tên HS: {{item.name}}</p>
                      <p>Mã HS:{{item.lms_code}}</p>
                      <p>Mã LMS:{{item.lms_id}}</p>
                      <p>Ngày bắt đầu: {{item.enrolment_start_date}}</p>
                      <p>Ngày kết thúc: {{item.enrolment_last_date}}</p>
                    </td>
                    <td class="td vs-table--td">
                      <p><strong>{{item.tuition_fee_name}}</strong></p>
                      <p>Phải đóng: {{item.must_charge | formatCurrency}}</p>
                      <p>Đã đóng: {{item.total_charged | formatCurrency}}</p>
                    </td>
                    <td class="td vs-table--td">
                      <p>Số buổi đã học: <strong>{{item.done_sessions}}</strong></p>
                      <p>Tổng số buổi: {{item.summary_sessions}}</p>
                      <p>Trạng thái: 
                        <strong v-if="item.left_sessions > 0" class="text-success">Đang học</strong>
                        <strong v-else class="text-danger">Hết số buổi học</strong>
                      </p>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/4 w-full item-last" style="border-left: 1px solid #ccc;">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Lớp nhận</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <tree
                :data="classesTo"
                text-field-name="text"
                allow-batch
                @item-click="selectClassTo"
              >
              </tree>
            </div>
          </div>
        </div>
      </div>
      <vs-divider/>
      <div class="vx-row">
        <div class="vx-col md:w-1/3 w-full mb-4">
          <p>Bạn đã lựa chọn <b>{{checked_list.length}}</b> học sinh</p> 
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Ngày bắt đầu chuyển kỳ <span class="text-danger"> (*)</span></label>
          <datepicker class="w-full"
            v-model="transfer_date"
            placeholder="Chọn ngày bắt đầu"
            :lang="datepickerOptions.lang"
            @change="selectDate"
            :not-before="temp.min_date"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4 pt-5">
          <router-link class="btn btn-danger" :to="`/lms/class_transfers`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Chuyển kỳ</vs-button> 
        </div>
      </div>
      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
        <div v-html="alert.body"></div>
      </vs-alert>
    </vx-card>
  </div>

</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';
  import tree from 'vue-jstree'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      tree,
    },
    data() {
      return {
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: "",
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ]
          }
        },
        select_type:1,
        checked_list: [],
        alert:{
          active: false,
          body: '',
          color:'',
        },
        classes: [],
        classesTo: [],
        html:{
          branches: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
          tuition_fee:{
            item: '',
            list: []
          },
          discount_codes:{
            item: '',
            list: []
          }
        },
        enrol:{
          branch_id:'',
          product_id:''
        },
        class_info:{
          class_id:'',
        },
        students:[],
        class_dates: [],
        from_class_id:"",
        to_class_id:"",
        transfer_date:"",
        temp:{
          min_date: new Date(),
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
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
    computed: {
      selectAll: {
        get: function() {
          return (
            parseInt(this.checked_list.length) === parseInt(this.students.length)
          );
        },
        set: function(value) {
          const selected_list = [];
          if (value) {
            this.students.forEach(student => {
              selected_list.push(student.contract_id);
            });
          }
          this.checked_list = selected_list;
        }
      }
    },
    methods: {
      selectDate(date){
        if (date) {
          this.transfer_date = moment(date).format("YYYY-MM-DD");
        }
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.enrol.branch_id = branch_id
        }else{
          this.enrol.branch_id = ""
        }
        this.loadClasses();
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.enrol.product_id = product_id
        }else{
          this.enrol.product_id = ""
        }
        this.loadClasses();
      },
      loadClasses(){
        if(this.enrol.branch_id && this.enrol.product_id){
          this.$vs.loading();
          axios.p(`/api/lms/enrolments/load-classes`, {
            branch_id: this.enrol.branch_id,
            product_id: this.enrol.product_id,
            select_type: this.select_type
          })
            .then(response => {
            this.$vs.loading.close();
            // this.classes = response.data
            // this.classesTo = response.data
            const rawData = response.data;
            this.classes = JSON.parse(JSON.stringify(rawData));
            this.classesTo = JSON.parse(JSON.stringify(rawData));
          })
        }else{
          this.classes =[]
          this.classesTo = []
        }
        this.students= [];
      },
      selectClass(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.loadDataClassSelected(selected_class.model.item_id)
          this.from_class_id = selected_class.model.item_id
        } else {
          this.class_info = {};
          this.students= [];
          this.from_class_id= ""
        }
      },
      selectClassTo(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.to_class_id = selected_class.model.item_id
        } else {
          this.to_class_id = ""
        }
      },
      loadDataClassSelected(class_id){
        this.$vs.loading();
        axios.g(`/api/lms/enrolments/info-class/${class_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.students = response.data.students
        })
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.from_class_id == "") {
          mess += " - Lớp chuyển không được để trống<br/>";
          resp = false;
        }
        if (this.to_class_id == "") {
          mess += " - Lớp nhận không được để trống<br/>";
          resp = false;
        }
        if (this.from_class_id == this.to_class_id) {
          mess += " - Lớp chuyển phải khác lớp nhận<br/>";
          resp = false;
        }
        if (this.transfer_date == "") {
          mess += " - Ngày chuyển kỳ không được để trống<br/>";
          resp = false;
        }
        if (this.checked_list.length == 0) {
          mess += " - Chọn học sinh chuyển kỳ<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/class_transfers/add-semester",{
          arr_contract: this.checked_list,
          from_class_id: this.from_class_id,
          to_class_id: this.to_class_id,
          transfer_date: this.transfer_date
        })
        .then((response) => {
          this.$vs.loading.close();
          if(response.data.status ==1){
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.$router.push('/lms/class_transfers')
          }else{
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'warning'
            })
          }
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>
<style>
.view-enrolments.con-vs-popup .vs-popup{
  width: 90%;
}

.td.vs-table--td{
  vertical-align: top;
}
.box-item-student.active{
  border: 1px solid rgba(var(--vs-success), 1);
  background: transparent !important;
}
.box-item-student .label-box-schedule{
  font-size: 13px;
}
.box-item-student .text-date-box-schedule{
  font-size: 11px;
}
.box-item-student.active .box-status{
    font-size: 10px;
    background: rgba(var(--vs-success), 1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student .box-status{
    font-size: 10px;
    background: rgba(var(--vs-primary),1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student{
  border: 1px solid rgba(var(--vs-primary),1);
  background: transparent !important;
}
</style>