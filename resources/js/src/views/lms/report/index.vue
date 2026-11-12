<template>
  <div id="lms-unified-reports" class="report-page-wrapper">
    <!-- Thanh tiêu đề & Chuyển đổi Tab Báo cáo -->
    <div class="report-header-card">
      <div class="report-header-top">
        <div>
          <h2 class="report-main-title">{{ currentTabTitle }}</h2>
          <p class="report-subtitle">
            <span v-if="filterMeta.scope === 'self'">
              Phạm vi hiển thị: <strong>Cá nhân Sale ({{ filterMeta.current_user.full_name }})</strong>
            </span>
            <span v-else-if="filterMeta.scope === 'team'">
              Phạm vi hiển thị: <strong>Đội nhóm ({{ 'Team ' + filterMeta.current_user.full_name }})</strong>
            </span>
            <span v-else>
              Phạm vi hiển thị: <strong>Toàn hệ thống / Các cơ sở được phân quyền</strong>
            </span>
          </p>
        </div>

        <div class="report-header-actions">
          <button
            v-if="filterMeta.can_manage_kpi"
            type="button"
            class="btn-action btn-kpi"
            @click="openKpiModal"
          >
            <i class="fa fa-bullseye mr-1"></i> Thiết lập KPI tháng {{ kpiForm.month }}/{{ kpiForm.year }}
          </button>
          <button
            type="button"
            class="btn-action btn-export"
            @click="exportCurrentTabCsv"
          >
            <i class="fa fa-file-excel mr-1"></i> Xuất Excel (CSV)
          </button>
        </div>
      </div>

      <!-- 3 Tabs Báo cáo chính -->
      <div class="report-tabs-nav">
        <button
          type="button"
          class="report-tab-btn"
          :class="{ active: activeTab === 'overview' }"
          @click="switchTab('overview')"
        >
          <i class="fa fa-chart-bar mr-1"></i>
          1. Báo cáo - Doanh thu - Tổng quan
        </button>
        <button
          type="button"
          class="report-tab-btn"
          :class="{ active: activeTab === 'team' }"
          @click="switchTab('team')"
        >
          <i class="fa fa-users mr-1"></i>
          2. Báo cáo - Doanh thu - Đội nhóm
        </button>
        <button
          type="button"
          class="report-tab-btn"
          :class="{ active: activeTab === 'business' }"
          @click="switchTab('business')"
        >
          <i class="fa fa-briefcase mr-1"></i>
          3. Báo cáo Kinh doanh (Nguồn &amp; Sản phẩm)
        </button>
      </div>
    </div>

    <!-- ======================================================================= -->
    <!-- TAB 1 & TAB 2: BÁO CÁO DOANH THU TỔNG QUAN & BÁO CÁO ĐỘI NHÓM          -->
    <!-- ======================================================================= -->
    <div v-if="activeTab === 'overview' || activeTab === 'team'" class="report-body-card">
      <!-- Bộ lọc Tổng quan / Đội nhóm (Bám sát ảnh mẫu) -->
      <div class="filter-grid-overview">
        <!-- Từ ngày & Đến ngày -->
        <div class="filter-date-pair">
          <input
            v-model="overviewFilters.start_date"
            type="date"
            class="erp-control"
            title="Từ ngày"
          />
          <input
            v-model="overviewFilters.end_date"
            type="date"
            class="erp-control"
            title="Đến ngày"
          />
        </div>

        <!-- Loại ngày -->
        <div class="filter-cell">
          <select v-model="overviewFilters.date_type" class="erp-control">
            <option value="created_at">Ngày tạo data</option>
            <option value="last_assign_date">Ngày nhận bàn giao</option>
            <option value="care_date">Ngày chăm sóc</option>
          </select>
        </div>

        <!-- Trường lọc Đội Nhóm (Hiển thị nổi bật ở Tab Đội nhóm hoặc khi chọn) -->
        <div v-if="activeTab === 'team'" class="filter-cell">
          <select
            v-model="overviewFilters.team_leader_id"
            class="erp-control erp-control-highlight"
            :disabled="filterMeta.scope === 'self' || filterMeta.scope === 'team'"
            @change="onOverviewTeamChange"
          >
            <option :value="0">- Chọn Đội Nhóm (Leader Sale) -</option>
            <option v-for="t in filterMeta.teams" :key="t.id" :value="t.id">
              {{ t.label }}
            </option>
          </select>
        </div>

        <!-- Chọn người quản lý -->
        <div class="filter-cell">
          <select
            v-model="overviewFilters.owner_id"
            class="erp-control"
            :disabled="filterMeta.scope === 'self'"
          >
            <option :value="0">- Chọn người quản lý -</option>
            <option v-for="s in filteredOverviewSales" :key="s.id" :value="s.id">
              {{ s.display_name }}
            </option>
          </select>
        </div>

        <!-- Quản lý đầu tiên -->
        <div class="filter-cell">
          <select v-model="overviewFilters.first_owner_id" class="erp-control">
            <option :value="0">- Quản lý đầu tiên -</option>
            <option v-for="u in filterMeta.creators" :key="u.id" :value="u.id">
              {{ u.full_name }}
            </option>
          </select>
        </div>

        <!-- Chọn loại hợp đồng -->
        <div class="filter-cell">
          <select v-model="overviewFilters.contract_type" class="erp-control">
            <option value="">- Chọn loại hợp đồng -</option>
            <option value="new">Hợp đồng mới</option>
            <option value="renew">Hợp đồng tái phí</option>
            <option value="trial">Học thử</option>
          </select>
        </div>

        <!-- Từ ngày hóa đơn -->
        <div class="filter-cell">
          <input
            v-model="overviewFilters.invoice_start_date"
            type="text"
            onfocus="(this.type='date')"
            onblur="if(!this.value)this.type='text'"
            placeholder="Từ ngày hóa đơn"
            class="erp-control"
          />
        </div>

        <!-- Đến ngày hóa đơn -->
        <div class="filter-cell">
          <input
            v-model="overviewFilters.invoice_end_date"
            type="text"
            onfocus="(this.type='date')"
            onblur="if(!this.value)this.type='text'"
            placeholder="Đến ngày hóa đơn"
            class="erp-control"
          />
        </div>

        <!-- Đang pacing -->
        <div class="filter-cell">
          <select v-model="overviewFilters.pacing" class="erp-control">
            <option value="">- Đang pacing -</option>
            <option value="1">Đang chăm sóc (Đang khóa)</option>
            <option value="0">Hết hạn chăm sóc (Mở khóa)</option>
          </select>
        </div>

        <!-- Học thử -->
        <div class="filter-cell">
          <select v-model="overviewFilters.trial_status" class="erp-control">
            <option value="">- Học thử -</option>
            <option value="1">Đã Check-in / Học thử</option>
            <option value="0">Chưa học thử</option>
          </select>
        </div>

        <!-- Phân loại (Hẹn checkin, thuê bao...) -->
        <div class="filter-cell">
          <select v-model="overviewFilters.status" class="erp-control">
            <option value="">- Phân loại -</option>
            <option v-for="st in filterMeta.statuses" :key="st.id" :value="st.id">
              {{ st.name }}
            </option>
          </select>
        </div>

        <!-- Chọn nguồn -->
        <div class="filter-cell">
          <select v-model="overviewFilters.source_id" class="erp-control" @change="overviewFilters.source_detail_id = 0">
            <option :value="0">- Chọn nguồn -</option>
            <option v-for="src in filterMeta.sources" :key="src.id" :value="src.id">
              {{ src.name }}
            </option>
          </select>
        </div>

        <!-- Chọn kênh (Kênh nguồn ngách) -->
        <div class="filter-cell">
          <select v-model="overviewFilters.source_detail_id" class="erp-control">
            <option :value="0">- Chọn kênh -</option>
            <option v-for="sd in filteredOverviewSourceDetails" :key="sd.id" :value="sd.id">
              {{ sd.name }}
            </option>
          </select>
        </div>

        <!-- Chọn người tạo -->
        <div class="filter-cell">
          <select v-model="overviewFilters.creator_id" class="erp-control">
            <option :value="0">- Chọn người tạo -</option>
            <option v-for="u in filterMeta.creators" :key="u.id" :value="u.id">
              {{ u.full_name }}
            </option>
          </select>
        </div>

        <!-- Trung tâm / Cơ sở -->
        <div class="filter-cell">
          <select v-model="overviewFilters.branch_id" class="erp-control">
            <option :value="0">- Tất cả cơ sở -</option>
            <option v-for="b in filterMeta.branches" :key="b.id" :value="b.id">
              {{ b.name }}
            </option>
          </select>
        </div>

        <!-- Nút Lọc & Đặt lại -->
        <div class="filter-actions-cell">
          <button type="button" class="btn-filter-green" :disabled="loading" @click="fetchOverviewReport">
            <i v-if="loading" class="fa fa-spinner fa-spin mr-1"></i>
            Lọc
          </button>
          <button type="button" class="btn-filter-reset" :disabled="loading" @click="resetOverviewFilters">
            Đặt lại
          </button>
        </div>
      </div>

      <!-- Bảng dữ liệu Tổng quan / Đội nhóm -->
      <div class="report-table-container">
        <table class="report-dark-header-table">
          <thead>
            <tr>
              <th class="col-stt">STT</th>
              <th class="col-staff">Họ tên nhân viên</th>
              <th class="col-num">Lượng data</th>
              <th class="col-num">Hợp đồng (%)</th>
              <th class="col-num">Doanh số tạm tính</th>
              <th class="col-num">Doanh số thực thu</th>
              <th class="col-num">Doanh số thực thu/data</th>
              <th class="col-num">Tổng nợ cần thu</th>
              <!-- 3 cột bổ sung ở cuối bảng khi xem Báo cáo Đội nhóm -->
              <th v-if="activeTab === 'team'" class="col-num col-kpi-header">Chỉ tiêu</th>
              <th v-if="activeTab === 'team'" class="col-num col-kpi-header">Thực đạt</th>
              <th v-if="activeTab === 'team'" class="col-num col-kpi-header">Phần trăm hoàn thành</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="activeTab === 'team' ? 11 : 8" class="td-empty">
                <i class="fa fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu báo cáo...
              </td>
            </tr>
            <tr v-else-if="overviewData.rows.length === 0">
              <td :colspan="activeTab === 'team' ? 11 : 8" class="td-empty">
                Không có dữ liệu phù hợp với bộ lọc đã chọn.
              </td>
            </tr>
            <tr v-else v-for="row in overviewData.rows" :key="row.user_id">
              <td class="col-stt font-semibold">{{ row.stt }}</td>
              <td class="col-staff">
                <div class="staff-name-main">{{ row.full_name }}</div>
                <div v-if="row.team_name" class="staff-team-sub">{{ row.team_name }}</div>
              </td>
              <td class="col-num font-semibold">{{ formatNumber(row.data_count) }}</td>
              <td class="col-num font-semibold">
                <span v-if="row.contract_count > 0">
                  {{ formatNumber(row.contract_count) }} ({{ formatPercent(row.conversion_rate) }}%)
                </span>
                <span v-else>0</span>
              </td>
              <td class="col-num font-semibold">{{ formatNumber(row.provisional_revenue) }}</td>
              <td class="col-num font-semibold">{{ formatNumber(row.actual_revenue) }}</td>
              <td class="col-num font-semibold">{{ formatNumber(row.revenue_per_data) }}</td>
              <td class="col-num font-semibold">{{ formatNumber(row.debt_amount) }}</td>

              <!-- 3 cột cuối của Báo cáo Đội nhóm -->
              <td v-if="activeTab === 'team'" class="col-num font-semibold text-primary">
                {{ formatNumber(row.target_amount) }}
              </td>
              <td v-if="activeTab === 'team'" class="col-num font-semibold text-success">
                {{ formatNumber(row.actual_achieved) }}
              </td>
              <td v-if="activeTab === 'team'" class="col-num">
                <span class="kpi-badge" :class="getKpiBadgeClass(row.completion_percent)">
                  {{ formatPercent(row.completion_percent) }}%
                </span>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="overviewData.rows.length > 0">
            <tr class="summary-total-row">
              <td colspan="2" class="col-staff font-bold">Tổng</td>
              <td class="col-num font-bold">{{ formatNumber(overviewData.summary.data_count) }}</td>
              <td class="col-num font-bold">
                <span v-if="overviewData.summary.contract_count > 0">
                  {{ formatNumber(overviewData.summary.contract_count) }} ({{ formatPercent(overviewData.summary.conversion_rate) }}%)
                </span>
                <span v-else>0</span>
              </td>
              <td class="col-num font-bold">{{ formatNumber(overviewData.summary.provisional_revenue) }}</td>
              <td class="col-num font-bold">{{ formatNumber(overviewData.summary.actual_revenue) }}</td>
              <td class="col-num font-bold">{{ formatNumber(overviewData.summary.revenue_per_data) }}</td>
              <td class="col-num font-bold">{{ formatNumber(overviewData.summary.debt_amount) }}</td>
              <td v-if="activeTab === 'team'" class="col-num font-bold text-primary">
                {{ formatNumber(overviewData.summary.target_amount) }}
              </td>
              <td v-if="activeTab === 'team'" class="col-num font-bold text-success">
                {{ formatNumber(overviewData.summary.actual_achieved) }}
              </td>
              <td v-if="activeTab === 'team'" class="col-num font-bold">
                <span class="kpi-badge" :class="getKpiBadgeClass(overviewData.summary.completion_percent)">
                  {{ formatPercent(overviewData.summary.completion_percent) }}%
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- ======================================================================= -->
    <!-- TAB 3: BÁO CÁO KINH DOANH (THEO NGUỒN KHÁCH HÀNG, KPI & SẢN PHẨM)       -->
    <!-- ======================================================================= -->
    <div v-if="activeTab === 'business'" class="report-body-card">
      <!-- Thanh chọn cấp độ nhóm dữ liệu: Cá nhân Sale | Từng Team | Từng Cơ sở | Tổng Trung tâm -->
      <div class="group-mode-bar">
        <span class="group-mode-label">Chế độ xem báo cáo:</span>
        <div class="group-mode-pills">
          <button
            type="button"
            class="mode-pill"
            :class="{ active: businessFilters.group_by === 'sale' }"
            @click="changeBusinessGroupBy('sale')"
          >
            <i class="fa fa-user mr-1"></i> Từng cá nhân Sale
          </button>
          <button
            v-if="filterMeta.scope !== 'self'"
            type="button"
            class="mode-pill"
            :class="{ active: businessFilters.group_by === 'team' }"
            @click="changeBusinessGroupBy('team')"
          >
            <i class="fa fa-users mr-1"></i> Từng Team
          </button>
          <button
            v-if="filterMeta.scope === 'all'"
            type="button"
            class="mode-pill"
            :class="{ active: businessFilters.group_by === 'branch' }"
            @click="changeBusinessGroupBy('branch')"
          >
            <i class="fa fa-building mr-1"></i> Từng Cơ sở
          </button>
          <button
            v-if="filterMeta.scope === 'all'"
            type="button"
            class="mode-pill"
            :class="{ active: businessFilters.group_by === 'center' }"
            @click="changeBusinessGroupBy('center')"
          >
            <i class="fa fa-globe mr-1"></i> Tổng của Trung tâm
          </button>
        </div>
      </div>

      <!-- Bộ lọc Báo cáo Kinh doanh -->
      <div class="filter-grid-business">
        <div class="filter-date-pair">
          <input v-model="businessFilters.start_date" type="date" class="erp-control" title="Từ ngày" />
          <input v-model="businessFilters.end_date" type="date" class="erp-control" title="Đến ngày" />
        </div>

        <div class="filter-cell">
          <select v-model="businessFilters.branch_id" class="erp-control">
            <option :value="0">- Tất cả cơ sở -</option>
            <option v-for="b in filterMeta.branches" :key="b.id" :value="b.id">
              {{ b.name }}
            </option>
          </select>
        </div>

        <div class="filter-cell">
          <select
            v-model="businessFilters.team_leader_id"
            class="erp-control"
            :disabled="filterMeta.scope === 'self' || filterMeta.scope === 'team'"
            @change="businessFilters.owner_id = 0"
          >
            <option :value="0">- Tất cả Đội nhóm (Team) -</option>
            <option v-for="t in filterMeta.teams" :key="t.id" :value="t.id">
              {{ t.label }}
            </option>
          </select>
        </div>

        <div class="filter-cell">
          <select
            v-model="businessFilters.owner_id"
            class="erp-control"
            :disabled="filterMeta.scope === 'self'"
          >
            <option :value="0">- Tất cả Nhân viên Sale -</option>
            <option v-for="s in filteredBusinessSales" :key="s.id" :value="s.id">
              {{ s.display_name }}
            </option>
          </select>
        </div>

        <div class="filter-cell">
          <select v-model="businessFilters.source_id" class="erp-control" @change="businessFilters.source_detail_id = 0">
            <option :value="0">- Nguồn khách hàng -</option>
            <option v-for="src in filterMeta.sources" :key="src.id" :value="src.id">
              {{ src.name }}
            </option>
          </select>
        </div>

        <div class="filter-cell">
          <select v-model="businessFilters.source_detail_id" class="erp-control">
            <option :value="0">- Kênh nguồn ngách -</option>
            <option v-for="sd in filteredBusinessSourceDetails" :key="sd.id" :value="sd.id">
              {{ sd.name }}
            </option>
          </select>
        </div>

        <div class="filter-cell">
          <select v-model="businessFilters.product_id" class="erp-control">
            <option :value="0">- Tất cả Sản phẩm -</option>
            <option v-for="prd in filterMeta.products" :key="prd.id" :value="prd.id">
              {{ prd.name }}
            </option>
          </select>
        </div>

        <div class="filter-actions-cell">
          <button type="button" class="btn-filter-green" :disabled="loading" @click="fetchBusinessReport">
            <i v-if="loading" class="fa fa-spinner fa-spin mr-1"></i>
            Lọc
          </button>
          <button type="button" class="btn-filter-reset" :disabled="loading" @click="resetBusinessFilters">
            Đặt lại
          </button>
        </div>
      </div>

      <!-- PHẦN 3A: BẢNG DOANH THU THEO NGUỒN KHÁCH HÀNG, KPI & TỶ LỆ CHUYỂN ĐỔI -->
      <div class="section-heading">
        <h3 class="section-title">
          <i class="fa fa-table mr-1 text-primary"></i>
          A. Bảng Doanh thu theo Nguồn khách hàng, Tỷ lệ chuyển đổi Data &amp; Tiến độ KPI tháng
        </h3>
      </div>

      <div class="report-table-container mb-6">
        <table class="report-dark-header-table">
          <thead>
            <tr>
              <th rowspan="2" class="col-stt">STT</th>
              <th rowspan="2" class="col-staff">{{ businessGroupHeaderLabel }}</th>
              <th rowspan="2" class="col-num">Lượng data</th>
              <th rowspan="2" class="col-num">Hợp đồng &amp; % Chuyển đổi</th>
              <th :colspan="businessData.sources.length" class="col-center-group">
                Doanh thu thực thu theo Nguồn khách hàng (VNĐ)
              </th>
              <th rowspan="2" class="col-num">Doanh số tạm tính</th>
              <th rowspan="2" class="col-num">Doanh thu thực thu</th>
              <th rowspan="2" class="col-num">Công nợ</th>
              <th rowspan="2" class="col-num col-kpi-header">KPI tháng</th>
              <th rowspan="2" class="col-num col-kpi-header">% Đạt KPI</th>
            </tr>
            <tr>
              <th
                v-for="src in businessData.sources"
                :key="'hdr-src-' + src.id"
                class="col-num col-sub-source"
              >
                {{ src.name }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="9 + businessData.sources.length" class="td-empty">
                <i class="fa fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu báo cáo kinh doanh...
              </td>
            </tr>
            <tr v-else-if="businessData.rows.length === 0">
              <td :colspan="9 + businessData.sources.length" class="td-empty">
                Không có dữ liệu phù hợp với bộ lọc đã chọn.
              </td>
            </tr>
            <tr v-else v-for="row in businessData.rows" :key="row.group_key">
              <td class="col-stt font-semibold">{{ row.stt }}</td>
              <td class="col-staff">
                <div class="staff-name-main">{{ row.group_name }}</div>
                <div v-if="row.sub_label" class="staff-team-sub">{{ row.sub_label }}</div>
              </td>
              <td class="col-num font-semibold">{{ formatNumber(row.data_count) }}</td>
              <td class="col-num font-semibold">
                <span v-if="row.contract_count > 0" class="conv-highlight">
                  {{ formatNumber(row.contract_count) }} ({{ formatPercent(row.conversion_rate) }}%)
                </span>
                <span v-else>0 (0%)</span>
              </td>
              <td
                v-for="src in businessData.sources"
                :key="row.group_key + '-src-' + src.id"
                class="col-num"
              >
                {{ formatNumber(row.source_revenues[src.id] || 0) }}
              </td>
              <td class="col-num font-semibold">{{ formatNumber(row.provisional_revenue) }}</td>
              <td class="col-num font-semibold text-success">{{ formatNumber(row.actual_revenue) }}</td>
              <td class="col-num font-semibold text-danger">{{ formatNumber(row.debt_amount) }}</td>
              <td class="col-num font-semibold text-primary">{{ formatNumber(row.target_amount) }}</td>
              <td class="col-num">
                <span class="kpi-badge" :class="getKpiBadgeClass(row.completion_percent)">
                  {{ formatPercent(row.completion_percent) }}%
                </span>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="businessData.rows.length > 0">
            <tr class="summary-total-row">
              <td colspan="2" class="col-staff font-bold">Tổng cộng</td>
              <td class="col-num font-bold">{{ formatNumber(businessData.summary.data_count) }}</td>
              <td class="col-num font-bold">
                {{ formatNumber(businessData.summary.contract_count) }} ({{ formatPercent(businessData.summary.conversion_rate) }}%)
              </td>
              <td
                v-for="src in businessData.sources"
                :key="'sum-src-' + src.id"
                class="col-num font-bold"
              >
                {{ formatNumber(businessData.summary.source_revenues[src.id] || 0) }}
              </td>
              <td class="col-num font-bold">{{ formatNumber(businessData.summary.provisional_revenue) }}</td>
              <td class="col-num font-bold text-success">{{ formatNumber(businessData.summary.actual_revenue) }}</td>
              <td class="col-num font-bold text-danger">{{ formatNumber(businessData.summary.debt_amount) }}</td>
              <td class="col-num font-bold text-primary">{{ formatNumber(businessData.summary.target_amount) }}</td>
              <td class="col-num font-bold">
                <span class="kpi-badge" :class="getKpiBadgeClass(businessData.summary.completion_percent)">
                  {{ formatPercent(businessData.summary.completion_percent) }}%
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- PHẦN 3B: BẢNG DOANH THU THEO TỪNG SẢN PHẨM (UCREA, BRIGHT I.G, TIẾNG ANH, TIỀN TIỂU HỌC...) -->
      <div class="section-heading">
        <h3 class="section-title">
          <i class="fa fa-layer-group mr-1 text-primary"></i>
          B. Bảng Doanh thu theo từng Sản phẩm (Ucrea, Bright I.G, Tiếng Anh, Tiền Tiểu Học...)
        </h3>
      </div>

      <div class="report-table-container">
        <table class="report-dark-header-table">
          <thead>
            <tr>
              <th class="col-stt">STT</th>
              <th class="col-staff">Tên Sản phẩm / Chương trình học</th>
              <th class="col-num">Số hợp đồng</th>
              <th class="col-num">Tỷ trọng HĐ (%)</th>
              <th
                v-for="src in businessData.sources"
                :key="'prd-hdr-src-' + src.id"
                class="col-num col-sub-source"
              >
                {{ src.name }}
              </th>
              <th class="col-num">Doanh số tạm tính</th>
              <th class="col-num">Doanh thu thực thu</th>
              <th class="col-num">Công nợ</th>
              <th class="col-num">Tỷ trọng Doanh số (%)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(prd, idx) in businessData.product_breakdown" :key="prd.product_id">
              <td class="col-stt font-semibold">{{ idx + 1 }}</td>
              <td class="col-staff font-semibold">{{ prd.product_name }}</td>
              <td class="col-num font-semibold">{{ formatNumber(prd.contract_count) }}</td>
              <td class="col-num">{{ formatPercent(prd.contract_share_percent) }}%</td>
              <td
                v-for="src in businessData.sources"
                :key="'prd-' + prd.product_id + '-src-' + src.id"
                class="col-num"
              >
                {{ formatNumber((prd.source_revenues && prd.source_revenues[src.id]) || 0) }}
              </td>
              <td class="col-num font-semibold">{{ formatNumber(prd.provisional_revenue) }}</td>
              <td class="col-num font-semibold text-success">{{ formatNumber(prd.actual_revenue) }}</td>
              <td class="col-num font-semibold text-danger">{{ formatNumber(prd.debt_amount) }}</td>
              <td class="col-num">
                <span class="kpi-badge kpi-badge-info">{{ formatPercent(prd.revenue_share_percent) }}%</span>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="businessData.product_breakdown.length > 0">
            <tr class="summary-total-row">
              <td colspan="2" class="col-staff font-bold">Tổng toàn bộ sản phẩm</td>
              <td class="col-num font-bold">{{ formatNumber(businessData.summary.contract_count) }}</td>
              <td class="col-num font-bold">100%</td>
              <td
                v-for="src in businessData.sources"
                :key="'prd-sum-src-' + src.id"
                class="col-num font-bold"
              >
                {{ formatNumber(businessData.summary.source_revenues[src.id] || 0) }}
              </td>
              <td class="col-num font-bold">{{ formatNumber(businessData.summary.provisional_revenue) }}</td>
              <td class="col-num font-bold text-success">{{ formatNumber(businessData.summary.actual_revenue) }}</td>
              <td class="col-num font-bold text-danger">{{ formatNumber(businessData.summary.debt_amount) }}</td>
              <td class="col-num font-bold">100%</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL THIẾT LẬP CHỈ TIÊU KPI THÁNG CHO NHÂN VIÊN SALE / ĐỘI NHÓM         -->
    <!-- ======================================================================= -->
    <div v-if="showKpiModal" class="kpi-modal-backdrop" @click.self="showKpiModal = false">
      <div class="kpi-modal-dialog">
        <div class="kpi-modal-header">
          <h3 class="kpi-modal-title">
            <i class="fa fa-bullseye mr-2 text-primary"></i>
            Thiết lập Chỉ tiêu KPI Doanh thu Tháng {{ kpiForm.month }}/{{ kpiForm.year }}
          </h3>
          <button type="button" class="kpi-close-btn" @click="showKpiModal = false">&times;</button>
        </div>

        <div class="kpi-modal-body">
          <div class="kpi-period-selector mb-4">
            <div>
              <label class="erp-label">Tháng:</label>
              <select v-model.number="kpiForm.month" class="erp-control" style="width: 120px; display: inline-block;">
                <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
              </select>
            </div>
            <div>
              <label class="erp-label">Năm:</label>
              <select v-model.number="kpiForm.year" class="erp-control" style="width: 120px; display: inline-block;">
                <option v-for="y in [2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
          </div>

          <div class="report-table-container" style="max-height: 420px; overflow-y: auto;">
            <table class="report-dark-header-table">
              <thead>
                <tr>
                  <th class="col-stt">STT</th>
                  <th class="col-staff">Nhân viên Kinh doanh</th>
                  <th class="col-staff">Đội nhóm (Team)</th>
                  <th class="col-num" style="width: 240px;">Chỉ tiêu KPI tháng (VNĐ)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in kpiForm.items" :key="item.user_id">
                  <td class="col-stt">{{ idx + 1 }}</td>
                  <td class="col-staff font-semibold">{{ item.full_name }}</td>
                  <td class="col-staff">{{ item.team_name || '-' }}</td>
                  <td class="col-num">
                    <input
                      v-model.number="item.target_amount"
                      type="number"
                      min="0"
                      step="1000000"
                      class="erp-control text-right font-semibold"
                      placeholder="Nhập chỉ tiêu VNĐ..."
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="kpi-modal-footer">
          <button type="button" class="btn-filter-reset" @click="showKpiModal = false">Đóng</button>
          <button type="button" class="btn-filter-green" :disabled="savingKpi" @click="saveKpiTargets">
            <i v-if="savingKpi" class="fa fa-spinner fa-spin mr-1"></i>
            Lưu chỉ tiêu KPI
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'
import u from '../../../until/helper.js'

export default {
  name: 'LmsUnifiedReports',
  data() {
    const now = new Date()
    const yyyy = now.getFullYear()
    const mm = String(now.getMonth() + 1).padStart(2, '0')
    const dd = String(now.getDate()).padStart(2, '0')
    const firstDayOfMonth = `${yyyy}-${mm}-01`
    const todayStr = `${yyyy}-${mm}-${dd}`

    return {
      activeTab: 'overview', // 'overview' | 'team' | 'business'
      loading: false,
      savingKpi: false,
      showKpiModal: false,
      defaultStartDate: firstDayOfMonth,
      defaultEndDate: todayStr,

      filterMeta: {
        scope: 'all',
        can_manage_kpi: false,
        current_user: {
          id: 0,
          full_name: '',
          hrm_id: '',
          role_id: 0,
          manager_id: 0
        },
        branches: [],
        teams: [],
        sales: [],
        sources: [],
        source_details: [],
        products: [],
        creators: [],
        statuses: []
      },

      overviewFilters: {
        start_date: firstDayOfMonth,
        end_date: todayStr,
        date_type: 'created_at',
        branch_id: 0,
        team_leader_id: 0,
        owner_id: 0,
        first_owner_id: 0,
        contract_type: '',
        invoice_start_date: '',
        invoice_end_date: '',
        pacing: '',
        trial_status: '',
        status: '',
        source_id: 0,
        source_detail_id: 0,
        creator_id: 0
      },

      overviewData: {
        rows: [],
        summary: {
          data_count: 0,
          contract_count: 0,
          conversion_rate: 0,
          provisional_revenue: 0,
          actual_revenue: 0,
          revenue_per_data: 0,
          debt_amount: 0,
          target_amount: 0,
          actual_achieved: 0,
          completion_percent: 0
        },
        period: {
          year: yyyy,
          month: now.getMonth() + 1
        }
      },

      businessFilters: {
        group_by: 'sale', // 'sale' | 'team' | 'branch' | 'center'
        start_date: firstDayOfMonth,
        end_date: todayStr,
        branch_id: 0,
        team_leader_id: 0,
        owner_id: 0,
        source_id: 0,
        source_detail_id: 0,
        product_id: 0
      },

      businessData: {
        group_by: 'sale',
        sources: [],
        rows: [],
        summary: {
          data_count: 0,
          contract_count: 0,
          conversion_rate: 0,
          provisional_revenue: 0,
          actual_revenue: 0,
          debt_amount: 0,
          target_amount: 0,
          completion_percent: 0,
          source_revenues: {}
        },
        product_breakdown: [],
        period: {
          year: yyyy,
          month: now.getMonth() + 1
        }
      },

      kpiForm: {
        year: yyyy,
        month: now.getMonth() + 1,
        items: []
      }
    }
  },
  computed: {
    currentTabTitle() {
      if (this.activeTab === 'overview') {
        return 'Báo cáo - Doanh thu - Tổng quan'
      }
      if (this.activeTab === 'team') {
        return 'Báo cáo - Doanh thu - Đội nhóm'
      }
      return 'Báo cáo Kinh doanh (Theo Nguồn khách hàng, KPI & Sản phẩm)'
    },
    filteredOverviewSales() {
      const leaderId = Number(this.overviewFilters.team_leader_id || 0)
      if (!leaderId) {
        return this.filterMeta.sales
      }
      return this.filterMeta.sales.filter(
        s => Number(s.id) === leaderId || Number(s.manager_id) === leaderId
      )
    },
    filteredOverviewSourceDetails() {
      const srcId = Number(this.overviewFilters.source_id || 0)
      if (!srcId) {
        return this.filterMeta.source_details
      }
      return this.filterMeta.source_details.filter(sd => Number(sd.source_id) === srcId)
    },
    filteredBusinessSales() {
      const leaderId = Number(this.businessFilters.team_leader_id || 0)
      if (!leaderId) {
        return this.filterMeta.sales
      }
      return this.filterMeta.sales.filter(
        s => Number(s.id) === leaderId || Number(s.manager_id) === leaderId
      )
    },
    filteredBusinessSourceDetails() {
      const srcId = Number(this.businessFilters.source_id || 0)
      if (!srcId) {
        return this.filterMeta.source_details
      }
      return this.filterMeta.source_details.filter(sd => Number(sd.source_id) === srcId)
    },
    businessGroupHeaderLabel() {
      if (this.businessData.group_by === 'team') return 'Đội nhóm (Team Sale)'
      if (this.businessData.group_by === 'branch') return 'Cơ sở / Trung tâm'
      if (this.businessData.group_by === 'center') return 'Toàn Trung tâm'
      return 'Họ tên nhân viên Sale'
    }
  },
  async created() {
    await this.loadInitFilters()
    await this.fetchOverviewReport()
  },
  methods: {
    async loadInitFilters() {
      try {
        const res = await axios.g('/api/lms/reports/init-filters')
        if (res && res.data && res.data.status === 1) {
          this.filterMeta = res.data.data
          if (this.filterMeta.scope === 'self') {
            this.overviewFilters.owner_id = this.filterMeta.current_user.id
            this.businessFilters.owner_id = this.filterMeta.current_user.id
          } else if (this.filterMeta.scope === 'team') {
            this.overviewFilters.team_leader_id = this.filterMeta.current_user.id
            this.businessFilters.team_leader_id = this.filterMeta.current_user.id
          }
        }
      } catch (e) {
        console.error('Lỗi tải bộ lọc báo cáo:', e)
      }
    },

    switchTab(tab) {
      this.activeTab = tab
      if (tab === 'overview') {
        if (this.filterMeta.scope === 'all') {
          this.overviewFilters.team_leader_id = 0
        }
        this.fetchOverviewReport()
      } else if (tab === 'team') {
        if (this.filterMeta.scope === 'team') {
          this.overviewFilters.team_leader_id = this.filterMeta.current_user.id
        }
        this.fetchOverviewReport()
      } else if (tab === 'business') {
        this.fetchBusinessReport()
      }
    },

    onOverviewTeamChange() {
      this.overviewFilters.owner_id = 0
      this.fetchOverviewReport()
    },

    async fetchOverviewReport() {
      this.loading = true
      try {
        const payload = { ...this.overviewFilters }
        if (this.activeTab === 'overview' && this.filterMeta.scope === 'all') {
          payload.team_leader_id = 0
        }
        const res = await axios.p('/api/lms/reports/overview', payload)
        if (res && res.data && res.data.status === 1) {
          this.overviewData = res.data.data
          if (res.data.data.period) {
            this.kpiForm.year = res.data.data.period.year
            this.kpiForm.month = res.data.data.period.month
          }
        }
      } catch (e) {
        console.error('Lỗi tải báo cáo tổng quan / đội nhóm:', e)
      } finally {
        this.loading = false
      }
    },

    resetOverviewFilters() {
      this.overviewFilters = {
        start_date: this.defaultStartDate,
        end_date: this.defaultEndDate,
        date_type: 'created_at',
        branch_id: 0,
        team_leader_id: this.filterMeta.scope === 'team' ? this.filterMeta.current_user.id : 0,
        owner_id: this.filterMeta.scope === 'self' ? this.filterMeta.current_user.id : 0,
        first_owner_id: 0,
        contract_type: '',
        invoice_start_date: '',
        invoice_end_date: '',
        pacing: '',
        trial_status: '',
        status: '',
        source_id: 0,
        source_detail_id: 0,
        creator_id: 0
      }
      this.fetchOverviewReport()
    },

    changeBusinessGroupBy(mode) {
      this.businessFilters.group_by = mode
      this.fetchBusinessReport()
    },

    async fetchBusinessReport() {
      this.loading = true
      try {
        const res = await axios.p('/api/lms/reports/business', this.businessFilters)
        if (res && res.data && res.data.status === 1) {
          this.businessData = res.data.data
          if (res.data.data.period) {
            this.kpiForm.year = res.data.data.period.year
            this.kpiForm.month = res.data.data.period.month
          }
        }
      } catch (e) {
        console.error('Lỗi tải báo cáo kinh doanh:', e)
      } finally {
        this.loading = false
      }
    },

    resetBusinessFilters() {
      this.businessFilters = {
        group_by: this.businessFilters.group_by,
        start_date: this.defaultStartDate,
        end_date: this.defaultEndDate,
        branch_id: 0,
        team_leader_id: this.filterMeta.scope === 'team' ? this.filterMeta.current_user.id : 0,
        owner_id: this.filterMeta.scope === 'self' ? this.filterMeta.current_user.id : 0,
        source_id: 0,
        source_detail_id: 0,
        product_id: 0
      }
      this.fetchBusinessReport()
    },

    openKpiModal() {
      const existingMap = {}
      this.overviewData.rows.forEach(r => {
        existingMap[r.user_id] = r.target_amount || 0
      })
      this.kpiForm.items = this.filterMeta.sales.map(s => ({
        user_id: s.id,
        full_name: s.full_name,
        team_name: s.team_label,
        target_amount: existingMap[s.id] !== undefined ? existingMap[s.id] : 0
      }))
      this.showKpiModal = true
    },

    async saveKpiTargets() {
      this.savingKpi = true
      try {
        const res = await axios.p('/api/lms/reports/save-targets', {
          year: this.kpiForm.year,
          month: this.kpiForm.month,
          targets: this.kpiForm.items
        })
        if (res && res.data && res.data.status === 1) {
          this.$vs && this.$vs.notify({
            title: 'Thành công',
            text: res.data.message,
            color: 'success'
          })
          this.showKpiModal = false
          if (this.activeTab === 'business') {
            this.fetchBusinessReport()
          } else {
            this.fetchOverviewReport()
          }
        }
      } catch (e) {
        console.error('Lỗi lưu KPI:', e)
      } finally {
        this.savingKpi = false
      }
    },

    formatNumber(val) {
      const num = Number(val || 0)
      if (!num) return '0'
      return num.toLocaleString('en-US', { maximumFractionDigits: 0 })
    },

    formatPercent(val) {
      const num = Number(val || 0)
      if (!num) return '0.00'
      return num.toFixed(2)
    },

    getKpiBadgeClass(pct) {
      const n = Number(pct || 0)
      if (n >= 100) return 'kpi-badge-success'
      if (n >= 50) return 'kpi-badge-warning'
      return 'kpi-badge-neutral'
    },

    exportCurrentTabCsv() {
      let headers = []
      let rows = []
      let filename = `bao-cao-${this.activeTab}-${new Date().toISOString().slice(0, 10)}.csv`

      if (this.activeTab === 'overview' || this.activeTab === 'team') {
        headers = [
          'STT',
          'Họ tên nhân viên',
          'Đội nhóm',
          'Lượng data',
          'Hợp đồng (%)',
          'Doanh số tạm tính',
          'Doanh số thực thu',
          'Doanh số thực thu/data',
          'Tổng nợ cần thu'
        ]
        if (this.activeTab === 'team') {
          headers.push('Chỉ tiêu', 'Thực đạt', 'Phần trăm hoàn thành (%)')
        }
        this.overviewData.rows.forEach(r => {
          const line = [
            r.stt,
            r.full_name,
            r.team_name || '',
            r.data_count,
            r.contract_count > 0 ? `${r.contract_count} (${this.formatPercent(r.conversion_rate)}%)` : '0',
            r.provisional_revenue,
            r.actual_revenue,
            r.revenue_per_data,
            r.debt_amount
          ]
          if (this.activeTab === 'team') {
            line.push(r.target_amount, r.actual_achieved, `${this.formatPercent(r.completion_percent)}%`)
          }
          rows.push(line)
        })
      } else {
        const srcHeaders = this.businessData.sources.map(s => `Nguồn: ${s.name}`)
        headers = [
          'STT',
          this.businessGroupHeaderLabel,
          'Lượng data',
          'Hợp đồng & % Chuyển đổi',
          ...srcHeaders,
          'Doanh số tạm tính',
          'Doanh thu thực thu',
          'Công nợ',
          'KPI tháng',
          '% Đạt KPI'
        ]
        this.businessData.rows.forEach(r => {
          const srcVals = this.businessData.sources.map(s => r.source_revenues[s.id] || 0)
          rows.push([
            r.stt,
            r.group_name,
            r.data_count,
            `${r.contract_count} (${this.formatPercent(r.conversion_rate)}%)`,
            ...srcVals,
            r.provisional_revenue,
            r.actual_revenue,
            r.debt_amount,
            r.target_amount,
            `${this.formatPercent(r.completion_percent)}%`
          ])
        })
      }

      const csvContent =
        '\uFEFF' +
        [headers, ...rows]
          .map(e => e.map(cell => `"${String(cell ?? '').replace(/"/g, '""')}"`).join(','))
          .join('\n')

      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.setAttribute('download', filename)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }
  }
}
</script>

<style scoped>
.report-page-wrapper {
  font-size: 14px;
  color: #1e293b;
}

.report-header-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 18px 20px 0 20px;
  margin-bottom: 16px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.report-header-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 14px;
}

.report-main-title {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.report-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.report-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-action {
  height: 36px;
  padding: 0 14px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  border: 1px solid transparent;
  transition: all 0.15s ease;
}

.btn-kpi {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}
.btn-kpi:hover {
  background: #dbeafe;
}

.btn-export {
  background: #f0fdf4;
  color: #15803d;
  border-color: #bbf7d0;
}
.btn-export:hover {
  background: #dcfce7;
}

/* Tabs */
.report-tabs-nav {
  display: flex;
  gap: 6px;
  border-bottom: 1px solid #e2e8f0;
  overflow-x: auto;
}

.report-tab-btn {
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s ease;
}

.report-tab-btn:hover {
  color: #0f172a;
  background: #f8fafc;
}

.report-tab-btn.active {
  color: #dc2626;
  border-bottom-color: #dc2626;
  background: #fff5f5;
}

/* Body Card */
.report-body-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 18px 20px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

/* Filter Grids (bám sát screenshot) */
.filter-grid-overview,
.filter-grid-business {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 18px;
}

@media (max-width: 1400px) {
  .filter-grid-overview,
  .filter-grid-business {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

@media (max-width: 992px) {
  .filter-grid-overview,
  .filter-grid-business {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.filter-date-pair {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}

.erp-control {
  width: 100%;
  height: 36px;
  padding: 6px 10px;
  font-size: 13px;
  color: #1e293b;
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.erp-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}

.erp-control:disabled {
  background-color: #f1f5f9 !important;
  border-color: #e2e8f0 !important;
  color: #94a3b8 !important;
  cursor: not-allowed;
}

.erp-control-highlight {
  border-color: #2563eb;
  background-color: #f8fafc;
  font-weight: 600;
}

.filter-actions-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-filter-green {
  height: 36px;
  padding: 0 18px;
  background-color: #28a745;
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.15s ease;
}

.btn-filter-green:hover {
  background-color: #218838;
}

.btn-filter-reset {
  height: 36px;
  padding: 0 14px;
  background-color: #f1f5f9;
  color: #475569;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  cursor: pointer;
  white-space: nowrap;
}

.btn-filter-reset:hover {
  background-color: #e2e8f0;
}

/* Group mode bar in Tab 3 */
.group-mode-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 10px 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  margin-bottom: 14px;
}

.group-mode-label {
  font-size: 13px;
  font-weight: 700;
  color: #334155;
  white-space: nowrap;
}

.group-mode-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.mode-pill {
  height: 34px;
  padding: 0 14px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
  white-space: nowrap;
}

.mode-pill.active {
  background: #1e293b;
  color: #ffffff;
  border-color: #1e293b;
}

.section-heading {
  margin: 14px 0 10px 0;
}

.section-title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

/* Table (Dark header matching screenshot) */
.report-table-container {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
}

.report-dark-header-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.report-dark-header-table thead th {
  background-color: #2b3441;
  color: #ffffff;
  font-weight: 700;
  padding: 11px 12px;
  border: 1px solid #3b4656;
  white-space: nowrap;
  text-align: left;
}

.report-dark-header-table thead th.col-num {
  text-align: right;
}

.report-dark-header-table thead th.col-center-group {
  text-align: center;
  background-color: #1e293b;
}

.report-dark-header-table thead th.col-sub-source {
  background-color: #334155;
  font-size: 13px;
}

.report-dark-header-table thead th.col-kpi-header {
  background-color: #1e3a8a;
}

.report-dark-header-table tbody td {
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  color: #1e293b;
  vertical-align: middle;
}

.report-dark-header-table tbody tr:nth-child(even) {
  background-color: #f8fafc;
}

.report-dark-header-table tbody tr:hover {
  background-color: #f1f5f9;
}

.col-stt {
  width: 52px;
  text-align: center !important;
}

.col-staff {
  min-width: 190px;
}

.col-num {
  text-align: right;
  white-space: nowrap;
}

.staff-name-main {
  font-weight: 600;
  color: #0f172a;
}

.staff-team-sub {
  font-size: 13px;
  color: #2563eb;
  font-weight: 500;
  margin-top: 2px;
}

.summary-total-row td {
  background-color: #f1f5f9 !important;
  border: 1px solid #cbd5e1;
  padding: 11px 12px;
  font-weight: 700;
  color: #0f172a;
}

.td-empty {
  text-align: center;
  padding: 28px 16px !important;
  color: #64748b;
  font-size: 14px;
}

.kpi-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 700;
  white-space: nowrap;
}

.kpi-badge-success {
  background-color: #dcfce7;
  color: #15803d;
}

.kpi-badge-warning {
  background-color: #fef3c7;
  color: #b45309;
}

.kpi-badge-neutral {
  background-color: #f1f5f9;
  color: #475569;
}

.kpi-badge-info {
  background-color: #e0f2fe;
  color: #0369a1;
}

.conv-highlight {
  color: #0f172a;
}

/* Modal KPI */
.kpi-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.kpi-modal-dialog {
  background: #ffffff;
  border-radius: 8px;
  width: 100%;
  max-width: 780px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.kpi-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.kpi-modal-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.kpi-close-btn {
  background: none;
  border: none;
  font-size: 22px;
  color: #64748b;
  cursor: pointer;
}

.kpi-modal-body {
  padding: 18px 20px;
}

.kpi-period-selector {
  display: flex;
  align-items: center;
  gap: 20px;
}

.erp-label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-right: 8px;
}

.kpi-modal-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  padding: 12px 20px;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}
</style>
