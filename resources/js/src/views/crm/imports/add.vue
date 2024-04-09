<!-- =========================================================================================
  File Name: UserList.vue
  Description: User List page
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="row bs-wizard" style="border-bottom:0;">
                
        <div :class="curr_step == 1 ?'col-sm-3 bs-wizard-step active': 'col-sm-3 bs-wizard-step complete'">
          <div class="text-center bs-wizard-stepnum">Chọn tập tin</div>
          <div class="progress"><div class="progress-bar"></div></div>
          <a class="bs-wizard-dot"></a>
          <div class="bs-wizard-info text-center"></div>
        </div>
        
        <div :class="curr_step == 2 ?'col-sm-3 bs-wizard-step active': (curr_step < 2 ? 'col-sm-3 bs-wizard-step disabled':'col-sm-3 bs-wizard-step complete')" ><!-- complete -->
          <div class="text-center bs-wizard-stepnum">Kiểm tra dữ liệu</div>
          <div class="progress"><div class="progress-bar"></div></div>
          <a class="bs-wizard-dot"></a>
          <div class="bs-wizard-info text-center"></div>
        </div>
        
        <div :class="curr_step == 3 ?'col-sm-3 bs-wizard-step active': (curr_step < 3 ? 'col-sm-3 bs-wizard-step disabled':'col-sm-3 bs-wizard-step complete')"><!-- complete -->
          <div class="text-center bs-wizard-stepnum">Phân chia dữ liệu</div>
          <div class="progress"><div class="progress-bar"></div></div>
          <a class="bs-wizard-dot"></a>
          <div class="bs-wizard-info text-center"></div>
        </div>
        
        <div :class="curr_step == 4 ?'col-sm-3 bs-wizard-step active': (curr_step < 4 ? 'col-sm-3 bs-wizard-step disabled':'col-sm-3 bs-wizard-step complete')"><!-- active -->
          <div class="text-center bs-wizard-stepnum">Hoàn thành</div>
          <div class="progress"><div class="progress-bar"></div></div>
          <a class="bs-wizard-dot"></a>
          <div class="bs-wizard-info text-center"></div>
        </div>
      </div>
      <div class="card-body" v-if="curr_step==1">
        <div class="mb-4">
          <p><a href="/static/template/import_khach_hang.xlsx" target="blank"><i class="fas fa-download"></i> Tải danh sách khách hàng mẫu</a></p>
        </div>
        <div style="overflow: hidden;">
           <input type="file" class="vs-inputx vs-input--input normal" id="fileUploadExcel" @change="fileChanged" style="width: calc(100% - 175px); float:left;">
          <vs-button class="mr-3 mb-2" color="success" @click="btnUpload" style=" float:left; margin-left: 10px;" ><i class="fas fa-upload"></i> Import</vs-button>
        </div>
      </div>
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'

  export default {
    components: {
      vSelect
    },
    data() {
      return {
        attached_file:"",
        file_name:"",
        curr_step:1,
        list_data_check:[],
        error_checked:false,
        total_error:0,
        total_validate:0,
        total_open_lock:0,
        list_owner:[],
        list_source:[],
        list_source_detail:[],
        data_assign:{
          owners:"",
          import_id:"",
          source:"",
          source_id:"",
          source_detail:"",
          source_detail_id:"",
          owners_id:"",
          error_message:"",
        },
        result_total_success:0,
        result_total_error:0,
      }
    },
    methods: {
      fileChanged(e) {
        const fileReader = new FileReader();
        const fileName = e.target.value.split("\\").pop();
        this.file_name = fileName
        fileReader.readAsDataURL(e.target.files[0]);
        fileReader.onload = e => {
          this.attached_file = e.target.result;
        };
      },
    btnUpload() {
      if (this.attached_file) {
        this.$vs.loading()
        let dataUpload = {
          files: this.attached_file,
          file_name: this.file_name,
        }
        axios.p(`/api/crm/imports/upload`, dataUpload)
          .then(response => {
            this.$vs.loading.close()
            if(response.data.error){
              alert(response.data.message);
              this.reloadPage();
            }else{
              this.list_data_check = response.data.data
              this.curr_step=2
              this.total_error = response.data.total_error
              this.total_validate = response.data.total_validate
              this.total_open_lock = response.data.total_open_lock
              this.data_assign.import_id = response.data.import_id
            }
          })
          .catch(e => console.log(e))
      }
    },
    },
    created() {
    },
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input{
    width: 360px;
  }
}
/*Form Wizard*/
.bs-wizard {border-bottom: solid 1px #e0e0e0; padding: 0 0 10px 0;}
.bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
.bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
.bs-wizard > .bs-wizard-step .bs-wizard-info {color: #999; font-size: 14px;}
.bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 30px; height: 30px; display: block; background: #fbe8aa; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;} 
.bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 14px; height: 14px; background: #fbbd19; border-radius: 50px; position: absolute; top: 8px; left: 8px; } 
.bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 8px; box-shadow: none; margin: 20px 0;}
.bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #fbe8aa;}
.bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
.bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
.bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
.bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}
.bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
.bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}
.bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 50%;}
.bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
.bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
.fl-right{
  float: right;
  margin-left:10px;
}
.col-sm-3 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
.row {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}
.progress {
    display: flex;
    height: 1rem;
    overflow: hidden;
    font-size: 0.65625rem;
    border-radius: 0.25rem;
    background-color: #ebedef;
}
</style>