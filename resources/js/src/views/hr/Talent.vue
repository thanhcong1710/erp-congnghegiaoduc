<template>
  <div id="hr-talent">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Vinh danh Talent</h2>
      <p class="text-slate-500">Bảng vàng vinh danh các nhân viên xuất sắc hoàn thành xuất sắc KPI</p>
    </div>

    <!-- Top 3 Talent -->
    <div class="vx-row mb-base flex justify-center items-end text-center">
      <!-- Top 2 -->
      <div class="vx-col w-full sm:w-1/3 md:w-1/4">
        <vx-card class="talent-card rank-2 transform hover:-translate-y-2 transition-transform">
          <div class="medal-icon text-slate-400 mb-2">
            <feather-icon icon="AwardIcon" svgClasses="w-12 h-12"></feather-icon>
          </div>
          <vs-avatar size="70px" color="primary" text="B" class="mx-auto mb-3 shadow-md" />
          <h4 class="font-bold text-lg">Trần Thị B</h4>
          <p class="text-sm text-slate-500 mb-2">Nhân sự</p>
          <div class="bg-slate-100 py-2 rounded">
            <p class="text-xs font-semibold text-slate-600">ĐIỂM KPI</p>
            <h3 class="text-xl font-bold text-primary">95/100</h3>
          </div>
        </vx-card>
      </div>

      <!-- Top 1 -->
      <div class="vx-col w-full sm:w-1/3 md:w-1/3 px-2 z-10">
        <vx-card class="talent-card rank-1 border-2 border-solid border-warning transform hover:-translate-y-2 transition-transform scale-110 shadow-lg">
          <div class="medal-icon text-warning mb-2 relative">
            <feather-icon icon="AwardIcon" svgClasses="w-16 h-16"></feather-icon>
            <div class="absolute inset-0 flex flex-col items-center justify-center -mt-2">
              <feather-icon icon="StarIcon" svgClasses="w-5 h-5 text-warning fill-current"></feather-icon>
            </div>
          </div>
          <vs-avatar size="90px" color="warning" text="C" class="mx-auto mb-3 shadow-lg ring-4 ring-warning ring-opacity-50" />
          <h4 class="font-bold text-xl text-warning">Lê Văn C</h4>
          <p class="text-sm text-slate-500 mb-3">Kinh doanh (Sales)</p>
          <div class="bg-warning bg-opacity-10 py-3 rounded">
            <p class="text-xs font-semibold text-warning">ĐIỂM KPI TỐI ĐA</p>
            <h3 class="text-2xl font-bold text-warning">98/100</h3>
          </div>
        </vx-card>
      </div>

      <!-- Top 3 -->
      <div class="vx-col w-full sm:w-1/3 md:w-1/4">
        <vx-card class="talent-card rank-3 transform hover:-translate-y-2 transition-transform">
          <div class="medal-icon text-orange-400 mb-2">
            <feather-icon icon="AwardIcon" svgClasses="w-12 h-12"></feather-icon>
          </div>
          <vs-avatar size="70px" color="danger" text="H" class="mx-auto mb-3 shadow-md" />
          <h4 class="font-bold text-lg">Đặng Thu H</h4>
          <p class="text-sm text-slate-500 mb-2">Marketing</p>
          <div class="bg-slate-100 py-2 rounded">
            <p class="text-xs font-semibold text-slate-600">ĐIỂM KPI</p>
            <h3 class="text-xl font-bold text-primary">92/100</h3>
          </div>
        </vx-card>
      </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="vx-row">
      <div class="vx-col w-full">
        <vx-card title="Bảng xếp hạng tổng (Top 10)">
          <div slot="no-body" class="mt-4">
            <vs-table :data="leaderboard" stripe class="w-full">
              <template slot="thead">
                <vs-th>Hạng</vs-th>
                <vs-th>Nhân viên</vs-th>
                <vs-th>Phòng ban</vs-th>
                <vs-th>Dự án / Thành tích nổi bật</vs-th>
                <vs-th>Điểm KPI</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td>
                    <span class="font-bold text-lg flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600">
                      {{ indextr + 4 }}
                    </span>
                  </vs-td>
                  <vs-td>
                    <div class="flex items-center">
                      <vs-avatar :text="tr.name" color="primary" class="mr-3" size="35px" />
                      <span class="font-semibold">{{ tr.name }}</span>
                    </div>
                  </vs-td>
                  <vs-td>{{ tr.department }}</vs-td>
                  <vs-td class="text-sm text-slate-600">{{ tr.achievement }}</vs-td>
                  <vs-td>
                    <span class="font-bold text-primary">{{ tr.score }}</span>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      leaderboard: [
        { name: 'Nguyễn Văn A', department: 'IT & Công nghệ', achievement: 'Triển khai thành công Module Loyalty trước hạn', score: 90 },
        { name: 'Phạm Thị D', department: 'Học thuật', achievement: 'Học sinh đạt điểm cao IELTS 7.0 (15 bạn)', score: 88 },
        { name: 'Trần Khoa E', department: 'Kinh doanh', achievement: 'Vượt chỉ tiêu doanh số 120%', score: 85 },
        { name: 'Lê Hoàng F', department: 'Kế toán', achievement: 'Tối ưu quy trình thanh toán', score: 83 },
        { name: 'Nguyễn Trọng G', department: 'Tuyển sinh', achievement: 'Tổ chức thành công Open Day', score: 82 },
        { name: 'Vũ Thị K', department: 'Nhân sự', achievement: 'Tuyển dụng đủ định biên Quý 3', score: 80 },
        { name: 'Đoàn Hữu L', department: 'IT & Công nghệ', achievement: 'Bảo trì hệ thống không gián đoạn', score: 79 },
      ]
    }
  }
}
</script>

<style scoped>
.talent-card {
  border-radius: 1rem;
  overflow: hidden;
}
.medal-icon {
  display: flex;
  justify-content: center;
}
</style>
