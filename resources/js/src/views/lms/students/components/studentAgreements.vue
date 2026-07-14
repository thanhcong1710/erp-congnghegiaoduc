<template>
  <div class="vs-component vs-con-table stripe vs-table-primary">
    <div class="con-tablex vs-table--content">
      <div class="vs-con-tbody vs-table--tbody ">
        <table class="vs-table vs-table--tbody-table">
          <thead class="vs-table--thead">
            <tr>
              <th colspan="1" rowspan="1" class="text-center">STT</th>
              <th colspan="1" rowspan="1">Học sinh</th>
              <th colspan="1" rowspan="1">Trung tâm</th>
              <th colspan="1" rowspan="1">Hợp đồng</th>
              <th colspan="1" rowspan="1">Đóng phí</th>
              <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
              <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in agreements" :key="index">
            <td class="td vs-table--td text-center">{{ index + 1 }}</td>
            <td class="td vs-table--td">
              <p><strong>{{ item.name }}</strong></p>
              <p>Mã: {{item.lms_code}}</p>
            </td>
            <td class="td vs-table--td">
              <p><strong>{{ item.branch_name }}</strong></p>
              <p>EC: {{ item.ec_name }}</p>
            </td>
            <td class="td vs-table--td">
              <p>Mã:  <router-link target="_blank" :to="`/lms/agreements/${item.agreement_id}/detail`" ><strong>{{ item.code }}</strong></router-link></p>
              <p>Gói phí: {{ item.tuition_fee_name }}</p>
            </td>
            <td class="td vs-table--td">
              <p>Giá: <strong>{{ item.must_charge | formatMoney }}</strong></p>
              <p>Công nợ: {{ item.debt_amount | formatMoney }}</p>
            </td>
            <td class="td vs-table--td text-center">{{ item.label_status}}</td>
            <td class="td vs-table--td text-center list-action"> 
                <router-link target="_blank" :to="`/lms/agreements/${item.agreement_id}/detail`" >
                  <vs-button size="small"><i class="fa fa-eye"></i></vs-button>
                </router-link> 
                <vs-button size="small" style="background: rgb(19 128 213) !important"><i class="fa-solid fa-print"></i></vs-button>
            </td>
          </tr>
          <tr v-if="agreements.length === 0">
            <td colspan="7" class="text-center py-4">Không có dữ liệu</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'

  export default {
    props: {
      student_info: {
        type: Object,
        required: true,
      }
    },
    data() {
      return {
        agreements: [],
        delete_id:'',
      }
    },
    created() {
      this.getData();
    },
    methods: {
      getData() {
        const data = {
            keyword: "",
            branch_id: "",
            start_date: "",
            end_date: "",
            is_class: -1,
            student_id: this.$route.params.id,
            pagination: { limit: 500, cpage: 1 }
          }

        this.$vs.loading()
        axios.p('/api/lms/agreements/list-by-student', data)
          .then((response) => {
            this.$vs.loading.close()
            this.agreements = response.data.list
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmDelete (item) {
        this.delete_id = item.agreement_id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn hủy hợp đồng nhập học - ${item.code}?`,
          accept: this.deleteContract,
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      deleteContract(){
        const data = {
          agreement_id: this.delete_id,
        };
        this.$vs.loading();
        axios.p(`/api/lms/agreements/delete`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.getData();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
    }
  }
</script>
