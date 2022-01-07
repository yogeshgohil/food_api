<?php

namespace App\Http\Controllers;

use App\Models\TblUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
class UserController extends Controller
{
    public function register(Request $request)
    {
        $response = array("status" => "", "message" => "", "errors" => array());
        $validator = Validator::make(
            $request->all(),
            [
                'firstname' => 'required',
                "lastname" => 'required',
                "profile_img" => 'required|mimes:png,jpg,jpeg|max:1024',
                'contactno' => 'required|size:10|unique:tbluser,contactno',
                "email" => 'required|unique:tbluser,email|email',
                "password" => 'required',
                'confirm_password' => 'required|same:password',
                'gender'=>'required',
                
            ]
            , [
                'firstname.required' => 'First name is required.',
                'lastname.required' => 'Last name is required.',
                'profile_img.required' => 'Profile picture is required.',
                'profile_img.mimes' => 'Profile picture allows only png, jpg, jpeg extension.',
                'profile_img.max' => 'Profile picture size may not be greater than 1MB.',
                'contactno.required' => 'Mobile number is required.',
                'contactno.size' => 'Mobile number must be 10 digits.',
                'contactno.unique' => 'Mobile number already exist.',
                'email.required' => 'Email is required.',
                'email.email' => 'Enter valid email.',
                'email.unique' => 'Email already exist.',
                'password.required'=>'Enter a valid email.',
                'confirm_password.required'=>'Confirm password is required.',
                'confirm_password.same'=>'Password and confirm password must be same.',
                'gender.required'=>'Gender is required.'
            ]
        );
        if ($validator->fails()) {
            $response['status'] = "failure";
            $response['errors'] = $validator->errors()->all();
        } else {
            $file = $request->file('profile_img');
            $name=time();
            $ext=$request->profile_img->getClientOriginalExtension();
            $newname=$name.".".$ext;
            $file->move(public_path().'/UserProfile',$newname);
            $reg = new TblUser();
            $reg->firstname = $request->firstname;
            $reg->lastname = $request->lastname;
            $reg->profile_img = $newname;
            $reg->contactno = $request->contactno;
            $reg->email = $request->email;
            $reg->password = md5($request->password);
            $reg->token = Str::random(32);
            $reg->gender=$request->gender;
            $reg->save();
            $response["status"] = "success";
            $response["message"] = "User has been register successfully.";
            $response["data"]=$reg;
        }
        return json_encode($response);
    }
}
