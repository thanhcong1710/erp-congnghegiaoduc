

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên chính sách <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="campaign.title" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Ngày bắt đầu <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <datepicker class="w-full"
                v-model="campaign.start_date"
                placeholder="Chọn ngày bắt đầu"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Ngày kết thúc <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <datepicker class="w-full"
                v-model="campaign.end_date"
                placeholder="Chọn ngày kết thúc"
                :lang="datepickerOptions.lang"
                @change="selectEndDate"
              />
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Trạng thái</label>
          <div class=w-full>
            <vs-switch v-model="campaign.status" color="success"/>
          </div>
        </div>
        <div class="mb-6 vx-col w-full">
          <label>Nội dung</label>
          <div class=w-full>
            <textarea class="vs-inputx vs-input--input normal" v-model="campaign.note"></textarea>
          </div>
        </div>
        <div class="mb-6 vx-col w-full">
            <label>Gói phí áp dụng </label>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody " style="min-height: 250px">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th rowspan="2" class="text-center" style="min-width: 280px">Gói phí</th>
                        <th colspan="2" class="text-center">KH giới thiệu</th>
                        <th colspan="2" class="text-center">KH được giới thiệu</th>
                        <th rowspan="2" class="text-center">Thao tác</th>
                      </tr>
                      <tr>
                        <!---->
                        <th class="text-center">Giảm giá</th>
                        <th class="text-center">Học bổng</th>
                        <th class="text-center">Giảm giá</th>
                        <th class="text-center">Học bổng</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td">
                        <vue-select
                            label="name"
                            placeholder="Chọn gói phí"
                            :options="html.tuition_fees.list"
                            v-model="html.tuition_fees.item"
                            :searchable="true"
                            language="tv-VN"
                            style="background:#fff"
                        ></vue-select>
                      </td>
                      <td class="td vs-table--td">
                        <input type="number" v-model="input.amount" class="vs-inputx vs-input--input normal">
                      </td>
                      <td class="td vs-table--td">
                        <input type="number" v-model="input.bonus_session" class="vs-inputx vs-input--input normal">
                      </td>
                      <td class="td vs-table--td">
                        <input type="number" v-model="input.ref_amount" class="vs-inputx vs-input--input normal">
                      </td>
                      <td class="td vs-table--td">
                        <input type="number" v-model="input.ref_bonus_session" class="vs-inputx vs-input--input normal">
                      </td>
                      <td class="td vs-table--td text-center list-action"> 
                        <vs-button size="small" color="success" @click="saveTuitionFee()"><i class="fa-solid fa-plus"></i></vs-button>
                      </td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in tuition_fees" :key="index">
                      <td class="td vs-table--td">{{item.tuition_fee.name}}</td>
                      <td class="td vs-table--td">{{item.amount | formatMoney}}</td>
                      <td class="td vs-table--td">{{item.bonus_session}}</td>
                      <td class="td vs-table--td">{{item.ref_amount | formatMoney}}</td>
                      <td class="td vs-table--td">{{item.ref_bonus_session}}</td>
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
      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/marketing/campaigns`">
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
  import axios from '../../../../http/axios.js'
  import u from '../../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';

  export default {
    components: {
      datepicker,
      "vue-select": vSelect
    },
    data() {
      return {
        html:{
          tuition_fees: {
            item: '',
            list: []
          }
        },
        tuition_fees:[],
        input:{
          amount: '',
          bonus_session:'',
          ref_amount: '',
          ref_bonus_session:''
        },
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
        campaign:{
          start_date:'',
          end_date:'',
          title: '',
          status:1,
          note:'',
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
      saveTuitionFee(){
        var check_exit = 0;
        this.tuition_fees.map(item => {
          if(item.id == this.html.tuition_fees.item.id){
            check_exit = 1;
          }
        })
        if(!check_exit){
          this.tuition_fees.push({
            tuition_fee: this.html.tuition_fees.item,
            amount :  this.input.amount ?  this.input.amount :0,
            bonus_session : this.input.bonus_session ?  this.input.bonus_session :0,
            ref_amount :  this.input.ref_amount ?  this.input.ref_amount :0,
            ref_bonus_session : this.input.ref_bonus_session ?  this.input.ref_bonus_session :0
          })
        }
        this.html.tuition_fees.item=''
        this.input.amount=''
        this.input.bonus_session=''
        this.input.ref_amount=''
        this.input.ref_bonus_session=''
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
      selectDate(date){
        if (date) {
          this.campaign.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      selectEndDate(date){
        if (date) {
          this.campaign.end_date = moment(date).format("YYYY-MM-DD");
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.campaign.title == "") {
          mess += " - Tên chính sách không được để trống<br/>";
          resp = false;
        }
        if (this.campaign.start_date == "") {
          mess += " - Ngày bắt đầu không được để trống<br/>";
          resp = false;
        }
        if (this.campaign.end_date == "") {
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
        axios.p("/api/marketing/c2c/campaigns/add",{
          campaign:this.campaign,
          tuition_fees: this.tuition_fees
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
              this.$router.push('/marketing/c2c/campaigns')
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
<style scoped>
[dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .vs-table--thead th, [dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .vs-table--thead td{
  border: 1px solid #ccc;
}
</style>