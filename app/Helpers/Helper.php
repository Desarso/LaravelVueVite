<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Enums\App;


class Helper
{
    public static function applClasses()
    {
        // Demo
        // $fullURL = request()->fullurl();
        // if (App()->environment() === 'production') {
        //     for ($i = 1; $i < 7; $i++) {
        //         $contains = Str::contains($fullURL, 'demo-' . $i);
        //         if ($contains === true) {
        //             $data = config('custom.' . 'demo-' . $i);
        //         }
        //     }
        // } else {
        //     $data = config('custom.custom');
        // }

        // default data array
        $DefaultData = [
            'mainLayoutType' => 'vertical',
            'theme' => 'light',
            'sidebarCollapsed' => false,
            'navbarColor' => '',
            'horizontalMenuType' => 'floating',
            'verticalMenuNavbarType' => 'floating',
            'footerType' => 'static', //footer
            'bodyClass' => '',
            'pageHeader' => false,
            'contentLayout' => 'default',
            'blankPage' => false,
            'defaultLanguage'=>'es',
            'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'),
            'version' => "4.1.1",
        ];

        $preferences = Auth::check() ? json_decode(Auth::user()->preferences, true) : null;
        $preferences = is_null($preferences) ? [] : $preferences;
        self::updatePageConfig($preferences);

        
        $data = array_merge($DefaultData, config('custom.custom'));

        // All options available in the template
        $allOptions = [
            'mainLayoutType' => array('vertical', 'horizontal'),
            'theme' => array('light' => 'light', 'dark' => 'dark-layout', 'semi-dark' => 'semi-dark-layout'),
            'sidebarCollapsed' => array(true, false),
            'navbarColor' => array('bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'),
            'horizontalMenuType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'),
            'horizontalMenuClass' => array('static' => 'menu-static', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'),
            'verticalMenuNavbarType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'),
            'navbarClass' => array('floating' => 'floating-nav', 'static' => 'static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'),
            'footerType' => array('static' => 'footer-static', 'sticky' => 'fixed-footer', 'hidden' => 'footer-hidden'),
            'pageHeader' => array(true, false),
            'contentLayout' => array('default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'),
            'blankPage' => array(false, true),
            'sidebarPositionClass' => array('content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'),
            'contentsidebarClass' => array('content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'),
            'defaultLanguage'=>array('en'=>'en','fr'=>'fr','de'=>'de','pt'=>'pt','es'=>'es'),
            'direction' => array('ltr', 'rtl'),
        ];
        
        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists( $data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }
        
        //layout classes
        $layoutClasses = [
            'theme' => $data['theme'],
            'layoutTheme' => $allOptions['theme'][$data['theme']],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
            'navbarClass' => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
            'navbarColor' => $data['navbarColor'],
            'horizontalMenuType' => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
            'horizontalMenuClass' => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
            'footerType' => $allOptions['footerType'][$data['footerType']],
            'sidebarClass' => 'menu-expanded',
            'bodyClass' => $data['bodyClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => $allOptions['sidebarPositionClass'][$data['contentLayout']],
            'contentsidebarClass' => $allOptions['contentsidebarClass'][$data['contentLayout']],
            'mainLayoutType' => $data['mainLayoutType'],
            'defaultLanguage'=>$allOptions['defaultLanguage'][$data['defaultLanguage']],
            'direction' => $data['direction'],
            'version' => $data['version'],
        ];
        // set default language if session hasn't locale value the set default language
        if(!session()->has('locale')){
            app()->setLocale($layoutClasses['defaultLanguage']);
        }
        
        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        //dd($layoutClasses);
        
        return $layoutClasses;
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        // $fullURL = request()->fullurl();
        // if (App()->environment() === 'production') {
        //     for ($i = 1; $i < 7; $i++) {
        //         $contains = Str::contains($fullURL, 'demo-' . $i);
        //         if ($contains === true) {
        //             $demo = 'demo-' . $i;
        //         }
        //     }
        // }
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }


    public static function kendoLocale()
    {
        $lang = "es-ES";

        switch ( session()->get('locale'))
        {
            case 'es':
                $lang = "es-ES";
                break;

            case 'en':
                $lang = "en-US";
                break;

            case 'de':
                $lang = "de-DE";
                break;

        }
        return $lang;
    }

    public static function get_local_time($ip)
    {
        //$ip = file_get_contents("http://ipecho.net/plain");
        
        $url = 'http://ip-api.com/json/'.$ip;
        $tz = file_get_contents($url);
        $tz = json_decode($tz, true);
        
        if($tz["status"] == "success")
        {
            $tz = $tz['timezone'];
        }
        else
        {
            $tz = "America/Costa_Rica";
        }

        return $tz;
    }

    public static function formatIcon($iconItem)
    {
        $icon = explode(" ", $iconItem);
        $prefix = $icon[0];
        $iconFixed = $prefix . '-' . substr($icon[1], 3, strlen($icon[1]));

        return $iconFixed;
    }

    public static function UploadImageApp($base64Array)
    {
        if(is_null($base64Array)) return null;

        $url         = '';
        $client = env('DO_SPACES_HOTEL', 'prueba');
        $path   = env('DO_SPACES_ROUTE', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/');

        for ($a = 0; count($base64Array) > $a; $a++) {

            $name = $client . '/'  . uniqid('wh', false) . ".jpg";

            $upload_success = Storage::disk('spaces')->put($name, base64_decode($base64Array[$a]), 'public'); //Guardamos las imaganes en DigitalOcean y obtenemos la ruta de la imagen devuelta por el método "puFile"

            if ($upload_success) {
                if ($a != 0) $url .= ",";
                $url .= $path . $name;
            }
        }

        return $url;
    }

    public static function getCleaningSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Cleaning)->first()->settings;
        return json_decode($settings);
    }

    
    public static function deleteUrl($fullUrl)
    {
        if(is_null($fullUrl)) return false;

        $url = str_replace('https://dingdonecdn.nyc3.digitaloceanspaces.com/', '', $fullUrl);
        $deleted = Storage::disk('spaces')->delete($url);

        if($deleted) {
            return true;
        } else {
            return false;
        }
    }
}
