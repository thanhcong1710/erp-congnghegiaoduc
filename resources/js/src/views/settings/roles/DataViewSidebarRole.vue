<!-- =========================================================================================
  File Name: AddNewDataSidebar.vue
  Description: Add New Data - Sidebar component
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <vs-sidebar click-not-close position-right parent="body" default-index="1" color="primary" class="add-new-data-sidebar items-no-padding" spacer v-model="isSidebarActiveLocal">
    <div class="mt-6 flex items-center justify-between px-6">
        <h4>{{ Object.entries(this.data).length === 0 ? "THÊM MỚI" : "CẬP NHẬT" }} CHỨC VỤ</h4>
        <feather-icon icon="XIcon" @click.stop="isSidebarActiveLocal = false" class="cursor-pointer"></feather-icon>
    </div>
    <vs-divider class="mb-0"></vs-divider>

    <component :is="scrollbarTag" class="scroll-area--data-list-add-new" :settings="settings" :key="$vs.rtl">

      <div class="p-6 pb-3">
        <vs-input label="Chức vụ" type="text" v-model="name" class="mt-2 w-full" name="name" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('name')">{{ errors.first('name') }}</span>
      </div>
      <div class="p-6 pt-0">
        <vs-input label="Mô tả" type="text" v-model="description" class="mt-2 w-full" name="description" v-validate="'required'" />
        <span class="text-danger text-sm" v-show="errors.has('description')">{{ errors.first('description') }}</span>
      </div>
      <div class="flex flex-wrap items-center p-6 pt-4">
        
        <vs-button class="mr-6" @click="submitData" >{{ Object.entries(this.data).length === 0 ? "Thêm mới" : "Cập nhật" }}</vs-button>
        <vs-button type="border" color="danger" @click="isSidebarActiveLocal = false">Hủy</vs-button>
      </div>
    </component>

  </vs-sidebar>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import axios from '../../../http/axios.js'

export default {
  props: {
    isSidebarActive: {
      type: Boolean,
      required: true
    },
    data: {
      type: Object,
      default: () => {}
    },
    context:{}
  },
  components: {
    VuePerfectScrollbar
  },
  data () {
    return {
      name: null,
      description:null,
      dataId: null,
      settings: { // perfectscrollbar settings
        maxScrollbarLength: 60,
        wheelSpeed: .60
      }
    }
  },
  watch: {
    isSidebarActive (val) {
      if (!val) return
      if (Object.entries(this.data).length === 0) {
        this.initValues()
        this.$validator.reset()
      } else {
        const { id, description, name} = JSON.parse(JSON.stringify(this.data))
        this.dataId = id
        this.description = description
        this.name = name
      }
    }
  },
  computed: {
    isSidebarActiveLocal: {
      get () {
        return this.isSidebarActive
      },
      set (val) {
        if (!val) {
          this.$emit('closeSidebar')
        }
      }
    },
    scrollbarTag () { return this.$store.getters.scrollbarTag }
  },
  methods: {
    initValues () {
      if (this.data.id) return
      this.name = null
      this.description = null
      this.dataId = null
    },
    submitData () {
      this.$validator.validateAll().then(result => {
        if (result) {
          const obj = {
            id: this.dataId,
            name: this.name,
            description: this.description
          }
          this.$vs.loading()
          axios.p('/api/roles/create',obj).then((response) => {
            /* Below two lines are just for demo purpose */
            this.$vs.loading.close()
            if(response.data.status==1){
              this.$vs.notify({
                title: 'Thành Công',
                text: response.data.message,
                color: 'success',
                iconPack: 'feather',
                icon: 'icon-check'
              })
              this.$emit('closeSidebar')
              this.initValues()
              this.context.componentParent.getData()
            }else{
              this.$vs.notify({
                title: 'Lỗi',
                text: response.data.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            }
          }).catch((error)   => { console.log(error); this.$vs.loading.close(); })
          
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.add-new-data-sidebar {
  ::v-deep .vs-sidebar--background {
    z-index: 52010;
  }

  ::v-deep .vs-sidebar {
    z-index: 52010;
    width: 400px;
    max-width: 90vw;

    .img-upload {
      margin-top: 2rem;

      .con-img-upload {
        padding: 0;
      }

      .con-input-upload {
        width: 100%;
        margin: 0;
      }
    }
  }
}

.scroll-area--data-list-add-new {
    // height: calc(var(--vh, 1vh) * 100 - 4.3rem);
    height: calc(var(--vh, 1vh) * 100 - 16px - 45px - 82px);

    &:not(.ps) {
      overflow-y: auto;
    }
}
</style>
