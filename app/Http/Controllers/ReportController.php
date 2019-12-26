<?php

namespace App\Http\Controllers;

use App\DepartmentManagement;
use App\Remark;
use App\Report;
use App\Task;
use App\TaskHistory;
use App\TaskUser;
use App\TopicTaskRelationship;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Topic;
use App\User;

class ReportController extends Controller
{
    //

    public function reports(Request $request)
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
                $type = $request->input('type');
                $type = empty($type) ? 0 : $type;

                $departments = DepartmentManagement::whereHas('organization', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })->get();

                $reports = Report::where('company_id',$company_id)->where('report_type',$type)->whereNull('deleted_at');

                $report_date = $request->input('report_date');
                if($report_date)
                {
                    $report_date = date('Y-m-d',strtotime($report_date));
                    $reports->whereDate('created_at','=',$report_date);
                }


                $reports = $reports->orderBy('id', 'desc')->get();
                foreach ($reports as $report)
                {
                    $return_result[] = array(
                        'id' => $report->id,
                        'title' => $report->title,
                        'contents' => $report->contents,
                        'created_by' => $report->created_by,
                        'report_date' => $report->report_date,
                        'department_id' => $report->dept_id,
                        'next_actions' => $report->next_actions,
                        'report_type' => $report->report_type,
                        'created_at' => $report->created_at
                    );
                }

                foreach ($departments as $department)
                {
                    $return_departments[] = array(
                        'id' => $department->id,
                        'short_name' => $department->department_short_name,
                        'full_name' => $department->department_name
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
                'list' =>$return_result,
                'departments'=>$return_departments
            )
        ], $code);


    }

    public function report_detail(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');
        $return_pending_tasks = array();
        $return_remarks = array();

        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'report_id' => 'required',
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

            $report_id = $request->input('report_id');
            $report = Report::findOrFail($report_id);
            $relationships = TopicTaskRelationship::where('type', 2)->where('listing_id', $report_id)->get();
            $arr_rshp = array();
            foreach ($relationships as $relationship) {
                if($relationship->task_id > 0) {
                    array_push($arr_rshp, $relationship->task_id);
                }
            }

            $pending_tasks = Task::where('company_id', $company_id)
                //->whereDate('created_at', '<', date('Y-m-d', strtotime($report->report_date)))
                ->whereIn('id', $arr_rshp)
                ->where('status',0)
                ->get();
              //  print_r($pending_tasks);
            $return_result = array(
                'id' => $report->id,
                'title' => $report->title,
                'contents' => $report->contents,
                'created_by' => $report->created_by,
                'report_date' => $report->report_date,
                'department_id' => $report->dept_id,
                'next_actions' => $report->next_actions,
                'report_type' => $report->report_type,
                'created_at' => $report->created_at

            );

            foreach ($pending_tasks as $pending_task)
            {
                $user_avatars = array();
                if ($pending_task->users)
                {
                    foreach ($pending_task->users as $user)
                    {
                       // echo "<br>".$user->id."==".$user->user->id;
                        if($user->user)
                        {
                            $avatar = $user->user && $user->user->avatar ? json_decode($user->user->avatar, true) : null;
                            $team_user['user_id'] = $user->user->id;
                            $team_user['user_name'] = $user->user->name;
                            if($avatar)
                                $team_user['user_avatar'] = $avatar[0]['download_link'];


                            array_push($user_avatars,$team_user);
                        }

                    }
                }
                $return_pending_tasks = array(
                    'id' => $pending_task->id,
                    'status' => $pending_task->status,
                    'title' =>$pending_task->title,
                    'due_date' =>$pending_task->due_date,
                    'owners' =>$user_avatars,
                );
            }


            //get remarks
            if($report->remarks)
            {
                foreach($report->remarks as $rem)
                {
                    $return_remarks[] = array(
                        'created_by' => $rem->createdBy ? $rem->createdBy->name : '',
                        'created_at' => $rem->created_at,
                        'comments' =>$rem->comments

                    );
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
            'pending_tasks' =>(Object)$return_pending_tasks,
            'remarks' => (Object)$return_remarks

        ], $code);
    }

    public function save_report(Request $request)
    {
        $validator = validator::make($request->all(), [
            'title'     => 'required',
            'user_id' => 'required',
            'company_id'=>'required',
            'type'	=> 'required',
            'department_id'	=> 'required'
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
            if ($request->input('is_update')) {
                $report = Report::find($request->input('listing_id'));
                $report->updated_at = date('Y-m-d H:i:s');
            }else{
                $report = new Report();
                $report->created_by = $request->input('user_id');
                $report->company_id = $request->input('company_id');
                $report->created_at = date('Y-m-d H:i:s');
            }

            $report->title = $request->input('title');
            $report->report_type = $request->input('type');
            $report->dept_id = $request->input('department_id');
            $report->contents = $request->input('report_details');
            $report->next_actions  = $request->input('next_actions');
            $report->report_date = date('Y-m-d H:i:s');

            if ($report->save()) {
                $listing_id = $report->id;


                //assign topics
                if ($request->input('topics')) {
                    foreach ($request->input('topics') as $topic) {
                        $task_topic = new TopicTaskRelationship();
                        //$task_topic->task_id = $task->id;
                        $task_topic->topic_id = $topic;
                        $task_topic->listing_id = $listing_id;
                        $task_topic->type = 2;
                        $task_topic->company_id = $company_id;
                        $task_topic->created_by = $user_id;
                        $task_topic->created_at = date('Y-m-d H:i:s');
                        $task_topic->save();
                    }
                }

                //update tasks TODO


                //save tasks TODO



                $message = $request->input('is_update') ? 'Report updated.' : 'Report created.';
                $success_status = true;
                $code = 200;
            }

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


    public function save_remarks(Request $request)
    {
        $validator = validator::make($request->all(), [
           'user_id' => 'required',
            'company_id'=>'required',
            'type'	=> 'required'

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
            if ($request->input('is_update')) {
                $remark = Remark::findOrFail($request->input('remark_id'));
                $remark->updated_at = date('Y-m-d H:i:s');
            }
            else {
                $remark = new Remark();
                $remark->created_by = $user_id;
                $remark->company_id = $company_id;
                $remark->created_at = date('Y-m-d H:i:s');
            }

            $remark->title = $request->input('title');
            $remark->comments = $request->input('comments');
            $remark->type = $request->input('type');
            $remark->listing_id = $request->input('listing_id');
            if ($remark->save()) {
                $message = $request->input('is_update') ? 'Remarks updated.' : 'Remarks Added.';
                $success_status = true;
                $code = 200;
            }

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
