<?php

namespace Api\Controllers;

use Mail;
use App\User;
use App\Http\Controllers\Controller;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Api\Requests\MailingRequest;

class MailingController extends BaseController
{
    /**
     * Send an e-mail reminder to the user.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function sendEmailReminder(MailingRequest $request)
    {
        //$user = User::findOrFail($id);
/*
        Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
            $m->from('hello@app.com', 'Your Application');

            $m->to($user->email, $user->name)->subject('Your Reminder!');
*/
        Mail::send('emails.welcome', $data, function ($message) {
            $message->from('us@example.com', 'Laravel');

            $message->to('anuarnordin5463@yahoo.com')->cc('bar@example.com');

        });
    }
}