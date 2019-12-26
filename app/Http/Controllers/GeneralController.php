<?php


namespace App\Http\Controllers;
use App\Company;
use App\CorrespondenceMessage;
use App\Meeting;
use App\Offer;
use App\Plan;
use App\PositionManagement;
use App\Report;
use App\Task;
use App\Topic;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;

class GeneralController extends Controller
{
    public function __construct()
    {

    }

    public function correlative_map(Request $request)
    {
        //validate request
        $validator = validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'failure',
                'message'=>$validator->errors(),
                'code'=>400,
                'result'=>''
            ], 400);
        }

        try {
            $company_id = $request->input('company_id');
            $department_id = $request->input('department_id');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $dateRange = false;
            if(($start_date) && ($end_date))
            {
                $dateRange = true;
                $start_date = $start_date.' 00:00:00';
                $end_date = $end_date.' 23:59:59';
            }

            //$show_arr = array();
            $entities = array();
            $tasks = array();
            $topics = array();

            $rpt_arr = array();
            if($dateRange)
            {
                $myReports = Report::whereNull('deleted_at')->whereBetween('report_date', [$start_date,$end_date])->get();
            }else{
                $myReports = Report::whereNull('deleted_at')->get();
            }

            foreach ($myReports as $arr) {
                $rpt_arr[] = array(
                    'id' => $arr->id,
                    'title' => $arr->title,
                    'type' => "Report",
                    'unique_id' => "Report-".$arr->id,
                    'url' => '/admin/report/detail/'.$arr->id,
                );
            }

            $letter_arr = array();
            if($dateRange)
            {
                $myLetters = CorrespondenceMessage::whereNull('deleted_at')->whereBetween('created_at', [$start_date,$end_date])->get();
            }else{
                $myLetters = CorrespondenceMessage::whereNull('deleted_at')->get();
            }

            foreach ($myLetters as $arr) {
                $letter_arr[] = array(
                    'id' => $arr->id,
                    'title' => $arr->subject,
                    'type' => "Letter",
                    'unique_id' => "Letter-".$arr->id,
                    'url' => '/admin/correspondence/letter/detail/'.$arr->id
                );
            }

            $mom_arr = array();
            if($dateRange)
            {
                $myMoms = Meeting::whereNull('deleted_at')
                    ->whereBetween('meeting_date', [$request->input('start_date'),$request->input('end_date')])
                    ->get();
            }else{
                $myMoms = Meeting::whereNull('deleted_at')->get();
            }

            foreach ($myMoms as $arr) {
                $mom_arr[] = array(
                    'id' => $arr->id,
                    'title' => $arr->subject,
                    'type' => "MOM",
                    'unique_id' => "MOM-".$arr->id,
                    'url' => '/admin/minutes-of-meeting/detail/'.$arr->id
                );
            }

            $entities = array_merge($rpt_arr, $letter_arr,$mom_arr);


            //tasks
            $myTasks = Task::whereNull('deleted_at')->get();
            foreach ($myTasks as $arr) {

                $entitles_ids_arr = $arr->entities->all();
                $entitles_ids = array();
                foreach ($entitles_ids_arr as $ar)
                {
                    if($ar->type == 2)
                    {
                        array_push($entitles_ids,'Report-'.$ar->listing_id);
                    }elseif ($ar->type == 1)
                    {
                        array_push($entitles_ids,'MOM-'.$ar->listing_id);
                    }else{
                        array_push($entitles_ids,'Letter-'.$ar->listing_id);
                    }
                }
                $linked_topics = $arr->linked_topics?$arr->linked_topics->pluck('topic_id')->all():'';
                array_walk($linked_topics, function(&$value, $key) { $value = 'Topic-'.$value; } );



                $tasks[] = array(
                    'id' => $arr->id,
                    'title' => $arr->title,
                    'unique_id' => "Task-".$arr->id,
                    'url' => '/admin/task/detail/'.$arr->id,
                    'linkedEntityIds' => $entitles_ids,
                    'linkedTopicIds' => $linked_topics
                );
            }

            //topics
            $myTopics = Topic::whereNull('deleted_at')->get();
            foreach ($myTopics as $arr) {
               // $entitles_ids_arr = $arr->entities?$arr->entities->pluck('listing_id','type')->all():'';
                $entitles_ids_arr = $arr->entities->all();
                $entitles_ids = array();
                foreach ($entitles_ids_arr as $ar)
                {
                    if($ar->type == 2)
                    {
                        array_push($entitles_ids,'Report-'.$ar->listing_id);
                    }elseif ($ar->type == 1)
                    {
                        array_push($entitles_ids,'MOM-'.$ar->listing_id);
                    }else{
                        array_push($entitles_ids,'Letter-'.$ar->listing_id);
                    }
                }
                $linked_tasks = $arr->linked_tasks?$arr->linked_tasks->pluck('task_id')->all():'';
                array_walk($linked_tasks, function(&$value, $key) { $value = 'Task-'.$value; } );


                $topics[] = array(
                    'id' => $arr->id,
                    'title' => $arr->title,
                    'unique_id' => "Topic-".$arr->id,
                    'url'       => '/admin/topic/detail/'.$arr->id,
                    'linkedEntityIds' => $entitles_ids,
                    'linkedTaskIds' => $linked_tasks
                );
            }


            $show_arr = array(
                'entities' => $entities,
                'tasks' => $tasks,
                'topics' => $topics,

            );

            return response()->json([
                'status' => 'success',
                'message' => 'success result',
                'code' => 200,
                //'result' => $show_arr
                'result'=>array('list' =>$show_arr)

            ], 200);
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' =>$message
            ]);
        }
    }


    public function test_api(Request $request)
    {


        if ($request->input('topics')) {

            foreach ($request->input('topics') as $topic) {
                echo "<br>".$topic;
            }
        }else{
            echo "empty";
        }
    }
}
