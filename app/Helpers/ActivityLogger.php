<?php 
// app/Helpers/ActivityLogger.php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Events\UserActionOccurred;


class ActivityLogger
{
    public static function log($action, $description = null)
    {
       $newActivity = ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
        $newActivity->load('user');
        event(new UserActionOccurred($newActivity));
        broadcast(new UserActionOccurred($newActivity))->toOthers();
       // dd($newActivity);

    }
}