<template>
  <div id="lms-test-catalog">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Danh Mục Bài Kiểm Tra (LMS Tests)</h4>
          <p class="text-sm text-gray-500">Danh mục 59+ đề thi, rubric và bài khảo sát năng lực UCREA & IG.BH</p>
        </div>
        <div class="flex border rounded overflow-hidden">
          <button
            class="px-4 py-2 text-sm font-medium"
            :class="selectedType === '' ? 'bg-primary text-white' : 'bg-white text-gray-700'"
            @click="setType('')"
          >
            Tất cả
          </button>
          <button
            class="px-4 py-2 text-sm font-medium"
            :class="selectedType === 'UCREA' ? 'bg-primary text-white' : 'bg-white text-gray-700'"
            @click="setType('UCREA')"
          >
            UCREA
          </button>
          <button
            class="px-4 py-2 text-sm font-medium"
            :class="selectedType === 'IG.BH' ? 'bg-primary text-white' : 'bg-white text-gray-700'"
            @click="setType('IG.BH')"
          >
            IG.BH
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="vx-row mb-4">
        <div class="vx-col sm:w-1/2 w-full mb-2">
          <vs-input
            class="w-full"
            placeholder="Tìm theo tên bài kiểm tra, mã test, level..."
            v-model="search"
            @keyup.enter="fetchData"
            icon="search"
          />
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2 flex items-end">
          <vs-button class="mr-2" @click="fetchData">Tìm kiếm</vs-button>
          <vs-button color="dark" type="border" @click="resetSearch">Đặt lại</vs-button>
        </div>
      </div>

      <!-- Table -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center w-12">STT</th>
                  <th>Tên bài kiểm tra</th>
                  <th>Hệ thống</th>
                  <th>Mã bài test</th>
                  <th>Level</th>
                  <th class="text-center">Test Seq</th>
                  <th class="text-center">Tài liệu</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center py-6 text-gray-500">Đang tải danh mục bài test...</td>
                </tr>
                <tr v-else-if="tests.length === 0">
                  <td colspan="7" class="text-center py-6 text-gray-500">Không tìm thấy bài test nào</td>
                </tr>
                <tr v-for="(item, index) in tests" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center text-gray-500">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td><strong>{{ item.name }}</strong></td>
                  <td>
                    <vs-chip :color="item.test_type === 'UCREA' ? 'success' : 'primary'">{{ item.test_type }}</vs-chip>
                  </td>
                  <td><code>{{ item.test_cd || '-' }}</code></td>
                  <td><vs-chip color="dark">{{ item.level_cd || '-' }}</vs-chip></td>
                  <td class="text-center font-bold text-gray-700">{{ item.test_seq }}</td>
                  <td class="text-center">
                    <a v-if="item.pdf_url" :href="item.pdf_url" target="_blank" class="text-primary" title="Xem đề PDF">
                      <i class="feather icon-file-text text-lg"></i>
                    </a>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap items-center justify-between mt-5">
        <div class="text-sm text-gray-500">
          Tổng số: <strong>{{ pagination.total }}</strong> bài kiểm tra
        </div>
        <vs-pagination
          v-if="Math.ceil(pagination.total / pagination.per_page) > 1"
          :total="Math.ceil(pagination.total / pagination.per_page)"
          :max="7"
          v-model="pagination.current_page"
          @change="fetchData"
        />
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  data() {
    return {
      selectedType: '',
      search: '',
      loading: false,
      tests: [],
      pagination: {
        current_page: 1,
        per_page: 20,
        total: 0
      }
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    setType(type) {
      this.selectedType = type
      this.pagination.current_page = 1
      this.fetchData()
    },
    fetchData() {
      this.loading = true
      const params = {
        type: this.selectedType,
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page
      }
      axios.g('/api/lms/evaluations/tests', params).then(res => {
        this.loading = false
        if (res && res.data && res.data.data) {
          this.tests = res.data.data
          if (res.data.pagination) {
            this.pagination.total = res.data.pagination.total
            this.pagination.current_page = res.data.pagination.current_page
          }
        }
      }).catch(err => {
        this.loading = false
      })
    },
    resetSearch() {
      this.search = ''
      this.pagination.current_page = 1
      this.fetchData()
    }
  }
}
</script>

