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
    
    
        'failed' => 'これらの資格情報は私たちの記録と一致しません。',
        'password' => '提供されたパスワードが正しくありません。',
        'throttle' => 'ログイン試行が多すぎます。:seconds 秒後にもう一度お試しください。',
    
        'messages' => [
            'account_approval'=> 'アカウントへのアクセスが承認されるまでお待ちください。',
            'registeration' => [
                'success'               => '正常に登録されました。',
                'phone_unique'          => 'その電話番号はすでに使用されています。',
            ],
            'login' => [
                'success'               => 'ログインに成功しました。',
                'failed'                => '無効な資格情報です！もう一度お試しください。',
            ],
            'logout' => [
                'success'               => '正常にログアウトしました。',
                'failed'                => 'すでにログアウトしています。',
            ],
            'forgot_password' => [
                'success'               => 'パスワードリセットリンクを含むメールを送信しました。受信トレイを確認してください！',
                'success_update'        => 'パスワードが正常にリセットされました。',
                'otp_sent'              => 'OTPを含むメールを送信しました。受信トレイを確認してください！',
                'validation'            => [
                    'phone_number_not_found'=> 'その電話番号のユーザーが見つかりません。',
                    'verified_phone_number' => 'このパスワードリセットの電話番号は確認されています。',
                    'email_not_found'       => 'そのメールアドレスのユーザーが見つかりません。',
                    'incorrect_password'    => '現在のパスワードが正しくありません！もう一度お試しください。',
                    'invalid_otp'           => 'このパスワードリセットのOTPは無効です。',
                    'expire_otp'            => 'このパスワードリセットのOTPは期限切れです。',
                    'verified_otp'          => 'このパスワードリセットのOTPは確認済みです。',
                    'expire_request'        => 'このパスワードリセットのリクエストは期限切れです。',
                    'invalid_request'       => 'このパスワードリセットのリクエストは無効です。',
                    'invalid_token_email'   => '無効なトークンまたはメールアドレス！',
                ],
            ],
        ],
    
        'unauthorize'  => 'このアクションを実行する権限がありません。',
    
    ];
    ```
