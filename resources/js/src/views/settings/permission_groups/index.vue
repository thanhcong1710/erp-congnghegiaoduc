<template>
  <div id="page-permission-groups-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap items-center">
        <!-- SEARCH -->
        <div class="flex-grow">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-2">
              <vs-input class="w-full" placeholder="Tìm theo tên hoặc mô tả" v-model="searchData.keyword" @keyup.enter="getData"/>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-2">
              <vs-button class="mr-3" @click="getData">Tìm kiếm</vs-button>
              <vs-button color="success" @click="showAddModal">Thêm mới</vs-button>
            </div>
          </div>
        </div>
      </div>

      <!-- TABLE -->
      <div class="vs-component vs-con-table stripe vs-table-primary mt-5">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1">Tên nhóm</th>
                  <th colspan="1" rowspan="1">Mô tả</th>
                  <th colspan="1" rowspan="1" class="text-center">Thứ tự</th>
                  <th colspan="1" rowspan="1">Số Permissions</th>
                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in groups" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td"><strong>{{ item.name }}</strong></td>
                <td class="td vs-table--td">{{ item.description }}</td>
                <td class="td vs-table--td text-center">{{ item.display_order }}</td>
                <td class="td vs-table--td">
                  <vs-chip v-if="item.permission_count > 0" color="primary">{{ item.permission_count }}</vs-chip>
                  <span v-else class="text-muted">0</span>
                </td>
                <td class="td vs-table--td text-center">
                  <vs-button size="small" color="primary" type="border" icon-pack="feather" icon="icon-edit" @click="showEditModal(item)"></vs-button>
                  <vs-button size="small" color="danger" type="border" icon-pack="feather" icon="icon-trash" @click="confirmDelete(item)"></vs-button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- PAGINATION -->
      <div class="flex flex-wrap items-center mt-5">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)">
              <span>{{item}}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
          style="width: calc(100% - 160px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage" @change="changePage()"/>
      </div>
    </vx-card>

    <!-- MODAL ADD/EDIT -->
    <vs-popup :class="'modal_' + modal.color" :title="modal.title" :active.sync="modal.show">
      <div class="vx-row">
        <div class="vx-col w-full mb-4">
          <label>Tên nhóm <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="form.name" placeholder="Ví dụ: CRM, LMS, Dashboard"/>
        </div>

        <div class="vx-col w-full mb-4">
          <label>Mô tả <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="form.description" placeholder="Mô tả ngắn gọn về nhóm"/>
        </div>

        <div class="vx-col w-full mb-4">
          <label>Thứ tự hiển thị</label>
          <vs-input class="w-full" type="number" v-model="form.display_order" placeholder="0"/>
          <small class="text-muted">Số càng nhỏ càng hiển thị trước</small>
        </div>
      </div>

      <div class="mt-5">
        <vs-button color="success" @click="save">Lưu</vs-button>
        <vs-button color="rgb(154 167 169)" type="border" @click="modal.show = false">Hủy</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'

export default {
  data() {
    return {
      groups: [],
      searchData: {
        keyword: ''
      },
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: "/api/permission-groups/list",
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
      modal: {
        title: "THÊM MỚI NHÓM PERMISSION",
        show: false,
        color: "success",
        mode: 'add'
      },
      form: {
        id: null,
        name: '',
        description: '',
        display_order: 0
      }
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      const data = {
        keyword: this.searchData.keyword,
        pagination: this.pagination
      }
      this.$vs.loading()
      axios.p('/api/permission-groups/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.groups = response.data.list
          this.pagination = response.data.paging
          setTimeout(() => {
            this.pagination.init = 1
          }, 500)
        })
        .catch((error) => {
          console.log(error)
          this.$vs.loading.close()
        })
    },
    changePage() {
      if (this.pagination.init) {
        this.getData()
      }
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1
      this.pagination.limit = limit
      this.getData()
    },
    showAddModal() {
      this.modal.title = "THÊM MỚI NHÓM PERMISSION"
      this.modal.mode = 'add'
      this.modal.color = "success"
      this.resetForm()
      this.modal.show = true
    },
    showEditModal(item) {
      this.modal.title = "CẬP NHẬT NHÓM PERMISSION"
      this.modal.mode = 'edit'
      this.modal.color = "primary"
      
      this.form.id = item.id
      this.form.name = item.name
      this.form.description = item.description
      this.form.display_order = item.display_order || 0
      
      this.modal.show = true
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        description: '',
        display_order: 0
      }
    },
    save() {
      if (!this.form.name) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập tên nhóm',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return
      }

      if (!this.form.description) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập mô tả',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return
      }

      const url = this.modal.mode === 'add' ? '/api/permission-groups/add' : '/api/permission-groups/update'
      const data = {
        id: this.form.id,
        name: this.form.name,
        description: this.form.description,
        display_order: this.form.display_order
      }

      this.$vs.loading()
      axios.p(url, data)
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Thành công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.modal.show = false
          this.getData()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: error.response?.data?.message || 'Có lỗi xảy ra',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa nhóm "${item.name}"?`,
        accept: () => this.deleteGroup(item),
        acceptText: 'Xóa',
        cancelText: 'Hủy'
      })
    },
    deleteGroup(item) {
      this.$vs.loading()
      axios.p('/api/permission-groups/delete', { id: item.id })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Thành công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.getData()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: error.response?.data?.message || 'Có lỗi xảy ra',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
    }
  }
}
</script>
