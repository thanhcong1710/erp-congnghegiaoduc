<?php

namespace App\Http\Controllers;

use App\TeachingAssistant;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

            \App\Services\TATutoringService::generateTutorings($class_id);

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
        $list = DB::table('teaching_assistants as ta')
            ->leftJoin('users as u', 'u.id', '=', 'ta.user_id')
            ->where('ta.status', 1)
            ->select('ta.user_id as id', 'ta.full_name', 'u.hrm_id')
            ->orderBy('ta.full_name', 'asc')
            ->get();
        return response()->json($list);
    }

    public function listGroupTutorings(Request $request)
    {
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $class_name = isset($request->class_name) ? trim($request->class_name) : $keyword;
        $ta_id = isset($request->ta_id) ? $request->ta_id : '';
        $is_inspected = isset($request->is_inspected) && $request->is_inspected !== '' ? $request->is_inspected : null;
        $is_note_read = isset($request->is_note_read) && $request->is_note_read !== '' ? $request->is_note_read : null;
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $class_id = isset($request->class_id) ? $request->class_id : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        
        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);

        $query = DB::table('ta_group_tutorings as tgt')
            ->leftJoin('users as u', 'u.id', '=', 'tgt.ta_id')
            ->leftJoin('classes as c', 'c.id', '=', 'tgt.class_id')
            ->leftJoin('products as p', 'p.id', '=', 'tgt.product_id')
            ->leftJoin('branches as b', 'b.id', '=', 'c.branch_id')
            ->select('tgt.*', 'u.name as ta_name', 'u.hrm_id as ta_hrm_id', 'c.cls_name', 'p.name as product_name', 'b.name as branch_name');

        $user = Auth::user();
        $is_ta_role = false;
        if ($user) {
            $is_ta_role = DB::table('role_has_user')->where('user_id', $user->id)->where('role_id', 54)->exists();
        }

        if ($is_ta_role && $user) {
            $query->where(function ($q) use ($user) {
                $q->where('tgt.ta_id', $user->id)
                  ->orWhereRaw("FIND_IN_SET('{$user->id}', c.ta_id)");
            });
        }

        if (!empty($branch_id)) {
            $query->whereIn('c.branch_id', $branch_id);
        }
        if ($class_name !== '') {
            $query->where('c.cls_name', 'LIKE', "%$class_name%");
        }
        if ($ta_id !== '' && $ta_id !== null) {
            $query->where('tgt.ta_id', $ta_id);
        }
        if ($is_inspected !== null && $is_inspected !== '') {
            $query->where('tgt.is_inspected', (int) $is_inspected);
        }
        if ($is_note_read !== null && $is_note_read !== '') {
            $query->where('tgt.is_note_read', (int) $is_note_read);
        }
        if ($class_id !== '') {
            $query->where('tgt.class_id', $class_id);
        }
        if ($start_date !== '') {
            $query->where('tgt.expected_date', '>=', $start_date);
        }
        if ($end_date !== '') {
            $query->where('tgt.expected_date', '<=', $end_date);
        }

        $total = $query->count();
        $list = $query->orderBy('tgt.expected_date', 'asc')->offset($offset)->limit($limit)->get();

        return response()->json([
            'list' => $list,
            'paging' => [
                'cpage' => $page,
                'limit' => $limit,
                'total' => $total
            ],
            'is_ta' => $is_ta_role
        ]);
    }

    public function updateGroupTutoring(Request $request, $id)
    {
        try {
            $updateData = [
                'support_request' => $request->support_request,
                'lp_check_status' => $request->lp_check_status,
                'record_link' => $request->record_link,
                'record_note' => $request->record_note,
                'is_inspected' => $request->is_inspected ? 1 : 0,
                'is_note_read' => $request->is_note_read ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($request->has('expected_date')) {
                $updateData['expected_date'] = $request->expected_date ?: null;
            }
            if ($request->has('expected_time')) {
                $updateData['expected_time'] = $request->expected_time;
            }
            DB::table('ta_group_tutorings')->where('id', $id)->update($updateData);
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function listPersonalTutorings(Request $request)
    {
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $class_name = isset($request->class_name) ? trim($request->class_name) : $keyword;
        $ta_id = isset($request->ta_id) ? $request->ta_id : '';
        $is_inspected = isset($request->is_inspected) && $request->is_inspected !== '' ? $request->is_inspected : null;
        $is_note_read = isset($request->is_note_read) && $request->is_note_read !== '' ? $request->is_note_read : null;
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $class_id = isset($request->class_id) ? $request->class_id : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        
        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);

        $user = Auth::user();
        $is_ta_role = false;
        if ($user) {
            $is_ta_role = DB::table('role_has_user')->where('user_id', $user->id)->where('role_id', 54)->exists();
        }

        $query = DB::table('ta_one_on_one_tutorings as tpt')
            ->leftJoin('users as u', 'u.id', '=', 'tpt.ta_id')
            ->leftJoin('classes as c', 'c.id', '=', 'tpt.class_id')
            ->leftJoin('students as s', 's.id', '=', 'tpt.student_id')
            ->leftJoin('products as p', 'p.id', '=', 'tpt.product_id')
            ->leftJoin('branches as b', 'b.id', '=', 'c.branch_id')
            ->select('tpt.*', 'u.name as ta_name', 'u.hrm_id as ta_hrm_id', 'c.cls_name', 's.name as student_name', 'p.name as product_name', 'b.name as branch_name');

        if ($is_ta_role && $user) {
            $query->where(function ($q) use ($user) {
                $q->where('tpt.ta_id', $user->id)
                  ->orWhereRaw("FIND_IN_SET('{$user->id}', c.ta_id)");
            });
        }

        if (!empty($branch_id)) {
            $query->whereIn('c.branch_id', $branch_id);
        }
        if ($class_name !== '') {
            $query->where('c.cls_name', 'LIKE', "%$class_name%");
        }
        if ($ta_id !== '' && $ta_id !== null) {
            $query->where('tpt.ta_id', $ta_id);
        }
        if ($is_inspected !== null && $is_inspected !== '') {
            $query->where('tpt.is_inspected', (int) $is_inspected);
        }
        if ($is_note_read !== null && $is_note_read !== '') {
            $query->where('tpt.is_note_read', (int) $is_note_read);
        }
        if ($class_id !== '') {
            $query->where('tpt.class_id', $class_id);
        }
        if ($start_date !== '') {
            $query->where('tpt.expected_date', '>=', $start_date);
        }
        if ($end_date !== '') {
            $query->where('tpt.expected_date', '<=', $end_date);
        }

        $total = $query->count();
        $list = $query->orderBy('tpt.expected_date', 'asc')->offset($offset)->limit($limit)->get();

        return response()->json([
            'list' => $list,
            'paging' => [
                'cpage' => $page,
                'limit' => $limit,
                'total' => $total
            ],
            'is_ta' => $is_ta_role
        ]);
    }

    public function updatePersonalTutoring(Request $request, $id)
    {
        try {
            $updateData = [
                'support_request' => $request->support_request,
                'lp_check_status' => $request->lp_check_status,
                'record_link' => $request->record_link,
                'record_note' => $request->record_note,
                'is_inspected' => $request->is_inspected ? 1 : 0,
                'is_note_read' => $request->is_note_read ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($request->has('expected_date')) {
                $updateData['expected_date'] = $request->expected_date ?: null;
            }
            if ($request->has('expected_time')) {
                $updateData['expected_time'] = $request->expected_time;
            }
            DB::table('ta_one_on_one_tutorings')->where('id', $id)->update($updateData);
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
