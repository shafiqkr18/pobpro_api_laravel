<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /*
     * get all users
     */
    public function all_users(Request $request)
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
            $users = User::where('is_active', 1)->where('company_id',$request->input('company_id'))->get();

            foreach ($users as $user)
            {
                $return_result[] = array(
                    'id' => $user->id,
                    'api_token' => $user->api_token,
                    'first_name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'user_uuid' => $user->user_uuid,
                    'company_id' => $user->company_id,
                    'avatar'=> json_encode($user->avatar),
                    'organization_id' => $user->org_id,
                    'division_id' => $user->div_id,
                    'department_id' => $user->dept_id,
                    'section_id' => $user->sec_id,
                    'position_id' => $user->position_id,

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
    /*
     * logout
     */
    public function logout(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = '';
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
            User::where('id', $request->input('user_id'))->where('company_id',$request->input('company_id'))
                ->update([
                    'api_token'=> null
                ]);
            $code = 200;
            $success_status = true;
            $message = 'Logout successfully';

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
    /*
     * user profile
     */
    public function profile(Request $request)
    {
        $code = 400;
        $success_status = false;
        $return_result = '';
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
            $user = User::where('id', $request->input('user_id'))->where('company_id',$request->input('company_id'))->first();

            if (!$user) {
                return response()->json([
                    'status'=>'false',
                    'message'=>"User does not exist!",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }


            $return_result = array(
                'id' => $user->id,
                'api_token' => $user->api_token,
                'first_name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'user_uuid' => $user->user_uuid,
                'company_id' => $user->company_id,
                'organization_id' => $user->org_id,
                'division_id' => $user->div_id,
                'department_id' => $user->dept_id,
                'section_id' => $user->sec_id,
                'position_id' => $user->position_id,

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
            'result'=>$return_result
        ], $code);
    }


    /*
     * user login
     */
    public function login(Request $request)
    {
       $validator = validator::make($request->all(), [
            'email'     => 'required|email',
            'password' => 'required'
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
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                return response()->json([
                    'status'=>'false',
                    'message'=>"Email does not exist!",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }

            if(Hash::check($request->input('password'), $user->password)){
                $token = Str::random(80);
                User::where('email', $request->input('email'))->update(['api_token' => hash('sha256', $token)]);
                $show_arr = array(
                    'id' => $user->id,
                    'api_token' => hash('sha256', $token),
                    'first_name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'user_uuid' => $user->user_uuid,
                    'company_id' => $user->company_id,
                    'organization_id' => $user->org_id,
                    'division_id' => $user->div_id,
                    'department_id' => $user->dept_id,
                    'section_id' => $user->sec_id,
                    'position_id' => $user->position_id,
                    'avatar'=> json_encode($user->avatar),

                );
                return response()->json([
                    'status' => 'true',
                    'message' => 'success result',
                    'code' => 200,
                    'result' => $show_arr

                ], 200);
            }else{
                return response()->json([
                    'status'=>'false',
                    'message'=>"Email or password is invalid",
                    'code'=>400,
                    'result'=>''
                ], 400);
            }

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

    /*
     * user register
     */
    public function save_new_user(Request $request)
    {
        $validator = validator::make($request->all(), [
            'first_name'     => 'required', 'string', 'max:255',
            'last_name'     => 'required', 'string', 'max:255',
            'email' => 'required', 'string', 'email', 'max:255', 'unique:users',
            //'password' => 'required', 'string', 'min:8',
            'org_id'=>'required',
            'div_id'=>'required',
            'dept_id'=>'required',
            'roles'=>'required',
            'position_id'=>'required',
            'company_id'=>'required',
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

        try{
            if($request->input('is_update')){
                $user = User::find($request->input('listing_id'));
                DB::table('model_has_roles')->where('model_id',$request->input('listing_id'))->delete();
            }else{
                $user = new User();
                $user->user_uuid = (string) Str::uuid();
                $user->created_at = date('Y-m-d H:i:s');
            }

            /*save file*/
            $my_files = '';
            if ($request->hasFile('file')) {
                $files = Arr::wrap($request->file('file'));
                $filesPath = [];
                $path = generatePath('users');

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
                }
                $my_files = json_encode($filesPath);
            }


            $user->name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->mobile_number =$request->input('mobile_number');
            $user->notes = $request->input('notes');
            $user->is_active = $request->input('is_active');
            $user->user_type = $request->input('user_type');
            $user->org_id = $request->input('org_id');
            $user->div_id = $request->input('div_id');
            $user->dept_id = $request->input('dept_id');
            $user->sec_id = $request->input('sec_id');
            $user->position_id = $request->input('position_id');
            $user->company_id = $request->input('company_id');
            $user->updated_at = date('Y-m-d H:i:s');
            $user->api_token = hash('sha256', Str::random(80));

            if ($my_files != '') {
                $user->avatar = $my_files;
            }


            if($user->save())
            {

//                if($request->input('roles'))
//                {
//                    $user->assignRole($request->input('roles'));
//                }else{
//                    $user->assignRole('User');
//                }

                $user_id = $user->id;


                $return_result = array(
                    'id' => $user->id,
                    'api_token' => $user->api_token,
                    'first_name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'user_uuid' => $user->user_uuid,
                    'company_id' => $user->company_id,

                );

                $success_status = true;
                $message = $request->input('is_update') ? " User Updated!" : "User Created!";
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
