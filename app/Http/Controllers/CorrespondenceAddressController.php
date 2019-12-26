<?php

namespace App\Http\Controllers;

use App\CorrespondenceAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CorrespondenceAddressController extends Controller
{


    public function addresses(Request $request)
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
            $addresses = CorrespondenceAddress::where('company_id',$company_id);

            $addresses = $addresses->orderBy('id', 'desc')->get();

            foreach ($addresses as $address)
            {
                $avatar =  $address->contact_person_avatar ? json_decode($address->contact_person_avatar, true) : null;
                $company_logo =  $address->company_logo ? json_decode($address->company_logo, true) : null;
                $return_result[] = array(
                    'id' => $address->id,
                    'u_id' => $address->u_id,
                    'first_name' => $address->first_name,
                    'middle_name' => $address->middle_name,
                    'last_name' => $address->last_name,
                    'email' => $address->email,
                    'position' => $address->position,
                    'company' => $address->company,
                    'address' => $address->address,
                    'city' => $address->city,
                    'country' => $address->country,
                    'website' => $address->website,
                    'company_logo' => $company_logo? $company_logo[0]['download_link'] : '',
                    'contact_person_avatar' => $avatar? $avatar[0]['download_link'] : ''

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

    public function save_address(Request $request)
    {
        $validator = validator::make($request->all(), [
           'user_id' => 'required',
            'company_id'=>'required',
            'first_name' => 'required',
            'company'	=> 'required',
            'email'	=> 'required|unique:crspndnc_addresses,email'
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
                $cAddress = CorrespondenceAddress::find($request->input('listing_id'));
                $cAddress->updated_at = date('Y-m-d H:i:s');
            }else{
                $cAddress = new CorrespondenceAddress();
                $cAddress->created_by = $request->input('user_id');
                $cAddress->company_id = $request->input('company_id');
                $cAddress->created_at = date('Y-m-d H:i:s');
            }

            /*save file*/
            $company_logo = '';
            if ($request->hasFile('company_logo')) {
                $files = Arr::wrap($request->file('company_logo'));
                $filesPath = [];
                $path = generatePath('lettercontacts');

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
                $company_logo = json_encode($filesPath);
            }

            $contact_person_avatar = '';
            if ($request->hasFile('contact_person_avatar')) {
                $files = Arr::wrap($request->file('contact_person_avatar'));
                $filesPath = [];
                $path = generatePath('lettercontacts');

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
                $contact_person_avatar = json_encode($filesPath);
            }


            $cAddress->first_name = $request->input('first_name');
            $cAddress->middle_name = $request->input('middle_name');
            $cAddress->last_name = $request->input('last_name');
            $cAddress->company = $request->input('company');
            $cAddress->position = $request->input('position');
            $cAddress->email = $request->input('email');
            $cAddress->website = $request->input('website');
            $cAddress->country = $request->input('country');
            $cAddress->city = $request->input('city');
            $cAddress->address = $request->input('address');
            $cAddress->company_id = $company_id;
            $cAddress->created_by = $user_id;
            $cAddress->u_id = rand(11111,99999);
            $cAddress->contact_person_avatar = $contact_person_avatar;
            $cAddress->company_logo = $company_logo;

            if ($cAddress->save()) {
                $listing_id = $cAddress->id;
                $message = 'Address ' . ($request->input('is_update') ? 'updated' : 'submitted') . '.';
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

    public function detail(Request $request)
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
            'contact_id' => 'required',
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
            $address = CorrespondenceAddress::findOrFail($request->input('contact_id'));

            $avatar =  $address->contact_person_avatar ? json_decode($address->contact_person_avatar, true) : null;
            $company_logo =  $address->company_logo ? json_decode($address->company_logo, true) : null;
            $return_result[] = array(
                'id' => $address->id,
                'u_id' => $address->u_id,
                'first_name' => $address->first_name,
                'middle_name' => $address->middle_name,
                'last_name' => $address->last_name,
                'email' => $address->email,
                'position' => $address->position,
                'company' => $address->company,
                'address' => $address->address,
                'city' => $address->city,
                'country' => $address->country,
                'website' => $address->website,
                'company_logo' => $company_logo? $company_logo[0]['download_link'] : '',
                'contact_person_avatar' => $avatar? $avatar[0]['download_link'] : ''
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
}
