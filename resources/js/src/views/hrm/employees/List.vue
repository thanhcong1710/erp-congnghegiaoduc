<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Nhân viên</h4>
        <router-link :to="'/hrm/employees/add'">
          <vs-button class="mr-3 mb-2" color="success"  ><i class="fa fa-plus"></i> Thêm nhân viên</vs-button>
        </router-link>
      </div>
      
      <vs-table :data="employees">
        <template slot="thead">
          <vs-th>Tên nhân viên</vs-th>
          <vs-th>Phòng ban</vs-th>
          <vs-th>Chức danh</vs-th>
          <vs-th>Cấp bậc</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].user ? data[indextr].user.name : ''">{{ data[indextr].user ? data[indextr].user.name : '' }}</vs-td>
            <vs-td :data="data[indextr].department ? data[indextr].department.name : ''">{{ data[indextr].department ? data[indextr].department.name : '' }}</vs-td>
            <vs-td :data="data[indextr].job_title ? data[indextr].job_title.name : ''">{{ data[indextr].job_title ? data[indextr].job_title.name : '' }}</vs-td>
            <vs-td :data="data[indextr].job_level ? data[indextr].job_level.name : ''">{{ data[indextr].job_level ? data[indextr].job_level.name : '' }}</vs-td>
            <vs-td>
              <router-link :to="`/hrm/employees/edit/${data[indextr].id}`">
                <vs-button size="small"   color="success"><i class="fa fa-edit"></i></vs-button>
              </router-link>
              <vs-button size="small"   color="danger"><i class="fa-solid fa-trash"></i></vs-button>
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
      employees: []
    }
  },
  created() {
    this.fetchEmployees();
  },
  methods: {
    fetchEmployees() {
      axios.p('/api/hrm/employees/list').then(res => {
        this.employees = res.data;
      }).catch(err => {
        console.error(err);
      });
    }
  }
}
</script>
