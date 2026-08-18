<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmEmploymentHistory;
use App\Models\HrmEmployeeDocument;
use App\Models\HrmContract;

class EmployeeProfileController extends Controller
{
    public function list(Request $request)
    {
        $employees = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->get();
        return response()->json($employees);
    }
    
    public function add(Request $request) {
        $profile = HrmEmployeeProfile::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Created successfully', 'data' => $profile]);
    }
    
    public function show($id) {
        $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->where('user_id', $id)->first();
        if (!$profile) {
            $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->find($id);
        }
        
        $userId = $profile ? $profile->user_id : $id;
        
        $history = HrmEmploymentHistory::where('user_id', $userId)->orderBy('effective_date', 'desc')->get();
        $documents = HrmEmployeeDocument::where('user_id', $userId)->get();
        $contracts = HrmContract::where('user_id', $userId)->orderBy('start_date', 'desc')->get();
        
        return response()->json([
            'profile' => $profile,
            'history' => $history,
            'documents' => $documents,
            'contracts' => $contracts
        ]);
    }
    
    public function update(Request $request, $id) {
        $profile = HrmEmployeeProfile::find($id);
        if ($profile) {
            $profile->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Updated successfully']);
        }
        return response()->json(['status' => 0, 'message' => 'Not found'], 404);
    }

    public function addContract(Request $request) {
        $contract = HrmContract::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Đã thêm hợp đồng thành công', 'data' => $contract]);
    }

    public function addDocument(Request $request) {
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/documents'), $filename);
                $paths[] = '/uploads/documents/' . $filename;
            }
        }
        
        $docs = [];
        if (count($paths) > 0) {
            foreach ($paths as $index => $path) {
                $docTitle = count($paths) > 1 ? $request->title . ' - Phần ' . ($index + 1) : $request->title;
                $docs[] = HrmEmployeeDocument::create([
                    'user_id' => $request->user_id,
                    'title' => $docTitle,
                    'document_type' => $request->document_type,
                    'expiry_date' => $request->expiry_date,
                    'file_path' => url($path)
                ]);
            }
        } else {
            $docs[] = HrmEmployeeDocument::create($request->except('attachments'));
        }

        return response()->json(['status' => 1, 'message' => 'Đã tải lên tài liệu thành công', 'data' => $docs]);
    }
}
