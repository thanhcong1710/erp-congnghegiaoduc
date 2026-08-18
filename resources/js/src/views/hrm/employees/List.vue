<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Nhân viên</h4>
        <vs-button color="primary" type="filled" icon="add">Thêm nhân viên</vs-button>
      </div>
      
      <vs-table :data="employees">
        <template slot="thead">
          <vs-th>Tên nhân viên</vs-th>
          <vs-th>Phòng ban</vs-th>
          <vs-th>Vị trí</vs-th>
          <vs-th>Ngày vào làm</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].name">{{ data[indextr].name }}</vs-td>
            <vs-td :data="data[indextr].department">{{ data[indextr].department }}</vs-td>
            <vs-td :data="data[indextr].position">{{ data[indextr].position }}</vs-td>
            <vs-td :data="data[indextr].start_date">{{ data[indextr].start_date }}</vs-td>
            <vs-td>
              <vs-button size="small" type="flat" icon="edit" color="warning"></vs-button>
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
      employees: []
    }
  },
  created() {
    this.fetchEmployees();
  },
  methods: {
    fetchEmployees() {
      // Mock data for now since we just created the controller shell
      this.employees = [
        { name: 'Nguyễn Văn A', department: 'IT', position: 'Developer', start_date: '2023-01-01' },
        { name: 'Trần Thị B', department: 'HR', position: 'Manager', start_date: '2022-05-10' },
      ];
      // axios.post('/api/hrm/employees/list').then(res => {
      //   this.employees = res.data;
      // }).catch(err => {
      //   console.error(err);
      // });
    }
  }
}
</script>
