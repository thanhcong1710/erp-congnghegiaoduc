<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Phòng ban</h4>
        <vs-button color="primary" type="filled" icon="add">Thêm phòng ban</vs-button>
      </div>
      
      <vs-table :data="departments">
        <template slot="thead">
          <vs-th>Tên phòng ban</vs-th>
          <vs-th>Mô tả</vs-th>
          <vs-th>Trưởng phòng</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].name">{{ data[indextr].name }}</vs-td>
            <vs-td :data="data[indextr].description">{{ data[indextr].description }}</vs-td>
            <vs-td :data="data[indextr].manager ? data[indextr].manager.name : ''">
              {{ data[indextr].manager ? data[indextr].manager.name : 'Chưa cập nhật' }}
            </vs-td>
            <vs-td :data="data[indextr].status">
              <vs-chip :color="data[indextr].status === 1 ? 'success' : 'danger'">
                {{ data[indextr].status === 1 ? 'Hoạt động' : 'Tạm dừng' }}
              </vs-chip>
            </vs-td>
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
      departments: []
    }
  },
  created() {
    this.fetchDepartments();
  },
  methods: {
    fetchDepartments() {
      axios.post('/api/hrm/departments/list').then(res => {
        this.departments = res.data;
      }).catch(err => {
        console.error(err);
      });
    }
  }
}
</script>
