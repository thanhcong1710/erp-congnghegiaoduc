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
        $list = u::query("SELECT * FROM tmp_hanam WHERE status = 0 AND (crm_parent_id = 0 OR crm_parent_id IS NULL)");
        foreach($list AS $row){
            //insert crm_parents
            if(data_get($row,'parent_mobile')){
                $crm_parent_id = u::insertSimpleRow(array(
                    'name'=>data_get($row,'parent_name'),
                    'mobile_1' => substr(data_get($row,'parent_mobile'),0,1) == '0' ? data_get($row,'parent_mobile') : '0'.data_get($row,'parent_mobile'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'branch_id' => data_get($row, 'branch_id'),
                    'owner_id' => 17,
                ), 'crm_parents');
                //insert crm_students
                $crm_student_id = u::insertSimpleRow(array(
                    'parent_id'=>$crm_parent_id,
                    'name'=>data_get($row,'student_name'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => 3,
                ), 'crm_students');
                u::updateSimpleRow(array('crm_parent_id'=>$crm_parent_id, 'crm_student_id' => $crm_student_id), array('id'=>data_get($row,'id')) ,'tmp_hanam');
            }
            echo data_get($row,'id')."_crm_students/";
        }

        $list = u::query("SELECT * FROM tmp_hanam WHERE status = 0 AND (student_id = 0 OR student_id IS NULL)");
        foreach($list AS $row){
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
                'branch_id' => data_get($row, 'branch_id'),
                'status' => 1,
                'avatar_url' => '/images/common/avatar-boy.svg'
            ), 'students');
            u::insertSimpleRow(array(
                'student_id' => $lms_student_id,
                'branch_id' => data_get($row, 'branch_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 1
            ), 'term_student_user');

            u::updateSimpleRow(array('status'=>3, 'lms_id' =>$lms_student_id), array('id'=> data_get($row, 'crm_student_id')), 'crm_students');
            $last_lms_code = str_pad((string)$lms_student_id, 6, '0', STR_PAD_LEFT);
            $lms_code = config('app.prefix_student_code').$last_lms_code;
            u::updateSimpleRow(array('lms_code'=>$lms_code), array('id'=>$lms_student_id), 'students');
            u::updateSimpleRow(array('student_id'=>$lms_student_id), array('id'=>data_get($row,'id')), 'tmp_hanam');
            echo data_get($row,'id')."_students/";
        }

        $list = u::query("SELECT * FROM tmp_hanam WHERE status = 0 AND (contract_id = 0 OR contract_id IS NULL)");
        foreach($list AS $row){
            //insert contracts
            $tuition_fee = u::first("SELECT * FROM tuition_fee WHERE id = ".data_get($row, 'tuition_fee_id'));
            $contract_id = u::insertSimpleRow(array(
                'type' => 1,
                'student_id' => data_get($row, 'student_id'), 
                'branch_id' => data_get($row, 'branch_id'),
                'product_id' => data_get($tuition_fee, 'product_id'),
                'tuition_fee_id' => data_get($row, 'tuition_fee_id'),
                'init_tuition_fee_id' => data_get($row, 'tuition_fee_id'),
                'init_tuition_fee_amount' => data_get($tuition_fee, 'amount'),
                'init_tuition_fee_receivable' => data_get($tuition_fee, 'receivable'),
                'init_tuition_fee_session' => data_get($tuition_fee, 'session'),
                'init_total_charged'=>data_get($row, 'so_tien_con_lai'),
                'must_charge' => data_get($row, 'so_tien_con_lai'),
                'total_charged'=>data_get($row, 'so_tien_con_lai'),
                'debt_amount' => 0,
                'total_sessions' => (int)data_get($row, 'so_buoi_tra_phi'),
                'real_sessions' => (int)data_get($row, 'so_buoi_tra_phi'),
                'bonus_sessions' => (int)data_get($row, 'so_buoi_hoc_bong'),
                'summary_sessions' => (int)data_get($row, 'so_buoi_tra_phi') + (int)data_get($row, 'so_buoi_hoc_bong'),
                'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
                'start_date'=> data_get($row, 'start_date'),
                'created_at'=>date('Y-m-d H:i:s'),
                'status' => 3,
                'count_recharge' => 0,
            ), 'contracts');
            u::updateSimpleRow(array('contract_id'=>$contract_id, 'status'=>1), array('id'=>data_get($row,'id')), 'tmp_hanam' );
            echo data_get($row,'id')."_contract/";
        }
        return "ok";
    }
    
}
