<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\ResidentSecurityAlert;
use App\Models\ResidentVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResidentSecurityAlertController extends APIController
{
    public function index (){
        try {
            $user = Auth::user();
            $vehicles = $user->securityAlertContacts()->select('id','name','phone_number')
            ->latest()
            ->get();
            return $this->respondOk([
                'status'   => true,
                'data'   => $vehicles
            ]);
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store (Request $request){
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_security_alerts,phone_number,NULL,id,deleted_at,NULL'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            $input = $request->only('name','phone_number');

            $input['resident_id'] = $user->id;

            ResidentSecurityAlert::create($input);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.resident_security_alert.create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit ($id){
        try {
            $residentSecurityAlert = ResidentSecurityAlert::whereId($id)->select('id','name','phone_number')->first();

            return $this->respondOk([
                'status'   => true,
                'data'   => $residentSecurityAlert
            ]);
        } catch(\Exception $e){
            
            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function Update (Request $request, $id){
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'numeric', 'digits_between:10,15', 'unique:resident_security_alerts,phone_number,'.$id.',id,deleted_at,NULL'],
        ]);
        
        DB::beginTransaction();
        try {
            $residentSecurityAlert = ResidentSecurityAlert::find($id);
            $user = Auth::user();
            if(!$residentSecurityAlert){
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }            
            $input = $request->only('name','phone_number');

            $residentSecurityAlert->update($input);

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.resident_security_alert.update_success')
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
            $residentSecurityAlert = ResidentSecurityAlert::find($id);
            if ($residentSecurityAlert) {
                $residentSecurityAlert->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.resident_security_alert.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
