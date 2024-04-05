<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function create(Request $request)
    {
        $schedule_info = u::first("SELECT * FROM law_schedules WHERE id = $request->law_schedule_id");
        $bbb = new BigBlueButton();
        $meetingID = uniqid('LAW_'.$schedule_info->id.'_');
        $meetingName = 'LAW TECH';
        $attendee_password = 'attendee_'.uniqid();
        $moderator_password = 'moderator_'.uniqid();
        $duration = 0;
        $urlLogout = 'https://law.dapanh.com/';
        $isRecordingTrue = true;

        $createMeetingParams = new CreateMeetingParameters($meetingID, $meetingName);
        $createMeetingParams->setAttendeePassword($attendee_password);
        $createMeetingParams->setModeratorPassword($moderator_password);
        $createMeetingParams->setDuration($duration);
        $createMeetingParams->setLogoutUrl($urlLogout);
        if ($isRecordingTrue) {
            $createMeetingParams->setRecord(true);
            $createMeetingParams->setAllowStartStopRecording(true);
            $createMeetingParams->setAutoStartRecording(true);
        }
        u::updateSimpleRow(array(
            'meeting_id' => $meetingID,
            'attendee_password' => $attendee_password,
            'moderator_password' => $moderator_password,
        ), array('id'=>$schedule_info->id), 'law_schedules');

        $response = $bbb->createMeeting($createMeetingParams);
        if ($response->getReturnCode() == 'FAILED') {
            $result = array(
                'status' => 0,
                'message' => 'Can\'t create room! please contact our administrator.'
            );
        } else {
            $joinMeetingParams = new JoinMeetingParameters($meetingID, Auth::user()->name, $attendee_password);
            $joinMeetingParams->setRedirect(true);
            $url = $bbb->getJoinMeetingURL($joinMeetingParams);
            $result = array(
                'status' => 1,
                'message' => 'ok',
                'redirect_url' => $url
            );
        }
        return response()->json($result);
    }

    public function join(Request $request)
    {
        $law_schedule_id = $request->law_schedule_id;
        $metting_info = u::first("SELECT s.meeting_id, s.attendee_password FROM law_schedules AS s WHERE s.id=$law_schedule_id");
        $bbb = new BigBlueButton();
        $meetingID = data_get($metting_info, 'meeting_id');
        $name = Auth::user()->name;
        $password = data_get($metting_info, 'attendee_password');

        $joinMeetingParams = new JoinMeetingParameters($meetingID, $name, $password);
        $joinMeetingParams->setRedirect(true);
        $url = $bbb->getJoinMeetingURL($joinMeetingParams);
        $result = array(
            'status' => 1,
            'message' => 'ok',
            'redirect_url' => $url
        );
        return response()->json($result);
    }

}
