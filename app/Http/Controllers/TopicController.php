<?php

namespace App\Http\Controllers;

use App\Topic;
use App\TopicTaskRelationship;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    //

    public function all_topics(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
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
            $topics = Topic::where('deleted_at', null)->where('company_id',$request->input('company_id'));
            if($request->input('type'))
            {
                $type = $request->input('type') == 'EMPTY' ? [] : explode(',', $request->input('type'));
                $topics->whereIn('type', $type);
            }
            $topics = $topics->orderBy('id', 'desc')->get();

            foreach ($topics as $topic)
            {
                $return_result[] = array(
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'contents' => $topic->contents,
                    'created_by' => $topic->created_by,
                    'company_id' => $topic->company_id,

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

    public function topic_detail(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'topic_id' => 'required'
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
            $topics = Topic::where('deleted_at', null)
                ->where('id',$request->input('topic_id'))
                ->where('company_id',$request->input('company_id'))->get();

            foreach ($topics as $topic)
            {
                $return_result = array(
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'contents' => $topic->contents,
                    'created_by' =>$topic->createdBy ? $topic->createdBy->name : '',
                    'created_by_id' => $topic->created_by,
                    'company_id' => $topic->company_id,

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
            'result'=>(Object)$return_result
        ], $code);
    }

    public function save_topic(Request $request)
    {
        $validator = validator::make($request->all(), [
            'title'     => 'required',
            'user_id' => 'required',
            'company_id'=>'required',
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
                $topic = Topic::findOrFail($request->input('listing_id'));
                $topic->updated_at = date('Y-m-d H:i:s');
            }
            else {
                $topic = new Topic();
                $topic->created_by = $request->input('user_id');
                $topic->company_id = $request->input('company_id');
                $topic->created_at = date('Y-m-d H:i:s');
            }

            $topic->title = $request->input('title');
            $topic->type = $request->input('type');
            $topic->contents = $request->input('contents');

            if ($topic->save()) {
                //assign tasks
                if ($request->input('tasks')) {
                    $passed_tasks = explode(',', $request->input('tasks'));

                    if ($request->input('is_update')) {
                        $topic_tasks_ids = $topic->tasks->pluck('task_id')->all();

                        foreach ($topic->tasks as $topic_task_relation) {
                            // remove task relationship if not in task_ids input
                            if (!in_array($topic_task_relation->task_id, $passed_tasks)) {
                                $to_be_deleted = TopicTaskRelationship::find($topic_task_relation->id);
                                $to_be_deleted->delete();
                            }
                        }

                        foreach ($passed_tasks as $task_id) {
                            // create new task relation if not found in existing relationship table
                            if (!in_array($task_id, $topic_tasks_ids)) {
                                $topic_task = new TopicTaskRelationship();
                                $topic_task->topic_id = $topic->id;
                                $topic_task->task_id = $task_id;
                                $topic_task->listing_id = 0;
                                 $topic_task->type = $topic->type;
                                $topic_task->company_id = $company_id;
                                $topic_task->created_by = $user_id;
                                $topic_task->created_at = date('Y-m-d H:i:s');
                                $topic_task->save();
                            }
                        }
                    }
                    else {
                        foreach ($passed_tasks as $task) {
                            $topic_task = new TopicTaskRelationship();
                            $topic_task->topic_id = $topic->id;
                            $topic_task->task_id = $task;
                            $topic_task->listing_id = 0;
                             $topic_task->type = $topic->type;
                            $topic_task->company_id = $company_id;
                            $topic_task->created_by = $user_id;
                            $topic_task->created_at = date('Y-m-d H:i:s');
                            $topic_task->save();
                        }
                    }

                }
                else {
                    if ($request->input('is_update')) {
                        // remove all task relationships when no task_ids
                        if ($topic->tasks) {
                            foreach ($topic->tasks as $task) {
                                $task->delete();
                            }
                        }
                    }
                }
                $success_status = true;
                $message = $request->input('is_update') ? " Topic Updated!" : "Topic Created!";
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
