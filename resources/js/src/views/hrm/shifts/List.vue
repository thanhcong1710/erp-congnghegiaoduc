<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Ca làm việc</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-plus"></i> Thêm ca làm</vs-button>
      </div>
      
      <vs-table :data="shifts" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Tên ca</vs-th>
          <vs-th>Giờ bắt đầu</vs-th>
          <vs-th>Giờ kết thúc</vs-th>
          <vs-th>Đi muộn cho phép (phút)</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>{{ data[indextr].name }}</vs-td>
            <vs-td>{{ data[indextr].start_time }}</vs-td>
            <vs-td>{{ data[indextr].end_time }}</vs-td>
            <vs-td>{{ data[indextr].late_grace_period }}</vs-td>
            <vs-td>
              <vs-chip :color="data[indextr].status === 1 ? 'success' : 'danger'">
                {{ data[indextr].status === 1 ? 'Hoạt động' : 'Tạm dừng' }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <vs-button size="small"   color="success" @click="openPopup(data[indextr])"><i class="fa fa-edit"></i></vs-button>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <vs-popup :title="isEdit ? 'Cập nhật Ca làm việc' : 'Thêm Ca làm việc'" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="Tên ca (*)" v-model="form.name" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-input type="time" class="w-full" label="Giờ bắt đầu (*)" v-model="form.start_time" />
          </div>
          <div class="vx-col w-1/2">
            <vs-input type="time" class="w-full" label="Giờ kết thúc (*)" v-model="form.end_time" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-input type="number" class="w-full" label="Cho phép đi muộn (phút)" v-model="form.late_grace_period" />
          </div>
          <div class="vx-col w-1/2">
            <vs-input type="number" class="w-full" label="Cho phép về sớm (phút)" v-model="form.early_leave_grace_period" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Trạng thái</label>
            <vs-select class="w-full mt-1" v-model="form.status">
              <vs-select-item :value="1" text="Hoạt động" />
              <vs-select-item :value="0" text="Tạm dừng" />
            </vs-select>
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" class="mr-2"><i class="fa fa-save"></i> Lưu lại</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
export default {
  data() {
    return {
      shifts: [],
      popupActive: false,
      isEdit: false,
      form: { id: null, name: '', start_time: '', end_time: '', late_grace_period: 0, early_leave_grace_period: 0, status: 1 }
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.p('/api/hrm/shifts/list').then(res => this.shifts = res.data).catch(err => console.error(err));
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = { ...item };
      } else {
        this.isEdit = false;
        this.form = { id: null, name: '', start_time: '', end_time: '', late_grace_period: 0, early_leave_grace_period: 0, status: 1 };
      }
      this.popupActive = true;
    },
    saveData() {
      let url = this.isEdit ? `/api/hrm/shifts/update/${this.form.id}` : '/api/hrm/shifts/add';
      axios.p(url, this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.popupActive = false;
          this.fetchData();
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message, color: 'danger'});
        }
      });
    }
  }
}
</script>
