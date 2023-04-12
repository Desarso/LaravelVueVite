<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function swap($locale){
    
         // available language in template array
        $availLocale=['en'=>'en', 'fr'=>'fr','de'=>'de','pt'=>'pt','es'=>'es'];
        // check for existing language
        if(property_exists($availLocale, $locale)){
            session()->put('locale', $locale);
        }
         return redirect()->back();
    }
}
