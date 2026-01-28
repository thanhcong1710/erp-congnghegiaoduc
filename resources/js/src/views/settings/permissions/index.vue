<template>
  <div id="page-permissions-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap items-center">
        <!-- SEARCH -->
        <div class="flex-grow">
          <div class="vx-row">
            <div class="vx-col md:w-1/3 w-full mb-2">
              <vs-input class="w-full" placeholder="Tìm theo mã hoặc mô tả" v-model="searchData.keyword" @keyup.enter="getData"/>
            </div>
            <div class="vx-col md:w-1/3 w-full mb-2">
              <vue-select
                label="name"
                placeholder="Chọn nhóm"
                :options="groups"
                v-model="selectedGroup"
                :searchable="true"
                @input="saveGroup"
              ></vue-select>
            </div>
            <div class="vx-col md:w-1/3 w-full mb-2">
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
                  <th colspan="1" rowspan="1">Tên Permission</th>
                  <th colspan="1" rowspan="1">Mô tả</th>
                  <th colspan="1" rowspan="1">Nhóm</th>
                  <th colspan="1" rowspan="1" class="text-center">Thứ tự</th>
                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in permissions" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td"><code>{{ item.name }}</code></td>
                <td class="td vs-table--td">{{ item.description }}</td>
                <td class="td vs-table--td">{{ item.group_name }}</td>
                <td class="td vs-table--td text-center">{{ item.display_order }}</td>
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
          <label>Tên Permission <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="form.name" placeholder="Ví dụ: dashboard_01, lms_agreements"/>
          <small class="text-muted">Sử dụng snake_case (chữ thường, gạch dưới)</small>
        </div>

        <div class="vx-col w-full mb-4">
          <label>Mô tả <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="form.description" placeholder="Mô tả ngắn gọn về permission"/>
        </div>

        <div class="vx-col w-full mb-4">
          <label>Nhóm <span class="text-danger">*</span></label>
          <vue-select
            label="name"
            placeholder="Chọn nhóm"
            :options="groups"
            v-model="form.group_obj"
            :searchable="true"
            @input="saveFormGroup"
          ></vue-select>
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
import vSelect from 'vue-select'
import axios from '../../../http/axios.js'

export default {
  components: {
    'vue-select': vSelect
  },
  data() {
    return {
      permissions: [],
      groups: [],
      selectedGroup: null,
      searchData: {
        keyword: '',
        group_id: ''
      },
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: "/api/permissions/list",
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
        title: "THÊM MỚI PERMISSION",
        show: false,
        color: "success",
        mode: 'add'
      },
      form: {
        id: null,
        name: '',
        description: '',
        group_id: '',
        group_obj: null,
        display_order: 0
      }
    }
  },
  created() {
    this.loadGroups()
    this.getData()
  },
  methods: {
    getData() {
      const data = {
        keyword: this.searchData.keyword,
        group_id: this.searchData.group_id,
        pagination: this.pagination
      }
      this.$vs.loading()
      axios.p('/api/permissions/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.permissions = response.data.list
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
    loadGroups() {
      axios.g('/api/permissions/groups')
        .then((response) => {
          this.groups = response.data
        })
    },

    saveGroup(data = null) {
      if (data && typeof data === 'object') {
        this.searchData.group_id = data.id
      } else {
        this.searchData.group_id = ''
      }
    },
    saveFormGroup(data = null) {
      if (data && typeof data === 'object') {
        this.form.group_id = data.id
      } else {
        this.form.group_id = ''
      }
    },
    showAddModal() {
      this.modal.title = "THÊM MỚI PERMISSION"
      this.modal.mode = 'add'
      this.modal.color = "success"
      this.resetForm()
      this.modal.show = true
    },
    showEditModal(item) {
      this.modal.title = "CẬP NHẬT PERMISSION"
      this.modal.mode = 'edit'
      this.modal.color = "primary"
      
      this.form.id = item.id
      this.form.name = item.name
      this.form.description = item.description
      this.form.group_id = item.group_id
      this.form.display_order = item.display_order || 0
      
      // Set group object
      this.form.group_obj = this.groups.find(g => g.id === item.group_id)
      
      this.modal.show = true
    },
    resetForm() {
      this.form = {
        id: null,
        name: '',
        description: '',
        group_id: '',
        group_obj: null,
        display_order: 0
      }
    },
    save() {
      if (!this.form.name) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập tên permission',
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

      if (!this.form.group_id) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng chọn nhóm',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return
      }

      const url = this.modal.mode === 'add' ? '/api/permissions/add' : '/api/permissions/update'
      const data = {
        id: this.form.id,
        name: this.form.name,
        description: this.form.description,
        group_id: this.form.group_id,
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
        text: `Bạn có chắc chắn muốn xóa permission "${item.description}" (${item.name})?`,
        accept: () => this.deletePermission(item),
        acceptText: 'Xóa',
        cancelText: 'Hủy'
      })
    },
    deletePermission(item) {
      this.$vs.loading()
      axios.p('/api/permissions/delete', { id: item.id })
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

<style scoped>
code {
  background: #f4f4f4;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: 'Courier New', monospace;
  color: #e83e8c;
}
</style>
