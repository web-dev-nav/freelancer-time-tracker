<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\BackupSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupSettingController extends Controller
{
    public function index(BackupSchedule $schedule)
    {
        return response()->json(['success' => true, 'data' => $schedule->status()]);
    }

    public function update(Request $request, BackupSchedule $schedule)
    {
        $values = $request->validate([
            'enabled' => 'required|boolean',
            'time' => 'required|date_format:H:i',
            'retention_days' => 'required|integer|min:7|max:365',
        ]);
        DB::transaction(function () use ($values) {
            foreach ($values as $key => $value) {
                Setting::setValue('backup_'.$key, is_bool($value) ? ($value ? '1' : '0') : (string) $value);
            }
        });

        return response()->json(['success' => true, 'message' => 'Backup schedule saved.', 'data' => $schedule->status()]);
    }
}
