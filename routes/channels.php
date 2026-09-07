<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('olt-cli.{sessionId}', function ($user, $sessionId) {
    if (!$user) return false;
    $session = Cache::get('olt_cli_session_' . $sessionId);
    if ($session && isset($session['user_id'])) {
        return (int) $session['user_id'] === (int) $user->id;
    }
    return true; // tighten later
});
