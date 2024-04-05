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
        <div v-if="status==0"
            class="vx-col flex items-center justify-center flex-col sm:w-1/2 md:w-3/5 lg:w-3/4 xl:w-1/2 mx-auto text-center">
            <img src="@assets/images/pages/not-authorized.png" alt="graphic-not-authorized" class="mx-auto mb-4">
            <h3 class="sm:mx-0 mx-4 sm:mb-12 mb-8 text-4xl d-theme-heading-color text-danger" >Kích hoạt tài khoản thất bại!</h3>
            <p class="sm:mx-0 mx-4 sm:mb-12 mb-8 d-theme-text-inverse" >Link kích hoạt không hợp lệ vui lòng thử lại hoặc liên hệ với quản trị viên để được hỗ trợ.</p>
            <a href="/" ><vs-button color="primary" type="filled" style="font-size: 18px;">Trang chủ</vs-button></a>
        </div>
        <div v-else
            class="vx-col flex items-center justify-center flex-col sm:w-1/2 md:w-3/5 lg:w-3/4 xl:w-1/2 mx-auto text-center">
            <img src="@assets/images/pages/not-authorized.png" alt="graphic-not-authorized" class="mx-auto mb-4">
            <h3 class="sm:mx-0 mx-4 sm:mb-12 mb-8 text-4xl d-theme-heading-color text-success" >Kích hoạt tài khoản thành công!</h3>
            <p class="sm:mx-0 mx-4 sm:mb-12 mb-8 d-theme-text-inverse"> Email {{email}} đã được kích hoạt thành công, đăng nhập ngay để sử dụng các tính năng hoàn toàn miễn phí.</p>
            <vs-button size="large" to="/pages/login">Đăng nhập ngay</vs-button>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                email: '',
                status: '',
            }
        },
        created() {
            this.status = this.$route.query.status
            this.email = this.$route.query.email
        },
        methods: {

        },
    };
</script>