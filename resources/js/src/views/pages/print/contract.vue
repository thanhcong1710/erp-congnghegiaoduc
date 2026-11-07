<template>
    <div class="erp-printing-detail">
        <div class="print-box">
            <div class="container">
                <div class="print-body print-container">
                    <div class="inner">
                        <!-- BEGIN FORM -->
                        <div class="print-header">
                            <div class="h-left" style="width: 30%">
                                <span class="logo"><img src="/images/logo.png" /></span>
                            </div>
                        </div>
                        <div class="print-content">
                            <h2 class="title">ĐƠN NHẬP HỌC ANT ENGLISH</h2>
                            
                            <h3 class="title">THÔNG TIN HỌC VIÊN</h3>
                            <div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Họ và tên:</span>
                                        {{contract.student_name}}
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Ngày sinh:</span>
                                            {{contract.date_of_birth}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Giới tính:</span>
                                            {{contract.gender}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Địa chỉ nhà:</span>
                                        {{contract.address}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Trường học hiện tại:</span>
                                        {{contract.school}}
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Đã từng học tiếng Anh tại:</span>
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">trong thời gian:</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Nguồn biết thông tin: </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="input-line">
                                        <span class="label">Họ và tên bố/mẹ: </span>
                                        {{contract.gud_name1}}
                                        <!-- <i class="doted"></i> -->
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Di động:</span>
                                            {{contract.gud_mobile1}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Email:</span>
                                            {{contract.gud_email1}}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Facebook: </span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <span class="label">Một vài lưu ý về học sinh (tính cách, điểm mạnh/yếu):</span>
                                        {{contract.note}}
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Tư vấn viên:</span>
                                            {{contract.ec_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Nhóm:</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="title mt-2">THÔNG TIN KHOÁ HỌC</h3>
                            <table class="table table-responsive-sm table-striped erp-table mb-2">
                                <thead>
                                    <tr>
                                        <th width="15%">KHÓA HỌC</th>
                                        <th  width="15%">THỜI GIAN HỌC<br>
                                        (Ca học)
                                        </th>
                                        <th width="15%">GÓI HỌC PHÍ<br>
                                        (Tháng)
                                        </th>
                                        <th width="15%">TỔNG MỨC HỌC PHÍ<br>
                                        (Sau chiết khấu)
                                        </th>
                                        <th width="15%">CHƯƠNG TRÌNH KHUYẾN MẠI</th>
                                        <th width="15%">TỔNG MỨC HỌC PHÍ THỰC ĐÓNG (VNĐ)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ contract.product_name }}</td>
                                        <td></td>
                                        <td>{{ contract.number_of_months }}</td>
                                        <td>{{contract.tuition_fee_price | formatMoney}}</td>
                                        <td>{{(contract.tuition_fee_price - (contract.tuition_receivable || contract.must_charge)) | formatMoney}}</td>
                                        <td> {{(contract.tuition_receivable || contract.must_charge) | formatMoney}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="contract.materials && contract.materials.length > 0" class="mt-2 mb-2">
                                <h4 class="title" style="font-size: 13px; margin-bottom: 4px;">GIÁO TRÌNH / HỌC LIỆU MUA KÈM</h4>
                                <table class="table table-responsive-sm table-striped erp-table mb-2">
                                    <thead>
                                        <tr>
                                            <th width="45%">TÊN GIÁO TRÌNH / HỌC LIỆU</th>
                                            <th width="10%">SL</th>
                                            <th width="15%">GIÁ NIÊM YẾT</th>
                                            <th width="15%">CHIẾT KHẤU</th>
                                            <th width="15%">THỰC ĐÓNG (VNĐ)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(m, idx) in contract.materials" :key="idx">
                                            <td>{{ m.material_name }}</td>
                                            <td style="text-align: center">{{ m.quantity }}</td>
                                            <td>{{ m.original_price | formatMoney }}</td>
                                            <td>{{ m.discount_percent }}%</td>
                                            <td>{{ m.final_price | formatMoney }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="contract.gift_note" class="mb-2">
                                <span class="label"><b>Quà tặng & Quyền lợi kèm theo:</b> {{ contract.gift_note }}</span>
                            </div>
                            <div>
                                <div>
                                    <div>
                                        <span class="label"><b>Số tiền đặt cọc: {{contract.total_charged | formatNumber}} VNĐ ({{contract.total_charged_text}})</b></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Số học phí còn phải hoàn thành: {{contract.debt_amount}} VNĐ</b></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div>
                                        <span class="label">Tôi, _______________________________, đồng ý cho Ant English sử dụng thông tin và hình ảnh của con tôi cho các tài liệu truyền thông với điều kiện Ant English sẽ không gây bất cứ ảnh hưởng nào cho bé.</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 50%"></div>
                                <div class="h-right" style="width: 50%" >
                                    
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 30%">
                                </div>
                                <div class="h-left" style="width: 30%">
                                </div>
                                <div class="h-right" style="width: 40%">
                                    <span class="dot-line">.......</span><span>, ngày</span><span class="dot-line">.......</span><span>tháng </span><span class="dot-line">.......</span><span>năm</span><span class="dot-line">.......</span>
                                    <div class="center"><b>Phụ Huynh Học Sinh</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from '../../../http/axios.js'
    export default {
        name : 'Contract',
        data() {
            return {
                contract: {},
            }
        },
        created() {
            axios.g(`/api/lms/contracts/print/${this.$route.params.id}`)
            .then(response => {
                this.contract = response.data
            })
            setTimeout(function () {
                window.print();
            }, 2000)
        },
        methods: {
        }
    }
</script>