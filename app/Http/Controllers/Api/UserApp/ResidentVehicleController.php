<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;

use App\Http\Controllers\Controller;
use App\Models\ResidentVehicle;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResidentVehicleController extends APIController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $vehicles = $user->vehicles()->select('id', 'vehicle_number', 'vehicle_model', 'vehicle_color', 'created_at')
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
                        'gatepass_type'     => 'vehicle',
                        'created_at'        => $residentVehicle->created_at->format(config('constant.date_format.date_time')),
                    ];
                });
            return $this->respondOk([
                'status'   => true,
                'data'   => $vehicles
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_number'    => ['required', 'string', 'max:255', 'unique:resident_vehicles,vehicle_number,NULL,id,deleted_at,NULL'],
            'vehicle_model'     => ['required', 'string', 'max:255'],
            'vehicle_color'     => ['required', 'string', 'max:255'],
            'vehicle_image'     => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
            'gatepass_code'     => ['required'],
            'gatepass_qr_image' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $input = $request->only('vehicle_number', 'vehicle_model', 'vehicle_color', 'gatepass_code');

            $input['resident_id'] = $user->id;
            $input['created_by'] = $user->id;
            $input['updated_by'] = $user->id;

            $input['society_id'] = $user->society ? $user->society->id : null;
            $input['building_id'] = $user->building ? $user->building->id : null;
            $input['unit_id'] = $user->unit ? $user->unit->id : null;

            $residentVehicle = ResidentVehicle::create($input);

            // upload Vehicle image
            if ($residentVehicle && $request->has('vehicle_image')) {
                uploadImage($residentVehicle, $request->vehicle_image,  'vehicles/vehicle-image', "resident_vehicle_image", 'original', 'save', null);
            }

            // upload Gtepass QR image
            if ($residentVehicle && $request->has('gatepass_qr_image')) {
                uploadImage($residentVehicle, $request->gatepass_qr_image,  'vehicles/gatepass-qr', "vehicle_gatepass_qr", 'original', 'save', null);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.resident_vehicle.create_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function edit($id)
    {
        try {
            $residentVehicle = ResidentVehicle::whereId($id)->select('id', 'vehicle_number', 'vehicle_model', 'vehicle_color')->first();
            $residentVehicle['vehicle_image_url'] = $residentVehicle->vehicle_image_url;
            $residentVehicle['gatepass_qr_image'] = $residentVehicle->gatepass_qr_image;

            $residentVehicle = collect($residentVehicle)->except(['vehicle_image', 'vehicle_qr'])->all();
            return $this->respondOk([
                'status'   => true,
                'data'   => $residentVehicle
            ]);
        } catch (\Exception $e) {

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }

    public function Update(Request $request, $id)
    {
        $request->validate([
            'vehicle_number'    => ['required', 'string', 'max:255', 'unique:resident_vehicles,vehicle_number,' . $id . ',id,deleted_at,NULL'],
            'vehicle_model'     => ['required', 'string', 'max:255'],
            'vehicle_color'     => ['required', 'string', 'max:255'],
            'vehicle_image'     => ['nullable', 'image', 'max:' . config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ]);

        DB::beginTransaction();
        try {
            $residentVehicle = ResidentVehicle::find($id);
            $user = Auth::user();
            if (!$residentVehicle) {
                return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
            }

            $input = $request->only('vehicle_number', 'vehicle_model', 'vehicle_color');
            $input['updated_by'] = $user->id;

            $residentVehicle->update($input);

            if ($request->has('vehicle_image')) {
                $uploadId = null;
                $actionType = 'save';
                if ($profileImageRecord = $residentVehicle->vehicleImage) {
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($residentVehicle, $request->vehicle_image, 'vehicles/vehicle-image', "resident_vehicle_image", 'original', $actionType, $uploadId);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.resident_vehicle.update_success')
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
            $residentVehicle = ResidentVehicle::find($id);
            if ($residentVehicle) {
                if ($residentVehicle->vehicleImage) {
                    deleteFile($residentVehicle->vehicleImage->id);
                }
                if ($residentVehicle->vehicleQr) {
                    deleteFile($residentVehicle->vehicleQr->id);
                }
                $residentVehicle->delete();
            }

            DB::commit();
            return response()->json([
                'success'    => true,
                'message'    => trans('messages.resident_vehicle.delete_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400);
        }
    }
}
