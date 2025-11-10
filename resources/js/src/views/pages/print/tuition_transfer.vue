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
                            <div style="text-align: center; margin-bottom: 15px;">
                                <h3 style="margin-bottom: 0px;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
                                <p><b>Độc lập – Tự do – Hạnh phúc</b></p>    
                                <p><b>------------------</b></p>
                            </div>
                            <h2 class="title">ĐƠN CHUYỂN HỌC PHÍ</h2>
                            
                            <h3 class="title">1.	Thông tin học sinh chuyển phí:</h3>
                            <div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh chuyển phí:</span>
                                            {{tuition_transfer.from_gud_name1}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số điện thoại:</span>
                                            {{tuition_transfer.from_gud_mobile1}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên học sinh chuyển phí:</span>
                                            {{tuition_transfer.from_student_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Mã LMS:</span>
                                            {{tuition_transfer.from_lms_id}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Số buổi học còn lại:</span>
                                        {{tuition_transfer.left_real_sessions}}
                                        <span class="label">&nbsp;(buổi chính thức) và </span>
                                        {{tuition_transfer.left_bonus_sessions}}
                                        <span class="label">&nbsp;(buổi học bổng)</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Số học phí còn lại:</span>
                                        {{tuition_transfer.left_amount | formatCurrency}}
                                    </div>
                                </div>
                            </div>
                            <h3 class="title mt-2">2.	Thông tin học sinh nhận phí</h3>
                            <div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên phụ huynh nhận phí:</span>
                                            {{tuition_transfer.to_gud_name1}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Số điện thoại:</span>
                                            {{tuition_transfer.to_gud_mobile1}}
                                        </div>
                                    </div>
                                </div>
                                <div class="vx-row">
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Họ và tên học sinh nhận phí:</span>
                                            {{tuition_transfer.to_student_name}}
                                        </div>
                                    </div>
                                    <div class="w-1/2">
                                        <div class="input-line">
                                            <span class="label">Mã LMS:</span>
                                            {{tuition_transfer.to_lms_id}}
                                        </div>
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label">Số buổi học còn lại:</span>
                                        {{tuition_transfer.to_real_sessions}}
                                        <span class="label">&nbsp;(buổi chính thức) và </span>
                                        {{tuition_transfer.to_bonus_sessions}}
                                        <span class="label">&nbsp;(buổi học bổng)</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Lý do chuyển phí: </b><p>{{tuition_transfer.note}}</p></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label">Kèm theo: Giấy khai sinh của hai học sinh và căn cước công dân của 2 phụ huynh</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div>
                                        <span class="label"><b>Cam kết của phụ huynh:</b> Chúng tôi đồng ý rằng việc chuyển học phí đồng nghĩa với việc các buổi học còn lại của học sinh trên hệ thống sẽ bị dừng lại ngay lập tức tại thời điểm làm đơn yêu cầu chuyển phí. Việc chuyển học phí sẽ được thực hiện chuyển toàn phần số học phí còn lại chưa sử dụng, không chuyển lẻ từng phần. Lãi trả góp (nếu có) bao gồm trong học phí sẽ được hệ thống tính toán và khấu trừ trước khi chuyển đổi số tiền còn lại cho phụ huynh khác.</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 30%">
                                    <div class="center"><b>Phụ huynh chuyển phí</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <br><br> <br>
                                    <p class="center"><span>Ngày</span><span class="dot-line">.......</span><span>/ </span><span class="dot-line">.......</span><span>/</span><span class="dot-line">.......</span></p>
                                </div>
                                <div class="h-left" style="width: 40%">
                                </div>
                                <div class="h-right" style="width: 30%">
                                    <div class="center"><b>Phụ huynh nhận phí</b></div>
                                    <p class="center"><i>(Ký và ghi rõ họ tên)</i></p>
                                    <br><br> <br>
                                    <p class="center"><span>Ngày</span><span class="dot-line">.......</span><span>/ </span><span class="dot-line">.......</span><span>/</span><span class="dot-line">.......</span></p>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div>
                                        <p><b><i>Lưu ý:</i></b></p>
                                        <p><i>Trường hợp học sinh đang theo học tại Ant English chuyển phí cho học sinh khác đang theo học tại Ant English hoặc chuyển cho anh chị em ruột thì áp dụng chuyển ngang số buổi</i></p>
                                        <p><i>Trường hợp học sinh đang theo học tại Ant English muốn chuyển phí cho học sinh mới, thì quy đổi số buổi dựa trên học phí còn lại theo mức học phí niêm yết khóa học 12 tháng đang áp dụng tại thời điểm chuyển phí.</i></p>
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
                tuition_transfer: {},
            }
        },
        created() {
            axios.g(`/api/lms/tuition_transfer/print/${this.$route.params.id}`)
            .then(response => {
                this.tuition_transfer = response.data
            })
            setTimeout(function () {
                window.print();
            }, 2000)
        },
        methods: {
        }
    }
</script>