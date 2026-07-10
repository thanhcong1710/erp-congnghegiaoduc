<template>
  <div id="hr-approvals">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-800">Quy trình phê duyệt</h2>
      <p class="text-slate-500">Số hoá đơn từ nghỉ phép, công tác, đề xuất tạm ứng</p>
    </div>

    <div class="vx-row">
      <div class="vx-col w-full mb-base">
        <vx-card>
          <vs-tabs>
            <vs-tab label="Chờ tôi duyệt" icon-pack="feather" icon="icon-clock">
              <div class="mt-4">
                <vs-table :data="pendingApprovals" stripe>
                  <template slot="thead">
                    <vs-th>Người đề xuất</vs-th>
                    <vs-th>Loại đơn</vs-th>
                    <vs-th>Nội dung</vs-th>
                    <vs-th>Ngày tạo</vs-th>
                    <vs-th>Trạng thái</vs-th>
                    <vs-th>Thao tác</vs-th>
                  </template>
                  <template slot-scope="{data}">
                    <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                      <vs-td class="font-bold">{{ tr.name }}</vs-td>
                      <vs-td>
                        <vs-chip color="primary">{{ tr.type }}</vs-chip>
                      </vs-td>
                      <vs-td>{{ tr.content }}</vs-td>
                      <vs-td>{{ tr.date }}</vs-td>
                      <vs-td>
                        <span class="text-warning font-medium flex items-center">
                          <feather-icon icon="ClockIcon" svgClasses="w-4 h-4 mr-1"></feather-icon> Chờ duyệt
                        </span>
                      </vs-td>
                      <vs-td>
                        <div class="flex gap-2">
                          <vs-button size="small" color="success" icon-pack="feather" icon="icon-check">Duyệt</vs-button>
                          <vs-button size="small" color="danger" type="border" icon-pack="feather" icon="icon-x">Từ chối</vs-button>
                        </div>
                      </vs-td>
                    </vs-tr>
                  </template>
                </vs-table>
              </div>
            </vs-tab>
            
            <vs-tab label="Đơn từ của tôi" icon-pack="feather" icon="icon-file-text">
              <div class="mt-4 p-4 bg-slate-50 rounded text-center">
                <p>Danh sách các đơn đề xuất bạn đã tạo.</p>
                <vs-button type="filled" icon-pack="feather" icon="icon-plus" class="mt-4">Tạo đơn đề xuất mới</vs-button>
              </div>
            </vs-tab>
            
            <vs-tab label="Lịch sử duyệt" icon-pack="feather" icon="icon-archive">
              <div class="mt-4 text-center text-slate-500 py-10">
                Không có dữ liệu
              </div>
            </vs-tab>
          </vs-tabs>
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pendingApprovals: [
        { name: 'Lê Văn C', type: 'Xin nghỉ phép', content: 'Nghỉ ốm 1 ngày (10/08/2026)', date: '09/08/2026' },
        { name: 'Phạm Thị D', type: 'Đề xuất tạm ứng', content: 'Tạm ứng 2,000,000đ mua văn phòng phẩm', date: '08/08/2026' },
        { name: 'Nguyễn Văn A', type: 'Đăng ký công tác', content: 'Đi công tác chi nhánh HCM từ 15-20/08', date: '07/08/2026' },
      ]
    }
  }
}
</script>
