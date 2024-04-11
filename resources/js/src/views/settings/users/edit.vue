

<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <div class="mb-6">
            <label>Mã Nhân Viên <span class="text-danger"> (*)</span></label>
            <div class=w-full>
              <input type="text" v-model="user_info.hrm_id" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label>Họ tên <span class="text-danger"> (*)</span></label>
            <div class=w-full>
              <input type="text" v-model="user_info.name" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label>Điện thoại <span class="text-danger"> (*)</span></label>
            <div class=w-full>
              <input type="text" v-model="user_info.phone" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label>Email <span class="text-danger"> (*)</span></label>
            <div class=w-full>
              <input type="text" v-model="user_info.email" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label><vs-checkbox v-model="user_info.change_pass" class="mt-1">Đổi mật khẩu </vs-checkbox></label>
            <div class=w-full v-if="user_info.change_pass">
              <input type="text" v-model="user_info.re_password" class="vs-inputx vs-input--input normal" placeholder="Nhập mật khẩu mới">
            </div>
          </div>
          <div class="mb-6">
            <label>Mã Quản Lý</label>
            <div class=w-full>
              <input type="text" v-model="user_info.manager_hrm_id" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label>Đầu số SIP</label>
            <div class=w-full>
              <input type="text" v-model="user_info.sip_id" class="vs-inputx vs-input--input normal">
            </div>
          </div>
          <div class="mb-6">
            <label>Trạng thái</label>
            <div class=w-full>
               <vs-switch v-model="user_info.status" color="success"/>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <div class="mb-6">
            <label><strong>Trung tâm</strong></label>
            <div class=w-full>
              <div v-for="(item, index) in branches" :key="index" class="w-full pl-8">
                <vs-checkbox v-model="item.selected" class="mt-1">{{item.name}}</vs-checkbox>
              </div>
            </div>
          </div>
          <div class="mb-6">
            <label><strong>Phân quyền</strong></label>
            <div class=w-full>
              <div v-for="(item, index) in roles" :key="index" class="w-full pl-8">
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
          <router-link class="btn btn-danger" :to="`/settings/users`">
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
  import helper from '../../../until/helper.js'

  export default {
    components: {
      vSelect,
      helper
    },
    data() {
      return {
        alert:{
          active: false,
          body: '',
          color:'',
        },
        branches:[],
        roles: [],
        user_info:{
          change_pass: false,
          name: '',
          email: '',
          phone:'',
          hrm_id:'',
          sip_id:'',
          manager_hrm_id:'',
          re_password: '12345678@',
          message: '',
          status:1,
        }
      }
    },
    methods: {
      save() {
        let mess = "";
        let resp = true;
        if (this.user_info.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.user_info.email == "") {
          mess += " - Email không được để trống<br/>";
          resp = false;
        }
        if (this.user_info.phone == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.user_info.phone != "" && !helper.vld.phone(this.user_info.phone)) {
          mess += " - Số điện thoại không đúng định dạng<br/>";
          resp = false;
        }
        if (this.user_info.hrm_id == "") {
          mess += " - Mã nhân viên không được để trống<br/>";
          resp = false;
        }
        
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p(`/api/users/update/${this.$route.params.user_id}`,{
          name: this.user_info.name,
          email: this.user_info.email,
          phone:this.user_info.phone,
          change_pass: this.user_info.change_pass,
          password: this.user_info.re_password,
          status: this.user_info.status,
          hrm_id: this.user_info.hrm_id,
          manager_hrm_id : this.user_info.manager_hrm_id,
          roles:this.roles,
          branches:this.branches,
          sip_id: this.user_info.sip_id
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
              this.$router.push('/settings/users')
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
    created() {
      axios.g(`/api/users/${this.$route.params.user_id}`)
      .then(response => {
        this.user_info = response.data.user_info
        this.roles = response.data.roles
        this.branches = response.data.branches
      })
    },
  }
</script>