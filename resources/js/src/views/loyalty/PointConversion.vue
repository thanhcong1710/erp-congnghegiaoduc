<template>
  <div id="loyalty-conversion">
    <div class="vx-row">
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Cấu hình quy đổi điểm">
          <div class="mb-4">
            <label class="font-medium text-slate-700 block mb-2">Tỷ lệ quy đổi điểm sang VNĐ</label>
            <div class="flex items-center gap-3">
              <vs-input type="number" v-model="pointVal" class="w-24 text-center" /> 
              <span class="font-bold">Điểm =</span>
              <vs-input type="number" v-model="vndVal" class="w-32 text-center" />
              <span class="font-bold">VNĐ</span>
            </div>
            <p class="text-sm text-slate-500 mt-2">Ví dụ: 1 điểm tương đương 1,000 VNĐ khi giảm trừ học phí.</p>
          </div>

          <div class="mb-5">
            <label class="font-medium text-slate-700 block mb-2">Hạn mức thanh toán bằng điểm tối đa</label>
            <div class="flex items-center gap-2">
              <vs-input type="number" v-model="maxPercent" class="w-24" />
              <span class="font-medium">% tổng hóa đơn</span>
            </div>
          </div>

          <vs-button type="filled" icon-pack="feather" icon="icon-save">Lưu cài đặt</vs-button>
        </vx-card>
      </div>

      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Cấu hình Hết hạn điểm">
          <div class="mb-4">
            <label class="font-medium text-slate-700 block mb-2">Chu kỳ reset điểm</label>
            <vs-select v-model="resetCycle" class="w-full">
              <vs-select-item value="none" text="Không bao giờ hết hạn" />
              <vs-select-item value="year_end" text="Ngày cuối cùng của năm (31/12)" />
              <vs-select-item value="12_months" text="Sau 12 tháng kể từ ngày tích điểm" />
            </vs-select>
          </div>

          <div class="mb-5" v-if="resetCycle !== 'none'">
            <vs-checkbox v-model="notifyBefore">Gửi thông báo trước khi điểm hết hạn (Zalo/SMS)</vs-checkbox>
            <div class="flex items-center gap-2 mt-2" v-if="notifyBefore">
              <span class="text-sm">Trước</span>
              <vs-input type="number" v-model="notifyDays" class="w-20" />
              <span class="text-sm">ngày</span>
            </div>
          </div>

          <vs-button type="filled" icon-pack="feather" icon="icon-save">Lưu cài đặt</vs-button>
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pointVal: 1,
      vndVal: 1000,
      maxPercent: 20,
      resetCycle: 'year_end',
      notifyBefore: true,
      notifyDays: 30
    }
  }
}
</script>
