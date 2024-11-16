<?php
return [

    'crud' => [
        'add_record'    => '成功新增！',
        'update_record' => '成功更新！',
        'delete_record' => '此記錄已成功刪除！',
        'restore_record' => '此記錄已成功恢復！',
        'merge_record'  => '此記錄已成功合併！',
        'approve_record' => '記錄已成功批准！',
        'status_update' => '狀態已成功更新！',
    ],

    'unable_to_add_blank_field' => '抱歉，無法新增空白欄位在',
    'data_already_exists' => '抱歉，無法以相同名稱創建新項目，請使用現有項目。',

    'areYouSure' => '您確定要刪除此記錄嗎？',
    'areYouSureapprove' => '您確定要批准此記錄嗎？',
    'areYouSurerestore' => '您確定要恢復此資料庫嗎？這將刪除您當前的資料庫。',
    'deletetitle' => '刪除確認',
    'restoretitle' => '恢復確認',
    'approvaltitle' => '批准確認',
    'areYouSureRestore' => '您確定要恢復此記錄嗎？',
    'error_message'   => '發生錯誤...請稍後再試！',
    'no_record_found' => '找不到記錄！',
    'suspened' => "您的帳戶已被暫停！",
    'unverified' => "您的帳戶尚未驗證！",
    'invalid_email' => '無效的電子郵件',
    'invalid_otp' => '無效的OTP',
    'invalid_pin' => '無效的PIN碼',
    'wrong_credentials' => '這些憑據與我們的記錄不符！',
    'not_activate' => '您的帳戶尚未啟動。',
    'otp_sent_email' => '我們已成功將OTP發送到您註冊的電子郵件。',
    'otp_sent_number' => "OTP已成功發送到用戶的手機號碼。",
    'expire_otp' => 'OTP已過期',
    'verified_otp' => 'OTP已成功驗證。',
    'invalid_token_email' => '無效的Token或電子郵件！',
    'success' => '成功',
    'register_success' => '您的帳戶已成功創建！',
    'login_success' => '您已成功登錄！',
    'logout_success' => '已成功登出！',
    'warning_select_record' => '請至少選擇一個記錄',
    'required_role' => "指定電子郵件的用戶沒有所需的角色。",

    'invalid_token'                 => '您的訪問權限已過期。請重新登錄。',
    'not_authorized'                => '無權訪問此資源/ API',
    'not_found'                     => '找不到！',
    'endpoint_not_found'            => '找不到端點',
    'resource_not_found'            => '找不到資源',
    'token_invalid'                 => 'Token無效',
    'unexpected'                    => '發生意外錯誤。請稍後重試。',

    'data_retrieved_successfully'   => '資料成功獲取',
    'record_retrieved_successfully' => '記錄成功獲取',
    'record_created_successfully'   => '記錄成功創建',
    'record_updated_successfully'   => '記錄成功更新',
    'record_deleted_successfully'   => '記錄成功刪除',
    'password_updated_successfully' => '密碼成功更新',

    'profile_updated_successfully'  => '個人資料成功更新',
    'language_updated_successfully'  => '語言成功更新',
    'account_deactivate'            => '您的帳戶已被停用。請聯繫管理員。',
    'user_account_deactivate'       => '您的帳戶已被停用。',
    'minimum_value'                 => '值必須至少為1',

    'contact' => [
        'store' => [
            'success' => "您的訊息已成功發送，我們將盡快與您聯繫。"
        ]
    ],

    'rating' => [
        'store' => [
            'success' => "您的反饋已成功提交。我們感謝您為改進服務所付出的時間和努力。"
        ]
    ],

    'created_successfully'   => '成功創建',
    'updated_successfully'   => '成功更新',
    'deleted_successfully'   => '成功刪除',
    'status_update_successfully' => '狀態成功更新！',

    'location' => [
        'slug' => '此slug已存在。請嘗試使用不同的標題或自定義slug。',
    ],

    'service' => [
        'slug' => '此slug已存在。請嘗試使用不同的標題或自定義slug。',
    ],

    'society' => [
        'not_verified' => '用戶的社區詳細資料未上傳'
    ],

    'complaint' => [
        'store_success' => '您的投訴已成功提交',
        'mark_resolved' => '您的投訴已成功標記為解決',
        'comment_send' => '您的評論已成功發送',
    ],

    'support' => [
        'submit_success' => '您的支援請求已成功提交。'
    ],
    'payment_request' => [
        'sucess_payment' => '付款已成功提交。'
    ],

    'amenity' => [
        'advance_booking_error' => '設施預訂必須在 :days 天前完成',
        'already_booked' => '所選時間段的設施已被預訂',
        'booked_success' => '設施已成功預訂',
        'delete_error_booking_exist' => '由於此設施已被預訂，無法刪除。'
    ],

    'post' => [
        'slug' => '此slug已存在。請嘗試使用不同的標題或自定義slug。',
        'store_success' => '您的帖子已成功創建',
        'update_success' => '您的帖子已成功更新',
        'delete_success' => '您的帖子已成功刪除',
        'save_post' => '帖子已成功保存',
        'unsave_post' => '帖子已成功取消保存',
        'comment_send' => '您的評論已成功發送',
    ],

    'daily_help' => [
        'create_success' => '日常幫助已成功創建',
        'update_success' => '日常幫助已成功更新',
        'delete_success' => '日常幫助已成功刪除',
    ],

    'resident_vehicle' => [
        'create_success' => '車輛已成功創建',
        'update_success' => '車輛已成功更新',
        'delete_success' => '車輛已成功刪除',
    ],

    'frequest_entry' => [
        'create_success' => '常用條目已成功創建',
        'update_success' => '常用條目已成功更新',
        'delete_success' => '常用條目已成功刪除',
    ],

    'family_member' => [
        'create_success' => '家庭成員已成功創建',
        'update_success' => '家庭成員已成功更新',
        'delete_success' => '家庭成員已成功刪除',
    ],
    'resident_security_alert' => [
        'create_success' => '安全警報聯絡人已成功創建',
        'update_success' => '安全警報聯絡人已成功更新',
        'delete_success' => '安全警報聯絡人已成功刪除',
    ],

    'visitor_mark_in_out' => '訪客狀態已更改',
    'allow_visitor_by_guard' => '訪客已成功允許',
    'recent_message' => '最近的消息列表',
    'society_not_found' => '找不到社區',

    'message_to_guard_success' => '消息已成功發送給警衛',
    'message_to_admin_success' => '消息已成功發送給管理員',

    'notification' => [
        'not_found'                 => '找不到通知',
        'mark_as_read'              => '通知已標記為已讀',
        'no_notification'           => '無可清除的通知！',
        'clear_notification'        => '所有通知已被清除',
        'delete'                    => '通知已成功刪除！',
        'threat_alert_success'      => '安全警報已成功發送',
        'visitor_status_success'    => '訪客狀態已成功更新',
        'visitor_status_error_already_updated'    => '訪客請求已處理。無需進一步操作。',
        'mark_all_read'              => '所有通知已標記為已讀',
    ],

    '

notification_messages'  => [
        'visitor_on_gate_no_preapproved' => ':visitor_type 在大門等待',

        'security_alert' => [
            'fire_alert'       => '火警：社區報告了火災',
            'lift_alert'       => '電梯卡住：目前有人被困在電梯中。',
            'animal_alert'     => '動物警報：發現流浪動物',
            'visitor_alert'    => '訪客警報：報告了不明訪客',
        ],

        'visitor_status_by_resident' => '訪客已被 :status。',

        'post' => [
            'new_post' => '新帖子已創建',
            'post_updated' => '帖子已更新',

            'create_post_title' => '新帖子',
            'create_post_message' => ':user_name 上傳了一個新帖子',

            'comment_post_title' => "對帖子評論",
            'comment_post_message' => ":user_name 在您的帖子上新增了一條評論。",

            'like_post_title' => "對帖子點讚",
            'like_post_message' => ":user_name 對您的帖子點了贊。",
        ],

        'notice_board' => [
            'new_announcement' => '公告已創建',
            'announcement_updated' => '公告已更新',

            'comment_notification' => ":user_name 已在公告 :announcement_title 上發表評論",
            'like_notification' => ":user_name 已 :type 公告 :announcement_title",
            'vote_poll' => ":user_name 已在投票 :announcement_title 中投票",
        ],

        'conversation' => [
            'new_message_title' => "收到新消息",
            'new_message_message' => "您收到來自 :user_name 的新消息",
        ],

        'payment_request' => [
            'request' => '請求付款',
        ],

        'complaint' => [
            'status_message'  => '您的投訴狀態為 :status',
            'raise_complaint'   => ":user_name 提出了新投訴。請查看詳細信息並採取必要措施。",
            'resolve_complaint' => ":user_name 提出的投訴已被標記為解決",

            'comment_by_other_complaint_title' => "投訴評論",
            'comment_by_other_complaint' => ":user_name 在您的投訴中新增了一條評論。",
        ],

        'amenity_booking' => [
            'status_message'  => '您的設施預訂狀態為 :status',

            'booking_notification' => ":user_name 請求在 :requested_date 預訂 :amenity_name。",
        ],

        'service_booking' => [
            'status_message'  => '您的服務預訂狀態為 :status',
        ],

        'visitor_log' => [
            'status_message'  => '訪客狀態為 :status',
        ],

        'ai_box' => [
            'fall_detection' => [
                'title' => "跌倒檢測！",
                'message' => "位於 :society_name 的 :camera_location 處的攝像頭檢測到跌倒。請立即查看以提供緊急援助。",
                'success' => '消息已成功發送。',
                'socieity_error' => '攝像頭尚未分配給社區',
            ]
        ],

        'service' => [
            'book_by_user' => ":user_name 已預訂 :service_name。"
        ],

        'message_to_guard_admin' => [
            'title' => "來自 :user_name 的新消息",
            'message' => "您已收到來自 :user_name 的新消息。",
        ]
    ],

    'no_resident_found_in_unit' => "在選定的單位中未找到住戶",

    'announcement' => [
        'error_expire_date' => '此投票已過期。您無法再投票。',
        'id_not_exist' => '抱歉！！ID不存在。'
    ]

];