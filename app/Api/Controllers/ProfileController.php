<?php

namespace Api\Controllers;

use App\ActiveUser;
use App\User;
use App\UserProfile;
use Symfony\Component\HttpFoundation\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Requests\ProfileListRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Api\Requests\UpdateProfileRequest;
use App\UserGallery;
use App\UserFavourite;

class ProfileController extends BaseController
{
    public function upload(UpdateProfileRequest $request){

        $signature = $request->get('signature');


        $imageBase64 = $request->get('user_image');


        /*$data = 'data:image/png;base64,AAAFBfj42Pj4';

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents('image.png', $data);*/

        $binary=base64_decode($imageBase64);
        //header('Content-Type: bitmap; charset=utf-8');
        $imgName =  time().'_'.'profile.png';
        $file = fopen('user_image/'.$imgName, 'wb');
        fwrite($file, $binary);
        fclose($file);

        //save image name to db
        $userProfile = [
            'user_image' => $imgName
        ];

        $email = ActiveUser::where('signature', '=', $signature)->select('email')->first();

        $affectedRows = UserProfile::where('user_email', '=', $email->email)->update($userProfile);
        if($affectedRows){
            $status = 'success';
            $message = 'Successfully updated!';
            //$token = JWTAuth::refresh($token);

        }else{
            $status = 'error';
            $message = 'Update profile failed!';
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message

        ));
    }

    public function uploadGallery(UpdateProfileRequest $request){

        $signature = $request->get('signature');


        $imageBase64 = $request->get('user_image');


        /*$data = 'data:image/png;base64,AAAFBfj42Pj4';

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents('image.png', $data);*/

        $binary=base64_decode($imageBase64);
        //header('Content-Type: bitmap; charset=utf-8');
        $imgName =  time().'_'.'profile.png';
        $file = fopen('user_image/'.$imgName, 'wb');
        fwrite($file, $binary);
        fclose($file);

        $email = ActiveUser::where('signature', '=', $signature)->select('email')->first();
        //save image and email to db
        $userGallery = [
            'user_image' => $imgName,
            'user_email' => $email->email 
            
        ];

        //$affectedRows = UserGallery::where('user_email', '=', $email->email)->update($userGallery);
        $userGallery = UserGallery::create($userGallery);
        if($userGallery){
            $status = 'success';
            $message = 'Successfully upload!';
            //$token = JWTAuth::refresh($token);

        }else{
            $status = 'error';
            $message = 'Update profile failed!';
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
            'email' => $email->email 

        ));
    }

        public function addFavourite(UpdateProfileRequest $request){

        $signature = $request->get('signature');
        $user_addEmail = $request->get('user_addEmail');

        /*$data = 'data:image/png;base64,AAAFBfj42Pj4';

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents('image.png', $data);*/

        $email = ActiveUser::where('signature', '=', $signature)->select('email')->first();
        //save image and email to db
        $userFavourite = [
            
            'user_email' => $email->email,
            'user_addEmail' => $user_addEmail
            
        ];

        //$affectedRows = UserGallery::where('user_email', '=', $email->email)->update($userGallery);
        $userFavourite = UserFavourite::create($userFavourite);
        if($userFavourite){
            $status = 'success';
            $message = 'Successfully add!';
            //$token = JWTAuth::refresh($token);

        }else{
            $status = 'error';
            $message = 'Update profile failed!';
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
            //'email' => $email->email 

        ));
    }
}