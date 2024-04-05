<template>
  <vs-tabs :position="isSmallerScreen ? 'top' : 'left'" class="tabs-shadow-none" id="profile-tabs" :key="isSmallerScreen">

    <!-- GENERAL -->
    <vs-tab icon-pack="feather" icon="icon-user" :label="!isSmallerScreen ? 'Tổng Quan' : ''">
      <div class="tab-general md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-general />
      </div>
    </vs-tab>
    <vs-tab icon-pack="feather" icon="icon-lock" :label="!isSmallerScreen ? 'Đổi Mật Khẩu' : ''">
      <div class="tab-change-pwd md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-change-password />
      </div>
    </vs-tab>
    <vs-tab icon-pack="feather" icon="icon-info" :label="!isSmallerScreen ? 'Thông Tin' : ''">
      <div class="tab-info md:ml-4 md:mt-0 mt-4 ml-0">
        <user-settings-info />
      </div>
    </vs-tab>
  </vs-tabs>
</template>

<script>
import UserSettingsGeneral from './UserSettingsGeneral.vue'
import UserSettingsChangePassword from './UserSettingsChangePassword.vue'
import UserSettingsInfo from './UserSettingsInfo.vue'

export default {
  components: {
    UserSettingsGeneral,
    UserSettingsChangePassword,
    UserSettingsInfo
  },
  data () {
    return {

    }
  },
  computed: {
    isSmallerScreen () {
      return this.$store.state.windowWidth < 768
    }
  }
}
</script>

<style lang="scss">
#profile-tabs {
  .vs-tabs--content {
    padding: 0;
  }
}
</style>
