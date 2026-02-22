<?php

use App\Models\Setting;

function setting($category)
{

    $settings = Setting::where('category', $category)->first();

    if (!$settings) {
        return (object) [];
    }

    return json_decode($settings->value);
}

function saveSetting($category, $value)
{
    $settings = Setting::where('category', $category)->first();

    if (!$settings) {
        $settings = new Setting();
        $settings->category = $category;
    }

    $settings->value = json_encode($value);
    $settings->save();
}
