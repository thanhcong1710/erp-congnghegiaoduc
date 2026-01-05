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
                            <h2 class="title">ĐƠN TÁI PHÍ ANT ENGLISH</h2>
                            
                            <h3 class="title">THÔNG TIN HỌC VIÊN</h3>
                            <div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên học sinh:</span>
                                            {{contract.student_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Nickname:</span>
                                            {{contract.nick}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Mã LMS:</span>
                                            {{contract.lms_id}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Mã CRM:</span>
                                            {{contract.lms_code}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Lớp học hiện tại:</span>
                                        {{contract.cls_name}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Họ và tên Phụ huynh:</span>
                                        {{contract.gud_name1}}
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
                                        <span class="label">Chủ nhiệm lớp: </span>
                                        {{contract.cm_name}}
                                    </div>
                                </div>
                            </div>
                            <h3 class="title mt-2">THÔNG TIN KHOÁ HỌC</h3>
                            <table class="table table-responsive-sm table-striped erp-table mb-2">
                                <thead>
                                    <tr>
                                        <th width="15%">CẤP ĐỘ</th>
                                        <th  width="15%">THỜI GIAN HẾT PHÍ</th>
                                        <th width="15%">THỜI GIAN BẮT ĐẦU – KẾT THÚC DỰ KIẾN</th>
                                        <th width="15%">NGUYÊN GIÁ<br>
                                            ( Giá gốc )
                                        </th>
                                        <th width="15%">CHƯƠNG TRÌNH KHUYẾN MẠI</th>
                                        <th width="15%">TỔNG MỨC HỌC PHÍ THỰC ĐÓNG<br>
                                            ( Giá sau chiết khấu )</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ contract.product_name }}</td>
                                        <td>{{ contract.enrolment_last_date }}</td>
                                        <td>{{contract.start_date }} - {{ contract.end_date }}</td>
                                        <td>{{contract.tuition_fee_price | formatMoney}}</td>
                                        <td>{{contract.tuition_fee_price - contract.must_charge | formatMoney}}</td>
                                        <td>{{contract.must_charge | formatMoney}}</td>
                                    </tr>
                                </tbody>
                            </table>
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
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 30%">
                                    <div class="center"><b>Chủ nhiệm lớp</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                </div>
                                <div class="h-left" style="width: 40%">
                                    <div class="center"><b>Phụ Huynh Học Sinh</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                </div>
                                <div class="h-right" style="width: 30%">
                                    <div class="center"><b>Giám đốc Trung tâm</b></div>
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