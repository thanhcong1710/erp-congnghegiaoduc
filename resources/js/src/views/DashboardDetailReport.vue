<template>
  <div id="dashboard-detail-report" class="p-4">
    
    <!-- Breadcrumb -->
    <div class="mb-4">
       <vs-breadcrumb :items="[
         {title: 'Dashboard', url: '/admin/dashboard'}, 
         {title: 'Báo cáo chi tiết', active: false},
         {title: filterParam || 'Tất cả', active: true}
       ]"></vs-breadcrumb>
    </div>

    <!-- Native Vuexy Card -->
    <div class="bg-white rounded-lg shadow-md mb-base p-6">
      
      <!-- No Slot Actions Here Anymore -->

      <!-- Compact Inline Stats Bar -->
      <div class="mb-6 mt-0">
        <div class="flex flex-col lg:flex-row items-center justify-between p-4 rounded-lg border border-solid border-gray-200" 
             :class="'border-l-4 border-' + accentColor"
             :style="accentColor ? 'background-color: rgba(var(--vs-' + accentColor + '), 0.15)' : 'background-color: #f8f9fa'">
          
          <!-- Left: Title, Icon & Total Value -->
          <div class="flex items-center flex-1 w-full lg:w-auto mb-4 lg:mb-0">
            <div class="p-2 rounded-lg mr-4 flex items-center justify-center shadow-sm" :class="'bg-' + accentColor + ' text-white'" style="width: 45px; height: 45px;">
              <feather-icon :icon="reportIcon" svgClasses="w-6 h-6" />
            </div>
            <div>
              <p class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-0" style="opacity: 0.7;">Đang phân tích thông số</p>
              <div class="flex items-baseline flex-wrap mt-1">
                <h3 class="text-xl font-extrabold text-dark tracking-wide leading-tight mr-6 mb-0">{{ filterParam || 'Dữ liệu tổng hợp' }}</h3>
                <h2 class="text-3xl font-black mb-0" :class="'text-' + accentColor" v-if="totalValue">{{ totalValue }}</h2>
              </div>
            </div>
          </div>

          <!-- Right: Back Button -->
          <div class="w-full lg:w-auto flex-none text-right">
            <vs-button icon-pack="feather" icon="icon-arrow-left" type="border" color="dark" class="font-bold w-full lg:w-auto bg-white" @click="goBack">
              Quay lại
            </vs-button>
          </div>
          
        </div>
      </div>

      <!-- Data Table -->
      <vs-table :data="mockData" stripe pagination max-items="15" search class="report-table">
        <template slot="header">
          <vs-button icon-pack="feather" icon="icon-file-text" color="success" class="shadow-md">Xuất Excel</vs-button>
        </template>
        <template slot="thead">
          <vs-th sort-key="id">Mã Code</vs-th>
          <vs-th sort-key="name">Khách hàng / Học viên</vs-th>
          <vs-th sort-key="category">Phân loại / Nguồn</vs-th>
          <vs-th sort-key="date">Ngày thao tác</vs-th>
          <vs-th sort-key="amount" v-if="showAmount">Giá trị (VNĐ)</vs-th>
          <vs-th sort-key="status">Trạng thái</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td class="font-bold text-primary">{{ tr.id }}</vs-td>
            <vs-td>
              <div class="flex items-center">
                <vs-avatar :color="tr.color" :text="tr.name.charAt(0)" class="mr-2"/>
                <span class="font-semibold text-dark">{{ tr.name }}</span>
              </div>
            </vs-td>
            <vs-td>
              <vs-chip color="dark" transparent class="m-0">{{ tr.category }}</vs-chip>
            </vs-td>
            <vs-td class="text-gray-600">{{ tr.date }}</vs-td>
            <vs-td class="font-bold text-success" v-if="showAmount">{{ tr.amount | formatCurrency }}</vs-td>
            <vs-td>
              <vs-chip :color="tr.status === 'Thành công' ? 'success' : (tr.status === 'Đang xử lý' ? 'warning' : 'danger')" class="m-0">
                <span class="font-medium text-sm">{{ tr.status }}</span>
              </vs-chip>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>

    </div>
  </div>
</template>




<script>
export default {
  data() {
    return {
      filterParam: '',
      mockData: [],
      reportTitle: 'Danh Sách Dữ Liệu',
      tableTitle: 'Dữ liệu phân tích',
      reportIcon: 'FilterIcon',
      showAmount: true,
      totalValue: '0',
      accentColor: 'primary'
    }
  },
  created() {
    this.filterParam = this.$route.query.filter || 'Không có tham số';
    this.analyzeReportType();
    this.applyExactValue();
    this.generateMockData();
  },
  watch: {
    '$route.query.filter'(newVal) {
      this.filterParam = newVal || 'Không có tham số';
      this.analyzeReportType();
      this.applyExactValue();
      this.generateMockData();
    },
    '$route.query.value'() {
      this.applyExactValue();
    }
  },
  methods: {
    applyExactValue() {
      if (this.$route.query.value && this.$route.query.value !== '') {
         const val = parseFloat(this.$route.query.value);
         if (!isNaN(val)) {
            let fp = this.filterParam.toLowerCase();
            // Check for percentage charts first
            if (fp.includes('doanh thu theo sp') || fp.includes('phân bổ') || fp.includes('tỷ lệ') || fp.includes('rate')) {
               this.totalValue = val + '%';
            } else if (fp.includes('doanh thu') || fp.includes('dòng tiền') || fp.includes('lợi nhuận')) {
               this.totalValue = this.formatCurrency(val);
            } else {
               this.totalValue = val.toLocaleString();
            }
         } else {
            this.totalValue = this.$route.query.value;
         }
      }
    },
    formatCurrency(value) {
      if (value >= 1000000000) return (value / 1000000000).toFixed(2) + ' Tỷ';
      if (value >= 1000000) return (value / 1000000).toFixed(2) + ' Tr';
      return value.toLocaleString() + ' đ';
    },
    goBack() {
      const query = { ...this.$route.query };
      delete query.filter;
      this.$router.push({ path: '/admin/dashboard', query: query });
    },
    analyzeReportType() {
        let p = this.filterParam.toLowerCase();
        
        if (p.includes('doanh thu') || p.includes('tiền') || p.includes('cashflow') || p.includes('dt') || p.includes('doanh số') || p.includes('thu')) {
            this.reportTitle = 'Báo Cáo Doanh Thu Thu Tiền';
            this.tableTitle = 'Danh sách hóa đơn / Phiếu thu';
            this.reportIcon = 'DollarSignIcon';
            this.showAmount = true;
            this.totalValue = '4.52 Tỷ';
            this.accentColor = 'success';
        } else if (p.includes('học viên') || p.includes('hs') || p.includes('active') || p.includes('kids') || p.includes('teens')) {
            this.reportTitle = 'Danh Sách Học Viên Chi Tiết';
            this.tableTitle = 'Danh sách học sinh (Active)';
            this.reportIcon = 'UsersIcon';
            this.showAmount = false;
            this.totalValue = '12,500';
            this.accentColor = 'primary';
        } else if (p.includes('tỷ lệ') || p.includes('renew') || p.includes('churn') || p.includes('nps')) {
            this.reportTitle = 'Báo Cáo Biến Động Khách Hàng';
            this.tableTitle = 'Danh sách khách hàng tương ứng';
            this.reportIcon = 'ActivityIcon';
            this.showAmount = true;
            this.totalValue = '78.5%';
            this.accentColor = 'warning';
        } else {
            this.reportTitle = 'Báo Cáo Phân Tích Đa Chiều';
            this.tableTitle = 'Chi tiết dữ liệu tổng hợp';
            this.reportIcon = 'PieChartIcon';
            this.showAmount = true;
            this.totalValue = '1,450';
            this.accentColor = 'danger';
        }
    },
    generateMockData() {
      const colors = ['primary', 'success', 'warning', 'danger', 'dark'];
      const statuses = ['Thành công', 'Đang xử lý', 'Thất bại'];
      let data = [];
      
      for(let i=1; i<=35; i++) {
        let statusIdx = Math.floor(Math.random() * 3);
        // Ưu tiên trạng thái thành công cho dữ liệu Doanh thu
        if (this.showAmount && i % 4 !== 0) statusIdx = 0; 

        data.push({
          id: 'CODE-' + (1000 + i),
          name: (this.showAmount ? 'Phụ huynh ' : 'Học sinh ') + String.fromCharCode(65 + Math.floor(Math.random() * 26)) + ' ' + i,
          category: this.filterParam !== 'Không có tham số' ? this.filterParam : 'Nguồn Tự nhiên',
          date: '2026-07-' + (i < 10 ? '0'+i : i),
          amount: Math.floor(Math.random() * 15) * 1000000 + 500000,
          status: statuses[statusIdx],
          color: colors[Math.floor(Math.random() * colors.length)]
        });
      }
      this.mockData = data;
    }
  },
  filters: {
    formatCurrency(val) {
      if (!val) return '';
      return new Intl.NumberFormat('vi-VN').format(val) + ' đ';
    }
  }
}
</script>

<style>
/* Đảo ngược thứ tự Header Table: Tìm kiếm qua TRÁI, Export qua PHẢI */
.report-table .vs-table--header {
  display: flex !important;
  flex-direction: row-reverse !important;
  justify-content: space-between !important;
  width: 100%;
}
.report-table .vs-table--search {
  justify-content: flex-start !important;
  display: flex !important;
  max-width: 300px;
  margin: 0 !important;
}
.report-table .vs-table--search-input {
  width: 100%;
}
</style>
