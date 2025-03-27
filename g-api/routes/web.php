<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/send-test-email', function () {
    $recipient = 'gabrielmosesmzee@gmail.com'; 
    $subject = 'Test Email from Laravel';
    $message = 'This is a test email sent from your Laravel app.';

    try {
        Mail::raw($message, function ($mail) use ($recipient, $subject) {
            $mail->to($recipient)
                 ->subject($subject);
        });

        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
});
