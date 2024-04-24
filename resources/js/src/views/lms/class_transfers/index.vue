

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Trung tâm</label>
            <multiselect
                name="search_branch"
                placeholder="Chọn trung tâm"
                v-model="searchData.arr_branch"
                :options="branch_list"
                label="name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã tên học sinh, mã hợp đồng" v-model="searchData.keyword"></vs-input>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <router-link class="btn btn-success" :to="'/lms/class_transfers/add'">
              <vs-button class="mr-3 mb-2" color="success"><i class="fa fa-plus"></i> Thêm mới</vs-button>
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
                    <div class="vs-table-text">Trung tâm
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Mã học sinh
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Tên Học sinh
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Lớp đi
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Lớp đến
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Ngày chuyển lớp
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Thao tác
                      <!---->
                    </div>
                  </th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in class_transfers" :key="index">
                <!---->
                
                <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.from_branch_name}}</td>
                <td class="td vs-table--td">{{item.lms_code}}</td>
                <td class="td vs-table--td">{{item.name}}</td>
                <td class="td vs-table--td">{{item.from_class_name}}</td>
                <td class="td vs-table--td">{{item.to_class_name}}</td>
                <td class="td vs-table--td">{{item.transfer_date}}</td>
                <td class="td vs-table--td text-center list-action"> 
                    <router-link :to="`/lms/class_transfers/${item.id}/detail`" >
                      <vs-button size="small"><i class="fa fa-eye"></i></vs-button>
                    </router-link> 
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
  import Multiselect from "vue-multiselect";
  import u from '../../../until/helper.js'

  export default {
    components: { 
      vSelect,
      Multiselect
    },
    data() {
      return {
        branch_list: [],
        searchData: {
          arr_branch: "",
          branch_id:"",
          keyword: "",
        },
        class_transfers: [],
        limitSource: [20, 50, 100, 500],
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
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      this.getData();
    },
    methods: {
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch= ""
        this.searchData.branch_id= ""
        this.searchData.pagination= this.pagination
        this.getData();
      },
      getData() {
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch
       
        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/class_transfers/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.class_transfers = response.data.list
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
    },
    filters: {
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
th .sort-th, th .vs-table-text{
  display: contents;
}
.multiselect{
  z-index: 999;
}
.td.vs-table--td{
  vertical-align: top;
}
</style>