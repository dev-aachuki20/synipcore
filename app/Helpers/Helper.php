<?php

use App\Models\AmenityBooking;
use App\Models\Comment;
use App\Models\Setting;
use App\Models\Uploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as Str;
use App\Models\MetaField;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

if (!function_exists('getCommonValidationRuleMsgs')) {
	function getCommonValidationRuleMsgs()
	{
		return [
			'currentpassword.required' => 'The current password is required.',
			'password.required' => 'The new password is required.',
			'password.min' => 'The new password must be at least 8 characters',
			'password.different' => 'The new password and current password must be different.',
			'password.confirmed' => 'The password confirmation does not match.',
			'password_confirmation.required' => 'The new password confirmation is required.',
			'password_confirmation.min' => 'The new password confirmation must be at least 8 characters',
			'email.required' => 'Please enter email address.',
			'email.email' => 'Email is not valid. Enter email address for example test@gmail.com',
			'email.exists' => "Please Enter Valid Registered Email!",
			'password_confirmation.same' => 'The confirm password and new password must match.',

			'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
			'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
		];
	}
}

if (!function_exists('generateRandomString')) {
	function generateRandomString($length = 20)
	{
		$randomString = Str::random($length);
		return $randomString;
	}
}

if (!function_exists('getWithDateTimezone')) {
	function getWithDateTimezone($date)
	{
		$newdate = Carbon::parse($date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i:s');
		return $newdate;
	}
}

if (!function_exists('uploadImage')) {
	/**
	 * Upload Image.
	 *
	 * @param array $input
	 *
	 * @return array $input
	 */
	function uploadImage($directory, $file, $folder, $type = "profile", $fileType = "jpg", $actionType = "save", $uploadId = null, $orientation = null)
	{
		$oldFile = null;
		if ($actionType == "save") {
			$upload               		= new Uploads;
		} else {
			$upload               		= Uploads::find($uploadId);
			$oldFile = $upload->file_path;
		}
		$upload->file_path      	= $file->store($folder, 'public');
		$upload->extension      	= $file->getClientOriginalExtension();
		$upload->original_file_name = $file->getClientOriginalName();
		$upload->media_type 				= $type;
		$upload->file_type 			= $fileType;
		$upload->orientation 		= $orientation;
		$response             		= $directory->uploads()->save($upload);
		// delete old file
		if ($oldFile) {
			Storage::disk('public')->delete($oldFile);
		}

		return $upload;
	}
}


/* store meta key value */
if (!function_exists('metakeyField')) {
	function metakeyField($keys, $values, $data, $type = 'save')
	{
		$store = null;
		if (count($keys) === count($values)) {
			$metaFields = array_map(function ($key, $value) use ($data) {
				return new MetaField([
					'key'           => $key,
					'value'         => $value,
					'metaable_id'   => $data->id,
					'metaable_type' => get_class($data),
				]);
			}, $keys, $values);

			if ($type == 'update') {
				$data->metafields()->delete();
			}
			$store = $data->metafields()->saveMany($metaFields);
		}
		return $store;
	}
}

/* Create */
if (!function_exists('convertTitleToSlug')) {
	function convertTitleToSlug($str)
	{
		$str = strtolower($str);
		$str = str_replace(' ', '-', $str);
		$str = preg_replace('/[^a-z0-9\-]/', '', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');

		return $str;
	}
}

if (!function_exists('deleteFile')) {
	/**
	 * Destroy Old Image.	 *
	 * @param int $id
	 */
	function deleteFile($upload_id)
	{
		$upload = Uploads::find($upload_id);
		Storage::disk('public')->delete($upload->file_path);
		$upload->delete();
		return true;
	}
}


if (!function_exists('getSetting')) {
	function getSetting($key)
	{
		$result = null;
		$setting = Setting::where('key', $key)->whereStatus(1)->first();
		if ($setting->setting_type == 'image') {
			$result = $setting->image_url;
		} elseif ($setting->setting_type == 'file') {
			$result = $setting->doc_url;
		} elseif ($setting->setting_type == 'json') {
			$result = $setting->value ? json_decode($setting->value, true) : null;
		} else {
			$result = $setting->value;
		}
		return $result;
	}
}


if (!function_exists('str_limit_custom')) {
	/**
	 * Limit the number of characters in a string.
	 *
	 * @param  string  $value
	 * @param  int  $limit
	 * @param  string  $end
	 * @return string
	 */
	function str_limit_custom($value, $limit = 100, $end = '...')
	{
		return \Illuminate\Support\Str::limit($value, $limit, $end);
	}
}

if (!function_exists('getSvgIcon')) {
	function getSvgIcon($icon)
	{
		return view('components.svg-icons', ['icon' => $icon])->render();
	}
}

if (!function_exists('dateFormat')) {
	function dateFormat($date, $format = '')
	{
		$startDate = Carbon::parse($date);
		$formattedDate = $startDate->format($format);
		return $formattedDate;
	}
}

if (!function_exists('generateSlug')) {

	function generateSlug($name, $tableName, $ignoreId = null)
	{
		// Convert the name to a slug
		$slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));

		// Ensure no multiple hyphens
		$slug = preg_replace('/-+/', '-', $slug);

		// Trim hyphens from both ends
		$slug = trim($slug, '-');

		// Ensure the slug is unique
		$originalSlug = $slug;
		$count = 1;

		$query = DB::table($tableName)->where('slug', $slug)->whereNull('deleted_at');

		// Ignore the current record if updating
		if ($ignoreId) {
			$query->where('id', '!=', $ignoreId);
		}

		while ($query->exists()) {
			$slug = "{$originalSlug}-{$count}";
			$count++;

			// Update the query to check for the new slug
			$query = DB::table($tableName)->where('slug', $slug)->whereNull('deleted_at');
			if ($ignoreId) {
				$query->where('id', '!=', $ignoreId);
			}
		}

		return $slug;
	}
}

if (!function_exists('uploadComment')) {
	function uploadComment($model, $userId, $commentText, $isApprove = 1)
	{
		$comment = new Comment;

		$comment->user_id = $userId;
		$comment->comment = $commentText;
		$comment->is_approve = $isApprove;

		$response = $model->comments()->save($comment);

		return $response;
	}
}


if (!function_exists('updateLikeDislike')) {
	function updateLikeDislike($model, $userId, $type)
	{
		$reaction = $model->reactions()->where('user_id', $userId)->first();

		if ($reaction && $reaction->reaction_type == $type) {
			$reaction->delete();
		} else if ($reaction && $reaction->reaction_type != $type) {
			$reaction->update(['reaction_type' => $type]);
		} else {
			$reaction = new Reaction();
			$reaction->user_id = $userId;
			$reaction->reaction_type = $type;

			$model->reactions()->save($reaction);
		}
		return $reaction;
	}
}

if (!function_exists('isAmenityAvailable')) {
	function isAmenityExists($amenityId, $fromDate, $fromTime, $toDate, $toTime)
	{
		$fromDateTime 	= Carbon::parse("$fromDate $fromTime");
		$toDateTime 	= Carbon::parse("$toDate $toTime");

		// Check for overlapping bookings
		$existingBooking = AmenityBooking::whereIn('status', ['pending', 'approved'])
			->where('amenity_id', $amenityId)
			->where(function ($query) use ($fromDateTime, $toDateTime) {
				$query->where(function ($q) use ($fromDateTime, $toDateTime) {
					$q->whereDate('from_date', '<=', $toDateTime->format('Y-m-d'))
						->whereDate('to_date', '>=', $fromDateTime->format('Y-m-d'))
						->whereTime('from_time', '<', $toDateTime->format('H:i:s'))
						->whereTime('to_time', '>', $fromDateTime->format('H:i:s'));
				});
			})
			->exists();

		return $existingBooking;
	}
}


if (!function_exists('getUsersDailyHelps')) {
	function getUsersDailyHelps($user = '')
	{
		$user = $user ? $user : auth()->user();
		$dailyHelps = $user->dailyhelps()->select('id', 'name', 'phone_number', 'help_type')
			->latest()
			->get()
			->map(function ($dailyHelp) {
				return [
					'id' => $dailyHelp->id,
					'name' => $dailyHelp->name,
					'phone_number' => $dailyHelp->phone_number,
					'help_type' => $dailyHelp->help_type,
					'profile_image_url' => $dailyHelp->profile_image_url,
					'gatepass_qr_image' => $dailyHelp->gatepass_qr_image,
				];
			});

		return $dailyHelps;
	}
}

if (!function_exists('getUsersVehicles')) {
	function getUsersVehicles($user = '')
	{
		$user = $user ? $user : auth()->user();
		$vehicles = $user->vehicles()->select('id', 'vehicle_number', 'vehicle_model', 'vehicle_color')
			->latest()
			->get()
			->map(function ($residentVehicle) {
				return [
					'id' => $residentVehicle->id,
					'vehicle_number' => $residentVehicle->vehicle_number,
					'vehicle_model' => $residentVehicle->vehicle_model,
					'vehicle_color' => $residentVehicle->vehicle_color,
					'vehicle_image_url' => $residentVehicle->vehicle_image_url,
					'gatepass_qr_image' => $residentVehicle->gatepass_qr_image,
				];
			});

		return $vehicles;
	}
}

if (!function_exists('getUsersFamilyMembers')) {
	function getUsersFamilyMembers($user = '')
	{
		$user = $user ? $user : auth()->user();
		$familymembers = $user->familymembers()->select('id', 'name', 'phone_number', 'relation')
			->latest()
			->get()
			->map(function ($familymember) {
				return [
					'id'                => $familymember->id,
					'name'              => $familymember->name,
					'phone_number'      => $familymember->phone_number,
					'relation'          => $familymember->relation,
					'profile_image_url' => $familymember->profile_image_url,
					'gatepass_qr_image' => $familymember->gatepass_qr_image,
				];
			});

		return $familymembers;
	}
}

if (!function_exists('getUsersFrequestEntries')) {
	function getUsersFrequestEntries($user = '')
	{
		$user = $user ? $user : auth()->user();
		$frequestEntries = $user->frequestEntries()->select('id', 'name', 'phone_number', 'task')
			->latest()
			->get()
			->map(function ($frequestEntry) {
				return [
					'id' => $frequestEntry->id,
					'name' => $frequestEntry->name,
					'phone_number' => $frequestEntry->phone_number,
					'task' => $frequestEntry->task,
					'profile_image_url' => $frequestEntry->profile_image_url,
					'gatepass_qr_image' => $frequestEntry->gatepass_qr_image,
				];
			});

		return $frequestEntries;
	}
}

if (!function_exists('SendPushNotification')) {
	function SendPushNotification($userIds, $title, $message, $notificationType='user' , $visitorDetails=[])
	{
		$message = strip_tags($message);
		$fcmTokens = [];

		if($notificationType == 'user'){
			$configJsonFile = config('constant.user_firebase_json_file');
		} else {
			$configJsonFile = config('constant.guard_firebase_json_file');
		}
		$firebase = (new Factory)->withServiceAccount($configJsonFile);
		$messaging = $firebase->createMessaging();


		// Define the FCM tokens you want to send the message to
		$fcmTokens = User::whereIn('id', $userIds)->whereNotNull('device_token')->pluck('device_token')->toArray();

		// Create the notification
		$notification = Notification::create()
			->withTitle($title)
			->withBody($message);

		// Create the message
		$messageData = CloudMessage::new()->withNotification($notification)->withData($visitorDetails); // Optional: Add custom data

		// Send the message to the FCM tokens
		try {
			$messaging->sendMulticast($messageData, $fcmTokens);
		} catch (\Kreait\Firebase\Exception\MessagingException $e) {
			// Log::info('Error sending firebase message:', $e->getMessage());
		} catch (\Kreait\Firebase\Exception\FirebaseException $e) {
			// Log::info('Firebase error:', $e->getMessage());
		}
	}
}

if (!function_exists('getUserForNotification')) {
	function getUserForNotification($roles, $society=null, $isSA=false, $exceptUserIds=null)
	{
		$arrRoles = [];
		$allRoles = config('constant.roles');
		foreach($roles as $role){
			$arrRoles[] = $allRoles[$role];
		}

		if($society){
			$societyUsers = User::whereStatus(1)->select('id', 'email')
			->where(function($query) use($society, $arrRoles){
				$query->where('society_id', $society->id)->whereHas('roles', function($q) use($arrRoles){
					$q->whereIN('id', $arrRoles);
				});
			});
			
			if($isSA){
				$societyUsers = $societyUsers->orWhere(function($qq){
					$qq->whereHas('roles', function($qry){
						$qry->where('id', config('constant.roles.superadmin'));
					});
				});
			}

			if(is_array($exceptUserIds)){
				$societyUsers = $societyUsers->whereNotIn('id', $exceptUserIds);
			} else if($exceptUserIds) {
				$societyUsers = $societyUsers->where('id', '<>', $exceptUserIds);
			}
			$societyUsers = $societyUsers->get();

			return $societyUsers;
		}
	}
}
