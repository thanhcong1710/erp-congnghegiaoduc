<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Lịch làm việc (Xếp ca)</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-calendar-plus"></i> Xếp ca mới</vs-button>
      </div>
      
      <vs-table :data="assignments" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Nhân sự</vs-th>
          <vs-th>Ca làm việc</vs-th>
          <vs-th>Ngày</vs-th>
          <vs-th>Loại ca</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>{{ data[indextr].user ? data[indextr].user.name : '' }}</vs-td>
            <vs-td>{{ data[indextr].shift ? data[indextr].shift.name : '' }}</vs-td>
            <vs-td>{{ data[indextr].date }}</vs-td>
            <vs-td>
              <vs-chip :color="data[indextr].is_overtime ? 'warning' : 'primary'">
                {{ data[indextr].is_overtime ? 'Ca làm thêm' : 'Ca chính' }}
              </vs-chip>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <vs-popup title="Xếp ca nhân viên" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="ID Nhân viên (*)" v-model="form.user_id" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Ca làm việc</label>
            <vs-select class="w-full mt-1" v-model="form.shift_id">
              <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item,index) in shifts" />
            </vs-select>
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input type="date" class="w-full" label="Ngày làm (*)" v-model="form.date" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-checkbox v-model="form.is_overtime">Đây là ca làm thêm (OT)</vs-checkbox>
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="primary" class="mr-2">Xếp ca</vs-button>
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
      assignments: [],
      shifts: [],
      popupActive: false,
      form: { user_id: '', shift_id: null, date: '', is_overtime: 0 }
    }
  },
  created() {
    this.fetchData();
    axios.p('/api/hrm/shifts/list').then(res => this.shifts = res.data);
  },
  methods: {
    fetchData() {
      axios.p('/api/hrm/shift-assignments/list').then(res => this.assignments = res.data).catch(err => console.error(err));
    },
    openPopup() {
      this.form = { user_id: '', shift_id: null, date: '', is_overtime: 0 };
      this.popupActive = true;
    },
    saveData() {
      this.form.is_overtime = this.form.is_overtime ? 1 : 0;
      axios.p('/api/hrm/shift-assignments/add', this.form).then(res => {
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
