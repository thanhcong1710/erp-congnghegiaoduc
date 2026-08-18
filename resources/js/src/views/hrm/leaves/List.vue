<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Đơn xin nghỉ phép</h4>
        <vs-button color="primary" type="filled" icon="add">Tạo đơn</vs-button>
      </div>
      
      <vs-table :data="leaves">
        <template slot="thead">
          <vs-th>Người làm đơn</vs-th>
          <vs-th>Loại nghỉ</vs-th>
          <vs-th>Từ ngày</vs-th>
          <vs-th>Đến ngày</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].user_name">{{ data[indextr].user_name }}</vs-td>
            <vs-td :data="data[indextr].type">{{ data[indextr].type }}</vs-td>
            <vs-td :data="data[indextr].start_date">{{ data[indextr].start_date }}</vs-td>
            <vs-td :data="data[indextr].end_date">{{ data[indextr].end_date }}</vs-td>
            <vs-td :data="data[indextr].status">
              <vs-chip :color="data[indextr].status === 1 ? 'success' : (data[indextr].status === 2 ? 'danger' : 'warning')">
                {{ data[indextr].status === 1 ? 'Đã duyệt' : (data[indextr].status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <vs-button v-if="data[indextr].status === 0" size="small" type="flat" icon="check" color="success">Duyệt</vs-button>
              <vs-button size="small" type="flat" icon="delete" color="danger"></vs-button>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      leaves: []
    }
  },
  created() {
    this.fetchLeaves();
  },
  methods: {
    fetchLeaves() {
      // Mock data for now
      this.leaves = [
        { user_name: 'Nguyễn Văn A', type: 'Nghỉ ốm', start_date: '2023-10-01', end_date: '2023-10-02', status: 0 },
        { user_name: 'Trần Thị B', type: 'Nghỉ phép năm', start_date: '2023-11-10', end_date: '2023-11-12', status: 1 },
      ];
    }
  }
}
</script>
