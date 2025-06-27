

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên chiến dịch <span class="text-danger"> (*)</span></label>
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
          <label>Phương thức<span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <select class="vs-inputx vs-input--input normal" v-model="campaign.type" disabled="true">
              <option value="" disabled>Chọn loại</option>
              <option value="0">Google Ads</option>
              <option value="1">Facebook Ads</option>
              <option value="2">Tiktok Ads</option>
              <option value="3">Quà tặng</option>
              <option value="4">Voucher</option>
              <option value="5">Khác</option>
            </select>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Ngân sách</label>
          <div class=w-full>
            <input class="vs-inputx vs-input--input normal" v-model="campaign.amount" type="number">
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
      </div>
      <div class="vx-row" v-if="campaign.type==4">
        <div class="vx-col md:w-2/3 w-full">
          <div class="vx-row">
            <div class="mb-6 vx-col md:w-1/2 w-full" >
              <label>Loại voucher</label>
              <div class=w-full>
                <select class="vs-inputx vs-input--input normal" v-model="campaign.voucher_type" disabled="true">
                  <option value="" disabled>Chọn loại voucher</option>
                  <option value="1">Voucher random</option>
                  <option value="2">Voucher cố định</option>
                </select> 
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="campaign.voucher_type==1">
              <label>Số lượng voucher</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_num" type="number">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="campaign.voucher_type==2">
              <label>Mã voucher</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_code" type="text">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="campaign.voucher_type==2">
              <label>Số lượng tối đa (không nhập là không giới hạn)</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_quota" type="number">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full" v-if="campaign.voucher_type==2">
              <label>Số lượng áp dụng trên 1 học sinh</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_limit" type="number">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Giá trị voucher</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_amount" type="number">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Số buổi học bổng</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_bonus_sessions" type="number">
              </div>
            </div>
            <div class="mb-6 vx-col md:w-1/2 w-full">
              <label>Giảm trừ %</label>
              <div class=w-full>
                <input class="vs-inputx vs-input--input normal" v-model="campaign.voucher_percent" type="number">
              </div>
            </div>
          </div>
        </div> 
        <div class="vx-col md:w-1/3 w-full">
          <div class="mb-6">
            <label><strong>Trung tâm áp dụng</strong><br> <i>(Lưu ý: Không chọn tương ứng với áp dụng tất cả trung tâm)</i></label>
            <div class=w-full>
              <div v-for="(item, index) in branches" :key="index" class="w-full">
                <vs-checkbox v-model="item.selected" class="mt-1">{{item.name}}</vs-checkbox>
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
          <vs-button class="mb-2" color="success" @click="save">Lưu</vs-button>
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
        campaign:{
          id:'',
          start_date:'',
          end_date:'',
          title: '',
          status:'',
          type:'',
          amount:'',
          note:'',
          voucher_bonus_sessions:'',
          voucher_num:'',
          voucher_amount:'',
        },
        branches:[],
      }
    },
    created() {
      axios.g(`/api/system/branches`)
      .then(response => {
        this.branches = response.data
      })
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/marketing/campaigns/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.campaign = response.data.campaign
            this.branches = response.data.branches
          }else{
            this.$router.push({ path: `/marketing/campaigns` });
          }
        })
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
          mess += " - Tên chiến dịch không được để trống<br/>";
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
        axios.p("/api/marketing/campaigns/update",{
          campaign:this.campaign,
          branches: this.branches
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
              this.$router.push('/marketing/campaigns')
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