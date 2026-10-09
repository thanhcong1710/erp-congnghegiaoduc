<template>
  <div id="page-teaching-assistants-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row mt-3">
          <div class="vx-col w-full flex justify-end">
            <vs-button color="primary" type="filled" @click="openAddModal"><i class="fa fa-plus"></i> Thêm mới trợ giảng</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Mã HRM</th>
                  <th>Tên đầy đủ</th>
                  <th>Ngày sinh</th>
                  <th>SĐT</th>
                  <th>Email</th>
                  <th>Bắt đầu làm việc</th>
                  <th class="text-center">Trạng thái</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in listData" :key="index">
                  <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                  <td class="td vs-table--td font-bold">{{item.hrm_id}}</td>
                  <td class="td vs-table--td">{{item.full_name}}</td>
                  <td class="td vs-table--td">{{item.dob}}</td>
                  <td class="td vs-table--td">{{item.phone}}</td>
                  <td class="td vs-table--td">{{item.email}}</td>
                  <td class="td vs-table--td">{{item.start_date}}</td>
                  <td class="td vs-table--td text-center">
                    <div class="flex justify-center items-center">
                      <vs-chip :color="item.status === 1 ? 'success' : 'danger'">
                        {{ item.status === 1 ? 'Hoạt động' : 'Nghỉ việc' }}
                      </vs-chip>
                    </div>
                  </td>
                  <td class="td vs-table--td text-center list-action"> 
                    <vs-button size="small" color="primary" @click="openEditModal(item)" title="Cập nhật" class="mr-2">
                      <i class="fa fa-edit"></i>
                    </vs-button>
                  </td>
                </tr>
              </tbody>
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
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="pagination.limit=item; getData()" >
              <span>{{item}}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
              style="width: calc(100% - 160px);"
              v-if="Math.ceil(pagination.total / pagination.limit) >1"
              :total="Math.ceil(pagination.total / pagination.limit)"
              :max="7"
              v-model="pagination.cpage" @change="getData()"/>
      </div>
    </vx-card>

    <!-- Modal Thêm Mới -->
    <vs-popup :title="formData.id ? 'Cập nhật thông tin trợ giảng' : 'Thêm mới trợ giảng'" :active.sync="isAddModalActive">
      <div class="vx-row">
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Tên đầy đủ *</label>
          <vs-input class="w-full" v-model="formData.full_name" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Email</label>
          <vs-input class="w-full" type="email" v-model="formData.email" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Ngày sinh</label>
          <vs-input class="w-full" type="date" v-model="formData.dob" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Số điện thoại</label>
          <vs-input class="w-full" v-model="formData.phone" />
        </div>
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Địa chỉ</label>
          <vs-input class="w-full" v-model="formData.address" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Số tài khoản</label>
          <vs-input class="w-full" v-model="formData.bank_account" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Link Facebook</label>
          <vs-input class="w-full" v-model="formData.facebook_link" />
        </div>
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Link LR (Toeic)</label>
          <vs-input class="w-full" v-model="formData.lr_link" />
        </div>
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Link SW</label>
          <vs-input class="w-full" v-model="formData.sw_link" />
        </div>
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Link Profile (CV)</label>
          <vs-input class="w-full" v-model="formData.profile_link" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Ngày bắt đầu làm việc</label>
          <vs-input class="w-full" type="date" v-model="formData.start_date" />
        </div>
        <div class="vx-col sm:w-1/2 w-full mb-4">
          <label class="vs-input--label">Trạng thái</label>
          <vs-select class="w-full" v-model="formData.status">
            <vs-select-item :key="1" value="1" text="Hoạt động" />
            <vs-select-item :key="0" value="0" text="Nghỉ việc" />
          </vs-select>
        </div>
      </div>
      <div class="flex justify-end mt-4">
        <vs-button class="mr-2" @click="saveData" :disabled="!formData.full_name">Lưu lại</vs-button>
        <vs-button color="dark" type="border" @click="isAddModalActive = false">Hủy</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'

export default {
  data() {
    return {
      listData: [],
      isAddModalActive: false,
      formData: {
        full_name: '',
        email: '',
        dob: '',
        phone: '',
        address: '',
        bank_account: '',
        facebook_link: '',
        lr_link: '',
        sw_link: '',
        profile_link: '',
        start_date: '',
        status: 1
      },
      pagination: {
        url: "/api/lms/teaching-assistants/list",
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
        pages: []
      },
      limitSource: [20, 30, 40, 50]
    }
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      const data = {
        pagination: this.pagination
      };
      this.$vs.loading();
      axios.p(this.pagination.url, data)
        .then(response => {
          this.listData = response.data.list;
          this.pagination = response.data.paging;
          this.$vs.loading.close();
        })
        .catch(error => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
    openAddModal() {
      this.formData = {
        full_name: '',
        email: '',
        dob: '',
        phone: '',
        address: '',
        bank_account: '',
        facebook_link: '',
        lr_link: '',
        sw_link: '',
        profile_link: '',
        start_date: '',
        status: 1
      };
      this.isAddModalActive = true;
    },
    openEditModal(item) {
      this.formData = {
        id: item.id,
        full_name: item.full_name,
        email: item.email,
        dob: item.dob,
        phone: item.phone,
        address: item.address,
        bank_account: item.bank_account,
        facebook_link: item.facebook_link,
        lr_link: item.lr_link,
        sw_link: item.sw_link,
        profile_link: item.profile_link,
        start_date: item.start_date,
        status: item.status
      };
      this.isAddModalActive = true;
    },
    saveData() {
      this.$vs.loading();
      const url = this.formData.id ? `/api/lms/teaching-assistants/update/${this.formData.id}` : '/api/lms/teaching-assistants/add';
      axios.p(url, this.formData)
        .then(response => {
          this.$vs.loading.close();
          if (response.data.status == 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.isAddModalActive = false;
            if (!this.formData.id) {
              this.pagination.cpage = 1;
            }
            this.getData();
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        })
        .catch(error => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    },
    toggleStatus(item) {
      const newStatus = item.status === 1 ? 0 : 1;
      this.$vs.dialog({
        type: 'confirm',
        color: 'warning',
        title: 'Xác nhận',
        text: 'Bạn có chắc chắn muốn ' + (newStatus === 1 ? 'mở lại' : 'khóa') + ' tài khoản này?',
        acceptText: 'Đồng ý',
        cancelText: 'Hủy',
        accept: () => {
          this.$vs.loading();
          axios.p(`/api/lms/teaching-assistants/update-status/${item.id}`, { status: newStatus })
            .then(response => {
              this.$vs.loading.close();
              if (response.data.status == 1) {
                this.$vs.notify({
                  title: 'Thành công',
                  text: response.data.message,
                  color: 'success'
                });
                this.getData();
              } else {
                this.$vs.notify({
                  title: 'Lỗi',
                  text: response.data.message,
                  color: 'danger'
                });
              }
            })
            .catch(() => {
              this.$vs.loading.close();
            });
        }
      });
    }
  }
}
</script>
