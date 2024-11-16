<?php

return [

    'menus' => [
        'dashboard' => 'ダッシュボード',
        'setting' => '設定',
        'user' => 'ユーザー',
        'categories' => 'カテゴリ',
        'services' => 'サービス',
        'roles' => '役割',
        'permissions' => '権限',
        'locations' => '場所',
        'societies' => 'コミュニティ',
        'service_bookings' => 'サービス予約',
        'buildings' => '建物',
        'units' => 'ユニット',
        'announcements' => 'お知らせ',
        'notice_board' => '掲示板',
        'complaints_types' => '苦情の種類',
        'roles_and_permissions' => '役割と権限',
        'complaints' => '苦情',
        'guards' => 'ガード',
        'residents' => '住民',
        'posts' => '投稿管理',
        'faqs' => 'よくある質問',
        'supports' => 'サポート',
        'visitor_logs' => '訪問者ログ',
        'comments' => 'コメント',
        'features' => '特徴',
        'payment_methods' => '支払い方法',
        'amenity_booking' => '施設予約',
        'amenities' => '施設',
        'providers' => 'プロバイダー',
        'transaction' => '取引',
        'payment_requests' => '支払い要求',
        'property_types' => '物件タイプ',
        'maintenance_categories' => 'メンテナンスカテゴリ',
        'maintenance_items' => 'メンテナンス項目',
        'resident_vehicles' => '住民の車両',
        'resident_daily_helps' => '住民の日常の助け',
        'maintenance_plans' => 'メンテナンス計画',
        'property_managements' => '物件管理',
        'delivery_types' => '配達タイプ',
        'delivery_managements' => '配達管理',
        'transaction_reports' => '取引レポート',
        'message' => 'メッセージ',
        'cameras' => 'カメラ',
        'notifications' => '通知',
        'service_categories' => 'サービスカテゴリ',
        'aibox_notifications' => 'AIボックス通知',
    ],

    'datatable' => [
        'show' => '表示',
        'entries' => 'エントリ',
        'showing' => '表示中',
        'to' => 'から',
        'of' => 'の',
        'search' => '検索',
        'previous' => '前へ',
        'next' => '次へ',
        'first' => '最初',
        'last' => '最後',
        'data_not_found' => 'テーブルに利用可能なデータがありません',
        'processing' => '処理中...',
        'select_date' => '日付を選択',
    ],

    'dashboard' => [
        'title' => 'ダッシュボード',
        'title_singular' => 'ダッシュボード',
        'fields' => [
            'new_registration' => '新規登録',
            'alert_graph' => '新規登録アラートグラフ',
            'no_users' => 'ユーザー数',
            'count' => 'ユーザー総数',
            'filter_by_date' => '日付でフィルタ',
            'total' => '合計',
            'transaction_count' => '総取引数',
            'transaction_alert_graph' => '新規取引アラートグラフ',
            'activity_count' => '最近の活動総数',
            'activity_alert_graph' => '新しい最近の活動アラートグラフ',
        ],
        'labels' => [
            'hour' => '時',
            'day' => '日',
            'week' => '週',
            'month' => '月',
            'year' => '年',
            'all' => 'すべて',
            'custom_range' => 'カスタム範囲',
        ],
        'apply' => '適用',
        'clear' => 'クリア',
        'custom_range' => 'カスタム範囲',
        'days_of_week' => [
            'sunday' => '日曜日',
            'monday' => '月曜日',
            'tuesday' => '火曜日',
            'wednesday' => '水曜日',
            'thursday' => '木曜日',
            'friday' => '金曜日',
            'saturday' => '土曜日',
        ],
        'month_names' => [
            'january' => '1月',
            'february' => '2月',
            'march' => '3月',
            'april' => '4月',
            'may' => '5月',
            'june' => '6月',
            'july' => '7月',
            'august' => '8月',
            'september' => '9月',
            'october' => '10月',
            'november' => '11月',
            'december' => '12月',
        ],
    ],

    'header' => [
        'title' => 'ヘッダー',
        'title_singular' => 'ヘッダー',
        'fields' => [],
    ],

    'user' => [
        'title' => 'ユーザー',
        'title_singular' => 'ユーザー',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'email' => 'メール',
            'mobile' => '携帯番号',
            'mobile_verified' => '携帯が確認済みですか？',
            'is_enabled' => '有効',
            'password' => 'パスワード',
            'roles' => '役割',
            'language' => '言語',
            'society' => 'コミュニティ',
            'description' => '説明',
            'profile_image' => 'プロフィール画像',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'setting' => [
        'title' => '設定',
        'add_message_subject' => 'メッセージ件名を追加',
        'title_singular' => '設定',
        'fields' => [],
        'contact_details' => [
            'title' => 'お問い合わせ',
            'fields' => [
                'contact_email' => '連絡用メール',
                'contact_phone' => '連絡用電話',
                'contact_details' => '連絡詳細',
            ]
        ],
        'message_subject' => [
            'subject_name' => '件名',
        ],
    ],

    'location' => [
        'title' => '場所',
        'title_singular' => '場所',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'scope' => '範囲',
            'image' => '画像',
            'status' => 'ステータス',
            'meta_key' => 'メタキー',
            'meta_value' => 'メタ値',
            'parent' => '親',
            'sort_order' => 'ソート順',
            'publish_date' => '公開日',
            'meta_fields' => 'メタフィールド',
            'meta_description' => 'メタ説明',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
            'add_metafields' => 'メタフィールドを追加',
        ],
    ],

    'service' => [
        'title' => 'サービス',
        'title_singular' => 'サービス',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'provider' => '提供者',
            'slug' => 'スラッグ',
            'image' => '画像',
            'description' => '説明',
            'feature' => '特徴',
            'status' => 'ステータス',
            'sort_order' => 'ソート順',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'service_booking' => [
        'title' => 'サービス予約',
        'title_singular' => 'サービス予約',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'detail' => '詳細',
            'booking_date' => '予約日',
            'booking_time' => '予約時間',
            'service' => 'サービス',
            'society' => 'コミュニティ',
            'unit' => 'ユニット',
            'status' => 'ステータス',
            'action' => 'アクション',
            'accept' => '受け入れる',
            'reject' => '拒否',
            'pending' => '保留中',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'permission' => [
        'title' => '権限',
        'title_singular' => '権限',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'route_name' => 'ルート名',
            'key' => 'キー',
            'permission' => '権限',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'role' => [
        'title' => '役割',
        'title_singular' => '役割',
        'fields' => [
            'id' => 'ID',
            'role_name' => '役割名',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'society' => [
        'title' => 'コミュニティ',
        'title_singular' => 'コミュニティ',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'city' => '市',
            'district' => '地区',
            'fire_alert' => '火災警報',
            'lift_alert' => 'エレベーター警報',
            'animal_alert' => '動物警報',
            'visitor_alert' => '訪問者警報',
            'image' => '画像',
            'status' => 'ステータス',
            'address' => '住所',
            'latitude' => '緯度',
            'longitude' => '経度',
            'meta_key' => 'メタキー',
            'meta_value' => 'メタ値',
            'meta_fields' => 'メタフィールド',
            'meta_description' => 'メタ説明',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
            'add_metafields' => 'メタフィールドを追加',
        ],
    ],

    'building' => [
        'title' => '建物',
        'title_singular' => '建物',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'city' => '市',
            'society' => 'コミュニティ',
            'status' => 'ステータス',
            'address' => '住所',
            'latitude' => '緯度',
            'longitude' => '経度',
            'meta_key' => 'メタキー',
            'meta_value' => 'メタ値',
            'meta_fields' => 'メタフィールド',
            'meta_description' => 'メタ説明',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
            'add_metafields' => 'メタフィールドを追加',
        ],
    ],

    'unit' => [
        'title' => 'ユニット',
        'title_singular' => 'ユニット',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'society' => 'コミュニティ',
            'building' => '建物',
            'status' => 'ステータス',
            'meta_key' => 'メタキー',
            'meta_value' => 'メタ値',
            'meta_fields' => 'メタフィールド',
            'meta_description' => 'メタ説明',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
            'add_metafields' => 'メタフィールドを追加',
        ],
    ],

    'announcement' => [
        'title' => '掲示板',
        'title_singular' => '掲示板',
        'fields' => [
            'id' => 'ID',
            'type' => '告知の種類',
            'title' => 'タイトル',
            'message' => 'メッセージ',
            'posted_by' => '投稿者',
            'society' => 'コミュニティ',
            'status' => 'ステータス',
            'option' => 'オプション',
            'poll_type' => '投票タイプ',
            'expire_date' => '有効期限',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'complaint_type' => [
        'title' => '苦情の種類',
        'title_singular' => '苦情の種類',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'image' => '画像',
            'status' => 'ステータス',
            'sort_order' => 'ソート順',
            'updated_by' => '更新者',
            'created_by' => '作成者',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],


    'guard' => [
        'title' => 'ガード',
        'title_singular' => 'ガード',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'email' => 'メール',
            'security_pin' => 'セキュリティPIN',
            'guard_duty_status' => 'ガードの勤務状況',
            'mobile' => '携帯番号',
            'note' => 'メモ',
            'password' => 'パスワード',
            'roles' => '役割',
            'society' => 'コミュニティ',
            'profile_image' => 'プロフィール画像',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'resident' => [
        'title' => '住民',
        'title_singular' => '住民',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'email' => 'メール',
            'mobile' => '携帯番号',
            'password' => 'パスワード',
            'note' => 'メモ',
            'is_approved' => '承認済み',
            'type' => 'タイプ',
            'roles' => '役割',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'profile_image' => 'プロフィール画像',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'post' => [
        'title' => '投稿',
        'title_singular' => '投稿',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'user' => 'ユーザー',
            'description' => '説明',
            'status' => 'ステータス',
            'publish' => '公開',
            'unpublish' => '非公開',
            'draft' => '下書き',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
            'total_comments' => 'コメント総数',
            'detail' => '詳細',
            'created_by' => '作成者',
            'total_views' => '閲覧総数',
            'total_likes' => 'いいね総数',
            'total_dislikes' => '嫌い総数',
            'images' => '画像',
            'video' => 'ビデオ',
            'video_url' => 'ビデオURL',
            'post_type' => '投稿タイプ',
            'content' => '内容',
            'stats' => '統計',
            'likes_count' => 'いいね数',
            'comments_count' => 'コメント数',
            'views_count' => '閲覧数',
            'post_text' => '投稿テキスト',
            'video_link_text' => 'ビデオを見るにはここをクリック',
        ],
    ],

    'faq' => [
        'title' => 'よくある質問',
        'title_singular' => 'よくある質問',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'short_description' => '短い説明',
            'description' => '説明',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'complaint' => [
        'title' => '苦情',
        'title_singular' => '苦情',
        'fields' => [
            'id' => 'ID',
            'description' => '説明',
            'message' => 'メッセージ',
            'type' => 'タイプ',
            'complaint_type' => '苦情の種類',
            'society' => 'コミュニティ',
            'user' => 'ユーザー',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'support' => [
        'title' => 'サポートリクエスト',
        'title_singular' => 'サポートリクエスト',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'email' => 'メール',
            'topic' => 'トピック',
            'message' => 'メッセージ',
            'user' => 'ユーザー',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'visitor' => [
        'title' => '訪問者',
        'title_singular' => '訪問者',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'visitor_info' => '訪問者情報',
            'contact' => '連絡先情報',
            'type' => 'タイプ',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'user' => '住人の名前',
            'notes' => 'メモ',
            'status' => 'ステータス',
            'gatepass_code' => 'ゲートパス',
            'other_info'    => '他の情報',
            'visitor_note'  => '訪問者のメモ',
            'keep_package'  => 'パッケージを保持',
            'cab_number' => 'タクシー番号',
            'visit_date' => '訪問日',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'comment' => [
        'title' => 'コメント',
        'title_singular' => 'コメント',
        'fields' => [
            'id' => 'ID',
            'comment' => 'コメント',
            'user' => 'ユーザー',
            'type' => 'タイプ',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'amenity_booking' => [
        'title' => '施設予約',
        'title_singular' => '施設予約',
        'fields' => [
            'id' => 'ID',
            'resident' => '住民',
            'amenity' => '施設',
            'date_period' => '期間',
            'from_date' => '開始日',
            'to_date' => '終了日',
            'amount' => '金額',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'amenity' => [
        'title' => '施設',
        'title_singular' => '施設',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'description' => '説明',
            'society' => 'コミュニティ',
            'fee' => '料金',
            'capacity' => '収容能力',
            'booking_capacity' => '予約収容能力',
            'advance_booking_days' => '事前予約日数',
            'max_days_per_unit' => '1ユニットあたりの最大予約日数',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'feature' => [
        'title' => '特徴',
        'title_singular' => '特徴',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'payment_method' => [
        'title' => '支払い方法',
        'title_singular' => '支払い方法',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'slug' => 'スラッグ',
            'type' => 'タイプ',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'provider' => [
        'title' => 'プロバイダー',
        'title_singular' => 'プロバイダー',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'email' => 'メール',
            'mobile' => '携帯番号',
            'password' => 'パスワード',
            'description' => '説明',
            'address' => '住所',
            'features' => '特徴',
            'is_approved' => '承認済み',
            'type' => 'タイプ',
            'roles' => '役割',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'profile_image' => 'プロフィール画像',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'transaction' => [
        'title' => '取引',
        'title_singular' => '取引',
        'fields' => [
            'id' => 'ID',
            'user' => 'ユーザー',
            'type' => 'タイプ',
            'meta' => 'メタ',
            'amount' => '金額',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'payment_request' => [
        'title' => '支払い要求',
        'title_singular' => '支払い要求',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'due_date' => '期日',
            'status' => 'ステータス',
            'amount' => '金額',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'property_type' => [
        'title' => '物件タイプ',
        'title_singular' => '物件タイプ',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'code' => 'コード',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'category' => [
        'title' => 'メンテナンスカテゴリ',
        'title_singular' => 'カテゴリ',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'description' => '説明',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'maintenance_item' => [
        'title' => 'メンテナンス項目',
        'title_singular' => 'メンテナンス項目',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'description' => '説明',
            'duration' => '期間',
            'budget' => '予算',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'maintenance_plan' => [
        'title' => 'メンテナンス計画',
        'title_singular' => 'メンテナンス計画',
        'fields' => [
            'id' => 'ID',
            'society' => 'コミュニティ',
            'year' => '年',
            'category' => 'カテゴリ',
            'item' => '項目',
            'month' => '月',
            'total_budget' => '総予算',
            'budget' => '予算',
            'comments' => 'コメント',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'resident_daily_help' => [
        'title' => '住民の日常の助け',
        'title_singular' => '住民の日常の助け',
        'fields' => [
            'id' => 'ID',
            'name' => '名前',
            'contact' => '連絡先',
            'help_type' => '助けのタイプ',
            'resident_id' => '住民',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'resident_vehicle' => [
        'title' => '住民の車両',
        'title_singular' => '住民の車両',
        'fields' => [
            'id' => 'ID',
            'resident_id' => '住民の名前',
            'vehicle_number' => '車両番号',
            'vehicle_type' => '車両タイプ',
            'vehicle_model' => '車両モデル',
            'parking_slot' => '駐車場番号',
            'color'         => '色',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'property_management' => [
        'title' => '物件管理',
        'title_singular' => '物件管理',
        'fields' => [
            'id' => 'ID',
            'item_name' => '項目名',
            'property_type' => '物件タイプ',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'image' => '画像',
            'description' => '説明',
            'amount' => '金額',
            'unit_price' => '単価',
            'purchase_date' => '購入日',
            'location' => '場所',
            'allocation' => '割り当て',
            'property_code' => '物件コード',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'delivery_type' => [
        'title' => '配達タイプ',
        'title_singular' => '配達タイプ',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'other_title' => 'その他のタイトル',
            'description' => '説明',
            'notify_user' => 'ユーザーに通知',
            'due_payment' => '支払期日',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'delivery_management' => [
        'title' => '配達管理',
        'title_singular' => '配達管理',
        'fields' => [
            'id' => 'ID',
            'subject' => '件名',
            'delivery_type' => '配達タイプ',
            'message' => 'メッセージ',
            'note' => 'メモ',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'actor_name' => '担当者名',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'camera' => [
        'title' => 'カメラ',
        'title_singular' => 'カメラ',
        'fields' => [
            'id' => 'ID',
            'subject' => '件名',
            'camera_id' => 'カメラID',
            'lacated_at' => '設置場所',
            'description' => '説明',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'service_category' =>

    [
        'title' => 'サービスカテゴリ',
        'title_singular' => 'サービスカテゴリ',
        'fields' => [
            'id' => 'ID',
            'title' => 'タイトル',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'aibox_notification' => [
        'title' => 'AIボックス通知',
        'title_singular' => 'AIボックス通知',
        'fields' => [
            'id' => 'ID',
            'society' => 'コミュニティ',
            'building' => '建物',
            'unit' => 'ユニット',
            'camera_id' => 'カメラID',
            'location' => '場所',
            'api_type' => 'APIタイプ',
            'event_id' => 'イベントID',
            'event_code' => 'イベントコード',
            'event_name' => 'イベント名',
            'event_desc' => 'イベント説明',
            'status' => 'ステータス',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'deleted_at' => '削除日',
        ],
    ],

    'property_managment_report' => [
        'fields' => [
            'no' => '番号',
            'item' => '項目',
            'image' => '画像',
            'location' => '場所',
            'amount' => '金額',
            'specifications' => '仕様',
            'purchased_date' => '購入日',
            'unit_price' => '単価',
            'allocation' => '割り当て',
            'code' => 'コード',
            'updated_at' => '更新日',
        ]
    ],

    'plan_months' => [
        '1月',
        '2月',
        '3月',
        '4月',
        '5月',
        '6月',
        '7月',
        '8月',
        '9月',
        '10月',
        '11月',
        '12月'
    ]
];
