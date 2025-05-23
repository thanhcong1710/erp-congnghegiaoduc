<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importdata:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        u::query("UPDATE tmp_import AS t
                JOIN (
                    SELECT lms_id
                    FROM tmp_import
                    GROUP BY lms_id
                    HAVING COUNT(id) > 1
                ) AS dup ON dup.lms_id = t.lms_id
                SET t.status = 2
                WHERE t.so_buoi_con_lai = 0");
        $list = u::query("SELECT * FROM tmp_import WHERE status = 0 AND (crm_parent_id = 0 OR crm_parent_id IS NULL)");
        foreach($list AS $row){
            //insert crm_parents
            if(data_get($row,'parent_mobile')){
                $parent_info = u::first("SELECT * FROM crm_parents WHERE mobile_1 LIKE '%".data_get($row,'parent_mobile')."'");
                if($parent_info){
                    $crm_parent_id = data_get($parent_info, 'id');
                } else{
                    $crm_parent_id = u::insertSimpleRow(array(
                        'name'=>data_get($row,'parent_name'),
                        'mobile_1' => substr(data_get($row,'parent_mobile'),0,1) == '0' ? data_get($row,'parent_mobile') : '0'.data_get($row,'parent_mobile'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'branch_id' => data_get($row, 'branch_id'),
                        'owner_id' => 1,
                    ), 'crm_parents');
                }
                $student_info = u::first("SELECT * FROM crm_students WHERE name = '".data_get($row,'student_name')."' AND parent_id = ".$crm_parent_id);
                if($student_info){
                    $crm_student_id = data_get($student_info, 'id');
                } else{
                    //insert crm_students
                    $crm_student_id = u::insertSimpleRow(array(
                        'parent_id'=>$crm_parent_id,
                        'name'=>data_get($row,'student_name'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'status' => 3,
                    ), 'crm_students');
                }
                u::updateSimpleRow(array('crm_parent_id'=>$crm_parent_id, 'crm_student_id' => $crm_student_id), array('id'=>data_get($row,'id')) ,'tmp_import');
            }
            echo data_get($row,'id')."_crm_students/";
        }

        $list = u::query("SELECT * FROM tmp_import WHERE status = 0 AND (student_id = 0 OR student_id IS NULL)");
        foreach($list AS $row){
            $branchInfo = u::first("SELECT * FROM branches WHERE name = '".data_get($row, 'branch_name')."'");
            if($branchInfo){
                //insert students
                $arr_name = u::explodeName(data_get($row, 'student_name'));
                $lms_student_id = u::insertSimpleRow(array(
                    'lms_code' => '',
                    'name' => data_get($row, 'student_name'),
                    'firstname' => data_get($arr_name, 'firstname'),
                    'midname' => data_get($arr_name, 'midname'),
                    'lastname' => data_get($arr_name, 'lastname'),
                    'gud_mobile1' => substr(data_get($row,'parent_mobile'),0,1) == '0' ? data_get($row,'parent_mobile') : '0'.data_get($row,'parent_mobile'),
                    'gud_name1' => data_get($row,'parent_name'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'branch_id' => data_get($branchInfo, 'id'),
                    'status' => 1,
                    'avatar_url' => '/images/common/avatar-boy.svg',
                    'lms_id' => data_get($row, 'lms_id'),
                ), 'students');
                u::insertSimpleRow(array(
                    'student_id' => $lms_student_id,
                    'branch_id' => data_get($branchInfo, 'id'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 1
                ), 'term_student_user');

                u::updateSimpleRow(array('status'=>3, 'lms_id' =>$lms_student_id), array('id'=> data_get($row, 'crm_student_id')), 'crm_students');
                $last_lms_code = str_pad((string)$lms_student_id, 6, '0', STR_PAD_LEFT);
                $lms_code = config('app.prefix_student_code').$last_lms_code;
                u::updateSimpleRow(array('lms_code'=>$lms_code), array('id'=>$lms_student_id), 'students');
                u::updateSimpleRow(array('student_id'=>$lms_student_id), array('id'=>data_get($row,'id')), 'tmp_import');
            }
            echo data_get($row,'id')."_students/";
        }

        $list = u::query("SELECT * FROM tmp_import WHERE status = 0 AND (contract_id = 0 OR contract_id IS NULL)");
        foreach($list AS $row){
            $branchInfo = u::first("SELECT * FROM branches WHERE name = '".data_get($row, 'branch_name')."'");
            if($branchInfo){
                //insert contracts
                $tuition_fee_id = $this->getTuitionFee(data_get($row, 'product_name'), data_get($row, 'tuition_fee_name'));
                if($tuition_fee_id){
                    $tuitionFeeInfo = u::first("SELECT * FROM tuition_fee WHERE id = ".$tuition_fee_id);
                    if(strtoupper(data_get($row,'status_name')) == strtoupper('Deposit')){
                        $contract_id = u::insertSimpleRow(array(
                            'type' => 1,
                            'student_id' => data_get($row, 'student_id'), 
                            'branch_id' => data_get($branchInfo, 'id'),
                            'product_id' => data_get($tuitionFeeInfo, 'product_id'),
                            'tuition_fee_id' => data_get($tuitionFeeInfo, 'id'),
                            'init_tuition_fee_id' => data_get($tuitionFeeInfo, 'id'),
                            'init_tuition_fee_amount' => data_get($row, 'must_charge'),
                            'init_tuition_fee_receivable' => data_get($tuitionFeeInfo, 'receivable'),
                            'init_tuition_fee_session' => data_get($tuitionFeeInfo, 'session'),
                            'init_total_charged'=>data_get($row, 'total_charge'),
                            'must_charge' => data_get($row, 'must_charge'),
                            'total_charged'=>data_get($row, 'total_charge'),
                            'debt_amount' => data_get($row, 'must_charge') - data_get($row, 'total_charge'),
                            'total_sessions' => (int)data_get($row, 'so_buoi_tra_phi'),
                            'real_sessions' => (int)data_get($row, 'so_buoi_tra_phi'),
                            'bonus_sessions' => (int)data_get($row, 'so_buoi_hoc_bong'),
                            'summary_sessions' => 0,
                            'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
                            'start_date'=> data_get($row, 'start_date'),
                            'created_at'=>date('Y-m-d H:i:s'),
                            'status' => 2,
                            'count_recharge' => 0,
                        ), 'contracts');
                        u::updateDoneSessions($contract_id);
                        u::updateSimpleRow(array('contract_id'=>$contract_id, 'status'=>1), array('id'=>data_get($row,'id')), 'tmp_import' );
                    }else{
                        $real_sessions = (int)data_get($row, 'so_buoi_con_lai') -  (int)data_get($row, 'so_buoi_hoc_bong') > 0 ? (int)data_get($row, 'so_buoi_con_lai') -  (int)data_get($row, 'so_buoi_hoc_bong') : 0;
                        $so_tien_con_lai =  data_get($row, 'so_buoi_tra_phi') ? (((int)data_get($row, 'total_charge') * (int)$real_sessions) / (int)data_get($row, 'so_buoi_tra_phi')) : 0;
                        $contract_id = u::insertSimpleRow(array(
                            'type' => 1,
                            'student_id' => data_get($row, 'student_id'), 
                            'branch_id' => data_get($branchInfo, 'id'),
                            'product_id' => data_get($tuitionFeeInfo, 'product_id'),
                            'tuition_fee_id' => data_get($tuitionFeeInfo, 'id'),
                            'init_tuition_fee_id' => data_get($tuitionFeeInfo, 'id'),
                            'init_tuition_fee_amount' => data_get($row, 'must_charge'),
                            'init_tuition_fee_receivable' => data_get($tuitionFeeInfo, 'receivable'),
                            'init_tuition_fee_session' => data_get($tuitionFeeInfo, 'session'),
                            'init_total_charged'=>data_get($row, 'total_charge'),
                            'must_charge' => data_get($row, 'must_charge'),
                            'total_charged'=>$so_tien_con_lai,
                            'debt_amount' => 0,
                            'total_sessions' => (int)data_get($row, 'so_buoi_con_lai'),
                            'real_sessions' => (int)data_get($row, 'so_buoi_con_lai') -  (int)data_get($row, 'so_buoi_hoc_bong') > 0 ? (int)data_get($row, 'so_buoi_con_lai') -  (int)data_get($row, 'so_buoi_hoc_bong') : 0,
                            'bonus_sessions' => (int)data_get($row, 'so_buoi_hoc_bong') > (int)data_get($row, 'so_buoi_con_lai') ? (int)data_get($row, 'so_buoi_con_lai') : (int)data_get($row, 'so_buoi_hoc_bong'),
                            'summary_sessions' => (int)data_get($row, 'so_buoi_con_lai'),
                            'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
                            'start_date'=> data_get($row, 'start_date'),
                            'created_at'=>date('Y-m-d H:i:s'),
                            'status' => 3,
                            'count_recharge' => 0,
                        ), 'contracts');
                        u::updateDoneSessions($contract_id);
                        u::updateSimpleRow(array('contract_id'=>$contract_id, 'status'=>1), array('id'=>data_get($row,'id')), 'tmp_import' );
                    }
                }
            }
            echo data_get($row,'id')."_contract/";
        }
        $list = u::query("SELECT * FROM tmp_import");
        foreach($list AS $row){
            u::updateDoneSessions(data_get($row,'contract_id'));
            echo "updateDoneSessions_".data_get($row,'id')."/";
        }
        return "ok";
    }
    
    private function getTuitionFee($product_name, $tuition_fee_name)
    {
        if(strtoupper($product_name) == strtoupper('April')){
            switch ((int)$tuition_fee_name) {
                case '3':
                    return 32;
                case '6':
                    return 33;
                case '12':
                    return 34;
                default:
                    return null;
            }

        } elseif(strtoupper($product_name) == strtoupper('i-garten')){
            switch ((int)$tuition_fee_name) {
                case '3':
                    return 37;
                case '6':
                    return 38;
                case '12':
                    return 39;
                default:
                    return null;
            }
        }
    }
}
