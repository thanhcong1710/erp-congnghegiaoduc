

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <div class="vx-row">
            <div class="mb-6 vx-col w-full">
              <label>Gói khóa học <span class="text-danger"> (*)</span></label>
              <div class=w-full>
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
            <div class="mb-6 vx-col w-full">
              <label>Tên gói phí <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.name" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Số buổi học <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.session" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Số tháng học <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.number_of_months" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Giá niêm yết <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="price" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Giá thực thu <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="receivable" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Ngày hiệu lực <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <datepicker class="w-full"
                  v-model="tuition_fee.available_date"
                  placeholder="Chọn ngày hiệu lực"
                  :lang="datepickerOptions.lang"
                  @change="selectDate"
                />
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Ngày hết hiệu lực <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                  v-model="tuition_fee.expired_date"
                  placeholder="Chọn ngày hết hiệu lực"
                  :lang="datepickerOptions.lang"
                  @change="selectExpiredDate"
                />
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Loại gói phí</label>
              <div class=w-full>
                <select class="vs-inputx vs-input--input normal" v-model="tuition_fee.type_contract">
                  <option value="0">Học thử</option>
                  <option value="1">Chính thức</option>
                </select>
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Trạng thái</label>
              <div class=w-full>
                <vs-switch v-model="tuition_fee.status" color="success"/>
              </div>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <div class="mb-6">
            <label><strong>Trung tâm áp dụng</strong></label>
            <div class=w-full>
              <div v-for="(item, index) in branches" :key="index" class="w-full pl-8">
                <vs-checkbox v-model="item.selected" class="mt-1">{{item.name}}</vs-checkbox>
              </div>
            </div>
          </div>
          <div class="mb-6">
            <label><strong>Gói phí quy đổi </strong></label>
            <div class=w-full>
              <div class="vx-col w-full mb-4">
                <vue-select
                      label="name"
                      placeholder="Chọn gói phí quy đổi"
                      :options="html.tuition_fees.list"
                      v-model="html.tuition_fees.item"
                      :searchable="true"
                      language="tv-VN"
                      @input="saveTuitionFeeRelation"
                  ></vue-select>
              </div>
              <div class="vs-component vs-con-table stripe vs-table-primary">
                <div class="con-tablex vs-table--content">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <thead class="vs-table--thead">
                        <tr>
                          <!---->
                          <th colspan="1" rowspan="1">Khóa học</th>
                          <th colspan="1" rowspan="1">Gói phí</th>
                          <th colspan="1" rowspan="1">Thời gian</th>
                          <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                          <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                        </tr>
                      </thead>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in tuition_fees_relation" :key="index">
                        <td class="td vs-table--td">{{item.product_name}}</td>
                        <td class="td vs-table--td">{{item.name}}</td>
                        <td class="td vs-table--td">{{item.available_date | formatDateView}} - {{item.expired_date | formatDateView}}</td>
                        <td class="td vs-table--td text-center">{{ item.status == 1 ? 'kích hoạt': 'Không kích hoạt'}}</td>
                        <td class="td vs-table--td text-center list-action"> 
                          <vs-button size="small" color="danger" @click="deleteTuitionFeeRelation(item)"><i class="fa-solid fa-trash"></i></vs-button>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/settings/tuition-fees`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
        </div>
      </div>
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';

  export default {
    components: {
      datepicker,
      "vue-select": vSelect
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
        alert:{
          active: false,
          body: '',
          color:'',
        },
        html:{
          products: {
            item: '',
            list: []
          },
          tuition_fees: {
            item: '',
            list: []
          }
        },
        branches:[],
        tuition_fees_relation: [],
        tuition_fee:{
          product_id:'',
          name: '',
          session: '',
          number_of_months:'',
          price:'',
          receivable:'',
          available_date:'',
          expired_date: '',
          type_contract: 1,
          status:1,
        },
        price:'',
        receivable:'',
      }
    },
    watch: {
      price: function (val) {
        const value = u.fmc(val)
        this.price = value.s
        this.tuition_fee.price = value.n
      },
      receivable: function (val) {
        const value = u.fmc(val)
        this.receivable = value.s
        this.tuition_fee.receivable = value.n
      }
    },
    created() {
      axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
      axios.g(`/api/system/branches`)
      .then(response => {
        this.branches = response.data
      })
      axios.g(`/api/system/tuition-fees?status=1`)
      .then(response => {
        this.html.tuition_fees.list = response.data
      })
    },
    methods: {
      selectDate(date){
        if (date) {
          this.tuition_fee.available_date = moment(date).format("YYYY-MM-DD");
        }
      },
      selectExpiredDate(date){
        if (date) {
          this.tuition_fee.expired_date = moment(date).format("YYYY-MM-DD");
        }
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.tuition_fee.product_id = product_id
        }else{
          this.tuition_fee.product_id = ""
        }
      },
      saveTuitionFeeRelation(data =null){
        if (data && typeof data === 'object') {
          const check_exit = 0;
          this.tuition_fees_relation.map(item => {
            if(item.id==data.id){
              check_exit = 1;
            }
          })
          if(!check_exit){
            this.tuition_fees_relation.push(data)
          }
          this.html.tuition_fees.item=''
        }
      },
      deleteTuitionFeeRelation(data){
        const ids_tuition = []
        this.tuition_fees_relation.map(item => {
          if(data.id != item.id){
            ids_tuition.push(item)
          }
        })
        this.tuition_fees_relation = ids_tuition
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.tuition_fee.product_id == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_fee.name == "") {
          mess += " - Tên gói phí không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_fee.session == "") {
          mess += " - Số buổi học không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.number_of_months == "") {
          mess += " - Số tháng học không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.price == "") {
          mess += " - Giá niêm yết không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.receivable == "") {
          mess += " - Giá thực thu không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.available_date == "") {
          mess += " - Ngày hiệu lực không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_fee.expired_date == "") {
          mess += " - Ngày hết hiệu lực không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/settings/tuition-fees/add",{
          tuition_fee: this.tuition_fee,
          branches : this.branches,
          tuition_fees_relation: this.tuition_fees_relation,
        })
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
              this.$router.push('/settings/tuition-fees')
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