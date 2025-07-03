<?php
use App\Models\ActivityLog;

if (! function_exists('log_activity')) {
    function log_activity(string $type, string $action, string $description): void
    {
        ActivityLog::create([
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
        ]);
    }
}
