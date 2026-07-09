<template>
  <div id="loyalty-vouchers">
    <vx-card>
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold">Danh sách Voucher / Quà tặng</h4>
        <vs-button type="filled" icon-pack="feather" icon="icon-plus">Thêm quà tặng mới</vs-button>
      </div>

      <div class="flex flex-wrap gap-4 mb-4">
        <vs-input icon="icon-search" icon-pack="feather" placeholder="Tìm kiếm voucher" v-model="search" class="w-full sm:w-auto" />
        <vs-select v-model="statusFilter" class="w-full sm:w-auto" placeholder="Trạng thái">
          <vs-select-item value="all" text="Tất cả" />
          <vs-select-item value="active" text="Đang phát hành" />
          <vs-select-item value="inactive" text="Đã tạm dừng" />
        </vs-select>
      </div>

      <vs-table :data="vouchers" stripe pagination max-items="10">
        <template slot="thead">
          <vs-th>Hình ảnh</vs-th>
          <vs-th>Tên Voucher / Quà tặng</vs-th>
          <vs-th>Loại</vs-th>
          <vs-th>Điểm cần đổi</vs-th>
          <vs-th>Số lượng còn</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <div class="w-16 h-16 rounded bg-slate-200 flex items-center justify-center overflow-hidden">
                <img v-if="tr.img" :src="tr.img" class="w-full h-full object-cover" />
                <feather-icon v-else icon="ImageIcon" class="text-slate-400"></feather-icon>
              </div>
            </vs-td>
            <vs-td>
              <span class="font-bold">{{ tr.name }}</span>
            </vs-td>
            <vs-td>{{ tr.type }}</vs-td>
            <vs-td><span class="font-bold text-primary">{{ tr.pointsRequired }}</span></vs-td>
            <vs-td>{{ tr.stock }}</vs-td>
            <vs-td>
              <vs-switch v-model="tr.active" />
            </vs-td>
            <vs-td>
              <div class="flex gap-2">
                <vs-button radius color="primary" type="border" icon-pack="feather" icon="icon-edit"></vs-button>
                <vs-button radius color="danger" type="border" icon-pack="feather" icon="icon-trash-2"></vs-button>
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
      statusFilter: 'all',
      vouchers: [
        { img: '', name: 'Voucher giảm 500k học phí', type: 'Giảm giá', pointsRequired: 500, stock: 100, active: true },
        { img: '', name: 'Balo Ant English', type: 'Hiện vật', pointsRequired: 300, stock: 50, active: true },
        { img: '', name: 'Bình nước giữ nhiệt', type: 'Hiện vật', pointsRequired: 150, stock: 200, active: true },
        { img: '', name: 'Voucher xem phim CGV', type: 'Đối tác', pointsRequired: 200, stock: 0, active: false },
        { img: '', name: 'Sổ tay học tập', type: 'Hiện vật', pointsRequired: 50, stock: 500, active: true },
      ]
    }
  }
}
</script>
