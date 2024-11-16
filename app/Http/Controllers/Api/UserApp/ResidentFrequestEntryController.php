<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;

use App\Models\ResidentFrequestEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResidentFrequestEntryController extends APIController
{
    public function index (){
        try {
            $user = Auth::user();
            $frequestEntries = $user->frequestEntries()->select('id','name','phone_number','task')
            ->latest()
            ->get()
            ->map(function($frequestEntry){
                return [
                    'id' => $frequestEntry->id,
                    'name' => $frequestEntry->name,
                    'phone_number' => $frequestEntry->phone_number,
                    'task' => $frequestEntry->task,
                    'profile_image_url' => $frequestEntry->profile_image_url,
                    'gatepass_qr_image' => $frequestEntry->gatepass_qr_image,
                    'gatepass_type'     => 'frequest_entry',
                ];
            });
            return $this->respondOk([
                'status'   => true,
                'data'   => $frequestEntries
            ]);
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store (Request $request){
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_frequest_entries,phone_number,NULL,id,deleted_at,NULL'],
            'task'          => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:'.config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'gatepass_code' => ['required'],
            'gatepass_qr_image' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            $input = $request->only('name','phone_number','task', 'gatepass_code');

            $input['resident_id'] = $user->id;
            $frequestEntry = ResidentFrequestEntry::create($input);

            // upload profile image
            if($frequestEntry && $request->has('profile_image')){
                uploadImage($frequestEntry, $request->profile_image,  'frequest-entries/profile-image',"frequest_entry_profile_image", 'original', 'save', null);
            }

            // upload Gtepass QR image
            if($frequestEntry && $request->has('gatepass_qr_image')){
                uploadImage($frequestEntry, $request->gatepass_qr_image,  'frequest-entries/gatepass-qr',"frequest_entry_qr", 'original', 'save', null);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.frequest_entry.create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit ($id){
        try {
            $frequestEntry = ResidentFrequestEntry::whereId($id)->select('id','name','phone_number','task')->first();
            $frequestEntry['profile_image_url'] = $frequestEntry->profile_image_url;
            $frequestEntry['gatepass_qr_image'] = $frequestEntry->gatepass_qr_image;

            $frequestEntry = collect($frequestEntry)->except(['profile_image', 'frequest_entry_qr'])->all();
            return $this->respondOk([
                'status'   => true,
                'data'   => $frequestEntry
            ]);
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function Update (Request $request, $id){
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_frequest_entries,phone_number,'.$id.',id,deleted_at,NULL'],
            'task'          => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:'.config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ]);
        
        DB::beginTransaction();
        try {
            $frequestEntry = ResidentFrequestEntry::find($id);
            $user = Auth::user();
            if(!$frequestEntry){
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }
            
            $input = $request->only('name','phone_number','task');

            $frequestEntry->update($input);

            if($request->has('profile_image')){
                $uploadId = null;
                $actionType = 'save';
                if($profileImageRecord = $frequestEntry->profileImage){
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($frequestEntry, $request->profile_image, 'frequest-entries/profile-image',"frequest_entry_profile_image", 'original', $actionType, $uploadId);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.frequest_entry.update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $frequestEntry = ResidentFrequestEntry::find($id);
            if ($frequestEntry) {
                if ($frequestEntry->profileImage) {         
                    deleteFile($frequestEntry->profileImage->id);
                }
                if ($frequestEntry->frequestEntryQr) {        
                    deleteFile($frequestEntry->frequestEntryQr->id);
                }
                $frequestEntry->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.frequest_entry.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
