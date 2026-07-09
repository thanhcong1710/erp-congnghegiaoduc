<template>
  <div id="loyalty-history">
    <vx-card>
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold">Lịch sử tích/tiêu điểm</h4>
        <vs-button type="filled" icon-pack="feather" icon="icon-download">Xuất danh sách</vs-button>
      </div>

      <div class="flex flex-wrap gap-4 mb-4">
        <vs-input icon="icon-search" icon-pack="feather" placeholder="Tìm tên/Mã HS" v-model="search" class="w-full sm:w-auto" />
        <vs-select v-model="filterType" class="w-full sm:w-auto" placeholder="Phân loại">
          <vs-select-item value="all" text="Tất cả" />
          <vs-select-item value="plus" text="Tích điểm" />
          <vs-select-item value="minus" text="Tiêu điểm" />
        </vs-select>
        <vs-input type="date" v-model="dateFrom" class="w-full sm:w-auto" />
        <vs-input type="date" v-model="dateTo" class="w-full sm:w-auto" />
      </div>

      <vs-table :data="historyData" stripe pagination max-items="10">
        <template slot="thead">
          <vs-th>Học sinh</vs-th>
          <vs-th>Mã HS</vs-th>
          <vs-th>Điểm (+/-)</vs-th>
          <vs-th>Phân loại</vs-th>
          <vs-th>Ngày thực hiện</vs-th>
          <vs-th>Mô tả</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <div class="flex items-center">
                <vs-avatar :text="tr.studentName" color="primary" class="mr-2" />
                <span class="font-medium">{{ tr.studentName }}</span>
              </div>
            </vs-td>
            <vs-td>{{ tr.studentCode }}</vs-td>
            <vs-td>
              <span :class="tr.points > 0 ? 'text-success' : 'text-danger'" class="font-bold">
                {{ tr.points > 0 ? '+' : '' }}{{ tr.points }}
              </span>
            </vs-td>
            <vs-td>
              <vs-chip :color="tr.type === 'Tích điểm' ? 'success' : 'warning'">{{ tr.type }}</vs-chip>
            </vs-td>
            <vs-td>{{ tr.date }}</vs-td>
            <vs-td>{{ tr.description }}</vs-td>
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
      filterType: 'all',
      dateFrom: '',
      dateTo: '',
      historyData: [
        { studentName: 'Nguyễn Văn A', studentCode: 'HV001', points: 50, type: 'Tích điểm', date: '01/01/2026 14:30', description: 'Điểm danh buổi học' },
        { studentName: 'Trần Thị B', studentCode: 'HV002', points: -100, type: 'Tiêu điểm', date: '02/01/2026 09:15', description: 'Đổi quà Balo' },
        { studentName: 'Lê Văn C', studentCode: 'HV003', points: 200, type: 'Tích điểm', date: '03/01/2026 16:45', description: 'Giới thiệu bạn bè' },
        { studentName: 'Phạm Thị D', studentCode: 'HV004', points: 10, type: 'Tích điểm', date: '04/01/2026 18:00', description: 'Đánh giá khóa học' },
        { studentName: 'Nguyễn Văn A', studentCode: 'HV001', points: -500, type: 'Tiêu điểm', date: '05/01/2026 10:20', description: 'Trừ điểm thanh toán học phí' }
      ]
    }
  }
}
</script>
