<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Cấp bậc</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-plus"></i> Thêm cấp bậc</vs-button>
      </div>
      
      <vs-table :data="levels" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Tên cấp bậc</vs-th>
          <vs-th>Mô tả</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].name">{{ data[indextr].name }}</vs-td>
            <vs-td :data="data[indextr].description">{{ data[indextr].description }}</vs-td>
            <vs-td :data="data[indextr].status">
              <vs-chip :color="data[indextr].status == 1 ? 'success' : 'danger'">
                {{ data[indextr].status == 1 ? 'Hoạt động' : 'Tạm dừng' }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <vs-button size="small"   color="success" @click="openPopup(data[indextr])"><i class="fa fa-edit"></i></vs-button>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Popup Add/Edit -->
    <vs-popup :title="isEdit ? 'Cập nhật Cấp bậc' : 'Thêm Cấp bậc'" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="Tên cấp bậc (*)" v-model="form.name" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea label="Mô tả" v-model="form.description" />
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
      levels: [],
      popupActive: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        description: '',
        status: 1
      }
    }
  },
  created() {
    this.fetchLevels();
  },
  methods: {
    fetchLevels() {
      axios.p('/api/hrm/job-levels/list').then(res => {
        this.levels = res.data;
      }).catch(err => {
        console.error(err);
      });
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = { ...item };
      } else {
        this.isEdit = false;
        this.form = { id: null, name: '', description: '', status: 1 };
      }
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.name) {
        this.$vs.notify({title: 'Lỗi', text: 'Vui lòng nhập tên cấp bậc', color: 'danger'});
        return;
      }
      
      let url = this.isEdit ? `/api/hrm/job-levels/update/${this.form.id}` : '/api/hrm/job-levels/add';
      
      axios.p(url, this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.popupActive = false;
          this.fetchLevels();
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message, color: 'danger'});
        }
      }).catch(err => {
        this.$vs.notify({title: 'Lỗi', text: 'Có lỗi xảy ra', color: 'danger'});
      });
    }
  }
}
</script>
