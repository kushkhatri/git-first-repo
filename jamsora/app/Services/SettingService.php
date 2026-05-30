<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get(string $key, mixed $default = null, string $group = 'general'): mixed
    {
        $settings = $this->all($group);

        return $settings[$key] ?? $default;
    }

    public function all(string $group = 'general'): array
    {
        return Cache::remember("settings.{$group}", 3600, function () use ($group) {
            return Setting::query()
                ->where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'type' => $type]
        );

        Cache::forget("settings.{$group}");
    }
}
