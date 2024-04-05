<template>
  <vx-card no-shadow>
    <vs-input
      class="w-full mt-8"
      v-validate="'required|min:6|max:10'"
      label-placeholder="Old Password"
      v-model="old_password"
      name="old_password"
      data-vv-validate-on="blur"
      data-vv-as="Old Password"
      type="password"
    />
    <span class="text-danger text-sm">{{ errors.first("old_password") }}</span>
    <vs-input
      ref="new_password"
      class="w-full mt-8"
      v-validate="'required|min:6|max:10'"
      label-placeholder="New Password"
      v-model="new_password"
      name="new_password"
      data-vv-validate-on="blur"
      data-vv-as="New Password"
      type="password"
    />
    <span class="text-danger text-sm">{{ errors.first("new_password") }}</span>
    <vs-input
      class="w-full mt-8"
      v-validate="'min:6|max:10|confirmed:new_password'"
      label-placeholder="Confirm Password"
      v-model="confirm_new_password"
      name="confirm_new_password"
      data-vv-validate-on="blur"
      data-vv-as="New Password"
      type="password"
    />
    <span class="text-danger text-sm mt-4 mb-4">{{
      errors.first("confirm_new_password")
    }}</span>
    <vs-alert :color="alert.color" :active.sync="alert.show" class="mt-4 mb-4" closable icon-pack="feather" close-icon="icon-x">
      {{alert.message}} <vs-button class="ml-20" type="border" color="success" v-if="alert.status" @click="logout"
        >Đăng nhập lại</vs-button>
    </vs-alert>
    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end mt-4">
      <vs-button
        class="ml-auto mt-2"
        @click="changePass"
        :disabled="!validateForm"
        >Đổi mật khẩu</vs-button
      >
      <vs-button class="ml-4 mt-2" type="border" color="warning"
        >Hủy</vs-button
      >
    </div>
  </vx-card>
</template>

<script>
import axios from './../../http/axios.js'
export default {
  data() {
    return {
      old_password: "",
      new_password: "",
      confirm_new_password: "",
      alert: {
        status:'',
        show: false,
        message: ''
      }
    };
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.AppActiveUser;
    },
    validateForm() {
      return (
        !this.errors.any() &&
        this.old_password !== "" &&
        this.new_password !== "" &&
        this.confirm_new_password !== ""
      );
    },
  },
  methods: {
    changePass() {
      this.$vs.loading();
      axios.p("/api/user/change-password", {
          old_password: this.old_password,
          new_password: this.new_password,
        })
        .then((response) => {
          this.$vs.loading.close();
          this.alert.show=true
          this.alert.status=response.data.status
          this.alert.message=response.data.message
          this.alert.color = response.data.status ? 'success' : 'danger'
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
    reset() {
      this.old_password = "";
      this.new_password = "";
      this.confirm_new_password = "";
    },
    logout () {
      axios.p('/api/auth/logout',{
        token: localStorage.getItem('accessToken')
      }).then((response) => {})
      // if user is logged in via auth0
      if (this.$auth.profile) this.$auth.logOut()

      // If JWT login
      if (localStorage.getItem('accessToken')) {
        localStorage.removeItem('accessToken')
        this.$router.push('/pages/login').catch(() => {})
      }

      // Change role on logout. Same value as initialRole of acj.js
      this.$acl.change('admin')
      localStorage.removeItem('userInfo')

      // This is just for demo Purpose. If user clicks on logout -> redirect
      this.$router.push('/pages/login').catch(() => {})
    }
  }
};
</script>
