<template>
  <div>
    <vs-card>
      <div class="flex items-center justify-between mb-4">
        <h4>Quản lý Chức danh</h4>
        <vs-button class="mr-3 mb-2" color="success"   @click="openPopup()"><i class="fa fa-plus"></i> Thêm chức danh</vs-button>
      </div>
      
      <vs-table :data="titles" search pagination :max-items="10">
        <template slot="thead">
          <vs-th>Tên chức danh</vs-th>
          <vs-th>Phòng ban liên kết</vs-th>
          <vs-th>Mô tả</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td :data="data[indextr].name">{{ data[indextr].name }}</vs-td>
            <vs-td :data="data[indextr].department ? data[indextr].department.name : ''">
              {{ data[indextr].department ? data[indextr].department.name : 'Dùng chung (Không gắn phòng ban)' }}
            </vs-td>
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
    <vs-popup :title="isEdit ? 'Cập nhật Chức danh' : 'Thêm Chức danh'" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="Tên chức danh (*)" v-model="form.name" />
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Phòng ban liên kết</label>
            <vs-select class="w-full mt-1" v-model="form.department_id" autocomplete>
              <vs-select-item :value="null" text="Dùng chung (Không gắn)" />
              <vs-select-item :key="index" :value="item.id" :text="item.name" v-for="(item,index) in departments" />
            </vs-select>
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
      titles: [],
      departments: [],
      popupActive: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        department_id: null,
        description: '',
        status: 1
      }
    }
  },
  created() {
    this.fetchTitles();
    this.fetchDepartments();
  },
  methods: {
    fetchTitles() {
      axios.p('/api/hrm/job-titles/list').then(res => {
        this.titles = res.data;
      }).catch(err => {
        console.error(err);
      });
    },
    fetchDepartments() {
      // Need a flat list of departments here for the select box.
      // Assuming /api/hrm/departments/list returns the list.
      axios.p('/api/hrm/departments/list').then(res => {
        // Flat mapping if it returns nested tree, but for simplicity assuming array.
        // If it's a tree, we need to flatten it. Let's just use it as is if it's flat, or flatten if it has children.
        this.departments = this.flattenDepartments(res.data);
      }).catch(err => console.error(err));
    },
    flattenDepartments(deps, prefix = '') {
      let flat = [];
      deps.forEach(d => {
        flat.push({ id: d.id, name: prefix + d.name });
        if (d.children && d.children.length > 0) {
          flat = flat.concat(this.flattenDepartments(d.children, prefix + '-- '));
        }
      });
      return flat;
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = { ...item };
      } else {
        this.isEdit = false;
        this.form = { id: null, name: '', department_id: null, description: '', status: 1 };
      }
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.name) {
        this.$vs.notify({title: 'Lỗi', text: 'Vui lòng nhập tên chức danh', color: 'danger'});
        return;
      }
      
      let url = this.isEdit ? `/api/hrm/job-titles/update/${this.form.id}` : '/api/hrm/job-titles/add';
      
      axios.p(url, this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.popupActive = false;
          this.fetchTitles();
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
