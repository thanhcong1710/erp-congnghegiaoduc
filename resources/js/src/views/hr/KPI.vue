<template>
  <div id="hr-kpi">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Đánh giá KPI & OKR</h2>
      <p class="text-slate-500">Thiết lập KPI, thực hiện đánh giá, xếp loại nhân sự định kỳ</p>
    </div>

    <vx-card>
      <vs-tabs>
        <vs-tab label="Chu kỳ hiện tại (Q3-2026)">
          <div class="mt-4">
            <vs-table :data="kpiList" stripe>
              <template slot="thead">
                <vs-th>Nhân viên</vs-th>
                <vs-th>Phòng ban</vs-th>
                <vs-th>Mục tiêu (KPI/OKR)</vs-th>
                <vs-th>Tiến độ</vs-th>
                <vs-th>Điểm tự ĐG</vs-th>
                <vs-th>Điểm QL ĐG</vs-th>
                <vs-th>Xếp loại</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td class="font-bold">{{ tr.name }}</vs-td>
                  <vs-td>{{ tr.department }}</vs-td>
                  <vs-td>
                    <ul class="list-disc pl-4 text-sm">
                      <li v-for="(goal, idx) in tr.goals" :key="idx">{{ goal }}</li>
                    </ul>
                  </vs-td>
                  <vs-td>
                    <vs-progress :percent="tr.progress" :color="tr.progress > 80 ? 'success' : (tr.progress > 50 ? 'warning' : 'danger')"></vs-progress>
                    <span class="text-sm font-medium mt-1 block">{{ tr.progress }}%</span>
                  </vs-td>
                  <vs-td>{{ tr.selfScore }}/100</vs-td>
                  <vs-td>{{ tr.managerScore }}/100</vs-td>
                  <vs-td>
                    <vs-chip :color="getGradeColor(tr.grade)">{{ tr.grade }}</vs-chip>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vs-tab>
        <vs-tab label="Thiết lập tiêu chí">
          <div class="mt-4">
            <div class="flex justify-between items-center mb-4">
              <h4 class="font-bold">Danh sách bộ tiêu chí KPI</h4>
              <vs-button type="filled" icon-pack="feather" icon="icon-plus" @click="showAddForm = !showAddForm">
                {{ showAddForm ? 'Đóng form' : 'Thêm bộ tiêu chí mới' }}
              </vs-button>
            </div>

            <!-- Form Thêm mới -->
            <transition name="fade">
              <div v-if="showAddForm" class="p-4 border border-solid border-slate-200 bg-slate-50 rounded-lg mb-6">
                <h5 class="font-bold mb-4">Tạo bộ tiêu chí mới</h5>
                <div class="vx-row mb-4">
                  <div class="vx-col sm:w-1/2 w-full">
                    <vs-input label="Tên bộ tiêu chí" v-model="newCriteria.name" class="w-full" placeholder="VD: KPI Sale Mức 1" />
                  </div>
                  <div class="vx-col sm:w-1/2 w-full">
                    <vs-select label="Phòng ban áp dụng" v-model="newCriteria.department" class="w-full mt-1">
                      <vs-select-item value="Kinh doanh" text="Kinh doanh (Sales)" />
                      <vs-select-item value="Vận hành" text="Vận hành (Quản lý lớp học)" />
                      <vs-select-item value="Học thuật" text="Học thuật" />
                    </vs-select>
                  </div>
                </div>

                <div class="mb-4">
                  <h6 class="font-semibold mb-2">Danh sách chỉ tiêu (Tổng trọng số: 100%)</h6>
                  <div v-for="(item, idx) in newCriteria.items" :key="idx" class="flex gap-4 items-end mb-3">
                    <vs-input v-model="item.title" class="flex-1" placeholder="Tên chỉ tiêu (VD: Doanh số thực tế)" />
                    <vs-input v-model="item.target" class="w-1/4" placeholder="Mục tiêu (VD: 500tr)" />
                    <div class="flex items-center gap-2">
                      <vs-input type="number" v-model="item.weight" class="w-20" />
                      <span class="font-medium">%</span>
                      <vs-button radius color="danger" type="border" icon-pack="feather" icon="icon-trash-2" @click="removeCriteriaItem(idx)"></vs-button>
                    </div>
                  </div>
                  <vs-button type="border" size="small" icon-pack="feather" icon="icon-plus" @click="addCriteriaItem">Thêm chỉ tiêu</vs-button>
                </div>
                
                <div class="flex justify-end mt-4">
                  <vs-button type="filled" color="success">Lưu bộ tiêu chí</vs-button>
                </div>
              </div>
            </transition>

            <!-- Table -->
            <vs-table :data="kpiTemplates" stripe>
              <template slot="thead">
                <vs-th>Tên bộ tiêu chí</vs-th>
                <vs-th>Phòng ban</vs-th>
                <vs-th>Các chỉ tiêu (Mục tiêu - Trọng số)</vs-th>
                <vs-th>Thao tác</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td class="font-bold text-primary">{{ tr.name }}</vs-td>
                  <vs-td>{{ tr.department }}</vs-td>
                  <vs-td>
                    <ul class="list-disc pl-4 text-sm">
                      <li v-for="(item, idx) in tr.items" :key="idx">
                        <span class="font-medium">{{ item.title }}</span>: {{ item.target }} <span class="text-warning font-bold">({{ item.weight }}%)</span>
                      </li>
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
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      kpiList: [
        { name: 'Lê Văn C', department: 'Kinh doanh', goals: ['Doanh số đạt 500tr', 'Tỉ lệ chốt > 20%'], progress: 85, selfScore: 90, managerScore: 85, grade: 'Xuất sắc' },
        { name: 'Nguyễn Văn A', department: 'IT', goals: ['Hoàn thành Module HR', 'Tối ưu tốc độ tải trang'], progress: 60, selfScore: 70, managerScore: 65, grade: 'Khá' },
        { name: 'Trần Thị B', department: 'Nhân sự', goals: ['Tuyển dụng đủ 10 Sales', 'Tổ chức sự kiện trung thu'], progress: 95, selfScore: 95, managerScore: 90, grade: 'Xuất sắc' },
        { name: 'Phạm Thị D', department: 'Học thuật', goals: ['Duy trì tỉ lệ qua môn > 90%', 'Soạn giáo trình K2'], progress: 40, selfScore: 50, managerScore: 45, grade: 'Cần cố gắng' },
      ],
      showAddForm: false,
      newCriteria: {
        name: '',
        department: 'Kinh doanh',
        items: [
          { title: '', target: '', weight: 0 }
        ]
      },
      kpiTemplates: [
        {
          name: 'KPI Nhân viên Tư vấn (Sale)',
          department: 'Kinh doanh (Sales)',
          items: [
            { title: 'Doanh số cá nhân', target: '500,000,000 VNĐ', weight: 50 },
            { title: 'Tỉ lệ chốt sale (Conversion)', target: '> 20%', weight: 30 },
            { title: 'Số lượng cuộc gọi/ngày', target: '100 calls', weight: 20 }
          ]
        },
        {
          name: 'KPI Quản lý Lớp học / Vận hành',
          department: 'Vận hành (Quản lý lớp học)',
          items: [
            { title: 'Tỉ lệ học sinh tái phí', target: '> 80%', weight: 40 },
            { title: 'Mức độ hài lòng của phụ huynh', target: '> 4.5/5 sao', weight: 30 },
            { title: 'Tỉ lệ học sinh nghỉ học/bảo lưu', target: '< 5%', weight: 30 }
          ]
        }
      ]
    }
  },
  methods: {
    addCriteriaItem() {
      this.newCriteria.items.push({ title: '', target: '', weight: 0 })
    },
    removeCriteriaItem(index) {
      if(this.newCriteria.items.length > 1) {
        this.newCriteria.items.splice(index, 1)
      }
    },
    getGradeColor(grade) {
      if(grade === 'Xuất sắc') return 'success';
      if(grade === 'Khá') return 'primary';
      if(grade === 'Trung bình') return 'warning';
      return 'danger';
    }
  }
}
</script>
