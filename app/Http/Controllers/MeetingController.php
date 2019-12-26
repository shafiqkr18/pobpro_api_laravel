<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\MeetingAttendant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function meetings(Request $request)
    {

        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';

        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');

        $return_departments = array();
        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'false',
                'message'=>$validator->errors(),
                'code'=>400,
                'result'=>''
            ], 400);
        }

        try{

            $meetings = Meeting::where('company_id',$company_id)->whereNull('deleted_at');

            $meetings = $meetings->orderBy('id', 'desc')->get();

            foreach ($meetings as $meeting)
            {
                $return_result[] = array(
                    'id' => $meeting->id,
                    'subject' => $meeting->subject,
                    'summary' => $meeting->summary,
                    'mom_contents' => $meeting->mom_contents,
                    'meeting_date' => $meeting->meeting_date,
                    'meeting_time' => $meeting->meeting_time,
                    'location' => $meeting->location,
                    'created_at' => $meeting->created_at
                );
            }


            $code = 200;
            $success_status = true;
            $message = 'success result';
        }catch (\Exception $e)
        {
            $message = $e->getMessage();

        }

        return response()->json([
            'status'=>$success_status,
            'message'=>$message,
            'code'=>$code,
            'result'=>array(
                'list' =>$return_result
            )
        ], $code);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function meeting_detail(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');
        $return_pending_tasks = array();
        $meeting_attendants = array();

        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'meeting_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'false',
                'message'=>$validator->errors(),
                'code'=>400,
                'result'=>''
            ], 400);
        }


        try{

            $meeting_id = $request->input('meeting_id');
            $meeting = Meeting::findOrFail($meeting_id);




            $return_result = array(
                'id' => $meeting->id,
                'reference_no' => $meeting->reference_no,
                'subject' => $meeting->subject,
                'summary' => $meeting->summary,
                'mom_contents' => $meeting->mom_contents,
                'department' => $meeting->department ? $meeting->department->department_short_name : '',
                'host' => $meeting->host ? $meeting->host->name : '',
                'location' => $meeting->location,
                'created_at' => $meeting->created_at,
                'created_by' => $meeting->createdBy ? $meeting->createdBy->name : '',
                'meeting_date' => $meeting->meeting_date,
                'meeting_time' => $meeting->meeting_time,

            );


            //attendants
            if ($meeting->attendants)
            {
                foreach ($meeting->attendants as $att)
                {
                    if($att->attendant)
                    {
                        $avatar = $att->attendant->avatar?  json_decode($att->attendant->avarat, true) : null;
                        $team_user['user_id'] = $att->attendant->id;
                        $team_user['user_name'] = $att->attendant->name;
                        if($avatar)
                        {
                            $team_user['user_avatar'] = $avatar[0]['download_link'];
                        }
                        array_push($meeting_attendants,$team_user);
                    }
                }
            }


            $code = 200;
            $success_status = true;
            $message = 'success result';

        }catch (\Exception $e)
        {
            $message = $e->getMessage();

        }

        return response()->json([
            'status'=>$success_status,
            'message'=>$message,
            'code'=>$code,
            'result'=>(Object)$return_result,
            'meeting_attendants' =>(Object)$meeting_attendants
            //'remarks' => (Object)$return_remarks

        ], $code);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_meeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject'     => 'required',
            'meeting_date' => 'required',
            'meeting_time' => 'required',
            'location'     => 'required',
            'department_id' => 'required',
            'host_id' => 'required',
            'user_id' => 'required',
            'company_id'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'false',
                'message'=>$validator->errors(),
                'code'=>400,
                'result'=>''
            ], 400);
        }

        $code = 400;
        $success_status = false;
        $return_result = '';
        $message = 'Error Occurred!';
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');


        try{

            if($request->input('is_update')){
                $meeting = Meeting::find($request->input('listing_id'));
                $meeting->updated_at = date('Y-m-d H:i:s');
            }else{
                $meeting = new Meeting();
                $meeting->created_by = $user_id;
                $meeting->company_id = $company_id;
                $meeting->created_at = date('Y-m-d H:i:s');
            }

            $meeting->reference_no = 'M'.date('ymdHis');
            $meeting->subject = $request->input('subject');
            $meeting->location = $request->input('location');
            $meeting->dept_id = $request->input('department_id');
            $meeting->host_id = $request->input('host_id');
            $meeting->summary = $request->input('summary');
            $meeting->meeting_date = db_date_format($request->input('meeting_date'));
            $meeting->meeting_time = $request->input('meeting_time');

            if($meeting->save())
            {
                $meeting_id = $meeting->id;

                if ($request->input('attendants')) {
                    $attendants_array = explode(',', $request->input('attendants'));
                    if (!empty($attendants_array)) {
                        //if update then delete the existing records
                        if($request->input('is_update')){
                            $a = MeetingAttendant::where('meeting_id',$meeting_id)->delete();
                        }
                        foreach ($attendants_array as $attendant)
                        {
                            if(isset($attendant) && !empty($attendant))
                            {
                                $meeting_attendant = new MeetingAttendant();
                                $meeting_attendant->meeting_id = $meeting_id;
                                $meeting_attendant->attendant_id = $attendant;
                                $meeting_attendant->save();
                            }
                        }

                    }
                }
            }

            $success_status = true;
            $message = $request->input('is_update') ? " Meeting Updated!" : "Meeting Created!";
            $code = 200;

        }catch (\Exception $e)
        {
            $message = $e->getMessage();

        }

        return response()->json([
            'status'=>$success_status,
            'message'=>$message,
            'code'=>$code,
            'result'=>$return_result
        ], $code);




    }
}
