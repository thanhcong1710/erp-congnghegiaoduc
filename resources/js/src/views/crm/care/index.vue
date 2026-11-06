<template>
  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label class="vs-input--label">Từ khóa</label>
            <vs-input
              class="w-full"
              placeholder="Tên khách hàng, số điện thoại"
              v-model="searchData.keyword"
              @keyup.enter="search"
            ></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label class="vs-input--label">Người phụ trách</label>
            <multiselect
              name="search_creator"
              placeholder="Chọn người phụ trách"
              v-model="searchData.arr_creator"
              :options="care_users_list"
              label="label_name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel=""
              selectLabel=""
              deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label class="vs-input--label">Khoảng thời gian chăm sóc</label>
            <date-picker
              name="item-date"
              v-model="searchData.dateRange"
              range
              format="YYYY-MM-DD"
              style="width: 100%;"
              :clearable="true"
              :lang="datepickerOptions.lang"
              placeholder="Chọn khoảng thời gian chăm sóc"
            ></date-picker>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label class="vs-input--label">Trạng thái cuộc gọi</label>
            <multiselect
              name="search_call_status"
              placeholder="Chọn trạng thái cuộc gọi"
              v-model="searchData.arr_call_status"
              :options="callStatusOptions"
              label="label"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel=""
              selectLabel=""
              deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="search">
              <i class="fa fa-search"></i> Tìm kiếm
            </vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset">
              <i class="fas fa-undo-alt"></i> Hủy
            </vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table" style="min-width: 1400px">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th class="text-center">Thời gian</th>
                  <th>Tên khách hàng</th>
                  <th class="text-center">Số điện thoại</th>
                  <th>Người phụ trách</th>
                  <th class="text-center">Phương thức</th>
                  <th class="text-center">Trạng thái cuộc gọi</th>
                  <th>Nội dung</th>
                  <th class="text-center">Lịch hẹn</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr
                class="tr-values vs-table--tr tr-table-state-null"
                v-for="(item, index) in cares"
                :key="item.id || index"
              >
                <td class="td vs-table--td text-center">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>
                <td class="td vs-table--td text-center">{{ item.care_date }}</td>
                <td class="td vs-table--td">
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`">
                    <a>{{ item.parent_name }}</a>
                  </router-link>
                  <span v-else>{{ item.parent_name }}</span>
                </td>
                <td class="td vs-table--td text-center">
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`">
                    <a>{{ item.parent_phone }}</a>
                  </router-link>
                  <span v-else>{{ item.parent_phone }}</span>
                </td>
                <td class="td vs-table--td">{{ item.creator_name }}</td>
                <td class="td vs-table--td text-center">{{ item.method_name }}</td>
                <td class="td vs-table--td text-center">{{ item.call_status_label }}</td>
                <td class="td vs-table--td" style="max-width: 280px;">
                  <div v-html="item.note"></div>
                  <p v-if="item.attached_file" class="mt-1">
                    <a :href="item.attached_file" target="_blank">File đính kèm</a>
                  </p>
                </td>
                <td class="td vs-table--td text-center">{{ item.next_care_date }}</td>
                <td class="td vs-table--td text-center list-action">
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`">
                    <vs-button size="small"><i class="fa fa-eye"></i></vs-button>
                  </router-link>
                </td>
              </tr>
              <tr v-if="!cares || cares.length === 0">
                <td colspan="10" class="td vs-table--td text-center py-4">
                  Không có dữ liệu chăm sóc
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap items-center mt-5" v-if="pagination.total > 0">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">
              {{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} -
              {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }}
              of {{ pagination.total }}
            </span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item
              v-for="(item, index) in limitSource"
              :key="index"
              @click="changePageLimit(item)"
            >
              <span>{{ item }}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
          style="width: calc(100% - 160px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage()"
        />
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import u from '../../../until/helper.js'

export default {
  components: {
    DatePicker,
    Multiselect
  },
  data() {
    return {
      searchData: {
        keyword: '',
        arr_creator: [],
        creator_id: [],
        arr_call_status: [],
        call_status: [],
        dateRange: ''
      },
      callStatusOptions: u.statusOptionsCare,
      care_users_list: [],
      datepickerOptions: {
        closed: true,
        value: '',
        minDate: '',
        lang: {
          days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
          months: [
            'Tháng 1',
            'Tháng 2',
            'Tháng 3',
            'Tháng 4',
            'Tháng 5',
            'Tháng 6',
            'Tháng 7',
            'Tháng 8',
            'Tháng 9',
            'Tháng 10',
            'Tháng 11',
            'Tháng 12'
          ]
        }
      },
      cares: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: '/api/crm/care/list',
        id: '',
        style: 'line',
        class: '',
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: [],
        init: 0
      }
    }
  },
  created() {
    axios.g('/api/crm/care/users').then((response) => {
      this.care_users_list = response.data
    })
    this.getData()
  },
  methods: {
    search() {
      this.pagination.cpage = 1
      this.getData()
    },
    reset() {
      this.searchData.keyword = ''
      this.searchData.arr_creator = []
      this.searchData.creator_id = []
      this.searchData.arr_call_status = []
      this.searchData.call_status = []
      this.searchData.dateRange = ''
      this.pagination.cpage = 1
      this.getData()
    },
    getData() {
      const startDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange &&
        this.searchData.dateRange[0]
          ? `${u.dateToString(this.searchData.dateRange[0])}`
          : ''
      const endDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange &&
        this.searchData.dateRange[1]
          ? `${u.dateToString(this.searchData.dateRange[1])}`
          : ''

      const ids_creator = []
      if (this.searchData.arr_creator && this.searchData.arr_creator.length) {
        this.searchData.arr_creator.forEach((item) => {
          ids_creator.push(item.id)
        })
      }
      this.searchData.creator_id = ids_creator

      const ids_call_status = []
      if (this.searchData.arr_call_status && this.searchData.arr_call_status.length) {
        this.searchData.arr_call_status.forEach((item) => {
          ids_call_status.push(item.id)
        })
      }
      this.searchData.call_status = ids_call_status

      const data = {
        keyword: this.searchData.keyword,
        creator_id: this.searchData.creator_id,
        call_status: this.searchData.call_status,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination
      }

      this.$vs.loading()
      axios
        .p('/api/crm/care/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.cares = response.data.list
          this.pagination = response.data.paging
          setTimeout(() => {
            this.pagination.init = 1
          }, 500)
        })
        .catch((error) => {
          console.log(error)
          this.$vs.loading.close()
        })
    },
    changePage() {
      if (this.pagination.init) {
        this.getData()
      }
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1
      this.pagination.limit = limit
      this.getData()
    }
  }
}
</script>

<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input {
    width: 360px;
  }
}
th .sort-th,
th .vs-table-text {
  display: contents;
}
.multiselect {
  z-index: 999;
}
</style>

