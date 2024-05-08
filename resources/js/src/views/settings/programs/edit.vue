

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
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
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Mã chương trình học <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="program.code" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên chương trình học <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="program.name" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <div class="mb-6 md:w-2/3 vx-col w-full">
          <label>Mô tả</label>
          <div class=w-full>
            <textarea class="vs-inputx vs-input--input normal" v-model="program.description"></textarea>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Trạng thái</label>
          <div class=w-full>
            <vs-switch v-model="program.status" color="success"/>
          </div>
        </div>
        
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/settings/programs`">
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
        html:{
          products: {
            item: '',
            list: []
          },
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        program:{
          product_id:'',
          code:'',
          name: '',
          description: '',
          status:1,
        },
      }
    },
    async created() {
      await axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/settings/programs/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.program = response.data
            this.html.products.item = this.html.products.list.filter(item => item.id == response.data.product_id)[0]
          }else{
            this.$router.push({ path: `/settings/programs` });
          }
        })
      },
      selectDate(date){
        if (date) {
          this.program.opened_date = moment(date).format("YYYY-MM-DD");
        }
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.program.product_id = product_id
        }else{
          this.program.product_id = ""
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.program.product_id == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.program.code == "") {
          mess += " - Mã chương trình học không được để trống<br/>";
          resp = false;
        }
        if (this.program.name == "") {
          mess += " - Tên chương trình học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/settings/programs/update",this.program)
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
              this.$router.push('/settings/programs')
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