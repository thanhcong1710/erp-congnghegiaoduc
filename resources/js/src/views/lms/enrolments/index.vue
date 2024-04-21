<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/4 w-full item-first" style="border-right: 1px solid #ccc;">
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
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
            <div class="vx-col w-full mb-4">
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
            <vs-divider/>
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
        <div class="vx-col md:w-3/4 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin lớp học</h5>
          <div class="vx-row">
            
          </div>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
            <vs-button class="mb-2" color="success" >Cập nhật hợp đồng</vs-button>
          </router-link>
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
  import tree from 'vue-jstree'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      tree,
    },
    data() {
      return {
        classes: [],
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
            product_id: this.enrol.product_id
          })
            .then(response => {
            this.$vs.loading.close();
            this.classes = response.data
          })
        }else{
          this.classes =[]
        }
      },
      selectClass(selected_class) {
      if (selected_class.model.item_type === 'class') {
        // u.log('Program', selected_class.model)
        this.cache.selected_class = selected_class
        this.action.loading = true
        this.cache.class = selected_class.model.item_id
        this.filter.class = this.cache.class
        u.g(`${this.url.class}${this.filter.class}`)
        .then(response => {
            const data = response
            this.cache.class_info = data.class
            this.cache.students = data.students
            data.students.map(std => {
              this.cache.nicks.push(std.student_nick)
              return std
            })
            // u.log('Class Data', this.cache.class_info)
            if (data.class.cm_id) {
              this.html.disable.load_contracts = false
            }
            this.html.disable.save_contracts = true
            this.html.disable.up_semester = false
            this.html.class.display.class_info = 'display'
            this.html.class.display.contracts_list = 'display'
            this.action.loading = false
        }).catch(e => u.log('Exeption', e))
        this.loadSchedule(this.cache.class)
      } else {
        u.log('Parent', selected_class.model)
        this.cache.selected_class = {}
        this.action.loading = false
        this.cache.class = ''
        this.filter.class = ''
        this.cache.class_info = {}
        this.cache.students = []
        this.cache.nicks = []
        this.html.disable.load_contracts = true
        this.html.disable.save_contracts = true
        this.html.disable.up_semester = true
        this.html.class.display.class_info = 'display'
        this.html.class.display.contracts_list = 'display'
      }
    },
    },
  }
</script>