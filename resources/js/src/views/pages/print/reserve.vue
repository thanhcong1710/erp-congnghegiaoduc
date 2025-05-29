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
                            <div style="text-align: center;">
                                <h3 class="title mb-1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
                                <h3 class="mb-1">Độc lập – Tự do – Hạnh phúc</h3>
                                <div>--------------------</div>
                            </div>
                            <h2 class="title mt-2">ĐƠN BẢO LƯU HỌC PHÍ</h2>
                            <div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Họ và tên phụ huynh:</span>
                                        {{reserve.gud_name1}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Họ và tên học sinh:</span>
                                        {{reserve.student_name}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Mã LMS:</span>
                                        {{reserve.lms_id}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Khóa học:</span>
                                        {{reserve.product_name}}
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Ngày nộp học phí:</span>
                                            {{reserve.charge_date}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số giao dịch: </span>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số phí:</span>
                                            {{reserve.must_charge}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Tổng số buổi học theo gói phí: </span>
                                            {{reserve.init_tuition_fee_session}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số buổi đã học:</span>
                                            {{reserve.total_done_sessions}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số buổi chưa học xin bảo lưu: </span>
                                            {{reserve.left_sessions}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Số tiền chưa sử dụng xin bảo lưu:</span>
                                        {{reserve.left_amount}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Thời gian bảo lưu dự kiến: {{reserve.start_date}} đến {{reserve.end_date}}</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <span class="label">Lý do xin bảo lưu: {{reserve.note}}</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <span class="label">Nếu học sinh bảo lưu không giữ chỗ, sau khi học sinh quay lại sẽ phải làm bài kiểm tra lại đầu vào và không được giữ chỗ lớp học, ca học hiện tại. Tôi cam đoan sẽ không khiếu nại gì sau này</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex" class="mt-3">
                                <div class="h-left" style="width: 25%;border-right: 1px solid #959191;">
                                    <div class="center"><b>Giám đốc dự án</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <p class="center" style="margin-top: 76px;"><i>Ngày…/…/…</i></p>
                                </div>
                                <div class="h-left" style="width: 25%;border-right: 1px solid #959191;">
                                    <div class="center"><b>Phòng kế toán</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <p class="center" style="margin-top: 76px;"><i>Ngày…/…/…</i></p>
                                </div>
                                <div class="h-left" style="width: 25%;border-right: 1px solid #959191;">
                                    <div class="center"><b>Phòng CSKH</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <p class="center" style="margin-top: 76px;"><i>Ngày…/…/…</i></p>
                                </div>
                                <div class="h-right" style="width: 25%">
                                    <div class="center"><b>Phụ Huynh Học Sinh</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <p class="center" style="margin-top: 76px;"><i>Ngày…/…/…</i></p>
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
                reserve: {},
            }
        },
        created() {
            axios.g(`/api/lms/reserves/print/${this.$route.params.id}`)
            .then(response => {
                this.reserve = response.data
            })
            setTimeout(function () {
                window.print();
            }, 2000)
        },
        methods: {
        }
    }
</script>