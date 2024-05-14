

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Mã chiết khấu <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="discount_code.code" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên chiết khấu <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="discount_code.name" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <!-- <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Buổi học bổng</label>
          <div class=w-full>
            <input type="number" v-model="discount_code.bonus_sessions" class="vs-inputx vs-input--input normal">
          </div>
        </div> -->
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tỷ lệ chiết khấu % <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="number" v-model="discount_code.percent" class="vs-inputx vs-input--input normal" @change="caculatorDiscount()">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Giá gốc gói phí <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="number" v-model="discount_code.price" class="vs-inputx vs-input--input normal" @change="caculatorDiscount()">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tiền chiết khấu</label>
          <div class=w-full>
            <input type="text" :value="discount_code.discount | formatNumber" class="vs-inputx vs-input--input normal" disabled="true">
          </div>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-6 ">
          <label>Ngày bắt đầu <span class="text-danger"> (*)</span></label>
          <datepicker class="w-full"
            v-model="discount_code.start_date"
            placeholder="Chọn ngày bắt đầu"
            :lang="datepickerOptions.lang"
            @change="selectDate"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-6 ">
          <label>Ngày kết thúc <span class="text-danger"> (*)</span></label>
          <datepicker class="w-full"
            v-model="discount_code.end_date"
            placeholder="Chọn ngày kết thúc"
            :lang="datepickerOptions.lang"
            @change="selectEndDate"
          />
        </div>
        
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Trạng thái</label>
          <div class=w-full>
            <vs-switch v-model="discount_code.status" color="success"/>
          </div>
        </div>
        <div class="mb-6 vx-col w-full">
          <label><strong>Gói phí áp dụng </strong></label>
          <div class=w-full>
            <div class="vx-col md:w-1/3  w-full mb-4">
              <vue-select
                    label="name"
                    placeholder="Chọn gói phí áp dụng"
                    :options="html.tuition_fees.list"
                    v-model="html.tuition_fees.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveTuitionFee"
                ></vue-select>
            </div>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="1" rowspan="1" class="text-center">Khóa học</th>
                        <th colspan="1" rowspan="1">Gói phí</th>
                        <th colspan="1" rowspan="1" class="text-center">Thời gian</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                        <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in tuition_fees" :key="index">
                      <td class="td vs-table--td text-center">{{item.product_name}}</td>
                      <td class="td vs-table--td">{{item.name}}</td>
                      <td class="td vs-table--td text-center">{{item.available_date | formatDateView}} - {{item.expired_date | formatDateView}}</td>
                      <td class="td vs-table--td text-center">{{ item.status == 1 ? 'kích hoạt': 'Không kích hoạt'}}</td>
                      <td class="td vs-table--td text-center list-action"> 
                        <vs-button size="small" color="danger" @click="deleteTuitionFee(item)"><i class="fa-solid fa-trash"></i></vs-button>
                      </td>
                    </tr>
                  </table>
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
          <router-link class="btn btn-danger" :to="`/settings/discount-codes`">
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
          tuition_fees: {
            item: '',
            list: []
          }
        },
        tuition_fees:[],
        discount_code:{
          code:'',
          name: '',
          bonus_sessions: '',
          start_date: '',
          end_date: '',
          status:1,
          discount:'',
          percent:'',
          price:''
        },
      }
    },
    created() {
      axios.g(`/api/system/tuition-fees?status=1`)
      .then(response => {
        this.html.tuition_fees.list = response.data
      })
    },
    methods: {
      selectDate(date){
        if (date) {
          this.discount_code.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      selectEndDate(date){
        if (date) {
          this.discount_code.end_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorDiscount(){
        if(this.discount_code.price && this.discount_code.percent){
          this.discount_code.discount = Math.round(Number(this.discount_code.percent)* Number(this.discount_code.price)/100)
        }else{
          this.discount_code.discount=""
        }
      },
      saveTuitionFee(data =null){
        if (data && typeof data === 'object') {
          let check_exit = 0;
          this.tuition_fees.map(item => {
            if(item.id==data.id){
              check_exit = 1;
            }
          })
          if(!check_exit){
            this.tuition_fees.push(data)
          }
          this.html.tuition_fees.item=''
        }
      },
      deleteTuitionFee(data){
        const ids_tuition = []
        this.tuition_fees.map(item => {
          if(data.id != item.id){
            ids_tuition.push(item)
          }
        })
        this.tuition_fees = ids_tuition
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.discount_code.code == "") {
          mess += " - Mã chiết khấu không được để trống<br/>";
          resp = false;
        }
        if (this.discount_code.name == "") {
          mess += " - Tên chiết khấu không được để trống<br/>";
          resp = false;
        }
        if (this.discount_code.price == "") {
          mess += " - Giá gốc gói phí không được để trống<br/>";
          resp = false;
        }
        if (this.discount_code.percent == "") {
          mess += " - Tỷ lệ chiết khấu không được để trống<br/>";
          resp = false;
        }
        if (this.discount_code.start_date == "") {
          mess += " - Ngày bắt đầu không được để trống<br/>";
          resp = false;
        }
        if (this.discount_code.end_date == "") {
          mess += " - Ngày kết thúc không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/settings/discount-codes/add",{
            discount_code : this.discount_code,
            tuition_fees : this.tuition_fees
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
              this.$router.push('/settings/discount-codes')
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