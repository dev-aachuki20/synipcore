<?php

namespace App\Http\Controllers\Api\UserApp;

use App\Http\Controllers\Api\APIController;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorsController extends APIController
{
    public function allowVisitor(Request $request)
    {
        $request->validate([
            'type'          => ['required', 'in:' . implode(',', array_keys(config('constant.visitor_types')))],
            'name'          => ['required'],
            'phone_number'  => ['nullable', 'required_if:type,guest', 'required_if:type,service_man', 'numeric', 'digits_between:10,15'],
            'cab_number'    => ['nullable', 'required_if:type,cab', 'min:4', 'max:4'],
            'visit_date'    => ['nullable', 'required_if:type,guest', 'required_if:type,service_man', 'required_if:type,delivery_man'],
            'visitor_note'  => ['nullable'],
            'other_info'    => ['nullable'],
        ]);

        DB::beginTransaction();
        try {
            $input = $request->all();

            if ($request->type != 'cab') {
                $bookingDate = Carbon::createFromFormat('d-m-Y H:i', $input['visit_date']);
                $input['visit_date'] = $bookingDate->format('Y-m-d H:i');
            } else {
                $input['visit_date'] = now();
            }

            if ($request->type == 'delivery_man' && $request->has('keep_package')) {
                $input['keep_package'] = 1;
            }

            $input['visitor_type'] = $request->type;

            $input['user_id'] = auth()->user()->id;

            $visitor = Visitor::create($input);

            // upload Gtepass QR image
            if ($visitor && $request->has('gatepass_qr_image')) {
                uploadImage($visitor, $request->gatepass_qr_image,  'visitors/gatepass-qr', "visitor_gatepass_qr", 'original', 'save', null);
            }

            DB::commit();
            return $this->respondOk([
                'status'   => true,
                'message'   => trans('messages.crud.add_record')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->setStatusCode(500)->respondWithError(trans('messages.error_message'));
        }
    }
}
