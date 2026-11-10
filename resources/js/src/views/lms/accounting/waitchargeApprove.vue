

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label for="" class="vs-input--label">Trung tâm</label>
            <multiselect
                name="search_branch"
                placeholder="Chọn trung tâm"
                v-model="searchData.arr_branch"
                :options="branch_list"
                label="name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã phiếu thu, mã HS, tên HS, mã HĐ" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label for="" class="vs-input--label">Trạng thái phiếu thu</label>
            <select class="vs-inputx vs-input--input normal" v-model="searchData.status">
              <option value="">-- Tất cả trạng thái --</option>
              <option value="0">Chờ duyệt</option>
              <option value="1">Đã duyệt</option>
              <option value="2">Từ chối duyệt</option>
            </select>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1" class="text-center">Mã Phiếu Thu</th>
                  <th colspan="1" rowspan="1" class="text-center">Mã Hợp Đồng</th>
                  <th colspan="1" rowspan="1">Học sinh</th>
                  <th colspan="1" rowspan="1">Trung tâm</th>
                  <th colspan="1" rowspan="1">Khóa học - Gói phí</th>
                  <th colspan="1" rowspan="1" class="text-right">Số tiền thu</th>
                  <th colspan="1" rowspan="1" class="text-center">Hình thức</th>
                  <th colspan="1" rowspan="1" class="text-center">Ngày thu</th>
                  <th colspan="1" rowspan="1">Người tạo (Sale)</th>
                  <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                  <th colspan="1" rowspan="1">Người duyệt</th>
                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in contracts" :key="index">
                <!---->
                
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td text-center">
                  <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`" class="font-bold text-primary">
                    {{ item.receipt_code || item.code }}
                  </router-link>
                </td>
                <td class="td vs-table--td text-center">
                  <router-link :to="`/lms/contracts/${item.contract_id}/detail`" class="font-bold">
                    {{ item.contract_code }}
                  </router-link>
                </td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.name }}</strong></p>
                  <p class="text-xs text-muted">Mã: {{ item.lms_code }}</p>
                </td>
                <td class="td vs-table--td">
                  <p>{{ item.branch_name }}</p>
                </td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.product_name }}</strong></p>
                  <p class="text-xs text-muted">{{ item.tuition_fee_name }}</p>
                </td> 
                <td class="td vs-table--td text-right">
                  <p class="font-bold text-danger" style="font-size: 15px;">{{ item.charge_amount | formatMoney }}</p>
                </td>
                <td class="td vs-table--td text-center">
                  <span class="badge" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
                    {{ item.method == 0 ? 'Tiền mặt' : (item.method == 1 ? 'Chuyển khoản' : 'Thẻ tín dụng') }}
                  </span>
                </td>
                <td class="td vs-table--td text-center">
                  <p>{{ item.charge_date }}</p>
                </td>
                <td class="td vs-table--td">
                  <p>{{ item.creator_name }}</p>
                  <p class="text-xs text-muted">{{ item.created_at }}</p>
                </td>
                <td class="td vs-table--td text-center">
                  <span v-if="item.status == 0" class="badge" style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                    Chờ duyệt
                  </span>
                  <span v-else-if="item.status == 1" class="badge" style="background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                    Đã duyệt
                  </span>
                  <span v-else-if="item.status == 2" class="badge" style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                    Từ chối duyệt
                  </span>
                </td>
                <td class="td vs-table--td">
                  <p v-if="item.approver_name">{{ item.approver_name }}</p>
                  <p v-if="item.approved_at" class="text-xs text-muted">{{ item.approved_at }}</p>
                  <span v-if="!item.approver_name" class="text-muted">-</span>
                </td>
                <td class="td vs-table--td text-center list-action"> 
                    <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`" >
                      <vs-button size="small" title="Xem chi tiết"><i class="fa fa-eye"></i></vs-button>
                    </router-link> 
                    <vs-button size="small" color="success" v-if="item.status == 0" @click="confirmApprove(item)" title="Duyệt phiếu thu">
                      <i class="fa fa-check"></i>
                    </vs-button>
                    <vs-button size="small" color="danger" v-if="item.status == 0" @click="confirmReject(item)" title="Từ chối duyệt">
                      <i class="fa fa-times"></i>
                    </vs-button>
                    <router-link :to="`/pages/print/receipt/${item.id}`" target="_blank"> 
                      <vs-button color="warning" size="small" title="In phiếu thu"><i class="fa fa-print"></i></vs-button>
                    </router-link> 
                </td>
              </tr>
            </table>
            
          </div>
        </div>
      </div>
      <div class="flex flex-wrap items-center mt-5">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)" >
              <span>{{item}}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
              style="width: calc(100% - 160px);"
              v-if="Math.ceil(pagination.total / pagination.limit) >1"
              :total="Math.ceil(pagination.total / pagination.limit)"
              :max="7"
              v-model="pagination.cpage" @change="changePage()"/>
      </div>
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import u from '../../../until/helper.js'

  export default {
    components: { 
      vSelect,
      Multiselect
    },
    data() {
      return {
        branch_list: [],
        searchData: {
          arr_branch: "",
          branch_id:"",
          keyword: "",
          status: "",
          dateRange: "",
        },
        contracts: [],
        limitSource: [20, 50, 100, 500],
        pagination: {
          url: "/api/roles/list",
          id: "",
          style: "line",
          class: "",
          spage: 1,
          ppage: 1,
          npage: 0,
          lpage: 1,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: [],
          init: 0
        },
        delete_id:'',
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      this.getData();
    },
    methods: {
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch= ""
        this.searchData.branch_id= ""
        this.searchData.status = ""
        this.searchData.pagination= this.pagination
        this.getData();
      },
      getData() {
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch
        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            status: this.searchData.status,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/accounting/waitcharge-approve/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data.list
            this.pagination = response.data.paging;
            setTimeout(() => {
              this.pagination.init = 1;
            }, 500)
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmApprove(item) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'success',
          title: 'Xác nhận duyệt phiếu thu',
          text: `Bạn có chắc chắn muốn duyệt phiếu thu ${item.receipt_code || item.code} của học sinh ${item.name} với số tiền ${u.formatMoney(item.charge_amount)}? Tiền sẽ được ghi nhận vào hợp đồng.`,
          acceptText: 'Duyệt phiếu thu',
          cancelText: 'Hủy',
          accept: () => {
            this.processApproval(item.id, 1);
          }
        });
      },
      confirmReject(item) {
        this.$vs.dialog({
          type: 'prompt',
          color: 'danger',
          title: 'Từ chối duyệt phiếu thu',
          text: `Nhập lý do từ chối duyệt phiếu thu ${item.receipt_code || item.code}:`,
          inputPlaceholder: 'Lý do từ chối...',
          acceptText: 'Từ chối',
          cancelText: 'Hủy',
          accept: (val) => {
            this.processApproval(item.id, 2, val);
          }
        });
      },
      processApproval(id, status, rejectReason = '') {
        this.$vs.loading();
        axios.p('/api/lms/accounting/waitcharge-approve/update', {
          id: id,
          status: status,
          reject_reason: rejectReason
        }).then(response => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành công',
            text: response.data.message || (status === 1 ? 'Duyệt thành công' : 'Từ chối duyệt thành công'),
            color: status === 1 ? 'success' : 'warning',
            iconPack: 'feather',
            icon: status === 1 ? 'icon-check' : 'icon-alert-triangle'
          });
          this.getData();
        }).catch(err => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text: err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Có lỗi xảy ra',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
      },
      changePage() {
        if (this.pagination.init) {
          this.getData();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getData();
      },
    },
    filters: {
    },
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input{
    width: 360px;
  }
}
th .sort-th, th .vs-table-text{
  display: contents;
}
.multiselect{
  z-index: 999;
}
.td.vs-table--td{
  vertical-align: top;
}
</style>