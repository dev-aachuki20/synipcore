<?php

return [

    'crud' => [
        'add_record'    => 'Successfully Added !',
        'update_record' => 'Successfully Updated !',
        'delete_record' => 'This record has been succesfully deleted!',
        'restore_record' => 'This record has been succesfully Restored!',
        'merge_record'  => 'This record has been succesfully Merged!',
        'approve_record' => 'Record Successfully Approved !',
        'status_update' => 'Status successfully updated!',
    ],

    'unable_to_add_blank_field' => 'Sorry, Unable to add a blank field in',
    'data_already_exists' => 'Sorry, You cannot create new with the same name so use existing.',

    'areYouSure' => 'Are you sure you want to delete this record?',
    'areYouSureapprove' => 'Are you sure you want to Approve this record?',
    'areYouSurerestore' => 'Are you sure you want to Restore this Database? It will delete your current database.',
    'deletetitle' => 'Delete Confirmation',
    'restoretitle' => 'Restore Confirmation',
    'approvaltitle' => 'Approval Confirmation',
    'areYouSureRestore' => 'Are you sure you want to restore this record?',
    'error_message'   => 'Something went wrong....please try again later!',
    'no_record_found' => 'No Records Found!',
    'suspened' => "Your account has been suspened!",
    'unverified' => "Your account has been not verified!",
    'invalid_email' => 'Invalid Email',
    'invalid_otp' => 'Invalid OTP',
    'invalid_pin' => 'Invalid PIN',
    'wrong_credentials' => 'These credentials do not match our records!',
    'not_activate' => 'Your account is not activated.',
    'otp_sent_email' => 'We have successfully sent OTP on your Registered Email',
    'otp_sent_number' => "OTP has been sent successfully on user's mobile number",
    'expire_otp' => 'OTP has been Expired',
    'verified_otp' => 'OTP successfully Verified.',
    'invalid_token_email' => 'Invalid Token or Email!',
    'success' => 'Success',
    'register_success' => 'Your account created successfully!',
    'login_success' => 'You have logged in successfully!',
    'logout_success' => 'Logged out successfully!',
    'warning_select_record' => 'Please select at least one record',
    'required_role' => "User with the specified email doesn't have the required role.",

    'invalid_token'                 => 'Your access token has been expired. Please login again.',
    'not_authorized'                => 'Not Authorized to access this resource/api',
    'not_found'                     => 'Not Found!',
    'endpoint_not_found'            => 'Endpoint not found',
    'resource_not_found'            => 'Resource not found',
    'token_invalid'                 => 'Token is invalid',
    'unexpected'                    => 'Unexpected Exception. Try later',

    'data_retrieved_successfully'   => 'Data retrieved successfully',
    'record_retrieved_successfully' => 'Record retrieved successfully',
    'record_created_successfully'   => 'Record created successfully',
    'record_updated_successfully'   => 'Record updated successfully',
    'record_deleted_successfully'   => 'Record deleted successfully',
    'password_updated_successfully' => 'Password updated successfully',

    'profile_updated_successfully'  => 'Profile updated successfully',
    'language_updated_successfully'  => 'Language updated successfully',
    'account_deactivate'            => 'Your account has been deactivated. Please contact the admin.',
    'user_account_deactivate'       => 'Your account has been deactivated.',
    'minimum_value'                 => 'The value must be at least 1',

    'contact' => [
        'store' => [
            'success' => "Your message has been sent successfully, We will get back to you as soon as possible"
        ]
    ],

    'rating' => [
        'store' => [
            'success' => "Your feedback has been submitted successfully. We appreciate your time and effort in helping us improve our service."
        ]
    ],

    'created_successfully'   => 'Created successfully',
    'updated_successfully'   => 'Updated successfully',
    'deleted_successfully'   => 'Deleted successfully',
    'status_update_successfully' => 'Status successfully updated!',

    'location' => [
        'slug' => 'The slug already exists. Try a different title or use a custom slug.',
    ],

    'service' => [
        'slug' => 'The slug already exists. Try a different title or use a custom slug.',
    ],

    'society' => [
        'not_verified' => 'For User Society Details is not uploaded'
    ],

    'complaint' => [
        'store_success' => 'Your complaint has been submitted successfully',
        'mark_resolved' => 'Your complaint has been mark as resolved successfully',
        'comment_send' => 'Your comment sent successfully',
    ],

    'support' => [
        'submit_success' => 'Your support request has been successfully submitted.'
    ],
    'payment_request' => [
        'sucess_payment' => 'Payment submitted successfully.'
    ],

    'amenity' => [
        'advance_booking_error' => 'Amenity booking must be booked before :days days before',
        'already_booked' => 'Amenity already booked for selected time slot',
        'booked_success' => 'Amenity booked successfully',
        'delete_error_booking_exist' => 'This amenity cannot be deleted because it has been booked.'
    ],

    'post' => [
        'slug' => 'The slug already exists. Try a different title or use a custom slug.',
        'store_success' => 'Your post has been created successfully',
        'update_success' => 'Your post has been updated successfully',
        'delete_success' => 'Your post has been deleted successfully',
        'save_post' => 'Post saved successfully',
        'unsave_post' => 'Post unsaved successfully',
        'comment_send' => 'Your comment sent successfully',
    ],

    'daily_help' => [
        'create_success' => 'Daily help created successfully',
        'update_success' => 'Daily help updated successfully',
        'delete_success' => 'Daily help deleted successfully',
    ],

    'resident_vehicle' => [
        'create_success' => 'Vehicle created successfully',
        'update_success' => 'Vehicle updated successfully',
        'delete_success' => 'Vehicle deleted successfully',
    ],

    'frequest_entry' => [
        'create_success' => 'Frequest Entry created successfully',
        'update_success' => 'Frequest Entry updated successfully',
        'delete_success' => 'Frequest Entry deleted successfully',
    ],

    'family_member' => [
        'create_success' => 'Family Member created successfully',
        'update_success' => 'Family Member updated successfully',
        'delete_success' => 'Family Member deleted successfully',
    ],
    'resident_security_alert' => [
        'create_success' => 'Security Alert Contact created successfully',
        'update_success' => 'Security Alert Contact updated successfully',
        'delete_success' => 'Security Alert Contact deleted successfully',
    ],

    'visitor_mark_in_out' => 'Visitor status changed',
    'allow_visitor_by_guard' => 'Visitor allowed successfully',
    'recent_message' => 'Recent message list',
    'society_not_found' => 'Society not found',

    'message_to_guard_success' => 'Message send to guard successfully',
    'message_to_admin_success' => 'Message send to Admin successfully',

    'notification' => [
        'not_found'                 => 'Notification not found',
        'mark_as_read'              => 'Notification marked as read',
        'no_notification'           => 'No notifications to clear!',
        'clear_notification'        => 'All notifications have been cleared',
        'delete'                    => 'Notification has been deleted successfully!',
        'threat_alert_success'      => 'Security alert send successfully',
        'visitor_status_success'    => 'Visitor status updated successfully',
        'visitor_status_error_already_updated'    => 'The visitor request has already been processed. No further action is needed',
        'mark_all_read'              => 'All notification marked as read',

    ],


    // Notification Messages
    'notification_messages'  => [
        'visitor_on_gate_no_preapproved' => ':visitor_type is waiting at Gate',

        // Security Alerts
        'security_alert' => [
            'fire_alert'       => 'Fire Alert : A fire has been reported in society',
            'lift_alert'       => 'Lift Stuck : A person is currently stuck in the lift.',
            'animal_alert'     => 'Animal Alert : A stray animal has been spotted',
            'visitor_alert'    => 'Visitor Alert : An unidentified visitor has been reported',
        ],

        // 
        'visitor_status_by_resident' => 'Visitor has been :status.',

        'post' => [
            'new_post' => 'New <strong>post</strong> created',
            'post_updated' => '<strong>Post</strong> updated',

            // notification message
            'create_post_title' => 'New post',
            'create_post_message' => ':user_name has uploaded a new <strong>post</strong>',

            'comment_post_title' => "<strong>Commented</strong> on your <strong>post</strong>",
            'comment_post_message' => ":user_name has added a new <strong>Comment</strong> on your <strong>post</strong>.",

            'like_post_title' => "<strong>Like</strong> on <strong>post</strong>",
            'like_post_message' => ":user_name has <strong>liked</strong> your <strong>post.</strong>",
        ],

        'notice_board' => [
            'new_announcement' => 'Posted <strong>:announcement_type</strong> in notice board',
            'announcement_updated' => 'Updated <strong>:announcement_type</strong> in notice board',

            'comment_notification' => ":user_name has commented on the announcement titled :announcement_title",
            'like_notification' => ":user_name has :type the announcement titled :announcement_title",
            'vote_poll' => "<strong>:user_name</strong> has voted on the <strong>poll</strong> titled :announcement_title",
        ],

        'conversation' => [
            'new_message_title' => "New <strong>message</strong> received",
            'new_message_message' => "You have a <strong>new message</strong> from <strong>:user_name</strong>",
        ],

        'payment_request' => [
            'request' => 'Requested for payment',
        ],

        'complaint' => [
            'status_message'  => 'Your <strong>complaint</strong> is :status',
            'raise_complaint'   => "A new <strong>complaint</strong> has been raised by :user_name. Please review the details and take necessary action.",
            'resolve_complaint' => "The <strong>complaint</strong> raised by :user_name has been marked as resolved",

            'comment_by_other_complaint_title' => "<strong>Commented</strong> on <strong>complaint</strong>",
            'comment_by_other_complaint' => ":user_name has added a new <strong>comment</strong> on your <strong>complaint</strong>.",
        ],

        'amenity_booking' => [
            'status_message'  => 'Your <strong>amenity booking</strong> is <strong>:status</strong>',

            'booking_notification' => ":user_name has requested to book :amenity_name on :requested_date.",
        ],

        'service_booking' => [
            'status_message'  => 'Your service booking is :status',
        ],

        'visitor_log' => [
            'status_message'  => 'Visitor is :status',
        ],

        'ai_box' => [
            'fall_detection' => [
                'title' => "Fall Detected!",
                'message' => "A fall has been detected by the camera located at :camera_location in :society_name. Please review immediately for any emergency assistance.",
                'success' => 'Message sent successfully.',
                'socieity_error' => 'The camera is not have assign to a society',
            ]
        ],

        'service' => [
            'book_by_user' => ":user_name has <strong>booked</strong> the <strong>:service_name.</strong>"
        ],

        'message_to_guard_admin' => [
            'title' => "<strong>New Message</strong> from :user_name",
            'message' => "You have received a <strong>new message</strong> from <strong>:user_name.</strong>",
        ]
    ],

    'no_resident_found_in_unit' => "Their is not resident found in selected unit",

    'announcement' => [
        'error_expire_date' => 'This poll has expired. You cannot vote anymore.',
        'id_not_exist' => 'Sorry!! Id not exist.'
    ]

];
