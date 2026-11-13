<template>
  <div class="birthday-input">
    <input
      class="vs-inputx vs-input--input normal bi-field"
      :class="{ 'bi-invalid': state === 'invalid' }"
      type="text"
      inputmode="numeric"
      maxlength="10"
      :value="value"
      :placeholder="placeholder"
      @input="onInput"
      @blur="onBlur"
    >
    <div class="bi-hint" :class="'bi-' + state">
      <template v-if="state === 'empty'">Nhập năm sinh (2019) hoặc ngày đầy đủ (15/03/2019)</template>
      <template v-else-if="state === 'typing'">Đang nhập…</template>
      <template v-else-if="state === 'invalid'">✗ Không hợp lệ – nhập yyyy hoặc dd/mm/yyyy</template>
      <template v-else>
        ✓ {{ parsed.yearOnly ? 'Năm sinh ' + display : display }}
        <span v-if="age !== null"> · {{ age }} tuổi</span>
      </template>
    </div>
  </div>
</template>

<script>
import u from '../until/helper.js'

// Ô nhập ngày sinh tay: gõ "2019" => chỉ năm sinh; gõ "15032019" => tự thành "15/03/2019".
// v-model là chuỗi hiển thị; khi lưu dùng u.parseManualBirthday(value) để lấy { date, yearOnly }.
export default {
  name: 'BirthdayInput',
  props: {
    value: { type: String, default: '' },
    placeholder: { type: String, default: 'yyyy hoặc dd/mm/yyyy' }
  },
  data () {
    return { focused: false }
  },
  computed: {
    parsed () {
      return u.parseManualBirthday(this.value)
    },
    state () {
      const v = (this.value || '').trim()
      if (v === '') return 'empty'
      if (this.parsed) return 'ok'
      // Chưa gõ xong ngày đầy đủ thì chưa báo lỗi
      const digits = v.replace(/\D/g, '')
      if (this.focused && v.indexOf('/') !== -1 && digits.length < 8) return 'typing'
      return 'invalid'
    },
    display () {
      return this.parsed ? u.formatBirthday(this.parsed.date, this.parsed.yearOnly) : ''
    },
    age () {
      return this.parsed ? u.birthdayAge(this.parsed.date) : null
    }
  },
  methods: {
    format (raw) {
      const v = (raw || '').replace(/[-.]/g, '/')
      const digits = v.replace(/\D/g, '').slice(0, 8)
      // Chỉ toàn số và <= 4 chữ số => đang nhập năm sinh, giữ nguyên
      if (v.indexOf('/') === -1 && digits.length <= 4) return digits
      // Người dùng tự gõ dạng d/m/yyyy thì giữ nguyên cách họ gõ
      if (/^\d{1,2}\/\d{1,2}\/\d{0,4}$/.test(v) || /^\d{1,2}\/\d{0,2}$/.test(v)) return v
      let out = digits.slice(0, 2)
      if (digits.length > 2) out += '/' + digits.slice(2, 4)
      if (digits.length > 4) out += '/' + digits.slice(4, 8)
      return out
    },
    onInput (e) {
      this.focused = true
      const formatted = this.format(e.target.value)
      if (formatted !== e.target.value) e.target.value = formatted
      this.$emit('input', formatted)
    },
    onBlur () {
      this.focused = false
      // Chuẩn hoá về dạng hiển thị chuẩn khi rời ô (vd 5/3/2019 => 05/03/2019)
      if (this.parsed && this.display !== this.value) this.$emit('input', this.display)
    }
  }
}
</script>

<style scoped>
.birthday-input .bi-field {
  width: 100%;
  background: #ffffff;
  border: 1.5px solid #cbd5e1;
  font-size: 14px;
}
.birthday-input .bi-field:focus {
  border-color: #7367f0;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15);
}
.birthday-input .bi-field.bi-invalid {
  border-color: #ea5455;
}
.bi-hint {
  margin-top: 4px;
  font-size: 13px;
  line-height: 1.4;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.bi-empty, .bi-typing { color: #94a3b8; }
.bi-ok { color: #28c76f; font-weight: 500; }
.bi-invalid { color: #ea5455; }
</style>
