<template>
  <div id="loyalty-member-ranks">
    <vx-card>
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold">Quản lý hạng thành viên</h4>
        <vs-button type="filled" icon-pack="feather" icon="icon-plus">Thêm hạng mới</vs-button>
      </div>

      <vs-table :data="ranks" stripe>
        <template slot="thead">
          <vs-th>Hạng</vs-th>
          <vs-th>Tên hạng</vs-th>
          <vs-th>Điểm yêu cầu</vs-th>
          <vs-th>Quyền lợi</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" :style="{ background: tr.color }">
                {{ tr.icon }}
              </div>
            </vs-td>
            <vs-td>
              <span class="font-bold" :style="{ color: tr.color }">{{ tr.name }}</span>
            </vs-td>
            <vs-td>{{ tr.points }} điểm</vs-td>
            <vs-td>
              <ul class="list-disc pl-4 text-sm text-slate-600">
                <li v-for="(benefit, bIdx) in tr.benefits" :key="bIdx">{{ benefit }}</li>
              </ul>
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
      ranks: [
        { name: 'Đồng', icon: 'B', color: '#cd7f32', points: 0, benefits: ['Tích điểm cơ bản x1'] },
        { name: 'Bạc', icon: 'S', color: '#c0c0c0', points: 1000, benefits: ['Tích điểm x1.2', 'Tặng 100đ sinh nhật'] },
        { name: 'Vàng', icon: 'G', color: '#ffd700', points: 5000, benefits: ['Tích điểm x1.5', 'Ưu tiên đổi quà', 'Voucher giảm 5% học phí'] },
        { name: 'Kim Cương', icon: 'D', color: '#b9f2ff', points: 15000, benefits: ['Tích điểm x2', 'Quà tặng VIP', 'Voucher giảm 10% học phí'] },
      ]
    }
  }
}
</script>
