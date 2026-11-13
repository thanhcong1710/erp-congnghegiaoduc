<template>
  <div class="ev-modnav">
    <div v-for="g in groups" :key="g.label" class="ev-modnav__group">
      <span class="ev-modnav__label">{{ g.label }}</span>
      <router-link
        v-for="l in g.links"
        :key="l.to"
        :to="l.to"
        class="ev-modnav__link"
        :class="{ 'is-active': isActive(l) }"
      >
        <i :class="['feather', l.icon]"></i>{{ l.text }}
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EvalModuleNav',
  data() {
    return {
      groups: [
        {
          label: 'UCREA',
          links: [
            { to: '/lms/evaluations/ucrea', text: 'Đánh giá năng lực', icon: 'icon-star', exact: true },
            { to: '/lms/evaluations/ucrea/tests', text: 'Đề kiểm tra', icon: 'icon-book-open' }
          ]
        },
        {
          label: 'IG.BH',
          links: [
            { to: '/lms/evaluations/igbh/diagnostic', text: 'Khảo sát đầu vào', icon: 'icon-target' },
            { to: '/lms/evaluations/igbh/weekly', text: 'Đánh giá tuần', icon: 'icon-calendar' },
            { to: '/lms/evaluations/igbh/summative', text: 'Tổng kết lớp', icon: 'icon-award', exact: true },
            { to: '/lms/evaluations/igbh/summative/students', text: 'Tổng kết học sinh', icon: 'icon-users' },
            { to: '/lms/evaluations/igbh/tests', text: 'Đề kiểm tra', icon: 'icon-book-open' }
          ]
        }
      ]
    }
  },
  methods: {
    isActive(l) {
      const p = this.$route.path
      return l.exact ? p === l.to : p === l.to || p.indexOf(l.to + '/') === 0
    }
  }
}
</script>
