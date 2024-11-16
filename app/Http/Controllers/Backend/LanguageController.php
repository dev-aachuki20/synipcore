<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {

        if (in_array($locale, ['en', 'cn', 'jp'])) {  // Default cn traditional chienese
            Session::put('locale', $locale);
        }
        return redirect()->back();
    }
}
