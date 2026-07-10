<template>
  <div id="hr-recruitment">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Tuyển dụng</h2>
      <p class="text-slate-500">Quản lý chiến dịch, thu thập CV, theo dõi vòng phỏng vấn</p>
    </div>

    <div class="vx-row">
      <!-- Campaign List -->
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card title="Chiến dịch tuyển dụng">
          <ul class="flex flex-col gap-3">
            <li v-for="(camp, idx) in campaigns" :key="idx" 
                class="p-4 border border-solid border-slate-200 rounded cursor-pointer hover:bg-slate-50 transition-colors"
                :class="{'border-primary shadow-sm': activeCampaign === camp.id}"
                @click="activeCampaign = camp.id">
              <div class="flex justify-between items-start mb-2">
                <h6 class="font-bold text-primary">{{ camp.title }}</h6>
                <vs-chip :color="camp.status === 'Đang mở' ? 'success' : 'warning'">{{ camp.status }}</vs-chip>
              </div>
              <p class="text-sm text-slate-500">Hạn chót: {{ camp.deadline }}</p>
              <div class="flex justify-between text-sm mt-3 font-medium">
                <span><feather-icon icon="UsersIcon" svgClasses="w-4 h-4 mr-1"></feather-icon>{{ camp.applicants }} CV</span>
                <span>Chỉ tiêu: {{ camp.target }}</span>
              </div>
            </li>
          </ul>
          <vs-button type="border" class="w-full mt-4" icon-pack="feather" icon="icon-plus">Tạo chiến dịch mới</vs-button>
        </vx-card>
      </div>

      <!-- Applicants Kanban/List -->
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card title="Danh sách ứng viên">
          <vs-table :data="applicants" stripe pagination max-items="10">
            <template slot="thead">
              <vs-th>Ứng viên</vs-th>
              <vs-th>Vị trí ứng tuyển</vs-th>
              <vs-th>Ngày nộp CV</vs-th>
              <vs-th>Vòng hiện tại</vs-th>
              <vs-th>Thao tác</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                <vs-td>
                  <div class="font-bold">{{ tr.name }}</div>
                  <div class="text-sm text-slate-500">{{ tr.phone }}</div>
                </vs-td>
                <vs-td>{{ tr.position }}</vs-td>
                <vs-td>{{ tr.date }}</vs-td>
                <vs-td>
                  <vs-chip :color="getStageColor(tr.stage)">{{ tr.stage }}</vs-chip>
                </vs-td>
                <vs-td>
                  <div class="flex gap-2">
                    <vs-button radius color="primary" type="border" icon-pack="feather" icon="icon-file-text" title="Xem CV"></vs-button>
                    <vs-button radius color="success" type="border" icon-pack="feather" icon="icon-check" title="Chuyển vòng"></vs-button>
                  </div>
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      activeCampaign: 1,
      campaigns: [
        { id: 1, title: 'Tuyển dụng Sales Tháng 8', status: 'Đang mở', deadline: '31/08/2026', applicants: 45, target: 10 },
        { id: 2, title: 'Giáo viên Tiếng Anh K1', status: 'Đang mở', deadline: '15/09/2026', applicants: 12, target: 5 },
        { id: 3, title: 'Senior VueJS Developer', status: 'Tạm dừng', deadline: '01/08/2026', applicants: 8, target: 1 },
      ],
      applicants: [
        { name: 'Hoàng Văn X', phone: '0987654321', position: 'Sales', date: '10/08/2026', stage: 'Mới ứng tuyển' },
        { name: 'Lê Yến Nhi', phone: '0912345678', position: 'Sales', date: '09/08/2026', stage: 'Phỏng vấn vòng 1' },
        { name: 'Trần Văn Z', phone: '0909090909', position: 'Sales', date: '08/08/2026', stage: 'Phỏng vấn vòng 2' },
        { name: 'Nguyễn Thị M', phone: '0933334444', position: 'Sales', date: '07/08/2026', stage: 'Gửi Offer' },
      ]
    }
  },
  methods: {
    getStageColor(stage) {
      const colors = {
        'Mới ứng tuyển': 'primary',
        'Phỏng vấn vòng 1': 'warning',
        'Phỏng vấn vòng 2': 'warning',
        'Gửi Offer': 'success',
        'Từ chối': 'danger'
      }
      return colors[stage] || 'primary'
    }
  }
}
</script>
