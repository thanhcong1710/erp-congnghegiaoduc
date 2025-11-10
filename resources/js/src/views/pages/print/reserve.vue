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
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh:</span>
                                            {{reserve.gud_name1}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số điện thoại: </span>
                                            {{reserve.gud_mobile1}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên học sinh:</span>
                                            {{reserve.student_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Mã LMS:</span>
                                            {{reserve.lms_id}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Trình độ:</span>
                                            {{reserve.product_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Lịch học:</span>
                                            {{reserve.lich_hoc}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Số buổi học còn lại:</span>
                                        {{reserve.real_sessions}}
                                        <span class="label">&nbsp;(buổi chính thức) và </span>
                                        {{reserve.bonus_sessions}}
                                        <span class="label">&nbsp;(buổi học bổng)</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Số buổi học xin bảo lưu:</span>
                                        {{reserve.session}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Thời gian bảo: {{reserve.start_date}} đến {{reserve.end_date}}</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <span class="label">Lý do xin bảo lưu chi tiết: {{reserve.note}}</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <span class="label">Kèm theo:</span>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                       <p><b>Đề nghị và cam kết của phụ huynh:</b> Đề nghị Ant English cho con của tôi được bảo lưu học phí theo thời gian trên. Tôi xin cam kết nếu con tôi không quay lại học theo đúng thời gian này, số buổi học sẽ tiếp tục được tính phí trên hệ thống kể từ ngày kết thúc bảo lưu.</p>
                                       <p>Nếu học sinh bảo lưu không giữ chỗ, sau khi học sinh quay lại sẽ phải làm bài kiểm tra lại đầu vào và không được giữ chỗ lớp học, ca học hiện tại. Tôi cam đoan sẽ không khiếu nại gì sau này.</p>
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
                            <div>
                                <div>
                                    <div>
                                        <p><b><i>Lưu ý:</i></b></p>
                                        <p><i>Đơn bảo lưu sẽ được xét duyệt và trả lời trong 3 ngày làm việc</i></p>
                                        <p><i>Phụ huynh thông báo 03 ngày trước khi quay lại học</i></p>
                                        <p><i>Phòng CSKH scan và lưu trữ theo quy định</i></p>
                                    </div>
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