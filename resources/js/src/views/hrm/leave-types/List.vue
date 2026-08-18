<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Loại nghỉ phép</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-plus"></i> Thêm loại phép</vs-button>
      </div>
      
      <vs-table :data="types" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Tên loại phép</vs-th>
          <vs-th>Số ngày tối đa/năm</vs-th>
          <vs-th>Hưởng lương</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>{{ data[indextr].name }}</vs-td>
            <vs-td>{{ data[indextr].days_per_year }}</vs-td>
            <vs-td>
              <vs-chip :color="data[indextr].is_paid ? 'success' : 'danger'">
                {{ data[indextr].is_paid ? 'Có' : 'Không' }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <vs-chip :color="data[indextr].status === 1 ? 'primary' : 'dark'">
                {{ data[indextr].status === 1 ? 'Đang áp dụng' : 'Tạm dừng' }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <vs-button size="small"   color="success" @click="openPopup(data[indextr])"><i class="fa fa-edit"></i></vs-button>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <vs-popup :title="isEdit ? 'Cập nhật Loại phép' : 'Thêm Loại phép'" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="Tên loại phép (*)" v-model="form.name" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input type="number" class="w-full" label="Số ngày tối đa trong năm (*)" v-model="form.days_per_year" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-checkbox v-model="form.is_paid">Nghỉ hưởng lương</vs-checkbox>
          </div>
          <div class="vx-col w-1/2">
             <vs-checkbox v-model="form.status">Đang áp dụng</vs-checkbox>
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
      types: [],
      popupActive: false,
      isEdit: false,
      form: { id: null, name: '', days_per_year: 0, is_paid: 1, status: 1 }
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.p('/api/hrm/leave-types/list').then(res => this.types = res.data).catch(err => console.error(err));
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = { ...item, is_paid: item.is_paid == 1, status: item.status == 1 };
      } else {
        this.isEdit = false;
        this.form = { id: null, name: '', days_per_year: 0, is_paid: true, status: true };
      }
      this.popupActive = true;
    },
    saveData() {
      this.form.is_paid = this.form.is_paid ? 1 : 0;
      this.form.status = this.form.status ? 1 : 0;
      let url = this.isEdit ? `/api/hrm/leave-types/update/${this.form.id}` : '/api/hrm/leave-types/add';
      
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
