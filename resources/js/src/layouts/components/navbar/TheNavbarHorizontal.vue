<!-- =========================================================================================
  File Name: TheNavbar.vue
  Description: Navbar component
  Component Name: TheNavbar
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
<div class="relative">
  <div class="vx-navbar-wrapper navbar-full p-0">
    <vs-navbar class="navbar-custom navbar-skelton" :class="navbarClasses"  :style="navbarStyle" :color="navbarColor">

      <!-- <bookmarks :navbarColor="navbarColor" v-if="windowWidth >= 992" /> -->

      <router-link tag="div" to="/" class="vx-logo cursor-pointer mx-auto flex items-center">
        <!-- <logo class="w-10 mr-4 fill-current text-primary" />
        <span class="vx-logo-text text-primary">Vuexy</span> -->
        <img src="@assets/images/logo/logo.png" alt="Logo" style="height: 56px;">
      </router-link>
      <h-nav-menu :navMenuItems="navMenuItems" />

      <!-- <i18n /> -->

      <!-- <search-bar /> -->

      <!-- <notification-drop-down /> -->

      <profile-drop-down />

    </vs-navbar>
  </div>
</div>
</template>

<script>
import Bookmarks            from './components/Bookmarks.vue'
import I18n                 from './components/I18n.vue'
import SearchBar            from './components/SearchBar.vue'
import NotificationDropDown from './components/NotificationDropDown.vue'
import ProfileDropDown      from './components/ProfileDropDown.vue'
import Logo                 from '../Logo.vue'
import HNavMenu            from '@/layouts/components/horizontal-nav-menu/HorizontalNavMenuCustom.vue'

export default {
  name: 'the-navbar-horizontal',
  props: {
    logo: { type: String                                                                                                          },
    navbarType: {
      type: String,
      required: true
    },
    navMenuItems:''
  },
  components: {
    Logo,
    Bookmarks,
    I18n,
    SearchBar,
    NotificationDropDown,
    ProfileDropDown,
    HNavMenu
  },
  computed: {
    navbarColor () {
      let color = '#fff'
      if (this.navbarType === 'sticky') color = '#f7f7f7'
      else if (this.navbarType === 'static') {
        if (this.scrollY < 50) {
          color = '#f7f7f7'
        }
      }

      if (this.isThemedark === 'dark') {
        if (color === '#fff') {
          color = '#10163a'
        } else {
          color = '#262c49'
        }
      }

      return color
    },
    isThemedark ()          { return this.$store.state.theme                                                                       },
    navbarStyle ()          { return this.navbarType === 'static' ? {transition: 'all .25s ease-in-out'} : {}                      },
    navbarClasses ()        { return this.scrollY > 5 && this.navbarType === 'static' ? null : 'd-theme-dark-light-bg shadow-none' },
    scrollY ()              { return this.$store.state.scrollY                                                                     },
    verticalNavMenuWidth () { return this.$store.state.verticalNavMenuWidth                                                        },
    windowWidth ()          { return this.$store.state.windowWidth                                                                 }
  }
}

</script>

