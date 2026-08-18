<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Đơn nghỉ phép</h4>
        <vs-button color="success" class="mr-3 mb-2" @click="openPopup()"><i class="fa fa-plus"></i> Tạo đơn mới</vs-button>
      </div>
      
      <vs-table :data="leaves.data" search pagination :max-items="20">
        <template slot="thead">
          <vs-th>Người làm đơn</vs-th>
          <vs-th>Loại nghỉ</vs-th>
          <vs-th>Từ ngày</vs-th>
          <vs-th>Đến ngày</vs-th>
          <vs-th>Lý do</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].user ? data[indextr].user.name : ''">
              <span class="font-medium">{{ data[indextr].user ? data[indextr].user.name : 'Unknown' }}</span>
            </vs-td>
            <vs-td :data="data[indextr].leave_type ? data[indextr].leave_type.name : ''">
              {{ data[indextr].leave_type ? data[indextr].leave_type.name : 'Khác' }}
            </vs-td>
            <vs-td :data="data[indextr].start_date">{{ data[indextr].start_date }}</vs-td>
            <vs-td :data="data[indextr].end_date">{{ data[indextr].end_date }}</vs-td>
            <vs-td :data="data[indextr].reason">{{ data[indextr].reason }}</vs-td>
            <vs-td :data="data[indextr].status">
              <vs-chip :color="data[indextr].status === 1 ? 'success' : (data[indextr].status === 2 ? 'danger' : 'warning')">
                {{ data[indextr].status === 1 ? 'Đã duyệt' : (data[indextr].status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <div class="flex space-x-2">
                <vs-button v-if="data[indextr].status === 0" size="small" color="primary" @click="openApprovePopup(data[indextr])"><i class="fa fa-check"></i> Duyệt/Từ chối</vs-button>
                <vs-button v-if="data[indextr].status === 0" size="small" color="danger" @click="deleteItem(data[indextr].id)"><i class="fa-solid fa-trash"></i></vs-button>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Popup Create Leave Request -->
    <vs-popup title="Tạo Đơn xin nghỉ phép" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Loại nghỉ phép (*)</label>
            <vs-select class="w-full mt-1" v-model="form.leave_type_id">
              <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item,index) in leaveTypes" />
            </vs-select>
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-input type="date" class="w-full" label="Từ ngày (*)" v-model="form.start_date" />
          </div>
          <div class="vx-col w-1/2">
            <vs-input type="date" class="w-full" label="Đến ngày (*)" v-model="form.end_date" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea label="Lý do nghỉ (*)" v-model="form.reason" />
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" class="mr-2"><i class="fa fa-save"></i> Lưu lại</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Popup Approve/Reject -->
    <vs-popup title="Phê duyệt Đơn nghỉ phép" :active.sync="approvePopupActive">
      <div class="p-4">
        <p class="mb-4">Bạn đang thao tác phê duyệt đơn nghỉ phép của <strong>{{ selectedLeave ? selectedLeave.user.name : '' }}</strong></p>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Trạng thái phê duyệt</label>
            <vs-select class="w-full mt-1" v-model="approveForm.status">
              <vs-select-item :value="1" text="Đồng ý duyệt" />
              <vs-select-item :value="2" text="Từ chối" />
            </vs-select>
          </div>
        </div>
        <div class="vx-row mb-4" v-if="approveForm.status == 2">
          <div class="vx-col w-full">
            <vs-textarea label="Lý do từ chối" v-model="approveForm.rejection_reason" />
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="submitApproval()" color="success" class="mr-2"><i class="fa fa-check"></i> Xác nhận</vs-button>
            <vs-button @click="approvePopupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Đóng</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
export default {
  data() {
    return {
      leaves: { data: [] },
      leaveTypes: [],
      popupActive: false,
      approvePopupActive: false,
      selectedLeave: null,
      form: {
        leave_type_id: null,
        start_date: '',
        end_date: '',
        reason: ''
      },
      approveForm: {
        status: 1,
        rejection_reason: ''
      }
    }
  },
  created() {
    this.fetchLeaveTypes();
    this.fetchLeaves();
  },
  methods: {
    fetchLeaveTypes() {
      axios.p('/api/hrm/leave-types/list', { limit: 100 }).then(res => {
        if (res.data.data) {
          this.leaveTypes = res.data.data;
        }
      });
    },
    fetchLeaves() {
      this.$vs.loading();
      axios.p('/api/hrm/leaves/list', { limit: 20 }).then(res => {
        this.leaves = res.data;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
      });
    },
    openPopup() {
      this.form = { leave_type_id: null, start_date: '', end_date: '', reason: '' };
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.leave_type_id || !this.form.start_date || !this.form.end_date || !this.form.reason) {
        this.$vs.notify({title: 'Lỗi', text: 'Vui lòng điền đầy đủ các thông tin bắt buộc', color: 'danger'});
        return;
      }
      axios.p('/api/hrm/leaves/add', this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.popupActive = false;
          this.fetchLeaves();
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message, color: 'danger'});
        }
      });
    },
    openApprovePopup(leave) {
      this.selectedLeave = leave;
      this.approveForm = { status: 1, rejection_reason: '' };
      this.approvePopupActive = true;
    },
    submitApproval() {
      if (this.approveForm.status == 2 && !this.approveForm.rejection_reason) {
        this.$vs.notify({title: 'Lỗi', text: 'Vui lòng nhập lý do từ chối', color: 'danger'});
        return;
      }
      axios.p(`/api/hrm/leaves/approve/${this.selectedLeave.id}`, this.approveForm).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.approvePopupActive = false;
          this.fetchLeaves();
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message, color: 'danger'});
        }
      });
    },
    deleteItem(id) {
      // Stub for delete if needed
      this.$vs.notify({title: 'Thông báo', text: 'Chức năng xoá đang được cập nhật', color: 'warning'});
    }
  }
}
</script>
