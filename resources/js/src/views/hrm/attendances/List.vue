<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Bảng Chấm Công</h4>
        <!-- Trong thực tế nút này sẽ dành cho nhân viên tự chấm công trên app -->
        <div class="flex space-x-2">
          <vs-button class="mr-3 mb-2" color="success"   @click="mockCheckIn()"><i class="fa fa-fingerprint"></i> Giả lập Check-in</vs-button>
        </div>
      </div>
      
      <vs-table :data="attendances" search pagination :max-items="15">
        <template slot="thead">
          <vs-th>Nhân viên</vs-th>
          <vs-th>Ngày</vs-th>
          <vs-th>Giờ vào (Check-in)</vs-th>
          <vs-th>Giờ ra (Check-out)</vs-th>
          <vs-th>IP Check-in</vs-th>
          <vs-th>Thiết bị</vs-th>
          <vs-th>Trạng thái</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>{{ data[indextr].user ? data[indextr].user.name : data[indextr].user_id }}</vs-td>
            <vs-td>{{ data[indextr].date }}</vs-td>
            <vs-td>{{ data[indextr].check_in }}</vs-td>
            <vs-td>{{ data[indextr].check_out || '--:--:--' }}</vs-td>
            <vs-td>{{ data[indextr].check_in_ip || 'N/A' }}</vs-td>
            <vs-td>{{ data[indextr].device_id || 'N/A' }}</vs-td>
            <vs-td>
              <vs-chip :color="getStatusColor(data[indextr].status)">
                {{ getStatusText(data[indextr].status) }}
              </vs-chip>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
export default {
  data() {
    return {
      attendances: []
    }
  },
  created() {
    // Để có dữ liệu demo, có thể gọi API. Hiện tại chưa có controller list riêng cho attendance, 
    // bạn cần bổ sung hàm list trong AttendanceController
  },
  methods: {
    getStatusText(status) {
      const map = { 1: 'Đúng giờ', 2: 'Đi muộn', 3: 'Về sớm', 4: 'Vắng mặt' };
      return map[status] || 'Không xác định';
    },
    getStatusColor(status) {
      const map = { 1: 'success', 2: 'warning', 3: 'warning', 4: 'danger' };
      return map[status] || 'dark';
    },
    mockCheckIn() {
      let form = { user_id: 1, date: new Date().toISOString().split('T')[0], check_in: new Date().toTimeString().split(' ')[0] };
      axios.p('/api/hrm/attendances/check-in', form).then(res => {
         this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
      });
    }
  }
}
</script>
