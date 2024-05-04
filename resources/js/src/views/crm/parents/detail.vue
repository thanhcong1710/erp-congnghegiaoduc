

<template>

  <div id="page-parent-detail">
    <vx-card no-shadow class="mb-base">
        <div class="vx-row">
          <div class="vx-col md:w-1/3 w-full item-first">
            <div style="float: left; width: 110px;">
              <img class="rounded" src="@assets/images/common/avatar-default.jpg" width="100%">
            </div>
            <div style="float: left; padding: 10px; width: calc(100% - 110px);">
               <h5><strong>{{parent.name}}</strong></h5>
              <p style="margin-top: 5px;" class="list-action"><span> {{parent.mobile_1}}</span> 
                <vs-button size="small" @click="callPhone(parent.mobile_1)"><i class="fa fa-phone"></i></vs-button>
                <vs-button size="small" @click="showSendSms(parent.mobile_1)"><i class="fa fa-sms"></i></vs-button>
              </p>
              <p style="margin-top: 5px;" class="list-action" v-if="parent.mobile_2"> 
                <span> {{parent.mobile_2}}</span> 
                <vs-button size="small" @click="callPhone(parent.mobile_2)"><i class="fa fa-phone"></i></vs-button>
                <vs-button size="small" @click="showSendSms(parent.mobile_2)"><i class="fa fa-sms"></i></vs-button>
              </p>
            </div>
          </div>
          <div class="vx-col md:w-1/3 w-full">
            <p class="mb-1">Liên hệ lần cuối: {{parent.last_care}}</p>
            <p class="mb-1">Tương tác: <strong>{{parent.num_care}}</strong></p>
            <vs-divider/>
            <p><i class="fa fa-calendar"></i> Lịch chăm sóc tiếp theo</p>
            <p class="mb-1"><input class="vs-inputx vs-input--input normal" type="datetime-local" :value="parent.next_care_date" id="next_care_date" @change="updateNextCareDate" :disabled="disabled_action"></p>
          </div>
          <div class="vx-col md:w-1/3 w-full">
            <p>Trạng thái</p>
            <p class="mb-1">
              <select class="vs-inputx vs-input--input normal" @change="openConfirmChangeStatus" v-model="tmp_status" >
                <option value="0" v-if="tmp_status<70">0. KH mới</option>
                <option value="10" v-if="tmp_status<70">1. KH không liên lạc được</option>
                <option value="20" v-if="tmp_status<70">2. KH ở vùng CMS không có cơ sở</option>
                <option value="30" v-if="tmp_status<70">3. KH không nghe máy</option>
                <option value="40" v-if="tmp_status<70">4. KH hẹn gọi lại sau</option>
                <option value="50" v-if="tmp_status<70">5. KH không quan tâm</option>
                <option value="60" v-if="tmp_status<70">6. KH không tiềm năng</option>
                <option value="71">7.1. KH quan tâm, cần follow up date</option>
                <option value="72">7.2. KH tiềm năng nhưng không muốn làm phiền</option>
                <option value="73">7.3. KH đồng ý đặt lịch Checkin</option>
                <option value="81">8.1. KH đã đến checkin</option>
                <option value="82">8.2. KH đã mua gói phí</option>
                <option value="83">8.3. KH đến hạn tái tục</option>
                <option value="90">9. Danh sách đen</option>
              </select>
            </p> 
            <p>Người phụ trách</p>
            <p class="mb-1">
              <select class="vs-inputx vs-input--input normal" @change="openConfirmAssgin" v-model="tmp_owner_id" > 
                <option :value="item.id" v-for="(item, index) in users_manager" :key="index">{{item.label_name}}</option>
              </select>
            </p>
          </div>
        </div>
      </vx-card>

      <vx-card no-shadow class="mb-base" v-if="phone.show">
        <div class="alert alert-secondary" role="alert" >
          <h5 class="alert-heading"> <i class="fa fa-phone" style="margin-right:10px"></i> {{phone.title}}</h5>
          <vs-divider/>
            <div v-html="phone.description"></div>
            <div class="mt-3">
              <label for="nf-email">Trạng thái cuộc gọi <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="phone.select_note_status">
                <option value="">Chọn trạng thái</option>
                <!-- <option value="0">Blank</option> -->
                <option value="1">Thuê bao - Tắt máy - Sai số</option>
                <option value="2">Location</option>
                <option value="3">Máy bận - Không nghe máy</option>
                <option value="4">KH hẹn gọi lại sau</option>
                <option value="5">KH Từ chối nói chuyện</option>
                <option value="6">KH không phù hợp</option>
                <option value="7">KH tiềm năng</option>
                <option value="9">Blacklist</option>
              </select>
            </div>
            <div v-if="['5','6','7'].indexOf(phone.select_note_status) > -1" class="mt-3">
              <label >Chi tiết trạng thái cuộc gọi <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="phone.select_note_status_sub">
                <option value="">Chọn chi tiết trạng thái</option>
                <option value="51" v-if="phone.select_note_status==5">KH đã từng sử dụng dịch vụ</option>
                <option value="52" v-if="phone.select_note_status==5">KH không quan tâm</option>
                <option value="53" v-if="phone.select_note_status==5">KH thực sự không muốn nói chuyện</option>
                <option value="61" v-if="phone.select_note_status==6">Không có con</option>
                <option value="62" v-if="phone.select_note_status==6">Lý do khác</option>
                <option value="71" v-if="phone.select_note_status==7">KH đang cân nhắc</option>
                <option value="72" v-if="phone.select_note_status==7">KH hẹn thời gian khác</option>
                <option value="73" v-if="phone.select_note_status==7">KH ko muốn làm phiền</option>
                <option value="74" v-if="phone.select_note_status==7">Confirm 1</option>
              </select>
            </div>
            <div class="mt-3">
              <label >Ghi chú cuộc gọi<span class="text-danger"> (*)</span></label>
              <textarea class="vs-inputx vs-input--input normal mt-2" v-model="phone.note" placeholder="Thêm ghi chú cuộc gọi"></textarea>
            </div>
            <vs-alert :active.sync="phone.alert.active" class="mb-5 mr-4 ml-4" :color="phone.alert.color" closable icon-pack="feather" close-icon="icon-x">
              <div v-html="phone.alert.body"></div>
            </vs-alert>
            <div class="mt-3" style="text-align:right">
              <vs-button color="success" @click="updateNotePhone"> <i class="fa fa-paper-plane"></i> Lưu</vs-button>
              <vs-button color="dark" @click="phone.show=false"> <i class="fa fa-times"></i> Hủy</vs-button>
            </div>
        </div>
      </vx-card>
      
      <vx-card no-shadow class="mb-base" v-if="sms.show">
        <div class="alert alert-secondary" role="alert" >
          <h5 class="alert-heading"> <i class="fa fa-sms" style="margin-right:10px"></i> {{sms.title}}</h5>
          <vs-divider/>
            <textarea class="vs-inputx vs-input--input normal" v-model="sms.content" placeholder="Nhập nội dung tin nhắn"></textarea>
            <div style="margin-top:5px;text-align:right">
              <vs-button color="success" @click="sendSms"> <i class="fa fa-paper-plane"></i> Gửi</vs-button>
              <vs-button color="dark" @click="sms.show=false"> <i class="fa fa-times"></i> Hủy</vs-button>
            </div>
        </div>
      </vx-card>

      <vx-card no-shadow class="mt-5">
        <vs-tabs v-model="active_tab">
          <vs-tab label="Thông tin" @click="changeTab()">
            <div class="tab-text">
              <div class="vx-row">
                <div class="vx-col md:w-1/2 w-full item-first">
                  <div class="vx-row">
                    <div class="vx-col md:w-1/3 w-full mb-4">
                      <label>Danh xưng <span class="text-danger"> (*)</span></label>
                      <select class="vs-inputx vs-input--input normal" v-model="parent.gender" :disabled="disabled_edit">
                        <option value="M">Ông</option>
                        <option value="F">Bà</option>
                      </select>
                    </div>
                    <div class="vx-col md:w-2/3 w-full mb-4">
                      <label>Họ tên <span class="text-danger"> (*)</span></label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        name="title"
                        v-model="parent.name"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label  >Điện thoại <span class="text-danger"> (*)</span></label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        name="title"
                        v-model="parent.mobile_1"
                        @change="validatePhone"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label  >Điện thoại 2</label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        name="title"
                        v-model="parent.mobile_2"
                        @change="validatePhone2"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Email</label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        name="title"
                        v-model="parent.email"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Ngày sinh </label>
                      <datepicker class="w-full"
                        v-model="parent.birthday"
                        placeholder="Chọn ngày sinh nhật"
                        :lang="datepickerOptions.lang"
                        @change="selectDateParent"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Nghề nghiệp</label>
                      <vue-select
                            label="title"
                            placeholder="Chọn nghề nghiệp"
                            :options="html.jobs.list"
                            v-model="parent_input.job"
                            :searchable="true"
                            language="tv-VN"
                            @input="saveJob"
                            :disabled="disabled_edit"
                        ></vue-select>
                    </div>
                    <div class="vx-col w-full mb-4">
                      <label >Ghi chú</label>
                      <textarea class="vs-inputx vs-input--input normal" v-model="parent.note" :disabled="disabled_edit"></textarea>
                    </div>
                  </div>
                </div>
                <div class="vx-col md:w-1/2 w-full item-last">
                  <div class="vx-row">
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Tỉnh Thành Phố</label>
                      <vue-select
                        naem="input_province"
                        label="name"
                        placeholder="Chọn Tỉnh/Thành Phố"
                        :options="html.province.list"
                        v-model="parent_input.province"
                        :searchable="true"
                        language="tv-VN"
                        @input="getDistrict"
                        :disabled="disabled_edit"
                      ></vue-select>
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Quận huyện</label>
                      <vue-select
                            naem="input_district"
                            label="name"
                            placeholder="Chọn Quận/Huyện/Thị Xã"
                            :options="html.district.list"
                            v-model="parent_input.district"
                            :searchable="true"
                            language="tv-VN"
                            @input="saveDistrict"
                            :disabled="disabled_edit"
                        ></vue-select>
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Nguồn  <span class="text-danger"> (*)</span></label>
                      <vue-select
                            label="name"
                            placeholder="Chọn nguồn"
                            :options="html.source.list"
                            v-model="parent_input.source"
                            :searchable="true"
                            language="tv-VN"
                            @input="saveSource"
                            :disabled="disabled_edit"
                        ></vue-select>
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label >Nguồn chi tiết</label>
                      <vue-select
                            label="name"
                            placeholder="Chọn nguồn chi tiết"
                            :options="html.source_detail.list"
                            v-model="parent_input.source_detail"
                            :searchable="true"
                            language="tv-VN"
                            @input="saveSourceDetail"
                            :disabled="disabled_edit"
                        ></vue-select>
                    </div>
                    <div class="vx-col md:w-1/2 w-full mb-4">
                      <label  >Người giới thiệu (ĐT)</label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        name="title"
                        v-model="parent.c2c_mobile"
                        @change="validatePhoneC2C"
                        :disabled="disabled_edit"
                      />
                    </div>
                    <div class="vx-col w-full mb-4">
                      <p><i>{{c2c_info}}</i></p>
                    </div>
                  </div>
                </div>
                <vs-alert :active.sync="alert.active" class="mb-5 mr-4 ml-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
                  <div v-html="alert.body"></div>
                </vs-alert>
                <div class="vx-col w-full">
                  <vs-button class="mb-2" @click="disabled_edit=false" v-if="disabled_edit== true">Cập nhật thông tin</vs-button>
                  <vs-button color="dark" class="mb-2 mr-3" @click="reloadPage" v-if="disabled_edit== false">Hủy</vs-button>
                  <vs-button class="mb-2" color="success" @click="saveInfo" v-if="disabled_edit== false">Lưu</vs-button>
                </div>
              </div>
            </div>
          </vs-tab>
          <vs-tab label="Chăm sóc"  @click="changeTab()">
            <div class="tab-text">
              <vs-button :disabled="disabled_action" color="success" class=" mt-3 mb-2" @click="showModalCare"><i class="fa fa-plus"></i> Thêm mới chăm sóc</vs-button>
              <div class="vs-component vs-con-table stripe vs-table-primary">
                <div class="con-tablex vs-table--content">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <thead class="vs-table--thead">
                        <tr>
                          <!---->
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text text-center">Thời gian
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Phụ trách
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Trung tâm
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Phương thức
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Trạng thái cuộc gọi
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Chi tiết
                              <!---->
                            </div>
                          </th>
                        </tr>
                      </thead>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in cares" :key="index">
                        <td class="td vs-table--td">{{ item.care_date }}</td>
                        <td class="td vs-table--td">{{ item.creator_name }}</td>
                        <td class="td vs-table--td">{{ item.branch_name }}</td>
                        <td class="td vs-table--td">{{ item.method_name}}{{item.type_call?" - "+item.type_call:''}}</td>
                        <td class="td vs-table--td">{{ item.call_status_label }}</td>
                        <td class="td vs-table--td">
                          <p v-if="item.link_record">
                            <audio controls style="height: 40px; width: 256px; border: 1px solid #ccc;">
                              <source :src="item.link_record" type="audio/x-wav">
                            </audio>
                          </p>
                          <p v-html="item.note"></p>
                          <p v-if="item.attached_file">
                            <a :href="item.attached_file" target="blank">File đính kèm</a>
                          </p>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </vs-tab>
          <vs-tab label="Học sinh"  @click="changeTab()">
            <div class="tab-text">
              <vs-button :disabled="disabled_action" color="success" class=" mt-3 mb-3" @click="showModalStudent(0)"><i class="fa fa-plus"></i> Thêm mới học sinh</vs-button>
               
              <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full item-first" v-for="(item, index) in students" :key="index">
                  <vx-card class="mb-base">
                    <div class="card-header list-action">
                      <strong>{{ item.name }}</strong>
                      <vs-button :disabled="disabled_action" color="success" class="small" @click="showModalStudent(item)"> <i class="fa fa-edit"></i> </vs-button>
                      <vs-button :disabled="disabled_action" color="danger" v-if="item.status==0" class="small" @click="showModalCheckin(item)"> <i class="fa fa-location-arrow"></i></vs-button>
                    </div>
                    <div class="card-body">
                      <p>Ngày sinh: {{ item.birthday }}</p>
                      <p>Giới tính: {{ item.gender | genTextGender}}</p>
                      <p>Ghi chú: {{ item.note}}</p>
                      <p>Ngày tạo: {{ item.created_at}}</p>
                      <p>Người tạo: {{ item.creator_name}}</p>
                      <p>Trạng thái: <b>{{ item.status | genStudentStatus}}</b></p>
                      <p v-if="item.status>0">Trung tâm checkin: {{ item.checkin_branch_name}}</p>
                      <p v-if="item.status>0">Thời gian checkin: {{ item.checkin_at}}</p>
                      <p v-if="item.status>0 && item.status!=2" class="list-action">Cập nhật checkin: 
                        <vs-button :disabled="disabled_action" v-if="item.status>0" @click="showModalUpdateCheckin(item)" class="small"><i class="fa fa-edit"></i></vs-button>
                      </p>
                    </div>
                  </vx-card>
                </div>
              </div>
            </div>
          </vs-tab>
          <vs-tab label="Lịch sử cập nhật"  @click="changeTab()">
            <div class="tab-text">
              <div class="vs-component vs-con-table stripe vs-table-primary">
                <div class="con-tablex vs-table--content">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <thead class="vs-table--thead">
                        <tr>
                          <!---->
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text text-center">Thời gian
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Nội dung
                              <!---->
                            </div>
                          </th>
                          <th colspan="1" rowspan="1" class="text-center">
                            <div class="vs-table-text">Người thực hiện
                              <!---->
                            </div>
                          </th>
                          
                        </tr>
                      </thead>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in logs" :key="index">
                        <td class="td vs-table--td">{{ item.created_at }}</td>
                        <td class="td vs-table--td">{{ item.content }}</td>
                        <td class="td vs-table--td">{{ item.creator_name }}</td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </vs-tab>
        </vs-tabs>
      </vx-card>
      <vs-popup :class="'modal_'+ modal_care.color" :title="modal_care.title" :active.sync="modal_care.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Phương thức</label>
            <select class="vs-inputx vs-input--input normal" v-model="care.method_id">
              <option value="">Chọn phương thức</option>
              <option
                :value="method.id"
                v-for="(method, index) in methods"
                :key="index"
              >{{method.name}}</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>File đính kèm</label>
             <input
                type="file"
                class="vs-inputx vs-input--input normal"
                id="fileUploadExcel"
                @change="fileChanged"
              >
          </div>
          <div class="vx-col w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="care.note"></textarea>
          </div>
          <vs-alert :active.sync="modal_care.alert.active" class="mb-5  mr-4 ml-4" :color="modal_care.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_care.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_care.show = false">Hủy</vs-button>
            <vs-button color="success" @click="addCare">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
      <vs-popup :class="'modal_'+ modal_student.color" :title="modal_student.title" :active.sync="modal_student.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Họ tên học sinh</label>
            <input class="vs-inputx vs-input--input normal" type="text" v-model="student.name">
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ngày sinh</label>
            <datepicker
              class=" w-full calendar"
              v-model="student.birthday"
              placeholder="Chọn ngày sinh nhật"
              :lang="datepickerOptions.lang"
              @change="selectDate"
            />
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Giới tính</label>
            <select class="vs-inputx vs-input--input normal" v-model="student.gender">
              <option value="M">Nam</option>
              <option value="F">Nữ</option>
            </select>
          </div>
          
          <div class="vx-col w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="student.note"></textarea>
          </div>
          <vs-alert :active.sync="modal_student.alert.active" class="mb-5" :color="modal_student.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_student.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_student.show = false">Hủy</vs-button>
            <vs-button color="success" @click="addStudent">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>

      <vs-popup :class="'modal_'+ modal_checkin.color" :title="modal_checkin.title" :active.sync="modal_checkin.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Trung tâm checkin</label>
            <select class="vs-inputx vs-input--input normal" v-model="modal_checkin.branch_id" :disabled="modal_checkin.disabled">
              <option value="">Chọn trung tâm</option>
              <option :value="item.id" v-for="(item, index) in branches" :key="index">{{item.name}}</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ngày/Giờ Checkin</label>
            <datepicker
                      id="checkin-at"
                      class="form-control calendar"
                      :value="modal_checkin.checkin_at"
                      v-model="modal_checkin.checkin_at"
                      placeholder="Chọn ngày giờ"
                      :lang="datepickerOptions.lang"
                      type="datetime"
                      format="YYYY-MM-DD HH:mm"
              >
              </datepicker>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Khóa học</label>
            <select class="vs-inputx vs-input--input normal" v-model="modal_checkin.type_product">
              <option value="">Chọn khóa học</option>
              <option value="1">CMS</option>
              <option value="2">Accelium</option>
            </select>
          </div>
          
          <vs-alert :active.sync="modal_checkin.alert.active" class="mb-5 mr-4 ml-4" :color="modal_checkin.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_checkin.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_checkin.show = false">Hủy</vs-button>
            <vs-button color="success" @click="checkin">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
  </div>

</template>

<script>

  import Multiselect from "vue-multiselect";
  import axios from '../../../http/axios.js';
  import u from '../../../until/helper.js'
  import moment from 'moment';
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      Multiselect,
      moment,
      datepicker
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
        check_list: [],
        active_tab: 1,
        alert:{
          active: false,
          body: '',
          color:'',
        },
        html:{
          province: {
            item: '',
            list: []
          },
          district: {
            item: '',
            list: []
          },
          jobs: {
            item: '',
            list: []
          },
          source: {
            item: '',
            list: []
          },
          source_detail: {
            item: '',
            list: []
          },
        },
        branches:[],
        modal_care: {
          title: "THÊM MỚI CHĂM SÓC",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        modal_student: {
          title: "THÊM MỚI HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        modal_checkin: {
          title: "TẠO CHECKIN HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          branch_id:"",
          checkin_at:"",
          student_id:"",
          error_message:"",
          disabled:false,
          type_product:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        parent_input:{
          job:"",
          source:"",
          source_detail:"",
          province:"",
          district:"",
        },
        parent: {
          id:"",
          gender: "",
          name: "",
          birthday: "",
          mobile_1: "",
          mobile_2:"",
          note: "",
          email: "",
          status: 0,
          province_id:"",
          district_id:"",
          job_id:"",
          source_id:"",
          source_detail_id:"",
          address:"",
          owner_id:"",
          c2c_mobile:"",
        },
        activeItem: 'customer_care',
        methods:[],
        cares:[],
        care:{
          method_id:"",
          care_date:"",
          note:"",
          parent_id:"",
          attached_file:"",
          file_name:"",
        },
        students:[],
        student:{
          id:0,
          parent_id:"",
          name:"",
          gender:"",
          school_level:"",
          birthday:"",
          select_school:"",
          note:"",
        },
        logs:[],
        users_manager:[],
        tmp_owner_id:"",
        tmp_status:"",
        phone:{
          css_class: 'alert alert-success',
          show: false,
          title:'Đang thực hiện cuộc gọi đi',
          status:0,
          description:'',
          show_input_note:false,
          care_id:'',
          note:'',
          select_note:'',
          select_note_status:'',
          select_note_status_sub:'',
          next_care_date:'',
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        sms:{
          content:'',
          show:false,
          title:'Gửi tin nhắn SMS',
          phone:''
        },
        template_note:[],
        disabled_action:false,
        disabled_edit:true,
        c2c_info:"",
      };
    },
    async created() {
      axios.g(`/api/system/methods`)
        .then(response => {
        this.methods = response.data
      })
      axios.g(`/api/system/branches`)
        .then(response => {
        this.branches = response.data
      })
      axios.g(`/api/users/get-data/users-manager`)
      .then(response => {
        this.users_manager = response.data
      })
      axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      axios.g(`/api/system/jobs`)
        .then(response => {
        this.html.jobs.list = response.data
      })
      axios.g(`/api/system/sources`)
        .then(response => {
        this.html.source.list = response.data
      })
      await axios.g(`/api/system/source_detail`)
        .then(response => {
        this.html.source_detail.list = response.data
      })
      this.loadDetail();
      this.loadCares();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/crm/parents/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.parent = response.data
            this.parent_input.job = this.html.jobs.list.filter(item => item.id == response.data.job_id)[0]
            this.parent_input.source = this.html.source.list.filter(item => item.id == response.data.source_id)[0]
            this.parent_input.source_detail = this.html.source_detail.list.filter(item => item.id == response.data.source_detail_id)[0]
            this.parent_input.province = this.html.province.list.filter(item => item.id == response.data.province_id)[0]
            this.tmp_district_id = response.data.district_id
            this.tmp_owner_id = response.data.owner_id
            this.tmp_status = response.data.status
            this.getDistrict(this.parent_input.province);
          }else{
            this.$router.push({ path: `/crm/parent` });
          }
        })
      },
      updateNextCareDate(){
          const data = {
            parent_id: this.$route.params.id,
            next_care_date: document.getElementById('next_care_date').value
          };
          this.$vs.loading();;
          u.p(`/api/parents/update_next_care_date`,data)
          .then((response) => {
            this.loadDetail();
          })
          .catch((e) => {
          });
      },
      showModalAssgin(){
        this.modal_assign.show =true
      },
      showModalChangeStatus(){
        this.modal_status.show =true
      },
      validatePhone(){
        if(this.parent.mobile_1){
          const data = {
            phone: this.parent.mobile_1,
            parent_id: this.parent.id,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.mobile_1 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }
          })
        }
      },
      validatePhone2(){
        if(this.parent.mobile_2){
          const data = {
            phone: this.parent.mobile_2,
            parent_id: this.parent.id,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.mobile_2 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else if(response.data.status==2){
              this.modal_overwrite.show = true;
              this.modal_overwrite.message = response.data.message;
            }
          })
        }
      },
      validatePhoneC2C(){
        this.c2c_info=""
        if(this.parent.c2c_mobile){
          const data = {
            phone: this.parent.c2c_mobile,
          };
          this.$vs.loading();
          u.p(`/api/crm/parents/validate_c2c_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.c2c_mobile ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else{
              this.c2c_info = response.data.message
            }
          })
        }
      },
      selectDateParent(){
        if (date) {
          this.parent.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDate(date) {
        if (date) {
          this.student.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.parent.province = data
          this.parent.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            if(this.tmp_district_id){
              this.parent_input.district = this.html.district.list.filter(item => item.id == this.tmp_district_id)[0]
            }else{
              this.parent.district_id = ""
              this.parent_input.district = ""
            } 
          })
        }else{
          this.parent.province = ""
          this.parent.province_id = ""
          this.html.district.list = []
          this.parent.district = ""
          this.parent.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.parent_input.district = data
          this.parent.district_id = district_id
        }else{
          this.parent_input.district = ""
          this.parent.district_id = ""
        }
      },
      saveJob(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.parent_input.job = data
          this.parent.job_id = job_id
        }else{
          this.parent_input.job = ""
          this.parent.job_id = ""
        }
      },
      saveSource(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent_input.source = data
          this.parent.source_id = source_id
        }else{
          this.parent_input.source = ""
          this.parent.source_id = ""
        }
      },
      saveSourceDetail(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent_input.source_detail = data
          this.parent.source_detail_id = source_id
        }else{
          this.parent_input.source_detail = ""
          this.parent.source_detail_id = ""
        }
      },
      reloadPage(){
        location.reload();
      },
      saveInfo(){
        let mess = "";
        let resp = true;
        if (this.parent.gender == "") {
          mess += " - Danh xưng không được để trống<br/>";
          resp = false;
        }
        if (this.parent.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 != "" && !u.vld.phone(this.parent.mobile_1)) {
          mess += " - Số điện thoại không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.mobile_2 != null && this.parent.mobile_2 != "" && !u.vld.phone(this.parent.mobile_2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.source_id == "") {
          mess += " - Nguồn không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/crm/parents/update",this.parent)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.disabled_edit = true
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      openConfirmChangeStatus () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn cập nhật trạng thái của khách hàng?`,
          accept: this.changeStatus,
          cancel: this.cancelChangeStaus,
          acceptText: 'Cập nhật',
          cancelText: 'Hủy'
        })
      },
      cancelChangeStaus(){
        this.tmp_status = this.parent.status
      },
      changeStatus(){
        const data = {
          parent_id: this.parent.id,
          status: this.tmp_status,
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/change_status`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.loadDetail();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
        .catch((e) => {
        });
      },
      openConfirmAssgin () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn thay đổi người phụ trách của khách hàng?`,
          accept: this.changeAssgin,
          cancel: this.cancelChangeAssgin,
          acceptText: 'Cập nhật',
          cancelText: 'Hủy'
        })
      },
      cancelChangeAssgin(){
        this.tmp_owner_id = this.parent.owner_id
      },
      changeAssgin(){
        const data = {
          parent_id: this.parent.id,
          owner_id: this.tmp_owner_id,
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/assign`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.loadDetail();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
      updateNextCareDate(){
        const data = {
          parent_id: this.$route.params.id,
          next_care_date: document.getElementById('next_care_date').value
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/update_next_care_date`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
        .catch((e) => {
        });
      },
      changeTab(){
        if(this.active_tab ==1){
          this.loadCares();
        }else if(this.active_tab ==2){
          this.loadStudents();
        }else if(this.active_tab ==3){
          this.loadLogs();
        }else{
          this.loadDetail();
        }
      },
      loadLogs(){
        this.$vs.loading();
        axios.g(`/api/crm/parents/get_logs/${this.parent.id}`)
        .then((response) => {
          this.$vs.loading.close();
          this.logs=response.data;
        })
        .catch((e) => {
        });
      },
      showModalCare(){
        // document.getElementById('published_date').value=""
        this.modal_care.show = true
        this.modal_care.error_message=""
        this.care.method_id=""
        this.care.note=""
        this.care.attached_file=""
        this.care.file_name=""
      },
      loadCares(){
        this.$vs.loading();
          axios.g(`/api/crm/care/get_all_data/${this.$route.params.id}`)
          .then((response) => {
            this.$vs.loading.close();
            this.cares=response.data;
          })
          .catch((e) => {
          });
      },
      addCare(){
        this.care.parent_id = this.parent.id
        let mess = "";
        let resp = true;
        if (this.care.method_id == "") {
          mess += " - Phương thức chăm sóc không được để trống<br/>";
          resp = false;
        }
        if (this.care.note == "") {
          mess += " - Nội dung chăm sóc không được để trống<br/>";
          resp = false;
        }
        if (this.care.file_name == "") {
          mess += " - File đính kèm không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_care.alert.color = 'danger'
          this.modal_care.alert.body = mess;
          this.modal_care.alert.active = true;
          return false;
        }
        this.$vs.loading();
        this.modal_care.show = false
        axios.p(`/api/crm/care/add`,this.care)
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.loadCares();
        })
        .catch((e) => {
        });
      },
      fileChanged(e) {
        const fileReader = new FileReader();
        const fileName = e.target.value.split("\\").pop();
        this.care.file_name = fileName
        fileReader.readAsDataURL(e.target.files[0]);
        fileReader.onload = e => {
          this.care.attached_file = e.target.result;
        };
      },
      showSendSms(phone){
        this.sms.show =true
        this.sms.phone =phone
        this.sms.content = ''
        this.sms.title = 'Gửi tin nhắn SMS tới SĐT - '+phone
      },
      sendSms(){
        if(this.sms.content){
          const data = {
            parent_id: this.$route.params.id,
            content: this.sms.content,
            phone: this.sms.phone,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/send_sms`,data)
          .then((response) => {
            this.$vs.loading.close();
            this.sms.show = false;
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.loadCares();
          })
          .catch((e) => {
          });
        }
      },
      callPhone(phone){
        this.phone.show = true
        this.phone.status = 0
        this.phone.select_note = ""
        this.phone.show_input_note = false
        this.phone.css_class= 'alert alert-success'
        this.phone.title = "Đang thực hiện cuộc gọi đi đến SĐT - "+phone+" ..."
        this.phone.care_id = ''
        this.phone.note=''
        this.phone.select_note_status='',
        this.phone.select_note_status_sub='',
        this.phone.next_care_date='',
        this.phone.alert.body=''
        this.phone.alert.color=''
      },
      updateNotePhone(){
        let mess = "";
        let resp = true;
        
        this.phone.alert.body=''
        this.phone.alert.color=''
        if (this.phone.select_note_status == "") {
          mess += " - Trạng thái cuộc gọi không được để trống<br/>";
          resp = false;
        }
        if (['5','6','7'].indexOf(this.phone.select_note_status) > -1 && this.phone.select_note_status_sub =='') {
          mess += " - Chi tiết trạng thái cuộc gọi không được để trống<br/>";
          resp = false;
        }
        
        if (this.phone.note == "") {
          mess += " - Ghi chú cuộc gọi không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.phone.alert.color = 'danger'
          this.phone.alert.body = mess;
          this.phone.alert.active = true;
          return false;
        }

        const data = {
          method_id:1,
          note:this.phone.note,
          parent_id:this.parent.id,
          attached_file:"",
          file_name:"",
          care_date:"",
          call_status_sub:this.phone.select_note_status_sub,
          call_status:this.phone.select_note_status
        };
        this.$vs.loading();
        axios.p(`/api/crm/care/add`,data)
          .then((response) => {
            this.$vs.loading.close();
            this.phone.show = false;
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.loadCares();
          })
          .catch((e) => {
          });
      },
      showModalStudent(data){
        if(data==0){
          this.modal_student.show = true
          this.modal_student.error_message=""
          this.modal_student.title ="THÊM MỚI HỌC SINH"
          this.student.id =0
          this.student.parent_id =""
          this.student.name =""
          this.student.gender =""
          this.student.school_level =""
          this.student.birthday =""
          this.student.select_school =""
          this.student.note =""
        }else{
          console.log(data)
          this.modal_student.show = true
          this.modal_student.error_message=""
          this.modal_student.title ="CẬP NHẬT THÔNG TIN HỌC SINH"
          this.student.id =data.id
          this.student.parent_id =data.parent_id
          this.student.name =data.name
          this.student.gender =data.gender
          this.student.school_level =data.school_level
          this.student.birthday =data.birthday
          this.student.select_school =data.school
          this.student.school = data.school
          this.student.note =data.note
          this.getSchools()
        }
      },
      showModalCheckin(item){
        this.modal_checkin.show =true
        this.modal_checkin.student_id = item.id
        this.modal_checkin.branch_id = this.parent.branch_id
        this.modal_checkin.checkin_at = ""
        this.modal_checkin.error_message=""
        this.modal_checkin.type_product=""
      },
      checkin(){
        let mess = "";
        let resp = true;
        if (this.modal_checkin.branch_id == "") {
          mess += " - Trung tâm checkin không được để trống<br/>";
          resp = false;
        }
        if (this.modal_checkin.checkin_at == "") {
          mess += " - Thời gian checkin không được để trống<br/>";
          resp = false;
        }
        if (this.modal_checkin.type_product == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_checkin.alert.color = 'danger'
          this.modal_checkin.alert.body = mess;
          this.modal_checkin.alert.active = true;
          return false;
        }
        const data = {
          owner_id : this.parent.owner_id,
          student_id: this.modal_checkin.student_id,
          branch_id: this.modal_checkin.branch_id,
          checkin_at: moment(this.modal_checkin.checkin_at).format('YYYY-MM-DD HH:mm'),
          type_product: this.modal_checkin.type_product
        };
        this.$vs.loading();
        axios.p(`/api/crm/students/checkin`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.modal_checkin.show =false
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadStudents();
        })
        .catch((e) => {
        });
      },
      showModalUpdateCheckin(item){
        this.modal_checkin.show =true
        this.modal_checkin.student_id = item.id
        this.modal_checkin.branch_id = item.checkin_branch_id
        this.modal_checkin.checkin_at = item.checkin_at
        this.modal_checkin.type_product = item.type_product
        this.modal_checkin.error_message=""
      },
      addStudent(){
        this.student.parent_id = this.parent.id
        let mess = "";
        let resp = true;
        if (this.student.name == "") {
          mess += " - Tên học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.student.birthday == "") {
          mess += " - Ngày sinh không được để trống<br/>";
          resp = false;
        }
        if (this.student.gender == "") {
          mess += " - Giới tính không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_student.alert.color = 'danger'
          this.modal_student.alert.body = mess;
          this.modal_student.alert.active = true;
          return false;
        }
        this.$vs.loading();
        axios.p(`/api/crm/students/add`,this.student)
        .then((response) => {
          this.modal_student.show = false
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadStudents();
        })
        .catch((e) => {
        });
      },
      loadStudents(){
        this.$vs.loading();
        axios.g(`/api/crm/students/get_all_data/${this.$route.params.id}`)
        .then((response) => {
          this.students = response.data;
          this.$vs.loading.close();
        })
        .catch((e) => {
        });
      },
    },
    filters: {
      genTextGender(item){
        let resp = ''
        if(item== 'M'){
          resp = 'Nam'
        }else{
          resp = 'Nữ'
        }
        return resp
      },
      genStudentStatus(item){
        let resp = ''
        if(item== 0){
          resp = 'Mới tạo'
        }else if(item==1){
          resp = 'Thêm mới checkin'
        }else if(item==2){
          resp = 'Đã đến checkin'
        }
        return resp
      },
    },
  }
</script>