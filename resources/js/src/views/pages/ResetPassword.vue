<!-- =========================================================================================
    File Name: ResetPassword.vue
    Description: Reset Password Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div class="h-screen flex w-full bg-img">
        <div class="vx-col sm:w-3/5 md:w-3/5 lg:w-3/4 xl:w-3/5 mx-auto self-center">
            <vx-card>
                <div slot="no-body" class="full-page-bg-color">
                    <div class="vx-row">
                        <div class="vx-col hidden sm:hidden md:hidden lg:block lg:w-1/2 mx-auto self-center">
                            <img src="@assets/images/pages/reset-password.png" alt="login" class="mx-auto">
                        </div>
                        <div class="vx-col sm:w-full md:w-full lg:w-1/2 mx-auto self-center  d-theme-dark-bg">
                            <div class="p-8">
                                <div class="vx-card__title mb-8">
                                    <h4 class="mb-4">Đặt Lại Mật Khẩu</h4>
                                    <p>Vui lòng nhập mật khẩu mới của bạn.</p>
                                </div>
                                <vs-input type="email" label-placeholder="Email" v-model="email" class="w-full mt-6" disabled="true"/>
                                <vs-input type="password" label-placeholder="Mật khẩu" v-model="password" :disabled="success"
                                    ref="password"
                                    data-vv-validate-on="blur"
                                    name="password"
                                    class="w-full mt-6" v-validate="'required|min:6|max:10'" />
                                <span class="text-danger text-sm">{{ errors.first('password') }}</span>
                                <vs-input type="password" label-placeholder="Nhập lại mật khẩu" :disabled="success"
                                    data-vv-validate-on="blur"
                                    name="confirm_password"
                                    v-model="confirm_password" class="w-full mt-6"
                                    v-validate="'min:6|max:10|confirmed:password'" />
                                <span class="text-danger text-sm">{{ errors.first('confirm_password') }}</span>
                                 <vs-alert :active.sync="alert.show" :color="alert.color" class="mt-4 mb-4"  closable icon-pack="feather" close-icon="icon-x">
                                    {{alert.message}}
                                </vs-alert>
                                <div class="flex flex-wrap justify-between flex-col-reverse sm:flex-row mt-8" v-if="!success">
                                    <vs-button type="border" to="/pages/login"
                                        class="w-full sm:w-auto mb-8 sm:mb-auto mt-3 sm:mt-auto">Đăng nhập
                                    </vs-button>
                                    <vs-button class="w-full sm:w-auto" @click="resetPass" :disabled="!validateForm">Đặt lại</vs-button>
                                </div>
                                <vs-button type="filled" to="/pages/login" v-if="success"
                                    class="w-full" color="primary">Đăng nhập ngay
                                </vs-button>

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
        data() {
            return {
                email: '',
                password: '',
                confirm_password: '',
                key_reset: '',
                alert: {
                    status:'',
                    show: false,
                    message: '',
                    color:'',
                },
                success: false
            }
        },
        computed: {
            validateForm () {
                return !this.errors.any() &&  this.password !== '' && this.confirm_password !== ''
            }
        },
        created() {
            this.key_reset = this.$route.query.key_reset
            this.email = this.$route.query.email
        },
        methods: {
            resetPass() {
                if (this.validateForm) {
                    this.$vs.loading()
                    axios.p('/api/auth/reset-password', {
                            'email': this.email,
                            'key_reset': this.key_reset,
                            'password':this.password,
                            'confirm_password':this.confirm_password,
                        })
                        .then((response) => {
                            this.$vs.loading.close()
                            this.alert.show = true
                            this.alert.status = response.data.status
                            this.alert.message = response.data.message
                            this.alert.color = response.data.status ? 'success' : 'danger'
                            if(response.data.status){
                                this.success =true
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                            this.$vs.loading.close();
                        })
                }
            },
        }
    }
</script>