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
                            <div class="h-right" style="border:none">
                                <h4>CÔNG TY CỔ PHẦN GIÁO DỤC LOGIC LAB</h4>
                                <p><i class="fa fa-map-marker"></i> Lô B1.1, Số 2 đường Đặng Thai Mai , Phường Quảng An, Quận Tây Hồ, 
                                    Thành phố Hà Nội, Việt Nam.</p>
                            </div>
                            <div class="h-right" style="border:none; text-align:center;width: 35%">
                                <p><b>Mẫu số 01 - TT</b></p>
                                <p>(Ban hành theo TT số 200/2014/TT-BTC </p>
                                <p>Ngày 22/12/2014 của Bộ trưởng BTC)</p>
                            </div>
                        </div>
                        <div class="print-content">
                            <h2 class="title" style="margin-bottom:0px">PHIẾU THU TIỀN HỌC</h2>
                            <p style="text-align: center;margin-bottom: 20px">{{contract.text_1}}</p>
                            
                            <div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Người nộp:</b></span>
                                        {{contract.gud_name1}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Địa chỉ:</b></span>
                                        {{contract.address}}
                                    </div>
                                </div>
                                <div >
                                    <div class="input-line">
                                        <span class="label"><b>Lý do nộp:</b></span>
                                        {{contract.note}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Số tiền:</b></span>
                                        {{contract.text_amount}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Bằng chữ:</b></span>
                                        {{contract.text_amount_words}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>HTTT:</b></span>
                                        {{contract.text_3}}
                                    </div>
                                </div>
                                <div>
                                    <div class="input-line">
                                        <span class="label"><b>Kèm theo:</b></span>
                                        <i class="doted"></i>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 50%"></div>
                                <div class="h-right" style="width: 50%" >
                                    <span class="dot-line">..........</span><span>, ngày</span><span class="dot-line">..........</span><span>tháng </span><span class="dot-line">..........</span><span>năm</span><span class="dot-line">...........</span>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="h-left" style="width: 33%">
                                    <div class="center">Giám đốc TT</div>
                                    <p class="center">(Ký, họ tên, đóng dấu)</p>
                                </div>
                                <div class="h-left" style="width: 33%">
                                    <div class="center">Người lập biểu</div>
                                    <p class="center">(Ký, họ tên)</p>
                                </div>
                                <div class="h-right" style="width: 34%">
                                    <div class="center">Người nộp tiền</div>
                                    <p class="center">(Ký, họ tên)</p>
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
    import u from '../../../until/helper.js'
    export default {
        name : 'Contract',
        data() {
            return {
                contract: {},
                student: {},
                gender : true,
                std_class : {},
                is_ucrea : false,
                is_bright : false,
                is_blkhole : false,
                sources:[],
            }
        },
        created() {

            setTimeout(function () {
                window.print();
            }, 1000)
        },
        methods: {
        }
    }
</script>
<style>
.contract_new p{
    margin-bottom: 0px;
    font-size: 14px;
    line-height: 18px;
}
@page {
    size: A5;
    margin: 10mm;
    size: landscape; 
    }
    body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 0;
    }
    .erp-printing-detail .print-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    }
@media print {
    @page {
    size: A5;
    margin: 10mm;
    size: landscape; 
    }
    body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 0;
    }
    .erp-printing-detail .print-container {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    }
}
</style>