<template>
  <div id="loyalty-module" class="mt-4">
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold text-slate-800">Quản lý Loyalty</h1>
      <p class="text-slate-500 mt-2">Phân hệ chăm sóc khách hàng và hệ thống điểm thưởng.</p>
    </div>

    <vs-tabs position="left" color="primary" class="loyalty-tabs">
      <vs-tab label="Dashboard Thống kê" icon-pack="feather" icon="icon-pie-chart">
        <div class="tab-content-wrapper">
          <loyalty-dashboard />
        </div>
      </vs-tab>
      
      <vs-tab label="Lịch sử tích/tiêu điểm" icon-pack="feather" icon="icon-clock">
        <div class="tab-content-wrapper">
          <loyalty-history />
        </div>
      </vs-tab>

      <vs-tab label="Quản lý hạng thành viên" icon-pack="feather" icon="icon-award">
        <div class="tab-content-wrapper">
          <loyalty-member-ranks />
        </div>
      </vs-tab>

      <vs-tab label="Quy tắc tích điểm" icon-pack="feather" icon="icon-check-square">
        <div class="tab-content-wrapper">
          <loyalty-rules />
        </div>
      </vs-tab>

      <vs-tab label="Cấu hình quy đổi điểm" icon-pack="feather" icon="icon-refresh-cw">
        <div class="tab-content-wrapper">
          <loyalty-point-conversion />
        </div>
      </vs-tab>

      <vs-tab label="Danh sách Voucher/Quà" icon-pack="feather" icon="icon-gift">
        <div class="tab-content-wrapper">
          <loyalty-vouchers />
        </div>
      </vs-tab>
    </vs-tabs>
  </div>
</template>

<script>
import LoyaltyDashboard from './LoyaltyDashboard.vue'
import LoyaltyHistory from './History.vue'
import LoyaltyMemberRanks from './MemberRanks.vue'
import LoyaltyRules from './Rules.vue'
import LoyaltyPointConversion from './PointConversion.vue'
import LoyaltyVouchers from './Vouchers.vue'

export default {
  components: {
    LoyaltyDashboard,
    LoyaltyHistory,
    LoyaltyMemberRanks,
    LoyaltyRules,
    LoyaltyPointConversion,
    LoyaltyVouchers
  }
}
</script>

<style lang="scss">
.loyalty-tabs {
  .vs-tabs--ul {
    background: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.05);
    padding: 1rem 0;
    min-width: 250px;
    
    .vs-tabs--li {
      margin-bottom: 0.25rem;
      .vs-tabs--btn {
        padding: 12px 20px;
        font-size: 1rem;
        font-weight: 500;
        justify-content: flex-start;
        text-align: left;
        color: #626262;

        &.active {
          background: rgba(var(--vs-primary), 0.1) !important;
          color: rgba(var(--vs-primary), 1) !important;
          border-right: 4px solid rgba(var(--vs-primary), 1);
          font-weight: 700;
        }

        i {
          margin-right: 12px;
          font-size: 1.2rem;
        }
      }
    }
  }

  .vs-tabs--content {
    padding-left: 2rem;
    padding-top: 0;
    
    @media (max-width: 768px) {
      padding-left: 0;
      padding-top: 2rem;
    }
  }
}

.tab-content-wrapper {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
