<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function sendGasEmail($to, $subject, $body) {
        $url = 'https://script.google.com/macros/s/AKfycbw3Ig9FNC1SZwtKrZgLueXRrMvZ-DvzXDQVMc8CAQ88hNnJ7LtFW2cAlPBDPNMBR-v4/exec'; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['to' => $to, 'subject' => $subject, 'body' => $body]));
        curl_exec($ch);
        curl_close($ch);
    }
}
