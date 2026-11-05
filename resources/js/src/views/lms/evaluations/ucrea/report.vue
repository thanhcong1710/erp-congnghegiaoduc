<template>
  <div class="ucrea-report-view space-y-6">
    <!-- Action buttons (Hidden on print) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 print:hidden">
      <div>
        <div class="flex items-center gap-3 mb-1">
          <router-link to="/lms/evaluations/ucrea" class="text-sm text-gray-500 hover:text-primary transition flex items-center">
            <i class="feather icon-arrow-left mr-1"></i> Quay lại danh sách
          </router-link>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Báo Cáo Đánh Giá Năng Lực UCREA</h3>
        <p class="text-xs text-gray-500 mt-1" v-if="result">
          {{ result.general.test_nm }} | Học sinh: <strong class="text-primary">{{ result.general.stu_nm }}</strong>
        </p>
      </div>
      <div v-if="result" class="flex items-center gap-2">
        <vs-button color="primary" icon="print" @click="exportPdf">In Báo Cáo (PDF)</vs-button>
        <router-link :to="`/lms/evaluations/ucrea/form/${result.general.id}`">
          <vs-button color="warning" icon="edit">Sửa Điểm</vs-button>
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 text-gray-500 print:hidden">
      <i class="fa fa-spinner fa-spin text-3xl mb-3 text-primary"></i>
      <p class="text-sm">Đang tải báo cáo đánh giá...</p>
    </div>

    <!-- MAIN REPORT (Screen and Print) -->
    <div v-else-if="result" id="pdf-content" class="bg-white text-black p-6 mx-auto w-full max-w-[900px] shadow-md print:shadow-none print:p-0 print:m-0 text-[12px] leading-relaxed font-sans border border-gray-200 print:border-none">
      
      <!-- PAGE 1 -->
      <div class="page-container bg-white pb-8">
        
        <!-- Header (Dark blue background with white pill student info) -->
        <div class="bg-[#1b3664] text-white px-6 py-4 flex flex-col md:flex-row md:items-center justify-between rounded-t-lg print:rounded-none">
          <div class="flex items-center gap-4">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="h-10 w-auto bg-white/10 rounded px-1.5 py-0.5 object-contain" />
            <div class="text-left border-l border-white/20 pl-4">
               <p class="text-[9px] uppercase text-white/70 font-semibold mb-0 leading-none">Ucrea Assessment</p>
               <h2 class="text-[16px] font-bold text-[#f5c518] uppercase tracking-wide leading-none mt-1">{{ reportData.name || 'ĐÁNH GIÁ ĐẦU KỲ' }}</h2>
            </div>
          </div>
          <!-- White pill student info -->
          <div class="bg-white text-[#1b3664] font-bold text-[11px] px-4 py-1.5 rounded-full mt-2 md:mt-0 shadow-sm flex items-center gap-2">
             <span>{{ result.general.stu_nm }}</span>
             <span>|</span>
             <span>{{ result.general.gender_nm || 'Nam' }}</span>
             <span>|</span>
             <span>{{ result.general.age_nm || '3 Tuổi' }}</span>
             <span>|</span>
             <span>{{ result.general.class_nm || '-' }}</span>
          </div>
        </div>

        <div class="mt-6 px-2">
          <!-- 1. Lĩnh vực đánh giá -->
          <h2 class="text-base font-extrabold text-[#1b3664] mb-3 border-b-2 border-[#1b3664] pb-1">Lĩnh vực đánh giá</h2>
          <div class="grid grid-cols-12 gap-4 mb-8">
            <div class="col-span-7">
              <table class="w-full border-collapse border border-gray-200">
                <tbody>
                  <tr class="border-b border-gray-200">
                    <td class="p-2.5 text-center font-bold text-gray-700 w-[25%] bg-gray-50">Tư duy cơ bản</td>
                    <td class="p-2.5 text-gray-600 text-[11px]">Đánh giá sự chú ý và tập trung, quan sát và ghi nhớ, đó là những nhân tố cơ bản cần thiết cho sự phát triển tư duy.</td>
                  </tr>
                  <tr class="border-b border-gray-200">
                    <td class="p-2.5 text-center font-bold text-gray-700 bg-gray-50">Tư duy toán học</td>
                    <td class="p-2.5 text-gray-600 text-[11px]">Đánh giá năng lực về số và phép toán, không gian, đo lường, kiểu mẫu, thống kê; đó là những nhân tố cơ bản cần thiết cho sự phát triển tư duy.</td>
                  </tr>
                  <tr class="border-b border-gray-200">
                    <td class="p-2.5 text-center font-bold text-gray-700 bg-gray-50">Tư duy logic</td>
                    <td class="p-2.5 text-gray-600 text-[11px]">Đánh giá sự hiểu biết, áp dụng, phân tích và tổng hợp; đó là các thành phần của kỹ năng tư duy logic, kỹ năng cốt lõi để phát triển tư duy.</td>
                  </tr>
                  <tr>
                    <td class="p-2.5 text-center font-bold text-gray-700 bg-gray-50">Tư duy sáng tạo</td>
                    <td class="p-2.5 text-gray-600 text-[11px]">Đánh giá sự lưu loát, linh hoạt, độc đáo và tinh tế, đó là những kỹ năng cần thiết cho tư duy đa dạng và sâu sắc.</td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="col-span-5">
              <table class="w-full border-collapse border border-gray-200 text-center text-[11px]">
                <thead>
                  <tr class="bg-[#e86036] text-white">
                    <th colspan="3" class="p-2 font-bold text-xs uppercase">Tiêu chuẩn và cấp độ</th>
                  </tr>
                  <tr class="bg-gray-100 text-gray-700 border-b border-gray-200">
                    <th class="p-1.5 font-semibold w-1/3 border-r border-gray-200">Tiêu chuẩn</th>
                    <th class="p-1.5 font-semibold w-1/3 border-r border-gray-200">Cấp độ</th>
                    <th class="p-1.5 font-semibold w-1/3">Điểm</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200"><td class="p-1.5 border-r border-gray-200 font-medium text-gray-700">Vượt trội</td><td class="p-1.5 border-r border-gray-200 font-bold text-gray-800">S</td><td class="p-1.5 text-gray-500">91~100</td></tr>
                  <tr class="border-b border-gray-200"><td class="p-1.5 border-r border-gray-200 font-medium text-gray-700">Rất tốt</td><td class="p-1.5 border-r border-gray-200 font-bold text-gray-800">A</td><td class="p-1.5 text-gray-500">71~90</td></tr>
                  <tr class="border-b border-gray-200"><td class="p-1.5 border-r border-gray-200 font-medium text-gray-700">Trung bình</td><td class="p-1.5 border-r border-gray-200 font-bold text-gray-800">B</td><td class="p-1.5 text-gray-500">41~70</td></tr>
                  <tr><td class="p-1.5 border-r border-gray-200 font-medium text-gray-700">Yếu</td><td class="p-1.5 border-r border-gray-200 font-bold text-gray-800">C</td><td class="p-1.5 text-gray-500">0~40</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 2. Kết quả đánh giá -->
          <div class="flex flex-col md:flex-row md:items-center justify-between border-b-2 border-[#1b3664] pb-1 mb-4 print:border-b-2">
            <h2 class="text-base font-extrabold text-[#1b3664] mb-0 border-none pb-0">Kết quả đánh giá</h2>
            
            <!-- Checkboxes for related tests -->
            <div class="flex items-center gap-4 text-[11px] font-bold text-gray-700 mt-2 md:mt-0 print:hidden" v-if="allTests.length">
              <label v-for="t in allTests" :key="t.test_cd" 
                     class="flex items-center gap-1.5" 
                     :class="{ 'cursor-pointer': t.hasData && !t.isCurrent, 'opacity-40 cursor-not-allowed': !t.hasData, 'cursor-not-allowed': t.isCurrent }">
                <input type="checkbox" 
                       :value="t.dataId" 
                       v-model="selectedTestIds" 
                       @change="initCharts" 
                       :disabled="!t.hasData || t.isCurrent"
                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-70">
                <span class="uppercase">{{ t.name }}</span>
              </label>
            </div>
          </div>
          
          <!-- Tư duy cơ bản -->
          <div class="grid-section grid grid-cols-12 gap-4 mb-8 items-center">
            <!-- Chart Left -->
            <div class="chart-container col-span-5 relative h-[250px] w-full border border-gray-100 rounded-lg p-1">
               <div ref="chart1" class="w-full h-full"></div>
            </div>
            <!-- Table Right -->
            <div class="col-span-7">
              <table class="w-full border-collapse border border-[#0050a0] text-center text-[11px] shadow-sm">
                <thead>
                  <tr class="bg-[#0050a0] text-white">
                    <th class="font-bold p-2 border-r border-white/20 w-[25%] text-left pl-3">Lĩnh vực chi tiết</th>
                    <th class="font-bold p-2 border-r border-white/20 text-left pl-3">Nội dung chi tiết</th>
                    <th class="font-bold p-2 w-[15%]">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Năng lực chú ý</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng tập trung quan sát để giải quyết vấn đề.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Năng lực chú ý') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Năng lực quan sát</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng quan sát tỉ mỉ một sự vật nào đó.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Năng lực quan sát') }}</td>
                  </tr>
                  <tr class="bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Năng lực ghi nhớ</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng ghi nhớ một thông tin mới về sự vật, sự việc.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Năng lực ghi nhớ') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tư duy toán học -->
          <div class="grid-section grid grid-cols-12 gap-4 mb-6 items-center">
            <!-- Chart Left -->
            <div class="chart-container col-span-5 relative h-[250px] w-full border border-gray-100 rounded-lg p-1">
               <div ref="chart2" class="w-full h-full"></div>
            </div>
            <!-- Table Right -->
            <div class="col-span-7">
              <table class="w-full border-collapse border border-[#0050a0] text-center text-[11px] shadow-sm">
                <thead>
                  <tr class="bg-[#0050a0] text-white">
                    <th class="font-bold p-2 border-r border-white/20 w-[25%] text-left pl-3">Lĩnh vực chi tiết</th>
                    <th class="font-bold p-2 border-r border-white/20 text-left pl-3">Nội dung chi tiết</th>
                    <th class="font-bold p-2 w-[15%]">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Số và tính toán</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng hiểu những khái niệm về tính toán cơ bản, hiểu được ý nghĩa của toán học trong đời sống sinh hoạt hàng ngày.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Số và tính toán') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Hình học không gian</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng nhận biết vị trí và hướng trong không gian và hiểu biết về hình dạng.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Hình học không gian') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Đo lường</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng thể hiện số lượng bằng cách sử dụng số và so sánh các đồ vật với nhau.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Đo lường') }}</td>
                  </tr>
                  <tr class="bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Kiểu mẫu</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng nhận diện và dự đoán mối quan hệ mang tính quy luật của đồ vật.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Kiểu mẫu') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
        </div>
      </div>

      <!-- PAGE BREAK -->
      <div class="page-break-element"></div>

      <!-- PAGE 2 -->
      <div class="page-container bg-white pb-8 pt-6">
        <div class="mt-4 px-2">
          <!-- Tư duy logic -->
          <div class="grid-section grid grid-cols-12 gap-4 mb-8 items-center">
            <!-- Chart Left -->
            <div class="chart-container col-span-5 relative h-[250px] w-full border border-gray-100 rounded-lg p-1">
               <div ref="chart3" class="w-full h-full"></div>
            </div>
            <!-- Table Right -->
            <div class="col-span-7">
              <table class="w-full border-collapse border border-[#0050a0] text-center text-[11px] shadow-sm">
                <thead>
                  <tr class="bg-[#0050a0] text-white">
                    <th class="font-bold p-2 border-r border-white/20 w-[25%] text-left pl-3">Lĩnh vực chi tiết</th>
                    <th class="font-bold p-2 border-r border-white/20 text-left pl-3">Nội dung chi tiết</th>
                    <th class="font-bold p-2 w-[15%]">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Hiểu biết</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng phân tích những dự kiện liên quan và hiểu về vấn đề cần giải quyết.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Hiểu biết') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Ứng dụng</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng vận dụng linh hoạt những vấn đề đã học vào thực hành thực tế.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Ứng dụng') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Phân tích</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Năng lực so sánh các thuộc tính và khái quát hóa vấn đề.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Phân tích') }}</td>
                  </tr>
                  <tr v-if="!isL1L2" class="bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tổng hợp</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng phân tích tình huống và giải quyết toàn diện vấn đề.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Tổng hợp') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tư duy sáng tạo -->
          <div class="grid-section grid grid-cols-12 gap-4 mb-10 items-center">
            <!-- Chart Left -->
            <div class="chart-container col-span-5 relative h-[250px] w-full border border-gray-100 rounded-lg p-1">
               <div ref="chart4" class="w-full h-full"></div>
            </div>
            <!-- Table Right -->
            <div class="col-span-7">
              <table class="w-full border-collapse border border-[#0050a0] text-center text-[11px] shadow-sm">
                <thead>
                  <tr class="bg-[#0050a0] text-white">
                    <th class="font-bold p-2 border-r border-white/20 w-[25%] text-left pl-3">Lĩnh vực chi tiết</th>
                    <th class="font-bold p-2 border-r border-white/20 text-left pl-3">Nội dung chi tiết</th>
                    <th class="font-bold p-2 w-[15%]">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Sự trôi chảy</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng thể hiện nhiều ý tưởng mới lạ.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Sự trôi chảy') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tính linh hoạt</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng tìm và giải quyết vấn đề và những ý tưởng đa dạng bằng cách vận dụng một đối tượng thống nhất.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Tính linh hoạt') }}</td>
                  </tr>
                  <tr class="border-b border-gray-200 bg-white">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tính độc đáo</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng đưa ra một ý tưởng độc đáo và mới lạ hoặc hoàn toàn khác biệt.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Tính độc đáo') }}</td>
                  </tr>
                  <tr class="bg-[#f4f7fa]">
                    <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tính chính xác</td>
                    <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">Khả năng bổ sung thêm những điểm chi tiết của ý tưởng có tính phức tạp cao.</td>
                    <td class="p-2.5 font-bold text-gray-800">{{ getGrade('Tính chính xác') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 3. Đánh giá tổng quát -->
          <div class="overall-section">
            <h2 class="text-base font-extrabold text-[#1b3664] mb-4 border-b-2 border-[#1b3664] pb-1">Đánh giá tổng quát</h2>
            <div class="grid-section grid grid-cols-12 gap-4 items-center">
              <!-- Chart Left -->
              <div class="overall-chart-container col-span-5 relative h-[270px] w-full border border-gray-100 rounded-lg p-1">
                 <div ref="chartOverall" class="w-full h-full"></div>
              </div>
              <!-- Table Right -->
              <div class="col-span-7">
                <table class="w-full border-collapse border border-[#e86036] text-center text-[11px] shadow-sm">
                  <thead>
                    <tr class="bg-[#e86036] text-white">
                      <th class="font-bold p-2 border-r border-white/20 w-[20%] text-left pl-3">Lĩnh vực</th>
                      <th class="font-bold p-2 border-r border-white/20 text-left pl-3">Nội dung</th>
                      <th class="font-bold p-2 w-[15%]">Trung bình</th>
                      <th class="font-bold p-2 w-[20%]">Bình quân của tất cả HS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b border-gray-200 bg-[#fef6ef]">
                      <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tư duy cơ bản</td>
                      <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">
                        Tư duy cơ bản của học sinh {{ result.general.stu_nm }} ở mức độ <span class="text-[#dc3545] font-bold">{{ getGradeLabel(getGrade('Tư duy cơ bản')) }}</span>.
                      </td>
                      <td class="border-r border-gray-200 p-2.5 font-bold text-gray-800">{{ getGrade('Tư duy cơ bản') }}</td>
                      <td class="p-2.5 text-gray-500">B</td>
                    </tr>
                    <tr class="border-b border-gray-200 bg-white">
                      <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tư duy toán học</td>
                      <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">
                        Tư duy toán học của học sinh {{ result.general.stu_nm }} ở mức độ <span class="text-[#dc3545] font-bold">{{ getGradeLabel(getGrade('Tư duy toán học')) }}</span>.
                      </td>
                      <td class="border-r border-gray-200 p-2.5 font-bold text-gray-800">{{ getGrade('Tư duy toán học') }}</td>
                      <td class="p-2.5 text-gray-500">B</td>
                    </tr>
                    <tr class="border-b border-gray-200 bg-[#fef6ef]">
                      <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tư duy logic</td>
                      <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">
                        Tư duy logic của học sinh {{ result.general.stu_nm }} ở mức độ <span class="text-[#dc3545] font-bold">{{ getGradeLabel(getGrade('Tư duy logic')) }}</span>.
                      </td>
                      <td class="border-r border-gray-200 p-2.5 font-bold text-gray-800">{{ getGrade('Tư duy logic') }}</td>
                      <td class="p-2.5 text-gray-500">B</td>
                    </tr>
                    <tr class="bg-white">
                      <td class="border-r border-gray-200 p-2.5 text-left font-bold text-gray-700 pl-3">Tư duy sáng tạo</td>
                      <td class="border-r border-gray-200 p-2.5 text-left text-gray-600">
                        Tư duy sáng tạo của học sinh {{ result.general.stu_nm }} ở mức độ <span class="text-[#dc3545] font-bold">{{ getGradeLabel(getGrade('Tư duy sáng tạo')) }}</span>.
                      </td>
                      <td class="border-r border-gray-200 p-2.5 font-bold text-gray-800">{{ getGrade('Tư duy sáng tạo') }}</td>
                      <td class="p-2.5 text-gray-500">B</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import * as echarts from 'echarts'

export default {
  name: 'UcreaReportView',
  data() {
    return {
      result: null,
      loading: true,
      charts: [],
      selectedTestIds: []
    }
  },
  computed: {
    isL1L2() {
      return this.result && ['L1', 'L2'].includes(this.result.general.level_cd)
    },
    availableTests() {
      if (!this.result || !this.result.related_results) return []
      return this.result.related_results.map(r => {
        let rd = {}
        try { rd = typeof r.report_data === 'string' ? JSON.parse(r.report_data) : r.report_data } catch(e) {}
        return {
          id: r.id,
          test_cd: r.test_cd,
          name: (rd && rd.name) ? rd.name : r.test_nm,
          reportData: rd || {}
        }
      }).sort((a,b) => {
        return (a.test_cd || '').localeCompare(b.test_cd || '')
      })
    },
    allTests() {
      const types = [
        { test_cd: 'TT001', name: 'Đầu kỳ' },
        { test_cd: 'TT002', name: 'Giữa kỳ' },
        { test_cd: 'TT003', name: 'Cuối kỳ' }
      ]

      return types.map(type => {
        const found = this.availableTests.find(t => t.test_cd === type.test_cd)
        return {
          test_cd: type.test_cd,
          name: type.name,
          hasData: !!found,
          dataId: found ? found.id : null,
          isCurrent: found && this.result && found.id == this.result.general.id
        }
      })
    },
    reportData() {
      if (!this.result || !this.result.general.report_data) return {}
      try {
        return typeof this.result.general.report_data === 'string' ? JSON.parse(this.result.general.report_data) : this.result.general.report_data
      } catch(e) {
        return {}
      }
    },
    parsedSkillsGrade() {
      if (!this.result || !this.result.general.skills_grade) return []
      try {
        return typeof this.result.general.skills_grade === 'string' ? JSON.parse(this.result.general.skills_grade) : this.result.general.skills_grade
      } catch(e) {
        return []
      }
    }
  },
  created() {
    this.fetchResult()
  },
  beforeDestroy() {
    this.charts.forEach(c => {
      if (c && !c.isDisposed()) c.dispose()
    })
  },
  methods: {
    fetchResult() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/ucrea/results/${id}`).then(response => {
        this.loading = false
        if (response && response.data && response.data.status === 'success') {
          this.result = response.data.data
          this.selectedTestIds = this.availableTests.map(t => t.id)
          this.$nextTick(() => {
            this.initCharts()
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    getGrade(skillName) {
       const found = this.parsedSkillsGrade.find(s => s.skill === skillName)
       if (found) {
           return found.grade === 'L' ? 'C' : found.grade
       }
       return 'B'
    },
    getGradeLabel(grade) {
      const mapping = {
        'S': 'Vượt trội',
        'A': 'Rất tốt',
        'B': 'Trung bình',
        'C': 'Yếu',
        'L': 'Yếu'
      }
      return mapping[grade] || grade || 'Trung bình'
    },
    exportPdf() {
      window.print()
    },
    initCharts() {
      const selectedData = this.availableTests.filter(t => this.selectedTestIds.includes(t.id))
      if (selectedData.length === 0) return

      const getColor = (test_cd) => {
          if (test_cd === 'TT001') return '#cccccc' // Đầu kỳ
          if (test_cd === 'TT002') return '#003366' // Giữa kỳ
          if (test_cd === 'TT003') return '#8cc63f' // Cuối kỳ
          return '#dc3545'
      }

      const legendData = selectedData.map(t => t.name)
      const legendTopRight = { top: 0, right: 0, data: legendData, itemGap: 5, itemWidth: 15, itemHeight: 10, textStyle: { fontSize: 10, color: '#666' } }

      const commonOpts = {
        grid: { top: 30, bottom: 25, left: 25, right: 5 },
        tooltip: { trigger: 'axis' },
        yAxis: { type: 'value', max: 100, axisLabel: { fontSize: 10 }, splitLine: { lineStyle: { color: '#eee' } } },
        xAxis: { type: 'category', axisLabel: { interval: 0, fontSize: 9, width: 50, overflow: 'break' }, axisTick: { show: false } },
        animation: false
      }

      // 1. Tư duy cơ bản
      if (!this.$refs.chart1) return
      const c1 = echarts.getInstanceByDom(this.$refs.chart1) || echarts.init(this.$refs.chart1)
      c1.setOption({
        ...commonOpts,
        title: { text: 'Tư duy cơ bản', left: 'center', textStyle: { fontSize: 13, color: '#333', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: ['Năng lực\nchú ý', 'Năng lực\nquan sát', 'Năng lực\nghi nhớ'] },
        series: selectedData.map(t => ({
          name: t.name,
          type: 'bar',
          data: [t.reportData.sk11, t.reportData.sk12, t.reportData.sk13],
          barMaxWidth: 20,
          itemStyle: { color: getColor(t.test_cd) }
        }))
      }, true)

      // 2. Tư duy toán học
      let mathData = ['Số và\ntính toán', 'Hình học\nkhông gian', 'Đo lường', 'Kiểu mẫu']
      const c2 = echarts.getInstanceByDom(this.$refs.chart2) || echarts.init(this.$refs.chart2)
      c2.setOption({
        ...commonOpts,
        title: { text: 'Tư duy toán học', left: 'center', textStyle: { fontSize: 13, color: '#333', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: mathData },
        series: selectedData.map(t => {
          let data = [t.reportData.kn11, t.reportData.kn12, t.reportData.kn13, t.reportData.kn14]
          return {
            name: t.name,
            type: 'bar',
            data: data,
            barMaxWidth: 20,
            itemStyle: { color: getColor(t.test_cd) }
          }
        })
      }, true)

      // 3. Tư duy logic
      let logicData = ['Hiểu biết', 'Ứng dụng', 'Phân tích', 'Tổng hợp']
      if (this.isL1L2) {
          logicData = ['Hiểu biết', 'Ứng dụng', 'Phân tích']
      }
      const c3 = echarts.getInstanceByDom(this.$refs.chart3) || echarts.init(this.$refs.chart3)
      c3.setOption({
        ...commonOpts,
        title: { text: 'Tư duy logic', left: 'center', textStyle: { fontSize: 13, color: '#333', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: logicData },
        series: selectedData.map(t => {
          let data = [t.reportData.sk21, t.reportData.sk22, t.reportData.sk23, t.reportData.sk24]
          if (this.isL1L2) {
             data = [t.reportData.sk21, t.reportData.sk22, t.reportData.sk23]
          }
          return {
            name: t.name,
            type: 'bar',
            data: data,
            barMaxWidth: 20,
            itemStyle: { color: getColor(t.test_cd) }
          }
        })
      }, true)

      // 4. Tư duy sáng tạo
      const c4 = echarts.getInstanceByDom(this.$refs.chart4) || echarts.init(this.$refs.chart4)
      c4.setOption({
        ...commonOpts,
        title: { text: 'Tư duy sáng tạo', left: 'center', textStyle: { fontSize: 13, color: '#333', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: ['Sự\ntrôi chảy', 'Tính\nlinh hoạt', 'Tính\nđộc đáo', 'Tính\nchính xác'] },
        series: selectedData.map(t => ({
          name: t.name,
          type: 'bar',
          data: [t.reportData.sk31, t.reportData.sk32, t.reportData.sk33, t.reportData.sk34],
          barMaxWidth: 20,
          itemStyle: { color: getColor(t.test_cd) }
        }))
      }, true)

      // 5. Đánh giá tổng quát
      const cOverall = echarts.getInstanceByDom(this.$refs.chartOverall) || echarts.init(this.$refs.chartOverall)
      
      const overallSeries = selectedData.map(t => {
        const rd = t.reportData
        const avg1 = Math.round((Number(rd.sk11 || 0) + Number(rd.sk12 || 0) + Number(rd.sk13 || 0)) / 3)
        const avg2 = Math.round((Number(rd.kn11 || 0) + Number(rd.kn12 || 0) + Number(rd.kn13 || 0) + Number(rd.kn14 || 0)) / 4)
        const avg3 = this.isL1L2 ?
            Math.round((Number(rd.sk21 || 0) + Number(rd.sk22 || 0) + Number(rd.sk23 || 0)) / 3) :
            Math.round((Number(rd.sk21 || 0) + Number(rd.sk22 || 0) + Number(rd.sk23 || 0) + Number(rd.sk24 || 0)) / 4)
        const avg4 = Math.round((Number(rd.sk31 || 0) + Number(rd.sk32 || 0) + Number(rd.sk33 || 0) + Number(rd.sk34 || 0)) / 4)
        return {
          name: t.name,
          type: 'bar',
          data: [avg1, avg2, avg3, avg4],
          barMaxWidth: 20,
          itemStyle: { color: getColor(t.test_cd) }
        }
      })
      
      // Keep average line/bar
      overallSeries.push({
         name: 'Bình quân tất cả HS', 
         type: 'bar', 
         data: [66, 71, 69, 61], 
         barMaxWidth: 20, 
         itemStyle: { color: '#f39c12' }
      })

      cOverall.setOption({
        ...commonOpts,
        title: { text: 'Bình quân tổng quát', left: 'center', textStyle: { fontSize: 13, color: '#333', fontWeight: 'bold' } },
        legend: { top: 0, right: 0, data: [...legendData, 'Bình quân tất cả HS'], itemGap: 5, itemWidth: 15, itemHeight: 10, textStyle: { fontSize: 10, color: '#666' } },
        xAxis: { ...commonOpts.xAxis, data: ['Tư duy\ncơ bản', 'Tư duy\ntoán học', 'Tư duy\nlogic', 'Tư duy\nsáng tạo'] },
        series: overallSeries
      }, true)

      this.charts = [c1, c2, c3, c4, cOverall]
    }
  }
}
</script>

<style scoped>
@media print {
  header, aside, nav, .print\:hidden, .vs-navbar, .the-navbar {
    display: none !important;
  }
  body, #app, .content-area__content {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  #pdf-content {
    width: 100% !important;
    max-width: 100% !important;
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  .page-container {
    page-break-after: always;
    break-after: page;
  }
  .page-break-element {
    page-break-before: always;
    break-before: page;
    height: 0;
  }
}
.page-break-element {
  height: 20px;
  background-color: transparent;
}
</style>
