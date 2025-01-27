<?php
return [

    'accepted' => ':attributeを受け入れる必要があります。',
    'accepted_if' => ':otherが:valueの場合、:attributeを受け入れる必要があります。',
    'active_url' => ':attributeは有効なURLでなければなりません。',
    'after' => ':attributeは:dateより後の日付でなければなりません。',
    'after_or_equal' => ':attributeは:dateと同じかそれ以降の日付でなければなりません。',
    'alpha' => ':attributeは文字のみを含む必要があります。',
    'alpha_dash' => ':attributeは文字、数字、ダッシュ、アンダースコアのみを含む必要があります。',
    'alpha_num' => ':attributeは文字と数字のみを含む必要があります。',
    'array' => ':attributeは配列でなければなりません。',
    'ascii' => ':attributeはシングルバイトの英数字と記号のみを含む必要があります。',
    'before' => ':attributeは:dateより前の日付でなければなりません。',
    'before_or_equal' => ':attributeは:date以前の日付でなければなりません。',
    'between' => [
        'array' => ':attributeは:minから:maxのアイテムを含む必要があります。',
        'file' => ':attributeは:minから:maxキロバイトの間でなければなりません。',
        'numeric' => ':attributeは:minから:maxの間でなければなりません。',
        'string' => ':attributeは:minから:max文字の間でなければなりません。',
    ],
    'boolean' => ':attributeはYesまたはNoでなければなりません。',
    'can' => ':attributeに不正な値が含まれています。',
    'confirmed' => ':attributeの確認が一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは有効な日付でなければなりません。',
    'date_equals' => ':attributeは:dateと同じ日付でなければなりません。',
    'date_format' => ':attributeは:format形式に一致する必要があります。',
    'decimal' => ':attributeは:decimal小数点を含む必要があります。',
    'declined' => ':attributeは拒否する必要があります。',
    'declined_if' => ':otherが:valueの場合、:attributeは拒否する必要があります。',
    'different' => ':attributeと:otherは異なる必要があります。',
    'digits' => ':attributeは:digits桁でなければなりません。',
    'digits_between' => ':attributeは:minから:max桁の間でなければなりません。',
    'dimensions' => ':attributeは無効な画像寸法を持っています。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは以下のいずれかで終わることはできません: :values。',
    'doesnt_start_with' => ':attributeは以下のいずれかで始まることはできません: :values。',
    'email' => ':attributeは有効なメールアドレスでなければなりません。',
    'ends_with' => ':attributeは以下のいずれかで終わる必要があります: :values。',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは以下の拡張子のいずれかを持つ必要があります: :values。',
    'file' => ':attributeはファイルでなければなりません。',
    'filled' => ':attributeに値が必要です。',
    'gt' => [
        'array' => ':attributeは:value個以上のアイテムを含む必要があります。',
        'file' => ':attributeは:valueキロバイト以上でなければなりません。',
        'numeric' => ':attributeは:valueより大きくなければなりません。',
        'string' => ':attributeは:value文字以上でなければなりません。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上のアイテムを含む必要があります。',
        'file' => ':attributeは:valueキロバイト以上でなければなりません。',
        'numeric' => ':attributeは:value以上でなければなりません。',
        'string' => ':attributeは:value文字以上でなければなりません。',
    ],
    'hex_color' => ':attributeは有効な16進数カラーでなければなりません。',
    'image' => ':attributeは画像でなければなりません。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeは:otherに存在する必要があります。',
    'integer' => ':attributeは整数でなければなりません。',
    'ip' => ':attributeは有効なIPアドレスでなければなりません。',
    'ipv4' => ':attributeは有効なIPv4アドレスでなければなりません。',
    'ipv6' => ':attributeは有効なIPv6アドレスでなければなりません。',
    'json' => ':attributeは有効なJSON文字列でなければなりません。',
    'lowercase' => ':attributeは小文字でなければなりません。',
    'lt' => [
        'array' => ':attributeは:value個未満のアイテムを含む必要があります。',
        'file' => ':attributeは:valueキロバイト未満でなければなりません。',
        'numeric' => ':attributeは:value未満でなければなりません。',
        'string' => ':attributeは:value文字未満でなければなりません。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下のアイテムを含む必要があります。',
        'file' => ':attributeは:valueキロバイト以下でなければなりません。',
        'numeric' => ':attributeは:value以下でなければなりません。',
        'string' => ':attributeは:value文字以下でなければなりません。',
    ],
    'mac_address' => ':attributeは有効なMACアドレスでなければなりません。',
    'max' => [
        'array' => ':attributeは:max個以下のアイテムを含む必要があります。',
        'file' => ':attributeは:maxキロバイト以下でなければなりません。',
        'numeric' => ':attributeは:max以下でなければなりません。',
        'string' => ':attributeは:max文字以下でなければなりません。',
    ],
    'max_digits' => ':attributeは:max桁以下でなければなりません。',
    'mimes' => ':attributeは以下の形式のファイルでなければなりません: :values。',
    'mimetypes' => ':attributeは以下の形式のファイルでなければなりません: :values。',
    'min' => [
        'array' => ':attributeは少なくとも:min個のアイテムを含む必要があります。',
        'file' => ':attributeは少なくとも:minキロバイトでなければなりません。',
        'numeric' => ':attributeは少なくとも:minでなければなりません。',
        'string' => ':attributeは少なくとも:min文字でなければなりません。',
    ],
    'min_digits' => ':attributeは少なくとも:min桁でなければなりません。',
    'missing' => ':attributeが存在しなければなりません。',
    'missing_if' => ':otherが:valueの場合、:attributeが存在しなければなりません。',
    'missing_unless' => ':otherが:valueでない限り、:attributeが存在しなければなりません。',
    'missing_with' => ':valuesが存在する場合、:attributeが存在しなければなりません。',
    'missing_with_all' => ':valuesがすべて存在する場合、:attributeが存在しなければなりません。',
    'multiple_of' => ':attributeは:valueの倍数でなければなりません。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数字でなければなりません。',
    'password' => [
        'letters' => ':attributeは少なくとも1つの文字を含む必要があります。',
        'mixed' => ':attributeは少なくとも1つの大文字と1つの小文字を含む必要があります。',
        'numbers' => ':attributeは少なくとも1つの数字を含む必要があります。',
        'symbols' => ':attributeは少なくとも1つの記号を含む必要があります。',
        'uncompromised' => '指定された:attributeはデータ漏洩で確認されています。別の:attributeを選択してください。',
        'regex' =>

 ':attributeは8文字以上で、大文字、数字、特殊文字を含む必要があります。',
    ],
    'present' => ':attributeが存在する必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeが存在する必要があります。',
    'present_unless' => ':otherが:valueでない限り、:attributeが存在する必要があります。',
    'present_with' => ':valuesが存在する場合、:attributeが存在する必要があります。',
    'present_with_all' => ':valuesがすべて存在する場合、:attributeが存在する必要があります。',
    'prohibited' => ':attributeは禁止されています。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは禁止されています。',
    'prohibited_unless' => ':otherが:valuesに含まれない限り、:attributeは禁止されています。',
    'prohibits' => ':attributeは:otherが存在することを禁止します。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeは必須です。',
    'required_array_keys' => ':attributeは:valuesに含まれるキーを含む必要があります。',
    'required_if' => ':otherが:valueの場合、:attributeが必須です。',
    'required_if_accepted' => ':otherが受け入れられている場合、:attributeが必須です。',
    'required_unless' => ':otherが:valuesに含まれない限り、:attributeが必須です。',
    'required_with' => ':valuesが存在する場合、:attributeが必須です。',
    'required_with_all' => ':valuesがすべて存在する場合、:attributeが必須です。',
    'required_without' => ':valuesが存在しない場合、:attributeが必須です。',
    'required_without_all' => ':valuesのいずれも存在しない場合、:attributeが必須です。',
    'same' => ':attributeは:otherと一致する必要があります。',
    'size' => [
        'array' => ':attributeは:size個のアイテムを含む必要があります。',
        'file' => ':attributeは:sizeキロバイトでなければなりません。',
        'numeric' => ':attributeは:sizeでなければなりません。',
        'string' => ':attributeは:size文字でなければなりません。',
    ],
    'starts_with' => ':attributeは以下のいずれかで始まる必要があります: :values。',
    'string' => ':attributeは文字列でなければなりません。',
    'timezone' => ':attributeは有効なタイムゾーンでなければなりません。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字でなければなりません。',
    'url' => ':attributeは有効なURLでなければなりません。',
    'ulid' => ':attributeは有効なULIDでなければなりません。',
    'uuid' => ':attributeは有効なUUIDでなければなりません。',
    'invalid' => '入力された:attributeは無効です。',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムメッセージ',
        ],
    ],

    'attributes' => [
        'name'              => '名前',
        'email'             => 'メールアドレス',
        'mobile_number'     => '携帯番号',
        'mobile_verified'   => '携帯認証済み',
        'password'          => 'パスワード',
        'language'          => '言語',
        'roles'             => '役割',
        'role'              => '役割',
        'society'           => 'コミュニティ',
        'profile_image'     => 'プロフィール画像',
        'description'       => '説明',
        'title'             => 'タイトル',
        'building'          => '建物',
        'address'           => '住所',
        'city'              => '都市',
        'latitude'          => '緯度',
        'longitude'         => '経度',
        'key'               => 'キー',
        'value'             => '値',
        'district'          => '地区',
        'fire_alert'        => '火災警報',
        'lift_alert'        => 'エレベーター警報',
        'animal_alert'      => '動物警報',
        'visitor_alert'     => '訪問者警報',
        'provider'          => 'プロバイダー',
        'slug'              => 'スラッグ',
        'sort_order'        => '並び順',
        'image'             => '画像',
        'is_featured'       => '注目',
        'provider'          => 'プロバイダー',
        'permissions'       => '権限',
        'vehicle_number'    => '車両番号',
        'vehicle_type'      => '車両タイプ',
        'vehicle_model'     => '車両モデル',
        'parking_slot_no'   => '駐車番号',
        'unit'              => 'ユニット',
        'resident'          => '住民名',
        'status'            => 'ステータス',
        'type'              => 'タイプ',
        'is_verified'       => '検証済み',
        'code'              => 'コード',
        'maintenance_item_id'   => 'アイテム名',
        'property_type_id'      => '物业类型',
        'unit_price'            => '単価',
        'purchase_date'         => '購入日',
        'amount'                => '金額',
        'allocation'            => '割り当て',
        'property_image'        => 'プロパティ画像',
        'post_image'            => '投稿画像',
        'due_date'              => '期限日',
        'method_type'           => '方法タイプ',
        'maintenance_item'      => 'メンテナンスアイテム',
        'year_of'               => '年',
        'category_id'           => 'カテゴリー',
        'month'                 => '月',
        'total_budget'          => '総予算',
        'comments'              => 'コメント',
        'parent_id'             => '親',
        'scope_id'              => 'スコープ',
        'note'                  => 'メモ',
        'short_description'     => '短い説明',
        'other'                 => 'その他',
        'password_confirmation' => 'パスワード確認',
        'location_id'           => '場所',
        'district_id'           => '地区',
        'message'               => 'メッセージ',
        'announcement_type'     => '告知タイプ',
        'posted_by'             => '投稿者',
        'fee'                   => '料金',
        'capacity'              => '容量',
        'booking_capacity'      => '予約容量',
        'advance_booking_days'  => '事前予約日数',
        'max_days_per_unit'     => '1ユニットあたりの最大日数',
        'help_type'             => 'ヘルプタイプ',
        'subject'               => '件名',
        'delivery_type_id'      => '配達タイプ',
        'security_pin'          => 'セキュリティPIN',
        'guard_duty_status'     => 'ガードの勤務状態',
        'visitor_id'            => '訪問者',
        'guard_id'              => 'ガード',
        'resident_id'           => '住民',
        'monthplan'             => '月プラン',
        'camera'                => 'カメラ',
        'lacated_at'            => '位置',
        'expire_date'           => '有効期限',
        'poll_type'             => '投票タイプ',
        'notify_user'           => 'ユーザーに通知',
        'service_category_id'   => 'サービスカテゴリー',
    ],
];