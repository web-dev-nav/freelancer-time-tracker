<?php

namespace App\Services;

use App\Models\SchedulerLog;
use Carbon\Carbon;

class SchedulerLogService
{
    public static function record(array $data): SchedulerLog
    {
        $payload = $data['payload'] ?? null;
        unset($data['payload']);

        return SchedulerLog::create([
            'source' => $data['source'] ?? 'scheduler',
            'type' => $data['type'] ?? 'unknown',
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'detail' => $data['detail'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'executed_at' => $data['executed_at'] ?? Carbon::now(),
            'message' => $data['message'] ?? null,
            'payload' => $payload,
        ]);
    }
}
