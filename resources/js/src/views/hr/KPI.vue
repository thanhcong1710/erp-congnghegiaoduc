<template>
  <div id="hr-kpi">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Đánh giá KPI & OKR</h2>
      <p class="text-slate-500">Thiết lập KPI, thực hiện đánh giá, xếp loại nhân sự định kỳ</p>
    </div>

    <vx-card>
      <vs-tabs>
        <vs-tab label="Chu kỳ hiện tại (Q3-2026)">
          <div class="mt-4">
            <vs-table :data="kpiList" stripe>
              <template slot="thead">
                <vs-th>Nhân viên</vs-th>
                <vs-th>Phòng ban</vs-th>
                <vs-th>Mục tiêu (KPI/OKR)</vs-th>
                <vs-th>Tiến độ</vs-th>
                <vs-th>Điểm tự ĐG</vs-th>
                <vs-th>Điểm QL ĐG</vs-th>
                <vs-th>Xếp loại</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td class="font-bold">{{ tr.name }}</vs-td>
                  <vs-td>{{ tr.department }}</vs-td>
                  <vs-td>
                    <ul class="list-disc pl-4 text-sm">
                      <li v-for="(goal, idx) in tr.goals" :key="idx">{{ goal }}</li>
                    </ul>
                  </vs-td>
                  <vs-td>
                    <vs-progress :percent="tr.progress" :color="tr.progress > 80 ? 'success' : (tr.progress > 50 ? 'warning' : 'danger')"></vs-progress>
                    <span class="text-sm font-medium mt-1 block">{{ tr.progress }}%</span>
                  </vs-td>
                  <vs-td>{{ tr.selfScore }}/100</vs-td>
                  <vs-td>{{ tr.managerScore }}/100</vs-td>
                  <vs-td>
                    <vs-chip :color="getGradeColor(tr.grade)">{{ tr.grade }}</vs-chip>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vs-tab>
        <vs-tab label="Thiết lập tiêu chí">
          <div class="mt-4 p-4 bg-slate-50 rounded text-center">
            <p>Giao diện cấu hình bộ tiêu chí đánh giá KPI cho từng phòng ban.</p>
            <vs-button type="filled" class="mt-4">Thêm bộ tiêu chí mới</vs-button>
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      kpiList: [
        { name: 'Lê Văn C', department: 'Kinh doanh', goals: ['Doanh số đạt 500tr', 'Tỉ lệ chốt > 20%'], progress: 85, selfScore: 90, managerScore: 85, grade: 'Xuất sắc' },
        { name: 'Nguyễn Văn A', department: 'IT', goals: ['Hoàn thành Module HR', 'Tối ưu tốc độ tải trang'], progress: 60, selfScore: 70, managerScore: 65, grade: 'Khá' },
        { name: 'Trần Thị B', department: 'Nhân sự', goals: ['Tuyển dụng đủ 10 Sales', 'Tổ chức sự kiện trung thu'], progress: 95, selfScore: 95, managerScore: 90, grade: 'Xuất sắc' },
        { name: 'Phạm Thị D', department: 'Học thuật', goals: ['Duy trì tỉ lệ qua môn > 90%', 'Soạn giáo trình K2'], progress: 40, selfScore: 50, managerScore: 45, grade: 'Cần cố gắng' },
      ]
    }
  },
  methods: {
    getGradeColor(grade) {
      if(grade === 'Xuất sắc') return 'success';
      if(grade === 'Khá') return 'primary';
      if(grade === 'Trung bình') return 'warning';
      return 'danger';
    }
  }
}
</script>
