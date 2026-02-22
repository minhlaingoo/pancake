<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Casts\Json;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SettingFactory
{

    public function createSettingIfNotExist($category, array $value)
    {
        $existingSetting = \App\Models\Setting::where('category', $category)->first();
        if ($existingSetting) return;
        return \App\Models\Setting::create([
            'category' => $category,
            'value' => json_encode($value),
        ]);
    }

    public function updateSetting($category, $key, $value)
    {
        $setting = \App\Models\Setting::where('category', $category)->first();
        $setting_value = json_decode($setting->value, true);
        $setting_value[$key] = $value;
        $setting->value = json_encode($setting_value);
        $setting->save();
    }
}
