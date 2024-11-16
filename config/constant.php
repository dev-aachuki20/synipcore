<?php

return [
    'default' => [
        'logo'      => 'default/logo.png',
        'splash_logo' => 'default/splash-logo.png',
        'darklogo'    => 'default/light-logo.png',
        'favicon'   => 'default/logo.png',
        'no_image'  => 'default/no-image.jpg',
        'user_icon' => 'default/user-icon.svg',
        'page_loader' => 'default/page-loader.gif',
        'terms_condition_pdf' => 'default/terms-and-conditions.pdf',
        'privacy_pdf' => 'default/privacy-policy.pdf',
    ],

    'support' => [
        'name' => "Super Admin",
        'email' => "support@gmail.com",
    ],

    'profile_max_size' => 2048,
    'profile_max_size_in_mb' => '2MB',

    'video_max_size' => 102400,
    'video_max_size_in_mb' => '2MB',

    'roles' => [
        'superadmin'    => 1,
        'admin'         => 2,
        'customer'      => 3,
        'provider'      => 4,
        'guard'         => 5,
        'resident'      => 6,
    ],

    'date_format' => [
        'date' => 'd M Y',
        'time' => 'h:i a',
        'date_time' => 'd M Y, h:i A',
    ],

    'flatpicker_date_format' => [
        'date' => 'd M Y',
        'time' => 'h:i K',
        'date_time' => 'd M Y, h:i K',
    ],

    'search_date_format' => [ //$whereFormat = '%m/%d/%Y %h:%i %p';
        'date' => '%d %b %Y',
        'time' => '%h:%i %p',
        'date_time' => '%d %b %Y, %h:%i %p'
    ],

    'total_employees' => [
        '2 - 9',
        '10 - 49',
        '50 - 99',
        '100 - 499',
        '+500',
    ],

    'founding_year' => [],

    'status' => [
        0 => "Inactive",
        1 => "Active"

    ],

    'status_type' => [
        'service_status' => [
            'accept'    => 'Accept',
            'reject'    => 'Reject',
            'pending'   => 'Pending',
        ],

        'post_status' => [
            'publish'       => 'Publish',
            'unpublish'     => 'Unpublish',
            'draft'         => 'Draft',
        ],

        'complaint_status' => [
            'resolved'      => 'Resolved',
            'pending'       => 'Pending',
            'in_progress'   => 'In progress',
            'rejected'      => 'Rejected',
        ],

        'comment_status' => [
            0       => 'Deny',
            1       => 'Approved',
        ],

        'payment_method_status' => [
            1       => 'Enabled',
            0       => 'Disabled',
        ],

        'payment_method_type' => [
            1       => 'Postpaid',
            2       => 'Prepaid',
        ],
        'vehicle_status' => [
            'pending'       => 'Pending',
            'approved'      => 'Approved',
            'rejected'      => 'Rejected',
        ],

        'delivery_status' => [
            'new'       => 'New',
            'processing' => 'Processing',
            'delivered'  => 'Delivered',
        ],
        'guard_duty_status' => [
            1 => 'Active',
            0 => 'Inactive',
        ],

        'amenity_booking_status' => [
            'pending'       => 'Pending',
            'approved'      => 'Approved',
            'rejected'      => 'Rejected',
        ],

        'visitor_status' => [
            'pending'       => 'Waiting',
            'approved'      => 'Pre Approved',
            'rejected'      => 'Deny',
            'in'            => 'In',
            'out'           => 'Out',
        ],

        'service_category_status' => [
            'active' => "Active",
            'inactive' => "Inactive",
        ],


    ],

    /* Location Meta Fields */
    'location_meta_keys' => [
        'key'   => 'Scope',
        'value' => [
            1 => 'Default',
            2 => 'Country',
            3 => 'City',
            4 => 'District',
        ],
    ],

    'annuncement_types' => [
        1   => 'Announcement',
        2   => 'Poll',
        3   => 'Event',
        4   => 'AI Box',
    ],

    'poll_type' => [
        'single' => 'Single',
        'multiple' => 'Multiple',
    ],

    'resident_types' => [
        1 => 'Owner',
        2 => 'Tenant',
        3 => 'Owner Family',
        4 => 'Tenant Family',
    ],

    'complaint_categories' => ['personal', 'community'],

    'location_scope' => [
        1 => 'Default',
        2 => 'Country',
        3 => 'City',
        4 => 'District',
    ],

    'visitor_types' => [
        'guest' => 'Guest',
        'cab' => 'Cab',
        'delivery_man' => 'Delivery man',
        'service_man' => 'Service man'
    ],

    'payment_request_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed'
    ],

    'post_image_max_file_count' => 5,

    'durations' => [
        'once_a_week' => 'Once a week',
        'twice_a_week' => 'Twice a week',
        'monthly' => 'Monthly',
        'every_quarter' => 'Every quarter',
        'yearly' => 'Yearly',
        'twice_a_year' => 'Twice a year',
        'every_2_years' => 'Every 2 years',
        'every_3_years' => 'Every 3 years',
    ],

    'api_page_limit' => [
        'post' => 20,
        'comment' => 50,
        'message' => 50,
        'conversation' => 20,
        'guard_messages' => 50,
        'admin_messages' => 50,
        'announcement' => 50,
    ],

    'month' => [
        'january' => 'January',
        'february' => 'February',
        'march' => 'March',
        'april' => 'April',
        'may' => 'May',
        'june' => 'June',
        'july' => 'July',
        'august' => 'August',
        'september' => 'September',
        'october' => 'October',
        'november' => 'November',
        'december' => 'December',
    ],

    'week' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],

    'days' => [
        '1' => 'Day 1',
        '2' => 'Day 2',
        '3' => 'Day 3',
        '4' => 'Day 4',
        '5' => 'Day 5',
        '6' => 'Day 6',
        '7' => 'Day 7',
        '8' => 'Day 8',
        '9' => 'Day 9',
        '11' => 'Day 11',
        '12' => 'Day 12',
        '13' => 'Day 13',
        '14' => 'Day 14',
        '15' => 'Day 15',
        '16' => 'Day 16',
        '17' => 'Day 17',
        '18' => 'Day 18',
        '19' => 'Day 19',
        '20' => 'Day 20',
        '21' => 'Day 21',
        '22' => 'Day 22',
        '23' => 'Day 23',
        '24' => 'Day 24',
        '25' => 'Day 25',
        '26' => 'Day 26',
        '27' => 'Day 27',
        '28' => 'Day 28',
        '29' => 'Day 29',
        '30' => 'Day 30',
        '31' => 'Day 31',
    ],

    'delivery_type' => [
        1 => 'Package',
        2 => 'Cargo',
        3 => 'Mail',
        4 => 'Task',
        5 => 'Message',
    ],

    'language_flag_image'  => [
        'en' => [
            'image' => 'default/flag/eng.png',
            'text' => 'en',
        ],
        'cn' => [
            'image' => 'default/flag/chinese.jpg',
            'text' => 'cn',
        ],
        'jp' => [
            'image' => 'default/flag/japanese.png',
            'text' => 'jp',
        ],
    ],
    'email_subjects' => [
        'support-admin' => "New Support Request Submitted"
    ],

    'user_firebase_json_file' => storage_path('app/user-firebase-auth.json'),
    'guard_firebase_json_file' => storage_path('app/guard-firebase-auth.json'),

    'alert_types' => ['fire_alert', 'lift_alert', 'animal_alert', 'visitor_alert', 'other_alert'],

    'post_types' => [
        'text' => "Text",
        'image' => "Image",
        'video' => "Video",
    ],

    'notify_user' => [
        'admin'     => "Admin",
        'guard'     => "Guard",
        'resident'  => "Resident",
    ],

    'aibox_notification' => [
        'api_type' => [
            101 => 'fall_detected',
            102 => 'face_recognition',
            103 => 'transportation_recognition',
            104 => 'animal_thread_detected',
            105 => 'fire_detected',
            106 => 'ppe_detection',
        ],

        'api_code' => [
            101 => [
                'event_name' => "Fall detected",
                'event_desc' => "A sudden drop at {location_data_key} has been identified, indicating a potential fall incident requiring attention.",
            ],
            102 => [
                'event_name' => "Face recognition",
                'event_desc' => "A human face has been recognized in the {location_data_key}, triggering a response for monitoring or identification purposes.",
            ],
            103 => [
                'event_name' => "Transportation recognition",
                'event_desc' => "Movement consistent with vehicles has been detected at {location_data_key}, suggesting the presence of transportation nearby.",
            ],
            104 => [
                'event_name' => "Animal thread detected",
                'event_desc' => "An animal has been identified nearby, which may pose a risk or require caution in the area {location_data_key}.",
            ],
            105 => [
                'event_name' => "Fire detected",
                'event_desc' => "Signs of fire, such as heat or smoke, have been detected at {location_data_key}, indicating an emergency situation that needs immediate action.",
            ],
            106 => [
                'event_name' => "PPE detection",
                'event_desc' => "Potential non-compliance with safety gear at {location_data_key}. Please check and take immediate action.",
            ]

        ],
    ],

    'aibox_notification_attachemnts' => [
        'aibox_images' => [
            'fall_detected' => [
                'default/aibox/images/s-blob-v1-IMAGE-_TFd8XKh2Yo.png',
                'default/aibox/images/s-blob-v1-IMAGE-d4W3uAa9rNc.png',
                'default/aibox/images/s-blob-v1-IMAGE-FDv6jjWG48o.jfif',
            ],
            'face_recognition' => [
                'default/aibox/images/s-blob-v1-IMAGE-9wWYhcpwWks.png',
                'default/aibox/images/s-blob-v1-IMAGE-QXPki-AWbas.png',
                'default/aibox/images/s-blob-v1-IMAGE-CnrZGgfT5xk.png',
            ],
            'transportation_recognition' => [
                'default/aibox/images/s-blob-v1-IMAGE-tsNLP8IUO6c.png',
                'default/aibox/images/s-blob-v1-IMAGE-dK5qvRAWqUc.png',
                'default/aibox/images/s-blob-v1-IMAGE-uGjldGlsbAM.jfif',
            ],
            'animal_thread_detected' => [
                'default/aibox/images/s-blob-v1-IMAGE-kz7gCphIEko.png',
                'default/aibox/images/s-blob-v1-IMAGE-kYtvHpF7hUo.jfif',
            ],
            'fire_detected' => [
                'default/aibox/images/s-blob-v1-IMAGE-RRKDNYEpJm8.jfif',
            ],
            'ppe_detection' => [
                'default/aibox/images/s-blob-v1-IMAGE-U6RLUMVFsLE.png',
                'default/aibox/images/s-blob-v1-IMAGE-mHNuHZvkFRs.png',
            ],
        ],

        'aibox_videos' => [
            'fall_detected' => [
                "https://www.youtube.com/watch?v=_hafXu9xgrw",
            ],
            'face_recognition' => [
                "https://www.youtube.com/watch?v=EHgjYXWtaIs",
            ],
            'transportation_recognition' => [
                "https://www.youtube.com/watch?v=i0yqhHKWY0A",
            ],
            'animal_thread_detected' => [
                "https://www.youtube.com/watch?v=Yc6YuxKa8Uw",
            ],
            'fire_detected' => [
                "https://www.youtube.com/watch?v=xHcLlvGQ2uM",
            ],
            'ppe_detection' => [
                "https://www.youtube.com/watch?v=MncQncwnVWU",
            ],
        ],
    ],

    'plan_months' => [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ],

    'all_visitor_types' => [
        'guest' => 'Guest',
        'cab' => 'Cab',
        'delivery_man'  => 'Delivery man',
        'service_man'   => 'Service man',
        'family_member' => 'Family member',
        'daily_help'    => 'Daily help',
        'vehicle'       => 'Vehicle',
    ],

];
