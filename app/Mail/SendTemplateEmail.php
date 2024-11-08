<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTemplateEmail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $templateType;
    protected $templateData;
    public function __construct($templateType, $templateData)
    {
        $this->templateType = $templateType;
        $this->templateData = $templateData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        $template = \DB::table('mst_template')
//            ->where('key', $this->templateType)
//            ->where('is_active', 1)
//            ->first();
//        $otp = \DB::table('password_resets')
//            ->select('token')
//            ->where('email', $this->templateData['email'])->first();
//            // var_dump($otp); exit;
//        if (!empty($template) && $template->key == config('global.MAIL_TEMPLATE.FORGOT_PASSWORD.key')) {
//
//            $user = $this->templateData['user'];
//            $otpLink = $this->templateData['otpLink'];
//
//            $sendEmailArray = [
//                'firstname' => $user->firstname,
//                'lastname'  => $user->lastname,
//                'link'      => $otpLink,
//                'appname'   => env('APP_NAME'),
//            ];
//            if (!empty($otp->token)) {
//                $content = $otp->token;
//                foreach ($sendEmailArray as $key => $value) {
//                    $content = str_replace('{{~' . $key . '~}}', $value, $content);
//                }
//                // dd($content);
//                return $this->subject(env('APP_NAME') . " " . $template->key)->view('email.mails')->with(['content' => $content]);
//            }
//        }
    }
}
