

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên ngày nghỉ lễ <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="holiday.name" class="vs-inputx vs-input--input normal" disabled="true">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Ngày bắt đầu <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <datepicker class="w-full"
                v-model="holiday.start_date"
                placeholder="Chọn ngày bắt đầu"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                disabled="true"
              />
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Ngày kết thúc <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <datepicker class="w-full"
                v-model="holiday.end_date"
                placeholder="Chọn ngày kết thúc"
                :lang="datepickerOptions.lang"
                @change="selectEndDate"
                disabled="true"
              />
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Trạng thái</label>
          <div class=w-full>
            <vs-switch v-model="holiday.status" color="success" disabled="true"/>
          </div>
        </div>
        
      </div>
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/2 w-full">
          <label>Trung tâm áp dụng</label>
          <div class=w-full>
             <div v-for="(item, index) in branches" :key="index" class="w-full pl-8">
                <vs-checkbox v-model="item.selected" class="mt-1" disabled="true">{{item.name}}</vs-checkbox>
              </div>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/2 w-full">
          <label>Sản phẩm áp dụng</label>
          <div class=w-full>
             <div v-for="(item, index) in products" :key="index" class="w-full pl-8">
                <vs-checkbox v-model="item.selected" class="mt-1" disabled="true">{{item.name}}</vs-checkbox>
              </div>
          </div>
        </div>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/settings/holidays`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
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
        holiday:{
          start_date:'',
          end_date:'',
          name: '',
          status:1,
        },
        branches:[],
        products:[]
      }
    },
    created() {
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/settings/holidays/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.holiday = response.data.holiday
            this.branches = response.data.branches
            this.products = response.data.products
          }else{
            this.$router.push({ path: `/settings/holidays` });
          }
        })
      },
      selectDate(date){
        if (date) {
          this.holiday.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      selectEndDate(date){
        if (date) {
          this.holiday.end_date = moment(date).format("YYYY-MM-DD");
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.holiday.name == "") {
          mess += " - Tên ngày nghỉ lễ không được để trống<br/>";
          resp = false;
        }
        if (this.holiday.start_date == "") {
          mess += " - Ngày bắt đầu không được để trống<br/>";
          resp = false;
        }
        if (this.holiday.end_date == "") {
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
        axios.p("/api/settings/holidays/update",{
          holiday:this.holiday,
          branches:this.branches,
          products:this.products,
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
              this.$router.push('/settings/holidays')
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