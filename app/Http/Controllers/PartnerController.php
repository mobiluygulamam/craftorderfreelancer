<?php

namespace App\Http\Controllers;

use App\Models\PartnerApplication;
use Illuminate\Http\Request;
use App\Models\Mail\EmailTest;
class PartnerController extends Controller
{
    public function store(Request $request)
    {
        // Form verilerini doğrula
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'company' => 'nullable|string|max:255',
            'partner_type' => 'required|string|max:255',
        ]);

        // Veritabanına kaydet
        PartnerApplication::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'partner_type' => $request->partner_type,
        ]);

     //    Send mail to admin
        return response()->json(['message' => 'Başvurunuz başarıyla alındı!']);
    }
    public function joinnewPartner(Request $request)
    {
     ini_set('max_execution_time',300);
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'mail_driver' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_from_address' => 'required',
                'mail_from_name' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['success' => false, 'message' => 'Please Add The Email Credentails']);
        }

        // if (($request->mail_driver == null) && ($request->mail_host == null) && ($request->mail_port == null) && ($request->mail_username  == null) && ($request->mail_password  == null) && ($request->mail_from_address  == null) && ($request->mail_from_name  == null)) {
        //     return response()->json(['success' => false,'message' => 'Please Add The Email Credentails']);
        // }

        try {
            config([
                'mail.driver' => $request->mail_driver,
                'mail.host' => $request->mail_host,
                'mail.port' => $request->mail_port,
                'mail.username' => $request->mail_username,
                'mail.password' => $request->mail_password,
                'mail.encryption' => $request->mail_encryption,
                'mail.from.address' => $request->mail_from_address,
                'mail.from.name' => $request->mail_from_name,
            ]);

            Mail::to($request->email)->send(new EmailTest());
        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'is_success' => true,
            'message' => __('Email send Successfully'),
        ]);
    }
}


