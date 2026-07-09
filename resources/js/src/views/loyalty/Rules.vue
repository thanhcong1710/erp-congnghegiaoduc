<template>
  <div id="loyalty-rules">
    <vx-card>
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold">Quy tắc tích điểm</h4>
        <vs-button type="filled" icon-pack="feather" icon="icon-save">Lưu cấu hình</vs-button>
      </div>

      <div class="vx-row">
        <div class="vx-col w-full md:w-1/2 mb-4" v-for="(rule, index) in rules" :key="index">
          <div class="p-4 border border-solid border-slate-200 rounded-lg flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
              <div class="p-3 rounded-full bg-primary-light text-primary">
                <feather-icon :icon="rule.icon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <h6 class="font-bold mb-1">{{ rule.title }}</h6>
                <p class="text-sm text-slate-500 mb-2">{{ rule.desc }}</p>
                <div class="flex items-center gap-2">
                  <span class="text-sm font-medium">+</span>
                  <vs-input type="number" v-model="rule.points" class="w-20" />
                  <span class="text-sm font-medium">điểm / lần</span>
                </div>
              </div>
            </div>
            <vs-switch v-model="rule.active" />
          </div>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      rules: [
        { title: 'Điểm danh đi học', desc: 'Học sinh đi học đầy đủ và đúng giờ', icon: 'CheckCircleIcon', points: 10, active: true },
        { title: 'Làm bài tập về nhà', desc: 'Hoàn thành bài tập về nhà trên app', icon: 'BookOpenIcon', points: 20, active: true },
        { title: 'Điểm kiểm tra xuất sắc', desc: 'Đạt điểm tối đa trong bài kiểm tra định kỳ', icon: 'AwardIcon', points: 50, active: true },
        { title: 'Giới thiệu bạn bè', desc: 'Giới thiệu thành công 1 học sinh mới', icon: 'UsersIcon', points: 500, active: true },
        { title: 'Đánh giá/Feedback', desc: 'Để lại đánh giá sau khóa học', icon: 'StarIcon', points: 30, active: false },
        { title: 'Sinh nhật', desc: 'Quà tặng nhân dịp sinh nhật học sinh', icon: 'GiftIcon', points: 100, active: true },
      ]
    }
  }
}
</script>

<style scoped>
.bg-primary-light {
  background-color: rgba(var(--vs-primary), 0.1);
}
</style>
