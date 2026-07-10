<template>
  <div id="rate-ratings">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Danh sách Đánh giá 5 Sao</h2>
      <p class="text-slate-500">Quản lý phản hồi và đánh giá từ phụ huynh, học sinh về chất lượng buổi học, giáo viên và dịch vụ.</p>
    </div>

    <vx-card>
      <div class="flex items-center justify-between mb-4 flex-wrap gap-4">
        <div class="flex gap-4 items-center">
          <vs-input icon="icon-search" icon-pack="feather" placeholder="Tìm kiếm tên, lớp..." v-model="search" />
          <vs-select v-model="filterRate" placeholder="Lọc theo sao">
            <vs-select-item value="all" text="Tất cả đánh giá" />
            <vs-select-item value="5" text="5 Sao (Rất tốt)" />
            <vs-select-item value="4" text="4 Sao (Tốt)" />
            <vs-select-item value="123" text="1-3 Sao (Cần chú ý)" />
          </vs-select>
        </div>
        <vs-button type="filled" icon-pack="feather" icon="icon-download">Xuất Excel</vs-button>
      </div>

      <vs-table :data="ratings" stripe pagination max-items="10">
        <template slot="thead">
          <vs-th>Học sinh / Phụ huynh</vs-th>
          <vs-th>Lớp học</vs-th>
          <vs-th>Giáo viên</vs-th>
          <vs-th>Đánh giá (Sao)</vs-th>
          <vs-th>Nội dung nhận xét</vs-th>
          <vs-th>Ngày đánh giá</vs-th>
          <vs-th>Trạng thái xử lý</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <span class="font-bold text-primary">{{ tr.student }}</span>
            </vs-td>
            <vs-td>{{ tr.class }}</vs-td>
            <vs-td>{{ tr.teacher }}</vs-td>
            <vs-td>
              <div class="flex items-center text-warning">
                <feather-icon icon="StarIcon" svgClasses="w-4 h-4 fill-current" v-for="n in tr.stars" :key="'s'+n"></feather-icon>
                <feather-icon icon="StarIcon" svgClasses="w-4 h-4" v-for="n in (5 - tr.stars)" :key="'e'+n"></feather-icon>
              </div>
            </vs-td>
            <vs-td>
              <div class="truncate max-w-xs" :title="tr.comment">{{ tr.comment }}</div>
            </vs-td>
            <vs-td>{{ tr.date }}</vs-td>
            <vs-td>
              <vs-chip :color="tr.status === 'Đã xử lý' ? 'success' : (tr.status === 'Cần gọi lại' ? 'danger' : 'primary')">
                {{ tr.status }}
              </vs-chip>
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
      filterRate: 'all',
      ratings: [
        { student: 'Nguyễn Tiến Minh', class: 'K1 - IELTS Cơ bản', teacher: 'Lê Yến', stars: 5, comment: 'Cô giáo dạy rất nhiệt tình và dễ hiểu. Bé rất thích!', date: '01/08/2026', status: 'Ghi nhận' },
        { student: 'Trần Khoa', class: 'K2 - Giao tiếp', teacher: 'Hoàng Huy', stars: 4, comment: 'Bài tập về nhà hơi nhiều nhưng bài giảng tốt.', date: '02/08/2026', status: 'Ghi nhận' },
        { student: 'Lê Hoàng Anh', class: 'K1 - IELTS Nâng cao', teacher: 'Thanh Trúc', stars: 2, comment: 'Cơ sở vật chất phòng học hôm nay điều hòa bị hỏng, rất nóng.', date: '03/08/2026', status: 'Cần gọi lại' },
        { student: 'Vũ Minh Đức', class: 'Lớp Tự luận', teacher: 'Phạm Trang', stars: 5, comment: 'Dịch vụ chăm sóc khách hàng tuyệt vời.', date: '04/08/2026', status: 'Ghi nhận' },
        { student: 'Phạm Ngọc Trâm', class: 'K3 - Ngữ pháp', teacher: 'Hoàng Huy', stars: 1, comment: 'Cháu phản ánh giáo viên đi trễ 15 phút không báo trước.', date: '04/08/2026', status: 'Đã xử lý' },
      ]
    }
  }
}
</script>
