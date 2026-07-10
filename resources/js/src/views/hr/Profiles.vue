<template>
  <div id="hr-profiles">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Quản lý Hồ sơ Nhân sự</h2>
      <p class="text-slate-500">Lưu trữ lý lịch, hợp đồng lao động, thông tin bảo hiểm</p>
    </div>

    <vx-card>
      <div class="flex items-center justify-between mb-4">
        <div class="flex gap-4">
          <vs-input icon="icon-search" icon-pack="feather" placeholder="Tìm kiếm nhân sự..." v-model="search" />
          <vs-select v-model="department" placeholder="Phòng ban">
            <vs-select-item value="all" text="Tất cả phòng ban" />
            <vs-select-item value="it" text="IT & Công nghệ" />
            <vs-select-item value="hr" text="Nhân sự" />
            <vs-select-item value="sales" text="Kinh doanh" />
            <vs-select-item value="academic" text="Học thuật" />
          </vs-select>
        </div>
        <vs-button type="filled" icon-pack="feather" icon="icon-user-plus">Thêm nhân sự mới</vs-button>
      </div>

      <vs-table :data="profiles" stripe pagination max-items="10">
        <template slot="thead">
          <vs-th>Nhân viên</vs-th>
          <vs-th>Phòng ban</vs-th>
          <vs-th>Chức vụ</vs-th>
          <vs-th>Loại hợp đồng</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <div class="flex items-center">
                <vs-avatar :text="tr.name" color="primary" class="mr-2" />
                <div>
                  <div class="font-bold">{{ tr.name }}</div>
                  <div class="text-sm text-slate-500">{{ tr.code }}</div>
                </div>
              </div>
            </vs-td>
            <vs-td>{{ tr.department }}</vs-td>
            <vs-td>{{ tr.position }}</vs-td>
            <vs-td>
              <span class="text-sm">{{ tr.contractType }}</span>
            </vs-td>
            <vs-td>
              <vs-chip :color="tr.status === 'Đang làm việc' ? 'success' : 'warning'">{{ tr.status }}</vs-chip>
            </vs-td>
            <vs-td>
              <div class="flex gap-2">
                <vs-button radius color="primary" type="border" icon-pack="feather" icon="icon-eye"></vs-button>
                <vs-button radius color="warning" type="border" icon-pack="feather" icon="icon-edit"></vs-button>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vx-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      department: 'all',
      profiles: [
        { code: 'NV001', name: 'Nguyễn Văn A', department: 'IT & Công nghệ', position: 'Developer', contractType: 'Chính thức', status: 'Đang làm việc' },
        { code: 'NV002', name: 'Trần Thị B', department: 'Nhân sự', position: 'HR Manager', contractType: 'Chính thức', status: 'Đang làm việc' },
        { code: 'NV003', name: 'Lê Văn C', department: 'Kinh doanh', position: 'Sales', contractType: 'Thử việc', status: 'Đang làm việc' },
        { code: 'NV004', name: 'Phạm Thị D', department: 'Học thuật', position: 'Giáo viên', contractType: 'Part-time', status: 'Nghỉ thai sản' },
      ]
    }
  }
}
</script>
