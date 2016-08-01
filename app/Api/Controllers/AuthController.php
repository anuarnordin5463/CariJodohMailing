<?php

namespace Api\Controllers;

use App\User;
use App\ActiveUser;
use App\UserProfile;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{


    public function me(Request $request)
    {
        return JWTAuth::parseToken()->authenticate();
    }

    public function authenticate(Request $request)
    {

        $proceed = true;

        // grab credentials from the request
        $credentials = $request->only('email', 'password');
		$deviceId = $request->get('device_id');
        $active_id = UserProfile::select('users_profile.user_id')->join('active_users', 'active_users.email', '=', 'users_profile.user_email')->get('user_id');


		
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {

                $message = "Emel/Kata Laluan Tidak Sah";
                //$message = "Invalid Username / Password";
                $status = "error";
                $proceed = false;
                //
                //return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
                $message = "Unable to create token";
                $status = "error";
                $proceed = false;
        }

        $signature = new UUID;

        if($proceed){
            //generate token for user


            //$token = $this->getToken($email);
            $login_time = date('Y-m-d H:i:s');

            //if success login , add token to active_user
            $activeUser = [
                'user_id' => $active_id,
                'email' => $credentials['email'],
                'signature' => $signature->uuid,
				'device_id' => $deviceId,
                'login_time' => $login_time,
                'last_active' => "",
            ];

            $activeUser = ActiveUser::create($activeUser);

            if(!empty($activeUser->id)){
                $status = 'success';
            }else{
                $status = 'error';
            }

            $message = "Successfully login!";

        }
        $auth_token = compact('token');
        if(!$auth_token['token']){
            $reprinttoken = "";
        }else{
            $reprinttoken = $auth_token['token'];
        }

        $userid = UserProfile::select('user_id','user_name','user_dob','user_image')->where('user_email', '=', $credentials['email'])->first();

        return response()->json(array(
            'status' => $status,
            'message' => $message ,
            'signature' => $signature->uuid,
            'auth_token' => $reprinttoken,
            'user' => $userid
        ));
    }

    public function validateToken() 
    {
        // Our routes file should have already authenticated this token, so we just return success here
        return API::response()->array(['status' => 'success'])->statusCode(200);
    }

    public function register(UserRequest $request)
    {
        $proceed = true;
        $status = null;
        $unique = uniqid();
        $user_id = "CJ_".substr($unique, 7);


        $newUser = [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'user_id' => $user_id,

        ];

        $userEmail = [
            'user_email' => $request->get('email'),
        ];

        $newProfile = [
            'user_email' => $request->get('email'),
            'user_id' => $user_id,
        ];

        //check if email already existed.
        $email = $userEmail['user_email'];
        $affectedRows = UserProfile::where('user_email', '=', $email)->first();

        if($affectedRows){
            $status = "error";
            $message = 'Email already taken!';
            $proceed = false;
        }

        if($proceed){
            //create new user profile into users_profile table
            $userProfile = UserProfile::create($newProfile);

            if(!empty($userProfile)){
                //create new user into users table
                $user = User::create($newUser);
            }

            if(!empty($user->id)){
                $status = 'success';
                $message = "Successfully registered!";
            }
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
        ));
    }

    public function logout(Request $request) {

        $signature = $request->only('signature');

        //clear from active user
        $affectedRows = ActiveUser::where('signature', '=', $signature)->delete();

        if($affectedRows){
            $status = 'success';
        }else{
            $status = 'failed';
        }
        return response()->json(array(
            'status' => $status,
            'message' => "Successfully log-out!"
        ));


    }

}