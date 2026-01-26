<template>
  <div id="page-zoom-rooms-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Tên lớp, Meeting ID, Topic" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Trung tâm</label>
            <multiselect
              name="search_branch"
              placeholder="Chọn trung tâm"
              v-model="searchData.arr_branch"
              :options="branches"
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
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <router-link class="btn btn-success" :to="'/settings/zoom-rooms/add'">
              <vs-button class="mr-3 mb-2" color="success"><i class="fa fa-plus"></i> Thêm mới</vs-button>
            </router-link>
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1">Trung tâm</th>
                  <th colspan="1" rowspan="1">Lớp học</th>
                  <th colspan="1" rowspan="1">Chủ đề</th>
                  <th colspan="1" rowspan="1">Meeting ID</th>
                  <th colspan="1" rowspan="1">Link tham gia</th>
                  <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in zoomRooms" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.branch_name}}</td>
                <td class="td vs-table--td">
                  <p><strong>{{item.cls_name}}</strong></p>
                  <p>{{item.product_name}}</p>
                  <p>GV: {{item.teacher_name}}</p>
                </td>
                <td class="td vs-table--td">{{item.topic}}</td>
                <td class="td vs-table--td">{{item.meeting_id}}</td>
                <td class="td vs-table--td">
                  <a :href="item.join_url" target="_blank" class="text-primary" v-if="item.join_url">
                    <i class="fa fa-external-link"></i> Tham gia
                  </a>
                </td>
                <td class="td vs-table--td text-center">
                  <vs-chip :color="item.status == 1 ? 'success' : 'danger'">
                    {{item.status == 1 ? 'Hoạt động' : 'Không hoạt động'}}
                  </vs-chip>
                </td>
                <td class="td vs-table--td text-center list-action">
                  <router-link :to="`/settings/zoom-rooms/edit/${item.id}`">
                    <vs-button size="small" color="success"><i class="fa fa-edit"></i></vs-button>
                  </router-link>
                  <vs-button size="small" color="danger" @click="confirmDelete(item)"><i class="fa-solid fa-trash"></i></vs-button>
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
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="pagination.limit=item">
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
import axios from '../../../http/axios.js'
import Multiselect from "vue-multiselect";

export default {
  components: {
    Multiselect
  },
  data() {
    return {
      searchData: {
        keyword: "",
        arr_branch: [],
        branch_id: [],
      },
      branches: [],
      zoomRooms: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        cpage: 1,
        total: 0,
        limit: 20,
        init: 0
      },
      delete_id: '',
    }
  },
  created() {
    axios.g('/api/system/branches').then(response => {
      this.branches = response.data
    })
    this.getData();
  },
  methods: {
    reset() {
      this.searchData.keyword = ""
      this.searchData.arr_branch = []
      this.searchData.branch_id = []
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
        pagination: this.pagination,
      }

      this.$vs.loading()
      axios.p('/api/settings/zoom-rooms/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.zoomRooms = response.data.list
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
    confirmDelete(item) {
      this.delete_id = item.id
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Thông báo',
        text: `Bạn chắc chắn muốn xóa phòng Zoom - ${item.topic}?`,
        accept: this.deleteZoomRoom,
        acceptText: 'Xóa',
        cancelText: 'Hủy'
      })
    },
    deleteZoomRoom() {
      this.$vs.loading();
      axios.p(`/api/settings/zoom-rooms/delete/${this.delete_id}`, {})
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
        .catch((error) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi xóa phòng Zoom',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
    },
  },
}
</script>

<style>
.multiselect {
  z-index: 999;
}
.td.vs-table--td {
  vertical-align: top;
}
</style>
