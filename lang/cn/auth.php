<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    
    
    'failed' => '這些憑據與我們的記錄不符。',
    'password' => '提供的密碼不正確。',
    'throttle' => '嘗試登錄次數過多。請在:seconds秒後重試。',
    
    'messages' => [
        'account_approval'=> '請等待帳戶審核通過以進入您的帳戶。',
        'registeration' => [
            'success'               => '註冊成功。',
            'phone_unique'          => '該手機號碼已被使用。',
        ],
        'login' => [
            'success'               => '登入成功。',
            'failed'                => '無效的憑據！請重試。',
        ],
        'logout' => [
            'success'               => '成功登出。',
            'failed'                => '您已經登出了。',
        ],
        'forgot_password' => [
            'success'               => '我們已發送重置密碼的連結至您的電子郵件。請檢查您的收件箱！',
            'success_update'        => '您的密碼已成功重置。',
            'otp_sent'              => '我們已發送OTP至您的電子郵件。請檢查您的收件箱！',
            'validation'            => [
                'phone_number_not_found'=> '我們無法找到使用該手機號碼的用戶。',
				'verified_phone_number' => '此密碼重置的手機號碼已驗證。',
				'email_not_found'       => '我們無法找到使用該電子郵件地址的用戶。',
                'incorrect_password'    => '當前密碼不正確！請重試。',
                'invalid_otp'           => '此密碼重置的OTP無效。',
                'expire_otp'            => '此密碼重置的OTP已過期。',
                'verified_otp'          => '此密碼重置的OTP已驗證。',				
                'expire_request'        => '此密碼重置請求已過期。',
				'invalid_request'       => '此密碼重置請求無效。',
                'invalid_token_email'   => '無效的令牌或電子郵件！',
            ],
        ],
    ],

    'unauthorize'  => '您未被授權執行此操作。',

];
