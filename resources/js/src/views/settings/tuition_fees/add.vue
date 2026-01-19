

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <div class="vx-row">
            <div class="mb-6 vx-col w-full">
              <label>Tên gói phí <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.name" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Hình thức học <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <select class="vs-inputx vs-input--input normal" v-model="tuition_fee.type">
                  <option value="0">Online và Offline </option>
                  <option value="1">Offline</option>
                  <option value="2">Online</option>
                </select>
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Loại gói phí <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <select class="vs-inputx vs-input--input normal" v-model="tuition_fee.type_fee">
                  <option value="">Chọn loại gói phí </option>
                  <option value="1">Gói lẻ</option>
                  <option value="2">Gói combo</option>
                </select>
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="tuition_fee.type_fee==1">
              <label>Khóa học <span class="text-danger"> (*)</span></label>
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
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="tuition_fee.type_fee==1">
              <label>Số buổi học <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.session" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Số đơn sau tách <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="tuition_fee.number_of_months" class="vs-inputx vs-input--input normal">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Giá bán <span class="text-danger"> (*)</span></label>
              <div class=w-full>
                <input type="text" v-model="price" class="vs-inputx vs-input--input normal">
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
          <div class="mb-6" v-if="tuition_fee.type_fee==2">
            <label><strong>Gói phí lẻ tương ứng </strong></label>
            <div class=w-full>
              <div class="vx-col w-full mb-4">
                <vue-select
                      label="name"
                      placeholder="Chọn gói phí lẻ"
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
                          <th colspan="1" rowspan="1">Gói phí</th>
                          <th colspan="1" rowspan="1">Giá gốc</th>
                          <th colspan="1" rowspan="1">Giá mua combo</th>
                          <th colspan="1" rowspan="1">Thứ tự học</th>
                          <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                        </tr>
                      </thead>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in tuition_fees_relation" :key="index">
                        <td class="td vs-table--td">{{item.name}}</td>
                        <td class="td vs-table--td">{{item.price | formatNumber}}</td>
                        <td class="td vs-table--td">
                          <input type="text" :value="item.price_combo_display"  @input="formatPriceCombo(item, $event.target.value)" class="vs-inputx vs-input--input normal">
                        </td>
                        <td class="td vs-table--td">
                          <input type="number" v-model="item.stt" class="vs-inputx vs-input--input normal">
                        </td>
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
          status:1,
          type_fee:'',
          type:0
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
      axios.g(`/api/system/tuition-fees?status=1&type_fee=1`)
      .then(response => {
        this.html.tuition_fees.list = response.data
      })
    },
    methods: {
      formatPriceCombo(item, val) {
        const value = u.fmc(val)
        item.price_combo_display = value.s   // hiển thị
        item.price_combo = value.n            // lưu number
      },
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
      saveTuitionFeeRelation(data = null){
        if (data && typeof data === 'object') {
          let check_exit = false;
          this.tuition_fees_relation.forEach(item => {
            if(item.id === data.id){
              check_exit = true;
            }
          });

          if(!check_exit){
            this.tuition_fees_relation.push({
              ...data,
              price_combo: '',
              price_combo_display: ''
            })
          }
          this.html.tuition_fees.item = ''
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
        if (this.tuition_fee.name == "") {
          mess += " - Tên gói phí không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_fee.product_id == "" && this.tuition_fee.type_fee==1) {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_fee.session == "" && this.tuition_fee.type_fee==1) {
          mess += " - Số buổi học không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.number_of_months == "") {
          mess += " - Số đơn sau tách không được để trống<br/>";
          resp = false;
        }
         if (this.tuition_fee.price === "") {
          mess += " - Giá bán không được để trống<br/>";
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
        if (this.tuition_fee.type_fee === "") {
          mess += " - Loại gói phí không được để trống<br/>";
          resp = false;
        }
        // Validate tổng giá combo = giá bán
        if (this.tuition_fee.type_fee == 2) {
          const totalComboPrice = this.getTotalPriceCombo()
          const salePrice = Number(this.tuition_fee.price || 0)

          if (totalComboPrice !== salePrice) {
            mess += `- Tổng giá các gói lẻ (<b>${u.fmc(totalComboPrice).s}</b>) 
              phải bằng giá bán (<b>${u.fmc(salePrice).s}</b>)<br/>`;
            resp = false;
          }
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