<!-- =========================================================================================
    File Name: ForgotPassword.vue
    Description: FOrgot Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div class="h-screen flex w-full bg-img">
        <div class="vx-col w-4/5 sm:w-4/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
            <vx-card>
                <div slot="no-body" class="full-page-bg-color">
                    <div class="vx-row">
                        <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
                            <img src="@assets/images/pages/forgot-password.png" alt="login" class="mx-auto">
                        </div>
                        <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center d-theme-dark-bg">
                            <div class="p-8">
                                <div class="vx-card__title mb-8">
                                    <h4 class="mb-4">Khôi phục mật khẩu</h4>
                                    <p>Vui lòng nhập địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn hướng dẫn về cách đặt lại mật khẩu.</p>
                                </div>

                                <vs-input type="email" label-placeholder="Email" v-model="email" class="w-full mb-8" />
                                <vs-alert :active.sync="alert.show" :color="alert.color" class="mt-4 mb-4"  closable icon-pack="feather" close-icon="icon-x">
                                    {{alert.message}}
                                </vs-alert>
                                <vs-button type="border" to="/pages/login" class="px-4 w-full md:w-auto">Đăng nhập</vs-button>
                                <vs-button class="float-right px-4 w-full md:w-auto mt-3 mb-8 md:mt-0 md:mb-0" @click="forgotPass">Khôi phục mật khẩu</vs-button>
                            </div>
                        </div>
                    </div>
                </div>
            </vx-card>
        </div>
    </div>
</template>

<script>
import axios from './../../http/axios.js'
export default {
  data () {
    return {
      email: '',
      alert: {
        status:'',
        show: false,
        message: '',
        color:'',
      }
    }
  },
  methods: {
    forgotPass(){
        if(this.email){
            this.$vs.loading()
            axios.p('/api/auth/forgot-password', {
                'email' : this.email
            })
            .then((response) => {  
                this.$vs.loading.close()
                this.alert.show=true
                this.alert.status=response.data.status
                this.alert.message=response.data.message  
                this.alert.color = response.data.status ? 'success' : 'danger'
            })
            .catch((error)   => { console.log(error); this.$vs.loading.close(); })
        }
    },
  }
}
</script>
