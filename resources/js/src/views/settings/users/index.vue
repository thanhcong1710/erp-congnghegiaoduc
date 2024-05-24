

<template>

  <div id="page-users-list">
    <div class="flex flex-wrap items-center">
      <vs-button class="p-3" color="success" type="border" @click="addNewData"><i class="fa-solid fa-plus"></i> Thêm mới</vs-button>
      <vs-button class="ml-3 p-3" color="danger" type="border" @click="confirmDelete(0)"><i class="fa-solid fa-right-from-bracket"></i> Xóa hết Token</vs-button>
      <div class="con-input-search vs-table--search">
        <input type="text" class="input-search vs-table--search-input" style="padding:14px 35px; font-size:14px;" placeholder="Mã, tên nhân viên" v-model="searchQuery.keyword" @input="getData()">
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
                  <th colspan="1" rowspan="1" width="5%" class="text-center">STT</th>
                  <th colspan="1" rowspan="1" width="20%">Trung tâm</th>
                  <th colspan="1" rowspan="1" width="20%">Nhân viên</th>
                  <th colspan="1" rowspan="1" width="20%">Vai trò</th>
                  <th colspan="1" rowspan="1" width="10%">Trạng thái</th>
                  <th colspan="1" rowspan="1" width="10%" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in users" :key="index">
                <!---->
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.branch_name}}</td>
                <td class="td vs-table--td">{{item.hrm_id}} - {{item.name}}</td>
                <td class="td vs-table--td">{{item.role_name}}</td>
                <td>
                  <vs-chip :color="item.status | getStatusColor" class="product-order-status">{{ item.status | getStatusText }}</vs-chip>
                </td>
                <td class="td vs-table--td text-center list-action">
                  <vs-button size="small" color="success"  @click="editData(item)"><i class="fa-solid fa-edit"></i></vs-button>
                  <vs-button size="small" color="danger"  @click="confirmDelete(1, item)"><i class="fa-solid fa-right-from-bracket"></i></vs-button>
                </td>
              </tr>
            </table>
            
          </div>
        </div>
      </div>
      <div class="flex flex-wrap items-center mt-5">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="pagination.limit=item" >
              <span>{{item}}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
              style="width: calc(100% - 160px);"
              v-if="Math.ceil(pagination.total / pagination.limit) >1"
              :total="Math.ceil(pagination.total / pagination.limit)"
              :max="7"
              v-model="pagination.cpage" @change="changePage()"/>
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
        context : { componentParent: this },
        searchQuery: {
          keyword: '',
        },

        users: [],
        limitSource: [10, 20, 30, 40, 50],
        pagination: {
          url: "/api/users/list",
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
      confirmDelete (type, item = null) {
        this.delete_id = type ? item.id : 0
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: type ? `Bạn chắc chắn muốn xóa token của - ${item.name}?` : `Bạn chắc chắn muốn xóa hết token`,
          accept: this.deletetuition_fee,
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      deletetuition_fee(){
        const data = {
          user_id: this.delete_id,
        };
        this.$vs.loading();
        axios.p(`/api/revoke-token`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.getData();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
      addNewData() {
        this.$router.push('/settings/users/add')
      },
      editData(data){
        this.$router.push(`/settings/users/edit/${data.id}`)
      },
      getData() {
        this.$vs.loading()
        axios.p('/api/users/list', {
            keyword: this.searchQuery.keyword,
            pagination: this.pagination
          })
          .then((response) => {
            this.users = response.data.list
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
  #page-users-list .vs-table--search {
    max-width: 360px;
  }
  #page-users-list .vs-table--search-input{
    width: 360px;
  }
}
</style>