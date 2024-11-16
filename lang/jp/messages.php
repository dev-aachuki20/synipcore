<?php
return [

    'crud' => [
        'add_record'    => '正常に追加されました！',
        'update_record' => '正常に更新されました！',
        'delete_record' => 'このレコードは正常に削除されました！',
        'restore_record' => 'このレコードは正常に復元されました！',
        'merge_record'  => 'このレコードは正常にマージされました！',
        'approve_record' => 'レコードが正常に承認されました！',
        'status_update' => 'ステータスが正常に更新されました！',
    ],

    'unable_to_add_blank_field' => '申し訳ありませんが、空白フィールドを追加できません',
    'data_already_exists' => '同じ名前で新規作成することはできません。既存のデータを使用してください。',

    'areYouSure' => '本当にこのレコードを削除しますか？',
    'areYouSureapprove' => '本当にこのレコードを承認しますか？',
    'areYouSurerestore' => '本当にこのデータベースを復元しますか？現在のデータベースが削除されます。',
    'deletetitle' => '削除の確認',
    'restoretitle' => '復元の確認',
    'approvaltitle' => '承認の確認',
    'areYouSureRestore' => '本当にこのレコードを復元しますか？',
    'error_message'   => 'エラーが発生しました。後でもう一度お試しください！',
    'no_record_found' => 'レコードが見つかりません！',
    'suspened' => 'あなたのアカウントは停止されています！',
    'unverified' => 'あなたのアカウントは未確認です！',
    'invalid_email' => '無効なメールアドレス',
    'invalid_otp' => '無効なOTP',
    'invalid_pin' => '無効なPIN',
    'wrong_credentials' => 'これらの資格情報は記録と一致しません！',
    'not_activate' => 'アカウントがアクティベートされていません。',
    'otp_sent_email' => '登録済みメールにOTPを正常に送信しました',
    'otp_sent_number' => 'ユーザーの携帯番号にOTPが正常に送信されました',
    'expire_otp' => 'OTPの有効期限が切れました',
    'verified_otp' => 'OTPが正常に確認されました。',
    'invalid_token_email' => '無効なトークンまたはメール！',
    'success' => '成功',
    'register_success' => 'アカウントが正常に作成されました！',
    'login_success' => '正常にログインしました！',
    'logout_success' => '正常にログアウトしました！',
    'warning_select_record' => '少なくとも1つのレコードを選択してください',
    'required_role' => '指定されたメールを持つユーザーは必要な役割を持っていません。',

    'invalid_token'                 => 'アクセストークンの有効期限が切れました。もう一度ログインしてください。',
    'not_authorized'                => 'このリソース/APIにアクセスする権限がありません',
    'not_found'                     => '見つかりません！',
    'endpoint_not_found'            => 'エンドポイントが見つかりません',
    'resource_not_found'            => 'リソースが見つかりません',
    'token_invalid'                 => 'トークンが無効です',
    'unexpected'                    => '予期しない例外が発生しました。後でもう一度試してください',

    'data_retrieved_successfully'   => 'データが正常に取得されました',
    'record_retrieved_successfully' => 'レコードが正常に取得されました',
    'record_created_successfully'   => 'レコードが正常に作成されました',
    'record_updated_successfully'   => 'レコードが正常に更新されました',
    'record_deleted_successfully'   => 'レコードが正常に削除されました',
    'password_updated_successfully' => 'パスワードが正常に更新されました',

    'profile_updated_successfully'  => 'プロフィールが正常に更新されました',
    'language_updated_successfully' => '言語が正常に更新されました',
    'account_deactivate'            => 'アカウントが無効になっています。管理者に連絡してください。',
    'user_account_deactivate'       => 'アカウントが無効になりました。',
    'minimum_value'                 => '値は少なくとも1でなければなりません',

    'contact' => [
        'store' => [
            'success' => 'メッセージが正常に送信されました。できるだけ早くご連絡いたします。'
        ]
    ],

    'rating' => [
        'store' => [
            'success' => 'フィードバックが正常に送信されました。サービス向上にご協力いただきありがとうございます。'
        ]
    ],

    'created_successfully'   => '正常に作成されました',
    'updated_successfully'   => '正常に更新されました',
    'deleted_successfully'   => '正常に削除されました',
    'status_update_successfully' => 'ステータスが正常に更新されました！',

    'location' => [
        'slug' => 'このスラッグは既に存在します。別のタイトルを試すか、カスタムスラッグを使用してください。',
    ],

    'service' => [
        'slug' => 'このスラッグは既に存在します。別のタイトルを試すか、カスタムスラッグを使用してください。',
    ],

    'society' => [
        'not_verified' => 'ユーザーのコミュニティ詳細はアップロードされていません'
    ],

    'complaint' => [
        'store_success' => '苦情が正常に送信されました',
        'mark_resolved' => '苦情が正常に解決済みとしてマークされました',
        'comment_send' => 'コメントが正常に送信されました',
    ],

    'support' => [
        'submit_success' => 'サポートリクエストが正常に送信されました。'
    ],

    'payment_request' => [
        'sucess_payment' => '支払いが正常に送信されました。'
    ],

    'amenity' => [
        'advance_booking_error' => '施設予約は:days日前までに予約する必要があります',
        'already_booked' => '選択された時間帯ですでに予約されています',
        'booked_success' => '施設が正常に予約されました',
        'delete_error_booking_exist' => 'この施設は予約されているため削除できません。'
    ],

    'post' => [
        'slug' => 'このスラッグは既に存在します。別のタイトルを試すか、カスタムスラッグを使用してください。',
        'store_success' => '投稿が正常に作成されました',
        'update_success' => '投稿が正常に更新されました',
        'delete_success' => '投稿が正常に削除されました',
        'save_post' => '投稿が正常に保存されました',
        'unsave_post' => '投稿が正常に未保存されました',
        'comment_send' => 'コメントが正常に送信されました',
    ],

    'daily_help' => [
        'create_success' => '日常の助けが正常に作成されました',
        'update_success' => '日常の助けが正常に更新されました',
        'delete_success' => '日常の助けが正常に削除されました',
    ],

    'resident_vehicle' => [
        'create_success' => '車両が正常に作成されました',
        'update_success' => '車両が正常に更新されました',
        'delete_success' => '車両が正常に削除されました',
    ],

    'frequest_entry' => [
        'create_success' => '頻繁なエントリが正常に作成されました',
        'update_success' => '頻繁なエントリが正常に更新されました',
        'delete_success' => '頻繁なエントリが正常に削除されました',
    ],

    'family_member' => [
        'create_success' => '家族のメンバーが正常に作成されました',
        'update_success' => '家族のメンバーが正常に更新されました',
        'delete_success' => '家族のメンバーが正常に削除されました',
    ],

    'resident_security_alert' => [
        'create_success' => 'セキュリティアラートの連絡先が正常に作成されました',
        'update_success' => 'セキュリティアラートの連絡先が正常に更新されました',
        'delete_success' => 'セキュリ

ティアラートの連絡先が正常に削除されました',
    ],

    'visitor_mark_in_out' => '訪問者のステータスが変更されました',
    'allow_visitor_by_guard' => '訪問者が正常に許可されました',
    'recent_message' => '最新のメッセージリスト',
    'society_not_found' => 'コミュニティが見つかりません',

    'message_to_guard_success' => 'ガードにメッセージが正常に送信されました',
    'message_to_admin_success' => '管理者にメッセージが正常に送信されました',

    'notification' => [
        'not_found'                 => '通知が見つかりません',
        'mark_as_read'              => '通知が既読としてマークされました',
        'no_notification'           => 'クリアする通知がありません！',
        'clear_notification'        => 'すべての通知がクリアされました',
        'delete'                    => '通知が正常に削除されました！',
        'threat_alert_success'      => 'セキュリティアラートが正常に送信されました',
        'visitor_status_success'    => '訪問者のステータスが正常に更新されました',
        'visitor_status_error_already_updated'    => '訪問者リクエストはすでに処理されています。これ以上の操作は不要です',
        'mark_all_read'              => 'すべての通知が既読としてマークされました',
    ],

    // 通知メッセージ
    'notification_messages'  => [
        'visitor_on_gate_no_preapproved' => '<strong>:visitor_type</strong> 正在门口等候',

        // セキュリティアラート
        'security_alert' => [
            'fire_alert'       => '火警：<strong>社区内已报告火灾</strong>',
            'lift_alert'       => '<strong>电梯困人</strong>：有人被困在电梯内。',
            'animal_alert'     => '<strong>动物警报</strong>：发现一只流浪动物',
            'visitor_alert'    => '<strong>访客警报</strong>：报告有未经确认的访客',
        ],

        'visitor_status_by_resident' => '访客已被 <strong>:status</strong>。已授予访问权限',


        'post' => [
            'new_post' => '新しい投稿が作成されました',
            'post_updated' => '投稿が更新されました',

            'create_post_title' => '新しい投稿',
            'create_post_message' => ':user_nameが新しい投稿をアップロードしました',

            'comment_post_title' => '投稿へのコメント',
            'comment_post_message' => ':user_nameがあなたの投稿に新しいコメントを追加しました。',

            'like_post_title' => '投稿への「いいね」',
            'like_post_message' => ':user_nameがあなたの投稿に「いいね」しました。',
        ],

        'notice_board' => [
            'new_announcement' => '新しい告知が作成されました',
            'announcement_updated' => '告知が更新されました',

            'comment_notification' => ':user_nameが:announcement_titleにコメントしました',
            'like_notification' => ':user_nameが:announcement_titleに:typeしました',
            'vote_poll' => ':user_nameが:announcement_titleの投票に投票しました',
        ],

        'conversation' => [
            'new_message_title' => '新しいメッセージを受信しました',
            'new_message_message' => ':user_nameから新しいメッセージがあります',
        ],

        'payment_request' => [
            'request' => '支払いの要求がありました',
        ],

        'complaint' => [
            'status_message'  => 'あなたの苦情は:statusです',
            'raise_complaint'   => ':user_nameが新しい苦情を提出しました。詳細を確認し、必要な処置を取ってください。',
            'resolve_complaint' => ':user_nameによって提出された苦情が解決済みとしてマークされました',

            'comment_by_other_complaint_title' => '苦情へのコメント',
            'comment_by_other_complaint' => ':user_nameがあなたの苦情に新しいコメントを追加しました。',
        ],

        'amenity_booking' => [
            'status_message'  => 'あなたの施設予約は<strong>:status</strong>です',

            'booking_notification' => '<strong>:user_name</strong>が<strong>:requested_date</strong>に<strong>:amenity_name</strong>の予約をリクエストしました。',
        ],

        'service_booking' => [
            'status_message'  => 'あなたのサービス予約は<strong>:status</strong>です',
        ],

        'visitor_log' => [
            'status_message'  => '訪問者は<strong>:status</strong>です',
        ],

        'ai_box' => [
            'fall_detection' => [
                'title' => '<strong>転倒が検出されました！</strong>',
                'message' => ':society_nameの<strong>:camera_location</strong>に設置されたカメラによって転倒が検出されました。緊急対応が必要な場合は、直ちに確認してください。',
                'success' => 'メッセージが正常に送信されました。',
                'socieity_error' => 'カメラがコミュニティに割り当てられていません',
            ]
        ],

        'service' => [
            'book_by_user' => '<strong>:user_name</strong>が<strong>:service_name</strong>を予約しました。',
        ],

        'message_to_guard_admin' => [
            'title' => '<strong>:user_name</strong>からの新しいメッセージ',
            'message' => '<strong>:user_name</strong>から新しいメッセージを受信しました。',
        ]
    ],

    'no_resident_found_in_unit' => '選択されたユニットに住民が見つかりません',

    'announcement' => [
        'error_expire_date' => 'この投票は期限切れです。これ以上投票することはできません。',
        'id_not_exist' => '申し訳ありません！IDが存在しません。',
    ]

];
