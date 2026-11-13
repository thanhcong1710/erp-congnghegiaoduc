<template>
  <div class="ev-foot">
    <div>
      <template v-if="total > 0">
        Hiển thị <strong>{{ from }}–{{ to }}</strong> trên tổng <strong>{{ total }}</strong> {{ unit }}
      </template>
      <template v-else>Không có {{ unit }}</template>
    </div>
    <div class="ev-foot__right">
      <select class="ev-perpage" :value="perPage" @change="$emit('per-page', Number($event.target.value))">
        <option v-for="n in [10, 20, 50, 100]" :key="n" :value="n">{{ n }} / trang</option>
      </select>
      <vs-pagination
        v-if="lastPage > 1"
        :total="lastPage"
        :max="7"
        :value="page"
        @input="$emit('page', $event)"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'EvalTableFoot',
  props: {
    page: { type: Number, default: 1 },
    perPage: { type: Number, default: 20 },
    total: { type: Number, default: 0 },
    unit: { type: String, default: 'bản ghi' }
  },
  computed: {
    lastPage() { return Math.max(1, Math.ceil(this.total / this.perPage)) },
    from() { return this.total ? (this.page - 1) * this.perPage + 1 : 0 },
    to() { return Math.min(this.total, this.page * this.perPage) }
  }
}
</script>
