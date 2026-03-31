

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <h3>{{role.name}}</h3>
      <div class="mt-3">
        <label for="permission" style="font-size:16px">Chọn phân hệ và quyền <span style="color: red;">*</span></label>
        <div class="vx-row">
          <div class="vx-col md:w-1/2 w-full mt-3" v-for="(item, index) in group_permissions" :key="index">
            <vs-checkbox v-model="item.active" class="mt-1 group-checkbox" @input="changeGroup(index)">
              <strong>{{item.description}}</strong>
            </vs-checkbox>
            <div v-for="(item_per, index_per) in item.permissions" :key="index_per" class="w-full pl-8">
              <vs-checkbox 
                v-model="item_per.active" 
                class="mt-1 permission-checkbox" 
                @input="changePermission(index, index_per)"
              >
                {{item_per.description}}
              </vs-checkbox>
              <div v-if="item_per.listSub && item_per.listSub.length > 0" v-for="(item_sub_per, index_sub_per) in item_per.listSub" :key="index_sub_per" class="w-full pl-8">
                <vs-checkbox 
                  v-model="item_sub_per.active" 
                  class="mt-1 sub-permission-checkbox"
                  @input="changeSubPermission(index, index_per, index_sub_per)"
                >
                  {{item_sub_per.description}}
                </vs-checkbox>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-10" style="overflow: hidden">
          <vs-button color="rgb(154 167 169)" type="filled" @click="reset">Hủy</vs-button>
          <vs-button color="success" type="filled" @click="savePermissions">Lưu</vs-button>
        </div>
      </div>
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'

  export default {
    components: {
      vSelect
    },
    data() {
      return {
        role : '',
        group_permissions : ''
      }
    },
    methods: {
      getData() {
        this.$vs.loading()
        axios.g(`/api/roles/${this.$route.params.role_id}`)
          .then((response) => {
            this.role = response.data.role_info
            this.group_permissions = response.data.group_permissions
            this.$vs.loading.close()
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      reset(){
        this.$router.push('/settings/roles')
      },
      savePermissions(){
        this.$vs.loading()
        axios.p(`/api/roles/permissions`,{
          role_id: this.role.id,
          group_permissions: this.group_permissions
        })
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close()
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        })
      },
      // Khi check/uncheck group, áp dụng cho tất cả permissions và sub-permissions
      changeGroup($k){
        const groupActive = this.group_permissions[$k].active
        this.group_permissions[$k].permissions.forEach((element) => {
          element.active = groupActive
          // Cập nhật cả sub-permissions nếu có
          if (element.listSub && element.listSub.length > 0) {
            element.listSub.forEach((sub) => {
              sub.active = groupActive
            })
          }
        });
      },
      // Khi check/uncheck permission, cập nhật sub-permissions và kiểm tra group
      changePermission(groupIndex, permIndex) {
        const permission = this.group_permissions[groupIndex].permissions[permIndex]
        
        // Nếu permission được check, check tất cả sub-permissions
        // Nếu permission được uncheck, uncheck tất cả sub-permissions
        if (permission.listSub && permission.listSub.length > 0) {
          permission.listSub.forEach((sub) => {
            sub.active = permission.active
          })
        }
        
        // Cập nhật trạng thái group
        this.updateGroupStatus(groupIndex)
      },
      // Khi check/uncheck sub-permission, kiểm tra parent permission và group
      changeSubPermission(groupIndex, permIndex, subIndex) {
        const permission = this.group_permissions[groupIndex].permissions[permIndex]
        
        // Nếu tất cả sub-permissions được check, check parent permission
        // Nếu có ít nhất 1 sub-permission được uncheck, uncheck parent permission
        if (permission.listSub && permission.listSub.length > 0) {
          const allSubActive = permission.listSub.every(sub => sub.active)
          permission.active = allSubActive
        }
        
        // Cập nhật trạng thái group
        this.updateGroupStatus(groupIndex)
      },
      // Cập nhật trạng thái active của group dựa trên permissions
      updateGroupStatus(groupIndex) {
        const group = this.group_permissions[groupIndex]
        let allActive = true
        
        group.permissions.forEach((perm) => {
          if (!perm.active) {
            allActive = false
          }
          // Kiểm tra cả sub-permissions
          if (perm.listSub && perm.listSub.length > 0) {
            perm.listSub.forEach((sub) => {
              if (!sub.active) {
                allActive = false
              }
            })
          }
        })
        
        group.active = allActive
      }
    },
    created() {
      this.getData();
    },
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input{
    width: 360px;
  }
}

/* Cải thiện visual hierarchy */
#page-roles-list .group-checkbox {
  font-weight: bold;
  margin-bottom: 8px;
}

#page-roles-list .permission-checkbox {
  margin-left: 20px;
}

#page-roles-list .sub-permission-checkbox {
  margin-left: 40px;
  font-size: 14px;
  color: #666;
}

#page-roles-list .vs-checkbox {
  margin-top: 4px !important;
  margin-bottom: 4px !important;
}
</style>