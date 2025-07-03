<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return \App\Models\Setting::get($key, $default);
    }
}
