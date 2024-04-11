

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <h3>{{role.name}}</h3>
      <div class="mt-3">
        <label for="permission" style="font-size:16px">Chọn phân hệ và quyền <span style="color: red;">*</span></label>
        <div class="vx-row">
          <div class="vx-col md:w-1/2 w-full mt-3" v-for="(item, index) in group_permissions" :key="index">
            <vs-checkbox v-model="item.active" class="mt-1" @input="changeGroup(index)"><strong>{{item.description}}</strong></vs-checkbox>
            <div v-for="(item_per, index_per) in item.permissions" :key="index_per" class="w-full pl-8">
              <vs-checkbox v-model="item_per.active" class="mt-1">{{item_per.description}}</vs-checkbox>
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
        location.reload();
      },
      savePermissions(){
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
      changeGroup($k){
        this.group_permissions[$k].permissions.forEach((element) => {
          element.active = this.group_permissions[$k].active
        });
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
</style>