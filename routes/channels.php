<?php

use Illuminate\Support\Facades\Broadcast;

// Only register channels when a broadcaster with real credentials is configured.
// This prevents a crash during `composer install` / `package:discover` in
// environments (CI, Docker build) where PUSHER_APP_KEY is not yet set.
if (config('broadcasting.default') !== 'null' && config('broadcasting.connections.' . config('broadcasting.default') . '.key')) {
    Broadcast::channel('document.{id}', fn() => true);
}
