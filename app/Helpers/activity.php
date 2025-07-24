<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;

if (!function_exists('log_activity')) {
    function log_activity(string $type, string $action, string $description): void
    {
        try {
            if (!Schema::hasTable('activity_logs')) {
                return;
            }

            ActivityLog::create([
                'type' => $type,
                'action' => $action,
                'description' => $description,
                'user_id' => auth()->id(),
            ]);
        } catch (\Throwable $e) {
        }
    }
}
