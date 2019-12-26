<?php

namespace App\Http\Controllers;

use App\SourceType;
use Illuminate\Http\Request;

class SourceTypeController extends Controller
{
    //
    public function source_types(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = array();
        $message = 'Error Occurred!';
        try{
            $sources = SourceType::whereNull('deleted_at')->get();
            foreach ($sources as $source)
            {
                $return_result[] = array(
                    'source_type_id' => $source->source_type_id,
                    'title' => $source->title

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
}
