<?php

namespace App\Http\Controllers;

use App\TeachingAssistant;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Providers\UtilityServiceProvider as u;

class TeachingAssistantsController extends Controller
{
    public function list(Request $request)
    {
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        
        $query = TeachingAssistant::select('teaching_assistants.*', 'users.hrm_id')
            ->leftJoin('users', 'teaching_assistants.user_id', '=', 'users.id');
        
        $total = $query->count();
        $list = $query->orderBy('teaching_assistants.id', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
            
        $data = u::makingPagination($list, $total, $page, $limit);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Check email if provided
            $email = $request->email;
            if (!empty($email) && User::where('email', $email)->exists()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Email đã tồn tại trong hệ thống'
                ]);
            }

            // Generate hrm_id
            $lastUser = User::where('hrm_id', 'like', 'TG%')->orderBy('id', 'desc')->first();
            $nextHrmId = 'TG001';
            if ($lastUser && $lastUser->hrm_id) {
                $lastNumber = (int)str_replace('TG', '', $lastUser->hrm_id);
                $nextHrmId = 'TG' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }

            $user = new User();
            $user->name = $request->full_name;
            $user->email = $email;
            $user->hrm_id = $nextHrmId;
            $user->phone = $request->phone;
            $user->password = Hash::make('12345678@'); // Default password
            $user->status = 1;
            $user->save();

            // Insert role 54
            DB::table('role_has_user')->insert([
                'user_id' => $user->id,
                'role_id' => 54
            ]);

            // Assign default branch
            $defaultBranch = DB::table('branches')->first();
            if ($defaultBranch) {
                DB::table('branch_has_user')->insert([
                    'user_id' => $user->id,
                    'branch_id' => $defaultBranch->id
                ]);
            }

            // Create Teaching Assistant
            $ta = new TeachingAssistant();
            $ta->user_id = $user->id;
            $ta->full_name = $request->full_name;
            $ta->facebook_link = $request->facebook_link;
            $ta->dob = $request->dob;
            $ta->phone = $request->phone;
            $ta->address = $request->address;
            $ta->bank_account = $request->bank_account;
            $ta->email = $email;
            $ta->lr_link = $request->lr_link;
            $ta->sw_link = $request->sw_link;
            $ta->profile_link = $request->profile_link;
            $ta->start_date = $request->start_date;
            $ta->status = isset($request->status) ? $request->status : 1;
            $ta->save();

            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Thêm mới trợ giảng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $ta = TeachingAssistant::find($id);
            if (!$ta) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy trợ giảng'
                ]);
            }

            $ta->status = $request->status;
            $ta->save();

            // Update user status if necessary
            if ($ta->user_id) {
                $user = User::find($ta->user_id);
                if ($user) {
                    $user->status = $request->status;
                    $user->save();
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $ta = TeachingAssistant::find($id);
            if (!$ta) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Không tìm thấy trợ giảng'
                ]);
            }

            // Check email if provided and changed
            $email = $request->email;
            if (!empty($email) && $email !== $ta->email) {
                if (User::where('email', $email)->exists()) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Email đã tồn tại trong hệ thống'
                    ]);
                }
            }

            // Update user
            if ($ta->user_id) {
                $user = User::find($ta->user_id);
                if ($user) {
                    $user->name = $request->full_name;
                    $user->email = $email;
                    $user->phone = $request->phone;
                    $user->status = isset($request->status) ? $request->status : 1;
                    $user->save();
                }
            }

            // Update Teaching Assistant
            $ta->full_name = $request->full_name;
            $ta->facebook_link = $request->facebook_link;
            $ta->dob = $request->dob;
            $ta->phone = $request->phone;
            $ta->address = $request->address;
            $ta->bank_account = $request->bank_account;
            $ta->email = $email;
            $ta->lr_link = $request->lr_link;
            $ta->sw_link = $request->sw_link;
            $ta->profile_link = $request->profile_link;
            $ta->start_date = $request->start_date;
            $ta->status = isset($request->status) ? $request->status : 1;
            $ta->save();

            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Cập nhật trợ giảng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function assignClassTA(Request $request)
    {
        try {
            $class_id = $request->class_id;
            $ta_id = $request->ta_id; // Array or string
            if (is_array($ta_id)) {
                $ta_id = implode(',', $ta_id);
            }

            DB::table('classes')
                ->where('id', $class_id)
                ->update(['ta_id' => $ta_id]);

            return response()->json([
                'status' => 1,
                'message' => 'Phân công trợ giảng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function getAllActive(Request $request)
    {
        $list = TeachingAssistant::where('status', 1)->get(['user_id as id', 'full_name']);
        return response()->json($list);
    }
}
