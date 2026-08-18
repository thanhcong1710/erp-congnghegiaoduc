<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required',
        ]);

        $data = $request->all();
        $data['check_in_ip'] = $request->ip();
        
        $attendance = HrmAttendance::create($data);
        return response()->json(['status' => 1, 'message' => 'Check-in thành công', 'data' => $attendance]);
    }

    public function checkOut(Request $request, $id)
    {
        $attendance = HrmAttendance::findOrFail($id);
        
        $data = $request->only(['check_out', 'check_out_location', 'device_id', 'note']);
        $data['check_out_ip'] = $request->ip();

        $attendance->update($data);
        return response()->json(['status' => 1, 'message' => 'Check-out thành công', 'data' => $attendance]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
