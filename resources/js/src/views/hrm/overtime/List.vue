<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Làm thêm giờ (Overtime)</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-plus"></i> Tạo đơn OT</vs-button>
      </div>
      
      <vs-table :data="requests" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Nhân viên</vs-th>
          <vs-th>Ngày làm thêm</vs-th>
          <vs-th>Thời gian</vs-th>
          <vs-th>Lý do</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Người duyệt</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>{{ data[indextr].user ? data[indextr].user.name : data[indextr].user_id }}</vs-td>
            <vs-td>{{ data[indextr].date }}</vs-td>
            <vs-td>{{ data[indextr].start_time }} - {{ data[indextr].end_time }}</vs-td>
            <vs-td>{{ data[indextr].reason }}</vs-td>
            <vs-td>
              <vs-chip :color="data[indextr].status === 0 ? 'warning' : (data[indextr].status === 1 ? 'success' : 'danger')">
                {{ data[indextr].status === 0 ? 'Chờ duyệt' : (data[indextr].status === 1 ? 'Đã duyệt' : 'Từ chối') }}
              </vs-chip>
            </vs-td>
            <vs-td>{{ data[indextr].approver ? data[indextr].approver.name : 'Chưa có' }}</vs-td>
            <vs-td>
              <vs-dropdown vs-trigger-click>
                <vs-button size="small"   color="dark"><i class="fa-solid fa-ellipsis-v"></i></vs-button>
                <vs-dropdown-menu>
                  <vs-dropdown-item @click="updateStatus(data[indextr].id, 1)">Phê duyệt</vs-dropdown-item>
                  <vs-dropdown-item @click="updateStatus(data[indextr].id, 2)">Từ chối</vs-dropdown-item>
                </vs-dropdown-menu>
              </vs-dropdown>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <vs-popup title="Tạo đơn xin Làm thêm giờ (OT)" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="ID Nhân viên (*)" v-model="form.user_id" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input type="date" class="w-full" label="Ngày OT (*)" v-model="form.date" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-input type="time" class="w-full" label="Từ giờ (*)" v-model="form.start_time" />
          </div>
          <div class="vx-col w-1/2">
            <vs-input type="time" class="w-full" label="Đến giờ (*)" v-model="form.end_time" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea label="Lý do làm thêm" v-model="form.reason" />
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="primary" class="mr-2">Gửi đơn</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
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
      requests: [],
      popupActive: false,
      form: { user_id: '', date: '', start_time: '', end_time: '', reason: '' }
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.p('/api/hrm/overtime/list').then(res => this.requests = res.data).catch(err => console.error(err));
    },
    openPopup() {
      this.form = { user_id: '', date: '', start_time: '', end_time: '', reason: '' };
      this.popupActive = true;
    },
    saveData() {
      axios.p('/api/hrm/overtime/add', this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.popupActive = false;
          this.fetchData();
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message, color: 'danger'});
        }
      });
    },
    updateStatus(id, status) {
      axios.p(`/api/hrm/overtime/update-status/${id}`, { status: status }).then(res => {
        this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
        this.fetchData();
      });
    }
  }
}
</script>
