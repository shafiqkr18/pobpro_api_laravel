<?php

namespace App\Http\Controllers;

use App\Activities;
use App\CorrespondenceMessage;
use App\TopicTaskRelationship;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CorrespondenceController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function letters(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';

        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');

        $return_result = array();

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

            $letters = CorrespondenceMessage::select("*")->where('deleted_at', null)->where('company_id',$company_id);

            if($request->input('directions'))
            {
                //$direction = $request->input('directions') == 'EMPTY' ? [] : explode(',', $request->input('directions'));
               //$letters->whereIn('direction', $direction);

                $direction = $request->input('directions') == 'EMPTY' ? 'ALL' : $request->input('directions');
                $letters->where('direction', $direction);
            }

            if($request->input('topics'))
            {
                $topics = $request->input('topics') == 'EMPTY' ? [] : explode(',', $request->input('topics'));
                $letter_ids = TopicTaskRelationship::where('type',0)->where('listing_id' , '>' , 0)
                    ->where('company_id',$company_id)
                    ->whereIn('topic_id',$topics)->pluck('listing_id')->all();
                $letters->whereIn('id', $letter_ids);
            }

            if($request->input('msg_from'))
            {
                $letters->where('msg_from_id',$request->input('msg_from'));
            }

            if($request->input('msg_to'))
            {
                $letters->where('msg_to_id',$request->input('msg_to'));
            }

            if($request->input('msg_start_date'))
            {
                $letters->where('msg_date' , '>=', date('Y-m-d H:i:s',strtotime($request->input('msg_start_date'))));
            }

            if($request->input('msg_end_date'))
            {
                $letters->where('msg_date' , '<=', date('Y-m-d H:i:s',strtotime($request->input('msg_end_date'))));
            }

            if($request->input('contacts'))
            {
                if($request->input('directions') == 'IN')
                {
                    $letters->where('msg_from_id',$request->input('contacts'));
                }else if($request->input('directions') == 'OUT')
                {
                    $letters->where('msg_to_id',$request->input('contacts'));
                }

            }


            $letters = $letters->orderBy('id', 'desc')->get();


            foreach ($letters as $letter)
            {
                $return_result[] = array(
                    'id' => $letter->id,
                    'reference_no' => $letter->reference_no,
                    'msg_to' => $letter->to ? $letter->to->getName() : '',
                    'msg_from' => $letter->from ? $letter->from->getName() : '',
                    'subject' => $letter->subject,
                    'pob_status' => $letter->pob_status,
                    'status' => $letter->status,
                    'msg_date' => $letter->msg_date,
                    'created_at' => $letter->created_at,
                    'updated_at' => $letter->updated_at,
                    'direction' => $letter->direction,
                    'msg_code' => $letter->msg_code,
                    'msg_parent_id' => $letter->msg_parent_id,
                    'contents' => $letter->contents

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

    public function letter_detail(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        $user_id = $request->input('user_id');
        $company_id = $request->input('company_id');

        $validator = validator::make($request->all(), [
            'user_id'     => 'required',
            'company_id' => 'required',
            'letter_id' => 'required',
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

            $letter = CorrespondenceMessage::where('id',$request->input('letter_id'))->first();
            if (!$letter) {
                return response()->json([
                    'status'=>'false',
                    'message'=>"Task Not Found",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }


            $return_result[] = array(
                'id' => $letter->id,
                'reference_no' => $letter->reference_no,
                'msg_to' => $letter->to ? $letter->to->getName() : '',
                'msg_from' => $letter->from ? $letter->from->getName() : '',
                'subject' => $letter->subject,
                'pob_status' => $letter->pob_status,
                'status' => $letter->status,
                'msg_date' => $letter->msg_date,
                'created_at' => $letter->created_at,
                'updated_at' => $letter->updated_at,
                'direction' => $letter->direction,
                'msg_code' => $letter->msg_code,
                'msg_parent_id' => $letter->msg_parent_id,
                'contents' => $letter->contents

            );


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

    /**
     * @param Request $request
     * * @return \Illuminate\Http\JsonResponse
     */
    public function save_letter(Request $request)
    {
        $validator = validator::make($request->all(), [
            'msg_to_id'	=> 'required',
            'msg_from_id'	=> 'required',
            'reference_no'	=> 'required',
            'subject'	=> 'required',
            'direction' => 'required',
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
                $CorrespondenceMsg = CorrespondenceMessage::find($request->input('listing_id'));
                $CorrespondenceMsg->updated_at = date('Y-m-d H:i:s');
            }else{
                $CorrespondenceMsg = new CorrespondenceMessage();
                $CorrespondenceMsg->created_at = date('Y-m-d H:i:s');
                $CorrespondenceMsg->created_by = $user_id;
                $CorrespondenceMsg->company_id = $company_id;
            }


            /*save file*/
            $attachment_files = '';
            $attachment_files_name = '';
            if ($request->hasFile('attachment_files')) {
                $files = Arr::wrap($request->file('attachment_files'));
                $filesPath = [];
                $path = generatePath('correspondence');

                foreach ($files as $file) {
                    $filename = generateFileName($file, $path);
                    $file->storeAs(
                        $path,
                        $filename.'.'.$file->getClientOriginalExtension(),
                        config('app.storage.disk', 'public')

                    );

                    array_push($filesPath, [
                        'download_link' => $path.$filename.'.'.$file->getClientOriginalExtension(),
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                    ]);

                    $attachment_files_name = $file->getClientOriginalName();
                }
                $attachment_files = json_encode($filesPath);
            }

            $original_files = '';
            $original_files_name = '';
            if ($request->hasFile('original_files')) {
                $files = Arr::wrap($request->file('original_files'));
                $filesPath = [];
                $path = generatePath('correspondence');

                foreach ($files as $file) {
                    $filename = generateFileName($file, $path);
                    $file->storeAs(
                        $path,
                        $filename.'.'.$file->getClientOriginalExtension(),
                        config('app.storage.disk', 'public')

                    );

                    array_push($filesPath, [
                        'download_link' => $path.$filename.'.'.$file->getClientOriginalExtension(),
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                    ]);

                    $original_files_name = $file->getClientOriginalName();
                }
                $original_files = json_encode($filesPath);
            }


            $CorrespondenceMsg->msg_code = $request->input('msg_code') ? $request->input('msg_code') : rand(11111,99999);
            $CorrespondenceMsg->reference_no = $request->input('reference_no');
            $CorrespondenceMsg->subject = $request->input('subject');
            $CorrespondenceMsg->ar_subject = $request->input('ar_subject');
            $CorrespondenceMsg->ar_reference_no = $request->input('ar_reference_no');
            $CorrespondenceMsg->direction = $request->input('direction');
            $CorrespondenceMsg->msg_date = $request->input('msg_date') ? date('Y-m-d H:i:s',strtotime($request->input('msg_date'))) : date('Y-m-d H:i:s');
            $CorrespondenceMsg->details_date = $request->input('details_date');
            $CorrespondenceMsg->contents = $request->input('contents');
            $CorrespondenceMsg->ar_contents = $request->input('ar_contents');
            $CorrespondenceMsg->assign_dept_id = $request->input('assign_dept_id');
            $CorrespondenceMsg->attachment_file_name = $attachment_files_name;
            $CorrespondenceMsg->attachment_files = $attachment_files;
            $CorrespondenceMsg->orignal_file_name = $original_files_name;
            $CorrespondenceMsg->orignal_files = $original_files;
            $CorrespondenceMsg->msg_from_id = $request->input('msg_from_id');
            $CorrespondenceMsg->msg_to_id = $request->input('msg_to_id');
            $CorrespondenceMsg->status = $request->input('status') ? $request->input('status') : 0;
            $CorrespondenceMsg->pob_status = $request->input('pob_status') ? $request->input('pob_status') : 0;

            if($request->input('msg_parent_id')) { $CorrespondenceMsg->msg_parent_id = $request->input('msg_parent_id'); }

            if ($CorrespondenceMsg->save()) {
                $listing_id = $CorrespondenceMsg->id;
                $success_status = true;
                $code = 200;
                $message = 'Letter ' . ($request->input('is_update') ? 'updated' : 'created') . '.';


                //save history
                if($request->input('msg_parent_id')) // save parent id
                {
                    $listing_id = $request->input('msg_parent_id');
                }
                $activity = new Activities();
                $activity->listing_id = $listing_id;
                $activity->type = "letters";
                $activity->activity_title = $request->input('subject') ? $request->input('subject') : 'Letter Created';
                $activity->activity_details = $request->input('contents') ? $request->input('contents') : "Letter ".($request->input('is_update') ? 'updated' : 'created')." By - ".User::find($user_id)->name;
                $activity->action_type = 3;
                $activity->created_by = $user_id;
                $activity->company_id = $company_id;
                $activity->created_at = date('Y-m-d H:i:s');
                $activity->save();
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
