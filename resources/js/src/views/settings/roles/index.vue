

<template>

  <div id="page-roles-list">
    <data-view-sidebar :isSidebarActive="addNewDataSidebar" @closeSidebar="toggleDataSidebar" :context="context" :data="sidebarData" />
    <div class="flex flex-wrap items-center">
      <div
        class="btn-add-new p-3 rounded-lg cursor-pointer flex items-center justify-center text-lg font-medium text-base text-primary border border-solid border-primary"
        @click="addNewData">
        <feather-icon icon="PlusIcon" svgClasses="h-4 w-4" />
        <span class="ml-2 text-base text-primary">Thêm mới</span>
      </div>
      <div class="con-input-search vs-table--search">
        <input type="text" class="input-search vs-table--search-input" style="padding:14px 35px; font-size:14px;" v-model="searchQuery.keyword" @input="getData()">
        <i class="vs-icon notranslate icon-scale material-icons null" style="font-size:24px;">search</i>
      </div>
    </div>
    <vx-card no-shadow class="mt-5">
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">STT
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Chức vụ
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Mô tả
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Thao tác
                      <!---->
                    </div>
                  </th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in roles" :key="index">
                <!---->
                <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.name}}</td>
                <td class="td vs-table--td">{{item.description}}</td>
                <td class="td vs-table--td">
                  <feather-icon icon="EditIcon" svgClasses="h-5 w-5 mr-4 text-success cursor-pointer" @click="editData(item)" />
                  <router-link :to="`/settings/permissions/${item.id}`" ><feather-icon icon="SettingsIcon" class="mr-2" svgClasses="h-5 w-5"></feather-icon></router-link>
                </td>
              </tr>
            </table>
            
          </div>
        </div>
      </div>
      <vs-pagination
            class="mt-3"
            v-if="Math.ceil(pagination.total / pagination.limit) >1"
            :total="Math.ceil(pagination.total / pagination.limit)"
            :max="7"
            v-model="pagination.cpage" @change="changePage()"/>
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'

  import DataViewSidebar from './DataViewSidebarRole.vue'

  export default {
    components: {
      vSelect,
      DataViewSidebar
    },
    data() {
      return {
        context : { componentParent: this },
        searchQuery: {
          keyword: '',
        },

        roles: [],
        limitSource: [10, 20, 30, 40, 50],
        pagination: {
          url: "/api/roles/list",
          id: "",
          style: "line",
          class: "",
          spage: 1,
          ppage: 1,
          npage: 0,
          lpage: 1,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: [],
          init: 0
        },
        context: {
          componentParent: this
        },
        addNewDataSidebar: false,
        sidebarData: {},
        delete_id : ''
      }
    },
    methods: {
      addNewData() {
        this.sidebarData = {}
        this.toggleDataSidebar(true)
      },
      editData(data){
        this.sidebarData = data
        this.toggleDataSidebar(true)
      },
      toggleDataSidebar(val = false) {
        this.addNewDataSidebar = val
      },
      getData() {
        this.$vs.loading()
        axios.p('/api/roles/list', {
            keyword: this.searchQuery.keyword,
            pagination: this.pagination
          })
          .then((response) => {
            this.roles = response.data.list
            this.$vs.loading.close()
            this.pagination = response.data.paging;
            setTimeout(() => {
              this.pagination.init = 1;
            }, 500)
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      changePage() {
        if (this.pagination.init) {
          this.getData();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getData();
      },
    },
    created() {
      this.getData();
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
</style>