<?php

namespace App\Http\Controllers\Web;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = ['page_title' => 'Dashboard', 'page_icon' => 'fa-dashboard'];
        $data['totalUser'] = User::where('user_type', USER_TYPE['USER'])->count();

        $data['monthlyUserData'] = lastOneYearMontlyData(User::where('user_type', USER_TYPE['USER']));


        return view('web.dashboard.index', $data);
    }

    public function privacyPolicy()
    {
        return view('web.privacy-policy');
    }

    public function howDoDeleteAccount()
    {
        return view('web.how-delete');
    }

    public function contactUs()
    {
        
        return view('web.contactus');
    }

    public function about()
    {
        
        return view('web.about');
    }


    public function submit(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'description' => 'required',
        ]);

        // Retrieve validated data
        $data = $request->only(['email', 'phone', 'description']);

        // Send email
        // Mail::send('mail.contact', $data, function ($message) use ($data) {
        //     $message->from($data['email']);
        //     $message->to('your-email@example.com'); // Update recipient email address
        //     $message->subject('Contact Form Submission: ' . $data['subject']); // Subject of the email
        // });

        $mailData = [
            'to' => 'mohit1990sumbria@gmail.com',
            'subject' => 'Contact Form Submission:',
            'data'=>$data,
            'view' => 'mail.send-mail',
        ];


        // Send the email
        if (Helper::SendMail($mailData)) {
            Session::flash('success',('Your message has been sent successfully!'));

            return redirect()->back();

        }

    }

    
}
