<template>
  <div id="page-staff-list">
    <div class="flex flex-wrap items-center">
      <vs-button class="p-3" color="success" type="border" @click="addNewData"><i class="fa-solid fa-plus mr-2"></i> Thêm mới Sale</vs-button>
      <div class="con-input-search vs-table--search ml-auto">
        <input type="text" class="input-search vs-table--search-input" style="padding:14px 35px; font-size:14px;" placeholder="Mã, tên nhân viên" v-model="searchQuery.keyword" @input="getData()">
        <i class="vs-icon notranslate icon-scale material-icons null" style="font-size:24px;">search</i>
      </div>
    </div>
    <vx-card no-shadow class="mt-5">
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th width="5%" class="text-center">STT</th>
                  <th width="15%">Mã nhân viên</th>
                  <th width="30%">Họ tên</th>
                  <th width="20%">Điện thoại</th>
                  <th width="20%">Email</th>
                  <th width="10%" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(item, index) in staffList" :key="index">
                  <td class="text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                  <td>{{ item.hrm_id }}</td>
                  <td>{{ item.name }}</td>
                  <td>{{ item.phone }}</td>
                  <td>{{ item.email || '-' }}</td>
                  <td class="text-center list-action">
                    <vs-button size="small" color="success" @click="editData(item)" title="Chỉnh sửa"><i class="fa-solid fa-edit"></i></vs-button>
                  </td>
                </tr>
                <tr v-if="!staffList.length">
                  <td colspan="6" class="text-center p-4">Không có dữ liệu nhân viên cấp dưới</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="flex flex-wrap items-center mt-5">
        <vs-pagination
              v-if="Math.ceil(pagination.total / pagination.limit) > 1"
              :total="Math.ceil(pagination.total / pagination.limit)"
              v-model="pagination.cpage" @change="changePage()"/>
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'
export default {
  data() {
    return {
      searchQuery: { keyword: '' },
      staffList: [],
      pagination: { cpage: 1, limit: 20, total: 0, init: 0 }
    }
  },
  methods: {
    addNewData() { this.$router.push('/crm/staff/add') },
    editData(item) { this.$router.push(`/crm/staff/edit/${item.id}`) },
    getData() {
      this.$vs.loading()
      axios.p('/api/crm/staff/list', {
        keyword: this.searchQuery.keyword,
        pagination: this.pagination
      }).then((response) => {
        this.staffList = response.data.list
        this.pagination = response.data.paging
        this.$vs.loading.close()
        this.pagination.init = 1
      }).catch((e) => { 
        console.log(e);
        this.$vs.loading.close() 
      })
    },
    changePage() { if (this.pagination.init) this.getData() }
  },
  created() { this.getData() }
}
</script>

<style scoped>
@media only screen and (min-width: 600px) {
  .vs-table--search {
    max-width: 300px;
  }
}
</style>
