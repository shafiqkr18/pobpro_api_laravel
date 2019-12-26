<?php

namespace App\Http\Controllers;

use App\Task;
use App\TaskUser;
use App\Topic;
use App\TopicTaskRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class TaskController extends Controller
{
    //
    public function all_tasks(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $related_topics = array();
        $message = 'Error Occurred!';
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
            $tasks = Task::where('deleted_at', null)->where('company_id',$request->input('company_id'));

            $due_date = $request->input('due_date');
            if($due_date)
            {
                $due_date = date('Y-m-d',strtotime($due_date));
                $tasks->whereDate('due_date','=',$due_date);
            }


            if($request->input('type'))
            {
                $type = $request->input('type') == 'EMPTY' ? [] : explode(',', $request->input('type'));
                $tasks->whereIn('type', $type);
            }
            if($request->input('status'))
            {
                $status = $request->input('status') == 'EMPTY' ? [] : explode(',', $request->input('status'));
                $tasks->whereIn('status', $status);
            }



            if($request->input('topics'))
            {
                $topics = $request->input('topics') == 'EMPTY' ? [] : explode(',', $request->input('topics'));
                $task_ids = TopicTaskRelationship::where('company_id',$request->input('company_id'))
                    ->where('task_id' , '>' , 0)
                    ->whereIn('topic_id',$topics)->pluck('task_id')->all();

                $tasks->whereIn('id', $task_ids);
            }

            if($request->input('search'))
            {
                $q = $request->input('search');
                $tasks->where(function($query) use ($q) {
                $query->where('title', 'LIKE', '%'.$q.'%')
                    ->orWhere('contents', 'LIKE', '%'.$q.'%');
                    //->orWhere('email', 'LIKE', '%'.$q.'%');
            });
            }

             $tasks = $tasks->orderBy('id', 'desc')->get();
            //echo count($tasks);
            foreach ($tasks as $task)
            {
                $user_avatars = array();
                if ($task->users)
                {
                    foreach ($task->users as $user)
                    {
                        if($user->user)
                        {
                            $avatar = $user->user && $user->user->avatar ? json_decode($user->user->avatar, true) : null;
                            $team_user['user_id'] = $user->user->id;
                            $team_user['user_name'] = $user->user->name;
                            if($avatar)
                            {
//
                                $team_user['user_avatar'] = $avatar[0]['download_link'];
                            }
                            array_push($user_avatars,$team_user);
                        }


                    }
                }

                if ($task->allTopics)
                {
                    foreach ($task->allTopics as $topic)
                    {
                        if($topic->topic)
                        {
                            $related_topics[] = array(
                                'id' => $topic->topic->id,
                                'title' => $topic->topic->title,
                                'created_at' =>$topic->topic->created_at
                            );
                        }

                    }
                }

                //print_r($task->entities[0]);
                $return_result[] = array(
                    'id' => $task->id,
                    'title' => $task->title,
                    'contents' => $task->contents,
                    'created_by' => $task->created_by,
                    'company_id' => $task->company_id,
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                    'status' => $task->status,
                    'type' => $task->type,
                    'priority' => $task->priority,
                    'team_users' =>$user_avatars,
                    'related_topics'=>$related_topics




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
            'result'=>array('list' =>$return_result)
        ], $code);
    }


    public function task_detail(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');

        $related_topics = array();
        $related_letters = array();
        $related_meetings = array();
        $related_reports = array();
        $user_owners = array();

        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'task_id' => 'required',
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
            $task = Task::where('deleted_at', null)
                ->where('id',$request->input('task_id'))
                ->where('company_id',$request->input('company_id'))->first();
            if (!$task) {
                return response()->json([
                    'status'=>'false',
                    'message'=>"Task Not Found",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }



            //find related topics

            if ($task->allTopics)
            {
                foreach ($task->allTopics as $topic)
                {
                    $related_topics[] = array(
                        'id' => $topic->topic->id,
                        'title' => $topic->topic->title,
                        'created_at' =>$topic->topic->created_at
                    );
                }
            }

            //related letters
            if($task->allLetters)
            {
                foreach ($task->allLetters as $letter)
                {
                    $related_letters[] = array(
                        'id' => $letter->letter->id,
                        'title' => $letter->letter->subject,
                        'created_at' =>$letter->letter->created_at,
                        'status' =>$letter->letter->status
                    );
                }
            }

            //related meetings
            if ($task->allMOM)
            {
                foreach ($task->allMOM as $letter)
                {
                    $related_meetings[] = array(
                        'id' => $letter->mom->id,
                        'title' => $letter->mom->subject,
                        //'url' => 'admin/minutes-of-meeting/detail/'. $letter->mom->id,
                        'created_at' =>$letter->mom->created_at,
                        'host' =>$letter->mom->host? $letter->mom->host->name : ''
                    );
                }
            }

            //related reports
            if ($task->allReports)
            {
                foreach ($task->allReports as $letter)
                {
                    $related_reports[] = array(
                        'id' => $letter->report->id,
                        'title' => $letter->report->title,
                        'contents' =>$letter->report->contents,
                        'created_at' =>$letter->report->created_at,
                        'type' =>getReportType($letter->report->report_type)
                    );
                }
            }

            //owners

            if ($task->users)
            {
                foreach ($task->users as $user)
                {
                    if($user->user)
                    {
                        $avatar =  $user->user->avatar ? json_decode($user->user->avatar, true) : null;
                        $team_user['user_id'] = $user->user->id;
                        $team_user['user_name'] = $user->user->name;
                        $team_user['department'] = $user->user->department ? $user->user->department->department_short_name:'';
                        if($avatar)
                        {
//
                            $team_user['user_avatar'] = $avatar[0]['download_link'];
                        }
                        array_push($user_owners,$team_user);
                    }


                }
            }


            $return_result = array(
                'id' => $task->id,
                'title' => $task->title,
                'reference_no' => $task->reference_no,
                'contents' => $task->contents,
                'created_by' => $task->created_by && $task->createdBy ? $task->createdBy->name: '',
                'company_id' => $task->company_id,
                'start_date' => $task->start_date,
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
                'priority' => $task->priority,
                'status' => $task->status,
                'type' => $task->type,
                'related_topics' =>$related_topics,
                'related_letters' =>$related_letters,
                'related_meetings' =>$related_meetings,
                'related_reports' => $related_reports,
                'task_owners' =>$user_owners

            );

            //set status
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



        ], $code);
    }

    public function save_task(Request $request)
    {
        $validator = validator::make($request->all(), [
            'title'     => 'required',
            'user_id' => 'required',
            'company_id'=>'required',
            'due_date'	=> 'required',
            'status'	=> 'required',
            'type'=>'required',
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
                $task = Task::findOrFail($request->input('listing_id'));
                $task->updated_at = date('Y-m-d H:i:s');
            }else{
                $task = new Task();
                $task->created_by = $user_id;
                $task->company_id = $company_id;
                $task->created_at = date('Y-m-d H:i:s');
            }

            $task->title = $request->input('title');
            $task->type = $request->input('type');
            $task->status = $request->input('status');
            $task->priority = $request->input('priority');
            $task->reference_no = rand(1111111,9999999);

            if ($request->input('start_date'))
            {
                $task->start_date = date('Y-m-d', strtotime($request->input('start_date')));
            }else{
                $task->start_date = date('Y-m-d');
            }

            if ($request->input('due_date'))
                $task->due_date = date('Y-m-d', strtotime($request->input('due_date')));

            if ($request->input('contents'))
                $task->contents = $request->input('contents');

            if ($task->save()) {

                if ($request->input('is_update')) {
                    // assign users
                    if ($request->input('users')) {
                        $passed_users = explode(',', $request->input('users'));
                        $task_user_ids = $task->users->pluck('user_id')->all();

                        foreach ($task->users as $tu) {
                            // remove task user if not in users input
                            if (!in_array($tu->user_id, $passed_users)) {
                                $to_be_deleted = TaskUser::find($tu->id);
                                $to_be_deleted->delete();
                            }
                        }

                        foreach ($passed_users as $user) {
                            // add task user if not in existing task users table
                            if (!in_array($user, $task_user_ids)) {
                                $to_add = new TaskUser();
                                $to_add->task_id = $task->id;
                                $to_add->user_id = $user;
                                $to_add->save();
                            }
                        }
                    }
                    else {
                        // delete all task users when user input is empty
                        foreach ($task->users as $tu) {
                            $to_be_deleted = TaskUser::find($tu->id);
                            $to_be_deleted->delete();
                        }
                    }

                    // assign topics
                    if ($request->input('topics')) {
                        $task_topic_ids = $task->topics->pluck('topic_id')->all();

                        $passed_topics = explode(',', $request->input('topics'));

                        foreach ($task->topics as $topic_task_relation) {
                            // remove topic relationship if not in topic_ids input
                            if (!in_array($topic_task_relation->topic_id, $passed_topics)) {
                                $to_be_deleted = TopicTaskRelationship::find($topic_task_relation->id);
                                $to_be_deleted->delete();
                            }
                        }

                        foreach ($passed_topics as $topic_id) {
                            // create new task relation if not found in existing relationship table
                            if (!in_array($topic_id, $task_topic_ids)) {
                                $task_topic = new TopicTaskRelationship();
                                $task_topic->topic_id = $topic_id;
                                $task_topic->task_id = $task->id;
                                $task_topic->listing_id = 0;
                                $task_topic->type = $task->type;
                                $task_topic->company_id = $company_id;
                                $task_topic->created_by = $user_id;
                                $task_topic->created_at = date('Y-m-d H:i:s');
                                $task_topic->save();
                            }
                        }
                    }
                    else {
                        // remove all topic relationships when no topic_ids
                        if ($task->topics) {
                            foreach ($task->topics as $topic) {
                                $topic->delete();
                            }
                        }
                    }

                }
                else {
                    if ($request->input('users')) {
                        $passed_users = explode(',', $request->input('users'));
                        foreach ($passed_users as $user) {
                            $task_user = new TaskUser();
                            $task_user->task_id = $task->id;
                            $task_user->user_id = $user;
                            $task_user->save();
                        }
                    }

                    if ($request->input('topics')) {
                        $passed_topics = explode(',', $request->input('topics'));
                        foreach ($passed_topics as $topic) {
                            $task_topic = new TopicTaskRelationship();
                            $task_topic->task_id = $task->id;
                            $task_topic->topic_id = $topic;
                            $task_topic->listing_id = 0;
                            $task_topic->type = $task->type;
                            $task_topic->company_id = $company_id;
                            $task_topic->created_by = $user_id;
                            $task_topic->created_at = date('Y-m-d H:i:s');
                            $task_topic->save();
                        }
                    }
                }


                $success_status = true;
                $message = $request->input('is_update') ? " Task Updated!" : "Task Created!";
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


    public function delete(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'task_id' => 'required'
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
            $task = Task::find($request->input('task_id'));
            if (!$task) {
                return response()->json([
                    'status'=>'false',
                    'message'=>"Task Not Found",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }

            $task->deleted_at = date('Y-m-d H:i:s');
            $task->deleted_by = $request->input('user_id');
            if ($task->save()) {
                $code = 200;
                $success_status = true;
                $message = 'Task Deleted';

                if ($task->users) {
                    foreach ($task->users as $task_user) {
                        $to_be_deleted = TaskUser::find($task_user->id);
                        $to_be_deleted->delete();
                    }
                }
            }
        }catch (\Exception $e)
        {
            $message = $e->getMessage();

        }
        return response()->json([
            'status'=>$success_status,
            'message'=>$message,
            'code'=>$code,
            'result'=>''
        ], $code);
    }


    public function filters(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $status_result = array();
        $type_result = array();
        $topics_result = array();
        $message = 'Error Occurred!';
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
            //status filter
            $status_tasks = Task::where('deleted_at', null)->where('company_id',$request->input('company_id'))
               ->groupBy('status')->select('status', DB::raw('count(*) as total'))
                ->get();

            foreach ($status_tasks as $task)
            {
                $status_result[] = array(
                    'status' => $task->status,
                    'count' => $task->total,

                );
            }

            //type filter
            $type_tasks = Task::where('deleted_at', null)->where('company_id',$request->input('company_id'))
                ->groupBy('type')->select('type', DB::raw('count(*) as total'))
                ->get();

            foreach ($type_tasks as $task)
            {
                $type_result[] = array(
                    'type' => $task->type,
                    'count' => $task->total,

                );
            }

            //filter topics

            $topics = Topic::where('deleted_at', null)->where('company_id',$request->input('company_id'))
               // ->groupBy('title')->select('id','title', DB::raw('count(*) as total'))
                ->get();

            foreach ($topics as $topic)
            {
                $topics_result[] = array(
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'count' => $topic->allTasks->count(),

                );
            }

            //set status
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
                'list' =>(Object)$return_result,
                'filter_tasks' =>$status_result,
                'filter_types' =>$type_result,
                'filter_topics' =>$topics_result,

            )
        ], $code);
    }
}
