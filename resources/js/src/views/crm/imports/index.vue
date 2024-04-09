<!-- =========================================================================================
  File Name: UserList.vue
  Description: User List page
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <v-select :clearable="false" :options="statusOptions" class="w-full" v-model="searchData.status"  />
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <router-link class="btn btn-success" :to="'/crm/imports/add'">
              <vs-button class="mr-3 mb-2" color="success"><i class="fas fa-upload"></i> Import</vs-button>
            </router-link>  
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
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
                    <div class="vs-table-text">Tên File
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Thời gian import
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Người thực hiện
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Thành công
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Lỗi
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Trạng thái
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Kết quả import
                      <!---->
                    </div>
                  </th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in imports" :key="index">
                <!---->
                <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td"><a :href="item.file_link" target="blank">{{ item.file_name }}</a></td>
                <td class="td vs-table--td">{{ item.created_at }}</td>
                <td class="td vs-table--td">{{ item.creator_name }}</td>
                <td class="td vs-table--td">{{ item.count_success }}</td>
                <td class="td vs-table--td">{{ item.count_error }}</td>
                <td class="td vs-table--td">{{ item.status | getStatusName}}</td>
                <td><button class="btn btn-sm btn-info" @click="exportExcel(item)"> <i class="fas fa-download"></i></button></td>
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

  export default {
    components: {
      vSelect
    },
    data() {
      return {
        statusOptions:[
          { label: 'Chọn trạng thái', value: '-1' },
          { label: 'Lỗi', value: '0' },
          { label: 'Hoàn thành', value: '1' },
        ],
        searchData: {
          status: {  label: 'Chọn trạng thái', value: '-1' },
        },

        imports: [],
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
      }
    },
    methods: {
      reset() {
        location.reload();
      },
      getData() {
        this.$vs.loading()
        axios.p('/api/crm/imports/list', {
            status: this.searchData.status.value,
            pagination: this.pagination
          })
          .then((response) => {
            this.roles = response.data.list
            this.$vs.loading.close()
            this.pagination = response.data.paging;
            this.pagination.init = 1;
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
      exportExcel(data) {
        window.open(`/api/export/import/${data.id}`,"_blank")
      },
    },
    created() {
      this.getData();
    },
    filters: {
      getStatusName(value) {
        return value == 1 ? "Hoàn thành" : "Lỗi";
      },
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