

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Chương trình học <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <vue-select
                  label="name"
                  placeholder="Chọn chương trình học"
                  :options="html.products.list"
                  v-model="html.products.item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveProduct"
              ></vue-select>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Lộ trình học</label>
          <div class=w-full>
            <vue-select
                  label="label"
                  placeholder="Chọn lộ trình học"
                  :options="html.loTrinh.list"
                  v-model="html.loTrinh.item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveLoTrinh"
              ></vue-select>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Option</label>
          <div class=w-full>
            <vue-select
                  label="label"
                  placeholder="Chọn option"
                  :options="html.option.list"
                  v-model="html.option.item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveOption"
              ></vue-select>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Số buổi trên tuần</label>
          <div class=w-full>
            <vue-select
                  label="label"
                  placeholder="Chọn số buổi trên tuần"
                  :options="html.type.list"
                  v-model="html.type.item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveType"
              ></vue-select>
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Mã khóa học <span class="text-danger"> (*)</span></label>
          <div class=w-full>
            <input type="text" v-model="program.code" class="vs-inputx vs-input--input normal">
          </div>
        </div>
        <div class="mb-6 vx-col md:w-1/3 w-full">
          <label>Tên khóa học <span class="text-danger"> (*)</span></label>
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
          programs: {
            item: '',
            list: []
          },
          loTrinh: {
            item: '',
            list: [
              {'id': 1, 'label' : 'Lộ trình 0 - 5.5'},
              {'id': 2, 'label' : 'Lộ trình 3.0 - 5.5'},
              {'id': 5, 'label' : 'Lộ trình 3.0 - 6.0'},
              {'id': 3, 'label' : 'Lộ trình 4.0 - 5.5'},
              {'id': 6, 'label' : 'Lộ trình 4.0 - 6.0'},
              {'id': 8, 'label' : 'Lộ trình 5.0 - 6.5'},
              {'id': 4, 'label' : 'Lộ trình 5.5 - 6.5'},
              {'id': 7, 'label' : 'Lộ trình 6.0 - 7.0'},
              {'id': 8, 'label' : 'Lộ trình 6.5 - 7.0'},
              {'id': 8, 'label' : 'Lộ trình 6.5 - 7.5'},
              {'id': 8, 'label' : 'Lộ trình 7.0 - 7.5'},
            ]
          },
          option: {
            item: '',
            list: [
              {'id': 1, 'label' : 'Option 1'},
              {'id': 2, 'label' : 'Option 2'},
            ]
          },
          type: {
            item: '',
            list: [
              {'id': 4, 'label' : 'Normal'},
              {'id': 5, 'label' : 'FT5'},
              {'id': 6, 'label' : 'FT6'},
              {'id': 8, 'label' : 'FT8'},
              {'id': 10, 'label' : 'FT10'},
            ]
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
          lo_trinh_id:'',
          option_id:'',
          type:'',
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
      loadProgram(program_id=0){
        axios.g(`/api/system/programs/${this.program.product_id}?is_parent=1`)
          .then(response => {
          this.html.programs.list = response.data
        })
      },
      saveProgram(data = null){
        if (data && typeof data === 'object') {
          const parent_id = data.id
          this.program.parent_id = parent_id
        }else{
          this.program.parent_id = ""
        }
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/settings/programs/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.program = response.data
            this.html.products.item = this.html.products.list.filter(item => item.id == response.data.product_id)[0]
            this.html.loTrinh.item = this.html.loTrinh.list.filter(item => item.id == response.data.lo_trinh_id)[0]
            this.html.option.item = this.html.option.list.filter(item => item.id == response.data.option_id)[0]
            this.html.type.item = this.html.type.list.filter(item => item.id == response.data.type)[0]
            this.loadProgram(this.program.parent_id)
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
        this.loadProgram();
      },
      saveLoTrinh(data = null){
        if (data && typeof data === 'object') {
          const lo_trinh_id = data.id
          this.program.lo_trinh_id = lo_trinh_id
        }else{
          this.program.lo_trinh_id = ""
        }
      },
      saveOption(data = null){
        if (data && typeof data === 'object') {
          const option_id = data.id
          this.program.option_id = option_id
        }else{
          this.program.option_id = ""
        }
      },
      saveType(data = null){
        if (data && typeof data === 'object') {
          const type = data.id
          this.program.type = type
        }else{
          this.program.type = ""
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.program.product_id == "") {
          mess += " - Chương trình học không được để trống<br/>";
          resp = false;
        }
        if (this.program.code == "") {
          mess += " - Mã khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.program.name == "") {
          mess += " - Tên khóa học không được để trống<br/>";
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