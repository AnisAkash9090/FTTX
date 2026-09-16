<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('olt-cli.{sessionId}', function ($user, $sessionId) {
    if (!$user) {
        return false;
    }

    $session = Cache::get('olt_cli_session_' . $sessionId);

    return $session
        && isset($session['user_id'])
        && (int) $session['user_id'] === (int) $user->id;
});
