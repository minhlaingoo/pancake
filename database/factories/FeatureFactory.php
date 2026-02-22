<?php

namespace Database\Factories;

use App\Models\Feature;

class FeatureFactory
{
    public function createFeatureIfNotExists($name)
    {
        $existingFeature = Feature::where('name', $name)->first();

        if (!$existingFeature) {
            return Feature::create([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $existingFeature;
    }
}
