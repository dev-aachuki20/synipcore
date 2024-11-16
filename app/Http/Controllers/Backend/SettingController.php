<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $settings = Setting::whereStatus(1)->whereIn('group', ['web', 'api'])->orderBy('id', 'asc')->orderBy('position', 'asc')->get();
        return view('backend.setting.index',compact('settings'));
    }

    public function update(UpdateRequest $request, Setting $setting)
    {
        $data=$request->all();
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                $setting_value = $value;
                if ($setting) {
                    if ($setting->setting_type === 'image') {
                        if ($value) {
                            $uploadId = $setting->image ? $setting->image->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'save', null);
                            }
                        }
                        $setting_value = null;
                    }
                    elseif($setting->setting_type === 'file'){
                        if ($value) {
                            $uploadId = $setting->doc ? $setting->doc->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/doc/', "setting-file", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/doc/',"setting-file", 'original', 'save', null);
                            }
                        }
                        $setting_value = null;
                    } else {
                        // Handle other fields
                        $setting->value = $setting_value;
                    }
                    $setting->save();
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error_type' => 'something_error', 'error' => trans('messages.error_message')], 400 );
        }
    }
}
