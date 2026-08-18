<template>
  <div id="system-guide">
    <div class="vx-row flex-col-reverse md:flex-row">
      <!-- Cây thư mục (Sidebar Left) -->
      <div class="vx-col w-full md:w-1/4 mb-base">
        <vs-card>
          <div slot="header">
            <h4><i class="fa fa-list"></i> Mục lục tài liệu</h4>
          </div>
          <ul class="doc-tree text-base">
            <!-- Phần 1: Cấu hình Hệ thống -->
            <li>
              <div class="tree-header font-bold mb-2 text-danger">I. CẤU HÌNH HỆ THỐNG</div>
              <ul class="pl-4 mb-4">
                <li class="tree-item" :class="{ 'active': activeTab === 'config_general' }" @click="activeTab = 'config_general'">
                  <i class="fa fa-cogs mr-2"></i>1. Cấu hình Chung (Admin)
                </li>
                <li class="tree-item" :class="{ 'active': activeTab === 'config_hrm' }" @click="activeTab = 'config_hrm'">
                  <i class="fa fa-user-cog mr-2"></i>2. Cấu hình Nhân sự (HRM)
                </li>
              </ul>
            </li>
            
            <vs-divider />

            <!-- Phần 2: Nghiệp vụ -->
            <li>
              <div class="tree-header font-bold mb-2 text-primary">II. HƯỚNG DẪN NGHIỆP VỤ</div>
              <ul class="pl-4">
                <li class="tree-item" :class="{ 'active': activeTab === 'usage_hrm' }" @click="activeTab = 'usage_hrm'">
                  <i class="fa fa-users mr-2"></i>1. Quản trị Nhân sự (HRM)
                </li>
                <li class="tree-item" :class="{ 'active': activeTab === 'usage_crm' }" @click="activeTab = 'usage_crm'">
                  <i class="fa fa-headset mr-2"></i>2. Quy trình Sales (CRM)
                </li>
                <li class="tree-item" :class="{ 'active': activeTab === 'usage_lms' }" @click="activeTab = 'usage_lms'">
                  <i class="fa fa-graduation-cap mr-2"></i>3. Quản lý Đào tạo (LMS)
                </li>
              </ul>
            </li>
          </ul>
        </vs-card>
      </div>

      <!-- Nội dung tài liệu (Content Right) -->
      <div class="vx-col w-full md:w-3/4 mb-base">
        <vs-card class="doc-content-wrapper">
          
          <!-- Nội dung: Cấu hình Chung -->
          <div v-if="activeTab === 'config_general'" class="p-4 doc-content">
            <h2 class="mb-4 text-danger border-b pb-2"><i class="fa fa-cogs mr-2"></i> Cấu hình Hệ thống Chung</h2>
            <p class="mb-6 text-gray-800">Thiết lập các danh mục cơ sở dùng chung cho hệ thống ERP (ngoại trừ phần Nhân sự). Bạn bắt buộc phải cấu hình các thông tin này trước khi các phòng ban có thể vận hành hệ thống.</p>
            
            <h4 class="text-primary font-bold">1. Cấu hình Trung tâm & Phòng học (LMS)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Hệ thống hỗ trợ quản lý chuỗi nhiều cơ sở (Branch). Để thiết lập, bạn cần làm theo các bước:</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Bước 1 (Tạo Trung tâm):</strong> Truy cập <em>Cấu hình hệ thống > Danh sách trung tâm</em>. Thêm mới các trung tâm với thông tin Tên trung tâm, Địa chỉ, Hotline liên hệ. (Ví dụ: Cơ sở Cầu Giấy, Cơ sở Quận 1).</li>
                <li><strong>Bước 2 (Tạo Phòng học):</strong> Truy cập <em>Cấu hình hệ thống > Danh sách phòng học</em>. Chọn Trung tâm trực thuộc và tạo các phòng học vật lý (Ví dụ: Phòng 101, Phòng 102). <strong>Lưu ý:</strong> Hãy nhập chính xác <em>Sức chứa tối đa</em> của phòng để hệ thống cảnh báo khi Giáo vụ xếp lớp vượt quá số lượng học viên cho phép.</li>
              </ul>
            </div>

            <h4 class="text-primary font-bold">2. Cấu hình Khóa học & Gói phí (CRM / LMS)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Khóa học và Gói phí là xương sống của phễu kinh doanh. Dữ liệu này hiển thị cho cả Sale (khi chốt hợp đồng) và Kế toán (khi thu tiền).</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Danh sách Khóa học:</strong> Truy cập <em>Cấu hình hệ thống > Danh sách khóa học</em>. Khai báo Tên khóa học (Ví dụ: Tiếng Anh Giao tiếp cơ bản), số buổi học tiêu chuẩn, và giáo trình đính kèm.</li>
                <li><strong>Danh sách Gói phí:</strong> Truy cập <em>Cấu hình hệ thống > Danh sách gói phí</em>. Mỗi Khóa học có thể có nhiều Gói phí (Ví dụ: Đóng 3 tháng, Đóng 6 tháng, Gói Combo). Khi tạo gói phí, cần ghi rõ <strong>Mức giá niêm yết</strong>.</li>
                <li><strong>Mã chiết khấu (Discount):</strong> Thiết lập các chương trình khuyến mãi (Giảm 10%, Giảm 500k) tại <em>Danh sách mã chiết khấu</em> để Kế toán có thể áp mã hợp lệ khi lập phiếu thu.</li>
              </ul>
            </div>
          </div>

          <!-- Nội dung: Cấu hình HRM -->
          <div v-if="activeTab === 'config_hrm'" class="p-4 doc-content">
            <h2 class="mb-4 text-primary border-b pb-2"><i class="fa fa-user-cog mr-2"></i> Cấu hình Nhân sự (HRM)</h2>
            <p class="mb-6 text-gray-800">Cấu trúc doanh nghiệp và các chính sách nhân sự phải được định nghĩa trước khi tạo mới Hồ sơ nhân viên để đảm bảo tính nhất quán dữ liệu.</p>
            
            <h4 class="text-primary font-bold">1. Xây dựng Sơ đồ Tổ chức (Phòng ban)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Truy cập <em>HRM > Cơ cấu tổ chức</em>. Khai báo danh sách các Phòng Ban/Khối. Bạn có thể chọn "Phòng ban cha" để tạo cấu trúc hình cây (Ví dụ: Khối Công nghệ là cha, Phòng Backend và Phòng Frontend là con). Sơ đồ này sẽ quyết định luồng duyệt đơn xin nghỉ phép (Nhân viên tạo đơn -> Trưởng phòng ban duyệt).</p>
            </div>

            <h4 class="text-primary font-bold">2. Cấu hình Cấp bậc & Chức danh</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Cấp bậc (Job Level):</strong> Là thang bậc chuyên môn của công ty. Truy cập <em>HRM > Cấp bậc</em> để tạo mới (Ví dụ: Intern, Fresher, Junior, Middle, Senior, Manager).</li>
                <li><strong>Chức danh (Job Title):</strong> Là tên vị trí công việc. Truy cập <em>HRM > Chức danh</em> (Ví dụ: Lập trình viên Backend, Chuyên viên Telesale).</li>
              </ul>
              <p class="mt-2 text-danger"><em>* Mẹo: Việc tách biệt Cấp bậc và Chức danh giúp bạn quản lý lương và lộ trình thăng tiến dễ dàng hơn (Ví dụ: Lập trình viên Backend - nhưng cấp bậc Junior).</em></p>
            </div>

            <h4 class="text-primary font-bold">3. Cấu hình Loại nghỉ phép & Ca làm việc</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Loại nghỉ phép:</strong> Truy cập <em>Nghỉ phép > Loại phép</em>. Hãy định nghĩa chi tiết (Phép năm, Nghỉ ốm, Nghỉ thai sản). Quan trọng nhất là tick chọn loại phép đó có được <strong>Hưởng lương</strong> hay không.</li>
                <li><strong>Ca làm việc:</strong> Truy cập <em>Chấm công > Ca làm việc</em>. Tạo các khung giờ chuẩn. Ví dụ "Ca hành chính" (Bắt đầu lúc 08:00, Kết thúc lúc 17:30). Bạn có thể cấu hình thêm "Thời gian linh động (Grace Period)" cho phép đi muộn 15 phút không bị phạt.</li>
              </ul>
            </div>
          </div>

          <!-- Nội dung: Nghiệp vụ HRM -->
          <div v-if="activeTab === 'usage_hrm'" class="p-4 doc-content">
            <h2 class="mb-4 text-primary border-b pb-2"><i class="fa fa-users mr-2"></i> Hướng dẫn Quản trị Nhân sự (HRM)</h2>
            
            <h4 class="text-primary font-bold">1. Quản lý Hồ sơ Nhân sự & Hợp đồng</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Hồ sơ nhân sự (Profile) là nơi lưu trữ toàn bộ vòng đời của nhân viên tại công ty từ lúc Onboard đến lúc Offboard.</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Tạo mới Hồ sơ:</strong> Truy cập <em>Hồ sơ nhân sự</em> > "Thêm nhân viên". Cần chọn tài khoản User tương ứng để liên kết. Bắt buộc nhập đầy đủ Mã nhân viên, Phòng ban, Cấp bậc, Chức danh hiện tại và Lương cơ bản.</li>
                <li><strong>Lịch sử Công tác:</strong> Khi nhân viên được tăng lương hoặc chuyển bộ phận, Bộ phận HR KHÔNG GHI ĐÈ thông tin cũ. Thay vào đó, mở chi tiết hồ sơ > Tab "Lịch sử công tác" > Bấm "Thêm lịch sử" (Thăng tiến / Chuyển vị trí) với ngày hiệu lực mới. Việc này đặc biệt quan trọng để phần mềm tính lương chính xác theo từng giai đoạn.</li>
                <li><strong>Quản lý Hợp đồng / Tài liệu:</strong> Trong tab "Tài liệu", HR có thể tải lên bản scan PDF Hợp đồng lao động (nhập ngày hết hạn để hệ thống tự động nhắc nhở), CMND/CCCD hoặc Bằng cấp.</li>
              </ul>
            </div>

            <h4 class="text-primary font-bold">2. Quản lý Chấm công & Xếp ca (Rostering)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Luồng tính công của hệ thống được thực hiện bằng cách so sánh <em>Lịch xếp ca</em> và <em>Dữ liệu Check-in/out thực tế</em>.</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Xếp ca (Quản lý/HR):</strong> Truy cập <em>Chấm công > Xếp ca</em>. Vào cuối mỗi tháng, Trưởng bộ phận phải lên lịch cho nhân viên trong tháng tiếp theo. Bạn có thể chọn Nhân viên, chọn Ca làm việc (Ví dụ: Ca hành chính) và gán cho nhiều ngày cùng lúc.</li>
                <li><strong>Dữ liệu Check-in/out:</strong> Hệ thống có API mở để nhận dữ liệu từ Máy chấm công vân tay/FaceID. Quản lý xem tại <em>Dữ liệu chấm công</em>. Nếu phát sinh sai sót (nhân viên quên chấm công), HR có thể chỉnh sửa thủ công (Cần có quyền Admin).</li>
              </ul>
            </div>

            <h4 class="text-primary font-bold">3. Xử lý Đơn từ (Nghỉ phép & Làm thêm giờ)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Tính năng tự động hóa quy trình xin phép - phê duyệt không cần giấy tờ.</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Gửi đơn (Nhân viên):</strong> Truy cập <em>Đơn nghỉ phép</em> hoặc <em>Làm thêm giờ</em>. Bấm Thêm mới. Điền chính xác Khoảng thời gian (Ngày/Giờ), Loại phép và Lý do. Sau khi gửi, đơn sẽ ở trạng thái "Chờ duyệt" và gửi thông báo (Notification) tới Trưởng phòng.</li>
                <li><strong>Phê duyệt (Trưởng phòng/HR):</strong> Truy cập danh sách Đơn từ. Trưởng phòng có thể xem nhanh nội dung và quyết định "Duyệt" hoặc "Từ chối". <strong>Lưu ý:</strong> Khi chọn "Từ chối", hệ thống bắt buộc Trưởng phòng phải gõ lý do từ chối để nhân viên nắm được.</li>
                <li>Khi đơn <em>Phép năm</em> được duyệt hoàn tất, hệ thống tự động trừ đi 1 ngày trong "Quỹ phép năm" của nhân viên đó.</li>
              </ul>
            </div>
          </div>

          <!-- Nội dung: Nghiệp vụ CRM -->
          <div v-if="activeTab === 'usage_crm'" class="p-4 doc-content">
            <h2 class="mb-4 text-success border-b pb-2"><i class="fa fa-headset mr-2"></i> Hướng dẫn Quy trình Sales (CRM)</h2>
            <p class="mb-6 text-gray-800">Module CRM (Customer Relationship Management) giúp số hóa toàn bộ phễu bán hàng (Sales Funnel). Quy trình này đảm bảo không bỏ sót bất kỳ khách hàng tiềm năng nào.</p>

            <h4 class="text-success font-bold">1. Quản lý Data Khách hàng (Leads) & Phân bổ</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Nguồn Data:</strong> Data khách hàng có thể tự động đổ về từ Facebook, Landing Page qua API hoặc Sales có thể tải lên hàng loạt từ file Excel. Khách hàng này gọi chung là "Lead" (Khách hàng tiềm năng chưa mua hàng).</li>
                <li><strong>Phân bổ Lead:</strong> Trưởng nhóm Sales (Team Leader) sử dụng tính năng "Phân bổ" để chia Data cho từng nhân viên Telesale. Mỗi nhân viên chỉ được xem và gọi điện cho Data do mình phụ trách.</li>
              </ul>
            </div>
            
            <h4 class="text-success font-bold">2. Kịch bản Gọi điện & Cập nhật Trạng thái</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Telesale tiến hành gọi điện và cập nhật tiến trình vào hệ thống. Việc cập nhật Trạng thái (Pipeline) chuẩn xác là bắt buộc.</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Call Logs:</strong> Mỗi lần gọi cho khách, Sale truy cập <em>CRM > Gọi điện/Chăm sóc</em>, mở hồ sơ Lead và ghi chú lại nội dung cuộc nói chuyện (Khách bận, Khách hẹn tuần sau...).</li>
                <li><strong>Trạng thái khách hàng:</strong> Khách hàng đi theo luồng trạng thái chuẩn: <em>Data mới -> Đang chăm sóc -> Hẹn lịch Test -> Đã Test -> Suy nghĩ -> Chốt Sale / Fail</em>.</li>
              </ul>
            </div>

            <h4 class="text-success font-bold">3. Chuyển giao Khách hàng (Chốt Sale)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Khi khách hàng đồng ý đăng ký học, Telesale thực hiện hành động cực kỳ quan trọng là "Chốt Sale":</p>
              <ul class="list-disc ml-6 mt-2">
                <li>Đổi trạng thái Lead sang "Chốt Sale".</li>
                <li>Hệ thống yêu cầu Telesale điền thông tin Khóa học và Gói phí mà khách đã chọn.</li>
                <li><strong>Tự động hóa:</strong> Sau khi hoàn tất, hồ sơ này sẽ được chuyển đổi từ một "Lead" thành "Học viên" bên phân hệ Đào tạo (LMS), đồng thời tự động tạo ra một khoản Công nợ bên phân hệ Kế toán để nhắc Kế toán thu tiền.</li>
              </ul>
            </div>
          </div>

          <!-- Nội dung: Nghiệp vụ LMS -->
          <div v-if="activeTab === 'usage_lms'" class="p-4 doc-content">
            <h2 class="mb-4 text-warning border-b pb-2"><i class="fa fa-graduation-cap mr-2"></i> Hướng dẫn Quản lý Đào tạo (LMS)</h2>
            <p class="mb-6 text-gray-800">Module LMS (Learning Management System) được sử dụng bởi Giáo vụ, Giáo viên và Kế toán để vận hành các lớp học sau khi Sales đã chốt hợp đồng thành công.</p>
            
            <h4 class="text-warning font-bold">1. Quản lý Học viên & Xếp lớp (Giáo vụ)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Học viên chờ xếp lớp:</strong> Học viên do Sales chốt sẽ hiển thị trong danh sách "Chờ xếp lớp".</li>
                <li><strong>Khởi tạo Lớp học mới:</strong> Truy cập <em>LMS > Lớp học</em> để mở lớp mới. Giáo vụ cần chỉ định Khóa học, Trung tâm, Phòng học, Giáo viên đứng lớp, và Khung giờ học trong tuần (Ví dụ: Thứ 2-4-6 ca Tối 19h).</li>
                <li><strong>Xếp lớp:</strong> Giáo vụ tick chọn các học viên trong danh sách chờ và đưa vào Lớp học vừa tạo. Hệ thống sẽ ngay lập tức tự động sinh ra Thời khóa biểu (TKB) chi tiết cho tất cả các buổi học dựa trên khung giờ đã cài đặt.</li>
              </ul>
            </div>

            <h4 class="text-warning font-bold">2. Sổ đầu bài điện tử & Điểm danh (Giáo viên)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Giáo viên (hoặc Trợ giảng) sẽ là người trực tiếp vận hành tính năng này trước mỗi buổi học.</p>
              <ul class="list-disc ml-6 mt-2">
                <li>Giáo viên mở lịch học của ngày hôm nay, truy cập vào <strong>Sổ đầu bài</strong> của lớp.</li>
                <li>Thực hiện điểm danh nhanh chóng với các trạng thái: <em>Có mặt, Vắng có phép, Vắng không phép, Đi muộn</em>.</li>
                <li>Viết nhận xét chi tiết cho từng học viên (nếu cần) và Nhận xét chung cho cả buổi học. Dữ liệu này có thể được đồng bộ để gửi Báo cáo học tập qua Email/Zalo cho Phụ huynh vào cuối khóa.</li>
              </ul>
            </div>

            <h4 class="text-warning font-bold">3. Thu học phí & Quản lý Công nợ (Kế toán)</h4>
            <div class="mb-6 p-4 bg-gray-100 rounded">
              <p>Phân hệ tài chính gắn liền chặt chẽ với LMS để đảm bảo tính thông suốt dòng tiền.</p>
              <ul class="list-disc ml-6 mt-2">
                <li><strong>Thu tiền:</strong> Kế toán truy cập <em>Kế toán > Thu học phí</em>. Tra cứu mã Học viên để lập Phiếu thu. Kế toán nhập số tiền khách đóng thực tế, hình thức thanh toán (Tiền mặt/Chuyển khoản).</li>
                <li><strong>Áp dụng Mã giảm giá:</strong> Nếu khách hàng có mã ưu đãi, Kế toán điền Mã chiết khấu (đã cấu hình) để hệ thống trừ tiền tự động.</li>
                <li><strong>Công nợ:</strong> Nếu khách hàng đóng tiền làm nhiều đợt (Trả góp), hệ thống sẽ lưu trữ Số tiền còn nợ. Kế toán có thể lọc danh sách "Học viên nợ học phí" để chủ động nhắc nhở khách hàng trước kỳ học tiếp theo.</li>
              </ul>
            </div>
          </div>

        </vs-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SystemDocs',
  data() {
    return {
      activeTab: 'config_general'
    }
  }
}
</script>

<style scoped>
/* Thay đổi font-size lớn hơn và dễ đọc hơn */
.doc-content-wrapper {
  min-height: 500px;
}
.doc-content {
  font-size: 1.05rem; /* ~16.8px */
  line-height: 1.75;
  color: #2d3748; /* Text-gray-800 */
}
.doc-content h2 {
  font-size: 1.75rem;
  font-weight: 700;
}
.doc-content h4 {
  font-size: 1.25rem;
  margin-top: 1.5rem;
}
.doc-content p {
  margin-bottom: 1rem;
}
.doc-content ul li {
  margin-bottom: 0.75rem;
}

/* Sidebar Tree CSS */
.doc-tree {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 1.05rem;
}
.tree-item {
  cursor: pointer;
  padding: 12px 16px;
  margin-bottom: 6px;
  border-radius: 8px;
  transition: all 0.2s ease-in-out;
  color: #4a5568;
  display: flex;
  align-items: center;
}
.tree-item i {
  width: 24px;
  text-align: center;
  font-size: 1.1rem;
}
.tree-item:hover {
  background: rgba(0, 0, 0, 0.05);
}
.tree-item.active {
  background: rgba(var(--vs-primary), 0.1);
  color: rgba(var(--vs-primary), 1);
  font-weight: 700;
}
</style>
