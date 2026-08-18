<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.name"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Trung tâm <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <vue-select
                    v-if="!agreement.is_sale_role"
                    label="label"
                    placeholder="Chọn EC"
                    :options="html.ec.list"
                    v-model="html.ec.item"
                    :searchable="true"
                    @input="saveEC"
                ></vue-select>
              <input
                v-else
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.ec_leader_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Điểm đầu vào TOEIC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                placeholder="Nhập điểm TOEIC"
                v-model="agreement.point_toeic"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin đăng ký học</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label >Gói học phí</label>
              <vue-select
                    label="label"
                    placeholder="Chọn gói học phí"
                    :options="html.tuition_fee.list"
                    v-model="html.tuition_fee.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveTuitionFee"
                    :disabled="!agreement.branch_id || agreement.is_sale_role"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4 vs-con-table stripe vs-table-primary" v-if="agreement.tuition_fee_type==2">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="1" rowspan="1">Tên gói phí lẻ</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá bán</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá bán combo</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in agreement.tuition_fee_relation" :key="index">
                      <!---->
                      <td class="td vs-table--td">{{item.name}}</td>
                      <td class="td vs-table--td text-center">{{item.session}}</td>
                      <td class="td vs-table--td text-center">{{item.price | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.price_combo | formatMoney}}</td>
                    </tr>
                  </table>
                  
                </div>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giá bán</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="agreement.tuition_fee_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="agreement.tuition_fee_type==1">
              <label>Số buổi</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.tuition_fee_session"
                disabled="true"
              />
            </div>
            
            <!-- CHỌN LỚP ĐANG XẾP -->
            <div class="vx-col w-full mb-4">
              <label>Lớp học</label>
              <vue-select
                    label="label"
                    placeholder="Chọn lớp để xếp lớp ngay"
                    :options="html.classes.list"
                    v-model="html.classes.item"
                    :searchable="true"
                    @input="saveClass"
                    :disabled="isClassEditDisabled || isClassSelectDisabled"
                ></vue-select>
                <div v-if="isClassEditDisabled" class="mt-1">
                   <small class="text-danger"><i class="fa-solid fa-lock mr-1"></i> Lớp học đã khai giảng nên không thể sửa đổi tại đây.</small>
                </div>
                <p class="text-danger mt-1 text-sm" v-if="isClassSelectDisabled" style="font-size: 11px;">
                  <i>* Chỉ được xếp lớp khi có bill học phí từ 2 triệu trở lên (hiện tại: {{ (agreement.tmp_payment_amount || 0) | formatMoney }})</i>
                </p>
            </div>

            <!-- HIỂN THỊ CHI TIẾT LỚP HỌC -->
            <div class="vx-col w-full mb-4" v-if="classInfo">
              <div class="border border-gray-300 rounded shadow-sm">
                <div class="bg-gray-100 px-3 py-2 border-b border-gray-300">
                  <h6 class="font-bold flex items-center text-sm mb-0">
                    <i class="fa-solid fa-circle-info mr-2"></i> CHI TIẾT LỚP HỌC
                  </h6>
                </div>
                <div class="p-3 bg-white text-sm">
                   <div class="vx-row">
                      <div class="vx-col sm:w-1/2 w-full mb-2">
                         <span class="text-gray-500">Giáo viên:</span> <span class="font-medium">{{ classInfo.teacher_name || '---' }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-2">
                         <span class="text-gray-500">CM:</span> <span class="font-medium">{{ classInfo.cm_name || '---' }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-2">
                         <span class="text-gray-500">Thời gian:</span> <span class="font-medium">{{ formatDate(classInfo.cls_startdate) }} - {{ formatDate(classInfo.cls_enddate) }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-2">
                         <span class="text-gray-500">Sĩ số:</span> 
                         <span class="font-bold ml-1">{{ classInfo.enrolled_students }}/{{ classInfo.max_students }}</span>
                         <span class="ml-2 py-0.5 px-2 rounded-lg text-xs" :class="classInfo.enrolled_students >= classInfo.max_students ? 'bg-danger text-white' : 'bg-success text-white'">
                            {{ classInfo.availability_text }}
                         </span>
                      </div>
                   </div>
                </div>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày dự kiến học</label>
              <datepicker class="w-full"
                v-model="agreement.start_date"
                placeholder="Chọn ngày dự kiến học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận sách</label>
              <vue-select
                    label="label"
                    placeholder="Chọn trạng thái nhận sách"
                    :options="[{label: 'Có nhận', value: 1}, {label: 'Không nhận (Trừ 100K/1khoá học)', value: 2}, {label: 'Đã nhận (sale đưa trực tiếp cho học viên)', value: 3}]"
                    v-model="agreement.book_receive_obj"
                    :searchable="false"
                    @input="saveBookReceive"
                ></vue-select>
            </div>

            <div class="vx-col w-full mb-4">
              <label>Địa chỉ nhận sách</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="agreement.book_receive_address"
                placeholder="Nhập địa chỉ nhẫn sách"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận hợp đồng</label>
              <vue-select
                    label="label"
                    placeholder="Chọn trạng thái nhận hợp đồng"
                    :options="[{label: 'Có nhận', value: 1}, {label: 'Không nhận', value: 2}, {label: 'Đã nhận (sale đưa trực tiếp cho học viên)', value: 3}]"
                    v-model="agreement.contract_receive_obj"
                    :searchable="false"
                    @input="saveContractReceive"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký theo nhóm</label>
              <vue-select
                    label="label"
                    placeholder="Chọn nhóm đăng ký"
                    :options="[{label: 'Không', value: 0}, {label: 'Nhóm 2', value: 2}, {label: 'Nhóm 3', value: 3}, {label: 'Nhóm 4', value: 4}, {label: 'Nhóm 5', value: 5}, {label: 'Nhóm 6', value: 6}]"
                    v-model="agreement.group_type_obj"
                    :searchable="false"
                    @input="saveGroupType"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền giảm trừ (VNĐ)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="number"
                min="0"
                placeholder="Nhập số tiền giảm trừ"
                v-model="agreement.discount_amount"
                @input="updateDiscount"
                :disabled="agreement.is_sale_role"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lý do / Ghi chú giảm trừ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                placeholder="Nhập lý do giảm trừ"
                v-model="agreement.discount_note"
                :disabled="agreement.is_sale_role"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú hợp đồng</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="agreement.note"></textarea>
            </div>
            <vs-divider/>
            <div class="vx-col md:w-1/3 w-full mb-4">
            </div>
            <div class="vx-col md:w-2/3 w-full mb-4">
              <div  class="invoice-total-wrapper">
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Tổng tiền phải đóng: </p>
                      <p  class="invoice-total-amount"> {{ agreement.total_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item" v-if="agreement.discount_amount > 0">
                      <p  class="invoice-total-title"> Giảm trừ: </p>
                      <p  class="invoice-total-amount" style="color: #ff9f43;"> -{{ agreement.discount_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Số tiền đã đóng: </p>
                      <p  class="invoice-total-amount"> {{ agreement.total_charged | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item" v-if="agreement.transferred_amount > 0">
                      <p  class="invoice-total-title"> Đã chuyển sang gói khác: </p>
                      <p  class="invoice-total-amount" style="color: #ea5455;"> -{{ agreement.transferred_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item" v-if="agreement.received_amount > 0">
                      <p  class="invoice-total-title"> Nhận từ gói khác: </p>
                      <p  class="invoice-total-amount" style="color: #28c76f;"> +{{ agreement.received_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item" v-if="agreement.refunded_amount > 0">
                      <p  class="invoice-total-title"> Đã hoàn tiền: </p>
                      <p  class="invoice-total-amount" style="color: #ea5455;"> -{{ agreement.refunded_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Công nợ: </p>
                      <p  class="invoice-total-amount"  style="font-weight: bold;"> {{ agreement.debt_amount | formatMoney}} </p>
                  </div>
                  <div class="invoice-total-item" v-if="excessAmount > 0 || (effectiveAmount > 0 && !hasStudied)">
                      <p class="invoice-total-title" v-if="excessAmount > 0"> Tiền thừa: </p>
                      <p class="invoice-total-title" v-else> Có thể hoàn cọc: </p>
                      <div class="invoice-total-amount flex items-center">
                          <span class="text-success font-bold mr-2" v-if="excessAmount > 0">{{ excessAmount | formatMoney }}</span>
                          <span class="text-warning font-bold mr-2" v-else>{{ effectiveAmount | formatMoney }}</span>
                          <vs-button v-if="agreement.can_edit_lower_fee && excessAmount > 0" size="small" color="primary" type="border" class="mr-2" @click="openTransferModal">Chuyển sang gói khác</vs-button>
                          <vs-button v-if="agreement.can_edit_lower_fee && availableRefundTypes.length > 0" size="small" color="danger" type="border" @click="openRefundModal">Hoàn tiền</vs-button>
                      </div>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Số tiền còn lại (đã học): </p>
                      <p  class="invoice-total-amount" style="font-weight: bold;color: red;"> {{ agreement.total_left_amount | formatMoney}} </p>
                  </div>
              </div>
            </div>
            <!-- ================== ĐỐI SOÁT GÓI PHÍ ================== -->
            <vx-card class="mt-5" title="📦 Đối soát gói học phí khi thay đổi" v-if="agreement.tuition_fee_id != tmp_tuition_fee_id">
              
              <!-- SUMMARY -->
              <div class="vx-row mb-4">
                <div class="vx-col md:w-1/3 w-full">
                  <p><strong>💰 Tổng tiền gói mới</strong></p>
                  <h4 class="text-primary">
                    {{ agreement.tuition_fee_amount | formatMoney }}
                  </h4>
                </div>

                <div class="vx-col md:w-1/3 w-full">
                  <p><strong>➖ Được trừ từ gói cũ</strong></p>
                  <h4 class="text-success">
                    {{ totalTransferAmount | formatMoney }}
                  </h4>
                </div>

                <div class="vx-col md:w-1/3 w-full">
                  <p><strong>🔴 Cần đóng thêm</strong></p>
                  <h4 class="text-danger">
                    {{ needPayMore | formatMoney }}
                  </h4>
                </div>
              </div>

              <!-- TABLE -->
              <div
                class="vx-col w-full mb-4 vs-con-table stripe vs-table-primary"
                v-if="comparePackages && comparePackages.length"
              >
                <div class="con-tablex vs-table--content">
                  <div class="vs-con-tbody vs-table--tbody">
                    <table class="vs-table vs-table--tbody-table">
                      <thead class="vs-table--thead">
                        <tr>
                          <th>Gói học</th>
                          <th>Trạng thái</th>
                          <th class="text-right">Đã đóng</th>
                          <th class="text-right">Còn lại</th>
                          <th class="text-right">Áp dụng</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr
                          class="tr-values vs-table--tr"
                          v-for="(item, index) in comparePackages"
                          :key="index"
                        >
                          <td class="td vs-table--td">
                            {{ item.name }}
                          </td>

                          <td class="td vs-table--td">
                            <vs-chip :color="item.statusColor" transparent>
                              {{ item.statusText }}
                            </vs-chip>
                          </td>

                          <td class="td vs-table--td text-right">
                            {{ item.total_charged | formatMoney }}
                          </td>

                          <td class="td vs-table--td text-right">
                            {{ item.left_amount | formatMoney }}
                          </td>

                          <td
                            class="td vs-table--td text-right font-bold"
                            :class="item.statusColor === 'danger' ? 'text-danger' : 'text-success'"
                          >
                            {{ item.apply_amount | formatMoney }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- NOTE -->
              <vs-alert color="warning" class="mt-4">
                <strong>Lưu ý:</strong><br/>
                • Các gói thuộc gói học phí mới sẽ được <b>tính luôn sang gói mới</b><br/>
                • Các gói <b>bị loại</b> sẽ chỉ được tính phần phí còn lại sang gói mới
              </vs-alert>

            </vx-card>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/agreements/${agreement.id}/detail`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Lưu</vs-button>
        </div>
      </div>
      <vs-divider/>
      <div class="vx-col w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin hợp đồng</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4 vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="1" rowspan="1"> Mã hợp đồng</th>
                        <th colspan="1" rowspan="1"> Gói phí</th>
                        <th colspan="1" rowspan="1"> Tên Lớp</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá</th>
                        <th colspan="1" rowspan="1" class="text-center">Đã đóng</th>
                        <th colspan="1" rowspan="1" class="text-center">Còn lại</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi đã học</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" :style="item.relearn_from_contract_code ? 'background-color: #fff9e6;' : ''" v-for="(item, index) in sortedContracts" :key="index">
                      <!---->
                      <td class="td vs-table--td">
                        {{item.code}}
                        <div v-if="item.relearn_from_contract_code" style="font-size: 0.75rem; color: #ff9f43; font-style: italic;">
                          (Học lại từ {{ item.relearn_from_contract_code }})
                        </div>
                      </td>
                      <td class="td vs-table--td">
                        {{item.tuition_fee_name}}
                        <div v-if="item.product_id != item.original_product_id" style="font-size: 0.75rem; color: #ff9f43; font-style: italic; margin-top: 4px;">
                          (đã đổi sang sản phẩm {{ item.current_product_name }})
                        </div>
                      </td>
                      <td class="td vs-table--td">{{item.class_name}}</td>
                      <td class="td vs-table--td text-center">{{item.must_charge | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.total_charged | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.left_amount | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.real_sessions }}</td>
                      <td class="td vs-table--td text-center">{{item.done_sessions}}</td>
                      <td class="td vs-table--td text-center">
                        <div class="flex flex-col items-center justify-center">
                          <span class="mb-1">{{item.label_status}}</span>
                          <div class="flex justify-center items-center mt-1">
                            <vx-tooltip v-if="agreement.is_admin && !item.relearn_from_contract_id && !item.has_relearned" text="Học lại" position="bottom" class="inline-block mr-2">
                              <vs-button size="small" color="warning" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="confirmRelearn(item.id)"></vs-button>
                            </vx-tooltip>
                            <vx-tooltip v-if="agreement.can_edit_lower_fee" text="Đổi sản phẩm" position="bottom" class="inline-block">
                              <vs-button size="small" color="primary" type="filled" icon-pack="feather" icon="icon-edit" @click="openChangeProductModal(item)"></vs-button>
                            </vx-tooltip>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  
                </div>
              </div>
            </div>
          </div>
      </div>  
    </vx-card>

    <vs-popup title="Chuyển tiền thừa sang gói khác" :active.sync="popupTransferActive">
      <div v-if="transferData" class="vx-row">
        <div class="vx-col w-full mb-4">
          <p class="mb-2">Số tiền thừa hiện tại: <strong>{{ excessAmount | formatMoney }}</strong></p>
          <label>Chọn gói học phí nhận tiền</label>
          <vue-select
            label="label"
            placeholder="Chọn gói nhận tiền"
            :options="transferAgreements"
            v-model="transferData.to_agreement"
            :searchable="true"
          ></vue-select>
        </div>
        <div class="vx-col w-full mb-4">
          <label>Số tiền muốn chuyển</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="number"
            v-model="transferData.amount"
            :max="excessAmount"
          />
        </div>
        <div class="vx-col w-full mb-4">
          <label>Ghi chú</label>
          <textarea class="vs-inputx vs-input--input normal" v-model="transferData.note"></textarea>
        </div>
        <div class="vx-col w-full text-right mt-4">
          <vs-button color="dark" type="border" class="mr-2" @click="popupTransferActive = false">Hủy</vs-button>
          <vs-button color="success" @click="submitTransfer" :disabled="transferCalling">Xác nhận chuyển</vs-button>
        </div>
      </div>
    </vs-popup>

    <vs-popup title="Hoàn tiền cho học sinh" :active.sync="popupRefundActive">
      <div v-if="refundData" class="vx-row">
        <div class="vx-col w-full mb-4">
          <label>Loại hoàn tiền</label>
          <vue-select
            label="label"
            placeholder="Chọn loại hoàn tiền"
            :options="availableRefundTypes"
            v-model="refundData.refund_type"
            :searchable="false"
            @input="changeRefundType"
          ></vue-select>
        </div>
        <div class="vx-col w-full mb-4">
          <p class="mb-2">Số tiền có thể hoàn tối đa: <strong class="text-success">{{ maxRefundAmount | formatMoney }}</strong></p>
          <label>Hình thức hoàn tiền</label>
          <vue-select
            label="label"
            placeholder="Chọn hình thức"
            :options="methodOptions"
            v-model="refundData.method"
            :searchable="false"
          ></vue-select>
        </div>
        <div class="vx-col w-full mb-4">
          <label>Số tiền muốn hoàn</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="number"
            v-model="refundData.amount"
            :max="maxRefundAmount"
          />
        </div>
        <div class="vx-col w-full mb-4">
          <label>Ghi chú hoàn tiền</label>
          <textarea class="vs-inputx vs-input--input normal" v-model="refundData.note"></textarea>
        </div>
        <div class="vx-col w-full text-right mt-4">
          <vs-button color="dark" type="border" class="mr-2" @click="popupRefundActive = false">Hủy</vs-button>
          <vs-button color="danger" @click="submitRefund" :disabled="refundCalling">Xác nhận hoàn tiền</vs-button>
        </div>
      </div>
    </vs-popup>

    <vs-popup title="Đổi sản phẩm cho gói phí" :active.sync="popupChangeProductActive">
      <div v-if="changeProductData && changeProductData.contract" class="vx-row">
        <div class="vx-col w-full mb-4">
          <p class="mb-2">Gói học phí: <strong>{{ changeProductData.contract.tuition_fee_name }}</strong></p>
          <label>Chọn sản phẩm mới</label>
          <vue-select
            label="name"
            placeholder="Chọn sản phẩm mới"
            :options="html.products.list"
            v-model="changeProductData.new_product"
            :searchable="true"
          ></vue-select>
        </div>
        <div class="vx-col w-full text-right mt-4">
          <vs-button color="dark" type="border" class="mr-2" @click="popupChangeProductActive = false">Hủy</vs-button>
          <vs-button color="success" @click="submitChangeProduct" :disabled="changeProductCalling">Xác nhận đổi</vs-button>
        </div>
      </div>
    </vs-popup>
  </div>

</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';
  import search from '../../../components/StudentSearch'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      search
    },
    data() {
      return {
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: "",
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ]
          }
        },
        calling:false,
        filter: {
          search: {
              link: 0,
              display: 'hidden',
              find: keyword => this.searchSuggestStudent(keyword),
              action: student => this.selectStudent(student)
          }
        },
        html:{
          branches: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
          tuition_fee:{
            item: '',
            list: []
          },
          discount_codes:{
            item: '',
            list: []
          },
          b2b_campaign:{
            item: '',
            list: []
          },
          ec: {
            item: '',
            list: []
          },
          classes: {
            item: '',
            list: []
          }
        },
        agreement:{
          branch_id:'',
          student_id: '',
          product_id: '',
          type:'',
          tuition_fee_id:'', 
          tuition_fee_amount:'',
          tuition_fee_receivable:'',
          tuition_fee_session:'',
          discount_code_id:'',
          discount_code:'',
          discount_code_amount:'',
          discount_code_percent:'',
          coupon_code_check:0,
          coupon_code:'',
          coupon_amount: '',
          coupon_session: '',
          total_amount:'',
          total_session:'',
          start_date:'',
          note:'',
          b2b_campaign_id:'',
          b2b_amount:'',
          b2b_bonus_session:'',
          tuition_fee_relation:[],
          tuition_fee_type:'',
          book_receive: 0,
          book_receive_obj: null,
          book_receive_address: '',
          contract_receive: 0,
          contract_receive_obj: null,
          group_type: 0,
          group_type_obj: null,
          point_toeic: '',
          ec_id: '',
          ec_leader_id: '',
          is_admin: false,
          class_id: null,
          class_name: '',
          class_start_date: '',
        },
        student_info:{

        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        tmp_tuition_fee_id:'',
        tmp_discount_code_id:'',
        classInfo: null,
        minPaymentForClass: 2000000,
        popupTransferActive: false,
        transferCalling: false,
        transferAgreements: [],
        transferData: {
          to_agreement: null,
          amount: 0,
          note: ''
        },
        popupRefundActive: false,
        refundCalling: false,
        refundData: {
          amount: 0,
          method: { label: 'Chuyển khoản', value: 1 },
          refund_type: { label: 'Hoàn tiền thừa', value: 1 },
          note: ''
        },
        methodOptions: [
          { label: 'Chuyển khoản', value: 1 },
          { label: 'Tiền mặt', value: 2 }
        ],
        popupChangeProductActive: false,
        changeProductCalling: false,
        changeProductData: {
          contract: null,
          new_product: null
        },
      }
    },
    async created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.html.branches.list = response.data
      })
      await axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
      this.loadDetail();
    },
    computed: {
      // =============================
      // product_id thuộc gói phí mới
      // =============================
      productIdsOfNewFee() {
        // Chưa chọn gói học phí
        if (!this.html.tuition_fee.item) return []

        // Gói combo
        if (
          this.html.tuition_fee.item.type_fee == 2 &&
          this.html.tuition_fee.item.tuition_fee_relation
        ) {
          return this.html.tuition_fee.item.tuition_fee_relation.map(
            i => Number(i.product_id)
          )
        }

        // Gói thường
        if (this.html.tuition_fee.item.product_id) {
          return [Number(this.html.tuition_fee.item.product_id)]
        }

        return []
      },

      // =============================
      // BẢNG ĐỐI SOÁT GÓI PHÍ
      // =============================
      comparePackages() {
        if (!this.agreement.contracts || !this.agreement.contracts.length) {
          return []
        }

        return this.agreement.contracts.map(contract => {
          const isInNewFee = this.productIdsOfNewFee.includes(Number(contract.product_id))

          return {
            name: contract.tuition_fee_name,
            statusText: isInNewFee ? 'Giữ / Chuyển tiếp' : 'Bị loại',
            statusColor: isInNewFee ? 'primary' : 'danger',
            total_charged: Number(contract.total_charged || 0),
            left_amount: Number(contract.left_amount || 0),
            apply_amount: isInNewFee
              ? Number(contract.total_charged || 0)
              : Number(contract.left_amount || 0)
          }
        })
      },

      // =============================
      // TỔNG TIỀN ĐƯỢC TRỪ
      // =============================
      totalTransferAmount() {
        return this.comparePackages.reduce(
          (sum, item) => sum + item.apply_amount,
          0
        )
      },

      // =============================
      // SỐ TIỀN CẦN ĐÓNG THÊM
      // =============================
      needPayMore() {
        const total = Number(this.agreement.tuition_fee_amount || 0)
        return total > this.totalTransferAmount
          ? total - this.totalTransferAmount
          : 0
      },
      isClassEditDisabled() {
        if (!this.agreement.class_id) return false;
        if (!this.agreement.class_start_date) return false;
        return moment(this.agreement.class_start_date).isBefore(moment(), 'day');
      },
      isClassSelectDisabled() {
        if (this.agreement.is_sale_role) {
          const totalPaid = Number(this.agreement.tmp_payment_amount) || 0;
          if (totalPaid < this.minPaymentForClass) {
            return true;
          }
        }
        return false;
      },
      sortedContracts() {
        if (!this.agreement || !this.agreement.contracts) return [];
        let sorted = [];
        let originalContracts = this.agreement.contracts.filter(c => !c.relearn_from_contract_id);
        originalContracts.forEach(c => {
          sorted.push(c);
          let relearned = this.agreement.contracts.filter(r => r.relearn_from_contract_id == c.id);
          sorted.push(...relearned);
        });
        
        let processedIds = sorted.map(c => c.id);
        let remaining = this.agreement.contracts.filter(c => !processedIds.includes(c.id));
        sorted.push(...remaining);

        return sorted;
      },
      excessAmount() {
        const charged = (Number(this.agreement.total_charged) || 0) + (Number(this.agreement.received_amount) || 0) - (Number(this.agreement.transferred_amount) || 0);
        const must_charge = Number(this.agreement.total_amount) || 0;
        return charged > must_charge ? (charged - must_charge) : 0;
      },
      effectiveAmount() {
        return (Number(this.agreement.total_charged) || 0) + (Number(this.agreement.received_amount) || 0) - (Number(this.agreement.transferred_amount) || 0);
      },
      hasStudied() {
        if (!this.agreement || !this.agreement.contracts) return false;
        return this.agreement.contracts.some(c => Number(c.done_sessions) > 0);
      },
      availableRefundTypes() {
        let types = [];
        if (this.excessAmount > 0) {
          types.push({ label: 'Hoàn tiền thừa', value: 1 });
        }
        if (this.effectiveAmount > 0 && !this.hasStudied) {
          types.push({ label: 'Hoàn tiền đặt cọc', value: 2 });
        }
        return types;
      },
      maxRefundAmount() {
         if (this.refundData && this.refundData.refund_type) {
            return this.refundData.refund_type.value === 1 ? this.excessAmount : this.effectiveAmount;
         }
         return 0;
      }
    },

    methods: {
      confirmRelearn(contractId) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: `Xác nhận`,
          text: 'Bạn có chắc chắn muốn tạo gói học lại cho gói phí này?',
          accept: () => this.relearnContract(contractId),
          acceptText: 'Đồng ý'
        });
      },
      relearnContract(contractId) {
        this.$vs.loading();
        axios.p('/api/lms/agreements/relearn', { contract_id: contractId })
          .then((response) => {
            this.$vs.loading.close();
            if (response.data.status == 1) {
              this.$vs.notify({
                title: 'Thành công',
                text: response.data.message,
                color: 'success',
                iconPack: 'feather',
                icon: 'icon-check'
              });
              this.loadDetail(); // reload detail
            } else {
              this.$vs.notify({
                title: 'Lỗi',
                text: response.data.message,
                color: 'danger',
                iconPack: 'feather',
                icon: 'icon-alert-circle'
              });
            }
          })
          .catch((error) => {
            this.$vs.loading.close();
          });
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/agreements/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.agreement = Object.assign({}, this.agreement, response.data)
          this.agreement.discount_amount = Number(response.data.discount_amount) || 0
          this.agreement.discount_note = response.data.discount_note || ''
          this.agreement.tuition_fee_amount = Number(response.data.init_tuition_fee_amount) || (Number(response.data.must_charge || 0) + Number(response.data.discount_amount || 0))
          this.agreement.total_amount = response.data.must_charge
          this.agreement.must_charge = response.data.must_charge
          this.agreement.total_session = response.data.total_sessions
          this.agreement.tuition_fee_session = response.data.init_tuition_fee_session
          this.agreement.tuition_fee_type = response.data.type_fee
          this.tmp_tuition_fee_id = response.data.tuition_fee_id
          this.agreement.contracts = response.data.contracts
          this.agreement.class_id = response.data.class_id
          this.agreement.class_name = response.data.class_name
          this.agreement.class_start_date = response.data.class_start_date
          this.updateDiscount();
          
          // Load các trường mới
          this.agreement.book_receive = response.data.book_receive || 0
          this.agreement.book_receive_address = response.data.book_receive_address || ''
          this.agreement.contract_receive = response.data.contract_receive || 0
          this.agreement.group_type = response.data.group_type || 0
          
          // Set các obj cho vue-select
          if (this.agreement.book_receive > 0) {
            const bookOptions = [{label: 'Có nhận', value: 1}, {label: 'Không nhận (Trừ 100K/1khoá học)', value: 2}, {label: 'Đã nhận (sale đưa trực tiếp cho học viên)', value: 3}]
            this.agreement.book_receive_obj = bookOptions.find(o => o.value == this.agreement.book_receive)
          }
          if (this.agreement.contract_receive > 0) {
            const contractOptions = [{label: 'Có nhận', value: 1}, {label: 'Không nhận', value: 2}, {label: 'Đã nhận (sale đưa trực tiếp cho học viên)', value: 3}]
            this.agreement.contract_receive_obj = contractOptions.find(o => o.value == this.agreement.contract_receive)
          }
          if (this.agreement.group_type > 0) {
            const groupOptions = [{label: 'Không', value: 0}, {label: 'Nhóm 2', value: 2}, {label: 'Nhóm 3', value: 3}, {label: 'Nhóm 4', value: 4}, {label: 'Nhóm 5', value: 5}, {label: 'Nhóm 6', value: 6}]
            this.agreement.group_type_obj = groupOptions.find(o => o.value == this.agreement.group_type)
          }
          
          if (!this.agreement.is_sale_role) {
            this.loadECList();
          }

          this.loadTuitionFee(response.data.tuition_fee_id);
          this.loadClassesForEnrolment();
        })
      },
      resetTuitionFee(){
        this.agreement.tuition_fee_id = ''
        this.agreement.tuition_fee_amount = ''
        this.agreement.tuition_fee_session = ''
        this.agreement.tuition_fee_type = ''
        this.agreement.tuition_fee_relation = []
        this.agreement.session = ''
      },
      saveTuitionFee(data = null){
        if (data && typeof data === 'object') {
          const tuition_fee_id = data.id
          this.agreement.tuition_fee_id = tuition_fee_id
          this.agreement.tuition_fee_amount = data.price
          this.agreement.tuition_fee_session = data.session
          this.agreement.tuition_fee_type = data.type_fee
          this.agreement.tuition_fee_relation = data.tuition_fee_relation
          this.updateDiscount();
          this.calculatorLeftAmountWhenChangeTuitionFee();
          this.loadClassesForEnrolment();
        }else{
          this.agreement.tuition_fee_id = ""
        }
      },
      calculatorLeftAmountWhenChangeTuitionFee() {
        let total = 0

        if (!this.agreement.contracts || !this.agreement.contracts.length) {
          this.agreement.total_left_amount = 0
          return
        }

        // 👉 Danh sách product_id thuộc gói phí mới
        let productIdsOfNewFee = this.productIdsOfNewFee

        // 👉 Tính tiền
        this.agreement.contracts.forEach(contract => {
          if (productIdsOfNewFee.includes(Number(contract.product_id))) {
            total += Number(contract.total_charged || 0)
          } else {
            total += Number(contract.left_amount || 0)
          }
        })

        this.agreement.total_left_amount = total
        const effectiveCharged = (Number(this.agreement.total_charged) || 0) + (Number(this.agreement.received_amount) || 0) - (Number(this.agreement.transferred_amount) || 0)
        this.agreement.debt_amount = Number(this.agreement.total_amount || 0) > effectiveCharged ? Number(this.agreement.total_amount || 0) - effectiveCharged : 0
      },
      loadTuitionFee(tuition_fee_id=0){
        if(this.agreement.branch_id){
          this.$vs.loading();
          axios.p(`/api/lms/agreements/load-tuition-fee`,{
            branch_id: this.agreement.branch_id,
          }).then((response) => {
            this.$vs.loading.close();
            this.html.tuition_fee.list = response.data
            if(tuition_fee_id){
              this.html.tuition_fee.item = this.html.tuition_fee.list.filter(item => item.id == tuition_fee_id)[0]
              this.agreement.tuition_fee_relation = this.html.tuition_fee.item.tuition_fee_relation
              this.agreement.tuition_fee_type = this.html.tuition_fee.item.type_fee
              this.agreement.tuition_fee_amount = this.html.tuition_fee.item.price
            }
          }).catch(e => console.log(e))
        }
      },
      selectDate(date){
        if (date) {
          this.agreement.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorSession(){
        console.log(this.agreement);
        this.agreement.total_amount = Number(this.agreement.tuition_fee_amount)  > 0 ? Number(this.agreement.tuition_fee_amount) : 0;
      },
      saveBookReceive(data = null){
        if (data && typeof data === 'object') {
          this.agreement.book_receive = data.value
          if (data.value === 2) {
             const courseCount = this.productIdsOfNewFee && this.productIdsOfNewFee.length > 0 ? this.productIdsOfNewFee.length : (this.agreement.contracts ? this.agreement.contracts.length : 1);
             this.agreement.discount_amount = Number(this.agreement.discount_amount || 0) + (courseCount * 100000);
             let noteAdd = 'Không nhận sách';
             if (this.agreement.discount_note && !this.agreement.discount_note.includes(noteAdd)) {
                 this.agreement.discount_note = this.agreement.discount_note + ', ' + noteAdd;
             } else if (!this.agreement.discount_note) {
                 this.agreement.discount_note = noteAdd;
             }
             this.updateDiscount();
          }
        }else{
          this.agreement.book_receive = 0
        }
      },
      saveContractReceive(data = null){
        if (data && typeof data === 'object') {
          this.agreement.contract_receive = data.value
        }else{
          this.agreement.contract_receive = 0
        }
      },
      saveGroupType(data = null){
        if (data && typeof data === 'object') {
          this.agreement.group_type = data.value
        }else{
          this.agreement.group_type = 0
        }
      },
      loadECList() {
        axios.g(`/api/users/get-data/users-manager`)
          .then(response => {
            this.html.ec.list = response.data.map(item => ({...item, label: item.label_name}))
            if (this.agreement.ec_id) {
              this.html.ec.item = this.html.ec.list.find(e => e.id === this.agreement.ec_id);
            }
          })
      },
      saveEC(data = null) {
        if (data && typeof data === 'object') {
          this.agreement.ec_id = data.id;
          
          axios.p('/api/lms/agreements/get-ec-leader', {
            ec_id: data.id
          }).then(response => {
            if (response.data.status == 1 && response.data.data) {
              this.agreement.ec_leader_id = response.data.data.id;
              this.agreement.ec_leader_name = response.data.data.name;
            } else {
              this.agreement.ec_leader_id = data.id;
              this.agreement.ec_leader_name = data.label_name;
            }
          });
        } else {
          this.agreement.ec_id = '';
          this.agreement.ec_leader_id = '';
          this.agreement.ec_leader_name = '';
        }
      },
      confirmSave () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn thay gói phí?`,
          accept: this.processSave,
          acceptText: 'Thay đổi',
          cancelText: 'Hủy'
        })
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.agreement.total_charged > this.agreement.total_amount && !this.agreement.can_edit_lower_fee) {
          mess += " - Số tiền phải đóng phải lớn hơn số tiền đã đóng<br/>";
          resp = false;
        }
        if (this.agreement.tuition_fee_id == "") {
          mess += " - Gói học phí không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.start_date == "") {
          mess += " - Ngày dự kiến học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        if(this.tmp_tuition_fee_id != this.agreement.tuition_fee_id){
          this.confirmSave ()
        } else{
          this.processSave()
        }
      },
      processSave(){
        this.$vs.loading()
        axios.p("/api/lms/agreements/update",this.agreement)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push(`/lms/agreements/${this.agreement.id}/detail`)
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      loadClassesForEnrolment() {
        if (this.agreement.branch_id && this.agreement.tuition_fee_id && this.agreement.tuition_fee_type) {
          axios.p(`/api/lms/agreements/load-classes-for-enrolment`, {
            branch_id: this.agreement.branch_id,
            tuition_fee_id: this.agreement.tuition_fee_id,
            tuition_fee_type: this.agreement.tuition_fee_type,
            current_class_id: this.agreement.class_id || 0
          }).then((response) => {
            this.html.classes.list = response.data || []
            if (this.agreement.class_id) {
               const item = this.html.classes.list.find(c => Number(c.id) === Number(this.agreement.class_id))
               if (item) {
                  this.html.classes.item = item
                  this.classInfo = item
               }
            }
          }).catch(e => console.log(e))
        }
      },
      saveClass(data = null) {
        if (data && typeof data === 'object') {
          this.agreement.class_id = data.id
          this.classInfo = data
        } else {
          this.agreement.class_id = ''
          this.classInfo = null
        }
      },
      formatDate(date) {
        return date ? moment(date).format('DD/MM/YYYY') : '---';
      },
      openTransferModal() {
        this.$vs.loading();
        axios.p('/api/lms/agreements/list-by-student', {
          student_id: this.agreement.student_id,
          pagination: { limit: 100, cpage: 1 }
        }).then(res => {
          this.$vs.loading.close();
          const list = res.data.list || res.data;
          this.transferAgreements = list
            .filter(item => item.agreement_id !== this.agreement.id)
            .map(item => ({
              ...item,
              label: `${item.code} - ${item.tuition_fee_name} (Nợ: ${String(item.debt_amount).replace(/(.)(?=(\d{3})+$)/g,'$1,')})`
            }));
          
          this.transferData = {
            to_agreement: null,
            amount: this.excessAmount,
            note: 'Chuyển tiền thừa sang gói khác'
          };
          this.popupTransferActive = true;
        }).catch(e => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      submitTransfer() {
        if (!this.transferData.to_agreement) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Vui lòng chọn gói học phí nhận tiền',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }
        if (this.transferData.amount <= 0 || this.transferData.amount > this.excessAmount) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Số tiền chuyển không hợp lệ',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }

        this.transferCalling = true;
        axios.p('/api/lms/agreements/transfer-excess', {
          from_agreement_id: this.agreement.id,
          to_agreement_id: this.transferData.to_agreement.agreement_id,
          amount: this.transferData.amount,
          note: this.transferData.note
        }).then(res => {
          this.transferCalling = false;
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành Công',
              text: res.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupTransferActive = false;
            this.loadDetail(); // Reload trang để cập nhật số dư
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: res.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        }).catch(e => {
          console.log(e);
          this.transferCalling = false;
        });
      },
      updateDiscount() {
        const feePrice = Number(this.agreement.tuition_fee_amount) || 0;
        const discount = Math.max(0, Number(this.agreement.discount_amount) || 0);
        this.agreement.discount_amount = discount;
        this.agreement.must_charge = Math.max(0, feePrice - discount);
        this.agreement.total_amount = this.agreement.must_charge;

        const effectiveCharged = (Number(this.agreement.total_charged) || 0) + (Number(this.agreement.received_amount) || 0) - (Number(this.agreement.transferred_amount) || 0);
        this.agreement.debt_amount = Math.max(0, this.agreement.must_charge - effectiveCharged);
      },
      openRefundModal() {
        let defaultType = this.availableRefundTypes.length > 0 ? this.availableRefundTypes[0] : { label: 'Hoàn tiền đặt cọc', value: 2 };
        this.refundData = {
          amount: defaultType.value === 1 ? this.excessAmount : this.effectiveAmount,
          method: { label: 'Chuyển khoản', value: 1 },
          refund_type: defaultType,
          note: defaultType.value === 1 ? 'Hoàn tiền thừa cho học sinh' : 'Hoàn tiền đặt cọc cho học sinh'
        };
        this.popupRefundActive = true;
      },
      changeRefundType() {
        if (this.refundData.refund_type) {
           this.refundData.amount = this.maxRefundAmount;
           this.refundData.note = this.refundData.refund_type.value === 1 ? 'Hoàn tiền thừa cho học sinh' : 'Hoàn tiền đặt cọc cho học sinh';
        }
      },
      submitRefund() {
        if (this.refundData.amount <= 0 || this.refundData.amount > this.maxRefundAmount) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Số tiền hoàn không hợp lệ',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }

        this.refundCalling = true;
        axios.p('/api/lms/agreements/refund-excess', {
          agreement_id: this.agreement.id,
          amount: this.refundData.amount,
          method: this.refundData.method ? this.refundData.method.value : 1,
          refund_type: this.refundData.refund_type ? this.refundData.refund_type.value : 1,
          note: this.refundData.note
        }).then(res => {
          this.refundCalling = false;
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành Công',
              text: res.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupRefundActive = false;
            this.loadDetail();
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: res.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        }).catch(e => {
          console.log(e);
          this.refundCalling = false;
        });
      },
      openChangeProductModal(contract) {
        let currentProduct = this.html.products.list.find(p => Number(p.id) === Number(contract.product_id));
        this.changeProductData = {
          contract: contract,
          new_product: currentProduct || null
        };
        this.popupChangeProductActive = true;
      },
      submitChangeProduct() {
        if (!this.changeProductData.new_product) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Vui lòng chọn sản phẩm mới',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }
        
        if (Number(this.changeProductData.new_product.id) === Number(this.changeProductData.contract.product_id)) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Sản phẩm mới phải khác sản phẩm hiện tại',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }

        this.changeProductCalling = true;
        axios.p('/api/lms/agreements/change-contract-product', {
          contract_id: this.changeProductData.contract.id,
          product_id: this.changeProductData.new_product.id
        }).then(res => {
          this.changeProductCalling = false;
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành Công',
              text: res.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupChangeProductActive = false;
            this.loadDetail();
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: res.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        }).catch(e => {
          console.log(e);
          this.changeProductCalling = false;
        });
      }
    },
  }
</script>